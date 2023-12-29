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
    header("Content-Disposition: attachment; filename=Data_PCS_detail.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table border="1" >
        <tr>
            <th>No</th>
            <th>No BPB</th>
            <th>BPB Date</th>
            <th>No PO</th>
            <th>PO Date</th>
            <th>No Kontrabon</th>
            <th>KontraBon Date</th>
            <th>No Faktur</th>
            <th>No Invoice</th>
            <th>Invoice Date</th>
            <th>Due Date</th>
            <th>No Payment</th>
            <th>Payment Date</th>
            <th>Periode</th>
            <th>Description</th>
            <th>Rate</th>
            <th>Debit Foreign</th>
            <th>Credit Foreign</th>
            <th>Debit IDR</th>
            <th>Credit IDR</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
        $data = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$nama_supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek, create_date asc");

        $no = 1;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['nama_supp'];
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
                 if ($ket == "Purchase") {
                    $tgl4 = '-';
                }else{
                    $tgl4 = date("d M Y",strtotime($row['tgl_payment']));
                }

 $sql1= "select nama_supp, SUM(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from kartu_hutang where nama_supp = '$nama_supp' and curr = 'USD' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());

$sql11= "select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$nama_supp' and a.curr = 'USD' and a.confirm_date between '$start_date' and '$end_date' and  a.status = 'Approved' group by a.nama_supp";
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

$sqlsww = mysqli_query($conn2,"select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$nama_supp' and a.confirm_date < '$start_date' and  a.status = 'Approved' group by a.nama_supp");
    $rowsww = mysqli_fetch_array($sqlsww);
    $pph_idr2 = isset($rowsww['pph1']) ? $rowsww['pph1'] : 0;
    $pph_fgn2 = isset($rowsww['pph2']) ? $rowsww['pph2'] : 0;
    $jml_potong2 = isset($rowsww['jml_potong']) ? $rowsww['jml_potong'] : 0;
    $jml_return2 = isset($rowsww['jml_return']) ? $rowsww['jml_return'] : 0;
    $potongan2 = $jml_return2 - $jml_potong2;
    $potongan3 = $potongan2 * $rate;

    $sqlaas = mysqli_query($conn2,"select nama_supp, SUM(credit_usd) as cre_usd, SUM(debit_usd) as deb_usd, SUM(credit_idr) as cre_idr, SUM(debit_idr) as deb_idr from kartu_hutang where nama_supp = '$nama_supp' and create_date < '$start_date' and cek >= 1 group by nama_supp");
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


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
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
        ?>
        <?php 

        }
        ?>
        <?php
        echo '<table width="100%" border="0" style="font-size:12px">

   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
             
        </td>
<td style="width:1%"></td>
        <td style="text-align:right">
            
        </td>       
    </tr>
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
 ?>
    </table>

</body>
</html>




