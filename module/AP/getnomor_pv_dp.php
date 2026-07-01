<?php
include '../../conn/conn.php';
$tanggal = isset($_POST['tanggal']) ? date("Y-m-d",strtotime($_POST['tanggal'])) : null;

$sql = mysqli_query($conn1, "select CONCAT(
        'PV-AP/DP/',
        DATE_FORMAT('$tanggal', '%Y'), '/',
        DATE_FORMAT('$tanggal', '%m'), '/',
        LPAD(
            COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
            5, '0'
        )
    ) nomor from kontrabon_h_dp WHERE YEAR(tgl_kbon) = YEAR('$tanggal')");

$row = mysqli_fetch_array($sql);
echo $row['nomor'];
