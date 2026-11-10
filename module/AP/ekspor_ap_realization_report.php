<?php
/* ============================================================================
   AP PAYMENT REALIZATION & VARIANCE REPORT - EKSPOR EXCEL.
   Dipanggil tombol Export di ap_realization_report.php.

   EMPAT LEMBAR dalam satu berkas, susunannya mengikuti
   "AP Payment Realization & Variance Report Ver 1.1.xlsx":
       1. Executive Summary            indikator + rekap relasi x jenis barang
       2. AP Payment Realization & Var satu baris per voucher
       3. Realization                  satu baris per baris pembayaran
       4. Projection                   isi dokumen proyeksinya

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI. Berkas ini menyetel $_POST lalu
   meng-include ajx_ap_realization_report.php dalam mode pustaka
   (APRV_PUSTAKA) dan memakai keluarannya apa adanya - jadi isi Excel dijamin
   sama dgn layar. Menyalin rumusnya ke sini cepat atau lambat membuat Excel
   dan layar berbeda, dan itu jenis selisih yang paling lama ketahuannya.

   PAKAI PhpSpreadsheet, bukan tabel HTML bertipe vnd-ms-excel seperti ekspor
   AP yang lain. Alasannya satu: cara HTML itu hanya sanggup SATU lembar,
   sedangkan laporan ini harus keluar empat lembar sekaligus. Pustakanya
   sudah ada di vendor/ dan sudah dipakai ekspor_kontrabon.php, jadi tidak
   ada kebergantungan baru. Angkanya ditulis sbg ANGKA dgn number format -
   bukan teks yang kebetulan mirip angka - sehingga SUM di Excel langsung
   jalan.

   Garisnya TIPIS HITAM (BORDER_THIN), mengikuti permintaan pemakai 6 Nov
   2026 untuk ekspor proyeksi: "border nya hitam normal saja", lalu "masih
   terlalu bold" - jadi yang paling tipis.
   ============================================================================ */

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

define('APRV_PUSTAKA', 1);
$_POST['doc_number'] = isset($_REQUEST['doc_number']) ? $_REQUEST['doc_number'] : '';

ob_start();
include __DIR__ . '/ajx_ap_realization_report.php';
$jawaban = json_decode(trim((string) ob_get_clean()), true);

if (!is_array($jawaban) || isset($jawaban['error'])) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'The report could not be exported: '
       . (is_array($jawaban) && isset($jawaban['error']) ? $jawaban['error'] : 'no data.');
    exit;
}

$info      = $jawaban['info'];
$baris     = $jawaban['data'];
$ringkas   = $jawaban['ringkas'];
$proyeksi  = $jawaban['proyeksi'];
$realisasi = $jawaban['realisasi'];

/* ------------------------------------------------------------------ gaya */
const APRV_NAVY   = 'FF1E3A5F';
const APRV_PRJ    = 'FF2C5282';
const APRV_REAL   = 'FF115E59';
const APRV_RES    = 'FF854D0E';
const APRV_UANG   = '#,##0.00';
const APRV_UANG0  = '#,##0';
const APRV_PERSEN = '0.00%';
/* Sepasang dgn tampilan layarnya: '01 Oct 2026' (permintaan pemakai
   9 Nov 2026). Selnya tetap berisi TANGGAL Excel yang sebenarnya, bukan
   teks - yang diatur di sini hanya cara Excel menampilkannya, jadi
   pengurutan dan penyaringan di Excel tetap jalan. */
const APRV_TGL    = 'dd mmm yyyy';

function aprvGarisTipis()
{
    return array('borderStyle' => Border::BORDER_THIN,
                 'color' => array('argb' => 'FF000000'));
}

/** Kepala tabel: tebal, putih di atas latar, bergaris, teks di tengah. */
function aprvKepala($lembar, $rentang, $warna)
{
    $lembar->getStyle($rentang)->applyFromArray(array(
        'font' => array('bold' => true, 'size' => 9,
                        'color' => array('argb' => 'FFF8FAFC')),
        'fill' => array('fillType' => Fill::FILL_SOLID,
                        'startColor' => array('argb' => $warna)),
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                             'vertical' => Alignment::VERTICAL_CENTER,
                             'wrapText' => true),
        'borders' => array('allBorders' => aprvGarisTipis()),
    ));
}

