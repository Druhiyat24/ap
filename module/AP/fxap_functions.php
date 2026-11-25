<?php
// =========================================================================
// AUTO JURNAL SELISIH KURS AP - revaluasi saldo UTANG USAHA valas.
// 1 JURNAL PER TANGGAL PER PROFIT CENTER.
//
// Saudara kandung fxsk_functions.php (yang merevaluasi saldo BANK). Mesinnya
// sama persis - "rolling/crystallize harian" - yang beda cuma:
//   grain      : bank -> per rekening ; AP -> per profit center x COA utang
//   sisi akun  : bank -> aset         ; AP -> kewajiban (arah D/K terbalik)
//   kurs pasar : bank -> HARIAN       ; AP -> KMK, akhir bulan HARIAN
//   akun L/R   : bank -> 8.52.01      ; AP -> 8.52.02 (belum terealisasi)
//
// Dipakai bareng oleh:
//   - auto-jurnal-selisih-kurs-ap.php      (form web, Preview lalu Post)
//   - sync_selisih_kurs_ap_scheduler.php   (otomatis, tanpa sesi login)
//
// ------------------------------------------------------------------------
// RUMUS, per TANGGAL x PROFIT CENTER x COA (persis kolom F s/d Q di sheet
// "Forex AP - GR" milik D:\File Project\Forex Gain or Loss - AP.xlsx):
//
//   saldo_usd_D   = saldo_usd_(D-1) + credit_usd_D - debit_usd_D
//                   (kewajiban: credit MENAMBAH utang)
//   nilai_pasar_D = saldo_usd_D x kurs pasar tanggal D (KMK; akhir bulan HARIAN)
//   nilai_buku_D  = opening_D + credit_idr_D - debit_idr_D
//                   opening_D = nilai_pasar_(D-1); untuk tanggal pertama =
//                   saldo awal USD x kurs pasar hari itu (fxApRatePasar)
//   selisih_D     = nilai_pasar_D - nilai_buku_D
//   opening_(D+1) = nilai_pasar_D        <- crystallize harian
//
// ARAH DEBIT/KREDIT (DIKOREKSI dari template Excel - keputusan user
// 24 Nov 2026). Ini akun KEWAJIBAN, jadi kebalikan dari akun bank:
//   selisih > 0  -> nilai utang NAIK  -> RUGI   -> Debit 8.52.02, Credit utang
//   selisih < 0  -> nilai utang TURUN -> UNTUNG -> Credit 8.52.02, Debit utang
// Template Excel memasang kebalikannya. Buktinya kolom Q template sendiri:
// 2.10.01 nilai buku 13.116.438,46 dengan selisih -98.384,22; Q (saldo
// sesudah penyesuaian) = 13.018.054,24 - itu hasil DIDEBIT. Kalau dikredit
// hasilnya 13.214.822,68, tidak sama dengan Q. Dampak Oktober: 120,25 juta.
//
// SALDO AWAL diambil dari AP Report (ap_report2/query_bpb2.php untuk GR/IR
// dan query_pv2.php untuk UTANG USAHA), bukan dari GL - keputusan user:
// "saldo awal ikuti saja excel, excel ambil dari ap report". Konsekuensinya
// angkanya memang bisa beda dari saldo GL; itu disengaja.
//
// COA yang direvaluasi TIDAK dipatok di kode: diambil dari flag
// mastercoa_v2.forex_ap = 'Y' (db_migrate_LIVE_20261124_forex_ap_coa.sql),
// yang bisa dicentang lewat menu Master COA. PROFIT CENTER sengaja TIDAK
// dipetakan sama sekali - diambil apa adanya dari tbl_list_journal, karena
// template Excel mematok 2.10/2.11/2.13 = NAG dan 2.12 = NAK padahal
// kenyataannya 2.10.11 bersaldo di NAG MAUPUN NAK dan 2.12.01 ada mutasi NAG.
//
// Nomor dokumen "FX-AP/{profit_center}/{mmyy}/{00001}" - seri sendiri,
// terpisah dari FX/ milik bank maupun GM memorial journal manual.
//
// Dijalankan ulang untuk tanggal yang sama = AMAN: dokumen lama dengan nomor
// yang sama dihapus barisnya lalu ditulis ulang. Sengaja hapus-tulis-ulang,
// bukan UPDATE seperti versi bank, karena DAFTAR COA-nya bisa berubah antar
// perhitungan (sebuah COA bisa jadi nol atau baru muncul) - UPDATE per baris
// akan meninggalkan baris basi.
// =========================================================================

