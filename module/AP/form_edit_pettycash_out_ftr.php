<?php
include '../header.php';
include 'pv_data_functions.php';
?>

<!-- Berkas CSS ditaut dgn penanda versi dari filemtime, jadi perubahan di
     dalamnya langsung sampai ke user dan tidak tertahan cache.

     Kosakata .pco- dipakai BERSAMA oleh halaman create dan kelima halaman
     edit Petty Cash Out. Sebelumnya aturannya disalin di blok style
     masing-masing halaman - enam salinan yang sudah mulai melenceng satu
     dari yang lain. Ditaut hanya oleh halaman-halaman itu, bukan dari
     header.php, supaya menu lain tidak ikut berubah tanpa diminta. -->
<link rel="stylesheet" href="../css/app-pco-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-pco-form.css'); ?>">

<?php
$doc_num = base64_decode($_GET['doc_num']);
$doc_num_esc = mysqli_real_escape_string($conn2, $doc_num);

$sqlH = mysqli_query($conn2, "select a.*, concat(a.coa_akun,' ',b.nama_coa) nama_akun from c_petty_cashout_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun where a.no_pco = '$doc_num_esc' and a.reff = 'FTR (CBD / DP)'");
$rowH = mysqli_fetch_assoc($sqlH);

if (!$rowH) {
    echo '<div class="container-fluid mt-3 p-3"><div class="alert alert-danger">Data tidak ditemukan.</div></div>';
    include '../footer.php';
    exit;
}

if ($rowH['status'] !== 'Draft') {
    echo '<div class="container-fluid mt-3 p-3"><div class="alert alert-danger">Data sudah bukan Draft, tidak bisa diedit.</div></div>';
    include '../footer.php';
    exit;
}

