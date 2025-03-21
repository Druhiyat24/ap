<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];

$query = "delete from memo_mapping_v2 where id = '$id' ";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>