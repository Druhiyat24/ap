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

$id     = intval($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$valid_status = ['Planned', 'On Progress', 'On Hold', 'Done'];

if ($id === 0 || !in_array($status, $valid_status)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$user = $_SESSION['username'] ?? 'system';
$now  = date('Y-m-d H:i:s');

if ($status === 'Done') {
    // Completing via the quick-change control also marks it 100% and stamps
    // an actual date if one hasn't been set yet, mirroring the full edit form.
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_project
         SET status = ?, progress = 100, actual_date = COALESCE(actual_date, CURDATE()), updated_by = ?, updated_date = ?
         WHERE id = ?"
    );
} else {
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_project SET status = ?, updated_by = ?, updated_date = ? WHERE id = ?"
    );
}
mysqli_stmt_bind_param($stmt, 'sssi', $status, $user, $now, $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
