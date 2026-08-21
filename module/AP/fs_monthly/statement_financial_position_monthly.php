<style>
#sfp .card-body {
  background: #f9fafb;
}
#sfp table {
  color: #333;
}
#sfp th, #sfp td {
  padding: 6px 10px;
}

.sfpm-export-buttons {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 10px;
}
.sfpm-btn-export-excel {
  font-size: 12.5px;
  font-weight: 600;
  color: #fff;
  background: #1d6f42;
  border: none;
  border-radius: 6px;
  padding: 7px 16px;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
  transition: background 0.15s ease, transform 0.1s ease;
}
.sfpm-btn-export-excel:hover {
  background: #218c53;
}
.sfpm-btn-export-excel:active {
  transform: translateY(1px);
}

/* Bungkus luar (border/radius/shadow) - overflow:hidden di sini yang
   "memotong" 2 kotak di dalamnya (header + body) supaya sudut membulatnya
   tetap rapi walau salah satu anaknya sendiri punya scrollbar. */
.laporan-outer {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  overflow: hidden;
}

/* Header (judul bulan, NAG/NAK/Total) SENGAJA dipisah jadi <table> sendiri
   yang berdiri di LUAR area yang discroll vertikal (bukan di-freeze lewat
   position:sticky di dalam tabel yang sama). Sudah pernah dicoba versi
   sticky per-baris/thead - hasilnya kadang bikin celah/gap kosong yang
   nyelip pas discroll cepat (bug rendering browser utk multi-baris sticky
   header yang dikombinasi sama sticky kolom kiri sekaligus), jadi
   ditinggalkan. Header di sini benar-benar TIDAK PERNAH ikut scroll YANG
   DIPICU USER (karena secara fisik ada di luar box yang overflow:auto),
   lebar tiap kolomnya dikunci sama persis dengan tabel body lewat
   <colgroup> yang identik (lihat sfpmColgroup()), dan posisi geser
   horizontalnya disamakan ke tabel body lewat 1 baris JS (scrollLeft).
   overflow-x DIKUNCI ke "scroll" (BUKAN "hidden") - sudah dibuktikan lewat
   browser sungguhan: men-set scrollLeft lewat JS pada elemen overflow:hidden
   TIDAK RELIABLE di Chromium (nilainya ke-set tapi kontennya tidak
   ke-repaint), itu penyebab asli header "ketinggalan" tidak sejajar
   dari body waktu discroll. Elemen overflow:scroll pasti mendukung
   scrollLeft terprogram dengan benar; scrollbar bawaannya sendiri
   disembunyikan lewat CSS (::-webkit-scrollbar dkk di bawah) karena
   scrollbar horizontal yang dipakai user tetap cuma satu, milik
   .laporan-container. overflow-y tetap "hidden" (vertikal memang tidak
   pernah discroll sama sekali di header). */
