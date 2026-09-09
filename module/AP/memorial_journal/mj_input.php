<style>
/* ============================================================================
   Skin tab MANUAL JOURNAL ENTRY — sejalan dengan tab HRIS, Upload & PPN Masukan.
   Prefiks .mji- (Memorial Journal Input). WAJIB berbeda dari .mjh- (HRIS),
   .mju- (Upload) dan .ppn- (PPN Masukan): keempat tab di-include ke HALAMAN YANG
   SAMA, jadi nama kelas yang sama akan saling menimpa antar tab.

   BEDA PENTING dari tab lain: tabel di sini BUKAN DataTables, melainkan grid
   yang bisa disunting — tiap barisnya berisi selectpicker (bootstrap-select) dan
   input yang dibuat oleh addRow() di halaman induk. Karena itu:
     - TIDAK ada max-height / kotak scroll baru: wadah ber-overflow akan memotong
       menu dropdown selectpicker. Pembungkus .table-responsive yang sudah ada
       DIPERTAHANKAN apa adanya supaya perilakunya tidak berubah dari sekarang.
     - TIDAK ada text-overflow:ellipsis pada sel: isinya kontrol form, bukan teks.
     - TIDAK ada sticky header: menu dropdown yang terbuka bisa tertutup olehnya.
   ============================================================================ */

/* -------- Panel kontrol atas -------- */
.mji-head {
    border: 1px solid #e6ebf3; border-radius: 12px; background: #fff;
    padding: 14px 16px 4px; margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
}
.mji-flabel {
    display: block; margin-bottom: 5px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #64748b;
}
/* Kotak setinggi input, dipakai kontrol non-input (ceklis) supaya sebaris rapi
   dengan field di sebelahnya — tanpa ini ceklis menggantung di tengah kolom. */
