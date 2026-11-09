<?php
/* ============================================================================
   Update Payment Plan - menyimpan SATU DOKUMEN. Pasangannya
   create-payment-plan.php.

   Nomor dokumennya DIBUAT DI SINI, bukan diambil dari formulir: kalau dua
   orang membuka formulir bersamaan, nomor yang mereka lihat sama dan yang
   satu akan menimpa yang lain. Polanya mengikuti simpan_data_duedate() di
   AR - kunci penasihat (GET_LOCK) selama nomornya dihitung, lalu dilepas.

   SATU DOKUMEN BOLEH MEMUAT TANGGAL YANG BERBEDA-BEDA (5 Nov 2026).
   Tiap baris kiriman boleh membawa plan_date sendiri; yang di kepala
   ('plan_date' di luar baris) jadi tanggal DOKUMEN sekaligus cadangan untuk
   baris yang tidak membawa tanggalnya. Laporan proyeksi membaca
   ap_payment_plan_det.plan_date - yaitu tanggal PER BARIS - jadi yang
   menentukan di situ memang tanggal barisnya, bukan tanggal kepalanya.

   Voucher yang nomornya tidak ada di kontrabon_h atau vouchernya belum
   disetujui DITOLAK - supaya dokumen rencana tidak memuat nomor karangan
   yang nanti tidak bisa dipasangkan ke laporan proyeksi. Yang ditolak
   dikembalikan lewat 'ditolak', bukan membatalkan seluruh dokumen.

   Voucher yang SUDAH punya rencana aktif dulu ikut ditolak. Sejak
   9 Nov 2026 tidak lagi: rencananya boleh diubah selama vouchernya masih
   outstanding (permintaan pemakai), jadi rencana lamanya DIPADAMKAN -
   status barisnya jadi 'N' - dan dokumen ini menggantikannya. Satu voucher
   tetap hanya punya SATU rencana aktif. Yang digantikan dikembalikan lewat
   'digantikan' supaya kelihatan di layar.
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

/* Awalan nomor dokumen. Diganti dari 'AP-PP' ke 'PPU' atas permintaan
   pemakai (5 Nov 2026). Ditaruh di SATU tempat supaya pembuatan nomor dan
   penyaring urutannya tidak bisa melenceng satu sama lain. */
define('PP_AWALAN', 'PPU');

/* Nomor dokumen: PPU/<bulan><tahun>/<urut 5 digit>, urutnya per bulan -
   bentuk yang sama dgn DDU/<bulan><tahun>/<urut> di AR.

   Urutannya dihitung HANYA dari dokumen ber-awalan sama. Tanpa itu, dokumen
   lama ber-awalan AP-PP di bulan yang sama ikut terhitung dan nomor PPU
   pertama akan melompat. */