// Profit Center akun kas terpilih (untuk isi ulang label PC saat page load)
$sqlPcAkun = mysqli_query($conn2, "select IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc, kode_cash from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $rowH['coa_akun']) . "'");
$rowPcAkun = mysqli_fetch_assoc($sqlPcAkun);

// Rate fallback utk DP/CBD (tidak punya kolom rate sendiri) - sama seperti
// petty-out/get_ftr_ajax.php.
function getRateByCurrDate($conn2, $curr, $tgl)
{
    if (empty($curr) || empty($tgl)) {
        return 1;
    }
    $sql = mysqli_query($conn2, "select rate from ap_masterrate where v_codecurr = 'PAJAK' and curr = '" . mysqli_real_escape_string($conn2, $curr) . "' and tanggal = '" . mysqli_real_escape_string($conn2, $tgl) . "' limit 1");
    $row = mysqli_fetch_assoc($sql);
    return !empty($row['rate']) ? (float) $row['rate'] : 1;
}
?>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="pco-card">
    <!-- Kepala kartu: kotak ikon + judul + jejak menu, mengikuti List
         Memorial Journal. Pita gradien selebar kartu diganti ini supaya
         warnanya jadi aksen, bukan latar - judulnya yang paling
         menonjol. -->
    <div class="pco-head">
      <span class="pco-head-icon"><i class="fas fa-edit"></i></span>
      <div>
        <h1>Edit Petty Cash Out</h1>
        <span class="pco-crumb">AP &rsaquo; Petty Cash Out &rsaquo; Edit</span>
      </div>
    </div><!-- /.pco-head -->

<form id="form-data6" method="post">
    <input type="hidden" id="doc_num6" value="<?= htmlspecialchars($doc_num); ?>">
    <div class="card shadow-sm">
        <div class="card-body">
          <div class="pco-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Header</div>
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Doc Number</b></label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($doc_num); ?>">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_active6" id="tgl_active6" class="form-control tanggal" value="<?= date("d-m-Y", strtotime($rowH['tgl_pco'])); ?>" autocomplete="off" >
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Supplier</b></label>
                    <select class="form-control select2" name="nama_supp6" id="nama_supp6" data-live-search="true">
                        <option value="<?= htmlspecialchars($rowH['nama_supp']); ?>" selected><?= htmlspecialchars($rowH['nama_supp']); ?></option>
                        <?php
                        $sql = mysqli_query($conn1, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup='S' and supplier != '' and supplier != '" . mysqli_real_escape_string($conn1, $rowH['nama_supp']) . "' ORDER BY Supplier ASC");
                        while ($row = mysqli_fetch_array($sql)) {
                            echo "<option value='" . $row['Supplier'] . "'>" . $row['Supplier'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" name="ref_num6" id="ref_num6" class="form-control" value="FTR (CBD / DP)" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Profit Center</b></label>
                    <input type="text" class="form-control angka" id="profit_center_kas_show6" name="profit_center_kas_show6" value="<?= htmlspecialchars($rowPcAkun['nama_pc'] ?? ''); ?>" readonly>
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
             <div class="col-md-3 mb-2"> </div>

             <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="account6" name="account6" data-live-search="true">
                        <option value="">Select Account</option>
                        <?php
                        $sql = mysqli_query($conn1, "select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa, kode_cash, IF(no_coa = '1.01.11','NAK','NAG') profit_center, IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            $selected = ($row['id_coa'] == $rowH['coa_akun']) ? 'selected' : '';
                            echo "<option value='" . $row['id_coa'] . "' data-kode6='" . $row['kode_cash'] . "' data-pc6='" . $row['profit_center'] . "' data-namapc6='" . $row['nama_pc'] . "' $selected>" . $row['coa'] . " </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Currency</b></label>
                    <input type="text" class="form-control" id="currency6" name="currency6" value="<?= htmlspecialchars($rowH['curr']); ?>" readonly>
                    <input type="hidden" class="form-control" id="kode_kas6" name="kode_kas6" value="<?= htmlspecialchars($rowPcAkun['kode_cash'] ?? ''); ?>" readonly>
                    <input type="hidden" class="form-control" id="profit_center_kas6" name="profit_center_kas6" value="<?= htmlspecialchars($rowPcAkun['profit_center'] ?? ''); ?>" readonly>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount_kas6" name="amount_kas6" value="<?= (float) $rowH['amount']; ?>">
                </div>

            <div class="col-md-2 mb-2"> </div>

            <div class="col-md-3 mb-2">
                <label><b>Cash Flow Category</b></label>
                <select class="form-control select2" name="cash_flow6" id="cash_flow6" data-live-search="true">
                    <option value="">Select Cash Flow Category</option>
                    <?php
                    $id_cash_flow6 = $rowH['id_cash_flow'] ?? '';
                    $sqlCf6 = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash Out' and status = 'Y' order by nama_category asc, urutan asc");
                    while ($rowCf6 = mysqli_fetch_assoc($sqlCf6)) {
                        $selectedCf5 = ($rowCf6['id'] == $id_cash_flow6) ? ' selected="selected"' : '';
                        echo '<option value="'.$rowCf6['id'].'"'.$selectedCf5.'>'.$rowCf6['show_subcategory'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-7 mb-2">
                <label><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;height: 40px;" cols="30" type="text" class="form-control " name="pesan6" id="pesan6" placeholder="descriptions..." required><?= htmlspecialchars($rowH['deskripsi']); ?></textarea>
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
            <!-- Baris yang sudah tertaut ditarik JavaScript lewat
                 get_ftr_ajax.php dgn linked_only=1 - bentuknya jadi SAMA
                 PERSIS dgn hasil tombol Search, bukan salinan terpisah. -->
            <tbody>
            </tbody>
        </table>
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
    <tbody id="tbody6">
        <?php
        $sqlAdj = mysqli_query($conn2, "select a.id_coa, concat(b.no_coa,' ',b.nama_coa) nama_coa, a.profit_center, a.no_cc, concat(c.no_cc,' - ',c.cc_name) nama_cc, a.reff_doc, a.reff_date, a.t_debit, a.t_credit, a.deskripsi from c_petty_cashout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa left join b_master_cc c on c.no_cc = a.no_cc where a.no_pco = '$doc_num_esc'");
        while ($rowAdj = mysqli_fetch_assoc($sqlAdj)) {
            ?>
            <tr>
            <td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

            <td>
            <select class="form-control selectpicker no_coa6" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
                <option value="<?= htmlspecialchars($rowAdj['id_coa']); ?>" selected><?= htmlspecialchars($rowAdj['nama_coa']); ?></option>
                <option value="-">-</option>
                <?php
                $sqlCoa = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2 where no_coa != '" . mysqli_real_escape_string($conn1, $rowAdj['id_coa']) . "'");
                foreach ($sqlCoa as $coa) : ?>
                <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                <?php endforeach; ?>
            </select>
            </td>

            <td>
            <select class="form-control selectpicker prof_ctr6" name="prof_ctr6[]" data-live-search="true" data-width="100%">
                <?php
                $sqlPc = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc,CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status='Active'");
                while ($fc = mysqli_fetch_assoc($sqlPc)) {
                    $sel = ($fc['kode_pc'] == $rowAdj['profit_center']) ? 'selected' : '';
                    echo '<option value="' . $fc['kode_pc'] . '" ' . $sel . '>' . $fc['tampil'] . '</option>';
                }
                ?>
            </select>
            </td>

            <td>
            <select class="form-control selectpicker cost_ctr6" name="cost_ctr6[]" data-live-search="true" data-width="100%">
                <option value="<?= htmlspecialchars($rowAdj['no_cc']); ?>" selected><?= htmlspecialchars($rowAdj['nama_cc']); ?></option>
                <?php
                $sqlCc = mysqli_query($conn1, "select no_cc as code_combine, concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '" . mysqli_real_escape_string($conn1, $rowAdj['no_cc']) . "'");
                foreach ($sqlCc as $ccs) : ?>
                <option value="<?= $ccs["code_combine"]; ?>"><?= $ccs["cost_name"]; ?></option>
                <?php endforeach; ?>
            </select>
            </td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff6[]" value="<?= htmlspecialchars($rowAdj['reff_doc']); ?>" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date6[]" value="<?= !empty($rowAdj['reff_date']) && $rowAdj['reff_date'] != '1970-01-01' ? date('d-m-Y', strtotime($rowAdj['reff_date'])) : ''; ?>" autocomplete="off"></td>

            <td>
            <select class="form-control selectpicker currenc6" name="currenc6[]">
                <option value="IDR" selected>IDR</option>
            </select>
            </td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount6[]" value="<?= $rowAdj['t_debit'] > 0 ? $rowAdj['t_debit'] : ''; ?>" <?= $rowAdj['t_debit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_amt5(this)" autocomplete="off"></td>

            <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit6[]" value="<?= $rowAdj['t_credit'] > 0 ? $rowAdj['t_credit'] : ''; ?>" <?= $rowAdj['t_credit'] == 0 ? 'readonly' : ''; ?> oninput="modal_input_cre5(this)" autocomplete="off"></td>

            <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan6[]" value="<?= htmlspecialchars($rowAdj['deskripsi']); ?>" autocomplete="off"></td>

            <td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>
            </tr>
            <?php
        }
        ?>
    </tbody>

          <tfoot>
        <tr>
          <td colspan="11" align="center">
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
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <!-- bootstrap-select TIDAK dimuat lagi: dropdown baris kini memakai
       select2, lewat shim $.fn.selectpicker di bawah. -->
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script>
/* ----------------------------------------------------------------------------
   $.fn.selectpicker DIDEFINISIKAN ULANG DI ATAS select2.

   Halaman ini tidak lagi memuat bootstrap-select. Dropdown di dalam baris
   (COA / Profit Center / Cost Center / Currency) dulu memakainya, sementara
   isian kepala memakai select2 - dua bentuk berbeda di satu halaman.

   Seluruh pemanggilan lama (.selectpicker(), 'refresh', 'destroy') dibiarkan
   apa adanya dan dialihkan ke select2 di sini, supaya tidak ada satu pun titik
   pemanggilan yang perlu disunting - titik-titik itu yang memberi makan proses
   simpan.

   'refresh' sengaja destroy + init ulang: di bootstrap-select 'refresh' membaca
   ulang daftar opsi, dan di select2 satu-satunya cara setara adalah membangun
   ulang. Itu dipakai cascade Cost Center setelah opsinya diganti.

   Elemen <select> aslinya tidak diubah select2 (hanya disembunyikan), jadi
   name="...[]", .val(), dan .serialize() tetap sama persis.
---------------------------------------------------------------------------- */
(function ($) {
  function opsiSelect2($el) {
    var o = { width: '100%' };
    /* Daftar pendek (mis. Currency) tidak perlu kotak cari. */
    if ($el.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
    /* Panel select2 menempel di <body>, jadi TIDAK terpotong .table-responsive
       - masalah yang dulu harus ditambal untuk bootstrap-select. */
    return o;
  }
  $.fn.selectpicker = function (perintah) {
    return this.each(function () {
      var $s = $(this);
      var hidup = $s.hasClass('select2-hidden-accessible');
      if (perintah === 'destroy') { if (hidup) { $s.select2('destroy'); } return; }
      if (perintah === 'refresh') {
        if (hidup) { $s.select2('destroy'); }
        $s.select2(opsiSelect2($s));
        return;
      }
      if (!hidup) { $s.select2(opsiSelect2($s)); }
    });
  };
})(jQuery);
</script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });
        $('.select2').select2({ width: '100%' });
        $('.selectpicker').selectpicker();
      /* Menu dropdown selectpicker terpotong oleh pembungkus scroll
         (.table-responsive). Bootstrap 4 hanya menyetel overflow-x: auto,
         tapi menurut spesifikasi CSS kalau satu sumbu bukan 'visible' maka
         sumbu lainnya ikut diperlakukan 'auto' - itulah yang memotongnya
         secara tegak.

         SENGAJA TIDAK memakai opsi container:'body' milik bootstrap-select:
         opsi itu memicu galat internal plugin "Cannot read properties of
         undefined (reading 'length')" saat menu dibuka. Catatan yang sama
         ada di create_memorial_journal.php, tempat cara ini dipakai lebih
         dulu.

         Gantinya: overflow pembungkus dilepas HANYA selagi menu terbuka.
         Didelegasikan ke document supaya baris baru dari addRow() /
         InsertRow() ikut tertangani tanpa diikat ulang. */
      $(document).on('show.bs.dropdown', '.table-responsive', function () {
          $(this).css('overflow', 'visible');
      });
      $(document).on('hide.bs.dropdown', '.table-responsive', function () {
          $(this).css({ 'overflow-x': 'auto', 'overflow-y': '' });
      });

    });

    function getNumber(val) {
        return parseFloat(String(val).replace(/,/g, '')) || 0;
    }

    let coaWajibCC = [];
    $.getJSON('get_coa_wajib_cc.php', function(data){
        coaWajibCC = data;
    });

    // Hitung ulang total awal saat page load (baris PV & adjust sudah terisi dari server)
    $(document).ready(function(){
        hitungTotalFTR6();
    });
</script>

<script type="text/javascript">

let isManualAmountFTR6 = true; // amount header sudah terisi dari data lama, jangan langsung ditimpa
let tableFTR6;

function initTableFTR6(){
  if ($.fn.DataTable.isDataTable('#table-ftr6')) {
    $('#table-ftr6').DataTable().destroy();
  }
  tableFTR6 = $('#table-ftr6').DataTable({
    paging: true,
    searching: true,
    ordering: false,
    info: false,
    autoWidth: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50],
    language: {
      search: "Cari:",
      lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      paginate: { previous: "Prev", next: "Next" }
    }
  });
}

$('#account6').on('change', function() {
  let kode5 = $(this).find(':selected').data('kode6');
  let pc5 = $(this).find(':selected').data('pc6');
  let namapc5 = $(this).find(':selected').data('namapc6');

  $('#profit_center_kas_show6').val(namapc5);
  $('#profit_center_kas6').val(pc5);
  $('#currency6').val('IDR');
  $('#kode_kas6').val(kode5);
  hitungTotalFTR6();
});

/* Nominal di ISIAN ditulis polos tanpa pemisah ribuan - "100985", bukan
   "100,985". Pemisah ribuan membuat kursor melompat tiap kali disunting. */
function angkaPolosFTR6(n){
  let v = Math.round((parseFloat(n) || 0) * 100) / 100;
  return v.toString();
}

/* MUAT AWAL: baris yang sudah tertaut ke dokumen ini ditarik lewat endpoint
   yang SAMA dgn tombol Search (linked_only=1), jadi bentuk barisnya dijamin
   identik - tidak ada dua potongan kode yang harus dijaga agar seragam. */
$(function(){
  $.ajax({
    url: 'petty-out/get_ftr_ajax.php',
    type: 'POST',
    data: { linked_only: 1, exclude_doc_num: $('#doc_num6').val() },
    success: function(res){
      if ($.fn.DataTable.isDataTable('#table-ftr6')) {
        $('#table-ftr6').DataTable().clear().destroy();
      }
      $('#table-ftr6 tbody').html(res);
      initTableFTR6();
      hitungTotalFTR6();
    }
  });
});

$('#btn_tarik_ftr6').on('click', function(){

  let tgl_awal  = $('#tgl_filawal6').val();
  let tgl_akhir = $('#tgl_filakhir6').val();
  let supplier  = $('#nama_supp6').val();

  if(tgl_awal == '' || tgl_akhir == ''){
    Swal.fire('Warning','Tanggal harus diisi','warning');
    return;
  }

  if(supplier == ''){
    Swal.fire('Warning','Supplier harus dipilih','warning');
    return;
  }

  $.ajax({
    url: 'petty-out/get_ftr_ajax.php',
    type: 'POST',
    data: {
      tgl_awal: tgl_awal,
      tgl_akhir: tgl_akhir,
      supplier: supplier,
      fund_type: 'CASH',
      // Alokasi dokumen INI sendiri tidak dihitung sbg "sudah dibayar",
      // supaya FTR yang sudah dibayar penuh olehnya tetap muncul dan
      // nominalnya masih bisa diturunkan.
      exclude_doc_num: $('#doc_num6').val()
    },
    beforeSend:function(){
      Swal.fire({ title:'Loading...', allowOutsideClick:false, didOpen:()=>{ Swal.showLoading(); } });
    },
    success:function(res){

      // no_ftr yang sudah ada di tabel (baik yang dari data lama maupun hasil
      // pencarian sebelumnya) tidak boleh ditampilkan dobel
      let existing = [];
      $('#table-ftr6 .no_ftr').each(function(){
        existing.push($(this).data('noftr'));
      });

      let $tmp = $('<tbody>' + res + '</tbody>');
      $tmp.find('tr').each(function(){
        let nopv = $(this).find('.no_ftr').data('noftr');
        if(existing.includes(nopv)){
          $(this).remove();
        }
      });

      if ($.fn.DataTable.isDataTable('#table-ftr6')) {
        $('#table-ftr6').DataTable().destroy();
      }
      $('#table-ftr6 tbody').append($tmp.html());
      Swal.close();
      initTableFTR6();
    }
  });

});

$('#table-ftr6').on('change', '.chk_ftr', function(){

  let tr = $(this).closest('tr');

  let total = parseFloat(tr.find('.total_ftr').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_ftr').data('rateftr')) || 0;
  let input = tr.find('.txt_amount_ftr');
  let input_idr = tr.find('.txt_amount_ftr_idr');
  let total_idr = total * rate;

  if($(this).is(':checked')){

    input.prop('disabled', false);
    if(!input.val()){
      input.val(angkaPolosFTR6(total));
    }
    input_idr.prop('disabled', false);
    if(!input_idr.val()){
      input_idr.val(angkaPolosFTR6(total_idr));
    }

    if(!$('#account6').val()){
      let pvAccount = tr.find('.no_ftr').data('account');
      if(pvAccount && $('#account6 option[value="' + pvAccount + '"]').length){
        $('#account6').val(pvAccount).trigger('change');
      }
    }

    hitungTotalFTR6();

  }else{

    input.prop('disabled', true);
    input.val('');

    input_idr.prop('disabled', true);
    input_idr.val('');

    if($('#table-ftr6 .chk_ftr:checked').length === 0){
      $('#amount_kas6').val('');
      isManualAmountFTR6 = false;
      resetTotalFTR6();
    }else{
      hitungTotalFTR6();
    }

  }

});

$('#table-ftr6').on('keyup', '.txt_amount_ftr', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_ftr').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_ftr').data('rateftr')) || 0;

  let val = $(this).val().replace(/,/g,'');
  val = parseFloat(val) || 0;

  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
  }

  $(this).val(angkaPolosFTR6(val));

  let val_idr = val * rate;

  tr.find('.txt_amount_ftr_idr').val(angkaPolosFTR6(val_idr));

  hitungTotalFTR6();

});

