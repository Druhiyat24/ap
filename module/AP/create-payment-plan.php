<?php
/* ============================================================================
   Update Payment Plan - FORMULIR. Berkas ini BERDIRI SENDIRI dan dibuka dari
   tombol Create New di list-payment-plan.php - bukan dari butir menu
   tersendiri, sama seperti Create Petty Cash Out.

   Alurnya: isi tanggal rencana bayar + catatan -> cari voucher -> centang
   yang mau dijadwalkan -> Save. Satu dokumen = SATU tanggal rencana untuk
   seluruh voucher yang dicentang; voucher yang jadwalnya lain dibuatkan
   dokumen sendiri.

   Nomor dokumennya TIDAK ditampilkan sebagai isian: nomornya dibuat waktu
   disimpan (simpan_payment_plan.php) supaya dua orang yang membuka formulir
   bersamaan tidak mendapat nomor yang sama.

   Relationship, Item Type dan Flexibility di sini hanya DITAMPILKAN - tiga-
   tiganya punya sumbernya sendiri:
     Relationship, Item Type : mastercoa_v2 lewat kontrabon_h.no_coa - sama
         dgn laporan AP (Payment Voucher).
     Flexibility Category    : mastersupplier.payment_rating. Masih kosong
         untuk semua supplier, jadi wajar kalau tampil '-'.

   SUSUNAN TAMPILANNYA MENGIKUTI create-pettycash-out.php (diminta pemakai,
   4 Nov 2026): kosakata .pco- - satu kartu luar dgn kepala berkotak ikon,
   bagian-bagian bertanda .pco-sec, dan tombol simpan duduk di .pco-foot.
   Sebelumnya halaman ini memakai kartu .ftl- milik halaman DAFTAR plus
   tombol Bootstrap biasa, jadi tampil lain dari formulir AP yang sudah
   jalan.
   ============================================================================ */
include '../header.php';
?>
<?php
/* Daftar supplier untuk saringan - hanya yang punya voucher disetujui, jadi
   isinya tidak sepanjang master supplier. */
