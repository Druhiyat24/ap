<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$total = 0;

 $sql = mysqli_query($conn1,"select id_bpb, no_po, no_bpb, tgl_bpb, a.id_jo, id_item, itemdesc,qty_tagih, price_tagih,unit, styleno from req_dn_po_detail_temp a LEFT JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) b on b.id_jo=a.id_jo where created_by = '$create_user'");

$table = '';

			while ($row = mysqli_fetch_assoc($sql)) {
                $price = $row['price_tagih'];
                $qty = $row['qty_tagih'];
                $total = $price * $qty;
			
            // Kolom yg datanya berasal dari PO/BPB (No PO..Total) dikunci `readonly`:
            // ini data hasil pilihan di modal "Add Data", bukan ketikan manual, jadi
            // tidak boleh diubah lagi di sini. .form-control TETAP dipakai (bukan
            // diganti <span>/teks polos) supaya semua JS yang membaca .val() dari
            // sel ini (hitungRow, deleteRow, Save) tidak perlu diubah sama sekali —
            // readonly cukup memblokir pengetikan; tampilannya dibuat mirip teks
            // polos lewat CSS (lihat #mytable tbody .form-control[readonly] di
            // create_request_dn.php). Baris kosong dari Add Row / Interject Row
            // TIDAK lewat sini (dibuat oleh template JS-nya sendiri) jadi tetap
            // bisa diisi manual seperti biasa.
            $table .= '<tr>
                        <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" value="'.$row['no_po'].'" placeholder="" autocomplete="off" readonly tabindex="-1"></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" value="'.$row['no_bpb'].'" placeholder="" autocomplete="off" readonly tabindex="-1"></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" value="'.$row['itemdesc'].'" placeholder="" autocomplete="off" readonly tabindex="-1"></td>
                        <td><input style="text-align: right;font-size: 12px;" type="number" min="1" value="'.$row['qty_tagih'].'" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off" readonly tabindex="-1"></td>
                        <td><input style="text-align: right;font-size: 12px;" type="number" min="1" value="'.$row['price_tagih'].'" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off" readonly tabindex="-1"></td>
                        <td><input style="text-align: right;font-size: 12px;" type="text" class="form-control" id="tot_row" name="tot_row" value="'.$total.'" placeholder="" autocomplete="off" readonly tabindex="-1"></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value=""></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value=""></td>
                        <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value="'.$row['styleno'].'"></td>
                        <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
                        <td hidden value="'.$row['id_bpb'].'"></td>
                        <td hidden value="'.$row['tgl_bpb'].'"></td>
                        <td hidden value="'.$row['id_jo'].'"></td>
                        <td hidden value="'.$row['id_item'].'"></td>
                        <td hidden value="'.$row['unit'].'"></td>
                        </tr>';
        }



echo $table;

mysqli_close($conn2);
?>