.mji-field {
    display: flex; align-items: center; gap: 9px;
    border: 1px solid #dbe4f3; border-radius: 8px; background: #fff;
    padding: 0 12px; min-height: 38px;
}
.mji-field label { margin: 0; font-size: 13px; color: #334155; cursor: pointer; font-weight: 500; }
/* Pesan "*Jurnal akan terbentuk di SB I" — tetap <input disabled> karena JS induk
   mengisinya lewat .val(); border/latar dilucuti supaya terbaca sebagai teks. */
input.mji-sb1-note {
    border: 0; background: transparent; padding: 0; margin: 0; height: auto;
    font-size: 11px; font-style: italic; font-weight: 700; color: #c00000;
    width: 100%; box-shadow: none; outline: none;
}

/* -------- Judul seksi -------- */
.mji-sec-head {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin: 0 0 8px;
}
.mji-sec-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; }
.mji-sec-sub { font-size: 12px; color: #94a3b8; }

/* -------- Grid baris jurnal --------
   Pembungkus .table-responsive milik Bootstrap DIBIARKAN; kelas di bawah hanya
   memberi bingkai & sudut membulat supaya senada dengan tab lain.
   Catatan lebar: halaman induk memaksa #mytablenone .form-control dan
   .bootstrap-select ke width:100%!important, sehingga kontrol MEREGANG
   mengikuti sel. Ke-15 kolom (13 semula + No Faktur/Faktur Date) sudah lebih
   lebar dari layar umum sehingga akan menggulir mendatar - scrollbar
   ditipiskan untuk itu. */
.mji-grid-wrap {
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.05); padding: 0;
    scrollbar-width: thin; scrollbar-color: #c7d2e0 transparent;
}
.mji-grid-wrap::-webkit-scrollbar { width: 9px; height: 9px; }
.mji-grid-wrap::-webkit-scrollbar-track { background: transparent; }
.mji-grid-wrap::-webkit-scrollbar-thumb { background: #c7d2e0; border-radius: 8px; border: 2px solid #fff; }
.mji-grid-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

/* table-layout:fixed WAJIB di sini. Dengan layout auto (bawaan), lebar kolom
   ditentukan oleh KONTEN TERLEBAR di kolom itu - satu baris dengan COA/Cost
   Center bernama panjang (mis. "5.61.02 BEBAN KONSUMSI TENAGA KERJA TIDAK
   LANGSUNG") memaksa kolom itu melebar jauh melebihi min-width yang diset di
   bawah, dan kolom lain (Reference, No Faktur, dst) ikut terpepet sampai
   nyaris tak terlihat. Dengan fixed, lebar kolom HANYA ditentukan oleh nilai
   width pada th baris pertama - konten yang lebih lebar dipotong (elipsis),
   tidak lagi mencuri ruang kolom lain. */
.mji-grid { margin-bottom: 0; border: 0; table-layout: fixed; }
/* Gradien dipasang di BARIS (tr), bukan tiap sel — kalau di th, tiap kolom
   menggambar gradiennya sendiri sehingga header terlihat berpita. */
.mji-grid thead tr { background: linear-gradient(90deg, #1E3A8A, #2f5bbf); }
.mji-grid thead th {
    background: transparent !important;
    color: #fff; border: 0; border-bottom: 2px solid #16306e;
    font-size: 11px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase;
    white-space: nowrap; vertical-align: middle; padding: 9px 10px;
}
.mji-grid tbody td {
    vertical-align: middle; padding: 7px 8px;
    border-top: 0; border-bottom: 1px solid #eef2f7; background: #fff;
}
.mji-grid tbody tr:hover td { background: #f8fbff; }

/* LEBAR TETAP PER KOLOM (width + min-width, dipasangkan dengan table-layout:fixed
   di atas). Riwayat masalah yang diperbaiki lewat width tetap ini:
   1) Halaman induk memaksa #mytablenone .form-control & .bootstrap-select ke
      width:100%!important, sehingga kontrol tidak lagi menahan lebar alaminya
      dan (tanpa aturan ini) kolom bisa kolaps sampai teks dropdown menumpuk
      vertikal (N/T/1.).
   2) Sebaliknya, opsi terpilih yang teksnya panjang (mis. nama COA/Cost
      Center) bisa memaksa kolom itu melebar jauh melewati batas dan
      memepetkan kolom lain sampai nyaris tak terlihat — ini HANYA teratasi
      dengan table-layout:fixed; min-width saja (tanpa fixed) tidak cukup
      karena table-layout:auto tetap membiarkan konten terlebar menang.
   width & min-width dipasang sama besar di th DAN td: th mengunci lebar
   kolom, td menjaga baris baru dari addRow()/InsertRow(). Konsekuensi
   table-layout:fixed: kolom TIDAK lagi melar mengisi layar lebar — tabel
   selalu selebar total kolom, kelebihannya digulir lewat .mji-grid-wrap. */
.mji-grid th:nth-child(1),  .mji-grid td:nth-child(1)  { width: 34px;  min-width: 34px; }
.mji-grid th:nth-child(2),  .mji-grid td:nth-child(2)  { width: 210px; min-width: 210px; }
.mji-grid th:nth-child(3),  .mji-grid td:nth-child(3)  { width: 185px; min-width: 185px; }
.mji-grid th:nth-child(4),  .mji-grid td:nth-child(4)  { width: 165px; min-width: 165px; }
.mji-grid th:nth-child(5),  .mji-grid td:nth-child(5)  { width: 120px; min-width: 120px; }
.mji-grid th:nth-child(6),  .mji-grid td:nth-child(6)  { width: 125px; min-width: 125px; }
.mji-grid th:nth-child(7),  .mji-grid td:nth-child(7)  { width: 140px; min-width: 140px; } /* No Faktur */
.mji-grid th:nth-child(8),  .mji-grid td:nth-child(8)  { width: 125px; min-width: 125px; } /* Faktur Date */
.mji-grid th:nth-child(9),  .mji-grid td:nth-child(9)  { width: 135px; min-width: 135px; } /* Buyer */
.mji-grid th:nth-child(10), .mji-grid td:nth-child(10) { width: 120px; min-width: 120px; }
.mji-grid th:nth-child(11), .mji-grid td:nth-child(11) { width: 95px;  min-width: 95px; }
.mji-grid th:nth-child(12), .mji-grid td:nth-child(12) { width: 120px; min-width: 120px; }
.mji-grid th:nth-child(13), .mji-grid td:nth-child(13) { width: 120px; min-width: 120px; }
.mji-grid th:nth-child(14), .mji-grid td:nth-child(14) { width: 160px; min-width: 160px; }
.mji-grid th:nth-child(15), .mji-grid td:nth-child(15) { width: 44px;  min-width: 44px; }
/* Teks dalam dropdown jangan dibungkus ke baris baru — itu yang membuat
   kolom sempit terlihat seperti tumpukan huruf. */
.mji-grid .bootstrap-select .filter-option-inner-inner,
.mji-grid .bootstrap-select .dropdown-toggle { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
/* PENTING: JANGAN pasang overflow:hidden pada <td> di grid ini. Menu dropdown
   & kotak pencarian selectpicker dirender SEBAGAI ANAK di dalam <td> yang sama
   (bootstrap-select membungkus <select> di tempat, .dropdown-menu adalah
   saudara di dalam wrapper itu) — overflow:hidden pada <td> memotongnya
   sebelum sempat terlihat, dan perbaikan show.bs.dropdown/hide.bs.dropdown di
   create_memorial_journal.php hanya menangani overflow milik .table-responsive
   (leluhur lebih atas), bukan <td> ini. Percobaan sebelumnya memasang aturan
   ini untuk mencegah konten meluber ke sel sebelah, tapi itu TIDAK PERLU:
   table-layout:fixed + width eksplisit di atas sudah mengunci lebar kolom, dan
   .filter-option milik bootstrap-select sendiri SUDAH punya overflow:hidden
   bawaan untuk memotong teks tombol. */

/* MENU DROPDOWN BOLEH LEBIH LEBAR DARI TOMBOLNYA.
   app-skin-form.css mengunci .bootstrap-select > .dropdown-menu ke
   width/min-width/max-width:100%!important, artinya selebar tombol. Di grid
   ini tombolnya cuma selebar kolom (~200px), sehingga nama COA panjang
   terbungkus beberapa baris dan kotak pencariannya ikut terjepit sampai tak
   terlihat. Di sini kuncinya dilepas: menu memakai lebar isinya, dengan
   batas atas supaya tidak melebar berlebihan. */
.mji-grid .bootstrap-select > .dropdown-menu {
    width: auto !important; min-width: 320px !important; max-width: 460px !important;
}
/* Kotak pencarian dipastikan tampil & selebar menu. #mytablenone .form-control
   dari halaman induk memaksa width:100%!important ke SEMUA .form-control,
   termasuk input pencarian ini — jadi ukurannya ditegaskan ulang di sini. */
.mji-grid .bootstrap-select .bs-searchbox { display: block !important; padding: 4px 6px 8px; }
.mji-grid .bootstrap-select .bs-searchbox input.form-control {
    display: block !important; width: 100% !important; height: auto !important;
    padding: 7px 12px !important; font-size: 13px;
}
/* Item menu boleh membungkus di menu yang kini lebih lebar — elipsis hanya
   untuk TOMBOLnya (di atas), bukan untuk daftar pilihannya. */
.mji-grid .bootstrap-select .dropdown-menu li a { white-space: normal; }
.mji-rowtools { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }

/* -------- Kartu total -------- */
.mji-tot {
    border: 1px solid #e6ebf3; border-left: 4px solid #cbd5e1; border-radius: 10px;
    background: #fff; box-shadow: 0 1px 3px rgba(15,23,42,.05);
    padding: 12px 14px 13px; height: 100%;
}
.mji-tot.tone-nag { border-left-color: #5b7ba8; }
.mji-tot.tone-nak { border-left-color: #4f8a6b; }
.mji-tot.tone-all { border-left-color: #4a5578; background: #f8fafc; }
.mji-tot-name {
    font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    color: #475569; padding-bottom: 6px; border-bottom: 1px solid #eef2f7; margin-bottom: 2px;
}
.mji-tot-code {
    background: #eef2f7; color: #475569; border-radius: 4px; padding: 0 5px;
    margin-left: 6px; font-size: 9.5px; letter-spacing: .4px;
}
/* Debit & Credit BERSEBELAHAN. Tiap kolom menampung DUA baris, diisi menurun
   lewat grid-auto-flow:column — mata uang asli di atas (kecil), IDR di bawah
   (besar & tebal), mengikuti keputusan tata letak di tab Upload Journal. */
.mji-tot-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    grid-auto-flow: column;
    column-gap: 14px; row-gap: 3px; margin-top: 7px;
}
.mji-tot-col { display: flex; flex-direction: column; min-width: 0; }
.mji-tot-col.is-cre { border-left: 1px solid #eef2f7; padding-left: 14px; }
.mji-tot-lbl {
    font-size: 10.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; margin-bottom: 1px; cursor: help;
}
.mji-tot-col.is-deb .mji-tot-lbl { color: #1d4ed8; }
.mji-tot-col.is-cre .mji-tot-lbl { color: #b45309; }
/* Baris atas (mata uang asli) jadi pendukung; penekanan ada di baris IDR. */
.mji-tot-col.is-sub .mji-tot-lbl { color: #94a3b8; font-size: 9.5px; letter-spacing: .4px; }
/* Tetap <input readonly>: JS induk mengisinya lewat .val(), yang TIDAK bekerja
   pada <span>/<div>. tabindex=-1 supaya tidak jadi perhentian Tab.
   Warna JANGAN di-!important — induk mewarnainya inline saat tidak balance. */
input.mji-tot-val {
    display: block; width: 100%; min-width: 0;
    margin: 0; padding: 0; border: 0; background: transparent; box-shadow: none;
    font-size: 17px; font-weight: 700; color: #0f172a;
    font-variant-numeric: tabular-nums; line-height: 1.25; text-align: left;
}
input.mji-tot-val.is-sub { font-size: 12px; font-weight: 600; color: #94a3b8; }
input.mji-tot-val:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
@media (max-width: 991.98px) {
    .mji-tot-split { grid-template-columns: 1fr; grid-auto-flow: row; }
    .mji-tot-col.is-cre { border-left: 0; padding-left: 0; margin-top: 8px; }
}

.mji-actions { display: flex; gap: 8px; flex-wrap: wrap; }
</style>

<form id="form-data" method="post">
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- ============ Panel kontrol ============ -->
            <div class="mji-head">
                <div class="form-row">

                    <div class="col-md-2 mb-3">
                        <label class="mji-flabel" for="mj_date">Date</label>
                        <input type="text" name="mj_date" id="mj_date" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="getRate()">
                        <input type="hidden" class="form-control angka" id="rate_mj" name="rate_mj">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mji-flabel" for="mj_type">Type</label>
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

                    <div class="col-md-3 mb-3">
                        <label class="mji-flabel" for="profit_center">Profit Center</label>
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

                    <div class="col-md-4 mb-3">
                        <label class="mji-flabel" for="to_sb1">SB I</label>
                        <div class="mji-field">
                            <input type="checkbox" class="checkbox_sb1" id="to_sb1" name="to_sb1" value="1">
                            <label for="to_sb1">Include</label>
                            <input type="text" class="mji-sb1-note" id="txt_sb1" name="txt_sb1" value="" disabled>
                            <input type="hidden" id="fil_sb1" name="fil_sb1" value="">
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="mji-flabel" for="pesan">Description</label>
                        <textarea class="form-control" rows="2" name="pesan" id="pesan" placeholder="descriptions..." required></textarea>
                    </div>

                </div>
            </div>

            <!-- ============ Baris jurnal (grid manual) ============ -->
            <div class="mji-sec-head">
                <h6 class="mji-sec-title">Journal Lines</h6>
                <!-- <span class="mji-sec-sub">isi tiap baris, lalu pastikan total debit = total credit</span> -->
            </div>

            <!-- .table-responsive DIPERTAHANKAN: menghapusnya membuat grid meluber
                 keluar kartu, menggantinya dengan wadah lain berisiko memotong
                 menu dropdown selectpicker di dalam baris. -->
            <div class="table-responsive mji-grid-wrap">
                <table id="mytablenone" class="table table-hover table-sm nowrap mji-grid">

                    <thead>
                        <tr>
                            <th style="width:10px;">-</th>
                            <th>Coa</th>
                            <th>Profit Center</th>
                            <th>Cost Center</th>
                            <th>Reference</th>
                            <th>Reference Date</th>
                            <th>No Faktur</th>
                            <th>Faktur Date</th>
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


                </table>
            </div>

            <div class="mji-rowtools">
                <button type="button" class="app-btn app-btn-primary app-btn-sm" onclick="addRow('tbody3')">
                    <i class="fa fa-plus"></i> Add Row
                </button>
                <button type="button" class="app-btn app-btn-warning app-btn-sm" onclick="InsertRow('tbody3')">
                    <i class="fa fa-level-down"></i> Insert Row
                </button>
                <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="deleteRow('tbody3')">
                    <i class="fa fa-trash"></i> Delete Row
                </button>
            </div>

            <!-- ============ Ringkasan saldo ============ -->
            <div class="mji-sec-head mt-4">
                <h6 class="mji-sec-title">Balance Summary</h6>
            </div>

            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="mji-tot tone-nag">
                        <div class="mji-tot-name">PT. Nirwana Alabare Garment<span class="mji-tot-code">NAG</span></div>
                        <div class="mji-tot-split">
                            <div class="mji-tot-col is-deb is-sub">
                                <span class="mji-tot-lbl" title="Total debit persis seperti diinput — mata uang TIDAK dikonversi">Debit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_debit_nag" name="tot_debit_nag" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-deb">
                                <span class="mji-tot-lbl" title="Total debit setelah dikonversi ke IDR memakai kurs tanggal jurnal">Debit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_debit_idr_nag" name="tot_debit_idr_nag" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre is-sub">
                                <span class="mji-tot-lbl" title="Total credit persis seperti diinput — mata uang TIDAK dikonversi">Credit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_credit_nag" name="tot_credit_nag" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre">
                                <span class="mji-tot-lbl" title="Total credit setelah dikonversi ke IDR memakai kurs tanggal jurnal">Credit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_credit_idr_nag" name="tot_credit_idr_nag" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <!-- Nilai mentah (tanpa pemisah ribuan) — diisi JS induk,
                             ikut terkirim saat Save lewat form #form-data. -->
                        <input type="hidden" id="h_tot_debit_nag" name="h_tot_debit_nag">
                        <input type="hidden" id="h_tot_credit_nag" name="h_tot_credit_nag">
                        <input type="hidden" id="h_tot_debit_idr_nag" name="h_tot_debit_idr_nag">
                        <input type="hidden" id="h_tot_credit_idr_nag" name="h_tot_credit_idr_nag">
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="mji-tot tone-nak">
                        <div class="mji-tot-name">PT. Nirwana Alabare Knitting<span class="mji-tot-code">NAK</span></div>
                        <div class="mji-tot-split">
                            <div class="mji-tot-col is-deb is-sub">
                                <span class="mji-tot-lbl" title="Total debit persis seperti diinput — mata uang TIDAK dikonversi">Debit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_debit_nak" name="tot_debit_nak" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-deb">
                                <span class="mji-tot-lbl" title="Total debit setelah dikonversi ke IDR memakai kurs tanggal jurnal">Debit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_debit_idr_nak" name="tot_debit_idr_nak" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre is-sub">
                                <span class="mji-tot-lbl" title="Total credit persis seperti diinput — mata uang TIDAK dikonversi">Credit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_credit_nak" name="tot_credit_nak" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre">
                                <span class="mji-tot-lbl" title="Total credit setelah dikonversi ke IDR memakai kurs tanggal jurnal">Credit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_credit_idr_nak" name="tot_credit_idr_nak" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <input type="hidden" id="h_tot_debit_nak" name="h_tot_debit_nak">
                        <input type="hidden" id="h_tot_credit_nak" name="h_tot_credit_nak">
                        <input type="hidden" id="h_tot_debit_idr_nak" name="h_tot_debit_idr_nak">
                        <input type="hidden" id="h_tot_credit_idr_nak" name="h_tot_credit_idr_nak">
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="mji-tot tone-all">
                        <div class="mji-tot-name">Grand Total<span class="mji-tot-code">NAG + NAK</span></div>
                        <div class="mji-tot-split">
                            <div class="mji-tot-col is-deb is-sub">
                                <span class="mji-tot-lbl" title="Total debit seluruh profit center — mata uang TIDAK dikonversi">Debit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_debit" name="tot_debit" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-deb">
                                <span class="mji-tot-lbl" title="Total debit seluruh profit center dalam IDR">Debit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_debit_idr" name="tot_debit_idr" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre is-sub">
                                <span class="mji-tot-lbl" title="Total credit seluruh profit center — mata uang TIDAK dikonversi">Credit</span>
                                <input type="text" class="mji-tot-val is-sub" id="tot_credit" name="tot_credit" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mji-tot-col is-cre">
                                <span class="mji-tot-lbl" title="Total credit seluruh profit center dalam IDR">Credit IDR</span>
                                <input type="text" class="mji-tot-val" id="tot_credit_idr" name="tot_credit_idr" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <input type="hidden" id="h_tot_debit" name="h_tot_debit">
                        <input type="hidden" id="h_tot_credit" name="h_tot_credit">
                        <input type="hidden" id="h_tot_debit_idr" name="h_tot_debit_idr">
                        <input type="hidden" id="h_tot_credit_idr" name="h_tot_credit_idr">
                    </div>
                </div>
            </div>

            <div class="mji-actions mt-3 mb-2">
                <button type="button" class="app-btn app-btn-primary" name="simpan" id="simpan"><i class="fa fa-floppy-o"></i> Save</button>
                <button type="button" name="batal" id="batal" class="app-btn app-btn-danger" onclick="location.href='memorial-journal.php'"><i class="fa fa-angle-double-left"></i> Back</button>
            </div>

        </div>
    </div>
</form>
