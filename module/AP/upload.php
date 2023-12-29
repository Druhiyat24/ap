<?php
require_once '../../conn/conn.php';

if(ISSET($_POST['submit'])){
	if($_FILES['upload']['name'] != "") {
		$file = $_FILES['upload'];
		$no_kbon = $_POST['no_kbon']
		
		$file_name = $file['name'];
		$file_temp = $file['tmp_name'];
		$name = explode('.', $file_name);
		$path = "files/".$file_name;
		
		$query = "INSERT INTO file (no_kbon, name, file) 
		VALUES 
				('$no_kbon', '$name[0]', '$path')";
		$execute = mysqli_query($conn2,$query);
		move_uploaded_file($file_temp, $path);
		
	}else{
		echo "<script>alert('Required Field!')</script>";
		echo "<script>window.location='index.php'</script>";
	}
}
?>