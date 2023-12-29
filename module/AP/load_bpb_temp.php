<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$user = $_POST['user'];
$table = '';

 $sql = mysqli_query($conn1,"select no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input from tbl_bpb_temp where user_input = '$user' GROUP BY no_bpb");

			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" class="select_item" value="" checked disabled></td>    
                        <td style="" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                        <td style="" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                        <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                        <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        <td style="" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                    
                             
                       </tr>';
        }


echo $table;

mysqli_close($conn2);
?>