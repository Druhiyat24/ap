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

  .dataTables_scrollHeadInner,
  .dataTables_scrollBody table {
    width: 100% !important;
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
  .table-gradient2 th {
    background: #3B82F6;
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
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
      style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM PETTY CASH OUT</h5>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-1">
      <div class="card-body p-4">

        <div class="tab-container">
          <button class="tablinks" onclick="openTab(event, 'pettyout_none')">None</button>
          <button class="tablinks" onclick="openTab(event, 'pettyout_advance')">Advance</button>
          <button class="tablinks" onclick="openTab(event, 'pettyout_settle')">Settlement</button>
          <button class="tablinks active" onclick="openTab(event, 'pettyout_lp')">List Payment</button>
        </div>


        <div id="pettyout_none" class="tabcontent">
          <?php include 'petty-out/pettyout_none.php'; ?>
        </div>

        <div id="pettyout_advance" class="tabcontent">
          <?php include 'petty-out/pettyout_advance.php'; ?>
        </div>

        <div id="pettyout_settle" class="tabcontent">
           <?php include 'petty-out/pettyout_settle.php'; ?>
        </div>

        <div id="pettyout_lp" class="tabcontent">
          <?php include 'petty-out/pettyout_lp.php'; ?>
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
    });
  </script>




  <script type="text/javascript">
    $(document).ready(function() {
      $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose: true
      });
      $('.select2').select2({
        theme: 'bootstrap4'
      });

    });

    let coaWajibCC = [];

// Load sekali saat halaman dibuka
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
});


//NONE.....................................................................................


    $('#account1').on('change', function() {

      let kode1 = $(this).find(':selected').data('kode1');
      let pc1 = $(this).find(':selected').data('pc1');
      let namapc1 = $(this).find(':selected').data('namapc1');

      $('#profit_center_kas_show1').val(namapc1);
      $('#profit_center_kas1').val(pc1);
      $('#currency1').val('IDR');
      $('#kode_kas1').val(kode1);

      hitung_total1();

    });


    function UbahCostArc1(val) {
      var profit = val; // Ambil nilai parameter
      // alert(profit); // Debug: pastikan nilai 'profit' diterima

      // Pastikan selektor dropdown sesuai dengan nama elemen
      const costCtrDropdown = $('select[name="cost1"]');
      const no_coa = $('select[name=coa1] option').filter(':selected').val();
      console.log(profit + ' ' + no_coa)

      // Hapus opsi yang ada terlebih dahulu, kecuali opsi default
      costCtrDropdown.find('option').not(':first').remove();

      $.ajax({
        url: 'getCostCenter.php', // URL endpoint server Anda
        type: 'POST',
        data: {
          prof_ctr: profit,
          no_coa: no_coa
        }, // Kirim data prof_ctr ke server
        dataType: 'json',
        success: function(response) {
          // Periksa apakah respons valid
          if (response && response.length > 0) {
            $.each(response, function(index, costCtr) {
              console.log(costCtr);
              costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
            });

            costCtrDropdown.selectpicker('refresh');
          } else {
            console.error('Tidak ada data yang diterima dari server.');
            alert('Tidak ada data cost center yang tersedia.');
          }
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    }

    $(document).on('change', '.prof_ctr1', function() {
      const selectedProfCtr = $(this).val();
      const row = $(this).closest('tr');
      const selectedCoa = row.find('select.no_coa1').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter1(selectedProfCtr, selectedCoa, row);
    });

    $(document).on('change', '.no_coa1', function() {
      const selectedCoa = $(this).val();
      const row = $(this).closest('tr');
      const selectedProfCtr = row.find('select.prof_ctr1').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter1(selectedProfCtr, selectedCoa, row);
    });



    // Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
    function updateCostCenter1(profCtr, noCoa, row) {
      const costCtrDropdown = $(row).find('.cost_ctr1'); // dropdown cost center pada baris tsb

      // Kosongkan dropdown cost_ctr sebelum diisi
      costCtrDropdown.selectpicker('destroy'); // Hancurkan selectpicker lama
      costCtrDropdown.empty(); // Kosongkan semua opsi yang ada
      costCtrDropdown.append('<option value="-"> - </option>'); // Tambahkan opsi default
      costCtrDropdown.selectpicker(); // Inisialisasi ulang selectpicker

      if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
          url: 'getCostCenter.php', // Ganti dengan URL endpoint server Anda
          type: 'POST',
          data: {
            prof_ctr: profCtr,
            no_coa: noCoa
          }, // Kirim data prof_ctr ke server
          dataType: 'json',
          success: function(response) {
            if (response && response.length > 0) {
              $.each(response, function(index, costCtr) {
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
          error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
          }
        });
      } else {
        costCtrDropdown.selectpicker('refresh');
      }
    }

    function initializePlugins() {
      $(function() {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true
        });
        $('.select2').select2({
          theme: 'bootstrap4'
        });
      });
    }


    // JavaScript Document
    function addRow1(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det1" name="currenc1[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

<td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

</tr>

`;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_kas1').val();

      if (headerPC) {

        $(row).find('.prof_ctr1').val(headerPC);
        $(row).find('.prof_ctr1').selectpicker('refresh');

      }

    }


    function deleteRow1(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var deleted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a1[]"]');

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


    function InsertRow1(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inserted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a1[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det1" name="currenc1[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

<td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

</tr>
`;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_kas1').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr1').val(headerPC);
              $(newRow).find('.prof_ctr1').selectpicker('refresh');

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
    return new Intl.NumberFormat('en-US').format(x);
}


    function modal_input_amt1(el){

let row = $(el).closest('tr');

let debit  = parseFloat($(el).val()) || 0;
let creditInput = row.find('input[name="txt_credit1[]"]');

if(debit > 0){

creditInput.val(0);
creditInput.prop('readonly',true);

}else{

creditInput.prop('readonly',false);

}

hitung_total1();

}


function modal_input_cre1(el){

let row = $(el).closest('tr');

let credit = parseFloat($(el).val()) || 0;
let debitInput = row.find('input[name="txt_amount1[]"]');

if(credit > 0){

debitInput.val(0);
debitInput.prop('readonly',true);

}else{

debitInput.prop('readonly',false);

}

hitung_total1();

}


function hitung_total1(){

  // alert('tes');

let debit_nag = 0;
let credit_nag = 0;

let debit_nak = 0;
let credit_nak = 0;

console.log("===== MULAI HITUNG TOTAL =====");

/* ======================
   RATE HEADER
====================== */

let rate = 1;

console.log("Rate Header :", rate);


/* ======================
   DETAIL ROW
====================== */

$('#tbody1 tr').each(function(index){

let pc = $(this).find('select.prof_ctr1').val();
let curr = $(this).find('select[name="currenc1[]"]').val();

let debitVal  = $(this).find('input[name="txt_amount1[]"]').val();
let creditVal = $(this).find('input[name="txt_credit1[]"]').val();

let debit  = parseFloat(debitVal)  || 0;
let credit = parseFloat(creditVal) || 0;

console.log("----- ROW",index,"-----");
console.log("PC :", pc);
console.log("Currency :", curr);
console.log("Debit Raw :", debit);
console.log("Credit Raw :", credit);


/* ======================
   KONVERSI USD
====================== */

if(curr === 'USD'){

debit  = debit  * rate;
credit = credit * rate;

console.log("Debit x Rate :", debit);
console.log("Credit x Rate :", credit);

}


if(pc === 'NAG'){
debit_nag  += debit;
credit_nag += credit;
}

if(pc === 'NAK'){
debit_nak  += debit;
credit_nak += credit;
}

});


/* ======================
   HEADER AMOUNT
====================== */

let header_pc = $('#profit_center_kas1').val();

let amount = parseFloat($('#amount_kas1').val().replace(/,/g,'')) || 0;

let eqv = amount * rate;

console.log("===== HEADER DATA =====");
console.log("Header PC :", header_pc);
console.log("Amount :", amount);
console.log("Equivalent :", eqv);



/* tambahkan ke debit */

if(header_pc === 'NAG'){
credit_nag += eqv;
}

if(header_pc === 'NAK'){
credit_nak += eqv;
}


/* ======================
   GRAND TOTAL
====================== */

let grand_debit  = debit_nag + debit_nak;
let grand_credit = credit_nag + credit_nak;

console.log("===== HASIL =====");

console.log("Debit NAG :", debit_nag);
console.log("Credit NAG :", credit_nag);

console.log("Debit NAK :", debit_nak);
console.log("Credit NAK :", credit_nak);

console.log("Grand Debit :", grand_debit);
console.log("Grand Credit :", grand_credit);


/* tampil */

$('#tot_debit_nag1').val(formatAngka(debit_nag));
$('#tot_credit_nag1').val(formatAngka(credit_nag));

$('#tot_debit_nak1').val(formatAngka(debit_nak));
$('#tot_credit_nak1').val(formatAngka(credit_nak));

$('#tot_debit1').val(formatAngka(grand_debit));
$('#tot_credit1').val(formatAngka(grand_credit));


/* hidden */

$('#h_tot_debit_nag1').val(debit_nag);
$('#h_tot_credit_nag1').val(credit_nag);

$('#h_tot_debit_nak1').val(debit_nak);
$('#h_tot_credit_nak1').val(credit_nak);

$('#h_tot_debit1').val(grand_debit);
$('#h_tot_credit1').val(grand_credit);

}




$(document).on('change','.prof_ctr1,.curr_det1',function(){

hitung_total1();

});

$('#amount_kas1').on('keyup change', function(){

hitung_total1();

});



$('#simpan1').on('click', function () {

    console.log("===== MULAI SAVE =====");

    let source  = $('#nama_supp1').val();
  let account = $('#account1').val();

  if(source == ''){
    Swal.fire('Warning','Supplier tidak boleh kosong','warning');
    return;
  }

  if(account == ''){
    Swal.fire('Warning','Account tidak boleh kosong','warning');
    return;
  }

    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let rowIndex = 0;
    let error = false;

    console.log("===== LOOP DETAIL =====");

    $("#tbody1 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('select.no_coa1').first().val();
        let pc  = tr.find('select.prof_ctr1').first().val();
        let cc  = tr.find('select.cost_ctr1').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount1[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit1[]"]').val()) || 0;

        console.log("----- ROW",rowIndex,"-----");
        console.log("COA :", coa);
        console.log("PC :", pc);
        console.log("Cost Center :", cc);
        console.log("Debit :", debit);
        console.log("Credit :", credit);

        /* VALIDASI COST CENTER */

        if(coaWajibCC.includes(coa)){

            console.log("COA WAJIB CC :", coa);

            if(cc == '-' || cc == '' || cc == null){

                console.log("ERROR: Cost Center kosong");

                Swal.fire(
                    'Warning',
                    'COA '+coa+' wajib isi Cost Center',
                    'warning'
                );

                error = true;
                return false;
            }
        }

        /* HITUNG PER PC */

        if(pc == 'NAG'){
            debitNAG  += debit;
            creditNAG += credit;
        }

        if(pc == 'NAK'){
            debitNAK  += debit;
            creditNAK += credit;
        }

        rowIndex++;

    });

    if(error) return;

   /* ======================
   HEADER BANK
====================== */

    let header_pc = $('#profit_center_kas1').val();

    let header_debit = parseFloat(
        $('#amount_kas1').val().replace(/,/g,'')
    ) || 0;

    console.log("===== HEADER BANK =====");
    console.log("Header PC :", header_pc);
    console.log("Debit Bank :", header_debit);


    /* ======================
       HASIL AKUMULASI
    ====================== */

    console.log("===== HASIL AKUMULASI =====");

    console.log("Debit NAG :", debitNAG);
    console.log("Credit NAG :", creditNAG);

    console.log("Debit NAK :", debitNAK);
    console.log("Credit NAK :", creditNAK);


    /* ======================
       VALIDASI PER PC (FIX)
    ====================== */

    if(header_pc == 'NAG'){

        let totalDebit  = debitNAG;
        let totalCredit = header_debit + creditNAG;

        console.log("TOTAL DEBIT NAG :", totalDebit);
        console.log("TOTAL CREDIT NAG :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAG tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Garment tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }

    if(header_pc == 'NAK'){

        let totalDebit  = debitNAK;
        let totalCredit = header_debit + creditNAK;

        console.log("TOTAL DEBIT NAK :", totalDebit);
        console.log("TOTAL CREDIT NAK :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAK tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Knitting tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }


    console.log("VALIDASI LOLOS");

    console.log($('#form-data1').serialize());

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){

            console.log("PROSES AJAX SAVE");

            $.ajax({

                url: "petty-out/save_pettyout_none.php",
                type: "POST",
                data: $('#form-data1').serialize(),

                beforeSend:function(){

                    console.log("Sending data...");

                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick:false,
                        didOpen:()=>{
                            Swal.showLoading();
                        }
                    });

                },

                success:function(res){

                    console.log("Response Server :", res);

                    let r = JSON.parse(res);

                    if(r.status == 'success'){

                        console.log("SAVE SUCCESS");

                        Swal.fire({
                            icon:'success',
                            title:'Success',
                            text:r.message
                        }).then(()=>{
                            location.href='petty-cashout.php';
                        });

                    }else{

                        console.log("SAVE ERROR :", r.message);

                        Swal.fire(
                            'Error',
                            r.message,
                            'error'
                        );

                    }

                },

                error:function(xhr){

                    console.log("AJAX ERROR");
                    console.log(xhr.responseText);

                    Swal.fire('Error','Server Error','error');
                }

            });

        }

    });

});