require_once __DIR__ . '/fxsk_functions.php';   // fxSkTanggalIndo, fxSkIsPeriodOpen, fxSkCcSelisih, fxSkEnsureCategory

define('FXAP_COA_SELISIH', '8.52.02');
define('FXAP_ID_CMJ',      'CMJ021');                   // FOREX GAIN OR LOSS, sama dengan versi bank
define('FXAP_TYPE',        'FOREX GAIN OR LOSS - AP');  // type_journal di tbl_list_journal
define('FXAP_CURR',        'USD');
// DUA BATAS BAWAH, sengaja dipisah - diminta pemakai 24 Nov 2026
// ("pastikan mulainya awal 1 november 2026 saja", lalu diperjelas
// "1 november itu khusus pembentukan jurnal forex nya ya").
//
// FXAP_MIN_DATE = mulai MENGHITUNG. 1 Jul 2026 adalah batas data AP itu
// sendiri: kedua query laporan (query_bpb2.php / query_pv2.php) menyaring
// tgl_journal > '2026-06-30', jadi sebelum tanggal itu saldonya mustahil
// benar.
//
// FXAP_POST_FROM = mulai MEMBUAT JURNAL. Tanggal sebelum ini tetap
// dihitung - saldonya harus jalan supaya nilai buku 1 Nov benar - tapi
// tidak pernah jadi dokumen dan tidak ditampilkan di Preview.
//
// KENAPA DIPISAH, bukan sekadar menggeser FXAP_MIN_DATE ke 1 Nov: kalau
// digeser, saldo awal dibaca dari AP Report per 31 Okt yang KEBETULAN
// akhir bulan, sehingga dinilai kurs HARIAN sedangkan nilai pasar 1 Nov
// memakai kurs PAJAK - bedanya 96/USD, memunculkan satu dokumen raksasa
// 1 Nov yang bukan pergerakan kurs sungguhan. Dengan dipisah, rantai
// nilai buku mengalir wajar dari Juli dan 1 Nov hanya memuat pergerakan
// kurs PAJAK yang nyata.
//
// Biaya menghitung ulang dari Juli tiap malam tetap murah: saldo awal
// cuma 2 query laporan sekali jalan, sisanya perulangan PHP - bukan query
// per tanggal.
define('FXAP_MIN_DATE',    '2026-07-01');
define('FXAP_POST_FROM',   '2026-11-01');
define('FXAP_PREFIX',      'FX-AP/');


// ---------------------------------------------------------------- master --

// COA utang yang ikut direvaluasi - dari flag, bukan daftar di kode.
function fxApDaftarCoa($conn) {
    $coa = array();
    if (!fxApSiap($conn)) { return $coa; }
    $q = mysqli_query($conn, "select no_coa, nama_coa from mastercoa_v2 where forex_ap = 'Y' order by no_coa");
    while ($r = mysqli_fetch_assoc($q)) { $coa[trim($r['no_coa'])] = $r['nama_coa']; }
    return $coa;
}

// Kolom penandanya sudah ada atau belum. Kalau kode ini naik ke server lebih
// dulu daripada db_migrate_LIVE_20261124_forex_ap_coa.sql dijalankan, query
// di atas akan gagal dan hasilnya jadi "tidak ada selisih" - menyesatkan.
// Lebih baik ketahuan dan dikatakan apa adanya oleh pemanggilnya.
function fxApSiap($conn) {
    static $ada = null;
    if ($ada !== null) { return $ada; }
    $q = mysqli_query($conn, "show columns from mastercoa_v2 like 'forex_ap'");
    $ada = ($q && mysqli_num_rows($q) > 0);
    return $ada;
}

