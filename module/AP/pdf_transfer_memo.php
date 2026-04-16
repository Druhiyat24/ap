<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$doc_number=$_GET['doc_number'];
?>

<?php
$sql= "select b.nm_memo, c.tgl_memo, ms.supplier, jns_trans, jns_pengiriman, mb.supplier buyer, upper(IFNULL(b.keterangan,a.keterangan)) keterangan from transfer_memo_exim_h a INNER JOIN transfer_memo_exim_det b on b.no_trans = a.no_trans INNER JOIN memo_h c on c.nm_memo = b.nm_memo INNER JOIN mastersupplier ms on ms.id_supplier = c.id_supplier INNER JOIN mastersupplier mb on mb.id_supplier = c.id_buyer where a.no_trans = '$doc_number' GROUP BY b.nm_memo";

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

	
  <title>Transfer Memo</title>
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
      DOCUMENT HANDOVER
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
      <th style="text-align:left;width: 25%;padding-top: -5px;">TRANSFER TYPE :</th>                               
    </tr>

	<tbody>
	<tr>  	      
	<td style="text-align:left;padding-left: 25px;padding-top: -10px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select DISTINCT DATE_FORMAT(created_at,'%Y-%m-%d') created_date from transfer_memo_exim_h where no_trans = '$doc_number'");
      $rows2 = mysqli_fetch_array($sql2);
      	$create_date = $rows2['created_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select DISTINCT tgl_trans from transfer_memo_exim_h where no_trans = '$doc_number'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tglftrcbd = $rows3['tgl_trans'];
		echo date("d M Y", strtotime($tglftrcbd));
		?>		
	</td>


	<td style="text-align:left;padding-left: 15px;padding-top: -10px;padding-bottom: -10px;"><?php
			if (strpos($doc_number, 'TETM') !== false) {
				echo 'TRANSFER MEMO EXIM TO MARKETING';
			}elseif (strpos($doc_number, 'TETF') !== false) {
				echo 'TRANSFER MEMO EXIM TO FINANCE';
			}else{
				echo 'TRANSFER MEMO MARKETING TO EXIM';
			}
		 ?></td>								      
	</tr>
</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:11px;border-spacing:2px;">
  <tr>
      <th style="width:20%;border: 1px solid black;text-align:center;">No Memo</th>
      <th style="width:12%;border: 1px solid black;text-align:center;">Tgl Memo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Supplier</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Jenis Transaksi</th> 
      <th style="width:15%;border: 1px solid black;text-align:center;">Jenis Pengiriman</th> 
      <th style="width:15%;border: 1px solid black;text-align:center;">Buyer</th>     
      <th style="width:15%;border: 1px solid black;text-align:center;">Description</th>
    </tr>
<tbody>
<?php
$sum_total = 0;
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
   echo '<tr >
      <td style="text-align:center;">'.$data['nm_memo'].'</td>
	  <td style="text-align:center;">'.date("d M Y",strtotime($data['tgl_memo'])).'</td>
	  <td style="text-align:center;">'.$data['supplier'].'</td>
	  <td style="text-align:center;">'.$data['jns_trans'].'</td>
	  <td style="text-align:center;">'.$data['jns_pengiriman'].'</td>
	  <td style="text-align:center;">'.$data['buyer'].'</td>
	  <td style="text-align:center;">'.$data['keterangan'].'</td>
    </tr>';	
};	
?>


  </tbody>
</table> 
<br>


<br/>
<br/>
<br/>
<br/>
<br/>


	<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;width: 70%" cellpadding="0" cellspacing="0" border="1" width='70%';>

		<tr>	
			<th style="font-size:12px;width: 50%;">Send By : </th>
			<th style="font-size:12px;width: 50%;">Accept By : </th>
	
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

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/../../mpdf8/tmp'
]);

$mpdf->WriteHTML($html);
$mpdf->Output();
exit;
?>