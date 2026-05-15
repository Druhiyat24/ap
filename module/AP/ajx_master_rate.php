<?php
include "../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$type_curr = $_POST['type_curr'];
$curr = $_POST['curr'];
$search = $_POST['search']['value'] ?? '';
$where = "WHERE 1=1 ";

if ($type_curr != 'ALL') {
    $type_curr = "and v_codecurr = '$type_curr'";
}else{
    $type_curr = "";
}

if ($curr != 'ALL') {
    $curr = "and curr = '$curr'";
}else{
    $curr = "";
}

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            v_codecurr LIKE '%$search%' OR
            curr LIKE '%$search%' OR
            tanggal LIKE '%$search%' OR
            v_lastupdate LIKE '%$search%'
        )
    ";
}


$sql = "select v_codecurr, v_idgroup, IF(COUNT(tanggal) > 1, CONCAT(DATE_FORMAT(MIN(tanggal), '%e %M %Y'),' - ',DATE_FORMAT(MAX(tanggal), '%e %M %Y')), DATE_FORMAT(MAX(tanggal), '%e %M %Y')) tanggal_input, curr, TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate)) AS rate, TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate_jual)) AS rate_jual, TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate_beli)) AS rate_beli, v_lastupdate from ap_masterrate where v_codecurr != '' and v_idgroup != '' $type_curr $curr GROUP BY v_codecurr, v_idgroup order by tanggal DESC ";

$sql_count = "SELECT COUNT(*) total FROM ($sql) x";
$q_count = mysqli_query($conn2, $sql_count);
$row = mysqli_fetch_assoc($q_count);
$recordsTotal = intval($row['total']);
$recordsFiltered = $recordsTotal;

/* ================= DATA + LIMIT ================= */
$sql_limit = $sql . " LIMIT $start, $length";
$q = mysqli_query($conn2, $sql_limit);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
]);