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
    $saldo_awal = 0;
    $saldoawal = 0;
    $sqlxss2 = mysqli_query($conn1,"select curr  from b_masterbank where bank_account = '$accountid'");
    $rowxss2 = mysqli_fetch_array($sqlxss2);
    $curren1 = isset($rowxss2['curr']) ? $rowxss2['curr'] : null;

    $sqlxss1 = mysqli_query($conn1,"select max(id) as id from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date2'");
    $rowxss1 = mysqli_fetch_array($sqlxss1);
    $maxidss1 = isset($rowxss1['id']) ? $rowxss1['id'] : null;

    $sqlyss1 = mysqli_query($conn1,"select balance from b_reportbank where id = '$maxidss1'");
    $rowyss1 = mysqli_fetch_array($sqlyss1);
    $balance = isset($rowyss1['balance']) ? $rowyss1['balance'] : 0;
    $saldoawal = $saldo_awal + $balance;
    ?>

    <center>
        <h4>REPORT BANK ACCOUNT BALANCE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table style="width:100%;font-size:12px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Name Bank</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: <?php echo $nama_bank; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Bank Account</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: <?php echo $accountid; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 23%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Currency</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: <?php echo $curren1; ?></th>                                                                            
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
   
     $sql = mysqli_query($conn1,"select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr, balance from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' order by date asc");
    

    $sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$bal = $rowy['balance'];

$saldo_awal = 0;
$balance = $bal + $saldo_awal;


   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];
    $blc = $row['balance'];

    $balanc = $saldo_awal + $blc;

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
            <td value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                            
            <td value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td colspan="3" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td> 
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>         
            <td style="text-align:right;" value = "'.$balanc.'">'.$balanc.'</td>         
             ';
            
        // }


}
echo '
            <tr  style="font-size:12px;">
            <th style="text-align: center;vertical-align: middle;width: 11%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 78%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 11%;">'.$balance.'</th></th>                                                                            
        </tr>';

?>
    </table>

</body>
</html>




