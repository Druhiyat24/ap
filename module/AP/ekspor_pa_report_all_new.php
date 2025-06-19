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
            <th style="text-align: center;vertical-align: middle;">No Bankout</th>
            <th style="text-align: center;vertical-align: middle;">Bankout Date</th>
            <th style="text-align: center;vertical-align: middle;">No PI</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">Po Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Description</th>
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


        $sql = mysqli_query($conn2,"SELECT coa, no_coa, no_ftr, tgl_ftr, no_pi, no_po, tgl_po, supp, no_bankout, tgl_bankout, deskripsi, curr, saldo_awal, addition_idr addition, ded_idr ded, tot_gm ded_gm, forex, (saldo_awal + addition_idr - (ded_idr + tot_gm) - forex) saldo_akhir from (select coa, no_coa, no_ftr, tgl_ftr, no_pi, no_po, tgl_po, supp, no_bankout, tgl_bankout, deskripsi, curr, if ((saldo_awal - ded_bfr - tot_gm_bfr) <= 0, 0, (saldo_awal_idr - ded_bfr_idr - tot_gm_bfr)) saldo_awal, saldo_awal_idr, addition_idr, ded_bfr_idr, ded_idr, tot_gm_bfr, tot_gm, IF(curr ='USD' and (saldo_awal + addition - (ded_bfr + ded + tot_gm_bfr_ocy + tot_gm_ocy)) = 0 ,(if ((saldo_awal - ded_bfr - tot_gm_bfr) <= 0, 0, (saldo_awal_idr - ded_bfr_idr - tot_gm_bfr)) + addition_idr - (ded_idr + tot_gm)),0) forex from (select coa, no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout, deskripsi, a.curr, COALESCE(saldo_awal,0) saldo_awal, COALESCE(saldo_awal_idr,0) saldo_awal_idr, COALESCE(addition,0) addition, COALESCE(addition_idr,0) addition_idr, b.curr curr_ded_po_bfr, COALESCE(ded_bfr,0) ded_bfr, COALESCE(ded_bfr_idr,0) ded_bfr_idr, c.curr curr_ded_po, COALESCE(ded,0) ded, COALESCE(ded_idr,0) ded_idr, d.curr curr_ded_gm_bfr, COALESCE(tot_gm_bfr_ocy,0) tot_gm_bfr_ocy, COALESCE(tot_gm_bfr,0) tot_gm_bfr, e.curr curr_ded_gm, COALESCE(tot_gm_ocy,0) tot_gm_ocy, COALESCE(tot_gm,0) tot_gm from (

#Pemasukan Before
        select no_ftr, tgl_ftr, no_pi, no_po, tgl_po, supp, GROUP_CONCAT(no_bankout SEPARATOR ',') no_bankout, GROUP_CONCAT(tgl_bankout SEPARATOR ',') tgl_bankout, sum(saldo_awal) saldo_awal, sum(saldo_awal_idr) saldo_awal_idr, sum(addition) addition, sum(addition_idr) addition_idr, curr, rate, no_coa, coa,deskripsi  from (select * from (select no_ftr, tgl_ftr, no_pi, no_po, tgl_po, supp, GROUP_CONCAT(no_bankout SEPARATOR ',') no_bankout, GROUP_CONCAT(tgl_bankout SEPARATOR ',') tgl_bankout, sum(saldo_awal) saldo_awal, sum(saldo_awal_idr) saldo_awal_idr, sum(addition) addition, sum(addition_idr) addition_idr, curr, rate, no_coa, coa,deskripsi  from (select CASE
            WHEN reff_doc like '%CBD%' OR reff_doc like '%DP%' THEN
            no_ftr_cbd
            ELSE
            reff_doc
            END no_ftr, IF(no_ftr_cbd is null,'-',tgl_ftr_cbd) tgl_ftr, no_pi, IF(reff_doc LIKE '%KKK%',reff_doc,no_po) no_po, tgl_po, nama_supp supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr,0 addition, 0 addition_idr, a.curr, rate, no_coa, coa, deskripsi from (select nama_supp, no_coa, coa, no_journal, tgl_journal,reff_bk, reff_pv, CASE
                WHEN reff_bk not like '%PV%' THEN
                CONCAT(no_coa,no_journal)
                ELSE
                CASE
                WHEN reff_pv like '%CBD%' OR reff_pv like '%DP%' THEN
                reff_pv
                ELSE
                CONCAT(no_coa,no_journal)
                END
                END reff_doc, curr, debit, rate, debit_idr, deskripsi
                from (select a.nama_supp, no_coa, a.coa, no_journal, tgl_journal,a.reff_doc reff_bk, upper(b.reff_doc) reff_pv, curr, debit, rate, debit_idr,deskripsi from (select no_journal, tgl_journal, no_coa , CONCAT(no_coa,' - ',nama_coa) as coa, a.reff_doc, a.curr, a.rate, SUM(debit) debit, SUM(debit_idr) debit_idr, b.nama_supp, b.deskripsi from tbl_list_journal a INNER JOIN (select * from b_bankout_h where stat_rpt is null GROUP BY no_bankout) b on b.no_bankout = a.no_journal where nama_coa like '%UANG MUKA PEMBELIAN%' and no_journal like '%BK%' and tgl_journal < '$start_date' GROUP BY no_journal, no_coa, reff_doc
                    UNION
                    select no_journal, tgl_journal, no_coa, CONCAT(no_coa,' - ',nama_coa) as coa, a.reff_doc, a.curr, a.rate, SUM(debit) debit, SUM(debit_idr) debit_idr, b.nama_supp, b.deskripsi from tbl_list_journal a INNER JOIN (select * from c_petty_cashout_h where reff = 'Advance') b on b.no_pco = a.no_journal where nama_coa like '%UANG MUKA PEMBELIAN%' and no_journal like '%KKK%' and tgl_journal < '$start_date' GROUP BY no_journal, no_coa, reff_doc ORDER BY tgl_journal asc) a LEFT JOIN (select a.coa, b.nama_coa, a.no_pv, a.reff_doc, sum(a.amount) amount from tbl_pv a INNER JOIN mastercoa_v2 b on b.no_coa = a.coa where nama_coa like '%UANG MUKA PEMBELIAN%' GROUP BY no_pv, coa) b on b.no_pv = a.reff_doc and b.coa = a.no_coa) a) a left join (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
            UNION
            select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp order by no_payment asc) b on b.no_payment = a.reff_doc order by tgl_journal asc) a GROUP BY no_po, no_ftr) a

        UNION
#Pemasukan
        select * from (select no_ftr, tgl_ftr, no_pi, no_po, tgl_po, supp, GROUP_CONCAT(no_bankout SEPARATOR ',') no_bankout, GROUP_CONCAT(tgl_bankout SEPARATOR ',') tgl_bankout, sum(saldo_awal) saldo_awal, sum(saldo_awal_idr) saldo_awal_idr, sum(addition) addition, sum(addition_idr) addition_idr, curr, rate, no_coa, coa,deskripsi  from (select CASE
            WHEN reff_doc like '%CBD%' OR reff_doc like '%DP%' THEN
            no_ftr_cbd
            ELSE
            reff_doc
            END no_ftr, IF(no_ftr_cbd is null,'-',tgl_ftr_cbd) tgl_ftr, no_pi, IF(reff_doc LIKE '%KKK%',reff_doc,no_po) no_po, tgl_po, nama_supp supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr,debit addition, debit_idr addition_idr, a.curr, rate, no_coa, coa, deskripsi from (select nama_supp, no_coa, coa, no_journal, tgl_journal,reff_bk, reff_pv, CASE
                WHEN reff_bk not like '%PV%' THEN
                CONCAT(no_coa,no_journal)
                ELSE
                CASE
                WHEN reff_pv like '%CBD%' OR reff_pv like '%DP%' THEN
                reff_pv
                ELSE
                CONCAT(no_coa,no_journal)
                END
                END reff_doc, curr, debit, rate, debit_idr, deskripsi
                from (select a.nama_supp, no_coa, a.coa, no_journal, tgl_journal,a.reff_doc reff_bk, upper(b.reff_doc) reff_pv, curr, debit, rate, debit_idr, deskripsi from (select no_journal, tgl_journal, no_coa, CONCAT(no_coa,' - ',nama_coa) as coa, a.reff_doc, a.curr, a.rate, SUM(debit) debit, SUM(debit_idr) debit_idr, b.nama_supp, b.deskripsi from tbl_list_journal a INNER JOIN (select * from b_bankout_h where stat_rpt is null GROUP BY no_bankout) b on b.no_bankout = a.no_journal where nama_coa like '%UANG MUKA PEMBELIAN%' and no_journal like '%BK%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY no_journal, no_coa, reff_doc 
                    UNION
                    select no_journal, tgl_journal, no_coa, CONCAT(no_coa,' - ',nama_coa) as coa, a.reff_doc, a.curr, a.rate, SUM(debit) debit, SUM(debit_idr) debit_idr, b.nama_supp, b.deskripsi from tbl_list_journal a INNER JOIN (select * from c_petty_cashout_h where reff = 'Advance') b on b.no_pco = a.no_journal where nama_coa like '%UANG MUKA PEMBELIAN%' and no_journal like '%KKK%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY no_journal, no_coa, reff_doc ORDER BY tgl_journal asc) a LEFT JOIN (select a.coa, b.nama_coa, a.no_pv, a.reff_doc, sum(a.amount) amount from tbl_pv a INNER JOIN mastercoa_v2 b on b.no_coa = a.coa where nama_coa like '%UANG MUKA PEMBELIAN%' GROUP BY no_pv, coa) b on b.no_pv = a.reff_doc and b.coa = a.no_coa) a) a left join (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
            UNION
            select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp order by no_payment asc) b on b.no_payment = a.reff_doc order by tgl_journal asc) a GROUP BY no_po, no_ftr) a) a GROUP BY no_po, no_ftr
        ) a 
#total Deduction By PO Before
LEFT JOIN (select no_po, kbon_date, curr, ded_bfr, IF(curr = 'USD',round(ded_bfr * COALESCE(rate,1),2),ded_bfr) ded_bfr_idr from (select no_po, curr, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date
    UNION
    select CONCAT(no_coa,oth_doc) reff_doc, tgl_journal, curr, sum(credit - debit) ded, sum(credit - debit) ded_idr from (select no_journal, tgl_journal, no_coa, nama_coa,curr, rate, debit, credit from tbl_list_journal where type_journal = 'Settlement' and nama_coa like '%UANG MUKA PEMBELIAN%') a INNER JOIN (select no_pci, oth_doc from (select no_pci, oth_doc from c_petty_cashin_h where reff = 'Settlement' and status != 'Cancel' GROUP BY no_pci, oth_doc
        UNION
        select no_pco, reff_doc from c_petty_cashout_h where reff = 'Settlement' and status != 'Cancel' GROUP BY no_pco, reff_doc) a) b on b.no_pci = a.no_journal where tgl_journal < '$start_date' GROUP BY no_coa, oth_doc) b on b.no_po = a.no_po
#total Deduction By PO
LEFT JOIN (select no_po, kbon_date, curr, ded, IF(curr = 'USD',round(ded * COALESCE(rate,1),2),ded) ded_idr from (select no_po, curr, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date
    UNION
    select CONCAT(no_coa,oth_doc) reff_doc, tgl_journal, curr, sum(credit - debit) ded, sum(credit - debit) ded_idr from (select no_journal, tgl_journal, no_coa, nama_coa,curr, rate, debit, credit from tbl_list_journal where type_journal = 'Settlement' and nama_coa like '%UANG MUKA PEMBELIAN%') a INNER JOIN (select no_pci, oth_doc from (select no_pci, oth_doc from c_petty_cashin_h where reff = 'Settlement' and status != 'Cancel' GROUP BY no_pci, oth_doc
        UNION
        select no_pco, reff_doc from c_petty_cashout_h where reff = 'Settlement' and status != 'Cancel' GROUP BY no_pco, reff_doc) a) b on b.no_pci = a.no_journal where tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY no_coa, oth_doc) c on c.no_po = a.no_po
#total Deduction GM Before
LEFT JOIN (select reff_doc, curr, sum((credit - debit)) tot_gm_bfr_ocy, if(curr = 'USD',sum((credit * rate) - (debit * rate)),sum(credit - debit)) tot_gm_bfr from tbl_list_journal where nama_coa like '%uang muka pembelian%' and (no_journal like '%GM/%' or no_journal like '%BM/%' or no_journal like '%BK/%') and (reff_doc not like '%BK%' and reff_doc not like '%KK%') and tgl_journal < '$start_date' GROUP BY reff_doc
    UNION
    select CONCAT(no_coa,reff_doc) reff_doc, curr, sum((credit - debit)) tot_gm_bfr_ocy, if(curr = 'USD',sum((credit * rate) - (debit * rate)),sum(credit - debit)) tot_gm_bfr from tbl_list_journal where nama_coa like '%uang muka pembelian%' and (no_journal like '%GM/%' or no_journal like '%BM/%') and (reff_doc like '%BK%' OR reff_doc like '%KK%') and tgl_journal < '$start_date' GROUP BY reff_doc
    ) d on d.reff_doc = a.no_ftr
#total Deduction GM 
LEFT JOIN (select reff_doc, curr, sum((credit - debit)) tot_gm_ocy, if(curr = 'USD',sum((credit * rate) - (debit * rate)),sum(credit - debit)) tot_gm from tbl_list_journal where nama_coa like '%uang muka pembelian%' and (no_journal like '%GM/%' or no_journal like '%BM/%' or no_journal like '%BK/%') and (reff_doc not like '%BK%' and reff_doc not like '%KK%') and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc
    UNION
    select CONCAT(no_coa,reff_doc) reff_doc, curr, sum((credit - debit)) tot_gm_ocy, if(curr = 'USD',sum((credit * rate) - (debit * rate)),sum(credit - debit)) tot_gm from tbl_list_journal where nama_coa like '%uang muka pembelian%' and (no_journal like '%GM/%' or no_journal like '%BM/%') and (reff_doc like '%BK%' OR reff_doc like '%KK%') and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a) a order by tgl_bankout asc");

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
    $tgl_ftr = $row2['tgl_ftr'];
    $no_ftr = $row2['no_ftr'];
    $tgl_po = $row2['tgl_po'];
    $no_coa = $row2['no_coa'];
    $no_pi =  isset($row2['no_pi']) ? $row2['no_pi'] : '-';
    $no_po =  isset($row2['no_po']) ? $row2['no_po'] : '-';
    $sqlcoa = mysql_query("select DISTINCT no_coa, nama_coa, nama_coa as coa from mastercoa_v2 where no_coa = '$no_coa'",$conn1);
    $rowcoa = mysql_fetch_array($sqlcoa);
    $coa = $rowcoa['coa'];

    if ($no_ftr == 'FTR/C/NAG/0425/00308' and $no_po == 'SMT/0425/030/01607') {
        $dednya = 0;
        $saldoakhir = $row2['saldo_akhir'] + $row2['ded'];
    }else{
        $dednya = $row2['ded'];
        $saldoakhir = $row2['saldo_akhir'];
    }

    if(strpos($no_ftr, 'BK/') !== false) {
        $noftr = '-';
    }else{
        $noftr= $row2['no_ftr'];
    }

    if ($tgl_ftr == '0000-00-00' || $tgl_ftr == '1970-01-01' || $tgl_ftr == '-') {
        $ftrdate = '-'; 
    }else{
        $ftrdate = date("Y-m-d",strtotime($tgl_ftr));
    }

    if ($tgl_po == '0000-00-00' || $tgl_po == '1970-01-01' || $tgl_po == '' || $tgl_po == null) {
        $podate = '-'; 
    }else{
        $podate = date("Y-m-d",strtotime($tgl_po));
    }

    $ttl_beg += $row2['saldo_awal'];
    $ttl_add += $row2['addition'];
    $ttl_ded += $row2['ded'];
    $ttl_mj += $row2['ded_gm'];
    $ttl_for += $row2['forex'];
    $ttl_end += $saldoakhir;

    if ($row2['saldo_awal'] == 0 && $row2['addition'] == 0 && $row2['ded'] == 0 && $row2['ded_gm'] == 0 && $row2['forex'] == 0 && $row2['saldo_akhir'] == 0) {
        echo '';
    }else{
        echo ' <tr style="font-size:12px;text-align:center;">
        <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
        <td style="text-align : left;" value = "'.$row2['no_coa'].'">'.$row2['no_coa'].'</td>
        <td style="text-align : left;" value = "'.$coa.'">'.$coa.'</td>
        <td style="text-align : left;" value = "'.$row2['no_ftr'].'">'.$noftr.'</td>
        <td style="width: 100px;" value = "'.$row2['tgl_ftr'].'">'.$ftrdate.'</td>
        <td style="text-align : left;" value = "'.$row2['no_bankout'].'">'.$row2['no_bankout'].'</td>
        <td style="width: 100px;" value = "'.$row2['tgl_bankout'].'">'.date("d-M-Y",strtotime($row2['tgl_bankout'])).'</td>
        <td style="text-align : left;" value = "'.$row2['no_pi'].'">'.$no_pi.'</td>
        <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$no_po.'</td>
        <td style="width: 100px;" value = "'.$row2['tgl_po'].'">'.$podate.'</td>
        <td style="text-align : left;" value = "'.$row2['supp'].'">'.$row2['supp'].'</td>
        <td style="text-align : left;" value = "'.$row2['deskripsi'].'">'.$row2['deskripsi'].'</td>
        <td style="text-align : right;" value = "'.$row2['saldo_awal'].'">'.number_format($row2['saldo_awal'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['ded'].'">'.number_format($row2['ded'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['ded_gm'].'">'.number_format($row2['ded_gm'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['forex'].'">'.number_format($row2['forex'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['saldo_akhir'].'">'.number_format($saldoakhir,2).'</td>
        </tr>
        ';
    }

    ?>
    <?php 

}
?>
</table>

</body>
</html>




