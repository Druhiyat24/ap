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

$sql = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, status, deskripsi, created_by, created_at
    FROM Req_update_bpb_h
    WHERE status NOT IN ('Approved','Cancel') AND jenis = '$jenis_esc'
    ORDER BY id DESC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_pengajuan'] = !empty($row['tgl_pengajuan']) ? date('d-M-Y', strtotime($row['tgl_pengajuan'])) : '-';

    if (!empty($row['created_by'])) {
        $created_at_fmt = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
        $row['created_by'] = $row['created_by'] . ' (' . $created_at_fmt . ')';
    }

    $statusRaw = $row['status'];

    /* Pil status memakai kosakata .ftl- yang sama dgn halaman daftar;
       badge bawaan Bootstrap warnanya beradu dgn kepala kartu navy. */
    $kelasStatus = array('approved' => 'is-approved', 'cancel' => 'is-cancel');
    $kunci = strtolower($statusRaw);
    $kelas = isset($kelasStatus[$kunci]) ? $kelasStatus[$kunci] : 'is-draft';

    $row['status'] = '<span class="ftl-st ' . $kelas . '">' . htmlspecialchars($statusRaw) . '</span>';

    $row['checkbox'] = '<input type="checkbox" class="chk-pengajuan" value="' . htmlspecialchars($row['no_pengajuan']) . '">';

    $row['action'] = '<div class="ftl-act"><button type="button" class="ftl-mini is-info btn-view-pengajuan" title="Show the request detail" data-no="' . htmlspecialchars($row['no_pengajuan'], ENT_QUOTES) . '"><i class="fa fa-eye" aria-hidden="true"></i> View</button></div>';

    $data[] = $row;
}

echo json_encode(['data' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
?>
