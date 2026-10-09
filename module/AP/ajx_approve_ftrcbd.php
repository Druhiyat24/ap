<?php
/* ============================================================================
   Sumber data DataTables untuk HALAMAN PERSETUJUAN FTR CBD
   (approve_ftrcbd.php). Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Bentuknya dibuat SAMA dgn Approval Update BPB: tiap baris membawa kolom
   `checkbox` dan `action` yang sudah jadi markup, supaya halamannya tinggal
   menampilkan.

   Isinya HANYA dokumen berstatus draft - disaring di sini, bukan di tampilan.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

$sql = mysqli_query($conn2, "select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, supp,
        GROUP_CONCAT(DISTINCT no_po ORDER BY no_po SEPARATOR ', ') no_po,
        ROUND(SUM(total),2) total, MAX(curr) curr, status, create_user, create_date
    from ftr_cbd
    where status = 'draft'
    group by no_ftr_cbd
    order by tgl_ftr_cbd desc, no_ftr_cbd desc");

$data = array();
while ($row = mysqli_fetch_assoc($sql)) {
    $no = $row['no_ftr'];

    $row['tgl_ftr'] = !empty($row['tgl_ftr']) ? date('d-M-Y', strtotime($row['tgl_ftr'])) : '-';
    $row['total_n'] = (float) $row['total'];
    $row['total']   = number_format((float) $row['total'], 2);

    if (!empty($row['create_user'])) {
        $tgl = !empty($row['create_date']) ? date('d-M-Y H:i:s', strtotime($row['create_date'])) : '-';
        $row['create_user'] = $row['create_user'] . ' (' . $tgl . ')';
    }

    /* Pil status memakai kosakata .ftl- yang sama dgn halaman daftar. */
    $row['status'] = '<span class="ftl-st is-draft">' . htmlspecialchars($row['status']) . '</span>';

    $row['checkbox'] = '<input type="checkbox" class="chk-ftr" value="' . htmlspecialchars($no, ENT_QUOTES) . '">';

    $row['action'] = '<div class="ftl-act">'
        . '<button type="button" class="ftl-mini is-info btn-view-ftr" title="Show the FTR detail"'
        . ' data-no="' . htmlspecialchars($no, ENT_QUOTES) . '"><i class="fa fa-eye" aria-hidden="true"></i> View</button>'
        . '<a class="ftl-mini is-pdf" target="_blank" title="Open the printable PDF"'
        . ' href="pdf_ftrcbd.php?noftrcbd=' . htmlspecialchars($no, ENT_QUOTES) . '">'
        . '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf</a>'
        . '</div>';

    $data[] = $row;
}

echo json_encode(array('data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
?>