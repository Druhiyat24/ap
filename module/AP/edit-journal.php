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
}

.row-error{
    background:#FA9B9B !important;
}

.input-error{
    border:2px solid #FA9B9B !important;
}



/* ===============================
   MODAL SIZE
================================*/
#modalEdit .modal-dialog{
    max-width:95%;
}

#modalEdit .modal-body{
    max-height:none;
    overflow:visible;
}

.table-scroll{
    height:420px;
    overflow-y:auto;
    overflow-x:auto;
    border:1px solid #ddd;
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


/* ===============================
   TABLE JOURNAL SCROLL
================================*/
.table-scroll{
    max-height:420px;
    overflow-y:auto;
    overflow-x:auto;
    border:1px solid #ddd;
}

#tableJournal{
    min-width:1400px;
    width:100%;
}

#tableJournal thead th{
    position:sticky;
    top:0;
    background:#f8f9fa;
    z-index:2;
}

#tableJournal td,
#tableJournal th{
    white-space:nowrap;
}


/* ===============================
   COLUMN WIDTH
================================*/
#modalEdit table th:nth-child(2),
#modalEdit table td:nth-child(2){
    width:220px;
    min-width:220px;
}

#modalEdit table th:nth-child(13),
#modalEdit table td:nth-child(13){
    width:200px;
    min-width:200px;
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

.total-box{
    border:1px solid #dcdcdc;
    border-radius:6px;
    padding:15px;
    background:#fafafa;
}

.total-box h6{
    font-weight:bold;
    margin-bottom:15px;
    border-bottom:1px solid #ddd;
    padding-bottom:5px;
}

.total-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}

.total-row label{
    font-weight:600;
    margin:0;
}

.total-row input{
    text-align:right;
}





</style>

<!-- Modal EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:95%;width:95%;">
    <div class="modal-content">

      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Journal
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
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


function initSelect2Element(el,url,minInput=1){

    if(el.data('select2')) return;

    el.select2({
        width:'100%',
        dropdownParent: $('#modalEdit'),
        dropdownAutoWidth:true,
        placeholder:'Nothing Selected',
        allowClear:true,
        minimumInputLength:minInput,
        ajax:{
            url:url,
            dataType:'json',
            delay:250,
            cache:true,
            data:function(params){
                return { q: params.term };
            },
            processResults:function(data){
                return { results:data };
            }
        }
    });

}



$.fn.modal.Constructor.prototype.enforceFocus = function() {};

$(document).ready(function(){

/* ===============================
   SELECT2 CLICK INIT
================================ */

// COA
$(document).on('click','.sel-coa',function(){

    let el=$(this);

    initSelect2Element(el,'coa.php');

    setTimeout(function(){
        el.select2('open');
    },50);

});

// COST CENTER
$(document).on('click','.sel-cc',function(){

    let el=$(this);

    initSelect2Element(el,'cc.php');

    setTimeout(function(){
        el.select2('open');
    },50);

});

// PROFIT CENTER
$(document).on('click','.sel-pc',function(){

    let el=$(this);

    initSelect2Element(el,'pc.php',0);

    setTimeout(function(){
        el.select2('open');
    },50);

});

// BUYER
$(document).on('click','.sel-buyer',function(){

    let el=$(this);

    initSelect2Element(el,'buyer.php');

    setTimeout(function(){
        el.select2('open');
    },50);

});

// WS
$(document).on('click','.sel-ws',function(){

    let el=$(this);

    initSelect2Element(el,'ws.php');

    setTimeout(function(){
        el.select2('open');
    },50);

});

// CURRENCY
$(document).on('click','.sel-curr',function(){

    let el=$(this);

    initSelect2Element(el,'curr.php',0);

    setTimeout(function(){
        el.select2('open');
    },50);

});


/* ===============================
   ADD ROW
================================ */

$(document).on('click','#btnAdd',function(){

    let r=$("#templateRow").clone().removeAttr("id").show();

    $("#tbody2").append(r);

});


/* ===============================
   INSERT ROW
================================ */

$(document).on('click','#btnInsert',function(){

    let r=$("#templateRow").clone().removeAttr("id").show();

    $("#tbody2 tr:first").before(r);

});


/* ===============================
   DELETE ROW
================================ */

$(document).on('click','#btnDelete',function(){

    $("#tbody2 .remove:checked").each(function(){

        $(this).closest("tr").remove();
        hitungTotal();

    });

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
        text:'Failed load data'
      });

    }

  });

}


function cancelData(no_journal) {

    Swal.fire({
        title: 'Yakin mau cancel?',
        text: "Data journal akan dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, cancel!',
        cancelButtonText: 'Batal'
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
                            'Berhasil!',
                            res.message,
                            'success'
                        ).then(() => {
                            $('#table-data').DataTable().ajax.reload(null,false);
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            res.message,
                            'error'
                        );
                    }

                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Server bermasalah',
                        'error'
                    );
                }
            });

        }
    });
}


