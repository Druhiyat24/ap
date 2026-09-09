<?php
// ============================================================================
// Endpoint DataTables AJAX — SUB LEDGER : PPN MASUKAN.
// TODO: ISI QUERY di sini. Balikan JSON:  { "data": [ [ ...29 kolom... ], ... ] }
// (satu baris = array 29 elemen, urutannya HARUS sama dgn header di
//  ppn_masukan_report.php).
//
// Urutan 29 kolom:
//   0 SI No | 1 SI Date | 2 Supplier | 3 No Faktur Pajak | 4 Profit Center
//   Beginning Balance : 5 Currency  | 6  Amount OCY | 7  Eqv IDR
//   Addition          : 8 Currency  | 9  Amount OCY | 10 Eqv IDR
//   Deduction         : 11 Doc No | 12 Date | 13 Currency | 14 Amount OCY | 15 Eqv IDR
//   Reclassification  : 16 Doc No | 17 Date | 18 Currency | 19 Amount OCY | 20 Eqv IDR
//   Adjustment        : 21 Doc No | 22 Date | 23 Currency | 24 Amount OCY | 25 Eqv IDR
//   Ending Balance    : 26 Currency | 27 Amount OCY | 28 Eqv IDR
//
// Kolom nominal (6,7,9,10,14,15,19,20,24,25,27,28) sudah dibuat rata-kanan di client.
// Filter dari DataTables (POST): nama_supp, start_date, end_date.
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$nama_supp  = $_POST['nama_supp'] ?? 'ALL';
// Batas mundur filter (samakan dgn ppn_masukan_report.php). Periode sebelum tanggal
// ini tidak dilayani lewat jurnal — nanti lewat menu UPLOAD SALDO AWAL.
$PPN_MIN_DATE = '2026-01-01';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date   = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');
if ($start_date < $PPN_MIN_DATE) { $start_date = $PPN_MIN_DATE; }
if ($end_date   < $PPN_MIN_DATE) { $end_date   = $PPN_MIN_DATE; }

$data = [];

// Query-nya ada di berkas terpisah supaya DIPAKAI BERSAMA dgn ekspor Excel
// (ekspor_ppn_masukan.php) — angka di layar & di file dijamin dari SQL yang sama.
require_once __DIR__ . '/ppn_masukan_query.php';
$sql = ppn_report_sql($conn2, $nama_supp, $start_date, $end_date, $PPN_MIN_DATE);

