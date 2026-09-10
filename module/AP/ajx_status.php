<?php
// ============================================================================
// Sumber AJAX DataTables untuk menu STATUS INFORMATION (status.php).
//
// Dulu tabelnya dirender langsung di halaman: setiap kali Search, seluruh
// halaman di-POST ulang dan browser menunggu query selesai sambil layar kosong.
// Query laporan ini BISA memakan beberapa menit untuk filter tertentu (filter
// Payment Date & Kontrabon Date tidak membatasi tanggal di tabel sumber, jadi
// seluruh isi `bpb` ikut dipindai), sehingga halaman terasa menggantung.
// Dipindah ke AJAX supaya halaman langsung tampil dan proses panjangnya
// ditutupi overlay loading.
//
// Query-nya sendiri dibangun di status_query.php - dipakai bareng oleh halaman
// ini dan ekspor Excel, supaya isi keduanya tidak bisa berbeda.
//
// Balikan: { data: [ {...}, ... ] }
// ============================================================================
ini_set('memory_limit', '2048M');
set_time_limit(0);

include '../../conn/conn.php';
require_once __DIR__ . '/status_query.php';
header('Content-Type: application/json; charset=utf-8');

$val = function ($k, $def = '') {
    return isset($_POST[$k]) && trim($_POST[$k]) !== '' ? trim($_POST[$k]) : $def;
};

$nama_supp  = $val('nama_supp', 'ALL');
$filter     = $val('filter', 'tgl_bpb');
$start_date = date('Y-m-d', strtotime($val('start_date', date('Y-m-d'))));
$end_date   = date('Y-m-d', strtotime($val('end_date', date('Y-m-d'))));

$res = mysqli_query($conn2, status_build_query($conn2, $filter, $nama_supp, $start_date, $end_date));

if (!$res) {
    http_response_code(500);
    echo json_encode(['data' => [], 'error' => mysqli_error($conn2)]);
    exit;
}

// Format tanggal & tanda '-' dikerjakan di sini (bukan di JavaScript) supaya
// aturannya sama persis dgn yang dipakai ekspor Excel.
function st_tgl($v)
{
    if (empty($v) || $v == '0000-00-00' || $v == '0000-00-00 00:00:00') { return '-'; }
    $t = strtotime($v);
    return $t ? date('d-M-Y', $t) : '-';
}
function st_isi($v)
{
    return (!empty($v) && $v !== '-') ? $v : '-';
}

$data = [];
while ($r = mysqli_fetch_assoc($res)) {
    $no = (string) $r['no_bpb'];

    $data[] = [
        'jenis'        => status_jenis_dokumen($no),
        'nama_supp'    => st_isi($r['nama_supp']),
        'no_bpb'       => st_isi($no),
        'tgl_bpb'      => st_tgl($r['tgl_bpb']),
        'approve_bpb'  => st_tgl($r['approve_bpb']),
        'verif_date'   => st_tgl($r['verif_date']),
        'no_sj'        => st_isi($r['no_sj']),
        'no_ws'        => st_isi($r['no_ws']),
        'style'        => st_isi($r['style']),
        'no_kbon'      => st_isi($r['no_kbon']),
        'tgl_kbon'     => st_tgl($r['tgl_kbon']),
        'approve_kbon' => st_tgl($r['approve_kbon']),
        'no_payment'   => st_isi($r['no_payment']),
        'tgl_payment'  => st_tgl($r['tgl_payment']),
        'approve_lp'   => st_tgl($r['approve_lp']),
        'close_lp'     => st_tgl($r['close_lp']),
        'no_pelunasan' => st_isi($r['no_pelunasan']),
        'tgl_pelunasan'=> st_tgl($r['tgl_pelunasan']),
    ];
}

echo json_encode(['data' => $data]);
