<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$notrf = $_POST['notrf'];

 $sql = mysqli_query($conn1,"select no_transfer,tgl_transfer,no_bpb,tgl_bpb,nama_supp, curr,total,created_at,created_by from ir_trans_bpb where no_transfer = '$notrf' GROUP BY id");

$table = '';

         while ($row = mysqli_fetch_assoc($sql)) {
         
            $table .= '<tr>
                           <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) checked></td>                        
                           <td style="display:none;" value="'.$row['no_transfer'].'">'.$row['no_transfer'].'</td>
                            <td style="display:none;" value="'.$row['tgl_transfer'].'">'.date("d-M-Y",strtotime($row['tgl_transfer'])).'</td>
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td class="dt_total" style="width:100px;" value="'.$row['created_by'].'">'.$row['created_by'].'</td>
                            <td style="" value = "'.$row['created_at'].'">'.$row['created_at'].'</td>                    
                        </tr>';
        }

echo $table;

mysqli_close($conn2);
?>