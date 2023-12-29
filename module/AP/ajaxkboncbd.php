<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = ''; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select ftr_cbd.no_ftr_cbd, ftr_cbd.no_po, ftr_cbd.tgl_po, ftr_cbd.subtotal as sub, ftr_cbd.tax as tax, ftr_cbd.total - kontrabon_cbd.pph_value as total, kontrabon_cbd.pph_value as pph, ftr_cbd.supp, ftr_cbd.biaya_tambahan from ftr_cbd inner join kontrabon_cbd on kontrabon_cbd.no_cbd = ftr_cbd.no_ftr_cbd and kontrabon_cbd.no_po = ftr_cbd.no_po  where kontrabon_cbd.no_kbon = '$no_kbon'");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No FTR</th>
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">Tgl PO</th>                                                                                
                            <th style="width:50px;">Subtotal</th>
                            <th style="width:50px;">Tax</th>
                            <th style="width:50px;">PPh</th>                            
                            <th style="width:50px;">Total</th>                            
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$sub += $row['sub'];
			$tax += $row['tax'];
            $pph += $row['pph'];
            $tambahan = $row['biaya_tambahan'];
			$total = $sub + $tax - $pph + $tambahan;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_ftr_cbd'].'">'.$row['no_ftr_cbd'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['sub'].'">'.number_format($row['sub'],2).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="width:50px;text-align:right;" value="'.$row['pph'].'">-'.number_format($row['pph'],2).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                            
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
            
        <td>
            Tax (PPh) 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            -'.number_format($pph,2).'
        </td>       
    </tr>  

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Ongkos Kirim 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tambahan,2).'
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
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>