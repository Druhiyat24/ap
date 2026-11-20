<?php
/* ============================================================================
   HISTORY PROJECTION - POST & DUA TINGKAT APPROVAL.

   Dipakai bersama layar potretnya (cf_history_detail.php) dan endpoint yang
   menjalankan aksinya (simpan_cf_history_appr.php). Urutan langkah, nama
   hak aksesnya, dan tulisan statusnya ditulis DI SATU TEMPAT: layar dan
   server harus mustahil berbeda pendapat soal "sekarang boleh apa".

   TIDAK meng-include conn.php - koneksinya dikirim sebagai argumen, supaya
   berkas ini aman di-require dari mana pun.
   ============================================================================ */

if (!defined('CFA_PERAN_POST')) {
    define('CFA_PERAN_POST',  'Bank Projection History Post');
    define('CFA_PERAN_APPR1', 'Bank Projection History Approve 1');
    define('CFA_PERAN_APPR2', 'Bank Projection History Approve 2');
}

if (!function_exists('cfa_langkah')) {
    /**
     * Seluruh alurnya, berkunci STATUS SAAT INI.
     *   teks    - yang dibaca pemakai
     *   aksi    - nama aksi yang dikirim layar ke server
     *   tombol  - tulisan di tombolnya
     *   peran   - hak akses yang boleh menekannya
     *   berikut - status sesudah aksi itu berhasil
     *   kolom   - sepasang kolom jejak (siapa & kapan) yang DIISI aksi ini
     *   ikon    - ikon di depan tulisan statusnya
     *   nada    - kelas warna: draft / tunggu / selesai
     *   jejak   - dari mana keterangan "oleh siapa & kapan" dibaca untuk
     *             status INI: array(kolom_oleh, kolom_waktu, kata depan)
     */
    function cfa_langkah()
    {
        return array(
            'DRAFT' => array(
                'teks' => 'Draft - Waiting to post',
                'aksi' => 'post', 'tombol' => 'Post', 'peran' => CFA_PERAN_POST,
                'berikut' => 'POSTED', 'kolom' => array('post_by', 'post_date'),
                'ikon' => 'fa-pencil-square-o', 'nada' => 'draft',
                /* Belum ada jejak alur sama sekali, jadi yang ditampilkan
                   siapa yang MENYIMPAN potretnya - itu jejak terakhir yang
                   memang sudah ada. */
                'jejak' => array('created_by', 'created_date', 'saved by'),
            ),
            'POSTED' => array(
                'teks' => 'Waiting for First Approval',
                'aksi' => 'appr1', 'tombol' => 'First Approval', 'peran' => CFA_PERAN_APPR1,
                'berikut' => 'APPROVED1', 'kolom' => array('appr1_by', 'appr1_date'),
                'ikon' => 'fa-clock-o', 'nada' => 'tunggu',
                'jejak' => array('post_by', 'post_date', 'posted by'),
            ),
            'APPROVED1' => array(
                'teks' => 'Waiting for Second Approval',
                'aksi' => 'appr2', 'tombol' => 'Second Approval', 'peran' => CFA_PERAN_APPR2,
                'berikut' => 'APPROVED2', 'kolom' => array('appr2_by', 'appr2_date'),
                'ikon' => 'fa-clock-o', 'nada' => 'tunggu',
                'jejak' => array('appr1_by', 'appr1_date', 'first approval by'),
            ),
            /* Langkah terakhir: tidak ada aksi lagi, cuma tulisannya. */
            'APPROVED2' => array(
                'teks' => 'Second Approval',
                'aksi' => '', 'tombol' => '', 'peran' => '',
                'berikut' => '', 'kolom' => array(),
                'ikon' => 'fa-check-circle', 'nada' => 'selesai',
                'jejak' => array('appr2_by', 'appr2_date', 'second approval by'),
            ),
        );
    }
}

