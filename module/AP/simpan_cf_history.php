<?php
/* ============================================================================
   CASH FLOW PROJECTION - SIMPAN POTRET (Save History).

   Angkanya DIHITUNG ULANG DI SERVER dgn saringan yang sama, bukan diambil
   dari kiriman layar: isi tabel di browser bisa disunting siapa saja, dan
   potret yang salah tidak akan pernah ketahuan lagi karena memang dibekukan.

   Koneksi datang dari mesin yang di-include (lihat catatan di kepala
   ajx_cash_flow_projection.php) - jangan include conn.php di sini.
   ============================================================================ */
session_start();

define('CFP_PUSTAKA', 1);
/* Minta mesin mengumpulkan RINCIAN SUMBER (AR, PV, book transfer, saldo,
   master kategori) sekalian - hanya di sini, layar tidak memerlukannya.
   Lihat bagian 5e di mesinnya dan cf_sumber_lib.php. */
define('CFP_SUMBER', 1);
$_POST['start_date'] = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$_POST['end_date']   = isset($_POST['end_date'])   ? $_POST['end_date']   : '';

ob_start();
include __DIR__ . '/ajx_cash_flow_projection.php';
$cfsJson = trim((string) ob_get_clean());

header('Content-Type: application/json');

$cfsUser = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($cfsUser === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$cfsHasil = json_decode($cfsJson, true);
if (!is_array($cfsHasil)) {
    echo json_encode(array('status' => 'error', 'message' => 'The projection could not be built.'));
    exit;
}
if (isset($cfsHasil['error'])) {
    echo json_encode(array('status' => 'error', 'message' => $cfsHasil['error']));
    exit;
}

$cfsMode  = isset($cfsHasil['mode']) ? $cfsHasil['mode'] : 'tanggal';
$cfsKolom = isset($cfsHasil['kolom']) ? $cfsHasil['kolom'] : array();
$cfsTgl   = $cfsHasil['tgl'];
$cfsInfo  = $cfsHasil['info'];
$cfsBaris = $cfsHasil['baris'];
$cfsPinj  = $cfsHasil['pinjaman'];
$cfsBank  = isset($cfsHasil['bank']) ? $cfsHasil['bank'] : array();

/* Weekly/Monthly tidak berkolom tanggal, jadi periodenya diambil dari
   saringan - bukan dari kolom pertama/terakhir seperti Daily. */
if ($cfsMode === 'akun') {
    $cfsDari   = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['start_date'])));
    $cfsSampai = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['end_date'])));
} else {
    $cfsDari   = $cfsTgl[0];
    $cfsSampai = $cfsTgl[count($cfsTgl) - 1];
}

/* ---- nomor dokumen: CF-PPR/MMYY/NNNNN, urutannya per BULAN ----
   GET_LOCK supaya dua orang yang menekan Save bersamaan tidak mendapat
   nomor yang sama - tanpa itu UNIQUE KEY-nya yang menolak, dan satu orang
   kehilangan simpanannya tanpa tahu sebabnya. */
$cfsAwalan = 'CF-PPR/' . date('my', strtotime($cfsDari)) . '/';
$cfsKunci  = 'ap_cf_history_gen';
mysqli_query($conn2, "SELECT GET_LOCK('$cfsKunci', 10)");

