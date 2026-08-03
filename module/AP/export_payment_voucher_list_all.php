<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

include '../../conn/conn.php';

$startDateInput = trim($_GET['start_date'] ?? '');
$endDateInput = trim($_GET['end_date'] ?? '');
$status = trim($_GET['status'] ?? 'ALL');
$startDate = DateTime::createFromFormat('d-m-Y', $startDateInput);
$endDate = DateTime::createFromFormat('d-m-Y', $endDateInput);

if (!$startDate || !$endDate || $startDate->format('d-m-Y') !== $startDateInput || $endDate->format('d-m-Y') !== $endDateInput) {
    http_response_code(400);
    exit('Format tanggal harus dd-mm-yyyy.');
}

if ($startDate > $endDate) {
    http_response_code(400);
    exit('Tanggal awal tidak boleh melebihi tanggal akhir.');
}

$startDateSql = mysqli_real_escape_string($conn2, $startDate->format('Y-m-d'));
$endDateSql = mysqli_real_escape_string($conn2, $endDate->format('Y-m-d'));
$where = "h.pl_date BETWEEN '$startDateSql' AND '$endDateSql'";

if ($status !== '' && strtoupper($status) !== 'ALL') {
    $statusSql = mysqli_real_escape_string($conn2, $status);
    $where .= " AND h.status = '$statusSql'";
}

$result = mysqli_query($conn2, "SELECT h.pl_number, h.pl_date, h.status AS pl_status,
        d.type_pv, d.no_kbon, d.tgl_kbon, d.nama_supp, d.curr, d.total,
        d.deskripsi, d.profit_center
    FROM pv_payment_voucher_list_h h
    INNER JOIN pv_payment_voucher_list_det d ON d.pl_number = h.pl_number
    WHERE $where AND d.status != 'Cancel'
    ORDER BY h.pl_date DESC, h.pl_number DESC, d.no_kbon ASC");

if (!$result) {
    http_response_code(500);
    exit('Query export gagal: ' . mysqli_error($conn2));
}

function getDueDateForAllExport($conn, $typePv, $noKbon)
{
    $noKbonEsc = mysqli_real_escape_string($conn, $noKbon);

    if ($typePv === 'Biaya') {
        $dueDateResult = mysqli_query($conn, "SELECT MAX(b.due_date) AS due_date
            FROM tbl_pv_h a INNER JOIN tbl_pv b ON b.no_pv = a.no_pv
            WHERE a.no_pv = '$noKbonEsc'");
    } elseif ($typePv === 'Installment') {
        $dueDateResult = mysqli_query($conn, "SELECT tgl_tempo AS due_date
            FROM kontrabon_h_installment_detail WHERE no_kbon_det = '$noKbonEsc'");
    } else {
        $table = $typePv === 'DP' ? 'kontrabon_h_dp' : ($typePv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
        $dueDateResult = mysqli_query($conn, "SELECT tgl_tempo AS due_date FROM $table WHERE no_kbon = '$noKbonEsc'");
    }

    $dueDateRow = $dueDateResult ? mysqli_fetch_assoc($dueDateResult) : null;
    return $dueDateRow['due_date'] ?? null;
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Payment Voucher List');

$sheet->mergeCells('A1:K1');
$sheet->setCellValue('A1', 'PAYMENT VOUCHER LIST - DETAIL');
$sheet->mergeCells('A2:K2');
$sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'));
$sheet->getStyle('A1:A2')->getFont()->setBold(true);

$headers = ['No Payment Voucher List', 'PL Date', 'Status', 'Supplier', 'No PV', 'PV Date', 'Due Date', 'Curr', 'Amount', 'Profit Center', 'Description'];
$sheet->fromArray($headers, null, 'A4');
$sheet->getStyle('A4:K4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A4:K4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
$sheet->getStyle('A4:K4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$rowNumber = 5;
$grandTotals = [];
$currentPlNumber = null;
$documentStartRow = $rowNumber;
while ($row = mysqli_fetch_assoc($result)) {
    // Nomor dan tanggal dokumen hanya ditampilkan sekali di sisi kiri untuk
    // setiap kelompok detail Payment Voucher List.
    if ($currentPlNumber !== null && $currentPlNumber !== $row['pl_number']) {
        $documentEndRow = $rowNumber - 1;
        if ($documentEndRow > $documentStartRow) {
            foreach (['A', 'B', 'C'] as $column) {
                $sheet->mergeCells($column . $documentStartRow . ':' . $column . $documentEndRow);
            }
        }
        $documentStartRow = $rowNumber;
    }

    $dueDate = getDueDateForAllExport($conn2, $row['type_pv'], $row['no_kbon']);
    $amount = (float) $row['total'];
    $profitCenter = !empty($row['profit_center']) && $row['profit_center'] !== '-' ? $row['profit_center'] : '-';

    $sheet->fromArray([
        $row['pl_number'],
        !empty($row['pl_date']) ? date('d M Y', strtotime($row['pl_date'])) : '-',
        $row['pl_status'],
        $row['nama_supp'],
        $row['no_kbon'],
        !empty($row['tgl_kbon']) ? date('d M Y', strtotime($row['tgl_kbon'])) : '-',
        !empty($dueDate) ? date('d M Y', strtotime($dueDate)) : '-',
        $row['curr'],
        $amount,
        $profitCenter,
        $row['deskripsi'] ?: '-',
    ], null, 'A' . $rowNumber);
    $sheet->getStyle('I' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $grandTotals[$row['curr']] = ($grandTotals[$row['curr']] ?? 0) + $amount;
    $currentPlNumber = $row['pl_number'];
    $rowNumber++;
}

// Gabungkan juga kelompok dokumen terakhir.
if ($currentPlNumber !== null) {
    $documentEndRow = $rowNumber - 1;
    if ($documentEndRow > $documentStartRow) {
        foreach (['A', 'B', 'C'] as $column) {
            $sheet->mergeCells($column . $documentStartRow . ':' . $column . $documentEndRow);
        }
    }
}

ksort($grandTotals);
foreach ($grandTotals as $currency => $total) {
    $sheet->mergeCells('A' . $rowNumber . ':G' . $rowNumber);
    $sheet->setCellValue('A' . $rowNumber, 'Grand Total ' . $currency);
    $sheet->setCellValue('H' . $rowNumber, $currency);
    $sheet->setCellValue('I' . $rowNumber, $total);
    $sheet->getStyle('A' . $rowNumber . ':K' . $rowNumber)->getFont()->setBold(true);
    $sheet->getStyle('I' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $rowNumber++;
}

$lastRow = max(4, $rowNumber - 1);
$sheet->getStyle('A4:K' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$sheet->getStyle('A4:K' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->getStyle('K5:K' . max(5, $lastRow))->getAlignment()->setWrapText(true);
foreach (['A' => 26, 'B' => 14, 'C' => 12, 'D' => 28, 'E' => 20, 'F' => 14, 'G' => 14, 'H' => 10, 'I' => 18, 'J' => 16, 'K' => 45] as $column => $width) {
    $sheet->getColumnDimension($column)->setWidth($width);
}

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Payment_Voucher_List_All_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx"');
header('Cache-Control: max-age=0');

IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
