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
    header("Content-Disposition: attachment; filename=Other Receivable Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

     ?>

        <h4>OTHER RECEIVABLE REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Memo</th>
            <th style="text-align: center;vertical-align: middle;">Memo Date</th>
            <th style="text-align: center;vertical-align: middle;">No Invoice Vendor</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">No DN</th>
            <th style="text-align: center;vertical-align: middle;">DN Date</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (ALK)</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
            <th style="text-align: center;vertical-align: middle;">Forex Gain / (Loss)</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

  
        $sql = mysqli_query($conn2,"SELECT nm_memo,tgl_memo,no_invoice,supplier,buyer,no_dn,tgl_dn,IF(tgl_memo < '$start_date',total_memo,0) beg_balance, IF(tgl_memo >= '$start_date',total_memo,0) addition,total_alk deduction, total_alk2 deduction2,total_dn,total_dn_alk,total_dn_alk2 FROM (SELECT a.nm_memo,tgl_memo,no_invoice,supplier,buyer,total_memo,IF(no_dn is null,'-',no_dn) no_dn,IF(tgl_dn is null,'-',tgl_dn) tgl_dn,COALESCE(val_memodn,0) total_memo_dn,COALESCE(b.rate,1) rate_dn, COALESCE(val_dn,0) total_dn,IF(no_alk is null,'-',no_alk) no_alk,IF(tgl_alk is null,'-',tgl_alk) tgl_alk,COALESCE(val_dn_alk,0) total_dn_alk,COALESCE(c.rate,1) rate_alk, COALESCE(val_alk,0) total_alk,IF(no_alk_bfr is null,'-',no_alk_bfr) no_alk_bfr,IF(tgl_alk_bfr is null,'-',tgl_alk_bfr) tgl_alk_bfr,COALESCE(val_dn_alk_bfr,0) total_dn_alk2,COALESCE(d.rate_bfr,1) rate_alk2, COALESCE(val_alk_bfr,0) total_alk2 FROM (
SELECT nm_memo, tgl_memo,no_invoice,supplier,buyer,total_memo from (SELECT b.id,a.id_supplier,d.supplier,g.supplier buyer,a.nm_memo, a.tgl_memo,IF(b.inv_vendor is null,'-',b.inv_vendor) no_invoice,a.curr,sum(b.biaya) total_memo,'' id_book from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join mastersupplier g on g.id_supplier =a.id_buyer inner join memo_det b on b.id_h = a.id_h where  a.nm_memo >= 'MEMO/NAG/2310/01039' and a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and a.ditagihkan = 'Y' and b.cancel = 'N' and a.status NOT IN ('DRAFT','CANCEL') and b.nm_sub_ctg != 'VAT' GROUP BY a.nm_memo order by a.nm_memo asc ) a) a left join

(SELECT no_dn,tgl_dn,sum(value) val_memodn,rate,SUM(amount) val_dn,nm_memo from (SELECT DISTINCT b.id,c.no_dn,e.tgl_dn,c.value,c.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn where a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and b.cancel = 'N' and b.nm_sub_ctg != 'VAT' and e.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo) b on b.nm_memo = a.nm_memo left join

(SELECT no_alk,tgl_alk,sum(value) val_alk,rate,SUM(amount) val_dn_alk,nm_memo from (SELECT DISTINCT b.id,g.no_alk,g.tgl_alk,(g.rate * c.amount) value,g.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk BETWEEN '$start_date' and '$end_date' and b.cancel = 'N' and b.nm_sub_ctg != 'VAT' and g.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo ) c on c.nm_memo = a.nm_memo left join

(SELECT no_alk_bfr,tgl_alk_bfr,val_alk_bfr,rate_bfr,val_dn_alk_bfr,nm_memo_bfr from (SELECT no_alk no_alk_bfr,tgl_alk tgl_alk_bfr,sum(value) val_alk_bfr,rate rate_bfr,SUM(amount) val_dn_alk_bfr,nm_memo nm_memo_bfr from (SELECT DISTINCT b.id,g.no_alk,g.tgl_alk,(g.rate * c.amount) value,g.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk < '$start_date' and b.cancel = 'N' and b.nm_sub_ctg != 'VAT' and g.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo 
union
select no_journal,tgl_journal,sum((credit * rate) - (debit * rate)) total, rate,((credit * rate) - (debit * rate)) total2, reff_doc from tbl_list_journal where no_coa = '1.34.04' and no_journal like '%GM/%' and tgl_journal < '$start_date') a GROUP BY nm_memo_bfr) d on d.nm_memo_bfr = a.nm_memo) a");

        $ttl_beg =0;
        $ttl_add =0;
        $ttl_ded =0;
        $ttl_for =0;
        $ttl_end =0;
        $no = 0;
        $ttl_mj =0;
    while($row2 = mysqli_fetch_array($sql)){
        $no++;
        $nm_memo = $row2['nm_memo'];
        $sql_mj_b = mysqli_query($conn2,"select no_journal,tgl_journal,sum((credit * rate) - (debit * rate)) total, rate,((credit * rate) - (debit * rate)) total2, reff_doc from tbl_list_journal where no_coa = '1.34.04' and no_journal like '%GM/%' and tgl_journal < '$start_date' and reff_doc = '$nm_memo'");
        $row_mj_b = mysqli_fetch_array($sql_mj_b);
        $val_mj_b = isset($row_mj_b['total']) ? $row_mj_b['total'] : 0;

        if ($val_mj_b > 0) {
             $beg_balance = $row2['beg_balance'] - $val_mj_b;
        }else{
             $beg_balance = $row2['beg_balance'];
        }

        // $beg_balance = $row2['beg_balance'];
        $addition = $row2['addition'];
        $deduction = $row2['deduction'];
        $deduction2 = $row2['deduction2'];

        $sql_mj = mysqli_query($conn2,"select no_journal,tgl_journal,sum((credit * rate) - (debit * rate)) total, rate,((credit * rate) - (debit * rate)) total2, reff_doc from tbl_list_journal where no_coa = '1.34.04' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' and reff_doc = '$nm_memo'");
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
            <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_dn'].'">'.$row2['no_dn'].'</td>
            <td style="text-align : left;" value = "'.$row2['tgl_dn'].'">'.$row2['tgl_dn'].'</td>
            <td style="text-align : right;" value = "'.$beg_balance.'">'.number_format($beg_balance,2).'</td>
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
        
    }
        ?>
    </table>

</body>
</html>




