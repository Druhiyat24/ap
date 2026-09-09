<?php
// ============================================================================
// Ekspor Excel report SUB LEDGER — PPN MASUKAN.
//
// Query-nya TIDAK ditulis ulang di sini: dipanggil dari ppn_masukan_query.php,
// berkas yang sama dengan yang dipakai tampilan layar. Jadi isi file dijamin
// sama persis dengan yang dilihat user (tinggal beda format).
//
// Format .xls (BIFF, Writer\Xls) — BUKAN .xlsx: PHP di server ini tidak punya
// ekstensi zip & xml, jadi penulis Xlsx pasti gagal. Xls murni PHP dan aman.
//
// Susunan kolom persis seperti di layar (29 kolom, header 2 tingkat):
//   A SI No | B SI Date | C Supplier | D No Faktur Pajak | E Profit Center
//   F-H Beginning | I-K Addition | L-P Deduction | Q-U Reclassification
//   V-Z Adjustment | AA-AC Ending
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

include '../../conn/conn.php';
require_once __DIR__ . '/ppn_masukan_query.php';
date_default_timezone_set('Asia/Jakarta');

$PPN_MIN_DATE = '2026-01-01';
$nama_supp  = $_REQUEST['nama_supp'] ?? 'ALL';
$start_date = !empty($_REQUEST['start_date']) ? date('Y-m-d', strtotime($_REQUEST['start_date'])) : date('Y-m-d');
$end_date   = !empty($_REQUEST['end_date'])   ? date('Y-m-d', strtotime($_REQUEST['end_date']))   : date('Y-m-d');
if ($start_date < $PPN_MIN_DATE) { $start_date = $PPN_MIN_DATE; }
if ($end_date   < $PPN_MIN_DATE) { $end_date   = $PPN_MIN_DATE; }

$res = mysqli_query($conn2, ppn_report_sql($conn2, $nama_supp, $start_date, $end_date, $PPN_MIN_DATE));
if ($res === false) { header('Content-Type: text/plain'); echo 'Query failed: ' . mysqli_error($conn2); exit; }

$ss = new Spreadsheet();
$sh = $ss->getActiveSheet();
$sh->setTitle('PPN MASUKAN');

// ---- Judul & periode (teks polos, TANPA merge cell) ------------------------
$sh->setCellValue('A1', 'SUB LEDGER - PPN MASUKAN');
$sh->setCellValue('A2', 'Period : ' . date('d M Y', strtotime($start_date)) . ' s/d ' . date('d M Y', strtotime($end_date)));
$sh->getStyle('A1')->getFont()->setBold(true)->setSize(12);

// ---- Header DUA baris (4 & 5) — sama seperti di layar, TAPI tanpa merge -----
// Baris 4 = nama grup, ditulis di sel PERTAMA tiap grup (sel grup lainnya dibiar-
// kan kosong). Baris 5 = nama sub-kolom. Kolom tunggal (SI No .. Profit Center)
// judulnya ditaruh di baris 4 saja.
// Sengaja tanpa merge cell: sel gabungan bikin file susah di-filter/di-pivot dan
// sering kacau kalau dibuka di aplikasi selain Excel.
$H  = 4;   // baris nama grup
$H2 = 5;   // baris sub-kolom
$R0 = 6;   // data mulai di sini

$barisGrup = [
    'A' => 'SI No', 'B' => 'SI Date', 'C' => 'Supplier', 'D' => 'No Faktur Pajak', 'E' => 'Profit Center',
    'F' => 'Beginning Balance', 'I' => 'Addition', 'L' => 'Deduction',
    'Q' => 'Reclassification', 'V' => 'Adjustment', 'AA' => 'Ending Balance',
];
foreach ($barisGrup as $c => $t) { $sh->setCellValue($c . $H, $t); }

$barisSub = [
    'F' => 'Currency', 'G' => 'Amount OCY', 'H' => 'Eqv IDR',
    'I' => 'Currency', 'J' => 'Amount OCY', 'K' => 'Eqv IDR',
    'L' => 'Doc No', 'M' => 'Date', 'N' => 'Currency', 'O' => 'Amount OCY', 'P' => 'Eqv IDR',
    'Q' => 'Doc No', 'R' => 'Date', 'S' => 'Currency', 'T' => 'Amount OCY', 'U' => 'Eqv IDR',
    'V' => 'Doc No', 'W' => 'Date', 'X' => 'Currency', 'Y' => 'Amount OCY', 'Z' => 'Eqv IDR',
    'AA' => 'Currency', 'AB' => 'Amount OCY', 'AC' => 'Eqv IDR',
];
foreach ($barisSub as $c => $t) { $sh->setCellValue($c . $H2, $t); }

