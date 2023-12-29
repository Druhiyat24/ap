<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$status = $_POST['status'];
$id_cf = $_POST['id_cf'];
$update_user = $_POST['update_user'];
$active_date = date("Y-m-d H:i:s");


if($status == "Active"){
$sql = "update master_pc set status = 'Deactive' where id_pc = '$id_cf'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "update master_pc_child set status = 'Deactive' where id_pc = '$id_cf'";
$execute2 = mysqli_query($conn2,$sql2);
}else{
$sql = "update master_pc set status = 'Active' where id_pc = '$id_cf'";
$execute = mysqli_query($conn2,$sql);	

$sql2 = "update master_pc_child set status = 'Active' where id_pc = '$id_cf'";
$execute2 = mysqli_query($conn2,$sql2); 	
}


if($execute ){

}else{
	die('Error: ' . mysqli_error());		
}
$no = 0;
$sql = mysqli_query($conn1,"select id_pc,nama_pc, IF(keterangan = '','-',keterangan) as keterangan,status from master_pc");

while ($row = mysqli_fetch_assoc($sql)) {
             $no++;
			 $status = $row['status']; 
            $table .= '<tr style="font-size:12px;text-align:center;">
            <td style="display: none" value = "'.$row['id_pc'].'">'.$row['id_pc'].'</td>
            <td value = "'.$no.'">'.$no.'</td>
            <td value = "'.$row['nama_pc'].'">'.$row['nama_pc'].'</td>
            <td value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>
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