$('#table-ftr6').on('keyup', '.txt_amount_ftr_idr', function(){

  let tr = $(this).closest('tr');

  let max  = parseFloat(tr.find('.total_ftr').data('total')) || 0;
  let rate = parseFloat(tr.find('.rate_ftr').data('rateftr')) || 0;

  let val_idr = $(this).val().replace(/,/g,'');
  val_idr = parseFloat(val_idr) || 0;

  let val = rate > 0 ? val_idr / rate : 0;

  if(val > max){
    Swal.fire('Warning','Amount tidak boleh lebih dari Total','warning');
    val = max;
    val_idr = val * rate;
  }

  tr.find('.txt_amount_ftr').val(angkaPolosFTR6(val));
  $(this).val(angkaPolosFTR6(val_idr));

  hitungTotalFTR6();

});

$('#amount_kas6').on('keyup change', function(){
  isManualAmountFTR6 = true;
  hitungTotalFTR6();
});

$(document).on('change', '.prof_ctr6', function() {
  const selectedProfCtr = $(this).val();
  const row = $(this).closest('tr');
  const selectedCoa = row.find('select.no_coa6').val() || '-';
  updateCostCenter5(selectedProfCtr, selectedCoa, row);
});

$(document).on('change', '.no_coa6', function() {
  const selectedCoa = $(this).val();
  const row = $(this).closest('tr');
  const selectedProfCtr = row.find('select.prof_ctr6').val() || '-';
  updateCostCenter5(selectedProfCtr, selectedCoa, row);
});

