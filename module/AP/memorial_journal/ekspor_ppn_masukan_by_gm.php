<?php
// ============================================================================
// ekspor_ppn_masukan_by_gm.php — export DATA MENTAH/UPLOADAN (rekap faktur pajak
// apa adanya, sebelum diolah jadi jurnal) untuk SATU nomor jurnal GM tertentu.
//
// Dipakai dari tombol "Export" khusus di kolom Action memorial-journal.php -
// tombol itu HANYA muncul kalau no_mj-nya memang berasal dari tab PPN Masukan
// (ada baris di tbl_ppn_masukan_upload dengan no_mj tsb). Lihat ajx_memorial-journal.php.
//
// SENGAJA dibiarkan bisa diakses walau GM sudah Cancel / periode sudah closing —
// ini murni menampilkan ULANG data mentah yang diupload user dulu (read-only,
// tidak mengubah apa pun), jadi tidak ada alasan dibatasi status jurnalnya.
//
// Format kolom & gaya PERSIS format_ppn_masukan.xls (template upload) supaya
// hasil export ini BISA diupload ulang apa adanya kalau perlu diperbaiki & disimpan lagi.
// Writer dipakai Xls (bukan Xlsx) krn environment ini tak selalu punya ext. zip.
// ============================================================================
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

include '../../../conn/conn.php';
ini_set('memory_limit', '1024M');

$no_mj = isset($_GET['no_mj']) ? base64_decode($_GET['no_mj']) : '';
if ($no_mj === '') {
    http_response_code(400);
    echo 'no_mj parameter is required.';
    exit;
}
$e = mysqli_real_escape_string($conn2, $no_mj);

$q = mysqli_query($conn2, "select bulan, jenis, nama_penjual, npwp, no_faktur, tgl_faktur,
        dpp, dpp_nilai_lain, ppn, ppnbm, faktur_diganti
    from tbl_ppn_masukan_upload
    where no_mj = '$e'
    order by nama_penjual, no_faktur, id");

if ($q === false || mysqli_num_rows($q) === 0) {
    http_response_code(404);
    echo 'No PPN Masukan upload data found for this journal number.';
    exit;
}

$ss = new Spreadsheet();
$sh = $ss->getActiveSheet();
$sh->setTitle('PPN MASUKAN');

$head = ['Bulan', 'Jenis', 'Nama Penjual BKP/BKP Tidak Berwujud/Pemberi JKP', 'Nomor Identitas WP',
    'Faktur Pajak/Dokumen Tertentu/Nota Retur/Nota Pembatalan - Nomor',
    'Faktur Pajak/Dokumen Tertentu/Nota Retur/Nota Pembatalan - Tanggal',
    'Harga Jual/Penggantian/Nilai Impor/DPP (Rupiah)', 'DPP Nilai Lain / DPP (Rupiah)',
    'PPN (Rupiah)', 'PPnBM (Rupiah)', 'Kode dan Nomor Seri Faktur Pajak yang Diganti/Diretur'];
$col = 'A';
foreach ($head as $h) { $sh->setCellValue($col . '1', $h); $col++; }

$r = 2;
while ($row = mysqli_fetch_assoc($q)) {
    $tgl = (!empty($row['tgl_faktur']) && $row['tgl_faktur'] !== '0000-00-00') ? $row['tgl_faktur'] : '';
    $vals = [$row['bulan'], $row['jenis'], $row['nama_penjual'], $row['npwp'], $row['no_faktur'], $tgl,
        (float) $row['dpp'], (float) $row['dpp_nilai_lain'], (float) $row['ppn'], (float) $row['ppnbm'],
        $row['faktur_diganti'] !== '' ? $row['faktur_diganti'] : '-'];
    $c = 'A';
    foreach ($vals as $i => $v) {
        if (is_float($v)) { $sh->setCellValue($c . $r, $v); }
        else { $sh->setCellValueExplicit($c . $r, (string) $v, DataType::TYPE_STRING); }
        $c++;
    }
    $r++;
}
$lastRow = $r - 1;

$sh->getStyle('A1:K1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
$sh->getStyle('A1:K1')->getFont()->setBold(true);
if ($lastRow >= 2) {
    $sh->getStyle('G2:J' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00_);(#,##0.00)');
}
foreach (range('A', 'K') as $c) { $sh->getColumnDimension($c)->setWidth(22); }
$sh->freezePane('A2');

$fname = 'ppn_masukan_' . preg_replace('/[^A-Za-z0-9]+/', '_', $no_mj) . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $fname . '"');
header('Cache-Control: max-age=0');

(new Xls($ss))->save('php://output');
