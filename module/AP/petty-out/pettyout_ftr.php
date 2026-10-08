<?php
/* ============================================================================
   Tab FTR di menu Petty Cash Out - membayar FTR CBD/DP LANGSUNG dari kas kecil,
   tanpa melewati PV-AP terlebih dulu.

   Hanya FTR berstatus Approved DAN Payment Method = Cash yang bisa ditarik.
   Yang Transfer tetap lewat PV-AP lalu Bank Out seperti biasa.

   Jurnal yang dibuat: Debit UANG MUKA PEMBELIAN (akunnya dipetakan dari Item
   Type FTR + area supplier lewat pv_mapping_jurnal_dp - sumber yang SAMA dgn
   yang dipakai PV-AP CBD/DP), Credit Kas Kecil.

   Dobel-tarik dicegah dari PANGKALNYA, bukan dgn saling mengecualikan: tab ini
   hanya mengambil FTR ber-Payment Method Cash, sedangkan PV-AP CBD/DP hanya
   mengambil yang BUKAN Cash. Kedua himpunan itu tidak pernah beririsan.

   Tampilannya dibangun dari petty-out/pettyout_pv.php - kalau tab Payment
   Voucher diubah, samakan juga di sini.
   ============================================================================ */
?>
<form id="form-data6" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
          <div class="pco-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Header</div>
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num6" id="ref_num6" class="form-control" value="FTR (CBD / DP)" readonly>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active6" id="tgl_active6" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp6" id="nama_supp6" data-live-search="true">
                        <option value="">Select Supplier</option>
                        <?php
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='S' and supplier != '' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            echo "<option value='" . $row['Supplier'] . "'>" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2"> </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_kas_show6" name="profit_center_kas_show6" readonly>
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>Reff Date</b></label>
                    <input type="text" name="tgl_filawal6" id="tgl_filawal6" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2">
                    <label><b>-</b></label>
                    <input type="text" name="tgl_filakhir6" id="tgl_filakhir6" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" id="btn_tarik_ftr6" class="btn btn-primary">
                     <i class="fas fa-search"></i> Search
                 </button>
             </div>
             <div class="col-md-2 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account6" name="account6" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            echo "<option value='" . $row['id_coa'] . "' data-kode6='" . $row['kode_cash'] . "' data-pc6='" . $row['profit_center'] . "' data-namapc6='" . $row['nama_pc'] . "'>" . $row['coa'] . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency6" name="currency6" readonly>
                    <input type="hidden" class="form-control" id="kode_kas6" name="kode_kas6" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas6" name="profit_center_kas6" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas6" name="amount_kas6">
                </div>

            
            <div class="col-md-2 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Cash Flow Category</b></label>
                <select class="form-control select2" name="cash_flow6" id="cash_flow6" data-live-search="true">
                    <option value="">Select Cash Flow Category</option>
                    <?php
                    $sqlCf6 = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                    while ($rowCf6 = mysqli_fetch_assoc($sqlCf6)) {
                        echo "<option value='" . $rowCf6['id'] . "'>" . $rowCf6['show_subcategory'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-5 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30" type="text" class="form-control " name="pesan6" id="pesan6" value="" placeholder="descriptions..." required></textarea>
            </div>

        </div>
       <div class="card-body p-2">
         <div class="pco-sec"><i class="fa fa-exchange" aria-hidden="true"></i> FTR to pay</div>
          <div class="table-responsive">
              <table id="table-ftr6"
              class="table table-striped table-bordered table-hover table-sm nowrap" >
              <thead class="table-gradient">
                <tr>
                    <th style="text-align: center;vertical-align: middle;">Check</th>
                    <th style="text-align: center;vertical-align: middle;">Type</th>
                    <th style="text-align: center;vertical-align: middle;">No FTR</th>
                    <th style="text-align: center;vertical-align: middle;">FTR Date</th>
                    <th style="text-align: center;vertical-align: middle;">Payment Date</th>
                    <th style="text-align: center;vertical-align: middle;">SubTotal</th>
                    <th style="text-align: center;vertical-align: middle;">Tax</th>
                    <th style="text-align: center;vertical-align: middle;">Item Type</th>
                    <th style="text-align: center;vertical-align: middle;">Total</th>
                    <th style="text-align: center;vertical-align: middle;">Rate</th>
                    <th style="text-align: center;vertical-align: middle;">Amount</th>
                    <th style="text-align: center;vertical-align: middle;">Amount IDR Eqv</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
<!-- Ditampilkan hanya saat tabelnya masih kosong - lihat .pco-empty di css/app-pco-form.css. SENGAJA di luar tabel: isi yang bukan baris/sel akan dibungkus sel semu dan terjepit di kolom pertama. -->
<div class="pco-empty">No FTR loaded yet. Choose a supplier and date range above, then press Search.</div>
    </div>
</div>
<div class="card-body p-2">
  <div class="pco-sec"><i class="fa fa-book" aria-hidden="true"></i> Adjustment Value</div>
  <div class="table-responsive">
    <table id="table-ftr6_adjust"
    class="table table-striped table-bordered table-hover table-sm nowrap pco-jtbl" >
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
    <tbody id="tbody6"></tbody>

          <tfoot>
        <tr>
          <td colspan="11" align="center">
          <!-- Hanya tampil saat tbody masih kosong - lihat .pco-empty di
               css/app-pco-form.css. Diletakkan DI DALAM sel ber-colspan ini
               supaya membentang penuh; di luar sel, isi yang bukan baris/sel
               akan dibungkus sel semu dan terjepit di kolom pertama. -->
          <div class="pco-empty">No adjustment rows yet. Press Add Row to start.</div>
          <div class="pco-rowbtn">
<button type="button" class="btn btn-primary"
                onclick="addRow6('tbody6')">
                Add Row
            </button>

            <button type="button" class="btn btn-warning"
            onclick="InsertRow6('tbody6')">
            Insert Row
        </button>

        <button type="button" class="btn btn-danger"
        onclick="deleteRow6('tbody6')">
        Delete Row
    </button>
          </div>
          </td>
        </tr>
      </tfoot>
    </table>
</div>
</div>
<div class="row mt-1 p-3">
  <div class="col-12"><div class="pco-sec"><i class="fa fa-calculator" aria-hidden="true"></i> Totals</div></div>

    <!-- NAG -->
    <div class="col-md-4">
        <div class="total-box tone-nag">
            <div class="total-box-header"><i class="fa fa-building"></i> PT. Nirwana Alabare Garment</div>
            <div class="total-box-body">

                <div class="total-stat is-debit">
                    <span class="total-stat-label">Total Debit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_debit_nag_ftr6" name="tot_debit_nag_ftr6" readonly>
                        <input type="hidden" id="h_tot_debit_nag_ftr6" name="h_tot_debit_nag_ftr6" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_credit_nag_ftr6" name="tot_credit_nag_ftr6" readonly>
                        <input type="hidden" id="h_tot_credit_nag_ftr6" name="h_tot_credit_nag_ftr6" readonly>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- NAK -->
    <div class="col-md-4">
        <div class="total-box tone-nak">
            <div class="total-box-header"><i class="fa fa-industry"></i> PT. Nirwana Alabare Knitting</div>
            <div class="total-box-body">

                <div class="total-stat is-debit">
                    <span class="total-stat-label">Total Debit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_debit_nak_ftr6" name="tot_debit_nak_ftr6" readonly>
                        <input type="hidden" id="h_tot_debit_nak_ftr6" name="h_tot_debit_nak_ftr6" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_credit_nak_ftr6" name="tot_credit_nak_ftr6" readonly>
                        <input type="hidden" id="h_tot_credit_nak_ftr6" name="h_tot_credit_nak_ftr6" readonly>
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
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_debit_ftr6" name="tot_debit_ftr6" readonly>
                        <input type="hidden" id="h_tot_debit_ftr6" name="h_tot_debit_ftr6" readonly>
                    </div>
                </div>

                <div class="total-stat is-credit">
                    <span class="total-stat-label">Total Credit</span>
                    <div class="total-stat-value-wrap">
                        <input type="text" class="total-stat-value" placeholder="0.00" id="tot_credit_ftr6" name="tot_credit_ftr6" readonly>
                        <input type="hidden" id="h_tot_credit_ftr6" name="h_tot_credit_ftr6" readonly>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<div class="form-row">
    <div class="col-md-3 mt-3 mb-2">
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan6" id="simpan6"><span class="fa fa-floppy-o"></span> Save</button>
        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='petty-cashout.php'"><span class="fa fa-angle-double-left"></span> Back</button>
    </div>
</div>
</div>
</div>
</form>
