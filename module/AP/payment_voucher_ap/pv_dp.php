<?php
$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : null;
?>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <form id="dp-form-data" method="post">
    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#dp_header_card_body" aria-expanded="true" aria-controls="dp_header_card_body">
                    <span><i class="fa fa-money mr-2"></i> FORM PAYMENT VOUCHER - DP</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="dp_header_card_body">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="dp_nokontrabon"><b>No Payment Voucher</b></label>
                            <?php
                            $sql = mysqli_query($conn2, "select CONCAT('PV-AP/DP/',DATE_FORMAT(CURRENT_DATE(), '%Y'),'/',DATE_FORMAT(CURRENT_DATE(), '%m'),'/',LPAD((COALESCE(max(CAST(RIGHT(no_kbon,5) AS UNSIGNED)),0) + 1),5,'0')) nomor from kontrabon_h_dp WHERE YEAR(tgl_kbon) = YEAR (CURRENT_DATE())");
                            $row = mysqli_fetch_array($sql);
                            $kodeBarang = $row['nomor'];

                            echo '<input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="dp_nokontrabon" name="nokontrabon" value="' . $kodeBarang . '">';
                            ?>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_tanggal"><b>Payment Voucher Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" name="tanggal" id="dp_tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggalDp(this.value)"
                            value="<?php echo !empty($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date("d-m-Y"); ?>">
                        </div>

                        <input type="hidden" name="jurnal" id="dp_jurnal" value="0">

                       <!--  <div class="col-md-2 mb-3">
                            <label for="dp_matauang"><b>Currency</b></label> -->
                            <?php
                            $value = null;
                            if (!empty($nama_supp)) {
                                $sql = mysqli_query($conn2, "select curr from ftr_dp where supp = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'");
                                $row = mysqli_fetch_array($sql);
                                $value = isset($row['curr']) ? $row['curr'] : null;
                            }
                            echo '<input type="hidden" readonly class="form-control form-control-sm" id="dp_matauang" name="matauang" value="' . htmlspecialchars((string) $value) . '">';
                            ?>
                       <!--  </div> -->

                       <div class="col-md-2 mb-3">
                            <label for="dp_profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="profit_center" id="dp_profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoKontraBonDp()">
                                <option value="" disabled selected="true">Select Profit Center</option>
                                <?php
                                $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center'] : null;
                                $sql = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                                while ($rowPc = mysqli_fetch_array($sql)) {
                                    $data = $rowPc['kode_pc'];
                                    $data2 = $rowPc['nama_pc'];
                                    $isSelected = ($rowPc['kode_pc'] == $profit_center) ? ' selected="selected"' : '';
                                    echo '<option value="' . $data . '"' . $isSelected . '>' . $data2 . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="dp_txt_tgltempo"
                            value="<?php echo !empty($_POST['txt_tgltempo']) ? htmlspecialchars($_POST['txt_tgltempo']) : date("d-m-Y"); ?>">
                        </div>


                        

                        <!-- <div class="col-md-2 mb-3">
                            <label for="dp_no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm" id="dp_no_faktur" name="no_faktur"
                            value="<?php echo isset($_POST['no_faktur']) ? htmlspecialchars($_POST['no_faktur']) : ''; ?>" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm" id="dp_txt_inv" name="txt_inv"
                            value="<?php echo isset($_POST['txt_inv']) ? htmlspecialchars($_POST['txt_inv']) : ''; ?>" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="dp_txt_tglsi"
                            value="<?php echo !empty($_POST['txt_tglsi']) ? htmlspecialchars($_POST['txt_tglsi']) : date("d-m-Y"); ?>">
                        </div> -->

                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm" id="dp_no_faktur" name="no_faktur"
                            value="<?php echo isset($_POST['no_faktur']) ? htmlspecialchars($_POST['no_faktur']) : ''; ?>" required>
                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm" id="dp_txt_inv" name="txt_inv"
                            value="<?php echo isset($_POST['txt_inv']) ? htmlspecialchars($_POST['txt_inv']) : ''; ?>" required>
                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="dp_txt_tglsi"
                            value="<?php echo !empty($_POST['txt_tglsi']) ? htmlspecialchars($_POST['txt_tglsi']) : date("d-m-Y"); ?>">

                        
                        <div class="col-md-3 mb-3"></div>
                        

                        <?php
                        // Load bank accounts dari master_supplier_bank berdasarkan supplier terpilih.
                        $supplier_banks_dp = [];
                        if (!empty($nama_supp)) {
                            $q_sb_dp = mysqli_query($conn2,
                                "SELECT msb.id, msb.bank_account, msb.bank_currency, msb.bank_name, msb.beneficiary_name
                                 FROM master_supplier_bank msb
                                 INNER JOIN mastersupplier ms ON ms.id_supplier = msb.id_supplier
                                 WHERE ms.Supplier = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'
                                   AND msb.status = 'Active'
                                 ORDER BY msb.bank_name ASC"
                            );
                            while ($r_sb_dp = mysqli_fetch_assoc($q_sb_dp)) {
                                $supplier_banks_dp[] = $r_sb_dp;
                            }
                        }
                        ?>

                        <!-- Bank Account selectpicker -->
                        <div class="col-md-3 mb-3">
                            <label for="dp_sel_bank_account"><b>Bank Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker"
                                    id="dp_sel_bank_account" name="sel_bank_account"
                                    data-live-search="true" data-dropup-auto="false" data-size="8">
                                <option value="">-- Select Bank Account --</option>
                                <option value="-">- (Tidak ada rekening)</option>
                                <?php foreach ($supplier_banks_dp as $b): ?>
                                <option value="<?= htmlspecialchars($b['id']) ?>"
                                        data-currency="<?= htmlspecialchars($b['bank_currency']) ?>"
                                        data-bankname="<?= htmlspecialchars($b['bank_name']) ?>"
                                        data-beneficiary="<?= htmlspecialchars($b['beneficiary_name']) ?>">
                                    <?= htmlspecialchars($b['bank_account']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_disp_bankname"><b>Bank Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="dp_disp_bankname" placeholder="-">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_disp_beneficiary"><b>Beneficiary Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="dp_disp_beneficiary" placeholder="-">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_disp_currency"><b>Bank Currency</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="dp_disp_currency" placeholder="-">
                        </div>


                        <div class="col-md-3 mb-3"></div>

                        <div class="col-md-3 mb-3">
                            <label><b>From Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker" id="dp_from_account" name="from_account" data-live-search="true">
                                <option value="">Select Account</option>

                                <?php
                                $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                                while ($row = mysqli_fetch_assoc($sql)) {
                                    echo "<option value='" . $row['account'] . "' data-bank='" . $row['bank'] . "' data-currency='" . $row['curr'] . "' data-namapc='" . $row['nama_pc'] . "' data-kodepc='" . $row['kode_pc'] . "'  data-kodebank='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                                }
                                ?>

                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label><b>From Bank Name <i style="color: red;">*</i></b></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="dp_from_bank" name="from_bank" readonly>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label><b>From Bank Currency <i style="color: red;">*</i></b></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="dp_from_bank_curr" name="from_bank_curr" readonly>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="dp_item_type"><b>Item Type <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="item_type" id="dp_item_type" data-dropup-auto="false" data-live-search="true" onchange="updateNoKontraBonDp()">
                                <option value="" disabled selected="true">Select Profit Center</option>
                                <?php
                                $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : null;
                                $sql = mysqli_query($conn1, "select item_type from pv_mapping_jurnal_dp where status = 'Y'   GROUP BY item_type");
                                while ($rowPc = mysqli_fetch_array($sql)) {
                                    $data = $rowPc['item_type'];
                                    $data2 = $rowPc['item_type'];
                                    $isSelected = ($rowPc['item_type'] == $item_type) ? ' selected="selected"' : '';
                                    echo '<option value="' . $data . '"' . $isSelected . '>' . $data2 . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dp_txt_supp"><b>Supplier</b></label>
                            <div class="input-group">
                                <input type="text" readonly style="font-size: 13px;" class="form-control" id="dp_txt_supp" name="txt_supp"
                                value="<?php echo htmlspecialchars((string) $nama_supp); ?>">

                                <div class="input-group-append col">
                                    <input
                                    style="border: 0;
                                    line-height: 1;
                                    padding: 0 10px;
                                    font-size: 1rem;
                                    text-align: center;
                                    color: #fff;
                                    text-shadow: 1px 1px 1px #000;
                                    border-radius: 6px;
                                    background-color: rgb(95, 158, 160);"
                                    type="button"
                                    id="dp_mysupp"
                                    value="Select">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
        </div>
    </form>

    <form id="dp-form-simpan">
        <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#dp_ftr_card_body" aria-expanded="true" aria-controls="dp_ftr_card_body">
                    <span><i class="fa fa-list-alt"></i> Data FTR (DP)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="dp_ftr_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="dp_mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;text-align:center;">
                            <thead>
                                <tr style="background-color: #f1f5f9; color: #1e293b;">
                                    <th style="width:6%;"><input type="checkbox" id="dp_select_all"></th>
                                    <th style="width:14%;">NO FTR</th>
                                    <th style="width:14%;">NO PO</th>
                                    <th style="width:12%;">PO Date</th>
                                    <th style="width:15%;">Total PO</th>
                                    <th style="width:15%;">DP Amount</th>
                                    <th style="width:15%;">Balance</th>
                                    <th style="width:100px;display: none;">Supplier</th>
                                    <th style="width:100px;display: none;">Create User</th>
                                    <th style="width:100px;display: none;">Tgl FTR DP</th>
                                    <th style="width:100px;display: none;">Status</th>
                                    <th style="width:100px;display: none;">Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $start_date = '';
                                $end_date = '';
                                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                    $start_date = date("Y-m-d", strtotime($_POST['start_date']));
                                    $end_date = date("Y-m-d", strtotime($_POST['end_date']));
                                }

                                $nama_supp_esc = mysqli_real_escape_string($conn2, (string) $nama_supp);
                                $sql = mysqli_query($conn2, "select no_ftr_dp, tgl_ftr_dp, no_po, tgl_po, SUM(total) as sub, SUM(dp_value) as dp, SUM(balance) as balance, supp as supplier, status, keterangan, create_user from ftr_dp where supp = '$nama_supp_esc' and tgl_ftr_dp between '$start_date' and '$end_date' and status = 'Approved' and is_invoiced != 'Invoiced' group by no_ftr_dp");
                                while ($row = mysqli_fetch_array($sql)) {
                                    $dp_no = $row['no_ftr_dp'];
                                    $dp_no_esc = mysqli_real_escape_string($conn2, $dp_no);
                                    $querys = mysqli_query($conn2, "select no_dp, no_po, status from kontrabon_dp where no_dp = '$dp_no_esc' and status != 'Cancel'");
                                    $rows = mysqli_fetch_array($querys);
                                    $n_dp = isset($rows['no_dp']);
                                    $stat = isset($rows['status']);
                                    $sub = $row['sub'];
                                    $dp = $row['dp'];
                                    $balance = $row['balance'];
                                    if ($dp_no == $n_dp and $stat != 'Cancel') {
                                        echo '';
                                    } else {
                                        echo '<tr>
                                        <td style="text-align:center;"><input type="checkbox" class="dp_chk" name="select[]" value=""></td>
                                        <td value="' . $row['no_ftr_dp'] . '">' . $row['no_ftr_dp'] . '</td>
                                        <td value="' . $row['no_po'] . '">' . $row['no_po'] . '</td>
                                        <td value="' . $row['tgl_po'] . '">' . date("d-M-Y", strtotime($row['tgl_po'])) . '</td>
                                        <td class="dt_price" style="text-align:right;" data-link="1" data-subtotal="' . $sub . '">' . number_format($sub, 2) . '</td>
                                        <td class="dt_tax" style="text-align:right;" data-dp="' . $dp . '">' . number_format($dp, 2) . '</td>
                                        <td class="dt_total" style="text-align:right;" data-total="' . $balance . '">' . number_format($balance, 2) . '</td>
                                        <td style="display: none;" value="' . $row['supplier'] . '">' . $row['supplier'] . '</td>
                                        <td style="display: none;" value="' . $row['create_user'] . '">' . $row['create_user'] . '</td>
                                        <td style="display: none;" value="' . $row['tgl_ftr_dp'] . '">' . date("d-M-Y", strtotime($row['tgl_ftr_dp'])) . '</td>
                                        <td style="display: none;" value="' . $row['status'] . '">' . $row['status'] . '</td>
                                        <td style="display: none;" value="' . $row['keterangan'] . '">' . $row['keterangan'] . '</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>Total PO</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="dp_subtotal" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="dp_subtotal_h" value="">
                        </div>
                    </div>

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>DP Amount</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="dp_pajak" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="dp_pajak_h" value="">
                        </div>
                    </div>

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>Total Amount</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="dp_total" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="dp_total_h" value="">
                        </div>
                    </div>

                    <div class="row mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                        <div class="col d-flex">
                            <button type="button" class="btn btn-primary btn-sm me-2 px-4 mr-2" id="dp_simpan">
                                <span class="fa fa-floppy-o"></span> Save
                            </button>
                            <button type="button" class="btn btn-danger btn-sm px-4" onclick="location.href='payment-voucher-ap.php'">
                                <span class="fa fa-angle-double-left"></span> Back
                            </button>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </form>
  </div>
</div>

<div class="modal fade" id="dp_mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white">Choose Supplier</h4>
      </div>

      <div class="modal-body">
        <form id="dp-modal-form" method="post">
          <div class="form-group mb-3">
            <label for="dp_nama_supp"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp" id="dp_nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>
                <?php
                $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($rowS = mysqli_fetch_array($sql)) {
                    $data = $rowS['Supplier'];
                    $isSelected = ($data == $nama_supp) ? ' selected="selected"' : '';
                    echo '<option value="' . $data . '"' . $isSelected . '">' . $data . '</option>';
                }
                ?>
            </select>
          </div>

          <div class="form-group">
            <label><b>DP Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control tanggal_fil mr-2" id="dp_start_date" name="start_date"
              value="<?php echo !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date("d-m-Y"); ?>"
              placeholder="Tanggal Awal">

              <span class="mx-2">-</span>

              <input type="text" class="form-control tanggal_fil" id="dp_end_date" name="end_date"
              value="<?php echo !empty($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : date("d-m-Y"); ?>"
              placeholder="Tanggal Akhir">
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
        <button type="submit" form="dp-modal-form" id="dp_send" class="btn btn-warning">
          <i class="fa fa-search" aria-hidden="true"></i> Search
        </button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="dp_mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="dp_txt_dp"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="dp_txt_tgldp" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_txt_supp2" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_txt_curr" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_txt_create_user" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_txt_status" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_txt_keterangan" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="dp_details" class="modal-body col-12" style="font-size: 13px; padding: 0.5rem;"></div>
              </div>
          </div>
      </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    $('#dp_mytable').DataTable({
        paging: false,
        searching: true,
        info: false,
        scrollY: "300px",
        scrollCollapse: true,
        autoWidth: false
    });

    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
});
</script>

<script type="text/javascript">
    function ubahtanggalDp(value) {
        var tanggal = document.getElementById('dp_tanggal').value;
        $.ajax({
            type: 'POST',
            url: 'getnomor_pv_dp.php',
            data: { 'tanggal': tanggal },
            success: function (response) {
                $('#dp_nokontrabon').val(response);
                updateNoKontraBonDp();
            }
        });
        $("#dp_tanggal").val(tanggal);
    }

    function updateNoKontraBonDp() {
        let pc = document.getElementById("dp_profit_center").value;
        let input = document.getElementById("dp_nokontrabon");
        let current = input.value.trim();

        let parts = current.split("/");

        // belum ada PC: [0]=PV-AP, [1]=DP, [2]=YYYY, [3]=MM, [4]=00001
        if (parts.length === 5) {
            input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[2] + "/" + parts[3] + "/" + parts[4];
        }
        // sudah ada PC: [0]=PV-AP, [1]=DP, [2]=PC lama, [3]=YYYY, [4]=MM, [5]=00001
        else if (parts.length === 6) {
            input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[3] + "/" + parts[4] + "/" + parts[5];
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        let savedPc = localStorage.getItem("pv_dp_profit_center");
        if (savedPc) {
            document.getElementById("dp_profit_center").value = savedPc;
            updateNoKontraBonDp();
        }
    });

    document.getElementById("dp_profit_center").addEventListener("change", function () {
        localStorage.setItem("pv_dp_profit_center", this.value);
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#dp_mysupp").on("click", function () {
            let profit_center = $('#dp_profit_center option').filter(':selected').val();

            if (profit_center == "") {
                Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
                $("#dp_profit_center").focus();
                return;
            }

            $("#dp_mymodal").modal("show");
        });
    });
</script>

<script type="text/javascript">
    $(document).on('change', '#dp_sel_bank_account', function () {
        var opt = $(this).find('option:selected');
        var currency    = opt.data('currency')    || '';
        var bankname    = opt.data('bankname')    || '';
        var beneficiary = opt.data('beneficiary') || '';

        $('#dp_disp_currency').val(currency);
        $('#dp_disp_bankname').val(bankname);
        $('#dp_disp_beneficiary').val(beneficiary);
    });

    $(document).on('change', '#dp_from_account', function () {
        var opt = $(this).find('option:selected');
        $('#dp_from_bank').val(opt.data('bank') || '');
        $('#dp_from_bank_curr').val(opt.data('currency') || '');
    });
</script>

<script type="text/javascript">
    function formatMoneyDp(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e);
        }
    }

    $("#dp_mytable").on("change", "input.dp_chk", function () {
        var sum_sub = 0;
        var sum_dp = 0;

        $("#dp_mytable input.dp_chk:checked").each(function () {
            var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
            var dp = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-dp'), 10) || 0;
            sum_sub += price;
            sum_dp += dp;
        });

        $("#dp_subtotal").val(formatMoneyDp(sum_sub));
        $("#dp_subtotal_h").val(sum_sub);
        $("#dp_pajak").val(formatMoneyDp(sum_dp));
        $("#dp_pajak_h").val(sum_dp);
        $("#dp_total").val(formatMoneyDp(sum_dp));
        $("#dp_total_h").val(sum_dp);
    });
