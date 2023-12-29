<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_group = $_POST['txt_group'];
$txt_indname = $_POST['txt_indname'];
$txt_engname = $_POST['txt_engname'];
$status = "Active";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];





// echo $txt_group;
// echo "< -- >";
// echo $txt_indname;
// echo "< -- >";
// echo $txt_engname;
// echo "< -- >";
// echo $status;

	
$sqlx = mysqli_query($conn2,"select ind_name from tbl_master_cashflow where id_group = '$txt_group' and ind_name = '$txt_indname'");
$rowx = mysqli_fetch_array($sqlx);
$id_ctg2 = isset($rowx['ind_name']) ? $rowx['ind_name'] : NULL;
echo $id_ctg2;

if ($id_ctg2 != '') {
	$query = 'select max(id) from tbl_master_cashflow';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{

$query = "INSERT INTO tbl_master_cashflow (id_group, ind_name, eng_name, status,create_by,create_date) 
VALUES 
	('$txt_group', '$txt_indname', '$txt_engname', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>