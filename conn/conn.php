<?php
include "parser-php-version.php";

$servername = "10.10.5.12";
$user_name = "root";
$password = "ERP@S19n4lB1t";
$db = "signalbit_erp";

$server = "10.10.5.12";
$user = "root";
$pass = "ERP@S19n4lB1t";
$database = "signalbit_erp";

// $servername = "10.10.5.2:3307";
// $user_name = "root";
// $password = "ERP@S19n4lB1t";
// $db = "signalbit_erp";

// $server = "10.10.5.2:3307";
// $user = "root";
// $pass = "ERP@S19n4lB1t";
// $database = "signalbit_erp";

$port = '3307';

// $servername = "localhost";
// $user_name = "root";
// $password = "";
// $db = "signalbit_erp";

// $server = "localhost";
// $user = "root";
// $pass = "";
// $database = "signalbit_erp";

$server_hris = "10.10.5.111";
$user_hris = "root";
$pass_hris = "95*76s^SAl8a";
$database_hris = "hris_nag";

// $server_hris = "10.10.5.111";
// $user_hris = "root";
// $pass_hris = "ERP@S19n4lB1t";
// $database_hris = "hris_nag";

$host_pg = "10.10.5.62";
$port_pg = "5432";
$dbname_pg = "alabare_knitting";
$user_pg = "postgres";
$password_pg = "@P05t6r3_N@6";

$conn4 = pg_connect("host=$host_pg port=$port_pg dbname=$dbname_pg user=$user_pg password=$password_pg");

if (!$conn4) {
    die("Could not connect: " . pg_last_error());
}

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