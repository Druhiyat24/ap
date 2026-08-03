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

  #mytablecashout .form-control {
    width: 100% !important;
  }

  #mytablecashout .bootstrap-select {
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

$sql = mysqli_query($conn2, "select a.*, CONCAT(a.coa_akun,' ',b.nama_coa) nama_akun, IF(a.coa_akun = '1.01.11','NAK','NAG') profit_center_calc, IF(a.coa_akun = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc, b.kode_cash from c_petty_cashin_h a INNER JOIN mastercoa_v2 b on b.no_coa = a.coa_akun where no_pci = '$doc_num'");
$row = mysqli_fetch_array($sql);
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM EDIT PETTY CASH IN</h5>
    </div>

    <div class="card-body p-4">

      <form id="form-data1" method="post">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="form-row">

              <div class="col-md-3 mb-2">
                <label><b>Doc Number</b></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($doc_num); ?>" readonly>
                <input type="hidden" id="doc_num" name="doc_num" value="<?= htmlspecialchars($doc_num); ?>">
              </div>

              <div class="col-md-2 mb-2">
                <label><b>Date</b></label>
                <input type="text" name="tgl_active1" id="tgl_active1" class="form-control tanggal" value="<?= date("d-m-Y", strtotime($row['tgl_pci'])); ?>" autocomplete="off">
              </div>

              <div class="col-md-3 mb-2">
                <label><b>Profit Center</b></label>
                <input type="text" class="form-control" id="profit_center_kas_show1" value="<?= htmlspecialchars($row['nama_pc']); ?>" readonly>
                <input type="hidden" id="profit_center_kas1" name="profit_center_kas1" value="<?= htmlspecialchars($row['profit_center_calc']); ?>">
              </div>

              <div class="col-md-2 mb-2">
                <label><b>Reference</b></label>
                <input type="text" class="form-control" value="Cash Out" readonly>
              </div>

              <div class="col-md-3 mb-2">
                <label><b>Reff Document</b></label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['reff_doc']); ?>" readonly>
                <input type="hidden" id="reff_number1" name="reff_number1" value="<?= htmlspecialchars($row['reff_doc']); ?>">
              </div>

              <div class="col-md-2 mb-2">
                <label><b>Other Document</b></label>
                <input type="text" class="form-control angka" id="oth_doc1" name="oth_doc1" value="<?= htmlspecialchars($row['oth_doc']); ?>">
              </div>

              <div class="col-md-7 mb-2"></div>


              <div class="col-md-3 mb-2">
                <label><b>Account</b></label>
                <select class="form-control select2" id="account1" name="account1" data-live-search="true">
                  <option value="<?= htmlspecialchars($row['coa_akun']); ?>" selected data-kode1="<?= htmlspecialchars($row['kode_cash']); ?>" data-pc1="<?= htmlspecialchars($row['profit_center_calc']); ?>" data-namapc1="<?= htmlspecialchars($row['nama_pc']); ?>"><?= htmlspecialchars($row['nama_akun']); ?></option>
                  <?php
                  $sqlacc = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%' and no_coa != '" . $row['coa_akun'] . "'");
                  while ($racc = mysqli_fetch_assoc($sqlacc)) {
                      echo "<option value='" . $racc['id_coa'] . "' data-kode1='" . $racc['kode_cash'] . "' data-pc1='" . $racc['profit_center'] . "' data-namapc1='" . $racc['nama_pc'] . "'>" . $racc['coa'] . " </option>";
                  }
                  ?>
                </select>
              </div>

              <div class="col-md-2 mb-2">
                <label><b>Currency</b></label>
                <input type="text" class="form-control" id="currency1" name="currency1" value="<?= htmlspecialchars($row['curr']); ?>" readonly>
              </div>


              <div class="col-md-2 mb-2">
                <label><b>Amount</b></label>
                <input type="text" class="form-control angka" id="amount_kas1" name="amount_kas1" value="<?= number_format($row['amount'], 0); ?>">
              </div>

              <div class="col-md-3 mb-2"> </div>

              <div class="col-md-8 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" class="form-control" name="pesan1" id="pesan1" placeholder="descriptions..." required><?= htmlspecialchars($row['deskripsi']); ?></textarea>
              </div>

            </div>

            <div class="card-body p-2">
              <div class="table-responsive">
                <table id="mytablecashout" class="table table-striped table-bordered table-hover table-sm nowrap">

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
                    $sql_det = mysqli_query($conn1, "select a.no_coa id_coa, a.profit_center, a.no_costcenter id_cost_center, a.buyer, a.no_ws, a.curr, a.debit t_debit, a.credit t_credit, a.deskripsi keterangan, concat(b.no_coa,' ',b.nama_coa) nama_coa, IF(a.no_costcenter = '-' or a.no_costcenter is null,'-',c.cc_name) cc_name, IF(a.profit_center = '-' or a.profit_center is null,'-',CONCAT(mp.id_pc,' - ',mp.nama_pc)) nama_pc from c_petty_cashin_none a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcenter left join master_pc mp on mp.kode_pc = a.profit_center where a.no_pci = '$doc_num'");

                    while ($d = mysqli_fetch_assoc($sql_det)) {
                        $id_coa = $d['id_coa'];
                        $id_cost_center = $d['id_cost_center'];
                        $profit_center = $d['profit_center'];
                        ?>
                    <tr>
                      <td><input type="checkbox" name="select1[]" value="" checked disabled></td>

                      <td>
                        <select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5">
                          <option value="<?= $id_coa; ?>"><?= $d['nama_coa']; ?></option>
                          <option value="-"> - </option>
                          <?php
                          $sqlc = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '$id_coa'");
                          foreach ($sqlc as $cc) : ?>
                          <option value="<?= $cc["id_coa"]; ?>"><?= $cc["coa"]; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <td>
                        <select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%">
                          <option value="<?= $profit_center; ?>"><?= $d['nama_pc']; ?></option>
                          <?php
                          $sql3 = mysqli_query($conn1, "select kode_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active' and kode_pc != '$profit_center'");
                          foreach ($sql3 as $fc) : ?>
                          <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <td>
                        <select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%">
                          <option value="<?= $id_cost_center; ?>"><?= $d['cc_name']; ?></option>
                          <?php if ($id_cost_center != '-') { ?>
                          <option value="-"> - </option>
                          <?php } ?>
                          <?php
                          $sql2 = mysqli_query($conn1, "select no_cc as code_combine, concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status='Active' and no_cc != '$id_cost_center'");
                          foreach ($sql2 as $ccs) : ?>
                          <option value="<?= $ccs["code_combine"]; ?>"><?= $ccs["cost_name"]; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>

                      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" value="<?= htmlspecialchars($d['buyer']); ?>" autocomplete="off"></td>

                      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" value="<?= htmlspecialchars($d['no_ws']); ?>" autocomplete="off"></td>

                      <td>
                        <select class="form-control selectpicker curr_det1" name="currenc1[]">
                          <option value="<?= $d['curr']; ?>"><?= $d['curr']; ?></option>
                        </select>
                      </td>

                      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" value="<?= $d['t_debit']; ?>" oninput="modal_input_amt1(this)" <?= $d['t_debit'] == 0 ? 'readonly' : ''; ?> autocomplete="off"></td>

                      <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" value="<?= $d['t_credit']; ?>" oninput="modal_input_cre1(this)" <?= $d['t_credit'] == 0 ? 'readonly' : ''; ?> autocomplete="off"></td>

                      <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" value="<?= htmlspecialchars($d['keterangan']); ?>" autocomplete="off"></td>

                      <td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>
                    </tr>
                    <?php } ?>
                  </tbody>

                  <tfoot>
                    <tr>
                      <td colspan="11" align="center">
                        <button type="button" class="btn btn-primary" onclick="addRow1('tbody1')">Add Row</button>
                        <button type="button" class="btn btn-warning" onclick="InsertRow1('tbody1')">Insert Row</button>
                        <button type="button" class="btn btn-danger" onclick="deleteRow1('tbody1')">Delete Row</button>
                      </td>
                    </tr>
                  </tfoot>

                </table>
              </div>
            </div>

            <div class="row mt-1 p-3">

              <div class="col-md-4">
                <div class="total-box tone-nag">
                  <div class="total-box-header"><i class="fa fa-building"></i> Total PT. Nirwana Alabare Garment</div>
                  <div class="total-box-body">
                    <div class="total-stat is-debit">
                      <span class="total-stat-label">Total Debit</span>
                      <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_debit_nag1" readonly>
                        <input type="hidden" id="h_tot_debit_nag1" readonly>
                      </div>
                    </div>
                    <div class="total-stat is-credit">
                      <span class="total-stat-label">Total Credit</span>
                      <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nag1" readonly>
                        <input type="hidden" id="h_tot_credit_nag1" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="total-box tone-nak">
                  <div class="total-box-header"><i class="fa fa-industry"></i> Total PT. Nirwana Alabare Knitting</div>
                  <div class="total-box-body">
                    <div class="total-stat is-debit">
                      <span class="total-stat-label">Total Debit</span>
                      <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_debit_nak1" readonly>
                        <input type="hidden" id="h_tot_debit_nak1" readonly>
                      </div>
                    </div>
                    <div class="total-stat is-credit">
                      <span class="total-stat-label">Total Credit</span>
                      <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nak1" readonly>
                        <input type="hidden" id="h_tot_credit_nak1" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit1" readonly>
                        <input type="hidden" id="h_tot_debit1" readonly>
                      </div>
                    </div>
                    <div class="total-stat is-credit">
                      <span class="total-stat-label">Total Credit</span>
                      <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit1" readonly>
                        <input type="hidden" id="h_tot_credit1" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <div class="form-row">
              <div class="col-md-3 mt-3 mb-2">
                <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" id="simpan1"><span class="fa fa-floppy-o"></span> Save</button>
                <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" onclick="location.href='petty-cashin.php'"><span class="fa fa-angle-double-left"></span> Back</button>
              </div>
            </div>

          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  $(function() {
    $('.selectpicker').selectpicker();
    $('.select2').select2({ theme: 'bootstrap4' });
    $('.tanggal').datepicker({
      format: "dd-mm-yyyy",
      autoclose: true
    });
  });

  $('#account1').on('change', function() {

    let kode1 = $(this).find(':selected').data('kode1');
    let pc1 = $(this).find(':selected').data('pc1');
    let namapc1 = $(this).find(':selected').data('namapc1');

    $('#profit_center_kas_show1').val(namapc1);
    $('#profit_center_kas1').val(pc1);
    $('#currency1').val('IDR');

    hitung_total1();

  });

  function formatAngka(x) {
    return new Intl.NumberFormat('en-US').format(x);
  }

  function addRow1(tableID) {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

    var element = `
<tr>
<td><input type="checkbox" name="select1[]" value="" checked disabled></td>

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
$sql3 = mysqli_query($conn1, "select kode_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
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

          var element1 = `
<tr>
<td><input type="checkbox" name="select1[]" value="" checked disabled></td>

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
$sql3 = mysqli_query($conn1, "select kode_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
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
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" autocomplete="off"></td>

<td><input name="chk_a1[]" type="checkbox" class="checkall_a1"></td>

</tr>
`;

          var newRow = table.insertRow(i + 1);
          newRow.innerHTML = element1;
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

  function modal_input_amt1(el) {
    let row = $(el).closest('tr');
    let debit = parseFloat($(el).val()) || 0;
    let creditInput = row.find('input[name="txt_credit1[]"]');

    if (debit > 0) {
      creditInput.val(0);
      creditInput.prop('readonly', true);
    } else {
      creditInput.prop('readonly', false);
    }

    hitung_total1();
  }

  function modal_input_cre1(el) {
    let row = $(el).closest('tr');
    let credit = parseFloat($(el).val()) || 0;
    let debitInput = row.find('input[name="txt_amount1[]"]');

    if (credit > 0) {
      debitInput.val(0);
      debitInput.prop('readonly', true);
    } else {
      debitInput.prop('readonly', false);
    }

    hitung_total1();
  }

  function hitung_total1() {

    let debit_nag = 0, credit_nag = 0, debit_nak = 0, credit_nak = 0;
    let rate = 1;

    $('#tbody1 tr').each(function() {
      let pc = $(this).find('select.prof_ctr1').val();
      let curr = $(this).find('select[name="currenc1[]"]').val();

      let debit = parseFloat($(this).find('input[name="txt_amount1[]"]').val()) || 0;
      let credit = parseFloat($(this).find('input[name="txt_credit1[]"]').val()) || 0;

      if (curr === 'USD') {
        debit = debit * rate;
        credit = credit * rate;
      }

      if (pc === 'NAG') { debit_nag += debit; credit_nag += credit; }
      if (pc === 'NAK') { debit_nak += debit; credit_nak += credit; }
    });

    let header_pc = $('#profit_center_kas1').val();
    let amount = parseFloat($('#amount_kas1').val().replace(/,/g, '')) || 0;
    let eqv = amount * rate;

    if (header_pc === 'NAG') debit_nag += eqv;
    if (header_pc === 'NAK') debit_nak += eqv;

    let grand_debit = debit_nag + debit_nak;
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

  $(document).on('change', '.prof_ctr1,.curr_det1', function() {
    hitung_total1();
  });

  $('#amount_kas1').on('keyup change', function() {
    hitung_total1();
  });

  let coaWajibCC = [];

  $.getJSON('get_coa_wajib_cc.php', function(data) {
    coaWajibCC = data;
  });

  $('#simpan1').on('click', function() {

    if (!$('#account1').val()) {
      Swal.fire('Warning', 'Account tidak boleh kosong', 'warning');
      return;
    }

    if (!$('#pesan1').val().trim()) {
      Swal.fire('Warning', 'Description tidak boleh kosong', 'warning');
      return;
    }

    let debitNAG = 0, creditNAG = 0, debitNAK = 0, creditNAK = 0;
    let error = false;

    $("#tbody1 tr").each(function() {
      let tr = $(this);
      let coa = tr.find('select.no_coa1').first().val();
      let cc = tr.find('select.cost_ctr1').first().val();
      let pc = tr.find('select.prof_ctr1').first().val();

      let debit = parseFloat(tr.find('[name="txt_amount1[]"]').val()) || 0;
      let credit = parseFloat(tr.find('[name="txt_credit1[]"]').val()) || 0;

      let ketInput1 = tr.find('input[name="keterangan1[]"]');
      if (!ketInput1.val()) {
        ketInput1.val($('#pesan1').val());
      }

      if (coaWajibCC.includes(coa)) {
        if (cc == '-' || cc == '' || cc == null) {
          Swal.fire('Warning', 'COA ' + coa + ' wajib isi Cost Center', 'warning');
          error = true;
          return false;
        }
      }

      if (pc == 'NAG') { debitNAG += debit; creditNAG += credit; }
      if (pc == 'NAK') { debitNAK += debit; creditNAK += credit; }
    });

    if (error) return;

    let header_pc = $('#profit_center_kas1').val();
    let header_debit = parseFloat($('#amount_kas1').val().replace(/,/g, '')) || 0;

    if (header_pc == 'NAG') {
      if ((header_debit + debitNAG) != creditNAG) {
        Swal.fire('Warning', 'Journal Nirwana Alabare Garment tidak balance (Header + Detail)', 'warning');
        return;
      }
    }

    if (header_pc == 'NAK') {
      if ((header_debit + debitNAK) != creditNAK) {
        Swal.fire('Warning', 'Journal Nirwana Alabare Knitting tidak balance (Header + Detail)', 'warning');
        return;
      }
    }

    function doUpdatePettyinCashout1() {

      $('#simpan1').prop('disabled', true);

      $.ajax({
        url: "petty-in/update_pci_cashout.php",
        type: "POST",
        data: $('#form-data1').serialize(),

        beforeSend: function() {
          Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
          });
        },

        success: function(res) {
          let r = JSON.parse(res);

          if (r.status == 'success') {
            Swal.fire({ icon: 'success', title: 'Success', text: r.message }).then(() => {
              location.href = 'petty-cashin.php';
            });
          } else {
            Swal.fire({
              icon: 'error', title: 'Error', text: r.message,
              showCancelButton: true, confirmButtonText: 'Coba Lagi', cancelButtonText: 'Tutup'
            }).then((retry) => {
              if (retry.isConfirmed) { doUpdatePettyinCashout1(); }
              else { $('#simpan1').prop('disabled', false); }
            });
          }
        },

        error: function(xhr) {
          Swal.fire({
            icon: 'error', title: 'Error', text: 'Server Error',
            showCancelButton: true, confirmButtonText: 'Coba Lagi', cancelButtonText: 'Tutup'
          }).then((retry) => {
            if (retry.isConfirmed) { doUpdatePettyinCashout1(); }
            else { $('#simpan1').prop('disabled', false); }
          });
        }
      });
    }

    Swal.fire({
      title: "Update Data?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Save",
      cancelButtonText: "Cancel"
    }).then((result) => {
      if (result.isConfirmed) {
        doUpdatePettyinCashout1();
      }
    });

  });

  hitung_total1();
</script>
