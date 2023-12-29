<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$no_ro = $_POST['no_ro'];
$no_bpb = $_POST['no_bpb'];
$nama_supp = $_POST['nama_supp'];
$tgl_pengajuan = date("Y-m-d",strtotime($_POST['tgl_pengajuan']));
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$nama_pengaju = $_POST['nama_pengaju'];
$pesan = $_POST['pesan'];
$total = $_POST['total'];
$status = 'Waiting';
$status2 = 'Draft';
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

// echo $no_kbon;
// echo "----------";
// echo $tgl_kbon;
// echo "----------";
// echo $no_bpb;
// echo "----------";
// echo $tgl_bpb;
// echo "----------";
// echo $nama_supp;
// echo "----------";
// echo $total;
// echo "----------";
// echo $tgl_pengajuan;
// echo "----------";
// echo $nama_pengaju;
// echo "----------";
// echo $pesan;
// echo "----------";
// echo $status2;
// echo "----------";
// echo $users;
// echo "----------";
// echo $no_bpb;

if ($no_kbon != '') {	
$query = "INSERT INTO pengajuan_kb (no_kbon,tgl_kbon, no_bpb, nama_supp,total, tgl_pengajuan, nama_pengaju, pesan, status, approved_user, cancel_user) 
VALUES 
	('$no_kbon', '$tgl_kbon', '$no_bpb', '$nama_supp', '$total', '$tgl_pengajuan', '$nama_pengaju', '$pesan', '$status2', '$users', '$users')";
$execute = mysqli_query($conn2,$query);
}else{
		echo '';
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update kontrabon set status_int = '$status_int', status = '$status' where no_bpb= '$no_bpb'";
	$exec = mysqli_query($conn2,$sql);
}


mysqli_close($conn2);
?>