.sfp-header-clip {
  overflow-x: scroll;
  overflow-y: hidden;
  background: #fafafa;
  padding: 10px 25px 0;
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.sfp-header-clip::-webkit-scrollbar {
  display: none;
}

/* Body (baris data) - ini SATU-SATUNYA area yang benar-benar discroll
   (vertikal & horizontal), scrollbar horizontalnya yang dipakai user untuk
   geser (header ikut geser secara terprogram lewat JS, bukan discroll
   langsung oleh user). */
.laporan-container {
  max-height: 65vh;
  overflow-x: auto;
  overflow-y: auto;
  /* Reserve scrollbar space secara konsisten - tanpa ini, lebar area
     konten container ini menyusut sebesar lebar scrollbar HANYA waktu
     scrollbar vertikal muncul (baris cukup banyak sampai > 65vh), sedangkan
     .sfp-header-clip di atasnya (overflow-y:hidden, tidak pernah punya
     scrollbar) tidak ikut menyusut. Lebar gap ini dibaca ulang lewat JS
     (lihat script di bawah) dan ditambahkan sebagai padding-right header,
     supaya keduanya selalu punya lebar area konten yang identik persis. */
  scrollbar-gutter: stable;
  background: #fafafa;
  padding: 0 25px 15px;
}

.laporan-table {
  font-size: 12.5px;
  margin: 0 auto;
  /* table-layout:fixed WAJIB dipakai bareng <colgroup> - tanpa ini, lebar
     kolom dihitung otomatis dari isi (auto layout) dan tabel header vs body
     (2 elemen <table> terpisah) bisa saja menghasilkan lebar yang sedikit
     beda walau <colgroup>-nya sama persis, karena auto-layout juga
     mempertimbangkan isi tiap sel. Dengan fixed, lebar kolom MURNI dari
     <colgroup>, jadi dijamin identik di kedua tabel. */
  table-layout: fixed;
  border-collapse: separate;
  border-spacing: 0;
  color: #2c3e50;
}
.sfpm-tbl td, .sfpm-tbl th {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
/* ===== Judul laporan (nama PT, judul laporan, periode, disclaimer) =====
   Satu blok terpusat di ATAS tabel, bukan lagi ditempel per-kolom
   (kiri = ID, kanan = EN) seperti sebelumnya - versi lama gampang
   kelihatan "nyelip"/tidak sejajar karena mepet ke lebar kolom pertama &
   terakhir yang sempit (juga dipakai buat kolom deskripsi), dan nama PT +
   periode-nya sama persis di ID & EN jadi percuma ditulis dobel. Terjemahan
   Inggris cuma ditulis untuk baris yang teksnya memang beda (judul laporan
   & disclaimer mata uang), gaya italic. Kolom deskripsi EN yang dulu ada di
   paling kanan tabel sudah dihapus sepenuhnya (lihat .periode-desc). */
.sfp-title-block {
  text-align: left;
  padding: 18px 25px 14px;
  background: #fafafa;
  border-bottom: 1px solid #e5e9f2;
}

.sfp-title-company {
  font-weight: 700;
  font-size: 18px;
  color: #1e3a8a;
  letter-spacing: .3px;
}

.sfp-title-report {
  font-weight: 700;
  font-size: 16px;
  color: #2c3e50;
  margin-top: 4px;
}

.sfp-title-report-en {
  font-style: italic;
  font-size: 14.5px;
  color: #6b7280;
}

.sfp-title-period {
  font-weight: 600;
  font-size: 14.5px;
  color: #2c3e50;
  margin-top: 6px;
}

.sfp-title-desc {
  font-size: 13px;
  color: #777;
  margin-top: 3px;
}

.sfp-title-desc-en {
  font-style: italic;
  font-size: 12.5px;
  color: #999;
}

.periode {
  text-align: center;
  color: #1e3a8a;
  border-bottom: 2px solid #1e3a8a;
  font-weight: 600;
  padding-bottom: 6px;
}

.judul-periode {
  text-align: center;
  color: #1e3a8a;
  font-weight: 700;
  font-size: 13px;
  letter-spacing: .2px;
  padding-bottom: 4px;
}

.periode-desc {
  text-align: left;
  vertical-align: middle;
  color: #1e3a8a;
  font-weight: 700;
  font-size: 12.5px;
}

/* ===== Sections ===== */
.section-left {
  font-weight: bold;
  color: #1e3a8a;
  font-size: 13px;
  letter-spacing: .2px;
  text-align: left;
}

.subsection-left {
  font-weight: bold;
  text-align: left;
}

/* ===== Data Rows ===== */
.item-left {
  text-align: left;
  padding: 3px 0;
}

.item-right {
  text-align: right;
}

/* ===== Totals ===== */
.total-line {
  line-height: 24px;
  font-weight: bold;
}
/* border-top di sel (td/th), BUKAN di <tr> - dengan border-collapse:separate
   (dipakai tabel ini), border yang ditaruh langsung di elemen <tr> tidak
   dirender browser sama sekali; harus di elemen sel-nya. */
.total-line td,
.total-line th {
  border-top: 1px solid #94a3c4;
}

.total-left {
  text-align: left;
}

.total-right {
  text-align: right;
}

/* ===== Grand Total ===== */
.grand-total {
  background: #e8edfa;
  font-weight: bold;
  line-height: 26px;
}
/* border-top di sel, sama alasannya seperti .total-line di atas. */
.grand-total td,
.grand-total th {
  border-top: 2px solid #1e3a8a;
}

.grand-left {
  text-align: left;
}

.grand-right {
  text-align: right;
}

/* ===== Spacers ===== */
.spacer {
  height: 15px;
}

.spacer-mid {
  height: 10px;
}

.spacer-small {
  height: 5px;
}

/* Freeze kolom paling kiri (deskripsi) - dipakai class eksplisit
   (.sfpm-freeze-left) di KEDUA tabel (header & body, class .sfpm-tbl
   bareng), BUKAN :first-child. Sel "Description" di tabel header pakai
   rowspan="2" (menutupi baris judul bulan & baris NAG/NAK/Total sekaligus),
   jadi baris ke-2 (NAG/NAK/Total) secara DOM child pertamanya adalah kolom
   NAG bulan pertama, BUKAN kolom deskripsi - kalau dipilih pakai
   :first-child, kolom NAG itu ikut ke-freeze ke kiri (posisi & warna
   latarnya salah, numpuk sama sel Description yang sudah menutup kolom itu
   secara visual lewat rowspan). Class eksplisit menghindari jebakan
   DOM-vs-visual ini sama sekali. */
.sfpm-tbl .sfpm-freeze-left {
  position: sticky;
  left: 0;
  z-index: 2;
  background-color: #fafafa;
  box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.2);
}

/* Batas bawah header (pemisah visual dari body yang discroll di bawahnya). */
#sfp-header-table tr:last-child th {
  box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.3);
}