</script>

<script type="text/javascript">
    $("#dp_select_all").click(function () {
        var c = this.checked;
        $("#dp_mytable input.dp_chk").prop('checked', c).trigger('change');
    });
</script>

<script type="text/javascript">
    $("#dp-form-simpan").on("click", "#dp_simpan", function () {
        var no_kbon_h = document.getElementById('dp_nokontrabon').value;
        var tgl_kbon_h = document.getElementById('dp_tanggal').value;
        var nama_supp_h = document.getElementById('dp_txt_supp').value;
        var no_faktur_h = document.getElementById('dp_no_faktur').value;
        var supp_inv_h = document.getElementById('dp_txt_inv').value;
        var tgl_inv_h = document.getElementById('dp_txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('dp_txt_tgltempo').value;
        var curr_h = document.getElementById('dp_matauang').value;
        var sub_h = document.getElementById('dp_subtotal_h').value;
        var tax_h = document.getElementById('dp_pajak_h').value;
        var total_h = document.getElementById('dp_total_h').value;
        var jurnal_h = document.getElementById('dp_jurnal').value;
        var create_user_h = '<?php echo $user; ?>';
        var start_date = document.getElementById('dp_start_date').value;
        var end_date = document.getElementById('dp_end_date').value;
        var profit_center = document.getElementById('dp_profit_center').value;
        var bank_account = $('#dp_sel_bank_account option').filter(':selected').val();
        var from_account = document.getElementById('dp_from_account').value;
        var from_bank = document.getElementById('dp_from_bank').value;
        var from_bank_curr = document.getElementById('dp_from_bank_curr').value;
        var item_type = document.getElementById('dp_item_type').value;

        if (profit_center === '') {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
            document.getElementById('dp_profit_center').focus();
            return;
        }
        if (!bank_account) {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select bank account supplier'});
            return;
        }
        if (!from_account) {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select From Account'});
            return;
        }
        if (!item_type) {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select Item Type'});
            return;
        }
        if (document.querySelectorAll("#dp_mytable input.dp_chk:checked").length === 0) {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please check the FTR DP number'});
            return;
        }
        if (total_h === '') {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select at least 1 row to calculate the total'});
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'insertkbon_h_pv_dp.php',
            data: { 'no_kbon_h': no_kbon_h, 'tgl_kbon_h': tgl_kbon_h, 'nama_supp_h': nama_supp_h, 'no_faktur_h': no_faktur_h, 'supp_inv_h': supp_inv_h, 'tgl_inv_h': tgl_inv_h, 'tgl_tempo_h': tgl_tempo_h, 'curr_h': curr_h, 'create_user_h': create_user_h, 'sub_h': sub_h, 'tax_h': tax_h, 'total_h': total_h, 'profit_center': profit_center, 'bank_account': bank_account, 'from_account': from_account, 'from_bank': from_bank, 'from_bank_curr': from_bank_curr, 'item_type': item_type },
            cache: 'false',
            success: function (response) {
                console.log(response);

                $("#dp_mytable input.dp_chk:checked").each(function () {
                    var no_dp = $(this).closest('tr').find('td:eq(1)').attr('value');
                    var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
                    var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
                    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
                    var dp = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-dp'), 10) || 0;
                    var total = parseFloat($(this).closest('tr').find('td:eq(6)').attr('data-total'), 10) || 0;

                    $.ajax({
                        type: 'POST',
                        url: 'insertkbon_dp.php',
                        data: {
                            'no_kbon': no_kbon_h, 'tgl_kbon': tgl_kbon_h, 'jurnal': jurnal_h, 'no_dp': no_dp, 'no_po': no_po,
                            'nama_supp': nama_supp_h, 'tgl_po': tgl_po, 'no_faktur': no_faktur_h, 'supp_inv': supp_inv_h, 'tgl_inv': tgl_inv_h, 'tgl_tempo': tgl_tempo_h,
                            'curr': curr_h, 'ceklist': '1', 'create_user': create_user_h, 'sum_sub': price, 'sum_dp': dp, 'sum_total': total, 'start_date': start_date, 'end_date': end_date
                        },
                        cache: 'false',
                        success: function (response2) {
                            console.log(response2);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            console.log(xhr);
                            Swal.fire('Error', xhr.responseText, 'error');
                        }
                    });
                });

                Swal.fire({icon: 'success', title: 'Data Saved Successfully', text: response, timer: 3000, showConfirmButton: true, confirmButtonText: 'OK'}).then(function(){
                    window.location = 'payment-voucher-ap.php';
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });
</script>

<script type="text/javascript">
    $('#dp_mytable tbody').on('click', 'td:eq(1)', function () {
        $('#dp_mymodalbpb').modal('show');
        var noftrdp = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_dp = $(this).closest('tr').find('td:eq(9)').text();
        var supp = $(this).closest('tr').find('td:eq(7)').attr('value');
        var curr = document.getElementById('dp_matauang').value;
        var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
        var status = $(this).closest('tr').find('td:eq(10)').attr('value');
        var keterangan = $(this).closest('tr').find('td:eq(11)').attr('value');

        $.ajax({
            type: 'post',
            url: 'ajaxdp.php',
            data: { 'noftrdp': noftrdp },
            success: function (data) {
                $('#dp_details').html(data);
            }
        });

        $('#dp_txt_dp').html(noftrdp);
        $('#dp_txt_tgldp').html('Tgl FTR DP : ' + tgl_dp);
        $('#dp_txt_supp2').html('Supplier : ' + supp);
        $('#dp_txt_curr').html('Currency : ' + curr);
        $('#dp_txt_create_user').html('Create By : ' + create_user);
        $('#dp_txt_status').html('Status : ' + status);
        $('#dp_txt_keterangan').html('Keterangan : ' + keterangan);
    });
</script>