//ADVANCE.....................................................................................


    $('#account3').on('change', function() {

      let kode3 = $(this).find(':selected').data('kode3');
      let pc3 = $(this).find(':selected').data('pc3');
      let namapc3 = $(this).find(':selected').data('namapc3');

      $('#profit_center_kas_show3').val(namapc3);
      $('#profit_center_kas3').val(pc3);
      $('#currency3').val('IDR');
      $('#kode_kas3').val(kode3);

      hitung_total3();

    });


    function UbahCostArc3(val) {
      var profit = val; // Ambil nilai parameter
      // alert(profit); // Debug: pastikan nilai 'profit' diterima

      // Pastikan selektor dropdown sesuai dengan nama elemen
      const costCtrDropdown = $('select[name="cost3"]');
      const no_coa = $('select[name=coa3] option').filter(':selected').val();
      console.log(profit + ' ' + no_coa)

      // Hapus opsi yang ada terlebih dahulu, kecuali opsi default
      costCtrDropdown.find('option').not(':first').remove();

      $.ajax({
        url: 'getCostCenter.php', // URL endpoint server Anda
        type: 'POST',
        data: {
          prof_ctr: profit,
          no_coa: no_coa
        }, // Kirim data prof_ctr ke server
        dataType: 'json',
        success: function(response) {
          // Periksa apakah respons valid
          if (response && response.length > 0) {
            $.each(response, function(index, costCtr) {
              console.log(costCtr);
              costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
            });

            costCtrDropdown.selectpicker('refresh');
          } else {
            console.error('Tidak ada data yang diterima dari server.');
            alert('Tidak ada data cost center yang tersedia.');
          }
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    }

    $(document).on('change', '.prof_ctr3', function() {
      const selectedProfCtr = $(this).val();
      const row = $(this).closest('tr');
      const selectedCoa = row.find('select.no_coa3').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter3(selectedProfCtr, selectedCoa, row);
    });

    $(document).on('change', '.no_coa3', function() {
      const selectedCoa = $(this).val();
      const row = $(this).closest('tr');
      const selectedProfCtr = row.find('select.prof_ctr3').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter3(selectedProfCtr, selectedCoa, row);
    });



    // Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
    function updateCostCenter3(profCtr, noCoa, row) {
      const costCtrDropdown = $(row).find('.cost_ctr3'); // dropdown cost center pada baris tsb

      // Kosongkan dropdown cost_ctr sebelum diisi
      costCtrDropdown.selectpicker('destroy'); // Hancurkan selectpicker lama
      costCtrDropdown.empty(); // Kosongkan semua opsi yang ada
      costCtrDropdown.append('<option value="-"> - </option>'); // Tambahkan opsi default
      costCtrDropdown.selectpicker(); // Inisialisasi ulang selectpicker

      if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
          url: 'getCostCenter.php', // Ganti dengan URL endpoint server Anda
          type: 'POST',
          data: {
            prof_ctr: profCtr,
            no_coa: noCoa
          }, // Kirim data prof_ctr ke server
          dataType: 'json',
          success: function(response) {
            if (response && response.length > 0) {
              $.each(response, function(index, costCtr) {
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
          error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
          }
        });
      } else {
        costCtrDropdown.selectpicker('refresh');
      }
    }

    function initializePlugins() {
      $(function() {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true
        });
        $('.select2').select2({
          theme: 'bootstrap4'
        });
      });
    }


    // JavaScript Document
    function addRow3(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
<td><input type="checkbox" id="select3" name="select3[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa3" name="nomor_coa3[]" data-live-search="true" data-width="220px" data-size="5">
<option value="1.49.98" >1.49.98 UANG MUKA PEMBELIAN - KAS KECIL</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '1.49.98'");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr3" name="prof_ctr3[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr3" name="cost_ctr3[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer3[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws3[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det3" name="currenc3[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount3[]" oninput="modal_input_amt3(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit3[]" oninput="modal_input_cre3(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan3[]" autocomplete="off"></td>

<td><input name="chk_a3[]" type="checkbox" class="checkall_a3"></td>

</tr>

`;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_kas3').val();

      if (headerPC) {

        $(row).find('.prof_ctr3').val(headerPC);
        $(row).find('.prof_ctr3').selectpicker('refresh');

      }

    }


    function deleteRow3(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var deleted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a3[]"]');

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


    function InsertRow3(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inserted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a3[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
<tr>
<td><input type="checkbox" id="select3" name="select3[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa3" name="nomor_coa3[]" data-live-search="true" data-width="220px" data-size="5">
<option value="1.49.98" >1.49.98 UANG MUKA PEMBELIAN - KAS KECIL</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '1.49.98'");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr3" name="prof_ctr3[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr3" name="cost_ctr3[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer3[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws3[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det3" name="currenc3[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount3[]" oninput="modal_input_amt3(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit3[]" oninput="modal_input_cre3(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan3[]" autocomplete="off"></td>

<td><input name="chk_a3[]" type="checkbox" class="checkall_a3"></td>

</tr>
`;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_kas3').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr3').val(headerPC);
              $(newRow).find('.prof_ctr3').selectpicker('refresh');

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
    return new Intl.NumberFormat('en-US').format(x);
}


    function modal_input_amt3(el){

let row = $(el).closest('tr');

let debit  = parseFloat($(el).val()) || 0;
let creditInput = row.find('input[name="txt_credit3[]"]');

if(debit > 0){

creditInput.val(0);
creditInput.prop('readonly',true);

}else{

creditInput.prop('readonly',false);

}

hitung_total3();

}


