<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

.tab {
  display: flex;
  border-bottom: 2px solid #dee2e6; /* garis bawah tab bar */
  font-size: 13px;
}

.tab button {
  background: none;
  border: none;
  outline: none;
  padding: 8px 16px;
  cursor: pointer;
  color: #495057;
  transition: all 0.2s ease;
  font-weight: 500;
}

.tab button:hover {
  color: #0d6efd; /* biru saat hover */
}

.tab button.active {
  color: #0d6efd; /* teks aktif */
  border-bottom: 3px solid #0d6efd; /* garis bawah aktif */
  font-weight: 600;
}

.tabcontent {
  display: none;
  padding: 10px 0;
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

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-chart-line"></i> TRIAL BALANCE</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="trial_balance.php" method="post">
    <div class="row g-3">

    <!-- Start Date -->
    <div class="col-md-2">
        <label for="start_date" class="form-label"><b>From</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
        value="<?php
        $start_date ='';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $start_date = date("Y-m-d",strtotime($_POST['start_date']));
       }
       if(!empty($_POST['start_date'])) {
           echo $_POST['start_date'];
       }
       else{
           echo date("d-m-Y");
       } ?>" placeholder="Start Date" autocomplete="off">
   </div>

   <!-- End Date -->
   <div class="col-md-2">
    <label for="end_date" class="form-label"><b>To</b></label>
    <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
    value="<?php
    $end_date ='';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $end_date = date("Y-m-d",strtotime($_POST['end_date']));
   }
   if(!empty($_POST['end_date'])) {
       echo $_POST['end_date'];
   }
   else{
       echo date("d-m-Y");
   } ?>"  placeholder="End Date" autocomplete="off">
</div>

<!-- Tombol -->
<div class="col-md-3 d-flex align-items-end">
  <button type="submit" class="btn btn-info btn-sm me-2">
    <i class="fa fa-search"></i> Search
</button>
<button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
    <i class="fa fa-undo"></i> Reset
</button> 

