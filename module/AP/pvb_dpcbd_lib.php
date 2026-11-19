<?php
/* ============================================================================
   DP & CBD untuk menu Update Payment Date.
   Diminta pemakai 18 Nov 2026: "update payment plan pv biaya itu juga sama
   seperti bank out bisa tarik nomor FTR CBD dan DP yang statusnya sudah
   approve dan masih outstanding".

   DIPAKAI BERSAMA oleh pencarian (ajx_cari-pv-biaya.php) dan penyimpanan
   (simpan_pvbiaya_payment_date.php). Sengaja SATU berkas, bukan disalin:
   kalau dua tempat itu beda sedikit saja, gejalanya halus - voucher ketemu
   di layar lalu DITOLAK waktu Save, tanpa alasan yang masuk akal buat
   pemakainya.

   BEDA DGN BANK OUT - DUA HAL:
   1. status_pl TIDAK dibaca, sama spt pelonggaran di cabang Biaya: rencana
      bayar memang disusun sebelum approval payment list selesai.
   2. Yang ber-status 'Approved' (skema LAMA, 2022 s/d 30 Jun 2026) TIDAK
      ikut. Dokumen lama itu tidak punya jejak pembayaran di ketiga tabel
      yang dihitung di sini, jadi selamanya terlihat outstanding - 660
      dokumen hantu yang akan menenggelamkan yang benar-benar perlu
      dijadwalkan. Yang dipakai hanya skema baru, 'SECOND APPROVED'.
   ============================================================================ */

if (!defined('PVB_DPCBD_KATEGORI_BAWAAN')) {
    /* Pengeluaran Pembayaran Uang Muka Supplier. Dipilih berdasarkan
       KEBIASAAN YANG SUDAH ADA, bukan ditebak: dari dokumen Bank Out yang
       membayar DP/CBD, 58 CBD + 10 DP memakai kategori ini dan cuma 1 yang
       memakai kategori lain. */
    define('PVB_DPCBD_KATEGORI_BAWAAN', 14);
}

