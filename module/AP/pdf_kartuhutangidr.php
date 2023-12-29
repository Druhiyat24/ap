<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_bpb=$_GET['no_bpb'];
?>

<?php
$sql= "select * from detail where no_bpb = '$no_bpb' and cek >= 1 order by cek asc";

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
      <td style="width:30%;text-align:center;border: 1px solid black;"><img src="../../images/img-01.png" style="height:57px; width:80px;"></td>
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
	<td ><h4><?php echo $no_bpb?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from detail where no_bpb = '$no_bpb'");
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
    <tr style="margin-bottom: 50px">
      <th style="text-align:center;width: 20%;padding-top: -5px;">TGL BPB  :</th> 
      <td style="text-align:left;padding-left: 25px;padding-top: -20px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select tgl_bpb from detail where no_bpb = '$no_bpb'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tgl_kbon = $rows3['tgl_bpb'];
		echo date("d M Y", strtotime($tgl_kbon));
		?>		
	</td>                              
    </tr>
</tbody>
</table>
<hr />

<!-- TABEL MEMUNCULKAN UNTUK CURRENCY IDR -->

<table  border="1" cellspacing="0" style="width:100%;font-size:25px;border-spacing:2px;">
  <tr>
      <tr >
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No KB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Tax (Pph)</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Pay</th>
            <th  colspan="3" style="text-align: center;vertical-align: middle;">Equivalent IDR</th> 
            </tr>                                           
         <tr >
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Balance</th>                                   
                                          
        </tr>
<tbody>
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
	$no_kbon = $data['no_kbon'];
	$no_bpb = $data['no_bpb'];
	$no_payment = $data['no_payment'];
	$pph = $data['pph'];
	$credit_idr = $data['credit_idr'];
	$debit_idr = $data['debit_idr'];
	$balance_idr = $data['balance_idr'];
	$tgl_kbon = $data['tgl_kbon'];	

   echo '<tr>
	  <td style="width:30%;text-align:center;">'.$no_bpb.'</td>
      <td style="width:30%;text-align:center;">'.$no_kbon.'</td>
	  <td style="width:10%;text-align:right;">'.number_format($pph, 2).'</td> 
	  <td style="width:30%;text-align:center;">'.$no_payment.'</td>
	  <td style="width:10%;text-align:right;">'.number_format($credit_idr, 2).'</td> 
	  <td style="width:10%;text-align:right;">'.number_format($debit_idr, 2).'</td>
	  <td style="width:10%;text-align:right;">'.number_format($balance_idr, 2).'</td>		  		  	  
    </tr>';	
};	
?>

<?php
$sql1= "select no_bpb, MAX(pph) as pph, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr from detail where no_bpb = '$no_bpb'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
while($datas=mysqli_fetch_array($querys)){
	 $t_bpb = $datas['credit_idr'];
    $tax= $datas['pph'];
    $t_kbon = $t_bpb - $tax;
    $t_pay = $datas['debit_idr'];
    $balance = $t_kbon - $t_pay;
}
?>



<tr>
      <td colspan="4" style="width:35%;text-align:center;"><b>Jumlah</b></td>	  

	  <td style="width:26%;text-align:right;border-left:none;"><?php echo "Rp."." ".number_format($t_kbon, 2) ?></td>
	  <td style="width:26%;text-align:right;border-left:none;"><?php echo "Rp."." ".number_format($t_pay, 2) ?></td>
	  <td style="width:26%;text-align:right;border-left:none;"><?php echo "Rp."." ".number_format($balance, 2) ?></td>


    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:12px">

	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			SubTotal
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo "Rp."." ".number_format($t_bpb, 2); ?>
		</td>		
	</tr>	


	<tr>
		<td width="70%">
			
		</td>
			
		<td>
			Tax(Pph) 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo "Rp."." ".number_format($tax, 2); ?>
		</td>		
	</tr>	

	<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Total  
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo "Rp."." ".number_format($t_kbon, 2) ?>
		</td>
</tr>

<tr>
		<td width="70%">
			
		</td>
			
		<td >
			Total Pay 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo "Rp."." ".number_format($t_pay, 2) ?>
		</td>
</tr>	

<tr>
		<td width="70%">
			
		</td>
			
		<td width="100px">
			Balance
		</td>
<td style="width:1%">:</td>
		<td width="130px" style="text-align:right">
			<?php
			if ($balance < 1) {
				echo "Rp."." ".number_format($balance, 2)." (PAID OFF) ";
			}else{
			 echo "Rp."." ".number_format($balance, 2); 
			}
			 ?>
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


