<form id="form-data" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num" id="ref_num" class="form-control" value="AR Collection" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active" id="tgl_active" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="refreshRate()">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Source</b></label>
                    <select class="form-control select2" name="nama_supp" id="nama_supp" data-live-search="true">
                        <option value="Unrealize">Unrealize</option>
                        <?php
                        $nama_supp = $_POST['nama_supp'] ?? '';
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='C' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['Supplier'] == $nama_supp) ? 'selected' : '';
                            echo "<option value='" . $row['Supplier'] . "' " . $selected . ">" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account" name="account" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['account'] . "' data-bank='" . $row['bank'] . "' data-currency='" . $row['curr'] . "' data-namapc='" . $row['nama_pc'] . "' data-kodepc='" . $row['kode_pc'] . "'  data-kodebank='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Bank</b></label>
                    <input type="text" class="form-control" id="bank" name="bank" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency" name="currency" readonly>
                    <input type="hidden" class="form-control" id="profit_center_bank" name="profit_center_bank" readonly>
                    <input type="hidden" class="form-control" id="kode_bank" name="kode_bank" readonly>
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-3 mb-2">
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
                <div class="col-md-2 mb-2">
                    <label><b>COA</b></label>
                    <select class="form-control select2" id="coa" name="coa" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where nama_coa like '%POS SILANG%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['id_coa'] . "' >" . $row['coa'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Cost Center</b></label>
                    <select class="form-control select2" name="cost" id="cost" data-dropup-auto="false" data-live-search="true">
                        <option value="" disabled selected="true">Select Cost Center</option>
                    </select>
                </div>
                <div class="col-md-5 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_bank" name="amount_bank">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Rate</b></label>
                    <input type="text" class="form-control angka" id="rate_bank" name="rate_bank">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Equivalent IDR</b></label>
                    <input type="text" class="form-control angka" id="eqv_idr_bank" name="eqv_idr_bank" readonly>
                </div>


                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>
                </div>

            </div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-2">                              
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
                </div>
            </div> 
        </div>
    </div>
</form>