<?php
include "../../conn/conn.php";

/* ================= INPUT ================= */

$start_date = date("Y-m-d", strtotime($_POST['start_date']));
$end_date   = date("Y-m-d", strtotime($_POST['end_date']));
$id_supplier   = $_POST['id_supplier'];
$search     = $_POST['search']['value'] ?? '';


if ($id_supplier != 'ALL') {
    $where = "and a.id_supplier = '$id_supplier'";
}else{
    $where = "";
}


/* ================= SEARCH FILTER ================= */

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            bpbno LIKE '%$search%' OR
            invno LIKE '%$search%' OR
            jenis_dok LIKE '%$search%' OR
            nomor_aju LIKE '%$search%' OR
            bcno LIKE '%$search%' OR
            supplier LIKE '%$search%' OR
            pono LIKE '%$search%' OR
            tipe_com LIKE '%$search%' OR
            itemdesc LIKE '%$search%' OR
            remark LIKE '%$search%' OR
            jenis_trans LIKE '%$search%' OR
            no_coa LIKE '%$search%' OR
            nama_coa LIKE '%$search%' OR
            no_cc LIKE '%$search%' OR
            cc_name LIKE '%$search%'
        )
    ";
}

/* ================= MAIN QUERY ================= */

$sql = "select a.*, IFNULL(b.no_coa,'-') no_coa, IFNULL(b.nama_coa,'-') nama_coa from (SELECT if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate, ifnull(mp.nama_pc, 'NIRWANA ALABARE GARMENT') profit_center, a.invno,a.jenis_dok,right(a.nomor_aju,6) nomor_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.goods_code, CONCAT(s.itemdesc,' ',coalesce(s.add_info,'')) itemdesc,m.description,s.color,s.size, a.qty,a.unit,a.berat_bersih,a.remark,a.username,CONCAT(a.confirm_by, ' (', a.confirm_date, ')') AS confirm_by,r.reqno,'' whs_code,a.curr,if(z.tipe_com ='FOC','0',a.price) price,a.jenis_trans,a.reffno, IFNULL(cc.no_cc,'-') no_cc, IFNULL(cc.cc_name,'-') cc_name,CASE
    WHEN id_group2 = 1 THEN coa_production
    WHEN id_group2 = 2 THEN coa_sup_production
    WHEN id_group2 = 3 THEN coa_sup_gen_adm
    WHEN id_group2 = 4 THEN coa_sup_selling
    ELSE NULL
END AS coa
    from bpb a 
    inner join masteritem s on a.id_item=s.id_item 
    LEFT join (select pono,tipe_com from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono 
    LEFT join mastersupplier d on a.id_supplier=d.id_supplier 
    LEFT join master_pc mp on mp.kode_pc = a.profit_center 
    LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id 
    left join reqnon_header r on a.id_jo = r.id 
    LEFT JOIN userpassword up on up.username = r.username
    LEFT JOIN b_master_cc cc on cc.no_cc = up.no_cc
    where bpbno_int LIKE '%GEN%' AND m.n_id != '3' AND a.bpbdate >= '$start_date' AND a.bpbdate <= '$end_date' $where) a LEFT JOIN mastercoa_v2 b on b.no_coa = a.coa
";

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
