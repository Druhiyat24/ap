<?php
/* ============================================================================
   Approve / Cancel BANYAK FTR DP sekaligus, dari halaman persetujuan.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dulu persetujuan dikerjakan satu per satu lewat approveftrdp.php /
   cancelftrdp.php dari halaman daftar. Halaman persetujuan memakai ceklis,
   jadi butuh endpoint yang menerima DAFTAR - bentuknya mengikuti
   proses_approve_update_bpb_*.php.

   Tiap dokumen dikerjakan dalam transaksinya SENDIRI: satu dokumen gagal
   tidak ikut membatalkan dokumen lain yang sudah benar.
   ============================================================================ */
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$list   = isset($_POST['no_ftr']) ? $_POST['no_ftr'] : array();
$user   = isset($_POST['approve_user']) ? trim($_POST['approve_user']) : '';

if (!is_array($list) || empty($list)) {
    echo json_encode(array('success' => false, 'message' => 'Select at least 1 FTR DP'));
    exit;
}
if (!in_array($action, array('approve', 'cancel'))) {
    echo json_encode(array('success' => false, 'message' => 'Unknown action'));
    exit;
}
if ($user === '') {
    echo json_encode(array('success' => false, 'message' => 'Your session has expired. Please sign in again.'));
    exit;
}

$user_esc = mysqli_real_escape_string($conn2, $user);
$waktu    = date('Y-m-d H:i:s');
$log_pc   = mysqli_real_escape_string($conn2, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-');

$berhasil = 0; $dilewati = 0; $pesan = array();

foreach ($list as $no) {
    $e = mysqli_real_escape_string($conn2, $no);

    mysqli_begin_transaction($conn2);

    /* Hanya draft yang boleh diproses - diperiksa ULANG di sini, bukan cuma
       mengandalkan daftar di layar yang bisa sudah basi. */
    $cek = mysqli_query($conn2, "select status, count(*) n from ftr_dp where no_ftr_dp = '$e' group by status");
    $status_ada = array();
    while ($cek && ($w = mysqli_fetch_assoc($cek))) { $status_ada[$w['status']] = (int) $w['n']; }

    if (empty($status_ada)) {
        mysqli_rollback($conn2); $dilewati++; $pesan[] = "$no: tidak ditemukan"; continue;
    }
    if (!isset($status_ada['draft'])) {
        mysqli_rollback($conn2); $dilewati++;
        $pesan[] = "$no: sudah " . implode(' / ', array_keys($status_ada)) . ", hanya draft yang bisa diproses";
        continue;
    }

    if ($action === 'approve') {
        $ok = mysqli_query($conn2, "update ftr_dp
                set confirm_date = '$waktu', confirm_user = '$user_esc', status = 'Approved'
              where no_ftr_dp = '$e' and status = 'draft'");
        $aktivitas = 'Approve FTR DP'; $ket = 'draft -> Approved';
    } else {
        $ok = mysqli_query($conn2, "update ftr_dp
                set cancel_date = '$waktu', cancel_user = '$user_esc', status = 'Cancel'
              where no_ftr_dp = '$e' and status = 'draft'");
        $aktivitas = 'Cancel FTR DP'; $ket = 'draft -> Cancel';
    }

    if (!$ok || mysqli_affected_rows($conn2) < 1) {
        mysqli_rollback($conn2); $dilewati++;
        $pesan[] = "$no: gagal diproses, statusnya mungkin baru diubah orang lain";
        continue;
    }

    /* Jejak aktivitas - lihat tbl_log_ftr. doc_date dibaca ULANG dari tabelnya,
       bukan dari kiriman browser. Gagal mencatat TIDAK membatalkan prosesnya. */
    mysqli_query($conn2, "insert into tbl_log_ftr (nama_user, activitas, from_pc, log_date, doc_num, doc_date, keterangan)
        select '$user_esc', '$aktivitas', '$log_pc', NOW(), '$e', MIN(tgl_ftr_dp), '$ket'
        from ftr_dp where no_ftr_dp = '$e'");

    mysqli_commit($conn2);
    $berhasil++;
}

echo json_encode(array(
    'success'  => true,
    'updated'  => $berhasil,
    'skipped'  => $dilewati,
    'warnings' => $pesan,
));
?>