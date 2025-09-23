<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$rvs_number = $_POST['rvs_number'] ?? '';

if (!empty($rvs_number)) {
    $sql = "UPDATE ap_reverse_h 
            SET status = 'CANCEL' 
            WHERE rvs_number = '$rvs_number'";

    if (mysqli_query($conn2, $sql)) {
        echo "OK";
    } else {
        echo "Error: " . mysqli_error($conn2);
    }
} else {
    echo "Invalid data";
}

mysqli_close($conn2);
?>
