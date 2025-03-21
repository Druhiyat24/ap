<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$id_sub = isset($_POST['id_sub']) ? $_POST['id_sub']: null;

    $sql = mysqli_query($conn1,"select id,jns_trans,ditagihkan,id_ctg,nm_ctg,id_sub_ctg,nm_sub_ctg,id_item,item_name,no_coa,nama_coa,id_cc,cc_name from memo_mapping_v2 where id_sub_ctg = '$id_sub'");

    $sql2 = mysqli_query($conn1,"select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg where id_sub_ctg = '$id_sub' order by id_ctg asc");
    $row2 = mysqli_fetch_assoc($sql2);
    $nm_ctg = $row2['nm_ctg'];
    $nm_sub_ctg = $row2['nm_sub_ctg'];
  
    $table = '<div class="row">
                <div id="txt_nm_ctg" class="modal-body col-6" style="font-size: 14px; padding: 1rem;">Category Name : '.$nm_ctg.'</div>
                <div id="txt_nm_subctg" class="modal-body col-6" style="font-size: 14px; padding: 1rem;">Sub Category Name : '.$nm_sub_ctg.'</div>
            </div> ';
    $table .= '<div class="table-responsive">
    <table id="mytdmodal" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="bg-dark" style="color:white">                       
                            <th>Jenis Transaksi</th>  
                            <th>Ditagihkan</th>  
                            <th>Item Name</th>  
                            <th>No Coa</th>  
                            <th>Nama Coa</th>  
                            <th>No Cost Center</th>     
                            <th>Nama Cost Center</th>                                                     
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['jns_trans'].'">'.$row['jns_trans'].'</td>
                            <td style="" value="'.$row['ditagihkan'].'">'.$row['ditagihkan'].'</td>                                                                       
                            <td style="" value="'.$row['item_name'].'">'.$row['item_name'].'</td>
                            <td style="" value="'.$row['no_coa'].'">'.$row['no_coa'].'</td>
                            <td style="" value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
                            <td style="" value="'.$row['id_cc'].'">'.$row['id_cc'].'</td>
                            <td style="" value="'.$row['cc_name'].'">'.$row['cc_name'].'</td>
                       </tr>
                       ';
                   }

            $table .= '</table></div>';

echo $table;

?>