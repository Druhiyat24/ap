<!-- ==========================================================================
     mj_ppn_masukan.php — tab "PPN Masukan" (upload rekap Faktur Pajak Masukan).
     Sengaja SELF-CONTAINED (JS-nya di file ini) supaya tidak mengganggu JS tab
     lain di create_memorial_journal.php. Semua id diberi akhiran "_ppn"/"Ppn".
     Alur: upload file -> proses_upload_ppn.php (staging tbl_ppn_masukan_upload)
           -> ajx_get_data_ppn.php (preview) -> save_mj_ppn.php (GM + jurnal).
     Preview 1 tabel; kolom Cost Center / Buyer / Worksheet sengaja disembunyikan
     (selalu "-" utk jurnal PPN). Description otomatis "PPN - <SUPPLIER>".
     Ganti Profit Center -> preview & total per PC langsung ikut berubah.
     Kelas CSS diberi prefiks "ppn-" supaya tidak menimpa gaya tab lain.
     ========================================================================== -->
<style>
    /* -------- Panel header: Date/Type/SB I + upload jadi SATU blok ------------ */
    .ppn-head {
        border: 1px solid #e6ebf3; border-radius: 12px; background: #fff;
        padding: 14px 16px 4px; margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
    }
    /* Kotak setinggi input, dipakai kontrol non-input (ceklis) supaya sebaris rapi */
    .ppn-field {
        display: flex; align-items: center; gap: 12px;
        border: 1px solid #dbe4f3; border-radius: 8px; background: #fff;
        padding: 0 12px; min-height: 38px;
    }
    .ppn-field .custom-control-label { font-size: 13px; color: #334155; cursor: pointer; }
    .ppn-lock { color: #94a3b8; font-size: 10px; margin-left: 3px; }

    /* Catatan Profit Center: satu baris ramping di bawah panel, bukan sebesar field */
    .ppn-pc-note {
        display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
        border-top: 1px dashed #e6ebf3; margin-top: 8px; padding: 9px 2px 8px;
        font-size: 11.5px; color: #64748b; line-height: 1.45;
    }
    .ppn-pc-note i { color: var(--app-accent, #17a2b8); font-size: 13px; }
    .ppn-pc-note b { color: #334155; }
    .ppn-pc-badges { display: inline-flex; gap: 5px; }
    .ppn-pc-badge {
        display: inline-block; padding: 1px 8px; border-radius: 20px;
        background: var(--app-accent-soft, #e4f6f9); color: var(--app-accent-dark, #0e7c8f);
        font-weight: 700; font-size: 10.5px; letter-spacing: .03em;
    }

    /* -------- Bar upload: strip mendatar (drop area kiri, tombol kanan) ------- */
    .ppn-upload-bar {
        display: flex; align-items: center; gap: 14px;
        border: 1.5px dashed #bcd7f5; border-radius: 10px;
        background: linear-gradient(180deg, #fbfdff, #f5f9ff); padding: 12px 14px;
        transition: border-color .15s ease, background .15s ease;
    }
    .ppn-upload-bar:hover, .ppn-upload-bar.is-drag {
        border-color: var(--app-accent, #17a2b8); background: #f3fbfd;
    }
    .ppn-drop { flex: 1 1 auto; display: flex; align-items: center; gap: 12px; cursor: pointer; margin-bottom: 0; }
    .ppn-drop i { font-size: 25px; color: var(--app-accent, #17a2b8); }
    .ppn-drop-txt { font-size: 13.5px; font-weight: 600; line-height: 1.3; color: #334155; }
    .ppn-drop-file { font-size: 11.5px; color: #94a3b8; }
    .ppn-drop-file.has-file { color: var(--app-accent-dark, #0e7c8f); font-weight: 700; }
    .ppn-upload-act { flex: 0 0 auto; display: flex; gap: 8px; }

    /* -------- Ceklis Include (SB I) — kotaknya memakai .ppn-field di atas ----- */
    .ppn-sb1-note { font-style: italic; font-weight: 600; color: #c00000; font-size: 11px; }
    /* -------- Tabel preview jurnal -------- */
    .ppn-tbl-wrap {
        max-height: 360px; overflow: auto;
        border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }
    .ppn-tbl { margin-bottom: 0; border: 0; }
    /* Gradien dipasang di BARIS (tr), bukan tiap sel — kalau di th, tiap kolom
       menggambar gradiennya sendiri sehingga header terlihat berpita.
       Sticky dipindah ke <thead> supaya latar baris ikut menempel saat scroll
       (kalau sticky-nya di th, latar tr tidak ikut dan header jadi transparan). */
    .ppn-tbl thead { position: sticky; top: 0; z-index: 2; background: #1E3A8A; }
    .ppn-tbl thead tr { background: linear-gradient(90deg, #1E3A8A, #2f5bbf); }
    .ppn-tbl thead th {
        background: transparent;
        color: #fff; border: 0; border-bottom: 2px solid #16306e;
        font-size: 11px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase;
        white-space: nowrap; vertical-align: middle; padding: 9px 10px;
    }
    .ppn-tbl tbody td {
        font-size: 12px; vertical-align: middle; white-space: nowrap;
        padding: 6px 10px; border-top: 0; border-bottom: 1px solid #eef2f7;
    }
    /* Kolom di tabel Journal Preview DIKUNCI lebarnya (table-layout:fixed di
       elemen <table>-nya) supaya Description/Supplier tidak melebar sendiri
       mengikuti isinya — teks panjang dipotong (...) alih-alih mendorong tabel
       jadi sangat lebar. Hanya berlaku di tabel ini (bukan .ppn-tbl umum),
       karena tabel Invoice Preview di bawahnya tetap butuh lebar mengikuti isi. */
    #tbl_ppn_jurnal tbody td { overflow: hidden; text-overflow: ellipsis; }
    .ppn-tbl tbody tr:nth-child(even) td { background: #fbfcfe; }
    .ppn-tbl tbody tr:hover td { background: #eaf1ff; }
    .ppn-coa-no { font-weight: 600; color: #1E3A8A; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ppn-coa-nm { font-size: 10.5px; color: #64748b; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ppn-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .ppn-muted { color: #cbd5e1; }
    .ppn-tbl td.ppn-deb { background: #f7fbff !important; font-weight: 600; color: #1d4ed8; }
    .ppn-tbl td.ppn-cre { background: #fffdf5 !important; font-weight: 600; color: #b45309; }
    .ppn-tbl tbody tr:hover td.ppn-deb,
    .ppn-tbl tbody tr:hover td.ppn-cre { background: #eaf1ff !important; }
    .ppn-badge-curr { background: #eef2f7; color: #475569; border-radius: 4px; padding: 1px 6px; font-size: 11px; }
    .ppn-tbl tbody tr.ppn-grp-start td { border-top: 2px solid #cfdaea; }
    .ppn-toggle { cursor: pointer; user-select: none; }
    .ppn-toggle:hover { color: #1E3A8A; }
    .ppn-toggle i { width: 12px; font-size: 11px; color: #64748b; }
    .ppn-tools { display: flex; align-items: center; gap: 10px; margin-left: auto; }
    .ppn-search-wrap { position: relative; display: inline-block; }
    .ppn-search-wrap > i {
        position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
        font-size: 11px; color: #94a3b8; pointer-events: none;
    }
    .ppn-search {
        border: 1px solid #dbe4f3; border-radius: 8px; padding: 5px 11px 5px 28px;
        font-size: 12px; color: #334155; width: 250px; max-width: 100%; background: #fff;
    }
    .ppn-search:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); outline: none; }
    .ppn-count {
        font-size: 11px; color: #64748b; white-space: nowrap; font-weight: 600;
        background: #eef2f7; border-radius: 20px; padding: 2px 9px; margin-left: 6px;
    }

    /* -------- Tile total: ringkas, aksen warna tipis di kiri --------
       Sengaja TIDAK memakai header tebal bergradasi seperti form Outgoing Bank —
       di sini isinya cuma 2 angka, kartu besar jadi mubazir tempat. */
    .ppn-tot {
        display: flex; flex-direction: column; gap: 6px; height: 100%;
        background: #fff; border: 1px solid #e8edf5; border-left: 4px solid #94a3b8;
        border-radius: 8px; padding: 10px 14px; box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .ppn-tot.tone-nag { border-left-color: #5b7ba8; }
    .ppn-tot.tone-nak { border-left-color: #4f8a6b; }
    .ppn-tot.tone-all { border-left-color: #4a5578; background: #f8fafc; }
    .ppn-tot-name {
        font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis;
    }
    /* Debit & Credit berdampingan supaya tile tetap pendek, TAPI wajib dipisah
       tegas: nilainya sering sama persis, kalau menempel dua angka itu terbaca
       sebagai satu deretan panjang. Jadi: lebar sama rata + garis pemisah +
       label diberi warna berbeda (biru/oranye, sewarna kolom di tabel). */
    .ppn-tot-grid { display: flex; align-items: stretch; }
    .ppn-tot-item { flex: 1 1 0; min-width: 0; padding-right: 10px; }
    .ppn-tot-item + .ppn-tot-item { border-left: 1px solid #e5eaf2; padding-left: 12px; padding-right: 0; }
    .ppn-tot-lbl {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .6px; margin-bottom: 1px; color: #64748b;
    }
    .ppn-tot-item.is-deb .ppn-tot-lbl { color: #1d4ed8; }
    .ppn-tot-item.is-cre .ppn-tot-lbl { color: #b45309; }
    .ppn-tot-val {
        font-size: 15.5px; font-weight: 700; color: #1f2937;
        font-variant-numeric: tabular-nums; line-height: 1.25;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .ppn-tot.tone-all .ppn-tot-val { font-size: 16.5px; color: #16306e; }
    .ppn-tot-bal { font-size: 11px; font-weight: 700; }
</style>

<?php
// Daftar Profit Center — dipakai dropdown header, kartu total, & peta utk tabel.
$ppnPcList = [];
$sqlPpnPc = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
while ($rPpnPc = mysqli_fetch_array($sqlPpnPc)) { $ppnPcList[] = $rPpnPc; }

// Warna kartu total per PC (mengikuti tone di form Outgoing Bank).
$ppnTone = ['NAG' => 'tone-nag', 'NAK' => 'tone-nak'];

// Peta kode_pc -> id & nama, dipakai JS utk menampilkan PC lengkap di tabel.
$ppnPcMap = [];
foreach ($ppnPcList as $p) { $ppnPcMap[$p['kode_pc']] = ['id' => $p['id_pc'], 'nama' => $p['nama_pc']]; }

// ---- Batas tanggal dari CLOSING PERIODE ----------------------------------
// Tanggal GM tidak boleh mundur ke periode yang bukunya sudah ditutup.
// Dipakai sbg startDate datepicker; validasi sungguhannya tetap di server
// (save_mj_ppn.php), karena batas di UI saja gampang dilewati.
require_once __DIR__ . '/../closing_periode_guard.php';
$ppnMinDate = closing_min_date($conn2);                 // Y-m-d | null
$ppnMinDp   = $ppnMinDate ? date('d-m-Y', strtotime($ppnMinDate)) : '';
// Default tanggal = hari ini, tapi jangan sampai jatuh di periode tertutup.
$ppnDefDate = date('Y-m-d');
if ($ppnMinDate && $ppnDefDate < $ppnMinDate) { $ppnDefDate = $ppnMinDate; }

// ---- Type DIKUNCI ke kategori VAT ---------------------------------------
// Jurnal PPN Masukan selalu masuk kategori VAT, user tidak boleh menggantinya.
// Dicari lewat NAMA (bukan id_cmj) supaya tetap kena walau id di produksi beda.
// Kalau kategori VAT belum ada (migrasi belum dijalankan), dropdown dikembalikan
// ke daftar penuh supaya halaman tetap bisa dipakai — bukan mati total.
$qVat = mysqli_query($conn1, "select id_cmj, nama_cmj from master_category_mj where UPPER(TRIM(nama_cmj)) = 'VAT' order by id_cmj limit 1");
$ppnVat = $qVat ? mysqli_fetch_assoc($qVat) : null;
?>

<div class="card shadow-sm">
    <div class="card-body">

        <!-- ============ HEADER: satu panel — inputan (kiri) | upload (kanan) ============ -->
        <div class="ppn-head">
            <div class="row">
                <div class="col-lg-7">
                    <div class="form-row">
                        <div class="col-md-4 mb-2">
                            <label class="app-flabel">Date</label>
                            <input type="text" class="form-control tanggal" id="mj_date_ppn" name="mj_date_ppn"
                                value="<?php echo date('d-m-Y', strtotime($ppnDefDate)); ?>" autocomplete="off"
                                <?php if ($ppnMinDp) { ?>title="Closed period: the earliest date allowed is <?php echo date('d M Y', strtotime($ppnMinDate)); ?>"<?php } ?>>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="app-flabel">Type
                                <?php if ($ppnVat) { ?><i class="fa fa-lock ppn-lock"
                                    title="Locked: PPN Masukan journals are always posted under the VAT category"></i><?php } ?>
                            </label>
                            <?php if ($ppnVat) { ?>
                            <!-- TERKUNCI: cuma satu pilihan (VAT) & disabled, jadi tidak bisa diubah.
                                 Nilainya tetap terbaca JS lewat .val() walau disabled. Penguncian
                                 sungguhannya ada di save_mj_ppn.php (server yang menentukan). -->
                            <select class="form-control select2" id="id_cmj_ppn" name="id_cmj_ppn" disabled
                                    title="PPN Masukan journals are always posted under the VAT category">
                                <option value="<?php echo $ppnVat['id_cmj']; ?>" selected><?php echo $ppnVat['nama_cmj']; ?></option>
                            </select>
                            <?php } else { ?>
                            <!-- Kategori VAT belum ada di master (migrasi belum dijalankan) -->
                            <select class="form-control select2" id="id_cmj_ppn" name="id_cmj_ppn">
                                <option value="">-- Select Type --</option>
                                <?php
                                $sqlPpnCat = mysqli_query($conn1, "select id_cmj, nama_cmj from master_category_mj order by id_cmj");
                                while ($rPpnCat = mysqli_fetch_array($sqlPpnCat)) {
                                    echo "<option value='" . $rPpnCat['id_cmj'] . "'>" . $rPpnCat['nama_cmj'] . "</option>";
                                }
                                ?>
                            </select>
                            <?php } ?>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="app-flabel">SB I</label>
                            <!-- Dijadikan kotak setinggi input supaya sebaris rapi dgn Date & Type -->
                            <div class="ppn-field ppn-sb1">
                                <div class="custom-control custom-checkbox mb-0">
                                    <input type="checkbox" class="custom-control-input" id="to_sb1_ppn" name="to_sb1_ppn" value="1">
                                    <label class="custom-control-label" for="to_sb1_ppn">Include</label>
                                </div>
                                <span class="ppn-sb1-note" id="txt_sb1_ppn"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="app-flabel">Upload Tax Invoice Recap File</label>
                    <div class="ppn-upload-bar" id="uploadBoxPpn">
                        <label for="fileUploadPpn" class="ppn-drop">
                            <i class="fa fa-cloud-upload"></i>
                            <span>
                                <span class="ppn-drop-txt">Click or drag an Excel file here</span><br>
                                <span class="ppn-drop-file" id="fileNamePpn">No file selected</span>
                            </span>
                        </label>
                        <div class="ppn-upload-act">
                            <button type="button" id="btnUploadPpn" class="app-btn app-btn-success app-btn-sm">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                            <a target="_blank" href="format-excel/format_ppn_masukan.xls?ver=<?php echo time(); ?>" class="app-btn app-btn-dark app-btn-sm">
                                <i class="fa fa-file-excel-o"></i> Template
                            </a>
                        </div>
                        <!-- TANPA atribut accept (sama spt tab Upload Journal): accept bikin
                             file ter-filter/abu-abu di dialog sehingga tidak bisa dipilih. -->
                        <input type="file" id="fileUploadPpn" style="display:none">
                    </div>
                </div>
            </div>

            <!-- Catatan Profit Center: dibuat satu baris ramping di bawah, bukan kotak
                 sebesar field — supaya tidak bersaing dgn kontrol yang benar-benar diisi. -->
            <div class="ppn-pc-note">
                <i class="fa fa-info-circle"></i>
                <span><b>Profit Center</b> is read per row from the last column of the uploaded file
                      &mdash; one file may contain several profit centers.</span>
                <span class="ppn-pc-badges">
                    <?php foreach ($ppnPcList as $p) { ?>
                        <span class="ppn-pc-badge" title="<?php echo htmlspecialchars($p['nama_pc']); ?>"><?php echo $p['kode_pc']; ?></span>
                    <?php } ?>
                </span>
            </div>
        </div>

        <!-- ============ PREVIEW FAKTUR (isi file apa adanya) ============
             Ditaruh di ATAS Preview Jurnal supaya alurnya jelas: apa yang
             diupload -> jadi jurnal apa. Tidak dipakai untuk perhitungan. -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <label class="mb-0 ppn-toggle" id="ppn_fk_toggle">
                <i class="fa fa-chevron-right" id="ppn_fk_ico"></i>
                <b>Invoice Preview</b>
                <span class="ppn-count" id="ppn_count_fk">0 rows</span>
                <span class="text-muted" style="font-weight:400;font-size:12px;">(uploaded file contents — click to expand)</span>
            </label>
            <div class="ppn-tools">
                <!-- Wadah yang di-hide (bukan input-nya) supaya ikon ikut hilang saat dilipat. -->
                <span class="ppn-search-wrap" id="ppn_search_fk_wrap" style="display:none;">
                    <i class="fa fa-search"></i>
                    <input type="text" class="ppn-search" id="ppn_search_fk" placeholder="Search invoices...">
                </span>
            </div>
        </div>
        <!-- Default TERTUTUP: tabel ini hanya untuk pengecekan, yang utama Preview Jurnal. -->
        <div class="ppn-tbl-wrap mb-3" id="ppn_fk_wrap" style="display:none;">
            <table id="tbl_ppn_faktur" class="table table-hover table-sm ppn-tbl">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jenis</th>
                        <th>Nama Penjual</th>
                        <th>Nomor Identitas WP</th>
                        <th>No Faktur</th>
                        <th>Tgl Faktur</th>
                        <th class="ppn-num" style="width:130px;">DPP</th>
                        <th class="ppn-num" style="width:130px;">DPP Nilai Lain</th>
                        <th class="ppn-num" style="width:130px;">PPN</th>
                        <th class="ppn-num" style="width:110px;">PPnBM</th>
                        <th>Faktur Diganti/Diretur</th>
                    </tr>
                </thead>
                <tbody><tr><td colspan="11" class="text-center text-muted">No data yet</td></tr></tbody>
            </table>
        </div>

        <!-- ============ PREVIEW JURNAL ============
             Urutan baris PERSIS seperti yang akan tersimpan: baris debit (rinci per
             faktur) dulu, lalu baris credit (SUM per supplier + no faktur). -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <label class="mb-0">
                <b>Journal Preview</b>
                <span class="ppn-count" id="ppn_count">0 rows</span>
            </label>
            <div class="ppn-tools">
                <span class="ppn-search-wrap">
                    <i class="fa fa-search"></i>
                    <input type="text" class="ppn-search" id="ppn_search" placeholder="Search supplier / invoice / COA...">
                </span>
            </div>
        </div>
        <div class="ppn-tbl-wrap mb-3">
            <table id="tbl_ppn_jurnal" class="table table-hover table-sm ppn-tbl" style="table-layout:fixed;width:100%;">
                <thead>
                    <tr>
                        <th style="width:110px;">Profit Center</th>
                        <th style="width:190px;">COA</th>
                        <th style="width:150px;">Reff Document</th>
                        <th style="width:95px;">Reff Date</th>
                        <th style="width:55px;">Curr</th>
                        <th class="ppn-num" style="width:125px;">Debit</th>
                        <th class="ppn-num" style="width:125px;">Credit</th>
                        <th style="width:200px;">Description</th>
                        <th style="width:150px;">Supplier</th>
                    </tr>
                </thead>
                <tbody><tr><td colspan="9" class="text-center text-muted">No data yet</td></tr></tbody>
            </table>
        </div>

        <!-- ============ TOTAL: per Profit Center + Grand Total ============ -->
        <div class="row mt-3">
            <?php foreach ($ppnPcList as $p) {
                $k = htmlspecialchars($p['kode_pc']);
                $tone = $ppnTone[$p['kode_pc']] ?? 'tone-all';
            ?>
            <div class="col-md-4 mb-2">
                <div class="ppn-tot <?php echo $tone; ?>">
                    <div class="ppn-tot-name"><?php echo htmlspecialchars($p['nama_pc']); ?></div>
                    <div class="ppn-tot-grid">
                        <div class="ppn-tot-item is-deb">
                            <div class="ppn-tot-lbl">Debit</div>
                            <div class="ppn-tot-val" id="ppn_deb_<?php echo $k; ?>">0.00</div>
                        </div>
                        <div class="ppn-tot-item is-cre">
                            <div class="ppn-tot-lbl">Credit</div>
                            <div class="ppn-tot-val" id="ppn_cre_<?php echo $k; ?>">0.00</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="col-md-4 mb-2">
                <div class="ppn-tot tone-all">
                    <div class="ppn-tot-name">Grand Total</div>
                    <div class="ppn-tot-grid">
                        <div class="ppn-tot-item is-deb">
                            <div class="ppn-tot-lbl">Debit</div>
                            <div class="ppn-tot-val" id="tot_debit_ppn">0.00</div>
                        </div>
                        <div class="ppn-tot-item is-cre">
                            <div class="ppn-tot-lbl">Credit</div>
                            <div class="ppn-tot-val" id="tot_credit_ppn">0.00</div>
                        </div>
                    </div>
                    <!-- baris sendiri: pesan "tidak balance" bisa panjang, jangan terpotong -->
                    <div id="balance_ppn" class="ppn-tot-bal"></div>
                </div>
            </div>
        </div>

        <!-- ============ AKSI ============ -->
        <div class="form-row">
            <div class="col-md-12 mt-2 mb-2">
                <div class="app-actions">
                    <button type="button" class="app-btn app-btn-primary" id="simpan_ppn"><i class="fa fa-floppy-o"></i> Save</button>
                    <button type="button" class="app-btn app-btn-danger" onclick="location.href='memorial-journal.php'"><i class="fa fa-angle-double-left"></i> Back</button>
                    <button type="button" class="app-btn app-btn-warning" id="reset_ppn"><i class="fa fa-repeat"></i> Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// PENTING: file ini di-include SEBELUM <script src="jquery"> di create_memorial_journal.php.
// Saat blok ini di-parse, $ / Swal BELUM ada — kalau langsung dieksekusi, ReferenceError
// bikin SELURUH handler gagal terpasang (gejala: pilih file tidak bereaksi, tombol diam).
// DOMContentLoaded itu API native dan baru jalan setelah semua <script> sinkron dimuat.
document.addEventListener('DOMContentLoaded', function () {
    // Peta kode PC -> id & nama, supaya kolom Profit Center bisa tampil lengkap
    // (PCP001 / NIRWANA ALABARE GARMENT) seperti kolom COA.
    var pcMap = <?php echo json_encode($ppnPcMap); ?>;

    // 0 ditulis "-" samar (gaya laporan keuangan) supaya tabel tidak penuh angka nol.
    var fmt = function (n) {
        n = parseFloat(n || 0);
        if (!n) { return '<span class="ppn-muted">-</span>'; }
        return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    var fmt0 = function (n) {
        return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    var esc = function (s) { return $('<div>').text(s === null || s === undefined ? '' : s).html(); };
    var dash = function (s) { return (s === null || s === undefined || s === '') ? '<span class="ppn-muted">-</span>' : esc(s); };

    // ---- ceklis Include: jurnal kembar di SB I --------------------------------
    $('#to_sb1_ppn').on('change', function () {
        $('#txt_sb1_ppn').text($(this).is(':checked') ? '*Jurnal akan terbentuk di SB I' : '');
    });

    // ---- pilih file: nama tampil + drag & drop --------------------------------
    $('#fileUploadPpn').off('change');
    $('#uploadBoxPpn').off('dragover dragleave drop');

    function showPpnFile(file) {
        // .has-file dipakai CSS utk menonjolkan nama berkas yang sudah dipilih.
        if (!file) { $('#fileNamePpn').removeClass('has-file').text('No file selected'); return; }
        $('#fileNamePpn').addClass('has-file').text(file.name);
        if (!/\.(xls|xlsx|csv)$/i.test(file.name)) {
            Swal.fire({ icon: 'warning', title: 'File format', text: 'Please use an Excel file (.xls / .xlsx) or .csv.' });
        }
    }
    $('#fileUploadPpn').on('change', function () { showPpnFile(this.files[0]); });

    // Sorotan saat file diseret: pakai class .is-drag (gaya ada di CSS), bukan
    // .css() langsung — supaya warnanya sekali diatur di satu tempat.
    $('#uploadBoxPpn').on('dragover', function (e) {
        e.preventDefault(); $(this).addClass('is-drag');
    }).on('dragleave', function (e) {
        e.preventDefault(); $(this).removeClass('is-drag');
    }).on('drop', function (e) {
        e.preventDefault(); $(this).removeClass('is-drag');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById('fileUploadPpn').files = files;
            showPpnFile(files[0]);
        }
    });

    // ---- render 1 tabel = daftar baris jurnal yang akan tersimpan -------------
    var ppnRows = [];
    var ppnFaktur = [];   // data faktur mentah, dipakai cek periode saat Save

    function pcCell(kode) {
        var m = pcMap[kode];
        if (!m) { return dash(kode); }
        return '<div class="ppn-coa-no">' + esc(m.id) + '</div>' +
               '<div class="ppn-coa-nm">' + esc(m.nama) + '</div>';
    }

    // Filter sederhana: cocokkan potongan teks di kolom-kolom yang ditentukan.
    // Data sudah ada di memori, jadi tidak perlu bolak-balik ke server.
    function ppnFilter(rows, q, fields) {
        q = $.trim(q || '').toLowerCase();
        if (!q) { return rows; }
        return rows.filter(function (r) {
            for (var i = 0; i < fields.length; i++) {
                var v = r[fields[i]];
                if (v !== null && v !== undefined && String(v).toLowerCase().indexOf(q) !== -1) { return true; }
            }
            return false;
        });
    }
    function ppnCountText(shown, total) {
        return (shown === total) ? (total + ' rows') : (shown + ' of ' + total + ' rows');
    }

    function renderPpnRows() {
        var rows = ppnFilter(ppnRows, $('#ppn_search').val(),
            ['profit_center', 'no_coa', 'nama_coa', 'no_faktur', 'tgl_faktur', 'keterangan', 'supplier']);
        $('#ppn_count').text(ppnCountText(rows.length, ppnRows.length));

        if (!rows.length) {
            $('#tbl_ppn_jurnal tbody').html('<tr><td colspan="9" class="text-center text-muted">' +
                (ppnRows.length ? 'No matching rows' : 'No data yet') + '</td></tr>');
            return;
        }
        var h = '', lastGrp = null;
        rows.forEach(function (r) {
            // Garis tebal tiap ganti pasangan (supplier + faktur) supaya blok
            // debit-credit miliknya kelihatan jelas.
            var sep = (lastGrp !== null && r.grp !== lastGrp) ? ' class="ppn-grp-start"' : '';
            lastGrp = r.grp;
            h += '<tr' + sep + '>' +
                 '<td>' + pcCell(r.profit_center) + '</td>' +
                 '<td><div class="ppn-coa-no">' + esc(r.no_coa) + '</div>' +
                     '<div class="ppn-coa-nm">' + esc(r.nama_coa) + '</div></td>' +
                 '<td>' + dash(r.no_faktur) + '</td>' +
                 '<td>' + dash(r.tgl_faktur) + '</td>' +
                 '<td><span class="ppn-badge-curr">IDR</span></td>' +
                 '<td class="ppn-num ppn-deb">' + fmt(r.debit) + '</td>' +
                 '<td class="ppn-num ppn-cre">' + fmt(r.credit) + '</td>' +
                 '<td>' + dash(r.keterangan) + '</td>' +
                 '<td>' + dash(r.supplier) + '</td>' +
                 '</tr>';
        });
        $('#tbl_ppn_jurnal tbody').html(h);
    }

    // ---- buka/tutup tabel "Preview Faktur" (default TERTUTUP) ----------------
    $('#ppn_fk_toggle').on('click', function () {
        var buka = $('#ppn_fk_wrap').is(':visible');   // true = sedang terbuka -> akan ditutup
        $('#ppn_fk_wrap').slideToggle(150);
        $('#ppn_fk_ico').toggleClass('fa-chevron-right', buka).toggleClass('fa-chevron-down', !buka);
        // Kotak search ikut tampil/sembunyi bersama tabelnya — kalau tabel tertutup,
        // search-nya tidak ada gunanya dan cuma bikin header ramai. Saat ditutup
        // filternya dikosongkan supaya hitungan barisnya kembali utuh.
        if (buka) {
            $('#ppn_search_fk_wrap').hide(); $('#ppn_search_fk').val('');
            renderPpnFaktur();
        } else {
            $('#ppn_search_fk_wrap').show();
        }
    });

    // ---- render tabel "Preview Faktur" (isi file apa adanya) -----------------
    // Negatif (nota retur) ditulis merah dalam kurung, seperti di file aslinya.
    function fmtRaw(n) {
        n = parseFloat(n || 0);
        if (!n) { return '<span class="ppn-muted">-</span>'; }
        var s = Math.abs(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return n < 0 ? '<span style="color:#c00000">(' + s + ')</span>' : s;
    }

    function renderPpnFaktur() {
        var rows = ppnFilter(ppnFaktur, $('#ppn_search_fk').val(),
            ['bulan', 'jenis', 'nama_penjual', 'npwp', 'no_faktur', 'tgl_faktur', 'faktur_diganti']);
        $('#ppn_count_fk').text(ppnCountText(rows.length, ppnFaktur.length));

        if (!rows.length) {
            $('#tbl_ppn_faktur tbody').html('<tr><td colspan="11" class="text-center text-muted">' +
                (ppnFaktur.length ? 'No matching rows' : 'No data yet') + '</td></tr>');
            return;
        }
        var h = '';
        rows.forEach(function (r) {
            h += '<tr>' +
                 '<td>' + dash(r.bulan) + '</td>' +
                 '<td>' + dash(r.jenis) + '</td>' +
                 '<td>' + dash(r.nama_penjual) + '</td>' +
                 '<td>' + dash(r.npwp) + '</td>' +
                 '<td>' + dash(r.no_faktur) + '</td>' +
                 '<td>' + dash(r.tgl_faktur) + '</td>' +
                 '<td class="ppn-num">' + fmtRaw(r.dpp) + '</td>' +
                 '<td class="ppn-num">' + fmtRaw(r.dpp_nilai_lain) + '</td>' +
                 '<td class="ppn-num">' + fmtRaw(r.ppn) + '</td>' +
                 '<td class="ppn-num">' + fmtRaw(r.ppnbm) + '</td>' +
                 '<td>' + dash(r.faktur_diganti) + '</td>' +
                 '</tr>';
        });
        $('#tbl_ppn_faktur tbody').html(h);
    }

    // ---- kotak search kedua tabel --------------------------------------------
    // Debounce 180ms: dgn ribuan baris, render ulang tiap ketikan terasa berat.
    function ppnDebounce(fn) {
        var t;
        return function () { clearTimeout(t); t = setTimeout(fn, 180); };
    }
    $('#ppn_search').on('input', ppnDebounce(renderPpnRows));
    $('#ppn_search_fk').on('input', ppnDebounce(renderPpnFaktur));

    // ---- muat preview (angka dari helper server yg sama dgn Save) -------------
    function loadPpn() {
        $.getJSON('memorial_journal/ajx_get_data_ppn.php', function (res) {
            if (!res || res.status !== 'success') { return; }

            // Tabel atas: isi file apa adanya.
            ppnFaktur = res.faktur || [];
            renderPpnFaktur();

            // Urutan dari helper: per (supplier + faktur) baris debit lalu credit
            // pasangannya — SAMA persis dgn urutan yang ditulis save_mj_ppn.php.
            ppnRows = res.lines || (res.detail || []).concat(res.grouped || []);
            renderPpnRows();

            // Total per profit center — nolkan dulu semua, lalu isi yang ada.
            $('[id^=ppn_deb_], [id^=ppn_cre_]').text('0.00');
            (res.per_pc || []).forEach(function (p) {
                $('#ppn_deb_' + p.profit_center).text(fmt0(p.debit));
                $('#ppn_cre_' + p.profit_center).text(fmt0(p.credit));
            });

            $('#tot_debit_ppn').text(fmt0(res.tot_debit));
            $('#tot_credit_ppn').text(fmt0(res.tot_credit));
            $('#balance_ppn').html(res.balance
                ? '<span style="color:#127a12">&#10004; Balance</span>'
                : '<span style="color:#c00000">&#10007; TIDAK balance &mdash; selisih ' + fmt0((res.tot_debit || 0) - (res.tot_credit || 0)) + '</span>');
        });
    }
    window.ppnLoadPreview = loadPpn;

    // ---- upload ---------------------------------------------------------------
    $('#btnUploadPpn').on('click', function () {
        var f = $('#fileUploadPpn')[0].files[0];
        if (!f) { Swal.fire({ icon: 'warning', title: 'Warning', text: 'Please choose a file first.' }); return; }

        var fd = new FormData();
        fd.append('file', f);

        Swal.fire({ title: 'Uploading...', html: 'Processing file, please wait...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

        $.ajax({
            url: 'memorial_journal/proses_upload_ppn.php', type: 'POST', data: fd,
            processData: false, contentType: false, dataType: 'json',
            success: function (res) {
                Swal.close();
                if (res && res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Success', text: res.rows + ' invoice rows loaded.' });
                    loadPpn();
                } else {
                    Swal.fire('Error', (res && res.message) ? res.message : 'Failed to process the file.', 'error');
                }
            },
            error: function () { Swal.close(); Swal.fire('Error', 'File upload failed.', 'error'); }
        });
    });

    // ---- simpan ---------------------------------------------------------------
    $('#simpan_ppn').on('click', function () {
        var btn = $(this);
        // Type wajib dipilih user (tidak ada default) — cegat di klien dulu.
        if (!$('#id_cmj_ppn').val()) {
            Swal.fire({ icon: 'warning', title: 'Warning', text: 'Please select a Type first.' });
            return;
        }
        // Urutan pemeriksaan (semua hanya MEMPERINGATKAN, user tetap boleh lanjut):
        //   1. periode tgl faktur vs tgl GM  (cek di klien, instan)
        //   2. faktur pernah masuk jurnal GM (cek ke server)
        //   3. konfirmasi simpan
        cekPeriodePpn(function () {
            cekDuplikatPpn(btn);
        });
    });

    // 1) Bulan/tahun tgl faktur beda dgn tgl GM -> kemungkinan salah pilih tanggal.
    function cekPeriodePpn(lanjut) {
        var g = ($('#mj_date_ppn').val() || '').split('-');   // dd-mm-yyyy
        if (g.length !== 3) { lanjut(); return; }
        var gmKey = g[2] + '-' + g[1];                        // yyyy-mm

        var beda = {};
        (ppnFaktur || []).forEach(function (f) {
            var t = String(f.tgl_faktur || '').substring(0, 7);   // yyyy-mm
            if (t && t !== gmKey) { beda[t] = (beda[t] || 0) + 1; }
        });
        var keys = Object.keys(beda).sort();
        if (!keys.length) { lanjut(); return; }

        var h = '<div style="text-align:left;font-size:13px">';
        h += 'Journal date period is <b>' + periodeLabel(gmKey) + '</b>, but some invoice dates fall in a different period:';
        h += '<ul style="margin:8px 0 0 18px;padding:0">';
        keys.forEach(function (k) {
            h += '<li><b>' + periodeLabel(k) + '</b> — ' + beda[k] + ' invoice(s)</li>';
        });
        h += '</ul><div class="mt-2 text-muted">Please make sure the Date field is correct.</div></div>';

        Swal.fire({
            icon: 'warning', title: 'Invoice period differs from journal date', html: h, width: 560,
            showCancelButton: true, confirmButtonText: 'Yes, save anyway', cancelButtonText: 'Cancel'
        }).then(function (ok) { if (ok.isConfirmed) { lanjut(); } });
    }

    // 'yyyy-mm' -> 'Jan 2026'
    function periodeLabel(k) {
        var nm = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var p = k.split('-'), i = parseInt(p[1], 10) - 1;
        return (nm[i] || p[1]) + ' ' + p[0];
    }

    // 2) Faktur yang pernah masuk jurnal GM (yang belum di-cancel).
    function cekDuplikatPpn(btn) {
        Swal.fire({
            title: 'Checking invoices...', allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); }
        });
        $.getJSON('memorial_journal/ajx_cek_faktur_ppn.php', function (res) {
            Swal.close();
            if (res && res.status === 'success' && res.total > 0) {
                peringatanDuplikat(btn, res.dupe);
            } else {
                konfirmasiSimpanPpn(btn);
            }
        }).fail(function () {
            // Gagal cek jangan menghalangi user — lanjut ke konfirmasi biasa.
            Swal.close();
            konfirmasiSimpanPpn(btn);
        });
    }

    // Peringatan faktur duplikat. Hanya MEMPERINGATKAN — user boleh tetap simpan.
    function peringatanDuplikat(btn, dupe) {
        var maks = 15, h = '';
        h += '<div style="text-align:left;font-size:13px">';
        h += '<b>' + dupe.length + '</b> invoice number(s) below have already been posted to a GM journal:';
        h += '<div style="max-height:220px;overflow:auto;margin-top:8px;border:1px solid #e2e8f0;border-radius:8px">';
        h += '<table class="table table-sm mb-0" style="font-size:12px">';
        h += '<thead><tr><th>Invoice No</th><th>Journal No</th><th>Date</th></tr></thead><tbody>';
        dupe.slice(0, maks).forEach(function (d) {
            h += '<tr><td>' + esc(d.no_faktur) + '</td><td>' + esc(d.gm) + '</td><td>' + esc(d.tgl) + '</td></tr>';
        });
        h += '</tbody></table></div>';
        if (dupe.length > maks) { h += '<div class="text-muted mt-1">…and ' + (dupe.length - maks) + ' more invoice(s).</div>'; }
        h += '<div class="mt-2 text-muted">Cancelled GM journals are not counted.</div>';
        h += '</div>';

        Swal.fire({
            icon: 'warning', title: 'Invoice already posted', html: h, width: 640,
            showCancelButton: true, confirmButtonText: 'Yes, save anyway', cancelButtonText: 'Cancel'
        }).then(function (ok) {
            if (ok.isConfirmed) { konfirmasiSimpanPpn(btn); }
        });
    }

    function konfirmasiSimpanPpn(btn) {
        var h = '<b>' + ppnRows.length + '</b> journal rows will be saved.';
        if ($('#to_sb1_ppn').is(':checked')) {
            h += '<br><span style="color:#c00000">A journal will also be created in <b>SB I</b>.</span>';
        }
        Swal.fire({
            title: 'Save PPN Masukan journal?', icon: 'question', html: h,
            showCancelButton: true, confirmButtonText: 'Yes, save', cancelButtonText: 'Cancel'
        }).then(function (ok) {
            if (ok.isConfirmed) { kirimSimpanPpn(btn); }
        });
    }

    // Dipisah jadi fungsi supaya bisa dipanggil ulang lewat tombol "Coba Lagi".
    // Aman diulang: kalau gagal, server rollback total dan data upload masih
    // berstatus 'Temp' (belum terpakai). Kalau ternyata SUDAH tersimpan, percobaan
    // berikutnya otomatis ditolak dgn "Tidak ada data upload untuk disimpan"
    // — jadi tidak mungkin dobel jurnal.
    function kirimSimpanPpn(btn) {
        btn.prop('disabled', true);
        Swal.fire({
            title: 'Saving...', html: 'Saving ' + ppnRows.length + ' journal rows. Please wait.',
            allowOutsideClick: false, allowEscapeKey: false, didOpen: function () { Swal.showLoading(); }
        });

        $.ajax({
            url: 'memorial_journal/save_mj_ppn.php', type: 'POST', dataType: 'json',
            data: {
                mj_date: $('#mj_date_ppn').val(), id_cmj: $('#id_cmj_ppn').val(),
                to_sb1: $('#to_sb1_ppn').is(':checked') ? '1' : '0'
            },
            success: function (res) {
                btn.prop('disabled', false);
                Swal.close();
                if (res && res.status === 'success') {
                    var msg = '<div style="text-align:left;font-size:13.5px">' +
                              'No Journal (GM): <b>' + esc(res.no_mj) + '</b>';
                    // Nomor SB I ditampilkan jelas kalau ceklis Include dipakai.
                    if (res.no_mj_sb) {
                        msg += '<br>No Journal <b>SB I</b>: <b style="color:#127a12">' + esc(res.no_mj_sb) + '</b>';
                    }
                    msg += '<br><span class="text-muted">' + res.baris + ' journal rows saved.</span></div>';
                    Swal.fire({ icon: 'success', title: 'Saved', html: msg });
                    $('#fileUploadPpn').val(''); $('#fileNamePpn').removeClass('has-file').text('No file selected');
                    loadPpn();
                } else {
                    gagalSimpanPpn(btn, (res && res.message) ? res.message : 'Failed to save.');
                }
            },
            error: function () {
                btn.prop('disabled', false);
                Swal.close();
                gagalSimpanPpn(btn, 'Connection lost or the process took too long.');
            }
        });
    }

    function gagalSimpanPpn(btn, pesan) {
        Swal.fire({
            icon: 'error', title: 'Save failed',
            html: pesan + '<br><br><small>Nothing was saved (fully rolled back). Your uploaded data is intact, so you can retry right away.</small>',
            showCancelButton: true, confirmButtonText: 'Try Again', cancelButtonText: 'Close'
        }).then(function (ok) {
            if (ok.isConfirmed) { kirimSimpanPpn(btn); }
        });
    }

    $('#reset_ppn').on('click', function () {
        $('#fileUploadPpn').val(''); $('#fileNamePpn').text('No file selected');
        $('#to_sb1_ppn').prop('checked', false); $('#txt_sb1_ppn').text('');
        $('#ppn_search, #ppn_search_fk').val('');
        renderPpnFaktur();

        ppnRows = [];
        renderPpnRows();
        $('[id^=ppn_deb_], [id^=ppn_cre_]').text('0.00');
        $('#tot_debit_ppn').text('0.00'); $('#tot_credit_ppn').text('0.00'); $('#balance_ppn').html('');
    });

    // DOM sudah siap di titik ini, jadi langsung dijalankan.
    // startDate: tanggal di periode yang sudah closing tidak bisa dipilih.
    $('#mj_date_ppn').datepicker({
        format: 'dd-mm-yyyy', autoclose: true
        <?php if ($ppnMinDp) { ?>, startDate: '<?php echo $ppnMinDp; ?>'<?php } ?>
    });
    loadPpn();
});
</script>
