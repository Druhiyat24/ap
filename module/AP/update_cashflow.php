<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$status = $_POST['status'];
$id_cf = $_POST['id_cf'];
$update_user = $_POST['update_user'];
$active_date = date("Y-m-d H:i:s");


if($status == "Active"){
$sql = "update tbl_master_cashflow set status = 'Deactive', deactive_by = '$update_user', deactive_date = '$active_date' where id = '$id_cf'";
$execute = mysqli_query($conn2,$sql);
}else{
$sql = "update tbl_master_cashflow set status = 'Active', active_by = '$update_user', active_date = '$active_date' where id = '$id_cf'";
$execute = mysqli_query($conn2,$sql);		
}


if($execute ){

}else{
	die('Error: ' . mysqli_error());		
}

$sql = mysqli_query($conn1,"select a.id,b.type,b.eng_name as grup,a.eng_name as cf_name,a.status from tbl_master_cashflow a inner join tbl_master_group_cf b on b.id=a.id_group");

while ($row = mysqli_fetch_assoc($sql)) {
			 $status = $row['status']; 
            $table .= '<tr style="font-size:12px;text-align:center;">
            <td style="display: none" value = "'.$row['id'].'">'.$row['id'].'</td>
            <td value = "'.$row['type'].'">'.$row['type'].'</td>
            <td value = "'.$row['grup'].'">'.$row['grup'].'</td>
            <td value = "'.$row['cf_name'].'">'.$row['cf_name'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>';

            $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$update_user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];
            $app = $rs['ap_apprv_lp'];

            $table .='<td width="100px;">';
            if($status == 'Deactive' and $group != 'STAFF' and $fin == '1'){
                $table .='<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-success"> Active </button>';
            }elseif($status == 'Deactive' and $fin == '1'){
                $table .='-';
            }elseif($status == 'Active' and $group != 'STAFF' and $fin == '1'){
                $table .='<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-danger">Deactive</button>';                
            }elseif($status == 'Active' and $fin == '1') {
                $table .= '-';                
            } else {
                $table .='';                
            }                                     
            $table .='</td>
            </tr>';
        }
echo $table;
mysqli_close($conn2);

?>