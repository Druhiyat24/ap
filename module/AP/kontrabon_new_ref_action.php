<?php
// AJAX handler for the Kontrabon Reference Number master (ir_kontrabon_ref).
// action: add (single) | bulk (many, from Excel) | delete (single, Available
// only). Returns JSON. UI-facing messages are in English (per user request).
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$user   = $_SESSION['username'] ?? '';
$action = $_POST['action'] ?? '';
$now    = date('Y-m-d H:i:s');

// Normalize number: trim surrounding spaces. (Content is kept as-is, e.g.
// leading zeros are preserved because it is stored as text.)
function normRef($v)
{
    return trim((string) $v);
}

if ($action === 'add') {
    $ref = normRef($_POST['ref_number'] ?? '');
    $ket = normRef($_POST['keterangan'] ?? '');
    if ($ref === '') {
        echo json_encode(['ok' => false, 'msg' => 'Reference number cannot be empty.']);
        exit;
    }
    $ref_esc = mysqli_real_escape_string($conn2, $ref);
    $ket_esc = mysqli_real_escape_string($conn2, $ket);
    $user_esc = mysqli_real_escape_string($conn2, $user);

    // check duplicate first so the message is clear
    $chk = mysqli_query($conn2, "SELECT id FROM ir_kontrabon_ref WHERE ref_number = '$ref_esc' LIMIT 1");
    if ($chk && mysqli_num_rows($chk) > 0) {
        echo json_encode(['ok' => false, 'msg' => "Number '$ref' is already registered."]);
        exit;
    }
    $ins = mysqli_query($conn2, "INSERT INTO ir_kontrabon_ref (ref_number, status, keterangan, create_user, create_date)
        VALUES ('$ref_esc', 'Available', '$ket_esc', '$user_esc', '$now')");
    echo json_encode($ins
        ? ['ok' => true, 'msg' => "Number '$ref' added."]
        : ['ok' => false, 'msg' => 'Save failed: ' . mysqli_error($conn2)]);
    exit;
}

if ($action === 'bulk') {
    // items = [{n: reference number, d: description}] parsed from Excel.
    // Per-row description (d) wins; if empty, fall back to the single
    // "Description for all rows" field (keterangan).
    $raw = $_POST['items'] ?? '[]';
    $ketFallback = normRef($_POST['keterangan'] ?? '');
    $list = json_decode($raw, true);
    if (!is_array($list)) {
        echo json_encode(['ok' => false, 'msg' => 'Invalid data.']);
        exit;
    }

    $user_esc = mysqli_real_escape_string($conn2, $user);

    $inserted = 0;
    $dup = 0;
    $empty = 0;
    $seen = []; // avoid duplicates within the same file

    foreach ($list as $item) {
        // support both {n,d} objects and plain-string items (backward safe)
        $ref  = is_array($item) ? normRef($item['n'] ?? '') : normRef($item);
        $desc = is_array($item) ? normRef($item['d'] ?? '') : '';
        if ($desc === '') { $desc = $ketFallback; }
        if ($ref === '') { $empty++; continue; }
        if (isset($seen[$ref])) { $dup++; continue; }
        $seen[$ref] = true;

        $ref_esc  = mysqli_real_escape_string($conn2, $ref);
        $desc_esc = mysqli_real_escape_string($conn2, $desc);
        // INSERT IGNORE: if ref_number already exists (UNIQUE) -> skipped (0 rows)
        $ins = mysqli_query($conn2, "INSERT IGNORE INTO ir_kontrabon_ref (ref_number, status, keterangan, create_user, create_date)
            VALUES ('$ref_esc', 'Available', '$desc_esc', '$user_esc', '$now')");
        if ($ins && mysqli_affected_rows($conn2) > 0) {
            $inserted++;
        } else {
            $dup++;
        }
    }

    echo json_encode([
        'ok'       => true,
        'inserted' => $inserted,
        'dup'      => $dup,
        'empty'    => $empty,
        'msg'      => "Success: $inserted added, $dup skipped (already exists/duplicate)" . ($empty ? ", $empty empty ignored" : "") . ".",
    ]);
    exit;
}

if ($action === 'cancel') {
    // Soft cancel: keep the row (audit trail) and mark status = 'Cancel'.
    // Only an Available number can be cancelled (not one already Used).
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Invalid ID.']);
        exit;
    }
    $upd = mysqli_query($conn2, "UPDATE ir_kontrabon_ref SET status = 'Cancel' WHERE id = $id AND status = 'Available'");
    if ($upd && mysqli_affected_rows($conn2) > 0) {
        echo json_encode(['ok' => true, 'msg' => 'Number cancelled.']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Cannot cancel (maybe already used/cancelled or not found).']);
    }
    exit;
}

// GENERATE: buat N nomor berurutan KB/NAG/<tahun>/NNNNN (5 digit), status Available.
// Running number melanjut otomatis dari MAX tahun berjalan (reset per tahun). Nomor
// lama (angka polos) tidak ikut dihitung -> coexist. Semua atomik (1 INSERT InnoDB).
if ($action === 'generate') {
    $qty = (int) ($_POST['qty'] ?? 0);
    $ket = normRef($_POST['keterangan'] ?? '');
    if ($qty < 1)   { echo json_encode(['ok' => false, 'msg' => 'Quantity must be at least 1.']); exit; }
    if ($qty > 500) { echo json_encode(['ok' => false, 'msg' => 'Maximum 500 numbers per generate.']); exit; }

    $year = date('Y');
    $prefix = "KB/NAG/$year/";
    $prefix_esc = mysqli_real_escape_string($conn2, $prefix);
    $rm = mysqli_query($conn2, "SELECT MAX(CAST(SUBSTRING_INDEX(ref_number, '/', -1) AS UNSIGNED)) mx
        FROM ir_kontrabon_ref WHERE ref_number LIKE '$prefix_esc%'");
    $next = 1;
    if ($rm && ($rw = mysqli_fetch_assoc($rm))) $next = ((int) $rw['mx']) + 1;

    $user_esc = mysqli_real_escape_string($conn2, $user);
    $ket_esc  = mysqli_real_escape_string($conn2, $ket !== '' ? $ket : 'Generated');
    $vals = []; $from = ''; $to = '';
    for ($i = 0; $i < $qty; $i++) {
        $num = $next + $i;
        $ref = $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
        if ($i === 0) $from = $ref;
        $to = $ref;
        $vals[] = "('" . mysqli_real_escape_string($conn2, $ref) . "', 'Available', '$ket_esc', '$user_esc', '$now')";
    }
    $ok = mysqli_query($conn2, "INSERT INTO ir_kontrabon_ref (ref_number, status, keterangan, create_user, create_date)
        VALUES " . implode(',', $vals));
    if (!$ok) { echo json_encode(['ok' => false, 'msg' => 'Generate failed (a number may already exist, please retry): ' . mysqli_error($conn2)]); exit; }

    echo json_encode([
        'ok' => true, 'qty' => $qty, 'year' => $year,
        'from' => $from, 'to' => $to, 'from_num' => $next, 'to_num' => $next + $qty - 1,
        'msg' => "Generated $qty number(s): <b>$from</b> &rarr; <b>$to</b>.",
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Unknown action.']);
mysqli_close($conn2);
