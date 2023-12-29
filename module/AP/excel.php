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
	header("Content-Disposition: attachment; filename=Data.xls");
	$nama_supp=$_GET['nama_supp'];
	$start_date = date("d F Y",strtotime($_GET['start_date']));
	$end_date = date("d F Y",strtotime($_GET['end_date']));	?>

	<center>
		<h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
	</center>
 
	<table border="1">
		<tr>
			<th>No</th>
			<th>no_bpb</th>
			<th>tgl_bpb</th>
			<th>create_date</th>
			<th>no_payment</th>
			<th>tgl_payment</th>
			<th>curr</th>
			<th>ket</th>
			<th>rate</th>
			<th>credit_usd</th>
			<th>debit_usd</th>
			<th>credit_idr</th>
			<th>debit_idr</th>
		</tr>
		<?php 
		// koneksi database
		include '../../conn/conn.php';
 		$nama_supp=$_GET['nama_supp'];
 		$start_date = date("Y-m-d",strtotime($_GET['start_date']));
		$end_date = date("Y-m-d",strtotime($_GET['end_date']));
		// menampilkan data pegawai
		$data = mysqli_query($conn2,"select * from kartu_hutang where nama_supp = '$nama_supp'");
		$no = 1;

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
		while($d = mysqli_fetch_array($data)){
			$data1 = $d['debit_idr'];
		$curr = $d['curr'];
		?>
		<tr>
			<td><?php echo $no++; ?></td>
			<td><?php echo $d['no_bpb']; ?></td>
			<td><?php echo $d['tgl_bpb']; ?></td>
			<td><?php echo $d['create_date']; ?></td>
			<td><?php echo $d['no_payment']; ?></td>
			<td><?php echo $d['tgl_payment']; ?></td>
			<td><?php echo $d['curr']; ?></td>
			<td><?php echo $d['ket']; ?></td>
			<td><?php echo $d['rate']; ?></td>
			<td><?php echo $d['credit_usd']; ?></td>
			<td><?php echo $d['debit_usd']; ?></td>
			<td><?php echo $d['credit_idr']; ?></td>
			<td><?php echo $d['debit_idr']; ?></td>
		</tr>
		<?php 
		}
		?>
	</table>
<?php
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
 ?>
</body>
</html>




