<?php
/* ============================================================================
   Update Payment Date PV Biaya - menyimpan SATU DOKUMEN.
   Pasangannya create-pvbiaya-payment-date.php.

   Polanya mengikuti simpan_payment_plan.php milik AP; yang berbeda cuma
   sumber vouchernya (PV Biaya dari Bank Out, bukan kontrabon/LP) dan kunci
   barisnya yang ikut membawa profit_center.

   NOMOR DOKUMEN DIBUAT DI SINI, bukan diambil dari formulir: kalau dua orang
   membuka formulir bersamaan, nomor yang mereka lihat sama dan yang satu akan
   menimpa yang lain. Dikunci dgn GET_LOCK selama nomornya dihitung.

   SATU DOKUMEN BOLEH MEMUAT TANGGAL BERBEDA-BEDA. Tiap baris kiriman boleh
   membawa plan_date sendiri; yang di kepala jadi tanggal DOKUMEN sekaligus
   cadangan untuk baris yang tidak membawa tanggalnya. Yang dibaca laporan
   adalah tanggal PER BARIS.

   VOUCHER YANG SUDAH PUNYA RENCANA TIDAK DITOLAK - rencananya memang harus
   bisa digeser (permintaan pemakai). Rencana lamanya DIPADAMKAN (status 'N')
   dan dokumen ini menggantikannya, jadi satu voucher selalu punya SATU
   rencana aktif. Tanggal lamanya tidak hilang: barisnya tetap ada sbg
   riwayat, yang berubah cuma penandanya.

   Voucher yang nomornya tidak ketemu, sudah tidak outstanding, atau belum
   SECOND APPROVED DITOLAK - dikembalikan lewat 'ditolak', bukan membatalkan
   seluruh dokumen. Pemeriksaannya DIULANG DI SINI dgn query yang sama persis
   dgn ajx_cari-pv-biaya.php, karena kiriman layar tidak boleh dipercaya:
   antara Search dan Save, vouchernya bisa saja sudah dibayar orang lain.

   KATEGORI ARUS KAS TIDAK DISENTUH sama sekali. Tidak ada satu pun UPDATE ke
   tbl_pv_h di berkas ini.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

/* Awalan nomor dokumen. SATU-SATUNYA tempat awalan ini ditulis - pencari
   nomor berikutnya memakai konstanta yang sama, jadi mengubahnya di sini
   sudah cukup. Dokumen lama berawalan lain tidak ikut terhitung, jadi
   nomornya mulai lagi dari 00001 untuk awalan baru. */
define('PVB_AWALAN', 'PPU-E');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

function pvbYmd($teks)
{
    $teks = trim((string) $teks);
    if ($teks === '') { return ''; }
    $t = strtotime($teks);
    return $t === false ? '' : date('Y-m-d', $t);
}

$plan_date  = pvbYmd(isset($_POST['plan_date']) ? $_POST['plan_date'] : '');
$keterangan = trim(isset($_POST['keterangan']) ? (string) $_POST['keterangan'] : '');
$baris      = json_decode(isset($_POST['baris']) ? $_POST['baris'] : '[]', true);

if ($plan_date === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Payment Plan Date is required.'));
    exit;
}
if (!is_array($baris) || count($baris) === 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Tick at least one payment voucher first.'));
    exit;
}

/* ---------------------------------------------------------------------------
   Keadaan voucher DIBACA ULANG dari sumbernya. Query ini sengaja dibuat sama
   persis dgn ajx_cari-pv-biaya.php - termasuk ambang >= 1 - supaya apa yang
   boleh disimpan tidak pernah lebih longgar dari apa yang boleh dicari.
   --------------------------------------------------------------------------- */
$sahQ = mysqli_query($conn2, "
with total_pv as (
  select b.profit_center, a.no_pv, a.curr,
         (SUM(b.amount - b.ded_add)
          + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100))
          - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total
    from tbl_pv_h a
    inner join tbl_pv b on b.no_pv = a.no_pv
   where a.status = 'Approved' and a.status_pl = 'SECOND APPROVED'
   group by a.no_pv, b.profit_center),
total_bk as (
  select a.profit_center, a.no_reff, sum(a.total) total_pv
    from b_bankout_det a
   inner join b_bankout_h b on b.no_bankout = a.no_bankout
   where b.status <> 'Cancel' and a.type_pv = 'Biaya'
   group by a.no_reff, a.profit_center)
select a.no_pv, a.profit_center, a.curr, (a.total - COALESCE(b.total_pv,0)) sisa
  from total_pv a
  left join total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center
 where (a.total - COALESCE(b.total_pv,0)) >= 1");
if (!$sahQ) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}
$sah = array();
while ($r = mysqli_fetch_assoc($sahQ)) {
    $sah[$r['no_pv'] . '|' . $r['profit_center']] = $r;
}

/* ---------------------------------------------------------------------------
   Nomor dokumen. Yang dihitung hanya dokumen bulan berjalan yang awalannya
   sama, supaya nomor tidak melompat kalau nanti ada awalan lain.
   --------------------------------------------------------------------------- */