function appNomor($conn1)
{
    $awalEsc = mysqli_real_escape_string($conn1, PP_AWALAN);
    $r = mysqli_query($conn1, "SELECT MAX(RIGHT(doc_number, 5)) AS kd_max
            FROM ap_payment_plan_h
            WHERE doc_number LIKE '$awalEsc/%'
              AND YEAR(created_date) = YEAR(CURRENT_DATE())
              AND MONTH(created_date) = MONTH(CURRENT_DATE())");
    $kd = '00001';
    if ($r && ($b = mysqli_fetch_assoc($r)) && $b['kd_max'] !== null) {
        $kd = sprintf('%05s', ((int) $b['kd_max']) + 1);
    }
    date_default_timezone_set('Asia/Jakarta');
    return PP_AWALAN . '/' . date('my') . '/' . $kd;
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

$tersimpan  = 0;
$ditolak    = array();
/* Voucher yang rencana lamanya digantikan dokumen ini. Dilaporkan balik ke
   layar supaya penjadwalan ulang KELIHATAN - kalau diam saja, orang tidak
   tahu bahwa dokumen lain baru saja kehilangan satu barisnya. */
$digantikan = array();

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

    /* Satu voucher hanya boleh punya SATU rencana aktif. Dulu penjaganya
       cuma di tampilan: formulirnya menyaring voucher yang sudah punya
       rencana dan kolom "Current Plan" memperlihatkan nomor dokumennya.
       Kedua hal itu dihapus 5 Nov 2026 atas permintaan pemakai, jadi
       penjaganya dipindah ke sini - sekaligus menutup celah yang sejak awal
       terbuka: dua orang yang membuka formulir bersamaan sama-sama melihat
       voucher itu "belum dijadwalkan" dan menyimpannya di dua dokumen.

       Pemeriksaannya berjalan DI DALAM transaksi dan selama GET_LOCK masih
       dipegang, jadi penyimpan kedua baru memeriksa setelah yang pertama
       commit. Syaratnya sama persis dgn $rencana di
       ajx_cari-pv-payment-plan.php: baris 'Y' milik dokumen yang belum
       dibatalkan. Dokumen yang dibatalkan membebaskan vouchernya kembali. */
    $aktif = mysqli_query($conn1, "SELECT h.doc_number FROM ap_payment_plan_det d
            INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
            WHERE d.no_kbon = '$no_esc' AND d.status = 'Y' AND h.status <> 'CANCEL'
            ORDER BY h.id DESC LIMIT 1");
    if ($aktif && mysqli_num_rows($aktif)) {
        /* DIJADWALKAN ULANG, bukan ditolak (permintaan pemakai 9 Nov 2026):
           rencana satu voucher harus bisa diubah selama vouchernya masih
           outstanding. Rencana lamanya DIPADAMKAN di sini - status barisnya
           jadi 'N', persis seperti yang dilakukan pembatalan dokumen - lalu
           baris baru di bawah menggantikannya.

           Dipadamkan, BUKAN dibiarkan hidup berdua: syarat "rencana yang
           berlaku" di layar Payment Plan, laporan proyeksi dan laporan
           realisasi ditulis sbg baris 'Y' milik dokumen terbaru. Dua baris
           'Y' sekaligus memang masih terbaca benar oleh ketiganya (semuanya
           memakai MAX(h.id)), tapi menghitung "berapa voucher terjadwal"
           jadi menggandakan - dan tidak ada satu pun tempat yang menjamin
           dua baris itu sedate.

           Yang TIDAK hilang: tanggal rencana lama tetap tersimpan di baris
           itu, jadi dokumen lamanya tetap bisa dibaca sbg riwayat; yang
           berubah cuma penandanya, dari berlaku jadi tidak.

           Aman dari dua penyimpan bersamaan: ini berjalan di dalam
           transaksi yang sama dan selama GET_LOCK masih dipegang, jadi
           penyimpan kedua baru melihat keadaan setelah yang pertama
           commit - penjaga yang dipasang 5 Nov 2026 tetap utuh. */
        $a = mysqli_fetch_assoc($aktif);
        $padam = mysqli_query($conn1, "UPDATE ap_payment_plan_det d
                INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
                SET d.status = 'N'
                WHERE d.no_kbon = '$no_esc' AND d.status = 'Y' AND h.status <> 'CANCEL'");
        if (!$padam) {
            mysqli_query($conn1, 'ROLLBACK');
            mysqli_query($conn1, "DO RELEASE_LOCK('ap_payment_plan_gen')");
            echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn1)));
            exit;
        }
        $digantikan[] = $no_kbon . ' (was in ' . $a['doc_number'] . ')';
    }

    /* Tanggal rencana PER BARIS. Layar Create mengirimkannya untuk setiap
       voucher yang dicentang; kalau kosong atau tidak dikirim sama sekali -
       mis. dari permintaan yang dirakit sendiri - dipakai tanggal dokumen,
       persis perilaku sebelum 5 Nov 2026. Dilewatkan appYmd() yang sama
       dgn tanggal kepala, jadi tanggal yang tidak terbaca ikut jatuh ke
       tanggal dokumen, bukan tersimpan jadi '0000-00-00'. */
    $plan_baris = appYmd(isset($b['plan_date']) ? $b['plan_date'] : '');
    if ($plan_baris === '') { $plan_baris = $plan_date; }
    $planb_esc = mysqli_real_escape_string($conn1, $plan_baris);

    $curr_esc = mysqli_real_escape_string($conn1, (string) $v['curr']);
    $amount   = (float) $v['total'];
    $ketb     = trim(isset($b['keterangan']) ? (string) $b['keterangan'] : '');
    $ketb_sql = ($ketb === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn1, $ketb) . "'";

    $ok = mysqli_query($conn1, "INSERT INTO ap_payment_plan_det
            (doc_number, no_kbon, plan_date, curr, amount, keterangan, status)
            VALUES ('$doc_esc', '$no_esc', '$planb_esc', '$curr_esc', $amount, $ketb_sql, 'Y')");
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

$pesan = 'Payment plan ' . $doc_number . ' saved with ' . $tersimpan
       . ' voucher' . ($tersimpan == 1 ? '' : 's') . '.';
if (!empty($digantikan)) {
    $n = count($digantikan);
    $pesan .= ' ' . $n . ' voucher' . ($n == 1 ? '' : 's')
            . ' rescheduled, replacing an earlier plan.';
}

echo json_encode(array(
    'status'     => 'success',
    'doc_number' => $doc_number,
    'tersimpan'  => $tersimpan,
    'ditolak'    => $ditolak,
    'digantikan' => $digantikan,
    'message'    => $pesan,
));
?>
