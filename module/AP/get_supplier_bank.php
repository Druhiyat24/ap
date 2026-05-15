<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

if(isset($_POST['supplier'])){

    $supplier = mysqli_real_escape_string($conn1, $_POST['supplier']);

    $query = mysqli_query($conn1, "select bank_name, bank_account, beneficiary_name from mastersupplier where tipe_sup = 'S' and supplier = '$supplier' LIMIT 1");

    if(mysqli_num_rows($query) > 0){

        $row = mysqli_fetch_assoc($query);

        echo json_encode([
            'status' => 'success',
            'bank_name' => $row['bank_name'],
            'bank_account' => $row['bank_account']
        ]);

    }else{

        echo json_encode([
            'status' => 'error'
        ]);
    }

}
