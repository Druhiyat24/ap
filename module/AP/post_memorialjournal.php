<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_mj = $_POST['no_mj'];
$post_date = date("Y-m-d H:i:s");
$post_user = $_POST['post_user'];


// echo $no_mj;
// echo "<-->";
// echo $post_date;
// echo "<-->";
// echo $post_user;

if(isset($no_mj)){
	
$sql = "update tbl_memorial_journal set post_date = '$post_date', post_by = '$post_user', status = 'Post' where no_mj = '$no_mj'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "update tbl_list_journal set approve_date = '$post_date', approve_by = '$post_user', status = 'Post' where no_journal = '$no_mj'";
$execute2 = mysqli_query($conn2,$sql2);

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Post';
header('Refresh:0; url=memorial-journal.php');
}

mysqli_close($conn2);

?>