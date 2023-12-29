<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0; 
$supp = isset($_POST['supp']) ? $_POST['supp']: null;
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

$sqll = mysqli_query($conn2,"select curr from kartu_hutang where nama_supp = '$supp'"); 
$roww = mysqli_fetch_assoc($sqll);
$curr= $roww['curr'];

if ($curr == 'IDR') {

    $sql = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek, create_date asc"); 

    echo '<a href="pcs_detailidr.php?nama_supp='.$supp.' && start_date='.$start_date.' && end_date='.$end_date.'"  target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Excel</i></button></a> ';
    echo "</br>";
    echo "</br>";

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">PO Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No KontraBon</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">KontraBon Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Faktur</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Invoice</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Invoice Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Due Date</th>
           
            </tr>                                           
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $ket = $row['ket'];
                $kbon = isset($row['tgl_kbon']) ? $row['tgl_kbon'] : null;
                $inv = isset($row['tgl_inv']) ? $row['tgl_inv'] : null;
                $tempo = isset($row['tgl_tempo']) ? $row['tgl_tempo'] : null;
                if ($ket == "Payment") {
                    $tgl0 = '-';
                }elseif ($ket == "Selisih kurs") {
                    $tgl0 = '-';
                }else{
                    $tgl0 = date("d M Y",strtotime($row['tgl_po']));
                }

                if ($ket != "Purchase") {
                    $tgl = '-';
                }else{
                    $tgl = date("d M Y",strtotime($row['tgl_bpb']));
                }

                if ($ket != "Purchase") {
                    $tgl1 = '-';
                }elseif($kbon == null){
                    $tgl1 = '-';
                }else{
                    $tgl1 = date("d M Y",strtotime($row['tgl_kbon']));
                }

                if ($ket != "Purchase") {
                    $tgl2 = '-';
                }elseif($inv == null){
                    $tgl2 = '-';
                }else{
                    $tgl2 = date("d M Y",strtotime($row['tgl_inv']));
                }

                if ($ket != "Purchase") {
                    $tgl3 = '-';
                }elseif($tempo == null){
                    $tgl3 = '-';
                }else{
                    $tgl3 = date("d M Y",strtotime($row['tgl_tempo']));
                }

            $table .= '<tr>                       
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td  value="'.$tgl.'">'.$tgl.'</td>
            <td value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
            <td  value="'.$tgl0.'">'.$tgl0.'</td>
            <td value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td  value="'.$tgl1.'">'.$tgl1.'</td>
            <td value = "'.$row['no_faktur'].'">'.$row['no_faktur'].'</td>
            <td value = "'.$row['supp_inv'].'">'.$row['supp_inv'].'</td>
            <td value = "'.$tgl2.'">'.$tgl2.'</td>
            <td  value = "'.$tgl3.'">'.$tgl3.'</td>           
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;

$sql = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek, create_date asc"); 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Payment Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Periode</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Description</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;">Equivalent IDR</th> 
            </tr>                                           
         <tr class="table-warning">
            <th style="text-align: center;vertical-align: middle;">Debit</th>                                                                       
            <th style="text-align: center;vertical-align: middle;">Credit</th>
        </tr>
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $ket = $row['ket'];

                if ($ket == "Purchase") {
                    $tgl4 = '-';
                }else{
                    $tgl4 = date("d M Y",strtotime($row['tgl_payment']));
                }

            $table .= '<tr>                       
            <td  value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td  value="'.$tgl4.'">'.$tgl4.'</td>
            <td  value="'.$row['create_date'].'">'.date("d M Y",strtotime($row['create_date'])).'</td>
            <td  value = "'.$row['ket'].'">'.$row['ket'].'</td>
            <td style="text-align:right;" value = "'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td>
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;

$sql1= "select nama_supp, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from kartu_hutang where nama_supp = '$supp' and curr = 'IDR' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
$sql11= "select a.nama_supp, a.status, SUM(a.pph_idr) as pph, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return  from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$supp' and a.curr = 'IDR' and a.confirm_date between '$start_date' and '$end_date' and  a.status = 'Approved' group by a.nama_supp";
$querys1 = mysqli_query($conn2,$sql11)or die(mysqli_error());
$datas1=mysqli_fetch_array($querys1);
$pph = isset($datas1['pph']) ? $datas1['pph'] : 0;
$jml_potong = isset($datas1['jml_potong']) ? $datas1['jml_potong'] : 0;
$jml_return = isset($datas1['jml_return']) ? $datas1['jml_return'] : 0;
$potongan = $jml_return - $jml_potong;

$sqlsww = mysqli_query($conn2,"select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$supp' and a.confirm_date < '$start_date' and  a.status = 'Approved' group by a.nama_supp");
    $rowsww = mysqli_fetch_array($sqlsww);
    $pph_idr2 = isset($rowsww['pph1']) ? $rowsww['pph1'] : 0;
    $pph_fgn2 = isset($rowsww['pph2']) ? $rowsww['pph2'] : 0;
    $jml_potong2 = isset($rowsww['jml_potong']) ? $rowsww['jml_potong'] : 0;
    $jml_return2 = isset($rowsww['jml_return']) ? $rowsww['jml_return'] : 0;
    $potongan2 = $jml_return2 - $jml_potong2;

    $sqlaas = mysqli_query($conn2,"select nama_supp, SUM(credit_usd) as cre_usd, SUM(debit_usd) as deb_usd, SUM(credit_idr) as cre_idr, SUM(debit_idr) as deb_idr from kartu_hutang where nama_supp = '$supp' and create_date < '$start_date' and cek >= 1 group by nama_supp");
    $rowaas = mysqli_fetch_array($sqlaas);
    $cre_usd = isset($rowaas['cre_usd']) ? $rowaas['cre_usd'] : 0;
    $deb_usd = isset($rowaas['deb_usd']) ? $rowaas['deb_usd'] : 0;
    $cre_idr = isset($rowaas['cre_idr']) ? $rowaas['cre_idr'] : 0;
    $deb_idr = isset($rowaas['deb_idr']) ? $rowaas['deb_idr'] : 0;

    $saldo =$cre_idr - $deb_idr - $pph_idr2 - $potongan2;
while($datas=mysqli_fetch_array($querys)){
    $t_bpb = $datas['credit_idr'];
    $t_kbon = $t_bpb;
    $t_pay = $datas['debit_idr'];
    // $balance = $t_kbon - $t_pay - $pph - $potongan;
    if ($saldo == '0') {
    $balance = $t_kbon - $t_pay - $pph - $potongan;
    }else {
    $balance = $saldo + $t_kbon - $t_pay - $pph - $potongan;
}
}



echo '<table width="100%" border="0" style="font-size:12px">

   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Begining Balance 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($saldo,2).'
        </td>       
    </tr> 
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
            Tax (PPh)
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."Rp ".number_format($pph,2).'
        </td>
</tr>  

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Potongan
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."Rp ".number_format($potongan,2).'
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
     $sql = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek, create_date asc"); 
     echo '<a href="pcs_detailfgn.php?nama_supp='.$supp.' && start_date='.$start_date.' && end_date='.$end_date.'"  target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Excel</i></button></a> ';
    echo "</br>";
    echo "</br>";


    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">BPB Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">PO Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No KontraBon</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; ">KontraBon Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Faktur</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Invoice</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Invoice Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Due Date</th>
           
            </tr>                                           
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $ket = $row['ket'];
                $kbon = isset($row['tgl_kbon']) ? $row['tgl_kbon'] : null;
                $inv = isset($row['tgl_inv']) ? $row['tgl_inv'] : null;
                $tempo = isset($row['tgl_tempo']) ? $row['tgl_tempo'] : null;
                if ($ket == "Payment") {
                    $tgl0 = '-';
                }elseif ($ket == "Selisih Kurs") {
                    $tgl0 = '-';
                }else{
                    $tgl0 = date("d M Y",strtotime($row['tgl_po']));
                }

                if ($ket != "Purchase") {
                    $tgl = '-';
                }else{
                    $tgl = date("d M Y",strtotime($row['tgl_bpb']));
                }

                if ($ket != "Purchase") {
                    $tgl1 = '-';
                }elseif($kbon == null){
                    $tgl1 = '-';
                }else{
                    $tgl1 = date("d M Y",strtotime($row['tgl_kbon']));
                }

                if ($ket != "Purchase") {
                    $tgl2 = '-';
                }elseif($inv == null){
                    $tgl2 = '-';
                }else{
                    $tgl2 = date("d M Y",strtotime($row['tgl_inv']));
                }

                if ($ket != "Purchase") {
                    $tgl3 = '-';
                }elseif($tempo == null){
                    $tgl3 = '-';
                }else{
                    $tgl3 = date("d M Y",strtotime($row['tgl_tempo']));
                }

            $table .= '<tr>                       
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td  value="'.$tgl.'">'.$tgl.'</td>
            <td value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
            <td  value="'.$tgl0.'">'.$tgl0.'</td>
            <td value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td  value="'.$tgl1.'">'.$tgl1.'</td>
            <td value = "'.$row['no_faktur'].'">'.$row['no_faktur'].'</td>
            <td value = "'.$row['supp_inv'].'">'.$row['supp_inv'].'</td>
            <td value = "'.$tgl2.'">'.$tgl2.'</td>
            <td  value = "'.$tgl3.'">'.$tgl3.'</td>           
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;

$sql = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek, create_date asc"); 

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
            <tr class="table-danger">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Payment Date </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Periode</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Description</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Rate</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;">Foreign IDR</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;">Equivalent IDR</th> 
            </tr>                                           
         <tr class="table-warning">
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>                                                                       
            <th style="text-align: center;vertical-align: middle;">Credit</th>
        </tr>
    </thead>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $ket = $row['ket'];

                if ($ket == "Purchase") {
                    $tgl4 = '-';
                }else{
                    $tgl4 = date("d M Y",strtotime($row['tgl_payment']));
                }

            $table .= '<tr>                       
            <td  value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td  value="'.$tgl4.'">'.$tgl4.'</td>
            <td  value="'.$row['create_date'].'">'.date("d M Y",strtotime($row['create_date'])).'</td>
            <td  value = "'.$row['ket'].'">'.$row['ket'].'</td>
            <td style="text-align:right;" value = "'.$row['rate'].'">'.number_format($row['rate'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>            
            <td style="text-align:right;" value = "'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td>
                       </tr>
                       ';

            $table .= '</tbody>';


        }
            $table .= '</table>';

