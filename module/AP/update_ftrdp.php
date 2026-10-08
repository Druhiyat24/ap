<?php
/* ============================================================================
   Simpan hasil edit FTR DP.

   SATU permintaan, SATU transaksi: baris lama dihapus lalu baris baru ditulis.
   Dipisah per baris seperti Create tidak bisa dipakai di sini - kalau putus di
   tengah, dokumennya tinggal separuh dan tidak ada yang mengembalikannya.

   HANYA dokumen draft. Dokumen Approved dirujuk kontrabon_ftr / payment_ftrdp
   / pa_saldo_awal, jadi menambah atau membuang PO di sana membuat angka di
   dokumen hilir tidak cocok lagi.
   ============================================================================ */
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

function jawab($kode, $isi) {
    http_response_code($kode);
    echo json_encode($isi);
    exit;
}

$no        = isset($_POST['noftrdp']) ? trim((string) $_POST['noftrdp']) : '';
$edit_user = isset($_POST['edit_user']) ? trim((string) $_POST['edit_user']) : '';
$nama_supp = isset($_POST['nama_supp']) ? trim((string) $_POST['nama_supp']) : '';
$keterangan = isset($_POST['keterangan']) ? (string) $_POST['keterangan'] : '';
$profit_center = isset($_POST['profit_center']) ? trim((string) $_POST['profit_center']) : '';

if ($no === '')        { jawab(400, array('ok' => false, 'message' => 'FTR DP number is missing.')); }
if ($edit_user === '') { jawab(400, array('ok' => false, 'message' => 'Your session has expired. Please sign in again.')); }

$tgl_ftr   = !empty($_POST['tglftrdp']) ? date('Y-m-d', strtotime($_POST['tglftrdp'])) : '';
$tgl_bayar = !empty($_POST['tgl_bayar']) && $_POST['tgl_bayar'] !== '-' ? date('Y-m-d', strtotime($_POST['tgl_bayar'])) : '';
if ($tgl_ftr === '' || $tgl_ftr <= '1970-01-01')     { jawab(400, array('ok' => false, 'message' => 'FTR DP Date is required.')); }
if ($tgl_bayar === '' || $tgl_bayar <= '1970-01-01') { jawab(400, array('ok' => false, 'message' => 'Payment Date is required.')); }
/* Payment Date tidak boleh mendahului FTR Date - penjaga di form bisa dilewati. */
if ($tgl_ftr !== '' && $tgl_bayar < $tgl_ftr) {
    jawab(400, array('ok' => false, 'message' =>
        'Payment Date (' . date('d-M-Y', strtotime($tgl_bayar))
        . ') cannot be earlier than FTR DP Date (' . date('d-M-Y', strtotime($tgl_ftr)) . ').'));
}

/* Cara bayar & jenis barang diperiksa SAMA KETATNYA dgn saat dibuat - kalau di
   sini lebih longgar, nilai yang tidak mungkin lolos lewat Create bisa masuk
   lewat Edit. */
$payment_method = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
if ($payment_method !== 'Transfer' && $payment_method !== 'Cash') {
    jawab(400, array('ok' => false, 'message' => 'Payment Method is required (Transfer / Cash).'));
}
$item_type = isset($_POST['item_type']) ? trim((string) $_POST['item_type']) : '';
if ($item_type === '') { jawab(400, array('ok' => false, 'message' => 'Item Type is required.')); }
$cekIt = mysqli_query($conn1, "select 1 from pv_mapping_jurnal_dp
    where status = 'Y' and item_type = '" . mysqli_real_escape_string($conn1, $item_type) . "' limit 1");
if (!$cekIt || mysqli_num_rows($cekIt) === 0) {
    jawab(400, array('ok' => false, 'message' => 'Item Type is not recognised.'));
}

$baris = json_decode(isset($_POST['baris']) ? $_POST['baris'] : '', true);
if (!is_array($baris) || count($baris) === 0) {
    jawab(400, array('ok' => false, 'message' => 'No PO row was sent.'));
}
foreach ($baris as $b) {
    if (!isset($b['no_po']) || trim((string) $b['no_po']) === '') {
        jawab(400, array('ok' => false, 'message' => 'One of the rows has no PO number.'));
    }
    if (!isset($b['no_pi']) || trim((string) $b['no_pi']) === '') {
        jawab(400, array('ok' => false, 'message' => 'Fill in the PI number for every selected PO.'));
    }
}

$no_esc = mysqli_real_escape_string($conn2, $no);

/* Status diperiksa DULU, dan dibaca lagi DI DALAM transaksi di bawah supaya
   tidak ada celah antara memeriksa dan menulis. */
/* Dikelompokkan per STATUS saja. create_user/create_date diambil dgn MIN()
   supaya selisih satu detik antar baris tidak dibaca sbg "dokumen tidak
   seragam". */
$cek = mysqli_query($conn2, "select status, MIN(create_user) create_user, MIN(create_date) create_date
    from ftr_dp where no_ftr_dp = '$no_esc' group by status");
if (!$cek) { jawab(500, array('ok' => false, 'message' => 'Database error: ' . mysqli_error($conn2))); }

$jml_status = mysqli_num_rows($cek);
if ($jml_status === 0) { jawab(404, array('ok' => false, 'message' => 'FTR DP ' . $no . ' was not found.')); }
if ($jml_status > 1) {
    /* Lebih dari satu status dalam satu nomor = dokumennya tidak seragam;
       jangan disentuh tanpa diperiksa orang. */
    jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $no . ' has mixed statuses. Please check it first.'));
}

