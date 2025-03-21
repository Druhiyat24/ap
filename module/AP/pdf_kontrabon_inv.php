<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$doc_number=$_GET['doc_number'];
?>

<?php
$sql= "select no_invoice,tgl_invoice,amount, COALESCE(no_reff,'-') no_reff from ir_invoice_supp a INNER JOIN ir_invoice_supp_h b on b.doc_number = a.doc_number where a.doc_number = '$doc_number'";

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
	<td ><h4><?php echo $doc_number ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from ir_invoice_supp_h where doc_number = '$doc_number'");
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
      <th style="text-align:left;width: 25%;padding-top: -5px;">DOCUMENT DATE :</th> 
      <th style="text-align:left;width: 25%;padding-top: -5px;">DOCUMENT TYPE :</th>                              
    </tr>

	<tbody>
	<tr>  	      
	<td style="text-align:left;padding-left: 25px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select created_date from ir_invoice_supp_h where doc_number = '$doc_number'");
      $rows2 = mysqli_fetch_array($sql2);
      	$create_date = $rows2['created_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:left;padding-left: 15px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select tgl_penerimaan from ir_invoice_supp_h where doc_number = '$doc_number'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tglftrcbd = $rows3['tgl_penerimaan'];
		echo date("d M Y", strtotime($tglftrcbd));
		?>		
	</td>	
	<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">RECEIVED INVOICE</td>								      
	</tr>
</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:12px;border-spacing:2px;">
  <tr>
      <th style="width:13%;border: 1px solid black;text-align:center;">No Reff</th>
      <th style="width:13%;border: 1px solid black;text-align:center;">No Invoice</th>
      <th style="width:12%;border: 1px solid black;text-align:center;">Invoice Date</th>     
      <th style="width:12%;border: 1px solid black;text-align:center;">Amount</th>

<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody>
<?php
$sum_total = 0;
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
	$no_invoice = $data['no_invoice'];
	$tgl_invoice = $data['tgl_invoice'];
	$amount = $data['amount'];
	$sum_total += $data['amount'];;
   echo '<tr >
      <td style="width:30%;text-align:center;">'.$data['no_reff'].'</td>
      <td style="width:30%;text-align:center;">'.$no_invoice.'</td>
	  <td style="width:20%;text-align:center;">'.date("d M Y",strtotime($tgl_invoice)).'</td>
	  <td style="width:10%;text-align:right;border-left:none">'.number_format($amount, 2).'</td>		  		  	  
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