function modal_input_cre3(el){

let row = $(el).closest('tr');

let credit = parseFloat($(el).val()) || 0;
let debitInput = row.find('input[name="txt_amount3[]"]');

if(credit > 0){

debitInput.val(0);
debitInput.prop('readonly',true);

}else{

debitInput.prop('readonly',false);

}

hitung_total3();

}


function hitung_total3(){

  // alert('tes');

let debit_nag = 0;
let credit_nag = 0;

let debit_nak = 0;
let credit_nak = 0;

console.log("===== MULAI HITUNG TOTAL =====");

/* ======================
   RATE HEADER
====================== */

let rate = 1;

console.log("Rate Header :", rate);


/* ======================
   DETAIL ROW
====================== */

$('#tbody3 tr').each(function(index){

let pc = $(this).find('select.prof_ctr3').val();
let curr = $(this).find('select[name="currenc3[]"]').val();

let debitVal  = $(this).find('input[name="txt_amount3[]"]').val();
let creditVal = $(this).find('input[name="txt_credit3[]"]').val();

let debit  = parseFloat(debitVal)  || 0;
let credit = parseFloat(creditVal) || 0;

console.log("----- ROW",index,"-----");
console.log("PC :", pc);
console.log("Currency :", curr);
console.log("Debit Raw :", debit);
console.log("Credit Raw :", credit);


/* ======================
   KONVERSI USD
====================== */

if(curr === 'USD'){

debit  = debit  * rate;
credit = credit * rate;

console.log("Debit x Rate :", debit);
console.log("Credit x Rate :", credit);

}


if(pc === 'NAG'){
debit_nag  += debit;
credit_nag += credit;
}

if(pc === 'NAK'){
debit_nak  += debit;
credit_nak += credit;
}

});


/* ======================
   HEADER AMOUNT
====================== */

let header_pc = $('#profit_center_kas3').val();

let amount = parseFloat($('#amount_kas3').val().replace(/,/g,'')) || 0;

let eqv = amount * rate;

console.log("===== HEADER DATA =====");
console.log("Header PC :", header_pc);
console.log("Amount :", amount);
console.log("Equivalent :", eqv);



/* tambahkan ke debit */

if(header_pc === 'NAG'){
credit_nag += eqv;
}

if(header_pc === 'NAK'){
credit_nak += eqv;
}


/* ======================
   GRAND TOTAL
====================== */

let grand_debit  = debit_nag + debit_nak;
let grand_credit = credit_nag + credit_nak;

console.log("===== HASIL =====");

console.log("Debit NAG :", debit_nag);
console.log("Credit NAG :", credit_nag);

console.log("Debit NAK :", debit_nak);
console.log("Credit NAK :", credit_nak);

console.log("Grand Debit :", grand_debit);
console.log("Grand Credit :", grand_credit);


/* tampil */

$('#tot_debit_nag3').val(formatAngka(debit_nag));
$('#tot_credit_nag3').val(formatAngka(credit_nag));

$('#tot_debit_nak3').val(formatAngka(debit_nak));
$('#tot_credit_nak3').val(formatAngka(credit_nak));

$('#tot_debit3').val(formatAngka(grand_debit));
$('#tot_credit3').val(formatAngka(grand_credit));


/* hidden */

$('#h_tot_debit_nag3').val(debit_nag);
$('#h_tot_credit_nag3').val(credit_nag);

$('#h_tot_debit_nak3').val(debit_nak);
$('#h_tot_credit_nak3').val(credit_nak);

$('#h_tot_debit3').val(grand_debit);
$('#h_tot_credit3').val(grand_credit);

}




$(document).on('change','.prof_ctr3,.curr_det3',function(){

hitung_total3();

});

$('#amount_kas3').on('keyup change', function(){

hitung_total3();

});



