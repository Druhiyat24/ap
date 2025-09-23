<?php
include '../../conn/conn.php';
$total = 0;
$total_idr = 0;
$payment_ftr_id = isset($_POST['payment_ftr_id']) ? $_POST['payment_ftr_id']: null;

$sql = mysqli_query($conn2,"select list_payment_id, tgl_list_payment, valuta_ftr curr, ttl_bayar total, rate, (ttl_bayar * rate) total_idr from payment_ftr where payment_ftr_id = '$payment_ftr_id'");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr >                       
                            <th style="width:100px;">No List Payment</th>
                            <th style="width:100px;">List Payment Date</th>
                            <th style="width:100px;">Curr</th>
                            <th style="width:50px;">Total</th> 
                            <th style="width:50px;">Rate</th> 
                            <th style="width:50px;">Equivalent IDR</th> 
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$total += $row['total'];
			$total_idr += $row['total_idr'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['list_payment_id'].'">'.$row['list_payment_id'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_list_payment'].'">'.date("d-M-Y",strtotime($row['tgl_list_payment'])).'</td>
                            <td style="width:100px;" value="'.$row['curr'].'">'.$row['curr'].'</td>       
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['rate'].'">'.number_format($row['rate'],2).'</td>                           
                            <td style="width:50px;text-align: right;" value="'.$row['total_idr'].'">'.number_format($row['total_idr'],2).'</td>                            
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