<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$images = '../../images/img-01.png';
$no_kbon=$_GET['nokontrabon'];
?>

<?php

$sql= "select bpb.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb.bpbno_int no_bpb, bpb.bpbdate tgl_bpb, bpb.pono, po_header.podate tgl_po, act_costing.kpno ws, kontrabon.nama_supp supplier, masteritem.itemdesc ,IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) qty, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty1, bpb.price, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) * bpb.price as subtotal, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) * bpb.price as subtotal1, kontrabon.tax, '0' as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb.curr, bpb.unit uom, kontrabon.pph_value, kontrabon.dp_value from bpb inner join kontrabon on kontrabon.no_bpb = bpb.bpbno_int inner join po_header on po_header.pono = bpb.pono left JOIN jo on jo.id = bpb.id_jo left JOIN jo_det on jo_det.id_jo = jo.id left JOIN so on so.id = jo_det.id_so left JOIN act_costing on act_costing.id = so.id_cost left JOIN masteritem on masteritem.id_item = bpb.id_item where kontrabon.no_kbon = '$no_kbon'
union
	select bppb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bppb_new.no_bppb as no_bpb, bppb_new.tgl_bppb as tgl_bpb, bppb_new.no_po, bppb_new.tgl_po, '' as ws, bppb_new.supplier, bppb_new.itemdesc, (- bppb_new.qty) as qty, '0' as qty4, bppb_new.price, (- bppb_new.qty * bppb_new.price) as subtotal,'0' as subtotal4,'0' as tax, (- (bppb_new.qty * bppb_new.price) * (bppb_new.tax / 100)) as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bppb_new.curr, bppb_new.uom, kontrabon.pph_value, kontrabon.dp_value from bppb_new inner join return_kb on return_kb.no_bpbrtn = bppb_new.no_bppb inner join kontrabon on kontrabon.no_kbon = return_kb.no_kbon where kontrabon.no_kbon = '$no_kbon' and bppb_new.status != 'Cancel' GROUP BY bppb_new.id order by no_bpb asc,id asc";

	//$sql= "select bpb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb_new.no_bpb, bpb_new.tgl_bpb, bpb_new.pono, bpb_new.tgl_po, bpb_new.ws, bpb_new.supplier, bpb_new.itemdesc, bpb_new.qty, bpb_new.qty as qty1, bpb_new.price, bpb_new.qty * bpb_new.price as subtotal, bpb_new.qty * bpb_new.price as subtotal1, (bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100) as tax, '0' as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb_new.curr, bpb_new.uom, kontrabon.pph_value, kontrabon.dp_value from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb  where kontrabon.no_kbon = '$no_kbon' and bpb_new.status != 'Cancel'";


// $sql= "select bpb.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb.bpbno_int no_bpb, bpb.bpbdate tgl_bpb, bpb.pono, po_header.podate tgl_po, act_costing.kpno ws, kontrabon.nama_supp supplier, masteritem.itemdesc ,IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) qty, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty1, bpb.price, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) * bpb.price as subtotal, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) * bpb.price as subtotal1, (IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) * bpb.price) * (po_header.tax / 100) as tax, '0' as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb.curr, bpb.unit uom, kontrabon.pph_value, kontrabon.dp_value from bpb inner join kontrabon on kontrabon.no_bpb = bpb.bpbno_int inner join po_header on po_header.pono = bpb.pono left JOIN jo on jo.id = bpb.id_jo left JOIN jo_det on jo_det.id_jo = jo.id left JOIN so on so.id = jo_det.id_so left JOIN act_costing on act_costing.id = so.id_cost left JOIN masteritem on masteritem.id_item = bpb.id_item where kontrabon.no_kbon = '$no_kbon' union select bppb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bppb_new.no_bppb as no_bpb, bppb_new.tgl_bppb as tgl_bpb, bppb_new.no_po, bppb_new.tgl_po, '' as ws, bppb_new.supplier, bppb_new.itemdesc, (- bppb_new.qty) as qty, '0' as qty4, bppb_new.price, (- bppb_new.qty * bppb_new.price) as subtotal,'0' as subtotal4,'0' as tax, (- (bppb_new.qty * bppb_new.price) * (bppb_new.tax / 100)) as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bppb_new.curr, bppb_new.uom, kontrabon.pph_value, kontrabon.dp_value from bppb_new inner join return_kb on return_kb.no_bpbrtn = bppb_new.no_bppb inner join kontrabon on kontrabon.no_kbon = return_kb.no_kbon where kontrabon.no_kbon = '$no_kbon' and bppb_new.status != 'Cancel' GROUP BY bppb_new.id order by no_bpb asc,id asc";

