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
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM OUTGOING BANK</h5>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-1">
      <div class="card-body p-4">

        <div class="tab-container">
          <button class="tablinks" onclick="openTab(event, 'inbank_ar_collection')">List Payment</button>
          <button class="tablinks active" onclick="openTab(event, 'outbank-pv')">Payment Voucher</button>
          <button class="tablinks" onclick="openTab(event, 'outbank-none')">None</button>
        </div>


        <div id="inbank_ar_collection" class="tabcontent">
          <?php include 'bank_in/inbank_ar_collection.php'; ?>
        </div>

        <div id="outbank-pv" class="tabcontent">
          <?php include 'bank-out/outbank-pv.php'; ?>
        </div>

        <div id="outbank-none" class="tabcontent">
          <?php include 'bank-out/outbank-none.php'; ?>
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
              location.href='bank-in.php';
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



    let datatable;

    $(document).ready(function() {

      $('#no_bk').prop('disabled', true);

      datatable = $("#table-bank-out").DataTable({

        ordering: false,
        processing: true,
        serverSide: false,
        searching: true,
        info: true,
        autoWidth: false,

        scrollX: true,
        scrollY: "350px",
        scrollCollapse: true,
        paging: false,

        ajax: {
          url: 'bank_in/ajx_get_data_bank_out.php',
          type: 'POST',
          data: function(d) {
            d.no_bk = $('#no_bk').val();
          }
        },

        columns: [{
            data: 'nama_pc'
          },
          {
            data: 'nama_coa'
          },
          {
            data: 'cc_name'
          },
          {
            data: 'no_journal'
          },
          {
            data: 'tgl_journal'
          },
          {
            data: 'keterangan'
          },
          {
            data: 'debit'
          },
          {
            data: 'rate'
          },
          {
            data: 'debit_idr'
          }
        ],

        columnDefs: [{
          targets: [6, 7, 8],
          className: "text-right",
          render: function(data) {

            let val = parseFloat(data || 0);

            return val.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });

          }
        }],

        initComplete: function() {

          let api = this.api();

          // paksa hitung ulang width
          setTimeout(function() {

            api.columns.adjust().draw();

            $(".dataTables_scrollHeadInner").css("width", "100%");
            $(".dataTables_scrollHeadInner table").css("width", "100%");

          }, 200);

        }

      });


      $('#no_bk').on('change', function() {
        dataTableReload();
        loadAmount();
      });

    });


    function dataTableReload() {

      datatable.ajax.reload(function() {

        datatable.columns.adjust().draw();

      }, false);

    }

    //KE 2

    $('#btn_tarik_pv').on('click', function(){

    let tgl_awal  = $('#tgl_filawal').val();
    let tgl_akhir = $('#tgl_filakhir').val();
    let supplier  = $('#nama_supp2').val();

    if(tgl_awal == '' || tgl_akhir == ''){
        Swal.fire('Warning','Tanggal harus diisi','warning');
        return;
    }

    if(supplier == ''){
        Swal.fire('Warning','Supplier harus dipilih','warning');
        return;
    }

    $.ajax({
        url: 'bank-out/get_pv_ajax.php',
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
            $('#table-pv tbody').html(res);
            Swal.close();
        }
    });

});

    $(document).on('change', '.chk_pv', function(){

    let tr = $(this).closest('tr');

    let total = parseFloat(tr.find('.total_pv').data('total')) || 0;

    let input = tr.find('.txt_amount_pv');

    if($(this).is(':checked')){

        input.prop('disabled', false);
        input.val(total.toLocaleString('en-US'));

        // =========================
        // AUTO ISI HEADER (AMBIL DARI PV)
        // =========================
        $('#amount_bank2').val(total.toLocaleString('en-US'));

    }else{
        input.prop('disabled', true);
        input.val('');
    }

    hitungTotalPV();

});



    function hitungEqv2() {

      let amount2 = getNumber($('#amount_bank2').val());
      let rate2 = getNumber($('#rate_bank2').val());

      let total2 = amount2 * rate2;

      $('#eqv_idr_bank2').val(total2);

      formatNumber(document.getElementById('eqv_idr_bank2'));

    }


    function refreshRate2() {
      let currency2 = $('#currency2').val();
      getRate2(currency2);
    }

    function getRate2() {

      let valuta2 = $('#currency2').val();
      let doc_date2 = $('#tgl_active2').val();

      if (!valuta2 || !doc_date2) return;

      $.ajax({
        url: 'get_rate.php',
        type: 'POST',
        dataType: 'json',
        data: {
          valuta: valuta2,
          doc_date: doc_date2
        },
        success: function(res) {

          if (res.status === 'ok') {
            $('#rate_bank2').val(res.rate);
          } else {
            $('#rate_bank2').val('1');
          }

          formatNumber(document.getElementById('rate_bank2'));

          // TAMBAHKAN INI
          hitungEqv2();

        }
      });

    }

    // KE 3

    function getRate3() {

      let valuta3 = $('#currency3').val();
      let doc_date3 = $('#tgl_active3').val();

      if (!valuta3 || !doc_date3) return;

      $.ajax({
        url: 'get_rate.php',
        type: 'POST',
        dataType: 'json',
        data: {
          valuta: valuta3,
          doc_date: doc_date3
        },
        success: function(res) {

          if (res.status === 'ok') {
            $('#rate_bank3').val(res.rate);
          } else {
            $('#rate_bank3').val('1');
          }

          formatNumber(document.getElementById('rate_bank3'));

          // TAMBAHKAN INI
          hitung_total();

        }
      });

    }


    $('#account3').on('change', function() {

      let bank3 = $(this).find(':selected').data('bank3');
      let currency3 = $(this).find(':selected').data('currency3');
      let namapc3 = $(this).find(':selected').data('namapc3');
      let kodepc3 = $(this).find(':selected').data('kodepc3');
      let kodebank3 = $(this).find(':selected').data('kodebank3');

      $('#bank3').val(bank3);
      $('#kode_bank3').val(kodebank3);
      $('#currency3').val(currency3);
      $('#profit_center_bank3').val(kodepc3);


      if (currency3 === 'IDR') {

        $('#rate_bank3').val('1');
        formatNumber(document.getElementById('rate_bank3'));

        $('#rate_bank3').prop('readonly', true);

        // langsung hitung
        // hitungEqv2();

      } else {

        $('#rate_bank3').prop('readonly', false);
        getRate3(); // nanti setelah ajax selesai juga akan hitung

      }

      hitung_total()

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
<option value="USD">USD</option>
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

      var headerPC = $('#profit_center_bank3').val();

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
<option value="USD">USD</option>
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
            var headerPC = $('#profit_center_bank3').val();
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

let debit_nag = 0;
let credit_nag = 0;

let debit_nak = 0;
let credit_nak = 0;

console.log("===== MULAI HITUNG TOTAL =====");

/* ======================
   RATE HEADER
====================== */

let rate = parseFloat($('#rate_bank3').val().replace(/,/g,'')) || 0;

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

let header_pc = $('#profit_center_bank3').val();

let amount = parseFloat($('#amount_bank3').val().replace(/,/g,'')) || 0;

let eqv = amount * rate;

console.log("===== HEADER DATA =====");
console.log("Header PC :", header_pc);
console.log("Amount :", amount);
console.log("Equivalent :", eqv);


/* tampilkan eqv */

$('#eqv_idr_bank3').val(formatAngka(eqv));


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

$('#amount_bank3, #rate_bank3').on('keyup change', function(){

hitung_total();

});


let coaWajibCC = [];

// Load sekali saat halaman dibuka
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
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

    // TETAP ADA (tapi tidak dipakai hitung)
    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let error = false;

    console.log("===== LOOP DETAIL =====");

    $("#tbody3 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('.no_coa').val();
        let pc  = tr.find('.prof_ctr').val();
        let cc  = tr.find('.cost_ctr').val();

        let debit  = parseFloat(tr.find('[name="txt_amount[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit[]"]').val()) || 0;

        /* VALIDASI COST CENTER */
        if(coaWajibCC.includes(coa)){
            if(cc == '-' || cc == '' || cc == null){
                Swal.fire('Warning','COA '+coa+' wajib isi Cost Center','warning');
                error = true;
                return false;
            }
        }

        // ❌ HITUNG PER PC DIHAPUS (tidak dipakai lagi)

    });

    if(error) return;

    /* ======================
       AMBIL TOTAL DARI FORM
    ====================== */

    debitNAG  = parseFloat($('#h_tot_debit_nag').val())  || 0;
    creditNAG = parseFloat($('#h_tot_credit_nag').val()) || 0;

    debitNAK  = parseFloat($('#h_tot_debit_nak').val())  || 0;
    creditNAK = parseFloat($('#h_tot_credit_nak').val()) || 0;

    console.log("===== TOTAL DARI FORM =====");
    console.log("NAG Debit :", debitNAG);
    console.log("NAG Credit :", creditNAG);
    console.log("NAK Debit :", debitNAK);
    console.log("NAK Credit :", creditNAK);

    /* ======================
       PEMBULATAN (ANTI SELISIH 0.01)
    ====================== */

    debitNAG  = Math.round(debitNAG);
    creditNAG = Math.round(creditNAG);

    debitNAK  = Math.round(debitNAK);
    creditNAK = Math.round(creditNAK);

    console.log("===== HASIL AKHIR =====");
    console.log("NAG Debit :", debitNAG);
    console.log("NAG Credit :", creditNAG);
    console.log("NAK Debit :", debitNAK);
    console.log("NAK Credit :", creditNAK);

    /* ======================
       VALIDASI PER PC
    ====================== */

    if(debitNAG != creditNAG){
        Swal.fire(
            'Warning',
            'Journal PT Nirwana Alabare Garment tidak balance',
            'warning'
        );
        return;
    }

    if(debitNAK != creditNAK){
        Swal.fire(
            'Warning',
            'Journal PT Nirwana Alabare Knitting tidak balance',
            'warning'
        );
        return;
    }

    console.log("VALIDASI LOLOS");

    Swal.fire({
        title: "Save Data?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel"
    }).then((result)=>{

        if(result.isConfirmed){

            $.ajax({

                url: "bank-out/save_bankout_none.php",
                type: "POST",
                data: $('#form-data3').serialize(),

                beforeSend:function(){
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick:false,
                        didOpen:()=>{ Swal.showLoading(); }
                    });
                },

                success:function(res){

                    let r = JSON.parse(res);

                    if(r.status == 'success'){

                        Swal.fire({
                            icon:'success',
                            title:'Success',
                            text:r.message
                        });

                    }else{

                        Swal.fire('Error', r.message, 'error');
                    }

                },

                error:function(xhr){
                    Swal.fire('Error','Server Error','error');
                }

            });

        }

    });

});




  </script>




  </body>

  </html>