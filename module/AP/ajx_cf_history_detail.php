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

$mode = (strtolower($h['type']) === 'daily') ? 'tanggal' : 'akun';

/* ---- kisi kolom ---- */
$kolom = array();      /* judul kolom, urut */
$petaKolom = array();  /* penanda baris -> indeks kolom */

if ($mode === 'tanggal') {
    $t = strtotime($h['periode_dari']);
    $akhir = strtotime($h['periode_sampai']);
    $i = 0;
    while ($t <= $akhir) {
        $tgl = date('Y-m-d', $t);
        $kolom[] = array('judul' => date('d-M', $t), 'hari' => date('D', $t),
                         'tgl' => $tgl,
                         'akhir_pekan' => (date('N', $t) >= 6) ? 1 : 0);
        $petaKolom[$tgl] = $i++;
        $t = strtotime('+1 day', $t);
    }
} else {
    $q = mysqli_query($conn2, "select distinct kolom, kolom_urut from ap_cf_history_det
                                where doc_number = '$docEsc' and kolom is not null
                                order by kolom_urut asc");
    $i = 0;
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $kolom[] = array('judul' => $r['kolom'], 'hari' => '', 'tgl' => '', 'akhir_pekan' => 0);
        $petaKolom[$r['kolom']] = $i++;
    }
}
$nk = count($kolom);

/* ---- baris ---- */
$baris = array('report' => array(), 'loan' => array());
$urutBaris = array();
$q = mysqli_query($conn2, "select blok, urut, tipe, no_urut, label, tanggal, kolom, nilai
                             from ap_cf_history_det where doc_number = '$docEsc'
                            order by blok desc, urut asc, id asc");
if (!$q) {
    if (cfhSalah(mysqli_error($conn2))) { return; }
}
while ($r = mysqli_fetch_assoc($q)) {
    $blok = ($r['blok'] === 'loan') ? 'loan' : 'report';
    $u = (int) $r['urut'];
    if (!isset($baris[$blok][$u])) {
        $baris[$blok][$u] = array(
            'tipe'  => $r['tipe'],
            'no'    => (string) $r['no_urut'],
            'label' => $r['label'],
            'v'     => array_fill(0, $nk, null),
            'tot'   => null,
        );
    }
    $nilai = ($r['nilai'] === null) ? null : (float) $r['nilai'];

    if ($r['tanggal'] === null && $r['kolom'] === null) {
        $baris[$blok][$u]['tot'] = $nilai;
        continue;
    }
    $penanda = ($mode === 'tanggal') ? $r['tanggal'] : $r['kolom'];
    if (isset($petaKolom[$penanda])) {
        $baris[$blok][$u]['v'][$petaKolom[$penanda]] = $nilai;
    }
}

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
}
ksort($baris['report']);
ksort($baris['loan']);

echo json_encode(array(
    'status'   => 'success',
    'mode'     => $mode,
    'kolom'    => $kolom,
    'baris'    => array_values($baris['report']),
    'pinjaman' => array_values($baris['loan']),
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
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
