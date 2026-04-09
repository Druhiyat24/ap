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
    <h5 class="mb-0"><i class="fas fa-edit"></i> FORM OUTGOING BANK</h5>
  </div>

  <!-- Card Table -->
  <div class="card shadow border-0 mt-1">
    <div class="card-body p-4">

      <div class="tab-container">
        <button class="tablinks active" onclick="openTab(event, 'outbank-lp')">List Payment</button>
        <button class="tablinks" onclick="openTab(event, 'outbank-pv')">Payment Voucher</button>
        <button class="tablinks" onclick="openTab(event, 'outbank-none')">None</button>
      </div>


      <div id="outbank-lp" class="tabcontent">
        <?php include 'bank-out/outbank-lp.php'; ?>
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

    //KE 1...............................................................

    function getRate1() {

      let valuta3 = $('#currency1').val();
      let doc_date3 = $('#tgl_active1').val();

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
            $('#rate_bank1').val(res.rate);
          } else {
            $('#rate_bank1').val('1');
          }

          formatNumber(document.getElementById('rate_bank1'));

          // TAMBAHKAN INI
          hitungTotalLP();

        }
      });

    }

    $('#account1').on('change', function() {

      let bank1 = $(this).find(':selected').data('bank1');
      let currency1 = $(this).find(':selected').data('currency1');
      let namapc1 = $(this).find(':selected').data('namapc1');
      let kodepc1 = $(this).find(':selected').data('kodepc1');
      let kodebank1 = $(this).find(':selected').data('kodebank1');

      $('#bank1').val(bank1);
      $('#kode_bank1').val(kodebank1);
      $('#currency1').val(currency1);
      $('#profit_center_bank1').val(kodepc1);
      $('#profit_center_bank_show1').val(namapc1);


      if (currency1 === 'IDR') {

        $('#rate_bank1').val('1');
        formatNumber(document.getElementById('rate_bank1'));

        $('#rate_bank1').prop('readonly', true);

        // langsung hitung
        // hitungEqv2();

      } else {

        $('#rate_bank1').prop('readonly', false);
        getRate1(); // nanti setelah ajax selesai juga akan hitung

      }

      hitungTotalLP()

    });


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


// ===============================
// TARIK DATA PV
// ===============================
$('#btn_tarik_lp').on('click', function(){

  let tgl_awal  = $('#tgl_filawal1').val();
  let tgl_akhir = $('#tgl_filakhir1').val();
  let supplier  = $('#nama_supp1').val();

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
      $('#amount_bank1').val('');
      $('#eqv_idr_bank1').val('');

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
  hitungEqv1();

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
  hitungEqv1();

});



let isManualAmount = false;


