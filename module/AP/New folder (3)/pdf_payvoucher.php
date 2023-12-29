<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_pv=$_GET['no_pv'];
?>

<?php
$sql= "select * from tbl_pv_h where no_pv = '$no_pv'";
$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));
$amount = $rs['subtotal'];
$adjust = $rs['adjust'];

$twot = $amount + $adjust;
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

	
  <title>Payment Voucher</title>
</head>
<body style=" padding-left:5%; padding-right:5%;">
  <div class="header">
        <table width="100%" border="1">
            <tr>
                <td rowspan ="3" style="border-right: none;">
                    <img src="../../images/img-01.png" style="heigh:65px; width:75px;">
                </td>
                <td rowspan ="3" style="text-align: center;border-left: none; width: 51%;font-weight: bold;">
                    PAYMENT VOUCHER
                </td>
                <td style="border-right: none;font-size: 12px;width: 12%;border-bottom: none;">
                    Document No
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;border-bottom: none;">
                    <?php echo $no_pv?>
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-bottom: none;border-top: none;">
                    Date
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-bottom: none;border-top: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;border-bottom: none;border-top: none;">
                    <?php echo date("d F Y",strtotime($rs['pv_date']));?>
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;">
                    Division
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;border-top: none;">
                    PT Nirwana Alabare Garment
                </td>
            </tr>
        </table>
        <table width="100%" border="1">
        	<tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Payment To
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php echo $rs['nama_supp'];?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    Payment Date
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
                    <?php echo date("d F Y",strtotime($rs['pay_date']));?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Payment For
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php
      					$sql3 = mysqli_query($conn2," select for_pay,ke,dari from tbl_pv_h where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$for_pay = $rows3['for_pay'];
      					$ke = $rows3['ke'];
      					$dari = $rows3['dari'];
						if ($for_pay == 'Cicilan Pinjaman Bank' || $for_pay == 'Cicilan Aktiva Tetap') {
							echo $for_pay; echo ' (Cicilan ke '; echo $ke; echo ' dari '; echo $dari; echo')';
						}else{
							echo $for_pay;
						}
					?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    From Account
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                   <?php
      					$sql3 = mysqli_query($conn2," select frm_akun from tbl_pv_h where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$frm_akun = $rows3['frm_akun'];
						if ($frm_akun == '-') {
							echo ''; 
						}else{
							echo $frm_akun;
						}
					?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    To Account
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
                    <?php
      					$sql3 = mysqli_query($conn2," select to_akun from tbl_pv_h where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$to_akun = $rows3['to_akun'];
						if ($to_akun == '-') {
							echo '-'; 
						}else{
							echo $to_akun;
						}
					?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Payment Method
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php echo $rs['pay_meth'];?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                   
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Cheque No
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                     <?php
      					$sql3 = mysqli_query($conn2," select no_cek from tbl_pv_h where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$no_cek = $rows3['no_cek'];
						if ($no_cek == '') {
							echo '-'; 
						}else{
							echo $no_cek;
						}
					?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    Cheque Date
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
                    <?php
      					$sql3 = mysqli_query($conn2," select cek_date from tbl_pv_h where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$cek_date = $rows3['cek_date'];
						if ($cek_date == '0000-00-00' || $cek_date == '1970-01-01') {
							echo '-'; 
						}else{
							echo date("d F Y",strtotime($cek_date));
						}
					?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Supporting Doc
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php echo $rs['supp_doc'];?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Charge To Buyer
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                   <?php $ctb =  $rs['ctb'];
                   if ($ctb == '' || $ctb == 'Unrealize') {
							echo '-'; 
						}else{
							echo $ctb;
						}?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Refference Doc
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php
      					$sql3 = mysqli_query($conn2," select GROUP_CONCAT(if(reff_doc = '', '-', reff_doc)) as reff_doc from tbl_pv where no_pv = '$no_pv' ORDER BY id asc");
      					$rows3 = mysqli_fetch_array($sql3);
      					$reff_doc = $rows3['reff_doc'];
						if ($reff_doc == '') {
							echo ''; 
						}else{
							echo $reff_doc;
						}
					?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Reff Doc Date
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
                    <?php
      					$sql3 = mysqli_query($conn2,"select GROUP_CONCAT(if(reff_date = '1970-01-01', '-', reff_date)) as reff_date from tbl_pv where no_pv = '$no_pv' ORDER BY id asc");
      					$rows3 = mysqli_fetch_array($sql3);
      					$reff_date = $rows3['reff_date'];
						if ($reff_date == '') {
							echo ''; 
						}else{
							echo $reff_date;
						}
					?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;width: 19%;vertical-align: top;">
                    Description
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;vertical-align: top;">
                    :
                </td>
                <td colspan="5" style="border-left: none;font-size: 12px;border-top: none;border-right: none;">
                    <?php echo $rs['deskripsi'];?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    Amount
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; border-bottom: none;">
                    
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;padding-left: 10px;">
                    Total Without Tax
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                   : 
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; border-bottom: none;text-align: right;padding-right: 20px;">
                  <?php echo $rs['curr']; echo ' '; echo number_format($twot, 2); ?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;padding-left: 10px;">
                	<?php
      					$sql3 = mysqli_query($conn2,"select max(pph) as pph from tbl_pv where no_pv = '$no_pv'");
      					$rows3 = mysqli_fetch_array($sql3);
      					$pph = $rows3['pph'];
						
					?>
                    Incoming Tax(<?php echo $pph;?>%)
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                   : 
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; border-bottom: none;text-align: right;padding-right: 20px;">
                  (<?php echo $rs['curr']; echo ' '; echo number_format(abs($rs['pph']), 2); ?>)
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;padding-left: 10px;width: 21%;">
                    Value Added Tax(<?php echo $rs['per_ppn'];?>%)
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;">
                   : 
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;text-align: right;padding-right: 20px;">
                  <?php echo $rs['curr']; echo ' '; echo number_format($rs['ppn'], 2); ?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;padding-left: 10px;border-bottom: none;">
                    Grand Total
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                   : 
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; text-align: right;padding-right: 20px;border-bottom: none;">
                 <?php echo $rs['curr']; echo ' '; echo number_format($rs['total'], 2); ?>
                </td>
                <td style="width: 3%;border-left: none;border-right: none;border-top: none;border-bottom: none; ">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>
            <tr>
                <td style="border-right: none;font-size: 12px;border-top: none;padding-left: 10px;padding-top: 8px;">
                  
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; text-align: right;padding-right: 20px;">
                  
                </td>
                <td style="width: 3%;border-left: none;border-right: none;border-top: none; ">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;">
                    
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;">
                    
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
                    
                </td>
                <td style="width: 2%;border-left: none; border-top: none;">
                    
                </td>
            </tr>

        </table>
        <table width="100%" border="1">
        	<tr>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Created By  
                </td>
                <td colspan="2" style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                 Checked By  
                </td>
                <td colspan="4" style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                 Approved By   
                </td>
                
            </tr>
            <tr>
                <td style="width: 13%;font-size: 12px; padding-top: 40px;font-weight: bold;">
                  
                </td>
                <td style="width: 13%;font-size: 12px;font-weight: bold;">
                   
                </td>
                <td style="width: 16%;font-size: 12px;font-weight: bold;">
                   
                </td>
                <td style="width: 14%;font-size: 12px;font-weight: bold;">
                    
                </td>
                <td style="width: 14%;font-size: 12px;font-weight: bold;">
                    
                </td>
                <td style="width: 15%;font-size: 12px;font-weight: bold;">
                    
                </td>
                <td style="width: 15%;font-size: 12px;font-weight: bold;">
                    
                </td>
            </tr>
            <tr>
                <td style="width: 13%;font-size: 11px;text-align: center;font-weight: bold;">
                  Putrie
                </td>
                <td style="width: 13%;font-size: 11px;text-align: center;font-weight: bold;">
                  Mandy 
                </td>
                <td style="width: 16%;font-size: 11px;text-align: center;font-weight: bold;">
                  Willy 
                </td>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Herman  
                </td>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Budiarto  
                </td>
                <td style="width: 15%;font-size: 11px;text-align: center;font-weight: bold;">
                  Syenni Santosa  
                </td>
                <td style="width: 15%;font-size: 11px;text-align: center;font-weight: bold;">
                  Sylvia Santosa  
                </td>
            </tr>
            <tr>
                <td style="width: 13%;font-size: 11px;text-align: center;font-weight: bold;">
                  Staff Bank
                </td>
                <td style="width: 13%;font-size: 11px;text-align: center;font-weight: bold;">
                  SPV Bank 
                </td>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Fin&Acc Manager 
                </td>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Kadept Fin&Acc  
                </td>
                <td style="width: 14%;font-size: 11px;text-align: center;font-weight: bold;">
                  Kadiv Fin&Acc  
                </td>
                <td style="width: 15%;font-size: 11px;text-align: center;font-weight: bold;">
                  Director  
                </td>
                <td style="width: 15%;font-size: 11px;text-align: center;font-weight: bold;">
                  Director  
                </td>
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