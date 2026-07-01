<?php
include '../../conn/conn.php';
$tanggal = isset($_POST['tanggal']) ? date("Y-m-d", strtotime($_POST['tanggal'])) : null;

$sql = mysqli_query($conn1, "select CONCAT('PV-AP/CBD/',DATE_FORMAT('$tanggal', '%Y'),'/',DATE_FORMAT('$tanggal', '%m'),'/',LPAD((COALESCE(max(CAST(RIGHT(no_kbon,5) AS UNSIGNED)),0) + 1),5,'0')) nomor from kontrabon_h_cbd WHERE YEAR(tgl_kbon) = YEAR ('$tanggal')");

while ($r = mysqli_fetch_array($sql)) {
    echo $r['nomor'];
}
?>
