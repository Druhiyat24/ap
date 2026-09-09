<?php include '../header.php' ?>
<?php
// ============================================================================
// FORM EDIT REQUEST DEBIT NOTE — turunan dari create_request_dn.php.
// Beda dgn create: no_req/unik_code/supplier SUDAH ADA (tidak dibuat baru),
// supplier DIKUNCI (tidak boleh diubah lewat form ini), dan tabel item
// di-preload dari baris req_dn yang sudah tersimpan. User cuma boleh
// menambah baris baru (lewat Select PO / Add Row) atau menghapus baris yang
// sudah ada (Delete Row) — sama seperti create, TIDAK mengubah struktur
// kolom/urutan <td> supaya semua JS yang membaca cells[4]/cells[5]/td:eq(11..15)
// tetap jalan tanpa perubahan.
//
// Cuma request berstatus "Post" yang boleh diedit — begitu statusnya
// "Processed" artinya Debit Note sudah dibuat dari request ini di modul lain
// (no_dn terisi), jadi item-nya tidak boleh diutak-atik lagi dari sini.
// ============================================================================

$noReqParam = trim($_GET['no_req'] ?? '');

$sqlH = mysqli_query($conn2, "select no_req, tgl_req, nama_supp, deskripsi, total_amount, status, unik_code
    from req_dn_h where no_req = '" . mysqli_real_escape_string($conn2, $noReqParam) . "'");
$hdr  = $sqlH ? mysqli_fetch_assoc($sqlH) : null;

if (!$hdr) {
    ?>
    <div class="container-fluid mt-4 p-4">
      <div class="card app-card border-0">
        <div class="card-body p-4">
          <div class="app-empty" style="padding:40px 0;">
            <i class="fa fa-exclamation-triangle"></i>
            Request "<?= htmlspecialchars($noReqParam) ?>" not found.
          </div>
          <div class="text-center"><a href="request_debitnote.php" class="app-btn app-btn-primary app-btn-ctl"><i class="fa fa-angle-double-left"></i> Back to List</a></div>
        </div>
      </div>
    </div>
    <?php
    echo '</body></html>';
    exit;
}
if ($hdr['status'] !== 'Post') {
    ?>
    <div class="container-fluid mt-4 p-4">
      <div class="card app-card border-0">
        <div class="card-body p-4">
          <div class="app-empty" style="padding:40px 0;">
            <i class="fa fa-lock"></i>
            Request "<?= htmlspecialchars($hdr['no_req']) ?>" can no longer be edited (status: <?= htmlspecialchars($hdr['status']) ?>).
          </div>
          <div class="text-center"><a href="request_debitnote.php" class="app-btn app-btn-primary app-btn-ctl"><i class="fa fa-angle-double-left"></i> Back to List</a></div>
        </div>
      </div>
    </div>
    <?php
    echo '</body></html>';
    exit;
}

$kodepay  = $hdr['no_req'];
$unikCode = $hdr['unik_code'];
$fSupp    = $hdr['nama_supp'];
$fTglDoc  = !empty($hdr['tgl_req']) ? date('d-m-Y', strtotime($hdr['tgl_req'])) : date('d-m-Y');
$fDesc    = $hdr['deskripsi'];
$fStartPo = date('d-m-Y');
$fEndPo   = date('d-m-Y');

// Baris item yg sudah tersimpan — dipreload persis dgn format yg dipakai
// load_po_detail_temp.php (readonly, 16 kolom termasuk 5 hidden) utk baris
// yg berasal dari BPB nyata (id_bpb terisi), atau format Add Row (11 kolom,
// bebas diedit) utk baris manual (id_bpb kosong) — supaya kedua jenis baris
// tetap berperilaku sama seperti waktu pertama kali dibuat di create.
$existingRows = '';
$sqlItems = mysqli_query($conn2, "select no_po, no_bpb, item, qty, price, attn, seasons, no_reff, id_bpb, tgl_bpb, id_jo, id_item, unit
    from req_dn where no_req = '" . mysqli_real_escape_string($conn2, $kodepay) . "' order by id asc");
while ($sqlItems && $ri = mysqli_fetch_assoc($sqlItems)) {
    $total = (float) $ri['qty'] * (float) $ri['price'];
    $esc   = function ($v) { return htmlspecialchars((string) $v); };

    if (!empty($ri['id_bpb'])) {
        $existingRows .= '<tr>'
            . '<td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_po']) . '" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_bpb']) . '" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['item']) . '" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="number" min="1" value="' . $esc($ri['qty']) . '" class="form-control" id="txt_qty" name="txt_qty" oninput="modal_input_qty(value)" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="number" min="1" value="' . $esc($ri['price']) . '" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="text" class="form-control" id="tot_row" name="tot_row" value="' . $esc($total) . '" autocomplete="off" readonly tabindex="-1"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['attn']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['seasons']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_reff']) . '" autocomplete="off"></td>'
            . '<td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>'
            . '<td hidden value="' . $esc($ri['id_bpb']) . '"></td>'
            . '<td hidden value="' . $esc($ri['tgl_bpb']) . '"></td>'
            . '<td hidden value="' . $esc($ri['id_jo']) . '"></td>'
            . '<td hidden value="' . $esc($ri['id_item']) . '"></td>'
            . '<td hidden value="' . $esc($ri['unit']) . '"></td>'
            . '</tr>';
    } else {
        $existingRows .= '<tr>'
            . '<td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_po']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_bpb']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['item']) . '" autocomplete="off"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="number" min="1" value="' . $esc($ri['qty']) . '" class="form-control" id="txt_qty" name="txt_qty" oninput="modal_input_qty(value)" autocomplete="off"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="number" min="1" value="' . $esc($ri['price']) . '" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>'
            . '<td><input style="text-align:right;font-size:12px;" type="text" class="form-control" id="tot_row" name="tot_row" value="' . $esc($total) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['attn']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['seasons']) . '" autocomplete="off"></td>'
            . '<td><input style="font-size:12px;" type="text" class="form-control" name="keterangan[]" value="' . $esc($ri['no_reff']) . '" autocomplete="off"></td>'
            . '<td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>'
            . '</tr>';
    }
}
?>

