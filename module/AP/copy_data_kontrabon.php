<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'] ?? '';
$status  = 'Updating';

if ($no_kbon && $status) {
	$sql = "UPDATE kontrabon_h SET status = '$status' WHERE no_kbon = '$no_kbon'";
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

		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>