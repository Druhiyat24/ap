<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$images = '../../images/img-01.png';
$no_payment=$_GET['no_payment'];
$no_kbon=$_GET['no_kbon'];
?>

<?php

$sql= "select list_payment.no_payment as no_payment, list_payment.no_kbon as no_kbon, list_payment.tgl_kbon as tgl_kbon, list_payment.tgl_payment as tgl_payment, list_payment.nama_supp as nama_supp, list_payment.total_kbon as total_kbon, list_payment.outstanding as outstanding, list_payment.amount as amount, list_payment.curr as curr, list_payment.create_user as create_user, list_payment.status as status, list_payment.top as top, list_payment.tgl_tempo as tgl_tempo, list_payment.memo as memo, SUM(kontrabon.pph_value) as pph_value, kontrabon_h.subtotal as subtotal, kontrabon_h.dp_value as dp_value from list_payment inner join kontrabon_h on kontrabon_h.no_kbon = list_payment.no_kbon left join kontrabon on kontrabon.no_kbon = list_payment.no_kbon where no_payment = '$no_payment' group by no_kbon";

// $sql= "select list_payment.no_payment as no_payment, list_payment.no_kbon as no_kbon, list_payment.tgl_kbon as tgl_kbon, list_payment.tgl_payment as tgl_payment, list_payment.nama_supp as nama_supp, list_payment.total_kbon as total_kbon, list_payment.outstanding as outstanding, list_payment.amount as amount, list_payment.curr as curr, list_payment.create_user as create_user, list_payment.status as status, list_payment.top as top, list_payment.tgl_tempo as tgl_tempo, list_payment.memo as memo, list_payment.pph_value as pph_value, kontrabon_h.subtotal as subtotal, kontrabon_h.dp_value as dp_value from list_payment inner join kontrabon_h on kontrabon_h.no_kbon = list_payment.no_kbon where no_payment = '$no_payment' group by no_kbon";

$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));
ob_start();


$sqll = mysqli_query($conn2,"select sum(pph_value) as pph from list_payment where no_payment = '$no_payment' group by no_payment");    
$rowl = mysqli_fetch_assoc($sqll);
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

	
  <title>LIST PAYMENT</title>
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
	<td ><h4>No List Payment : <?php echo $no_payment?></h4></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from list_payment where no_payment = '$no_payment'");
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
      <th style="text-align:left;width: 25%;padding-top: -5px;"></th>
      <td style="text-align:left;width: 75%;padding-top: -5px;"></td>                               
    </tr>

	<tbody>
	<tr>  	      
	<td style="text-align:left;padding-left: 25px;padding-top: -15px;padding-bottom: -10px; font-weight: bold;">
      LIST PAYMENT DATE :		
	</td>
	<td style="text-align:left;padding-left: 5px;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select tgl_payment from list_payment where no_payment = '$no_payment'");
      $rows3 = mysqli_fetch_array($sql3);
      	$tgl_payment = $rows3['tgl_payment'];
		echo date("d F Y", strtotime($tgl_payment));
		?>		
	</td>									      
	</tr>
</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:120%;font-size:24px;border-spacing:2px;">
  <tr>
      <th style="width:35%;border: 1px solid black;text-align:center;">No Kontrabon</th>
      <th style="width:20%;border: 1px solid black;text-align:center;">Kontrabon Date</th>
      <th style="width:10%;border: 1px solid black;text-align:center;">TOP</th>
      <th style="width:10%;border: 1px solid black;text-align:center;">Due Date</th>            
      <th style="width:15%;border: 1px solid black;text-align:center;">Total KB</th>      
	  <th style="width:15%;border: 1px solid black;text-align:center;">Outstanding</th>
      <th style="width:15%;border: 1px solid black;text-align:center;">Amount</th>
<!--      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">Jatuh Tempo</th>
      <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Invoice</th>	  
	  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th>-->
    </tr>
<tbody>
<?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
$no_kbon = "";
$tgl_kbon = "";
$curr = "";
$top = 0;
$tgl_tempo = "";
$outstanding = 0;
$total_kbon = 0;
$total_bpb = 0;
$total_cash = 0;
$total_pph = 0;
$amount = 0;
while($data=mysqli_fetch_array($query)){
	$no_kbon = $data['no_kbon'];
	$tgl_kbon = $data['tgl_kbon'];	
	$curr = $data['curr'];
	$top = $data['top'];
	$tgl_tempo = $data['tgl_tempo'];
	$outstanding = $data['outstanding'];
	$total_kbon = $data['total_kbon'];
	$total_bpb += $data['subtotal'];
	$total_cash = $data['dp_value'];
	$total_pph = $rowl['pph'];
	$amount = $data['amount'];

   echo '<tr>
      <td style="width:35%;text-align:center;">'.$no_kbon.'</td>
      <td style="width:25%;text-align:center;">'.date("d M Y",strtotime($tgl_kbon)).'</td>
	  <td style="width:20%;text-align:center;">'.$top.' Day</td>
      <td style="width:20%;text-align:center;">'.date("d M Y",strtotime($tgl_tempo)).'</td>	  
	  <td style="width:15%;text-align:right;border-left:none;">'.$curr.' '.number_format($total_kbon, 2).'</td>
	  <td style="width:15%;text-align:right;border-left:none;">'.$curr.' '.number_format($outstanding, 2).'</td>
	  <td style="width:15%;text-align:right;border-left:none;">'.$curr.' '.number_format($amount, 2).'</td>		  		  	  
    </tr>';	
};	
?>

