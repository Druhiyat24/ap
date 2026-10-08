<?php
/* ============================================================================
   Cancel FTR CBD.

   Dipanggil lewat AJAX dari ftrcbd.php dan sekarang MENJAWAB dgn JSON, supaya
   halaman bisa menampilkan pesan berhasil/gagal yang sebenarnya. Sebelumnya
   berkas ini mencetak teks biasa lalu memasang header Refresh - keduanya tidak
   ada artinya untuk pemanggil AJAX.
   ============================================================================ */
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

function jawab($kode, $isi) {
    http_response_code($kode);
    echo json_encode($isi);
    exit;
}

$noftrcbd    = isset($_POST['noftrcbd']) ? trim($_POST['noftrcbd']) : '';
$cancel_user = isset($_POST['cancel_user']) ? trim($_POST['cancel_user']) : '';
$cancel_date = date('Y-m-d H:i:s');

if ($noftrcbd === '')    { jawab(400, array('ok' => false, 'message' => 'FTR CBD number is missing.')); }
if ($cancel_user === '') { jawab(400, array('ok' => false, 'message' => 'Your session has expired. Please sign in again.')); }

$no_esc = mysqli_real_escape_string($conn2, $noftrcbd);

/* Status diperiksa DULU - hanya dokumen draft yang boleh dibatalkan, sama
   seperti tombol Cancel yang memang cuma muncul di baris draft. */
$cek = mysqli_query($conn2, "select status, count(*) baris from ftr_cbd where no_ftr_cbd = '$no_esc' group by status");
if (!$cek) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$status_ada = array();
while ($r = mysqli_fetch_assoc($cek)) { $status_ada[$r['status']] = (int) $r['baris']; }

if (empty($status_ada))           { jawab(404, array('ok' => false, 'message' => 'FTR CBD ' . $noftrcbd . ' was not found.')); }
if (isset($status_ada['Cancel'])) { jawab(409, array('ok' => false, 'message' => 'FTR CBD ' . $noftrcbd . ' has already been canceled.')); }
if (!isset($status_ada['draft'])) { jawab(409, array('ok' => false, 'message' => 'FTR CBD ' . $noftrcbd . ' is already ' . implode(' / ', array_keys($status_ada)) . '. Only a draft can be canceled.')); }

$user_esc = mysqli_real_escape_string($conn2, $cancel_user);
$ok = mysqli_query($conn2, "update ftr_cbd
        set cancel_date = '$cancel_date', cancel_user = '$user_esc', status = 'Cancel'
        where no_ftr_cbd = '$no_esc' and status = 'draft'");

if (!$ok) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$terdampak = mysqli_affected_rows($conn2);
if ($terdampak < 1) { jawab(409, array('ok' => false, 'message' => 'Nothing was canceled - the status may have been changed by someone else.')); }

echo json_encode(array(
    'ok'      => true,
    'rows'    => $terdampak,
    'message' => 'FTR CBD ' . $noftrcbd . ' has been canceled.',
));

mysqli_close($conn2);
