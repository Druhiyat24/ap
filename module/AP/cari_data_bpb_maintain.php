<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$nomtn = $_POST['nomtn'];

 $sql = mysqli_query($conn1,"select a.no_maintain, a.tgl_maintain, nama_supp, no_bpb, tgl_bpb, b.keterangan from maintain_bpb_h a INNER JOIN maintain_bpb_det b on b.no_maintain  = a.no_maintain where a.no_maintain = '$nomtn' GROUP BY b.id");

$table = '';

         while ($row = mysqli_fetch_assoc($sql)) {
         
            $table .= '<tr>
                           <td style="display:none;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) checked></td>                        
                           <td style="display:none;" value="'.$row['no_maintain'].'">'.$row['no_maintain'].'</td>
                            <td style="display:none;" value="'.$row['tgl_maintain'].'">'.date("d-M-Y",strtotime($row['tgl_maintain'])).'</td>
                            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td style="" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>                    
                        </tr>';
        }

echo $table;

mysqli_close($conn2);
?>