function updateCostCenter5(profCtr, noCoa, row) {
  const costCtrDropdown = $(row).find('.cost_ctr6');

  costCtrDropdown.selectpicker('destroy');
  costCtrDropdown.empty();
  costCtrDropdown.append('<option value="-"> - </option>');
  costCtrDropdown.selectpicker();

  if (profCtr && profCtr !== '-') {
    $.ajax({
      url: 'getCostCenter.php',
      type: 'POST',
      data: { prof_ctr: profCtr, no_coa: noCoa },
      dataType: 'json',
      success: function(response) {
        if (response && response.length > 0) {
          $.each(response, function(index, costCtr) {
            costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
          });
          costCtrDropdown.selectpicker('refresh');
        } else {
          costCtrDropdown.selectpicker('refresh');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
      }
    });
  } else {
    costCtrDropdown.selectpicker('refresh');
  }
}

function addRow6(tableID) {

  var table = document.getElementById(tableID);
  var rowCount = table.rows.length;
  var row = table.insertRow(rowCount);

  var element = `
<tr>
<td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa6" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr6" name="prof_ctr6[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr6" name="cost_ctr6[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff6[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date6[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker currenc6" name="currenc6[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount6[]" oninput="modal_input_amt5(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit6[]" oninput="modal_input_cre5(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan6[]" autocomplete="off"></td>

<td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>

</tr>
`;

  row.innerHTML = element;

  $('.selectpicker').selectpicker('refresh');
  $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });

  var headerPC = $('#profit_center_kas6').val();
  if (headerPC) {
    $(row).find('.prof_ctr6').val(headerPC);
    $(row).find('.prof_ctr6').selectpicker('refresh');
  }

}

