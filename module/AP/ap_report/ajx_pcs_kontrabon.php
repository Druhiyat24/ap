<?php
include "../../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$search = $_POST['search']['value'] ?? '';
$nama_supp = $_POST['nama_supp'];
$where = "WHERE 1=1 ";

if ($nama_supp != 'ALL') {
    $supplier = "and supplier = '$nama_supp'";
}else{
    $supplier = "";
}

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
    AND (
    supplier LIKE '%$search%' OR
    no_kbon LIKE '%$search%' OR
    curr LIKE '%$search%' OR
    nama_coa LIKE '%$search%' OR
    item_type1 LIKE '%$search%' OR
    item_type2 LIKE '%$search%' OR
    relasi LIKE '%$search%'
    )
    ";
}


$sql = "WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in  + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi $where $supplier
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
SUM(pph) pph,
SUM(uang_muka) uang_muka,
SUM(potongan) potongan,
SUM(ded_lp) ded_lp,
SUM(ded_gm) ded_gm,
SUM(reverse_kontrabon) reverse_kontrabon,
SUM(saldo_akhir) saldo_akhir,
SUM(saldo_akhir_idr) saldo_akhir_idr,
SUM(due_current) due_current,
SUM(due_1_30) due_1_30,
SUM(due_31_60) due_31_60,
SUM(due_61_90) due_61_90,
SUM(due_91_120) due_91_120,
SUM(due_121_180) due_121_180,
SUM(due_181_360) due_181_360,
SUM(due_gt_360) due_gt_360,
SUM(total_due) total_due,
SUM(pro_due) pro_due,
SUM(pro_due0) pro_due0,
SUM(pro_due1) pro_due1,
SUM(pro_due2) pro_due2,
SUM(pro_due3) pro_due3,
SUM(pro_due4) pro_due4,
SUM(pro_due5) pro_due5,
SUM(tot_produe) tot_produe
FROM (
$sql
) x
";

$q_total = mysqli_query($conn2, $sql_total);
$footer = mysqli_fetch_assoc($q_total);


$sql_total_idr = "
SELECT
SUM(saldo_awal) saldo_awal,
SUM(total_in) total_in,
SUM(pph) pph,
SUM(uang_muka) uang_muka,
SUM(potongan) potongan,
SUM(ded_lp) ded_lp,
SUM(ded_gm) ded_gm,
SUM(reverse_kontrabon) reverse_kontrabon,
SUM(saldo_akhir) saldo_akhir,
SUM(saldo_akhir_idr) saldo_akhir_idr,
SUM(due_current) due_current,
SUM(due_1_30) due_1_30,
SUM(due_31_60) due_31_60,
SUM(due_61_90) due_61_90,
SUM(due_91_120) due_91_120,
SUM(due_121_180) due_121_180,
SUM(due_181_360) due_181_360,
SUM(due_gt_360) due_gt_360,
SUM(total_due) total_due,
SUM(pro_due) pro_due,
SUM(pro_due0) pro_due0,
SUM(pro_due1) pro_due1,
SUM(pro_due2) pro_due2,
SUM(pro_due3) pro_due3,
SUM(pro_due4) pro_due4,
SUM(pro_due5) pro_due5,
SUM(tot_produe) tot_produe
FROM (
$sql
) x where curr = 'IDR'
";

$q_total_idr = mysqli_query($conn2, $sql_total_idr);
$footer_idr = mysqli_fetch_assoc($q_total_idr);


$sql_total_usd = "
SELECT
SUM(saldo_awal) saldo_awal,
SUM(total_in) total_in,
SUM(pph) pph,
SUM(uang_muka) uang_muka,
SUM(potongan) potongan,
SUM(ded_lp) ded_lp,
SUM(ded_gm) ded_gm,
SUM(reverse_kontrabon) reverse_kontrabon,
SUM(saldo_akhir) saldo_akhir,
SUM(saldo_akhir_idr) saldo_akhir_idr,
SUM(due_current) due_current,
SUM(due_1_30) due_1_30,
SUM(due_31_60) due_31_60,
SUM(due_61_90) due_61_90,
SUM(due_91_120) due_91_120,
SUM(due_121_180) due_121_180,
SUM(due_181_360) due_181_360,
SUM(due_gt_360) due_gt_360,
SUM(total_due) total_due,
SUM(pro_due) pro_due,
SUM(pro_due0) pro_due0,
SUM(pro_due1) pro_due1,
SUM(pro_due2) pro_due2,
SUM(pro_due3) pro_due3,
SUM(pro_due4) pro_due4,
SUM(pro_due5) pro_due5,
SUM(tot_produe) tot_produe
FROM (
$sql
) x where curr != 'IDR'
";

$q_total_usd = mysqli_query($conn2, $sql_total_usd);
$footer_usd = mysqli_fetch_assoc($q_total_usd);

$sql_total_all = "
SELECT
SUM(saldo_awal) saldo_awal,
SUM(total_in) total_in,
SUM(pph) pph,
SUM(uang_muka) uang_muka,
SUM(potongan) potongan,
SUM(ded_lp) ded_lp,
SUM(ded_gm) ded_gm,
SUM(reverse_kontrabon) reverse_kontrabon,
SUM(saldo_akhir) saldo_akhir,
SUM(saldo_akhir_idr) saldo_akhir_idr,
SUM(due_current) due_current,
SUM(due_1_30) due_1_30,
SUM(due_31_60) due_31_60,
SUM(due_61_90) due_61_90,
SUM(due_91_120) due_91_120,
SUM(due_121_180) due_121_180,
SUM(due_181_360) due_181_360,
SUM(due_gt_360) due_gt_360,
SUM(total_due) total_due,
SUM(pro_due) pro_due,
SUM(pro_due0) pro_due0,
SUM(pro_due1) pro_due1,
SUM(pro_due2) pro_due2,
SUM(pro_due3) pro_due3,
SUM(pro_due4) pro_due4,
SUM(pro_due5) pro_due5,
SUM(tot_produe) tot_produe
FROM (
$sql
) x 
";

$q_total_all = mysqli_query($conn2, $sql_total_all);
$footer_all = mysqli_fetch_assoc($q_total_all);

/* ================= RESPONSE ================= */
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data,
    "footer_idr" => $footer_idr,
    "footer_usd" => $footer_usd,
    "footer_all" => $footer_all
]);