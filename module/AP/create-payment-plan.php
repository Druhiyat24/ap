<?php
/* ============================================================================
   Update Payment Plan - FORMULIR. Berkas ini BERDIRI SENDIRI dan dibuka dari
   tombol Create New di list-payment-plan.php - bukan dari butir menu
   tersendiri, sama seperti Create Petty Cash Out.

   Alurnya: isi tanggal rencana bayar + catatan -> cari voucher -> centang
   yang mau dijadwalkan -> Save. Satu dokumen = SATU tanggal rencana untuk
   seluruh voucher yang dicentang; voucher yang jadwalnya lain dibuatkan
   dokumen sendiri.

   Nomor dokumennya TIDAK ditampilkan sebagai isian: nomornya dibuat waktu
   disimpan (simpan_payment_plan.php) supaya dua orang yang membuka formulir
   bersamaan tidak mendapat nomor yang sama.

   Relationship, Item Type dan Flexibility di sini hanya DITAMPILKAN - tiga-
   tiganya punya sumbernya sendiri:
     Relationship, Item Type : mastercoa_v2 lewat kontrabon_h.no_coa - sama
         dgn laporan AP (Payment Voucher).
     Flexibility Category    : mastersupplier.payment_rating. Masih kosong
         untuk semua supplier, jadi wajar kalau tampil '-'.

   SUSUNAN TAMPILANNYA MENGIKUTI create-pettycash-out.php (diminta pemakai,
   4 Nov 2026): kosakata .pco- - satu kartu luar dgn kepala berkotak ikon,
   bagian-bagian bertanda .pco-sec, dan tombol simpan duduk di .pco-foot.
   Sebelumnya halaman ini memakai kartu .ftl- milik halaman DAFTAR plus
   tombol Bootstrap biasa, jadi tampil lain dari formulir AP yang sudah
   jalan.

   PUTARAN KEDUA (diminta pemakai, 5 Nov 2026):
     - app-skin-form.css DITAUT. Di situlah .app-btn beserta warnanya
       didefinisikan; tanpa berkas itu Search/Save/Back tampil sebagai kotak
       putih polos - bukan karena kelasnya salah, tapi karena aturannya tidak
       pernah termuat. Sekalian isian .tanggal dapat ikon kalendernya.
     - Dropdown memakai select2, BUKAN bootstrap-select. Dgn bootstrap-select,
       kelas form-control ikut menempel di pembungkus .bootstrap-select
       sementara tombol di dalamnya punya bingkai sendiri - jadi tampak dua
       kotak bertumpuk dan tingginya tidak sebaris dgn isian lain.
     - Kotak centang "Not scheduled yet" dan kolom "Current Plan" DIHAPUS:
       layar ini sekarang SELALU hanya menampilkan voucher yang belum
       dijadwalkan, jadi kolom yang isinya pasti "Not Scheduled" tidak ada
       gunanya. Penjaganya pindah ke simpan_payment_plan.php - lihat di sana.

   PUTARAN KETIGA (diminta pemakai, 5 Nov 2026) - empat perbaikan tampilan,
   masing-masing ada catatannya di tempat perubahannya:
     - Lebar tiap kolom dipatok (columnDefs), supaya nomor voucher tidak lagi
       memakan ruang Supplier & Description.
     - Mencentang baris TIDAK lagi mengganti tanggalnya dgn tanggal dokumen;
       rencana awal baris itu dipertahankan - lihat rencanaBaris().
     - Isian tanggal di baris dibuat seukuran teks yang digantikannya, jadi
       kolomnya tidak bergeser & barisnya tidak meninggi waktu dicentang.
     - Sorotan baris terpilih dimudakan.
   ============================================================================ */
include '../header.php';
?>
<?php
/* Daftar supplier untuk saringan - hanya yang punya voucher disetujui, jadi
   isinya tidak sepanjang master supplier. */
$supplierList = array();
$qSup = mysqli_query($conn1, "SELECT DISTINCT nama_supp FROM kontrabon_h
        WHERE status IN ('Approved','FIRST APPROVED','SECOND APPROVED')
          AND nama_supp IS NOT NULL AND nama_supp <> ''
        ORDER BY nama_supp ASC");
if ($qSup) {
    while ($rSup = mysqli_fetch_assoc($qSup)) { $supplierList[] = $rSup['nama_supp']; }
}
?>

<!-- Berkas CSS ditaut dgn penanda versi dari filemtime, jadi perubahan di
     dalamnya langsung sampai ke user dan tidak tertahan cache.

     Kosakata .pco- dipakai BERSAMA oleh create & kelima halaman edit Petty
     Cash Out. Halaman ini ikut memakainya - bukan menyalin aturannya ke blok
     style sendiri - supaya tidak ada salinan yang nanti melenceng.

     app-skin-form.css ditaut PALING DULU: di situ .app-btn (dan warna
     -primary / -success / -danger), skin select2, serta ikon kalender
     .tanggal didefinisikan. Berkas yang sama dipakai halaman daftar
     list-payment-plan.php, jadi tombol di kedua layar benar-benar sama -
     bukan sekadar mirip. Aturan .pco- di berkas berikutnya lebih khusus
     (berawalan .pco-card), jadi yang di dalam kartu tetap menang. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-pco-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-pco-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<!-- app-ftr-list.css TIDAK ditaut lagi: satu-satunya pemakainya di halaman
     ini adalah pil .ftl-st di kolom Current Plan, dan kolom itu sudah
     dihapus. Berkas itu milik halaman DAFTAR; menautkannya di halaman
     formulir hanya menambah aturan yang tidak dipakai. -->

<style>
/* Hanya yang BELUM ada di app-pco-form.css. Sengaja sedikit: sisanya
   diurus kosakata .pco-. */

#table-pv td { vertical-align: middle; }

/* ---- latar baris menurut Flexibility supplier ---------------------------
   Diminta pemakai (5 Nov 2026): warna baris mengikuti
   mastersupplier.payment_rating, supaya waktu menjadwalkan langsung terlihat
   supplier mana yang pembayarannya boleh dimundurkan dan mana yang tidak -
   tanpa membuka rincian tiap voucher satu per satu.

   Warnanya ditaruh di <td>, BUKAN di <tr>: .table-striped milik Bootstrap
   mewarnai <tr>, dan latar <td> menang atasnya. Akibatnya garis
   selang-seling itu hilang - memang disengaja, karena warna baris sekarang
   PUNYA ARTI, dan dua sistem warna di tempat yang sama hanya membingungkan.
   Barisnya tetap terpisah oleh garis tabel (.table-bordered).

   KUNINGNYA SENGAJA PALING TIPIS. Di master supplier, YELLOW jauh lebih
   banyak daripada RED & GREEN, jadi kuning yang pekat akan menenggelamkan
   hampir seluruh tabel dan justru MENYEMBUNYIKAN baris merah - padahal itu
   yang paling perlu terlihat. */
#table-pv tbody tr                { --flx-bg: #ffffff; --flx-hov: #eef4ff; --flx-bar: transparent; }
#table-pv tbody tr.app-flx-green  { --flx-bg: #f1faf4; --flx-hov: #e2f3e8; --flx-bar: #34a853; }
#table-pv tbody tr.app-flx-yellow { --flx-bg: #fffdf2; --flx-hov: #fdf5e0; --flx-bar: #e0a800; }
#table-pv tbody tr.app-flx-red    { --flx-bg: #fdeeec; --flx-hov: #fadbd7; --flx-bar: #ea4335; }

#table-pv tbody tr > td       { background: var(--flx-bg); }
#table-pv tbody tr:hover > td { background: var(--flx-hov); }

/* BARIS YANG DICENTANG TIDAK MENGUBAH LATAR LAGI (diminta pemakai,
   5 Nov 2026). Latar baris sudah dipakai untuk Flexibility supplier; kalau
   sorotan centang menimpanya, justru kategori suppliernya yang hilang
   PERSIS waktu baris itu sedang dikerjakan - padahal di situlah warnanya
   paling dibutuhkan.

   Penandanya pindah ke TEPI KIRI: garis biru tipis di sebelah dalam garis
   warna flexibility. Dua bayangan inset ditumpuk, dan yang DISEBUT DULUAN
   digambar DI ATAS - jadi 0-4px tetap warna flexibility, 4-7px jadi biru.
   Untuk supplier yang flexibility-nya belum disetel, --flx-bar tembus
   pandang sehingga birunya terlihat penuh 0-7px; itu tidak apa-apa, yang
   penting barisnya tetap bertanda.

   Kotak centang di kiri tetap penanda utamanya - ini cuma supaya baris yang
   ikut masih kebaca waktu tabelnya digulir cepat. */
#table-pv tbody tr > td:first-child {
  box-shadow: inset 4px 0 0 var(--flx-bar);
}
#table-pv tbody tr.app-pilih > td:first-child {
  box-shadow: inset 4px 0 0 var(--flx-bar), inset 7px 0 0 #2563eb;
}

