<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_rvs = $_POST['no_rvs'];

 $sql = mysqli_query($conn1,"select rvs_number, doc_number, doc_date, nama_supp, curr, total, deskripsi from ap_reverse_det where status = 'Y' and rvs_number = '$no_rvs'");

$table = '';

         while ($row = mysqli_fetch_assoc($sql)) {
         
            $table .= '<tr>
                            <td style="" value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="" value="'.$row['doc_date'].'">'.date("d-M-Y",strtotime($row['doc_date'])).'</td>
                            <td style="" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="" value = "'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="text-align: right;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="" value = "'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                    
                        </tr>';
        }

echo $table;

mysqli_close($conn2);
?>