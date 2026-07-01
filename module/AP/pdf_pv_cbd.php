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

$sql= "select no_kbon, no_cbd, no_po, tgl_po, subtotal, tax, pph_value, total, curr from kontrabon_cbd where no_kbon = '$no_kbon' and status != 'Cancel' order by no_cbd asc";

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


	<title>PAYMENT VOUCHER CBD</title>
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
				$sql1 = mysqli_query($conn2,"select nama_supp from kontrabon_h_cbd where no_kbon = '$no_kbon'");
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
			<td style="text-align:left;width: 18%;padding-bottom: 10px;"><b>DATE CREATED  :</b></td>
			<td style="text-align:left;width: 28%;padding-bottom: 10px;"><b>PAYMENT VOUCHER DATE :</b></td>
			<td style="text-align:left;width: 15%;padding-bottom: 10px;"><b>DUE DATE :</b></td>
			<td style="text-align:left;width: 25%;padding-bottom: 10px;"><b>FROM ACCOUNT :</b></td>
			<td style="text-align:left;width: 14%;padding-bottom: 10px;"><b>TYPE :</b></td>
      </tr>

      <tbody>
      	<tr>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql1 = mysqli_query($conn2,"select create_date from kontrabon_h_cbd where no_kbon = '$no_kbon'");
		$rows = mysqli_fetch_array($sql1);
		$create_date = $rows['create_date'];
		echo date("d M Y", strtotime($create_date));
		?>
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql3 = mysqli_query($conn2,"select tgl_kbon from kontrabon_h_cbd where no_kbon = '$no_kbon'");
		$rows = mysqli_fetch_array($sql3);
		$tgl_kbon = $rows['tgl_kbon'];
		echo date("d M Y", strtotime($tgl_kbon));
		?>
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		<?php
		$sql2 = mysqli_query($conn2,"select tgl_tempo from kontrabon_h_cbd where no_kbon = '$no_kbon'");
		$rows1 = mysqli_fetch_array($sql2);
		$tgl_tempo = $rows1['tgl_tempo'];
		echo date("d M Y", strtotime($tgl_tempo));
		?>
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select no_kbon, IFNULL(coa_name,'-') bank_account from kontrabon_h_cbd a left join b_masterbank b on b.bank_account = a.from_account where no_kbon = '$no_kbon'");
      $rows2 = mysqli_fetch_array($sql3);
      	$bank_account = $rows2['bank_account'];
		echo $bank_account;
		?>		
	</td>
	<td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
		CBD
	</td>
</tr>



</tbody>
</table>
<hr />

<?php
$sql_curr = mysqli_query($conn2,"select curr from kontrabon_h_cbd where no_kbon = '$no_kbon'");
$row_curr = mysqli_fetch_array($sql_curr);
$curr1 = $row_curr['curr'];

$sql_bank = mysqli_query($conn2,"select msb.id, msb.beneficiary_name, msb.bank_name, msb.bank_account, msb.bank_currency from kontrabon_h_cbd kh LEFT JOIN master_supplier_bank msb on msb.id = kh.id_bank_account where kh.no_kbon = '$no_kbon'");
$row_bank = mysqli_fetch_array($sql_bank);
$beneficiary_name = $row_bank['beneficiary_name'];
$bank_name = $row_bank['bank_name'];
$bank_account = $row_bank['bank_account'];
$bank_currency = $row_bank['bank_currency'];
?>
<table  border="1" cellspacing="0" style="width:100%;font-size:10px;border-spacing:2px;">
	<tr>

		<th style="width: 20%;border: 1px solid black;text-align:center;">No FTR CBD</th>
		<th style="width: 20%;border: 1px solid black;text-align:center;">No PO</th>
		<th style="width: 15%;border: 1px solid black;text-align:center;">PO Date</th>
		<th style="width: 15%;border: 1px solid black;text-align:center;">SubTotal (<?php echo $curr1 ?>)</th>
		<th style="width: 15%;border: 1px solid black;text-align:center;">Tax PPn (<?php echo $curr1 ?>)</th>
		<th style="width: 15%;border: 1px solid black;text-align:center;">Total (<?php echo $curr1 ?>)</th>
  </tr>
  <tbody >
  	<?php
  	$query = mysqli_query($conn2,$sql)or die(mysqli_error());
  	$no_cbd = "";
  	$no_po = "";
  	$tgl_po = "";
  	$curr = "";
  	$subtotal = 0;
  	$tax = 0;
  	$pph = 0;
  	$total = 0;
  	$sum_sub = 0;
  	$sum_tax = 0;
  	$sum_pph = 0;
  	$sum_total = 0;
  	while($data=mysqli_fetch_array($query)){
  		$no_cbd = $data['no_cbd'];
  		$no_po = $data['no_po'];
  		if ($no_po == '') {
  			$no_po = '-';
  		}
  		$tgl_po = $data['tgl_po'];
  		if ($tgl_po == '' || $tgl_po == '1970-01-01' || $tgl_po == '0000-00-00') {
  			$tgl_po = '-';
  		}else{
  			$tgl_po = date("d M Y",strtotime($tgl_po));
  		}
  		$curr = $data['curr'];
  		$subtotal = $data['subtotal'];
  		$tax = $data['tax'];
  		$pph = $data['pph_value'];
  		$total = $data['total'];
  		$sum_sub += $subtotal;
  		$sum_tax += $tax;
  		$sum_pph += $pph;
  		$sum_total += $total;
  		echo '<tr>
  		<td style="width:20%;text-align:left;vertical-align:top;">'.$no_cbd.'</td>
  		<td style="width:20%;text-align:left;vertical-align:top;">'.$no_po.'</td>
  		<td style="width:15%;text-align:left;vertical-align:top;">'.$tgl_po.'</td>
  		<td style="width:15%;text-align:right;">'.number_format($subtotal, 2).'</td>
  		<td style="width:15%;text-align:right;">'.number_format($tax, 2).'</td>
  		<td style="width:15%;text-align:right;">'.number_format($total, 2).'</td>
  		</tr>';
  	};
  	?>

