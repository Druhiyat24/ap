<?php
// Export Kontrabon ke Excel, mengikuti filter aktif (Supplier/Status/From/To via GET).
// 1 SHEET saja: DETAIL lengkap - 1 baris per BPB, dengan info kontrabon -> invoice ->
// faktur -> BPB (LEFT JOIN, baris tetap ada walau belum ada faktur/BPB).
// Tanpa latar warna (plain): header hanya tebal + garis, teks hitam.
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

try {
    session_start();
    include '../../conn/conn.php';
    date_default_timezone_set('Asia/Jakarta');

    $fSupp   = $_GET['nama_supp'] ?? 'ALL';
    $fStatus = $_GET['status'] ?? 'ALL';
    $fStart  = !empty($_GET['start_date']) ? date('Y-m-d', strtotime($_GET['start_date'])) : date('Y-01-01');
    $fEnd    = !empty($_GET['end_date'])   ? date('Y-m-d', strtotime($_GET['end_date']))   : date('Y-m-d');

    // Status efektif = mirror IR (COALESCE(ir_invoice_supp_h.status, kontrabon.status)),
    // supaya filter & kolom Status sama persis dengan List.
    $eff = "COALESCE(ih.status, h.status)";

    $whereH = "WHERE h.kontrabon_date BETWEEN '" . mysqli_real_escape_string($conn2, $fStart) . "' AND '" . mysqli_real_escape_string($conn2, $fEnd) . "'";
    if ($fSupp !== 'ALL' && $fSupp !== '')     $whereH .= " AND h.nama_supp = '" . mysqli_real_escape_string($conn2, $fSupp) . "'";
    if ($fStatus !== 'ALL' && $fStatus !== '') $whereH .= " AND $eff = '" . mysqli_real_escape_string($conn2, $fStatus) . "'";

    $qDet = mysqli_query($conn2, "SELECT h.doc_number, h.kontrabon_date, h.no_reff, h.nama_supp, $eff AS eff_status,
            i.no_inv, i.tgl_inv, i.amount,
            f.no_faktur, f.tgl_faktur, f.nama_supplier, f.npwp_supplier, f.dpp fdpp, f.ppn fppn, f.status_faktur,
            b.no_bpb, b.tgl_bpb, b.no_po, b.supplier bsupp, b.dpp bdpp, b.ppn bppn, b.total btotal, b.curr
        FROM ir_kontrabon_h h
        LEFT JOIN ir_invoice_supp_h ih ON ih.doc_number = h.doc_number
        LEFT JOIN ir_kontrabon_inv i    ON i.unik_code = h.unik_code
        LEFT JOIN ir_kontrabon_faktur f ON f.inv_id = i.id
        LEFT JOIN ir_kontrabon_bpb b    ON b.faktur_id = f.id
        $whereH ORDER BY h.id DESC, i.id, f.id, b.id");

    $fd  = function ($d) { return (!empty($d) && $d !== '0000-00-00') ? date('d-M-Y', strtotime($d)) : ''; };
    $S   = function ($ws, $range, array $arr) { $ws->getStyle($range)->applyFromArray($arr); };
    $NF  = function ($ws, $range) { $ws->getStyle($range)->getNumberFormat()->setFormatCode('#,##0.00'); };
    $thin = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];
    $CEN  = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]];
    $RIGHT = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]];

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
    $LEFT = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]];

    /* ================= SHEET TUNGGAL: DETAIL ================= */
    $ds = $spreadsheet->getActiveSheet();
    $ds->setTitle('Invoice Received');
    $dw = ['A' => 22, 'B' => 16, 'C' => 11, 'D' => 28, 'E' => 14, 'F' => 16, 'G' => 13, 'H' => 16,
           'I' => 20, 'J' => 13, 'K' => 24, 'L' => 20, 'M' => 15, 'N' => 14, 'O' => 18, 'P' => 12,
           'Q' => 20, 'R' => 26, 'S' => 15, 'T' => 14, 'U' => 16, 'V' => 7];
    foreach ($dw as $c => $w) $ds->getColumnDimension($c)->setWidth($w);

    // Judul rata KIRI, TANPA merge (meniru format export Bank Out).
    $ds->setCellValue('A1', 'DATA INVOICE RECEIVED');
    $S($ds, 'A1', ['font' => ['bold' => true, 'size' => 14]] + $LEFT);
    $ds->setCellValue('A2', 'PERIODE ' . date('d F Y', strtotime($fStart)) . ' - ' . date('d F Y', strtotime($fEnd)));
    $S($ds, 'A2', ['font' => ['bold' => true, 'size' => 11]] + $LEFT);
    $ds->setCellValue('A3', 'Supplier: ' . ($fSupp === 'ALL' || $fSupp === '' ? 'ALL' : $fSupp)
        . '     Status: ' . ($fStatus === 'ALL' || $fStatus === '' ? 'ALL' : $fStatus));
    $S($ds, 'A3', ['font' => ['italic' => true, 'size' => 10]] + $LEFT);

    // Header kolom: 1 baris saja (tanpa baris grup) - tebal + garis, tanpa latar.
    $hh = 5;
    $cols = ['Document Number', 'Invoice Received Date', 'No Reff', 'Supplier', 'Status',
             'No Invoice', 'Invoice Date', 'Invoice Amount',
             'No Faktur', 'Tgl Faktur', 'Faktur Supplier', 'NPWP Supplier', 'DPP', 'PPN',
             'No BPB', 'Tgl BPB', 'No PO', 'BPB Supplier', 'DPP', 'PPN', 'Total', 'Curr'];
    $c = 'A'; foreach ($cols as $t) { $ds->setCellValue($c . $hh, $t); $c++; }
    $S($ds, "A$hh:V$hh", ['font' => ['bold' => true], 'alignment' => $CEN['alignment'] + ['wrapText' => false]] + $thin);
    $ds->getRowDimension($hh)->setRowHeight(18);

    $r = $hh + 1;
    while ($row = mysqli_fetch_assoc($qDet)) {
        $invOk = $row['no_inv'] !== null; $fakOk = $row['no_faktur'] !== null; $bpbOk = $row['no_bpb'] !== null;
        $ds->setCellValue("A$r", $row['doc_number']);
        $ds->setCellValue("B$r", $fd($row['kontrabon_date']));
        $ds->setCellValue("C$r", $row['no_reff']);
        $ds->setCellValue("D$r", $row['nama_supp']);
        $ds->setCellValue("E$r", $row['eff_status']);
        if ($invOk) {
            $ds->setCellValue("F$r", $row['no_inv']);
            $ds->setCellValue("G$r", $fd($row['tgl_inv']));
            $ds->setCellValue("H$r", (float) $row['amount']);
        }
        if ($fakOk) {
            $ds->setCellValue("I$r", $row['no_faktur']);
            $ds->setCellValue("J$r", $fd($row['tgl_faktur']));
            $ds->setCellValue("K$r", $row['nama_supplier']);
            $ds->setCellValue("L$r", $row['npwp_supplier']);
            $ds->setCellValue("M$r", (float) $row['fdpp']);
            $ds->setCellValue("N$r", (float) $row['fppn']);
        }
        if ($bpbOk) {
            $ds->setCellValue("O$r", $row['no_bpb']);
            $ds->setCellValue("P$r", $fd($row['tgl_bpb']));
            $ds->setCellValue("Q$r", $row['no_po']);
            $ds->setCellValue("R$r", $row['bsupp']);
            $ds->setCellValue("S$r", (float) $row['bdpp']);
            $ds->setCellValue("T$r", (float) $row['bppn']);
            $ds->setCellValue("U$r", (float) $row['btotal']);
            $ds->setCellValue("V$r", $row['curr']);
        }
        $r++;
    }
    $dlast = $r - 1;
    if ($dlast >= $hh + 1) {
        $S($ds, "A" . ($hh + 1) . ":V$dlast", ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]] + $thin);
        // center: C(No Reff), E(Status), G(Inv Date), J(Tgl Faktur), P(Tgl BPB), V(Curr)
        foreach (['C', 'E', 'G', 'J', 'P', 'V'] as $cc) $S($ds, "{$cc}" . ($hh + 1) . ":{$cc}$dlast", $CEN);
        // number + right: H, M, N, S, T, U
        foreach (['H', 'M', 'N', 'S', 'T', 'U'] as $cc) { $NF($ds, "{$cc}" . ($hh + 1) . ":{$cc}$dlast"); $S($ds, "{$cc}" . ($hh + 1) . ":{$cc}$dlast", $RIGHT); }
    } else {
        $ds->mergeCells("A$r:V$r"); $ds->setCellValue("A$r", 'No data for the selected filter.'); $S($ds, "A$r:V$r", $CEN + $thin);
    }
    $ds->freezePane('A' . ($hh + 1));

    $fname = 'Invoice Received ' . date('d-M-Y', strtotime($fStart)) . ' to ' . date('d-M-Y', strtotime($fEnd)) . '.xlsx';
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fname . '"');
    header('Cache-Control: max-age=0');
    IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
    exit;
} catch (\Throwable $e) {
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: text/plain');
    echo 'Export gagal: ' . $e->getMessage();
    exit;
}
