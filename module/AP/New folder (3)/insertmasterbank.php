<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_active =  date("Y-m-d",strtotime($_POST['tgl_active']));
$tgl_active2 =  date("Y-m-d H:i:s",strtotime($_POST['tgl_active']));
$no_doc = $_POST['no_doc'];
$sob = $_POST['sob'];
$account = $_POST['account'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$pesan = $_POST['pesan'];
$maxid = $_POST['maxid'];
$status = "Active";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

// echo $tgl_active;
// echo "< -- >";
// echo $no_doc;
// echo "< -- >";
// echo $sob;
// echo "< -- >";
// echo $account;
// echo "< -- >";
// echo $bank;
// echo "< -- >";
// echo $curr;
// echo "< -- >";
// echo $pesan;
// echo "< -- >";
// echo $status;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $create_date;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $tgl_active2;


$query = "INSERT INTO b_masterbank (id, active_date, doc_number, sob, bank_account, bank_name, curr, deskripsi, status, create_by, create_date, active_by, date_active) 
VALUES 
	('$maxid', '$tgl_active', '$no_doc', '$sob', '$account', '$bank', '$curr', '$pesan', '$status', '$create_user', '$create_date', '$create_user', '$tgl_active2')";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>