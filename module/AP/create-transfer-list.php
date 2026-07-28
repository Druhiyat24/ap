<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input, select {
        font-size: 14px;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Header Form -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-exchange" aria-hidden="true"></i> CREATE TRANSFER LIST</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-header">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label"><b>Tgl Dokumen</b></label>
                        <input type="text" class="form-control" id="tl_date" value="<?php echo date('d-m-Y'); ?>" readonly autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label"><b>Tipe Mutasi <span class="text-danger">*</span></b></label>
                        <select class="form-control select2bs4" id="tipe_mutasi" style="width:100%;">
                            <option value="">- Pilih -</option>
                            <option value="Single">Single</option>
                            <option value="Multi">Multi</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label"><b>Jenis Otorisasi <span class="text-danger">*</span></b></label>
                        <select class="form-control select2bs4" id="jenis_otorisasi" style="width:100%;">
                            <option value="">- Pilih -</option>
                            <option value="Bulk">Bulk</option>
                            <option value="Satuan">Satuan</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label"><b>Tanggal Efektif <span class="text-danger">*</span></b></label>
                        <input type="text" class="form-control tanggal" id="tanggal_efektif" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label"><b>Jenis Biaya <span class="text-danger">*</span></b></label>
                        <select class="form-control select2bs4" id="jenis_biaya" style="width:100%;">
                            <option value="">- Pilih -</option>
                            <option value="OUR">OUR</option>
                            <option value="BEN">BEN</option>
                            <option value="SHA">SHA</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-2">
                        <label class="form-label"><b>From</b></label>
                        <input type="text" class="form-control  tanggal" id="start_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>To</b></label>
                        <input type="text" class="form-control  tanggal" id="end_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Filter PL -->
    <div class="card shadow border-0 mt-4">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-list-alt" aria-hidden="true"></i> PILIH PAYMENT LIST</h5>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="mytable" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-gradient">
                        <tr>
                            <th style="width:30px;text-align:center;"><input type="checkbox" id="chk_all"></th>
                            <th style="text-align:center;">No Payment List</th>
                            <th style="text-align:center;">PL Date</th>
                            <th style="text-align:center;">Keterangan</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Rekening Asal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" id="btnBack" class="btn btn-danger mr-2">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> Back
                </button>
                <button type="button" id="btnSave" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> Save Transfer List
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
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
    $('#body-row .collapse').collapse('hide');
    $('#collapse-icon').addClass('fa-angle-double-left');
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse() {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        var SeparatorTitle = $('.sidebar-separator-title');
        if (SeparatorTitle.hasClass('d-flex')) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('#tanggal_efektif').datepicker('setStartDate', new Date());
        $('.select2bs4').select2({
            theme: 'bootstrap4'
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
            url: 'ajx_pl_for_transfer.php',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            }
        },

        columns: [
            { data: null, orderable: false, render: function(data){
                var checked = selectedPL[data.pl_number] ? 'checked' : '';
                return '<input type="checkbox" class="chk_pl" data-pl="'+data.pl_number+'" data-account="'+data.from_account+'" '+checked+'>';
            }},
            { data: 'pl_number' },
            { data: 'pl_date' },
            { data: 'deskripsi' },
            { data: 'status' },
            { data: 'from_account' },
        ],

        columnDefs: [
            { targets: [0, 1, 2, 4, 5], className: 'text-center' },
        ],
    });

    // Simpan pilihan PL di luar DOM tabel, supaya tidak hilang saat pindah
    // halaman/paging (DataTables cuma render baris yang sedang tampil).
    var selectedPL = {};

    $('#mytable').on('change', '.chk_pl', function() {
        var pl = $(this).data('pl');
        if ($(this).is(':checked')) {
            selectedPL[pl] = true;
        } else {
            delete selectedPL[pl];
        }
    });

    $('#form-header').on('submit', function(e) {
        e.preventDefault();
        datatable.ajax.reload();
    });

    $('#chk_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.chk_pl').prop('checked', isChecked).trigger('change');
    });

    $('#btnBack').on('click', function() {
        location.href = 'transfer-list.php';
    });

    $('#btnSave').on('click', function() {
        var tipe_mutasi = $('#tipe_mutasi').val();
        var jenis_otorisasi = $('#jenis_otorisasi').val();
        var tanggal_efektif = $('#tanggal_efektif').val();
        var jenis_biaya = $('#jenis_biaya').val();
        var tl_date = $('#tl_date').val();

        if (!tipe_mutasi) {
            Swal.fire('Warning', 'Tipe Mutasi wajib diisi', 'warning');
            return;
        }
        if (!jenis_otorisasi) {
            Swal.fire('Warning', 'Jenis Otorisasi wajib diisi', 'warning');
            return;
        }
        if (!tanggal_efektif) {
            Swal.fire('Warning', 'Tanggal Efektif wajib diisi', 'warning');
            return;
        }
        if (!jenis_biaya) {
            Swal.fire('Warning', 'Jenis Biaya wajib diisi', 'warning');
            return;
        }

        var pl_numbers = Object.keys(selectedPL);

        if (pl_numbers.length === 0) {
            Swal.fire('Warning', 'Pilih minimal 1 Payment List', 'warning');
            return;
        }

        Swal.fire({
            title: 'Simpan Transfer List?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: 'save_transfer_list.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    tl_date: tl_date,
                    tipe_mutasi: tipe_mutasi,
                    jenis_otorisasi: jenis_otorisasi,
                    tanggal_efektif: tanggal_efektif,
                    jenis_biaya: jenis_biaya,
                    pl_numbers: pl_numbers
                },
                success: function(res) {
                    if (res.status === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Transfer List ' + res.tl_number + ' berhasil dibuat'
                        }).then(() => {
                            location.href = 'transfer-list.php';
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });
    });
</script>

</body>

</html>
