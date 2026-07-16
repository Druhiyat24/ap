<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #table-approve {
        width: 100% !important;
    }

    #table-approve th,
    #table-approve td {
        vertical-align: middle;
    }

    @media (min-width: 992px) {
        #mymodal .modal-dialog {
            max-width: 75%;
        }
    }

    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.5rem + 2px);
        font-size: 0.875rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + 0.5rem);
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.5rem + 2px);
    }

    .badge-pv-waiting {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        background: #eef2f9;
        color: #1e3a8a;
    }

    .pv-approve-bar {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 2px solid #1E3A8A;
        padding: 12px 16px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        border-radius: 0 0 10px 10px;
    }

    .btn-pv-approve {
        background: linear-gradient(90deg, #191970, #1e90ff);
        color: #fff;
        border: none;
        border-radius: 20px;
        font-weight: 600;
        padding: 8px 22px;
    }

    .btn-pv-approve:hover {
        color: #fff;
    }

    .btn-pv-cancel {
        border-radius: 20px;
        font-weight: 600;
        padding: 8px 22px;
    }

    .btn-pv-approve:disabled,
    .btn-pv-cancel:disabled {
        opacity: 0.5;
    }

    /* Due date yang sudah lewat, sama seperti form-closing-payreg.php (warna disoftkan) */
    #table-approve tr.pv-row-overdue > td {
        background-color: #f8d7da !important;
        color: #842029;
    }

    /* Due date tinggal <= 7 hari */
    #table-approve tr.pv-row-due-soon > td {
        background-color: #fff3cd !important;
        color: #664d03;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3"
        style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0"><i class="fa fa-check-double" aria-hidden="true"></i> SECOND APPROVAL - PAYMENT VOUCHER</h5>
    </div>

    <div class="card-body p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="nama_supp"><b>Supplier</b></label>
                <select class="form-control form-control-sm select2" name="nama_supp" id="nama_supp" style="width: 100%;">
                    <option value="ALL" selected="selected">ALL</option>
                    <?php
                    $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                    while ($row = mysqli_fetch_array($sql)) {
                        $data = $row['Supplier'];
                        echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="type_pv"><b>Type</b></label>
                <!-- DP & CBD sudah pindah ke approve-pv.php (digabung dengan Second Approval
                     PV Biaya) - lihat catatan di ajx_approve-payment-voucher-ap.php. -->
                <select class="form-control form-control-sm select2" name="type_pv" id="type_pv" style="width: 100%;">
                    <option value="ALL" selected="selected">ALL</option>
                    <option value="Regular">Regular</option>
                    <option value="Installment">Installment</option>
                    <option value="Saldo Awal">Saldo Awal</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
        <div style="overflow-x:auto;">
            <table id="table-approve" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
                <thead class="table-gradient">
                    <tr>
                        <th style="text-align: center;vertical-align: middle;width: 36px;"><input type="checkbox" id="chkSelectAll"></th>
                        <th style="text-align: center;vertical-align: middle;">No Kontra Bon</th>
                        <th style="text-align: center;vertical-align: middle;">Type</th>
                        <th style="text-align: center;vertical-align: middle;">Kontra Bon Date</th>
                        <th style="text-align: center;vertical-align: middle;">Due Date</th>
                        <th style="text-align: center;vertical-align: middle;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;">SubTotal</th>
                        <th style="text-align: center;vertical-align: middle;">Tax (PPn)</th>
                        <th style="text-align: center;vertical-align: middle;">Tax (PPh)</th>
                        <th style="text-align: center;vertical-align: middle;">Total CBD/DP</th>
                        <th style="text-align: center;vertical-align: middle;">Return</th>
                        <th style="text-align: center;vertical-align: middle;">Potongan</th>
                        <th style="text-align: center;vertical-align: middle;">Total KB</th>
                        <th style="text-align: center;vertical-align: middle;">Currency</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="pv-approve-bar">
            <button type="button" class="btn btn-pv-approve" id="btnApproveSelected" disabled>
                <i class="fa fa-check-double"></i> Approve
            </button>
            <button type="button" class="btn btn-outline-danger btn-pv-cancel" id="btnCancelSelected" disabled>
                <i class="fa fa-times-circle"></i> Cancel
            </button>
            <span id="selectedCount" class="text-muted">0 item dipilih</span>
        </div>
    </div>
</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-3 mb-2"></div>
          <div id="txt_supp" class="col-md-3 mb-2"></div>
          <div id="txt_duedate" class="col-md-3 mb-2"></div>
          <div id="txt_curr" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-3 mb-2"></div>
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_nofaktur" class="col-md-3 mb-2"></div>
          <div id="txt_suppinv" class="col-md-3 mb-2"></div>
          <div id="txt_tglinv" class="col-md-3 mb-2"></div>
          <div id="txt_installment" class="col-md-3 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
  </div>
</div>
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
    $(document).ready(function() {
        $('#nama_supp').select2({ width: '100%' });
        $('#type_pv').select2({ width: '100%' });
    });
</script>

<script type="text/javascript">
    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, function (ch) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
        });
    }

    let approveTable = $('#table-approve').DataTable({
        // Hanya kolom Due Date yang boleh disortir (lihat columnDefs orderable:false di bawah) -
        // kolom lain (termasuk checkbox) tetap tidak bisa disortir seperti sebelumnya.
        ordering: true,
        order: [],
        processing: true,
        serverSide: false,
        pageLength: 10,
        searching: true,
        info: true,
        autoWidth: false,

        ajax: {
            url: 'ajx_approve-payment-voucher-ap.php',
            type: 'POST',
            data: function (d) {
                d.nama_supp = $('#nama_supp').val();
                d.type_pv = $('#type_pv').val();
                d.stage = 'second';
            }
        },

        columns: [
            { data: 'select', orderable: false },
            { data: 'no_kbon' },
            { data: 'type_label' },
            { data: 'tgl_kbon' },
            // tgl_tempo ditampilkan "d-M-Y" (tidak sortable secara chronological kalau disortir
            // sebagai string), jadi sort/type pakai tgl_tempo_raw (format Y-m-d dari database).
            { data: { _: 'tgl_tempo', sort: 'tgl_tempo_raw', type: 'tgl_tempo_raw' } },
            { data: 'nama_supp' },
            { data: 'subtotal' },
            { data: 'tax' },
            { data: 'pph_value' },
            { data: 'dp' },
            { data: 'return' },
            { data: 'potong' },
            { data: 'total' },
            { data: 'curr' }
        ],

        columnDefs: [
            { targets: [6, 7, 8, 9, 10, 11, 12], className: 'text-right' },
            { targets: [0, 1, 2, 3, 4, 5, 13], className: 'text-center' },
            { targets: [0, 1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12, 13], orderable: false }
        ],

        // Due date yang sudah lewat ditandai merah, tinggal <= 7 hari ditandai kuning -
        // sama seperti form-closing-payreg.php (merah saja disitu, kuning ditambahkan disini).
        createdRow: function (row, data) {
            if (data.overdue) {
                $(row).addClass('pv-row-overdue');
            } else if (data.due_soon) {
                $(row).addClass('pv-row-due-soon');
            }
        },

        drawCallback: function () {
            updateSelectedCount();
            $('#chkSelectAll').prop('checked', false);
        }
    });

    function reloadApproveTable() {
        approveTable.ajax.reload(function () {
            approveTable.columns.adjust();
        }, false);
    }

    // Auto reload saat supplier/type filter berubah, tanpa tombol Search
    $('#nama_supp, #type_pv').on('change', function () {
        reloadApproveTable();
    });

    // Tiap type PV punya tabel & endpoint approve/cancel sendiri-sendiri
    // (lihat ajx_approve-payment-voucher-ap.php: getApprovalRegular/Installment/SaldoAwal).
    // DP & CBD sudah pindah ke approve-pv.php.
    const APPROVE_ENDPOINT = {
        regular: 'second_approve_kbon.php',
        installment: 'second_approve_installment.php',
        saldo_awal: 'second_approve_saldo_awal.php'
    };
    const CANCEL_ENDPOINT = {
        regular: 'cancelkbon.php',
        installment: 'cancelkboninstallment.php',
        saldo_awal: 'cancel_saldo_awal.php'
    };

    // DataTables hanya merender <tr> halaman aktif ke DOM saat paging client-side,
    // jadi selector jQuery biasa ($('.pv-approve-chk')) cuma kena baris yang sedang tampil.
    // Pakai rows().nodes() supaya select-all dan pengumpulan no_kbon mencakup SEMUA baris di semua halaman.
    function allCheckboxes() {
        // Pakai $('#table-approve').DataTable() (tanpa argumen) untuk ambil instance yang sudah
        // terdaftar, bukan variabel approveTable - drawCallback pertama berjalan SEBELUM
        // assignment "let approveTable = ..." selesai, jadi approveTable masih kena TDZ saat itu.
        return $($('#table-approve').DataTable().rows().nodes()).find('.pv-approve-chk');
    }

    function updateSelectedCount() {
        const n = allCheckboxes().filter(':checked').length;
        $('#selectedCount').text(n + ' item dipilih');
        $('#btnApproveSelected').prop('disabled', n === 0);
        $('#btnCancelSelected').prop('disabled', n === 0);
    }

    $('#table-approve').on('change', '.pv-approve-chk', function () {
        updateSelectedCount();
    });

    $('#chkSelectAll').on('change', function () {
        const checked = this.checked;
        allCheckboxes().prop('checked', checked);
        updateSelectedCount();
    });

    // Klik baris (kolom No Kontra Bon) untuk lihat detail
    $('#table-approve').on('click', 'tbody tr td:nth-child(2)', function () {
        const data = approveTable.row($(this).closest('tr')).data();
        if (!data) return;

        $('#mymodal').modal('show');
        $('#txt_bpb').text(data.no_kbon);
        $('#txt_tglbpb').html('Payment Voucher Date : ' + escapeHtml(data.tgl_kbon));
        $('#txt_supp').html('Supplier : ' + escapeHtml(data.nama_supp));
        $('#txt_duedate').html('Due Date : ' + escapeHtml(data.tgl_tempo));
        $('#txt_curr').html('Currency : ' + escapeHtml(data.curr));
        $('#txt_created_by').html('Create By : ' + escapeHtml(data.create_user));
        $('#txt_status').html('Status : ' + escapeHtml(data.status));
        $('#txt_nofaktur').html('No Faktur : ' + escapeHtml(data.no_faktur));
        $('#txt_suppinv').html('No Supplier Invoice : ' + escapeHtml(data.supp_inv));
        $('#txt_tglinv').html('Supplier Invoice Date : ' + escapeHtml(data.tgl_inv));
        // tgl_kbon2 dipakai getApprovalInstallment() untuk menyimpan "Installment X/Y" -
        // tampilkan di modal, kosongkan untuk tipe lain.
        if (data.no_kbon_parent && data.tgl_kbon2 && data.tgl_kbon2.indexOf('Installment') === 0) {
            $('#txt_installment').html('Installment : ' + escapeHtml(data.tgl_kbon2.replace('Installment ', '').replace('/', ' of ')));
        } else {
            $('#txt_installment').html('');
        }
        $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');

        $.ajax({
            // DP/CBD pakai tabel sendiri (kontrabon_dp/kontrabon_cbd, bukan kontrabon),
            // jadi detailnya harus lewat ajaxkbondp.php/ajaxkboncbd.php masing-masing,
            // bukan ajaxkbon.php generik. Installment menampilkan nomor anak
            // (data.no_kbon) di listing, tapi BPB/RO/FTR detailnya tersimpan di nomor
            // induk - pakai no_kbon_parent kalau ada supaya query-nya tidak salah nomor.
            url: data.detail_url || 'ajaxkbon.php',
            type: 'POST',
            data: { no_kbon: data.no_kbon_parent || data.no_kbon },
            success: function (html) {
                $('#details').html(html);
            },
            error: function () {
                $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
            }
        });
    });

    // Bulk Second Approve - tiap baris dikirim ke endpoint sesuai data-type-nya
    $('#btnApproveSelected').on('click', function () {
        const items = allCheckboxes().filter(':checked').map(function () {
            return { no_kbon: this.dataset.no_kbon, type: this.dataset.type };
        }).get();
        if (items.length === 0) return;

        Swal.fire({
            title: 'Approve ' + items.length + ' payment voucher?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#btnApproveSelected').prop('disabled', true);

            const requests = items.map(item => $.ajax({
                type: 'POST',
                url: APPROVE_ENDPOINT[item.type] || 'second_approve_kbon.php',
                data: { no_kbon: item.no_kbon }
            }));

            Promise.allSettled(requests).then(results => {
                let success = 0;
                let errors = [];

                results.forEach((r, i) => {
                    const response = r.status === 'fulfilled' ? r.value : '';
                    if (String(response).indexOf('Error') === 0) {
                        errors.push(items[i].no_kbon + ': ' + response);
                    } else if (r.status === 'fulfilled') {
                        success++;
                    } else {
                        errors.push(items[i].no_kbon + ': Request failed');
                    }
                });

                let msg = success + ' Approved Successfully';
                if (errors.length > 0) {
                    msg += '<br><br><b>Gagal:</b><br>' + errors.map(escapeHtml).join('<br>');
                }

                Swal.fire({
                    icon: errors.length > 0 ? 'warning' : 'success',
                    title: 'Selesai',
                    html: msg
                });

                reloadApproveTable();
            });
        });
    });

    // Bulk Cancel - cancel selalu menyasar data-cancel_id (induk no_kbon untuk Installment)
    // dan endpoint sesuai type. Dedupe supaya satu Payment Voucher yang punya beberapa
    // cicilan tercentang tidak dibatalkan berkali-kali.
    $('#btnCancelSelected').on('click', function () {
        const seen = new Set();
        const items = allCheckboxes().filter(':checked').map(function () {
            return { cancel_id: this.dataset.cancel_id, type: this.dataset.type };
        }).get().filter(item => {
            const key = item.type + ':' + item.cancel_id;
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });
        if (items.length === 0) return;

        Swal.fire({
            title: 'Cancel ' + items.length + ' payment voucher?',
            text: 'Status akan diubah menjadi Cancel.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel!',
            cancelButtonText: 'Close'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#btnCancelSelected').prop('disabled', true);

            const requests = items.map(item => $.ajax({
                type: 'POST',
                url: CANCEL_ENDPOINT[item.type] || 'cancelkbon.php',
                data: { no_kbon: item.cancel_id, cancel_user: '<?php echo $user ?>' }
            }));

            Promise.allSettled(requests).then(() => {
                Swal.fire({ icon: 'success', title: 'Successfully Cancelled', timer: 1500, showConfirmButton: false });
                reloadApproveTable();
            });
        });
    });
</script>

</body>

</html>
