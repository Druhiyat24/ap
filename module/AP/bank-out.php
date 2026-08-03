<?php include '../header.php';

$nama_supp = isset($_GET['nama_supp']) ? $_GET['nama_supp']: 'ALL';
$status = isset($_GET['status']) ? $_GET['status']: 'ALL';
$bank = isset($_GET['bank']) ? $_GET['bank']: 'ALL';
$akun = isset($_GET['akun']) ? $_GET['akun']: 'ALL';
$startdate = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$enddate = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");  
$start_date = date("Y-m-d",strtotime($startdate));
$end_date = date("Y-m-d",strtotime($enddate));
?>

<style>
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
    }

    .kbon-action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 6px;
    }

    .kbon-action-buttons .btn,
    .kbon-action-buttons .dropdown-toggle {
        border: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        line-height: 1.4;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(15, 23, 42, .15);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .kbon-action-buttons .btn:hover,
    .kbon-action-buttons .dropdown-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, .28);
        filter: brightness(1.06);
    }

    .kbon-action-buttons .btn:active,
    .kbon-action-buttons .dropdown-toggle:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .2);
    }

    .kbon-action-buttons .btn:focus,
    .kbon-action-buttons .dropdown-toggle:focus {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .25);
    }

    #mymodal2 .modal-content { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 16px 42px rgba(15, 23, 42, .24); }
    #mymodal2 .modal-header { background: linear-gradient(90deg, #1E3A8A, #2563EB); border: 0; padding: 18px 22px; }
    #mymodal2 .modal-title { font-size: 19px; font-weight: 700; }
    #mymodal2 .modal-body { padding: 24px; background: #f8fafc; }
    #mymodal2 .document-number { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; color: #1e3a8a; padding: 11px 13px; }
    .upload-dropzone { display: block; position: relative; border: 2px dashed #93c5fd; border-radius: 10px; background: #eff6ff; color: #1e3a8a; cursor: pointer; padding: 21px 16px; text-align: center; transition: .2s ease; }
    .upload-dropzone:hover { border-color: #2563eb; background: #dbeafe; }
    .upload-dropzone input { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .upload-dropzone .fa { font-size: 24px; display: block; margin-bottom: 7px; }
    .upload-dropzone small { display: block; color: #64748b; margin-top: 4px; }
    #mymodal2 .modal-footer { border: 0; padding: 18px 24px; background: #fff; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-university" aria-hidden="true"></i> OUTGOING BANK</h5>
        </div>
        <div class="card-body p-3">
            <form id="form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="nama_supp" class="form-label"><b>Supplier</b></label>
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="bank" class="form-label"><b>Bank</b></label>
                        <select class="form-control selectpicker" name="bank" id="bank" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "SELECT DISTINCT UPPER(bank_name) AS nama_bank FROM b_masterbank ORDER BY bank_name ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['nama_bank'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="akun" class="form-label"><b>Account</b></label>
                        <select class="form-control selectpicker" name="akun" id="akun" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "SELECT DISTINCT bank_account AS no_rek FROM b_masterbank WHERE status = 'Active' ORDER BY no_rek ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['no_rek'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label"><b>Status</b></label>
                        <select class="form-control selectpicker" name="status" id="status" data-size="5">
                            <option value="ALL" selected>ALL</option><option value="Draft">Draft</option><option value="Approved">Approved</option><option value="Cancel">Cancel</option>
                        </select>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2"><label for="start_date" class="form-label"><b>From</b></label><input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off"></div>
                    <div class="col-md-2"><label for="end_date" class="form-label"><b>To</b></label><input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off"></div>
                    <div class="col-md-8 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm me-2"><i class="fa fa-search"></i> Search</button>
                        <?php
                        $querys = mysqli_query($conn2, "SELECT menurole.id AS id FROM useraccess INNER JOIN menurole ON menurole.menu = useraccess.menu WHERE username = '$user' AND useraccess.menu = 'Create Bank'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;
                        if ($id == '37') echo '<button id="btncreate_new" type="button" class="btn btn-primary btn-sm ml-2"><i class="fa fa-plus-circle"></i> Create</button>';
                        ?>
                        <button type="button" id="btnExportExcel" class="btn btn-success btn-sm ml-2"><i class="fa fa-file-excel-o"></i> Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow border-0 mt-4"><div class="card-body p-4"><div class="table-responsive">
        <table id="mytable" class="table table-striped table-bordered table-hover table-sm" style="width:100%"><thead class="table-gradient"><tr>
            <th>No Bank Out</th><th>Supplier</th><th>Date</th><th>Currency</th><th>Amount</th><th>Status</th><th>Approve Date</th><th style="width:190px">Action</th>
        </tr></thead><tbody></tbody></table>
    </div><div class="mt-2" style="font-size:13px"><b>Total Amount:</b> <span id="total_amount">0.00</span></div></div></div>
</div>

                <div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width:70%;width:70%;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                <h4 class="modal-title" id="txt_bpb"></h4>
                            </div>
                            <div class="container-fluid">
                                <div class="row">
                                    <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                                    <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                                    <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                                    <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
                                    <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                                    <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                                    <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
                                    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
                                </div>
                            </div>
                        </div>
                        <!-- /.modal-content --> 
                    </div>
                    <!-- /.modal-dialog --> 
                </div>                            

  <div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading"><i class="fa fa-cloud-upload mr-2"></i>Upload Document Bank Out</h4>
                </div>
                <div class="modal-body">
                    <form id="modal-form2" Method="Post" Action="insert_doc_bankout.php" Enctype="Multipart/Form-Data">
                        <div class="form-row">
                            <div class="col-md-12 mb-4">
                                <label for="txt_no_bankout"><b>No Bank Out</b></label>
                                <input type="text" readonly class="form-control document-number" name="txt_no_bankout" id="txt_no_bankout" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_nama_supp" id="txt_nama_supp" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_nama_bank" id="txt_nama_bank" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_nama_akun" id="txt_nama_akun" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_user" id="txt_user" value="<?php echo $user; ?>">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_status" id="txt_status" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_start_date" id="txt_start_date" value="">
                                    <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_end_date" id="txt_end_date" value="">
                            </div>
                        </div>
                        <div class="form-row"><div class="col-md-12">
                            <label for="txtfile"><b>Dokumen PDF</b></label>
                            <label class="upload-dropzone" for="txtfile">
                                <input type="file" id="txtfile" name="txtfile" accept="application/pdf" required>
                                <i class="fa fa-file-pdf-o"></i><span id="upload-file-label">Pilih atau tarik dokumen PDF ke sini</span><small>Maksimal sesuai ketentuan sistem</small>
                            </label>
                        </div></div>
                        <div class="modal-footer mx-n4 mb-n4 mt-4">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="send2" name="send2" class="btn btn-primary px-4"><span class="fa fa-cloud-upload"></span> Upload Document</button>
                        </div>
                    </form>
                </div>
          </div>
      </div>
  </div>
</div>
</div> 

<div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading">Document Bank Out</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <div id="labelfile" name="labelfile"></div>
                    <div id="fileshow" name="fileshow"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>


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
    
    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    
    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
    $(document).ready(function() {
        window.bankOutTable = $('#mytable').DataTable({
            ordering: false,
            processing: true,
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: 'ajx_bank-out.php',
                type: 'POST',
                data: function(d) {
                    d.nama_supp = $('#nama_supp').val();
                    d.bank = $('#bank').val();
                    d.akun = $('#akun').val();
                    d.status = $('#status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                dataSrc: function(json) {
                    $('#total_amount').text(json.footer.amount);
                    return json.data;
                }
            },
            columns: [
                { data: 'no_bankout' }, { data: 'nama_supp' }, { data: 'date' }, { data: 'curr' },
                { data: 'amount' }, { data: 'status' }, { data: 'approve_date' }, { data: 'action', orderable: false }
            ],
            columnDefs: [
                { targets: [1], className: 'text-left' }, { targets: [4], className: 'text-right' },
                { targets: [0, 2, 3, 5, 6, 7], className: 'text-center' }
            ]
        });

        $('#form-data').on('submit', function(e) { e.preventDefault(); window.bankOutTable.ajax.reload(); });
        $('#btncreate_new').on('click', function() { window.location.href = 'create-out-bank.php'; });
        $('#btnExportExcel').on('click', function() {
            window.location.href = 'ekspor_bank_out.php?nama_supp=' + encodeURIComponent($('#nama_supp').val())
                + '&bank=' + encodeURIComponent($('#bank').val()) + '&akun=' + encodeURIComponent($('#akun').val())
                + '&status=' + encodeURIComponent($('#status').val()) + '&start_date=' + encodeURIComponent($('#start_date').val())
                + '&end_date=' + encodeURIComponent($('#end_date').val());
        });
    });
</script>

<script type="text/javascript">
    $(document).on("click", ".edit-none", function() {
        let doc_num = $(this).data("bank");
        // var closing = $(this).closest('tr').find('td:eq(13)').attr('value');
        var closing = 'Open';
    // alert(no_kbon + ' ' + closing);
    let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

    if (closing == 'Open') {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_bank_out_none.php?doc_num=" + encodedDocNum;
            // window.open("form_edit_bank_in_none.php?doc_num=" + encodedDocNum, "_blank");
        }
    });
    }else{
        Swal.fire({
            icon: "error",
            title: "Sorry!",
            text: "The Bank period has already been closed.",
            confirmButtonText: "OK"
        });

    }

});


    $(document).on("click", ".edit-pv", function() {
        let doc_num = $(this).data("bank");
        // var closing = $(this).closest('tr').find('td:eq(13)').attr('value');
        var closing = 'Open';
    // alert(no_kbon + ' ' + closing);
    let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

    if (closing == 'Open') {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_bank_out_pv.php?doc_num=" + encodedDocNum;
            // window.open("form_edit_bank_in_none.php?doc_num=" + encodedDocNum, "_blank");
        }
    });
    }else{
        Swal.fire({
            icon: "error",
            title: "Sorry!",
            text: "The Bank period has already been closed.",
            confirmButtonText: "OK"
        });

    }

});


    $(document).on("click", ".edit-lp", function() {
        let doc_num = $(this).data("bank");
        // var closing = $(this).closest('tr').find('td:eq(13)').attr('value');
        var closing = 'Open';
    // alert(no_kbon + ' ' + closing);
    let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

    if (closing == 'Open') {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_bank_out_lp.php?doc_num=" + encodedDocNum;
            // window.open("form_edit_bank_in_none.php?doc_num=" + encodedDocNum, "_blank");
        }
    });
    }else{
        Swal.fire({
            icon: "error",
            title: "Sorry!",
            text: "The Bank period has already been closed.",
            confirmButtonText: "OK"
        });

    }

});

