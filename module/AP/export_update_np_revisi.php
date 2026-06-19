<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../../conn/conn.php';

$no_dok = $_GET['no_dok'] ?? '';
$no_dok_esc = mysqli_real_escape_string($conn1, $no_dok);

if (empty($no_dok)) {
    http_response_code(400);
    exit('No Dokumen tidak valid');
}

$sql = mysqli_query($conn1, "SELECT baris, tipe, no_ref, no_barcode, curr_old, curr_new, price_old, price_new, status, keterangan
    FROM update_np_revisi_d WHERE no_dok = '$no_dok_esc' ORDER BY baris ASC, id ASC");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Detail');

$headers = ['No', 'Tipe', 'No Dokumen', 'No Barcode', 'Curr Lama', 'Curr Baru', 'Price Lama', 'Price Baru', 'Status', 'Keterangan'];
$sheet->fromArray($headers, null, 'A1');

$sheet->getStyle('A1:J1')->getFont()->setBold(true);
$sheet->getStyle('A1:J1')->getFont()->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
$sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$r = 2;
$no = 1;
while ($row = mysqli_fetch_assoc($sql)) {
    $sheet->fromArray([
        $no++,
        $row['tipe'],
        $row['no_ref'],
        $row['no_barcode'],
        $row['curr_old'],
        $row['curr_new'],
        $row['price_old'] !== null ? (float) $row['price_old'] : null,
        $row['price_new'] !== null ? (float) $row['price_new'] : null,
        $row['status'],
        $row['keterangan'],
    ], null, 'A' . $r);
    $r++;
}

$widths = ['A' => 5, 'B' => 8, 'C' => 20, 'D' => 18, 'E' => 10, 'F' => 10, 'G' => 14, 'H' => 14, 'I' => 10, 'J' => 30];
foreach ($widths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

$filename = 'Detail_' . preg_replace('/[^A-Za-z0-9]+/', '_', $no_dok) . '.xlsx';

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
