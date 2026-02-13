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
    header("Content-Disposition: attachment; filename=AP Report - LIST PAYMENT.xls");
    include '../../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> Payable Card Statement - LIST PAYMENT<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">List Payment Number</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">List Payment Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Created By</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Bank</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Non Bank</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Cash</th>
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
    ) AS tot_produe from mutasi $supplier
");



$no = 1;
while($row = mysqli_fetch_array($sql)){

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
    <td value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
    <td value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td> 
    <td value="'.$row['duedate'].'">'.date("d-M-Y",strtotime($row['duedate'])).'</td>                          
    <td value="'.$row['create_user'].'">'.$row['create_user'].'</td> 
    <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
    <td style="text-align:right;" value = "'.number_format($row['saldo_awal'],2).'">'.number_format($row['saldo_awal'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total_in'],2).'">'.number_format($row['total_in'],2).'</td>         
    <td style="text-align:right;" value = "'.number_format($row['pay_bank'],2).'">'.number_format($row['pay_bank'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['pay_non_bank'],2).'">'.number_format($row['pay_non_bank'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['pay_cash'],2).'">'.number_format($row['pay_cash'],2).'</td>
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




