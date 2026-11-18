<?php
/* ============================================================================
   ajax_project_studio.php - data DESIGN STUDIO (mockup) & DATA ROOM (ERD)
   untuk menu Project mode 3D. Jawaban JSON, kecuali action 'mockup_raw'
   (isi mockup apa adanya, untuk iframe & unduh).

   Sama seperti menu Project: hanya user 'indro', dan datanya SELALU produksi
   ($conn_live) - penjelasannya di project.php.

   Tabelnya dibuat db_migrate_LIVE_20261117_project_studio.sql. Sebelum SQL
   itu dijalankan, 'summary' menjawab ready=false dan aksi lain menolak dengan
   pesan (bukan error).
   ============================================================================ */
include "../../conn/conn.php";
session_start();

$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($current_user !== 'indro') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'error', 'message' => 'Access restricted.'));
    exit;
}
// sesi cukup dibaca; dilepas supaya beberapa permintaan halaman bisa berjalan bersamaan
session_write_close();

$cs = $conn_live;   // koneksi studio = produksi (sama dgn ajax_project.php)
$TABEL = array('project_mockup', 'project_erd', 'project_review', 'project_workflow');
$MAX_BYTES = 3 * 1024 * 1024;   // batas satu mockup / satu ERD

/* ---------- alat bantu ---------- */
function jawab($arr)
{
    echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function gagal($msg)
{
    jawab(array('status' => 'error', 'message' => $msg));
}
function jalan($cs, $sql, $types = '', $params = array())
{
    $st = mysqli_prepare($cs, $sql);
    if (!$st) gagal('Database error: ' . mysqli_error($cs));
    if ($types !== '') mysqli_stmt_bind_param($st, $types, ...$params);
    if (!mysqli_stmt_execute($st)) gagal('Database error: ' . mysqli_stmt_error($st));
    return $st;
}
function baris($cs, $sql, $types = '', $params = array())
{
    $res = mysqli_stmt_get_result(jalan($cs, $sql, $types, $params));
    $rows = array();
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}
function satu($cs, $sql, $types = '', $params = array())
{
    $rows = baris($cs, $sql, $types, $params);
    return $rows ? $rows[0] : null;
}
function tabel_ada($cs, $TABEL)
{
    $rows = baris($cs, "SELECT table_name AS t FROM information_schema.tables
                         WHERE table_schema = DATABASE() AND table_name IN ('" . implode("','", $TABEL) . "')");
    return array_map(function ($r) { return $r['t']; }, $rows);
}
// ERD: hitung tabel baru (perlu migrasi) & tabel lama (sudah ada di database)
function hitung_erd($json)
{
    $d = json_decode($json, true);
    $baru = 0; $lama = 0;
    if (is_array($d) && isset($d['tables']) && is_array($d['tables'])) {
        foreach ($d['tables'] as $t) { if (!empty($t['old'])) $lama++; else $baru++; }
    }
    return array($baru, $lama);
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$siap = count(tabel_ada($cs, $TABEL)) === count($TABEL);

/* ---------- isi mockup mentah (iframe / unduh) ----------
   Mockup = berkas .html buatan sendiri (hanya user 'indro' yang bisa upload),
   jadi boleh satu origin dgn aplikasi - sama seperti selama ini dibuka langsung
   (localhost/ap_dev/module/AP/mockup_xxx.html). Tanpa itu ikon (font) & skrip
   modul di dalam mockup gagal dimuat. Kiriman form tetap diblok sandbox. */
if ($action === 'mockup_raw') {
    $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
    $r = $siap ? satu($cs, "SELECT html, file_name, version, project_id FROM project_mockup WHERE id = ?", 'i', array($id)) : null;
    if (!$r) { http_response_code(404); echo 'Mockup not found.'; exit; }
    header('Content-Type: text/html; charset=utf-8');
    header("Content-Security-Policy: sandbox allow-scripts allow-same-origin allow-popups allow-modals");
    header('X-Content-Type-Options: nosniff');
    if (!empty($_GET['download'])) {
        $nama = $r['file_name'] ? preg_replace('/[^A-Za-z0-9_.\-]/', '_', $r['file_name']) : 'mockup_' . $r['project_id'] . '_v' . $r['version'] . '.html';
        header('Content-Disposition: attachment; filename="' . $nama . '"');
    }
    echo $r['html'];
    exit;
}

header('Content-Type: application/json');

/* ---------- ringkasan semua project (dipanggil saat halaman 3D dibuka) ---------- */
if ($action === 'summary') {
    if (!$siap) {
        jawab(array('status' => 'success', 'ready' => false, 'missing' => array_values(array_diff($TABEL, tabel_ada($cs, $TABEL)))));
    }
    $mockups = array();
    foreach (baris($cs, "SELECT m.id, m.project_id, m.version, m.status, m.note, m.created_date, c.n
                           FROM project_mockup m
                           JOIN (SELECT project_id, MAX(version) AS v, COUNT(*) AS n FROM project_mockup GROUP BY project_id) c
                             ON c.project_id = m.project_id AND c.v = m.version") as $r) {
        $mockups[$r['project_id']] = $r;
    }
    $erds = array();
    foreach (baris($cs, "SELECT e.id, e.project_id, e.version, e.status, e.erd_json, e.sql_run_date, e.updated_date, c.n
                           FROM project_erd e
                           JOIN (SELECT project_id, MAX(version) AS v, COUNT(*) AS n FROM project_erd GROUP BY project_id) c
                             ON c.project_id = e.project_id AND c.v = e.version") as $r) {
        list($r['n_new'], $r['n_old']) = hitung_erd($r['erd_json']);
        unset($r['erd_json']);
        $erds[$r['project_id']] = $r;
    }
    $flags = array();
    foreach (baris($cs, "SELECT project_id, mockup_skip, erd_skip FROM project_workflow") as $r) {
        $flags[$r['project_id']] = $r;
    }
    jawab(array('status' => 'success', 'ready' => true, 'mockups' => $mockups, 'erds' => $erds, 'flags' => $flags));
}

if (!$siap) gagal('The Design Studio tables do not exist yet. Run db_migrate_LIVE_20261117_project_studio.sql on production first.');

$pid = intval(isset($_POST['project_id']) ? $_POST['project_id'] : 0);
$id  = intval(isset($_POST['id']) ? $_POST['id'] : 0);

/* ======================= MOCKUP ======================= */
if ($action === 'mockup_list') {
    $vers = baris($cs, "SELECT id, version, status, note, file_name, LENGTH(html) AS size, created_by, created_date
                          FROM project_mockup WHERE project_id = ? ORDER BY version DESC", 'i', array($pid));
    $rev = baris($cs, "SELECT ref_id, version, action, note, created_by, created_date
                         FROM project_review WHERE project_id = ? AND kind = 'mockup' ORDER BY id", 'i', array($pid));
    jawab(array('status' => 'success', 'versions' => $vers, 'reviews' => $rev));
}

// berkas mockup_*.html yang ada di folder ini (yang biasa dibuat lewat sesi Claude Code)
if ($action === 'mockup_files') {
    $files = array();
    foreach (glob(__DIR__ . '/mockup_*.html') as $f) {
        $files[] = array('name' => basename($f), 'size' => filesize($f), 'mtime' => date('Y-m-d H:i', filemtime($f)));
    }
    usort($files, function ($a, $b) { return strcmp($b['mtime'], $a['mtime']); });
    jawab(array('status' => 'success', 'files' => $files));
}

if ($action === 'mockup_upload') {
    if ($pid <= 0) gagal('Project not found.');
    $note = trim(isset($_POST['note']) ? $_POST['note'] : '');
    $html = null; $nama = null;
    if (!empty($_POST['server_file'])) {
        // ambil dari berkas di folder ini - hanya nama mockup_*.html, tanpa folder
        $nama = basename($_POST['server_file']);
        if (!preg_match('/^mockup_[A-Za-z0-9_\-]+\.html$/', $nama) || !is_file(__DIR__ . '/' . $nama)) gagal('File not found.');
        $html = file_get_contents(__DIR__ . '/' . $nama);
    } elseif (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $nama = basename($_FILES['file']['name']);
        if (!preg_match('/\.html?$/i', $nama)) gagal('Only .html files can be uploaded.');
        $html = file_get_contents($_FILES['file']['tmp_name']);
    } else {
        gagal('Choose an .html file first.');
    }
    if ($html === false || trim($html) === '') gagal('The file is empty.');
    if (strlen($html) > $MAX_BYTES) gagal('The file is larger than 3 MB.');
    $last = satu($cs, "SELECT COALESCE(MAX(version), 0) AS v FROM project_mockup WHERE project_id = ?", 'i', array($pid));
    $ver = intval($last['v']) + 1;
    jalan($cs, "INSERT INTO project_mockup (project_id, version, status, note, file_name, html, created_by, created_date)
                VALUES (?, ?, 'review', ?, ?, ?, ?, NOW())", 'iissss', array($pid, $ver, mb_substr($note, 0, 255), mb_substr($nama, 0, 150), $html, $current_user));
    jawab(array('status' => 'success', 'version' => $ver, 'id' => mysqli_insert_id($cs)));
}

if ($action === 'mockup_review') {
    $act = isset($_POST['act']) ? $_POST['act'] : '';
    $note = trim(isset($_POST['note']) ? $_POST['note'] : '');
    if (!in_array($act, array('approved', 'changes'), true)) gagal('Unknown action.');
    if ($act === 'changes' && $note === '') gagal('Write what needs to change first.');
    $m = satu($cs, "SELECT id, project_id, version, status FROM project_mockup WHERE id = ?", 'i', array($id));
    if (!$m) gagal('Mockup not found.');
    $last = satu($cs, "SELECT MAX(version) AS v FROM project_mockup WHERE project_id = ?", 'i', array($m['project_id']));
    if (intval($last['v']) !== intval($m['version'])) gagal('Only the latest version can be reviewed.');
    if ($m['status'] === 'approved') gagal('This version is already approved.');
    jalan($cs, "UPDATE project_mockup SET status = ? WHERE id = ?", 'si', array($act, $id));
    jalan($cs, "INSERT INTO project_review (project_id, kind, ref_id, version, action, note, created_by, created_date)
                VALUES (?, 'mockup', ?, ?, ?, ?, ?, NOW())", 'iiisss', array($m['project_id'], $id, $m['version'], $act, $note, $current_user));
    jawab(array('status' => 'success'));
}

/* ======================= ERD ======================= */
if ($action === 'erd_get') {
    $vers = baris($cs, "SELECT id, version, status, created_date, updated_date, sql_run_date, sql_run_by
                          FROM project_erd WHERE project_id = ? ORDER BY version DESC", 'i', array($pid));
    $erd = null;
    if ($vers) {
        $want = $id > 0 ? $id : intval($vers[0]['id']);
        $erd = satu($cs, "SELECT id, version, status, erd_json, sql_run_date, sql_run_by FROM project_erd WHERE id = ? AND project_id = ?", 'ii', array($want, $pid));
        if ($erd) {
            $d = json_decode($erd['erd_json'], true);
            $erd['data'] = is_array($d) ? $d : array('tables' => array(), 'rels' => array());
            unset($erd['erd_json']);
        }
    }
    $rev = baris($cs, "SELECT ref_id, version, action, note, created_by, created_date
                         FROM project_review WHERE project_id = ? AND kind = 'erd' ORDER BY id", 'i', array($pid));
    jawab(array('status' => 'success', 'versions' => $vers, 'erd' => $erd, 'reviews' => $rev));
}

// versi baru (draft): salinan versi terakhir, atau kosong kalau belum ada
if ($action === 'erd_new') {
    if ($pid <= 0) gagal('Project not found.');
    $last = satu($cs, "SELECT id, version, status, erd_json FROM project_erd WHERE project_id = ? ORDER BY version DESC LIMIT 1", 'i', array($pid));
    if ($last && $last['status'] === 'draft') jawab(array('status' => 'success', 'id' => intval($last['id']), 'version' => intval($last['version'])));
    $ver = $last ? intval($last['version']) + 1 : 1;
    $json = $last ? $last['erd_json'] : json_encode(array('tables' => array(), 'rels' => array()));
    jalan($cs, "INSERT INTO project_erd (project_id, version, status, erd_json, created_by, created_date, updated_by, updated_date)
                VALUES (?, ?, 'draft', ?, ?, NOW(), ?, NOW())", 'iisss', array($pid, $ver, $json, $current_user, $current_user));
    jawab(array('status' => 'success', 'id' => mysqli_insert_id($cs), 'version' => $ver));
}

if ($action === 'erd_save') {
    $raw = isset($_POST['json']) ? $_POST['json'] : '';
    if (strlen($raw) > $MAX_BYTES) gagal('The ERD is too large.');
    $d = json_decode($raw, true);
    if (!is_array($d) || !isset($d['tables']) || !is_array($d['tables'])) gagal('Invalid ERD data.');
    $e = satu($cs, "SELECT id, status FROM project_erd WHERE id = ?", 'i', array($id));
    if (!$e) gagal('ERD not found.');
    if ($e['status'] !== 'draft') gagal('Only a draft can be edited. Start a new version first.');
    $bersih = json_encode(array('tables' => array_values($d['tables']), 'rels' => isset($d['rels']) && is_array($d['rels']) ? array_values($d['rels']) : array()));
    jalan($cs, "UPDATE project_erd SET erd_json = ?, updated_by = ?, updated_date = NOW() WHERE id = ?", 'ssi', array($bersih, $current_user, $id));
    jawab(array('status' => 'success'));
}

if ($action === 'erd_submit') {
    $e = satu($cs, "SELECT id, project_id, version, status FROM project_erd WHERE id = ?", 'i', array($id));
    if (!$e) gagal('ERD not found.');
    if ($e['status'] !== 'draft') gagal('Only a draft can be submitted.');
    jalan($cs, "UPDATE project_erd SET status = 'review', updated_by = ?, updated_date = NOW() WHERE id = ?", 'si', array($current_user, $id));
    jalan($cs, "INSERT INTO project_review (project_id, kind, ref_id, version, action, note, created_by, created_date)
                VALUES (?, 'erd', ?, ?, 'submitted', '', ?, NOW())", 'iiis', array($e['project_id'], $id, $e['version'], $current_user));
    jawab(array('status' => 'success'));
}

if ($action === 'erd_review') {
    $act = isset($_POST['act']) ? $_POST['act'] : '';
    $note = trim(isset($_POST['note']) ? $_POST['note'] : '');
    if (!in_array($act, array('approved', 'changes'), true)) gagal('Unknown action.');
    if ($act === 'changes' && $note === '') gagal('Write what needs to change first.');
    $e = satu($cs, "SELECT id, project_id, version, status FROM project_erd WHERE id = ?", 'i', array($id));
    if (!$e) gagal('ERD not found.');
    if ($e['status'] !== 'review') gagal('Only an ERD that is in review can be approved or sent back.');
    jalan($cs, "UPDATE project_erd SET status = ?, updated_by = ?, updated_date = NOW() WHERE id = ?", 'ssi', array($act, $current_user, $id));
    jalan($cs, "INSERT INTO project_review (project_id, kind, ref_id, version, action, note, created_by, created_date)
                VALUES (?, 'erd', ?, ?, ?, ?, ?, NOW())", 'iiisss', array($e['project_id'], $id, $e['version'], $act, $note, $current_user));
    jawab(array('status' => 'success'));
}

// tandai SQL migrasi ERD ini sudah / belum dijalankan di produksi
if ($action === 'erd_sql_done') {
    $on = !empty($_POST['on']);
    if ($on) jalan($cs, "UPDATE project_erd SET sql_run_date = NOW(), sql_run_by = ? WHERE id = ?", 'si', array($current_user, $id));
    else     jalan($cs, "UPDATE project_erd SET sql_run_date = NULL, sql_run_by = NULL WHERE id = ?", 'i', array($id));
    jawab(array('status' => 'success'));
}

/* ======================= TANDA "TIDAK PERLU" ======================= */
if ($action === 'skip') {
    $kind = isset($_POST['kind']) ? $_POST['kind'] : '';
    if ($pid <= 0 || !in_array($kind, array('mockup', 'erd'), true)) gagal('Unknown request.');
    $on = !empty($_POST['on']) ? 1 : 0;
    $kol = $kind === 'mockup' ? 'mockup_skip' : 'erd_skip';
    jalan($cs, "INSERT INTO project_workflow (project_id, $kol, updated_by, updated_date) VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE $kol = VALUES($kol), updated_by = VALUES(updated_by), updated_date = NOW()", 'iis', array($pid, $on, $current_user));
    jawab(array('status' => 'success'));
}

/* ======================= STRUKTUR DATABASE (untuk "Pull from database") ======================= */
if ($action === 'db_tables') {
    $q = trim(isset($_POST['q']) ? $_POST['q'] : '');
    $like = '%' . addcslashes($q, '%_\\') . '%';
    $rows = baris($cs, "SELECT table_name AS t, table_rows AS r FROM information_schema.tables
                         WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE' AND table_name LIKE ?
                         ORDER BY table_name LIMIT 80", 's', array($like));
    jawab(array('status' => 'success', 'tables' => $rows));
}

if ($action === 'db_columns') {
    $names = json_decode(isset($_POST['tables']) ? $_POST['tables'] : '[]', true);
    if (!is_array($names) || !$names) gagal('Choose at least one table.');
    $names = array_slice(array_values(array_filter($names, 'is_string')), 0, 20);
    $tanda = implode(',', array_fill(0, count($names), '?'));
    $rows = baris($cs, "SELECT table_name AS t, column_name AS c, column_type AS ty, column_key AS k
                          FROM information_schema.columns
                         WHERE table_schema = DATABASE() AND table_name IN ($tanda)
                         ORDER BY table_name, ordinal_position", str_repeat('s', count($names)), $names);
    $out = array();
    foreach ($rows as $r) {
        $key = $r['k'] === 'PRI' ? 'pk' : ($r['k'] === 'UNI' ? 'uq' : '');
        $out[$r['t']][] = array($r['c'], strtoupper($r['ty']), $key);
    }
    jawab(array('status' => 'success', 'columns' => $out));
}

gagal('Unknown action.');
