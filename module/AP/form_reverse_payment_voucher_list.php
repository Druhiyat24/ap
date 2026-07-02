<?php include '../header.php' ?>
<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
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

    .select2-container {
      width: 100% !important;
      max-width: 100% !important;
      min-width: 100% !important;
  }

  .select2-container--bootstrap4 .select2-selection {
      height: calc(1.5em + .5rem + 2px) !important;
      padding: .25rem .5rem !important;
      font-size: .875rem !important;
      line-height: 1.5 !important;
      border-radius: .2rem !important;
  }

  .select2-container--bootstrap4 .select2-selection__rendered {
      line-height: 1.5 !important;
      font-size: 12px !important;
  }

  .select2-container--bootstrap4 .select2-results__option {
      font-size: 12px !important;
      padding: 4px 8px;
  }

  td.td-pvl-number {
      cursor: pointer;
      color: #1d4ed8;
  }

</style>

<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0 text-white"><i class="fab fa-wpforms"></i> FORM REVERSE PAYMENT VOUCHER LIST</h5>
    </div>

    <form id="form-header" method="post">
        <div class="form-row mt-2">

            <div class="col-md-3 mb-3">
                <label for="no_reverse"><b>No Reverse</b></label>
                <?php
                $sql = mysqli_query($conn2,"SELECT CONCAT('RVS/PVL/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/', LPAD(COALESCE(MAX(CAST(RIGHT(rvs_number, 5) AS UNSIGNED)), 0) + 1, 5, '0')) nomor FROM ap_reverse_h WHERE rvs_number LIKE CONCAT('RVS/PVL/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/%')");
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
                    if (!empty($_POST['h_start_date']) && !empty($_POST['h_end_date'])) {
                        echo htmlspecialchars($_POST['h_start_date']) . ' s/d ' . htmlspecialchars($_POST['h_end_date']);
                    }
                    ?>">
                    <button type="button" class="btn btn-info btn-sm" id="btnSelectCriteria"><i class="fas fa-plus-square"></i> Select</button>
                </div>
            </div>
            <div class="col-md-7 mb-3"></div>

            <div class="col-md-5 mb-3">
                <label for="memo"><b>Descriptions <i style="color: red;">*</i></b></label>
                <textarea style="font-size: 14px;" class="form-control form-control-sm" name="memo" id="memo" rows="3"><?php echo isset($_POST['memo']) ? htmlspecialchars($_POST['memo']) : ''; ?></textarea>
            </div>

            <!-- Hidden fields for date filter -->
            <input type="hidden" name="h_start_date" id="h_start_date" value="<?php echo isset($_POST['h_start_date']) ? htmlspecialchars($_POST['h_start_date']) : ''; ?>">
            <input type="hidden" name="h_end_date" id="h_end_date" value="<?php echo isset($_POST['h_end_date']) ? htmlspecialchars($_POST['h_end_date']) : ''; ?>">

        </div>
    </form>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive p-1">
                <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px; text-align:center;">
                    <thead>
                        <tr class="text-white" style="background-color: #1E3A8A;">
                            <th style="width:10px;">Check</th>
                            <th>No PVL</th>
                            <th>PVL Date</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start_date = '';
                        $end_date = '';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            if (!empty($_POST['h_start_date'])) {
                                $start_date = date("Y-m-d", strtotime($_POST['h_start_date']));
                            }
                            if (!empty($_POST['h_end_date'])) {
                                $end_date = date("Y-m-d", strtotime($_POST['h_end_date']));
                            }
                        }

                        if (!empty($start_date) && !empty($end_date)) {
                            $sql = mysqli_query($conn2,"SELECT h.pl_number, h.pl_date, h.deskripsi, h.status, h.created_by,
                                (SELECT COUNT(*) FROM pv_payment_voucher_list_det det
                                 WHERE det.pl_number = h.pl_number AND det.status != 'Cancel'
                                 AND (
                                   EXISTS (
                                     SELECT 1 FROM b_bankout_det bdet
                                     INNER JOIN b_bankout_h bh ON bh.no_bankout = bdet.no_bankout
                                     WHERE bdet.no_reff = det.no_kbon AND bh.status != 'Cancel'
                                   )
                                   OR EXISTS (
                                     SELECT 1 FROM c_petty_cashout_det pdet
                                     INNER JOIN c_petty_cashout_h ph ON ph.no_pco = pdet.no_pco
                                     WHERE pdet.no_reff = det.no_kbon AND ph.status != 'Cancel'
                                   )
                                   OR EXISTS (
                                     SELECT 1 FROM b_bankout_none bnone
                                     INNER JOIN b_bankout_h bh ON bh.no_bankout = bnone.no_bankout
                                     WHERE bnone.reff_doc = det.no_kbon AND bh.status != 'Cancel'
                                   )
                                 )
                                ) AS paid_count,
                                (SELECT COUNT(*) FROM pv_payment_voucher_list_det det
                                 WHERE det.pl_number = h.pl_number AND det.status != 'Cancel'
                                 AND EXISTS (
                                   SELECT 1 FROM pv_payment_list_det pld
                                   WHERE pld.no_kbon = det.no_kbon AND pld.status != 'Cancel'
                                 )
                                ) AS in_pl_count
                                FROM pv_payment_voucher_list_h h
                                WHERE h.status = 'APPROVED'
                                AND h.pl_date BETWEEN '$start_date' AND '$end_date'
                                AND h.pl_number NOT IN (
                                    SELECT doc_number FROM ap_reverse_det d
                                    INNER JOIN ap_reverse_h rh ON rh.rvs_number = d.rvs_number
                                    WHERE rh.status = 'DRAFT' AND rh.rvs_number LIKE 'RVS/PVL/%'
                                )");

                            while ($row = mysqli_fetch_array($sql)) {
                                $pvlDate    = (!empty($row['pl_date']) && $row['pl_date'] != '0000-00-00') ? date("d-M-Y", strtotime($row['pl_date'])) : '-';
                                $isPaid     = (int)$row['paid_count'] > 0;
                                $isInPl     = (int)$row['in_pl_count'] > 0;
                                $isBlocked  = $isPaid || $isInPl;
                                $rowStyle   = $isBlocked ? ' style="background:#fef9f9;"' : '';
                                $cbAttr     = $isBlocked ? ' disabled title="This PVL has PV(s) that cannot be reversed"' : '';
                                $taAttr     = $isBlocked ? ' disabled' : '';
                                $paidNote   = $isPaid ? '<br><span style="color:#dc2626;font-size:10px;font-weight:bold;">&#9888; Some PV(s) already paid</span>' : '';
                                $inPlNote   = $isInPl ? '<br><span style="color:#d97706;font-size:10px;font-weight:bold;">&#9888; Some PV(s) already in Payment List</span>' : '';

                                echo '<tr'.$rowStyle.'>
                                    <td style="width:10px;text-align:center;"><input type="checkbox" name="select[]" value=""'.$cbAttr.'></td>
                                    <td value="'.htmlspecialchars($row['pl_number']).'" class="td-pvl-number">'.htmlspecialchars($row['pl_number']).$paidNote.$inPlNote.'</td>
                                    <td value="'.htmlspecialchars($row['pl_date']).'">'.$pvlDate.'</td>
                                    <td style="text-align:left;" value="'.htmlspecialchars($row['deskripsi']).'">'.htmlspecialchars($row['deskripsi']).'</td>
                                    <td value="'.htmlspecialchars($row['status']).'">'.htmlspecialchars($row['status']).'</td>
                                    <td value="'.htmlspecialchars($row['created_by']).'">'.htmlspecialchars($row['created_by']).'</td>
                                    <td>
                                        <textarea class="form-control form-control-sm" style="font-size: 12px; text-align: left;" rows="1"'.$taAttr.'></textarea>
                                    </td>
                                </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <form id="form-simpan">
                <div class="form-row col mt-3">
                    <div class="col-md-3 mb-3">
                        <button type="button" style="border-radius:6px;" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>
                        <button type="button" style="border-radius:6px;" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='reverse_payment_voucher_list.php'"><span class="fa fa-angle-double-left"></span> Back</button>
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
        <h4 class="modal-title text-white">Search Payment Voucher List</h4>
      </div>
      <div class="modal-body">
        <form id="modal-form" method="post">
          <div class="form-group">
            <label><b>PVL Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="modal_start_date" name="modal_start_date"
              value="<?php echo !empty($_POST['h_start_date']) ? htmlspecialchars($_POST['h_start_date']) : date('d-m-Y'); ?>"
              placeholder="Start Date">
              <span class="mx-2">-</span>
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

<!-- Modal Detail PV per PVL -->
<div class="modal fade" id="modal-pvl-detail" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="modal_pvl_number"></h4>
      </div>
      <div class="container-fluid">
        <div class="row">
          <div id="modal_pvl_date" class="modal-body col-md-4" style="font-size:12px;padding:0.5rem;"></div>
          <div id="modal_pvl_status" class="modal-body col-md-4" style="font-size:12px;padding:0.5rem;"></div>
          <div id="modal_pvl_deskripsi" class="modal-body col-md-4" style="font-size:12px;padding:0.5rem;"></div>
          <div class="modal-body col-12" style="font-size:12px;padding:0.5rem;max-height:420px;overflow-y:auto;">
            <table class="table table-striped table-bordered table-sm" style="font-size:12px;text-align:center;">
              <thead>
                <tr class="text-white" style="background-color:#1E3A8A;">
                  <th>Type PV</th>
                  <th>No PV / Kontrabon</th>
                  <th>PV Date</th>
                  <th style="text-align:left;">Supplier</th>
                  <th>Curr</th>
                  <th style="text-align:right;">Total</th>
                  <th style="text-align:left;">Description</th>
                </tr>
              </thead>
              <tbody id="modal_pvl_tbody"></tbody>
            </table>
          </div>
        </div>
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
        scrollY: "300px",
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
    var start = $("#modal_start_date").val();
    var end = $("#modal_end_date").val();
    if (!start || !end) {
        Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Please input date range.' });
        return;
    }
    $("#h_start_date").val(start);
    $("#h_end_date").val(end);
    $("#mymodal").modal("hide");
    $("#form-header").submit();
});
</script>

<script type="text/javascript">
$("input[type=checkbox]").change(function(){
    if ($(this).prop('disabled')) return;
    $(this).closest('tr').find('td:last textarea').prop('disabled', true).val('');
    $("input[type=checkbox]:not(:disabled):checked").each(function () {
        $(this).closest('tr').find('td:last textarea').prop('disabled', false);
    });
});

// Click No PVL -> show PV detail
$('#mytable tbody').on('click', 'td.td-pvl-number', function() {
    var pvl_number = $(this).attr('value');
    var pvl_date   = $(this).closest('tr').find('td:eq(2)').text();
    var deskripsi  = $(this).closest('tr').find('td:eq(3)').text();
    var status     = $(this).closest('tr').find('td:eq(4)').text();

    $('#modal_pvl_number').html(pvl_number);
    $('#modal_pvl_date').html('<b>PVL Date:</b> ' + pvl_date);
    $('#modal_pvl_status').html('<b>Status:</b> ' + status);
    $('#modal_pvl_deskripsi').html('<b>Description:</b> ' + deskripsi);
    $('#modal_pvl_tbody').html('<tr><td colspan="7" style="text-align:center;"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
    $('#modal-pvl-detail').modal('show');

    $.ajax({
        type: 'POST',
        url: 'get_pv_detail_for_reverse_pvl.php',
        data: { pvl_number: pvl_number },
        success: function(data) {
            $('#modal_pvl_tbody').html(data);
        },
        error: function(xhr) {
            $('#modal_pvl_tbody').html('<tr><td colspan="7" style="color:red;">Error: ' + xhr.responseText + '</td></tr>');
        }
    });
});
</script>

<script type="text/javascript">
$("#form-simpan").on("click", "#simpan", function () {
    var no_reverse   = $("#no_reverse").val();
    var reverse_date = $("#tanggal").val();
    var deskripsi    = $("#memo").val();
    var create_user  = '<?php echo addslashes($user); ?>';

    let checked = $("input[name='select[]']:checked");

    if (deskripsi === '') {
        Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please input Description.' });
        return;
    }

    if (checked.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please check at least one Payment Voucher List.' });
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'insert_reverse_pvl_h.php',
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

            checked.each(function () {
                var tr = $(this).closest("tr");
                var doc_number    = tr.find('td:eq(1)').attr('value');
                var doc_date      = tr.find('td:eq(2)').attr('value');
                var row_deskripsi = tr.find('td:last textarea').val() || '-';

                $.ajax({
                    type: 'POST',
                    url: 'insert_reverse_pvl_det.php',
                    data: {
                        'no_reverse'  : no_reverse,
                        'doc_number'  : doc_number,
                        'doc_date'    : doc_date,
                        'deskripsi'   : row_deskripsi,
                        'create_user' : create_user
                    },
                    cache: false,
                    success: function (res) { console.log("Detail inserted:", res); },
                    error: function (xhr) { console.error(xhr.responseText); },
                    complete: function () {
                        processed++;
                        if (processed === total) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Data saved successfully!'
                            }).then(() => {
                                window.location = 'reverse_payment_voucher_list.php';
                            });
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
