<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$user = isset($_POST['user']) ? $_POST['user']: null;
$no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;

$sql = mysqli_query($conn1,"select a.no_bpb,if(no_inv is null OR no_inv = '',upt_no_inv,no_inv) no_inv,DATE_FORMAT(if(no_inv is null OR no_inv = '',upt_tgl_inv,tgl_inv), '%d-%m-%Y') tgl_inv,no_faktur,DATE_FORMAT(tgl_faktur, '%Y-%m-%d')tgl_faktur from (select no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input,no_inv,tgl_inv,no_faktur,tgl_faktur from tbl_bpb_temp where user_input = '$user' and no_bpb = '$no_bpb' GROUP BY no_bpb) a LEFT JOIN (select no_bpb,upt_no_inv,upt_tgl_inv from bpb_new where upt_no_inv is not null GROUP BY no_bpb) b on b.no_bpb = a.no_bpb");

$data=mysqli_fetch_row($sql);
echo json_encode($data);  


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>