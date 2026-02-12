<?php
include "../../conn/conn.php";
session_start();
$user = $_POST['user'];

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = $_POST['search']['value'] ?? '';

$where = " WHERE a.user_input = '$user' ";
if ($search != '') {
    $where .= " AND (
        a.no_bpb LIKE '%$search%' OR
        a.nama_supp LIKE '%$search%' OR
        a.curr LIKE '%$search%'
    )";
}

$totalData = mysqli_fetch_row(mysqli_query($conn2,"
    SELECT COUNT(DISTINCT no_bpb)
    FROM tbl_bpb_temp
    WHERE user_input = '$user'
"))[0];

$sql = "
SELECT 
    a.no_bpb,
    a.tgl_bpb,
    a.nama_supp,
    a.curr,
    a.total,
    IF(a.no_inv IS NULL OR a.no_inv='', b.upt_no_inv, a.no_inv) AS no_inv,
    IF(a.no_inv IS NULL OR a.no_inv='', b.upt_tgl_inv, a.tgl_inv) AS tgl_inv,
    a.no_faktur,
    a.tgl_faktur
FROM (
    SELECT no_bpb,tgl_bpb,nama_supp,curr,total,no_inv,tgl_inv,no_faktur,tgl_faktur,user_input
    FROM tbl_bpb_temp
    GROUP BY no_bpb
) a
LEFT JOIN (
    SELECT no_bpb,upt_no_inv,upt_tgl_inv
    FROM bpb_new
    WHERE upt_no_inv IS NOT NULL
    GROUP BY no_bpb
) b ON b.no_bpb = a.no_bpb
where user_input = '$user' order by no_faktur asc
";

$query = mysqli_query($conn2, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($query)) {

    $no_inv = $row['no_inv'] ?: '-';
    $tgl_inv = ($no_inv == '-') ? '-' : date('d-M-Y', strtotime($row['tgl_inv']));

    $no_faktur = $row['no_faktur'] ?: '-';
    $tgl_faktur = ($no_faktur == '-') ? '-' : date('d-M-Y', strtotime($row['tgl_faktur']));

    if ($no_inv != '-' && $no_faktur != '-') {
    $row_color = 'row-ok';
} elseif ($no_inv != '-' || $no_faktur != '-') {
    $row_color = 'row-warning';
} else {
    $row_color = 'row-danger';
}

    $data[] = [
        'checkbox'   => '<input type="checkbox" id="select" name="select[]" class="select_item" value="" checked disabled>',
        'no_bpb'     => $row['no_bpb'],
        'tgl_bpb'    => date('d-M-Y', strtotime($row['tgl_bpb'])),
        'nama_supp'  => $row['nama_supp'],
        'curr'       => $row['curr'],
        'total'      => number_format($row['total'],2),
        'no_inv'     => $no_inv,
        'tgl_inv'    => $tgl_inv,
        'no_faktur'  => $no_faktur,
        'tgl_faktur' => $tgl_faktur,
        'action' => '
<button 
    type="button" 
    class="btn-xs btn-info btn-tambah"
    data-no_bpb="'.$row['no_bpb'].'"
    data-tgl_bpb="'.date('d-m-Y', strtotime($row['tgl_bpb'])).'"
>
<i class="fa fa-plus"></i> Add
</button>',
        'row_color'  => $row_color
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
]);
