<?php
include '../../conn/conn.php';

$no_bankout = $_POST['no_bankout'] ?? '';
$response = ['tanggal' => ''];

if ($no_bankout !== '') {
    $query = "
        SELECT DATE_FORMAT(bankout_date, '%d-%m-%Y') AS tgl 
        FROM b_bankout_h 
        WHERE no_bankout = '$no_bankout'
        LIMIT 1
    ";
    $result = mysqli_query($conn2, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['tanggal'] = $row['tgl'];
    }
}

echo json_encode($response);
?>
