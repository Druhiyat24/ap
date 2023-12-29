<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
if(strpos($no_bpb, '/IN/') !== false) {
if(strpos($no_bpb, 'GEN/') !== false) {
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.kpno from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");    
}else{
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, act_costing.kpno from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item INNER JOIN po_item on po_item.id = bpb.id_po_item INNER JOIN jo on jo.id = bpb.id_jo INNER JOIN jo_det on jo_det.id_jo = jo.id INNER JOIN so on so.id = jo_det.id_so INNER JOIN act_costing on act_costing.id = so.id_cost where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");
}
}else{
  if(strpos($no_bpb, 'GEN/') !== false) {
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.kpno from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");    
}else{
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, act_costing.kpno from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item INNER JOIN jo on jo.id = bpb.id_jo INNER JOIN jo_det on jo_det.id_jo = jo.id INNER JOIN so on so.id = jo_det.id_so INNER JOIN act_costing on act_costing.id = so.id_cost where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");
}  
}
	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">WS #</th>
                            <th style="width:100px;">Material</th>
                            <th style="width:50px;">Qty</th>                                                                                
                            <th style="width:50px;">UOM</th>
                            <th style="width:50px;">Price</th>
                            <th style="width:50px;">SubTotal</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$qty = $row['qty'];
			$price = $row['price'];
            if(strpos($price, '.') !== false){
                $prc = number_format($row['price'],4);
            }else{
                $prc = number_format($row['price'],2);
            }
			$tax = $row['tax'];
            $kali = $qty * $price;
			$sub += $qty * $price;
			$tax = $sub * ($tax / 100);
			$total = $sub + $tax;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['kpno'].'">'.$row['kpno'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['itemdesc'].' ('.$row['color'].' '.$row['size'].')">'.$row['itemdesc'].' ('.$row['color'].' '.$row['size'].')</td>
                            <td style="width:50px;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>                            
                            <td style="width:50px;" value="'.$row['unit'].'">'.$row['unit'].'</td>                            
                            <td style="width:50px;text-align: right;" value="'.$row['price'].'">'.$prc.'</td>
                            <td style="width:50px;text-align: right;" value="'.$kali.'">'.number_format($kali,2).'</td> 
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
            Subtotal
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($sub,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (PPn)
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tax,2).'
        </td>       
    </tr>      

   <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>