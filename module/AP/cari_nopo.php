<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$nama_supp = $_POST['nama_supp'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));


  $sql = mysqli_query($conn1,"select a.id,a.id_draft,a.pono,podate,c.Supplier,tipe_com from po_header a
left join po_header_draft b on b.id = a.id_draft
left join mastersupplier c on c.id_supplier = a.id_supplier where tipe_com = 'BUYER' and podate BETWEEN '$start_date' and '$end_date' and supplier = '$nama_supp'");

$table = '';

			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
                        <td style="width:10px;"><input type="checkbox" id="pilih_sj" name="pilih_sj" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? onclick="tambah_sj('.$row['id'].')"></td>    
                        <td style="" value="'.$row['pono'].'">'.$row['pono'].'</td>
                        <td style="width:100px;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>
                        <td style="" value="'.$row['Supplier'].'">'.$row['Supplier'].'</td>
                        <td style="" value="'.$row['tipe_com'].'">'.$row['tipe_com'].'</td>  
                        <td style="" value="'.$row['id'].'">'.$row['id'].'</td>                   
                             
                       </tr>';
        }

echo $table;

mysqli_close($conn2);
?>