// ===============================
// HITUNG TOTAL PV + ADJUST
// ===============================
function hitungTotalLP(){

  let total_lp = 0;

  let nag_debit = 0;
  let nak_debit = 0;

  let nag_credit = 0;
  let nak_credit = 0;

  let curr_h  = $('#currency1').val();
  let rate_bank  = getNumber($('#rate_bank1').val());
  let eqv_idr_bank1  = getNumber($('#eqv_idr_bank1').val());

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
    $('#amount_bank1').val(header_amount.toLocaleString('en-US'));
  }

  // =========================
  // 🔥 HEADER AMOUNT (KHUSUS HITUNG → IDR)
  // =========================
  let header_amount_idr = 0;

  if(isManualAmount){

    let manual_val = getNumber($('#amount_bank1').val());

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
  let header_pc = ($('#profit_center_bank1').val() || '').trim().toUpperCase();

  console.log('HEADER PC:', header_pc, 'HEADER DISPLAY:', header_amount, 'HEADER IDR:', header_amount_idr, 'MANUAL:', isManualAmount);

  if(header_pc === 'NAG'){
    nag_credit += header_amount_idr;
  }else if(header_pc === 'NAK'){
    nak_credit += header_amount_idr;
  }

  // =========================
  // 🔥 ADJUST (TABLE BAWAH)
  // =========================
  $('#tbody1 tr').each(function(index){

    let tr = $(this);

    let pc = (tr.find('select.prof_ctr1').first().val() || '').trim().toUpperCase();

    let debit  = getNumber(tr.find('input[name="txt_amount1[]"]').val());
    let credit = getNumber(tr.find('input[name="txt_credit1[]"]').val());

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

  // =========================
  // 🔥 EQV
  // =========================
  hitungEqv1();

}





// ===============================
// RESET TOTAL
// ===============================
function resetTotalLP(){

  $('#tot_debit_nag_lp, #tot_debit_nak_lp, #tot_debit_lp').val('');
  $('#tot_credit_nag_lp, #tot_credit_nak_lp, #tot_credit_lp').val('');

}

function hitungEqv1(){

  let amount = getNumber($('#amount_bank1').val());
  let rate   = getNumber($('#rate_bank1').val());

  let total = amount * rate;

  $('#eqv_idr_bank1').val(total.toLocaleString('en-US'));

}


// ===============================
// EVENT HEADER
// ===============================
$('#amount_bank1, #rate_bank1').on('keyup change', function(){
  isManualAmount = true;
  hitungTotalLP();
});


// ===============================
// EVENT ADJUST TABLE
// ===============================
$(document).on('keyup change', 
  'input[name="txt_amount1[]"], input[name="txt_credit1[]"], .prof_ctr1', 
  function(){
    hitungTotalLP();
  });




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

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff1[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date1[]" autocomplete="off"></td>

      <td>
      <select class="form-control selectpicker curr_det1" name="currenc2[]">
      <option value="IDR">IDR</option>
      </select>
      </td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" autocomplete="off"></td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

      <td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

      </tr>

      `;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_bank1').val();

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

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff1[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date1[]" autocomplete="off"></td>

      <td>
      <select class="form-control selectpicker curr_det1" name="currenc2[]">
      <option value="IDR">IDR</option>
      </select>
      </td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" autocomplete="off"></td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

      <td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

      </tr>
            `;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_bank1').val();
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


      $('#simpan1').on('click', function(){

      console.log("===== MULAI SAVE PV =====");

  // =========================
  // HEADER
  // =========================
  let header = {
    ref        : $('#ref_num1').val(),
    tgl        : $('#tgl_active1').val(),
    supp       : $('#nama_supp1').val(),
    pc_header  : $('#profit_center_bank1').val(),
    account    : $('#account1').val(),
    bank       : $('#bank1').val(),
    currency   : $('#currency1').val(),
    amount     : getNumber($('#amount_bank1').val()),
    rate       : getNumber($('#rate_bank1').val()),
    eqv        : getNumber($('#eqv_idr_bank1').val()),
    desc       : $('#pesan1').val(),
    kode_bank  : $('#kode_bank1').val()
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

  $('#tbody1 tr').each(function(index){

    let tr = $(this);

    let coa   = tr.find('select.no_coa1').first().val();
    let pc    = tr.find('select.prof_ctr1').first().val();
    let cc    = tr.find('select.cost_ctr1').first().val();
    let debit = parseFloat(tr.find('input[name="txt_amount1[]"]').val()) || 0;
    let credit= parseFloat(tr.find('input[name="txt_credit1[]"]').val()) || 0;
    let desc  = tr.find('input[name="keterangan1[]"]').val();
    let curr  = tr.find('select.currenc1').first().val();
    let reff_doc  = tr.find('input[name="no_reff1[]"]').val();
    let reff_date  = tr.find('input[name="reff_date1[]"]').val();

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
      tr.find('.no_coa1').focus();
      valid_adjust = false;
      return false;
    }

    if(!pc || pc === '-'){
      Swal.fire('Warning','Profit Center wajib diisi','warning');
      tr.find('.prof_ctr1').focus();
      valid_adjust = false;
      return false;
    }

    if(coaWajibCC.includes(coa)){
      if(!cc || cc === '-' || cc === ''){
        Swal.fire('Warning','COA wajib isi Cost Center','warning');
        tr.find('.cost_ctr1').focus();
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
    url: 'bank-out/save_lp.php',
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
          location.href='bank-out.php';
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



    //KE 2................................................................

    let tablePV;

    function initTablePV(){

    // destroy dulu kalau sudah pernah dibuat
    if ($.fn.DataTable.isDataTable('#table-pv')) {
      $('#table-pv').DataTable().destroy();
    }

    tablePV = $('#table-pv').DataTable({
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


// ===============================
// TARIK DATA PV
// ===============================
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
      if ($.fn.DataTable.isDataTable('#table-pv')) {
        $('#table-pv').DataTable().clear().destroy();
      }
      $('#table-pv tbody').html(res);
      Swal.close();
      initTablePV();
    }
  });

});


// ===============================
// CHECK PV
// ===============================
$(document).on('change', '.chk_pv', function(){

  let tr = $(this).closest('tr');
  let no_pv = tr.find('.no_pv').data('nopv');

  let total = parseFloat(tr.find('.total_pv').data('total')) || 0;
  let input = tr.find('.txt_amount_pv');

  if($(this).is(':checked')){

    input.prop('disabled', false);
    input.val(total.toLocaleString('en-US'));

    $.ajax({
      url: 'bank-out/get_pv_detail.php',
      type: 'POST',
      data: { no_pv: no_pv },
      success: function(res){

        let data = JSON.parse(res);

        // HEADER (PC HEADER SAJA)
        $('#account2').val(data.account);
        $('#bank2').val(data.bank);
        $('#currency2').val(data.currency);
        $('#kode_bank2').val(data.b_code);

        $('#profit_center_bank2').val(data.profit_center);
        $('#profit_center_bank_show').val(data.nama_pc);

        loadRate();
        hitungTotalPV();

      }
    });

  }else{

    input.prop('disabled', true);
    input.val('');

    if($('.chk_pv:checked').length === 0){

      // RESET HEADER
      $('#amount_bank2').val('');
      $('#rate_bank2').val('');
      $('#eqv_idr_bank2').val('');

      $('#account2').val('');
      $('#kode_bank2').val('');
      $('#bank2').val('');
      $('#currency2').val('');
      $('#profit_center_bank2').val('');
      $('#profit_center_bank_show').val('');

      resetTotalPC();

    }else{
      hitungTotalPV();
    }

  }

});


// ===============================
// EDIT AMOUNT PV
// ===============================
$(document).on('keyup', '.txt_amount_pv', function(){

  let tr = $(this).closest('tr');
  let max = parseFloat(tr.find('.total_pv').data('total')) || 0;

  let val = $(this).val().replace(/,/g,'');
  val = parseFloat(val) || 0;

  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total PV','warning');
    val = max;
  }

  $(this).val(val.toLocaleString('en-US'));

  hitungTotalPV();

});


// ===============================
// HITUNG TOTAL PV + ADJUST
// ===============================
function hitungTotalPV(){

  let total_pv = 0;

  let nag_debit = 0;
  let nak_debit = 0;

  // =========================
  // 🔥 PV (DETAIL ATAS)
  // =========================
  $('.chk_pv:checked').each(function(){

    let tr = $(this).closest('tr');

    let val = tr.find('.txt_amount_pv').val().replace(/,/g,'');
    val = parseFloat(val) || 0;

    let pc = tr.find('.pc_pv').data('pcpv');

    total_pv += val;

    if(pc === 'NAG'){
      nag_debit += val;
    }else if(pc === 'NAK'){
      nak_debit += val;
    }

  });

  // =========================
  // 🔥 HEADER (CREDIT → PC HEADER)
  // =========================
  let header_pc = $('#profit_center_bank2').val();

  let nag_credit = 0;
  let nak_credit = 0;

  if(header_pc === 'NAG'){
    nag_credit += total_pv;
  }else if(header_pc === 'NAK'){
    nak_credit += total_pv;
  }

  // =========================
  // 🔥 ADJUST (TABLE BAWAH)
  // =========================
  $('#tbody2 tr').each(function(){

    let tr = $(this);

    let pc = (tr.find('select.prof_ctr2').first().val() || '').trim().toUpperCase();
    console.log('ROW:', tr.index());
    console.log('PC RAW:', tr.find('select.prof_ctr2').val());
    console.log('PC TEXT:', tr.find('select.prof_ctr2 option:selected').text());


    let debit  = getNumber(tr.find('input[name="txt_amount2[]"]').val());
    let credit = getNumber(tr.find('input[name="txt_credit2[]"]').val());

    console.log('PC:', pc, 'Debit:', debit, 'Credit:', credit);

    if(pc === 'NAG'){
      nag_debit  += debit;
      nag_credit += credit;
    }else if(pc === 'NAK'){
      nak_debit  += debit;
      nak_credit += credit;
    }

  });


  // =========================
  // GRAND TOTAL
  // =========================
  let grand_debit  = nag_debit + nak_debit;
  let grand_credit = nag_credit + nak_credit;

  // =========================
  // HEADER AMOUNT
  // =========================
  $('#amount_bank2').val(total_pv.toLocaleString('en-US'));

  // =========================
  // UPDATE UI
  // =========================
  $('#tot_debit_nag_pv').val(nag_debit.toLocaleString('en-US'));
  $('#tot_debit_nak_pv').val(nak_debit.toLocaleString('en-US'));
  $('#tot_debit_pv').val(grand_debit.toLocaleString('en-US'));

  $('#tot_credit_nag_pv').val(nag_credit.toLocaleString('en-US'));
  $('#tot_credit_nak_pv').val(nak_credit.toLocaleString('en-US'));
  $('#tot_credit_pv').val(grand_credit.toLocaleString('en-US'));

  // =========================
  // EQV
  // =========================
  hitungEqv2();

}


// ===============================
// RESET TOTAL
// ===============================
function resetTotalPC(){

  $('#tot_debit_nag_pv, #tot_debit_nak_pv, #tot_debit_pv').val('');
  $('#tot_credit_nag_pv, #tot_credit_nak_pv, #tot_credit_pv').val('');

}


// ===============================
// HITUNG EQV
// ===============================
function hitungEqv2(){

  let amount = getNumber($('#amount_bank2').val());
  let rate   = getNumber($('#rate_bank2').val());

  let total = amount * rate;

  $('#eqv_idr_bank2').val(total.toLocaleString('en-US'));

}


// ===============================
// EVENT HEADER
// ===============================
$('#amount_bank2, #rate_bank2').on('keyup change', function(){
  hitungEqv2();
});


// ===============================
// EVENT ADJUST TABLE
// ===============================
$(document).on('keyup change', 
  'input[name="txt_amount2[]"], input[name="txt_credit2[]"], .prof_ctr2', 
  function(){
    hitungTotalPV();
  });


// ===============================
// LOAD RATE
// ===============================
function loadRate(){

  let valuta = $('#currency2').val();
  let doc_date = $('#tgl_active2').val();

  if(valuta === 'IDR'){
    $('#rate_bank2')
    .val(1)
    .prop('readonly', true);

    formatNumber(document.getElementById('rate_bank2'));
    hitungEqv2();
    return;
  }else{
    $('#rate_bank2').prop('readonly', false);
  }

  if (!valuta || !doc_date) return;

  $.ajax({
    url: 'get_rate.php',
    type: 'POST',
    dataType: 'json',
    data: {
      valuta: valuta,
      doc_date: doc_date
    },
    success: function(res){

      let rate = (res.status === 'ok') ? res.rate : 1;

      $('#rate_bank2').val(rate);
      formatNumber(document.getElementById('rate_bank2'));

      hitungEqv2();

    }
  });

}


// ===============================
// TANGGAL CHANGE
// ===============================
$('#tgl_active2').on('change', function(){
  loadRate();
});


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

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff2[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date2[]" autocomplete="off"></td>

      <td>
      <select class="form-control selectpicker curr_det2" name="currenc2[]">
      <option value="IDR">IDR</option>
      </select>
      </td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount2[]" autocomplete="off"></td>

      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit2[]" autocomplete="off"></td>

      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan2[]" autocomplete="off"></td>

      <td><input name="chk_a2[]" type="checkbox" class="checkall_a2"></td>

      </tr>

      `;

      row.innerHTML = element;


      /* refresh selectpicker */
      $('.selectpicker').selectpicker('refresh');


      /* set default profit center dari header */

      var headerPC = $('#profit_center_bank2').val();

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

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff2[]" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date2[]" autocomplete="off"></td>

            <td>
            <select class="form-control selectpicker curr_det2" name="currenc2[]">
            <option value="IDR">IDR</option>
            </select>
            </td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount2[]" autocomplete="off"></td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit2[]" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan2[]" autocomplete="off"></td>

            <td><input name="chk_a2[]" type="checkbox" class="checkall_a2"></td>

            </tr>
            `;

            var newRow = table.insertRow(i + 1);
            newRow.innerHTML = element2;

            inserted = true;

            /* set profit center default */
            var headerPC = $('#profit_center_bank2').val();
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

    $('#simpan2').on('click', function(){

      console.log("===== MULAI SAVE PV =====");

  // =========================
  // HEADER
  // =========================
  let header = {
    ref        : $('#ref_num2').val(),
    tgl        : $('#tgl_active2').val(),
    supp       : $('#nama_supp2').val(),
    pc_header  : $('#profit_center_bank2').val(),
    account    : $('#account2').val(),
    bank       : $('#bank2').val(),
    currency   : $('#currency2').val(),
    amount     : getNumber($('#amount_bank2').val()),
    rate       : getNumber($('#rate_bank2').val()),
    eqv        : getNumber($('#eqv_idr_bank2').val()),
    desc       : $('#pesan2').val(),
    kode_bank  : $('#kode_bank2').val()
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

  if($('.chk_pv:checked').length === 0){
    Swal.fire('Warning','Pilih minimal 1 PV','warning');
    return;
  }

  // =========================
  // TOTAL GLOBAL
  // =========================
  let total_debit  = getNumber($('#tot_debit_pv').val());
  let total_credit = getNumber($('#tot_credit_pv').val());

  console.log("TOTAL GLOBAL =>", total_debit, total_credit);

  if(total_debit !== total_credit){
    Swal.fire('Error','Total Debit & Credit tidak balance','error');
    return;
  }

  // =========================
  // TOTAL PER PC
  // =========================
  let nag_debit  = getNumber($('#tot_debit_nag_pv').val());
  let nag_credit = getNumber($('#tot_credit_nag_pv').val());

  let nak_debit  = getNumber($('#tot_debit_nak_pv').val());
  let nak_credit = getNumber($('#tot_credit_nak_pv').val());

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
  let detail_pv = [];

  $('.chk_pv:checked').each(function(){

    let tr = $(this).closest('tr');

    let data = {
      no_pv  : tr.find('.no_pv').data('nopv'),
      amount : getNumber(tr.find('.txt_amount_pv').val()),
      pc     : tr.find('.pc_pv').data('pcpv')
    };

    console.log("PV:", data);

    detail_pv.push(data);

  });

  // =========================
  // DETAIL ADJUST
  // =========================
  let detail_adjust = [];
  let valid_adjust = true;

  $('#tbody2 tr').each(function(index){

    let tr = $(this);

    let coa   = tr.find('select.no_coa2').first().val();
    let pc    = tr.find('select.prof_ctr2').first().val();
    let cc    = tr.find('select.cost_ctr2').first().val();
    let debit = parseFloat(tr.find('input[name="txt_amount2[]"]').val()) || 0;
    let credit= parseFloat(tr.find('input[name="txt_credit2[]"]').val()) || 0;
    let desc  = tr.find('input[name="keterangan2[]"]').val();
    let curr  = tr.find('select.currenc2').first().val();
    let reff_doc  = tr.find('input[name="no_reff2[]"]').val();
    let reff_date  = tr.find('input[name="reff_date2[]"]').val();

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
      tr.find('.no_coa2').focus();
      valid_adjust = false;
      return false;
    }

    if(!pc || pc === '-'){
      Swal.fire('Warning','Profit Center wajib diisi','warning');
      tr.find('.prof_ctr2').focus();
      valid_adjust = false;
      return false;
    }

    if(coaWajibCC.includes(coa)){
      if(!cc || cc === '-' || cc === ''){
        Swal.fire('Warning','COA wajib isi Cost Center','warning');
        tr.find('.cost_ctr2').focus();
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

  console.log("DETAIL PV:", detail_pv);
  console.log("DETAIL ADJUST:", detail_adjust);

  // =========================
  // FINAL DATA
  // =========================
  let finalData = {
    header,
    detail_pv,
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
    url: 'bank-out/save_pv.php',
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
          location.href='bank-out.php';
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



    // KE 3............................................................

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
                }).then(() => {
              location.href='bank-out.php';
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