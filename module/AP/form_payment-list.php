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

    /* Collapsible card headers - sama seperti form Payment Voucher AP
       (pv_regular.php dkk): Bootstrap toggle ".collapsed" otomatis dipasang
       di [data-toggle=collapse], jadi chevron bisa dibalik pakai CSS saja. */
    [data-toggle="collapse"] .fa-chevron-up {
        transition: transform 0.2s ease;
    }
    [data-toggle="collapse"].collapsed .fa-chevron-up {
        transform: rotate(180deg);
    }

</style>

<?php
// Payment List menarik Payment Voucher yang sudah "fully approved". Regular/
// Installment perlu status_pvl = 'APPROVED' (sudah melalui Payment Voucher
// List); DP/CBD cukup status 'SECOND APPROVED' di kontrabon_h_dp/kontrabon_h_cbd
// saja. PV yang sudah pernah dimasukkan ke Payment List lain
// (pv_payment_list_det.status != 'Cancel') disembunyikan dari hasil search ini
// - mirip persis pola form_reverse_kontrabon.php.
include 'pv_data_functions.php';
?>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#pl_header_card_body" aria-expanded="true" aria-controls="pl_header_card_body">
        <span><i class="fab fa-wpforms mr-2"></i> FORM PAYMENT LIST</span>
        <i class="fa fa-chevron-up"></i>
    </div>
    <div class="collapse show" id="pl_header_card_body">
    <div class="card-body p-2">
    <form id="form-data" method="post">
        <div class="form-row mt-2">
            <div class="col-md-3 mb-3">
                <label for="pl_number"><b>No Payment List</b></label>
                <?php
                // Penomoran tidak lagi per Profit Center (PL sekarang bisa multi
                // Profit Center & multi Supplier dalam satu pengajuan) - format
                // PL-PV/{MMYY}/{00001}, urutan reset tiap bulan baru.
                $sql = mysqli_query($conn2, "select CONCAT('PL-PV/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/', LPAD(COALESCE(MAX(CAST(RIGHT(pl_number, 5) AS UNSIGNED)), 0) + 1, 5, '0')) nomor from pv_payment_list_h WHERE pl_number LIKE CONCAT('PL-PV/', DATE_FORMAT(CURRENT_DATE(), '%m%y'), '/%')");
                $row = mysqli_fetch_array($sql);
                $kodepay = $row['nomor'];
                echo '<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="pl_number" name="pl_number" value="' . htmlspecialchars($kodepay) . '">';
                ?>
            </div>

            <div class="col-md-2 mb-3">
                <label for="pl_date"><b>Payment List Date <i style="color: red;">*</i></b></label>
                <input type="text" style="font-size: 14px;" name="pl_date" id="pl_date" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" readonly>
            </div>

            <div class="col-md-3 mb-3">
                <label for="from_account"><b>From Account <i style="color: red;">*</i></b></label>
                <select class="form-control form-control-sm select2bs4" name="from_account" id="from_account" data-dropup-auto="false">
                    <option value="">-- Pilih Rekening --</option>
                    <?php
                    $sqlBank = mysqli_query($conn2, "SELECT bank_account, IF(bank_name like '%MANDIRI%', 'MANDIRI', bank_name) bank_name, curr FROM b_masterbank where status = 'Active' ORDER BY id ASC");
                    $savedFromAccount = $_POST['from_account'] ?? '';
                    while ($rowBank = mysqli_fetch_assoc($sqlBank)) {
                        $sel = ($savedFromAccount === $rowBank['bank_account']) ? ' selected' : '';
                        $label = htmlspecialchars($rowBank['bank_account'] . ' - ' . $rowBank['bank_name'] . ' (' . $rowBank['curr'] . ')');
                        echo '<option value="' . htmlspecialchars($rowBank['bank_account']) . '"' . $sel . '>' . $label . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4 mb-3"></div>

            <div class="col-md-5 mb-3">
                <label for="txt_search"><b>Search Criteria</b></label>
                <div class="input-group input-group-sm">
                    <input type="text"
                    readonly
                    class="form-control"
                    style="font-size: 14px;"
                    id="txt_search"
                    value="<?php
                    $type_pv_h = isset($_POST['h_type_pv']) ? $_POST['h_type_pv'] : null;
                    $nama_supp_h = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : null;
                    if (!empty($type_pv_h)) {
                        echo 'Type: ' . $type_pv_h . ($nama_supp_h ? ' | Supplier: ' . $nama_supp_h : '');
                    }
                    ?>">
                    <button type="button" class="btn btn-info btn-sm" id="mysearch"><i class="fas fa-search"></i> Select</button>
                </div>
            </div>

        </div>
    </form>
    </div>
    </div>
    </div>

    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
      <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#pl_table_card_body" aria-expanded="true" aria-controls="pl_table_card_body">
          <span><i class="fa fa-list-alt"></i> Eligible Payment Vouchers</span>
          <i class="fa fa-chevron-up"></i>
      </div>
      <div class="collapse show" id="pl_table_card_body">
      <div class="card-body p-2">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive p-1">
                <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                    <thead>
                        <tr class="text-white" style="background-color: #1E3A8A;">
                            <th style="width:10px;"><input type="checkbox" id="chkPlSelectAll"></th>
                            <th style="width:80px;">Type</th>
                            <th style="width:120px;">No Payment Voucher</th>
                            <th style="width:90px;">PV Date</th>
                            <th style="width:90px;">Profit Center</th>
                            <th style="width:120px;">Supplier</th>
                            <th style="width:50px;">Curr</th>
                            <th style="width:100px;">Total</th>
                            <th style="width:150px;">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $type_pv = isset($_POST['h_type_pv']) ? $_POST['h_type_pv'] : null;
                        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : null;
                        $start_date = !empty($_POST['start_date']) ? date("Y-m-d", strtotime($_POST['start_date'])) : '';
                        $end_date = !empty($_POST['end_date']) ? date("Y-m-d", strtotime($_POST['end_date'])) : '';

                        if (!empty($type_pv)) {
                            $filters = [
                                'nama_supp'   => !empty($nama_supp) ? $nama_supp : 'ALL',
                                'status'      => 'ALL',
                                'filter_date' => 'tgl_kbon',
                                'start_date'  => $start_date,
                                'end_date'    => $end_date,
                            ];

                            $rowsAll = [];
                            if ($type_pv === 'Regular-Installment' || $type_pv === 'ALL') {
                                $rowsAll = array_merge($rowsAll, getDataRegular($conn1, $conn2, $filters, '1', '1', ''));
                                $rowsAll = array_merge($rowsAll, getDataInstallment($conn1, $conn2, $filters, '1', '1', ''));
                                $rowsAll = array_merge($rowsAll, getDataSaldoAwal($conn2, $filters));
                            }
                            if ($type_pv === 'Biaya-DP-CBD' || $type_pv === 'ALL') {
                                $rowsAll = array_merge($rowsAll, getDataBiaya($conn2, $filters));
                                $rowsAll = array_merge($rowsAll, getDataDp($conn1, $conn2, $filters, '1', '1', ''));
                                $rowsAll = array_merge($rowsAll, getDataCbd($conn1, $conn2, $filters, '1', '1', ''));
                            }

                            // Hanya PV yang sudah fully approved yang boleh masuk Payment List.
                            // Biaya tidak melalui PVL sehingga cukup status 'Approved' saja.
                            // DP/CBD cukup status 'SECOND APPROVED' di kontrabon_h_cbd/kontrabon_h_dp,
                            // tidak perlu lagi status_pvl = 'APPROVED'.
                            // Regular/Installment tetap perlu status_pvl = 'APPROVED' (sudah melalui
                            // Payment Voucher List).
                            $rowsAll = array_filter($rowsAll, function ($r) {
                                if ($r['type'] === 'Biaya') {
                                    return $r['status'] === 'Approved';
                                }
                                if (in_array($r['type'], ['DP', 'CBD'])) {
                                    return $r['status'] === 'SECOND APPROVED';
                                }
                                if (!in_array($r['status'], ['SECOND APPROVED', 'Approved'])) return false;
                                return strtoupper(trim($r['status_pvl'] ?? '')) === 'APPROVED';
                            });

                            // Regular/Installment/DP/CBD sebelum 1 Juli 2026 sudah masuk saldo awal,
                            // tidak boleh ditarik lagi lewat form ini.
                            $rowsAll = array_filter($rowsAll, function ($r) {
                                if (in_array($r['type'], ['Regular', 'Installment', 'DP', 'CBD'])) {
                                    return substr($r['create_date'] ?? '', 0, 10) >= '2026-07-01';
                                }
                                return true;
                            });

                            // Exclude PV yang sudah pernah dimasukkan ke Payment List ATAU
                            // Payment Voucher List lain (belum dibatalkan) - supaya tidak ada
                            // PV yang diklaim dua kali oleh dua feature "list" ini.
                            $alreadyListed = [];
                            $sqlListed = mysqli_query($conn2, "select no_kbon from pv_payment_list_det where status != 'Cancel'");
                            while ($rowListed = mysqli_fetch_assoc($sqlListed)) {
                                $alreadyListed[$rowListed['no_kbon']] = true;
                            }
                            
                            $rowsAll = array_filter($rowsAll, function ($r) use ($alreadyListed) {
                                return !isset($alreadyListed[$r['no_kbon']]);
                            });

                            foreach ($rowsAll as $r) {
                                $totalRaw = (float) str_replace(',', '', $r['total']);
                                $pc = !empty($r['profit_center']) ? $r['profit_center'] : '-';
                                echo '<tr>
                                <td style="width:10px;"><input type="checkbox" class="chkPl" name="select_pl[]" value=""></td>
                                <td style="width:80px;text-align:left;" value="' . htmlspecialchars($r['type']) . '">' . htmlspecialchars($r['type']) . '</td>
                                <td style="width:120px;text-align:left;" value="' . htmlspecialchars($r['no_kbon']) . '">' . htmlspecialchars($r['no_kbon']) . '</td>
                                <td style="width:90px;text-align:left;" value="' . htmlspecialchars($r['tgl_kbon']) . '">' . htmlspecialchars($r['tgl_kbon']) . '</td>
                                <td style="width:90px;text-align:left;" value="' . htmlspecialchars($pc) . '">' . htmlspecialchars($pc) . '</td>
                                <td style="width:120px;text-align:left;" value="' . htmlspecialchars($r['nama_supp']) . '">' . htmlspecialchars($r['nama_supp']) . '</td>
                                <td style="width:50px;text-align:left;" value="' . htmlspecialchars($r['curr']) . '">' . htmlspecialchars($r['curr']) . '</td>
                                <td style="width:100px;text-align:right;" value="' . $totalRaw . '">' . number_format($totalRaw, 2) . '</td>
                                <td style="width:150px;"><input type="text" class="form-control form-control-sm txt-row-keterangan" placeholder="Optional" readonly></td>
                                </tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="pl_summary_box" class="mt-3 p-2" style="background:#f8f9fa;border:1px solid #e2e8f0;border-radius:6px;font-size:13px;">
                <b>Total per Profit Center:</b>
                <div id="pl_summary_content" class="mt-1 text-muted">Belum ada Payment Voucher yang dicentang.</div>
            </div>

            <form id="form-simpan">
                <div class="form-row col mt-3">
                    <div class="col-md-3 mb-3">
                        <button type="button" style="border-radius: 6px" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>
                        <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='payment-list.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
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
        <h4 class="modal-title text-white" id="Heading">Search Criteria</h4>
      </div>

      <div class="modal-body">
        <form id="modal-form" method="post">

          <div class="form-group mb-3">
            <label for="type_pv"><b>Type Document</b></label>
            <select class="form-control form-control-sm select2bs4" name="h_type_pv" id="type_pv" data-dropup-auto="false">
                <?php $selType = $_POST['h_type_pv'] ?? ''; ?>
                <option value="ALL" <?php echo ($selType === 'ALL' || $selType === '') ? 'selected' : ''; ?>>ALL</option>
                <option value="Regular-Installment" <?php echo ($selType === 'Regular-Installment') ? 'selected' : ''; ?>>Regular-Installment</option>
                <option value="Biaya-DP-CBD" <?php echo ($selType === 'Biaya-DP-CBD') ? 'selected' : ''; ?>>Biaya-DP-CBD</option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control form-control-sm selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php echo (($_POST['nama_supp'] ?? 'ALL') === 'ALL') ? ' selected="selected"' : ''; ?>>ALL</option>
                <?php
                $sqlSupp = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($rowSupp = mysqli_fetch_array($sqlSupp)) {
                    $dataSupp = $rowSupp['Supplier'];
                    $isSelected = (($_POST['nama_supp'] ?? '') === $dataSupp) ? ' selected="selected"' : '';
                    echo '<option value="' . $dataSupp . '"' . $isSelected . '>' . $dataSupp . '</option>';
                }
                ?>
            </select>
          </div>

          <div class="form-group">
            <label><b>Payment Voucher Date</b></label>
            <div class="d-flex align-items-center gap-2">
               <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="start_date" name="start_date"
               value="<?php echo !empty($_POST['start_date']) ? $_POST['start_date'] : date("d-m-Y"); ?>"
               placeholder="Tanggal Awal">

               <span class="mx-2">-</span>

               <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="end_date" name="end_date"
               value="<?php echo !empty($_POST['end_date']) ? $_POST['end_date'] : date("d-m-Y"); ?>"
               placeholder="Tanggal Akhir">
            </div>
          </div>

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
            <i class="fa fa-times"></i> Close
        </button>
        <button type="submit" form="modal-form" id="send" name="send" class="btn btn-warning">
            <i class="fa fa-search" aria-hidden="true"></i> Search
        </button>
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

<script type="text/javascript">
    $(document).ready(function () {
        $("#mysearch").on("click", function () {
            $("#mymodal").modal("show");
        });
    });

</script>

<script>
   $(function() {
      $('.select2').select2()
      $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
  });

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
    $(document).ready(function() {
        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal3').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    // Total per Profit Center (+ Currency) - dihitung ulang dari baris yang
    // sedang dicentang setiap kali centang berubah, supaya selalu mencerminkan
    // apa yang akan benar-benar disimpan saat Save ditekan.
    function recalcPlSummary() {
        var sums = {};

        $("input[name='select_pl[]']:checked").each(function () {
            var tr = $(this).closest("tr");
            var pc   = tr.find('td:eq(4)').attr('value') || '-';
            var curr = tr.find('td:eq(6)').attr('value') || '-';
            var total = parseFloat(tr.find('td:eq(7)').attr('value')) || 0;
            var key = pc + '|' + curr;

            sums[key] = (sums[key] || 0) + total;
        });

        var keys = Object.keys(sums);
        if (keys.length === 0) {
            $('#pl_summary_content').html('Belum ada Payment Voucher yang dicentang.').addClass('text-muted');
            return;
        }

        $('#pl_summary_content').removeClass('text-muted');
        var html = keys.sort().map(function (key) {
            var parts = key.split('|');
            return '<div><b>' + parts[0] + '</b> (' + parts[1] + '): ' + sums[key].toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</div>';
        }).join('');
        $('#pl_summary_content').html(html);
    }

    $(document).on('change', "input[name='select_pl[]']", function () {
        $(this).closest('tr').find('input.txt-row-keterangan').prop('readonly', !this.checked);
        recalcPlSummary();
    });

    $(document).on('change', '#chkPlSelectAll', function () {
        $("input[name='select_pl[]']").prop('checked', this.checked);
        $("input.txt-row-keterangan").prop('readonly', !this.checked);
        recalcPlSummary();
    });

    // Fungsi simpan — dipanggil dari tombol Save maupun tombol "Coba Lagi" setelah error
    function doSavePl(rows, totalRows, pl_date, from_account, create_user) {
        $.ajax({
            type: 'POST',
            url: 'save_pv_payment_list.php',
            data: {
                pl_date:      pl_date,
                from_account: from_account,
                create_user:  create_user,
                rows:         JSON.stringify(rows)
            },
            cache: false,
            success: function (res) {
                if (String(res).indexOf('Error') === 0) {
                    // Rollback terjadi — tawarkan Coba Lagi tanpa ceklis ulang
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        html: res + '<br><br>Data belum tersimpan. Klik <b>Coba Lagi</b> untuk mencoba kembali.',
                        showCancelButton: true,
                        confirmButtonText: 'Coba Lagi',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#1e3a8a'
                    }).then(function (r) {
                        if (r.isConfirmed) doSavePl(rows, totalRows, pl_date, from_account, create_user);
                    });
                    return;
                }
                // Server mengembalikan "OK:PL-PV/0726/00001" — ambil nomor aktual
                var actualNumber = res.indexOf(':') >= 0 ? res.split(':').slice(1).join(':') : res;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan',
                    html: '<b>' + totalRows + ' baris</b> Payment Voucher berhasil disimpan ke Payment List <b>' + actualNumber + '</b>.'
                }).then(function () {
                    window.location = 'payment-list.php';
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: xhr.responseText + '<br><br>Klik <b>Coba Lagi</b> untuk mencoba kembali.',
                    showCancelButton: true,
                    confirmButtonText: 'Coba Lagi',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#1e3a8a'
                }).then(function (r) {
                    if (r.isConfirmed) doSavePl(rows, totalRows, pl_date, from_account, create_user);
                });
            }
        });
    }

    $("#form-simpan").on("click", "#simpan", function () {
        var pl_date      = $("#pl_date").val();
        var from_account = $("#from_account").val();
        var create_user  = '<?php echo $user ?>';

        var checked = $("input[name='select_pl[]']:checked");

        if (checked.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please check at least 1 Payment Voucher.' });
            return;
        }

        if (!from_account) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Silahkan pilih From Account terlebih dahulu.' });
            return;
        }

        // Kumpulkan semua baris yang dicentang sebelum konfirmasi
        var rows = [];
        checked.each(function () {
            var tr = $(this).closest("tr");
            rows.push({
                type_pv:      tr.find('td:eq(1)').attr('value'),
                no_kbon:      tr.find('td:eq(2)').attr('value'),
                tgl_kbon:     tr.find('td:eq(3)').attr('value'),
                profit_center:tr.find('td:eq(4)').attr('value'),
                nama_supp:    tr.find('td:eq(5)').attr('value'),
                curr:         tr.find('td:eq(6)').attr('value'),
                total:        tr.find('td:eq(7)').attr('value'),
                deskripsi:    tr.find('td:eq(8) input.txt-row-keterangan').val(),
                create_user:  create_user
            });
        });

        var totalRows = rows.length;

        // Konfirmasi sebelum simpan — tampilkan jumlah baris yang akan disimpan
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi Simpan',
            html: 'Anda akan menyimpan <b>' + totalRows + ' baris</b> Payment Voucher ke Payment List baru.<br>Lanjutkan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1e3a8a'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            doSavePl(rows, totalRows, pl_date, from_account, create_user);
        });
    });
</script>

</body>

</html>
