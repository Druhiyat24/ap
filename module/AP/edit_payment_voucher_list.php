<?php include '../header.php' ?>
<?php
include 'pv_data_functions.php';

$pl_number = isset($_GET['pl_number']) ? trim($_GET['pl_number']) : '';
if (empty($pl_number)) {
    echo '<div class="alert alert-danger m-4">pl_number not found.</div>';
    exit;
}

$pl_esc = mysqli_real_escape_string($conn2, $pl_number);
$sqlH = mysqli_query($conn2, "SELECT pl_number, pl_date, deskripsi, status, created_by FROM pv_payment_voucher_list_h WHERE pl_number = '$pl_esc' LIMIT 1");
$plHeader = mysqli_fetch_assoc($sqlH);

if (!$plHeader) {
    echo '<div class="alert alert-danger m-4">Payment Voucher List not found.</div>';
    exit;
}

if ($plHeader['status'] !== 'Draft') {
    echo '<div class="alert alert-warning m-4">This Payment Voucher List has status <b>' . htmlspecialchars($plHeader['status']) . '</b> and cannot be edited. Only <b>Draft</b> documents can be edited.</div>';
    echo '<a href="payment-voucher-list.php" class="btn btn-secondary ml-4"><i class="fa fa-angle-double-left"></i> Back to Payment Voucher List</a>';
    exit;
}

// Current PVs in this PVL
$sqlDet = mysqli_query($conn2, "SELECT id, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center, status FROM pv_payment_voucher_list_det WHERE pl_number = '$pl_esc' AND status != 'Cancel' ORDER BY id ASC");
$currentPVs = [];
while ($r = mysqli_fetch_assoc($sqlDet)) {
    $currentPVs[] = $r;
}

// Section B search
$type_pv    = isset($_POST['h_type_pv']) ? $_POST['h_type_pv'] : null;
$nama_supp  = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : null;
$start_date = !empty($_POST['start_date']) ? date("Y-m-d", strtotime($_POST['start_date'])) : '';
$end_date   = !empty($_POST['end_date']) ? date("Y-m-d", strtotime($_POST['end_date'])) : '';

$searchRows = [];
if (!empty($type_pv)) {
    $filters = [
        'nama_supp'   => !empty($nama_supp) ? $nama_supp : 'ALL',
        'status'      => 'ALL',
        'filter_date' => 'tgl_kbon',
        'start_date'  => $start_date,
        'end_date'    => $end_date,
    ];

    $rowsAll = [];
    if ($type_pv === 'ALL' || $type_pv === 'Regular') {
        $rowsAll = array_merge($rowsAll, getDataRegular($conn1, $conn2, $filters, '1', '1', ''));
    }
    if ($type_pv === 'ALL' || $type_pv === 'Installment') {
        $rowsAll = array_merge($rowsAll, getDataInstallment($conn1, $conn2, $filters, '1', '1', ''));
    }
    if ($type_pv === 'ALL' || $type_pv === 'DP') {
        $rowsAll = array_merge($rowsAll, getDataDp($conn1, $conn2, $filters, '1', '1', ''));
    }
    if ($type_pv === 'ALL' || $type_pv === 'CBD') {
        $rowsAll = array_merge($rowsAll, getDataCbd($conn1, $conn2, $filters, '1', '1', ''));
    }
    if ($type_pv === 'ALL' || $type_pv === 'Biaya') {
        $rowsAll = array_merge($rowsAll, getDataBiaya($conn2, $filters));
    }
    if ($type_pv === 'ALL' || $type_pv === 'SaldoAwal') {
        $rowsAll = array_merge($rowsAll, getDataSaldoAwal($conn2, $filters));
    }

    // Eligibility for PVL: fully approved (SECOND APPROVED or Approved) AND status_pvl null/Cancel AND status_pl null/Cancel
    $rowsAll = array_filter($rowsAll, function ($r) {
        if (!in_array($r['status'], ['SECOND APPROVED', 'Approved'])) return false;
        $pvl = strtoupper(trim($r['status_pvl'] ?? ''));
        $pl  = strtoupper(trim($r['status_pl']  ?? ''));
        $pvlOk = ($pvl === '' || $pvl === 'CANCEL');
        $plOk  = ($pl  === '' || $pl  === 'CANCEL');
        return $pvlOk && $plOk;
    });

    // Build set of no_kbon already in this PVL (to exclude from Section B)
    $currentKbons = array_column($currentPVs, 'no_kbon');
    $currentKbonsSet = array_flip($currentKbons);

    // Exclude PVs already in other PVLs (non-cancelled)
    $alreadyListed = [];
    $sqlListed = mysqli_query($conn2, "SELECT no_kbon FROM pv_payment_voucher_list_det WHERE pl_number != '$pl_esc' AND status != 'Cancel'");
    while ($rr = mysqli_fetch_assoc($sqlListed)) {
        $alreadyListed[$rr['no_kbon']] = true;
    }

    $rowsAll = array_filter($rowsAll, function ($r) use ($alreadyListed, $currentKbonsSet) {
        return !isset($alreadyListed[$r['no_kbon']]) && !isset($currentKbonsSet[$r['no_kbon']]);
    });

    $searchRows = array_values($rowsAll);
}
?>

