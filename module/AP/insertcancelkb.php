<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$nama_supp = $_POST['nama_supp'];
$tgl_pengajuan = date("Y-m-d",strtotime($_POST['tgl_pengajuan']));
$nama_pengaju = $_POST['nama_pengaju'];
$pesan = $_POST['pesan'];
$status = 2;


// $sql = "select balance from kontrabon_h where no_kbon = '$no_kbon'";
// $exec = mysql_query($sql,$conn2);
// while($row = mysql_fetch_array($exec)){
// 	$balance = $row['balance'];
// }

// $sum_balance = $balance - $amount;

// echo $no_kbon;
// echo "----------";
// echo $nama_supp;
// echo "----------";
// echo $tgl_pengajuan;
// echo "----------";
// echo $nama_pengaju;
// echo "----------";
// echo $pesan;
// echo "----------";
// echo $file;
// echo "----------";
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

	
$query = "INSERT INTO cancel_kb (no_kbon, nama_supp, tgl_pengajuan, nama_pengaju, pesan) 
VALUES 
	('$no_kbon', '$nama_supp', '$tgl_pengajuan', '$nama_pengaju', '$pesan')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update kontrabon set status_int = '$status', status = 'Recall' where no_kbon= '$no_kbon'";
	$exec = mysqli_query($conn2,$sql);
}

mysqli_close($conn2);
?>