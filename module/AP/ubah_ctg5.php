<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$id = isset($_POST['id']) ? $_POST['id']: null;
if ($id == 'ALL') {
    $sql = mysqli_query($conn1,"select id_ctg5,ind_name, CONCAT(id_ctg5,' - ',ind_name) as name from master_coa_ctg5 group by id");
}else {
    $sql = mysqli_query($conn1,"select id_ctg5,ind_name, CONCAT(id_ctg5,' - ',ind_name) as name from master_coa_ctg5 where id_ctg2 = '$id' group by id");
}
  
    echo'<option value="ALL"selected="selected"">ALL</option>';
    while($r=mysqli_fetch_array($sql)){
        $isSelected = ' selected="selected"';
        echo '<option value="'.$r['id_ctg5'].'"'.$isSelected.'">'.$r['name'].'</option>'; 
        ?>
<!--         <option  value="<?php echo $r['id_ctg5'] ?>"><?php echo $r['name'] ?></option>
 -->        <?php        
}


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>