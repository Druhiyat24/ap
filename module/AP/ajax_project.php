<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

// Restricted page - only 'indro' may read project data
$current_user = $_SESSION['username'] ?? '';
if ($current_user !== 'indro') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access restricted.']);
    exit;
}

$action = $_POST['action'] ?? 'list';

// ===== GET ONE (for Edit) =====
if ($action === 'get_one') {
    $id   = intval($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn2, "SELECT * FROM master_project WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    echo json_encode(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)));
    exit;
}

// ===== LIST (cards) =====
$q = mysqli_query($conn2,
    "SELECT *,
        CASE
            WHEN status IN ('On Progress', 'Planned') AND target_date IS NOT NULL AND target_date < CURDATE() THEN 1
            ELSE 0
        END AS is_overdue,
        CASE status
            WHEN 'On Progress' THEN 1
            WHEN 'On Hold'     THEN 2
            WHEN 'Planned'     THEN 3
            WHEN 'Done'        THEN 4
            ELSE 5
        END AS status_order
     FROM master_project
     ORDER BY status_order ASC, target_date IS NULL, target_date ASC, id DESC"
);

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
