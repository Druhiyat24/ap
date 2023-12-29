<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_mj = isset($_POST['no_mj']) ? $_POST['no_mj']: null;

    $sql = mysqli_query($conn1,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,concat(c.no_coa,' ', c.nama_coa) as coa , d.cc_name,a.no_coa,a.no_costcenter, a.no_reff, a.reff_date,a.buyer,a.no_ws,a.curr,a.rate,a.debit,a.credit,a.credit_idr,a.debit_idr,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj left join mastercoa_v2 c on c.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcenter where a.no_mj = '$no_mj' AND a.no_coa != ''");

    $sql3 = mysqli_query($conn1,"select sum(debit) as debit,sum(credit) as credit,sum(credit_idr) as credit_idr,sum(debit_idr) as debit_idr from tbl_memorial_journal where no_mj = '$no_mj'");
    $row3 = mysqli_fetch_assoc($sql3);
    $debit = $row3['debit'];
    $credit = $row3['credit'];
    $debit_idr = $row3['debit_idr'];
    $credit_idr = $row3['credit_idr'];


    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:14%;">Coa</th>
                            <th style="width:13%;">Cost Center</th>
                            <th style="width:11%;">Reff</th>                                                                                
                            <th style="width:12%;">Reff Date</th>
                            <th style="width:12%;">Buyer</th>                                                                                
                            <th style="width:11%;">WS</th>
                            <th style="width:7%;">Curr</th>
                            <th style="width:10%;">Debit</th> 
                            <th style="width:10%;">Credit</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['coa'].'">'.$row['coa'].'</td>
                            <td style="" value="'.$row['cc_name'].'">'.$row['cc_name'].'</td>
                            <td style="" value="'.$row['no_reff'].'">'.$row['no_reff'].'</td>
                            <td value="'.$row['reff_date'].'">'.date("d-M-Y",strtotime($row['reff_date'])).'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td>
                            <td style="text-align: right;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td>
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
            Total Debit
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($debit,2).'
        </td>       
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Credit
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($credit,2).'
        </td>       
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Debit IDR
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($debit_idr,2).'
        </td>       
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Credit IDR
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($credit_idr,2).'
        </td>       
    </tr>   
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>