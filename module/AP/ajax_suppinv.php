<?php
include '../../conn/conn.php';
$tot_inv = 0; 
$no_document = isset($_POST['no_document']) ? $_POST['no_document']: null;

$sql = mysqli_query($conn2,"select no_invoice,tgl_invoice,amount from ir_invoice_supp where doc_number = '$no_document'");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No Invoice</th>
                            <th style="width:100px;">Invoice Date</th>
                            <th style="width:100px;">Amount</th>                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_inv += $row['amount'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_invoice'].'">'.$row['no_invoice'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_invoice'].'">'.date("d-M-Y",strtotime($row['tgl_invoice'])).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>                            
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