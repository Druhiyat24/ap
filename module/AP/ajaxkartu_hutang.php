<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0; 
$supp = isset($_POST['supp']) ? $_POST['supp']: null;
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

$sqll = mysqli_query($conn2,"select curr from pcs where nama_supp = '$supp'"); 
$roww = mysqli_fetch_assoc($sqll);
$curr= $roww['curr'];

if ($curr == 'IDR') {
    $sql = mysqli_query($conn2,"select * from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek asc"); 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">Tax (Pph)</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Payment Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Approved Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Description</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 80px; display: none;">Foreign Currency</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;width: 80px">Equivalent IDR</th> 
            </tr>                                           
         <tr class="table-danger">
            <th style="text-align: center;vertical-align: middle;display: none;">No</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 80px; display: none;">Debit</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Balance</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>                               
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            if (!empty($row['tgl_payment'])) {
                $aa = date("d-M-Y",strtotime($row['tgl_payment']));
            }
            else{
                $aa = '-';
            }

            if (!empty($row['tgl_bpb'])) {
                $bb = date("d-M-Y",strtotime($row['tgl_bpb']));
            }
            else{
                $bb = '-';
            }
            $table .= '<tr>                       
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="width:100px;" value="'.$bb.'">'.$bb.'</td>
            <td style="display: none;" value = "'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
            <td  value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="width:100px;" value="'.$aa.'">'.$aa.'</td>
            <td style="width:100px;" value="'.$row['create_date'].'">'.date("d-M-Y",strtotime($row['create_date'])).'</td>
            <td  value = "'.$row['ket'].'">'.$row['ket'].'</td>
            <td style="display: none;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="display: none;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>         
            <td style="display: none;" value = "'.$row['balance_usd'].'">'.number_format($row['balance_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>            
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;

$sql1= "select nama_supp, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
while($datas=mysqli_fetch_array($querys)){
    $t_bpb = $datas['credit_idr'];
    // $tax= $datas['pph'];
    $t_kbon = $t_bpb;
    $t_pay = $datas['debit_idr'];
    $balance = $t_kbon - $t_pay;
}

$sql3= "select nama_supp, status, SUM(pph) as pph from kontrabon_h where nama_supp = '$supp' and status = 'Approved'";
$querys3 = mysqli_query($conn2,$sql3)or die(mysqli_error());
while($datas3=mysqli_fetch_array($querys3)){
    $pph = $datas3['pph'];
}

echo '<table width="100%" border="0" style="font-size:12px">
   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Credit 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_kbon,2).'
        </td>       
    </tr>   

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Debit
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_pay,2).'
        </td>
</tr>  

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Balance
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right; font-weight: bold">
            '."Rp ".number_format($balance,2).'
        </td>
</tr>


    
</table>';
echo "</br>";
echo "</br>";
}else{

 $sql = mysqli_query($conn2,"select * from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek asc"); 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-warning">
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">Tax (Pph)</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Payment Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Approved Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Description</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 80px; display: none;">Foreign Currency</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;width: 80px">Equivalent IDR</th> 
            </tr>                                           
         <tr class="table-warning">
            <th style="text-align: center;vertical-align: middle;display: none;">No</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 80px; display: none;">Debit</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Balance</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>                               
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            if (!empty($row['tgl_payment'])) {
                $aa = date("d-M-Y",strtotime($row['tgl_payment']));
            }
            else{
                $aa = '-';
            }

            if (!empty($row['tgl_bpb'])) {
                $bb = date("d-M-Y",strtotime($row['tgl_bpb']));
            }
            else{
                $bb = '-';
            }
            $table .= '<tr>                       
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="width:100px;" value="'.$bb.'">'.$bb.'</td>
            <td style="display: none;" value = "'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
            <td  value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="width:100px;" value="'.$aa.'">'.$aa.'</td>
            <td style="width:100px;" value="'.$row['create_date'].'">'.date("d-M-Y",strtotime($row['create_date'])).'</td>
            <td  value = "'.$row['ket'].'">'.$row['ket'].'</td>
            <td style="display: none;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="display: none;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>         
            <td style="display: none;" value = "'.$row['balance_usd'].'">'.number_format($row['balance_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>            
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;


$sql1= "select nama_supp, SUM(credit_usd) as credit_idr, SUM(debit_usd) as debit_idr from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
while($datas=mysqli_fetch_array($querys)){
    $t_bpb = $datas['credit_idr'];
    // $tax= $datas['pph'];
    $t_kbon = $t_bpb;
    $t_pay = $datas['debit_idr'];
    $balance = $t_kbon - $t_pay;
}

$sql3= "select nama_supp, status, SUM(pph) as pph from kontrabon_h where nama_supp = '$supp' and status = 'Approved'";
$querys3 = mysqli_query($conn2,$sql3)or die(mysqli_error());
while($datas3=mysqli_fetch_array($querys3)){
    $pph = $datas3['pph'];
}

echo '<table width="100%" border="0" style="font-size:12px">
   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Credit 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_kbon,2).'
        </td>       
    </tr>   

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Debit
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_pay,2).'
        </td>
