<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../../conn/conn.php';

$startInput = trim($_GET['start_date'] ?? '');
$endInput = trim($_GET['end_date'] ?? '');
$status = trim($_GET['status'] ?? 'ALL');
$startDate = DateTime::createFromFormat('d-m-Y', $startInput);
$endDate = DateTime::createFromFormat('d-m-Y', $endInput);

if (!$startDate || !$endDate || $startDate->format('d-m-Y') !== $startInput || $endDate->format('d-m-Y') !== $endInput || $startDate > $endDate) {
    http_response_code(400);
    exit('Rentang tanggal tidak valid.');
}

$startSql = mysqli_real_escape_string($conn2, $startDate->format('Y-m-d'));
$endSql = mysqli_real_escape_string($conn2, $endDate->format('Y-m-d'));
$where = "h.pl_date BETWEEN '$startSql' AND '$endSql'";
if ($status !== '' && strtoupper($status) !== 'ALL') {
    $where .= " AND h.status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}

$result = mysqli_query($conn2, "SELECT h.pl_number, h.pl_date, h.status AS pl_status,
        d.nama_supp, d.no_kbon, d.tgl_kbon, d.curr, d.total, d.profit_center, d.deskripsi
    FROM pv_payment_list_h h
    INNER JOIN pv_payment_list_det d ON d.pl_number = h.pl_number
    WHERE $where AND d.status != 'Cancel'
    ORDER BY h.pl_date DESC, h.pl_number DESC, d.nama_supp, d.no_kbon");
if (!$result) {
    http_response_code(500);
    exit('Query export gagal: ' . mysqli_error($conn2));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Payment List');
$sheet->mergeCells('A1:J1');
$sheet->setCellValue('A1', 'PAYMENT LIST - DETAIL');
$sheet->mergeCells('A2:J2');
$sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'));
$sheet->getStyle('A1:A2')->getFont()->setBold(true);

$sheet->fromArray(['No Payment List', 'PL Date', 'Status', 'Supplier', 'No PV', 'PV Date', 'Curr', 'Amount', 'Profit Center', 'Description'], null, 'A4');
$sheet->getStyle('A4:J4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A4:J4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
$sheet->getStyle('A4:J4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rowNumber = 5;
$currentDoc = null;
$documentStart = $rowNumber;
$totals = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($currentDoc !== null && $currentDoc !== $row['pl_number']) {
        $documentEnd = $rowNumber - 1;
        if ($documentEnd > $documentStart) foreach (['A', 'B', 'C'] as $column) $sheet->mergeCells($column . $documentStart . ':' . $column . $documentEnd);
        $documentStart = $rowNumber;
    }

    $amount = (float) $row['total'];
    $sheet->fromArray([$row['pl_number'], !empty($row['pl_date']) ? date('d M Y', strtotime($row['pl_date'])) : '-', $row['pl_status'], $row['nama_supp'], $row['no_kbon'], !empty($row['tgl_kbon']) ? date('d M Y', strtotime($row['tgl_kbon'])) : '-', $row['curr'], $amount, $row['profit_center'] ?: '-', $row['deskripsi'] ?: '-'], null, 'A' . $rowNumber);
    $sheet->getStyle('H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $totals[$row['curr']] = ($totals[$row['curr']] ?? 0) + $amount;
    $currentDoc = $row['pl_number'];
    $rowNumber++;
}
if ($currentDoc !== null && $rowNumber - 1 > $documentStart) foreach (['A', 'B', 'C'] as $column) $sheet->mergeCells($column . $documentStart . ':' . $column . ($rowNumber - 1));

ksort($totals);
foreach ($totals as $curr => $total) {
    $sheet->mergeCells('A' . $rowNumber . ':F' . $rowNumber);
    $sheet->setCellValue('A' . $rowNumber, 'Grand Total ' . $curr);
    $sheet->setCellValue('G' . $rowNumber, $curr);
    $sheet->setCellValue('H' . $rowNumber, $total);
    $sheet->getStyle('A' . $rowNumber . ':J' . $rowNumber)->getFont()->setBold(true);
    $sheet->getStyle('H' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $rowNumber++;
}

$lastRow = max(4, $rowNumber - 1);
$sheet->getStyle('A4:J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->getStyle('A4:J' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$sheet->getStyle('J5:J' . max(5, $lastRow))->getAlignment()->setWrapText(true);
foreach (['A' => 26, 'B' => 14, 'C' => 18, 'D' => 28, 'E' => 20, 'F' => 14, 'G' => 10, 'H' => 18, 'I' => 16, 'J' => 45] as $column => $width) $sheet->getColumnDimension($column)->setWidth($width);

while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Payment_List_All_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx"');
header('Cache-Control: max-age=0');
IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
