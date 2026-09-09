<?php
// ============================================================================
// Simpan ulang SEMUA baris item Request Debit Note versi EDIT.
// Beda dgn insert_req_dn_bulk.php (dipakai saat CREATE, no_req-nya belum punya
// baris item sama sekali): endpoint ini dipakai saat EDIT, jadi baris LAMA
// milik no_req ini DIHAPUS dulu, baru baris versi TERBARU (hasil user menambah/
// menghapus BPB di halaman edit) dimasukkan — DIHAPUS+DIMASUKKAN dalam SATU
// transaksi supaya tidak pernah ada momen request kehilangan seluruh itemnya
// kalau prosesnya gagal di tengah jalan (auto-rollback).
//
// Status 'Post' dicek ulang di sini juga (bukan cuma di update_req_dn_h.php)
// sbg jaga2 kalau endpoint ini dipanggil sendirian tanpa lewat update_req_dn_h.php
// dulu.
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

function rdnu_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) ($v ?? '')); };

$unik_code   = trim($_POST['unik_code'] ?? '');
$create_user = trim($_POST['create_user'] ?? '');
$rows        = json_decode($_POST['rows'] ?? '[]', true);

if ($unik_code === '')   { rdnu_out(['status' => 'error', 'message' => 'unik_code is empty.']); }
if ($create_user === '') { rdnu_out(['status' => 'error', 'message' => 'create_user is empty.']); }
if (!is_array($rows) || !$rows) { rdnu_out(['status' => 'error', 'message' => 'No item row to save.']); }

$q = mysqli_query($conn2, "select no_req, status from req_dn_h where unik_code = '" . $e($unik_code) . "'");
$r = $q ? mysqli_fetch_assoc($q) : null;
if (!$r) { rdnu_out(['status' => 'error', 'message' => 'Request header not found (invalid unik_code).']); }
if ($r['status'] !== 'Post') { rdnu_out(['status' => 'error', 'message' => 'This request can no longer be edited (status: ' . $r['status'] . ').']); }
$kode = $r['no_req'];

$create_date = date('Y-m-d H:i:s');
$head  = "INSERT INTO req_dn (no_req,no_po,item,qty,price,attn,seasons,no_reff,id_bpb,tgl_bpb,no_bpb,id_jo,id_item,unit,created_by,created_date) VALUES ";
$vals  = [];
$CHUNK = 500;
$n     = 0;
$ok    = true;

mysqli_begin_transaction($conn2);

// Baris LAMA dibuang semua dulu — versi baru (hasil tambah/kurang BPB oleh
// user) yg jadi acuan tunggal sesudah ini.
if (mysqli_query($conn2, "delete from req_dn where no_req = '" . $e($kode) . "'") === false) {
    mysqli_rollback($conn2);
    rdnu_out(['status' => 'error', 'message' => 'Failed to clear old items: ' . mysqli_error($conn2)]);
}

$flush = function () use (&$vals, $head, $conn2, &$ok) {
    if (!$vals) { return; }
    if (mysqli_query($conn2, $head . implode(',', $vals)) === false) { $ok = false; }
    $vals = [];
};

foreach ($rows as $row) {
    $qty   = (float) ($row['qty'] ?? 0);
    $price = (float) ($row['price'] ?? 0);
    if ($qty <= 0 || $price <= 0) { continue; }

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
    rdnu_out(['status' => 'error', 'message' => 'Failed to save items: ' . mysqli_error($conn2)]);
}
if ($n === 0) {
    mysqli_rollback($conn2);
    rdnu_out(['status' => 'error', 'message' => 'No valid item row (Qty and Price must both be greater than 0).']);
}

mysqli_query($conn2, "delete from req_dn_po_detail_temp where created_by = '" . $e($create_user) . "'");

mysqli_commit($conn2);
rdnu_out(['status' => 'success', 'baris' => $n, 'no_req' => $kode]);
