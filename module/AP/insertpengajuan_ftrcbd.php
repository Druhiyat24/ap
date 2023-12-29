<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_cbd = $_POST['no_cbd'];
$tgl_cbd = date("Y-m-d",strtotime($_POST['tgl_cbd']));
$no_po = $_POST['no_po'];
$total = $_POST['total'];
$curr = $_POST['curr'];
$nama_supp = $_POST['nama_supp'];
$tgl_pengajuan = date("Y-m-d",strtotime($_POST['tgl_pengajuan']));
$nama_pengaju = $_POST['nama_pengaju'];
$pesan = $_POST['pesan'];
$status = 'Waiting';
// $status_int = 3;
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

// echo $no_cbd;
// echo "///";
// echo $tgl_cbd;
// echo "///";
// echo $no_po;
// echo "///";
// echo $total;
// echo "///";
// echo $curr;
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

if ($no_cbd != '') {
		$query = "INSERT INTO pengajuan_ftrcbd (no_cbd, tgl_cbd, no_po, nama_supp, total, curr, tgl_pengajuan, nama_pengaju, pesan, status, approved_user, cancel_user) 
VALUES 
	('$no_cbd', '$tgl_cbd', '$no_po', '$nama_supp', '$total', '$curr',  '$tgl_pengajuan', '$nama_pengaju', '$pesan', '$status', '$users', '$users')";
$execute = mysqli_query($conn2,$query);
	}else{
		echo '';
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update ftr_cbd set status = '$status' where no_ftr_cbd= '$no_cbd' and no_po = '$no_po'";
	$exec = mysqli_query($conn2,$sql);
}


mysqli_close($conn2);
?>
