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
            no_payment LIKE '%$search%' OR
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

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
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
    ) AS tot_produe from mutasi $where $supplier
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
    SUM(pay_bank) pay_bank,
    SUM(pay_non_bank) pay_non_bank,
    SUM(pay_cash) pay_cash,
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
    SUM(pay_bank) pay_bank,
    SUM(pay_non_bank) pay_non_bank,
    SUM(pay_cash) pay_cash,
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
    SUM(pay_bank) pay_bank,
    SUM(pay_non_bank) pay_non_bank,
    SUM(pay_cash) pay_cash,
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
    SUM(pay_bank) pay_bank,
    SUM(pay_non_bank) pay_non_bank,
    SUM(pay_cash) pay_cash,
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