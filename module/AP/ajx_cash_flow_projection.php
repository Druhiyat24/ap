<?php
/* ============================================================================
   CASH FLOW PROJECTION - MESIN HITUNG.
   Satu berkas ini dipakai LAYAR dan EKSPOR EXCEL supaya keduanya mustahil
   berbeda. Ekspor memanggilnya dgn define('CFP_PUSTAKA') lalu memakai
   keluarannya apa adanya - pola yang sama dgn ekspor proyeksi AP.

   KONEKSI SENGAJA TIDAK DI-INCLUDE DI SINI. Panggilan ke mesin proyeksi AP
   di bawah sudah meng-include ../../conn/conn.php, sedangkan
   conn/parser-php-version.php TIDAK berpagar function_exists - meng-include
   conn dua kali = fatal "cannot redeclare". Jadi $conn1/$conn2 datang dari
   panggilan itu.

   SEMUA PEUBAH DI SINI BERAWALAN $cfp. Mesin proyeksi AP di-include ke
   dalam lingkup yang SAMA dan memakai nama umum ($mulai, $akhir, $type, $q,
   $r, $data, ...). Tanpa awalan, nilainya akan saling menimpa diam-diam.

   Susunan barisnya mengikuti lembar "Cash Flow Daily" di
   D:\File Project\Cash Flow Projection Report.xlsx.
   ============================================================================ */

if (!defined('CFP_PUSTAKA')) { session_start(); header('Content-Type: application/json'); }

function cfpKeluar($pesan)
{
    echo json_encode(array('error' => $pesan));
    if (defined('CFP_PUSTAKA')) { return true; }
    exit;
}

/* Isian tanggal halaman berbentuk dd-mm-yyyy. */
function cfpTgl($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));
    return $t ? date('Y-m-d', $t) : '';
}

/* cfpKunci(), cfpIkut() dan DAFTAR AKUN-nya ada di pustaka bersama -
   dipakai juga pengisi dropdown Bank (ajx_cf_bank.php), supaya isi dropdown
   mustahil berbeda dari isi laporan. */
require_once __DIR__ . '/cf_akun_lib.php';

function cfpIdr($a, $nilai, $rate) { return ($a['curr'] === 'USD') ? $nilai * $rate : $nilai; }

function cfpBaris($tipe, $no, $label, $v, $tot)
{
    return array('tipe' => $tipe, 'no' => $no, 'label' => $label, 'v' => $v, 'tot' => $tot);
}

/* ---------------------------------------------------------------------------
   MASUKAN
   --------------------------------------------------------------------------- */