/* Pemisah bulan - tint warna latar selang-seling per bulan. */
.sfpm-tbl .sfpm-month-a {
  background-color: #f8fafd;
}
.sfpm-tbl .sfpm-month-b {
  background-color: #eef2fb;
}
.sfpm-tbl tr.grand-total .sfpm-month-a {
  background-color: #e7ecf9;
}
.sfpm-tbl tr.grand-total .sfpm-month-b {
  background-color: #dbe3f6;
}

/* Grup kolom "YTD" di ujung kanan (duplikat nilai bulan terakhir filter,
   lihat sfpmRenderRowValues()) - dikasih warna amber yang jelas beda dari
   tint biru bulan biasa supaya kelihatan sebagai kolom RINGKASAN, bukan
   cuma "bulan lain". Aturan ini sengaja ditulis SETELAH .sfpm-month-a/-b
   di atas supaya menang di cascade (spesifisitas classnya sama-sama 1
   class, yang terakhir didefinisikan yang berlaku). */
.sfpm-tbl .sfpm-ytd {
  background-color: #fdf6e6;
  border-left: 2px solid #c9962f;
}
.sfpm-tbl tr.grand-total .sfpm-ytd {
  background-color: #f8e8c4;
}
.sfpm-tbl .judul-periode.sfpm-ytd,
.sfpm-tbl .periode.sfpm-ytd {
  color: #8a5a10;
}

/* Highlight baris data (bukan spacer/section/total) saat di-hover - :has()
   dipakai supaya tetap 1 rule (tidak perlu nandain tiap <tr> pakai class
   dari PHP), aman di-skip browser lama karena cuma soal hover, bukan
   alignment. Selector pakai ID (bukan .sfpm-tbl) supaya spesifisitasnya
   menang dibanding rule background sticky .sfpm-freeze-left di atas. */
#sfp-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