function deleteRow6(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var deleted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a5[]"]');

      if (chkbox && chkbox.checked) {
        table.deleteRow(i);
        deleted = true;
        rowCount--;
      }

    }

    if (!deleted) {
      Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin dihapus' });
    }

    $('.selectpicker').selectpicker('refresh');
    hitungTotalFTR6();

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function InsertRow6(tableID) {

  try {

    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var inserted = false;

    for (var i = rowCount - 1; i >= 0; i--) {

      var row = table.rows[i];
      var chkbox = row.querySelector('input[name="chk_a5[]"]');

      if (chkbox && chkbox.checked) {

        var element2 = `
<tr>
<td><input type="checkbox" id="select5" name="select5[]" value="" checked disabled></td>

<td >
<select class="form-control selectpicker no_coa6" name="nomor_coa5[]" data-live-search="true" data-width="100%" data-size="5">
<option value="-">-</option>
<?php
$sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ',nama_coa) as coa from mastercoa_v2");
foreach ($sql as $coa) : ?>
<option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker prof_ctr6" name="prof_ctr6[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc,id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
<option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>

<td>
<select class="form-control selectpicker cost_ctr6" name="cost_ctr6[]" data-live-search="true" data-width="100%">
<option value="-"> - </option>
</select>
</td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_reff6[]" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control tanggal" name="reff_date6[]" autocomplete="off"></td>

<td>
<select class="form-control selectpicker currenc6" name="currenc6[]">
<option value="IDR">IDR</option>
</select>
</td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount6[]" oninput="modal_input_amt5(this)" autocomplete="off"></td>

<td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit6[]" oninput="modal_input_cre5(this)" autocomplete="off"></td>

<td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan6[]" autocomplete="off"></td>

<td><input name="chk_a5[]" type="checkbox" class="checkall_a5"></td>

</tr>
`;

        var newRow = table.insertRow(i + 1);
        newRow.innerHTML = element2;
        inserted = true;

        var headerPC = $('#profit_center_kas6').val();
        if (headerPC) {
          $(newRow).find('.prof_ctr6').val(headerPC);
          $(newRow).find('.prof_ctr6').selectpicker('refresh');
        }

      }

    }

    if (!inserted) {
      Swal.fire({ icon: 'warning', title: 'Warning', text: 'Silahkan ceklis baris yang ingin disisipkan' });
    }

    $('.selectpicker').selectpicker('refresh');
    $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });

  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message });
  }

}

