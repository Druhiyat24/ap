<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

try {
    include '../../conn/conn.php';
    require __DIR__ . '/cfr_functions.php';

    $start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? date('Y-m-d', strtotime($_GET['start_date'])) : date('Y-m-01');
    $end_date   = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? date('Y-m-d', strtotime($_GET['end_date'])) : date('Y-m-d');
    // Sama seperti report-cashflow-realisation.php - filter kolom akun yang
    // diekspor, kosong berarti semua akun.
    $selectedAccounts = isset($_GET['accounts']) ? (array) $_GET['accounts'] : [];

    extract(cfrComputeReportData($conn2, $start_date, $end_date, $selectedAccounts));

    $GREEN = '90EE90';
    $BLUE = '0070C0';
    $RED = 'FF0000';
    $NUMFMT = '#,##0.00;(#,##0.00);"-"';

    // Layout kolom - hirarki Descriptions dipecah jadi 3 kolom (bukan 1 kolom lebar):
    // A = nomor section utama ("1.") / label baris polos (SALDO AWAL, TOTAL ..., dst),
    // B = teks section utama ("OPERATING ACTIVITIES :") / nomor item detail ("1","98",dst),
    // C = teks item detail ("Penerimaan Piutang Usaha ..."),
    // D/E/F = Projection/Realisation/Variance, G kosong (spacer), H dst = per akun.
    $COL_A = 1;
    $COL_B = 2;
    $COL_C = 3;
    $COL_PROJ = 4;
    $COL_REAL = 5;
    $COL_VAR = 6;
    $COL_SPACER = 7;
    $COL_ACC_START = 8;
    $lastCol = $COL_ACC_START - 1 + $colspanAccounts;
    $lastColLetter = Coordinate::stringFromColumnIndex($lastCol);
    $colA = Coordinate::stringFromColumnIndex($COL_A);
    $colB = Coordinate::stringFromColumnIndex($COL_B);
    $colC = Coordinate::stringFromColumnIndex($COL_C);
    $projColLetter = Coordinate::stringFromColumnIndex($COL_PROJ);
    $realColLetter = Coordinate::stringFromColumnIndex($COL_REAL);
    $varColLetter = Coordinate::stringFromColumnIndex($COL_VAR);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Cash Flow Realisation');

    // ================= JUDUL =================
    $sheet->setCellValue('A1', 'PT NIRWANA ALABARE GARMENT');
    $sheet->setCellValue('A2', 'CASH FLOW REALISATION');
    $sheet->setCellValue('A3', 'FOR PERIOD ' . strtoupper(date('d M Y', strtotime($start_date))) . ' - ' . strtoupper(date('d M Y', strtotime($end_date))));
    $sheet->getStyle('A1:A3')->getFont()->setBold(true);

    // ================= HEADER TABEL =================
    $headerRow1 = 5;
    $headerRow2 = 6;
    // Sengaja TIDAK di-merge (DESCRIPTIONS/PROJECTION/REALISATION/VARIANCE) - sama
    // seperti REALISATION BY BANK, teksnya ditaruh di baris atas (row5) saja, posisi
    // kolom tetap sama seperti sebelumnya.
    $sheet->setCellValue($colA . $headerRow1, 'DESCRIPTIONS');
    $sheet->setCellValue($projColLetter . $headerRow1, 'PROJECTION');
    $sheet->setCellValue($realColLetter . $headerRow1, 'REALISATION');
    $sheet->setCellValue($varColLetter . $headerRow1, 'VARIANCE');
    if ($colspanAccounts > 0) {
        $bankStartCol = Coordinate::stringFromColumnIndex($COL_ACC_START);
        // Sengaja TIDAK di-merge (beda dari header lain) - teks dibiarkan overflow
        // secara visual ke kolom-kolom kosong di sebelah kanan, sama seperti judul
        // di baris paling atas (A1:A3), posisi teksnya tetap di kolom pertama akun.
        $sheet->setCellValue($bankStartCol . $headerRow1, 'REALISATION BY BANK');
    }
    $colIdx = $COL_ACC_START;
    foreach ($accounts as $acc) {
        $col = Coordinate::stringFromColumnIndex($colIdx);
        $sheet->setCellValue($col . $headerRow2, $acc['label']);
        $colIdx++;
    }
    $sheet->getStyle($colA . $headerRow1 . ':' . $lastColLetter . $headerRow2)
        ->getFont()->setBold(true);
    $sheet->getStyle($colA . $headerRow1 . ':' . $lastColLetter . $headerRow2)
        ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($GREEN);
    $sheet->getStyle($colA . $headerRow1 . ':' . $lastColLetter . $headerRow2)
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_BOTTOM);

    $row = $headerRow2 + 1;

    // Tulis angka Projection(kosong)/Realisation/Variance(kosong) + 1 angka per akun
    // di baris $atRow - dipakai oleh semua jenis baris (plain/detail/subtotal).
    $writeNumbers = function ($atRow, $realisation, array $perAccount) use ($sheet, $accounts, $NUMFMT, $realColLetter, $lastColLetter, $projColLetter, $COL_ACC_START) {
        $sheet->setCellValue($realColLetter . $atRow, round((float) $realisation, 2));
        $colIdx = $COL_ACC_START;
        foreach ($accounts as $acc) {
            $col = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($col . $atRow, round((float) ($perAccount[$acc['account']] ?? 0), 2));
            $colIdx++;
        }
        $sheet->getStyle($projColLetter . $atRow . ':' . $lastColLetter . $atRow)->getNumberFormat()->setFormatCode($NUMFMT);
        $sheet->getStyle($projColLetter . $atRow . ':' . $lastColLetter . $atRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    };

    // Baris label POLOS (tanpa nomor) - dipakai untuk SALDO AWAL/AKHIR, TOTAL ... .
    // Labelnya ditulis di kolom A saja (tanpa split), boleh overflow visual ke B/C
    // karena tidak ada border grid yang memotongnya.
    $writePlainRow = function ($label, $realisation, array $perAccount, $bold = false) use ($sheet, &$row, $colA, $lastColLetter, $writeNumbers) {
        $sheet->setCellValue($colA . $row, $label);
        $writeNumbers($row, $realisation, $perAccount);
        if ($bold) {
            $sheet->getStyle($colA . $row . ':' . $lastColLetter . $row)->getFont()->setBold(true);
        }
        $r = $row;
        $row++;
        return $r;
    };

    // Baris ITEM DETAIL - nomornya di kolom B, teksnya di kolom C.
    $writeDetailRow = function ($num, $text, $realisation, array $perAccount) use ($sheet, &$row, $colB, $colC, $writeNumbers) {
        if ($num !== '') {
            $sheet->setCellValue($colB . $row, $num);
        }
        $sheet->setCellValue($colC . $row, $text);
        $writeNumbers($row, $realisation, $perAccount);
        $r = $row;
        $row++;
        return $r;
    };

    // Baris judul section TANPA nomor ("CASH RECEIPTS :", "CASH DISBURSEMENT:",
    // "SUBTOTAL ACTIVITIES") - teks di kolom A saja, bold + underline opsional + warna.
    $sectionTitle = function ($label, $color = null, $underline = true) use ($sheet, &$row, $colA) {
        $sheet->setCellValue($colA . $row, $label);
        $sheet->getStyle($colA . $row)->getFont()->setBold(true)->setUnderline($underline);
        if ($color) {
            $sheet->getStyle($colA . $row)->getFont()->getColor()->setRGB($color);
        }
        $row++;
    };

    // Baris judul section BERNOMOR ("1.  OPERATING ACTIVITIES :") - nomornya di
    // kolom A, teksnya di kolom B, keduanya bold + underline.
    $sectionTitleNumbered = function ($num, $label) use ($sheet, &$row, $colA, $colB) {
        // Dipaksa string - "1." dkk kalau dilewatkan ke setCellValue biasa dianggap
        // numerik (is_numeric("1.") === true) dan titiknya hilang saat ditampilkan.
        $sheet->setCellValueExplicit($colA . $row, $num . '.', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue($colB . $row, $label);
        $sheet->getStyle($colA . $row)->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle($colA . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($colB . $row)->getFont()->setBold(true)->setUnderline(true);
        $row++;
    };

    // Baris "TOTAL ... FROM x ACTIVITIES" - bold + garis bawah (border-bottom) tipis
    // membentang di seluruh kolom, meniru pemisah horizontal di laporan keuangan.
    $totalRuleUnder = function ($atRow) use ($sheet, $colA, $lastColLetter) {
        $sheet->getStyle($colA . $atRow . ':' . $lastColLetter . $atRow)
            ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));
    };

    // ================= SALDO AWAL =================
    $writePlainRow('SALDO AWAL KAS DAN BANK', $totalBegin, $beginBalance, true);
    $row++; // baris kosong

    // ================= CASH RECEIPTS =================
    $sectionTitle('CASH RECEIPTS :', $BLUE);

    $grandReceiptTotal = 0;
    $subtotalReceiptByAct = [];
    foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
        $rows = $categories['Cash In'][$act];
        if (empty($rows)) continue;
        $sectionTitleNumbered($actNo, $act . ' :');
        $actTotal = 0;
        $accTotals = [];
        foreach ($accounts as $acc) { $accTotals[$acc['account']] = 0; }
        foreach ($rows as $catRow) {
            $total = cfrRowTotal($realisasi, $catRow['id'], true);
            $actTotal += $total;
            $perAccount = [];
            foreach ($accounts as $acc) {
                $v = cfrRowValue($realisasi, $catRow['id'], $acc['account'], true);
                $perAccount[$acc['account']] = $v;
                $accTotals[$acc['account']] += $v;
            }
            $writeDetailRow($catRow['display_seq'], $catRow['nama_subcategory'], $total, $perAccount);
        }
        $row++;
        $tr = $writePlainRow('TOTAL CASH RECEIPT FROM ' . $act, $actTotal, $accTotals, true);
        $totalRuleUnder($tr);
        $row++;
        $subtotalReceiptByAct[$act] = $actTotal;
        $grandReceiptTotal += $actTotal;
    }

    $grandReceiptByAcc = [];
    foreach ($accounts as $acc) {
        $t = 0;
        foreach ($categories['Cash In'] as $rows) {
            foreach ($rows as $catRow) {
                $t += cfrRowValue($realisasi, $catRow['id'], $acc['account'], true);
            }
        }
        $grandReceiptByAcc[$acc['account']] = $t;
    }
    $tr = $writePlainRow('TOTAL CASH RECEIPTS', $grandReceiptTotal, $grandReceiptByAcc, true);
    $sheet->getStyle($colA . $tr)->getFont()->getColor()->setRGB($BLUE);
    $row++;

    // ================= CASH DISBURSEMENT =================
    $sectionTitle('CASH DISBURSEMENT:', $RED);

    $grandDisbTotal = 0;
    $subtotalDisbByAct = [];
    foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
        $rows = $categories['Cash Out'][$act];
        if (empty($rows)) continue;
        $sectionTitleNumbered($actNo, $act . ':');
        $actTotal = 0;
        $accTotals = [];
        foreach ($accounts as $acc) { $accTotals[$acc['account']] = 0; }
        foreach ($rows as $catRow) {
            $total = cfrRowTotal($realisasi, $catRow['id'], false);
            $actTotal += $total;
            $perAccount = [];
            foreach ($accounts as $acc) {
                $v = cfrRowValue($realisasi, $catRow['id'], $acc['account'], false);
                $perAccount[$acc['account']] = $v;
                $accTotals[$acc['account']] += $v;
            }
            $writeDetailRow($catRow['display_seq'], $catRow['nama_subcategory'], $total, $perAccount);
        }
        $row++;
        $tr2 = $writePlainRow('TOTAL CASH DISBURSEMENT FROM ' . $act, $actTotal, $accTotals, true);
        $totalRuleUnder($tr2);
        $row++;
        $subtotalDisbByAct[$act] = $actTotal;
        $grandDisbTotal += $actTotal;
    }

    $grandDisbByAcc = [];
    foreach ($accounts as $acc) {
        $t = 0;
        foreach ($categories['Cash Out'] as $rows) {
            foreach ($rows as $catRow) {
                $t += cfrRowValue($realisasi, $catRow['id'], $acc['account'], false);
            }
        }
        $grandDisbByAcc[$acc['account']] = $t;
    }
    $tr2 = $writePlainRow('TOTAL CASH DISBURSEMENT', $grandDisbTotal, $grandDisbByAcc, true);
    $sheet->getStyle($colA . $tr2)->getFont()->getColor()->setRGB($RED);
    $row++;

    // ================= SUBTOTAL ACTIVITIES =================
    $sectionTitle('SUBTOTAL ACTIVITIES', null, false);
    foreach (['OPERATING ACTIVITIES', 'INVESTING ACTIVITIES', 'FINANCING ACTIVITIES'] as $act) {
        $subTotal = ($subtotalReceiptByAct[$act] ?? 0) + ($subtotalDisbByAct[$act] ?? 0);
        $perAccount = [];
        foreach ($accounts as $acc) {
            $t = 0;
            foreach (($categories['Cash In'][$act] ?? []) as $catRow) {
                $t += cfrRowValue($realisasi, $catRow['id'], $acc['account'], true);
            }
            foreach (($categories['Cash Out'][$act] ?? []) as $catRow) {
                $t += cfrRowValue($realisasi, $catRow['id'], $acc['account'], false);
            }
            $perAccount[$acc['account']] = $t;
        }
        $writeDetailRow('', $act, $subTotal, $perAccount);
    }
    $row++;

    // ================= SALDO AKHIR =================
    $writePlainRow('SALDO AKHIR KAS DAN BANK', $totalEnd, $endBalance, true);

    $lastRow = $row - 1;

    // ================= FORMAT UMUM =================
    $sheet->getColumnDimension($colA)->setWidth(4);
    $sheet->getColumnDimension($colB)->setWidth(6);
    $sheet->getColumnDimension($colC)->setWidth(36);
    $sheet->getColumnDimension($projColLetter)->setWidth(16);
    $sheet->getColumnDimension($realColLetter)->setWidth(16);
    $sheet->getColumnDimension($varColLetter)->setWidth(16);
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($COL_SPACER))->setWidth(3);
    for ($c = $COL_ACC_START; $c <= $lastCol; $c++) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(18);
    }
    $sheet->getDefaultRowDimension()->setRowHeight(15);

    // Freeze kolom Descriptions saja (A:C) + baris header (1:6), supaya tetap
    // kelihatan pas scroll ke samping - Projection/Realisation/Variance/akun ikut scroll.
    $sheet->freezePane($projColLetter . ($headerRow2 + 1));

    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Report Cash Flow Realisation.xlsx"');
    header('Cache-Control: max-age=0');
    IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
    exit;
} catch (\Throwable $e) {
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Export gagal: ' . $e->getMessage()]);
    exit;
}