/* Garis pemisah antar baris data supaya 18+ kolom angka lebih gampang
   dibaca sejajar per baris - :has(td) otomatis kena baris item & total-line
   (yang memang selalu punya <td> buat kolom nilai), TIDAK kena
   section/subsection/grand-total/spacer (semua selnya <th> tanpa <td>). */
#sfp-monthly-table tr:has(td) > td,
#sfp-monthly-table tr.total-line > th {
  border-bottom: 1px solid #eef1f6;
}

/* Garis vertikal tipis di kolom pertama tiap bulan (lihat sfpmColClass())
   supaya batas antar grup bulan (Jan|Feb|Mar|...) jelas kelihatan dari atas
   (judul bulan) sampai bawah tabel - tint warna bulan (.sfpm-month-a/-b)
   sendirian kurang tegas kalau kolomnya sudah banyak. */
.sfpm-tbl .sfpm-month-start {
  border-left: 1px solid #ccd6ee;
}

/* Scrollbar modern & tipis buat area body yang discroll - scrollbar bawaan
   OS/browser (kotak, ada tombol panah) kelihatan usang dan tidak nyambung
   sama desain kartu yang membulat/lembut di sekitarnya. */
.laporan-container {
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
.laporan-container::-webkit-scrollbar {
  height: 10px;
  width: 10px;
}
.laporan-container::-webkit-scrollbar-track {
  background: #f1f4fa;
}
.laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0;
  border-radius: 8px;
  border: 2px solid #f1f4fa;
}
.laporan-container::-webkit-scrollbar-thumb:hover {
  background-color: #93a4d1;
}
</style>

<!-- ====== TAB CONTENT: SFP MONTHLY ====== -->
<?php
require_once __DIR__ . '/fs_monthly_functions.php';

$profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center'] : 'ALL';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');

// Beginning balance ikut bulan "From" yang dipilih user, tidak dikunci ke
// Januari (biar konsisten dengan trial_balance_monthly.php).
$bulan_awal = date('m', strtotime($start_date));
$tahun_awal = date('Y', strtotime($start_date));
$bulan_akhir = date('m', strtotime($end_date));
$tahun_akhir = date('Y', strtotime($end_date));
$kata_filter = date('M', strtotime($start_date)) . '_' . $tahun_awal;

$periods = fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);
$lastPeriod = end($periods);

// Kolom nilai per bulan berurutan normal (NAG, NAK, Total) seperti tab YTD -
// 'ALL' dipakai sebagai key lookup ke total_all, tapi ditampilkan dengan
// label "Total".
$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];
$pcColLabels = ['ALL' => 'Total'];

// Semua kolom (NAG, NAK, Total) dalam 1 bulan yang sama dikasih class ganjil
// atau genap yang sama - dipakai buat tint warna latar selang-seling per
// bulan (lihat CSS .sfpm-month-a/.sfpm-month-b). $idx = urutan kolom nilai ke
// berapa (0-based, dihitung dari kiri lintas semua bulan). Kolom pertama tiap
// bulan (idx % groupSize === 0) juga dikasih garis vertikal tipis
// (.sfpm-month-start) - tint warna doang ternyata kurang kelihatan buat
// nandain batas antar bulan waktu kolomnya banyak (>15 kolom angka),
// garisnya bikin mata lebih gampang "mengunci" ke grup bulan yang sama pas
// baca ke bawah.
// Pakai $GLOBALS['sfpmYtdStartIdx'] (bukan parameter tambahan) supaya semua
// call site yang sudah ada (sfpmRenderRowValues, sfpmBlankCells, loop
// header) tidak perlu diubah - begitu $idx masuk rentang grup YTD di ujung
// kanan (lihat perhitungan $valueColCount di bawah), class .sfpm-ytd
// ditambahkan buat kasih highlight warna beda dari tint bulan biasa.
function sfpmColClass($idx, $groupSize) {
    $monthIdx = intdiv($idx, $groupSize);
    $cls = $monthIdx % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b';
    if ($idx % $groupSize === 0) {
        $cls .= ' sfpm-month-start';
    }
    if (isset($GLOBALS['sfpmYtdStartIdx']) && $idx >= $GLOBALS['sfpmYtdStartIdx']) {
        $cls .= ' sfpm-ytd';
    }
    return $cls;
}

