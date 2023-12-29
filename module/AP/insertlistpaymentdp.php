<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$tgl_payment = date("Y-m-d",strtotime($_POST['tgl_payment']));
$nama_supp = $_POST['nama_supp'];
$no_kbon = $_POST['no_kbon'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$pph_value = $_POST['pph_value'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$curr = $_POST['curr'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$keterangan = $_POST['keterangan'];
$create_user = $_POST['create_user'];
$total_kbon = $_POST['total_kbon'];
// $top = $_POST['top'];
$outstanding = $_POST['outstanding'];
$amount = $_POST['amount'];
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$balance = $_POST['balance'];

// $sql = "select balance from kontrabon_h where no_kbon = '$no_kbon'";
// $exec = mysql_query($sql,$conn2);
// while($row = mysql_fetch_array($exec)){
// 	$balance = $row['balance'];
// }

// $sum_balance = $balance - $amount;

// echo $no_payment;
// echo $tgl_payment;
// echo $nama_supp;
// echo $no_kbon;
// echo $tgl_kbon;
// echo $curr;
// echo $create_date;
// echo $status;
// echo $create_user;
// echo $keterangan;
// echo $total_kbon;
// echo $top;
// echo $outstanding;
// echo $amount;
// echo $tgl_tempo;
// echo $post_date;
// echo $update_date;
// echo $balance;

	
$query = "INSERT INTO list_payment_dp (no_payment, tgl_payment, nama_supp, no_kbon, tgl_kbon, no_po, tgl_po, pph_value, total_kbon, outstanding, amount, curr, tgl_tempo, memo, status,status_int, create_user, create_date, post_date, update_date) 
VALUES 
	('$no_payment', '$tgl_payment', '$nama_supp', '$no_kbon', '$tgl_kbon', '$no_po', '$tgl_po', '$pph_value', '$total_kbon', '$outstanding', '$amount', '$curr', '$tgl_tempo', '$keterangan', '$status', '$status_int', '$create_user', '$create_date', '$post_date', '$update_date')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update kontrabon_h_dp set balance = '$balance' where no_kbon= '$no_kbon'";
	$exec = mysqli_query($conn2,$sql);

	$sqlll = "update kontrabon_dp set lp_inv = '1' where no_kbon= '$no_kbon'";
	$execll = mysqli_query($conn2,$sqlll);
}

mysqli_close($conn2);
?>