<?php
// ============================================================================
// Simpan SEMUA baris item Request Debit Note — SATU request, SATU multi-row
// INSERT, di dalam SATU transaksi. Menggantikan insert_req_dn.php yang dulu
// ditembak SATU KALI PER BARIS di dalam $.each() TANPA ditunggu, dan tiap
// respons sukses langsung window.location = '...' (redirect) — kalau item-nya
// lebih dari satu, request PERTAMA yang selesai langsung membawa browser
// pergi, sementara baris-baris lain mungkin belum sempat terkirim/tersimpan.
// Sama persis dgn masalah "ceklis BPB banyak, yg masuk cuma sedikit" yang
// sudah diperbaiki di insert_po_detail_temp_bulk.php — pola perbaikannya juga
// sama: kumpulkan semua baris di JS, kirim SEKALI ke sini.
//
// $kode (no_req) diambil ULANG dari tbl req_dn_h via unik_code (BUKAN dari
// nilai yg tampil di halaman) — no_req sungguhan ditentukan insert_req_dn_h.php
// saat itu juga, jadi ini satu-satunya sumber yg benar.
//
// Input : unik_code, create_user, rows = JSON array of
//         {no_po,no_bpb,item,qty,price,attn,seasons,no_reff,id_bpb,tgl_bpb,id_jo,id_item,unit}
// Output: {status:'success', baris:n, no_req:'...'} | {status:'error', message:'...'}
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

function rdn_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) ($v ?? '')); };

$unik_code   = trim($_POST['unik_code'] ?? '');
$create_user = trim($_POST['create_user'] ?? '');
$rows        = json_decode($_POST['rows'] ?? '[]', true);

if ($unik_code === '')   { rdn_out(['status' => 'error', 'message' => 'unik_code is empty.']); }
if ($create_user === '') { rdn_out(['status' => 'error', 'message' => 'create_user is empty.']); }
if (!is_array($rows) || !$rows) { rdn_out(['status' => 'error', 'message' => 'No item row to save.']); }

$q = mysqli_query($conn2, "select distinct no_req from req_dn_h where unik_code = '" . $e($unik_code) . "'");
$r = $q ? mysqli_fetch_assoc($q) : null;
$kode = $r['no_req'] ?? '';
if ($kode === '') { rdn_out(['status' => 'error', 'message' => 'Request header not found (invalid unik_code). Please save the header first.']); }

$create_date = date('Y-m-d H:i:s');
$head  = "INSERT INTO req_dn (no_req,no_po,item,qty,price,attn,seasons,no_reff,id_bpb,tgl_bpb,no_bpb,id_jo,id_item,unit,created_by,created_date) VALUES ";
$vals  = [];
$CHUNK = 500;
$n     = 0;
$ok    = true;

mysqli_begin_transaction($conn2);

$flush = function () use (&$vals, $head, $conn2, &$ok) {
    if (!$vals) { return; }
    if (mysqli_query($conn2, $head . implode(',', $vals)) === false) { $ok = false; }
    $vals = [];
};

foreach ($rows as $row) {
    $qty   = (float) ($row['qty'] ?? 0);
    $price = (float) ($row['price'] ?? 0);
    if ($qty <= 0 || $price <= 0) { continue; } // syarat sama seperti versi lama

    $tglBpb = !empty($row['tgl_bpb']) ? date('Y-m-d', strtotime($row['tgl_bpb'])) : null;
    $vals[] = "('" . $e($kode) . "', '" . $e($row['no_po'] ?? '') . "', '" . $e($row['item'] ?? '') . "',"
            . " '" . $e($qty) . "', '" . $e($price) . "', '" . $e($row['attn'] ?? '') . "',"
            . " '" . $e($row['seasons'] ?? '') . "', '" . $e($row['no_reff'] ?? '') . "',"
            . " '" . $e($row['id_bpb'] ?? '') . "', '" . $e($tglBpb) . "', '" . $e($row['no_bpb'] ?? '') . "',"
            . " '" . $e($row['id_jo'] ?? '-') . "', '" . $e($row['id_item'] ?? '-') . "', '" . $e($row['unit'] ?? '-') . "',"
            . " '" . $e($create_user) . "', '" . $e($create_date) . "')";
    $n++;
    if (count($vals) >= $CHUNK) {
        $flush();
        if (!$ok) { break; }
    }
}
if ($ok) { $flush(); }

if (!$ok) {
    mysqli_rollback($conn2);
    rdn_out(['status' => 'error', 'message' => 'Failed to save items: ' . mysqli_error($conn2)]);
}
if ($n === 0) {
    mysqli_rollback($conn2);
    rdn_out(['status' => 'error', 'message' => 'No valid item row (Qty and Price must both be greater than 0).']);
}

// Draft PO/BPB temp milik user ini sudah dikonsumsi jadi baris permanen — bersihkan.
mysqli_query($conn2, "delete from req_dn_po_detail_temp where created_by = '" . $e($create_user) . "'");

mysqli_commit($conn2);
rdn_out(['status' => 'success', 'baris' => $n, 'no_req' => $kode]);