<!-- Skin UI bersama (kartu, tabel, tombol, dropdown, tanggal, modal) -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<style>
/* Halaman-spesifik: area tabel pilihan PO/BPB di dalam modal.
   PENTING: header.php (dimuat di baris paling atas file ini) SUDAH punya
   .tableFix generik sendiri dengan height:100px TETAP. Spesifisitasnya sama
   dengan aturan di sini, jadi "height" itu TIDAK KETIMPA cuma dgn menambahkan
   max-height — height:100px tetap membatasi kotaknya jadi cuma pas 1 baris,
   berapa pun max-height yang diset di sini. Makanya height:auto WAJIB ditulis
   ulang secara eksplisit di bawah supaya max-height baru bisa benar-benar
   berlaku (max-height cuma jadi plafon kalau height-nya sendiri fleksibel). */
.tableFix{ height:auto; overflow:auto; border:1px solid #e6ebf3; border-radius:9px; }
.tableFix table{ width:100%; margin-bottom:0; }
.tableFix thead{ position:sticky; top:0; z-index:2; }
.tableFix thead th{ position:sticky; top:0; z-index:1; }
/* .rows5 = tinggi dikunci +- 5 baris data; sisanya discroll DI DALAM tabel
   supaya modal tidak memanjang mengikuti jumlah PO/BPB yang ketemu. */
.tableFix.rows5{ max-height:260px; }

/* Modal "Add Data" diperbesar (lebar & tinggi) — override khusus modal ini
   (ID-scoped) supaya tidak ikut membesarkan modal .app-modal lain di halaman
   manapun yang memakai skin yang sama. */
#mymodal .modal-dialog{ max-width:1500px; width:92%; }
#mymodal .modal-body{ max-height:82vh; }

/* Input di dalam baris item dibuat ringkas supaya barisnya tidak terlalu tinggi */
#mytable tbody td{ padding:6px 7px !important; }
#mytable tbody .form-control{ height:31px; font-size:12px; padding:4px 8px; }
/* Baris hasil "Select PO" (atau baris BPB yg sudah ada sblm diedit) DIKUNCI
   (readonly) — ditampilkan seperti TEKS BIASA, bukan kotak input, supaya
   jelas kelihatan sudah tidak bisa diedit lagi. Baris manual (Add Row /
   Interject Row / baris tanpa BPB yg sudah ada) TIDAK readonly. */
#mytable tbody .form-control[readonly]{
  background:transparent; border-color:transparent; box-shadow:none;
  color:#334155; cursor:default;
}
#mytable tbody .form-control[readonly][type=number]{ -moz-appearance:textfield; }
#mytable tbody .form-control[readonly][type=number]::-webkit-outer-spin-button,
#mytable tbody .form-control[readonly][type=number]::-webkit-inner-spin-button{
  -webkit-appearance:none; margin:0;
}
.app-dt-scroll{ max-height:420px; overflow:auto; border-radius:8px; }
#mytable thead th{ position:sticky; top:0; z-index:2; }
.rdn-item-toolbar{ margin-top:10px; }
.rdn-item-head{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.rdn-item-title{ font-size:12px; font-weight:700; color:#1e3a8a; text-transform:uppercase; letter-spacing:.4px; }
#mytable tbody tr:nth-child(even) td{ background:#f8fafc; }
#mytable tbody tr:hover td{ background:#eef4ff; }
#mytable tbody td:nth-child(7) input{ font-weight:700; color:#0f172a; }
#mytable tbody td:first-child input[type=checkbox]{ accent-color:#10b981; cursor:not-allowed; }
#mytable tbody td:last-child input[type=checkbox]{ accent-color:#e13c37; cursor:pointer; }

.mji-tot{
  border:1px solid #e6ebf3; border-left:4px solid #1d4ed8; border-radius:10px;
  background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.05); padding:12px 14px 13px;
}
.mji-tot-name{
  font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px;
  color:#475569; padding-bottom:6px; border-bottom:1px solid #eef2f7; margin-bottom:2px;
}
.mji-tot-split{ display:grid; grid-template-columns:1fr; row-gap:3px; margin-top:7px; }
.mji-tot-col{ display:flex; flex-direction:column; min-width:0; }
.mji-tot-lbl{
  font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;
  margin-bottom:1px; color:#1d4ed8; cursor:help;
}
input.mji-tot-val{
  display:block; width:100%; min-width:0; margin:0; padding:0; border:0;
  background:transparent; box-shadow:none; font-size:19px; font-weight:700;
  color:#0f172a; font-variant-numeric:tabular-nums; line-height:1.25; text-align:left;
}
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- ===== Header form ===== -->
  <div class="card app-card border-0">
    <div class="card-header app-card-header">
      <h5><i class="fa fa-pencil" aria-hidden="true"></i> FORM EDIT REQUEST DEBIT NOTE</h5>
    </div>
    <div class="card-body p-3">
      <form id="form-data" method="post">
        <div class="row g-3">
          <div class="col-md-2">
            <label class="app-flabel">No Request</label>
            <input type="text" readonly class="form-control app-ctl-static" id="no_doc" name="no_doc" value="<?= htmlspecialchars($kodepay) ?>" style="font-weight:700;color:#1e3a8a;background:#f8fafc;">
          </div>
          <div class="col-md-2">
            <label class="app-flabel">Date</label>
            <input type="text" name="tgl_doc" id="tgl_doc" class="form-control tanggal" value="<?= htmlspecialchars($fTglDoc) ?>" autocomplete="off">
            <input type="hidden" name="unik_code" id="unik_code" value="<?= htmlspecialchars($unikCode) ?>" readonly>
          </div>
          <div class="col-md-4">
            <label class="app-flabel">Supplier <span style="font-weight:400;color:#94a3b8;">(locked, cannot be changed)</span></label>
            <input type="text" readonly class="form-control app-ctl-static" id="nama_supp_disp" value="<?= htmlspecialchars($fSupp) ?>" style="background:#f8fafc;">
            <input type="hidden" id="nama_supp_fixed" value="<?= htmlspecialchars($fSupp) ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" id="mysupp" name="v" data-toggle="modal" class="app-btn app-btn-primary app-btn-ctl">
              <i class="fa fa-search-plus"></i> Select PO
            </button>
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-8">
            <label class="app-flabel">Descriptions</label>
            <textarea rows="2" class="form-control" name="pesan" id="pesan" placeholder="descriptions..." required style="font-size:13px;"><?= htmlspecialchars($fDesc) ?></textarea>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Item table ===== -->
  <div class="card app-card border-0 mt-4">
    <div class="card-body p-4">
      <div class="rdn-item-head">
        <span class="rdn-item-title">Item List <span class="ftot-sub" id="itemRowCount"></span></span>
        <span style="flex:1;"></span>
        <span class="app-search"><i class="fa fa-search"></i>
          <input type="text" id="itemSearch" placeholder="Search item..." autocomplete="off"></span>
      </div>
      <!-- Isi Attn/Seasons di sini SEKALI, langsung menimpa kolom Attn/Seasons di
           SEMUA baris yang sedang ada di tabel (isi-cepat/fill-down). Sesudahnya
           tiap baris tetap bisa diedit satu-satu seperti biasa — ini bukan
           binding permanen, cuma pengisi awal supaya tidak ketik berulang. -->
      <div class="row g-2 mb-3">
        <div class="col-md-3">
          <label class="app-flabel">Attn (fill all rows)</label>
          <input type="text" class="form-control" id="fillAttn" placeholder="Type to fill Attn for all rows..." autocomplete="off">
        </div>
        <div class="col-md-3">
          <label class="app-flabel">Seasons (fill all rows)</label>
          <input type="text" class="form-control" id="fillSeasons" placeholder="Type to fill Seasons for all rows..." autocomplete="off">
        </div>
      </div>
      <div class="app-dt-scroll">
        <table id="mytable" class="table table-hover app-dt" cellspacing="0" width="100%" style="table-layout:fixed;">
          <thead>
            <tr>
              <th style="width:2%;">-</th>
              <th style="width:10%;">No PO</th>
              <th style="width:10%;">No BPB</th>
              <th style="width:17%;">Item</th>
              <th style="width:8%;">Qty</th>
              <th style="width:8%;">Price</th>
              <th style="width:9%;">Total</th>
              <th style="width:9%;">Attn</th>
              <th style="width:10%;">Seasons</th>
              <th style="width:10%;">Reff/Style</th>
              <th style="width:3%;">Action</th>
            </tr>
          </thead>
          <tbody id="tbody2">
            <tr class="rdn-tpl-row" style="display:none;">
              <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input type="number" min="1" class="form-control text-right" id="txt_qty" name="txt_qty" oninput="modal_input_qty(value)" autocomplete="off"></td>
              <td><input type="number" min="1" class="form-control text-right" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
              <td><input type="text" class="form-control" id="tot_row" name="tot_row" placeholder="" autocomplete="off"></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
              <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
            </tr>
            <?php echo $existingRows; ?>
          </tbody>
        </table>
      </div>
      <!-- Toolbar aksi baris — SENGAJA di luar .app-dt-scroll (dulu <tfoot> di
           dalam tabel yg sama, jadi ikut ter-scroll begitu baris sudah banyak).
           Sekarang selalu terlihat, tidak perlu digulir dulu utk menjangkaunya. -->
      <div class="app-actions rdn-item-toolbar">
        <button type="button" class="app-btn app-btn-primary app-btn-sm" onclick="addRow('tbody2')"><i class="fa fa-plus"></i> Add Row</button>
        <button type="button" class="app-btn app-btn-warning app-btn-sm" onclick="InsertRow('tbody2')"><i class="fa fa-level-down"></i> Interject Row</button>
        <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="hapusbaris()"><i class="fa fa-trash"></i> Delete Row</button>
      </div>

      <form id="form-simpan">
        <div class="row g-3 mt-3">
          <div class="col-md-4"></div>
          <div class="col-md-4"></div>
          <div class="col-md-4">
            <div class="mji-tot">
              <div class="mji-tot-name">Request Summary</div>
              <div class="mji-tot-split">
                <div class="mji-tot-col">
                  <span class="mji-tot-lbl" title="Total dari seluruh baris item yang dicentang">Total Amount</span>
                  <input type="text" class="mji-tot-val" id="total_value_disp" readonly tabindex="-1" aria-readonly="true" value="0.00">
                </div>
              </div>
            </div>
            <input type="hidden" id="total_value" name="total_value" value="0.00">
            <input type="hidden" name="total_value_h" id="total_value_h" value="">
          </div>
        </div>
        <div class="app-actions mt-3">
          <button type="button" class="app-btn app-btn-primary app-btn-ctl" name="simpan" id="simpan"><i class="fa fa-floppy-o"></i> Save</button>
          <button type="button" class="app-btn app-btn-danger app-btn-ctl" name="batal" id="batal" onclick="location.href='request_debitnote.php'"><i class="fa fa-angle-double-left"></i> Back</button>
        </div>
      </form>
    </div>
  </div>

</div>

<!-- ===== Modal: Select PO / BPB ===== -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog app-modal app-modal-80 modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="Heading"><i class="fa fa-search-plus"></i> Add Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <form id="modal-form2" method="post">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="app-flabel">Supplier</label>
              <input type="text" class="form-control app-ctl-static" id="mdl_customer" name="mdl_customer" value="" readonly>
              <input type="hidden" id="mdl_idcustomer" name="mdl_idcustomer" value="" readonly>
            </div>
            <div class="col-md-5">
              <label class="app-flabel">PO Date</label>
              <div class="d-flex align-items-center" style="gap:8px;">
                <input type="text" class="form-control tanggal" id="startdate_bpb" name="startdate_bpb" value="<?= htmlspecialchars($fStartPo) ?>" placeholder="Start Date">
                <span>-</span>
                <input type="text" class="form-control tanggal" id="enddate_bpb" name="enddate_bpb" value="<?= htmlspecialchars($fEndPo) ?>" placeholder="End Date">
              </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="button" id="send2" name="send2" class="app-btn app-btn-success app-btn-sm">
                <i class="fa fa-search"></i> Search
              </button>
            </div>
          </div>

          <div class="app-mod-sec no-line" style="margin-top:16px;">
            <span>Purchase Order</span>
            <span style="flex:1;"></span>
            <span class="app-search"><i class="fa fa-search"></i>
              <input type="text" id="soSearch" placeholder="Search PO..." autocomplete="off"></span>
          </div>
          <div class="tableFix rows5">
            <table id="table-so" class="table table-hover app-dt" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width:5%;">Cek</th>
                  <th style="width:20%;">PO Number</th>
                  <th style="width:15%;">PO Date</th>
                  <th style="width:20%;">Supplier</th>
                  <th style="width:15%;">Type PO</th>
                  <th style="width:10%;">ID PO</th>
                </tr>
              </thead>
              <tbody id="details"></tbody>
            </table>
          </div>

          <div class="app-mod-sec no-line" style="margin-top:16px;">
            <span>BPB / Delivery</span>
            <span style="flex:1;"></span>
            <span class="app-search"><i class="fa fa-search"></i>
              <input type="text" id="bpbSearch" placeholder="Search BPB..." autocomplete="off"></span>
          </div>
          <div class="tableFix rows5">
            <table id="table-sj" class="table table-hover app-dt text-nowrap" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <th style="width:5%;">Bpb ID</th>
                  <th>PO Number</th>
                  <th>Bpb Number</th>
                  <th>Bpb Date</th>
                  <th>Item Desc</th>
                  <th>Unit</th>
                  <th>Qty</th>
                  <th>Qty Tagih</th>
                  <th>Price</th>
                  <th>Price Tagih</th>
                  <th style="width:5%;"><input type="checkbox" id="bpb_check_all" title="Check all"></th>
                </tr>
              </thead>
              <tbody id="details_sj"></tbody>
            </table>
          </div>

          <div class="row mt-3">
            <div class="col-md-6"></div>
            <div class="col-md-6">
              <div class="row align-items-center">
                <label for="mdl_total" class="col-sm-4 app-flabel mb-0">Total</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control text-right" id="mdl_total" name="mdl_total" placeholder="0.00" readonly>
                  <input type="hidden" id="mdl_total_h" name="mdl_total_h" placeholder="0.00" readonly>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="savesj" name="savesj" class="app-btn app-btn-primary" onclick="save_data_po()"><i class="fa fa-floppy-o"></i> Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left');

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>
<script>
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
  });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script>
    // Supplier SUDAH TETAP (lihat #nama_supp_fixed) — beda dgn create_request_dn.php
    // yg membaca dari <select>, di sini tinggal pakai nilai yg sudah dikunci.
    $("#form-data").on("click", "#mysupp", function(){
        var customer = $('#nama_supp_fixed').val();
        var create_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'delete_po_temp.php',
            data: {'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false;
            },
            success: function(data){
                $('#tbody').html('');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
            }
        });

        $('[name="mdl_customer"]').val(customer);
        $('[name="mdl_idcustomer"]').val(customer);
        $('#mymodal').modal('show');
    });