/** Satu baris judul besar di kiri atas tiap lembar. */
function aprvJudul($lembar, $baris, $teks, $besar = 12)
{
    $lembar->setCellValue('A' . $baris, $teks);
    $lembar->getStyle('A' . $baris)->getFont()->setBold(true)->setSize($besar);
}

/** Menulis satu sel: angka tetap angka, null tetap kosong. */
function aprvSel($lembar, $kol, $brs, $nilai, $format = null)
{
    $alamat = $kol . $brs;
    if ($nilai === null || $nilai === '') {
        $lembar->setCellValue($alamat, null);
    } elseif (is_int($nilai) || is_float($nilai)) {
        $lembar->setCellValue($alamat, $nilai);
    } else {
        /* Nomor dokumen & nomor voucher WAJIB teks: "2026/06/01376" kalau
           dibiarkan ditebak Excel bisa berubah jadi tanggal atau pecahan. */
        $lembar->setCellValueExplicit($alamat, (string) $nilai,
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    }
    if ($format !== null) {
        $lembar->getStyle($alamat)->getNumberFormat()->setFormatCode($format);
    }
}

/** Tanggal ISO -> sel bertipe tanggal. Kosong tetap kosong, bukan 1900. */
function aprvSelTgl($lembar, $kol, $brs, $iso)
{
    $alamat = $kol . $brs;
    if (empty($iso)) { $lembar->setCellValue($alamat, null); return; }
    $lembar->setCellValue($alamat,
        \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($iso)));
    $lembar->getStyle($alamat)->getNumberFormat()->setFormatCode(APRV_TGL);
}

/** Huruf kolom dari nomor 1-based: 1->A, 27->AA. */
function aprvKol($n)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($n);
}

$sp = new Spreadsheet();
$sp->getProperties()->setCreator('AP')->setTitle('AP Payment Realization & Variance Report');

/* ======================================================================
   LEMBAR 1 - EXECUTIVE SUMMARY
   ====================================================================== */
