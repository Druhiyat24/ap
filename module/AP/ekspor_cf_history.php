<?php
/* ============================================================================
   HISTORY PROJECTION - EKSPOR EXCEL SATU POTRET, DUA LEMBAR.

       1. Executive Summary   kartu angka, penggerak terbesar, outlook pekanan
       2. Projection          matriks potretnya apa adanya

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI - dan tidak di mana pun. Berkas ini
   menyetel $_POST['doc_number'] lalu meng-include ajx_cf_history_detail.php
   dalam mode pustaka (CFH_PUSTAKA), dan ringkasannya dirakit fungsi yang
   SAMA dgn yang dipakai layar (cf_exec_ringkasan di cf_exec_lib.php). Jadi
   isi Excel mustahil beda dgn kedua tab di layarnya.

   PAKAI PhpSpreadsheet, bukan tabel HTML bertipe vnd-ms-excel seperti
   ekspor_cash_flow_projection.php. Alasannya satu: cara HTML itu hanya
   sanggup SATU lembar, sedangkan berkas ini harus keluar dua lembar
   (permintaan pemakai 17 Nov 2026). Pustakanya sudah ada di vendor/ dan
   sudah dipakai ekspor_ap_realization_report.php, jadi tidak ada
   kebergantungan baru.

   ANGKANYA DIBULATKAN SEPERTI LAYAR (floor(x+0.5), lihat cfe_bulat) lalu
   ditulis sbg ANGKA - bukan teks yang kebetulan mirip angka - sehingga SUM
   di Excel langsung jalan. Minus tampil DALAM KURUNG lewat number format,
   nilainya tetap negatif.
   ============================================================================ */

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

session_start();
define('CFH_PUSTAKA', 1);
$_POST['doc_number'] = isset($_REQUEST['doc_number']) ? $_REQUEST['doc_number'] : '';

ob_start();
include __DIR__ . '/ajx_cf_history_detail.php';
$jawab = json_decode(trim((string) ob_get_clean()), true);

if (!is_array($jawab) || !isset($jawab['status']) || $jawab['status'] !== 'success') {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'The snapshot could not be exported: '
       . (is_array($jawab) && isset($jawab['message']) ? $jawab['message'] : 'no data.');
    exit;
}

include __DIR__ . '/cf_exec_lib.php';
$ring = cf_exec_ringkasan($jawab);

/* ---- rincian sumber (lembar pendukung) ----
   DIBACA DARI POTRET, bukan dihitung ulang: barisnya ikut dibekukan waktu
   Save History (lihat ap_cf_history_src & cf_sumber_lib.php). Potret lama
   yang disimpan sebelum tabel itu ada tidak punya baris ini - lembarnya
   dilewati, dan itu memang jujur: datanya tidak pernah dibekukan. */
include __DIR__ . '/cf_sumber_lib.php';
$lembarSumber = cf_sumber_lembar();
$sumber = array();
$cfhxDocEsc = mysqli_real_escape_string($conn2, (string) $jawab['info']['doc_number']);
$q = mysqli_query($conn2, "show tables like 'ap_cf_history_src'");
if ($q && mysqli_num_rows($q)) {
    $q = mysqli_query($conn2, "select jenis, data from ap_cf_history_src
                                where doc_number = '$cfhxDocEsc' order by jenis asc, urut asc");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $baris = json_decode((string) $r['data'], true);
        if (is_array($baris)) { $sumber[$r['jenis']][] = $baris; }
    }
}

$info   = isset($jawab['info']) ? $jawab['info'] : array();
$kolom  = isset($jawab['kolom']) ? $jawab['kolom'] : array();
$baris  = isset($jawab['baris']) ? $jawab['baris'] : array();
$pinjam = isset($jawab['pinjaman']) ? $jawab['pinjaman'] : array();

/* ------------------------------------------------------------------ gaya */
const CFHX_NAVY  = 'FF1E3A8A';
const CFHX_BLOK  = 'FF1F2937';
const CFHX_GRUP  = 'FFEDF1F7';
const CFHX_TOT   = 'FFF4F6FA';
const CFHX_SAL   = 'FFDCE6F5';
const CFHX_PINJ  = 'FFFDF0E2';
const CFHX_SUB   = 'FFFEF8F1';
/* Gaya akuntansi: minus DALAM KURUNG, sama dgn layar. Nilainya tetap
   negatif, yang diatur cuma cara Excel menampilkannya - jadi penjumlahan
   dan grafik tetap benar. */
