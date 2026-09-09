<?php
// ============================================================================
// mj_rate_helper.php — penentu kurs untuk seluruh jalur simpan Memorial Journal
// ============================================================================
// ATURAN (permintaan user): kurs SELALU diambil dari TANGGAL JURNAL memakai
// kurs PAJAK (ap_masterrate.v_codecurr = 'PAJAK') untuk MATA UANG BARIS ITU
// SENDIRI — kecuali kurs sudah disertakan (dari berkas upload atau diketik
// manual), yang mana nilai itulah yang dipakai apa adanya.
//
// KENAPA HELPER, BUKAN DISALIN LAGI:
//   - proses_upload.php sudah melakukan hal ini dengan benar sejak awal.
//   - save_mj_input.php memakai SATU kurs untuk seluruh baris, hasil lookup
//     getRate() yang HANYA mencari USD. Baris ber-mata-uang lain (mis. EUR)
//     ikut dikalikan kurs USD.
//   - save_mj_hris.php membaca $_POST['rate_mj2'] lalu TIDAK memakainya sama
//     sekali: INSERT-nya menulis rate '1' dan debit_idr = debit secara harfiah.
//   Ketiganya kini memakai fungsi yang sama supaya tidak melenceng lagi.
//
// CATATAN IDR: baris IDR SELALU rate 1 dan nilai IDR = nominal aslinya. Tanpa
// pengunci ini, baris IDR ikut dikalikan kurs dan nilainya meledak.
// ============================================================================

if (!function_exists('mj_resolve_rate')) {

    /**
     * @param mysqli $conn      koneksi (dipakai untuk ap_masterrate)
     * @param string $curr      mata uang baris, mis. 'IDR' / 'USD'
     * @param string $mj_date   tanggal jurnal 'Y-m-d'
     * @param mixed  $override  kurs yang sudah ada (dari berkas upload / ketik
     *                          manual). Dipakai HANYA kalau berupa angka > 0.
     * @return float
     */
    function mj_resolve_rate($conn, $curr, $mj_date, $override = null)
    {
        $curr = strtoupper(trim((string) $curr));

        // 1. IDR tidak pernah dikonversi.
        if ($curr === '' || $curr === 'IDR') {
            return 1.0;
        }

        // 2. Kurs yang sudah disertakan menang — tapi hanya kalau benar-benar
        //    angka positif. String kosong / '0' / teks diabaikan, karena itulah
        //    yang selama ini diam-diam membuat nilai IDR jadi 0.
        if ($override !== null) {
            $o = str_replace(',', '', trim((string) $override));
            if ($o !== '' && is_numeric($o) && (float) $o > 0) {
                return (float) $o;
            }
        }

        // 3. Lookup kurs PAJAK pada TANGGAL JURNAL, untuk mata uang baris ini.
        //    Di-cache per (curr, tanggal): satu jurnal bisa berisi ratusan baris
        //    dengan mata uang yang sama, tidak perlu query berulang.
        static $cache = [];
        $key = $curr . '|' . $mj_date;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $c = mysqli_real_escape_string($conn, $curr);
        $d = mysqli_real_escape_string($conn, $mj_date);
        $q = mysqli_query($conn, "SELECT rate FROM ap_masterrate
             WHERE curr = '$c' AND tanggal = '$d' AND v_codecurr = 'PAJAK' LIMIT 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;

        // Tidak ketemu -> 1. Sengaja TIDAK melempar error supaya penyimpanan
        // tidak gagal total; nilai asing tersimpan apa adanya dan selisihnya
        // terlihat saat rekonsiliasi, bukan hilang jadi 0.
        $rate = ($r && is_numeric($r['rate']) && (float) $r['rate'] > 0) ? (float) $r['rate'] : 1.0;

        $cache[$key] = $rate;
        return $rate;
    }
}
