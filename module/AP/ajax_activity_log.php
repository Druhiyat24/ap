<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$current_user = $_SESSION['username'] ?? '';
if ($current_user !== 'indro') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access restricted.']);
    exit;
}

$project_id = intval($_POST['project_id'] ?? 0);
if ($project_id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'project_id required.']);
    exit;
}

$stmt = mysqli_prepare($conn2, "SELECT * FROM project_activity_log WHERE project_id = ? ORDER BY changed_date DESC, id DESC LIMIT 100");
mysqli_stmt_bind_param($stmt, 'i', $project_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$data = [];
while ($row = mysqli_fetch_assoc($res)) $data[] = $row;

echo json_encode(['status' => 'success', 'data' => $data]);
