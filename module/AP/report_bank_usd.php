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
    $end_date = date("d F Y",strtotime($_GET['end_date'])); 

//     $sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
// $rowxss = mysqli_fetch_array($sqlxss);
// $maxidss = $rowxss['id'];

// $sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'HARIAN'");
// $rowyss = mysqli_fetch_array($sqlyss);
$sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
$rowsaldo = mysqli_fetch_array($sqlsaldo);
$saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

$sqlsaldoidr = mysqli_query($conn1,"select eqv_idr from b_saldoawal_bank where account = '$accountid'");
$rowsaldoidr = mysqli_fetch_array($sqlsaldoidr);
$saldo_ = isset($rowsaldoidr['eqv_idr']) ? $rowsaldoidr['eqv_idr'] : 0;
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
            <th style="text-align: right;vertical-align: middle;width: 10%;"><?php echo number_format($saldo_awal,2); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"><?php echo number_format($saldo_,2); ?></th>                                                                            
        </tr>
        <?php
    $date_now = date("Y-m-d");                             
    $nama_bank=$_GET['nama_bank'];
    $accountid=$_GET['accountid'];
    $curren=$_GET['curren'];   
    $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $end_date = date("Y-m-d",strtotime($_GET['end_date']));                 
   
     $sql = mysqli_query($conn1,"select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr, balance from b_reportbank where akun = '$accountid' and curr = '$curren' and transaksi_date between '$start_date' and '$end_date' ");
    

    $sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where curr = '$curren'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$bal = $rowy['balance'];

$sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
$rowsaldo = mysqli_fetch_array($sqlsaldo);
$saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;
$balance = $bal + $saldo_awal;

$sqlsaldoidr = mysqli_query($conn1,"select eqv_idr from b_saldoawal_bank where account = '$accountid'");
$rowsaldoidr = mysqli_fetch_array($sqlsaldoidr);
$saldo_ = isset($rowsaldoidr['eqv_idr']) ? $rowsaldoidr['eqv_idr'] : 0;

$sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowxss = mysqli_fetch_array($sqlxss);
$maxidss = $rowxss['id'];

$sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'HARIAN'");
$rowyss = mysqli_fetch_array($sqlyss);
$rates = $rowyss['rate'];

$saldoawl = ($bal * $rates) + $saldo_;


   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];
    $blc = $row['balance'];

    $balanc = $saldo_awal + $blc;
    $balanceidr =  $saldo_ + ($blc* $rates);

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
            <td style="text-align:right;" value = "'.$balanc.'">'.number_format($balanc,2).'</td> 
            <td style="text-align:right;" value = "'.$balanceidr.'">'.number_format($balanceidr,2).'</td>        
             ';
            
        // }


}
echo '
            <tr >
           <th style="text-align: center;vertical-align: middle;width: 10%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 70%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($balance,2).'</th></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($saldoawl,2).'</th>
        </tr>';

?>
    </table>

</body>
</html>




