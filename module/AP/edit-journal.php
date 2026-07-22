<?php include '../header.php' ?>

<style type="text/css">

/* ===============================
   GENERAL FORM
================================*/
label{
    font-size:14px;
}

input{
    font-size:14px;
}

#modalBodyJournal{
    overflow-x:auto;
    display:flex;
    flex-direction:column;
    flex:1 1 auto;
    min-height:0;
}

.row-error{
    background:#FA9B9B !important;
}

.input-error{
    border:2px solid #FA9B9B !important;
}



/* ===============================
   MODAL SIZE - FULL SCREEN
   (biar berasa seperti tab/halaman baru, bukan popup kecil)
================================*/
#modalEdit.modal{
    padding:0 !important;
}

#modalEdit .modal-dialog{
    max-width:100%;
    width:100%;
    height:100%;
    margin:0;
}

#modalEdit .modal-content{
    height:100%;
    border:0;
    border-radius:0;
}

#modalEdit .modal-body{
    max-height:none;
    overflow-y:auto;
    flex:1 1 auto;
    display:flex;
    flex-direction:column;
    padding-top:10px;
}

#modalEdit .modal-body .form-row:first-child{
    margin-top:0;
}

#modalEdit .modal-body .form-row:first-child .col-md-3,
#modalEdit .modal-body .form-row:first-child .col-md-2{
    margin-bottom:10px;
}

/* Rantai flex biar tinggi mengalir sampai ke .table-scroll di bawah -
   tanpa ini, form#formEdit & #modalBodyJournal cuma setinggi isinya
   sendiri (bukan ikut tinggi modal), jadi flex:1 di .table-scroll
   tidak ada ruang buat "mengembang". */
#modalEdit .modal-body > form{
    display:flex;
    flex-direction:column;
    flex:1 1 auto;
    min-height:0;
}

#modalEdit .modal-header{
    padding:0.6rem 1rem;
    flex-shrink:0;
}

#modalEdit .modal-title{
    font-size:1.05rem;
    margin:0;
}

#modalEdit .modal-header .close{
    padding:0;
    margin:0;
    font-size:2rem;
    line-height:1;
    opacity:0.85;
}

#modalEdit .modal-header .close:hover{
    opacity:1;
}

#modalEdit .modal-footer{
    flex-shrink:0;
}

/* ===============================
   TABLE STYLE
================================*/
#modalEdit table{
    font-size:14px;
}

#modalEdit table th{
    font-size:14px;
    font-weight:600;
}

#modalEdit table td{
    font-size:14px;
}

#modalEdit table input,
#modalEdit table select{
    font-size:14px;
    height:36px;
}

/* Input/select lebih nyaman dilihat & dipakai: sudut lebih halus, transisi
   warna saat fokus/hover, biar user gampang lihat lagi ada di kolom mana
   pas ngisi banyak baris. */
#tableJournal .form-control{
    border-radius:6px;
    border-color:#dfe3e8;
    transition:border-color .15s ease, box-shadow .15s ease;
}

#tableJournal .form-control:hover{
    border-color:#b9c2cc;
}

#tableJournal .form-control:focus{
    border-color:#1e90ff;
    box-shadow:0 0 0 3px rgba(30,144,255,0.15);
}

#tableJournal .select2-container--default .select2-selection--single{
    border-radius:6px;
    border-color:#dfe3e8;
    transition:border-color .15s ease, box-shadow .15s ease;
}

#tableJournal .select2-container--default.select2-container--open .select2-selection--single,
#tableJournal .select2-container--default.select2-container--focus .select2-selection--single{
    border-color:#1e90ff;
    box-shadow:0 0 0 3px rgba(30,144,255,0.15);
}


/* ===============================
   TABLE JOURNAL SCROLL
   flex:1 bikin area tabel ini otomatis mengisi sisa ruang vertikal
   modal (bukan tinggi tetap 420px) - jadi menyesuaikan sendiri baik di
   layar kecil (14") maupun besar (21"+), nggak nyisain kotak kosong
   di atas footer kalau layarnya tinggi/lebar.
================================*/
.table-scroll{
    flex:1 1 auto;
    min-height:220px;
    overflow-y:auto;
    overflow-x:auto;
    border:1px solid #ddd;
    border-radius:8px;
}

#tableJournal{
    min-width:1400px;
    width:100%;
}

#tableJournal thead th{
    position:sticky;
    top:0;
    background:#1E3A8A;
    color:#fff;
    font-weight:600;
    z-index:2;
}

#tableJournal td,
#tableJournal th{
    white-space:nowrap;
    padding:10px 8px;
}

/* Zebra + hover, biar baris gampang diikuti mata pas datanya banyak */
#tableJournal tbody tr:nth-child(even){
    background:#f9fafb;
}

