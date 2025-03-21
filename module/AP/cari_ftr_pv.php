<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$supp = $_POST['supp'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

// echo "< -- >";
// echo $no_kbon;


 $sql = mysqli_query($conn1,"select * from (select no_payment,tgl_payment,no_po,nama_supp, SUM(total_kbon) total from  list_payment_cbd where status != 'Cancel' GROUP BY no_payment
UNION
select no_payment,tgl_payment,no_po,nama_supp, SUM(total_kbon) total from  list_payment_dp where status != 'Cancel' GROUP BY no_payment) a where nama_supp = '$supp' and tgl_payment BETWEEN '$start_date' and '$end_date' ORDER BY no_payment asc");

$table = '<div class="tableFix" style="height: 300px;">
<table id="table-memo" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:5%;">Cek</th>
                            <th style="width:20%;">No FTR</th>
                            <th style="width:15%;">Tgl FTR</th> 
                            <th style="width:20%;">NO PO</th>
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">Total</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>    
                        <td style="" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                        <td style="width:100px;" value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
                        <td style="" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                        <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                        <td style="" value="'.$row['total'].'">'.$row['total'].'</td>                    
                             
                       </tr>';
        }
            $table .= '</tbody>';
            $table .= '</table> </div>';

echo $table;

mysqli_close($conn2);
?>