<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #modalEditCf .modal-header {
        background: linear-gradient(135deg, #172554, #2563eb);
        color: #fff;
        border: none;
    }

    #modalEditCf .modal-header .close {
        color: #fff;
        opacity: .85;
        text-shadow: none;
    }
</style>

<?php
$type_cashflow = isset($_POST['type_cashflow']) ? $_POST['type_cashflow'] : 'ALL';
$status = isset($_POST['status']) ? $_POST['status'] : 'ALL';
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-random" aria-hidden="true"></i> MAPPING CASH FLOW</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data" method="post">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type_cashflow" class="form-label"><b>Type</b></label>
                        <select class="form-control select2" name="type_cashflow" id="type_cashflow">
                            <?php
                            $type_cashflow = isset($_POST['type_cashflow']) ? $_POST['type_cashflow'] : 'ALL';
                            $types = ['ALL' => 'ALL', 'Cash In' => 'Cash In', 'Cash Out' => 'Cash Out'];
                            foreach ($types as $val => $label) {
                                $selected = ($val == $type_cashflow) ? ' selected="selected"' : '';
                                echo '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label"><b>Status</b></label>
                        <select class="form-control select2" name="status" id="status">
                            <?php
                            $status = isset($_POST['status']) ? $_POST['status'] : 'ALL';
                            $statuses = ['ALL' => 'ALL', 'Y' => 'Active', 'N' => 'Inactive'];
                            foreach ($statuses as $val => $label) {
                                $selected = ($val == $status) ? ' selected="selected"' : '';
                                echo '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end mt-2">
                        <button type="submit" id="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <a target="_blank" href="ekspor_master_cash_flow.php?type_cashflow=<?php echo urlencode($type_cashflow); ?>&status=<?php echo urlencode($status); ?>" class="ml-2">
                            <button type="button" class="btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </button>
                        </a>
                        <button type="button" id="btnNewCf" class="btn btn-primary btn-sm ml-2">
                            <i class="fa fa-plus" aria-hidden="true"></i> New
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover table-gradient" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Subcategory (Full Name)</th>
                            <th>Short Name (Used in Transaction)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbl_cf"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit / New -->
<div class="modal fade" id="modalEditCf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCfTitle"><i class="fa fa-pencil"></i> Edit Mapping Cash Flow</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="form-group">
                    <label><b>Type</b></label>
                    <select class="form-control" id="edit_type_cashflow">
                        <option value="Cash In">Cash In</option>
                        <option value="Cash Out">Cash Out</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><b>Category</b></label>
                    <input type="text" class="form-control" id="edit_nama_category" maxlength="50">
                </div>
                <div class="form-group">
                    <label><b>Subcategory (Full Name)</b></label>
                    <input type="text" class="form-control" id="edit_nama_subcategory" maxlength="150">
                </div>
                <div class="form-group">
                    <label><b>Short Name (Used in Transaction)</b></label>
                    <input type="text" class="form-control" id="edit_show_subcategory" maxlength="100">
                </div>
                <div class="form-group">
                    <label><b>Status</b></label>
                    <select class="form-control" id="edit_status">
                        <option value="Y">Active</option>
                        <option value="N">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" id="btnSaveCf" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var cfTable;

function badgeStatus(status) {
    return (status === 'Y') ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
}

function loadCfTable() {
    cfTable = $('#datatable').DataTable({
        ajax: {
            url: 'ajx_master_cash_flow.php',
            data: function(d) {
                d.type_cashflow = $('#type_cashflow').val();
                d.status = $('#status').val();
            },
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'type_cashflow' },
            { data: 'nama_category' },
            { data: 'nama_subcategory', className: 'text-left' },
            { data: 'show_subcategory', className: 'text-left', render: function(d) { return '<b>' + d + '</b>'; } },
            { data: 'status', render: function(d) { return badgeStatus(d); } },
            {
                data: null,
                orderable: false,
                render: function() {
                    return '<button type="button" class="btn btn-info btn-xs btn-edit-cf" style="border-radius:6px;"><i class="fa fa-pencil"></i> Edit</button>';
                }
            }
        ]
    });
}

$(document).ready(function() {
    loadCfTable();
    $('.select2').select2({ theme: 'bootstrap4' });
});

$('#form-data').on('submit', function(e) {
    e.preventDefault();
    cfTable.ajax.reload();
});

$('a[href^="ekspor_master_cash_flow.php"]').on('click', function() {
    var url = 'ekspor_master_cash_flow.php?type_cashflow=' + encodeURIComponent($('#type_cashflow').val()) + '&status=' + encodeURIComponent($('#status').val());
    $(this).attr('href', url);
});

$('#tbl_cf').on('click', '.btn-edit-cf', function() {
    var row = cfTable.row($(this).closest('tr')).data();
    $('#modalCfTitle').html('<i class="fa fa-pencil"></i> Edit Mapping Cash Flow');
    $('#edit_id').val(row.id);
    $('#edit_type_cashflow').val(row.type_cashflow);
    $('#edit_nama_category').val(row.nama_category);
    $('#edit_nama_subcategory').val(row.nama_subcategory);
    $('#edit_show_subcategory').val(row.show_subcategory);
    $('#edit_status').val(row.status);
    $('#modalEditCf').modal('show');
});

$('#btnNewCf').on('click', function() {
    $('#modalCfTitle').html('<i class="fa fa-plus"></i> New Mapping Cash Flow');
    $('#edit_id').val('');
    $('#edit_type_cashflow').val('Cash In');
    $('#edit_nama_category').val('');
    $('#edit_nama_subcategory').val('');
    $('#edit_show_subcategory').val('');
    $('#edit_status').val('Y');
    $('#modalEditCf').modal('show');
});

$('#btnSaveCf').on('click', function() {
    var id = $('#edit_id').val();
    var type_cashflow = $('#edit_type_cashflow').val();
    var nama_category = $('#edit_nama_category').val().trim();
    var nama_subcategory = $('#edit_nama_subcategory').val().trim();
    var show_subcategory = $('#edit_show_subcategory').val().trim();
    var status = $('#edit_status').val();

    if (nama_category === '') {
        Swal.fire('Warning', 'Category tidak boleh kosong', 'warning');
        return;
    }
    if (nama_subcategory === '') {
        Swal.fire('Warning', 'Subcategory tidak boleh kosong', 'warning');
        return;
    }
    if (show_subcategory === '') {
        Swal.fire('Warning', 'Short Name tidak boleh kosong', 'warning');
        return;
    }

    var isNew = (id === '');
    var url = isNew ? 'insert_master_cash_flow.php' : 'update_master_cash_flow.php';
    var data = {
        type_cashflow: type_cashflow,
        nama_category: nama_category,
        nama_subcategory: nama_subcategory,
        show_subcategory: show_subcategory,
        status: status
    };
    if (!isNew) {
        data.id = id;
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                Swal.fire('Saved', 'Mapping Cash Flow saved.', 'success').then(function() {
                    $('#modalEditCf').modal('hide');
                    cfTable.ajax.reload(null, false);
                });
            } else {
                Swal.fire('Error', res.message || 'Failed to save.', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to save.', 'error');
        }
    });
});
</script>

</body>
</html>