#tableJournal tbody tr:hover{
    background:#eef6ff;
}

#tableJournal tbody tr.row-error:hover{
    background:#FA9B9B;
}


/* ===============================
   COLUMN WIDTH
================================*/
#modalEdit table th:nth-child(2),
#modalEdit table td:nth-child(2){
    width:220px;
    min-width:220px;
}

#modalEdit table th:nth-child(15),
#modalEdit table td:nth-child(15){
    width:200px;
    min-width:200px;
}

#modalEdit table th:nth-child(13),
#modalEdit table td:nth-child(13),
#modalEdit table th:nth-child(14),
#modalEdit table td:nth-child(14){
    width:130px;
    min-width:130px;
}

/* Curr (kolom ke-9) - dulu lebarnya cuma ngikutin native <select> yang sudah
   disembunyikan select2, jadi kolomnya menyempit sampai teks "IDR"/"USD"
   meluber keluar kotak. Kasih lebar tetap. */
#modalEdit table th:nth-child(9),
#modalEdit table td:nth-child(9){
    width:110px;
    min-width:110px;
}

#modalEdit textarea{
    overflow:hidden;
    resize:none;
    width:100%;
}



/* ===============================
   INPUT WIDTH
================================*/
#tableJournal select{
    min-width:140px;
}

#tableJournal input[type="text"],
#tableJournal input[type="number"]{
    min-width:120px;
}


/* ===============================
   CHECKBOX NORMAL SIZE
================================*/
#tableJournal input[type="checkbox"]{
    width:16px;
    height:16px;
}


/* ===============================
   SELECT2 STYLE
================================*/
#modalEdit .select2-container{
    width:100% !important;
    font-size:14px;
}

/* Samakan tinggi select2 dengan input/select lain (36px), dan pastikan
   teks yang kepanjangan dipotong rapi (...) bukan meluber ke luar kotak. */
#modalEdit .select2-container .select2-selection--single{
    height:36px;
    display:flex;
    align-items:center;
}

#modalEdit .select2-container .select2-selection--single .select2-selection__rendered{
    line-height:normal;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    padding-left:8px;
}

#modalEdit .select2-container .select2-selection--single .select2-selection__arrow{
    height:34px;
}

#modalEdit .select2-dropdown{
    width:auto !important;
    min-width:250px !important;
    max-width:420px !important;
}

#modalEdit .select2-results__options{
    max-height:220px !important;
    overflow-y:auto !important;
}

#modalEdit .select2-search--dropdown .select2-search__field{
    width:100% !important;
    font-size:14px;
}


/* ===============================
   SELECT WIDTH
================================*/
select.sel-coa,
select.sel-cc,
select.sel-buyer,
select.sel-ws,
select.sel-curr{
    width:100%;
}

#tableJournal tfoot td{
    position: sticky;
    bottom: 0;
    background: #f8f9fa;
    z-index: 3;
    border-top: 2px solid #ccc;
    padding:10px;
}

textarea.ket{
    width:100%;
    min-height:36px;
    resize:none;
    overflow:hidden;
    font-size:14px;
}


</style>




<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-edit"></i> EDIT JOURNAL</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="edit-journal.php" method="post">
    <div class="row g-3">

   <!-- Start Date -->
    <div class="col-md-2
    ">
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
   <div class="col-md-2
   ">
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
<div class="col-md-2 d-flex align-items-end">
      <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>
<!-- <a id="btnExportPrepaidTax" target="_blank">
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" > Excel</i>
    </button>
</a> -->
  

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="table-data" 
          class="table table-striped table-bordered table-hover table-sm nowrap" >
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">No Journal</th>
                <th style="text-align: center;vertical-align: middle;">Date</th>
                <th style="text-align: center;vertical-align: middle;">Type</th>
                <th style="text-align: center;vertical-align: middle;">curr</th>
                <th style="text-align: center;vertical-align: middle;">Debit</th>
                <th style="text-align: center;vertical-align: middle;">Credit</th>
                <th style="text-align: center;vertical-align: middle;">Debit IDR</th>
                <th style="text-align: center;vertical-align: middle;">Credit IDR</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
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

/* ===============================
   TOTAL CARDS (NAG / NAK / Grand Total)
   Redesain dari kotak input abu-abu polos jadi card dengan header
   berwarna + angka besar, biar nggak kaku kayak form. id input di
   dalamnya TIDAK berubah, jadi setVal()/hitungTotal() di JS tetap
   jalan tanpa perlu diubah.
================================*/
.total-box{
    border:0;
    border-radius:12px;
    padding:0;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    height:100%;
}

.total-box .total-box-header{
    padding:12px 16px;
    color:#fff;
    font-weight:700;
    font-size:14px;
    display:flex;
    align-items:center;
    gap:8px;
}

