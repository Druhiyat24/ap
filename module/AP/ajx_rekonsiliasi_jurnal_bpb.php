<?php
include "../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$search = $_POST['search']['value'] ?? '';
$where = "WHERE 1=1 ";
$status = $_POST['status'];

if ($status != 'ALL') {
    $status = "and status = '$status'";
}else{
    $status = "";
}


if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            bpbno_int LIKE '%$search%' OR
            supplier LIKE '%$search%' OR
            pono LIKE '%$search%' OR
            tipe_com LIKE '%$search%' OR
            curr LIKE '%$search%'
        )
    ";
}


$sql = "WITH
bpb_sb as (select bpbno_int, bpbdate, supplier, ph.pono, phd.tipe_com, IF(bpb.confirm = 'Y', 'APPROVED', 'DRAFT') status, bpb.curr, round(SUM(((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) + (((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) * (COALESCE(ph.tax,0) /100))),4) as total,round(SUM(((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price))),4) as dpp,round(SUM((((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) * (COALESCE(ph.tax,0) /100))),4) as ppn from bpb inner join masteritem mi on bpb.id_item = mi.id_item inner join mastersupplier ms on bpb.id_supplier = ms.id_supplier left join po_header ph on bpb.pono = ph.pono left join po_header_draft phd on phd.id = ph.id_draft where bpbdate BETWEEN '$start_date' and '$end_date' and bpb.cancel != 'Y' group by bpbno_int),

bpb_knitting as (select no_bpb, tgl_bpb, supplier, no_po, tipe, IF(confirm = 'Y', 'APPROVED', 'DRAFT') status, a.curr, SUM(total) total, SUM(dpp) dpp, SUM(ppn) ppn from bpb_knitting a INNER JOIN (select bpbno_int, confirm from bpb where bpbdate BETWEEN '$start_date' and '$end_date' and cancel != 'Y' and (bpbno_int like 'BPB/NAK%' OR bpbno_int like 'GB/IN%' OR bpbno_int like 'SGG/IN%'  OR bpbno_int like 'GG/IN%'  OR bpbno_int like 'GM/IN%') GROUP BY bpbno_int) b on b.bpbno_int = a.no_bpb GROUP BY no_bpb),

bppb as (select bppbno_int, bppbdate, supplier, b.pono, tipe_com tipe, IF(confirm = 'Y', 'APPROVED', 'DRAFT') status, curr, (dpp + (dpp * (coalesce(tax,0)/100))) total,dpp,(dpp * (coalesce(tax,0)/100)) ppn from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, supplier, mattype, n_code_category, if(matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) matclass, bppb.curr,bppb.username, bppb.dateinput, bppb.confirm, SUM(((qty) * price)) as dpp,bpbno_ro from bppb inner join masteritem mi on bppb.id_item = mi.id_item inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier where bppbdate BETWEEN '$start_date' and '$end_date' and cancel != 'Y' and bppbno_int not like 'OFC/%' group by bppbno_int) a left join (select bpbno,pono from bpb GROUP BY bpbno) b on b.bpbno = a.bpbno_ro left JOIN (select ph.pono,ph.tax, tipe_com from po_header ph left join po_header_draft phd on phd.id = ph.id_draft GROUP BY pono) c on c.pono = b.pono),

data_bpb as (select bpbno_int, bpbdate, supplier, COALESCE(pono,'-') pono, tipe_com, status, curr, round(total,4) total, round(dpp,4) dpp, round(ppn ,4) ppn from (select * from bpb_sb where total != 0
UNION ALL
select * from bpb_knitting where total != 0
UNION ALL
select * from bppb where total != 0) a),

data_jurnal as (SELECT no_journal, ABS( SUM(
            CASE 
                WHEN nama_coa LIKE 'GR/IR%' 
                     OR nama_coa LIKE 'PIUTANG LAIN-LAIN%'
                THEN (debit - credit)
                ELSE 0 
            END )) AS total, 
                        ABS( SUM(
            CASE 
                WHEN nama_coa LIKE 'PERSEDIAAN%' 
                     OR nama_coa LIKE 'BEBAN%'
                                         OR nama_coa LIKE 'MESIN%'
                THEN (debit - credit)
                ELSE 0 
            END ) ) AS dpp,
                        ABS( SUM(
            CASE 
                        
                WHEN nama_coa LIKE 'PAJAK%' 
                     OR nama_coa LIKE 'PENDAPATAN%'
                THEN (debit - credit)
                ELSE 0 
            END ) ) AS ppn FROM tbl_list_journal WHERE tgl_journal BETWEEN '$start_date' and '$end_date' AND type_journal LIKE '%AP - BPB%' GROUP BY no_journal)
                        
select a.*, ROUND(COALESCE(b.total,0),4) total_jurnal, ROUND(COALESCE(b.dpp,0),4) dpp_jurnal, ROUND(COALESCE(b.ppn,0),4) ppn_jurnal, ROUND(a.total - ROUND(COALESCE(b.total,0),4),4) diff_total, ROUND(a.dpp - ROUND(COALESCE(b.dpp,0),4),4) diff_dpp, ROUND(a.ppn - ROUND(COALESCE(b.ppn,0),4),4) diff_ppn from data_bpb a LEFT JOIN data_jurnal b on b.no_journal = a.bpbno_int $where $status
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
    SUM(dpp) dpp,
    SUM(ppn) ppn,
    SUM(total) total,
    SUM(dpp_jurnal) dpp_jurnal,
    SUM(ppn_jurnal) ppn_jurnal,
    SUM(total_jurnal) total_jurnal
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