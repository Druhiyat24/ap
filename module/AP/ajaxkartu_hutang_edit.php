<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0;
$total_ori = 0;
$total_idr = 0; 
$total_ori2 = 0;
$total_idr2 = 0; 
$supp = isset($_POST['supp']) ? $_POST['supp']: null;
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

    $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int, a.bpbdate, a.pono, c.podate,a.curr, round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as bpbori, if(curr = 'IDR', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) * (c.tax /100)))),2)) as bpbidr from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and status_retur = 'N' and b.Supplier = '$supp' and a.bpbdate between '$start_date' and '$end_date' group by a.bpbno_int");


    echo '<a href="pcs_detailidr.php?nama_supp='.$supp.' && start_date='.$start_date.' && end_date='.$end_date.'"  target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Excel</i></button></a> ';
    echo "</br>";
    echo "</br>";
    echo "<h6>ADDITION</h6>";

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">PO Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
            <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount </th>
           
            </tr>   
             <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
            <th style="text-align: center;vertical-align: middle;width: 80px"> Eqv IDR</th>                                                                            
        </tr>                                        
    </thead>
                    </thead>';

            $table .= '<tbody>';
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
                $ttl_ori += $row['bpbori'];
                $ttl_idr += $row['bpbidr'];


            $table .= '<tr>   
            <td  value="'.$i.'">'.$i.'</td>                    
            <td value = "'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
            <td  value="'.$row['bpbdate'].'">'.date("d M Y",strtotime($row['bpbdate'])).'</td>
            <td value = "'.$row['pono'].'">'.$row['pono'].'</td>
            <td  value="'.$row['podate'].'">'.date("d M Y",strtotime($row['podate'])).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$row['bpbori'].'">'.number_format($row['bpbori'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['bpbidr'].'">'.number_format($row['bpbidr'],2).'</td>     
                       </tr>
                       ';

            $table .= '</tbody>';


        }

        $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL BPB </td>
                       <td style="text-align:right;" value = "'.$ttl_ori.'">'.number_format($ttl_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>  
                       </tr>';

        $sqldd = mysqli_query($conn1,"select a.no_kbon,a.tgl_kbon,GROUP_CONCAT(a.no_bpb) as bpb,a.curr, b.jml_potong as potongori,IF(a.curr = 'IDR',b.jml_potong,(b.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon )))) as potongidr from kontrabon a inner join potongan b on b.no_kbon = a.no_kbon where b.status != 'Cancel' and b.jml_potong > '0' and b.nama_supp = '$supp' and b.tgl_kbon between '$start_date' and '$end_date' GROUP BY b.no_kbon"); 


            $table .= '<tr rowspan="2" class="table-danger">
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
                        <th rowspan="2" colspan="2" style="text-align: center;vertical-align: middle;">No Kontrabon</th>
                        <th rowspan="2" colspan="2" style="text-align: center;vertical-align: middle;">Kontrabon Date</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
                        <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount</th>  
                       </tr>

                       <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
            <th style="text-align: center;vertical-align: middle;width: 80px"> Eqv IDR</th>                                                                            
        </tr> ';
                       $z = 0;
        while ($rowdd = mysqli_fetch_assoc($sqldd)) {
                $z++;

                $total_ori += $rowdd['potongori'];
                $total_idr += $rowdd['potongidr'];

            $table .= '<tr>   
            <td  value="'.$z.'">'.$z.'</td>                    
            <td colspan="2" value = "'.$rowdd['no_kbon'].'">'.$rowdd['no_kbon'].'</td>
            <td  colspan="2" value="'.$rowdd['tgl_kbon'].'">'.date("d M Y",strtotime($rowdd['tgl_kbon'])).'</td> 
            <td value = "'.$rowdd['curr'].'">'.$rowdd['curr'].'</td>
            <td style="text-align:right;" value = "'.$rowdd['potongori'].'">'.number_format($rowdd['potongori'],2).'</td>            
            <td style="text-align:right;" value = "'.$rowdd['potongidr'].'">'.number_format($rowdd['potongidr'],2).'</td>     
                       </tr>
                       ';


        }
                $tot_ori = $ttl_ori + $total_ori;
                $tot_idr = $ttl_idr + $total_idr;

         
            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL ADDITION KONTRABON </td>
                       <td style="text-align:right;" value = "'.$total_ori.'">'.number_format($total_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$total_idr.'">'.number_format($total_idr,2).'</td>  
                       </tr>';
            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> TOTAL </td>
                       <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
                       </tr>';
            $table .= '</table>';

