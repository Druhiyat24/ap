<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

$response = ["status"=>"error","message"=>"Gagal update"];

if($_POST){

    $id = date('YmdHis') . substr(microtime(), 2, 4);
    $curr       = $_POST['curr'];
    $tgl_awal   = $_POST['tgl_awal'];
    $tgl_akhir  = $_POST['tgl_akhir'];
    $rate       = $_POST['rate'];
    $rate_jual  = $_POST['rate_jual'];
    $rate_beli  = $_POST['rate_beli'];
    $v_codecurr  = $_POST['v_codecurr'];
    $create_user  = $_POST['create_user'];
    $last_update = date("Y-m-d H:i:s");

    $query = "INSERT INTO log_masterrate (v_idgroup, username, activity, updated_at) 
VALUES 
    ('$id', '$create_user', 'INSERT', '$last_update')";

$execute = mysqli_query($conn2,$query);

    // ubah format dd-mm-yyyy ke yyyy-mm-dd
    function convert($tgl){
        $p = explode('-', $tgl);
        return $p[2]."-".$p[1]."-".$p[0];
    }

    $start = convert($tgl_awal);
    $end   = convert($tgl_akhir);

    mysqli_begin_transaction($conn2);

try {

    // ==============================
    // 1. KUMPULKAN SEMUA TANGGAL DULU
    // ==============================

    $current = strtotime($start);
    $endDate = strtotime($end);

    $tanggalList = [];

    while($current <= $endDate){
        $tanggalList[] = date("Y-m-d", $current);
        $current = strtotime("+1 day", $current);
    }

    $tanggalString = "'" . implode("','", $tanggalList) . "'";

    // ==============================
    // 2. CEK APAKAH ADA BENTROK
    // ==============================

    $cek = mysqli_query($conn2, "
        SELECT tanggal 
        FROM ap_masterrate
        WHERE v_codecurr = '$v_codecurr'
        AND curr = '$curr'
        AND tanggal IN ($tanggalString)
        AND v_idgroup != '$id'
    ");

    if(mysqli_num_rows($cek) > 0){

        $tanggalBentrok = [];
        while($row = mysqli_fetch_assoc($cek)){
            $tanggalBentrok[] = $row['tanggal'];
        }

        $list = implode(", ", $tanggalBentrok);

        throw new Exception("Tanggal $list untuk type $v_codecurr dan currency $curr sudah ada");
    }


    // ==============================
    // 4. DELETE DATA LAMA
    // ==============================

    mysqli_query($conn2, "DELETE FROM masterrate WHERE v_idgroup = '$id'");
    mysqli_query($conn2, "DELETE FROM ap_masterrate WHERE v_idgroup = '$id'");

    // ==============================
    // 5. INSERT PER TANGGAL
    // ==============================

    foreach($tanggalList as $tanggal){

        $insert1 = "
            INSERT INTO ap_masterrate
            (v_codecurr, v_idgroup, tanggal, curr, rate, rate_jual, rate_beli, v_lastupdate, n_codeperiode)
            VALUES
            ('$v_codecurr','$id','$tanggal','$curr','$rate','$rate_jual','$rate_beli','$create_user','0')
        ";

        if(!mysqli_query($conn2, $insert1)){
            throw new Exception(mysqli_error($conn2));
        }

        $insert2 = "
            INSERT INTO masterrate
            (v_codecurr, v_idgroup, tanggal, curr, rate, rate_jual, rate_beli, v_lastupdate, n_codeperiode)
            VALUES
            ('$v_codecurr','$id','$tanggal','$curr','$rate','$rate_jual','$rate_beli','$create_user','0')
        ";

        if(!mysqli_query($conn2, $insert2)){
            throw new Exception(mysqli_error($conn2));
        }
    }

    mysqli_commit($conn2);

    $response["status"]  = "success";
    $response["message"] = "Master rate berhasil diupdate";

} catch (Exception $e){

    mysqli_rollback($conn2);
    $response["status"]  = "error";
    $response["message"] = $e->getMessage();
}

}

echo json_encode($response);
