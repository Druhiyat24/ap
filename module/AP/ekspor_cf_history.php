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

/* Gaya & pembuat lembar matriksnya ada di pustaka bersama - dipakai juga
   ekspor laporan yang sedang tampil, supaya dua Excel itu mustahil berbeda
   tata letaknya. Di-require DI ATAS karena fungsinya sudah dipakai jauh
   sebelum titik ini - dulu bisa dipanggil lebih awal cuma karena PHP
   mendaftarkan fungsi tingkat atas saat berkasnya dibaca. */
require_once __DIR__ . '/cf_xlsx_lib.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/* KELUARAN BERKAS BINER - pesan PHP apa pun yang tercetak akan MASUK KE
   DALAM berkasnya dan membuat Excel menolak membukanya. Jadi tampilannya
   dimatikan di sini; kesalahannya tetap tercatat di log Apache. Pernah
   kejadian 19 Nov 2026: satu Notice "Undefined index: AR_EXISTING" membuat
   seluruh berkas unduhan rusak. */
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);

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

/* Bank & Profit Center lembar AR Existing dilengkapi DI SINI JUGA, bukan
   cuma waktu potret dibuat - potret yang sudah terlanjur disimpan pun jadi
   terbaca lengkap tanpa perlu disimpan ulang. Yang diisi HANYA YANG KOSONG,
   jadi tidak ada angka beku yang berubah: kedua kolom itu memang tidak
   pernah ikut tersimpan di potret AR-nya. */
if (!empty($sumber['AR_EXISTING'])) {
    $sumber['AR_EXISTING'] = cfs_lengkapi_ar($conn2, $sumber['AR_EXISTING']);
}

$info   = isset($jawab['info']) ? $jawab['info'] : array();
$kolom  = isset($jawab['kolom']) ? $jawab['kolom'] : array();
$baris  = isset($jawab['baris']) ? $jawab['baris'] : array();
$pinjam = isset($jawab['pinjaman']) ? $jawab['pinjaman'] : array();

/* DUA SUSUNAN, DUA LEMBAR (permintaan pemakai 20 Nov 2026). Potret lama cuma
   menyimpan satu susunan - lembarnya yang kosong tidak dibuat sama sekali,
   bukan dibuat lalu dibiarkan kosong. */
$cfhxKolomTgl  = isset($jawab['kolom_tgl'])     ? $jawab['kolom_tgl']     : array();
$cfhxKolomAkun = isset($jawab['kolom_akun'])    ? $jawab['kolom_akun']    : array();
$cfhxBarisTgl  = isset($jawab['baris_tgl'])     ? $jawab['baris_tgl']     : array();
$cfhxPinjTgl   = isset($jawab['pinjaman_tgl'])  ? $jawab['pinjaman_tgl']  : array();
$cfhxBarisAkun = isset($jawab['baris_akun'])    ? $jawab['baris_akun']    : array();
$cfhxPinjAkun  = isset($jawab['pinjaman_akun']) ? $jawab['pinjaman_akun'] : array();
if (!$cfhxBarisTgl && !$cfhxBarisAkun) {   /* jawaban lama */
    $cfhxKolomTgl = $kolom; $cfhxBarisTgl = $baris; $cfhxPinjTgl = $pinjam;
}

/* NOMOR POTRET AR DICETAK (permintaan pemakai 18 Nov 2026). Tanpa itu lembar
   AR Existing tidak bisa ditelusuri: pembacanya tidak tahu angkanya berasal
   dari potret AR yang mana, padahal AR menyimpan potret baru tiap pekan.
   Nomornya dibaca dari kepala potret (ar_doc), bukan dicari ulang - mencari
   ulang bisa menemukan potret AR yang LEBIH BARU daripada yang dipakai waktu
   angka ini dibekukan. */
$cfhxArDoc = trim((string) cfhxAmbil($info, 'ar_doc'));
/* Lembar PV AP TIDAK punya kolom Source Doc - susunannya meniru acuan, dan
   acuan tidak punya kolom itu. Nomornya ditulis di baris catatan. */
$cfhxApDoc = trim((string) cfhxAmbil($info, 'ap_doc'));
$lembarSumber['PV_AP']['catatan'] = ($cfhxApDoc !== '')
    ? 'From saved AP payment projection ' . $cfhxApDoc . '.'
    : 'No saved AP projection covered this period, so the figures were recalculated (not saved).';

$lembarSumber['AR_EXISTING']['catatan'] = ($cfhxArDoc !== '')
    ? 'From saved AR collection projection ' . $cfhxArDoc . ' - amounts are net of withholding tax.'
    /* Sejak 18 Nov 2026 periode tanpa potret DIHITUNG ULANG, jadi kalimat
       lamanya ("receivable lines are zero") sudah tidak benar - lembarnya
       kosong hanya kalau hitung ulang itu memang tidak menemukan piutang
       jatuh tempo. */
    : 'No saved AR projection covered this period, so the figures were recalculated (not saved).';

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
    array('AR Source',         ($cfhxArDoc !== '') ? $cfhxArDoc : '(no AR projection)'),
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
/* Urutan lembarnya mengikuti type potret: susunan yang jadi tab bawaan di
   layar ditaruh lebih dulu, supaya Excel dan layar terbaca searah. */
$cfhxLembar = array(
    array('Projection by Date', 'F2CFEE', $cfhxKolomTgl,  $cfhxBarisTgl,  $cfhxPinjTgl),
    array('Projection by Bank', 'CFE3F2', $cfhxKolomAkun, $cfhxBarisAkun, $cfhxPinjAkun),
);
if (isset($jawab['mode']) && $jawab['mode'] === 'akun') { $cfhxLembar = array_reverse($cfhxLembar); }
foreach ($cfhxLembar as $cfhxL) {
    if (!$cfhxL[3]) { continue; }   /* susunan ini tidak tersimpan di potret */
    cfhxLembarProyeksi($sp, $cfhxL[0], $cfhxL[1], $cfhxL[2], $cfhxL[3], $cfhxL[4],
                       $judulBerkas, $info);
}

/* ======================================================================
   LEMBAR 3 dst - DATA SUMBER
   Satu lembar per jenis, susunan kolomnya dari cf_sumber_lembar() - satu
   tempat yang sama dgn yang dipakai waktu menyimpannya, jadi kolom dan isi
   tidak bisa bergeser sendiri.
   ====================================================================== */
foreach ($lembarSumber as $kode => $def) {
    /* Penulisnya pindah ke cf_xlsx_lib.php (20 Nov 2026) - ekspor laporan
       yang sedang tampil memakai lembar yang sama, dan dua salinan penulis
       cepat atau lambat berbeda kolomnya. */
    cfhxLembarSumber($sp, $def,
                     isset($sumber[$kode]) ? $sumber[$kode] : array(),
                     $judulBerkas);
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
