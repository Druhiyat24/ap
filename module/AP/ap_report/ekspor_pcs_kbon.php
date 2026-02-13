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
    header("Content-Disposition: attachment; filename=AP Report - KONTRABON.xls");
    include '../../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> Payable Card Statement - KONTRABON<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Kontrabon Number</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Kontrabon Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Advance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Others</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction LP</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction GM</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Reverse</th>
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
                <th colspan="8" id="thProjection-kbon"  style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>
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
) AS tot_produe from report_mutasi $supplier
");



$no = 0;
while($row = mysqli_fetch_array($sql)){

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
    <td value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
    <td value="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td> 
    <td value="'.$row['duedate'].'">'.date("d-M-Y",strtotime($row['duedate'])).'</td>                          
    <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
    <td style="text-align:right;" value = "'.number_format($row['saldo_awal'],2).'">'.number_format($row['saldo_awal'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total_in'],2).'">'.number_format($row['total_in'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['uang_muka'],2).'">'.number_format($row['uang_muka'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['potongan'],2).'">'.number_format($row['potongan'],2).'</td>         
    <td style="text-align:right;" value = "'.number_format($row['ded_lp'],2).'">'.number_format($row['ded_lp'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['ded_gm'],2).'">'.number_format($row['ded_gm'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['reverse_kontrabon'],2).'">'.number_format($row['reverse_kontrabon'],2).'</td>
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




