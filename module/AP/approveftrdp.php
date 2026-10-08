<?php
/* ============================================================================
   Approve FTR DP.

   Dipanggil lewat AJAX dari ftrdp.php dan sekarang MENJAWAB dgn JSON, supaya
   halaman bisa menampilkan pesan berhasil/gagal yang sebenarnya. Sebelumnya
   berkas ini tidak pernah memberi tahu kalau update-nya gagal - pemanggilnya
   selalu menganggap berhasil.
   ============================================================================ */
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

function jawab($kode, $isi) {
    http_response_code($kode);
    echo json_encode($isi);
    exit;
}

$noftrdp = isset($_POST['noftrdp']) ? trim($_POST['noftrdp']) : '';
/* Dulu baris ini membaca $_POST['cancel_user'] - nama field yg memang tidak
   pernah dikirim saat Approve, sehingga confirm_user selalu tersimpan kosong. */
$confirm_user = isset($_POST['confirm_user']) ? trim($_POST['confirm_user']) : '';
$confirm_date = date('Y-m-d H:i:s');

if ($noftrdp === '')     { jawab(400, array('ok' => false, 'message' => 'FTR DP number is missing.')); }
if ($confirm_user === '') { jawab(400, array('ok' => false, 'message' => 'Your session has expired. Please sign in again.')); }

$no_esc = mysqli_real_escape_string($conn2, $noftrdp);

/* Status diperiksa DULU. Tanpa ini, dokumen yang sudah di-approve atau sudah
   di-cancel bisa ditimpa lagi - termasuk dokumen batal yang akan hidup kembali
   sbg Approved. */
$cek = mysqli_query($conn2, "select status, count(*) baris from ftr_dp where no_ftr_dp = '$no_esc' group by status");
if (!$cek) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$status_ada = array();
while ($r = mysqli_fetch_assoc($cek)) { $status_ada[$r['status']] = (int) $r['baris']; }

if (empty($status_ada))                  { jawab(404, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' was not found.')); }
if (!isset($status_ada['draft']))        { jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' is already ' . implode(' / ', array_keys($status_ada)) . '. Only a draft can be approved.')); }
if (count($status_ada) > 1)              { jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' has mixed statuses (' . implode(' / ', array_keys($status_ada)) . '). Please check it first.')); }

$user_esc = mysqli_real_escape_string($conn2, $confirm_user);
$ok = mysqli_query($conn2, "update ftr_dp
        set confirm_date = '$confirm_date', confirm_user = '$user_esc', status = 'Approved'
        where no_ftr_dp = '$no_esc' and status = 'draft'");

if (!$ok) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$terdampak = mysqli_affected_rows($conn2);
if ($terdampak < 1) { jawab(409, array('ok' => false, 'message' => 'Nothing was approved - the status may have been changed by someone else.')); }

echo json_encode(array(
    'ok'      => true,
    'rows'    => $terdampak,
    'message' => 'FTR DP ' . $noftrdp . ' has been approved.',
));

mysqli_close($conn2);
