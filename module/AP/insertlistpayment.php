<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$tgl_payment = date("Y-m-d",strtotime($_POST['tgl_payment']));
$nama_supp = $_POST['nama_supp'];
$no_kbon = $_POST['no_kbon'];
$no_bpb = $_POST['no_bpb'];
$tgl_bpb = $_POST['tgl_bpb'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$pph_value = $_POST['pph_value'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$curr = $_POST['curr'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$keterangan = $_POST['keterangan'];
$create_user = $_POST['create_user'];
$total_kbon = $_POST['total_kbon'];
$top = $_POST['top'];
$outstanding = $_POST['outstanding'];
$amount = $_POST['amount'];
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
$duedate = date("Y-m-d",strtotime($_POST['duedate']));
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$balance = $_POST['balance'];
$pph = '-';
$no_coa = $_POST['no_coa'];
$nama_coa = $_POST['nama_coa'];

// $sql = "select balance from kontrabon_h where no_kbon = '$no_kbon'";
// $exec = mysql_query($sql,$conn2);
// while($row = mysql_fetch_array($exec)){
// 	$balance = $row['balance'];
// }

// $sum_balance = $balance - $amount;

// echo $no_payment;
// echo "||";
// echo $tgl_payment;
// echo "||";
// echo $nama_supp;
// echo "||";
// echo $no_kbon;
// echo "||";
// echo $tgl_kbon;
// echo "||";
// echo $no_bpb;
// echo "||";
// echo $tgl_bpb;
// echo "||";
// echo $no_po;
// echo "||";
// echo $tgl_po;
// echo "||";
// echo $pph_value;
// echo "||";
// echo $total_kbon;
// echo "||";
// echo $outstanding;
// echo "||";
// echo $amount;
// echo "||";
// echo $curr;
// echo "||";
// echo $top;
// echo "||";
// echo $tgl_tempo;
// echo "||";
// echo $keterangan;
// echo "||";
// echo $status;
// echo "||";
// echo $status_int;
// echo "||";
// echo $create_user;
// echo "||";
// echo $create_date;
// echo "||";
// echo $post_date;
// echo "||";
// echo $update_date;
// echo "||";

	
$query = "INSERT INTO list_payment (no_payment, tgl_payment, nama_supp, no_kbon, tgl_kbon, no_bpb, tgl_bpb, no_po, tgl_po, pph_value, total_kbon, outstanding, amount, curr, top, tgl_tempo, memo, status,status_int, create_user, create_date, post_date, update_date, duedate_onkb, no_coa, nama_coa) 
VALUES 
	('$no_payment', '$tgl_payment', '$nama_supp', '$no_kbon', '$tgl_kbon', '$no_bpb', '$tgl_bpb', '$no_po', '$tgl_po', '$pph_value', '$total_kbon', '$outstanding', '$amount', '$curr', '$top', '$duedate', '$keterangan', '$status', '$status_int', '$create_user', '$create_date', '$post_date', '$update_date', '$tgl_tempo', '$no_coa', '$nama_coa')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update kontrabon_h set balance = '$balance' where no_kbon= '$no_kbon'";
	$exec = mysqli_query($conn2,$sql);

	$sqlll = "update kontrabon set lp_inv = '1' where no_kbon= '$no_kbon'";
	$execll = mysqli_query($conn2,$sqlll);

	$sqlac = "update status set no_lp ='$no_payment',tgl_lp = '$tgl_payment' where no_kbon = '$no_kbon'";
	$queryac = mysqli_query($conn2,$sqlac);
}

mysqli_close($conn2);
?>