</script>

<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        var nama_supp = document.getElementById('mdl_idcustomer').value;
        var start_date = document.getElementById('startdate_bpb').value;
        var end_date = document.getElementById('enddate_bpb').value;

        $.ajax({
            type:'POST',
            url:'cari_nopo.php',
            data: {'nama_supp':nama_supp, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false;
            },
            success: function(data){
                $('#details').html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
            }
        });

        return false;
    });


    function tambah_sj(val){
        $('#table-sj tbody tr').remove();
        $("input[type=checkbox]:checked").each(function () {
            var id_po = $(this).closest('tr').find('td:eq(5)').attr('value');

            if (id_po != '' && id_po != null) {
                $.ajax({
                    type:'POST',
                    url:'cari_bpb_by_po.php',
                    data: {'id_po':id_po},
                    cache: 'false',
                    close: function(e){
                        e.preventDefault();
                        return false;
                    },
                    success: function(data){
                $('#details_sj').append(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
            }
        });
            }

        });
        return false;
    };

    function modal_sum_total_sj(){

        var mdl_qty = document.getElementsByName("mdl_qty");
        var mdl_price = document.getElementsByName("mdl_price");
        var mdl_qty_h = document.getElementsByName("mdl_qty_h");
        var mdl_price_h = document.getElementsByName("mdl_price_h");
        var input = document.getElementsByName("mdl_cek_sj");
        var total = 0;
    for (var i = 0; i < input.length; i++) {
      for (var i = 0; i < mdl_qty.length; i++) {
        for (var i = 0; i < mdl_price.length;  i++){
            if (input[i].checked) {
                mdl_qty[i].readOnly = false;
                mdl_price[i].readOnly = false;
                mdl_qty[i].value = mdl_qty_h[i].value;
                mdl_price[i].value = mdl_price_h[i].value;
                total += parseFloat(mdl_qty_h[i].value) * parseFloat(mdl_price_h[i].value);
            } else {
                mdl_qty[i].readOnly = true;
                mdl_qty[i].value = '';
                mdl_price[i].readOnly = true;
                mdl_price[i].value = '';

            }
        }

    }

}
document.getElementsByName("mdl_total")[0].value = formatMoney(total.toFixed(2));
document.getElementsByName("mdl_total_h")[0].value = total.toFixed(2);

}


function mdl_input_qty(){

    var input = document.getElementsByName("mdl_cek_sj");
    var qty = document.getElementsByName('mdl_qty');
    var qty_h = document.getElementsByName('mdl_qty_h');
    var price = document.getElementsByName('mdl_price');

    var total = 0;

    for (var i = 0; i < input.length; i++) {
        if (input[i].checked) {
            if (parseFloat(qty[i].value) > parseFloat(qty_h[i].value)) {
                qty[i].value = qty_h[i].value;
            }

            total += parseFloat(qty[i].value) * parseFloat(price[i].value);
        }
    }

    document.getElementsByName("mdl_total")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("mdl_total_h")[0].value = total.toFixed(2);

}

function mdl_input_price(){

    var input = document.getElementsByName("mdl_cek_sj");
    var qty = document.getElementsByName('mdl_qty');
    var price = document.getElementsByName('mdl_price');

    var total = 0;

    for (var i = 0; i < input.length; i++) {
        if (input[i].checked) {
            total += parseFloat(qty[i].value) * parseFloat(price[i].value);
        }
    }

    document.getElementsByName("mdl_total")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("mdl_total_h")[0].value = total.toFixed(2);

}

function save_data_po(){

    var total       = $('[name="mdl_total_h"]').val();
    $('[name="total_value_h"]').val(total);
    $('[name="total_value"]').val(formatMoney(total));

    simpan_temp_po();

}

async function simpan_temp_po(){
    var res = await simpan_po_detail_temporary();
    if (res && res.status === 'error') {
        Swal.fire({ icon: 'error', title: 'Failed to save selected BPB', text: res.message });
        return;
    }
    load_po_detail_temporary();
}

function simpan_po_detail_temporary(){
    var rows = [];
    $("#table-sj input[name='mdl_cek_sj']:checked").each(function() {
        var $tr = $(this).closest('tr');
        rows.push({
            id_bpb:      $tr.find('td:eq(0)').attr('value'),
            no_po:       $tr.find('td:eq(1)').attr('value'),
            no_bpb:      $tr.find('td:eq(2)').attr('value'),
            tgl_bpb:     $tr.find('td:eq(3)').attr('value'),
            itemdesc:    $tr.find('td:eq(4)').attr('value'),
            unit:        $tr.find('td:eq(5)').attr('value'),
            qty:         $tr.find('td:eq(6)').attr('value'),
            qty_tagih:   parseFloat($tr.find('td:eq(7) input').val(), 10) || 0,
            price:       $tr.find('td:eq(8)').attr('value'),
            price_tagih: parseFloat($tr.find('td:eq(9) input').val(), 10) || 0,
            id_item:     $tr.find('td:eq(13)').attr('value'),
            id_jo:       $tr.find('td:eq(14)').attr('value')
        });
    });

    if (!rows.length) { return $.Deferred().resolve({ status: 'success', baris: 0 }).promise(); }

    return $.ajax({
        type: 'POST',
        url: 'insert_po_detail_temp_bulk.php',
        data: { rows: JSON.stringify(rows), create_user: '<?php echo $user ?>' },
        dataType: 'json'
    }).fail(function (xhr) {
        console.log(xhr);
    });
}

function load_po_detail_temporary() {
    $('#mymodal').modal('hide');
    var create_user = '<?php echo $user ?>';

    $.ajax({
        type:'POST',
        url:'load_po_detail_temp.php',
        data: {'create_user':create_user},
        cache: 'false',
        close: function(e){
            e.preventDefault();
            return false;
        },
        success: function(data){
            $('#tbody2').append(data);
            mdl_input_price();
            if (typeof hitungRow === 'function') { hitungRow(); }
            var $card = $('#mytable').closest('.card');
            if ($card.length) {
                $('html, body').animate({ scrollTop: Math.max(0, $card.offset().top - 80) }, 350);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr);
            Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
        }
    });
}


</script>


<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
    } );
