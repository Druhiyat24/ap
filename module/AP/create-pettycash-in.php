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

    #mytablesettle .form-control {
    width: 100% !important;
  }

  #mytablesettle .bootstrap-select {
    width: 100% !important;
  }

  .total-box{
    border:0;
    border-radius:10px;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    height:100%;
    padding:0;
  }

  .total-box .total-box-header{
    padding:12px 16px;
    color:#fff;
    font-weight:700;
    font-size:14px;
  }

  .total-box.tone-nag .total-box-header{
    background:linear-gradient(90deg, #5b7ba8, #7fa0c9);
  }

  .total-box.tone-nak .total-box-header{
    background:linear-gradient(90deg, #4f8a6b, #74ad8f);
  }

  .total-box.tone-all .total-box-header{
    background:linear-gradient(90deg, #4a5578, #6b7699);
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
    color:#212529;
  }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
      style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM PETTY CASH IN</h5>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-1">
      <div class="card-body p-4">

        <div class="tab-container">
          <button class="tablinks active" onclick="openTab(event, 'pettyin_none')">None</button>
          <button class="tablinks" onclick="openTab(event, 'pettyin_settle')">Settlement</button>
          <button class="tablinks" onclick="openTab(event, 'pettyin_cashout')">Cash Out</button>
        </div>


        <div id="pettyin_cashout" class="tabcontent">
          <?php include 'petty-in/pettyin_cashout.php'; ?>
        </div>

        <div id="pettyin_settle" class="tabcontent">
          <?php include 'petty-in/pettyin_settle.php'; ?>
        </div>

        <div id="pettyin_none" class="tabcontent">
          <?php include 'petty-in/pettyin_none.php'; ?>
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

      $('#account').on('change', function() {

        let bank = $(this).find(':selected').data('bank');
        let currency = $(this).find(':selected').data('currency');
        let namapc = $(this).find(':selected').data('namapc');
        let kodepc = $(this).find(':selected').data('kodepc');
        let kodebank = $(this).find(':selected').data('kodebank');

        $('#bank').val(bank);
        $('#kode_bank').val(kodebank);
        $('#currency').val(currency);
        $('#profit_center_bank').val(kodepc);

        if (currency === 'IDR') {

          $('#rate_bank').val('1');
          formatNumber(document.getElementById('rate_bank'));

          $('#rate_bank').prop('readonly', true);

          // langsung hitung
          hitungEqv();

        } else {

          $('#rate_bank').prop('readonly', false);
          getRate(); // nanti setelah ajax selesai juga akan hitung

        }

      });



      $(document).on('keyup', '#amount_bank,#rate_bank', function() {

        formatNumber(this);
        hitungEqv();

      });

    });

    function getNumber(val) {
      return parseFloat(val.replace(/,/g, '')) || 0;
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

    function refreshRate() {
      let currency = $('#currency').val();
      getRate(currency);
    }

    function getRate() {

      let valuta = $('#currency').val();
      let doc_date = $('#tgl_active').val();

      if (!valuta || !doc_date) return;

      $.ajax({
        url: 'get_rate.php',
        type: 'POST',
        dataType: 'json',
        data: {
          valuta: valuta,
          doc_date: doc_date
        },
        success: function(res) {

          if (res.status === 'ok') {
            $('#rate_bank').val(res.rate);
          } else {
            $('#rate_bank').val('1');
          }

          formatNumber(document.getElementById('rate_bank'));

          // TAMBAHKAN INI
          hitungEqv();

        }
      });

    }


    function hitungEqv() {

      let amount = getNumber($('#amount_bank').val());
      let rate = getNumber($('#rate_bank').val());

      let total = amount * rate;

      $('#eqv_idr_bank').val(total);

      formatNumber(document.getElementById('eqv_idr_bank'));

    }

    $('#simpan').on('click', function() {

      let account = $('#account').val();
      let coa = $('#coa').val();
      let amount = $('#amount_bank').val();
      let rate = $('#rate_bank').val().replace(/,/g, '');
      let currency = $('#currency').val();

      // VALIDASI
      if (account == '') {
        Swal.fire('Warning', 'Account tidak boleh kosong', 'warning');
        $('#account').focus();
        return;
      }

      if (coa == '') {
        Swal.fire('Warning', 'COA tidak boleh kosong', 'warning');
        $('#coa').focus();
        return;
      }

      if (amount == '' || amount == '0') {
        Swal.fire('Warning', 'Amount tidak boleh kosong', 'warning');
        $('#amount_bank').focus();
        return;
      }

      if (currency != 'IDR' && rate == 1) {
        Swal.fire('Warning', 'Currency non IDR harus memiliki rate selain 1', 'warning');
        $('#rate_bank').focus();
        return;
      }

      let btn = $(this);
      btn.prop('disabled', true);

      $.ajax({
        url: 'bank_in/save_bankin_arcollection.php',
        type: 'POST',
        data: $('#form-data').serialize(),
        dataType: 'json',
        success: function(res) {

          if (res.status == 'ok') {

            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Document : ' + res.doc_num
            }).then(() => {
              location.href='petty-cashin.php';
            });

          } else {

            Swal.fire('Error', res.msg, 'error');
            btn.prop('disabled', false);

          }

        },
        error: function() {
          Swal.fire('Error', 'Server error', 'error');
          btn.prop('disabled', false);
        }
      });

    });



