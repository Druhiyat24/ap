<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');


$no_coa = trim($_POST['no_coa']);
$nama_coa = strtoupper($_POST['nama_coa']);
$nama_ctg1_h = $_POST['nama_ctg1_h'];
$nama_ctg2 = $_POST['nama_ctg2'];
$nama_ctg3_h = $_POST['nama_ctg3_h'];
$nama_ctg4_h = $_POST['nama_ctg4_h'];
$nama_ctg5 = $_POST['nama_ctg5'];
$txt_dirdebit = $_POST['txt_dirdebit'];
$txt_dircredit = $_POST['txt_dircredit'];
$txt_indirect = $_POST['txt_indirect'];
$status = "Active";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

// echo $tgl_active;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $sob;
// echo "< -- >";
// echo $account;
// echo "< -- >";
// echo $bank;
// echo "< -- >";
// echo $curr;
// echo "< -- >";
// echo $pesan;
// echo "< -- >";
// echo $status;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $create_date;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $tgl_active2;
$query_copas = "insert into mastercoa_v2_edit (select * from mastercoa_v2 where no_coa = '$no_coa') ";
$execute_copas = mysqli_query($conn2,$query_copas);

if($execute_copas){	
$query_del_coa = "DELETE from mastercoa_v2 where no_coa = '$no_coa' ";
$execute_del_coa = mysqli_query($conn2,$query_del_coa);
}else{
	
}


if (!$execute_del_coa) {
	$query = 'select max(id) from mastercoa_v3';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{
$query = "INSERT INTO mastercoa_v3 (no_coa, nama_coa, id_ctg1, id_ctg2, id_ctg3, id_ctg4, id_ctg5, id_direct_debit, id_direct_credit, id_indirect, status) 
VALUES 
	('$no_coa', '$nama_coa', '$nama_ctg1_h', '$nama_ctg2', '$nama_ctg3_h', '$nama_ctg4_h', '$nama_ctg5', '$txt_dirdebit', '$txt_dircredit', '$txt_indirect', '$status')";

$execute = mysqli_query($conn2,$query);


$sql_ctg1 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg1 where id_ctg1 = '$nama_ctg1_h'");
$row_ctg1 = mysqli_fetch_array($sql_ctg1);
$ind_name1 = $row_ctg1['ind_name'];
$eng_name1 = $row_ctg1['eng_name'];

$sql_ctg2 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg2 where id_ctg2 = '$nama_ctg2'");
$row_ctg2 = mysqli_fetch_array($sql_ctg2);
$ind_name2 = $row_ctg2['ind_name'];
$eng_name2 = $row_ctg2['eng_name'];

$sql_ctg3 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg3 where id_ctg3 = '$nama_ctg3_h'");
$row_ctg3 = mysqli_fetch_array($sql_ctg3);
$ind_name3 = $row_ctg3['ind_name'];
$eng_name3 = $row_ctg3['eng_name'];

$sql_ctg4 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg4 where id_ctg4 = '$nama_ctg4_h'");
$row_ctg4 = mysqli_fetch_array($sql_ctg4);
$ind_name4 = $row_ctg4['ind_name'];
$eng_name4 = $row_ctg4['eng_name'];

$sql_ctg5 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg5 where id_ctg5 = '$nama_ctg5'");
$row_ctg5 = mysqli_fetch_array($sql_ctg5);
$ind_name5 = $row_ctg5['ind_name'];
$eng_name5 = $row_ctg5['eng_name'];

$sql_dirdebit = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_dirdebit'");
$row_dirdebit = mysqli_fetch_array($sql_dirdebit);
$ind_name6 = isset($row_dirdebit['ind_name']) ? $row_dirdebit['ind_name'] : "NA";
$eng_name6 = isset($row_dirdebit['eng_name']) ? $row_dirdebit['eng_name'] : "NA";

$sql_dircredit = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_dircredit'");
$row_dircredit = mysqli_fetch_array($sql_dircredit);
$ind_name7 = isset($row_dircredit['ind_name']) ? $row_dircredit['ind_name'] : "NA";
$eng_name7 = isset($row_dircredit['eng_name']) ? $row_dircredit['eng_name'] : "NA";

$sql_indirect = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_indirect'");
$row_indirect = mysqli_fetch_array($sql_indirect);
$ind_name8 = isset($row_indirect['ind_name']) ? $row_indirect['ind_name'] : "NA";
$eng_name8 = isset($row_indirect['eng_name']) ? $row_indirect['eng_name'] : "NA";


$query2 = "INSERT INTO mastercoa_v2 (no_coa, nama_coa, ind_categori1, eng_categori1, ind_categori2, eng_categori2, ind_categori3, eng_categori3, ind_categori4, eng_categori4, ind_categori5, eng_categori5, ind_debit_direct, eng_debit_direct, ind_credit_direct, eng_credit_direct, ind_indirect, eng_indirect, status,id_ctg1, id_ctg2, id_ctg3, id_ctg4, id_ctg5, id_direct_debit, id_direct_credit, id_indirect) 
VALUES 
	('$no_coa', '$nama_coa', '$ind_name1', '$eng_name1', '$ind_name2', '$eng_name2', '$ind_name3', '$eng_name3', '$ind_name4', '$eng_name4', '$ind_name5', '$eng_name5', '$ind_name6', '$eng_name6', '$ind_name7', '$eng_name7', '$ind_name8', '$eng_name8', 'Active', '$nama_ctg1_h', '$nama_ctg2', '$nama_ctg3_h', '$nama_ctg4_h', '$nama_ctg5', '$txt_dirdebit', '$txt_dircredit', '$txt_indirect')";

$execute2 = mysqli_query($conn2,$query2);
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>