if (!function_exists('pvb_dpcbd_kategori')) {
    /* Idnya dicari dari master supaya tidak jadi angka ajaib; kalau barisnya
       hilang, dipakai id bawaan di atas. */
    function pvb_dpcbd_kategori($conn)
    {
        $q = mysqli_query($conn, "select id from master_cash_flow
                                   where show_subcategory = 'UANG MUKA PEMBELIAN'
                                     and type_cashflow = 'Cash Out'
                                     and status = 'Y'
                                   order by id asc limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        return $r ? (int) $r['id'] : PVB_DPCBD_KATEGORI_BAWAAN;
    }
}

if (!function_exists('pvb_dpcbd_daftar')) {
    /**
     * Daftar DP & CBD yang sudah approve dan masih outstanding.
     *
     * $dasar    'pv' (tgl_kbon) atau 'tempo' (tgl_tempo); '' = tanpa saringan
     * $mulai    'yyyy-mm-dd' atau ''
     * $akhir    'yyyy-mm-dd' atau ''
     * $supplier nama persis, atau '' untuk semua
     *
     * Ambangnya >= 1 rupiah, sama dgn cabang Biaya - sisa di bawah Rp 1 itu
     * debu pembulatan dari dokumen yang sebenarnya sudah lunas.
     */
    function pvb_dpcbd_daftar($conn, $dasar = '', $mulai = '', $akhir = '', $supplier = '')
    {
        /* Yang sudah dibayar, dari KETIGA salurannya - sama persis dgn yang
           dipakai Bank Out (getAlreadyPaid): bank out, petty cash out, dan
           payment_ftr. Kalau salah satu ketinggalan, dokumen yang sudah lunas
           lewat saluran itu akan terlihat masih outstanding. */
        $bayar = "
            select no_reff, type_pv, sum(t) t from (
                select a.no_reff, a.type_pv, sum(a.total) t
                  from b_bankout_det a
                 inner join b_bankout_h b on b.no_bankout = a.no_bankout
                 where b.status <> 'Cancel' and a.type_pv in ('DP','CBD')
                 group by a.no_reff, a.type_pv
                union all
                select a.no_reff, a.type_pv, sum(a.amount)
                  from c_petty_cashout_det a
                 inner join c_petty_cashout_h b on b.no_pco = a.no_pco
                 where b.status <> 'Cancel' and a.type_pv in ('DP','CBD')
                 group by a.no_reff, a.type_pv
                union all
                select no_kbon, type_pv, sum(nominal)
                  from payment_ftr
                 where status <> 'Cancel' and type_pv in ('DP','CBD')
                 group by no_kbon, type_pv
            ) x group by no_reff, type_pv";

        /* Sisanya sudah dihitung di dalam subquery sbg kolom 'sisa' - di luar
           sini tabel pembayarannya tidak kelihatan lagi. */
        $saring = array("d.sisa >= 1");
        if ($dasar !== '' && $mulai !== '' && $akhir !== '') {
            $kol = ($dasar === 'pv') ? 'd.tgl_kbon' : 'd.tgl_tempo';
            $saring[] = "$kol between '" . mysqli_real_escape_string($conn, $mulai) . "'
                              and '" . mysqli_real_escape_string($conn, $akhir) . "'";
        }
        if ($supplier !== '') {
            $saring[] = "d.nama_supp = '" . mysqli_real_escape_string($conn, $supplier) . "'";
        }
        $where = implode(' and ', $saring);

        $sql = "
            select jenis, no_pv, pv_date, due_date, nama_supp, curr, profit_center,
                   frm_akun, sisa
              from (
                select 'DP' jenis, h.no_kbon no_pv, h.tgl_kbon pv_date, h.tgl_tempo due_date,
                       h.nama_supp, h.curr, h.profit_center, h.from_account frm_akun, h.total,
                       h.tgl_kbon, h.tgl_tempo,
                       (h.total - coalesce(p.t,0)) sisa, coalesce(p.t,0) t
                  from kontrabon_h_dp h
                  left join ($bayar) p on p.no_reff = h.no_kbon and p.type_pv = 'DP'
                 where h.status = 'SECOND APPROVED'
                union all
                select 'CBD', h.no_kbon, h.tgl_kbon, h.tgl_tempo,
                       h.nama_supp, h.curr, h.profit_center, h.from_account, h.total,
                       h.tgl_kbon, h.tgl_tempo,
                       (h.total - coalesce(p.t,0)), coalesce(p.t,0)
                  from kontrabon_h_cbd h
                  left join ($bayar) p on p.no_reff = h.no_kbon and p.type_pv = 'CBD'
                 where h.status = 'SECOND APPROVED'
              ) d
             where $where
             order by d.tgl_tempo asc, d.no_pv asc";

        $q = mysqli_query($conn, $sql);
        $hasil = array();
        while ($q && ($r = mysqli_fetch_assoc($q))) { $hasil[] = $r; }
        return $hasil;
    }
}

if (!function_exists('pvb_plan_sumber')) {
    /**
     * Potongan SQL untuk membaca rencana bayar (ap_pvbiaya_plan_det) beserta
     * rekening pembayar, supplier dan kategori arus kasnya - yang letaknya
     * BEDA-BEDA menurut jenis dokumennya:
     *   Biaya -> tbl_pv_h       (punya kolom id_cash_flow sendiri)
     *   DP    -> kontrabon_h_dp (kategorinya tetap: Uang Muka Supplier)
     *   CBD   -> kontrabon_h_cbd
     *
     * Dipakai mesin proyeksi DAN lembar sumber Excel, jadi dua-duanya
     * mustahil memberi angka yang berbeda.
     *
     * Kolom 'jenis' DITANYAKAN dulu, tidak diasumsikan: produksi bisa saja
     * belum dimigrasi, dan laporan tidak boleh mati karenanya - kalau
     * kolomnya belum ada, hasilnya persis seperti sebelum DP/CBD ada.
     */
    function pvb_plan_sumber($conn, $alias = 'd')
    {
        $ada = false;
        $q = mysqli_query($conn, "show columns from `ap_pvbiaya_plan_det` like 'jenis'");
        if ($q && mysqli_num_rows($q)) { $ada = true; }

        if (!$ada) {
            return array(
                'jenis' => "'Biaya'",
                'akun'  => 'p.frm_akun',
                'supp'  => 'p.nama_supp',
                'ktg'   => 'p.id_cash_flow',
                'join'  => "left join tbl_pv_h p on p.no_pv = $alias.no_pv",
            );
        }

        $idKtg  = (int) pvb_dpcbd_kategori($conn);
        $kasNag = PVB_FTRCASH_KAS_NAG;
        $kasNak   = PVB_FTRCASH_KAS_NAK;

        /* Tabel ftr_* digabung SESUDAH dikelompokkan per nomor - kalau
           di-join apa adanya, satu rencana akan berlipat sebanyak baris
           FTR-nya (bisa 5) dan nilainya ikut berlipat di laporan. */
        return array(
            'jenis' => "$alias.jenis",
            'akun'  => "case when $alias.jenis in ('FTR-CBD','FTR-DP')
                             then (case when $alias.profit_center = 'NAK' then '$kasNak' else '$kasNag' end)
                             else coalesce(p.frm_akun, dp.from_account, cb.from_account) end",
            'supp'  => "coalesce(p.nama_supp, dp.nama_supp, cb.nama_supp, fc.supp, fd.supp)",
            /* Semua yang bukan Biaya - DP, CBD, FTR-CBD, FTR-DP - memakai
               satu kategori: Uang Muka Supplier. */
            'ktg'   => "case when $alias.jenis = 'Biaya' then p.id_cash_flow else $idKtg end",
            'join'  => "left join tbl_pv_h      p  on p.no_pv   = $alias.no_pv and $alias.jenis = 'Biaya'
              left join kontrabon_h_dp  dp on dp.no_kbon = $alias.no_pv and $alias.jenis = 'DP'
              left join kontrabon_h_cbd cb on cb.no_kbon = $alias.no_pv and $alias.jenis = 'CBD'
              left join (select no_ftr_cbd nomor, max(supp) supp from ftr_cbd group by no_ftr_cbd) fc
                     on fc.nomor = $alias.no_pv and $alias.jenis = 'FTR-CBD'
              left join (select no_ftr_dp nomor, max(supp) supp from ftr_dp group by no_ftr_dp) fd
                     on fd.nomor = $alias.no_pv and $alias.jenis = 'FTR-DP'",
        );
    }
}

/* ============================================================================
   JALUR KAS: FTR CBD/DP yang cara bayarnya CASH.
   Diminta pemakai 18 Nov 2026: "di FTR ada type bayarnya pakai apa, transfer
   atau cash; kalau transfer wajib masuk PV dulu, jadi untuk yang cash itu
   langsung di-update payment plan pakai nomor FTR-nya saja".

   DIBUKTIKAN DI DATA PRODUKSI, bukan diterima begitu saja: dari FTR CBD/DP
   ber-status Approved - yang <b>Transfer</b> 19 CBD + 6 DP SEMUANYA jadi
   kontrabon PV-AP dan NOL dibayar kas; yang <b>Cash</b> 14 CBD NOL jadi
   kontrabon dan 13 dibayar kas kecil. Tidak ada satu pun penyimpangan.
   ============================================================================ */

/* KATEGORI FTR CASH = SAMA DGN DP/CBD: Pengeluaran Pembayaran Uang Muka
   Supplier (diputuskan pemakai 18 Nov 2026). Tidak ada fungsi kategori
   tersendiri - semuanya lewat pvb_dpcbd_kategori().

   YANG PERLU DIKETAHUI: 12 pembayaran kas kecil atas FTR CBD yang sudah
   terjadi semuanya dicatat dgn kategori 13 "Pengeluaran Pembayaran Utang
   Usaha". Jadi untuk dokumen-dokumen itu, proyeksi (Uang Muka Supplier) dan
   realisasi (Utang Usaha) akan jatuh di BARIS BERBEDA. Itu pilihan yang
   sudah disadari; kalau mau rapat, kategori di petty cash-nya yang
   disesuaikan, bukan di sini. */

/* Kas kecil pembayarnya. FTR tidak punya kolom rekening, jadi ditentukan dari
   profit center-nya: Knitting dari kas kecilnya sendiri, selain itu Garment.
   Seluruh 14 FTR Cash yang ada sejauh ini NAG, dibayar dari 1.01.01. */
if (!defined('PVB_FTRCASH_KAS_NAG')) { define('PVB_FTRCASH_KAS_NAG', '1.01.01'); }
if (!defined('PVB_FTRCASH_KAS_NAK')) { define('PVB_FTRCASH_KAS_NAK', '1.01.11'); }

if (!function_exists('pvb_ftrcash_kas')) {
    function pvb_ftrcash_kas($pc)
    {
        return (strtoupper(trim((string) $pc)) === 'NAK')
            ? PVB_FTRCASH_KAS_NAK : PVB_FTRCASH_KAS_NAG;
    }
}

if (!function_exists('pvb_ftrcash_daftar')) {
    /**
     * FTR CBD & DP bercara bayar Cash yang sudah Approved dan masih
     * outstanding. Bentuk barisnya sama dgn pvb_dpcbd_daftar().
     *
     * DIJUMLAHKAN PER NOMOR: ftr_cbd itu tabel BARIS, bukan kepala - satu
     * nomor bisa punya sampai 5 baris (mis. FTR/C/NAG/1126/00736 = 90.000 +
     * 240.000). Kalau tidak dijumlahkan, satu dokumen akan muncul berkali-
     * kali dgn nilai sepotong-sepotong.
     *
     * NILAI YANG DIBAYAR: CBD pakai total; DP pakai dp_value - kolom total
     * di ftr_dp itu nilai PO-nya, bukan uang mukanya (222.970.000 vs
     * 111.485.000 pada FTR/D/NAG/1126/00037).
     */
    function pvb_ftrcash_daftar($conn, $dasar = '', $mulai = '', $akhir = '', $supplier = '')
    {
        /* Pembayarannya dicari lewat NOMOR saja, tidak lewat type_pv: petty
           cash menulisnya 'FTR-CBD' (bukan 'CBD'), dan nomor FTR tidak pernah
           dipakai dokumen jenis lain - jadi nomornya sudah cukup, sekaligus
           tahan kalau nanti ejaan type_pv-nya berbeda lagi. */
        $bayar = "
            select no_reff, sum(t) t from (
                select a.no_reff, sum(a.amount) t
                  from c_petty_cashout_det a
                 inner join c_petty_cashout_h b on b.no_pco = a.no_pco
                 where b.status <> 'Cancel' and a.no_reff like 'FTR/%'
                 group by a.no_reff
                union all
                select a.no_reff, sum(a.total)
                  from b_bankout_det a
                 inner join b_bankout_h b on b.no_bankout = a.no_bankout
                 where b.status <> 'Cancel' and a.no_reff like 'FTR/%'
                 group by a.no_reff
            ) x group by no_reff";

        $saring = array("d.sisa >= 1");
        if ($dasar !== '' && $mulai !== '' && $akhir !== '') {
            $kol = ($dasar === 'pv') ? 'd.pv_date' : 'd.due_date';
            $saring[] = "$kol between '" . mysqli_real_escape_string($conn, $mulai) . "'
                              and '" . mysqli_real_escape_string($conn, $akhir) . "'";
        }
        if ($supplier !== '') {
            $saring[] = "d.nama_supp = '" . mysqli_real_escape_string($conn, $supplier) . "'";
        }
        $where = implode(' and ', $saring);

        $sql = "
            select jenis, no_pv, pv_date, due_date, nama_supp, curr, profit_center, sisa
              from (
                select 'FTR-CBD' jenis, f.no_ftr_cbd no_pv,
                       min(f.tgl_ftr_cbd) pv_date, max(f.tgl_bayar) due_date,
                       max(f.supp) nama_supp, max(f.curr) curr, max(f.profit_center) profit_center,
                       (sum(f.total) - coalesce(max(p.t),0)) sisa
                  from ftr_cbd f
                  left join ($bayar) p on p.no_reff = f.no_ftr_cbd
                 where f.status = 'Approved' and f.payment_method = 'Cash'
                 group by f.no_ftr_cbd
                union all
                select 'FTR-DP', f.no_ftr_dp,
                       min(f.tgl_ftr_dp), max(f.tgl_bayar),
                       max(f.supp), max(f.curr), max(f.profit_center),
                       (sum(f.dp_value) - coalesce(max(p.t),0))
                  from ftr_dp f
                  left join ($bayar) p on p.no_reff = f.no_ftr_dp
                 where f.status = 'Approved' and f.payment_method = 'Cash'
                 group by f.no_ftr_dp
              ) d
             where $where
             order by d.due_date asc, d.no_pv asc";

        $q = mysqli_query($conn, $sql);
        $hasil = array();
        while ($q && ($r = mysqli_fetch_assoc($q))) {
            $r['frm_akun'] = pvb_ftrcash_kas($r['profit_center']);
            $hasil[] = $r;
        }
        return $hasil;
    }
}
