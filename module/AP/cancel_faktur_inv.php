<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$jenis = $_POST['jenis'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");
$status = 'CANCEL';


if(isset($no_dok)){
$sql = "update bpb_faktur_inv set status = '$status',cancel_by = '$cancel_user',cancel_date = '$cancel_date' where no_dok = '$no_dok'";
$execute = mysqli_query($conn2,$sql);	

	if ($jenis == 'FAK') {
		$sql2 = "update bpb_new set upt_dok_faktur = NULL, upt_no_faktur2= NULL, upt_tgl_faktur2= NULL where upt_dok_faktur = '$no_dok'";
		$execute2 = mysqli_query($conn2,$sql2);	
	}else{
		$sql2 = "update bpb_new set upt_dok_inv = NULL,upt_no_inv= NULL, upt_tgl_inv= NULL,upt_no_faktur= NULL, upt_tgl_faktur= NULL where upt_dok_inv = '$no_dok'";
		$execute2 = mysqli_query($conn2,$sql2);	
	}

}else{
	die('Error: ' . mysqli_error());		
}


echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=kontrabon.php');

mysqli_close($conn2);

?>