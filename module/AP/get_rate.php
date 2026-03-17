<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');



$valuta = $_POST['valuta'];
$tgl    = date("Y-m-d",strtotime($_POST['doc_date']));

$sql = "
    SELECT TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate)) rate 
    FROM ap_masterrate
    WHERE curr = '$valuta'
    AND tanggal = '$tgl'
    LIMIT 1
";

$q = mysqli_query($conn2, $sql);
$data = mysqli_fetch_assoc($q);

if($data){
    echo json_encode([
        'status' => 'ok',
        'rate'   => $data['rate']
    ]);
}else{
    echo json_encode([
        'status' => 'fail'
    ]);
}

?>