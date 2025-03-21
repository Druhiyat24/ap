<?php
include '../../conn/conn.php';
$tot_inv = 0; 
$no_inv = isset($_POST['no_inv']) ? $_POST['no_inv']: null;
$supp = isset($_POST['supp']) ? $_POST['supp']: null;

$sql = mysqli_query($conn2,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, masterpterms.kode_pterms, bpb.curr, bpb.confirm_by,DATE_FORMAT(bpb.confirm_date,'%Y-%m-%d') confirm_date,round(sum((((IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject))) * bpb.price) + (((IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject))) * bpb.price) * (po_header.tax /100)))),2) as total,round((((IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject))) * bpb.price) * (po_header.tax /100)),2) ppn,round(((IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject))) * bpb.price),2) dpp, po_header.podate, po_header_draft.tipe_com
                from bpb 
                INNER JOIN po_header on po_header.pono = bpb.pono 
                left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier 
                inner join masterpterms on masterpterms.id = po_header.id_terms 
                where bpb.upt_no_inv = '$no_inv' and mastersupplier.supplier = '$supp' group by bpb.bpbno_int");	

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No BPB</th>
                            <th style="width:100px;">BPB Date</th>
                            <th style="width:100px;">DPP</th>
                            <th style="width:100px;">PPN</th>
                            <th style="width:100px;">Total</th>                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$tot_inv += $row['total'];
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>        
                            <td style="width:50px;text-align: right;" value="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>        
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                            
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