<?php
// ============================================================================
// Pembangun query untuk menu STATUS INFORMATION (status.php + ekspor_status.php).
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
 * @param string $filter     tgl_bpb | tgl_kbon | tgl_lp | tgl_pay
 * @param string $nama_supp  nama supplier, atau 'ALL'
 * @param string $start_date Y-m-d
 * @param string $end_date   Y-m-d
 */
function status_build_query($conn, $filter, $nama_supp, $start_date, $end_date)
{
    $s    = mysqli_real_escape_string($conn, $start_date);
    $e    = mysqli_real_escape_string($conn, $end_date);
    $supp = mysqli_real_escape_string($conn, $nama_supp);

    // ---- Bagian yang berubah-ubah mengikuti pilihan "Date Filter" -----------
    // Pola aslinya dipertahankan: tabel yang SEDANG difilter memakai BETWEEN +
    // INNER JOIN (supaya hanya dokumen di rentang itu yang keluar), sedangkan
    // tahap sesudahnya cukup dibatasi ">= tanggal awal" dgn LEFT JOIN.
    $srcBetween   = '';                                   // filter tgl di dokumen sumber (BPB/BPPB)
    $verifBetween = '';                                   // filter tgl di tabel verifikasi
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

    $srcBpb    = $srcBetween ? "and bpbdate $srcBetween"     : '';
    $srcBppb   = $srcBetween ? "and n.tgl_bppb $srcBetween"  : '';
    $verifBpb  = $verifBetween ? "tgl_bpb $verifBetween and" : '';
    $verifBppb = $verifBetween ? "tgl_bppb $verifBetween and": '';

    // Peta Job Order -> No WS & Style (dipakai blok BPB).
    $tmpjo = "(select id_jo,kpno,styleno from act_costing ac
                 inner join so on ac.id=so.id_cost
                 inner join jo_det jod on so.id=jod.id_so group by id_jo)";

    // ---- Dokumen sumber: BPB UNION BPPB ------------------------------------
    // Kolom kedua ('bpbno_int') menampung nomor BPB MAUPUN BPPB, sehingga
    // seluruh join di bawahnya (verifikasi, kontrabon, list payment, pelunasan)
    // tidak perlu diubah - cukup ikut mengenali nomor BPPB.
    // No SJ / No WS / Style dikosongkan untuk BPPB: kolom `bppb`.invno TERBUKTI
    // selalu kosong untuk dokumen BPPB (dicek 15 dokumen Agu 2026, semuanya
    // kosong) - nomor surat jalan memang hanya dipakai di sisi penerimaan (BPB).
    // Nomor BPB asal dari sebuah retur tersimpan di bppb_new.no_bpb, kalau nanti
    // mau ditampilkan tinggal ditambah sbg kolom baru.
    $sumber = "(select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj,
                    COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws,
                    COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style
                from bpb a
                INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier
                left join $tmpjo tmpjo on tmpjo.id_jo=a.id_jo
                where confirm = 'Y' and cancel = 'N' $srcBpb
                GROUP BY bpbno_int
                UNION ALL
                select n.supplier nama_supp, n.no_bppb bpbno_int, n.tgl_bppb bpbdate,
                    NULLIF(n.confirm_date,'0000-00-00 00:00:00') confirm_date,
                    '-' no_sj, '-' no_ws, '-' style
                from bppb_new n
                where n.status != 'Cancel' $srcBppb
                GROUP BY n.no_bppb) a";

    // ---- Tanggal verifikasi AP (bpb_new untuk BPB, bppb_new untuk BPPB) -----
    $verif = "(select no_bpb, create_date verif_date from bpb_new
                 where $verifBpb status != 'Cancel' GROUP BY no_bpb
               UNION ALL
               select no_bppb, create_date from bppb_new
                 where $verifBppb status != 'Cancel' GROUP BY no_bppb) b";

    // ---- Kontrabon --------------------------------------------------------
    // BPB nyambung lewat kontrabon.no_bpb. BPPB TIDAK ada di kolom itu, jadi
    // dipetakan lewat return_kb.no_bpbrtn - dan HANYA lewat situ.
    //
    // JANGAN pakai bppb_new.no_kbon sbg jalur tambahan: kolom itu TIDAK bisa
    // dipercaya. Contoh nyata: GK/OUT/0726/00095, 00097, 00100, 00101, 00103,
    // 00105 dan GK/RO/0726/00082 semuanya mencantumkan
    // PV-AP/REG/NAG/2026/09/02259, padahal PV itu isinya cuma GEN/IN/0826/02513
    // (Rp450.000) - jurnalnya pun tidak menyebut satu pun nomor BPPB tsb, dan
    // return_kb tidak punya barisnya sama sekali. Sebanyak 844 dokumen BPPB
    // punya no_kbon terisi tanpa padanan di return_kb, jadi memakai kolom itu
    // akan menempelkan kontrabon yang salah ke ratusan baris laporan.
    // return_kb sendiri sudah mencakup dua-duanya: 878 dokumen /RO + 53 /OUT.
    $kbonSrc = "(select no_kbon, tgl_kbon, confirm_date from kontrabon $kbonWhere GROUP BY no_kbon)";
    $kontrabon = "(select no_bpb, no_kbon, tgl_kbon, confirm_date from (
                      select no_bpb, no_kbon, tgl_kbon, confirm_date from kontrabon $kbonWhere GROUP BY no_bpb
                      UNION ALL
                      select r.no_bpbrtn no_bpb, k.no_kbon, k.tgl_kbon, k.confirm_date
                        from return_kb r INNER JOIN $kbonSrc k on k.no_kbon = r.no_kbon
                  ) z GROUP BY no_bpb) c";

    $listPayment = "(select no_payment, tgl_payment, no_kbon, confirm_date, closed_date
                       from list_payment $lpWhere GROUP BY no_kbon) d";

    $pelunasan = "(select * from (
                      select b.no_bankout, b.bankout_date, no_reff
                        from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout
                       where $payWhere and b.status != 'Cancel'
                         and (no_reff like '%LP%' or no_reff like 'PV-AP/%' or no_reff like 'SI/APR/%')
                      UNION ALL
                      select payment_ftr_id, tgl_pelunasan, COALESCE(NULLIF(list_payment_id,''), no_kbon) list_payment_id
                        from payment_ftr where $payWhereFtr AND status != 'Cancel'
                  ) a GROUP BY no_reff) e";

    $whereSupp = ($nama_supp == 'ALL' || $nama_supp === null || $nama_supp === '')
        ? '' : "where nama_supp = '$supp'";

    return "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date,
                   c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon,
                   d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp,
                   e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style
              from $sumber
              LEFT JOIN $verif on b.no_bpb = a.bpbno_int
              $kbonJoin $kontrabon on c.no_bpb = a.bpbno_int
              $lpJoin $listPayment on d.no_kbon = c.no_kbon
              $payJoin $pelunasan on (e.no_reff = d.no_payment OR e.no_reff = c.no_kbon)
              $whereSupp
             order by bpbdate asc";
}
