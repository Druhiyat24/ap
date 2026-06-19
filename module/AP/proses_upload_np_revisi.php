<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../conn/conn.php';
header('Content-Type: application/json');
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File tidak ditemukan']);
    exit;
}

mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS update_np_revisi_h (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_dok VARCHAR(30) NOT NULL,
  tgl_dok DATE NOT NULL,
  total_row INT(11) DEFAULT 0,
  total_success INT(11) DEFAULT 0,
  total_failed INT(11) DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'Updated',
  file_name VARCHAR(255) DEFAULT NULL,
  created_by VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_no_dok (no_dok)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS update_np_revisi_d (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_dok VARCHAR(30) NOT NULL,
  baris INT(11) DEFAULT NULL,
  tipe VARCHAR(5) NOT NULL,
  no_ref VARCHAR(100) DEFAULT NULL,
  no_barcode VARCHAR(50) DEFAULT NULL,
  curr_old VARCHAR(10) DEFAULT NULL,
  curr_new VARCHAR(10) DEFAULT NULL,
  price_old DECIMAL(18,4) DEFAULT NULL,
  price_new DECIMAL(18,4) DEFAULT NULL,
  status VARCHAR(20) DEFAULT NULL,
  keterangan VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_no_dok (no_dok)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

try {
    $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal membaca file: ' . $e->getMessage()]);
    exit;
}

$total_row = 0;
$total_success = 0;
$total_failed = 0;
$details = [];

foreach ($rows as $i => $row) {
    if ($i == 0) continue; // skip header row

    $tipe       = trim((string)($row[0] ?? ''));
    $no_ref     = trim((string)($row[1] ?? ''));
    $no_barcode = trim((string)($row[2] ?? ''));
    $curr_new   = trim((string)($row[3] ?? ''));
    $price_new  = $row[4] ?? '';

    // skip fully empty row
    if ($tipe === '' && $no_ref === '' && $no_barcode === '' && $curr_new === '' && $price_new === '') {
        continue;
    }

    $total_row++;

    $tipe     = strtoupper($tipe);
    $curr_new = strtoupper($curr_new);

    $detail = [
        'baris'      => $i + 1, // excel row number
        'tipe'       => $tipe,
        'no_ref'     => $no_ref,
        'no_barcode' => $no_barcode,
        'curr_old'   => null,
        'curr_new'   => $curr_new !== '' ? $curr_new : null,
        'price_old'  => null,
        'price_new'  => is_numeric($price_new) ? $price_new : null,
        'status'     => 'Failed',
        'keterangan' => ''
    ];

    if ($tipe !== 'IN' && $tipe !== 'OUT') {
        $detail['keterangan'] = 'Tipe harus IN atau OUT';
        $total_failed++;
        $details[] = $detail;
        continue;
    }

    if ($no_ref === '' || $no_barcode === '') {
        $detail['keterangan'] = 'No Dokumen / No Barcode tidak boleh kosong';
        $total_failed++;
        $details[] = $detail;
        continue;
    }

    if ($curr_new === '' || !is_numeric($price_new)) {
        $detail['keterangan'] = 'Curr Baru / Price Baru tidak valid';
        $total_failed++;
        $details[] = $detail;
        continue;
    }

    $no_ref_esc     = mysqli_real_escape_string($conn1, $no_ref);
    $no_barcode_esc = mysqli_real_escape_string($conn1, $no_barcode);
    $curr_new_esc   = mysqli_real_escape_string($conn1, $curr_new);
    $price_new_val  = (float) $price_new;

    if ($tipe === 'OUT') {
        $check = mysqli_query($conn1, "SELECT np_curr_rev, np_price_rev FROM whs_bppb_det WHERE no_bppb = '$no_ref_esc' AND id_roll = '$no_barcode_esc' LIMIT 1");
    } else {
        $check = mysqli_query($conn1, "SELECT np_curr_rev, np_price_rev FROM whs_lokasi_inmaterial WHERE no_dok = '$no_ref_esc' AND no_barcode = '$no_barcode_esc' LIMIT 1");
    }

    $found = $check ? mysqli_fetch_assoc($check) : null;

    if (!$found) {
        $detail['keterangan'] = 'Data tidak ditemukan';
        $total_failed++;
        $details[] = $detail;
        continue;
    }

    $detail['curr_old']  = $found['np_curr_rev'];
    $detail['price_old'] = $found['np_price_rev'];

    if ($tipe === 'OUT') {
        $update = mysqli_query($conn1, "UPDATE whs_bppb_det SET np_curr_rev = '$curr_new_esc', np_price_rev = $price_new_val WHERE no_bppb = '$no_ref_esc' AND id_roll = '$no_barcode_esc'");
    } else {
        $update = mysqli_query($conn1, "UPDATE whs_lokasi_inmaterial SET np_curr_rev = '$curr_new_esc', np_price_rev = $price_new_val WHERE no_dok = '$no_ref_esc' AND no_barcode = '$no_barcode_esc'");
    }

    if ($update) {
        $detail['status']     = 'Success';
        $detail['keterangan'] = 'Berhasil diupdate';
        $total_success++;
    } else {
        $detail['keterangan'] = 'Gagal update database';
        $total_failed++;
    }

    $details[] = $detail;
}

if ($total_row === 0) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data pada file yang diupload']);
    exit;
}

// Generate doc number REV/GK/MMYY/00001
$prefix = 'REV/GK/' . date('my') . '/';
$cek = mysqli_query($conn1, "SELECT MAX(CAST(SUBSTRING(no_dok, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) mx FROM update_np_revisi_h WHERE no_dok LIKE '$prefix%'");
$row_cek = mysqli_fetch_assoc($cek);
$next_no = (!empty($row_cek['mx'])) ? ((int) $row_cek['mx'] + 1) : 1;
$no_dok = $prefix . str_pad($next_no, 5, '0', STR_PAD_LEFT);

$user       = $_SESSION['username'] ?? 'system';
$created_at = date('Y-m-d H:i:s');
$tgl_dok    = date('Y-m-d');
$file_name  = $_FILES['file']['name'];

$no_dok_esc    = mysqli_real_escape_string($conn1, $no_dok);
$file_name_esc = mysqli_real_escape_string($conn1, $file_name);
$user_esc      = mysqli_real_escape_string($conn1, $user);

$insertHeader = mysqli_query($conn1, "INSERT INTO update_np_revisi_h (no_dok, tgl_dok, total_row, total_success, total_failed, file_name, created_by, created_at)
    VALUES ('$no_dok_esc', '$tgl_dok', $total_row, $total_success, $total_failed, '$file_name_esc', '$user_esc', '$created_at')");

if (!$insertHeader) {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan header dokumen']);
    exit;
}

foreach ($details as $d) {
    $tipe_esc       = mysqli_real_escape_string($conn1, $d['tipe']);
    $no_ref_esc     = mysqli_real_escape_string($conn1, $d['no_ref']);
    $no_barcode_esc = mysqli_real_escape_string($conn1, $d['no_barcode']);
    $curr_old_sql   = $d['curr_old'] !== null ? "'" . mysqli_real_escape_string($conn1, $d['curr_old']) . "'" : 'NULL';
    $curr_new_sql   = $d['curr_new'] !== null ? "'" . mysqli_real_escape_string($conn1, $d['curr_new']) . "'" : 'NULL';
    $price_old_sql  = $d['price_old'] !== null ? (float) $d['price_old'] : 'NULL';
    $price_new_sql  = $d['price_new'] !== null ? (float) $d['price_new'] : 'NULL';
    $status_esc     = mysqli_real_escape_string($conn1, $d['status']);
    $keterangan_esc = mysqli_real_escape_string($conn1, $d['keterangan']);

    mysqli_query($conn1, "INSERT INTO update_np_revisi_d (no_dok, baris, tipe, no_ref, no_barcode, curr_old, curr_new, price_old, price_new, status, keterangan)
        VALUES ('$no_dok_esc', {$d['baris']}, '$tipe_esc', '$no_ref_esc', '$no_barcode_esc', $curr_old_sql, $curr_new_sql, $price_old_sql, $price_new_sql, '$status_esc', '$keterangan_esc')");
}

echo json_encode([
    'success'       => true,
    'no_dok'        => $no_dok,
    'total_row'     => $total_row,
    'total_success' => $total_success,
    'total_failed'  => $total_failed
]);
