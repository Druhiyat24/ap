<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$unik_code = $_POST['unik_code'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$jurnal = $_POST['jurnal'];
$nama_supp = $_POST['nama_supp'];
$no_faktur = $_POST['no_faktur'];
$supp_inv = $_POST['supp_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
$pph = $_POST['pph'];
$cash = $_POST['cash'];
$ttl_ro = $_POST['ttl_ro'];
$no_bppb = $_POST['no_bppb'];
$idtax = $_POST['idtax'];
$curr = $_POST['curr'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$create_user = $_POST['create_user'];
$no_bpb = $_POST['no_bpb'];
$no_po = $_POST['no_po'];
$no_ro = $_POST['no_ro'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$tgl_po = date("Y-m-d",strtotime($_POST['tgl_po']));
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_dp = $_POST['sum_dp'];
$sum_pph = $_POST['sum_pph'];
$sum_total = $sum_sub - $sum_pph + $sum_tax;
$status_invoice = 'Invoiced';
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$status_update = 'Cancel';

$sql123 = mysqli_query($conn2,"select no_kbon, no_bpb from kontrabon where no_kbon = '$no_kbon' and no_bpb = '$no_bpb' and status != 'Cancel'");
$row123 = mysqli_fetch_array($sql123);
$dup_kbon = $row123['no_kbon'];
$dup_bpb = $row123['no_bpb'];

$sqlnkb = mysqli_query($conn2,"select no_kbon from kontrabon_h where unik_code = '$unik_code'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['no_kbon'];


if ($dup_kbon != null && $dup_bpb == $no_bpb) {
	echo '';
}else{

$sql1 = mysqli_query($conn2,"select no_kbon from kontrabon_dp where no_bpb = '$no_bpb'");
while($row = mysqli_fetch_array($sql1)){
$kbon = $row['no_kbon'];

if($sum_dp != '0'){
	echo '';
}else{
$sql11 = "update ftr_dp set status='$status_update' where no_po='$no_po'";
$query11 = mysqli_query($conn2,$sql11);

$sql111 = "update kontrabon_dp set status='$status_update' where no_po='$no_po'";
$query111 = mysqli_query($conn2,$sql111);

$sql1111 = "update list_payment_dp set status='$status_update' where no_po='$no_po'";
$query1111 = mysqli_query($conn2,$sql1111);

$sql11111 = "update kontrabon_h_dp set status='$status_update' where no_kbon='$kbon'";
$query11111 = mysqli_query($conn2,$sql11111);

}
}



if(isset($ceklist)){	
$query = "INSERT INTO kontrabon (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb,tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, dp_value, curr, ceklist, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date) 
VALUES 
	('$kode', '$tgl_kbon', '$jurnal', '$nama_supp', '$no_faktur', '$no_bpb', '$no_po', '$tgl_bpb', '$tgl_po', '$supp_inv', '$tgl_inv', '$tgl_tempo', '$sum_sub', '$sum_tax', '$idtax', '$pph', '$sum_pph', '$sum_total', '$sum_dp', '$curr', '$ceklist', '$post_date', '$update_date', '$status', '$status_int', '$create_user', '$create_date', '$start_date', '$end_date')";
$execute = mysqli_query($conn2,$query);
$squerys = mysqli_query($conn2,"update bppb_new set is_invoiced = '$status_invoice', no_kbon = '$no_kbon' where no_ro= '$no_ro'");

if($no_ro != ''){
$queryess = "INSERT INTO return_kb (no_kbon, no_ro, no_bpbrtn, total_ro, status) 
VALUES 
	('$kode', '$no_ro', '$no_bppb', '$ttl_ro', '$status')";
$executeess = mysqli_query($conn2,$queryess);
}else{
	echo '';
}
}

if($execute){

$squery = mysqli_query($conn2,"update bpb_new set is_invoiced = '$status_invoice' where no_bpb= '$no_bpb'");
$squerysss = mysqli_query($conn2,"delete from kontrabon where no_bpb = '' and no_po = '' ");
$sql2 = mysqli_query($conn2,"select no_po from list_payment_cbd where no_po = '$no_po'");
$row = mysqli_fetch_array($sql2);
$nopo = $row['no_po'];
$update_amount = $cash - $sum_dp;
// $sql3 = "update kontrabon_h_cbd set amount_update = '$update_amount' where no_po = '$no_po' and status = 'Approved'";
// $exec = mysqli_query($conn2,$sql3);

$sql1 = "update kartu_hutang set no_kbon='$no_kbon' where no_bpb = '$no_bpb' and no_po='$no_po'";
$query1 = mysqli_query($conn2,$sql1);

$sqlac = "update status set no_kbon='$no_kbon' where no_bpb = '$no_bpb'";
$queryac = mysqli_query($conn2,$sqlac);
}
}
	

// echo $no_kbon;
// echo $tgl_kbon;
// echo $jurnal;
// echo $nama_supp;
// echo $no_faktur;
// echo $supp_inv;
// echo $tgl_inv;
// echo $tgl_tempo;
// echo $pph;
// echo $cash;
// echo $idtax;
// echo $curr;
// echo $ceklist;
// echo $create_date;
// echo $post_date;
// echo $update_date;
// echo $status;
// echo $status_int;
// echo $create_user;
// echo $no_bpb;
// echo $no_po;
// echo $tgl_bpb;
// echo $tgl_po;
// echo $sum_sub;
// echo $sum_tax;
// echo $sum_dp;
// echo $sum_pph;
// echo $sum_total;
// echo $status_invoice;
// echo $start_date;
// echo $end_date;
// echo $status_update;
// echo $no_ro;

//	echo 'Data Berhasil Di Simpan';
//}else{
//    die('Error: ' . mysql_error());	
//}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>