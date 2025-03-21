<?php
include '../../conn/conn.php';
$sub = 0;
$tanggal = isset($_POST['tanggal']) ? date("Y-m-d",strtotime($_POST['tanggal'])) : null;

    $sql = mysqli_query($conn1,"select CONCAT('LP/CBD/NAG/',DATE_FORMAT('$tanggal', '%m%y'),'/',LPAD((COALESCE(max(SUBSTR(no_payment,17)),0) + 1),5,0)) nomor from list_payment_cbd WHERE no_payment != 'LP/CBD/NAG/0125/00253' and YEAR(tgl_payment) = YEAR ('$tanggal')");

    while($r=mysqli_fetch_array($sql)){
        echo $r['nomor'];     
        ?>
       <?php        
}

?>