.total-box.tone-nag .total-box-header{
    background:linear-gradient(90deg, #191970, #1e90ff);
}

.total-box.tone-nak .total-box-header{
    background:linear-gradient(90deg, #0f5c3f, #1f8a4c);
}

.total-box.tone-all .total-box-header{
    background:linear-gradient(90deg, #1E3A8A, #1E3A8A);
}

.total-box .total-box-body{
    padding:16px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.total-stat{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    padding-bottom:12px;
    border-bottom:1px dashed #e5e5e5;
}

.total-stat:last-child{
    border-bottom:0;
    padding-bottom:0;
}

.total-stat-label{
    font-size:12px;
    font-weight:600;
    color:#8a8a8a;
    text-transform:uppercase;
    letter-spacing:.03em;
}

.total-stat-value-wrap{
    text-align:right;
}

.total-stat input.total-stat-value{
    border:0;
    background:transparent;
    padding:0;
    font-size:19px;
    font-weight:700;
    text-align:right;
    width:auto;
    max-width:100%;
    height:auto;
}

/* Soft/muted, bukan warna terang - biar tetap gampang dibedain Debit vs
   Credit sekilas tapi kesannya tenang, cocok buat dokumen akuntansi
   (bukan dashboard). */
.total-stat.is-debit input.total-stat-value{
    color:#3d5a80;
}

.total-stat.is-credit input.total-stat-value{
    color:#4d7d63;
}

.total-stat-sub{
    font-size:16px;
    font-weight:700;
    color:#495057;
    margin-top:4px;
}

.total-stat-sub input.total-stat-value-sm{
    border:0;
    background:transparent;
    padding:0;
    font-size:16px;
    font-weight:700;
    color:#495057;
    text-align:right;
    width:auto;
    max-width:100%;
    height:auto;
}





</style>

<!-- Modal EDIT (full screen, tampil seperti halaman/tab baru) -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Journal
        </h5>
      </div>

      <div class="modal-body">

        <form id="formEdit">

          <div class="modal-body" id="modalBodyJournal">
Loading...
</div>

        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          Close
        </button>

        <button type="button" class="btn btn-success" onclick="SaveEdit()">
          <i class="fa fa-save"></i> Save Changes
        </button>
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

<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>


<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min"></script>
<script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <!--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


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
    $(document).ready(function () {
        $('.tgl_edit').datepicker({
            format: "yyyy-mm-dd",
            startDate : "2025-01-01",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tgl_new').datepicker({
            format: "yyyy-mm-dd",
            startDate : "2025-01-01",
            autoclose:true
        });
    });
</script>


<script>
    $(function() {
      $('.select2').select2()
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
  function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
  }

  let datatable = $("#table-data").DataTable({
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
        url: 'ajx_edit_journal.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
      { data: 'no_journal' },
      { data: 'tgl_journal' },
      { data: 'type_journal' },
      { data: 'curr' },
      { data: 'debit' },
      { data: 'credit' },
      { data: 'debit_idr' },
      { data: 'credit_idr' },
      { data: 'keterangan' },
      { data: 'no_journal' },
      ],

      columnDefs: [

            {
              targets: [4, 5, 6, 7],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
            },

            {
  targets: -1,
  orderable: false,
  className: "text-center",
  render: function (data, type, row) {

    if (row.status_closing && row.status_closing.toLowerCase() === 'closed') {
  return `
    <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
      <span class="badge badge-danger" style="font-size:11px; padding:5px 8px;">
        <i class="fa fa-lock"></i> PERIOD LOCKED
      </span>
      <small style="color:#888;">Open period to edit</small>
    </div>
  `;
}


    return `
      <button class="btn btn-xs btn-primary"
        onclick="editData('${row.no_journal}')">
        <i class="fa fa-edit"></i> Update
      </button>

      <button class="btn btn-xs btn-danger"
        onclick="cancelData('${row.no_journal}')">
        <i class="fa fa-times"></i> Cancel
      </button>
    `;
  }
}


            ],

            

initComplete: function () {
  this.api().columns.adjust();
}
});


$('#table-pcs-bpb').on('draw.dt', function () {
  datatable.columns.adjust();
});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });
}


/* ===============================
   SELECT2 INIT (eager - langsung aktif saat baris dirender/ditambah,
   bukan nunggu diklik dulu). Sebelumnya select2 baru diaktifkan saat
   elemen diklik: itu bikin native <select> sempat kebuka duluan lalu
   "dihijack" jadi select2 sepersekian detik kemudian - kelihatan
   ngaco/kedap-kedip. Sekarang semua select langsung jadi select2 sejak
   baris muncul, konsisten.
================================*/

const SELECT2_CONFIG = {
    'sel-coa'  : { url:'coa.php',   minInput:1 },
    'sel-cc'   : { url:'cc.php',    minInput:1 },
    'sel-pc'   : { url:'pc.php',    minInput:0 },
    'sel-buyer': { url:'buyer.php', minInput:1 },
    'sel-ws'   : { url:'ws.php',    minInput:1 },
    'sel-curr' : { url:'curr.php',  minInput:0 }
};

function initSelect2Element(el){

    if(el.data('select2')) return;

    let cssClass = Object.keys(SELECT2_CONFIG).find(function(c){
        return el.hasClass(c);
    });

    if(!cssClass) return;

    let cfg = SELECT2_CONFIG[cssClass];

    el.select2({
        width:'100%',
        dropdownParent: $('#modalEdit'),
        dropdownAutoWidth:true,
        placeholder: cssClass === 'sel-cc' ? 'Select Profit Center first' : 'Nothing Selected',
        allowClear:true,
        minimumInputLength:cfg.minInput,
        ajax:{
            url:cfg.url,
            dataType:'json',
            delay:250,
            cache:true,
            data:function(params){

                let data = { q: params.term };

                // Cost Center dibatasi sesuai Profit Center baris ini
                // (cc.php akan kosong kalau pc belum dipilih)
                if(cssClass === 'sel-cc'){
                    data.pc = el.closest('tr').find('.sel-pc').val() || '';
                }

                return data;
            },
            processResults:function(data){
                return { results:data };
            }
        }
    });

}

// Init semua select2 di dalam suatu scope (satu baris, atau seluruh tabel)
function initAllSelect2(scope){

    $(scope || document)
        .find('.sel-coa,.sel-cc,.sel-pc,.sel-buyer,.sel-ws,.sel-curr')
        .each(function(){
            initSelect2Element($(this));
        });

}



$.fn.modal.Constructor.prototype.enforceFocus = function() {};

$(document).ready(function(){


/* ===============================
   RESET COST CENTER SAAT PROFIT CENTER GANTI
   (Cost Center dibatasi per Profit Center - kalau PC-nya diganti,
   Cost Center yang sudah dipilih sebelumnya bisa jadi sudah tidak
   valid buat PC yang baru, jadi dikosongkan lagi)
================================ */

$(document).on('change','.sel-pc',function(){

    let tr = $(this).closest('tr');
    let ccEl = tr.find('.sel-cc');

    if(ccEl.length && ccEl.val()){
        ccEl.val(null).trigger('change');
    }

});


/* ===============================
   ADD ROW
================================ */

$(document).on('click','#btnAdd',function(){

    let r=$("#templateRow").clone().removeAttr("id").show();

    $("#tbody2").append(r);

    initAllSelect2(r);

});


/* ===============================
   INSERT ROW
================================ */

$(document).on('click','#btnInsert',function(){

    let r=$("#templateRow").clone().removeAttr("id").show();

    let firstRow = $("#tbody2 tr:first");

    if(firstRow.length){
        firstRow.before(r);
    }else{
        // tbody2 kosong (semua baris sudah dihapus) - tr:first tidak
        // ada apa-apanya, .before() diam-diam tidak melakukan apapun.
        // Fallback ke append supaya baris tetap masuk.
        $("#tbody2").append(r);
    }

    initAllSelect2(r);

});


/* ===============================
   DELETE ROW
================================ */

$(document).on('click','#btnDelete',function(){

    let checked = $("#tbody2 .remove:checked");

    if(checked.length === 0){
        Swal.fire({
            icon:'info',
            title:'No row selected',
            text:'Please check the Action column on the row(s) you want to delete first'
        });
        return;
    }

    checked.each(function(){

        $(this).closest("tr").remove();

    });

    hitungTotal();

});


});


/* ===============================
   OPEN EDIT MODAL
================================ */


function editData(no_journal) {

  Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  $.ajax({
    url: "get_edit_journal.php",
    type: "POST",
    data: { no_journal:no_journal },

    success: function(res){

      Swal.close();

      $("#modalBodyJournal").html(res);

      $('#modalEdit').modal('show');

      $('#modalEdit').modal({
    backdrop: 'static',
    keyboard: false
});


      // jalankan setelah html masuk
      initJournalUI();

    },

    error:function(){

      Swal.close();

      Swal.fire({
        icon:'error',
        title:'Error',
        text:'Failed to load data'
      });

    }

  });

}


function cancelData(no_journal) {

    Swal.fire({
        title: 'Are you sure you want to cancel?',
        text: "This journal entry will be cancelled!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: 'cancel_journal_all.php',
                type: 'POST',
                data: {
                    no_journal: no_journal,
                    cancel_user: '<?= $user ?>'
                },
                dataType: 'json',
                success: function(res) {

                    if (res.status == 'success') {
                        Swal.fire(
                            'Success!',
                            res.message,
                            'success'
                        ).then(() => {
                            $('#table-data').DataTable().ajax.reload(null,false);
                        });
                    } else {
                        Swal.fire(
                            'Failed!',
                            res.message,
                            'error'
                        );
                    }

                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'A server error occurred',
                        'error'
                    );
                }
            });

        }
    });
}