function pvbNomor($conn)
{
    $awalEsc = mysqli_real_escape_string($conn, PVB_AWALAN);
    $r = mysqli_query($conn, "SELECT MAX(RIGHT(doc_number, 5)) AS kd_max
            FROM ap_pvbiaya_plan_h
            WHERE doc_number LIKE '$awalEsc/%'
              AND YEAR(created_date)  = YEAR(CURRENT_DATE())
              AND MONTH(created_date) = MONTH(CURRENT_DATE())");
    $kd = '00001';
    if ($r && ($b = mysqli_fetch_assoc($r)) && $b['kd_max'] !== null) {
        $kd = sprintf('%05s', ((int) $b['kd_max']) + 1);
    }
    date_default_timezone_set('Asia/Jakarta');
    return PVB_AWALAN . '/' . date('my') . '/' . $kd;
}

mysqli_query($conn2, "DO GET_LOCK('ap_pvbiaya_plan_gen', 10)");
$doc_number = pvbNomor($conn2);

$doc_esc  = mysqli_real_escape_string($conn2, $doc_number);
$plan_esc = mysqli_real_escape_string($conn2, $plan_date);
$user_esc = mysqli_real_escape_string($conn2, $user);
$ket_sql  = ($keterangan === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn2, $keterangan) . "'";

mysqli_query($conn2, 'START TRANSACTION');

$gagal = function ($pesan) use ($conn2) {
    mysqli_query($conn2, 'ROLLBACK');
    mysqli_query($conn2, "DO RELEASE_LOCK('ap_pvbiaya_plan_gen')");
    echo json_encode(array('status' => 'error', 'message' => $pesan));
    exit;
};

$ok = mysqli_query($conn2, "INSERT INTO ap_pvbiaya_plan_h
        (doc_number, plan_date, keterangan, status, created_by, created_date)
        VALUES ('$doc_esc', '$plan_esc', $ket_sql, 'POST', '$user_esc', NOW())");
if (!$ok) { $gagal(mysqli_error($conn2)); }

$tersimpan  = 0;
$ditolak    = array();
$digantikan = array();

foreach ($baris as $b) {
    $no_pv = trim(isset($b['no_pv']) ? (string) $b['no_pv'] : '');
    $pc    = trim(isset($b['profit_center']) ? (string) $b['profit_center'] : '');
    if ($no_pv === '') { continue; }

    $kunci = $no_pv . '|' . $pc;
    if (!isset($sah[$kunci])) {
        /* Sudah dibayar orang lain sesudah Search, atau nomornya karangan. */
        $ditolak[] = $no_pv . ($pc !== '' ? ' (' . $pc . ')' : '') . ' - no longer outstanding';
        continue;
    }
    $v = $sah[$kunci];

    $no_esc = mysqli_real_escape_string($conn2, $no_pv);
    $pc_esc = mysqli_real_escape_string($conn2, $pc);

    /* Rencana lama dipadamkan. Berjalan DI DALAM transaksi dan selama
       GET_LOCK masih dipegang, jadi penyimpan kedua baru melihat keadaan
       sesudah yang pertama commit. */
    $aktif = mysqli_query($conn2, "SELECT h.doc_number FROM ap_pvbiaya_plan_det d
            INNER JOIN ap_pvbiaya_plan_h h ON h.doc_number = d.doc_number
            WHERE d.no_pv = '$no_esc' AND d.profit_center = '$pc_esc'
              AND d.status = 'Y' AND h.status <> 'CANCEL'
            ORDER BY h.id DESC LIMIT 1");
    if ($aktif && mysqli_num_rows($aktif)) {
        $a = mysqli_fetch_assoc($aktif);
        $padam = mysqli_query($conn2, "UPDATE ap_pvbiaya_plan_det d
                INNER JOIN ap_pvbiaya_plan_h h ON h.doc_number = d.doc_number
                SET d.status = 'N'
                WHERE d.no_pv = '$no_esc' AND d.profit_center = '$pc_esc'
                  AND d.status = 'Y' AND h.status <> 'CANCEL'");
        if (!$padam) { $gagal(mysqli_error($conn2)); }
        $digantikan[] = $no_pv . ' (was in ' . $a['doc_number'] . ')';
    }

    $plan_baris = pvbYmd(isset($b['plan_date']) ? $b['plan_date'] : '');
    if ($plan_baris === '') { $plan_baris = $plan_date; }
    $planb_esc = mysqli_real_escape_string($conn2, $plan_baris);

    $curr_esc = mysqli_real_escape_string($conn2, (string) $v['curr']);
    $amount   = (float) $v['sisa'];
    $ketb     = trim(isset($b['keterangan']) ? (string) $b['keterangan'] : '');
    $ketb_sql = ($ketb === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn2, $ketb) . "'";

    $ok = mysqli_query($conn2, "INSERT INTO ap_pvbiaya_plan_det
            (doc_number, no_pv, profit_center, plan_date, curr, amount, keterangan, status)
            VALUES ('$doc_esc', '$no_esc', '$pc_esc', '$planb_esc', '$curr_esc', $amount, $ketb_sql, 'Y')");
    if (!$ok) { $gagal(mysqli_error($conn2)); }
    $tersimpan++;
}

if ($tersimpan === 0) {
    /* Dokumen tanpa isi tidak ada gunanya - kepalanya ikut dibatalkan. */
    mysqli_query($conn2, 'ROLLBACK');
    mysqli_query($conn2, "DO RELEASE_LOCK('ap_pvbiaya_plan_gen')");
    echo json_encode(array(
        'status'  => 'error',
        'message' => 'No payment voucher could be scheduled.',
        'ditolak' => $ditolak,
    ));
    exit;
}

mysqli_query($conn2, 'COMMIT');
mysqli_query($conn2, "DO RELEASE_LOCK('ap_pvbiaya_plan_gen')");

echo json_encode(array(
    'status'     => 'success',
    'doc_number' => $doc_number,
    'tersimpan'  => $tersimpan,
    'ditolak'    => $ditolak,
    'digantikan' => $digantikan,
));
