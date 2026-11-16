<?php
/* Kurs PAJAK untuk satu tanggal. Dipanggil AJAX dari form Book Transfer. */

include '../../conn/conn.php';
include 'cash_projection/cfp_lib.php';
header('Content-Type: application/json');

$tanggal = isset($_GET['tanggal']) && $_GET['tanggal'] != '' ? $_GET['tanggal'] : date('Y-m-d');
$curr    = isset($_GET['curr']) ? $_GET['curr'] : 'USD';

$kurs = cfp_kurs_pajak($conn1, $tanggal, $curr);
$kurs['tanggal_tampil'] = date('d M Y', strtotime($kurs['tanggal']));

echo json_encode($kurs);