const CFHX_UANG  = '#,##0;(#,##0)';
const CFHX_TGL   = 'dd mmm yyyy';

function cfhxGarisTipis()
{
    return array('borderStyle' => Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'));
}

function cfhxKepala($lembar, $rentang, $warna = CFHX_NAVY)
{
    $lembar->getStyle($rentang)->applyFromArray(array(
        'font' => array('bold' => true, 'size' => 9, 'color' => array('argb' => 'FFF8FAFC')),
        'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('argb' => $warna)),
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER,
                             'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true),
        'borders' => array('allBorders' => cfhxGarisTipis()),
    ));
}

function cfhxJudul($lembar, $brs, $teks, $besar = 12)
{
    $lembar->setCellValue('A' . $brs, $teks);
    $lembar->getStyle('A' . $brs)->getFont()->setBold(true)->setSize($besar);
}

/** Menulis satu sel: angka tetap angka, null tetap kosong, teks tetap teks. */
function cfhxSel($lembar, $kol, $brs, $nilai, $format = null)
{
    $alamat = $kol . $brs;
    if ($nilai === null || $nilai === '') {
        $lembar->setCellValue($alamat, null);
    } elseif (is_int($nilai) || is_float($nilai)) {
        $lembar->setCellValue($alamat, $nilai);
    } else {
        /* Nomor dokumen WAJIB teks: "CF-PPR/1126/00003" kalau dibiarkan
           ditebak Excel bisa berubah bentuk. */
        $lembar->setCellValueExplicit($alamat, (string) $nilai, DataType::TYPE_STRING);
    }
    if ($format !== null) {
        $lembar->getStyle($alamat)->getNumberFormat()->setFormatCode($format);
    }
}

function cfhxSelTgl($lembar, $kol, $brs, $iso)
{
    $alamat = $kol . $brs;
    if (empty($iso)) { $lembar->setCellValue($alamat, null); return; }
    /* JANGAN PHPToExcel(strtotime(...)): cap waktu tengah malam menurut
       zona waktu PHP dibaca sbg UTC, jadi tanggalnya MUNDUR SATU HARI di
       zona mana pun yang lebih timur dari UTC (server ini Europe/Berlin -
       2026-11-01 jadi 31 Oct 23:00). formattedPHPToExcel menerima
       tahun-bulan-tanggal apa adanya, tanpa zona waktu. */
    $b = explode('-', substr((string) $iso, 0, 10));
    if (count($b) !== 3) { $lembar->setCellValue($alamat, null); return; }
    $lembar->setCellValue($alamat, \PhpOffice\PhpSpreadsheet\Shared\Date::formattedPHPToExcel(
        (int) $b[0], (int) $b[1], (int) $b[2]));
    $lembar->getStyle($alamat)->getNumberFormat()->setFormatCode(CFHX_TGL);
}

function cfhxKol($n)
{
    return Coordinate::stringFromColumnIndex($n);
}

function cfhxAmbil($a, $k, $bawaan = '')
{
    return isset($a[$k]) ? $a[$k] : $bawaan;
}

$sp = new Spreadsheet();
$sp->getProperties()->setCreator('AP')->setTitle('Cash Flow Projection Snapshot');
$judulBerkas = 'CASH FLOW PROJECTION - ' . cfhxAmbil($info, 'doc_number')
             . (cfhxAmbil($info, 'status') === 'CANCEL' ? ' (CANCELLED)' : '');

/* ======================================================================
   LEMBAR 1 - EXECUTIVE SUMMARY
   Susunannya meniru lembar "Executive Summary" di berkas acuan, tapi
   indikatornya ditulis MENURUN (Indicator | Value | Note) seperti ekspor
   Realization - bentuk itu bisa disaring dan diurut di Excel, sedangkan
   kotak 3x3 di berkas acuan tidak.
   ====================================================================== */
$l1 = $sp->getActiveSheet();
$l1->setTitle('Executive Summary');
/* Tab lembar diberi warna spt berkas acuan (lembar ringkasan hijau, lembar
   datanya merah muda) supaya langsung kelihatan mana yang mana. */
$l1->getTabColor()->setRGB('92D050');
cfhxJudul($l1, 1, $judulBerkas, 13);
cfhxJudul($l1, 2, 'EXECUTIVE SUMMARY', 11);

$meta = array(
    array('Projection Period', cfhxAmbil($info, 'periode')),
    array('Type',              cfhxAmbil($info, 'type')),
    array('Profit Center',     cfhxAmbil($info, 'pc')),
    array('Bank Filter',       cfhxAmbil($info, 'bank')),
    array('Rate',              (float) cfhxAmbil($info, 'rate', 0)),
);
$r = 4;
foreach ($meta as $m) {
    $l1->setCellValue('A' . $r, $m[0]);
    cfhxSel($l1, 'C', $r, $m[1], is_float($m[1]) ? CFHX_UANG : null);
    $l1->getStyle('A' . $r)->getFont()->setBold(true);
    $r++;
}

$r++;
$l1->setCellValue('A' . $r, 'INDICATOR');
$l1->setCellValue('C' . $r, 'Value');
$l1->setCellValue('D' . $r, 'Note');
cfhxKepala($l1, 'A' . $r . ':D' . $r);
$l1->mergeCells('A' . $r . ':B' . $r);
$r++;

foreach ($ring['kpi'] as $k) {
    $l1->mergeCells('A' . $r . ':B' . $r);
    $l1->setCellValue('A' . $r, $k['label']);
    if ($k['jenis'] === 'tgl') {
        /* "No Shortfall Found" adalah jawaban, bukan tanggal kosong. */
        if ($k['teks'] === '' ) {
            cfhxSel($l1, 'C', $r, null);
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $k['teks'])) {
            cfhxSelTgl($l1, 'C', $r, $k['teks']);
        } else {
            cfhxSel($l1, 'C', $r, $k['teks']);
        }
    } elseif ($k['nilai'] === null) {
        cfhxSel($l1, 'C', $r, null);
    } elseif ($k['jenis'] === 'angka') {
        cfhxSel($l1, 'C', $r, (float) $k['nilai'], '#,##0');
    } else {
        cfhxSel($l1, 'C', $r, (float) $k['nilai'], CFHX_UANG);
    }
    $l1->setCellValue('D' . $r, $k['sub']);
    $l1->getStyle('A' . $r . ':D' . $r)->applyFromArray(
        array('borders' => array('allBorders' => cfhxGarisTipis())));
    $l1->getStyle('D' . $r)->getFont()->setSize(9)->getColor()->setARGB('FF64748B');
    $r++;
}

