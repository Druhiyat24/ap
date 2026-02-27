<?php
include "../../conn/conn.php";

if (!isset($_POST['bpb_list'])) {
    exit("Tidak ada data");
}

$bpb_list = $_POST['bpb_list'];

if (!is_array($bpb_list) || count($bpb_list) == 0) {
    exit("Data kosong");
}

mysqli_begin_transaction($conn2);

try {

    $clean = array_map(function($val) use ($conn2) {
        return "'" . mysqli_real_escape_string($conn2, $val) . "'";
    }, $bpb_list);

    $in = implode(",", $clean);


    $sql_backup = "
        INSERT INTO tbl_list_journal_cancel
        SELECT *
        FROM tbl_list_journal
        WHERE no_journal IN ($in)
    ";

    if (!mysqli_query($conn2, $sql_backup)) {
        throw new Exception(mysqli_error($conn2));
    }


    $sql_delete = "
        DELETE FROM tbl_list_journal
        WHERE no_journal IN ($in)
    ";

    if (!mysqli_query($conn2, $sql_delete)) {
        throw new Exception(mysqli_error($conn2));
    }


    $sql_insert = "insert INTO tbl_list_journal
WITH
rate as (select curr, tanggal, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY curr, tanggal),

bpb as (select *, CASE 
    WHEN mattype <> 'N' THEN
        CASE 
            WHEN matclass = 'FABRIC' THEN 'PEMBELIAN KAIN'
            WHEN matclass = 'ACCESORIES' THEN 'PEMBELIAN AKSESORIS'
            WHEN matclass = 'CMT' THEN 'BIAYA MAKLOON PAKAIAN JADI'
            WHEN matclass = 'PRINTING' THEN 'BIAYA MAKLOON PRINTING'
            WHEN matclass = 'EMBRODEIRY' THEN 'BIAYA MAKLOON EMBRODEIRY'
            WHEN matclass = 'WASHING' THEN 'BIAYA MAKLOON WASHING'
            WHEN matclass = 'PAINTING' THEN 'BIAYA MAKLOON PAINTING'
            WHEN matclass = 'HEATSEAL' THEN 'BIAYA MAKLOON HEATSEAL'
            ELSE 'BIAYA MAKLOON LAINNYA'
        END
    ELSE
        CASE 
            WHEN n_code_category = '1' THEN 'PEMBELIAN PERSEDIAAN ATK'
            WHEN n_code_category = '2' THEN 'PEMBELIAN PERSEDIAAN UMUM'
            WHEN n_code_category = '3' THEN 'BIAYA PERSEDIAAN SPAREPARTS'
            WHEN n_code_category = '4' THEN 'BIAYA MESIN'
            ELSE ''
        END
END AS kata1
 from (select phd.tipe_com, bpbno_int, bpb.bpbdate, ms.supplier, mi.mattype, IFNULL(mi.n_code_category,'-') n_code_category,  CASE WHEN mi.mattype = 'C' AND mi.matclass IN ('CMT','PRINTING','EMBRODEIRY','WASHING','PAINTING','HEATSEAL') THEN mi.matclass WHEN mi.mattype = 'C' THEN 'OTHER' ELSE if(mi.matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) END AS matclass, IFNULL(mps.supplier_ctg,'Third') ctg_supp, bpb.curr,bpb.username, bpb.dateinput, bpb.confirm_by, bpb.confirm_date, round(SUM(((qty - COALESCE(qty_reject,0)) * price) + (((qty - COALESCE(qty_reject,0)) * price) * (COALESCE(ph.tax,0) /100))),4) as total,round(SUM(((qty - COALESCE(qty_reject,0)) * price)),4) as dpp,round(SUM((((qty - COALESCE(qty_reject,0)) * price) * (COALESCE(ph.tax,0) /100))),4) as ppn, IF(SUBSTR(bpbno_int,1,3) = 'GEN',bpb.profit_center,'NAG') profit_center
                        from bpb 
                        inner join masteritem mi on bpb.id_item = mi.id_item
                        inner join mastersupplier ms on bpb.id_supplier = ms.id_supplier
                        left join ap_mapping_supplier mps on mps.id_supplier = ms.id_supplier
                        left join po_header ph on bpb.pono = ph.pono
                        left join po_header_draft phd on phd.id = ph.id_draft
                        where bpbno_int IN ($in) group by bpbno,mi.mattype, mi.n_code_category order by bpbno_int) a),
                        
bpb_credit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.total credit, 0 debit_idr, ROUND(a.total * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit' ),
                        
bpb_debit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.dpp debit, 0 credit, ROUND(a.dpp * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),
                        
ppn as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.52.07' no_coa, 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.ppn debit, 0 credit, ROUND(a.ppn * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

bpb_debit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.34.05' no_coa, 'PIUTANG LAIN-LAIN PIHAK KETIGA - BAHAN BAKU / BAHAN PEMBANTU' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.total debit, 0 credit, ROUND(a.total * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

bpb_credit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.dpp credit, 0 debit_idr, ROUND(a.dpp * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

ppn_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '8.07.01' no_coa, 'PENDAPATAN LAIN-LAIN' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.ppn credit, 0 debit_idr, ROUND(a.ppn * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),
                        
jurnal as (select * from (select * from bpb_credit where (debit-credit) != 0
UNION ALL
select * from bpb_debit where (debit-credit) != 0
UNION ALL
select * from ppn where (debit-credit) != 0) a order by no_journal, no_coa asc),

jurnal_buyer as (select * from (select * from bpb_credit_buyer where (debit-credit) != 0
UNION ALL
select * from bpb_debit_buyer where (debit-credit) != 0
UNION ALL
select * from ppn_buyer where (debit-credit) != 0) a order by no_journal, no_coa asc)
                        
select * from jurnal
UNION ALL
SELECT * 
FROM jurnal_buyer jb
WHERE EXISTS (
    SELECT 1 
    FROM bpb 
    WHERE tipe_com = 'BUYER' GROUP BY bpbno_int
)       ";

    if (!mysqli_query($conn2, $sql_insert)) {
        throw new Exception(mysqli_error($conn2));
    }


    $sql_ro_insert = "insert INTO tbl_list_journal
    WITH
rate as (select curr, tanggal, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY curr, tanggal),

bpb as (select *, CASE 
    WHEN mattype <> 'N' THEN
        CASE 
            WHEN matclass = 'FABRIC' THEN 'RETURN PEMBELIAN KAIN'
            WHEN matclass = 'ACCESORIES' THEN 'RETURN PEMBELIAN AKSESORIS'
            WHEN matclass = 'CMT' THEN 'RETURN BIAYA MAKLOON PAKAIAN JADI'
            WHEN matclass = 'PRINTING' THEN 'RETURN BIAYA MAKLOON PRINTING'
            WHEN matclass = 'EMBRODEIRY' THEN 'RETURN BIAYA MAKLOON EMBRODEIRY'
            WHEN matclass = 'WASHING' THEN 'RETURN BIAYA MAKLOON WASHING'
            WHEN matclass = 'PAINTING' THEN 'RETURN BIAYA MAKLOON PAINTING'
            WHEN matclass = 'HEATSEAL' THEN 'RETURN BIAYA MAKLOON HEATSEAL'
            ELSE 'RETURN BIAYA MAKLOON LAINNYA'
        END
    ELSE
        CASE 
            WHEN n_code_category = '1' THEN 'RETURN PEMBELIAN PERSEDIAAN ATK'
            WHEN n_code_category = '2' THEN 'RETURN PEMBELIAN PERSEDIAAN UMUM'
            WHEN n_code_category = '3' THEN 'RETURN BIAYA PERSEDIAAN SPAREPARTS'
            WHEN n_code_category = '4' THEN 'RETURN BIAYA MESIN'
            ELSE ''
        END
END AS kata1
 from (select '' tipe_com, bppbno_int bpbno_int, bppbdate bpbdate, supplier, mattype,n_code_category,matclass,ctg_supp, curr, username, dateinput, confirm_by, confirm_date,(dpp + (dpp * (COALESCE(tax,0)/100))) total,dpp,(dpp * (COALESCE(tax,0)/100)) ppn, IF(SUBSTR(bppbno_int,1,3) = 'GEN',profit_center,'NAG') profit_center from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, ms.supplier, mi.mattype, IFNULL(mi.n_code_category,'-') n_code_category,  CASE WHEN mi.mattype = 'C' AND mi.matclass IN ('CMT','PRINTING','EMBRODEIRY','WASHING','PAINTING','HEATSEAL') THEN mi.matclass WHEN mi.mattype = 'C' THEN 'OTHER' ELSE if(mi.matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) END AS matclass, IFNULL(mps.supplier_ctg,'Third') ctg_supp, bppb.curr,bppb.username, bppb.dateinput, bppb.confirm_by, bppb.confirm_date, SUM(((qty) * price)) as dpp,bpbno_ro, bppb.profit_center
                        from bppb 
                        inner join masteritem mi on bppb.id_item = mi.id_item
                        inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier
                        left join ap_mapping_supplier mps on mps.id_supplier = ms.id_supplier
                        where bppbno_int IN ($in) group by bppbno) a left join
                        
                        (select bpbno,pono from bpb GROUP BY bpbno) b on b.bpbno = a.bpbno_ro
                        left JOIN
                        (select pono,tax from po_header GROUP BY pono) c on c.pono = b.pono) a),
                        
bpb_credit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.total credit, 0 debit_idr, ROUND(a.total * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit' ),
                        
bpb_debit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.dpp debit, 0 credit, ROUND(a.dpp * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit'),
                        
ppn as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.52.07' no_coa, 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.ppn debit, 0 credit, ROUND(a.ppn * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit'),

bpb_debit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.total debit, 0 credit, ROUND(a.total * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit'),

bpb_credit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.34.05' no_coa, 'PIUTANG LAIN-LAIN PIHAK KETIGA - BAHAN BAKU / BAHAN PEMBANTU' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.dpp credit, 0 debit_idr, ROUND(a.dpp * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit'),

ppn_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '8.07.01' no_coa, 'PENDAPATAN LAIN-LAIN' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.ppn credit, 0 debit_idr, ROUND(a.ppn * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit'),
                        
jurnal as (select * from (select * from bpb_credit where (debit-credit) != 0
UNION ALL
select * from bpb_debit where (debit-credit) != 0
UNION ALL
select * from ppn where (debit-credit) != 0) a order by no_journal, no_coa asc),

jurnal_buyer as (select * from (select * from bpb_credit_buyer where (debit-credit) != 0
UNION ALL
select * from bpb_debit_buyer where (debit-credit) != 0
UNION ALL
select * from ppn_buyer where (debit-credit) != 0) a order by no_journal, no_coa asc)
                        
select * from jurnal
UNION ALL
SELECT * 
FROM jurnal_buyer jb
WHERE EXISTS (
    SELECT 1 
    FROM bpb 
    WHERE tipe_com = 'BUYER' GROUP BY bpbno_int
)       ";

    if (!mysqli_query($conn2, $sql_ro_insert)) {
        throw new Exception(mysqli_error($conn2));
    }


    mysqli_commit($conn2);

    echo "Repost jurnal berhasil untuk " . count($bpb_list) . " data.";

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo "Gagal repost: " . $e->getMessage();
}
