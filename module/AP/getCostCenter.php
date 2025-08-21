<?php
// include '../../conn/conn.php'; // Koneksi ke database

// if (isset($_POST['prof_ctr'])) {
//     $profitCenter = mysqli_real_escape_string($conn2, $_POST['prof_ctr']);
//     $no_coa = mysqli_real_escape_string($conn2, $_POST['no_coa']);
//     $sql = "select no_cc AS value, concat(no_cc,' - ',cc_name) AS text from b_master_cc WHERE id_pc = '$profitCenter' AND status = 'Active'";
//     $result = mysqli_query($conn2, $sql);
    
//     $data = [];
//     while ($row = mysqli_fetch_assoc($result)) {
//         $data[] = $row;
//     }

//     echo json_encode($data); // Mengembalikan data sebagai JSON
// }

include '../../conn/conn.php'; // Koneksi ke database

if (isset($_POST['prof_ctr']) && isset($_POST['no_coa'])) {
    $profitCenter = mysqli_real_escape_string($conn2, $_POST['prof_ctr']);
    $no_coa = mysqli_real_escape_string($conn2, $_POST['no_coa']);

    // Ambil daftar group2 berdasarkan no_coa
    $sqlGroup = "
        SELECT TRIM(BOTH ',' FROM CONCAT(
            IF(support_gen_adm = 'Y','''SUPPORTING GENERAL & ADMINISTRATION'',',''),
            IF(support_prod = 'Y','''SUPPORTING PRODUCTION'',',''),
            IF(prod = 'Y','''PRODUCTION'',',''),
            IF(support_sell = 'Y','''SUPPORTING SELLING'',','')
        )) AS groups
        FROM mastercoa_v2 
        WHERE no_coa = '$no_coa'
    ";

    $resGroup = mysqli_query($conn2, $sqlGroup);
    $rowGroup = mysqli_fetch_assoc($resGroup);

    $groupFilter = $rowGroup['groups']; // hasil string: 'PRODUCTION','SUPPORTING SELLING'

    // Query utama ke CC dengan filter group2
    $sql = "
        SELECT no_cc AS value, CONCAT(no_cc,' - ',cc_name) AS text 
        FROM b_master_cc 
        WHERE id_pc = '$profitCenter' 
          AND status = 'Active'
          AND group2 IN ($groupFilter)
    ";

    $result = mysqli_query($conn2, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data); // Mengembalikan data sebagai JSON
}
?>
