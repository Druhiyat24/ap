<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$username = $_POST['username'];
$menu = $_POST['menu'];
$id = $_POST['id'];

if(isset($username)){
$sql = "delete from useraccess where username = '$username' and menu = '$menu'and id = '$id'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Delete Succesfully';
header('Refresh:0; url=userrole.php');
}

mysqli_close($conn2);

?>