$lama = mysqli_fetch_assoc($cek);
if ($lama['status'] !== 'draft') {
    jawab(409, array('ok' => false, 'message' => 'FTR DP ' . $no . ' is ' . $lama['status'] . '. Only a draft can be edited.'));
}

/* create_user & create_date dipertahankan: yang diganti isinya, bukan siapa
   yang membuatnya. */
$create_user = (string) $lama['create_user'];
$create_date = (string) $lama['create_date'];

mysqli_begin_transaction($conn2);

$hapus = mysqli_query($conn2, "delete from ftr_dp where no_ftr_dp = '$no_esc' and status = 'draft'");
if (!$hapus) {
    $e = mysqli_error($conn2);
    mysqli_rollback($conn2);
    jawab(500, array('ok' => false, 'message' => 'Database error: ' . $e));
}
if (mysqli_affected_rows($conn2) < 1) {
    mysqli_rollback($conn2);
    jawab(409, array('ok' => false, 'message' => 'Nothing was replaced - the status may have been changed by someone else.'));
}

$esc = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$nilai = array();
foreach ($baris as $b) {
    $tgl_po = !empty($b['tgl_po']) ? date('Y-m-d', strtotime($b['tgl_po'])) : '';
    $nilai[] = "('" . $esc($no) . "', '" . $esc($tgl_ftr) . "', '" . $esc($tgl_bayar) . "', '" . $esc($nama_supp) . "', '"
        . $esc($b['no_po']) . "', " . ($tgl_po === '' ? 'NULL' : "'" . $esc($tgl_po) . "'") . ", '" . $esc($b['no_pi']) . "', "
        . (float) (isset($b['total'])    ? $b['total']    : 0) . ", "
        . (float) (isset($b['dp_code'])  ? $b['dp_code']  : 0) . ", "
        . (float) (isset($b['dp_value']) ? $b['dp_value'] : 0) . ", "
        . (float) (isset($b['balance'])  ? $b['balance']  : 0) . ", '"
        . $esc(isset($b['curr']) ? $b['curr'] : '') . "', '" . $esc($payment_method) . "', '" . $esc($item_type) . "', '" . $esc($profit_center) . "', '"
        . $esc($keterangan) . "', 'draft', '" . $esc($create_user) . "', '" . $esc($create_date) . "', 'Waiting')";
}

$simpan = mysqli_query($conn2, "insert into ftr_dp
    (no_ftr_dp, tgl_ftr_dp, tgl_bayar, supp, no_po, tgl_po, no_pi, total, dp, dp_value, balance, curr,
     payment_method, item_type, profit_center, keterangan, status, create_user, create_date, is_invoiced)
    values " . implode(', ', $nilai));
if (!$simpan) {
    $e = mysqli_error($conn2);
    mysqli_rollback($conn2);
    jawab(500, array('ok' => false, 'message' => 'Database error: ' . $e));
}

mysqli_commit($conn2);

echo json_encode(array(
    'ok'      => true,
    'rows'    => count($nilai),
    'message' => 'FTR DP ' . $no . ' has been updated.',
));
