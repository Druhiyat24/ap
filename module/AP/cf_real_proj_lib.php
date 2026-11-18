<?php
/* ============================================================================
   KOLOM PROJECTION & VARIANCE untuk Report Cash Flow Realisation.

   Angka proyeksinya TIDAK dihitung ulang: dibaca dari POTRET yang disimpan
   menu History Projection (ap_cf_history_h/_det), kolom TOTAL-nya. Itu memang
   inti potret - angka yang dilihat & disetujui saat disimpan harus sama persis
   waktu dibandingkan dgn realisasinya, berapa kali pun dibuka lagi dan apa pun
   yang berubah di data sumbernya sesudah itu.

   Keduanya bisa dicocokkan karena susunan barisnya berasal dari master yang
   SAMA (master_cash_flow): mesin proyeksi dan laporan realisasi dua-duanya
   membacanya dari sana, jadi nama kategorinya identik. Yang dipakai sbg kunci:
   ARAH (in/out) + GRUP (operating/investing/financing) + nama kategori - bukan
   namanya saja, dan bukan id (potret tidak menyimpan id kategori).

   TIDAK meng-include conn.php: koneksinya dikirim sbg argumen. Lihat catatan
   yang sama di cf_akun_lib.php.
   ============================================================================ */

/* Kunci teks: huruf besar, spasi ganda dirapikan, titik dua di ujung dibuang.
   Potret menulis grupnya "OPERATING ACTIVITIES:" sedangkan laporan realisasi
   "OPERATING ACTIVITIES" - beda satu titik dua saja sudah tidak ketemu. */
function cfrp_kunci($v)
{
    $v = preg_replace('/\s+/', ' ', trim((string) $v));
    return strtoupper(rtrim($v, ": \t"));
}

/* ----------------------------------------------------------------------------
   Kepala potret - periode, PC/Bank, type, status. Dipisah karena BUKAN cuma
   dipakai membandingkan: laporan realisasi yang dibuka dari tombol Realization
   MENGAMBIL periode & daftar akunnya dari sini, jadi layarnya tidak perlu
   saringan tanggal/akun lagi. Kembaliannya null kalau nomornya tidak ada.
   ---------------------------------------------------------------------------- */
function cfrp_header($conn, $doc)
{
    $doc = trim((string) $doc);
    if ($doc === '') { return null; }
    $e = mysqli_real_escape_string($conn, $doc);
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "select doc_number, periode_dari, periode_sampai, profit_center, bank, type, status
           from ap_cf_history_h where doc_number = '$e' limit 1"));
    return $r ? $r : null;
}

/* ----------------------------------------------------------------------------
   Baca kolom TOTAL satu potret jadi peta siap-pakai.

   Barisnya dibaca BERURUTAN (urut), bukan dicari satu-satu: arah dan grup
   sebuah baris item ditentukan baris blok/grup di ATASNYA - itulah satu-satunya
   penanda yang dipunyai potret. Jadi urutannya memang data, bukan kosmetik.

   Blok pinjaman: baris IDR membawa nilainya sendiri, baris USD nilainya NULL
   dan rinciannya ada di dua anak baris - yang dipakai anak "Dalam Mata Uang
   Rupiah" (kolom laporan realisasi rupiah semua), yang "Dalam Mata Uang USD"
   dilewati. Keduanya DIJUMLAH ke satu kunci, karena satu baris di laporan
   realisasi mewakili semua rekening pinjaman sekaligus.
   ---------------------------------------------------------------------------- */
function cfrp_peta_potret($conn, $doc)
{
    $e = mysqli_real_escape_string($conn, $doc);
    $q = mysqli_query($conn, "select blok, urut, tipe, label, nilai
                                from ap_cf_history_det
                               where doc_number = '$e' and tanggal is null and kolom is null
                            order by blok desc, urut asc");

    $peta = array();
    $arah = '';   /* IN / OUT / NET */
    $grup = '';
    $pinj = '';   /* label baris pinjaman yang sedang berjalan */

    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $label = cfrp_kunci($r['label']);
        $nilai = ($r['nilai'] === null) ? null : (float) $r['nilai'];

        if ($r['blok'] === 'loan') {
            if ($r['tipe'] === 'pinj') {
                /* "SALDO AWAL PINJAMAN BANK IDR"/"... USD" jadi satu baris di
                   laporan realisasi, jadi penanda mata uangnya dibuang. */
                $pinj = trim(preg_replace('/\s+(IDR|USD)$/', '', $label));
                if ($nilai !== null) {
                    $k = 'LOAN|' . $pinj;
                    $peta[$k] = (isset($peta[$k]) ? $peta[$k] : 0) + $nilai;
                }
            } elseif ($r['tipe'] === 'sub' && $pinj !== '' && $nilai !== null
                      && strpos($label, 'RUPIAH') !== false) {
                $k = 'LOAN|' . $pinj;
                $peta[$k] = (isset($peta[$k]) ? $peta[$k] : 0) + $nilai;
            }
            continue;
        }

        switch ($r['tipe']) {
            case 'sal':
                /* Dua baris saldo - pembuka & penutup - dibedakan dari namanya
                   sendiri, bukan dari "yang pertama/terakhir", supaya tidak
                   ikut salah kalau nanti ada baris saldo lain. */
                if (strpos($label, 'OPENING') !== false) {
                    $peta['OPEN'] = $nilai;
                } elseif (strpos($label, 'CLOSING') !== false) {
                    $peta['CLOSE'] = $nilai;
                }
                break;
            case 'blok':
                if (strpos($label, 'INFLOW') !== false) {
                    $arah = 'IN';
                } elseif (strpos($label, 'OUTFLOW') !== false) {
                    $arah = 'OUT';
                } elseif (strpos($label, 'NET') !== false) {
                    $arah = 'NET';
                }
                $grup = '';
                break;
            case 'grup':
                $grup = $label;
                break;
            case 'item':
                if ($arah !== '' && $grup !== '') {
                    $peta[$arah . '|' . $grup . '|' . $label] = $nilai;
                }
                break;
            case 'tot':
                if ($arah !== '' && $grup !== '') {
                    $peta['TOT|' . $arah . '|' . $grup] = $nilai;
                }
                break;
            case 'tot2':
                if ($arah !== '') {
                    $peta['TOT2|' . $arah] = $nilai;
                }
                break;
            case 'net':
                $peta['NET|' . $label] = $nilai;
                break;
        }
    }
    return $peta;
}

