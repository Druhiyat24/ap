<?php
include '../../conn/conn.php';
$tot_lp = 0;
$tot_pay = 0;
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, sum(ttl_bayar) as ttl_bayar, cara_bayar, account, bank, valuta_bayar, SUM(nominal) as nominal, SUM(nominal_fgn) as nominal_fgn, rate, (ttl_bayar - SUM(nominal)) as sisa,status,keterangan from payment_ftrdp where payment_ftr_id = '$no_kbon' GROUP BY list_payment_id");    

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No List payment</th>
                            <th style="width:100px;">List Payment Date</th>
                            <th style="width:100px;">Total list Payment</th>
                            <th style="width:100px;">Rate</th>
                            <th style="width:50px;">Nominal Pay</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
                $val = $row['valuta_bayar'];
                $val2 = $row['valuta_ftr']; 
            if ($val == 'IDR') {
            $nom= $row['nominal']; 
            $rate = $row['rate'];
           }else{
            $nom= $row['nominal_fgn'];
            $rate = '1';
           }
           $tot_lp += $row['ttl_bayar'];
           $tot_pay += $nom;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['list_payment_id'].'">'.$row['list_payment_id'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_list_payment'].'">'.date("d-M-Y",strtotime($row['tgl_list_payment'])).'</td>                                                                                                            
                            <td style="" value = "'.$row['ttl_bayar'].'">'.$row['valuta_ftr'].' '.number_format($row['ttl_bayar'],2).'</td>
                            <td style="" value = "'.$rate.'">'.number_format($rate,2).'</td>
            <td style="text-align:right;" value = "'.$nom.'">'.$row['valuta_bayar'].' '.number_format($nom,2).'</td>                           
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
            Total List Payment
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.$val2.' '.number_format($tot_lp,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Rate 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($rate,2).'
        </td>       
    </tr>      

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total Payment
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
           '.$val.' '.number_format($tot_pay,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>