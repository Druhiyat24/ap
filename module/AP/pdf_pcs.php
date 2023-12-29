<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_kbon=$_GET['nokontrabon'];
?>

<?php
$sql= "select bpb_new.id, kontrabon_h.balance,kontrabon_h.total, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb_new.no_bpb, bpb_new.tgl_bpb, bpb_new.pono, bpb_new.tgl_po, bpb_new.ws, bpb_new.supplier, bpb_new.itemdesc, bpb_new.qty, bpb_new.price, bpb_new.qty * bpb_new.price as subtotal, (bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100) as tax, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb_new.curr, bpb_new.uom, kontrabon.pph_value from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb inner join kontrabon_h on kontrabon.no_kbon = kontrabon_h.no_kbon where kontrabon.no_kbon = '$no_kbon'";

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
	<td ><h5>KONTRA BON : <?php echo $no_kbon ?></h5></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h5>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from kontrabon where no_kbon = '$no_kbon'");
      $rows = mysqli_fetch_array($sql1);
      	$supplier = $rows['nama_supp'];
		echo $supplier;
		?>
	</h5>
	</td>
</tr>
</table>

<hr />
<table style="font-size:11px;">
	<thead>
    <tr>
      <th style="text-align:center;width: 5%;padding-top: -5px;">#WS :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">DATE CREATED  :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">DUE DATE :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">LATE :</th>
      <th style="text-align:center;width: 10%;padding-top: -5px;">NO INVOICE :</th>
      <th style="text-align:center;width: 15%;padding-top: -5px;">NO FAKTUR PAJAK :</th>                           
    </tr>

	<tbody>
	<tr>  	      
      <td style="text-align:center;padding-top: -25px;padding-bottom: -10px;">
      <?php
      $querys = mysqli_query($conn2,"select distinct bpb_new.ws from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb where kontrabon.no_kbon = '$no_kbon'");
      while($row = mysqli_fetch_array($querys)){
      	$no_ws = $row['ws'];
      	echo '<br>';
		echo $no_ws;
		}?>
		</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql1 = mysqli_query($conn2,"select create_date from kontrabon where no_kbon = '$no_kbon'");
      $rows = mysqli_fetch_array($sql1);
      	$create_date = $rows['create_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select tgl_tempo from kontrabon where no_kbon = '$no_kbon'");
      $rows1 = mysqli_fetch_array($sql2);
      	$tgl_tempo = $rows1['tgl_tempo'];
		echo date("d M Y", strtotime($tgl_tempo));
		?>
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql4 = mysqli_query($conn2,"select tgl_tempo from kontrabon where no_kbon = '$no_kbon'");
      $rows3 = mysqli_fetch_array($sql4);
      	$date_now = date("Y-m-d");          
    	$tgl_tempo = $rows3['tgl_tempo'];
    	$diff =strtotime($date_now) - strtotime($tgl_tempo);
    	$date_diff = round($diff / 86400);

		echo $date_diff;echo " Days";
		?>
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql4 = mysqli_query($conn2,"select no_faktur from kontrabon where no_kbon = '$no_kbon'");
      $rows3 = mysqli_fetch_array($sql4);
      	$no_faktur = $rows3['no_faktur'];
		echo $no_faktur;
		?>
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select supp_inv from kontrabon where no_kbon = '$no_kbon'");
      $rows2 = mysqli_fetch_array($sql3);
      	$supp_inv = $rows2['supp_inv'];
		echo $supp_inv;
		?>		
	</td>								      
	</tr>



</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:16px;border-spacing:2px;">
  <tr>

      <th style="width:30%;border: 1px solid black;text-align:center;">No.PO</th>
      <th style="width:10%;border: 1px solid black;text-align:center;display: none;">No.BPB</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Tanggal BPB</th>
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
	$subtotal = $data['total'];
	$balance = $data['balance'];
	$tgl_tempo = $data['tgl_tempo'];
	$supp_inv = $data['supp_inv'];
	$no_faktur = $data['no_faktur'];
	$ws = $data['ws'];
	$ppn += $data['tax'];
	$pph = $data['pph_value'];
	$sum_qty += $qty;
	$sum_sub += $subtotal;
	$sum_pyb += $balance;
	$sum_total = $sum_sub + $ppn - $pph;
   echo '<tr>
      <td style="width:20%;text-align:center;">'.$no_po.'</td>
      <td style="width:20%;text-align:center;">'.$no_bpb.'</td>
      <td style="width:auto;text-align:center;display:none;">'.date("d M Y",strtotime($tgl_bpb)).'</td>
	  <td style="width:auto;text-align:center;display:none;">'.date("d M Y",strtotime($tgl_po)).'</td>
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
      <td colspan="5" style="width:25%;border: 1px solid black;text-align:center;font-size:20px"><b>Jumlah</b></td>
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
			
		<td>
			Pph 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr."( ".number_format($pph, 2)." )"; ?>
		</td>		
	</tr>	

	<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Total KontraBon 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right;font-weight: bold;">
			<?php echo $curr." ".number_format($sum_total, 2) ?>
		</td>
	</tr>
	<tr>
		<td width="70%">
			
		</td>
		<td >
			Total Payable
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right;font-weight: bold;">
			<?php echo $curr." ".number_format($balance, 2) ?>
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