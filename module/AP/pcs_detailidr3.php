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
    header("Content-Disposition: attachment; filename=Data_Reduction_Global.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table border="1" >
       <tr>
           <tr >
            <th rowspan="2" style="text-align: center;vertical-align: center;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">No payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; ">Payment Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">No List Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; ">List Payment Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">Currency</th>
            <th colspan="2" style="text-align: center;vertical-align: center; ">Amount </th>
           
            </tr>   
             <tr >
            <th style="text-align: center;vertical-align: center;">Ori Curr</th>
            <th style="text-align: center;vertical-align: center;width: 80px">IDR</th>                                                                            
        </tr>                                                                                       
        </tr>     
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
         $sql = mysqli_query($conn2,"select payment_ftr_id,tgl_pelunasan,nama_supp, list_payment_id, tgl_list_payment,valuta_bayar,IF(valuta_bayar = 'IDR',nominal,nominal_fgn) as oricurr, IF(valuta_bayar = 'IDR',nominal,(nominal_fgn * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan )))) as total_idr from payment_ftr where nama_supp = '$nama_supp' and tgl_pelunasan between '$start_date' and '$end_date' group by list_payment_id"); 

            $i = 0;
            $ttl_ori = 0;
            $ttl_idr = 0;
            while ($row = mysqli_fetch_assoc($sql)) {
                $i++;
                $ttl_ori += $row['oricurr'];
                $ttl_idr += $row['total_idr'];

            echo '<tr>   
            <td  value="'.$i.'">'.$i.'</td>                    
            <td value = "'.$row['payment_ftr_id'].'">'.$row['payment_ftr_id'].'</td>
            <td  value="'.$row['tgl_pelunasan'].'">'.date("d M Y",strtotime($row['tgl_pelunasan'])).'</td>
            <td value = "'.$row['list_payment_id'].'">'.$row['list_payment_id'].'</td>
            <td  value="'.$row['tgl_list_payment'].'">'.date("d M Y",strtotime($row['tgl_list_payment'])).'</td>
            <td value = "'.$row['valuta_bayar'].'">'.$row['valuta_bayar'].'</td>
            <td style="text-align:right;" value = "'.$row['oricurr'].'">'.number_format($row['oricurr'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['total_idr'].'">'.number_format($row['total_idr'],2).'</td>     
                       </tr>
                       ';

        ?>
        <?php 

        }

        echo  '<tr>
                       <td colspan = "6" style="text-align:center;"> TOTAL </td>
                       <td style="text-align:right;" value = "'.$ttl_ori.'">'.number_format($ttl_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>  
                       </tr>';
        ?>
    </table>

</body>
</html>




