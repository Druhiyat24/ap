<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

try {
    session_start();
    include '../../conn/conn.php';

    $current_user = $_SESSION['username'] ?? '';
    if ($current_user !== 'indro') {
        http_response_code(403);
        echo 'Access restricted.';
        exit;
    }

    $filter_status = $_GET['status'] ?? '';
    $filter_module = $_GET['module'] ?? '';
    $filter_month  = $_GET['month']  ?? '';   // YYYY-MM
    $filter_search = $_GET['search'] ?? '';

    // Excel HANYA menampilkan project yang sudah DONE (per permintaan user).
    $where = "WHERE status = 'Done'";
    if ($filter_module !== '') {
        $fm = mysqli_real_escape_string($conn2, $filter_module);
        $where .= " AND category = '$fm'";
    }
    if ($filter_month !== '') {
        // DONE muncul di bulan SELESAI-nya (actual_date), bukan bulan target.
        // Project target Agustus tapi selesai September -> hanya muncul di export September.
        $fmo = mysqli_real_escape_string($conn2, $filter_month);
        $where .= " AND DATE_FORMAT(COALESCE(actual_date, target_date), '%Y-%m') = '$fmo'";
    }
    if ($filter_search !== '') {
        $fse = mysqli_real_escape_string($conn2, $filter_search);
        $where .= " AND project_name LIKE '%$fse%'";
    }

    $q = mysqli_query($conn2,
        "SELECT * FROM master_project
         $where
         ORDER BY (target_date IS NULL), target_date ASC, id DESC"
    );
    $rows = [];
    $categories = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
        if (!empty($r['category']) && !in_array($r['category'], $categories)) $categories[] = $r['category'];
    }

    $bulan_id = ['', 'JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];
    if ($filter_month !== '') {
        list($py, $pm) = explode('-', $filter_month);
        $periode = $bulan_id[(int)$pm] . ' ' . $py;
    } else {
        $periode = 'SEMUA PERIODE';
    }

    // NAMA PROJECT: tampilkan SEMUA modul (bukan hanya yang terfilter) dalam urutan
    // tetap seperti template -> AP - AR - SIGNALBIT - NDS (modul lain diappend).
    $allCats = [];
    $cq = mysqli_query($conn2, "SELECT DISTINCT category FROM master_project WHERE category IS NOT NULL AND category <> ''");
    while ($cr = mysqli_fetch_assoc($cq)) $allCats[] = strtoupper($cr['category']);
    $catOrder = ['AP', 'AR', 'SIGNALBIT', 'NDS'];
    usort($allCats, function ($a, $b) use ($catOrder) {
        $ia = array_search($a, $catOrder); $ib = array_search($b, $catOrder);
        if ($ia === false) $ia = 999;
        if ($ib === false) $ib = 999;
        return $ia === $ib ? strcmp($a, $b) : $ia - $ib;
    });
    $nama_project  = count($allCats) ? 'ACCOUNTING (' . implode(' - ', $allCats) . ')' : 'ACCOUNTING';
    $programmer    = 'Dede Ruhiyat';
    $sistem_analis = '-';
    $user_dept     = 'Accounting';
    $signer_name   = 'DEDE RUHIYAT';

    function fmt_tgl($d) {
        if (!$d) return '';
        return date('d/m/Y', strtotime($d));
    }
    function map_status($s) {
        if ($s === 'Done') return 'DONE';
        if ($s === 'On Progress') return 'ON PROGRESS';
        return 'PENDING'; // Planned / On Hold
    }

    $GREEN  = 'C6E0B4';
    $BLUE   = 'BDD7EE';
    $ORANGE = 'FFC000';
    $GREY   = 'F2F2F2';

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10); // seluruh tabel Arial 10
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Progress Project IT');

    // ===== Column widths (A..P = 16 columns) =====
    $widths = ['A'=>5,'B'=>16,'C'=>20,'D'=>38,'E'=>11,'F'=>11,'G'=>11,'H'=>11,'I'=>8,'J'=>11,'K'=>11,'L'=>8,'M'=>16,'N'=>13,'O'=>9,'P'=>9];
    foreach ($widths as $col => $w) { $sheet->getColumnDimension($col)->setWidth($w); }

    // Applies every requested property to a range in ONE pass via applyFromArray()
    // (a single, complete style array) rather than chained getFont()/getFill()
    // setter calls — chaining setters across many different ranges in sequence was
    // scrambling which cell got which fill/bold in this PhpSpreadsheet version.
    $style = function ($range, array $opts = []) use ($sheet) {
        $arr = [];
        if (!empty($opts['bold']) || !empty($opts['size'])) {
            $arr['font'] = [];
            if (!empty($opts['bold'])) $arr['font']['bold'] = true;
            if (!empty($opts['size'])) $arr['font']['size'] = $opts['size'];
        }
        if (isset($opts['fill'])) {
            $arr['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $opts['fill']]];
        }
        if (!empty($opts['hcenter']) || !empty($opts['hright']) || !empty($opts['vcenter']) || !empty($opts['vtop']) || !empty($opts['wrap'])) {
            $arr['alignment'] = [];
            if (!empty($opts['hcenter'])) $arr['alignment']['horizontal'] = Alignment::HORIZONTAL_CENTER;
            if (!empty($opts['hright']))  $arr['alignment']['horizontal'] = Alignment::HORIZONTAL_RIGHT;
            if (!empty($opts['vcenter'])) $arr['alignment']['vertical']   = Alignment::VERTICAL_CENTER;
            if (!empty($opts['vtop']))    $arr['alignment']['vertical']   = Alignment::VERTICAL_TOP;
            if (!empty($opts['wrap']))    $arr['alignment']['wrapText']   = true;
        }
        if (!empty($opts['border'])) {
            $arr['borders'] = ['allBorders' => ['borderStyle' => Border::BORDER_THIN]];
        }
        if (!empty($arr)) $sheet->getStyle($range)->applyFromArray($arr);
    };

    // ================= HEADER BLOCK (rows 1-8) =================
    $sheet->mergeCells('A1:P1');
    $sheet->setCellValue('A1', 'PROGRESS PROJECT IT');
    $style('A1:P1', ['bold' => true, 'size' => 16, 'hcenter' => true, 'vcenter' => true, 'border' => true]);
    $sheet->getRowDimension(1)->setRowHeight(24);

    $labels = [
        2 => ['DIVISI :', 'NAG'],
        3 => ['PERIODE :', $periode],
        4 => ['NAMA PROJECT :', $nama_project],
        5 => ['DESCRIPTION :', 'Program Aplikasi Finance untuk pengelolaan Hutang, Piutang'],
        6 => ['PROGRAMMER :', $programmer],
        7 => ['SISTEM ANALIS :', $sistem_analis],
        8 => ['USER :', $user_dept],
    ];
    foreach ($labels as $rn => $pair) {
        $sheet->mergeCells("A{$rn}:B{$rn}");
        $sheet->setCellValue("A{$rn}", $pair[0]);
        $style("A{$rn}:B{$rn}", ['bold' => true, 'fill' => 'FFFFFF', 'hright' => true, 'vcenter' => true, 'border' => true]);

        $sheet->mergeCells("C{$rn}:L{$rn}");
        $sheet->setCellValue("C{$rn}", $pair[1]);
        $style("C{$rn}:L{$rn}", ['vcenter' => true, 'border' => true]);
    }

    // "MATRIX PENILAIAN" / "PROGRES PROJECT" sit on their own rows (2 and 3),
    // aligned with DIVISI/PERIODE — NOT merged together with row 1's title.
    $sheet->mergeCells('M2:P2');
    $sheet->setCellValue('M2', 'MATRIX PENILAIAN');
    $style('M2:P2', ['bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true]);

    $sheet->mergeCells('M3:P3');
    $sheet->setCellValue('M3', 'PROGRES PROJECT');
    $style('M3:P3', ['bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true]);

    // Matrix Penilaian body sits on rows 4-7, aligned with NAMA PROJECT..SISTEM ANALIS.
    $matrix = [
        4 => ['BOBOT/NILAI', 'IT', 'SISTEM', 'USER', true],  // true = header row, all 4 cells bold
        5 => ['RINGAN', '1', '2', '4', false],
        6 => ['MENENGAH', '2', '3', '4.5', false],
        7 => ['BERAT', '3', '4', '5', false],
    ];
    foreach ($matrix as $rn => $vals) {
        $sheet->setCellValue("M{$rn}", $vals[0]);
        $sheet->setCellValue("N{$rn}", $vals[1]);
        $sheet->setCellValue("O{$rn}", $vals[2]);
        $sheet->setCellValue("P{$rn}", $vals[3]);
        if ($vals[4]) {
            $style("M{$rn}:P{$rn}", ['fill' => $BLUE, 'bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true]);
        } else {
            $style("M{$rn}", ['fill' => $BLUE, 'bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true]);
            // fill diisi eksplisit putih (bukan dibiarkan kosong) - versi PhpSpreadsheet
            // ini kadang mewarisi fill biru dari cell tetangga kalau key 'fill' tidak dikirim sama sekali.
            $style("N{$rn}", ['fill' => 'FFFFFF', 'bold' => true, 'hcenter' => true, 'border' => true]);
            $style("O{$rn}", ['fill' => 'FFFFFF', 'bold' => true, 'hcenter' => true, 'border' => true]);
            $style("P{$rn}", ['fill' => 'FFFFFF', 'bold' => true, 'hcenter' => true, 'border' => true]);
        }
    }

    // Header block (DIVISI..USER + Matrix Penilaian) -> Calibri 14, BOLD semua
    // (tabel di bawah tetap Arial 10). Title baris 1 dibiarkan (Arial 16 bold).
    $sheet->getStyle('A2:P8')->applyFromArray(['font' => ['name' => 'Calibri', 'size' => 14, 'bold' => true]]);

    // ================= MAIN TABLE HEADER (rows 10-11, after a blank spacer row 9) =================
    $h1 = 10; $h2 = 11;
    $sheet->mergeCells("A{$h1}:A{$h2}"); $sheet->setCellValue("A{$h1}", 'NO');
    $sheet->mergeCells("B{$h1}:B{$h2}"); $sheet->setCellValue("B{$h1}", 'MODUL');
    $sheet->mergeCells("C{$h1}:C{$h2}"); $sheet->setCellValue("C{$h1}", 'SUB MODUL');
    $sheet->mergeCells("D{$h1}:D{$h2}"); $sheet->setCellValue("D{$h1}", 'DETAIL');
    $sheet->mergeCells("E{$h1}:F{$h1}"); $sheet->setCellValue("E{$h1}", 'IT');
    $sheet->mergeCells("G{$h1}:I{$h1}"); $sheet->setCellValue("G{$h1}", 'SISTEM');
    $sheet->mergeCells("J{$h1}:L{$h1}"); $sheet->setCellValue("J{$h1}", 'USER');
    $sheet->mergeCells("M{$h1}:M{$h2}"); $sheet->setCellValue("M{$h1}", 'NOTES');
    $sheet->mergeCells("N{$h1}:N{$h2}"); $sheet->setCellValue("N{$h1}", "STATUS\nDONE / ON PROGRESS / PENDING");
    $sheet->mergeCells("O{$h1}:P{$h1}"); $sheet->setCellValue("O{$h1}", 'NILAI KPI *) jangan diisi');

    $sheet->setCellValue("E{$h2}", 'TARGET'); $sheet->setCellValue("F{$h2}", 'ACTUAL');
    $sheet->setCellValue("G{$h2}", 'TARGET'); $sheet->setCellValue("H{$h2}", 'ACTUAL'); $sheet->setCellValue("I{$h2}", 'PARAF');
    $sheet->setCellValue("J{$h2}", 'TARGET'); $sheet->setCellValue("K{$h2}", 'ACTUAL'); $sheet->setCellValue("L{$h2}", 'PARAF');
    $sheet->setCellValue("O{$h2}", 'BOBOT'); $sheet->setCellValue("P{$h2}", 'NILAI');

    // Thead seragam hijau semua (NO..NILAI KPI) sesuai permintaan user -
    // sebelumnya IT/SISTEM/USER biru dan NILAI KPI orange, sekarang disamakan.
    $baseHeaderOpts = ['bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true, 'fill' => $GREEN];
    $kpiHeaderOpts  = ['bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true, 'fill' => $ORANGE]; // NILAI KPI oranye (persis template)
    // One call per atomic merge/cell — see the $style() comment above for why.
    $style("A{$h1}:A{$h2}", $baseHeaderOpts);
    $style("B{$h1}:B{$h2}", $baseHeaderOpts);
    $style("C{$h1}:C{$h2}", $baseHeaderOpts);
    $style("D{$h1}:D{$h2}", $baseHeaderOpts);
    $style("E{$h1}:F{$h1}", $baseHeaderOpts);
    $style("G{$h1}:I{$h1}", $baseHeaderOpts);
    $style("J{$h1}:L{$h1}", $baseHeaderOpts);
    $style("M{$h1}:M{$h2}", $baseHeaderOpts);
    $style("N{$h1}:N{$h2}", $baseHeaderOpts + ['wrap' => true]);
    $style("O{$h1}:P{$h1}", $kpiHeaderOpts + ['wrap' => true]);
    foreach (['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $col) {
        $style("{$col}{$h2}", $baseHeaderOpts);
    }
    $style("O{$h2}", $kpiHeaderOpts);
    $style("P{$h2}", $kpiHeaderOpts);
    $sheet->getRowDimension($h1)->setRowHeight(32);
    $sheet->getRowDimension($h2)->setRowHeight(28);

    // ================= BODY =================
    $r = $h2 + 1;
    $no = 1;
    foreach ($rows as $row) {
        $sheet->setCellValue("A{$r}", $no++);
        $sheet->setCellValue("B{$r}", $row['category'] ?: '-');
        $sheet->setCellValue("C{$r}", $row['project_name']);
        $sheet->setCellValue("D{$r}", $row['description'] ?: '');
        $sheet->setCellValue("E{$r}", fmt_tgl($row['target_date']));
        $sheet->setCellValue("F{$r}", fmt_tgl($row['actual_date']));
        $sheet->setCellValue("N{$r}", map_status($row['status']));

        $style("A{$r}", ['hcenter' => true, 'vcenter' => true, 'border' => true]);
        $style("B{$r}:D{$r}", ['vcenter' => true, 'wrap' => true, 'border' => true]);
        $style("E{$r}:F{$r}", ['hcenter' => true, 'vcenter' => true, 'border' => true]);
        $style("G{$r}:M{$r}", ['vcenter' => true, 'border' => true]);
        $style("N{$r}", ['hcenter' => true, 'vcenter' => true, 'border' => true]);
        $style("O{$r}:P{$r}", ['fill' => $ORANGE, 'vcenter' => true, 'border' => true]);
        $sheet->getRowDimension($r)->setRowHeight(-1); // auto-height: teks panjang tampil penuh (tidak keporong)
        $r++;
    }
    $lastDataRow = $r - 1;

    // ================= TOTAL ROW =================
    $sheet->mergeCells("A{$r}:N{$r}");
    $sheet->setCellValue("A{$r}", 'TOTAL');
    $style("A{$r}:N{$r}", ['bold' => true, 'hcenter' => true, 'vcenter' => true, 'border' => true]);
    $style("O{$r}:P{$r}", ['fill' => $ORANGE, 'border' => true]);
    $totalRow = $r;
    $r += 2;

    // ================= NOTES =================
    $sheet->setCellValue("A{$r}", 'NOTES :');
    $style("A{$r}", ['bold' => true]);
    $r++;
    for ($i = 0; $i < 2; $i++) {
        $sheet->mergeCells("A{$r}:P{$r}");
        $style("A{$r}:P{$r}", ['border' => true]);
        $sheet->getRowDimension($r)->setRowHeight(18);
        $r++;
    }
    $r++;

    // ================= SIGNATURE BLOCK =================
    // Narrow footprint (columns B–D only, matching MODUL/SUB MODUL/DETAIL widths) —
    // matches the original template; it does NOT span the full table width.
    $sigRowStart = $r;
    $sigCols = ['B' => 'Mengetahui :', 'C' => 'Menyetujui :', 'D' => 'Dibuat Oleh :'];
    foreach ($sigCols as $col => $label) {
        $sheet->setCellValue("{$col}{$sigRowStart}", $label);
        $style("{$col}{$sigRowStart}", ['bold' => true]);
    }
    $sheet->getRowDimension($sigRowStart + 1)->setRowHeight(50); // signing space
    $signerRow = $sigRowStart + 2;
    $tglRow = $sigRowStart + 3;

    $sheet->setCellValue("B{$signerRow}", 'Kadiv. F/A/T');
    $sheet->setCellValue("C{$signerRow}", '(Kabag. IT)');
    $sheet->setCellValue("D{$signerRow}", $signer_name);

    $sheet->setCellValue("B{$tglRow}", 'Tgl :');
    $sheet->setCellValue("C{$tglRow}", 'Tgl :');
    $sheet->setCellValue("D{$tglRow}", 'Tgl :');

    $style("B{$sigRowStart}:D{$tglRow}", ['border' => true]);

    $sheet->freezePane('A' . ($h2 + 1));

    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="PLANNING - PROGRESS PROJECT ' . ($filter_month !== '' ? $periode : 'Semua Periode') . '.xlsx"');
    header('Cache-Control: max-age=0');
    IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
    exit;
} catch (\Throwable $e) {
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: text/plain');
    echo 'Export gagal: ' . $e->getMessage();
    exit;
}
