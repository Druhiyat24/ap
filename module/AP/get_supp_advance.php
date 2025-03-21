<?php
include '../../conn/conn.php';

$no_pco = isset($_POST['no_pco']) ? $_POST['no_pco']: null;

    $sql = mysqli_query($conn1,"select nama_supp from c_petty_cashout_h where no_pco = '$no_pco'");

    while($r=mysqli_fetch_array($sql)){
        echo $r['nama_supp'];     
        ?>
       <?php        
}

?>