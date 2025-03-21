<?php
include '../../conn/conn.php';
$tot_inv = 0; 
$no_document = isset($_POST['no_document']) ? $_POST['no_document']: null;

$sql = mysqli_query($conn2,"select no_transfer,tgl_transfer,no_bpb,tgl_bpb,nama_supp, total,created_at,CASE
    WHEN status like '%Approved%' THEN 'Accept From Warehouse To Accounting'
        WHEN status = 'Transfer' THEN 'Transfer From Warehouse To Accounting'
        WHEN status = 'Cancel' THEN 'Cancel From Warehouse To Accounting'
END as keterangan from ir_trans_bpb where no_transfer = '$no_document' GROUP BY id");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No BPB</th>
                            <th style="width:100px;">BBP Date</th>
                            <th style="width:100px;">Amount</th>
                            <th style="width:100px;">Keterangan</th>                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_inv += $row['total'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td> 
                            <td style="width:100px;" value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>                            
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <th width="70%">
            
        </th>
            
        <th>
            Grand Total
        </th>
<th style="width:1%">:</th>
        <th style="text-align:right">
            '.number_format($tot_inv,2).'
        </th>       
    </tr>     
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Total PO: '.number_format($tot_po,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>DP Amount: '.number_format($dp,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($sum_bal,2).'</h6></div>';
?>