<?php
/* ============================================================================
   AP PAYMENT PROJECTION REPORT - SIMPAN HISTORY (potret).
   Dipanggil tombol Save History di ap_projection_report.php dgn saringan yang
   SAMA PERSIS seperti yang sedang tampil di layar.

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI - sama seperti ekspor Excel, berkas
   ini meng-include ajx_ap_projection_report.php dalam mode pustaka
   (APPR_PUSTAKA) dan menyimpan keluarannya apa adanya. Jadi yang tersimpan
   benar-benar yang dilihat pemakai waktu menekan Save.

   YANG DISIMPAN ADALAH POTRET, bukan rujukan. Kurs, rencana bayar PPU, dan
   master supplier semuanya bisa berubah besok; dokumen riwayat harus tetap
   memperlihatkan angka hari ini. Karena itu nilainya disalin, bukan dibaca
   ulang saat dokumen dibuka.

   Kolom tanggal TIDAK disimpan satu per satu (jumlahnya berubah menurut
   periode) - yang disimpan plan_date + outstanding, dan dari keduanya kisi
   tanggalnya bisa dibangun ulang persis. Lihat
   db_migrate_LIVE_20261106_ap_projection_history.sql.

   AWAS $user: conn.php menimpa variabel $user dengan "root". Nama pemakai
   dibaca ke $userLogin SEBELUM include apa pun, dan jangan dinamai $user.
   ============================================================================ */

session_start();
$userLogin = isset($_SESSION['username']) ? trim((string) $_SESSION['username']) : '';

define('APPR_PUSTAKA', 1);

$_POST['start_date'] = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$_POST['end_date']   = isset($_POST['end_date'])   ? $_POST['end_date']   : '';
$_POST['nama_supp']  = isset($_POST['nama_supp'])  ? $_POST['nama_supp']  : 'ALL';
$_POST['type']       = isset($_POST['type'])       ? $_POST['type']       : 'daily';

$pMulai = $_POST['start_date'];
$pAkhir = $_POST['end_date'];
$pSupp  = $_POST['nama_supp'];
$pType  = $_POST['type'];

ob_start();
include __DIR__ . '/ajx_ap_projection_report.php';   /* membawa $conn2 juga */
$jawaban = json_decode(trim((string) ob_get_clean()), true);

header('Content-Type: application/json');

function aprJawab($arr)
{
    echo json_encode($arr, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
/* 'd-m-Y' atau '-' -> 'Y-m-d' atau NULL (untuk kolom DATE) */
/**
 * Tanggal dari LAYAR (sudah berbentuk tampilan) ke bentuk basis data.
 *
 * Mengerti TIGA bentuk, dan urutannya penting:
 *   1. '01 Oct 2026'  - bentuk baru sejak 9 Nov 2026 (apprTampilTgl)
 *   2. '2026-10-01'   - bentuk basis data, kalau suatu saat dikirim mentah
 *   3. '01-10-2026'   - bentuk lama; potret yang dibuat sebelum 9 Nov 2026
 *                       dan penyaring tanggal di layar masih memakainya
 *
 * Kenapa tidak strtotime() saja untuk semuanya: '01-10-2026' dibaca PHP
 * sbg hari-bulan-tahun, TAPI '10/01/2026' sbg bulan-hari-tahun - jadi
 * bentuk bertanda hubung sengaja dirakit sendiri, bukan ditebak.
 *
 * Kalau bentuk tampilan di apprTampilTgl() diubah lagi, fungsi ini WAJIB
 * ikut diperiksa: bentuk yang tidak dikenali mengembalikan null, dan null
 * tersimpan sbg tanggal KOSONG di potret - diam-diam, tanpa pesan galat.
 */
function aprTglDb($v)
{
    $v = trim((string) $v);
    if ($v === '' || $v === '-') { return null; }

    /* 1. '01 Oct 2026' / '1 October 2026' */
    if (preg_match('/^\d{1,2}\s+[A-Za-z]{3,9}\s+\d{4}$/', $v)) {
        $t = strtotime($v);
        return $t ? date('Y-m-d', $t) : null;
    }

    /* 2. '2026-10-01' */
    if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $v)) {
        $t = strtotime($v);
        return $t ? date('Y-m-d', $t) : null;
    }

    /* 3. '01-10-2026' */
    $p = explode('-', $v);
    if (count($p) !== 3) { return null; }
    $t = strtotime($p[2] . '-' . $p[1] . '-' . $p[0]);
    return $t ? date('Y-m-d', $t) : null;
}
function aprNum($v)
{
    return (float) str_replace(',', '', (string) $v);
}

