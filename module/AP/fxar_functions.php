<?php
// =========================================================================
// AUTO JURNAL SELISIH KURS AR - revaluasi saldo PIUTANG USAHA valas.
// 1 JURNAL PER TANGGAL PER PROFIT CENTER.
//
// Saudara kandung fxap_functions.php (utang) dan fxsk_functions.php (bank).
// Mesinnya sama persis - "rolling/crystallize harian" - yang beda:
//   grain      : bank -> per rekening ; AR -> per profit center x COA piutang
//   sisi akun  : AR -> ASET, jadi arah D/K kebalikan dari AP yang kewajiban
//   saldo awal : AR -> Kartu AR (ar_dev), bukan AP Report
//   akun L/R   : 8.52.02 (belum terealisasi), sama dengan bank dan AP
//
// Dipakai bareng oleh:
//   - auto-jurnal-selisih-kurs-ar.php      (form web, Preview lalu Post)
//   - sync_selisih_kurs_scheduler.php      (otomatis tiap malam, satu paket
//                                           dengan bank dan AP)
//
// ------------------------------------------------------------------------
// RUMUS, per TANGGAL x PROFIT CENTER x COA (persis kolom F s/d Q di sheet
// "Forex AR Calc" milik D:\File Project\Forex Gain or Loss - AR.xlsx):
//
//   saldo_usd_D   = saldo_usd_(D-1) + debit_usd_D - credit_usd_D
//                   (ASET: debit MENAMBAH piutang - kebalikan dari AP)
//   nilai_pasar_D = saldo_usd_D x kurs pasar tanggal D
//   nilai_buku_D  = opening_D + debit_idr_D - credit_idr_D
//                   opening_D = nilai_pasar_(D-1); untuk tanggal pertama =
//                   saldo awal USD x kurs pasar tanggal itu
//   selisih_D     = nilai_pasar_D - nilai_buku_D
//   opening_(D+1) = nilai_pasar_D        <- crystallize harian
//
// ARAH DEBIT/KREDIT. Ini akun ASET, jadi lurus saja:
//   selisih > 0  -> nilai piutang NAIK  -> UNTUNG -> Debit piutang, Credit 8.52.02
//   selisih < 0  -> nilai piutang TURUN -> RUGI   -> Credit piutang, Debit 8.52.02
// Berbeda dengan AP yang terbalik. Template Excel AR sudah benar sendiri -
// dicocokkan ke sheet "Journal": 1 Okt 2026 NAG selisih -46.899.581,85
// memang ditulis Debit 8.52.02 dan Credit 1.30.02.
//
// KURS PASAR: kurs PAJAK (KMK) tiap hari, KECUALI tanggal AKHIR BULAN yang
// memakai kurs HARIAN - lihat fxArRatePasar().
//
// SALDO AWAL diambil dari KARTU AR milik aplikasi ar_dev (SQL-nya disalin ke
// fxar_saldo_sql.php), bukan dari GL. Sama seperti AP yang memakai AP Report.
// Konsekuensinya angkanya memang beda dari saldo GL - per 30 Sep 2026
// bedanya NAG 119.786,90 USD; itu disengaja, laporanlah yang jadi patokan
// accounting. COA tiap faktur diambil dari baris jurnalnya sendiri di
// tbl_list_journal (no_journal = nomor faktur), karena Kartu AR tidak punya
// kolom COA.
//
// COA yang direvaluasi TIDAK dipatok di kode: diambil dari flag
// mastercoa_v2.forex_ar = 'Y' (db_migrate_LIVE_20261124_forex_ar_coa.sql),
// yang bisa dicentang lewat menu Master COA. PROFIT CENTER diambil apa
// adanya dari data, tidak dipetakan per COA.
//
// Nomor dokumen "FX-AR/{profit_center}/{mmyy}/{00001}" - seri sendiri,
// terpisah dari FX/ milik bank, FX-AP/ milik utang, maupun GM manual.
//
// Dijalankan ulang untuk tanggal yang sama = AMAN: dokumen lama dengan nomor
// yang sama dihapus barisnya lalu ditulis ulang.
// =========================================================================

require_once __DIR__ . '/fxsk_functions.php';     // fxSkTanggalIndo, fxSkIsPeriodOpen, fxSkCcSelisih, fxSkEnsureCategory
require_once __DIR__ . '/fxar_saldo_sql.php';     // fxArSqlKartuAr()

