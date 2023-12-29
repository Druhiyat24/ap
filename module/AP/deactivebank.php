<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$active_date = date("Y-m-d H:i:s");
$active_user = $_POST['active_user'];
$status = 'Deactive';

if(isset($doc_number)){
$sql = "update b_masterbank set status = '$status', non_active_by = '$active_user', non_active_date = '$active_date' where doc_number = '$doc_number'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}


mysqli_close($conn2);

?>