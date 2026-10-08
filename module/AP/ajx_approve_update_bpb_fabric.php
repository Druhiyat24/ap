<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$sql = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, status, deskripsi, created_by, created_at
    FROM update_bpb_fabric_h
    WHERE status NOT IN ('Approved','Cancel')
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
