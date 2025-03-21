<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;

$sql = mysqli_query($conn1,"select * from (select DISTINCT no_invoice,nama_supp from ir_invoice_supp a INNER JOIN ir_invoice_supp_h b on b.doc_number = a.doc_number where b.status != 'Cancel') a left join (select DISTINCT upt_no_inv from bpb_new where upt_no_inv is not null
                    ) b on b.upt_no_inv = a.no_invoice where upt_no_inv is null and nama_supp = '$nama_supp'");
  
    echo'<option value="-" disabled selected="true">Select Invoice</option>';
    while($r=mysqli_fetch_array($sql)){
        ?>
        <option value="<?php echo $r['no_invoice'] ?>"><?php echo $r['no_invoice'] ?></option>
        <?php        
}

?>