<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrdp = $_POST['noftrdp'];
$tglftrdp = date("Y-m-d",strtotime($_POST['tglftrdp']));
$nama_supp = $_POST['nama_supp'];
$no_pi = $_POST['no_pi'];
$curr = $_POST['curr'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$keterangan = $_POST['keterangan'];
$create_user = $_POST['create_user'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$total = $_POST['total'];
$dp_code = $_POST['dp_code'];
$dp_value = $_POST['dp_value'];
$balance = $_POST['balance'];
$invoiced = 'Waiting';

// echo $noftrdp;
// echo $tglftrdp;
// echo $nama_supp;
// echo $no_pi;
// echo $curr;
// echo $create_date;
// echo $status;
// echo $create_user;
// echo $no_bpb;
// echo $tgl_bpb;
// echo $total;
// echo $dp_code;
// echo $dp_value;
// echo $balance;
if ($no_po != '') {
$query = "INSERT INTO ftr_dp (no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, total, dp, dp_value, balance, curr, keterangan, status, is_invoiced, create_user, create_date)
VALUES 
	('$noftrdp', '$tglftrdp', '$nama_supp', '$no_po', '$tgl_po', '$no_pi', '$total', '$dp_code', '$dp_value', '$balance', '$curr', '$keterangan', '$status', '$invoiced', '$create_user', '$create_date')";
	}	else{
		echo '';
	}
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>