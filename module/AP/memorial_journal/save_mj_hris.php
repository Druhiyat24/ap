<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try {

    file_put_contents('debug_log.txt', "\n\n=== NEW SAVE ===\n", FILE_APPEND);
    file_put_contents('debug_log.txt', print_r($_POST, true), FILE_APPEND);


    $mj_date = date('Y-m-d', strtotime($_POST['mj_date2']));
    $mj_type = $_POST['mj_type2'];
    $profit_center = $_POST['profit_center2'];
    $description = $_POST['pesan2'];
    $rate = $_POST['rate_mj2'] ?? 1;
    $fil_sb1 = $_POST['fil_sb1'];
    $rate = str_replace(',', '', $rate);


    $bulan = date('m', strtotime($mj_date));
    $tahun = date('y', strtotime($mj_date));
    $status = "Post";
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");
    $tgl_hris =  date("Y-m",strtotime($_POST['hris_date']));
    $tgl_hris_input =  date("Y-m-d",strtotime($_POST['hris_date']));

    $sqlcmj = mysqli_query($conn2, "select nama_cmj from master_category_mj where id_cmj = '$mj_type'");
    $rowcmj = mysqli_fetch_array($sqlcmj);
    $nama_cmj = $rowcmj['nama_cmj'];

    $prefix = "GM/NAG/" . $bulan . $tahun;

    $sql = mysqli_query($conn2, "
SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
FROM tbl_memorial_journal
WHERE no_mj LIKE '$prefix%'
FOR UPDATE
");

    $row = mysqli_fetch_assoc($sql);

    $urutan = ($row['max_urut'] ?? 0) + 1;

    $no_mj = $prefix . "/" . sprintf("%05d", $urutan);


    $sql_sb = mysqli_query($conn2, "
SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
FROM sb_memorial_journal
WHERE no_mj LIKE '$prefix%'
FOR UPDATE
");

    $row_sb = mysqli_fetch_assoc($sql_sb);

    $urutan_sb = ($row_sb['max_urut'] ?? 0) + 1;

    $no_mj_sb = $prefix . "/" . sprintf("%05d", $urutan_sb);

    if ($fil_sb1 == '1') {

        mysqli_query($conn2, "
INSERT INTO status_memorial_journal
(
no_mj, mj_date, no_mj_sb, status, create_by, create_date
)
VALUES
('$no_mj', '$mj_date', '$no_mj_sb', 'Post', '$user', '$create_date')
");
    }

    if ($mj_type == 'CMJ001') {
    mysqli_query($conn3, "update jurnal set no_journal = '$no_mj' where periode_payroll = '$tgl_hris'");
    }

if ($mj_type == 'CMJ003') {
    mysqli_query($conn3, "INSERT INTO log_jurnal_bpjs (no_journal, tgl_journal, status, created_by, created_date)
VALUES
('$no_mj', '$tgl_hris_input', 'POST', '$user', '$create_date')
");

}


    $detail = json_decode($_POST['detail'], true);

    if (!$detail) {
        throw new Exception("JSON detail error");
    }

    // 🔥 VALIDASI BALANCE
    $total_d = 0;
    $total_c = 0;

    foreach ($detail as $row) {
        $total_d += floatval($row['debit']);
        $total_c += floatval($row['credit']);
    }

    if (round($total_d, 2) != round($total_c, 2)) {
        throw new Exception("NOT BALANCE");
    }


    // 🔥 INSERT DETAIL
    foreach ($detail as $i => $row) {

        $pc_det        = $row['profit_center'];
        $no_coa        = $row['no_coa'];
        $nama_coa      = $row['nama_coa'];
        $no_cc         = $row['no_cc'];
        $cc_name       = $row['cc_name'];
        $reff          = $row['reff_number'];
        $reff_date     = $row['reff_date'];
        $buyer         = $row['buyer'];
        $ws            = $row['ws'];
        $curr          = $row['curr'];
        $debit         = floatval($row['debit']);
        $credit        = floatval($row['credit']);
        $ket           = $row['deskripsi'];
    

    mysqli_query($conn2, "
INSERT INTO tbl_memorial_journal
(
no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center
)
VALUES
('$no_mj', '$mj_date', '$mj_type', '$no_coa', '$no_cc', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '1', '$debit', '$credit', '$debit', '$credit', '$ket', '$status', '$user', '$create_date', '$pc_det')
");


        mysqli_query($conn2, "
INSERT INTO tbl_list_journal
(
no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
)
VALUES
('$no_mj', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_cc', '$cc_name', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '1', '$debit', '$credit', '$debit', '$credit', '$status', '$ket', '$user', '$create_date', '', '', '', '', '$pc_det')
");

        if ($fil_sb1 == '1') {

            mysqli_query($conn2, "
INSERT INTO sb_memorial_journal
(
no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center
)
VALUES
('$no_mj_sb', '$mj_date', '$mj_type', '$no_coa', '$no_cc', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '1', '$debit', '$credit', '$debit', '$credit', '$ket', '$status', '$user', '$create_date', 'Input SB2', '$pc_det')
");


   mysqli_query($conn2, "
INSERT INTO sb_list_journal
(
no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
)
VALUES
('$no_mj_sb', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_cc', '$cc_name', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '1', '$debit', '$credit', '$debit', '$credit', '$status', '$ket', '$user', '$create_date', '', '', '', '', '$pc_det')
");


        }
    }

    // 🔥 COMMIT (kalau semua sukses)
    mysqli_commit($conn1);

    file_put_contents('debug_log.txt', "\nCOMMIT SUCCESS\n", FILE_APPEND);

    echo json_encode([
        'status' => 'success',
        'no_journal' => $no_mj
    ]);
} catch (Exception $e) {

    // 🔥 ROLLBACK kalau ada error
    mysqli_rollback($conn1);

    file_put_contents('debug_log.txt', "\nROLLBACK\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "\nERROR: " . $e->getMessage(), FILE_APPEND);

    echo json_encode([
        'status' => 'error',
        'msg' => $e->getMessage()
    ]);
}