/* ---- penggerak terbesar ---- */
$r += 1;
$l1->setCellValue('A' . $r, 'CASH FLOW DRIVERS');
$l1->getStyle('A' . $r)->getFont()->setBold(true);
$r++;
$l1->setCellValue('A' . $r, 'Top Cash Inflows');
$l1->setCellValue('C' . $r, 'Amount');
cfhxKepala($l1, 'A' . $r . ':C' . $r);
$l1->mergeCells('A' . $r . ':B' . $r);
$r++;
foreach ($ring['in'] as $x) {
    $l1->mergeCells('A' . $r . ':B' . $r);
    $l1->setCellValue('A' . $r, $x['label']);
    cfhxSel($l1, 'C', $r, (float) $x['nilai'], CFHX_UANG);
    $l1->getStyle('A' . $r . ':C' . $r)->applyFromArray(
        array('borders' => array('allBorders' => cfhxGarisTipis())));
    $r++;
}
$r++;
$l1->setCellValue('A' . $r, 'Top Cash Outflows');
$l1->setCellValue('C' . $r, 'Amount');
cfhxKepala($l1, 'A' . $r . ':C' . $r);
$l1->mergeCells('A' . $r . ':B' . $r);
$r++;
foreach ($ring['out'] as $x) {
    $l1->mergeCells('A' . $r . ':B' . $r);
    $l1->setCellValue('A' . $r, $x['label']);
    cfhxSel($l1, 'C', $r, (float) $x['nilai'], CFHX_UANG);
    $l1->getStyle('A' . $r . ':C' . $r)->applyFromArray(
        array('borders' => array('allBorders' => cfhxGarisTipis())));
    $r++;
}

/* ---- outlook pekanan ----
   Potret Weekly/Monthly berkolom REKENING, jadi tidak punya tanggal sama
   sekali - tabel ini mustahil disusun. Ditulis terang-terangan, bukan
   ditinggal kosong spt kehilangan data. */
$r += 1;
$l1->setCellValue('A' . $r, 'WEEKLY LIQUIDITY OUTLOOK');
$l1->getStyle('A' . $r)->getFont()->setBold(true);
$r++;

