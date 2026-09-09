<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$nama_type  = isset($_POST['nama_type']) ? trim($_POST['nama_type']) : 'ALL';
$status     = isset($_POST['status']) ? trim($_POST['status']) : 'ALL';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : '';
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : '';
$user       = isset($_POST['user']) ? trim($_POST['user']) : '';

$conditions = [];
if ($nama_type !== '' && $nama_type !== 'ALL') {
    $conditions[] = "a.id_cmj = '" . mysqli_real_escape_string($conn2, $nama_type) . "'";
}
if ($status !== '' && $status !== 'ALL') {
    $conditions[] = "a.status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}
if ($start_date !== '' && $end_date !== '') {
    $conditions[] = "a.mj_date BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'";
} elseif (empty($conditions) && $start_date === '' && $end_date === '') {
    // Default (belum pernah filter sama sekali) - tampilkan hari ini saja,
    // persis perilaku halaman sebelumnya.
    $conditions[] = "a.mj_date = CURDATE()";
}

$where = empty($conditions) ? '' : ('WHERE ' . implode(' AND ', $conditions));

// LEFT JOIN ke tbl_ppn_masukan_upload (subquery DISTINCT no_mj) -> MAX(p.no_mj)
// dipakai sbg penanda "jurnal ini asalnya dari tab PPN Masukan". Dipakai di
// bawah utk memunculkan tombol Export (data mentah/uploadan) di kolom Action.
$sql = mysqli_query($conn2, "select a.no_mj, a.mj_date, a.id_cmj, b.nama_cmj, a.curr, sum(a.debit) debit, sum(a.credit) credit, a.keterangan, a.status,
        MIN(c.status_closing) status_closing, MAX(p.no_mj) ppn_flag
    from tbl_memorial_journal a
    left join master_category_mj b on b.id_cmj = a.id_cmj
    left join tbl_closing_periode c on a.mj_date BETWEEN c.tgl_awal AND c.tgl_akhir
    left join (select distinct no_mj from tbl_ppn_masukan_upload where no_mj is not null and no_mj <> '') p on p.no_mj = a.no_mj
    $where
    group by a.no_mj
    order by a.mj_date desc, a.no_mj desc");

// Permission user SAMA utk semua baris - cukup query 1x di luar loop (di
// file sebelumnya query ini diulang tiap baris, padahal hasilnya selalu
// sama utk 1 user yang sedang login).
$querys = mysqli_query($conn1, "select Groupp, finance, ap_apprv_lp from userpassword where username = '" . mysqli_real_escape_string($conn1, $user) . "'");
$rs = mysqli_fetch_array($querys);
$fin = isset($rs['finance']) ? $rs['finance'] : null;
$app = isset($rs['ap_apprv_lp']) ? $rs['ap_apprv_lp'] : null;

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $noMj = $row['no_mj'];
    $rowStatus = $row['status'];

    // Khusus jurnal yang asalnya dari tab PPN Masukan (ada baris di
    // tbl_ppn_masukan_upload dgn no_mj ini) -> tombol Export data mentah/uploadan.
    // Sengaja SELALU ditampilkan (termasuk saat period locked / sudah Cancel) -
    // ini cuma menampilkan ulang file yang dulu diupload, read-only, tidak
    // mengubah status jurnal apa pun.
    $exportBtn = '';
    if (!empty($row['ppn_flag'])) {
        $exportBtn = '<a href="memorial_journal/ekspor_ppn_masukan_by_gm.php?no_mj=' . base64_encode($noMj) . '" target="_blank" class="btn btn-sm btn-outline-success" title="Export raw uploaded invoice data (Excel)"><i class="fa fa-file-excel-o"></i> Export</a>';
    }

    // Journal di periode yang sudah CLOSING -> tampilkan badge "PERIOD LOCKED"
    // (tombol Post/Edit/Cancel disembunyikan), konsisten dengan halaman Edit Journal.
    $isClosed = ($row['status_closing'] === 'Closed');

    if ($isClosed) {
        $action = '<div style="display:flex; flex-direction:column; align-items:center; gap:2px;">'
            . '<span class="badge badge-danger" style="font-size:11px; padding:5px 8px;"><i class="fa fa-lock"></i> PERIOD LOCKED</span>'
            . '<small style="color:#888;">Open period to edit</small>'
            . $exportBtn
            . '</div>';
    } else {
        $editBtn   = '<a href="edit-memorial-journal.php?no_mj=' . base64_encode($noMj) . '" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fa fa-edit"></i> Edit</a>';
        $cancelBtn = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-mj" data-no="' . htmlspecialchars($noMj) . '" title="Cancel"><i class="fa fa-ban"></i> Cancel</button>';

        $action = '<div class="kbon-action-buttons">';
        if ($rowStatus == 'Draft' && $fin == '1') {
            $action .= '<button type="button" class="btn btn-sm btn-outline-info btn-approve-mj" data-no="' . htmlspecialchars($noMj) . '" title="Post"><i class="fa fa-paper-plane"></i> Post</button>';
            $action .= $editBtn;
            $action .= $cancelBtn;
        } elseif ($rowStatus == 'Post' && $fin == '1' && $app != '1') {
            $action .= '<span class="badge text-bg-success">Post</span>';
            $action .= $editBtn;
        } elseif ($rowStatus == 'Post' && $fin == '1' && $app == '1') {
            $action .= $cancelBtn;
            $action .= $editBtn;
        } elseif ($rowStatus == 'Cancel' && $fin == '1') {
            $action .= '<span class="badge text-bg-danger">Canceled</span>';
        }
        $action .= $exportBtn;
        $action .= '</div>';
    }

    $data[] = [
        'no_mj'      => $noMj,
        'mj_date'    => !empty($row['mj_date']) ? date('d-M-Y', strtotime($row['mj_date'])) : '-',
        'nama_cmj'   => $row['nama_cmj'],
        'curr'       => $row['curr'],
        'debit'      => number_format((float) $row['debit'], 2),
        'credit'     => number_format((float) $row['credit'], 2),
        'status'     => $rowStatus,
        'keterangan' => $row['keterangan'],
        'action'     => $action,
    ];
}

echo json_encode(['data' => $data]);
