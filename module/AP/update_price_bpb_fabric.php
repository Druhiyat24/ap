<?php
include '../../conn/conn.php';
// Pastikan sudah terhubung dengan database menggunakan $conn1

if (isset($_POST['updatedData'])) {
    $updatedData = $_POST['updatedData'];

    foreach ($updatedData as $data) {
        $id = $data['id'];
        $price = $data['price'];

        // Pastikan bahwa harga yang baru adalah angka dan valid
        if (is_numeric($price)) {
            // Update query untuk mengupdate harga berdasarkan ID
            $sql = "UPDATE whs_inmaterial_fabric_det 
                    SET price_update = '$price' 
                    WHERE id = '$id'";

            if (mysql_query($sql, $conn1)) {
                echo "Update successful for ID: $id";
            } else {
                echo "Error updating record: " . mysql_error($conn1);
            }
        }
    }
} else {
    echo "No data to update.";
}
?>
