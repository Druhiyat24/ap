<?php
include '../../conn/conn.php';
$total = 0;
$total_idr = 0;
$payment_ftr_id = isset($_POST['payment_ftr_id']) ? $_POST['payment_ftr_id']: null;

$sql = mysqli_query($conn2,"select no_coa id_coa,nama_coa coa_name,reff_doc,IF(reff_date = '0000-00-00','-',reff_date) reff_date,nama_costcenter cost_center,buyer,no_ws,curr,debit,credit, (debit * rate) debit_idr, (credit * rate) credit_idr,keterangan from tbl_list_journal where no_journal = '$payment_ftr_id'");	

$table = '<table id="mytdmodal" class="table table-responsive table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
<thead>
<tr >                       
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
   $total += $row['debit'];
   $total_idr += $row['debit_idr'];
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

echo $table;

echo '<table width="100%" border="0" style="font-size:12px">

<tr>
<td width="70%">

</td>

<th>
Total
</th>
<th style="width:1%">:</th>
<th style="text-align:right">
'.number_format($total,2).'
</th>       
</tr>   

<tr>
<td width="70%">

</td>

<th >
Total IDR
</th>
<th style="width:1%">:</th>
<th style="text-align:right">
'.number_format($total_idr,2).'
</th>
</tr>   

</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>