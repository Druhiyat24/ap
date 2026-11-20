<?php
/* ============================================================================
   UNGGAH BARIS untuk Form Projection Other (Bank > Projection > Other).
   Diminta pemakai 19 Nov 2026: ketiga tabnya baru bisa diisi satu per satu;
   sekarang ada template Excel + tombol unggah, barisnya turun ke daftar
   "Rows to be saved", dan Save-nya tetap menghasilkan SATU dokumen.

   SATU BERKAS UNTUK TEMPLATE DAN UNGGAHAN. Susunan kolomnya ditulis sekali
   di cfp_unggah_kolom(): yang membuat template dan yang membaca unggahan
   memakai daftar yang SAMA, jadi mustahil template-nya punya kolom yang
   tidak dikenali pembacanya.

   YANG DIMINTA DARI PEMAKAI HANYA YANG TIDAK BISA DIHITUNG. Mata uang,
   kurs, nilai IDR/USD, profit center, dan Intra/Inter PC semuanya
   DITURUNKAN di sini dgn rumus yang sama persis dgn layarnya - kalau ikut
   diminta, pemakai bisa mengetik kurs yang berbeda dari kurs pajak hari itu
   dan angkanya jadi tidak bisa dipertanggungjawabkan.
   ============================================================================ */

include_once __DIR__ . '/cfp_lib.php';

if (!function_exists('cfp_unggah_kolom')) {
    /** Susunan kolom template per jenis tab. Urutannya = urutan di Excel. */
    function cfp_unggah_kolom($jenis)
    {
        if ($jenis === 'oi') {
            return array('Received Date', 'Customer Name', 'Income Category',
                         'Bank Account', 'Amount', 'Description');
        }
        if ($jenis === 'pv') {
            return array('Payment Date', 'Supplier Name', 'Cost Category',
                         'Bank Account', 'Amount', 'Description');
        }
        return array('Received Date', 'Source Account', 'Destination Account',
                     'Amount', 'Description');
    }

    function cfp_unggah_judul($jenis)
    {
        if ($jenis === 'oi') { return 'Other Inflow'; }
        if ($jenis === 'pv') { return 'PV Expense'; }
        return 'Book Transfer';
    }
}

if (!function_exists('cfp_unggah_tanggal')) {
    /** Tanggal dari sel Excel -> 'd M Y' (mis. "19 Nov 2026"), bentuk yang
     *  dipakai layarnya. Sel bisa berupa TANGGAL ASLI Excel (angka) atau
     *  teks; dua-duanya diterima, yang tidak terbaca dikembalikan ''. */
    function cfp_unggah_tanggal($sel)
    {
        if ($sel === null || $sel === '') { return ''; }

        if (is_numeric($sel)) {
            /* Angka seri Excel. 25569 = 1 Jan 1970; 86400 detik sehari. */
            $ts = ((float) $sel - 25569) * 86400;
            if ($ts > 0) { return date('d M Y', (int) $ts); }
        }
        $ts = strtotime(str_replace('/', '-', trim((string) $sel)));
        return $ts ? date('d M Y', $ts) : '';
    }
}

if (!function_exists('cfp_unggah_angka')) {
    /** Angka dari sel Excel. Menerima "1.234.567,89" (Indonesia),
     *  "1,234,567.89" (Inggris), maupun angka biasa. */
    function cfp_unggah_angka($sel)
    {
        if (is_numeric($sel)) { return (float) $sel; }
        $t = trim((string) $sel);
        if ($t === '') { return 0.0; }

        $koma = strrpos($t, ',');
        $titik = strrpos($t, '.');
        if ($koma !== false && $koma > $titik) {
            /* koma di paling kanan = pemisah desimal gaya Indonesia */
            $t = str_replace('.', '', $t);
            $t = str_replace(',', '.', $t);
        } else {
            $t = str_replace(',', '', $t);
        }
        $t = preg_replace('/[^0-9.\-]/', '', $t);
        return is_numeric($t) ? (float) $t : 0.0;
    }
}

if (!function_exists('cfp_unggah_cocok')) {
    /** Cocokkan teks dgn salah satu pilihan sah. Tidak peduli huruf besar
     *  kecil maupun spasi berlebih - pemakai menyalin dari lembar Reference,
     *  dan satu spasi tambahan tidak pantas menggagalkan satu baris. */
    function cfp_unggah_cocok($teks, $pilihan)
    {
        $cari = strtoupper(preg_replace('/\s+/', ' ', trim((string) $teks)));
        if ($cari === '') { return null; }
        foreach ($pilihan as $p) {
            if (strtoupper(preg_replace('/\s+/', ' ', trim($p))) === $cari) { return $p; }
        }
        return null;
    }
}
