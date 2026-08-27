<?php

include "../../../conn/conn.php";

$no_bk = $_POST['no_bk'] ?? '';

$rate = 0;
$debit = 0;
$debit_idr = 0;

if($no_bk!=''){

    // Baris ber-status 'Updated' = baris lama yang sudah digantikan (reverse).
    // Hitung amount hanya dari baris aktif (status != 'Updated') supaya jurnal
    // yang pernah di-reverse+buat-ulang tidak terhitung berlipat.
    $sql = mysqli_query($conn2,"select no_journal, tgl_journal, a.no_coa, CONCAT(b.no_coa,' - ',b.nama_coa) nama_coa, a.curr, a.keterangan, rate, SUM(a.debit) debit, SUM(a.credit) credit, sum(a.debit * a.rate) debit_idr, a.credit_idr, a.profit_center, CONCAT(d.id_pc,' - ',nama_pc) nama_pc, no_cc, CONCAT(no_cc,' - ',cc_name) cc_name from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN b_master_cc c on c.no_cc  = a.no_costcenter LEFT JOIN master_pc d on d.kode_pc = a.profit_center where no_journal = '$no_bk' and a.debit != 0 and a.status != 'Updated'");

    $row = mysqli_fetch_assoc($sql);

    $rate = $row['rate'] ?? 0;
    $debit = $row['debit'] ?? 0;
    $debit_idr = $row['debit_idr'] ?? 0;

}

echo json_encode([
    "rate"=>$rate,
    "debit"=>$debit,
    "debit_idr"=>$debit_idr
]);
?>