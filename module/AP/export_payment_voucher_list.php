<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../../conn/conn.php';

$plNumber = trim($_GET['pl_number'] ?? '');
if ($plNumber === '') {
    http_response_code(400);
    exit('Payment Voucher List tidak valid.');
}

$plNumberEsc = mysqli_real_escape_string($conn2, $plNumber);
$headerResult = mysqli_query($conn2, "SELECT pl_number, pl_date, deskripsi, created_by, created_date
    FROM pv_payment_voucher_list_h WHERE pl_number = '$plNumberEsc'");
$header = mysqli_fetch_assoc($headerResult);

if (!$header) {
    http_response_code(404);
    exit('Payment Voucher List tidak ditemukan.');
}

function getDueDateForExport($conn, $typePv, $noKbon)
{
    $noKbonEsc = mysqli_real_escape_string($conn, $noKbon);

    if ($typePv === 'Biaya') {
        $result = mysqli_query($conn, "SELECT MAX(b.due_date) AS due_date
            FROM tbl_pv_h a INNER JOIN tbl_pv b ON b.no_pv = a.no_pv
            WHERE a.no_pv = '$noKbonEsc'");
    } elseif ($typePv === 'Installment') {
        $result = mysqli_query($conn, "SELECT tgl_tempo AS due_date
            FROM kontrabon_h_installment_detail WHERE no_kbon_det = '$noKbonEsc'");
    } else {
        $table = $typePv === 'DP' ? 'kontrabon_h_dp' : ($typePv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
        $result = mysqli_query($conn, "SELECT tgl_tempo AS due_date FROM $table WHERE no_kbon = '$noKbonEsc'");
    }

    $row = $result ? mysqli_fetch_assoc($result) : null;
    return $row['due_date'] ?? null;
}

$detailResult = mysqli_query($conn2, "SELECT type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center
    FROM pv_payment_voucher_list_det
    WHERE pl_number = '$plNumberEsc' AND status != 'Cancel'
    ORDER BY no_kbon ASC");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Payment Voucher List');

$sheet->mergeCells('A1:H1');
$sheet->setCellValue('A1', 'PAYMENT VOUCHER LIST: ' . $header['pl_number']);
$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A2', 'Date: ' . (!empty($header['pl_date']) ? date('d M Y', strtotime($header['pl_date'])) : '-'));
$sheet->mergeCells('A3:H3');
$sheet->setCellValue('A3', 'Description: ' . ($header['deskripsi'] ?: '-'));
$sheet->getStyle('A1:A3')->getFont()->setBold(true);

$headers = ['Supplier', 'No PV', 'PV Date', 'Due Date', 'Curr', 'Amount', 'Profit Center', 'Description'];
$sheet->fromArray($headers, null, 'A5');
$sheet->getStyle('A5:H5')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A5:H5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
$sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rowNumber = 6;
$grandTotals = [];
while ($detail = mysqli_fetch_assoc($detailResult)) {
    $dueDate = getDueDateForExport($conn2, $detail['type_pv'], $detail['no_kbon']);
    $profitCenter = !empty($detail['profit_center']) && $detail['profit_center'] !== '-' ? $detail['profit_center'] : '-';
    $amount = (float) $detail['total'];

    $sheet->fromArray([
        $detail['nama_supp'],
        $detail['no_kbon'],
        !empty($detail['tgl_kbon']) ? date('d M Y', strtotime($detail['tgl_kbon'])) : '-',
        !empty($dueDate) ? date('d M Y', strtotime($dueDate)) : '-',
        $detail['curr'],
        $amount,
        $profitCenter,
        $detail['deskripsi'] ?: '-',
    ], null, 'A' . $rowNumber);
    $sheet->getStyle('F' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $grandTotals[$detail['curr']] = ($grandTotals[$detail['curr']] ?? 0) + $amount;
    $rowNumber++;
}

ksort($grandTotals);
foreach ($grandTotals as $currency => $total) {
    $sheet->mergeCells('A' . $rowNumber . ':D' . $rowNumber);
    $sheet->setCellValue('A' . $rowNumber, 'Grand Total ' . $currency);
    $sheet->setCellValue('E' . $rowNumber, $currency);
    $sheet->setCellValue('F' . $rowNumber, $total);
    $sheet->getStyle('A' . $rowNumber . ':H' . $rowNumber)->getFont()->setBold(true);
    $sheet->getStyle('F' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $rowNumber++;
}

$sheet->getStyle('A5:H' . max(5, $rowNumber - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$sheet->getStyle('A5:H' . max(5, $rowNumber - 1))->getBorders()->getAllBorders()->setBorderStyle('thin');
foreach (['A' => 28, 'B' => 20, 'C' => 14, 'D' => 14, 'E' => 10, 'F' => 18, 'G' => 16, 'H' => 45] as $column => $width) {
    $sheet->getColumnDimension($column)->setWidth($width);
}
$sheet->getStyle('H6:H' . max(6, $rowNumber - 1))->getAlignment()->setWrapText(true);

$filename = 'Payment_Voucher_List_' . preg_replace('/[^A-Za-z0-9]+/', '_', $header['pl_number']) . '.xlsx';
while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
