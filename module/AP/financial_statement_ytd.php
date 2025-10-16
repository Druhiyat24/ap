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

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-chart-line"></i> FINANCIAL STATEMENT YEAR TO DATE</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="financial_statement_ytd.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-3 mb-3">
                <label for="h_profit_center"><b>Profit Center</b></label>            
                <select class="form-control selectpicker" name="h_profit_center" id="h_profit_center" data-dropup-auto="false" data-live-search="true">
                    <option value="ALL" selected="true">ALL</option>                                                
                    <?php
                    $nama_supp ='';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $nama_supp = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }                 
                    $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    while ($row = mysqli_fetch_array($sql)) {
                        $data = $row['kode_pc'];
                        $data2 = $row['tampil'];
                        if($row['kode_pc'] == $_POST['h_profit_center']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';

                        }
                        echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                    }?>
                </select>
            </div>

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
<div class="col-md-3 d-flex align-items-end mb-3">
  <button type="submit" class="btn btn-info btn-sm me-2">
    <i class="fa fa-search"></i> Search
</button>
<button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
    <i class="fa fa-undo"></i> Reset
</button> 

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
  <div class="card-body p-4">

   <div class="tab">
  <button class="tablinks" onclick="openTab(event, 'trial-balance')">Trial Balance</button>
  <button class="tablinks" onclick="openTab(event, 'sfp')" id="defaultOpen">SFP</button>
  <button class="tablinks" onclick="openTab(event, 'spl')">SPL</button>
  <button class="tablinks" onclick="openTab(event, 'cf-direct')">CF Direct</button>
  <button class="tablinks" onclick="openTab(event, 'cf-indirect')">CF Indirect</button>
</div>

<div id="trial-balance" class="tabcontent">
  <!-- <h2>Trial Balance</h2> -->
  <?php include 'fs_ytd/trial_balance_ytd.php'; ?>
</div>
<div id="sfp" class="tabcontent">
  <?php include 'fs_ytd/statement_financial_position.php'; ?>
</div>
<div id="spl" class="tabcontent">
  <h6>Isi tab SPL</h6>
</div>
<div id="cf-direct" class="tabcontent">
  <h6>Isi tab CF Direct</h6>
</div>
<div id="cf-indirect" class="tabcontent">
  <h6>Isi tab CF Indirect</h6>
</div>

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

  <script type="text/javascript">
    $(document).on("click", "#co_sal", function(){ 
  var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
  var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
  var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
  var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
  var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
  var copy_user = '<?php echo $user ?>';
  var to_saldo = document.getElementById('to_saldo').value;

  $.ajax({
      type:'POST',
      url:'fs_ytd/copy_saldo_tb_new.php',
      data:{
        no_coa:no_coa,
        beg_balance:beg_balance,
        debit:debit,
        credit:credit,
        end_balance:end_balance,
        copy_user:copy_user,
        to_saldo:to_saldo
      },
      success: function(response){
          alert("Copy Saldo successfully");
      },
      error: function(xhr, ajaxOptions, thrownError) {
          alert("Error: " + xhr.responseText);
      }
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
        location.href = "financial_statement_ytd.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "financial_statement_ytd.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
