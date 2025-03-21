<div style="margin-left: 10px;margin-bottom: 10px;">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_summary.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.' && user='.$user.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size:11px;"> EXCEL SUMMARY </i></button></a>';
        ?>
    </div> 

<div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" style="font-size: 12px;" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Original Currency</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">rate</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance IDR</th>    
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>      
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>                                                                        
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Curr</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Beginning balance</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction Adj/DP</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction LP</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Adjustment</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance</th>
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
    <?php
    $bulan = '';  
    $tahun = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
    $bulan = date("m",strtotime($_POST['end_date']));  
    $tahun = date("Y",strtotime($_POST['end_date']));             
    }

    $sql_tanggal = mysqli_query($conn1,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun'");
    $row_tanggal = mysqli_fetch_array($sql_tanggal);
    $tgl_akhir = isset($row_tanggal['tgl_akhir']) ? $row_tanggal['tgl_akhir'] : 1;

    if ($tgl_akhir == $end_date) {
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'HARIAN'");
    }else{
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'PAJAK'");
    }
    
    $row_rate = mysqli_fetch_array($sql_rate);
    $jml_rate = isset($row_rate['rate']) ? $row_rate['rate'] : 1;

        $sql = mysqli_query($conn1,"select Supplier, curr, saldo_awal,addition, deduction_adj, deduction_lp, adjustment, (saldo_awal + addition + deduction_adj + deduction_lp + adjustment) saldo_akhir from (select Supplier,curr,(sal_awal_bpb + sal_awal_kbon + sal_awal_lp) saldo_awal, add_bpb addition, adj_kbon deduction_adj,ded_lp deduction_lp, ((add_lp - ded_kbon) + (add_kbon - ded_bpb)) adjustment from (select Supplier, supp.curr, COALESCE(sal_awal_bpb,0) sal_awal_bpb, COALESCE(add_bpb,0) add_bpb, COALESCE(ded_bpb,0) ded_bpb, COALESCE(sal_akhir_bpb,0) sal_akhir_bpb, COALESCE(sal_awal_kbon,0) sal_awal_kbon, COALESCE(add_kbon,0) add_kbon, COALESCE(adj_kbon,0) adj_kbon, COALESCE(ded_kbon,0) ded_kbon, COALESCE(sal_akhir_kbon,0) sal_akhir_kbon, COALESCE(sal_awal_lp,0) sal_awal_lp, COALESCE(add_lp,0) add_lp, COALESCE(ded_lp * -1,0) ded_lp, COALESCE(sal_akhir_lp,0) sal_akhir_lp from (select DISTINCT b.Supplier,a.curr from bpb a inner join mastersupplier b on b.Id_Supplier = a.id_supplier where a.curr != '' order by b.Supplier asc) supp 
left join
(select nama_supp supp_bpb, curr, sum(beg_balance) sal_awal_bpb,sum(addition) add_bpb,sum(deduction) ded_bpb,sum(end_balance) sal_akhir_bpb from rpt_ap_bpb where user_create = '$user' GROUP BY curr,nama_supp) bpb on bpb.supp_bpb = supp.Supplier and bpb.curr = supp.curr
left join
(select nama_supp supp_kbon, curr, sum(beg_balance) sal_awal_kbon,sum(add_bpb) add_kbon,sum(add_adj) adj_kbon,sum(deduction) ded_kbon,sum(end_balance) sal_akhir_kbon from rpt_ap_kbon where user_create = '$user' GROUP BY curr,nama_supp) kbon on kbon.supp_kbon = supp.Supplier and kbon.curr = supp.curr
left join
(select nama_supp supp_lp, curr, sum(beg_balance) sal_awal_lp,sum(addition) add_lp,sum(deduction) ded_lp,sum(end_balance) sal_akhir_lp from rpt_ap_lp where user_create = '$user' GROUP BY curr,nama_supp) lp on lp.supp_lp = supp.Supplier and lp.curr = supp.curr) a) a");

    $saldo_akhir_idr    = 0;
    $saldo_akhir_idr_   = 0;
    $saldo_awal_        = 0;
    $addition_          = 0;
    $deduction_adj_     = 0;
    $deduction_lp_      = 0;
    $adjustment_        = 0;
    $saldo_akhir_       = 0;
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
    $saldo_awal = $row['saldo_awal'];
    $Supplier_sum = $row['Supplier'];
    $curr_sum = $row['curr'];
    $addition = $row['addition'];
    $deduction_adj = $row['deduction_adj'];
    $deduction_lp = $row['deduction_lp'];
    $adjustment = $row['adjustment'];
    $saldo_akhir = $row['saldo_akhir'];

    if ($curr_sum == 'IDR') {
        $rate = 1;
    }elseif ($curr_sum == 'CNY') {
        $rate = 2234.01;
    }else{
        $rate = $jml_rate;
    }

    $saldo_akhir_idr = $saldo_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr; 
    $saldo_awal_ += $saldo_awal;
    $addition_ += $addition;
    $deduction_adj_ += $deduction_adj;
    $deduction_lp_ += $deduction_lp;
    $adjustment_ += $adjustment;
    $saldo_akhir_ += $saldo_akhir; 


    if($saldo_awal == '0' and $addition == '0' and $deduction_adj == '0' and $deduction_lp == '0' and $adjustment == '0'){
        echo '';
    }else{

        $sqlaging = mysqli_query($conn1,"select nama_supp, curr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from ( select 'bpb' id, nama_supp, curr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_bpb where user_create = '$user' GROUP BY nama_supp, curr
UNION
select 'kbon' id, nama_supp, curr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_kbon where user_create = '$user' GROUP BY nama_supp, curr
UNION
select 'lp' id, nama_supp, curr, sum(due_0) due_0, sum(due_1) due_1, sum(due_2) due_2, sum(due_3) due_3, sum(due_4) due_4, sum(due_5) due_5, sum(due_6) due_6, sum(due_7) due_7, sum(due_total) due_total, sum(produe_0) produe_0, sum(produe_1) produe_1, sum(produe_2) produe_2, sum(produe_3) produe_3, sum(produe_4) produe_4, sum(produe_5) produe_5, sum(produe_6) produe_6, sum(produe_total) produe_total from rpt_ap_lp where user_create = '$user' GROUP BY nama_supp, curr) a where nama_supp = '$Supplier_sum' and curr = '$curr_sum' GROUP BY nama_supp, curr ");
        $rowaging = mysqli_fetch_array($sqlaging);
        $jml_due_0 = isset($rowaging['due_0']) ? $rowaging['due_0'] : 0;
        $jml_due_1 = isset($rowaging['due_1']) ? $rowaging['due_1'] : 0;
        $jml_due_2 = isset($rowaging['due_2']) ? $rowaging['due_2'] : 0;
        $jml_due_3 = isset($rowaging['due_3']) ? $rowaging['due_3'] : 0;
        $jml_due_4 = isset($rowaging['due_4']) ? $rowaging['due_4'] : 0;
        $jml_due_5 = isset($rowaging['due_5']) ? $rowaging['due_5'] : 0;
        $jml_due_6 = isset($rowaging['due_6']) ? $rowaging['due_6'] : 0;
        $jml_due_7 = isset($rowaging['due_7']) ? $rowaging['due_7'] : 0;
        $jml_due_total = isset($rowaging['due_total']) ? $rowaging['due_total'] : 0;
        $jml_produe_0 = isset($rowaging['produe_0']) ? $rowaging['produe_0'] : 0;
        $jml_produe_1 = isset($rowaging['produe_1']) ? $rowaging['produe_1'] : 0;
        $jml_produe_2 = isset($rowaging['produe_2']) ? $rowaging['produe_2'] : 0;
        $jml_produe_3 = isset($rowaging['produe_3']) ? $rowaging['produe_3'] : 0;
        $jml_produe_4 = isset($rowaging['produe_4']) ? $rowaging['produe_4'] : 0;
        $jml_produe_5 = isset($rowaging['produe_5']) ? $rowaging['produe_5'] : 0;
        $jml_produe_6 = isset($rowaging['produe_6']) ? $rowaging['produe_6'] : 0;
        $jml_produe_total = isset($rowaging['produe_total']) ? $rowaging['produe_total'] : 0;

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
            <td value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                         
            <td style="text-align:right;" value = "'.$saldo_awal.'">'.number_format($saldo_awal,2).'</td>
            <td style="text-align:right;" value = "'.$addition.'">'.number_format($addition,2).'</td>         
            <td style="text-align:right;" value = "'.$deduction_adj.'">'.number_format($deduction_adj,2).'</td>
            <td style="text-align:right;" value = "'.$deduction_lp.'">'.number_format($deduction_lp,2).'</td>
            <td style="text-align:right;" value = "'.$adjustment.'">'.number_format($adjustment,2).'</td>
            <td style="text-align:right;" value = "'.$saldo_akhir.'">'.number_format($saldo_akhir,2).'</td>
            <td style="text-align:right;" value="'.$rate.'">'.number_format($rate,2).'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.number_format($saldo_akhir_idr,2).'</td>
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
}
echo '
            <tr >
            <th colspan = "2" style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($saldo_awal_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($addition_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($deduction_adj_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($deduction_lp_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($adjustment_,2).'</th> 
            <th style="text-align: right;vertical-align: middle;">'.number_format($saldo_akhir_,2).'</th> 
            <th style="text-align: right;vertical-align: middle;"></th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($saldo_akhir_idr_,2).'</th>             
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
        </tr>';
?>                                                     
</tbody>                    
</table>
</div>
<br>