function initJournalUI(){

  // select2-kan dropdown Type di header (sebelumnya cuma dikasih class
  // "select2bs4" tapi tidak pernah benar-benar di-init jadi select2).
  // Pakai tema default (bukan 'bootstrap4') karena CSS tema bootstrap4
  // untuk select2 tidak dimuat di halaman ini - sama seperti select2
  // lain di tabel journal, supaya stylingnya konsisten.
  if(!$('#nama_type').data('select2')){
      $('#nama_type').select2({
          width:'100%',
          dropdownParent: $('#modalEdit')
      });
  }

  // select2-kan semua select yang sudah ke-render dari server
  initAllSelect2('#tbody2');

  // hitung semua row yang sudah ada
  document.querySelectorAll("#tbody2 tr").forEach(row => {

    let debitInput = row.querySelector(".debit");
    let creditInput = row.querySelector(".credit");

    if(debitInput){
        updateRow(debitInput);
    }else if(creditInput){
        updateRow(creditInput);
    }

  });

  // auto resize textarea
  $('.ket').each(function(){
    autoResizeTextarea(this);
  });

  // datepicker
  $('.tanggal').datepicker({
      format: "dd-mm-yyyy",
      startDate : "01-01-2022",
      autoclose:true
  });

}


function autoResizeTextarea(el){
    el.style.height = "auto";
    el.style.height = el.scrollHeight + "px";
}


