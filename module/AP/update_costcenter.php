<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$status = $_POST['status'];
$id_cf = $_POST['id_cf'];
$update_user = $_POST['update_user'];
$active_date = date("Y-m-d H:i:s");


if($status == "Active"){
$sql = "update b_master_cc set status = 'Deactive' where no_cc = '$id_cf'";
$execute = mysqli_query($conn2,$sql);

}else{
$sql = "update b_master_cc set status = 'Active' where no_cc = '$id_cf'";
$execute = mysqli_query($conn2,$sql);
		
}


if($execute ){

}else{
	die('Error: ' . mysqli_error());		
}

$sql = mysqli_query($conn1,"select id,no_cc,cc_name,id_cc,group1,group2,group21,profit_center,id_pc,status from b_master_cc where id >= 100");

while ($row = mysqli_fetch_assoc($sql)) {
			 $status = $row['status']; 
            $table .= '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['no_cc'].'">'.$row['no_cc'].'</td>
            <td value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>
            <td value = "'.$row['id_cc'].'">'.$row['id_cc'].'</td>
            <td value = "'.$row['group1'].'">'.$row['group1'].'</td>
            <td value = "'.$row['group2'].'">'.$row['group2'].'</td>
            <td value = "'.$row['group21'].'">'.$row['group21'].'</td>
            <td value = "'.$row['profit_center'].'">'.$row['profit_center'].'</td>
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