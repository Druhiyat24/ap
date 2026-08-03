<form id="form-data5" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num5" id="ref_num5" class="form-control" value="Payment Voucher" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active5" id="tgl_active5" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp5" id="nama_supp5" data-live-search="true">
                        <option value="">Select Supplier</option>
                        <?php
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='S' and supplier != '' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            echo "<option value='" . $row['Supplier'] . "'>" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_kas_show5" name="profit_center_kas_show5" readonly>
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
             <div class="col-md-2 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account5" name="account5" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['id_coa'] . "' data-kode5='" . $row['kode_cash'] . "' data-pc5='" . $row['profit_center'] . "' data-namapc5='" . $row['nama_pc'] . "'>" . $row['coa'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency5" name="currency5" readonly>
                    <input type="hidden" class="form-control" id="kode_kas5" name="kode_kas5" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas5" name="profit_center_kas5" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas5" name="amount_kas5">
                </div>
            <div class="col-md-5 mb-2"> </div>

            <div class="col-md-8 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan5" id="pesan5" value="" placeholder="descriptions..." required></textarea>
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
    <tbody id="tbody5"></tbody>

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
