<?php
include "../../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

include 'query_bpb2.php';
$sql_bpb = $sql;

include 'query_pv2.php';
$sql_pv = $sql;

$sql = "
SELECT supplier, curr,
    ROUND(SUM(IF(UPPER(curr) != 'IDR', saldo_akhir, 0)),2) saldo_akhir,
    ROUND(SUM(saldo_akhir_idr),2)  saldo_akhir_idr,
    ROUND(SUM(due_current),2)      due_current,
    ROUND(SUM(due_1_30),2)         due_1_30,
    ROUND(SUM(due_31_60),2)        due_31_60,
    ROUND(SUM(due_61_90),2)        due_61_90,
    ROUND(SUM(due_91_120),2)       due_91_120,
    ROUND(SUM(due_121_180),2)      due_121_180,
    ROUND(SUM(due_181_360),2)      due_181_360,
    ROUND(SUM(due_gt_360),2)       due_gt_360,
    ROUND(SUM(total_due),2)        total_due,
    ROUND(SUM(pro_due),2)          pro_due,
    ROUND(SUM(pro_due0),2)         pro_due0,
    ROUND(SUM(pro_due1),2)         pro_due1,
    ROUND(SUM(pro_due2),2)         pro_due2,
    ROUND(SUM(pro_due3),2)         pro_due3,
    ROUND(SUM(pro_due4),2)         pro_due4,
    ROUND(SUM(pro_due5),2)         pro_due5,
    ROUND(SUM(tot_produe),2)       tot_produe
FROM (
    SELECT supplier, curr, saldo_akhir, saldo_akhir_idr,
        due_current, due_1_30, due_31_60, due_61_90, due_91_120,
        due_121_180, due_181_360, due_gt_360, total_due,
        pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe,
        relasi
    FROM ($sql_bpb) bpb
    UNION ALL
    SELECT supplier, curr, saldo_akhir, saldo_akhir_idr,
        due_current, due_1_30, due_31_60, due_61_90, due_91_120,
        due_121_180, due_181_360, due_gt_360, total_due,
        pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe,
        relasi
    FROM ($sql_pv) pv
) combined
WHERE relasi = 'GROUP'
GROUP BY supplier, curr
ORDER BY supplier, curr ASC
";

$footer_cols = "
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

$q_count = mysqli_query($conn2, "SELECT COUNT(*) total FROM ($sql) x");
$recordsTotal = intval(mysqli_fetch_assoc($q_count)['total']);

$q = mysqli_query($conn2, "$sql LIMIT $start, $length");
$data = [];
while ($r = mysqli_fetch_assoc($q)) $data[] = $r;

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