define('FXAR_COA_SELISIH', '8.52.02');
define('FXAR_ID_CMJ',      'CMJ021');                   // FOREX GAIN OR LOSS, sama dengan bank dan AP
define('FXAR_TYPE',        'FOREX GAIN OR LOSS - AR');  // type_journal di tbl_list_journal
define('FXAR_CURR',        'USD');

// DUA BATAS BAWAH, sama polanya dengan FX-AP.
//
// FXAR_MIN_DATE = mulai MENGHITUNG. 1 Okt 2026 dipilih karena 30 Sep 2026
// adalah AKHIR BULAN, jadi saldo awalnya dinilai kurs HARIAN - titik awal
// yang sama persis dengan yang dipakai file accounting (17.998). Kartu AR
// sendiri sanggup mundur sampai 2022, tapi tidak ada gunanya: tanggal
// sebelum ini tidak akan pernah jadi jurnal.
//
// FXAR_POST_FROM = mulai MEMBUAT JURNAL. Tanggal sebelum ini tetap dihitung
// (rantai nilai bukunya harus jalan supaya 1 Nov benar) tapi tidak pernah
// jadi dokumen dan tidak ditampilkan di Preview. Oktober di file accounting
// memang cuma contoh.
define('FXAR_MIN_DATE',    '2026-10-01');
define('FXAR_POST_FROM',   '2026-11-01');
define('FXAR_PREFIX',      'FX-AR/');

// Batas bawah data Kartu AR - angka ini dipatok di laporan aslinya di
// ar_dev ('2022-05-01'), bukan pilihan kita.
define('FXAR_KARTU_DARI',  '2022-05-01');


// ---------------------------------------------------------------- master --

// Kolom penandanya sudah ada atau belum. Kalau kode ini naik ke server lebih
// dulu daripada db_migrate_LIVE_20261124_forex_ar_coa.sql dijalankan, query
// daftar COA akan gagal dan hasilnya jadi "tidak ada selisih" - menyesatkan.
// Lebih baik ketahuan dan dikatakan apa adanya oleh pemanggilnya.
function fxArSiap($conn) {
    static $ada = null;
    if ($ada !== null) { return $ada; }
    $q = mysqli_query($conn, "show columns from mastercoa_v2 like 'forex_ar'");
    $ada = ($q && mysqli_num_rows($q) > 0);
    return $ada;
}

// COA piutang yang ikut direvaluasi - dari flag, bukan daftar di kode.
function fxArDaftarCoa($conn) {
    $coa = array();
    if (!fxArSiap($conn)) { return $coa; }
    $q = mysqli_query($conn, "select no_coa, nama_coa from mastercoa_v2 where forex_ar = 'Y' order by no_coa");
    while ($r = mysqli_fetch_assoc($q)) { $coa[trim($r['no_coa'])] = $r['nama_coa']; }
    return $coa;
}

// Peta segala bentuk tulisan profit center -> kode_pc ('NAG'/'NAK'). Kartu AR
// dan tbl_list_journal sama-sama memakai kode pendek, tapi peta ini tetap
// dipasang supaya aman kalau salah satunya berubah jadi nama panjang.
function fxArPetaPc($conn) {
    $peta = array();
    $q = mysqli_query($conn, "select id_pc, nama_pc, kode_pc from master_pc");
    while ($r = mysqli_fetch_assoc($q)) {
        $bentuk = array($r['kode_pc'], $r['id_pc'], $r['nama_pc'], $r['id_pc'] . ' - ' . $r['nama_pc']);
        foreach ($bentuk as $b) {
            if ($b !== null && trim($b) !== '') { $peta[strtoupper(trim($b))] = $r['kode_pc']; }
        }
    }
    return $peta;
}

function fxArKodePc($peta, $tulisan) {
    $k = strtoupper(trim((string) $tulisan));
    return isset($peta[$k]) ? $peta[$k] : $k;
}

// Nama divisi untuk KETERANGAN jurnal, menyamai fxApNamaDivisi() di sisi AP.
// Kalau ada divisi baru, tambahkan di KEDUA berkas. Yang tidak terdaftar
// tetap memakai kodenya sendiri, jadi keterangannya tidak pernah kosong.
// Hanya mempengaruhi teks; kolom profit_center di jurnal tetap diisi KODE.
function fxArNamaDivisi($pc) {
    $peta = array(
        'NAG' => 'Divisi Garment',
        'NAK' => 'Divisi Knitting',
    );
    $k = strtoupper(trim((string) $pc));
    return isset($peta[$k]) ? $peta[$k] : $k;
}