echo $table;
$sql1= "select nama_supp, SUM(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from kartu_hutang where nama_supp = '$supp' and curr = 'USD' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());

$sql11= "select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$supp' and a.curr = 'USD' and a.confirm_date between '$start_date' and '$end_date' and  a.status = 'Approved' group by a.nama_supp";
$querys1 = mysqli_query($conn2,$sql11)or die(mysqli_error());
$datas1=mysqli_fetch_array($querys1);
$no_kbonh = isset($datas1['no_kbon']) ? $datas1['no_kbon'] : null;
$pph2 = isset($datas1['pph2']) ? $datas1['pph2'] : 0;
$pph1 = isset($datas1['pph1']) ? $datas1['pph1'] : 0;
$jml_potong = isset($datas1['jml_potong']) ? $datas1['jml_potong'] : 0;
$jml_return = isset($datas1['jml_return']) ? $datas1['jml_return'] : 0;
$sqlsss = mysqli_query($conn2,"select rate from kartu_hutang group by nama_supp");
$rowsss = mysqli_fetch_array($sqlsss);
$rate = isset($rowsss['rate']) ? $rowsss['rate'] : 1;

$sqlsww = mysqli_query($conn2,"select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$supp' and a.confirm_date < '$start_date' and  a.status = 'Approved' group by a.nama_supp");
    $rowsww = mysqli_fetch_array($sqlsww);
    $pph_idr2 = isset($rowsww['pph1']) ? $rowsww['pph1'] : 0;
    $pph_fgn2 = isset($rowsww['pph2']) ? $rowsww['pph2'] : 0;
    $jml_potong2 = isset($rowsww['jml_potong']) ? $rowsww['jml_potong'] : 0;
    $jml_return2 = isset($rowsww['jml_return']) ? $rowsww['jml_return'] : 0;
    $potongan2 = $jml_return2 - $jml_potong2;
    $potongan3 = $potongan2 * $rate;

    $sqlaas = mysqli_query($conn2,"select nama_supp, SUM(credit_usd) as cre_usd, SUM(debit_usd) as deb_usd, SUM(credit_idr) as cre_idr, SUM(debit_idr) as deb_idr from kartu_hutang where nama_supp = '$supp' and create_date < '$start_date' and cek >= 1 group by nama_supp");
    $rowaas = mysqli_fetch_array($sqlaas);
    $cre_usd = isset($rowaas['cre_usd']) ? $rowaas['cre_usd'] : 0;
    $deb_usd = isset($rowaas['deb_usd']) ? $rowaas['deb_usd'] : 0;
    $cre_idr = isset($rowaas['cre_idr']) ? $rowaas['cre_idr'] : 0;
    $deb_idr = isset($rowaas['deb_idr']) ? $rowaas['deb_idr'] : 0;

    $saldo4 = $cre_usd - $deb_usd - $pph_fgn2 - $potongan2;
    $saldo5 =$cre_idr - $deb_idr - $pph_idr2 - $potongan3;

