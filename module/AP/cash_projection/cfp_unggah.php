<?php
/* ============================================================================
   Membaca berkas unggahan Form Projection Other dan mengembalikan barisnya
   sbg JSON - BENTUKNYA PERSIS seperti yang dibuat tombol "Add to list" di
   layarnya, jadi sisi JS tinggal menyambung ke daftar yang sudah ada dan
   tombol Save-nya tidak perlu tahu baris itu diketik atau diunggah.

   TIDAK MENYIMPAN APA PUN. Berkas ini hanya membaca dan memeriksa; yang
   menulis ke basis data tetap save_book_transfer.php dan saudaranya, sesudah
   pemakai menekan Save - jadi satu unggahan tetap jadi SATU dokumen.

   BARIS YANG SALAH TIDAK MENGGAGALKAN SELURUHNYA: yang sah tetap
   dikembalikan, yang bermasalah dilaporkan satu per satu dgn nomor barisnya
   supaya pemakai tahu persis mana yang harus dibetulkan.

   MATA UANG, KURS, IDR/USD, PROFIT CENTER DAN INTRA/INTER PC TIDAK DIMINTA
   dari pemakai - semuanya diturunkan di sini dgn rumus yang sama persis dgn
   layarnya. Kalau ikut diminta, pemakai bisa mengetik kurs yang berbeda dari
   kurs pajak hari itu dan angkanya jadi tidak bisa dipertanggungjawabkan.
   ============================================================================ */
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../../conn/conn.php';
require '../../../vendor/autoload.php';
include_once __DIR__ . '/cfp_unggah_lib.php';

header('Content-Type: application/json');

use PhpOffice\PhpSpreadsheet\IOFactory;

function cfpUJawab($a) { echo json_encode($a); exit; }