if ($userLogin === '') {
    aprJawab(array('status' => 'error', 'message' => 'Your session has expired. Please sign in again.'));
}
if (!is_array($jawaban)) {
    aprJawab(array('status' => 'error', 'message' => 'The projection could not be built.'));
}
if (isset($jawaban['error'])) {
    aprJawab(array('status' => 'error', 'message' => $jawaban['error']));
}

$baris = isset($jawaban['data']) ? $jawaban['data'] : array();
$info  = isset($jawaban['info']) ? $jawaban['info'] : array();
if (!$baris) {
    aprJawab(array('status' => 'error', 'message' => 'Nothing to save - this period has no outstanding payment voucher.'));
}

/* Tanggal periode dalam bentuk basis data. $mulai & $akhir sudah 'Y-m-d'
   hasil apprTgl() di berkas yang di-include. */
$dMulai = isset($mulai) ? $mulai : aprTglDb($pMulai);
$dAkhir = isset($akhir) ? $akhir : aprTglDb($pAkhir);
$dPer   = isset($perTanggal) ? $perTanggal : null;

$eSupp  = mysqli_real_escape_string($conn2, ($pSupp === '' ? 'ALL' : $pSupp));
$eType  = mysqli_real_escape_string($conn2, $pType);
$eUser  = mysqli_real_escape_string($conn2, $userLogin);
$eMulai = mysqli_real_escape_string($conn2, (string) $dMulai);
$eAkhir = mysqli_real_escape_string($conn2, (string) $dAkhir);
$ePer   = $dPer ? "'" . mysqli_real_escape_string($conn2, $dPer) . "'" : 'NULL';
$eKurs  = mysqli_real_escape_string($conn2, json_encode(isset($info['kurs']) ? $info['kurs'] : array()));
$totalIdr = aprNum(isset($info['total']) ? $info['total'] : 0);

/* Nomor dokumen AP-PPR/MMYY/NNNNN, nomornya dimulai ulang tiap bulan.
   MMYY diambil dari tanggal AWAL periode (bukan hari ini), supaya potret
   periode lama yang dibuat belakangan tetap bernomor bulan periodenya -
   sama dgn cara AR. */
$mmyy = date('my', strtotime($dMulai));
$awal = 'AP-PPR/' . $mmyy . '/';

mysqli_query($conn2, 'START TRANSACTION');

/* Nomornya dicari di tabel HIDUP *dan* di ARSIP pembatalan.
   Kalau hanya tabel hidup yang dilihat, nomor dokumen yang sudah dibatalkan
   jadi bebas lagi dan dipakai ulang dokumen berikutnya - lalu dua dokumen
   yang berbeda berkumpul di arsip dgn satu nomor, dan penjaga jumlah baris
   di batal_history_projection.php menolak membatalkan yang kedua (terlihat
   6 Nov 2026 waktu penjaga laporan realisasi dijalankan dua kali: "167 row(s)
   live vs 334 archived").

   Arsipnya diperiksa ADA lebih dulu: di basis data yang migrasi tabel
   _cancel-nya belum dijalankan, menyebut tabel yang tidak ada membuat
   SELURUH query gagal - dan pesan galatnya akan menuduh tabel history yang
   salah. */
$kolNomor = 'CAST(SUBSTRING(doc_number, ' . (strlen($awal) + 1) . ') AS UNSIGNED)';
$eAwal    = mysqli_real_escape_string($conn2, $awal);
$adaArsip = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_projection_history_h_cancel'");
$sumberNomor = "SELECT $kolNomor n FROM ap_projection_history_h
                 WHERE doc_number LIKE '$eAwal%'";
if ($adaArsip && mysqli_num_rows($adaArsip) > 0) {
    $sumberNomor .= " UNION ALL
                SELECT $kolNomor n FROM ap_projection_history_h_cancel
                 WHERE doc_number LIKE '$eAwal%'";
}
$qn = mysqli_query($conn2, "SELECT IFNULL(MAX(n), 0) + 1 nomor FROM ($sumberNomor) z");
if (!$qn) {
    mysqli_query($conn2, 'ROLLBACK');
    aprJawab(array('status' => 'error',
        'message' => 'History table not found. Run db_migrate_LIVE_20261106_ap_projection_history.sql first.'));
}
$nomor = (int) mysqli_fetch_assoc($qn)['nomor'];
$docNumber = $awal . str_pad((string) $nomor, 5, '0', STR_PAD_LEFT);
$eDoc = mysqli_real_escape_string($conn2, $docNumber);

