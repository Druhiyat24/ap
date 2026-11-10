<?php
/* ============================================================================
   AP PAYMENT PROJECTION REPORT - layarnya.
   Datanya dari ajx_ap_projection_report.php; keterangan panjang soal
   KEBIJAKAN laporan (apa itu "masih berutang", tanggal mana yang dipakai,
   rumus kolom uang) ada di berkas itu, tidak diulang di sini.

   SATU HAL KHAS HALAMAN INI: JUMLAH KOLOMNYA BERUBAH-UBAH. Kolom tanggal
   dibuat dari periode yang diminta (harian / mingguan / bulanan), jadi
   DataTables-nya DIBONGKAR dan dirakit ulang tiap kali Search ditekan -
   bukan sekadar ajax.reload(), karena reload tidak bisa mengubah susunan
   kolom. Daftar kolomnya ikut dikirim server di JSON.

   Susunan tampilannya mengikuti list-payment-plan.php / petty-cashout.php:
   kartu saringan di atas, kartu tabel di bawah, kosakata .ftl- dan .app-btn.
   ============================================================================ */
include '../header.php';
?>
<?php
/* Daftar supplier untuk saringan - hanya yang punya voucher, jadi tidak
   sepanjang master supplier. */
$supplierList = array();
$qSup = mysqli_query($conn1, "SELECT DISTINCT nama_supp FROM kontrabon_h
        WHERE nama_supp IS NOT NULL AND nama_supp <> '' ORDER BY nama_supp ASC");
if ($qSup) { while ($r = mysqli_fetch_assoc($qSup)) { $supplierList[] = $r['nama_supp']; } }

/* Saringan Relationship & Item Type DIHAPUS (diminta pemakai 5 Nov 2026),
   berikut query daftar Item Type yang dulu mengisinya. KOLOM Relationship &
   Item Type di tabel TETAP ADA - yang dibuang saringannya, bukan datanya. */
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* Tabel ini LEBAR (18 kolom tetap + sebanyak-banyaknya kolom tanggal), jadi
   di sini - BEDA dgn daftar lain - pembungkus yang bisa digulir ke samping
   memang diperlukan. Kolom tanggalnya dibuat rapat dan tidak dipatahkan. */
/* Yang digulir ke samping HANYA kotak di sekeliling tabelnya (.apr-scroll,
   dipasang sesudah DataTable jadi), BUKAN #apr-wrap.

   Pernah dicoba sebaliknya - #apr-wrap yang digulir, lalu kendali DataTables
   dipindahkan keluar supaya tidak ikut bergeser. Itu SALAH: seluruh aturan
   perataan DataTables diikat ke "div.dataTables_wrapper ...", jadi begitu
   dipindah keluar, Search & paging kehilangan text-align:right dan berhenti
   di tengah, dan select "Show N" kehilangan width:auto sehingga melebar dan
   kata "entries" turun ke baris berikutnya. Dilaporkan pemakai 6 Nov 2026.

   Dgn kotak gulirnya di DALAM pembungkus, kendalinya tidak ikut bergeser
   TANPA perlu memindahkan apa pun - dan tidak ada satu pun aturan DataTables
   yang perlu ditiru ulang di sini. */
#apr-tbl_wrapper .apr-scroll { overflow-x: auto; }
#apr-tbl { width: 100%; }
#apr-tbl th, #apr-tbl td { white-space: nowrap; }
#apr-tbl td.apr-supp, #apr-tbl th.apr-supp { white-space: normal; min-width: 190px; }
/* ---- WARNA KEPALA TABEL, SATU NADA PER BAGIAN -------------------------
   Palet diambil dari laporan proyeksi AR (ar_dev
   application/views/arnag/report/projection_report.php): kolom biasa gelap,
   payung "Payable Amount" biru, payung kolom tanggal teal. Di tabel selebar
   ini itu yang membuat mata langsung tahu sedang berada di bagian mana.
   Dipilih pemakai 6 Nov 2026 ("A saja") dari tiga arah yang dimockup.

   TIGA warna pertama sama persis dgn AR: #1e3a5f, #2c5282, #3563a0.

   PASANGAN TEAL-nya DIGESER satu langkah lebih gelap, dan ini disengaja.
   AR memakai #0f766e untuk payungnya dan #0d9488 untuk anaknya. #0d9488
   cuma 3,58:1 terhadap teks putih - di bawah ambang keterbacaan 4,5:1, dan
   itu sebabnya label tanggal di AR memang agak pudar. Kalau hanya anaknya
   yang digelapkan, payung dan anak jadi sewarna. Jadi dua-duanya turun satu
   langkah: payung #115e59 (7,25:1), anak #0f766e (5,23:1) - selisih
   terangnya tetap searah dgn AR (payung lebih gelap) dan dua-duanya
   terbaca.

   Jangan "dirapikan" kembali ke #0f766e/#0d9488 agar sama dgn AR: itu
   menurunkan keterbacaan, bukan membetulkan ketidaksamaan.

   Kelasnya dipasang SESUDAH atribut rowspan/colspan di penggambarnya -
   dua penjaga mencocokkan potongan teks '<th rowspan="2"' dan
   '<th colspan="' + nPayable + '"', jadi jangan disisipkan sebelum itu.

   !important DI SINI WAJIB, bukan kebiasaan buruk. Tabel ini memakai kelas
   .ftl-tbl, dan app-ftr-list.css memasang
       .ftl-tbl thead th { background-color: #eef3fb !important;
                           color: #1e3a8a !important;
                           border: 0 !important;
                           border-bottom: 2px solid #c9d8f0 !important; }
   !important mengalahkan aturan biasa BERAPA PUN spesifisitasnya, jadi
   versi pertama aturan di bawah ini kalah telak dan warnanya tidak pernah
   muncul sama sekali - ketahuan dari tangkapan layar pemakai, bukan dari
   penjaga. Dgn sama-sama !important, yang menang spesifisitas tertinggi,
   dan #apr-tbl ... lebih tinggi dari .ftl-tbl ...

   Properti yang TIDAK dilawan !important (font-size, letter-spacing,
   text-transform, text-align) sengaja dibiarkan polos. */
#apr-tbl thead th {
  background-color: #1e3a5f !important;
  color: #f8fafc !important;
  font-weight: 600;
  font-size: 11px;
  letter-spacing: .4px;
  text-transform: uppercase;
  text-align: center;
  vertical-align: middle;
  border: 0 !important;
  border-right: 1px solid rgba(255, 255, 255, .10) !important;
  border-bottom: 2px solid #0f2942 !important;
}
#apr-tbl thead th.apr-grp-pay { background-color: #2c5282 !important; }
#apr-tbl thead th.apr-sub-pay { background-color: #3563a0 !important; }
#apr-tbl thead th.apr-grp-prj { background-color: #115e59 !important; }
#apr-tbl thead th.apr-sub-prj { background-color: #0f766e !important; }

