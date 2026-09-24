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
// Lihat catatan valid_status di save_project.php.
$valid_status = ['Planned', 'On Progress', 'Done', 'Live'];

if ($id === 0 || !in_array($status, $valid_status)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$user = $_SESSION['username'] ?? 'system';
// Server default PHP timezone is Europe/Berlin, not Asia/Jakarta - force WIB
// explicitly so updated_date (and the activity log entry below) don't end up
// ~5h behind the browser's clock.
$now  = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

$oldRes = mysqli_query($conn2, "SELECT status FROM master_project WHERE id = " . intval($id));
$oldStatus = mysqli_fetch_assoc($oldRes)['status'] ?? null;

if ($status === 'Live') {
    // Pindah ke Live: stempel live_date, dan actual_date (tanggal DONE) HANYA
    // diisi kalau memang masih kosong - COALESCE menjaga tanggal selesai yang
    // sudah ada supaya TIDAK tertimpa tanggal go-live. Ini yang membuat kolom
    // tanggal di Export Excel tetap tanggal DONE, bukan tanggal live.
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_project
         SET status = ?, progress = 100,
             actual_date = COALESCE(actual_date, CURDATE()),
             live_date   = COALESCE(live_date, CURDATE()),
             updated_by = ?, updated_date = ?
         WHERE id = ?"
    );
} elseif ($status === 'Done') {
    // Completing via the quick-change control also marks it 100% and stamps
    // an actual date if one hasn't been set yet, mirroring the full edit form.
    // live_date sengaja TIDAK disentuh (tidak dihapus) supaya tanggal yang
    // pernah diisi manual tidak hilang kalau kartu sempat digeser bolak-balik.
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
    if ($oldStatus !== null && $oldStatus !== $status) {
        $stmtLog = mysqli_prepare($conn2,
            "INSERT INTO project_activity_log (project_id, action, field_name, old_value, new_value, changed_by, changed_date)
             VALUES (?, 'updated', 'Status', ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmtLog, 'issss', $id, $oldStatus, $status, $user, $now);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    }
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
