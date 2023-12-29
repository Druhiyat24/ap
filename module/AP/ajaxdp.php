<?php
include '../../conn/conn.php';
$tot_po = 0;
$dp = 0;
$sum_bal = 0; 
$noftrdp = isset($_POST['noftrdp']) ? $_POST['noftrdp']: null;

$sql = mysqli_query($conn2,"select no_po, tgl_po, no_pi, supp, SUM(total) as total_po, SUM(dp) as dp_code, SUM(dp_value) as dp_value, SUM(balance) as balance from ftr_dp where no_ftr_dp = '$noftrdp' group by no_po");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">Tgl PO</th>
                            <th style="width:100px;">No PI</th>                                                                                
                            <th style="width:50px;">Total PO</th>
                            <th style="width:50px;">DP (%)</th>
                            <th style="width:50px;">DP Amount</th>
                            <th style="width:50px;">Balance</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_po += $row['total_po'];
			$dp += $row['dp_value'];
            $sum_bal += $row['balance'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
                            <td style="width:100px;" value="'.$row['no_pi'].'">'.$row['no_pi'].'</td>                                                        
                            <td style="width:50px;text-align: right;" value="'.$row['total_po'].'">'.number_format($row['total_po'],2).'</td>
                            <td style="width:50px;" value="'.$row['dp_code'].'">'.number_format($row['dp_code'],2).'</td>                            
                            <td style="width:50px;text-align: right;" value="'.$row['dp_value'].'">'.number_format($row['dp_value'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['balance'].'">'.number_format($row['balance'],2).'</td>                            
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
            Total (PO)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tot_po,2).'
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
            
        <td >
            Total Balance
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($sum_bal,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>