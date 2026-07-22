<?php
$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : null;
?>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <form id="cbd-form-data" method="post">
    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#cbd_header_card_body" aria-expanded="true" aria-controls="cbd_header_card_body">
                    <span><i class="fa fa-money mr-2"></i> FORM PAYMENT VOUCHER - CBD</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="cbd_header_card_body">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="cbd_nokontrabon"><b>No Payment Voucher</b></label>
                            <?php
                            $sql = mysqli_query($conn2, "select CONCAT('PV-AP/CBD/',DATE_FORMAT(CURRENT_DATE(), '%Y'),'/',DATE_FORMAT(CURRENT_DATE(), '%m'),'/',LPAD((COALESCE(max(CAST(RIGHT(no_kbon,5) AS UNSIGNED)),0) + 1),5,'0')) nomor from kontrabon_h_cbd WHERE YEAR(tgl_kbon) = YEAR (CURRENT_DATE())");
                            $row = mysqli_fetch_array($sql);
                            $kodeBarang = $row['nomor'];

                            echo '<input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="cbd_nokontrabon" name="nokontrabon" value="' . $kodeBarang . '">';
                            ?>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_tanggal"><b>Payment Voucher Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" name="tanggal" id="cbd_tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggalCbd(this.value)"
                            value="<?php echo !empty($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date("Y-m-d"); ?>">
                        </div>

                        <input type="hidden" name="jurnal" id="cbd_jurnal" value="0">

                     <!--    <div class="col-md-2 mb-3">
                            <label for="cbd_matauang"><b>Currency</b></label> -->
                            <?php
                            $value = null;
                            if (!empty($nama_supp)) {
                                $sql = mysqli_query($conn2, "select curr from ftr_cbd where supp = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'");
                                $row = mysqli_fetch_array($sql);
                                $value = isset($row['curr']) ? $row['curr'] : null;
                            }
                            echo '<input type="hidden" readonly class="form-control form-control-sm" id="cbd_matauang" name="matauang" value="' . htmlspecialchars((string) $value) . '">';
                            ?>
                       <!--  </div> -->

                       <div class="col-md-2 mb-3">
                            <label for="cbd_profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="profit_center" id="cbd_profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoKontraBonCbd()">
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
                            <label for="cbd_txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="cbd_txt_tgltempo"
                            value="<?php echo !empty($_POST['txt_tgltempo']) ? htmlspecialchars($_POST['txt_tgltempo']) : date("Y-m-d"); ?>">
                        </div>

                        <div class="col-md-2 mb-3"></div>

                        

                        <!-- <div class="col-md-2 mb-3">
                            <label for="cbd_no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm" id="cbd_no_faktur" name="no_faktur"
                            value="<?php echo isset($_POST['no_faktur']) ? htmlspecialchars($_POST['no_faktur']) : ''; ?>" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm" id="cbd_txt_inv" name="txt_inv"
                            value="<?php echo isset($_POST['txt_inv']) ? htmlspecialchars($_POST['txt_inv']) : ''; ?>" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="cbd_txt_tglsi"
                            value="<?php echo !empty($_POST['txt_tglsi']) ? htmlspecialchars($_POST['txt_tglsi']) : date("d-m-Y"); ?>">
                        </div> -->
                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm" id="cbd_no_faktur" name="no_faktur"
                            value="<?php echo isset($_POST['no_faktur']) ? htmlspecialchars($_POST['no_faktur']) : ''; ?>" required>
                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm" id="cbd_txt_inv" name="txt_inv"
                            value="<?php echo isset($_POST['txt_inv']) ? htmlspecialchars($_POST['txt_inv']) : ''; ?>" required>
                        <input type="hidden" style="font-size: 13px;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="cbd_txt_tglsi"
                            value="<?php echo !empty($_POST['txt_tglsi']) ? htmlspecialchars($_POST['txt_tglsi']) : date("Y-m-d"); ?>">


                        <?php
                        // Load bank accounts dari master_supplier_bank berdasarkan supplier terpilih.
                        $supplier_banks_cbd = [];
                        if (!empty($nama_supp)) {
                            $q_sb_cbd = mysqli_query($conn2,
                                "SELECT msb.id, msb.bank_account, msb.bank_currency, msb.bank_name, msb.beneficiary_name
                                 FROM master_supplier_bank msb
                                 INNER JOIN mastersupplier ms ON ms.id_supplier = msb.id_supplier
                                 WHERE ms.Supplier = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'
                                   AND msb.status = 'Active'
                                 ORDER BY msb.bank_name ASC"
                            );
                            while ($r_sb_cbd = mysqli_fetch_assoc($q_sb_cbd)) {
                                $supplier_banks_cbd[] = $r_sb_cbd;
                            }
                        }
                        ?>

                        <!-- Bank Account selectpicker -->
                        <div class="col-md-3 mb-3">
                            <label for="cbd_sel_bank_account"><b>Bank Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker"
                                    id="cbd_sel_bank_account" name="sel_bank_account"
                                    data-live-search="true" data-dropup-auto="false" data-size="8">
                                <option value="">-- Select Bank Account --</option>
                                <option value="-">- (Tidak ada rekening)</option>
                                <?php foreach ($supplier_banks_cbd as $b): ?>
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
                            <label for="cbd_disp_bankname"><b>Bank Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="cbd_disp_bankname" placeholder="-">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_disp_beneficiary"><b>Beneficiary Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="cbd_disp_beneficiary" placeholder="-">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_disp_currency"><b>Bank Currency</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="cbd_disp_currency" placeholder="-">
                        </div>
                        <div class="col-md-3 mb-3"></div>

                        <div class="col-md-3 mb-3">
                            <label><b>From Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker" id="cbd_from_account" name="from_account" data-live-search="true" data-size="5">
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
                            <input type="text" class="form-control form-control-sm bg-light" id="cbd_from_bank" name="from_bank" readonly>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label><b>From Bank Currency <i style="color: red;">*</i></b></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="cbd_from_bank_curr" name="from_bank_curr" readonly>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="cbd_item_type"><b>Item Type <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="item_type" id="cbd_item_type" data-dropup-auto="false" data-live-search="true">
                                <option value="" disabled selected="true">Select Item Type</option>
                                <?php
                                $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : null;
                                $sql = mysqli_query($conn1, "select item_type from pv_mapping_jurnal_dp where status = 'Y' GROUP BY item_type");
                                while ($rowIt = mysqli_fetch_array($sql)) {
                                    $data = $rowIt['item_type'];
                                    $isSelected = ($rowIt['item_type'] == $item_type) ? ' selected="selected"' : '';
                                    echo '<option value="' . $data . '"' . $isSelected . '>' . $data . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cbd_txt_supp"><b>Supplier</b></label>
                            <div class="input-group">
                                <input type="text" readonly style="font-size: 13px;" class="form-control" id="cbd_txt_supp" name="txt_supp"
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
                                    id="cbd_mysupp"
                                    value="Select">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
        </div>
    </form>

    <form id="cbd-form-simpan">
        <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#cbd_ftr_card_body" aria-expanded="true" aria-controls="cbd_ftr_card_body">
                    <span><i class="fa fa-list-alt"></i> Data FTR (CBD)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="cbd_ftr_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="cbd_mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;text-align:center;">
                            <thead>
                                <tr style="background-color: #f1f5f9; color: #1e293b;">
                                    <th style="width:6%;">-</th>
                                    <th style="width:14%;">NO FTR</th>
                                    <th style="width:14%;">NO PO</th>
                                    <th style="width:12%;">PO Date</th>
                                    <th style="width:15%;">SubTotal</th>
                                    <th style="width:15%;">Tax (PPn)</th>
                                    <th style="width:100px;display: none;">Tax (PPh)</th>
                                    <th style="width:15%;">Total (PO)</th>
                                    <th style="width:100px;display: none;">Supplier</th>
                                    <th style="width:100px;display: none;">Status</th>
                                    <th style="width:100px;display: none;">Keterangan</th>
                                    <th style="width:100px;display: none;">Create By</th>
                                    <th style="width:100px;display: none;">Tgl CBD</th>
                                    <th style="width:100px;display: none;">No PO</th>
                                    <th style="width:100px;display: none;">Tgl PO</th>
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

                                $persen = '';
                                $q = mysqli_query($conn1, "select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");
                                while ($rs = mysqli_fetch_array($q)) {
                                    $persen .= '<option data-idtax="' . $rs['idtax'] . '" value="' . $rs['percentage'] . '">' . $rs['kriteria'] . '</option>';
                                }

                                $nama_supp_esc = mysqli_real_escape_string($conn2, (string) $nama_supp);
                                $sql = mysqli_query($conn2, "select no_ftr_cbd, tgl_ftr_cbd, no_po, tgl_po, SUM(subtotal + biaya_tambahan) as sub, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, supp as supplier, status, keterangan, create_user from ftr_cbd where supp = '$nama_supp_esc' and tgl_ftr_cbd between '$start_date' and '$end_date' and is_invoiced != 'Invoiced' and status = 'Approved' group by no_ftr_cbd");
                                while ($row = mysqli_fetch_array($sql)) {
                                    $cbd = $row['no_ftr_cbd'];
                                    $cbd_esc = mysqli_real_escape_string($conn2, $cbd);
                                    $querys = mysqli_query($conn2, "select no_cbd, no_po, status from kontrabon_cbd where no_cbd = '$cbd_esc' and status != 'Cancel'");
                                    $rows = mysqli_fetch_array($querys);
                                    $n_cbd = isset($rows['no_cbd']);
                                    $stat = isset($rows['status']);
                                    $sub = $row['sub'];
                                    $tax = $row['tax'];
                                    $total = $row['total'];
                                    if ($cbd == $n_cbd and $stat != 'Cancel') {
                                        echo '';
                                    } else {
                                        echo '<tr>
                                        <td style="text-align:center;"><input type="radio" class="cbd_chk" name="select_cbd[]" value=""></td>
                                        <td value="' . $row['no_ftr_cbd'] . '">' . $row['no_ftr_cbd'] . '</td>
                                        <td value="' . $row['no_po'] . '">' . $row['no_po'] . '</td>
                                        <td value="' . $row['tgl_po'] . '">' . date("d-M-Y", strtotime($row['tgl_po'])) . '</td>
                                        <td class="dt_price" style="text-align:right;" data-link="1" data-subtotal="' . $sub . '">' . number_format($sub, 2) . '</td>
                                        <td class="dt_tax" style="text-align:right;" data-tax="' . $tax . '">' . number_format($tax, 2) . '</td>
                                        <td style="display: none;">
                                        <select name="combo_pph" id="combo_pph" disabled>
                                        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                                        ' . $persen . '
                                        </select>
                                        </td>
                                        <td class="dt_total" style="text-align:right;" data-total="' . $total . '">' . number_format($total, 2) . '</td>
                                        <td style="display: none;" value="' . $row['supplier'] . '">' . $row['supplier'] . '</td>
                                        <td style="display: none;" value="' . $row['status'] . '">' . $row['status'] . '</td>
                                        <td style="display: none;" value="' . $row['keterangan'] . '">' . $row['keterangan'] . '</td>
                                        <td style="display: none;" value="' . $row['create_user'] . '">' . $row['create_user'] . '</td>
                                        <td style="display: none;" value="' . $row['tgl_ftr_cbd'] . '">' . date("d-M-Y", strtotime($row['tgl_ftr_cbd'])) . '</td>
                                        <td style="display: none;" value="' . $row['no_po'] . '">' . $row['no_po'] . '</td>
                                        <td style="display: none;" value="' . $row['tgl_po'] . '">' . date("d-M-Y", strtotime($row['tgl_po'])) . '</td>
                                        </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>Subtotal</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="cbd_subtotal" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="cbd_subtotal_h" value="">
                        </div>
                    </div>

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>Tax (PPn)</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="cbd_pajak" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="cbd_pajak_h" value="">
                        </div>
                    </div>

                    <input type="hidden" id="cbd_pph" value="">
                    <input type="hidden" id="cbd_pph_h" value="">

                    <div class="form-row col mt-3">
                        <label class="col-form-label" style="width: 150px; font-size: 13px;"><b><u>Total</u></b></label>
                        <div class="col-md-2 mb-3">
                            <input type="text" class="form-control form-control-sm" id="cbd_total" value="" placeholder="0.00" style="font-size: 13px;text-align: right;" readonly>
                            <input type="hidden" id="cbd_total_h" value="">
                            <input type="hidden" id="cbd_no_po" value="">
                            <input type="hidden" id="cbd_tgl_po" value="">
                        </div>
                    </div>

                    <div class="row mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                        <div class="col d-flex">
                            <button type="button" class="btn btn-primary btn-sm me-2 px-4 mr-2" id="cbd_simpan">
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

<div class="modal fade" id="cbd_mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content shadow-lg rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white">Choose Supplier</h4>
      </div>

      <div class="modal-body">
        <form id="cbd-modal-form" method="post">
          <div class="form-group mb-3">
            <label for="cbd_nama_supp"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp" id="cbd_nama_supp" data-dropup-auto="false" data-live-search="true">
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
            <label><b>CBD Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control tanggal_fil mr-2" id="cbd_start_date" name="start_date"
              value="<?php echo !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date("d-m-Y"); ?>"
              placeholder="Tanggal Awal">

              <span class="mx-2">-</span>

              <input type="text" class="form-control tanggal_fil" id="cbd_end_date" name="end_date"
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
        <button type="submit" form="cbd-modal-form" id="cbd_send" class="btn btn-warning">
          <i class="fa fa-search" aria-hidden="true"></i> Search
        </button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="cbd_mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="cbd_txt_cbd"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="cbd_txt_tglcbd" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_txt_supp2" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_txt_curr" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_txt_create_user" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_txt_status" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_txt_keterangan" class="modal-body col-6" style="font-size: 13px; padding: 0.5rem;"></div>
                  <div id="cbd_details" class="modal-body col-12" style="font-size: 13px; padding: 0.5rem;"></div>
              </div>
          </div>
      </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#cbd_mytable').DataTable({
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
    function ubahtanggalCbd(value) {
        var tanggal = document.getElementById('cbd_tanggal').value;
        $.ajax({
            type: 'POST',
            url: 'getnomor_pv_cbd.php',
            data: { 'tanggal': tanggal },
            success: function (response) {
                $('#cbd_nokontrabon').val(response);
                updateNoKontraBonCbd();
            }
        });
        $("#cbd_tanggal").val(tanggal);
    }

    function updateNoKontraBonCbd() {
        let pc = document.getElementById("cbd_profit_center").value;
        let input = document.getElementById("cbd_nokontrabon");
        let current = input.value.trim();

        let parts = current.split("/");

        // belum ada PC: [0]=PV-AP, [1]=CBD, [2]=YYYY, [3]=MM, [4]=00001
        if (parts.length === 5) {
            input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[2] + "/" + parts[3] + "/" + parts[4];
        }
        // sudah ada PC: [0]=PV-AP, [1]=CBD, [2]=PC lama, [3]=YYYY, [4]=MM, [5]=00001
        else if (parts.length === 6) {
            input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[3] + "/" + parts[4] + "/" + parts[5];
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        let savedPc = localStorage.getItem("pv_cbd_profit_center");
        if (savedPc) {
            document.getElementById("cbd_profit_center").value = savedPc;
            updateNoKontraBonCbd();
        }
    });

    document.getElementById("cbd_profit_center").addEventListener("change", function () {
        localStorage.setItem("pv_cbd_profit_center", this.value);
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cbd_mysupp").on("click", function () {
            let profit_center = $('#cbd_profit_center option').filter(':selected').val();

            if (profit_center == "") {
                Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
                $("#cbd_profit_center").focus();
                return;
            }

            $("#cbd_mymodal").modal("show");
        });
    });
</script>

<script type="text/javascript">
    $(document).on('change', '#cbd_sel_bank_account', function () {
        var opt = $(this).find('option:selected');
        var currency    = opt.data('currency')    || '';
        var bankname    = opt.data('bankname')    || '';
        var beneficiary = opt.data('beneficiary') || '';

        $('#cbd_disp_currency').val(currency);
        $('#cbd_disp_bankname').val(bankname);
        $('#cbd_disp_beneficiary').val(beneficiary);
    });

    $(document).on('change', '#cbd_from_account', function () {
        var opt = $(this).find('option:selected');
        $('#cbd_from_bank').val(opt.data('bank') || '');
        $('#cbd_from_bank_curr').val(opt.data('currency') || '');
    });
</script>

<script type="text/javascript">
    // Lihat pv_regular.php untuk penjelasan lengkap: toFixed() native JS salah
    // membulatkan nilai seperti 0.365 (jadi "0.36") karena representasi
    // floating-point biner. roundHalfUp() menutup celah itu supaya angka yang
    // ditampilkan selalu sama dengan yang tersimpan di database.
    function roundHalfUp(num, decimals) {
        var factor = Math.pow(10, decimals || 0);
        var nudged = (Number(num) || 0) * factor * (1 + Number.EPSILON);
        return Math.round(nudged) / factor;
    }

    function formatMoneyCbd(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            amount = roundHalfUp(Math.abs(Number(amount) || 0), decimalCount);
            let i = parseInt(amount.toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e);
        }
    }

    $("#cbd_mytable").on("change", "input.cbd_chk", function () {
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_pph = 0;
        var sum_total = 0;
        var nopo = '';
        var tglpo = '';

        $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);
        $("#cbd_mytable input.cbd_chk").prop('disabled', true);

        $("#cbd_mytable input.cbd_chk:checked").each(function () {
            var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
            var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'), 10) || 0;
            var po = $(this).closest('tr').find('td:eq(13)').attr('value');
            var tgl = $(this).closest('tr').find('td:eq(14)').attr('value');
            var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(), 10) || 0;
            var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
            select_pph.prop('disabled', false);

            sum_sub += price;
            sum_tax += tax;
            sum_pph += price * (pph / 100);
            sum_total = sum_sub + sum_tax - sum_pph;
            nopo = po;
            tglpo = tgl;
        });

        $("#cbd_mytable input.cbd_chk").prop('disabled', false);
        $("#cbd_subtotal").val(formatMoneyCbd(sum_sub));
        $("#cbd_subtotal_h").val(sum_sub);
        $("#cbd_pajak").val(formatMoneyCbd(sum_tax));
        $("#cbd_pajak_h").val(sum_tax);
        $("#cbd_pph").val(formatMoneyCbd(sum_pph));
        $("#cbd_pph_h").val(sum_pph);
        $("#cbd_total").val(formatMoneyCbd(sum_total));
        $("#cbd_total_h").val(sum_total);
        $("#cbd_no_po").val(nopo);
        $("#cbd_tgl_po").val(tglpo);
    });
