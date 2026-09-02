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

    $sql2 = "UPDATE payment_ftr 
    SET status = 'draft' 
    WHERE payment_ftr_id = '$doc_number'";

    $execute2 = mysqli_query($conn2, $sql2);

    $sql3 = "UPDATE payment_ftrdp 
    SET status = 'draft' 
    WHERE payment_ftr_id = '$doc_number'";

    $execute3 = mysqli_query($conn2, $sql3);

    $sql4 = "UPDATE payment_ftrcbd 
    SET status = 'draft' 
    WHERE payment_ftr_id = '$doc_number'";

    $execute4 = mysqli_query($conn2, $sql4);

    // CATATAN: dokumen payment dikembalikan ke 'draft' (BUKAN Cancel), jadi jurnalnya
    // TIDAK dibalik di sini. Jurnal terbentuk lagi saat payment di-approve ulang.
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