</script>


<script type="text/javascript">

   // JavaScript Document
   function addRow(tableID) {
    var tableID = "tbody2";
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

    $(function() {
        $('.selectpicker').selectpicker();
    });
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
    $(function() {
      var selectcoba = rowCount;
      $('.rowCount').select2({
         theme: 'bootstrap4'
     })
      $('.select2add').select2({
        theme: 'bootstrap4'
    })
  });
    $coa = '';
    var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="text" class="form-control" id="tot_row" name="tot_row" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';


    row.innerHTML = element1;
}

    function deleteRow()
    {
        try
        {
            var table = document.getElementById("tbody2");
            var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
            {
                var row = table.rows[i];
                var chkbox = row.cells[10].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                {
                    if (rowCount <= 1)
                    {
                        Swal.fire({ icon: 'warning', title: 'Tidak dapat menghapus semua baris.' });
                        break;
                    }
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                }
            }
        } catch(e)
        {
            Swal.fire({ icon: 'error', title: 'Error', text: String(e && e.message ? e.message : e) });
        }
    }

    function InsertRow(tableID)
    {
        try{
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
            {
                var row = table.rows[i];
                var chkbox = row.cells[10].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                {
                    $(function() {
                        $('.selectpicker').selectpicker();

                    });

                    $(document).ready(function () {
                        $('.tanggal').datepicker({
                            format: "dd-mm-yyyy",
                            autoclose:true
                        });
                    });
                    var element2 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="text" class="form-control" id="tot_row" name="tot_row" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';
                    var newRow = table.insertRow(i+1);
                    newRow.innerHTML = element2;

                }

            }
        } catch(e)
        {
            Swal.fire({ icon: 'error', title: 'Error', text: String(e && e.message ? e.message : e) });
        }
    }

    function hitungRow(){
        var table = document.getElementById("tbody2");
        var rowCount2 = table.rows.length;
        var tota = 0;
        var harga = 0;
        var tot_price = 0;

        for(var i=0; i< (table.rows.length); i++){

            var qty = document.getElementById("tbody2").rows[i].cells[4].children[0].value || 0;
            var price = document.getElementById("tbody2").rows[i].cells[5].children[0].value || 0;
            harga = parseFloat(qty) * parseFloat(price);
            tota += parseFloat(harga);

            document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
            document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));
            document.getElementById("total_value_disp").value = formatMoney(tota.toFixed(2));
        }

    }


    async function hapusbaris(){
       await deleteRow()
       console.log("result");
       hitungRow();
   }
