<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$status = $_POST['status'];
$id_cf = $_POST['id_cf'];
$update_user = $_POST['update_user'];
$active_date = date("Y-m-d H:i:s");


if($status == "Active"){
$sql = "update mastercoa_v3 set status = 'Deactive' where no_coa = '$id_cf'";
$execute = mysqli_query($conn2,$sql);

$sql = "update mastercoa_v2 set status = 'Deactive' where no_coa = '$id_cf'";
$execute = mysqli_query($conn2,$sql);
}else{
$sql = "update mastercoa_v3 set status = 'Active' where no_coa = '$id_cf'";
$execute = mysqli_query($conn2,$sql);

$sql = "update mastercoa_v2 set status = 'Active' where no_coa = '$id_cf'";
$execute = mysqli_query($conn2,$sql);		
}


if($execute ){

}else{
	die('Error: ' . mysqli_error());		
}

$sql = mysqli_query($conn1,"select a.no_coa,a.nama_coa,b.ind_name as name2,c.ind_name as name5,a.status from mastercoa_v3 a inner join master_coa_ctg2 b on b.id_ctg2 = a.id_ctg2 inner join master_coa_ctg5 c on c.id_ctg5 = a.id_ctg5");

while ($row = mysqli_fetch_assoc($sql)) {
			 $status = $row['status']; 
            $table .= '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td value = "'.$row['name2'].'">'.$row['name2'].'</td>
            <td value = "'.$row['name5'].'">'.$row['name5'].'</td>
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