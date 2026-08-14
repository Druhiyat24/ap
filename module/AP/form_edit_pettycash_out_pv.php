<?php
include '../header.php';
include 'pv_data_functions.php';
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
  .table-gradient2 th {
    background: #3B82F6;
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

$sqlH = mysqli_query($conn2, "select a.*, concat(a.coa_akun,' ',b.nama_coa) nama_akun from c_petty_cashout_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun where a.no_pco = '$doc_num_esc' and a.reff = 'Payment Voucher'");
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

// Profit Center akun kas terpilih (untuk isi ulang label PC saat page load)
$sqlPcAkun = mysqli_query($conn2, "select IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc, kode_cash from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $rowH['coa_akun']) . "'");
$rowPcAkun = mysqli_fetch_assoc($sqlPcAkun);

// Rate fallback utk DP/CBD (tidak punya kolom rate sendiri) - sama seperti
// bank-out/get_pv_ajax.php.
function getRateByCurrDate($conn2, $curr, $tgl)
{
    if (empty($curr) || empty($tgl)) {
        return 1;
    }
    $sql = mysqli_query($conn2, "select rate from ap_masterrate where v_codecurr = 'PAJAK' and curr = '" . mysqli_real_escape_string($conn2, $curr) . "' and tanggal = '" . mysqli_real_escape_string($conn2, $tgl) . "' limit 1");
    $row = mysqli_fetch_assoc($sql);
    return !empty($row['rate']) ? (float) $row['rate'] : 1;
}
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit"></i> FORM EDIT PETTY CASH OUT</h5>
    </div>

<form id="form-data5" method="post">
    <input type="hidden" id="doc_num5" value="<?= htmlspecialchars($doc_num); ?>">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Doc Number</b></label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($doc_num); ?>">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active5" id="tgl_active5" class="form-control tanggal" value="<?= date("d-m-Y", strtotime($rowH['tgl_pco'])); ?>" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp5" id="nama_supp5" data-live-search="true">
                        <option value="<?= htmlspecialchars($rowH['nama_supp']); ?>" selected><?= htmlspecialchars($rowH['nama_supp']); ?></option>
                        <?php
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='S' and supplier != '' and supplier != '" . mysqli_real_escape_string($conn1, $rowH['nama_supp']) . "' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            echo "<option value='" . $row['Supplier'] . "'>" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num5" id="ref_num5" class="form-control" value="Payment Voucher" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_kas_show5" name="profit_center_kas_show5" value="<?= htmlspecialchars($rowPcAkun['nama_pc'] ?? ''); ?>" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Reff Date</b></label>
                    <input type="text" name="tgl_filawal5" id="tgl_filawal5" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>-</b></label>
                    <input type="text" name="tgl_filakhir5" id="tgl_filakhir5" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" id="btn_tarik_pv5" class="btn btn-primary">
                     <i class="fas fa-search"></i> Search
                 </button>
             </div>
             <div class="col-md-3 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account5" name="account5" data-live-search="true">
                        <option value="">Select Account</option>
                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            $selected = ($row['id_coa'] == $rowH['coa_akun']) ? 'selected' : '';
                            echo "<option value='" . $row['id_coa'] . "' data-kode5='" . $row['kode_cash'] . "' data-pc5='" . $row['profit_center'] . "' data-namapc5='" . $row['nama_pc'] . "' $selected>" . $row['coa'] . " </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency5" name="currency5" value="<?= htmlspecialchars($rowH['curr']); ?>" readonly>
                    <input type="hidden" class="form-control" id="kode_kas5" name="kode_kas5" value="<?= htmlspecialchars($rowPcAkun['kode_cash'] ?? ''); ?>" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas5" name="profit_center_kas5" value="<?= htmlspecialchars($rowPcAkun['profit_center'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas5" name="amount_kas5" value="<?= number_format($rowH['amount'], 2); ?>">
                </div>

            <div class="col-md-2 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Cash Flow Category</b></label>
                <select class="form-control select2" name="cash_flow5" id="cash_flow5" data-live-search="true">
                    <option value="">Select Cash Flow Category</option>
                    <?php
                    $id_cash_flow5 = $rowH['id_cash_flow'] ?? '';
                    $sqlCf5 = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                    while ($rowCf5 = mysqli_fetch_assoc($sqlCf5)) {
                        $selectedCf5 = ($rowCf5['id'] == $id_cash_flow5) ? ' selected="selected"' : '';
                        echo '<option value="'.$rowCf5['id'].'"'.$selectedCf5.'>'.$rowCf5['show_subcategory'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-7 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30" type="text" class="form-control " name="pesan5" id="pesan5" placeholder="descriptions..." required><?= htmlspecialchars($rowH['deskripsi']); ?></textarea>
            </div>

        </div>
       <div class="card-body p-2">
          <div class="table-responsive">
              <table id="table-pv5"
              class="table table-striped table-bordered table-hover table-sm nowrap" >
              <thead class="table-gradient">
                <tr>
                    <th style="text-align: center;vertical-align: middle;">Check</th>
                    <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                    <th style="text-align: center;vertical-align: middle;">No PV</th>
                    <th style="text-align: center;vertical-align: middle;">PV Date</th>
                    <th style="text-align: center;vertical-align: middle;">Due Date</th>
                    <th style="text-align: center;vertical-align: middle;">DPP</th>
                    <th style="text-align: center;vertical-align: middle;">PPN</th>
                    <th style="text-align: center;vertical-align: middle;">PPH</th>
                    <th style="text-align: center;vertical-align: middle;">Total</th>
                    <th style="text-align: center;vertical-align: middle;">Rate</th>
                    <th style="text-align: center;vertical-align: middle;">Amount</th>
                    <th style="text-align: center;vertical-align: middle;">Amount IDR Eqv</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // =========================
            // PRE-LOAD PV YANG SUDAH TERTAUT KE DOKUMEN INI
            // (sama seperti update_pv_cash.php: outstanding dihitung dengan
            // mengecualikan alokasi dokumen ini sendiri, supaya user bisa
            // menaikkan/menurunkan amount PV yang sama)
            // =========================
            $sqlDet = mysqli_query($conn2, "select no_reff, type_pv, amount from c_petty_cashout_det where no_pco = '$doc_num_esc'");
            while ($rowDet = mysqli_fetch_assoc($sqlDet)) {

                $no_pv = $rowDet['no_reff'];
                $type_pv = !empty($rowDet['type_pv']) ? $rowDet['type_pv'] : 'Regular';
                $currentAmount = (float) $rowDet['amount'];

                $filtersOne = [
                    'nama_supp'   => 'ALL',
                    'status'      => 'ALL',
                    'filter_date' => 'tgl_kbon',
                    'start_date'  => '',
                    'end_date'    => '',
                ];

                $rowOne = null;

                if ($type_pv === 'Regular') {
                    $filtersOne['no_kbon'] = $no_pv;
                    $rows = getDataRegular($conn1, $conn2, $filtersOne, '1', '1', '');
                    $rowOne = $rows[0] ?? null;
                } elseif ($type_pv === 'Installment') {
                    $filtersOne['no_kbon_det'] = $no_pv;
                    $rows = getDataInstallment($conn1, $conn2, $filtersOne, '1', '1', '');
                    $rowOne = $rows[0] ?? null;
                } elseif ($type_pv === 'DP') {
                    $filtersOne['no_kbon'] = $no_pv;
                    $rows = getDataDp($conn1, $conn2, $filtersOne, '1', '1', '');
                    $rowOne = $rows[0] ?? null;
                } elseif ($type_pv === 'CBD') {
                    $filtersOne['no_kbon'] = $no_pv;
                    $rows = getDataCbd($conn1, $conn2, $filtersOne, '1', '1', '');
                    $rowOne = $rows[0] ?? null;
                } elseif ($type_pv === 'SaldoAwal') {
                    $filtersOne['no_kbon'] = $no_pv;
                    $rows = getDataSaldoAwal($conn2, $filtersOne);
                    $rowOne = $rows[0] ?? null;
                }

                if (!$rowOne) {
                    continue;
                }

                $pc = $rowOne['profit_center'];
                $pcLabelRow = mysqli_fetch_assoc(mysqli_query($conn2, "select CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where kode_pc = '" . mysqli_real_escape_string($conn2, $pc) . "'"));
                $namaPc = $pcLabelRow['tampil'] ?? $pc;

                if ($type_pv === 'DP' || $type_pv === 'CBD') {
                    $rate = getRateByCurrDate($conn2, $rowOne['curr'], $rowOne['tgl_kbon_raw'] ?? null);
                } else {
                    $rate = !empty($rowOne['rate']) ? (float) $rowOne['rate'] : 1;
                }

                $alreadyPaidOther = getAlreadyPaidFor($conn2, $type_pv, $no_pv) - $currentAmount;
                $outstanding = (float) $rowOne['total_raw'] - $alreadyPaidOther;

                $totalIdr = $currentAmount * $rate;
                ?>
                <tr>
                    <td>
                        <input type="checkbox" class="chk_pv" checked>
                    </td>
                    <td class="pc_pv" data-pcpv="<?= htmlspecialchars($pc); ?>"><?= htmlspecialchars($namaPc); ?></td>
                    <td class="no_pv" data-nopv="<?= htmlspecialchars($no_pv); ?>" data-typepv="<?= htmlspecialchars($type_pv); ?>" data-account="<?= htmlspecialchars($rowH['coa_akun']); ?>"><?= htmlspecialchars($no_pv); ?></td>
                    <td><?= !empty($rowOne['tgl_kbon_raw']) ? date("d-M-Y", strtotime($rowOne['tgl_kbon_raw'])) : '-'; ?></td>
                    <td><?= !empty($rowOne['tgl_tempo_raw']) ? date("d-M-Y", strtotime($rowOne['tgl_tempo_raw'])) : '-'; ?></td>
                    <td><?= number_format($rowOne['subtotal_raw'], 2); ?></td>
                    <td><?= number_format($rowOne['tax_raw'], 2); ?></td>
                    <td><?= number_format($rowOne['pph_raw'], 2); ?></td>
                    <td class="total_pv" data-total="<?= $outstanding; ?>"><?= number_format($outstanding, 2); ?></td>
                    <td class="rate_pv" data-ratepv="<?= $rate; ?>" data-curr="<?= htmlspecialchars($rowOne['curr']); ?>"><?= number_format($rate, 2); ?></td>
                    <td style="width: 170px;">
                        <input type="text" class="form-control txt_amount_pv" style="text-align:right" value="<?= number_format($currentAmount, 2); ?>">
                    </td>
                    <td style="width: 170px;">
                        <input type="text" class="form-control txt_amount_pv_idr" style="text-align:right" value="<?= number_format($totalIdr, 2); ?>">
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card-body p-2">
  <div class="table-responsive">
    <table id="table-pv5_adjust"
    class="table table-striped table-bordered table-hover table-sm nowrap" >
    <thead class="table-gradient2">
        <tr>
            <th style="width:10px;">-</th>
            <th>Coa</th>
            <th>Profit Center</th>
            <th>Cost Center</th>
            <th>Reff Doc</th>
            <th>Reff Date</th>
            <th style="width:80px;">Currency</th>
            <th style="width:120px;">Debit</th>
            <th style="width:120px;">Credit</th>
            <th>Description</th>
            <th style="width:40px;">Cek</th>
        </tr>
    </thead>
    <tbody id="tbody5">
        <?php
        $sqlAdj = mysqli_query($conn2, "select a.id_coa, concat(b.no_coa,' ',b.nama_coa) nama_coa, a.profit_center, a.no_cc, concat(c.no_cc,' - ',c.cc_name) nama_cc, a.reff_doc, a.reff_date, a.t_debit, a.t_credit, a.deskripsi from c_petty_cashout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa left join b_master_cc c on c.no_cc = a.no_cc where a.no_pco = '$doc_num_esc'");
        while ($rowAdj = mysqli_fetch_assoc($sqlAdj)) {
            ?>
            <tr>
            <td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

            <td>
            <select class="form-control selectpicker no_coa5" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
                <option value="<?= htmlspecialchars($rowAdj['id_coa']); ?>" selected><?= htmlspecialchars($rowAdj['nama_coa']); ?></option>
                <option value="-">-</option>
                <?php
                $sqlCoa = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '" . mysqli_real_escape_string($conn1, $rowAdj['id_coa']) . "'");
                foreach ($sqlCoa as $coa) : ?>
                <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                <?php endforeach; ?>
            </select>
            </td>

            <td>
            <select class="form-control selectpicker prof_ctr5" name="prof_ctr5[]" data-live-search="true" data-width="100%">
                <?php
                $sqlPc = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
                while ($fc = mysqli_fetch_assoc($sqlPc)) {
                    $sel = ($fc['kode_pc'] == $rowAdj['profit_center']) ? 'selected' : '';
                    echo '<option value="' . $fc['kode_pc'] . '" ' . $sel . '>' . $fc['tampil'] . '</option>';
                }
                ?>
            </select>
            </td>

            <td>
            <select class="form-control selectpicker cost_ctr5" name="cost_ctr5[]" data-live-search="true" data-width="100%">
                <option value="<?= htmlspecialchars($rowAdj['no_cc']); ?>" selected><?= htmlspecialchars($rowAdj['nama_cc']); ?></option>
                <?php
                $sqlCc = mysqli_query($conn1, "select no_cc as code_combine, concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '" . mysqli_real_escape_string($conn1, $rowAdj['no_cc']) . "'");
                foreach ($sqlCc as $ccs) : ?>
                <option value="<?= $ccs["code_combine"]; ?>"><?= $ccs["cost_name"]; ?></option>
                <?php endforeach; ?>
            </select>
            </td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff5[]" value="<?= htmlspecialchars($rowAdj['reff_doc']); ?>" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date5[]" value="<?= !empty($rowAdj['reff_date']) && $rowAdj['reff_date'] != '1970-01-01' ? date('d-m-Y', strtotime($rowAdj['reff_date'])) : ''; ?>" autocomplete="off"></td>

            <td>
            <select class="form-control selectpicker currenc5" name="currenc5[]">
                <option value="IDR" selected>IDR</option>
            </select>
            </td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount5[]" value="<?= $rowAdj['t_debit'] > 0 ? $rowAdj['t_debit'] : ''; ?>" <?= $rowAdj['t_debit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_amt5(this)" autocomplete="off"></td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit5[]" value="<?= $rowAdj['t_credit'] > 0 ? $rowAdj['t_credit'] : ''; ?>" <?= $rowAdj['t_credit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_cre5(this)" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan5[]" value="<?= htmlspecialchars($rowAdj['deskripsi']); ?>" autocomplete="off"></td>

            <td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>
            </tr>
            <?php
        }
        ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="11" align="center">

                <button type="button" class="btn btn-primary"
                onclick="addRow5('tbody5')">
                Add Row
            </button>

            <button type="button" class="btn btn-warning"
            onclick="InsertRow5('tbody5')">
            Insert Row
        </button>

        <button type="button" class="btn btn-danger"
        onclick="deleteRow5('tbody5')">
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
                        <input type="text" class="total-stat-value" id="tot_debit_nag_pv5" name="tot_debit_nag_pv5" readonly>
                        <input type="hidden" id="h_tot_debit_nag_pv5" name="h_tot_debit_nag_pv5" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nag_pv5" name="tot_credit_nag_pv5" readonly>
                        <input type="hidden" id="h_tot_credit_nag_pv5" name="h_tot_credit_nag_pv5" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_nak_pv5" name="tot_debit_nak_pv5" readonly>
                        <input type="hidden" id="h_tot_debit_nak_pv5" name="h_tot_debit_nak_pv5" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nak_pv5" name="tot_credit_nak_pv5" readonly>
                        <input type="hidden" id="h_tot_credit_nak_pv5" name="h_tot_credit_nak_pv5" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_pv5" name="tot_debit_pv5" readonly>
                        <input type="hidden" id="h_tot_debit_pv5" name="h_tot_debit_pv5" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_pv5" name="tot_credit_pv5" readonly>
                        <input type="hidden" id="h_tot_credit_pv5" name="h_tot_credit_pv5" readonly>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="form-row">
    <div class="col-md-3 mt-3 mb-2">
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan5" id="simpan5"><span class="fa fa-floppy-o"></span> Save</button>
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

    // Hitung ulang total awal saat page load (baris PV & adjust sudah terisi dari server)
    $(document).ready(function(){
        hitungTotalPV5();
    });
</script>

<script type="text/javascript">

let isManualAmountPV5 = true; // amount header sudah terisi dari data lama, jangan langsung ditimpa
let tablePV5;

function initTablePV5(){
  if ($.fn.DataTable.isDataTable('#table-pv5')) {
    $('#table-pv5').DataTable().destroy();
  }
  tablePV5 = $('#table-pv5').DataTable({
    paging: true,
    searching: true,
    ordering: false,
    info: false,
    autoWidth: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      paginate: { previous: "Prev", next: "Next" }
    }
  });
}

$('#account5').on('change', function() {
  let kode5 = $(this).find(':selected').data('kode5');
  let pc5 = $(this).find(':selected').data('pc5');
  let namapc5 = $(this).find(':selected').data('namapc5');

  $('#profit_center_kas_show5').val(namapc5);
  $('#profit_center_kas5').val(pc5);
  $('#currency5').val('IDR');
  $('#kode_kas5').val(kode5);
  hitungTotalPV5();
});

$('#btn_tarik_pv5').on('click', function(){

  let tgl_awal  = $('#tgl_filawal5').val();
  let tgl_akhir = $('#tgl_filakhir5').val();
  let supplier  = $('#nama_supp5').val();

  if(tgl_awal == '' || tgl_akhir == ''){
    Swal.fire('Warning','Tanggal harus diisi','warning');
    return;
  }

  if(supplier == ''){
    Swal.fire('Warning','Supplier harus dipilih','warning');
    return;
  }

  $.ajax({
    url: 'bank-out/get_pv_ajax.php',
    type: 'POST',
    data: {
      tgl_awal: tgl_awal,
      tgl_akhir: tgl_akhir,
      supplier: supplier,
      fund_type: 'CASH'
    },
    beforeSend:function(){
      Swal.fire({ title:'Loading...', allowOutsideClick:false, didOpen:()=>{ Swal.showLoading(); } });
    },
    success:function(res){

      // no_pv yang sudah ada di tabel (baik yang dari data lama maupun hasil
      // pencarian sebelumnya) tidak boleh ditampilkan dobel
      let existing = [];
      $('#table-pv5 .no_pv').each(function(){
        existing.push($(this).data('nopv'));
      });

      let $tmp = $('<tbody>' + res + '</tbody>');
      $tmp.find('tr').each(function(){
        let nopv = $(this).find('.no_pv').data('nopv');
        if(existing.includes(nopv)){
          $(this).remove();
        }
      });

      if ($.fn.DataTable.isDataTable('#table-pv5')) {
        $('#table-pv5').DataTable().destroy();
      }
      $('#table-pv5 tbody').append($tmp.html());
      Swal.close();
      initTablePV5();
    }
  });

});

$('#table-pv5').on('change', '.chk_pv', function(){

  let tr = $(this).closest('tr');

  let total = parseFloat(tr.find('.total_pv').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_pv').data('ratepv')) || 0;
  let input = tr.find('.txt_amount_pv');
  let input_idr = tr.find('.txt_amount_pv_idr');
  let total_idr = total * rate;

  if($(this).is(':checked')){

    input.prop('disabled', false);
    if(!input.val()){
      input.val(total.toLocaleString('en-US'));
    }
    input_idr.prop('disabled', false);
    if(!input_idr.val()){
      input_idr.val(total_idr.toLocaleString('en-US'));
    }

    if(!$('#account5').val()){
      let pvAccount = tr.find('.no_pv').data('account');
      if(pvAccount && $('#account5 option[value="' + pvAccount + '"]').length){
        $('#account5').val(pvAccount).trigger('change');
      }
    }

    hitungTotalPV5();

  }else{

    input.prop('disabled', true);
    input.val('');

    input_idr.prop('disabled', true);
    input_idr.val('');

    if($('#table-pv5 .chk_pv:checked').length === 0){
      $('#amount_kas5').val('');
      isManualAmountPV5 = false;
      resetTotalPV5();
    }else{
      hitungTotalPV5();
    }

  }

});

$('#table-pv5').on('keyup', '.txt_amount_pv', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_pv').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_pv').data('ratepv')) || 0;

  let val = $(this).val().replace(/,/g,'');
  val = parseFloat(val) || 0;

  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
  }

  $(this).val(val.toLocaleString('en-US'));

  let val_idr = val * rate;

  tr.find('.txt_amount_pv_idr').val(val_idr.toLocaleString('en-US'));

  hitungTotalPV5();

});

$('#table-pv5').on('keyup', '.txt_amount_pv_idr', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_pv').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_pv').data('ratepv')) || 0;

  let val_idr = $(this).val().replace(/,/g,'');
  val_idr = parseFloat(val_idr) || 0;

  let val = rate > 0 ? val_idr / rate : 0;

  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
    val_idr = val * rate;
  }

  tr.find('.txt_amount_pv').val(val.toLocaleString('en-US'));
  $(this).val(val_idr.toLocaleString('en-US'));

  hitungTotalPV5();

});