</script>

<script type="text/javascript">
$(function () {
    // ---- Search tabel item (client-side, tanpa reload) -------------------------
    $('#itemSearch').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#tbody2 tr').not('.rdn-tpl-row').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(q) >= 0);
        });
    });

    // ---- Jumlah baris di judul tabel --------------------------------------------
    function updateItemRowCount() {
        var n = $('#tbody2 tr').not('.rdn-tpl-row').length;
        $('#itemRowCount').text(n ? (n + ' rows') : '');
    }
    updateItemRowCount();
    if (window.MutationObserver && document.getElementById('tbody2')) {
        new MutationObserver(updateItemRowCount).observe(document.getElementById('tbody2'), { childList: true });
    }

    // ---- Isi-cepat Attn/Seasons utk semua baris ---------------------------------
    $('#fillAttn').on('input', function () {
        var v = $(this).val();
        $('#tbody2 tr').not('.rdn-tpl-row').each(function () {
            $(this).find('td:eq(7) input').val(v);
        });
    });
    $('#fillSeasons').on('input', function () {
        var v = $(this).val();
        $('#tbody2 tr').not('.rdn-tpl-row').each(function () {
            $(this).find('td:eq(8) input').val(v);
        });
    });

    // Total Amount dihitung ulang begitu halaman siap (baris yg sudah ada
    // dimuat langsung dari PHP, bukan lewat ajax, jadi hitungRow() perlu
    // dipanggil manual sekali di awal supaya kartu Total tidak nol/kosong).
    if (typeof hitungRow === 'function') { hitungRow(); }
});
</script>



