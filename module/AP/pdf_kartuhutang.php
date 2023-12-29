<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$nama_supp=$_GET['nama_supp'];
$start_date = date("Y-m-d",strtotime($_GET['start_date']));
$end_date = date("Y-m-d",strtotime($_GET['end_date']));
?>

<?php
$sql= "select * from kartu_hutang where nama_supp = '$nama_supp' and create_date between '$start_date' and '$end_date' and cek >= 1 order by cek asc";
$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));

ob_start();
?>
start();
?>




<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

<style>



@page *{

    margin-top: 1.54cm;

    margin-bottom: 1.54cm;

    margin-left: 3.175cm;

    margin-right: 3.175cm;

}



 	table{margin: auto;}

 	td,th{padding: 1px;text-align: left}

 	h1{text-align: center}

 	th{text-align:center; padding: 10px;}

	

.footer{

	width:100%;

	height:30px;

	margin-top:50px;

	text-align:right;

	

}

/*

CSS HEADER

*/



.header{

	width:100%;

	height:20px;

	padding-top:0;

	margin-bottom:10px;

}

.title{

	font-size:30px;

	font-weight:bold;

	text-align:center;

	margin-top:-90px;

}



.horizontal{

	height:0;

	width:100%;

	border:1px solid #000000;

}

.position_top {

	vertical-align: top;

	

}



table {

  border-collapse: collapse;

  width: 100%;

}

.td1{
    border:1px solid black;
    border-top: none;
    border-bottom: none;
}

.header_title{

	width:100%;

	height:auto;

	text-align:center;



	font-size:12px;

	

}



</style>

	
  <title>PCS</title>
</head>
<body style=" padding-left:5%; padding-right:5%;">
 
  <div class="header">
		<table width="100%">
			<tr>
				<td>
					<img src="../../images/img-01.png" style="heigh:70px; width:80px;">
				</td>
				<td class="title">
					PT.NIRWANA ALABARE GARMENT
					<div style="font-size:12px;line-height:9">
						Jl. Raya Rancaekek – Majalaya No. 289 Desa Solokan Jeruk Kecamatan Solokan Jeruk, <br />Kabupaten Bandung 40382 <br />Telp. 022-85962081
					</div>
				</td>
			</tr>
		</table>
		&nbsp;
		<div class="horizontal">

		</div>
	</div>

  <hr />
<br>
<table width="100%">
<tr>
	<td ><h4><?php echo $nama_supp ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      PAYABLE CARD STATEMENT
	</h4>
	</td>
</tr>
</table>
<hr />
<table style="font-size:12px;">
	<thead>
    <tr style="margin-bottom: 50px">
      <th style="text-align:center;width: 100%;padding-top: -5px;">Periode  : <?php echo date("d F Y",strtotime($start_date)) ?> - <?php echo date("d F Y",strtotime($end_date)) ?></th> 
      <td style="text-align:left;padding-left: 25px;padding-top: -20px;padding-bottom: -10px;">
      	
	</td>                              
    </tr>
</tbody>
</table>
<hr />

<!-- TABEL MEMUNCULKAN UNTUK CURRENCY USD -->

<table  border="1" cellspacing="0" style="width:120%;font-size:26px;border-spacing:2px;">
  <tr>
      <tr >
            <th rowspan="2" style="text-align: center;vertical-align: middle; width: 17%">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 20%">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle; width: 20%">No Kontrabon</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 20%">No List payment</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 10%">Periode</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;width: 10%">Descriptions</th>
            <th  colspan="2" style="text-align: center;vertical-align: middle;width: 20%">Foreign USD</th> 
            </tr>                                           
         <tr >
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>  
                                          
        </tr>
<tbody>
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
	$no_kbon = $data['create_date'];
	$no_bpb = $data['no_bpb'];
	$no_po = $data['no_po'];
	$kbon = $data['no_kbon'];
	$ket = $data['ket'];
	$curr = $data['curr'];
	$no_payment = $data['no_payment'];
	$tgl_payment = $data['tgl_payment'];
	$credit_idr = $data['credit_idr'];
	$debit_idr = $data['debit_idr'];
	$credit_usd = $data['credit_usd'];
	$debit_usd = $data['debit_usd'];
	$balance_idr = $data['balance_idr'];
	$tgl_kbon = $data['tgl_kbon'];	
	

   echo '<tr>
	  <td style="width:30%;text-align:center;">'.$no_bpb.'</td>
	  <td style="width:33%;text-align:center;">'.$no_po.'</td>
	  <td style="width:33%;text-align:center;">'.$kbon.'</td>
	  <td style="width:33%;text-align:center;">'.$no_payment.'</td>
      <td style="width:25%;text-align:center;">'.date("d-M-Y",strtotime($no_kbon)).'</td>
	  <td style="width:25%;text-align:center;">'.$ket.'</td>
	  <td style="width:10%;text-align:right;">'.number_format($credit_usd, 2).'</td> 
	  <td style="width:10%;text-align:right;">'.number_format($debit_usd, 2).'</td>		  		  	  
    </tr>';	
};	
?>