$l1 = $sp->getActiveSheet();
$l1->setTitle('Executive Summary');
aprvJudul($l1, 1, 'PT NIRWANA ALABARE GARMENT', 13);
aprvJudul($l1, 2, 'AP PAYMENT PERFORMANCE DASHBOARD', 11);
$l1->setCellValue('A3', 'Doc Number');
$l1->setCellValueExplicit('B3', $info['doc_number'],
    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
$l1->setCellValue('A4', 'Reporting Period');
$l1->setCellValue('B4', date('d M Y', strtotime($info['periode_dari']))
                      . ' s/d ' . date('d M Y', strtotime($info['periode_sampai'])));
$l1->setCellValue('A5', 'Supplier');
$l1->setCellValue('B5', $info['supplier']);
$l1->getStyle('A3:A5')->getFont()->setBold(true);

/* Daftar indikatornya SAMA dgn papan di layar dan dgn lembar contohnya -
   nama, urutan, dan keterangannya. */
$indikator = array(
    array('CASH FLOW PERFORMANCE', null, null),
    array('Projected Payment', $ringkas['projected'], 'Approved projected cash outflow'),
    array('Actual Payment', $ringkas['actual'], 'Actual cash outflow'),
    array('Payment Realization Ratio', $ringkas['ratio'], 'Ratio, not a performance score'),
    array('Net Cash Flow Variance', $ringkas['net_variance'],
          'Positive = higher cash outflow than projected'),
    array('Payment Variance (Excl. FX)', $ringkas['variance_excl_fx'],
          'Variance excluding exchange differences'),
    array('FX Variance (Source)', $ringkas['fx_variance'], 'FX difference on realized OCY'),
    array('Reconciliation Check', $ringkas['rekonsiliasi'],
          'Actual - Projected - Total Variance. Must be zero.'),
    array(null, null, null),
    array('PAYMENT EXECUTION', null, null),
    array('Planned Payments Realized', $ringkas['planned_realized'],
          'Actual paid against planned vouchers'),
    array('Unplanned Payments', $ringkas['unplanned'],
          'Actual payments without a planned date'),
    array('Planned Amount Not Executed', $ringkas['not_executed'],
          'Full projected IDR amount of unpaid vouchers'),
    array('Not Paid Vouchers (Count)', (int) $ringkas['jml_not_paid'],
          'Count of unpaid planned vouchers'),
    array('Partially Paid Vouchers (Count)', (int) $ringkas['jml_partial'],
          'Count of partially paid vouchers'),
    array(null, null, null),
    array('PAYMENT RISK & TIMELINESS', null, null),
    array('Overdue Unpaid Planned Amount', $ringkas['overdue_not_paid'],
          'Due before the reporting date and still unpaid'),
    array('On-time / Early Paid Vouchers', (int) $ringkas['jml_ontime'],
          'Versus planned date, not contractual due date'),
    array('Late Paid Vouchers', (int) $ringkas['jml_late'], 'Versus planned date'),
    array('On-time Payment Rate', $ringkas['ontime_rate'],
          'Based on fully paid vouchers with a date variance'),
);

$r = 7;
$l1->setCellValue('A' . $r, 'INDICATOR');
$l1->setCellValue('B' . $r, 'Amount / Ratio');
$l1->setCellValue('C' . $r, 'Management interpretation');
aprvKepala($l1, 'A' . $r . ':C' . $r, APRV_NAVY);
$r++;

foreach ($indikator as $b) {
    if ($b[0] === null) { $r++; continue; }
    if ($b[1] === null) {                        /* judul kelompok */
        $l1->setCellValue('A' . $r, $b[0]);
        $l1->getStyle('A' . $r . ':C' . $r)->applyFromArray(array(
            'font' => array('bold' => true, 'size' => 9),
            'fill' => array('fillType' => Fill::FILL_SOLID,
                            'startColor' => array('argb' => 'FFEEF3FB')),
        ));
        $r++;
        continue;
    }
    $l1->setCellValue('A' . $r, $b[0]);
    /* Rasio ditulis sbg ANGKA ber-format persen, bukan teks "97,50 %" -
       supaya masih bisa dihitung di Excel. */
    $format = APRV_UANG;
    if (strpos($b[0], 'Ratio') !== false || strpos($b[0], 'Rate') !== false) {
        $format = APRV_PERSEN;
    } elseif (strpos($b[0], 'Count') !== false) {
        $format = APRV_UANG0;
    }
    aprvSel($l1, 'B', $r, ($b[1] === null ? null : (float) $b[1]), $format);
    $l1->setCellValue('C' . $r, $b[2]);
    $l1->getStyle('A' . $r . ':C' . $r)->applyFromArray(
        array('borders' => array('allBorders' => aprvGarisTipis())));
    $r++;
}

/* rekap relasi x jenis barang */
$r += 1;
aprvJudul($l1, $r, 'Summary', 11);
$r++;
$kepalaRekap = array('Relationship', 'Item Type', 'Voucher', 'Projected',
                     'Realization', 'Variance', 'Achievement');
foreach ($kepalaRekap as $i => $t) { $l1->setCellValue(aprvKol($i + 1) . $r, $t); }
aprvKepala($l1, 'A' . $r . ':G' . $r, APRV_NAVY);
$r++;

$tp = 0.0; $ta = 0.0; $tj = 0;
foreach ($ringkas['rekap'] as $b) {
    $tp += (float) $b['projected']; $ta += (float) $b['actual']; $tj += (int) $b['jml'];
    aprvSel($l1, 'A', $r, $b['relasi']);
    aprvSel($l1, 'B', $r, $b['item_type']);
    aprvSel($l1, 'C', $r, (int) $b['jml'], APRV_UANG0);
    aprvSel($l1, 'D', $r, (float) $b['projected'], APRV_UANG);
    aprvSel($l1, 'E', $r, (float) $b['actual'], APRV_UANG);
    aprvSel($l1, 'F', $r, (float) $b['variance'], APRV_UANG);
    aprvSel($l1, 'G', $r, ($b['achievement'] === null ? null : (float) $b['achievement']), APRV_PERSEN);
    $l1->getStyle('A' . $r . ':G' . $r)->applyFromArray(
        array('borders' => array('allBorders' => aprvGarisTipis())));
    $r++;
}
aprvSel($l1, 'A', $r, 'TOTAL');
aprvSel($l1, 'C', $r, $tj, APRV_UANG0);
aprvSel($l1, 'D', $r, $tp, APRV_UANG);
aprvSel($l1, 'E', $r, $ta, APRV_UANG);
aprvSel($l1, 'F', $r, $tp - $ta, APRV_UANG);
aprvSel($l1, 'G', $r, ($tp != 0 ? $ta / $tp : null), APRV_PERSEN);
$l1->getStyle('A' . $r . ':G' . $r)->applyFromArray(array(
    'font' => array('bold' => true),
    'fill' => array('fillType' => Fill::FILL_SOLID,
                    'startColor' => array('argb' => 'FFF8FAFC')),
    'borders' => array('allBorders' => aprvGarisTipis()),
));

$l1->getColumnDimension('A')->setWidth(34);
$l1->getColumnDimension('B')->setWidth(22);
$l1->getColumnDimension('C')->setWidth(44);
foreach (array('D', 'E', 'F', 'G') as $k) { $l1->getColumnDimension($k)->setWidth(18); }

/* ======================================================================
   LEMBAR 2 - AP PAYMENT REALIZATION & VAR
   ====================================================================== */
$l2 = $sp->createSheet();
$l2->setTitle('AP Payment Realization & Var');
aprvJudul($l2, 1, 'PT NIRWANA ALABARE GARMENT', 13);
aprvJudul($l2, 2, 'ACCOUNT PAYABLE PAYMENT REALIZATION & VARIANCE REPORT', 11);
aprvSelTgl($l2, 'A', 3, $info['tgl_laporan']);
$l2->getStyle('A3')->getFont()->setBold(true);
$l2->setCellValue('C3', 'Doc Number : ' . $info['doc_number']);

/* Kepala DUA TINGKAT, sama spt lembar contohnya: tujuh kolom keterangan
   memanjang ke bawah, lalu tiga payung Projection / Realization / Result. */
$kolTunggal = array('Supplier', 'Payment Voucher No', 'Relationship', 'Item Type',
                    'PV Date', 'Due Date', 'Currency');
$grup = array(
    array('judul' => 'Projection',  'warna' => APRV_PRJ,
          'kolom' => array('Payment Plan Date', 'Amount in OCY', 'Rate', 'Amount Eqv Idr')),
    array('judul' => 'Realization', 'warna' => APRV_REAL,
          'kolom' => array('Payment Date', 'Amount in OCY', 'Rate', 'Amount Eqv Idr')),
    array('judul' => 'Result',      'warna' => APRV_RES,
          'kolom' => array('Payment Status', 'Payment Achievement %', 'Payment Date Variance',
                           'Days Past Due', 'Variance Explanation',
                           'Payment Variance (Excl. FX)', 'FX Variance', 'Total Variance')),
);

$br1 = 5; $br2 = 6; $c = 1;
foreach ($kolTunggal as $t) {
    $l2->setCellValue(aprvKol($c) . $br1, $t);
    $l2->mergeCells(aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2);
    aprvKepala($l2, aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2, APRV_NAVY);
    $c++;
}
foreach ($grup as $g) {
    $mulai = $c;
    foreach ($g['kolom'] as $t) {
        $l2->setCellValue(aprvKol($c) . $br2, $t);
        aprvKepala($l2, aprvKol($c) . $br2, $g['warna']);
        $c++;
    }
    $l2->setCellValue(aprvKol($mulai) . $br1, $g['judul']);
    $l2->mergeCells(aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1);
    aprvKepala($l2, aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1, $g['warna']);
}
$kolTerakhir = $c - 1;

$r = 7;
foreach ($baris as $b) {
    aprvSel($l2, 'A', $r, $b['supplier']);
    aprvSel($l2, 'B', $r, $b['no_kbon']);
    aprvSel($l2, 'C', $r, $b['relasi']);
    aprvSel($l2, 'D', $r, $b['item_type']);
    aprvSelTgl($l2, 'E', $r, $b['tgl_kbon']);
    aprvSelTgl($l2, 'F', $r, $b['duedate']);
    aprvSel($l2, 'G', $r, $b['curr']);

    aprvSelTgl($l2, 'H', $r, $b['plan_date']);
    aprvSel($l2, 'I', $r, (float) $b['proj_ocy'],  APRV_UANG);
    aprvSel($l2, 'J', $r, (float) $b['proj_rate'], APRV_UANG);
    aprvSel($l2, 'K', $r, (float) $b['proj_idr'],  APRV_UANG);

    aprvSelTgl($l2, 'L', $r, $b['real_date']);
    aprvSel($l2, 'M', $r, (float) $b['real_ocy'],  APRV_UANG);
    aprvSel($l2, 'N', $r, (float) $b['real_rate'], APRV_UANG);
    aprvSel($l2, 'O', $r, (float) $b['real_idr'],  APRV_UANG);

    aprvSel($l2, 'P', $r, $b['status']);
    aprvSel($l2, 'Q', $r, ($b['achievement'] === null ? null : (float) $b['achievement']), APRV_PERSEN);
    aprvSel($l2, 'R', $r, ($b['date_variance'] === null ? null : (int) $b['date_variance']), APRV_UANG0);
    aprvSel($l2, 'S', $r, ($b['days_past_due'] === null ? null : (int) $b['days_past_due']), APRV_UANG0);
    aprvSel($l2, 'T', $r, html_entity_decode((string) $b['explanation'], ENT_QUOTES));
    aprvSel($l2, 'U', $r, (float) $b['variance_excl_fx'], APRV_UANG);
    aprvSel($l2, 'V', $r, (float) $b['fx_variance'],      APRV_UANG);
    aprvSel($l2, 'W', $r, (float) $b['total_variance'],   APRV_UANG);

    $l2->getStyle('A' . $r . ':' . aprvKol($kolTerakhir) . $r)->applyFromArray(
        array('borders' => array('allBorders' => aprvGarisTipis())));
    $r++;
}

/* Baris TOTAL. Kolom tanggal & status sengaja dibiarkan kosong - menjumlah
   tanggal tidak ada artinya. */
if ($baris) {
    $awal = 7; $akhir = $r - 1;
    aprvSel($l2, 'A', $r, 'TOTAL');
    foreach (array('K', 'O', 'U', 'V', 'W') as $k) {
        $l2->setCellValue($k . $r, '=SUM(' . $k . $awal . ':' . $k . $akhir . ')');
        $l2->getStyle($k . $r)->getNumberFormat()->setFormatCode(APRV_UANG);
    }
    $l2->getStyle('A' . $r . ':' . aprvKol($kolTerakhir) . $r)->applyFromArray(array(
        'font' => array('bold' => true),
        'fill' => array('fillType' => Fill::FILL_SOLID,
                        'startColor' => array('argb' => 'FFF8FAFC')),
        'borders' => array('allBorders' => aprvGarisTipis()),
    ));
}

$l2->freezePane('A7');
$l2->getColumnDimension('A')->setWidth(30);
$l2->getColumnDimension('B')->setWidth(28);
$l2->getColumnDimension('T')->setWidth(34);
for ($i = 3; $i <= $kolTerakhir; $i++) {
    if (in_array(aprvKol($i), array('T'), true)) { continue; }
    $l2->getColumnDimension(aprvKol($i))->setWidth(15);
}

/* ======================================================================
   LEMBAR 3 - REALIZATION
   ====================================================================== */
$l3 = $sp->createSheet();
$l3->setTitle('Realization');
aprvJudul($l3, 1, 'AP PAYMENT REALIZATION REPORT', 12);
$l3->setCellValue('A2', 'Period : ' . date('d M Y', strtotime($info['periode_dari']))
                      . ' s/d ' . date('d M Y', strtotime($info['periode_sampai'])));

/* Rate ADA DI DAFTAR TUNGGAL, bukan di bawah payung "In Original Currency"
   (diminta pemakai 9 Nov 2026): kursnya bukan nilai dalam mata uang asal -
   justru ia yang menjembatani kelompok OCY ke kelompok IDR, jadi tidak boleh
   ikut berwarna hijau salah satu sisi. Lembar Projection sudah begitu sejak
   awal; lembar ini yang tertinggal.
   Letak kolomnya TIDAK berubah - Rate tetap kolom I - jadi seluruh aprvSel()
   di bawah tetap menunjuk sel yang sama. Yang berubah hanya kepalanya:
   I4:I5 jadi satu sel biru, dan payung "In Original Currency" mulai dari J. */
$tunggal3 = array('No Payment', 'Payment Date', 'Supplier', 'No Kontrabon',
                  'Kontrabon Date', 'Relationship', 'Item Type', 'Currency',
                  'Rate');
$grup3 = array(
    array('judul' => 'In Original Currency', 'warna' => APRV_REAL,
          'kolom' => array('Tax Base', 'VAT', 'Total Invoice',
                           'Income Tax Art 23', 'Payment Amount')),
    array('judul' => 'In IDR', 'warna' => APRV_PRJ,
          'kolom' => array('Tax Base', 'VAT', 'Total Invoice',
                           'Income Tax Art 23', 'Payment Amount')),
);

$br1 = 4; $br2 = 5; $c = 1;
foreach ($tunggal3 as $t) {
    $l3->setCellValue(aprvKol($c) . $br1, $t);
    $l3->mergeCells(aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2);
    aprvKepala($l3, aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2, APRV_NAVY);
    $c++;
}
foreach ($grup3 as $g) {
    $mulai = $c;
    foreach ($g['kolom'] as $t) {
        $l3->setCellValue(aprvKol($c) . $br2, $t);
        aprvKepala($l3, aprvKol($c) . $br2, $g['warna']);
        $c++;
    }
    $l3->setCellValue(aprvKol($mulai) . $br1, $g['judul']);
    $l3->mergeCells(aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1);
    aprvKepala($l3, aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1, $g['warna']);
}
$akhir3 = $c - 1;

$r = 6;
foreach ($realisasi as $b) {
    aprvSel($l3, 'A', $r, $b['no_payment']);
    aprvSelTgl($l3, 'B', $r, $b['pay_date']);
    aprvSel($l3, 'C', $r, $b['supplier']);
    aprvSel($l3, 'D', $r, $b['no_kbon']);
    aprvSelTgl($l3, 'E', $r, $b['tgl_kbon']);
    aprvSel($l3, 'F', $r, $b['relasi']);
    aprvSel($l3, 'G', $r, $b['item_type']);
    aprvSel($l3, 'H', $r, $b['curr']);
    aprvSel($l3, 'I', $r, (float) $b['rate'], APRV_UANG);
    aprvSel($l3, 'J', $r, ($b['tax_base'] === null ? null : (float) $b['tax_base']), APRV_UANG);
    aprvSel($l3, 'K', $r, ($b['vat'] === null ? null : (float) $b['vat']), APRV_UANG);
    aprvSel($l3, 'L', $r, ($b['total_invoice'] === null ? null : (float) $b['total_invoice']), APRV_UANG);
    aprvSel($l3, 'M', $r, ($b['pph'] === null ? null : (float) $b['pph']), APRV_UANG);
    aprvSel($l3, 'N', $r, (float) $b['pay_amount'], APRV_UANG);
    aprvSel($l3, 'O', $r, ($b['tax_base_idr'] === null ? null : (float) $b['tax_base_idr']), APRV_UANG);
    aprvSel($l3, 'P', $r, ($b['vat_idr'] === null ? null : (float) $b['vat_idr']), APRV_UANG);
    aprvSel($l3, 'Q', $r, ($b['total_invoice_idr'] === null ? null : (float) $b['total_invoice_idr']), APRV_UANG);
    aprvSel($l3, 'R', $r, ($b['pph_idr'] === null ? null : (float) $b['pph_idr']), APRV_UANG);
    aprvSel($l3, 'S', $r, (float) $b['pay_amount_idr'], APRV_UANG);
    $l3->getStyle('A' . $r . ':' . aprvKol($akhir3) . $r)->applyFromArray(
        array('borders' => array('allBorders' => aprvGarisTipis())));
    $r++;
}
if ($realisasi) {
    $awal = 6; $habis = $r - 1;
    aprvSel($l3, 'A', $r, 'TOTAL');
    foreach (array('N', 'S') as $k) {
        $l3->setCellValue($k . $r, '=SUM(' . $k . $awal . ':' . $k . $habis . ')');
        $l3->getStyle($k . $r)->getNumberFormat()->setFormatCode(APRV_UANG);
    }
    $l3->getStyle('A' . $r . ':' . aprvKol($akhir3) . $r)->applyFromArray(array(
        'font' => array('bold' => true),
        'fill' => array('fillType' => Fill::FILL_SOLID,
                        'startColor' => array('argb' => 'FFF8FAFC')),
        'borders' => array('allBorders' => aprvGarisTipis()),
    ));
}
$l3->freezePane('A6');
$l3->getColumnDimension('A')->setWidth(28);
/* Kolom B (Payment Date) TERLEWAT: lebarnya tidak pernah disetel - A, C dan D
   disetel satu per satu lalu perulangan di bawah mulai dari kolom E - jadi ia
   tinggal selebar bawaan dan Excel mencetak "########" untuk tanggalnya.
   Lebarnya disamakan dgn Kontrabon Date (kolom E) yang tampil utuh. */
$l3->getColumnDimension('B')->setWidth(15);
$l3->getColumnDimension('C')->setWidth(30);
$l3->getColumnDimension('D')->setWidth(28);
for ($i = 5; $i <= $akhir3; $i++) { $l3->getColumnDimension(aprvKol($i))->setWidth(15); }

/* ======================================================================
   LEMBAR 4 - PROJECTION
   ====================================================================== */
$l4 = $sp->createSheet();
$l4->setTitle('Projection');
aprvJudul($l4, 1, 'AP PAYMENT PROJECTION REPORT', 12);
$l4->setCellValue('A2', 'Doc Number : ' . $info['doc_number']);
$l4->setCellValue('A3', 'Period : ' . date('d M Y', strtotime($info['periode_dari']))
                      . ' s/d ' . date('d M Y', strtotime($info['periode_sampai'])));

$tunggal4 = array('No', 'Supplier', 'Payment Voucher No', 'PV Date', 'Payment Term',
                  'Due Date', 'Payment Plan Date', 'Relationship', 'Item Type',
                  'Flexibility Category', 'Currency', 'Invoice Amount', 'Rate');
$grup4 = array('judul' => 'Payable Amount', 'warna' => APRV_PRJ,
               'kolom' => array('Tax Base', 'VAT', 'Total Invoice', 'Income Tax Art 23',
                                'Payment Amount', 'Projected Cash Outflow'));

$br1 = 5; $br2 = 6; $c = 1;
foreach ($tunggal4 as $t) {
    $l4->setCellValue(aprvKol($c) . $br1, $t);
    $l4->mergeCells(aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2);
    aprvKepala($l4, aprvKol($c) . $br1 . ':' . aprvKol($c) . $br2, APRV_NAVY);
    $c++;
}
$mulai = $c;
foreach ($grup4['kolom'] as $t) {
    $l4->setCellValue(aprvKol($c) . $br2, $t);
    aprvKepala($l4, aprvKol($c) . $br2, $grup4['warna']);
    $c++;
}
$l4->setCellValue(aprvKol($mulai) . $br1, $grup4['judul']);
$l4->mergeCells(aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1);
aprvKepala($l4, aprvKol($mulai) . $br1 . ':' . aprvKol($c - 1) . $br1, $grup4['warna']);
$akhir4 = $c - 1;

$r = 7;
foreach ($proyeksi as $b) {
    aprvSel($l4, 'A', $r, (int) $b['no'], APRV_UANG0);
    aprvSel($l4, 'B', $r, $b['supplier']);
    aprvSel($l4, 'C', $r, $b['no_kbon']);
    aprvSelTgl($l4, 'D', $r, $b['tgl_kbon']);
    aprvSel($l4, 'E', $r, $b['term']);
    aprvSelTgl($l4, 'F', $r, $b['duedate']);
    aprvSelTgl($l4, 'G', $r, $b['plan_date']);
    aprvSel($l4, 'H', $r, $b['relasi']);
    aprvSel($l4, 'I', $r, $b['item_type']);
    aprvSel($l4, 'J', $r, $b['flexibility']);
    aprvSel($l4, 'K', $r, $b['curr']);
    aprvSel($l4, 'L', $r, (float) $b['invoice_amount'], APRV_UANG);
    aprvSel($l4, 'M', $r, (float) $b['rate'], APRV_UANG);
    aprvSel($l4, 'N', $r, (float) $b['tax_base'], APRV_UANG);
    aprvSel($l4, 'O', $r, (float) $b['vat'], APRV_UANG);
    aprvSel($l4, 'P', $r, (float) $b['total_invoice'], APRV_UANG);
    aprvSel($l4, 'Q', $r, (float) $b['pph'], APRV_UANG);
    aprvSel($l4, 'R', $r, (float) $b['payment_amount'], APRV_UANG);
    aprvSel($l4, 'S', $r, (float) $b['outstanding'], APRV_UANG);
    $l4->getStyle('A' . $r . ':' . aprvKol($akhir4) . $r)->applyFromArray(
        array('borders' => array('allBorders' => aprvGarisTipis())));
    $r++;
}
if ($proyeksi) {
    $awal = 7; $habis = $r - 1;
    aprvSel($l4, 'A', $r, 'TOTAL');
    foreach (array('N', 'O', 'P', 'Q', 'R', 'S') as $k) {
        $l4->setCellValue($k . $r, '=SUM(' . $k . $awal . ':' . $k . $habis . ')');
        $l4->getStyle($k . $r)->getNumberFormat()->setFormatCode(APRV_UANG);
    }
    $l4->getStyle('A' . $r . ':' . aprvKol($akhir4) . $r)->applyFromArray(array(
        'font' => array('bold' => true),
        'fill' => array('fillType' => Fill::FILL_SOLID,
                        'startColor' => array('argb' => 'FFF8FAFC')),
        'borders' => array('allBorders' => aprvGarisTipis()),
    ));
}
$l4->freezePane('A7');
$l4->getColumnDimension('B')->setWidth(30);
$l4->getColumnDimension('C')->setWidth(28);
for ($i = 4; $i <= $akhir4; $i++) { $l4->getColumnDimension(aprvKol($i))->setWidth(15); }

$sp->setActiveSheetIndex(0);

/* ---------------------------------------------------------------- kirim */
$nama = 'AP Payment Realization and Variance - '
      . str_replace(array('/', '\\'), '-', $info['doc_number']) . '.xlsx';

/* Mode pustaka: dipakai penjaga supaya berkasnya bisa diperiksa tanpa
   mengirim apa pun ke peramban. */
if (defined('APRV_KE_BERKAS') && APRV_KE_BERKAS !== '') {
    IOFactory::createWriter($sp, 'Xlsx')->save(APRV_KE_BERKAS);
    echo 'OK ' . APRV_KE_BERKAS . "\n";
    return;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nama . '"');
header('Cache-Control: max-age=0');
IOFactory::createWriter($sp, 'Xlsx')->save('php://output');
