<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment   = $_POST['no_payment'] ?? '';
$approve_user = $_POST['approve_user'] ?? '';
$approve_date = date("Y-m-d H:i:s");

if (!empty($no_payment)) {
  $sql = "UPDATE payment_ftr 
  SET approve_by = '$approve_user', 
  approve_date = '$approve_date', 
  status = 'Approved' 
  WHERE payment_ftr_id = '$no_payment'";

  $execute = mysqli_query($conn2, $sql);

  // $queryss = "INSERT INTO tbl_list_journal select '' id, a.payment_ftr_id, tgl_pelunasan, 'Payment' type_journal, a.no_coa, c.nama_coa, a.no_cc, d.cc_name, a.reff_doc, a.reff_date, '-' buyer, '-' ws, valuta_ftr curr, rate, a.t_debit, a.t_credit, (a.t_debit * rate) debit_idr, (a.t_credit * rate) credit_idr, 'Approved' status, a.deskripsi, b.create_user, b.create_date, '','','','','','', a.profit_center from payment_ftr_det a INNER JOIN payment_ftr b on b.payment_ftr_id = a.payment_ftr_id INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa LEFT JOIN b_master_cc d on d.no_cc = a.no_cc where a.payment_ftr_id = '$no_payment' GROUP BY a.id
  // UNION
  // select '' id, payment_ftr_id, tgl_pelunasan, 'Payment' type_journal, a.no_coa, b.nama_coa, '-' no_cc, '-' nama_cc, list_payment_id, tgl_list_payment, '-' buyer, '-' ws, valuta_ftr curr, rate, ttl_bayar debit, 0 credit, (ttl_bayar * rate) debit_idr, 0 credit_idr, 'Approved' status, '-' keterangan, create_user, create_date, '','','','','','', a.profit_center from payment_ftr a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where a.payment_ftr_id = '$no_payment'";

  $executess = mysqli_query($conn2,$queryss);

  if ($execute) {
    echo "OK"; 
  } else {
    echo "Error: " . mysqli_error($conn2);
  }
} else {
  echo "Invalid data";
}

mysqli_close($conn2);
