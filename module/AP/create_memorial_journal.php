<?php include '../header.php' ?>

<style type="text/css">
  label {
    font-size: 14px;
    ;
  }

  input {
    font-size: 14px;
    ;
  }


  .tabcontent {
    display: none;
    animation: fadeEffect 0.3s;
  }

  @keyframes fadeEffect {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
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
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  .tablinks:hover {
    background: #007bff;
    color: #fff;
    transform: translateY(-1px);
  }

  .tablinks.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35);
  }

  table.dataTable th,
  table.dataTable td {
    white-space: nowrap;
    vertical-align: middle;
  }


  .select2-container .select2-selection--single {
    height: calc(2.25rem + 2px);
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.25rem;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
  }

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

  #mytablenone .form-control {
    width: 100% !important;
  }

  #mytablenone .bootstrap-select {
    width: 100% !important;
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

#table-hris th,
#table-hris td {
    white-space: normal;
    word-break: break-word;
}

.dataTables_scrollHeadInner table,
.dataTables_scrollBody table {
    width: 100% !important;
}

.dataTables_scrollBody {
    overflow-y: scroll !important;
}
#uploadBox:hover {
    background:#eef5ff;
    border-color:#0056b3;
}

.btn-sm {
    transition:0.2s;
}

.btn-sm:hover {
    transform:scale(1.05);
}





</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
      style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM MEMORIAL JOURNAL</h5>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-1">
      <div class="card-body p-4">

        <div class="tab-container">
          <button class="tablinks active" onclick="openTab(event, 'mj_input')">Manual Journal Entry</button>
          <button class="tablinks" onclick="openTab(event, 'mj_hris')">Journal From HRIS</button>
          <button class="tablinks" onclick="openTab(event, 'mj_upload')">Upload Journal</button>
        </div>


        <div id="mj_input" class="tabcontent">
          <?php include 'memorial_journal/mj_input.php'; ?>
        </div>

        <div id="mj_hris" class="tabcontent">
          <?php include 'memorial_journal/mj_from_hris.php'; ?>
        </div>

        <div id="mj_upload" class="tabcontent">
          <?php include 'memorial_journal/mj_upload.php'; ?>
        </div>

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

    function SidebarCollapse() {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

      // Treating d-flex/d-none on separators with title
      var SeparatorTitle = $('.sidebar-separator-title');
      if (SeparatorTitle.hasClass('d-flex')) {
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
      $('.tanggal').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true
        });
      $('#table_tbytd').DataTable({
        scrollX: true,
        scrollCollapse: true,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,
        ordering: false,
        fixedHeader: true,
        columnDefs: [{
            width: "120px",
            targets: "_all"
          } // sisanya 100px semua
        ],
        initComplete: function() {
          this.api().columns.adjust();
        }
      });
    });
  </script>


  <script>
    $(function() {
      $('.select2').select2({
        width: '100%'
      });

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
    document.addEventListener("DOMContentLoaded", function() {
      document.querySelector(".tablinks.active").click();
      getRate();
    });
  </script>

  <script type="text/javascript">


    function addRow(tableID) {
      $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
    <td>
    <input type="checkbox" id="select" name="select[]" value="" checked disabled>
    </td>
    <td>
    <select class="form-control selectpicker no_coa" name="nomor_coa[]" id="nomor_coa" data-live-search="true" data-width="200px">
    <option value="-">-</option>
    <?php 
    $sql = mysqli_query($conn1, "SELECT no_coa AS id_coa, CONCAT(no_coa, ' ', nama_coa) AS coa FROM mastercoa_v2"); 
    foreach ($sql as $coa) : 
    ?>
    <option value="<?= $coa['id_coa']; ?>"><?= $coa['coa']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr[]" id="prof_ctr" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker nomor_cc" name="nomor_cc[]" id="nomor_cc" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
</select>
</td>
<td>
<input type="text" class="form-control" style="font-size: 12px;width: 150px;" name="reff[]" placeholder="" autocomplete="off">
</td>
<td>
<input type="text" class="form-control tanggal" style="font-size: 12px;width: 150px;" name="reffdate[]" placeholder="" autocomplete="off">
</td>
<td>
<select class="form-control selectpicker" name="buyer[]" id="buyer" data-live-search="true" data-width="200px">
<option value="-">-</option>
<?php 
$sql4 = mysqli_query($conn1, "SELECT DISTINCT(Supplier) AS buyer FROM mastersupplier WHERE tipe_sup = 'C' ORDER BY Supplier ASC"); 
foreach ($sql4 as $ms) : 
?>
<option value="<?= $ms['buyer']; ?>"><?= $ms['buyer']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker" name="no_ws[]" id="no_ws" data-live-search="true" data-width="200px">
<option value="-">-</option>
<?php 
$sql3 = mysqli_query($conn1, "SELECT a.kpno AS no_ws, b.Supplier FROM act_costing a 
INNER JOIN mastersupplier b ON b.id_supplier = a.id_buyer 
WHERE cost_date >= '2022-01-01'"); 
foreach ($sql3 as $ws) : 
?>
<option value="<?= $ws['no_ws']; ?>"><?= $ws['no_ws']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker" name="currenc[]" id="currenc" data-live-search="true">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>
<td style="text-align: right;">
<input type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_debit" name="txt_debit[]" oninput="modal_input_deb(this)" autocomplete="off">
</td>
<td>
<input type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_credit" name="txt_credit[]" oninput="modal_input_cre(this)" autocomplete="off">
</td>
<td>
<input type="text" class="form-control" style="font-size: 12px;width: 150px;" name="keterangan[]" placeholder="" autocomplete="off">
</td>
<td>
<input type="checkbox" class="checkall_a" name="chk_a[]" value="">
</td>
</tr>

`;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center').val();

      if (headerPC) {

        $(row).find('.prof_ctr').val(headerPC);
        $(row).find('.prof_ctr').selectpicker('refresh');

      }

    }


    function deleteRow(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var deleted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a[]"]');

          if (chkbox && chkbox.checked) {

            if (rowCount <= 1) {

              Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Tidak dapat menghapus semua baris'
              });

              return;

            }

            table.deleteRow(i);
            deleted = true;
            rowCount--;
            hitung_total();

          }

        }

        if (!deleted) {

          Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Silahkan ceklis baris yang ingin dihapus'
          });

        }

        /* refresh selectpicker jika ada */
        $('.selectpicker').selectpicker('refresh');

      } catch (e) {

        console.log(e);

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: e.message
        });

      }

    }


    function InsertRow(tableID) {

      $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inserted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
<tr>
    <td>
    <input type="checkbox" id="select" name="select[]" value="" checked disabled>
    </td>
    <td>
    <select class="form-control selectpicker no_coa" name="nomor_coa[]" id="nomor_coa" data-live-search="true" data-width="200px">
    <option value="-">-</option>
    <?php 
    $sql = mysqli_query($conn1, "SELECT no_coa AS id_coa, CONCAT(no_coa, ' ', nama_coa) AS coa FROM mastercoa_v2"); 
    foreach ($sql as $coa) : 
    ?>
    <option value="<?= $coa['id_coa']; ?>"><?= $coa['coa']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr[]" id="prof_ctr" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker nomor_cc" name="nomor_cc[]" id="nomor_cc" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
</select>
</td>
<td>
<input type="text" class="form-control" style="font-size: 12px;width: 150px;" name="reff[]" placeholder="" autocomplete="off">
</td>
<td>
<input type="text" class="form-control tanggal" style="font-size: 12px;width: 150px;" name="reffdate[]" placeholder="" autocomplete="off">
</td>
<td>
<select class="form-control selectpicker" name="buyer[]" id="buyer" data-live-search="true" data-width="200px">
<option value="-">-</option>
<?php 
$sql4 = mysqli_query($conn1, "SELECT DISTINCT(Supplier) AS buyer FROM mastersupplier WHERE tipe_sup = 'C' ORDER BY Supplier ASC"); 
foreach ($sql4 as $ms) : 
?>
<option value="<?= $ms['buyer']; ?>"><?= $ms['buyer']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker" name="no_ws[]" id="no_ws" data-live-search="true" data-width="200px">
<option value="-">-</option>
<?php 
$sql3 = mysqli_query($conn1, "SELECT a.kpno AS no_ws, b.Supplier FROM act_costing a 
INNER JOIN mastersupplier b ON b.id_supplier = a.id_buyer 
WHERE cost_date >= '2022-01-01'"); 
foreach ($sql3 as $ws) : 
?>
<option value="<?= $ws['no_ws']; ?>"><?= $ws['no_ws']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker" name="currenc[]" id="currenc" data-live-search="true">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>
<td style="text-align: right;">
<input type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_debit" name="txt_debit[]" oninput="modal_input_deb(this)" autocomplete="off">
</td>
<td>
<input type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_credit" name="txt_credit[]" oninput="modal_input_cre(this)" autocomplete="off">
</td>
<td>
<input type="text" class="form-control" style="font-size: 12px;width: 150px;" name="keterangan[]" placeholder="" autocomplete="off">
</td>
<td>
<input type="checkbox" class="checkall_a" name="chk_a[]" value="">
</td>
</tr>
`;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr').val(headerPC);
              $(newRow).find('.prof_ctr').selectpicker('refresh');

            }

          }

        }

        /* jika tidak ada yang dicentang */

        if (!inserted) {

          Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Silahkan ceklis baris yang ingin disisipkan'
          });

        }

        /* refresh plugin */

        $('.selectpicker').selectpicker('refresh');

      } catch (e) {

        console.log(e);

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: e.message
        });

      }

    }

   function formatAngka(x){

return new Intl.NumberFormat('en-US',{
minimumFractionDigits:0,
maximumFractionDigits:2
}).format(x);

}

