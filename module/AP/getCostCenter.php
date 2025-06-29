<?php
include '../../conn/conn.php'; // Koneksi ke database

if (isset($_POST['prof_ctr'])) {
    $profitCenter = mysqli_real_escape_string($conn2, $_POST['prof_ctr']);
    $sql = "select no_cc AS value, concat(no_cc,' - ',cc_name) AS text from b_master_cc WHERE id_pc = '$profitCenter' AND status = 'Active'";
    $result = mysqli_query($conn2, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data); // Mengembalikan data sebagai JSON
}
?>