$('#amount_kas5').on('keyup change', function(){
  isManualAmountPV5 = true;
  hitungTotalPV5();
});

$(document).on('change', '.prof_ctr5', function() {
  const selectedProfCtr = $(this).val();
  const row = $(this).closest('tr');
  const selectedCoa = row.find('select.no_coa5').val() || '-';
  updateCostCenter5(selectedProfCtr, selectedCoa, row);
});

$(document).on('change', '.no_coa5', function() {
  const selectedCoa = $(this).val();
  const row = $(this).closest('tr');
  const selectedProfCtr = row.find('select.prof_ctr5').val() || '-';
  updateCostCenter5(selectedProfCtr, selectedCoa, row);
});

function updateCostCenter5(profCtr, noCoa, row) {
  const costCtrDropdown = $(row).find('.cost_ctr5');

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

function addRow5(tableID) {

  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var element = `
<tr>
<td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa5" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr5" name="prof_ctr5[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr5" name="cost_ctr5[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff5[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date5[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker currenc5" name="currenc5[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount5[]" oninput="modal_input_amt5(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit5[]" oninput="modal_input_cre5(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan5[]" autocomplete="off"></td>

<td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>

</tr>
`;

  row.innerHTML = element;

  $('.selectpicker').selectpicker('refresh');
  $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });

  var headerPC = $('#profit_center_kas5').val();
  if (headerPC) {
    $(row).find('.prof_ctr5').val(headerPC);
    $(row).find('.prof_ctr5').selectpicker('refresh');
  }

}