function formatNumber(input) {

      let value = input.value.replace(/,/g, '');

      if (value === '') return;

      let parts = value.split('.');
      let integer = parts[0];
      let decimal = parts[1] ? parts[1].substring(0, 4) : '';

      integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

      input.value = decimal ? integer + '.' + decimal : integer;

    }

    $(document).on('keyup', '.angka', function() {
      formatNumber(this);
    });


$(document).on('change', '.prof_ctr', function () {
    const selectedProfCtr = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedCoa = row.find('select.no_coa').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
    hitung_total();
});

   $(document).on('change', '.no_coa', function () {
    const selectedCoa = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedProfCtr = row.find('select.prof_ctr').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});



// Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
function updateCostCenter(profCtr, noCoa, row) {
    const costCtrDropdown = $(row).find('.nomor_cc'); // dropdown cost center pada baris tsb

    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getCostCenter.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { prof_ctr: profCtr , no_coa: noCoa },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                if (response && response.length > 0) {
                    $.each(response, function (index, costCtr) {
                        costCtrDropdown.append(
                            `<option value="${costCtr.value}">${costCtr.text}</option>`
                            );
                    });

                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.warn('Tidak ada data cost center dari server.');
                    costCtrDropdown.selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    } else {
        costCtrDropdown.selectpicker('refresh');
    }
}

$('#profit_center').on('change', function(){

    let pcHeader = $(this).val();

    console.log("Header Profit Center berubah :", pcHeader);

    $('#tbody3 tr').each(function(){

        let row = $(this);

        /* set profit center row */
        row.find('.prof_ctr').val(pcHeader).trigger('change');

        console.log("Row PC diubah :", pcHeader);

        /* clear cost center */

        let cc = row.find('.nomor_cc');

        cc.selectpicker('destroy');
        cc.empty();
        cc.append('<option value="-"> - </option>');
        cc.selectpicker('refresh');

        console.log("Cost Center di clear");

        /* ambil coa untuk reload CC */

        let coa = row.find('.no_coa').val() || '-';

        updateCostCenter(pcHeader, coa, row);

    });

    /* hitung ulang */

    hitung_total();

});


