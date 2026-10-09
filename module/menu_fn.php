<?php
/* ============================================================================
   module/menu_fn.php - pembantu penulisan navbar, dipakai oleh module/menu.php.

   Di menu lama ada tiga tempat yang memeriksa hak akses dgn cara "cocok
   persis" pada hasil GROUP_CONCAT(menurole.id), lalu MENDAFTAR SETIAP
   KOMBINASI satu per satu:

       {41,42,43}     ->  7 cabang if/elseif   (2^3 - 1)
       {44,45,46,47}  -> 15 cabang if/elseif   (2^4 - 1)
       {86,87}        ->  3 cabang if/elseif   (2^2 - 1)

   Total 25 cabang hanya untuk memilih 9 butir menu. Tiap kombinasi menuliskan
   ulang markup butirnya, jadi satu butir bisa tertulis 8 kali di berkas yang
   sama - itulah sumber "banyak if" yang membuat menu sulit diubah.

   Dua kelemahannya, bukan cuma panjang:

     1. Kalau nanti ada baris menurole BARU yang ikut tersaring query-nya
        (mis. menu bernama "Bank - Approval ..." yang lain), hasil
        GROUP_CONCAT jadi mis. "41,99" - TIDAK ADA cabang yang cocok, dan
        SELURUH submenu Approval hilang tanpa pesan apa pun. Pemakai cuma
        melihat submenu kosong.
     2. Menambah satu butir berarti menulis ulang seluruh daftar kombinasi
        (dari 15 cabang jadi 31).

   Pemeriksaan per butir di bawah bebas dari keduanya. Untuk data yang ada
   sekarang hasilnya SAMA PERSIS: sudah diperiksa bahwa id yang mungkin
   muncul dari ketiga query itu tepat sama dgn anggota keluarganya, sehingga
   ke-25 cabang tadi memang mencakup semua kemungkinan.

   Catatan: 46 tempat lain di menu ini sudah memakai gaya strpos() yang
   setara dgn pemeriksaan per butir - jadi ini menyeragamkan, bukan
   memperkenalkan gaya baru.
   ============================================================================ */

/* Apakah pemakai memegang menurole id ini?
   $id = hasil GROUP_CONCAT(menurole.id ORDER BY id), mis. "44,46,47".
   Dipisah per koma supaya "4" tidak dianggap cocok dgn "41" atau "14"
   (jebakan yang ada pada strpos polos). */
function ubf_punya($id, $cari)
{
    foreach (explode(',', (string) $id) as $satu) {
        if (trim($satu) === (string) $cari) {
            return true;
        }
    }
    return false;
}

/* Gambar butir-butir menu yang hak aksesnya dimiliki pemakai.

     $butir : array( menurole_id => array($berkas, $label, $ikon, $badge) )
              $ikon  boleh dikosongkan -> memakai $ikonBaku
              $badge boleh dikosongkan -> tanpa lencana notifikasi
     $sela  : indentasi tiap baris, disamakan dgn markup di sekitarnya
     urutan tampil = urutan penulisan $butir (BUKAN urutan id) */
function ubf_butir($id, array $butir, $sela = '      ', $ikonBaku = 'fa fa-thumbs-up fa-fw')
{
    $keluar = '';
    foreach ($butir as $idButir => $b) {
        if (!ubf_punya($id, $idButir)) {
            continue;
        }
        $ikon  = (isset($b[2]) && $b[2] !== '') ? $b[2] : $ikonBaku;
        $badge = isset($b[3]) ? $b[3] : '';

        $keluar .= "\n" . $sela . '<a href="../AP/' . $b[0] . '" class="dropdown-item bg-dark text-white">'
                 . "\n" . $sela . '<span class="' . $ikon . '"></span>'
                 . "\n" . $sela . '<span class="menu-collapsed">' . $b[1] . '</span>'
                 . ($badge !== '' ? "\n" . $sela . $badge : '')
                 . "\n" . $sela . '</a>';
    }
    if ($keluar !== '') {
        $keluar .= ' ';
    }
    echo $keluar;
}
