<div style="margin-left: 10px;margin-bottom: 10px;">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_summary_group.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.' && user='.$user.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size:11px;"> EXCEL SUMMARY </i></button></a>';
        ?>
    </div> 

<div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" style="font-size: 12px;" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Supplier Category & Name</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Amount</th>
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>      
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>                                                                        
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Foreign Currency</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Equivalent IDR</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">1-30</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
            <?php 
                $end_date = 0;
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));

                $sqlbulan = mysqli_query($conn1,"select kode_tanggal,bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun, CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)),LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun from dim_date where kode_tanggal BETWEEN CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01') and CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),LPAD(IF(MONTH('$end_date')+5 > 12,MOD((MONTH('$end_date')+5),12),(MONTH('$end_date')+5)),2,0),'01') GROUP BY bulan,tahun order by kode_tanggal asc");
                while($rowbulan = mysqli_fetch_array($sqlbulan)){
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">'.$rowbulan['bulan_tahun'].'</th>';
                }
                }else{
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>';
                } 
            ?>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
        </tr>
    </thead>
   
    <tbody> 
        <tr>
            <th colspan="4" style="text-align: left;vertical-align: middle;background-color: white;">GROUP</th>
            <th style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: white;"></th>      
            <th style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: white;"></th>                                                                        
        </tr>
    <?php
    $bulan = '';  
    $tahun = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
    $bulan = date("m",strtotime($_POST['end_date']));  
    $tahun = date("Y",strtotime($_POST['end_date']));             
    }

        $sql = mysqli_query($conn1,"select  nama_supp,curr,sum(total_type) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from (select 'bpb' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_bpb where user_create = '$user' and end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
UNION
select 'kbn' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_kbon where user_create = '$user' and end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
UNION
select 'lp' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_lp where user_create = '$user' and end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr) a GROUP BY nama_supp,curr order by nama_supp asc");

    $ttl_type = 0;
    $ttl_type_idr = 0;
    $ttl_due_0 = 0;
    $ttl_due_1 = 0;
    $ttl_due_2 = 0;
    $ttl_due_3 = 0;
    $ttl_due_4 = 0;
    $ttl_due_5 = 0;
    $ttl_due_6 = 0;
    $ttl_due_7 = 0;
    $ttl_due_total = 0;
    $ttl_produe_0 = 0;
    $ttl_produe_1 = 0;
    $ttl_produe_2 = 0;
    $ttl_produe_3 = 0;
    $ttl_produe_4 = 0;
    $ttl_produe_5 = 0;
    $ttl_produe_6 = 0;
    $ttl_produe_total = 0;

   while($row = mysqli_fetch_array($sql)){

        $curr_type = $row['curr'];
        if ($curr_type == 'USD') {
            $total_type = isset($row['total_type']) ? $row['total_type'] : 0;
        }else{
            $total_type = 0; 
        }
        $total_type_idr = isset($row['total_type_idr']) ? $row['total_type_idr'] : 0;
        $jml_due_0 = isset($row['due_0']) ? $row['due_0'] : 0;
        $jml_due_1 = isset($row['due_1']) ? $row['due_1'] : 0;
        $jml_due_2 = isset($row['due_2']) ? $row['due_2'] : 0;
        $jml_due_3 = isset($row['due_3']) ? $row['due_3'] : 0;
        $jml_due_4 = isset($row['due_4']) ? $row['due_4'] : 0;
        $jml_due_5 = isset($row['due_5']) ? $row['due_5'] : 0;
        $jml_due_6 = isset($row['due_6']) ? $row['due_6'] : 0;
        $jml_due_7 = isset($row['due_7']) ? $row['due_7'] : 0;
        $jml_due_total = isset($row['due_total']) ? $row['due_total'] : 0;
        $jml_produe_0 = isset($row['produe_0']) ? $row['produe_0'] : 0;
        $jml_produe_1 = isset($row['produe_1']) ? $row['produe_1'] : 0;
        $jml_produe_2 = isset($row['produe_2']) ? $row['produe_2'] : 0;
        $jml_produe_3 = isset($row['produe_3']) ? $row['produe_3'] : 0;
        $jml_produe_4 = isset($row['produe_4']) ? $row['produe_4'] : 0;
        $jml_produe_5 = isset($row['produe_5']) ? $row['produe_5'] : 0;
        $jml_produe_6 = isset($row['produe_6']) ? $row['produe_6'] : 0;
        $jml_produe_total = isset($row['produe_total']) ? $row['produe_total'] : 0;

        $ttl_type += $total_type;
        $ttl_type_idr += $total_type_idr;
        $ttl_due_0 += $jml_due_0;
        $ttl_due_1 += $jml_due_1;
        $ttl_due_2 += $jml_due_2;
        $ttl_due_3 += $jml_due_3;
        $ttl_due_4 += $jml_due_4;
        $ttl_due_5 += $jml_due_5;
        $ttl_due_6 += $jml_due_6;
        $ttl_due_7 += $jml_due_7;
        $ttl_due_total += $jml_due_total;
        $ttl_produe_0 += $jml_produe_0;
        $ttl_produe_1 += $jml_produe_1;
        $ttl_produe_2 += $jml_produe_2;
        $ttl_produe_3 += $jml_produe_3;
        $ttl_produe_4 += $jml_produe_4;
        $ttl_produe_5 += $jml_produe_5;
        $ttl_produe_6 += $jml_produe_6;
        $ttl_produe_total += $jml_produe_total;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>  
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>                        
            <td style="text-align:right;" value="'.$total_type.'">'.number_format($total_type,2).'</td>
            <td style="text-align:right;" value="'.$total_type_idr.'">'.number_format($total_type_idr,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$jml_due_0.'">'.number_format($jml_due_0,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_1.'">'.number_format($jml_due_1,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_2.'">'.number_format($jml_due_2,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_3.'">'.number_format($jml_due_3,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_4.'">'.number_format($jml_due_4,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_5.'">'.number_format($jml_due_5,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_6.'">'.number_format($jml_due_6,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_7.'">'.number_format($jml_due_7,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_total.'">'.number_format($jml_due_total,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$jml_produe_0.'">'.number_format($jml_produe_0,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_1.'">'.number_format($jml_produe_1,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_2.'">'.number_format($jml_produe_2,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_3.'">'.number_format($jml_produe_3,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_4.'">'.number_format($jml_produe_4,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_5.'">'.number_format($jml_produe_5,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_6.'">'.number_format($jml_produe_6,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_total.'">'.number_format($jml_produe_total,2).'</td>
             ';

}
echo '
            <tr >
            <th colspan = "2" style="text-align: center;vertical-align: middle;">Total Group</th> 
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_type,2).'</th>             
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_type_idr,2).'</th> 
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_0,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_6,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_7,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_total,2).'</th>       
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_0,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_6,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_total,2).'</th>                                                              
        </tr>
        <tr>    
            <th colspan="23" style="border: none;width: 50px;background-color: white;"></th>                                                                      
        </tr>
        <tr>
            <th colspan="4" style="text-align: left;vertical-align: middle;background-color: white;">NON GROUP</th>
            <th style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: white;"></th>      
            <th style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: white;"></th>                                                                        
        </tr>';



        $sql_ng = mysqli_query($conn1,"select  item_type2,curr,sum(total_type) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from (select 'bpb' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_bpb where user_create = '$user' and end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
UNION
select 'kbn' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_kbon where user_create = '$user' and end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
UNION
select 'lp' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_lp where user_create = '$user' and end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a GROUP BY item_type2,curr order by item_type2 asc");

    $ttl_type_ng = 0;
    $ttl_type_idr_ng = 0;
    $ttl_due_0_ng = 0;
    $ttl_due_1_ng = 0;
    $ttl_due_2_ng = 0;
    $ttl_due_3_ng = 0;
    $ttl_due_4_ng = 0;
    $ttl_due_5_ng = 0;
    $ttl_due_6_ng = 0;
    $ttl_due_7_ng = 0;
    $ttl_due_total_ng = 0;
    $ttl_produe_0_ng = 0;
    $ttl_produe_1_ng = 0;
    $ttl_produe_2_ng = 0;
    $ttl_produe_3_ng = 0;
    $ttl_produe_4_ng = 0;
    $ttl_produe_5_ng = 0;
    $ttl_produe_6_ng = 0;
    $ttl_produe_total_ng = 0;

   while($row_ng = mysqli_fetch_array($sql_ng)){

        $curr_type_ng = $row_ng['curr'];
        if ($curr_type_ng == 'USD') {
            $total_type_ng = isset($row_ng['total_type']) ? $row_ng['total_type'] : 0;
        }else{
            $total_type_ng = 0; 
        }
        $total_type_idr_ng = isset($row_ng['total_type_idr']) ? $row_ng['total_type_idr'] : 0;
        $jml_due_0_ng = isset($row_ng['due_0']) ? $row_ng['due_0'] : 0;
        $jml_due_1_ng = isset($row_ng['due_1']) ? $row_ng['due_1'] : 0;
        $jml_due_2_ng = isset($row_ng['due_2']) ? $row_ng['due_2'] : 0;
        $jml_due_3_ng = isset($row_ng['due_3']) ? $row_ng['due_3'] : 0;
        $jml_due_4_ng = isset($row_ng['due_4']) ? $row_ng['due_4'] : 0;
        $jml_due_5_ng = isset($row_ng['due_5']) ? $row_ng['due_5'] : 0;
        $jml_due_6_ng = isset($row_ng['due_6']) ? $row_ng['due_6'] : 0;
        $jml_due_7_ng = isset($row_ng['due_7']) ? $row_ng['due_7'] : 0;
        $jml_due_total_ng = isset($row_ng['due_total']) ? $row_ng['due_total'] : 0;
        $jml_produe_0_ng = isset($row_ng['produe_0']) ? $row_ng['produe_0'] : 0;
        $jml_produe_1_ng = isset($row_ng['produe_1']) ? $row_ng['produe_1'] : 0;
        $jml_produe_2_ng = isset($row_ng['produe_2']) ? $row_ng['produe_2'] : 0;
        $jml_produe_3_ng = isset($row_ng['produe_3']) ? $row_ng['produe_3'] : 0;
        $jml_produe_4_ng = isset($row_ng['produe_4']) ? $row_ng['produe_4'] : 0;
        $jml_produe_5_ng = isset($row_ng['produe_5']) ? $row_ng['produe_5'] : 0;
        $jml_produe_6_ng = isset($row_ng['produe_6']) ? $row_ng['produe_6'] : 0;
        $jml_produe_total_ng = isset($row_ng['produe_total']) ? $row_ng['produe_total'] : 0;

        $ttl_type_ng += $total_type_ng;
        $ttl_type_idr_ng += $total_type_idr_ng;
        $ttl_due_0_ng += $jml_due_0_ng;
        $ttl_due_1_ng += $jml_due_1_ng;
        $ttl_due_2_ng += $jml_due_2_ng;
        $ttl_due_3_ng += $jml_due_3_ng;
        $ttl_due_4_ng += $jml_due_4_ng;
        $ttl_due_5_ng += $jml_due_5_ng;
        $ttl_due_6_ng += $jml_due_6_ng;
        $ttl_due_7_ng += $jml_due_7_ng;
        $ttl_due_total_ng += $jml_due_total_ng;
        $ttl_produe_0_ng += $jml_produe_0_ng;
        $ttl_produe_1_ng += $jml_produe_1_ng;
        $ttl_produe_2_ng += $jml_produe_2_ng;
        $ttl_produe_3_ng += $jml_produe_3_ng;
        $ttl_produe_4_ng += $jml_produe_4_ng;
        $ttl_produe_5_ng += $jml_produe_5_ng;
        $ttl_produe_6_ng += $jml_produe_6_ng;
        $ttl_produe_total_ng += $jml_produe_total_ng;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row_ng['item_type2'].'">'.$row_ng['item_type2'].'</td>  
            <td value = "'.$row_ng['curr'].'">'.$row_ng['curr'].'</td>                        
            <td style="text-align:right;" value="'.$total_type_ng.'">'.number_format($total_type_ng,2).'</td>
            <td style="text-align:right;" value="'.$total_type_idr_ng.'">'.number_format($total_type_idr_ng,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$jml_due_0_ng.'">'.number_format($jml_due_0_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_1_ng.'">'.number_format($jml_due_1_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_2_ng.'">'.number_format($jml_due_2_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_3_ng.'">'.number_format($jml_due_3_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_4_ng.'">'.number_format($jml_due_4_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_5_ng.'">'.number_format($jml_due_5_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_6_ng.'">'.number_format($jml_due_6_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_7_ng.'">'.number_format($jml_due_7_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_due_total_ng.'">'.number_format($jml_due_total_ng,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$jml_produe_0_ng.'">'.number_format($jml_produe_0_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_1_ng.'">'.number_format($jml_produe_1_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_2_ng.'">'.number_format($jml_produe_2_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_3_ng.'">'.number_format($jml_produe_3_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_4_ng.'">'.number_format($jml_produe_4_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_5_ng.'">'.number_format($jml_produe_5_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_6_ng.'">'.number_format($jml_produe_6_ng,2).'</td>
            <td style="text-align:right;" value="'.$jml_produe_total_ng.'">'.number_format($jml_produe_total_ng,2).'</td>
             ';

}
echo '
            <tr >
            <th colspan = "2" style="text-align: center;vertical-align: middle;">Total Non Group</th> 
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_type_ng,2).'</th>             
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_type_idr_ng,2).'</th> 
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_0_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_1_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_2_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_3_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_4_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_5_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_6_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_7_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_total_ng,2).'</th>       
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_0_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_1_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_2_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_3_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_4_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_5_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_6_ng,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_produe_total_ng,2).'</th>                                                              
        </tr>
        <tr>    
            <th colspan="23" style="border: none;width: 50px;background-color: white;"></th>                                                                      
        </tr>
        <tr >
            <th colspan = "2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">GRAND TOTAL</th> 
            <th style="text-align: right;vertical-align: middle;background-color: #FFE4E1;">'.number_format(($ttl_type + $ttl_type_ng),2).'</th>             
            <th style="text-align: right;vertical-align: middle;background-color: #FFE4E1;">'.number_format(($ttl_type_idr + $ttl_type_idr_ng),2).'</th> 
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_0 + $ttl_due_0_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_1 + $ttl_due_1_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_2 + $ttl_due_2_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_3 + $ttl_due_3_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_4 + $ttl_due_4_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_5 + $ttl_due_5_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_6 + $ttl_due_6_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_7 + $ttl_due_7_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #98FB98;">'.number_format(($ttl_due_total + $ttl_due_total_ng),2).'</th>       
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_0 + $ttl_produe_0_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_1 + $ttl_produe_1_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_2 + $ttl_produe_2_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_3 + $ttl_produe_3_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_4 + $ttl_produe_4_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_5 + $ttl_produe_5_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_6 + $ttl_produe_6_ng),2).'</th>
            <th style="text-align: right;vertical-align: middle;background-color: #87CEFA;">'.number_format(($ttl_produe_total + $ttl_produe_total_ng),2).'</th>                                                              
        </tr>
        ';
?>                                                     
</tbody>                    
</table>
</div>
<br>