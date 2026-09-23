<?php
/**
 * Daftar supplier yang "To Account"-nya diisi MANUAL.
 * ---------------------------------------------------------------------------
 * Sebagian supplier (kantor pajak, bea cukai, dsb) tidak punya rekening tetap
 * di master_supplier_bank - yang diisi di kolom To Account bukan nomor
 * rekening, melainkan KODE BILLING yang berganti-ganti tiap dokumen. Untuk
 * supplier semacam ini form PV menampilkan isian bebas (bukan dropdown
 * rekening), dan halaman cetak harus menampilkan isian itu apa adanya - bukan
 * mencoba mencocokkannya ke master bank (kalau dipaksa join, hasilnya gagal
 * dan kolom To Account tampil KOSONG).
 *
 * Dulu daftarnya HARDCODE dan disalin ke 6 berkas (4 form + 2 cetak). Begitu
 * ada supplier baru yang perlu perlakuan sama, salinannya gampang ketinggalan
 * - persis yang terjadi pada "KANTOR PELAYANAN UTAMA BEA DAN CUKAI TIPE A":
 * sudah terdaftar di keempat form, tapi lupa ditambahkan di
 * pdf_payment_list.php, sehingga To Account-nya kosong di PDF Payment List.
 *
 * Sekarang daftarnya DATA, bukan kode: kolom
 * `mastersupplier.to_account_manual` (1 = manual, 0 = pakai dropdown
 * rekening). Menambah supplier baru cukup mengubah datanya, tanpa deploy.
 * Lihat add_to_account_manual.sql untuk ALTER + pengisian awalnya.
 */

if (!defined('TOCC_MANUAL_COLUMN')) {
    define('TOCC_MANUAL_COLUMN', 'to_account_manual');
}

/**
 * Nilai cadangan yang dipakai HANYA bila kolomnya belum ada di database
 * (mis. kode sudah ter-deploy tapi ALTER-nya belum dijalankan). Isinya sama
 * persis dengan daftar hardcode yang lama, jadi perilaku aplikasi tidak
 * berubah sedikit pun sampai migrasinya dijalankan.
 */
function toccManualSuppliersFallback()
{
    return [
        'KANTOR PAJAK',
        'KPPBC TMP A BANDUNG',
        'KANTOR PELAYANAN UTAMA BEA DAN CUKAI TIPE A',
    ];
}

/**
 * Daftar nama supplier (UPPERCASE, sudah di-trim) yang To Account-nya manual.
 *
 * @param mixed $conns Satu koneksi mysqli, atau array koneksi. Bila diberi
 *                     lebih dari satu, hasilnya DIGABUNG - pemanggilnya tidak
 *                     perlu tahu $conn1 atau $conn2 yang memegang master
 *                     supplier (di sebagian berkas cuma salah satu yang ada,
 *                     dan blok koneksi di conn/conn.php sering ditukar).
 * @return string[]
 */
function toccManualSuppliers($conns)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    if (!is_array($conns)) {
        $conns = [$conns];
    }

    $names = [];
    $kolomAda = false;

    foreach ($conns as $conn) {
        if (!$conn) {
            continue;
        }

        // Dicek eksplisit - bukan mengandalkan query gagal - supaya kolom yang
        // memang belum ada bisa dibedakan dari kolom yang ada tapi belum ada
        // satu pun supplier yang ditandai.
        $cek = @mysqli_query($conn, "SHOW COLUMNS FROM mastersupplier LIKE '" . TOCC_MANUAL_COLUMN . "'");
        if (!$cek || !mysqli_num_rows($cek)) {
            continue;
        }
        $kolomAda = true;

        $q = @mysqli_query($conn, "SELECT UPPER(TRIM(Supplier)) nama FROM mastersupplier
            WHERE " . TOCC_MANUAL_COLUMN . " = 1 AND TRIM(IFNULL(Supplier,'')) != ''");
        if (!$q) {
            continue;
        }
        while ($r = mysqli_fetch_assoc($q)) {
            $names[$r['nama']] = true;
        }
    }

    // Kolomnya belum ada di mana pun -> pakai daftar lama supaya tidak ada
    // supplier yang tiba-tiba kehilangan mode manualnya.
    $cache = $kolomAda ? array_keys($names) : toccManualSuppliersFallback();
    sort($cache);

    return $cache;
}

/**
 * Apakah To Account supplier ini diisi manual?
 *
 * @param mixed  $conns Sama seperti toccManualSuppliers().
 * @param string $nama  Nama supplier apa adanya (boleh belum di-trim/upper).
 * @return bool
 */
function isToccManualSupplier($conns, $nama)
{
    return in_array(strtoupper(trim((string) $nama)), toccManualSuppliers($conns), true);
}
