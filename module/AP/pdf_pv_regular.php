<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$images = '../../images/img-01.png';
$no_kbon=$_GET['nokontrabon'];
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php


$sql= "select bpb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bpb_new.no_bpb, bpb_new.tgl_bpb, bpb_new.pono, bpb_new.tgl_po, bpb_new.ws, bpb_new.supplier, bpb_new.itemdesc, bpb_new.qty, bpb_new.price, bpb_new.qty * bpb_new.price as subtotal, (bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100) as tax, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bpb_new.curr, bpb_new.uom, kontrabon.pph_value, kontrabon.dp_value from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb  where kontrabon.no_kbon = '$no_kbon' and bpb_new.status != 'Cancel'
union
select bppb_new.id, kontrabon.no_kbon, kontrabon.tgl_kbon, bppb_new.no_bppb as no_bpb, bppb_new.tgl_bppb as tgl_bpb, bppb_new.no_po, bppb_new.tgl_po, '' as ws, bppb_new.supplier, bppb_new.itemdesc, (- bppb_new.qty) as qty, bppb_new.price, (- bppb_new.qty * bppb_new.price) as subtotal, (- (bppb_new.qty * bppb_new.price) * (bppb_new.tax / 100)) as tax1, kontrabon.tgl_tempo, kontrabon.supp_inv, kontrabon.no_faktur, kontrabon.create_date, bppb_new.curr, bppb_new.uom, kontrabon.pph_value, kontrabon.dp_value from bppb_new inner join return_kb on return_kb.no_bpbrtn = bppb_new.no_bppb inner join kontrabon on kontrabon.no_kbon = return_kb.no_kbon where kontrabon.no_kbon = '$no_kbon' and bppb_new.status != 'Cancel' GROUP BY bppb_new.id order by no_bpb asc,id asc";


$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));

$sqll = mysqli_query($conn2,"select jml_return, jml_potong, potongan_ppn, potongan_pph from potongan where no_kbon = '$no_kbon' group by no_kbon");
$rowl = mysqli_fetch_assoc($sqll);
$potongan_ppn = isset($rowl['potongan_ppn']) ? $rowl['potongan_ppn'] : 0;
$potongan_pph = isset($rowl['potongan_pph']) ? $rowl['potongan_pph'] : 0;

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

	
	<title>PAYMENT VOUCHER REGULAR</title>
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
			<td ><h5>PAYMENT VOUCHER : <?php echo $no_kbon ?></h5></td>
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
			<!--    <th style="text-align:center;width: 5%;padding-top: -5px;">#WS :</th> -->
			<td style="text-align:left;width: 18%;padding-bottom: 10px;"><b>DATE CREATED  :</b></td>
			<td style="text-align:left;width: 28%;padding-bottom: 10px;"><b>PAYMENT VOUCHER DATE :</b></td>
			<td style="text-align:left;width: 15%;padding-bottom: 10px;"><b>DUE DATE :</b></td>
			<td style="text-align:left;width: 25%;padding-bottom: 10px;"><b>FROM ACCOUNT :</b></td>
			<td style="text-align:left;width: 14%;padding-bottom: 10px;"><b>TYPE :</b></td>
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
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql1 = mysqli_query($conn2,"select create_date from kontrabon_h where no_kbon = '$no_kbon'");
		$rows = mysqli_fetch_array($sql1);
		$create_date = $rows['create_date'];
		echo date("d M Y", strtotime($create_date));
		?>		
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql3 = mysqli_query($conn2,"select tgl_kbon from kontrabon_h where no_kbon = '$no_kbon'");
		$rows = mysqli_fetch_array($sql3);
		$tgl_kbon = $rows['tgl_kbon'];
		echo date("d M Y", strtotime($tgl_kbon));
		?>		
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql2 = mysqli_query($conn2,"select tgl_tempo from kontrabon_h where no_kbon = '$no_kbon'");
		$rows1 = mysqli_fetch_array($sql2);
		$tgl_tempo = $rows1['tgl_tempo'];
		echo date("d M Y", strtotime($tgl_tempo));
		?>
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select no_kbon, IFNULL(coa_name,'-') bank_account from kontrabon_h a left join b_masterbank b on b.bank_account = a.from_account where no_kbon = '$no_kbon'");
      $rows2 = mysqli_fetch_array($sql3);
      	$bank_account = $rows2['bank_account'];
		echo $bank_account;
		?>		
	</td>	
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		REGULAR
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

