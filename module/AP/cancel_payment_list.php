<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

// Cancel sebuah Payment List: header + semua baris detail dibatalkan, supaya
// Payment Voucher yang ada di dalamnya otomatis muncul lagi di pencarian
// form_payment-list.php (exclude hanya berlaku selama status detail != Cancel),
// dan status_pl di table sumbernya dikosongkan lagi. Cancel boleh selama PL
// masih Draft atau FIRST APPROVED - sama seperti cancelkbon.php dkk yang
// tersedia di halaman First DAN Second Approval kontrabon. Begitu SECOND
// APPROVED (final), tidak bisa dibatalkan lagi.

$pl_number = $_POST['pl_number'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date('Y-m-d H:i:s');

if (isset($pl_number)) {
    $pl_number_esc = mysqli_real_escape_string($conn2, $pl_number);

    $sqlCheck = mysqli_query($conn2, "select status from pv_payment_list_h where pl_number = '$pl_number_esc' limit 1");
    $rowCheck = mysqli_fetch_assoc($sqlCheck);

    if (!$rowCheck) {
        echo 'Error: Payment List tidak ditemukan';
        exit;
    }

    if ($rowCheck['status'] !== 'Draft' && $rowCheck['status'] !== 'FIRST APPROVED') {
        echo 'Error: Status saat ini ' . $rowCheck['status'] . ', tidak bisa dibatalkan';
        exit;
    }

    $query1 = mysqli_query($conn2, "update pv_payment_list_h set status = 'Cancel', cancel_by = '$cancel_user', cancel_date = '$cancel_date' where pl_number = '$pl_number_esc'");
    $query2 = mysqli_query($conn2, "update pv_payment_list_det set status = 'Cancel' where pl_number = '$pl_number_esc'");

    if ($query1 && $query2) {
        $sqlDet = mysqli_query($conn2, "select type_pv, no_kbon from pv_payment_list_det where pl_number = '$pl_number_esc'");
        while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
            updateStatusPl($conn2, $rowDet['type_pv'], $rowDet['no_kbon'], null);
        }

        echo 'Data Berhasil Di Cancel';
    } else {
        echo mysqli_error($conn2);
    }
} else {
    echo 'Error: pl_number is required';
}

mysqli_close($conn2);
?>
