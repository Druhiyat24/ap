<?php
// ============================================================================
// Simpan SEMUA baris BPB yang dicentang di modal "Add Data" — SATU request,
// SATU multi-row INSERT, di dalam SATU transaksi.
//
// Menggantikan pola lama: JS menembak insert_po_detail_temp.php SATU KALI PER
// BARIS (N request paralel tak ditunggu), lalu menunggu setTimeout(1500ms)
// tebak-tebakan sebelum membaca balik. Kalau baris yang dicentang banyak
// (puluhan), sebagian request belum selesai saat batas waktu itu habis —
// itulah sebabnya "ceklis banyak, yang masuk cuma sedikit". Dengan satu
// request tunggal, tidak ada lagi tebak-tebakan waktu: begitu respons ini
// sukses, SEMUA baris sudah pasti tersimpan.
//
// Input : create_user, rows = JSON array of
//         {id_bpb,no_po,no_bpb,tgl_bpb,id_jo,id_item,itemdesc,qty,qty_tagih,price,price_tagih,unit}
// Output: {status:'success', baris:n} | {status:'error', message:'...'}
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

function pdt_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$create_user = trim($_POST['create_user'] ?? '');
$rows = json_decode($_POST['rows'] ?? '[]', true);

if ($create_user === '') { pdt_out(['status' => 'error', 'message' => 'create_user is empty.']); }
if (!is_array($rows) || !$rows) { pdt_out(['status' => 'error', 'message' => 'No BPB row is checked.']); }

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) ($v ?? '')); };
$create_date = date('Y-m-d H:i:s');

$head  = "INSERT INTO req_dn_po_detail_temp
    (id_bpb, no_po, no_bpb, tgl_bpb, id_jo, id_item, itemdesc, qty, qty_tagih, price, price_tagih, unit, created_by, created_date) VALUES ";
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

foreach ($rows as $r) {
    // Kolom tgl_bpb bertipe DATE (bukan datetime) — format Y-m-d saja.
    $tglBpb = !empty($r['tgl_bpb']) ? date('Y-m-d', strtotime($r['tgl_bpb'])) : null;
    $vals[] = "('" . $e($r['id_bpb'] ?? '') . "', '" . $e($r['no_po'] ?? '') . "', '" . $e($r['no_bpb'] ?? '') . "',"
            . " '" . $e($tglBpb) . "', '" . $e($r['id_jo'] ?? '') . "', '" . $e($r['id_item'] ?? '') . "',"
            . " '" . $e($r['itemdesc'] ?? '') . "', '" . $e($r['qty'] ?? 0) . "', '" . $e($r['qty_tagih'] ?? 0) . "',"
            . " '" . $e($r['price'] ?? 0) . "', '" . $e($r['price_tagih'] ?? 0) . "', '" . $e($r['unit'] ?? '') . "',"
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
    pdt_out(['status' => 'error', 'message' => 'Failed to save: ' . mysqli_error($conn2)]);
}

mysqli_commit($conn2);
pdt_out(['status' => 'success', 'baris' => $n]);
