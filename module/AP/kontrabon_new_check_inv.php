<?php
// Cek apakah No Invoice sudah pernah dipakai UNTUK SUPPLIER YANG SAMA di kontrabon
// tersimpan (yang belum Cancel). Boleh dipakai lagi kalau supplier beda / kontrabon
// lama sudah Cancel. Dipanggil saat "Add Invoice" di Create Kontrabon.
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$no_inv     = trim($_POST['no_inv'] ?? '');
$supplier   = trim($_POST['supplier'] ?? '');
$excludeDoc = trim($_POST['exclude_doc'] ?? '');   // mode edit: abaikan invoice milik kontrabon ini sendiri
if ($no_inv === '')   { echo json_encode(['ok' => false, 'msg' => 'Invoice number is empty.']); exit; }
if ($supplier === '') { echo json_encode(['ok' => false, 'msg' => 'Please select the Supplier first.']); exit; }

// No Invoice strip ("-") = penanda "tanpa nomor invoice". Boleh dipakai berkali-kali,
// termasuk oleh supplier yang sama -> lewati cek "sudah dipakai" (sama spt faktur).
if (preg_match('/^-+$/', $no_inv)) { echo json_encode(['ok' => true]); exit; }

$ni = mysqli_real_escape_string($conn2, $no_inv);
$sp = mysqli_real_escape_string($conn2, $supplier);
$excCond = ($excludeDoc !== '') ? " AND h.doc_number <> '" . mysqli_real_escape_string($conn2, $excludeDoc) . "'" : '';

$r = mysqli_query($conn2, "SELECT h.doc_number FROM ir_kontrabon_inv i
    JOIN ir_kontrabon_h h ON h.unik_code = i.unik_code
    WHERE i.no_inv = '$ni' AND h.nama_supp = '$sp' AND (h.status IS NULL OR h.status <> 'Cancel')$excCond
    LIMIT 1");

if ($r && mysqli_num_rows($r) > 0) {
    $doc = mysqli_fetch_assoc($r)['doc_number'];
    echo json_encode(['ok' => false, 'msg' => "Invoice '$no_inv' is already used for this supplier in invoice received $doc."]);
} else {
    echo json_encode(['ok' => true]);
}
mysqli_close($conn2);
