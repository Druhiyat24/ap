<?php
/* ============================================================================
   Update BPB - FABRIC.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen kain punya tabel kepala sendiri: whs_inmaterial_fabric (+_det)
   utk PENERIMAAN (GK/IN) dan whs_bppb_h/whs_bppb_ro utk RETUR (GK/RO).
   Keduanya dibedakan, karena jurnal GK/RO arahnya TERBALIK dari GK/IN.
   (Bandingkan Accessories & General: dokumennya langsung di `bpb` dan
   RI-nya tetap dihitung sbg penerimaan.)
   ============================================================================ */
include '../../conn/conn.php';
$jenis = 'fabric';
$jenis_esc = mysqli_real_escape_string($conn1, $jenis);
header('Content-Type: application/json');

/* FILTER TANGGAL. Sebelumnya query ini sama sekali TIDAK punya WHERE,
   padahal halaman mengirim start_date & end_date - jadi seluruh pengajuan
   selalu tampil dan filter Oktober tetap memunculkan dokumen Juli.

   Isian tanggalnya dd-mm-yyyy, jadi dikonversi di sini. Kalau salah satu
   kosong filternya tidak dipasang, supaya 'tampilkan semua' tetap bisa. */
function ubfTanggal($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));   /* dd-mm-yyyy dibaca sbg d-m-Y */
    return $t ? date('Y-m-d', $t) : '';
}

$start_date = ubfTanggal($_POST['start_date'] ?? '');
$end_date   = ubfTanggal($_POST['end_date'] ?? '');

$where = '';
if ($start_date !== '' && $end_date !== '') {
    $where = "WHERE tgl_pengajuan BETWEEN '" . mysqli_real_escape_string($conn1, $start_date)
           . "' AND '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}

$sql = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, status, deskripsi, created_by, created_at
    FROM Req_update_bpb_h
    $where AND jenis = '$jenis_esc'
    ORDER BY id DESC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_pengajuan'] = !empty($row['tgl_pengajuan']) ? date('d-M-Y', strtotime($row['tgl_pengajuan'])) : '-';

    if (!empty($row['created_by'])) {
        $created_at_fmt = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
        $row['created_by'] = $row['created_by'] . ' (' . $created_at_fmt . ')';
    }

    $statusRaw = $row['status'];

    /* Pil status memakai kosakata .ftl- (app-ftr-list.css), sama dgn daftar
       FTR & Petty Cash Out. Dipetakan tegas, bukan lewat strtolower(), supaya
       status bernama dua kata tidak menghasilkan nama kelas rusak. */
    $kelasStatus = array('approved' => 'is-approved', 'cancel' => 'is-cancel',
                        'draft' => 'is-draft', 'waiting' => 'is-draft');
    $kunci = strtolower(trim($statusRaw));
    $row['status'] = '<span class="ftl-st ' . ($kelasStatus[$kunci] ?? '') . '">'
                   . htmlspecialchars($statusRaw, ENT_QUOTES) . '</span>';
    $row['action'] = '<div class="ftl-act">'
        . '<button type="button" class="ftl-mini is-info btn-view-pengajuan" title="Show the request detail" data-no="' . htmlspecialchars($row['no_pengajuan'], ENT_QUOTES) . '"><i class="fa fa-eye" aria-hidden="true"></i> View</button>'
        . '<a href="pdf_Req_update_bpb.php?no_pengajuan=' . urlencode($row['no_pengajuan']) . '" target="_blank" class="ftl-mini is-pdf" title="Open the printable PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf</a>';

    if ($statusRaw != 'Approved' && $statusRaw != 'Cancel') {
        $row['action'] .= '<button type="button" class="ftl-mini is-cancel btn-cancel-pengajuan" title="Cancel this request" data-no="' . htmlspecialchars($row['no_pengajuan'], ENT_QUOTES) . '"><i class="fa fa-trash" aria-hidden="true"></i> Cancel</button>';
    }
    $row['action'] .= '</div>';

    $data[] = $row;
}

echo json_encode(['data' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
?>
