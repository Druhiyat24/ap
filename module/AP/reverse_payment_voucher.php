<?php include '../header.php' ?>

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

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-history"></i> REVERSE PAYMENT VOUCHER</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="reverse_payment_voucher.php" method="post">
    <div class="row g-3">

      <!-- Status -->
      <div class="col-md-2">
        <label for="filter" class="form-label"><b>Status</b></label>
        <select class="form-control select2bs4" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" <?php if(($_POST['filter'] ?? 'ALL') == 'ALL') echo 'selected'; ?>>ALL</option>
            <option value="DRAFT" <?php if(($_POST['filter'] ?? '') == 'DRAFT') echo 'selected'; ?>>DRAFT</option>
            <option value="APPROVED" <?php if(($_POST['filter'] ?? '') == 'APPROVED') echo 'selected'; ?>>APPROVED</option>
            <option value="CANCEL" <?php if(($_POST['filter'] ?? '') == 'CANCEL') echo 'selected'; ?>>CANCEL</option>
        </select>
      </div>

      <!-- Start Date -->
      <div class="col-md-2">
        <label for="start_date" class="form-label"><b>From</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
        value="<?php echo !empty($_POST['start_date']) ? $_POST['start_date'] : date('d-m-Y'); ?>"
        placeholder="Start Date" autocomplete="off">
      </div>

      <!-- End Date -->
      <div class="col-md-2">
        <label for="end_date" class="form-label"><b>To</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
        value="<?php echo !empty($_POST['end_date']) ? $_POST['end_date'] : date('d-m-Y'); ?>"
        placeholder="End Date" autocomplete="off">
      </div>

      <!-- Buttons -->
      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-info btn-sm me-2">
          <i class="fa fa-search"></i> Search
        </button>

        <?php
        $querys = mysqli_query($conn2,"SELECT menurole.id AS id FROM useraccess INNER JOIN menurole ON menurole.menu = useraccess.menu WHERE username = '$user' AND useraccess.menu = 'Create Reverse PV'");
        $rs = mysqli_fetch_array($querys);
        $id_create = isset($rs['id']) ? $rs['id'] : 0;

        if ($id_create == '128') {
            echo '<button id="btncreate" type="button" class="btn btn-primary btn-sm ml-2"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }
        ?>
      </div>

    </div>
  </form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="mytable"
          class="table table-striped table-bordered table-hover table-sm nowrap"
          style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th>No Reverse</th>
              <th>Reverse Date</th>
              <th>Description</th>
              <th>Status</th>
              <th>Created By</th>
              <th>Created Date</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
        <?php
        function formatDateOrDash($date) {
            return (!empty($date) && $date != '0000-00-00')
            ? date("d-M-Y", strtotime($date))
            : '-';
        }

        $status = 'ALL';
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $where = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = isset($_POST['filter']) ? $_POST['filter'] : 'ALL';
            $start_date = date("Y-m-d", strtotime($_POST['start_date']));
            $end_date = date("Y-m-d", strtotime($_POST['end_date']));
        }

        if (!empty($status) && $status != 'ALL') {
            $where = " AND status = '" . mysqli_real_escape_string($conn2, $status) . "'";
        }

        $sql = mysqli_query($conn2,"SELECT rvs_number, rvs_date, deskripsi, status, created_by, created_date
            FROM ap_reverse_h
            WHERE (rvs_number LIKE 'RVS/PV/%' AND rvs_number NOT LIKE 'RVS/PVL/%')
            AND rvs_date BETWEEN '$start_date' AND '$end_date'
            $where
            ORDER BY rvs_number DESC");

        while ($row = mysqli_fetch_array($sql)) {
            echo '<tr style="font-size: 12px; text-align: center;">';
            echo '<td value="'.htmlspecialchars($row['rvs_number']).'">'.htmlspecialchars($row['rvs_number']).'</td>';
            echo '<td value="'.htmlspecialchars($row['rvs_date']).'">'.formatDateOrDash($row['rvs_date']).'</td>';
            echo '<td style="text-align: left;" value="'.htmlspecialchars($row['deskripsi']).'">'.htmlspecialchars($row['deskripsi']).'</td>';
            echo '<td value="'.htmlspecialchars($row['status']).'">'.htmlspecialchars($row['status']).'</td>';
            echo '<td value="'.htmlspecialchars($row['created_by']).'">'.htmlspecialchars($row['created_by']).'</td>';
            echo '<td value="'.htmlspecialchars($row['created_date']).'">'.htmlspecialchars($row['created_date']).'</td>';
            $viewBtn = '<button style="border-radius:6px;" type="button" class="btn-xs btn-info btn-show-data ml-1"
                data-rvs="'.htmlspecialchars($row['rvs_number']).'"
                data-date="'.formatDateOrDash($row['rvs_date']).'"
                data-desc="'.htmlspecialchars($row['deskripsi']).'">
                <i class="fas fa-eye"></i> View</button>';
            if ($row['status'] == 'DRAFT') {
                echo '<td style="white-space:nowrap;">
                    <button style="border-radius:6px;" type="button" class="btn-xs btn-danger btn-cancel" data-rvs="'.htmlspecialchars($row['rvs_number']).'"><i class="fas fa-times"></i> Cancel</button>
                    '.$viewBtn.'
                </td>';
            } else {
                echo '<td style="white-space:nowrap;">'.$viewBtn.'</td>';
            }
            echo '</tr>';
        }
        ?>
      </tbody>
    </table>
    </div>
    </div>
</div>
</div>

<!-- Modal Detail Reverse PV -->
<div class="modal fade" id="modal-show" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #2563EB;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_rvs"></h4>
      </div>
      <div class="container-fluid">
        <div class="row">
          <div id="txt_rvs_date" class="modal-body col-md-6" style="font-size:12px;padding:0.5rem;"></div>
          <div id="txt_deskripsi" class="modal-body col-md-6" style="font-size:12px;padding:0.5rem;"></div>
          <div class="modal-body col-12" style="font-size:12px;padding:0.5rem;max-height:400px;overflow-y:auto;">
            <table class="table table-striped table-bordered table-sm" style="font-size:12px;text-align:center;">
              <thead>
                <tr class="text-white" style="background-color:#1E3A8A;">
                  <th>No PV</th>
                  <th>Type PV</th>
                  <th>PV Date</th>
                  <th style="text-align:left;">Supplier</th>
                  <th>Curr</th>
                  <th style="text-align:right;">Total</th>
                  <th style="text-align:left;">Remarks</th>
                </tr>
              </thead>
              <tbody id="datatable_modal"></tbody>
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
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, 'desc']],
        info: true,
        autoWidth: false
    });
    $("[data-toggle=tooltip]").tooltip();
});
</script>

