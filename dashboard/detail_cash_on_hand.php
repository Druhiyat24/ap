<?php
include '../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$tahun = date("Y"); 
$filter = isset($_POST['filter']) ? $_POST['filter']: null;

$sql = mysqli_query($conn1,"select nama_coa,sum($filter) total from b_trial_balance_$tahun where no_coa IN ('1.01.01','1.01.02','1.01.03') GROUP BY no_coa order by nama_coa asc"); 


$table = '<table id="mytdmodal" class="table table-striped" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">';

while ($row = mysqli_fetch_assoc($sql)) {
    $total += $row['total'];
    $table .= '<tr>
    <td><i class="fa fa-dot-circle-o" aria-hidden="true"></i></td>                       
    <td style="text-align: left" value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
    <td style="text-align: right;" value="'.$row['total'].'">Rp. '.number_format($row['total'],2).'</td> 
    </tr>';
}
 $table .= '<tr>
    <th colspan = "2">TOTAL</th>      
    <th style="text-align: right;" value="'.$total.'">Rp. '.number_format($total,2).'</th> 
    </tr>';
$table .= '</table>';



echo $table;


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>