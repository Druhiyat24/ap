<style>
  /*.tabcontent {
    padding: 15px;
    background: #fff;
    border-radius: 8px;
  }

  .tb-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 15px;
  }

  .tb-header h4 {
    font-weight: 600;
    color: #0d6efd;
    margin-bottom: 0;
  }

  .tb-header .btn {
    margin-right: 8px;
    font-size: 13px;
    border-radius: 6px;
  }

  .tb-header .btn i {
    font-size: 14px;
  }

  .tableFix {
    overflow-y: auto;
    max-height: 450px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
  }

  table th {
    position: sticky;
    top: 0;
    background: #0d6efd;
    color: #fff;
    text-align: center;
    font-size: 12px;
    vertical-align: middle;
  }

  table td {
    font-size: 12px;
    vertical-align: middle;
  }*/

  #myInput {
    border-radius: 6px;
    font-size: 13px;
    width: 200px;
  }

 /* Jangan pakai !important di sini, biar DataTables bisa hitung otomatis */
.dataTables_wrapper .dataTables_scrollHeadInner {
  width: auto !important;
}

.dataTables_wrapper .dataTables_scrollBody {
  width: auto !important;
  overflow-x: auto;
}

table.dataTable {
  width: 100%;
  min-width: max-content; /* biar kolom tidak ketarik jadi sempit */
}


</style>

<!-- ====== TAB CONTENT: TRIAL BALANCE ====== -->
  <form id="form-data" method="post">
    
    <!-- HEADER -->
    <div class="tb-header mt-2">
      <div>
        <?php
        $nama_type ='';
        $Status = '';
        $start_date ='';
        $end_date ='';
        $where ='';
        $date_now = date("Y-m-d");
        $tanggal_awal = date("Y-m-d",strtotime($date_now ));
        $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
        $bulan_awal = date("m",strtotime($date_now));
        $bulan_akhir = date("m",strtotime($date_now));  
        $tahun_awal = date("Y",strtotime($date_now));
        $tahun_akhir = date("Y",strtotime($date_now));
        $kata_awal = date("M",strtotime($date_now));
        $tengah = '_';
        $kata_akhir = date("Y",strtotime($date_now));
        $kata_filter = $kata_awal . $tengah . $kata_akhir;
        $kata_filter2 = $kata_awal . $tengah . $kata_akhir;                 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null; 
            $start_date = date("d-m-Y",strtotime($_POST['start_date']));
            $end_date = date("d-m-Y",strtotime($_POST['end_date'])); 

            $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
            $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

            $bulan_awal = date("m",strtotime($_POST['start_date']));
            $bulan_akhir = date("m",strtotime($_POST['end_date']));  
            $tahun_awal = date("Y",strtotime($_POST['start_date']));
            $tahun_akhir = date("Y",strtotime($_POST['end_date']));

            $kata_awal = date("M",strtotime($_POST['start_date']));
            $tengah = '_';
            $kata_akhir = date("Y",strtotime($_POST['start_date']));
            $kata_filter = $kata_awal . $tengah . $kata_akhir;

            $kata_awal2 = date("M",strtotime($_POST['end_date']));
            $tengah2 = '_';
            $kata_akhir2 = date("Y",strtotime($_POST['end_date']));
            $kata_filter2 = $kata_awal2 . $tengah2 . $kata_akhir2;
            
        }
