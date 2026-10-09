<?php
/* ============================================================================
   Cancel FTR DP.

   Dipanggil lewat AJAX dari ftrdp.php dan sekarang MENJAWAB dgn JSON, supaya
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

$noftrdp    = isset($_POST['noftrdp']) ? trim($_POST['noftrdp']) : '';
$cancel_user = isset($_POST['cancel_user']) ? trim($_POST['cancel_user']) : '';
$cancel_date = date('Y-m-d H:i:s');

if ($noftrdp === '')    { jawab(400, array('ok' => false, 'message' => 'FTR DP number is missing.')); }
if ($cancel_user === '') { jawab(400, array('ok' => false, 'message' => 'Your session has expired. Please sign in again.')); }

$no_esc = mysqli_real_escape_string($conn2, $noftrdp);

/* Status diperiksa DULU - hanya dokumen draft yang boleh dibatalkan, sama
   seperti tombol Cancel yang memang cuma muncul di baris draft. */
$cek = mysqli_query($conn2, "select status, count(*) baris from ftr_dp where no_ftr_dp = '$no_esc' group by status");
if (!$cek) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$status_ada = array();
while ($r = mysqli_fetch_assoc($cek)) { $status_ada[$r['status']] = (int) $r['baris']; }

if (empty($status_ada))           { jawab(404, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' was not found.')); }
if (isset($status_ada['Cancel'])) { jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' has already been canceled.')); }
if (!isset($status_ada['draft'])) { jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $noftrdp . ' is already ' . implode(' / ', array_keys($status_ada)) . '. Only a draft can be canceled.')); }

$user_esc = mysqli_real_escape_string($conn2, $cancel_user);
$ok = mysqli_query($conn2, "update ftr_dp
        set cancel_date = '$cancel_date', cancel_user = '$user_esc', status = 'Cancel'
        where no_ftr_dp = '$no_esc' and status = 'draft'");

if (!$ok) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$terdampak = mysqli_affected_rows($conn2);
if ($terdampak < 1) { jawab(409, array('ok' => false, 'message' => 'Nothing was canceled - the status may have been changed by someone else.')); }

/* ---- jejak aktivitas, lihat tbl_log_ftr (bentuknya mengikuti tbl_log_cash) ----
   doc_date dibaca ULANG dari tabelnya, bukan dari kiriman browser, supaya
   tanggal yang tercatat pasti sama dgn isi dokumen. Gagal mencatat TIDAK
   boleh menggagalkan transaksinya - karena itu hasilnya tidak diperiksa. */
$log_pc   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
$log_user = mysqli_real_escape_string($conn2, $cancel_user);
$log_no   = mysqli_real_escape_string($conn2, $noftrdp);
$log_pc   = mysqli_real_escape_string($conn2, $log_pc);
mysqli_query($conn2, "insert into tbl_log_ftr (nama_user, activitas, from_pc, log_date, doc_num, doc_date, keterangan)
    select '$log_user', 'Cancel FTR DP', '$log_pc', NOW(), '$log_no', MIN(tgl_ftr_dp), 'draft -> Cancel'
    from ftr_dp where no_ftr_dp = '$log_no'");

echo json_encode(array(
    'ok'      => true,
    'rows'    => $terdampak,
    'message' => 'FTR DP ' . $noftrdp . ' has been canceled.',
));

mysqli_close($conn2);