function deleteRow5(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var deleted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a5[]"]');

      if (chkbox && chkbox.checked) {
        table.deleteRow(i);
        deleted = true;
        rowCount--;
      }

    }

    if (!deleted) {
      Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin dihapus' });
    }

    $('.selectpicker').selectpicker('refresh');
    hitungTotalPV5();

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function InsertRow5(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var inserted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a5[]"]');

      if (chkbox && chkbox.checked) {

        var element2 = `
<tr>
<td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa5" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr5" name="prof_ctr5[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr5" name="cost_ctr5[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff5[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date5[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker currenc5" name="currenc5[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount5[]" oninput="modal_input_amt5(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit5[]" oninput="modal_input_cre5(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan5[]" autocomplete="off"></td>

<td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>

</tr>
`;

        var newRow = table.insertRow(i + 1);
        newRow.innerHTML = element2;
        inserted = true;

        var headerPC = $('#profit_center_kas5').val();
        if (headerPC) {
          $(newRow).find('.prof_ctr5').val(headerPC);
          $(newRow).find('.prof_ctr5').selectpicker('refresh');
        }

      }

    }

    if (!inserted) {
      Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin disisipkan' });
    }

    $('.selectpicker').selectpicker('refresh');
    $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function modal_input_amt5(el){

  let row = $(el).closest('tr');
  let debit  = parseFloat($(el).val()) || 0;
  let creditInput = row.find('input[name="txt_credit5[]"]');

  if(debit > 0){
    creditInput.val(0);
    creditInput.prop('readonly',true);
  }else{
    creditInput.prop('readonly',false);
  }

  hitungTotalPV5();

}

