<?php
// ============================================================================
// Penyiap query untuk menu STATUS INFORMATION (status.php + ekspor_status.php).
//
// Kenapa dipisah ke berkas ini: query yang sama DULU ditulis ulang 16 kali di
// status.php (8 varian sebagai string untuk link Excel + 8 varian lagi sebagai
// mysqli_query untuk tabel). Menambah satu baris saja harus disalin 16 tempat
// dan gampang meleset antara yang tampil di layar dgn yang keluar di Excel.
//
// Cakupan dokumen:
//   - BPB  (nomor .../IN/... & .../RI/...) - dari tabel `bpb`
//   - BPPB (nomor .../OUT/... & .../RO/...) - dari tabel `bppb_new`
//
// Kenapa BPPB diambil dari `bppb_new`, BUKAN dari `bppb` (sumber gudang):
//   `bppb` berisi SEMUA pengeluaran barang - per Agustus 2026 ada 2.164 dokumen,
//   dan hanya 273 yang lawannya supplier (tipe_sup='S'); sisanya pengiriman ke
//   customer/distributor yang tidak ada urusannya dgn hutang. Dari 273 itu pun
//   cuma 15 yang masuk verifikasi AP. `bppb_new` adalah tabel sisi AP-nya
//   (padanan `bpb_new` untuk BPB), jadi isinya persis dokumen yang memang
//   berjalan di alur ini - tanpa membanjiri laporan dgn ribuan baris kosong.
//
// ---------------------------------------------------------------------------
// KENAPA MEMAKAI TEMPORARY TABLE, bukan satu query panjang berisi subquery
//
// Versi sebelumnya merangkai semuanya sbg tabel turunan (derived table). Di
// MySQL versi ini tabel turunan TIDAK punya index, jadi tiap baris sisi kiri
// harus memindai seluruh isinya. Untuk filter Payment Date akibatnya fatal -
// terukur pada data nyata (Agustus 2026):
//
//   - query lama (sebelum BPPB ditambahkan)  : 2.615 detik lalu koneksi diputus
//   - setelah dokumen sumber dipotong di depan: 4.517 detik (hasil benar, tapi
//     tetap tak terpakai)
//
// Padahal tiap bagiannya sendiri cepat: daftar pelunasan 276 baris (0 detik),
// daftar dokumen relevan 1.012 baris (0,5 detik), dokumen sumber setelah
// dipotong 1.005 baris (0,6 detik). Seluruh waktu habis di perakitan akhir.
//
// Maka tiap tahap kini ditumpahkan ke TEMPORARY TABLE lalu DIBERI INDEX, baru
// dirakit. Temporary table hanya hidup di koneksi yang membuatnya, jadi aman
// dipakai bersamaan oleh banyak pengguna. Pola yang sama sudah dipakai
// approvemj_bulk.php di modul ini.
//
// Dibuat lewat "CREATE TEMPORARY TABLE ... AS SELECT" supaya tipe & COLLATION
// kolomnya mewarisi tabel aslinya - kalau dideklarasikan manual dan collation-
// nya meleset, join-nya justru berhenti memakai index (jebakan yang sudah
// pernah kena di approvemj_bulk.php).
// ============================================================================

/**
 * Jenis dokumen dibaca dari pola nomornya - dipakai bareng oleh tabel di layar
 * (ajx_status.php) dan ekspor Excel (ekspor_status.php) supaya labelnya tidak
 * mungkin berbeda antar keduanya.
 *
 *   IN  = penerimaan barang (BPB)  -> nomor .../IN/... & .../RI/...
 *   OUT = pengeluaran/retur (BPPB) -> nomor .../OUT/... & .../RO/...
 *
 * Catatan: di `bpb` ada pula segelintir nomor berpola .../SCR26xxxx/... (13
 * dokumen sepanjang 2026). Karena asalnya sisi penerimaan, semuanya ikut IN.
 */
function status_jenis_dokumen($no)
{
    $no = (string) $no;
    if (strpos($no, '/OUT/') !== false || strpos($no, '/RO/') !== false) {
        return 'OUT';
    }
    return 'IN';
}

