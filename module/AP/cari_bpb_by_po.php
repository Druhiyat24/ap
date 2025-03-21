<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id_po = $_POST['id_po'];

 $sql_po = mysqli_query($conn2,"select pono from po_header where id = '$id_po'");
 $row_po = mysqli_fetch_array($sql_po);
 $nopo = $row_po['pono'];

 $sql = mysqli_query($conn2,"select id,pono,bpbno_int,bpbdate,id_item,id_jo,goods_code,itemdesc,round(qty - coalesce(qty_dn,0),2) qty,price,unit from (select a.id,pono,bpbno_int,bpbdate,a.id_item,a.id_jo,goods_code,itemdesc,qty,price,unit FROM bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN masteritem c on c.id_item = a.id_item where pono = '$nopo' GROUP BY a.id) a LEFT JOIN (select id_bpb,sum(qty) qty_dn from req_dn a INNER JOIN req_dn_h b on b.no_req = a.no_req where no_po = '$nopo' and id_bpb != '' and b.status != 'Cancel' GROUP BY id_bpb) b on b.id_bpb = a.id where round(qty - coalesce(qty_dn,0),2) > 0");

$table = '';

			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>       
                        <td style="" value="'.$row['id'].'">'.$row['id'].'</td>
                        <td style="" value="'.$row['pono'].'">'.$row['pono'].'</td>
                        <td style="" value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
                        <td style="width:100px;" value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
                        <td style="" value="'.$row['itemdesc'].'">'.$row['itemdesc'].'</td>
                        <td style="" value="'.$row['unit'].'">'.$row['unit'].'</td>
                        <td style="" value="'.$row['qty'].'">'.$row['qty'].'</td>
                        <td style="" ><input type="text" class="form-control" id="mdl_disc" name="mdl_qty" style="width: 100px; text-align: center" oninput="mdl_input_qty()" readonly autocomplete="off"></td>
                        <td style="" value="'.$row['price'].'">'.$row['price'].'</td>
                         <td style="" ><input type="text" class="form-control" id="mdl_disc" name="mdl_price" style="width: 100px; text-align: center" oninput="mdl_input_price()" readonly autocomplete="off"></td>
                        <td style="width:10px;"><input type="checkbox" id="mdl_cek_sj" name="mdl_cek_sj" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? onclick="modal_sum_total_sj()"></td>
                        <td hidden> <input type="text" class="form-control" id="mdl_qty_h" name="mdl_qty_h" value = "'.$row['qty'].'" style="width: 80%; text-align: center"  readonly autocomplete="off"></td>
                        <td hidden> <input type="text" class="form-control" id="mdl_price_h" name="mdl_price_h" value = "'.$row['price'].'" style="width: 80%; text-align: center"  readonly autocomplete="off"></td>
                        <td hidden value="'.$row['id_item'].'"></td>
                        <td hidden value="'.$row['id_jo'].'"></td>       
                             
                       </tr>';
        }

echo $table;

mysqli_close($conn2);
?>