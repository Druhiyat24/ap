<?php
// ============================================================================
// ppn_masukan_lines.php — SATU sumber kebenaran bentuk jurnal tab "PPN Masukan".
// Dipakai bareng oleh preview (ajx_get_data_ppn.php) & simpan (save_mj_ppn.php)
// supaya angka yang dilihat user PERSIS sama dengan yang tersimpan.
//
// Aturan (sesuai permintaan user):
//   - Nilai jurnal = kolom PPN (Rupiah) dari file.
//   - DEBIT  2.52.03 PAJAK YANG MASIH HARUS DIBAYAR - PPN  -> DIGABUNG 1 BARIS
//     (total seluruh faktur). Dulu rinci per baris faktur; digabung atas permintaan
//     user. Kalau ternyata ada lebih dari satu Profit Center, penggabungannya per
//     Profit Center supaya total per PC tetap benar (dgn PC dari dropdown header,
//     yang berlaku ke semua baris, hasilnya tetap PERSIS 1 baris).
//   - CREDIT 1.52.04 PAJAK DIBAYAR DIMUKA PPN MASUKAN      -> TIDAK BERUBAH:
//     tetap SUM per (supplier + no faktur), satu baris per faktur.
//   - Baris retur (PPN negatif) DIBALIK SISI-nya: yang harusnya debit jadi credit
//     dan sebaliknya, supaya tidak ada angka minus di jurnal.
//   - Profit Center DIAMBIL DARI FILE (kolom terakhir template), per baris faktur.
//     Satu upload boleh memuat beberapa profit center; dropdown PC di header sudah
//     dihapus. Karena itu baris gabungan 2.52.03 jadi SATU per Profit Center.
//   - Description jurnal dibentuk OTOMATIS: "PPN - <SUPPLIER>" huruf besar (field
//     Description di form dihapus atas permintaan user).
// Karena grup memecah-habis baris detail, Sum(debit) = Sum(credit) (jurnal balance).
// Mata uang selalu IDR (file dalam Rupiah) -> rate 1, *_idr = nominal asli.
// ============================================================================
const PPN_COA_DEBIT  = '2.52.03';   // Pajak yang masih harus dibayar - PPN
const PPN_COA_CREDIT = '1.52.04';   // Pajak dibayar dimuka PPN Masukan

