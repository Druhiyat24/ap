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
            <th style="text-align: center; vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center; vertical-align: middle;">Kontrabon Date</th>
            <th style="text-align: center; vertical-align: middle;">Subtotal</th>
            <th style="text-align: center; vertical-align: middle;">PPN</th>
            <th style="text-align: center; vertical-align: middle;">PPH</th>
            <th style="text-align: center; vertical-align: middle;">Total CBD/DP</th>                         
            <th style="text-align: center; vertical-align: middle;">Return</th>                                    
            <th style="text-align: center; vertical-align: middle;">Potongan</th>
            <th style="text-align: center; vertical-align: middle;">Total Kontrabon</th>                         
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
  

        $sql = mysqli_query($conn2,"select a.no_kbon, a.tgl_kbon, a.no_bpb,a.no_po,a.tgl_bpb,a.tgl_po, a.nama_supp, SUM(a.subtotal), SUM(a.tax) as tax, SUM(a.total), a.curr, a.create_user, a.status, a.tgl_tempo, a.no_faktur, a.supp_inv, a.tgl_inv, a.pph_code, SUM(a.pph_value) as pph_value, a.dp_value, a.pph_code, b.jml_return, b.jml_potong from kontrabon a INNER JOIN potongan b on b.no_kbon = a.no_kbon where a.tgl_kbon between '$start_date' and '$end_date' and a.no_bpb != '' and a.status = 'draft' group by a.no_kbon");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

            $kbonno = $row['no_kbon'];

    $sql45 = mysqli_query($conn2,"select a.no_kbon, (c.qty * c.price) * (d.tax / 100) as tax_return from kontrabon a left join bppb_new c on c.no_bpb = a.no_bpb left join bpb_new d on d.no_bpb = a.no_bpb where a.no_kbon = '$kbonno' and a.no_bpb != '' group by a.no_kbon");
    while($row45 = mysqli_fetch_array($sql45)){
        $tax_return = $row45['tax_return'];

            $sub = $row['SUM(a.subtotal)'];
            $tax1 = $row['tax'];
            $tax = $tax1 + $tax_return;
            $pph = $row['pph_value'];
            $dp = $row['dp_value'];
            $return1 = $row['jml_return'];
            $return = $return1 + $tax_return;
            $potong = $row['jml_potong'];
            $total = $sub + $tax - ($pph + $dp + $return) + $potong ;
            $ttl_potong = $potong;
            $status = $row['status'];


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
            <td style="text-align:right;" value = "'.$row['SUM(a.subtotal)'].'">'.number_format($row['SUM(a.subtotal)'],2).'</td>
            <td style="text-align:right;" value = "'.$tax.'">'.number_format($tax,2).'</td>
            <td style="text-align:right;" value = "'.$row['pph_value'].'">- '.number_format($row['pph_value'],2).'</td>
            <td style="text-align:right;" value = "'.$row['dp_value'].'">- '.number_format($row['dp_value'],2).'</td>            
            <td style="text-align:right;" value = "'.$return.'">'.number_format($return,2).'</td>
            <td style="text-align:right;" value = "'.$ttl_potong.'">'.number_format($ttl_potong,2).'</td>
            <td style="text-align:right;" value = "'.$total.'">'.number_format($total,2).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
             ';
         
        ?>
        <?php 
        }
    }
        ?>
    </table>

</body>
</html>




