<form id="form-data" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-2 mb-3">
                    <label><b>Date</b></label>
                    <input type="text" name="mj_date" id="mj_date" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="getRate()">
                    <input type="hidden" class="form-control angka" id="rate_mj" name="rate_mj">
                </div>

                <div class="col-md-3 mb-3">
                    <label><b>Type</b></label>
                    <select class="form-control select2" name="mj_type" id="mj_type" data-live-search="true">
                        <option value="">Select Source</option>
                        <?php
                        $mj_type = $_POST['mj_type'] ?? '';
                        $sql = mysqli_query($conn1, "select id_cmj,CONCAT(id_cmj,'-',nama_cmj) as type,nama_cmj from master_category_mj");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['id_cmj'] == $mj_type) ? 'selected' : '';
                            echo "<option value='" . $row['id_cmj'] . "' " . $selected . ">" . $row['nama_cmj'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label><b>Profit Center</b></label>
                    <select class="form-control select2" name="profit_center" id="profit_center" data-live-search="true">
                        <?php
                        $profit_center = $_POST['profit_center'] ?? '';
                        $sql = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['kode_pc'] == $profit_center) ? 'selected' : '';
                            echo "<option value='" . $row['kode_pc'] . "' " . $selected . ">" . $row['tampil'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <input type="checkbox" class="checkbox_sb1" id="to_sb1" name="to_sb1" value="1">
                    <label for="to_sb1"><b>Include</b></label><br>
                    <input type="text" class="form-control" style="border: none;border-color: transparent;outline: none;text-align: left;background-color: white;color: red;margin-left: 0;margin-right: 0;font-style: italic;font-weight: bold;" id="txt_sb1" name="txt_sb1" value="" disabled>
                    <input type="hidden" id="fil_sb1" name="fil_sb1" value="">
                </div>

                <div class="col-md-7 mb-3">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>
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
                                <th>Reference</th>
                                <th>Reference Date</th>
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
                    <div class="total-box">
                        <h6>Total PT. Nirwana Alabare Garment</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_nag" name="tot_debit_nag" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nag" name="h_tot_debit_nag" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nag" name="tot_credit_nag" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nag" name="h_tot_credit_nag" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr_nag" name="tot_debit_idr_nag" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr_nag" name="h_tot_debit_idr_nag" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr_nag" name="tot_credit_idr_nag" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr_nag" name="h_tot_credit_idr_nag" readonly>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- NAK -->
                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Total PT. Nirwana Alabare Knitting</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_nak" name="tot_debit_nak" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nak" name="h_tot_debit_nak" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nak" name="tot_credit_nak" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nak" name="h_tot_credit_nak" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr_nak" name="tot_debit_idr_nak" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr_nak" name="h_tot_debit_idr_nak" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr_nak" name="tot_credit_idr_nak" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr_nak" name="h_tot_credit_idr_nak" readonly>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Grand Total</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit" name="tot_debit" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit" name="h_tot_debit" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit" name="tot_credit" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit" name="h_tot_credit" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr" name="tot_debit_idr" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr" name="h_tot_debit_idr" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr" name="tot_credit_idr" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr" name="h_tot_credit_idr" readonly>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-3">
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                </div>
            </div>
        </div>
    </div>
</form>