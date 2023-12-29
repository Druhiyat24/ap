<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_ob = isset($_POST['no_ob']) ? $_POST['no_ob']: null;
$refdoc = isset($_POST['refdoc']) ? $_POST['refdoc']: null;
if ($refdoc == 'List Payment') {
    $sql = mysqli_query($conn1,"select no_reff, IF(reff_date = '1970-01-01', '-', DATE_FORMAT(reff_date, '%d-%m-%Y')) as reff_date, IF(due_date = '1970-01-01', '-', DATE_FORMAT(due_date, '%d-%m-%Y')) as due_date,dpp,ppn,pph,total,amount from c_petty_cashout_det where no_pco = '$no_ob'");
    $sql2 = mysqli_query($conn1,"select CONCAT(b.no_coa,' ',b.nama_coa) as coa, IF(a.reff_doc = '', '-', a.reff_doc) as reff_doc,IF(a.reff_date = '1970-01-01', '-', DATE_FORMAT(a.reff_date, '%d-%m-%Y')) as reff_date, a.deskripsi, a.t_debit, a.t_credit from c_petty_cashout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa where no_pco = '$no_ob'"); 
    $sql3 = mysqli_query($conn1,"select amount from c_petty_cashout_h where no_pco = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];

}elseif ($refdoc == 'None') {
    $sql = mysqli_query($conn1,"select DISTINCT if(a.no_coa = '-', '-',CONCAT(b.no_coa,' ',b.nama_coa)) as coa,if(a.no_costcntr = '-','-',c.cc_name) as cost_center,if(a.buyer = '','-',a.buyer) as buyer ,if(a.no_ws = '','-',a.no_ws) as no_ws,a.curr,a.debit,a.credit,a.deskripsi from c_petty_cashout_none a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcntr where a.no_pco = '$no_ob'");
    $sql3 = mysqli_query($conn1,"select amount from c_petty_cashout_h where no_pco = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    
}else{
}
  

if($refdoc == 'None'){
    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Coa</th>
                            <th style="width:15%;">Cost Center</th>
                            <th style="width:15%;">Buyer</th>                                                                                
                            <th style="width:15%;">WS</th>
                            <th style="width:8%;">Curr</th>
                            <th style="width:11%;">Debit</th> 
                            <th style="width:11%;">Credit</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['coa'].'">'.$row['coa'].'</td>
                            <td style="" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td>
                            <td style="text-align: right;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td> 
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

}elseif($refdoc == 'List Payment'){

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:10px;">#LP</th>
                             <th style="width:100px;">No LP</th>
                            <th style="width:100px;">LP Date</th>
                            <th style="width:50px;">Duedate</th>                                                                                
                            <th style="width:50px;">DPP</th>
                            <th style="width:50px;">PPN</th>
                            <th style="width:50px;">PPH</th> 
                            <th style="width:50px;">Total</th>
                            <th style="width:50px;">Amount</th>                                                                                    
                        </tr>
                        <tr>                       
                            <th style="width:10px;">#Adj</th>
                             <th style="width:100px;">COA</th>
                            <th style="width:100px;">Reff Doc</th>
                            <th style="width:50px;">Reff Date</th>                                                                                
                            <th colspan = "3"; style="width:50px;">Description</th>
                            <th style="width:50px;">Credit</th>                                                                                    
                            <th style="width:50px;">Debit</th> 
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="width:10px;">#PV</td>
                            <td style="width:100px;" value="'.$row['no_reff'].'">'.$row['no_reff'].'</td>
                            <td style="width:100px;" value="'.$row['reff_date'].'">'.$row['reff_date'].'</td>
                            <td style="width:100px;" value="'.$row['due_date'].'">'.$row['due_date'].'</td>                                                                       
                            <td style="width:50px;text-align: right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td> 
                            <td style="width:50px;text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td> 
                       </tr>
                       ';
                   }

            while ($row2 = mysqli_fetch_assoc($sql2)) {
            $table .= '<tr>                       
                            <td style="width:10px;">#Adj</td>
                            <td style="width:100px;" value="'.$row2['coa'].'">'.$row2['coa'].'</td>
                            <td style="width:100px;" value="'.$row2['reff_doc'].'">'.$row2['reff_doc'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row2['reff_date'].'">'.$row2['reff_date'].'</td>
                            <td colspan = "3"; style="width:100px;" value="'.$row2['deskripsi'].'">'.$row2['deskripsi'].'</td>
                            <td style="width:50px;text-align: right;" value="'.$row2['t_credit'].'">'.number_format($row2['t_credit'],2).'</td> 
                            <td style="width:50px;text-align: right;" value="'.$row2['t_debit'].'">'.number_format($row2['t_debit'],2).'</td>
                       </tr>
                       ';
            $table .= '</tbody>';
        }
            $table .= '</table>';


}else{

}

echo $table;

if ($refdoc == 'List Payment') {
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

}else{
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
}

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>