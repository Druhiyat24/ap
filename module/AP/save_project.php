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

$id           = intval($_POST['id'] ?? 0);
$project_name = trim($_POST['project_name'] ?? '');
$description  = trim($_POST['description']  ?? '');
$category     = trim($_POST['category']     ?? '');
$status       = trim($_POST['status']       ?? 'Planned');
$priority     = trim($_POST['priority']     ?? 'Medium');
$progress     = intval($_POST['progress']   ?? 0);
$pic          = trim($_POST['pic']          ?? '');
$start_date   = trim($_POST['start_date']   ?? '');
$target_date  = trim($_POST['target_date']  ?? '');
$actual_date  = trim($_POST['actual_date']  ?? '');

if (!$project_name) {
    echo json_encode(['status' => 'error', 'message' => 'Project Name is required.']);
    exit;
}

$valid_status   = ['Planned', 'On Progress', 'On Hold', 'Done'];
$valid_priority = ['Low', 'Medium', 'High'];
if (!in_array($status, $valid_status))     $status   = 'Planned';
if (!in_array($priority, $valid_priority)) $priority = 'Medium';
if ($progress < 0)   $progress = 0;
if ($progress > 100) $progress = 100;
if ($status === 'Done') $progress = 100;

$start_date  = $start_date  !== '' ? $start_date  : null;
$target_date = $target_date !== '' ? $target_date : null;
$actual_date = $actual_date !== '' ? $actual_date : null;

$user = $_SESSION['username'] ?? 'system';
$now  = date('Y-m-d H:i:s');

// ===== INSERT =====
if ($id === 0) {
    $stmt = mysqli_prepare($conn2,
        "INSERT INTO master_project
            (project_name, description, category, status, priority, progress,
             pic, start_date, target_date, actual_date, created_by, created_date)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'sssssissssss',
        $project_name, $description, $category, $status, $priority, $progress,
        $pic, $start_date, $target_date, $actual_date, $user, $now
    );

// ===== UPDATE =====
} else {
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_project
         SET project_name = ?, description = ?, category = ?, status = ?, priority = ?,
             progress = ?, pic = ?, start_date = ?, target_date = ?, actual_date = ?, updated_by = ?, updated_date = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'sssssissssssi',
        $project_name, $description, $category, $status, $priority,
        $progress, $pic, $start_date, $target_date, $actual_date, $user, $now, $id
    );
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
