<?PHP
error_reporting(0);
$host = "10.10.5.2"; #host for your database
$user = "root"; #username database
$pswd = "ERP@S19n4lB1t"; #password database
$db = "signalbit_erp"; #nama database
$con = mysql_connect($host, $user, $pswd); #for do connection to host
$con_new = new mysqli($host, $user, $pswd, $db);
$conn_li = mysqli_connect($host, $user, $pswd, $db);


if (!$con){ #jika koneksi gagal maka munculkan pesan error
echo "Koneksi Gagal Karena ".mysql_error();
}
else{ #kebalikannya (jika koneksi berhasil maka lakukan pemilhan database)
mysql_select_db($db);
date_default_timezone_set("Asia/Jakarta"); #untuk settingan default time zone (zona waktu yang di pakai)
}
?>