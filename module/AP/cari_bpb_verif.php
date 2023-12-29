<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$nama_supp = $_POST['nama_supp'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

// echo "< -- >";
// echo $no_kbon;


 $sql = mysqli_query($conn1,"select no_bpb,tgl_bpb,supplier,curr,ROUND(SUM((qty * price) + ((qty * price) * (tax / 100))),2) total from bpb_new where status != 'Cancel' and supplier = '$nama_supp' and tgl_bpb BETWEEN '$start_date' and '$end_date' and upt_dok_inv is null GROUP BY no_bpb");

$table = '<div class="tableFix" style="height: 300px;">
<table id="table-bpb" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:5%;">Cek</th>
                            <th style="width:20%;">No BPB</th>
                            <th style="width:15%;">Tgl BPB</th> 
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">Curr</th>
                            <th style="width:20%;">Total</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>    
                        <td style="" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                        <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                        <td style="" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                        <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        <td style="" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                    
                             
                       </tr>';
        }
            $table .= '</tbody>';
            $table .= '</table> </div>';

echo $table;

mysqli_close($conn2);
?>