$okH = mysqli_query($conn2, "INSERT INTO ap_projection_history_h
    (doc_number, periode_dari, periode_sampai, nama_supp, type, per_tanggal,
     kurs, jml_baris, total_idr, status, created_by)
    VALUES ('$eDoc', '$eMulai', '$eAkhir', '$eSupp', '$eType', $ePer,
            '$eKurs', " . count($baris) . ", $totalIdr, 'ACTIVE', '$eUser')");
if (!$okH) {
    mysqli_query($conn2, 'ROLLBACK');
    aprJawab(array('status' => 'error', 'message' => 'Failed to save the document header: ' . mysqli_error($conn2)));
}

/* Rinciannya ditulis BERKELOMPOK, bukan satu INSERT per baris: satu periode
   bisa ratusan baris, dan ratusan perjalanan ke basis data membuat tombol
   Save terasa menggantung. */
$nilai = array();
$urut  = 0;
foreach ($baris as $b) {
    $urut++;
    $tgl = function ($v) use ($conn2) {
        $d = aprTglDb($v);
        return $d === null ? 'NULL' : "'" . mysqli_real_escape_string($conn2, $d) . "'";
    };
    $teks = function ($v) use ($conn2) {
        return "'" . mysqli_real_escape_string($conn2,
            html_entity_decode((string) $v, ENT_QUOTES)) . "'";
    };
    $nilai[] = '(' . implode(',', array(
        "'$eDoc'", $urut,
        $teks(isset($b['supplier']) ? $b['supplier'] : ''),
        $teks(isset($b['no_kbon']) ? $b['no_kbon'] : ''),
        $tgl(isset($b['tgl_kbon']) ? $b['tgl_kbon'] : ''),
        $teks(isset($b['term']) ? $b['term'] : ''),
        $tgl(isset($b['duedate']) ? $b['duedate'] : ''),
        $tgl(isset($b['tgl_proyeksi']) ? $b['tgl_proyeksi'] : ''),
        $teks(isset($b['basis']) ? $b['basis'] : ''),
        $teks(isset($b['relasi']) ? $b['relasi'] : ''),
        $teks(isset($b['item_type']) ? $b['item_type'] : ''),
        $teks(isset($b['flexibility']) ? $b['flexibility'] : ''),
        $teks(isset($b['curr']) ? $b['curr'] : ''),
        aprNum(isset($b['invoice_amount']) ? $b['invoice_amount'] : 0),
        aprNum(isset($b['rate']) ? $b['rate'] : 1),
        aprNum(isset($b['tax_base']) ? $b['tax_base'] : 0),
        aprNum(isset($b['vat']) ? $b['vat'] : 0),
        aprNum(isset($b['total_invoice']) ? $b['total_invoice'] : 0),
        aprNum(isset($b['pph']) ? $b['pph'] : 0),
        aprNum(isset($b['payment_amount']) ? $b['payment_amount'] : 0),
        aprNum(isset($b['outstanding']) ? $b['outstanding'] : 0),
    )) . ')';
}

$kolomDet = '(doc_number, no_urut, supplier, no_kbon, tgl_kbon, term, duedate,'
          . ' plan_date, basis, relasi, item_type, flexibility, curr,'
          . ' invoice_amount, rate, tax_base, vat, total_invoice, pph,'
          . ' payment_amount, outstanding)';
foreach (array_chunk($nilai, 200) as $potong) {
    $okD = mysqli_query($conn2, "INSERT INTO ap_projection_history_det $kolomDet VALUES "
         . implode(',', $potong));
    if (!$okD) {
        mysqli_query($conn2, 'ROLLBACK');
        aprJawab(array('status' => 'error', 'message' => 'Failed to save the rows: ' . mysqli_error($conn2)));
    }
}

mysqli_query($conn2, 'COMMIT');

aprJawab(array(
    'status'     => 'success',
    'doc_number' => $docNumber,
    'baris'      => count($baris),
    'total'      => isset($info['total']) ? $info['total'] : '0.00',
));
