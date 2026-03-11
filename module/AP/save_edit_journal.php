<?php
include "../../conn/conn.php";

mysqli_begin_transaction($conn2);

try{

    $no_mj   = $_POST['no_journal'];
    $mj_date = date("Y-m-d",strtotime($_POST['tgl_journal']));
    $nama_cmj  = $_POST['type_journal'];
    $status  = "Post";
    $create_user = $_POST['create_user'];
    $create_date = date("Y-m-d H:i:s");

    $rows = json_decode($_POST['rows'], true);

    if(!$rows){
        throw new Exception("Data rows kosong");
    }

    $query_log = "INSERT INTO tbl_log_edit_mj (no_mj,user_edit,tgl_edit) 
VALUES 
    ('$no_mj', '$create_user', '$create_date')";

    if(!mysqli_query($conn2,$query_log)){
        throw new Exception(mysqli_error($conn2));
    }

    /* =============================
       AMBIL NAMA CATEGORY MJ
    ============================== */

    $sqlcmj = mysqli_query($conn1,"
        SELECT id_cmj 
        FROM master_category_mj 
        WHERE nama_cmj = '$nama_cmj'
    ");

    $rowcmj = mysqli_fetch_assoc($sqlcmj);
    $id_cmj = $rowcmj['id_cmj'] ?? '-';

    /* =============================
       BACKUP DATA LAMA
    ============================== */

    $sql_backup = "
        INSERT INTO tbl_list_journal_cancel
        SELECT * 
        FROM tbl_list_journal 
        WHERE no_journal = '$no_mj'
    ";

    if(!mysqli_query($conn2,$sql_backup)){
        throw new Exception(mysqli_error($conn2));
    }

    /* =============================
       DELETE DATA LAMA
    ============================== */

    $sql_delete = "
        DELETE FROM tbl_list_journal 
        WHERE no_journal = '$no_mj'
    ";

    if(!mysqli_query($conn2,$sql_delete)){
        throw new Exception(mysqli_error($conn2));
    }

    if($id_cmj != '-'){

    $sql_update2 = "insert into tbl_edit_mj (select * from tbl_memorial_journal where no_mj = '$no_mj')";

    if(!mysqli_query($conn2,$sql_update2)){
        throw new Exception(mysqli_error($conn2));
    }

    $sql_delete2 = "delete from tbl_memorial_journal where no_mj = '$no_mj'";

    if(!mysqli_query($conn2,$sql_delete2)){
        throw new Exception(mysqli_error($conn2));
    }
}

    /* =============================
       VALIDASI BALANCE
    ============================== */

    $total_debit = 0;
    $total_credit = 0;

    foreach($rows as $r){

        $total_debit  += $r['debit'];
        $total_credit += $r['credit'];

    }

    if($total_debit != $total_credit){
        throw new Exception("Debit dan Credit tidak balance");
    }

    /* =============================
       INSERT DATA BARU
    ============================== */

    foreach($rows as $r){

        $no_coa = $r['no_coa'];
        $profit_center = $r['profit_center'];
        $no_costcenter = $r['no_costcenter'];

        $reff_doc  = $r['reff_doc'];

        $reff_date = !empty($r['reff_date']) 
                     ? date("Y-m-d",strtotime($r['reff_date'])) 
                     : NULL;

        $buyer = $r['buyer'];
        $no_ws = $r['no_ws'];

        $curr = $r['curr'];
        $rate = $r['rate'];

        $debit  = $r['debit'];
        $credit = $r['credit'];

        $debit_idr  = $debit * $rate;
        $credit_idr = $credit * $rate;

        $keterangan = $r['keterangan'];

        /* =============================
           AMBIL NAMA COA
        ============================== */

        $sqlcoa = mysqli_query($conn1,"
            SELECT nama_coa 
            FROM mastercoa_v2 
            WHERE no_coa = '$no_coa'
        ");

        $rowcoa = mysqli_fetch_assoc($sqlcoa);
        $nama_coa = $rowcoa['nama_coa'];

        /* =============================
           AMBIL NAMA COST CENTER
        ============================== */

        $sqlcc = mysqli_query($conn1,"
            SELECT cc_name 
            FROM b_master_cc 
            WHERE no_cc = '$no_costcenter'
        ");

        $rowcc = mysqli_fetch_assoc($sqlcc);
        $nama_cc = $rowcc['cc_name'] ?? '-';

        /* =============================
           INSERT MEMORIAL JOURNAL
        ============================== */
        if($id_cmj != '-'){
        $query_memorial = "
        INSERT INTO tbl_memorial_journal
        (
            no_mj, mj_date, id_cmj, no_coa, no_costcenter,
            no_reff, reff_date, buyer, no_ws,
            curr, rate, debit, credit,
            debit_idr, credit_idr,
            keterangan, status, create_by, create_date, profit_center
        )
        VALUES
        (
            '$no_mj', '$mj_date', '$id_cmj', '$no_coa', '$no_costcenter',
            '$reff_doc', '$reff_date', '$buyer', '$no_ws',
            '$curr', '$rate', '$debit', '$credit',
            '$debit_idr', '$credit_idr',
            '$keterangan', '$status', '$create_user', '$create_date', '$profit_center'
        )
        ";

        if(!mysqli_query($conn2,$query_memorial)){
            throw new Exception(mysqli_error($conn2));
        }
    }

        /* =============================
           INSERT LIST JOURNAL
        ============================== */

        $query_list = "
        INSERT INTO tbl_list_journal
        (
            no_journal, tgl_journal, type_journal,
            no_coa, nama_coa,
            no_costcenter, nama_costcenter,
            reff_doc, reff_date,
            buyer, no_ws,
            curr, rate,
            debit, credit,
            debit_idr, credit_idr,
            status, keterangan,
            create_by, create_date,
            approve_by, approve_date,
            cancel_by, cancel_date,
            profit_center
        )
        VALUES
        (
            '$no_mj', '$mj_date', '$nama_cmj',
            '$no_coa', '$nama_coa',
            '$no_costcenter', '$nama_cc',
            '$reff_doc', '$reff_date',
            '$buyer', '$no_ws',
            '$curr', '$rate',
            '$debit', '$credit',
            '$debit_idr', '$credit_idr',
            '$status', '$keterangan',
            '$create_user', '$create_date',
            '', '',
            '', '',
            '$profit_center'
        )
        ";

        if(!mysqli_query($conn2,$query_list)){
            throw new Exception(mysqli_error($conn2));
        }

    }

    mysqli_commit($conn2);

    echo "success";

}catch(Exception $e){

    mysqli_rollback($conn2);

    echo $e->getMessage();

}
?>