$(document).on("input",".ket",function(){
    autoResizeTextarea(this);
});

$('.ket').each(function(){
    autoResizeTextarea(this);
});

function setVal(id,val){
    let el = document.getElementById(id);
    if(el) el.value = formatNumber(val);
}

function formatNumber(num){
    return new Intl.NumberFormat("en-US",{
        minimumFractionDigits:2,
        maximumFractionDigits:2
    }).format(num);
}

function toNumber(val){
    if(!val) return 0;

    return parseFloat(
        val.toString()
           .replace(/,/g,'')
           .replace(/\s/g,'')
    ) || 0;
}





function updateRow(el){

    let row = el.closest("tr");

    let debitInput  = row.querySelector(".debit");
    let creditInput = row.querySelector(".credit");
    let rateInput   = row.querySelector("input[name='rate[]']");
    let currSelect  = row.querySelector(".sel-curr");
    let curr        = currSelect ? currSelect.value : "";

    let debit  = toNumber(debitInput.value);
    let credit = toNumber(creditInput.value);
    let rate   = toNumber(rateInput.value) || 1;

    // =========================
    // RULE DEBIT CREDIT
    // =========================

    if(el.classList.contains("debit") || el.classList.contains("credit")){

        if(debit > 0){
            creditInput.value = "";
            creditInput.disabled = true;
            debitInput.disabled = false;
        }
        else if(credit > 0){
            debitInput.value = "";
            debitInput.disabled = true;
            creditInput.disabled = false;
        }
        else{
            debitInput.disabled = false;
            creditInput.disabled = false;
        }

    }

    // =========================
    // RULE RATE
    // =========================

    if(curr){

        if(curr.toLowerCase() === "idr"){

            rateInput.value = 1;
            rateInput.readOnly = true;
            rate = 1;

        }else{

            rateInput.readOnly = false;

            if(rate <= 0){
                rateInput.value = "";
                rate = 0;
            }

        }

    }

    // =========================
    // HITUNG IDR
    // =========================

    let debit_idr  = debit * rate;
    let credit_idr = credit * rate;

    row.dataset.debit_idr  = debit_idr;
    row.dataset.credit_idr = credit_idr;

    let debitIdrInput  = row.querySelector(".debit_idr");
    let creditIdrInput = row.querySelector(".credit_idr");

    if(debitIdrInput)  debitIdrInput.value  = debit_idr  ? formatNumber(debit_idr)  : "";
    if(creditIdrInput) creditIdrInput.value = credit_idr ? formatNumber(credit_idr) : "";

    hitungTotal();
}




