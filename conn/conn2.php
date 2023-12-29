<?php
include "parser-php-version.php";

$server = "10.10.5.60";
$user = "root";
$pass = "ERP@S19n4lB1t";
$database = "sb2";

$connect = mysql_connect($server, $user, $pass);
if (!$connect) {
    die('Could not connect: ' . mysql_error());
}
$db_selected = mysql_select_db($database, $connect);
if (!$db_selected) {
    die ('Can\'t use signalbit_erp : ' . mysql_error());
}
?>