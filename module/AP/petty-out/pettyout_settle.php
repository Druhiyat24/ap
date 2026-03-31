<form id="form-data2" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num2" id="ref_num2" class="form-control" value="Settlement" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active2" id="tgl_active2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_kas_show2" name="profit_center_kas_show2" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas2" name="profit_center_kas2" readonly>
                </div>

                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account2" name="account2" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['id_coa'] . "' data-kode2='" . $row['kode_cash'] . "' data-pc2='" . $row['profit_center'] . "' data-namapc2='" . $row['nama_pc'] . "'>" . $row['coa'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency2" name="currency2" readonly>
                    <input type="hidden" class="form-control" id="kode_kas2" name="kode_kas2" readonly>
                </div>
                <div class="col-md-7 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Reff Document</b></label>
                    <select class="form-control select2" id="reff_number" name="reff_number" data-live-search="true" onchange="getdataadvance(this.value)">
                        <option value="">Select Reff Document</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_pco, coa_akun  from c_petty_cashout_h where status != 'Cancel' and reff = 'Advance' and settlement is null");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['no_pco'] . "'>" . $row['no_pco'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas2" name="amount_kas2">
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan2" id="pesan2" value="" placeholder="descriptions..." required></textarea>
                </div>

            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="mytablesettle"
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
                    <div class="total-box">
                        <h6>Total PT. Nirwana Alabare Garment</h6>
                        <hr>

                        <div class="row mb-2 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_nag2" name="tot_debit_nag2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nag2" name="h_tot_debit_nag2" readonly>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nag2" name="tot_credit_nag2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nag2" name="h_tot_credit_nag2" readonly>
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
                                    class="form-control" id="tot_debit_nak2" name="tot_debit_nak2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nak2" name="h_tot_debit_nak2" readonly>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nak2" name="tot_credit_nak2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nak2" name="h_tot_credit_nak2" readonly>
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
                                    class="form-control" id="tot_debit2" name="tot_debit2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit2" name="h_tot_debit2" readonly>
                            </div>
                        </div>

                        <div class="row mb-2 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit2" name="tot_credit2" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit2" name="h_tot_credit2" readonly>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-2">
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan2" id="simpan2"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='petty-cashout.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                </div>
            </div>
        </div>
    </div>
</form>