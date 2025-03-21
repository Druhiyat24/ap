<?php
include '../../conn/conn.php'; // Koneksi ke database

if (isset($_POST['supplier'])) {
    $supplier = mysqli_real_escape_string($conn2, $_POST['supplier']);
    $sql = "select DISTINCT supplier,pono, a.id from po_header a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier where supplier = '$supplier'";
    $result = mysqli_query($conn2, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data); // Mengembalikan data sebagai JSON
}
?>
