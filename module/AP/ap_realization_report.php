<?php
/* ============================================================================
   AP PAYMENT REALIZATION & VARIANCE REPORT - layarnya.
   Penyedia datanya ajx_ap_realization_report.php.

   TIDAK PUNYA MENU SENDIRI. Dibuka dari tombol "Realization" di daftar AP
   History Projection (?doc_number=AP-PPR/...), sama seperti laporan sejenis
   di ar_dev. Sebabnya ada di kepala ajx-nya: yang dibandingkan satu POTRET
   proyeksi dgn pembayaran yang benar-benar terjadi di periode potret itu,
   jadi tanpa dokumen tidak ada yang bisa dibandingkan.

   EMPAT TAB, isinya empat lembar berkas contohnya:
       1. Executive Summary  indikator kinerja + rekap relasi x jenis barang
       2. AP Payment Realization & Variance  satu baris per voucher
       3. Realization  satu baris per baris pembayaran
       4. Projection   isi dokumen proyeksinya apa adanya
   SATU TARIKAN DATA untuk keempatnya - pindah tab tidak memanggil server
   lagi, dan dgn begitu empat tab itu mustahil memperlihatkan dua keadaan
   yang berbeda.

   SELURUH layar ini BACA-SAJA. Kolom Variance Explanation dulu bisa diisi
   di sini lewat simpan_variance_ap_realization.php; 9 Nov 2026 pemakai
   meminta kolomnya kosong tanpa inputan apa pun, jadi kolomnya tinggal
   tempat kosong - diisi nanti di berkas Excel hasil ekspornya.

   Angka dari ajx-nya MENTAH; formatnya dipasang di sini. Jadi DataTables
   mengurutkan kolom uang sebagai angka - penting, laporan ini dipakai
   mencari yang paling melenceng.

   Susunan, kosakata, dan warna kepala tabelnya mengikuti
   ap_projection_report.php & ap_history_projection.php.
   ============================================================================ */
include '../header.php';

/* Nomor dokumennya dibaca di sini supaya bisa langsung ditanam ke JS -
   layar ini memang tidak berguna tanpa nomor dokumen. */
$docAwal = isset($_GET['doc_number']) ? trim((string) $_GET['doc_number']) : '';
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* ===== kepala laporan =====
   Nomor dokumennya ditaruh paling depan dan dibuat paling besar: SELURUH
   angka di layar ini mengacu ke satu dokumen itu, jadi itulah yang harus
   terbaca duluan. */
