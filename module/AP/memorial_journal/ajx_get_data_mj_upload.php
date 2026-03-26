<?php
include "../../../conn/conn.php";

/* ================= INPUT ================= */

session_start();

date_default_timezone_set('Asia/Jakarta');

$user = $_SESSION['username'] ?? 'system';

$search = $_POST['search']['value'] ?? '';

$where = "WHERE 1=1 ";

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            no_coa LIKE '%$search%' OR
            nama_coa LIKE '%$search%' OR
            cc_name LIKE '%$search%' OR
            keterangan LIKE '%$search%'
            nama_pc LIKE '%$search%'
        )
    ";
}


    $sql = "select * from (select a.*, ifnull(filter,'-') filter from (select a.id, a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,concat(c.no_coa,' ', c.nama_coa) as coa , d.cc_name,a.no_coa,a.no_costcenter, a.no_reff, a.reff_date,a.buyer,a.no_ws,a.curr,a.rate,a.debit,a.credit,a.credit_idr,a.debit_idr,a.keterangan,a.status, kode_pc, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc from tbl_memorial_journal_temp a left join master_category_mj b on b.id_cmj = a.id_cmj left join mastercoa_v2 c on c.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcenter LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where a.create_by = '$user') a LEFT JOIN
                                (select id filter from (select a.id, a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,concat(c.no_coa,' ', c.nama_coa) as coa , d.cc_name,a.no_coa,a.no_costcenter, a.no_reff, a.reff_date,a.buyer,a.no_ws,a.curr,a.rate,a.debit,a.credit,a.credit_idr,a.debit_idr,a.keterangan,a.status, kode_pc, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc from tbl_memorial_journal_temp a left join master_category_mj b on b.id_cmj = a.id_cmj left join mastercoa_v2 c on c.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcenter LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where a.create_by = '$user') a INNER JOIN 
                                (select no_coa, no_cc, id_pc from (select a.no_coa, b.no_cc, b.id_pc from (select no_coa, support_gen_adm, support_prod, prod, support_sell from mastercoa_v2 where support_gen_adm != 'N' OR support_prod != 'N' OR prod != 'N' OR support_sell != 'N') a inner join
                                (select no_cc, cc_name, id_pc, 'Y' support_gen_adm from b_master_cc where group2 = 'SUPPORTING GENERAL & ADMINISTRATION' and status = 'Active') b on b.support_gen_adm = a. support_gen_adm
                                UNION
                                select a.no_coa, b.no_cc, b.id_pc from (select no_coa, support_gen_adm, support_prod, prod, support_sell from mastercoa_v2 where support_gen_adm != 'N' OR support_prod != 'N' OR prod != 'N' OR support_sell != 'N') a inner join
                                (select no_cc, cc_name, id_pc, 'Y' support_prod from b_master_cc where group2 = 'SUPPORTING PRODUCTION' and status = 'Active') b on b.support_prod = a. support_prod
                                UNION
                                select a.no_coa, b.no_cc, b.id_pc from (select no_coa, support_gen_adm, support_prod, prod, support_sell from mastercoa_v2 where support_gen_adm != 'N' OR support_prod != 'N' OR prod != 'N' OR support_sell != 'N') a inner join
                                (select no_cc, cc_name, id_pc, 'Y' prod from b_master_cc where group2 = 'PRODUCTION' and status = 'Active') b on b.prod = a.prod
                                UNION
                                select a.no_coa, b.no_cc, b.id_pc from (select no_coa, support_gen_adm, support_prod, prod, support_sell from mastercoa_v2 where support_gen_adm != 'N' OR support_prod != 'N' OR prod != 'N' OR support_sell != 'N') a inner join
                                (select no_cc, cc_name, id_pc, 'Y' support_sell from b_master_cc where group2 = 'SUPPORTING SELLING' and status = 'Active') b on b.support_sell = a.support_sell)a GROUP BY no_coa, no_cc, id_pc
                                UNION
                                select no_coa, '' no_cc, 'NAK' id_pc from mastercoa_v2
                                UNION
                                select no_coa, '' no_cc, 'NAG' id_pc from mastercoa_v2
                                ORDER BY no_coa asc) b on b.no_coa = a.no_coa and b.no_cc = a.no_costcenter and b.id_pc = a.kode_pc) b on b.filter = a.id) a $where ";


if(!$sql){
    echo json_encode([
        "error" => "SQL kosong"
    ]);
    exit;
}


/* ================= EXECUTE ================= */

$q = mysqli_query($conn2, $sql);

$q = mysqli_query($conn2, $sql);

if(!$q){
    echo json_encode([
        "error" => mysqli_error($conn2),
        "query" => $sql
    ]);
    exit;
}


$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */

echo json_encode([
    "data" => $data
]);
