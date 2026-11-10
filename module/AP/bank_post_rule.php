<?php
// ============================================================================
// bank_post_rule.php — dokumen Bank In / Bank Out yang TIDAK perlu approval.
//
// Status dokumen bank: Draft -> Approved (lewat menu Approval) atau Cancel.
// Sejak Nov 2026 ada 'Post' = langsung final tanpa approval:
//   Bank In  - AR Collection, Bank Keluar : selalu Post.
//            - None : Post kalau SEMUA COA detailnya ber-flag
//                     mastercoa_v2.bankin_no_approve = 'Y'.
//   Bank Out - Payment Voucher : Post kalau tidak ada baris "add row"
//                     (b_bankout_adj_det).
//            - None : Post kalau SEMUA COA detailnya ber-flag
//                     mastercoa_v2.bankout_no_approve = 'Y'.
//            - List Payment : selalu lewat approval.
// Daftar COA sengaja disimpan di mastercoa_v2 (diatur dari menu Master COA),
// bukan ditulis di kode.
//
// Aturan dibaca dari baris yang SUDAH tersimpan, jadi dipanggil sesudah semua
// insert dan sebelum commit, di transaksi yang sama.
//
// Dokumen Post boleh diedit atau di-cancel selama periodenya masih Open
// (tbl_closing_periode) dan belum dipakai dokumen lain. Draft yang diedit tetap
// Draft (tetap harus di-approve), Post yang sesudah diedit tidak lagi memenuhi
// aturan turun ke Draft.
// ============================================================================
require_once __DIR__ . '/closing_periode_guard.php';

