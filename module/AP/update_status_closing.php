<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$update_user = $_POST['update_user'] ?? '';
$create_date = date("Y-m-d H:i:s");

if ($id && $status) {
	if ($status == 'Closed') {
		$update = mysqli_query($conn1, "UPDATE tbl_closing_periode SET status_closing = '$status', keterangan = '$keterangan', lock_by = '$update_user', lock_date = '$create_date', unlock_by = null, unlock_date = null WHERE id = '$id'");
	}else{
		$update = mysqli_query($conn1, "UPDATE tbl_closing_periode SET status_closing = '$status', keterangan = '$keterangan', unlock_by = '$update_user', unlock_date = '$create_date', lock_by = null, lock_date = null WHERE id = '$id'");
	}

	if ($update) {
		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Gagal update ke database']);
	}
} else {
	echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
}

?>