<?php
include "../../conn/conn.php";

header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

// ================= HEADER =================
$stmt = mysqli_prepare($conn2, "SELECT * FROM transfer_memo_exim_h WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result_header = mysqli_stmt_get_result($stmt);
$header = mysqli_fetch_assoc($result_header);


// ================= DETAIL =================
$query_detail = "
SELECT 
    c.id_h, 
    b.nm_memo, 
    c.tgl_memo, 
    ms.supplier, 
    jns_trans, 
    jns_pengiriman, 
    mb.supplier buyer, 
    UPPER(IFNULL(b.keterangan,a.keterangan)) keterangan 
FROM transfer_memo_exim_h a 
INNER JOIN transfer_memo_exim_det b ON b.no_trans = a.no_trans 
INNER JOIN memo_h c ON c.nm_memo = b.nm_memo 
INNER JOIN mastersupplier ms ON ms.id_supplier = c.id_supplier 
INNER JOIN mastersupplier mb ON mb.id_supplier = c.id_buyer 
WHERE a.id = ? 
GROUP BY b.nm_memo
";

$stmt2 = mysqli_prepare($conn2, $query_detail);
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$result_detail = mysqli_stmt_get_result($stmt2);

$detail = [];
while ($row = mysqli_fetch_assoc($result_detail)) {
    $detail[] = $row;
}


// ================= RESPONSE =================
echo json_encode([
    'header' => $header,
    'detail' => $detail
]);
