<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select no_po, tgl_po, no_pi, supp, SUM(subtotal) as sub, SUM(tax) as tax, SUM(total) as total from ftr_cbd where no_ftr_cbd = '$no_kbon' group by no_po");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">Tgl PO</th>
                            <th style="width:100px;">No PI</th>                                                                                
                            <th style="width:50px;">Subtotal</th>
                            <th style="width:50px;">Tax</th>
                            <th style="width:50px;">Total</th>                            
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$sub += $row['sub'];
			$tax += $row['tax'];
			$total = $sub + $tax;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
                            <td style="width:100px;" value="'.$row['no_pi'].'">'.$row['no_pi'].'</td>                                                        
                            <td style="width:50px;text-align: right;" value="'.$row['sub'].'">'.number_format($row['sub'],2).'</td>                            
                            <td style="width:50px;text-align: right;" value="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                            
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Subtotal
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($sub,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (PPn)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tax,2).'
        </td>       
    </tr>      

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>