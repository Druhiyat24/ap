<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$id = isset($_POST['id']) ? $_POST['id']: null;

    $sql = mysqli_query($conn1,"select CONCAT('$id','.',if(if(max(SUBSTR(no_coa,6,2) + 1) > 9, max(SUBSTR(no_coa,6,2) + 1),CONCAT('0',max(SUBSTR(no_coa,6,2) + 1))) > 99,'00',if(max(SUBSTR(no_coa,6,2) + 1) > 9, max(SUBSTR(no_coa,6,2) + 1),CONCAT('0',max(SUBSTR(no_coa,6,2) + 1))))) as coa_num from mastercoa_v3 where id_ctg5 like '$id%'");

   $r=mysqli_fetch_array($sql);
        $nomor = $r['coa_num'];
        echo $nomor;?>
        <?php        



// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>