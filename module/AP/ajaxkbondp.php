<?php
include '../../conn/conn.php';
$sub = 0;
$dp = 0;
$pph = 0;
$total = ''; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select ftr_dp.no_ftr_dp, ftr_dp.no_po, ftr_dp.tgl_po, SUM(ftr_dp.total) as sub, SUM(ftr_dp.dp_value) as dp, SUM(ftr_dp.balance) as total, kontrabon_dp.pph_value as pph, ftr_dp.supp from ftr_dp inner join kontrabon_dp on kontrabon_dp.no_dp = ftr_dp.no_ftr_dp where kontrabon_dp.no_kbon = '$no_kbon' group by ftr_dp.no_ftr_dp");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No FTR</th>
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">Tgl PO</th>                                                                                
                            <th style="width:50px;">Subtotal</th>
                            <th style="width:50px;">DP Amount</th>
                            <th style="width:50px;">PPh</th>                            
                            <th style="width:50px;">Balance</th>                            
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$sub += $row['sub'];
			$dp += $row['dp'];
            $pph += $row['pph'];
			$total = $dp - $pph;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_ftr_dp'].'">'.$row['no_ftr_dp'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['sub'].'">'.number_format($row['sub'],2).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['dp'].'">'.number_format($row['dp'],2).'</td>
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
            DP Amount 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($dp,2).'
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
            
        <td >
            Total Amount
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