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
    header("Content-Disposition: attachment; filename=Other Receivable Material Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>PURCHASE ADVANCE REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Coa</th>
            <th style="text-align: center;vertical-align: middle;">Coa Name</th>
            <th style="text-align: center;vertical-align: middle;">No FTR</th>
            <th style="text-align: center;vertical-align: middle;">FTR Date</th>
            <th style="text-align: center;vertical-align: middle;">Bankout Date</th>
            <th style="text-align: center;vertical-align: middle;">No PI</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">Po Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (KB)</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
            <th style="text-align: center;vertical-align: middle;">Forex Gain / (Loss)</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"SELECT *, if(ded = 0,0,((saldo_awal + addition) - ded)) forex, (saldo_awal + addition - ded - if(ded = 0,0,((saldo_awal + addition) - ded))) saldo_akhir from (select no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout,(saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm,0)) saldoawal_, addition addition_, (saldo_awal_idr - COALESCE(ded_bfr_idr,0) - COALESCE(tot_gm_bfr,0)) saldo_awal, addition_idr addition, COALESCE(ded_idr,0) ded,  COALESCE(tot_gm,0) ded_gm from (select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr, 0 addition, 0 addition_idr, rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                    UNION
                    select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(amount * rate,2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%') b on b.no_pv = a.reff_doc group by b.reff_doc
                    UNION
                    select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(amount * rate,2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%') b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal < '$start_date'
                    UNION
                    select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr, debit addition, debit_idr addition_idr,rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                        UNION
                        select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(amount * rate,2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%') b on b.no_pv = a.reff_doc group by b.reff_doc
                        UNION
                        select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(amount * rate,2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%') b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal BETWEEN '$start_date' and '$end_date') a 
                LEFT JOIN (select no_po, ded_bfr, kbon_date, IF(curr = 'USD',round(ded_bfr * COALESCE(rate,1),2),ded_bfr) ded_bfr_idr from (select no_po, curr, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) b on b.no_po = a.no_po

                LEFT JOIN (select no_po, ded, kbon_date, IF(curr = 'USD',round(ded * COALESCE(rate,1),2),ded) ded_idr from (select no_po, curr, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) c on c.no_po = a.no_po
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_bfr from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal < '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.no_ftr
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a where (saldoawal_ + addition_) > 0");

$ttl_beg =0;
$ttl_add =0;
$ttl_ded =0;
$ttl_for =0;
$sisa_dn =0;
$sisa_dn_bfr =0;
$ttl_end =0;
$no = 0;
$ttl_mj =0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;
    $no_coa = $row2['no_coa'];
    $sqlcoa = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2 where no_coa = '$no_coa'",$conn1);
    $rowcoa = mysql_fetch_array($sqlcoa);
    $coa = $rowcoa['nama_coa'];

    $ttl_beg += $row2['saldo_awal'];
    $ttl_add += $row2['addition'];
    $ttl_ded += $row2['ded'];
    $ttl_mj += $row2['ded_gm'];
    $ttl_for += $row2['forex'];
    $ttl_end += $row2['saldo_akhir'];

    echo ' <tr style="font-size:12px;text-align:center;">
    <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
    <td style="text-align : left;" value = "'.$row2['no_coa'].'">'.$row2['no_coa'].'</td>
    <td style="text-align : left;" value = "'.$coa.'">'.$coa.'</td>
    <td style="text-align : left;" value = "'.$row2['no_ftr'].'">'.$row2['no_ftr'].'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_ftr'].'">'.date("d-M-Y",strtotime($row2['tgl_ftr'])).'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_bankout'].'">'.date("d-M-Y",strtotime($row2['tgl_bankout'])).'</td>
    <td style="text-align : left;" value = "'.$row2['no_pi'].'">'.$row2['no_pi'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_po'].'">'.date("d-M-Y",strtotime($row2['tgl_po'])).'</td>
    <td style="text-align : left;" value = "'.$row2['supp'].'">'.$row2['supp'].'</td>
    <td style="text-align : right;" value = "'.$row2['saldo_awal'].'">'.number_format($row2['saldo_awal'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['ded'].'">'.number_format($row2['ded'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['ded_gm'].'">'.number_format($row2['ded_gm'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['forex'].'">'.number_format($row2['forex'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['saldo_akhir'].'">'.number_format($row2['saldo_akhir'],2).'</td>
    </tr>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




