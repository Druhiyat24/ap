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
    header("Content-Disposition: attachment; filename=request debitnote.xls");
    $nama_supp =$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>DATA REQUEST DEBIT NOTE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Request</th>
            <th style="text-align: center;vertical-align: middle;">Request Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">Item Desc</th>
            <th style="text-align: center;vertical-align: middle;">Qty</th>
            <th style="text-align: center;vertical-align: middle;">Price</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Attn</th>
            <th style="text-align: center;vertical-align: middle;">Seasons</th>
            <th style="text-align: center;vertical-align: middle;">Reff/Style</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">No DN</th>
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
  

        $sql = mysqli_query($conn2,"select a.no_req,tgl_req,nama_supp,no_po,item,qty,price,(qty * price) total_amount,attn,seasons,no_reff,deskripsi,status,CONCAT(a.created_by,' (',a.created_date,')') create_user,if(no_dn is null,'-',no_dn) no_dn from req_dn_h a INNER JOIN req_dn b on b.no_req = a.no_req where tgl_req between '$start_date' and '$end_date'");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align: left" value = "'.$row['no_req'].'">'.$row['no_req'].'</td>
            <td style="text-align: center" value = "'.$row['tgl_req'].'">'.date("d-M-Y",strtotime($row['tgl_req'])).'</td>
            <td style="text-align: left"value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align: left"value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
            <td style="text-align: left"value = "'.$row['item'].'">'.$row['item'].'</td>
            <td style="text-align:right;" value = "'.$row['qty'].'">'.number_format($row['qty'],2).'</td>
            <td style="text-align:right;" value = "'.$row['price'].'">'.number_format($row['price'],2).'</td>
            <td style="text-align:right;" value = "'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
            <td style="text-align: left"value = "'.$row['attn'].'">'.$row['attn'].'</td>
            <td style="text-align: left"value = "'.$row['seasons'].'">'.$row['seasons'].'</td>
            <td style="text-align: left"value = "'.$row['no_reff'].'">'.$row['no_reff'].'</td>
            <td style="text-align: left" value = "'.$row['status'].'">'.$row['status'].'</td> 
            <td style="text-align: left" value = "'.$row['no_dn'].'">'.$row['no_dn'].'</td> 
            <td style="text-align: left" value = "'.$row['deskripsi'].'">'.$row['deskripsi'].'</td> 
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