// Lebar kolom DIKUNCI lewat <colgroup> yang sama persis di tabel header
// maupun body (lihat catatan table-layout:fixed di CSS) - dipanggil 2x
// dengan parameter identik, bukan cuma sekali/di-share, karena masing-masing
// <table> butuh <colgroup>-nya sendiri (tidak bisa dipakai bareng lintas
// elemen table berbeda).
function sfpmColgroup($valueColCount, $descW = 260, $valW = 155) {
    $html = '<colgroup><col style="width:' . $descW . 'px">';
    for ($i = 0; $i < $valueColCount; $i++) {
        $html .= '<col style="width:' . $valW . 'px">';
    }
    $html .= '</colgroup>';
    return $html;
}

// Lebar total tabel (dipakai sebagai style="width:...px" eksplisit di TAG
// <table> header maupun body, bukan cuma dibiarkan "auto"). Terbukti lewat
// pengujian nyata (headless browser): dengan width:auto, table-layout:fixed
// TIDAK menjamin dua elemen <table> terpisah dengan <colgroup> identik
// akan dirender dengan lebar akhir yang sama persis - lebar minimum
// konten sel (angka panjang tidak boleh wrap di tabel body, teks pendek di
// tabel header) ikut memengaruhi kalkulasi auto-width, dan hasilnya bisa
// menyimpang jauh dari sekadar jumlah <colgroup> (pernah terukur: tabel
// header 1711px, tabel body 3595px, padahal <colgroup>-nya identik). Kalau
// tabel header jadi TIDAK actually overflow (lebih sempit dari
// wrapper-nya), scrollLeft yang di-set lewat JS jadi no-op (browser
// otomatis clamp balik ke 0 karena tidak ada yang bisa discroll) - itu
// akar masalah header yang "ketinggalan" pas tabel body discroll. width
// eksplisit di sini menghapus ambiguitas itu - lebar akhir MURNI dari
// angka PHP ini, tidak diserahkan ke algoritma auto-width browser sama
// sekali.
function sfpmTableWidth($valueColCount, $descW = 260, $valW = 155) {
    return $descW + $valueColCount * $valW;
}

// Render 1 baris nilai, urutan periode lalu pcCol di dalamnya (NAG, NAK,
// Total per bulan) dari lookup [period_label][pcCol] => float.
function sfpmRenderRowValues($valuesByPeriod, $periods, $pcCols, $tag, $cellClass) {
    $html = '';
    $idx = 0;
    foreach ($periods as $p) {
        foreach ($pcCols as $pcCol) {
            $v = $valuesByPeriod[$p['label']][$pcCol] ?? 0;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, count($pcCols)) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
            $idx++;
        }
    }
    // Grup kolom YTD di ujung kanan - bukan periode baru, cuma nilai bulan
    // TERAKHIR dari filter yang diulang lagi (lihat catatan $valueColCount
    // di bawah, kenapa nilainya memang identik dengan YTD).
    $lastPeriod = end($periods);
    if ($lastPeriod) {
        foreach ($pcCols as $pcCol) {
            $v = $valuesByPeriod[$lastPeriod['label']][$pcCol] ?? 0;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, count($pcCols)) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
            $idx++;
        }
    }
    return $html;
}