function modal_input_amt5(el){

  let row = $(el).closest('tr');
  let debit  = parseFloat($(el).val()) || 0;
  let creditInput = row.find('input[name="txt_credit6[]"]');

  if(debit > 0){
    creditInput.val(0);
    creditInput.prop('readonly',true);
  }else{
    creditInput.prop('readonly',false);
  }

  hitungTotalFTR6();

}

function modal_input_cre5(el){

  let row = $(el).closest('tr');
  let credit = parseFloat($(el).val()) || 0;
  let debitInput = row.find('input[name="txt_amount6[]"]');

  if(credit > 0){
    debitInput.val(0);
    debitInput.prop('readonly',true);
  }else{
    debitInput.prop('readonly',false);
  }

  hitungTotalFTR6();

}

function resetTotalFTR6(){
  $('#tot_debit_nag_ftr6, #tot_debit_nak_ftr6, #tot_debit_ftr6').val('');
  $('#tot_credit_nag_ftr6, #tot_credit_nak_ftr6, #tot_credit_ftr6').val('');
}

function hitungTotalFTR6(){

  let total_ftr = 0;
  let nag_debit = 0;
  let nak_debit = 0;
  let nag_credit = 0;
  let nak_credit = 0;
  let curr_h = 'IDR';

  $('#table-ftr6 .chk_ftr:checked').each(function(){

    let tr = $(this).closest('tr');

    let val = getNumber(tr.find('.txt_amount_ftr').val());
    let val_idr = getNumber(tr.find('.txt_amount_ftr_idr').val());

    // Profit center dokumennya sendiri. Dokumen lama belum punya (kolomnya
    // baru 02 Okt 2026), jadi mundur ke profit center akun kas yang membayar.
    let pc_baris = (tr.find('.pc_ftr').data('pcftr') || '').toString().trim().toUpperCase();
    let pc = pc_baris !== '' ? pc_baris : ($('#profit_center_kas6').val() || '').trim().toUpperCase();

    if (curr_h == 'IDR') {
      total_ftr += val_idr;
    }else{
      total_ftr += val;
    }

    if(pc === 'NAG'){
      nag_debit += val_idr;
    }else if(pc === 'NAK'){
      nak_debit += val_idr;
    }

  });

  let header_amount = total_ftr;

  if(!isManualAmountFTR6){
    $('#amount_kas6').val(angkaPolosFTR6(header_amount));
  }

  let header_amount_idr = isManualAmountFTR6 ? getNumber($('#amount_kas6').val()) : total_ftr;

  let header_pc = ($('#profit_center_kas6').val() || '').trim().toUpperCase();

  if(header_pc === 'NAG'){
    nag_credit += header_amount_idr;
  }else if(header_pc === 'NAK'){
    nak_credit += header_amount_idr;
  }

  $('#tbody6 tr').each(function(){

    let tr = $(this);

    let pc = (tr.find('select.prof_ctr6').first().val() || '').trim().toUpperCase();

    let debit  = getNumber(tr.find('input[name="txt_amount6[]"]').val());
    let credit = getNumber(tr.find('input[name="txt_credit6[]"]').val());

    if(pc === 'NAG'){
      nag_debit  += debit;
      nag_credit += credit;
    }else if(pc === 'NAK'){
      nak_debit  += debit;
      nak_credit += credit;
    }

  });

  let grand_debit  = nag_debit + nak_debit;
  let grand_credit = nag_credit + nak_credit;

  $('#tot_debit_nag_ftr6').val(nag_debit.toLocaleString('en-US'));
  $('#tot_debit_nak_ftr6').val(nak_debit.toLocaleString('en-US'));
  $('#tot_debit_ftr6').val(grand_debit.toLocaleString('en-US'));

  $('#tot_credit_nag_ftr6').val(nag_credit.toLocaleString('en-US'));
  $('#tot_credit_nak_ftr6').val(nak_credit.toLocaleString('en-US'));
  $('#tot_credit_ftr6').val(grand_credit.toLocaleString('en-US'));

}

