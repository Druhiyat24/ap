<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

define('ROW_TOLERANCE', 0.5);
define('MATERIALITY_THRESHOLD', 1.0);

try {
    session_start();
    include '../../conn/conn.php';

    $current_user = $_SESSION['username'] ?? '';
    if ($current_user !== 'indro') {
        http_response_code(403);
        echo 'Access restricted.';
        exit;
    }

    $start_date = trim($_GET['start_date'] ?? '');
    $end_date   = trim($_GET['end_date'] ?? '');
    $diagnosis_filter = trim($_GET['diagnosis'] ?? '');
    $search = trim($_GET['search'] ?? '');

    $where = "status != 'Cancel'";
    if ($start_date !== '' && $end_date !== '') {
        $sd = mysqli_real_escape_string($conn2, $start_date);
        $ed = mysqli_real_escape_string($conn2, $end_date);
        $where .= " AND tgl_journal BETWEEN '$sd' AND '$ed'";
    }

    $sql = "
        SELECT
            no_journal,
            MIN(tgl_journal) tgl_journal,
            GROUP_CONCAT(DISTINCT type_journal SEPARATOR ', ') types,
            GROUP_CONCAT(DISTINCT status SEPARATOR ', ') statuses,
            GROUP_CONCAT(DISTINCT curr SEPARATOR ',') currs,
            COUNT(*) line_count,
            SUM(debit_idr) tot_debit_idr,
            SUM(credit_idr) tot_credit_idr,
            ROUND(SUM(debit_idr) - SUM(credit_idr), 2) selisih,
            SUM(CASE WHEN ABS(ROUND(IF(curr='IDR', debit, debit*rate),2) - debit_idr) > " . ROW_TOLERANCE . " THEN 1 ELSE 0 END) bad_debit_rows,
            SUM(CASE WHEN ABS(ROUND(IF(curr='IDR', credit, credit*rate),2) - credit_idr) > " . ROW_TOLERANCE . " THEN 1 ELSE 0 END) bad_credit_rows
        FROM tbl_list_journal
        WHERE $where
        GROUP BY no_journal
        HAVING ABS(ROUND(SUM(debit_idr) - SUM(credit_idr), 2)) > " . MATERIALITY_THRESHOLD . "
        ORDER BY ABS(ROUND(SUM(debit_idr) - SUM(credit_idr), 2)) DESC
    ";
    $res = mysqli_query($conn2, $sql);
    if (!$res) throw new Exception(mysqli_error($conn2));

    $rows = [];
    $needCurrencyCheck = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[$r['no_journal']] = $r;
        if ($r['bad_debit_rows'] == 0 && $r['bad_credit_rows'] == 0) {
            $needCurrencyCheck[] = $r['no_journal'];
        }
    }

    $incompleteSet = [];
    if (count($needCurrencyCheck)) {
        foreach (array_chunk($needCurrencyCheck, 500) as $chunk) {
            $esc = array_map(function ($v) use ($conn2) { return "'" . mysqli_real_escape_string($conn2, $v) . "'"; }, $chunk);
            $csql = "
                SELECT no_journal FROM (
                    SELECT no_journal, curr, SUM(debit) d, SUM(credit) c
                    FROM tbl_list_journal
                    WHERE status != 'Cancel' AND no_journal IN (" . implode(',', $esc) . ")
                    GROUP BY no_journal, curr
                ) x
                GROUP BY no_journal
                HAVING SUM(ABS(d - c) > 0.01) > 0
            ";
            $cres = mysqli_query($conn2, $csql);
            while ($crow = mysqli_fetch_assoc($cres)) $incompleteSet[$crow['no_journal']] = true;
        }
    }

    $data = [];
    foreach ($rows as $no => $r) {
        if ($r['bad_debit_rows'] > 0 || $r['bad_credit_rows'] > 0) {
            $r['diagnosis'] = 'salah_kurs_konversi';
            $r['diagnosis_label'] = 'Salah Kurs (Konversi Baris)';
        } elseif (isset($incompleteSet[$no])) {
            $r['diagnosis'] = 'tidak_lengkap';
            $r['diagnosis_label'] = 'Jurnal Tidak Lengkap';
        } else {
            $r['diagnosis'] = 'salah_kurs_rate';
            $r['diagnosis_label'] = 'Salah Kurs (Rate Tidak Konsisten)';
        }
        if ($diagnosis_filter !== '' && $r['diagnosis'] !== $diagnosis_filter) continue;
        if ($search !== '' && stripos($no, $search) === false && stripos($r['types'], $search) === false) continue;
        $data[] = $r;
    }

    // ================= BUILD SPREADSHEET =================
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Journal Balance Tracker');

    $style = function ($range, $opts = []) use ($sheet) {
        $arr = [];
        if (!empty($opts['bold'])) $arr['font']['bold'] = true;
        if (!empty($opts['size'])) $arr['font']['size'] = $opts['size'];
        if (!empty($opts['color'])) $arr['font']['color'] = ['rgb' => $opts['color']];
        if (!empty($opts['fill'])) {
            $arr['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $opts['fill']]];
        }
        if (!empty($opts['align'])) $arr['alignment']['horizontal'] = $opts['align'];
        if (!empty($opts['valign'])) $arr['alignment']['vertical'] = $opts['valign'];
        if (!empty($opts['wrap'])) $arr['alignment']['wrapText'] = true;
        if (!empty($opts['border'])) $arr['borders']['allBorders'] = ['borderStyle' => Border::BORDER_THIN];
        if ($arr) $sheet->getStyle($range)->applyFromArray($arr);
    };

    $cols = ['A','B','C','D','E','F','G','H','I','J'];
    $widths = [22, 12, 22, 12, 8, 6, 18, 18, 14, 26];
    foreach ($cols as $i => $c) $sheet->getColumnDimension($c)->setWidth($widths[$i]);

    $sheet->setCellValue('A1', 'JOURNAL BALANCE TRACKER');
    $style('A1', ['bold' => true, 'size' => 15]);
    $period = ($start_date !== '' && $end_date !== '') ? "$start_date s/d $end_date" : 'Semua Periode';
    $sheet->setCellValue('A2', 'Periode: ' . $period . '   |   Dicetak: ' . date('Y-m-d H:i'));
    $style('A2', ['size' => 10, 'color' => '667788']);
    $sheet->setCellValue('A3', 'Total jurnal tidak balance: ' . count($data));
    $style('A3', ['size' => 10, 'bold' => true]);

    $headerRow = 5;
    $headers = ['No. Journal', 'Tanggal', 'Tipe Journal', 'Status', 'Curr', 'Baris', 'Debit IDR', 'Credit IDR', 'Selisih', 'Diagnosis'];
    foreach ($headers as $i => $h) $sheet->setCellValue($cols[$i] . $headerRow, $h);
    $style('A' . $headerRow . ':J' . $headerRow, ['bold' => true, 'fill' => '1B2350', 'color' => 'FFFFFF', 'align' => Alignment::HORIZONTAL_CENTER, 'border' => true]);

    $r = $headerRow + 1;
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $r, $row['no_journal']);
        $sheet->setCellValue('B' . $r, $row['tgl_journal']);
        $sheet->setCellValue('C' . $r, $row['types']);
        $sheet->setCellValue('D' . $r, $row['statuses']);
        $sheet->setCellValue('E' . $r, $row['currs']);
        $sheet->setCellValue('F' . $r, (int)$row['line_count']);
        $sheet->setCellValue('G' . $r, (float)$row['tot_debit_idr']);
        $sheet->setCellValue('H' . $r, (float)$row['tot_credit_idr']);
        $sheet->setCellValue('I' . $r, (float)$row['selisih']);
        $sheet->setCellValue('J' . $r, $row['diagnosis_label']);
        $sheet->getStyle('G' . $r . ':I' . $r)->getNumberFormat()->setFormatCode('#,##0.00');
        $fillColor = $row['diagnosis'] === 'salah_kurs_konversi' ? 'FDECEC' : ($row['diagnosis'] === 'tidak_lengkap' ? 'FDF3E3' : 'F1EAFD');
        $style('J' . $r, ['fill' => $fillColor]);
        $style('A' . $r . ':J' . $r, ['border' => true]);
        $r++;
    }

    $style('A' . $headerRow . ':J' . ($r - 1), ['border' => true]);
    $sheet->freezePane('A' . ($headerRow + 1));

    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Journal Balance Tracker - ' . date('Ymd_His') . '.xlsx"');
    header('Cache-Control: max-age=0');
    IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
    exit;
} catch (\Throwable $e) {
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: text/plain');
    echo 'Export gagal: ' . $e->getMessage();
    exit;
}
