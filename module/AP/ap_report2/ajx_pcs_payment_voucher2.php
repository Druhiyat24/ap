<?php
include "../../../conn/conn.php";

$draw   = intval($_POST['draw']   ?? 1);
$start  = intval($_POST['start']  ?? 0);
$length = intval($_POST['length'] ?? 10);

include 'query_pv2.php'; // sets $sql, $where, $start_date, $end_date

/* ── count ── */
$q_count = mysqli_query($conn2, "SELECT COUNT(*) total FROM ($sql) x");
$recordsTotal = intval(mysqli_fetch_assoc($q_count)['total']);

/* ── paginated data ── */
$q = mysqli_query($conn2, "$sql LIMIT $start, $length");
$data = [];
while ($r = mysqli_fetch_assoc($q)) $data[] = $r;

/* ── footer totals ── */
$cols = "SUM(saldo_awal) saldo_awal, SUM(total_in) total_in,
    SUM(pph) pph, SUM(uang_muka) uang_muka, SUM(potongan) potongan,
    SUM(ded_bank) ded_bank, SUM(ded_cash) ded_cash, SUM(ded_nonbank) ded_nonbank,
    SUM(ded_gm) ded_gm, SUM(reverse_kontrabon) reverse_kontrabon,
    SUM(saldo_akhir) saldo_akhir, SUM(saldo_akhir_idr) saldo_akhir_idr,
    SUM(due_current) due_current, SUM(due_1_30) due_1_30, SUM(due_31_60) due_31_60,
    SUM(due_61_90) due_61_90, SUM(due_91_120) due_91_120, SUM(due_121_180) due_121_180,
    SUM(due_181_360) due_181_360, SUM(due_gt_360) due_gt_360, SUM(total_due) total_due,
    SUM(pro_due) pro_due, SUM(pro_due0) pro_due0, SUM(pro_due1) pro_due1,
    SUM(pro_due2) pro_due2, SUM(pro_due3) pro_due3, SUM(pro_due4) pro_due4,
    SUM(pro_due5) pro_due5, SUM(tot_produe) tot_produe";

$q_idr = mysqli_query($conn2, "SELECT $cols FROM ($sql) x WHERE curr = 'IDR'");
$q_usd = mysqli_query($conn2, "SELECT $cols FROM ($sql) x WHERE curr != 'IDR'");
$q_all = mysqli_query($conn2, "SELECT $cols FROM ($sql) x");

echo json_encode([
    "draw"            => $draw,
    "recordsTotal"    => $recordsTotal,
    "recordsFiltered" => $recordsTotal,
    "data"            => $data,
    "footer_idr"      => mysqli_fetch_assoc($q_idr),
    "footer_usd"      => mysqli_fetch_assoc($q_usd),
    "footer_all"      => mysqli_fetch_assoc($q_all),
]);