<script type="text/javascript">
  function modal_input_qty(){

    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var qty = document.getElementById("tbody2").rows[i].cells[4].children[0].value || 0;
        var price = document.getElementById("tbody2").rows[i].cells[5].children[0].value || 0;
        harga = parseFloat(qty) * parseFloat(price);
        tota += parseFloat(harga);

        document.getElementById("tbody2").rows[i].cells[6].children[0].value = formatMoney(harga.toFixed(2));


        document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
        document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));
        document.getElementById("total_value_disp").value = formatMoney(tota.toFixed(2));

    }
}

function modal_input_amt(){

    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var qty = document.getElementById("tbody2").rows[i].cells[4].children[0].value || 0;
        var price = document.getElementById("tbody2").rows[i].cells[5].children[0].value || 0;
        harga = parseFloat(qty) * parseFloat(price);
        tota += parseFloat(harga);

        document.getElementById("tbody2").rows[i].cells[6].children[0].value = formatMoney(harga.toFixed(2));


        document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
        document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));
        document.getElementById("total_value_disp").value = formatMoney(tota.toFixed(2));

    }
}
</script>


<script type="text/javascript">
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};
</script>

<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');

Array.prototype.forEach.call(numInputs, addListener);


function addListener(elm,index){
  elm.setAttribute('min', 1);

  elm.addEventListener('keypress', function(e){
     var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
     str = String.fromCharCode(key);
     if (str.localeCompare('-') === 0){
       event.preventDefault();
   }

});

}
</script>

