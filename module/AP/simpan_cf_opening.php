<?php
/* ============================================================================
   CASH FLOW PROJECTION - MENYIMPAN SALDO AWAL yang diketik tangan.

   Satu tanggal, banyak rekening, satu transaksi.

   DUA HAL YANG SENGAJA DIKERASKAN DI SINI:

   1. ANGKA SISTEMNYA DIHITUNG ULANG DI SERVER lewat mesin laporan, tidak
      diambil dari kiriman layar. Yang disimpan di kolom saldo_sistem adalah
      pembanding - "waktu angka ini ditulis, sistem bilang sekian" - jadi
      nilainya harus datang dari sistem, bukan dari sesuatu yang bisa disunting
      di browser.

   2. SETIAP PERUBAHAN DICATAT di ap_cf_opening_log dan TIDAK PERNAH ditimpa.
      Tabel ap_cf_opening cuma menyimpan keadaan TERAKHIR; tanpa log, "dulu
      angkanya berapa dan siapa yang mengubah" tidak bisa dijawab.

   Baris yang isinya TIDAK BERUBAH dilewati - tidak ditulis, tidak dicatat -
   supaya riwayatnya berisi perubahan betulan, bukan jejak setiap kali tombol
   Save ditekan.

   3. DUA PEMAKAI TIDAK BOLEH SALING MENIMPA. Saldo awal ini milik BERSAMA -
      satu angka per (tanggal x rekening), bukan per orang - jadi dua orang
      yang membukanya bersamaan bisa saling menghapus pekerjaan tanpa sadar.
      Yang paling berbahaya bukan dua-duanya mengetik, melainkan: A membuka
      modal, B menyimpan 76.000.000, lalu A menekan Save TANPA MENGUBAH
      APA PUN - kotak A masih kosong, dan kosong berarti "kembalikan ke
      hitungan sistem", sehingga angka B terhapus diam-diam.

      Penjagaannya: layar mengirim balik nilai yang DIA LIHAT waktu modal
      dibuka (<code>semula</code>). Kalau yang tersimpan sekarang sudah
      berbeda, berarti ada orang lain yang mengubahnya - SELURUH simpanan
      ditolak (bukan sebagian), disebutkan rekening mana dan oleh siapa,
      lalu modalnya dimuat ulang. Ditolak seluruhnya supaya tidak ada
      keadaan setengah tersimpan yang lebih sulit dibereskan.

      Selain itu penulisannya dipagari GET_LOCK per TANGGAL, jadi dua orang
      yang menekan Save pada detik yang sama tetap berbaris satu per satu -
      tanpa itu keduanya bisa lolos pemeriksaan di atas sebelum salah satu
      sempat menulis.

   Koneksi datang dari mesin yang di-include - JANGAN include conn.php.
   ============================================================================ */
session_start();

define('CFP_PUSTAKA', 1);
$cfsTgl   = isset($_POST['tanggal']) ? trim((string) $_POST['tanggal']) : '';
$cfsBaris = json_decode(isset($_POST['baris']) ? $_POST['baris'] : '[]', true);

/* periode penuh dipakai supaya kursnya SAMA dgn kurs laporan - lihat
   catatan di ajx_cf_opening.php */
$_POST['start_date'] = $cfsTgl;
$_POST['end_date']   = isset($_POST['end_date']) && trim((string) $_POST['end_date']) !== ''
                     ? $_POST['end_date'] : $cfsTgl;
$_POST['pc']         = 'ALL';
$_POST['bank']       = 'ALL';
$_POST['type']       = 'daily';

ob_start();
include __DIR__ . '/ajx_cash_flow_projection.php';
$cfsJson = trim((string) ob_get_clean());

header('Content-Type: application/json');

$cfsUser = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($cfsUser === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}
if (!is_array($cfsBaris) || count($cfsBaris) === 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Nothing to save.'));
    exit;
}

$cfsHasil = json_decode($cfsJson, true);
if (!is_array($cfsHasil) || isset($cfsHasil['error'])) {
    $pesan = (is_array($cfsHasil) && isset($cfsHasil['error']))
           ? $cfsHasil['error'] : 'The opening balance could not be rebuilt.';
    echo json_encode(array('status' => 'error', 'message' => $pesan));
    exit;
}

$cfsTanggal = $cfsHasil['tgl'][0];
$tglEsc = mysqli_real_escape_string($conn2, $cfsTanggal);

