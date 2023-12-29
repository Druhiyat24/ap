<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$tgl_payment = date("Y-m-d",strtotime($_POST['tgl_payment']));
$no_kbon = $_POST['no_kbon'];
$total_kbon = $_POST['total_kbon'];
$amount = $_POST['amount'];
$nama_supp = $_POST['nama_supp'];
$tgl_pengajuan = date("Y-m-d",strtotime($_POST['tgl_pengajuan']));
$nama_pengaju = $_POST['nama_pengaju'];
$pesan = $_POST['pesan'];
$status = 'Waiting';
$status_int = 3;
$users = '-';

// $name = explode('.', $file);
// $path = "files/".$file;
 // $lokasi_file = $_FILES;
 //  $nama_file = $_POST['file'];
 //  $direktori = "files/$nama_file";
// $sql = "select balance from kontrabon_h where no_kbon = '$no_kbon'";
// $exec = mysql_query($sql,$conn2);
// while($row = mysql_fetch_array($exec)){
// 	$balance = $row['balance'];
// }

// $sum_balance = $balance - $amount;

// echo $no_payment;
// echo "///";
// echo $tgl_payment;
// echo "///";
// echo $no_kbon;
// echo "///";
// echo $total_kbon;
// echo "///";
// echo $amount;
// echo "///";

// echo $nama_supp;
// echo "///";
// echo $tgl_pengajuan;
// echo "///";
// echo $nama_pengaju;
// echo "///";
// echo $pesan;
// echo "///";
// echo $status;
// echo "///";
// echo $status_int;
// echo "///";
// echo $users;
// echo "///";
// echo $users;

if ($no_payment != '') {	
$query = "INSERT INTO pengajuan_payment (no_payment, tgl_payment, no_kbon, nama_supp, total_kbon, total_amount, tgl_pengajuan, nama_pengaju, pesan, status, approved_user, cancel_user) 
VALUES 
	('$no_payment', '$tgl_payment', '$no_kbon', '$nama_supp', '$total_kbon', '$amount',  '$tgl_pengajuan', '$nama_pengaju', '$pesan', '$status', '$users', '$users')";
$execute = mysqli_query($conn2,$query);
}else{
		echo '';
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update list_payment set status_int = '$status_int', status = '$status' where no_payment= '$no_payment'";
	$exec = mysqli_query($conn2,$sql);
}


mysqli_close($conn2);
?>
