<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$jurnal = $_POST['jurnal'];
$nama_supp = $_POST['nama_supp'];
$no_faktur = $_POST['no_faktur'];
$supp_inv = $_POST['supp_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
// $pph = $_POST['pph'];
// $idtax = $_POST['idtax'];
$curr = $_POST['curr'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$create_user = $_POST['create_user'];
$no_dp = $_POST['no_dp'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$sum_sub = $_POST['sum_sub'];
$sum_dp = $_POST['sum_dp'];
// $sum_pph = $_POST['sum_pph'];
$sum_total = $_POST['sum_total'];
$status_invoice = 'Invoiced';
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$nol = 0;


// echo $no_bpb;
// echo $tgl_bpb;
// // echo $jurnal;
// echo $nama_supp;
// echo $no_faktur;
// echo $no_dp;
// echo $no_po;
// echo $tgl_po;
// echo $supp_inv;
// echo $tgl_inv;
// echo $tgl_tempo;
// echo $sum_sub;
// echo $sum_dp;
// echo $sum_total;
// echo $curr;
// echo $ceklist;
// echo $post_date;
// echo $update_date;
// echo $status;
// echo $status_int;
// echo $create_user;
// echo $create_date;
// echo $start_date;
// echo $end_date;

if(isset($ceklist)){	
$query = "INSERT INTO kontrabon_dp (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_dp, no_po, tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, dp_value, total, curr, ceklist,pph_value, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date) 
VALUES 
	('$no_kbon', '$tgl_kbon', '$jurnal', '$nama_supp', '$no_faktur', '$no_dp', '$no_po', '$tgl_po', '$supp_inv', '$tgl_inv', '$tgl_tempo', '$sum_sub', '$sum_dp', '$sum_total', '$curr', '$ceklist', '$nol', '$post_date', '$update_date', '$status', '$status_int', '$create_user', '$create_date', '$start_date', '$end_date')";
$execute = mysqli_query($conn2,$query);
}

if($execute){

$squery = mysqli_query($conn2,"update ftr_dp set is_invoiced = '$status_invoice', kb_inv = '1' where no_ftr_dp= '$no_dp'");	

}

// echo $sum_sub;

//	echo 'Data Berhasil Di Simpan';
//}else{
//    die('Error: ' . mysql_error());	
//}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>