$('#simpan3').on('click', function () {

    console.log("===== MULAI SAVE =====");

    let source  = $('#nama_supp3').val();
  let account = $('#account3').val();

  if(source == ''){
    Swal.fire('Warning','Supplier tidak boleh kosong','warning');
    return;
  }

  if(account == ''){
    Swal.fire('Warning','Account tidak boleh kosong','warning');
    return;
  }

    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let rowIndex = 0;
    let error = false;

    console.log("===== LOOP DETAIL =====");

    $("#tbody3 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('select.no_coa3').first().val();
        let pc  = tr.find('select.prof_ctr3').first().val();
        let cc  = tr.find('select.cost_ctr3').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount3[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit3[]"]').val()) || 0;

        console.log("----- ROW",rowIndex,"-----");
        console.log("COA :", coa);
        console.log("PC :", pc);
        console.log("Cost Center :", cc);
        console.log("Debit :", debit);
        console.log("Credit :", credit);

        /* VALIDASI COST CENTER */

        if(coaWajibCC.includes(coa)){

            console.log("COA WAJIB CC :", coa);

            if(cc == '-' || cc == '' || cc == null){

                console.log("ERROR: Cost Center kosong");

                Swal.fire(
                    'Warning',
                    'COA '+coa+' wajib isi Cost Center',
                    'warning'
                );

                error = true;
                return false;
            }
        }

        /* HITUNG PER PC */

        if(pc == 'NAG'){
            debitNAG  += debit;
            creditNAG += credit;
        }

        if(pc == 'NAK'){
            debitNAK  += debit;
            creditNAK += credit;
        }

        rowIndex++;

    });

    if(error) return;

   /* ======================
   HEADER BANK
====================== */

    let header_pc = $('#profit_center_kas3').val();

    let header_debit = parseFloat(
        $('#amount_kas3').val().replace(/,/g,'')
    ) || 0;

    console.log("===== HEADER BANK =====");
    console.log("Header PC :", header_pc);
    console.log("Debit Bank :", header_debit);


    /* ======================
       HASIL AKUMULASI
    ====================== */

    console.log("===== HASIL AKUMULASI =====");

    console.log("Debit NAG :", debitNAG);
    console.log("Credit NAG :", creditNAG);

    console.log("Debit NAK :", debitNAK);
    console.log("Credit NAK :", creditNAK);


    /* ======================
       VALIDASI PER PC (FIX)
    ====================== */

    if(header_pc == 'NAG'){

        let totalDebit  = debitNAG;
        let totalCredit = header_debit + creditNAG;

        console.log("TOTAL DEBIT NAG :", totalDebit);
        console.log("TOTAL CREDIT NAG :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAG tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Garment tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }

    if(header_pc == 'NAK'){

        let totalDebit  = debitNAK;
        let totalCredit = header_debit + creditNAK;

        console.log("TOTAL DEBIT NAK :", totalDebit);
        console.log("TOTAL CREDIT NAK :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAK tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Knitting tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }


    console.log("VALIDASI LOLOS");

    console.log($('#form-data3').serialize());

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){

            console.log("PROSES AJAX SAVE");

            $.ajax({

                url: "petty-out/save_pettyout_advance.php",
                type: "POST",
                data: $('#form-data3').serialize(),

                beforeSend:function(){

                    console.log("Sending data...");

                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick:false,
                        didOpen:()=>{
                            Swal.showLoading();
                        }
                    });

                },

                success:function(res){

                    console.log("Response Server :", res);

                    let r = JSON.parse(res);

                    if(r.status == 'success'){

                        console.log("SAVE SUCCESS");

                        Swal.fire({
                            icon:'success',
                            title:'Success',
                            text:r.message
                        }).then(()=>{
                           location.href='petty-cashout.php';
                        });

                    }else{

                        console.log("SAVE ERROR :", r.message);

                        Swal.fire(
                            'Error',
                            r.message,
                            'error'
                        );

                    }

                },

                error:function(xhr){

                    console.log("AJAX ERROR");
                    console.log(xhr.responseText);

                    Swal.fire('Error','Server Error','error');
                }

            });

        }

    });

});


//SETTLEMENT......................................................................................

 function getdataadvance(val){
    var no_pco = val;
    // alert(no_pco);

    $.ajax({
        type:'POST',
        url:'petty-in/get_data_settlement.php',
        data: {'no_pco':no_pco},
        cache: 'false',
        close: function(e){
            e.preventDefault();
            return false; 
        },
        success: function(data){

    $('#tbody2').html(data); // langsung ganti isi

    $('.selectpicker').selectpicker('refresh');

    hitung_total2();
}
,
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr);
            alert(xhr);
        }
    }); 
}

$('#account2').on('change', function() {

      let kode2 = $(this).find(':selected').data('kode2');
      let pc2 = $(this).find(':selected').data('pc2');
      let namapc2 = $(this).find(':selected').data('namapc2');

      $('#profit_center_kas_show2').val(namapc2);
      $('#profit_center_kas2').val(pc2);
      $('#currency2').val('IDR');
      $('#kode_kas2').val(kode2);



      hitung_total2();

    });

function UbahCostArc2(val) {
      var profit = val; // Ambil nilai parameter
      // alert(profit); // Debug: pastikan nilai 'profit' diterima

      // Pastikan selektor dropdown sesuai dengan nama elemen
      const costCtrDropdown = $('select[name="cost2"]');
      const no_coa = $('select[name=coa2] option').filter(':selected').val();
      console.log(profit + ' ' + no_coa)

      // Hapus opsi yang ada terlebih dahulu, kecuali opsi default
      costCtrDropdown.find('option').not(':first').remove();

      $.ajax({
        url: 'getCostCenter.php', // URL endpoint server Anda
        type: 'POST',
        data: {
          prof_ctr: profit,
          no_coa: no_coa
        }, // Kirim data prof_ctr ke server
        dataType: 'json',
        success: function(response) {
          // Periksa apakah respons valid
          if (response && response.length > 0) {
            $.each(response, function(index, costCtr) {
              console.log(costCtr);
              costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
            });

            costCtrDropdown.selectpicker('refresh');
          } else {
            console.error('Tidak ada data yang diterima dari server.');
            alert('Tidak ada data cost center yang tersedia.');
          }
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText);
        }
      });
    }

    $(document).on('change', '.prof_ctr2', function() {
      const selectedProfCtr = $(this).val();
      const row = $(this).closest('tr');
      const selectedCoa = row.find('select.no_coa2').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter2(selectedProfCtr, selectedCoa, row);
    });

    $(document).on('change', '.no_coa2', function() {
      const selectedCoa = $(this).val();
      const row = $(this).closest('tr');
      const selectedProfCtr = row.find('select.prof_ctr2').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter2(selectedProfCtr, selectedCoa, row);
    });



    // Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
    function updateCostCenter2(profCtr, noCoa, row) {
      const costCtrDropdown = $(row).find('.cost_ctr2'); // dropdown cost center pada baris tsb

      // Kosongkan dropdown cost_ctr sebelum diisi
      costCtrDropdown.selectpicker('destroy'); // Hancurkan selectpicker lama
      costCtrDropdown.empty(); // Kosongkan semua opsi yang ada
      costCtrDropdown.append('<option value="-"> - </option>'); // Tambahkan opsi default
      costCtrDropdown.selectpicker(); // Inisialisasi ulang selectpicker

      if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
          url: 'getCostCenter.php', // Ganti dengan URL endpoint server Anda
          type: 'POST',
          data: {
            prof_ctr: profCtr,
            no_coa: noCoa
          }, // Kirim data prof_ctr ke server
          dataType: 'json',
          success: function(response) {
            if (response && response.length > 0) {
              $.each(response, function(index, costCtr) {
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
          error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
          }
        });
      } else {
        costCtrDropdown.selectpicker('refresh');
      }
    }

    function initializePlugins() {
      $(function() {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true
        });
        $('.select2').select2({
          theme: 'bootstrap4'
        });
      });
    }

    // JavaScript Document
    function addRow2(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
<td><input type="checkbox" id="select2" name="select2[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa2" name="nomor_coa2[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr2" name="prof_ctr2[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr2" name="cost_ctr2[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer2[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws2[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det2" name="currenc2[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount2[]" oninput="modal_input_amt2(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit2[]" oninput="modal_input_cre2(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan2[]" autocomplete="off"></td>

<td><input name="chk_a2[]" type="checkbox" class="checkall_a2"></td>

</tr>

`;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_kas2').val();

      if (headerPC) {

        $(row).find('.prof_ctr2').val(headerPC);
        $(row).find('.prof_ctr2').selectpicker('refresh');

      }

    }


    function deleteRow2(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var deleted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a2[]"]');

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

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inserted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a2[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
<tr>
<td><input type="checkbox" id="select2" name="select2[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa2" name="nomor_coa2[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr2" name="prof_ctr2[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr2" name="cost_ctr2[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer2[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws2[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det2" name="currenc2[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount2[]" oninput="modal_input_amt2(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit2[]" oninput="modal_input_cre2(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan2[]" autocomplete="off"></td>

<td><input name="chk_a2[]" type="checkbox" class="checkall_a2"></td>

</tr>
`;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_kas2').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr2').val(headerPC);
              $(newRow).find('.prof_ctr2').selectpicker('refresh');

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
    return new Intl.NumberFormat('en-US').format(x);
}


