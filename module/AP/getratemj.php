<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$tgl_doc = isset($_POST['tgl_doc']) ? $_POST['tgl_doc']: null;

    $sql = mysqli_query($conn1,"select DISTINCT IF(rate like ',',ROUND(rate,2),rate) as rate FROM masterrate where tanggal = '$tgl_doc' and v_codecurr = 'PAJAK'");

    while($r=mysqli_fetch_array($sql)){
        echo $r['rate'];     
        ?>
       <?php        
}


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>