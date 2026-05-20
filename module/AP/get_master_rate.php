<?php
include '../../conn/conn.php';

header('Content-Type: application/json');

$response = [
    "status"  => "error",
    "message" => "Data tidak ditemukan",
    "data"    => null
];

if(isset($_POST['id']) && $_POST['id'] != ''){

    $id = mysql_real_escape_string($_POST['id']);

    $sql = "
        SELECT 
            v_idgroup,
            v_codecurr,
            curr,
            MIN(tanggal) AS tgl_awal,
            MAX(tanggal) AS tgl_akhir,
            TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate)) AS rate,
            TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate_jual)) AS rate_jual,
            TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate_beli)) AS rate_beli
        FROM ap_masterrate
        WHERE v_idgroup = '$id'
        GROUP BY v_idgroup, curr
        LIMIT 1
    ";

    $query = mysqli_query($conn2,$sql);

    if($query){

        if(mysql_num_rows($query) > 0){

            $data = mysql_fetch_assoc($query);

            $response["status"]  = "success";
            $response["message"] = "Data ditemukan";
            $response["data"]    = $data;

        } else {

            $response["message"] = "ID tidak ditemukan";

        }

    } else {

        $response["message"] = mysql_error();

    }
}

echo json_encode($response);
exit;
?>
