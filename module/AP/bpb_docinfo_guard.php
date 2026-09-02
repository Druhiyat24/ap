<?php
// ============================================================================
// bpb_docinfo_guard.php
// Update kolom "dokumen invoice/faktur" (upt_dok_inv / upt_no_inv / upt_tgl_inv /
// upt_no_faktur / upt_tgl_faktur) dari jalur pembuatan/edit Kontrabon-IR & Payment
// Voucher, DENGAN PENGAMAN:
//   Nilai No/Tgl INVOICE atau No/Tgl FAKTUR yang SUDAH terisi (real) TIDAK boleh
//   ditimpa oleh strip "-" / kosong. Strip hanya boleh mengisi kalau nilai lama
//   masih NULL / kosong / "-". Kalau incoming nilai asli (bukan strip) -> menimpa.
//   Invoice & faktur dinilai per-field (independen).
//
// Dipakai untuk 2 tabel: bpb_new (key no_bpb) & bppb_new (key no_bppb, untuk retur).
// $tgl_*_sql = literal SQL siap-tempel ("'YYYY-MM-DD'" / "NULL").
// ============================================================================

if (!function_exists('_docinfo_guard_apply')) {
    function _docinfo_guard_apply($conn, $table, $keyCol, $keyVal, $dok, $no_inv, $tgl_inv_sql, $no_faktur, $tgl_faktur_sql)
    {
        $keyVal = trim((string) $keyVal);
        if ($keyVal === '') return false;

        $e = function ($v) use ($conn) { return mysqli_real_escape_string($conn, (string) $v); };
        $isReal = function ($v) { $v = trim((string) $v); return $v !== '' && $v !== '-'; };
        $k = $e($keyVal);

        // Nilai lama (semua baris utk 1 key selalu sama; ambil 1 wakil).
        $cur = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT upt_no_inv, upt_no_faktur FROM `$table` WHERE `$keyCol` = '$k' LIMIT 1"));
        $curInv = $cur['upt_no_inv'] ?? null;
        $curFak = $cur['upt_no_faktur'] ?? null;

        $sets = ["upt_dok_inv = '" . $e($dok) . "'"];
        // Invoice: tulis kalau incoming real ATAU existing belum real.
        if ($isReal($no_inv) || !$isReal($curInv)) {
            $sets[] = "upt_no_inv = '" . $e($no_inv) . "'";
            $sets[] = "upt_tgl_inv = " . (trim((string) $tgl_inv_sql) !== '' ? $tgl_inv_sql : 'NULL');
        }
        // Faktur: idem.
        if ($isReal($no_faktur) || !$isReal($curFak)) {
            $sets[] = "upt_no_faktur = '" . $e($no_faktur) . "'";
            $sets[] = "upt_tgl_faktur = " . (trim((string) $tgl_faktur_sql) !== '' ? $tgl_faktur_sql : 'NULL');
        }
        return mysqli_query($conn, "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE `$keyCol` = '$k'");
    }
}

// BPB (pembelian) -> bpb_new, key = no_bpb.
if (!function_exists('bpbnew_apply_docinfo')) {
    function bpbnew_apply_docinfo($conn, $no_bpb, $dok, $no_inv, $tgl_inv_sql, $no_faktur, $tgl_faktur_sql)
    {
        return _docinfo_guard_apply($conn, 'bpb_new', 'no_bpb', $no_bpb, $dok, $no_inv, $tgl_inv_sql, $no_faktur, $tgl_faktur_sql);
    }
}

// RETUR -> bppb_new, key = no_bppb (= no_bpbrtn di return_kb).
if (!function_exists('bppbnew_apply_docinfo')) {
    function bppbnew_apply_docinfo($conn, $no_bppb, $dok, $no_inv, $tgl_inv_sql, $no_faktur, $tgl_faktur_sql)
    {
        return _docinfo_guard_apply($conn, 'bppb_new', 'no_bppb', $no_bppb, $dok, $no_inv, $tgl_inv_sql, $no_faktur, $tgl_faktur_sql);
    }
}
