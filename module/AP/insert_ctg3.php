<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_idctg1 = $_POST['txt_idctg1'];
$txt_idctg2 = $_POST['txt_idctg2'];
$txt_idctg3 = trim($_POST['txt_idctg3']);
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
if ($txt_idctg3 == '') {
	$id_ctg3 = "123";
echo $id_ctg3;
}
else{
	
$sqlx = mysqli_query($conn2,"select id_ctg3 FROM master_coa_ctg3 where id_ctg3 = '$txt_idctg3' ");
$rowx = mysqli_fetch_array($sqlx);
$id_ctg3 = isset($rowx['id_ctg3']) ? $rowx['id_ctg3'] : NULL;
echo $id_ctg3;
}

if ($id_ctg3 != '') {
	$query = 'select max(id) from master_coa_ctg3';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{

$query = "INSERT INTO master_coa_ctg3 (id_ctg1,id_ctg2,id_ctg3,ind_name,eng_name) 
VALUES 
	('$txt_idctg1', '$txt_idctg2', '$txt_idctg3', '$txt_indname', '$txt_engname')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>