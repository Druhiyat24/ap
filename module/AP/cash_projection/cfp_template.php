<?php
/* Template Excel untuk unggahan Form Projection Other.
   Dipanggil: cfp_template.php?jenis=bt|oi|pv

   DUA LEMBAR. "Data" berisi kepala kolom + satu baris contoh yang sengaja
   ditandai supaya kelihatan harus dihapus. "Reference" berisi DAFTAR PILIHAN
   SAH - nama rekening, kategori, supplier/customer - supaya pemakai menyalin,
   bukan mengetik ulang dan salah satu huruf.

   KELUARANNYA BERKAS BINER, jadi pesan PHP dimatikan: satu Notice saja cukup
   membuat berkasnya rusak dan Excel menolak membukanya. */
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

session_start();
include '../../../conn/conn.php';
require '../../../vendor/autoload.php';
include_once __DIR__ . '/cfp_unggah_lib.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$jenis = isset($_GET['jenis']) ? strtolower(trim($_GET['jenis'])) : 'bt';
if (!in_array($jenis, array('bt', 'oi', 'pv'), true)) { $jenis = 'bt'; }

$kolom = cfp_unggah_kolom($jenis);
$judul = cfp_unggah_judul($jenis);

$akun     = cfp_daftar_akun($conn1);
$bank     = $akun;
$ktgIn    = cfp_daftar_kategori($conn1, 'Cash In');
$ktgOut   = cfp_daftar_kategori($conn1, 'Cash Out');
$supplier = cfp_daftar_supplier($conn1);
$customer = cfp_daftar_customer($conn1);

$sp = new Spreadsheet();
$sp->getProperties()->setCreator('AP')->setTitle('Projection Other - ' . $judul);

/* ------------------------------------------------------------- lembar Data */
$l = $sp->getActiveSheet();
$l->setTitle('Data');

for ($i = 0; $i < count($kolom); $i++) {
    $l->setCellValueByColumnAndRow($i + 1, 1, $kolom[$i]);
}
$akhir = chr(64 + count($kolom));
$l->getStyle('A1:' . $akhir . '1')->applyFromArray(array(
    'font' => array('bold' => true, 'color' => array('argb' => 'FFFFFFFF')),
    'fill' => array('fillType' => 'solid', 'startColor' => array('argb' => 'FF1E3A8A')),
    'alignment' => array('horizontal' => 'center'),
));

/* Baris contoh - diberi warna supaya jelas bukan data. */
$contoh = ($jenis === 'bt')
    ? array('19 Nov 2026', '(copy from Reference)', '(copy from Reference)', 1000000, 'example - delete this row')
    : array('19 Nov 2026', '(copy from Reference)', '(copy from Reference)',
            '(copy from Reference)', 1000000, 'example - delete this row');
for ($i = 0; $i < count($contoh); $i++) {
    $l->setCellValueByColumnAndRow($i + 1, 2, $contoh[$i]);
}
$l->getStyle('A2:' . $akhir . '2')->applyFromArray(array(
    'font' => array('italic' => true, 'color' => array('argb' => 'FF9A3412')),
    'fill' => array('fillType' => 'solid', 'startColor' => array('argb' => 'FFFFF7ED')),
));

for ($i = 1; $i <= count($kolom); $i++) {
    $l->getColumnDimensionByColumn($i)->setWidth(($i <= 4) ? 34 : 20);
}
$l->freezePane('A2');

/* -------------------------------------------------------- lembar Reference */
$r = $sp->createSheet();
$r->setTitle('Reference');
$r->getTabColor()->setRGB('92D050');
$r->setCellValue('A1', 'Copy the exact text from the columns below into the Data sheet.');
$r->getStyle('A1')->getFont()->setItalic(true)->setSize(9);

if ($jenis === 'bt') {
    $daftar = array('Source / Destination Account' => array_keys($akun));
} elseif ($jenis === 'oi') {
    $daftar = array('Bank Account' => array_keys($bank),
                    'Income Category' => $ktgIn,
                    'Customer Name' => array_values($customer));
} else {
    $daftar = array('Bank Account' => array_keys($bank),
                    'Cost Category' => $ktgOut,
                    'Supplier Name' => array_values($supplier));
}

$kol = 1;
foreach ($daftar as $namaKolom => $isi) {
    $r->setCellValueByColumnAndRow($kol, 3, $namaKolom);
    $r->getStyleByColumnAndRow($kol, 3)->applyFromArray(array(
        'font' => array('bold' => true, 'color' => array('argb' => 'FFFFFFFF')),
        'fill' => array('fillType' => 'solid', 'startColor' => array('argb' => 'FF1E3A8A')),
    ));
    $baris = 4;
    foreach ($isi as $v) { $r->setCellValueByColumnAndRow($kol, $baris++, $v); }
    $r->getColumnDimensionByColumn($kol)->setWidth(42);
    $kol++;
}
$r->freezePane('A4');

$sp->setActiveSheetIndex(0);

$nama = 'Projection Other - ' . $judul . ' - Template.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nama . '"');
header('Cache-Control: max-age=0');
$w = new Xlsx($sp);
$w->save('php://output');
exit;
