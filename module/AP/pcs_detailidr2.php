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
    header("Content-Disposition: attachment; filename=Data_Addition_Global2.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table border="1" >
       <tr>
            <th rowspan="2" style="text-align: center;vertical-align: center;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; ">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; ">PO Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: center;">Currency</th>
            <th colspan="2" style="text-align: center;vertical-align: center; ">Amount </th>
           
            </tr>   
             <tr>
            <th style="text-align: center;vertical-align: center;">Ori Curr</th>
            <th style="text-align: center;vertical-align: center;width: 80px">IDR</th>                                                                            
        </tr>     
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
         $sql = mysqli_query($conn2,"select a.supplier,a.no_bpb, a.tgl_bpb, a.pono, a.tgl_po, a.curr,sum(round((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100)),4)) as oricurr, if(a.curr = 'IDR',sum(round((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100)),4)),if(a.is_invoiced != 'Invoiced',sum(round((a.qty * (a.price * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_bpb))) + ((a.qty * (a.price * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_bpb))) * (a.tax / 100)),4)), ((b.total + b.pph_value) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ))))) as total_idr from bpb_new a left join kontrabon b on b.no_bpb = a.no_bpb where is_invoiced != 'Cancel' and a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' || is_invoiced != 'Cancel' and a.supplier = '$nama_supp' and b.tgl_kbon between '$start_date' and '$end_date' group by a.no_bpb");

     $sqlaa = mysqli_query($conn2,"select a.nama_supp, if(b.curr = 'IDR', sum(jml_return),sum(jml_return * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon )))) as returnidr, sum(jml_return) as returnusd, if(b.curr = 'IDR', sum(jml_potong),sum(jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon )))) as potongidr, sum(jml_potong) as potongusd from potongan a inner join  kontrabon_h b on b.no_kbon = a.no_kbon where b.status != 'Cancel' and a.nama_supp = '$nama_supp' and a.tgl_kbon between '$start_date' and '$end_date' GROUP BY a.nama_supp"); 

             $i = 0;
            $ttl_ori = 0;
            $ttl_idr = 0;
            $tot_ori = 0;
            $tot_idr = 0;
            $returnidr = 0;
            $returnusd = 0;
            $potongidr = 0;
            $potongusd = 0;
            while ($row = mysqli_fetch_assoc($sql)) {
                $i++;
                $ttl_ori += $row['oricurr'];
                $ttl_idr += $row['total_idr'];


            echo '<tr>   
            <td  value="'.$i.'">'.$i.'</td>                    
            <td  value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td  value="'.$row['tgl_bpb'].'">'.date("d M Y",strtotime($row['tgl_bpb'])).'</td>
            <td value = "'.$row['pono'].'">'.$row['pono'].'</td>
            <td  value="'.$row['tgl_po'].'">'.date("d M Y",strtotime($row['tgl_po'])).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$row['oricurr'].'">'.number_format($row['oricurr'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['total_idr'].'">'.number_format($row['total_idr'],2).'</td>     
                       </tr>
                       ';

        ?>
        <?php 

        }

        $rowaa = mysqli_fetch_assoc($sqlaa);
          $returnusd = isset($rowaa['returnusd']) ? $rowaa['returnusd'] : 0;
                $returnidr = isset($rowaa['returnidr']) ? $rowaa['returnidr'] : 0;
                $potongusd = isset($rowaa['potongusd']) ? $rowaa['potongusd'] : 0;
                $potongidr = isset($rowaa['potongidr']) ? $rowaa['potongidr'] : 0;
                $tot_ori = $ttl_ori - $returnusd + $potongusd;
                $tot_idr = $ttl_idr - $returnidr + $potongidr;

            echo '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL </td>
                       <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
                       </tr>';
            echo '<tr>
                       <td colspan = "6" style="text-align:middle;"> RETURN </td>
                       <td style="text-align:right;" value = "'.$returnusd.'">'.number_format($returnusd,2).'</td>            
                       <td style="text-align:right;" value = "'.$returnidr.'">'.number_format($returnidr,2).'</td>  
                       </tr>';
            echo '<tr>
                       <td colspan = "6" style="text-align:middle;"> OTHER ADDITION / REDUCTION </td>
                       <td style="text-align:right;" value = "'.$potongusd.'">'.number_format($potongusd,2).'</td>            
                       <td style="text-align:right;" value = "'.$potongidr.'">'.number_format($potongidr,2).'</td>  
                       </tr>';
            echo '<tr>
                       <td colspan = "6" style="text-align:middle;"> TOTAL </td>
                       <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
                       </tr>';
        ?>
    </table>

</body>
</html>




