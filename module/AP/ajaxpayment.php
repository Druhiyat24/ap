<?php
include '../../conn/conn.php';
$sub = '';
$tax = '';
$total = ''; 
$no_po = isset($_POST['no_po']) ? $_POST['no_po']: null;
if(strpos($no_po, 'PO/') !== false) {
$sql = mysql_query("select po_header.pono as no_po, po_header.podate as podate, mastersupplier.Supplier as supplier, masteritem.itemdesc as item_desc, po_item.qty as qty, po_item.price as price, po_header.tax as tax, po_item.curr as matauang, po_header.app as app, po_item.cancel as cancel, masterpterms.kode_pterms, po_item.unit as unit
from po_header 
inner join po_item on po_item.id_po = po_header.id 
inner join masteritem on masteritem.id_item = po_item.id_gen
inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
inner join masterpterms on masterpterms.id = po_header.id_terms
where po_header.app = 'A' and po_header.pono = '$no_po' and po_item.cancel != 'Y' and po_header.jenis = 'N' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y'",$conn2);	
}else{
$sql = mysql_query("select po_header.pono, jo.jo_no, po_header.id_terms, masterpterms.kode_pterms, 
concat(mastergroup.nama_group, ' ', mastersubgroup.nama_sub_group, ' ', mastertype2.nama_type, ' ', mastercontents.nama_contents, ' ',masterwidth.nama_width, ' ', masterlength.nama_length, ' ', masterweight.nama_weight, ' ', mastercolor.nama_color, ' ', masterdesc.nama_desc,' ', masterdesc.add_info) as item_desc, 
po_item.qty as qty, po_item.unit as unit, po_item.price as price, po_item.curr, po_header.tax as tax
from po_header
inner join masterpterms on masterpterms.id = po_header.id_terms
inner join po_item on po_item.id_po = po_header.id
inner join jo on jo.id = po_item.id_jo
inner join mastergroup 
inner join mastersubgroup on mastersubgroup.id_group = mastergroup.id
inner join mastertype2 on mastertype2.id_sub_group = mastersubgroup.id
inner join mastercontents on mastercontents.id_type = mastertype2.id
inner join masterwidth on masterwidth.id_contents = mastercontents.id
inner join masterlength on masterlength.id_width = masterwidth.id
inner join masterweight on masterweight.id_length = masterlength.id
inner join mastercolor on mastercolor.id_weight = masterweight.id
inner join masterdesc on masterdesc.id_color = mastercolor.id
and po_item.id_gen = masterdesc.id
where po_header.app = 'A' and po_header.pono = '$no_po' and po_item.cancel = 'N' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y'",$conn2);
}
	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">Item Description</th>
                            <th style="width:50px;">Qty</th>                                                                                
                            <th style="width:50px;">UOM</th>
                            <th style="width:50px;">Price</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysql_fetch_assoc($sql)) {
			$qty = $row['qty'];
			$price = $row['price'];
			$tax = $row['tax'];
			$sub += $qty * $price;
			$tax = $sub * ($tax / 100);
			$total = $sub + $tax;
            $table .= '<tr>                                                                                              
                            <td style="width:100px;" value="'.$row['item_desc'].'">'.$row['item_desc'].'</td>
                            <td style="width:50px;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>                            
                            <td style="width:50px;" value="'.$row['unit'].'">'.$row['unit'].'</td>                            
                            <td style="width:50px;text-align: right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>
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
            
        <td >
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>