function modal_input_amt2(el){

let row = $(el).closest('tr');

let debit  = parseFloat($(el).val()) || 0;
let creditInput = row.find('input[name="txt_credit2[]"]');

if(debit > 0){

creditInput.val(0);
creditInput.prop('readonly',true);

}else{

creditInput.prop('readonly',false);

}

hitung_total2();

}


function modal_input_cre2(el){

let row = $(el).closest('tr');

let credit = parseFloat($(el).val()) || 0;
let debitInput = row.find('input[name="txt_amount2[]"]');

if(credit > 0){

debitInput.val(0);
debitInput.prop('readonly',true);

}else{

debitInput.prop('readonly',false);

}

hitung_total2();

}


function hitung_total2(){
  // alert('tes');

let debit_nag = 0;
let credit_nag = 0;

let debit_nak = 0;
let credit_nak = 0;

console.log("===== MULAI HITUNG TOTAL =====");

/* ======================
   RATE HEADER
====================== */

let rate = 1;

console.log("Rate Header :", rate);


/* ======================
   DETAIL ROW
====================== */

$('#tbody2 tr').each(function(index){

let pc = $(this).find('select.prof_ctr2').val();
let curr = $(this).find('select[name="currenc2[]"]').val();

let debitVal  = $(this).find('input[name="txt_amount2[]"]').val();
let creditVal = $(this).find('input[name="txt_credit2[]"]').val();

let debit  = parseFloat(debitVal)  || 0;
let credit = parseFloat(creditVal) || 0;

console.log("----- ROW",index,"-----");
console.log("PC :", pc);
console.log("Currency :", curr);
console.log("Debit Raw :", debit);
console.log("Credit Raw :", credit);


/* ======================
   KONVERSI USD
====================== */

if(curr === 'USD'){

debit  = debit  * rate;
credit = credit * rate;

console.log("Debit x Rate :", debit);
console.log("Credit x Rate :", credit);

}


if(pc === 'NAG'){
debit_nag  += debit;
credit_nag += credit;
}

if(pc === 'NAK'){
debit_nak  += debit;
credit_nak += credit;
}

});


/* ======================
   HEADER AMOUNT
====================== */

let header_pc = $('#profit_center_kas2').val();

let amount = parseFloat($('#amount_kas2').val().replace(/,/g,'')) || 0;

let eqv = amount * rate;

console.log("===== HEADER DATA =====");
console.log("Header PC :", header_pc);
console.log("Amount :", amount);
console.log("Equivalent :", eqv);



/* tambahkan ke debit */

if(header_pc === 'NAG'){
credit_nag += eqv;
}

if(header_pc === 'NAK'){
credit_nak += eqv;
}


/* ======================
   GRAND TOTAL
====================== */

let grand_debit  = debit_nag + debit_nak;
let grand_credit = credit_nag + credit_nak;

console.log("===== HASIL =====");

console.log("Debit NAG :", debit_nag);
console.log("Credit NAG :", credit_nag);

console.log("Debit NAK :", debit_nak);
console.log("Credit NAK :", credit_nak);

console.log("Grand Debit :", grand_debit);
console.log("Grand Credit :", grand_credit);


/* tampil */

$('#tot_debit_nag2').val(formatAngka(debit_nag));
$('#tot_credit_nag2').val(formatAngka(credit_nag));

$('#tot_debit_nak2').val(formatAngka(debit_nak));
$('#tot_credit_nak2').val(formatAngka(credit_nak));

$('#tot_debit2').val(formatAngka(grand_debit));
$('#tot_credit2').val(formatAngka(grand_credit));


/* hidden */

$('#h_tot_debit_nag2').val(debit_nag);
$('#h_tot_credit_nag2').val(credit_nag);

$('#h_tot_debit_nak2').val(debit_nak);
$('#h_tot_credit_nak2').val(credit_nak);

$('#h_tot_debit2').val(grand_debit);
$('#h_tot_credit2').val(grand_credit);

}




$(document).on('change','.prof_ctr2,.curr_det2',function(){

hitung_total2();

});

$('#amount_kas2').on('keyup change', function(){

hitung_total2();

});



$('#simpan2').on('click', function () {

    console.log("===== MULAI SAVE =====");

    let account = $('#account2').val();

    console.log("Account :", account);


    if(account == ''){
        Swal.fire('Warning','Account tidak boleh kosong','warning');
        return;
    }

    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let rowIndex = 0;
    let error = false;

    console.log("===== LOOP DETAIL =====");

    $("#tbody2 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('select.no_coa2').first().val();
        let pc  = tr.find('select.prof_ctr2').first().val();
        let cc  = tr.find('select.cost_ctr2').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount2[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit2[]"]').val()) || 0;

        console.log("----- ROW",rowIndex,"-----");
        console.log("COA :", coa);
        console.log("PC :", pc);
        console.log("Cost Center :", cc);
        console.log("Debit :", debit);
        console.log("Credit :", credit);

        /* VALIDASI COST CENTER */

        if(coaWajibCC.includes(coa)){

            console.log("COA WAJIB CC :", coa);

            if(cc == '-' || cc == '' || cc == null){

                console.log("ERROR: Cost Center kosong");

                Swal.fire(
                    'Warning',
                    'COA '+coa+' wajib isi Cost Center',
                    'warning'
                );

                error = true;
                return false;
            }
        }

        /* HITUNG PER PC */

        if(pc == 'NAG'){
            debitNAG  += debit;
            creditNAG += credit;
        }

        if(pc == 'NAK'){
            debitNAK  += debit;
            creditNAK += credit;
        }

        rowIndex++;

    });

    if(error) return;

   /* ======================
   HEADER BANK
====================== */

    let header_pc = $('#profit_center_kas2').val();

    let header_debit = parseFloat(
        $('#amount_kas2').val().replace(/,/g,'')
    ) || 0;

    console.log("===== HEADER BANK =====");
    console.log("Header PC :", header_pc);
    console.log("Debit Bank :", header_debit);


    /* ======================
       HASIL AKUMULASI
    ====================== */

    console.log("===== HASIL AKUMULASI =====");

    console.log("Debit NAG :", debitNAG);
    console.log("Credit NAG :", creditNAG);

    console.log("Debit NAK :", debitNAK);
    console.log("Credit NAK :", creditNAK);


    /* ======================
       VALIDASI PER PC (FIX)
    ====================== */

    if(header_pc == 'NAG'){

        let totalDebit  = debitNAG;
        let totalCredit = header_debit + creditNAG;

        console.log("TOTAL DEBIT NAG :", totalDebit);
        console.log("TOTAL CREDIT NAG :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAG tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Garment tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }

    if(header_pc == 'NAK'){

        let totalDebit  = debitNAK;
        let totalCredit = header_debit + creditNAK;

        console.log("TOTAL DEBIT NAK :", totalDebit);
        console.log("TOTAL CREDIT NAK :", totalCredit);

        if(totalDebit != totalCredit){

            console.log("ERROR: NAK tidak balance (HEADER + DETAIL)");

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Knitting tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }


    console.log("VALIDASI LOLOS");

    console.log($('#form-data2').serialize());

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){

            console.log("PROSES AJAX SAVE");

            $.ajax({

                url: "petty-out/save_pettyout_settle.php",
                type: "POST",
                data: $('#form-data2').serialize(),

                beforeSend:function(){

                    console.log("Sending data...");

                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick:false,
                        didOpen:()=>{
                            Swal.showLoading();
                        }
                    });

                },

                success:function(res){

                    console.log("Response Server :", res);

                    let r = JSON.parse(res);

                    if(r.status == 'success'){

                        console.log("SAVE SUCCESS");

                        Swal.fire({
                            icon:'success',
                            title:'Success',
                            text:r.message
                        }).then(()=>{
                           location.href='petty-cashout.php';
                        });

                    }else{

                        console.log("SAVE ERROR :", r.message);

                        Swal.fire(
                            'Error',
                            r.message,
                            'error'
                        );

                    }

                },

                error:function(xhr){

                    console.log("AJAX ERROR");
                    console.log(xhr.responseText);

                    Swal.fire('Error','Server Error','error');
                }

            });

        }

    });

});