<?php
          $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
          $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
          $tanggal_awal = date("Y-m-d", strtotime($start_date));
          $tanggal_akhir = date("Y-m-d", strtotime($end_date));
          $kata_awal = date("M", strtotime($start_date));
          $kata_akhir = date("Y", strtotime($start_date));
          $kata_filter = $kata_awal . '_' . $kata_akhir;

          if ($tanggal_akhir >= $tanggal_awal) {
            echo '
              <a target="_blank" href="ekspor_tb_ytd.php?start_date=' . $start_date . '&end_date=' . $end_date . '&kata_filter=' . $kata_filter . '">
                <button type="button" class="btn btn-success ml-2">
                  <i class="fa fa-file-excel-o me-2"></i> Export Excel
                </button>
              </a>';
          }
        ?>

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
  <div class="card-body p-4">

     <form id="form-data" method="post">

    <!-- ====== TABEL ====== -->
    <div class="table-responsive mt-2">
          <table id="table_tbytd" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          >
        <thead class="text-white" style="background-color: #2563EB;">
          <tr>
            <th>No COA</th>
            <th>COA Name</th>
            <th>Category 1</th>
            <th>Category 2</th>
            <th>Category 3</th>
            <th>Category 4</th>
            <th>Beginning Balance</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Ending Balance</th>
        </thead>
        <tbody>
          <?php
        $nama_type ='';
        $Status = '';
        $start_date ='';
        $end_date ='';
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
            $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null; 
            $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
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

    // echo  $tanggal_awal;
    // echo  $tanggal_akhir;
    // echo  $tahun_akhir;            
        }
        if(empty($start_date) and empty($end_date)){
           $sql = mysqli_query($conn2,"    
            select * from 
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
            LEFT JOIN
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc");
       }
       else{
        $sql = mysqli_query($conn2,"select * from 
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
            LEFT JOIN
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc");
    }

    $sqldelete = "DELETE from tbl_saldo_tb_temp";
    $execute5 = mysqli_query($conn2, $sqldelete);

    if(!$execute5){ 

    }else{

        $queryss2 = "insert into tbl_saldo_tb_temp select '', nocoa, saldo, debit_idr, credit_idr, ((saldo + debit_idr) - credit_idr) end_balance, '','','' from (select nocoa,COALESCE(saldo,0) saldo,COALESCE(debit_idr,0) debit_idr,COALESCE(credit_idr,0) credit_idr from 
        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
        left join
        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4 from mastercoa_v2 order by no_coa asc) coa
        on coa.no_coa = saldo.nocoa
        left join
        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a order by a.nocoa asc";

        $executess2 = mysqli_query($conn2, $queryss2);

        if(!$executess2){ 

        }else{
            ini_set('date.timezone', 'Asia/Jakarta');
            $sqlx = mysqli_query($conn1,"SELECT to_saldo FROM tbl_bln_tb where from_saldo = '$kata_filter2'");
            $rowx = mysqli_fetch_array($sqlx);
            $saldo_to = isset($rowx['to_saldo']) ? $rowx['to_saldo'] : null;
            $copy_date = date("Y-m-d H:i:s");

            $sqlupdate = "UPDATE tbl_saldo_tb_temp set copy_user = '$user',copy_date = '$copy_date',to_saldo = '$saldo_to'";

            $execute = mysqli_query($conn2, $sqlupdate);
        }
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
        // if ($reff_date == '0000-00-00' || $reff_date == '1970-01-01' || $reff_date == '') {
        //     $Reffdate = '-'; 
        // }else{
        //     $Reffdate = date("d-M-Y",strtotime($reff_date));
        // }
        //background-color:'.$warna.';

       echo '<tr style="font-size:11px;text-align:center;">
       <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
       <td style="text-align : left;" value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
       <td style="text-align : left;" value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
       <td style="text-align : left;" value = "'.$row['indname4'].'">'.$row['indname4'].'</td>
       <td style=" text-align : right;" value="'.$beg_balance.'">'.number_format($beg_balance,2).'</td>
       <td style=" text-align : right;" value="'.$debit_idr.'">'.number_format($debit_idr,2).'</td>
       <td style=" text-align : right;" value="'.$credit_idr.'">'.number_format($credit_idr,2).'</td>
       <td style=" text-align : right;" value="'.$saldoakhir.'">'.number_format($saldoakhir,2).'</td>

       ';
       echo '</tr>';
   }
}
?>
        </tbody>                    
      </table>
    </div>
  </form>

  </div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
}
div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}


</style>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-6 mb-2"></div>
          <div id="txt_no_po" class="col-md-6 mb-2"></div>
          <div id="txt_supp" class="col-md-6 mb-2"></div>
          <div id="txt_top" class="col-md-6 mb-2"></div>
          <div id="txt_curr" class="col-md-6 mb-2"></div>
          <div id="txt_confirm" class="col-md-6 mb-2"></div>
          <div id="txt_confirm2" class="col-md-6 mb-2"></div>
          <div id="txt_tgl_po" class="col-md-6 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
  </div>
</div>
</div>
</div>



<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide'); 

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left'); 

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    
    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    
    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>
<script>
    $(document).ready(function() {
        $('#mytable').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false 
        });

        $("[data-toggle=tooltip]").tooltip();
    });

      $(document).ready(function() {
  $('#table_tbytd').DataTable({
    scrollX: true,
    scrollCollapse: true,
    paging: true,
    searching: true,
    info: true,
    autoWidth: false,
    ordering: false,
    fixedHeader: true,
    columnDefs: [
    { width: "120px", targets: "_all" }       // sisanya 100px semua
  ],
    initComplete: function() {
      this.api().columns.adjust();
    }
  });
});

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2022",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script>
function openTab(evt, tabName) {
  // Sembunyikan semua tab
  var tabcontent = document.getElementsByClassName("tabcontent");
  for (var i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Hapus class active
  var tablinks = document.getElementsByClassName("tablinks");
  for (var i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Tampilkan tab yang diklik
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";

  // 🔧 Re-adjust DataTables saat tab aktif
  $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
}

document.getElementById("defaultOpen").click();
</script>



<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>
<!-- 
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "trial_balance.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "trial_balance.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
