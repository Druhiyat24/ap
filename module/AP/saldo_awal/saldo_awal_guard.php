<?php
// ============================================================================
// Penjaga akses menu SET OPENING BALANCE (saldo awal PPN Masukan).
//
// Menu ini mengubah SALDO AWAL sub ledger, jadi hanya boleh dipakai user
// tertentu. Daftar user ditaruh di SATU tempat ini supaya tombol di halaman,
// endpoint upload, dan endpoint save memakai daftar yang sama (tidak bisa
// ditembus dgn memanggil endpoint langsung).
//
// Pembandingan HURUF BESAR/KECIL diabaikan, tapi harus SAMA PERSIS — jadi
// 'willy_herdiansyah' TIDAK ikut dapat akses walau namanya berawalan 'willy'.
// ============================================================================
if (!function_exists('ppn_sa_allowed_users')) {

function ppn_sa_allowed_users() {
    return array('indro', 'willy');
}

/** true kalau username boleh membuka menu saldo awal. */
function ppn_sa_can($username) {
    $u = strtolower(trim((string) $username));
    if ($u === '') { return false; }
    foreach (ppn_sa_allowed_users() as $a) {
        if ($u === strtolower($a)) { return true; }
    }
    return false;
}

/** Dipakai di endpoint: hentikan request kalau tidak berhak (balasan JSON). */
function ppn_sa_guard_json($username) {
    if (ppn_sa_can($username)) { return; }
    if (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'status'  => 'error',
        'message' => 'You are not authorized to access Opening Balance.',
    ));
    exit;
}

}