//ke 2......................................................................................

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


    function InsertRow2(tableID) {

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
debit_nag += eqv;
}

if(header_pc === 'NAK'){
debit_nak += eqv;
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


let coaWajibCC = [];

// Load sekali saat halaman dibuka
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
});

$('#simpan2').on('click', function () {

    console.log("===== MULAI SAVE =====");

    let account = $('#account2').val();

    console.log("Account :", account);


    if(account == ''){
        Swal.fire('Warning','Account tidak boleh kosong','warning');
        return;
    }

    if(!$('#pesan2').val().trim()){
        Swal.fire('Warning','Description tidak boleh kosong','warning');
        return;
    }

    if(!$('#cash_flow2').val()){
        Swal.fire('Warning','Cash Flow Category tidak boleh kosong','warning');
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

        // Kalau description baris kosong, pakai description header
        let ketInput2 = tr.find('input[name="keterangan2[]"]');
        if(!ketInput2.val()){
          ketInput2.val($('#pesan2').val());
        }

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

        let totalDebit  = header_debit + debitNAG;
        let totalCredit = creditNAG;

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

        let totalDebit  = header_debit + debitNAK;
        let totalCredit = creditNAK;

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

    function doSavePettyinSettle2(){

      $('#simpan2').prop('disabled', true);

      $.ajax({

          url: "petty-in/save_pettyin_settle.php",
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
                      location.href='petty-cashin.php';
                  });

              }else{

                  console.log("SAVE ERROR :", r.message);

                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: r.message,
                    showCancelButton: true,
                    confirmButtonText: 'Coba Lagi',
                    cancelButtonText: 'Tutup'
                  }).then((retry) => {
                    if(retry.isConfirmed){
                      doSavePettyinSettle2();
                    }else{
                      $('#simpan2').prop('disabled', false);
                    }
                  });

              }

          },

          error:function(xhr){

              console.log("AJAX ERROR");
              console.log(xhr.responseText);

              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Server Error',
                showCancelButton: true,
                confirmButtonText: 'Coba Lagi',
                cancelButtonText: 'Tutup'
              }).then((retry) => {
                if(retry.isConfirmed){
                  doSavePettyinSettle2();
                }else{
                  $('#simpan2').prop('disabled', false);
                }
              });
          }

      });

    }

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){
            console.log("PROSES AJAX SAVE");
            doSavePettyinSettle2();
        }

    });

});



