<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$no_kbon_det = $_POST['no_kbon_det'];
$cicilan_ke = (int) $_POST['cicilan_ke'];
$total_cicilan = (int) $_POST['total_cicilan'];
$top = isset($_POST['top']) && $_POST['top'] !== '' ? (int) $_POST['top'] : 'NULL';
$tgl_tempo = !empty($_POST['tgl_tempo']) ? date("Y-m-d", strtotime($_POST['tgl_tempo'])) : 'NULL';
$dpp = isset($_POST['dpp']) && $_POST['dpp'] !== '' ? $_POST['dpp'] : 0;
$ppn = isset($_POST['ppn']) && $_POST['ppn'] !== '' ? $_POST['ppn'] : 0;
$pph = isset($_POST['pph']) && $_POST['pph'] !== '' ? $_POST['pph'] : 0;
$ro = isset($_POST['ro']) && $_POST['ro'] !== '' ? $_POST['ro'] : 0;
$ftr = isset($_POST['ftr']) && $_POST['ftr'] !== '' ? $_POST['ftr'] : 0;
$potongan = isset($_POST['potongan']) && $_POST['potongan'] !== '' ? $_POST['potongan'] : 0;
$total = isset($_POST['total']) && $_POST['total'] !== '' ? $_POST['total'] : 0;
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

if ($tgl_tempo !== 'NULL') {
    $tgl_tempo = "'$tgl_tempo'";
}

$query = "INSERT INTO kontrabon_h_installment_detail (no_kbon, no_kbon_det, cicilan_ke, total_cicilan, top, tgl_tempo, dpp, ppn, pph, ro, ftr, potongan, total, status, create_user, create_date)
VALUES
('$no_kbon', '$no_kbon_det', '$cicilan_ke', '$total_cicilan', $top, $tgl_tempo, '$dpp', '$ppn', '$pph', '$ro', '$ftr', '$potongan', '$total', 'draft', '$create_user', '$create_date')";
$execute = mysqli_query($conn2, $query);

if ($execute) {
    echo 'OK';
} else {
    echo mysqli_error($conn2);
}

mysqli_close($conn2);
?>
