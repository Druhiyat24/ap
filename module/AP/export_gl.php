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
    $profit_center =strtolower($_GET['profit_center']);

    $sql3 = mysqli_query($conn2," select nama_coa from mastercoa_v2 where no_coa = '$coa_number'");
    $row3 = mysqli_fetch_array($sql3);
    $nama_coa = isset($row3['nama_coa']) ? $row3['nama_coa'] : null;

    $sql_pc = mysqli_query($conn2," select nama_pc from master_pc where kode_pc = '$profit_center'");
    $row_pc = mysqli_fetch_array($sql_pc);
    $nama_pc = isset($row_pc['nama_pc']) ? $row_pc['nama_pc'] : null;
     ?>

        <h4>COA NUMBER : <?php echo $coa_number ?> <br/> PROFIT CENTER : <?php echo $nama_pc ?> <br/> COA NAME : <?php echo $nama_coa ?> <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table border="1" style="width:100%; table-layout:fixed; font-size:10px;">
        <tr>
            <th style="text-align: center;vertical-align: middle;width:50px;">No</th>
            <th style="text-align: center;vertical-align: middle;width:150px;">No Journal</th>
            <th style="text-align: center;vertical-align: middle;width:120px;">Date</th>
            <th style="text-align: center;vertical-align: middle;width:120px;">Profit Center</th>
            <th style="text-align: center;vertical-align: middle;width:200px;">Reff Document</th>
            <th style="text-align: center;vertical-align: middle;width:500px;">Descriptions</th>
            <th style="text-align: center;vertical-align: middle;width:200px;">Debit</th>
            <th style="text-align: center;vertical-align: middle;width:200px;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width:200px;">Saldo</th>

        </tr>
        <?php 
        // koneksi database
        $coa_number =strtolower($_GET['coa_number']);
        $profit_center =strtolower($_GET['profit_center']);
        $kata_filter = date("M_Y", strtotime($_GET['start_date']));
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

  
        $sql = mysqli_query($conn2,"SELECT * from (SELECT UPPER('$profit_center') nama_pc,'-' reff_doc, '-' no_journal, '-' tgl_journal, 'SALDO AWAL' keterangan, '0' credit_idr, '0' debit_idr, $kata_filter saldo_akhir FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number' and profit_center = '$profit_center'
UNION ALL
(SELECT profit_center nama_pc, reff_doc, q1.no_journal,q1.tgl_journal,q1.keterangan,q1.credit_idr,q1.debit_idr, (@runtot :=@runtot + q1.debit_idr - q1.credit_idr) AS saldo_akhir
FROM
   (select a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, IFNULL(NULLIF(reff_doc,''),'-') reff_doc, no_journal,tgl_journal,a.keterangan,ROUND(credit * rate,2) credit_idr,ROUND(debit * rate,2) debit_idr from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center where no_coa = '$coa_number' and tgl_journal BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and profit_center = '$profit_center' order by tgl_journal,a.id ASC) AS q1 JOIN
     (SELECT @runtot:= IFNULL(( SELECT $kata_filter FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number' and profit_center = '$profit_center'),0)) runtot ORDER BY tgl_journal ASC)) a ORDER BY tgl_journal ASC");

        $limit = 0;

    while($row2 = mysqli_fetch_array($sql)){
        $limit++;

        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : center;" value = "'.$limit.'">'.$limit.'</td>
            <td style="text-align : left;" value = "'.$row2['no_journal'].'">'.$row2['no_journal'].'</td>
            <td style="text-align : center;" value = "'.$row2['tgl_journal'].'">'.$row2['tgl_journal'].'</td>
            <td style="text-align : center;" value = "'.$row2['nama_pc'].'">'.$row2['nama_pc'].'</td>
            <td style="text-align : center;" value = "'.$row2['reff_doc'].'">'.$row2['reff_doc'].'</td>
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




