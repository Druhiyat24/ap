<?php
include "../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$profit_center = $_POST['profit_center'];
$no_coa = $_POST['no_coa'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$search = $_POST['search']['value'] ?? '';
$where = "WHERE 1=1 ";

if ($profit_center != 'ALL') {
    $profit_center = "and kode_pc = '$profit_center'";
}else{
    $profit_center = "";
}

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            profit_center LIKE '%$search%' OR
            no_journal LIKE '%$search%' OR
            deskripsi LIKE '%$search%' OR
            supplier LIKE '%$search%' OR
            no_kbon LIKE '%$search%'
        )
    ";
}


$sql = "WITH

bpb as (select bpbno_int, a.id_supplier, b.supplier from bpb a inner join mastersupplier b on b.id_supplier = a.id_supplier where bpbdate >= '2025-01-01' and cancel != 'Y'
UNION
select bppbno_int, a.id_supplier, b.supplier from bppb a inner join mastersupplier b on b.id_supplier = a.id_supplier where bppbdate >= '2025-01-01' and cancel != 'Y'),

saldo_awal as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, profit_center kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, round(total,2) total from acc_saldo_awal_prepaid_tax a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.no_coa = '$no_coa'),

data_in as (select tgl_journal, no_journal, '-' no_kbon, a.keterangan, c.supplier, kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN bpb c on c.bpbno_int = a.no_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - BPB%' GROUP BY no_journal),

data_in_before as (select tgl_journal, no_journal, '-' no_kbon, a.keterangan, c.supplier, kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN bpb c on c.bpbno_int = a.no_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - BPB%' GROUP BY no_journal),

data_out as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - Kontrabon%' GROUP BY reff_doc),

data_out_before as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - Kontrabon%' GROUP BY reff_doc),

data_gm as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%ACCOUNT PAYABLE%' and no_journal like '%GM%' GROUP BY reff_doc),

data_gm_before as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%ACCOUNT PAYABLE%' and no_journal like '%GM%' GROUP BY reff_doc),

saldo_in as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, sum(saldo_awal) saldo_awal, sum(total_in) total_in from (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, total saldo_awal, 0 total_in from saldo_awal
UNION ALL
select tgl_journal, no_journal, no_kbon, keterangan, supplier, kode_pc, profit_center, total saldo_awal, 0 total_in from data_in_before
UNION ALL
select tgl_journal, no_journal, no_kbon, keterangan, supplier, kode_pc, profit_center, 0 saldo_awal, total total_in from data_in) a GROUP BY no_journal),

saldo_out as (select reff_doc, no_coa, sum(total_out_before) total_out_before, sum(total_out) total_out , sum(total_gm_before) total_gm_before, sum(total_gm) total_gm from (select reff_doc, no_coa, 0 total_out_before, total total_out, 0 total_gm_before, 0 total_gm from data_out
UNION ALL
select reff_doc, no_coa, total total_out_before, 0 total_out, 0 total_gm_before, 0 total_gm from data_out_before
UNION ALL
select reff_doc, no_coa, 0 total_out_before, 0 total_out, 0 total_gm_before, total total_gm from data_gm
UNION ALL
select reff_doc, no_coa, 0 total_out_before, 0 total_out, total total_gm_before, 0 total_gm from data_gm_before) a GROUP BY reff_doc),

mutasi as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, (saldo_awal + COALESCE(total_out_before,0) + COALESCE(total_gm_before,0)) saldo_awal, total_in, COALESCE(total_out,0) total_out, COALESCE(total_gm,0) total_gm from saldo_in a left join saldo_out b on b.reff_doc = a.no_journal)

select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, saldo_awal, total_in, total_out, total_gm, (saldo_awal + total_in + total_out + total_gm) saldo_akhir, '' remark from mutasi $where $profit_center
";

$sql_count = "SELECT COUNT(*) total FROM ($sql) x";
$q_count = mysqli_query($conn2, $sql_count);
$row = mysqli_fetch_assoc($q_count);
$recordsTotal = intval($row['total']);
$recordsFiltered = $recordsTotal;

/* ================= DATA + LIMIT ================= */
$sql_limit = $sql . " LIMIT $start, $length";
$q = mysqli_query($conn2, $sql_limit);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

$sql_total = "
SELECT
    SUM(saldo_awal) saldo_awal,
    SUM(total_in) total_in,
    SUM(total_out) total_out,
    SUM(total_gm) total_gm,
    SUM(saldo_akhir) saldo_akhir
FROM (
    $sql
) x
";

$q_total = mysqli_query($conn2, $sql_total);
$footer = mysqli_fetch_assoc($q_total);


/* ================= RESPONSE ================= */
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data,
    "footer" => $footer
]);