/* app-ftr-list.css memipihkan sel PERTAMA kepala tabel:
       .ftl-tbl thead th:first-child { width: 0;
         padding-left: 0 !important; padding-right: 0 !important; }
   Itu ditulis untuk kolom tombol "+" milik DataTables Responsive, yang
   memang harus tak berlebar. Halaman ini TIDAK memakai Responsive, dan
   kepalanya DUA TINGKAT - jadi :first-child mengenai DUA sel sekaligus:
   "No" di baris pertama DAN "Tax Base" di baris kedua, keduanya dipipihkan
   tanpa alasan sampai menempel ke tepi. Dikembalikan di sini. */
#apr-tbl thead th:first-child {
  width: auto;
  padding-left: 10px !important;
  padding-right: 10px !important;
}

/* ---- BARIS TOTAL di kaki tabel ---------------------------------------
   Disamakan dgn AR atas permintaan pemakai 6 Nov 2026 ("boleh samakan"):
   begitu kepalanya gelap, baris total yang putih jadi menggantung
   sendirian di bawah tabel. AR:
       #table-projection-report tfoot td { background: #1e3a5f;
         color: #f8fafc; font-weight: 700; border-top: 2px solid #2c5282; }

   TANPA !important, dan itu disengaja. Berbeda dgn kepala tabel, tidak ada
   satu berkas pun yang mengunci kaki tabel dgn !important - penjaga di
   uji_prj_awal bagian 8 memeriksa ini untuk thead DAN tfoot, jadi kalau
   suatu saat ada yang menguncinya, ketahuan di sana, bukan dari layar.
   Yang perlu dikalahkan cuma `.table th { border-top: 1px }` bawaan
   Bootstrap, dan #apr-tbl tfoot th sudah lebih tinggi spesifisitasnya.

   TIDAK dibuat menempel di bawah (sticky) seperti AR: di AR tabelnya ada
   di dalam kotak ber-max-height yang bisa digulir tegak, di sini tidak -
   .apr-scroll hanya menggulir ke SAMPING dan isinya cuma 10 baris per
   halaman, jadi sticky-nya tidak punya tempat berpegang. */
