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
    header("Content-Disposition: attachment; filename=Data Pelunasan Hutang & Biaya.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

     ?>

        <h4>REKAP PELUNASAN HUTANG DAN BIAYA <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Payment</th>
            <th style="text-align: center;vertical-align: middle;">Payment Date</th>
            <th style="text-align: center;vertical-align: middle;">Create Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Curr LP</th>
            <th style="text-align: center;vertical-align: middle;">Total LP</th>
            <th style="text-align: center;vertical-align: middle;">Payment Method</th>
            <th style="text-align: center;vertical-align: middle;">Account</th>
            <th style="text-align: center;vertical-align: middle;">Bank</th>
            <th style="text-align: center;vertical-align: middle;">Curr Payment</th>
            <th style="text-align: center;vertical-align: middle;">Rate</th>
            <th style="text-align: center;vertical-align: middle;">Nominal Payment</th>
            <th style="text-align: center;vertical-align: middle;">Keterangan</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

  
        $sql = mysqli_query($conn2,"select No_Payment,Payment_date,create_date,Supplier,Curr_LP,Total_LP,cara_bayar,account,bank,valuta_bayar,if(rate <= 1,1,rate) rate,nominal_payment,keterangan from (
select payment_ftr_id as No_Payment, tgl_pelunasan as Payment_date, create_date, nama_supp as Supplier, valuta_ftr as Curr_LP, sum(ttl_bayar) as Total_LP, cara_bayar, account, bank, valuta_bayar,rate, if(valuta_bayar = 'IDR' ,SUM(nominal), SUM(nominal_fgn)) as nominal_payment, keterangan,status from payment_ftr where tgl_pelunasan BETWEEN '$start_date' and '$end_date' and status != 'Cancel' group by payment_ftr_id
union
select a.no_bankout,a.bankout_date,a.create_date,a.nama_supp,b.curr,sum(b.total),'TRANSFER',a.akun,a.bank,a.curr,b.rates,sum(b.for_balance),a.status,b.no_reff from b_bankout_h a inner join b_bankout_det b on b.no_bankout = a.no_bankout where bankout_date BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.no_reff not like '%PV%' group by a.no_bankout
union
select a.no_pco,a.tgl_pco,a.create_date,a.nama_supp,b.curr,sum(b.total),'TUNAI','KAS KECIL' akun,'KAS' bank,a.curr,'1' rates,sum(b.total),a.status,b.no_reff from c_petty_cashout_h a inner join c_petty_cashout_det b on b.no_pco = a.no_pco where a.tgl_pco BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.no_reff not like '%PV%' group by a.no_pco
union
select a.no_bankout,a.bankout_date,a.create_date,a.nama_supp,b.curr,sum(b.total),'TRANSFER',a.akun,a.bank,a.curr,b.rates,sum(b.total),a.status,b.no_reff from b_bankout_h a inner join b_bankout_det b on b.no_bankout = a.no_bankout where b.no_reff like '%PV%' and a.bankout_date BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' group by a.no_bankout) a");


$no = 0;
    while($row2 = mysqli_fetch_array($sql)){
        $no++;
        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['No_Payment'].'">'.$row2['No_Payment'].'</td>
            <td style="text-align : left;" value = "'.$row2['Payment_date'].'">'.$row2['Payment_date'].'</td>
            <td style="text-align : left;" value = "'.$row2['create_date'].'">'.$row2['create_date'].'</td>
            <td style="text-align : left;" value = "'.$row2['Supplier'].'">'.$row2['Supplier'].'</td>
            <td style="text-align : left;" value = "'.$row2['Curr_LP'].'">'.$row2['Curr_LP'].'</td>
            <td style="text-align : right;" value = "'.$row2['Total_LP'].'">'.$row2['Total_LP'].'</td>
            <td style="text-align : left;" value = "'.$row2['cara_bayar'].'">'.$row2['cara_bayar'].'</td>
            <td style="text-align : left;" value = "'.$row2['account'].'">'.$row2['account'].'</td>
            <td style="text-align : left;" value = "'.$row2['bank'].'">'.$row2['bank'].'</td>
            <td style="text-align : left;" value = "'.$row2['valuta_bayar'].'">'.$row2['valuta_bayar'].'</td>
            <td style="text-align : right;" value = "'.$row2['rate'].'">'.$row2['rate'].'</td>
            <td style="text-align : right;" value = "'.$row2['nominal_payment'].'">'.$row2['nominal_payment'].'</td>
            <td style="text-align : left;" value = "'.$row2['keterangan'].'">'.$row2['keterangan'].'</td>
            </tr>
            ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