if (!function_exists('bank_tanpa_approval')) {

function bank_q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

/** [tabel header, kolom nomor, kolom tanggal, kolom tipe] per arah 'in' / 'out'. */
function bank_doc_header($arah) {
    return $arah === 'in'
        ? ['tbl_bankin_arcollection', 'doc_num', 'date', 'ref_data']
        : ['b_bankout_h', 'no_bankout', 'bankout_date', 'reff_doc'];
}

/** True kalau SEMUA COA di baris detail ber-flag $flag = 'Y'. */
function bank_coa_semua_bebas($conn, $tabel, $kolomCoa, $kolomDoc, $docEsc, $flag) {
    // Dua query terpisah (bukan JOIN) supaya tidak kena beda collation antar tabel.
    $coas = [];
    $q = bank_q($conn, "SELECT DISTINCT $kolomCoa c FROM $tabel WHERE $kolomDoc = '$docEsc'");
    while ($r = mysqli_fetch_assoc($q)) {
        $coas[] = mysqli_real_escape_string($conn, $r['c']);
    }
    if (!$coas) {
        return false;
    }
    $q = bank_q($conn, "SELECT COUNT(DISTINCT no_coa) n FROM mastercoa_v2
        WHERE no_coa IN ('" . implode("','", $coas) . "') AND $flag = 'Y'");
    return (int) mysqli_fetch_assoc($q)['n'] === count($coas);
}

/** True kalau dokumen ini boleh langsung Post tanpa approval. */
function bank_tanpa_approval($conn, $arah, $doc) {
    list($tbl, $kDoc, , $kTipe) = bank_doc_header($arah);
    $e = mysqli_real_escape_string($conn, $doc);
    $h = mysqli_fetch_assoc(bank_q($conn, "SELECT $kTipe tipe FROM $tbl WHERE $kDoc = '$e' LIMIT 1"));
    $tipe = $h['tipe'] ?? '';

    if ($arah === 'in') {
        if ($tipe === 'AR Collection' || $tipe === 'Bank Keluar') {
            return true;
        }
        if ($tipe === 'None') {
            return bank_coa_semua_bebas($conn, 'tbl_bankin', 'id_coa', 'no_doc', $e, 'bankin_no_approve');
        }
        return false;
    }

    if ($tipe === 'Payment Voucher') {
        $r = mysqli_fetch_assoc(bank_q($conn, "SELECT COUNT(*) n FROM b_bankout_adj_det WHERE no_bankout = '$e'"));
        return (int) $r['n'] === 0;
    }
    if ($tipe === 'None') {
        return bank_coa_semua_bebas($conn, 'b_bankout_none', 'no_coa', 'no_bankout', $e, 'bankout_no_approve');
    }
    return false;
}

/**
 * Samakan status header + b_reportbank + jurnal: Draft -> Post kalau $post,
 * Post -> Draft kalau tidak. Baris Approved/Cancel/Updated tidak tersentuh.
 */
function bank_terapkan_status($conn, $arah, $doc, $post) {
    list($tbl, $kDoc) = bank_doc_header($arah);
    $e = mysqli_real_escape_string($conn, $doc);
    $dari = $post ? 'Draft' : 'Post';
    $ke   = $post ? 'Post' : 'Draft';
    bank_q($conn, "UPDATE $tbl SET status = '$ke' WHERE $kDoc = '$e' AND status = '$dari'");
    bank_q($conn, "UPDATE b_reportbank SET status = '$ke' WHERE no_doc = '$e' AND status = '$dari'");
    bank_q($conn, "UPDATE tbl_list_journal SET status = '$ke' WHERE no_journal = '$e' AND status = '$dari'");
}

/**
 * Pesan penolakan kalau dokumen sudah dipakai dokumen lain, '' kalau belum.
 * Sebelum ada Post hal ini tidak mungkin terjadi pada dokumen yang bisa
 * diedit, karena hanya dokumen Approved yang bisa dialokasikan/dipasangkan.
 */
function bank_dipakai_dokumen_lain($conn, $arah, $doc) {
    $e = mysqli_real_escape_string($conn, $doc);
    if ($arah === 'in') {
        $r = mysqli_fetch_assoc(bank_q($conn, "SELECT no_alk FROM tbl_alokasi
            WHERE doc_number = '$e' AND UPPER(IFNULL(status, '')) <> 'CANCEL' LIMIT 1"));
        return $r ? "Dokumen $doc sudah dialokasikan di AR ({$r['no_alk']}), tidak bisa diedit atau di-cancel." : '';
    }
    $q = bank_q($conn, "SELECT DISTINCT no_bankin FROM b_bankin_none WHERE no_reff = '$e'");
    while ($r = mysqli_fetch_assoc($q)) {
        $bi = mysqli_real_escape_string($conn, $r['no_bankin']);
        $h = mysqli_fetch_assoc(bank_q($conn, "SELECT doc_num FROM tbl_bankin_arcollection
            WHERE doc_num = '$bi' AND status <> 'Cancel' LIMIT 1"));
        if ($h) {
            return "Dokumen $doc sudah dipakai Bank In {$h['doc_num']}, tidak bisa diedit atau di-cancel.";
        }
    }
    return '';
}

/**
 * Penjaga endpoint edit: hanya Draft & Post, periode tanggal lama DAN tanggal
 * baru (kalau ada) harus masih Open, dan dokumen belum dipakai dokumen lain.
 * Lempar Exception kalau ditolak; kembalikan status lama ('Draft' / 'Post').
 */
function bank_cek_boleh_edit($conn, $arah, $doc, $tgl_baru = null) {
    list($tbl, $kDoc, $kTgl) = bank_doc_header($arah);
    $e = mysqli_real_escape_string($conn, $doc);
    $h = mysqli_fetch_assoc(bank_q($conn, "SELECT status, $kTgl tgl FROM $tbl WHERE $kDoc = '$e' LIMIT 1 FOR UPDATE"));
    if (!$h) {
        throw new Exception("Dokumen $doc tidak ditemukan.");
    }
    $status = ucfirst(strtolower(trim($h['status'])));
    if ($status !== 'Draft' && $status !== 'Post') {
        throw new Exception("Dokumen $doc berstatus {$h['status']}, tidak bisa diedit.");
    }
    $tanggal = [date('Y-m-d', strtotime($h['tgl']))];
    if ($tgl_baru !== null) {
        $tanggal[] = $tgl_baru;
    }
    foreach (array_unique($tanggal) as $tgl) {
        $c = closing_check($conn, $tgl);
        if (!$c['ok']) {
            throw new Exception($c['message']);
        }
    }
    $pakai = bank_dipakai_dokumen_lain($conn, $arah, $doc);
    if ($pakai !== '') {
        throw new Exception($pakai);
    }
    return $status;
}

/**
 * Penjaga endpoint cancel. Draft tetap bebas dibatalkan seperti dulu (antrean
 * Approval juga menolak Draft lewat endpoint yang sama); Post hanya kalau
 * aturan Edit terpenuhi. Approved/Cancel ditolak - dokumen Approved dibuka
 * lewat Reverse Bank.
 */
function bank_cek_boleh_cancel($conn, $arah, $doc) {
    list($tbl, $kDoc) = bank_doc_header($arah);
    $e = mysqli_real_escape_string($conn, $doc);
    $h = mysqli_fetch_assoc(bank_q($conn, "SELECT status FROM $tbl WHERE $kDoc = '$e' LIMIT 1"));
    if (!$h) {
        throw new Exception("Dokumen $doc tidak ditemukan.");
    }
    $status = ucfirst(strtolower(trim($h['status'])));
    if ($status === 'Post') {
        bank_cek_boleh_edit($conn, $arah, $doc);
    } elseif ($status !== 'Draft') {
        throw new Exception("Dokumen $doc berstatus {$h['status']}, tidak bisa di-cancel.");
    }
}

}
