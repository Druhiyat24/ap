<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_mj = $_POST['no_mj'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

// echo "< -- >";
// echo $no_kbon;
$cek_status =  mysqli_query($conn2,"select status from tbl_memorial_journal where no_mj = '$no_mj'");
$row= mysqli_fetch_assoc($cek_status); 
$status = $row['status'];

if ($status == 'Post') {

$querys = "INSERT INTO tbl_log_edit_mj (no_mj,user_edit,tgl_edit) 
VALUES 
	('$no_mj', '$create_user', '$create_date')";

$query = "insert into tbl_edit_mj (select * from tbl_memorial_journal where no_mj = '$no_mj') ";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$querys);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal = '$no_mj')";
$execute2 = mysqli_query($conn2,$sql2);
}else{

}

echo $status;


if(!$execute){	
   // die('Error: ' . mysqli_error());	
}else{
	$sql2 = "delete from tbl_memorial_journal where no_mj = '$no_mj'";
	$query2 = mysqli_query($conn2,$sql2);

	$sql3 = "delete from tbl_list_journal where no_journal = '$no_mj'";
	$query3 = mysqli_query($conn2,$sql3);
	
}

mysqli_close($conn2);
?>