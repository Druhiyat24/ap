<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon_h = $_POST['no_kbon_h'];
$tgl_kbon_h = date("Y-m-d",strtotime($_POST['tgl_kbon_h']));
$no_po_h = $_POST['no_po_h'];
$tgl_po_h = date("Y-m-d",strtotime($_POST['tgl_po_h']));
$nama_supp_h = $_POST['nama_supp_h'];
$no_faktur_h = $_POST['no_faktur_h'];
$supp_inv_h = $_POST['supp_inv_h'];
$tgl_inv_h = date("Y-m-d",strtotime($_POST['tgl_inv_h']));
$tgl_tempo_h = date("Y-m-d",strtotime($_POST['tgl_tempo_h']));
$pph_h = $_POST['pph_h'];
$curr_h = $_POST['curr_h'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$create_user_h = $_POST['create_user_h'];
$sub_h = $_POST['sub_h'];
$tax_h = $_POST['tax_h'];
$pph_h = $_POST['pph_h'];
$total_h = $_POST['total_h'];
$balance = $total_h;
	
$query = "INSERT INTO kontrabon_h_cbd (no_po, tgl_po ,no_kbon, tgl_kbon, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph, total, amount_update, balance, curr, post_date, update_date, status, create_user, create_date) 
VALUES 
	('$no_po_h', '$tgl_po_h', '$no_kbon_h', '$tgl_kbon_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h', '$total_h', '$total_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date')";
$execute = mysqli_query($conn2,$query);


// echo $no_kbon_h;
// echo $tgl_kbon_h;
// echo $nama_supp_h;
// echo $no_faktur_h;
// echo $supp_inv_h;
// echo $tgl_tempo_h;
// echo $pph_h;
// echo $curr_h;
// echo $create_date;
// echo $post_date;
// echo $update_date;
// echo $status;
// echo $create_user_h;
// echo $sub_h;
// echo $tax_h;
// echo $pph_h;
// echo $total_h;
// echo $balance;

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