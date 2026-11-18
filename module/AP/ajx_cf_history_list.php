<?php
/* ============================================================================
   DAFTAR potret Cash Flow Projection yang disimpan lewat Save History.

   Baca-saja. Angkanya TIDAK dihitung ulang - yang ditampilkan ringkasan dari
   kepala dokumennya saja; isinya dibaca lewat ajx_cf_history_detail.php.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

function cfhTgl($conn, $v, $bawaan)
{
    $v = trim((string) $v);
    if ($v === '') { return $bawaan; }
    $t = strtotime(str_replace('/', '-', $v));
    return mysqli_real_escape_string($conn, $t ? date('Y-m-d', $t) : $bawaan);
}

$dari   = cfhTgl($conn2, isset($_POST['start_date']) ? $_POST['start_date'] : '', date('Y-m-01'));
$sampai = cfhTgl($conn2, isset($_POST['end_date'])   ? $_POST['end_date']   : '', date('Y-m-t'));
$status = strtoupper(trim(isset($_POST['status']) ? $_POST['status'] : 'ALL'));

/* Saringan tanggalnya memakai TANGGAL DOKUMEN DIBUAT, bukan periodenya:
   yang dicari orang biasanya "potret yang saya simpan minggu lalu", bukan
   "potret yang periodenya menyentuh tanggal ini". */
$syarat = "date(created_date) between '$dari' and '$sampai'";
if ($status !== 'ALL' && $status !== '') {
    $syarat .= " and status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}

$q = mysqli_query($conn2, "select doc_number, periode_dari, periode_sampai, profit_center, bank,
                                  type, rate, jml_baris, status, created_by, created_date,
                                  cancel_by, cancel_date
                             from ap_cf_history_h
                            where $syarat
                            order by id desc");
if (!$q) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

/* Tombol Realization di daftar ini membuka Report Cash Flow Realisation
   dgn periode & akun potretnya. Peta akunnya dibangun SEKALI di luar
   perulangan - potret yang saringannya sama menghasilkan daftar yang sama,
   jadi hasilnya disimpan sebentar supaya tidak dihitung berulang. */
include_once __DIR__ . '/cf_akun_lib.php';
$cfhAkunCache = array();

$data = array();
while ($r = mysqli_fetch_assoc($q)) {
    $cfhKunciSaring = strtoupper(trim((string) $r['profit_center'])) . '|' . trim((string) $r['bank']);
    if (!isset($cfhAkunCache[$cfhKunciSaring])) {
        $cfhAkunCache[$cfhKunciSaring] = cf_akun_realisasi($conn2, $r['profit_center'], $r['bank']);
    }
    $data[] = array(
        'doc_number' => $r['doc_number'],
        /* dd-mm-yyyy: bentuk yang dibaca isian tanggal laporan realisasinya */
        'tgl1'       => date('d-m-Y', strtotime($r['periode_dari'])),
        'tgl2'       => date('d-m-Y', strtotime($r['periode_sampai'])),
        'akun'       => $cfhAkunCache[$cfhKunciSaring],
        'periode'    => date('d-M-Y', strtotime($r['periode_dari'])) . ' s/d ' . date('d-M-Y', strtotime($r['periode_sampai'])),
        'pc'         => $r['profit_center'],
        'bank'       => $r['bank'],
        'type'       => ucfirst(strtolower($r['type'])),
        'rate'       => (float) $r['rate'],
        'jml_baris'  => (int) $r['jml_baris'],
        'status'     => $r['status'],
        'created_by' => $r['created_by'],
        'created'    => $r['created_date'] ? date('d-M-Y H:i', strtotime($r['created_date'])) : '',
        'cancel_by'  => (string) $r['cancel_by'],
        'cancel'     => $r['cancel_date'] ? date('d-M-Y H:i', strtotime($r['cancel_date'])) : '',
    );
}

echo json_encode(array('status' => 'success', 'data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
