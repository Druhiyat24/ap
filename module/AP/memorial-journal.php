<?php include '../header.php' ?>

<!-- Overlay loading BERSAMA (.app-loading + .app-spinner). Berkas ini AMAN
     ditaut langsung: ia berada di ujung rantai - tidak meng-@import apa pun -
     jadi tidak kena jebakan cache yang menimpa app-skin.css, yang meng-@import
     app-skin-form.css TANPA nomor versi sehingga perubahan di dalamnya tidak
     pernah sampai ke browser. Versinya diambil dari mtime berkasnya sendiri. -->
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<style type="text/css">
    /* ========================================================================
       OPSI B — tampilan yang dipilih user (30 Sep 2026) dari kanvas mockup.
       Kosakatanya diberi awalan .mjb- (Memorial Journal, opsi B) supaya tidak
       bentrok dgn apa pun di header.php / main.css.

       SENGAJA DITULIS DI HALAMAN INI, bukan di module/css/app-skin*.css:
       (1) supaya menu lain tidak ikut berubah tanpa diminta, dan (2) karena
       app-skin.css meng-@import app-skin-form.css TANPA nomor versi - perubahan
       di sana tidak pernah sampai ke browser sampai mtime berkas induknya ikut
       berubah, dan itu sempat membuat tombol tampil tanpa warna sama sekali.
       Kalau nanti tampilan ini mau dipakai menu lain, barulah dipindahkan ke
       app-skin dan cache-busting-nya diperbaiki sekalian.
       ======================================================================== */

    body { background-color: #f4f6fb; }

    /* ---- Kartu ---- */
    .mjb-card {
        background: #fff;
        border: 1px solid #e6ebf3;
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    /* ---- Kepala kartu: kotak ikon navy + judul + jejak menu ---- */
    .mjb-head {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 11px;
        padding: 11px 18px;
        border-bottom: 0;
        /* Gradien NAVY - keluarga warna yang SAMA dgn kepala tabel (#1E3A8A),
           bukan lagi periwinkle terang. Tiga versi terang sebelumnya
           (#e6eeff, #c8dcff, #8fb6f7) selalu bentrok: headernya pastel,
           kepala tabel di bawahnya navy pekat, jadi keduanya terbaca sebagai
           dua biru yang berbeda. Sekarang keduanya berangkat dari #1E3A8A
           yang sama persis. Konsekuensinya teks di atasnya HARUS putih -
           lihat aturan judul, jejak menu & kotak ikon di bawah. */
        background: linear-gradient(90deg, #1E3A8A 0%, #2f5bbf 55%, #3b82f6 100%);
    }
    .mjb-head-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        /* Dulu kotak navy pekat di atas latar putih. Latarnya kini navy juga,
           jadi kotak navy akan hilang menyatu - diganti kaca transparan yang
           justru terbaca di atas warna apa pun sepanjang gradien. */
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .3);
        color: #fff;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }
    .mjb-head h1 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 700;
        color: #fff;
        letter-spacing: .01em;
    }
    .mjb-head .mjb-crumb {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        /* Putih diredupkan, bukan abu: di atas gradien navy, abu apa pun
           terbaca kotor. Sengaja tidak terlalu redup supaya tetap terbaca di
           ujung kiri gradien yang paling pekat. */
        color: rgba(255, 255, 255, .78);
    }

    /* ---- Panel filter ---- */
    .mjb-filter { padding: 13px 18px 15px; background: #fafbfe; }

    .mjb-label {
        display: block;
        margin-bottom: 4px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #64748b;
    }
    /* Tinggi SEMUA kontrol dikunci satu angka supaya sebaris selalu rata. */
    .mjb-filter .form-control,
    .mjb-filter .select2-container--default .select2-selection--single {
        height: 34px;
        border: 1px solid #dbe4f3;
        border-radius: 9px;
        background-color: #fff;
        color: #1e293b;
        font-size: 13px;
        box-shadow: none;
    }
    .mjb-filter .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }
    /* select2 punya markup sendiri; tinggi & posisi teksnya disetel terpisah. */
    .mjb-filter .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        padding-left: 11px;
        color: #1e293b;
        font-size: 13px;
    }
    .mjb-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px; }
    .mjb-filter .select2-container--default.select2-container--focus .select2-selection--single,
    .mjb-filter .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }
    /* Ikon kalender ditempel lewat CSS, jadi markup-nya tidak perlu diubah. */
    .mjb-filter input.tanggal {
        padding-right: 32px;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230e7c8f' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 11px center;
        background-size: 14px 14px;
    }


    /* --------------------------------------------------------------------
       select2 & kalender: PANEL POPUP-nya disisipkan ke <body>, DI LUAR
       .mjb-filter. Aturan yang discope ke .mjb-filter cuma kena kotak yang
       masih tertutup - begitu dibuka, daftar pilihan & kalendernya kembali
       ke tampilan bawaan. Karena itu blok ini TANPA scope.
       Nilainya disalin dari module/css/app-skin-form.css (bagian 2 & 4),
       dgn var() diganti nilai harfiah karena berkas itu tidak dimuat di sini.
       -------------------------------------------------------------------- */
    .select2-container--default .select2-selection--single {
        border: 1px solid #dbe4f3; border-radius: 9px; background: #fff;
        height: 34px; padding: 0 12px; outline: none;
        display: flex; align-items: center;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155; font-size: 13px; line-height: 1.35; padding: 0 20px 0 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #94a3b8; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 100%; top: 0; right: 8px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #94a3b8 transparent transparent transparent; }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b { border-color: transparent transparent #94a3b8 transparent; }

    .select2-container--default .select2-dropdown {
        border: 1px solid #e6ebf3; border-radius: 10px;
        box-shadow: 0 14px 36px rgba(15, 23, 42, .15); padding: 6px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #dbe4f3; border-radius: 8px; padding: 7px 12px; font-size: 13px; box-shadow: none;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .15); outline: none;
    }
    .select2-container--default .select2-results__option {
        border-radius: 7px; padding: 8px 12px; font-size: 13px; color: #334155;
        white-space: normal; word-break: break-word;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #e4f6f9; color: #0e7c8f; }
    .select2-container--default .select2-results__option[aria-selected=true] { background: #d2eff5; color: #0e7c8f; font-weight: 600; }
    .select2-container--default .select2-results > .select2-results__options::-webkit-scrollbar { width: 8px; }
    .select2-container--default .select2-results > .select2-results__options::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }

    /* Kalender bootstrap-datepicker */
    .datepicker.datepicker-dropdown {
        border: 1px solid #e6ebf3; border-radius: 12px;
        box-shadow: 0 14px 38px rgba(15, 23, 42, .16); padding: 12px; font-size: 13px;
    }
    .datepicker.datepicker-dropdown:before,
    .datepicker.datepicker-dropdown:after { display: none !important; }
    .datepicker table { width: 100%; }
    .datepicker table tr th { font-weight: 700; color: #334155; }
    .datepicker .datepicker-switch { font-weight: 700; color: #1e3a8a; border-radius: 8px; }
    .datepicker .prev, .datepicker .next { color: #64748b; border-radius: 8px; font-size: 15px; }
    .datepicker .datepicker-switch:hover,
    .datepicker .prev:hover, .datepicker .next:hover,
    .datepicker tfoot tr th:hover { background: #eef4ff !important; color: #1e3a8a; }
    .datepicker table tr th.dow { color: #94a3b8; font-size: 10.5px; text-transform: uppercase; font-weight: 700; padding: 6px 0; }
    .datepicker table tr td { padding: 1px; }
    .datepicker table tr td.day { border-radius: 8px; color: #334155; padding: 6px 8px; transition: background .1s; }
    .datepicker table tr td.day:hover,
    .datepicker table tr td.focused { background: #eef4ff !important; color: #1e3a8a; }
    .datepicker table tr td.old, .datepicker table tr td.new { color: #cbd5e1; }
    .datepicker table tr td.today { background: #fef3c7 !important; color: #92400e !important; }
    .datepicker table tr td.active,
    .datepicker table tr td.active:hover,
    .datepicker table tr td.active.active {
        background: #1E3A8A !important; background-image: none !important; color: #fff !important;
        box-shadow: 0 3px 8px rgba(30, 58, 138, .3); text-shadow: none;
    }
    .datepicker table tr td span.month, .datepicker table tr td span.year { border-radius: 8px; }
    .datepicker table tr td span.month:hover, .datepicker table tr td span.year:hover { background: #eef4ff; color: #1e3a8a; }
    .datepicker table tr td span.active,
    .datepicker table tr td span.active:hover { background: #1E3A8A !important; background-image: none !important; color: #fff; text-shadow: none; }

    /* ---- Tombol pil ---- */
    .mjb-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 34px;
        padding: 0 16px;
        border: 0;
        /* 9px, bukan pil (999px): disamakan dgn .app-btn di app-skin-form.css
           yang dipakai halaman Create Memorial Journal. Dua halaman yang
           bersebelahan dalam satu alur kerja tidak boleh punya bentuk tombol
           yang beda. */
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        cursor: pointer;
        text-decoration: none;
        transition: transform .12s ease, box-shadow .15s ease, filter .15s ease;
    }
    .mjb-btn:hover { transform: translateY(-1px); filter: brightness(1.05); text-decoration: none; }
    .mjb-btn:active { transform: translateY(0); }
    .mjb-btn:focus { outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, .3); }
    .mjb-btn i { font-size: 12px; }
    .mjb-btn-sm { height: 31px; padding: 0 15px; font-size: 11.5px; }

    /* Keempatnya diselaraskan: kepekatan dibuat setara supaya tidak ada yang
       melompat keluar, dan Search memakai navy yang SAMA dgn kepala tabel &
       header - itu penanda aksi utama halaman ini. Sebelumnya biru terang +
       hijau + oranye kecokelatan + merah bata: empat saturasi berbeda yang
       saling berebut perhatian. Semuanya tetap lolos kontras teks putih. */
    .mjb-btn-primary { background: #1E3A8A; color: #fff; box-shadow: 0 4px 10px rgba(30, 58, 138, .3); }
    .mjb-btn-primary:hover { color: #fff; }
    .mjb-btn-success { background: #12855a; color: #fff; box-shadow: 0 4px 10px rgba(18, 133, 90, .26); }
    .mjb-btn-success:hover { color: #fff; }
    .mjb-btn-warning { background: #a16207; color: #fff; box-shadow: 0 4px 10px rgba(161, 98, 7, .24); }
    .mjb-btn-warning:hover { color: #fff; }
    .mjb-btn-danger  { background: #9f2d28; color: #fff; box-shadow: 0 4px 10px rgba(159, 45, 40, .24); }
    .mjb-btn-danger:hover { color: #fff; }

    .mjb-actions { display: flex; gap: 7px; flex-wrap: wrap; align-items: flex-end; }

    /* KAPAN TOMBOL TURUN KE BARIS SENDIRI.
       Keempat tombol butuh ~405px berjajar. col-md-3 baru selebar itu kalau
       viewport-nya di atas ~1900px - jadi di layar biasa mereka PAS-PASAN, dan
       begitu jendelanya dipersempit sedikit saja mereka membungkus jadi 2-3
       baris DI DALAM kolomnya yang sempit: Search+Create sebaris, Verifikasi
       sendiri, Selisih Kurs sendiri. Berantakan.
       Di bawah ambang itu kolomnya dijadikan satu baris penuh sendiri, dengan
       jarak atas supaya tidak menempel ke field di atasnya, dan nowrap supaya
       keempatnya tetap berjajar - ruangnya memang sudah cukup. */
    @media (max-width: 1899.98px) {
        .mjb-filter .mjb-col-actions {
            flex: 0 0 100%;
            max-width: 100%;
            margin-top: .85rem;
        }
    }
    /* Di bawah xl kolom tombol memang sudah col-12, tapi jarak atasnya tetap
       perlu supaya tidak menempel ke field di atasnya. */
    @media (max-width: 1199.98px) {
        .mjb-filter .mjb-col-actions { margin-top: .85rem; }
    }

    /* nowrap HANYA saat barisnya memang cukup lebar. Kalau dipasang tanpa batas
       bawah, di ponsel (~360-430px) empat tombol yang butuh ~403px berjajar
       akan MELUBER keluar layar dan halaman jadi bisa digeser ke samping.
       Di bawah 576px mereka dibiarkan membungkus seperti biasa. */
    @media (min-width: 576px) and (max-width: 1899.98px) {
        .mjb-filter .mjb-col-actions .mjb-actions { flex-wrap: nowrap; }
    }

    /* ---- Layar kecil: rapatkan sedikit supaya isinya tidak terdorong ---- */
    @media (max-width: 575.98px) {
        .mjb-head { padding: 10px 14px; }
        .mjb-head h1 { font-size: 14.5px; }
        .mjb-filter { padding: 12px 14px 14px; }
        /* Tiap tombol memakai separuh baris: dua-dua ke bawah, bukan empat
           tombol berbeda lebar yang menumpuk tidak beraturan. */
        .mjb-filter .mjb-col-actions .mjb-actions .mjb-btn { flex: 1 1 calc(50% - 4px); }
    }
    /* Empat pil sebaris di kolom yang tidak lebar: paddingnya dirapatkan
       sedikit supaya tidak perlu membungkus, tanpa mengubah tingginya. */
    .mjb-actions .mjb-btn { padding: 0 13px; }

    /* ---- Tabel ---- */
    .mjb-tbl { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0; }
    /* Kepala tabel DILEMBUTKAN. Sebelumnya navy pekat #1E3A8A - sama persis
       dgn pita header di atasnya, jadi dua blok navy bertumpuk dan saling
       beradu: tidak ada yang lebih penting dari yang lain. Sekarang cuma
       SATU blok pekat di halaman ini (header), dan kepala tabel turun jadi
       latar biru muda dgn teks navy - masih satu keluarga warna, tapi jelas
       berada di tingkat kedua. Kontras teks navy di atas #eef3fb sangat
       tinggi, jadi keterbacaannya justru naik. */
    .mjb-tbl thead th {
        background: #eef3fb;
        color: #1e3a8a;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        white-space: nowrap;
        vertical-align: middle;
        padding: 9px 10px;
        border: 0;
        border-bottom: 2px solid #c9d8f0;
    }
    .mjb-tbl thead th:first-child { border-top-left-radius: 10px; }
    .mjb-tbl thead th:last-child  { border-top-right-radius: 10px; }
    .mjb-tbl tbody td {
        padding: 8px 10px;
        border: 0;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        font-size: 12px;
        /* line-height dirapatkan dari bawaan (~1.5): Description sering turun
           ke baris kedua, dan di situlah tinggi baris paling banyak terbuang.
           Teksnya TETAP tampil utuh - yang dirapatkan cuma jarak antar baris. */
        line-height: 1.35;
        color: #334155;
    }
    .mjb-tbl tbody tr:hover td { background: #f5f8ff; }
    .mjb-tbl tbody tr:last-child td { border-bottom: 0; }
    /* Baris membuka modal detail kalau diklik - tanpa ini tidak ada petunjuknya. */
    .mjb-tbl tbody tr { cursor: pointer; }
    .mjb-tbl tbody td:last-child { cursor: default; }
    .mjb-doc { font-weight: 700; color: #1e3a8a; white-space: nowrap; }
    .mjb-amt { font-weight: 600; color: #0f172a; font-variant-numeric: tabular-nums; white-space: nowrap; }

    /* ---- Badge status ---- */
    .mjb-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }
    .mjb-badge.post   { background: #e6f7ec; color: #15724a; }
    .mjb-badge.draft  { background: #eef2ff; color: #312e81; }
    .mjb-badge.cancel { background: #fdecec; color: #b3312c; }
    .mjb-badge.netral { background: #eef2f7; color: #64748b; }
    .mjb-badge.lock   { background: #fdecec; color: #b91c1c; }

    /* ---- Tombol di kolom Action: pil kecil berlatar lembut ---- */
    /* nowrap: tombolnya TIDAK BOLEH turun ke baris kedua. Begitu diberi lebar
       minimum yang sama, dua tombol jadi lebih lebar dari kolom Action dan
       browser membungkusnya ke bawah. Tabel ini table-layout:auto, jadi dgn
       nowrap kolomnya yang MELEBAR mengikuti isi - lebar di <th> cuma jadi
       usulan, bukan batas keras. */
    .mjb-act { display: flex; flex-wrap: nowrap; justify-content: center; gap: 5px; }
    /* min-width + justify-content: tanpa keduanya lebar tombol mengikuti
       panjang teksnya, jadi "Cancel" selalu lebih lebar dari "Edit" dan
       kolom Action terlihat tidak rata dari baris ke baris. 88px diambil
       dari label terpanjang yang mungkin muncul di kolom ini ("Export"),
       jadi semua varian - Post, Edit, Cancel, Export - keluar sama besar. */
    .mjb-act .mjb-mini {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 74px;
        flex: 0 0 auto;
        gap: 5px;
        height: 28px;
        padding: 0 10px;
        border: 0;
        /* Mengikuti .app-btn-sm (8px), sekeluarga dgn tombol filter di atas. */
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        cursor: pointer;
        text-decoration: none;
        transition: transform .12s ease, filter .15s ease;
    }
    .mjb-act .mjb-mini:hover { transform: translateY(-1px); filter: brightness(.97); text-decoration: none; }
    .mjb-mini.is-cancel  { background: #fdecec; color: #b3312c; }
    .mjb-mini.is-cancel:hover  { color: #b3312c; }
    .mjb-mini.is-edit    { background: #fdf7e9; color: #8a5a06; }
    .mjb-mini.is-edit:hover    { color: #8a5a06; }
    .mjb-mini.is-post    { background: #eef4ff; color: #1d4ed8; }
    .mjb-mini.is-post:hover    { color: #1d4ed8; }
    .mjb-mini.is-export  { background: #f1fbf5; color: #15724a; }
    .mjb-mini.is-export:hover  { color: #15724a; }
    .mjb-locked { display: inline-flex; flex-direction: column; align-items: center; gap: 2px; }
    .mjb-locked small { font-size: 9.5px; color: #94a3b8; }

    /* Di layar sempit, kolom Action yang dikunci "all" ikut menentukan lebar
       minimum tabel. Dgn nowrap + min-width 74px, empat tombol butuh ~330px -
       cukup untuk memaksa tabelnya melebar melewati layar, persis gulir
       menyamping yang ingin dihindari. Di bawah md tombolnya dibiarkan
       membungkus ke bawah DI DALAM selnya. */
    @media (max-width: 767.98px) {
        .mjb-act { flex-wrap: wrap; }
        .mjb-act .mjb-mini { min-width: 0; padding: 0 9px; }
    }

    /* ---- Kontrol "+" dan baris detail milik DataTables Responsive ----
       Muncul hanya saat ada kolom yang dikolaps (layar sempit). Warnanya
       disamakan dgn kosakata halaman ini supaya tidak terlihat sebagai
       tempelan dari pustaka luar. */
    /* TANDA "+" HANYA MUNCUL KALAU ADA YANG DIKOLAPS.
       DataTables Responsive menambah kelas .collapsed pada <table> begitu ada
       kolom yang disembunyikan - TAPI hanya mode dtr-inline yang memakai kelas
       itu untuk menyembunyikan ikonnya. Mode dtr-column (yang dipakai di sini,
       karena kontrolnya diberi kolom sendiri) menggambar ikonnya TERUS, bahkan
       saat seluruh kolom muat. Jadi digerbangi sendiri di bawah ini.
       Kolomnya ikut menciut jadi nol lebar supaya tidak menyisakan lajur kosong
       di kiri tabel saat tidak ada yang dikolaps. */
    .mjb-tbl thead th:first-child,
    .mjb-tbl tbody td.dtr-control {
        width: 0;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .mjb-tbl tbody td.dtr-control:before { display: none !important; }

    .mjb-tbl.collapsed thead th:first-child,
    .mjb-tbl.collapsed tbody td.dtr-control {
        width: 1%;
        padding-left: 8px !important;
        padding-right: 0 !important;
    }
    .mjb-tbl.collapsed tbody td.dtr-control { cursor: pointer; }
    .mjb-tbl.collapsed tbody td.dtr-control:before { display: block !important; }
    .mjb-tbl tbody td.dtr-control:before {
        background-color: #1E3A8A !important;
        border: 0 !important;
        color: #fff !important;
        box-shadow: 0 2px 5px rgba(30, 58, 138, .3) !important;
        font-weight: 700;
    }
    .mjb-tbl tbody tr.dtr-expanded td.dtr-control:before { background-color: #b3312c !important; }
    .mjb-tbl tbody tr.child td { background: #f8fafc !important; }
    .mjb-tbl ul.dtr-details { display: block; width: 100%; padding: 4px 0; margin: 0; }
    .mjb-tbl ul.dtr-details > li {
        display: flex; gap: 12px; padding: 7px 4px;
        border-bottom: 1px solid #e8edf5; align-items: baseline;
    }
    .mjb-tbl ul.dtr-details > li:last-child { border-bottom: 0; }
    .mjb-tbl ul.dtr-details .dtr-title {
        min-width: 9.5rem; font-weight: 700; color: #64748b;
        font-size: 10px; text-transform: uppercase; letter-spacing: .05em;
    }
    .mjb-tbl ul.dtr-details .dtr-data { color: #0f172a; font-size: 12px; }

    /* ---- Chrome DataTables disamakan dgn kosakata di atas ---- */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter { margin-bottom: 9px; font-size: 12.5px; color: #475569; }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        height: 30px; border: 1px solid #dbe4f3; border-radius: 8px;
        background: #fbfcfe; color: #1e293b; font-size: 12.5px; padding: 0 10px;
    }
    .dataTables_wrapper .dataTables_filter input:focus,
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, .15); outline: none;
    }
    .dataTables_wrapper .dataTables_info { color: #94a3b8; font-size: 12px; }
    .dataTables_wrapper .pagination .page-link {
        border: 1px solid #e2e8f0; border-radius: 999px; margin: 0 2px;
        color: #334155; font-size: 12px; min-width: 34px; text-align: center;
    }
    .dataTables_wrapper .pagination .page-item.active .page-link {
        background: #1E3A8A; border-color: #1E3A8A; color: #fff; font-weight: 600;
    }
    .dataTables_wrapper .pagination .page-item.disabled .page-link { color: #cbd5e1; }

    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    div.dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }

    div.dataTables_wrapper .dataTables_info {
        float: left;
        margin-top: 10px;
    }

    .kbon-action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .kbon-action-buttons .btn {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 11px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kbon-action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    }

    /* ========================================================================
       MODAL DETAIL (kosakata .mjd-). Isinya dibentuk ajax_mj.php; di sini
       hanya gayanya. Birunya SENGAJA memakai nilai yang sama persis dengan
       tampilan halaman: #1E3A8A (kepala tabel & aksen), #eef3fb (latar tenang),
       #1d4ed8 (aksen ikon & tag), #e6ebf3 / #e0e8f7 (garis tepi kartu).
       Badge status memakai .mjb-badge yang sudah ada, bukan varian baru.
       ======================================================================== */
    #mymodal .modal-dialog { max-width: 1360px; width: 96vw; }
    #mymodal .modal-content { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 24px 60px rgba(15, 23, 42, .28); }
    #mymodal .modal-header {
        background: linear-gradient(90deg, #1E3A8A 0%, #2f5bbf 55%, #3b82f6 100%);
        border: 0; padding: 12px 20px; align-items: center; gap: 11px;
    }
    #mymodal .modal-title { display: flex; align-items: center; gap: 10px; margin: 0; font-size: 14px; font-weight: 700; color: #fff; }
    #mymodal .modal-header .close {
        color: #fff; opacity: 1; text-shadow: none; background: rgba(255, 255, 255, .18);
        width: 26px; height: 26px; padding: 0; border-radius: 7px; font-size: 13px; flex-shrink: 0;
    }
    #mymodal .modal-header .close:hover { background: rgba(255, 255, 255, .3); }
    #mymodal .modal-body { padding: 12px 16px 14px; max-height: 78vh; overflow-y: auto; background: #eef3fb; }

    /* ---- Panel info & Balance Summary BERDAMPINGAN ----
       Keduanya ringkas; menumpuknya membuang tinggi yang justru dibutuhkan
       tabel di bawah. Di layar sempit kembali bertumpuk sendiri. */
    .mjd-atas { display: grid; grid-template-columns: 300px 1fr; gap: 9px; align-items: stretch; margin-bottom: 9px; }
    @media (max-width: 991.98px) { .mjd-atas { grid-template-columns: 1fr; } }

    .mjd-panel { background: #fff; border: 1px solid #e0e8f7; border-radius: 10px; }
    .mjd-info { padding: 10px 14px; display: flex; flex-direction: column; justify-content: center; gap: 9px; }
    .mjd-info-item { display: flex; align-items: center; gap: 9px; }
    .mjd-ico {
        width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
        background: #eef4ff; color: #1d4ed8;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .mjd-ico.is-status { background: #e6f7ec; color: #15724a; }
    .mjd-info-teks { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
    .mjd-info-teks b {
        font-size: 9px; font-weight: 700; letter-spacing: .07em;
        text-transform: uppercase; color: #94a3b8;
    }
    .mjd-info-teks > span { font-size: 12px; font-weight: 600; color: #0f172a; }

    .mjd-judul {
        display: flex; align-items: center; gap: 7px;
        font-size: 9px; font-weight: 700; letter-spacing: .07em;
        text-transform: uppercase; color: #94a3b8;
    }
    .mjd-judul svg { color: #1d4ed8; }
    .mjd-judul-tbl { margin-bottom: 7px; }

    .mjd-bal { padding: 9px 15px 11px; display: flex; flex-direction: column; }
    .mjd-bal-tbl { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0; margin-top: 7px; }
    .mjd-bal-tbl th {
        font-size: 8.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
        color: #cbd5e1; padding: 0 0 5px; text-align: left; border: 0; background: none;
    }
    .mjd-bal-tbl th.is-deb { color: #1d4ed8; }
    .mjd-bal-tbl th.is-cre { color: #b45309; }
    .mjd-bal-tbl td { padding: 5px 0; border: 0; border-bottom: 1px solid #f2f5fa; font-size: 11.5px; color: #0f172a; }
    .mjd-bal-tbl .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; padding-right: 12px; }
    .mjd-bal-tbl th:last-child, .mjd-bal-tbl td:last-child { padding-right: 0; }
    .mjd-bal-total td {
        border-bottom: 0; border-top: 2px solid #1E3A8A;
        padding-top: 7px; font-size: 12.5px; font-weight: 700;
    }
    .mjd-bal-total td:first-child {
        font-size: 10px; letter-spacing: .05em; text-transform: uppercase;
    }
    /* margin-top:auto - lencana didorong ke dasar panel supaya tingginya rata
       dgn panel info di sebelah kiri. */
    .mjd-lencana { align-self: flex-start; margin-top: auto; padding-top: 0; }
    .mjd-bal .mjd-lencana { margin-top: 8px; }

    .mjd-pc-tag {
        display: inline-block; padding: 1px 8px; border-radius: 5px;
        background: #eef4ff; color: #1d4ed8; font-size: 10px; font-weight: 700;
    }

    /* ---- Tabel baris jurnal ---- */
    .mjd-tbl { border-collapse: separate; border-spacing: 0; margin: 0; }
    .mjd-tbl thead th {
        background: #eef3fb; color: #1e3a8a;
        font-size: 8.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
        padding: 7px 8px; border: 0; border-bottom: 2px solid #c9d8f0;
        white-space: nowrap; vertical-align: middle; text-align: left;
    }
    .mjd-tbl tbody td {
        padding: 5px 8px; border: 0; border-bottom: 1px solid #f2f5fa;
        vertical-align: middle; font-size: 10.5px; color: #475569;
    }
    .mjd-tbl tbody tr:hover td { background: #f5f8ff; }
    .mjd-tbl .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; color: #0f172a; font-weight: 600; }
    .mjd-tbl td.nol { color: #cbd5e1; font-weight: 400; }
    /* Kolom IDR tidak lagi punya gaya sendiri: nilainya dibaca sama pentingnya
       dgn Debit/Credit, jadi ikut .num (pekat) dan .nol (redup saat 0.00) -
       persis seperti dua kolom di sebelahnya. */
    /* Kode di atas, nama di bawah - dipakai COA maupun Cost Center. */
    .mjd-kode { display: block; font-size: 10.5px; font-weight: 700; color: #1e3a8a; white-space: nowrap; }
    .mjd-kode.is-cc { color: #334155; }
    .mjd-nama { display: block; font-size: 9px; color: #94a3b8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mjd-ket { font-size: 10px; line-height: 1.35; }

    /* Chrome DataTables di dalam modal dirapatkan - ruangnya lebih sempit
       daripada di halaman. */
    #details .dataTables_wrapper .dataTables_length,
    #details .dataTables_wrapper .dataTables_filter { margin-bottom: 7px; font-size: 11px; }
    #details .dataTables_wrapper .dataTables_length select,
    #details .dataTables_wrapper .dataTables_filter input { height: 26px; font-size: 11px; }
    #details .dataTables_wrapper .dataTables_info { font-size: 10.5px; padding-top: 8px; }
    #details .dataTables_wrapper .pagination .page-link { font-size: 10.5px; min-width: 28px; padding: 3px 8px; }
    .mj-badge { display: inline-block; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .mj-badge.post { background: #e6f7ec; color: #1f9d57; }
    .mj-badge.draft { background: #eef2ff; color: #3730a3; }
    .mj-badge.cancel { background: #fdecec; color: #dc2626; }
    /* Gaya .mj-detail-table & .mj-total-table DILEPAS: tabel detail sekarang
       memakai .mjd-tbl di blok modal di atas, dan tabel total terpisah sudah
       digantikan Balance Summary per Profit Center. */
</style>

<!-- MAIN -->
<!-- Jarak halaman dirapatkan (p-4 mt-4 -> p-3 mt-3): 24px di keempat sisi
     terasa boros untuk halaman daftar yang isinya memang banyak baris. -->
<div class="container-fluid mt-3 p-3">
    <!-- Card Filter -->
    <div class="mjb-card">
        <!-- Kepala kartu Opsi B: kotak ikon navy + judul + jejak menu. Pita
             gradien selebar kartu diganti ini supaya warnanya jadi aksen, bukan
             latar - judulnya tetap yang paling menonjol. -->
        <div class="mjb-head">
            <span class="mjb-head-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
            <div>
                <h1>List Memorial Journal</h1>
                <span class="mjb-crumb">Accounting &rsaquo; Memorial Journal</span>
            </div>
        </div>

        <div class="mjb-filter">
            <form id="form-data">
                <!-- Grid 12 kolom Bootstrap, pola yang sama dgn menu lain
                     (Report PPN Masukan, Report Faktur Pajak): row g-3 dgn
                     perbandingan 3 + 2 + 2 + 2 + 3 = 12.

                     TITIK AKTIFNYA xl (1200px), BUKAN md (768px). Sidebar di
                     aplikasi ini lebarnya TETAP 230px dan tidak pernah menciut
                     sendiri, jadi pada 768px sisa ruang isi cuma ~506px - satu
                     satuan kolom tinggal ~42px, dan Type yang 3 kolom jadi
                     ~126px. Tidak terpakai. Dgn xl, perbandingan itu baru
                     berlaku saat ruangnya memang cukup (di 1366px: Type ~276px).
                     Di antaranya (576-1199px, tablet) dua field per baris. -->
                <div class="row g-1">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <label for="nama_type" class="mjb-label">Type</label>
                        <select class="form-control select2" name="nama_type" id="nama_type">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "select id_cmj, nama_cmj from master_category_mj order by id_cmj");
                            while ($row = mysqli_fetch_array($sql)) {
                                echo '<option value="' . htmlspecialchars($row['id_cmj']) . '">' . htmlspecialchars($row['nama_cmj']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2">
                        <label for="status" class="mjb-label">Status</label>
                        <select class="form-control select2" name="status" id="status">
                            <option value="ALL" selected>ALL</option>
                            <option value="Draft">Draft</option>
                            <option value="Post">Post</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2">
                        <label for="start_date" class="mjb-label">From</label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?php echo date('d-m-Y'); ?>" placeholder="Start Date" autocomplete="off">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2">
                        <label for="end_date" class="mjb-label">To</label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?php echo date('d-m-Y'); ?>" placeholder="End Date" autocomplete="off">
                    </div>

                    <!-- d-flex + align-items-end: tombolnya menempel ke dasar
                         kolom, jadi sejajar dgn dropdown & input tanggal yang
                         punya label di atasnya - bukan melayang di tengah.
                         .mjb-col-actions dipakai media query di blok <style>:
                         di layar yang tidak cukup lebar, kolom ini turun jadi
                         satu baris penuh sendiri. -->
                    <div class="col-12 col-xl-3 d-flex align-items-end mjb-col-actions">
                        <div class="mjb-actions">
                            <button type="submit" class="mjb-btn mjb-btn-primary">
                                <i class="fa fa-search" aria-hidden="true"></i> Search
                            </button>
                            <?php
                            $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Acct - Create Memorial Journal'");
                            $rs = mysqli_fetch_array($querys);
                            $id = isset($rs['id']) ? $rs['id'] : 0;
                            if ($id == '54') {
                                echo '<button id="btncreate_new" type="button" class="mjb-btn mjb-btn-success"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create</button>';
                            }
                            ?>
                            <button id="btnverifikasi" type="button" class="mjb-btn mjb-btn-warning">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i> Verification
                            </button>
                            <button id="btnsync_fx" type="button" class="mjb-btn mjb-btn-danger">
                                <i class="fa fa-refresh" aria-hidden="true"></i> FX Revaluation
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Table -->
    <div class="mjb-card mt-3">
        <div class="card-body p-3">
            <!-- .app-loading-wrap: area yang ditutup overlay saat data ditarik.
                 Sebelumnya satu-satunya tanda "sedang memuat" adalah teks
                 "Processing..." bawaan DataTables di tengah tabel. -->
            <div class="app-loading-wrap" id="mjLoad">
                <div class="app-loading">
                    <div class="app-loading-box">
                        <div class="app-spinner"></div>
                        <div class="app-loading-text">Loading data...</div>
                    </div>
                </div>
            <!-- .table-responsive DILEPAS: pembungkus itu memberi gulir
                 MENDATAR, sedangkan DataTables Responsive justru mengolaps
                 kolom yang tidak muat. Kalau dipasang berbarengan, Responsive
                 mengira ruangnya selalu cukup (karena pembungkusnya bisa
                 digulir) dan tidak pernah mengolaps apa pun - di ponsel
                 tabelnya tetap melebar dan harus digeser-geser. -->
            <div>
                <!-- Opsi B: kepala navy dgn sudut membulat, baris tanpa garis
                     kotak - cuma pemisah tipis. Kelas .table bawaan Bootstrap
                     dilepas supaya tidak menimpa padding & garis .mjb-tbl. -->
                <table id="mytable" class="mjb-tbl" style="width:100%">
                    <thead>
                        <tr>
                            <!-- Kolom kosong khusus tanda "+" milik DataTables
                                 Responsive. Tanpa kolom sendiri, kontrolnya
                                 menempel di kolom PERTAMA - dan kolom pertama di
                                 sini adalah No Journal, yang klik-nya dipakai
                                 untuk membuka modal detail. Dua makna di satu sel
                                 yang sama membuat salah satunya pasti kalah. -->
                            <th style="width: 2.5%;"></th>
                            <th style="text-align: center;vertical-align: middle;width: 13%;">No Journal</th>
                            <th style="text-align: center;vertical-align: middle;width: 8%;">Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 11%;">Type</th>
                            <th style="text-align: center;vertical-align: middle;width: 5%;">Curr</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Debit</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Credit</th>
                            <th style="text-align: center;vertical-align: middle;width: 6%;">Status</th>
                            <th style="text-align: center;vertical-align: middle;width: 24%;">Description</th>
                            <th style="text-align: center;vertical-align: middle;width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            </div><!-- /.app-loading-wrap -->
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span id="txt_bpb"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times" aria-hidden="true"></span></button>
            </div>
            <!-- SELURUH isi modal (panel info, Balance Summary, tabel baris)
                 dibentuk ajax_mj.php dan dipasang ke #details. Panel infonya
                 TIDAK lagi diisi dari baris daftar: profit_center &amp; keterangan
                 berbeda di dalam satu dokumen (26% &amp; 60% dokumen), jadi nilai
                 dari satu baris tidak mewakili dokumennya. -->
            <div class="modal-body">
                <div id="details"></div>
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
<!-- CSS select2 sudah dimuat header.php sejak awal; yang belum cuma JS-nya,
     karena halaman ini dulu memakai bootstrap-select. -->
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
    $('#body-row .collapse').collapse('hide');
    $('#collapse-icon').addClass('fa-angle-double-left');
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse() {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        var SeparatorTitle = $('.sidebar-separator-title');
        if (SeparatorTitle.hasClass('d-flex')) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script>
    $(function() {
        // Type: daftarnya panjang & datang dari master_category_mj, jadi kotak
        // pencarian select2 tetap dipakai. Status cuma 4 pilihan tetap, jadi
        // pencariannya dimatikan.
        $('#nama_type').select2({ width: '100%' });
        $('#status').select2({ width: '100%', minimumResultsForSearch: Infinity });
    });
</script>

<script>
    /* ========================================================================
       Filter daftar (Type, Status, From, To) DISIMPAN lalu DIPULIHKAN.

       Sebelumnya: user menyaring daftar, membuka satu journal, menekan Back -
       dan kembali ke daftar yang sudah kosong lagi ke bawaan (ALL / hari ini),
       jadi harus menyaring ulang dari nol tiap kali selesai mengedit satu
       dokumen.

       sessionStorage, BUKAN localStorage: filternya bertahan selama tab itu
       masih dibuka (cukup untuk pulang-pergi ke halaman edit), tapi tidak
       menempel berhari-hari sehingga user membuka menu ini besok lusa dan
       bingung kenapa daftarnya menyaring periode lama.

       Blok ini SENGAJA dijalankan langsung, bukan di dalam $(function(){}).
       DataTables di bawah dibuat saat skripnya dibaca dan LANGSUNG menarik data
       memakai isi keempat field itu - kalau pemulihannya menunggu DOM ready,
       tarikan pertama sudah terlanjur memakai nilai bawaan. */
    (function () {
        var KUNCI = 'mjb-filter-mj';

        window.mjbSimpanFilter = function () {
            try {
                sessionStorage.setItem(KUNCI, JSON.stringify({
                    type:   $('#nama_type').val(),
                    status: $('#status').val(),
                    dari:   $('#start_date').val(),
                    sampai: $('#end_date').val()
                }));
            } catch (err) { /* mode privat / penyimpanan diblokir - abaikan */ }
        };

        try {
            var f = JSON.parse(sessionStorage.getItem(KUNCI) || 'null');
            if (f) {
                // Tipe yang tersimpan bisa saja sudah dihapus dari
                // master_category_mj. Dipasang hanya kalau opsinya masih ada,
                // supaya kotaknya tidak diam-diam jatuh ke pilihan pertama.
                if (f.type && $('#nama_type option[value="' + f.type + '"]').length) {
                    $('#nama_type').val(f.type);
                }
                if (f.status && $('#status option[value="' + f.status + '"]').length) {
                    $('#status').val(f.status);
                }
                if (f.dari)   { $('#start_date').val(f.dari); }
                if (f.sampai) { $('#end_date').val(f.sampai); }
            }
        } catch (err) { /* abaikan */ }
    })();
</script>

<script type="text/javascript">
    // Badge status dibentuk DI SINI, bukan di ajx_memorial-journal.php, karena
    // nilai kolom itu dibaca ulang oleh JavaScript (modal detail memakai
    // data.status untuk memilih warnanya sendiri). Kalau server mengirim HTML,
    // perbandingan di sana langsung meleset. render() juga hanya memberi markup
    // untuk type 'display', jadi kotak Search tetap mencari teks statusnya.
    var MJB_KELAS = { 'post': 'post', 'draft': 'draft', 'cancel': 'cancel' };

    function mjbBadge(status) {
        var teks  = (status == null) ? '' : String(status);
        var kelas = MJB_KELAS[teks.toLowerCase().trim()] || 'netral';
        // Teks disisipkan sebagai text node, bukan dirangkai jadi string HTML.
        return $('<span>').addClass('mjb-badge ' + kelas)
                          .text(teks).prop('outerHTML');
    }

    function mjbBungkus(kelas) {
        return function (data, type) {
            if (type !== 'display') { return data; }
            return $('<span>').addClass(kelas)
                              .text(data == null ? '' : String(data)).prop('outerHTML');
        };
    }

    var datatable = $('#mytable').DataTable({
        ordering: false,
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,

        // Kotak Search, nomor halaman & jumlah baris ikut diingat, jadi kembali
        // dari halaman edit benar-benar mendarat di tampilan yang sama.
        // stateDuration -1 = simpan di sessionStorage (bukan localStorage),
        // seumur tab, sejalan dgn cara filter di atas disimpan.
        stateSave: true,
        stateDuration: -1,

        // RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris detail yang
        // bisa dibuka lewat tanda "+", bukan dipaksa digulir ke samping.
        // Modulnya sudah ada di datatables.min.js dan CSS-nya sudah dimuat
        // header.php (responsive.bootstrap4.min.css) - dipakai juga di
        // kontrabon_new.php, jadi perilakunya seragam antar menu.
        responsive: {
            details: { type: 'column', target: 0 }
        },

        ajax: {
            url: 'ajx_memorial-journal.php',
            type: 'POST',
            data: function(d) {
                d.nama_type  = $('#nama_type').val();
                d.status     = $('#status').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
                d.user       = '<?php echo $user; ?>';
            },
            dataSrc: 'data'
        },

        // responsivePriority: makin KECIL angkanya, makin dipertahankan saat
        // layar menyempit. Urutannya dipilih dari cara orang membaca daftar ini:
        // nomor jurnal & tombol aksi harus selalu ada, lalu status, lalu nilai,
        // lalu tanggal. Curr paling akhir - isinya IDR di 94% dokumen.
        columns: [
            { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
            { data: 'no_mj',  render: mjbBungkus('mjb-doc'), responsivePriority: 1 },
            { data: 'mj_date', responsivePriority: 5 },
            { data: 'nama_cmj', responsivePriority: 7 },
            { data: 'curr', responsivePriority: 9 },
            { data: 'debit',  render: mjbBungkus('mjb-amt'), responsivePriority: 4 },
            { data: 'credit', render: mjbBungkus('mjb-amt'), responsivePriority: 6 },
            { data: 'status', render: function (d, type) { return type === 'display' ? mjbBadge(d) : d; }, responsivePriority: 3 },
            { data: 'keterangan', responsivePriority: 8 },
            { data: 'action', orderable: false, responsivePriority: 2 },
        ],

        // Kelas "all" milik DataTables Responsive = kolom ini TIDAK PERNAH
        // dikolaps, seberapa pun sempit layarnya. responsivePriority saja tidak
        // cukup: itu hanya menentukan URUTAN dibuang, jadi pada layar yang
        // sangat sempit kolom berprioritas 1 pun masih bisa ikut hilang.
        // Tiga yang dikunci: No Journal, Status, Action - sisanya boleh masuk
        // baris detail di balik tanda "+".
        // Nomor kolom bergeser +1 karena kolom kontrol "+" disisipkan di depan:
        // 0 kontrol, 1 No Journal, 2 Date, 3 Type, 4 Curr, 5 Debit, 6 Credit,
        // 7 Status, 8 Description, 9 Action.
        columnDefs: [
            { targets: [1], className: 'text-left all' },      // No Journal
            { targets: [7], className: 'text-center all' },    // Status
            { targets: [9], className: 'text-center all' },    // Action
            { targets: [2, 3, 4, 8], className: 'text-left' },
            { targets: [5, 6], className: 'text-right' },
        ],
    });

    // Overlay mengikuti status processing DataTables - satu baris, sesuai
    // contoh di app-loading.css. Kotak "Processing" bawaan DataTables otomatis
    // disembunyikan berkas itu, jadi tidak muncul dua-duanya.
    datatable.on('processing.dt', function (e, settings, processing) {
        $('#mjLoad').toggleClass('is-loading', processing);
    });

    function dataTableReload() {
        datatable.ajax.reload(null, false);
    }

    // Tombol Search berada DI DALAM <form id="form-data">, jadi cukup
    // type="submit" - handler ini menangani klik tombol DAN tombol Enter di
    // field filter sekaligus. Handler klik terpisah sengaja tidak dipakai:
    // keduanya akan menyala bersamaan dan datanya ditarik dua kali.
    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        mjbSimpanFilter();
        dataTableReload();
    });

    // Disimpan juga saat field-nya diubah, bukan cuma saat Search ditekan:
    // user sering mengubah filter lalu langsung mengklik nomor journal tanpa
    // menekan Search dulu.
    $('#nama_type, #status, #start_date, #end_date').on('change', function () {
        mjbSimpanFilter();
    });

    document.getElementById('btnverifikasi').onclick = function() {
        location.href = "formverifikasimj.php";
    };

    <?php if ($id == '54') { ?>
    document.getElementById('btncreate_new').onclick = function() {
        location.href = "create_memorial_journal.php";
    };
    <?php } ?>
</script>

<script type="text/javascript">
    $(document).on("click", ".btn-approve-mj", function() {
        let no_mj = $(this).data("no");
        let post_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Post this journal?",
            text: no_mj + " will be posted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Post",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'post_memorialjournal.php',
                    data: { no_mj: no_mj, post_user: post_user },
                    success: function() {
                        Swal.fire('Success!', 'Memorial Journal has been posted.', 'success');
                        dataTableReload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Could not reach the server: ' + xhr.status, 'error');
                    }
                });
            }
        });
    });

    $(document).on("click", ".btn-cancel-mj", function() {
        let no_mj = $(this).data("no");
        let cancel_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Cancel this journal?",
            text: no_mj + " will be cancelled and can no longer be edited.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Cancel",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'cancel_memorialjournal.php',
                    data: { no_mj: no_mj, cancel_user: cancel_user },
                    success: function() {
                        Swal.fire('Cancelled!', 'Memorial Journal has been cancelled.', 'success');
                        dataTableReload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Could not reach the server: ' + xhr.status, 'error');
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
    document.getElementById('btnsync_fx').onclick = function() {
        Swal.fire({
            title: "Sync FX Revaluation Journal?",
            text: "This will recalculate and post (insert/update) the FX revaluation journal for every USD account up to today. Continue?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Sync",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: "Processing...",
                text: "Please wait, syncing the FX revaluation journal.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'ajx_sync_selisih_kurs.php',
                data: { user: '<?php echo $user; ?>' },
                dataType: 'json',
                success: function(res) {
                    if (!res.ok) {
                        Swal.fire('Sebagian Gagal', (res.errors || []).join('\n') || 'Terjadi kesalahan.', 'error');
                        dataTableReload();
                        return;
                    }

                    var detail = (res.per_account || []).map(function(a) {
                        return a.account + ': ' + a.count + ' jurnal';
                    }).join('<br>');

                    Swal.fire({
                        icon: 'success',
                        title: 'Sync Selesai',
                        html: 'Sampai dengan <b>' + res.end_date + '</b><br>' +
                            'Total <b>' + res.total_jurnal + '</b> jurnal diproses ' +
                            '(' + res.total_insert + ' baru, ' + res.total_update + ' update)' +
                            (detail ? '<br><br>' + detail : '')
                    });
                    dataTableReload();
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Could not reach the server: ' + xhr.status, 'error');
                }
            });
        });
    };
</script>

<script type="text/javascript">
    $("#mytable").on("click", "tbody tr", function(e) {
        if ($(e.target).closest('button, a').length) return;
        // Kontrol "+" milik Responsive dan baris detail hasil bukaannya JUGA
        // berada di dalam tbody. Tanpa dua penjaga ini, menekan "+" di ponsel
        // akan membuka modal detail alih-alih membentangkan barisnya.
        if ($(e.target).closest('td.dtr-control').length) return;
        if ($(this).hasClass('child')) return;

        const data = datatable.row(this).data();
        if (!data) return;

        $('#mymodal').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajax_mj.php',
            data: { 'no_mj': data.no_mj },
            success: function(html) {
                // DataTables lama WAJIB dibongkar dulu. Tabelnya dibuat ulang
                // tiap kali modal dibuka; tanpa destroy, pembukaan kedua kena
                // "Cannot reinitialise DataTable" dan tabelnya diam tanpa paging.
                if ($.fn.DataTable.isDataTable('#mytdmodal')) {
                    $('#mytdmodal').DataTable().destroy();
                }
                $('#details').html(html);

                $('#mytdmodal').DataTable({
                    ordering: false,
                    paging: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    searching: true,
                    info: true,
                    autoWidth: false,
                    // Lebar kolom baru bisa diukur setelah modalnya benar-benar
                    // tampil - saat ajax selesai, modal masih dalam animasi buka.
                    initComplete: function () {
                        var t = this.api();
                        $('#mymodal').off('shown.bs.modal.mjd').on('shown.bs.modal.mjd', function () {
                            t.columns.adjust();
                        });
                        setTimeout(function () { t.columns.adjust(); }, 220);
                    }
                });
            }
        });

        // Hanya nomor dokumen yang diisi dari baris daftar. Date, Type &
        // Status sekarang datang dari ajax_mj.php bersama isi modal lainnya,
        // supaya seluruh isinya berasal dari SATU sumber.
        $('#txt_bpb').text(data.no_mj);

    });
</script>

</body>

</html>