<script type="text/javascript">
    // BULK UPDATE — sama alasannya dgn create_request_dn.php: dulu tiap baris
    // ditembak sendiri-sendiri, sekarang dikumpulkan lalu dikirim SEKALI ke
    // update_req_dn_bulk.php (yg akan menghapus baris LAMA lalu memasukkan
    // baris versi TERBARU dalam satu transaksi) supaya tidak ada baris yg
    // "nanggung" tersimpan sebagian saja.
    $("#form-simpan").on("click", "#simpan", function(){
        var no_req = document.getElementById('no_doc').value;
        var tgl_req = document.getElementById('tgl_doc').value;
        var unik_code = document.getElementById('unik_code').value;
        var deskripsi = document.getElementById('pesan').value;
        var total_amount = document.getElementById('total_value_h').value;
        var create_user = '<?php echo $user; ?>';

        if (total_amount === '') {
            Swal.fire({ icon: 'warning', title: 'Please Input Amount' });
            return;
        }
        if (parseFloat(total_amount) <= 0) {
            Swal.fire({ icon: 'warning', title: "Amount can't be Minus" });
            return;
        }
        if (parseFloat(total_amount) === 0) {
            Swal.fire({ icon: 'warning', title: "Total Amount can't be Zero" });
            return;
        }

        var rows = [];
        $('#tbody2 tr').not('.rdn-tpl-row').each(function () {
            var $tr = $(this);
            var qty = parseFloat($tr.find('td:eq(4) input').val()) || 0;
            var price = parseFloat($tr.find('td:eq(5) input').val()) || 0;
            if (qty <= 0 || price <= 0) { return; }

            rows.push({
                no_po:    $tr.find('td:eq(1) input').val(),
                no_bpb:   $tr.find('td:eq(2) input').val(),
                item:     $tr.find('td:eq(3) input').val(),
                qty:      qty,
                price:    price,
                attn:     $tr.find('td:eq(7) input').val(),
                seasons:  $tr.find('td:eq(8) input').val(),
                no_reff:  $tr.find('td:eq(9) input').val(),
                id_bpb:   $tr.find('td:eq(11)').attr('value') || '',
                tgl_bpb:  $tr.find('td:eq(12)').attr('value') || '',
                id_jo:    $tr.find('td:eq(13)').attr('value') || '-',
                id_item:  $tr.find('td:eq(14)').attr('value') || '-',
                unit:     $tr.find('td:eq(15)').attr('value') || '-'
            });
        });

        if (!rows.length) {
            Swal.fire({ icon: 'warning', title: 'No item row to save', text: 'Isi Qty dan Price minimal satu baris.' });
            return;
        }

        $.ajax({
            type:'POST',
            url:'update_req_dn_h.php',
            data: {'no_req':no_req, 'tgl_req':tgl_req, 'unik_code':unik_code, 'deskripsi':deskripsi, 'total_amount':total_amount, 'create_user':create_user},
            dataType: 'json',
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(hres){
                if (!hres || hres.status !== 'success') {
                    Swal.fire({ icon: 'error', title: 'Failed to save request', text: (hres && hres.message) || 'Unknown error' });
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: 'update_req_dn_bulk.php',
                    data: { unik_code: unik_code, create_user: create_user, rows: JSON.stringify(rows) },
                    dataType: 'json'
                }).done(function (res) {
                    if (res && res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Data Updated Successfully', text: 'Document Number ' + res.no_req }).then(function () {
                            window.location = 'request_debitnote.php';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed to save items', text: (res && res.message) || 'Unknown error' });
                    }
                }).fail(function (xhr) {
                    console.log(xhr);
                    Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
            }
        });
    });
</script>

<script type="text/javascript">
    // ---- Search per tabel (client-side, tanpa reload) --------------------------
    function tableRowFilter(inputSel, tbodySel) {
        $(document).on('input', inputSel, function () {
            var q = $(this).val().toLowerCase();
            $(tbodySel + ' tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) >= 0);
            });
        });
    }
    tableRowFilter('#soSearch', '#details');
    tableRowFilter('#bpbSearch', '#details_sj');

    // ---- Check All utk tabel BPB ------------------------------------------------
    $(document).on('change', '#bpb_check_all', function () {
        var c = this.checked;
        $("#details_sj input[name='mdl_cek_sj']").prop('checked', c);
        if (typeof modal_sum_total_sj === 'function') { modal_sum_total_sj(); }
    });
    $(document).on('click', "#details_sj input[name='mdl_cek_sj']", function () {
        if (!this.checked) { $('#bpb_check_all').prop('checked', false); }
    });
</script>

</body>

</html>