</script>

<script type="text/javascript">
    $("#cbd_mytable").on('change', "select[name=combo_pph]", function () {
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_pph = 0;
        var sum_total = 0;

        $("#cbd_mytable input.cbd_chk:checked").each(function () {
            var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
            var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'), 10) || 0;
            var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(), 10) || 0;

            sum_sub += price;
            sum_tax += tax;
            sum_pph += price * (pph / 100);
            sum_total = sum_sub + sum_tax - sum_pph;
        });

        $("#cbd_subtotal").val(formatMoneyCbd(sum_sub));
        $("#cbd_subtotal_h").val(sum_sub);
        $("#cbd_pajak").val(formatMoneyCbd(sum_tax));
        $("#cbd_pajak_h").val(sum_tax);
        $("#cbd_pph").val(formatMoneyCbd(sum_pph));
        $("#cbd_pph_h").val(sum_pph);
        $("#cbd_total").val(formatMoneyCbd(sum_total));
        $("#cbd_total_h").val(sum_total);
    });
</script>

<script type="text/javascript">
    $("#cbd-form-simpan").on("click", "#cbd_simpan", function () {
        var no_kbon_h = document.getElementById('cbd_nokontrabon').value;
        var tgl_kbon_h = document.getElementById('cbd_tanggal').value;
        var no_po_h = document.getElementById('cbd_no_po').value;
        var tgl_po_h = document.getElementById('cbd_tgl_po').value;
        var nama_supp_h = document.getElementById('cbd_txt_supp').value;
        var no_faktur_h = document.getElementById('cbd_no_faktur').value;
        var supp_inv_h = document.getElementById('cbd_txt_inv').value;
        var tgl_inv_h = document.getElementById('cbd_txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('cbd_txt_tgltempo').value;
        var curr_h = document.getElementById('cbd_matauang').value;
        var sub_h = document.getElementById('cbd_subtotal_h').value;
        var tax_h = document.getElementById('cbd_pajak_h').value;
        var pph_h = document.getElementById('cbd_pph_h').value;
        var total_h = document.getElementById('cbd_total_h').value;
        var jurnal_h = document.getElementById('cbd_jurnal').value;
        var create_user_h = '<?php echo $user; ?>';
        var start_date = document.getElementById('cbd_start_date').value;
        var end_date = document.getElementById('cbd_end_date').value;
        var profit_center = document.getElementById('cbd_profit_center').value;
        var bank_account = $('#cbd_sel_bank_account option').filter(':selected').val();
        var from_account = document.getElementById('cbd_from_account').value;
        var from_bank = document.getElementById('cbd_from_bank').value;
        var from_bank_curr = document.getElementById('cbd_from_bank_curr').value;
        var item_type = document.getElementById('cbd_item_type').value;

        if (profit_center === '') {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
            document.getElementById('cbd_profit_center').focus();
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
        if (document.querySelectorAll("#cbd_mytable input.cbd_chk:checked").length === 0) {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please check the FTR CBD number'});
            return;
        }
        if (total_h === '') {
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select at least 1 row to calculate the total'});
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'insertkbon_h_pv_cbd.php',
            data: { 'no_kbon_h': no_kbon_h, 'tgl_kbon_h': tgl_kbon_h, 'no_po_h': no_po_h, 'tgl_po_h': tgl_po_h, 'nama_supp_h': nama_supp_h, 'no_faktur_h': no_faktur_h, 'supp_inv_h': supp_inv_h, 'tgl_inv_h': tgl_inv_h, 'tgl_tempo_h': tgl_tempo_h, 'curr_h': curr_h, 'create_user_h': create_user_h, 'sub_h': sub_h, 'tax_h': tax_h, 'pph_h': pph_h, 'total_h': total_h, 'profit_center': profit_center, 'bank_account': bank_account, 'from_account': from_account, 'from_bank': from_bank, 'from_bank_curr': from_bank_curr, 'item_type': item_type },
            cache: 'false',
            success: function (response) {
                console.log(response);

                $("#cbd_mytable input.cbd_chk:checked").each(function () {
                    var no_cbd = $(this).closest('tr').find('td:eq(1)').attr('value');
                    var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
                    var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
                    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(), 10) || 0;
                    var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');

                    $.ajax({
                        type: 'POST',
                        url: 'insertkbon_cbd.php',
                        data: {
                            'no_kbon': no_kbon_h, 'tgl_kbon': tgl_kbon_h, 'jurnal': jurnal_h, 'no_cbd': no_cbd, 'no_po': no_po,
                            'nama_supp': nama_supp_h, 'tgl_po': tgl_po, 'no_faktur': no_faktur_h, 'supp_inv': supp_inv_h, 'tgl_inv': tgl_inv_h, 'tgl_tempo': tgl_tempo_h,
                            'curr': curr_h, 'ceklist': '1', 'create_user': create_user_h, 'sum_sub': sub_h, 'sum_tax': tax_h, 'sum_pph': pph_h, 'sum_total': total_h,
                            'start_date': start_date, 'end_date': end_date, 'pph': pph, 'idtax': idtax
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
    $('#cbd_mytable tbody').on('click', 'td:eq(1)', function () {
        $('#cbd_mymodalbpb').modal('show');
        var noftrcbd = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_cbd = $(this).closest('tr').find('td:eq(12)').text();
        var supp = $(this).closest('tr').find('td:eq(8)').attr('value');
        var curr = document.getElementById('cbd_matauang').value;
        var create_user = $(this).closest('tr').find('td:eq(11)').attr('value');
        var status = $(this).closest('tr').find('td:eq(9)').attr('value');
        var keterangan = $(this).closest('tr').find('td:eq(10)').attr('value');

        $.ajax({
            type: 'post',
            url: 'ajaxcbd.php',
            data: { 'noftrcbd': noftrcbd },
            success: function (data) {
                $('#cbd_details').html(data);
            }
        });

        $('#cbd_txt_cbd').html(noftrcbd);
        $('#cbd_txt_tglcbd').html('Tgl FTR CBD : ' + tgl_cbd);
        $('#cbd_txt_supp2').html('Supplier : ' + supp);
        $('#cbd_txt_curr').html('Currency : ' + curr);
        $('#cbd_txt_create_user').html('Create By : ' + create_user);
        $('#cbd_txt_status').html('Status : ' + status);
        $('#cbd_txt_keterangan').html('Keterangan : ' + keterangan);
    });
</script>
