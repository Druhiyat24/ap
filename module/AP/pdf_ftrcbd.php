<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$noftrcbd=$_GET['noftrcbd'];
?>

<?php
$sql= "select no_ftr_cbd, tgl_ftr_cbd, no_po, no_pi, tgl_po, supp, SUM(subtotal) as subtotal, SUM(tax) as tax, SUM(total) as total,biaya_tambahan, curr, create_user, status from ftr_cbd where no_ftr_cbd = '$noftrcbd' and status !='Cancel'";

$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));
ob_start();
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

	
  <title>FTR CBD</title>
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
	<td ><h4>No FTR : <?php echo $noftrcbd ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select supp from ftr_cbd where no_ftr_cbd = '$noftrcbd'");
      $rows = mysqli_fetch_array($sql1);
      	$supplier = $rows['supp'];
		echo $supplier;
		?>
	</h4>
	</td>
</tr>
</table>
<hr />
<table style="font-size:12px;">
	<thead>
    <tr>
      <th style="text-align:left;width: 20%;padding-top: -5px;">DATE CREATED  :</th>
      <th style="text-align:left;width: 80%;padding-top: -5px;">TGL FTR CBD  :</th>                               
    </tr>

	<tbody>
	<tr>  	      
	<td style="text-align:left;padding-left: 25px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select create_date from ftr_cbd where no_ftr_cbd = '$noftrcbd'");
      $rows2 = mysqli_fetch_array($sql2);
      	$create_date = $rows2['create_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:left;padding-left: 25px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select tgl_ftr_cbd from ftr_cbd where no_ftr_cbd = '$noftrcbd'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tglftrcbd = $rows3['tgl_ftr_cbd'];
		echo date("d M Y", strtotime($tglftrcbd));
		?>		
	</td>									      
	</tr>
</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:12px;border-spacing:2px;">
  <tr>
      <th style="width:20%;border: 1px solid black;text-align:center;">No.PO</th>
      <th style="width:20%;border: 1px solid black;text-align:center;">No.PI</th>
      <th style="width:20%;border: 1px solid black;text-align:center;">Tanggal PO</th>
      <th style="width:25%;border: 1px solid black;text-align:center;">Amount CBD</th>      
<!-- 	  <th style="width:20%;border: 1px solid black;text-align:center;">Item Description</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Quantity</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Unit Price</th> -->

<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody>
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
	$no_po = $data['no_po'];
	$no_pi = $data['no_pi'];
	$tgl_po = $data['tgl_po'];
	$create_user = $data['create_user'];	
	// $item_desc = $data['itemdesc'];
	// $qty = $data['qty'];
	// $uom = $data['uom'];
	$curr = $data['curr'];
	// $price = $data['price'];
	$subtotal = $data['subtotal'];
	$tambahan = $data['biaya_tambahan'];
	// $tgl_tempo = $data['tgl_tempo'];
	// $supp_inv = $data['supp_inv'];
	// $no_faktur = $data['no_faktur'];
	// $ws = $data['ws'];
	$ppn = $data['tax'];
	// $sum_qty += $qty;
	$total = $data['total'] + $tambahan;
   echo '<tr>
      <td style="width:20%;text-align:center;">'.$no_po.'</td>
      <td style="width:20%;text-align:center;">'.$no_pi.'</td>
	  <td style="width:20%;text-align:center;">'.date("d M Y",strtotime($tgl_po)).'</td>
	  <td style="width:auto;text-align:right;border-left:none">'.$curr.' '.number_format($subtotal, 2).'</td>
    </tr>';	
};	
?>

<!-- <?php
//$qty1 +=$qty1+ $qty;				
//$mata_uang = $data['curr'];
//$unit = $data['uom']; 

//$total_curr_bef_tax  = $total_curr_bef_tax + $data['subtotal'];
//$total_curr = $total_curr + $data['subtotal'];	
 
//$grand_total = $total_curr_bef_tax + $ppn_nya_____ - $value_pph + $total_utang_debit + $total_other;
 
?> -->

<tr>
      <td colspan="3" style="width:20%;text-align:center;"><b>Jumlah</b></td>
<!--       <td style="width:20%;text-align:center;"></td> -->
<!-- 	  <td style="width:9%;text-align:center;border-right:none"><?php echo number_format($sum_qty,2) ?></td> 
	  <td style="width:5%;text-align:left;border-left:none"><?php echo $uom ?></td> 
      <td style="width:5%;text-align:center;border-right:none"></td>
	  <td style="width:9%;text-align:center;border-left:none"></td> -->
	  <td style="width:auto;text-align:right;border-left:none"><?php echo $curr.' '.number_format($subtotal, 2) ?></td>
<!-- 	  <td style="width:15%;text-align:center;display: none;"></td>
	  <td style="width:15%;text-align:center;display: none;"></td> 
      <td style="width:15%;text-align:center;display: none;"></td> -->
    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:12px">

	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			Total Before Tax
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($subtotal, 2); ?>
		</td>		
	</tr>	

<!-- pajak -- -->	

<!--
	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			PPn 10% 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php 
				// echo $mata_uang." ".number_format((float)$ppn_nya, 2, '.', ','); 
			?>
		</td>		
	</tr>	
-->
	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			Ppn 10% 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($ppn, 2); ?>
		</td>		
	</tr>	

	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			Ongkos Kirim 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($tambahan, 2); ?>
		</td>		
	</tr>	
<!-- 	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			Pph 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr."( ".number_format($pph, 2)." )"; ?>
		</td>		
	</tr> -->	

	<tr>
		<td width="70%">
			
		</td>
			
		<td style="font-weight: bold;">
			Total 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right; font-weight: bold;">
			<?php echo $curr." ".number_format($total, 2) ?>
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
	<table style="page-break-inside:avoid;" cellpadding="0" cellspacing="0" border="1" width='500';>

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