function hitungTotal(){

    let total = {
        nag:{debit:0,credit:0,debit_idr:0,credit_idr:0},
        nak:{debit:0,credit:0,debit_idr:0,credit_idr:0},
        all:{debit:0,credit:0,debit_idr:0,credit_idr:0}
    };

    document.querySelectorAll("#tbody2 tr").forEach(row=>{

        // Grand Total ini cuma tampilan running total buat bantu user pantau
        // balance sambil ngisi form - jadi TIDAK perlu nunggu Profit
        // Center/COA/Currency lengkap dulu (itu baru divalidasi & diwajibkan
        // saat SaveEdit()). Kalau disyaratkan lengkap, baris yang baru
        // ditambah (Add Row) tapi belum sempat dipilih PC/COA/Curr-nya jadi
        // hilang dari Grand Total meski Debit/Credit-nya sudah diisi.
        let pc = row.querySelector(".sel-pc")?.value;

        let debit  = toNumber(row.querySelector(".debit")?.value);
        let credit = toNumber(row.querySelector(".credit")?.value);

        // skip jika kosong
        if(debit === 0 && credit === 0){
            return;
        }

        let debit_idr  = toNumber(row.dataset.debit_idr);
        let credit_idr = toNumber(row.dataset.credit_idr);

        // ALL
        total.all.debit      += debit;
        total.all.credit     += credit;
        total.all.debit_idr  += debit_idr;
        total.all.credit_idr += credit_idr;

        // NAG
        if(pc === "NAG"){
            total.nag.debit      += debit;
            total.nag.credit     += credit;
            total.nag.debit_idr  += debit_idr;
            total.nag.credit_idr += credit_idr;
        }

        // NAK
        if(pc === "NAK"){
            total.nak.debit      += debit;
            total.nak.credit     += credit;
            total.nak.debit_idr  += debit_idr;
            total.nak.credit_idr += credit_idr;
        }

    });

    // =================
    // SET VALUE
    // =================

    setVal("txt_debit_nag", total.nag.debit);
    setVal("txt_credit_nag", total.nag.credit);
    setVal("txt_debit_nag_idr", total.nag.debit_idr);
    setVal("txt_credit_nag_idr", total.nag.credit_idr);

    setVal("txt_debit_nak", total.nak.debit);
    setVal("txt_credit_nak", total.nak.credit);
    setVal("txt_debit_nak_idr", total.nak.debit_idr);
    setVal("txt_credit_nak_idr", total.nak.credit_idr);

    setVal("txt_debit_all", total.all.debit);
    setVal("txt_credit_all", total.all.credit);
    setVal("txt_debit_all_idr", total.all.debit_idr);
    setVal("txt_credit_all_idr", total.all.credit_idr);

}





function AddData() {

$('#modalNew').modal('show');
}


function validasiPeriode() {

    var awal  = $('#edit_tgl_awal').val();
    var akhir = $('#edit_tgl_akhir').val();

    if(awal !== '' && akhir !== '') {

        if(akhir < awal){

            Swal.fire({
                icon: 'warning',
                title: 'Invalid Period',
                text: 'End date cannot be earlier than the start date'
            });

            $('#edit_tgl_akhir').val(lastValidAkhir);
            $('#edit_tgl_awal').val(lastValidAwal);
            return false;
        }

        lastValidAkhir = akhir;
        lastValidAwal = awal;
    }

    return true;
}


function validasiPeriodeNew() {

    var awal  = $('#new_tgl_awal').val();
    var akhir = $('#new_tgl_akhir').val();

    if(awal !== '' && akhir !== '') {

        if(akhir < awal){

            Swal.fire({
                icon: 'warning',
                title: 'Invalid Period',
                text: 'End date cannot be earlier than the start date'
            });

            $('#new_tgl_akhir').val(lastValidAkhirNew);
            $('#new_tgl_awal').val(lastValidAwalNew);
            return false;
        }

        lastValidAkhirNew = akhir;
        lastValidAwalNew = awal;
    }

    return true;
}




let coaWajibCC = [];

// Load sekali saat halaman dibuka
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
});