function modal_input_deb(el){

    let tr = $(el).closest('tr');

    let debitInput  = tr.find('[name="txt_debit[]"]');
    let creditInput = tr.find('[name="txt_credit[]"]');
    let curr        = tr.find('[name="currenc[]"]').val();

    let rate = parseFloat($('#rate_mj').val().replace(/,/g,'')) || 1;

    let debit = parseFloat(debitInput.val()) || 0;

    if(debit > 0){

        creditInput.val(0);
        creditInput.prop('readonly',true);

    }else{

        creditInput.prop('readonly',false);

    }

    hitung_total();
}

function modal_input_cre(el){

    let tr = $(el).closest('tr');

    let debitInput  = tr.find('[name="txt_debit[]"]');
    let creditInput = tr.find('[name="txt_credit[]"]');
    let curr        = tr.find('[name="currenc[]"]').val();

    let rate = parseFloat($('#rate_mj').val().replace(/,/g,'')) || 1;

    let credit = parseFloat(creditInput.val()) || 0;

    if(credit > 0){

        debitInput.val(0);
        debitInput.prop('readonly',true);

    }else{

        debitInput.prop('readonly',false);

    }

    hitung_total();
}

function hitung_total(){

let debitNAG = 0;
let creditNAG = 0;
let debitNAK = 0;
let creditNAK = 0;

let debitNAG_IDR = 0;
let creditNAG_IDR = 0;
let debitNAK_IDR = 0;
let creditNAK_IDR = 0;

let rate = parseFloat($('#rate_mj').val().replace(/,/g,'')) || 1;

$("#tbody3 tr").each(function(){

    let tr = $(this);

    let pc   = tr.find('[name="prof_ctr[]"]').val();
    let curr = tr.find('[name="currenc[]"]').val();

    let debit  = parseFloat(tr.find('[name="txt_debit[]"]').val()) || 0;
    let credit = parseFloat(tr.find('[name="txt_credit[]"]').val()) || 0;

    let debit_idr  = debit;
    let credit_idr = credit;

    if(curr != 'IDR'){
        debit_idr  = debit * rate;
        credit_idr = credit * rate;
    }

    if(pc == 'NAG'){

        debitNAG  += debit;
        creditNAG += credit;

        debitNAG_IDR  += debit_idr;
        creditNAG_IDR += credit_idr;

    }

    if(pc == 'NAK'){

        debitNAK  += debit;
        creditNAK += credit;

        debitNAK_IDR  += debit_idr;
        creditNAK_IDR += credit_idr;

    }

});


let grandDebit  = debitNAG + debitNAK;
let grandCredit = creditNAG + creditNAK;

let grandDebit_IDR  = debitNAG_IDR + debitNAK_IDR;
let grandCredit_IDR = creditNAG_IDR + creditNAK_IDR;


/* ======================
   TAMPILKAN
====================== */

$('#tot_debit_nag').val(formatAngka(debitNAG));
$('#tot_credit_nag').val(formatAngka(creditNAG));

$('#tot_debit_nak').val(formatAngka(debitNAK));
$('#tot_credit_nak').val(formatAngka(creditNAK));

$('#tot_debit').val(formatAngka(grandDebit));
$('#tot_credit').val(formatAngka(grandCredit));


/* ======================
   TAMPILKAN IDR
====================== */

$('#tot_debit_idr_nag').val(formatAngka(debitNAG_IDR));
$('#tot_credit_idr_nag').val(formatAngka(creditNAG_IDR));

$('#tot_debit_idr_nak').val(formatAngka(debitNAK_IDR));
$('#tot_credit_idr_nak').val(formatAngka(creditNAK_IDR));

$('#tot_debit_idr').val(formatAngka(grandDebit_IDR));
$('#tot_credit_idr').val(formatAngka(grandCredit_IDR));


/* ======================
   HIDDEN
====================== */

$('#h_tot_debit_nag').val(debitNAG);
$('#h_tot_credit_nag').val(creditNAG);

$('#h_tot_debit_nak').val(debitNAK);
$('#h_tot_credit_nak').val(creditNAK);

$('#h_tot_debit').val(grandDebit);
$('#h_tot_credit').val(grandCredit);


$('#h_tot_debit_idr_nag').val(debitNAG_IDR);
$('#h_tot_credit_idr_nag').val(creditNAG_IDR);

$('#h_tot_debit_idr_nak').val(debitNAK_IDR);
$('#h_tot_credit_idr_nak').val(creditNAK_IDR);

$('#h_tot_debit_idr').val(grandDebit_IDR);
$('#h_tot_credit_idr').val(grandCredit_IDR);

}


function getRate(){

    let valuta   = 'USD';
    let doc_date = $('#mj_date').val();

    console.log('getRate jalan');
    console.log('valuta :', valuta);
    console.log('doc_date :', doc_date);

    if(!doc_date){
        console.log('Tanggal kosong');
        return;
    }

    $.ajax({
        url: 'get_rate.php',
        type: 'POST',
        dataType: 'json',
        data:{
            valuta: valuta,
            doc_date: doc_date
        },
        success:function(res){

            console.log('Response:', res);

            if(res.status === 'ok'){

                $('#rate_mj').val(res.rate);

            }else{

                $('#rate_mj').val('1');

            }

            formatNumber(document.getElementById('rate_mj'));

            // hitung ulang total
            hitung_total();

        },
        error:function(xhr){

            console.log('AJAX ERROR');
            console.log(xhr.responseText);

        }
    });

}

$(document).on('change','[name="currenc[]"]',function(){

    hitung_total();

});

let coaWajibCC = [];

// Load sekali saat halaman dibuka
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
});

$("#to_sb1").on("change", function(){

    if($(this).is(":checked")){
        var kata = '*Jurnal akan terbentuk di SB I';
        $("#txt_sb1").val(kata);
        $("#fil_sb1").val('1');
    }else{
        $("#txt_sb1").val('');
        $("#fil_sb1").val('0');
    }

});