/* rekening sah + hitungan sistemnya, dari mesin */
/* SEMUANYA RUPIAH: yang diketik pemakai, yang disimpan, dan yang dicatat
   di log. Rekening USD TIDAK dibagi kurs di sini - pembagiannya dikerjakan
   mesin laporan saat memakai. Sempat dicoba sebaliknya dan hasilnya
   melenceng: DECIMAL(20,2) memotong angka USD, lalu dikalikan kurs lagi
   sehingga angka yang dibaca kembali beda puluhan rupiah dari yang diketik. */
$cfsRate = (float) $cfsHasil['info']['rate'];
$cfsSah = array();
foreach ($cfsHasil['bank'] as $b) {
    if (!empty($b['loan'])) { continue; }
    $kurs = (strtoupper($b['curr']) === 'USD' && $cfsRate > 0) ? $cfsRate : 1;
    $cfsSah[$b['kunci']] = array('label' => $b['label'], 'curr' => $b['curr'],
                                 'sistem' => (float) $b['saldo_tarik'] * $kurs);   /* rupiah */
}

function cfsE($conn, $v) { return mysqli_real_escape_string($conn, (string) $v); }

/* Kunci per TANGGAL: dua orang yang menyimpan tanggal yang sama berbaris,
   yang menyimpan tanggal berbeda tidak saling menunggu. */
$cfsKunciNama = 'ap_cf_opening|' . $cfsTanggal;
$cfsKunciEsc  = cfsE($conn2, $cfsKunciNama);
$cfsR = mysqli_fetch_row(mysqli_query($conn2, "SELECT GET_LOCK('$cfsKunciEsc', 10)"));
if (!$cfsR || (int) $cfsR[0] !== 1) {
    echo json_encode(array('status' => 'error',
        'message' => 'Someone else is saving this date right now. Please try again in a moment.'));
    exit;
}

mysqli_query($conn2, 'START TRANSACTION');

/* Keadaan TERKINI dibaca DI DALAM kunci - kalau dibaca sebelum mengunci,
   yang dibandingkan bisa sudah basi sebelum sempat dipakai. */
$cfsLama = array();
$q = mysqli_query($conn2, "select akun, saldo_manual, keterangan, update_by
                             from ap_cf_opening where tanggal = '$tglEsc'");
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $cfsLama[cfpKunci($r['akun'])] = array('nilai' => (float) $r['saldo_manual'],
                                               'ket'   => (string) $r['keterangan'],
                                               'oleh'  => (string) $r['update_by']);
    }
}

