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
            <th>Debit</th>
            <th>Credit</th>
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

    $sql1= "select nama_supp, SUM(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from kartu_hutang where nama_supp = '$nama_supp' and curr = 'IDR' and create_date between '$start_date' and '$end_date'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
$sql11= "select a.nama_supp, a.status, SUM(a.pph_idr) as pph, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return  from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$nama_supp' and a.curr = 'IDR' and a.confirm_date between '$start_date' and '$end_date' and  a.status = 'Approved' group by a.nama_supp";
$querys1 = mysqli_query($conn2,$sql11)or die(mysqli_error());
$datas1=mysqli_fetch_array($querys1);
$pph = isset($datas1['pph']) ? $datas1['pph'] : 0;
$jml_potong = isset($datas1['jml_potong']) ? $datas1['jml_potong'] : 0;
$jml_return = isset($datas1['jml_return']) ? $datas1['jml_return'] : 0;
$potongan = $jml_return - $jml_potong;

$sqlsww = mysqli_query($conn2,"select a.nama_supp, a.no_kbon, a.status, SUM(a.pph_idr) as pph1, SUM(a.pph_fgn) as pph2, SUM(b.jml_potong) as jml_potong, SUM(b.jml_return) as jml_return from kontrabon_h a left JOIN potongan b on b.no_kbon = a.no_kbon where a.nama_supp = '$nama_supp' and a.confirm_date < '$start_date' and  a.status = 'Approved' group by a.nama_supp");
    $rowsww = mysqli_fetch_array($sqlsww);
    $pph_idr2 = isset($rowsww['pph1']) ? $rowsww['pph1'] : 0;
    $pph_fgn2 = isset($rowsww['pph2']) ? $rowsww['pph2'] : 0;
    $jml_potong2 = isset($rowsww['jml_potong']) ? $rowsww['jml_potong'] : 0;
    $jml_return2 = isset($rowsww['jml_return']) ? $rowsww['jml_return'] : 0;
    $potongan2 = $jml_return2 - $jml_potong2;

    $sqlaas = mysqli_query($conn2,"select nama_supp, SUM(credit_usd) as cre_usd, SUM(debit_usd) as deb_usd, SUM(credit_idr) as cre_idr, SUM(debit_idr) as deb_idr from kartu_hutang where nama_supp = '$nama_supp' and create_date < '$start_date' and cek >= 1 group by nama_supp");
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
 ?>
    </table>

</body>
</html>




