<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_co = $_POST['no_co'];
$tgl_co =  date("Y-m-d",strtotime($_POST['tgl_co']));
$type_co = $_POST['type_co'];
$dokumen = $_POST['dokumen'];
$no_coa = $_POST['no_coa'];
$no_costcenter = $_POST['no_costcenter'];
$buyer = $_POST['buyer'];
$ws = $_POST['ws'];
$req_by = $_POST['req_by'];
$curr = "IDR";
$amount = $_POST['amount'];
$deskrip = $_POST['deskrip'];
$status = "Draft";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];
$stat_pci = "N";





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;


if($amount != ''){
$query = "INSERT INTO c_cash_out (no_co, tgl_co, type_co, dokumen, no_coa, no_costcenter, buyer, ws, req_by, curr, amount, deskrip, status, create_date, create_by,stat_pci) 
VALUES 
	('$no_co', '$tgl_co', '$type_co', '$dokumen', '$no_coa','$no_costcenter', '$buyer', '$ws', '$req_by', '$curr','$amount', '$deskrip', '$status', '$create_date', '$create_user', '$stat_pci')";

$execute = mysqli_query($conn2,$query);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>