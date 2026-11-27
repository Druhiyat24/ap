<?php
/* ============================================================================
   CASH FLOW PROJECTION - MEMBACA SALDO AWAL yang bisa diketik tangan.

   Dipanggil tombol "Opening Balance" di cash_flow_projection.php. Jawabannya
   dipakai mengisi modal: hitungan sistem, angka tangan (kalau ada), dan
   riwayat perubahannya.

   ANGKA SISTEMNYA DIAMBIL DARI MESIN LAPORAN ITU SENDIRI (mode pustaka),
   bukan dihitung ulang di sini - supaya angka di modal mustahil berbeda dgn
   angka di baris OPENING CASH & BANK BALANCE.

   Koneksi datang dari mesin yang di-include (lihat catatan di kepala
   ajx_cash_flow_projection.php) - JANGAN include conn.php di sini, berkas itu
   tidak berpagar function_exists.
   ============================================================================ */
session_start();

define('CFP_PUSTAKA', 1);
$cfoTgl  = isset($_POST['start_date']) ? trim((string) $_POST['start_date']) : '';
/* Saringan Bank dibaca SEKARANG, sebelum mesin di-include: mesin menimpa
   seluruh $_POST waktu memanggil mesin proyeksi AP. */
$cfoBank = trim(isset($_POST['bank']) ? (string) $_POST['bank'] : 'ALL');

/* end_date DIPAKAI APA ADANYA, tidak dipendekkan jadi satu hari. Kurs
   laporan diambil dari kurs PAJAK terakhir yang <= tanggal To, jadi
   memendekkan periodenya membuat modal memakai kurs yang BERBEDA dari
   laporannya - dan rekening USD lalu tampil beda puluhan juta rupiah
   antara modal dan baris OPENING. */
$_POST['start_date'] = $cfoTgl;
$_POST['end_date']   = isset($_POST['end_date']) && trim((string) $_POST['end_date']) !== ''
                     ? $_POST['end_date'] : $cfoTgl;
$_POST['pc']         = isset($_POST['pc'])   ? $_POST['pc']   : 'ALL';
$_POST['bank']       = isset($_POST['bank']) ? $_POST['bank'] : 'ALL';
$_POST['type']       = 'daily';

ob_start();
include __DIR__ . '/ajx_cash_flow_projection.php';
$cfoJson = trim((string) ob_get_clean());

header('Content-Type: application/json');

