<?php
include "../../../conn/conn.php";

/* ================= INPUT ================= */

$no_bk      = $_POST['no_bk'];
$search     = $_POST['search']['value'] ?? '';

$where = "WHERE 1=1 ";


$sql = "select no_journal, tgl_journal, a.no_coa, CONCAT(b.no_coa,' - ',b.nama_coa) nama_coa, a.curr, a.debit, a.rate,  a.keterangan, (a.debit * a.rate) debit_idr, a.credit_idr, a.profit_center, CONCAT(d.id_pc,' - ',nama_pc) nama_pc, no_cc, CONCAT(no_cc,' - ',cc_name) cc_name from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN b_master_cc c on c.no_cc  = a.no_costcenter LEFT JOIN master_pc d on d.kode_pc = a.profit_center where no_journal = '$no_bk' and debit != 0";

/* ================= EXECUTE ================= */

$q = mysqli_query($conn2, $sql);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */

echo json_encode([
    "data" => $data
]);
