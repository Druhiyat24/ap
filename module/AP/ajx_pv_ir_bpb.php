<?php
// Kembalikan daftar no_bpb milik sebuah Invoice Received (dari kontrabon baru),
// dipakai untuk AUTO-CEKLIS BPB di Form Payment Voucher saat IR dipilih.
// Sumber: ir_kontrabon_bpb (JOIN ir_kontrabon_h via unik_code, doc_number = IR).
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$ir = isset($_POST['ir_number']) ? trim($_POST['ir_number']) : '';
if ($ir === '' || $ir === '-') { echo json_encode(['ok' => false, 'bpb' => []]); exit; }

$ire = mysqli_real_escape_string($conn2, $ir);
$q = mysqli_query($conn2, "SELECT DISTINCT b.no_bpb, f.no_faktur, f.tgl_faktur
    FROM ir_kontrabon_bpb b
    JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
    LEFT JOIN ir_kontrabon_faktur f ON f.id = b.faktur_id
    WHERE h.doc_number = '$ire' AND h.status <> 'Cancel'
      AND b.no_bpb IS NOT NULL AND b.no_bpb <> ''");

$list = [];
$fak  = [];  // no_bpb => {no_faktur, tgl_faktur} untuk auto-fill input di form PV
while ($q && ($r = mysqli_fetch_assoc($q))) {
    $list[] = $r['no_bpb'];
    $fak[$r['no_bpb']] = [
        'no_faktur'  => $r['no_faktur'] ?? '',
        'tgl_faktur' => (!empty($r['tgl_faktur']) && $r['tgl_faktur'] !== '0000-00-00') ? $r['tgl_faktur'] : '',
    ];
}

// Daftar SEMUA faktur (distinct) di IR ini -> jadi saran (datalist) di input No Faktur
// untuk BPB tambahan manual. User tetap boleh mengetik di luar daftar ini.
$flq = mysqli_query($conn2, "SELECT f.no_faktur, MAX(f.tgl_faktur) tgl_faktur
    FROM ir_kontrabon_faktur f
    JOIN ir_kontrabon_h h ON h.unik_code = f.unik_code
    WHERE h.doc_number = '$ire' AND h.status <> 'Cancel'
      AND f.no_faktur IS NOT NULL AND f.no_faktur <> ''
    GROUP BY f.no_faktur ORDER BY f.no_faktur");
$faktur_list = [];
while ($flq && ($fr2 = mysqli_fetch_assoc($flq))) {
    $faktur_list[] = [
        'no_faktur'  => $fr2['no_faktur'],
        'tgl_faktur' => (!empty($fr2['tgl_faktur']) && $fr2['tgl_faktur'] !== '0000-00-00') ? $fr2['tgl_faktur'] : '',
    ];
}

echo json_encode(['ok' => true, 'bpb' => $list, 'faktur' => $fak, 'faktur_list' => $faktur_list]);