// ------------------------------------------------------------------ kurs --

// Kurs pasar untuk menilai saldo piutang pada $tanggal.
//
// ATURANNYA: kurs PAJAK (KMK) setiap hari, KECUALI tanggal AKHIR BULAN yang
// memakai kurs HARIAN. Persis seperti yang dilakukan file accounting: di
// sheet "KMK Rate" seluruh Oktober diisi KMK, hanya baris 31 Okt yang diisi
// 18.128 - itu kurs HARIAN, bukan KMK (KMK 28 Okt s/d 3 Nov = 18.032).
// Dipakai supaya saldo akhir bulan di buku besar sama dengan yang dilihat
// accounting di Kartu AR dan di neraca.
//
// AKIBAT YANG HARUS DIKETAHUI: tiap tanggal akhir bulan muncul jurnal
// lonjakan lalu tanggal 1 bulan berikutnya muncul jurnal balik sebesar itu
// juga, karena nilai buku tanggal 1 = nilai pasar akhir bulan. Itu MEMANG
// disengaja: neraca akhir bulan disajikan pada kurs HARIAN, bulan berjalan
// pada kurs KMK.
//
// Kalau kurs jenis yang diminta belum diisi di masterrate, diambil yang
// terdekat SEBELUMNYA (tanggal <= $tanggal), bukan dianggap nol.
function fxArRatePasar($conn, $tanggal) {
    static $ingat = array();
    if (isset($ingat[$tanggal])) { return $ingat[$tanggal]; }
    $d = mysqli_real_escape_string($conn, $tanggal);
    $q = mysqli_query($conn, "select rate from masterrate
         where curr = '" . FXAR_CURR . "' and tanggal <= '$d'
           and v_codecurr = CASE WHEN '$d' = LAST_DAY('$d') THEN 'HARIAN' ELSE 'PAJAK' END
         order by tanggal desc limit 1");
    $r = mysqli_fetch_assoc($q);
    $ingat[$tanggal] = $r ? (float) $r['rate'] : 1.0;
    return $ingat[$tanggal];
}


// ------------------------------------------------------------ saldo awal --

// Saldo piutang valas per PROFIT CENTER x COA pada akhir $perTanggal, DARI
// KARTU AR (bukan GL). SQL kartunya ada di fxar_saldo_sql.php.
//
// Kartu AR tidak punya kolom COA, jadi COA tiap faktur diambil dari baris
// jurnalnya sendiri di tbl_list_journal (no_journal = nomor faktur). Faktur
// yang tidak ketemu jurnalnya TIDAK dibuang diam-diam: nilainya dikumpulkan
// di 'tanpa_coa' supaya bisa diperingatkan di layar.
//
// Return: array('saldo' => array('NAG|1.30.02' => 347404.31, ...),
//               'tanpa_coa' => array('NAG' => 0.0, ...))
function fxArSaldoAwal($conn, $perTanggal, $peta, $daftarCoa) {
    $hasil = array('saldo' => array(), 'tanpa_coa' => array());
    if (empty($daftarCoa)) { return $hasil; }

    $in = array();
    foreach (array_keys($daftarCoa) as $c) { $in[] = "'" . mysqli_real_escape_string($conn, $c) . "'"; }

    $kartu = fxArSqlKartuAr(
        mysqli_real_escape_string($conn, FXAR_KARTU_DARI),
        mysqli_real_escape_string($conn, $perTanggal)
    );

    // min(no_coa) - satu faktur pada praktiknya hanya memakai satu COA
    // piutang; min() cuma penjaga kalau ada yang terbelah.
    $sql = "select r.profit_center pc, coalesce(j.no_coa, '') no_coa, sum(r.total) saldo
              from (" . $kartu . ") r
              left join (select no_journal, min(no_coa) no_coa
                           from tbl_list_journal
                          where curr = '" . FXAR_CURR . "' and status <> 'Cancel'
                            and no_coa in (" . implode(',', $in) . ")
                          group by no_journal) j on j.no_journal = r.no_invoice
             where r.curr = '" . FXAR_CURR . "'
             group by r.profit_center, coalesce(j.no_coa, '')";

    $q = mysqli_query($conn, $sql);
    if (!$q) { return $hasil; }
    while ($r = mysqli_fetch_assoc($q)) {
        $pc    = fxArKodePc($peta, $r['pc']);
        $coa   = trim($r['no_coa']);
        $nilai = (float) $r['saldo'];
        if ($coa === '') {
            if (abs($nilai) >= 0.005) {
                if (!isset($hasil['tanpa_coa'][$pc])) { $hasil['tanpa_coa'][$pc] = 0.0; }
                $hasil['tanpa_coa'][$pc] += $nilai;
            }
            continue;
        }
        $kunci = $pc . '|' . $coa;
        if (!isset($hasil['saldo'][$kunci])) { $hasil['saldo'][$kunci] = 0.0; }
        $hasil['saldo'][$kunci] += $nilai;
    }
    return $hasil;
}


// ---------------------------------------------------------------- mutasi --

// Mutasi harian per tanggal x profit center x COA, dalam USD dan IDR. Baris
// jurnal buatan tool ini sendiri ber-curr IDR, jadi saringan curr = 'USD'
// sudah otomatis mengecualikannya (tidak memakan hasil jurnalnya sendiri
// kalau dijalankan ulang).
function fxArMutasi($conn, $start, $end, $daftarCoa, $peta) {
    $mutasi = array();
    if (empty($daftarCoa)) { return $mutasi; }

    $in = array();
    foreach (array_keys($daftarCoa) as $c) { $in[] = "'" . mysqli_real_escape_string($conn, $c) . "'"; }
    $sE = mysqli_real_escape_string($conn, $start);
    $eE = mysqli_real_escape_string($conn, $end);

    $q = mysqli_query($conn, "select tgl_journal, profit_center, no_coa,
                sum(debit) db, sum(credit) kr, sum(debit_idr) db_idr, sum(credit_idr) kr_idr
           from tbl_list_journal
          where tgl_journal between '$sE' and '$eE'
            and curr = '" . FXAR_CURR . "'
            and status <> 'Cancel'
            and no_coa in (" . implode(',', $in) . ")
          group by tgl_journal, profit_center, no_coa");
    while ($r = mysqli_fetch_assoc($q)) {
        $kunci = fxArKodePc($peta, $r['profit_center']) . '|' . trim($r['no_coa']);
        // ASET: debit menambah piutang.
        $mutasi[$r['tgl_journal']][$kunci] = array(
            'usd' => (float) $r['db']     - (float) $r['kr'],
            'idr' => (float) $r['db_idr'] - (float) $r['kr_idr'],
        );
    }
    return $mutasi;
}


// -------------------------------------------------------------- hitungan --

// Dokumen FX-AR yang SUDAH ADA untuk tanggal + profit center ini, kalau ada.
function fxArDokumenAda($conn, $tanggal, $pc) {
    $t = mysqli_real_escape_string($conn, $tanggal);
    $p = mysqli_real_escape_string($conn, $pc);
    $q = mysqli_query($conn, "select no_journal from tbl_list_journal
          where tgl_journal = '$t' and profit_center = '$p'
            and no_journal like '" . FXAR_PREFIX . "%' and status <> 'Cancel' limit 1");
    $r = mysqli_fetch_assoc($q);
    return $r ? $r['no_journal'] : null;
}

// Semua dokumen FX-AR yang sudah ada di rentang ini, dikunci 'tanggal|pc'.
// Dipakai untuk menemukan dokumen BASI: jurnal yang pernah dibuat tapi pada
// hitungan sekarang selisihnya jadi nol (mis. fakturnya dibatalkan). Tanpa
// ini angka lama akan tertinggal diam-diam di buku besar.
function fxArDokumenRentang($conn, $start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    $q = mysqli_query($conn, "select distinct no_journal, tgl_journal, profit_center
          from tbl_list_journal
          where tgl_journal between '$s' and '$e'
            and no_journal like '" . FXAR_PREFIX . "%' and status <> 'Cancel'");
    $hasil = array();
    while ($r = mysqli_fetch_assoc($q)) {
        $hasil[$r['tgl_journal'] . '|' . $r['profit_center']] = $r['no_journal'];
    }
    return $hasil;
}

// Hitung revaluasi seluruh rentang. Return:
//   ['baris'     => [...per tanggal x pc x coa, hanya yang selisihnya != 0;
//                    termasuk yang periodenya sudah Closed, ditandai
//                    'periode_tutup' => true...],
//    'dokumen'   => ['2026-11-01|NAG' => ['tanggal','pc','net','baris',
//                                         'dokumen_ada']] - periode Closed
//                    TIDAK pernah masuk ke sini,
//    'rate_awal' => kurs pembuka, 'per_tanggal' => tanggal saldo awal,
//    'tanpa_coa' => faktur bersaldo yang COA-nya tidak ketemu, per PC]
function fxArHitung($conn, $start, $end) {
    if ($start < FXAR_MIN_DATE) { $start = FXAR_MIN_DATE; }
    $kosong = array('baris' => array(), 'dokumen' => array(), 'rate_awal' => 0,
                    'per_tanggal' => '', 'tanpa_coa' => array());
    if ($end < $start) { return $kosong; }

    $daftarCoa = fxArDaftarCoa($conn);
    $peta      = fxArPetaPc($conn);

    $perTanggal = date('Y-m-d', strtotime($start . ' -1 day'));
    $awal       = fxArSaldoAwal($conn, $perTanggal, $peta, $daftarCoa);
    $saldoAwal  = $awal['saldo'];
    $rateAwal   = fxArRatePasar($conn, $perTanggal);
    $mutasi     = fxArMutasi($conn, $start, $end, $daftarCoa, $peta);

    // Kumpulkan semua kunci (pc|coa) yang pernah muncul - dari saldo awal
    // MAUPUN mutasi, supaya COA yang baru dipakai di tengah periode ikut.
    $kunci = array();
    foreach (array_keys($saldoAwal) as $k) { $kunci[$k] = true; }
    foreach ($mutasi as $perHari) { foreach (array_keys($perHari) as $k) { $kunci[$k] = true; } }

    // Saldo awal bisa memuat COA yang tidak dicentang - buang di sini.
    foreach (array_keys($kunci) as $k) {
        $bagian = explode('|', $k);
        if (count($bagian) !== 2 || trim($bagian[0]) === '' || !isset($daftarCoa[$bagian[1]])) {
            unset($kunci[$k]);
        }
    }

    $usd  = array();   // saldo USD berjalan
    $buku = array();   // nilai buku IDR berjalan
    foreach (array_keys($kunci) as $k) {
        $usd[$k]  = isset($saldoAwal[$k]) ? (float) $saldoAwal[$k] : 0.0;
        $buku[$k] = $usd[$k] * $rateAwal;
    }

    $baris   = array();
    $dokumen = array();
    $kursor  = strtotime($start);
    $batas   = strtotime($end);

    while ($kursor <= $batas) {
        $d = date('Y-m-d', $kursor);
        $kursor = strtotime('+1 day', $kursor);
        $ratePasar   = fxArRatePasar($conn, $d);
        $periodeBuka = fxSkIsPeriodOpen($conn, $d);

        foreach (array_keys($kunci) as $k) {
            $m = isset($mutasi[$d][$k]) ? $mutasi[$d][$k] : array('usd' => 0.0, 'idr' => 0.0);
            $usd[$k]  += $m['usd'];
            $buku[$k] += $m['idr'];

            $bukuSebelum = $buku[$k];
            $pasar       = $usd[$k] * $ratePasar;
            $selisih     = round($pasar - $bukuSebelum, 2);

            // Crystallize: nilai pasar hari ini jadi nilai buku pembuka besok.
            // Tetap dijalankan walau periodenya sudah ditutup, supaya tanggal
            // SESUDAHNYA tetap benar - yang dilewati hanya pembuatan jurnalnya.
            $buku[$k] = $pasar;

            // Sebelum tanggal mulai jurnal: saldonya sudah dijalankan di
            // atas, sisanya tidak perlu - tidak dijurnal, tidak ditampilkan.
            if ($d < FXAR_POST_FROM) { continue; }

            if ($selisih == 0) { continue; }

            list($pc, $coa) = explode('|', $k);
            $satu = array(
                'tanggal'       => $d,
                'pc'            => $pc,
                'no_coa'        => $coa,
                'nama_coa'      => $daftarCoa[$coa],
                'saldo_usd'     => round($usd[$k], 2),
                'rate'          => $ratePasar,
                'buku'          => round($bukuSebelum, 2),
                'pasar'         => round($pasar, 2),
                'selisih'       => $selisih,
                'periode_tutup' => !$periodeBuka,
            );
            $baris[] = $satu;

            // Periode yang sudah Closed tetap DITAMPILKAN di baris (supaya
            // jelas kenapa angkanya tidak muncul jadi jurnal), tapi TIDAK
            // pernah masuk daftar dokumen yang akan diposting.
            if (!$periodeBuka) { continue; }

            $kd = $d . '|' . $pc;
            if (!isset($dokumen[$kd])) {
                $dokumen[$kd] = array(
                    'tanggal'     => $d,
                    'pc'          => $pc,
                    'net'         => 0.0,
                    'baris'       => array(),
                    'dokumen_ada' => fxArDokumenAda($conn, $d, $pc),
                );
            }
            $dokumen[$kd]['net'] += $selisih;
            $dokumen[$kd]['baris'][] = $satu;
        }
    }

    // Dokumen basi: pernah dijurnal, sekarang tidak ada selisih lagi. Masuk
    // daftar dengan baris KOSONG - fxArTulisJurnal() menghapus baris lamanya
    // lalu tidak menulis apa pun, jadi dokumennya hilang dengan sendirinya.
    // Periode yang sudah Closed sengaja tidak disentuh.
    $awalBasi = ($start > FXAR_POST_FROM) ? $start : FXAR_POST_FROM;
    foreach (fxArDokumenRentang($conn, $awalBasi, $end) as $kd => $noJurnal) {
        if (isset($dokumen[$kd])) { continue; }
        list($dTua, $pcTua) = explode('|', $kd);
        if (!fxSkIsPeriodOpen($conn, $dTua)) { continue; }
        $dokumen[$kd] = array(
            'tanggal'     => $dTua,
            'pc'          => $pcTua,
            'net'         => 0.0,
            'baris'       => array(),
            'dokumen_ada' => $noJurnal,
            'basi'        => true,
        );
    }

    foreach ($dokumen as $kd => $v) { $dokumen[$kd]['net'] = round($v['net'], 2); }
    ksort($dokumen);

    return array(
        'baris'       => $baris,
        'dokumen'     => $dokumen,
        'rate_awal'   => $rateAwal,
        'per_tanggal' => $perTanggal,
        'tanpa_coa'   => $awal['tanpa_coa'],
    );
}


// --------------------------------------------------------------- posting --

// Nomor dokumen baru "FX-AR/{pc}/{mmyy}/{00001}" - urut per profit center per
// bulan, dibaca dari tbl_list_journal (tempat barisnya sendiri disimpan).
function fxArNomorBaru($conn, $pc, $tanggal) {
    $awalan = FXAR_PREFIX . $pc . '/' . date('my', strtotime($tanggal));
    $aE = mysqli_real_escape_string($conn, $awalan);
    $q = mysqli_query($conn, "select max(cast(right(no_journal,5) as unsigned)) urut
           from tbl_list_journal where no_journal like '$aE%'");
    $r = mysqli_fetch_assoc($q);
    $urut = ($r && $r['urut'] !== null) ? ((int) $r['urut'] + 1) : 1;
    return $awalan . '/' . sprintf('%05d', $urut);
}

// Tulis 1 dokumen (1 tanggal x 1 profit center). Dipanggil di dalam transaksi
// DB oleh pemanggil. Kalau dokumennya sudah pernah ada, barisnya dihapus dulu
// lalu ditulis ulang dengan NOMOR YANG SAMA.
function fxArTulisJurnal($connBaca, $connTulis, $doc, $isi, $user) {
    $tanggal = $isi['tanggal'];
    $pc      = $isi['pc'];
    $net     = $isi['net'];
    $buat    = date('Y-m-d H:i:s');

    // Huruf besar semua, menyamai jurnal FX-AP. Dibungkus di SINI, bukan di
    // dalam fxSkTanggalIndo(), karena fungsi itu dipakai bersama jurnal
    // selisih kurs BANK yang keterangannya tidak boleh berubah.
    $ket  = strtoupper('REVALUASI KURS PIUTANG USAHA ' . fxArNamaDivisi($pc) . ' PER ' . fxSkTanggalIndo($tanggal));
    $ketE = mysqli_real_escape_string($connTulis, $ket);
    $docE = mysqli_real_escape_string($connTulis, $doc);
    $pcE  = mysqli_real_escape_string($connTulis, $pc);

    // Tulis ulang bersih - daftar COA-nya bisa berubah antar perhitungan.
    mysqli_query($connTulis, "delete from tbl_list_journal where no_journal = '$docE' and no_journal like '" . FXAR_PREFIX . "%'");
    mysqli_query($connTulis, "delete from tbl_memorial_journal where no_mj = '$docE' and no_mj like '" . FXAR_PREFIX . "%'");

    // Cost center 8.52.02 dicari per profit center dari master, sama persis
    // dengan jurnal selisih kurs bank (NAG -> DEP06SUB001, NAK -> DEPNK09SUB001).
    $cc     = fxSkCcSelisih($connBaca, $pc);
    $ccNo   = $cc['no_cc'];
    $ccNama = $cc['cc_name'];

    $rSk = mysqli_fetch_assoc(mysqli_query($connBaca, "select nama_coa from mastercoa_v2 where no_coa = '" . FXAR_COA_SELISIH . "'"));
    $namaCoaSk = $rSk ? $rSk['nama_coa'] : 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI';

    // Baris piutang - ASET: selisih > 0 = piutang bertambah = DIDEBIT.
    $legs = array();
    foreach ($isi['baris'] as $b) {
        $nilai = abs($b['selisih']);
        $legs[] = array($b['no_coa'], $b['nama_coa'], ($b['selisih'] > 0 ? $nilai : 0), ($b['selisih'] < 0 ? $nilai : 0), '-', '-');
    }
    // Baris lawan 8.52.02: net > 0 = UNTUNG = DIKREDIT ; net < 0 = RUGI = DIDEBIT.
    $legs[] = array(FXAR_COA_SELISIH, $namaCoaSk, ($net < 0 ? abs($net) : 0), ($net > 0 ? abs($net) : 0), $ccNo, $ccNama);

    $jumlahBaris = 0;
    foreach ($legs as $leg) {
        list($coa, $namaCoa, $debit, $credit, $ccx, $namaCcx) = $leg;
        if ($debit == 0 && $credit == 0) { continue; }
        $coaE     = mysqli_real_escape_string($connTulis, $coa);
        $namaCoaE = mysqli_real_escape_string($connTulis, $namaCoa);
        $ccE      = mysqli_real_escape_string($connTulis, $ccx);
        $namaCcE  = mysqli_real_escape_string($connTulis, $namaCcx);

        mysqli_query($connTulis, "insert into tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            values
            ('$docE', '$tanggal', '" . FXAR_TYPE . "', '$coaE', '$namaCoaE', '$ccE', '$namaCcE', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Post', '$ketE', '$user', '$buat', '', '', '', '', '$pcE')");

        mysqli_query($connTulis, "insert into tbl_memorial_journal
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center)
            values
            ('$docE', '$tanggal', '" . FXAR_ID_CMJ . "', '$coaE', '$ccE', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', '$ketE', 'Post', '$user', '$buat', '$pcE')");

        $jumlahBaris++;
    }

    return $jumlahBaris;
}

// Post seluruh hasil hitungan dalam SATU transaksi. $hasil = keluaran
// fxArHitung(). Return ['ok'=>bool, 'dokumen'=>[...], 'message'=>..].
function fxArPost($connBaca, $connTulis, $hasil, $user) {
    if (empty($hasil['dokumen'])) { return array('ok' => true, 'dokumen' => array()); }

    fxSkEnsureCategory($connBaca);   // kategori CMJ021, idempotent

    mysqli_begin_transaction($connTulis);
    try {
        $jadi = array();
        foreach ($hasil['dokumen'] as $isi) {
            $lama = $isi['dokumen_ada'];
            $doc  = $lama ? $lama : fxArNomorBaru($connTulis, $isi['pc'], $isi['tanggal']);
            $n    = fxArTulisJurnal($connBaca, $connTulis, $doc, $isi, $user);
            $jadi[] = array(
                'tanggal' => $isi['tanggal'],
                'pc'      => $isi['pc'],
                'net'     => $isi['net'],
                'baris'   => $n,
                'doc'     => $doc,
                'aksi'    => $lama ? 'update' : 'insert',
            );
        }
        mysqli_commit($connTulis);
        return array('ok' => true, 'dokumen' => $jadi);
    } catch (\Throwable $e) {
        mysqli_rollback($connTulis);
        return array('ok' => false, 'message' => $e->getMessage());
    }
}
