<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['nokontrabon'] ?? '';
$tgl_kbon = date("Y-m-d",strtotime($_POST['tanggal'])) ?? '';
$profit_center = $_POST['profit_center'] ?? '';
$supp_inv = $_POST['txt_inv'] ?? '';
$tgl_inv = date("Y-m-d",strtotime($_POST['txt_tglsi'])) ?? '';
$no_faktur = $_POST['no_faktur'] ?? '';
$tgl_tempo = date("Y-m-d",strtotime($_POST['txt_tgltempo'])) ?? '';


if ($no_kbon) {
	$sql = "UPDATE ap_edit_kontrabon_h SET tgl_kbon = '$tgl_kbon', profit_center = '$profit_center', supp_inv = '$supp_inv', tgl_inv = '$tgl_inv', no_faktur = '$no_faktur', tgl_tempo = '$tgl_tempo' WHERE no_kbon = '$no_kbon'";
	if (mysqli_query($conn2, $sql)) {
		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>