$('#simpan').on('click', function(){

console.log('=== PROSES SAVE DIMULAI ===');

let mj_date = $('#mj_date').val();
let mj_type = $('#mj_type').val();

console.log('Tanggal MJ :', mj_date);
console.log('Type MJ :', mj_type);

let totalDebitIDR  = parseFloat($('#h_tot_debit_idr').val()) || 0;
let totalCreditIDR = parseFloat($('#h_tot_credit_idr').val()) || 0;

console.log('Total Debit IDR :', totalDebitIDR);
console.log('Total Credit IDR :', totalCreditIDR);

if(!mj_date){
    Swal.fire('Warning','Date wajib diisi','warning');
    return;
}

if(!mj_type){
    Swal.fire('Warning','Type wajib dipilih','warning');
    return;
}

/* ======================
   VALIDASI ROW
====================== */

let rowValid = true;
let rowIndex = 0;

$("#tbody3 tr").each(function(){

    rowIndex++;

    let tr = $(this);

    let coa = tr.find('[name="nomor_coa[]"]').val();
    let pc  = tr.find('[name="prof_ctr[]"]').val();
    let cc  = tr.find('[name="nomor_cc[]"]').val();

    let debit  = parseFloat(tr.find('[name="txt_debit[]"]').val()) || 0;
    let credit = parseFloat(tr.find('[name="txt_credit[]"]').val()) || 0;

    console.log('---- ROW',rowIndex,'----');
    console.log('COA :',coa);
    console.log('PC :',pc);
    console.log('CC :',cc);
    console.log('Debit :',debit);
    console.log('Credit :',credit);

    if(coa == '-' || coa == '' || coa == null){

        Swal.fire('Warning','COA wajib diisi pada baris '+rowIndex,'warning');
        rowValid = false;
        return false;

    }

    if(pc == '-' || pc == '' || pc == null){

        Swal.fire('Warning','Profit Center wajib diisi pada baris '+rowIndex,'warning');
        rowValid = false;
        return false;

    }

    if(debit == 0 && credit == 0){

        Swal.fire('Warning','Debit atau Credit harus diisi pada baris '+rowIndex,'warning');
        rowValid = false;
        return false;

    }

    /* ======================
       VALIDASI COA WAJIB CC
    ====================== */

    if(coaWajibCC.includes(coa)){

        console.log('COA ini wajib Cost Center');

        if(cc == '-' || cc == '' || cc == null){

            Swal.fire('Warning','Cost Center wajib diisi untuk COA '+coa+' pada baris '+rowIndex,'warning');
            rowValid = false;
            return false;

        }

    }

});

if(!rowValid){
    console.log('VALIDASI ROW GAGAL');
    return;
}

/* ======================
   VALIDASI BALANCE
====================== */

if(totalDebitIDR != totalCreditIDR){

    console.log('JOURNAL TIDAK BALANCE');

    Swal.fire({
        icon:'error',
        title:'Journal Tidak Balance',
        html:'Debit IDR : <b>'+formatAngka(totalDebitIDR)+'</b><br>Credit IDR : <b>'+formatAngka(totalCreditIDR)+'</b>'
    });

    return;
}

console.log('Journal Balance ✔');

/* ======================
   VALIDASI BALANCE PER PROFIT CENTER
====================== */

let nag_debit  = parseFloat($('#h_tot_debit_nag').val()) || 0;
let nag_credit = parseFloat($('#h_tot_credit_nag').val()) || 0;

let nak_debit  = parseFloat($('#h_tot_debit_nak').val()) || 0;
let nak_credit = parseFloat($('#h_tot_credit_nak').val()) || 0;

console.log('=== VALIDASI PER PROFIT CENTER ===');

console.log('NAG Debit :', nag_debit);
console.log('NAG Credit :', nag_credit);

console.log('NAK Debit :', nak_debit);
console.log('NAK Credit :', nak_credit);

if(nag_debit != nag_credit){

    Swal.fire({
        icon:'error',
        title:'Journal NAG Tidak Balance',
        html:'Debit : <b>'+formatAngka(nag_debit)+'</b><br>Credit : <b>'+formatAngka(nag_credit)+'</b>'
    });

    return;

}

if(nak_debit != nak_credit){

    Swal.fire({
        icon:'error',
        title:'Journal NAK Tidak Balance',
        html:'Debit : <b>'+formatAngka(nak_debit)+'</b><br>Credit : <b>'+formatAngka(nak_credit)+'</b>'
    });

    return;

}

console.log('Balance per Profit Center ✔');



/* ======================
   CONFIRM SAVE
====================== */

Swal.fire({
    title: 'Save Journal ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Save'
}).then((result)=>{

if(result.isConfirmed){

    console.log('User klik SAVE');

    let formData = new FormData($('#form-data')[0]);

    console.log(formData);

    $.ajax({

        url:'memorial_journal/save_mj_input.php',
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        dataType: 'json',

        beforeSend:function(){

            console.log('Mengirim data ke save_mj.php');

            Swal.fire({
                title:'Saving...',
                allowOutsideClick:false,
                didOpen:()=>{
                    Swal.showLoading();
                }
            });

        },

        success:function(res){

            console.log('Response dari server :',res);

            Swal.fire({
    icon: 'success',
    title: 'Berhasil Disimpan',
    html: `
        <div style="text-align:center">
            <div style="font-size:14px">No Journal</div>
            <div style="font-size:22px;font-weight:bold;color:#007bff;margin-bottom:15px">
                ${res.no_journal}
            </div>

            <div style="display:flex;justify-content:center;gap:10px">
                <button id="copyJournal" 
                    class="swal2-styled" 
                    style="background:#28a745;color:#fff">
                    Copy
                </button>

                <button id="okBtn" 
                    class="swal2-styled" 
                    style="background:#007bff;color:#fff">
                    OK
                </button>
            </div>
        </div>
    `,
    showConfirmButton: false,
    showCancelButton: false,

    didOpen: () => {

        // 🔥 COPY
        $('#copyJournal').on('click', function(){
            navigator.clipboard.writeText(res.no_journal);

            $(this).text('Copied!');
            setTimeout(() => {
                $(this).text('Copy');
            }, 1000);
        });

        // 🔥 OK
        $('#okBtn').on('click', function(){
            Swal.close();
        });

    }

}).then(() => {
    location.href='memorial-journal.php';
});


        },

        error:function(xhr){

            console.log('AJAX ERROR');
            console.log(xhr.responseText);

            Swal.fire('Error','Terjadi kesalahan saat save','error');

        }

    });

}

});

});



