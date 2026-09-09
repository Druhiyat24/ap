<?php
// ============================================================================
// proses_upload_ppn.php — parser upload rekap FAKTUR PAJAK MASUKAN (e-Faktur/
// Coretax) untuk tab "PPN Masukan" di Memorial Journal.
//
// Baris file disimpan APA ADANYA (data full) ke tbl_ppn_masukan_upload dengan
// status 'Temp' milik user ybs. Jurnalnya sendiri baru dibentuk saat Save
// (save_mj_ppn.php) — di sini murni staging + normalisasi angka/tanggal.
//
// Kolom file (0-based, sesuai rekap Coretax):
//   0 Bulan | 1 Jenis | 2 Nama Penjual | 3 Nomor Identitas WP | 4 No Faktur
//   5 Tgl Faktur | 6 DPP | 7 DPP Nilai Lain | 8 PPN | 9 PPnBM
//   10 Kode & No Seri Faktur yang Diganti/Diretur
// ============================================================================
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

include '../../../conn/conn.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

// Angka: "(1,055,000.00)" -> -1055000.00 ; "4,290,000.00" -> 4290000.00 ; "-"/"" -> 0
function ppn_num($v) {
    if ($v === null) return 0.0;
    if (is_int($v) || is_float($v)) return (float) $v;
    $s = trim((string) $v);
    if ($s === '' || $s === '-') return 0.0;
    $neg = (strpos($s, '(') !== false && strpos($s, ')') !== false) || strpos($s, '-') === 0;
    $s = str_replace(',', '', preg_replace('/[^0-9.,\-]/', '', $s));
    $s = ltrim($s, '-');
    if ($s === '' || !is_numeric($s)) return 0.0;
    $f = (float) $s;
    return $neg ? -$f : $f;
}

// Tanggal: ISO "2026-01-13T07:00:00+07:00", serial Excel, atau string biasa.
function ppn_date($v) {
    if ($v === null) return null;
    if (is_numeric($v) && (float) $v > 1000) {
        try { return XlsDate::excelToDateTimeObject((float) $v)->format('Y-m-d'); } catch (Exception $e) {}
    }
    $s = trim((string) $v);
    if ($s === '' || $s === '-') return null;
    $ts = strtotime($s);
    return $ts ? date('Y-m-d', $ts) : null;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['status' => 'error', 'message' => 'File not found.']);
    exit;
}

