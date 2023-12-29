<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$images = '../../images/img-01.png';
$no_pay =$_GET['nopay'];
?>

<?php
$sql= "select * from payment_ftrcbd where payment_ftr_id = '$no_pay' ";

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
<table width="100%">
<tr>
    <td ><h4><?php echo $no_pay ?></h4></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><h4>
      <?php
      $sql1 = mysqli_query($conn2,"select nama_supp from payment_ftrcbd where payment_ftr_id = '$no_pay'");
      $rows = mysqli_fetch_array($sql1);
        $supplier = isset($rows['nama_supp']) ? $rows['nama_supp']:'';
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
      <th style="text-align:center;width: 25%;padding-top: -5px;">PAYMENT DATE  :</th>
      <th style="text-align:center;width: 25%;padding-top: -5px;">PAY METHOD :</th>
      <th style="text-align:center;width: 25%;padding-top: -5px;">BANK :</th>
      <th style="text-align:center;width: 25%;padding-top: -5px;">ACCOUNT :</th>                           
    </tr>

    <tbody>
    <tr>          
      
    <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql1 = mysqli_query($conn2,"select tgl_pelunasan from payment_ftrcbd where payment_ftr_id = '$no_pay'");
      $rows = mysqli_fetch_array($sql1);
        $create_date = $rows['tgl_pelunasan'];
        echo date("d F Y", strtotime($create_date));
        ?>      
    </td>
    <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql2 = mysqli_query($conn2,"select cara_bayar from payment_ftrcbd where payment_ftr_id = '$no_pay'");
      $rows1 = mysqli_fetch_array($sql2);
        $tgl_tempo = $rows1['cara_bayar'];
        echo $tgl_tempo;
        ?>
    </td>
    <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql4 = mysqli_query($conn2,"select bank from payment_ftrcbd where payment_ftr_id = '$no_pay'");
      $rows3 = mysqli_fetch_array($sql4);
        $no_faktur = $rows3['bank'];
        echo $no_faktur;
        ?>
    </td>
    <td style="text-align:center;padding-top: -15px;padding-bottom: -10px;">
      <?php
      $sql3 = mysqli_query($conn2,"select account from payment_ftrcbd where payment_ftr_id = '$no_pay'");
      $rows2 = mysqli_fetch_array($sql3);
        $supp_inv = $rows2['account'];
        echo $supp_inv;
        ?>      
    </td>                                     
    </tr>

</tbody>
</table>
<hr />

<table  border="1" cellspacing="0" style="width:100%;font-size:22px;border-spacing:2px;">
  <tr> 
      <th style="width:23%;border: 1px solid black;text-align:center;">No List payment</th>
      <th style="width:23%;border: 1px solid black;text-align:center;">List Payment Date</th> 
      <th style="width:23%;border: 1px solid black;text-align:center;">No Kontrabon</th>
      <th style="width:23%;border: 1px solid black;text-align:center;">Kontrabon Date</th> 
      <th style="width:18%;border: 1px solid black;text-align:center;">Total Pay</th>      
      <th style="width:18%;border: 1px solid black;text-align:center;">Rate</th>
      <th style="width:18%;border: 1px solid black;text-align:center;">Nominal Pay</th>
     <!--  <th style="width:15%;border: 1px solid black;text-align:center;display: none;">No Faktur Pajak</th> -->
    </tr>
<tbody>

     <?php
$query = mysqli_query($conn2,$sql)or die(mysqli_error());
while($data=mysqli_fetch_array($query)){
    $no_kbon1 = $data['payment_ftr_id'];
    $tgl_kbon = $data['tgl_pelunasan'];
    $no_lp = $data['list_payment_id'];
    $tgl_lp = $data['tgl_list_payment'];    
    $curr1 = $data['valuta_ftr'];
    $curr2 = $data['valuta_bayar'];
    $total = $data['ttl_bayar'];
    if ($curr2 == 'IDR') {
        $amount = $data['nominal'];
    }else{
        $amount = $data['nominal_fgn'];
    }
    $rate1 = $data['rate'];
    if ($rate1 == '0') {
        $rate = '-';
    }else{
        $rate = number_format($data['rate'],2);
    }
    $sumtotal += $total;
    $sumamount += $amount;

   echo '<tr>
      <td style="text-align:center;">'.$no_lp.'</td>
      <td style="text-align:center;border-left:none;">'.date("d M Y",strtotime($tgl_lp)).'</td>
      <td style="text-align:center;border-left:none;">'.$no_kbon1.'</td>
      <td style="text-align:center;border-left:none;">'.date("d M Y",strtotime($tgl_kbon)).'</td>
      <td style="text-align:right;border-left:none;">'.$curr1.' '.number_format($total, 2).'</td>
      <td style="text-align:right;border-left:none;">'.$rate.'</td>
      <td style="text-align:right;border-left:none;">'.$curr2.' '.number_format($amount, 2).'</td>                      
    </tr>'; 
}   
?>


<tr>
      <td colspan="4" style="width:35%;text-align:center;"><b>Jumlah</b></td>     
      <td style="width:30%;text-align:right;border-left:none;"><?php echo $curr1.' '.number_format($sumtotal, 2) ?></td>
      <td style="width:30%;text-align:right;border-left:none;"></td>
      <td style="width:30%;text-align:right;border-left:none;"><?php echo $curr2.' '.number_format($sumamount, 2) ?></td>                 
    </tr>

  </tbody>
</table> 
<br>

<table width="100%" border="0" style="font-size:11px">

<tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total List Payment
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right;">
            <?php echo $curr1." ".number_format($sumtotal, 2); ?>
        </td>       
    </tr>   

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Payment
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            <?php echo $curr1." ".number_format($sumtotal, 2) ?>
        </td>
</tr>

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Rate
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;">
            <?php echo $rate ?>
        </td>
</tr>

    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight: bold;" >
            Nominal Payment
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right; font-weight: bold;">
            <?php echo $curr2." ".number_format($sumamount, 2) ?>
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
              <?php echo $user; ?>
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


