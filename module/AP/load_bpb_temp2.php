<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$user = $_POST['user'];
$table = '';
$no_inv = '';
$tgl_inv = '';
$no_faktur = '';
$tgl_faktur = '';

 $sql = mysqli_query($conn1,"select a.no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input,if(no_inv is null OR no_inv = '',upt_no_inv,no_inv) no_inv,if(no_inv is null OR no_inv = '',upt_tgl_inv,tgl_inv) tgl_inv,no_faktur,tgl_faktur from (select no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input,no_inv,tgl_inv,no_faktur,tgl_faktur from tbl_bpb_temp where user_input = '$user' GROUP BY no_bpb) a LEFT JOIN (select no_bpb,upt_no_inv,upt_tgl_inv from bpb_new where upt_no_inv is not null GROUP BY no_bpb) b on b.no_bpb = a.no_bpb");

		while ($row = mysqli_fetch_assoc($sql)) {

        $no_inv = isset($row['no_inv']) ? $row['no_inv'] : '-';
        if ($no_inv == '-') {
            $tgl_inv = '-';
        }else{
            $tgl_inv = date("d-M-Y",strtotime($row['tgl_inv']));
        }

        $no_faktur = isset($row['no_faktur']) ? $row['no_faktur'] : '-';
        if ($no_faktur == '-') {
            $tgl_faktur = '-';
        }else{
            $tgl_faktur = date("d-M-Y",strtotime($row['tgl_faktur']));
        }

        if ($no_inv != '-' && $no_faktur != '-') {
            $color = 'class="bg-success"';
        }elseif ($no_inv == '-' && $no_faktur != '-') {
            $color = 'class="bg-warning"';
        }elseif ($no_inv != '-' && $no_faktur == '-') {
            $color = 'class="bg-warning"';
        }else{
            $color = 'class="bg-danger"';
        }
            
			
            $table .= '<tr '.$color.'>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" class="select_item" value="" checked disabled></td>    
                        <td style="" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                        <td style="" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                        <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                        <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        <td style="" value="'.$row['total'].'">'.number_format($row['total'],2).'</td> 
                        <td style="" value="'.$row['no_inv'].'">'.$no_inv.'</td>
                        <td style="" value="'.$row['tgl_inv'].'">'.$tgl_inv.'</td>
                        <td style="" value="'.$row['no_inv'].'">'.$no_faktur.'</td>
                        <td style="" value="'.$row['tgl_inv'].'">'.$tgl_faktur.'</td> 
                        <td><a id="tambah"><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-plus"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Add</i></button></a></td>                  
                             
                       </tr>';
        }


echo $table;

mysqli_close($conn2);
?>