$cfsR = mysqli_fetch_assoc(mysqli_query($conn2,
    "select coalesce(max(cast(substring_index(doc_number,'/',-1) as unsigned)),0) n
       from ap_cf_history_h where doc_number like '" . mysqli_real_escape_string($conn2, $cfsAwalan) . "%'"));
$cfsNo  = (int) $cfsR['n'] + 1;
$cfsDoc = $cfsAwalan . str_pad($cfsNo, 5, '0', STR_PAD_LEFT);

function cfsEsc($conn, $v) { return mysqli_real_escape_string($conn, (string) $v); }

mysqli_query($conn2, 'START TRANSACTION');

$cfsGagal = '';
$cfsSql = "INSERT INTO ap_cf_history_h
    (doc_number, periode_dari, periode_sampai, profit_center, bank, type, rate, ar_doc,
     jml_baris, status, created_by, created_date)
    VALUES ('" . cfsEsc($conn2, $cfsDoc) . "', '$cfsDari', '$cfsSampai',
            '" . cfsEsc($conn2, $cfsInfo['pc']) . "', '" . cfsEsc($conn2, $cfsInfo['bank']) . "',
            '" . cfsEsc($conn2, $cfsInfo['type']) . "', " . (float) $cfsInfo['rate'] . ",
            " . ($cfsInfo['ar_doc'] !== '' ? "'" . cfsEsc($conn2, $cfsInfo['ar_doc']) . "'" : 'NULL') . ",
            0, 'ACTIVE', '" . cfsEsc($conn2, $cfsUser) . "', NOW())";
if (!mysqli_query($conn2, $cfsSql)) { $cfsGagal = mysqli_error($conn2); }

/* ---- rincian: satu baris per (baris laporan x tanggal) ----
   Nilai NOL dilewati; baris TOTAL selalu ditulis supaya susunan barisnya
   tetap utuh walau seluruh harinya nol. Dikirim per 200 baris, bukan satu
   per satu: 103 baris x 31 kolom terlalu banyak untuk sekali jalan. */
$cfsJml = 0;
$cfsAntri = array();
$cfsKirim = function () use (&$cfsAntri, $conn2, &$cfsGagal) {
    if (!$cfsAntri || $cfsGagal !== '') { $cfsAntri = array(); return; }
    $sql = "INSERT INTO ap_cf_history_det (doc_number, blok, urut, tipe, no_urut, label, tanggal, kolom, kolom_urut, nilai) VALUES "
         . implode(',', $cfsAntri);
    if (!mysqli_query($conn2, $sql)) { $cfsGagal = mysqli_error($conn2); }
    $cfsAntri = array();
};

foreach (array('report' => $cfsBaris, 'loan' => $cfsPinj) as $cfsBlok => $cfsDaftar) {
    foreach ($cfsDaftar as $cfsUrut => $cfsB) {
        $dep = "'" . cfsEsc($conn2, $cfsDoc) . "','" . $cfsBlok . "'," . (int) $cfsUrut . ",'"
             . cfsEsc($conn2, $cfsB['tipe']) . "','" . cfsEsc($conn2, $cfsB['no']) . "','"
             . cfsEsc($conn2, $cfsB['label']) . "'";

        /* Penanda kolom:
             TOTAL          -> tanggal NULL, kolom NULL
             Daily          -> tanggal diisi, kolom NULL
             Weekly/Monthly -> tanggal NULL, kolom diisi kunci rekening
           Tiga keadaan itu tidak bisa tertukar satu sama lain. */
        $tot = ($cfsB['tot'] === null) ? 'NULL' : (float) $cfsB['tot'];
        $cfsAntri[] = '(' . $dep . ',NULL,NULL,NULL,' . $tot . ')';
        $cfsJml++;

        /* kolom_urut = urutan kolom saat disimpan. Tanpa itu, potret mode akun
           tidak bisa disusun ulang urut kolomnya waktu dibaca lagi - nama
           rekening saja tidak memberi urutan. */
        if ($cfsMode === 'akun') {
            foreach ($cfsKolom as $i => $k) {
                $v = isset($cfsB['v'][$i]) ? $cfsB['v'][$i] : null;
                if ($v === null || round((float) $v) == 0) { continue; }
                /* yang disimpan NAMA rekening, bukan kuncinya: ini potret, dan
                   harus tetap terbaca walau master rekeningnya berubah. */
                $cfsAntri[] = '(' . $dep . ",NULL,'" . cfsEsc($conn2, $k['label']) . "'," . (int) $i . ',' . (float) $v . ')';
                $cfsJml++;
            }
        } else {
            foreach ($cfsTgl as $i => $t) {
                $v = isset($cfsB['v'][$i]) ? $cfsB['v'][$i] : null;
                if ($v === null || round((float) $v) == 0) { continue; }
                $cfsAntri[] = '(' . $dep . ",'" . $t . "',NULL," . (int) $i . ',' . (float) $v . ')';
                $cfsJml++;
            }
        }
        if (count($cfsAntri) >= 200) { $cfsKirim(); }
    }
}
$cfsKirim();

/* ---- bekukan setelan rekening ke nomor dokumen ini ---- */
if ($cfsGagal === '') {
    foreach ($cfsBank as $cfsA) {
        /* manual_by/manual_date = siapa MENGETIK angka tangannya dan kapan,
           bukan siapa menekan Save. Tanpa dua kolom ini, dokumen beku tidak
           bisa menjawab "saldo awal ini dari mana". Kosong = murni hitungan
           sistem. */
        $mBy   = isset($cfsA['manual_by'])   ? trim((string) $cfsA['manual_by'])   : '';
        $mDate = isset($cfsA['manual_date']) ? trim((string) $cfsA['manual_date']) : '';
        $sql = "INSERT INTO ap_cf_bank_setting
                (doc_number, akun, curr, saldo_tarik, saldo_pakai, manual_by, manual_date,
                 min_balance, limit_amount, rate, update_by, update_date)
                VALUES ('" . cfsEsc($conn2, $cfsDoc) . "','" . cfsEsc($conn2, $cfsA['label']) . "','"
              . cfsEsc($conn2, $cfsA['curr']) . "'," . (float) $cfsA['saldo_tarik'] . ","
              . (float) $cfsA['saldo_pakai'] . ","
              . ($mBy   !== '' ? "'" . cfsEsc($conn2, $mBy)   . "'" : 'NULL') . ","
              . ($mDate !== '' ? "'" . cfsEsc($conn2, $mDate) . "'" : 'NULL') . ","
              . (float) $cfsA['min_balance'] . ","
              . (float) $cfsA['limit'] . "," . (float) $cfsInfo['rate'] . ",'"
              . cfsEsc($conn2, $cfsUser) . "', NOW())";
        if (!mysqli_query($conn2, $sql)) { $cfsGagal = mysqli_error($conn2); break; }
    }
}

/* ---- bekukan RINCIAN SUMBERNYA (lembar pendukung di Excel) ----
   $cfpSumber datang dari mesin yang di-include di atas; berkas ini berbagi
   lingkup dengannya, jadi tidak perlu lewat JSON. Dikumpulkan SEKARANG, di
   transaksi yang sama dgn angkanya - kalau dibangun ulang waktu Excel
   diunduh, rinciannya bisa sudah tidak cocok lagi dgn angka yang dibekukan. */
if ($cfsGagal === '' && !empty($cfpSumber)) {
    $cfsSrc = array();
    foreach ($cfpSumber as $cfsJenis => $cfsDaftarSrc) {
        foreach ($cfsDaftarSrc as $cfsI => $cfsRow) {
            /* json_encode bawaan menulis karakter non-ASCII sbg \uXXXX, jadi
               isinya selalu ASCII dan aman di kolom latin1. */
            $cfsSrc[] = "('" . cfsEsc($conn2, $cfsDoc) . "','" . cfsEsc($conn2, $cfsJenis) . "',"
                      . (int) $cfsI . ",'" . cfsEsc($conn2, json_encode($cfsRow)) . "')";
            if (count($cfsSrc) >= 200) {
                if (!mysqli_query($conn2, "INSERT INTO ap_cf_history_src (doc_number, jenis, urut, data) VALUES "
                                        . implode(',', $cfsSrc))) { $cfsGagal = mysqli_error($conn2); break 2; }
                $cfsSrc = array();
            }
        }
    }
    if ($cfsGagal === '' && $cfsSrc) {
        if (!mysqli_query($conn2, "INSERT INTO ap_cf_history_src (doc_number, jenis, urut, data) VALUES "
                                . implode(',', $cfsSrc))) { $cfsGagal = mysqli_error($conn2); }
    }
}

if ($cfsGagal === '') {
    mysqli_query($conn2, "UPDATE ap_cf_history_h SET jml_baris = " . (int) $cfsJml
                       . " WHERE doc_number = '" . cfsEsc($conn2, $cfsDoc) . "'");
}

if ($cfsGagal !== '') {
    mysqli_query($conn2, 'ROLLBACK');
    mysqli_query($conn2, "SELECT RELEASE_LOCK('$cfsKunci')");
    echo json_encode(array('status' => 'error', 'message' => $cfsGagal));
    exit;
}

mysqli_query($conn2, 'COMMIT');
mysqli_query($conn2, "SELECT RELEASE_LOCK('$cfsKunci')");

echo json_encode(array('status' => 'success', 'doc_number' => $cfsDoc, 'baris' => $cfsJml));
