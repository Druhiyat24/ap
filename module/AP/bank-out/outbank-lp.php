<form id="form-data1" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num1" id="ref_num1" class="form-control" value="Payment" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active1" id="tgl_active1" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" onchange="getRate1()" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp1" id="nama_supp1" data-live-search="true">
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
                    <input type="text" class="form-control angka" id="profit_center_bank_show1" name="profit_center_bank_show1" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Reff Date</b></label>
                    <input type="text" name="tgl_filawal1" id="tgl_filawal1" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>-</b></label>
                    <input type="text" name="tgl_filakhir1" id="tgl_filakhir1" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" id="btn_tarik_lp" class="btn btn-primary">
                     <i class="fas fa-search"></i> Search
                 </button>
             </div>
             <div class="col-md-2 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account1" name="account1" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, CONCAT(id_pc,' - ',nama_pc) nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['account'] . "' data-bank1='" . $row['bank'] . "' data-currency1='" . $row['curr'] . "' data-namapc1='" . $row['nama_pc'] . "' data-kodepc1='" . $row['kode_pc'] . "'  data-kodebank1='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Bank</b></label>
                    <input type="text" class="form-control" id="bank1" name="bank1" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency1" name="currency1" readonly>
                    <input type="hidden" class="form-control" id="profit_center_bank1" name="profit_center_bank1" readonly>
                    <input type="hidden" class="form-control" id="kode_bank1" name="kode_bank1" readonly>
                </div>
            <div class="col-md-5 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Amount</b></label>
                <input type="text" class="form-control angka" id="amount_bank1" name="amount_bank1">
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Rate</b></label>
                <input type="text" class="form-control angka" id="rate_bank1" name="rate_bank1">
            </div>

            <div class="col-md-2 mb-2">
                <label><b>Equivalent IDR</b></label>
                <input type="text" class="form-control angka" id="eqv_idr_bank1" name="eqv_idr_bank1" readonly>
            </div>
            <div class="col-md-5 mb-2"> </div>

            <div class="col-md-8 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan1" id="pesan1" value="" placeholder="descriptions..." required></textarea>
            </div>

        </div>
        <div class="card-body p-2">
          <div class="table-responsive">
              <table id="table-lp" 
              class="table table-striped table-bordered table-hover table-sm nowrap" >
              <thead class="table-gradient">
                <tr>
                    <th style="text-align: center;vertical-align: middle;">Check</th>
                    <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                    <th style="text-align: center;vertical-align: middle;">No LP</th>
                    <th style="text-align: center;vertical-align: middle;">LP Date</th>
                    <th style="text-align: center;vertical-align: middle;">Due Date</th>
                    <th style="text-align: center;vertical-align: middle;">DPP</th>
                    <th style="text-align: center;vertical-align: middle;">PPN</th>
                    <th style="text-align: center;vertical-align: middle;">PPH</th>
                    <th style="text-align: center;vertical-align: middle;">Total</th>
                    <th style="text-align: center;vertical-align: middle;">Amount</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div class="card-body p-2">
  <div class="table-responsive">
    <table id="table-lp_adjust" 
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
    <tbody id="tbody1"></tbody>

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
        <div class="total-box">
            <h6>Total PT. Nirwana Alabare Garment</h6>
            <hr>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Debit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_debit_nag_lp" name="tot_debit_nag_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_debit_nag_lp" name="h_tot_debit_nag_lp" readonly>
                </div>
            </div>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Credit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_credit_nag_lp" name="tot_credit_nag_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_credit_nag_lp" name="h_tot_credit_nag_lp" readonly>
                </div>
            </div>

        </div>
    </div>


    <!-- NAK -->
    <div class="col-md-4">
        <div class="total-box">
            <h6>Total PT. Nirwana Alabare Knitting</h6>
            <hr>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Debit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_debit_nak_lp" name="tot_debit_nak_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_debit_nak_lp" name="h_tot_debit_nak_lp" readonly>
                </div>
            </div>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Credit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_credit_nak_lp" name="tot_credit_nak_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_credit_nak_lp" name="h_tot_credit_nak_lp" readonly>
                </div>
            </div>

        </div>
    </div>


    <div class="col-md-4">
        <div class="total-box">
            <h6>Grand Total</h6>
            <hr>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Debit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_debit_lp" name="tot_debit_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_debit_lp" name="h_tot_debit_lp" readonly>
                </div>
            </div>

            <div class="row mb-2 align-items-center">
                <div class="col-4">
                    <label class="mb-0">Total Credit</label>
                </div>
                <div class="col-8">
                    <input type="text" style="font-size:14px;text-align:right"
                    class="form-control" id="tot_credit_lp" name="tot_credit_lp" readonly>
                    <input type="hidden" style="font-size:14px;text-align:right"
                    class="form-control" id="h_tot_credit_lp" name="h_tot_credit_lp" readonly>
                </div>
            </div>

        </div>
    </div>

</div>
<div class="form-row">
    <div class="col-md-3 mt-3 mb-2">                              
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan1" id="simpan1"><span class="fa fa-floppy-o"></span> Save</button>                
        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-out.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
    </div>
</div> 
</div>
</div>
</form>