<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0; 
$id = isset($_POST['id']) ? $_POST['id']: null;
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select * from pengajuan_kb where id = '$id' and no_kbon = '$no_kbon' group by id ");
$sql2 = mysqli_query($conn2,"select curr, no_bpb, tgl_bpb, no_po, subtotal, tax, pph_value, total from kontrabon where no_kbon = '$no_kbon' ");
$row = mysqli_fetch_assoc($sql);

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                            <hr/>
                            <p style="font-size: 13px;">
                             <b>Message :</b> '.$row['pesan'].' </p>
                        <hr/>
                        <tr>                       
                            <th style="width:100px;">No BPB</th>                                                                                
                            <th style="width:100px;">BPB Date</th>
                            <th style="width:100px;">No PO</th> 
                            <th style="width:100px;">SubTotal</th>
                            <th style="width:100px;">Tax (Ppn)</th>
                            <th style="width:100px;">Tax (Pph)</th>
                            <th style="width:100px;">Total</th>                            
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row2 = mysqli_fetch_assoc($sql2)) {
                $sub += $row2['subtotal'];
                $tax += $row2['tax'];
                $pph += $row2['pph_value'];
                $total = $sub + $tax - $pph;
                 $curr = $row2['curr'];
            $table .= '<tr>                                                                                         
                            <td style="width:100px;" value="'.$row2['no_bpb'].'">'.$row2['no_bpb'].'</td>
                            <td style="width:100px;" value="'.$row2['tgl_bpb'].'">'.date("d-M-Y",strtotime($row2['tgl_bpb'])).'</td>
                            <td style="width:100px;" value="'.$row2['no_po'].'">'.$row2['no_po'].'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row2['subtotal'].'">'.number_format($row2['subtotal'],2).'</td> 
                            <td style="width:50px;text-align:right;" value="'.$row2['tax'].'">'.number_format($row2['tax'],2).'</td> 
                            <td style="width:50px;text-align:right;" value="'.$row2['pph_value'].'"> - '.number_format($row2['pph_value'],2).'</td> 
                            <td style="width:50px;text-align:right;" value="'.$row2['total'].'">'.number_format($row2['total'],2).'</td>                                            
                       </tr>';
            $table .= '</tbody>';
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