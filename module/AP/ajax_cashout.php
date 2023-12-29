<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_ib = isset($_POST['no_ib']) ? $_POST['no_ib']: null;

    $sql = mysqli_query($conn1,"select if(a.no_coa = '-', '-',CONCAT(b.no_coa,' ',b.nama_coa)) as coa,if(a.no_costcenter = '-','-',c.cc_name) as cost_center,if(a.buyer = '','-',a.buyer) as buyer ,if(a.ws = '','-',a.ws) as no_ws, if(a.req_by = '','-',a.req_by) as req_by,a.curr,a.amount from c_cash_out a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcenter where a.no_co = '$no_ib'");
    $sql3 = mysqli_query($conn1,"select sum(amount) as amount from c_cash_out where no_co = '$no_ib'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];


    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Coa</th>
                            <th style="width:15%;">Cost Center</th>
                            <th style="width:15%;">Buyer</th>                                                                                
                            <th style="width:15%;">WS</th>
                            <th style="width:11%;">Request By</th> 
                            <th style="width:8%;">Curr</th>
                            <th style="width:11%;">Amount</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['coa'].'">'.$row['coa'].'</td>
                            <td style="" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                             <td style="" value="'.$row['req_by'].'">'.$row['req_by'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;



echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Amount
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($amount,2).'
        </td>       
    </tr>   
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>