function modal_input_cre5(el){

  let row = $(el).closest('tr');
  let credit = parseFloat($(el).val()) || 0;
  let debitInput = row.find('input[name="txt_amount5[]"]');

  if(credit > 0){
    debitInput.val(0);
    debitInput.prop('readonly',true);
  }else{
    debitInput.prop('readonly',false);
  }

  hitungTotalPV5();

}

function resetTotalPV5(){
  $('#tot_debit_nag_pv5, #tot_debit_nak_pv5, #tot_debit_pv5').val('');
  $('#tot_credit_nag_pv5, #tot_credit_nak_pv5, #tot_credit_pv5').val('');
}

function hitungTotalPV5(){

  let total_pv = 0;
  let nag_debit = 0;
  let nak_debit = 0;
  let nag_credit = 0;
  let nak_credit = 0;
  let curr_h = 'IDR';

  $('#table-pv5 .chk_pv:checked').each(function(){

    let tr = $(this).closest('tr');

    let val = getNumber(tr.find('.txt_amount_pv').val());
    let val_idr = getNumber(tr.find('.txt_amount_pv_idr').val());

    let pc = (tr.find('.pc_pv').data('pcpv') || '').toString().trim().toUpperCase();

    if (curr_h == 'IDR') {
      total_pv += val_idr;
    }else{
      total_pv += val;
    }

    if(pc === 'NAG'){
      nag_debit += val_idr;
    }else if(pc === 'NAK'){
      nak_debit += val_idr;
    }

  });

  let header_amount = total_pv;

  if(!isManualAmountPV5){
    $('#amount_kas5').val(header_amount.toLocaleString('en-US'));
  }

  let header_amount_idr = isManualAmountPV5 ? getNumber($('#amount_kas5').val()) : total_pv;

  let header_pc = ($('#profit_center_kas5').val() || '').trim().toUpperCase();

  if(header_pc === 'NAG'){
    nag_credit += header_amount_idr;
  }else if(header_pc === 'NAK'){
    nak_credit += header_amount_idr;
  }

  $('#tbody5 tr').each(function(){

    let tr = $(this);

    let pc = (tr.find('select.prof_ctr5').first().val() || '').trim().toUpperCase();

    let debit  = getNumber(tr.find('input[name="txt_amount5[]"]').val());
    let credit = getNumber(tr.find('input[name="txt_credit5[]"]').val());

    if(pc === 'NAG'){
      nag_debit  += debit;
      nag_credit += credit;
    }else if(pc === 'NAK'){
      nak_debit  += debit;
      nak_credit += credit;
    }

  });

  let grand_debit  = nag_debit + nak_debit;
  let grand_credit = nag_credit + nak_credit;

  $('#tot_debit_nag_pv5').val(nag_debit.toLocaleString('en-US'));
  $('#tot_debit_nak_pv5').val(nak_debit.toLocaleString('en-US'));
  $('#tot_debit_pv5').val(grand_debit.toLocaleString('en-US'));

  $('#tot_credit_nag_pv5').val(nag_credit.toLocaleString('en-US'));
  $('#tot_credit_nak_pv5').val(nak_credit.toLocaleString('en-US'));
  $('#tot_credit_pv5').val(grand_credit.toLocaleString('en-US'));

}

