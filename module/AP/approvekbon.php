<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$supp = $_POST['supp'];
$pph = $_POST['pph'];
$curr = $_POST['curr'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$no_po = $_POST['no_po'];
$no_bpb = $_POST['no_bpb'];
$total = $_POST['total'];
$tgl_po =date("Y-m-d",strtotime($_POST['tgl_po']));
$confirm_date = date("Y-m-d H:i:s");
$confirm_date2 = date("Y-m-d");
$status = 'Approved';
$status_int = 4;
$approve_user = $_POST['approve_user'];
// $cek = 4;


$sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
$rowy = mysqli_fetch_array($sqly);
$rate = $rowy['rate'];
$tglrate = $rowy['tanggal'];

$ttl_konversi = $total * $rate;


$sql = mysqli_query($conn2,"select * from kontrabon where no_kbon = '$no_kbon'");

if($no_kbon == ''){
	echo '';
}elseif($curr == 'IDR'){
while($row= mysqli_fetch_assoc($sql)) {
$po = $row['no_po'];
$bpb = $row['no_bpb'];
$kbon = $row['no_kbon'];
$tglkbon = $row['tgl_kbon'];
$supp_inv = $row['supp_inv'];
$tgl_inv = $row['tgl_inv'];
$no_faktur = $row['no_faktur'];
$tgl_tempo = $row['tgl_tempo'];
$pph_value = $row['pph_value'];

$sql = "update kontrabon set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status', status_int='$status_int' where no_kbon='$kbon'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon_h set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);

$sqls = "update potongan set status = 'Approved' where no_kbon = '$no_kbon'";
$querys = mysqli_query($conn2,$sqls);

$sqlssas = "update return_kb set status = '$status' where no_kbon = '$no_kbon'";
$queryssas = mysqli_query($conn2,$sqlssas);

$sql1 = "update kartu_hutang set no_kbon='$kbon', tgl_kbon='$tglkbon', supp_inv='$supp_inv', tgl_inv='$tgl_inv', no_faktur='$no_faktur', tgl_tempo='$tgl_tempo', create_date='$confirm_date2', pph='$pph_value' where no_kbon = '$no_kbon'";
$query1 = mysqli_query($conn2,$sql1);

$sqlac = "update status set no_kbon='$kbon', tgl_kbon='$tglkbon' where no_kbon = '$no_kbon'";
$queryac = mysqli_query($conn2,$sqlac);

$sqljrnl = "update tbl_list_journal set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_journal='$no_kbon'";
$queryjrnl = mysqli_query($conn2,$sqljrnl);

// $sql1 = "update detail set cek = '$cek' where no_kbon='$kbon'";
// $query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=pengajuankb.php');
}
}else{
while($row= mysqli_fetch_assoc($sql)) {
$po = $row['no_po'];
$bpb = $row['no_bpb'];
$kbon = $row['no_kbon'];
$tglkbon = $row['tgl_kbon'];
$supp_inv = $row['supp_inv'];
$tgl_inv = $row['tgl_inv'];
$no_faktur = $row['no_faktur'];
$tgl_tempo = $row['tgl_tempo'];
$pph_value = $row['pph_value'];

$sqlz = mysqli_query($conn2,"select credit_usd  FROM kartu_hutang where no_bpb = '$bpb' and no_po = '$po' and no_kbon = '$kbon' ");
$rowz = mysqli_fetch_array($sqlz);
$credit_usd = $rowz['credit_usd'];
$credit_idr = $credit_usd * $rate;

$sql = "update kontrabon set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status', status_int='$status_int' where no_kbon='$kbon'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon_h set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);

$sqls = "update potongan set status = 'Approved' where no_kbon = '$no_kbon'";
$querys = mysqli_query($conn2,$sqls);

$sqlssas = "update return_kb set status = '$status' where no_kbon = '$no_kbon'";
$queryssas = mysqli_query($conn2,$sqlssas);

$sql1 = "update kartu_hutang set no_kbon='$kbon', tgl_kbon='$tglkbon', supp_inv='$supp_inv', tgl_inv='$tgl_inv', no_faktur='$no_faktur', tgl_tempo='$tgl_tempo', create_date='$confirm_date2', pph='$pph_value', rate = '$rate', credit_idr = '$credit_idr' where no_kbon = '$no_kbon'";
$query1 = mysqli_query($conn2,$sql1);

$sqlac = "update status set no_kbon='$kbon', tgl_kbon='$tglkbon' where no_kbon = '$no_kbon'";
$queryac = mysqli_query($conn2,$sqlac);

// $sql1 = "update detail set cek = '$cek' where no_kbon='$kbon'";
// $query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=pengajuankb.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>