$(document).on("click", ".btn-cancel-bankout", function() {
    let no_bankout = $(this).data("bank");
    let update_user = '<?php echo $user; ?>';

    Swal.fire({
        title: "Yakin ingin cancel dokumen ini?",
        text: no_bankout + " akan dibatalkan dan tidak bisa diedit lagi.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Cancel",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: 'cancel_bank_out.php',
                data: { no_pay: no_bankout, update_user: update_user },
                success: function(res) {
                    if (res.trim() === 'OK') {
                        Swal.fire('Dibatalkan!', 'Dokumen berhasil dibatalkan.', 'success');
                        window.bankOutTable.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal menghubungi server: ' + xhr.status, 'error');
                }
            });
        }
    });
});
</script>


<script type="text/javascript">
    $(document).on("click", ".btnupdate", function(){
        var no_bankout = $(this).data('bank');
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val(); 
        var bank = $('select[name=bank] option').filter(':selected').val(); 
        var akun = $('select[name=akun] option').filter(':selected').val(); 
        var status = $('select[name=status] option').filter(':selected').val();  
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;

        $('#mymodal2').modal('show');
        $('#txt_no_bankout').val(no_bankout);
        $('#txt_nama_supp').val(nama_supp);
        $('#txt_nama_bank').val(bank);
        $('#txt_nama_akun').val(akun);
        $('#txt_status').val(status);
        $('#txt_start_date').val(start_date);
        $('#txt_end_date').val(end_date);

    });