//ke 1 (Cash Out)...........................................................................

 function getdataadvance1(val){
    var no_co = val;

    $.ajax({
        type:'POST',
        url:'petty-in/get_data_cashout.php',
        data: {'no_co':no_co},
        cache: 'false',
        close: function(e){
            e.preventDefault();
            return false;
        },
        success: function(data){

    $('#tbody1').html(data); // langsung ganti isi

    $('.selectpicker').selectpicker('refresh');

    hitung_total1();
}
,
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr);
            alert(xhr);
        }
    });
}

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

    $(document).on('change', '.prof_ctr1', function() {
      const selectedProfCtr = $(this).val();
      const row = $(this).closest('tr');
      const selectedCoa = row.find('select.no_coa1').val() || '-';
      updateCostCenter1(selectedProfCtr, selectedCoa, row);
    });

    $(document).on('change', '.no_coa1', function() {
      const selectedCoa = $(this).val();
      const row = $(this).closest('tr');
      const selectedProfCtr = row.find('select.prof_ctr1').val() || '-';
      updateCostCenter1(selectedProfCtr, selectedCoa, row);
    });

    // Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
    function updateCostCenter1(profCtr, noCoa, row) {
      const costCtrDropdown = $(row).find('.cost_ctr1');

      costCtrDropdown.selectpicker('destroy');
      costCtrDropdown.empty();
      costCtrDropdown.append('<option value="-"> - </option>');
      costCtrDropdown.selectpicker();

      if (profCtr && profCtr !== '-') {

        $.ajax({
          url: 'getCostCenter.php',
          type: 'POST',
          data: {
            prof_ctr: profCtr,
            no_coa: noCoa
          },
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

    function addRow1(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
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

      $('.selectpicker').selectpicker('refresh');

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

            var element1 = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
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
            newRow.innerHTML = element1;

            inserted = true;

            var headerPC = $('#profit_center_kas1').val();
            if (headerPC) {

              $(newRow).find('.prof_ctr1').val(headerPC);
              $(newRow).find('.prof_ctr1').selectpicker('refresh');

            }

          }

        }

        if (!inserted) {

          Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Silahkan ceklis baris yang ingin disisipkan'
          });

        }

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

let debit_nag = 0;
let credit_nag = 0;
let debit_nak = 0;
let credit_nak = 0;

let rate = 1;

$('#tbody1 tr').each(function(index){

let pc = $(this).find('select.prof_ctr1').val();
let curr = $(this).find('select[name="currenc1[]"]').val();

let debitVal  = $(this).find('input[name="txt_amount1[]"]').val();
let creditVal = $(this).find('input[name="txt_credit1[]"]').val();

let debit  = parseFloat(debitVal)  || 0;
let credit = parseFloat(creditVal) || 0;

if(curr === 'USD'){

debit  = debit  * rate;
credit = credit * rate;

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

let header_pc = $('#profit_center_kas1').val();

let amount = parseFloat($('#amount_kas1').val().replace(/,/g,'')) || 0;

let eqv = amount * rate;

if(header_pc === 'NAG'){
debit_nag += eqv;
}

if(header_pc === 'NAK'){
debit_nak += eqv;
}

let grand_debit  = debit_nag + debit_nak;
let grand_credit = credit_nag + credit_nak;

$('#tot_debit_nag1').val(formatAngka(debit_nag));
$('#tot_credit_nag1').val(formatAngka(credit_nag));

$('#tot_debit_nak1').val(formatAngka(debit_nak));
$('#tot_credit_nak1').val(formatAngka(credit_nak));

$('#tot_debit1').val(formatAngka(grand_debit));
$('#tot_credit1').val(formatAngka(grand_credit));

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

    let account = $('#account1').val();

    if(account == ''){
        Swal.fire('Warning','Account tidak boleh kosong','warning');
        return;
    }

    if(!$('#reff_number1').val()){
        Swal.fire('Warning','Reff Document tidak boleh kosong','warning');
        return;
    }

    if(!$('#pesan1').val().trim()){
        Swal.fire('Warning','Description tidak boleh kosong','warning');
        return;
    }

    if(!$('#cash_flow1').val()){
        Swal.fire('Warning','Cash Flow Category tidak boleh kosong','warning');
        return;
    }

    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let error = false;

    $("#tbody1 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('select.no_coa1').first().val();
        let pc  = tr.find('select.prof_ctr1').first().val();
        let cc  = tr.find('select.cost_ctr1').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount1[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit1[]"]').val()) || 0;

        let ketInput1 = tr.find('input[name="keterangan1[]"]');
        if(!ketInput1.val()){
          ketInput1.val($('#pesan1').val());
        }

        if(coaWajibCC.includes(coa)){

            if(cc == '-' || cc == '' || cc == null){

                Swal.fire(
                    'Warning',
                    'COA '+coa+' wajib isi Cost Center',
                    'warning'
                );

                error = true;
                return false;
            }
        }

        if(pc == 'NAG'){
            debitNAG  += debit;
            creditNAG += credit;
        }

        if(pc == 'NAK'){
            debitNAK  += debit;
            creditNAK += credit;
        }

    });

    if(error) return;

    let header_pc = $('#profit_center_kas1').val();

    let header_debit = parseFloat(
        $('#amount_kas1').val().replace(/,/g,'')
    ) || 0;

    if(header_pc == 'NAG'){

        let totalDebit  = header_debit + debitNAG;
        let totalCredit = creditNAG;

        if(totalDebit != totalCredit){

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Garment tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }

    if(header_pc == 'NAK'){

        let totalDebit  = header_debit + debitNAK;
        let totalCredit = creditNAK;

        if(totalDebit != totalCredit){

            Swal.fire(
                'Warning',
                'Journal Nirwana Alabare Knitting tidak balance (Header + Detail)',
                'warning'
            );

            return;
        }

    }

    function doSavePettyinCashout1(){

      $('#simpan1').prop('disabled', true);

      $.ajax({

          url: "petty-in/save_pettyin_cashout.php",
          type: "POST",
          data: $('#form-data1').serialize(),

          beforeSend:function(){

              Swal.fire({
                  title: 'Saving...',
                  allowOutsideClick:false,
                  didOpen:()=>{
                      Swal.showLoading();
                  }
              });

          },

          success:function(res){

              let r = JSON.parse(res);

              if(r.status == 'success'){

                  Swal.fire({
                      icon:'success',
                      title:'Success',
                      text:r.message
                  }).then(()=>{
                      location.href='petty-cashin.php';
                  });

              }else{

                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: r.message,
                    showCancelButton: true,
                    confirmButtonText: 'Coba Lagi',
                    cancelButtonText: 'Tutup'
                  }).then((retry) => {
                    if(retry.isConfirmed){
                      doSavePettyinCashout1();
                    }else{
                      $('#simpan1').prop('disabled', false);
                    }
                  });

              }

          },

          error:function(xhr){

              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Server Error',
                showCancelButton: true,
                confirmButtonText: 'Coba Lagi',
                cancelButtonText: 'Tutup'
              }).then((retry) => {
                if(retry.isConfirmed){
                  doSavePettyinCashout1();
                }else{
                  $('#simpan1').prop('disabled', false);
                }
              });
          }

      });

    }

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){
            doSavePettyinCashout1();
        }

    });

});


