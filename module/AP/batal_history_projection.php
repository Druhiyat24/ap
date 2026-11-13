<?php
/* ============================================================================
   AP HISTORY PROJECTION - BATALKAN satu dokumen potret.
   Dipanggil tombol Cancel di ap_history_projection.php (diminta pemakai
   6 Nov 2026, mengikuti History Projection di ar_dev).

   DIARSIPKAN DULU, BARU DIHAPUS - sama spt AR: barisnya disalin ke
   ap_projection_history_h_cancel / _det_cancel, baru dihapus dari tabel
   hidup. Dokumen yang dibatalkan tetap bisa ditelusuri; yang hilang hanya
   kemunculannya di daftar.

   URUTANNYA PENTING dan dibungkus SATU transaksi:
     1. arsipkan RINCIAN  2. arsipkan KEPALA
     3. hapus RINCIAN     4. hapus KEPALA
   Kalau kepalanya dihapus lebih dulu lalu gagal di tengah, rinciannya jadi
   yatim - tidak muncul di mana pun dan tidak bisa diarsipkan lagi karena
   acuan kepalanya sudah tidak ada.

   AWAS $user: conn.php menimpa variabel $user dengan "root". Nama pemakai
   dibaca ke $userLogin SEBELUM include.
   ============================================================================ */

session_start();
$userLogin = isset($_SESSION['username']) ? trim((string) $_SESSION['username']) : '';

include '../../conn/conn.php';
header('Content-Type: application/json');

function bhpJawab($arr)
{
    echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$doc = isset($_POST['doc_number']) ? trim((string) $_POST['doc_number']) : '';
if ($doc === '') {
    bhpJawab(array('status' => 'error', 'message' => 'No document number was sent.'));
}
if ($userLogin === '') {
    bhpJawab(array('status' => 'error', 'message' => 'Your session has expired. Please sign in again.'));
}

$eDoc  = mysqli_real_escape_string($conn2, $doc);
$eUser = mysqli_real_escape_string($conn2, $userLogin);

// Hak Cancel diatur di menu User Role ("AP History Projection - Cancel").
// Dicek di sini juga, bukan cuma tombolnya yang disembunyikan.
$hak = mysqli_query($conn2, "SELECT 1 FROM useraccess WHERE username = '$eUser'
                             AND menu = 'AP History Projection - Cancel' LIMIT 1");
if (!$hak || mysqli_num_rows($hak) === 0) {
    bhpJawab(array('status' => 'error',
        'message' => 'You do not have access to cancel History Projection documents. '
                   . 'Ask the admin to enable "AP History Projection - Cancel" in User Role.'));
}

/* Tabel arsipnya BOLEH BELUM ADA - migrasinya dijalankan terpisah di
   produksi. Tanpa penjaga ini, Cancel akan MENGHAPUS tanpa sempat
   mengarsipkan, dan itu kehilangan data yang tidak bisa dikembalikan. */
foreach (array('ap_projection_history_h_cancel', 'ap_projection_history_det_cancel') as $t) {
    $c = mysqli_query($conn2, "SHOW TABLES LIKE '$t'");
    if (!$c || mysqli_num_rows($c) === 0) {
        bhpJawab(array('status' => 'error',
            'message' => 'The archive table ' . $t . ' does not exist yet. '
                       . 'Run db_migrate_LIVE_20261106_GABUNGAN.sql first - '
                       . 'cancelling without it would delete the document for good.'));
    }
}

$cek = mysqli_query($conn2, "SELECT doc_number FROM ap_projection_history_h
                             WHERE doc_number = '$eDoc'");
if (!$cek || mysqli_num_rows($cek) === 0) {
    bhpJawab(array('status' => 'error',
        'message' => 'Document ' . htmlspecialchars($doc, ENT_QUOTES) . ' was not found. '
                   . 'It may have been cancelled already.'));
}

mysqli_query($conn2, 'START TRANSACTION');

/* Tanda pembatalan ditulis di tabel HIDUP dulu, supaya nilai itu ikut
   tersalin ke arsipnya - arsip yang tidak mencatat siapa & kapan
   membatalkan tidak ada gunanya saat ditelusuri. */
$ok = mysqli_query($conn2, "UPDATE ap_projection_history_h
        SET status = 'CANCEL', cancel_by = '$eUser', cancel_date = NOW()
        WHERE doc_number = '$eDoc'");

if ($ok) {
    $ok = mysqli_query($conn2, "INSERT IGNORE INTO ap_projection_history_det_cancel
            SELECT * FROM ap_projection_history_det WHERE doc_number = '$eDoc'");
}
if ($ok) {
    $ok = mysqli_query($conn2, "INSERT IGNORE INTO ap_projection_history_h_cancel
            SELECT * FROM ap_projection_history_h WHERE doc_number = '$eDoc'");
}

/* PENJAGA: jangan menghapus kalau arsipnya tidak lengkap. Membandingkan
   jumlah barisnya, bukan sekadar "query-nya tidak gagal" - INSERT IGNORE
   bisa saja melewati baris tanpa memunculkan error. */
$jmlHidup = 0; $jmlArsip = 0;
if ($ok) {
    $a = mysqli_query($conn2, "SELECT COUNT(*) n FROM ap_projection_history_det WHERE doc_number='$eDoc'");
    $b = mysqli_query($conn2, "SELECT COUNT(*) n FROM ap_projection_history_det_cancel WHERE doc_number='$eDoc'");
    $jmlHidup = ($a) ? (int) mysqli_fetch_assoc($a)['n'] : -1;
    $jmlArsip = ($b) ? (int) mysqli_fetch_assoc($b)['n'] : -2;
    if ($jmlHidup !== $jmlArsip) { $ok = false; }
}

if (!$ok) {
    mysqli_query($conn2, 'ROLLBACK');
    bhpJawab(array('status' => 'error',
        'message' => 'Could not archive the document, so nothing was deleted'
                   . ($jmlHidup !== $jmlArsip
                      ? " ($jmlHidup row(s) live vs $jmlArsip archived)" : '')
                   . '. ' . mysqli_error($conn2)));
}

$ok = mysqli_query($conn2, "DELETE FROM ap_projection_history_det WHERE doc_number = '$eDoc'");
if ($ok) {
    $ok = mysqli_query($conn2, "DELETE FROM ap_projection_history_h WHERE doc_number = '$eDoc'");
}
if (!$ok) {
    mysqli_query($conn2, 'ROLLBACK');
    bhpJawab(array('status' => 'error', 'message' => 'Failed to remove the document: ' . mysqli_error($conn2)));
}

mysqli_query($conn2, 'COMMIT');

bhpJawab(array(
    'status'     => 'success',
    'doc_number' => $doc,
    'baris'      => $jmlArsip,
));