$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));

$sqll = mysqli_query($conn2,"select jml_return, jml_potong from potongan where no_kbon = '$no_kbon' group by no_kbon");    
$rowl = mysqli_fetch_assoc($sqll);

$sqll12 = mysqli_query($conn2,"select sum(pph_value) as pph from kontrabon where no_kbon = '$no_kbon' ");    
$rowl12 = mysqli_fetch_assoc($sqll12);

$sqll15 = mysqli_query($conn2,"select sum((qty * price) * (tax / 100)) as tax_return from bppb_new  where no_kbon = '$no_kbon' ");    
$rowl15 = mysqli_fetch_assoc($sqll15);
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

	
  <title>KONTRA BON</title>
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
	<td ><h4>KONTRA BON : <?php echo $no_kbon ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from kontrabon where no_kbon = '$no_kbon'");
      $rows = mysqli_fetch_array($sql1);
      	$supplier = $rows['nama_supp'];
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
   <!--    <th style="text-align:center;width: 5%;padding-top: -5px;">#WS :</th> -->
       <th style="text-align:center;width: 5%;padding-top: -5px;">DATE CREATED  :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">KONTRABON DATE :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">DUE DATE :</th>
      <!-- <th style="text-align:center;width: 5%;padding-top: -5px;">NO INVOICE :</th>
      <th style="text-align:center;width: 20%;padding-top: -5px;">NO FAKTUR PAJAK :</th>      -->                      
    </tr>

	<tbody>
	<tr>  	      
<!--       <td style="text-align:center;padding-top: -25px;padding-bottom: -10px;">
      <?php
      $querys = mysqli_query($conn2,"select distinct bpb_new.ws from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb where kontrabon.no_kbon = '$no_kbon'");
      while($row = mysqli_fetch_array($querys)){
      	$no_ws = $row['ws'];
      	echo '<br>';
		echo $no_ws;
		}?>
		</td> -->
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
      $sql3 = mysqli_query($conn2,"select tgl_kbon from kontrabon where no_kbon = '$no_kbon'");
      $rows = mysqli_fetch_array($sql3);
      	$tgl_kbon = $rows['tgl_kbon'];
		echo date("d M Y", strtotime($tgl_kbon));
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
	<!-- <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select supp_inv from kontrabon where no_kbon = '$no_kbon'");
      $rows2 = mysqli_fetch_array($sql3);
      	$supp_inv = $rows2['supp_inv'];
		echo $supp_inv;
		?>		
	</td>								      
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql4 = mysqli_query($conn2,"select no_faktur from kontrabon where no_kbon = '$no_kbon'");
      $rows3 = mysqli_fetch_array($sql4);
      	$no_faktur = $rows3['no_faktur'];
		echo $no_faktur;
		?>
	</td> -->
	</tr>



</tbody>
</table>
<hr />

<?php
$query1 = mysqli_query($conn2,$sql)or die(mysqli_error());
	$data1=mysqli_fetch_array($query1);
	$curr1 = $data1['curr'];
 ?>
