<?php
// ============================================================================
// ajx_cek_faktur_ppn.php — cek FAKTUR DUPLIKAT sebelum Save di tab PPN Masukan.
//
// Mencari nomor faktur pada data upload (staging milik user) yang TERNYATA sudah
// pernah masuk jurnal memorial sebelumnya. Dipakai untuk memunculkan konfirmasi
// "faktur ini sudah pernah disimpan di GM xxx — yakin tetap simpan?".
// User boleh tetap melanjutkan (hanya peringatan, bukan larangan).
//
// PENTING: GM yang SUDAH DI-CANCEL tidak dihitung. Jurnal GM yang dibatalkan
// ditandai tbl_memorial_journal.status='Cancel' (baris tbl_list_journal-nya
// dipindah ke tbl_list_journal_cancel), jadi sumber cek yang benar adalah
// tbl_memorial_journal + filter status <> 'Cancel'.
//
// Dibatasi ke COA PPN (2.52.03 / 1.52.04) supaya tidak salah tangkap jurnal
// memorial lain yang kebetulan memakai No Reff sama.
//
// Respons: {status:'success', dupe:[{no_faktur, gm, tgl}], total: n}
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/ppn_masukan_lines.php';   // konstanta COA PPN
session_start();
date_default_timezone_set('Asia/Jakarta');
ini_set('max_execution_time', 0);
header('Content-Type: application/json; charset=utf-8');

function ppn_cek_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); }

$user = $_SESSION['username'] ?? 'system';
$e    = mysqli_real_escape_string($conn2, $user);

// JOIN langsung ke tabel staging (bukan IN (...) berisi ribuan nomor) supaya
// tetap ringan walau file berisi ribuan faktur.
$sql = "SELECT u.no_faktur,
               GROUP_CONCAT(DISTINCT m.no_mj ORDER BY m.no_mj SEPARATOR ', ') AS gm,
               MAX(m.mj_date) AS tgl
        FROM (SELECT DISTINCT no_faktur FROM tbl_ppn_masukan_upload
              WHERE create_by = '$e' AND status = 'Temp' AND no_faktur <> '') u
        INNER JOIN tbl_memorial_journal m
                ON m.no_reff = u.no_faktur
               AND m.status <> 'Cancel'
               AND m.no_coa IN ('" . PPN_COA_DEBIT . "', '" . PPN_COA_CREDIT . "')
        GROUP BY u.no_faktur
        ORDER BY u.no_faktur";

$q = mysqli_query($conn2, $sql);
if ($q === false) {
    ppn_cek_out(['status' => 'error', 'message' => 'Failed to check invoices: ' . mysqli_error($conn2)]);
    exit;
}

$dupe = [];
while ($r = mysqli_fetch_assoc($q)) { $dupe[] = $r; }

ppn_cek_out(['status' => 'success', 'dupe' => $dupe, 'total' => count($dupe)]);