function SaveEdit(){

  console.clear(); // DEBUG
  console.log("===== SAVE EDIT START ====="); // DEBUG

  let btn = $("#btnSaveEdit");

  // Loading swal dipindah ke setelah validasi + konfirmasi selesai (lihat
  // bawah) - sebelumnya loading swal ini dibuka duluan di sini, jadi kalau
  // ada error validasi, Swal.fire() berikutnya cuma nge-update swal yang
  // sudah kebuka itu tanpa mereset state showLoading()-nya, hasilnya alert
  // error muncul tanpa tombol Close (kelihatan nyangkut/nggak bisa ditutup).

  function resetButton(){
      btn.prop("disabled", false)
         .html('<i class="fa fa-save"></i> Save Changes');
  }

  function toNumber(val){
      if(!val) return 0;
      return parseFloat(val.toString().replace(/,/g,'')) || 0;
  }

  function round2(num){
      return Math.round((num + Number.EPSILON) * 100) / 100;
  }

  let no_journal  = $("#no_doc").val();
  let tgl_journal = $("#tgl_doc").val();
  let type_journal = $("#nama_type").val();
  let create_user = '<?php echo $user; ?>';

  console.log("HEADER DATA", { // DEBUG
      no_journal,
      tgl_journal,
      type_journal,
      create_user
  });

  let rows = [];
  let totalPC = {};
  let rowIndex = 0;
  let hasError = false;

  $("#tbody2 tr").each(function(){

      if(hasError) return false;

      rowIndex++;

      let tr = $(this);

      tr.removeClass("row-error");
      tr.find("select,input,textarea").removeClass("input-error");

      let profit_center = tr.find("select[name='profit_center[]']").val();
      let coa = tr.find("select[name='nomor_coa[]']").val();
      let costcenter = tr.find("select[name='nomor_cc[]']").val();

      let curr = tr.find("select[name='currenc[]']").val();
      let rate = round2(toNumber(tr.find("input[name='rate[]']").val()));

      let debit  = round2(toNumber(tr.find("input[name='debit[]']").val()));
      let credit = round2(toNumber(tr.find("input[name='credit[]']").val()));

      // Balance dicek pakai debit_idr/credit_idr (kolom yang memang ada di tbl_list_journal),
      // bukan debit/credit mentah - karena rate bisa beda-beda per baris kalau currency-nya
      // campuran, jadi debit/credit asli tidak bisa dibandingkan langsung antar baris.
      let debit_idr  = round2(debit * rate);
      let credit_idr = round2(credit * rate);

      console.log("ROW "+rowIndex,{ // DEBUG
          profit_center,
          coa,
          costcenter,
          curr,
          rate,
          debit,
          credit,
          debit_idr,
          credit_idr
      });

      /* =====================
         SKIP ROW KOSONG
      ===================== */

      if(!profit_center && !coa && debit === 0 && credit === 0){
          return;
      }

      /* =====================
         VALIDASI PROFIT CENTER
      ===================== */

      if(!profit_center){

          tr.addClass("row-error");
          tr.find("select[name='profit_center[]']").addClass("input-error");

          Swal.fire({
              icon:'warning',
              title:'Profit Center is empty',
              text:'Profit Center has not been selected in row '+rowIndex
          });

          hasError = true;
          resetButton();
          return false;
      }

      /* =====================
         VALIDASI COA
      ===================== */

      if(!coa){

          tr.addClass("row-error");
          tr.find("select[name='nomor_coa[]']").addClass("input-error");

          Swal.fire({
              icon:'warning',
              title:'COA is empty',
              text:'COA has not been selected in row '+rowIndex
          });

          hasError = true;
          resetButton();
          return false;
      }

      /* =====================
         VALIDASI COST CENTER
      ===================== */

      let coaClean = (coa || "").toString().trim();
      let ccClean  = (costcenter || "").toString().trim();

      if(
          coaWajibCC.includes(coaClean) &&
          (ccClean === "" || ccClean === "-" || ccClean === "null")
      ){

          tr.addClass("row-error");
          tr.find("select[name='nomor_cc[]']").addClass("input-error");

          Swal.fire({
              icon:'warning',
              title:'Cost Center is required',
              text:'COA '+coaClean+' requires a Cost Center (row '+rowIndex+')'
          });

          hasError = true;
          resetButton();
          return false;
      }

      /* =====================
         VALIDASI RATE
      ===================== */

      if(curr && curr !== "IDR" && rate === 1){

          tr.addClass("row-error");
          tr.find("input[name='rate[]']").addClass("input-error");

          Swal.fire({
              icon:'error',
              title:'Invalid Rate',
              text:'Currency '+curr+' cannot have a rate of 1 (row '+rowIndex+')'
          });

          hasError = true;
          resetButton();
          return false;
      }

      if(debit === 0 && credit === 0){
          return;
      }

      /* =====================
         HITUNG TOTAL PER PC (pakai IDR)
      ===================== */

      if(!totalPC[profit_center]){
          totalPC[profit_center] = {debit_idr:0,credit_idr:0};
      }

      totalPC[profit_center].debit_idr  = round2(totalPC[profit_center].debit_idr + debit_idr);
      totalPC[profit_center].credit_idr = round2(totalPC[profit_center].credit_idr + credit_idr);

      console.log("TOTAL PC UPDATE",profit_center,totalPC[profit_center]); // DEBUG

      /* =====================
         PUSH DATA
      ===================== */

      rows.push({

          no_coa: coa,
          profit_center: profit_center,
          no_costcenter: costcenter,

          reff_doc: tr.find("input[name='ref_no[]']").val(),
          reff_date: tr.find("input[name='tgl_active[]']").val(),

          buyer: tr.find("select[name='buyer[]']").val(),
          no_ws: tr.find("select[name='no_ws[]']").val(),

          curr: curr,
          rate: rate,

          debit: debit,
          credit: credit,
          debit_idr: debit_idr,
          credit_idr: credit_idr,

          keterangan: tr.find("textarea[name='remark[]']").val()

      });

  });

  console.log("TOTAL PER PROFIT CENTER",totalPC); // DEBUG
  console.table(rows); // DEBUG

  if(hasError) return;

  if(rows.length === 0){

      Swal.fire({
          icon:'warning',
          title:'No transactions',
          text:'Please fill in at least one transaction'
      });

      resetButton();
      return;
  }

  for(let pc in totalPC){

      console.log("CHECK BALANCE PC",pc,totalPC[pc]); // DEBUG

      if(round2(totalPC[pc].debit_idr) !== round2(totalPC[pc].credit_idr)){

          Swal.fire({
              icon:'error',
              title:'Not Balanced',
              text:'Profit Center '+pc+' Debit IDR and Credit IDR are not balanced'
          });

          resetButton();
          return;
      }
  }

  let totalDebitIdr = 0;
  let totalCreditIdr = 0;

  rows.forEach(function(r){

      totalDebitIdr  = round2(totalDebitIdr + r.debit_idr);
      totalCreditIdr = round2(totalCreditIdr + r.credit_idr);

  });

  console.log("GRAND TOTAL", { // DEBUG
      totalDebitIdr,
      totalCreditIdr,
      selisih: totalDebitIdr-totalCreditIdr
  });

  if(totalDebitIdr === 0 && totalCreditIdr === 0){

      Swal.fire({
          icon:'warning',
          title:'Empty Data',
          text:'Total Debit IDR and Credit IDR cannot be 0'
      });

      resetButton();
      return;
  }

  if(round2(totalDebitIdr) !== round2(totalCreditIdr)){

      Swal.fire({
          icon:'error',
          title:'Not Balanced',
          text:'Grand Total Debit IDR and Credit IDR are not balanced'
      });

      resetButton();
      return;
  }

  let dataPost = {

      no_journal:no_journal,
      tgl_journal:tgl_journal,
      type_journal:type_journal,
      create_user:create_user,
      rows:JSON.stringify(rows)

  };

  console.log("DATA POST",dataPost); // DEBUG

  // Semua validasi lolos - baru sekarang minta konfirmasi ke user
  // sebelum benar-benar menyimpan.
  Swal.fire({
      icon:'question',
      title:'Save changes?',
      text:'Are you sure you want to save these changes?',
      showCancelButton:true,
      confirmButtonText:'Yes, save it',
      cancelButtonText:'Cancel',
      confirmButtonColor:'#28a745',
      cancelButtonColor:'#6c757d'
  }).then((confirmResult) => {

      if(!confirmResult.isConfirmed){
          return;
      }

      performSave(dataPost, btn, resetButton);

  });

}

