<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_ib = isset($_POST['no_ib']) ? $_POST['no_ib']: null;
$refdoc = isset($_POST['refdoc']) ? $_POST['refdoc']: null;
if ($refdoc == 'Bank Keluar') {
    $sql = mysqli_query($conn1,"select CONCAT(b.no_coa,' ',b.nama_coa) as coa, IF(a.no_reff = '', '-', a.no_reff) as reff_doc,IF(a.reff_date = '1970-01-01', '-', DATE_FORMAT(a.reff_date, '%d-%m-%Y')) as reff_date, if(a.deskripsi = '', '-',a.deskripsi) as deskripsi, a.t_debit, a.t_credit from b_bankin_none a left join mastercoa_v2 b on b.no_coa = a.id_coa where no_bankin = '$no_ib'"); 
    $sql3 = mysqli_query($conn1,"select amount,rate,eqv_idr from tbl_bankin_arcollection where doc_num = '$no_ib'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    $rate = $row3['rate'];
    $eqv_idr = $row3['eqv_idr'];
}if ($refdoc == 'AR Collection') {
    $sql3 = mysqli_query($conn1,"select amount,rate,eqv_idr from tbl_bankin_arcollection where doc_num = '$no_ib'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    $rate = $row3['rate'];
    $eqv_idr = $row3['eqv_idr'];
}if ($refdoc == 'None') {
    $sql = mysqli_query($conn1,"select if(a.id_coa = '-', '-',CONCAT(b.no_coa,' ',b.nama_coa)) as coa,if(a.id_cost_center = '-','-',c.cc_name) as cost_center,if(a.buyer = '','-',a.buyer) as buyer ,if(a.no_ws = '','-',a.no_ws) as no_ws,a.curr,a.t_debit,a.t_credit from tbl_bankin a left join mastercoa_v2 b on b.no_coa = a.id_coa left join b_master_cc c on c.no_cc = a.id_cost_center where a.no_doc = '$no_ib'");
    $sql3 = mysqli_query($conn1,"select amount,rate,eqv_idr from tbl_bankin_arcollection where doc_num = '$no_ib'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    $rate = $row3['rate'];
    $eqv_idr = $row3['eqv_idr'];
    
}else{
}
  

if($refdoc == 'None'){
    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Coa</th>
                            <th style="width:15%;">Cost Center</th>
                            <th style="width:15%;">Buyer</th>                                                                                
                            <th style="width:15%;">WS</th>
                            <th style="width:8%;">Curr</th>
                            <th style="width:11%;">Debit</th> 
                            <th style="width:11%;">Credit</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['coa'].'">'.$row['coa'].'</td>
                            <td style="" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['t_debit'].'">'.number_format($row['t_debit'],2).'</td>
                            <td style="text-align: right;" value="'.$row['t_credit'].'">'.number_format($row['t_credit'],2).'</td> 
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

}elseif($refdoc == 'AR Collection'){
    $table = '';

}elseif($refdoc == 'Bank Keluar'){

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:150px;">COA</th>
                            <th style="width:50px;">Reff Doc</th>                                                                                
                            <th style="width:50px;">Reff Date</th>
                            <th style="width:50px;">Description</th>
                            <th style="width:50px;">Debit</th> 
                            <th style="width:50px;">Credit</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="width:150px;" value="'.$row['coa'].'">'.$row['coa'].'</td>                                                                       
                            <td style="width:50px;" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
                            <td style="width:50px;" value="'.$row['reff_date'].'">'.$row['reff_date'].'</td>
                            <td style="width:50px;" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['t_debit'].'">'.number_format($row['t_debit'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['t_credit'].'">'.number_format($row['t_credit'],2).'</td> 
                       </tr>
                       ';
                   }

            $table .= '</table>';


}else{

}

echo $table;


if ($refdoc == 'Bank Keluar') {
 echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Amount
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($amount,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Rate
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($rate,2).'
        </td>       
    </tr>      

   <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Equivalent IDR
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($eqv_idr,2).'
        </td>
</tr>   
    
</table>';

}elseif ($refdoc == 'AR Collection') {
 echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Amount
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($amount,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Rate
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($rate,2).'
        </td>       
    </tr>      

   <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Equivalent IDR
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($eqv_idr,2).'
        </td>
</tr>   
    
</table>';

}else{
echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Amount
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($amount,2).'
        </td>       
    </tr>   
</table>';
}

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>