<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_bpb = $_POST['no_bpb'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_bpb)){
$sql = "update maintain_bpb_h set status = 'APPROVED', approve_by = '$approve_user', approve_date = '$confirm_date' where no_maintain = '$no_dok'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, '$approve_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$no_bpb'";
$execute2 = mysqli_query($conn2,$sql2);

$sql3 = "update bpb set confirm = 'N', confirm_by = '', confirm_date = '', status_maintain = null where bpbno_int = '$no_bpb'";
$execute3 = mysqli_query($conn2,$sql3);

$sql4 = "update bppb set confirm = 'N', confirm_by = '', confirm_date = '', status_maintain = null where bppbno_int = '$no_bpb'";
$execute4 = mysqli_query($conn2,$sql4);

$sql5 = "update whs_inmaterial_fabric set status = 'Pending', approved_by = '', approved_date = '' where no_dok = '$no_bpb'";
$execute5 = mysqli_query($conn2,$sql5);

$sql6 = "update whs_bppb_h set status = 'Pending', approved_by = '', approved_date = '' where no_bppb = '$no_bpb'";
$execute6 = mysqli_query($conn2,$sql6);

$sql7 = "UPDATE bpb SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute7 = pg_query($conn4, $sql7);

$sql8 = "UPDATE gp_penerimaan_greige_h SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute8 = pg_query($conn4, $sql8);

$sql9 = "UPDATE bpb_celup SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute9 = pg_query($conn4, $sql9);

$sql10 = "delete from bpb_knitting where no_bpb = '$no_bpb'";
$execute10 = mysqli_query($conn2,$sql10);

$sql11 = "delete from tbl_tamb_bpb2 where no_bpb = '$no_bpb'";
$execute11 = mysqli_query($conn2,$sql11);

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
}

mysqli_close($conn2);

?>