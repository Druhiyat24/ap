<?php
/* ============================================================================
   ISI satu potret Cash Flow Projection, disusun ulang jadi matriks seperti
   saat disimpan.

   ANGKANYA TIDAK DIHITUNG ULANG - seluruhnya dibaca dari ap_cf_history_det.
   Itu memang inti potret: harus tetap memperlihatkan angka saat Save
   ditekan, walau kurs, rencana bayar, atau saldo awalnya sudah berubah
   sejak itu.

   NILAI NOL TIDAK DISIMPAN (lihat simpan_cf_history.php), jadi matriksnya
   dibangun dari kisi kosong lalu diisi - bukan dari daftar baris apa adanya.
   Kisi kolomnya:
     mode tanggal -> dibangun ulang dari periode dokumennya, supaya hari yang
                     seluruh nilainya nol tetap punya kolom
     mode akun    -> dari kolom yang tersimpan, diurutkan dgn kolom_urut
   ============================================================================ */
/* MODE PUSTAKA: ekspor Excel (ekspor_cf_history.php) meng-include berkas
   ini sesudah menyetel $_POST['doc_number'], lalu memakai keluarannya apa
   adanya - jadi isi Excel mustahil beda dgn layar. Dalam mode itu session
   & header sudah diurus pemanggilnya, dan berkas ini TIDAK BOLEH exit
   (keluaran Excel-nya sudah telanjur mengalir). */
if (!defined('CFH_PUSTAKA')) { session_start(); header('Content-Type: application/json'); }
include '../../conn/conn.php';

function cfhSalah($pesan)
{
    echo json_encode(array('status' => 'error', 'message' => $pesan));
    if (defined('CFH_PUSTAKA')) { return true; }
    exit;
}

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    if (cfhSalah('Your session has ended. Please sign in again.')) { return; }
}

$doc = trim(isset($_POST['doc_number']) ? (string) $_POST['doc_number'] : '');
if ($doc === '') {
    if (cfhSalah('Document number is required.')) { return; }
}
$docEsc = mysqli_real_escape_string($conn2, $doc);

$h = mysqli_fetch_assoc(mysqli_query($conn2,
    "select * from ap_cf_history_h where doc_number = '$docEsc' limit 1"));
if (!$h) {
    if (cfhSalah('Document not found.')) { return; }
}

/* mode = tab mana yang terbuka duluan, BUKAN satu-satunya yang ada. */
$mode = (strtolower($h['type']) === 'daily') ? 'tanggal' : 'akun';

/* POTRET BARU menyimpan DUA tata letak sekaligus - blok rep_bank/loan_bank di
   samping report/loan, lihat simpan_cf_history.php. ADA TIDAKNYA blok itu yang
   membedakan potret baru dari potret lama, jadi tidak perlu kolom penanda
   baru. Potret lama cuma punya satu tata letak: bawaan type-nya. */
