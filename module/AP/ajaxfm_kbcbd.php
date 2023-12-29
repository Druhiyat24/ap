<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$tot_po = 0;
$tot_dp = 0; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;


$sql = mysqli_query($conn2,"select no_cbd,curr, subtotal, total, tax, pph_value from kontrabon_cbd where no_kbon = '$no_kbon' and status = 'draft' group by id ");

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:120px;">No FTR DP</th>
                            <th style="width:120px;">FTR DP Date</th>
                            <th style="width:120px;">No PO</th>                                                                                
                            <th style="width:120px;">PO Date</th>
                            <th style="width:80px;">Total PO</th>
                            <th style="width:80px;">Tax (Ppn)</th>
                            <th style="width:80px;">Tax (Pph)</th>                            
                            <th style="width:80px;">Total </th>
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $no_dp = $row['no_cbd'];
                $sub += $row['subtotal'];
                $tax += $row['tax'];
                $pph += $row['pph_value'];
                $total = $sub + $tax - $pph;
                $sql2 = mysqli_query($conn2,"select * from ftr_cbd where no_ftr_cbd = '$no_dp' ");
                $curr = $row['curr'];
                while($row2 = mysqli_fetch_assoc($sql2)){
                    $tot_po += $row2['total'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row2['no_ftr_cbd'].'">'.$row2['no_ftr_cbd'].'</td>                                                                      
                            <td style="width:100px;" value="'.$row2['tgl_ftr_cbd'].'">'.date("d-M-Y",strtotime($row2['tgl_ftr_cbd'])).'</td>                            
                            <td style="width:100px;" value="'.$row2['no_po'].'">'.$row2['no_po'].'</td>
                            <td style="width:100px;" value="'.$row2['tgl_po'].'">'.date("d-M-Y",strtotime($row2['tgl_po'])).'</td> 
                            <td style="width:50px;text-align:right;" value="'.$row2['total'].'">'.number_format($row2['total'],2).'</td>
                            <td style="width:50px;text-align:right;" value="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="width:50px;text-align:right;" value="'.$row['pph_value'].'"> - '.number_format($row['pph_value'],2).'</td>                                                    
                            <td style="width:50px;text-align:right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
    }
            $table .= '</table>';

echo $table;
echo '<table width="100%" border="0" style="font-size:12px;">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            SubTotal
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.$curr.' '.number_format($sub,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (Ppn)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.$curr.' '.number_format($tax,2).'
        </td>       
    </tr>
     <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (Pph)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.$curr.' - '.number_format($pph,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight : bold;">
            Total kontrabon
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right;font-weight : bold;">
            '.$curr.' '.number_format($total,2).'
        </td>       
    </tr>   
    
</table>';
echo "</br>";



// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>