try {
    $sheet = IOFactory::load($_FILES['file']['tmp_name'])->getActiveSheet();
    $rows  = $sheet->toArray(null, true, false, false);

    $user        = $_SESSION['username'] ?? 'system';
    $create_date = date('Y-m-d H:i:s');
    $e           = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };

    // ---- Profit Center sekarang DARI FILE (kolom ke-12 / L), bukan dari header --
    // Satu file boleh memuat beberapa profit center. Nilai yang diterima: kode_pc
    // (NAG/NAK) maupun id_pc (PCP001/PCP002), tidak peduli huruf besar/kecil —
    // semuanya dinormalkan ke kode_pc supaya sama dgn isi kolom profit_center di
    // jurnal.
    $pcValid = [];
    $qpc = mysqli_query($conn1, "select kode_pc, id_pc from master_pc where status = 'Active'");
    while ($qpc && $rpc = mysqli_fetch_assoc($qpc)) {
        $pcValid[strtoupper(trim($rpc['kode_pc']))] = $rpc['kode_pc'];
        $pcValid[strtoupper(trim($rpc['id_pc']))]   = $rpc['kode_pc'];
    }
    $pcList = implode(', ', array_values(array_unique($pcValid)));

    // ---- Pass 1: KUMPULKAN baris valid + periksa Profit Center ------------------
    // Diperiksa SEBELUM satu baris pun ditulis: kalau ada yang kosong/tidak dikenal,
    // upload dibatalkan seluruhnya dan staging lama TIDAK ikut terhapus.
    $siap = []; $skip = 0; $kosong = []; $asing = [];
    foreach ($rows as $i => $r) {
        $no_faktur = isset($r[4]) ? trim((string) $r[4]) : '';
        // Lewati baris header / baris kosong (tanpa nomor faktur).
        if ($no_faktur === '' || $no_faktur === '-' || stripos($no_faktur, 'faktur') !== false) { $skip++; continue; }

        $baris = $i + 1;                                   // nomor baris seperti di Excel
        $pcRaw = isset($r[11]) ? trim((string) $r[11]) : '';
        if ($pcRaw === '' || $pcRaw === '-') { $kosong[] = $baris; continue; }

        $pcKey = strtoupper($pcRaw);
        if (!isset($pcValid[$pcKey])) { $asing[] = $baris . " ('" . $pcRaw . "')"; continue; }

        $r['__pc']    = $pcValid[$pcKey];
        $r['__fk']    = $no_faktur;
        $siap[] = $r;
    }

    // Ringkas daftar baris bermasalah supaya pesan Swal tidak kepanjangan.
    $ringkas = function ($arr) {
        $n = count($arr);
        $tampil = array_slice($arr, 0, 10);
        return implode(', ', $tampil) . ($n > 10 ? ' (dan ' . ($n - 10) . ' baris lagi)' : '');
    };
    if ($kosong) {
        echo json_encode(['status' => 'error',
            'message' => 'Profit Center is empty on ' . count($kosong) . ' row(s): ' . $ringkas($kosong) . '. '
                       . 'Every invoice row must have a Profit Center (' . $pcList . ') in the last column. '
                       . 'Nothing was uploaded.']);
        exit;
    }
    if ($asing) {
        echo json_encode(['status' => 'error',
            'message' => 'Unknown Profit Center on ' . count($asing) . ' row(s): ' . $ringkas($asing) . '. '
                       . 'Allowed values: ' . $pcList . '. Nothing was uploaded.']);
        exit;
    }
    if (!$siap) {
        echo json_encode(['status' => 'error', 'message' => 'No invoice rows were read. Please check the file format.']);
        exit;
    }

    // ---- Pass 2: tulis ke staging ---------------------------------------------
    mysqli_begin_transaction($conn2);

    // Buang staging lama milik user ini (upload ulang = ganti isi, bukan numpuk).
    mysqli_query($conn2, "DELETE FROM tbl_ppn_masukan_upload WHERE create_by = '" . $e($user) . "' AND status = 'Temp'");

    $ok = 0;
    foreach ($siap as $r) {
        $tgl = ppn_date($r[5] ?? null);
        $q = "INSERT INTO tbl_ppn_masukan_upload
            (bulan, jenis, nama_penjual, npwp, no_faktur, tgl_faktur, dpp, dpp_nilai_lain, ppn, ppnbm,
             faktur_diganti, profit_center, status, create_by, create_date)
            VALUES ('" . $e(trim((string)($r[0] ?? ''))) . "', '" . $e(trim((string)($r[1] ?? ''))) . "', '" . $e(trim((string)($r[2] ?? ''))) . "',
             '" . $e(trim((string)($r[3] ?? ''))) . "', '" . $e($r['__fk']) . "', " . ($tgl ? "'" . $e($tgl) . "'" : "NULL") . ",
             " . ppn_num($r[6] ?? 0) . ", " . ppn_num($r[7] ?? 0) . ", " . ppn_num($r[8] ?? 0) . ", " . ppn_num($r[9] ?? 0) . ",
             '" . $e(trim((string)($r[10] ?? ''))) . "', '" . $e($r['__pc']) . "', 'Temp', '" . $e($user) . "', '" . $e($create_date) . "')";
        if (mysqli_query($conn2, $q) === false) {
            mysqli_rollback($conn2);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save invoice row ' . $r['__fk'] . ': ' . mysqli_error($conn2)]);
            exit;
        }
        $ok++;
    }

    mysqli_commit($conn2);
    echo json_encode(['status' => 'success', 'rows' => $ok, 'skipped' => $skip]);

} catch (Exception $ex) {
    @mysqli_rollback($conn2);
    echo json_encode(['status' => 'error', 'message' => $ex->getMessage()]);
}
