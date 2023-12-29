<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_idctg1 = $_POST['txt_idctg1'];
$txt_idctg2 = $_POST['txt_idctg2'];
$txt_idctg3 = $_POST['txt_idctg3'];
$txt_idctg4 = $_POST['txt_idctg4'];
$txt_idctg5 = trim($_POST['txt_idctg5']);
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
if ($txt_idctg5 == '') {
	$id_ctg5 = "123";
	echo $id_ctg5;
}elseif($txt_idctg5 == '1.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '2.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '3.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '4.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '5.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '6.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '7.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '8.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '9.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '10.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif($txt_idctg5 == '11.'){
	$id_ctg5 = "456";
	echo $id_ctg5;
}elseif(strpos($txt_idctg5, '.') !== false){
	$id_ctg5 = "789";
	echo $id_ctg5;
}
else{
	
$sqlx = mysqli_query($conn2,"select id_ctg5 FROM master_coa_ctg5 where id_ctg5 = '$txt_idctg5' ");
$rowx = mysqli_fetch_array($sqlx);
$id_ctg5 = isset($rowx['id_ctg5']) ? $rowx['id_ctg5'] : NULL;
echo $id_ctg5;
}

if ($id_ctg5 != '') {
	$query = 'select max(id) from master_coa_ctg5';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{


$query = "INSERT INTO master_coa_ctg5 (id_ctg1,id_ctg2,id_ctg3,id_ctg4,id_ctg5,ind_name,eng_name) 
VALUES 
	('$txt_idctg1', '$txt_idctg2', '$txt_idctg3', '$txt_idctg4', '$txt_idctg5', '$txt_indname', '$txt_engname')";

$execute = mysqli_query($conn2,$query);
// echo "Data saved successfully";
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
}

mysqli_close($conn2);
?>