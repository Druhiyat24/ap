<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

$no_mj = $_POST['no_mj'];
$tgl_mj = date("Y-m-d",strtotime($_POST['tgl_mj']));
$create_date = date("Y-m-d H:i:s");
// $update_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];
// $end_date = date("Y-m-d",strtotime($_POST['end_date']));

$sql_fil = mysqli_query($conn2,"select count(no_mj) ttl_mj from status_memorial_journal where no_mj = '$no_mj'");
$row_fil = mysqli_fetch_array($sql_fil);
$count_mj = isset($row_fil['ttl_mj']) ? $row_fil['ttl_mj'] : 0;

if ($count_mj <= 0 && $no_mj != '') {

$sql = mysqli_query($conn2,"select CONCAT('GM/NAG/',DATE_FORMAT('$tgl_mj', '%m'),DATE_FORMAT('$tgl_mj', '%y'),'/',IF(MAX(RIGHT(no_mj,5)) IS NULL,'00001',LPAD(MAX(RIGHT(no_mj,5))+1,5,0))) no_mj from sb_memorial_journal where MONTH(mj_date) = MONTH('$tgl_mj') and YEAR(mj_date) = YEAR('$tgl_mj')");
$row = mysqli_fetch_array($sql);
$kodepay = $row['no_mj'];
// $urutan = (int) substr($kodepay, 13, 5);
// $urutan++;
// $bln =  date("m",strtotime($tgl_mj));
// $thn =  date("y",strtotime($tgl_mj));
// $huruf = "GM/NAG/$bln$thn/";
// $kodepay = $huruf . sprintf("%05s", $urutan);


$query = "INSERT INTO status_memorial_journal (no_mj, mj_date, no_mj_sb, status, create_by, create_date) 
VALUES 
	('$no_mj', '$tgl_mj', '$kodepay', 'Post', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);



$sqla = "insert into sb_memorial_journal (select '' id,'$kodepay',mj_date,id_cmj,no_coa,no_costcenter,no_reff,reff_date,buyer,no_ws,curr,rate,debit,credit,debit_idr,credit_idr,keterangan,status,create_by,create_date,post_by,post_date,cancel_by,cancel_date, 'Verifikasi SB2', profit_center from tbl_memorial_journal where no_mj = '$no_mj')";
$querya = mysqli_query($conn1,$sqla);


$sqlb = "insert into sb_list_journal (select '' id,'$kodepay',tgl_journal,type_journal,no_coa,nama_coa,no_costcenter,nama_costcenter,reff_doc,reff_date,buyer,no_ws,curr,rate,debit,credit,debit_idr,credit_idr,status,keterangan,create_by,create_date,approve_by,approve_date,cancel_by,cancel_date, profit_center from tbl_list_journal where no_journal = '$no_mj')";
$queryb = mysqli_query($conn1,$sqlb);

}

// $sqlc = "update sb_list_journal a
// INNER JOIN master_category_mj b ON b.id_cmj = a.type_journal
// SET a.type_journal = b.nama_cmj 
// where a.no_journal like '%GM/%'";
// $queryc = mysqli_query($conn1,$sqlc);

// $sqld = "update sb_list_journal a
// INNER JOIN mastercoa_v2 b ON b.no_coa = a.no_coa
// SET a.nama_coa = b.nama_coa 
// where a.no_journal like '%GM/%'";
// $queryd = mysqli_query($conn1,$sqld);

// $sqle = "update sb_list_journal a
// INNER JOIN b_master_cc b ON b.no_cc = a.no_costcenter
// SET a.nama_costcenter = b.cc_name 
// where a.no_journal like '%GM/%'";
// $querye = mysqli_query($conn1,$sqle);



if(!$querya){
    die('Error: ' . mysqli_error());	
}
//echo 'Data Berhasil Di Simpan';	
echo $kodepay;
mysqli_close($conn2);

//if($query){
//	echo '<script type="text/javascript">alert("Data has been submitted");</script>';
//}else {
//	echo '<script type="text/javascript">alert("Error");</script>';
//}	
?>