// FORM DATA 2

$('#hris_date').datepicker({
    format: "M yyyy",
    startView: "months",
    minViewMode: "months",
    autoclose: true
});


function getRate2(){

    let valuta   = 'USD';
    let doc_date = $('#mj_date2').val();

    console.log('getRate jalan');
    console.log('valuta :', valuta);
    console.log('doc_date :', doc_date);

    if(!doc_date){
        console.log('Tanggal kosong');
        return;
    }

    $.ajax({
        url: 'get_rate.php',
        type: 'POST',
        dataType: 'json',
        data:{
            valuta: valuta,
            doc_date: doc_date
        },
        success:function(res){

            console.log('Response:', res);

            if(res.status === 'ok'){

                $('#rate_mj2').val(res.rate);

            }else{

                $('#rate_mj2').val('1');

            }

            formatNumber(document.getElementById('rate_mj2'));

            // hitung ulang total
            // hitung_total();

        },
        error:function(xhr){

            console.log('AJAX ERROR');
            console.log(xhr.responseText);

        }
    });

}


$("#to2_sb1").on("change", function(){

    if($(this).is(":checked")){
        var kata = '*Jurnal akan terbentuk di SB I';
        $("#txt2_sb1").val(kata);
        $("#fil2_sb1").val('1');
    }else{
        $("#txt2_sb1").val('');
        $("#fil2_sb1").val('0');
    }

});

