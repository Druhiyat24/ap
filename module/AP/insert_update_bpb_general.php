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
ini_set('date.timezone', 'Asia/Jakarta');

$no_pengajuan  = $_POST['no_pengajuan'] ?? '';
$tgl_pengajuan = $_POST['tgl_pengajuan'] ?? date('Y-m-d');
$deskripsi     = $_POST['deskripsi'] ?? '';
$nama_supp     = $_POST['nama_supp'] ?? 'ALL';
$created_by    = $_POST['created_by'] ?? '';
$items         = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if (empty($no_pengajuan) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
    exit;
}

$no_pengajuan_esc  = mysqli_real_escape_string($conn1, $no_pengajuan);
$tgl_pengajuan_esc = mysqli_real_escape_string($conn1, $tgl_pengajuan);
$deskripsi_esc     = mysqli_real_escape_string($conn1, $deskripsi);
$nama_supp_esc     = mysqli_real_escape_string($conn1, $nama_supp);
$created_by_esc    = mysqli_real_escape_string($conn1, $created_by);
$created_at        = date('Y-m-d H:i:s');

/* Jenisnya ikut disimpan - inilah yang memisahkan daftar Fabric dgn
   Accessories walau tabelnya sama. */
$insertHeader = mysqli_query($conn1, "INSERT INTO Req_update_bpb_h (no_pengajuan, tgl_pengajuan, nama_supp, deskripsi, status, jenis, created_by, created_at)
    VALUES ('$no_pengajuan_esc', '$tgl_pengajuan_esc', '$nama_supp_esc', '$deskripsi_esc', 'Draft', '$jenis_esc', '$created_by_esc', '$created_at')");

if (!$insertHeader) {
    echo json_encode(['success' => false, 'message' => 'Failed to save header']);
    exit;
}

foreach ($items as $item) {
    $no_bpb    = mysqli_real_escape_string($conn1, $item['no_bpb'] ?? '');
    $tgl_bpb   = !empty($item['tgl_bpb']) ? date('Y-m-d', strtotime($item['tgl_bpb'])) : null;
    $item_supp = mysqli_real_escape_string($conn1, $item['nama_supp'] ?? '');
    $no_po     = mysqli_real_escape_string($conn1, $item['no_po'] ?? '');
    $id_det    = mysqli_real_escape_string($conn1, $item['id_det'] ?? '');
    $no_ws     = mysqli_real_escape_string($conn1, $item['no_ws'] ?? '');
    $id_jo     = mysqli_real_escape_string($conn1, $item['id_jo'] ?? '');
    $id_item   = mysqli_real_escape_string($conn1, $item['id_item'] ?? '');
    $desc_item = mysqli_real_escape_string($conn1, $item['desc_item'] ?? '');
    $qty       = (float) ($item['qty'] ?? 0);
    $unit      = mysqli_real_escape_string($conn1, $item['unit'] ?? '');
    $curr      = mysqli_real_escape_string($conn1, $item['curr'] ?? '');
    $price_old = (float) ($item['price_old'] ?? 0);
    $price_new = (float) ($item['price_new'] ?? 0);
    $ppn_old   = (float) ($item['ppn_old'] ?? 0);
    $ppn_new   = (float) ($item['ppn_new'] ?? 0);

    $tgl_bpb_val = $tgl_bpb ? "'$tgl_bpb'" : 'NULL';

    mysqli_query($conn1, "INSERT INTO Req_update_bpb (no_pengajuan, no_bpb, tgl_bpb, nama_supp, no_po, id_det, no_ws, id_jo, id_item, desc_item, qty, unit, curr, price_old, price_new, ppn_old, ppn_new, created_by, created_at)
        VALUES ('$no_pengajuan_esc', '$no_bpb', $tgl_bpb_val, '$item_supp', '$no_po', '$id_det', '$no_ws', '$id_jo', '$id_item', '$desc_item', $qty, '$unit', '$curr', $price_old, $price_new, $ppn_old, $ppn_new, '$created_by_esc', '$created_at')");
}

echo json_encode(['success' => true, 'no_pengajuan' => $no_pengajuan]);
?>
