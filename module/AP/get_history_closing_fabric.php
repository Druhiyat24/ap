<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = (int) ($_GET['id'] ?? 0);
$result = [];

if ($id) {
	$sql = mysqli_query($conn1, "SELECT status_closing, keterangan, action_by, action_date FROM tbl_closing_periode_fabric_wh_log WHERE periode_id = '$id' ORDER BY action_date DESC, id DESC");
	while ($row = mysqli_fetch_assoc($sql)) {
		$row['action_date_fmt'] = !empty($row['action_date']) ? date("d-M-Y H:i:s", strtotime($row['action_date'])) : '-';
		$result[] = $row;
	}
}

header('Content-Type: application/json');
echo json_encode($result);
?>
