<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$rvs_number   = $_POST['rvs_number'] ?? '';
$approve_user = $_POST['approve_user'] ?? '';
$approve_date = date("Y-m-d H:i:s");

if (!empty($rvs_number)) {

  $sql = "UPDATE ap_reverse_h 
  SET approve_by = '$approve_user', 
  approve_date = '$approve_date',
  status = 'APPROVED' 
  WHERE rvs_number = '$rvs_number'";

  $execute = mysqli_query($conn2, $sql);


  $sql_rvs = mysqli_query($conn1,"select rvs_number, doc_number, doc_date, nama_supp, curr, total, deskripsi from ap_reverse_det where rvs_number = '$rvs_number'"); 

  while($row= mysqli_fetch_assoc($sql_rvs)) { 
    $doc_number = $row['doc_number'];

    $sql2 = "UPDATE kontrabon_h 
    SET status = 'draft' 
    WHERE no_kbon = '$doc_number'";
    $execute2 = mysqli_query($conn2, $sql2);

    $sql3 = "UPDATE kontrabon_h_cbd 
    SET status = 'draft' 
    WHERE no_kbon = '$doc_number'";
    $execute3 = mysqli_query($conn2, $sql3);

    $sql4 = "UPDATE kontrabon_h_dp 
    SET status = 'draft' 
    WHERE no_kbon = '$doc_number'";
    $execute4 = mysqli_query($conn2, $sql4);

    $sql5 = "UPDATE kontrabon
    SET status = 'draft', status_int = '2' 
    WHERE no_kbon = '$doc_number'";
    $execute2 = mysqli_query($conn2, $sql5);

    $sql6 = "UPDATE kontrabon_cbd 
    SET status = 'draft', status_int = '2' 
    WHERE no_kbon = '$doc_number'";
    $execute3 = mysqli_query($conn2, $sql6);

    $sql7 = "UPDATE kontrabon_dp 
    SET status = 'draft', status_int = '2' 
    WHERE no_kbon = '$doc_number'";
    $execute4 = mysqli_query($conn2, $sql7);

    // CATATAN: dokumen kontrabon dikembalikan ke 'draft' (BUKAN Cancel), jadi jurnalnya
    // TIDAK dibalik di sini. Jurnal terbentuk lagi saat kontrabon di-approve ulang.
  }

  if ($execute) {
    echo "OK"; 
  } else {
    echo "Error: " . mysqli_error($conn2);
  }
} else {
  echo "Invalid data";
}

mysqli_close($conn2);
