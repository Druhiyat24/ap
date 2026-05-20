<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$v_idgroup = $_POST['id'];
$username = $_POST['username'];
$last_update = date("Y-m-d H:i:s");


$query_copy1 = "insert into cancel_masterrate select * from masterrate where v_idgroup = '$v_idgroup'";
$execute_copy1 = mysqli_query($conn2,$query_copy1);

$query_copy2 = "insert into cancel_ap_masterrate select * from ap_masterrate where v_idgroup = '$v_idgroup'";
$execute_copy2 = mysqli_query($conn2,$query_copy2);

if ($execute_copy2) {
	
$query = "INSERT INTO log_masterrate (v_idgroup, username, activity, updated_at) 
VALUES 
	('$v_idgroup', '$username', 'CANCEL', '$last_update')";

$execute = mysqli_query($conn2,$query);

	if($execute){
		$query_del1 = "delete from masterrate where v_idgroup = '$v_idgroup'";
		$execute_del1 = mysqli_query($conn2,$query_del1);

		$query_del2 = "delete from ap_masterrate where v_idgroup = '$v_idgroup'";
		$execute_del2 = mysqli_query($conn2,$query_del2);
	}

}

mysqli_close($conn2);
?>