if (!count($ring['pekan'])) {
    $l1->setCellValue('A' . $r, $ring['catatan']);
    $l1->getStyle('A' . $r)->getFont()->setItalic(true)->getColor()->setARGB('FF9A3412');
    $r++;
} else {
    $pekan = $ring['pekan'];
    $awalPekan = $r;
    $l1->setCellValue('A' . $r, 'Week');
    $l1->mergeCells('A' . $r . ':B' . $r);
    for ($i = 0; $i < count($pekan); $i++) {
        $l1->setCellValue(cfhxKol(3 + $i) . $r, '#' . $pekan[$i]['no']);
    }
    cfhxKepala($l1, 'A' . $r . ':' . cfhxKol(2 + count($pekan)) . $r);
    $r++;

    $barisPekan = array(
        array('Start Date',      'mulai',  'tgl'),
        array('End Date',        'akhir',  'tgl'),
        array('Opening Balance', 'buka',   'uang'),
        array('Cash Inflows',    'masuk',  'uang'),
        array('Cash Outflows',   'keluar', 'uang'),
        array('Closing Balance', 'tutup',  'uang'),
        array('Lowest Balance',  'rendah', 'uang'),
        array('UNDRAWN CREDIT FACILITY', null, null),
        array('- IDR',           'idr',    'uang'),
        array('- USD (in IDR)',  'usd',    'uang'),
        array('Total Facility',  'total',  'uang'),
    );
    foreach ($barisPekan as $bp) {
        $l1->mergeCells('A' . $r . ':B' . $r);
        $l1->setCellValue('A' . $r, $bp[0]);
        if ($bp[1] === null) {
            $l1->getStyle('A' . $r . ':' . cfhxKol(2 + count($pekan)) . $r)->applyFromArray(array(
                'font' => array('bold' => true, 'size' => 9),
                'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('argb' => CFHX_GRUP)),
            ));
        } else {
            for ($i = 0; $i < count($pekan); $i++) {
                $kol = cfhxKol(3 + $i);
                if ($bp[2] === 'tgl') {
                    cfhxSelTgl($l1, $kol, $r, $pekan[$i][$bp[1]]);
                } else {
                    cfhxSel($l1, $kol, $r, (float) $pekan[$i][$bp[1]], CFHX_UANG);
                }
            }
            if ($bp[0] === 'Closing Balance' || $bp[0] === 'Total Facility') {
                $l1->getStyle('A' . $r . ':' . cfhxKol(2 + count($pekan)) . $r)->getFont()->setBold(true);
            }
        }
        $l1->getStyle('A' . $r . ':' . cfhxKol(2 + count($pekan)) . $r)->applyFromArray(
            array('borders' => array('allBorders' => cfhxGarisTipis())));
        $r++;
    }
    for ($i = 0; $i < count($pekan); $i++) {
        $l1->getColumnDimension(cfhxKol(3 + $i))->setWidth(17);
    }
    unset($awalPekan);
}

$l1->getColumnDimension('A')->setWidth(34);
$l1->getColumnDimension('B')->setWidth(14);
if (!count($ring['pekan'])) { $l1->getColumnDimension('C')->setWidth(22); }
$l1->getColumnDimension('D')->setWidth(46);

/* ======================================================================
   LEMBAR 2 - PROJECTION
   Susunan kolomnya SAMA dgn ekspor layar proyeksinya:
     A,B,C = tiga tingkat keterangan, D = TOTAL, E.. = tanggal / rekening.
   ====================================================================== */
$l2 = $sp->createSheet();
$l2->setTitle('Projection');
$l2->getTabColor()->setRGB('F2CFEE');

$judulKolom = array();
foreach ($kolom as $k) {
    $judulKolom[] = (isset($k['tgl']) && $k['tgl'] !== '') ? $k['tgl'] : cfhxAmbil($k, 'judul');
}
$nk = count($judulKolom);
$kolAkhir = 4 + $nk;