</script>

<script>
    $(document).on("click", ".delete-pdf-item", function(e){
        e.preventDefault();
        var no_bankout = $(this).data("noreq");
        var file_name = $(this).data("filename");
        var cancel_user = '<?php echo $user ?>';
        alert(no_bankout+file_name);

        if (confirm("Yakin ingin menghapus dokumen ini?\n" + file_name)) {
            $.ajax({
                type: 'POST',
                url: 'cancel_bankout_dok.php',
                data: {
                    no_bankout: no_bankout,
                    file_name: file_name,
                    cancel_user: cancel_user
                },
                success: function(data) {
                    alert("Dokumen berhasil dihapus");
                    window.bankOutTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert("Error: " + xhr.status);
                }
            });
        }
    });

    $(function () {
        $(document).on("click", ".btnshow", function () {
            var namafileStr = $(this).data('files');
            var labelfileStr = $(this).data('labels');

            var namafiles = namafileStr.split('|');
            var labelfiles = labelfileStr.split('|');

            var output = '';

            for (var i = 0; i < namafiles.length; i++) {
                var label = labelfiles[i] ? `<label><b>${labelfiles[i]}</b></label>` : '';
                var embed = `<embed src="file_pdf/bank_out/${namafiles[i]}" type="application/pdf" width="100%" height="400px" style="margin-bottom: 20px;">`;
                output += label + '<br>' + embed + '<hr>';
            }

            $('#fileshow').html(output); 
            $('#mymodal3').modal('show');
        });
    });

