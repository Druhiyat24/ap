<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$images = '../../images/img-01.png';
$no_kbon=$_GET['nokontrabon'];
?>

<?php
$sql= "select ftr_dp.id, kontrabon_dp.no_kbon, kontrabon_dp.tgl_kbon, ftr_dp.no_ftr_dp, ftr_dp.tgl_ftr_dp, ftr_dp.no_po, ftr_dp.tgl_po, ftr_dp.supp, SUM(ftr_dp.total) as subtotal, SUM(ftr_dp.dp_value) as tax, SUM(kontrabon_dp.dp_value) as dp, SUM(ftr_dp.balance) as balance, kontrabon_dp.tgl_tempo, kontrabon_dp.supp_inv, kontrabon_dp.no_faktur, kontrabon_dp.create_date, ftr_dp.curr, kontrabon_dp.pph_value from ftr_dp inner join kontrabon_dp on kontrabon_dp.no_dp = ftr_dp.no_ftr_dp where kontrabon_dp.no_kbon = '$no_kbon'";

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

	
  <title>KONTRA BON DP</title>
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
      $sql1 = mysqli_query($conn2,"select nama_supp from kontrabon_dp where no_kbon = '$no_kbon'");
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
<!--       <th style="text-align:center;width: 5%;padding-top: -5px;">NO FTR CBD :</th> -->
      <th style="text-align:center;width: 5%;padding-top: -5px;">DATE CREATED  :</th>
      <th style="text-align:center;width: 5%;padding-top: -5px;">DUE DATE :</th>
<!--       <th style="text-align:center;width: 5%;padding-top: -5px;">NO INVOICE :</th>
      <th style="text-align:center;width: 20%;padding-top: -5px;">NO FAKTUR PAJAK :</th>     -->                       
    </tr>

	<tbody>
	<tr>  	      
