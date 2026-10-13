<?php
// ============================================================================
// PENJAGA PASANGAN PROFIT CENTER <-> COST CENTER
//
// Masalahnya (dilaporkan pemakai 13 Okt 2026): jurnal ber-profit-center NAK
// bisa memakai cost center milik NAG, dan sebaliknya. Di produksi ada 212
// baris / 118 dokumen di tbl_list_journal yang begitu.
//
// Dropdown-nya sendiri SUDAH benar - cc.php maupun getCostCenter.php sama-sama
// menyaring b_master_cc dgn id_pc + grup COA. Yang tidak ada adalah pemeriksaan
// ULANG di sisi server: tiap endpoint simpan cuma meng-query b_master_cc untuk
// mengambil cc_name (tanpa syarat id_pc), lalu menulis apa pun yang dikirim
// browser. Akibatnya semua jalan yang melewati atau mendahului dropdown lolos:
// form edit yang memuat nilai lama, profit center diubah SESUDAH cost center
// dipilih, balasan AJAX yang basi, atau POST langsung.
//
// Berkas ini menyediakan pemeriksaan itu supaya tiap endpoint tinggal memanggil.
//
// YANG DITOLAK HANYA yang BISA DIBUKTIKAN salah:
//   - cost center kosong / '-'            -> DILEWATI (banyak baris jurnal
//                                            memang tidak bercost-center)
//   - profit center kosong / '-'          -> DILEWATI (tidak ada pembanding)
//   - cost center tidak ada di b_master_cc-> DILEWATI (tidak bisa disimpulkan;
//                                            di produksi ada 180 baris begini)
//   - cost center ADA, tapi id_pc-nya beda-> DITOLAK
// Sikap ini sengaja: penjaga tidak boleh menghentikan pekerjaan yang sah hanya
// karena masternya belum lengkap.
// ============================================================================

/**
 * Cari profit center pemilik sebuah cost center.
 *
 * @return string kode profit center (mis. 'NAG'), atau '' kalau cost center
 *                tidak dikenal di b_master_cc.
 */
function ccpc_pemilik($conn, $no_cc)
{
    static $ingat = array();   // satu cost center cukup dicari sekali per request

    $cc = trim((string) $no_cc);
    if ($cc === '' || $cc === '-') { return ''; }
    if (array_key_exists($cc, $ingat)) { return $ingat[$cc]; }

    $ccEsc = mysqli_real_escape_string($conn, $cc);
    // MAX(id_pc): satu no_cc bisa punya beberapa baris (Active + Deactive),
    // tapi diperiksa 13 Okt 2026 semuanya menunjuk profit center yang SAMA -
    // tidak ada no_cc yang dimiliki dua profit center.
    $r = mysqli_query($conn, "select MAX(id_pc) id_pc from b_master_cc where no_cc = '$ccEsc'");
    $row = ($r ? mysqli_fetch_assoc($r) : null);
    $ingat[$cc] = ($row && $row['id_pc'] !== null) ? trim($row['id_pc']) : '';

    return $ingat[$cc];
}

/**
 * Periksa SATU pasangan.
 *
 * @return string '' kalau sah, atau kalimat galat kalau cost center-nya milik
 *                profit center lain.
 */
function ccpc_cek($conn, $no_cc, $profit_center)
{
    $cc = trim((string) $no_cc);
    $pc = strtoupper(trim((string) $profit_center));

    if ($cc === '' || $cc === '-') { return ''; }
    if ($pc === '' || $pc === '-') { return ''; }

    $pemilik = ccpc_pemilik($conn, $cc);
    if ($pemilik === '') { return ''; }          // tidak dikenal -> tidak disimpulkan
    if (strtoupper($pemilik) === $pc) { return ''; }

    return 'Cost Center ' . $cc . ' milik Profit Center ' . $pemilik
         . ', bukan ' . $pc . '.';
}

/**
 * Periksa BANYAK pasangan sekaligus.
 *
 * @param array $baris daftar array('no_cc' => ..., 'profit_center' => ...)
 * @return string '' kalau semua sah, atau kalimat galat PERTAMA yang ketemu.
 *                Disebutkan nomor barisnya supaya pemakai tahu mana yang salah.
 */
function ccpc_cek_baris($conn, array $baris)
{
    foreach ($baris as $i => $b) {
        $cc = isset($b['no_cc']) ? $b['no_cc'] : '';
        $pc = isset($b['profit_center']) ? $b['profit_center'] : '';
        $pesan = ccpc_cek($conn, $cc, $pc);
        if ($pesan !== '') {
            return 'Baris ' . ($i + 1) . ': ' . $pesan;
        }
    }
    return '';
}