let datatable;

    $(document).ready(function() {

      datatable = $("#table-hris").DataTable({

    ordering: false,
    processing: true,
    serverSide: false,
    searching: true,
    info: true,

    paging: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],

    autoWidth: false,

    ajax: {
        url: 'memorial_journal/ajx_get_data_hris.php',
        type: 'POST',
        data: function(d) {

            d.mj_type2 = $('#mj_type2').val();

            let val = $('#hris_date').val();

            if(val){
                let bulanMap = {
                    Jan:'01', Feb:'02', Mar:'03', Apr:'04',
                    May:'05', Jun:'06', Jul:'07', Aug:'08',
                    Sep:'09', Oct:'10', Nov:'11', Dec:'12'
                };

                let p = val.split(' ');
                let bulan = bulanMap[p[0]];
                let tahun = p[1];

                d.hris_date = tahun + '-' + bulan + '-01';
            } else {
                d.hris_date = '';
            }
        },
        dataSrc: function(res){
            return res.data;
        }
    },

    columns: [
        { data: 'profit_center' },
        { data: 'nama_coa' },
        { data: 'cc_name' },
        { data: 'reff_number' },
        { data: 'reff_date' },
        { data: 'buyer' },
        { data: 'ws' },
        { data: 'curr' },
        { data: 'debit' },
        { data: 'credit' },
        { data: 'deskripsi' }
    ],

    columnDefs: [
        {
            targets: [8, 9],
            className: "text-right",
            render: function(data) {
                let val = parseFloat(data || 0);
                return val.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        }
    ],

    drawCallback: function () {

        let api = this.api();

        let total_debit_nag = 0;
        let total_credit_nag = 0;

        let total_debit_nak = 0;
        let total_credit_nak = 0;

        // 🔥 LOOP SEMUA DATA (BUKAN PER PAGE)
        api.rows().every(function () {

            let d = this.data();
            console.log(d); 

            let pc = (d.id_pc  || '').toUpperCase();
            let debit = parseFloat(d.debit) || 0;
            let credit = parseFloat(d.credit) || 0;

            if(pc === 'NAG'){
                total_debit_nag += debit;
                total_credit_nag += credit;
            }

            if(pc === 'NAK'){
                total_debit_nak += debit;
                total_credit_nak += credit;
            }

        });

        // 🔥 GRAND TOTAL
        let grand_debit = total_debit_nag + total_debit_nak;
        let grand_credit = total_credit_nag + total_credit_nak;

        // 🔥 FORMAT ANGKA
        function formatAngka(x){
            return x.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // 🔥 SET KE INPUT (VISIBLE)
        $('#tot_debit_nag2').val(formatAngka(total_debit_nag));
        $('#tot_credit_nag2').val(formatAngka(total_credit_nag));

        $('#tot_debit_nak2').val(formatAngka(total_debit_nak));
        $('#tot_credit_nak2').val(formatAngka(total_credit_nak));

        $('#tot_debit2').val(formatAngka(grand_debit));
        $('#tot_credit2').val(formatAngka(grand_credit));

        // 🔥 SET KE HIDDEN (RAW NUMBER)
        $('#h_tot_debit_nag2').val(total_debit_nag);
        $('#h_tot_credit_nag2').val(total_credit_nag);

        $('#h_tot_debit_nak2').val(total_debit_nak);
        $('#h_tot_credit_nak2').val(total_credit_nak);

        $('#h_tot_debit2').val(grand_debit);
        $('#h_tot_credit2').val(grand_credit);

        // 🔥 VALIDASI BALANCE (WARNA)
        if (
    Math.round(grand_debit * 100) / 100 !== 
    Math.round(grand_credit * 100) / 100
){
    $('#tot_debit2, #tot_credit2').css({
        'color':'red',
        'font-weight':'bold'
    });
} else {
    $('#tot_debit2, #tot_credit2').css({
        'color':'green',
        'font-weight':'bold'
    });
}


    }

});


       });

      function dataTableReload() {

      datatable.ajax.reload(function() {

        datatable.columns.adjust().draw();

      }, false);

    }

    $('#simpan2').on('click', function () {

    console.log("=== START SAVE ===");

    let mj_date   = $('#mj_date2').val();
    let mj_type   = $('#mj_type2').val();
    let pc        = $('#profit_center2').val();
    let desc      = $('#pesan2').val();
    let rate      = $('#rate_mj2').val();
    let fil_sb1      = $('#fil2_sb1').val();
    let val = $('#hris_date').val();

let hris_date = '';

if(val){
    let bulanMap = {
        Jan:'01', Feb:'02', Mar:'03', Apr:'04',
        May:'05', Jun:'06', Jul:'07', Aug:'08',
        Sep:'09', Oct:'10', Nov:'11', Dec:'12'
    };

    let p = val.split(' ');
    let bulan = bulanMap[p[0]];
    let tahun = p[1];

    hris_date = tahun + '-' + bulan + '-01';
}


    console.log("HEADER:", { mj_date, mj_type, pc, desc, rate });

    // VALIDASI
    if (mj_date == '') {
        Swal.fire('Warning','Date wajib diisi','warning');
        return;
    }

    if (mj_type == '') {
        Swal.fire('Warning','Type wajib dipilih','warning');
        return;
    }

    // 🔥 AMBIL SEMUA DATA DATATABLE
    let tableData = datatable.rows().data().toArray();

    console.log("TABLE DATA:", tableData);

    if (tableData.length == 0) {
        Swal.fire('Warning','Data tabel kosong','warning');
        return;
    }

    // 🔥 HITUNG TOTAL
    let total_debit = 0;
    let total_credit = 0;

    tableData.forEach(function(row, i){
        let d = parseFloat(row.debit || 0);
        let c = parseFloat(row.credit || 0);

        console.log(`ROW ${i}`, row);

        total_debit  += d;
        total_credit += c;
    });

    console.log("TOTAL D:", total_debit);
    console.log("TOTAL C:", total_credit);

    if (parseFloat(total_debit.toFixed(2)) !== parseFloat(total_credit.toFixed(2))) {
        Swal.fire({
            icon: 'error',
            title: 'Tidak Balance',
            html: `Debit: ${total_debit}<br>Credit: ${total_credit}`
        });
        return;
    }

    let payload = {
        mj_date2: mj_date,
        mj_type2: mj_type,
        profit_center2: pc,
        pesan2: desc,
        rate_mj2: rate,
        fil_sb1: fil_sb1,
        hris_date: hris_date,
        detail: JSON.stringify(tableData)
    };

    console.log("PAYLOAD:", payload);

    Swal.fire({
        title: 'Saving...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: 'memorial_journal/save_mj_hris.php',
        type: 'POST',
        data: payload,

        success: function (res) {

            console.log("RAW RESPONSE:", res);

            try {
                let json = JSON.parse(res);

                console.log("PARSED:", json);

                if(json.status === 'success'){

                    let no_journal = json.no_journal;

                    Swal.fire({
    icon: 'success',
    title: 'Berhasil Disimpan',
    html: `
        <div style="text-align:center">
            <div style="font-size:14px">No Journal</div>
            <div style="font-size:22px;font-weight:bold;color:#007bff;margin-bottom:15px">
                ${no_journal}
            </div>

            <div style="display:flex;justify-content:center;gap:10px">
                <button id="copyJournal" class="swal2-confirm swal2-styled" style="background:#28a745">
                    Copy
                </button>
                <button id="okBtn" class="swal2-cancel swal2-styled " style="background:#007bff;border:none;color:#fff">
                    OK
                </button>
            </div>
        </div>
    `,
    showConfirmButton: false,
    showCancelButton: false,

    didOpen: () => {

        // 🔥 COPY BUTTON
        $('#copyJournal').on('click', function(){
            navigator.clipboard.writeText(no_journal);

            // efek kecil (tanpa popup baru biar smooth)
            $(this).text('Copied!').css('background','#17a2b8');

            setTimeout(() => {
                $(this).text('Copy').css('background','#28a745');
            }, 1000);
        });

        // 🔥 OK BUTTON
        $('#okBtn').on('click', function(){
            Swal.close();
        });
    }

}).then(() => {
    location.href='memorial-journal.php';
});


                } else {
                    Swal.fire('Error', json.msg || 'Unknown error', 'error');
                }

            } catch (e) {
                console.error("JSON ERROR:", e);

                Swal.fire({
                    icon: 'error',
                    title: 'Response Error',
                    html: res
                });
            }
        },

        error: function (xhr, status, error) {

            console.error("AJAX ERROR:", xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'AJAX Error',
                html: `
                    Status: ${status}<br>
                    Error: ${error}<br>
                    ${xhr.responseText}
                `
            });
        }
    });

});



// FORM DATA 3

// reset dulu biar ga double binding
$('#fileUpload').off('change');
$('#uploadBox').off('dragover dragleave drop');

// tampil nama file
$('#fileUpload').on('change', function(){
    let file = this.files[0];

    if(file){
        $('#fileName').html('<b>' + file.name + '</b>');
    } else {
        $('#fileName').text('Belum ada file');
    }
});

// drag over
$('#uploadBox').on('dragover', function(e){
    e.preventDefault();
    $(this).css('background','#e9f3ff');
});

// drag leave
$('#uploadBox').on('dragleave', function(e){
    e.preventDefault();
    $(this).css('background','#f8f9fa');
});

// drop file
$('#uploadBox').on('drop', function(e){
    e.preventDefault();

    let files = e.originalEvent.dataTransfer.files;

    if(files.length > 0){
        document.getElementById('fileUpload').files = files;
        $('#fileName').html('<b>' + files[0].name + '</b>');
    }
});



let datatable3;

