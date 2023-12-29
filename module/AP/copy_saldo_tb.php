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

$sqlx = mysqli_query($conn1,"SELECT to_saldo FROM tbl_bln_tb where from_saldo = '$to_saldo'");
$rowx = mysqli_fetch_array($sqlx);
$saldo_to = isset($rowx['to_saldo']) ? $rowx['to_saldo'] : 0;

$sqlupdate4 = "INSERT into tbl_saldo_tb select * from tbl_saldo_tb_temp";
				
$execute4 = mysqli_query($conn2, $sqlupdate4);


if(!$execute4){	
   die('Error: ' . mysqli_error());	
}else{
	$sqlupdate = "UPDATE saldo_awal_tb
				INNER JOIN tbl_saldo_tb_temp ON tbl_saldo_tb_temp.no_coa = saldo_awal_tb.no_coa
				SET saldo_awal_tb.$saldo_to = tbl_saldo_tb_temp.end_balance
				where saldo_awal_tb.no_coa < '4' and saldo_awal_tb.no_coa != '3.40.01'";
				
	$execute = mysqli_query($conn2, $sqlupdate);


	$sqly = mysqli_query($conn1,"SELECT sum(end_balance) sld_akhir from tbl_saldo_tb_temp where no_coa >= '4' || no_coa = '3.40.01' ORDER BY no_coa asc");
	$rowy = mysqli_fetch_array($sqly);
	$sld_akhir = isset($rowy['sld_akhir']) ? $rowy['sld_akhir'] : 0;


	$sqlupdate2 = "UPDATE saldo_awal_tb
				SET saldo_awal_tb.$saldo_to = $sld_akhir
				where saldo_awal_tb.no_coa = '3.40.01'";
				
	$execute2 = mysqli_query($conn2, $sqlupdate2);

	$sqlupdate3 = "UPDATE saldo_awal_tb
				SET saldo_awal_tb.$saldo_to = $sld_akhir
				where saldo_awal_tb.no_coa = '3.40.01'";
				
	$execute3 = mysqli_query($conn2, $sqlupdate3);


	$queryss3 = "INSERT INTO tbl_log_copsal_tb (copy_user,copy_date,to_saldo)
	VALUES
	('$copy_user', '$copy_date', '$saldo_to')";

	$executess3 = mysqli_query($conn2, $queryss3);

	// if(!$execute3){	
 //   		die('Error: ' . mysqli_error());	
	// }else{
	// 	$sqldelete = "DELETE from tbl_saldo_tb_temp";
				
	// $execute5 = mysqli_query($conn2, $sqldelete);
	// }
}

mysqli_close($conn2);

?>