cfhxJudul($l2, 1, $judulBerkas, 13);
cfhxJudul($l2, 2, 'FOR PERIOD ' . strtoupper(cfhxAmbil($info, 'periode')), 11);
$r = 3;
foreach (array(array('TYPE', cfhxAmbil($info, 'type')),
               array('RATE', (float) cfhxAmbil($info, 'rate', 0)),
               array('PROFIT CENTER', cfhxAmbil($info, 'pc')),
               array('BANK', cfhxAmbil($info, 'bank'))) as $m) {
    $l2->setCellValue('A' . $r, $m[0]);
    cfhxSel($l2, 'C', $r, $m[1], is_float($m[1]) ? CFHX_UANG : null);
    $l2->getStyle('A' . $r)->getFont()->setBold(true);
    $r++;
}
$r++;

/** Kepala tabel matriks; dipanggil dua kali (laporan & blok pinjaman). */
function cfhxKepalaMatriks($l2, $r, $judulKolom, $kolAkhir)
{
    $l2->setCellValue('A' . $r, 'DESCRIPTIONS');
    $l2->mergeCells('A' . $r . ':C' . $r);
    $l2->setCellValue('D' . $r, 'TOTAL');
    for ($i = 0; $i < count($judulKolom); $i++) {
        $t = $judulKolom[$i];
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $t)) {
            cfhxSelTgl($l2, cfhxKol(5 + $i), $r, $t);
        } else {
            cfhxSel($l2, cfhxKol(5 + $i), $r, $t);
        }
    }
    cfhxKepala($l2, 'A' . $r . ':' . cfhxKol($kolAkhir) . $r);
    return $r + 1;
}

/** Warna latar per jenis baris - sepadan dgn layarnya. */
function cfhxWarnaBaris($tipe)
{
    switch ($tipe) {
        case 'sal':  return array(CFHX_SAL,  true,  'FF1E3A8A');
        case 'blok': return array(CFHX_BLOK, true,  'FFFFFFFF');
        case 'grup': return array(CFHX_GRUP, true,  'FF334155');
        case 'tot':
        case 'tot2': return array(CFHX_TOT,  true,  'FF1E293B');
        case 'net':  return array(null,      true,  'FF1E293B');
        case 'pinj': return array(CFHX_PINJ, true,  'FF9A3412');
        case 'sub':  return array(CFHX_SUB,  false, 'FF1E293B');
    }
    return array(null, false, 'FF1E293B');
}

/** Satu blok baris matriks. */
function cfhxTulisBaris($l2, $r, $daftar, $nk, $kolAkhir)
{
    foreach ($daftar as $b) {
        $no  = (string) cfhxAmbil($b, 'no');
        $lbl = (string) cfhxAmbil($b, 'label');
        /* Tiga tingkat keterangan, sama dgn lembar aslinya. */
        switch ($b['tipe']) {
            case 'grup':
                cfhxSel($l2, 'A', $r, $no); cfhxSel($l2, 'B', $r, $lbl);
                $l2->mergeCells('B' . $r . ':C' . $r);
                break;
            case 'item':
            case 'sub':
                cfhxSel($l2, 'B', $r, $no); cfhxSel($l2, 'C', $r, $lbl);
                break;
            case 'tot':
            case 'net':
                cfhxSel($l2, 'B', $r, $lbl);
                $l2->mergeCells('B' . $r . ':C' . $r);
                break;
            default:
                cfhxSel($l2, 'A', $r, $lbl);
                $l2->mergeCells('A' . $r . ':C' . $r);
        }
        if (isset($b['tot']) && $b['tot'] !== null) {
            cfhxSel($l2, 'D', $r, cfe_bulat($b['tot']), CFHX_UANG);
        }
        for ($i = 0; $i < $nk; $i++) {
            if (isset($b['v'][$i]) && $b['v'][$i] !== null) {
                cfhxSel($l2, cfhxKol(5 + $i), $r, cfe_bulat($b['v'][$i]), CFHX_UANG);
            }
        }
        list($latar, $tebal, $warnaTeks) = cfhxWarnaBaris($b['tipe']);
        $rentang = 'A' . $r . ':' . cfhxKol($kolAkhir) . $r;
        $gaya = array(
            'font' => array('bold' => $tebal, 'color' => array('argb' => $warnaTeks)),
            'borders' => array('allBorders' => cfhxGarisTipis()),
        );
        if ($latar !== null) {
            $gaya['fill'] = array('fillType' => Fill::FILL_SOLID, 'startColor' => array('argb' => $latar));
        }
        $l2->getStyle($rentang)->applyFromArray($gaya);
        $r++;
    }
    return $r;
}

$barisKepala = $r;
$r = cfhxKepalaMatriks($l2, $r, $judulKolom, $kolAkhir);
$r = cfhxTulisBaris($l2, $r, $baris, $nk, $kolAkhir);