function initJournalUI(){

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

    hitungTotal();
}




function hitungTotal(){

    let total = {
        nag:{debit:0,credit:0,debit_idr:0,credit_idr:0},
        nak:{debit:0,credit:0,debit_idr:0,credit_idr:0},
        all:{debit:0,credit:0,debit_idr:0,credit_idr:0}
    };

    document.querySelectorAll("#tbody2 tr").forEach(row=>{

        let pc   = row.querySelector(".sel-pc")?.value;
        let coa  = row.querySelector(".sel-coa")?.value;
        let curr = row.querySelector(".sel-curr")?.value;

        // skip jika belum lengkap
        if(!pc || !coa || !curr){
            return;
        }

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
                title: 'Periode Salah',
                text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal'
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
                title: 'Periode Salah',
                text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal'
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

  btn.prop("disabled", true)
     .html('<i class="fa fa-spinner fa-spin"></i> Processing...');

     Swal.fire({
    title: 'Saving Journal...',
    html: 'Data sedang diproses, mohon tunggu',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => {
        Swal.showLoading();
    }
});

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

      console.log("ROW "+rowIndex,{ // DEBUG
          profit_center,
          coa,
          costcenter,
          curr,
          rate,
          debit,
          credit
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
              title:'Profit Center kosong',
              text:'Profit Center belum dipilih di baris '+rowIndex
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
              title:'COA kosong',
              text:'COA belum dipilih di baris '+rowIndex
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
              title:'Cost Center wajib',
              text:'COA '+coaClean+' wajib menggunakan Cost Center (baris '+rowIndex+')'
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
              title:'Rate Tidak Valid',
              text:'Currency '+curr+' tidak boleh rate = 1 (baris '+rowIndex+')'
          });

          hasError = true;
          resetButton();
          return false;
      }

      if(debit === 0 && credit === 0){
          return;
      }

      /* =====================
         HITUNG TOTAL PER PC
      ===================== */

      if(!totalPC[profit_center]){
          totalPC[profit_center] = {debit:0,credit:0};
      }

      totalPC[profit_center].debit  = round2(totalPC[profit_center].debit + debit);
      totalPC[profit_center].credit = round2(totalPC[profit_center].credit + credit);

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

          keterangan: tr.find("textarea[name='remark[]']").val()

      });

  });

  console.log("TOTAL PER PROFIT CENTER",totalPC); // DEBUG
  console.table(rows); // DEBUG

  if(hasError) return;

  if(rows.length === 0){

      Swal.fire({
          icon:'warning',
          title:'Tidak ada transaksi',
          text:'Silakan isi minimal satu transaksi'
      });

      resetButton();
      return;
  }

  for(let pc in totalPC){

      console.log("CHECK BALANCE PC",pc,totalPC[pc]); // DEBUG

      if(round2(totalPC[pc].debit) !== round2(totalPC[pc].credit)){

          Swal.fire({
              icon:'error',
              title:'Tidak Balance',
              text:'Profit Center '+pc+' Debit dan Credit tidak balance'
          });

          resetButton();
          return;
      }
  }

  let totalDebit = 0;
  let totalCredit = 0;

  rows.forEach(function(r){

      totalDebit  = round2(totalDebit + r.debit);
      totalCredit = round2(totalCredit + r.credit);

  });

  console.log("GRAND TOTAL", { // DEBUG
      totalDebit,
      totalCredit,
      selisih: totalDebit-totalCredit
  });

  if(totalDebit === 0 && totalCredit === 0){

      Swal.fire({
          icon:'warning',
          title:'Data Kosong',
          text:'Total Debit dan Credit tidak boleh 0'
      });

      resetButton();
      return;
  }

  if(round2(totalDebit) !== round2(totalCredit)){

      Swal.fire({
          icon:'error',
          title:'Tidak Balance',
          text:'Grand Total Debit dan Credit tidak balance'
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

  $.ajax({

      url:"save_edit_journal.php",
      type:"POST",
      data:dataPost,

      success:function(res){

          console.log("SERVER RESPONSE",res); // DEBUG

          if(res === "success"){

              Swal.fire({
    icon:'success',
    title:'Berhasil',
    text:'Journal berhasil diupdate'
}).then(()=>{

    // tutup modal
    $("#modalEdit").modal("hide");

    // reload datatable tanpa refresh halaman
    $('#table-data').DataTable().ajax.reload(null,false);

});


          }else{

              Swal.fire({
                  icon:'error',
                  title:'Error',
                  text:res
              });

              resetButton();
          }

      },

      error:function(err){

          console.log("AJAX ERROR",err); // DEBUG

          Swal.fire({
              icon:'error',
              title:'Server Error',
              text:'Terjadi kesalahan saat menyimpan data'
          });

          resetButton();
      }

  });

}








</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
