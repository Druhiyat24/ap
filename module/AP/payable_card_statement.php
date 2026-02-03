<?php include '../header.php' ?>

<style type="text/css">
  label {
    font-size: 14px;;
  }

  input {
    font-size: 14px;;
  }


  .tabcontent {
    display: none;
    animation: fadeEffect 0.3s;
  }

  @keyframes fadeEffect {
    from {opacity: 0;}
    to {opacity: 1;}
  }

  .tab-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px;
    background: #f4f6f9;
    border-radius: 10px;
    border: 1px solid #ddd;
  }

  .tablinks {
    border: none;
    background: #ffffff;
    color: #555;
    padding: 8px 14px;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  }

  .tablinks:hover {
    background: #007bff;
    color: #fff;
    transform: translateY(-1px);
  }

  .tablinks.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0,123,255,0.35);
  }

  table.dataTable th,
  table.dataTable td {
    white-space: nowrap;
    vertical-align: middle;
  }

  .dataTables_scrollHeadInner,
  .dataTables_scrollBody table {
    width: 100% !important;
  }


</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-swatchbook"></i> PAYABLE CARD STATEMENT</h5>
  </div>

  <div class="card-body p-3">
    <form id="form-data" action="payable_card_statement.php" method="post">
      <div class="row g-3">
        <!-- Supplier -->
        <div class="col-md-3 mb-3">
          <label for="nama_supp"><b>Supplier</b></label>            
          <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" <?php
            $nama_supp = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            }                 
            if($nama_supp == 'ALL'){
              $isSelected = ' selected="selected"';
            }else{
              $isSelected = '';
            }
            echo $isSelected;
            ?>                
            >ALL</option>                                 
            <?php
            $nama_supp ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            }                 
            $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
            while ($row = mysql_fetch_array($sql)) {
              $data = $row['Supplier'];
              if($row['Supplier'] == $_POST['nama_supp']){
                $isSelected = ' selected="selected"';
              }else{
                $isSelected = '';
              }
              echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
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
           echo date("Y-m-d");
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
         echo date("Y-m-d");
       } ?>"  placeholder="End Date" autocomplete="off">
     </div>

     <!-- Tombol -->
     <div class="col-md-3 d-flex align-items-end mb-3">
      <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>

    </div>

  </div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
  <div class="card-body p-4">

   <div class="tab-container">
    <button class="tablinks active" onclick="openTab(event, 'pcs_bpb')">BPB</button>
    <button class="tablinks" onclick="openTab(event, 'pcs_kontrabon')">Kontrabon</button>
    <button class="tablinks" onclick="openTab(event, 'pcs_list_payment')">List Payment</button>
    <button class="tablinks" onclick="openTab(event, 'pcs_summary_supplier')">Summary Supplier</button>
    <button class="tablinks" onclick="openTab(event, 'pcs_type1')">Item Type 1</button>
    <button class="tablinks" onclick="openTab(event, 'pcs_type2')">Item Type 2</button>
    <button class="tablinks" onclick="openTab(event, 'summary')">Summary</button>
  </div>


  <div id="pcs_bpb" class="tabcontent">
    <?php include 'ap_report/pcs_bpb.php'; ?>
  </div>

  <div id="pcs_kontrabon" class="tabcontent">
    <?php include 'ap_report/pcs_kontrabon.php'; ?>
  </div>

  <div id="pcs_list_payment" class="tabcontent">
    <?php include 'ap_report/pcs_list_payment.php'; ?>
  </div>

  <div id="pcs_summary_supplier" class="tabcontent">
    <?php include 'ap_report/pcs_summary_supplier.php'; ?>
  </div>

  </div> <div id="pcs_type1" class="tabcontent">
    <?php include 'ap_report/pcs_type1.php'; ?>
  </div>

</div> <div id="pcs_type2" class="tabcontent">
    <?php include 'ap_report/pcs_type2.php'; ?>
  </div>


</div>
</div>


<style type="text/css">
  table.dataTable th,
  table.dataTable td {
    white-space: nowrap;
    vertical-align: middle;
  }

  .dataTables_scrollHeadInner,
  .dataTables_scrollBody table {
    width: 100% !important;
  }

</style>



<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script> -->

<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>


<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min"></script>


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


<script>
  $(function() {
    $('.selectpicker').selectpicker();
  });
</script>

