<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_dok = isset($_POST['no_dok']) ? $_POST['no_dok']: null;
$jenis = isset($_POST['jenis']) ? $_POST['jenis']: null;
$no_inv = isset($_POST['no_inv']) ? $_POST['no_inv']: null;
$tgl_inv = isset($_POST['tgl_inv']) ? $_POST['tgl_inv']: null;
$no_fak = isset($_POST['no_fak']) ? $_POST['no_fak']: null;
$tgl_fak = isset($_POST['tgl_fak']) ? $_POST['tgl_fak']: null;
if ($jenis == 'FAK') {
    $sql = mysqli_query($conn1,"select * from (select a.no_bpb,a.tgl_bpb,a.supplier,a.curr,sum(a.qty) qty,a.uom,ROUND(SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))),2) total from bpb_new a inner join bpb_faktur_inv b on b.no_bpb = a.no_bpb where b.no_dok = '$no_dok' GROUP BY a.no_bpb) a inner join (select bpbno_int,invno from bpb where left(bpbno_int,1) in ('G','W') GROUP BY bpbno_int) b on b.bpbno_int = a.no_bpb"); 
}else{
    $sql = mysqli_query($conn1,"select * from (select a.no_bpb,a.tgl_bpb,a.supplier,a.curr,sum(a.qty) qty,a.uom,ROUND(SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))),2) total from bpb_new a inner join bpb_faktur_inv b on b.no_bpb = a.no_bpb where b.no_dok = '$no_dok' GROUP BY a.no_bpb) a inner join (select bpbno_int,invno from bpb where left(bpbno_int,1) in ('G','W') GROUP BY bpbno_int) b on b.bpbno_int = a.no_bpb");	
}

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="">No BPB</th>
                            <th style="">Tgl BPB</th>
                            <th style="">No SJ</th>
                            <th style="">Currency</th> 
                            <th style="">Qty</th>
                            <th style="">Unit</th> 
                            <th style="">Total</th>                                                                                   
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			// $qty = $row['qty'];
			// $price = $row['price'];
			// $sub += $qty * $price;
			// $total = $sub;
            $table .= '<tr>
                            <td style="" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>  
                            <td style="" value="'.$row['invno'].'">'.$row['invno'].'</td>                            
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td> 
                            <td style="text-align: right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td> 
                            <td style="" value="'.$row['uom'].'">'.$row['uom'].'</td>                          
                            <td style="text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

// echo '<table width="100%" border="0" style="font-size:14px; font-weight: bold;">
//     <tr>
//         <td width="70%">
            
//         </td>
            
//         <td >
//             Total Return
//         </td>
//         <td style="width:1%">:</td>
//         <td style="text-align:right">
//             '.number_format($total,2).'
//         </td>
// </tr>   
    
// </table>';

// <th style="width:17%;">No BPB</th>
// <th style="width:10%;">Tgl BPB</th>
// <th style="width:15%;">No Invoice</th>
// <th style="width:10%;">Tgl Invoice</th>
// <th style="width:15%;">No Faktur</th>
// <th style="width:10%;">Tgl Faktur</th> 
// <th style="width:8%;">Currency</th> 
// <th style="width:15%;">Total</th> 
// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>