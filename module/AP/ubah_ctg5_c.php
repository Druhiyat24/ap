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
    $sql = mysqli_query($conn1,"select a.id_ctg5, CONCAT(a.id_ctg5,'.') as no_coa,a.ind_name as name5,CONCAT(a.id_ctg5,' - ',a.ind_name) as name,b.id_ctg3,b.ind_name as name3,c.id_ctg4,c.ind_name as name4 from master_coa_ctg5 a inner join master_coa_ctg3 b on b.id_ctg3 = a.id_ctg3 inner join master_coa_ctg4 c on c.id_ctg4 = a.id_ctg4 group by a.id");
}else {
    $sql = mysqli_query($conn1,"select a.id_ctg5, CONCAT(a.id_ctg5,'.') as no_coa,a.ind_name as name5,CONCAT(a.id_ctg5,' - ',a.ind_name) as name,b.id_ctg3,b.ind_name as name3,c.id_ctg4,c.ind_name as name4 from master_coa_ctg5 a inner join master_coa_ctg3 b on b.id_ctg3 = a.id_ctg3 inner join master_coa_ctg4 c on c.id_ctg4 = a.id_ctg4 where a.id_ctg2 = '$id' group by a.id");
}

    $jsArray2 = "var prdName = new Array();\n";
  
    echo'<option value="" disabled selected="true">Select Category 5</option>';
    while($r=mysqli_fetch_array($sql)){
        $isSelected = ' selected="selected"';
        echo '<option name="nama_ctg5" value="'.$r['id_ctg5'].'">'.$r['name5'].'</option>';    
        $jsArray2 .= "prdName2['" . $row['id_ctg5'] . "'] = {nama_ctg3:'" . addslashes($row['name3']) . "',nama_ctg3_h:'".addslashes($row['id_ctg3'])."',nama_ctg4:'" . addslashes($row['name4']) . "',nama_ctg4_h:'".addslashes($row['id_ctg4'])."',no_coa:'".addslashes($row['no_coa'])."'};\n";
        ?>
<!--         <option  value="<?php echo $r['id_ctg5'] ?>"><?php echo $r['name'] ?></option>
 -->        <?php        
}


// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>