$supplierList = array();
$qSup = mysqli_query($conn1, "SELECT DISTINCT nama_supp FROM kontrabon_h
        WHERE status IN ('Approved','FIRST APPROVED','SECOND APPROVED')
          AND nama_supp IS NOT NULL AND nama_supp <> ''
        ORDER BY nama_supp ASC");
if ($qSup) {
    while ($rSup = mysqli_fetch_assoc($qSup)) { $supplierList[] = $rSup['nama_supp']; }
}
?>

<!-- Berkas CSS ditaut dgn penanda versi dari filemtime, jadi perubahan di
     dalamnya langsung sampai ke user dan tidak tertahan cache.

     Kosakata .pco- dipakai BERSAMA oleh create & kelima halaman edit Petty
     Cash Out. Halaman ini ikut memakainya - bukan menyalin aturannya ke blok
     style sendiri - supaya tidak ada salinan yang nanti melenceng. -->
<link rel="stylesheet" href="../css/app-pco-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-pco-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<!-- Hanya untuk pil .ftl-st di kolom Current Plan - bentuknya sama dgn pil
     status di halaman daftar, jadi tidak dibuat yang baru. -->
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* Hanya yang BELUM ada di app-pco-form.css. Sengaja sedikit: sisanya
   diurus kosakata .pco-. */
#table-pv tr.app-pilih > td { background: #e8f0fe; }
#table-pv td { vertical-align: middle; }

/* Flexibility Category memakai warnanya sendiri - itu memang isyarat lampu
   lalu lintas, jadi angka di sebelahnya tidak perlu dibaca dulu. */
.app-flex { font-size: .75rem; font-weight: 600; padding: 1px 8px; border-radius: 10px;
            border: 1px solid transparent; white-space: nowrap; }
.app-flex.is-green  { background: #e6f4ea; border-color: #b7e1c4; color: #1e7e34; }
.app-flex.is-yellow { background: #fff6e0; border-color: #f3dca0; color: #8a6100; }
.app-flex.is-red    { background: #fdecea; border-color: #f5c2bd; color: #a3281c; }
.app-flex.is-kosong { color: #9aa0a6; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="pco-card">
    <!-- Kepala kartu: kotak ikon + judul + jejak menu, mengikuti create
         Petty Cash Out. -->
    <div class="pco-head">
      <span class="pco-head-icon"><i class="fas fa-calendar-plus"></i></span>
      <div>
        <h1>Form Payment Plan</h1>
        <span class="pco-crumb">AP &rsaquo; Payment Plan &rsaquo; Update Payment Plan &rsaquo; Create</span>
      </div>
    </div><!-- /.pco-head -->

    <div class="pco-body">

      <!-- ---------------- kepala dokumen ---------------- -->
      <div class="pco-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Header</div>
      <div class="form-row">
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="plan_date" class="form-label"><b>Payment Plan Date</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="plan_date"
                 name="plan_date" value="" placeholder="dd-mm-yyyy" autocomplete="off">
        </div>
        <div class="col-12 col-xl-6 mb-2">
          <label for="keterangan" class="form-label"><b>Note</b></label>
          <input type="text" class="form-control form-control-sm" id="keterangan"
                 name="keterangan" maxlength="255" placeholder="Optional">
        </div>
      </div>

      <!-- ---------------- saringan voucher ---------------- -->
      <div class="pco-sec"><i class="fa fa-search" aria-hidden="true"></i> Find payment vouchers</div>
      <div class="form-row">

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="dasar" class="form-label"><b>Date Basis</b></label>
          <select class="form-control form-control-sm" id="dasar" name="dasar">
            <option value="tempo">Due Date</option>
            <option value="kbon">Voucher Date</option>
          </select>
        </div>

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="start_date" class="form-label"><b>From</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="start_date"
                 name="start_date" value="<?php echo date('01-m-Y'); ?>"
                 placeholder="Start Date" autocomplete="off">
        </div>

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="end_date" class="form-label"><b>To</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="end_date"
                 name="end_date" value="<?php echo date('t-m-Y'); ?>"
                 placeholder="End Date" autocomplete="off">
        </div>

        <!-- Daftar supplier panjang (ratusan), jadi dropdown-nya berkotak
             cari - sama spt saringan Reference di List Petty Cash Out. -->
        <div class="col-12 col-sm-6 col-xl-3 mb-2">
          <label for="supplier" class="form-label"><b>Supplier</b></label>
          <select class="form-control selectpicker" id="supplier" name="supplier"
                  data-dropup-auto="false" data-live-search="true" data-container="body">
            <option value="" selected="selected">All Suppliers</option>
            <?php foreach ($supplierList as $s) { ?>
            <option value="<?php echo htmlspecialchars($s, ENT_QUOTES); ?>"><?php echo htmlspecialchars($s, ENT_QUOTES); ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-12 col-xl-3 mb-2 pco-actions align-items-end">
          <div class="custom-control custom-checkbox mr-2 mb-2">
            <input type="checkbox" class="custom-control-input" id="hanya_belum" checked>
            <label class="custom-control-label" for="hanya_belum">Not scheduled yet</label>
          </div>
          <button type="button" class="app-btn app-btn-primary app-btn-sm mb-2" id="btn-cari">
            <i class="fa fa-search" aria-hidden="true"></i> Search
          </button>
        </div>

      </div><!-- /.form-row -->

      <!-- ---------------- hasil ---------------- -->
      <div class="pco-sec"><i class="fa fa-list" aria-hidden="true"></i> Payment vouchers
        <span class="text-muted ml-2" id="app-info" style="text-transform:none;letter-spacing:0;font-weight:500;">Pick a period, then press Search.</span>
      </div>

      <!-- .app-loading-wrap: area yang ditutup overlay saat data ditarik. -->
      <div class="app-loading-wrap" id="ppLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>

        <!-- TANPA .table-responsive: pembungkus yang bisa digulir ke samping
             membuat modul Responsive menyangka ruangnya selalu cukup, jadi
             tanda "+" tidak pernah muncul. -->
        <table id="table-pv" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th style="text-align:center;vertical-align:middle;"></th>
              <th style="text-align:center;vertical-align:middle;"><input type="checkbox" id="pilih-semua" title="Tick every row on this page"></th>
              <th style="text-align:center;vertical-align:middle;">Supplier</th>
              <th style="text-align:center;vertical-align:middle;">Payment Voucher No</th>
              <th style="text-align:center;vertical-align:middle;">Voucher Date</th>
              <th style="text-align:center;vertical-align:middle;">Due Date</th>
              <th style="text-align:center;vertical-align:middle;">Term</th>
              <th style="text-align:center;vertical-align:middle;">Relationship</th>
              <th style="text-align:center;vertical-align:middle;">Item Type</th>
              <th style="text-align:center;vertical-align:middle;">Flexibility</th>
              <th style="text-align:center;vertical-align:middle;">Curr</th>
              <th style="text-align:center;vertical-align:middle;">Tax Base</th>
              <th style="text-align:center;vertical-align:middle;">VAT</th>
              <th style="text-align:center;vertical-align:middle;">Total Invoice</th>
              <th style="text-align:center;vertical-align:middle;">Current Plan</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div><!-- /.app-loading-wrap -->

    </div><!-- /.pco-body -->

    <!-- Tombol simpan duduk di kaki kartu, sama spt formulir Petty Cash
         Out - bukan menempel di sebelah isian tanggal. -->
    <div class="pco-foot">
      <div class="pco-actions">
        <button type="button" class="app-btn app-btn-success app-btn-sm" id="btn-simpan" disabled>
          <i class="fa fa-save" aria-hidden="true"></i> Save
        </button>
        <a href="list-payment-plan.php" class="app-btn app-btn-light app-btn-sm">
          <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to List
        </a>
      </div>
    </div><!-- /.pco-foot -->

  </div><!-- /.pco-card -->
</div><!-- /.container-fluid -->

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');

  // Collapse/Expand icon
  $('#collapse-icon').addClass('fa-angle-double-left');

  // Collapse click
  $('[data-toggle=sidebar-colapse]').click(function () {
      SidebarCollapse();
  });

  function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
          SeparatorTitle.removeClass('d-flex');
      } else {
          SeparatorTitle.addClass('d-flex');
      }

      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
  $(function () { $('.selectpicker').selectpicker(); });
</script>

<script>
$(document).ready(function () {

  $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });

  /* Voucher yang dicentang. Kuncinya nomor voucher, jadi centangnya tetap
     terjaga waktu pindah halaman tabel. */
  var dipilih = {};

  function hitungPilihan() {
    var n = Object.keys(dipilih).length;
    $('#btn-simpan').prop('disabled', n === 0 || $('#plan_date').val().trim() === '');
    $('#app-info').text(n ? (n + ' voucher' + (n === 1 ? '' : 's') + ' ticked.') : ($('#app-info').data('asli') || ''));
  }

  /* Flexibility datang dari master supplier apa adanya ('GREEN', 'Green',
     'green'), jadi warnanya dipilih dari huruf besar semua. */
  function pilFlex(v) {
    var t = (v || '-').toString();
    var k = t.trim().toUpperCase();
    var kelas = (k === 'GREEN') ? 'is-green'
              : (k === 'YELLOW') ? 'is-yellow'
              : (k === 'RED') ? 'is-red' : 'is-kosong';
    return '<span class="app-flex ' + kelas + '">' + t + '</span>';
  }

  var tabel = $('#table-pv').DataTable({
    ordering: false,
    processing: true,
    serverSide: false,
    pageLength: 10,
    searching: true,
    info: true,
    autoWidth: false,
    stateSave: true,
    stateDuration: -1,

    /* RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris rincian yang
       dibuka lewat tanda "+", bukan dipaksa digulir ke samping. */
    responsive: { details: { type: 'column', target: 0 } },

    ajax: {
      url: 'ajx_cari-pv-payment-plan.php',
      type: 'POST',
      data: function (d) {
        d.start_date  = $('#start_date').val();
        d.end_date    = $('#end_date').val();
        d.supplier    = $('#supplier').val();
        d.dasar       = $('#dasar').val();
        d.hanya_belum = $('#hanya_belum').is(':checked') ? '1' : '0';
      },
      dataSrc: function (res) {
        if (res.error) {
          Swal.fire({ icon: 'error', title: 'Could not load the list', text: res.error });
          return [];
        }
        dipilih = {};
        $('#pilih-semua').prop('checked', false);
        var n = res.data ? res.data.length : 0;
        $('#app-info').data('asli', n + ' voucher' + (n === 1 ? '' : 's') + ' found.');
        hitungPilihan();
        return res.data || [];
      },
      /* Tanpa ini overlay-nya menggantung terus kalau permintaannya gagal,
         dan user tidak tahu apa yang terjadi. */
      error: function (xhr) {
        $('#ppLoad').removeClass('is-loading');
        Swal.fire({
          icon: 'error', title: 'Failed to load the list',
          text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')
        });
      }
    },

    /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
       dipertahankan saat layar menyempit. Centang & nomor voucher dipegang
       paling kuat - itu isi pekerjaan di layar ini. */
    columns: [
      { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
      { data: null, orderable: false, searchable: false, responsivePriority: 1,
        render: function (d, t, row) {
          if (t !== 'display') { return ''; }
          return '<input type="checkbox" class="app-centang"' +
                 (dipilih[row.no_kbon] ? ' checked' : '') + '>';
        } },
      { data: 'nama_supp',   responsivePriority: 4 },
      { data: 'no_kbon',     responsivePriority: 2 },
      { data: 'tgl_kbon',    responsivePriority: 11 },
      { data: 'tgl_tempo',   responsivePriority: 5 },
      { data: 'term',        responsivePriority: 12 },
      { data: 'relasi',      responsivePriority: 9 },
      { data: 'item_type',   responsivePriority: 3 },
      { data: 'flexibility', responsivePriority: 8, render: function (d, t) {
          return (t !== 'display') ? (d || '') : pilFlex(d);
        } },
      { data: 'curr',        responsivePriority: 10 },
      { data: 'tax_base',    responsivePriority: 13 },
      { data: 'tax',         responsivePriority: 14 },
      { data: 'total',       responsivePriority: 6 },
      { data: 'status_plan', responsivePriority: 7 }
    ],

    /* Kelas "all" milik Responsive = kolom ini TIDAK PERNAH dikolaps,
       seberapa pun sempit layarnya. Yang dikunci: kotak centang, nomor
       voucher, dan Total Invoice - tanpa ketiganya layar ini tidak bisa
       dipakai sama sekali di HP. */
    /* Kelas .ftl-doc / .ftl-amt TIDAK dipakai di sini: aturannya ber-scope
       `.ftl-tbl td.ftl-...` di app-ftr-list.css, sedangkan tabel ini tabel
       form (.pco-card). Menuliskannya cuma akan jadi kelas yang tak
       berpengaruh dan menyesatkan orang berikutnya. */
    columnDefs: [
      { targets: [1],        className: 'text-center all' },
      { targets: [3],        className: 'text-left all' },
      { targets: [13],       className: 'text-right all' },
      { targets: [2, 7, 8],  className: 'text-left' },
      { targets: [11, 12],   className: 'text-right' },
      { targets: [4, 5, 6, 9, 10, 14], className: 'text-center' }
    ],

    language: {
      emptyTable: 'No approved payment voucher found for this filter.',
      zeroRecords: 'No payment voucher matches your search.'
    }
  });

  /* Overlay mengikuti status processing DataTables. */
  tabel.on('processing.dt', function (e, settings, processing) {
    $('#ppLoad').toggleClass('is-loading', processing);
  });

  /* Centang ditangkap di satu tempat (delegasi), jadi baris halaman
     berikutnya ikut terpantau tanpa dipasangi ulang. */
  $('#table-pv tbody').on('change', 'input.app-centang', function () {
    var $tr = $(this).closest('tr');
    var data = tabel.row($tr).data();
    if (!data) { return; }
    if ($(this).is(':checked')) {
      dipilih[data.no_kbon] = { no_kbon: data.no_kbon };
      $tr.addClass('app-pilih');
    } else {
      delete dipilih[data.no_kbon];
      $tr.removeClass('app-pilih');
    }
    hitungPilihan();
  });

  /* Centang semua hanya berlaku untuk baris di HALAMAN yang terlihat -
     mencentang ratusan baris sekaligus tanpa dilihat lebih berbahaya
     daripada berguna. */
  $('#pilih-semua').on('change', function () {
    var nyala = $(this).is(':checked');
    $('#table-pv tbody input.app-centang').each(function () {
      $(this).prop('checked', nyala).trigger('change');
    });
  });

  $('#plan_date').on('change keyup', hitungPilihan);

  $('#btn-cari').on('click', function () {
    if (Object.keys(dipilih).length) {
      Swal.fire({
        icon: 'warning',
        title: 'Unsaved selection',
        text: 'Searching again will clear the vouchers you have ticked. Continue?',
        showCancelButton: true,
        confirmButtonText: 'Discard and search'
      }).then(function (r) { if (r.isConfirmed) { tabel.ajax.reload(); } });
      return;
    }
    tabel.ajax.reload();
  });

  /* Tombol dikunci selama penyimpanan - supaya dokumen tidak terbuat dua kali. */
  $('#btn-simpan').on('click', function () {
    var baris = [];
    for (var k in dipilih) { if (dipilih.hasOwnProperty(k)) { baris.push(dipilih[k]); } }
    if (!baris.length) { return; }
    if ($('#plan_date').val().trim() === '') {
      Swal.fire({ icon: 'warning', title: 'Payment plan date is empty', text: 'Please fill it in first.' });
      return;
    }

    var $tombol = $('#btn-simpan, #btn-cari').prop('disabled', true);
    Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

    $.ajax({
      url: 'simpan_payment_plan.php',
      type: 'POST',
      dataType: 'json',
      data: {
        plan_date:  $('#plan_date').val(),
        keterangan: $('#keterangan').val(),
        baris:      JSON.stringify(baris)
      }
    }).done(function (res) {
      if (res.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'Not saved', text: res.message || 'Unknown error' });
        $tombol.prop('disabled', false);
        return;
      }
      var lanjut = function () { window.location.href = 'list-payment-plan.php'; };
      if (res.ditolak && res.ditolak.length) {
        Swal.fire({
          icon: 'warning',
          title: res.message,
          html: 'These vouchers were skipped:<br><small>' + res.ditolak.join('<br>') + '</small>'
        }).then(lanjut);
      } else {
        Swal.fire({ icon: 'success', title: res.message, timer: 1800, showConfirmButton: false }).then(lanjut);
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Not saved', text: 'The server did not answer. Please try again.' });
      $tombol.prop('disabled', false);
    });
  });

});
</script>
