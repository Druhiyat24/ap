<html>
<head> 
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
</head>
<?php
include('conn/conn.php');
session_start();
//tangkap data dari form login
if (isset($_POST['user'])) { $username = $_POST['user']; } else { $username = ""; }
if (isset($_POST['pass'])) { $pass = $_POST['pass']; } else { $pass = ""; }
//untuk mencegah sql injection
//kita gunakan mysql_real_escape_string
$user = mysqli_real_escape_string($conn1,$username);
$pwd=$pass;

$q = mysqli_query($conn1,"select * from userpassword where username='".$user."' and password='".$pwd."'")or die(mysqli_error());
$result = mysqli_num_rows($q);
$data = mysqli_fetch_array($q);
if($result == 1)
{
	$_SESSION['username']= $data['username'];
	$_SESSION['FullName']= $data['FullName'];
	header("location:module/");
}
else
{
  echo "<script>alert('Gagal login: Cek username, password!');history.go(-1);</script>";
}	  

	
?>