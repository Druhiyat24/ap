<?php
include '../header.php';
?>

<style type="text/css">
  label { font-size: 14px; }
  input { font-size: 14px; }

  table.dataTable th,
  table.dataTable td {
    white-space: nowrap;
    vertical-align: middle;
  }

  .dataTables_scrollHeadInner,
  .dataTables_scrollBody table {
    width: 100% !important;
  }

  .select2-container .select2-selection--single {
    height: calc(2.25rem + 2px);
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.25rem;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
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
$doc_num_esc = mysqli_real_escape_string($conn2, $doc_num);

$sqlH = mysqli_query($conn2, "select a.*, concat(a.coa_akun,' ',b.nama_coa) nama_akun from c_petty_cashout_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun where a.no_pco = '$doc_num_esc' and a.reff in ('None','Advance')");
$rowH = mysqli_fetch_assoc($sqlH);

if (!$rowH) {
    echo '<div class="container-fluid mt-4 p-4"><div class="alert alert-danger">Data tidak ditemukan.</div></div>';
    include '../footer.php';
    exit;
}

if ($rowH['status'] !== 'Draft') {
    echo '<div class="container-fluid mt-4 p-4"><div class="alert alert-danger">Data sudah bukan Draft, tidak bisa diedit.</div></div>';
    include '../footer.php';
    exit;
}

$sqlPcAkun = mysqli_query($conn2, "select IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc, kode_cash from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $rowH['coa_akun']) . "'");
$rowPcAkun = mysqli_fetch_assoc($sqlPcAkun);
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM EDIT PETTY CASH OUT</h5>
    </div>

<form id="form-data1" method="post">
    <input type="hidden" id="doc_num1" value="<?= htmlspecialchars($doc_num); ?>">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Doc Number</b></label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($doc_num); ?>">
                </div>


                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active1" id="tgl_active1" class="form-control tanggal" value="<?= date("d-m-Y", strtotime($rowH['tgl_pco'])); ?>" autocomplete="off">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp1" id="nama_supp1" data-live-search="true">
                        <option value="<?= htmlspecialchars($rowH['nama_supp']); ?>" selected><?= htmlspecialchars($rowH['nama_supp']); ?></option>
                        <?php
                        $sql = mysqli_query($conn1, "select * from (select distinct(Supplier) Supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC) a UNION select cc_name as cost_name from b_master_cc where status = 'Active'");
                        while ($row = mysqli_fetch_array($sql)) {
                            if ($row['Supplier'] == '' || $row['Supplier'] == $rowH['nama_supp']) continue;
                            echo "<option value='" . $row['Supplier'] . "'>" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num1" id="ref_num1" class="form-control" value="<?= htmlspecialchars($rowH['reff']); ?>" readonly>
                </div>
                
                <div class="col-md-2 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account1" name="account1" data-live-search="true">
                        <option value="">Select Account</option>
                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            $selected = ($row['id_coa'] == $rowH['coa_akun']) ? 'selected' : '';
                            echo "<option value='" . $row['id_coa'] . "' data-kode1='" . $row['kode_cash'] . "' data-pc1='" . $row['profit_center'] . "' data-namapc1='" . $row['nama_pc'] . "' $selected>" . $row['coa'] . " </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency1" name="currency1" value="<?= htmlspecialchars($rowH['curr']); ?>" readonly>
                    <input type="hidden" class="form-control" id="kode_kas1" name="kode_kas1" value="<?= htmlspecialchars($rowPcAkun['kode_cash'] ?? ''); ?>" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas1" name="profit_center_kas1" value="<?= htmlspecialchars($rowPcAkun['profit_center'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas1" name="amount_kas1" value="<?= number_format($rowH['amount'], 2); ?>">
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan1" id="pesan1" placeholder="descriptions..." required><?= htmlspecialchars($rowH['deskripsi']); ?></textarea>
                </div>

            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="mytablenone"
                        class="table table-striped table-bordered table-hover table-sm nowrap">

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
                                <th style="width:40px;">Cek</th>
                            </tr>
                        </thead>

                        <tbody id="tbody1">
                            <?php
                            $sqlDet = mysqli_query($conn2, "select a.no_coa id_coa, concat(b.no_coa,' ',b.nama_coa) nama_coa, a.profit_center, a.no_costcntr id_cost_center, concat(c.no_cc,' - ',c.cc_name) nama_cc, a.buyer, a.no_ws, a.curr, a.debit t_debit, a.credit t_credit, a.deskripsi keterangan from c_petty_cashout_none a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcntr where a.no_pco = '$doc_num_esc'");
                            while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
                                ?>
                                <tr>
                                <td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

                                <td>
                                <select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
                                    <option value="<?= htmlspecialchars($rowDet['id_coa']); ?>" selected><?= htmlspecialchars($rowDet['nama_coa']); ?></option>
                                    <option value="-">-</option>
                                    <?php
                                    $sqlCoa = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '" . mysqli_real_escape_string($conn1, $rowDet['id_coa']) . "'");
                                    foreach ($sqlCoa as $coa) : ?>
                                    <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                </td>

                                <td>
                                <select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
                                    <?php
                                    $sqlPc = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
                                    while ($fc = mysqli_fetch_assoc($sqlPc)) {
                                        $sel = ($fc['kode_pc'] == $rowDet['profit_center']) ? 'selected' : '';
                                        echo '<option value="' . $fc['kode_pc'] . '" ' . $sel . '>' . $fc['tampil'] . '</option>';
                                    }
                                    ?>
                                </select>
                                </td>

                                <td>
                                <select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
                                    <option value="<?= htmlspecialchars($rowDet['id_cost_center']); ?>" selected><?= htmlspecialchars($rowDet['nama_cc']); ?></option>
                                    <?php
                                    $sqlCc = mysqli_query($conn1, "select no_cc as code_combine, concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '" . mysqli_real_escape_string($conn1, $rowDet['id_cost_center']) . "'");
                                    foreach ($sqlCc as $ccs) : ?>
                                    <option value="<?= $ccs["code_combine"]; ?>"><?= $ccs["cost_name"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                </td>

                                <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" value="<?= htmlspecialchars($rowDet['buyer']); ?>" autocomplete="off"></td>

                                <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" value="<?= htmlspecialchars($rowDet['no_ws']); ?>" autocomplete="off"></td>

                                <td>
                                <select class="form-control selectpicker curr_det1" name="currenc1[]">
                                    <option value="<?= htmlspecialchars($rowDet['curr']); ?>" selected><?= htmlspecialchars($rowDet['curr']); ?></option>
                                    <?php if ($rowDet['curr'] != 'IDR') { ?><option value="IDR">IDR</option><?php } ?>
                                    <?php if ($rowDet['curr'] != 'USD') { ?><option value="USD">USD</option><?php } ?>
                                </select>
                                </td>

                                <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" value="<?= $rowDet['t_debit'] > 0 ? $rowDet['t_debit'] : ''; ?>" <?= $rowDet['t_debit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_amt1(this)" autocomplete="off"></td>

                                <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" value="<?= $rowDet['t_credit'] > 0 ? $rowDet['t_credit'] : ''; ?>" <?= $rowDet['t_credit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_cre1(this)" autocomplete="off"></td>

                                <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" value="<?= htmlspecialchars($rowDet['keterangan']); ?>" autocomplete="off"></td>

                                <td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="11" align="center">

                                    <button type="button" class="btn btn-primary"
                                        onclick="addRow1('tbody1')">
                                        Add Row
                                    </button>

                                    <button type="button" class="btn btn-warning"
                                        onclick="InsertRow1('tbody1')">
                                        Insert Row
                                    </button>

                                    <button type="button" class="btn btn-danger"
                                        onclick="deleteRow1('tbody1')">
                                        Delete Row
                                    </button>

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
                                    <input type="text" class="total-stat-value" id="tot_debit_nag1" name="tot_debit_nag1" readonly>
                                    <input type="hidden" id="h_tot_debit_nag1" name="h_tot_debit_nag1" readonly>
                                </div>
                            </div>

                            <div class="total-stat is-credit">
                                <span class="total-stat-label">Total Credit</span>
                                <div class="total-stat-value-wrap">
                                    <input type="text" class="total-stat-value" id="tot_credit_nag1" name="tot_credit_nag1" readonly>
                                    <input type="hidden" id="h_tot_credit_nag1" name="h_tot_credit_nag1" readonly>
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
                                    <input type="text" class="total-stat-value" id="tot_debit_nak1" name="tot_debit_nak1" readonly>
                                    <input type="hidden" id="h_tot_debit_nak1" name="h_tot_debit_nak1" readonly>
                                </div>
                            </div>

                            <div class="total-stat is-credit">
                                <span class="total-stat-label">Total Credit</span>
                                <div class="total-stat-value-wrap">
                                    <input type="text" class="total-stat-value" id="tot_credit_nak1" name="tot_credit_nak1" readonly>
                                    <input type="hidden" id="h_tot_credit_nak1" name="h_tot_credit_nak1" readonly>
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
                                    <input type="text" class="total-stat-value" id="tot_debit1" name="tot_debit1" readonly>
                                    <input type="hidden" id="h_tot_debit1" name="h_tot_debit1" readonly>
                                </div>
                            </div>

                            <div class="total-stat is-credit">
                                <span class="total-stat-label">Total Credit</span>
                                <div class="total-stat-value-wrap">
                                    <input type="text" class="total-stat-value" id="tot_credit1" name="tot_credit1" readonly>
                                    <input type="hidden" id="h_tot_credit1" name="h_tot_credit1" readonly>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-2">
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan1" id="simpan1"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='petty-cashout.php'"><span class="fa fa-angle-double-left"></span> Back</button>
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
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });
        $('.select2').select2({ theme: 'bootstrap4' });
        $('.selectpicker').selectpicker();
    });

    function getNumber(val) {
        return parseFloat(String(val).replace(/,/g, '')) || 0;
    }

    let coaWajibCC = [];
    $.getJSON('get_coa_wajib_cc.php', function(data){
        coaWajibCC = data;
    });

    $(document).ready(function(){
        hitung_total1();
    });
</script>

<script type="text/javascript">

$('#account1').on('change', function() {

  let kode1 = $(this).find(':selected').data('kode1');
  let pc1 = $(this).find(':selected').data('pc1');
  let namapc1 = $(this).find(':selected').data('namapc1');

  $('#profit_center_kas1').val(pc1);
  $('#currency1').val('IDR');
  $('#kode_kas1').val(kode1);

  hitung_total1();

});

$(document).on('change', '.prof_ctr1', function() {
  const selectedProfCtr = $(this).val();
  const row = $(this).closest('tr');
  const selectedCoa = row.find('select.no_coa1').val() || '-';
  updateCostCenter1(selectedProfCtr, selectedCoa, row);
});

$(document).on('change', '.no_coa1', function() {
  const selectedCoa = $(this).val();
  const row = $(this).closest('tr');
  const selectedProfCtr = row.find('select.prof_ctr1').val() || '-';
  updateCostCenter1(selectedProfCtr, selectedCoa, row);
});

function updateCostCenter1(profCtr, noCoa, row) {
  const costCtrDropdown = $(row).find('.cost_ctr1');

  costCtrDropdown.selectpicker('destroy');
  costCtrDropdown.empty();
  costCtrDropdown.append('<option value="-"> - </option>');
  costCtrDropdown.selectpicker();

  if (profCtr && profCtr !== '-') {
    $.ajax({
      url: 'getCostCenter.php',
      type: 'POST',
      data: { prof_ctr: profCtr, no_coa: noCoa },
      dataType: 'json',
      success: function(response) {
        if (response && response.length > 0) {
          $.each(response, function(index, costCtr) {
            costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
          });
          costCtrDropdown.selectpicker('refresh');
        } else {
          costCtrDropdown.selectpicker('refresh');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
      }
    });
  } else {
    costCtrDropdown.selectpicker('refresh');
  }
}

function addRow1(tableID) {

  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var element = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det1" name="currenc1[]">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

<td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

</tr>
`;

  row.innerHTML = element;

  $('.selectpicker').selectpicker('refresh');

  var headerPC = $('#profit_center_kas1').val();
  if (headerPC) {
    $(row).find('.prof_ctr1').val(headerPC);
    $(row).find('.prof_ctr1').selectpicker('refresh');
  }

}

function deleteRow1(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var deleted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a1[]"]');

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
    }

    $('.selectpicker').selectpicker('refresh');
    hitung_total1();

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function InsertRow1(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var inserted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a1[]"]');

      if (chkbox && chkbox.checked) {

        var element2 = `
<tr>
<td><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker curr_det1" name="currenc1[]">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

<td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

</tr>
`;

        var newRow = table.insertRow(i + 1);
        newRow.innerHTML = element2;
        inserted = true;

        var headerPC = $('#profit_center_kas1').val();
        if (headerPC) {
          $(newRow).find('.prof_ctr1').val(headerPC);
          $(newRow).find('.prof_ctr1').selectpicker('refresh');
        }

      }

    }

    if (!inserted) {
      Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin disisipkan' });
    }

    $('.selectpicker').selectpicker('refresh');

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function formatAngka(x){
  return new Intl.NumberFormat('en-US').format(x);
}

function modal_input_amt1(el){

  let row = $(el).closest('tr');
  let debit  = parseFloat($(el).val()) || 0;
  let creditInput = row.find('input[name="txt_credit1[]"]');

  if(debit > 0){
    creditInput.val(0);
    creditInput.prop('readonly',true);
  }else{
    creditInput.prop('readonly',false);
  }

  hitung_total1();

}

function modal_input_cre1(el){

  let row = $(el).closest('tr');
  let credit = parseFloat($(el).val()) || 0;
  let debitInput = row.find('input[name="txt_amount1[]"]');

  if(credit > 0){
    debitInput.val(0);
    debitInput.prop('readonly',true);
  }else{
    debitInput.prop('readonly',false);
  }

  hitung_total1();

}

function hitung_total1(){

  let debit_nag = 0;
  let credit_nag = 0;

  let debit_nak = 0;
  let credit_nak = 0;

  let rate = 1;

  $('#tbody1 tr').each(function(index){

    let pc = $(this).find('select.prof_ctr1').val();
    let curr = $(this).find('select[name="currenc1[]"]').val();

    let debitVal  = $(this).find('input[name="txt_amount1[]"]').val();
    let creditVal = $(this).find('input[name="txt_credit1[]"]').val();

    let debit  = parseFloat(debitVal)  || 0;
    let credit = parseFloat(creditVal) || 0;

    if(curr === 'USD'){
      debit  = debit  * rate;
      credit = credit * rate;
    }

    if(pc === 'NAG'){
      debit_nag  += debit;
      credit_nag += credit;
    }

    if(pc === 'NAK'){
      debit_nak  += debit;
      credit_nak += credit;
    }

  });

  let header_pc = $('#profit_center_kas1').val();
  let amount = parseFloat($('#amount_kas1').val().replace(/,/g,'')) || 0;
  let eqv = amount * rate;

  if(header_pc === 'NAG'){
    credit_nag += eqv;
  }

  if(header_pc === 'NAK'){
    credit_nak += eqv;
  }

  let grand_debit  = debit_nag + debit_nak;
  let grand_credit = credit_nag + credit_nak;

  $('#tot_debit_nag1').val(formatAngka(debit_nag));
  $('#tot_credit_nag1').val(formatAngka(credit_nag));

  $('#tot_debit_nak1').val(formatAngka(debit_nak));
  $('#tot_credit_nak1').val(formatAngka(credit_nak));

  $('#tot_debit1').val(formatAngka(grand_debit));
  $('#tot_credit1').val(formatAngka(grand_credit));

  $('#h_tot_debit_nag1').val(debit_nag);
  $('#h_tot_credit_nag1').val(credit_nag);

  $('#h_tot_debit_nak1').val(debit_nak);
  $('#h_tot_credit_nak1').val(credit_nak);

  $('#h_tot_debit1').val(grand_debit);
  $('#h_tot_credit1').val(grand_credit);

}

$(document).on('change','.prof_ctr1,.curr_det1',function(){
  hitung_total1();
});

$('#amount_kas1').on('keyup change', function(){
  hitung_total1();
});

$('#simpan1').on('click', function () {

    let source  = $('#nama_supp1').val();
    let account = $('#account1').val();

    if(source == ''){
        Swal.fire('Warning','Supplier tidak boleh kosong','warning');
        return;
    }

    if(account == ''){
        Swal.fire('Warning','Account tidak boleh kosong','warning');
        return;
    }

    if(!$('#pesan1').val().trim()){
        Swal.fire('Warning','Description tidak boleh kosong','warning');
        return;
    }

    let debitNAG  = 0;
    let creditNAG = 0;
    let debitNAK  = 0;
    let creditNAK = 0;

    let error = false;
    let detail = [];

    $("#tbody1 tr").each(function(){

        let tr = $(this);

        let coa = tr.find('select.no_coa1').first().val();
        let pc  = tr.find('select.prof_ctr1').first().val();
        let cc  = tr.find('select.cost_ctr1').first().val();
        let buyer = tr.find('input[name="buyer1[]"]').val();
        let ws    = tr.find('input[name="no_ws1[]"]').val();
        let currency = tr.find('select.curr_det1').first().val();

        let debit  = parseFloat(tr.find('[name="txt_amount1[]"]').val()) || 0;
        let credit = parseFloat(tr.find('[name="txt_credit1[]"]').val()) || 0;
        let desc = tr.find('input[name="keterangan1[]"]').val();
        if(!desc){
          desc = $('#pesan1').val();
        }

        if(!coa || coa == '-'){
            Swal.fire('Warning','COA wajib diisi','warning');
            error = true;
            return false;
        }

        if(!pc || pc == '-'){
            Swal.fire('Warning','Profit Center wajib diisi','warning');
            error = true;
            return false;
        }

        if(coaWajibCC.includes(coa)){
            if(cc == '-' || cc == '' || cc == null){
                Swal.fire('Warning','COA '+coa+' wajib isi Cost Center','warning');
                error = true;
                return false;
            }
        }

        if(pc == 'NAG'){
            debitNAG  += debit;
            creditNAG += credit;
        }

        if(pc == 'NAK'){
            debitNAK  += debit;
            creditNAK += credit;
        }

        detail.push({ coa, pc, cc, buyer, ws, currency, debit, credit, desc });

    });

    if(error) return;

    let header_pc = $('#profit_center_kas1').val();
    let header_debit = parseFloat($('#amount_kas1').val().replace(/,/g,'')) || 0;

    if(header_pc == 'NAG'){
        let totalDebit  = debitNAG;
        let totalCredit = header_debit + creditNAG;

        if(totalDebit != totalCredit){
            Swal.fire('Warning','Journal Nirwana Alabare Garment tidak balance (Header + Detail)','warning');
            return;
        }
    }

    if(header_pc == 'NAK'){
        let totalDebit  = debitNAK;
        let totalCredit = header_debit + creditNAK;

        if(totalDebit != totalCredit){
            Swal.fire('Warning','Journal Nirwana Alabare Knitting tidak balance (Header + Detail)','warning');
            return;
        }
    }

    let header = {
      doc_num   : $('#doc_num1').val(),
      ref       : $('#ref_num1').val(),
      tgl       : $('#tgl_active1').val(),
      supp      : $('#nama_supp1').val(),
      account   : $('#account1').val(),
      currency  : $('#currency1').val(),
      kode_kas  : $('#kode_kas1').val(),
      pc_header : $('#profit_center_kas1').val(),
      amount    : header_debit,
      desc      : $('#pesan1').val()
    };

    let finalData = { header, detail };

    function doUpdateNone1(){

        // Cegah double-submit: tombol dikunci begitu user konfirmasi & mulai
        // proses save, baru dibuka lagi kalau ada error (supaya bisa dicoba ulang).
        $('#simpan1').prop('disabled', true);

        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            type: "POST",
            url: "petty-out/update_pettyout_none.php",
            data: { data: JSON.stringify(finalData) },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'ok') {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message
                    }).then(() => {
                        window.location.href = "petty-cashout.php";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: res.message,
                        showCancelButton: true,
                        confirmButtonText: "Coba Lagi",
                        cancelButtonText: "Tutup"
                    }).then((retry) => {
                        if(retry.isConfirmed){
                            doUpdateNone1();
                        }else{
                            $('#simpan1').prop('disabled', false);
                        }
                    });
                }
            },
            error: function (xhr) {
                console.log("ERROR AJAX:", xhr.responseText);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Terjadi kesalahan server",
                    showCancelButton: true,
                    confirmButtonText: "Coba Lagi",
                    cancelButtonText: "Tutup"
                }).then((retry) => {
                    if(retry.isConfirmed){
                        doUpdateNone1();
                    }else{
                        $('#simpan1').prop('disabled', false);
                    }
                });
            }
        });

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

        if(!result.isConfirmed) return;
        doUpdateNone1();

    });
});
</script>

<?php include '../footer.php'; ?>
