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
    header("Content-Disposition: attachment; filename=general-ledger.xls");
    include '../../conn/conn.php';
    $coa_number =strtolower($_GET['coa_number']);
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    $sql3 = mysqli_query($conn2," select nama_coa from mastercoa_v2 where no_coa = '$coa_number'");
  $row3 = mysqli_fetch_array($sql3);
  $nama_coa = isset($row3['nama_coa']) ? $row3['nama_coa'] : null;
     ?>

        <h4>COA NUMBER : <?php echo $coa_number ?> <br/> COA NAME : <?php echo $nama_coa ?> <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No Journal</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Descriptions</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Saldo</th>
        </tr>
        <?php 
        // koneksi database
        $coa_number =strtolower($_GET['coa_number']);
        $kata_filter =strtolower($_GET['kata_filter']);
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $sql2 = mysqli_query($conn2," select $kata_filter as saldo from saldo_awal_tb where no_coa = '$coa_number'");
  $row = mysqli_fetch_array($sql2);
  $saldoawal = isset($row['saldo']) ? $row['saldo'] : 0;
  
        $sql = mysqli_query($conn2,"SELECT '',q1.no_journal,q1.tgl_journal,q1.keterangan,q1.credit_idr,q1.debit_idr, (@runtot :=@runtot + q1.debit_idr - q1.credit_idr) AS saldo_akhir
FROM
   (select no_journal,tgl_journal,keterangan,ROUND(credit * rate,2) credit_idr,ROUND(debit * rate,2) debit_idr from tbl_list_journal where no_coa = '$coa_number' and tgl_journal BETWEEN '$start_date' and '$end_date' and status != 'Cancel' order by tgl_journal,id ASC) AS q1 JOIN
     (SELECT @runtot:= $saldoawal) runtot");


        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : center;" value = "">-</td>
            <td style="text-align : center;" value = "">-</td>
            <td style="text-align : left;" value = "">SALDO AWAL</td>
            <td style="text-align : left;" value = "">-</td>
            <td style="text-align : left;" value = "">-</td>
            <td style="text-align : right;" value = "">'.number_format($saldoawal,2).'</td>
            </tr>
            ';
$limit = 0;
    while($row2 = mysqli_fetch_array($sql)){
        $limit++;
        $sql3 = mysqli_query($conn2,"select (debit - credit) saldo2 from (select sum(debit_idr) debit, sum(credit_idr) credit from(select no_journal,tgl_journal,keterangan,(rate * debit) debit_idr,(rate * credit) credit_idr from tbl_list_journal where no_coa = '$coa_number' and tgl_journal BETWEEN '$start_date' and '$end_date' and status != 'Cancel' order by tgl_journal,id asc limit $limit) a) a");
        $row3 = mysqli_fetch_array($sql3);
        $saldo = isset($row3['saldo2']) ? $row3['saldo2'] : 0;
        $saldoakhir = $saldoawal + $saldo;

        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$row2['no_journal'].'">'.$row2['no_journal'].'</td>
            <td style="text-align : center;" value = "'.$row2['tgl_journal'].'">'.$row2['tgl_journal'].'</td>
            <td style="text-align : left;" value = "'.$row2['keterangan'].'">'.$row2['keterangan'].'</td>
            <td style="text-align : right;" value = "'.$row2['debit_idr'].'">'.number_format($row2['debit_idr'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['credit_idr'].'">'.number_format($row2['credit_idr'],2).'</td>
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




