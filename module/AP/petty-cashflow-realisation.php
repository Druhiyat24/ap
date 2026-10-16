<?php include '../header.php' ?>

<!-- Skin daftar/laporan yang dipakai aplikasi sekarang - sama dgn halaman
     List Invoice Received:
       app-ftr-list.css  : kartu .ftl-card/.ftl-head/.ftl-panel/.ftl-body, tabel .ftl-tbl
       app-skin-form.css : kontrol form (kotak tanggal) & tombol .app-btn
     header.php TIDAK memuat CSS ini, jadi tiap halaman memuatnya sendiri. -->
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">

<style type="text/css">
    /* Warna kepala tabel sekarang datang dari .ftl-tbl (app-ftr-list.css) -
       biru lembut, bukan biru pekat. Yang tersisa di sini cuma perataannya,
       karena itu urusan isi laporan, bukan skin. */
    #cfr-table thead th {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    /* .ftl-tbl MENOLKAN lebar & padding kolom PERTAMA - di halaman daftar itu
       kolom tombol "+" milik DataTables Responsive. Tabel ini tidak memakai
       DataTables sama sekali; kolom pertamanya DESCRIPTIONS, jadi aturan itu
       dibatalkan di sini (id + kelas, jadi menang atas aturan skin). */
    #cfr-table.ftl-tbl thead th:first-child {
        width: 220px !important;
        min-width: 220px !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    /* .ftl-card memakai overflow:hidden supaya sudut membulatnya rapi. Untuk
       kartu TABEL itu memang yang diinginkan, tapi untuk kartu FILTER tidak:
       menu dropdown "Account" terbuka DI BAWAH tombolnya, yaitu di LUAR
       kartu, sehingga ikut terpotong habis - tombolnya jadi terlihat mati
       padahal menunya terbuka tapi tidak kelihatan (dilaporkan pemakai
       16 Okt 2026). Jadi kartu filter saja yang dibolehkan meluap, dan
       diangkat di atas kartu tabel supaya menunya tidak tertimpa.
       Halaman daftar lain tidak kena masalah ini karena dropdown-nya select2,
       yang menempelkan menunya ke <body>, bukan di dalam kartu. */
    #cfr-filter-card {
        overflow: visible;
        position: relative;
        z-index: 20;
    }
    /* Tombol dropdown Account disamakan dgn kontrol lain di baris filter
       (putih + garis tipis), bukan abu-abu penuh bawaan btn-outline-secondary
       yang jadi gelap begitu di-hover/aktif. */
    #cfrAccountDropdownBtn {
        background-color: #fff;
        color: #334155;
        border: 1px solid #dbe4f3;
        border-radius: 9px;
        text-align: left;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    /* Label memakan sisa ruang supaya lencana & tanda panah selalu menempel
       di kanan, berapa pun panjang teksnya. */
    #cfrAccLabel {
        flex: 1 1 auto;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    /* Lencana jumlah akun terpilih - supaya kelihatan sekilas tanpa membaca
       teksnya, dan tetap terbaca waktu nama akunnya panjang. */
    #cfrAccBadge {
        flex: 0 0 auto;
        background: #eef3fb;
        color: #1e3a8a;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .04em;
        padding: 2px 8px;
        line-height: 1.5;
    }
    #cfrAccountDropdownBtn:hover,
    #cfrAccountDropdownBtn:focus,
    #cfrAccountDropdownBtn[aria-expanded="true"] {
        background-color: #fff;
        color: #334155;
        border-color: #93b4f5;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
    }
    #cfr-table {
        font-size: 11px;
        white-space: nowrap;
    }
    #cfr-table td, #cfr-table th {
        padding: 3px 8px;
    }
    #cfr-table td.num {
        text-align: right;
    }
    /* Area tabel dibatasi tinggi biar bisa di-scroll di dalam kotaknya
       sendiri (bukan scroll seluruh halaman). */
    #cfr-table-wrapper {
        max-height: 65vh;
        overflow-y: auto;
        position: relative;
    }
    /* Freeze kolom DESCRIPTIONS (kolom 1 saja) supaya tetap kelihatan saat
       scroll horizontal - sengaja dikecualikan dari sel yang punya colspan
       (baris judul section/spacer), karena sel itu melebar ke seluruh baris
       jadi tidak cocok di-freeze. */
    #cfr-table th:nth-child(1),
    #cfr-table td:not([colspan]):nth-child(1) {
        position: sticky;
        left: 0;
        width: 220px;
        min-width: 220px;
        background: #fff;
        z-index: 2;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #cfr-table tr.grand-row td:not([colspan]):nth-child(1) { background: #eef2f9; }
    /* Baris nama bank (account-header-row) TIDAK punya sel untuk kolom 1-4
       sama sekali di DOM-nya (kolom itu sudah "diisi" rowspan=2 dari baris
       header pertama) - jadi th PERTAMA di baris ini sebenarnya kolom bank
       ke-1 (visual), BUKAN kolom DESCRIPTIONS, walau sama-sama nth-child(1).
       Tanpa override ini dia ikut ke-freeze di left:0 seolah-olah kolom
       DESCRIPTIONS, jadi ketutup/hilang di belakang kolom itu. */
    #cfr-table thead tr.account-header-row th:nth-child(1) {
        /* Tetap sticky (biar tetap nempel di bawah header saat discroll
           VERTIKAL, sama seperti sel header lainnya) - cuma left/width-nya
           yang direset supaya tidak ikut freeze HORIZONTAL seperti kolom
           DESCRIPTIONS. */
        position: sticky;
        left: auto;
        width: auto;
        min-width: 90px;
        background: #1E3A8A;
        overflow: visible;
        text-overflow: clip;
    }
    /* Freeze header (horizontal) - dipakai bareng dengan freeze kolom 1 di
       atas (vertical). Karena ini sticky di TABLE YANG SAMA (bukan tabel
       salinan/clone terpisah), lebar & posisi kolom header otomatis selalu
       identik dengan body-nya. Ketiga baris di thead (judul kolom, nama
       bank, SALDO AWAL) dibuat freeze - nilai `top` baris ke-2/ke-3 di-set
       lewat JS (bukan di-hardcode di sini) karena baris pertama pakai
       rowspan=2 sehingga tingginya tidak otomatis kehitung CSS untuk baris
       di bawahnya. */
    #cfr-table thead tr:nth-child(1) th,
    #cfr-table thead tr.account-header-row th,
    #cfr-table thead tr#cfr-saldo-awal-row td {
        position: sticky;
        z-index: 2;
        top: 0;
    }
    /* Sel pojok (kolom DESCRIPTIONS di tiap baris header) butuh z-index
       lebih tinggi supaya tidak ketutup kolom sticky-left baris body yang
       lewat di bawahnya saat discroll. */
    #cfr-table thead tr:nth-child(1) th:nth-child(1) {
        z-index: 3;
        background: #1E3A8A;
    }
    #cfr-table thead tr#cfr-saldo-awal-row td:nth-child(1) {
        z-index: 3;
    }
    /* Baris judul section yang di-merge (CASH RECEIPTS, 1. OPERATING
       ACTIVITIES, dst - pakai <td colspan>) melebar sepanjang baris, dan
       sticky langsung di <td> selebar itu ternyata tidak konsisten nempel di
       semua kondisi. Jadi dipakai teknik yang lebih pasti jalan: teks di
       dalamnya dibungkus <span> (lihat script di bawah), lalu SPAN itu yang
       di-sticky - caranya lebih reliable karena elemen yang di-sticky-kan
       kecil (selebar teksnya saja), bukan sel yang selebar seluruh baris. */
    #cfr-table td[colspan] > span.cfr-sticky-label {
        position: sticky;
        left: 8px;
        display: inline-block;
    }
    #cfr-table tr.section-title td {
        font-weight: bold;
        text-decoration: underline;
        color: #1E3A8A;
    }
    #cfr-table tr.total-row td {
        font-weight: bold;
        border-top: 1px solid #333;
        border-bottom: 1px solid #333;
    }
    #cfr-table tr.grand-row td {
        font-weight: bold;
        background: #eef2f9;
    }

    #cfr-table tr.disbursement-header td {
        font-weight: bold;
        color: #b91c1c;
    }

    /* Shadow tipis di tepi kolom/baris yang freeze - sinyal visual standar
       bahwa area itu "mengambang" di atas konten yang discroll di
       bawah/sampingnya. */
    #cfr-table th:nth-child(1),
    #cfr-table td:not([colspan]):nth-child(1) {
        box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.2);
    }
    #cfr-table thead tr#cfr-saldo-awal-row td {
        box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    }

    /* Highlight baris saat di-hover - baris section-title/total/grand/
       disbursement-header dikecualikan karena sudah punya warna penekanan
       sendiri (biar tidak "bertabrakan" saat di-hover). */
    #cfr-table tbody tr:not(.section-title):not(.total-row):not(.grand-row):not(.disbursement-header):hover td:not([colspan]) {
        background: #eaf1ff;
    }

    /* Baris data selang-seling (zebra) - dikasih lewat class .cfr-row-alt
       yang di-set oleh JS (lihat script di bawah), bukan CSS :nth-child
       biasa, karena banyak baris spacer/section-title/total di antara baris
       data yang bikin pola :nth-child jadi tidak rapi kalau dihitung dari
       urutan DOM mentah. */
    #cfr-table tbody tr.cfr-row-alt td:not([colspan]) {
        background: #f7f9fc;
    }

    /* Angka negatif (format "(1.234,56)" dari cfrFmt) ditandai merah -
       konvensi umum laporan keuangan. Class ditambahkan lewat JS karena
       cfrFmt() cuma menghasilkan teks polos, tidak ada penanda negatif di
       markup-nya. */
    #cfr-table td.cfr-negative {
        color: #c00000;
    }

    /* Filter "Account" (dropdown REALISATION BY BANK) - gaya toggle switch
       yang sama seperti panel "Manage Role" di userrole.php, biar konsisten
       di seluruh aplikasi. */
    /* Menu DIKUNCI selebar tombolnya - dulu min-width 320px membuatnya meluber
       ke kolom sebelah kanan. Pola yang sama dipakai dropdown filter lain
       (lihat .bootstrap-select > .dropdown-menu di app-skin-form.css). */
    #cfrAccountDropdownMenu {
        width: 100%;
        /* LANTAI lebar, bukan 100% mentah. Kolomnya sekarang col-md-2, dan di
           layar ~1366px itu cuma sekitar 200px - nama seperti "KAS KECIL
           KANTOR 1" akan kena ellipsis padahal laporan ini justru perlu nama
           akunnya terbaca utuh. Dgn lantai 230px, di layar lebar menunya tetap
           mengikuti lebar tombol (karena tombolnya lebih lebar dari itu), dan
           di layar sempit menunya sedikit melebihi tombol - jauh lebih baik
           daripada nama terpotong. Tetap jauh dari min-width 320px yang dulu
           bikin menunya meluber ke kolom sebelah. */
        min-width: 230px;
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #e6ebf3;
        border-radius: 12px;
        box-shadow: 0 14px 36px rgba(15, 23, 42, .15);
        margin-top: 5px;
    }
    /* SATU KOLOM. Dulu grid 2 kolom: dgn 5 akun selalu ada 1 baris yatim, dan
       nama panjang seperti "KAS KECIL KANTOR 1" kepotong. */
    .cfr-acc-panel {
        display: block;
    }
    /* Kepala menu: judul kecil + pintasan "Select all" di kanannya. Toggle
       "ALL ACCOUNTS"-nya sendiri DISEMBUNYIKAN (lihat .cfr-acc-allsw) tapi
       tetap ada di DOM karena seluruh logika centang di JS bersandar padanya. */
    .cfr-acc-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 4px 8px 8px;
        border-bottom: 1px solid #eef2f7;
        margin-bottom: 4px;
    }
    .cfr-acc-head b {
        font-size: 10.5px;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #64748b;
    }
    .cfr-acc-link {
        font-size: 11.5px;
        font-weight: 700;
        color: #2563eb;
        cursor: pointer;
        user-select: none;
    }
    .cfr-acc-link:hover { text-decoration: underline; }
    .cfr-acc-allsw { display: none !important; }
    /* Baris akun tidak lagi berbingkai sendiri - menunya sudah punya bingkai,
       jadi kotak di dalam kotak terasa ramai. Penanda arah kursor cukup dgn
       latar lembut saat disorot. */
    .cfr-acc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 7px 8px;
        font-size: 12.5px;
        background: transparent;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color .15s ease;
    }
    .cfr-acc-row:hover {
        background-color: #f6f9ff;
    }
    .cfr-acc-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .cfr-acc-all-row .cfr-acc-label {
        font-weight: 700;
    }

    /* Toggle dikecilkan: dulu 38x21 terasa besar dibanding teksnya. */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 19px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: background-color .2s ease;
        border-radius: 19px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 2px;
        bottom: 2px;
        background-color: #fff;
        transition: transform .2s ease;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .35);
    }
    .toggle-switch input:checked + .toggle-slider { background-color: #1E3A8A; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(15px); }
    .toggle-switch input:focus + .toggle-slider { box-shadow: 0 0 0 3px rgba(30, 58, 138, .25); }
</style>

<?php
require __DIR__ . '/cfr_functions.php';

// ============================================================================
// PETTY CASH FLOW REALISATION (menu Cash)
//
// SALINAN PENUH dari report-cashflow-realisation.php (menu Bank > Report),
// dibuat atas permintaan pemakai 15 Okt 2026. Yang BEDA cuma satu hal:
// daftar akunnya HANYA akun KAS (Kas Besar & Kas Kecil), tanpa akun bank.
// Perhitungannya TIDAK disalin - tetap memakai cfr_functions.php yang sama
// persis dgn halaman aslinya, jadi angka untuk akun yang sama pasti sama.
//
// Dibuat sebagai BERKAS TERSENDIRI (bukan parameter di halaman lama) supaya
// dua menu ini bisa berkembang sendiri-sendiri tanpa saling mengganggu -
// pola yang sama dgn Update BPB (1 menu = 1 berkas).
// ============================================================================


// Default From/To = hari ini (bukan awal bulan) - dan laporan langsung tampil
// begitu halaman pertama kali dibuka (belum klik Search), pakai tanggal hari
// ini itu sebagai filter awal.
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date   = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');
$doSearch   = true;

// Filter kolom "REALISATION BY BANK" - pilih akun mana yang mau ditampilkan
// (minimal 1, dipaksa lewat JS di form). Kosong (belum pernah submit/pertama
// buka halaman) berarti tampilkan SEMUA akun - lihat cfrComputeReportData().
$cfrAllAccounts = cfrGetKasAccounts($conn2);
$cfrSelectedAccounts = isset($_POST['accounts']) ? (array) $_POST['accounts'] : [];
// Belum pernah submit -> kosong. Di halaman aslinya kosong berarti SEMUA akun
// (termasuk bank), jadi di sini harus diisi sendiri dgn seluruh akun kas -
// kalau dibiarkan kosong, cfrComputeReportData() akan ikut menghitung akun
// bank dan saldo awal/akhirnya jadi bukan punya kas saja.
if (empty($cfrSelectedAccounts)) {
    $cfrSelectedAccounts = array_map(function ($a) { return $a['account']; }, $cfrAllAccounts);
}
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- id dipakai CSS di atas: kartu INI harus boleh meluap, lihat
         catatan #cfr-filter-card. -->
    <div class="ftl-card" id="cfr-filter-card">
        <!-- Kepala kartu: kotak ikon + judul + jejak menu - susunan yang sama
             dgn List Invoice Received. Pita gradien selebar kartu diganti
             kotak ikon supaya warnanya jadi aksen, bukan latar. -->
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
            <div>
                <h1>Petty Cash Flow Realisation</h1>
                <span class="ftl-crumb">Cash &rsaquo; Report &rsaquo; Petty Cash Flow Realisation</span>
            </div>
        </div><!-- /.ftl-head -->
        <div class="ftl-panel">
            <form id="form-data" method="post">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?= date('d-m-Y', strtotime($start_date)); ?>" placeholder="Start Date" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?= date('d-m-Y', strtotime($end_date)); ?>" placeholder="End Date" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label"><b>Account</b></label>
                        <!-- Filter kolom "REALISATION BY BANK" - dropdown berisi kartu toggle
                             switch (gaya sama seperti "Manage Role" di userrole.php), bukan
                             checkbox Bootstrap biasa. Minimal 1 akun harus tetap ON - dipaksa
                             lewat JS di bawah (lihat #cfrAccountDropdownMenu). -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-secondary btn-sm form-control text-start dropdown-toggle" id="cfrAccountDropdownBtn" data-toggle="dropdown" aria-expanded="false">
                                <span id="cfrAccLabel">All Accounts</span>
                                <span id="cfrAccBadge">0</span>
                            </button>
                            <div class="dropdown-menu p-2" id="cfrAccountDropdownMenu">
                                <!-- Kepala menu. Toggle "ALL ACCOUNTS" lama tetap ada di DOM
                                     (disembunyikan lewat .cfr-acc-allsw) karena seluruh logika
                                     centang di JS bersandar padanya; yang terlihat pemakai
                                     sekarang pintasan teks "Select all".
                                     TIDAK ada "Clear all": aturan laporan ini minimal 1 akun
                                     harus tetap menyala, jadi pintasan itu akan menyesatkan. -->
                                <div class="cfr-acc-head">
                                    <b>Select accounts</b>
                                    <span class="cfr-acc-link" id="cfrAccSelectAll">Select all</span>
                                    <label class="toggle-switch cfr-acc-allsw">
                                        <input type="checkbox" id="cfr-acc-all" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="cfr-acc-panel">
                                    <?php foreach ($cfrAllAccounts as $acc) {
                                        $accId = 'cfr-acc-' . md5($acc['account']);
                                        $checked = empty($cfrSelectedAccounts) || in_array($acc['account'], $cfrSelectedAccounts, true);
                                    ?>
                                    <div class="cfr-acc-row">
                                        <span class="cfr-acc-label"><?= htmlspecialchars($acc['label']); ?></span>
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="cfr-acc-item" name="accounts[]" value="<?= htmlspecialchars($acc['account']); ?>" id="<?= $accId; ?>"<?= $checked ? ' checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .ftl-actions menyejajarkan tombol dgn dasar isian di
                         kirinya; kosakata tombolnya .app-btn - sama dgn
                         halaman daftar lain. id tombol TIDAK diubah karena
                         dipakai JS di bawah (#submit & #btnExportExcel). -->
                    <div class="col-md-6 ftl-actions">
                        <button type="submit" id="submit" class="app-btn app-btn-primary app-btn-sm">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <button type="button" id="btnExportExcel" class="app-btn app-btn-excel app-btn-sm">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ftl-card mt-3">
        <div class="ftl-body">
            <?php if (!$doSearch) { ?>
                <p class="text-muted mb-0">Pilih periode lalu klik Search untuk menampilkan laporan.</p>
            <?php } else {

            // =========================
            // HITUNG SEMUA DATA REPORT (akun, saldo awal/akhir, kategori,
            // realisasi, penerimaan/pelunasan pinjaman bank dedicated)
            // =========================
            extract(cfrComputeReportData($conn2, $start_date, $end_date, $cfrSelectedAccounts));
            ?>

            <!-- Judul laporan (nama PT/periode) sengaja DILUAR
                 #cfr-table-wrapper (bukan di dalamnya) supaya tidak ikut
                 kepotong saat tabel di-scroll ke samping - judul ini statis,
                 tidak perlu freeze karena memang tidak pernah scroll. -->
            <div class="mb-3">
                <div><b>PT NIRWANA ALABARE GARMENT</b></div>
                <div><b>CASH FLOW REALISATION</b></div>
                <div>FOR PERIOD <?= strtoupper(date('d M Y', strtotime($start_date))); ?> - <?= strtoupper(date('d M Y', strtotime($end_date))); ?></div>
            </div>

            <div class="table-responsive" id="cfr-table-wrapper">
                <table id="cfr-table" class="table ftl-tbl">
                    <thead>
                        <tr>
                            <th rowspan="2" style="text-align:left;">DESCRIPTIONS</th>
                            <th rowspan="2">PROJECTION</th>
                            <th rowspan="2">REALISATION</th>
                            <th rowspan="2">VARIANCE</th>
                            <th colspan="<?= $colspanAccounts; ?>">REALISATION BY BANK</th>
                        </tr>
                        <tr class="account-header-row">
                            <?php foreach ($accounts as $acc) { ?>
                                <th><?= htmlspecialchars($acc['label']); ?></th>
                            <?php } ?>
                        </tr>
                        <!-- Baris "SALDO AWAL" sengaja DIPINDAH ke dalam <thead> (bukan baris
                             <tbody> biasa yang "dipaksa" ikut sticky) - lebih stabil karena
                             perilaku sticky untuk baris thead jauh lebih konsisten di semua
                             browser dibanding baris tbody yang top-nya dihitung manual lewat JS. -->
                        <tr class="grand-row" id="cfr-saldo-awal-row">
                            <td>SALDO AWAL KAS DAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalBegin); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($beginBalance[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="section-title"><td colspan="<?= 4 + $colspanAccounts; ?>">CASH RECEIPTS :</td></tr>

                        <?php
                        $grandReceiptTotal = 0;
                        $subtotalReceiptByAct = [];
                        foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
                            $rows = $categories['Cash In'][$act];
                            if (empty($rows)) continue;
                            echo '<tr class="section-title"><td colspan="' . (4 + $colspanAccounts) . '">' . $actNo . '. ' . $act . ' :</td></tr>';
                            $actTotal = 0;
                            foreach ($rows as $row) {
                                $total = cfrRowTotal($realisasi, $row['id'], true);
                                $actTotal += $total;
                                echo '<tr><td style="padding-left:30px;">' . $row['display_seq'] . ' ' . htmlspecialchars($row['nama_subcategory']) . '</td>';
                                echo '<td class="num">-</td><td class="num">' . cfrFmt($total) . '</td><td class="num">-</td>';
                                foreach ($accounts as $acc) {
                                    echo '<td class="num">' . cfrFmt(cfrRowValue($realisasi, $row['id'], $acc['account'], true)) . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '<tr class="total-row"><td>TOTAL CASH RECEIPT FROM ' . $act . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($actTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($rows as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr><tr><td colspan="' . (4 + $colspanAccounts) . '">&nbsp;</td></tr>';
                            $subtotalReceiptByAct[$act] = $actTotal;
                            $grandReceiptTotal += $actTotal;
                        }
                        ?>

                        <tr class="total-row">
                            <td>TOTAL CASH RECEIPTS</td>
                            <td class="num">-</td><td class="num"><?= cfrFmt($grandReceiptTotal); ?></td><td class="num">-</td>
                            <?php foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($categories['Cash In'] as $rows) {
                                    foreach ($rows as $row) {
                                        $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                    }
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="disbursement-header"><td colspan="<?= 4 + $colspanAccounts; ?>">CASH DISBURSEMENT:</td></tr>

                        <?php
                        $grandDisbTotal = 0;
                        $subtotalDisbByAct = [];
                        foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
                            $rows = $categories['Cash Out'][$act];
                            if (empty($rows)) continue;
                            echo '<tr class="section-title"><td colspan="' . (4 + $colspanAccounts) . '">' . $actNo . '. ' . $act . ':</td></tr>';
                            $actTotal = 0;
                            foreach ($rows as $row) {
                                $total = cfrRowTotal($realisasi, $row['id'], false);
                                $actTotal += $total;
                                echo '<tr><td style="padding-left:30px;">' . $row['display_seq'] . ' ' . htmlspecialchars($row['nama_subcategory']) . '</td>';
                                echo '<td class="num">-</td><td class="num">' . cfrFmt($total) . '</td><td class="num">-</td>';
                                foreach ($accounts as $acc) {
                                    echo '<td class="num">' . cfrFmt(cfrRowValue($realisasi, $row['id'], $acc['account'], false)) . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '<tr class="total-row"><td>TOTAL CASH DISBURSEMENT FROM ' . $act . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($actTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($rows as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr><tr><td colspan="' . (4 + $colspanAccounts) . '">&nbsp;</td></tr>';
                            $subtotalDisbByAct[$act] = $actTotal;
                            $grandDisbTotal += $actTotal;
                        }
                        ?>

                        <tr class="total-row">
                            <td>TOTAL CASH DISBURSEMENT</td>
                            <td class="num">-</td><td class="num"><?= cfrFmt($grandDisbTotal); ?></td><td class="num">-</td>
                            <?php foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($categories['Cash Out'] as $rows) {
                                    foreach ($rows as $row) {
                                        $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                    }
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="section-title"><td colspan="<?= 4 + $colspanAccounts; ?>">SUBTOTAL ACTIVITIES</td></tr>
                        <?php foreach (['OPERATING ACTIVITIES', 'INVESTING ACTIVITIES', 'FINANCING ACTIVITIES'] as $act) {
                            $subTotal = ($subtotalReceiptByAct[$act] ?? 0) + ($subtotalDisbByAct[$act] ?? 0);
                            echo '<tr><td style="padding-left:20px;">' . strtoupper($act) . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($subTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach (($categories['Cash In'][$act] ?? []) as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                }
                                foreach (($categories['Cash Out'][$act] ?? []) as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr>';
                        } ?>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="grand-row">
                            <td>SALDO AKHIR KAS DAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalEnd); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($endBalance[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <?php
                        // Section "Pinjaman Bank" - khusus akun 008-997-1979 & 008-998-1982
                        // (satu-satunya akun dengan bank_loan='Yes' di b_masterbank); akun
                        // lain otomatis 0 krn cfrRowValue(9/47/55) & cfrGetLoanLimitIdr
                        // memang cuma berisi data utk 2 akun itu.
                        // PROJECTION & VARIANCE sengaja "-" dulu (belum ada sumber data
                        // proyeksi yang dipakai - sama seperti kolom PROJECTION/VARIANCE di
                        // seluruh laporan ini yang juga masih placeholder "-").
                        $loanBegin = [];
                        $loanAdd = [];
                        $loanPay = [];
                        $loanFx = [];
                        $loanEnd = [];
                        $loanLimit = [];
                        $loanRemain = [];
                        $totalLoanBegin = 0;
                        $totalLoanAdd = 0;
                        $totalLoanPay = 0;
                        $totalLoanFx = 0;
                        $totalLoanEnd = 0;
                        $totalLoanLimit = 0;
                        $totalLoanRemain = 0;
                        foreach ($accounts as $acc) {
                            $a = $acc['account'];
                            $rawBegin = cfrGetBankRawBeginBalanceIdr($conn2, $a, $start_date);
                            $loanBegin[$a] = $rawBegin < 0 ? $rawBegin : 0;
                            // Dibalik tanda (-1x) dari cfrRowValue(9/47) apa adanya - di baris
                            // "9 Penerimaan Pinjaman Bank"/"47 Pelunasan Pinjaman Bank" biasa,
                            // tandanya merepresentasikan efek ke ARUS KAS. Di section Pinjaman
                            // Bank ini yang direpresentasikan adalah SALDO PINJAMAN (utang) -
                            // nambah pinjaman = utang tambah besar (makin negatif), bayar
                            // pinjaman = utang berkurang (makin ke 0) - kebalikan dari makna
                            // arus kas makanya tandanya dibalik.
                            $loanAdd[$a] = -cfrRowValue($realisasi, 9, $a, true);
                            $loanPay[$a] = -cfrRowValue($realisasi, 47, $a, false);
                            // Selisih kurs section Pinjaman Bank khusus akun 1982 - KEBALIKAN
                            // dari kondisi baris "97 Pengakuan Kerugian Selisih Kurs" di
                            // section Cash Out biasa: di sana muncul kalau saldo akhir native
                            // > 0 (uang riil), di sini JUSTRU muncul kalau saldo akhir native
                            // <= 0 (murni pinjaman/overdraft). Pakai flag $cfrSuppressFxAccounts
                            // yang sama (di-set di cfrComputeReportData) supaya 2 baris ini
                            // selalu saling eksklusif - tidak pernah dobel muncul, tidak pernah
                            // kosong berdua. TIDAK lewat cfrRowValue() - fungsi itu SENGAJA
                            // sudah men-nol-kan nilainya sendiri kalau flag ini true (itu yang
                            // bikin baris "97 Pengakuan Kerugian Selisih Kurs" biasa suppress) -
                            // kalau dipanggil dari sini juga bakal ke-nol-kan dobel. Baca
                            // $realisasi mentah langsung supaya dapat nilai aslinya.
                            $loanFx[$a] = 0;
                            if (!empty($GLOBALS['cfrSuppressFxAccounts'][$a]) && isset($realisasi[55][$a])) {
                                $loanFx[$a] = $realisasi[55][$a]['debit'] - $realisasi[55][$a]['credit'];
                            }
                            $loanEnd[$a] = $loanBegin[$a] + $loanAdd[$a] + $loanPay[$a] + $loanFx[$a];
                            $loanLimit[$a] = cfrGetLoanLimitIdr($conn2, $a, $end_date);
                            $loanRemain[$a] = $loanLimit[$a] + $loanEnd[$a];

                            $totalLoanBegin += $loanBegin[$a];
                            $totalLoanAdd += $loanAdd[$a];
                            $totalLoanPay += $loanPay[$a];
                            $totalLoanFx += $loanFx[$a];
                            $totalLoanEnd += $loanEnd[$a];
                            $totalLoanLimit += $loanLimit[$a];
                            $totalLoanRemain += $loanRemain[$a];
                        }
                        ?>
                        <tr class="section-title"><td colspan="<?= 4 + $colspanAccounts; ?>">PINJAMAN BANK</td></tr>
                        <tr class="grand-row">
                            <td>SALDO AWAL PINJAMAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanBegin); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanBegin[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>
                        <tr>
                            <td>PENAMBAHAN PINJAMAN</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanAdd); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanAdd[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td>PELUNASAN PINJAMAN</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanPay); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanPay[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td>PENGAKUAN LABA / (RUGI) SELISIH KURS</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanFx); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanFx[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>
                        <tr class="grand-row">
                            <td>SALDO AKHIR PINJAMAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanEnd); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanEnd[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td>LIMIT</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanLimit); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanLimit[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr class="grand-row">
                            <td>SISA / (KELEBIHAN) LIMIT</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalLoanRemain); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($loanRemain[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script type="text/javascript">
    function cfrToYmd(dmy) {
        if (!dmy) return '';
        let p = dmy.split('-'); // [dd, mm, yyyy]
        return `${p[2]}-${p[1]}-${p[0]}`;
    }

    // ====== FILTER AKUN "REALISATION BY BANK" (dropdown checkbox) ======
    // Klik "ALL" nyentang/lepas semua sekaligus; klik salah satu akun otomatis
    // sinkron balik status "ALL" (tercentang penuh/indeterminate). Minimal 1
    // akun harus tetap tercentang - kalau user coba lepas centang terakhir,
    // dibatalkan (balik tercentang).
    (function () {
        var allCheckbox = document.getElementById('cfr-acc-all');
        var itemCheckboxes = Array.prototype.slice.call(document.querySelectorAll('.cfr-acc-item'));
        var dropdownBtn = document.getElementById('cfrAccountDropdownBtn');
        var dropdownMenu = document.getElementById('cfrAccountDropdownMenu');
        if (!allCheckbox || !itemCheckboxes.length || !dropdownBtn || !dropdownMenu) return;

        // Label & lencana ditulis ke ELEMEN MASING-MASING, bukan ke
        // dropdownBtn.textContent seperti dulu - menulis textContent ke
        // tombolnya akan menghapus kedua <span> itu sekaligus.
        var labelEl = document.getElementById('cfrAccLabel');
        var badgeEl = document.getElementById('cfrAccBadge');

        function updateSummary() {
            var checked = itemCheckboxes.filter(function (cb) { return cb.checked; });
            var teks;
            if (checked.length === itemCheckboxes.length) {
                teks = 'All Accounts';
            } else if (checked.length === 1) {
                teks = checked[0].closest('.cfr-acc-row').querySelector('.cfr-acc-label').textContent;
            } else {
                teks = checked.length + ' of ' + itemCheckboxes.length + ' accounts';
            }
            if (labelEl) { labelEl.textContent = teks; }
            if (badgeEl) { badgeEl.textContent = checked.length; }
        }

        // Pintasan "Select all" - menyalakan semua akun sekaligus.
        var linkSelectAll = document.getElementById('cfrAccSelectAll');
        if (linkSelectAll) {
            linkSelectAll.addEventListener('click', function () {
                itemCheckboxes.forEach(function (cb) { cb.checked = true; });
                updateAllCheckboxState();
                updateSummary();
            });
        }

        function updateAllCheckboxState() {
            var checkedCount = itemCheckboxes.filter(function (cb) { return cb.checked; }).length;
            allCheckbox.checked = checkedCount === itemCheckboxes.length;
            allCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
        }

        allCheckbox.addEventListener('change', function () {
            itemCheckboxes.forEach(function (cb) { cb.checked = allCheckbox.checked; });
            if (!allCheckbox.checked) {
                // "ALL" tidak boleh sampai bikin semua ke-uncheck - minimal 1
                // akun harus tetap tercentang.
                itemCheckboxes[0].checked = true;
                allCheckbox.indeterminate = true;
            }
            updateSummary();
        });

        itemCheckboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                var checkedCount = itemCheckboxes.filter(function (c) { return c.checked; }).length;
                if (checkedCount === 0) {
                    cb.checked = true; // batalkan uncheck terakhir
                    return;
                }
                updateAllCheckboxState();
                updateSummary();
            });
        });

        // Klik checkbox/label di dalam dropdown tidak boleh menutup dropdown-nya -
        // biar bisa centang beberapa akun sekaligus tanpa dropdown ketutup tiap klik.
        dropdownMenu.addEventListener('click', function (e) { e.stopPropagation(); });

        updateSummary();
        updateAllCheckboxState();
    })();

    function cfrSelectedAccounts() {
        return Array.prototype.slice.call(document.querySelectorAll('.cfr-acc-item:checked')).map(function (cb) { return cb.value; });
    }

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        let sd = cfrToYmd(document.getElementById('start_date').value);
        let ed = cfrToYmd(document.getElementById('end_date').value);

        Swal.fire({
            title: 'Sedang export...',
            text: 'Mohon tunggu, jangan tutup halaman ini.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let params = new URLSearchParams({ start_date: sd, end_date: ed });
        cfrSelectedAccounts().forEach(function (a) { params.append('accounts[]', a); });

        fetch(`ekspor_petty_cashflow_realisation.php?${params.toString()}`)
            .then(async (res) => {
                const contentType = res.headers.get('Content-Type') || '';
                if (contentType.indexOf('json') !== -1) {
                    const data = await res.json();
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: data.error || 'Export gagal.' });
                    return;
                }
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Petty Cash Flow Realisation.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.close();
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Export gagal, coba lagi.' });
            });
    });

    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });

    // Baris ke-2 (nama bank) & ke-3 (SALDO AWAL) di thead numpuk berurutan di
    // bawah baris sebelumnya - tinggi tiap baris dihitung dari hasil render
    // (bukan di-hardcode) supaya tetap presisi walau ukuran font berubah, lalu
    // di-set sebagai inline style top per sel (paling reliable, menang dari
    // aturan CSS manapun tanpa tergantung urutan/spesifisitas selector).
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;
        var row1 = table.querySelector('thead tr:first-child');
        var row2 = table.querySelector('thead tr.account-header-row');
        var row3 = table.querySelector('thead tr#cfr-saldo-awal-row');
        if (!row1 || !row2 || !row3) return;

        function setRowTop(row, top) {
            for (var i = 0; i < row.children.length; i++) {
                row.children[i].style.top = top + 'px';
            }
        }

        function updateStickyOffsets() {
            var h1 = row1.getBoundingClientRect().height;
            var h2 = row2.getBoundingClientRect().height;
            setRowTop(row2, h1);
            setRowTop(row3, h1 + h2);
        }

        updateStickyOffsets();
        window.addEventListener('resize', updateStickyOffsets);
    })();

    // Bungkus teks baris judul section (CASH RECEIPTS, 1. OPERATING
    // ACTIVITIES, dst - sel <td colspan> yang melebar sepanjang baris)
    // dengan <span> supaya bisa di-sticky lewat CSS (lihat aturan
    // #cfr-table td[colspan] > span.cfr-sticky-label) - tetap kelihatan di
    // tepi kiri area yang keliatan walau baris itu discroll ke samping.
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;
        var cells = table.querySelectorAll('td[colspan]');
        for (var i = 0; i < cells.length; i++) {
            var td = cells[i];
            var span = document.createElement('span');
            span.className = 'cfr-sticky-label';
            span.innerHTML = td.innerHTML;
            td.innerHTML = '';
            td.appendChild(span);
        }
    })();

    // Zebra striping (.cfr-row-alt) + tandai angka negatif (.cfr-negative).
    // Dilakukan di JS (bukan CSS :nth-child) supaya baris spacer/judul
    // section/total/grand tidak ikut kehitung dalam pola selang-seling -
    // yang di-zebra cuma baris data biasa.
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;

        var rows = table.querySelectorAll('tbody tr');
        var alt = false;
        for (var i = 0; i < rows.length; i++) {
            var tr = rows[i];
            if (tr.querySelector('td[colspan]')) continue;
            if (tr.classList.contains('grand-row') || tr.classList.contains('total-row') ||
                tr.classList.contains('disbursement-header')) continue;
            if (alt) tr.classList.add('cfr-row-alt');
            alt = !alt;
        }

        var numCells = table.querySelectorAll('td.num');
        for (var j = 0; j < numCells.length; j++) {
            if (numCells[j].textContent.trim().charAt(0) === '(') {
                numCells[j].classList.add('cfr-negative');
            }
        }
    })();
</script>

</body>
</html>