$(document).ready(function () {

    datatable3 = $("#table-upload-mj").DataTable({

        ordering: false,
        processing: true,
        serverSide: false,
        searching: true,
        info: true,
        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        autoWidth: false,

        ajax: {
            url: 'memorial_journal/ajx_get_data_mj_upload.php',
            type: 'POST',
            data: function (d) {
                d.mj_type2 = '1';
            },
            dataSrc: function (res) {

                console.log('DATA TABLE RESPONSE:', res);

                if (!res || !res.data) {
                    return [];
                }

                // 🔥 SORT: yang filter kosong ke atas
                res.data.sort(function (a, b) {

                    let fa = (a.filter || '').trim();
                    let fb = (b.filter || '').trim();

                    let aInvalid = (fa === '' || fa === '-' || fa === null);
                    let bInvalid = (fb === '' || fb === '-' || fb === null);

                    if (aInvalid && !bInvalid) return -1;
                    if (!aInvalid && bInvalid) return 1;
                    return 0;
                });

                return res.data;
            }
        },

        columns: [
            { data: 'nama_pc' },
            { data: 'coa' },
            { data: 'cc_name' },
            { data: 'no_reff' },
            { data: 'reff_date' },
            { data: 'buyer' },
            { data: 'no_ws' },
            { data: 'curr' },
            { data: 'debit' },
            { data: 'credit' },
            { data: 'keterangan' },
            { data: 'status' }
        ],

        columnDefs: [
            {
                targets: [8, 9],
                className: "text-right",
                render: function (data) {
                    let val = parseFloat(data || 0);
                    return val.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        ],

        // 🔥 ROW MERAH
        rowCallback: function (row, data) {

            let f = (data.filter || '').trim();

            if (f === '' || f === '-' || f === null) {
                $(row).css({
                    'background-color': '#ffcccc'
                });
            }
        },

        drawCallback: function () {

            let api = this.api();

            let total_debit_nag = 0;
            let total_credit_nag = 0;
            let total_debit_nak = 0;
            let total_credit_nak = 0;

            let total_debit_nag_idr = 0;
            let total_credit_nag_idr = 0;
            let total_debit_nak_idr = 0;
            let total_credit_nak_idr = 0;

            api.rows().every(function () {

                let d = this.data();

                let pc = (d.kode_pc || '').toUpperCase();

                let debit = parseFloat(d.debit) || 0;
                let credit = parseFloat(d.credit) || 0;
                let debit_idr = parseFloat(d.debit_idr) || 0;
                let credit_idr = parseFloat(d.credit_idr) || 0;

                if (pc === 'NAG') {
                    total_debit_nag += debit;
                    total_credit_nag += credit;
                    total_debit_nag_idr += debit_idr;
                    total_credit_nag_idr += credit_idr;
                }

                if (pc === 'NAK') {
                    total_debit_nak += debit;
                    total_credit_nak += credit;
                    total_debit_nak_idr += debit_idr;
                    total_credit_nak_idr += credit_idr;
                }

            });

            let grand_debit = total_debit_nag + total_debit_nak;
            let grand_credit = total_credit_nag + total_credit_nak;
            let grand_debit_idr = total_debit_nag_idr + total_debit_nak_idr;
            let grand_credit_idr = total_credit_nag_idr + total_credit_nak_idr;

            function formatAngka(x) {
                return x.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            $('#tot_debit_nag3').val(formatAngka(total_debit_nag));
            $('#tot_credit_nag3').val(formatAngka(total_credit_nag));

            $('#tot_debit_nak3').val(formatAngka(total_debit_nak));
            $('#tot_credit_nak3').val(formatAngka(total_credit_nak));

            $('#tot_debit3').val(formatAngka(grand_debit));
            $('#tot_credit3').val(formatAngka(grand_credit));

            $('#h_tot_debit_nag3').val(total_debit_nag);
            $('#h_tot_credit_nag3').val(total_credit_nag);

            $('#h_tot_debit_nak3').val(total_debit_nak);
            $('#h_tot_credit_nak3').val(total_credit_nak);

            $('#h_tot_debit3').val(grand_debit);
            $('#h_tot_credit3').val(grand_credit);

            $('#tot_debit_idr_nag3').val(formatAngka(total_debit_nag_idr));
            $('#tot_credit_idr_nag3').val(formatAngka(total_credit_nag_idr));

            $('#tot_debit_idr_nak3').val(formatAngka(total_debit_nak_idr));
            $('#tot_credit_idr_nak3').val(formatAngka(total_credit_nak_idr));

            $('#tot_debit_idr3').val(formatAngka(grand_debit_idr));
            $('#tot_credit_idr3').val(formatAngka(grand_credit_idr));

            $('#h_tot_debit_idr_nag3').val(total_debit_nag_idr);
            $('#h_tot_credit_idr_nag3').val(total_credit_nag_idr);

            $('#h_tot_debit_idr_nak3').val(total_debit_nak_idr);
            $('#h_tot_credit_idr_nak3').val(total_credit_nak_idr);

            $('#h_tot_debit_idr3').val(grand_debit_idr);
            $('#h_tot_credit_idr3').val(grand_credit_idr);

            // 🔥 VALIDASI BALANCE
            if (
                Math.round(grand_debit * 100) / 100 !==
                Math.round(grand_credit * 100) / 100
            ) {
                $('#tot_debit3, #tot_credit3,#tot_debit_idr3, #tot_credit_idr3').css({
                    'color': 'red',
                    'font-weight': 'bold'
                });
            } else {
                $('#tot_debit3, #tot_credit3,#tot_debit_idr3, #tot_credit_idr3').css({
                    'color': 'green',
                    'font-weight': 'bold'
                });
            }

        }

    });

});



let datatable4;

$(document).ready(function () {

    datatable4 = $("#table-upload-mj-group").DataTable({

        ordering: false,
        processing: true,
        serverSide: false,
        searching: true,
        info: true,
        paging: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        autoWidth: false,

        ajax: {
            url: 'memorial_journal/ajx_get_sum_mj_upload.php',
            type: 'POST',
            data: function (d) {
                d.mj_type2 = '1';
            },
            dataSrc: function (res) {

                console.log('DATA TABLE RESPONSE:', res);

                // 🔥 HANDLE kalau response tidak sesuai
                if (!res || !res.data) {
                    return [];
                }

                return res.data;
            }
        },

        columns: [
            { data: 'no_mj' },
            { data: 'mj_date' },
            { data: 'nama_cmj' },
            { data: 'curr' },
            { data: 'debit' },
            { data: 'credit' },
            { data: 'keterangan' },
            { data: 'status' }
        ],

        columnDefs: [
            {
                targets: [4, 5],
                className: "text-right",
                render: function (data) {
                    let val = parseFloat(data || 0);
                    return val.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }
        ],

    });

});



$('#btnUpload').on('click', function () {

    let file = $('#fileUpload')[0].files[0];

    if (!file) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Pilih file terlebih dahulu'
        });
        return;
    }

    let formData = new FormData();
    formData.append('file', file);

    let tgl = $('#mj_date3').val();
    formData.append('tanggal', tgl);

    Swal.fire({
        title: 'Uploading...',
        html: 'Sedang proses file...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: 'memorial_journal/proses_upload.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {

            Swal.close();
            console.log('UPLOAD RESPONSE:', res);

            try {
                let data = JSON.parse(res);

                if (data.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'File berhasil diproses'
                    });

                    // 🔥 DELAY + RELOAD DATATABLE
                    setTimeout(function () {

                        if ($.fn.DataTable.isDataTable('#table-upload-mj')) {
                            $('#table-upload-mj').DataTable().ajax.reload(null, false);
                        }

                        if ($.fn.DataTable.isDataTable('#table-upload-mj-group')) {
                            $('#table-upload-mj-group').DataTable().ajax.reload(null, false);
                        }

                    }, 700);

                } else {
                    Swal.fire('Error', data.message, 'error');
                }

            } catch (e) {
                Swal.fire('Error', 'Response tidak valid', 'error');
                console.log('ERROR PARSE:', res);
            }

        },

        error: function () {
            Swal.fire('Error', 'Gagal upload file', 'error');
        }

    });

});