$('#simpan5').on('click', function(){

  let header = {
    doc_num    : $('#doc_num5').val(),
    ref        : $('#ref_num5').val(),
    tgl        : $('#tgl_active5').val(),
    supp       : $('#nama_supp5').val(),
    account    : $('#account5').val(),
    currency   : $('#currency5').val(),
    kode_kas   : $('#kode_kas5').val(),
    pc_header  : $('#profit_center_kas5').val(),
    amount     : getNumber($('#amount_kas5').val()),
    desc       : $('#pesan5').val(),
    cash_flow  : $('#cash_flow5').val()
  };

  if(!header.tgl){
    Swal.fire('Warning','Tanggal wajib diisi','warning');
    return;
  }

  if(!header.supp){
    Swal.fire('Warning','Supplier wajib diisi','warning');
    return;
  }

  if(!header.account){
    Swal.fire('Warning','Account belum terisi','warning');
    return;
  }

  if(!header.desc || !header.desc.trim()){
    Swal.fire('Warning','Description tidak boleh kosong','warning');
    return;
  }

  if(!header.cash_flow){
    Swal.fire('Warning','Cash Flow Category tidak boleh kosong','warning');
    return;
  }

  if(header.amount <= 0){
    Swal.fire('Warning','Amount tidak boleh 0','warning');
    return;
  }

  if($('#table-pv5 .chk_pv:checked').length === 0){
    Swal.fire('Warning','Pilih minimal 1 Payment Voucher','warning');
    return;
  }

  let total_debit  = getNumber($('#tot_debit_pv5').val());
  let total_credit = getNumber($('#tot_credit_pv5').val());

  if(total_debit !== total_credit){
    Swal.fire('Error','Total Debit & Credit tidak balance','error');
    return;
  }

  let nag_debit  = getNumber($('#tot_debit_nag_pv5').val());
  let nag_credit = getNumber($('#tot_credit_nag_pv5').val());

  let nak_debit  = getNumber($('#tot_debit_nak_pv5').val());
  let nak_credit = getNumber($('#tot_credit_nak_pv5').val());

  if(nag_debit !== nag_credit){
    Swal.fire('Error','NAG tidak balance','error');
    return;
  }

  if(nak_debit !== nak_credit){
    Swal.fire('Error','NAK tidak balance','error');
    return;
  }

  let detail_pv = [];

  $('#table-pv5 .chk_pv:checked').each(function(){

    let tr = $(this).closest('tr');

    let data = {
      no_pv   : tr.find('.no_pv').data('nopv'),
      type_pv : tr.find('.no_pv').data('typepv'),
      amount  : getNumber(tr.find('.txt_amount_pv').val()),
      pc      : tr.find('.pc_pv').data('pcpv')
    };

    detail_pv.push(data);

  });

  let detail_adjust = [];
  let valid_adjust = true;

  $('#tbody5 tr').each(function(index){

    let tr = $(this);

    let coa   = tr.find('select.no_coa5').first().val();
    let pc    = tr.find('select.prof_ctr5').first().val();
    let cc    = tr.find('select.cost_ctr5').first().val();
    let debit = parseFloat(tr.find('input[name="txt_amount5[]"]').val()) || 0;
    let credit= parseFloat(tr.find('input[name="txt_credit5[]"]').val()) || 0;
    let desc  = tr.find('input[name="keterangan5[]"]').val();
    if(!desc){
      desc = $('#pesan5').val();
    }
    let curr  = tr.find('select.currenc5').first().val();
    let reff_doc  = tr.find('input[name="no_reff5[]"]').val();
    let reff_date  = tr.find('input[name="reff_date5[]"]').val();

    let rowData = {
      row: index + 1, coa, pc, cc, debit, credit, desc, curr, reff_doc, reff_date
    };

    if(!coa || coa === '-'){
      Swal.fire('Warning','COA wajib diisi','warning');
      tr.find('.no_coa5').focus();
      valid_adjust = false;
      return false;
    }

    if(!pc || pc === '-'){
      Swal.fire('Warning','Profit Center wajib diisi','warning');
      tr.find('.prof_ctr5').focus();
      valid_adjust = false;
      return false;
    }

    if(coaWajibCC.includes(coa)){
      if(!cc || cc === '-' || cc === ''){
        Swal.fire('Warning','COA wajib isi Cost Center','warning');
        tr.find('.cost_ctr5').focus();
        valid_adjust = false;
        return false;
      }
    }

    if(debit === 0 && credit === 0){
      Swal.fire('Warning','Debit/Credit harus diisi','warning');
      valid_adjust = false;
      return false;
    }

    detail_adjust.push(rowData);

  });

  if(!valid_adjust) return;

  let finalData = {
    header,
    detail_pv,
    detail_adjust,
    total: {
      global_debit  : total_debit,
      global_credit : total_credit,
      nag_debit, nag_credit, nak_debit, nak_credit
    }
  };

  function doUpdatePv5(){

    // Cegah double-submit: tombol dikunci begitu user konfirmasi & mulai
    // proses save, baru dibuka lagi kalau ada error (supaya bisa dicoba ulang).
    $('#simpan5').prop('disabled', true);

    Swal.fire({
      title: 'Saving...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
      url: 'update_pv_cash.php',
      type: 'POST',
      dataType: 'json',
      data: { data: JSON.stringify(finalData) },
      success: function(res){

        if(res.status === 'ok'){
          Swal.fire({ icon: 'success', title: 'Success', text: res.message }).then(() => {
            location.href='petty-cashout.php';
          });
        }else{
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: res.message,
            showCancelButton: true,
            confirmButtonText: 'Coba Lagi',
            cancelButtonText: 'Tutup'
          }).then((retry) => {
            if(retry.isConfirmed){
              doUpdatePv5();
            }else{
              $('#simpan5').prop('disabled', false);
            }
          });
        }

      },
      error: function(xhr){
        console.log("ERROR AJAX:", xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Terjadi kesalahan server',
          showCancelButton: true,
          confirmButtonText: 'Coba Lagi',
          cancelButtonText: 'Tutup'
        }).then((retry) => {
          if(retry.isConfirmed){
            doUpdatePv5();
          }else{
            $('#simpan5').prop('disabled', false);
          }
        });
      }
    });

  }

  Swal.fire({
    title: 'Are you sure?',
    text: 'The data will be updated.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, save it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {

    if(!result.isConfirmed) return;
    doUpdatePv5();

  });

});

</script>

<?php include '../footer.php'; ?>
