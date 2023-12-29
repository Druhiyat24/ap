<?php
include '../../conn/conn.php';
$tot_kbon = 0;
$tot_outstanding = 0;
$tot_amount = 0; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank, valuta_bayar, nominal, ttl_cicilan, keterangan, ttl_bayar,nominal,SUM(nominal) as sum from payment_ftr  where no_kbon = '$no_kbon' group by payment_ftr_id");	

$sql2 = mysqli_query($conn2,"select pph from kontrabon_h where no_kbon = '$no_kbon' ");
$row2 = mysqli_fetch_assoc($sql2);
$pph = $row2['pph'];

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No Payment</th>
                            <th style="width:100px;">Tgl Payment</th>
                            <th style="width:100px;">Account</th>
                            <th style="width:100px;">Bank</th>                                                                                                           
                            <th style="width:50px;">Cara Bayar</th>
                            <th style="width:50px;">Currency</th>
                            <th style="width:50px;">Nominal Pay</th>                                                      
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_kbon = $row['ttl_bayar'];
			$tot_outstanding += $row['sum'];
            $tot_amount = $tot_kbon - $tot_outstanding;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['list_payment_id'].'">'.$row['list_payment_id'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_list_payment'].'">'.date("d-M-Y",strtotime($row['tgl_list_payment'])).'</td>
                            <td style="width:100px;" value="'.$row['account'].'">'.$row['account'].'</td> 
                            <td style="width:100px;" value="'.$row['bank'].'">'.$row['bank'].'</td> 
                            <td style="width:100px;" value="'.$row['cara_bayar'].'">'.$row['cara_bayar'].'</td> 
                            <td style="width:100px;" value="'.$row['valuta_bayar'].'">'.$row['valuta_bayar'].'</td>                                                                                                               
                            <td style="width:50px;text-align: right;" value="'.$row['nominal'].'">'.number_format($row['nominal'],2).'</td>
                                                       
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
            
        <td>
            Tax (Pph)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($pph,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Pay 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tot_outstanding,2).'
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
           '.number_format($tot_amount,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>