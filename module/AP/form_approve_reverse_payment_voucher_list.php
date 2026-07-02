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

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> APPROVAL REVERSE PAYMENT VOUCHER LIST <span class="badge bg-danger ms-2">
        <i class="fas fa-bell"></i>
        <?php
        $sql = mysqli_query($conn2,"SELECT COUNT(id) jml FROM ap_reverse_h WHERE rvs_number LIKE 'RVS/PVL/%' AND status = 'DRAFT'");
        $row = mysqli_fetch_array($sql);
        echo $row['jml'];
        ?>
    </span></h5>
</div>

<div class="card-body p-3">
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
              <th style="width: 18%;">No Reverse</th>
              <th style="width: 13%;">Reverse Date</th>
              <th style="width: 27%;">Deskripsi</th>
              <th style="width: 14%;">Created By</th>
              <th style="width: 18%;">Created Date</th>
              <th style="width:5%; text-align: center;"><input type="checkbox" id="select_all"></th>
          </tr>
      </thead>
      <tbody>
        <?php
        function formatDateOrDash($date) {
            return (!empty($date) && $date != '0000-00-00')
            ? date("d-M-Y", strtotime($date))
            : '-';
        }

        $sql = mysqli_query($conn2,"SELECT rvs_number, rvs_date, deskripsi, created_by, created_date FROM ap_reverse_h WHERE status = 'DRAFT' AND rvs_number LIKE 'RVS/PVL/%'");

        while ($row = mysqli_fetch_array($sql)) {
            echo '<tr style="text-align:center;">';
            echo '<td value="'.htmlspecialchars($row['rvs_number']).'">'.htmlspecialchars($row['rvs_number']).'</td>';
            echo '<td value="'.htmlspecialchars($row['rvs_date']).'">'.formatDateOrDash($row['rvs_date']).'</td>';
            echo '<td style="text-align: left;" value="'.htmlspecialchars($row['deskripsi']).'">'.htmlspecialchars($row['deskripsi']).'</td>';
            echo '<td value="'.htmlspecialchars($row['created_by']).'">'.htmlspecialchars($row['created_by']).'</td>';
            echo '<td value="'.htmlspecialchars($row['created_date']).'">'.htmlspecialchars($row['created_date']).'</td>';
            echo '<td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.htmlspecialchars($row['rvs_number']).'"></td>
            </tr>';
        }
        ?>
      </tbody>
    </table>
    </div>

    <form id="form-simpan">
        <div class="mt-3">
            <button type="button" name="approve" id="approve" class="btn btn-success">
              <i class="fas fa-check-circle"></i> Approve
            </button>
            <button type="button" name="cancel" id="cancel" class="btn btn-danger ml-2">
              <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </form>
    </div>
</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal-show" data-target="#modal-show" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_rvs"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_rvs_date" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_deskripsi" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem; max-height:420px; overflow-y:auto;">
                      <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px; text-align:center;">
                        <thead>
                            <tr class="text-white" style="background-color:#1E3A8A;">
                                <th>No PVL</th>
                                <th>PVL Date</th>
                                <th style="text-align:left;">No PV</th>
                                <th style="text-align:left;">Description PVL</th>
                                <th>Status</th>
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
$("#select_all").on("click", function(){
    $("input[name='select[]']").prop("checked", this.checked);
});

$("#form-simpan").on("click", "#approve", function(e){
    e.preventDefault();
    let selected = $("input[name='select[]']:checked");
    if (selected.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Please check at least one data before proceeding' });
        return;
    }

    let approve_user = '<?php echo addslashes($user); ?>';
    let total = selected.length;
    let processed = 0;
    let errors = 0;

    selected.each(function () {
        let row = $(this).closest('tr');
        let data = {
            rvs_number   : row.find('td:eq(0)').attr('value'),
            approve_user : approve_user
        };
        $.ajax({
            type: 'POST',
            url: 'approve_reverse_pvl_doc.php',
            data: data,
            success: function(response){ console.log("Approved:", response); },
            error: function(xhr){ console.error(xhr.responseText); errors++; },
            complete: function(){
                processed++;
                if (processed === total) {
                    if (errors === 0) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: total + ' reverse(s) approved successfully' }).then(() => {
                            window.location = 'form_approve_reverse_payment_voucher_list.php';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed!', text: (total - errors) + ' approved successfully, ' + errors + ' failed' });
                    }
                }
            }
        });
    });
});

$("#form-simpan").on("click", "#cancel", function(e){
    e.preventDefault();
    let selected = $("input[name='select[]']:checked");
    if (selected.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Please check at least one data before proceeding' });
        return;
    }

    let total = selected.length;
    let processed = 0;
    let errors = 0;

    selected.each(function () {
        let row = $(this).closest('tr');
        let data = { rvs_number: row.find('td:eq(0)').attr('value') };
        $.ajax({
            type: 'POST',
            url: 'cancel_reverse_pvl_doc.php',
            data: data,
            success: function(response){ console.log("Cancelled:", response); },
            error: function(xhr){ console.error(xhr.responseText); errors++; },
            complete: function(){
                processed++;
                if (processed === total) {
                    if (errors === 0) {
                        Swal.fire({ icon: 'success', title: 'Success!', text: total + ' reverse(s) cancelled successfully' }).then(() => {
                            window.location = 'form_approve_reverse_payment_voucher_list.php';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed!', text: (total - errors) + ' cancelled successfully, ' + errors + ' failed' });
                    }
                }
            }
        });
    });
});
</script>

<script type="text/javascript">
$('table tbody tr').on('click', 'td:eq(0)', function(){
    var no_rvs    = $(this).closest('tr').find('td:eq(0)').attr('value');
    var rvs_date  = $(this).closest('tr').find('td:eq(1)').attr('value');
    var deskripsi = $(this).closest('tr').find('td:eq(2)').attr('value');

    $('#datatable_modal').html('<tr><td colspan="6" style="text-align:center;"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

    $.ajax({
        type: 'POST',
        url: 'get_data_reverse_pvl.php',
        data: { 'no_rvs': no_rvs },
        cache: false,
        success: function(data){
            $('#datatable_modal').html(data);
        },
        error: function (xhr) {
            $('#datatable_modal').html('<tr><td colspan="6" style="color:red;">Error loading data.</td></tr>');
            console.log(xhr);
        }
    });

    $('#txt_rvs').html(no_rvs);
    $('#txt_rvs_date').html('<b>Reverse Date : </b>' + rvs_date);
    $('#txt_deskripsi').html('<b>Descriptions : </b>' + deskripsi);
    $('#modal-show').modal('show');
});
</script>

</body>
</html>
