<?php include '../header.php' ?>
<?php
// ============================================================================
// DOCUMENT HANDOVER — gabungan "Invoice Received" + "BPB Transferred".
// 1 halaman, DataTable AJAX (ajx_document_handover.php). "Type Document" ada di
// baris filter bawah (Invoice / BPB & SJ). Ganti type -> reload halaman supaya
// tombol approve/transfer + opsi Status/Description ikut ganti sesuai type.
// Halaman lama (invoice_received.php / bpb_received.php) TIDAK diubah.
// SJ (= "BPB keluar") menyusul ditambahkan ke type BPB & SJ.
// ============================================================================
$type = (isset($_GET['type']) && $_GET['type'] === 'bpb') ? 'bpb' : 'invoice';
// Opsi dropdown Status & Description berbeda per type (ikut halaman aslinya).
$statFlag = ($type === 'bpb') ? 'bpb'     : 'Y';
$decFlag  = ($type === 'bpb') ? 'dec_bpb' : 'dec';
?>
<style>
  #dh-wrap > .card{ border-radius:14px; }
  .dh-filter label{ margin-bottom:3px; font-size:13px; }
  /* Selar tinggi input & bootstrap-select biar sebaris rapi */
  .dh-filter .form-control{ height:38px; }
  .dh-filter .bootstrap-select > .dropdown-toggle{ height:38px; padding-top:6px; }
  /* Toolbar tombol approve/transfer: bungkus rapi dengan jarak konsisten */
  #dh-toolbar{ display:flex; flex-wrap:wrap; gap:5px; align-items:center; }
  #dh-toolbar .btn-xs{ margin:0; padding:3px 8px; font-size:11px; border-radius:6px; box-shadow:0 1px 3px rgba(30,58,138,.14); transition:transform .12s ease, box-shadow .15s ease; }
  #dh-toolbar .btn-xs:hover{ transform:translateY(-1px); box-shadow:0 4px 11px rgba(30,58,138,.22); }
  #dh-toolbar .btn-xs .fa{ font-size:10px; }
  #dh-toolbar .badge{ margin-left:3px; font-size:9px !important; }
  /* ===== Tabel BIRU (mengikuti kontrabon_new) ===== */
  #datatable{ border-collapse:separate !important; border-spacing:0; }
  #datatable thead th{ background:#1E3A8A; color:#fff; font-weight:600; font-size:12px; letter-spacing:.02em; padding:12px 14px; border:0 !important; white-space:nowrap; vertical-align:middle; text-align:center; }
  #datatable tbody td{ padding:12px 14px; border:0 !important; border-bottom:1px solid #eef2f7 !important; vertical-align:middle; font-size:13px; color:#334155; }
  #datatable tbody tr{ transition:background .12s; }
  #datatable tbody tr:hover{ background:#f5f8ff; }
  #datatable tbody tr:last-child td{ border-bottom:0 !important; }
  #datatable td.dh-detail{ cursor:pointer; color:#1e3a8a; font-weight:700; letter-spacing:.02em; }
  #datatable td.dh-detail:hover{ text-decoration:underline; }

  /* ===== Modal detail (modern) ===== */
  #mymodalftrdp .modal-dialog{ max-width:760px; }
  #mymodalftrdp .modal-content{ border:0; border-radius:16px; overflow:hidden; box-shadow:0 24px 70px rgba(15,23,42,.28); }
  #mymodalftrdp .modal-header{ background:linear-gradient(135deg,#172554,#2563eb); color:#fff; border:0; padding:15px 22px; align-items:center; }
  #mymodalftrdp .modal-title{ font-weight:700; font-size:17px; letter-spacing:.02em; }
  #mymodalftrdp .modal-header .close{ color:#fff; opacity:.9; text-shadow:none; font-size:20px; }
  #mymodalftrdp .modal-body{ padding:20px 22px 24px; }
  .dhm-info{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:2px 22px; background:#f8fafc; border:1px solid #e8edf5; border-radius:12px; padding:12px 18px; margin-bottom:16px; }
  .dhm-info > div{ display:flex; flex-direction:column; padding:6px 0; min-width:0; }
  .dhm-info .lbl{ font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; margin-bottom:2px; }
  .dhm-info .val{ font-size:13.5px; font-weight:600; color:#0f172a; overflow-wrap:anywhere; }
  @media (max-width:600px){ .dhm-info{ grid-template-columns:repeat(2,minmax(0,1fr)); } }
  /* Tabel detail yang di-inject (ajax_suppinv.php / ajax_trfbpb.php) — hanya di dalam modal */
  .dhm-details table#mytdmodal{ width:100%; border-collapse:separate !important; border-spacing:0; font-size:12.5px; margin-bottom:10px; overflow:hidden; border-radius:10px; box-shadow:0 1px 4px rgba(15,23,42,.08); }
  .dhm-details table#mytdmodal thead th{ background:#1E3A8A !important; color:#fff; font-weight:600; padding:10px 12px; border:0 !important; text-align:center; }
  .dhm-details table#mytdmodal thead th:first-child{ border-top-left-radius:10px; }
  .dhm-details table#mytdmodal thead th:last-child{ border-top-right-radius:10px; }
  .dhm-details table#mytdmodal tbody td{ padding:9px 12px; border:0 !important; border-bottom:1px solid #eef2f7 !important; }
  .dhm-details table#mytdmodal.table-striped tbody tr:nth-of-type(odd){ background:#fbfcfe; }
  .dhm-details table#mytdmodal tbody tr:hover{ background:#f5f8ff; }
  .dhm-details table#mytdmodal tbody tr:last-child td{ border-bottom:0 !important; }
  /* Baris Grand Total (tabel kedua tanpa id) */
  .dhm-details table:not(#mytdmodal){ width:100%; margin-top:2px; }
  .dhm-details table:not(#mytdmodal) th{ font-size:13px; color:#334155; padding:6px 12px; font-weight:700; }
  .dhm-details table:not(#mytdmodal) th:last-child{ color:#1e3a8a; font-weight:800; font-size:15px; }
</style>

<div class="container-fluid mt-4 p-4" id="dh-wrap">

  <!-- ===== FILTER CARD ===== -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3 d-flex align-items-center" style="background: linear-gradient(90deg,#191970,#1e90ff);">
      <h5 class="mb-0"><i class="fa fa-exchange" aria-hidden="true"></i> DOCUMENT HANDOVER</h5>
    </div>
    <div class="card-body p-3">

      <!-- ===== FILTER (Type Document ada di sini, paling kiri) ===== -->
      <form id="form-data" onsubmit="return false;">
        <!-- Baris 1: Type / Supplier / Status / Description -->
        <div class="form-row dh-filter">
          <div class="col-md-2 mb-2">
            <label for="dh-type"><b>Type Document</b></label>
            <select class="form-control selectpicker" name="type" id="dh-type" data-width="100%">
              <option value="invoice"<?= $type === 'invoice' ? ' selected' : '' ?>>Invoice</option>
              <option value="bpb"<?= $type === 'bpb' ? ' selected' : '' ?>>BPB &amp; SJ</option>
            </select>
          </div>

          

          <div class="col-md-2 mb-2">
            <label for="status"><b>Status</b></label>
            <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
              <option value="ALL" selected>ALL</option>
              <?php
              $sql = mysqli_query($conn1, "select distinct(nama_status) nama_status from ir_status where status = '$statFlag' order by id ASC");
              while ($row = mysqli_fetch_array($sql)) {
                  echo '<option value="' . htmlspecialchars($row['nama_status'], ENT_QUOTES) . '">' . htmlspecialchars($row['nama_status'], ENT_QUOTES) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-md-3 mb-2">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" title="ALL">
              <option value="ALL" selected>ALL</option>
              <?php
              $sql = mysqli_query($conn1, "SELECT DISTINCT(Supplier) FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
              while ($row = mysqli_fetch_array($sql)) {
                  echo '<option value="' . htmlspecialchars($row['Supplier'], ENT_QUOTES) . '">' . htmlspecialchars($row['Supplier'], ENT_QUOTES) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-md-3 mb-2">
            <label for="keterangan"><b>Description</b></label>
            <select class="form-control selectpicker" name="keterangan" id="keterangan" data-dropup-auto="false" data-live-search="true">
              <option value="ALL" selected>ALL</option>
              <?php
              $sql = mysqli_query($conn1, "select distinct(nama_status) nama_status from ir_status where status = '$decFlag' order by id ASC");
              while ($row = mysqli_fetch_array($sql)) {
                  echo '<option value="' . htmlspecialchars($row['nama_status'], ENT_QUOTES) . '">' . htmlspecialchars($row['nama_status'], ENT_QUOTES) . '</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <!-- Baris 2: From / To / tombol -->
        <div class="form-row dh-filter align-items-end">
          <div class="col-md-2 mb-2">
            <label for="start_date"><b>From</b></label>
            <input type="text" class="form-control tanggal" id="start_date" name="start_date" value="<?= date('d-m-Y') ?>" placeholder="Start Date" autocomplete="off" style="height:38px;font-size:13px;">
          </div>

          <div class="col-md-2 mb-2">
            <label for="end_date"><b>To</b></label>
            <input type="text" class="form-control tanggal" id="end_date" name="end_date" value="<?= date('d-m-Y') ?>" placeholder="End Date" autocomplete="off" style="height:38px;font-size:13px;">
          </div>

          <div class="col-md-6 mb-2">
            <button type="button" id="submit" class="btn btn-sm text-white mr-2" style="background-color:rgb(46,139,87);height:38px;"><i class="fa fa-search"></i> Search</button>
            <button type="button" id="reset" class="btn btn-sm text-white" style="background-color:rgb(250,69,1);height:38px;"><i class="fa fa-repeat"></i> Reset</button>
          </div>
        </div>
      </form>

      <hr class="my-2">


      <!-- ===== TOOLBAR approve/transfer (per type) ===== -->
      <div id="dh-toolbar" class="mb-2">
        <?php
        if ($type === 'invoice') {
            // Role gating id1..id8 == 67..74 (identik invoice_received.php)
            $menus = [
                1 => 'Document Handover - Create Invoice', 2 => 'Document Handover - Transfer Invoice Finance To Accounting', 3 => 'Document Handover - Accept Invoice Accounting',
                4 => 'Document Handover - Transfer Invoice Accounting To Purchasing', 5 => 'Document Handover - Accept Invoice Purchasing', 6 => 'Document Handover - Transfer Invoice Purchasing To Finance',
                7 => 'Document Handover - Accept Invoice Finance', 8 => 'Document Handover - Reverse Invoice',
            ];
            $ids = [];
            foreach ($menus as $k => $m) {
                $q = mysqli_query($conn2, "select menurole.id id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = '" . mysqli_real_escape_string($conn2, $m) . "'");
                $r = mysqli_fetch_array($q);
                $ids[$k] = isset($r['id']) ? $r['id'] : 0;
            }
            $cnt = function ($sql) use ($conn2) { $r = mysqli_fetch_array(mysqli_query($conn2, $sql)); return $r ? $r[0] : 0; };
            $c_recv = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status = 'Received'");
            $c_acc  = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status = 'Post Fin To Acc'");
            $c_rec2 = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status IN ('Received','Accepted Acc')");
            $c_pch  = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status = 'Post Acc To Pch'");
            $c_apch = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status = 'Accepted Pch'");
            $c_fin  = $cnt("select COUNT(doc_number) from ir_invoice_supp_h where status = 'Post Pch To Fin'");
            $bdg = function ($n) { return ' <span class="badge bg-danger text-white" style="font-size:10px">' . $n . '</span>'; };
            if ($ids[1] == '67') echo '<button id="btncreate" type="button" class="btn-primary btn-xs" data-href="create_invoice_received.php"><span class="fa fa-pencil-square-o"></span> Invoice received</button> ';
            if ($ids[2] == '68') echo '<button type="button" class="btn-info btn-xs" data-href="post_inv_fintoacc.php"><span class="fa fa-paper-plane"></span> Post Fin-Acc' . $bdg($c_recv) . '</button> ';
            if ($ids[3] == '69') echo '<button type="button" class="btn-success btn-xs" data-href="form_approve_acc.php"><span class="fa fa-thumbs-up"></span> Accept Acc' . $bdg($c_acc) . '</button> ';
            if ($ids[4] == '70') echo '<button type="button" class="btn-info btn-xs" data-href="post_inv_acctopch.php"><span class="fa fa-paper-plane"></span> Post Acc-Pch' . $bdg($c_rec2) . '</button> ';
            if ($ids[5] == '71') echo '<button type="button" class="btn-success btn-xs" data-href="form_approve_pch.php"><span class="fa fa-thumbs-up"></span> Accept Pch' . $bdg($c_pch) . '</button> ';
            if ($ids[6] == '72') echo '<button type="button" class="btn-info btn-xs" data-href="post_inv_pchtofin.php"><span class="fa fa-paper-plane"></span> Post Pch-Fin' . $bdg($c_apch) . '</button> ';
            if ($ids[7] == '73') echo '<button type="button" class="btn-success btn-xs" data-href="form_approve_fin.php"><span class="fa fa-thumbs-up"></span> Accept Fin' . $bdg($c_fin) . '</button> ';
            if ($ids[8] == '74') echo '<button type="button" class="btn-warning btn-xs" data-href="reverse_transfer_inv.php"><span class="fa fa-undo"></span> Reverse</button> ';
        } else {
            // BPB: 1 tombol Accept BPB (id7 == 76) (identik bpb_received.php)
            $q = mysqli_query($conn2, "select menurole.id id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Document Handover - Accept BPB Warehouse To Accounting'");
            $r = mysqli_fetch_array($q);
            $id7 = isset($r['id']) ? $r['id'] : 0;
            $rf = mysqli_fetch_array(mysqli_query($conn2, "select count(no_transfer) c from (select no_transfer, CASE WHEN s_post > 0 THEN 'Transfer' WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel' WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved' WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial' END as status from (select a.no_transfer,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_transfer from ir_trans_bpb GROUP BY no_transfer) a left join (select no_transfer,COUNT(status) s_post from ir_trans_bpb where status = 'Transfer' GROUP BY no_transfer) b on b.no_transfer = a.no_transfer LEFT JOIN (select no_transfer,COUNT(status) s_cancel from ir_trans_bpb where status = 'Cancel' GROUP BY no_transfer) c on c.no_transfer = a.no_transfer LEFT JOIN (select no_transfer,COUNT(status) s_approved from ir_trans_bpb where status = 'Approved' GROUP BY no_transfer) d on d.no_transfer = a.no_transfer) a) a where status = 'Transfer'"));
            $c_bpb = $rf ? $rf['c'] : 0;
            if ($id7 == '76') echo '<button type="button" class="btn-success btn-xs" data-href="form_approve_bpb.php"><span class="fa fa-thumbs-up"></span> Accept BPB <span class="badge bg-danger text-white" style="font-size:10px">' . $c_bpb . '</span></button>';
        }
        ?>
      </div>

    </div>
  </div>

  <!-- ===== TABLE CARD ===== -->
  <div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
        <table id="datatable" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th><?= $type === 'bpb' ? 'No Transfer' : 'No Document' ?></th>
              <th><?= $type === 'bpb' ? 'Tgl Transfer' : 'Document Date' ?></th>
              <th>Supplier</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th><?= $type === 'bpb' ? 'Keterangan' : 'Description' ?></th>
              <th><?= $type === 'bpb' ? 'User Create' : 'Create User' ?></th>
              <?php if ($type === 'invoice'): ?><th>Action</th><?php endif; ?>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ===== MODAL DETAIL ===== -->
<div class="modal fade" id="mymodalftrdp" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o mr-2" aria-hidden="true"></i><span id="txt_dp"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <div class="dhm-info">
          <div>
            <span class="lbl"><?= $type === 'bpb' ? 'Transfer Date' : 'Receive Date' ?></span>
            <span class="val" id="txt_tgl_dp">-</span>
          </div>
          <div>
            <span class="lbl">Supplier</span>
            <span class="val" id="txt_nama_supp">-</span>
          </div>
          <div id="wrap_no_reff">
            <span class="lbl">No Reff</span>
            <span class="val" id="txt_no_reff">-</span>
          </div>
        </div>
        <div id="details" class="dhm-details"></div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>

<script>
  var DH_TYPE = <?= json_encode($type) ?>;
  var DH_DETAIL_URL = DH_TYPE === 'bpb' ? 'ajax_trfbpb.php' : 'ajax_suppinv.php';

  $(function () {
    $('.selectpicker').selectpicker();
    $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });

    var cols = [
      { data: 'no_doc', className: 'dh-detail' },
      { data: null, render: function (row, type) { return (type === 'sort' || type === 'type') ? row.tgl_raw : row.tgl; } },
      { data: 'supplier' },
      { data: 'total', className: 'text-right' },
      { data: 'status' },
      { data: 'keterangan' },
      { data: 'user' }
    ];
    if (DH_TYPE === 'invoice') cols.push({ data: 'action', orderable: false, className: 'text-center' });

    var table = $('#datatable').DataTable({
      ajax: {
        url: 'ajx_document_handover.php',
        type: 'POST',
        data: function (d) {
          d.type       = DH_TYPE;
          d.nama_supp  = $('#nama_supp').val();
          d.status     = $('#status').val();
          d.keterangan = $('#keterangan').val();
          d.start_date = $('#start_date').val();
          d.end_date   = $('#end_date').val();
        },
        dataSrc: 'data'
      },
      columns: cols,
      order: [[1, 'desc']],
      language: { emptyTable: 'No data for the selected filter.' }
    });

    // Search -> reload data
    $('#submit').on('click', function () { table.ajax.reload(); });

    // Reset -> kembali ke default type & filter
    $('#reset').on('click', function () {
      window.location.href = 'document_handover.php?type=' + encodeURIComponent(DH_TYPE);
    });

    // Ganti Type Document -> reload halaman (tombol & opsi ikut ganti)
    $('#dh-type').on('change', function () {
      window.location.href = 'document_handover.php?type=' + encodeURIComponent(this.value);
    });

    // Row-click (kolom No Document) -> modal detail
    $('#datatable tbody').on('click', 'td.dh-detail', function () {
      var d = table.row($(this).closest('tr')).data();
      if (!d) return;
      $('#mymodalftrdp').modal('show');
      $('#txt_dp').text(d.no_doc);
      $('#txt_tgl_dp').text(d.tgl);
      $('#txt_nama_supp').text(d.supplier);
      if (DH_TYPE === 'invoice') {
        $('#wrap_no_reff').show();
        $('#txt_no_reff').text(d.no_reff);
      } else {
        $('#wrap_no_reff').hide();
      }
      $('#details').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
      $.ajax({
        type: 'post',
        url: DH_DETAIL_URL,
        data: { no_document: d.no_doc },
        success: function (data) { $('#details').html(data); }
      });
    });

    // Tombol toolbar approve/transfer -> redirect
    $('#dh-toolbar').on('click', 'button[data-href]', function () {
      location.href = $(this).data('href');
    });
  });
</script>
</body>
</html>