$cfpMulai = cfpTgl(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$cfpAkhir = cfpTgl(isset($_POST['end_date'])   ? $_POST['end_date']   : '');
$cfpPc    = strtoupper(trim(isset($_POST['pc'])   ? $_POST['pc']   : 'ALL'));
$cfpBank  = trim(isset($_POST['bank']) ? $_POST['bank'] : 'ALL');
$cfpType  = strtolower(trim(isset($_POST['type']) ? $_POST['type'] : 'daily'));
$cfpRate  = (float) str_replace(',', '', (string) (isset($_POST['rate']) ? $_POST['rate'] : 0));

/* Nama profit center untuk judul laporan - sama persis dgn tulisan di
   dropdown halamannya. Judul TIDAK memakai nama perusahaan karena laporan
   ini melayani Garment maupun Knitting dari satu halaman. */
$cfpNamaPc = array('ALL' => 'All PC', '' => 'All PC',
                   'NAG' => 'Garment (NAG)', 'NAK' => 'Knitting (NAK)');

/* Nama rekening terpilih untuk kepala laporan. Kunci yang tidak dikenali
   (mis. rekening yang sudah dinonaktifkan sesudah potret dibuat) dikembalikan
   apa adanya - lebih baik terbaca mentah daripada hilang tanpa jejak. */
function cfpLabelBank($akun, $kunci, $mentah)
{
    if (!$kunci) { return 'All Banks'; }
    $nama = array();
    foreach ($kunci as $k) { $nama[] = isset($akun[$k]) ? $akun[$k]['label'] : $k; }
    return $nama ? implode('; ', $nama) : (string) $mentah;
}

if ($cfpMulai === '' || $cfpAkhir === '') { if (cfpKeluar('Please fill in both the period From and To dates.')) { return; } }
if ($cfpMulai > $cfpAkhir)                { if (cfpKeluar('The From date must not be later than the To date.')) { return; } }

$cfpJmlHari = (int) ((strtotime($cfpAkhir) - strtotime($cfpMulai)) / 86400) + 1;
if ($cfpJmlHari > 62) { if (cfpKeluar('The projection is limited to 62 days.')) { return; } }

$cfpTanggal = array();
for ($i = 0; $i < $cfpJmlHari; $i++) { $cfpTanggal[] = date('Y-m-d', strtotime($cfpMulai . ' +' . $i . ' day')); }
$cfpSebelum = date('Y-m-d', strtotime($cfpMulai . ' -1 day'));
$cfpPesan   = array();

/* ---------------------------------------------------------------------------
   1. PROYEKSI AP - POTRET DULU, baru hitung ulang.

   Aturannya ditegaskan pemakai 18 Nov 2026: selama ada potret AP yang
   periodenya MENYELIMUTI rentang laporan ini, angkanya dibaca dari potret
   itu. Baru kalau tidak ada, mesinnya dipanggil - dan hasilnya TIDAK
   disimpan ke mana pun.

   Mesinnya dipanggil dgn include, BUKAN disalin, supaya angka utang usaha di
   sini tidak bisa berbeda dari AP Payment Projection Report. Sekalian berkas
   itu yang membawa koneksi.
   --------------------------------------------------------------------------- */
/* MESINNYA TETAP DIPANGGIL LEBIH DULU, walau nanti hasilnya mungkin tidak
   dipakai - karena BERKAS ITU yang membawa koneksi ($conn2), dan koneksi itu
   yang dibutuhkan untuk mencari potretnya. Membuka koneksi sendiri di sini
   tidak bisa: conn.php meng-include parser-php-version.php tanpa _once, jadi
   dua kali include berujung "Cannot redeclare is_mysqli_or_resource()".
   Harganya satu panggilan mesin AP yang terbuang kalau potretnya ada -
   murah, dan jauh lebih aman daripada mengutak-atik berkas koneksi yang
   dipakai seluruh aplikasi. */
$cfpPostAsli = $_POST;
$_POST = array('start_date' => date('d-m-Y', strtotime($cfpMulai)),
               'end_date'   => date('d-m-Y', strtotime($cfpAkhir)),
               'type'       => 'daily', 'nama_supp' => '');
if (!defined('APPR_PUSTAKA')) { define('APPR_PUSTAKA', 1); }
ob_start();
include __DIR__ . '/ajx_ap_projection_report.php';
$cfpApJson = trim((string) ob_get_clean());
$_POST     = $cfpPostAsli;

$cfpAp = json_decode($cfpApJson, true);
$cfpApBaris = (is_array($cfpAp) && isset($cfpAp['data'])) ? $cfpAp['data'] : array();
if (is_array($cfpAp) && isset($cfpAp['error'])) { $cfpPesan[] = 'AP projection: ' . $cfpAp['error']; }

/* POTRET MENANG kalau ada yang menyelimuti periode ini (ditegaskan pemakai
   18 Nov 2026). Hasil mesin di atas dibuang, bukan dicampur.

   KECUALI DAILY (permintaan pemakai 20 Nov 2026): proyeksi harian SELALU
   menarik AR & AP ulang, tidak pernah memakai potret. Potret AR/AP dibuat
   per pekan atau per bulan - angkanya dibekukan pada satu saat tertentu -
   sedangkan laporan harian dipakai untuk melihat keadaan HARI INI. Memakai
   potret di situ berarti menampilkan keadaan beberapa hari lalu di layar
   yang justru dibuka untuk mengetahui keadaan sekarang. */
require_once __DIR__ . '/cf_sumber_potret.php';
$cfpPakaiPotret = ($cfpType !== 'daily');
$cfpApDoc    = '';
$cfpApPotret = $cfpPakaiPotret ? cf_potret_ap($conn2, $cfpMulai, $cfpAkhir) : null;
if ($cfpApPotret) {
    $cfpApDoc   = $cfpApPotret['doc'];
    $cfpApBaris = $cfpApPotret['baris'];
}

/* KURS USD TIDAK LAGI DIISI DI LAYAR (permintaan pemakai 16 Nov 2026):
   diambil sendiri dari kurs PAJAK terakhir yang tanggalnya <= tanggal To -
   periode lampau memakai kurs saat itu, periode depan memakai kurs terakhir
   yang ada. Angkanya TETAP dicetak di kepala laporan & di Excel supaya bisa
   ditelusuri; yang hilang cuma kotak isiannya. */
if ($cfpRate <= 0) {
    $cfpR = mysqli_fetch_assoc(mysqli_query($conn2,
        "select rate from masterrate where curr = 'USD' and v_codecurr = 'PAJAK'
           and tanggal <= '$cfpAkhir' order by tanggal desc limit 1"));
    $cfpRate = $cfpR ? (float) $cfpR['rate'] : 0;
}
if ($cfpRate <= 0) { if (cfpKeluar('No USD tax rate is available for this period.')) { return; } }

/* ---------------------------------------------------------------------------
   2. DAFTAR AKUN (bank + kas) - dari pustaka bersama cf_akun_lib.php.
   Keterangan lengkap soal sumber, status pinjaman, limit dan profit center
   akun kas ada di berkas itu, tidak diulang di sini.
   --------------------------------------------------------------------------- */
$cfpKasTanpaPc = array();
$cfpAkun = cf_daftar_akun($conn2, $cfpKasTanpaPc);

/* Setelan yang TIDAK bertanggal: minimum balance & limit per rekening. */
$cfpSetel = array();
$cfpQ = mysqli_query($conn2, "show tables like 'ap_cf_bank_setting'");
if ($cfpQ && mysqli_num_rows($cfpQ)) {
    $cfpQ = mysqli_query($conn2, "select akun, min_balance from ap_cf_bank_setting
                                  where doc_number is null or doc_number = ''");
    while ($cfpR = mysqli_fetch_assoc($cfpQ)) { $cfpSetel[cfpKunci($cfpR['akun'])] = $cfpR; }
}

/* Saldo awal yang diketik tangan - HANYA yang tanggalnya SAMA PERSIS dgn
   tanggal From. Saldo awal selalu melekat pada satu tanggal; kalau dibaca
   tanpa tanggal, angka yang diketik untuk November ikut terpakai waktu
   laporan dijalankan untuk Desember - dan salahnya tidak kentara karena
   laporannya tetap tampil rapi. */
$cfpTangan = array();
$cfpQ = mysqli_query($conn2, "show tables like 'ap_cf_opening'");
if ($cfpQ && mysqli_num_rows($cfpQ)) {
    $cfpQ = mysqli_query($conn2, "select akun, saldo_manual, keterangan, update_by, update_date
                                    from ap_cf_opening where tanggal = '$cfpMulai'");
    while ($cfpR = mysqli_fetch_assoc($cfpQ)) { $cfpTangan[cfpKunci($cfpR['akun'])] = $cfpR; }
}

/* LARIK kunci rekening, kosong = semua (saringan Bank bisa dicentang
   beberapa sejak 19 Nov 2026 - lihat cfpDaftarKunciBank di cf_akun_lib.php). */
$cfpKunciBank = cfpDaftarKunciBank($cfpBank);

/* ---------------------------------------------------------------------------
   3. SALDO AWAL per akun, per H-1 dari tanggal From.
   Rumusnya SAMA dgn bankreport.php / cashreport.php: saldo awal master +
   mutasi sebelum tanggal itu, dgn baris revaluasi kurs (no_doc "FX/%")
   di-nol-kan. Diuji 31 Okt 2026: sembilan dari sepuluh rekening sama persis
   dgn lembar Balance di Excel (BNI beda karena di Excel dinaikkan manual -
   itulah gunanya kolom saldo_pakai).
   --------------------------------------------------------------------------- */
$cfpSaldo = array();
foreach ($cfpAkun as $cfpK => $cfpA) {
    if ($cfpA['kas']) {
        $e = mysqli_real_escape_string($conn2, $cfpK);
        $cfpR = mysqli_fetch_assoc(mysqli_query($conn2,
            "select coalesce(amount,0) s from b_saldoawal_pettycash where account = '$e'"));
        $s = $cfpR ? (float) $cfpR['s'] : 0;
        $cfpR = mysqli_fetch_assoc(mysqli_query($conn2,
            "select coalesce(sum(debit - credit),0) m from c_report_pettycash
             where akun = '$e' and transaksi_date <= '$cfpSebelum' and status <> 'Cancel'"));
        $s += (float) $cfpR['m'];
    } else {
        $cfpR = mysqli_fetch_assoc(mysqli_query($conn2,
            "select coalesce(amount,0) s from b_saldoawal_bank
             where replace(replace(account,'-',''),' ','') = '$cfpK' limit 1"));
        $s = $cfpR ? (float) $cfpR['s'] : 0;
        $cfpR = mysqli_fetch_assoc(mysqli_query($conn2,
            "select coalesce(sum(if(no_doc like 'FX/%',0,debit) - if(no_doc like 'FX/%',0,credit)),0) m
             from b_reportbank
             where replace(replace(akun,'-',''),' ','') = '$cfpK'
               and transaksi_date <= '$cfpSebelum' and status <> 'Cancel'"));
        $s += (float) $cfpR['m'];
    }
    /* tarik = hitungan sistem (selalu disimpan sbg pembanding)
       pakai = angka yang benar-benar dipakai laporan */
    $cfpSaldo[$cfpK] = array('tarik' => $s, 'pakai' => $s,
                             'manual' => null, 'manual_by' => '', 'manual_date' => '');
    if (isset($cfpTangan[$cfpK])) {
        /* Angka tangan DISIMPAN DALAM RUPIAH; saldo rekening dipegang dalam
           mata uangnya sendiri, jadi dibagi kurs DI SINI - bukan waktu
           disimpan. Pembagiannya tidak pernah dibulatkan ke basis data,
           jadi dikalikan kurs lagi hasilnya persis seperti yang diketik. */
        $t = $cfpTangan[$cfpK];
        $kurs = (strtoupper($cfpA['curr']) === 'USD' && $cfpRate > 0) ? $cfpRate : 1;
        $cfpSaldo[$cfpK]['pakai']       = (float) $t['saldo_manual'] / $kurs;
        $cfpSaldo[$cfpK]['manual']      = (float) $t['saldo_manual'];   /* rupiah, apa adanya */
        $cfpSaldo[$cfpK]['manual_by']   = (string) $t['update_by'];
        $cfpSaldo[$cfpK]['manual_date'] = (string) $t['update_date'];
    }
}

/* Penampung untuk nilai yang rekeningnya tidak dikenali - mis. invoice AR
   yang kolom banknya masih "-". Nilainya IKUT dihitung, tetapi HANYA saat
   saringan Bank dan Profit Center sama-sama ALL: uangnya tidak hilang dari
   total, dan juga tidak ditempelkan ke rekening yang salah. Pc dikosongkan,
   jadi cfpIkut() menolaknya sendiri begitu salah satu saringan dipakai. */
$cfpAkun['(none)'] = array('kunci' => '(none)', 'label' => '(unassigned account)', 'curr' => 'IDR',
                           'pc' => '', 'loan' => false, 'limit' => 0, 'kas' => false);
$cfpSaldo['(none)'] = array('tarik' => 0, 'pakai' => 0,
                            'manual' => null, 'manual_by' => '', 'manual_date' => '');

/* ---------------------------------------------------------------------------
   4. KATEGORI - susunan barisnya diambil dari master_cash_flow.
   --------------------------------------------------------------------------- */
$cfpKtg = array();
$cfpKtgNama = array();
$cfpUrutGrup = "FIELD(nama_category,'OPERATING ACTIVITIES','INVESTING ACTIVITIES','FINANCING ACTIVITIES')";
$cfpQ = mysqli_query($conn2, "select id, type_cashflow, nama_category, nama_subcategory, display_seq
                              from master_cash_flow where status = 'Y'
                              order by type_cashflow desc, IF($cfpUrutGrup = 0, 9, $cfpUrutGrup) asc, urutan asc");
while ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) {
    $cfpKtg[(int) $cfpR['id']] = array('id' => (int) $cfpR['id'], 'tipe' => $cfpR['type_cashflow'],
                                       'grup' => $cfpR['nama_category'], 'nama' => $cfpR['nama_subcategory'],
                                       'no' => $cfpR['display_seq']);
    $cfpKtgNama[strtoupper(trim($cfpR['nama_subcategory']))] = (int) $cfpR['id'];
}
function cfpId($peta, $nama, $bawaan) { $k = strtoupper($nama); return isset($peta[$k]) ? $peta[$k] : $bawaan; }

$cfpIdInv   = cfpId($cfpKtgNama, 'Penerimaan Piutang Usaha (Existing)', 1);
$cfpIdDn    = cfpId($cfpKtgNama, 'Penerimaan Piutang Lain-lain', 3);
$cfpIdUtang = cfpId($cfpKtgNama, 'Pengeluaran Pembayaran Utang Usaha', 13);
$cfpIdLain  = cfpId($cfpKtgNama, 'Pengeluaran Lain-lain', 39);
$cfpIdAntarK = cfpId($cfpKtgNama, 'Pengeluaran Antar Bank (Pemindahbukuan Internal)', 38);
$cfpIdAntarM = cfpId($cfpKtgNama, 'Penerimaan Antar Bank (Dalam Satu PT)', 6);
$cfpIdTerima = cfpId($cfpKtgNama, 'Penerimaan Pinjaman Bank', 9);
$cfpIdLunas  = cfpId($cfpKtgNama, 'Pelunasan Pinjaman Bank', 47);

/* ---------------------------------------------------------------------------
   5. PENGUMPULAN MUTASI.
   $cfpGerak[tanggal][kunciAkun][idKategori] = nilai dalam mata uang AKUN itu.
   Positif = uang masuk, negatif = uang keluar.
   --------------------------------------------------------------------------- */
$cfpGerak = array();
$cfpTakAkun = array();
$cfpTaruh = function ($tgl, $kunci, $idKtg, $nilai) use (&$cfpGerak, $cfpAkun, $cfpTanggal) {
    if ($nilai == 0 || $kunci === '' || !isset($cfpAkun[$kunci])) { return; }
    if (!in_array($tgl, $cfpTanggal, true)) { return; }
    if (!isset($cfpGerak[$tgl][$kunci][$idKtg])) { $cfpGerak[$tgl][$kunci][$idKtg] = 0; }
    $cfpGerak[$tgl][$kunci][$idKtg] += $nilai;
};

/* 5a. PIUTANG (AR). Sumbernya POTRET proyeksi AR yang disimpan menu AR
   (tbl_history_projection_*) - potret terbaru yang periodenya MELINGKUPI
   rentang laporan. Uang yang benar-benar diterima = amount_idr dikalikan
   porsi collection_amount/total_invoice, yaitu nilai SETELAH dipotong
   PPh 23 - PPh bukan arus kas. Diuji utk Nov 2026: sama persis dgn Excel
   (Invoice 28.045.045.530, Debit Note 6.446.039.260). */
/* Potretnya TIDAK DICARI SAMA SEKALI untuk Daily - lihat keterangan di
   bagian AP di atas. Dibiarkan kosong, jadi jalur di bawah menghitung ulang
   seperti ketika memang tidak ada potret yang menyelimuti periodenya. */
$cfpArDoc = ''; $cfpArTgl = '';
if ($cfpPakaiPotret) {
    $cfpQ = mysqli_query($conn2, "select doc_number, created_at from tbl_history_projection_h
                                  where periode_dari <= '$cfpMulai' and periode_sampai >= '$cfpAkhir'
                                  order by id desc limit 1");
    if ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) { $cfpArDoc = $cfpR['doc_number']; $cfpArTgl = $cfpR['created_at']; }
}

/* Barisnya dikumpulkan dulu ke satu bentuk - tgl, doc_type, bank_acc, nilai -
   apa pun sumbernya, baru ditaruh. Dgn begitu aturan penempatannya (akun,
   kategori Invoice/Debit Note, akun USD) ditulis SATU KALI saja; kalau
   dipisah per sumber, dua jalur itu cepat atau lambat akan berbeda. */
$cfpArBaris = array();   /* untuk lembar Excel, hanya terisi kalau dihitung ulang */
$cfpArRinci = array();

if ($cfpArDoc !== '') {
    $e = mysqli_real_escape_string($conn2, $cfpArDoc);
    $cfpQ = mysqli_query($conn2, "
        select coalesce(duedate_update, duedate) tgl, doc_type, bank_acc,
               sum(case when coalesce(total_invoice,0) <> 0
                        then amount_idr * collection_amount / total_invoice
                        else amount_idr end) nilai
          from tbl_history_projection_det
         where doc_number = '$e'
           and coalesce(duedate_update, duedate) between '$cfpMulai' and '$cfpAkhir'
         group by 1,2,3");
    while ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) { $cfpArRinci[] = $cfpR; }
} else {
    /* TIDAK ADA POTRET -> dihitung ulang dgn mesin AR, dan TIDAK DISIMPAN.
       Rumus nilainya sama persis dgn yang dipakai di atas, cuma dijumlahkan
       di PHP karena barisnya sudah berupa array. */
    $cfpArBaris = cf_ar_hitung($conn2, $cfpMulai, $cfpAkhir, $cfpType);
    $cfpKumpul = array();
    foreach ($cfpArBaris as $cfpB) {
        $tgl = (string) (isset($cfpB['duedate_update']) && $cfpB['duedate_update']
                         ? $cfpB['duedate_update'] : (isset($cfpB['duedate']) ? $cfpB['duedate'] : ''));
        if ($tgl === '' || $tgl < $cfpMulai || $tgl > $cfpAkhir) { continue; }
        $ti  = isset($cfpB['total_invoice']) ? (float) $cfpB['total_invoice'] : 0;
        $ai  = isset($cfpB['amount_idr']) ? (float) $cfpB['amount_idr'] : 0;
        $col = isset($cfpB['collection_amount']) ? (float) $cfpB['collection_amount'] : 0;
        $n   = ($ti != 0) ? ($ai * $col / $ti) : $ai;
        $dt  = isset($cfpB['doc_type']) ? (string) $cfpB['doc_type'] : 'Invoice';
        $ba  = isset($cfpB['bank_acc']) ? (string) $cfpB['bank_acc'] : '';
        $kk  = $tgl . '|' . $dt . '|' . $ba;
        if (!isset($cfpKumpul[$kk])) {
            $cfpKumpul[$kk] = array('tgl' => $tgl, 'doc_type' => $dt, 'bank_acc' => $ba, 'nilai' => 0);
        }
        $cfpKumpul[$kk]['nilai'] += $n;
    }
    $cfpArRinci = array_values($cfpKumpul);
    $cfpPesan[] = $cfpArRinci
        ? 'No saved AR projection covers this period, so the receivable lines were recalculated (not saved).'
        : 'No saved AR projection covers this period, and recalculating found no receivable falling due.';
}

foreach ($cfpArRinci as $cfpR) {
    $k = cfpKunci($cfpR['bank_acc']);
    if (!isset($cfpAkun[$k])) { $cfpTakAkun[] = 'AR ' . $cfpR['bank_acc']; $k = '(none)'; }
    $id = (stripos($cfpR['doc_type'], 'debit') !== false) ? $cfpIdDn : $cfpIdInv;
    /* Nilainya sudah rupiah; akun USD disimpan dalam USD-nya sendiri. */
    $n = (float) $cfpR['nilai'];
    $cfpTaruh($cfpR['tgl'], $k, $id, ($cfpAkun[$k]['curr'] === 'USD') ? $n / $cfpRate : $n);
}

/* 5b. UTANG USAHA (AP) - dari mesin proyeksi AP yang dipanggil di atas.
   Rekening pembayarnya tidak ikut di keluaran mesin itu, jadi dicari di sini
   lewat dokumen induknya. Nomor cicilan ada DUA bentuk (sama dgn
   ajx_ap_projection_report.php):
     'SI/APR/NAG/2025/12/02515 - 10'    -> induk 'SI/APR/NAG/2025/12/02515'
     'PV-AP/INS/NAG/2026/08/00001-002'  -> induk 'PV-AP/INS/NAG/2026/08/00001'
   Bentuk kedua dulu tidak dikenali (27 Nov 2026): PC, Bank & Creator di
   lembar PV AP kosong, dan nilainya masuk kolom "(none)", bukan rekeningnya. */
if ($cfpApBaris) {
    $cfpDaftar = array();
    foreach ($cfpApBaris as $cfpB) {
        if (isset($cfpB['no_kbon']) && $cfpB['no_kbon'] !== '') {
            $cfpDaftar[] = mysqli_real_escape_string($conn2, $cfpB['no_kbon']);
        }
    }
    $cfpPetaKbon = array();
    if ($cfpDaftar) {
        $cfpIn = "'" . implode("','", array_unique($cfpDaftar)) . "'";
        $cfpQ = mysqli_query($conn2, "
            select x.no_kbon,
                   coalesce(k.from_account, i.from_account, s.from_account, si.from_account)     from_account,
                   coalesce(k.profit_center, i.profit_center, s.profit_center, si.profit_center) profit_center,
                   /* Kolom Creator di lembar PV AP - ikut diambil di sini,
                      bukan lewat query sendiri: dokumen induknya sudah di-join
                      untuk rekening & profit center. */
                   coalesce(k.create_user, i.create_user, s.create_user, si.create_user)         create_user
              from (select u.no_kbon,
                           case when u.no_kbon like '% - %' then substring_index(u.no_kbon, ' - ', 1)
                                when u.no_kbon regexp '-[0-9]{3}\$' then left(u.no_kbon, char_length(u.no_kbon) - 4)
                                else u.no_kbon end induk
                      from (select no_kbon from ap_payment_plan_det where no_kbon in ($cfpIn)
                             union
                            select no_kbon from kontrabon_h where no_kbon in ($cfpIn)
                             union
                            select no_kbon from ap_saldo_payment_voucher where no_kbon in ($cfpIn)
                             union
                            /* anak cicilan yang belum masuk Payment Plan */
                            select no_kbon_det from kontrabon_h_installment_detail where no_kbon_det in ($cfpIn)) u) x
              left join kontrabon_h k on k.no_kbon = x.no_kbon
              left join kontrabon_h i on i.no_kbon = x.induk
              left join ap_saldo_payment_voucher s on s.no_kbon = x.no_kbon
              left join ap_saldo_payment_voucher si on si.no_kbon = x.induk");
        while ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) { $cfpPetaKbon[$cfpR['no_kbon']] = $cfpR; }
    }
    foreach ($cfpApBaris as $cfpB) {
        $no  = isset($cfpB['no_kbon']) ? $cfpB['no_kbon'] : '';
        $inf = isset($cfpPetaKbon[$no]) ? $cfpPetaKbon[$no] : null;
        $k   = $inf ? cfpKunci($inf['from_account']) : '';
        if ($k === '' || !isset($cfpAkun[$k])) { $cfpTakAkun[] = 'AP ' . $no; $k = '(none)'; }
        foreach ($cfpTanggal as $t) {
            $v = isset($cfpB['d_' . $t]) ? (float) str_replace(',', '', $cfpB['d_' . $t]) : 0;
            if ($v == 0) { continue; }
            /* Keluaran mesin AP selalu rupiah; akun USD dikembalikan ke USD. */
            $cfpTaruh($t, $k, $cfpIdUtang, -(($cfpAkun[$k]['curr'] === 'USD') ? $v / $cfpRate : $v));
        }
    }
}

/* 5c. PV BIAYA + DP + CBD - rencana bayar dari menu Update Payment Date.
   Rekening pembayar & kategorinya letaknya beda-beda menurut jenis dokumen,
   jadi potongan SQL-nya diambil dari pvb_dpcbd_lib.php - satu tempat yang
   sama dgn lembar sumber Excel, supaya dua-duanya mustahil berbeda. Dokumen
   yang kategorinya belum diisi jatuh ke "Pengeluaran Lain-lain" supaya
   uangnya TIDAK hilang dari laporan; jumlahnya dilaporkan di bagian info. */
$cfpPvTanpaKtg = 0;
require_once __DIR__ . '/pvb_dpcbd_lib.php';

/* Satu pintu, dipakai dua sumber di bawah - rencana yang sudah ada maupun
   dokumen yang belum dijadwalkan masuk lewat jalur yang sama. */
$cfpPasangPv = function ($tgl, $akun, $idKtg, $nilai) use (&$cfpAkun, &$cfpKtg, &$cfpTakAkun,
                                                           &$cfpPvTanpaKtg, $cfpIdLain, $cfpTaruh) {
    $k = cfpKunci($akun);
    if ($k === '' || !isset($cfpAkun[$k])) { $cfpTakAkun[] = 'PV ' . $akun; $k = '(none)'; }
    $id = (int) $idKtg;
    if (!isset($cfpKtg[$id])) { $id = $cfpIdLain; $cfpPvTanpaKtg++; }
    $cfpTaruh($tgl, $k, $id, -(float) $nilai);
};

$cfpQ = mysqli_query($conn2, "show tables like 'ap_pvbiaya_plan_det'");
if ($cfpQ && mysqli_num_rows($cfpQ)) {
    $cfpSrc = pvb_plan_sumber($conn2);
    $cfpQ = mysqli_query($conn2, "
        select d.plan_date tgl, d.curr, d.amount,
               {$cfpSrc['akun']} frm_akun, {$cfpSrc['ktg']} id_cash_flow
          from ap_pvbiaya_plan_det d
          inner join ap_pvbiaya_plan_h h on h.doc_number = d.doc_number and h.status <> 'CANCEL'
          {$cfpSrc['join']}
         where d.status = 'Y' and d.plan_date between '$cfpMulai' and '$cfpAkhir'");
    while ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) {
        $cfpPasangPv($cfpR['tgl'], $cfpR['frm_akun'], $cfpR['id_cash_flow'], $cfpR['amount']);
    }
} else {
    $cfpPesan[] = 'Table ap_pvbiaya_plan_det does not exist yet, so planned expense payments are zero.';
}

/* 5c-2. YANG BELUM DIJADWALKAN SAMA SEKALI (permintaan pemakai 20 Nov 2026:
   "yang belum diupdate pun bisa ditarik ... harus di-set dulu ke Kamis
   terdekat seperti yang PV AP"). Sebelum ini laporan HANYA memuat dokumen
   yang sudah diberi tanggal di menu Update Payment Date - dan di produksi
   tidak satu pun PV biaya outstanding punya tanggal itu, jadi seluruh baris
   pengeluarannya nol tanpa ada yang salah di laporannya sendiri.

   Tanggalnya Kamis di minggu jatuh tempo, rumus yang sama persis dgn PV AP.
   Rencana yang sudah disetel orang TETAP MENANG - nomor yang sudah punya
   rencana dikecualikan di dalam pvb_tanpa_rencana(), jadi tidak ada yang
   terhitung dua kali. Dokumen yang sudah lewat tempo jatuh di Kamis yang
   sudah lewat dan karena itu tidak muncul di periode depan - sama dgn AP,
   dan memang begitu seharusnya: menaruhnya di hari pertama periode sama
   saja dgn mengarang tanggal bayar yang tidak pernah diputuskan siapa pun. */
foreach (pvb_tanpa_rencana($conn2, $cfpMulai, $cfpAkhir) as $cfpR) {
    $cfpPasangPv($cfpR['tgl'], $cfpR['frm_akun'], $cfpR['id_cash_flow'], $cfpR['amount']);
}

/* 5d. CASH PROJECTION OTHER - Book Transfer, Other Inflow, PV Expense.
   Kategorinya tersimpan sebagai NAMA di ap_cf_projection.kategori.

   HANYA ENTRI YANG JENISNYA SAMA DGN TYPE LAPORAN INI (permintaan pemakai
   20 Nov 2026) - lihat cfp_saring_type_report(). Untuk Daily hasilnya
   kosong, karena inputan Daily-nya memang belum ada. */
require_once __DIR__ . '/cash_projection/cfp_lib.php';
$cfpSaringOther = cfp_saring_type_report($conn2, $cfpType);
$cfpQ = mysqli_query($conn2, "select jenis, tanggal, akun_source, akun_tujuan, kategori,
                                     amount, amount_tujuan
                                from ap_cf_projection
                               where status <> 'Cancel'
                                 and tanggal between '$cfpMulai' and '$cfpAkhir'
                                     $cfpSaringOther");
while ($cfpQ && ($cfpR = mysqli_fetch_assoc($cfpQ))) {
    $jenis = strtolower(trim($cfpR['jenis']));
    $id = isset($cfpKtgNama[strtoupper(trim($cfpR['kategori']))])
        ? $cfpKtgNama[strtoupper(trim($cfpR['kategori']))] : 0;
    if ($jenis === 'book transfer') {
        $ks = cfpKunci($cfpR['akun_source']);
        $kt = cfpKunci($cfpR['akun_tujuan']);
        if (isset($cfpAkun[$ks])) { $cfpTaruh($cfpR['tanggal'], $ks, $cfpIdAntarK, -abs((float) $cfpR['amount'])); }
        $masuk = ($cfpR['amount_tujuan'] !== null && (float) $cfpR['amount_tujuan'] != 0)
               ? abs((float) $cfpR['amount_tujuan']) : abs((float) $cfpR['amount']);
        if (isset($cfpAkun[$kt])) { $cfpTaruh($cfpR['tanggal'], $kt, $cfpIdAntarM, $masuk); }
        continue;
    }
    $k = cfpKunci(trim($cfpR['akun_tujuan']) !== '' ? $cfpR['akun_tujuan'] : $cfpR['akun_source']);
    if ($k === '' || !isset($cfpAkun[$k])) { $cfpTakAkun[] = 'CF ' . $cfpR['akun_tujuan']; $k = '(none)'; }
    if ($id === 0) { $id = ($jenis === 'other inflow') ? cfpId($cfpKtgNama, 'Penerimaan Lain-lain', 7) : $cfpIdLain; }
    $tanda = (isset($cfpKtg[$id]) && $cfpKtg[$id]['tipe'] === 'Cash In') ? 1 : -1;
    $cfpTaruh($cfpR['tanggal'], $k, $id, $tanda * abs((float) $cfpR['amount']));
}

/* ---------------------------------------------------------------------------
   5e. DATA SUMBER untuk lembar pendukung di Excel - HANYA kalau diminta.

   DUA PEMINTA: Save History (define CFP_SUMBER) dan LAYAR, sejak 20 Nov 2026
   (permintaan pemakai: "biar user juga tahu yang di-save history itu datanya
   apa saja"). Layar memintanya lewat POST sumber=1 - tidak otomatis, karena
   barisnya bisa ratusan dan tidak semua pemanggil memerlukannya; modal saldo
   awal, misalnya, ikut menjalankan mesin ini juga.

   Dikumpulkan DI SINI, di tengah perhitungan, supaya memakai dokumen AR,
   baris AP, dan saldo yang SAMA PERSIS dgn yang dipakai angka laporannya -
   bukan hasil query ulang yang bisa sudah berubah.

   Save History membaca $cfpSumber langsung dari peubah ini (berkas itu
   meng-include mesin ini ke lingkup yang sama); layar membacanya dari JSON.
   Dua-duanya isi yang sama - lihat cf_sumber_lib.php.
   --------------------------------------------------------------------------- */
$cfpSumber = array();
$cfpSumberDef = array();
$cfpMintaSumber = defined('CFP_SUMBER')
               || (isset($_POST['sumber']) && (string) $_POST['sumber'] === '1');
if ($cfpMintaSumber) {
    include_once __DIR__ . '/cf_sumber_lib.php';
    $cfpSumber = cf_kumpul_sumber($conn2, array(
        'mulai'    => $cfpMulai,
        'akhir'    => $cfpAkhir,
        'rate'     => $cfpRate,
        'akun'     => $cfpAkun,
        'saldo'    => $cfpSaldo,
        'setel'    => $cfpSetel,
        'ktg'      => $cfpKtg,
        'ap_baris' => $cfpApBaris,
        'ap_peta'  => isset($cfpPetaKbon) ? $cfpPetaKbon : array(),
        'ar_doc'   => $cfpArDoc,
        'ar_baris' => $cfpArBaris,
        'ap_doc'   => $cfpApDoc,
        /* Type-nya ikut: lembar sumber menyaring entri Projection Other
           dgn aturan yang sama persis dgn mesin ini. */
        'type'     => $cfpType,
    ));

    /* Susunan kolomnya ikut dikirim supaya layar tidak perlu menyalin
       daftarnya - satu-satunya daftar tetap di cf_sumber_lembar(), jadi
       layar, Excel potret, dan Excel laporan mustahil berbeda kolomnya.
       cf_sumber_kolom() dipanggil di sini karena lembar PV AP kolomnya baru
       ketahuan sesudah barisnya ada (satu kolom per tanggal proyeksi). */
    foreach (cf_sumber_lembar() as $cfpKode => $cfpDef) {
        $cfpBarisLbr = isset($cfpSumber[$cfpKode]) ? $cfpSumber[$cfpKode] : array();
        $cfpSumberDef[$cfpKode] = array(
            'judul'   => $cfpDef['judul'],
            'catatan' => $cfpDef['catatan'],
            'kolom'   => cf_sumber_kolom($cfpDef, $cfpBarisLbr),
            'jml'     => count($cfpBarisLbr),
        );
    }
}

/* ---------------------------------------------------------------------------
   6. SIMULASI PINJAMAN - per rekening berstatus Loan, per hari.
   Penambahan = seluruh uang keluar hari itu di rekening tsb.
   Pelunasan  = uang masuk hari itu, dibatasi sebesar pinjaman berjalan.
   Rumusnya diambil apa adanya dari Excel (baris 109 & 111).
   --------------------------------------------------------------------------- */
$cfpPinjam = array();
foreach ($cfpAkun as $cfpK => $cfpA) {
    if (!$cfpA['loan'] || !cfpIkut($cfpA, $cfpPc, $cfpKunciBank)) { continue; }
    /* SALDO SISTEM, BUKAN angka manual (ditegaskan pemakai 20 Nov 2026:
       "opening balance ngga ngaruh ke situ, itu hanya ngaruh untuk
       perhitungan yang atas saja"). Menu Opening Balance menyetel saldo awal
       KAS & BANK; blok pinjaman punya arti sendiri - besarnya pinjaman yang
       masih berjalan - dan itu tidak boleh ikut berubah hanya karena ada
       yang menekan Save di modal saldo awal. Kejadiannya nyata: kotak
       rekening pinjaman terisi bawaan 0, lalu satu kali Save membuat SALDO
       AWAL PINJAMAN BANK jatuh dari -49,6 M jadi 0. */
    $jalan = $cfpSaldo[$cfpK]['tarik'];
    $cfpPinjam[$cfpK] = array();
    foreach ($cfpTanggal as $t) {
        $keluar = 0; $masuk = 0;
        if (isset($cfpGerak[$t][$cfpK])) {
            foreach ($cfpGerak[$t][$cfpK] as $v) { if ($v < 0) { $keluar += $v; } else { $masuk += $v; } }
        }
        $saldo = $jalan + $keluar;
        if ($saldo > 0)           { $lunas = 0; }
        elseif ($masuk > -$saldo) { $lunas = -$saldo; }
        else                      { $lunas = $masuk; }
        $cfpPinjam[$cfpK][$t] = array('awal' => $jalan, 'tambah' => $keluar,
                                      'lunas' => $lunas, 'akhir' => $saldo + $lunas);
        $jalan = $saldo + $lunas;
    }
}

/* ---------------------------------------------------------------------------
   7. RANGKUMAN per kategori per tanggal (sudah rupiah & tersaring).
   --------------------------------------------------------------------------- */
$cfpNilai = array();
foreach ($cfpTanggal as $t) {
    foreach ($cfpAkun as $cfpK => $cfpA) {
        if (!cfpIkut($cfpA, $cfpPc, $cfpKunciBank)) { continue; }
        if (!isset($cfpGerak[$t][$cfpK])) { continue; }
        foreach ($cfpGerak[$t][$cfpK] as $id => $v) {
            if (!isset($cfpNilai[$id][$t])) { $cfpNilai[$id][$t] = 0; }
            $cfpNilai[$id][$t] += cfpIdr($cfpA, $v, $cfpRate);
        }
    }
}
/* Penerimaan / Pelunasan Pinjaman Bank diambil dari simulasi, bukan data. */
foreach ($cfpTanggal as $t) {
    $terima = 0; $lunas = 0;
    foreach ($cfpPinjam as $cfpK => $hari) {
        $terima += cfpIdr($cfpAkun[$cfpK], -$hari[$t]['tambah'], $cfpRate);
        $lunas  += cfpIdr($cfpAkun[$cfpK], -$hari[$t]['lunas'], $cfpRate);
    }
    $cfpNilai[$cfpIdTerima][$t] = $terima;
    $cfpNilai[$cfpIdLunas][$t]  = $lunas;
}

/* ---------------------------------------------------------------------------
   SALDO AWAL KAS & BANK per rekening.

   Rekening pinjaman: saldo SISTEM-nya tidak pernah ikut - angka itu besarnya
   pinjaman yang masih berjalan, bukan uang yang bisa dipakai. TAPI angka yang
   DIKETIK TANGAN ikut (permintaan pemakai 19 Nov 2026: &quot;kalau user isi,
   totalnya nambah&quot;): kalau seseorang sengaja menuliskan saldo awal rekening
   itu, yang ditulisnya memang saldo rekening - dan kotak yang nilainya diketik
   lalu tidak mengubah apa pun jauh lebih membingungkan daripada aturan ini.
   --------------------------------------------------------------------------- */
function cfpAwalKas($a, $saldo, $rate)
{
    if ($a['loan'] && $saldo['manual'] === null) { return 0; }
    return cfpIdr($a, $saldo['pakai'], $rate);
}

$cfpKasAwal = 0;
foreach ($cfpAkun as $cfpK => $cfpA) {
    if (!cfpIkut($cfpA, $cfpPc, $cfpKunciBank)) { continue; }
    $cfpKasAwal += cfpAwalKas($cfpA, $cfpSaldo[$cfpK], $cfpRate);
}

/* ---------------------------------------------------------------------------
   8. SUSUN BARIS LAPORAN.
   --------------------------------------------------------------------------- */
$cfpN = count($cfpTanggal);
$cfpSub = array();
$cfpMasuk = array_fill(0, $cfpN, 0);
$cfpKeluar = array_fill(0, $cfpN, 0);
foreach ($cfpKtg as $id => $c) {
    $kunciSub = $c['tipe'] . '|' . $c['grup'];
    if (!isset($cfpSub[$kunciSub])) { $cfpSub[$kunciSub] = array_fill(0, $cfpN, 0); }
    foreach ($cfpTanggal as $i => $t) {
        $v = isset($cfpNilai[$id][$t]) ? $cfpNilai[$id][$t] : 0;
        $cfpSub[$kunciSub][$i] += $v;
        if ($c['tipe'] === 'Cash In') { $cfpMasuk[$i] += $v; } else { $cfpKeluar[$i] += $v; }
    }
}

$cfpBuka = array(); $cfpTutup = array();
$jalan = $cfpKasAwal;
foreach ($cfpTanggal as $i => $t) {
    $cfpBuka[$i]  = $jalan;
    $cfpTutup[$i] = $jalan + $cfpMasuk[$i] + $cfpKeluar[$i];
    $jalan = $cfpTutup[$i];
}

$cfpBarisLap = array();
$cfpBarisLap[] = cfpBaris('sal', '', 'OPENING CASH & BANK BALANCE', $cfpBuka, $cfpBuka[0]);

$cfpGrup = array('OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3');
foreach (array('Cash In' => 'INFLOWS', 'Cash Out' => 'OUTFLOWS') as $cfpTipe => $cfpKata) {
    $cfpBarisLap[] = cfpBaris('blok', '', 'CASH ' . $cfpKata . ':', array_fill(0, $cfpN, null), null);
    foreach ($cfpGrup as $cfpNamaGrup => $cfpNoGrup) {
        $cfpBarisLap[] = cfpBaris('grup', $cfpNoGrup, $cfpNamaGrup . ':', array_fill(0, $cfpN, null), null);
        foreach ($cfpKtg as $id => $c) {
            if ($c['tipe'] !== $cfpTipe || $c['grup'] !== $cfpNamaGrup) { continue; }
            $v = array(); $tot = 0;
            foreach ($cfpTanggal as $t) {
                $x = isset($cfpNilai[$id][$t]) ? $cfpNilai[$id][$t] : 0;
                $v[] = $x; $tot += $x;
            }
            $cfpBarisLap[] = cfpBaris('item', $c['no'], $c['nama'], $v, $tot);
        }
        $s = isset($cfpSub[$cfpTipe . '|' . $cfpNamaGrup]) ? $cfpSub[$cfpTipe . '|' . $cfpNamaGrup] : array_fill(0, $cfpN, 0);
        $cfpBarisLap[] = cfpBaris('tot', '', 'TOTAL CASH ' . $cfpKata . ' FROM ' . $cfpNamaGrup, $s, array_sum($s));
    }
    $semua = ($cfpTipe === 'Cash In') ? $cfpMasuk : $cfpKeluar;
    $cfpBarisLap[] = cfpBaris('tot2', '', 'TOTAL CASH ' . $cfpKata, $semua, array_sum($semua));
}

$cfpBarisLap[] = cfpBaris('blok', '', 'NET CASH FLOW BY ACTIVITY', array_fill(0, $cfpN, null), null);
foreach ($cfpGrup as $cfpNamaGrup => $cfpNoGrup) {
    $v = array();
    foreach ($cfpTanggal as $i => $t) {
        $a = isset($cfpSub['Cash In|' . $cfpNamaGrup][$i])  ? $cfpSub['Cash In|' . $cfpNamaGrup][$i]  : 0;
        $b = isset($cfpSub['Cash Out|' . $cfpNamaGrup][$i]) ? $cfpSub['Cash Out|' . $cfpNamaGrup][$i] : 0;
        $v[] = $a + $b;
    }
    $cfpBarisLap[] = cfpBaris('net', '', $cfpNamaGrup, $v, array_sum($v));
}
$cfpBarisLap[] = cfpBaris('sal', '', 'CLOSING CASH & BANK BALANCE', $cfpTutup,
                          $cfpBuka[0] + array_sum($cfpMasuk) + array_sum($cfpKeluar));

/* ---------------------------------------------------------------------------
   9. BLOK PINJAMAN.
   --------------------------------------------------------------------------- */
$cfpBarisPinj = array();
$cfpAmbil = function ($bagian, $mata, $jadiIdr) use ($cfpPinjam, $cfpAkun, $cfpTanggal, $cfpRate) {
    $out = array();
    foreach ($cfpTanggal as $t) {
        $j = 0;
        foreach ($cfpPinjam as $k => $h) {
            if ($cfpAkun[$k]['curr'] !== $mata) { continue; }
            $j += $jadiIdr ? cfpIdr($cfpAkun[$k], $h[$t][$bagian], $cfpRate) : $h[$t][$bagian];
        }
        $out[] = $j;
    }
    return $out;
};
foreach (array('IDR', 'USD') as $cfpMata) {
    $cfpLimit = 0; $cfpAda = false;
    foreach ($cfpPinjam as $cfpK => $h) {
        if ($cfpAkun[$cfpK]['curr'] !== $cfpMata) { continue; }
        $cfpAda = true; $cfpLimit += $cfpAkun[$cfpK]['limit'];
    }
    if (!$cfpAda) { continue; }

    if ($cfpMata === 'IDR') {
        $awal = $cfpAmbil('awal', 'IDR', true);
        $tmb  = $cfpAmbil('tambah', 'IDR', true);
        $lns  = $cfpAmbil('lunas', 'IDR', true);
        $akh  = $cfpAmbil('akhir', 'IDR', true);
        $sisa = array();
        foreach ($akh as $v) { $sisa[] = $v + $cfpLimit; }
        $akhirTot = $awal[0] + array_sum($tmb) + array_sum($lns);
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SALDO AWAL PINJAMAN BANK IDR', $awal, $awal[0]);
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'PENAMBAHAN PINJAMAN IDR', $tmb, array_sum($tmb));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'PELUNASAN PINJAMAN IDR', $lns, array_sum($lns));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SALDO AKHIR PINJAMAN BANK IDR', $akh, $akhirTot);
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'LIMIT', array_fill(0, $cfpN, $cfpLimit), $cfpLimit);
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SISA / (KELEBIHAN) LIMIT', $sisa, $akhirTot + $cfpLimit);
        continue;
    }

    /* USD: tiap bagian dua baris - mata uang asal & rupiahnya. */
    $aU = $cfpAmbil('awal', 'USD', false);   $aI = $cfpAmbil('awal', 'USD', true);
    $tU = $cfpAmbil('tambah', 'USD', false); $tI = $cfpAmbil('tambah', 'USD', true);
    $lU = $cfpAmbil('lunas', 'USD', false);  $lI = $cfpAmbil('lunas', 'USD', true);
    $kU = $cfpAmbil('akhir', 'USD', false);  $kI = $cfpAmbil('akhir', 'USD', true);
    $pasang = function ($judul, $u, $i, $totU, $totI) use (&$cfpBarisPinj, $cfpN) {
        $cfpBarisPinj[] = cfpBaris('pinj', '', $judul, array_fill(0, $cfpN, null), null);
        $cfpBarisPinj[] = cfpBaris('sub', '', '- Dalam Mata Uang USD', $u, $totU);
        $cfpBarisPinj[] = cfpBaris('sub', '', '- Dalam Mata Uang Rupiah', $i, $totI);
    };
    $pasang('SALDO AWAL PINJAMAN BANK USD', $aU, $aI, $aU[0], $aI[0]);
    $pasang('PENAMBAHAN PINJAMAN USD', $tU, $tI, array_sum($tU), array_sum($tI));
    $pasang('PELUNASAN PINJAMAN USD', $lU, $lI, array_sum($lU), array_sum($lI));
    $pasang('SALDO AKHIR PINJAMAN BANK USD', $kU, $kI,
            $aU[0] + array_sum($tU) + array_sum($lU),
            $aI[0] + array_sum($tI) + array_sum($lI));
    $pasang('LIMIT', array_fill(0, $cfpN, $cfpLimit), array_fill(0, $cfpN, $cfpLimit * $cfpRate),
            $cfpLimit, $cfpLimit * $cfpRate);
    $sU = array(); $sI = array();
    foreach ($kU as $i => $v) { $sU[] = $v + $cfpLimit; $sI[] = $kI[$i] + $cfpLimit * $cfpRate; }
    $pasang('SISA / (KELEBIHAN) LIMIT', $sU, $sI,
            $aU[0] + array_sum($tU) + array_sum($lU) + $cfpLimit,
            $aI[0] + array_sum($tI) + array_sum($lI) + $cfpLimit * $cfpRate);
}

/* ---------------------------------------------------------------------------
   9b. TATA LETAK WEEKLY / MONTHLY.

   Di lembar acuan (D:\File Project\Cash Flow Projection Report.xlsx, lembar
   "Cash Flow Weekly and Monthly") KOLOMNYA REKENING, bukan tanggal: satu
   kolom per rekening bank/kas plus kolom TOTAL, barisnya 56 kategori yang
   sama. Weekly dan Monthly memakai SATU lembar yang sama - yang membedakan
   cuma rentang tanggal yang dipilih pemakai, bukan bentuk tabelnya.

   Bedanya dgn Daily, selain kolom:
     - judulnya "OPENING/CLOSING CASH BALANCE" (tanpa "& BANK")
     - blok pinjaman cuma 6 baris, TANPA pecahan "Dalam Mata Uang USD/Rupiah":
       tiap rekening sudah punya kolomnya sendiri, jadi tidak ada yang perlu
       dipisah per mata uang.

   REKENING PINJAMAN IKUT JADI KOLOM. Saldo kas awal/akhirnya nol - pinjaman
   bukan kas - tetapi MUTASINYA tetap tampil di baris kategori, persis
   seperti di lembar acuan (mis. penerimaan piutang yang masuk ke rekening
   pinjaman). Aturan ini sama dgn Daily, yang berbeda cuma cara
   mengelompokkannya.

   Barisnya DIBANGUN ULANG di sini, bukan menggantikan hitungan Daily di
   atas: sumbernya sama ($cfpGerak), jadi dua tata letak itu mustahil
   berbeda isi.
   --------------------------------------------------------------------------- */
/* $cfpModeAkun TIDAK LAGI memilih tata letak - dua-duanya selalu dibangun
   (lihat di bawah). Yang ditentukannya cuma TAB MANA YANG TERBUKA DULUAN. */
$cfpModeAkun = ($cfpType === 'weekly' || $cfpType === 'monthly');
$cfpKolom = array();

/* Tata letak per TANGGAL yang baru selesai disusun disimpan dulu: blok di
   bawah memakai $cfpBarisLap & $cfpBarisPinj yang sama dan akan menimpanya. */
$cfpBarisTgl = $cfpBarisLap;
$cfpPinjTgl  = $cfpBarisPinj;

/* DUA TATA LETAK SELALU DIBANGUN DUA-DUANYA (permintaan pemakai 20 Nov 2026:
   &quot;di setiap type harusnya ada 2 tab report, by date dan by bank&quot;).
   Dulu blok ini hanya jalan untuk Weekly/Monthly, jadi susunan per rekening
   tidak bisa dilihat sama sekali di Daily - dan susunan per tanggal tidak
   bisa dilihat, apalagi disimpan, di Weekly/Monthly. Kurung di bawah ini
   cuma pengelompok supaya isinya tetap satu kesatuan yang gampang dibaca;
   PHP tidak punya lingkup peubah di dalamnya. */
{
    foreach ($cfpAkun as $cfpK => $cfpA) {
        if (!cfpIkut($cfpA, $cfpPc, $cfpKunciBank)) { continue; }
        $cfpKolom[] = array(
            'kunci' => (string) $cfpA['kunci'],
            'label' => $cfpA['label'],
            'curr'  => $cfpA['curr'],
            'loan'  => $cfpA['loan'] ? 1 : 0,
        );
    }

    $cfpNk = count($cfpKolom);
    if ($cfpNk === 0) {
        if (cfpKeluar('No account matches the current filter.')) { return; }
    }

    /* nilai tiap kategori per kolom, sudah dirupiahkan */
    $cfpNilaiK = array();
    $cfpKosong = array_fill(0, $cfpNk, 0);
    foreach ($cfpKolom as $cfpI => $cfpKol) {
        $cfpKk = $cfpKol['kunci'];
        foreach ($cfpTanggal as $cfpT) {
            if (!isset($cfpGerak[$cfpT][$cfpKk])) { continue; }
            foreach ($cfpGerak[$cfpT][$cfpKk] as $cfpIdK => $cfpV) {
                if (!isset($cfpNilaiK[$cfpIdK])) { $cfpNilaiK[$cfpIdK] = $cfpKosong; }
                $cfpNilaiK[$cfpIdK][$cfpI] += cfpIdr($cfpAkun[$cfpKk], $cfpV, $cfpRate);
            }
        }
    }

    /* Penerimaan / Pelunasan Pinjaman Bank dari SIMULASI, bukan data -
       aturannya sama persis dgn Daily. */
    foreach (array($cfpIdTerima, $cfpIdLunas) as $cfpIdP) {
        if (!isset($cfpNilaiK[$cfpIdP])) { $cfpNilaiK[$cfpIdP] = $cfpKosong; }
    }
    foreach ($cfpKolom as $cfpI => $cfpKol) {
        $cfpKk = $cfpKol['kunci'];
        if (!isset($cfpPinjam[$cfpKk])) { continue; }
        $cfpTerima = 0; $cfpLunas = 0;
        foreach ($cfpTanggal as $cfpT) {
            $cfpTerima += cfpIdr($cfpAkun[$cfpKk], -$cfpPinjam[$cfpKk][$cfpT]['tambah'], $cfpRate);
            $cfpLunas  += cfpIdr($cfpAkun[$cfpKk], -$cfpPinjam[$cfpKk][$cfpT]['lunas'], $cfpRate);
        }
        $cfpNilaiK[$cfpIdTerima][$cfpI] = $cfpTerima;
        $cfpNilaiK[$cfpIdLunas][$cfpI]  = $cfpLunas;
    }

    /* saldo kas awal per kolom - rekening pinjaman nol, pinjaman bukan kas */
    $cfpBukaK = array(); $cfpTutupK = array();
    $cfpMasukK = $cfpKosong; $cfpKeluarK = $cfpKosong;
    $cfpSubK = array();
    foreach ($cfpKtg as $cfpIdK => $cfpC) {
        $cfpKunciSub = $cfpC['tipe'] . '|' . $cfpC['grup'];
        if (!isset($cfpSubK[$cfpKunciSub])) { $cfpSubK[$cfpKunciSub] = $cfpKosong; }
        $cfpBarisNilai = isset($cfpNilaiK[$cfpIdK]) ? $cfpNilaiK[$cfpIdK] : $cfpKosong;
        foreach ($cfpBarisNilai as $cfpI => $cfpV) {
            $cfpSubK[$cfpKunciSub][$cfpI] += $cfpV;
            if ($cfpC['tipe'] === 'Cash In') { $cfpMasukK[$cfpI] += $cfpV; }
            else                             { $cfpKeluarK[$cfpI] += $cfpV; }
        }
    }
    foreach ($cfpKolom as $cfpI => $cfpKol) {
        $cfpKk = $cfpKol['kunci'];
        $cfpAwalK = !isset($cfpSaldo[$cfpKk]) ? 0
                  : cfpAwalKas($cfpAkun[$cfpKk], $cfpSaldo[$cfpKk], $cfpRate);
        $cfpBukaK[$cfpI]  = $cfpAwalK;
        $cfpTutupK[$cfpI] = $cfpAwalK + $cfpMasukK[$cfpI] + $cfpKeluarK[$cfpI];
    }

    /* ---- baris laporan ---- */
    $cfpBarisLap = array();
    $cfpBarisLap[] = cfpBaris('sal', '', 'OPENING CASH BALANCE', $cfpBukaK, array_sum($cfpBukaK));

    foreach (array('Cash In' => 'INFLOWS', 'Cash Out' => 'OUTFLOWS') as $cfpTipe => $cfpKata) {
        $cfpBarisLap[] = cfpBaris('blok', '', 'CASH ' . $cfpKata . ':', array_fill(0, $cfpNk, null), null);
        foreach ($cfpGrup as $cfpNamaGrup => $cfpNoGrup) {
            $cfpBarisLap[] = cfpBaris('grup', $cfpNoGrup, $cfpNamaGrup . ':', array_fill(0, $cfpNk, null), null);
            foreach ($cfpKtg as $cfpIdK => $cfpC) {
                if ($cfpC['tipe'] !== $cfpTipe || $cfpC['grup'] !== $cfpNamaGrup) { continue; }
                $cfpV = isset($cfpNilaiK[$cfpIdK]) ? $cfpNilaiK[$cfpIdK] : $cfpKosong;
                $cfpBarisLap[] = cfpBaris('item', $cfpC['no'], $cfpC['nama'], $cfpV, array_sum($cfpV));
            }
            $cfpS = isset($cfpSubK[$cfpTipe . '|' . $cfpNamaGrup]) ? $cfpSubK[$cfpTipe . '|' . $cfpNamaGrup] : $cfpKosong;
            $cfpBarisLap[] = cfpBaris('tot', '', 'TOTAL CASH ' . $cfpKata . ' FROM ' . $cfpNamaGrup, $cfpS, array_sum($cfpS));
        }
        $cfpSemua = ($cfpTipe === 'Cash In') ? $cfpMasukK : $cfpKeluarK;
        $cfpBarisLap[] = cfpBaris('tot2', '', 'TOTAL CASH ' . $cfpKata, $cfpSemua, array_sum($cfpSemua));
    }

    $cfpBarisLap[] = cfpBaris('blok', '', 'NET CASH FLOW BY ACTIVITY', array_fill(0, $cfpNk, null), null);
    foreach ($cfpGrup as $cfpNamaGrup => $cfpNoGrup) {
        $cfpV = array();
        for ($cfpI = 0; $cfpI < $cfpNk; $cfpI++) {
            $cfpA1 = isset($cfpSubK['Cash In|' . $cfpNamaGrup][$cfpI])  ? $cfpSubK['Cash In|' . $cfpNamaGrup][$cfpI]  : 0;
            $cfpA2 = isset($cfpSubK['Cash Out|' . $cfpNamaGrup][$cfpI]) ? $cfpSubK['Cash Out|' . $cfpNamaGrup][$cfpI] : 0;
            $cfpV[] = $cfpA1 + $cfpA2;
        }
        $cfpBarisLap[] = cfpBaris('net', '', $cfpNamaGrup, $cfpV, array_sum($cfpV));
    }
    $cfpBarisLap[] = cfpBaris('sal', '', 'CLOSING CASH BALANCE', $cfpTutupK, array_sum($cfpTutupK));

    /* Kolom (unassigned account) hanya berguna kalau benar-benar ada isinya;
       kalau nol, yang tampil cuma kolom kosong yang membingungkan. Dibuang
       SESUDAH baris disusun supaya indeksnya tidak bergeser di tengah jalan. */
    foreach ($cfpKolom as $cfpI => $cfpKol) {
        if ($cfpKol['kunci'] !== '(none)') { continue; }
        $cfpAdaIsi = false;
        foreach ($cfpBarisLap as $cfpB) {
            if (isset($cfpB['v'][$cfpI]) && round((float) $cfpB['v'][$cfpI]) != 0) { $cfpAdaIsi = true; break; }
        }
        if ($cfpAdaIsi) { break; }
        foreach ($cfpBarisLap as $cfpJ => $cfpB) {
            if (is_array($cfpB['v'])) { array_splice($cfpBarisLap[$cfpJ]['v'], $cfpI, 1); }
        }
        array_splice($cfpKolom, $cfpI, 1);
        $cfpNk--;
        $cfpKosong = array_fill(0, $cfpNk, 0);
        break;
    }

    /* ---- blok pinjaman: 6 baris, satu kolom per rekening ---- */
    $cfpBarisPinj = array();
    $cfpAdaPinjaman = false;
    $cfpPawal = $cfpKosong; $cfpPtambah = $cfpKosong; $cfpPlunas = $cfpKosong;
    $cfpPakhir = $cfpKosong; $cfpPlimit = $cfpKosong; $cfpPsisa = $cfpKosong;
    foreach ($cfpKolom as $cfpI => $cfpKol) {
        $cfpKk = $cfpKol['kunci'];
        if (!isset($cfpPinjam[$cfpKk])) { continue; }
        $cfpAdaPinjaman = true;
        $cfpPertama = $cfpTanggal[0];
        $cfpPawal[$cfpI] = cfpIdr($cfpAkun[$cfpKk], $cfpPinjam[$cfpKk][$cfpPertama]['awal'], $cfpRate);
        foreach ($cfpTanggal as $cfpT) {
            $cfpPtambah[$cfpI] += cfpIdr($cfpAkun[$cfpKk], $cfpPinjam[$cfpKk][$cfpT]['tambah'], $cfpRate);
            $cfpPlunas[$cfpI]  += cfpIdr($cfpAkun[$cfpKk], $cfpPinjam[$cfpKk][$cfpT]['lunas'], $cfpRate);
        }
        $cfpPakhir[$cfpI] = $cfpPawal[$cfpI] + $cfpPtambah[$cfpI] + $cfpPlunas[$cfpI];
        $cfpPlimit[$cfpI] = cfpIdr($cfpAkun[$cfpKk], $cfpAkun[$cfpKk]['limit'], $cfpRate);
        $cfpPsisa[$cfpI]  = $cfpPakhir[$cfpI] + $cfpPlimit[$cfpI];
    }
    if ($cfpAdaPinjaman) {
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SALDO AWAL PINJAMAN BANK',  $cfpPawal,  array_sum($cfpPawal));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'PENAMBAHAN PINJAMAN',       $cfpPtambah, array_sum($cfpPtambah));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'PELUNASAN PINJAMAN',        $cfpPlunas, array_sum($cfpPlunas));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SALDO AKHIR PINJAMAN BANK', $cfpPakhir, array_sum($cfpPakhir));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'LIMIT',                     $cfpPlimit, array_sum($cfpPlimit));
        $cfpBarisPinj[] = cfpBaris('pinj', '', 'SISA / (KELEBIHAN) LIMIT',  $cfpPsisa,  array_sum($cfpPsisa));
    }
}

$cfpBarisAkun = $cfpBarisLap;
$cfpPinjAkun  = $cfpBarisPinj;

/* 'baris' & 'pinjaman' tetap memuat tata letak BAWAAN type-nya - itu yang
   dibaca pemanggil lama (ekspor, potret) sebelum semuanya ikut dua-duanya. */
if (!$cfpModeAkun) { $cfpBarisLap = $cfpBarisTgl; $cfpBarisPinj = $cfpPinjTgl; }

/* ---------------------------------------------------------------------------
   10. KELUARAN
   --------------------------------------------------------------------------- */
if ($cfpPvTanpaKtg > 0) {
    $cfpPesan[] = $cfpPvTanpaKtg . ' expense payment(s) have no cash flow category yet and are shown under Pengeluaran Lain-lain.';
}
/* Kas yang tidak terikat profit center hilang begitu saringan PC dipakai -
   disebutkan supaya hilangnya tidak diam-diam. */
if ($cfpKasTanpaPc && $cfpPc !== 'ALL' && $cfpPc !== '') {
    $cfpPesan[] = 'Cash account(s) without a profit center are not included while a profit center filter is on: '
                . implode(', ', $cfpKasTanpaPc) . '.';
}

$cfpTakAkun = array_values(array_unique($cfpTakAkun));
if ($cfpTakAkun) {
    $cfpPesan[] = count($cfpTakAkun) . ' item(s) could not be matched to an active account: '
                . implode(', ', array_slice($cfpTakAkun, 0, 6)) . (count($cfpTakAkun) > 6 ? ' ...' : '');
}

$cfpDaftarBank = array();
foreach ($cfpAkun as $cfpK => $cfpA) {
    if ($cfpK === '(none)') { continue; }
    if ($cfpPc !== 'ALL' && $cfpPc !== '' && strtoupper($cfpA['pc']) !== $cfpPc) { continue; }
    /* Saldo & limit ikut dikirim: dipakai Save History untuk MEMBEKUKAN
       setelan rekening apa adanya saat laporan itu disimpan. */
    $cfpDaftarBank[] = array(
        /* Dari $cfpA, BUKAN dari $cfpK: kunci array PHP yang isinya angka
           (mis. rekening 442-244-2000 -> 4422442000) otomatis berubah jadi
           INTEGER, lalu terkirim ke JSON sebagai angka. Pembandingnya di
           sisi lain memakai teks, jadi rekening bernomor murni tidak pernah
           cocok - sedangkan COA kas (1.01.01) cocok. Cacat yang cuma
           mengenai sebagian rekening seperti ini paling sulit dilihat. */
        'kunci'       => (string) $cfpA['kunci'],
        'label'       => $cfpA['label'],
        'curr'        => $cfpA['curr'],
        'pc'          => $cfpA['pc'],
        'loan'        => $cfpA['loan'] ? 1 : 0,
        'limit'       => $cfpA['limit'],
        'saldo_tarik' => isset($cfpSaldo[$cfpK]) ? $cfpSaldo[$cfpK]['tarik'] : 0,
        'saldo_pakai' => isset($cfpSaldo[$cfpK]) ? $cfpSaldo[$cfpK]['pakai'] : 0,
        'manual'      => isset($cfpSaldo[$cfpK]) ? $cfpSaldo[$cfpK]['manual'] : null,
        'manual_by'   => isset($cfpSaldo[$cfpK]) ? $cfpSaldo[$cfpK]['manual_by'] : '',
        'manual_date' => isset($cfpSaldo[$cfpK]) ? $cfpSaldo[$cfpK]['manual_date'] : '',
        'min_balance' => isset($cfpSetel[$cfpK]) ? (float) $cfpSetel[$cfpK]['min_balance'] : 0,
    );
}

echo json_encode(array(
    /* mode 'tanggal' = kolomnya tanggal (Daily); mode 'akun' = kolomnya
       rekening (Weekly/Monthly). Layar & ekspor memilih kepala tabel dari
       sini, bukan menebak dari type. */
    /* mode = tab mana yang TERBUKA DULUAN, bukan satu-satunya yang ada.
       baris/pinjaman = isi tab itu, dipertahankan untuk pemanggil lama;
       baris_tgl/baris_akun = dua-duanya, selalu terisi. */
    'mode'     => $cfpModeAkun ? 'akun' : 'tanggal',
    'kolom'    => $cfpKolom,
    'tgl'      => $cfpTanggal,
    'baris'    => $cfpBarisLap,
    'pinjaman' => $cfpBarisPinj,
    'baris_tgl'     => $cfpBarisTgl,
    'pinjaman_tgl'  => $cfpPinjTgl,
    'baris_akun'    => $cfpBarisAkun,
    'pinjaman_akun' => $cfpPinjAkun,
    /* Isinya kosong kalau tidak diminta - layar yang memutuskan. */
    'sumber'        => $cfpSumber,
    'sumber_def'    => $cfpSumberDef,
    'bank'     => $cfpDaftarBank,
    'info'     => array(
        'periode'    => date('j M Y', strtotime($cfpMulai)) . ' - ' . date('j M Y', strtotime($cfpAkhir)),
        'rate'       => $cfpRate,
        /* pc      = kodenya, untuk baris PROFIT CENTER
           pc_label = namanya, untuk judul. Judul tidak boleh memakai nama
           perusahaan: laporan ini melayani Garment maupun Knitting. */
        'pc'         => ($cfpPc === 'ALL' || $cfpPc === '') ? 'All PC' : $cfpPc,
        'pc_label'   => isset($cfpNamaPc[$cfpPc]) ? $cfpNamaPc[$cfpPc] : 'All PC',
        /* NAMA rekeningnya yang disimpan, bukan kuncinya - nilai ini ikut
           dibekukan ke potret (ap_cf_history_h.bank) dan dibaca manusia di
           kepala laporan & Excel. Beberapa rekening digabung dgn '; ', dan
           cfpDaftarKunciBank() tahu cara memecahnya kembali. */
        'bank'       => cfpLabelBank($cfpAkun, $cfpKunciBank, $cfpBank),
        'type'       => $cfpType,
        'ar_doc'     => $cfpArDoc,
        'ap_doc'     => $cfpApDoc,
        'ar_tanggal' => $cfpArTgl ? date('j M Y H:i', strtotime($cfpArTgl)) : '',
        'peringatan' => $cfpPesan,
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
