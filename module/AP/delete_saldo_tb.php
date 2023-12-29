<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_coa = $_POST['no_coa'];
$beg_balance = $_POST['beg_balance'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$end_balance = $_POST['end_balance'];
$copy_date = date("Y-m-d H:i:s");
$copy_user = $_POST['copy_user'];
$to_saldo = $_POST['to_saldo'];


$sqldelete = "DELETE from tbl_saldo_tb_temp";
				
$execute5 = mysqli_query($conn2, $sqldelete);

mysqli_close($conn2);

?>