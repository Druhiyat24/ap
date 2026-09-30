<?php include '../header.php' ?>
<?php
// ============================================================================
// edit_memorial_journal_new.php — Edit Memorial Journal gaya halaman Create.
//
// Halaman edit LAMA (edit-memorial-journal.php) TIDAK disentuh dan masih bisa
// dipakai sebagai jalan mundur.
//
// MASALAH YANG DIPERBAIKI DI SINI
//
// 1. LOAD LAMBAT. Halaman lama merender SELURUH master di SETIAP baris: satu
//    <select> COA (692 opsi) + Profit Center (2) + Cost Center (132) + Buyer
//    (589) + WS (6.517) + Currency (2) = +-7.900 <option> PER BARIS. Dokumen
//    terbesar di database 672 baris -> +-5,3 JUTA elemen <option>, ditambah 672
//    inisialisasi selectpicker dan 4 query lookup per baris. WS sendiri
//    menyumbang 4,4 juta di antaranya, padahal kolom itu cuma terisi di 51 dari
//    38.119 baris journal 2026 (0,13%).
//
//    DI SINI barisnya tetap diedit LANGSUNG DI TABEL seperti halaman lama -
//    bukan lewat modal. Bedanya: pagingnya 10 baris, jadi yang hidup di DOM
//    cuma 10 baris itu, bukan 672. Untuk dokumen terbesar sekalipun beban satu
//    layar +-14.000 <option> - kira-kira sama dengan DUA baris halaman lama.
//    WS tidak diprabuat sama sekali (dicari lewat ajx_cari_ws.php), dan daftar
//    Cost Center disaring di sisi JavaScript dari dua tabel master yang ditanam
//    sekali di halaman - jadi tidak ada lagi query per baris.
//
// 2. BARIS DIMUAT DARI tbl_list_journal, BUKAN tbl_memorial_journal.
//    No Faktur, Tgl Faktur & Supplier HANYA ada di tbl_list_journal (keputusan
//    user - lihat catatan di save_mj_upload_fix.php). Halaman lama membaca dari
//    tbl_memorial_journal yang tidak punya kolom itu, lalu menimpa
//    tbl_list_journal dari isi formnya - sehingga ketiganya TERHAPUS setiap kali
//    dokumen diedit. Untuk dokumen ber-status Post kedua tabel selalu sebaris
//    (diperiksa: 822 dari 822 dokumen 2026 cocok 1:1), jadi tbl_list_journal
//    aman dipakai sebagai sumber dan merupakan superset-nya.
//
// 3. SB I. Centangnya sekarang ADA di halaman edit dan keadaannya dibaca dari
//    status_memorial_journal. Lihat save_mj_edit.php untuk sisi penyimpanannya.
//
// 4. SIMPAN SELALU SELURUH BARIS. Sumber kebenaran halaman ini adalah array
//    BARIS di JavaScript, bukan baris <tr> yang kebetulan sedang tampil.
//    Mencari, mengganti halaman, atau mengurutkan TIDAK mengubah apa yang
//    disimpan. Halaman lama memungut nilainya dari DOM lewat
//    checkedRows.each(), jadi baris yang tidak tampil memang tidak ikut.
// ============================================================================

$no_mj = isset($_GET['no_mj']) ? base64_decode($_GET['no_mj']) : '';
$mjEsc = mysqli_real_escape_string($conn1, $no_mj);

