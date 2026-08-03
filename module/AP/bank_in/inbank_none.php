<form id="form-data3" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num3" id="ref_num3" class="form-control" value="None" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active3" id="tgl_active3" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="getRate3()">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Source</b></label>
                    <select class="form-control select2" name="nama_supp3" id="nama_supp3" data-live-search="true">
                        <option value="">Select Source</option>
                        <?php
                        $nama_supp3 = $_POST['nama_supp3'] ?? '';
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='C' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['Supplier'] == $nama_supp3) ? 'selected' : '';
                            echo "<option value='" . $row['Supplier'] . "' " . $selected . ">" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account3" name="account3" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['account'] . "' data-bank3='" . $row['bank'] . "' data-currency3='" . $row['curr'] . "' data-namapc3='" . $row['nama_pc'] . "' data-kodepc3='" . $row['kode_pc'] . "'  data-kodebank3='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Bank</b></label>
                    <input type="text" class="form-control" id="bank3" name="bank3" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency3" name="currency3" readonly>
                    <input type="hidden" class="form-control" id="profit_center_bank3" name="profit_center_bank3" readonly>
                    <input type="hidden" class="form-control" id="kode_bank3" name="kode_bank3" readonly>
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_bank3" name="amount_bank3">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Rate</b></label>
                    <input type="text" class="form-control angka" id="rate_bank3" name="rate_bank3">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Equivalent IDR</b></label>
                    <input type="text" class="form-control angka" id="eqv_idr_bank3" name="eqv_idr_bank3" readonly>
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan3" id="pesan3" value="" placeholder="descriptions..." required></textarea>
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

                        <tbody id="tbody3"></tbody>

                        <tfoot>
                            <tr>
                                <td colspan="11" align="center">

                                    <button type="button" class="btn btn-primary"
                                        onclick="addRow('tbody3')">
                                        Add Row
                                    </button>

                                    <button type="button" class="btn btn-warning"
                                        onclick="InsertRow('tbody3')">
                                        Insert Row
                                    </button>

                                    <button type="button" class="btn btn-danger"
                                        onclick="deleteRow('tbody3')">
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
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan3" id="simpan3"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                </div>
            </div>
        </div>
    </div>
</form>