while($datas=mysqli_fetch_array($querys)){
    $t_bpb1 = $datas['credit_usd'];
    $t_kbon1 = $t_bpb1;
    $pph_2 = $pph2;
    $potongan = $jml_return - $jml_potong;
    $t_pay1 = $datas['debit_usd'];

    $t_bpb = $datas['credit_idr'];
    $t_kbon = $t_bpb;
    $t_pay = $datas['debit_idr'];
    $pph = $pph1;
    $potongan1 = $potongan * $rate;
    if ($saldo4 == '0' || $saldo5 == '0') {
    $balance1 = $t_kbon1 - $t_pay1 - $pph_2 - $potongan;
    $balance = $t_kbon - $t_pay - $pph - $potongan1;
    }else {
    $balance1 = $saldo4 + $t_kbon1 - $t_pay1 - $pph_2 - $potongan;
    $balance = $saldo5 + $t_kbon - $t_pay - $pph - $potongan1;
}
}



echo '<table width="100%" border="0" style="font-size:12px">


<tr>
        <td width="70%">
            
        </td>
            
        
<td colspan="3" style="width:20%; font-size: 14px; text-align: center">Foreign USD</td>
              
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Begining Balance 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."$ ".number_format($saldo4,2).'
        </td>       
    </tr> 

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Credit 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."$ ".number_format($t_kbon1,2).'
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
            '."$ ".number_format($t_pay1,2).'
        </td>
</tr>  
<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Tax (PPh)
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."$ ".number_format($pph2,2).'
        </td>
</tr>

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Potongan
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."$ ".number_format($potongan,2).'
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
            '."$ ".number_format($balance1,2).'
        </td>
</tr>

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            
        </td>

</tr>
<tr>
        <td width="70%">
            
        </td>
            
<td colspan="3" style="width:20%; font-size: 14px; text-align: center">Equivalent IDR</td>
        
    </tr> 

   <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Begining Balance 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '."Rp ".number_format($saldo5,2).'
        </td>       
    </tr> 
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
            Tax (PPh)
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."Rp ".number_format($pph,2).'
        </td>
</tr> 

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Potongan
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            -'."Rp ".number_format($potongan1,2).'
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