<form id="form-data2" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num2" id="ref_num2" class="form-control" value="Payment Voucher" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active2" id="tgl_active2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp2" id="nama_supp2" data-live-search="true">
                        <option value="">Select Supplier</option>
                        <?php
                        $nama_supp2 = $_POST['nama_supp2'] ?? '';
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='S' and supplier != '' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['Supplier'] == $nama_supp2) ? 'selected' : '';
                            echo "<option value='" . $row['Supplier'] . "' " . $selected . ">" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_bank_show" name="profit_center_bank_show" readonly>
                    <input type="hidden" class="form-control" id="profit_center_bank2" name="profit_center_bank2" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Reff Date</b></label>
                    <input type="text" name="tgl_filawal" id="tgl_filawal" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>-</b></label>
                    <input type="text" name="tgl_filakhir" id="tgl_filakhir" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" id="btn_tarik_pv" class="btn btn-primary">
                     <i class="fas fa-search"></i> Search
                 </button>
             </div>
             <div class="col-md-2 mb-2"> </div>

             <div class="col-md-3 mb-2">
                <label><b>Account</b></label>
                <input type="text" class="form-control" id="account2" name="account2" readonly>
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Bank</b></label>
                <input type="text" class="form-control" id="bank2" name="bank2" readonly>
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Currency</b></label>
                <input type="text" class="form-control" id="currency2" name="currency2" readonly>
                <input type="hidden" class="form-control" id="profit_center_bank2" name="profit_center_bank2" readonly>
                <input type="hidden" class="form-control" id="kode_bank2" name="kode_bank2" readonly>
            </div>
            <div class="col-md-5 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Amount</b></label>
                <input type="text" class="form-control angka" id="amount_bank2" name="amount_bank2">
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Rate</b></label>
                <input type="text" class="form-control angka" id="rate_bank2" name="rate_bank2">
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Equivalent IDR</b></label>
                <input type="text" class="form-control angka" id="eqv_idr_bank2" name="eqv_idr_bank2" readonly>
            </div>
            <div class="col-md-5 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Cash Flow Category</b></label>
                <select class="form-control select2" name="cash_flow2" id="cash_flow2" data-live-search="true">
                    <option value="">Select Cash Flow Category</option>
                    <?php
                    $sqlCf2 = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                    while ($rowCf2 = mysqli_fetch_assoc($sqlCf2)) {
                        echo "<option value='" . $rowCf2['id'] . "'>" . $rowCf2['show_subcategory'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-5 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30"  type="text" class="form-control " name="pesan2" id="pesan2" value="" placeholder="descriptions..." required></textarea>
            </div>

        </div>
        <div class="card-body p-2">
          <div class="table-responsive">
              <table id="table-pv" 
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
    <table id="table-pv_adjust" 
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
    <tbody id="tbody2"></tbody>

    <tfoot>
        <tr>
            <td colspan="11" align="center">

                <button type="button" class="btn btn-primary"
                onclick="addRow2('tbody2')">
                Add Row
            </button>

            <button type="button" class="btn btn-warning"
            onclick="InsertRow2('tbody2')">
            Insert Row
        </button>

        <button type="button" class="btn btn-danger"
        onclick="deleteRow2('tbody2')">
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
                        <input type="text" class="total-stat-value" id="tot_debit_nag_pv" name="tot_debit_nag_pv" readonly>
                        <input type="hidden" id="h_tot_debit_nag_pv" name="h_tot_debit_nag_pv" readonly>
                    </div>
                </div>
                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nag_pv" name="tot_credit_nag_pv" readonly>
                        <input type="hidden" id="h_tot_credit_nag_pv" name="h_tot_credit_nag_pv" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_nak_pv" name="tot_debit_nak_pv" readonly>
                        <input type="hidden" id="h_tot_debit_nak_pv" name="h_tot_debit_nak_pv" readonly>
                    </div>
                </div>
                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nak_pv" name="tot_credit_nak_pv" readonly>
                        <input type="hidden" id="h_tot_credit_nak_pv" name="h_tot_credit_nak_pv" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_pv" name="tot_debit_pv" readonly>
                        <input type="hidden" id="h_tot_debit_pv" name="h_tot_debit_pv" readonly>
                    </div>
                </div>
                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_pv" name="tot_credit_pv" readonly>
                        <input type="hidden" id="h_tot_credit_pv" name="h_tot_credit_pv" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="form-row">
    <div class="col-md-3 mt-3 mb-2">                              
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan2" id="simpan2"><span class="fa fa-floppy-o"></span> Save</button>                
        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-out.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
    </div>
</div> 
</div>
</div>
</form>