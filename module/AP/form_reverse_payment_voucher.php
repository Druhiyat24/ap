<?php include '../header.php' ?>
<?php include 'pv_data_functions.php'; ?>
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
    div.dataTables_wrapper .dataTables_paginate { float: right; margin-top: 10px; }
    div.dataTables_wrapper .dataTables_info { float: left; margin-top: 10px; }
    .select2-container { width: 100% !important; max-width: 100% !important; min-width: 100% !important; }
    .select2-container--bootstrap4 .select2-selection {
        height: calc(1.5em + .5rem + 2px) !important;
        padding: .25rem .5rem !important;
        font-size: .875rem !important;
        line-height: 1.5 !important;
        border-radius: .2rem !important;
    }
    .select2-container--bootstrap4 .select2-selection__rendered { line-height: 1.5 !important; font-size: 12px !important; }
    .select2-container--bootstrap4 .select2-results__option { font-size: 12px !important; padding: 4px 8px; }
</style>

<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0 text-white"><i class="fab fa-wpforms"></i> FORM REVERSE PAYMENT VOUCHER</h5>
    </div>

    <form id="form-header" method="post">
        <div class="form-row mt-2 p-3">

            <div class="col-md-3 mb-3">
                <label for="no_reverse"><b>No Reverse</b></label>
                <?php
                $sql = mysqli_query($conn2,"SELECT CONCAT('RVS/PV/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/', LPAD(COALESCE(MAX(CAST(RIGHT(rvs_number, 5) AS UNSIGNED)), 0) + 1, 5, '0')) nomor FROM ap_reverse_h WHERE rvs_number LIKE CONCAT('RVS/PV/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/%') AND rvs_number NOT LIKE 'RVS/PVL/%'");
                $row = mysqli_fetch_array($sql);
                $kodepay = $row['nomor'];
                echo '<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="no_reverse" name="no_reverse" value="'.htmlspecialchars($kodepay).'">';
                ?>
            </div>

            <div class="col-md-2 mb-3">
                <label for="tanggal"><b>Reverse Date</b></label>
                <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>

            <div class="col-md-7 mb-3"></div>

            <div class="col-md-5 mb-3">
                <label for="txt_criteria"><b>Search Criteria</b></label>
                <div class="input-group input-group-sm">
                    <input type="text" readonly class="form-control" style="font-size: 14px;" name="txt_criteria" id="txt_criteria"
                    value="<?php
                    if (!empty($_POST['h_type_pv']) || !empty($_POST['h_start_date'])) {
                        $parts = [];
                        if (!empty($_POST['h_type_pv']) && $_POST['h_type_pv'] !== 'ALL') $parts[] = htmlspecialchars($_POST['h_type_pv']);
                        if (!empty($_POST['h_nama_supp']) && $_POST['h_nama_supp'] !== 'ALL') $parts[] = htmlspecialchars($_POST['h_nama_supp']);
                        if (!empty($_POST['h_start_date']) && !empty($_POST['h_end_date'])) $parts[] = htmlspecialchars($_POST['h_start_date']) . ' s/d ' . htmlspecialchars($_POST['h_end_date']);
                        echo implode(' | ', $parts);
                    }
                    ?>">
                    <button type="button" class="btn btn-info btn-sm" id="btnSelectCriteria"><i class="fas fa-plus-square"></i> Select</button>
                </div>
            </div>

            <div class="col-md-7 mb-3"></div>

            <div class="col-md-5 mb-3">
                <label for="memo"><b>Description <i style="color: red;">*</i></b></label>
                <textarea style="font-size: 14px;" class="form-control form-control-sm" name="memo" id="memo" rows="3" required><?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?></textarea>
            </div>

            <!-- Hidden fields for criteria filter -->
            <input type="hidden" name="h_type_pv" id="h_type_pv" value="<?php echo isset($_POST['h_type_pv']) ? htmlspecialchars($_POST['h_type_pv']) : 'ALL'; ?>">
            <input type="hidden" name="h_nama_supp" id="h_nama_supp" value="<?php echo isset($_POST['h_nama_supp']) ? htmlspecialchars($_POST['h_nama_supp']) : 'ALL'; ?>">
            <input type="hidden" name="h_start_date" id="h_start_date" value="<?php echo isset($_POST['h_start_date']) ? htmlspecialchars($_POST['h_start_date']) : ''; ?>">
            <input type="hidden" name="h_end_date" id="h_end_date" value="<?php echo isset($_POST['h_end_date']) ? htmlspecialchars($_POST['h_end_date']) : ''; ?>">

        </div>
    </form>

    <div class="row px-3">
        <div class="col-md-12">
            <div class="table-responsive p-1">
                <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px; text-align:center;">
                    <thead>
                        <tr class="text-white" style="background-color: #1E3A8A;">
                            <th style="width:30px;">Check</th>
                            <th>Type</th>
                            <th>No PV</th>
                            <th>PV Date</th>
                            <th>Profit Center</th>
                            <th style="text-align:left;">Supplier</th>
                            <th>Curr</th>
                            <th style="text-align:right;">Total</th>
                            <th>Status</th>
                            <th style="text-align:left;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['h_start_date'])) {
                            $h_type_pv   = $_POST['h_type_pv'] ?? 'ALL';
                            $h_nama_supp = $_POST['h_nama_supp'] ?? 'ALL';
                            $h_start     = date("Y-m-d", strtotime($_POST['h_start_date']));
                            $h_end       = date("Y-m-d", strtotime($_POST['h_end_date']));

                            $filters = [
                                'nama_supp'   => $h_nama_supp,
                                'status'      => 'ALL',
                                'filter_date' => 'tgl_kbon',
                                'start_date'  => $h_start,
                                'end_date'    => $h_end,
                            ];

                            $rowsAll = [];
                            if ($h_type_pv === 'ALL' || $h_type_pv === 'Regular')     $rowsAll = array_merge($rowsAll, getDataRegular($conn1, $conn2, $filters, '1', '1', ''));
                            if ($h_type_pv === 'ALL' || $h_type_pv === 'Installment') $rowsAll = array_merge($rowsAll, getDataInstallment($conn1, $conn2, $filters, '1', '1', ''));
                            if ($h_type_pv === 'ALL' || $h_type_pv === 'DP')          $rowsAll = array_merge($rowsAll, getDataDp($conn1, $conn2, $filters, '1', '1', ''));
                            if ($h_type_pv === 'ALL' || $h_type_pv === 'CBD')         $rowsAll = array_merge($rowsAll, getDataCbd($conn1, $conn2, $filters, '1', '1', ''));
                            if ($h_type_pv === 'ALL' || $h_type_pv === 'Biaya')       $rowsAll = array_merge($rowsAll, getDataBiaya($conn2, $filters));

                            // Filter to only SECOND APPROVED / Approved
                            $rowsAll = array_filter($rowsAll, function($r) {
                                if ($r['type'] === 'Biaya')                      return $r['status'] === 'Approved';
                                if (in_array($r['type'], ['DP', 'CBD']))         return $r['status'] === 'Approved';
                                return $r['status'] === 'SECOND APPROVED';
                            });

                            // Build excluded set (already in active DRAFT or APPROVED reverse PV)
                            $alreadyInReverse = [];
                            $sqlExcl = mysqli_query($conn2,
                                "SELECT d.doc_number FROM ap_reverse_det d
                                 INNER JOIN ap_reverse_h rh ON rh.rvs_number = d.rvs_number
                                 WHERE (rh.status = 'DRAFT' OR rh.status = 'APPROVED')
                                 AND rh.rvs_number LIKE 'RVS/PV/%'
                                 AND rh.rvs_number NOT LIKE 'RVS/PVL/%'
                                 AND d.status = 'Y'"
                            );
                            if ($sqlExcl) {
                                while ($re = mysqli_fetch_assoc($sqlExcl)) {
                                    $alreadyInReverse[$re['doc_number']] = true;
                                }
                            }

                            foreach ($rowsAll as $r) {
                                $no_kbon  = $r['no_kbon'];
                                if (isset($alreadyInReverse[$no_kbon])) continue;

                                $type      = $r['type'];
                                $tgl_disp  = $r['tgl_kbon'] ?? '-';
                                $tgl_raw   = $r['tgl_kbon_raw'] ?? '';
                                $supp      = $r['nama_supp'] ?? '-';
                                $curr      = $r['curr'] ?? '-';
                                $total_fmt = $r['total'] ?? '0.00';
                                $total_raw = $r['total_raw'] ?? 0;
                                $pc        = $r['profit_center'] ?? '-';

                                $status_pl  = $r['status_pl'] ?? null;
                                $status_pvl = $r['status_pvl'] ?? null;

                                $plBlocked  = !empty($status_pl)  && strtoupper(trim($status_pl))  !== 'CANCEL';
                                $pvlWarning = !empty($status_pvl) && strtoupper(trim($status_pvl)) !== 'CANCEL';

                                if ($plBlocked) {
                                    $rowStyle = ' style="background:#fef9f9;"';
                                    $cbAttr   = ' disabled title="In Payment List — cannot reverse"';
                                    $statusBadge = '<span style="display:inline-block;background:#dc2626;color:#fff;border-radius:12px;padding:2px 8px;font-size:11px;font-weight:600;">In Payment List (cannot reverse)</span>';
                                } elseif ($pvlWarning) {
                                    $rowStyle = ' style="background:#fffbeb;"';
                                    $cbAttr   = '';
                                    $statusBadge = '<span style="display:inline-block;background:#d97706;color:#fff;border-radius:12px;padding:2px 8px;font-size:11px;font-weight:600;">&#9888; In PVL — will be cancelled on approval</span>';
                                } else {
                                    $rowStyle    = '';
                                    $cbAttr      = '';
                                    $statusBadge = '-';
                                }

                                echo '<tr'.$rowStyle.'>
                                    <td style="text-align:center;"><input type="checkbox" name="select[]" value=""'.$cbAttr.'></td>
                                    <td data-type="'.htmlspecialchars($type).'">'.htmlspecialchars($type).'</td>
                                    <td data-no_kbon="'.htmlspecialchars($no_kbon).'" data-tgl="'.htmlspecialchars($tgl_raw).'">'.htmlspecialchars($no_kbon).'</td>
                                    <td>'.htmlspecialchars($tgl_disp).'</td>
                                    <td>'.htmlspecialchars($pc).'</td>
                                    <td style="text-align:left;">'.htmlspecialchars($supp).'</td>
                                    <td>'.htmlspecialchars($curr).'</td>
                                    <td style="text-align:right;" data-total="'.htmlspecialchars($total_raw).'">'.htmlspecialchars($total_fmt).'</td>
                                    <td>'.$statusBadge.'</td>
                                    <td><textarea class="form-control form-control-sm" style="font-size: 12px;" rows="1" disabled></textarea></td>
                                </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <form id="form-simpan">
                <div class="form-row col mt-3 mb-3">
                    <div class="col-md-3">
                        <button type="button" style="border-radius:6px;" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>
                        <button type="button" style="border-radius:6px;" class="btn-danger btn-sm" onclick="location.href='reverse_payment_voucher.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

<!-- Modal Search Criteria -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow-lg rounded-3">
      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white">Search Payment Voucher</h4>
      </div>
      <div class="modal-body">
        <form id="modal-form" method="post">
          <div class="form-group">
            <label><b>Type PV</b></label>
            <select class="form-control form-control-sm select2bs4" id="modal_type_pv" name="modal_type_pv">
                <option value="ALL" <?php if(($_POST['h_type_pv'] ?? 'ALL') === 'ALL') echo 'selected'; ?>>ALL</option>
                <option value="Regular" <?php if(($_POST['h_type_pv'] ?? '') === 'Regular') echo 'selected'; ?>>Regular</option>
                <option value="Installment" <?php if(($_POST['h_type_pv'] ?? '') === 'Installment') echo 'selected'; ?>>Installment</option>
                <option value="DP" <?php if(($_POST['h_type_pv'] ?? '') === 'DP') echo 'selected'; ?>>DP</option>
                <option value="CBD" <?php if(($_POST['h_type_pv'] ?? '') === 'CBD') echo 'selected'; ?>>CBD</option>
                <option value="Biaya" <?php if(($_POST['h_type_pv'] ?? '') === 'Biaya') echo 'selected'; ?>>Biaya</option>
            </select>
          </div>
          <div class="form-group">
            <label><b>Supplier</b></label>
            <select class="form-control form-control-sm select2bs4" id="modal_nama_supp" name="modal_nama_supp">
                <option value="ALL">ALL</option>
                <?php
                $sqlSupp = mysqli_query($conn2, "SELECT supplier nama_supp FROM mastersupplier WHERE tipe_sup='S' ORDER BY nama_supp ASC");
                while ($rs = mysqli_fetch_assoc($sqlSupp)) {
                    $sel = (($_POST['h_nama_supp'] ?? '') === $rs['nama_supp']) ? 'selected' : '';
                    echo '<option value="'.htmlspecialchars($rs['nama_supp']).'" '.$sel.'>'.htmlspecialchars($rs['nama_supp']).'</option>';
                }
                ?>
            </select>
          </div>
          <div class="form-group">
            <label><b>PV Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="modal_start_date" name="modal_start_date"
              value="<?php echo !empty($_POST['h_start_date']) ? htmlspecialchars($_POST['h_start_date']) : date('d-m-Y'); ?>"
              placeholder="Start Date">
              <span class="mx-2">&nbsp;-&nbsp;</span>
              <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="modal_end_date" name="modal_end_date"
              value="<?php echo !empty($_POST['h_end_date']) ? htmlspecialchars($_POST['h_end_date']) : date('d-m-Y'); ?>"
              placeholder="End Date">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <button type="button" id="btnModalSearch" class="btn btn-warning"><i class="fa fa-search"></i> Search</button>
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
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
$(function() {
    $('.select2').select2();
    $('.select2bs4').select2({ theme: 'bootstrap4' });
});

$('#body-row .collapse').collapse('hide');
$('#collapse-icon').addClass('fa-angle-double-left');
$('[data-toggle=sidebar-colapse]').click(function() { SidebarCollapse(); });

function SidebarCollapse () {
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
$(document).ready(function() {
    $('#mytable').DataTable({
        paging: false,
        searching: true,
        info: false,
        scrollY: "350px",
        scrollCollapse: true,
        scrollX: true
    });
    $("[data-toggle=tooltip]").tooltip();
});
</script>

<script type="text/javascript">
$(document).ready(function () {
    $('.tanggal3').datepicker({ format: "dd-mm-yyyy", autoclose: true });
});
</script>

<script type="text/javascript">
$("#btnSelectCriteria").on("click", function () {
    $("#mymodal").modal("show");
});

$("#btnModalSearch").on("click", function () {
    var type_pv = $("#modal_type_pv").val();
    var supp    = $("#modal_nama_supp").val();
    var start   = $("#modal_start_date").val();
    var end     = $("#modal_end_date").val();
    if (!start || !end) {
        Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Please input date range.' });
        return;
    }
    $("#h_type_pv").val(type_pv);
    $("#h_nama_supp").val(supp);
    $("#h_start_date").val(start);
    $("#h_end_date").val(end);
    $("#mymodal").modal("hide");
    $("#form-header").submit();
});
</script>

<script type="text/javascript">
// Checkbox change: enable/disable remarks textarea
$(document).on('change', "input[type=checkbox]", function(){
    if ($(this).prop('disabled')) return;
    var tr = $(this).closest('tr');
    if ($(this).is(':checked')) {
        tr.find('td:last textarea').prop('disabled', false);
    } else {
        tr.find('td:last textarea').prop('disabled', true).val('');
    }
});
</script>

<script type="text/javascript">
$("#form-simpan").on("click", "#simpan", function () {
    var no_reverse   = $("#no_reverse").val();
    var reverse_date = $("#tanggal").val();
    var deskripsi    = $("#memo").val();
    var create_user  = '<?php echo addslashes($user); ?>';

    let checked = $("input[name='select[]']:checked:not(:disabled)");

    if (!deskripsi) {
        Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please input Description.' });
        return;
    }

    if (checked.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please check at least one Payment Voucher.' });
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'insert_reverse_pv_h.php',
        data: {
            'no_reverse'   : no_reverse,
            'reverse_date' : reverse_date,
            'deskripsi'    : deskripsi,
            'create_user'  : create_user
        },
        cache: false,
        success: function (response) {
            if (response.indexOf('Error') !== -1) {
                Swal.fire({ icon: 'error', title: 'Error', text: response });
                return;
            }

            let total = checked.length;
            let processed = 0;
            let errors = 0;

            checked.each(function () {
                var tr        = $(this).closest("tr");
                var type_pv   = tr.find('td:eq(1)').data('type') || tr.find('td:eq(1)').text().trim();
                var doc_number = tr.find('td:eq(2)').data('no_kbon') || tr.find('td:eq(2)').text().trim();
                var doc_date  = tr.find('td:eq(2)').data('tgl') || '';
                var nama_supp = tr.find('td:eq(5)').text().trim();
                var curr      = tr.find('td:eq(6)').text().trim();
                var total_val = tr.find('td:eq(7)').data('total') || 0;
                var row_deskripsi = tr.find('td:last textarea').val() || '-';

                $.ajax({
                    type: 'POST',
                    url: 'insert_reverse_pv_det.php',
                    data: {
                        'no_reverse'  : no_reverse,
                        'doc_number'  : doc_number,
                        'type_pv'     : type_pv,
                        'doc_date'    : doc_date,
                        'nama_supp'   : nama_supp,
                        'curr'        : curr,
                        'total'       : total_val,
                        'deskripsi'   : row_deskripsi,
                        'create_user' : create_user
                    },
                    cache: false,
                    success: function (res) {
                        if (res.indexOf('Error') !== -1) {
                            errors++;
                            console.error("Det error:", res);
                        }
                    },
                    error: function (xhr) { errors++; console.error(xhr.responseText); },
                    complete: function () {
                        processed++;
                        if (processed === total) {
                            if (errors === 0) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Data saved successfully!'
                                }).then(() => {
                                    window.location = 'reverse_payment_voucher.php';
                                });
                            } else {
                                Swal.fire({ icon: 'warning', title: 'Partial', text: (total - errors) + ' saved, ' + errors + ' failed.' });
                            }
                        }
                    }
                });
            });
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseText });
        }
    });
});
</script>

</body>
</html>
