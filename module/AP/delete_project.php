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

$id = intval($_POST['id'] ?? 0);
if ($id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
    exit;
}

$stmt = mysqli_prepare($conn2, "DELETE FROM master_project WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($ok && $affected > 0) {
    // Cascade - task list items and the activity log are meaningless once
    // their parent project is gone.
    mysqli_query($conn2, "DELETE FROM project_milestones WHERE project_id = " . intval($id));
    mysqli_query($conn2, "DELETE FROM project_activity_log WHERE project_id = " . intval($id));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data not found or already deleted.']);
}
