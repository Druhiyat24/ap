<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

// echo "< -- >";
// echo $no_kbon;
$cek_status =  mysqli_query($conn2,"select status from tbl_pv_h where no_pv = '$no_pv'");
$row= mysqli_fetch_assoc($cek_status); 
$status = $row['status'];

if ($status == 'Draft') {

$querys = "INSERT INTO tbl_log_edit_pv (no_pv,user_edit,tgl_edit) 
VALUES 
	('$no_pv', '$create_user', '$create_date')";

$query = "insert into tbl_edit_pv_h (select * from tbl_pv_h where no_pv = '$no_pv') ";
$queryss = "insert into tbl_edit_pv (select * from tbl_pv where no_pv = '$no_pv') ";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$querys);
$executess = mysqli_query($conn2,$queryss);
}else{

}

echo $status;


if(!$execute){	
   // die('Error: ' . mysqli_error());	
}else{
	$sql2 = "delete from tbl_pv_h where no_pv = '$no_pv'";
	$query2 = mysqli_query($conn2,$sql2);

	$sql3 = "delete from tbl_pv where no_pv = '$no_pv'";
	$query3 = mysqli_query($conn2,$sql3);
	
}

mysqli_close($conn2);
?>