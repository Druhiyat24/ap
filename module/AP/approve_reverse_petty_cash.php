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

    $sql2 = "UPDATE c_petty_cashout_h 
    SET status = 'Draft' 
    WHERE no_pco = '$doc_number'";

    $execute2 = mysqli_query($conn2, $sql2);

    $sql3 = "UPDATE c_petty_cashin_h 
    SET status = 'Draft' 
    WHERE no_pci = '$doc_number'";

    $execute3 = mysqli_query($conn2, $sql3);

    // CATATAN: dokumen petty cash dikembalikan ke 'Draft' (BUKAN Cancel), jadi jurnalnya
    // TIDAK dibalik di sini. Jurnal terbentuk lagi saat petty cash di-approve ulang.
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
