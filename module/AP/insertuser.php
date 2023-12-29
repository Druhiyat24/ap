<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$username = $_POST['username'];
$fullname = $_POST['fullname'];
$menu = $_POST['menu'];
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];
// echo $noftrdp;create_user
// echo $tglftrdp;
// echo $nama_supp;
// echo $no_pi;
// echo $curr;
// echo $create_date;
// echo $status;
// echo $create_user;
// echo $no_bpb;
// echo $tgl_bpb;
// echo $total;
// echo $dp_code;
// echo $dp_value;
// echo $balance;
if($menu == ''){
	echo '';
}else{	
$query = "INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
VALUES 
	('$username', '$fullname', '$menu', '$create_date', '$create_user')";
$execute = mysqli_query($conn2,$query);
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>