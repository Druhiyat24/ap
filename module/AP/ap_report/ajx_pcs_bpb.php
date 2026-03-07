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
            no_bpb LIKE '%$search%' OR
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

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi $where $supplier
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

$sql_total_idr = "
SELECT
    SUM(saldo_awal) saldo_awal,
    SUM(in_bpb) in_bpb,
    SUM(reverse_bpb) reverse_bpb,
    SUM(ded_kontrabon) ded_kontrabon,
    SUM(reverse_kontrabon) reverse_kontrabon,
    SUM(gm) gm,
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
    SUM(in_bpb) in_bpb,
    SUM(reverse_bpb) reverse_bpb,
    SUM(ded_kontrabon) ded_kontrabon,
    SUM(reverse_kontrabon) reverse_kontrabon,
    SUM(gm) gm,
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
    SUM(saldo_awal * rate) saldo_awal,
    SUM(in_bpb * rate) in_bpb,
    SUM(reverse_bpb * rate) reverse_bpb,
    SUM(ded_kontrabon * rate) ded_kontrabon,
    SUM(reverse_kontrabon * rate) reverse_kontrabon,
    SUM(gm * rate) gm,
    SUM(saldo_akhir * rate) saldo_akhir,
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