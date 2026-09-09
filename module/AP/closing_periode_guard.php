<?php
// ============================================================================
// closing_periode_guard.php — batas tanggal transaksi berdasar CLOSING PERIODE.
//
// Sumber: tbl_closing_periode (kode_closing, tgl_awal, tgl_akhir, status_closing).
// Periode ber-status selain 'Open' berarti buku sudah ditutup: tidak boleh lagi
// ada jurnal masuk ke tanggal tersebut.
//
// Dipakai di DUA sisi supaya tidak bisa ditembus:
//   1. Tampilan  -> closing_min_date() jadi startDate datepicker (tanggal lama
//                   tidak bisa diklik sama sekali).
//   2. Server    -> closing_check() dipanggil di endpoint save SEBELUM menulis.
//      Pembatasan yang cuma di UI gampang dilewati (kirim POST langsung /
//      ubah value lewat console), jadi validasi server WAJIB ada.
// ============================================================================
if (!function_exists('closing_min_date')) {

/** Tanggal paling awal yang masih boleh dipakai = awal periode Open pertama. */
function closing_min_date($conn) {
    $q = mysqli_query($conn, "SELECT MIN(tgl_awal) t FROM tbl_closing_periode WHERE status_closing = 'Open'");
    $r = $q ? mysqli_fetch_assoc($q) : null;
    return (!empty($r['t']) && $r['t'] != '0000-00-00') ? $r['t'] : null;
}

/**
 * Periksa satu tanggal (Y-m-d) terhadap closing periode.
 * Return: ['ok' => bool, 'message' => string, 'min' => Y-m-d|null]
 */
function closing_check($conn, $ymd) {
    $min = closing_min_date($conn);
    $out = ['ok' => true, 'message' => '', 'min' => $min];

    if (empty($ymd) || $ymd === '0000-00-00') {
        return ['ok' => false, 'message' => 'Journal date is empty.', 'min' => $min];
    }
    $e = mysqli_real_escape_string($conn, $ymd);

    // 1) lebih tua dari periode Open pertama -> pasti sudah closing
    if ($min !== null && $ymd < $min) {
        $out['ok'] = false;
        $out['message'] = 'The period of ' . date('d M Y', strtotime($ymd)) . ' is already closed. '
                        . 'The earliest date allowed is ' . date('d M Y', strtotime($min)) . '.';
        return $out;
    }

    // 2) periode yang memuat tanggal ini statusnya bukan Open (mis. bulan
    //    tengah yang ditutup ulang) -> tetap ditolak
    $q = mysqli_query($conn, "SELECT kode_closing, status_closing FROM tbl_closing_periode
        WHERE '$e' BETWEEN tgl_awal AND tgl_akhir ORDER BY id LIMIT 1");
    $p = $q ? mysqli_fetch_assoc($q) : null;
    if ($p && strtolower($p['status_closing']) !== 'open') {
        $out['ok'] = false;
        $out['message'] = 'Period ' . $p['kode_closing'] . ' (' . date('M Y', strtotime($ymd)) . ') is '
                        . $p['status_closing'] . '. Journal cannot be posted to a closed period.';
    }
    return $out;
}

/** Versi ringkas utk endpoint JSON: balikan pesan error, atau '' kalau lolos. */
function closing_error($conn, $ymd) {
    $c = closing_check($conn, $ymd);
    return $c['ok'] ? '' : $c['message'];
}

}
