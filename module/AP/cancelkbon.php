<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");
$status_int = 1;


if(isset($no_kbon)){
$sql = "update bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb set bpb_new.is_invoiced = 'Waiting' where kontrabon.no_kbon = '$no_kbon'";
$execute = mysqli_query($conn2,$sql);	

$sqljrnl = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_kbon')";
$queryjrnl = mysqli_query($conn2,$sqljrnl);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
	$query = mysqli_query($conn2,"update kontrabon set status = 'Cancel', status_int = '$status_int', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
	$query2 = mysqli_query($conn2,"update kontrabon_h set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
	$query3 = mysqli_query($conn2,"update potongan set status = 'Cancel' where no_kbon = '$no_kbon'");

	$query4 = mysqli_query($conn2,"update bppb_new set is_invoiced = 'Waiting', no_kbon = null where no_kbon = '$no_kbon'");
	// $query3 = mysqli_query($conn2,"delete from detail where no_kbon = '$no_kbon'");

	$sql1 = mysqli_query($conn2,"select list_payment_cbd.no_kbon as no_kbon, list_payment_cbd.no_po as no_po, kontrabon_h.dp_value as amount, list_payment_cbd.amount_update as balance from list_payment_cbd inner join kontrabon_h on kontrabon_h.no_po = list_payment_cbd.no_po where kontrabon_h.no_kbon = '$no_kbon' group by kontrabon_h.no_kbon");
$row = mysqli_fetch_array($sql1);
$no_po = $row['no_po'];
$amount = $row['amount'];
$balance = $row['balance'];
$update_balance = $balance + $amount;
$sql2 = "update kontrabon_h_cbd set amount_update = '$update_balance' where no_po = '$no_po' and status = 'Approved' ";
$exec = mysqli_query($conn2,$sql2);

$sql111 = "update kartu_hutang set no_kbon='-' where no_kbon = '$no_kbon'";
$query111 = mysqli_query($conn2,$sql111);

$sqlac = "update status set no_kbon = null where no_kbon = '$no_kbon'";
$queryac = mysqli_query($conn2,$sqlac);

$sql59 = "update return_kb set status='Cancel' where no_kbon = '$no_kbon'";
$query59 = mysqli_query($conn2,$sql59);

$sqljrnl2 = "Delete from tbl_list_journal where no_journal='$no_kbon'";
$queryjrnl2 = mysqli_query($conn2,$sqljrnl2);	
}


echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=kontrabon.php');

mysqli_close($conn2);

?>