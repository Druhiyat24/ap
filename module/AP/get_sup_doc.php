<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';

$sql = mysqli_query($conn2, "select GROUP_CONCAT(ket) as sup_doc from (select * from supp_doc_temp where ket != '' ) supp_doc_temp");
$row = mysqli_fetch_array($sql);
$sup_doc = isset($row['sup_doc']) ? $row['sup_doc'] : '';

mysqli_close($conn2);

header('Content-Type: text/plain');
echo $sup_doc;
