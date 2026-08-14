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

  #mytablenone .form-control {
    width: 100% !important;
  }

  #mytablenone .bootstrap-select {
    width: 100% !important;
  }

  .total-box{
    border:0;
    border-radius:10px;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    height:100%;
    padding:0;
  }

  .total-box .total-box-header{
    padding:12px 16px;
    color:#fff;
    font-weight:700;
    font-size:14px;
  }

  .total-box.tone-nag .total-box-header{
    background:linear-gradient(90deg, #5b7ba8, #7fa0c9);
  }

  .total-box.tone-nak .total-box-header{
    background:linear-gradient(90deg, #4f8a6b, #74ad8f);
  }

  .total-box.tone-all .total-box-header{
    background:linear-gradient(90deg, #4a5578, #6b7699);
  }

  .total-box .total-box-body{
    padding:16px;
    display:flex;
    flex-direction:column;
    gap:14px;
  }

  .total-stat{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    padding-bottom:12px;
    border-bottom:1px dashed #e5e5e5;
  }

  .total-stat:last-child{
    border-bottom:0;
    padding-bottom:0;
  }

  .total-stat-label{
    font-size:12px;
    font-weight:600;
    color:#8a8a8a;
    text-transform:uppercase;
    letter-spacing:.03em;
  }

  .total-stat input.total-stat-value{
    border:0;
    background:transparent;
    padding:0;
    font-size:19px;
    font-weight:700;
    text-align:right;
    width:auto;
    max-width:100%;
    height:auto;
    color:#212529;
  }
</style>

<?php
$doc_num = base64_decode($_GET['doc_num']);

$sql = mysqli_query($conn2,"select * from b_bankout_h where no_bankout = '$doc_num'");
$row = mysqli_fetch_array($sql);

$sql_acc_now = mysqli_query($conn1,"select kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.bank_account = '".$row['akun']."'");
$row_acc_now = mysqli_fetch_assoc($sql_acc_now);
$kode_bank_now = $row_acc_now['b_code'] ?? '';
$pc_bank_now = $row_acc_now['kode_pc'] ?? '';
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <div class="d-flex align-items-center justify-content-start">
        <img src="../../images/note.png" alt="Bank Logo"
        style="width:25px; height:auto; margin-right:10px;">
        <h5 class="mb-0 text-white">FORM EDIT BANK OUT</h5>
      </div>
    </div>

    <form id="form-data" method="post">
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="form-row">

            <div class="col-md-3 mb-2">
              <label><b>Doc Number</b></label>
              <input type="text" readonly class="form-control" id="no_bankout" name="no_bankout" value="<?= $doc_num; ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Date</b></label>
              <input type="text" name="bankout_date" id="bankout_date" class="form-control tanggal"
              value="<?= date("d-m-Y",strtotime($row['bankout_date'])); ?>" autocomplete="off">
            </div>

            <div class="col-md-3 mb-2">
              <label><b>Supplier</b></label>
              <select class="form-control select2" name="nama_supp" id="nama_supp" data-live-search="true">
                <?php
                $customer = $row['nama_supp'];
                echo '<option value="'.$customer.'" selected="selected">'.$customer.'</option>';
                $sql_supp = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' and Supplier != '$customer' order by Supplier ASC");
                while ($row_supp = mysqli_fetch_array($sql_supp)) {
                    echo '<option value="'.$row_supp['Supplier'].'">'.$row_supp['Supplier'].'</option>';
                }
                ?>
              </select>
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Reference</b></label>
              <input type="text" readonly class="form-control" id="reff_doc" name="reff_doc" value="<?= $row['reff_doc']; ?>">
            </div>

            <?php $profit_center = $pc_bank_now; ?>
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
              <input type="text" readonly class="form-control" id="nama_bank" name="nama_bank" value="<?= $row['bank']; ?>">
            </div>

            <div class="col-md-2 mb-2">
              <label><b>Currency</b></label>
              <input type="text" readonly class="form-control" id="valuta" name="valuta" value="<?= $row['curr']; ?>">
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

            <div class="col-md-3 mb-2">
              <label><b>Cash Flow Category</b></label>
              <select class="form-control select2" name="cash_flow" id="cash_flow" data-live-search="true">
                <option value="">Select Cash Flow Category</option>
                <?php
                $id_cash_flow = $row['id_cash_flow'] ?? '';
                $sqlCf = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                while ($rowCf = mysqli_fetch_assoc($sqlCf)) {
                    $selectedCf = ($rowCf['id'] == $id_cash_flow) ? ' selected="selected"' : '';
                    echo '<option value="'.$rowCf['id'].'"'.$selectedCf.'>'.$rowCf['show_subcategory'].'</option>';
                }
                ?>
              </select>
            </div>

            <div class="col-md-5 mb-2">
              <label><b>Description</b></label>
              <textarea class="form-control" style="text-align: left;height: 40px;" cols="30" name="pesan" id="pesan" placeholder="descriptions..." required><?= $row['deskripsi']; ?></textarea>
            </div>

          </div>

          <div class="card-body p-2">
            <div class="table-responsive">
              <table id="mytablenone" class="table table-striped table-bordered table-hover table-sm nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                <thead class="table-gradient">
                  <tr>
                    <th style="width:10px;">-</th>
                    <th>Coa</th>
                    <th>Profit Center</th>
                    <th>Cost Center</th>
                    <th>Buyer</th>
                    <th>WS</th>
                    <th style="width:80px;">Currency</th>
                    <th style="width:120px;">Debit</th>
                    <th style="width:120px;">Credit</th>
                    <th>Description</th>
                    <th style="width:40px;">cek</th>
                  </tr>
                </thead>

                <tbody id="tbody2">
                  <?php
                  $sql_none = mysqli_query($conn2,"select no_bankout no_doc, a.no_coa id_coa, no_costcntr id_cost_center, buyer, no_ws, curr, debit t_debit, credit t_credit, a.deskripsi keterangan, a.profit_center, concat(b.no_coa,' ', b.nama_coa) as nama_coa, CONCAT(a.no_costcntr,' - ',d.cc_name) cc_name, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc from b_bankout_none a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcntr LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where no_bankout = '$doc_num'");

                  while($rowDet = mysqli_fetch_array($sql_none)){
                      $id_coa = $rowDet['id_coa'];
                      $id_cost_center = $rowDet['id_cost_center'];
                      $t_debit = $rowDet['t_debit'];
                      $t_credit = $rowDet['t_credit'];
                      $profitCenterDet = $rowDet['profit_center'];

                      echo '<tr>
                      <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                      <td>
                      <select class="form-control selectpicker no_coa" name="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
                      <option value="'.$rowDet['id_coa'].'">'.$rowDet['nama_coa'].'</option>
                      <option value="-"> - </option>';
                      $sqlCoa = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa != '$id_coa'");
                      while ($cc = mysqli_fetch_assoc($sqlCoa)) {
                          echo '<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>';
                      }
                      echo '</select>
                      </td>

                      <td style="width: 200px;">
                      <select class="form-control selectpicker prof_ctr" name="prof_ctr" style="width: 250px">
                      <option value="'.$rowDet['profit_center'].'">'.$rowDet['nama_pc'].'</option>';
                      $sqlPc = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$profitCenterDet'");
                      while ($fc = mysqli_fetch_assoc($sqlPc)) {
                          echo '<option value="'.$fc["kode_pc"].'"> '.$fc["tampil"].' </option>';
                      }
                      echo '</select>
                      </td>

                      <td style="width: 200px;">
                      <select class="form-control selectpicker cost_ctr" name="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
                      <option value="'.$rowDet['id_cost_center'].'">'.$rowDet['cc_name'].'</option>';
                      if ($rowDet['id_cost_center'] != '-') {
                          echo '<option value="-"> - </option>';
                      }
                      $sqlCc = mysqli_query($conn1,"select no_cc as code_combine,concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '$id_cost_center'");
                      while ($ccs = mysqli_fetch_assoc($sqlCc)) {
                          echo '<option value="'.$ccs["code_combine"].'"> '.$ccs["cost_name"].' </option>';
                      }
                      echo '</select>
                      </td>

                      <td><input style="text-align:left;font-size:14px;" type="text" class="form-control" name="buyer" value="'.$rowDet['buyer'].'" autocomplete="off"></td>
                      <td><input style="text-align:left;font-size:14px;" type="text" class="form-control" name="ws" value="'.$rowDet['no_ws'].'" autocomplete="off"></td>

                      <td>
                      <select class="form-control selectpicker" name="currenc" data-live-search="true">
                      <option value="'.$rowDet['curr'].'">'.$rowDet['curr'].'</option>';
                      echo ($rowDet['curr'] == 'IDR') ? '<option value="USD">USD</option>' : '<option value="IDR">IDR</option>';
                      echo '</select>
                      </td>';

                      if ($t_debit == '0') {
                          echo '<td><input style="text-align:right;font-size:14px;" type="number" min="1" class="form-control" name="txt_amount" oninput="modal_input_amt(this)" autocomplete="off" readonly></td>';
                      } else {
                          echo '<td><input style="text-align:right;font-size:14px;" type="number" min="1" value="'.$t_debit.'" class="form-control" name="txt_amount" oninput="modal_input_amt(this)" autocomplete="off"></td>';
                      }

                      if ($t_credit == '0') {
                          echo '<td><input style="text-align:right;font-size:14px;" type="number" min="1" class="form-control" name="txt_credit" oninput="modal_input_cre(this)" autocomplete="off" readonly></td>';
                      } else {
                          echo '<td><input style="text-align:right;font-size:14px;" type="number" min="1" value="'.$t_credit.'" class="form-control" name="txt_credit" oninput="modal_input_cre(this)" autocomplete="off"></td>';
                      }

                      echo '<td><input style="text-align:left;font-size:14px;" type="text" class="form-control" name="keterangan" value="'.$rowDet['keterangan'].'" autocomplete="off"></td>
                      <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
                      </tr>';
                  }
                  ?>
                </tbody>

                <tfoot>
                  <tr>
                    <td colspan="11" align="center">
                      <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
                      <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Insert Row</button>
                      <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')">Delete Row</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="row mt-1 p-3">

            <!-- NAG -->
            <div class="col-md-4">
              <div class="total-box tone-nag">
                <div class="total-box-header"><i class="fa fa-building"></i> Total PT. Nirwana Alabare Garment</div>
                <div class="total-box-body">
                  <div class="total-stat is-debit">
                    <span class="total-stat-label">Total Debit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_debit_nag" name="tot_debit_nag" readonly>
                      <input type="hidden" id="h_tot_debit_nag" name="h_tot_debit_nag" readonly>
                    </div>
                  </div>
                  <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_credit_nag" name="tot_credit_nag" readonly>
                      <input type="hidden" id="h_tot_credit_nag" name="h_tot_credit_nag" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- NAK -->
            <div class="col-md-4">
              <div class="total-box tone-nak">
                <div class="total-box-header"><i class="fa fa-industry"></i> Total PT. Nirwana Alabare Knitting</div>
                <div class="total-box-body">
                  <div class="total-stat is-debit">
                    <span class="total-stat-label">Total Debit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_debit_nak" name="tot_debit_nak" readonly>
                      <input type="hidden" id="h_tot_debit_nak" name="h_tot_debit_nak" readonly>
                    </div>
                  </div>
                  <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_credit_nak" name="tot_credit_nak" readonly>
                      <input type="hidden" id="h_tot_credit_nak" name="h_tot_credit_nak" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="total-box tone-all">
                <div class="total-box-header"><i class="fa fa-calculator"></i> Grand Total</div>
                <div class="total-box-body">
                  <div class="total-stat is-debit">
                    <span class="total-stat-label">Total Debit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_debit" name="tot_debit" readonly>
                      <input type="hidden" id="h_tot_debit" name="h_tot_debit" readonly>
                    </div>
                  </div>
                  <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                      <input type="text" class="total-stat-value" id="tot_credit" name="tot_credit" readonly>
                      <input type="hidden" id="h_tot_credit" name="h_tot_credit" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="form-row">
            <div class="col-md-3 mt-3 mb-2">
              <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="edit_data" id="edit_data"><span class="fa fa-floppy-o"></span> Save</button>
              <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-out.php'"><span class="fa fa-angle-double-left"></span> Back</button>
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
    let coaWajibCC = [];
    $.getJSON('get_coa_wajib_cc.php', function(data){
        coaWajibCC = data;
    });

    function initializePlugins() {
        $(function () {
            $('.selectpicker').selectpicker();
            $('.tanggal').datepicker({
                format: "dd-mm-yyyy",
                autoclose: true
            });
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    }

    $(document).ready(function() {
        initializePlugins();
        hitung_total();

        $('#mytablenone').DataTable({
            paging: false,
            searching: false,
            scrollCollapse: true,
            fixedHeader: true,
            language: {
                info: "",
                infoEmpty: "",
                infoFiltered: ""
            }
        });

        $("[data-toggle=tooltip]").tooltip();
    });

    $('#account').on('change', function() {
        let opt = $(this).find(':selected');
        let bank = opt.data('bank');
        let curr = opt.data('currency');
        let kodebank = opt.data('kodebank');
        let kodepc = opt.data('kodepc');

        if (typeof bank === 'undefined') return;

        $('#nama_bank').val(bank);
        $('#valuta').val(curr);
        $('#kode_bank_acc').val(kodebank);
        $('#pc_bank_acc').val(kodepc);
        $('#profit_center').val(kodepc);

        if (curr === 'IDR') {
            $('#rate').val('1').prop('readonly', true);
        } else {
            $('#rate').prop('readonly', false);
        }

        let amount = parseFloat($('#amount').val().replace(/,/g, '')) || 0;
        let rate = parseFloat($('#rate').val().replace(/,/g, '')) || 0;
        $('#eqv_idr').val(formatMoney(amount * rate));

        hitung_total();
    });

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
    }

    document.getElementById('amount').addEventListener('input', function(e) {
        let amount = document.getElementById('amount');
        amount.value = amount.value.replace(/,/g, '');
        let rate = document.getElementById('rate');
        rate.value = rate.value.replace(/,/g, '');
        let ttl_idr = amount.value * rate.value;
        $("#eqv_idr").val(formatMoney(ttl_idr));
        hitung_total();
    });

    document.getElementById('rate').addEventListener('input', function(e) {
        let amount = document.getElementById('amount');
        amount.value = amount.value.replace(/,/g, '');
        let rate = document.getElementById('rate');
        rate.value = rate.value.replace(/,/g, '');
        let ttl_idr = amount.value * rate.value;
        $("#eqv_idr").val(formatMoney(ttl_idr));
        hitung_total();
    });

    function modal_input_amt(el){
        let row = $(el).closest('tr');
        let debit = parseFloat($(el).val()) || 0;
        let creditInput = row.find('input[name="txt_credit"]');

        if (debit > 0) {
            creditInput.val(0);
            creditInput.prop('readonly', true);
        } else {
            creditInput.prop('readonly', false);
        }

        hitung_total();
    }

    function modal_input_cre(el){
        let row = $(el).closest('tr');
        let credit = parseFloat($(el).val()) || 0;
        let debitInput = row.find('input[name="txt_amount"]');

        if (credit > 0) {
            debitInput.val(0);
            debitInput.prop('readonly', true);
        } else {
            debitInput.prop('readonly', false);
        }

        hitung_total();
    }

    /* Ganti Currency atau Profit Center di baris detail harus ikut menghitung
       ulang total (konversi ke IDR pakai Rate header untuk baris USD). */
    $(document).on('change', '#tbody2 select[name="currenc"], #tbody2 select[name="prof_ctr"]', function(){
        hitung_total();
    });

    function hitung_total(){

        let debit_nag = 0;
        let credit_nag = 0;
        let debit_nak = 0;
        let credit_nak = 0;

        let rate = parseFloat($('#rate').val().replace(/,/g,'')) || 0;

        $('#tbody2 tr').each(function(){
            let pc = $(this).find('select[name="prof_ctr"]').first().val() || $(this).find('.prof_ctr').first().val();
            let curr = $(this).find('select[name="currenc"]').first().val() || $(this).find('.currenc').first().val();

            let debit = parseFloat($(this).find('input[name="txt_amount"]').val()) || 0;
            let credit = parseFloat($(this).find('input[name="txt_credit"]').val()) || 0;

            if (curr === 'USD') {
                debit = debit * rate;
                credit = credit * rate;
            }

            pc = (pc || '').trim().toUpperCase();

            if (pc === 'NAG') {
                debit_nag += debit;
                credit_nag += credit;
            }

            if (pc === 'NAK') {
                debit_nak += debit;
                credit_nak += credit;
            }
        });

        let header_pc = ($('#profit_center').val() || '').trim().toUpperCase();
        let amount = parseFloat($('#amount').val().replace(/,/g,'')) || 0;
        let eqv = amount * rate;

        $('#eqv_idr').val(formatMoney(eqv));

        if (header_pc === 'NAG') {
            credit_nag += eqv;
        }

        if (header_pc === 'NAK') {
            credit_nak += eqv;
        }

        let grand_debit = debit_nag + debit_nak;
        let grand_credit = credit_nag + credit_nak;

        $('#tot_debit_nag').val(formatMoney(debit_nag));
        $('#tot_credit_nag').val(formatMoney(credit_nag));

        $('#tot_debit_nak').val(formatMoney(debit_nak));
        $('#tot_credit_nak').val(formatMoney(credit_nak));

        $('#tot_debit').val(formatMoney(grand_debit));
        $('#tot_credit').val(formatMoney(grand_credit));

        $('#h_tot_debit_nag').val(debit_nag);
        $('#h_tot_credit_nag').val(credit_nag);

        $('#h_tot_debit_nak').val(debit_nak);
        $('#h_tot_credit_nak').val(credit_nak);

        $('#h_tot_debit').val(grand_debit);
        $('#h_tot_credit').val(grand_credit);
    }

    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);

        var element = `
        <tr>
        <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
        <td>
        <select class="form-control selectpicker no_coa" name="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
        <option value="-">-</option>
        <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
        <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
        <?php endforeach; ?>
        </select>
        </td>
        <td>
        <select class="form-control selectpicker prof_ctr" name="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
        <option value="-"> - </option>
        <?php
        $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
        foreach ($sql3 as $fc) : ?>
        <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
        <?php endforeach; ?>
        </select>
        </td>
        <td>
        <select class="form-control selectpicker cost_ctr" name="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
        <option value="-"> - </option>
        </select>
        </td>
        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" autocomplete="off"></td>
        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" autocomplete="off"></td>
        <td>
        <select class="form-control selectpicker" name="currenc" data-live-search="true">
        <option value="IDR">IDR</option>
        <option value="USD">USD</option>
        </select>
        </td>
        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" name="txt_amount" oninput="modal_input_amt(this)" autocomplete="off"></td>
        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" name="txt_credit" oninput="modal_input_cre(this)" autocomplete="off"></td>
        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" autocomplete="off"></td>
        <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
        </tr>
        `;

        row.innerHTML = element;
        initializePlugins();

        var headerPC = $('#profit_center').val();
        if (headerPC) {
            $(row).find('.prof_ctr').val(headerPC);
            $(row).find('.prof_ctr').selectpicker('refresh');
        }
    }

    function deleteRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            var deleted = false;

            for (var i = rowCount - 1; i >= 0; i--) {
                var row = table.rows[i];
                var chkbox = row.querySelector('input[name="chk_a[]"]');

                if (chkbox && chkbox.checked) {
                    if (rowCount <= 1) {
                        Swal.fire({ icon: 'warning', title: 'Warning', text: 'Tidak dapat menghapus semua baris' });
                        return;
                    }

                    table.deleteRow(i);
                    deleted = true;
                    rowCount--;
                }
            }

            if (!deleted) {
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin dihapus' });
            } else {
                hitung_total();
            }

            $('.selectpicker').selectpicker('refresh');

        } catch (e) {
            console.log(e);
            Swal.fire({ icon: 'error', title: 'Error', text: e.message });
        }
    }

    function InsertRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            var inserted = false;

            for (var i = rowCount - 1; i >= 0; i--) {
                var row = table.rows[i];
                var chkbox = row.querySelector('input[name="chk_a[]"]');

                if (chkbox && chkbox.checked) {

                    var element2 = `
                    <tr>
                    <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                    <td>
                    <select class="form-control selectpicker no_coa" name="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
                    <option value="-">-</option>
                    <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
                    <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                    <?php endforeach; ?>
                    </select>
                    </td>
                    <td>
                    <select class="form-control selectpicker prof_ctr" name="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
                    <option value="-"> - </option>
                    <?php
                    $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    foreach ($sql3 as $fc) : ?>
                    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
                    <?php endforeach; ?>
                    </select>
                    </td>
                    <td>
                    <select class="form-control selectpicker cost_ctr" name="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
                    <option value="-"> - </option>
                    </select>
                    </td>
                    <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" autocomplete="off"></td>
                    <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" autocomplete="off"></td>
                    <td>
                    <select class="form-control selectpicker" name="currenc" data-live-search="true">
                    <option value="IDR">IDR</option>
                    <option value="USD">USD</option>
                    </select>
                    </td>
                    <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" name="txt_amount" oninput="modal_input_amt(this)" autocomplete="off"></td>
                    <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" name="txt_credit" oninput="modal_input_cre(this)" autocomplete="off"></td>
                    <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" autocomplete="off"></td>
                    <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
                    </tr>
                    `;

                    var newRow = table.insertRow(i + 1);
                    newRow.innerHTML = element2;
                    inserted = true;

                    initializePlugins();

                    var headerPC = $('#profit_center').val();
                    if (headerPC) {
                        $(newRow).find('.prof_ctr').val(headerPC);
                        $(newRow).find('.prof_ctr').selectpicker('refresh');
                    }
                }
            }

            if (!inserted) {
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin disisipkan' });
            } else {
                hitung_total();
            }

            $('.selectpicker').selectpicker('refresh');

        } catch (e) {
            console.log(e);
            Swal.fire({ icon: 'error', title: 'Error', text: e.message });
        }
    }

    $(document).on("click", "#edit_data", function () {
        if ($(this).prop('disabled')) return;

        let doc_num       = $("#no_bankout").val();
        let date          = $("#bankout_date").val();
        let ref_data      = $("#reff_doc").val();
        let customer      = $("#nama_supp").val();
        let profit_center = $("#profit_center").val();
        let account       = $("#account").val();
        let curr          = $("#valuta").val();
        let bank          = $("#nama_bank").val();
        let amount        = $("#amount").val().replace(/,/g, '');
        let rate          = $("#rate").val().replace(/,/g, '');
        let eqv_idr       = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi     = $("#pesan").val().trim();
        let h_tot_debit   = $("#h_tot_debit").val();
        let h_tot_credit  = $("#h_tot_credit").val();
        let h_tot_debit_nag  = $("#h_tot_debit_nag").val();
        let h_tot_credit_nag = $("#h_tot_credit_nag").val();
        let h_tot_debit_nak  = $("#h_tot_debit_nak").val();
        let h_tot_credit_nak = $("#h_tot_credit_nak").val();
        let kode_bank_acc = $("#kode_bank_acc").val();
        let pc_bank_acc   = $("#pc_bank_acc").val();
        var create_user   = '<?php echo $user; ?>';

        if (customer === '') {
            Swal.fire('Warning', 'Supplier tidak boleh kosong', 'warning');
            return;
        }

        if (account === '') {
            Swal.fire('Warning', 'Account tidak boleh kosong', 'warning');
            return;
        }

        if (amount === '' || parseFloat(amount) === 0) {
            Swal.fire('Warning', 'Amount tidak boleh kosong', 'warning');
            return;
        }

        if (deskripsi === '') {
            Swal.fire('Warning', 'Description tidak boleh kosong', 'warning');
            return;
        }

        if (curr !== 'IDR' && (rate === '' || parseFloat(rate) === 0 || parseFloat(rate) === 1)) {
            Swal.fire('Warning', 'Currency non IDR harus memiliki rate diisi dan tidak boleh 1', 'warning');
            return;
        }

        let cash_flow = $('#cash_flow').val();
        if (!cash_flow || cash_flow === '') {
            Swal.fire('Warning', 'Cash Flow Category tidak boleh kosong', 'warning');
            return;
        }

        if (Math.abs(parseFloat(h_tot_debit_nag) - parseFloat(h_tot_credit_nag)) > 1) {
            Swal.fire('Warning', 'Journal PT Nirwana Alabare Garment tidak balance', 'warning');
            return;
        }

        if (Math.abs(parseFloat(h_tot_debit_nak) - parseFloat(h_tot_credit_nak)) > 1) {
            Swal.fire('Warning', 'Journal PT Nirwana Alabare Knitting tidak balance', 'warning');
            return;
        }

        if (Math.abs(parseFloat(h_tot_debit) - parseFloat(h_tot_credit)) > 1) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Data belum balance. Periksa kembali detail transaksi."
            });
            return;
        }

        let details = [];
        let validationError = false;

        $("#tbody2 tr").each(function () {
            let coa        = $(this).find("select[name='nomor_coa']").first().val() || $(this).find('.no_coa').first().val();
            let prof_ctr   = $(this).find("select[name='prof_ctr']").first().val() || $(this).find('.prof_ctr').first().val();
            let cost_ctr   = $(this).find("select[name='cost_ctr']").first().val() || $(this).find('.cost_ctr').first().val() || '-';
            let buyer      = $(this).find("input[name='buyer']").val();
            let ws         = $(this).find("input[name='ws']").val();
            let currency   = $(this).find("select[name='currenc']").first().val() || $(this).find('.currenc').first().val();
            let debit      = parseFloat($(this).find("input[name='txt_amount']").val()) || 0;
            let credit     = parseFloat($(this).find("input[name='txt_credit']").val()) || 0;
            let keterangan = $(this).find("input[name='keterangan']").val();

            coa = (coa || '').trim();
            prof_ctr = (prof_ctr || '').trim();
            cost_ctr = (cost_ctr || '').trim();

            if (validationError) return;

            /* baris benar-benar kosong - lewati saja */
            if ((coa === '-' || coa === '') && debit === 0 && credit === 0) {
                return;
            }

            if (!coa || coa === '-') {
                Swal.fire('Warning', 'COA wajib diisi', 'warning');
                validationError = true;
                return false;
            }

            if (!prof_ctr || prof_ctr === '-') {
                Swal.fire('Warning', 'Profit Center wajib diisi', 'warning');
                validationError = true;
                return false;
            }

            if (coaWajibCC.includes(coa) && (cost_ctr === '-' || cost_ctr === '' || cost_ctr === null)) {
                Swal.fire('Warning', 'COA ' + coa + ' wajib isi Cost Center', 'warning');
                validationError = true;
                return false;
            }

            if (debit === 0 && credit === 0) {
                Swal.fire('Warning', 'Debit/Credit harus diisi', 'warning');
                validationError = true;
                return false;
            }

            details.push({
                coa: coa,
                prof_ctr: prof_ctr,
                cost_ctr: cost_ctr,
                buyer: buyer,
                ws: ws,
                currency: currency,
                debit: debit,
                credit: credit,
                keterangan: keterangan
            });
        });

        if (validationError) return;

        if (details.length === 0) {
            Swal.fire('Warning', 'Detail transaksi tidak boleh kosong', 'warning');
            return;
        }

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
                $('#edit_data').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "update_bankout_none.php",
                    data: {
                        doc_num: doc_num,
                        date: date,
                        ref_data: ref_data,
                        customer: customer,
                        profit_center: profit_center,
                        akun: account,
                        curr: curr,
                        bank: bank,
                        amount: amount,
                        rate: rate,
                        eqv_idr: eqv_idr,
                        deskripsi: deskripsi,
                        create_user: create_user,
                        kode_bank_acc: kode_bank_acc,
                        pc_bank_acc: pc_bank_acc,
                        cash_flow: cash_flow,
                        details: JSON.stringify(details)
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
                                window.location.href = "bank-out.php";
                            });
                        } else {
                            $('#edit_data').prop('disabled', false);
                            Swal.fire("Error", res, "error");
                        }
                    },
                    error: function (xhr) {
                        $('#edit_data').prop('disabled', false);
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