$r = mysqli_fetch_assoc(mysqli_query($conn2,
    "select count(*) n from ap_cf_history_det
      where doc_number = '$docEsc' and blok in ('rep_bank','loan_bank')"));
$punyaDua = ($r && (int) $r['n'] > 0);

/* ---- kisi kolom TANGGAL: dibangun ulang dari periode dokumennya, supaya
        hari yang seluruh nilainya nol tetap punya kolom ---- */
$kolomTgl = array(); $petaTgl = array();
$t = strtotime($h['periode_dari']);
$akhir = strtotime($h['periode_sampai']);
$i = 0;
while ($t <= $akhir) {
    $tgl = date('Y-m-d', $t);
    $kolomTgl[] = array('judul' => date('d-M', $t), 'hari' => date('D', $t),
                        'tgl' => $tgl,
                        'akhir_pekan' => (date('N', $t) >= 6) ? 1 : 0);
    $petaTgl[$tgl] = $i++;
    $t = strtotime('+1 day', $t);
}

/* ---- kisi kolom REKENING: dari kolom yang tersimpan, diurutkan kolom_urut ---- */
$saringBlok = $punyaDua ? " and blok in ('rep_bank','loan_bank')" : '';
$kolomAkun = array(); $petaAkun = array();
$q = mysqli_query($conn2, "select distinct kolom, kolom_urut from ap_cf_history_det
                            where doc_number = '$docEsc' and kolom is not null" . $saringBlok . "
                            order by kolom_urut asc");
$i = 0;
while ($q && ($r = mysqli_fetch_assoc($q))) {
    $kolomAkun[] = array('judul' => $r['kolom'], 'hari' => '', 'tgl' => '', 'akhir_pekan' => 0);
    $petaAkun[$r['kolom']] = $i++;
}

/* ---- baris, dipisah per tata letak ----
   Di potret lama, blok report/loan berisi tata letak BAWAAN type-nya - jadi
   masuk ke keranjang yang sesuai, bukan selalu ke keranjang tanggal. */
$keranjang = array('tgl_report' => array(), 'tgl_loan' => array(),
                   'akun_report' => array(), 'akun_loan' => array());
if ($punyaDua) {
    $petaBlok = array('report'    => array('tgl_report', 'tgl'),
                      'loan'      => array('tgl_loan', 'tgl'),
                      'rep_bank'  => array('akun_report', 'akun'),
                      'loan_bank' => array('akun_loan', 'akun'));
} else {
    $j = ($mode === 'akun') ? 'akun' : 'tgl';
    $petaBlok = array('report' => array($j . '_report', $j),
                      'loan'   => array($j . '_loan', $j));
}

$q = mysqli_query($conn2, "select blok, urut, tipe, no_urut, label, tanggal, kolom, nilai
                             from ap_cf_history_det where doc_number = '$docEsc'
                            order by blok desc, urut asc, id asc");
if (!$q) {
    if (cfhSalah(mysqli_error($conn2))) { return; }
}
while ($r = mysqli_fetch_assoc($q)) {
    $blokDb = (string) $r['blok'];
    /* nama blok yang tidak dikenal diperlakukan seperti 'report' - lebih baik
       terbaca di tempat yang masuk akal daripada hilang diam-diam. */
    if (!isset($petaBlok[$blokDb])) { $blokDb = 'report'; }
    list($wadah, $jenis) = $petaBlok[$blokDb];
    $nk = ($jenis === 'akun') ? count($kolomAkun) : count($kolomTgl);

    $u = (int) $r['urut'];
    if (!isset($keranjang[$wadah][$u])) {
        $keranjang[$wadah][$u] = array(
            'tipe'  => $r['tipe'],
            'no'    => (string) $r['no_urut'],
            'label' => $r['label'],
            'v'     => array_fill(0, $nk, null),
            'tot'   => null,
        );
    }
    $nilai = ($r['nilai'] === null) ? null : (float) $r['nilai'];

    if ($r['tanggal'] === null && $r['kolom'] === null) {
        $keranjang[$wadah][$u]['tot'] = $nilai;
        continue;
    }
    $penanda = ($jenis === 'akun') ? $r['kolom'] : $r['tanggal'];
    $peta    = ($jenis === 'akun') ? $petaAkun : $petaTgl;
    if ($penanda !== null && isset($peta[$penanda])) {
        $keranjang[$wadah][$u]['v'][$peta[$penanda]] = $nilai;
    }
}
$baris = $keranjang;

/* Baris judul blok memang bernilai NULL seluruhnya - dibiarkan NULL supaya
   tampil kosong, bukan nol. Baris lain yang nilainya tidak tersimpan memang
   nol, jadi diisi nol. */
foreach ($baris as $blok => $daftar) {
    foreach ($daftar as $u => $b) {
        if (in_array($b['tipe'], array('blok', 'grup'), true)) { continue; }
        foreach ($b['v'] as $i => $v) {
            if ($v === null) { $baris[$blok][$u]['v'][$i] = 0; }
        }
        if ($baris[$blok][$u]['tot'] === null) { $baris[$blok][$u]['tot'] = 0; }
    }
    ksort($baris[$blok]);
}

$barisTgl  = array_values($baris['tgl_report']);
$pinjTgl   = array_values($baris['tgl_loan']);
$barisAkun = array_values($baris['akun_report']);
$pinjAkun  = array_values($baris['akun_loan']);

/* 'kolom', 'baris' & 'pinjaman' = TAB BAWAAN, dibiarkan apa adanya untuk
   pemanggil lama - ekspor potret & Executive Summary membacanya. */
$kolom = ($mode === 'akun') ? $kolomAkun : $kolomTgl;

echo json_encode(array(
    'status'   => 'success',
    'mode'     => $mode,
    'kolom'    => $kolom,
    'baris'    => ($mode === 'akun') ? $barisAkun : $barisTgl,
    'pinjaman' => ($mode === 'akun') ? $pinjAkun  : $pinjTgl,
    'kolom_tgl'     => $kolomTgl,
    'kolom_akun'    => $kolomAkun,
    'baris_tgl'     => $barisTgl,
    'pinjaman_tgl'  => $pinjTgl,
    'baris_akun'    => $barisAkun,
    'pinjaman_akun' => $pinjAkun,
    /* Potret lama cuma punya satu tata letak - dua penanda ini yang dipakai
       layar untuk memutuskan tab mana yang pantas ditampilkan. */
    'ada_tgl'       => $barisTgl ? 1 : 0,
    'ada_akun'      => $barisAkun ? 1 : 0,
    'info'     => array(
        'doc_number' => $h['doc_number'],
        'periode'    => date('j M Y', strtotime($h['periode_dari'])) . ' - ' . date('j M Y', strtotime($h['periode_sampai'])),
        'pc'         => $h['profit_center'],
        'bank'       => $h['bank'],
        'type'       => ucfirst(strtolower($h['type'])),
        'rate'       => (float) $h['rate'],
        'status'     => $h['status'],
        'created_by' => $h['created_by'],
        'created'    => $h['created_date'] ? date('d-M-Y H:i', strtotime($h['created_date'])) : '',
        /* Nomor potret AR yang dipakai potret ini. DIBEKUKAN di kepala waktu
           Save History, jadi tetap menunjuk dokumen yang benar walau AR
           menyimpan potret baru sesudahnya. Kosong = tidak ada potret AR yang
           melingkupi periode ini, dan baris piutangnya memang nol. */
        'ar_doc'     => isset($h['ar_doc']) ? (string) $h['ar_doc'] : '',
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
