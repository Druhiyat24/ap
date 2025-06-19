<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$doc_number=$_GET['doc_number'];
?>

<?php
$sql= "select a.* from maintain_bpb_det a INNER JOIN maintain_bpb_h b on b.no_maintain = a.no_maintain where b.id = '$doc_number' order by no_bpb asc";

$rs=mysqli_fetch_array(mysql_query($conn2,$sql));

$sql_header = mysqli_query($conn2,"select DISTINCT no_maintain, tgl_maintain, DATE_FORMAT(created_date,'%Y-%m-%d') created_date, upper(keterangan) keterangan from maintain_bpb_h where id = '$doc_number'");
$rows_header = mysqli_fetch_array($sql_header);
$keterangan_h = $rows_header['keterangan'];
$no_maintain = $rows_header['no_maintain'];
$tglftrcbd = $rows_header['tgl_maintain'];
$create_date = $rows_header['created_date'];
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

	
	<title>Maintain</title>
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
			<td ><h4><?php
			echo $no_maintain;
		?></h4></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="right"><h4>
			MAINTAIN DOCUMENT
		</h4>
	</td>
</tr>
</table>
<hr />
<table style="font-size:12px;">
	<thead>
		<tr>
			<th style="text-align:left;width: 25%;padding-top: -5px;">Document Date :</th> 
			<th style="text-align:left;width: 25%;padding-top: -5px;">Date Created :</th>
			<th style="text-align:left;width: 25%;padding-top: -5px;">Maintain Type :</th>                               
		</tr>

		<tbody>
			<tr>  	      
				<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">
					<?php
					echo strtoupper(date("d M Y", strtotime($tglftrcbd)));
					?>		
				</td>
				<td style="text-align:left;padding-left: 25px;padding-top: -10px;padding-bottom: -10px;">
					<?php
					echo strtoupper(date("d M Y", strtotime($create_date)));
					?>		
				</td>
				<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">MAINTAIN BPB</td>								      
			</tr>
		</tbody>
	</table>
	<hr />

	<table style="font-size:12px;">
		<thead>
			<tr>
				<th colspan="3" style="text-align:left;width: 75%;padding-top: -5px;">Descriptions :</th>                     
			</tr>

			<tbody>
				<tr>  	      
					<td colspan="3" style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">
						<?php
						echo $keterangan_h;
						?>		
					</td>								      
				</tr>
			</tbody>
		</table>
		<hr />

		<table  border="1" cellspacing="0" style="width:100%;font-size:11px;border-spacing:2px;">
			<tr>
				<th style="width:15%;border: 1px solid black;text-align:center;">Supplier</th>
				<th style="width:20%;border: 1px solid black;text-align:center;">No BPB</th>
				<th style="width:12%;border: 1px solid black;text-align:center;">BPB Date</th>
				<th style="width:15%;border: 1px solid black;text-align:center;">No PO</th>     
				<th style="width:15%;border: 1px solid black;text-align:center;">Description</th>

			</tr>
			<tbody>
				<?php
				$sum_total = 0;
				$query = mysqli_query($conn2,$sql)or die(mysqli_error());
				while($data=mysqli_fetch_array($query)){
					$no_bpb = $data['no_bpb'];
					$tgl_bpb = $data['tgl_bpb'];
					$nama_supp = $data['nama_supp'];
					$no_po = $data['no_po'];	
					$curr = $data['curr'];
					$total = $data['total'];
					$status = $data['status'];
					$ket = $data['ket'];
					$keterangan = $data['keterangan'];
					$sum_total += $data['total'];;
					echo '<tr >
					<td style="text-align:left;">'.$nama_supp.'</td>	  		  	  
					<td style="text-align:left;">'.$no_bpb.'</td>
					<td style="text-align:center;">'.date("d M Y",strtotime($tgl_bpb)).'</td>
					<td style="text-align:left;">'.$no_po.'</td>
					<td style="text-align:left;">'.$keterangan.'</td>
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
<!-- 
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
</table> -->


<br/>
<br/>
<br/>
<br/>
<br/>


<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;width: 70%" cellpadding="0" cellspacing="0" border="1" width='70%';>

		<tr>	
			<th style="font-size:12px;width: 50%;">Created By : </th>
			<th style="font-size:12px;width: 50%;">Approved By : </th>

		</tr>
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
		</tr>   
		<tr>	
			<td style="font-size:12px;text-align:center;"><b>&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
			<td style="font-size:12px;text-align:center"><b>&nbsp;&nbsp;&nbsp;&nbsp;</b></td>


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