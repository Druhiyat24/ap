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
    header("Content-Disposition: attachment; filename=Data_Addition_Global.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table border="1" style="width: 100%" >
       <tr>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 5%">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 16%">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 12% ">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 16%">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 12% ">PO Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: center; width: 8%">Currency</th>
            <th colspan="2" style="text-align: center;vertical-align: center; width: 31%">Amount </th>
           
            </tr>   
             <tr>
            <th style="text-align: center;vertical-align: center;">Ori Curr</th>
            <th style="text-align: center;vertical-align: center;">IDR</th>                                                                            
        </tr>     
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
         $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int, a.bpbdate, a.pono, c.podate,a.curr, round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as bpbori, if(curr = 'IDR', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) * (c.tax /100)))),2)) as bpbidr from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and status_retur = 'N' and b.Supplier = '$nama_supp' and a.bpbdate between '$start_date' and '$end_date' group by a.bpbno_int"); 

             $i = 0;
            $ttl_ori = 0;
            $ttl_idr = 0;
            $tot_ori = 0;
            $tot_idr = 0;
            $total_ori = 0;
            $total_idr = 0;
            $returnidr = 0;
            $returnusd = 0;
            $potongidr = 0;
            $potongusd = 0;
        while ($row = mysqli_fetch_assoc($sql)) {
                $i++;
                $ttl_ori += $row['bpbori'];
                $ttl_idr += $row['bpbidr'];
 

        echo '<tr>   
            <td style="text-align:center;"  value="'.$i.'">'.$i.'</td>                    
            <td style="text-align:center;" value = "'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
            <td style="text-align:center;"  value="'.$row['bpbdate'].'">'.date("d M Y",strtotime($row['bpbdate'])).'</td>
            <td style="text-align:center;" value = "'.$row['pono'].'">'.$row['pono'].'</td>
            <td style="text-align:center;"  value="'.$row['podate'].'">'.date("d M Y",strtotime($row['podate'])).'</td>
            <td style="text-align:center;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$row['bpbori'].'">'.number_format($row['bpbori'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['bpbidr'].'">'.number_format($row['bpbidr'],2).'</td>          
                       </tr>
             ';
        ?>
        <?php 

        }
        echo '<tr>
                       <td colspan = "6" style="text-align:center;"> SUBTOTAL BPB</td>
                       <td style="text-align:right;" value = "'.$ttl_ori.'">'.number_format($ttl_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>  
                       </tr>';

        $sqldd = mysqli_query($conn1,"select a.no_kbon,a.tgl_kbon,GROUP_CONCAT(a.no_bpb) as bpb,a.curr, b.jml_potong as potongori,IF(a.curr = 'IDR',b.jml_potong,(b.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon )))) as potongidr from kontrabon a inner join potongan b on b.no_kbon = a.no_kbon where b.status != 'Cancel' and b.jml_potong > '0' and b.nama_supp = '$nama_supp' and b.tgl_kbon between '$start_date' and '$end_date' GROUP BY b.no_kbon"); 


            echo '<tr>
                        <th style="text-align: center;vertical-align: center;">No</th>
                        <th colspan="2" style="text-align: center;vertical-align: center; ">No Kontrabon </th>
                        <th colspan="2" style="text-align: center;vertical-align: center; ">Kontrabon Date</th>
                        <th style="text-align: center;vertical-align: center;">Curr</th>
                        <th colspan="2" style="text-align: center;vertical-align: center; ">Addition/Reduction</th>  
                       </tr>';
                       $z = 0;
        while ($rowdd = mysqli_fetch_assoc($sqldd)) {
                $z++;
                $total_ori += $rowdd['potongori'];
                $total_idr += $rowdd['potongidr'];


            echo '<tr>   
            <td style="text-align:center"  value="'.$z.'">'.$z.'</td>                    
            <td style="text-align:center" colspan="2" value = "'.$rowdd['no_kbon'].'">'.$rowdd['no_kbon'].'</td>
            <td style="text-align:center"  colspan="2" value="'.$rowdd['tgl_kbon'].'">'.date("d M Y",strtotime($rowdd['tgl_kbon'])).'</td> 
            <td style="text-align:center" value = "'.$rowdd['curr'].'">'.$rowdd['curr'].'</td>
            <td style="text-align:right;" value = "'.$rowdd['potongori'].'">'.number_format($rowdd['potongori'],2).'</td>            
            <td style="text-align:right;" value = "'.$rowdd['potongidr'].'">'.number_format($rowdd['potongidr'],2).'</td>         
                       </tr>
                       ';

        }
                $tot_ori = $ttl_ori + $total_ori;
                $tot_idr = $ttl_idr + $total_idr;

                echo'<tr>
                       <td colspan = "6" style="text-align:center;"> SUBTOTAL ADDITION KONTRABON </td>
                       <td style="text-align:right;" value = "'.$total_ori.'">'.number_format($total_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$total_idr.'">'.number_format($total_idr,2).'</td>  
                       </tr>';
            echo '<tr>
                       <td colspan = "6" style="text-align:center;"> TOTAL </td>
                       <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
                       </tr>';
        ?>
    </table>

</body>
</html>




