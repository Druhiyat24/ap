<style>
/* ============================================================================
   Skin tab JOURNAL FROM HRIS — sejalan dengan tab Upload Journal & PPN Masukan.
   Prefiks .mjh- (Memorial Journal HRIS). WAJIB berbeda dari .mju- (Upload) dan
   .ppn- (PPN Masukan): keempat tab di-include ke HALAMAN YANG SAMA, jadi nama
   kelas yang sama akan saling menimpa antar tab.
   ============================================================================ */

/* -------- Panel kontrol atas -------- */
.mjh-head {
    border: 1px solid #e6ebf3; border-radius: 12px; background: #fff;
    padding: 14px 16px 4px; margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
}
.mjh-flabel {
    display: block; margin-bottom: 5px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #64748b;
}
/* Kotak setinggi input, dipakai kontrol non-input (ceklis) supaya sebaris rapi
   dengan field di sebelahnya — tanpa ini ceklis menggantung di tengah kolom. */
.mjh-field {
    display: flex; align-items: center; gap: 9px;
    border: 1px solid #dbe4f3; border-radius: 8px; background: #fff;
    padding: 0 12px; min-height: 38px;
}
.mjh-field label { margin: 0; font-size: 13px; color: #334155; cursor: pointer; font-weight: 500; }
/* Pesan "*Jurnal akan terbentuk di SB I" — tetap <input disabled> karena JS
   mengisinya lewat .val(); border/latar dilucuti supaya terbaca sebagai teks. */
input.mjh-sb1-note {
    border: 0; background: transparent; padding: 0; margin: 0; height: auto;
    font-size: 11px; font-style: italic; font-weight: 700; color: #c00000;
    width: 100%; box-shadow: none; outline: none;
}
.mjh-head textarea.form-control { min-height: 38px; font-size: 13px; }

/* -------- Judul seksi -------- */
.mjh-sec-head {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
    margin: 0 0 8px;
}
.mjh-sec-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; }
.mjh-pill {
    background: #eef2f7; color: #64748b; border-radius: 20px;
    padding: 2px 9px; font-size: 11px; font-weight: 700; white-space: nowrap;
}
.mjh-tools { display: flex; align-items: center; gap: 10px; margin-left: auto; }
.mjh-tools label { margin: 0; font-size: 10.5px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .5px; }
.mjh-tools select {
    border: 1px solid #dbe4f3; border-radius: 8px; padding: 4px 8px;
    font-size: 12px; color: #334155; background: #fff;
}
.mjh-search-wrap { position: relative; display: inline-block; }
.mjh-search-wrap > i {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    font-size: 11px; color: #94a3b8; pointer-events: none;
}
.mjh-search {
    border: 1px solid #dbe4f3; border-radius: 8px; padding: 5px 11px 5px 28px;
    font-size: 12px; color: #334155; width: 260px; max-width: 100%; background: #fff;
}
.mjh-search:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); outline: none; }

