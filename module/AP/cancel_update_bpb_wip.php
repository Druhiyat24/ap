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