<style type="text/css">
    label { font-size: 14px; }
    input { font-size: 14px; }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-remove th {
        background: #7b1c1c;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-add th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .select2-container { width: 100% !important; max-width: 100% !important; min-width: 100% !important; }
    .select2-container--bootstrap4 .select2-selection { height: calc(1.5em + .5rem + 2px) !important; padding: .25rem .5rem !important; font-size: .875rem !important; line-height: 1.5 !important; border-radius: .2rem !important; }
    .select2-container--bootstrap4 .select2-selection__rendered { line-height: 1.5 !important; font-size: 12px !important; }
    .select2-container--bootstrap4 .select2-results__option { font-size: 12px !important; padding: 4px 8px; }

    [data-toggle="collapse"] .fa-chevron-up { transition: transform 0.2s ease; }
    [data-toggle="collapse"].collapsed .fa-chevron-up { transform: rotate(180deg); }
</style>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">

    <!-- Header Card -->
    <div class="card shadow mb-3" style="border: 1px solid #e2e8f0; border-radius: 10px;">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; color: #fff;">
            <span><i class="fa fa-edit mr-2"></i> EDIT PAYMENT VOUCHER LIST</span>
        </div>
        <div class="card-body p-3">
            <div class="form-row">
                <div class="col-md-3 mb-2">
                    <label><b>No Payment Voucher List</b></label>
                    <input type="text" class="form-control form-control-sm" readonly value="<?= htmlspecialchars($plHeader['pl_number']) ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Tanggal</b></label>
                    <input type="text" class="form-control form-control-sm" readonly value="<?= htmlspecialchars($plHeader['pl_date']) ?>">
                </div>
                <div class="col-md-4 mb-2">
                    <label><b>Keterangan</b></label>
                    <input type="text" class="form-control form-control-sm" readonly value="<?= htmlspecialchars($plHeader['deskripsi'] ?? '-') ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Status</b></label>
                    <input type="text" class="form-control form-control-sm" readonly value="<?= htmlspecialchars($plHeader['status']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Section A: Current PVs -->
    <div class="card shadow mb-3" style="border: 1px solid #e2e8f0; border-left: 5px solid #7b1c1c; border-radius: 10px;">
        <div class="card-header bg-white d-flex justify-content-between align-items-center"
             style="font-weight: bold; color: #7b1c1c; border-bottom: 1px solid #e2e8f0; cursor: pointer;"
             data-toggle="collapse" data-target="#section_a_body" aria-expanded="true" aria-controls="section_a_body">
            <span><i class="fa fa-list"></i> PVs in this Payment Voucher List</span>
            <i class="fa fa-chevron-up"></i>
        </div>
        <div class="collapse show" id="section_a_body">
        <div class="card-body p-2">
            <p class="text-muted" style="font-size: 12px; margin-bottom: 6px;">
                <i class="fa fa-info-circle"></i> Check a row to <b>remove</b> that PV from this Payment Voucher List.
            </p>
            <div class="table-responsive">
                <table id="tbl_current" class="table table-striped table-bordered" style="font-size: 13px; text-align: center; width: 100%;">
                    <thead class="table-remove">
                        <tr>
                            <th style="width:30px;">Remove</th>
                            <th style="width:80px;">Type</th>
                            <th style="width:140px;">No Payment Voucher</th>
                            <th style="width:90px;">PV Date</th>
                            <th style="width:100px;">Profit Center</th>
                            <th style="width:140px;">Supplier</th>
                            <th style="width:50px;">Curr</th>
                            <th style="width:110px;">Total</th>
                            <th style="width:160px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($currentPVs)): ?>
                        <tr><td colspan="9" class="text-center text-muted">No PVs in this Payment Voucher List.</td></tr>
                        <?php else: ?>
                        <?php foreach ($currentPVs as $cv): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="chk-remove"
                                    data-no_kbon="<?= htmlspecialchars($cv['no_kbon']) ?>"
                                    data-type_pv="<?= htmlspecialchars($cv['type_pv']) ?>">
                            </td>
                            <td class="td-type" value="<?= htmlspecialchars($cv['type_pv']) ?>"><?= htmlspecialchars($cv['type_pv']) ?></td>
                            <td class="td-nokbon text-left" value="<?= htmlspecialchars($cv['no_kbon']) ?>"><?= htmlspecialchars($cv['no_kbon']) ?></td>
                            <td class="td-tgl" value="<?= htmlspecialchars($cv['tgl_kbon']) ?>"><?= htmlspecialchars(!empty($cv['tgl_kbon']) ? date('d-M-Y', strtotime($cv['tgl_kbon'])) : '-') ?></td>
                            <td class="td-pc" value="<?= htmlspecialchars($cv['profit_center'] ?? '-') ?>"><?= htmlspecialchars($cv['profit_center'] ?? '-') ?></td>
                            <td class="td-supp text-left" value="<?= htmlspecialchars($cv['nama_supp']) ?>"><?= htmlspecialchars($cv['nama_supp']) ?></td>
                            <td class="td-curr" value="<?= htmlspecialchars($cv['curr']) ?>"><?= htmlspecialchars($cv['curr']) ?></td>
                            <td class="td-total text-right" value="<?= (float)$cv['total'] ?>"><?= number_format((float)$cv['total'], 2) ?></td>
                            <td><?= htmlspecialchars($cv['deskripsi'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    <!-- Section B: Add PVs -->
    <div class="card shadow mb-3" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
        <div class="card-header bg-white d-flex justify-content-between align-items-center"
             style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;"
             data-toggle="collapse" data-target="#section_b_body" aria-expanded="true" aria-controls="section_b_body">
            <span><i class="fa fa-plus-circle"></i> Add PV to Payment Voucher List</span>
            <i class="fa fa-chevron-up"></i>
        </div>
        <div class="collapse show" id="section_b_body">
        <div class="card-body p-2">

            <!-- Search form -->
            <form id="form-search" method="post">
                <input type="hidden" name="pl_number" value="<?= htmlspecialchars($pl_number) ?>">
                <div class="form-row mb-2">
                    <div class="col-md-3 mb-2">
                        <label><b>Type Document</b></label>
                        <select class="form-control form-control-sm select2bs4" name="h_type_pv" id="type_pv" data-dropup-auto="false">
                            <?php $selType = $_POST['h_type_pv'] ?? ''; ?>
                            <option value="" <?= $selType === '' ? 'selected' : '' ?>>-- Pilih Type --</option>
                            <option value="ALL" <?= $selType === 'ALL' ? 'selected' : '' ?>>ALL</option>
                            <option value="Regular" <?= $selType === 'Regular' ? 'selected' : '' ?>>Regular</option>
                            <option value="Installment" <?= $selType === 'Installment' ? 'selected' : '' ?>>Installment</option>
                            <option value="DP" <?= $selType === 'DP' ? 'selected' : '' ?>>DP</option>
                            <option value="CBD" <?= $selType === 'CBD' ? 'selected' : '' ?>>CBD</option>
                            <option value="Biaya" <?= $selType === 'Biaya' ? 'selected' : '' ?>>Biaya</option>
                            <option value="SaldoAwal" <?= $selType === 'SaldoAwal' ? 'selected' : '' ?>>Saldo Awal</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label><b>Supplier</b></label>
                        <select class="form-control form-control-sm selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" <?= (($_POST['nama_supp'] ?? 'ALL') === 'ALL') ? 'selected' : '' ?>>ALL</option>
                            <?php
                            $sqlSupp = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                            while ($rowSupp = mysqli_fetch_array($sqlSupp)) {
                                $ds = $rowSupp['Supplier'];
                                $isSel = (($_POST['nama_supp'] ?? '') === $ds) ? ' selected' : '';
                                echo '<option value="' . htmlspecialchars($ds) . '"' . $isSel . '>' . htmlspecialchars($ds) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label><b>From Date</b></label>
                        <input type="text" class="form-control form-control-sm tanggal3" name="start_date"
                               value="<?= !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date('d-m-Y') ?>" autocomplete="off">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label><b>To Date</b></label>
                        <input type="text" class="form-control form-control-sm tanggal3" name="end_date"
                               value="<?= !empty($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : date('d-m-Y') ?>" autocomplete="off">
                    </div>
                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-sm w-100">
                            <i class="fa fa-search"></i> Search PV
                        </button>
                    </div>
                </div>
            </form>

            <!-- Search results -->
            <div class="table-responsive">
                <table id="tbl_search" class="table table-striped table-bordered" style="font-size: 13px; text-align: center; width: 100%;">
                    <thead class="table-add">
                        <tr>
                            <th style="width:30px;"><input type="checkbox" id="chkAddAll"></th>
                            <th style="width:80px;">Type</th>
                            <th style="width:140px;">No Payment Voucher</th>
                            <th style="width:90px;">PV Date</th>
                            <th style="width:100px;">Profit Center</th>
                            <th style="width:140px;">Supplier</th>
                            <th style="width:50px;">Curr</th>
                            <th style="width:110px;">Total</th>
                            <th style="width:160px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($type_pv) && empty($searchRows)): ?>
                        <tr><td colspan="9" class="text-center text-muted">No PVs matching the search criteria.</td></tr>
                        <?php elseif (empty($type_pv)): ?>
                        <tr><td colspan="9" class="text-center text-muted">Fill in the search criteria and click <b>Search PV</b>.</td></tr>
                        <?php else: ?>
                        <?php foreach ($searchRows as $r): ?>
                        <?php
                            $totalRaw = (float) str_replace(',', '', $r['total']);
                            $pc = !empty($r['profit_center']) ? $r['profit_center'] : '-';
                            $tglRaw = $r['tgl_kbon_raw'] ?? '';
                        ?>
                        <tr>
                            <td><input type="checkbox" class="chk-add"></td>
                            <td class="td-type text-left" value="<?= htmlspecialchars($r['type']) ?>"><?= htmlspecialchars($r['type']) ?></td>
                            <td class="td-nokbon text-left" value="<?= htmlspecialchars($r['no_kbon']) ?>"><?= htmlspecialchars($r['no_kbon']) ?></td>
                            <td class="td-tgl" value="<?= htmlspecialchars($tglRaw) ?>"><?= htmlspecialchars($r['tgl_kbon']) ?></td>
                            <td class="td-pc" value="<?= htmlspecialchars($pc) ?>"><?= htmlspecialchars($pc) ?></td>
                            <td class="td-supp text-left" value="<?= htmlspecialchars($r['nama_supp']) ?>"><?= htmlspecialchars($r['nama_supp']) ?></td>
                            <td class="td-curr" value="<?= htmlspecialchars($r['curr']) ?>"><?= htmlspecialchars($r['curr']) ?></td>
                            <td class="td-total text-right" value="<?= $totalRaw ?>"><?= number_format($totalRaw, 2) ?></td>
                            <td><textarea class="form-control form-control-sm txt-keterangan" rows="1" placeholder="Optional" readonly style="resize:none;font-size:12px;"></textarea></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card shadow mb-4" style="border: 1px solid #e2e8f0; border-radius: 10px;">
        <div class="card-body p-3 d-flex align-items-center" style="gap: 10px;">
            <button type="button" id="btnSave" class="btn btn-primary btn-sm" style="border-radius: 6px;">
                <i class="fa fa-floppy-o"></i> Save Changes
            </button>
            <a href="payment-voucher-list.php" class="btn btn-danger btn-sm" style="border-radius: 6px;">
                <i class="fa fa-angle-double-left"></i> Back
            </a>
        </div>
    </div>

</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function() { SidebarCollapse(); });
  function SidebarCollapse() {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
      var SeparatorTitle = $('.sidebar-separator-title');
      if (SeparatorTitle.hasClass('d-flex')) { SeparatorTitle.removeClass('d-flex'); } else { SeparatorTitle.addClass('d-flex'); }
      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
  $(function() {
      $('.select2').select2();
      $('.select2bs4').select2({ theme: 'bootstrap4' });
      $('.selectpicker').selectpicker();
  });
  $(document).ready(function() {
      $('.tanggal3').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
  });
</script>

<script>
  $(document).ready(function() {
      $('#tbl_current').DataTable({
          paging: false, searching: true, info: false,
          scrollY: '250px', scrollCollapse: true, scrollX: true,
          order: []
      });
      $('#tbl_search').DataTable({
          paging: false, searching: true, info: false,
          scrollY: '300px', scrollCollapse: true, scrollX: true,
          order: []
      });
  });
</script>

<script>
  // Checkbox logic for Section B
  $(document).on('change', '.chk-add', function() {
      var td = $(this).closest('tr').find('textarea.txt-keterangan');
      td.prop('readonly', !this.checked);
  });

  $('#chkAddAll').on('change', function() {
      var checked = this.checked;
      $('.chk-add').prop('checked', checked);
      $('textarea.txt-keterangan').prop('readonly', !checked);
  });

  // Highlight Section A rows to-be-removed
  $(document).on('change', '.chk-remove', function() {
      if (this.checked) {
          $(this).closest('tr').addClass('table-danger');
      } else {
          $(this).closest('tr').removeClass('table-danger');
      }
  });

  var pl_number   = <?= json_encode($pl_number) ?>;
  var create_user = <?= json_encode($user) ?>;

  function doSaveEdit(pl_number, removes, adds, create_user) {
      $.ajax({
          type: 'POST',
          url: 'save_edit_payment_voucher_list.php',
          data: {
              pl_number:   pl_number,
              create_user: create_user,
              removes:     JSON.stringify(removes),
              adds:        JSON.stringify(adds)
          },
          cache: false,
          success: function(res) {
              if (String(res).indexOf('Error') === 0) {
                  Swal.fire({
                      icon: 'error',
                      title: 'Failed',
                      html: res + '<br><br>Click <b>Retry</b> to try again.',
                      showCancelButton: true,
                      confirmButtonText: 'Retry',
                      cancelButtonText: 'Close',
                      confirmButtonColor: '#1e3a8a'
                  }).then(function(r) {
                      if (r.isConfirmed) doSaveEdit(pl_number, removes, adds, create_user);
                  });
                  return;
              }
              Swal.fire({ icon: 'success', title: 'Success', text: 'Changes saved successfully.' })
              .then(function() { window.location.reload(); });
          },
          error: function(xhr) {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  html: (xhr.responseText || 'A network error occurred.') + '<br><br>Click <b>Retry</b>.',
                  showCancelButton: true,
                  confirmButtonText: 'Retry',
                  cancelButtonText: 'Close',
                  confirmButtonColor: '#1e3a8a'
              }).then(function(r) {
                  if (r.isConfirmed) doSaveEdit(pl_number, removes, adds, create_user);
              });
          }
      });
  }

  $('#btnSave').on('click', function() {
      var removes = [];
      $('input.chk-remove:checked').each(function() {
          removes.push({
              no_kbon:  $(this).data('no_kbon'),
              type_pv:  $(this).data('type_pv')
          });
      });

      var adds = [];
      $('input.chk-add:checked').each(function() {
          var tr = $(this).closest('tr');
          adds.push({
              type_pv:       tr.find('td.td-type').attr('value'),
              no_kbon:       tr.find('td.td-nokbon').attr('value'),
              tgl_kbon:      tr.find('td.td-tgl').attr('value'),
              profit_center: tr.find('td.td-pc').attr('value'),
              nama_supp:     tr.find('td.td-supp').attr('value'),
              curr:          tr.find('td.td-curr').attr('value'),
              total:         tr.find('td.td-total').attr('value'),
              deskripsi:     tr.find('textarea.txt-keterangan').val()
          });
      });

      if (removes.length === 0 && adds.length === 0) {
          Swal.fire({ icon: 'warning', title: 'No Changes', text: 'Select PVs to remove or add new PVs.' });
          return;
      }

      var msgParts = [];
      if (removes.length > 0) msgParts.push('remove <b>' + removes.length + ' PV(s)</b>');
      if (adds.length > 0)    msgParts.push('add <b>' + adds.length + ' PV(s)</b>');
      var msg = 'You are about to ' + msgParts.join(' and ') + '. Continue?';

      Swal.fire({
          icon: 'question',
          title: 'Confirm Changes',
          html: msg,
          showCancelButton: true,
          confirmButtonText: 'Yes, Save',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#1e3a8a'
      }).then(function(result) {
          if (!result.isConfirmed) return;
          doSaveEdit(pl_number, removes, adds, create_user);
      });
  });
</script>

</body>
</html>