<script>
  function openTab(evt, tabName) {
    let i, tabcontent, tablinks;

  // sembunyikan semua konten
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // hapus active di semua tombol
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }

  // tampilkan tab yang dipilih
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.classList.add("active");
}

// buka default tab saat halaman load
document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".tablinks.active").click();
});
</script>




<script type="text/javascript">
  $(document).ready(function () {
    $('.tanggal').datepicker({
      format: "yyyy-mm-dd",
      autoclose:true
    });
  });
</script>


<script type="text/javascript">
  document.getElementById('btncreate').onclick = function () {
    location.href = "payable_card_statement.php";
  };
</script>

<script type="text/javascript">
  document.getElementById('reset').onclick = function () {
    location.href = "payable_card_statement.php";
  };
</script>



<!-- AP REPORT -->

<script type="text/javascript">
  function loadProjectionHeader() {
    let endDate = toYmd($('#end_date').val());
    // alert(endDate);
    $.ajax({
      url: 'ap_report/get-projection-month.php',
      type: 'POST',
      data: { end_date: endDate },
      dataType: 'json',
      success: function(res){

        for(let i=0;i<6;i++){
          $('#proj-month-'+(i+1)).text(res[i] ?? '-');
          $('#proj-month-kbon-'+(i+1)).text(res[i] ?? '-');
          $('#proj-month-lp-'+(i+1)).text(res[i] ?? '-');
          $('#proj-month-type1-'+(i+1)).text(res[i] ?? '-');
          $('#proj-month-type2-'+(i+1)).text(res[i] ?? '-');
          $('#proj-month-sum-supp-'+(i+1)).text(res[i] ?? '-');
        }

        datatable.columns.adjust();
        datatable2.columns.adjust();
      }
    });
  }


  $(document).ready(function(){
    loadProjectionHeader();
  });

</script>