/* ----------------------------------------------------------------------------
   Siapkan kolom Projection untuk satu tampilan laporan realisasi.

   Proyeksinya hanya dipakai kalau PERIODENYA SAMA. Potret menyimpan kolom
   TOTAL untuk periodenya sendiri; begitu periode laporan diganti, angka itu
   bukan pembanding yang sah lagi - lebih baik kolomnya kosong daripada
   menampilkan perbandingan yang kelihatan benar tapi bukan.

   Saringan akun yang berbeda TIDAK menghentikan kolomnya (totalnya tetap total
   potret), tapi dicatat di 'catatan' supaya terlihat di layar.
   ---------------------------------------------------------------------------- */
function cf_proyeksi_realisasi($conn, $doc, $mulai, $akhir, $akunLaporan = array())
{
    $h = array('pakai' => false, 'doc' => '', 'info' => '', 'catatan' => '', 'peta' => array());
    $doc = trim((string) $doc);
    if ($doc === '') { return $h; }
    $h['doc'] = $doc;

    $r = cfrp_header($conn, $doc);
    if (!$r) {
        $h['catatan'] = 'Projection column is empty: snapshot ' . $doc . ' was not found.';
        return $h;
    }
    if ($r['status'] === 'CANCEL') {
        $h['catatan'] = 'Projection column is empty: snapshot ' . $doc . ' has been cancelled.';
        return $h;
    }
    if ($r['periode_dari'] !== $mulai || $r['periode_sampai'] !== $akhir) {
        $h['catatan'] = 'Projection column is empty: snapshot ' . $doc . ' covers '
                      . date('d M Y', strtotime($r['periode_dari'])) . ' - '
                      . date('d M Y', strtotime($r['periode_sampai']))
                      . ', which is not the period shown here.';
        return $h;
    }

    $h['peta'] = cfrp_peta_potret($conn, $doc);
    $h['pakai'] = !empty($h['peta']);
    if (!$h['pakai']) {
        $h['catatan'] = 'Projection column is empty: snapshot ' . $doc . ' has no saved figure.';
        return $h;
    }

    $h['info'] = 'PROJECTION FROM ' . $doc . ' (' . $r['profit_center'] . ' / ' . $r['bank'] . ')';

    /* Saringan akun potret vs yang sedang tampil. cf_akun_realisasi()
       mengembalikan array kosong utk "semua akun", jadi dimekarkan dulu -
       dibandingkan sbg himpunan, bukan urutan. */
    include_once __DIR__ . '/cf_akun_lib.php';
    $akunPotret = cf_akun_realisasi($conn, $r['profit_center'], $r['bank']);
    if (empty($akunPotret)) {
        foreach (cfrGetAllAccounts($conn) as $a) { $akunPotret[] = $a['account']; }
    }
    $a1 = array_map('strval', $akunPotret);
    $a2 = array_map('strval', $akunLaporan);
    sort($a1);
    sort($a2);
    if ($a1 !== $a2) {
        $h['catatan'] = 'The account filter differs from snapshot ' . $doc
                      . ', so the Projection column is still that snapshot full total.';
    }
    return $h;
}

/* Satu baris: kembalikan teks sel Projection & Variance. Variance =
   Projection - Realisation. Kunci yang tidak ada di potret berarti potret itu
   memang tidak memproyeksikan apa pun di baris tsb (0), bukan "tidak tahu" -
   dgn begitu kolom Variance tetap menjumlah ke bawah. */
function cfrp_sel($proj, $kunci, $real)
{
    if (empty($proj['pakai'])) { return array('-', '-'); }
    $v = isset($proj['peta'][$kunci]) ? (float) $proj['peta'][$kunci] : 0.0;
    return array(cfrFmt($v), cfrFmt($v - (float) $real));
}

/* Angka mentahnya - dipakai ekspor Excel (sel angka, bukan teks). */
function cfrp_nilai($proj, $kunci)
{
    if (empty($proj['pakai'])) { return null; }
    return isset($proj['peta'][$kunci]) ? (float) $proj['peta'][$kunci] : 0.0;
}
