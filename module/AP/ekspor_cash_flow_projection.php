<?php
/* ============================================================================
   CASH FLOW PROJECTION REPORT - EKSPOR EXCEL.
   Dipanggil tombol Excel di cash_flow_projection.php dgn saringan yang SAMA
   PERSIS seperti yang sedang tampil di layar.

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI. Berkas ini menyetel $_POST lalu
   meng-include ajx_cash_flow_projection.php dalam mode pustaka (CFP_PUSTAKA)
   dan memakai keluarannya apa adanya - jadi isi Excel mustahil berbeda dari
   layar. Pola & alasannya sama dgn ekspor_ap_projection_report.php.

   DUA LEMBAR, SAMA DGN DUA TAB DI LAYAR (permintaan pemakai 20 Nov 2026):
     "Report by Date" - kolomnya tanggal
     "Report by Bank" - kolomnya rekening
   Sampai 20 Nov 2026 berkas ini bentuknya TABEL HTML berkepala vnd-ms-excel;
   bentuk itu cuma bisa satu lembar, jadi kedua susunan sempat dideretkan ke
   bawah dalam satu lembar. Sekarang memakai PhpSpreadsheet seperti ekspor
   potretnya, dan pembuat lembarnya pun pustaka yang SAMA
   (cf_xlsx_lib.php) - dua Excel itu karena itu mustahil berbeda tata
   letaknya.

   Susunan kolom tiap lembar:
     A,B,C = tiga tingkat keterangan (blok / kelompok+nomor / nama kategori)
     D     = TOTAL
     E..   = satu kolom per tanggal (atau per rekening)
   Blok pinjaman ditaruh di bawah dgn kepala tabel DIULANG - beda dgn layar:
   di Excel barisnya bisa tercetak di halaman lain, jadi kepalanya perlu.
   ============================================================================ */

/* Keluarannya BINER. Satu notice saja yang ikut tercetak membuat berkasnya
   tidak bisa dibuka sama sekali - pernah terjadi di ekspor potret. */
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

session_start();
require '../../vendor/autoload.php';
require_once __DIR__ . '/cf_xlsx_lib.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

define('CFP_PUSTAKA', 1);
/* Lembar sumbernya ikut dicetak (permintaan pemakai 20 Nov 2026), jadi
   mesinnya diminta mengumpulkan datanya. Mesin meng-include cf_sumber_lib
   sendiri waktu penanda ini ada, dan hasilnya tinggal dibaca dari
   $cfpSumber - berkas ini satu lingkup dgn mesinnya. */
define('CFP_SUMBER', 1);

$_POST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$_POST['end_date']   = isset($_REQUEST['end_date'])   ? $_REQUEST['end_date']   : '';
$_POST['pc']         = isset($_REQUEST['pc'])         ? $_REQUEST['pc']         : 'ALL';
$_POST['bank']       = isset($_REQUEST['bank'])       ? $_REQUEST['bank']       : 'ALL';
$_POST['type']       = isset($_REQUEST['type'])       ? $_REQUEST['type']       : 'daily';
$_POST['rate']       = isset($_REQUEST['rate'])       ? $_REQUEST['rate']       : '0';

/* Mesinnya dijalankan DULU, headernya menyusul: kalau gagal, yang dikirim
   pesan biasa - bukan berkas Excel rusak yang baru ketahuan waktu dibuka. */
ob_start();
include __DIR__ . '/ajx_cash_flow_projection.php';
$jawab = json_decode(trim((string) ob_get_clean()), true);

$salah = (!is_array($jawab) || isset($jawab['error']))
       ? ((is_array($jawab) && isset($jawab['error'])) ? $jawab['error'] : 'The report could not be built.')
       : '';
if ($salah !== '') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo $salah;
    exit;
}

$info = isset($jawab['info']) ? $jawab['info'] : array();
$type = ucfirst(strtolower(isset($info['type']) && $info['type'] !== '' ? $info['type'] : 'daily'));

/* ---- kolom kedua susunan ---------------------------------------------
   Lembar tanggal memakai kunci 'tgl' (ISO) supaya cfhxSelTgl menulisnya
   sbg TANGGAL betulan, bukan teks; lembar rekening memakai 'judul'. */
$kolomTgl = array();
foreach (isset($jawab['tgl']) ? $jawab['tgl'] : array() as $t) {
    $kolomTgl[] = array('tgl' => $t, 'judul' => $t);
}
$kolomAkun = array();
foreach (isset($jawab['kolom']) ? $jawab['kolom'] : array() as $k) {
    $kolomAkun[] = array('tgl' => '', 'judul' => isset($k['label']) ? $k['label'] : '');
}

