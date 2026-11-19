<?php
/* ============================================================================
   SUMBER ANGKA AP & AR UNTUK CASH FLOW PROJECTION.

   ATURANNYA SATU KALIMAT (ditegaskan pemakai 18 Nov 2026): "untuk proyeksi AR
   dan AP tetap pakai yang sudah di-save history apabila masuk ke rentang cash
   flow, ini hanya untuk yang belum ada save history-nya".

   Jadi untuk KEDUA sisi:
     1. cari POTRET yang periodenya MENYELIMUTI rentang laporan; kalau ada,
        angkanya dibaca dari potret itu - tidak dihitung ulang;
     2. kalau tidak ada, dihitung ULANG dgn mesinnya sendiri sesuai saringan
        Cash Flow - dan TIDAK DISIMPAN. Tidak ada satu pun baris yang ditulis
        ke ap_projection_history_* maupun tbl_history_projection_* dari sini.
        Menyimpan potret tetap wewenang menu AP/AR masing-masing.

   NOMOR DOKUMENNYA DIBAWA KE EXCEL. Lembar AR Existing dan PV AP memuat kolom
   Source Doc: berisi nomor potretnya kalau angkanya dari potret, dan "-"
   kalau dihitung ulang. Tanpa itu, dua berkas Excel yang angkanya berbeda
   tidak bisa dijelaskan bedanya dari mana.
   ============================================================================ */

if (!function_exists('cf_potret_cari')) {
    /* Potret yang periodenya MENYELIMUTI rentang laporan, yang paling baru.
       Sengaja "menyelimuti", bukan "beririsan": potret yang cuma menutup
       separuh periode akan membuat separuh lainnya diam-diam nol. */
    function cf_potret_cari($conn, $tabel, $mulai, $akhir, $adaStatus = true)
    {
        $q = mysqli_query($conn, "show tables like '$tabel'");
        if (!$q || !mysqli_num_rows($q)) { return null; }

        $saringStatus = $adaStatus ? " and (status is null or status <> 'CANCEL')" : '';
        $q = mysqli_query($conn, "select doc_number from `$tabel`
                                   where periode_dari <= '" . mysqli_real_escape_string($conn, $mulai) . "'
                                     and periode_sampai >= '" . mysqli_real_escape_string($conn, $akhir) . "'
                                     $saringStatus
                                   order by id desc limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        return $r ? $r['doc_number'] : null;
    }
}

if (!function_exists('cf_potret_ap')) {
    /**
     * Baris proyeksi AP dari potret, DIBENTUK PERSIS seperti keluaran mesinnya
     * (ajx_ap_projection_report.php) - termasuk kolom harian d_yyyy-mm-dd -
     * supaya yang memakainya tidak perlu tahu angkanya dari potret atau bukan.
     *
     * Di potret, tanggal proyeksinya tersimpan di kolom plan_date (lihat
     * simpan_history_ap_projection.php, yang menulis tgl_proyeksi ke sana),
     * dan nilai kasnya di kolom outstanding.
     *
     * @return array|null  array('doc' => ..., 'baris' => array(...)) atau null
     */
    function cf_potret_ap($conn, $mulai, $akhir)
    {
        $doc = cf_potret_cari($conn, 'ap_projection_history_h', $mulai, $akhir);
        if ($doc === null) { return null; }

        $e = mysqli_real_escape_string($conn, $doc);
        $q = mysqli_query($conn, "select * from ap_projection_history_det
                                   where doc_number = '$e' order by no_urut asc, id asc");
        if (!$q) { return null; }

        $baris = array();
        while ($r = mysqli_fetch_assoc($q)) {
            $tgl = ($r['plan_date'] !== null && $r['plan_date'] !== '0000-00-00')
                 ? date('Y-m-d', strtotime($r['plan_date'])) : '';
            $b = array(
                'supplier'       => (string) $r['supplier'],
                'no_kbon'        => (string) $r['no_kbon'],
                'tgl_kbon'       => (string) $r['tgl_kbon'],
                'term'           => (string) $r['term'],
                'duedate'        => (string) $r['duedate'],
                'plan_date'      => $tgl,
                'tgl_proyeksi'   => $tgl,
                'basis'          => (string) $r['basis'],
                'relasi'         => (string) $r['relasi'],
                'item_type'      => (string) $r['item_type'],
                'flexibility'    => (string) $r['flexibility'],
                'curr'           => (string) $r['curr'],
                'invoice_amount' => (string) $r['invoice_amount'],
                'rate'           => (string) $r['rate'],
                'tax_base'       => (string) $r['tax_base'],
                'vat'            => (string) $r['vat'],
                'total_invoice'  => (string) $r['total_invoice'],
                'pph'            => (string) $r['pph'],
                'payment_amount' => (string) $r['payment_amount'],
                'outstanding'    => (string) $r['outstanding'],
            );
            /* Kolom harian - persis bentuk yang dibaca mesin Cash Flow. */
            if ($tgl !== '') { $b['d_' . $tgl] = (string) $r['outstanding']; }
            $baris[] = $b;
        }
        return array('doc' => $doc, 'baris' => $baris);
    }
}

if (!function_exists('cf_potret_ar_doc')) {
    /* Nomor potret AR yang menyelimuti periode, atau null.
       tbl_history_projection_h tidak punya kolom status - dokumen yang
       dibatalkan DIPINDAH ke tabel _cancel oleh menu AR-nya. */
    function cf_potret_ar_doc($conn, $mulai, $akhir)
    {
        return cf_potret_cari($conn, 'tbl_history_projection_h', $mulai, $akhir, false);
    }
}

if (!function_exists('cf_ar_hitung')) {
    /**
     * Hitung ulang proyeksi AR dgn mesin salinan (cf_ar_engine.php).
     * TIDAK MENYIMPAN apa pun.
     *
     * LAMBAT: di basis data lokal satu panggilan memakan ~66 detik, karena
     * query AR-nya menyapu seluruh invoice sejak 2022. Karena itu hanya
     * dipanggil kalau memang tidak ada potret - jalur normalnya tetap cepat.
     */
    function cf_ar_hitung($conn, $mulai, $akhir, $type = 'daily')
    {
        require_once __DIR__ . '/cf_ar_engine.php';
        $e = new CfArEngine($conn);
        $r = $e->cari_projection_report_export('ALL', $mulai, $akhir, $type);
        return is_array($r) ? $r : array();
    }
}
