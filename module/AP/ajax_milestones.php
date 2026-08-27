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

$action     = $_POST['action']     ?? 'list';
$project_id = intval($_POST['project_id'] ?? 0);
// Server default PHP timezone is Europe/Berlin, not Asia/Jakarta - force WIB
// explicitly so milestone/activity timestamps don't end up ~5h behind the
// browser's clock (that's what made "5 hr ago" show up right after saving).
$now        = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

function log_activity($conn, $project_id, $action, $note, $user, $now) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO project_activity_log (project_id, action, note, changed_by, changed_date) VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'issss', $project_id, $action, $note, $user, $now);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Recomputes master_project.progress from checklist completion ratio whenever
// the project has at least one milestone — keeps the progress bar/ring in sync
// without the user having to also drag the manual slider.
function recompute_progress($conn, $project_id, $user, $now) {
    $r = mysqli_query($conn, "SELECT COUNT(*) total, SUM(is_done) done FROM project_milestones WHERE project_id = " . intval($project_id));
    $row = mysqli_fetch_assoc($r);
    $total = (int)$row['total'];
    if ($total === 0) return null;
    $done = (int)$row['done'];
    $pct = (int)round(($done / $total) * 100);

    $oldStatusRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM master_project WHERE id = " . intval($project_id)));
    $oldStatus = $oldStatusRow['status'] ?? null;

    if ($pct === 100) {
        // Mirrors quick_update_status.php's own Done handling (stamp actual_date
        // once, don't clobber it if already set).
        $stmt = mysqli_prepare($conn,
            "UPDATE master_project SET progress = ?, status = 'Done', actual_date = COALESCE(actual_date, CURDATE()), updated_by = ?, updated_date = ? WHERE id = ?"
        );
    } else {
        // Un-checking a milestone after the project auto-completed should pull
        // it back out of Done - it can't be 100% Done at less than 100% checklist.
        $stmt = mysqli_prepare($conn,
            "UPDATE master_project SET progress = ?, status = IF(status = 'Done', 'On Progress', status), updated_by = ?, updated_date = ? WHERE id = ?"
        );
    }
    mysqli_stmt_bind_param($stmt, 'issi', $pct, $user, $now, $project_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $newStatus = $pct === 100 ? 'Done' : ($oldStatus === 'Done' ? 'On Progress' : $oldStatus);
    if ($oldStatus !== null && $newStatus !== $oldStatus) {
        $stmtLog = mysqli_prepare($conn,
            "INSERT INTO project_activity_log (project_id, action, field_name, old_value, new_value, changed_by, changed_date)
             VALUES (?, 'updated', 'Status', ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmtLog, 'issss', $project_id, $oldStatus, $newStatus, $user, $now);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
    return $pct;
}

if ($project_id === 0 && $action !== 'list_none') {
    echo json_encode(['status' => 'error', 'message' => 'project_id required.']);
    exit;
}

if ($action === 'list') {
    $stmt = mysqli_prepare($conn2, "SELECT * FROM project_milestones WHERE project_id = ? ORDER BY sort_order ASC, id ASC");
    mysqli_stmt_bind_param($stmt, 'i', $project_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) $data[] = $row;
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

if ($action === 'add') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') { echo json_encode(['status' => 'error', 'message' => 'Title is required.']); exit; }
    $r = mysqli_query($conn2, "SELECT COALESCE(MAX(sort_order),0)+1 AS n FROM project_milestones WHERE project_id = " . intval($project_id));
    $sort_order = (int)mysqli_fetch_assoc($r)['n'];
    $stmt = mysqli_prepare($conn2, "INSERT INTO project_milestones (project_id, title, sort_order, created_date) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'isis', $project_id, $title, $sort_order, $now);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($ok) log_activity($conn2, $project_id, 'milestone_added', $title, $current_user, $now);
    $newProgress = recompute_progress($conn2, $project_id, $current_user, $now);
    echo json_encode(['status' => $ok ? 'success' : 'error', 'progress' => $newProgress]);
    exit;
}

if ($action === 'toggle') {
    $id = intval($_POST['id'] ?? 0);
    $r = mysqli_query($conn2, "SELECT title, is_done FROM project_milestones WHERE id = $id AND project_id = " . intval($project_id));
    $row = mysqli_fetch_assoc($r);
    if (!$row) { echo json_encode(['status' => 'error', 'message' => 'Not found.']); exit; }
    $newVal = $row['is_done'] ? 0 : 1;
    $doneDate = $newVal ? "'$now'" : 'NULL';
    mysqli_query($conn2, "UPDATE project_milestones SET is_done = $newVal, done_date = $doneDate WHERE id = $id");
    log_activity($conn2, $project_id, $newVal ? 'milestone_done' : 'milestone_undone', $row['title'], $current_user, $now);
    $newProgress = recompute_progress($conn2, $project_id, $current_user, $now);
    echo json_encode(['status' => 'success', 'is_done' => $newVal, 'progress' => $newProgress]);
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $r = mysqli_query($conn2, "SELECT title FROM project_milestones WHERE id = $id AND project_id = " . intval($project_id));
    $row = mysqli_fetch_assoc($r);
    mysqli_query($conn2, "DELETE FROM project_milestones WHERE id = $id");
    if ($row) log_activity($conn2, $project_id, 'milestone_deleted', $row['title'], $current_user, $now);
    $newProgress = recompute_progress($conn2, $project_id, $current_user, $now);
    echo json_encode(['status' => 'success', 'progress' => $newProgress]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