<table  border="1" cellspacing="0" style="width:100%;font-size:10px;border-spacing:2px;">
  <tr>

      <th style="width: 16%;border: 1px solid black;text-align:center;">No PO</th>
      <th style="width: 9%;border: 1px solid black;text-align:center;">PO Date</th>
      <th style="width: 16%;border: 1px solid black;text-align:center;">No BPB</th>
      <th style="width: 9%;border: 1px solid black;text-align:center;">BPB Date</th>
	  <th style="width: 21%;border: 1px solid black;text-align:center;">Item Description</th>
      <th style="width: 8%;border: 1px solid black;text-align:center;">Quantity</th>
      <th style="width: 11%;border: 1px solid black;text-align:center;">Unit Price (<?php echo $curr1 ?>)</th>
      <th style="width: 10%;border: 1px solid black;text-align:center;">Amount BPB (<?php echo $curr1 ?>)</th>
<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody >
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
$no_po = "";
$no_bpb = "";
$tgl_bpb = "";
$tgl_po = "";
$item_desc = "";
$qty = 0;
$uom = "";
$curr = "";
$price = 0;
$subtotal = 0;
$tgl_tempo = "";
$supp_inv = "";
$no_faktur = "";
$ws = "";
$ppn = 0;
$ppn_return = 0;
$pph = 0;
$dp = 0;
$jml_return = 0;
$jml_potong = 0;
$potong = 0;
$sum_qty = 0;
$sum_sub = 0;
$sum_price = 0;
$sum_total = 0;
$h_ppn = 0;
$ppn_return += $rowl15['tax_return'];
while($data=mysqli_fetch_array($query)){
	$no_po = $data['pono'];
	if ($no_po == '') {
		$no_po = '-';
	}else{
		$no_po = $no_po;
	}
	$no_bpb = $data['no_bpb'];
	$tgl_bpb = $data['tgl_bpb'];
	$tgl_po = $data['tgl_po'];	
	if ($tgl_po == '' || $tgl_po == '1970-01-01' || $tgl_po == '0000-00-00') {
		$tgl_po = '-';
	}else{
		$tgl_po = date("d M Y",strtotime($tgl_po));
	}
	$item_desc = $data['itemdesc'];
	$qty = $data['qty'];
	$qty1 = $data['qty1'];
	$uom = $data['uom'];
	$curr = $data['curr'];
	$price = $data['price'];
	$subtotal = $data['subtotal'];
	$subtotal1 = $data['subtotal1'];
	$tgl_tempo = $data['tgl_tempo'];
	$supp_inv = $data['supp_inv'];
	$no_faktur = $data['no_faktur'];
	$ws = $data['ws'];
	$ppn += $data['tax'];
	$pph = $rowl12['pph'];
	$dp = $data['dp_value'];
	$jml_return = $rowl['jml_return'];
	$jml_potong = $rowl['jml_potong'];
	$h_ppn = $ppn - $ppn_return;
	$potong = $jml_potong;
	$sum_qty += $qty;
	$sum_sub += $subtotal;
	$sum_price += $price;
	$sum_total = $sum_sub + ($ppn -$ppn_return)  - $pph - $dp + $potong;
   echo '<tr>
      <td style="width:16%;text-align:center;">'.$no_po.'</td>
	  <td style="width:9%;text-align:center;">'.$tgl_po.'</td>
      <td style="width:16%;text-align:center;">'.$no_bpb.'</td>
      <td style="width:9%;text-align:center;">'.date("d M Y",strtotime($tgl_bpb)).'</td>
      <td style="width:21%;text-align:center;">'.$item_desc.'</td>
	  <td style="width:8%;text-align:center;">'.number_format($qty, 2).' '.$uom.'</td> 
	  <td style="width:11%;text-align:right;">'.number_format($price, 2).'</td>
	  <td style="width:10%;text-align:right;">'.number_format($subtotal, 2).'</td>
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
      <td colspan="5" style="width:68%;border: 1px solid black;text-align:center;font-size:10px"><b>Jumlah</b></td>
	  <td style="width:8%;text-align:center;"><?php echo number_format($sum_qty,2) ?></td> 
	  <td style="width:11%;text-align:center;border-left:none"></td>
      <td style="width:13%;text-align:right;"><?php echo $curr ?> <?php echo number_format($sum_sub, 2) ?></td>
<!--	  <td style="width:15%;text-align:center;display: none;"></td>
	  <td style="width:15%;text-align:center;display: none;"></td> 
      <td style="width:15%;text-align:center;display: none;"></td>-->
    </tr>

  </tbody>
</table> 
<br>

<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
<table width="100%" border="0" style="page-break-inside: avoid;font-size:12px;">

	<tr>
		<td width="58%">
			
		</td>
			
		<td>
			SubTotal
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($sum_sub, 2); ?>
		</td>		
	</tr>

	<tr>
		<td width="58%">
			
		</td>
			
		<td>
			Ppn 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php
      $sqltax = mysqli_query($conn2,"select tax from kontrabon_h where no_kbon = '$no_kbon'");
      $rowstax = mysqli_fetch_array($sqltax);
      	$jml_tax = $rowstax['tax'];
	  echo $curr." ".number_format($jml_tax, 2).""; ?>
		</td>		
	</tr>		

	<tr>
		<td width="58%">
			
		</td>
			
		<td>
			Total CBD or DP
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr."( - ".number_format($dp, 2)." )"; ?>
		</td>		
	</tr>	
<tr>
		<td width="58%">
			
		</td>
			
		<td>
			Pph 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right;">
			<?php echo $curr."( - ".number_format($pph, 2)." )"; ?>
		</td>		
	</tr>

	<tr>
		<td width="58%">
			
		</td>
			
		<td>
			Penambahan / Pengurangan 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right;">

			<?php if($potong >= 1) {
				echo $curr." ".number_format($potong, 2)."";
				}else{
					echo $curr."( - ".number_format(abs($potong), 2)." )";
				} ?>
		</td>		
	</tr>	
	
	<tr>
		<td width="58%">
			
		</td>
			
		<td style="font-weight: bold;">
			Total Kontrabon
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right;font-weight: bold;">
			<?php
      $sqltotal = mysqli_query($conn2,"select total from kontrabon_h where no_kbon = '$no_kbon'");
      $rowstotal = mysqli_fetch_array($sqltotal);
      	$jml_total = $rowstotal['total'];
	  echo $curr." ".number_format($jml_total, 2).""; ?>
		</td>
</tr>

</table>
</div>



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
            <td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;">
              <?php
            $sql1 = mysqli_query($conn2,"select create_user from kontrabon_h where no_kbon = '$no_kbon'");
            $rows = mysqli_fetch_array($sql1);
            $create_by = $rows['create_user'];
             echo $create_by;
            ?>
         </td>
            <td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;"><?php
            $sql1 = mysqli_query($conn2,"select confirm1 from ttd");
            $rows = mysqli_fetch_array($sql1);
            $confirm1 = $rows['confirm1'];
             echo $confirm1;
            ?>
         </td>
         <td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;"><?php
            $sql1 = mysqli_query($conn2,"select confirm2 from ttd");
            $rows = mysqli_fetch_array($sql1);
            $confirm2 = $rows['confirm2'];
             echo $confirm2;
            ?>
         </td>
		</tr>
		<tr>    
            <td style="font-size:12px;text-align:center;">AP Staff</td>
            <td style="font-size:12px;text-align:center">Supervisor</td>
            <td style="font-size:12px;text-align:center">Finance Accounting Manager</td>
        </tr> 				
	
		</table>
		<br />

		<table style="page-break-inside: avoid; font-size:11px;" border="0">
			<tr>
				<td style="font-weight: bold">NOTE :</td>
			</tr>
			<tr>
				<td>Kontra Bon Number : <?php echo $no_kbon ?></td>
			</tr>
			<tr>
				<td>Total Kontra Bon : <?php echo $curr." ".number_format($jml_total, 2) ?></td>
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