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
    header("Content-Disposition: attachment; filename=Report Bank.xls");
     include '../../conn/conn.php'; 
    $nama_bank=$_GET['nama_bank'];
    $accountid=$_GET['accountid'];
    $curren=$_GET['curren'];   
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $start_date2 = date("Y-m-d",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); 

    $sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
    $rowsaldo = mysqli_fetch_array($sqlsaldo);
    $saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

    $sqlxss2 = mysqli_query($conn1,"select curr  from b_masterbank where bank_account = '$accountid'");
    $rowxss2 = mysqli_fetch_array($sqlxss2);
    $curren1 = isset($rowxss2['curr']) ? $rowxss2['curr'] : null;

    $sqlyss1 = mysqli_query($conn1,"select nomor,date,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date2' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $saldo_awal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
    $rowyss1 = mysqli_fetch_array($sqlyss1);
    $saldoawal = isset($rowyss1['saldoawal']) ? $rowyss1['saldoawal'] : 0;
    $dateswal = isset($rowyss1['date']) ? $rowyss1['date'] : null;

    $sqlrates = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateswal'");
    $rowrates = mysqli_fetch_array($sqlrates);
    $maxidrate = isset($rowrates['id']) ? $rowrates['id'] : null;

    if ($maxidrate != null) {
        $rates = $rowrates['rate'];
    }else{
    $sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
    $rowxss = mysqli_fetch_array($sqlxss);
    $maxidss = $rowxss['id'];

    $sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and    v_codecurr = 'HARIAN'");
    $rowyss = mysqli_fetch_array($sqlyss);
    $rates = isset($rowyss['rate']) ? $rowyss['rate'] : 0;
    }

    $saldo_ = $saldoawal * $rates;
    ?>

    <center>
        <h4>REPORT BANK ACCOUNT BALANCE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
         <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 10%;">Name Bank</th>
            <th style="text-align: left;vertical-align: middle;width: 10%;">: <?php echo $nama_bank; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Bank Account</th>
            <th style="text-align: left;vertical-align: middle;width: 10%;">: <?php echo $accountid; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 20%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Currency</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 20%;">: <?php echo $curren; ?></th>                                                                            
        </tr>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 10%;">Transaction Date</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Journal No</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;width: 20%;">Description</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Category</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Cash Flow Category</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Debit</th> 
            <th style="text-align: center;vertical-align: middle;width: 10%;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Balance</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Balance Eq IDR</th>                                                                            
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Beginning Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 70%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"><?php echo number_format($saldoawal,2); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"><?php echo number_format($saldo_,2); ?></th>                                                                            
        </tr>
        <?php
    $date_now = date("Y-m-d");                             
    $nama_bank=$_GET['nama_bank'];
    $accountid=$_GET['accountid'];
    $curren=$_GET['curren'];   
    $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $end_date = date("Y-m-d",strtotime($_GET['end_date']));                 
   
    $sqlswl = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
     $rowswl = mysqli_fetch_array($sqlswl);
     $swl = isset($rowswl['amount']) ? $rowswl['amount'] : 0;
    
     $sqlswl2 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $swl ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl2 = mysqli_fetch_array($sqlswl2);
     $saldoswal = isset($rowswl2['saldoawal']) ? $rowswl2['saldoawal'] : 0;

     $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date') AS q1 JOIN
        (SELECT @runtot:= $saldoswal,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
     $rows6 = mysqli_fetch_array($sql6);
     $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : 0;
     $dateakhir = isset($rows6['date']) ? $rows6['date'] : 0;

     $sqlrates = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateakhir'");
    $rowrates = mysqli_fetch_array($sqlrates);
    $maxidrate3 = isset($rowrates['id']) ? $rowrates['id'] : null;

    if ($maxidrate3 != null) {
        $rates3 = $rowrates['rate'];
    }else{
    $sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
    $rowxss = mysqli_fetch_array($sqlxss);
    $maxidss = $rowxss['id'];

    $sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and    v_codecurr = 'HARIAN'");
    $rowyss = mysqli_fetch_array($sqlyss);
    $rates3 = isset($rowyss['rate']) ? $rowyss['rate'] : 0;
    }
    $sal_akhir = $saldoakhir * $rates3;

     $sql = mysqli_query($conn1," SELECT '',q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
     (SELECT @runtot:= $saldoswal) runtot ");


   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];
    $tglswal = $row['date'];

    $sqlrates = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$tglswal'");
    $rowrates = mysqli_fetch_array($sqlrates);
    $maxidrate2 = isset($rowrates['id']) ? $rowrates['id'] : null;

    if ($maxidrate2 != null) {
        $rates2 = $rowrates['rate'];
    }else{
    $sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
    $rowxss = mysqli_fetch_array($sqlxss);
    $maxidss = $rowxss['id'];

    $sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and    v_codecurr = 'HARIAN'");
    $rowyss = mysqli_fetch_array($sqlyss);
    $rates2 = isset($rowyss['rate']) ? $rowyss['rate'] : 0;
    }

    if($debit == '0'){
        $t_debit = '';
    }else{
        $t_debit = number_format($row['debit'],2);
    }

    if($credit == '0'){
        $t_credit = '';
    }else{
        $t_credit = number_format($row['credit'],2);
    }
    
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                            
            <td value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td colspan="2" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td> 
            <td value=""></td>                            
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>         
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.$row['saldo_akhir'].'</td>
            <td style="text-align:right;" value = "'.($row['saldo_akhir'] * $rates2).'">'.($row['saldo_akhir'] * $rates2).'</td>        
             ';
            
        // }


}
echo '
            <tr >
           <th style="text-align: center;vertical-align: middle;width: 10%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 70%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($saldoakhir,2).'</th></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($sal_akhir,2).'</th>
        </tr>';

?>
    </table>

</body>
</html>




