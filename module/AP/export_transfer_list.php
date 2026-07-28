<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

include '../../conn/conn.php';
include 'bca_dom_va_deskripsi.php';

$tlNumber = trim($_GET['tl_number'] ?? '');
if ($tlNumber === '') exit('Transfer List tidak valid.');
$tlNumberSql = mysqli_real_escape_string($conn2, $tlNumber);

$headerResult = mysqli_query($conn2, "SELECT tl_number, tl_date, tipe_mutasi, jenis_otorisasi, tanggal_efektif, jenis_biaya, status
    FROM pv_transfer_list_h WHERE tl_number = '$tlNumberSql'");
$header = mysqli_fetch_assoc($headerResult);
if (!$header) exit('Transfer List tidak ditemukan.');

$plResult = mysqli_query($conn2, "SELECT pl_number FROM pv_transfer_list_det WHERE tl_number = '$tlNumberSql'");
$plNumbers = [];
while ($r = mysqli_fetch_assoc($plResult)) $plNumbers[] = $r['pl_number'];
if (empty($plNumbers)) exit('Transfer List ini tidak memiliki Payment List.');

$plListSql = "'" . implode("','", array_map(function ($v) use ($conn2) {
    return mysqli_real_escape_string($conn2, $v);
}, $plNumbers)) . "'";

// Rekening Asal/Currency dipakai satu untuk seluruh batch - diambil dari
// Payment List pertama (asumsi semua PL dalam satu Transfer List berasal
// dari rekening sumber yang sama).
$firstAccountResult = mysqli_query($conn2, "SELECT h.from_account,
    UPPER(IFNULL(b.curr,'IDR')) bank_curr
    FROM pv_payment_list_h h
    LEFT JOIN b_masterbank b ON b.bank_account = h.from_account
    WHERE h.pl_number IN ($plListSql)
    ORDER BY h.pl_date ASC LIMIT 1");
$firstAccount = mysqli_fetch_assoc($firstAccountResult) ?: ['from_account' => '', 'bank_curr' => 'IDR'];

function paymentListDueBank($conn, $type, $number) {
    $number = mysqli_real_escape_string($conn, $number);
    $bank = ['beneficiary_name' => '', 'bank_currency' => '', 'bank_name' => '', 'bank_account' => '', 'swift_code' => '', 'kategori_penerima' => '', 'kependudukan' => '', 'kewarganegaraan' => '', 'tujuan_transaksi' => ''];
    $bankId = null;
    if ($type === 'Biaya') {
        $q = mysqli_query($conn, "SELECT MAX(b.due_date) due_date, a.to_akun FROM tbl_pv_h a INNER JOIN tbl_pv b ON b.no_pv=a.no_pv WHERE a.no_pv='$number' GROUP BY a.no_pv");
        $r = mysqli_fetch_assoc($q);
        $due = $r['due_date'] ?? null;
        if (!empty($r['to_akun'])) {
            $account = mysqli_real_escape_string($conn, $r['to_akun']);
            $qBank = mysqli_query($conn, "SELECT msb.beneficiary_name, msb.bank_currency, msb.bank_name, msb.bank_account, msb.swift_code, msb.kategori_penerima, msb.kependudukan, msb.kewarganegaraan, COALESCE(smp.nama_pilihan, msb.tujuan_transaksi) tujuan_transaksi FROM master_supplier_bank msb LEFT JOIN supplier_master_pilihan smp ON smp.kode_pilihan = msb.tujuan_transaksi AND smp.type_pilihan = 'TUJUAN TRANSAKSI' WHERE msb.bank_account='$account' UNION SELECT beneficiary_name, curr, bank_name, bank_account, swift_code, '', '', '', '' FROM b_masterbank WHERE bank_account='$account' LIMIT 1");
            $bank = mysqli_fetch_assoc($qBank) ?: $bank;
        }
    } elseif ($type === 'Installment') {
        $q = mysqli_query($conn, "SELECT no_kbon, tgl_tempo due_date FROM kontrabon_h_installment_detail WHERE no_kbon_det='$number'");
        $r = mysqli_fetch_assoc($q); $due = $r['due_date'] ?? null;
        $parent = mysqli_real_escape_string($conn, $r['no_kbon'] ?? '');
        $qBank = mysqli_query($conn, "SELECT id_bank_account FROM kontrabon_h WHERE no_kbon='$parent' AND create_date > '2026-06-30'");
        $bankId = mysqli_fetch_assoc($qBank)['id_bank_account'] ?? null;
    } elseif ($type === 'SaldoAwal') {
        $q = mysqli_query($conn, "SELECT tgl_tempo due_date, id_bank_supplier FROM ap_saldo_payment_voucher WHERE no_kbon='$number'");
        $r = mysqli_fetch_assoc($q); $due = $r['due_date'] ?? null; $bankId = $r['id_bank_supplier'] ?? null;
    } else {
        $table = $type === 'DP' ? 'kontrabon_h_dp' : ($type === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
        $q = mysqli_query($conn, "SELECT tgl_tempo due_date, id_bank_account FROM $table WHERE no_kbon='$number' AND create_date > '2026-06-30'");
        $r = mysqli_fetch_assoc($q); $due = $r['due_date'] ?? null; $bankId = $r['id_bank_account'] ?? null;
    }
    if (!empty($bankId)) {
        $bankId = mysqli_real_escape_string($conn, $bankId);
        $qBank = mysqli_query($conn, "SELECT msb.beneficiary_name, msb.bank_currency, msb.bank_name, msb.bank_account, msb.swift_code, msb.kategori_penerima, msb.kependudukan, msb.kewarganegaraan, COALESCE(smp.nama_pilihan, msb.tujuan_transaksi) tujuan_transaksi FROM master_supplier_bank msb LEFT JOIN supplier_master_pilihan smp ON smp.kode_pilihan = msb.tujuan_transaksi AND smp.type_pilihan = 'TUJUAN TRANSAKSI' WHERE msb.id='$bankId'");
        $bank = mysqli_fetch_assoc($qBank) ?: $bank;
    }
    // Berlaku untuk semua tipe: kalau swift_code belum ketemu di master_supplier_bank,
    // coba cari lagi di b_masterbank berdasarkan bank_account yang sama.
    if (empty($bank['swift_code']) && !empty($bank['bank_account'])) {
        $accForSwift = mysqli_real_escape_string($conn, $bank['bank_account']);
        $qSwift = mysqli_query($conn, "SELECT swift_code FROM b_masterbank WHERE bank_account='$accForSwift' LIMIT 1");
        $rSwift = mysqli_fetch_assoc($qSwift);
        if (!empty($rSwift['swift_code'])) $bank['swift_code'] = $rSwift['swift_code'];
    }
    return ['due' => $due, 'bank' => $bank];
}

// Bersihkan nomor rekening dari karakter non-angka (strip, spasi, dll) supaya
// tidak tampil aneh (mis. "008-997-1979") di file BCA Dom VA.
function cleanAccountNumber($value) {
    return preg_replace('/\D/', '', (string) $value);
}

// bank_name di master_supplier_bank biasanya tersimpan sebagai nama lengkap
// (mis. "BANK CENTRAL ASIA" / "BCA"). Kalau itu Bank Central Asia -> "BCA",
// selain itu -> "LLG".
function isBcaBank($bankName) {
    $bankName = (string) $bankName;
    return stripos($bankName, 'BCA') !== false || stripos($bankName, 'CENTRAL ASIA') !== false;
}

$result = mysqli_query($conn2, "SELECT pl_number, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center
    FROM pv_payment_list_det WHERE pl_number IN ($plListSql) AND status != 'Cancel' ORDER BY pl_number, nama_supp, no_kbon");
$rawRows = [];
while ($row = mysqli_fetch_assoc($result)) { $rawRows[] = $row + paymentListDueBank($conn2, $row['type_pv'], $row['no_kbon']); }

// Detail table di-sum per Payment List dan per penerima (satu baris =
// satu kombinasi PL + rekening tujuan, Nominal-nya dijumlah).
$grouped = [];
foreach ($rawRows as $row) {
    $bank = $row['bank'];
    $recipientKey = cleanAccountNumber($bank['bank_account'] ?? '') ?: strtoupper($bank['beneficiary_name'] ?? '');
    $key = $row['pl_number'] . '|' . $recipientKey;
    if (!isset($grouped[$key])) {
        $grouped[$key] = $row;
        $grouped[$key]['total'] = 0;
        $grouped[$key]['deskripsi_list'] = [];
    }
    $grouped[$key]['total'] += (float) $row['total'];
    if (!empty($row['no_kbon'])) $grouped[$key]['deskripsi_list'][] = $row['no_kbon'];
}
$rows = [];
foreach ($grouped as $g) {
    $g['deskripsi'] = implode(', ', array_unique($g['deskripsi_list']));
    unset($g['deskripsi_list']);
    $rows[] = $g;
}

$NAVY = '1F3864'; $YELLOW = 'FFFF00'; $HEADER_BLUE = 'BDD7EE'; $REQUIRED_RED = 'C00000';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('File Import_BCA Dom VA');

/* ==============================
   PARAMETER WAJIB (B1:C4) - mulai dari kolom B, bukan A, supaya
   kolom A (No.) di tabel detail tetap sempit dan tidak ikut tertutup
   box header.
============================== */
$sheet->mergeCells('B1:C1');
$sheet->setCellValue('B1', 'Parameter Wajib');
$sheet->setCellValue('B2', 'Corporate ID :');
$sheet->setCellValue('B3', 'Tipe Mutasi :');
$sheet->setCellValue('B4', 'Jenis Otorisasi :');
$sheet->getStyle('B2:B4')->getFont()->setBold(true)->getColor()->setRGB($REQUIRED_RED);

$sheet->setCellValue('C2', 'GARMENTNIR');
$sheet->setCellValue('C3', $header['tipe_mutasi'] ?: '');
$sheet->setCellValue('C4', $header['jenis_otorisasi'] ?: '');

/* ==============================
   PARAMETER OPTIONAL (E1:F8)
============================== */
$sheet->mergeCells('E1:F1');
$sheet->setCellValue('E1', 'Parameter Optional');
$sheet->setCellValue('E2', 'Header ID :');
$sheet->setCellValue('E3', 'Dependency Header ID :');
$sheet->setCellValue('E4', 'Waktu Proses Transaksi :');
$sheet->setCellValue('E5', 'Berita 1 :');
$sheet->setCellValue('E6', 'Berita 2 :');
$sheet->setCellValue('E7', 'Validasi Nama Penerima :');
$sheet->setCellValue('E8', 'Penyesuaian Tanggal Efektif :');
$sheet->getStyle('E2:E8')->getFont()->setBold(true);
$sheet->getStyle('E2:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->setCellValue('F7', 'Y');
$sheet->setCellValue('F8', 'Y');

/* ==============================
   PARAMETER TRANSAKSI (B6:C11)
   Tanggal Efektif & Jenis Biaya sekarang datang dari header Transfer
   List (diisi wajib saat create), bukan kosong seperti versi per-PL.
============================== */
$sheet->mergeCells('B6:C6');
$sheet->setCellValue('B6', 'Parameter Transaksi');
$sheet->setCellValue('B7', 'Tanggal efektif :');
$sheet->setCellValue('B8', 'Rekening Asal :');
$sheet->setCellValue('B9', 'Currency :');
$sheet->setCellValue('B10', 'Jenis Biaya :');
$sheet->setCellValue('B11', 'Rekening Biaya :');
$sheet->getStyle('B7:B11')->getFont()->setBold(true)->getColor()->setRGB($REQUIRED_RED);

// Format Tanggal Efektif : Ymd (mis. 20260723), sesuai template resmi BCA.
$sheet->setCellValue('C7', !empty($header['tanggal_efektif']) ? date('Ymd', strtotime($header['tanggal_efektif'])) : '');
$sheet->getStyle('C7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
// Rekening Asal & Rekening Biaya di header sengaja dikosongkan.
$sheet->setCellValue('C8', '');
$sheet->setCellValue('C9', $firstAccount['bank_curr'] ?: '');
$sheet->setCellValue('C10', $header['jenis_biaya'] ?: '');
$sheet->setCellValue('C11', '');

$sheet->getStyle('B1:C1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('B1:C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
$sheet->getStyle('E1:F1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('E1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
$sheet->getStyle('B6:C6')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('B6:C6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
$sheet->getStyle('B1:C4')->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->getStyle('B6:C11')->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->getStyle('E1:F8')->getBorders()->getAllBorders()->setBorderStyle('thin');

/* ==============================
   HEADER TABEL DETAIL (row 13-14)
============================== */
$columns = [
    'A' => 'No.', 'B' => 'Transaction ID', 'C' => 'Layanan Transfer', 'D' => 'Rekening Asal',
    'E' => 'Currency', 'F' => 'Jenis Biaya', 'G' => 'Rekening Biaya',
    'H' => 'Tanggal Efektif', 'I' => 'Waktu Proses Transaksi',
    'J' => 'Beneficiary ID', 'K' => 'Rekening Tujuan',
    'L' => 'Nama Penerima', 'M' => 'Nominal', 'N' => 'Berita 1', 'O' => 'Berita 2',
    'P' => 'Deskripsi', 'Q' => 'Email Penerima', 'R' => 'Kode SWIFT Bank Tujuan',
    'S' => 'Kategori Penerima', 'T' => 'Kependudukan', 'U' => 'Kewarganegaraan',
    'V' => 'Tujuan Transaksi',
];
$redCols = ['B', 'K', 'M'];
$headerRow = 13;
$filterRow = 14;
foreach ($columns as $col => $label) {
    $sheet->setCellValue($col . $headerRow, $label);
    $sheet->setCellValue($col . $filterRow, 'Filter');
    if (in_array($col, $redCols, true)) $sheet->getStyle($col . $headerRow)->getFont()->getColor()->setRGB($REQUIRED_RED);
}
$sheet->getStyle('A' . $headerRow . ':V' . $filterRow)->getFont()->setBold(true);
$sheet->getStyle('A' . $headerRow . ':V' . $filterRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($HEADER_BLUE);
$sheet->getStyle('A' . $headerRow . ':V' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
$sheet->getStyle('A' . $filterRow . ':V' . $filterRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A' . $filterRow . ':V' . $filterRow)->getFont()->setSize(9)->setBold(false)->getColor()->setRGB('808080');
$sheet->getStyle('A' . $headerRow . ':V' . $filterRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->setAutoFilter('A' . $filterRow . ':V' . $filterRow);

/* ==============================
   DATA DETAIL (satu baris per PV/invoice, digabung dari semua PL)
============================== */
$rowNumber = $filterRow + 1;
$firstDataRow = $rowNumber;
$no = 1;
$createDate = !empty($header['tl_date']) ? date('Ymd', strtotime($header['tl_date'])) : date('Ymd');
// Format Tanggal Efektif : Ymd (mis. 20260723), sesuai template resmi BCA.
$effectiveDate = !empty($header['tanggal_efektif']) ? date('Ymd', strtotime($header['tanggal_efektif'])) : '';
foreach ($rows as $row) {
    $bank = $row['bank'];
    $amount = (float) $row['total'];
    $transactionId = $createDate . str_pad($no, 6, '0', STR_PAD_LEFT);
    $layananTransfer = isBcaBank($bank['bank_name'] ?? '') ? 'BCA' : 'LLG';
    $sheet->fromArray([
        $no, $transactionId, $layananTransfer, $firstAccount['from_account'] ?: '', $row['curr'] ?: '', $header['jenis_biaya'] ?: '', $firstAccount['from_account'] ?: '',
        $effectiveDate, '', '', '', strtoupper($bank['beneficiary_name'] ?? ''), $amount,
        '', '', '', '', $bank['swift_code'] ?? '', $bank['kategori_penerima'] ?? '', $bank['kependudukan'] ?? '', $bank['kewarganegaraan'] ?? '', $bank['tujuan_transaksi'] ?? '',
    ], null, 'A' . $rowNumber);
    // Transaction ID & Rekening (Asal/Biaya/Tujuan) dipaksa jadi text supaya
    // tidak berubah jadi notasi ilmiah (mis. 7.02611E+11) atau kehilangan angka 0 di depan.
    $sheet->setCellValueExplicit('B' . $rowNumber, $transactionId, DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('D' . $rowNumber, cleanAccountNumber($firstAccount['from_account'] ?? ''), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('G' . $rowNumber, cleanAccountNumber($firstAccount['from_account'] ?? ''), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('K' . $rowNumber, cleanAccountNumber($bank['bank_account'] ?? ''), DataType::TYPE_STRING);
    $sheet->getStyle('M' . $rowNumber)->getNumberFormat()->setFormatCode('#,##0.00');
    $rowNumber++;
    $no++;
}
$sheet->getStyle('B' . $firstDataRow . ':B' . max($firstDataRow, $rowNumber - 1))->getNumberFormat()->setFormatCode('@');
$sheet->getStyle('D' . $firstDataRow . ':D' . max($firstDataRow, $rowNumber - 1))->getNumberFormat()->setFormatCode('@');
$sheet->getStyle('G' . $firstDataRow . ':G' . max($firstDataRow, $rowNumber - 1))->getNumberFormat()->setFormatCode('@');
$sheet->getStyle('K' . $firstDataRow . ':K' . max($firstDataRow, $rowNumber - 1))->getNumberFormat()->setFormatCode('@');
$lastRow = max($filterRow, $rowNumber - 1);
$sheet->getStyle('A' . $firstDataRow . ':V' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle('thin');
$sheet->getStyle('A' . $firstDataRow . ':V' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$sheet->getStyle('H' . $firstDataRow . ':H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Freeze No. & Transaction ID (kolom A:B) plus semua baris header di
// atasnya, supaya tetap kelihatan saat scroll ke kanan/bawah.
$sheet->freezePane('D' . $firstDataRow);

// Lebar kolom mengikuti panjang teks isinya (auto-size).
foreach (array_keys($columns) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* ==============================
   SHEET KEDUA: Deskripsi (dokumentasi struktur file & pilihan field,
   sesuai spesifikasi resmi template BCA Dom VA)
============================== */
$spreadsheet->createSheet();
$deskripsiSheet = $spreadsheet->getSheet(1);
$deskripsiSheet->setTitle('Deskripsi');
fillBcaDomVaDeskripsiSheet($deskripsiSheet);
$spreadsheet->setActiveSheetIndex(0);

while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="FileImport_BCADomVA_' . preg_replace('/[^A-Za-z0-9]+/', '_', $header['tl_number']) . '.xlsx"');
header('Cache-Control: max-age=0');
IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
exit;
