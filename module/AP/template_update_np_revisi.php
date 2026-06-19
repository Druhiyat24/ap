<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Template');

$headers = ['Tipe (IN/OUT)', 'No Dokumen', 'No Barcode', 'Curr Update', 'Price Update'];
$sheet->fromArray($headers, null, 'A1');

$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A1:E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
$sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Example rows (one IN, one OUT)
$sheet->fromArray([
    ['IN', 'GK/RI/0426/00234', 'F260213883', 'IDR', 147113.75],
    ['OUT', 'GK/OUT/0426/00543', 'F260406222', 'IDR', 19350],
], null, 'A2');

// Notes / instructions
$noteRow = 5;
$notes = [
    // 'Keterangan:',
    // '1. Tipe harus diisi "IN" atau "OUT" (huruf besar).',
    // '2. Untuk Tipe = IN (Penerimaan): No Dokumen = No Dokumen Penerimaan (no_dok), No Barcode = No Barcode (no_barcode) pada data Lokasi Inmaterial.',
    // '3. Untuk Tipe = OUT (Pengeluaran): No Dokumen = No BPPB (no_bppb), No Barcode = No Roll (id_roll) pada data BPPB Detail.',
    // '4. Curr Baru = kode mata uang revisi (contoh: IDR, USD).',
    // '5. Price Baru = nilai harga revisi (boleh menggunakan desimal).',
    // '6. Baris contoh (baris 2 dan 3) hanya ilustrasi, silakan hapus/ganti dengan data sesuai sebelum upload.',
    // '7. Jika kombinasi Tipe/No Dokumen/No Barcode tidak ditemukan di sistem, baris tersebut akan ditandai "Failed" pada hasil upload.',
];

foreach ($notes as $i => $note) {
    $sheet->setCellValue('A' . ($noteRow + $i), $note);
}
$sheet->getStyle('A' . $noteRow)->getFont()->setBold(true);

// Column widths
$sheet->getColumnDimension('A')->setWidth(18);
$sheet->getColumnDimension('B')->setWidth(22);
$sheet->getColumnDimension('C')->setWidth(18);
$sheet->getColumnDimension('D')->setWidth(12);
$sheet->getColumnDimension('E')->setWidth(14);

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Template_Update_Barcode.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
