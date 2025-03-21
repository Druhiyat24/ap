<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_req=$_GET['no_req'];
?>

<?php
$sql= "select no_po,no_bpb,item,qty,price,(qty * price) total,attn,seasons,no_reff from req_dn where no_req = '$no_req'";

$rs=mysqli_fetch_array(mysql_query($conn2,$sql));
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

	border:1Spx solid #000000;

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

	
  <title>Transfer Invoice</title>
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
	<td ><h4><?php echo $no_req ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from req_dn_h where no_req = '$no_req'");
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
      <th style="text-align:left;width: 25%;padding-top: -5px;">DATE CREATED :</th>
      <th style="text-align:left;width: 25%;padding-top: -5px;">REQUEST DATE :</th> 
      <th style="text-align:left;width: 25%;padding-top: -5px;">DOCUMENT TYPE :</th>                              
    </tr>

	<tbody>
	<tr>  	      
	<td style="text-align:left;padding-left: 25px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select created_date from req_dn_h where no_req = '$no_req'");
      $rows2 = mysqli_fetch_array($sql2);
      	$create_date = $rows2['created_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:left;padding-left: 15px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select tgl_req from req_dn_h where no_req = '$no_req'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tglftrcbd = $rows3['tgl_req'];
		echo date("d M Y", strtotime($tglftrcbd));
		?>		
	</td>	
	<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">REQUEST DEBIT NOTE</td>								      
	</tr>
</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:16px;border-spacing:2px;">
  <tr>
      <th style="width:14%;border: 1px solid black;text-align:center;">No PO</th>
      <th style="width:13%;border: 1px solid black;text-align:center;">No BPB</th>
      <th style="width:20%;border: 1px solid black;text-align:center;">Item</th>     
      <th style="width:10%;border: 1px solid black;text-align:center;">Qty</th>
      <th style="width:10%;border: 1px solid black;text-align:center;">Price</th>
      <th style="width:18%;border: 1px solid black;text-align:center;">Total</th>     
      <th style="width:15%;border: 1px solid black;text-align:center;">Attn</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Seasons</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Reff</th>

<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody>
<?php
$sum_total = 0;
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
	$no_po = $data['no_po'];
	$item = $data['item'];
	$qty = $data['qty'];
	$price = $data['price'];
	$total = $data['total'];
	$attn = $data['attn'];
	$seasons = $data['seasons'];
	$no_reff = $data['no_reff'];
	$sum_total += $data['total'];;
   echo '<tr >
      <td style="text-align:center;">'.$no_po.'</td>
      <td style="text-align:center;">'.$data['no_bpb'].'</td>
      <td style="text-align:center;">'.$item.'</td>
	  <td style="text-align:right;border-left:none">'.number_format($qty, 2).'</td>
	  <td style="text-align:right;border-left:none">'.number_format($price, 2).'</td>
	  <td style="text-align:right;border-left:none">'.number_format($total, 2).'</td>
	  <td style="text-align:center;">'.$attn.'</td>
	  <td style="text-align:center;">'.$seasons.'</td>
	  <td style="text-align:center;">'.$no_reff.'</td>		  		  	  
    </tr>';	
};	
?>



<!-- <tr>
      <td colspan="5" style="width:30%;text-align:center;"><b>Jumlah</b></td>
	  <td style="width:auto;text-align:right;border-left:none"><b><?php echo number_format($sum_total, 2) ?></b></td>
      
    </tr> -->

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:13px">

	<tr>
		<th width="65%">
			
		</th>
			
		<th>
			Grand Total
		</th>
<th style="width:1%">:</th>
		<th style="text-align:right">
			<?php echo number_format($sum_total, 2); ?>
		</th>		
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
ob_clean();
$mpdf->Output();
exit;
?>