<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_idctg1 = trim($_POST['txt_idctg1']);
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

if ($txt_idctg1 == '') {
	$id_ctg1 = "123";
echo $id_ctg1;
}
else{
	
$sqlx = mysqli_query($conn2,"select id_ctg1 FROM master_coa_ctg1 where id_ctg1 = '$txt_idctg1' ");
$rowx = mysqli_fetch_array($sqlx);
$id_ctg1 = isset($rowx['id_ctg1']) ? $rowx['id_ctg1'] : NULL;
echo $id_ctg1;
}

if ($id_ctg1 != '') {
	$query = 'select max(id) from master_coa_ctg1';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{
$query = "INSERT INTO master_coa_ctg1 (id_ctg1,ind_name,eng_name) 
VALUES 
	('$txt_idctg1', '$txt_indname', '$txt_engname')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>