<?php
/* ============================================================================
   MATRIKS CASH FLOW DI EXCEL - PEMBUATNYA, DIPAKAI BERSAMA.

   Dipakai DUA ekspor sekaligus:
     - ekspor_cf_history.php           (potret yang sudah disimpan)
     - ekspor_cash_flow_projection.php (laporan yang sedang tampil)
   Sebelum 20 Nov 2026 yang kedua bentuknya tabel HTML berkepala
   vnd-ms-excel - satu berkas = satu lembar - sehingga tidak bisa memuat dua
   susunan (per tanggal & per rekening) sebagai dua lembar. Begitu keduanya
   memakai PhpSpreadsheet, pembuat lembarnya tidak boleh ditulis dua kali:
   dua salinan tata letak yang sama cepat atau lambat berbeda, dan bedanya
   baru ketahuan waktu dua Excel dibandingkan berdampingan.

   TIDAK meng-include conn.php maupun vendor/autoload.php: pemanggilnya yang
   mengurus itu, supaya berkas ini aman di-require dari mana pun.
   ============================================================================ */

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/** Pembulat yang dipakai seluruh sel uang di sini - setengah ke atas, sama
    dgn yang dipakai layar. Namanya sengaja BUKAN cfe_bulat(): yang itu milik
    cf_exec_lib.php dan dideklarasikan tanpa pagar function_exists, jadi dua
    berkas yang sama-sama mendeklarasikannya tidak bisa dimuat bersama. */
if (!function_exists('cfx_bulat')) {
    function cfx_bulat($v)
    {
        return (float) floor((float) $v + 0.5);
    }
}

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
            cfhxSel($l2, 'D', $r, cfx_bulat($b['tot']), CFHX_UANG);
        }
        for ($i = 0; $i < $nk; $i++) {
            if (isset($b['v'][$i]) && $b['v'][$i] !== null) {
                cfhxSel($l2, cfhxKol(5 + $i), $r, cfx_bulat($b['v'][$i]), CFHX_UANG);
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

/** Satu lembar matriks lengkap - kepala berkas, kepala tabel, baris
    laporan, lalu blok pinjaman. Dipanggil dua kali: sekali untuk susunan
    per tanggal, sekali untuk susunan per rekening. */
function cfhxLembarProyeksi($sp, $judulLembar, $warnaTab, $kolomDef, $daftar, $pinjam,
                            $judulBerkas, $info)
{
    $l2 = $sp->createSheet();
    $l2->setTitle($judulLembar);
    $l2->getTabColor()->setRGB($warnaTab);

    $judulKolom = array();
    foreach ($kolomDef as $k) {
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

    $barisKepala = $r;
    $r = cfhxKepalaMatriks($l2, $r, $judulKolom, $kolAkhir);
    $r = cfhxTulisBaris($l2, $r, $daftar, $nk, $kolAkhir);

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
}
