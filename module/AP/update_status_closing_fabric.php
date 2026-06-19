<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$update_user = $_POST['update_user'] ?? '';
$create_date = date("Y-m-d H:i:s");

if ($id && $status) {
	if ($status == 'Closed') {
		$update = mysqli_query($conn1, "UPDATE tbl_closing_periode_fabric_wh SET status_closing = '$status', keterangan = '$keterangan', lock_by = '$update_user', lock_date = '$create_date', unlock_by = null, unlock_date = null WHERE id = '$id'");
	}else{
		$update = mysqli_query($conn1, "UPDATE tbl_closing_periode_fabric_wh SET status_closing = '$status', keterangan = '$keterangan', unlock_by = '$update_user', unlock_date = '$create_date', lock_by = null, lock_date = null WHERE id = '$id'");
	}

	if ($update) {
		mysqli_query($conn1, "INSERT INTO tbl_closing_periode_fabric_wh_log (periode_id, bulan, tahun, status_closing, keterangan, action_by, action_date)
			SELECT id, bulan, tahun, '$status', '$keterangan', '$update_user', '$create_date' FROM tbl_closing_periode_fabric_wh WHERE id = '$id'");

		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Failed to update database']);
	}
} else {
	echo json_encode(['success' => false, 'message' => 'Incomplete data']);
}

?>