</script>


<script>
    function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
    } else {
        tr[i].style.display = "none";
    }
}
}
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2021",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script>
    $('#txtfile').on('change', function() {
        var fileName = this.files && this.files.length ? this.files[0].name : 'Pilih atau tarik dokumen PDF ke sini';
        $('#upload-file-label').text(fileName);
    });
</script>

<script type="text/javascript">     
    $('#mytable').on('click', 'tbody tr', function(e){
        if ($(e.target).closest('button, a').length) return;
        var row = window.bankOutTable.row(this).data();
        if (!row) return;
        $('#mymodal').modal('show');
        var no_ob = row.no_bankout;
        var supp = row.nama_supp;
        var refdoc = row.reff_doc;
        var akun = row.akun;
        var bank = row.bank;
        var curr = row.curr;
        var status = row.status;
        var desk = row.deskripsi;

        $.ajax({
            type : 'post',
            url : 'ajaxbankout.php',
            data : {'no_ob': no_ob, 'refdoc': refdoc},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ob);
        $('#txt_tglbpb').html('Supplier : ' + supp + '');
        $('#txt_no_po').html('Reff Doc : ' + refdoc + '');
        $('#txt_supp').html('Account : ' + akun + '');
        $('#txt_top').html('Bank : ' + bank + '');
        $('#txt_curr').html('Currency : ' + curr + '');        
        $('#txt_confirm').html('Status : ' + status + '');
        $('#txt_tgl_po').html('Description : ' + desk + '');                    
    });

</script>

<!--<script type="text/javascript"> 
$("#mytable").on("click", "#delbutton", function() {
    var sub = $(this).closest('tr').find('td:eq(4)').attr('data-subtotal');
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-tax');
    var total = $(this).closest('tr').find('td:eq(6)').attr('data-total');        
    var sub_val = document.getElementById("subtotal").value.replace(/[^0-9.]/g, '');
    var sub_tax = document.getElementById("pajak").value.replace(/[^0-9.]/g, '');
    var sub_total = document.getElementById("total").value.replace(/[^0-9.]/g, '');
    var min_sub = 0;
    var min_tax = 0;
    var min_total = 0;
    min_sub = sub_val - sub;
    min_tax = sub_tax - pajak;
    min_total = sub_total - total;
    $('#subtotal').val(formatMoney(min_sub));
    $('#pajak').val(formatMoney(min_tax));
    $('#total').val(formatMoney(min_total));                      
    $(this).closest("tr").remove();

});
</script>-->

<script type="text/javascript">
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};
$("input[type=checkbox]").change(function(){
    var sub = 0;
    var tax = 0;
    var total = 0;
    var ceklist = 0;         
    $("input[type=checkbox]:checked").each(function () {        
        var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value'),10) || 0;
        var qty = parseFloat($(this).closest('tr').find('td:eq(7)').attr('value'),10) ||0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(8)').attr('value'),10) ||0;               
        sub += price * qty;
        tax += tax;
        total = sub + tax;     
    });
    $("#subtotal").val(formatMoney(sub));
    $("#pajak").val(formatMoney(tax));
    $("#total").val(formatMoney(total));
    $("#select").val("1");                    
});        
</script>