<script>
  function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
  }

  let datatable = $("#table-pcs-bpb").DataTable({
    ordering: false,
    processing: true,
    serverSide: true,
    searching: true,
    info: true,
    autoWidth: false,
    scrollX: false,
    fixedColumns: {
        leftColumns: 5 // sampai kolom curr
      },
      paging: true,

      ajax: {
        url: 'ap_report/ajx_pcs_bpb.php',
        type: 'POST',
        data: function (d) {
          d.start_date = $('#start_date').val();
          d.end_date   = $('#end_date').val();
          d.nama_supp   = $('#nama_supp').val();
        }
      },

      columns: [
      { data: 'supplier' },
      { data: 'no_bpb' },
      { data: 'tgl_bpb' },
      { data: 'duedate' },
      { data: 'curr' },
      { data: 'saldo_awal' },
      { data: 'in_bpb' },
      { data: 'reverse_bpb' },
      { data: 'ded_kontrabon' },
      { data: 'reverse_kontrabon' },
      { data: 'gm' },
      { data: 'saldo_akhir' },
      { data: 'rate' },
      { data: 'saldo_akhir_idr' },
      { data: 'no_coa' },
      { data: 'nama_coa' },
      { data: 'item_type1' },
      { data: 'item_type2' },
      { data: 'relasi' },
      { data: null, defaultContent: '' },
      { data: 'due_current' },
      { data: 'due_1_30' },
      { data: 'due_31_60' },
      { data: 'due_61_90' },
      { data: 'due_91_120' },
      { data: 'due_121_180' },
      { data: 'due_181_360' },
      { data: 'due_gt_360' },
      { data: 'total_due' },
      { data: null, defaultContent: '' },
      { data: 'pro_due' },
      { data: 'pro_due0' },
      { data: 'pro_due1' },
      { data: 'pro_due2' },
      { data: 'pro_due3' },
      { data: 'pro_due4' },
      { data: 'pro_due5' },
      { data: 'tot_produe' }
      ],

      columnDefs: [

        /* ===============================
           FORMAT ACCOUNTING – REVERSE
           ================================*/
           {
            targets: [7, 8, 10], // reverse_bpb & reverse_kontrabon
            className: "text-right",
            render: function (data, type) {

              let val = parseFloat(data);
              if (isNaN(val) || val === 0) return '0.00';

                // selalu dianggap minus
                let absVal = Math.abs(val);

                if (type === 'display') {
                  return '<span style="color:red;">(' +
                  absVal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) +
                  ')</span>';
                }

                // untuk total & proses internal
                return -absVal;
              }
            },

            {
              targets: [5, 6,
              9, 11,
              13,
              20, 21, 22, 23, 24, 25, 26, 27, 28,
              30, 31, 32, 33, 34, 35, 36,
              37],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
            }
            ],

            footerCallback: function () {
              let api  = this.api();
              let json = api.ajax.json();

              if (!json) return;

              let footer = $(api.table().footer());
              let rowIDR = footer.find('tr:eq(0)');
              let rowUSD = footer.find('tr:eq(1)');
              let rowALL = footer.find('tr:eq(2)');

              function fmt(val) {
                val = parseFloat(val) || 0;
                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }

    // mapping kolom DataTable -> field backend
    const map = {
      5:  'saldo_awal',
      6:  'in_bpb',
      7:  'reverse_bpb',
      8:  'ded_kontrabon',
      9:  'reverse_kontrabon',
      10: 'gm',
      11: 'saldo_akhir',
      13: 'saldo_akhir_idr',
      20: 'due_current',
      21: 'due_1_30',
      22: 'due_31_60',
      23: 'due_61_90',
      24: 'due_91_120',
      25: 'due_121_180',
      26: 'due_181_360',
      27: 'due_gt_360',
      28: 'total_due',
      30: 'pro_due',
      31: 'pro_due0',
      32: 'pro_due1',
      33: 'pro_due2',
      34: 'pro_due3',
      35: 'pro_due4',
      36: 'pro_due5',
      37: 'tot_produe'
    };

    // ================= TOTAL IDR =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_idr[key] || 0;
      rowIDR.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= TOTAL USD =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_usd[key] || 0;
      rowUSD.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= SUMMARY TOTAL =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_all[key] || 0;
      rowALL.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= LABEL KIRI + COLSPAN =================
    rowIDR.find('th:eq(0)')
    .html('<b>TOTAL IDR</b>')
    .attr('colspan', 5)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowUSD.find('th:eq(0)')
    .html('<b>TOTAL USD</b>')
    .attr('colspan', 5)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowALL.find('th:eq(0)')
    .html('<b>SUMMARY TOTAL</b>')
    .attr('colspan', 13)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

// ================= SEMBUNYIKAN KOLOM 1–4 =================
for (let i = 1; i <= 4; i++) {
  rowIDR.find('th:eq(' + i + ')').css('display', 'none');
  rowUSD.find('th:eq(' + i + ')').css('display', 'none');
}

for (let i = 1; i <= 12; i++) {
  rowALL.find('th:eq(' + i + ')').css('display', 'none');
}


// ================= RATA KANAN ANGKA =================
rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
rowALL.find('th:not(:eq(0))').css('text-align', 'right');
}
,

initComplete: function () {
  this.api().columns.adjust();
}
});



$('#table-pcs-bpb').on('draw.dt', function () {
  datatable.columns.adjust();
});

//KONTRABON

