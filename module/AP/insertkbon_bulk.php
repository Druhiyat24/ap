<?php
// ============================================================================
// insertkbon_bulk.php — SIMPAN PV Regular sebagai SATU transaksi (all-or-nothing).
//
// Kenapa ada: alur lama = 1 request header + N request per-BPB (async). Kalau
// halaman pindah / proses mati / satu BPB gagal di tengah -> PV bisa tersimpan
// SEBAGIAN. Endpoint ini menerima header + seluruh BPB dalam 1 request, membungkus
// semua INSERT/UPDATE (di $conn2) dalam 1 transaksi InnoDB:
//   - semua sukses  -> COMMIT (PV utuh)
//   - ada yang gagal -> ROLLBACK total (TIDAK ada yang tersimpan)
//   - proses mati mendadak sebelum commit -> shutdown handler rollback + koneksi
//     putus => InnoDB auto-rollback.
//
// Logika simpan dipakai dari insertkbon_core.php (SALINAN dari insertkbon.php &
// insertkbon_h_pv_regular.php). File asli itu SENGAJA tidak diubah karena dipakai
// banyak form kontrabon lain.
//
// Payload (POST):
//   header = JSON object  (field sama persis dgn insertkbon_h_pv_regular.php)
//   items  = JSON array   (tiap elemen field sama persis dgn insertkbon.php)
// Respons (JSON): {ok:true, no_kbon} | {ok:false, code?, no_kbon?, msg, failed?}
// ============================================================================
// API: respons WAJIB JSON murni. Jangan biarkan notice/warning PHP (mis. lookup COA
// yang tak ketemu -> "array offset on null") tercetak ke body; kalau ikut tercetak,
// jQuery dataType:'json' gagal parse -> dianggap error padahal mungkin sudah commit.
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start(); // safety net: buang output tak terduga sebelum echo JSON

include '../../conn/conn.php';
require_once __DIR__ . '/insertkbon_core.php';
require_once __DIR__ . '/pv_ppn_group_faktur.php';
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');
header('Content-Type: application/json; charset=utf-8');

function pv_bulk_out($arr) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($arr); }

$headerData = json_decode($_POST['header'] ?? '', true);
$items      = json_decode($_POST['items'] ?? '[]', true);
if (!is_array($headerData) || empty($headerData)) { pv_bulk_out(['ok' => false, 'msg' => 'Payload header tidak valid.']); exit; }
if (!is_array($items)) { $items = []; }

// Safety net: kalau script mati (fatal/timeout) SEBELUM commit -> rollback.
$GLOBALS['__pv_bulk_committed'] = false;
register_shutdown_function(function () use ($conn2) {
    if (empty($GLOBALS['__pv_bulk_committed'])) { @mysqli_rollback($conn2); }
});

mysqli_begin_transaction($conn2);

// ---- 1) HEADER (kontrabon_h + potongan + jurnal beban/ppn) -----------------
$_POST = $headerData;
$rh = pv_reg_save_header($conn1, $conn2);

if (isset($rh['ir_used'])) {
    mysqli_rollback($conn2);
    pv_bulk_out(['ok' => false, 'code' => 'IR_ALREADY_USED', 'no_kbon' => $rh['ir_used'],
        'msg' => 'Invoice Received sudah dipakai PV lain' . ($rh['ir_used'] ? ' (' . $rh['ir_used'] . ')' : '') . '.']);
    exit;
}
if (empty($rh['ok'])) {
    mysqli_rollback($conn2);
    pv_bulk_out(['ok' => false, 'msg' => 'Gagal menyimpan header PV (kontrabon_h). Tidak ada yang tersimpan.']);
    exit;
}
$kode      = $rh['kode'];
$unik_code = $headerData['unik_code'] ?? '';

// ---- 2) SEMUA BPB / RO / FTR (kontrabon, return_kb, jurnal per-baris) -------
$failed = [];
foreach ($items as $i => $it) {
    if (!is_array($it)) { continue; }
    // Pastikan no_kbon = nomor PV yang BENAR2 dibuat header ini (bukan tebakan form),
    // dan unik_code ikut header supaya lookup $kode di dalam fungsi konsisten.
    $it['no_kbon']   = $kode;
    $it['unik_code'] = $unik_code !== '' ? $unik_code : ($it['unik_code'] ?? '');
    $_POST = $it;
    $rb = pv_reg_save_bpb($conn1, $conn2);
    if (empty($rb['ok'])) {
        $failed[] = ($it['no_bpb'] ?? '') !== '' ? $it['no_bpb'] : (($it['no_bppb'] ?? '') !== '' ? $it['no_bppb'] : ('#' . ($i + 1)));
    }
}

if (!empty($failed)) {
    mysqli_rollback($conn2);
    pv_bulk_out(['ok' => false, 'failed' => $failed,
        'msg' => count($failed) . ' baris gagal disimpan. SEMUA dibatalkan (rollback) — tidak ada yang tersimpan.']);
    exit;
}

// ---- 2b) PPN Masukan billed HEADER: pecah per NOMOR FAKTUR (jaga total=balance) --
if (!pv_group_ppn_billed_by_faktur($conn2, $kode)) {
    mysqli_rollback($conn2);
    pv_bulk_out(['ok' => false, 'msg' => 'Gagal mengelompokkan PPN Masukan per faktur. Tidak ada yang tersimpan.']);
    exit;
}

// ---- 3) COMMIT -------------------------------------------------------------
if (!mysqli_commit($conn2)) {
    mysqli_rollback($conn2);
    pv_bulk_out(['ok' => false, 'msg' => 'Commit gagal. Tidak ada yang tersimpan.']);
    exit;
}
$GLOBALS['__pv_bulk_committed'] = true;
pv_bulk_out(['ok' => true, 'no_kbon' => $kode]);
