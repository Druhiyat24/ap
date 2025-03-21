<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$url = isset($_POST['url']) ? $_POST['url']: null;

    $resp = simplexml_load_file($url);
        echo json_encode($resp);    
?>