// Section > sub_kategori (kategori fs_kategori_laporan) - persis daftar +
// label EN yang dipakai fs_ytd/statement_financial_position.php. Semua
// kategori SFP pakai formula debit-normal (saldo + debit - credit), termasuk
// Liabilitas/Ekuitas - bukan salah ketik, memang itu formula yang sudah
// berjalan di versi YTD-nya.
$sections = [
    'ASET' => [
        'label_en' => 'ASSETS',
        'grand_label' => 'JUMLAH ASET',
        'grand_label_en' => 'TOTAL ASSETS',
        'items' => [
            ['ASET LANCAR', 'CURRENT ASSETS', 'Jumlah Aset Lancar', 'Total Current Assets'],
            ['ASET TIDAK LANCAR', 'NON-CURRENT ASSETS', 'Jumlah Aset Tidak Lancar', 'Total Non Current Asset'],
        ],
    ],
    'LIABILITAS DAN EKUITAS' => [
        'label_en' => 'LIABILITIES AND EQUITY',
        'grand_label' => 'JUMLAH LIABILITAS DAN EKUITAS',
        'grand_label_en' => 'TOTAL LIABILITIES AND EQUITY',
        'items' => [
            ['LIABILITAS JANGKA PENDEK', 'CURRENT LIABILITIES', 'Jumlah Liabilitas jangka Pendek', 'Total Current Liabilities'],
            ['LIABILITAS JANGKA PANJANG', 'NONCURRENT LIABILITIES', 'Jumlah Liabilitas jangka Panjang', 'Total Noncurrent Liabilities'],
            ['EKUITAS', 'EQUITY', 'Jumlah Ekuitas', 'Total Equity'],
        ],
    ],
];

// Ambil data tiap bulan sekali per kategori (bukan per baris) - hemat query.
$monthCategoryData = []; // [kategori][period_label] = array of rows
foreach ($periods as $p) {
    foreach ($sections as $sectionInfo) {
        foreach ($sectionInfo['items'] as $catInfo) {
            $kategori = $catInfo[0];
            if (!isset($monthCategoryData[$kategori])) {
                $monthCategoryData[$kategori] = [];
            }
            $monthCategoryData[$kategori][$p['label']] = fsGetCategoryTotals(
                $conn2, [$kategori], $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun'], $kata_filter, false
            );
        }
    }
}

$pcColCount = count($pcCols);
// +$pcColCount di akhir buat 1 grup kolom tambahan "YTD" di ujung kanan -
// bukan periode baru, cuma duplikat nilai bulan TERAKHIR dari rentang
// filter (lihat sfpmRenderRowValues()), karena tiap kolom bulanan di
// laporan ini MEMANG SUDAH kumulatif YTD dari Januari, jadi nilai bulan
// terakhir = YTD sampai akhir filter. $ytdStartIdx dipakai sfpmColClass()
// (lewat $GLOBALS, lihat catatan di situ) buat nandain kolom mana saja
// yang masuk grup YTD ini.
$valueColCount = count($periods) * $pcColCount + $pcColCount;
$GLOBALS['sfpmYtdStartIdx'] = count($periods) * $pcColCount;

// Baris kosong (blank filler) sejumlah $valueColCount - tetap dikasih class
// sfpm-month-a/b (lihat sfpmColClass()) biar tint per bulan tetap nyambung
// waktu lewatin baris spacer/section yang kosong.
function sfpmBlankCells($valueColCount, $pcColCount, $tag = 'th') {
    $html = '';
    for ($idx = 0; $idx < $valueColCount; $idx++) {
        $html .= '<' . $tag . ' class="' . trim(sfpmColClass($idx, $pcColCount)) . '"></' . $tag . '>';
    }
    return $html;
}

// Baris spacer/kosong di antara section - jumlah <th> kosongnya ikut jumlah
// kolom nilai (dulu di YTD cuma 1/3 sesuai profit_center, sekarang sejumlah
// periode x pcCols).
function sfpmSpacerRow($valueColCount, $pcColCount, $rowClass = 'spacer') {
    echo '<tr class="' . $rowClass . '"><th class="sfpm-freeze-left"></th>' . sfpmBlankCells($valueColCount, $pcColCount) . '</tr>';
}
?>

