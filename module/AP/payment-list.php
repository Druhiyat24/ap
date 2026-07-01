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

  .kbon-action-buttons {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 5px;
  }

  .kbon-action-buttons .btn {
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 11px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.15);
      transition: all 0.2s ease;
      white-space: nowrap;
  }

  .kbon-action-buttons .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.25);
  }

  .kbon-status-label {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 20px;
  }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-list-alt" aria-hidden="true"></i> PAYMENT LIST</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="payment-list.php" method="post">
    <div class="row g-3">

        <div class="col-md-2">
            <label for="status" class="form-label"><b>Status</b></label>
            <select class="form-control select2bs4" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>
                <option value="Draft" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $status = isset($_POST['filter']) ? $_POST['filter'] : null;
                }
                echo ($status == 'Draft') ? ' selected="selected"' : '';
                ?>>Draft</option>
                <option value="FIRST APPROVED" <?php echo ($status == 'FIRST APPROVED') ? ' selected="selected"' : ''; ?>>First Approved</option>
                <option value="SECOND APPROVED" <?php echo ($status == 'SECOND APPROVED') ? ' selected="selected"' : ''; ?>>Second Approved</option>
                <option value="Cancel" <?php echo ($status == 'Cancel') ? ' selected="selected"' : ''; ?>>Cancel</option>
            </select>
        </div>

        <!-- Start Date -->
        <div class="col-md-2">
            <label for="start_date" class="form-label"><b>From</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
            value="<?php
            if (!empty($_POST['start_date'])) {
                echo $_POST['start_date'];
            } else {
                echo date("d-m-Y");
            } ?>" placeholder="Start Date" autocomplete="off">
        </div>

        <!-- End Date -->
        <div class="col-md-2">
            <label for="end_date" class="form-label"><b>To</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
            value="<?php
            if (!empty($_POST['end_date'])) {
                echo $_POST['end_date'];
            } else {
                echo date("d-m-Y");
            } ?>" placeholder="End Date" autocomplete="off">
        </div>

        <!-- Tombol -->
        <div class="col-md-6 d-flex align-items-end mt-2">
          <button type="submit" class="btn btn-info btn-sm me-2">
                <i class="fa fa-search"></i> Search
              </button>
            <?php
            $querysCreate = mysqli_query($conn2, "select useraccess.menu as menu, useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Payment List'");
            $rsCreate = mysqli_fetch_array($querysCreate);
            $idCreate = isset($rsCreate['id']) ? $rsCreate['id'] : 0;

            if ($idCreate == '116') {
                echo '<button type="button" id="btncreate" class="btn btn-primary btn-sm ml-2">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
                </button>';
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
           class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">No Payment List</th>
                <th style="text-align: center;vertical-align: middle;">PL Date</th>
                <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                <th style="text-align: center;vertical-align: middle;">Status</th>
                <th style="text-align: center;vertical-align: middle;">Created By</th>
                <th style="text-align: center;vertical-align: middle;">Created Date</th>
                <th style="text-align: center;vertical-align: middle;width: 190px;">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal-show" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_pl"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_pl_date" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_pl_desc" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;">
                      <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                        <thead>
                            <tr>
                                <th style="width:15%;">Type</th>
                                <th style="width:25%;">No Payment Voucher</th>
                                <th style="width:15%;">PV Date</th>
                                <th style="width:25%;">Supplier</th>
                                <th style="width:10%;">Curr</th>
                                <th style="width:10%;">Total</th>
                                <th style="width:15%;">Keterangan</th>
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
  $('#body-row .collapse').collapse('hide');
$('#collapse-icon').addClass('fa-angle-double-left');
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
</script>

<script>
    $(function() {
      $('.select2').select2()
      $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
  });

    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script type="text/javascript">
    var datatable = $('#mytable').DataTable({
        ordering: false,
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,

        ajax: {
            url: 'ajx_payment-list.php',
            type: 'POST',
            data: function (d) {
                d.status     = $('#filter').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            }
        },

        columns: [
            { data: 'pl_number' },
            { data: 'pl_date' },
            { data: 'deskripsi' },
            { data: 'status' },
            { data: 'created_by' },
            { data: 'created_date' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [2], className: 'text-left' },
            { targets: [0, 1, 3, 4, 5, 6], className: 'text-center' },
        ],
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function (e) {
        e.preventDefault();
        dataTableReload();
    });
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "form_payment-list.php";
    };
</script>

<script type="text/javascript">
    $("#mytable").on("click", ".btn-show-pl", function () {
        const data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        var pl_number = data.pl_number;

        $.ajax({
            type: 'POST',
            url: 'get_data_payment_list_det.php',
            data: { 'pl_number': pl_number },
            cache: false,
            success: function (html) {
                $('#datatable_modal').html(html);
            },
            error: function (xhr) {
                console.log(xhr);
            }
        });

        $('#txt_pl').html(pl_number);
        $('#txt_pl_date').html('<b>Payment List Date : </b>' + data.pl_date);
        $('#txt_pl_desc').html('<b>Keterangan : </b>' + data.deskripsi);
        $('#modal-show').modal('show');
    });
</script>

<script type="text/javascript">
    $(document).on("click", ".btn-cancel-pl", function () {
        var pl_number = $(this).data("pl");

        Swal.fire({
            title: "Are you sure?",
            text: "This Payment List will be cancelled, and all Payment Vouchers in it will be released for the next Payment List.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, cancel it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "cancel_payment_list.php",
                    data: { pl_number: pl_number, cancel_user: '<?php echo $user ?>' },
                    success: function () {
                        Swal.fire({
                            icon: "success",
                            title: "Cancelled",
                            text: "Payment List has been cancelled successfully."
                        }).then(() => {
                            dataTableReload();
                        });
                    },
                    error: function () {
                        Swal.fire({ icon: "error", title: "Error", text: "Failed to cancel Payment List!" });
                    }
                });
            }
        });
    });
</script>

</body>

</html>