let datatable2 = $("#table-pcs-kontrabon").DataTable({
  ordering: false,
  processing: true,
  serverSide: true,
  searching: true,
  info: true,
  autoWidth: false,
  scrollX: false,
  fixedColumns: {
        leftColumns: 5 // sampai kolom curr
      },
      paging: true,

      ajax: {
        url: 'ap_report/ajx_pcs_kontrabon.php',
        type: 'POST',
        data: function (d) {
          d.start_date = $('#start_date').val();
          d.end_date   = $('#end_date').val();
          d.nama_supp   = $('#nama_supp').val();
        }
      },

      columns: [
      { data: 'supplier' },
      { data: 'no_kbon' },
      { data: 'tgl_kbon' },
      { data: 'duedate' },
      { data: 'curr' },
      { data: 'saldo_awal' },
      { data: 'total_in' },
        // { data: 'pph' },
        { data: 'uang_muka' },
        { data: 'potongan' },
        { data: 'ded_lp' },
        { data: 'ded_gm' },
        { data: 'reverse_kontrabon' },
        { data: 'saldo_akhir' },
        { data: 'rate' },
        { data: 'saldo_akhir_idr' },
        { data: 'no_coa' },
        { data: 'nama_coa' },
        { data: 'item_type1' },
        { data: 'item_type2' },
        { data: 'relasi' },
        { data: null, defaultContent: '' },
        { data: 'due_current' },
        { data: 'due_1_30' },
        { data: 'due_31_60' },
        { data: 'due_61_90' },
        { data: 'due_91_120' },
        { data: 'due_121_180' },
        { data: 'due_181_360' },
        { data: 'due_gt_360' },
        { data: 'total_due' },
        { data: null, defaultContent: '' },
        { data: 'pro_due' },
        { data: 'pro_due0' },
        { data: 'pro_due1' },
        { data: 'pro_due2' },
        { data: 'pro_due3' },
        { data: 'pro_due4' },
        { data: 'pro_due5' },
        { data: 'tot_produe' }
        ],

        columnDefs: [

        /* ===============================
           FORMAT ACCOUNTING – REVERSE
           ================================*/
           {
            targets: [7, 8, 11], // reverse_bpb & reverse_kontrabon
            className: "text-right",
            render: function (data, type) {

              let val = parseFloat(data);
              if (isNaN(val) || val === 0) return '0.00';

                // selalu dianggap minus
                let absVal = Math.abs(val);

                if (type === 'display') {
                  return '<span style="color:red;">(' +
                  absVal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }) +
                  ')</span>';
                }

                // untuk total & proses internal
                return -absVal;
              }
            },

            {
              targets: [5, 6, 9, 10, 12, 13, 14, 21, 22, 23, 24, 25, 26, 27, 28,
              29, 31, 32, 33, 34, 35, 36, 37, 38],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
            }
            ],

            footerCallback: function () {
              let api  = this.api();
              let json = api.ajax.json();

              if (!json) return;

              let footer = $(api.table().footer());
              let rowIDR = footer.find('tr:eq(0)');
              let rowUSD = footer.find('tr:eq(1)');
              let rowALL = footer.find('tr:eq(2)');

              function fmt(val) {
                val = parseFloat(val) || 0;
                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }

    // mapping kolom DataTable -> field backend
    const map = {
      5: 'saldo_awal',
      6: 'total_in',
      7: 'uang_muka',
      8: 'potongan',
      9: 'ded_lp',
      10:'ded_gm',
      11:'reverse_kontrabon',
      12:'saldo_akhir',
      14:'saldo_akhir_idr',
      21:'due_current',
      22:'due_1_30',
      23:'due_31_60',
      24:'due_61_90',
      25:'due_91_120',
      26:'due_121_180',
      27:'due_181_360',
      28:'due_gt_360',
      29:'total_due',
      31:'pro_due',
      32:'pro_due0',
      33:'pro_due1',
      34:'pro_due2',
      35:'pro_due3',
      36:'pro_due4',
      37:'pro_due5',
      38:'tot_produe'
    };

    // ================= TOTAL IDR =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_idr[key] || 0;
      rowIDR.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= TOTAL USD =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_usd[key] || 0;
      rowUSD.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= SUMMARY TOTAL =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_all[key] || 0;
      rowALL.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= LABEL KIRI + COLSPAN =================
    rowIDR.find('th:eq(0)')
    .html('<b>TOTAL IDR</b>')
    .attr('colspan', 5)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowUSD.find('th:eq(0)')
    .html('<b>TOTAL USD</b>')
    .attr('colspan', 5)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowALL.find('th:eq(0)')
    .html('<b>SUMMARY TOTAL</b>')
    .attr('colspan', 14)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

// ================= SEMBUNYIKAN KOLOM 1–4 =================
for (let i = 1; i <= 4; i++) {
  rowIDR.find('th:eq(' + i + ')').css('display', 'none');
  rowUSD.find('th:eq(' + i + ')').css('display', 'none');
}

for (let i = 1; i <= 13; i++) {
  rowALL.find('th:eq(' + i + ')').css('display', 'none');
}


// ================= RATA KANAN ANGKA =================
rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
rowALL.find('th:not(:eq(0))').css('text-align', 'right');
}

// footerCallback: function () {
//     let api = this.api();
//     let json = api.ajax.json();

//     if (!json || !json.footer) return;

//     function fmt(val) {
//         val = parseFloat(val) || 0;
//         return val.toLocaleString('en-US', {
//             minimumFractionDigits: 2,
//             maximumFractionDigits: 2
//         });
//     }

