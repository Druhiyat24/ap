<?php
include '../../../conn/conn.php';

$no_pv = $_POST['no_pv'];

$sql = mysqli_query($conn2,"select kode_pc, CONCAT(id_pc,' - ',nama_pc) nama_pc, b.bank_account, b.bank_name, b.curr, b.b_code from tbl_pv_h a INNER JOIN b_masterbank b on b.bank_account = a.frm_akun INNER JOIN master_pc c on c.kode_pc = b.profit_center_bank where no_pv = '$no_pv'");

$row = mysqli_fetch_assoc($sql);

echo json_encode([
    'account'        => $row['bank_account'],
    'bank'           => $row['bank_name'],
    'currency'       => $row['curr'],
    'nama_pc'        => $row['nama_pc'],
    'profit_center'  => $row['kode_pc'],
    'b_code'         => $row['b_code']
]);
