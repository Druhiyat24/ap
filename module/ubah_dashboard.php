<?php
include '../conn/conn.php';

$id_dsb = isset($_POST['id_dsb']) ? $_POST['id_dsb']: null;

if ($id_dsb == '80') {
    include '../dashboard/dashboard-ap.php';  
}elseif($id_dsb == '81'){
    include '../dashboard/dashboard-bank.php';  
}

?>