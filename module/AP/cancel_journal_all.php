<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

// ambil dari AJAX
$no_mj = $_POST['no_journal']; // mapping dari JS
$cancel_user = $_POST['cancel_user']; // kirim dari JS juga
$cancel_date = date("Y-m-d H:i:s");

if(isset($no_mj)){

    $sql = "update tbl_memorial_journal set cancel_date = '$cancel_date', cancel_by = '$cancel_user', status = 'Cancel' where no_mj = '$no_mj'";
    $execute = mysqli_query($conn2,$sql);

    $sqlsb = "update sb_memorial_journal a INNER JOIN status_memorial_journal b ON b.no_mj_sb = a.no_mj SET a.cancel_date = '$cancel_date', a.cancel_by = '$cancel_user', a.status = 'Cancel' where b.no_mj = '$no_mj'";
    $executesb = mysqli_query($conn2,$sqlsb);

    $sql_upt = "update jurnal set no_journal = NULL where no_journal = '$no_mj'";
    $execute_upt = mysqli_query($conn3,$sql_upt);

    $sql_upt2 = "update log_jurnal_bpjs set status = 'CANCEL' where no_journal = '$no_mj'";
    $execute_upt2 = mysqli_query($conn3,$sql_upt2);

    $sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_mj')";
    $query2 = mysqli_query($conn2,$sql2);

    $sql4 = "update status_memorial_journal set status = 'Cancel' where no_mj = '$no_mj'";
    $execute4 = mysqli_query($conn2,$sql4);

    if(!$query2) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal insert cancel log'
        ]);
        exit;
    } else {

        $sql3 = "Delete from tbl_list_journal where no_journal='$no_mj'";
        $query3 = mysqli_query($conn2,$sql3);

        $sql3_sb = "Delete a FROM sb_list_journal a INNER JOIN status_memorial_journal b ON b.no_mj_sb = a.no_journal WHERE b.no_mj = '$no_mj'";
        $query3_sb = mysqli_query($conn2,$sql3_sb);

        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dicancel'
        ]);
        exit;
    }

}else{
    echo json_encode([
        'status' => 'error',
        'message' => 'No MJ tidak ditemukan'
    ]);
    exit;
}

mysqli_close($conn2);
?>