/**
 * Menyiapkan temporary table tahap-demi-tahap, lalu mengembalikan SELECT akhir.
 *
 * PENTING: fungsi ini MENJALANKAN perintah ke database (membuat temp table),
 * bukan sekadar merangkai teks. SELECT yang dikembalikan hanya sah dijalankan
 * di koneksi $conn yang sama.
 *
 * @param string $filter     tgl_bpb | tgl_kbon | tgl_lp | tgl_pay
 * @param string $nama_supp  nama supplier, atau 'ALL'
 * @param string $start_date Y-m-d
 * @param string $end_date   Y-m-d
 * @return string SELECT akhir, atau '' bila penyiapan gagal
 */
function status_siapkan_query($conn, $filter, $nama_supp, $start_date, $end_date)
{
    $s    = mysqli_real_escape_string($conn, $start_date);
    $e    = mysqli_real_escape_string($conn, $end_date);
    $supp = mysqli_real_escape_string($conn, $nama_supp);

    // ---- Bagian yang berubah-ubah mengikuti pilihan "Date Filter" -----------
    // Pola aslinya dipertahankan: tahap yang SEDANG difilter memakai BETWEEN +
    // INNER JOIN (hanya dokumen di rentang itu yang keluar), sedangkan tahap
    // sesudahnya cukup dibatasi ">= tanggal awal" dgn LEFT JOIN.
    $srcBetween   = '';
    $verifBetween = '';
    $kbonWhere    = "where status != 'Cancel'";
    $kbonJoin     = 'LEFT JOIN';
    $lpWhere      = '';
    $lpJoin       = 'LEFT JOIN';
    $payWhere     = '';
    $payWhereFtr  = '';
    $payJoin      = 'LEFT JOIN';

    if ($filter == 'tgl_bpb') {
        $srcBetween   = "BETWEEN '$s' AND '$e'";
        $verifBetween = "BETWEEN '$s' AND '$e'";
        $kbonWhere    = "where tgl_kbon >= '$s' and status != 'Cancel'";
        $lpWhere      = "where tgl_payment >= '$s'";
        $payWhere     = "bankout_date >= '$s'";
        $payWhereFtr  = "tgl_pelunasan >= '$s'";
    } elseif ($filter == 'tgl_kbon') {
        $kbonWhere    = "where tgl_kbon BETWEEN '$s' AND '$e' and status != 'Cancel'";
        $kbonJoin     = 'INNER JOIN';
        $lpWhere      = "where tgl_payment >= '$s'";
        $payWhere     = "bankout_date >= '$s'";
        $payWhereFtr  = "tgl_pelunasan >= '$s'";
    } elseif ($filter == 'tgl_lp') {
        $lpWhere      = "where tgl_payment BETWEEN '$s' AND '$e'";
        $lpJoin       = 'INNER JOIN';
        $payWhere     = "bankout_date >= '$s'";
        $payWhereFtr  = "tgl_pelunasan >= '$s'";
    } else { // tgl_pay
        $payWhere     = "bankout_date BETWEEN '$s' AND '$e'";
        $payWhereFtr  = "tgl_pelunasan BETWEEN '$s' AND '$e'";
        $payJoin      = 'INNER JOIN';
    }

    $srcBpb    = $srcBetween   ? "and bpbdate $srcBetween"      : '';
    $srcBppb   = $srcBetween   ? "and n.tgl_bppb $srcBetween"   : '';
    $verifBpb  = $verifBetween ? "tgl_bpb $verifBetween and"    : '';
    $verifBppb = $verifBetween ? "tgl_bppb $verifBetween and"   : '';

    $jalan = function ($sql) use ($conn) {
        if (!mysqli_query($conn, $sql)) {
            throw new RuntimeException(mysqli_error($conn));
        }
    };

    try {
        // ---- 1. st_e : pelunasan (Bank Out + kanal lama payment_ftr) -------
        $jalan("DROP TEMPORARY TABLE IF EXISTS st_e");
        $jalan("CREATE TEMPORARY TABLE st_e AS
                select * from (
                    select b.no_bankout, b.bankout_date, no_reff
                      from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout
                     where $payWhere and b.status != 'Cancel'
                       and (no_reff like '%LP%' or no_reff like 'PV-AP/%' or no_reff like 'SI/APR/%')
                    UNION ALL
                    select payment_ftr_id, tgl_pelunasan, COALESCE(NULLIF(list_payment_id,''), no_kbon) list_payment_id
                      from payment_ftr where $payWhereFtr AND status != 'Cancel'
                ) a GROUP BY no_reff");
        $jalan("ALTER TABLE st_e ADD INDEX (no_reff)");

        // ---- 2. st_d : List Payment ---------------------------------------
        $jalan("DROP TEMPORARY TABLE IF EXISTS st_d");
        $jalan("CREATE TEMPORARY TABLE st_d AS
                select no_payment, tgl_payment, no_kbon, confirm_date, closed_date
                  from list_payment $lpWhere GROUP BY no_kbon");
        $jalan("ALTER TABLE st_d ADD INDEX (no_kbon), ADD INDEX (no_payment)");

        // ---- 3. st_c : peta dokumen -> Kontrabon ---------------------------
        // BPB nyambung lewat kontrabon.no_bpb. BPPB TIDAK ada di kolom itu,
        // jadi dipetakan lewat return_kb.no_bpbrtn - dan HANYA lewat situ.
        //
        // JANGAN pakai bppb_new.no_kbon sbg jalur tambahan: kolom itu TIDAK
        // bisa dipercaya. Contoh nyata: GK/OUT/0726/00095, 00097, 00100, 00101,
        // 00103, 00105 dan GK/RO/0726/00082 semuanya mencantumkan
        // PV-AP/REG/NAG/2026/09/02259, padahal PV itu isinya cuma
        // GEN/IN/0826/02513 (Rp450.000) - jurnalnya pun tidak menyebut satu pun
        // nomor BPPB tsb, dan return_kb tidak punya barisnya sama sekali.
        // Sebanyak 844 dokumen BPPB punya no_kbon terisi tanpa padanan di
        // return_kb. return_kb sendiri sudah mencakup dua-duanya: 878 dokumen
        // /RO + 53 /OUT.
        $jalan("DROP TEMPORARY TABLE IF EXISTS st_c");
        $jalan("CREATE TEMPORARY TABLE st_c AS
                select no_bpb, no_kbon, tgl_kbon, confirm_date from (
                    select no_bpb, no_kbon, tgl_kbon, confirm_date from kontrabon $kbonWhere GROUP BY no_bpb
                    UNION ALL
                    select r.no_bpbrtn no_bpb, k.no_kbon, k.tgl_kbon, k.confirm_date
                      from return_kb r
                      INNER JOIN (select no_kbon, tgl_kbon, confirm_date from kontrabon $kbonWhere GROUP BY no_kbon) k
                              on k.no_kbon = r.no_kbon
                ) z GROUP BY no_bpb");
        $jalan("ALTER TABLE st_c ADD INDEX (no_bpb), ADD INDEX (no_kbon)");

        // ---- 4. st_w : daftar dokumen yang relevan -------------------------
        // Untuk filter selain "BPB Date", tabel sumber tidak dibatasi tanggal.
        // Tanpa daftar ini, sisi kiri berisi 260.199 dokumen BPB dan baru
        // disaring di ujung. Karena tahap yang difilter memang INNER JOIN,
        // dokumen di luar daftar ini pasti terbuang - jadi memotongnya di depan
        // TIDAK mengubah hasil, hanya memangkas pekerjaan.
        $pakaiDaftar = ($filter != 'tgl_bpb');
        if ($pakaiDaftar) {
            $jalan("DROP TEMPORARY TABLE IF EXISTS st_w");
            if ($filter == 'tgl_kbon') {
                $isiDaftar = "select distinct no_bpb dok from st_c";
            } elseif ($filter == 'tgl_lp') {
                $isiDaftar = "select distinct c.no_bpb dok from st_c c INNER JOIN st_d d on d.no_kbon = c.no_kbon";
            } else { // tgl_pay - pelunasan bisa mengacu ke No List Payment ATAU
                     // langsung ke nomor kontrabon; dua-duanya ditelusuri balik.
                $isiDaftar = "select c.no_bpb dok from st_c c INNER JOIN st_e e on e.no_reff = c.no_kbon
                              UNION
                              select c.no_bpb dok from st_c c
                                INNER JOIN st_d d on d.no_kbon = c.no_kbon
                                INNER JOIN st_e e on e.no_reff = d.no_payment";
            }
            $jalan("CREATE TEMPORARY TABLE st_w AS $isiDaftar");
            $jalan("ALTER TABLE st_w ADD INDEX (dok)");

            // st_c ikut dipangkas supaya join terakhir tidak lagi menyentuh
            // puluhan ribu baris yang toh tidak terpakai.
            $jalan("DELETE c FROM st_c c LEFT JOIN st_w w ON w.dok = c.no_bpb WHERE w.dok IS NULL");
        }

        // ---- 5. st_b : tanggal verifikasi AP -------------------------------
        $jalan("DROP TEMPORARY TABLE IF EXISTS st_b");
        $jalan("CREATE TEMPORARY TABLE st_b AS
                select no_bpb, create_date verif_date from bpb_new
                  where $verifBpb status != 'Cancel' GROUP BY no_bpb
                UNION ALL
                select no_bppb, create_date from bppb_new
                  where $verifBppb status != 'Cancel' GROUP BY no_bppb");
        $jalan("ALTER TABLE st_b ADD INDEX (no_bpb)");
        if ($pakaiDaftar) {
            $jalan("DELETE b FROM st_b b LEFT JOIN st_w w ON w.dok = b.no_bpb WHERE w.dok IS NULL");
        }
    } catch (RuntimeException $x) {
        return '';
    }

    // ---- Dokumen sumber: BPB UNION BPPB ------------------------------------
    // Kolom kedua ('bpbno_int') menampung nomor BPB MAUPUN BPPB, sehingga
    // seluruh join di bawahnya mengenali dua-duanya tanpa perlu dibedakan.
    // No SJ / No WS / Style dikosongkan untuk BPPB: kolom `bppb`.invno TERBUKTI
    // selalu kosong untuk dokumen BPPB (dicek 15 dokumen Agu 2026) - nomor surat
    // jalan memang hanya dipakai di sisi penerimaan. Nomor BPB asal dari sebuah
    // retur ada di bppb_new.no_bpb, kalau nanti mau ditampilkan tinggal ditambah.
    $potongBpb  = $pakaiDaftar ? "INNER JOIN st_w w on w.dok = a.bpbno_int"  : '';
    $potongBppb = $pakaiDaftar ? "INNER JOIN st_w w2 on w2.dok = n.no_bppb"  : '';

    $tmpjo = "(select id_jo,kpno,styleno from act_costing ac
                 inner join so on ac.id=so.id_cost
                 inner join jo_det jod on so.id=jod.id_so group by id_jo)";

    $sumber = "(select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj,
                    COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws,
                    COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style
                from bpb a
                INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier
                $potongBpb
                left join $tmpjo tmpjo on tmpjo.id_jo=a.id_jo
                where confirm = 'Y' and cancel = 'N' $srcBpb
                GROUP BY bpbno_int
                UNION ALL
                select n.supplier nama_supp, n.no_bppb bpbno_int, n.tgl_bppb bpbdate,
                    NULLIF(n.confirm_date,'0000-00-00 00:00:00') confirm_date,
                    '-' no_sj, '-' no_ws, '-' style
                from bppb_new n
                $potongBppb
                where n.status != 'Cancel' $srcBppb
                GROUP BY n.no_bppb) a";

    $whereSupp = ($nama_supp == 'ALL' || $nama_supp === null || $nama_supp === '')
        ? '' : "where nama_supp = '$supp'";

    return "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date,
                   c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon,
                   d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp,
                   e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style
              from $sumber
              LEFT JOIN st_b b on b.no_bpb = a.bpbno_int
              $kbonJoin st_c c on c.no_bpb = a.bpbno_int
              $lpJoin st_d d on d.no_kbon = c.no_kbon
              $payJoin st_e e on (e.no_reff = d.no_payment OR e.no_reff = c.no_kbon)
              $whereSupp
             order by bpbdate asc";
}
