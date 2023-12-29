<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_ro = $_POST['no_ro'];
$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$nama_supp = $_POST['nama_supp'];
$jml_return = $_POST['jml_return'];
$lr_kurs = $_POST['lr_kurs'];
$s_qty = $_POST['s_qty'];
$s_harga = $_POST['s_harga'];
$materai = $_POST['materai'];
$pot_beli = $_POST['pot_beli'];
$moq = $_POST['moq'];
$jml_potong = $_POST['jml_potong'];
$status_invoice = 'Invoiced';
$status = 'draft';



	$query = "INSERT INTO potongan (no_kbon, tgl_kbon, nama_supp, jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, moq, jml_potong, status) 
VALUES 
	('$no_kbon','$tgl_kbon', '$nama_supp', '$jml_return', '$lr_kurs', '$s_qty', '$s_harga', '$materai', '$pot_beli', '$moq', '$jml_potong', '$status')";
$execute = mysqli_query($conn2,$query);


if($execute){

$squery = mysqli_query($conn2,"update bppb_new set is_invoiced = '$status_invoice' where no_ro= '$no_ro'");
}
	


// echo $no_po;
// echo $tgl_kbon_h;
// // echo $nama_supp_h;
// // echo $no_faktur_h;
// // echo $supp_inv_h;
// // echo $tgl_tempo_h;
// // echo $sub_h;
// // echo $tax_h;
// // echo $pph_h;
// // echo $total_h;
// // echo $balance;
// echo $dp_h;
// // echo $curr_h;
// // echo $create_date;
// // echo $post_date;
// // echo $update_date;
// // echo $status;
// // echo $create_user_h;

// if($execute){

// $squery = mysql_query("update bpb set is_invoiced = '$status_invoice' where no_bpb= '$no_bpb'",$conn2);	

// }
//	echo 'Data Berhasil Di Simpan';
//}else{
//    die('Error: ' . mysql_error());	
//}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>