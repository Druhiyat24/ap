<html>
<head>
    <title>Export Data General Ledger </title>
</head>
<body>
    <style type="text/css">
        body{
            font-family: sans-serif;
        }
        table{
            margin: 20px auto;
            border-collapse: collapse;
        }
        table th,
        table td{
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=AP Report - SUMMARY TYPE 2.xls");
    include '../../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> Payable Card Statement - SUMMARY TYPE 2<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Item Type</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Relationship</th> 
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Amount (Equivalent IDR)</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Percentage from Total</th>
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
                <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
                <th colspan="8" id="thProjection-lp"  style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>
            </tr>
            <tr>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">1-30</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
                <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
                <?php 
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $sqlbulan = mysqli_query($conn1,"select kode_tanggal,bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun, CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)),LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun from dim_date where kode_tanggal BETWEEN CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01') and CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),LPAD(IF(MONTH('$end_date')+5 > 12,MOD((MONTH('$end_date')+5),12),(MONTH('$end_date')+5)),2,0),'01') GROUP BY bulan,tahun order by kode_tanggal asc");
        while($rowbulan = mysqli_fetch_array($sqlbulan)){
            echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">'.$rowbulan['bulan_tahun'].'</th>';
        }
        ?>
                <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
            </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        $nama_supp = $_GET['nama_supp'];

        if ($nama_supp != 'ALL') {
            $supplier = "where supplier = '$nama_supp'";
        }else{
            $supplier = "";
        }


        $sql = mysqli_query($conn2,"WITH
bpb as (
WITH
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

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr),

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
        CASE WHEN duedate < '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
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
    tot_produe * f AS tot_produe from laporan_mutasi  $supplier ),
        
        kontrabon as (
        WITH
saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

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

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr)

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
CASE WHEN duedate < '$end_date' THEN saldo_akhir_idr ELSE 0 END +
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
) AS tot_produe from report_mutasi  $supplier  ),

list_payment as (
WITH
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

all_payment as (SELECT
    r.no_payment, lp.total_payment, lp.total_pph, r.no_pay, r.total_bayar,
    CasE
        WHEN r.rn = r.cnt
         AND tp.total_bayar_lp + lp.total_pph >= lp.total_payment
         AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END as pph_flag,
    CasE
        WHEN r.rn = r.cnt
         AND tp.total_bayar_lp + lp.total_pph >= lp.total_payment
        THEN lp.total_pph
        ELSE 0
    END as pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
JOIN total_paid tp ON tp.no_payment = r.no_payment

ORDER BY r.no_payment),

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

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr)


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
        CASE WHEN duedate < '$end_date' THEN saldo_akhir_idr ELSE 0 END +
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
    ) AS tot_produe from mutasi  $supplier  )

        SELECT supplier,
    item_type2,
    relasi,

    ROUND(SUM(saldo_akhir), 2) AS saldo_akhir,

    ROUND(
        SUM(saldo_akhir) / SUM(SUM(saldo_akhir)) OVER () * 100
    , 2) AS saldo_akhir_persen,

    ROUND(SUM(due_current),2) due_current,
    ROUND(SUM(due_1_30),2) due_1_30,
    ROUND(SUM(due_31_60),2) due_31_60,
    ROUND(SUM(due_61_90),2) due_61_90,
    ROUND(SUM(due_91_120),2) due_91_120,
    ROUND(SUM(due_121_180),2) due_121_180,
    ROUND(SUM(due_181_360),2) due_181_360,
        ROUND(SUM(due_gt_360),2) due_gt_360,
    ROUND(SUM(total_due),2) total_due,
    ROUND(SUM(pro_due),2) pro_due,
    ROUND(SUM(pro_due0),2) pro_due0,
    ROUND(SUM(pro_due1),2) pro_due1,
    ROUND(SUM(pro_due2),2) pro_due2,
    ROUND(SUM(pro_due3),2) pro_due3,
    ROUND(SUM(pro_due4),2) pro_due4,
    ROUND(SUM(pro_due5),2) pro_due5,
    ROUND(SUM(tot_produe),2) tot_produe FROM (select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb
        UNION ALL
        select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon
        UNION ALL
        select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment) a  GROUP BY supplier,item_type2,relasi order by supplier,item_type2 asc
");



$no = 0;
while($row = mysqli_fetch_array($sql)){
    $no++;

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>                          
    <td value="'.$row['supplier'].'">'.$row['supplier'].'</td> 
    <td value="'.$row['item_type2'].'">'.$row['item_type2'].'</td> 
    <td value="'.$row['relasi'].'">'.$row['relasi'].'</td>                            
    <td style="text-align:right;" value="'.number_format($row['saldo_akhir'],2).'">'.number_format($row['saldo_akhir'],2).'</td>
    <td style="text-align:right;" value="'.number_format($row['saldo_akhir_persen'],2).'">'.number_format($row['saldo_akhir_persen'],2).' %</td>
    <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
    <td style="text-align:right;" value="'.$row['due_current'].'">'.number_format($row['due_current'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_1_30'].'">'.number_format($row['due_1_30'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_31_60'].'">'.number_format($row['due_31_60'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_61_90'].'">'.number_format($row['due_61_90'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_91_120'].'">'.number_format($row['due_91_120'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_121_180'].'">'.number_format($row['due_121_180'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_181_360'].'">'.number_format($row['due_181_360'],2).'</td>
    <td style="text-align:right;" value="'.$row['due_gt_360'].'">'.number_format($row['due_gt_360'],2).'</td>
    <td style="text-align:right;" value="'.$row['total_due'].'">'.number_format($row['total_due'],2).'</td>
    <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
    <td style="text-align:right;" value="'.$row['pro_due'].'">'.number_format($row['pro_due'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due0'].'">'.number_format($row['pro_due0'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due1'].'">'.number_format($row['pro_due1'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due2'].'">'.number_format($row['pro_due2'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due3'].'">'.number_format($row['pro_due3'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due4'].'">'.number_format($row['pro_due4'],2).'</td>
    <td style="text-align:right;" value="'.$row['pro_due5'].'">'.number_format($row['pro_due5'],2).'</td>
    <td style="text-align:right;" value="'.$row['tot_produe'].'">'.number_format($row['tot_produe'],2).'</td>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