// Dipisah dari SaveEdit() supaya bisa dipanggil ulang langsung dari tombol
// "Retry Save" di alert kegagalan - tanpa perlu validasi + konfirmasi ulang,
// dan tanpa user harus input ulang data (dataPost sudah jadi, tinggal kirim lagi).
function performSave(dataPost, btn, resetButton){

    btn.prop("disabled", true)
       .html('<i class="fa fa-spinner fa-spin"></i> Processing...');

    Swal.fire({
        title: 'Saving Journal...',
        html: 'Please wait, your data is being processed',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({

        url:"save_edit_journal.php",
        type:"POST",
        data:dataPost,

        success:function(res){

            console.log("SERVER RESPONSE",res); // DEBUG

            if(res === "success"){

                Swal.fire({
                    icon:'success',
                    title:'Success',
                    text:'Journal updated successfully'
                }).then(()=>{

                    // tutup modal
                    $("#modalEdit").modal("hide");

                    // reload datatable tanpa refresh halaman
                    $('#table-data').DataTable().ajax.reload(null,false);

                });

            }else{

                showSaveFailedRetry(res, dataPost, btn, resetButton);
                resetButton();

            }

        },

        error:function(err){

            console.log("AJAX ERROR",err); // DEBUG

            showSaveFailedRetry('A server error occurred while saving the data', dataPost, btn, resetButton);
            resetButton();

        }

    });

}

// Alert gagal-save dengan tombol "Retry Save" - klik langsung kirim ulang
// dataPost yang sama tanpa harus tutup modal / isi ulang form.
function showSaveFailedRetry(message, dataPost, btn, resetButton){

    Swal.fire({
        icon:'error',
        title:'Save Failed',
        text:message,
        showCancelButton:true,
        confirmButtonText:'Retry Save',
        cancelButtonText:'Close',
        confirmButtonColor:'#dc3545',
        cancelButtonColor:'#6c757d'
    }).then((retryResult) => {

        if(retryResult.isConfirmed){
            performSave(dataPost, btn, resetButton);
        }

    });

}








</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