$sql_bank = mysqli_query($conn2,"select msb.id, msb.beneficiary_name, msb.bank_name, msb.bank_account, msb.bank_currency from kontrabon_h kh LEFT JOIN master_supplier_bank msb on msb.id = kh.id_bank_account where kh.no_kbon = '$no_kbon'");
$row_bank = mysqli_fetch_array($sql_bank);
$beneficiary_name = $row_bank['beneficiary_name'];
$bank_name = $row_bank['bank_name'];
$bank_account = $row_bank['bank_account'];
$bank_currency = $row_bank['bank_currency'];
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
  		$uom = $data['uom'];
  		$curr = $data['curr'];
  		$price = $data['price'];
  		$subtotal = $data['subtotal'];
  		$tgl_tempo = $data['tgl_tempo'];
  		$supp_inv = $data['supp_inv'];
  		$no_faktur = $data['no_faktur'];
  		$ws = $data['ws'];
  		$ppn += $data['tax'];
  		$pph = $rowl12['pph'];
  		$querys = mysqli_query($conn2,"select sum(total_ftr) total_ftr from kontrabon_ftr where no_kbon = '$no_kbon' group by no_kbon");
  		$rows = mysqli_fetch_array($querys);
  		$dp = $rows['total_ftr'];
  		$jml_return = $rowl['jml_return'];
  		$jml_potong = $rowl['jml_potong'];
  		$h_ppn += $ppn;
  		$potong = $jml_potong;
  		$sum_qty += $qty;
  		$sum_sub += $subtotal;
  		$sum_price += $price;
  		$sum_total = $sum_sub + $ppn  - $pph - $dp + $potong;
  		echo '<tr>
  		<td style="width:16%;text-align:left;vertical-align:top;">'.$no_po.'</td>
  		<td style="width:9%;text-align:left;vertical-align:top;">'.$tgl_po.'</td>
  		<td style="width:16%;text-align:left;vertical-align:top;">'.$no_bpb.'</td>
  		<td style="width:9%;text-align:left;vertical-align:top;">'.date("d M Y",strtotime($tgl_bpb)).'</td>
  		<td style="width:21%;text-align:left;vertical-align:top;">'.$item_desc.'</td>
  		<td style="width:8%;text-align:left;vertical-align:top;">'.number_format($qty, 2).' '.$uom.'</td> 
  		<td style="width:11%;text-align:right;vertical-align:top;">'.number_format($price, 2).'</td>
  		<td style="width:10%;text-align:right;vertical-align:top;">'.number_format($subtotal, 2).'</td>
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
	<table width="100%" border="0" style="page-break-inside: avoid;font-size:11px;">

		<tr>
			<td width="20%">
				Payment To
			</td>
			<td width="48%"></td>

			<td>
				SubTotal
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right">
				<?php echo $curr." ".number_format($sum_sub, 2); ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Beneficiary Name
			</td>
			<td width="48%">: <?php echo $beneficiary_name; ?></td>

			<td>
				Adjustment
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right;">

				<?php if($potong >= 1) {
					echo $curr." ".number_format($potong, 2)."";
				}else{
					echo $curr." ( - ".number_format(abs($potong), 2)." )";
				} ?>
			</td>
		</tr>


		<tr>
			<td width="20%">
				Bank Account Number
			</td>
			<td width="48%">: <?php echo $bank_account; ?></td>

			<td>
				Total
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right">
				<?php echo $curr." ".number_format($sum_sub + $potong, 2); ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Name Of The Bank
			</td>
			<td width="48%">: <?php echo $bank_name; ?></td>

			<td>
				Ppn
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right">
				<?php
				$sqltax = mysqli_query($conn2,"select tax from kontrabon_h where no_kbon = '$no_kbon'");
				$rowstax = mysqli_fetch_array($sqltax);
				$jml_tax = $rowstax['tax'];
				echo $curr." ".number_format($ppn - $potongan_ppn, 2).""; ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Bank Account Currency
			</td>
			<td width="48%">: <?php echo $bank_currency; ?></td>

			<td>
				Pph
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right;">
				<?php echo $curr." ( - ".number_format($pph - $potongan_pph, 2)." )"; ?>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="48%"></td>

			<td>
				CBD or DP
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right">
				<?php echo $curr." ( - ".number_format($dp, 2)." )"; ?>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="48%"></td>

			<td style="font-weight: bold;">
				Grand Total
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



