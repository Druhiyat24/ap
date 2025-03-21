<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_dok = isset($_POST['no_dok']) ? $_POST['no_dok']: null;

    $sql = mysqli_query($conn1,"SELECT supplier, no_po, no_ws, id_item, qty, price, ROUND((qty * price), 2) total, status FROM ca_adjust_input where no_dok = '$no_dok' GROUP BY id");


    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">No PO</th>
                            <th style="width:15%;">No WS</th>
                            <th style="width:15%;">Id Item</th>
                            <th style="width:9%;">Qty</th> 
                            <th style="width:10%;">Price</th>
                            <th style="width:11%;">Amount</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $amount += $row['total']; 
            $table .= '<tr>                       
                            <td style="text-align: left;">'.$row['supplier'].'</td>
                            <td style="text-align: left;">'.$row['no_po'].'</td>
                            <td style="text-align: left;">'.$row['no_ws'].'</td>
                            <td style="text-align: left;">'.$row['id_item'].'</td>                    
                            <td style="text-align: right;">'.number_format($row['qty'],2).'</td>
                            <td style="text-align: right;">'.number_format($row['price'],2).'</td>
                            <td style="text-align: right;">'.number_format($row['total'],2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;



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

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>