<?php
$sql1= "select no_payment, SUM(total_kbon) as total_kbon, SUM(outstanding) as outstanding, SUM(amount) as amount from list_payment where no_payment = '$no_payment'";
$querys = mysqli_query($conn2,$sql1)or die(mysqli_error());
while($datas=mysqli_fetch_array($querys)){
	$sum_total = $datas['total_kbon'];
	$sum_outstanding = $datas['outstanding'];
	$sum_amount = $datas['amount'];
}
?>

<!-- <?php
//$qty1 +=$qty1+ $qty;				
//$mata_uang = $data['curr'];
//$unit = $data['uom']; 

//$total_curr_bef_tax  = $total_curr_bef_tax + $data['subtotal'];
//$total_curr = $total_curr + $data['subtotal'];	
 
//$grand_total = $total_curr_bef_tax + $ppn_nya_____ - $value_pph + $total_utang_debit + $total_other;
 
?> -->

<tr>
      <td colspan="4" style="width:35%;text-align:center;"><b>Jumlah</b></td>	  
<!--       <td style="width:20%;text-align:center;"></td> -->
<!-- 	  <td style="width:9%;text-align:center;border-right:none"><?php echo number_format($sum_qty,2) ?></td> 
	  <td style="width:5%;text-align:left;border-left:none"><?php echo $uom ?></td> 
      <td style="width:5%;text-align:center;border-right:none"></td>
	  <td style="width:9%;text-align:center;border-left:none"></td> -->
	  <td style="width:30%;text-align:right;border-left:none;"><?php echo $curr.' '.number_format($sum_total, 2) ?></td>
<!--       <td style="width:auto;text-align:center;border-left:none"></td> -->
	  <td style="width:30%;text-align:right;border-left:none;"><?php echo $curr.' '.number_format($sum_outstanding, 2) ?></td>
	  <td style="width:30%;text-align:right;border-left:none;"><?php echo $curr.' '.number_format($sum_amount, 2) ?></td> 	        	  
<!-- 	  <td style="width:auto;text-align:center;display: none;"></td> -->
<!-- 	  <td style="width:15%;text-align:center;display: none;"></td> 
      <td style="width:15%;text-align:center;display: none;"></td> -->
    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:11px">

	<!-- <tr>
		<td width="60%">
			
		</td>
			
		<td style="font-weight: bold;">
			Total BPB
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right; font-weight: bold;">
			<?php echo $curr." ".number_format($total_bpb, 2); ?>
		</td>		
	</tr>	

	<tr>
		<td width="60%">
			
		</td>
			
		<td>
			Total Cash
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr." - ".number_format($total_cash, 2); ?>
		</td>		
	</tr>	 -->
	<tr>


		<td width="60%">
			
		</td>
			
		<td >
			Total Kontrabon
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right; ">
			<?php echo $curr." ".number_format($sum_total, 2); ?>
		</td>		
	</tr>	

	<tr>
		<td width="60%">
			
		</td>
			
		<td >
			Tax (Pph) 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right">
			<?php echo $curr."( - ".number_format($total_pph, 2)." )"; ?>
		</td>
</tr>

<tr>
	<tr>
		<td width="60%">
			
		</td>
			
		<td >
			Outstanding 
		</td>
<td style="width:1%">:</td>
		<td style="text-align:right;">
			<?php echo $curr." ".number_format($sum_outstanding, 2); ?>
		</td>		
	</tr>	


	<tr>
		<td width="60%">
			
		</td>
			
		<td style="font-weight: bold;">
			Total Amount 
		</td>
		<td style="width:1%">:</td>
		<td style="text-align:right; font-weight: bold;">
			<?php echo $curr." ".number_format($sum_amount, 2) ?>
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



	  <div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
    <table cellpadding="0" cellspacing="0" border="1" width='600';>

        <tr>    
            <th width='150' style="font-size:12px">Made By : </th>
            <th width='300'  colspan="2" style="font-size:12px">Checked By : </th>
            <th width='150'  style="font-size:12px">Approved By : </th>
    
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
            <td class="td1">&nbsp;</td>
            <td class="td1">&nbsp;</td>         
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
            <td class="td1">&nbsp;</td>
            <td class="td1">&nbsp;</td>
        </tr>   
        <tr>    
            <td class="td1">&nbsp;</td>
            <td class="td1">&nbsp;</td>
            <td class="td1">&nbsp;</td>
            <td class="td1">&nbsp;</td>
    
        </tr>

        <tr>    
            <td style="font-size:12px;text-align:center; width: 150px;text-decoration:underline;">
             <?php
            $sql1 = mysqli_query($conn2,"select create_user from list_payment where no_payment = '$no_payment'");
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
            <td style="font-size:12px;text-align:center;">Supervisor</td>
            <td style="font-size:12px;text-align:center">Finance Accounting Manager</td>
            <td style="font-size:12px;text-align:center">Director</td>
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


