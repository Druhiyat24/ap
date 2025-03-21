<?php
include '../../conn/conn.php'; // Koneksi ke database

if (isset($_POST['prof_ctr'])) {
    $profitCenter = mysqli_real_escape_string($conn2, $_POST['prof_ctr']);
    $sql = "SELECT no_cc AS value, cc_name AS text FROM b_master_cc WHERE profit_center = '$profitCenter' AND status = 'Active'";
    $result = mysqli_query($conn2, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data); // Mengembalikan data sebagai JSON
}
?>