#apr-tbl tfoot th {
  background-color: #1e3a5f;
  color: #f8fafc;
  font-weight: 700;
  font-size: 11.5px;
  padding: 9px 10px;
  border: 0;
  border-top: 2px solid #2c5282;
  border-right: 1px solid rgba(255, 255, 255, .10);
  white-space: nowrap;
  /* Angka rata digit, sama seperti AR - di kolom uang sempit itu yang
     membuat totalnya mudah dibandingkan antar kolom. */
  font-variant-numeric: tabular-nums;
}
/* Kolom tanggal yang TIDAK ada isinya dibuat redup, bukan dihilangkan -
   kerangka tanggalnya tetap terbaca utuh. */
#apr-tbl td.apr-kosong { color: #cbd5e1; }
/* Sel ISI kolom tanggal TIDAK diberi latar sendiri - latar baris dipakai
   Flexibility. Batas antara kolom tetap dan kolom tanggal ditandai satu
   garis tegak di kolom tanggal PERTAMA; di tabel selebar ini garis itu yang
   membuat orang tahu di mana uangnya mulai.

   Di KEPALA tabel garisnya putih bening, bukan #b9cdeb: di atas latar teal,
   garis biru muda terbaca sebagai cacat gambar. Di kepala, perpindahan
   bagiannya sudah ditandai perubahan warnanya sendiri. */
#apr-tbl td.apr-tgl-mulai { border-left: 2px solid #b9cdeb; }
#apr-tbl thead th.apr-tgl-mulai { border-left: 2px solid rgba(255, 255, 255, .35) !important; }

/* ---- latar baris menurut Flexibility supplier ---------------------------
   Diminta pemakai (6 Nov 2026), kosakata warnanya sama persis dgn layar
   Update Payment Plan supaya satu arti di dua tempat.

   Warnanya ditaruh di <td>, BUKAN di <tr>: .table-striped milik Bootstrap
   mewarnai <tr> dan latar <td> menang atasnya. Garis selang-selingnya jadi
   hilang - memang disengaja, karena warna baris sekarang PUNYA ARTI.

   KUNINGNYA SENGAJA PALING TIPIS: di master supplier YELLOW jauh lebih
   banyak daripada RED & GREEN, jadi kuning yang pekat akan menenggelamkan
   hampir seluruh tabel dan justru MENYEMBUNYIKAN baris merah - padahal itu
   yang paling perlu terlihat. */
#apr-tbl tbody tr                { --flx-bg: #ffffff; --flx-hov: #eef4ff; --flx-bar: transparent; }
#apr-tbl tbody tr.apr-flx-green  { --flx-bg: #f1faf4; --flx-hov: #e2f3e8; --flx-bar: #34a853; }
#apr-tbl tbody tr.apr-flx-yellow { --flx-bg: #fffdf2; --flx-hov: #fdf5e0; --flx-bar: #e0a800; }
#apr-tbl tbody tr.apr-flx-red    { --flx-bg: #fdeeec; --flx-hov: #fadbd7; --flx-bar: #ea4335; }

#apr-tbl tbody tr > td       { background: var(--flx-bg); }
#apr-tbl tbody tr:hover > td { background: var(--flx-hov); }
#apr-tbl tbody tr > td:first-child { box-shadow: inset 4px 0 0 var(--flx-bar); }
.apr-warn { background: #fdecea; border: 1px solid #f5c2bd; color: #a3281c;
            border-radius: 9px; padding: 8px 12px; font-size: 12px; margin-bottom: 10px; }

