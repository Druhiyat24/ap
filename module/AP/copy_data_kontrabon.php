<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'] ?? '';
$status  = 'Updating';

if ($no_kbon && $status) {
	$sql = "UPDATE kontrabon_h SET status = '$status' WHERE no_kbon = '$no_kbon'";
	$uptquery1 = mysqli_query($conn2,"update kontrabon set status = 'Updating' where no_kbon= '$no_kbon'");
	$uptquery2 = mysqli_query($conn2,"update kontrabon_ftr set status = 'Updating' where no_kbon= '$no_kbon'");
	$uptquery3 = mysqli_query($conn2,"update return_kb set status = 'Updating' where no_kbon= '$no_kbon'");
	$uptquery4 = mysqli_query($conn2,"update potongan set status = 'Updating' where no_kbon= '$no_kbon'");
	if (mysqli_query($conn2, $sql)) {

		$sql_copy1 = "INSERT into ap_edit_kontrabon_h select * from kontrabon_h where no_kbon = '$no_kbon'";
		$query_copy1 = mysqli_query($conn2,$sql_copy1);

		$sql_copy2 = "INSERT into ap_edit_kontrabon select * from kontrabon where no_kbon = '$no_kbon'";
		$query_copy2 = mysqli_query($conn2,$sql_copy2);

		$sql_copy3 = "INSERT into ap_edit_kontrabon_ftr select * from kontrabon_ftr where no_kbon = '$no_kbon'";
		$query_copy3 = mysqli_query($conn2,$sql_copy3);

		$sql_copy4 = "INSERT into ap_edit_potongan select * from potongan where no_kbon = '$no_kbon'";
		$query_copy4 = mysqli_query($conn2,$sql_copy4);

		$sql_copy5 = "INSERT into ap_edit_return_kb select * from return_kb where no_kbon = '$no_kbon'";
		$query_copy5 = mysqli_query($conn2,$sql_copy5);

		$sql_copy6 = "INSERT into ap_journal_temp select * from tbl_list_journal where no_journal = '$no_kbon' and LENGTH(reff_doc) > 2";
		$query_copy6 = mysqli_query($conn2,$sql_copy6);

		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>