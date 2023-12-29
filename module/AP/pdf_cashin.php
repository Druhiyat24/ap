<!DOCTYPE html>
<?php
include '../../conn/conn.php';
$images = '../../images/img-01.png';
$no_ci=$_GET['no_ci'];
?>

<?php
$sql= "select no_ci,tgl_ci,'KAS BESAR' akun,type_ci,deskrip,curr,create_by,approve_by FROM c_cash_in where no_ci = '$no_ci'";
$rs=mysqli_fetch_array(mysqli_query($conn2,$sql));

$sqlys = "select no_coa,nama_coa,nama_costcenter,reff_doc,reff_date,curr,debit,credit, keterangan from tbl_list_journal where debit > 0 and no_journal = '$no_ci' || credit > 0 and no_journal = '$no_ci'";


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

    margin-left: 1.54cm;

    margin-right: 1.54cm;

}



 	table{margin: auto;}

 	td,th{text-align: left}

 	h1{text-align: center}

 	th{text-align:center;}

	

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

	
  <title>Cash In</title>
</head>
<body style=" padding-left:-5%; padding-right:-5%;">
        <table width="100%" border="1">
            <tr>
                <td rowspan ="3" style="border-right: none;">
                    <img src="../../images/img-01.png" style="heigh:65px; width:75px;">
                </td>
                <td rowspan ="3" style="text-align: center;border-left: none; width: 51%;font-weight: bold;">
                    CASH IN
                </td>
                <td style="border-right: none;font-size: 12px;width: 12%;border-bottom: none;">
                    Document No
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;border-bottom: none;">
                    <?php echo $no_ci?>
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
                    <?php 
                    echo date("d F Y",strtotime($rs['tgl_ci']));
                    ?>
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
        <?php
            for ($x = 0; $x <= 5; $x++) {
        ?>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 3%; border: none;"></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;"></td>
            </tr>
        <?php
            }
        ?> 
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;">
                    Reff Doc
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;">
                    <?php 
                    echo $rs['type_ci'];
                    ?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    Account
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    <?php 
                    echo $rs['akun'];
                    ?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>

            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;">
                    Description
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;">
                    <?php 
                    echo $rs['deskrip'];
                    ?>
                </td>
                <td style="width: 3%; border: none;">
                    
                </td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
                    Currency
                </td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
                    :
                </td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">
                    <?php 
                    echo $rs['curr'];
                    ?>
                </td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">
                    
                </td>
            </tr>

        <?php
            for ($x = 0; $x <= 5; $x++) {
        ?>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 3%; border: none;"></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;"></td>
            </tr>
        <?php
            }
        ?> 
            
        </table>
        <table border="1" cellspacing="0" style="width:100%;font-size:10px;">
  <tr style="line-height: 10px;">
      <th style="width: 7%;text-align:center;">Coa No</th>
      <th style="width: 13%;text-align:center;">Coa Name</th>
      <th style="width: 13%;text-align:center;">Cost Center</th>
      <th style="width: 15%;text-align:center;">Reff Doc</th>
      <th style="width: 10%;text-align:center;">Reff Date</th>
      <th style="width: 7%;text-align:center;">Curr</th>
      <th style="width: 11%;text-align:center;">Debit</th>
      <th style="width: 11%;text-align:center;">Credit</th>
      <th style="width: 13%;text-align:center;">Description</th>
      <th style="display: none;"></th>  
    </tr>
<tbody >
<?php
$query = mysqli_query($conn2,$sqlys)or die(mysqli_error());

while($data=mysqli_fetch_array($query)){
            $reffdate = $data['reff_date'];
            
            if ($reffdate == '' || $reffdate == '1970-01-01' || $reffdate == '0000-00-00') { 
             $reff_date = '-';
            }else{
                $reff_date = date("d-M-Y",strtotime($data['reff_date'])); 
            } 
   echo '<tr>
        <td style="text-align: center" value="'.$data['no_coa'].'">'.$data['no_coa'].'</td>
        <td style="text-align: left" value="'.$data['nama_coa'].'">'.$data['nama_coa'].'</td>
        <td style="text-align: left" value="'.$data['nama_costcenter'].'">'.$data['nama_costcenter'].'</td>
        <td style="text-align: left" value="'.$data['reff_doc'].'">'.$data['reff_doc'].'</td> 
        <td style="text-align: left" value="'.$reff_date.'">'.$reff_date.'</td>                                                                      
        <td style="text-align: left" value="'.$data['curr'].'">'.$data['curr'].'</td> 
        <td style="text-align: right" value="'.$data['debit'].'">'.number_format($data['debit'],2).'</td>
        <td style="text-align: right" value="'.$data['credit'].'">'.number_format($data['credit'],2).'</td>                            
        <td style="text-align: left" value="'.$data['keterangan'].'">'.$data['keterangan'].'</td>
        <td style="display: none;"></td>  
    </tr>'; 
};  
?>

  </tbody>
</table>
<table width="100%" border="1">
        <?php
            for ($x = 0; $x <= 7; $x++) {
        ?>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 3%; border: none;"></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 10%;border-left: none; border-top: none;border-bottom: none;"></td>
            </tr>
        <?php
            }
        ?> 
        <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 13%;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 13%; border: none;font-size: 15px"><u><b>Created By</b></u></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 6%;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 15px;width: 22%; border-top: none;border-right: none;border-bottom: none;"><u><b>Checked By</b></u></td>
                <td style="width: 2%;border-left: none;width: 10%; border-top: none;border-bottom: none;"></td>
            </tr>
         <?php
            for ($x = 0; $x <= 20; $x++) {
        ?>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 3%; border: none;"></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;"></td>
            </tr>
        <?php
            }
        ?> 
        <!-- <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 13%;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 13%; border: none;font-size: 15px"><u><b><?php 
                    echo ucfirst($rs['create_by']);
                    ?></b></u></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 6%;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 15px;width: 22%; border-top: none;border-right: none;border-bottom: none;"><u><b>Senta</b></u></td>
                <td style="width: 2%;border-left: none;width: 10%; border-top: none;border-bottom: none;"></td>
            </tr>
 -->
            <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 13%;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 13%; border: none;font-size: 15px"><b>Cashier</b></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;width: 6%;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 15px;width: 22%; border-top: none;border-right: none;border-bottom: none;"><b>Staff Accounting</b></td>
                <td style="width: 2%;border-left: none;width: 10%; border-top: none;border-bottom: none;"></td>
            </tr>
            <?php
            for ($x = 0; $x <= 15; $x++) {
        ?>
            <tr>
                <td style="border-right: none;font-size: 12px;width: 10%;border-top: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 45%;border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 3%; border: none;"></td>
                <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;"></td>
                <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;"></td>
                <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;"></td>
                <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;"></td>
            </tr>
        <?php
            }
        ?> 
        <tr>
                <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;"></td>
                <td style="width: 1%; border-left: none;width: 13%;border-right: none;font-size: 12px;border-top: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;"></td>
                <td style="border-left: none;font-size: 12px;width: 12%;border-top: none;border-right: none;"></td>
                <td style="width: 2%;border-left: none;width: 10%; border-top: none;"></td>
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