<tr>
	<td colspan="3" style="width:55%;border: 1px solid black;text-align:center;font-size:10px"><b>Jumlah</b></td>
	<td style="width:15%;text-align:right;"><?php echo $curr.' '.number_format($sum_sub, 2) ?></td>
	<td style="width:15%;text-align:right;"><?php echo $curr.' '.number_format($sum_tax, 2) ?></td>
	<td style="width:15%;text-align:right;"><?php echo $curr.' '.number_format($sum_total, 2) ?></td>
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
				Tax (PPn)
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right;">
				<?php echo $curr." ".number_format($sum_tax, 2); ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Bank Account Number
			</td>
			<td width="48%">: <?php echo $bank_account; ?></td>

			<td>
				Pph
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right;">
				<?php echo $curr." ( - ".number_format($sum_pph, 2)." )"; ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Name Of The Bank
			</td>
			<td width="48%">: <?php echo $bank_name; ?></td>

			<td style="font-weight: bold;">
				Grand Total
			</td>
			<td style="width:1%">:</td>
			<td style="text-align:right;font-weight: bold;">
				<?php
				$sqltotal = mysqli_query($conn2,"select total from kontrabon_h_cbd where no_kbon = '$no_kbon'");
				$rowstotal = mysqli_fetch_array($sqltotal);
				$jml_total = $rowstotal['total'];
				echo $curr." ".number_format($jml_total, 2).""; ?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				Bank Account Currency
			</td>
			<td width="48%">: <?php echo $bank_currency; ?></td>

			<td></td>
			<td style="width:1%"></td>
			<td style="text-align:right;"></td>
		</tr>

	</table>
</div>



<!--TTD_SPACER-->

<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;" cellpadding="0" cellspacing="0" border="1" width='620';>

		<tr>
			<th style="font-size:12px">Created By : </th>
			<th style="font-size:12px">Checked By : </th>
			<th colspan="3" style="font-size:12px">Approved By : </th>

		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
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
			<td class="td1">&nbsp;</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
		</tr>
		<tr>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp; </td>
			<td class="td1">&nbsp;</td>
			<td class="td1">&nbsp;</td>

		</tr>
		<tr>
			<td style="font-size:12px;text-align:center; width: 120px;">
				<?php
				$sql1 = mysqli_query($conn2,"select create_user from kontrabon_h_cbd where no_kbon = '$no_kbon'");
				$rows = mysqli_fetch_array($sql1);
				$create_by = $rows['create_user'];
				echo $create_by;
				?>
			</td>
			<td style="font-size:12px;text-align:center; width: 120px;"><?php
			$sql1 = mysqli_query($conn2,"select confirm1 from ttd");
			$rows = mysqli_fetch_array($sql1);
			$confirm1 = $rows['confirm1'];
			echo $confirm1;
			?>
		</td>
		<td style="font-size:12px;text-align:center; width: 120px;"><?php
		$sql1 = mysqli_query($conn2,"select confirm2 from ttd");
		$rows = mysqli_fetch_array($sql1);
		$confirm2 = $rows['confirm2'];
		echo $confirm2;
		?>
	</td>
	<td style="font-size:12px;text-align:center; width: 120px;">Herman</td>

	<td style="font-size:12px;text-align:center; width: 120px;"><?php
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
	<td style="font-size:12px;text-align:center">Kadept Fin&amp;Acc</td>
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

$sisaHalaman = $mpdf->h - $mpdf->bMargin - $mpdf->y;
$estimasiTinggiTtd = 75; // mm, perkiraan tinggi blok TTD + catatan
$jarakTtd = max(5, min(20, $sisaHalaman - $estimasiTinggiTtd));

$mpdf->WriteHTML('<div style="height:' . $jarakTtd . 'mm;"></div>');
$mpdf->WriteHTML($html_ttd);
$mpdf->Output();
exit;
?>