echo '<input type="hidden" style="font-size: 12px;" class="form-control" id="to_saldo" name="to_saldo" 
    value="'.$kata_filter2.'">';
    ?>
        <?php
          $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
          $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
          $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          $tanggal_awal = date("Y-m-d", strtotime($start_date));
          $tanggal_akhir = date("Y-m-d", strtotime($end_date));
          $kata_awal = date("M", strtotime($start_date));
          $kata_akhir = date("Y", strtotime($start_date));
          $kata_filter = $kata_awal . '_' . $kata_akhir;

          if ($tanggal_akhir >= $tanggal_awal) {
            echo '
              <a target="_blank" href="ekspor_tb_ytd_new.php?start_date='.$start_date.'&end_date='.$end_date.'&kata_filter='.$kata_filter.'&profit_center='.$profit_center.'">
                <button type="button" class="btn btn-success">
                  <i class="fa fa-file-excel-o me-2"></i> Export Excel
                </button>
              </a>';
          }
        ?>

        <?php
          $querys = mysqli_query($conn2, "
            SELECT useraccess.menu, useraccess.username, menurole.id
            FROM useraccess 
            INNER JOIN menurole ON menurole.menu = useraccess.menu 
            WHERE username = '$user' AND useraccess.menu = 'Acct - Copy saldo TB'
          ");
          $rs = mysqli_fetch_array($querys);
          $id = isset($rs['id']) ? $rs['id'] : 0;

          if ($id == '56') {
            echo '
              <button type="button" class="btn btn-info" name="co_sal" id="co_sal">
                <i class="fa fa-clipboard me-2"></i> Copy Saldo
              </button>';
          }
        ?>
      </div>
    </div>

    <!-- ====== TABEL ====== -->
    <div class="table-responsive mt-2">
          <table id="table_tbytd" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          >
        <thead class="text-white" style="background-color: #2563EB;">
          <tr>
            <th rowspan="2">No COA</th>
            <th rowspan="2">COA Name</th>
            <th rowspan="2">Category 1</th>
            <th rowspan="2">Category 2</th>
            <th rowspan="2">Category 3</th>
            <th rowspan="2">Category 4</th>
            <?php
              if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
              }

              if ($profit_center == 'ALL') {
                echo '<th colspan="4">Nirwana Alabare Garment</th>';
                echo '<th colspan="4">Nirwana Alabare Knitting</th>';
                echo '<th colspan="4">Summary ALL</th>';
              }elseif ($profit_center == 'NAG') {
                echo '<th colspan="4">Nirwana Alabare Garment</th>';
              }else{
                echo '<th colspan="4">Nirwana Alabare Knitting</th>';
              }
            ?>
          </tr>
          <tr>
          <?php
              if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
              }

              if ($profit_center == 'ALL') {
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }elseif ($profit_center == 'NAG') {
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }else{
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
        $nama_type ='';
        $Status = '';
        $start_date ='';
        $end_date ='';
        $where ='';
        $date_now = date("Y-m-d");
        $tanggal_awal = date("Y-m-d",strtotime($date_now ));
        $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
        $bulan_awal = date("m",strtotime($date_now));
        $bulan_akhir = date("m",strtotime($date_now));  
        $tahun_awal = date("Y",strtotime($date_now));
        $tahun_akhir = date("Y",strtotime($date_now));
        $kata_awal = date("M",strtotime($date_now));
        $tengah = '_';
        $kata_akhir = date("Y",strtotime($date_now));
        $kata_filter = $kata_awal . $tengah . $kata_akhir;
        $kata_filter2 = $kata_awal . $tengah . $kata_akhir;                 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null; 
            $start_date = date("d-m-Y",strtotime($_POST['start_date']));
            $end_date = date("d-m-Y",strtotime($_POST['end_date'])); 

            $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
            $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

            $bulan_awal = date("m",strtotime($_POST['start_date']));
            $bulan_akhir = date("m",strtotime($_POST['end_date']));  
            $tahun_awal = date("Y",strtotime($_POST['start_date']));
            $tahun_akhir = date("Y",strtotime($_POST['end_date']));

            $kata_awal = date("M",strtotime($_POST['start_date']));
            $tengah = '_';
            $kata_akhir = date("Y",strtotime($_POST['start_date']));
            $kata_filter = $kata_awal . $tengah . $kata_akhir;

            $kata_awal2 = date("M",strtotime($_POST['end_date']));
            $tengah2 = '_';
            $kata_akhir2 = date("Y",strtotime($_POST['end_date']));
            $kata_filter2 = $kata_awal2 . $tengah2 . $kata_akhir2;
            
        }
        


        // $que = "select no_coa, nama_coa, profit_center, nama_pc, indname1, indname2, indname3, indname4, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, c.nama_coa, a.profit_center, b.nama_pc, indname1, indname2, indname3, indname4, $kata_filter as saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from fs_saldo_awal_tb a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) d on d.id_ctg5A =c.id_ctg5 LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa, profit_center) e on e.no_coa = a.no_coa and e.profit_center = a.profit_center) a $where order by no_coa asc";

        $que = "select a.*,b.saldo_awal saldo_awal_nag, b.debit_idr debit_idr_nag, b.credit_idr credit_idr_nag, b.saldo_akhir saldo_akhir_nag, c.saldo_awal saldo_awal_nak, c.debit_idr debit_idr_nak, c.credit_idr credit_idr_nak, c.saldo_akhir saldo_akhir_nak, (b.saldo_awal + c.saldo_awal) saldo_awal_all, (b.debit_idr + c.debit_idr) debit_idr_all, (b.credit_idr + c.credit_idr) credit_idr_all, (b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
