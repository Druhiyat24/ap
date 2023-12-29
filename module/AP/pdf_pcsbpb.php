<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_bpb=$_GET['nobpb'];
?>

<?php
$sql= "select bpb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb_new.no_bpb, bpb_new.tgl_bpb, bpb_new.pono, bpb_new.tgl_po, bpb_new.ws, bpb_new.supplier, bpb_new.itemdesc, bpb_new.qty, bpb_new.price, bpb_new.qty * bpb_new.price as subtotal, (bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100) as tax, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb_new.curr, bpb_new.uom, kontrabon.pph_value from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb where bpb_new.no_bpb = '$no_bpb' group by bpb_new.no_bpb";

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

	border:3px solid #000000;

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
  <table style="width:100%;height:500px;border-collapse: collapse;">
    <tr>
      <td style="width:30%;text-align:center;border: 1px solid black;"><img src="../../images/img-01.png" style="heigh:70px; width:80px;"></td>
       <td style="width:20%;text-align:center;"></td>
      <td style="width:50%;border: 1px solid black;text-align:center;"><font color=black>PT.NIRWANA ALABARE GARMENT</font></td>
    </tr>
  </table>
  <hr />
<br>
<table width="100%">
<tr>
	<td style="text-align: center;"><h4>PAYABLE CARD STATEMENT</h4></td>
</tr>
</table>

<hr />
<table width="100%">
<tr>
	<td ><h4>NO BPB : <?php echo $no_bpb ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select supplier from bpb_new where no_bpb = '$no_bpb'");
      $rows = mysqli_fetch_array($sql1);
      	$supplier = $rows['supplier'];
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
      <th style="text-align:center;width: 5%;padding-top: -5px;"></th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">BPB DATE :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">END DATE :</th>                          
      <th style="text-align:center;width: 5%;padding-top: -5px;"></th>
    </tr>

	<tbody>
	<tr>  	      
      <td style="text-align:center;padding-top: -25px;padding-bottom: -10px;">
      
		</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql1 = mysqli_query($conn2,"select tgl_bpb from bpb_new where no_bpb = '$no_bpb'");
      $rows = mysqli_fetch_array($sql1);
      	$create_date = $rows['tgl_bpb'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select end_date from bpb_new where no_bpb = '$no_bpb'");
      $rows1 = mysqli_fetch_array($sql2);
      	$tgl_tempo = $rows1['end_date'];
		echo date("d M Y", strtotime($tgl_tempo));
		?>
	</td>
									      
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      		
	</td>
	</tr>



</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:16px;border-spacing:2px;">
  <tr>

      <th style="width:25%;border: 1px solid black;text-align:center;">No.PO</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Tanggal PO</th>
	  <th style="width:20%;border: 1px solid black;text-align:center;">Item Description</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Quantity</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Unit Price</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Amount BPB</th>
<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody >
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
$ppn = 0;
$sum_qty = 0;
$sum_sub = 0;
$sum_total = 0;
while($data=mysqli_fetch_array($query)){
	$no_po = $data['pono'];
	$no_bpb = $data['no_bpb'];
	$tgl_bpb = $data['tgl_bpb'];
	$tgl_po = $data['tgl_po'];	
	$item_desc = $data['itemdesc'];
	$qty = $data['qty'];
	$uom = $data['uom'];
	$curr = $data['curr'];
	$price = $data['price'];
	$subtotal = $data['subtotal'];
	// $tgl_tempo = $data['tgl_tempo'];
	// $supp_inv = $data['supp_inv'];
	// $no_faktur = $data['no_faktur'];
	$ws = $data['ws'];
	$ppn += $data['tax'];
	$pph = $data['pph_value'];
	$sum_qty += $qty;
	$sum_sub += $subtotal;
	$sum_ppn += $sum_sub * $ppn;
	$sum_total = $sum_sub + $ppn;
   echo '<tr>
      <td style="width:20%;text-align:center;">'.$no_po.'</td>
	  <td style="width:auto;text-align:center;">'.date("d M Y",strtotime($tgl_po)).'</td>
      <td style="width:15%;text-align:center;">'.$item_desc.'</td>
	  <td style="width:9%;text-align:center;border-right:none">'.number_format($qty, 2).'</td> 
	  <td style="width:5%;text-align:left;border-left:none">'.$uom.'</td> 
      <td style="width:5%;text-align:center;border-right:none">'.$curr.'</td>
	  <td style="width:9%;text-align:center;border-left:none">'.number_format($price, 2).'</td>
      <td style="width:5%;text-align:center;border-right:none">'.$curr.'</td>
	  <td style="width:12%;text-align:center;border-left:none">'.number_format($subtotal, 2).'</td>
    </tr>';	
};	
?>

<!--<?php
//$qty1 +=$qty1+ $qty;				
//$mata_uang = $data['curr'];
//$unit = $data['uom']; 

//$total_curr_bef_tax  = $total_curr_bef_tax + $data['subtotal'];
//$total_curr = $total_curr + $data['subtotal'];	
 
//$grand_total = $total_curr_bef_tax + $ppn_nya_____ - $value_pph + $total_utang_debit + $total_other;
 
?>-->

<tr>
      <td colspan="3" style="width:25%;border: 1px solid black;text-align:center;font-size:20px"><b>Jumlah</b></td>
	  <td style="width:9%;text-align:center;border-right:none"><?php echo number_format($sum_qty,2) ?></td> 
	  <td style="width:5%;text-align:left;border-left:none"><?php echo $uom ?></td> 
      <td style="width:5%;text-align:center;border-right:none"></td>
	  <td style="width:9%;text-align:center;border-left:none"></td>
      <td style="width:5%;text-align:center;border-right:none"><?php echo $curr ?></td>
	  <td style="width:12%;text-align:center;border-left:none"><?php echo number_format($sum_sub, 2) ?></td>
<!--	  <td style="width:15%;text-align:center;display: none;"></td>
	  <td style="width:15%;text-align:center;display: none;"></td> 
      <td style="width:15%;text-align:center;display: none;"></td>-->
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
			<?php echo $curr." ".number_format($sum_sub, 2); ?>
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
			
		<td >
			Total 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($sum_total, 2) ?>
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



	<table cellpadding="0" cellspacing="0" border="1" width='500';>

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