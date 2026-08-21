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
    $end_date2 = date("Y-m-d",strtotime($_GET['end_date']));

    $sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
    $rowsaldo = mysqli_fetch_array($sqlsaldo);
    $saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

    $sqlxss2 = mysqli_query($conn1,"select curr  from b_masterbank where bank_account = '$accountid'");
    $rowxss2 = mysqli_fetch_array($sqlxss2);
    $curren1 = isset($rowxss2['curr']) ? $rowxss2['curr'] : null;

    // Baris penyesuaian selisih kurs (curr = IDR, dari auto jurnal selisih
    // kurs) TIDAK ikut menambah/mengurangi saldo NATIVE - debit/credit
    // di-nol-kan dulu (IF curr = curr asli akun) sebelum masuk running
    // total saldo native, persis pola bankreport.php.
    $sqlyss1 = mysqli_query($conn1,"select nomor,date,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,curr, IF(no_doc like 'FX/%', 0, debit) as debit, IF(no_doc like 'FX/%', 0, credit) as credit from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date2' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $saldo_awal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
    $rowyss1 = mysqli_fetch_array($sqlyss1);
    $saldoawal = isset($rowyss1['saldoawal']) ? $rowyss1['saldoawal'] : 0;

    // Kurs Saldo Awal (Beginning Balance) = HARIAN, di tanggal SEBELUM
    // start_date (rate HARIAN terdekat yang tersedia sebelum start_date) -
    // persis bankreport.php, bukan PAJAK exact-match seperti sebelumnya.
    $sqlrates = mysqli_query($conn1,"select rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal < '$start_date2' order by tanggal desc limit 1");
    $rowrates = mysqli_fetch_array($sqlrates);
    $rates = isset($rowrates['rate']) ? $rowrates['rate'] : 1;

    $saldo_ = $saldoawal * $rates;
    ?>

    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <td style="text-align:left;font-size:14px;font-weight:bold;border:none;">REPORT BANK ACCOUNT BALANCE</td>
        </tr>
        <tr>
            <td style="text-align:left;font-size:11px;font-weight:bold;border:none;">PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></td>
        </tr>
        <tr>
            <td style="border:none;">&nbsp;</td>
        </tr>
         <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 9%;">Name Bank</th>
            <th style="text-align: left;vertical-align: middle;width: 9%;">: <?php echo $nama_bank; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 9%;">Bank Account</th>
            <th style="text-align: left;vertical-align: middle;width: 9%;">: <?php echo $accountid; ?></th>
            <th style="text-align: center;vertical-align: middle;width: 13%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 13%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 9%;">Currency</th>
            <th colspan="4" style="text-align: left;vertical-align: middle;width: 20%;">: <?php echo $curren; ?></th>
        </tr>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 9%;">Transaction Date</th>
            <th style="text-align: center;vertical-align: middle;width: 9%;">Journal No</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 22%;">Description</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Category</th>
            <th style="text-align: center;vertical-align: middle;width: 8%;">Debit</th>
            <th style="text-align: center;vertical-align: middle;width: 8%;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 8%;">Balance</th>
            <th style="text-align: center;vertical-align: middle;width: 8%;">Debit IDR</th>
            <th style="text-align: center;vertical-align: middle;width: 8%;">Credit IDR</th>
            <th style="text-align: center;vertical-align: middle;width: 9%;">Balance Eq IDR</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 9%;">Beginning Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 57%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 8%;"><?php echo number_format($saldoawal,2); ?></th>
            <th colspan="2" style="text-align: center;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 9%;"><?php echo number_format($saldo_,2); ?></th>
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
        (select transaksi_date as date, no_doc as doc_num,deskripsi,curr, IF(no_doc like 'FX/%', 0, debit) as debit, IF(no_doc like 'FX/%', 0, credit) as credit from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $swl ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl2 = mysqli_fetch_array($sqlswl2);
     $saldoswal = isset($rowswl2['saldoawal']) ? $rowswl2['saldoawal'] : 0;

     $sqlswl4 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,curr, IF(no_doc like 'FX/%', 0, debit) as debit, IF(no_doc like 'FX/%', 0, credit) as credit from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $swl ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl4 = mysqli_fetch_array($sqlswl4);
     $saldoswal2 = isset($rowswl4['saldoawal']) ? $rowswl4['saldoawal'] : 0;

     $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,curr, IF(no_doc like 'FX/%', 0, debit) as debit, IF(no_doc like 'FX/%', 0, credit) as credit from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $saldoswal2,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
     $rows6 = mysqli_fetch_array($sql6);
     $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : $saldoswal2;

     // Kurs Saldo Akhir (Ending Balance) = HARIAN, di tanggal AKHIR FILTER
     // ($end_date) - persis bankreport.php.
     $sqlrates3 = mysqli_query($conn1,"select rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal <= '$end_date' order by tanggal desc limit 1");
     $rowrates3 = mysqli_fetch_array($sqlrates3);
     $rates3 = isset($rowrates3['rate']) ? $rowrates3['rate'] : 1;
     $sal_akhir = $saldoakhir * $rates3;

     // orig_debit/orig_credit = nilai ASLI (sebelum di-nol-kan) - dipakai
     // utk baris selisih kurs (no_doc "FX/...") yang nilainya SUDAH dalam
     // IDR. debit/credit (di-nol-kan kalau no_doc "FX/...") dipakai utk
     // saldo NATIVE (saldo_akhir). Exclude berdasarkan no_doc (BUKAN curr) -
     // pernah ketemu baris transaksi asli yang curr-nya kosong/salah input
     // (data lama), no_doc "FX/..." lebih aman krn cuma cocok baris yang
     // benar-benar dibuat auto jurnal selisih kurs.
     $sql = mysqli_query($conn1," SELECT '',q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit,q1.orig_credit,q1.orig_debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
FROM
   (select id, transaksi_date as date, no_doc as doc_num,deskripsi,curr,
           debit as orig_debit, credit as orig_credit,
           IF(no_doc like 'FX/%', 0, debit) as debit,
           IF(no_doc like 'FX/%', 0, credit) as credit
    from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel' order by transaksi_date asc) AS q1 JOIN
     (SELECT @runtot:= $saldoswal) runtot order by date,id asc");


   // "Balance Eq IDR" per baris = saldo BUKU, running - numpuk dari
   // Beginning Balance Eq IDR ($saldo_). Transaksi NATIVE (curr = curren1)
   // dikonversi pakai kurs PAJAK di tanggal transaksinya sendiri (rate yang
   // sesungguhnya dipakai saat transaksi diposting ke GL). Baris selisih
   // kurs (curr = IDR) nilainya SUDAH dalam IDR - dipakai apa adanya -
   // persis bankreport.php.
   $runningBalanceIdr = $saldo_;
   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];
    $isFxRow = (strpos($row['doc_num'], 'FX/') === 0);

    if ($isFxRow) {
        $debitIdr = (float) $row['orig_debit'];
        $creditIdr = (float) $row['orig_credit'];
    } else {
        $sqlratespjk = mysqli_query($conn1,"select rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal <= '".$row['date']."' order by tanggal desc limit 1");
        $rowratespjk = mysqli_fetch_array($sqlratespjk);
        $ratepjk = isset($rowratespjk['rate']) ? $rowratespjk['rate'] : 1;
        $debitIdr = (float) $debit * $ratepjk;
        $creditIdr = (float) $credit * $ratepjk;
    }

    $runningBalanceIdr += $debitIdr - $creditIdr;

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

    $t_debit_idr = $debitIdr == 0 ? '' : number_format($debitIdr,2);
    $t_credit_idr = $creditIdr == 0 ? '' : number_format($creditIdr,2);

        echo '<tr style="font-size:12px;text-align:center;">
            <td value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>
            <td value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td colspan="3" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td>
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.number_format($row['saldo_akhir'],2).'</td>
            <td style="text-align:right;" value = "'.$debitIdr.'">'.$t_debit_idr.'</td>
            <td style="text-align:right;" value = "'.$creditIdr.'">'.$t_credit_idr.'</td>
            <td style="text-align:right;" value = "'.$runningBalanceIdr.'">'.number_format($runningBalanceIdr,2).'</td>
             ';


}
echo '
            <tr >
           <th style="text-align: center;vertical-align: middle;width: 9%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 57%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 8%;">'.number_format($saldoakhir,2).'</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 9%;">'.number_format($sal_akhir,2).'</th>
        </tr>';

?>
    </table>

</body>
</html>