if (empty($_SESSION['username'])) {
    cfpUJawab(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
}

$jenis = isset($_POST['jenis']) ? strtolower(trim($_POST['jenis'])) : '';
if (!in_array($jenis, array('bt', 'oi', 'pv'), true)) {
    cfpUJawab(array('status' => 'error', 'message' => 'Unknown form type.'));
}
if (!isset($_FILES['berkas']) || $_FILES['berkas']['error'] !== UPLOAD_ERR_OK) {
    cfpUJawab(array('status' => 'error', 'message' => 'No file received, or it was too large.'));
}

$ext = strtolower(pathinfo($_FILES['berkas']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, array('xlsx', 'xls'), true)) {
    cfpUJawab(array('status' => 'error', 'message' => 'Please upload the .xlsx template.'));
}

try {
    $sp = IOFactory::load($_FILES['berkas']['tmp_name']);
} catch (Exception $e) {
    cfpUJawab(array('status' => 'error', 'message' => 'The file could not be read: ' . $e->getMessage()));
}

/* Lembar "Data" kalau ada - kalau pemakai mengirim berkas lain, dipakai
   lembar pertamanya, supaya daftar yang disusun sendiri tetap bisa dipakai. */
$l = $sp->getSheetByName('Data');
if (!$l) { $l = $sp->getSheet(0); }

$akun     = cfp_daftar_akun($conn1);
$bank     = cfp_daftar_bank($conn1);
$kategori = cfp_daftar_kategori($conn1, $jenis === 'pv' ? 'Cash Out' : 'Cash In');
$master   = ($jenis === 'pv') ? cfp_daftar_supplier($conn1) : cfp_daftar_customer($conn1);

$baris    = array();
$salah    = array();
$dibaca   = 0;
$kursMemo = array();   /* satu tanggal cukup dicari sekali */

$akhir = $l->getHighestDataRow();
for ($n = 2; $n <= $akhir; $n++) {
    $sel = array();
    for ($k = 1; $k <= 6; $k++) {
        $sel[$k] = trim((string) $l->getCellByColumnAndRow($k, $n)->getValue());
    }
    /* Baris kosong dilewati diam-diam - lembar Excel hampir selalu punya
       beberapa baris kosong di bawah datanya. */
    if (implode('', $sel) === '') { continue; }

    /* Baris contoh bawaan template ikut dilewati, bukan dianggap salah -
       pemakai sering lupa menghapusnya dan itu bukan kesalahan data. */
    $gabung = strtolower(implode(' ', $sel));
    if (strpos($gabung, 'example - delete this row') !== false) { continue; }

    $dibaca++;
    $tgl = cfp_unggah_tanggal($sel[1]);
    if ($tgl === '') { $salah[] = 'Row ' . $n . ': the date could not be read.'; continue; }

    /* Kurs PAJAK tanggal itu - dicari sekali per tanggal. */
    if (!isset($kursMemo[$tgl])) {
        $k = cfp_kurs_pajak($conn1, $tgl, 'USD');
        $kursMemo[$tgl] = $k['ketemu'] ? (float) $k['rate'] : 0.0;
    }
    $kurs = $kursMemo[$tgl];

    /* ------------------------------------------------------ Book Transfer */
    if ($jenis === 'bt') {
        $src = cfp_unggah_cocok($sel[2], array_keys($akun));
        $dst = cfp_unggah_cocok($sel[3], array_keys($akun));
        $amt = cfp_unggah_angka($sel[4]);
        $ket = (string) $sel[5];

        if ($src === null) { $salah[] = 'Row ' . $n . ': source account "' . $sel[2] . '" is not in the list.'; continue; }
        if ($dst === null) { $salah[] = 'Row ' . $n . ': destination account "' . $sel[3] . '" is not in the list.'; continue; }
        if ($src === $dst) { $salah[] = 'Row ' . $n . ': source and destination are the same.'; continue; }
        if ($amt <= 0)     { $salah[] = 'Row ' . $n . ': amount must be greater than 0.'; continue; }

        $cs = $akun[$src]['curr'];
        $ct = $akun[$dst]['curr'];
        $rupiahSaja = ($cs === 'IDR' && $ct === 'IDR');
        $rate = $rupiahSaja ? 1.0 : $kurs;
        if (!$rupiahSaja && $rate <= 0) {
            $salah[] = 'Row ' . $n . ': no tax rate found for ' . $tgl . '.'; continue;
        }
        $idr = ($cs === 'IDR') ? $amt : $amt * $rate;
        $usd = 0.0;
        if ($cs !== 'IDR')     { $usd = $amt; }
        elseif ($ct !== 'IDR') { $usd = $amt / $rate; }

        $pcS = $akun[$src]['nama_pc'];
        $pcT = $akun[$dst]['nama_pc'];
        $baris[] = array(
            'source'      => $src,
            'tujuan'      => $dst,
            'tanggal'     => $tgl,
            'curr'        => $cs,
            'amount'      => $amt,
            'rate'        => $rate,
            'amount_idr'  => $idr,
            'deskripsi'   => $ket,
            'pc_source'   => $pcS,
            'pc_tujuan'   => $pcT,
            'amount_usd'  => $usd,
            'curr_tujuan' => $ct,
            'intra_inter' => ($pcS !== '' && $pcS === $pcT) ? 'Intra PC' : 'Inter PC',
        );
        continue;
    }

    /* ------------------------ Other Inflow & PV Expense: kolomnya sama ---- */
    $nama = trim((string) $sel[2]);
    $ktg  = cfp_unggah_cocok($sel[3], $kategori);
    $bnk  = cfp_unggah_cocok($sel[4], array_keys($bank));
    $amt  = cfp_unggah_angka($sel[5]);
    $ket  = (string) $sel[6];

    if ($nama === '')  { $salah[] = 'Row ' . $n . ': the name is empty.'; continue; }
    if ($ktg === null) { $salah[] = 'Row ' . $n . ': category "' . $sel[3] . '" is not in the list.'; continue; }
    if ($bnk === null) { $salah[] = 'Row ' . $n . ': bank account "' . $sel[4] . '" is not in the list.'; continue; }
    if ($amt <= 0)     { $salah[] = 'Row ' . $n . ': amount must be greater than 0.'; continue; }

    /* Nama yang PERSIS ada di master dipakai beserta idnya; selain itu
       diterima sbg ketikan bebas dgn id kosong - sama persis dgn yang boleh
       dilakukan di layarnya. */
    $id = '';
    $namaResmi = $nama;
    $cari = strtoupper(preg_replace('/\s+/', ' ', $nama));
    foreach ($master as $idm => $nm) {
        if (strtoupper(preg_replace('/\s+/', ' ', trim($nm))) === $cari) {
            $id = (string) $idm;
            $namaResmi = $nm;
            break;
        }
    }

    $cb     = $bank[$bnk]['curr'];
    $rupiah = ($cb === 'IDR');
    $rate   = $rupiah ? 1.0 : $kurs;
    if (!$rupiah && $rate <= 0) {
        $salah[] = 'Row ' . $n . ': no tax rate found for ' . $tgl . '.'; continue;
    }

    $baris[] = array(
        'nama'        => $namaResmi,
        'id_supplier' => $id,
        'kategori'    => $ktg,
        'bank'        => $bnk,
        'tanggal'     => $tgl,
        'curr'        => $cb,
        'amount'      => $amt,
        'rate'        => $rate,
        'amount_idr'  => $rupiah ? $amt : $amt * $rate,
        'amount_usd'  => $rupiah ? 0.0 : $amt,
        'deskripsi'   => $ket,
        'pc'          => $bank[$bnk]['nama_pc'],
    );
}

cfpUJawab(array('status' => 'success', 'baris' => $baris,
                'dibaca' => $dibaca, 'salah' => $salah));
