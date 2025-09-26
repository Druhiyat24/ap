<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon_h = $_POST['no_kbon_h'];

$uptquery1 = mysqli_query($conn2,"update kontrabon set status = 'draft' where no_kbon= '$no_kbon_h'");
$uptquery2 = mysqli_query($conn2,"update kontrabon_h set status = 'draft' where no_kbon= '$no_kbon_h'");
$uptquery3 = mysqli_query($conn2,"update kontrabon_ftr set status = 'draft' where no_kbon= '$no_kbon_h'");
$uptquery4 = mysqli_query($conn2,"update return_kb set status = 'draft' where no_kbon= '$no_kbon_h'");
$uptquery5 = mysqli_query($conn2,"update potongan set status = 'draft' where no_kbon= '$no_kbon_h'");

$delquery1 = mysqli_query($conn2,"delete from ap_edit_kontrabon where no_kbon= '$no_kbon_h'");
$delquery2 = mysqli_query($conn2,"delete from ap_edit_kontrabon_h where no_kbon= '$no_kbon_h'");
$delquery3 = mysqli_query($conn2,"delete from ap_edit_kontrabon_ftr where no_kbon= '$no_kbon_h'");
$delquery4 = mysqli_query($conn2,"delete from ap_edit_return_kb where no_kbon= '$no_kbon_h'");
$delquery5 = mysqli_query($conn2,"delete from ap_edit_potongan where no_kbon= '$no_kbon_h'");


if($delquery1){

	// echo 'Data Saved Successfully With No Kontrabon '; echo $kode;

}else{
   // die('Error: ' . mysql_error());	
}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>