$barisTgl  = isset($jawab['baris_tgl'])     ? $jawab['baris_tgl']     : array();
$pinjTgl   = isset($jawab['pinjaman_tgl'])  ? $jawab['pinjaman_tgl']  : array();
$barisAkun = isset($jawab['baris_akun'])    ? $jawab['baris_akun']    : array();
$pinjAkun  = isset($jawab['pinjaman_akun']) ? $jawab['pinjaman_akun'] : array();
/* jawaban lama cuma punya 'baris' */
if (!$barisTgl && !$barisAkun) {
    $barisTgl = isset($jawab['baris']) ? $jawab['baris'] : array();
    $pinjTgl  = isset($jawab['pinjaman']) ? $jawab['pinjaman'] : array();
}

/* Nama perusahaan sengaja TIDAK dicetak: berkas ini dipakai untuk Garment
   (NAG) maupun Knitting (NAK), jadi nama perusahaan yang dipatok akan salah
   separuh waktu. Lingkupnya ikut di judul dan di baris PROFIT CENTER. */
$judulBerkas = 'CASH FLOW PROJECTION'
             . (isset($info['pc_label']) && $info['pc_label'] !== ''
                ? ' - ' . strtoupper($info['pc_label']) : '');

/* Lembar sumber: susunannya DARI SATU TEMPAT yang sama dgn ekspor potret
   (cf_sumber_lembar + cfhxLembarSumber), jadi dua Excel itu mustahil berbeda
   kolomnya. Catatan lembarnya disesuaikan dulu dgn keadaan laporan ini -
   dihitung ulang atau memakai potret. */
$lembarSumber = function_exists('cf_sumber_lembar') ? cf_sumber_lembar() : array();
$sumber = isset($cfpSumber) ? $cfpSumber : array();

$arDoc = trim((string) (isset($info['ar_doc']) ? $info['ar_doc'] : ''));
$apDoc = trim((string) (isset($info['ap_doc']) ? $info['ap_doc'] : ''));
if (isset($lembarSumber['AR_EXISTING'])) {
    $lembarSumber['AR_EXISTING']['catatan'] = ($arDoc !== '')
        ? 'From saved AR collection projection ' . $arDoc . ' - amounts are net of withholding tax.'
        : 'No saved AR projection was used, so the figures were recalculated (not saved).';
}
if (isset($lembarSumber['PV_AP'])) {
    $lembarSumber['PV_AP']['catatan'] = ($apDoc !== '')
        ? 'From saved AP projection ' . $apDoc . '.'
        : 'No saved AP projection was used, so the figures were recalculated (not saved).';
}

$sp = new Spreadsheet();

/* Urutan lembarnya mengikuti type: susunan yang jadi tab bawaan di layar
   ditaruh lebih dulu, supaya Excel dan layar terbaca searah. */
$lembar = array(
    array('Report by Date', 'F2CFEE', $kolomTgl,  $barisTgl,  $pinjTgl),
    array('Report by Bank', 'CFE3F2', $kolomAkun, $barisAkun, $pinjAkun),
);
if (isset($jawab['mode']) && $jawab['mode'] === 'akun') { $lembar = array_reverse($lembar); }

$dibuat = 0;
foreach ($lembar as $l) {
    if (!$l[3]) { continue; }
    cfhxLembarProyeksi($sp, $l[0], $l[1], $l[2], $l[3], $l[4], $judulBerkas, $info);
    $dibuat++;
}

/* Lembar bawaan yang lahir bersama Spreadsheet() dibuang - kedua lembar di
   atas dibuat lewat createSheet(). Kalau tidak ada satu pun lembar isi
   (mustahil, tapi tetap dijaga) lembar bawaannya dibiarkan supaya berkasnya
   tetap sah. */
/* Lembar sumbernya menyusul SESUDAH kedua lembar matriks - urutannya sama
   dgn ekspor potret, dan sama dgn urutan tab di layar. */
foreach ($lembarSumber as $kodeLbr => $defLbr) {
    cfhxLembarSumber($sp, $defLbr,
                     isset($sumber[$kodeLbr]) ? $sumber[$kodeLbr] : array(),
                     $judulBerkas);
    $dibuat++;
}

if ($dibuat > 0) { $sp->removeSheetByIndex(0); }
$sp->setActiveSheetIndex(0);

$nama = 'Cash Flow Projection ' . $type . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nama . '"');
header('Cache-Control: max-age=0');

IOFactory::createWriter($sp, 'Xlsx')->save('php://output');
exit;