if (count($pinjam)) {
    /* Kepalanya DIULANG di sini - beda dgn layar. Di Excel barisnya bisa
       tercetak di halaman lain, jadi kepalanya perlu. */
    $r++;
    $r = cfhxKepalaMatriks($l2, $r, $judulKolom, $kolAkhir);
    $r = cfhxTulisBaris($l2, $r, $pinjam, $nk, $kolAkhir);
}

$l2->freezePane('E' . ($barisKepala + 1));
$l2->getColumnDimension('A')->setWidth(5);
$l2->getColumnDimension('B')->setWidth(6);
$l2->getColumnDimension('C')->setWidth(46);
$l2->getColumnDimension('D')->setWidth(18);
for ($i = 0; $i < $nk; $i++) { $l2->getColumnDimension(cfhxKol(5 + $i))->setWidth(16); }

/* ======================================================================
   LEMBAR 3 dst - DATA SUMBER
   Satu lembar per jenis, susunan kolomnya dari cf_sumber_lembar() - satu
   tempat yang sama dgn yang dipakai waktu menyimpannya, jadi kolom dan isi
   tidak bisa bergeser sendiri.
   ====================================================================== */
foreach ($lembarSumber as $kode => $def) {
    if (empty($sumber[$kode])) { continue; }
    $ls = $sp->createSheet();
    $ls->setTitle($def['judul']);
    $ls->getTabColor()->setRGB($def['warna']);

    cfhxJudul($ls, 1, $judulBerkas . '  -  ' . strtoupper($def['judul']), 12);
    $ls->setCellValue('A2', $def['catatan']);
    $ls->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setARGB('FF64748B');

    $kol = $def['kolom'];
    $nKol = count($kol);
    $r = 4;
    for ($i = 0; $i < $nKol; $i++) { $ls->setCellValue(cfhxKol($i + 1) . $r, $kol[$i][0]); }
    cfhxKepala($ls, 'A' . $r . ':' . cfhxKol($nKol) . $r);
    $r++;

    foreach ($sumber[$kode] as $brs) {
        for ($i = 0; $i < $nKol; $i++) {
            $nama = $kol[$i][1];
            $jenis = $kol[$i][2];
            $v = isset($brs[$nama]) ? $brs[$nama] : null;
            $alamat = cfhxKol($i + 1);
            if ($jenis === 'd') {
                cfhxSelTgl($ls, $alamat, $r, $v);
            } elseif ($jenis === 'n') {
                /* null DIBIARKAN KOSONG, bukan jadi nol: "tidak diisi
                   tangan" dan "diisi tangan nol" dua hal yang berbeda. */
                cfhxSel($ls, $alamat, $r, ($v === null || $v === '') ? null : (float) $v, CFHX_UANG);
            } else {
                cfhxSel($ls, $alamat, $r, ($v === null) ? '' : (string) $v);
            }
        }
        $ls->getStyle('A' . $r . ':' . cfhxKol($nKol) . $r)->applyFromArray(
            array('borders' => array('allBorders' => cfhxGarisTipis())));
        $r++;
    }

    $ls->freezePane('A5');
    $ls->setAutoFilter('A4:' . cfhxKol($nKol) . ($r - 1));
    for ($i = 1; $i <= $nKol; $i++) {
        $ls->getColumnDimension(cfhxKol($i))->setWidth(($i <= 3) ? 26 : 16);
    }
}

$sp->setActiveSheetIndex(0);

/* ---------------------------------------------------------------- kirim */
/* Nomor dokumen memuat garis miring - tidak boleh masuk nama berkas. */
$nama = 'Cash Flow Projection ' . str_replace(array('/', '\\'), '-', cfhxAmbil($info, 'doc_number')) . '.xlsx';

/* Mode penjaga: dipakai pengujian supaya berkasnya bisa diperiksa tanpa
   mengirim apa pun ke peramban. */
if (defined('CFHX_KE_BERKAS') && CFHX_KE_BERKAS !== '') {
    IOFactory::createWriter($sp, 'Xlsx')->save(CFHX_KE_BERKAS);
    echo 'OK ' . CFHX_KE_BERKAS . "\n";
    return;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nama . '"');
header('Cache-Control: max-age=0');
IOFactory::createWriter($sp, 'Xlsx')->save('php://output');
