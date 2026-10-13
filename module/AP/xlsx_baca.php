<?php
// ============================================================================
// PEMBACA SHEET EXCEL YANG TIDAK JEBOL OLEH SEL BER-FORMAT TANGGAL
//
// MASALAHNYA (dilaporkan pemakai 13 Okt 2026, saat upload jurnal GM):
//   Warning : DateTime::modify(): Failed to parse time string
//             (+5.0026002959202E+15 days)
//   Fatal   : Call to a member function setTime() on bool
//             di Shared/Date.php baris 238
//
//   Sebabnya ada sel yang FORMAT ANGKANYA tanggal (mis. yyyy-mm-dd) tapi
//   ISINYA bukan tanggal - khasnya nomor faktur pajak 16-17 digit yang
//   tertempel di kolom ber-format tanggal, atau kolom faktur yang formatnya
//   kebawa tanggal dari templat.
//
//   $sheet->toArray() bawaan PhpSpreadsheet memformat SEMUA sel. Begitu ketemu
//   sel semacam itu, Date::excelToDateTimeObject() menyusun interval
//   "+5.0026002959202E+15 days" - PHP tidak bisa mengurai notasi ilmiah di
//   interval, jadi ->modify() mengembalikan false, lalu ->setTime() pada false
//   memicu PHP Error. Error (bukan Exception), sehingga blok
//   catch (Exception) di pemanggilnya TIDAK menangkapnya dan fatal-nya bocor
//   mentah sbg HTML ke dalam balasan JSON.
//
// CARA KERJA PENGGANTINYA:
//   Sel dibaca satu per satu. Sel ber-format tanggal hanya diformat kalau
//   nilainya masih di dalam jangkauan serial Excel yang sah; kalau di luar itu,
//   nilainya dikembalikan APA ADANYA sbg teks (bukan notasi ilmiah), sehingga
//   nomor faktur tetap terbaca utuh. Sel lain diformat seperti biasa, tetapi
//   tiap pemformatan dibungkus try/catch supaya satu sel aneh tidak pernah lagi
//   menjatuhkan seluruh proses upload.
//
//   Pemeriksaan ISI tetap jadi urusan pemanggil - mis. proses_upload.php sudah
//   menolak baris yang Tanggal Journal-nya tidak terbaca, lengkap dgn nomor
//   barisnya. Berkas ini hanya memastikan prosesnya SAMPAI ke pemeriksaan itu.
// ============================================================================

use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate as XlsCoordinate;

// Serial Excel tertinggi yang masih sah = 9999-12-31. Di atas ini pasti bukan
// tanggal, berapa pun format selnya.
if (!defined('XLSX_SERIAL_MAKS')) { define('XLSX_SERIAL_MAKS', 2958465); }

if (!function_exists('xlsx_baca_sheet')) {
/**
 * Pengganti $sheet->toArray() yang tahan sel ber-format tanggal berisi bukan tanggal.
 *
 * @param  object $sheet     Worksheet PhpSpreadsheet
 * @param  array  $selAneh   (keluaran) daftar sel yang formatnya tanggal tapi
 *                           isinya di luar jangkauan: ['sel' => 'J5', 'nilai' => '...']
 * @return array             array baris, indeks kolom 0-based - sama bentuknya
 *                           dgn toArray() supaya pemanggil tidak perlu berubah.
 */
function xlsx_baca_sheet($sheet, array &$selAneh = array())
{
    $selAneh = array();

    // getHighestDataRow/Column: hanya sel yang BERISI. Sheet templat punya
    // puluhan ribu baris sisa; getHighestRow() akan ikut menghitungnya dan
    // pembacaan jadi lambat sekali.
    $barisAkhir  = $sheet->getHighestDataRow();
    $kolomAkhir  = $sheet->getHighestDataColumn();
    $kolomJumlah = XlsCoordinate::columnIndexFromString($kolomAkhir);

    $hasil = array();

    for ($b = 1; $b <= $barisAkhir; $b++) {
        $baris = array();
        for ($k = 1; $k <= $kolomJumlah; $k++) {
            $cell = $sheet->getCellByColumnAndRow($k, $b);
            $mentah = $cell->getValue();

            if ($mentah === null || $mentah === '') {
                $baris[] = $mentah;
                continue;
            }

            $tanggal = false;
            try { $tanggal = XlsDate::isDateTime($cell); } catch (Throwable $e) { $tanggal = false; }

            if ($tanggal && is_numeric($mentah)
                && ((float) $mentah < 0 || (float) $mentah > XLSX_SERIAL_MAKS)) {
                // Inilah sel yang dulu menjatuhkan prosesnya. Nilainya dipakai
                // apa adanya, dan ditulis dgn %.0f - bukan (string) biasa -
                // supaya angka besar tidak berubah jadi "5.0026E+15" dan nomor
                // faktur tetap terbaca utuh.
                $teks = (floor((float) $mentah) == (float) $mentah)
                    ? sprintf('%.0f', (float) $mentah)
                    : (string) $mentah;
                $selAneh[] = array(
                    'sel'   => XlsCoordinate::stringFromColumnIndex($k) . $b,
                    'nilai' => $teks,
                );
                $baris[] = $teks;
                continue;
            }

            // Sel biasa: diformat seperti toArray(). Dibungkus supaya sel aneh
            // jenis lain pun tidak menjatuhkan seluruh upload.
            try {
                $baris[] = $cell->getFormattedValue();
            } catch (Throwable $e) {
                $baris[] = is_scalar($mentah) ? (string) $mentah : '';
            }
        }
        $hasil[] = $baris;
    }

    return $hasil;
}
}