$('#simpan3').on('click', function () {

    console.log('=== START SAVE PROCESS ===');

    $('#simpan3').prop('disabled', true);

    /* =============================
       1. VALIDASI ROW MERAH
    ============================= */
    let invalidRows = [];

    datatable3.rows().every(function () {
        let d = this.data();

        let f = (d.filter || '').trim();

        if (f === '' || f === '-' || f === null) {
            invalidRows.push(d);
        }
    });

    console.log('INVALID ROWS:', invalidRows);

    if (invalidRows.length > 0) {

        Swal.fire({
            icon: 'error',
            title: 'Tidak bisa save!',
            text: 'Masih ada data mapping (filter) yang kosong!'
        });

        $('#simpan3').prop('disabled', false);
        return;
    }


    /* =============================
       2. VALIDASI BALANCE PER MJ
    ============================= */
    let tidakBalance = [];

    datatable4.rows().every(function () {
        let d = this.data();

        let debit  = parseFloat(d.debit) || 0;
        let credit = parseFloat(d.credit) || 0;

        if (Math.round(debit * 100) / 100 !== Math.round(credit * 100) / 100) {
            tidakBalance.push(d.no_mj);
        }
    });

    console.log('MJ TIDAK BALANCE:', tidakBalance);

    if (tidakBalance.length > 0) {

        Swal.fire({
            icon: 'error',
            title: 'Journal Tidak Balance!',
            html: `
                Berikut No Journal yang tidak balance:<br><br>
                <b style="color:red">${tidakBalance.join(', ')}</b>
            `
        });

        $('#simpan3').prop('disabled', false);
        return;
    }


    /* =============================
       3. AMBIL DATA DETAIL
    ============================= */
    let detailData = [];

    datatable3.rows().every(function () {
        detailData.push(this.data());
    });

    console.log('DETAIL DATA:', detailData);


    /* =============================
       4. AMBIL DATA HEADER
    ============================= */
    let headerData = [];

    datatable4.rows().every(function () {
        headerData.push(this.data());
    });

    console.log('HEADER DATA:', headerData);


    /* =============================
       5. FORM DATA
    ============================= */
    let formData = new FormData($('#form-data3')[0]);

    formData.append('detail', JSON.stringify(detailData));
    formData.append('header', JSON.stringify(headerData));

    console.log('FORM DATA READY');


    /* =============================
       6. LOADING SWAL
    ============================= */
    Swal.fire({
        title: 'Menyimpan data...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });



    $.ajax({
        url: 'memorial_journal/save_mj_upload_fix.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function () {
            console.log('SENDING DATA...');
        },

        success: function (res) {

            console.log('SAVE RESPONSE RAW:', res);

            try {
                let r = JSON.parse(res);

                console.log('SAVE RESPONSE JSON:', r);

                if (r.status === 'success') {

                    let listMJ = (r.no_mj || []).join('\n');
    let listMJ_SB = (r.no_mj_sb || []).join('\n');

    let textAlert = 'Data berhasil disimpan\n\nNo GM:\n' + listMJ;

    if (listMJ_SB) {
        textAlert += '\n\nNo GM SB:\n' + listMJ_SB;
    }

    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: textAlert
    }).then(() => {'memorial-journal.php';
            });


                    // reload table
                    datatable3.ajax.reload(null, false);
                    datatable4.ajax.reload(null, false);

                } else {

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: r.message || 'Terjadi kesalahan'
                    });

                }

            } catch (e) {

                console.log('ERROR PARSE:', e);

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Response tidak valid dari server'
                });

            }

            $('#simpan3').prop('disabled', false);
        },

        error: function (xhr) {

            console.log('AJAX ERROR:', xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'Server Error!',
                text: 'Gagal menghubungi server'
            });

            $('#simpan3').prop('disabled', false);
        }

    });

});



  </script>

  </body>

  </html>