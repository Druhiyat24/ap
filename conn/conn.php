<?php
include "parser-php-version.php";

$servername = "10.10.5.2";
$user_name = "root";
$password = "ERP@S19n4lB1t";
$db = "signalbit_erp";

$server = "10.10.5.2";
$user = "root";
$pass = "ERP@S19n4lB1t";
$database = "signalbit_erp";

$server_hris = "10.10.5.111";
$user_hris = "root";
$pass_hris = "toor";
$database_hris = "hris_new";

$conn1 = mysql_connect($servername, $user_name, $password);
if (!$conn1) {
    die('Could not connect: ' . mysql_error());
}
$db_selected1 = mysql_select_db($db, $conn1);
if (!$db_selected1) {
    die ('Can\'t use signalbit_erp : ' . mysql_error());
}

$conn2 = mysql_connect($server, $user, $pass);
if (!$conn2) {
    die('Could not connect: ' . mysql_error());
}
$db_selected2 = mysql_select_db($database, $conn2);
if (!$db_selected2) {
    die ('Can\'t use sbv2 : ' . mysql_error());
}

$conn3 = mysql_connect($server_hris, $user_hris, $pass_hris);
if (!$conn3) {
    die('Could not connect: ' . mysql_error());
}
$db_selected3 = mysql_select_db($database_hris, $conn3);
if (!$db_selected3) {
    die ('Can\'t use hris : ' . mysql_error());
}
?>