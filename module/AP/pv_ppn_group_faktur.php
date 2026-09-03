<?php
// ============================================================================
// pv_ppn_group_faktur.php
//
// Pecah baris jurnal PPN Masukan (BILLED) HEADER sebuah PV — yang semula 1 baris
// konsolidasi TANPA nomor faktur (debit = tax_h, reff_doc '-') — menjadi 1 baris
// PER NOMOR FAKTUR, masing-masing debit = Σ PPN semua BPB dgn faktur itu.
//
// Sumber nilai per faktur: baris per-BPB PPN "UNBILLED" (mis. 1.52.07) sisi CREDIT
// yang SUDAH punya faktur_pajak (di-set saat simpan per-BPB). Jadi helper WAJIB
// dipanggil SETELAH semua item PV tersimpan.
//
// BALANCE: total baris baru DIPAKSA sama dgn total baris header lama; selisih
// pembulatan (Σ per-BPB idr vs tax_h*rate) ditempel ke grup faktur TERBESAR, jadi
// jurnal tetap balance PERSIS (Σdebit_idr tidak berubah).
//
// TIDAK disentuh: baris RO (PPN billed/unbilled dgn reff_doc = no_bppb, karena
// filter reff_doc '-'/kosong) & baris per-BPB unbilled itu sendiri.
//
// Dipanggil DI DALAM transaksi (create: insertkbon_bulk.php, edit: insertkbon_bulk_edit.php).
// Hanya menyentuh type_journal = 'AP - Kontrabon' (baris 'Reverse ...' aman).
// Return: true = sukses / tak ada yg perlu diproses; false = query gagal (caller rollback).
// ============================================================================
if (!function_exists('pv_group_ppn_billed_by_faktur')) {
function pv_group_ppn_billed_by_faktur($conn2, $kode) {
    $e  = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };
    $ke = $e($kode);

    // Filter baris PPN billed HEADER. Baris ini = SATU-SATUNYA baris billed-PPN sisi
    // DEBIT dalam 1 PV (per-BPB memakai akun UNBILLED; RO billed selalu di sisi CREDIT
    // dgn reff = no_bppb). Terbukti di data: max billed-debit per PV = 1. Jadi cukup
    // filter billed + debit>0, TANPA syarat reff_doc — aman walau No Faktur header terisi.
    $whereHdr = "no_journal = '$ke' AND type_journal = 'AP - Kontrabon'
        AND nama_coa LIKE '%PPN MASUKAN%' AND nama_coa NOT LIKE '%UNBILLED%'
        AND debit_idr > 0";

    // 1) Total header lama (jadi TARGET total baris baru) + template kolom statis.
    $tq = mysqli_query($conn2, "SELECT SUM(debit) sdeb, SUM(debit_idr) sdeb_idr FROM tbl_list_journal WHERE $whereHdr");
    if ($tq === false) return false;
    $T = mysqli_fetch_assoc($tq);
    if (!$T || $T['sdeb_idr'] === null) return true;   // PV tanpa PPN billed header -> tak ada yg diproses
    $tgt_deb     = (float) $T['sdeb'];
    $tgt_deb_idr = (float) $T['sdeb_idr'];

    $tplq = mysqli_query($conn2, "SELECT no_coa, nama_coa, no_costcenter, nama_costcenter, curr, rate,
        status, keterangan, create_by, create_date, profit_center FROM tbl_list_journal WHERE $whereHdr LIMIT 1");
    if ($tplq === false) return false;
    $H = mysqli_fetch_assoc($tplq);
    if (!$H) return true;

    // 2) Grup PPN per-BPB (unbilled, sisi CREDIT) per faktur_pajak, terbesar dulu.
    $gq = mysqli_query($conn2, "SELECT COALESCE(faktur_pajak, '') fp, MAX(tgl_faktur_pajak) tfp,
            MAX(curr) curr, MAX(rate) rate, SUM(credit) scr, SUM(credit_idr) scr_idr
        FROM tbl_list_journal
        WHERE no_journal = '$ke' AND type_journal = 'AP - Kontrabon'
          AND nama_coa LIKE '%UNBILLED%' AND credit_idr > 0
        GROUP BY COALESCE(faktur_pajak, '')
        ORDER BY SUM(credit_idr) DESC");
    if ($gq === false) return false;
    $groups = [];
    while ($g = mysqli_fetch_assoc($gq)) { $groups[] = $g; }
    if (!$groups) return true;   // tak ada per-BPB PPN -> biarkan header apa adanya

    // 3) Tempel selisih pembulatan ke grup terbesar (idx 0) supaya Σ = header lama.
    $sum_deb = 0.0; $sum_deb_idr = 0.0;
    foreach ($groups as $g) { $sum_deb += (float) $g['scr']; $sum_deb_idr += (float) $g['scr_idr']; }
    $groups[0]['scr']     = (float) $groups[0]['scr']     + ($tgt_deb     - $sum_deb);
    $groups[0]['scr_idr'] = (float) $groups[0]['scr_idr'] + ($tgt_deb_idr - $sum_deb_idr);

    // 4) Hapus baris PPN billed HEADER lama (RO reff=no_bppb tetap).
    if (mysqli_query($conn2, "DELETE FROM tbl_list_journal WHERE $whereHdr") === false) return false;

    // 5) Insert 1 baris PPN billed debit per faktur (faktur_pajak & tgl terisi).
    foreach ($groups as $g) {
        $fp   = $e($g['fp']);
        $tfp  = (!empty($g['tfp']) && $g['tfp'] != '0000-00-00') ? "'" . $e($g['tfp']) . "'" : "NULL";
        $curr = $e(($g['curr'] !== null && $g['curr'] !== '') ? $g['curr'] : $H['curr']);
        $rate = $e(((float) $g['rate']) > 0 ? $g['rate'] : $H['rate']);
        $deb     = number_format((float) $g['scr'], 4, '.', '');
        $deb_idr = number_format((float) $g['scr_idr'], 4, '.', '');
        $q = "INSERT INTO tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
             reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
             debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
             approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES ('$ke', '" . $e($H['create_date']) . "', 'AP - Kontrabon', '" . $e($H['no_coa']) . "', '" . $e($H['nama_coa']) . "',
             '" . $e($H['no_costcenter']) . "', '" . $e($H['nama_costcenter']) . "',
             '-', '', '$fp', $tfp, '-', '-', '$curr', '$rate',
             '$deb', '0', '$deb_idr', '0', '" . $e($H['status']) . "', '" . $e($H['keterangan']) . "',
             '" . $e($H['create_by']) . "', '" . $e($H['create_date']) . "', '', '', '', '', '" . $e($H['profit_center']) . "')";
        if (mysqli_query($conn2, $q) === false) return false;
    }
    return true;
}
}