$sh->getStyle("A$H:AC$H2")->getFont()->setBold(true);
// Rata KIRI-ATAS. wrapText dimatikan supaya nama grup boleh meluber ke sel grup
// di sebelahnya yang sengaja dikosongkan — inilah yang bikin tampilannya seperti
// sel gabungan padahal tidak di-merge.
$sh->getStyle("A$H:AC$H2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
   ->setVertical(Alignment::VERTICAL_TOP)->setWrapText(false);
// ---- Isi data --------------------------------------------------------------
// Angka ditulis sebagai ANGKA (bukan teks berformat) supaya bisa langsung
// dijumlah/di-pivot di Excel. Tanda Deduction/Reclass/Adjustment dibalik persis
// seperti di layar, jadi rumusnya tetap: Beg + Add - Ded - Rcl - Adj = End.
$num = function ($v) { return round((float) $v, 2); };
$tgl = function ($v) { return (!empty($v) && $v != '0000-00-00') ? date('d-M-Y', strtotime($v)) : ''; };
$nz  = function ($v) { return abs((float) $v) > 0.0000001; };
$STR = DataType::TYPE_STRING;

$r = $R0;
$tot = array_fill_keys(['beg_o','beg_i','add_o','add_i','ded_o','ded_i','rcl_o','rcl_i','adj_o','adj_i','end_o','end_i'], 0.0);
$curSet = [];

while ($x = mysqli_fetch_assoc($res)) {
    $cur = $x['curr'];
    if ($cur !== '') { $curSet[$cur] = 1; }

    $sh->setCellValueExplicit("A$r", $x['si_no'], $STR);
    $sh->setCellValue("B$r", $tgl($x['si_date']));
    // Supplier kosong -> "-" (permintaan user, sama seperti di layar).
    $sh->setCellValue("C$r", ($x['nama_supp'] !== '' && $x['nama_supp'] !== null) ? $x['nama_supp'] : '-');
    $sh->setCellValueExplicit("D$r", $x['faktur_pajak'], $STR);
    $sh->setCellValue("E$r", $x['profit_center']);

    $sh->setCellValue("F$r", $cur);
    $sh->setCellValue("G$r", $num($x['beg_ocy'])); $tot['beg_o'] += (float) $x['beg_ocy'];
    $sh->setCellValue("H$r", $num($x['beg_idr'])); $tot['beg_i'] += (float) $x['beg_idr'];

    // Addition SELALU diisi (Currency + 0.00 kalau tidak ada mutasi) — permintaan
    // user: kolom mutasi jangan kosong. Sama seperti tampilan layar.
    $sh->setCellValue("I$r", $cur);
    $sh->setCellValue("J$r", $num($x['add_ocy'])); $tot['add_o'] += (float) $x['add_ocy'];
    $sh->setCellValue("K$r", $num($x['add_idr'])); $tot['add_i'] += (float) $x['add_idr'];

    // Deduction/Reclassification/Adjustment: Currency & Amount SELALU diisi;
    // Doc No & Date tetap "-" kalau memang tidak ada dokumennya (bukan angka).
    foreach ([['ded','L','ded_no','ded_date'], ['rcl','Q','rcl_no','rcl_date'], ['adj','V','adj_no','adj_date']] as $b) {
        list($k, $c0, $kNo, $kTgl) = $b;
        $ada = $nz($x[$k . '_ocy']) || $nz($x[$k . '_idr']);
        $c = $c0;
        $sh->setCellValueExplicit($c . $r, $ada ? $x[$kNo] : '-', $STR); $c++;
        $sh->setCellValue($c . $r, $ada ? $tgl($x[$kTgl]) : '-');        $c++;
        $sh->setCellValue($c . $r, $cur);                                $c++;
        $sh->setCellValue($c . $r, $num(-1 * (float) $x[$k . '_ocy'])); $c++;
        $sh->setCellValue($c . $r, $num(-1 * (float) $x[$k . '_idr']));
        $tot[$k . '_o'] += -1 * (float) $x[$k . '_ocy'];
        $tot[$k . '_i'] += -1 * (float) $x[$k . '_idr'];
    }
    $sh->setCellValue("AA$r", $cur);
    $sh->setCellValue("AB$r", $num($x['end_ocy'])); $tot['end_o'] += (float) $x['end_ocy'];
    $sh->setCellValue("AC$r", $num($x['end_idr'])); $tot['end_i'] += (float) $x['end_idr'];
    $r++;
}
$last  = $r - 1;
$baris = ($last >= $R0) ? ($last - $R0 + 1) : 0;

// ---- Baris GRAND TOTAL -----------------------------------------------------
$T = ($baris > 0) ? $last + 1 : $R0;
$satuMataUang = (count($curSet) === 1) ? key($curSet) : '';
$sh->setCellValue("A$T", 'GRAND TOTAL');
$sh->setCellValue("B$T", number_format($baris) . ' rows');
foreach ([['F','G','H','beg'], ['I','J','K','add'], ['N','O','P','ded'],
          ['S','T','U','rcl'], ['X','Y','Z','adj'], ['AA','AB','AC','end']] as $g) {
    // Amount OCY hanya diisi kalau seluruh baris satu mata uang — menjumlah
    // IDR + USD tidak ada artinya (sama seperti tanda "—" di layar).
    $sh->setCellValue($g[0] . $T, $satuMataUang);
    if ($satuMataUang !== '') { $sh->setCellValue($g[1] . $T, round($tot[$g[3] . '_o'], 2)); }
    else { $sh->setCellValue($g[1] . $T, '-'); }
    $sh->setCellValue($g[2] . $T, round($tot[$g[3] . '_i'], 2));
}
$sh->getStyle("A$T:AC$T")->getFont()->setBold(true);
$sh->getStyle("A$T:AC$T")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);

