<?php
/* Kategori arus kas untuk PV EXIM.
   Form EXIM tidak punya pilihan kategori: "For Payment"-nya kotak readonly
   bertuliskan "Export - Import", jadi kategorinya cuma satu kemungkinan.
   Sampai 18 Nov 2026 kolom id_cash_flow memang TIDAK PERNAH diisi di jalur
   EXIM - 249 PV jatuh ke baris "Pengeluaran Lain-lain" di Cash Flow Report,
   bukan ke baris Export & Import. */

if (!defined('PV_EXIM_CF_BAWAAN')) {
    /* Pengeluaran Pembayaran Biaya Export & Import (OPERATING ACTIVITIES). */
    define('PV_EXIM_CF_BAWAAN', 34);
}

if (!function_exists('pv_exim_cash_flow')) {
    /* Idnya DICARI DI MASTER dulu supaya tidak jadi angka ajaib yang diam-diam
       salah kalau master dibangun ulang. Kalau barisnya hilang atau
       dinonaktifkan, dipakai id bawaan di atas - lebih baik kategori bawaan
       daripada NULL, karena NULL itu persis cacat yang sedang diperbaiki. */
    function pv_exim_cash_flow($conn)
    {
        $q = mysqli_query($conn, "select id from master_cash_flow
                                   where show_subcategory = 'EXPORT & IMPORT'
                                     and type_cashflow = 'Cash Out'
                                     and status = 'Y'
                                   order by id asc limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        return $r ? (int) $r['id'] : PV_EXIM_CF_BAWAAN;
    }
}
