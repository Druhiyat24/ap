<?php
include "../../conn/conn.php";

mysqli_begin_transaction($conn2);

try{

    $no_mj       = mysqli_real_escape_string($conn2, $_POST['no_journal']);
    $mj_date     = mysqli_real_escape_string($conn2, date("Y-m-d",strtotime($_POST['tgl_journal'])));
    $nama_cmj    = mysqli_real_escape_string($conn2, $_POST['type_journal']);
    $status      = "Post";
    $create_user = mysqli_real_escape_string($conn2, $_POST['create_user']);
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
       VALIDASI BALANCE (pakai debit_idr/credit_idr, sesuai kolom
       yang memang ada di tbl_list_journal - bukan debit/credit
       mentah, karena rate bisa beda-beda per baris kalau
       currency-nya campuran)
    ============================== */

    $total_debit_idr = 0;
    $total_credit_idr = 0;

    foreach($rows as $r){

        $total_debit_idr  += $r['debit'] * $r['rate'];
        $total_credit_idr += $r['credit'] * $r['rate'];

    }

    if(round($total_debit_idr,2) != round($total_credit_idr,2)){
        throw new Exception("Debit IDR dan Credit IDR tidak balance");
    }

    /* =============================
       AMBIL NAMA COA & COST CENTER SEKALIGUS (batch, bukan query
       satu-satu per baris di dalam loop)
    ============================== */

    $coaCodes = array_unique(array_filter(array_column($rows, 'no_coa')));
    $ccCodes  = array_unique(array_filter(array_column($rows, 'no_costcenter')));

    $coaMap = [];
    if(!empty($coaCodes)){
        $escaped = array_map(function($c) use ($conn1){ return "'".mysqli_real_escape_string($conn1,$c)."'"; }, $coaCodes);
        $res = mysqli_query($conn1, "SELECT no_coa, nama_coa FROM mastercoa_v2 WHERE no_coa IN (".implode(',',$escaped).")");
        while($row = mysqli_fetch_assoc($res)){
            $coaMap[$row['no_coa']] = $row['nama_coa'];
        }
    }

    $ccMap = [];
    if(!empty($ccCodes)){
        $escaped = array_map(function($c) use ($conn1){ return "'".mysqli_real_escape_string($conn1,$c)."'"; }, $ccCodes);
        $res = mysqli_query($conn1, "SELECT no_cc, cc_name FROM b_master_cc WHERE no_cc IN (".implode(',',$escaped).")");
        while($row = mysqli_fetch_assoc($res)){
            $ccMap[$row['no_cc']] = $row['cc_name'];
        }
    }

    /* =============================
       SUSUN BARIS INSERT (bulk - satu INSERT multi-row per tabel,
       bukan satu query per baris)
    ============================== */

    $memorialValues = [];
    $listValues     = [];

    foreach($rows as $r){

        $no_coa        = mysqli_real_escape_string($conn2, $r['no_coa']);
        $profit_center = mysqli_real_escape_string($conn2, $r['profit_center']);
        $no_costcenter = mysqli_real_escape_string($conn2, $r['no_costcenter'] ?? '');

        $reff_doc   = mysqli_real_escape_string($conn2, $r['reff_doc'] ?? '');
        $reff_date  = !empty($r['reff_date'])
                      ? "'" . mysqli_real_escape_string($conn2, date("Y-m-d",strtotime($r['reff_date']))) . "'"
                      : "NULL";

        $buyer = mysqli_real_escape_string($conn2, $r['buyer'] ?? '');
        $no_ws = mysqli_real_escape_string($conn2, $r['no_ws'] ?? '');

        $curr = mysqli_real_escape_string($conn2, $r['curr']);
        $rate = (float)$r['rate'];

        $debit  = (float)$r['debit'];
        $credit = (float)$r['credit'];

        $debit_idr  = $debit * $rate;
        $credit_idr = $credit * $rate;

        $keterangan = mysqli_real_escape_string($conn2, $r['keterangan'] ?? '');

        $nama_coa = mysqli_real_escape_string($conn2, $coaMap[$r['no_coa']] ?? '');
        $nama_cc  = mysqli_real_escape_string($conn2, $ccMap[$r['no_costcenter']] ?? '-');

        if($id_cmj != '-'){
            $memorialValues[] = "('$no_mj','$mj_date','$id_cmj','$no_coa','$no_costcenter',
                '$reff_doc',$reff_date,'$buyer','$no_ws',
                '$curr','$rate','$debit','$credit',
                '$debit_idr','$credit_idr',
                '$keterangan','$status','$create_user','$create_date','$profit_center')";
        }

        $listValues[] = "('$no_mj','$mj_date','$nama_cmj',
            '$no_coa','$nama_coa',
            '$no_costcenter','$nama_cc',
            '$reff_doc',$reff_date,
            '$buyer','$no_ws',
            '$curr','$rate',
            '$debit','$credit',
            '$debit_idr','$credit_idr',
            '$status','$keterangan',
            '$create_user','$create_date',
            '', '',
            '', '',
            '$profit_center')";

    }

    /* =============================
       INSERT MEMORIAL JOURNAL (bulk)
    ============================== */

    if(!empty($memorialValues)){

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
        " . implode(',', $memorialValues);

        if(!mysqli_query($conn2,$query_memorial)){
            throw new Exception(mysqli_error($conn2));
        }
    }

    /* =============================
       INSERT LIST JOURNAL (bulk)
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
    " . implode(',', $listValues);

    if(!mysqli_query($conn2,$query_list)){
        throw new Exception(mysqli_error($conn2));
    }

    mysqli_commit($conn2);

    echo "success";

}catch(Exception $e){

    mysqli_rollback($conn2);

    echo $e->getMessage();

}
?>