// Peta segala bentuk tulisan profit center -> kode_pc ('NAG'/'NAK'). Perlu
// karena laporan AP menulis nama panjang ("NIRWANA ALABARE GARMENT")
// sedangkan tbl_list_journal memakai kode pendek.
function fxApPetaPc($conn) {
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

// Nama divisi untuk KETERANGAN jurnal - pemakai minta 24 Nov 2026 supaya
// keterangannya terbaca orang, bukan kode: "jangan nag / nak tapi jika nag
// text nya jadi Divisi Garment kalau nak text nya Divisi Knitting".
//
// Sengaja TIDAK diambil dari master_pc.nama_pc: isinya "NIRWANA ALABARE
// GARMENT" / "NIRWANA ALABARE KNITTING", bukan kata yang diminta. Kalau
// ada divisi baru, tambahkan satu baris di sini; yang tidak terdaftar
// tetap memakai kodenya sendiri, jadi keterangannya tidak pernah kosong.
//
// Hanya mempengaruhi teks keterangan. Kolom profit_center di jurnal tetap
// diisi KODE (NAG/NAK) seperti semula - jangan diubah, banyak laporan
// menyaring memakai kode itu.
function fxApNamaDivisi($pc) {
    $peta = array(
        'NAG' => 'Divisi Garment',
        'NAK' => 'Divisi Knitting',
    );
    $k = strtoupper(trim((string) $pc));
    return isset($peta[$k]) ? $peta[$k] : $k;
}

function fxApKodePc($peta, $tulisan) {
    $k = strtoupper(trim((string) $tulisan));
    return isset($peta[$k]) ? $peta[$k] : $k;
}


// ------------------------------------------------------------------ kurs --

// Kurs pasar untuk menilai saldo utang pada $tanggal.
//
// ATURANNYA: kurs PAJAK (KMK) setiap hari, KECUALI tanggal AKHIR BULAN yang
// memakai kurs HARIAN. Itu aturan yang sama yang dipakai AP Report (CTE
// "rate" di ap_report2/query_bpb2.php), jadi saldo akhir bulan di buku besar
// jadi sama dengan yang dilihat accounting di laporan dan di neraca.
//
// Dipakai untuk DUA hal sekaligus - nilai pasar harian DAN nilai buku saldo
// awal - karena keduanya memang pertanyaan yang sama: "berapa nilai saldo ini
// pada tanggal itu". Dulu dipisah jadi fxApRatePajak() (PAJAK terus) dan
// fxApRateLaporan() (aturan akhir bulan); disatukan 24 Nov 2026 setelah
// pemakai memastikan "ap juga tolong ubah ternyata memang pakai kurs harian
// khusus akhir bulan".
//
// AKIBAT YANG HARUS DIKETAHUI: tiap tanggal akhir bulan muncul jurnal
// lonjakan (31 Okt 2026: KMK 18.032 vs HARIAN 18.128, beda 96/USD) lalu
// tanggal 1 bulan berikutnya muncul jurnal balik sebesar itu juga, karena
// nilai buku 1 Nov = nilai pasar 31 Okt. Itu MEMANG disengaja: neraca akhir
// bulan disajikan pada kurs HARIAN, bulan berjalan pada kurs KMK.
//
// Kalau kurs jenis yang diminta belum diisi di masterrate, diambil yang
// terdekat SEBELUMNYA (tanggal <= $tanggal), bukan dianggap nol.
function fxApRatePasar($conn, $tanggal) {
    static $ingat = array();
    if (isset($ingat[$tanggal])) { return $ingat[$tanggal]; }
    $d = mysqli_real_escape_string($conn, $tanggal);
    $q = mysqli_query($conn, "select rate from masterrate
         where curr = '" . FXAP_CURR . "' and tanggal <= '$d'
           and v_codecurr = CASE WHEN '$d' = LAST_DAY('$d') THEN 'HARIAN' ELSE 'PAJAK' END
         order by tanggal desc limit 1");
    $r = mysqli_fetch_assoc($q);
    $ingat[$tanggal] = $r ? (float) $r['rate'] : 1.0;
    return $ingat[$tanggal];
}


// ------------------------------------------------------------ saldo awal --

// Saldo utang valas per PROFIT CENTER x COA pada akhir $perTanggal, DARI
// LAPORAN AP (bukan GL). Dua query laporan di-include seperti yang sudah
// dilakukan ajx_ap_projection_report.php: keduanya membaca $_REQUEST dan
// menimpa $sql/$where, jadi isi request disetel dulu lalu dikembalikan.
// Return: array['NAG|2.10.01'] = saldo USD.
function fxApSaldoAwal($conn, $perTanggal, $peta) {
    $saldo = array();

    $simpanPost    = $_POST;
    $simpanRequest = $_REQUEST;
    $_REQUEST['start_date'] = $perTanggal;
    $_REQUEST['end_date']   = $perTanggal;
    $_REQUEST['nama_supp']  = 'ALL';
    unset($_POST['search']);

    $conn2   = $conn;          // nama yang diharapkan kedua berkas laporan
    $dirAsal = getcwd();
    chdir(__DIR__ . '/ap_report2');

    foreach (array('query_bpb2.php', 'query_pv2.php') as $berkas) {
        $sql = '';
        include $berkas;       // mengisi $sql
        if (trim($sql) === '') { continue; }
        $q = mysqli_query($conn2, $sql);
        if (!$q) { continue; }
        while ($r = mysqli_fetch_assoc($q)) {
            if (strtoupper(trim($r['curr'])) !== FXAP_CURR) { continue; }
            if (trim((string) $r['no_coa']) === '') { continue; }
            $kunci = fxApKodePc($peta, $r['profit_center']) . '|' . trim($r['no_coa']);
            if (!isset($saldo[$kunci])) { $saldo[$kunci] = 0.0; }
            $saldo[$kunci] += (float) $r['saldo_akhir'];
        }
    }

    chdir($dirAsal);
    $_POST    = $simpanPost;
    $_REQUEST = $simpanRequest;
    return $saldo;
}


// ---------------------------------------------------------------- mutasi --

// Mutasi harian per tanggal x profit center x COA, dalam USD dan IDR. Baris
// jurnal buatan tool ini sendiri ber-curr IDR, jadi saringan curr = 'USD'
// sudah otomatis mengecualikannya (tidak memakan hasil jurnalnya sendiri
// kalau dijalankan ulang).
function fxApMutasi($conn, $start, $end, $daftarCoa, $peta) {
    $mutasi = array();
    if (empty($daftarCoa)) { return $mutasi; }

    $in = array();
    foreach (array_keys($daftarCoa) as $c) { $in[] = "'" . mysqli_real_escape_string($conn, $c) . "'"; }
    $sE = mysqli_real_escape_string($conn, $start);
    $eE = mysqli_real_escape_string($conn, $end);

    $q = mysqli_query($conn, "select tgl_journal, profit_center, no_coa,
                sum(credit) kr, sum(debit) db, sum(credit_idr) kr_idr, sum(debit_idr) db_idr
           from tbl_list_journal
          where tgl_journal between '$sE' and '$eE'
            and curr = '" . FXAP_CURR . "'
            and status <> 'Cancel'
            and no_coa in (" . implode(',', $in) . ")
          group by tgl_journal, profit_center, no_coa");
    while ($r = mysqli_fetch_assoc($q)) {
        $kunci = fxApKodePc($peta, $r['profit_center']) . '|' . trim($r['no_coa']);
        $mutasi[$r['tgl_journal']][$kunci] = array(
            'usd' => (float) $r['kr']     - (float) $r['db'],
            'idr' => (float) $r['kr_idr'] - (float) $r['db_idr'],
        );
    }
    return $mutasi;
}


// -------------------------------------------------------------- hitungan --

// Dokumen FXAP yang SUDAH ADA untuk tanggal + profit center ini, kalau ada.
function fxApDokumenAda($conn, $tanggal, $pc) {
    $t = mysqli_real_escape_string($conn, $tanggal);
    $p = mysqli_real_escape_string($conn, $pc);
    $q = mysqli_query($conn, "select no_journal from tbl_list_journal
          where tgl_journal = '$t' and profit_center = '$p'
            and no_journal like '" . FXAP_PREFIX . "%' and status <> 'Cancel' limit 1");
    $r = mysqli_fetch_assoc($q);
    return $r ? $r['no_journal'] : null;
}

// Semua dokumen FXAP yang sudah ada di rentang ini, dikunci 'tanggal|pc'.
// Dipakai untuk menemukan dokumen BASI: jurnal yang pernah dibuat tapi pada
// hitungan sekarang selisihnya jadi nol (mis. BPB-nya dibatalkan). Tanpa ini
// angka lama akan tertinggal diam-diam di buku besar.
function fxApDokumenRentang($conn, $start, $end) {
    $s = mysqli_real_escape_string($conn, $start);
    $e = mysqli_real_escape_string($conn, $end);
    $q = mysqli_query($conn, "select distinct no_journal, tgl_journal, profit_center
          from tbl_list_journal
          where tgl_journal between '$s' and '$e'
            and no_journal like '" . FXAP_PREFIX . "%' and status <> 'Cancel'");
    $hasil = array();
    while ($r = mysqli_fetch_assoc($q)) {
        $hasil[$r['tgl_journal'] . '|' . $r['profit_center']] = $r['no_journal'];
    }
    return $hasil;
}

// Hitung revaluasi seluruh rentang. Return:
//   ['baris'   => [...per tanggal x pc x coa, hanya yang selisihnya != 0;
//                  termasuk yang periodenya sudah Closed, ditandai
//                  'periode_tutup' => true...],
//    'dokumen' => ['2026-10-01|NAG' => ['tanggal','pc','net','baris',
//                                       'dokumen_ada']] - periode Closed
//                  TIDAK pernah masuk ke sini,
//    'rate_awal' => kurs pembuka, 'per_tanggal' => tanggal saldo awal]
function fxApHitung($conn, $start, $end) {
    if ($start < FXAP_MIN_DATE) { $start = FXAP_MIN_DATE; }
    $kosong = array('baris' => array(), 'dokumen' => array(), 'rate_awal' => 0, 'per_tanggal' => '');
    if ($end < $start) { return $kosong; }

    $daftarCoa = fxApDaftarCoa($conn);
    $peta      = fxApPetaPc($conn);

    $perTanggal = date('Y-m-d', strtotime($start . ' -1 day'));
    $saldoAwal  = fxApSaldoAwal($conn, $perTanggal, $peta);
    $rateAwal   = fxApRatePasar($conn, $perTanggal);
    $mutasi     = fxApMutasi($conn, $start, $end, $daftarCoa, $peta);

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
        $ratePasar   = fxApRatePasar($conn, $d);
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
            if ($d < FXAP_POST_FROM) { continue; }

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
                    'dokumen_ada' => fxApDokumenAda($conn, $d, $pc),
                );
            }
            $dokumen[$kd]['net'] += $selisih;
            $dokumen[$kd]['baris'][] = $satu;
        }
    }

    // Dokumen basi: pernah dijurnal, sekarang tidak ada selisih lagi. Masuk
    // daftar dengan baris KOSONG - fxApTulisJurnal() menghapus baris lamanya
    // lalu tidak menulis apa pun, jadi dokumennya hilang dengan sendirinya.
    // Periode yang sudah Closed sengaja tidak disentuh.
    // Dibatasi mulai FXAP_POST_FROM - jangan sampai dokumen di luar
    // jangkauan jurnal ikut terhapus.
    $awalBasi = ($start > FXAP_POST_FROM) ? $start : FXAP_POST_FROM;
    foreach (fxApDokumenRentang($conn, $awalBasi, $end) as $kd => $noJurnal) {
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
    );
}


// --------------------------------------------------------------- posting --

// Nomor dokumen baru "FX-AP/{pc}/{mmyy}/{00001}" - urut per profit center per
// bulan, dibaca dari tbl_list_journal (tempat barisnya sendiri disimpan).
function fxApNomorBaru($conn, $pc, $tanggal) {
    $awalan = FXAP_PREFIX . $pc . '/' . date('my', strtotime($tanggal));
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
function fxApTulisJurnal($connBaca, $connTulis, $doc, $isi, $user) {
    $tanggal = $isi['tanggal'];
    $pc      = $isi['pc'];
    $net     = $isi['net'];
    $buat    = date('Y-m-d H:i:s');

    // Huruf besar semua - diminta pemakai 24 Nov 2026. Dibungkus di SINI,
    // bukan di dalam fxSkTanggalIndo(), karena fungsi itu dipakai bersama
    // jurnal selisih kurs BANK yang keterangannya tidak boleh berubah.
    $ket  = strtoupper('REVALUASI KURS UTANG USAHA ' . fxApNamaDivisi($pc) . ' PER ' . fxSkTanggalIndo($tanggal));
    $ketE = mysqli_real_escape_string($connTulis, $ket);
    $docE = mysqli_real_escape_string($connTulis, $doc);
    $pcE  = mysqli_real_escape_string($connTulis, $pc);

    // Tulis ulang bersih - daftar COA-nya bisa berubah antar perhitungan.
    mysqli_query($connTulis, "delete from tbl_list_journal where no_journal = '$docE' and no_journal like '" . FXAP_PREFIX . "%'");
    mysqli_query($connTulis, "delete from tbl_memorial_journal where no_mj = '$docE' and no_mj like '" . FXAP_PREFIX . "%'");

    // Cost center 8.52.02 dicari per profit center dari master, sama persis
    // dengan jurnal selisih kurs bank (NAG -> DEP06SUB001, NAK -> DEPNK09SUB001).
    $cc     = fxSkCcSelisih($connBaca, $pc);
    $ccNo   = $cc['no_cc'];
    $ccNama = $cc['cc_name'];

    $rSk = mysqli_fetch_assoc(mysqli_query($connBaca, "select nama_coa from mastercoa_v2 where no_coa = '" . FXAP_COA_SELISIH . "'"));
    $namaCoaSk = $rSk ? $rSk['nama_coa'] : 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI';

    // Baris utang: selisih > 0 = utang bertambah = DIKREDIT ; < 0 = DIDEBIT.
    $legs = array();
    foreach ($isi['baris'] as $b) {
        $nilai = abs($b['selisih']);
        $legs[] = array($b['no_coa'], $b['nama_coa'], ($b['selisih'] < 0 ? $nilai : 0), ($b['selisih'] > 0 ? $nilai : 0), '-', '-');
    }
    // Baris lawan 8.52.02: net > 0 = RUGI = DIDEBIT ; net < 0 = UNTUNG = DIKREDIT.
    $legs[] = array(FXAP_COA_SELISIH, $namaCoaSk, ($net > 0 ? abs($net) : 0), ($net < 0 ? abs($net) : 0), $ccNo, $ccNama);

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
            ('$docE', '$tanggal', '" . FXAP_TYPE . "', '$coaE', '$namaCoaE', '$ccE', '$namaCcE', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Post', '$ketE', '$user', '$buat', '', '', '', '', '$pcE')");

        mysqli_query($connTulis, "insert into tbl_memorial_journal
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center)
            values
            ('$docE', '$tanggal', '" . FXAP_ID_CMJ . "', '$coaE', '$ccE', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', '$ketE', 'Post', '$user', '$buat', '$pcE')");

        $jumlahBaris++;
    }

    return $jumlahBaris;
}

// Post seluruh hasil hitungan dalam SATU transaksi. $hasil = keluaran
// fxApHitung(). Return ['ok'=>bool, 'dokumen'=>[...], 'message'=>..].
function fxApPost($connBaca, $connTulis, $hasil, $user) {
    if (empty($hasil['dokumen'])) { return array('ok' => true, 'dokumen' => array()); }

    fxSkEnsureCategory($connBaca);   // kategori CMJ021, idempotent

    mysqli_begin_transaction($connTulis);
    try {
        $jadi = array();
        foreach ($hasil['dokumen'] as $isi) {
            $lama = $isi['dokumen_ada'];
            $doc  = $lama ? $lama : fxApNomorBaru($connTulis, $isi['pc'], $isi['tanggal']);
            $n    = fxApTulisJurnal($connBaca, $connTulis, $doc, $isi, $user);
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
