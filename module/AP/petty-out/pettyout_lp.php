<form id="form-data4" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num4" id="ref_num4" class="form-control" value="List Payment" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active4" id="tgl_active4" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" onchange="getRate1()" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp4" id="nama_supp4" data-live-search="true">
                        <option value="">Select Supplier</option>
                        <?php
                        $nama_supp2 = $_POST['nama_supp2'] ?? '';
                        $sql = mysqli_query($conn1, "select * from (select distinct(Supplier) Supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC) a UNION select cc_name as cost_name from b_master_cc where status = 'Active'");
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
                    <input type="text" class="form-control angka" id="profit_center_kas_show4" name="profit_center_kas_show4" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Reff Date</b></label>
                    <input type="text" name="tgl_filawal4" id="tgl_filawal4" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>-</b></label>
                    <input type="text" name="tgl_filakhir4" id="tgl_filakhir4" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" id="btn_tarik_lp" class="btn btn-primary">
                     <i class="fas fa-search"></i> Search
                 </button>
             </div>
             <div class="col-md-2 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account4" name="account4" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['id_coa'] . "' data-kode4='" . $row['kode_cash'] . "' data-pc4='" . $row['profit_center'] . "' data-namapc4='" . $row['nama_pc'] . "'>" . $row['coa'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency4" name="currency4" readonly>
                    <input type="hidden" class="form-control" id="kode_kas4" name="kode_kas4" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas4" name="profit_center_kas4" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas4" name="amount_kas4">
                </div>

            
            <div class="col-md-2 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Cash Flow Category</b></label>
                <select class="form-control select2" name="cash_flow4" id="cash_flow4" data-live-search="true">
                    <option value="">Select Cash Flow Category</option>
                    <?php
                    $sqlCf4 = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                    while ($rowCf4 = mysqli_fetch_assoc($sqlCf4)) {
                        echo "<option value='" . $rowCf4['id'] . "'>" . $rowCf4['show_subcategory'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-5 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30" type="text" class="form-control " name="pesan4" id="pesan4" value="" placeholder="descriptions..." required></textarea>
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
    <tbody id="tbody4"></tbody>

    <tfoot>
        <tr>
            <td colspan="11" align="center">

                <button type="button" class="btn btn-primary"
                onclick="addRow4('tbody4')">
                Add Row
            </button>

            <button type="button" class="btn btn-warning"
            onclick="InsertRow4('tbody4')">
            Insert Row
        </button>

        <button type="button" class="btn btn-danger"
        onclick="deleteRow4('tbody4')">
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
                        <input type="text" class="total-stat-value" id="tot_debit_nag_lp" name="tot_debit_nag_lp" readonly>
                        <input type="hidden" id="h_tot_debit_nag_lp" name="h_tot_debit_nag_lp" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nag_lp" name="tot_credit_nag_lp" readonly>
                        <input type="hidden" id="h_tot_credit_nag_lp" name="h_tot_credit_nag_lp" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_nak_lp" name="tot_debit_nak_lp" readonly>
                        <input type="hidden" id="h_tot_debit_nak_lp" name="h_tot_debit_nak_lp" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_nak_lp" name="tot_credit_nak_lp" readonly>
                        <input type="hidden" id="h_tot_credit_nak_lp" name="h_tot_credit_nak_lp" readonly>
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
                        <input type="text" class="total-stat-value" id="tot_debit_lp" name="tot_debit_lp" readonly>
                        <input type="hidden" id="h_tot_debit_lp" name="h_tot_debit_lp" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" id="tot_credit_lp" name="tot_credit_lp" readonly>
                        <input type="hidden" id="h_tot_credit_lp" name="h_tot_credit_lp" readonly>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="form-row">
    <div class="col-md-3 mt-3 mb-2">                              
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan4" id="simpan4"><span class="fa fa-floppy-o"></span> Save</button>                
        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='petty-cashout.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
    </div>
</div> 
</div>
</div>
</form>