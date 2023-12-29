<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id_supp = $_POST['id_supp'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

// echo "< -- >";
// echo $no_kbon;


 $sql = mysqli_query($conn1,"        select a.nm_memo,a.tgl_memo,a.jns_trans, ms.supplier supplier,format(sum(round(mdet.biaya,2)),2) biaya,round(sum(mdet.biaya),2) biaya2 from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h where a.status = 'APPROVED' and a.id_supplier = '$id_supp' and tgl_memo >= '$start_date' and tgl_memo <= '$end_date' and a.no_pv = ' ' and mdet.cancel = 'N' || a.status = 'APPROVED' and a.id_supplier = '$id_supp' and tgl_memo >= '$start_date' and tgl_memo <= '$end_date' and a.no_pv is null and mdet.cancel = 'N' GROUP BY nm_memo order by a.id_h desc");

$table = '<div class="tableFix" style="height: 300px;">
<table id="table-memo" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:5%;">Cek</th>
                            <th style="width:20%;">No Memo</th>
                            <th style="width:15%;">Tgl Memo</th> 
                            <th style="width:20%;">Jenis Transaksi</th>
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">Biaya</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>    
                        <td style="" value="'.$row['nm_memo'].'">'.$row['nm_memo'].'</td>
                        <td style="width:100px;" value="'.$row['tgl_memo'].'">'.date("d-M-Y",strtotime($row['tgl_memo'])).'</td>
                        <td style="" value="'.$row['jns_trans'].'">'.$row['jns_trans'].'</td>
                        <td style="" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                        <td style="" value="'.$row['biaya2'].'">'.$row['biaya'].'</td>                    
                             
                       </tr>';
        }
            $table .= '</tbody>';
            $table .= '</table> </div>';

echo $table;

mysqli_close($conn2);
?>