</tr>  

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Balance
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right; font-weight: bold">
            '."Rp ".number_format($balance,2).'
        </td>
</tr>


    
</table>';

echo "</br>";
echo "</br>";

$sql = mysqli_query($conn2,"select * from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek asc"); 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">Tax (Pph)</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 80px">No Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Payment Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Approved Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 90px">Description</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 80px; display: none;">Foreign Currency</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;width: 80px">Equivalent IDR</th> 
            </tr>                                           
         <tr class="table-danger">
            <th style="text-align: center;vertical-align: middle;display: none;">No</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 80px; display: none;">Debit</th>
            <th style="text-align: center;vertical-align: middle; display: none;">Balance</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>                               
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            if (!empty($row['tgl_payment'])) {
                $aa = date("d-M-Y",strtotime($row['tgl_payment']));
            }
            else{
                $aa = '-';
            }

            if (!empty($row['tgl_bpb'])) {
                $bb = date("d-M-Y",strtotime($row['tgl_bpb']));
            }
            else{
                $bb = '-';
            }
            $table .= '<tr>                       
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="width:100px;" value="'.$bb.'">'.$bb.'</td>
            <td style="display: none;" value = "'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
            <td  value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="width:100px;" value="'.$aa.'">'.$aa.'</td>
            <td style="width:100px;" value="'.$row['create_date'].'">'.date("d-M-Y",strtotime($row['create_date'])).'</td>
            <td  value = "'.$row['ket'].'">'.$row['ket'].'</td>
            <td style="display: none;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="display: none;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>         
            <td style="display: none;" value = "'.$row['balance_usd'].'">'.number_format($row['balance_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>            
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;

$sql1= "select nama_supp, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from pcs where nama_supp = '$supp' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
while($datas=mysqli_fetch_array($querys)){
    $t_bpb = $datas['credit_idr'];
    // $tax= $datas['pph'];
    $t_kbon = $t_bpb;
    $t_pay = $datas['debit_idr'];
    $balance = $t_kbon - $t_pay;
}

$sql3= "select nama_supp, status, SUM(pph) as pph from kontrabon_h where nama_supp = '$supp' and status = 'Approved'";
$querys3 = mysqli_query($conn2,$sql3)or die(mysqli_error());
while($datas3=mysqli_fetch_array($querys3)){
    $pph = $datas3['pph'];
}

echo '<table width="100%" border="0" style="font-size:12px">
   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Credit 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_kbon,2).'
        </td>       
    </tr>   

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Debit
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($t_pay,2).'
        </td>
</tr>  

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Balance
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right; font-weight: bold">
            '."Rp ".number_format($balance,2).'
        </td>
</tr>


    
</table>';

echo "</br>";
echo "</br>";
}


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>