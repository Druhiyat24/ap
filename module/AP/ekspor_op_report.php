<html>
<head>
    <title>Export Data OP </title>
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
    header("Content-Disposition: attachment; filename=Other Payable Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

     ?>

        <h4>OTHER PAYABLE REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Memo</th>
            <th style="text-align: center;vertical-align: middle;">Memo Date</th>
            <th style="text-align: center;vertical-align: middle;">No Invoice Vendor</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (BK)</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
            <th style="text-align: center;vertical-align: middle;">Inc Tax Art 23</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

  
        $sql = mysqli_query($conn2,"SELECT nm_memo,tgl_memo,no_invoice,supplier,buyer,IF(tgl_memo < '$start_date',total_memo,0) beg_balance, IF(tgl_memo >= '$start_date',total_memo,0) addition,total_bk deduction, total_bk2 deduction2 FROM (SELECT a.nm_memo,tgl_memo,no_invoice,supplier,buyer,total_memo,COALESCE(ttl_amount,0) total_bk, COALESCE(ttl_amount2,0) total_bk2 FROM (
SELECT nm_memo, tgl_memo,no_invoice,supplier,buyer,total_memo from (SELECT b.id,a.id_supplier,d.supplier,g.supplier buyer,a.nm_memo, a.tgl_memo,IF(b.inv_vendor is null,'-',b.inv_vendor) no_invoice,a.curr,sum(b.biaya) total_memo,'' id_book from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join mastersupplier g on g.id_supplier =a.id_buyer inner join memo_det b on b.id_h = a.id_h where  a.nm_memo >= 'MEMO/NAG/2310/01039' and a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and b.cancel = 'N' and a.status NOT IN ('DRAFT','CANCEL') GROUP BY a.nm_memo order by a.nm_memo asc ) a) a left join

(select nm_memo,bankout_date,SUM(amount) amount,SUM(ttl_ppn) ttl_ppn, SUM(ttl_pph) ttl_pph, SUM(ttl_amount) ttl_amount, SUM(inc_tax) inc_tax from (select reff_doc nm_memo,bankout_date,amount,ttl_ppn,ttl_pph,(amount + ttl_ppn - ttl_pph)ttl_amount,(ttl_ppn - ttl_pph) inc_tax from (select a.reff_doc,d.bankout_date, a.amount,a.ppn,round( a.amount * a.ppn /100,2) ttl_ppn,a.pph, round( a.amount * a.pph /100,2) ttl_pph from tbl_pv a inner join b_bankout_det c on c.no_reff = a.no_pv inner join b_bankout_h d on d.no_bankout = c.no_bankout where d.status != 'CANCEL' and a.reff_doc like '%MEMO/%') a) a where bankout_date BETWEEN '$start_date' and '$end_date' GROUP BY nm_memo) b on b.nm_memo = a.nm_memo left join

(select nm_memo_bfr,bankout_date,amount2,ttl_ppn2,ttl_pph2,ttl_amount2,inc_tax2 from (select nm_memo nm_memo_bfr,bankout_date,SUM(amount) amount2,SUM(ttl_ppn) ttl_ppn2, SUM(ttl_pph) ttl_pph2, SUM(ttl_amount) ttl_amount2, SUM(inc_tax) inc_tax2 from (select reff_doc nm_memo,bankout_date,amount,ttl_ppn,ttl_pph,(amount + ttl_ppn - ttl_pph)ttl_amount,(ttl_ppn - ttl_pph) inc_tax from (select a.reff_doc,d.bankout_date, a.amount,a.ppn,round( a.amount * a.ppn /100,2) ttl_ppn,a.pph, round( a.amount * a.pph /100,2) ttl_pph from tbl_pv a inner join b_bankout_det c on c.no_reff = a.no_pv inner join b_bankout_h d on d.no_bankout = c.no_bankout where d.status != 'CANCEL' and a.reff_doc like '%MEMO/%') a) a where bankout_date < '$start_date' GROUP BY nm_memo union
select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal < '$start_date') a) c on c.nm_memo_bfr = a.nm_memo) a");

        $ttl_beg =0;
        $ttl_add =0;
        $ttl_ded =0;
        $ttl_for =0;
        $ttl_end =0;
        $no = 0;
        $ttl_mj =0;
    while($row2 = mysqli_fetch_array($sql)){
        $nm_memo = $row2['nm_memo'];
        $sqlmm = mysqli_query($conn1,"SELECT no_journal from tbl_list_journal where no_journal = '$nm_memo'");
        $rowmm = mysqli_fetch_array($sqlmm);
        $no_journal = isset($rowmm['no_journal']) ? $rowmm['no_journal'] : null;
        if ($no_journal != null) {
        $no++;
        // $beg_balance = $row2['beg_balance'];
        $sql_mj_b = mysqli_query($conn2,"select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal < '$start_date' and reff_doc = '$nm_memo'");
        $row_mj_b = mysqli_fetch_array($sql_mj_b);
        $val_mj_b = isset($row_mj_b['total']) ? $row_mj_b['total'] : 0;
        // echo $val_mj_b;

        if ($val_mj_b > 0) {
             $beg_balance = $row2['beg_balance'] - $val_mj_b;
        }else{
             $beg_balance = $row2['beg_balance'];
        }
        $addition = $row2['addition'];
        $deduction = $row2['deduction'];
        $deduction2 = $row2['deduction2'];

        $sql_mj = mysqli_query($conn2,"select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' and reff_doc = '$nm_memo'");
        $row_mj = mysqli_fetch_array($sql_mj);
        $val_mj = isset($row_mj['total']) ? $row_mj['total'] : 0;
        
        if ($deduction > 0) {
        if ($beg_balance > 0) {
            $forex = $beg_balance - $deduction - $val_mj;
        }else{
            $forex = $addition - $deduction - $val_mj;
        }
        }else{
            $forex = 0;
        }
        $end_balance = $beg_balance + $addition - $deduction - $val_mj - $forex;
        if ($deduction2 > 0) {
        }else{
        $ttl_beg +=$beg_balance;
        $ttl_add +=$addition;
        $ttl_ded +=$deduction;
        $ttl_for +=$forex;
        $ttl_end +=$end_balance;
        if ($beg_balance == 0 && $addition == 0) {
            // code...
        }else{
        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['nm_memo'].'">'.$row2['nm_memo'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_memo'].'">'.date("d-M-Y",strtotime($row2['tgl_memo'])).'</td>
            <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
            <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
            <td style="text-align : right;" value = "'.$row2['beg_balance'].'">'.number_format($row2['beg_balance'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['deduction'].'">'.number_format($row2['deduction'],2).'</td>
            <td style="text-align : right;" value = "'.$val_mj.'">'.number_format($val_mj,2).'</td>
            <td style="text-align : right;" value = "'.$forex.'">'.number_format($forex,2).'</td>
            <td style="text-align : right;" value = "'.$end_balance.'">'.number_format($end_balance,2).'</td>
            </tr>
            ';
        }
    }
         
        ?>
        <?php 
        }else{
        }
        
    }
        ?>
    </table>

</body>
</html>




