<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

// Satu endpoint dipakai bersama oleh approve-payment-list-first.php dan
// approve-payment-list-second.php (lewat parameter stage), sama seperti
// ajx_approve-payment-voucher-ap.php dipakai bareng oleh first/second approval
// kontrabon - bedanya cuma status PL yang jadi syarat tampil di masing-masing tahap.

$stage = isset($_POST['stage']) ? $_POST['stage'] : 'first';
$targetStatus = $stage === 'second' ? 'FIRST APPROVED' : 'Draft';

$sql = mysqli_query($conn2, "select pl_number, pl_date, deskripsi, created_by, created_date,
    (select count(*) from pv_payment_list_det d where d.pl_number = h.pl_number and d.status != 'Cancel') as jumlah_pv
    from pv_payment_list_h h
    where h.status = '" . mysqli_real_escape_string($conn2, $targetStatus) . "'
    order by h.created_date asc");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $plNumber = $row['pl_number'];

    $data[] = [
        'select'       => '<input type="checkbox" class="pl-approve-chk" data-pl="' . htmlspecialchars($plNumber) . '">',
        'pl_number'    => $plNumber,
        'pl_date'      => !empty($row['pl_date']) ? date('d-M-Y', strtotime($row['pl_date'])) : '-',
        'deskripsi'    => $row['deskripsi'],
        'jumlah_pv'    => $row['jumlah_pv'],
        'created_by'   => $row['created_by'],
        'created_date' => !empty($row['created_date']) ? date('d-M-Y H:i:s', strtotime($row['created_date'])) : '-',
    ];
}

echo json_encode(['data' => $data]);