(select no_coa, nama_coa, indname1, indname2, indname3, indname4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
left join 
(select no_coa, COALESCE(saldo,0) saldo_awal, COALESCE(debit_idr,0) debit_idr, COALESCE(credit_idr,0) credit_idr, (COALESCE(saldo,0) + COALESCE(debit_idr,0) - COALESCE(credit_idr,0)) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
left join 
(select no_coa, COALESCE(saldo,0) saldo_awal, COALESCE(debit_idr,0) debit_idr, COALESCE(credit_idr,0) credit_idr, (COALESCE(saldo,0) + COALESCE(debit_idr,0) - COALESCE(credit_idr,0)) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa";

      $sql = mysqli_query($conn2,$que);

    $sqldelete = "DELETE from fs_saldo_tb_temp";
    $execute5 = mysqli_query($conn2, $sqldelete);

    if(!$execute5){ 

    }else{

      ini_set('date.timezone', 'Asia/Jakarta');
            $sqlx = mysqli_query($conn1,"SELECT to_saldo FROM tbl_bln_tb where from_saldo = '$kata_filter2'");
            $rowx = mysqli_fetch_array($sqlx);
            $saldo_to = isset($rowx['to_saldo']) ? $rowx['to_saldo'] : null;
            $copy_date = date("Y-m-d H:i:s");

        $queryss2 = "insert into fs_saldo_tb_temp select '', nocoa, profit_center, saldo, debit_idr, credit_idr, ((saldo + debit_idr) - credit_idr) end_balance, '$user','$copy_date','$saldo_to' from (select nocoa, saldo.profit_center, COALESCE(saldo,0) saldo,COALESCE(debit_idr,0) debit_idr,COALESCE(credit_idr,0) credit_idr from (select profit_center, no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from fs_saldo_awal_tb order by no_coa asc) saldo
        left join
        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4 from mastercoa_v2 order by no_coa asc) coa
        on coa.no_coa = saldo.nocoa
        left join
        (select profit_center, no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa, profit_center) 
        jnl on jnl.coa_no = coa.no_coa and saldo.profit_center = jnl.profit_center order by no_coa asc) a order by a.nocoa, profit_center asc";

        $executess2 = mysqli_query($conn2, $queryss2);

    }

    echo '<input type="hidden" style="font-size: 12px;" class="form-control" id="to_saldo" name="to_saldo" 
    value="'.$kata_filter2.'">';
    $saldoakhir = 0;

    if($tanggal_akhir < $tanggal_awal){
        $message = "Mohon Masukan Tanggal Filter Yang Benar";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }

    else{
        while($row = mysqli_fetch_array($sql)){
            $beg_balance = isset($row['saldo']) ? $row['saldo'] : 0;
            $credit_idr = isset($row['credit_idr']) ? $row['credit_idr'] : 0;
            $debit_idr = isset($row['debit_idr']) ? $row['debit_idr'] : 0;
            $saldoakhir = ($beg_balance + $debit_idr) - $credit_idr;
            $balance_idr = isset($row['balance_idr']) ? $row['balance_idr'] : null;

            if ($balance_idr == 'NB') {
             $warna = '#FF7F50';
         }else{
           $warna = 'grey';
       }

       echo '<tr style="font-size:11px;text-align:center;">
       <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
       <td style="text-align : left;" value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
       <td style="text-align : left;" value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
       <td style="text-align : left;" value = "'.$row['indname4'].'">'.$row['indname4'].'</td>';
        if ($profit_center == 'ALL') {
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nag'].'">'.number_format($row['saldo_awal_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nag'].'">'.number_format($row['debit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nag'].'">'.number_format($row['credit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nag'].'">'.number_format($row['saldo_akhir_nag'],2).'</td>

                <td style=" text-align : right;" value="'.$row['saldo_awal_nak'].'">'.number_format($row['saldo_awal_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nak'].'">'.number_format($row['debit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nak'].'">'.number_format($row['credit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nak'].'">'.number_format($row['saldo_akhir_nak'],2).'</td>

                <td style=" text-align : right;" value="'.$row['saldo_awal_all'].'">'.number_format($row['saldo_awal_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_all'].'">'.number_format($row['debit_idr_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_all'].'">'.number_format($row['credit_idr_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_all'].'">'.number_format($row['saldo_akhir_all'],2).'</td>';
        }elseif ($profit_center == 'NAG') {
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nag'].'">'.number_format($row['saldo_awal_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nag'].'">'.number_format($row['debit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nag'].'">'.number_format($row['credit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nag'].'">'.number_format($row['saldo_akhir_nag'],2).'</td>';
        }else{
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nak'].'">'.number_format($row['saldo_awal_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nak'].'">'.number_format($row['debit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nak'].'">'.number_format($row['credit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nak'].'">'.number_format($row['saldo_akhir_nak'],2).'</td>';
        }
       echo '</tr>';
   }
}
?>
        </tbody>                    
      </table>
    </div>
  </form>

