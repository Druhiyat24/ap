<?php
include '../../../conn/conn.php';
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

$sqlupdate4 = "INSERT into fs_saldo_tb select * from fs_saldo_tb_temp";
				
$execute4 = mysqli_query($conn2, $sqlupdate4);


if(!$execute4){	
   die('Error: ' . mysqli_error());	
}else{
	$sqlupdate = "UPDATE fs_saldo_awal_tb
				INNER JOIN fs_saldo_tb_temp ON fs_saldo_tb_temp.no_coa = fs_saldo_awal_tb.no_coa and fs_saldo_tb_temp.profit_center = fs_saldo_awal_tb.profit_center
				SET fs_saldo_awal_tb.$saldo_to = fs_saldo_tb_temp.end_balance
				where fs_saldo_awal_tb.no_coa < '4' and fs_saldo_awal_tb.no_coa != '3.40.01'";
				
	$execute = mysqli_query($conn2, $sqlupdate);


$sql_nak = mysqli_query($conn1,"SELECT sum(end_balance) sld_akhir from fs_saldo_tb_temp where profit_center = 'NAK' and (no_coa >= '4' || no_coa = '3.40.01') ORDER BY no_coa asc");
$row_nak = mysqli_fetch_array($sql_nak);
$sld_akhir_nak = isset($row_nak['sld_akhir']) ? $row_nak['sld_akhir'] : 0;

$sqlupdate_nak = "UPDATE fs_saldo_awal_tb SET fs_saldo_awal_tb.$saldo_to = $sld_akhir_nak where fs_saldo_awal_tb.no_coa = '3.40.01' and profit_center = 'NAK'";
$execute_nak = mysqli_query($conn2, $sqlupdate_nak);

$sql_nag = mysqli_query($conn1,"SELECT sum(end_balance) sld_akhir from fs_saldo_tb_temp where profit_center = 'NAG' and (no_coa >= '4' || no_coa = '3.40.01') ORDER BY no_coa asc");
$row_nag = mysqli_fetch_array($sql_nag);
$sld_akhir_nag = isset($row_nag['sld_akhir']) ? $row_nag['sld_akhir'] : 0;

$sqlupdate_nag = "UPDATE fs_saldo_awal_tb SET fs_saldo_awal_tb.$saldo_to = $sld_akhir_nag where fs_saldo_awal_tb.no_coa = '3.40.01' and profit_center = 'NAG'";
$execute_nag = mysqli_query($conn2, $sqlupdate_nag);


	$queryss3 = "INSERT INTO tbl_log_copsal_tb (copy_user,copy_date,to_saldo)
	VALUES
	('$copy_user', '$copy_date', '$saldo_to')";

	$executess3 = mysqli_query($conn2, $queryss3);

}

mysqli_close($conn2);

?>