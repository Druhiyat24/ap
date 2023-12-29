<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_cc = isset($_POST['no_cc']) ? $_POST['no_cc']: null;
$startdate = date("Y-m-d",strtotime($_POST['startdate']));
$enddate = date("Y-m-d",strtotime($_POST['enddate']));

    if ($no_cc == 'ALL') {
         $sql = mysqli_query($conn1,"select a.no_ca, a.tgl_ca,b.cc_name,a.amount from c_cash_advances a inner join b_master_cc b on b.no_cc = a.no_costcenter where a.tgl_ca BETWEEN '$startdate' and '$enddate' group by a.no_ca");
    }else{
    $sql = mysqli_query($conn1,"select a.no_ca, a.tgl_ca,b.cc_name,a.amount from c_cash_advances a inner join b_master_cc b on b.no_cc = a.no_costcenter where a.no_ca = '$no_cc' AND  a.tgl_ca BETWEEN '$startdate' and '$enddate' group by a.no_ca");
}
  


    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Coa</th>
                            <th style="width:20%;">Cost Center</th>
                            <th style="width:30%;">Buyer</th>                                                                                
                            <th style="width:25%;">WS</th>                                                                                   
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['no_ca'].'">'.$row['no_ca'].'</td>
                            <td style="" value="'.$row['tgl_ca'].'">'.$row['tgl_ca'].'</td>
                            <td style="" value="'.$row['cc_name'].'">'.$row['cc_name'].'</td>
                            <td style="" value="'.$row['amount'].'">'.$row['amount'].'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';


echo $table;


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>