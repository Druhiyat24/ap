<?php
/* SAKLAR TAMPILAN
   TAMPILAN_BARU  true  = tampilan baru NDS (login, navbar, dashboard, tema semua halaman)
                  false = tampilan lama sebelum restyle Nov 2026 ("balikan ke semua")
   TEMA_SELECT2   true  = semua <select> memakai select2 (hanya berlaku kalau TAMPILAN_BARU true)
                  false = select dibiarkan seperti aslinya, tema warna tetap jalan
   TEMA_INTERAKSI true  = alert() jadi kotak SweetAlert, loading + kunci tombol saat proses,
                          warna tombol lama diseragamkan, ikon di judul halaman lama
                          (hanya kalau TAMPILAN_BARU true)
                  false = matikan ketiganya saja
   Menu, hak akses & fitur TIDAK ikut berubah. */
if (!defined('TAMPILAN_BARU')) {
    define('TAMPILAN_BARU', true);
}
if (!defined('TEMA_SELECT2')) {
    define('TEMA_SELECT2', true);
}
if (!defined('TEMA_INTERAKSI')) {
    define('TEMA_INTERAKSI', true);
}
