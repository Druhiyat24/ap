<?php
// ============================================================================
// Upload dokumen lampiran Request Debit Note.
// SEKARANG BOLEH BERULANG KALI per no_req — dulu tombol upload di list otomatis
// disembunyikan begitu SATU dokumen sudah ada, jadi cuma bisa 1 dokumen per
// request. Tabel req_dn_dok sendiri memang sudah dari dulu tidak punya unique
// constraint di no_req (bisa banyak baris), cuma UI lama yang membatasinya.
//
// Nama file FISIK dibuat UNIK (kode no_req + timestamp + random) — dulu nama
// file fisik = nama file asli (spasi dibuang saja), jadi 2 request BERBEDA yang
// kebetulan upload file dgn nama sama akan SALING TIMPA file-nya di file_pdf/.
// Sekarang risiko itu makin nyata karena 1 request bisa punya banyak dokumen
// sekaligus. Nama asli tetap disimpan di file_name_as supaya user tetap lihat
// nama yang mereka kenal di daftar dokumen.
//
// Dulu file ini POST biasa (submit form -> redirect penuh via Header()) dan
// bahkan sempat memanggil alert() di PHP (fungsi yang tidak ada di PHP, cuma
// tidak pernah kepanggil krn move_uploaded_file kedua selalu gagal/false sebab
// file sementara sudah dipindah di baris sebelumnya). Sekarang dijadikan AJAX
// murni (respons JSON) supaya modalnya tidak perlu reload halaman dan bisa
// dipakai upload berkali-kali tanpa menutup modal.
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

function rdd_out($a) { echo json_encode($a); exit; }

$txt_no_req = trim($_POST['txt_no_req'] ?? '');
$txt_user   = trim($_POST['txt_user'] ?? '');

if ($txt_no_req === '') { rdd_out(['status' => 'error', 'message' => 'No Request is empty.']); }
if (empty($_FILES['txtfile']['name'])) { rdd_out(['status' => 'error', 'message' => 'Please choose a file to upload.']); }
if ($_FILES['txtfile']['error'] !== UPLOAD_ERR_OK) { rdd_out(['status' => 'error', 'message' => 'Upload failed (error code ' . $_FILES['txtfile']['error'] . ').']); }

$origName = $_FILES['txtfile']['name'];
$tmpFile  = $_FILES['txtfile']['tmp_name'];
$ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION)) ?: 'pdf';
$baseCode = preg_replace('/[^A-Za-z0-9]/', '', $txt_no_req);
$physical = $baseCode . '_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
$path     = 'file_pdf/' . $physical;

if (!move_uploaded_file($tmpFile, $path)) {
    rdd_out(['status' => 'error', 'message' => 'Failed to save the uploaded file on the server.']);
}

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$sql = "INSERT INTO req_dn_dok (no_req, file_name, file_name_as, created_by, created_date) VALUES ('"
     . $e($txt_no_req) . "', '" . $e($physical) . "', '" . $e($origName) . "', '" . $e($txt_user) . "', '" . date('Y-m-d H:i:s') . "')";

if (!mysqli_query($conn2, $sql)) {
    @unlink($path);
    rdd_out(['status' => 'error', 'message' => 'Failed to save the record: ' . mysqli_error($conn2)]);
}

rdd_out(['status' => 'success']);