</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
            <div>
                <h1>AP Payment Projection Report</h1>
                <span class="ftl-crumb">AP &rsaquo; Report &rsaquo; Projection &rsaquo; Projection Report</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-body">
            <form id="apr-form" onsubmit="return false;">
                <div class="form-row">

                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="apr-supp" class="form-label"><b>Supplier</b></label>
                        <select class="form-control app-select2" id="apr-supp">
                            <option value="" selected="selected">All Suppliers</option>
                            <?php foreach ($supplierList as $s) { ?>
                            <option value="<?php echo htmlspecialchars($s, ENT_QUOTES); ?>"><?php echo htmlspecialchars($s, ENT_QUOTES); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Daily / Weekly / Monthly - aturannya DISAMAKAN DGN
                         ar_dev (pemakai, 6 Nov 2026). Type TIDAK menggabungkan
                         kolom: kolom tanggal selalu satu per hari. Yang
                         diaturnya dua hal - rentang From-To yang boleh dipakai
                         (weekly Minggu-Sabtu, monthly satu bulan penuh) dan
                         tanggal acuan kurs di server.
                         Ditaruh SESUDAH Supplier dan SEBELUM From atas
                         permintaan pemakai (6 Nov 2026). -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="apr-type" class="form-label"><b>Type</b></label>
                        <select class="form-control app-select2" id="apr-type">
                            <option value="daily" selected="selected">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="apr-dari" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="apr-dari"
                               value="<?php echo date('01-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="apr-sampai" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="apr-sampai"
                               value="<?php echo date('t-m-Y'); ?>" autocomplete="off">
                    </div>

                    <!-- Export Excel & Save History: dua tombol yang sama
                         dengan laporan proyeksi AR (diminta pemakai 6 Nov
                         2026). Keduanya memakai saringan yang SEDANG tampil,
                         dan lewat pagar rentang yang sama dengan Search -
                         rentang yang ditolak layar tidak boleh bisa diekspor
                         atau disimpan lewat pintu samping. -->
                    <div class="col-12 col-xl-3 mb-2 ftl-actions">
                        <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="apr-cari">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <button type="button" class="app-btn app-btn-excel app-btn-ctl app-btn-sm" id="apr-excel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                        <button type="button" class="app-btn app-btn-success app-btn-ctl app-btn-sm" id="apr-simpan">
                            <i class="fa fa-download" aria-hidden="true"></i> Save History
                        </button>
                    </div>

                </div><!-- /.form-row -->
            </form>
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

    <div class="ftl-card mt-3">
        <div class="ftl-body">

            <!-- Baris keterangan (periode, jumlah voucher, total, kurs)
                 DIBUANG atas permintaan pemakai (6 Nov 2026). Peringatan kurs
                 TIDAK ikut dibuang: itu bukan hiasan - kalau kursnya tidak
                 ketemu, angka rupiah di tabel memang tidak bisa dipercaya. -->
            <div id="apr-peringatan"></div>

            <div class="app-loading-wrap" id="aprLoad">
                <div class="app-loading">
                    <div class="app-loading-box">
                        <div class="app-spinner"></div>
                        <div class="app-loading-text">Building the projection...</div>
                    </div>
                </div>

                <div id="apr-wrap">
                    <table id="apr-tbl" class="table ftl-tbl" cellspacing="0">
                        <thead><tr></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div><!-- /.app-loading-wrap -->

        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

</div><!-- /.container-fluid -->

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<!-- Penjaga rentang From/To, dipakai bersama beberapa laporan AP.
     SESUDAH jQuery, bootstrap-datepicker dan SweetAlert. -->
<script language="JavaScript" src="app_rentang_tanggal.js"></script>

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

  $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
  /* To tidak boleh lebih kecil dari From. Kalendernya dikunci di sini;
     penjaga saat Search ada di periodeSah(). */
  apRentangTanggal('apr-dari', 'apr-sampai');
  $('.app-select2').each(function () {
    var $s = $(this);
    var o = { width: '100%', dropdownParent: $('body') };
    if ($s.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
    $s.select2(o);
  });

  /* Kolom TETAP laporan - susunannya mengikuti lembar "Projection" di
     spesifikasi tim AP. Kolom tanggal menyusul di belakangnya, dibuat dari
     jawaban server. */
  var KOLOM_TETAP = [
    { data: 'no',             title: 'No',                 kelas: 'text-center' },
    { data: 'supplier',       title: 'Supplier',           kelas: 'text-left apr-supp' },
    { data: 'no_kbon',        title: 'Payment Voucher No', kelas: 'text-left' },
    { data: 'tgl_kbon',       title: 'PV Date',            kelas: 'text-center' },
    { data: 'term',           title: 'Payment Term',       kelas: 'text-center' },
    { data: 'duedate',        title: 'Due Date',           kelas: 'text-center' },
    /* SATU kolom rencana bayar saja - sesuai lembar "Projection" di
       spesifikasi, dan ditegaskan pemakai 6 Nov 2026. Isinya tanggal yang
       BENAR-BENAR dipakai menaruh uangnya di kolom tanggal.

       Dasarnya tetap berlapis di server (rencana PPU -> rencana awal ->
       jatuh tempo); yang hilang cuma tiga kolom yang dulu memamerkan
       lapisannya. Supaya keterangannya tidak ikut hilang, lapis yang
       terpakai ditempelkan sbg tooltip sel - lihat render-nya di bawah. */
    { data: 'tgl_proyeksi',   title: 'Payment Plan Date',  kelas: 'text-center' },
    { data: 'relasi',         title: 'Relationship',       kelas: 'text-left' },
    { data: 'item_type',      title: 'Item Type',          kelas: 'text-left' },
    { data: 'flexibility',    title: 'Flexibility Category', kelas: 'text-center' },
    { data: 'curr',           title: 'Currency',           kelas: 'text-center' },
    { data: 'invoice_amount', title: 'Invoice Amount',     kelas: 'text-right' },
    { data: 'rate',           title: 'Rate',               kelas: 'text-right' },
    /* Lima kolom di bawah payung "Payable Amount" di lembar spesifikasi. */
    { data: 'tax_base',       title: 'Tax Base',           kelas: 'text-right', grup: 'payable' },
    { data: 'vat',            title: 'VAT',                kelas: 'text-right', grup: 'payable' },
    { data: 'total_invoice',  title: 'Total Invoice',      kelas: 'text-right', grup: 'payable' },
    { data: 'pph',            title: 'Income Tax Art 23',  kelas: 'text-right', grup: 'payable' },
    { data: 'payment_amount', title: 'Payment Amount',     kelas: 'text-right', grup: 'payable' }
  ];

  /* Judul payung kedua kelompok, disalin dari lembar "Projection". */
  var GRUP_PAYABLE  = 'Payable Amount';
  var GRUP_PROYEKSI = 'Projected Cash Outflow for Accounts Payable Payment';

  /* Satu tempat yang memutuskan warna Flexibility, sama seperti di layar
     Update Payment Plan. Nilai yang tidak dikenal TIDAK diberi warna:
     barisnya putih, bukan salah warna. */
  function kodeFlex(v) {
    var k = (v || '').toString().trim().toUpperCase();
    return (k === 'GREEN' || k === 'YELLOW' || k === 'RED') ? k.toLowerCase() : '';
  }

  var tabel = null;
  /* Membedakan "belum dicari" dari "sudah dicari, hasilnya nol". Pesan
     "tidak ada voucher di periode ini" waktu periodenya belum dipilih itu
     menyesatkan - seolah sudah dicari dan memang kosong. */
  var sudahCari = false;

  function bersihkanTabel() {
    if (tabel) { tabel.destroy(); tabel = null; }
    /* destroy() membongkar pembungkus DataTables, TAPI tidak membongkar
       kotak gulir yang dipasang sendiri - kalau tidak dilepas, tiap Search
       menambah satu lapis div lagi. */
    if ($('#apr-tbl').parent().hasClass('apr-scroll')) { $('#apr-tbl').unwrap(); }
    $('#apr-tbl thead').empty();
    $('#apr-tbl tfoot').remove();
    $('#apr-tbl tbody').empty();
  }

  /* Membungkus TABELNYA saja dgn kotak yang bisa digulir ke samping.
     Dipanggil SESUDAH DataTable() jadi, supaya kotaknya berada di dalam
     pembungkus DataTables - dgn begitu kendalinya (Show N, Search, info,
     paging) tetap di tempat semula, tetap kena seluruh aturan perataan
     bawaan DataTables, dan tetap diam waktu tabelnya digeser. */
  function bungkusGulir() {
    var tbl = $('#apr-tbl');
    if (!tbl.parent().hasClass('apr-scroll')) {
      tbl.wrap('<div class="apr-scroll"></div>');
    }
  }

  function lolos(v) {
    return $('<div>').text(v === null || v === undefined ? '' : v).html();
  }

  /* Menggambar tabelnya. Dipakai DUA keadaan:
       - halaman baru dibuka: kolomTgl & baris kosong, jadi yang tampil
         kerangka 18 kolom tetap tanpa isi (diminta pemakai 6 Nov 2026 -
         sebelumnya kartunya kosong melompong),
       - sesudah Search: lengkap dgn kolom tanggal & barisnya.
     Hasil Search yang KOSONG pun lewat sini, tidak lagi menyembunyikan
     tabelnya - pesan "tidak ada barisnya" ditulis DataTables sendiri di
     dalam tabel, jadi kerangka kolomnya tetap terbaca. */
  function gambar(res) {
    bersihkanTabel();

    var kolomTgl = (res && res.kolom) || [];
    var baris    = (res && res.data)  || [];

    /* ---- kepala DUA TINGKAT, persis lembar "Projection" ----
       Kolom biasa memanjang ke bawah (rowspan 2); kolom uang dan kolom
       tanggal bernaung di bawah judul payungnya masing-masing. DataTables
       memang mengerti kepala bertingkat - yang dipakainya sbg kolom adalah
       sel paling bawah, jadi urutan isian di bawah tetap lurus. */
    var nPayable = 0;
    for (var i = 0; i < KOLOM_TETAP.length; i++) {
      if (KOLOM_TETAP[i].grup === 'payable') { nPayable++; }
    }

    var thAtas = '', thBawah = '', payableDitulis = false;
    for (var i = 0; i < KOLOM_TETAP.length; i++) {
      var kk = KOLOM_TETAP[i];
      if (kk.grup === 'payable') {
        if (!payableDitulis) {
          thAtas += '<th colspan="' + nPayable + '" class="apr-grp-pay">'
                  + GRUP_PAYABLE + '</th>';
          payableDitulis = true;
        }
        thBawah += '<th class="apr-sub-pay">' + kk.title + '</th>';
      } else {
        /* Kolom biasa tidak diberi kelas: warnanya warna dasar thead. */
        thAtas += '<th rowspan="2">' + kk.title + '</th>';
      }
    }
    if (kolomTgl.length) {
      thAtas += '<th colspan="' + kolomTgl.length
              + '" class="apr-tgl apr-tgl-mulai apr-grp-prj">' + GRUP_PROYEKSI + '</th>';
      for (var j = 0; j < kolomTgl.length; j++) {
        thBawah += '<th class="apr-tgl apr-sub-prj' + (j === 0 ? ' apr-tgl-mulai' : '')
                 + '">' + lolos(kolomTgl[j].label) + '</th>';
      }
    }
    /* Tanpa .thead-dark: warna kepala diatur aturan #apr-tbl thead th di
       atas. Dua-duanya dipasang hanya membuat orang menebak mana yang
       menang. */
    $('#apr-tbl thead').html('<tr>' + thAtas + '</tr>'
                           + '<tr>' + thBawah + '</tr>');

    /* ---- kaki: jumlah per kolom tanggal ----
       Tanpa kolom tanggal (keadaan awal) kakinya tidak digambar: baris
       total yang semua selnya kosong cuma jadi gangguan. */
    if (kolomTgl.length) {
    var tf = '<tfoot><tr>'
           + '<th colspan="' + KOLOM_TETAP.length + '" style="text-align:right;">Projected Cash Outflow</th>';
    for (var m = 0; m < kolomTgl.length; m++) {
      tf += '<th class="apr-tgl" style="text-align:right;">' + lolos(kolomTgl[m].total) + '</th>';
    }
    tf += '</tr></tfoot>';
    $('#apr-tbl').append(tf);
    }

    /* ---- susunan kolom untuk DataTables ---- */
    var kolom = [];
    var defs  = [];
    for (var a = 0; a < KOLOM_TETAP.length; a++) {
      var kd = { data: KOLOM_TETAP[a].data };
      /* Dasar tanggalnya tetap bisa ditanyakan walau kolomnya tinggal satu:
         ditempel sbg tooltip, jadi tabelnya tetap sebersih lembar Excel. */
      if (KOLOM_TETAP[a].data === 'tgl_proyeksi') {
        kd.render = function (d, t, row) {
          if (t !== 'display') { return d; }
          return '<span title="' + lolos(row.basis || '') + '">' + lolos(d) + '</span>';
        };
      }
      kolom.push(kd);
      defs.push({ targets: a, className: KOLOM_TETAP[a].kelas });
    }
    for (var b = 0; b < kolomTgl.length; b++) {
      (function (kunci, idx) {
        kolom.push({
          data: kunci,
          /* Sel kosong tetap digambar (bukan dilewati) supaya kerangka
             tanggalnya utuh, tapi diredupkan lewat kelas .apr-kosong. */
          render: function (d, t) {
            if (t !== 'display') { return d || ''; }
            return (d === '' || d === null || d === undefined) ? '<span class="apr-kosong">-</span>' : d;
          }
        });
        defs.push({ targets: KOLOM_TETAP.length + idx,
                    className: 'text-right apr-tgl' + (idx === 0 ? ' apr-tgl-mulai' : '') });
      })(kolomTgl[b].key, b);
    }

    tabel = $('#apr-tbl').DataTable({
      data: baris,
      columns: kolom,
      columnDefs: defs,
      ordering: false,
      searching: true,
      paging: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      info: true,
      autoWidth: false,
      /* Latar baris menurut Flexibility suppliernya. Ditempel di createdRow -
         bukan di render salah satu kolom - karena yang diwarnai SELURUH
         baris, dan createdRow dipanggil tiap <tr> dibuat, termasuk waktu
         pindah halaman. */
      createdRow: function (row, data) {
        var kode = kodeFlex(data.flexibility);
        if (kode !== '') { $(row).addClass('apr-flx-' + kode); }
      },
      /* TANPA modul Responsive: kolomnya memang banyak dan DISENGAJA digulir
         ke samping - menyembunyikan kolom tanggal justru menghilangkan isi
         laporannya. */
      language: {
        emptyTable: sudahCari
          ? 'No outstanding payment voucher falls in this period.'
          : 'Pick a period and click Search to build the projection.',
        zeroRecords: 'No row matches your search.'
      }
    });

    bungkusGulir();
  }

  /* 'dd-mm-yyyy' (bentuk yang dipakai datepicker halaman ini) -> objek Date.
     new Date('02-11-2026') dibaca browser sbg bulan-hari-tahun, jadi tidak
     boleh dipakai langsung di sini. */
  function tglObj(v) {
    var p = (v || '').split('-');
    if (p.length !== 3) { return null; }
    var d = new Date(+p[2], +p[1] - 1, +p[0]);
    return isNaN(d.getTime()) ? null : d;
  }

  /* Pagar rentang menurut Type - salinan aturan ar_dev
     (crud-nag-report.js cari_projection_report):
       weekly  : From Minggu, To Sabtu, tepat 7 hari
       monthly : From tanggal 1, To hari terakhir bulan yang sama
       daily   : bebas
     Server memagari hal yang sama; yang di sini semata supaya pemakai dapat
     pesan yang jelas sebelum permintaannya dikirim. */
  function periodeSah(type, dari, sampai) {
    var a = tglObj(dari), b = tglObj(sampai);
    if (!a || !b) { return true; }

    /* To < From diperiksa DI SINI, bukan hanya di kalendernya: ketiga
       jalurnya (Search, Export, Save History) lewat fungsi ini, sedangkan
       isiannya teks biasa yang masih bisa diketik tangan. Berlaku untuk
       SEMUA type - pagar weekly/monthly di bawah tidak menangkapnya kalau
       type-nya daily. */
    if (!apRentangSah('apr-dari', 'apr-sampai')) { return false; }

    var selisih = Math.round((b - a) / 86400000);

    if (type === 'weekly') {
      if (a.getDay() !== 0 || b.getDay() !== 6 || selisih !== 6) {
        Swal.fire({ icon: 'warning', title: 'Invalid Date Range',
          html: 'Weekly type requires a <b>Sunday to Saturday</b> range '
              + '(exactly 7 days).<br><small>From must be Sunday, To must be '
              + 'Saturday.</small>' });
        return false;
      }
    } else if (type === 'monthly') {
      var akhirBulan = new Date(b.getFullYear(), b.getMonth() + 1, 0).getDate();
      if (a.getDate() !== 1
          || a.getMonth() !== b.getMonth() || a.getFullYear() !== b.getFullYear()
          || b.getDate() !== akhirBulan) {
        Swal.fire({ icon: 'warning', title: 'Invalid Date Range',
          html: 'Monthly type requires a <b>full month</b> range '
              + '(1st to last day of the same month).' });
        return false;
      }
    }
    return true;
  }

  function cari() {
    if (!periodeSah($('#apr-type').val(), $('#apr-dari').val(), $('#apr-sampai').val())) {
      return;
    }
    var post = {
      start_date: $('#apr-dari').val(),
      end_date:   $('#apr-sampai').val(),
      nama_supp:  $('#apr-supp').val(),
      type:       $('#apr-type').val()
    };
    $('#aprLoad').addClass('is-loading');
    $('#apr-peringatan').empty();

    $.ajax({ url: 'ajx_ap_projection_report.php', type: 'POST', dataType: 'json', data: post })
      .done(function (res) {
        $('#aprLoad').removeClass('is-loading');
        sudahCari = true;
        if (res.error) {
          /* Kartu TIDAK dikosongkan waktu permintaannya gagal: kerangka
             tabelnya digambar ulang kosong, supaya layarnya tetap punya
             bentuk dan pemakai tahu tombol Search masih ada gunanya. */
          gambar(null);
          Swal.fire({ icon: 'warning', title: 'Could not build the projection', text: res.error });
          return;
        }

        /* Baris keterangan sudah tidak digambar lagi (pemakai 6 Nov 2026).
           info.periode / total / kurs TETAP dikirim server - angkanya
           dipakai penjaga dan siap dipakai ekspor Excel nanti. */
        var i = res.info || {};

        /* Mata uang tanpa kurs = angkanya TIDAK BISA dipercaya. Harus
           kelihatan, bukan disembunyikan di balik total yang rapi. */
        if (i.kurs_hilang && i.kurs_hilang.length) {
          $('#apr-peringatan').html('<div class="apr-warn"><b>Exchange rate not found for: '
            + lolos(i.kurs_hilang.join(', ')) + '.</b> Those rows are converted 1:1, so the IDR '
            + 'figures and the total below are not reliable. Please add the rate in Master Rate first.</div>');
        }

        gambar(res);
      })
      .fail(function (xhr) {
        $('#aprLoad').removeClass('is-loading');
        Swal.fire({ icon: 'error', title: 'Failed to build the projection',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response') });
      });
  }

  /* Keadaan awal: kerangka tabelnya digambar SEBELUM Search diklik, dan
     digambar sebagai DataTable sungguhan (lengkap dgn Search & paging-nya),
     bukan tabel polos - diminta pemakai 6 Nov 2026. Tanpa ini kartunya
     kosong melompong dan layarnya tidak memberi tahu apa-apa. */
  gambar(null);

  /* ---- Export Excel ----------------------------------------------------
     Saringannya dikirim lewat query string ke ekspor_ap_projection_report.php,
     yang menghitung ulang lewat endpoint YANG SAMA dgn layar - jadi isinya
     tidak bisa berbeda dari yang terlihat. Memakai window.location, bukan
     ajax: jawabannya berkas unduhan, bukan JSON. */
  function ekspor() {
    var type = $('#apr-type').val();
    var dari = $('#apr-dari').val();
    var sampai = $('#apr-sampai').val();
    if (!periodeSah(type, dari, sampai)) { return; }

    Swal.fire({ title: 'Preparing Excel...', allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); } });

    window.location.href = 'ekspor_ap_projection_report.php'
      + '?start_date=' + encodeURIComponent(dari)
      + '&end_date='   + encodeURIComponent(sampai)
      + '&nama_supp='  + encodeURIComponent($('#apr-supp').val())
      + '&type='       + encodeURIComponent(type);

    /* Unduhan tidak memuat ulang halaman, jadi tidak ada peristiwa yang bisa
       ditunggu - kotaknya ditutup setelah jeda singkat, sama dgn layar
       Payment Plan. */
    setTimeout(function () { Swal.close(); }, 1500);
  }

  /* ---- Save History ----------------------------------------------------
     Menyimpan POTRET: angka yang tersimpan adalah yang sedang dilihat, bukan
     rujukan yang dibaca ulang nanti. Dimintai konfirmasi dulu karena tiap
     penekanan membuat satu dokumen baru - tidak ada yang tertimpa. */
  function simpanHistory() {
    var type = $('#apr-type').val();
    var dari = $('#apr-dari').val();
    var sampai = $('#apr-sampai').val();
    if (!periodeSah(type, dari, sampai)) { return; }

    Swal.fire({
      icon: 'question',
      title: 'Save this projection?',
      html: 'A new history document will be created for<br><b>' + lolos(dari)
          + '</b> to <b>' + lolos(sampai) + '</b> (' + lolos(type) + ').',
      showCancelButton: true,
      confirmButtonText: 'Save',
      cancelButtonText: 'Cancel'
    }).then(function (ya) {
      if (!ya.isConfirmed) { return; }

      Swal.fire({ title: 'Saving...', allowOutsideClick: false,
                  didOpen: function () { Swal.showLoading(); } });

      $.ajax({
        url: 'simpan_history_ap_projection.php', type: 'POST', dataType: 'json',
        data: { start_date: dari, end_date: sampai,
                nama_supp: $('#apr-supp').val(), type: type }
      })
      .done(function (res) {
        if (!res || res.status !== 'success') {
          Swal.fire({ icon: 'warning', title: 'Could not save',
                      text: (res && res.message) ? res.message : 'Unknown response.' });
          return;
        }
        Swal.fire({ icon: 'success', title: 'Saved',
                    html: 'Document <b>' + lolos(res.doc_number) + '</b><br>'
                        + lolos(res.baris) + ' row(s), total ' + lolos(res.total) });
      })
      .fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'Failed to save',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response') });
      });
    });
  }

  $('#apr-excel').on('click', ekspor);
  $('#apr-simpan').on('click', simpanHistory);
  $('#apr-cari').on('click', cari);
  $('#apr-form').on('submit', function (e) { e.preventDefault(); cari(); });
});
</script>