$('#simpan6').on('click', function(){

  let header = {
    doc_num    : $('#doc_num6').val(),
    ref        : $('#ref_num6').val(),
    tgl        : $('#tgl_active6').val(),
    supp       : $('#nama_supp6').val(),
    account    : $('#account6').val(),
    currency   : $('#currency6').val(),
    kode_kas   : $('#kode_kas6').val(),
    pc_header  : $('#profit_center_kas6').val(),
    amount     : getNumber($('#amount_kas6').val()),
    desc       : $('#pesan6').val(),
    cash_flow  : $('#cash_flow6').val()
  };

  if(!header.tgl){
    Swal.fire('Warning','Tanggal wajib diisi','warning');
    return;
  }

  if(!header.supp){
    Swal.fire('Warning','Supplier wajib diisi','warning');
    return;
  }

  if(!header.account){
    Swal.fire('Warning','Account belum terisi','warning');
    return;
  }

  if(!header.desc || !header.desc.trim()){
    Swal.fire('Warning','Description tidak boleh kosong','warning');
    return;
  }

  if(!header.cash_flow){
    Swal.fire('Warning','Cash Flow Category tidak boleh kosong','warning');
    return;
  }

  if(header.amount <= 0){
    Swal.fire('Warning','Amount tidak boleh 0','warning');
    return;
  }

  if($('#table-ftr6 .chk_ftr:checked').length === 0){
    Swal.fire('Warning','Pilih minimal 1 FTR','warning');
    return;
  }

  let total_debit  = getNumber($('#tot_debit_ftr6').val());
  let total_credit = getNumber($('#tot_credit_ftr6').val());

  if(total_debit !== total_credit){
    Swal.fire('Error','Total Debit & Credit tidak balance','error');
    return;
  }

  let nag_debit  = getNumber($('#tot_debit_nag_ftr6').val());
  let nag_credit = getNumber($('#tot_credit_nag_ftr6').val());

  let nak_debit  = getNumber($('#tot_debit_nak_ftr6').val());
  let nak_credit = getNumber($('#tot_credit_nak_ftr6').val());

  if(nag_debit !== nag_credit){
    Swal.fire('Error','NAG tidak balance','error');
    return;
  }

  if(nak_debit !== nak_credit){
    Swal.fire('Error','NAK tidak balance','error');
    return;
  }

  let detail_ftr = [];

  $('#table-ftr6 .chk_ftr:checked').each(function(){

    let tr = $(this).closest('tr');

    let data = {
      no_ftr   : tr.find('.no_ftr').data('noftr'),
      type_pv : tr.find('.no_ftr').data('typeftr'),
      amount  : getNumber(tr.find('.txt_amount_ftr').val()),
      pc      : tr.find('.pc_ftr').data('pcftr')
    };

    detail_ftr.push(data);

  });

  let detail_adjust = [];
  let valid_adjust = true;

  $('#tbody6 tr').each(function(index){

    let tr = $(this);

    let coa   = tr.find('select.no_coa6').first().val();
    let pc    = tr.find('select.prof_ctr6').first().val();
    let cc    = tr.find('select.cost_ctr6').first().val();
    let debit = parseFloat(tr.find('input[name="txt_amount6[]"]').val()) || 0;
    let credit= parseFloat(tr.find('input[name="txt_credit6[]"]').val()) || 0;
    let desc  = tr.find('input[name="keterangan6[]"]').val();
    if(!desc){
      desc = $('#pesan6').val();
    }
    let curr  = tr.find('select.currenc6').first().val();
    let reff_doc  = tr.find('input[name="no_reff6[]"]').val();
    let reff_date  = tr.find('input[name="reff_date6[]"]').val();

    let rowData = {
      row: index + 1, coa, pc, cc, debit, credit, desc, curr, reff_doc, reff_date
    };

    if(!coa || coa === '-'){
      Swal.fire('Warning','COA wajib diisi','warning');
      tr.find('.no_coa6').focus();
      valid_adjust = false;
      return false;
    }

    if(!pc || pc === '-'){
      Swal.fire('Warning','Profit Center wajib diisi','warning');
      tr.find('.prof_ctr6').focus();
      valid_adjust = false;
      return false;
    }

    if(coaWajibCC.includes(coa)){
      if(!cc || cc === '-' || cc === ''){
        Swal.fire('Warning','COA wajib isi Cost Center','warning');
        tr.find('.cost_ctr6').focus();
        valid_adjust = false;
        return false;
      }
    }

    if(debit === 0 && credit === 0){
      Swal.fire('Warning','Debit/Credit harus diisi','warning');
      valid_adjust = false;
      return false;
    }

    detail_adjust.push(rowData);

  });

  if(!valid_adjust) return;

  let finalData = {
    header,
    detail_ftr,
    detail_adjust,
    total: {
      global_debit  : total_debit,
      global_credit : total_credit,
      nag_debit, nag_credit, nak_debit, nak_credit
    }
  };

  function doUpdatePv5(){

    // Cegah double-submit: tombol dikunci begitu user konfirmasi & mulai
    // proses save, baru dibuka lagi kalau ada error (supaya bisa dicoba ulang).
    $('#simpan6').prop('disabled', true);

    Swal.fire({
      title: 'Saving...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    $.ajax({
      url: 'update_ftr_cash.php',
      type: 'POST',
      dataType: 'json',
      data: { data: JSON.stringify(finalData) },
      success: function(res){

        if(res.status === 'ok'){
          Swal.fire({ icon: 'success', title: 'Success', text: res.message }).then(() => {
            location.href='petty-cashout.php';
          });
        }else{
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: res.message,
            showCancelButton: true,
            confirmButtonText: 'Coba Lagi',
            cancelButtonText: 'Tutup'
          }).then((retry) => {
            if(retry.isConfirmed){
              doUpdatePv5();
            }else{
              $('#simpan6').prop('disabled', false);
            }
          });
        }

      },
      error: function(xhr){
        console.log("ERROR AJAX:", xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Terjadi kesalahan server',
          showCancelButton: true,
          confirmButtonText: 'Coba Lagi',
          cancelButtonText: 'Tutup'
        }).then((retry) => {
          if(retry.isConfirmed){
            doUpdatePv5();
          }else{
            $('#simpan6').prop('disabled', false);
          }
        });
      }
    });

  }

  Swal.fire({
    title: 'Are you sure?',
    text: 'The data will be updated.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, save it!',
    cancelButtonText: 'Cancel'
  }).then((result) => {

    if(!result.isConfirmed) return;
    doUpdatePv5();

  });

});

</script>

<?php include '../footer.php'; ?>