//LIST PAYMENT...........................................................................
let isManualAmount = false;
function getNumber(val) {
      return parseFloat(val.replace(/,/g, '')) || 0;
    }

let tableLP;

    function initTableLP(){

    // destroy dulu kalau sudah pernah dibuat
    if ($.fn.DataTable.isDataTable('#table-lp')) {
      $('#table-lp').DataTable().destroy();
    }

    tableLP = $('#table-lp').DataTable({
      paging: true,
      searching: true,
      ordering: false,
      info: false,
      autoWidth: false,
      responsive: true,
      pageLength: 10,
      lengthMenu: [10, 25, 50],
      language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
        paginate: {
          previous: "Prev",
          next: "Next"
        }
      }
    });

  }


$('#account4').on('change', function() {

      let kode4 = $(this).find(':selected').data('kode4');
      let pc4 = $(this).find(':selected').data('pc4');
      let namapc4 = $(this).find(':selected').data('namapc4');

      $('#profit_center_kas_show4').val(namapc4);
      $('#profit_center_kas4').val(pc4);
      $('#currency4').val('IDR');
      $('#kode_kas4').val(kode4);
      hitungTotalLP();


    });


$('#btn_tarik_lp').on('click', function(){

  let tgl_awal  = $('#tgl_filawal4').val();
  let tgl_akhir = $('#tgl_filakhir4').val();
  let supplier  = $('#nama_supp4').val();

  if(tgl_awal == '' || tgl_akhir == ''){
    Swal.fire('Warning','Tanggal harus diisi','warning');
    return;
  }

  if(supplier == ''){
    Swal.fire('Warning','Supplier harus dipilih','warning');
    return;
  }

  $.ajax({
    url: 'bank-out/get_lp_ajax.php',
    type: 'POST',
    data: {
      tgl_awal: tgl_awal,
      tgl_akhir: tgl_akhir,
      supplier: supplier
    },
    beforeSend:function(){
      Swal.fire({
        title:'Loading...',
        allowOutsideClick:false,
        didOpen:()=>{ Swal.showLoading(); }
      });
    },
    success:function(res){
      if ($.fn.DataTable.isDataTable('#table-lp')) {
        $('#table-lp').DataTable().clear().destroy();
      }
      $('#table-lp tbody').html(res);
      Swal.close();
      initTableLP();
    }
  });

});

$(document).on('change', '.chk_lp', function(){

  let tr = $(this).closest('tr');
  let no_lp = tr.find('.no_lp').data('nolp');

  let total = parseFloat(tr.find('.total_lp').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_lp').data('ratelp')) || 0;
  let input = tr.find('.txt_amount_lp');
  let input_idr = tr.find('.txt_amount_lp_idr');
  let total_idr = total * rate;

  if($(this).is(':checked')){

    input.prop('disabled', false);
    input.val(total.toLocaleString('en-US'));
    input_idr.prop('disabled', false);
    input_idr.val(total_idr.toLocaleString('en-US'));

        hitungTotalLP();

  }else{

    input.prop('disabled', true);
    input.val('');

    input_idr.prop('disabled', true);
    input_idr.val('');

    if($('.chk_lp:checked').length === 0){

      // RESET HEADER
      $('#amount_kas4').val('');

      resetTotalLP();

    }else{
      hitungTotalLP();
    }

  }

});


$(document).on('change', '.chk_lp', function(){

  let tr = $(this).closest('tr');
  let no_lp = tr.find('.no_lp').data('nolp');

  let total = parseFloat(tr.find('.total_lp').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_lp').data('ratelp')) || 0;
  let input = tr.find('.txt_amount_lp');
  let input_idr = tr.find('.txt_amount_lp_idr');
  let total_idr = total * rate;

  if($(this).is(':checked')){

    input.prop('disabled', false);
    input.val(total.toLocaleString('en-US'));
    input_idr.prop('disabled', false);
    input_idr.val(total_idr.toLocaleString('en-US'));

        hitungTotalLP();

  }else{

    input.prop('disabled', true);
    input.val('');

    input_idr.prop('disabled', true);
    input_idr.val('');

    if($('.chk_lp:checked').length === 0){

      // RESET HEADER
      $('#amount_kas4').val('');

      resetTotalLP();

    }else{
      hitungTotalLP();
    }

  }

});

$(document).on('keyup', '.txt_amount_lp', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_lp').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_lp').data('ratelp')) || 0;

  let val = $(this).val().replace(/,/g,'');
  val = parseFloat(val) || 0;

  // 🔥 VALIDASI MAX
  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
  }

  // 🔥 FORMAT USD
  $(this).val(val.toLocaleString('en-US'));

  // 🔥 HITUNG IDR
  let val_idr = val * rate;

  tr.find('.txt_amount_lp_idr').val(val_idr.toLocaleString('en-US'));

  hitungTotalLP();

});


$(document).on('keyup', '.txt_amount_lp_idr', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_lp').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_lp').data('ratelp')) || 0;

  let val_idr = $(this).val().replace(/,/g,'');
  val_idr = parseFloat(val_idr) || 0;

  // 🔥 HITUNG USD
  let val = rate > 0 ? val_idr / rate : 0;

  // 🔥 VALIDASI MAX (pakai USD)
  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
    val_idr = val * rate;
  }

  // 🔥 FORMAT
  tr.find('.txt_amount_lp').val(val.toLocaleString('en-US'));
  $(this).val(val_idr.toLocaleString('en-US'));

  hitungTotalLP();

});



$('#amount_kas4').on('keyup change', function(){
  isManualAmount = true;
  hitungTotalLP();
});


// ===============================
// EVENT ADJUST TABLE
// ===============================
$(document).on('keyup change', 
  'input[name="txt_amount4[]"], input[name="txt_credit4[]"], .prof_ctr4', 
  function(){
    hitungTotalLP();
  });




$(document).on('change', '.prof_ctr4', function() {
  const selectedProfCtr = $(this).val();
  const row = $(this).closest('tr');
  const selectedCoa = row.find('select.no_coa4').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter4(selectedProfCtr, selectedCoa, row);
    });