<script type="text/javascript">
$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose: true
    });
});
</script>

<script>
$(document).on("click", ".btn-cancel", function () {
    let rvs_number = $(this).data("rvs");
    Swal.fire({
        title: "Are you sure?",
        text: "This reverse document will be cancelled!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, cancel it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "cancel_reverse_pv_doc.php",
                data: { rvs_number: rvs_number },
                success: function (res) {
                    if (res.indexOf('Error') !== -1) {
                        Swal.fire({ icon: "error", title: "Error", text: res });
                        return;
                    }
                    Swal.fire({
                        icon: "success",
                        title: "Cancelled",
                        text: "Document has been cancelled successfully."
                    }).then(() => { location.reload(); });
                },
                error: function (xhr) {
                    Swal.fire({ icon: "error", title: "Error", text: "Failed to cancel document!" });
                }
            });
        }
    });
});
</script>

<?php if (!empty($id_create) && $id_create == '128'): ?>
<script type="text/javascript">
document.getElementById('btncreate').onclick = function () {
    location.href = 'form_reverse_payment_voucher.php';
};
</script>
<?php endif; ?>

<script>
$(document).on("click", ".btn-show-data", function () {
    var rvs_number = $(this).data("rvs");
    var rvs_date   = $(this).data("date");
    var deskripsi  = $(this).data("desc");

    $('#txt_rvs').html(rvs_number);
    $('#txt_rvs_date').html('<b>Reverse Date : </b>' + rvs_date);
    $('#txt_deskripsi').html('<b>Description : </b>' + deskripsi);
    $('#datatable_modal').html('<tr><td colspan="7" style="text-align:center;"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
    $('#modal-show').modal('show');

    $.ajax({
        type: 'POST',
        url: 'get_data_reverse_pv.php',
        data: { no_rvs: rvs_number },
        success: function(data) {
            $('#datatable_modal').html(data);
        },
        error: function(xhr) {
            $('#datatable_modal').html('<tr><td colspan="7" style="color:red;">Error loading data.</td></tr>');
        }
    });
});
</script>

</body>
</html>