.rv-kepala { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 22px; }
.rv-dok { display: flex; align-items: center; gap: 11px; }
.rv-dok-ikon {
  display: inline-flex; align-items: center; justify-content: center;
  width: 40px; height: 40px; border-radius: 11px; flex: 0 0 auto;
  background: linear-gradient(135deg, #1e3a5f, #2c5282);
  color: #e8f0fb; font-size: 16px;
}
.rv-dok-teks { display: flex; flex-direction: column; gap: 1px; line-height: 1.25; }
.rv-dok-no { font-size: 17px; font-weight: 800; color: #0f172a; font-variant-numeric: tabular-nums; }
.rv-dok-ket { font-size: 10.5px; font-weight: 700; letter-spacing: .4px;
              text-transform: uppercase; color: #94a3b8;
              display: flex; align-items: center; gap: 6px; }
.rv-garis { width: 1px; align-self: stretch; min-height: 34px; background: #e8edf4; }
.rv-ruas { display: flex; flex-direction: column; gap: 2px; }
.rv-label { font-size: 10px; font-weight: 700; letter-spacing: .5px;
            text-transform: uppercase; color: #94a3b8; }
.rv-nilai { font-size: 13px; font-weight: 700; color: #0f172a; }
.rv-aksi { margin-left: auto; display: flex; gap: 8px; flex-wrap: wrap; }

/* Lencana Type: kosakata warna sama dgn History Projection. */
.rv-badge { display: inline-block; padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: lowercase; }
.rv-badge.daily   { background: #dbeafe; color: #1e40af; }
.rv-badge.weekly  { background: #fef3c7; color: #92400e; }
.rv-badge.monthly { background: #d1fae5; color: #065f46; }
.rv-badge.lain    { background: #e2e8f0; color: #334155; }

/* ===== tab ===== */
.rv-tab { border-bottom: 2px solid #e2e8f0; margin-bottom: 14px; flex-wrap: wrap; }
.rv-tab .nav-link {
  border: 0; border-bottom: 2px solid transparent; margin-bottom: -2px;
  color: #64748b; font-size: 12.5px; font-weight: 600; padding: 9px 14px;
}
.rv-tab .nav-link:hover { color: #1e3a5f; background: #f6f9fd; }
.rv-tab .nav-link.active {
  color: #1e3a5f; background: transparent; border-bottom-color: #2c5282;
}
.rv-tab .nav-link i { margin-right: 5px; color: #94a3b8; }
.rv-tab .nav-link.active i { color: #2c5282; }

/* ===== papan indikator =====
   Lebarnya ikut isi kartu - tidak dipaksa satu baris, supaya di layar
   sempit melipat sendiri dan halamannya tidak perlu digeser ke samping
   (14" s/d 32", tablet, dan HP). */
.rv-papan { display: grid; gap: 10px; grid-template-columns: repeat(auto-fit, minmax(205px, 1fr)); }
@media (min-width: 992px) { .rv-papan { grid-template-columns: repeat(4, 1fr); } }
.rv-kartu { position: relative; overflow: hidden;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 12px 13px 11px; display: flex; gap: 11px; align-items: flex-start;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
            transition: box-shadow .16s ease, transform .16s ease, border-color .16s ease; }
/* Pita aksen kiri dibuat dari ::before, BUKAN border-left: dgn border, sudut
   membulat kartunya ikut terpotong rata di sisi kiri. */
.rv-kartu::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0;
                    width: 3px; background: var(--aksen, #cbd5e1); }
.rv-kartu:hover { box-shadow: 0 6px 18px rgba(15, 23, 42, .09);
                  transform: translateY(-1px); border-color: #cbd5e1; }
/* Lencana ikon. Tiga warna per indikator, bukan satu: --aksen untuk pitanya,
   --aksen-lembut untuk latar lencana, --aksen-tua untuk gambar ikonnya -
   yang tua di atas yang lembut supaya ikonnya tajam, bukan pucat. */
.rv-k-ikon { flex: 0 0 34px; width: 34px; height: 34px; border-radius: 10px;
             display: flex; align-items: center; justify-content: center;
             font-size: 14px; background: var(--aksen-lembut, #f1f5f9);
             color: var(--aksen-tua, #475569); }
.rv-k-isi { min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.rv-k-judul { font-size: 9.5px; font-weight: 700; letter-spacing: .4px; line-height: 1.35;
              text-transform: uppercase; color: #64748b; }
.rv-k-nilai { font-size: 17px; font-weight: 700; color: #0f172a;
              font-variant-numeric: tabular-nums; line-height: 1.2; }
.rv-k-kaki  { font-size: 10px; color: #94a3b8; line-height: 1.35; }
.rv-kartu.c-proj       { --aksen: #60a5fa; --aksen-lembut: #eff6ff; --aksen-tua: #1d4ed8; }
.rv-kartu.c-real       { --aksen: #34d399; --aksen-lembut: #ecfdf5; --aksen-tua: #047857; }
.rv-kartu.c-capai      { --aksen: #818cf8; --aksen-lembut: #eef2ff; --aksen-tua: #4338ca; }
.rv-kartu.c-var        { --aksen: #fbbf24; --aksen-lembut: #fffbeb; --aksen-tua: #b45309; }
.rv-kartu.c-fx         { --aksen: #c084fc; --aksen-lembut: #faf5ff; --aksen-tua: #7e22ce; }
.rv-kartu.c-belum      { --aksen: #fb7185; --aksen-lembut: #fff1f2; --aksen-tua: #be123c; }
.rv-kartu.c-sebagian   { --aksen: #facc15; --aksen-lembut: #fefce8; --aksen-tua: #a16207; }
.rv-kartu.c-takrencana { --aksen: #f59e0b; --aksen-lembut: #fffbeb; --aksen-tua: #b45309; }
.rv-kartu.c-waktu      { --aksen: #38bdf8; --aksen-lembut: #f0f9ff; --aksen-tua: #0369a1; }
/* Rekonsiliasi: hijau kalau nol, merah kalau tidak - satu-satunya angka di
   papan ini yang punya nilai "benar". Statusnya TIDAK disampaikan lewat warna
   saja; ikonnya ikut berganti centang / segitiga seru, lihat gambarRingkas(). */
.rv-kartu.c-rekon.pas     { --aksen: #22c55e; --aksen-lembut: #f0fdf4; --aksen-tua: #15803d; }
.rv-kartu.c-rekon.pas .rv-k-nilai { color: #15803d; }
.rv-kartu.c-rekon.meleset { --aksen: #ef4444; --aksen-lembut: #fef2f2; --aksen-tua: #b91c1c;
                            background: #fffbfb; }
.rv-kartu.c-rekon.meleset .rv-k-nilai { color: #b91c1c; }

.rv-papan-judul { display: flex; align-items: center; gap: 10px; margin: 20px 0 10px; }
.rv-papan-judul:first-child { margin-top: 0; }
.rv-pj-ikon { width: 26px; height: 26px; border-radius: 8px; background: #eef4ff;
              color: #2c5282; display: flex; align-items: center;
              justify-content: center; font-size: 12px; flex: 0 0 26px; }
.rv-papan-judul b { font-size: 12.5px; color: #0f172a; }
.rv-papan-judul small { color: #94a3b8; font-size: 11px; }
/* Garis rambut yang memudar ke kanan - memisahkan bagian tanpa kotak
   tambahan. Disembunyikan di layar sempit supaya judulnya tidak terjepit. */
.rv-pj-garis { flex: 1 1 40px; height: 1px; min-width: 24px;
               background: linear-gradient(90deg, #e2e8f0, rgba(226, 232, 240, 0)); }
@media (max-width: 575px) { .rv-pj-garis { display: none; }
                            .rv-papan-judul { flex-wrap: wrap; } }

/* ===== rekap relasi x jenis barang =====
   Tingginya dibatasi dan digulung di dalam kotaknya sendiri - kombinasinya
   bisa belasan dan tidak boleh mendorong isi tab ini jauh ke bawah. */
.rv-rekap-kotak { max-height: 330px; overflow-y: auto; border: 1px solid #e8edf4;
                  border-radius: 9px; background: #fff; }
table.rv-rekap { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12px; }
table.rv-rekap th {
  position: sticky; top: 0; z-index: 2; background: #1e3a5f; color: #f8fafc;
  font-size: 10.5px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase;
  text-align: left; padding: 8px 11px; white-space: nowrap;
  border-right: 1px solid rgba(255, 255, 255, .10); border-bottom: 2px solid #0f2942;
}
table.rv-rekap th:last-child { border-right: 0; }
table.rv-rekap td { padding: 6px 11px; border-bottom: 1px solid #f3f6fa; color: #0f172a; }
table.rv-rekap tbody tr:hover td { background: #f8fafc; }
table.rv-rekap th.ang, table.rv-rekap td.ang { text-align: right; font-variant-numeric: tabular-nums; }
table.rv-rekap tfoot td { font-weight: 700; background: #f8fafc; border-top: 2px solid #e2e8f0; }

/* ===== tabel laporan =====
   Tiga tabelnya LEBAR, jadi yang digulir ke samping hanya kotak di
   sekeliling tabelnya - bukan kartunya, supaya Search & paging DataTables
   tidak ikut bergeser (pelajaran dari Projection Report: aturan perataan
   DataTables diikat ke div.dataTables_wrapper). */
#rv-tbl_wrapper .rv-scroll, #rv-real-tbl_wrapper .rv-scroll,
#rv-proy-tbl_wrapper .rv-scroll { overflow-x: auto; }
#rv-tbl th, #rv-tbl td, #rv-real-tbl th, #rv-real-tbl td,
#rv-proy-tbl th, #rv-proy-tbl td { white-space: nowrap; }
#rv-tbl td.rv-supp, #rv-real-tbl td.rv-supp, #rv-proy-tbl td.rv-supp { white-space: normal; min-width: 170px; }

/* Warna kepala tabel sama persis dgn Projection Report. !important WAJIB:
   app-ftr-list.css mengunci background-color/color/border pada
   .ftl-tbl thead th dgn !important, dan !important mengalahkan aturan biasa
   berapa pun spesifisitasnya. */
#rv-tbl thead th, #rv-real-tbl thead th, #rv-proy-tbl thead th {
  background-color: #1e3a5f !important;
  color: #f8fafc !important;
  font-weight: 600; font-size: 11px; letter-spacing: .4px; text-transform: uppercase;
  text-align: center; vertical-align: middle;
  border: 0 !important;
  border-right: 1px solid rgba(255, 255, 255, .10) !important;
  border-bottom: 2px solid #0f2942 !important;
}
/* Tiga payung kelompok, warnanya memisahkan arti: proyeksi biru, realisasi
   hijau kebiruan, hasil & selisih kuning tua. */
#rv-tbl thead th.g-prj, #rv-proy-tbl thead th.g-prj { background-color: #2c5282 !important; }
#rv-tbl thead th.s-prj, #rv-proy-tbl thead th.s-prj { background-color: #3563a0 !important; }
#rv-tbl thead th.g-real, #rv-real-tbl thead th.g-real { background-color: #115e59 !important; }
#rv-tbl thead th.s-real, #rv-real-tbl thead th.s-real { background-color: #0f766e !important; }
#rv-tbl thead th.g-res  { background-color: #854d0e !important; }
#rv-tbl thead th.s-res  { background-color: #a16207 !important; }
/* SUDUT MEMBULAT - sumber "garis putih" di kepala tabel.
   app-ftr-list.css membulatkan sudut atas lewat .ftl-tbl thead th:first-child
   dan :last-child. Aturan itu ditulis untuk kepala SATU tingkat. Di sini
   kepalanya DUA tingkat, jadi :first-child juga mengenai sel pertama BARIS
   KEDUA (Payment Plan Date) dan :last-child sel terakhir baris kedua -
   sudutnya tercukil di tengah-tengah deretan warna, dan yang menyembul dari
   balik cukilan itu latar putih halaman.
   Jadi: dinolkan untuk SEMUA sel kepala, lalu dikembalikan hanya pada dua
   sudut yang memang sudut luar tabel. */
#rv-tbl thead th, #rv-real-tbl thead th, #rv-proy-tbl thead th {
  border-radius: 0;
}
#rv-tbl thead tr:first-child th:first-child,
#rv-real-tbl thead tr:first-child th:first-child,
#rv-proy-tbl thead tr:first-child th:first-child { border-top-left-radius: 10px; }
#rv-tbl thead tr:first-child th:last-child,
#rv-real-tbl thead tr:first-child th:last-child,
#rv-proy-tbl thead tr:first-child th:last-child { border-top-right-radius: 10px; }

/* Aturan :first-child milik DataTables Responsive memipihkan sel pertama;
   kepalanya DUA TINGKAT, jadi aturan itu mengenai dua sel sekaligus. */
#rv-tbl thead th:first-child, #rv-real-tbl thead th:first-child,
#rv-proy-tbl thead th:first-child {
  width: auto; padding-left: 10px !important; padding-right: 10px !important;
}

/* Lencana status pembayaran. */
.rv-st { display: inline-block; padding: 2px 9px; border-radius: 20px;
         font-size: 10.5px; font-weight: 700; white-space: nowrap; }
.rv-st.paid      { background: #d1fae5; color: #065f46; }
.rv-st.partially { background: #fef3c7; color: #92400e; }
.rv-st.notpaid   { background: #fee2e2; color: #991b1b; }
.rv-st.unplanned { background: #ffedd5; color: #9a3412; }

.rv-neg { color: #b91c1c; }
.rv-pos { color: #15803d; }
.rv-kosong { color: #cbd5e1; }

/* Kolom penjelasan. Kolomnya sengaja dibiarkan kosong (permintaan 9 Nov 2026) - lebarnya
   tetap dipatok supaya tempat menulisnya kelihatan di Excel hasil ekspor. */
.rv-exp { min-width: 170px; white-space: normal !important; }

.rv-warn { background: #fdecea; border: 1px solid #f5c2bd; color: #a3281c;
           border-radius: 8px; padding: 9px 12px; margin-bottom: 10px; font-size: 12.5px; }
</style>

<div class="container-fluid mt-3 p-3">

    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-balance-scale" aria-hidden="true"></i></span>
            <div>
                <h1>AP Payment Realization &amp; Variance Report</h1>
                <span class="ftl-crumb">AP &rsaquo; Report &rsaquo; Projection &rsaquo; Realization &amp; Variance</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-body">
            <div class="rv-kepala">
                <div class="rv-dok">
                    <span class="rv-dok-ikon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
                    <span class="rv-dok-teks">
                        <span class="rv-dok-no" id="rv-doc">-</span>
                        <span class="rv-dok-ket">
                            Projection document <span class="rv-badge lain" id="rv-tipe"></span>
                        </span>
                    </span>
                </div>

                <span class="rv-garis"></span>

                <div class="rv-ruas">
                    <span class="rv-label">Period</span>
                    <span class="rv-nilai" id="rv-periode">-</span>
                </div>
                <div class="rv-ruas">
                    <span class="rv-label">Supplier</span>
                    <span class="rv-nilai" id="rv-supplier">-</span>
                </div>
                <div class="rv-ruas">
                    <span class="rv-label">Saved by</span>
                    <span class="rv-nilai" id="rv-oleh">-</span>
                </div>

                <div class="rv-aksi">
                    <button type="button" class="app-btn app-btn-success app-btn-ctl app-btn-sm" id="rv-ekspor">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export
                    </button>
                    <button type="button" class="app-btn app-btn-danger app-btn-ctl app-btn-sm" id="rv-kembali">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i> Back
                    </button>
                </div>
            </div>
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

    <div class="ftl-card mt-3">
        <div class="ftl-body">

            <ul class="nav nav-tabs rv-tab" id="rv-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#rv-pane-ringkas" role="tab">
                        <i class="fa fa-tachometer" aria-hidden="true"></i> Executive Summary
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#rv-pane-utama" role="tab">
                        <i class="fa fa-balance-scale" aria-hidden="true"></i> AP Payment Realization &amp; Variance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#rv-pane-real" role="tab">
                        <i class="fa fa-money" aria-hidden="true"></i> Realization
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#rv-pane-proy" role="tab">
                        <i class="fa fa-line-chart" aria-hidden="true"></i> Projection
                    </a>
                </li>
            </ul>

            <div id="rv-peringatan"></div>

            <!-- Tirai loadingnya menutup seluruh isi tab - datanya memang
                 satu tarikan untuk keempat tabnya. -->
            <div class="app-loading-wrap" id="rvLoad">
                <div class="app-loading">
                    <div class="app-loading-box">
                        <div class="app-spinner"></div>
                        <div class="app-loading-text">Loading data...</div>
                    </div>
                </div>

                <div class="tab-content">

                    <div class="tab-pane active" id="rv-pane-ringkas" role="tabpanel">
                        <div class="rv-papan-judul">
                            <span class="rv-pj-ikon"><i class="fas fa-tachometer-alt" aria-hidden="true"></i></span>
                            <span><b>Cash Flow Performance</b>
                            <small>&nbsp;Projected versus actual cash outflow for this document</small></span>
                            <span class="rv-pj-garis"></span>
                        </div>
                        <div class="rv-papan" id="rv-papan-kas"></div>

                        <div class="rv-papan-judul">
                            <span class="rv-pj-ikon"><i class="fas fa-tasks" aria-hidden="true"></i></span>
                            <span><b>Payment Execution</b>
                            <small>&nbsp;What was planned, what was paid, and what was not</small></span>
                            <span class="rv-pj-garis"></span>
                        </div>
                        <div class="rv-papan" id="rv-papan-eksekusi"></div>

                        <div class="rv-papan-judul">
                            <span class="rv-pj-ikon"><i class="fas fa-clock" aria-hidden="true"></i></span>
                            <span><b>Payment Risk &amp; Timeliness</b>
                            <small>&nbsp;Measured against the payment plan date, not the contractual due date</small></span>
                            <span class="rv-pj-garis"></span>
                        </div>
                        <div class="rv-papan" id="rv-papan-waktu"></div>

                        <div class="rv-papan-judul">
                            <span class="rv-pj-ikon"><i class="fas fa-table" aria-hidden="true"></i></span>
                            <span><b>Summary</b>
                            <small>&nbsp;By relationship and item type</small></span>
                            <span class="rv-pj-garis"></span>
                        </div>
                        <div class="rv-rekap-kotak">
                            <table class="rv-rekap" id="rv-rekap">
                                <thead>
                                    <tr>
                                        <th>Relationship</th>
                                        <th>Item Type</th>
                                        <th class="ang">Voucher</th>
                                        <th class="ang">Projected</th>
                                        <th class="ang">Realization</th>
                                        <th class="ang">Variance</th>
                                        <th class="ang">Achievement</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="rv-pane-utama" role="tabpanel">
                        <table id="rv-tbl" class="table ftl-tbl" cellspacing="0">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="rv-pane-real" role="tabpanel">
                        <table id="rv-real-tbl" class="table ftl-tbl" cellspacing="0">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="rv-pane-proy" role="tabpanel">
                        <table id="rv-proy-tbl" class="table ftl-tbl" cellspacing="0">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div><!-- /.tab-content -->
            </div><!-- /.app-loading-wrap -->
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

</div><!-- /.container-fluid -->

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });
  function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) { SeparatorTitle.removeClass('d-flex'); }
      else { SeparatorTitle.addClass('d-flex'); }
      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
$(document).ready(function () {

  var DOC = <?php echo json_encode($docAwal); ?>;
  var AKHIR = null;          /* jawaban terakhir, dipakai keempat tabnya */

  function lolos(v) {
    return $('<div>').text(v === null || v === undefined ? '' : v).html();
  }

  /* ---- format angka & tanggal ----------------------------------------
     Angkanya datang MENTAH dari server; formatnya dipasang di sini supaya
     DataTables mengurutkannya sebagai angka. Nilai null tampil '-', bukan
     0 - "tidak ada angkanya" dan "angkanya nol" dua hal berbeda. */
  function ang(v, dec) {
    if (v === null || v === undefined || v === '') { return '<span class="rv-kosong">-</span>'; }
    var n = Number(v);
    if (isNaN(n)) { return lolos(v); }
    var s = n.toLocaleString('en-US', { minimumFractionDigits: (dec === undefined ? 2 : dec),
                                        maximumFractionDigits: (dec === undefined ? 2 : dec) });
    return (n < 0) ? '<span class="rv-neg">' + s + '</span>' : s;
  }
  function angPolos(v, dec) {
    if (v === null || v === undefined || v === '') { return '-'; }
    var n = Number(v);
    if (isNaN(n)) { return String(v); }
    return n.toLocaleString('en-US', { minimumFractionDigits: (dec === undefined ? 2 : dec),
                                       maximumFractionDigits: (dec === undefined ? 2 : dec) });
  }
  function persen(v) {
    if (v === null || v === undefined || v === '') { return '-'; }
    return (Number(v) * 100).toLocaleString('en-US', { minimumFractionDigits: 2,
                                                       maximumFractionDigits: 2 }) + ' %';
  }
  var RV_BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  /* 'yyyy-mm-dd' -> '01 Oct 2026' (permintaan pemakai 9 Nov 2026).
     Dirakit dari potongan TEKSNYA, bukan lewat new Date(): string ISO
     dibaca browser sbg UTC, dan di zona WIB tanggalnya bisa mundur sehari.

     Yang diurutkan DataTables tetap nilai ISO-nya - renderer kolom tanggal
     hanya memakai bentuk ini untuk 'display'. Jadi mengubah bentuknya tidak
     mengacaukan urutan. */
  function tglRapi(iso) {
    var p = String(iso || '').split('-');
    if (p.length !== 3) { return ''; }
    var b = RV_BULAN[Number(p[1]) - 1];
    return b ? (p[2] + ' ' + b + ' ' + p[0]) : '';
  }
  function tgl(iso) {
    if (!iso) { return '<span class="rv-kosong">-</span>'; }
    var s = tglRapi(iso);
    return s === '' ? lolos(iso) : s;
  }
  function tglPolos(iso) {
    if (!iso) { return '-'; }
    var s = tglRapi(iso);
    return s === '' ? String(iso) : s;
  }
  function hari(v) {
    if (v === null || v === undefined || v === '') { return '<span class="rv-kosong">-</span>'; }
    var n = Number(v);
    /* Tanda dibaca duluan: + berarti LEBIH LAMBAT dari rencana, - lebih
       cepat. Tanpa tanda plusnya, angka 3 dan -3 tampak setara. */
    var s = (n > 0 ? '+' : '') + n;
    if (n > 0)  { return '<span class="rv-neg">' + s + '</span>'; }
    if (n < 0)  { return '<span class="rv-pos">' + s + '</span>'; }
    return s;
  }
  function kelasStatus(s) {
    if (s === 'Paid') { return 'paid'; }
    if (s === 'Partially Paid') { return 'partially'; }
    if (s === 'Not Paid') { return 'notpaid'; }
    return 'unplanned';
  }

  /* ================================================= KEPALA LAPORAN ===== */
  function gambarKepala(info) {
    $('#rv-doc').text(info.doc_number || '-');
    var t = (info.type || '').toLowerCase();
    var sah = (t === 'daily' || t === 'weekly' || t === 'monthly') ? t : 'lain';
    $('#rv-tipe').attr('class', 'rv-badge ' + sah).text(info.type || '');
    $('#rv-periode').text(tglPolos(info.periode_dari) + ' s/d ' + tglPolos(info.periode_sampai));
    $('#rv-supplier').text(info.supplier || 'ALL');
    $('#rv-oleh').text((info.created_by || '-') + ' - ' + (info.created_at || '-'));

    var pesan = '';
    if (!info.utuh) {
      pesan += '<div class="rv-warn"><b>Careful:</b> the projected total in this report '
             + '(' + angPolos(AKHIR.ringkas.projected) + ') is not the same as the total stored '
             + 'in the document header (' + angPolos(info.total_tersimpan) + '). '
             + 'Some snapshot rows are missing.</div>';
    }
    /* Peringatan "tabel ap_realization_variance belum ada" DIBUANG bersama
       jalur inputnya: tidak ada lagi yang disimpan dari layar ini, jadi
       tabelnya tidak lagi diperlukan dan peringatannya cuma menakuti. */
    $('#rv-peringatan').html(pesan);
  }

  /* ============================================== EXECUTIVE SUMMARY ===== */
  /* `ikon` = nama Font Awesome 5 TANPA awalan "fa-" (mis. 'bullseye').
     Berkasnya sudah dimuat header.php (fontawesome5/css/all.min.css). */
  function kartu(kelas, ikon, judul, nilai, kaki) {
    return '<div class="rv-kartu ' + kelas + '">'
         + '<span class="rv-k-ikon"><i class="fas fa-' + ikon + '" aria-hidden="true"></i></span>'
         + '<span class="rv-k-isi">'
         + '<span class="rv-k-judul">' + judul + '</span>'
         + '<span class="rv-k-nilai">' + nilai + '</span>'
         + '<span class="rv-k-kaki">' + (kaki || '') + '</span>'
         + '</span></div>';
  }

  function gambarRingkas(r) {
    /* Nama & urutan indikatornya mengikuti lembar Executive Summary di
       berkas contohnya, termasuk keterangan kecil di bawah angkanya. */
    /* Rekonsiliasi dihitung lebih dulu karena dipakai DUA kali: untuk kelas
       warnanya dan untuk ikonnya. */
    var rekonPas = Math.abs(Number(r.rekonsiliasi)) <= 0.05;

    var kas = ''
      + kartu('c-proj',  'bullseye', 'Projected Payment', angPolos(r.projected),
              'Approved projected cash outflow')
      + kartu('c-real',  'money-bill-wave', 'Actual Payment', angPolos(r.actual),
              'Actual cash outflow')
      + kartu('c-capai', 'percentage', 'Payment Realization Ratio', persen(r.ratio),
              'Ratio, not a performance score')
      + kartu('c-var',   'not-equal', 'Net Cash Flow Variance', angPolos(r.net_variance),
              'Positive = higher cash outflow than projected')
      + kartu('c-var',   'calculator', 'Payment Variance (Excl. FX)', angPolos(r.variance_excl_fx),
              'Variance excluding exchange differences')
      + kartu('c-fx',    'exchange-alt', 'FX Variance (Source)', angPolos(r.fx_variance),
              'Exchange difference on realized OCY')
      + kartu('c-rekon ' + (rekonPas ? 'pas' : 'meleset'),
              rekonPas ? 'check-circle' : 'exclamation-triangle',
              'Reconciliation Check', angPolos(r.rekonsiliasi),
              'Actual - Projected - Total Variance. Must be zero.');
    $('#rv-papan-kas').html(kas);

    var eks = ''
      + kartu('c-real',  'check-double', 'Planned Payments Realized', angPolos(r.planned_realized),
              'Actual paid against planned vouchers')
      + kartu('c-takrencana', 'bolt', 'Unplanned Payments', angPolos(r.unplanned),
              'Actual payments without a planned date')
      + kartu('c-belum', 'hourglass-half', 'Planned Amount Not Executed', angPolos(r.not_executed),
              'Full projected IDR amount of unpaid vouchers')
      + kartu('c-belum', 'file-invoice', 'Not Paid Vouchers', r.jml_not_paid + ' of ' + r.jml_voucher,
              'Count of unpaid planned vouchers')
      + kartu('c-sebagian', 'adjust', 'Partially Paid Vouchers', String(r.jml_partial),
              'Count of partially paid vouchers');
    $('#rv-papan-eksekusi').html(eks);

    var wkt = ''
      + kartu('c-belum', 'exclamation-triangle', 'Overdue Unpaid Planned Amount',
              angPolos(r.overdue_not_paid), 'Due before the reporting date and still unpaid')
      + kartu('c-real',  'check-circle', 'On-time / Early Paid Vouchers', String(r.jml_ontime),
              'Versus planned date, not contractual due date')
      + kartu('c-var',   'clock', 'Late Paid Vouchers', String(r.jml_late),
              'Versus planned date')
      + kartu('c-waktu', 'stopwatch', 'On-time Payment Rate', persen(r.ontime_rate),
              'Based on fully paid vouchers with a date variance');
    $('#rv-papan-waktu').html(wkt);

    var tb = '', tp = 0, ta = 0, tj = 0;
    for (var i = 0; i < r.rekap.length; i++) {
      var b = r.rekap[i];
      tp += Number(b.projected); ta += Number(b.actual); tj += Number(b.jml);
      tb += '<tr><td>' + lolos(b.relasi) + '</td><td>' + lolos(b.item_type) + '</td>'
          + '<td class="ang">' + b.jml + '</td>'
          + '<td class="ang">' + angPolos(b.projected) + '</td>'
          + '<td class="ang">' + angPolos(b.actual) + '</td>'
          + '<td class="ang">' + angPolos(b.variance) + '</td>'
          + '<td class="ang">' + persen(b.achievement) + '</td></tr>';
    }
    if (!r.rekap.length) {
      tb = '<tr><td colspan="7" style="text-align:center;color:#94a3b8;">No rows.</td></tr>';
    }
    $('#rv-rekap tbody').html(tb);
    /* Kolom Variance di rekap ini Projected - Realization, mengikuti lembar
       contohnya - bukan Actual - Projected seperti di papan atas. */
    $('#rv-rekap tfoot').html('<tr><td>TOTAL</td><td></td>'
      + '<td class="ang">' + tj + '</td>'
      + '<td class="ang">' + angPolos(tp) + '</td>'
      + '<td class="ang">' + angPolos(ta) + '</td>'
      + '<td class="ang">' + angPolos(tp - ta) + '</td>'
      + '<td class="ang">' + persen(tp != 0 ? ta / tp : null) + '</td></tr>');
  }

  /* ==================================================== TABEL UTAMA ===== */
  /* Susunan kolomnya SAMA PERSIS dgn lembar "AP Payment Realization & Var":
     tujuh kolom keterangan, lalu tiga payung Projection / Realization /
     Result. Jangan diubah urutannya tanpa mengubah lembar Excel-nya juga -
     dua susunan berbeda untuk laporan yang sama itu yang paling
     membingungkan waktu diadu. */
  var KOL_UTAMA = [
    { data: 'no',        title: 'No',                  kelas: 'text-center' },
    { data: 'supplier',  title: 'Supplier',            kelas: 'text-left rv-supp' },
    { data: 'no_kbon',   title: 'Payment Voucher No',  kelas: 'text-left' },
    { data: 'relasi',    title: 'Relationship',        kelas: 'text-center' },
    { data: 'item_type', title: 'Item Type',           kelas: 'text-center' },
    { data: 'tgl_kbon',  title: 'PV Date',             kelas: 'text-center', jenis: 'tgl' },
    { data: 'duedate',   title: 'Due Date',            kelas: 'text-center', jenis: 'tgl' },
    { data: 'curr',      title: 'Currency',            kelas: 'text-center' },

    { data: 'plan_date', title: 'Payment Plan Date',   kelas: 'text-center', grup: 'prj', jenis: 'tgl' },
    { data: 'proj_ocy',  title: 'Amount in OCY',       kelas: 'text-right',  grup: 'prj', jenis: 'ang' },
    { data: 'proj_rate', title: 'Rate',                kelas: 'text-right',  grup: 'prj', jenis: 'ang' },
    { data: 'proj_idr',  title: 'Amount Eqv Idr',      kelas: 'text-right',  grup: 'prj', jenis: 'ang' },

    { data: 'real_date', title: 'Payment Date',        kelas: 'text-center', grup: 'real', jenis: 'tgl' },
    { data: 'real_ocy',  title: 'Amount in OCY',       kelas: 'text-right',  grup: 'real', jenis: 'ang' },
    { data: 'real_rate', title: 'Rate',                kelas: 'text-right',  grup: 'real', jenis: 'ang' },
    { data: 'real_idr',  title: 'Amount Eqv Idr',      kelas: 'text-right',  grup: 'real', jenis: 'ang' },

    { data: 'status',        title: 'Payment Status',        kelas: 'text-center', grup: 'res', jenis: 'status' },
    { data: 'achievement',   title: 'Payment Achievement %', kelas: 'text-right',  grup: 'res', jenis: 'persen' },
    { data: 'date_variance', title: 'Payment Date Variance', kelas: 'text-center', grup: 'res', jenis: 'hari' },
    { data: 'days_past_due', title: 'Days Past Due',         kelas: 'text-center', grup: 'res', jenis: 'hari' },
    { data: 'explanation',   title: 'Variance Explanation',  kelas: 'text-left',   grup: 'res', jenis: 'exp' },
    { data: 'variance_excl_fx', title: 'Payment Variance (Excl. FX)', kelas: 'text-right', grup: 'res', jenis: 'ang' },
    { data: 'fx_variance',      title: 'FX Variance',   kelas: 'text-right', grup: 'res', jenis: 'ang' },
    { data: 'total_variance',   title: 'Total Variance', kelas: 'text-right', grup: 'res', jenis: 'ang' }
  ];
  var GRUP_UTAMA = { prj: 'Projection', real: 'Realization', res: 'Result' };

  var tblUtama = null, tblReal = null, tblProy = null;

  function bungkus(id, kelas) {
    var t = $('#' + id);
    if (!t.parent().hasClass(kelas)) { t.wrap('<div class="' + kelas + '"></div>'); }
  }
  function bongkar(id, kelas) {
    var t = $('#' + id);
    if (t.parent().hasClass(kelas)) { t.unwrap(); }
  }

  /* Kepala dua tingkat: kolom tanpa grup memanjang ke bawah (rowspan 2),
     kolom bergrup bernaung di bawah payungnya. DataTables memakai sel
     PALING BAWAH sbg kolomnya, jadi urutan isiannya tetap lurus. */
  function headDuaTingkat(kolom, grup, kelasGrup) {
    var atas = '', bawah = '', sudah = {};
    for (var i = 0; i < kolom.length; i++) {
      var k = kolom[i];
      if (!k.grup) {
        atas += '<th rowspan="2">' + lolos(k.title) + '</th>';
        continue;
      }
      if (!sudah[k.grup]) {
        var n = 0;
        for (var j = 0; j < kolom.length; j++) { if (kolom[j].grup === k.grup) { n++; } }
        atas += '<th colspan="' + n + '" class="g-' + kelasGrup(k.grup) + '">'
              + lolos(grup[k.grup]) + '</th>';
        sudah[k.grup] = true;
      }
      bawah += '<th class="s-' + kelasGrup(k.grup) + '">' + lolos(k.title) + '</th>';
    }
    return '<tr>' + atas + '</tr><tr>' + bawah + '</tr>';
  }

  function kolomDataTables(kolom) {
    var kol = [], defs = [];
    for (var i = 0; i < kolom.length; i++) {
      var k = kolom[i];
      var kd = { data: k.data };
      if (k.jenis === 'ang') {
        kd.render = function (d, t) { return (t === 'display') ? ang(d) : (d === null ? -1e18 : Number(d)); };
      } else if (k.jenis === 'tgl') {
        kd.render = function (d, t) { return (t === 'display') ? tgl(d) : (d || ''); };
      } else if (k.jenis === 'persen') {
        kd.render = function (d, t) { return (t === 'display') ? persen(d) : (d === null ? -1 : Number(d)); };
      } else if (k.jenis === 'hari') {
        kd.render = function (d, t) { return (t === 'display') ? hari(d) : (d === null ? -1e9 : Number(d)); };
      } else if (k.jenis === 'status') {
        kd.render = function (d, t) {
          if (t !== 'display') { return d; }
          return '<span class="rv-st ' + kelasStatus(d) + '">' + lolos(d) + '</span>';
        };
      } else if (k.jenis === 'exp') {
        /* Kosong ya kosong - tanpa ajakan mengisi dan tanpa ikon pensil. */
        kd.render = function (d, t) {
          if (t !== 'display') { return d || ''; }
          return (d === null || d === undefined || d === '') ? '' : lolos(d);
        };
      }
      kol.push(kd);
      var kelas = k.kelas + (k.jenis === 'exp' ? ' rv-exp' : '');
      defs.push({ targets: i, className: kelas });
    }
    return { kolom: kol, defs: defs };
  }

  function gambarUtama(baris) {
    if (tblUtama) { tblUtama.destroy(); tblUtama = null; }
    bongkar('rv-tbl', 'rv-scroll');
    $('#rv-tbl thead').empty(); $('#rv-tbl tbody').empty();
    $('#rv-tbl thead').html(headDuaTingkat(KOL_UTAMA, GRUP_UTAMA, function (g) { return g; }));

    var s = kolomDataTables(KOL_UTAMA);
    tblUtama = $('#rv-tbl').DataTable({
      data: baris, columns: s.kolom, columnDefs: s.defs,
      order: [], pageLength: 10, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      language: { emptyTable: 'This document has no vouchers.' }
    });
    bungkus('rv-tbl', 'rv-scroll');
  }

  /* ================================================ TABEL REALISASI ===== */
  var KOL_REAL = [
    { data: 'no',         title: 'No',              kelas: 'text-center' },
    { data: 'no_payment', title: 'No Payment',      kelas: 'text-left' },
    { data: 'pay_date',   title: 'Payment Date',    kelas: 'text-center', jenis: 'tgl' },
    { data: 'supplier',   title: 'Supplier',        kelas: 'text-left rv-supp' },
    { data: 'no_kbon',    title: 'No Kontrabon',    kelas: 'text-left' },
    { data: 'tgl_kbon',   title: 'Kontrabon Date',  kelas: 'text-center', jenis: 'tgl' },
    { data: 'relasi',     title: 'Relationship',    kelas: 'text-center' },
    { data: 'item_type',  title: 'Item Type',       kelas: 'text-center' },
    { data: 'curr',       title: 'Currency',        kelas: 'text-center' },

    { data: 'rate',          title: 'Rate',              kelas: 'text-right', grup: 'ocy', jenis: 'ang' },
    { data: 'tax_base',      title: 'Tax Base',          kelas: 'text-right', grup: 'ocy', jenis: 'ang' },
    { data: 'vat',           title: 'VAT',               kelas: 'text-right', grup: 'ocy', jenis: 'ang' },
    { data: 'total_invoice', title: 'Total Invoice',     kelas: 'text-right', grup: 'ocy', jenis: 'ang' },
    { data: 'pph',           title: 'Income Tax Art 23', kelas: 'text-right', grup: 'ocy', jenis: 'ang' },
    { data: 'pay_amount',    title: 'Payment Amount',    kelas: 'text-right', grup: 'ocy', jenis: 'ang' },

    { data: 'tax_base_idr',      title: 'Tax Base',          kelas: 'text-right', grup: 'idr', jenis: 'ang' },
    { data: 'vat_idr',           title: 'VAT',               kelas: 'text-right', grup: 'idr', jenis: 'ang' },
    { data: 'total_invoice_idr', title: 'Total Invoice',     kelas: 'text-right', grup: 'idr', jenis: 'ang' },
    { data: 'pph_idr',           title: 'Income Tax Art 23', kelas: 'text-right', grup: 'idr', jenis: 'ang' },
    { data: 'pay_amount_idr',    title: 'Payment Amount',    kelas: 'text-right', grup: 'idr', jenis: 'ang' }
  ];
  var GRUP_REAL = { ocy: 'In Original Currency', idr: 'In IDR' };

  function gambarReal(baris) {
    if (tblReal) { tblReal.destroy(); tblReal = null; }
    bongkar('rv-real-tbl', 'rv-scroll');
    $('#rv-real-tbl thead').empty(); $('#rv-real-tbl tbody').empty();
    $('#rv-real-tbl thead').html(headDuaTingkat(KOL_REAL, GRUP_REAL, function () { return 'real'; }));

    var s = kolomDataTables(KOL_REAL);
    tblReal = $('#rv-real-tbl').DataTable({
      data: baris, columns: s.kolom, columnDefs: s.defs,
      order: [], pageLength: 10, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      language: { emptyTable: 'No payment was recorded in this period.' }
    });
    bungkus('rv-real-tbl', 'rv-scroll');
  }

  /* ================================================= TABEL PROYEKSI ===== */
  /* Susunannya sama dgn lembar "Projection" di berkas contohnya, yaitu
     susunan ekspor Projection Report - supaya dokumen yang sama tidak perlu
     dibaca dgn dua susunan berbeda. */
  var KOL_PROY = [
    { data: 'no',          title: 'No',                    kelas: 'text-center' },
    { data: 'supplier',    title: 'Supplier',              kelas: 'text-left rv-supp' },
    { data: 'no_kbon',     title: 'Payment Voucher No',    kelas: 'text-left' },
    { data: 'tgl_kbon',    title: 'PV Date',               kelas: 'text-center', jenis: 'tgl' },
    { data: 'term',        title: 'Payment Term',          kelas: 'text-center' },
    { data: 'duedate',     title: 'Due Date',              kelas: 'text-center', jenis: 'tgl' },
    { data: 'plan_date',   title: 'Payment Plan Date',     kelas: 'text-center', jenis: 'tgl' },
    { data: 'relasi',      title: 'Relationship',          kelas: 'text-center' },
    { data: 'item_type',   title: 'Item Type',             kelas: 'text-center' },
    { data: 'flexibility', title: 'Flexibility Category',  kelas: 'text-center' },
    { data: 'curr',        title: 'Currency',              kelas: 'text-center' },
    { data: 'invoice_amount', title: 'Invoice Amount',     kelas: 'text-right', jenis: 'ang' },
    { data: 'rate',        title: 'Rate',                  kelas: 'text-right', jenis: 'ang' },

    { data: 'tax_base',       title: 'Tax Base',          kelas: 'text-right', grup: 'pay', jenis: 'ang' },
    { data: 'vat',            title: 'VAT',               kelas: 'text-right', grup: 'pay', jenis: 'ang' },
    { data: 'total_invoice',  title: 'Total Invoice',     kelas: 'text-right', grup: 'pay', jenis: 'ang' },
    { data: 'pph',            title: 'Income Tax Art 23', kelas: 'text-right', grup: 'pay', jenis: 'ang' },
    { data: 'payment_amount', title: 'Payment Amount',    kelas: 'text-right', grup: 'pay', jenis: 'ang' },
    { data: 'outstanding',    title: 'Projected Cash Outflow', kelas: 'text-right', grup: 'pay', jenis: 'ang' }
  ];
  var GRUP_PROY = { pay: 'Payable Amount' };

  function gambarProy(baris) {
    if (tblProy) { tblProy.destroy(); tblProy = null; }
    bongkar('rv-proy-tbl', 'rv-scroll');
    $('#rv-proy-tbl thead').empty(); $('#rv-proy-tbl tbody').empty();
    $('#rv-proy-tbl thead').html(headDuaTingkat(KOL_PROY, GRUP_PROY, function () { return 'prj'; }));

    var s = kolomDataTables(KOL_PROY);
    tblProy = $('#rv-proy-tbl').DataTable({
      data: baris, columns: s.kolom, columnDefs: s.defs,
      order: [], pageLength: 10, lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      language: { emptyTable: 'This document has no snapshot rows.' }
    });
    bungkus('rv-proy-tbl', 'rv-scroll');
  }

  /* ========================================================= MUAT ======= */
  function muat() {
    if (!DOC) {
      $('#rv-peringatan').html('<div class="rv-warn">No projection document was given. '
        + 'Open this report from the Realization button in AP History Projection.</div>');
      $('#rvLoad').removeClass('is-loading');
      return;
    }
    $('#rvLoad').addClass('is-loading');
    $.post('ajx_ap_realization_report.php', { doc_number: DOC }, null, 'json')
      .done(function (res) {
        if (!res || res.error) {
          $('#rv-peringatan').html('<div class="rv-warn">'
            + lolos(res && res.error ? res.error : 'The report could not be loaded.') + '</div>');
          return;
        }
        AKHIR = res;
        gambarKepala(res.info);
        gambarRingkas(res.ringkas);
        gambarUtama(res.data);
        gambarReal(res.realisasi);
        gambarProy(res.proyeksi);
      })
      .fail(function () {
        $('#rv-peringatan').html('<div class="rv-warn">The server could not be reached.</div>');
      })
      .always(function () { $('#rvLoad').removeClass('is-loading'); });
  }

  /* Kolom DataTables yang digambar waktu tabnya masih tersembunyi lebarnya
     salah hitung - lebarnya baru bisa diukur sesudah tabnya tampil. */
  $('#rv-tab a[data-toggle="tab"]').on('shown.bs.tab', function () {
    if (tblUtama) { tblUtama.columns.adjust(); }
    if (tblReal)  { tblReal.columns.adjust(); }
    if (tblProy)  { tblProy.columns.adjust(); }
  });

  $('#rv-ekspor').on('click', function () {
    if (!DOC) { return; }
    window.location = 'ekspor_ap_realization_report.php?doc_number=' + encodeURIComponent(DOC);
  });
  $('#rv-kembali').on('click', function () {
    window.location = 'ap_history_projection.php';
  });

  muat();
});
</script>
