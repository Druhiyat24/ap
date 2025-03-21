<?php
include '../../conn/conn.php';
$sub = 0;
$tanggal = isset($_POST['tanggal']) ? date("Y-m-d",strtotime($_POST['tanggal'])) : null;

    $sql = mysqli_query($conn1,"select CONCAT('SI/CBD/',DATE_FORMAT('$tanggal', '%Y'),'/',DATE_FORMAT('$tanggal', '%m'),'/',LPAD((COALESCE(max(SUBSTR(no_kbon,16)),0) + 1),5,0)) nomor from kontrabon_h_cbd WHERE no_kbon != 'SI/CBD/2025/01/00258' and YEAR(tgl_kbon) = YEAR ('$tanggal')");

    while($r=mysqli_fetch_array($sql)){
        echo $r['nomor'];     
        ?>
       <?php        
}

?>