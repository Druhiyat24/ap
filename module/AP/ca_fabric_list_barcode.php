<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
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
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-barcode" aria-hidden="true"></i> LIST BARCODE</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data" onsubmit="return false;">
                <div class="row g-3">

                    <!-- Start Date -->
                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
                            value="<?php echo date('d-m-Y'); ?>" placeholder="Start Date" autocomplete="off">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
                            value="<?php echo date('d-m-Y'); ?>" placeholder="End Date" autocomplete="off">
                    </div>

                    <!-- Tombol -->
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" onclick="dataTableReload()" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
                            <i class="fa fa-undo"></i> Reset
                        </button>

                        <button type="button" id="btnExportExcel" class="btn btn-success btn-sm ml-2">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                        <button type="button" id="btnUpdateBarcodeOut" class="btn btn-primary btn-sm ml-2">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Update Barcode Out
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Card Table -->
<div class="container-fluid px-4">
    <div class="card shadow border-0 mt-2">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="mytable" class="table table-striped table-bordered table-hover table-sm nowrap" style="width:100%">
                    <thead class="table-gradient text-white">
                        <tr>
                            <th style="text-align: center;vertical-align: middle;">No</th>
                            <th style="text-align: center;vertical-align: middle;">No BPB</th>
                            <th style="text-align: center;vertical-align: middle;">Tgl BPB</th>
                            <th style="text-align: center;vertical-align: middle;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;">No PO</th>
                            <th style="text-align: center;vertical-align: middle;">No Barcode</th>
                            <th style="text-align: center;vertical-align: middle;">Id Item</th>
                            <th style="text-align: center;vertical-align: middle;">Item Desc</th>
                            <th style="text-align: center;vertical-align: middle;">Id JO</th>
                            <th style="text-align: center;vertical-align: middle;">No WS</th>
                            <th style="text-align: center;vertical-align: middle;">Style</th>
                            <th style="text-align: center;vertical-align: middle;">Tgl Terima</th>
                            <th style="text-align: center;vertical-align: middle;">Curr</th>
                            <th style="text-align: center;vertical-align: middle;">Price</th>
                            <th style="text-align: center;vertical-align: middle;">Rate</th>
                            <th style="text-align: center;vertical-align: middle;">Price IDR</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
    function toYmd(dmy) {
        if (!dmy) return '';
        let p = dmy.split('-'); // [dd, mm, yyyy]
        return `${p[2]}-${p[1]}-${p[0]}`;
    }

    // Dataset ini ratusan ribu baris - selalu serverSide, jangan diubah ke client-side rendering.
    let datatable = $('#mytable').DataTable({
        ordering: true,
        order: [[2, 'desc']],
        processing: true,
        serverSide: true,
        pageLength: 10,
        searching: true,
        info: true,
        autoWidth: false,
        ajax: {
            url: 'ajx_ca_fabric_list_barcode.php',
            type: 'POST',
            data: function(d) {
                d.start_date = toYmd($('#start_date').val());
                d.end_date   = toYmd($('#end_date').val());
            }
        },
        columns: [
            { data: 'rownum', orderable: false },
            { data: 'no_dok' },
            { data: 'tgl_dok' },
            { data: 'supplier' },
            { data: 'no_po' },
            { data: 'no_barcode' },
            { data: 'id_item' },
            { data: 'itemdesc' },
            { data: 'id_jo' },
            { data: 'kpno' },
            { data: 'styleno' },
            { data: 'np_tgl_in' },
            { data: 'np_curr' },
            { data: 'np_price' },
            { data: 'rate' },
            { data: 'np_price_idr' }
        ],
        columnDefs: [
            { targets: [0, 1, 2, 3, 4, 5, 6, 7, 11, 12], className: 'text-left' },
            { targets: [13, 14, 15], className: 'text-right' }
        ]
    });

    function dataTableReload() {
        datatable.ajax.reload(() => {
            datatable.columns.adjust();
        });
    }

    // Export tidak lagi buka tab baru (bisa terlihat "loading" kosong lama kalau
    // datanya banyak) - sekarang fetch di background + swal loading, baru file-nya
    // di-download begitu selesai. Tidak ada batas jumlah baris (sengaja), jadi untuk
    // rentang tanggal yang lebar proses bisa makan waktu beberapa menit - fetch() tidak
    // punya timeout bawaan, jadi ini akan tetap menunggu sampai server selesai.
    document.getElementById('btnExportExcel').addEventListener('click', function() {
        let sd = toYmd(document.getElementById('start_date').value);
        let ed = toYmd(document.getElementById('end_date').value);

        Swal.fire({
            title: 'Sedang export...',
            text: 'Mohon tunggu, untuk rentang tanggal yang lebar proses bisa memakan waktu beberapa menit. Jangan tutup halaman ini.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`ekspor_ca_fabric_list_barcode.php?start_date=${sd}&end_date=${ed}`)
            .then(async (res) => {
                const contentType = res.headers.get('Content-Type') || '';
                if (contentType.indexOf('json') !== -1) {
                    const data = await res.json();
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: data.error || 'Export gagal.' });
                    return;
                }
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'List Barcode.xls';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.close();
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Export gagal, coba lagi.' });
            });
    });

    document.getElementById('btnUpdateBarcodeOut').addEventListener('click', function() {
        Swal.fire({
            title: 'Update Barcode Out?',
            text: 'Proses ini akan memperbarui data Barcode Out.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd'
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Memproses update...',
                text: 'Mohon tunggu dan jangan tutup halaman ini.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'update_out_barcode.php',
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'ok') {
                        Swal.fire('Berhasil', res.message || 'Barcode Out berhasil diperbarui.', 'success');
                        dataTableReload();
                    } else {
                        Swal.fire('Gagal', res.message || 'Update Barcode Out gagal.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Request gagal. Cek koneksi atau server.', 'error');
                }
            });
        });
    });

    $("[data-toggle=tooltip]").tooltip();
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function() {
        location.href = "ca_fabric_list_barcode.php";
    };
</script>

</body>

</html>
