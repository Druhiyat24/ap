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
// Server's default PHP timezone is misconfigured (Europe/Berlin, not Asia/Jakarta) -
// forcing WIB explicitly here keeps created_date/updated_date (and the activity
// log's changed_date) in sync with the browser's local clock instead of running
// ~5h behind, which made "X hr ago" timestamps look wrong right after saving.
$now  = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

function log_activity($conn, $project_id, $action, $field, $old, $new, $note, $user, $now) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO project_activity_log (project_id, action, field_name, old_value, new_value, note, changed_by, changed_date)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'isssssss', $project_id, $action, $field, $old, $new, $note, $user, $now);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

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
    // Snapshot the pre-change row so we can diff it below and log only the
    // fields that actually changed (one activity_log row per changed field).
    $oldRes = mysqli_query($conn2, "SELECT * FROM master_project WHERE id = " . intval($id));
    $old = mysqli_fetch_assoc($oldRes);

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
    if ($id === 0) {
        $newId = mysqli_insert_id($conn2);
        log_activity($conn2, $newId, 'created', null, null, null, $project_name, $user, $now);
    } elseif ($old) {
        $diffFields = [
            'project_name' => ['label' => 'Project Name', 'new' => $project_name],
            'category'     => ['label' => 'Module',       'new' => $category],
            'pic'          => ['label' => 'Requested By', 'new' => $pic],
            'status'       => ['label' => 'Status',       'new' => $status],
            'priority'     => ['label' => 'Priority',     'new' => $priority],
            'progress'     => ['label' => 'Progress',     'new' => $progress . '%'],
            'start_date'   => ['label' => 'Start Date',   'new' => $start_date],
            'target_date'  => ['label' => 'Target Date',  'new' => $target_date],
            'actual_date'  => ['label' => 'Actual Date',  'new' => $actual_date],
            'description'  => ['label' => 'Description',  'new' => $description],
        ];
        foreach ($diffFields as $col => $meta) {
            $oldVal = $col === 'progress' ? $old[$col] . '%' : $old[$col];
            if ((string)$oldVal !== (string)$meta['new']) {
                log_activity($conn2, $id, 'updated', $meta['label'], $oldVal, $meta['new'], null, $user, $now);
            }
        }
    }
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
