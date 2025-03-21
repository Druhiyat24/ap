<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$notrf = $_POST['notrf'];

 $sql = mysqli_query($conn1,"select * from (select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Fin To Acc') a INNER JOIN (select nama_trans,no_trans,tgl_trans,doc_number doc_num from ir_trans_invoice_supp where status = 'post' and nama_trans = 'TFTA' GROUP BY doc_number) b on b.doc_num = a.doc_number where no_trans = '$notrf'");

$table = '';

         while ($row = mysqli_fetch_assoc($sql)) {
         
            $table .= '<tr>
                           <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) checked></td>                        
                           <td style="display:none;" value="'.$row['no_trans'].'">'.$row['no_trans'].'</td>
                            <td style="display:none;" value="'.$row['tgl_trans'].'">'.date("d-M-Y",strtotime($row['tgl_trans'])).'</td>
                            <td style="width:50px;" value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
                            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style ="text-align: right;" class="dt_total" style="width:100px;" value="'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
                            <td style="" value = "'.$row['updated_at'].'">'.$row['updated_at'].'</td>                    
                        </tr>';
        }

echo $table;

mysqli_close($conn2);
?>