//     const map = {
//     5: 'saldo_awal',
//     6: 'total_in',
//     7: 'uang_muka',
//     8: 'potongan',
//     9: 'ded_lp',
//     10:'ded_gm',
//     11:'reverse_kontrabon',
//     12:'saldo_akhir',
//     14:'saldo_akhir_idr',
//     21:'due_current',
//     22:'due_1_30',
//     23:'due_31_60',
//     24:'due_61_90',
//     25:'due_91_120',
//     26:'due_121_180',
//     27:'due_181_360',
//     28:'due_gt_360',
//     29:'total_due',
//     31:'pro_due',
//     32:'pro_due0',
//     33:'pro_due1',
//     34:'pro_due2',
//     35:'pro_due3',
//     36:'pro_due4',
//     37:'pro_due5',
//     38:'tot_produe'
// };


//     Object.keys(map).forEach(function (colIdx) {
//         let key = map[colIdx];
//         let val = json.footer[key];

//         $(api.column(colIdx).footer()).html(fmt(val));
//     });

//     $(api.column(0).footer()).html('<b>TOTAL</b>');
// }

,

initComplete: function () {
  this.api().columns.adjust();
}
});



$('#table-pcs-kontrabon').on('draw.dt', function () {
  datatable2.columns.adjust();
});


