<html>
<head>
    <title>Export Data Ke Excel </title>
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
    header("Content-Disposition: attachment; filename=-.xls");
    include '../../conn/conn.php'; 
    $nama_bank=$_GET['nama_bank'];
    $accountid=$_GET['accountid'];
    $curren=$_GET['curren'];   
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $start_date2 = date("Y-m-d",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); 

    $sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_pettycash where account = '$accountid'");
    $rowsaldo = mysqli_fetch_array($sqlsaldo);
    $saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

    $sqlxss2 = mysqli_query($conn1,"select curr  from c_petty_cashin_h where coa_akun = '$accountid'");
    $rowxss2 = mysqli_fetch_array($sqlxss2);
    $curren1 = isset($rowxss2['curr']) ? $rowxss2['curr'] : null;

    $sqlyss1 = mysqli_query($conn1,"select nomor,date,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
    (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from c_report_pettycash where akun = '$accountid' and transaksi_date < '$start_date2' and status != 'Cancel') AS q1 JOIN
     (SELECT @runtot:= $saldo_awal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
    $rowyss1 = mysqli_fetch_array($sqlyss1);
    $saldoawal = isset($rowyss1['saldoawal']) ? $rowyss1['saldoawal'] : 0;
    ?>

        <h4>REPORT CASH <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:12px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 22%;">Cash Account</th>
            <th style="text-align: left;vertical-align: middle;width: 22%;">: <?php echo $nama_bank; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 23%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Currency</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: IDR</th>                                                                            
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Transaction Date</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Journal No</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 30%;">Description</th>
            <th style="text-align: center;vertical-align: middle;width: 16%;">Category</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Debit</th> 
            <th style="text-align: center;vertical-align: middle;width: 11%;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Balance</th>                                                                            
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Beginning Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 78%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 11%;"><?php echo $saldoawal; ?></th>                                                                            
        </tr>
        <?php
    $date_now = date("Y-m-d");                              
    $nama_bank=$_GET['nama_bank'];
    $accountid=$_GET['accountid'];
    $curren=$_GET['curren'];   
    $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $end_date = date("Y-m-d",strtotime($_GET['end_date']));                 
   
     $sqlswl = mysqli_query($conn1,"select amount from b_saldoawal_pettycash where account = '$accountid'");
     $rowswl = mysqli_fetch_array($sqlswl);
     $swl = isset($rowswl['amount']) ? $rowswl['amount'] : 0;
    
     $sqlswl2 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from c_report_pettycash where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $swl ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl2 = mysqli_fetch_array($sqlswl2);
     $saldoswal = isset($rowswl2['saldoawal']) ? $rowswl2['saldoawal'] : 0;

     $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from c_report_pettycash where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $saldoswal,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
     $rows6 = mysqli_fetch_array($sql6);
     $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : 0;


     $sql = mysqli_query($conn1," SELECT '',q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from c_report_pettycash where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
     (SELECT @runtot:= $saldoswal) runtot ");


   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];


    if($debit == '0'){
        $t_debit = '';
    }else{
        $t_debit = $row['debit'];
    }

    if($credit == '0'){
        $t_credit = '';
    }else{
        $t_credit = $row['credit'];
    }
    
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="text-align:left;" value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                            
            <td style="text-align:left;" value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td style="text-align:left;" colspan="3" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td> 
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>         
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.$row['saldo_akhir'].'</td>         
             ';
            
        // }


}
echo '
            <tr  style="font-size:12px;">
            <th style="text-align: center;vertical-align: middle;width: 11%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 78%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 11%;">'.$saldoakhir.'</th></th>                                                                            
        </tr>';

?>
    </table>

</body>
</html>




