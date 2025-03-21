<?php
include '../../conn/conn.php';
$tot_inv = 0; 
$no_req = isset($_POST['no_req']) ? $_POST['no_req']: null;

$sql = mysqli_query($conn2,"select no_po,coalesce(no_bpb,'-') no_bpb,item,qty,price,(qty * price) total,attn,seasons,no_reff from req_dn where no_req = '$no_req'");	

	$table = '<table id="mytdmodal"  class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px; text-align:center;">
                    <thead>
                        <tr class="bg-dark text-white">                       
                            <th style="width: 10%;">No PO</th>
                            <th style="width: 10%;">No BPB</th>
                            <th style="width: 20%;">Item</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 10%;">Price</th>
                            <th style="width: 10%;">Total</th>
                            <th style="width: 10%;">Attn</th>
                            <th style="width: 10%;">Seasons</th>    
                            <th style="width: 10%;">Reff</th>                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_inv += $row['total'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="width:100px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:100px;" value="'.$row['item'].'">'.$row['item'].'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>      
                            <td style="width:100px;" value="'.$row['attn'].'">'.$row['attn'].'</td>
                            <td style="width:100px;" value="'.$row['seasons'].'">'.$row['seasons'].'</td>
                            <td style="width:100px;" value="'.$row['no_reff'].'">'.$row['no_reff'].'</td>                      
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <th width="70%">
            
        </th>
            
        <th>
            Grand Total
        </th>
<th style="width:1%">:</th>
        <th style="text-align:right">
            '.number_format($tot_inv,2).'
        </th>       
    </tr>     
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>