if (!function_exists('ppn_build_lines')) {
function ppn_build_lines($conn2, $user, $status = 'Temp', $no_mj = null) {
    $e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
    $where = "create_by = '" . $e($user) . "' AND status = '" . $e($status) . "'";
    if ($no_mj !== null) { $where = "no_mj = '" . $e($no_mj) . "'"; }

    // Nama COA diambil dari master supaya konsisten dgn jurnal lain.
    $nm = [];
    foreach ([PPN_COA_DEBIT, PPN_COA_CREDIT] as $c) {
        $r = mysqli_fetch_assoc(mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . $e($c) . "' limit 1"));
        $nm[$c] = $r['nama_coa'] ?? '';
    }

    // --- DEBIT 2.52.03: DIGABUNG jadi satu baris -----------------------------
    // Nilainya NETTO seluruh faktur (retur yang minus ikut mengurangi). Kalau netto
    // negatif, barisnya pindah ke sisi credit — sama seperti perlakuan retur di sisi
    // 1.52.04, jadi tidak ada angka minus di jurnal.
    // Balance tetap terjaga: netto total = (Σ grup positif − Σ grup negatif), yaitu
    // persis selisih kedua sisi grup 1.52.04.
    // Dikelompokkan per Profit Center; kalau PC dioverride dari dropdown header,
    // dikelompokkan ke satu nilai konstan sehingga hasilnya tepat 1 baris.
    $grpPc  = "COALESCE(profit_center, '')";
    $detail = [];
    $q = mysqli_query($conn2, "select $grpPc pc, SUM(ppn) ppn, SUM(dpp) dpp, COUNT(*) n
        from tbl_ppn_masukan_upload where $where
        group by $grpPc order by $grpPc");
    if ($q === false) { return false; }
    while ($r = mysqli_fetch_assoc($q)) {
        $ppn = (float) $r['ppn'];
        $detail[] = [
            'id'            => 0,
            'no_coa'        => PPN_COA_DEBIT,
            'nama_coa'      => $nm[PPN_COA_DEBIT],
            'profit_center' => $r['pc'],
            'supplier'      => '-',
            'keterangan'    => 'PPN MASUKAN - ' . (int) $r['n'] . ' FAKTUR',
            'npwp'          => '',
            'no_faktur'     => '-',
            'tgl_faktur'    => '',      // baris gabungan -> tanpa reff date
            'dpp'           => (float) $r['dpp'],
            'n_baris'       => (int) $r['n'],
            'debit'         => $ppn >= 0 ? $ppn : 0.0,
            'credit'        => $ppn <  0 ? -$ppn : 0.0,
        ];
    }

    // --- CREDIT 1.52.04: SUM per (profit center + supplier + no faktur) ------
    $grouped = [];
    $qg = mysqli_query($conn2, "select profit_center, nama_penjual, no_faktur, MAX(tgl_faktur) tgl_faktur,
            SUM(ppn) ppn, COUNT(*) n
        from tbl_ppn_masukan_upload where $where
        group by profit_center, nama_penjual, no_faktur
        order by profit_center, nama_penjual, no_faktur");
    if ($qg === false) { return false; }
    while ($r = mysqli_fetch_assoc($qg)) {
        $s = (float) $r['ppn'];
        $grouped[] = [
            'no_coa'        => PPN_COA_CREDIT,
            'nama_coa'      => $nm[PPN_COA_CREDIT],
            'profit_center' => $r['profit_center'],
            'supplier'      => $r['nama_penjual'],
            'keterangan'    => strtoupper('PPN - ' . $r['nama_penjual']),
            'no_faktur'     => $r['no_faktur'],
            'tgl_faktur'    => $r['tgl_faktur'],
            'n_baris'       => (int) $r['n'],
            'debit'         => $s <  0 ? -$s : 0.0,
            'credit'        => $s >= 0 ? $s : 0.0,
        ];
    }

    // --- Data faktur MENTAH (utk tabel "Preview Faktur" di form) -------------
    // Apa adanya seperti isi file yang diupload — tidak dipakai untuk jurnal,
    // hanya supaya user bisa mencocokkan hasil baca file dgn file aslinya.
    $faktur = [];
    $qf = mysqli_query($conn2, "select bulan, jenis, nama_penjual, npwp, no_faktur, tgl_faktur,
            dpp, dpp_nilai_lain, ppn, ppnbm, faktur_diganti
        from tbl_ppn_masukan_upload where $where order by nama_penjual, no_faktur, id");
    if ($qf === false) { return false; }
    while ($r = mysqli_fetch_assoc($qf)) {
        $r['dpp']            = (float) $r['dpp'];
        $r['dpp_nilai_lain'] = (float) $r['dpp_nilai_lain'];
        $r['ppn']            = (float) $r['ppn'];
        $r['ppnbm']          = (float) $r['ppnbm'];
        $faktur[] = $r;
    }

    // --- Urutan tampil & simpan ----------------------------------------------
    // Sisi DEBIT 2.52.03 sekarang cuma 1 baris gabungan, jadi tidak ada lagi
    // pasangan berdampingan: baris gabungan ditaruh DULU, disusul seluruh baris
    // CREDIT 1.52.04 per faktur. 'grp' cuma dipakai UI utk garis pemisah, maka
    // cukup 2 grup (0 = baris gabungan, 1 = blok rincian) supaya garisnya satu
    // saja, bukan tiap baris.
    $lines = [];
    foreach ($detail as $d)  { $d['grp'] = 0; $lines[] = $d; }
    foreach ($grouped as $g) { $g['grp'] = 1; $lines[] = $g; }

    // --- Total: per Profit Center + grand total ------------------------------
    $perPc = [];
    $td = 0.0; $tc = 0.0;
    foreach (array_merge($detail, $grouped) as $r) {
        $pc = $r['profit_center'] !== '' ? $r['profit_center'] : '-';
        if (!isset($perPc[$pc])) { $perPc[$pc] = ['profit_center' => $pc, 'debit' => 0.0, 'credit' => 0.0]; }
        $perPc[$pc]['debit']  += $r['debit'];
        $perPc[$pc]['credit'] += $r['credit'];
        $td += $r['debit']; $tc += $r['credit'];
    }
    foreach ($perPc as $k => $v) {
        $perPc[$k]['debit']  = round($v['debit'], 2);
        $perPc[$k]['credit'] = round($v['credit'], 2);
    }
    ksort($perPc);

    return ['detail' => $detail, 'grouped' => $grouped, 'lines' => $lines, 'faktur' => $faktur,
            'per_pc' => array_values($perPc),
            'tot_debit' => round($td, 2), 'tot_credit' => round($tc, 2),
            'balance' => round($td - $tc, 2) == 0];
}
}