<?php
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
    $curr2 = "IDR";
    }else {
    $balance1 = $saldo4 + $t_kbon1 - $t_pay1 - $pph_2 - $potongan;
    $balance = $saldo5 + $t_kbon - $t_pay - $pph - $potongan1;
    $curr2 = "IDR";
}
}

?>



<tr>
      <td colspan="6" style="width:25%;text-align:center;"><b>Jumlah</b></td>	  

	  <td style="width:20%;text-align:right;border-left:none;"><?php echo $curr." ".number_format($t_kbon1, 2) ?></td>
	  <td style="width:20%;text-align:right;border-left:none;"><?php echo $curr." ".number_format($t_pay1, 2) ?></td>


    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:12px">
	
<tr>
        <td >
           
        </td>
        <td  colspan="3" style="text-align: center;font-size: 11px;">
            Foreign USD
        </td>      
    </tr> 
    <tr>
    	<td >
           
        </td>

    </tr>
    <tr>
    	<td >
           
        </td>
        
    </tr>
	<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Begining Balance  
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($saldo4, 2) ?>
		</td>
</tr>

<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Total Credit  
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($t_kbon1, 2) ?>
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
			<?php echo $curr." -".number_format($t_pay1, 2) ?>
		</td>
</tr>

<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Tax (Pph) 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." -".number_format($pph_2, 2) ?>
		</td>
</tr>	

<tr>
		<td width="70%">
			
		</td>
			
		<td width="100px" style="font-weight: bold;">
			Balance
		</td>
<td style="width:1%">:</td>
		<td width="130px" style="text-align:right; font-weight: bold;">
			<?php
			 echo $curr." ".number_format($balance1, 2); 
			 ?>
		</td>		
	</tr>

<!-- equivalent IDR -->
	<tr>
        <td >
           
        </td>
        <td colspan="3" style="text-align: center;font-size: 11px;">
            Equivalent IDR
        </td>      
    </tr> 
    <tr>
    	<td >
           
        </td>

    </tr>
    <tr>
    	<td >
           
        </td>
        
    </tr>
    <tr>
		<td width="70%">
			
		</td>
			
		<td >
			Begining Balance  
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr2." ".number_format($saldo5, 2) ?>
		</td>
</tr>
	<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Total Credit  
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr2." ".number_format($t_kbon, 2) ?>
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
			<?php echo $curr2." -".number_format($t_pay, 2) ?>
		</td>
</tr>

<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Tax (Pph) 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr2." -".number_format($pph, 2) ?>
		</td>
</tr>	

<tr>
		<td width="70%">
			
		</td>
			
		<td width="100px" style="font-weight: bold;">
			Balance
		</td>
<td style="width:1%">:</td>
		<td width="130px" style="text-align:right; font-weight: bold;">
			<?php
			 echo $curr2." ".number_format($balance, 2); 
			 ?>
		</td>		
	</tr>

	
</table>




<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>



	<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;" cellpadding="0" cellspacing="0" border="1" width='500';>

		<tr>	
			<th style="font-size:12px">Made By : </th>
			<th style="font-size:12px">Checked By : </th>
			<th style="font-size:12px">Approved By : </th>
	
		</tr>
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>						
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>			
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
	
		</tr>
		<tr>	
			<td style="font-size:12px;text-align:center;">&nbsp;&nbsp;&nbsp; </td>
			<td style="font-size:12px;text-align:center">&nbsp;&nbsp;&nbsp; </td>
			<td style="font-size:12px;text-align:center">&nbsp;&nbsp;&nbsp; </td>
	
	
		</tr>				
	
		</table>
	</div>

</body>


</html>  

<?php
$html = ob_get_clean();
require_once __DIR__ . '/../../mpdf8/vendor/autoload.php';
include("../../mpdf8/vendor/mpdf/mpdf/src/mpdf.php");

$mpdf=new \mPDF\mPDF();

$mpdf->WriteHTML($html);
$mpdf->Output();
exit;
?>