if (!function_exists('cfa_status')) {
    /** Status yang dikenal; apa pun yang lain dianggap DRAFT - dokumen lama
        maupun nilai yang entah dari mana tetap punya tempat di alur ini. */
    function cfa_status($v)
    {
        $v = strtoupper(trim((string) $v));
        $tahu = cfa_langkah();
        return isset($tahu[$v]) ? $v : 'DRAFT';
    }

    function cfa_teks($v)
    {
        $l = cfa_langkah();
        return $l[cfa_status($v)]['teks'];
    }
}

if (!function_exists('cfa_punya_peran')) {
    /**
     * Hak akses dibaca dari useraccess + menurole - pola yang sama dgn layar
     * approval lain. Nama perannya yang dicocokkan, BUKAN id-nya: id baru
     * lahir sendiri waktu migrasi dijalankan dan tidak harus sama antara
     * basis data lokal dan produksi.
     */
    function cfa_punya_peran($conn, $user, $peran)
    {
        $user  = trim((string) $user);
        $peran = trim((string) $peran);
        if ($user === '' || $peran === '') { return false; }
        $u = mysqli_real_escape_string($conn, $user);
        $p = mysqli_real_escape_string($conn, $peran);
        $q = mysqli_query($conn, "select 1
                                    from useraccess a
                                   inner join menurole m on m.menu = a.menu
                                   where a.username = '$u' and a.menu = '$p' limit 1");
        return (bool) ($q && mysqli_num_rows($q));
    }
}

if (!function_exists('cfa_keadaan')) {
    /**
     * Keadaan satu dokumen untuk layar: status, tulisannya, dan - kalau
     * pemakainya memang berhak - aksi apa yang boleh dia lakukan sekarang.
     *
     * Dokumen yang DIBATALKAN tidak punya aksi apa pun: membatalkan sesuatu
     * lalu masih bisa menyetujuinya hanya akan membingungkan pembaca jejaknya.
     */
    function cfa_keadaan($conn, $h, $user)
    {
        $st = cfa_status(isset($h['appr_status']) ? $h['appr_status'] : '');
        $l  = cfa_langkah();
        $batal = (isset($h['status']) && strcasecmp((string) $h['status'], 'CANCEL') === 0);

        $keluar = array(
            'status' => $st,
            'teks'   => $l[$st]['teks'],
            'aksi'   => '',
            'tombol' => '',
            'batal'  => $batal ? 1 : 0,
            'ikon'   => $l[$st]['ikon'],
            'nada'   => $l[$st]['nada'],
            'jejak'  => cfa_jejak($h, $l[$st]['jejak']),
        );
        if ($batal || $l[$st]['aksi'] === '') { return $keluar; }
        if (!cfa_punya_peran($conn, $user, $l[$st]['peran'])) { return $keluar; }

        $keluar['aksi']   = $l[$st]['aksi'];
        $keluar['tombol'] = $l[$st]['tombol'];
        return $keluar;
    }
}


if (!function_exists('cfa_jejak')) {
    /**
     * Keterangan "oleh siapa & kapan" untuk status yang sedang berlaku.
     * Kosong kalau kolomnya memang belum terisi - lebih baik tidak ada
     * keterangan daripada keterangan yang isinya tanda tanya.
     */
    function cfa_jejak($h, $def)
    {
        if (!$def || count($def) < 3) { return ''; }
        list($kolOleh, $kolWaktu, $kata) = $def;
        $oleh  = isset($h[$kolOleh])  ? trim((string) $h[$kolOleh])  : '';
        $waktu = isset($h[$kolWaktu]) ? trim((string) $h[$kolWaktu]) : '';
        if ($oleh === '' && $waktu === '') { return ''; }

        $teks = $kata;
        if ($oleh !== '')  { $teks .= ' <b>' . htmlspecialchars($oleh, ENT_QUOTES) . '</b>'; }
        if ($waktu !== '') {
            $t = strtotime($waktu);
            if ($t !== false) { $teks .= ' &middot; ' . date('d M Y H:i', $t); }
        }
        return $teks;
    }
}
