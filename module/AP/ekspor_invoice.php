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
    header("Content-Disposition: attachment; filename=invoiced received.xls");
    $nama_supp =$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>DATA RECEIVED INVOICE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Document</th>
            <th style="text-align: center;vertical-align: middle;">Document Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Total Amount</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Keterangan</th>
            <th style="text-align: center;vertical-align: middle;">Create User</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select * from (select 'IR' id,doc_number,tgl_penerimaan,nama_supp,total_amount, IF(status != 'Cancel','Received','Cancel')status, CONCAT(created_by,' (',created_date,')') create_user, 'Received Invoice' keterangan from ir_invoice_supp_h
UNION
select 'TF' id,no_trans,tgl_trans,nama_supp,SUM(amount) amount,status,CONCAT(created_by,' (',created_date,')') create_user,CASE
    WHEN nama_trans = 'TFTA' and status = 'Approved' THEN 'Accept From Finance To Accounting'
        WHEN nama_trans = 'TFTA' and status = 'Post' THEN 'Transfer From Finance To Accounting'
        WHEN nama_trans = 'TFTA' and status = 'Cancel' THEN 'Cancel From Finance To Accounting'
        WHEN nama_trans = 'TATP' and status = 'Approved' THEN 'Accept From Accounting To Purchasing'
        WHEN nama_trans = 'TATP' and status = 'Post' THEN 'Transfer From Accounting To Purchasing'
        WHEN nama_trans = 'TATP' and status = 'Cancel' THEN 'Cancel From Accounting To Purchasing'
        WHEN nama_trans = 'TPTF' and status = 'Approved' THEN 'Accept From Purchasing To Finance'
        WHEN nama_trans = 'TPTF' and status = 'Post' THEN 'Transfer From Purchasing To Finance'
        WHEN nama_trans = 'TPTF' and status = 'Cancel' THEN 'Cancel From Purchasing To Finance'
END as keterangan from ir_trans_invoice_supp GROUP BY no_trans) a where tgl_penerimaan between '$start_date' and '$end_date'");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align: left" value = "'.$row['doc_number'].'">'.$row['doc_number'].'</td>
            <td style="text-align: center" value = "'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
            <td style="text-align: left"value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:right;" value = "'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
            <td style="text-align: left" value = "'.$row['status'].'">'.$row['status'].'</td> 
            <td style="text-align: left" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td> 
            <td style="text-align: left" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
            </tr>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




