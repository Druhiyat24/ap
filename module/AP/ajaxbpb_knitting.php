<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;

$sql = mysqli_query($conn1,"select '-' no_ws, nama_barang, jumlah_bpb qty, satuan, price, round(ppn/dpp * 100,0) tax from bpb_knitting where no_bpb = '$no_bpb'");

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">WS #</th>
                            <th style="width:100px;">Material</th>
                            <th style="width:50px;">Qty</th>                                                                                
                            <th style="width:50px;">UOM</th>
                            <th style="width:50px;">Price</th>
                            <th style="width:50px;">SubTotal</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$qty = $row['qty'];
			$price = $row['price'];
            if(strpos($price, '.') !== false){
                $prc = number_format($row['price'],4);
            }else{
                $prc = number_format($row['price'],2);
            }
			$tax = $row['tax'];
            $kali = $qty * $price;
			$sub += $qty * $price;
			$tax = $sub * ($tax / 100);
			$total = $sub + $tax;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['nama_barang'].'">'.$row['nama_barang'].'</td>
                            <td style="width:50px;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>                            
                            <td style="width:50px;" value="'.$row['satuan'].'">'.$row['satuan'].'</td>                            
                            <td style="width:50px;text-align: right;" value="'.$row['price'].'">'.$prc.'</td>
                            <td style="width:50px;text-align: right;" value="'.$kali.'">'.number_format($kali,2).'</td> 
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
            
        <td style="font-weight:bold;">
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>