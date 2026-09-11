<?php
include "../../../conn/conn.php";

$draw   = intval($_POST['draw']   ?? 1);
$start  = intval($_POST['start']  ?? 0);
$length = intval($_POST['length'] ?? 10);

// Load BPB shared query → sets $sql, $start_date, $end_date, $where
include 'query_bpb2.php';
$sql_bpb = $sql;

// Load PV shared query → overwrites $sql (same params from $_REQUEST)
include 'query_pv2.php';
$sql_pv = $sql;

// Combined summary: UNION BPB + PV, aggregate by supplier + curr
$sql = "
SELECT
    supplier, a.curr,
    ROUND(SUM(saldo_awal),2)        saldo_awal,
    ROUND(SUM(addition),2)          addition,
    ROUND(SUM(reverse),2)           reverse,
    ROUND(SUM(deduction_advance),2) deduction_advance,
    ROUND(SUM(deduction_other),2)   deduction_other,
    ROUND(SUM(ded_bank),2)          ded_bank,
    ROUND(SUM(ded_cash),2)          ded_cash,
    ROUND(SUM(ded_nonbank),2)       ded_nonbank,
    ROUND(SUM(deduction_gm),2)      deduction_gm,
    0                               adjust,
    ROUND(SUM(saldo_akhir),2)       saldo_akhir,
    ROUND(SUM(saldo_akhir * IFNULL(b.rate,1)),2) saldo_akhir_idr,
    IFNULL(b.rate,1) rate,
    ROUND(SUM(due_current),2)       due_current,
    ROUND(SUM(due_1_30),2)          due_1_30,
    ROUND(SUM(due_31_60),2)         due_31_60,
    ROUND(SUM(due_61_90),2)         due_61_90,
    ROUND(SUM(due_91_120),2)        due_91_120,
    ROUND(SUM(due_121_180),2)       due_121_180,
    ROUND(SUM(due_181_360),2)       due_181_360,
    ROUND(SUM(due_gt_360),2)        due_gt_360,
    ROUND(SUM(total_due),2)         total_due,
    ROUND(SUM(pro_due),2)           pro_due,
    ROUND(SUM(pro_due0),2)          pro_due0,
    ROUND(SUM(pro_due1),2)          pro_due1,
    ROUND(SUM(pro_due2),2)          pro_due2,
    ROUND(SUM(pro_due3),2)          pro_due3,
    ROUND(SUM(pro_due4),2)          pro_due4,
    ROUND(SUM(pro_due5),2)          pro_due5,
    ROUND(SUM(tot_produe),2)        tot_produe
FROM (
    -- BPB rows
    SELECT supplier, curr, saldo_awal, in_bpb addition, reverse_bpb reverse, 0 deduction_advance, 0 deduction_other, 0 ded_bank, 0 ded_cash, 0 ded_nonbank, gm deduction_gm, saldo_akhir, rate, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
    FROM ($sql_bpb) bpb
    UNION ALL
    -- Payment Voucher rows
    SELECT supplier, curr, saldo_awal, 0 addition, 0 reverse, uang_muka deduction_advance, potongan deduction_other, ded_bank ded_bank, ded_cash ded_cash, ded_nonbank ded_nonbank, ded_gm deduction_gm, saldo_akhir, rate, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
    FROM ($sql_pv) pv
) a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr GROUP BY supplier,a.curr order by supplier,a.curr asc
";

/* ── count ── */
$q_count = mysqli_query($conn2, "SELECT COUNT(*) total FROM ($sql) x");
$recordsTotal = intval(mysqli_fetch_assoc($q_count)['total']);

/* ── paginated data ── */
$q = mysqli_query($conn2, "$sql LIMIT $start, $length");
$data = [];
while ($r = mysqli_fetch_assoc($q)) $data[] = $r;

/* ── footer totals ── */
$footer_cols = "
    SUM(saldo_awal) saldo_awal, SUM(addition) addition,
    SUM(reverse) reverse, SUM(deduction_advance) deduction_advance,
    SUM(deduction_other) deduction_other,
    SUM(ded_bank) ded_bank, SUM(ded_cash) ded_cash, SUM(ded_nonbank) ded_nonbank,
    SUM(deduction_gm) deduction_gm, SUM(adjust) adjust,
    SUM(saldo_akhir) saldo_akhir, SUM(saldo_akhir_idr) saldo_akhir_idr,
    SUM(due_current) due_current, SUM(due_1_30) due_1_30,
    SUM(due_31_60) due_31_60, SUM(due_61_90) due_61_90,
    SUM(due_91_120) due_91_120, SUM(due_121_180) due_121_180,
    SUM(due_181_360) due_181_360, SUM(due_gt_360) due_gt_360,
    SUM(total_due) total_due,
    SUM(pro_due) pro_due, SUM(pro_due0) pro_due0, SUM(pro_due1) pro_due1,
    SUM(pro_due2) pro_due2, SUM(pro_due3) pro_due3, SUM(pro_due4) pro_due4,
    SUM(pro_due5) pro_due5, SUM(tot_produe) tot_produe
";

$q_idr = mysqli_query($conn2, "SELECT $footer_cols FROM ($sql) x WHERE curr = 'IDR'");
$q_usd = mysqli_query($conn2, "SELECT $footer_cols FROM ($sql) x WHERE curr != 'IDR'");
$q_all = mysqli_query($conn2, "SELECT $footer_cols FROM ($sql) x");

echo json_encode([
    "draw"            => $draw,
    "recordsTotal"    => $recordsTotal,
    "recordsFiltered" => $recordsTotal,
    "data"            => $data,
    "footer_idr"      => mysqli_fetch_assoc($q_idr),
    "footer_usd"      => mysqli_fetch_assoc($q_usd),
    "footer_all"      => mysqli_fetch_assoc($q_all),
]);