<!--<script type="text/javascript">
$(document).ready(function(){
    $("#supp").on("change", function(){
        var supp= $('select[name=supp] option').filter(':selected').val();
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'supp':supp},
            close: function(e){
                e.preventDefault();
            },
            success: function(html){
                console.log(html);
                $("#no_po").html(html);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });            
    });
});    
</script>-->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {        
            var ceklist = document.getElementById('select').value;         
            var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
            var no_bpb_asal = $(this).closest('tr').find('td:eq(4)').attr('value');
            var create_user = '<?php echo $user ?>';
            var start_date = document.getElementById('start_date').value;
            var end_date = document.getElementById('end_date').value;
            var tgl_po = $(this).closest('tr').find('td:eq(9)').attr('value');
            var pterms = $(this).closest('tr').find('td:eq(10)').attr('value');        

            $.ajax({
                type:'POST',
                url:'insertfmvbpb.php',
                data: {'no_bpb':no_bpb, 'no_bpb_asal':no_bpb_asal, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date, 'tgl_po':tgl_po, 'pterms':pterms},
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){                
                    console.log(response);
                    window.location = 'verifikasibpb.php';

                },
                error:  function (xhr, ajaxOptions, thrownError) {
                   alert(xhr);
               }
           });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data saved successfully");
        }else{
            alert("Please check the BPB number");
        }        
    });
</script>

<script type="text/javascript">
    var btnCreate = document.getElementById('btncreate');
    if (btnCreate) btnCreate.onclick = function () { location.href = "create-bankout.php"; };
</script>

<script type="text/javascript">
    var btnCreateNew = document.getElementById('btncreate_new');
    if (btnCreateNew) btnCreateNew.onclick = function () { location.href = "create-out-bank.php"; };
</script>

<script type="text/javascript">
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

<!--<script>
$(document).ready(){
    $('#mybpb').click(function){
        $('#mymodal').modal('show');
    }
}
</script>-->
<!--<script>
$(document).ready(function() {   
    $("#send").click(function(e) {
        e.preventDefault();
        var datas= $(this).children("option:selected").val();
        $.ajax({
            type:"post",
            url:"cek.php",
            dataType: "json",
            data: {datas:datas},
            success: function(data){
                alert("Success: " + data);
            }
        });               
    });
</script>-->
    <!--<script>
    $(document).ready(function (){
        $("select.selectpicker").change(function(){
            var selectedbpb = $(this).children("option:selected").val();
            document.getElementById("bpbvalue").value = selectedbpb;             
        });
    });
</script>-->
    <!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

    </body>

    </html>