$(document).on('change', '.no_coa4', function() {
  const selectedCoa = $(this).val();
  const row = $(this).closest('tr');
  const selectedProfCtr = row.find('select.prof_ctr4').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter4(selectedProfCtr, selectedCoa, row);
    });



    // Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
    function updateCostCenter4(profCtr, noCoa, row) {
      const costCtrDropdown = $(row).find('.cost_ctr4'); // dropdown cost center pada baris tsb

      // Kosongkan dropdown cost_ctr sebelum diisi
      costCtrDropdown.selectpicker('destroy'); // Hancurkan selectpicker lama
      costCtrDropdown.empty(); // Kosongkan semua opsi yang ada
      costCtrDropdown.append('<option value="-"> - </option>'); // Tambahkan opsi default
      costCtrDropdown.selectpicker(); // Inisialisasi ulang selectpicker

      if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
          url: 'getCostCenter.php', // Ganti dengan URL endpoint server Anda
          type: 'POST',
          data: {
            prof_ctr: profCtr,
            no_coa: noCoa
          }, // Kirim data prof_ctr ke server
          dataType: 'json',
          success: function(response) {
            if (response && response.length > 0) {
              $.each(response, function(index, costCtr) {
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
          error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
          }
        });
      } else {
        costCtrDropdown.selectpicker('refresh');
      }
    }


    function addRow4(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
      <tr>
      <td><input type="checkbox" id="select4" name="select4[]" value="" checked disabled></td>

      <td >
      <select class="form-control selectpicker no_coa4" name="nomor_coa4[]" data-live-search="true" data-size="5">
      <option value="-">-</option>
      <?php
      $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
      foreach ($sql as $coa) : ?>
        <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
      <?php endforeach; ?>
      </select>
      </td>

      <td>
      <select class="form-control selectpicker prof_ctr4" name="prof_ctr4[]" data-live-search="true">
      <option value="-"> - </option>
      <?php
      $sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
      foreach ($sql3 as $fc) : ?>
        <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
      <?php endforeach; ?>
      </select>
      </td>

      <td>
      <select class="form-control selectpicker cost_ctr4" name="cost_ctr4[]" data-live-search="true">
      <option value="-"> - </option>
      </select>
      </td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff4[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date4[]" autocomplete="off"></td>

      <td>
      <select class="form-control selectpicker curr_det4" name="currenc4[]">
      <option value="IDR">IDR</option>
      </select>
      </td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount4[]" autocomplete="off"></td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit4[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan4[]" autocomplete="off"></td>

      <td><input name="chk_a4[]" type="checkbox" class="checkall_a4"></td>

      </tr>

      `;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_kas4').val();

      if (headerPC) {

        $(row).find('.prof_ctr4').val(headerPC);
        $(row).find('.prof_ctr4').selectpicker('refresh');

      }

    }


    function deleteRow4(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var deleted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a4[]"]');

          if (chkbox && chkbox.checked) {

            if (rowCount <= 0) {

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


    function InsertRow4(tableID) {

      try {

        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var inserted = false;

        for (var i = rowCount - 1; i >= 0; i--) {

          var row = table.rows[i];
          var chkbox = row.querySelector('input[name="chk_a4[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
            <tr>
      <td><input type="checkbox" id="select4" name="select4[]" value="" checked disabled></td>

      <td >
      <select class="form-control selectpicker no_coa4" name="nomor_coa4[]" data-live-search="true" data-size="5">
      <option value="-">-</option>
      <?php
      $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
      foreach ($sql as $coa) : ?>
        <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
      <?php endforeach; ?>
      </select>
      </td>

      <td>
      <select class="form-control selectpicker prof_ctr4" name="prof_ctr4[]" data-live-search="true">
      <option value="-"> - </option>
      <?php
      $sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
      foreach ($sql3 as $fc) : ?>
        <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
      <?php endforeach; ?>
      </select>
      </td>

      <td>
      <select class="form-control selectpicker cost_ctr4" name="cost_ctr4[]" data-live-search="true">
      <option value="-"> - </option>
      </select>
      </td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff4[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date4[]" autocomplete="off"></td>

      <td>
      <select class="form-control selectpicker curr_det4" name="currenc4[]">
      <option value="IDR">IDR</option>
      </select>
      </td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount4[]" autocomplete="off"></td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit4[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan4[]" autocomplete="off"></td>

      <td><input name="chk_a4[]" type="checkbox" class="checkall_a4"></td>

      </tr>
            `;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_kas4').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr4').val(headerPC);
              $(newRow).find('.prof_ctr4').selectpicker('refresh');

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

    function resetTotalLP(){

  $('#tot_debit_nag_lp, #tot_debit_nak_lp, #tot_debit_lp').val('');
  $('#tot_credit_nag_lp, #tot_credit_nak_lp, #tot_credit_lp').val('');

}


function hitungTotalLP(){

  let total_lp = 0;

  let nag_debit = 0;
  let nak_debit = 0;

  let nag_credit = 0;
  let nak_credit = 0;

  let curr_h  = 'IDR';
  let rate_bank  = 1;
  let eqv_idr_bank1  = getNumber($('#amount_kas4').val());

  // =========================
  // 🔥 PV (DETAIL ATAS)
  // =========================
  $('.chk_lp:checked').each(function(){

    let tr = $(this).closest('tr');

    let val = getNumber(tr.find('.txt_amount_lp').val());
    let val_idr = getNumber(tr.find('.txt_amount_lp_idr').val());

    let pc = (tr.find('.pc_lp').data('pclp') || '').toString().trim().toUpperCase();

    console.log('PV ROW => PC:', pc, 'AMOUNT:', val, 'IDR:', val_idr);

    // 🔥 DISPLAY LOGIC
    if (curr_h == 'IDR') {
      total_lp += val_idr;
    }else{
      total_lp += val;
    }

    // 🔥 PERHITUNGAN WAJIB IDR
    if(pc === 'NAG'){
      nag_debit += val_idr;
    }else if(pc === 'NAK'){
      nak_debit += val_idr;
    }

  });

  // =========================
  // 🔥 HEADER AMOUNT (DISPLAY)
  // =========================
  let header_amount = 0;

  if(curr_h === 'IDR'){
    header_amount = total_lp * rate_bank;
  }else{
    header_amount = total_lp;
  }

  // 🔥 JANGAN OVERRIDE kalau user manual
  if(!isManualAmount){
    $('#amount_kas4').val(header_amount.toLocaleString('en-US'));
  }

  // =========================
  // 🔥 HEADER AMOUNT (KHUSUS HITUNG → IDR)
  // =========================
  let header_amount_idr = 0;

  if(isManualAmount){

    let manual_val = getNumber($('#amount_kas4').val());

    if(curr_h === 'IDR'){
      header_amount_idr = manual_val;
    }else{
      header_amount_idr = manual_val * rate_bank;
    }

  }else{

    header_amount_idr = total_lp * rate_bank;

  }

  // =========================
  // 🔥 HEADER (CREDIT → PC HEADER)
  // =========================
  let header_pc = ($('#profit_center_kas4').val() || '').trim().toUpperCase();

  console.log('HEADER PC:', header_pc, 'HEADER DISPLAY:', header_amount, 'HEADER IDR:', header_amount_idr, 'MANUAL:', isManualAmount);

  if(header_pc === 'NAG'){
    nag_credit += header_amount_idr;
  }else if(header_pc === 'NAK'){
    nak_credit += header_amount_idr;
  }

  // =========================
  // 🔥 ADJUST (TABLE BAWAH)
  // =========================
  $('#tbody4 tr').each(function(index){

    let tr = $(this);

    let pc = (tr.find('select.prof_ctr4').first().val() || '').trim().toUpperCase();

    let debit  = getNumber(tr.find('input[name="txt_amount4[]"]').val());
    let credit = getNumber(tr.find('input[name="txt_credit4[]"]').val());

    console.log('ADJUST ROW:', index+1, 'PC:', pc, 'Debit:', debit, 'Credit:', credit);

    if(pc === 'NAG'){
      nag_debit  += debit;
      nag_credit += credit;
    }else if(pc === 'NAK'){
      nak_debit  += debit;
      nak_credit += credit;
    }

  });

  // =========================
  // 🔥 GRAND TOTAL
  // =========================
  let grand_debit  = nag_debit + nak_debit;
  let grand_credit = nag_credit + nak_credit;

  console.log('TOTAL NAG:', nag_debit, nag_credit);
  console.log('TOTAL NAK:', nak_debit, nak_credit);
  console.log('GRAND:', grand_debit, grand_credit);

  // =========================
  // 🔥 UPDATE UI
  // =========================
  $('#tot_debit_nag_lp').val(nag_debit.toLocaleString('en-US'));
  $('#tot_debit_nak_lp').val(nak_debit.toLocaleString('en-US'));
  $('#tot_debit_lp').val(grand_debit.toLocaleString('en-US'));

  $('#tot_credit_nag_lp').val(nag_credit.toLocaleString('en-US'));
  $('#tot_credit_nak_lp').val(nak_credit.toLocaleString('en-US'));
  $('#tot_credit_lp').val(grand_credit.toLocaleString('en-US'));

  // =========================
  // 🔥 VALIDASI BALANCE
  // =========================
  if(grand_debit !== grand_credit){
    console.log('❌ TIDAK BALANCE');
    $('#btn_save_lp').prop('disabled', true);
  }else{
    console.log('✅ BALANCE');
    $('#btn_save_lp').prop('disabled', false);
  }


}


$('#simpan4').on('click', function(){

      console.log("===== MULAI SAVE PV =====");

  // =========================
  // HEADER
  // =========================
  let header = {
    ref        : $('#ref_num4').val(),
    tgl        : $('#tgl_active4').val(),
    supp       : $('#nama_supp4').val(),
    account    : $('#account4').val(),
    currency   : $('#currency4').val(),
    kode_kas   : $('#kode_kas4').val(),
    pc_header   : $('#profit_center_kas4').val(),
    amount     : getNumber($('#amount_kas4').val()),
    desc       : $('#pesan1').val()
  };

  console.log("HEADER:", header);

  // =========================
  // VALIDASI HEADER
  // =========================
  if(!header.tgl){
    Swal.fire('Warning','Tanggal wajib diisi','warning');
    return;
  }

  if(!header.supp){
    Swal.fire('Warning','Supplier wajib diisi','warning');
    return;
  }

  if(!header.account){
    Swal.fire('Warning','Account belum terisi','warning');
    return;
  }

  if(header.amount <= 0){
    Swal.fire('Warning','Amount tidak boleh 0','warning');
    return;
  }

  if($('.chk_lp:checked').length === 0){
    Swal.fire('Warning','Pilih minimal 1 LP','warning');
    return;
  }

  // =========================
  // TOTAL GLOBAL
  // =========================
  let total_debit  = getNumber($('#tot_debit_lp').val());
  let total_credit = getNumber($('#tot_credit_lp').val());

  console.log("TOTAL GLOBAL =>", total_debit, total_credit);

  if(total_debit !== total_credit){
    Swal.fire('Error','Total Debit & Credit tidak balance','error');
    return;
  }

  // =========================
  // TOTAL PER PC
  // =========================
  let nag_debit  = getNumber($('#tot_debit_nag_lp').val());
  let nag_credit = getNumber($('#tot_credit_nag_lp').val());

  let nak_debit  = getNumber($('#tot_debit_nak_lp').val());
  let nak_credit = getNumber($('#tot_credit_nak_lp').val());

  console.log("NAG:", nag_debit, nag_credit);
  console.log("NAK:", nak_debit, nak_credit);

  if(nag_debit !== nag_credit){
    Swal.fire('Error','NAG tidak balance','error');
    return;
  }

  if(nak_debit !== nak_credit){
    Swal.fire('Error','NAK tidak balance','error');
    return;
  }

  // =========================
  // DETAIL PV
  // =========================
  let detail_lp = [];

  $('.chk_lp:checked').each(function(){

    let tr = $(this).closest('tr');

    let data = {
      no_lp  : tr.find('.no_lp').data('nolp'),
      amount : getNumber(tr.find('.txt_amount_lp').val()),
      pc     : tr.find('.pc_lp').data('pclp')
    };

    console.log("PV:", data);

    detail_lp.push(data);

  });

  // =========================
  // DETAIL ADJUST
  // =========================
  let detail_adjust = [];
  let valid_adjust = true;

  $('#tbody4 tr').each(function(index){

    let tr = $(this);

    let coa   = tr.find('select.no_coa4').first().val();
    let pc    = tr.find('select.prof_ctr4').first().val();
    let cc    = tr.find('select.cost_ctr4').first().val();
    let debit = parseFloat(tr.find('input[name="txt_amount4[]"]').val()) || 0;
    let credit= parseFloat(tr.find('input[name="txt_credit4[]"]').val()) || 0;
    let desc  = tr.find('input[name="keterangan4[]"]').val();
    let curr  = tr.find('select.currenc4').first().val();
    let reff_doc  = tr.find('input[name="no_reff4[]"]').val();
    let reff_date  = tr.find('input[name="reff_date4[]"]').val();

    let rowData = {
      row   : index + 1,
      coa,
      pc,
      cc,
      debit,
      credit,
      desc,
      curr,
      reff_doc,
      reff_date
    };

    console.log("ADJUST:", rowData);

    // VALIDASI
    if(!coa || coa === '-'){
      Swal.fire('Warning','COA wajib diisi','warning');
      tr.find('.no_coa4').focus();
      valid_adjust = false;
      return false;
    }

    if(!pc || pc === '-'){
      Swal.fire('Warning','Profit Center wajib diisi','warning');
      tr.find('.prof_ctr4').focus();
      valid_adjust = false;
      return false;
    }

    if(coaWajibCC.includes(coa)){
      if(!cc || cc === '-' || cc === ''){
        Swal.fire('Warning','COA wajib isi Cost Center','warning');
        tr.find('.cost_ctr4').focus();
        valid_adjust = false;
        return false;
      }
    }

    if(debit === 0 && credit === 0){
      Swal.fire('Warning','Debit/Credit harus diisi','warning');
      valid_adjust = false;
      return false;
    }

    detail_adjust.push(rowData);

  });

  if(!valid_adjust) return;

  console.log("DETAIL LP:", detail_lp);
  console.log("DETAIL ADJUST:", detail_adjust);

  // =========================
  // FINAL DATA
  // =========================
  let finalData = {
    header,
    detail_lp,
    detail_adjust,
    total: {
      global_debit  : total_debit,
      global_credit : total_credit,
      nag_debit,
      nag_credit,
      nak_debit,
      nak_credit
    }
  };

  console.log("FINAL DATA:", finalData);

  // =========================
  // AJAX SAVE
  // =========================
  Swal.fire({
    title: 'Saving...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  $.ajax({
    url: 'save_lp_cash.php',
    type: 'POST',
    dataType: 'json',
    data: {
      data: JSON.stringify(finalData)
    },
    success: function(res){

      console.log("RESPONSE:", res);

      if(res.status === 'ok'){
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: res.message
        }).then(() => {
          location.href='petty-cashout.php';
        });
      }else{
        Swal.fire('Error', res.message, 'error');
      }

    },
    error: function(xhr){

      console.log("ERROR AJAX:", xhr.responseText);

      Swal.fire('Error','Terjadi kesalahan server','error');

    }
  });

});


  </script>




  </body>

  </html>