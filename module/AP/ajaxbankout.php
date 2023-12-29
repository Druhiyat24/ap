<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_ob = isset($_POST['no_ob']) ? $_POST['no_ob']: null;
$refdoc = isset($_POST['refdoc']) ? $_POST['refdoc']: null;
if ($refdoc == 'Payment Voucher' || $refdoc == 'List Payment') {
    $sql = mysqli_query($conn1,"select no_coa id_coa,nama_coa coa_name,reff_doc,IF(reff_date = '0000-00-00','-',reff_date) reff_date,nama_costcenter cost_center,buyer,no_ws,curr,debit,credit,keterangan from tbl_list_journal where no_journal = '$no_ob'");
    $sql2 = mysqli_query($conn1,"select CONCAT(b.no_coa,' ',b.nama_coa) as coa, IF(a.reff_doc = '', '-', a.reff_doc) as reff_doc,IF(a.reff_date = '1970-01-01', '-', DATE_FORMAT(a.reff_date, '%d-%m-%Y')) as reff_date, a.deskripsi, a.t_debit, a.t_credit from b_bankout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa where no_bankout = '$no_ob'"); 
    $sql3 = mysqli_query($conn1,"select amount,rate,eqv_idr from b_bankout_h where no_bankout = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    $rate = $row3['rate'];
    $eqv_idr = $row3['eqv_idr'];
}if ($refdoc == 'None') {
    $sql = mysqli_query($conn1,"select no_coa id_coa,nama_coa coa_name,nama_costcenter cost_center,buyer,no_ws,curr,debit,credit,keterangan from tbl_list_journal where no_journal = '$no_ob'");
    $sql3 = mysqli_query($conn1,"select amount,rate,eqv_idr from b_bankout_h where no_bankout = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    $rate = $row3['rate'];
    $eqv_idr = $row3['eqv_idr'];
    
}else{
}
  

if($refdoc == 'None'){
    $table = '<table id="mytdmodal" class="table table-responsive table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="">No Coa</th>
                            <th style="">Coa Name</th>
                            <th style="">Cost Center</th>
                            <th style="">Buyer</th>                                                                            
                            <th style="">WS</th>
                            <th style="">Curr</th>
                            <th style="">Debit</th> 
                            <th style="">Credit</th>   
                            <th style="">Description</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['id_coa'].'">'.$row['id_coa'].'</td>
                            <td style="" value="'.$row['coa_name'].'">'.$row['coa_name'].'</td>
                            <td style="" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td>
                            <td style="text-align: right;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td> 
                            <td style="width:100px;" value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

}elseif($refdoc == 'Payment Voucher' || $refdoc == 'List Payment'){

    $table = '<table id="mytdmodal" class="table table-responsive table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No Coa</th>
                            <th style="width:100px;">Coa Name</th>
                            <th style="width:100px;">Cost Center</th>
                            <th style="width:50px;">Reff Doc</th>                                                                          
                            <th style="width:50px;">Reff Date</th>
                            <th style="width:50px;">Curr</th>
                            <th style="width:50px;">Credit</th> 
                            <th style="width:50px;">Debit</th>  
                            <th style="width:100px;">Description</th>                                                                                  
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>
                            <td style="width:100px;" value="'.$row['id_coa'].'">'.$row['id_coa'].'</td>
                            <td style="width:100px;" value="'.$row['coa_name'].'">'.$row['coa_name'].'</td>
                            <td style="width:100px;" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="width:100px;" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
                            <td style="width:100px;" value="'.$row['reff_date'].'">'.$row['reff_date'].'</td>
                            <td style="width:100px;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td> 
                            <td style="width:100px;" value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>
                       </tr>
                       ';
                       $table .= '</tbody>';
                   }
            $table .= '</table>';


}else{

}

echo $table;

if ($refdoc == 'Payment Voucher' || $refdoc == 'List Payment') {
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