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
