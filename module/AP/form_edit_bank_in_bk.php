<?php include '../header.php' ?>

<style type="text/css">
  label {
    font-size: 13px;
  }

  input {
    font-size: 13px;
  }

  .card-header {
    display: flex !important;
    justify-content: flex-start !important;
    align-items: center !important;
  }

  .card-header h5 {
    margin-left: 0 !important;
  }

  .select2-container {
    width: 100% !important;
  }

  .select2-container .select2-selection--single {
    height: calc(1.5em + .5rem + 2px);
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: calc(1.5em + .5rem);
    font-size: 13px;
    padding-left: 8px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + .5rem + 2px);
  }

  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }
</style>

<?php
$doc_num = base64_decode($_GET['doc_num']);

$sql = mysqli_query($conn2,"select * from tbl_bankin_arcollection where doc_num = '$doc_num'");
$row = mysqli_fetch_array($sql);

$sql_acc_now = mysqli_query($conn1,"select kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.bank_account = '".$row['akun']."'");
$row_acc_now = mysqli_fetch_assoc($sql_acc_now);
$kode_bank_now = $row_acc_now['b_code'] ?? '';
$pc_bank_now = $row_acc_now['kode_pc'] ?? '';

$sql_bk = mysqli_query($conn2,"select no_reff from b_bankin_none where no_bankin = '$doc_num' and no_reff != '' limit 1");
$row_bk = mysqli_fetch_assoc($sql_bk);
$no_bk_now = $row_bk['no_reff'] ?? '';
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <div class="d-flex align-items-center justify-content-start">
        <img src="../../images/note.png" alt="Bank Logo"
        style="width:25px; height:auto; margin-right:10px;">
        <h5 class="mb-0 text-white">FORM EDIT BANK IN</h5>
      </div>
    </div>

    <form id="form-data" method="post">
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="form-row">

            <div class="col-md-3 mb-2">
              <label><b>Doc Number</b></label>
              <input type="text" readonly class="form-control" id="no_bankin" name="no_bankin" value="<?= $doc_num; ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Date</b></label>
              <input type="text" name="tgl_bankin" id="tgl_bankin" class="form-control tanggal"
              value="<?= date("d-m-Y",strtotime($row['date'])); ?>" autocomplete="off">
            </div>

            <div class="col-md-3 mb-2">
              <label><b>Source</b></label>
              <select class="form-control select2" name="nama_supp" id="nama_supp" data-live-search="true">
                <?php
                $customer = $row['customer'];
                echo '<option value="'.$customer.'" selected="selected">'.$customer.'</option>';
                $sql_supp = mysqli_query($conn1,"SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='C' and id_supplier = '799' and Supplier != '$customer'");
                while ($row_supp = mysqli_fetch_array($sql_supp)) {
                    echo '<option value="'.$row_supp['Supplier'].'">'.$row_supp['Supplier'].'</option>';
                }
                ?>
              </select>
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Reference</b></label>
              <input type="text" readonly class="form-control" id="ref_data" name="ref_data" value="<?= $row['ref_data']; ?>">
            </div>

            <?php $profit_center = $row['profit_center']; ?>
            <input type="hidden" name="profit_center" id="profit_center" value="<?= $profit_center; ?>">

            <div class="col-md-3 mb-2">
              <label><b>Account</b></label>
              <select class="form-control select2" id="account" name="account" data-live-search="true">
                <?php
                $akun_now = $row['akun'];
                echo '<option value="'.$akun_now.'" selected="selected">'.$akun_now.'</option>';
                $sql_acc = mysqli_query($conn1,"select bank_name as bank,curr,bank_account as account,nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active' and a.bank_account != '$akun_now'");
                while ($row_acc = mysqli_fetch_assoc($sql_acc)) {
                    echo '<option value="'.$row_acc['account'].'" data-bank="'.$row_acc['bank'].'" data-currency="'.$row_acc['curr'].'" data-kodepc="'.$row_acc['kode_pc'].'" data-kodebank="'.$row_acc['b_code'].'">'.$row_acc['account'].'</option>';
                }
                ?>
              </select>
              <input type="hidden" id="kode_bank_acc" name="kode_bank_acc" value="<?= $kode_bank_now; ?>">
              <input type="hidden" id="pc_bank_acc" name="pc_bank_acc" value="<?= $pc_bank_now; ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Bank</b></label>
              <input type="text" readonly class="form-control" id="bank" name="bank" value="<?= $row['bank']; ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Currency</b></label>
              <input type="text" readonly class="form-control" id="currency" name="currency" value="<?= $row['curr']; ?>">
            </div>
            <div class="col-md-5 mb-2"></div>

            <div class="col-md-3 mb-2">
              <label><b>Bank Out</b></label>
              <select class="form-control select2" name="no_bk" id="no_bk" data-live-search="true">
                <option value="<?= $no_bk_now; ?>" selected="selected"><?= $no_bk_now; ?></option>
                <?php
                $sql_bko = mysqli_query($conn1,"select no_bankout from b_bankout_h where status = 'Approved' and stat_bi != 'Y' and nama_supp = 'PT. NIRWANA ALABARE GARMENT' and no_bankout != '$no_bk_now'");
                while ($row_bko = mysqli_fetch_array($sql_bko)) {
                    echo '<option value="'.$row_bko['no_bankout'].'">'.$row_bko['no_bankout'].'</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-5 mb-2"></div>

            <div class="col-md-3 mb-2">
              <label><b>Amount</b></label>
              <input type="text" class="form-control angka" id="amount" name="amount" style="text-align: right;" placeholder="0.00" value="<?= number_format($row['amount'], 2); ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Rate</b></label>
              <input type="text" class="form-control angka" id="rate" name="rate" style="text-align: right;" placeholder="0.00" value="<?= number_format($row['rate'], 2); ?>" <?= ($row['curr'] === 'IDR') ? 'readonly' : ''; ?>>
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Equivalent IDR</b></label>
              <input type="text" class="form-control" id="eqv_idr" name="eqv_idr" style="text-align: right;" placeholder="0.00" value="<?= number_format($row['eqv_idr'], 2); ?>" readonly>
            </div>
            <div class="col-md-5 mb-2"></div>

            <div class="col-md-8 mb-2">
              <label><b>Description</b></label>
              <textarea class="form-control" style="text-align: left;" cols="30" rows="3" name="pesan" id="pesan" placeholder="descriptions..." required><?= $row['deskripsi']; ?></textarea>
            </div>

          </div>

          <div class="card-body p-2">
            <div class="table-responsive">
              <table id="table-bank-out" class="table table-striped table-bordered table-hover table-sm nowrap">
                <thead class="table-gradient">
                  <tr>
                    <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                    <th style="text-align: center;vertical-align: middle;">COA</th>
                    <th style="text-align: center;vertical-align: middle;">Cost Center</th>
                    <th style="text-align: center;vertical-align: middle;">Reff Document</th>
                    <th style="text-align: center;vertical-align: middle;">Reff Date</th>
                    <th style="text-align: center;vertical-align: middle;">Description</th>
                    <th style="text-align: center;vertical-align: middle;">Total</th>
                    <th style="text-align: center;vertical-align: middle;">Rate</th>
                    <th style="text-align: center;vertical-align: middle;">Total IDR</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <div class="form-row">
            <div class="col-md-3 mt-3 mb-2">
              <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="edit_data" id="edit_data"><span class="fa fa-floppy-o"></span> Save</button>
              <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>
            </div>
          </div>

        </div>
      </div>
    </form>
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
    let datatable;

    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });

        $('.select2').select2({
            theme: 'bootstrap4'
        });

        datatable = $("#table-bank-out").DataTable({
            ordering: false,
            processing: true,
            serverSide: false,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            scrollY: "350px",
            scrollCollapse: true,
            paging: false,
            ajax: {
                url: 'bank_in/ajx_get_data_bank_out.php',
                type: 'POST',
                data: function(d) {
                    d.no_bk = $('#no_bk').val();
                }
            },
            columns: [
                { data: 'nama_pc' },
                { data: 'nama_coa' },
                { data: 'cc_name' },
                { data: 'no_journal' },
                { data: 'tgl_journal' },
                { data: 'keterangan' },
                { data: 'debit' },
                { data: 'rate' },
                { data: 'debit_idr' }
            ],
            columnDefs: [{
                targets: [6, 7, 8],
                className: "text-right",
                render: function(data) {
                    let val = parseFloat(data || 0);
                    return val.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }],
            initComplete: function() {
                let api = this.api();
                setTimeout(function() {
                    api.columns.adjust().draw();
                    $(".dataTables_scrollHeadInner").css("width", "100%");
                    $(".dataTables_scrollHeadInner table").css("width", "100%");
                }, 200);
            }
        });

        $('#no_bk').on('change', function() {
            dataTableReload();
            loadAmount();
        });

        $('#account').on('change', function() {
            let opt = $(this).find(':selected');
            let bank = opt.data('bank');
            let currency = opt.data('currency');
            let kodebank = opt.data('kodebank');
            let kodepc = opt.data('kodepc');

            if (typeof bank === 'undefined') return;

            $('#bank').val(bank);
            $('#currency').val(currency);
            $('#kode_bank_acc').val(kodebank);
            $('#pc_bank_acc').val(kodepc);
            $('#profit_center').val(kodepc);

            if (currency === 'IDR') {
                $('#rate').val('1');
                formatNumber(document.getElementById('rate'));
                $('#rate').prop('readonly', true);
                hitungEqv();
            } else {
                $('#rate').prop('readonly', false);
                hitungEqv();
            }

            loadAmount();
        });

        $(document).on('keyup', '#amount,#rate', function() {
            formatNumber(this);
            hitungEqv();
        });
    });

    function dataTableReload() {
        datatable.ajax.reload(function() {
            datatable.columns.adjust().draw();
        }, false);
    }

    function loadAmount() {
        let no_bk = $('#no_bk').val();
        let currency = $('#currency').val();

        if (!no_bk) return;

        $.ajax({
            url: 'bank_in/get_amount_bank_out.php',
            type: 'POST',
            data: { no_bk: no_bk },
            dataType: 'json',
            success: function(res) {
                let val = (currency === 'IDR') ? parseFloat(res.debit_idr || 0) : parseFloat(res.debit || 0);

                $('#amount').val(val.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                hitungEqv();
            }
        });
    }

    function getNumber(val) {
        return parseFloat(String(val).replace(/,/g, '')) || 0;
    }

    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (value === '') return;

        let parts = value.split('.');
        let integer = parts[0];
        let decimal = parts[1] ? parts[1].substring(0, 4) : '';

        integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        input.value = decimal ? integer + '.' + decimal : integer;
    }

    $(document).on('keyup', '.angka', function() {
        formatNumber(this);
    });

    function hitungEqv() {
        let amount = getNumber($('#amount').val());
        let rate = getNumber($('#rate').val());
        let total = amount * rate;

        $('#eqv_idr').val(total);
        formatNumber(document.getElementById('eqv_idr'));
    }

    $(document).on("click", "#edit_data", function () {
        let doc_num       = $("#no_bankin").val();
        let date          = $("#tgl_bankin").val();
        let ref_data      = $("#ref_data").val();
        let customer      = $("#nama_supp").val();
        let profit_center = $("#profit_center").val();
        let account       = $("#account").val();
        let bank          = $("#bank").val();
        let currency      = $("#currency").val();
        let no_bk         = $("#no_bk").val();
        let amount        = $("#amount").val().replace(/,/g, '');
        let rate          = $("#rate").val().replace(/,/g, '');
        let eqv_idr       = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi     = $("#pesan").val().trim();
        let kode_bank_acc = $("#kode_bank_acc").val();
        let pc_bank_acc   = $("#pc_bank_acc").val();
        var create_user   = '<?php echo $user; ?>';

        if (account === '') {
            Swal.fire('Warning', 'Account tidak boleh kosong', 'warning');
            return;
        }

        if (no_bk === '') {
            Swal.fire('Warning', 'Bank Out tidak boleh kosong', 'warning');
            return;
        }

        if (amount === '' || amount === '0') {
            Swal.fire('Warning', 'Amount tidak boleh kosong', 'warning');
            return;
        }

        if (currency !== 'IDR' && (rate === '' || parseFloat(rate) === 0 || parseFloat(rate) === 1)) {
            Swal.fire('Warning', 'Currency non IDR harus memiliki rate diisi dan tidak boleh 1', 'warning');
            return;
        }

        if (deskripsi === '') {
            Swal.fire('Warning', 'Description tidak boleh kosong', 'warning');
            return;
        }

        let tableData = datatable.rows().data().toArray();

        if (tableData.length === 0) {
            Swal.fire('Warning', 'Data referensi Bank Out tidak ditemukan', 'warning');
            return;
        }

        let refRow = tableData[0];

        Swal.fire({
            title: "Are you sure?",
            text: "The data will be updated.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "update_bi_bankout.php",
                    data: {
                        doc_num: doc_num,
                        date: date,
                        ref_data: ref_data,
                        customer: customer,
                        profit_center: profit_center,
                        akun: account,
                        curr: currency,
                        bank: bank,
                        amount: amount,
                        rate: rate,
                        eqv_idr: eqv_idr,
                        deskripsi: deskripsi,
                        create_user: create_user,
                        kode_bank_acc: kode_bank_acc,
                        pc_bank_acc: pc_bank_acc,
                        no_bk: no_bk,
                        no_journal: refRow.no_journal,
                        tgl_journal: refRow.tgl_journal,
                        no_coa: refRow.no_coa,
                        pc: refRow.profit_center,
                        no_cc: refRow.no_cc,
                        curr_reff: refRow.curr,
                        rate_reff: refRow.rate,
                        total_reff: refRow.debit,
                        total_idr_reff: refRow.debit_idr
                    },
                    success: function (res) {
                        let resTrim = res.trim();
                        if (resTrim === "OK" || resTrim.startsWith("OK|")) {
                            let newDocNum = resTrim.startsWith("OK|") ? resTrim.split("|")[1] : null;
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: newDocNum
                                    ? "Data berhasil diupdate. Nomor dokumen berubah menjadi " + newDocNum
                                    : "Data has been successfully updated!"
                            }).then(() => {
                                window.location.href = "bank-in.php";
                            });
                        } else {
                            Swal.fire("Error", res, "error");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to update : " + xhr.responseText
                        });
                    }
                });
            }
        });
    });
</script>

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

</body>

</html>
