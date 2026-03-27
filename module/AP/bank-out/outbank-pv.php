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
                    <input type="text" name="tgl_active2" id="tgl_active2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="refreshRate2()">
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
                     <i class="fas fa-search"></i> Search PV
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

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan2" id="pesan2" value="" placeholder="descriptions..." required></textarea>
                </div>

            </div>
    <div class="card-body p-2">
      <div class="table-responsive">
          <table id="table-pv" 
          class="table table-striped table-bordered table-hover table-sm nowrap" >
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">Check</th>
                <th style="text-align: center;vertical-align: middle;">No PV</th>
                <th style="text-align: center;vertical-align: middle;">PV Date</th>
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
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-2">                              
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan2" id="simpan2"><span class="fa fa-floppy-o"></span> Save</button>                
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
                </div>
            </div> 
        </div>
    </div>
</form>