/* Kunci warnanya, di kanan judul bagian. Tanpa ini warnanya cuma jadi
   tebak-tebakan. */
.app-flx-ket { float: right; font-size: 11px; font-weight: 500; color: #64748b;
               text-transform: none; letter-spacing: 0; }
.app-flx-ket > span { margin-left: 13px; white-space: nowrap; }
.app-flx-dot { display: inline-block; width: 12px; height: 12px; border-radius: 3px;
               vertical-align: -2px; margin-right: 5px; border: 1px solid #e2e8f0; }
.app-flx-dot.is-green  { background: #f1faf4; box-shadow: inset 4px 0 0 #34a853; }
.app-flx-dot.is-yellow { background: #fffdf2; box-shadow: inset 4px 0 0 #e0a800; }
.app-flx-dot.is-red    { background: #fdeeec; box-shadow: inset 4px 0 0 #ea4335; }
.app-flx-dot.is-kosong { background: #ffffff; }

/* Nomor voucher = tautan pembuka rincian. Dibuat terlihat bisa diklik,
   tapi tidak diberi garis bawah supaya kolomnya tidak jadi ramai. */
#table-pv a.app-no-pv { color: #1d4ed8; font-weight: 600; text-decoration: none; }
#table-pv a.app-no-pv:hover { color: #1e3a8a; text-decoration: underline; }

/* Kotak Description di dalam baris dibuat sepadat isian lain di sel.
   LEBARNYA MENGIKUTI KOLOMNYA (width:100%), bukan min-width 170px seperti
   dulu: isian yang punya lebar minimum sendiri memaksa kolomnya melebar dan
   dgn begitu melawan pembagian lebar kolom di columnDefs. */
#table-pv input.app-desk {
  width: 100%; min-width: 0; font-size: 12px; height: 26px; padding: 2px 8px;
}

/* ---- kolom Payment Plan --------------------------------------------------
   Selnya punya DUA WAJAH, dikemudikan kotak centang di kiri baris (diminta
   pemakai, 5 Nov 2026):
     BELUM dicentang -> TEKS, yaitu rencana bayar yang berlaku sekarang.
     SUDAH dicentang -> ISIAN TANGGAL, karena baris itulah yang sedang
                        dijadwalkan ulang oleh dokumen ini.
   DUA WAJAH, SATU UKURAN (diminta pemakai, 5 Nov 2026). Sebelumnya isiannya
   lebih lebar dan lebih tinggi daripada teks di baris lain, DAN ikon
   kalendernya memakan 24px di kanan sehingga angkanya terdorong ke kiri -
   jadi begitu satu baris dicentang, kolomnya tampak bergeser sendiri dan
   barisnya ikut meninggi. Sekarang isian dan teks memakai kotak yang sama
   persis (lebar, tinggi, perataan, jarak dalam), jadi yang berubah waktu
   baris dicentang hanya bingkainya.

   Ikon kalender dimatikan DI DALAM TABEL saja (background-image:none):
   kalendernya tetap terbuka begitu kotaknya diklik, jadi di sini ikonnya
   hanya memakan ruang. Isian tanggal di kepala formulir tetap bawa ikon. */
#table-pv input.app-plan,
#table-pv .app-plan-teks {
  display: inline-block; width: 96px; height: 26px; line-height: 24px;
  padding: 0 4px; text-align: center; font-size: 12px; vertical-align: middle;
}
#table-pv input.app-plan {
  min-width: 0; border-radius: 6px; background-image: none;
}
#table-pv .app-plan-teks {
  color: #475569; cursor: help;
  border: 1px solid transparent; border-bottom-color: #cbd5e1; border-bottom-style: dashed;
}

/* ---- hari Kamis di kalender ----------------------------------------------
   Kamis = hari bayar (aturan tim AP), jadi ditandai langsung di kalender -
   dgn begitu tanggal rencana tidak perlu dihitung di kepala dulu.

   SENGAJA BEDA DARI "HARI INI": today sudah kuning (app-skin-form.css),
   Kamis dibuat kebiruan tipis + garis bawah. Garis bawahnya bukan hiasan:
   latar milik .today DAN latar tanggal terpilih (.active) dua-duanya
   ber-!important di berkas itu, jadi kalau Kamis hanya ditandai lewat latar,
   tanggal yang kebetulan Kamis DAN hari ini akan kehilangan tandanya. Dgn
   garis bawah, dua-duanya tetap terbaca sekaligus.

   Ber-scope .app-kal-kamis - kelas yang dipasangkan lewat JS ke panel
   kalender milik isian Payment Plan saja, supaya kalender saringan From/To
   tidak ikut bertanda. */
   DITIPISKAN atas permintaan pemakai (5 Nov 2026): semula latar biru penuh
   + huruf tebal + garis 3px, dan di kalender yang tiap minggunya pasti
   punya satu Kamis itu terbaca terlalu ramai. Sekarang latarnya nyaris
   putih, hurufnya cuma agak tebal, garisnya 2px dan warnanya muda - masih
   terlihat sekilas, tapi tidak lagi menarik mata lebih dulu daripada
   tanggal yang sedang dipilih. */
.datepicker.app-kal-kamis table tr td.day.app-kamis {
  background: #f5fbff; color: #0369a1; font-weight: 600;
  box-shadow: inset 0 -2px 0 #93d3f5;
}
.datepicker.app-kal-kamis table tr td.day.app-kamis:hover {
  background: #eef4ff !important; color: #1e3a8a;
}
/* Kamis yang sedang TERPILIH: latar biru tua menang (itu memang maunya),
   garisnya diputihkan supaya masih terlihat di atas latar gelap. */
.datepicker.app-kal-kamis table tr td.day.app-kamis.active {
  box-shadow: inset 0 -2px 0 #bfdbfe;
}
/* Keterangan kecil di kaki kalender - tanpa ini warna birunya cuma jadi
   teka-teki. Ditempel lewat JS ke panel kalendernya. */
.app-kal-ket { border-top: 1px solid #eef2f7; margin-top: 8px; padding-top: 7px;
               font-size: 11px; color: #64748b; display: flex; gap: 14px;
               align-items: center; justify-content: center; }
.app-kal-ket i { width: 11px; height: 11px; border-radius: 3px; display: inline-block;
                 margin-right: 5px; vertical-align: -1px; }
.app-kal-ket .is-kamis { background: #f5fbff; box-shadow: inset 0 -2px 0 #93d3f5; }
.app-kal-ket .is-today { background: #fef3c7; }

/* Saklar on/off. Bootstrap 4.1.1 BELUM punya .custom-switch (baru ada di
   4.2), jadi dibuat di sini - bukan karena ingin bentuk sendiri. Kalau
   nanti Bootstrap-nya naik versi, ini bisa diganti .custom-switch. */
.app-switch { display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
              margin: 0; user-select: none; height: 34px; }
.app-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
.app-switch-track { position: relative; width: 38px; height: 20px; border-radius: 20px;
                    background: #cbd5e1; transition: background .15s ease; flex: 0 0 auto; }
.app-switch-knob { position: absolute; top: 2px; left: 2px; width: 16px; height: 16px;
                   border-radius: 50%; background: #fff;
                   box-shadow: 0 1px 3px rgba(15, 23, 42, .35); transition: transform .15s ease; }
.app-switch input:checked + .app-switch-track { background: #2563eb; }
.app-switch input:checked + .app-switch-track .app-switch-knob { transform: translateX(18px); }
.app-switch input:focus + .app-switch-track { box-shadow: 0 0 0 3px rgba(59, 130, 246, .25); }
.app-switch-text { font-size: 12px; font-weight: 600; color: #475569; }

/* ---- isi modal rincian voucher ------------------------------------------
   Tabel rinciannya datang SIAP PAKAI sebagai HTML dari ajaxkbon.php - berkas
   yang sama yang dipakai modal di payment-voucher-ap.php. Berkas itu TIDAK
   disentuh (dipakai menu lain juga), jadi penyesuaian tampilannya dikerjakan
   dari sini, ber-scope #pv-rincian supaya tidak merembet ke mana-mana.

   ajaxkbon.php mengirim DUA tabel: #mytdmodal (baris BPB, ber-thead) dan satu
   tabel tanpa bingkai berisi ringkasan nilai. Keduanya diatur terpisah. */
.pv-rinc-judul { font-size: 11px; font-weight: 700; text-transform: uppercase;
                 letter-spacing: .5px; color: #64748b; margin: 4px 0 8px; }
.pv-rinc-judul i { color: #94a3b8; margin-right: 4px; }

#pv-rincian table { width: 100%; font-size: 12px; margin-bottom: 10px; }
#pv-rincian #mytdmodal { border-collapse: separate; border-spacing: 0; }
#pv-rincian #mytdmodal thead th {
  background: #eef4ff; color: #1e3a8a; border-color: #dbe4f3;
  font-size: 10.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .4px; padding: 7px 8px; white-space: nowrap;
}
#pv-rincian #mytdmodal tbody td { border-color: #e9eef7; padding: 6px 8px; vertical-align: middle; }
#pv-rincian #mytdmodal tbody tr:hover td { background: #f5f8ff; }
/* Tabel ringkasan nilai: tanpa garis, angkanya rata kanan & rapat. */
#pv-rincian table:not(#mytdmodal) td { border: 0; padding: 3px 8px; }
#pv-rincian table:not(#mytdmodal) { margin-bottom: 0; }
#pv-tunggu { padding: 18px; text-align: center; color: #94a3b8; font-size: 12px; }

/* Flexibility Category memakai warnanya sendiri - itu memang isyarat lampu
   lalu lintas, jadi angka di sebelahnya tidak perlu dibaca dulu. */
.app-flex { font-size: .75rem; font-weight: 600; padding: 1px 8px; border-radius: 10px;
            border: 1px solid transparent; white-space: nowrap; }
.app-flex.is-green  { background: #e6f4ea; border-color: #b7e1c4; color: #1e7e34; }
.app-flex.is-yellow { background: #fff6e0; border-color: #f3dca0; color: #8a6100; }
.app-flex.is-red    { background: #fdecea; border-color: #f5c2bd; color: #a3281c; }
.app-flex.is-kosong { color: #9aa0a6; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="pco-card">
    <!-- Kepala kartu: kotak ikon + judul + jejak menu, mengikuti create
         Petty Cash Out. -->
    <div class="pco-head">
      <span class="pco-head-icon"><i class="fas fa-calendar-plus"></i></span>
      <div>
        <h1>Form Payment Plan</h1>
        <span class="pco-crumb">AP &rsaquo; Payment Plan &rsaquo; Update Payment Plan &rsaquo; Create</span>
      </div>
    </div><!-- /.pco-head -->

    <div class="pco-body">

      <!-- ---------------- kepala dokumen ---------------- -->
      <div class="pco-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Header</div>
      <div class="form-row">
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="plan_date" class="form-label"><b>Payment Plan Date</b></label>
          <!-- Terisi HARI INI begitu halaman dibuka: tanggal rencana yang
               paling sering dipakai adalah hari pembuatan dokumennya, dan
               tombol Save baru menyala kalau isian ini terisi. Tetap bisa
               diganti lewat datepicker. -->
          <input type="text" class="form-control form-control-sm tanggal" id="plan_date"
                 name="plan_date" value="<?php echo date('d M Y'); ?>"
                 placeholder="dd Mon yyyy" autocomplete="off">
        </div>
        <!-- Dulu bernama "Note". Dinamai Description (5 Nov 2026) supaya satu
             istilah dipakai dgn kolom Description per baris di tabel bawah:
             isian ini keterangan DOKUMEN, dan tombol di sebelahnya
             menyalinnya ke setiap baris yang dicentang. Nama field-nya tetap
             'keterangan' - itu yang dibaca simpan_payment_plan.php dan nama
             kolom di ap_payment_plan_h. -->
        <div class="col-12 col-sm-8 col-xl-6 mb-2">
          <label for="keterangan" class="form-label"><b>Description</b></label>
          <input type="text" class="form-control form-control-sm" id="keterangan"
                 name="keterangan" maxlength="255"
                 placeholder="Optional - ticked rows follow this text">
        </div>

        <!-- ====================================================================
             SAKLAR "Apply to ticked rows" - SENGAJA DISEMBUNYIKAN (diminta
             pemakai, 5 Nov 2026). Perilakunya sekarang SELALU menyalin: isi
             Description di atas langsung mengalir ke setiap baris yang
             dicentang, termasuk baris yang baru dicentang sesudahnya.

             Saklarnya TIDAK DIHAPUS, hanya disembunyikan, karena yang
             dimatikan cuma pilihannya - bukan mekanismenya: input di bawah
             ini sudah 'checked', dan seluruh logika di blok skrip halaman
             ini membaca keadaan centangnya. Jadi kalau nanti pemakai ingin bisa
             mematikan penyalinan itu, CUKUP buang style="display:none;" di
             <div> berikut - tidak ada kode lain yang perlu disentuh.
             ==================================================================== -->
        <div class="col-12 col-sm-4 col-xl-3 mb-2 pco-actions align-items-end" style="display:none;">
          <label class="app-switch" for="sinkron_desk" title="Copy the description above into every ticked row">
            <input type="checkbox" id="sinkron_desk" checked>
            <span class="app-switch-track"><span class="app-switch-knob"></span></span>
            <span class="app-switch-text">Apply to ticked rows</span>
          </label>
        </div>
      </div>

      <!-- ---------------- saringan voucher ---------------- -->
      <div class="pco-sec"><i class="fa fa-search" aria-hidden="true"></i> Find payment vouchers</div>
      <div class="form-row">

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="dasar" class="form-label"><b>Date Basis</b></label>
          <!-- Nilainya ('tempo' / 'kbon') TIDAK diubah - itu yang dibaca
               ajx_cari-pv-payment-plan.php; yang diganti cuma tulisannya,
               ke bahasa yang dipakai tim AP. -->
          <select class="form-control app-select2" id="dasar" name="dasar">
            <option value="tempo">Due Date</option>
            <option value="kbon">PV Date</option>
          </select>
        </div>

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="start_date" class="form-label"><b>From</b></label>
          <!-- From & To dua-duanya HARI INI (diminta pemakai, 5 Nov 2026).
               Dulu sebulan penuh (tanggal 1 s/d akhir bulan); sekarang
               rentangnya dimulai sempit dan dilebarkan sendiri oleh pemakai,
               supaya pencarian pertama tidak langsung menarik ratusan
               voucher yang belum tentu dicari. -->
          <input type="text" class="form-control form-control-sm tanggal" id="start_date"
                 name="start_date" value="<?php echo date('d M Y'); ?>"
                 placeholder="Start Date" autocomplete="off">
        </div>

        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="end_date" class="form-label"><b>To</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="end_date"
                 name="end_date" value="<?php echo date('d M Y'); ?>"
                 placeholder="End Date" autocomplete="off">
        </div>

        <!-- Daftar supplier panjang (ratusan), jadi dropdown-nya berkotak
             cari - select2 memasangnya sendiri, tidak perlu data-live-search.

             DULU bootstrap-select: kelas form-control ikut menempel di
             pembungkus .bootstrap-select sedangkan tombol di dalamnya punya
             bingkai sendiri, jadi tampak dua kotak bertumpuk dan tingginya
             tidak sebaris dgn isian tanggal di kirinya. -->
        <div class="col-12 col-sm-6 col-xl-3 mb-2">
          <label for="supplier" class="form-label"><b>Supplier</b></label>
          <select class="form-control app-select2" id="supplier" name="supplier">
            <option value="" selected="selected">All Suppliers</option>
            <?php foreach ($supplierList as $s) { ?>
            <option value="<?php echo htmlspecialchars($s, ENT_QUOTES); ?>"><?php echo htmlspecialchars($s, ENT_QUOTES); ?></option>
            <?php } ?>
          </select>
        </div>

        <!-- Kotak centang "Not scheduled yet" DIHAPUS: layar ini memang hanya
             untuk voucher yang belum dijadwalkan, jadi tidak ada gunanya
             dimatikan. .app-btn-ctl mengunci tinggi tombol ke tinggi kontrak
             isian (--app-ctl-h), supaya Search benar-benar sebaris dgn kotak
             tanggal & supplier di kirinya. -->
        <div class="col-12 col-sm-6 col-xl-3 mb-2 pco-actions align-items-end">
          <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="btn-cari">
            <i class="fa fa-search" aria-hidden="true"></i> Search
          </button>
        </div>

      </div><!-- /.form-row -->

      <!-- ---------------- hasil ---------------- -->
      <div class="pco-sec"><i class="fa fa-list" aria-hidden="true"></i> Payment vouchers
        <span class="text-muted ml-2" id="app-info" style="text-transform:none;letter-spacing:0;font-weight:500;">Pick a period, then press Search.</span>
        <!-- Kunci warna baris. Nilai flexibility-nya sendiri tetap terbaca
             lengkap di modal rincian voucher - ini hanya supaya warnanya
             tidak perlu ditebak. -->
        <span class="app-flx-ket">Supplier flexibility
          <span><i class="app-flx-dot is-green"></i>Green</span>
          <span><i class="app-flx-dot is-yellow"></i>Yellow</span>
          <span><i class="app-flx-dot is-red"></i>Red</span>
          <span><i class="app-flx-dot is-kosong"></i>Not set</span>
        </span>
      </div>

      <!-- .app-loading-wrap: area yang ditutup overlay saat data ditarik. -->
      <div class="app-loading-wrap" id="ppLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>

        <!-- TANPA .table-responsive: pembungkus yang bisa digulir ke samping
             membuat modul Responsive menyangka ruangnya selalu cukup, jadi
             tidak ada kolom yang dikolaps di layar sempit.

             KOLOM PEMBUKA "+" MILIK Responsive DIHAPUS (diminta pemakai,
             5 Nov 2026): di layar lebar tidak pernah ada yang disembunyikan,
             jadi yang terlihat cuma satu kolom kosong di kiri kotak centang.
             Gantinya rincian lengkap tiap voucher dibuka dgn mengklik NOMOR
             VOUCHER-nya - termasuk Term, Relationship, Item Type dan
             Flexibility yang kolomnya ikut dibuang dari tabel ini. -->
        <table id="table-pv" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th style="text-align:center;vertical-align:middle;"><input type="checkbox" id="pilih-semua" title="Tick every row on this page"></th>
              <!-- URUTAN (diminta pemakai, 5 Nov 2026): nomor voucher dan
                   tanggalnya di kiri, nama supplier menyusul - yang dicari
                   orang di layar ini nomor vouchernya, bukan suppliernya. -->
              <th style="text-align:center;vertical-align:middle;">Payment Voucher No</th>
              <th style="text-align:center;vertical-align:middle;">PV Date</th>
              <th style="text-align:center;vertical-align:middle;">Supplier</th>
              <th style="text-align:center;vertical-align:middle;">Due Date</th>
              <!-- Rencana bayar. Selama barisnya belum dicentang yang
                   tampil adalah rencana yang BERLAKU SEKARANG: Kamis di
                   minggu Minggu-Sabtu milik jatuh temponya (aturan tim AP,
                   5 Nov 2026), dibaca dari ap_payment_plan_awal.

                   Begitu barisnya DICENTANG, sel ini berubah jadi isian
                   tanggal berkalender (diminta pemakai, 5 Nov 2026) - itulah
                   tanggal yang akan DISIMPAN untuk voucher tsb, dan ISINYA
                   TETAP rencana awal tadi sampai pemakai sendiri yang
                   mengubahnya. Jadi satu dokumen boleh memuat tanggal yang
                   berbeda-beda, dan tanggal di kepala formulir perannya
                   tinggal tanggal DOKUMEN. -->
              <th style="text-align:center;vertical-align:middle;">Payment Plan</th>
              <th style="text-align:center;vertical-align:middle;">Curr</th>
              <!-- Yang DIBUANG dari tabel ini beserta alasannya:
                     Current Plan  - isinya pasti "Not Scheduled", karena
                                     daftarnya selalu disaring ke voucher yang
                                     belum punya rencana.
                     Tax Base, VAT - yang dipakai memutuskan di layar ini
                                     hanya nilai yang akan dibayar.
                     Term, Relationship, Item Type, Flexibility
                                   - pindah ke modal rincian.
                   Semuanya tetap ada di Excel (ekspor_payment_plan.php). -->
              <th style="text-align:center;vertical-align:middle;">Total</th>
              <!-- Description PER BARIS: tersimpan ke ap_payment_plan_det.
                   keterangan, terpisah dari Description dokumen di kepala
                   formulir (ap_payment_plan_h.keterangan). -->
              <th style="text-align:center;vertical-align:middle;">Description</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div><!-- /.app-loading-wrap -->

    </div><!-- /.pco-body -->

    <!-- Tombol simpan duduk di kaki kartu, sama spt formulir Petty Cash
         Out - bukan menempel di sebelah isian tanggal. -->
    <div class="pco-foot">
      <div class="pco-actions">
        <button type="button" class="app-btn app-btn-success app-btn-sm" id="btn-simpan" disabled>
          <i class="fa fa-save" aria-hidden="true"></i> Save
        </button>
        <!-- Back memakai varian -danger, sama dgn tombol Back di formulir AP
             lain (create_request_dn.php, formftrcbd.php, edit_ftrdp.php) -
             bukan -light yang nyaris putih dan tidak terbaca sbg tombol. -->
        <a href="list-payment-plan.php" class="app-btn app-btn-danger app-btn-sm">
          <i class="fa fa-angle-double-left" aria-hidden="true"></i> Back to List
        </a>
      </div>
    </div><!-- /.pco-foot -->

  </div><!-- /.pco-card -->
</div><!-- /.container-fluid -->

<!-- Rincian satu payment voucher, terbuka waktu nomornya diklik di tabel.
     Isinya diambil dari BARIS YANG SUDAH ADA di tabel - tidak menembak server
     lagi, jadi terbuka seketika dan tidak ada yang bisa melenceng dari angka
     yang barusan dilihat.

     Kosakata .app-modal / .app-mod-info datang dari app-skin-form.css yang
     sudah ditaut halaman ini; .ftl-modal milik halaman DAFTAR sengaja tidak
     dipakai supaya berkas CSS halaman daftar tidak perlu ikut ditarik. -->
<div class="modal fade" id="modal-pv" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span id="pv-judul"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <!-- Kepala voucher: dari baris yang sudah ada di tabel, langsung
             tampil tanpa menunggu permintaan apa pun. -->
        <div class="app-mod-info" id="pv-info"></div>

        <!-- Rincian BPB + ringkasan nilainya. Ini yang ditarik dari server,
             jadi ada tanda tunggu di sini sementara kepala di atas sudah
             terbaca. -->
        <div class="pv-rinc-judul"><i class="fa fa-list" aria-hidden="true"></i> Goods receipt lines</div>
        <div id="pv-rincian"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="app-btn app-btn-dark app-btn-sm" data-dismiss="modal">
          <i class="fa fa-times" aria-hidden="true"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<!-- bootstrap-select TIDAK dimuat: dropdown halaman ini memakai select2,
     sama spt formulir Petty Cash Out. CSS-nya sudah dimuat header.php. -->
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');

  // Collapse/Expand icon
  $('#collapse-icon').addClass('fa-angle-double-left');

  // Collapse click
  $('[data-toggle=sidebar-colapse]').click(function () {
      SidebarCollapse();
  });

  function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
          SeparatorTitle.removeClass('d-flex');
      } else {
          SeparatorTitle.addClass('d-flex');
      }

      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
  /* Dropdown saringan. Panel pilihannya ditempel ke <body> supaya tidak
     terpotong kartu, dan daftar pendek (Date Basis, 2 pilihan) tidak diberi
     kotak cari - ambang 8 opsi, sama dgn formulir Petty Cash Out. */
  $(function () {
    $('.app-select2').each(function () {
      var $s = $(this);
      var o = { width: '100%', dropdownParent: $('body') };
      if ($s.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
      $s.select2(o);
    });
  });
</script>

<script>
$(document).ready(function () {

  /* Bentuk tanggal yang dipakai SELURUH halaman ini. Ditaruh di satu
     tempat supaya kalender, teks petunjuk isian, dan nilai awal dari PHP
     tidak bisa berbeda sendiri-sendiri.

     'dd M yyyy' -> "10 Oct 2026". Dipilih pemakai (5 Nov 2026) menggantikan
     'dd-mm-yyyy'. Untungnya bukan cuma soal selera: "10-11-2026" itu bisa
     dibaca 10 November ATAU 11 Oktober tergantung siapa yang membacanya,
     sedangkan "10 Nov 2026" tidak bisa salah baca - termasuk oleh
     strtotime() di sisi server. */
  var FORMAT_TGL = 'dd M yyyy';

  /* ---- kalender -----------------------------------------------------------
     Ada dua macam di halaman ini:
       SARINGAN From/To - kalender biasa.
       PAYMENT PLAN     - kalender yang MENANDAI HARI KAMIS, baik yang di
                          kepala formulir maupun yang muncul di tiap baris.
     todayHighlight dinyalakan dua-duanya. Bawaan bootstrap-datepicker itu
     MATI, jadi sebelum ini "hari ini" sebenarnya tidak pernah ditandai sama
     sekali - dan tanda Kamis baru ada gunanya kalau hari ini juga terlihat,
     supaya bedanya langsung kelihatan: kuning vs biru. */
  $('#start_date, #end_date').datepicker({
    format: FORMAT_TGL, autoclose: true, todayHighlight: true
  });

  var KAL_KET = '<div class="app-kal-ket">'
              + '<span><i class="is-kamis"></i>Thursday &middot; payment day</span>'
              + '<span><i class="is-today"></i>Today</span></div>';

  /* Memasangkan kalender bertanda Kamis. Elemen yang sudah punya kalender
     dilewati, jadi fungsi ini aman dipanggil berulang - dan memang dipanggil
     berulang: baris tabel digambar ulang tiap ganti halaman. */
  function pasangKalenderKamis($el) {
    $el.each(function () {
      var $i = $(this);
      if ($i.data('datepicker')) { return; }
      $i.datepicker({
        format: FORMAT_TGL,
        autoclose: true,
        todayHighlight: true,
        beforeShowDay: function (tgl) {
          /* getDay(): 0 = Minggu ... 6 = Sabtu, jadi Kamis = 4. (Bukan
             DAYOFWEEK punya MySQL yang 1..7 - di sana Kamis = 5.) */
          if (tgl.getDay() !== 4) { return undefined; }
          return { classes: 'app-kamis', tooltip: 'Thursday - payment day' };
        }
      });
      var dp = $i.data('datepicker');
      if (dp && dp.picker) { dp.picker.addClass('app-kal-kamis').append(KAL_KET); }
    });
  }

  /* Melepas kalender SEBELUM elemennya dibuang. Perlu, karena
     bootstrap-datepicker menempelkan panel kalendernya ke <body>, bukan ke
     dalam barisnya: kalau <tr>-nya dibuang begitu saja waktu ganti halaman,
     panel yang sedang terbuka tertinggal melayang di layar. */
  function lepasKalender($el) {
    $el.each(function () {
      if ($(this).data('datepicker')) { $(this).datepicker('remove'); }
    });
  }

  pasangKalenderKamis($('#plan_date'));

  /* Voucher yang dicentang. Kuncinya nomor voucher, jadi centangnya tetap
     terjaga waktu pindah halaman tabel. */
  var dipilih = {};

  /* Description per baris, kuncinya juga nomor voucher. DISIMPAN DI SINI,
     bukan dibaca dari kotak isiannya waktu Save: DataTables membuang dan
     menggambar ulang <tr> tiap kali ganti halaman, jadi apa pun yang hanya
     hidup di DOM akan hilang begitu pemakai pindah ke halaman 2 dan kembali.
     Kotak isiannya digambar DARI simpanan ini. */
  var deskripsi = {};

  /* Tanggal rencana PER BARIS, kuncinya juga nomor voucher. Disimpan di sini
     dgn alasan yang persis sama seperti deskripsi[] di atas, dan kotak
     tanggalnya digambar DARI simpanan ini. */
  var rencana = {};

  /* Nilai yang masuk ke atribut value="" harus dilolos-kan: isinya teks
     yang diketik pemakai, bisa memuat tanda petik. (Kolom lain tidak perlu -
     ajx_cari-pv-payment-plan.php sudah htmlspecialchars di sisi server.) */
  function lolosAtr(v) {
    return String(v === null || v === undefined ? '' : v)
      .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
      .replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  /* Tanggal yang dipakai satu baris kalau dicentang, berurutan:
       1. yang sudah disetel sendiri di barisnya; kalau belum
       2. rencana yang BERLAKU SEKARANG (plan_awal) - Kamis di minggu jatuh
          temponya; kalau itu pun kosong
       3. tanggal di kepala formulir.

     URUTAN 2 & 3 DITUKAR (diminta pemakai, 5 Nov 2026). Dulu tanggal kepala
     didahulukan, jadi mencentang satu baris langsung mengganti rencananya
     dgn tanggal dokumen - yang isinya hari ini. Yang diminta justru
     sebaliknya: rencana awal baris itu dipertahankan apa adanya, dan yang
     perlu lain dibetulkan di barisnya sendiri. */
  function rencanaBaris(no, awal) {
    if (rencana[no]) { return rencana[no]; }
    if (awal) { return awal; }
    return $('#plan_date').val().trim();
  }

  /* Isi sel kolom Payment Plan - lihat catatan di CSS .app-plan. Kelas
     .app-plan-sel dipasang di DUA-DUANYA supaya selnya bisa ditemukan lagi
     tanpa menghitung indeks <td>: kolom yang sedang disembunyikan Responsive
     benar-benar lenyap dari DOM, jadi indeks <td> tidak bisa dipercaya. */
  function gambarRencana(no, awal) {
    if (!dipilih[no]) {
      return '<span class="app-plan-sel app-plan-teks"'
           + ' title="Tick this row to reschedule it">' + (awal ? awal : '-') + '</span>';
    }
    return '<input type="text" class="form-control form-control-sm tanggal app-plan app-plan-sel"'
         + ' placeholder="dd Mon yyyy" autocomplete="off" value="'
         + lolosAtr(rencanaBaris(no, awal)) + '">';
  }

  /* Menyamakan wajah sel Payment Plan dgn keadaan centang barisnya, untuk
     baris yang SEDANG TERLIHAT. Dipanggil sesudah satu baris dicentang, dan
     sesudah Responsive memunculkan kembali kolom yang tadi disembunyikan -
     sel yang muncul lagi dibawa DataTables apa adanya dari simpanannya, jadi
     bisa ketinggalan zaman.

     Digambar langsung di DOM, bukan lewat draw(): menggambar ulang tabel
     akan merebut fokus dari kotak yang sedang diketik di baris lain. */
  function segarkanSelRencana($baris) {
    var $tr = ($baris && $baris.length) ? $baris : $('#table-pv tbody tr');
    $tr.each(function () {
      var $b = $(this);
      var d = tabel.row($b).data();
      if (!d) { return; }
      var $sel = $b.find('.app-plan-sel');
      if (!$sel.length) { return; }          /* kolomnya sedang disembunyikan */
      var mauIsian = !!dipilih[d.no_kbon];
      if ($sel.is('input') === mauIsian) {
        if (mauIsian && !$sel.is(':focus')) { $sel.val(rencanaBaris(d.no_kbon, d.plan_awal)); }
        return;
      }
      lepasKalender($sel);
      var $baru = $(gambarRencana(d.no_kbon, d.plan_awal));
      $sel.replaceWith($baru);
      pasangKalenderKamis($baru.filter('input.app-plan'));
    });
  }

  function hitungPilihan() {
    var n = Object.keys(dipilih).length;
    $('#btn-simpan').prop('disabled', n === 0 || $('#plan_date').val().trim() === '');
    $('#app-info').text(n ? (n + ' voucher' + (n === 1 ? '' : 's') + ' ticked.') : ($('#app-info').data('asli') || ''));
  }

  /* Flexibility datang dari master supplier apa adanya ('GREEN', 'Green',
     'green'), jadi warnanya dipilih dari huruf besar semua. */
  /* SATU TEMPAT yang memutuskan warna Flexibility, dipakai dua-duanya: pil
     di modal rincian dan latar baris di tabel. Kalau dipisah, suatu saat
     salah satunya akan ketinggalan waktu nilai baru ditambahkan di master. */
  function kodeFlex(v) {
    var k = (v || '').toString().trim().toUpperCase();
    return (k === 'GREEN' || k === 'YELLOW' || k === 'RED') ? k.toLowerCase() : '';
  }

  function pilFlex(v) {
    var t = (v || '-').toString();
    var kode = kodeFlex(v);
    return '<span class="app-flex is-' + (kode !== '' ? kode : 'kosong') + '">' + t + '</span>';
  }

  /* Rincian satu voucher, susunannya disamakan dgn modal di
     payment-voucher-ap.php (diminta pemakai 5 Nov 2026): kepala voucher yang
     lengkap, lalu baris BPB berikut ringkasan nilainya.

     Dua sumber, sengaja dipisah:
       - KEPALA dari baris yang sudah ada di tabel -> tampil seketika.
       - BARIS BPB dari ajaxkbon.php, berkas yang SAMA dgn yang dipakai modal
         payment-voucher-ap.php. Dipakai ulang, bukan ditulis query sendiri,
         supaya angka di dua menu tidak bisa berbeda. Berkas itu sendiri
         tidak disentuh.

     DP & CBD tidak perlu dipikirkan di sini: keduanya tinggal di
     kontrabon_h_dp / kontrabon_h_cbd, sedangkan daftar layar ini hanya
     membaca kontrabon_h - jadi tidak ada barisnya yang butuh
     ajaxkbondp.php / ajaxkboncbd.php. */
  function bukaDetailPv(d) {
    var hari = (d.term === '' || d.term === null || d.term === undefined)
             ? '-' : (d.term + ' day' + (String(d.term) === '1' ? '' : 's'));
    var isi = [
      ['PV Date',              d.tgl_kbon],
      ['Due Date',             d.tgl_tempo],
      ['Payment Plan (current)', d.plan_awal],
      ['Payment Plan (this document)',
        dipilih[d.no_kbon] ? rencanaBaris(d.no_kbon, d.plan_awal) : 'Not ticked yet'],
      ['Payment Term',         hari],
      ['Currency',             d.curr],
      ['Status',               d.status],
      ['Supplier',             d.nama_supp],
      ['Relationship',         d.relasi],
      ['Item Type',            d.item_type],
      ['Flexibility',          pilFlex(d.flexibility)],
      ['Created By',           d.create_user],
      ['Tax Invoice No',       d.no_faktur],
      ['Supplier Invoice No',  d.supp_inv],
      ['Supplier Invoice Date', d.tgl_inv],
      /* Total Invoice (bruto) BEDA dengan Payment Amount (yang akan dibayar,
         sudah dipotong PPh 23). Dua-duanya ditampilkan supaya jelas kenapa
         kolom Total di tabel lebih kecil dari Total Invoice di sini. */
      ['Tax Base',             d.tax_base],
      ['VAT',                  d.tax],
      ['Total Invoice',        d.total_invoice],
      ['Income Tax Art 23',    d.pph],
      ['Payment Amount',       d.total]
    ];
    var h = '';
    for (var i = 0; i < isi.length; i++) {
      var nilai = isi[i][1];
      if (nilai === '' || nilai === null || nilai === undefined) { nilai = '-'; }
      h += '<div><b>' + isi[i][0] + '</b><span>' + nilai + '</span></div>';
    }
    $('#pv-info').html(h);
    $('#pv-judul').html(d.no_kbon);
    $('#pv-rincian').html('<div id="pv-tunggu"><i class="fa fa-spinner fa-spin"></i> Loading voucher lines...</div>');
    $('#modal-pv').modal('show');

    /* Nomor voucher di JSON sudah ter-escape HTML di sisi server; yang
       dikirim ke ajaxkbon.php harus bentuk ASLINYA, kalau tidak nomor yang
       memuat tanda petik tidak akan ketemu. */
    var nomorAsli = $('<div>').html(d.no_kbon).text();

    $.ajax({ url: 'ajaxkbon.php', type: 'POST', data: { no_kbon: nomorAsli } })
      .done(function (html) {
        $('#pv-rincian').html(html);
      })
      .fail(function (xhr) {
        $('#pv-rincian').html('<div id="pv-tunggu" style="color:#b91c1c;">'
          + 'Could not load the voucher lines (HTTP ' + xhr.status + ').</div>');
      });
  }

  var tabel = $('#table-pv').DataTable({
    ordering: false,
    processing: true,
    serverSide: false,
    pageLength: 10,
    searching: true,
    info: true,
    autoWidth: false,
    stateSave: true,
    stateDuration: -1,

    /* RESPONSIVE tetap menyala - kolom yang tidak muat disembunyikan, bukan
       memaksa halaman digulir ke samping - tapi details DIMATIKAN: kolom
       pembuka "+" dibuang (lihat catatan di atas tabel). Rincian lengkap
       tiap voucher dibuka lewat nomor vouchernya, dan itu memuat SEMUA
       kolom, termasuk yang sedang disembunyikan di layar sempit. */
    responsive: { details: false },

    ajax: {
      url: 'ajx_cari-pv-payment-plan.php',
      type: 'POST',
      data: function (d) {
        d.start_date  = $('#start_date').val();
        d.end_date    = $('#end_date').val();
        d.supplier    = $('#supplier').val();
        d.dasar       = $('#dasar').val();
        /* SELALU '1': layar ini hanya menjadwalkan voucher yang belum
           punya rencana aktif. Kotak centangnya dihapus - lihat catatan
           di kepala berkas. */
        d.hanya_belum = '1';
      },
      dataSrc: function (res) {
        if (res.error) {
          Swal.fire({ icon: 'error', title: 'Could not load the list', text: res.error });
          return [];
        }
        /* Hasil pencarian baru = daftar voucher yang lain, jadi centang DAN
           description per baris dinolkan dua-duanya. Kalau deskripsi
           dibiarkan, teks milik voucher lama bisa menempel ke voucher baru
           yang kebetulan bernomor sama. */
        dipilih = {};
        deskripsi = {};
        rencana = {};
        $('#pilih-semua').prop('checked', false);
        var n = res.data ? res.data.length : 0;
        $('#app-info').data('asli', n + ' voucher' + (n === 1 ? '' : 's') + ' found.');
        hitungPilihan();
        return res.data || [];
      },
      /* Tanpa ini overlay-nya menggantung terus kalau permintaannya gagal,
         dan user tidak tahu apa yang terjadi. */
      error: function (xhr) {
        $('#ppLoad').removeClass('is-loading');
        Swal.fire({
          icon: 'error', title: 'Failed to load the list',
          text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')
        });
      }
    },

    /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
       dipertahankan saat layar menyempit. Centang & nomor voucher dipegang
       paling kuat - itu isi pekerjaan di layar ini. */
    columns: [
      { data: null, orderable: false, searchable: false, responsivePriority: 1,
        render: function (d, t, row) {
          if (t !== 'display') { return ''; }
          return '<input type="checkbox" class="app-centang"' +
                 (dipilih[row.no_kbon] ? ' checked' : '') + '>';
        } },
      /* Nomor voucher jadi tautan pembuka rincian. Isinya sudah di-escape
         di sisi server, jadi dipasang apa adanya. */
      { data: 'no_kbon',     responsivePriority: 1, render: function (d, t) {
          return (t !== 'display') ? d : '<a href="#" class="app-no-pv" title="Show voucher detail">' + d + '</a>';
        } },
      { data: 'tgl_kbon',    responsivePriority: 6 },
      { data: 'nama_supp',   responsivePriority: 4 },
      { data: 'tgl_tempo',   responsivePriority: 5 },
      /* Payment Plan: teks selama belum dicentang, isian tanggal begitu
         dicentang. Di luar 'display' tetap dikembalikan tanggalnya apa
         adanya, supaya kotak Search DataTables masih bisa mencarinya. */
      { data: 'plan_awal',   responsivePriority: 6,
        render: function (d, t, row) {
          return (t !== 'display') ? d : gambarRencana(row.no_kbon, d);
        } },
      { data: 'curr',        responsivePriority: 7 },
      { data: 'total',       responsivePriority: 2 },
      /* Description per baris. Nilainya DIGAMBAR dari simpanan deskripsi[],
         bukan dari DOM - lihat catatan di deklarasinya. searchable:false
         karena kotak Search DataTables membaca data, bukan isi <input>. */
      { data: null, orderable: false, searchable: false, responsivePriority: 2,
        render: function (d, t, row) {
          if (t !== 'display') { return ''; }
          return '<input type="text" class="form-control form-control-sm app-desk" maxlength="255"'
               + ' placeholder="Optional" value="' + lolosAtr(deskripsi[row.no_kbon]) + '">';
        } }
    ],

    /* Kelas "all" milik Responsive = kolom ini TIDAK PERNAH disembunyikan,
       seberapa pun sempit layarnya. Yang dikunci: kotak centang, nomor
       voucher, Total, dan Description - itu empat hal yang dikerjakan di
       layar ini; tanpa salah satunya layar ini tidak bisa dipakai di HP.
       Sisanya boleh disembunyikan, dan tetap terbaca lengkap di modal
       rincian yang dibuka dari nomor vouchernya. */
    /* Kelas .ftl-doc / .ftl-amt TIDAK dipakai di sini: aturannya ber-scope
       `.ftl-tbl td.ftl-...` di app-ftr-list.css, sedangkan tabel ini tabel
       form (.pco-card) - dan berkas itu sudah tidak ditaut di halaman ini.
       Menuliskannya cuma akan jadi kelas yang tak berpengaruh dan
       menyesatkan orang berikutnya. */
    /* SUSUNAN KOLOM sekarang 0..8 - Payment Plan disisipkan di 5, jadi
       kolom sesudahnya bergeser naik satu:
         0 centang   1 PV No   2 PV Date   3 Supplier   4 Due Date
         5 Payment Plan       6 Curr      7 Total      8 Description */
    /* LEBAR TIAP KOLOM DIPATOK (diminta pemakai, 5 Nov 2026). Tanpa ini
       lebarnya dibagi menurut isi terpanjang yang kebetulan ada di halaman -
       dan yang terpanjang selalu nomor voucher ('PV-AP/REG/NAG/2026/09/
       02190'), jadi kolomnya melebar jauh melebihi kebutuhannya sementara
       Supplier dan Description berdesakan.

       Angkanya PERSEN, bukan piksel, supaya tabelnya tetap memenuhi lebar
       kartu di layar mana pun dan tidak pernah memaksa halaman digulir ke
       samping; jumlahnya pas 100. autoWidth:false di atas yang membuat
       nilai ini benar-benar dipakai - dgn autoWidth menyala, DataTables
       menghitung sendiri dan menimpanya. */
    columnDefs: [
      { targets: [0], className: 'text-center all', width: '3%'  },
      { targets: [1], className: 'text-left all',   width: '17%' },
      { targets: [2], className: 'text-center',     width: '8%'  },
      { targets: [3], className: 'text-left',       width: '21%' },
      { targets: [4], className: 'text-center',     width: '8%'  },
      { targets: [5], className: 'text-center',     width: '9%'  },
      { targets: [6], className: 'text-center',     width: '5%'  },
      { targets: [7], className: 'text-right all',  width: '11%' },
      { targets: [8], className: 'text-left all',   width: '18%' }
    ],

    /* Kalender baris dipasang SESUDAH tiap gambar ulang dan dilepas
       SEBELUMNYA - lihat alasan melepasnya di lepasKalender(). */
    /* Warna baris menurut Flexibility suppliernya. Ditempel di sini - bukan
       di render salah satu kolom - karena yang diwarnai SELURUH baris, dan
       createdRow memang dipanggil tiap <tr> dibuat, termasuk waktu pindah
       halaman tabel. Nilai yang tidak dikenal (kosong, '-', atau isian baru
       di master yang belum punya warna) sengaja TIDAK diberi kelas: barisnya
       putih, bukan salah warna. */
    createdRow: function (row, data) {
      var kode = kodeFlex(data.flexibility);
      if (kode !== '') { $(row).addClass('app-flx-' + kode); }
    },

    preDrawCallback: function () {
      lepasKalender($('#table-pv tbody input.app-plan'));
      return true;
    },
    drawCallback: function () {
      pasangKalenderKamis($('#table-pv tbody input.app-plan'));
    },

    language: {
      emptyTable: 'No approved payment voucher found for this filter.',
      zeroRecords: 'No payment voucher matches your search.'
    }
  });

  /* Overlay mengikuti status processing DataTables. */
  tabel.on('processing.dt', function (e, settings, processing) {
    $('#ppLoad').toggleClass('is-loading', processing);
  });

  /* Klik nomor voucher -> rincian. Delegasi, jadi baris di halaman
     berikutnya ikut hidup tanpa dipasangi ulang. */
  $('#table-pv tbody').on('click', 'a.app-no-pv', function (e) {
    e.preventDefault();
    var data = tabel.row($(this).closest('tr')).data();
    if (data) { bukaDetailPv(data); }
  });

  /* Description per baris disimpan ke deskripsi[] tiap ketikan. 'input'
     (bukan 'change') supaya teks yang baru diketik tidak hilang kalau
     pemakai langsung menekan Save tanpa memindahkan kursor dulu. */
  $('#table-pv tbody').on('input', 'input.app-desk', function () {
    var data = tabel.row($(this).closest('tr')).data();
    if (!data) { return; }
    deskripsi[data.no_kbon] = $(this).val();
  });

  /* Kotak Description di baris TIDAK boleh ikut mencentang/ membuka apa pun
     waktu diklik. */
  $('#table-pv tbody').on('click', 'input.app-desk', function (e) {
    e.stopPropagation();
  });

  /* Tanggal rencana per baris disimpan ke rencana[] tiap kali berubah - baik
     dipilih dari kalender (bootstrap-datepicker ikut melepas event 'change'
     bawaan) maupun diketik lalu ditinggalkan.

     DIKOSONGKAN = kembali ke RENCANA AWAL baris itu (5 Nov 2026; dulu ke
     tanggal dokumen). Mengosongkan kotaknya artinya "batal, pakai yang tadi
     saja" - dan yang tadi adalah rencana awalnya. Tetap tidak ada baris yang
     bisa tersimpan tanpa tanggal: rencanaBaris() masih jatuh ke tanggal
     dokumen kalau rencana awalnya pun kosong. */
  $('#table-pv tbody').on('change', 'input.app-plan', function () {
    var data = tabel.row($(this).closest('tr')).data();
    if (!data) { return; }
    var v = $(this).val().trim();
    if (v === '') {
      delete rencana[data.no_kbon];
      v = rencanaBaris(data.no_kbon, data.plan_awal);
      $(this).val(v);
    }
    rencana[data.no_kbon] = v;
  });

  $('#table-pv tbody').on('click', 'input.app-plan', function (e) {
    e.stopPropagation();
  });

  /* Kolom yang tadi disembunyikan Responsive lalu muncul lagi membawa sel
     lamanya - disamakan ulang di sini, sekalian kalendernya dipasang. */
  tabel.on('responsive-resize.dt', function () {
    segarkanSelRencana();
    pasangKalenderKamis($('#table-pv tbody input.app-plan'));
  });

  /* Centang ditangkap di satu tempat (delegasi), jadi baris halaman
     berikutnya ikut terpantau tanpa dipasangi ulang. */
  $('#table-pv tbody').on('change', 'input.app-centang', function () {
    var $tr = $(this).closest('tr');
    var data = tabel.row($tr).data();
    if (!data) { return; }
    if ($(this).is(':checked')) {
      dipilih[data.no_kbon] = { no_kbon: data.no_kbon };
      /* Tanggal rencananya dicatat SAAT DICENTANG, bukan nanti waktu Save:
         kalau pemakai tidak menyentuh kotak tanggalnya sama sekali, yang
         tersimpan tetap rencana awal baris itu. */
      if (!rencana[data.no_kbon]) {
        rencana[data.no_kbon] = rencanaBaris(data.no_kbon, data.plan_awal);
      }
      /* Baris yang BARU dicentang ikut kebagian deskripsi dokumen selama
         saklarnya hidup - itu gunanya saklar, bukan tombol sekali tekan. */
      if (sinkronHidup()) {
        deskripsi[data.no_kbon] = $('#keterangan').val();
        $tr.find('input.app-desk').val($('#keterangan').val());
      }
      $tr.addClass('app-pilih');
    } else {
      delete dipilih[data.no_kbon];
      $tr.removeClass('app-pilih');
    }
    /* Sel Payment Plan berganti wajah: teks <-> isian tanggal. Tanggal yang
       tadi disetel TIDAK dihapus waktu centangnya dilepas - kalau barisnya
       dicentang lagi, yang tadi diketik kembali seperti semula. */
    segarkanSelRencana($tr);
    hitungPilihan();
  });

  /* Centang semua hanya berlaku untuk baris di HALAMAN yang terlihat -
     mencentang ratusan baris sekaligus tanpa dilihat lebih berbahaya
     daripada berguna. */
  $('#pilih-semua').on('change', function () {
    var nyala = $(this).is(':checked');
    $('#table-pv tbody input.app-centang').each(function () {
      $(this).prop('checked', nyala).trigger('change');
    });
  });

  /* ---- penyalinan Description kepala -> baris yang dicentang ---------
     Dikemudikan oleh #sinkron_desk, yang saat ini 'checked' dan kotaknya
     DISEMBUNYIKAN - jadi penyalinannya selalu jalan. Keadaan centang itu
     tetap dibaca di sini (bukan di-hardcode true) supaya saklarnya cukup
     dimunculkan lagi kalau nanti pemakai ingin bisa mematikannya; lihat
     catatan di markup-nya. */
  function sinkronHidup() { return $('#sinkron_desk').is(':checked'); }

  /* Menyalin Description dokumen ke tiap baris yang dicentang. Kotak yang
     sedang TERLIHAT diperbarui langsung di DOM - bukan lewat draw() - karena
     menggambar ulang tabel akan merebut fokus dari kotak yang sedang
     diketik, dan tiap huruf jadi terasa patah. Baris di halaman lain tidak
     perlu disentuh: kotaknya digambar dari deskripsi[] waktu halaman itu
     ditampilkan. */
  function alirkanDeskripsi() {
    var teks = $('#keterangan').val();
    for (var k in dipilih) {
      if (dipilih.hasOwnProperty(k)) { deskripsi[k] = teks; }
    }
    $('#table-pv tbody tr').each(function () {
      var d = tabel.row(this).data();
      if (d && dipilih[d.no_kbon]) { $(this).find('input.app-desk').val(teks); }
    });
  }

  $('#sinkron_desk').on('change', function () {
    if (sinkronHidup()) { alirkanDeskripsi(); }
  });
  $('#keterangan').on('input', function () {
    if (sinkronHidup()) { alirkanDeskripsi(); }
  });

  /* Tanggal di kepala TIDAK LAGI mengalir ke baris yang dicentang (diminta
     pemakai, 5 Nov 2026). Dulu mengalir, dan itu masuk akal selama baris
     belum punya rencana sendiri. Sejak tiap baris membawa rencana awalnya -
     Kamis di minggu jatuh temponya - penyalinan itu justru menghapus
     satu-satunya tanggal yang sudah benar di baris tsb.

     Perannya sekarang tinggal dua: tanggal DOKUMEN (ap_payment_plan_h.
     plan_date) dan cadangan untuk baris yang rencana awalnya kosong - lihat
     rencanaBaris(). Yang perlu dijadwalkan lain dari rencana awalnya diubah
     di kotak tanggal barisnya masing-masing. */
  $('#plan_date').on('change keyup', hitungPilihan);

  $('#btn-cari').on('click', function () {
    if (Object.keys(dipilih).length) {
      Swal.fire({
        icon: 'warning',
        title: 'Unsaved selection',
        text: 'Searching again will clear the vouchers you have ticked. Continue?',
        showCancelButton: true,
        confirmButtonText: 'Discard and search'
      }).then(function (r) { if (r.isConfirmed) { tabel.ajax.reload(); } });
      return;
    }
    tabel.ajax.reload();
  });

  /* Tombol dikunci selama penyimpanan - supaya dokumen tidak terbuat dua kali. */
  $('#btn-simpan').on('click', function () {
    /* Description per baris diambil dari simpanan deskripsi[], bukan dari
       DOM: baris di halaman tabel yang tidak sedang ditampilkan tidak punya
       <input> sama sekali. Field-nya bernama 'keterangan' - itu yang dibaca
       simpan_payment_plan.php dan nama kolomnya di ap_payment_plan_det. */
    var baris = [];
    for (var k in dipilih) {
      if (dipilih.hasOwnProperty(k)) {
        baris.push({
          no_kbon:    k,
          keterangan: deskripsi[k] || '',
          /* Tanggal rencana PER VOUCHER. Kalau barisnya tidak pernah
             disentuh, isinya = tanggal di kepala formulir. */
          plan_date:  rencana[k] || $('#plan_date').val().trim()
        });
      }
    }
    if (!baris.length) { return; }
    if ($('#plan_date').val().trim() === '') {
      Swal.fire({ icon: 'warning', title: 'Payment plan date is empty', text: 'Please fill it in first.' });
      return;
    }

    var $tombol = $('#btn-simpan, #btn-cari').prop('disabled', true);
    Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

    $.ajax({
      url: 'simpan_payment_plan.php',
      type: 'POST',
      dataType: 'json',
      data: {
        plan_date:  $('#plan_date').val(),
        keterangan: $('#keterangan').val(),
        baris:      JSON.stringify(baris)
      }
    }).done(function (res) {
      if (res.status !== 'success') {
        Swal.fire({ icon: 'error', title: 'Not saved', text: res.message || 'Unknown error' });
        $tombol.prop('disabled', false);
        return;
      }
      var lanjut = function () { window.location.href = 'list-payment-plan.php'; };
      if (res.ditolak && res.ditolak.length) {
        Swal.fire({
          icon: 'warning',
          title: res.message,
          html: 'These vouchers were skipped:<br><small>' + res.ditolak.join('<br>') + '</small>'
        }).then(lanjut);
      } else {
        Swal.fire({ icon: 'success', title: res.message, timer: 1800, showConfirmButton: false }).then(lanjut);
      }
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Not saved', text: 'The server did not answer. Please try again.' });
      $tombol.prop('disabled', false);
    });
  });

});
</script>
