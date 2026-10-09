<?php
/* ============================================================================
   Update BPB - GENERAL.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumennya dibaca dari tabel `bpb` dgn bpbno_int LIKE 'GEN/%'. GEN/IN
   maupun GEN/RI ikut semua: diperiksa ke data 8 Okt 2026, jurnal GEN/RI
   SEARAH dgn penerimaan (persediaan didebit, GR/IR dikredit, type
   'AP - BPB'), jadi bukan retur akuntansi seperti GK/RO di Fabric.

   AWAS - kunci sambungan ke PO BEDA dari Fabric/Accessories:
   masteritem.id_gen KOSONG (NULL) untuk semua item GEN, sedangkan
   po_item.id_gen justru berisi id_item-nya langsung. Jadi di sini
   dipakai  pi.id_gen = bpb.id_item , bukan  pi.id_gen = masteritem.id_gen .
   Dgn kunci yang salah, 0 dari 4.816 baris dapat harga PO - tombol
   "isi dari PO" dan ceklis "sembunyikan yang sudah cocok" mati tanpa
   pesan apa pun. Dgn kunci ini: 4.812 dari 4.816 dapat harga.
   ============================================================================ */
include '../header.php';
?>

<?php
mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS Req_update_bpb_h (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_pengajuan VARCHAR(30) NOT NULL,
  tgl_pengajuan DATE NOT NULL,
  nama_supp VARCHAR(100) DEFAULT NULL,
  deskripsi VARCHAR(255) DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Draft',
  created_by VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_no_pengajuan (no_pengajuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS Req_update_bpb (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_pengajuan VARCHAR(30) NOT NULL,
  no_bpb VARCHAR(50) NOT NULL,
  tgl_bpb DATE DEFAULT NULL,
  nama_supp VARCHAR(100) DEFAULT NULL,
  no_po VARCHAR(50) DEFAULT NULL,
  id_det VARCHAR(50) DEFAULT NULL,
  no_ws VARCHAR(50) DEFAULT NULL,
  id_jo VARCHAR(50) DEFAULT NULL,
  id_item VARCHAR(50) DEFAULT NULL,
  desc_item VARCHAR(150) DEFAULT NULL,
  qty DECIMAL(15,2) DEFAULT 0,
  unit VARCHAR(20) DEFAULT NULL,
  curr VARCHAR(10) DEFAULT NULL,
  price_old DECIMAL(18,4) DEFAULT 0,
  price_new DECIMAL(18,4) DEFAULT 0,
  ppn_old DECIMAL(8,2) DEFAULT 0,
  ppn_new DECIMAL(8,2) DEFAULT 0,
  created_by VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_no_pengajuan (no_pengajuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "ALTER TABLE Req_update_bpb ADD COLUMN IF NOT EXISTS id_jo VARCHAR(50) DEFAULT NULL AFTER no_ws");

/* Kolom pembeda jenis barang (fabric / accessories / menyusul yang lain).
   Dibuat di sini supaya basis data yang belum dimigrasi ikut tertata saat
   halaman ini pertama dibuka - pola yang sama dgn id_jo di atas. */
mysqli_query($conn1, "ALTER TABLE Req_update_bpb_h ADD COLUMN IF NOT EXISTS jenis VARCHAR(20) NOT NULL DEFAULT 'fabric' AFTER status");

$jenis = 'general';
$label = 'General';

// Generate next transaction number: UPD/GK/MMYY/00001
/* Awalannya beda per jenis (UPD/GK/ vs UPD/GACC/) supaya penomorannya
   tidak pernah bertabrakan antar menu. */
$prefix = 'UPD/GEN/' . date('my') . '/';
/* Angka urutnya dipotong sepanjang awalan - DULU dipatok 13, yang hanya
   benar untuk 'UPD/GK/1026/' (12 huruf). Awalan aksesoris lebih panjang. */
$potong = strlen($prefix) + 1;
$cek = mysqli_query($conn1, "SELECT MAX(CAST(SUBSTRING(no_pengajuan,$potong) AS UNSIGNED)) mx FROM Req_update_bpb_h WHERE no_pengajuan LIKE '$prefix%'");
$row_cek = mysqli_fetch_assoc($cek);
$next_no = (!empty($row_cek['mx'])) ? ((int) $row_cek['mx'] + 1) : 1;
$no_pengajuan = $prefix . str_pad($next_no, 5, '0', STR_PAD_LEFT);
?>

<!-- Kartu & tabel memakai kosakata .ftl- (app-ftr-list.css) supaya satu
     keluarga dgn halaman daftar; perabot khas form ini (modal lebar, kaki
     form, isian di dalam sel) ada di app-ubf-form.css. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-ubf-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ubf-form.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fas fa-edit" aria-hidden="true"></i></span>
      <div>
        <h1>Update BPB <?php echo htmlspecialchars($label); ?></h1>
        <span class="ftl-crumb">Cost Accounting &rsaquo; Update BPB &rsaquo; <?php echo htmlspecialchars($label); ?> &rsaquo; Create</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <div class="ub-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Document</div>
      <form id="form-header">
        <div class="row">
          <div class="col-md-2 mb-2">
            <label for="no_pengajuan"><b>Transaction No</b></label>
            <input type="text" class="form-control form-control-sm" id="no_pengajuan" value="<?= htmlspecialchars($no_pengajuan) ?>" readonly>
          </div>
          <div class="col-md-2 mb-2">
            <label for="tgl_pengajuan"><b>Transaction Date</b></label>
            <input type="text" class="form-control form-control-sm" id="tgl_pengajuan" value="<?= date('Y-m-d') ?>" readonly>
          </div>
          <div class="col-md-4 mb-2">
            <label for="deskripsi"><b>Description</b> <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm" id="deskripsi" rows="1" placeholder="e.g. price correction for fabric receiving..." required></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2 mb-2">
            <label for="start_date"><b>BPB Date From</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="start_date" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>
          <div class="col-md-2 mb-2">
            <label for="end_date"><b>BPB Date To</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="end_date" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>
          <div class="col-md-3 mb-2">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control form-control-sm selectpicker" id="nama_supp" data-dropup-auto="false" data-live-search="true" data-container="body">
              <option value="ALL" selected>ALL</option>
              <?php
              $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
              while ($row = mysqli_fetch_array($sql)) {
                  $data = $row['Supplier'];
                  echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-2 mb-2 d-flex align-items-end">
            <button type="button" id="btnSearchBpb" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-search" aria-hidden="true"></i> Search BPB
            </button>
          </div>
        </div>
      </form>
    </div><!-- /.ftl-panel -->

    <div class="ftl-body">

      <!-- TABEL 1 - hasil pencarian BPB. Tombol "Edit Items" membuka modal. -->
      <div class="ub-bar">
        <span class="ub-sec ub-sec-inline"><i class="fa fa-list" aria-hidden="true"></i> BPB List</span>
        <span class="ub-bar-tools">
          <label class="ub-switch">
            <input type="checkbox" id="chkHideMatchBpb" checked> Hide rows already matching PO
            <span class="ub-switch-n" id="ub-hidden-n"></span>
          </label>
        </span>
      </div>
      <div class="ftl-tblwrap">
        <table id="table-bpb" class="table ftl-tbl ub-bpbtbl" style="width:100%">
          <thead>
            <tr class="thead-dark">
              <th>No BPB</th>
              <th>BPB Date</th>
              <th class="text-left">Supplier</th>
              <th class="text-left">No PO</th>
              <th>Curr</th>
              <th class="text-right">Qty</th>
              <th class="text-right">DPP</th>
              <th class="text-right">PPN</th>
              <th class="text-right">Total</th>
              <th>Price vs PO</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <!-- TABEL 2 - baris yang akan disimpan. Tabel INI yang dibaca saat Save
           (data-item), jadi id & urutan kolomnya tidak diubah. -->
      <div class="ub-sec" style="padding-top:20px;"><i class="fa fa-pen-square" aria-hidden="true"></i> Items to Update</div>
      <div class="ftl-tblwrap">
        <table id="table-selected" class="table ftl-tbl ub-listtbl">
          <thead>
            <tr class="thead-dark">
              <th class="text-center" style="width:166px;">No BPB</th>
              <th class="text-center" style="width:92px;">No WS</th>
              <th class="text-left">Item</th>
              <th class="text-right" style="width:92px;">Qty</th>
              <th class="text-center" style="width:56px;">Unit</th>
              <th class="text-center" style="width:54px;">Curr</th>
              <th class="text-right" style="width:108px;">Price (Old)</th>
              <th class="text-right" style="width:108px;">Price (New)</th>
              <th class="text-right" style="width:92px;">PPN % (Old)</th>
              <th class="text-right" style="width:92px;">PPN % (New)</th>
              <th class="text-center" style="width:62px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr id="row-empty-selected">
              <td colspan="11" class="ub-blankcell">
                <i class="fa fa-inbox" aria-hidden="true"></i>
                <b>Nothing to update yet</b>
                <span>Search a BPB above, then press <b>Edit Items</b> to correct its price or PPN.</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div><!-- /.ftl-body -->

    <div class="ub-foot">
      <span class="ub-foot-sisi">
        <span class="ub-foot-count"><b id="ub-count">0</b> row(s) ready to save</span>
        <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="location.href='update-bpb-general.php'">
          <i class="fa fa-angle-double-left" aria-hidden="true"></i> Back
        </button>
        <button type="button" id="btnSave" class="app-btn ub-btn-brand app-btn-sm">
          <i class="fas fa-save" aria-hidden="true"></i> Save Request
        </button>
      </span>
    </div>

  </div><!-- /.ftl-card -->
</div><!-- /.container-fluid -->

<!-- =========================================================================
     MODAL - penyuntingan harga & PPN per item, seperti semula.

     Dua kolom baru: Price (PO) dan tombol isi-dari-PO per baris. Angkanya
     memang SUDAH dikirim get_detail_bpb_general_edit.php (po_price / po_ppn)
     sejak dulu, hanya tidak pernah dipakai - jadi user mengetik ulang angka
     yang sebetulnya sudah diketahui sistem.

     Lebarnya ditentukan sendiri (96vw, maks 1500px) mengikuti pola modal
     Memorial Journal: .modal-xl bawaan Bootstrap 4 baru melebar di >=1200px
     dan tetap kurang untuk tabel selebar ini.
     ========================================================================= -->
<div class="modal fade ub-modal" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <span class="ub-mhead-icon"><i class="fas fa-boxes" aria-hidden="true"></i></span>
        <div>
          <h5 class="modal-title" id="modalDetailLabel">Items</h5>
          <small id="modalBpbLabel"></small>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="ub-crumb">
          <span>Tick the rows you want to change, then press <b>Add Selected</b>.</span>
          <span class="ub-crumb-tools">
            <label class="ub-switch">
              <input type="checkbox" id="chkHideMatch" checked> Hide rows already matching PO
            </label>
            <button type="button" id="btnFillPo" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-magic" aria-hidden="true"></i> Set all to PO
            </button>
          </span>
        </div>

        <div class="ub-tblwrap">
          <table id="table-modal-detail" class="table ftl-tbl ub-itemtbl">
            <thead>
              <tr class="thead-dark">
                <th class="text-center" style="width:32px;"><input type="checkbox" id="checkAllModal"></th>
                <th class="text-center" style="width:92px;">No WS</th>
                <th class="text-left">Item</th>
                <th class="text-right" style="width:92px;">Qty</th>
                <th class="text-center" style="width:56px;">Unit</th>
                <th class="text-center" style="width:54px;">Curr</th>
                <th class="text-right" style="width:106px;">Price (Current)</th>
                <th class="text-right" style="width:106px;">Price (PO)</th>
                <th class="text-right" style="width:122px;">New Price</th>
                <th class="text-right" style="width:92px;">PPN % (Current)</th>
                <th class="text-right" style="width:106px;">New PPN %</th>
                <th class="text-center" style="width:52px;"></th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="12" class="ub-empty"><i class="fas fa-spinner fa-spin"></i></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <span class="ub-mfoot-note" id="ub-mfoot-note">&nbsp;</span>
        <button type="button" class="app-btn app-btn-light app-btn-sm" data-dismiss="modal">Close</button>
        <button type="button" id="btnAddSelected" class="app-btn app-btn-primary app-btn-sm">
          <i class="fa fa-plus" aria-hidden="true"></i> Add Selected
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
          SeparatorTitle.removeClass('d-flex');
      } else {
          SeparatorTitle.addClass('d-flex');
      }

      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }

  $(function() {
      $('.selectpicker').selectpicker();
  });
</script>

<script>
  function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (ch) {
          return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
      });
  }

  function formatMoney(amount, decimalCount = 2) {
      const val = parseFloat(amount);
      if (isNaN(val)) return '0';
      return val.toLocaleString('en-US', {
          minimumFractionDigits: 0,
          maximumFractionDigits: decimalCount
      });
  }

  function toYmd(dmy) {
      if (!dmy) return '';
      let p = dmy.split('-'); // [dd, mm, yyyy]
      return `${p[2]}-${p[1]}-${p[0]}`;
  }

  let bpbTable;
  /* cocokPo[indeks baris] = true kalau harga BPB itu sudah sama dgn PO. */
  let cocokPo = [];

  /* Penyaring tambahan DataTables bersifat global, jadi harus dipagari:
     hanya berlaku untuk #table-bpb, tabel lain dibiarkan apa adanya. */
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      if (settings.nTable.id !== 'table-bpb') { return true; }
      if (!$('#chkHideMatchBpb').is(':checked')) { return true; }
      return cocokPo[dataIndex] !== true;
  });

  $(document).ready(function () {
      $('.tanggal').datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true
      });

      bpbTable = $('#table-bpb').DataTable({
          paging: true,
          searching: true,
          info: true,
          ordering: false,
          autoWidth: false,
          columnDefs: [
              { targets: [0, 1, 4, 9, 10], className: 'text-center' },
              { targets: [2, 3], className: 'text-left' },
              { targets: [5, 6, 7, 8], className: 'text-right ftl-amt' },
              { targets: [0], className: 'text-center ftl-doc' },
              { targets: [10], orderable: false, searchable: false }
          ],
          language: {
              emptyTable: 'Click "Search BPB" to load data',
              /* Sakelar sembunyikan-yang-sesuai-PO menyala dari awal, jadi
                 daftar kosong paling sering berarti semuanya sudah sesuai PO -
                 bukan pencariannya yang gagal. */
              zeroRecords: 'No BPB left to show. If "Hide rows already matching PO" is ticked, every BPB found already matches its PO.',
              search: '',
              searchPlaceholder: 'Search BPB / PO / supplier...'
          }
      });

      /* Sakelar sembunyikan-yang-sesuai-PO di tabel BPB. Jumlah yang
         disembunyikan ikut ditampilkan supaya tidak terkesan datanya hilang. */
      function perbaruiJmlTersembunyi() {
          if (!bpbTable) { return; }
          const n = $('#chkHideMatchBpb').is(':checked')
              ? cocokPo.filter(function (v) { return v === true; }).length : 0;
          $('#ub-hidden-n').text(n ? '(' + n + ' hidden)' : '');
      }
      $('#chkHideMatchBpb').on('change', function () {
          if (bpbTable) { bpbTable.draw(); }
          perbaruiJmlTersembunyi();
      });

      $('#btnSearchBpb').on('click', function () {
          const nama_supp = $('#nama_supp').val();
          const start_date = toYmd($('#start_date').val());
          const end_date = toYmd($('#end_date').val());

          cocokPo = [];
          bpbTable.clear().draw();

          $.ajax({
              type: 'POST',
              url: 'cari_data_bpb_general_edit.php',
              data: { nama_supp: nama_supp, start_date: start_date, end_date: end_date },
              dataType: 'json',
              success: function (res) {
                  res.forEach(function (r) {
                      /* Penanda beda-dari-PO dulu ditempel sebagai tulisan merah
                         kecil di dalam kolom Action, dan seluruh barisnya diwarnai
                         hijau/merah. Sekarang jadi kolomnya sendiri supaya bisa
                         diurutkan & dicari, dan barisnya cukup diberi semburat. */
                      const baris = bpbTable.row.add([
                          escapeHtml(r.no_dok),
                          r.tgl_dok_fmt,
                          escapeHtml(r.supplier),
                          escapeHtml(r.no_po),
                          escapeHtml(r.curr || '-'),
                          formatMoney(r.qty),
                          formatMoney(r.dpp),
                          formatMoney(r.ppn),
                          formatMoney(r.total),
                          r.is_match
                              ? '<span class="ub-tag is-ok">matches PO</span>'
                              : '<span class="ub-tag is-beda">differs from PO</span>',
                          '<button type="button" class="ftl-mini is-biru btn-edit-items" '
                              + 'data-no-bpb="' + escapeHtml(r.no_dok) + '" '
                              + 'data-tgl-bpb="' + r.tgl_dok_fmt + '" '
                              + 'data-supplier="' + escapeHtml(r.supplier) + '" '
                              + 'data-no-po="' + escapeHtml(r.no_po) + '">'
                              + '<i class="fas fa-pen"></i> Edit Items</button>'
                      ]);
                      /* Dicatat per indeks baris, bukan dibaca ulang dari
                         tulisan di dalam sel - supaya penyaringnya tidak ikut
                         rusak kalau label pilnya suatu saat diubah. */
                      cocokPo[baris.index()] = !!r.is_match;
                      $(baris.node()).addClass(r.is_match ? 'ub-bpb-ok' : 'ub-bpb-diff');
                  });
                  bpbTable.draw();
                  perbaruiJmlTersembunyi();
              },
              error: function () {
                  Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load BPB data' });
              }
          });
      });

      // Open detail modal
      $('#table-bpb tbody').on('click', '.btn-edit-items', function () {
          const noBpb    = this.dataset.noBpb;
          const tglBpb   = this.dataset.tglBpb;
          const supplier = this.dataset.supplier;
          const noPo     = this.dataset.noPo;

          $('#modalDetail').data({ noBpb, tglBpb, supplier, noPo });
          $('#modalDetailLabel').text(noBpb);
          $('#modalBpbLabel').text(tglBpb + ' \u00b7 ' + (supplier || '-') + ' \u00b7 PO ' + (noPo || '-'));
          $('#table-modal-detail tbody').html('<tr><td colspan="12" class="ub-empty"><i class="fas fa-spinner fa-spin"></i></td></tr>');
          $('#btnFillPo').prop('disabled', true);
          $('#checkAllModal').prop('checked', false);
          hitungCentang();
          $('#modalDetail').modal('show');

          $.ajax({
              type: 'GET',
              url: 'get_detail_bpb_general_edit.php',
              data: { no_bpb: noBpb },
              dataType: 'json',
              success: function (res) {
                  const tbody = $('#table-modal-detail tbody');
                  tbody.empty();

                  if (!res.items || !res.items.length) {
                      tbody.html('<tr><td colspan="12" class="ub-empty">No items found</td></tr>');
                      return;
                  }

                  res.items.forEach(function (item) {
                      const currentPrice = item.price;
                      /* PPN lama diambil PER ITEM. Sebelumnya memakai res.ppn,
                         yang di endpoint hanya diisi dari baris pertama lalu
                         dipakai untuk semua baris - salah kalau satu BPB memuat
                         PPN yang berbeda-beda. */
                      const ppn      = parseFloat(item.ppn) || 0;
                      const hargaPo  = (item.po_price === null || item.po_price === undefined || item.po_price === '')
                                          ? null : parseFloat(item.po_price);
                      const ppnPo    = (item.po_ppn === null || item.po_ppn === undefined || item.po_ppn === '')
                                          ? null : parseFloat(item.po_ppn);
                      const row      = $('<tr>');
                      const isLocked = !!item.is_locked;

                      row.attr({
                          'data-id-det': item.id,
                          'data-no-ws': item.no_ws,
                          'data-id-jo': item.id_jo,
                          'data-id-item': item.id_item,
                          'data-desc-item': item.desc_item,
                          'data-qty': item.qty,
                          'data-unit': item.unit,
                          'data-curr': item.curr,
                          'data-price-old': currentPrice,
                          'data-ppn-old': ppn,
                          'data-po-price': hargaPo === null ? '' : hargaPo,
                          'data-po-ppn': ppnPo === null ? '' : ppnPo
                      });

                      /* Baris yang harganya sudah sama dgn PO tidak perlu diubah:
                         diredupkan & isiannya dikunci, persis seperti semula.
                         Bedanya kini bisa disembunyikan lewat sakelar di atas. */
                      if (isLocked) {
                          row.addClass('ub-row-sesuai').attr('title', 'Already matches PO - no edit needed');
                      }

                      const disabledAttr = isLocked ? ' disabled' : '';
                      const adaPo = (hargaPo !== null || ppnPo !== null);

                      row.html(
                          '<td class="text-center"><input type="checkbox" class="chk-modal-row"' + disabledAttr + '></td>'
                          + '<td class="text-center">' + escapeHtml(item.no_ws || '-') + '</td>'
                          + '<td class="text-left">' + escapeHtml(item.desc_item || item.id_item) + '</td>'
                          + '<td class="text-right">' + formatMoney(item.qty) + '</td>'
                          + '<td class="text-center">' + escapeHtml(item.unit || '-') + '</td>'
                          + '<td class="text-center">' + escapeHtml(item.curr || '-') + '</td>'
                          + '<td class="text-right">' + formatMoney(currentPrice, 2) + '</td>'
                          + '<td class="text-right ub-po">' + (hargaPo === null ? '-' : formatMoney(hargaPo, 2)) + '</td>'
                          + '<td><input type="number" step="0.0001" class="ub-inp price-new-input" value="' + currentPrice + '"' + disabledAttr + '></td>'
                          + '<td class="text-right">' + formatMoney(ppn) + '</td>'
                          + '<td><select class="ub-inp ppn-new-input"' + disabledAttr + '>'
                              + '<option value="11"' + (ppn === 11 ? ' selected' : '') + '>11%</option>'
                              + '<option value="0"' + (ppn !== 11 ? ' selected' : '') + '>Non PPN</option>'
                              + '</select></td>'
                          + '<td class="text-center">'
                              + ((adaPo && !isLocked)
                                    ? '<button type="button" class="ub-mini btn-po-row" title="Fill this row from the PO price">PO</button>'
                                    : '')
                          + '</td>'
                      );

                      tbody.append(row);
                  });

                  $('#btnFillPo').prop('disabled', false);
                  terapkanSembunyiSesuai();
                  hitungCentang();
              },
              error: function () {
                  $('#table-modal-detail tbody').html('<tr><td colspan="12" class="ub-empty text-danger">Failed to load items</td></tr>');
              }
          });
      });

      /* ---- Sembunyikan baris yang sudah sesuai PO ----
         Disembunyikan, BUKAN dibuang: sakelarnya bisa dimatikan lagi kalau
         memang ada yang perlu diubah. Baris yang sudah tercentang tidak
         pernah ikut disembunyikan supaya tidak lenyap dari pandangan. */
      function terapkanSembunyiSesuai() {
          const sembunyi = $('#chkHideMatch').is(':checked');
          $('#table-modal-detail tbody tr.ub-row-sesuai').each(function () {
              const tercentang = $(this).find('.chk-modal-row').is(':checked');
              $(this).toggleClass('is-sembunyi', sembunyi && !tercentang);
          });
      }
      $('#chkHideMatch').on('change', terapkanSembunyiSesuai);

      function hitungCentang() {
          const n = $('#table-modal-detail tbody .chk-modal-row:checked').length;
          $('#ub-mfoot-note').html(n ? '<b>' + n + '</b> row(s) ticked' : '&nbsp;');
      }
      $('#table-modal-detail tbody').on('change', '.chk-modal-row', hitungCentang);

      /* ---- Isi dari PO ----
         Harga & PPN menurut PO sudah dikirim get_detail_bpb_general_edit.php
         (po_price / po_ppn) sejak dulu, hanya tidak pernah dipakai - jadi
         angkanya diketik ulang padahal sistem sudah tahu. */
      function isiDariPo(tr) {
          const hp = tr.attr('data-po-price');
          const pp = tr.attr('data-po-ppn');
          if (hp !== '' && hp !== undefined) {
              tr.find('.price-new-input').val(hp).addClass('is-dari-po');
          }
          if (pp !== '' && pp !== undefined) {
              tr.find('.ppn-new-input').val(String(parseFloat(pp) === 11 ? 11 : 0)).addClass('is-dari-po');
          }
          tr.find('.chk-modal-row:not(:disabled)').prop('checked', true);
      }

      $('#table-modal-detail tbody').on('click', '.btn-po-row', function () {
          isiDariPo($(this).closest('tr'));
          hitungCentang();
      });

      $('#btnFillPo').on('click', function () {
          const baris = $('#table-modal-detail tbody tr:not(.is-sembunyi)').filter(function () {
              const t = $(this);
              if (t.find('.chk-modal-row').is(':disabled')) { return false; }
              return t.attr('data-po-price') !== '' || t.attr('data-po-ppn') !== '';
          });
          if (!baris.length) {
              Swal.fire({ icon: 'info', title: 'Nothing to fill', text: 'No PO price available for these rows.' });
              return;
          }
          baris.each(function () { isiDariPo($(this)); });
          hitungCentang();
      });

      // Check all rows in modal
      $('#checkAllModal').on('change', function () {
          $('#table-modal-detail tbody tr:not(.is-sembunyi) .chk-modal-row:not(:disabled)')
              .prop('checked', this.checked);
          hitungCentang();
      });

      // Add selected items from modal into the "Items to Update" table
      $('#btnAddSelected').on('click', function () {
          const modalData = $('#modalDetail').data();
          const checked = $('#table-modal-detail tbody .chk-modal-row:checked');

          if (checked.length === 0) {
              Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please select at least one item' });
              return;
          }

          checked.each(function () {
              const tr = $(this).closest('tr');
              const idDet = tr.data('id-det');

              const priceNew = parseFloat(tr.find('.price-new-input').val()) || 0;
              const ppnNew   = parseFloat(tr.find('.ppn-new-input').val()) || 0;

              const item = {
                  id_det: idDet,
                  no_bpb: modalData.noBpb,
                  tgl_bpb: modalData.tglBpb,
                  nama_supp: modalData.supplier,
                  no_po: modalData.noPo,
                  no_ws: tr.data('no-ws'),
                  id_jo: tr.data('id-jo'),
                  id_item: tr.data('id-item'),
                  desc_item: tr.data('desc-item'),
                  qty: tr.data('qty'),
                  unit: tr.data('unit'),
                  curr: tr.data('curr'),
                  price_old: tr.data('price-old'),
                  price_new: priceNew,
                  ppn_old: tr.data('ppn-old'),
                  ppn_new: ppnNew
              };

              addOrUpdateSelectedItem(item);
          });

          $('#modalDetail').modal('hide');
      });

      function addOrUpdateSelectedItem(item) {
          $('#row-empty-selected').remove();

          const rowKey = item.no_bpb + '|' + item.id_det;
          let existing = $('#table-selected tbody tr[data-row-key="' + rowKey + '"]');

          const rowHtml = ''
              + '<td class="text-center ftl-doc">' + escapeHtml(item.no_bpb) + '</td>'
              + '<td class="text-center">' + escapeHtml(item.no_ws || '-') + '</td>'
              + '<td class="text-left">' + escapeHtml(item.desc_item || item.id_item) + '</td>'
              + '<td class="text-right">' + formatMoney(item.qty) + '</td>'
              + '<td class="text-center">' + escapeHtml(item.unit || '-') + '</td>'
              + '<td class="text-center">' + escapeHtml(item.curr || '-') + '</td>'
              + '<td class="text-right">' + formatMoney(item.price_old, 4) + '</td>'
              /* nilai BARU ditandai - itulah inti dokumen ini */
              + '<td class="text-right ub-baru">' + formatMoney(item.price_new, 4) + '</td>'
              + '<td class="text-right">' + formatMoney(item.ppn_old) + '</td>'
              + '<td class="text-right ub-baru">' + formatMoney(item.ppn_new) + '</td>'
              + '<td class="text-center"><button type="button" class="ub-mini is-hapus btn-remove-item" title="Remove this row"><i class="fas fa-trash"></i></button></td>';

          if (existing.length) {
              existing.attr('data-item', JSON.stringify(item)).html(rowHtml);
          } else {
              const tr = $('<tr>').attr({ 'data-row-key': rowKey, 'data-item': JSON.stringify(item) }).html(rowHtml);
              $('#table-selected tbody').append(tr);
          }
          perbaruiHitungan();
      }

      function rapikanDaftarKosong() {
          if ($('#table-selected tbody tr[data-item]').length === 0) {
              $('#table-selected tbody').html(
                  '<tr id="row-empty-selected"><td colspan="11" class="ub-blankcell">'
                  + '<i class="fa fa-inbox" aria-hidden="true"></i>'
                  + '<b>Nothing to update yet</b>'
                  + '<span>Search a BPB above, then press <b>Edit Items</b> to correct its price or PPN.</span>'
                  + '</td></tr>');
          }
      }

      function perbaruiHitungan() {
          $('#ub-count').text($('#table-selected tbody tr[data-item]').length);
      }

      // Remove item from selected list
      $('#table-selected tbody').on('click', '.btn-remove-item', function () {
          $(this).closest('tr').remove();
          rapikanDaftarKosong();
          perbaruiHitungan();
      });

      /* Ringkasan singkat isi pengajuan - dipakai di kotak konfirmasi supaya
         yang disimpan terlihat dulu sebelum benar-benar disimpan. */
      function ringkasanSebelumSimpan(items) {
        const bpb = {};
        let tanpaUbah = 0;
        items.forEach(function (it) {
            bpb[it.no_bpb] = true;
            const hargaSama = parseFloat(it.price_new) === parseFloat(it.price_old);
            const ppnSama   = parseFloat(it.ppn_new) === parseFloat(it.ppn_old);
            if (hargaSama && ppnSama) { tanpaUbah++; }
        });
        return { jmlBpb: Object.keys(bpb).length, jmlBaris: items.length, tanpaUbah: tanpaUbah };
      }

      // Save the edit request
      $('#btnSave').on('click', function () {
          const deskripsi = $('#deskripsi').val().trim();
          const noPengajuan = $('#no_pengajuan').val();

          if (!deskripsi) {
              Swal.fire({
                  icon: 'warning',
                  title: 'Description is required',
                  text: 'Please describe why these prices are being corrected.'
              }).then(() => $('#deskripsi').focus());
              return;
          }

          const items = [];
          $('#table-selected tbody tr[data-item]').each(function () {
              items.push(JSON.parse($(this).attr('data-item')));
          });

          if (items.length === 0) {
              Swal.fire({
                  icon: 'warning',
                  title: 'Nothing to update',
                  text: 'Press "Edit Items" on a BPB above and add at least one row first.'
              });
              return;
          }

          const r = ringkasanSebelumSimpan(items);

          /* Kalau SEMUA barisnya tidak mengubah apa pun, menyimpan tidak ada
             gunanya - itu pasti keliru, jadi dihentikan. Kalau hanya sebagian,
             cukup diberitahu: bisa saja memang disengaja. */
          if (r.tanpaUbah === r.jmlBaris) {
              Swal.fire({
                  icon: 'warning',
                  title: 'No actual change',
                  html: 'All <b>' + r.jmlBaris + '</b> row(s) still have the same price and PPN as the BPB.<br>'
                      + 'Change a value, or press <b>Set to PO</b> inside the modal.'
              });
              return;
          }

          Swal.fire({
              icon: 'question',
              title: 'Save this request?',
              html: '<div style="text-align:left;font-size:13px;line-height:1.9">'
                  + '<div><b>Transaction No</b> : ' + escapeHtml(noPengajuan) + '</div>'
                  + '<div><b>BPB document</b> : ' + r.jmlBpb + '</div>'
                  + '<div><b>Rows to update</b> : ' + r.jmlBaris + '</div>'
                  + (r.tanpaUbah
                        ? '<div style="color:#9a4b06"><b>Note</b> : ' + r.tanpaUbah
                          + ' row(s) have no change and will be saved as-is.</div>'
                        : '')
                  + '<div style="margin-top:6px;color:#64748b">It will be saved with status <b>Draft</b>.</div>'
                  + '</div>',
              showCancelButton: true,
              confirmButtonColor: '#16a34a',
              cancelButtonColor: '#94a3b8',
              confirmButtonText: '<i class="fas fa-save"></i> Yes, save it',
              cancelButtonText: 'Cancel'
          }).then((result) => {
              if (!result.isConfirmed) return;

              /* Tombol dikunci selama permintaan berjalan supaya tidak terkirim
                 dua kali kalau diklik berulang. */
              const tombol = $('#btnSave');
              tombol.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

              $.ajax({
                  type: 'POST',
                  url: 'insert_update_bpb_general.php',
                  data: {
                      no_pengajuan: noPengajuan,
                      tgl_pengajuan: $('#tgl_pengajuan').val(),
                      deskripsi: $('#deskripsi').val(),
                      nama_supp: $('#nama_supp').val(),
                      created_by: '<?php echo $user ?>',
                      items: JSON.stringify(items)
                  },
                  dataType: 'json',
                  success: function (res) {
                      if (res.success) {
                          /* Tanpa timer & dgn tombol: nomornya perlu sempat
                             dibaca atau dicatat dulu. */
                          Swal.fire({
                              icon: 'success',
                              title: 'Request saved',
                              html: '<div style="font-size:13px;color:#475569">Transaction No</div>'
                                  + '<div style="font-size:19px;font-weight:700;color:#1e3a8a;margin:4px 0 10px;'
                                  + 'letter-spacing:.01em">' + escapeHtml(res.no_pengajuan) + '</div>'
                                  + '<div style="font-size:13px;color:#475569">'
                                  + '<b>' + r.jmlBaris + '</b> row(s) from <b>' + r.jmlBpb + '</b> BPB document(s)'
                                  + ' saved as <b>Draft</b>.</div>',
                              confirmButtonColor: '#1d4ed8',
                              confirmButtonText: 'OK'
                          }).then(() => {
                              /* jenis WAJIB dibawa: tanpa ini daftar selalu
                                 jatuh ke Fabric walau yang disimpan Accessories. */
                              window.location = 'update-bpb-general.php';
                          });
                      } else {
                          tombol.prop('disabled', false).html('<i class="fas fa-save"></i> Save Request');
                          Swal.fire({ icon: 'error', title: 'Failed to save', text: res.message || 'An error occurred' });
                      }
                  },
                  error: function () {
                      tombol.prop('disabled', false).html('<i class="fas fa-save"></i> Save Request');
                      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save edit request' });
                  }
              });
          });
      });
  });
</script>

</body>

</html>
