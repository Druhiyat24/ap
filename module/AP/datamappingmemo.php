<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$id_sub_ctg = isset($_POST['id_sub_ctg']) ? $_POST['id_sub_ctg']: null;

    $sql = mysqli_query($conn1,"select id,jns_trans,ditagihkan,id_ctg,nm_ctg,id_sub_ctg,nm_sub_ctg,id_item,item_name,no_coa,nama_coa,id_cc,cc_name from memo_mapping_v2 where id_sub_ctg = '$id_sub_ctg'");
  
    $table = ' ';
    $no = 1;
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr style="font-size:12px;text-align:left;">
                            <td value = "">'.$no++.'.</td>
                            <td value = "'.$row['jns_trans'].'">'.$row['jns_trans'].'</td>
                            <td value = "'.$row['ditagihkan'].'">'.$row['ditagihkan'].'</td>
                            <td value = "'.$row['item_name'].'">'.$row['item_name'].'</td>
                            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
                            <td value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
                            <td value = "'.$row['id_cc'].'">'.$row['id_cc'].'</td>
                            <td value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>
                            <td style="font-size:12px;text-align:center;">';
                                $no_coa = $row['no_coa'];
                                $query2 = mysqli_query($conn2,"select id,no_coa,nama_coa from tbl_list_journal where no_journal like '%MEMO%' and no_coa != '2.18.02' and no_coa = '$no_coa' GROUP BY no_coa");
                                $cek_data = mysqli_fetch_array($query2);
                                $id_map = isset($cek_data['id']) ?  $cek_data['id'] : 0;

                                if ($id_map != 0) {
                                    $table .= '-';
                                }else{
                                    $table .= '<button style="border-radius: 6px" type="button" class="btn-xs btn-danger" onclick="deletedata('.$row['id'].')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';   
                                }           
                            $table .= '</td>
                        </tr>';
                   }
echo $table;

?>