// LIST PAYMENT
let datatable3 = $("#table-pcs-listpayment").DataTable({
  ordering: false,
  processing: true,
  serverSide: true,
  searching: true,
  info: true,
  autoWidth: false,
  scrollX: false,
  fixedColumns: {
    leftColumns: 6
  },
  paging: true,

  ajax: {
    url: 'ap_report/ajx_pcs_listpayment.php',
    type: 'POST',
    data: function (d) {
      d.start_date = $('#start_date').val();
      d.end_date   = $('#end_date').val();
      d.nama_supp   = $('#nama_supp').val();
    }
  },

  columns: [
  { data: 'supplier' },
  { data: 'no_payment' },
  { data: 'tgl_payment' },
  { data: 'duedate' },
  { data: 'create_user' },
  { data: 'curr' },
  { data: 'saldo_awal' },
  { data: 'total_in' },
  { data: 'pay_bank' },
  { data: 'pay_non_bank' },
  { data: 'pay_cash' },
  { data: 'saldo_akhir' },
  { data: 'rate' },
  { data: 'saldo_akhir_idr' },
  { data: 'no_coa' },
  { data: 'nama_coa' },
  { data: 'item_type1' },
  { data: 'item_type2' },
  { data: 'relasi' },
  { data: null, defaultContent: '' },
  { data: 'due_current' },
  { data: 'due_1_30' },
  { data: 'due_31_60' },
  { data: 'due_61_90' },
  { data: 'due_91_120' },
  { data: 'due_121_180' },
  { data: 'due_181_360' },
  { data: 'due_gt_360' },
  { data: 'total_due' },
  { data: null, defaultContent: '' },
  { data: 'pro_due' },
  { data: 'pro_due0' },
  { data: 'pro_due1' },
  { data: 'pro_due2' },
  { data: 'pro_due3' },
  { data: 'pro_due4' },
  { data: 'pro_due5' },
  { data: 'tot_produe' }
  ],

  columnDefs: [

  {
    targets: [6, 7, 8, 9, 10, 11, 12, 13,
    20, 21, 22, 23, 24, 25, 26, 27, 28,
    30, 31, 32, 33, 34, 35, 36, 37],
    className: "text-right",
    render: function (data) {
      let val = parseFloat(data);
      if (isNaN(val)) return data;

      return val.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }
  ],

  footerCallback: function () {
              let api  = this.api();
              let json = api.ajax.json();

              if (!json) return;

              let footer = $(api.table().footer());
              let rowIDR = footer.find('tr:eq(0)');
              let rowUSD = footer.find('tr:eq(1)');
              let rowALL = footer.find('tr:eq(2)');

              function fmt(val) {
                val = parseFloat(val) || 0;
                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }

    // mapping kolom DataTable -> field backend
    const map = {
      6: 'saldo_awal',
      7: 'total_in',
      8: 'pay_bank',
      9: 'pay_non_bank',
      10:'pay_cash',
      11:'saldo_akhir',
      13:'saldo_akhir',
      20:'due_current',
      21:'due_1_30',
      22:'due_31_60',
      23:'due_61_90',
      24:'due_91_120',
      25:'due_121_180',
      26:'due_181_360',
      27:'due_gt_360',
      28:'total_due',
      30:'pro_due',
      31:'pro_due0',
      32:'pro_due1',
      33:'pro_due2',
      34:'pro_due3',
      35:'pro_due4',
      36:'pro_due5',
      37:'tot_produe'
    };

    // ================= TOTAL IDR =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_idr[key] || 0;
      rowIDR.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= TOTAL USD =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_usd[key] || 0;
      rowUSD.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= SUMMARY TOTAL =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_all[key] || 0;
      rowALL.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= LABEL KIRI + COLSPAN =================
    rowIDR.find('th:eq(0)')
    .html('<b>TOTAL IDR</b>')
    .attr('colspan', 6)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowUSD.find('th:eq(0)')
    .html('<b>TOTAL USD</b>')
    .attr('colspan', 6)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowALL.find('th:eq(0)')
    .html('<b>SUMMARY TOTAL</b>')
    .attr('colspan', 12)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

// ================= SEMBUNYIKAN KOLOM 1–4 =================
for (let i = 1; i <= 5; i++) {
  rowIDR.find('th:eq(' + i + ')').css('display', 'none');
  rowUSD.find('th:eq(' + i + ')').css('display', 'none');
}

for (let i = 1; i <= 11; i++) {
  rowALL.find('th:eq(' + i + ')').css('display', 'none');
}


// ================= RATA KANAN ANGKA =================
rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
rowALL.find('th:not(:eq(0))').css('text-align', 'right');
},

  initComplete: function () {
    this.api().columns.adjust();
  }
});



$('#table-pcs-listpayment').on('draw.dt', function () {
  datatable3.columns.adjust();
});


//SUMMARY SUPPLIER
let datatable4 = $("#table-pcs-sum-supp").DataTable({
    ordering: false,
    processing: true,
    serverSide: true,
    searching: true,
    info: true,
    autoWidth: false,
    scrollX: false,
    fixedColumns: {
        leftColumns: 2 // sampai kolom curr
      },
      paging: true,

      ajax: {
        url: 'ap_report/ajx_pcs_summary_supplier.php',
        type: 'POST',
        data: function (d) {
          d.start_date = $('#start_date').val();
          d.end_date   = $('#end_date').val();
          d.nama_supp   = $('#nama_supp').val();
        }
      },

      columns: [
      { data: 'supplier' },
      { data: 'curr' },
      { data: 'saldo_awal' },
      { data: 'addition' },
      { data: 'deduction_advance' },
      { data: 'deduction_other' },
      { data: 'deduction_lp' },
      { data: 'adjust' },
      { data: 'saldo_akhir' },
      { data: 'rate' },
      { data: 'saldo_akhir_idr' },
      { data: null, defaultContent: '' },
      { data: 'due_current' },
      { data: 'due_1_30' },
      { data: 'due_31_60' },
      { data: 'due_61_90' },
      { data: 'due_91_120' },
      { data: 'due_121_180' },
      { data: 'due_181_360' },
      { data: 'due_gt_360' },
      { data: 'total_due' },
      { data: null, defaultContent: '' },
      { data: 'pro_due' },
      { data: 'pro_due0' },
      { data: 'pro_due1' },
      { data: 'pro_due2' },
      { data: 'pro_due3' },
      { data: 'pro_due4' },
      { data: 'pro_due5' },
      { data: 'tot_produe' }
      ],

      columnDefs: [

            {
              targets: [2, 3, 4, 5, 6, 7, 8, 9, 10 ,12, 13, 14, 15, 16, 17, 18, 19, 20, 22, 23, 24, 25, 26, 27, 28, 29],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
            }
            ],

            footerCallback: function () {
              let api  = this.api();
              let json = api.ajax.json();

              if (!json) return;

              let footer = $(api.table().footer());
              let rowIDR = footer.find('tr:eq(0)');
              let rowUSD = footer.find('tr:eq(1)');
              let rowALL = footer.find('tr:eq(2)');

              function fmt(val) {
                val = parseFloat(val) || 0;
                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }

    // mapping kolom DataTable -> field backend
    const map = {
      2:  'saldo_awal',
      3:  'addition',
      4:  'deduction_advance',
      5:  'deduction_other',
      6:  'deduction_lp',
      7:  'adjust',
      8:  'saldo_akhir',
      10: 'saldo_akhir_idr',
      12: 'due_current',
      13: 'due_1_30',
      14: 'due_31_60',
      15: 'due_61_90',
      16: 'due_91_120',
      17: 'due_121_180',
      18: 'due_181_360',
      19: 'due_gt_360',
      20: 'total_due',
      22: 'pro_due',
      23: 'pro_due0',
      24: 'pro_due1',
      25: 'pro_due2',
      26: 'pro_due3',
      27: 'pro_due4',
      28: 'pro_due5',
      29: 'tot_produe'
    };

    // ================= TOTAL IDR =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_idr[key] || 0;
      rowIDR.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= TOTAL USD =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_usd[key] || 0;
      rowUSD.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= SUMMARY TOTAL =================
    Object.keys(map).forEach(function (colIdx) {
      let key = map[colIdx];
      let val = json.footer_all[key] || 0;
      rowALL.find('th:eq(' + (colIdx) + ')').html(fmt(val));
    });

    // ================= LABEL KIRI + COLSPAN =================
    rowIDR.find('th:eq(0)')
    .html('<b>TOTAL IDR</b>')
    .attr('colspan', 2)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowUSD.find('th:eq(0)')
    .html('<b>TOTAL USD</b>')
    .attr('colspan', 2)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

    rowALL.find('th:eq(0)')
    .html('<b>SUMMARY TOTAL</b>')
    .attr('colspan', 10)
    .css({
      'text-align': 'center',
      'font-weight': 'bold'
    });

// ================= SEMBUNYIKAN KOLOM 1–4 =================
for (let i = 1; i <= 1; i++) {
  rowIDR.find('th:eq(' + i + ')').css('display', 'none');
  rowUSD.find('th:eq(' + i + ')').css('display', 'none');
}

for (let i = 1; i <= 9; i++) {
  rowALL.find('th:eq(' + i + ')').css('display', 'none');
}


// ================= RATA KANAN ANGKA =================
rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
rowALL.find('th:not(:eq(0))').css('text-align', 'right');
}
,

initComplete: function () {
  this.api().columns.adjust();
}
});



$('#table-pcs-sum-supp').on('draw.dt', function () {
  datatable4.columns.adjust();
});

// ITEM TYPE 1
let datatable5 = $("#table-pcs-type1").DataTable({
  ordering: false,
  processing: true,
  serverSide: true,
  searching: true,
  info: true,
  autoWidth: false,
  scrollX: false,
  fixedColumns: {
    leftColumns: 6
  },
  paging: true,

  ajax: {
    url: 'ap_report/ajx_pcs_type1.php',
    type: 'POST',
    data: function (d) {
      d.start_date = $('#start_date').val();
      d.end_date   = $('#end_date').val();
      d.nama_supp   = $('#nama_supp').val();
    }
  },

  columns: [
  { data: 'supplier' },
  { data: 'item_type1' },
  { data: 'relasi' },
  { data: 'saldo_akhir' },
  { data: 'saldo_akhir_persen' },
  { data: null, defaultContent: '' },
  { data: 'due_current' },
  { data: 'due_1_30' },
  { data: 'due_31_60' },
  { data: 'due_61_90' },
  { data: 'due_91_120' },
  { data: 'due_121_180' },
  { data: 'due_181_360' },
  { data: 'due_gt_360' },
  { data: 'total_due' },
  { data: null, defaultContent: '' },
  { data: 'pro_due' },
  { data: 'pro_due0' },
  { data: 'pro_due1' },
  { data: 'pro_due2' },
  { data: 'pro_due3' },
  { data: 'pro_due4' },
  { data: 'pro_due5' },
  { data: 'tot_produe' }
  ],

  columnDefs: [

  {
    targets: [3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23],
    className: "text-right",
    render: function (data) {
      let val = parseFloat(data);
      if (isNaN(val)) return data;

      return val.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }
  ],


footerCallback: function () {
    let api = this.api();
    let json = api.ajax.json();

    if (!json || !json.footer) return;

    function fmt(val) {
        val = parseFloat(val) || 0;
        return val.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    const map = {
    3:'saldo_akhir_idr',
      6:'due_current',
      7:'due_1_30',
      8:'due_31_60',
      9:'due_61_90',
      10:'due_91_120',
      11:'due_121_180',
      12:'due_181_360',
      13:'due_gt_360',
      14:'total_due',
      16:'pro_due',
      17:'pro_due0',
      18:'pro_due1',
      19:'pro_due2',
      20:'pro_due3',
      21:'pro_due4',
      22:'pro_due5',
      23:'tot_produe'
};


    Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer[key];

        $(api.column(colIdx).footer()).html(fmt(val));
    });

    $(api.column(0).footer()).html('<b>TOTAL</b>');
},

  initComplete: function () {
    this.api().columns.adjust();
  }
});



$('#table-pcs-type1').on('draw.dt', function () {
  datatable5.columns.adjust();
});



// ITEM TYPE 2
let datatable6 = $("#table-pcs-type2").DataTable({
  ordering: false,
  processing: true,
  serverSide: true,
  searching: true,
  info: true,
  autoWidth: false,
  scrollX: false,
  fixedColumns: {
    leftColumns: 6
  },
  paging: true,

  ajax: {
    url: 'ap_report/ajx_pcs_type2.php',
    type: 'POST',
    data: function (d) {
      d.start_date = $('#start_date').val();
      d.end_date   = $('#end_date').val();
      d.nama_supp   = $('#nama_supp').val();
    }
  },

  columns: [
  { data: 'supplier' },
  { data: 'item_type2' },
  { data: 'relasi' },
  { data: 'saldo_akhir' },
  { data: 'saldo_akhir_persen' },
  { data: null, defaultContent: '' },
  { data: 'due_current' },
  { data: 'due_1_30' },
  { data: 'due_31_60' },
  { data: 'due_61_90' },
  { data: 'due_91_120' },
  { data: 'due_121_180' },
  { data: 'due_181_360' },
  { data: 'due_gt_360' },
  { data: 'total_due' },
  { data: null, defaultContent: '' },
  { data: 'pro_due' },
  { data: 'pro_due0' },
  { data: 'pro_due1' },
  { data: 'pro_due2' },
  { data: 'pro_due3' },
  { data: 'pro_due4' },
  { data: 'pro_due5' },
  { data: 'tot_produe' }
  ],

  columnDefs: [

  {
    targets: [3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23],
    className: "text-right",
    render: function (data) {
      let val = parseFloat(data);
      if (isNaN(val)) return data;

      return val.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  }
  ],


footerCallback: function () {
    let api = this.api();
    let json = api.ajax.json();

    if (!json || !json.footer) return;

    function fmt(val) {
        val = parseFloat(val) || 0;
        return val.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    const map = {
    3:'saldo_akhir_idr',
      6:'due_current',
      7:'due_1_30',
      8:'due_31_60',
      9:'due_61_90',
      10:'due_91_120',
      11:'due_121_180',
      12:'due_181_360',
      13:'due_gt_360',
      14:'total_due',
      16:'pro_due',
      17:'pro_due0',
      18:'pro_due1',
      19:'pro_due2',
      20:'pro_due3',
      21:'pro_due4',
      22:'pro_due5',
      23:'tot_produe'
};


    Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer[key];

        $(api.column(colIdx).footer()).html(fmt(val));
    });

    $(api.column(0).footer()).html('<b>TOTAL</b>');
},

  initComplete: function () {
    this.api().columns.adjust();
  }
});



$('#table-pcs-type2').on('draw.dt', function () {
  datatable6.columns.adjust();
});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });

  datatable2.ajax.reload(()=>{
    datatable2.columns.adjust();
  });

  datatable3.ajax.reload(()=>{
    datatable3.columns.adjust();
  });

  datatable4.ajax.reload(()=>{
    datatable4.columns.adjust();
  });

  datatable5.ajax.reload(()=>{
    datatable5.columns.adjust();
  });

  datatable6.ajax.reload(()=>{
    datatable6.columns.adjust();
  });
}

document.getElementById('btnExportExcel_bpb').addEventListener('click', function(e) {
  let sd = toYmd(document.getElementById('start_date').value);
  let ed = toYmd(document.getElementById('end_date').value);
  let supp = document.getElementById('nama_supp').value;

  this.href = `ap_report/ekspor_pcs_bpb_new.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
});

document.getElementById('btnExportExcel_kontrabon').addEventListener('click', function(e) {
  let sd = toYmd(document.getElementById('start_date').value);
  let ed = toYmd(document.getElementById('end_date').value);
  let supp = document.getElementById('nama_supp').value;

  this.href = `ap_report/ekspor_pcs_kbon.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
});

document.getElementById('btnExportExcel_listpayment').addEventListener('click', function(e) {
  let sd = toYmd(document.getElementById('start_date').value);
  let ed = toYmd(document.getElementById('end_date').value);
  let supp = document.getElementById('nama_supp').value;

  this.href = `ap_report/ekspor_pcs_listpayment.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
});

</script>


</body>

</html>
