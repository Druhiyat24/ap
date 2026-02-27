<?php
include "../../conn/conn.php";

$sql = "SELECT MIN(tgl_awal) as tgl_awal 
        FROM tbl_closing_periode 
        WHERE status_closing = 'Open'";

$q = mysqli_query($conn2, $sql);
$row = mysqli_fetch_assoc($q);

echo json_encode([
    "tgl_awal" => date("d-m-Y", strtotime($row['tgl_awal']))
]);
