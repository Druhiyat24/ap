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
    header("Content-Disposition: attachment; filename=AP Report - BPB.xls");
    include '../../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> Payable Card Statement - BPB<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Number</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Reverse BPB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction KB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Reverse KB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction GM</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Rate</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance IDR</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA Name</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 1</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 2</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Relationship</th> 
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
                <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
                <th colspan="8" id="thProjection"  style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>
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

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate < '$end_date' THEN (saldo_akhir * rate)
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
    tot_produe * f AS tot_produe from laporan_mutasi $supplier
");



$no = 0;
while($row = mysqli_fetch_array($sql)){
    $no++;

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
    <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
    <td value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td> 
    <td value="'.$row['duedate'].'">'.date("d-M-Y",strtotime($row['duedate'])).'</td>                          
    <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
    <td style="text-align:right;" value = "'.number_format($row['saldo_awal'],2).'">'.number_format($row['saldo_awal'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['in_bpb'],2).'">'.number_format($row['in_bpb'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['reverse_bpb'],2).'">'.number_format($row['reverse_bpb'],2).'</td>         
    <td style="text-align:right;" value = "'.number_format($row['ded_kontrabon'],2).'">'.number_format($row['ded_kontrabon'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['reverse_kontrabon'],2).'">'.number_format($row['reverse_kontrabon'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['gm'],2).'">'.number_format($row['gm'],2).'</td>
    <td style="text-align:right;" value="'.number_format($row['saldo_akhir'],2).'">'.number_format($row['saldo_akhir'],2).'</td>
    <td style="text-align:right;" value="'.number_format($row['rate'],2).'">'.number_format($row['rate'],2).'</td>
    <td style="text-align:right;" value="'.number_format($row['saldo_akhir_idr'],2).'">'.number_format($row['saldo_akhir_idr'],2).'</td>
    <td value="'.$row['no_coa'].'">'.$row['no_coa'].'</td>
    <td value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
    <td value="'.$row['item_type1'].'">'.$row['item_type1'].'</td>
    <td value="'.$row['item_type2'].'">'.$row['item_type2'].'</td>
    <td value="'.$row['relasi'].'">'.$row['relasi'].'</td>
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