<div class="sfpm-export-buttons">
  <button type="button" id="btnExcel" class="sfpm-btn-export-excel">📊 Export Excel</button>
</div>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
      <div class="sfp-title-report">LAPORAN POSISI KEUANGAN</div>
      <div class="sfp-title-report-en">Statements of Financial Position</div>
      <div class="sfp-title-period"><?= htmlspecialchars($periods[0]['label']); ?> - <?= htmlspecialchars($lastPeriod['label']); ?></div>
      <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
      <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
    </div>
    <div class="sfp-header-clip">
      <table id="sfp-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:<?= sfpmTableWidth($valueColCount); ?>px">
        <?= sfpmColgroup($valueColCount); ?>
        <tr>
          <th rowspan="2" class="periode-desc sfpm-freeze-left">Description</th>
          <?php $monthIdx = 0; foreach ($periods as $p) : ?>
            <th colspan="<?= $pcColCount; ?>" class="judul-periode<?= $monthIdx++ % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b'; ?> sfpm-month-start"><?= htmlspecialchars($p['label']); ?></th>
          <?php endforeach; ?>
          <th colspan="<?= $pcColCount; ?>" class="judul-periode sfpm-month-start sfpm-ytd">YTD</th>
        </tr>
        <tr>
          <?php $idx = 0; foreach ($periods as $p) : ?>
            <?php foreach ($pcCols as $pcCol) : ?>
              <th class="periode<?= sfpmColClass($idx++, $pcColCount); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <?php foreach ($pcCols as $pcCol) : ?>
              <th class="periode<?= sfpmColClass($idx++, $pcColCount); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
          <?php endforeach; ?>
        </tr>
      </table>
    </div>
    <div class="laporan-container">
      <table id="sfp-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:<?= sfpmTableWidth($valueColCount); ?>px">
        <?= sfpmColgroup($valueColCount); ?>
        <?php
        foreach ($sections as $sectionName => $sectionInfo) {
            sfpmSpacerRow($valueColCount, $pcColCount);

            echo '<tr data-en="' . htmlspecialchars($sectionInfo['label_en']) . '"><th class="section-left sfpm-freeze-left">' . htmlspecialchars($sectionName) . '</th>'
               . sfpmBlankCells($valueColCount, $pcColCount)
               . '</tr>';

            sfpmSpacerRow($valueColCount, $pcColCount, 'spacer-small');

            $sectionTotalPerMonth = [];
            $itemCount = count($sectionInfo['items']);
            $itemIndex = 0;
            foreach ($sectionInfo['items'] as $catInfo) {
                [$kategori, $kategoriEn, $totalLabel, $totalLabelEn] = $catInfo;
                $itemIndex++;

                echo '<tr data-en="' . htmlspecialchars($kategoriEn) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($kategori) . '</th>'
                   . sfpmBlankCells($valueColCount, $pcColCount)
                   . '</tr>';

                // Kumpulkan sub_kategori yang muncul di bulan manapun dalam
                // rentang, biar barisnya konsisten walau nilainya 0 di sebagian bulan.
                $subKategoriList = []; // sub_kategori => sub_kategori_eng
                foreach ($periods as $p) {
                    foreach ($monthCategoryData[$kategori][$p['label']] as $row) {
                        if (!isset($subKategoriList[$row['sub_kategori']])) {
                            $subKategoriList[$row['sub_kategori']] = $row['sub_kategori_eng'] ?? '';
                        }
                    }
                }

                $catTotalPerMonth = [];
                foreach ($subKategoriList as $subKategori => $subKategoriEn) {
                    $itemValues = []; // [period_label][pcCol] = float
                    foreach ($periods as $p) {
                        $row = null;
                        foreach ($monthCategoryData[$kategori][$p['label']] as $r) {
                            if ($r['sub_kategori'] === $subKategori) {
                                $row = $r;
                                break;
                            }
                        }
                        $row = $row ?? ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
                        foreach ($pcCols as $pcCol) {
                            $v = (float) ($row['total_' . strtolower($pcCol)] ?? 0);
                            $itemValues[$p['label']][$pcCol] = $v;
                            $catTotalPerMonth[$p['label']][$pcCol] = ($catTotalPerMonth[$p['label']][$pcCol] ?? 0) + $v;
                        }
                    }
                    echo '<tr data-en="' . htmlspecialchars($subKategoriEn) . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($subKategori) . '</td>'
                       . sfpmRenderRowValues($itemValues, $periods, $pcCols, 'td', 'item-right')
                       . '</tr>';
                }

                foreach ($periods as $p) {
                    foreach ($pcCols as $pcCol) {
                        $sectionTotalPerMonth[$p['label']][$pcCol] = ($sectionTotalPerMonth[$p['label']][$pcCol] ?? 0) + ($catTotalPerMonth[$p['label']][$pcCol] ?? 0);
                    }
                }

                echo '<tr class="total-line" data-en="' . htmlspecialchars($totalLabelEn) . '"><th class="total-left sfpm-freeze-left">' . htmlspecialchars($totalLabel) . '</th>'
                   . sfpmRenderRowValues($catTotalPerMonth, $periods, $pcCols, 'td', 'total-right')
                   . '</tr>';

                sfpmSpacerRow($valueColCount, $pcColCount, $itemIndex === $itemCount ? 'spacer-mid' : 'spacer-small');
            }

            echo '<tr class="grand-total" data-en="' . htmlspecialchars($sectionInfo['grand_label_en']) . '"><th class="grand-left sfpm-freeze-left">' . htmlspecialchars($sectionInfo['grand_label']) . '</th>'
               . sfpmRenderRowValues($sectionTotalPerMonth, $periods, $pcCols, 'th', 'grand-right')
               . '</tr>';
        }
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  // Header (#sfp-header-table) berdiri sendiri di luar area scroll - satu-
  // satunya yang perlu disamakan cuma posisi geser HORIZONTAL-nya, disalin
  // dari body setiap kali body discroll (bukan sebaliknya - header sendiri
  // tidak bisa discroll manual, overflow-x-nya "hidden").
  (function () {
    var bodyWrapper = document.querySelector('#sfp .laporan-container');
    var headerClip = document.querySelector('#sfp .sfp-header-clip');
    if (!bodyWrapper || !headerClip) return;

    // Body punya scrollbar vertikal (kalau baris > 65vh), header tidak
    // pernah punya - beda lebar ini yang bikin kolom sticky paling kanan
    // "geser" antara header vs body. Lebar scrollbar-nya diukur dari DOM
    // asli (offsetWidth - clientWidth), bukan angka tebakan px, karena beda
    // per browser/OS, lalu ditambahkan sebagai padding-right header supaya
    // lebar area konten keduanya selalu identik.
    function syncHeaderGutter() {
      var scrollbarWidth = bodyWrapper.offsetWidth - bodyWrapper.clientWidth;
      headerClip.style.paddingRight = (25 + scrollbarWidth) + 'px';
    }

    bodyWrapper.addEventListener('scroll', function () {
      headerClip.scrollLeft = bodyWrapper.scrollLeft;
    });

    window.addEventListener('resize', syncHeaderGutter);
    // Tab SFP defaultnya display:none saat halaman pertama dimuat (lihat
    // openTab() di financial_statement_monthly.php) - elemen yang
    // display:none lebar-nya selalu 0, jadi pengukuran di atas baru akurat
    // setelah tab-nya benar-benar ditampilkan. openTab() akan memanggil
    // fungsi ini lagi lewat window.updateSfpHeaderOffset begitu tab SFP
    // diklik.
    window.updateSfpHeaderOffset = syncHeaderGutter;
    syncHeaderGutter();
  })();
</script>
