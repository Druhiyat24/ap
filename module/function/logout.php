<?php
session_start();
include '../../conn/conn.php';
$user=$_SESSION['username'];
session_destroy();
echo "<script>window.location.href='../../';</script>";
?>