echo $table;

// $sql = mysqli_query($conn2,"select a.supplier,a.no_bpb, a.tgl_bpb, a.pono, a.tgl_po, a.curr,sum(round((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100)),4)) as oricurr, if(a.curr = 'IDR',sum(round((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100)),4)),if(a.is_invoiced != 'Invoiced',sum(round((a.qty * (a.price * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_bpb))) + ((a.qty * (a.price * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_bpb))) * (a.tax / 100)),4)), ((b.total + b.pph_value) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ))))) as total_idr from bpb_new a left join kontrabon b on b.no_bpb = a.no_bpb where is_invoiced != 'Cancel' and a.supplier = '$supp' and a.tgl_bpb between '$start_date' and '$end_date' || is_invoiced != 'Cancel' and a.supplier = '$supp' and b.tgl_kbon between '$start_date' and '$end_date' group by a.no_bpb");

//      $sqlaa = mysqli_query($conn2,"select a.nama_supp, if(b.curr = 'IDR', sum(jml_return),sum(jml_return * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon )))) as returnidr, sum(jml_return) as returnusd, if(b.curr = 'IDR', sum(jml_potong),sum(jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon )))) as potongidr, sum(jml_potong) as potongusd from potongan a inner join  kontrabon_h b on b.no_kbon = a.no_kbon where b.status != 'Cancel' and a.nama_supp = '$supp' and a.tgl_kbon between '$start_date' and '$end_date' GROUP BY a.nama_supp");  

//     echo '<a href="pcs_detailidr2.php?nama_supp='.$supp.' && start_date='.$start_date.' && end_date='.$end_date.'"  target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Excel</i></button></a> ';
//     echo "</br>";
//     echo "</br>";

//     $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
//                     <thead>
//             <tr class="table-danger">
//             <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
//             <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
//             <th rowspan="2" style="text-align: center;vertical-align: middle; ">BPB Date </th>
//             <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
//             <th rowspan="2" style="text-align: center;vertical-align: middle; ">PO Date </th>
//             <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
//             <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount </th>
           
//             </tr>   
//              <tr class="thead-dark">
//             <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
//             <th style="text-align: center;vertical-align: middle;width: 80px">IDR</th>                                                                            
//         </tr>                                        
//     </thead>
//                     </thead>';

//             $table .= '<tbody>';
//             $i = 0;
//             $ttl_ori = 0;
//             $ttl_idr = 0;
//             $tot_ori = 0;
//             $tot_idr = 0;
//             $returnidr = 0;
//             $returnusd = 0;
//             $potongidr = 0;
//             $potongusd = 0;
//             while ($row = mysqli_fetch_assoc($sql)) {
//                 $i++;
//                 $ttl_ori += $row['oricurr'];
//                 $ttl_idr += $row['total_idr'];


//             $table .= '<tr>   
//             <td  value="'.$i.'">'.$i.'</td>                    
//             <td  value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
//             <td  value="'.$row['tgl_bpb'].'">'.date("d M Y",strtotime($row['tgl_bpb'])).'</td>
//             <td value = "'.$row['pono'].'">'.$row['pono'].'</td>
//             <td  value="'.$row['tgl_po'].'">'.date("d M Y",strtotime($row['tgl_po'])).'</td>
//             <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
//             <td style="text-align:right;" value = "'.$row['oricurr'].'">'.number_format($row['oricurr'],2).'</td>            
//             <td style="text-align:right;" value = "'.$row['total_idr'].'">'.number_format($row['total_idr'],2).'</td>     
//                        </tr>
//                        ';

//             $table .= '</tbody>';


//         }
//         $rowaa = mysqli_fetch_assoc($sqlaa);
//           $returnusd = isset($rowaa['returnusd']) ? $rowaa['returnusd'] : 0;
//                 $returnidr = isset($rowaa['returnidr']) ? $rowaa['returnidr'] : 0;
//                 $potongusd = isset($rowaa['potongusd']) ? $rowaa['potongusd'] : 0;
//                 $potongidr = isset($rowaa['potongidr']) ? $rowaa['potongidr'] : 0;
//                 $tot_ori = $ttl_ori - $returnusd + $potongusd;
//                 $tot_idr = $ttl_idr - $returnidr + $potongidr;