/* -------- Tabel -------- */
.mjh-tbl-wrap {
    max-height: 420px; overflow: auto;
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    scrollbar-width: thin; scrollbar-color: #c7d2e0 transparent;
}
.mjh-tbl-wrap::-webkit-scrollbar { width: 9px; height: 9px; }
.mjh-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
.mjh-tbl-wrap::-webkit-scrollbar-thumb { background: #c7d2e0; border-radius: 8px; border: 2px solid #fff; }
.mjh-tbl-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.mjh-tbl-wrap::-webkit-scrollbar-corner { background: transparent; }

.mjh-tbl { margin-bottom: 0; border: 0; }
/* Gradien dipasang di BARIS (tr), bukan tiap sel — kalau di th, tiap kolom
   menggambar gradiennya sendiri sehingga header terlihat berpita.
   Sticky di <thead> supaya latar barisnya ikut menempel saat digulir. */
.mjh-tbl thead { position: sticky; top: 0; z-index: 2; background: #1E3A8A; }
.mjh-tbl thead tr { background: linear-gradient(90deg, #1E3A8A, #2f5bbf); }
.mjh-tbl thead th {
    background: transparent !important;
    color: #fff; border: 0; border-bottom: 2px solid #16306e;
    font-size: 11px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase;
    white-space: nowrap; vertical-align: middle; padding: 9px 10px;
}
.mjh-tbl tbody td {
    font-size: 12px; vertical-align: middle; white-space: nowrap;
    padding: 6px 10px; border-top: 0; border-bottom: 1px solid #eef2f7;
}
.mjh-tbl tbody tr:nth-child(even) td { background: #fbfcfe; }
.mjh-tbl tbody tr:hover td { background: #eaf1ff; }
.mjh-tbl .text-right { text-align: right; font-variant-numeric: tabular-nums; }
/* Tiga kolom teks terpanjang dipotong supaya Debit/Credit tidak terdorong keluar
   layar. Nilai utuhnya dikembalikan sebagai tooltip oleh skrip di bawah. */
#table-hris tbody td:nth-child(1) { max-width: 200px; }
#table-hris tbody td:nth-child(2) { max-width: 230px; }
#table-hris tbody td:nth-child(11) { max-width: 190px; }
#table-hris tbody td:nth-child(1),
#table-hris tbody td:nth-child(2),
#table-hris tbody td:nth-child(11) { overflow: hidden; text-overflow: ellipsis; }

/* Chrome bawaan DataTables disembunyikan HANYA setelah skrip berhasil
   memindahkan info & pagination keluar kotak scroll. Kalau skrip tidak jalan,
   chrome aslinya tetap tampil dan berfungsi — degradasi, bukan layar rusak. */
.mjh-tbl-wrap div.dataTables_wrapper > .row { margin: 0; }
.mjh-tbl-wrap div.dataTables_wrapper > .row > [class*="col-"] { padding: 0; }
.mjh-js .mjh-tbl-wrap div.dataTables_wrapper > .row:first-child,
.mjh-js .mjh-tbl-wrap div.dataTables_wrapper > .row:last-child { display: none; }
.mjh-tbl-foot {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px; margin-top: 8px;
}
.mjh-tbl-foot:empty { display: none; }
.mjh-tbl-foot .dataTables_info { font-size: 12px; color: #64748b; padding: 0; }
.mjh-tbl-foot .dataTables_paginate { margin: 0; padding: 0; }
.mjh-tbl-foot .page-item:first-child .page-link,
.mjh-tbl-foot .page-item:last-child .page-link { border-radius: 8px; }

/* -------- Kartu total -------- */
.mjh-tot {
    border: 1px solid #e6ebf3; border-left: 4px solid #cbd5e1; border-radius: 10px;
    background: #fff; box-shadow: 0 1px 3px rgba(15,23,42,.05);
    padding: 12px 14px 13px; height: 100%;
}
.mjh-tot.tone-nag { border-left-color: #5b7ba8; }
.mjh-tot.tone-nak { border-left-color: #4f8a6b; }
.mjh-tot.tone-all { border-left-color: #4a5578; background: #f8fafc; }
.mjh-tot-name {
    font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    color: #475569; padding-bottom: 6px; border-bottom: 1px solid #eef2f7; margin-bottom: 2px;
}
.mjh-tot-code {
    background: #eef2f7; color: #475569; border-radius: 4px; padding: 0 5px;
    margin-left: 6px; font-size: 9.5px; letter-spacing: .4px;
}
/* Debit & Credit BERSEBELAHAN, mengikuti kartu total di tab PPN Masukan.
   Label di ATAS angka (bukan di sampingnya) supaya angka dapat selebar kolom;
   pada layar sempit keduanya kembali menumpuk lewat media query di bawah. */
.mjh-tot-split {
    display: grid; grid-template-columns: 1fr 1fr; column-gap: 14px; margin-top: 8px;
}
.mjh-tot-col { display: flex; flex-direction: column; min-width: 0; }
.mjh-tot-col.is-cre { border-left: 1px solid #eef2f7; padding-left: 14px; }
.mjh-tot-lbl {
    font-size: 10.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; margin-bottom: 2px; cursor: help;
}
.mjh-tot-col.is-deb .mjh-tot-lbl { color: #1d4ed8; }
.mjh-tot-col.is-cre .mjh-tot-lbl { color: #b45309; }
/* Tetap <input readonly>: JS induk mengisinya lewat .val(), yang TIDAK bekerja
   pada <span>/<div>. tabindex=-1 supaya tidak jadi perhentian Tab.
   Warna JANGAN di-!important — induk mewarnainya inline saat tidak balance. */
input.mjh-tot-val {
    display: block; width: 100%; min-width: 0;
    margin: 0; padding: 0; border: 0; background: transparent; box-shadow: none;
    font-size: 17px; font-weight: 700; color: #0f172a;
    font-variant-numeric: tabular-nums; line-height: 1.25; text-align: left;
}
input.mjh-tot-val:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
@media (max-width: 991.98px) {
    .mjh-tot-split { grid-template-columns: 1fr; }
    .mjh-tot-col.is-cre { border-left: 0; padding-left: 0; margin-top: 8px; }
}

.mjh-actions { display: flex; gap: 8px; flex-wrap: wrap; }
</style>

<form id="form-data2" method="post">
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- ============ Panel kontrol ============ -->
            <div class="mjh-head">
                <div class="form-row">

                    <div class="col-md-2 mb-3">
                        <label class="mjh-flabel" for="mj_date2">Date</label>
                        <input type="text" name="mj_date2" id="mj_date2" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off" onchange="getRate2()">
                        <input type="hidden" class="form-control angka" id="rate_mj2" name="rate_mj2">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mjh-flabel" for="mj_type2">Type</label>
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

                    <div class="col-md-3 mb-3">
                        <label class="mjh-flabel" for="profit_center2">Profit Center</label>
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

                    <div class="col-md-4 mb-3"></div>

                    <div class="col-md-2 mb-3">
                        <label class="mjh-flabel" for="hris_date">Period Filter</label>
                        <input type="text" name="hris_date" id="hris_date" class="form-control tanggal_hris" value="<?php echo date('M Y'); ?>" autocomplete="off">
                    </div>

                    <!-- Tombol sejajar dengan field di sebelahnya: label kosong (&nbsp;)
                         menyamakan tinggi baris, jadi tombol tidak naik ke atas. -->
                    <div class="col-md-1 mb-3">
                        <label class="mjh-flabel">&nbsp;</label>
                        <button type="button" id="send" name="send" class="app-btn app-btn-primary" onclick="dataTableReload()">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="mjh-flabel" for="to2_sb1">SB I</label>
                        <div class="mjh-field">
                            <input type="checkbox" class="checkbox_sb1" id="to2_sb1" name="to2_sb1" value="1">
                            <label for="to2_sb1">Include</label>
                            <input type="text" class="mjh-sb1-note" id="txt2_sb1" name="txt2_sb1" value="" disabled>
                            <input type="hidden" id="fil2_sb1" name="fil2_sb1" value="">
                        </div>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="mjh-flabel" for="pesan2">Description</label>
                        <textarea class="form-control" name="pesan2" id="pesan2" placeholder="descriptions..." required></textarea>
                    </div>

                </div>
            </div>

            <!-- ============ Baris jurnal ============ -->
            <div class="mjh-sec-head">
                <h6 class="mjh-sec-title">Journal Lines</h6>
                <span class="mjh-pill" id="mjh_count">0 rows</span>
                <div class="mjh-tools">
                    <label for="mjh_len">Rows</label>
                    <select id="mjh_len">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="mjh-search-wrap">
                        <i class="fa fa-search"></i>
                        <input type="text" class="mjh-search" id="mjh_search" placeholder="Search COA / cost center / desc...">
                    </span>
                </div>
            </div>

            <div class="mjh-tbl-wrap" id="mjh_wrap">
                <table id="table-hris" class="table table-hover table-sm mjh-tbl">
                    <thead>
                        <tr>
                            <th>Profit Center</th>
                            <th>COA</th>
                            <th>Cost Center</th>
                            <th>Reff Document</th>
                            <th>Reff Date</th>
                            <th>Buyer</th>
                            <th>Worksheet</th>
                            <th>Curr</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="mjh-tbl-foot" id="mjh_foot"></div>

            <!-- ============ Ringkasan saldo ============ -->
            <div class="mjh-sec-head mt-4">
                <h6 class="mjh-sec-title">Balance Summary</h6>
            </div>

            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="mjh-tot tone-nag">
                        <div class="mjh-tot-name">Nirwana Alabare Garment<span class="mjh-tot-code">NAG</span></div>
                        <div class="mjh-tot-split">
                            <div class="mjh-tot-col is-deb">
                                <span class="mjh-tot-lbl" title="Total debit for this profit center">Debit</span>
                                <input type="text" class="mjh-tot-val" id="tot_debit_nag2" name="tot_debit_nag2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mjh-tot-col is-cre">
                                <span class="mjh-tot-lbl" title="Total credit for this profit center">Credit</span>
                                <input type="text" class="mjh-tot-val" id="tot_credit_nag2" name="tot_credit_nag2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <!-- Nilai mentah (tanpa pemisah ribuan) — diisi drawCallback induk,
                             ikut terkirim saat Save lewat form #form-data2. -->
                        <input type="hidden" id="h_tot_debit_nag2" name="h_tot_debit_nag2">
                        <input type="hidden" id="h_tot_credit_nag2" name="h_tot_credit_nag2">
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="mjh-tot tone-nak">
                        <div class="mjh-tot-name">Nirwana Alabare Knitting<span class="mjh-tot-code">NAK</span></div>
                        <div class="mjh-tot-split">
                            <div class="mjh-tot-col is-deb">
                                <span class="mjh-tot-lbl" title="Total debit for this profit center">Debit</span>
                                <input type="text" class="mjh-tot-val" id="tot_debit_nak2" name="tot_debit_nak2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mjh-tot-col is-cre">
                                <span class="mjh-tot-lbl" title="Total credit for this profit center">Credit</span>
                                <input type="text" class="mjh-tot-val" id="tot_credit_nak2" name="tot_credit_nak2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <input type="hidden" id="h_tot_debit_nak2" name="h_tot_debit_nak2">
                        <input type="hidden" id="h_tot_credit_nak2" name="h_tot_credit_nak2">
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="mjh-tot tone-all">
                        <div class="mjh-tot-name">Grand Total<span class="mjh-tot-code">NAG + NAK</span></div>
                        <div class="mjh-tot-split">
                            <div class="mjh-tot-col is-deb">
                                <span class="mjh-tot-lbl" title="Total debit across all profit centers">Debit</span>
                                <input type="text" class="mjh-tot-val" id="tot_debit2" name="tot_debit2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            <div class="mjh-tot-col is-cre">
                                <span class="mjh-tot-lbl" title="Total credit across all profit centers">Credit</span>
                                <input type="text" class="mjh-tot-val" id="tot_credit2" name="tot_credit2" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                        </div>
                        <input type="hidden" id="h_tot_debit2" name="h_tot_debit2">
                        <input type="hidden" id="h_tot_credit2" name="h_tot_credit2">
                    </div>
                </div>
            </div>

            <div class="mjh-actions mt-3 mb-2">
                <button type="button" class="app-btn app-btn-primary" name="simpan2" id="simpan2"><i class="fa fa-floppy-o"></i> Save</button>
                <button type="button" name="batal" id="batal" class="app-btn app-btn-danger" onclick="location.href='memorial-journal.php'"><i class="fa fa-angle-double-left"></i> Back</button>
            </div>

        </div>
    </div>
</form>

<script>
/* Berkas ini di-include SEBELUM <script src="jquery">, jadi kodenya tidak boleh
   langsung memakai $. DOMContentLoaded menunggu seluruh <script> sinkron dimuat,
   lalu $(function(){}) menunggu DataTables induk selesai menginisialisasi
   #table-hris — sehingga tidak perlu mengubah blok init di halaman induk. */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') { return; }

    jQuery(function ($) {
        var SEL = '#table-hris';

        function dt() {
            return ($.fn.DataTable && $.fn.DataTable.isDataTable(SEL)) ? $(SEL).DataTable() : null;
        }

        /* Pindahkan info & pagination bawaan KELUAR dari kotak scroll, lalu baru
           sembunyikan chrome asli. Urutannya penting: kalau .mjh-js dipasang
           duluan dan pemindahan gagal, user kehilangan pagination sepenuhnya. */
        function wireTools() {
            var wrap = $(SEL).closest('.dataTables_wrapper');
            if (!wrap.length) { return false; }

            $('#mjh_foot').append(wrap.find('.dataTables_info'))
                          .append(wrap.find('.dataTables_paginate'));
            $('body').addClass('mjh-js');

            // Kontrol pengganti diikat ke API DataTables, bukan ke elemen aslinya.
            var t = dt();
            if (t) {
                $('#mjh_len').val(t.page.len()).off('change.mjh').on('change.mjh', function () {
                    t.page.len(parseInt(this.value, 10)).draw();
                });
                var timer = null;
                $('#mjh_search').off('input.mjh').on('input.mjh', function () {
                    var v = this.value;
                    clearTimeout(timer);
                    timer = setTimeout(function () { t.search(v).draw(); }, 250);
                });
            }
            return true;
        }

        /* Kolom teks panjang dipotong elipsis lewat CSS; tanpa title, isi yang
           terpotong tidak bisa dibaca sama sekali. Hanya baris yang sedang
           tampil (maks 1 halaman), jadi biayanya kecil. */
        function syncTitles() {
            $(SEL + ' tbody tr').each(function () {
                var td = this.cells;
                [0, 1, 10].forEach(function (i) {
                    if (td[i]) {
                        var txt = (td[i].textContent || '').trim();
                        if (txt && txt !== '-') { td[i].setAttribute('title', txt); }
                    }
                });
            });
        }

        function refresh() {
            var t = dt();
            if (!t) { return; }
            var info = t.page.info();
            $('#mjh_count').text(
                info.recordsDisplay === info.recordsTotal
                    ? info.recordsTotal + ' rows'
                    : info.recordsDisplay + ' of ' + info.recordsTotal + ' rows'
            );
            syncTitles();
        }

        // DataTables induk dibuat di $(document).ready juga; urutan handler tidak
        // dijamin, jadi coba beberapa kali sampai tabelnya benar-benar ada.
        var tries = 0;
        (function attach() {
            if (dt()) {
                wireTools();
                $(SEL).off('draw.dt.mjh').on('draw.dt.mjh', refresh);
                refresh();
                return;
            }
            if (++tries < 40) { setTimeout(attach, 150); }
        })();
    });
});
</script>
