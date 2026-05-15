<form id="form-data2" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-2 mb-3">
                    <label><b>Date</b></label>
                    <input type="text" name="mj_date2" id="mj_date2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="getRate2()">
                    <input type="hidden" class="form-control angka" id="rate_mj2" name="rate_mj2">
                </div>

                <div class="col-md-3 mb-3">
                    <label><b>Type</b></label>
                    <select class="form-control select2" name="mj_type2" id="mj_type2" data-live-search="true">
                        <?php
                        $mj_type = $_POST['mj_type'] ?? '';
                        $sql = mysqli_query($conn1, "select id_cmj,CONCAT(id_cmj,'-',nama_cmj) as type,nama_cmj from master_category_mj where status_hris = 'Y'");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['id_cmj'] == $mj_type) ? 'selected' : '';
                            echo "<option value='" . $row['id_cmj'] . "' " . $selected . ">" . $row['nama_cmj'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label><b>Profit Center</b></label>
                    <select class="form-control select2" name="profit_center2" id="profit_center2" data-live-search="true">
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

                
                <div class="col-md-5 mb-3"></div>

                <div class="col-md-2 mb-3">
                    <label><b>Date Filter</b></label>
                    <input type="text" name="hris_date" id="hris_date" class="form-control tanggal_hris" value="<?php echo date('M Y'); ?>" autocomplete="off">
                </div>

                <div class="col-md-1 mb-3">
                    <label><b>Search from HRIS</b></label><br>
                    <button type="button" id="send" name="send" class="btn btn-primary" onclick="dataTableReload()">
                        <span class="fa fa-search"></span> HRIS
                    </button>
                </div>

                <div class="col-md-2 mb-3">
                    <input type="checkbox" class="checkbox_sb1" id="to2_sb1" name="to2_sb1" value="1">
                    <label for="to2_sb1"><b>Include</b></label><br>
                    <input type="text" class="form-control" style="border: none;border-color: transparent;outline: none;text-align: left;background-color: white;color: red;margin-left: 0;margin-right: 0;font-style: italic;font-weight: bold;" id="txt2_sb1" name="txt2_sb1" value="" disabled>
                    <input type="hidden" id="fil2_sb1" name="fil2_sb1" value="">
                </div>

                <div class="col-md-5 mb-3"></div>

                <div class="col-md-7 mb-3">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30" type="text" class="form-control " name="pesan2" id="pesan2" value="" placeholder="descriptions..." required></textarea>
                </div>

            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="table-hris"
                        class="table table-striped table-bordered table-hover table-sm">
                        <thead class="table-gradient">
                            <tr>
                                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                                <th style="text-align: center;vertical-align: middle;">COA</th>
                                <th style="text-align: center;vertical-align: middle;">Cost Center</th>
                                <th style="text-align: center;vertical-align: middle;">Reff Document</th>
                                <th style="text-align: center;vertical-align: middle;">Reff Date</th>
                                <th style="text-align: center;vertical-align: middle;">Buyer</th>
                                <th style="text-align: center;vertical-align: middle;">Worksheet</th>
                                <th style="text-align: center;vertical-align: middle;">Curr</th>
                                <th style="text-align: center;vertical-align: middle;">Debit</th>
                                <th style="text-align: center;vertical-align: middle;">Credit</th>
                                <th style="text-align: center;vertical-align: middle;">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-1 p-3">

                <!-- NAG -->
                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Total Nirwana Alabare Garment</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
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

                        <div class="row mb-3 align-items-center">
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
                        <h6>Total Nirwana Alabare Knitting</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
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

                        <div class="row mb-3 align-items-center">
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

                        <div class="row mb-3 align-items-center">
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

                        <div class="row mb-3 align-items-center">
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
                <div class="col-md-3 mt-3 mb-3">
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan2" id="simpan2"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='memorial-journal.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                </div>
            </div>
        </div>
    </div>
</form>