<!--       <td style="text-align:center;padding-top: -25px;padding-bottom: -10px;">
      <?php
      $querys = mysqli_query($conn2,"select distinct ftr_dp.no_ftr_dp from ftr_dp inner join kontrabon_dp on kontrabon_dp.no_dp = ftr_dp.no_ftr_dp where kontrabon_dp.no_kbon = '$no_kbon'");
      while($row = mysqli_fetch_array($querys)){
      	$no_cbd = $row['no_ftr_cbd'];
      	echo '<br>';
		echo $no_cbd;
		}?>
		</td> -->
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql1 = mysqli_query($conn2,"select create_date from kontrabon_dp where no_kbon = '$no_kbon'");
      $rows = mysqli_fetch_array($sql1);
      	$create_date = $rows['create_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select tgl_tempo from kontrabon_dp where no_kbon = '$no_kbon'");
      $rows1 = mysqli_fetch_array($sql2);
      	$tgl_tempo = $rows1['tgl_tempo'];
		echo date("d M Y", strtotime($tgl_tempo));
		?>
	</td>
	<!-- <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select supp_inv from kontrabon_dp where no_kbon = '$no_kbon'");
      $rows2 = mysqli_fetch_array($sql3);
      	$supp_inv = $rows2['supp_inv'];
		echo $supp_inv;
		?>		
	</td>								      
	<td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql4 = mysqli_query($conn2,"select no_faktur from kontrabon_dp where no_kbon = '$no_kbon'");
      $rows3 = mysqli_fetch_array($sql4);
      	$no_faktur = $rows3['no_faktur'];
		echo $no_faktur;
		?>
	</td> -->
	</tr>



</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:14px;border-spacing:2px;">
  <tr>

      <th style="width:25%;border: 1px solid black;text-align:center;">No PO</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">PO Date</th>
      <th style="width:25%;border: 1px solid black;text-align:center;">No DP</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">DP Date</th>
<!-- 	  <th style="width:20%;border: 1px solid black;text-align:center;">Item Description</th> -->
<!--       <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Quantity</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;">Unit Price</th> -->
      <th style="width:19%;border: 1px solid black;text-align:center;">Total PO</th>
      <th style="width:19%;border: 1px solid black;text-align:center;">DP Amount</th>
      <th style="width:19%;border: 1px solid black;text-align:center;">Balance</th>            
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
$sum_dp = 0;
$sum_balance = 0;
while($data=mysqli_fetch_array($query)){
	$no_po = $data['no_po'];
	$no_dp = $data['no_ftr_dp'];
	$tgl_dp = $data['tgl_ftr_dp'];
	$tgl_po = $data['tgl_po'];	
	// $item_desc = $data['itemdesc'];
	// $qty = $data['qty'];
	// $uom = $data['uom'];
	$curr = $data['curr'];
	// $price = $data['price'];
	$subtotal = $data['subtotal'];
	$tgl_tempo = $data['tgl_tempo'];
	$supp_inv = $data['supp_inv'];
	$no_faktur = $data['no_faktur'];
	// $ws = $data['ws'];
	$ppn += $data['tax'];
	$pph = $data['pph_value'];
	$dp = $data['dp'];
	$balance = $data['balance'];
	// $sum_qty += $qty;
	$sum_dp += $dp;
	$sum_balance += $balance;
	$sum_sub += $subtotal;
	$sum_total = $sum_sub - $ppn;
   echo '<tr>
      <td style="width:auto;text-align:center;">'.$no_po.'</td>
	  <td style="width:auto;text-align:center;">'.date("d M Y",strtotime($tgl_po)).'</td>  
      <td style="width:auto;text-align:center;">'.$no_dp.'</td>
      <td style="width:auto;text-align:center;">'.date("d M Y",strtotime($tgl_dp)).'</td>
      <td style="width:13%;text-align:right;">'.$curr.' '.number_format($subtotal, 2).'</td>
      <td style="width:13%;text-align:right;">'.$curr.' '.number_format($dp, 2).'</td>
      <td style="width:13%;text-align:right;">'.$curr.' '.number_format($balance, 2).'</td>            
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
      <td colspan="4" style="width:20%;text-align:center;"><b>Jumlah</b></td>

<!--       <td style="width:20%;text-align:center;"></td> -->
<!-- 	  <td style="width:9%;text-align:center;border-right:none"><?php echo number_format($sum_qty,2) ?></td> 
	  <td style="width:5%;text-align:left;border-left:none"><?php echo $uom ?></td> 
      <td style="width:5%;text-align:center;border-right:none"></td>
	  <td style="width:9%;text-align:center;border-left:none"></td> -->
<!--       <td style="width:5%;text-align:center;border-right:none"><?php echo $curr ?></td> -->
	  <td style="width:12%;text-align:right;border-left:none"><?php echo $curr.' '.number_format($sum_sub, 2) ?></td>
	  <td style="width:12%;text-align:right;border-left:none"><?php echo $curr.' '.number_format($sum_dp, 2) ?></td>
	  <td style="width:12%;text-align:right;border-left:none"><?php echo $curr.' '.number_format($sum_balance, 2) ?></td>	  	  
<!--	  <td style="width:15%;text-align:center;display: none;"></td>
	  <td style="width:15%;text-align:center;display: none;"></td> 
      <td style="width:15%;text-align:center;display: none;"></td>-->
    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:12px">

	<tr>
		<td width="68%">
			
		</td>
			
		<td>
			Total PO
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
		<td width="68%">
			
		</td>
			
		<td >
			Balance
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." ".number_format($sum_total, 2) ?>
		</td>
</tr>	
	<tr>
		<td width="68%">
			
		</td>
			
		<td style="font-weight: bold;">
			DP Amount 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right; font-weight: bold;">
			<?php echo $curr." ".number_format($ppn, 2); ?>
		</td>		
	</tr>	
	<!-- <tr>
		<td width="70%">
			
		</td>
			
		<td>
			Pph 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr."( ".number_format($pph, 2)." )"; ?>
		</td>		
	</tr>	 -->

	
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
            <td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;">
             <?php
            $sql1 = mysqli_query($conn2,"select create_user from kontrabon_h_dp where no_kbon = '$no_kbon'");
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