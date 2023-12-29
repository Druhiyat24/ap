<?php
include '../../conn/conn.php';
$tot_kbon = 0;
$tot_outstanding = 0;
$tot_amount = 0;
$total = ''; 
$id = isset($_POST['id']) ? $_POST['id']: null;
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql2 = mysqli_query($conn2,"select * from pengajuan_paymentdp where id = '$id' and no_payment = '$no_kbon' group by id ");	
$rows = mysqli_fetch_assoc($sql2);
$sql = mysqli_query($conn2,"select no_payment, tgl_payment, no_kbon, tgl_kbon, nama_supp, SUM(total_kbon) as total_kbon, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment_dp where no_payment = '$no_kbon' group by no_kbon");  

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                    <hr/>
                            <p style="font-size: 13px;">
                             <b>Message :</b> '.$rows['pesan'].' </p>
                        <hr/>
                       <tr>                       
                            <th style="width:100px;">No Kontrabon</th>
                            <th style="width:100px;">Tgl Kontrabon</th>
                            <th style="width:100px;display:none;">TOP</th>
                            <th style="width:100px;">Tgl Jatuh Tempo</th>                                                                                                           
                            <th style="width:50px;">Total DP</th>
                            <th style="width:50px;">Amount DP</th>                                                         
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
                            <td style="width:100px;display:none;" value="'.$row['top'].'">'.$row['top'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>                                                                                                                
                            <td style="width:50px;text-align: right;" value="'.$row['total_kbon'].'">'.number_format($row['total_kbon'],2).'</td>                           
                            <td style="width:50px;text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>                           
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
            Total Kontrabon DP
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
            Total Amount DP
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
           '.number_format($tot_amount,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>