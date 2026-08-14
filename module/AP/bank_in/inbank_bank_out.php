<form id="form-data2" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num2" id="ref_num2" class="form-control" value="Bank Keluar" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active2" id="tgl_active2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="refreshRate2()">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Source</b></label>
                    <select class="form-control select2" name="nama_supp2" id="nama_supp2" data-live-search="true">
                        <?php
                        $nama_supp2 = $_POST['nama_supp2'] ?? '';
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='C' and id_supplier = '799' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['Supplier'] == $nama_supp2) ? 'selected' : '';
                            echo "<option value='" . $row['Supplier'] . "' " . $selected . ">" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account2" name="account2" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['account'] . "' data-bank2='" . $row['bank'] . "' data-currency2='" . $row['curr'] . "' data-namapc2='" . $row['nama_pc'] . "' data-kodepc2='" . $row['kode_pc'] . "'  data-kodebank2='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                        }
                        ?>

                    </select>
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

                <div class="col-md-5 mb-2"></div>

                <div class="col-md-3 mb-2">
                    <label><b>Cash Flow Category</b></label>
                    <input type="text" class="form-control" value="PEMINDAHBUKUAN INTERNAL" readonly>
                    <input type="hidden" name="cash_flow2" id="cash_flow2" value="6">
                </div>

                <div class="col-md-4 mb-2">
                    <label><b>Bank Out</b></label>
                    <select class="form-control select2" name="no_bk" id="no_bk" data-live-search="true">
                        <option value="">Select Bank Out</option>
                        <?php
                        $no_bk = $_POST['no_bk'] ?? '';
                        $sql = mysqli_query($conn1, "select no_bankout from b_bankout_h where status = 'Approved' and stat_bi != 'Y' and nama_supp = 'PT. NIRWANA ALABARE GARMENT'");
                        while ($row = mysqli_fetch_array($sql)) {
                            $selected = ($row['no_bankout'] == $no_bk) ? 'selected' : '';
                            echo "<option value='" . $row['no_bankout'] . "' " . $selected . ">" . $row['no_bankout'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-5 mb-2"></div>

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="2" type="text" class="form-control " name="pesan2" id="pesan2" value="" placeholder="descriptions..." required></textarea>
                </div>

            </div>
    <div class="card-body p-2">
      <div class="table-responsive">
          <table id="table-bank-out" 
          class="table table-striped table-bordered table-hover table-sm nowrap" >
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">COA</th>
                <th style="text-align: center;vertical-align: middle;">Cost Center</th>
                <th style="text-align: center;vertical-align: middle;">Reff Document</th>
                <th style="text-align: center;vertical-align: middle;">Reff Date</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Total</th>
                <th style="text-align: center;vertical-align: middle;">Rate</th>
                <th style="text-align: center;vertical-align: middle;">Total IDR</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-2">                              
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan2" id="simpan2"><span class="fa fa-floppy-o"></span> Save</button>                
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
                </div>
            </div> 
        </div>
    </div>
</form>