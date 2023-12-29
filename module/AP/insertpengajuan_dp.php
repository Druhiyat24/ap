<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$no_dp = $_POST['no_dp'];
$nama_supp = $_POST['nama_supp'];
$tgl_pengajuan = date("Y-m-d",strtotime($_POST['tgl_pengajuan']));
$nama_pengaju = $_POST['nama_pengaju'];
$pesan = $_POST['pesan'];
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

// echo $no_bpb;
// echo "----------";
// echo $nama_supp;
// echo "----------";
// echo $tgl_pengajuan;
// echo "----------";
// echo $nama_pengaju;
// echo "----------";
// echo $pesan;
// echo "----------";
// echo "<pre>";
// print_r($file);
// echo "</pre>";
// echo $status;
// // echo $create_user;
// echo $keterangan;
// echo $total_kbon;
// echo $top;
// echo $outstanding;
// echo $amount;
// echo $tgl_tempo;
// echo $post_date;
// echo $update_date;
// echo $balance;

if ($no_kbon != '') {	
$query = "INSERT INTO pengajuan_kb_dp (no_kbon, no_dp, nama_supp, tgl_pengajuan, nama_pengaju, pesan, status, approved_user, cancel_user) 
VALUES 
	('$no_kbon', '$no_dp', '$nama_supp', '$tgl_pengajuan', '$nama_pengaju', '$pesan', '$status2', '$users', '$users')";
$execute = mysqli_query($conn2,$query);
}else{
		echo '';
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update kontrabon_dp set status_int = '$status_int', status = '$status' where no_kbon= '$no_kbon'";
	$exec = mysqli_query($conn2,$sql);
}


mysqli_close($conn2);
?>