/* ---- PEMERIKSAAN BENTROK, sebelum satu baris pun ditulis ---- */
$cfsBentrok = array();
foreach ($cfsBaris as $b) {
    $kunci = isset($b['akun']) ? cfpKunci($b['akun']) : '';
    if ($kunci === '' || !isset($cfsSah[$kunci])) { continue; }

    $dilihat = isset($b['semula']) ? trim((string) $b['semula']) : '';
    $dilihat = str_replace(array(',', ' '), '', $dilihat);
    $adaSkrg = isset($cfsLama[$kunci]);
    $skrg    = $adaSkrg ? $cfsLama[$kunci]['nilai'] : null;

    $sama = ($dilihat === '' && !$adaSkrg)
         || ($dilihat !== '' && $adaSkrg && is_numeric($dilihat) && abs((float) $dilihat - $skrg) < 0.005);
    if (!$sama) {
        $cfsBentrok[] = array(
            'akun'  => $cfsSah[$kunci]['label'],
            'oleh'  => $adaSkrg ? $cfsLama[$kunci]['oleh'] : '',
            'nilai' => $adaSkrg ? $skrg : null,
        );
    }
}
if ($cfsBentrok) {
    mysqli_query($conn2, 'ROLLBACK');
    mysqli_query($conn2, "SELECT RELEASE_LOCK('$cfsKunciEsc')");
    echo json_encode(array('status' => 'conflict', 'bentrok' => $cfsBentrok,
        'message' => 'Someone else changed the opening balance while this window was open. Nothing was saved.'),
        JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
$gagal = '';
$jumSet = 0; $jumReset = 0; $jumLewat = 0; $ditolak = array();

foreach ($cfsBaris as $b) {
    if ($gagal !== '') { break; }

    $kunci = isset($b['akun']) ? cfpKunci($b['akun']) : '';
    if ($kunci === '' || !isset($cfsSah[$kunci])) { $ditolak[] = isset($b['akun']) ? $b['akun'] : '?'; continue; }

    $ket = isset($b['keterangan']) ? trim((string) $b['keterangan']) : '';
    $mentah = isset($b['manual']) ? trim((string) $b['manual']) : '';
    $mentah = str_replace(array(',', ' '), '', $mentah);

    $adaLama  = isset($cfsLama[$kunci]);
    $nilaiLama = $adaLama ? $cfsLama[$kunci]['nilai'] : null;
    $ketLama   = $adaLama ? $cfsLama[$kunci]['ket']   : '';
    $sistem    = $cfsSah[$kunci]['sistem'];        /* rupiah */
    $akunEsc   = cfsE($conn2, $cfsSah[$kunci]['label']);

    /* ---- kosong = kembalikan ke hitungan sistem ---- */
    if ($mentah === '') {
        if (!$adaLama) { $jumLewat++; continue; }
        if (!mysqli_query($conn2, "delete from ap_cf_opening
                                    where tanggal = '$tglEsc' and akun = '$akunEsc'")) {
            $gagal = mysqli_error($conn2); break;
        }
        /* RESET: sesudahnya yang berlaku adalah hitungan sistem, jadi ITU
           yang dicatat sbg nilai_baru - bukan dikosongkan. Riwayat yang
           menulis "system" memaksa pembacanya menebak angkanya berapa. */
        if (!mysqli_query($conn2, "insert into ap_cf_opening_log
                (tanggal, akun, aksi, nilai_lama, nilai_baru, saldo_sistem, keterangan, user, waktu)
                values ('$tglEsc', '$akunEsc', 'RESET', " . (float) $nilaiLama . ", " . (float) $sistem . ",
                        " . (float) $sistem . ", '" . cfsE($conn2, $ket) . "',
                        '" . cfsE($conn2, $cfsUser) . "', NOW())")) {
            $gagal = mysqli_error($conn2); break;
        }
        $jumReset++;
        continue;
    }

    if (!is_numeric($mentah)) { $ditolak[] = $cfsSah[$kunci]['label']; continue; }
    $nilaiBaru = (float) $mentah;          /* rupiah, disimpan apa adanya */

    /* tidak berubah -> tidak ditulis, tidak dicatat */
    if ($adaLama && abs($nilaiLama - $nilaiBaru) < 0.005 && $ketLama === $ket) {
        $jumLewat++;
        continue;
    }

    $sql = "insert into ap_cf_opening
              (tanggal, akun, curr, saldo_sistem, saldo_manual, keterangan, update_by, update_date)
            values ('$tglEsc', '$akunEsc', '" . cfsE($conn2, $cfsSah[$kunci]['curr']) . "',
                    " . (float) $sistem . ", " . $nilaiBaru . ",
                    '" . cfsE($conn2, $ket) . "', '" . cfsE($conn2, $cfsUser) . "', NOW())
            on duplicate key update
              curr = values(curr), saldo_sistem = values(saldo_sistem),
              saldo_manual = values(saldo_manual), keterangan = values(keterangan),
              update_by = values(update_by), update_date = values(update_date)";
    if (!mysqli_query($conn2, $sql)) { $gagal = mysqli_error($conn2); break; }

    /* nilai_lama = angka yang BERLAKU sebelum perubahan: isian tangan
       sebelumnya kalau ada, kalau belum pernah ya hitungan sistem. Dulu
       dikosongkan dan ditampilkan sbg "system"; pemakai memintanya diisi
       angkanya langsung (17 Nov 2026) - dan memang lebih betul, karena
       hitungan sistem hari itu bisa saja sudah berubah waktu riwayatnya
       dibaca lagi. */
    if (!mysqli_query($conn2, "insert into ap_cf_opening_log
            (tanggal, akun, aksi, nilai_lama, nilai_baru, saldo_sistem, keterangan, user, waktu)
            values ('$tglEsc', '$akunEsc', 'SET',
                    " . ($adaLama ? (float) $nilaiLama : (float) $sistem) . ", " . $nilaiBaru . ",
                    " . (float) $sistem . ", '" . cfsE($conn2, $ket) . "',
                    '" . cfsE($conn2, $cfsUser) . "', NOW())")) {
        $gagal = mysqli_error($conn2); break;
    }
    $jumSet++;
}

if ($gagal !== '') {
    mysqli_query($conn2, 'ROLLBACK');
    mysqli_query($conn2, "SELECT RELEASE_LOCK('$cfsKunciEsc')");
    echo json_encode(array('status' => 'error', 'message' => $gagal));
    exit;
}
mysqli_query($conn2, 'COMMIT');
mysqli_query($conn2, "SELECT RELEASE_LOCK('$cfsKunciEsc')");

echo json_encode(array(
    'status'   => 'success',
    'tanggal'  => date('d-M-Y', strtotime($cfsTanggal)),
    'diubah'   => $jumSet,
    'direset'  => $jumReset,
    'dilewati' => $jumLewat,
    'ditolak'  => $ditolak,
), JSON_INVALID_UTF8_SUBSTITUTE);