// ---- Kepala dokumen ---------------------------------------------------------
$qHead = mysqli_query($conn1, "select a.mj_date, a.id_cmj, a.status, b.nama_cmj,
        min(a.create_by) create_by, min(a.create_date) create_date
    from tbl_memorial_journal a
    left join master_category_mj b on b.id_cmj = a.id_cmj
    where a.no_mj = '$mjEsc' group by a.no_mj limit 1");
$head = $qHead ? mysqli_fetch_assoc($qHead) : null;

// ---- Pasangan SB1 -----------------------------------------------------------
// Pasangan SB TIDAK selalu tercatat di status_memorial_journal. Diukur ke data:
//   - 49 dokumen ber-awalan FX/ (selisih kurs, dibuat SCHEDULER) disalin
//     OTOMATIS ke buku SB dgn NOMOR YANG SAMA, tanpa lewat verifikasi, dan
//     TIDAK pernah tercatat di status_memorial_journal. Salinannya hanya ada di
//     sb_list_journal - sb_memorial_journal kosong untuk seluruh 49-nya.
//   - 1.620 dokumen GM/ warisan lama juga punya baris SB ber-nomor sama tanpa
//     peta (dibuat insert_memorial_journal.php sebelum tabel peta ada).
// Kalau hanya peta yang dibaca, dokumen-dokumen itu tampil SB I tidak tercentang
// padahal salinannya ada - dan mencentangnya akan membuat salinan KEDUA.
$qSb = mysqli_query($conn1, "select no_mj_sb from status_memorial_journal
    where no_mj = '$mjEsc' order by id limit 1");
$rSb = $qSb ? mysqli_fetch_assoc($qSb) : null;
$no_mj_sb = $rSb ? (string) $rSb['no_mj_sb'] : '';
$sb_dari_peta = ($no_mj_sb !== '');

if (!$sb_dari_peta) {
    $a = mysqli_fetch_assoc(mysqli_query($conn1,
        "select count(*) n from sb_memorial_journal where no_mj = '$mjEsc'"));
    $b = mysqli_fetch_assoc(mysqli_query($conn1,
        "select count(*) n from sb_list_journal where no_journal = '$mjEsc'"));
    if ((int) $a['n'] > 0 || (int) $b['n'] > 0) { $no_mj_sb = $no_mj; }
}
// Pasangan otomatis: centangnya DIKUNCI. Salinannya dipelihara proses lain
// (scheduler selisih kurs), bukan pilihan user - melepasnya di sini berarti
// menghapus salinan yang akan dibuat ulang lagi di luar kendali halaman ini.
$sb_otomatis = ($no_mj_sb !== '' && !$sb_dari_peta);

// ---- Baris ------------------------------------------------------------------
// SATU query, tanpa lookup per baris. Nama COA & Cost Center sudah tersimpan di
// tbl_list_journal, jadi tidak perlu join ke master sama sekali.
$lines = [];
$qL = mysqli_query($conn1, "select no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, supplier, buyer, no_ws,
        curr, rate, debit, credit, keterangan, profit_center
    from tbl_list_journal where no_journal = '$mjEsc' order by id");
// "Tidak ada tanggal" di data ini muncul dalam TIGA bentuk, semuanya harus
// tampil KOSONG: NULL (11.213 baris 2026), '0000-00-00' (108.286) dan
// '1970-01-01' (22.002). Yang terakhir penanda lama, bukan tanggal sungguhan -
// halaman edit lama pun sudah menyaringnya dgn tangan.
//
// JEBAKANNYA: strtotime('1970-01-01') di zona Asia/Jakarta (UTC+7) bernilai
// -25200, yaitu NEGATIF dan karenanya TRUTHY. Penjaga bergaya "$t ? ... : ''"
// meloloskannya, dan kolom Ref Date penuh berisi 01-01-1970. Begitu juga
// '0000-00-00' yang jadi '30-11--0001'. Karena itu hasilnya dibandingkan
// sebagai TANGGAL, bukan diuji benar/salah.
$tgl = function ($v) {
    if ($v === null || $v === '') { return ''; }
    $t = strtotime((string) $v);
    if ($t === false) { return ''; }
    $ymd = date('Y-m-d', $t);
    return ($ymd <= '1970-01-01') ? '' : date('d-m-Y', $t);
};
while ($qL && $r = mysqli_fetch_assoc($qL)) {
    $lines[] = [
        'no_coa'           => (string) $r['no_coa'],
        'nama_coa'         => (string) $r['nama_coa'],
        'profit_center'    => (string) $r['profit_center'],
        'no_costcenter'    => (string) $r['no_costcenter'],
        'nama_costcenter'  => (string) $r['nama_costcenter'],
        'no_reff'          => (string) $r['reff_doc'],
        'reff_date'        => $tgl($r['reff_date']),
        'faktur_pajak'     => (string) $r['faktur_pajak'],
        'tgl_faktur_pajak' => $tgl($r['tgl_faktur_pajak']),
        'supplier'         => (string) $r['supplier'],
        'buyer'            => (string) $r['buyer'],
        'no_ws'            => (string) $r['no_ws'],
        'curr'             => (string) $r['curr'],
        'rate'             => (float) $r['rate'],
        'debit'            => (float) $r['debit'],
        'credit'           => (float) $r['credit'],
        'keterangan'       => (string) $r['keterangan'],
    ];
}

// ---- Master yang ditanam SEKALI di halaman ----------------------------------
// Ketiganya kecil (692 + 2 + 132 baris). Ditanam sekali lalu dipakai berulang
// oleh JavaScript untuk membangun dropdown baris yang sedang tampil - jadi
// TIDAK ada lagi query master per baris seperti halaman lama.
$pcList = [];
$qPc = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc from master_pc where status = 'Active' order by id_pc");
while ($qPc && $r = mysqli_fetch_assoc($qPc)) {
    $pcList[] = ['kode' => (string) $r['kode_pc'], 'id' => (string) $r['id_pc'], 'nama' => (string) $r['nama_pc']];
}

// COA + grup akuntansinya. Grup inilah yang menentukan Cost Center mana yang sah
// untuk COA tersebut - aturan yang sama dgn getCostCenter.php, tapi dihitung di
// sisi JavaScript supaya tidak perlu satu request per baris.
$coaList = [];
$qC = mysqli_query($conn1, "select no_coa, nama_coa, support_gen_adm, support_prod, prod, support_sell
    from mastercoa_v2 order by no_coa");
while ($qC && $r = mysqli_fetch_assoc($qC)) {
    $g = [];
    if ($r['support_gen_adm'] === 'Y') { $g[] = 'SUPPORTING GENERAL & ADMINISTRATION'; }
    if ($r['support_prod']    === 'Y') { $g[] = 'SUPPORTING PRODUCTION'; }
    if ($r['prod']            === 'Y') { $g[] = 'PRODUCTION'; }
    if ($r['support_sell']    === 'Y') { $g[] = 'SUPPORTING SELLING'; }
    $coaList[] = ['k' => (string) $r['no_coa'], 'n' => (string) $r['nama_coa'], 'g' => $g];
}

// b_master_cc.id_pc menyimpan NAG/NAK, yaitu master_pc.kode_pc (bukan id_pc-nya
// master_pc yang berisi PCP001/PCP002) - sudah diperiksa di data.
$ccList = [];
$qCc = mysqli_query($conn1, "select no_cc, cc_name, id_pc, group2 from b_master_cc
    where status = 'Active' order by no_cc");
while ($qCc && $r = mysqli_fetch_assoc($qCc)) {
    $ccList[] = ['k' => (string) $r['no_cc'], 'n' => (string) $r['cc_name'],
                 'pc' => (string) $r['id_pc'], 'g' => (string) $r['group2']];
}

$buyerList = [];
$qB = mysqli_query($conn1, "select distinct(Supplier) as buyer from mastersupplier
    where tipe_sup = 'C' order by Supplier asc");
while ($qB && $r = mysqli_fetch_assoc($qB)) { $buyerList[] = (string) $r['buyer']; }
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">

<style type="text/css">
    /* ========================================================================
       Kosakata .mje-. Warnanya disamakan dgn halaman List & Create Memorial
       Journal supaya ketiganya satu keluarga: latar #f4f6fb, kartu 16px,
       kepala bergradien navy #1E3A8A -> #3b82f6.
       ======================================================================== */
    body { background-color: #f4f6fb; }

    .mje-card {
        background: #fff;
        border: 1px solid #e6ebf3;
        border-radius: 16px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .06);
        overflow: hidden;
    }
    .mje-head {
        display: flex; align-items: center; flex-wrap: wrap; gap: 11px;
        padding: 11px 18px;
        background: linear-gradient(90deg, #1E3A8A 0%, #2f5bbf 55%, #3b82f6 100%);
    }
    .mje-head-icon {
        width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .3);
        color: #fff;
        display: inline-flex; align-items: center; justify-content: center; font-size: 17px;
    }
    .mje-head h1 { margin: 0; font-size: 15.5px; font-weight: 700; color: #fff; letter-spacing: .01em; }
    .mje-head .mje-crumb { display: block; margin-top: 2px; font-size: 11.5px; color: rgba(255, 255, 255, .78); }
    /* margin-left:auto - nomor dokumen didorong ke ujung kanan kepala kartu. */
    .mje-head-no { margin-left: auto; display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
    .mje-head-no b {
        font-size: 9px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase;
        color: rgba(255, 255, 255, .7);
    }
    .mje-head-no span { font-size: 13.5px; font-weight: 700; color: #fff; font-variant-numeric: tabular-nums; }

    .mje-body { padding: 16px 18px 18px; }
    .mje-panel { background: #fafbfe; border: 1px solid #eef1f7; border-radius: 12px; padding: 13px 15px 4px; }

    .mje-flabel {
        display: block; margin-bottom: 4px;
        font-size: 10.5px; font-weight: 700; letter-spacing: .06em;
        text-transform: uppercase; color: #64748b;
    }
    .mje-ro {
        display: flex; align-items: center; height: 31px; padding: 0 10px;
        background: #eef3fb; border: 1px solid #d7e3f6; border-radius: 8px;
        font-size: 13px; font-weight: 700; color: #1e3a8a;
    }
    .mje-sb1 {
        display: flex; align-items: center; gap: 8px; height: 31px;
        padding: 0 11px; background: #fff; border: 1px solid #d9e1ee; border-radius: 8px;
    }
    .mje-sb1 label { margin: 0; font-size: 12.5px; font-weight: 600; color: #334155; cursor: pointer; }
    .mje-sb1 .mje-sb1-no { font-size: 11px; color: #94a3b8; font-variant-numeric: tabular-nums; }
    /* Penanda bahwa salinan SB-nya dibuat otomatis, bukan pilihan user. */
    .mje-sb1 .mje-sb1-auto {
        background: #eef4ff; color: #1d4ed8; border-radius: 5px;
        padding: 1px 6px; font-size: 9.5px; font-weight: 700;
        letter-spacing: .05em; text-transform: uppercase;
    }

    .mje-sec-head {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px; margin: 18px 0 8px;
    }
    .mje-sec-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; }
    .mje-sec-sub { font-size: 12px; color: #94a3b8; }
    /* Penegasan bahwa cari/paging tidak memotong apa yang disimpan - kebiasaan
       dari halaman lama yang memang hanya menyimpan baris yang tampil. */
    .mje-note {
        display: inline-flex; align-items: center; gap: 6px;
        background: #eef4ff; color: #1d4ed8; border-radius: 7px;
        padding: 4px 10px; font-size: 11px; font-weight: 600;
    }

    /* ---- Grid baris: kontrolnya HIDUP di dalam sel, seperti halaman lama ---- */
    /* overflow: hidden supaya isi tabel tidak melewati sudut membulat kartu -
       tanpa ini tabelnya terlihat "bocor" keluar tepi saat digulir. */
    .mje-grid-wrap { border: 1px solid #e6ebf3; border-radius: 10px; overflow: hidden; }
    /* HANYA kotak ini yang digulir mendatar - isinya cuma <table>. Perabot
       DataTables berada di luarnya, jadi Search & paging tetap pada lebar
       halaman dan tidak ikut bergeser saat tabelnya digulir. */
    .mje-scroll { overflow-x: auto; }

    /* DataTables membungkus tabelnya dgn .row > .col-sm-12 milik Bootstrap.
       .row punya margin NEGATIF -15px dan .col punya padding 15px, sehingga
       kotak gulirnya meleset dari tepi kartu di kedua sisi. Akibatnya kolom
       Action yang dipaku "right: 0" berhenti 15px sebelum tepi dan terlihat
       melayang menindih isi tabel, bukan menempel di pinggir. Jaraknya
       dinolkan supaya kotak gulir persis selebar kartunya. */
    .mje-grid-wrap .dataTables_wrapper > .row { margin-left: 0; margin-right: 0; }
    .mje-grid-wrap .dataTables_wrapper > .row > [class*="col-"] { padding-left: 0; padding-right: 0; }
    /* Perabot dirapatkan ke tepi kotak, dan sisi kanannya ditegaskan supaya
       Search & paging benar-benar rata kanan. */
    .mje-grid-wrap .dataTables_length,
    .mje-grid-wrap .dataTables_filter { padding: 9px 12px 0; }
    .mje-grid-wrap .dataTables_info,
    .mje-grid-wrap .dataTables_paginate { padding: 0 12px 9px; }
    .mje-grid-wrap .dataTables_filter { text-align: right; }
    .mje-grid-wrap .dataTables_paginate .pagination { justify-content: flex-end; margin-bottom: 0; }
    .mje-tbl { border-collapse: separate; border-spacing: 0; margin: 0; }
    .mje-tbl thead th {
        background: #eef3fb; color: #1e3a8a;
        font-size: 8.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
        padding: 7px 8px; border: 0; border-bottom: 2px solid #c9d8f0;
        white-space: nowrap; vertical-align: middle; text-align: left;
    }
    .mje-tbl tbody td {
        padding: 4px 5px; border: 0; border-bottom: 1px solid #f2f5fa;
        vertical-align: middle; font-size: 11px; color: #475569; white-space: nowrap;
    }
    .mje-tbl tbody tr:hover td { background: #f9fbff; }
    .mje-tbl td.mje-no {
        text-align: right; color: #cbd5e1; font-weight: 700; font-size: 10px;
        font-variant-numeric: tabular-nums;
    }

    /* Kontrol di dalam sel dibuat ringkas - satu layar memuat 10 baris x 15 kolom. */
    .mje-tbl .form-control {
        height: 28px; padding: 2px 7px; font-size: 11px; border-radius: 6px;
        border: 1px solid #dfe5ef; background: #fff;
    }
    .mje-tbl .form-control:focus { border-color: #93b4f5; box-shadow: 0 0 0 2px rgba(59, 130, 246, .12); }
    .mje-tbl .form-control[readonly] { background: #f4f6fb; color: #94a3b8; }
    .mje-tbl input.num { text-align: right; font-variant-numeric: tabular-nums; }

    /* Lebar dipatok di SEL (<td>), BUKAN di kontrolnya. select2 menyembunyikan
       <select> asli dan menggambar wadahnya sendiri, jadi min-width yang
       dipasang di <select> tidak berpengaruh sama sekali - kotak COA & Cost
       Center akan menciut jadi beberapa piksel. */
    .mje-tbl td > .select2-container { width: 100% !important; }
    .mje-tbl th.c-coa,   .mje-tbl td.c-coa   { min-width: 240px; }
    .mje-tbl th.c-cc,    .mje-tbl td.c-cc    { min-width: 200px; }
    .mje-tbl th.c-pc,    .mje-tbl td.c-pc    { min-width: 235px; }
    .mje-tbl th.c-txt,   .mje-tbl td.c-txt   { min-width: 130px; }
    .mje-tbl th.c-date,  .mje-tbl td.c-date  { min-width: 110px; }
    .mje-tbl th.c-buyer, .mje-tbl td.c-buyer { min-width: 170px; }
    .mje-tbl th.c-ws,    .mje-tbl td.c-ws    { min-width: 130px; }
    .mje-tbl th.c-curr,  .mje-tbl td.c-curr  { min-width: 80px; }
    .mje-tbl th.c-rate,  .mje-tbl td.c-rate  { min-width: 84px; }
    .mje-tbl th.c-num,   .mje-tbl td.c-num   { min-width: 120px; }
    .mje-tbl th.c-ket,   .mje-tbl td.c-ket   { min-width: 260px; }
    /* Kolom Action DIPAKU di tepi kanan. Grid ini 16 kolom dan memang harus
       digulir mendatar; tanpa dipaku, tombol hapus ikut hilang ke kanan dan
       user harus menggulir bolak-balik cuma untuk membuang satu baris.
       border-collapse: separate (sudah dipakai .mje-tbl) adalah syaratnya -
       position:sticky tidak bekerja pada tabel yang collapse. Latarnya WAJIB
       pekat, kalau tidak isi kolom di bawahnya akan menembus. */
    .mje-tbl th.c-act, .mje-tbl td.c-act {
        width: 46px; min-width: 46px; max-width: 46px;
        text-align: center;
        position: sticky; right: 0;
        /* Latar WAJIB pekat: sel ini melayang di atas kolom di bawahnya saat
           tabel digulir. Garis kiri + bayangan memberi batas yang jelas, jadi
           terbaca sebagai tepi yang menempel, bukan kotak yang nyasar. */
        background: #fff;
        border-left: 1px solid #e6ebf3;
        box-shadow: -8px 0 10px -8px rgba(15, 23, 42, .25);
    }
    /* Kepala tabel harus di lapis PALING atas: ia melayang ke kanan sekaligus
       berada di baris paling atas, jadi bertemu dua arah sekaligus. */
    .mje-tbl thead th.c-act { background: #eef3fb; border-left-color: #c9d8f0; z-index: 5; }
    .mje-tbl tbody td.c-act { z-index: 4; }
    .mje-tbl tbody tr:hover td.c-act { background: #f9fbff; }
    /* select2 di dalam sel: tingginya disamakan dgn .form-control di atas. */
    .mje-tbl .select2-container .select2-selection--single { height: 28px; border: 1px solid #dfe5ef; border-radius: 6px; }
    .mje-tbl .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 26px; font-size: 11px; padding-left: 7px; }
    .mje-tbl .select2-container--default .select2-selection--single .select2-selection__arrow { height: 26px; }

    .mje-del {
        border: 0; background: #fdeceb; color: #9f2d28;
        border-radius: 6px; padding: 4px 8px; font-size: 11px; cursor: pointer;
    }
    .mje-del:hover { filter: brightness(.95); }
    .mje-rowtools { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }

    /* ---- Balance Summary: SALINAN gaya tab Manual Journal Entry (.mji-tot di
         mj_input.php) - kartu bergaris tepi kiri berwarna, nilai mata uang asli
         KECIL di atas dan nilai IDR BESAR di bawahnya. Dibuat sama supaya
         membaca angka di halaman edit tidak terasa pindah tempat. */
    .mje-tot {
        border: 1px solid #e6ebf3; border-left: 4px solid #cbd5e1; border-radius: 10px;
        background: #fff; box-shadow: 0 1px 3px rgba(15, 23, 42, .05);
        padding: 12px 14px 13px; height: 100%;
    }
    .mje-tot.tone-nag { border-left-color: #5b7ba8; }
    .mje-tot.tone-nak { border-left-color: #4f8a6b; }
    .mje-tot.tone-all { border-left-color: #4a5578; background: #f8fafc; }
    .mje-tot-name {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: #475569; padding-bottom: 6px; border-bottom: 1px solid #eef2f7; margin-bottom: 2px;
    }
    .mje-tot-code {
        background: #eef2f7; color: #475569; border-radius: 4px; padding: 0 5px;
        margin-left: 6px; font-size: 9.5px; letter-spacing: .4px;
    }
    /* Debit & Credit BERSEBELAHAN. Tiap kolom menampung DUA baris, diisi
       menurun lewat grid-auto-flow:column - urutan anaknya WAJIB
       deb-sub, deb, cre-sub, cre. */
    .mje-tot-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        grid-auto-flow: column;
        column-gap: 14px; row-gap: 3px; margin-top: 7px;
    }
    .mje-tot-col { display: flex; flex-direction: column; min-width: 0; }
    .mje-tot-col.is-cre { border-left: 1px solid #eef2f7; padding-left: 14px; }
    .mje-tot-lbl {
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .6px; margin-bottom: 1px;
    }
    .mje-tot-col.is-deb .mje-tot-lbl { color: #1d4ed8; }
    .mje-tot-col.is-cre .mje-tot-lbl { color: #b45309; }
    .mje-tot-col.is-sub .mje-tot-lbl { color: #94a3b8; font-size: 9.5px; letter-spacing: .4px; }
    .mje-tot-val {
        font-size: 17px; font-weight: 700; color: #0f172a;
        font-variant-numeric: tabular-nums; line-height: 1.25;
    }
    .mje-tot-val.is-sub { font-size: 12px; font-weight: 600; color: #94a3b8; }
    @media (max-width: 767.98px) {
        .mje-tot-split { grid-template-columns: 1fr; grid-auto-flow: row; }
        .mje-tot-col.is-cre { border-left: 0; padding-left: 0; margin-top: 8px; }
    }
    .mje-bal { display: inline-flex; align-items: center; gap: 6px; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .mje-bal.ok { background: #e6f7ec; color: #15724a; }
    .mje-bal.no { background: #fdeceb; color: #9f2d28; }

    .mje-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 18px; }

    @media (max-width: 575.98px) {
        .mje-head { padding: 10px 14px; gap: 9px; }
        .mje-head h1 { font-size: 14px; }
        .mje-head-no { margin-left: 0; align-items: flex-start; }
        .mje-body { padding: 14px 12px 16px; }
    }
</style>

<div class="container-fluid mt-3 p-3">
    <div class="mje-card">
        <div class="mje-head">
            <span class="mje-head-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
            <div>
                <h1>Edit Memorial Journal</h1>
                <span class="mje-crumb">Accounting &rsaquo; Memorial Journal &rsaquo; Edit</span>
            </div>
            <div class="mje-head-no">
                <b>No Journal</b>
                <span><?php echo htmlspecialchars($no_mj); ?></span>
            </div>
        </div>

        <div class="mje-body">

            <?php if (!$head) { ?>
                <div class="alert alert-danger mb-0">
                    Journal <b><?php echo htmlspecialchars($no_mj); ?></b> tidak ditemukan.
                    <a href="memorial-journal.php">Kembali ke daftar</a>.
                </div>
            <?php } elseif (strcasecmp(trim($head['status']), 'Cancel') === 0) { ?>
                <!-- Dokumen Cancel: baris tbl_list_journal-nya sudah dipindah ke
                     tbl_list_journal_cancel, jadi halaman ini akan tampil KOSONG
                     kalau diteruskan. Tombol Edit memang tidak muncul untuk status
                     ini, tapi URL-nya bisa dibuka langsung. -->
                <div class="alert alert-warning mb-0">
                    Journal <b><?php echo htmlspecialchars($no_mj); ?></b> berstatus
                    <b>Cancel</b> dan tidak bisa diedit.
                    <a href="memorial-journal.php">Kembali ke daftar</a>.
                </div>
            <?php } elseif (count($lines) === 0) { ?>
                <div class="alert alert-warning mb-0">
                    Journal <b><?php echo htmlspecialchars($no_mj); ?></b> tidak punya baris di
                    <code>tbl_list_journal</code>, jadi tidak ada yang bisa diedit di sini.
                    <a href="memorial-journal.php">Kembali ke daftar</a>.
                </div>
            <?php } else { ?>

            <!-- ============ Panel kontrol ============ -->
            <div class="mje-panel">
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label class="mje-flabel">No Journal</label>
                        <div class="mje-ro"><?php echo htmlspecialchars($no_mj); ?></div>
                        <input type="hidden" id="no_doc" value="<?php echo htmlspecialchars($no_mj); ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="mje-flabel" for="mj_date">Date</label>
                        <input type="text" id="mj_date" class="form-control tanggal" autocomplete="off"
                               value="<?php echo date('d-m-Y', strtotime($head['mj_date'])); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="mje-flabel" for="id_cmj">Type</label>
                        <select class="form-control select2" id="id_cmj">
                            <?php
                            $qT = mysqli_query($conn1, "select id_cmj, nama_cmj from master_category_mj order by id_cmj");
                            while ($qT && $t = mysqli_fetch_assoc($qT)) {
                                $sel = ($t['id_cmj'] === $head['id_cmj']) ? ' selected' : '';
                                echo '<option value="' . htmlspecialchars($t['id_cmj']) . '"' . $sel . '>'
                                   . htmlspecialchars($t['nama_cmj']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="mje-flabel" for="to_sb1">SB I</label>
                        <div class="mje-sb1">
                            <input type="checkbox" id="to_sb1"
                                <?php echo $no_mj_sb !== '' ? 'checked' : ''; ?>
                                <?php echo $sb_otomatis ? 'disabled' : ''; ?>>
                            <label for="to_sb1">Include</label>
                            <span class="mje-sb1-no" id="txt_sb1"><?php echo htmlspecialchars($no_mj_sb); ?></span>
                            <?php if ($sb_otomatis) { ?>
                                <span class="mje-sb1-auto" title="Salinan SB dokumen ini dibuat otomatis dgn nomor yang sama dan tidak melalui verifikasi">auto</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ Baris journal ============ -->
            <div class="mje-sec-head">
                <h6 class="mje-sec-title">Journal Lines</h6>
                <span class="mje-note">
                    <i class="fa fa-info-circle"></i>
                    <span id="mjeCount"></span> &middot; search &amp; paging only change what you see
                </span>
            </div>

            <!-- Pembungkus ini SENGAJA tidak menggulir. Gulir mendatarnya
                 dipasang belakangan oleh JavaScript, HANYA pada kotak yang
                 membungkus <table> di dalam struktur buatan DataTables - lihat
                 initTabel(). Kalau .table-responsive dipasang di sini, seluruh
                 perabot DataTables (Show entries, Search, info, paging) ikut
                 masuk ke wilayah gulir selebar tabel: Search-nya melayang jauh
                 di kanan dan paging-nya terdampar di tengah. -->
            <div class="mje-grid-wrap">
                <table id="mjeTbl" class="mje-tbl" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:34px">#</th>
                            <th>COA</th>
                            <th>Profit Center</th>
                            <th>Cost Center</th>
                            <th>Reference</th>
                            <th>Ref Date</th>
                            <th>No Faktur</th>
                            <th>Faktur Date</th>
                            <th>Supplier</th>
                            <th>Buyer</th>
                            <th>WS</th>
                            <th>Curr</th>
                            <th>Rate</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Description</th>
                            <th class="c-act"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mje-rowtools">
                <button type="button" class="app-btn app-btn-success app-btn-sm" id="btnAddRow">
                    <i class="fa fa-plus"></i> Add Row
                </button>
            </div>

            <!-- ============ Balance Summary ============ -->
            <div class="mje-sec-head">
                <h6 class="mje-sec-title">Balance Summary</h6>
                <span id="mjeBal"></span>
            </div>
            <div class="row" id="mjeTotals"></div>

            <div class="mje-actions">
                <!-- .app-btn-primary: biru rata yang SAMA dgn tombol Save di
                     halaman Create (app-skin-form.css), bukan gradien sendiri. -->
                <button type="button" class="app-btn app-btn-primary" id="btnSave">
                    <i class="fa fa-check"></i> Save Changes
                </button>
                <button type="button" class="app-btn app-btn-danger" onclick="location.href='memorial-journal.php'">
                    <i class="fa fa-angle-double-left"></i> Back
                </button>
            </div>

            <?php } ?>
        </div>
    </div>
</div>

<!-- header.php TIDAK memuat satu pun pustaka JS - tiap halaman memuat sendiri. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
/* ============================================================================
   SUMBER KEBENARAN halaman ini adalah array BARIS, BUKAN baris <tr> yang sedang
   tampil. DataTables diberi array ini apa adanya, sehingga row().data()
   mengembalikan OBJEK YANG SAMA (bukan salinannya) - mengubah objek itu dari
   handler berarti langsung mengubah BARIS. Konsekuensinya: mencari, berpindah
   halaman, atau mengurutkan tidak pernah mengubah apa yang ikut disimpan.
   ============================================================================ */
var BARIS     = <?php echo json_encode($lines, JSON_UNESCAPED_UNICODE); ?>;
var PC_LIST   = <?php echo json_encode($pcList, JSON_UNESCAPED_UNICODE); ?>;
var COA_LIST  = <?php echo json_encode($coaList, JSON_UNESCAPED_UNICODE); ?>;
var CC_LIST   = <?php echo json_encode($ccList, JSON_UNESCAPED_UNICODE); ?>;
var BUYER     = <?php echo json_encode($buyerList, JSON_UNESCAPED_UNICODE); ?>;
var tabel     = null;

function angka(n) {
    return (parseFloat(n) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function teks(s) {
    return $('<div>').text(s === null || s === undefined ? '' : s).html();
}

/* ---------------------------------------------------------------------------
   Potongan <option> disusun SEKALI lalu dipakai ulang tiap baris yang tampil.
   Dgn paging 10, satu layar memuat 10 x (692 COA + 589 Buyer) - berat yang
   sama dgn DUA baris di halaman edit lama.
   --------------------------------------------------------------------------- */
var OPT_COA = '<option value=""></option>';
COA_LIST.forEach(function (c) { OPT_COA += '<option value="' + teks(c.k) + '">' + teks(c.k + ' - ' + c.n) + '</option>'; });

var OPT_PC = '';
// Ditampilkan PENUH ('PCP001 - NIRWANA ALABARE GARMENT'), bukan cuma kode
// 'NAG' - sama dgn dropdown Profit Center di halaman Create. Nilai yang
// dikirim tetap kode_pc, karena itulah yang tersimpan di kolom profit_center.
PC_LIST.forEach(function (p) { OPT_PC += '<option value="' + teks(p.kode) + '">' + teks(p.id + ' - ' + p.nama) + '</option>'; });

var OPT_BUYER = '<option value="-">-</option>';
BUYER.forEach(function (b) { OPT_BUYER += '<option value="' + teks(b) + '">' + teks(b) + '</option>'; });

// Grup akuntansi per COA -> dipakai menyaring Cost Center, aturan yang sama
// dgn getCostCenter.php tapi tanpa satu request per baris.
var COA_GRP = {};
COA_LIST.forEach(function (c) { COA_GRP[c.k] = c.g || []; });

function optCc(coa, pc, terpilih) {
    var grup = COA_GRP[coa] || [];
    var h = '<option value="-">-</option>';
    var ketemu = false;
    CC_LIST.forEach(function (c) {
        if (c.pc === pc && grup.indexOf(c.g) !== -1) {
            h += '<option value="' + teks(c.k) + '">' + teks(c.k + ' - ' + c.n) + '</option>';
            if (c.k === terpilih) { ketemu = true; }
        }
    });
    // Nilai tersimpan tetap ditampilkan walau tidak lolos saringan - diam-diam
    // mengosongkan Cost Center yang sudah dipilih akuntansi itu lebih buruk.
    //
    // Namanya dicari dari CC_LIST TANPA saringan, jadi tampil utuh
    // "DEP06SUB001 - FINANCE, ACCOUNTING & TAX" seperti opsi lain. Sebelumnya
    // cuma kodenya yang tampil dgn imbuhan "(tersimpan)" - istilah yang tidak
    // berarti apa-apa bagi pembacanya, di halaman yang seluruhnya berbahasa
    // Inggris pula.
    if (terpilih && terpilih !== '-' && !ketemu) {
        var cLain = CC_LIST.filter(function (x) { return x.k === terpilih; })[0];
        h += '<option value="' + teks(terpilih) + '">'
           + teks(cLain ? (cLain.k + ' - ' + cLain.n) : terpilih) + '</option>';
    }
    return h;
}

/* ---------------------------------------------------------------------------
   Kolom - tiap sel berisi kontrol form sungguhan, bukan teks.
   render() mengembalikan MARKUP hanya untuk 'display'; untuk 'filter'/'sort'
   nilai mentahnya yang dikembalikan, supaya kotak Search mencari isi datanya
   dan bukan potongan HTML.
   --------------------------------------------------------------------------- */
function sel(kelas, isiOption, nilai) {
    // Opsi terpilih ditandai lewat ATRIBUT selected, bukan $(h).val(...).
    // .val() menyetel PROPERTI option.selected, sedangkan .prop("outerHTML")
    // hanya menyalin atribut - jadi nilai tersimpannya hilang dan tiap baris
    // akan tampil memilih opsi pertama.
    nilai = (nilai === null || nilai === undefined) ? '' : String(nilai);
    var cari = 'value="' + teks(nilai) + '"';
    var isi, pos = isiOption.indexOf(cari);
    if (pos !== -1) {
        // Pengganti berupa fungsi, bukan string: nilai yang mengandung "$"
        // akan diartikan sebagai penanda khusus oleh String.replace biasa.
        isi = isiOption.replace(cari, function (m) { return m + ' selected'; });
    } else {
        // Nilai tersimpan yang sudah tidak ada di master tetap ditampilkan apa
        // adanya. Kalau dibuang, kotaknya diam-diam menunjuk opsi pertama dan
        // user tidak pernah tahu nilai aslinya apa.
        isi = (nilai === '' ? '' : '<option value="' + teks(nilai) + '" selected>' + teks(nilai) + '</option>') + isiOption;
    }
    return '<select class="form-control ' + kelas + '">' + isi + '</select>';
}
function inp(kelas, nilai, extra) {
    return '<input type="text" class="form-control ' + kelas + '" value="' + teks(nilai) + '"'
         + (extra || '') + ' autocomplete="off">';
}

function initTabel() {
    tabel = $('#mjeTbl').DataTable({
        data: BARIS,
        // deferRender: hanya baris halaman aktif yang dibentuk jadi DOM. Inilah
        // yang membuat dokumen 672 baris tetap ringan walau kontrolnya hidup.
        deferRender: true,
        ordering: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        autoWidth: false,
        columns: [
            { data: null, className: 'mje-no', orderable: false,
              render: function (d, t, r, meta) { return meta.row + 1; } },

            { data: 'no_coa', className: 'c-coa', render: function (v, t) {
                return t === 'display' ? sel('js-coa', OPT_COA, v) : v; } },

            { data: 'profit_center', className: 'c-pc', render: function (v, t) {
                return t === 'display' ? sel('js-pc', OPT_PC, v) : v; } },

            { data: null, className: 'c-cc', render: function (d, t) {
                return t === 'display'
                    ? sel('js-cc', optCc(d.no_coa, d.profit_center, d.no_costcenter), d.no_costcenter || '-')
                    : d.no_costcenter; } },

            { data: 'no_reff',          className: 'c-txt',  render: function (v, t) { return t === 'display' ? inp('js-f', v, ' data-k="no_reff"') : v; } },
            { data: 'reff_date',        className: 'c-date', render: function (v, t) { return t === 'display' ? inp('js-f js-date', v, ' data-k="reff_date"') : v; } },
            { data: 'faktur_pajak',     className: 'c-txt',  render: function (v, t) { return t === 'display' ? inp('js-f', v, ' data-k="faktur_pajak"') : v; } },
            { data: 'tgl_faktur_pajak', className: 'c-date', render: function (v, t) { return t === 'display' ? inp('js-f js-date', v, ' data-k="tgl_faktur_pajak"') : v; } },
            { data: 'supplier',         className: 'c-txt',  render: function (v, t) { return t === 'display' ? inp('js-f', v, ' data-k="supplier"') : v; } },

            { data: 'buyer', className: 'c-buyer', render: function (v, t) {
                return t === 'display' ? sel('js-buyer', OPT_BUYER, v || '-') : v; } },

            // WS tidak diprabuat: 6.517 opsi untuk kolom yang terisi di 0,13%
            // baris. Hanya nilai tersimpan yang ditanam; sisanya lewat pencarian.
            { data: 'no_ws', className: 'c-ws', render: function (v, t) {
                if (t !== 'display') { return v; }
                var w = v || '-';
                return '<select class="form-control js-ws"><option value="' + teks(w) + '" selected>' + teks(w) + '</option></select>'; } },

            { data: 'curr', className: 'c-curr', render: function (v, t) {
                return t === 'display'
                    ? sel('js-curr', '<option value="IDR">IDR</option><option value="USD">USD</option>', v || 'IDR')
                    : v; } },

            // Rate TERKUNCI untuk baris IDR (kursnya memang selalu 1) dan
            // TERBUKA untuk mata uang asing - kurs 1 untuk USD hampir pasti
            // salah dan akan membuat nilai IDR-nya meleset ribuan kali lipat.
            { data: 'rate', className: 'c-rate', render: function (v, t, d) {
                if (t !== 'display') { return v; }
                var idr = String(d.curr || 'IDR').toUpperCase() === 'IDR';
                var r = parseFloat(v) || (idr ? 1 : 0);
                return '<input type="number" step="any" class="form-control num js-rate" value="'
                     + (r || '') + '"' + (idr ? ' readonly' : ' placeholder="fill in rate"')
                     + ' autocomplete="off">'; } },

            { data: 'debit',  className: 'c-num', render: function (v, t) {
                return t === 'display' ? '<input type="number" step="any" class="form-control num js-amt" data-k="debit" value="' + (parseFloat(v) || 0) + '" autocomplete="off">' : v; } },
            { data: 'credit', className: 'c-num', render: function (v, t) {
                return t === 'display' ? '<input type="number" step="any" class="form-control num js-amt" data-k="credit" value="' + (parseFloat(v) || 0) + '" autocomplete="off">' : v; } },

            { data: 'keterangan', className: 'c-ket', render: function (v, t) { return t === 'display' ? inp('js-f', v, ' data-k="keterangan"') : v; } },

            { data: null, className: 'c-act', orderable: false, render: function () {
                return '<button type="button" class="mje-del js-del" title="Delete line"><i class="fa fa-trash"></i></button>'; } }
        ],

        // Kontrol di baris yang akan dibuang HARUS dilepas dulu: select2
        // menggantung wadahnya sendiri di samping <select> asli, dan sisa wadah
        // itu menumpuk tiap ganti halaman kalau tidak dihancurkan.
        preDrawCallback: function () {
            $('#mjeTbl tbody select').each(function () {
                if ($(this).data('select2')) { $(this).select2('destroy'); }
            });
        },
        drawCallback: function () { pasangKontrol(); }
    });

    // DataTables membungkus <table> ke dalam strukturnya sendiri saat init.
    // Gulir mendatarnya dipasang ke pembungkus LANGSUNG milik tabel itu, bukan
    // ke seluruh wrapper - dgn begitu Search, info & paging berada DI LUAR
    // wilayah gulir dan tetap diam di kanan saat tabelnya digeser.
    // Dicari lewat .parent() (bukan nama kelas bawaan DataTables) supaya tidak
    // bergantung pada struktur versi tertentu.
    $('#mjeTbl').parent().addClass('mje-scroll');
}

/* Dipanggil tiap kali DataTables menggambar halaman - hanya untuk 10 baris
   yang benar-benar tampil. */
function pasangKontrol() {
    var $b = $('#mjeTbl tbody');
    $b.find('.js-coa').select2({ width: '100%' });
    $b.find('.js-cc').select2({ width: '100%' });
    $b.find('.js-buyer').select2({ width: '100%' });
    // Profit Center & Currency ikut select2 supaya SEMUA dropdown di halaman
    // ini seragam. Kotak pencariannya dimatikan: isinya cuma 2 pilihan, jadi
    // kolom cari justru menambah satu ketukan tanpa gunanya.
    $b.find('.js-pc').select2({ width: '100%', minimumResultsForSearch: Infinity });
    $b.find('.js-curr').select2({ width: '100%', minimumResultsForSearch: Infinity });
    $b.find('.js-ws').select2({
        width: '100%', minimumInputLength: 2, placeholder: '-',
        ajax: {
            url: 'memorial_journal/ajx_cari_ws.php', dataType: 'json', delay: 250,
            data: function (p) { return { q: p.term }; },
            processResults: function (d) { return { results: d }; }
        }
    });
    $b.find('.js-date').datepicker({ format: 'dd-mm-yyyy', autoclose: true, todayHighlight: true });
}

/* ---------------------------------------------------------------------------
   Semua perubahan ditulis LANGSUNG ke objek barisnya. Didelegasikan ke <tbody>
   karena dgn deferRender kontrolnya dibentuk ulang tiap ganti halaman.
   --------------------------------------------------------------------------- */
function dataBaris(el) { return tabel.row($(el).closest('tr')).data(); }

$(document).on('change', '#mjeTbl tbody .js-coa', function () {
    var d = dataBaris(this);
    d.no_coa = this.value;
    var c = COA_LIST.filter(function (x) { return x.k === d.no_coa; })[0];
    d.nama_coa = c ? c.n : '';
    // Daftar Cost Center yang sah ikut berubah begitu COA-nya berubah.
    segarkanCc($(this).closest('tr'), d);
});

$(document).on('change', '#mjeTbl tbody .js-pc', function () {
    var d = dataBaris(this);
    d.profit_center = this.value;
    segarkanCc($(this).closest('tr'), d);
    hitungTotal();                       // Balance Summary dipecah per Profit Center
});

function segarkanCc($tr, d) {
    var $cc = $tr.find('.js-cc');
    if ($cc.data('select2')) { $cc.select2('destroy'); }
    $cc.html(optCc(d.no_coa, d.profit_center, d.no_costcenter))
       .val(d.no_costcenter || '-').select2({ width: '100%' });
    // Nilai lama bisa saja tidak lolos filter COA/PC yang baru.
    if ($cc.val() === null) { $cc.val('-').trigger('change.select2'); d.no_costcenter = '-'; d.nama_costcenter = ''; }
}

$(document).on('change', '#mjeTbl tbody .js-cc', function () {
    var d = dataBaris(this);
    d.no_costcenter = this.value;
    var c = CC_LIST.filter(function (x) { return x.k === d.no_costcenter; })[0];
    d.nama_costcenter = c ? c.n : '';
});

$(document).on('change', '#mjeTbl tbody .js-buyer', function () { dataBaris(this).buyer = this.value; });
$(document).on('change', '#mjeTbl tbody .js-ws',    function () { dataBaris(this).no_ws = this.value; });
$(document).on('change', '#mjeTbl tbody .js-curr', function () {
    var d = dataBaris(this), $rate = $(this).closest('tr').find('.js-rate');
    d.curr = this.value;

    if (String(d.curr).toUpperCase() === 'IDR') {
        // Baris IDR tidak pernah dikonversi - kursnya dikunci di 1.
        d.rate = 1;
        $rate.val(1).prop('readonly', true).removeAttr('placeholder');
        hitungTotal();
        return;
    }

    // Mata uang asing: kotak kursnya DIBUKA. Kalau kursnya masih 1 (bawaan
    // baris IDR), dicarikan dulu kurs PAJAK tanggal journal ini - kalau tidak
    // ketemu, dikosongkan supaya user mengisinya sendiri, BUKAN dibiarkan 1.
    $rate.prop('readonly', false);
    if (parseFloat(d.rate) > 1) { hitungTotal(); return; }

    d.rate = 0;
    $rate.val('').attr('placeholder', 'loading...');
    $.post('get_rate.php', { valuta: d.curr, doc_date: $('#mj_date').val() }, function (res) {
        var r = (res && res.status === 'ok') ? parseFloat(res.rate) : 0;
        if (r > 0) { d.rate = r; $rate.val(r).removeAttr('placeholder'); }
        else { $rate.attr('placeholder', 'fill in rate'); }
        hitungTotal();
    }, 'json').fail(function () { $rate.attr('placeholder', 'fill in rate'); });
    hitungTotal();
});

$(document).on('input', '#mjeTbl tbody .js-rate', function () {
    dataBaris(this).rate = parseFloat(this.value) || 0;
    hitungTotal();
});

// Teks & tanggal: 'input' supaya ketikan langsung tersimpan, 'change' supaya
// pilihan datepicker (yang tidak memicu 'input') ikut tertangkap.
$(document).on('input change', '#mjeTbl tbody .js-f', function () {
    dataBaris(this)[$(this).data('k')] = this.value;
});

$(document).on('input', '#mjeTbl tbody .js-amt', function () {
    var d = dataBaris(this), k = $(this).data('k'), v = parseFloat(this.value) || 0;
    d[k] = v;
    // Satu baris hanya berisi debit ATAU credit - sisi lawannya dinolkan.
    if (v > 0) {
        var lawan = (k === 'debit') ? 'credit' : 'debit';
        d[lawan] = 0;
        $(this).closest('tr').find('.js-amt[data-k="' + lawan + '"]').val(0);
    }
    hitungTotal();
});

$(document).on('click', '#mjeTbl tbody .js-del', function () {
    var $tr = $(this).closest('tr');
    var d = tabel.row($tr).data();
    Swal.fire({
        title: 'Delete this line?', text: (d.no_coa || '') + ' - ' + (d.keterangan || ''),
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel'
    }).then(function (r) {
        if (!r.isConfirmed) { return; }
        var i = BARIS.indexOf(d);
        if (i >= 0) { BARIS.splice(i, 1); }
        gambarTabel();
    });
});

$('#btnAddRow').on('click', function () {
    BARIS.push({
        no_coa: '', nama_coa: '', profit_center: (PC_LIST[0] ? PC_LIST[0].kode : ''),
        no_costcenter: '-', nama_costcenter: '', no_reff: '', reff_date: '',
        faktur_pajak: '', tgl_faktur_pajak: '', supplier: '', buyer: '-', no_ws: '-',
        curr: 'IDR', rate: 1, debit: 0, credit: 0, keterangan: ''
    });
    gambarTabel();
    tabel.page('last').draw(false);      // langsung dibawa ke baris barunya
});

function gambarTabel() {
    tabel.clear();
    tabel.rows.add(BARIS);
    tabel.draw(false);
    $('#mjeCount').text(BARIS.length + (BARIS.length === 1 ? ' line total' : ' lines total'));
    hitungTotal();
}

/* ---------------------------------------------------------------------------
   Balance Summary - dihitung dari SELURUH array, bukan dari halaman yang tampil
   --------------------------------------------------------------------------- */
function kartuTotal(nama, kode, t, tone) {
    // Urutan anak .mje-tot-split WAJIB deb-sub, deb, cre-sub, cre:
    // grid-auto-flow:column mengisinya menurun per kolom.
    function kolom(sisi, lblSub, lbl, vSub, v) {
        return '<div class="mje-tot-col ' + sisi + ' is-sub">'
             +   '<span class="mje-tot-lbl">' + lblSub + '</span>'
             +   '<div class="mje-tot-val is-sub">' + angka(vSub) + '</div></div>'
             + '<div class="mje-tot-col ' + sisi + '">'
             +   '<span class="mje-tot-lbl">' + lbl + '</span>'
             +   '<div class="mje-tot-val">' + angka(v) + '</div></div>';
    }
    return '<div class="col-md-4 mb-2"><div class="mje-tot ' + tone + '">'
         +   '<div class="mje-tot-name">' + teks(nama) + '<span class="mje-tot-code">' + teks(kode) + '</span></div>'
         +   '<div class="mje-tot-split">'
         +     kolom('is-deb', 'Debit', 'Debit IDR', t.d, t.di)
         +     kolom('is-cre', 'Credit', 'Credit IDR', t.c, t.ci)
         +   '</div></div></div>';
}

function hitungTotal() {
    // Nilai IDR dihitung dari kurs BARIS ITU SENDIRI (debit x rate), bukan satu
    // kurs global - sama dgn yang nanti dilakukan server lewat mj_resolve_rate().
    var per = {}, tot = { d: 0, c: 0, di: 0, ci: 0 };
    BARIS.forEach(function (b) {
        var k = b.profit_center || '-';
        if (!per[k]) { per[k] = { d: 0, c: 0, di: 0, ci: 0 }; }
        var d = parseFloat(b.debit) || 0, c = parseFloat(b.credit) || 0;
        var r = parseFloat(b.rate) || 1;
        per[k].d  += d;      per[k].c  += c;
        per[k].di += d * r;  per[k].ci += c * r;
        tot.d  += d;         tot.c  += c;
        tot.di += d * r;     tot.ci += c * r;
    });

    // Kartu selalu muncul untuk SEMUA profit center aktif, bukan cuma yang
    // kebetulan terpakai - supaya letaknya tidak berpindah saat baris terakhir
    // sebuah PC dihapus.
    var kosong = { d: 0, c: 0, di: 0, ci: 0 };
    var html = '', kode = [];
    PC_LIST.forEach(function (p) {
        kode.push(p.kode);
        var tone = p.kode === 'NAG' ? 'tone-nag' : (p.kode === 'NAK' ? 'tone-nak' : '');
        html += kartuTotal(p.nama, p.kode, per[p.kode] || kosong, tone);
    });
    // Profit center yang ada di baris tapi tidak ada di master aktif tetap
    // ditampilkan - kalau disembunyikan, angkanya hilang tanpa jejak.
    Object.keys(per).sort().forEach(function (k) {
        if (kode.indexOf(k) === -1) { html += kartuTotal(k, k, per[k], ''); kode.push(k); }
    });
    html += kartuTotal('Grand Total', kode.join(' + '), tot, 'tone-all');
    $('#mjeTotals').html(html);

    // Lencana memakai NILAI IDR - ukuran yang sama dgn yang dipakai saat Save.
    var selisih = Math.abs(tot.di - tot.ci);
    $('#mjeBal').html(selisih < 0.005
        ? '<span class="mje-bal ok"><i class="fa fa-check-circle"></i> Balanced &middot; IDR ' + angka(tot.di) + '</span>'
        : '<span class="mje-bal no"><i class="fa fa-exclamation-triangle"></i> Out of balance &middot; IDR D ' + angka(tot.di) + ' / C ' + angka(tot.ci) + '</span>');
}

/* ---------------------------------------------------------------------------
   Simpan - SATU request untuk SELURUH dokumen
   --------------------------------------------------------------------------- */
$('#btnSave').on('click', function () {
    if (BARIS.length === 0) { Swal.fire('No lines', 'This journal has no lines left.', 'warning'); return; }

    // Divalidasi atas SELURUH array, bukan baris yang tampil - baris bermasalah
    // bisa saja ada di halaman lain atau tersembunyi oleh kotak Search.
    var totDI = 0, totCI = 0, salah = null;
    BARIS.forEach(function (b, i) {
        if (salah) { return; }
        if (!b.no_coa) { salah = 'Line ' + (i + 1) + ': COA is empty.'; return; }
        if (!b.profit_center || b.profit_center === '-') { salah = 'Line ' + (i + 1) + ': Profit Center is empty.'; return; }
        var d = parseFloat(b.debit) || 0, c = parseFloat(b.credit) || 0;
        if (d <= 0 && c <= 0) { salah = 'Line ' + (i + 1) + ': both Debit and Credit are empty.'; return; }

        // Kurs 1 untuk mata uang asing akan membuat nilai IDR-nya meleset
        // ribuan kali lipat - ditolak di sini, bukan dibiarkan lewat.
        var cu = String(b.curr || 'IDR').toUpperCase(), r = parseFloat(b.rate) || 0;
        if (cu !== 'IDR' && !(r > 1)) {
            salah = 'Line ' + (i + 1) + ': ' + cu + ' rate is still ' + (r || 0) + '. Fill in the exchange rate.'; return;
        }
        if (cu === 'IDR') { r = 1; }
        totDI += d * r; totCI += c * r;
    });
    if (salah) {
        Swal.fire({ icon: 'warning', title: 'Check the lines', text: salah });
        return;
    }
    // Dibandingkan pada NILAI IDR, bukan nominal aslinya: journal bermata uang
    // campuran tidak akan pernah balance pada nominal asli.
    if (Math.abs(totDI - totCI) >= 0.005) {
        Swal.fire('Not balanced', 'Debit IDR ' + angka(totDI) + ' vs Credit IDR ' + angka(totCI) + '.', 'error');
        return;
    }

    var $btn = $(this).prop('disabled', true);
    Swal.fire({
        title: 'Saving...', text: 'Rewriting all ' + BARIS.length + ' lines.',
        allowOutsideClick: false, allowEscapeKey: false,
        didOpen: function () { Swal.showLoading(); }
    });

    $.ajax({
        type: 'POST',
        url: 'memorial_journal/save_mj_edit.php',
        dataType: 'json',
        data: {
            no_mj: $('#no_doc').val(),
            mj_date: $('#mj_date').val(),
            id_cmj: $('#id_cmj').val(),
            fil_sb1: $('#to_sb1').is(':checked') ? '1' : '',
            // Dikirim sebagai SATU string JSON. Sebagai array form biasa, dokumen
            // 672 baris x 17 field = 11.424 input - di atas max_input_vars bawaan
            // PHP (1000), dan kelebihannya dibuang DIAM-DIAM tanpa error.
            baris: JSON.stringify(BARIS)
        },
        success: function (res) {
            if (res && res.status === 'success') {
                Swal.fire({
                    icon: 'success', title: 'Saved',
                    html: '<b>' + teks(res.no_journal) + '</b> rewritten with ' + res.baris + ' lines.'
                        + (res.no_mj_sb ? '<br>SB I: <b>' + teks(res.no_mj_sb) + '</b>' : '')
                }).then(function () { location.href = 'memorial-journal.php'; });
            } else {
                $btn.prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: (res && res.kode === 'CLOSING') ? 'Period closed' : 'Failed to save',
                    html: teks((res && res.message) || 'Unknown error')
                        + ((res && res.detail) ? '<br><small>' + teks(res.detail) + '</small>' : '')
                });
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false);
            Swal.fire('Failed to save', 'Server responded ' + xhr.status + '.', 'error');
        }
    });
});

/* --------------------------------------------------------------------------- */
$(function () {
    $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true, todayHighlight: true });
    $('#id_cmj').select2({ width: '100%' });
    initTabel();
    gambarTabel();
});
</script>

</body>

</html>
