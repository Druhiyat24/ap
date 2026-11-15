<?php
session_start();
include '../../conn/conn.php';
include '../../login_next.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
session_destroy();
// Datang dari sesi habis (ada ?next=): teruskan ke form login. Log-out biasa: tanpa next.
$next = login_next_aman(isset($_GET['next']) ? $_GET['next'] : '');
$tujuan = '../../' . ($next !== '' ? 'index.php?next=' . rawurlencode($next) : '');
echo "<script>window.location.href=" . json_encode($tujuan) . ";</script>";
?>