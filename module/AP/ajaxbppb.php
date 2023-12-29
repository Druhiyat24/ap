<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_ro = isset($_POST['no_ro']) ? $_POST['no_ro']: null;
$sql = mysqli_query($conn1,"select bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate, bppb.bpbno_ro as bpbno_ro, bppb.qty as qty, bppb.price as price, bppb.unit as unit, bppb.confirm_by as confirm_by, masteritem.itemdesc, masteritem.color, masteritem.size, mastersupplier.Supplier as Supplier from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier INNER JOIN masteritem on masteritem.id_item = bppb.id_item where cancel != 'Y' and bppbno_int = '$no_ro' and bppb.ap_inv is null");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:20%;">No BPPB</th>
                            <th style="width:15%;">BPPB Date</th> 
                            <th style="width:10%;">Confirm</th> 
                            <th style="width:25%;">Material</th>
                            <th style="width:10%;">Qty</th>                                                                                
                            <th style="width:10%;">UOM</th>
                            <th style="width:10%;">Price</th>
                            <th style="width:10%;">SubTotal</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$qty = $row['qty'];
			$price = $row['price'];
			$sub += $qty * $price;
            $kali = $qty * $price;
			$total = $sub;
            $table .= '<tr>
                            <td style="width:20%;" value="'.$row['bppbno_int'].'">'.$row['bppbno_int'].'</td>   
                            <td style="width:15%;" value="'.$row['bppbdate'].'">'.$row['bppbdate'].'</td>    
                            <td style="width:10%;" value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td>                                                                                           
                            <td style="width:25%;" value="'.$row['itemdesc'].' ('.$row['color'].' '.$row['size'].')">'.$row['itemdesc'].' ('.$row['color'].' '.$row['size'].')</td>
                            <td style="width:10%;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>                            
                            <td style="width:10%;" value="'.$row['unit'].'">'.$row['unit'].'</td>                            
                            <td style="width:10%;text-align: right;" value="'.$row['price'].'">'.number_format($row['price'],4).'</td>
                            <td style="width:50px;text-align: right;" value="'.$kali.'">'.number_format($kali,2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:14px; font-weight: bold;">
    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Return
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>