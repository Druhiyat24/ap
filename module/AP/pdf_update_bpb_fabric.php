<!DOCTYPE html>
<?php
include '../../conn/conn.php';

$no_pengajuan = isset($_GET['no_pengajuan']) ? $_GET['no_pengajuan'] : '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

$h = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, nama_supp, deskripsi, status, created_by, created_at
    FROM update_bpb_fabric_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
$header = mysqli_fetch_assoc($h);

if (!$header) {
    die('Request data not found');
}

$items = [];
$sql = mysqli_query($conn1, "SELECT no_bpb, tgl_bpb, no_ws, id_item, desc_item, qty, unit, curr, price_old, price_new, ppn_old, ppn_new
    FROM update_bpb_fabric
    WHERE no_pengajuan = '$no_pengajuan_esc'
    ORDER BY no_bpb, no_ws, id_item");
while ($row = mysqli_fetch_assoc($sql)) {
    $items[] = $row;
}

function formatNum($value, $decimals = 2) {
    $formatted = number_format((float) $value, $decimals);
    if (strpos($formatted, '.') !== false) {
        $formatted = rtrim(rtrim($formatted, '0'), '.');
    }
    return $formatted;
}

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

  <title>Edit Request - BPB Fabric</title>
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
	<td><h4><?php echo htmlspecialchars($header['no_pengajuan']) ?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      EDIT REQUEST - BPB FABRIC
	</h4>
	</td>
</tr>
</table>
<hr />
<table style="font-size:11px;">
	<tr>
      <th style="text-align:left;width: 33%;padding:3px;">DOCUMENT DATE :</th>
      <th style="text-align:left;width: 33%;padding:3px;">CREATED BY :</th>
      <th style="text-align:left;width: 34%;padding:3px;">DATE CREATED :</th>
    </tr>
	<tr>
	<td style="text-align:left;padding:3px;">
      <?php echo !empty($header['tgl_pengajuan']) ? date("d M Y", strtotime($header['tgl_pengajuan'])) : '-'; ?>
	</td>
	<td style="text-align:left;padding:3px;">
      <?php echo htmlspecialchars($header['created_by'] ?: '-'); ?>
	</td>
	<td style="text-align:left;padding:3px;">
      <?php echo !empty($header['created_at']) ? date("d M Y H:i", strtotime($header['created_at'])) : '-'; ?>
	</td>
	</tr>
	<tr>
		<td colspan="5" style="text-align:left;padding:6px 3px 0 3px;"><b>DESCRIPTION :</b></td>
	</tr>
	<tr>
		<td colspan="5" style="text-align:left;padding:0 3px 3px 3px;"><?php echo htmlspecialchars($header['deskripsi'] ?: '-'); ?></td>
	</tr>
</table>
<hr />

<table border="1" cellspacing="0" style="width:100%;font-size:11px;border-spacing:2px;">
  <tr>
      <th rowspan="2" style="width:8%;border: 1px solid black;text-align:center;padding:2px;">No BPB</th>
      <th rowspan="2" style="width:7%;border: 1px solid black;text-align:center;padding:2px;">BPB Date</th>
      <th rowspan="2" style="width:8%;border: 1px solid black;text-align:center;padding:2px;">No WS</th>
      <th rowspan="2" style="width:20%;border: 1px solid black;text-align:center;padding:2px;">Item</th>
      <th rowspan="2" style="width:7%;border: 1px solid black;text-align:center;padding:2px;">Qty</th>
      <th rowspan="2" style="width:6%;border: 1px solid black;text-align:center;padding:2px;">Unit</th>
      <th rowspan="2" style="width:5%;border: 1px solid black;text-align:center;padding:2px;">Curr</th>
      <th colspan="2" style="width:20%;border: 1px solid black;text-align:center;padding:2px;">Price</th>
      <th colspan="2" style="width:18%;border: 1px solid black;text-align:center;padding:2px;">PPN %</th>
    </tr>
    <tr>
      <th style="width:10%;border: 1px solid black;text-align:center;padding:2px;">Current</th>
      <th style="width:10%;border: 1px solid black;text-align:center;padding:2px;">Updated</th>
      <th style="width:9%;border: 1px solid black;text-align:center;padding:2px;">Current</th>
      <th style="width:9%;border: 1px solid black;text-align:center;padding:2px;">Updated</th>
    </tr>
<tbody>
<?php
if (empty($items)) {
    echo '<tr><td colspan="11" style="text-align:center;border: 1px solid black;padding:2px;">No items found</td></tr>';
}
foreach ($items as $it) {
    $tgl_bpb   = !empty($it['tgl_bpb']) ? date("d M Y", strtotime($it['tgl_bpb'])) : '-';
    $desc_item = !empty($it['desc_item']) ? $it['desc_item'] : $it['id_item'];

    echo '<tr>'
        . '<td style="text-align:center;border: 1px solid black;padding:2px;">' . htmlspecialchars($it['no_bpb']) . '</td>'
        . '<td style="text-align:center;border: 1px solid black;padding:2px;">' . $tgl_bpb . '</td>'
        . '<td style="text-align:center;border: 1px solid black;padding:2px;">' . htmlspecialchars($it['no_ws'] ?: '-') . '</td>'
        . '<td style="text-align:left;border: 1px solid black;padding:2px;">' . htmlspecialchars($desc_item) . '</td>'
        . '<td style="text-align:right;border: 1px solid black;padding:2px;">' . formatNum($it['qty'], 2) . '</td>'
        . '<td style="text-align:center;border: 1px solid black;padding:2px;">' . htmlspecialchars($it['unit'] ?: '-') . '</td>'
        . '<td style="text-align:center;border: 1px solid black;padding:2px;">' . htmlspecialchars($it['curr'] ?: '-') . '</td>'
        . '<td style="text-align:right;border: 1px solid black;padding:2px;">' . formatNum($it['price_old'], 4) . '</td>'
        . '<td style="text-align:right;border: 1px solid black;padding:2px;">' . formatNum($it['price_new'], 4) . '</td>'
        . '<td style="text-align:right;border: 1px solid black;padding:2px;">' . formatNum($it['ppn_old'], 2) . '</td>'
        . '<td style="text-align:right;border: 1px solid black;padding:2px;">' . formatNum($it['ppn_new'], 2) . '</td>'
        . '</tr>';
}
?>
  </tbody>
</table>

<br/>
<br/>
<br/>
<br/>
<br/>

	<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
	<table style="page-break-inside: avoid;width: 70%" cellpadding="0" cellspacing="0" border="1" width='70%';>

		<tr>
			<th style="font-size:12px;width: 50%;">Prepared By : </th>
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
