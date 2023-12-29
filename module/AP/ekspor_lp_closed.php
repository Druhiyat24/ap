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
    header("Content-Disposition: attachment; filename=kontrabon.xls");
    $nama_supp =$_GET['nama_supp'];
    $status =$_GET['status'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>DATA KONTRABON <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
    STATUS: <?php echo $status; ?>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
             <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No List Payment</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Date</th>
            <th style="text-align: center; vertical-align: middle;">Total Kontrabon</th>
            <th style="text-align: center; vertical-align: middle;">Amount</th>
            <th style="text-align: center; vertical-align: middle;">Outsatnding</th>                       
            <th style="text-align: center; vertical-align: middle;">Currency</th>                                    
            <th style="text-align: center; vertical-align: middle;">Status</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $status =$_GET['status'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select * from ((select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where tgl_payment between '$start_date' and '$end_date'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and tgl_pay between '$start_date' and '$end_date' group by no_pay)) as b where b.status = 'Closed'");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td value = "'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