//             $table .= '<tr>
//                        <td colspan = "6" style="text-align:middle;"> SUBTOTAL </td>
//                        <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
//                        <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
//                        </tr>';
//             $table .= '<tr>
//                        <td colspan = "6" style="text-align:middle;"> RETURN </td>
//                        <td style="text-align:right;" value = "'.$returnusd.'">'.number_format($returnusd,2).'</td>            
//                        <td style="text-align:right;" value = "'.$returnidr.'">'.number_format($returnidr,2).'</td>  
//                        </tr>';
//             $table .= '<tr>
//                        <td colspan = "6" style="text-align:middle;"> OTHER ADDITION / REDUCTION </td>
//                        <td style="text-align:right;" value = "'.$potongusd.'">'.number_format($potongusd,2).'</td>            
//                        <td style="text-align:right;" value = "'.$potongidr.'">'.number_format($potongidr,2).'</td>  
//                        </tr>';
//             $table .= '<tr>
//                        <td colspan = "6" style="text-align:middle;"> TOTAL </td>
//                        <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
//                        <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
//                        </tr>';
//             $table .= '</table>';

// echo $table;

$sql = mysqli_query($conn2,"select payment_ftr_id,tgl_pelunasan,nama_supp, list_payment_id, tgl_list_payment,valuta_bayar,IF(valuta_bayar = 'IDR',nominal,nominal_fgn) as oricurr, IF(valuta_bayar = 'IDR',nominal,(nominal_fgn * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan )))) as total_idr from payment_ftr where nama_supp = '$supp' and tgl_pelunasan between '$start_date' and '$end_date' group by list_payment_id"); 

    echo '<a href="pcs_detailidr3.php?nama_supp='.$supp.' && start_date='.$start_date.' && end_date='.$end_date.'"  target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Excel</i></button></a> ';
    echo "</br>";
    echo "</br>";
    echo "<h6>DEDUCTION</h6>"; 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">Payment Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No List Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">List Payment Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
            <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount </th>
           
            </tr>   
             <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">Eqv IDR</th>                                                                            
        </tr>                                        
    </thead>
                    </thead>';

            $table .= '<tbody>';
            $i = 0;
            $ttl_ori = 0;
            $ttl_idr = 0;
            while ($row = mysqli_fetch_assoc($sql)) {
                $i++;
                $ttl_ori += $row['oricurr'];
                $ttl_idr += $row['total_idr'];

            $table .= '<tr>   
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

            $table .= '</tbody>';


        }
            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL PAYMENT </td>
                       <td style="text-align:right;" value = "'.$ttl_ori.'">'.number_format($ttl_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>  
                       </tr>';
        $sqldd = mysqli_query($conn1,"select  mastersupplier.Supplier as Supplier,bppb.bppbno_int, bppb.bppbdate, bppb.curr as curr, round(sum(bppb.qty * bppb.price),2) as rtnori, round(if(bppb.curr = 'IDR',sum(bppb.qty * bppb.price),sum(bppb.qty * (bppb.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ))))),2) as rtnidr from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where cancel != 'Y' and bppb.bpbno_ro != '' and mastersupplier.Supplier = '$supp' and bppb.bppbdate between '$start_date' and '$end_date' group by bppb.bppbno_int"); 


            $table .= '<tr rowspan="2" class="table-danger">
                       <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No Bpb Return</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle; ">Bpb Return Date</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle; ">PO Date</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
                        <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount </th>
           
                        </tr>   
                        <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
                        <th style="text-align: center;vertical-align: middle;width: 80px">Eqv IDR</th>                                                                             
            </tr> ';
                       $z = 0;
        while ($rowdd = mysqli_fetch_assoc($sqldd)) {
                $z++;

                $bppb = isset($rowdd['bppbno_int']) ? $rowdd['bppbno_int'] : null;
                $total_ori += $rowdd['rtnori'];
                $total_idr += $rowdd['rtnidr'];

                $sqlpono = mysqli_query($conn1,"select DISTINCT a.pono, c.podate from bpb a inner join bppb b on b.bpbno_ro = a.bpbno left JOIN po_header c on c.pono = a.pono where b.bppbno_int = '$bppb'"); 
                $rowpono = mysqli_fetch_assoc($sqlpono);

            $table .= '<tr>   
            <td  value="'.$z.'">'.$z.'</td>                    
            <td value = "'.$rowdd['bppbno_int'].'">'.$rowdd['bppbno_int'].'</td>
            <td  value="'.$rowdd['bppbdate'].'">'.date("d M Y",strtotime($rowdd['bppbdate'])).'</td>
            <td value = "'.$rowpono['pono'].'">'.$rowpono['pono'].'</td>
            <td  value="'.$rowpono['podate'].'">'.date("d M Y",strtotime($rowpono['podate'])).'</td>
            <td value = "'.$rowdd['curr'].'">'.$rowdd['curr'].'</td>
            <td style="text-align:right;" value = "'.$rowdd['rtnori'].'">'.number_format($rowdd['rtnori'],2).'</td>            
            <td style="text-align:right;" value = "'.$rowdd['rtnidr'].'">'.number_format($rowdd['rtnidr'],2).'</td>
                       ';


        }

         
            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL RETURN BPB </td>
                       <td style="text-align:right;" value = "'.$total_ori.'">'.number_format($total_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$total_idr.'">'.number_format($total_idr,2).'</td>  
                       </tr>';

        $sqlff = mysqli_query($conn1,"select a.no_kbon,a.tgl_kbon,GROUP_CONCAT(a.no_bpb) as bpb,a.curr, b.jml_potong as potongori,IF(a.curr = 'IDR',b.jml_potong,(b.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = b.tgl_kbon )))) as potongidr from kontrabon a inner join potongan b on b.no_kbon = a.no_kbon where b.status != 'Cancel' and b.jml_potong < '0' and b.nama_supp = '$supp' and b.tgl_kbon between '$start_date' and '$end_date' GROUP BY b.no_kbon"); 


            $table .= '<tr rowspan="2" class="table-danger">
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
                        <th rowspan="2" colspan="2" style="text-align: center;vertical-align: middle;">No Kontrabon</th>
                        <th rowspan="2" colspan="2" style="text-align: center;vertical-align: middle;">Kontrabon Date</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Currency</th>
                        <th colspan="2" style="text-align: center;vertical-align: middle; ">Amount</th>  
                       </tr>

                       <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Ori Curr</th>
            <th style="text-align: center;vertical-align: middle;width: 80px"> Eqv IDR</th>                                                                            
        </tr> ';
                       $z = 0;
        while ($rowff = mysqli_fetch_assoc($sqlff)) {
                $z++;

                $total_ori2 += abs($rowff['potongori']);
                $total_idr2 += abs($rowff['potongidr']);

            $table .= '<tr>   
            <td  value="'.$z.'">'.$z.'</td>                    
            <td colspan="2" value = "'.$rowff['no_kbon'].'">'.$rowff['no_kbon'].'</td>
            <td  colspan="2" value="'.$rowff['tgl_kbon'].'">'.date("d M Y",strtotime($rowff['tgl_kbon'])).'</td> 
            <td value = "'.$rowff['curr'].'">'.$rowff['curr'].'</td>
            <td style="text-align:right;" value = "'.$rowff['potongori'].'">'.number_format($rowff['potongori'],2).'</td>            
            <td style="text-align:right;" value = "'.$rowff['potongidr'].'">'.number_format($rowff['potongidr'],2).'</td>     
                       </tr>
                       ';


        }
                $tot_ori = $ttl_ori + $total_ori + $total_ori2;
                $tot_idr = $ttl_idr + $total_idr + $total_idr2;

         
            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> SUBTOTAL DEDUCTION KONTRABON </td>
                       <td style="text-align:right;" value = "'.$total_ori2.'">'.number_format($total_ori2,2).'</td>            
                       <td style="text-align:right;" value = "'.$total_idr2.'">'.number_format($total_idr2,2).'</td>  
                       </tr>';

            $table .= '<tr>
                       <td colspan = "6" style="text-align:middle;"> TOTAL </td>
                       <td style="text-align:right;" value = "'.$tot_ori.'">'.number_format($tot_ori,2).'</td>            
                       <td style="text-align:right;" value = "'.$tot_idr.'">'.number_format($tot_idr,2).'</td>  
                       </tr>';
            $table .= '</table>';

echo $table;







// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>