$res = mysqli_query($conn2, $sql);
if ($res) {
    // Deduction di jurnal ada di sisi CREDIT, jadi (debit-credit) negatif. Di kolom
    // report dibalik tandanya supaya tampil POSITIF (lazimnya sub ledger). Baris RETUR
    // sisinya terbalik (jadi debit), hasilnya NEGATIF di kolom Deduction = pembatalan
    // pengurangan. Pakai negasi, BUKAN abs(), supaya
    //     Beginning + Addition - Deduction - Reclassification - Adjustment = Ending.
    // Currency & Amount OCY/Eqv IDR SELALU ditampilkan di semua kolom mutasi
    // (Addition/Deduction/Reclassification/Adjustment), termasuk 0.00 kalau
    // tidak ada mutasi — permintaan user: jangan ada sel kosong. Doc No & Date
    // tetap "-" kalau memang tidak ada dokumennya (bukan angka, jadi "0" tidak
    // relevan).
    $n   = function ($v) { return number_format((float) $v, 2); };
    $nz  = function ($v) { return abs((float) $v) > 0.0000001; };
    $tgl = function ($v) { return (!empty($v) && $v != '0000-00-00') ? date('d-M-Y', strtotime($v)) : ''; };
    // Doc No deduction bisa lebih dari satu (di-concat koma). Tiap nomor dibikin
    // bisa DIKLIK -> modal detail per baris jurnal (ajx_ppn_masukan_detail.php).
    $lnk = function ($csv, $fk) {
        $out = [];
        foreach (explode(',', (string) $csv) as $d) {
            $d = trim($d);
            if ($d === '') { continue; }
            $out[] = '<a href="javascript:void(0)" class="ppn-doc"'
                   . ' data-doc="' . htmlspecialchars($d, ENT_QUOTES) . '"'
                   . ' data-fk="'  . htmlspecialchars((string) $fk, ENT_QUOTES) . '">'
                   . htmlspecialchars($d) . '</a>';
        }
        // Nomor dokumen dipecah DUA per baris: 4 nomor -> 2 baris, 5 -> 3 baris.
        // Pemisah baris pakai <br> (tetap jalan walau sel-nya white-space:nowrap).
        $html = '';
        foreach ($out as $i => $a) {
            if ($i > 0) {
                $html .= ($i % 2 === 0)
                    ? '<span class="ppn-doc-sep">,</span><br>'   // ganti baris tiap 2 nomor
                    : '<span class="ppn-doc-sep">, </span>';
            }
            $html .= $a;
        }
        return $html;
    };

    while ($r = mysqli_fetch_assoc($res)) {
        // Doc No & Date tetap "-" kalau tidak ada mutasi (dokumen memang tidak ada).
        // Currency & Amount OCY/Eqv IDR SELALU ditulis (0.00 kalau tidak ada mutasi) —
        // permintaan user: kolom jangan kosong.
        $adaDed = $nz($r['ded_ocy']) || $nz($r['ded_idr']);
        $adaRcl = $nz($r['rcl_ocy']) || $nz($r['rcl_idr']);
        $adaAdj = $nz($r['adj_ocy']) || $nz($r['adj_idr']);
        $cur    = $r['curr'];

        $data[] = [
            $r['si_no'],                                        // 0  SI No
            $tgl($r['si_date']),                                // 1  SI Date
            ($r['nama_supp'] !== '' && $r['nama_supp'] !== null) ? $r['nama_supp'] : '-', // 2  Supplier
            $r['faktur_pajak'],                                 // 3  No Faktur Pajak
            $r['profit_center'],                                // 4  Profit Center

            $cur,                                               // 5  Beginning Currency
            $n($r['beg_ocy']),                                  // 6  Beginning Amount OCY
            $n($r['beg_idr']),                                  // 7  Beginning Eqv IDR

            $cur,                                               // 8  Addition Currency
            $n($r['add_ocy']),                                  // 9  Addition Amount OCY
            $n($r['add_idr']),                                  // 10 Addition Eqv IDR

            $adaDed ? $lnk($r['ded_no'], $r['faktur_pajak']) : '-', // 11 Deduction Doc No (bisa diklik kalau ada)
            $adaDed ? $tgl($r['ded_date']) : '-',               // 12 Deduction Date
            $cur,                                               // 13 Deduction Currency
            $n(-1 * (float) $r['ded_ocy']),                     // 14 Deduction Amount OCY
            $n(-1 * (float) $r['ded_idr']),                     // 15 Deduction Eqv IDR

            $adaRcl ? $lnk($r['rcl_no'], $r['faktur_pajak']) : '-', // 16 Reclassification Doc No
            $adaRcl ? $tgl($r['rcl_date']) : '-',               // 17 Reclassification Date
            $cur,                                                // 18 Reclassification Currency
            $n(-1 * (float) $r['rcl_ocy']),                     // 19 Reclassification Amount OCY
            $n(-1 * (float) $r['rcl_idr']),                     // 20 Reclassification Eqv IDR

            $adaAdj ? $lnk($r['adj_no'], $r['faktur_pajak']) : '-', // 21 Adjustment Doc No
            $adaAdj ? $tgl($r['adj_date']) : '-',               // 22 Adjustment Date
            $cur,                                                // 23 Adjustment Currency
            $n(-1 * (float) $r['adj_ocy']),                     // 24 Adjustment Amount OCY
            $n(-1 * (float) $r['adj_idr']),                     // 25 Adjustment Eqv IDR

            $cur,                                               // 26 Ending Currency
            $n($r['end_ocy']),                                  // 27 Ending Amount OCY
            $n($r['end_idr']),                                  // 28 Ending Eqv IDR
        ];
    }
}
$out = ['data' => $data];
if (!$res) { $out['db_error'] = mysqli_error($conn2); } // bantu debug kalau query gagal
echo json_encode($out);