<!--TTD_SPACER-->

<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;" cellpadding="0" cellspacing="0" border="1" width='500';>

		<tr>	
			<th style="font-size:12px">Created By : </th>
			<th style="font-size:12px">Checked By : </th>
			<th colspan="2" style="font-size:12px">Approved By : </th>

		</tr>
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>	
			<td class="td1">&nbsp;</td>						
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>				
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>	
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>	
		</tr>   
		<tr>	
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>	

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

	<td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;"><?php
		$sql1 = mysqli_query($conn2,"select approve_by from ttd");
		$rows = mysqli_fetch_array($sql1);
		$approve_by = $rows['approve_by'];
		echo $approve_by;
		?>
	</td>
</tr>

<tr>    
	<td style="font-size:12px;text-align:center;">AP Staff</td>
	<td style="font-size:12px;text-align:center">Supervisor</td>
	<td style="font-size:12px;text-align:center">Finance Manager</td>
	<td style="font-size:12px;text-align:center">Director</td>
</tr> 				

</table>
<br />

<table style="page-break-inside: avoid; font-size:11px;" border="0">
	<tr>
		<td style="font-weight: bold">NOTE :</td>
	</tr>
	<tr>
		<td>Payment Voucher Number : <?php echo $no_kbon ?></td>
	</tr>
	<tr>
		<td>Total Payment Voucher : <?php echo $curr." ".number_format($jml_total, 2) ?></td>
	</tr>
</table>
</div>

</body>


</html>  

</html>  

<?php
$html = ob_get_clean();
$html_parts = explode('<!--TTD_SPACER-->', $html, 2);
$html_before_ttd = $html_parts[0];
$html_ttd = isset($html_parts[1]) ? $html_parts[1] : '';

require_once __DIR__ . '/../../mpdf8/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/../../mpdf8/tmp'
]);

$mpdf->WriteHTML($html_before_ttd);

// Jarak sebelum blok TTD dibuat otomatis berdasarkan sisa tinggi halaman saat ini:
// kalau sisa halaman masih banyak (detail BPB sedikit), jaraknya dibuat nyaman;
// kalau sisa halaman sudah sempit (detail BPB banyak), jaraknya dikecilkan supaya
// blok TTD tetap punya kesempatan muat di halaman yang sama. Blok TTD sendiri masih
// punya page-break-inside:avoid, jadi kalau memang tidak cukup ruang dia otomatis
// pindah utuh ke halaman baru, tidak terpotong.
$sisaHalaman = $mpdf->h - $mpdf->bMargin - $mpdf->y;
$estimasiTinggiTtd = 75; // mm, perkiraan tinggi blok TTD + catatan
$jarakTtd = max(5, min(20, $sisaHalaman - $estimasiTinggiTtd));

$mpdf->WriteHTML('<div style="height:' . $jarakTtd . 'mm;"></div>');
$mpdf->WriteHTML($html_ttd);
$mpdf->Output();
exit;
?>