//ke 3.....................................................................................


    $('#account3').on('change', function() {

      let kode3 = $(this).find(':selected').data('kode3');
      let pc3 = $(this).find(':selected').data('pc3');
      let namapc3 = $(this).find(':selected').data('namapc3');

      $('#profit_center_kas_show3').val(namapc3);
      $('#profit_center_kas3').val(pc3);
      $('#currency3').val('IDR');
      $('#kode_kas3').val(kode3);

      hitung_total();

    });


    function UbahCostArc(val) {
      var profit = val; // Ambil nilai parameter
      // alert(profit); // Debug: pastikan nilai 'profit' diterima

      // Pastikan selektor dropdown sesuai dengan nama elemen
      const costCtrDropdown = $('select[name="cost"]');
      const no_coa = $('select[name=coa] option').filter(':selected').val();
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

    $(document).on('change', '.prof_ctr', function() {
      const selectedProfCtr = $(this).val();
      const row = $(this).closest('tr');
      const selectedCoa = row.find('select.no_coa').val() || '-';
      // console.log("row:", row.html());
      // console.log("no_coa element:", row.find('.no_coa'));
      // console.log("selectedCoa:", selectedCoa);
      updateCostCenter(selectedProfCtr, selectedCoa, row);
    });

    $(document).on('change', '.no_coa', function() {
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
      const costCtrDropdown = $(row).find('.cost_ctr'); // dropdown cost center pada baris tsb

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
    function addRow(tableID) {

      var table = document.getElementById(tableID);
      var rowCount = table.rows.length;
      var row = table.insertRow(rowCount);

      var element = `
<tr>
<td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa" name="nomor_coa[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr" name="cost_ctr[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det" name="currenc[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount[]" oninput="modal_input_amt(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit[]" oninput="modal_input_cre(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan[]" autocomplete="off"></td>

<td><input name="chk_a[]" type="checkbox" class="checkall_a"></td>

</tr>

`;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_kas3').val();

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
          var chkbox = row.querySelector('input[name="chk_a[]"]');

          if (chkbox && chkbox.checked) {

            var element2 = `
<tr>
<td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa" name="nomor_coa[]" data-live-search="true" data-width="220px" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr" name="cost_ctr[]" data-live-search="true" data-width="200px">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det" name="currenc[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount[]" oninput="modal_input_amt(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit[]" oninput="modal_input_cre(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan[]" autocomplete="off"></td>

<td><input name="chk_a[]" type="checkbox" class="checkall_a"></td>

</tr>
`;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_kas3').val();
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
    return new Intl.NumberFormat('en-US').format(x);
}


    function modal_input_amt(el){

let row = $(el).closest('tr');

let debit  = parseFloat($(el).val()) || 0;
let creditInput = row.find('input[name="txt_credit[]"]');

if(debit > 0){

creditInput.val(0);
creditInput.prop('readonly',true);

}else{

creditInput.prop('readonly',false);

}

hitung_total();

}


function modal_input_cre(el){

let row = $(el).closest('tr');

let credit = parseFloat($(el).val()) || 0;
let debitInput = row.find('input[name="txt_amount[]"]');

if(credit > 0){

debitInput.val(0);
debitInput.prop('readonly',true);

}else{

debitInput.prop('readonly',false);

}

hitung_total();

}


function hitung_total(){
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

let pc = $(this).find('select.prof_ctr').val();
let curr = $(this).find('select[name="currenc[]"]').val();

let debitVal  = $(this).find('input[name="txt_amount[]"]').val();
let creditVal = $(this).find('input[name="txt_credit[]"]').val();

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
debit_nag += eqv;
}

if(header_pc === 'NAK'){
debit_nak += eqv;
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

$('#tot_debit_nag').val(formatAngka(debit_nag));
$('#tot_credit_nag').val(formatAngka(credit_nag));

$('#tot_debit_nak').val(formatAngka(debit_nak));
$('#tot_credit_nak').val(formatAngka(credit_nak));

$('#tot_debit').val(formatAngka(grand_debit));
$('#tot_credit').val(formatAngka(grand_credit));


/* hidden */

$('#h_tot_debit_nag').val(debit_nag);
$('#h_tot_credit_nag').val(credit_nag);

$('#h_tot_debit_nak').val(debit_nak);
$('#h_tot_credit_nak').val(credit_nak);

$('#h_tot_debit').val(grand_debit);
$('#h_tot_credit').val(grand_credit);

}




$(document).on('change','.prof_ctr,.curr_det',function(){

hitung_total();

});

$('#amount_kas3').on('keyup change', function(){

hitung_total();

});



$('#simpan3').on('click', function () {

    console.log("===== MULAI SAVE =====");

    let account = $('#account3').val();

    console.log("Account :", account);


    if(account == ''){
        Swal.fire('Warning','Account tidak boleh kosong','warning');
        return;
    }

    if(!$('#pesan3').val().trim()){
        Swal.fire('Warning','Description tidak boleh kosong','warning');
        return;
    }

    if(!$('#cash_flow3').val()){
        Swal.fire('Warning','Cash Flow Category tidak boleh kosong','warning');
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

        let coa = tr.find('select.no_coa').first().val();
        let pc  = tr.find('select.prof_ctr').first().val();
        let cc  = tr.find('select.cost_ctr').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit[]"]').val()) || 0;

        // Kalau description baris kosong, pakai description header
        let ketInput3 = tr.find('input[name="keterangan[]"]');
        if(!ketInput3.val()){
          ketInput3.val($('#pesan3').val());
        }

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
   VALIDASI PER PC
====================== */

if(header_pc == 'NAG'){

    if(header_debit != creditNAG){

        console.log("ERROR: NAG tidak balance");

        Swal.fire(
            'Warning',
            'Journal Nirwana Alabare Garment tidak balance',
            'warning'
        );

        return;
    }

}

if(header_pc == 'NAK'){

    if(header_debit != creditNAK){

        console.log("ERROR: NAK tidak balance");

        Swal.fire(
            'Warning',
            'Journal Nirwana Alabare Knitting tidak balance',
            'warning'
        );

        return;
    }

}



    console.log("VALIDASI LOLOS");

    console.log($('#form-data3').serialize());

    function doSavePettyinNone3(){

      $('#simpan3').prop('disabled', true);

      $.ajax({

          url: "petty-in/save_pettyin_none.php",
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
                      location.href='petty-cashin.php';
                  });

              }else{

                  console.log("SAVE ERROR :", r.message);

                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: r.message,
                    showCancelButton: true,
                    confirmButtonText: 'Coba Lagi',
                    cancelButtonText: 'Tutup'
                  }).then((retry) => {
                    if(retry.isConfirmed){
                      doSavePettyinNone3();
                    }else{
                      $('#simpan3').prop('disabled', false);
                    }
                  });

              }

          },

          error:function(xhr){

              console.log("AJAX ERROR");
              console.log(xhr.responseText);

              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Server Error',
                showCancelButton: true,
                confirmButtonText: 'Coba Lagi',
                cancelButtonText: 'Tutup'
              }).then((retry) => {
                if(retry.isConfirmed){
                  doSavePettyinNone3();
                }else{
                  $('#simpan3').prop('disabled', false);
                }
              });
          }

      });

    }

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){
            console.log("PROSES AJAX SAVE");
            doSavePettyinNone3();
        }

    });

});



  </script>




  </body>

  </html>