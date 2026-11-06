<?php
/* ============================================================================
   Update Payment Plan - menyimpan SATU DOKUMEN. Pasangannya
   create-payment-plan.php.

   Nomor dokumennya DIBUAT DI SINI, bukan diambil dari formulir: kalau dua
   orang membuka formulir bersamaan, nomor yang mereka lihat sama dan yang
   satu akan menimpa yang lain. Polanya mengikuti simpan_data_duedate() di
   AR - kunci penasihat (GET_LOCK) selama nomornya dihitung, lalu dilepas.

   Voucher yang nomornya tidak ada di kontrabon_h, atau vouchernya belum
   disetujui, DITOLAK - supaya dokumen rencana tidak memuat nomor karangan
   yang nanti tidak bisa dipasangkan ke laporan proyeksi.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

function appYmd($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));   /* dd-mm-yyyy */
    return $t ? date('Y-m-d', $t) : '';
}

$plan_date  = appYmd(isset($_POST['plan_date']) ? $_POST['plan_date'] : '');
$keterangan = trim(isset($_POST['keterangan']) ? (string) $_POST['keterangan'] : '');
$baris      = json_decode(isset($_POST['baris']) ? $_POST['baris'] : '', true);

if ($plan_date === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Please fill in the payment plan date.'));
    exit;
}
if (!is_array($baris) || !$baris) {
    echo json_encode(array('status' => 'error', 'message' => 'Please tick at least one payment voucher.'));
    exit;
}

/* Nomor dokumen: AP-PP/<bulan><tahun>/<urut 5 digit>, urutnya per bulan -
   bentuk yang sama dgn DDU/<bulan><tahun>/<urut> di AR. */
function appNomor($conn1)
{
    $r = mysqli_query($conn1, "SELECT MAX(RIGHT(doc_number, 5)) AS kd_max
            FROM ap_payment_plan_h
            WHERE YEAR(created_date) = YEAR(CURRENT_DATE())
              AND MONTH(created_date) = MONTH(CURRENT_DATE())");
    $kd = '00001';
    if ($r && ($b = mysqli_fetch_assoc($r)) && $b['kd_max'] !== null) {
        $kd = sprintf('%05s', ((int) $b['kd_max']) + 1);
    }
    date_default_timezone_set('Asia/Jakarta');
    return 'AP-PP/' . date('my') . '/' . $kd;
}

mysqli_query($conn1, "DO GET_LOCK('ap_payment_plan_gen', 10)");
$doc_number = appNomor($conn1);

$plan_esc = mysqli_real_escape_string($conn1, $plan_date);
$ket_sql  = ($keterangan === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn1, $keterangan) . "'";
$user_esc = mysqli_real_escape_string($conn1, $user);
$doc_esc  = mysqli_real_escape_string($conn1, $doc_number);

mysqli_query($conn1, 'START TRANSACTION');

$ok = mysqli_query($conn1, "INSERT INTO ap_payment_plan_h
        (doc_number, plan_date, keterangan, status, created_by, created_date)
        VALUES ('$doc_esc', '$plan_esc', $ket_sql, 'POST', '$user_esc', NOW())");
if (!$ok) {
    mysqli_query($conn1, 'ROLLBACK');
    mysqli_query($conn1, "DO RELEASE_LOCK('ap_payment_plan_gen')");
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn1)));
    exit;
}

$tersimpan = 0;
$ditolak   = array();

foreach ($baris as $b) {
    $no_kbon = isset($b['no_kbon']) ? trim((string) $b['no_kbon']) : '';
    if ($no_kbon === '') { continue; }
    $no_esc = mysqli_real_escape_string($conn1, $no_kbon);

    /* Nomornya harus ada di kontrabon_h dan vouchernya sudah disetujui -
       sama dgn penyaring daftarnya, supaya tidak ada yang bisa menjadwalkan
       voucher batal lewat permintaan yang dirakit sendiri. Nilai & mata
       uangnya diambil dari tabelnya, bukan dari kiriman halaman. */
    $cek = mysqli_query($conn1, "SELECT curr, total FROM kontrabon_h
            WHERE no_kbon = '$no_esc'
              AND status IN ('Approved','FIRST APPROVED','SECOND APPROVED') LIMIT 1");
    if (!$cek || !mysqli_num_rows($cek)) {
        $ditolak[] = $no_kbon . ' (voucher not found or not approved)';
        continue;
    }
    $v = mysqli_fetch_assoc($cek);

    $curr_esc = mysqli_real_escape_string($conn1, (string) $v['curr']);
    $amount   = (float) $v['total'];
    $ketb     = trim(isset($b['keterangan']) ? (string) $b['keterangan'] : '');
    $ketb_sql = ($ketb === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn1, $ketb) . "'";

    $ok = mysqli_query($conn1, "INSERT INTO ap_payment_plan_det
            (doc_number, no_kbon, plan_date, curr, amount, keterangan, status)
            VALUES ('$doc_esc', '$no_esc', '$plan_esc', '$curr_esc', $amount, $ketb_sql, 'Y')");
    if (!$ok) {
        mysqli_query($conn1, 'ROLLBACK');
        mysqli_query($conn1, "DO RELEASE_LOCK('ap_payment_plan_gen')");
        echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn1)));
        exit;
    }
    $tersimpan++;
}

if ($tersimpan === 0) {
    /* Dokumen tanpa isi tidak ada gunanya - kepalanya ikut dibatalkan. */
    mysqli_query($conn1, 'ROLLBACK');
    mysqli_query($conn1, "DO RELEASE_LOCK('ap_payment_plan_gen')");
    echo json_encode(array(
        'status'  => 'error',
        'message' => 'No voucher could be scheduled.',
        'ditolak' => $ditolak,
    ));
    exit;
}

mysqli_query($conn1, 'COMMIT');
mysqli_query($conn1, "DO RELEASE_LOCK('ap_payment_plan_gen')");

echo json_encode(array(
    'status'     => 'success',
    'doc_number' => $doc_number,
    'tersimpan'  => $tersimpan,
    'ditolak'    => $ditolak,
    'message'    => 'Payment plan ' . $doc_number . ' saved with ' . $tersimpan
                    . ' voucher' . ($tersimpan == 1 ? '' : 's') . '.',
));
?>
