<?php
/* ============================================================================
   Update BPB - WIP.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   WIP = barang dalam proses (makloon). Dokumennya dibaca dari tabel `bpb`
   dgn bpbno_int LIKE 'WIP/%' ATAU LIKE 'SPCK/%' - dua awalan nomor, satu
   menu, karena keduanya pekerjaan yang sama. Diperiksa ke data 13 Okt
   2026: 35.916 dokumen WIP dan 11 dokumen SPCK.

   Jurnalnya MAKLOON, bukan persediaan: GR/IR 2.10.06 PIHAK KETIGA -
   MAKLOON lawan beban 5.69.0x (PRINTING / EMBRODEIRY / WASHING / CMT /
   PAKAIAN JADI). Khusus awalan WIP, jurnal aslinya memakai cost center
   DEP04SUB004 - itu sudah ikut terbawa di proses_approve_update_bpb_wip.php,
   jangan diubah.

   AWAS - kunci sambungan ke PO sama dgn WIP, BEDA dari Fabric/
   Accessories: dipakai  pi.id_gen = bpb.id_item , bukan lewat
   masteritem.id_gen . Diukur 13 Okt 2026 atas 26.250 baris WIP/SPCK
   yang ber-PO dan berharga:
     dgn pi.id_gen = masteritem.id_gen :      0 baris dapat harga PO
     dgn pi.id_gen = a.id_item         : 26.193 baris dapat harga PO
   Dgn kunci yang salah, kolom is_match selalu 0 - tombol "isi dari PO"
   dan ceklis "sembunyikan yang sudah cocok" mati tanpa pesan apa pun.
   ============================================================================ */
include '../../conn/conn.php';
$jenis = 'wip';
$jenis_esc = mysqli_real_escape_string($conn1, $jenis);
header('Content-Type: application/json');

$no_pengajuan = isset($_GET['no_pengajuan']) ? $_GET['no_pengajuan'] : '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

$header = null;
$h = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, nama_supp, deskripsi, status, created_by, created_at
    FROM Req_update_bpb_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
if ($row = mysqli_fetch_assoc($h)) {
    $row['tgl_pengajuan'] = !empty($row['tgl_pengajuan']) ? date('d-M-Y', strtotime($row['tgl_pengajuan'])) : '-';
    $row['created_at'] = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
    $header = $row;
}

$items = [];
$sql = mysqli_query($conn1, "SELECT no_bpb, tgl_bpb, nama_supp, no_ws, id_item, desc_item, qty, unit, curr, price_old, price_new, ppn_old, ppn_new
    FROM Req_update_bpb
    WHERE no_pengajuan = '$no_pengajuan_esc'
    ORDER BY no_bpb, no_ws, id_item");
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_bpb'] = !empty($row['tgl_bpb']) ? date('d-M-Y', strtotime($row['tgl_bpb'])) : '-';
    $items[] = $row;
}

echo json_encode(['header' => $header, 'items' => $items], JSON_INVALID_UTF8_SUBSTITUTE);
?>
