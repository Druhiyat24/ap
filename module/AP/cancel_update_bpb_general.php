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

$no_pengajuan = $_POST['no_pengajuan'] ?? '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

if (empty($no_pengajuan)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
    exit;
}

$check = mysqli_query($conn1, "SELECT status FROM Req_update_bpb_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
$row = mysqli_fetch_assoc($check);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Data not found']);
    exit;
}

if ($row['status'] == 'Cancel') {
    echo json_encode(['success' => false, 'message' => 'Request already cancelled']);
    exit;
}

if ($row['status'] == 'Approved') {
    echo json_encode(['success' => false, 'message' => 'Request already approved, cannot be cancelled']);
    exit;
}

$update = mysqli_query($conn1, "UPDATE Req_update_bpb_h SET status = 'Cancel' WHERE no_pengajuan = '$no_pengajuan_esc'");

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
?>