$cfoUser = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($cfoUser === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$cfoHasil = json_decode($cfoJson, true);
if (!is_array($cfoHasil) || isset($cfoHasil['error'])) {
    $pesan = (is_array($cfoHasil) && isset($cfoHasil['error']))
           ? $cfoHasil['error'] : 'The opening balance could not be built.';
    echo json_encode(array('status' => 'error', 'message' => $pesan));
    exit;
}

$cfoTanggal = $cfoHasil['tgl'][0];          /* sudah Y-m-d, hasil olahan mesin */
$cfoEsc     = mysqli_real_escape_string($conn2, $cfoTanggal);

/* ---------------------------------------------------------------------------
   Daftar rekening. HANYA rekening kas & bank - rekening berpenanda pinjaman
   tidak ikut, saldo awalnya ada di blok pinjaman dan bukan bagian dari baris
   OPENING CASH & BANK BALANCE.

   IKUT SARINGAN BANK DI LAYAR, bukan cuma Profit Center. Kalau pemakai sudah
   memilih satu rekening, yang disetel di sini juga satu rekening itu saja -
   kalau semuanya tetap tampil, saldo awal rekening LAIN bisa ikut tersimpan
   tanpa disadari, padahal laporannya cuma memakai yang dipilih.
   Saringan PC sudah dikerjakan mesin lewat bank[]; saringan rekening
   dikerjakan di sini karena bank[] sengaja memuat seluruh rekening PC itu
   (dipakai juga untuk mengisi dropdown).
   --------------------------------------------------------------------------- */
/* LARIK kunci rekening, kosong = semua - saringan Bank di layar bisa
   dicentang beberapa sejak 19 Nov 2026. */
$cfoKunciBank = cfpDaftarKunciBank($cfoBank);
/* SELURUH ANGKA DI MODAL INI DALAM RUPIAH (permintaan pemakai 17 Nov 2026).
   Di basis data saldonya disimpan dalam MATA UANG REKENINGNYA sendiri -
   itu yang dipakai mesin dan yang dibekukan ke dokumen history - jadi
   rekening USD dirupiahkan di sini saja, dgn kurs laporan, dan diubah
   kembali waktu disimpan. Tanpa itu, saldo USD berdampingan dgn saldo IDR
   di satu kolom dan jumlahnya tidak berarti apa-apa. */
$cfoAkun = array();
$cfoJumSistem = 0;
$cfoJumPakai  = 0;
$cfoRate = (float) $cfoHasil['info']['rate'];
/* REKENING PINJAMAN IKUT TAMPIL (permintaan pemakai 19 Nov 2026 - BCA
   008-997-1979 & 008-998-1982). Dulu dibuang dari daftar ini karena saldo
   awalnya bukan bagian dari baris OPENING CASH & BANK BALANCE melainkan
   milik blok pinjaman; akibatnya pemakai tidak punya satu pun tempat untuk
   melihat, apalagi menyetel, saldo awal kedua rekening itu.

   Rekening pinjaman hanya menyumbang bagian PLUS-nya ke total (27 Nov 2026);
   bagian minus adalah saldo awal pinjaman. Aturan yang sama persis dipakai
   cfpAwalKas() di mesin laporan, jadi TOTAL IN IDR di modal ini dan baris
   OPENING di laporan tidak bisa berbeda. */
foreach ($cfoHasil['bank'] as $b) {
    if ($cfoKunciBank && !in_array((string) $b['kunci'], $cfoKunciBank, true)) { continue; }
    $pinjaman = !empty($b['loan']);
    $kurs = (strtoupper($b['curr']) === 'USD') ? $cfoRate : 1;
    $cfoAkun[] = array(
        'kunci'  => $b['kunci'],
        'label'  => $b['label'],
        'curr'   => $b['curr'],
        'pc'     => $b['pc'],
        'loan'   => $pinjaman ? 1 : 0,
        'sistem' => (float) $b['saldo_tarik'] * $kurs,
        /* manual sudah RUPIAH apa adanya - tidak dikalikan lagi */
        'manual' => (isset($b['manual']) && $b['manual'] !== null) ? (float) $b['manual'] : null,
    );
    $sistem = (float) $b['saldo_tarik'] * $kurs;
    $pakai  = (float) $b['saldo_pakai'] * $kurs;
    if ($pinjaman) { $sistem = max(0, $sistem); $pakai = max(0, $pakai); }
    $cfoJumSistem += $sistem;
    $cfoJumPakai  += $pakai;
}

/* Keterangan + penulis terakhir - tidak ikut lewat mesin. */
$cfoKet = array();
$q = mysqli_query($conn2, "select akun, keterangan, update_by, update_date from ap_cf_opening where tanggal = '$cfoEsc'");
if ($q) { while ($r = mysqli_fetch_assoc($q)) { $cfoKet[cfpKunci($r['akun'])] = $r; } }
foreach ($cfoAkun as $i => $a) {
    $r = isset($cfoKet[$a['kunci']]) ? $cfoKet[$a['kunci']] : null;
    $cfoAkun[$i]['keterangan'] = $r ? (string) $r['keterangan'] : '';
    $cfoAkun[$i]['oleh']       = $r ? (string) $r['update_by'] : '';
}

/* ---------------------------------------------------------------------------
   Riwayat. Dibaca untuk TANGGAL ini saja - riwayat tanggal lain tidak ada
   hubungannya dgn angka yang sedang dilihat.
   --------------------------------------------------------------------------- */
$cfoLog = array();
$q = mysqli_query($conn2, "select akun, aksi, nilai_lama, nilai_baru, saldo_sistem,
                                  keterangan, user, waktu
                             from ap_cf_opening_log
                            where tanggal = '$cfoEsc'
                            order by id desc limit 200");
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $cfoLog[] = array(
            'akun'       => $r['akun'],
            'aksi'       => $r['aksi'],
            /* log memang DISIMPAN dalam rupiah - lihat simpan_cf_opening.php */
            'nilai_lama' => $r['nilai_lama'] === null ? null : (float) $r['nilai_lama'],
            'nilai_baru' => $r['nilai_baru'] === null ? null : (float) $r['nilai_baru'],
            'sistem'     => $r['saldo_sistem'] === null ? null : (float) $r['saldo_sistem'],
            'keterangan' => (string) $r['keterangan'],
            'user'       => (string) $r['user'],
            'waktu'      => $r['waktu'] ? date('d-M-Y H:i', strtotime($r['waktu'])) : '',
        );
    }
}

/* berapa rekening yang saldo awalnya SUDAH pernah ditetapkan untuk tanggal
   ini - dipakai layar untuk memutuskan apakah modalnya perlu muncul sendiri */
$cfoAdaManual = 0;
foreach ($cfoAkun as $a) { if ($a['manual'] !== null) { $cfoAdaManual++; } }

$cfoPesan = '';
if (!$cfoAkun) { $cfoPesan = 'No cash or bank account matches the current filter.'; }

echo json_encode(array(
    'status'      => 'success',
    'pesan'       => $cfoPesan,
    'tanggal'     => date('d-M-Y', strtotime($cfoTanggal)),
    'tgl_db'      => $cfoTanggal,
    'tgl_sebelum' => date('d-M-Y', strtotime($cfoTanggal . ' -1 day')),
    'ada_manual'  => $cfoAdaManual,
    'rate'    => $cfoHasil['info']['rate'],
    'akun'    => $cfoAkun,
    'jumlah'  => array('sistem' => $cfoJumSistem, 'pakai' => $cfoJumPakai),
    'log'     => $cfoLog,
), JSON_INVALID_UTF8_SUBSTITUTE);
