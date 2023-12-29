<?php
include '../../conn/conn.php';
$tot_kbon = 0;
$tot_outstanding = 0;
$tot_amount = 0; 
$no_payment = isset($_POST['no_payment']) ? $_POST['no_payment']: null;

$sql = mysqli_query($conn2,"select no_payment, tgl_payment, no_kbon, tgl_kbon, nama_supp, SUM(total_kbon) as total_kbon, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where no_payment = '$no_payment' group by no_kbon");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No Kontrabon</th>
                            <th style="width:100px;">Tgl Kontrabon</th>
                            <th style="width:100px;">TOP</th>
                            <th style="width:100px;">Due Date</th>                                                                                                           
                            <th style="width:50px;">Total Kontrabon</th>
                            <th style="width:50px;">Amount</th>                                                         
                            <th style="width:50px;">Outstanding</th>
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_kbon += $row['total_kbon'];
			$tot_outstanding += $row['outstanding'];
            $tot_amount += $row['amount'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td style="width:100px;" value="'.$row['top'].'">'.$row['top'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>                                                                                                                
                            <td style="width:50px;text-align: right;" value="'.$row['total_kbon'].'">'.number_format($row['total_kbon'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>                           
                            <td style="width:50px;text-align: right;" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>                            
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
            Total Kontrabon
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tot_kbon,2).'
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
           '.number_format($tot_amount,2).'
        </td>
</tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Outstanding 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tot_outstanding,2).'
        </td>       
    </tr>      
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>