// ---- Format & tata letak ---------------------------------------------------
$akhir = max($T, $R0);
$FMT = '#,##0.00_);(#,##0.00)';
foreach (['G','H','J','K','O','P','T','U','Y','Z','AB','AC'] as $c) {
    $sh->getStyle($c . $R0 . ':' . $c . $akhir)->getNumberFormat()->setFormatCode($FMT);
}
// Kolom kode (nomor dokumen, faktur) DIPAKSA format Teks: kalau dibiarkan
// General, Excel membaca 04002600200893362 sebagai angka lalu awalan 0 hilang
// dan digit terakhirnya dibulatkan (presisi double cuma ~15-16 digit).
foreach (['A','D','L','Q','V'] as $c) {
    $sh->getStyle($c . $R0 . ':' . $c . $akhir)->getNumberFormat()->setFormatCode('@');
}
foreach (['E','F','I','N','S','X','AA'] as $c) {
    $sh->getStyle($c . $R0 . ':' . $c . $akhir)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}
// Border di SELURUH area tabel (header + data + grand total)
$sh->getStyle("A$H:AC$akhir")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// ---- Bikin header TERLIHAT seperti sel gabungan, tanpa benar-benar di-merge --
// Caranya: garis di DALAM blok header dihapus, kotak luarnya dibiarkan.
//   - baris grup  : garis vertikal antar kolom satu grup dibuang (F|G|H jadi satu)
//   - kolom tunggal: garis horizontal antara baris 4 & 5 dibuang (SI No dst.
//     terlihat memanjang ke bawah menutupi dua baris)
$tanpaGaris = ['borders' => ['vertical' => ['borderStyle' => Border::BORDER_NONE]]];
foreach (['F:H', 'I:K', 'L:P', 'Q:U', 'V:Z', 'AA:AC'] as $g) {
    list($a, $b) = explode(':', $g);
    $sh->getStyle($a . $H . ':' . $b . $H)->applyFromArray($tanpaGaris);
}
$sh->getStyle('A' . $H . ':E' . $H2)
   ->applyFromArray(['borders' => ['horizontal' => ['borderStyle' => Border::BORDER_NONE]]]);

foreach (range('A', 'Z') as $c) { $sh->getColumnDimension($c)->setAutoSize(true); }
foreach (['AA', 'AB', 'AC'] as $c) { $sh->getColumnDimension($c)->setAutoSize(true); }
foreach (['A', 'C', 'L', 'Q', 'V'] as $c) { $sh->getColumnDimension($c)->setAutoSize(false)->setWidth(26); }
$sh->getRowDimension($H)->setRowHeight(17);
$sh->getRowDimension($H2)->setRowHeight(17);
$sh->freezePane('F' . $R0);          // 5 kolom kiri + kedua baris header ikut terkunci

// ---- Kirim berkas ----------------------------------------------------------
$namaFile = 'SubLedger_PPN_Masukan_' . date('Ymd', strtotime($start_date)) . '_' . date('Ymd', strtotime($end_date)) . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $namaFile . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');
(new Xls($ss))->save('php://output');
exit;
