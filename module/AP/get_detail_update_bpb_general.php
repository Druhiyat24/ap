<?php
/* ============================================================================
   Update BPB - GENERAL.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumennya dibaca dari tabel `bpb` dgn bpbno_int LIKE 'GEN/%'. GEN/IN
   maupun GEN/RI ikut semua: diperiksa ke data 8 Okt 2026, jurnal GEN/RI
   SEARAH dgn penerimaan (persediaan didebit, GR/IR dikredit, type
   'AP - BPB'), jadi bukan retur akuntansi seperti GK/RO di Fabric.

   AWAS - kunci sambungan ke PO BEDA dari Fabric/Accessories:
   masteritem.id_gen KOSONG (NULL) untuk semua item GEN, sedangkan
   po_item.id_gen justru berisi id_item-nya langsung. Jadi di sini
   dipakai  pi.id_gen = bpb.id_item , bukan  pi.id_gen = masteritem.id_gen .
   Dgn kunci yang salah, 0 dari 4.816 baris dapat harga PO - tombol
   "isi dari PO" dan ceklis "sembunyikan yang sudah cocok" mati tanpa
   pesan apa pun. Dgn kunci ini: 4.812 dari 4.816 dapat harga.
   ============================================================================ */
include '../../conn/conn.php';
$jenis = 'general';
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
