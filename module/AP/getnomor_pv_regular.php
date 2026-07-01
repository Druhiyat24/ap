<?php
include '../../conn/conn.php';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : null;

$sql = mysqli_query($conn2, "select CONCAT(
        'PV-AP/REG/',
        DATE_FORMAT('$tanggal', '%Y'), '/',
        DATE_FORMAT('$tanggal', '%m'), '/',
        LPAD(
            COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
            5, '0'
        )
    ) nomor from kontrabon_h WHERE YEAR(tgl_kbon) = YEAR('$tanggal')");

$row = mysqli_fetch_array($sql);
echo $row['nomor'];
