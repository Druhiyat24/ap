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


/* ============================================================================
   PV BIAYA - SISA yang belum dibayar.

   Dipindah ke sini dari ajx_cari-pv-biaya.php supaya layar Update Payment
   Date dan mesin proyeksi memakai HITUNGAN YANG SAMA PERSIS. Dua salinan
   query seperti ini cepat atau lambat berbeda, dan bedanya berupa uang.

   PERBAIKAN 20 Nov 2026 - type_pv BOLEH NULL:
   Potongannya dulu `a.type_pv = 'Biaya'` saja. Kolom type_pv baru mulai diisi
   1 Juli 2026; SELURUH pembayaran sebelum tanggal itu NULL (1.163 baris di
   2026 saja, ditambah 2022-2025) sehingga tidak pernah ikut terpotong.
   Akibatnya di produksi 29 PV senilai Rp 22,07 M tampil masih outstanding
   padahal bank out-nya ada dan Approved - mis. PV/NAG/0426/00781 Rp 9,75 M.
   Sesudah NULL ikut dipotong: 51 PV / Rp 25,02 M turun jadi 22 PV / Rp 2,95 M.

   Aman karena no_reff sudah menentukan dokumennya: di seluruh b_bankout_det,
   baris ber-no_reff 'PV/%' HANYA punya type_pv 'Biaya' atau NULL - tidak ada
   jenis lain yang memakai penomoran itu.

   PERBAIKAN 20 Nov 2026 - STATUS: yang ditolak hanya Cancel.
   Dulu syaratnya `a.status = 'Approved'` saja, dan itu menolak DUA arah:
     - Draft belum boleh masuk - padahal rencana bayar justru disusun sebelum
       approval selesai (permintaan pemakai: "supaya yang masih draft juga
       muncul, jadi tidak sama dengan cancel saja");
     - SECOND APPROVED pun tertolak - jadi PV yang sudah LULUS approval
       terakhir malah HILANG dari daftar. Alurnya Draft -> Approved ->
       SECOND APPROVED, dan syarat lama cuma menangkap yang di tengah.

   PERBAIKAN 20 Nov 2026 - JOIN PROFIT CENTER TAHAN NULL (`<=>`).
   Potongannya dijoin per nomor + profit center, dan di SQL `NULL = NULL`
   TIDAK PERNAH benar. Kolom profit_center baru mulai diisi belakangan, jadi
   seluruh PV lama (pc NULL di kedua sisi) tidak pernah terpotong walaupun
   bank out-nya ada: begitu Cancel saja yang ditolak, 4.695 PV senilai
   Rp 721 M tampil outstanding - 4.574 di antaranya (Rp 653 M) HANYA karena
   NULL ini. Dgn `<=>` sisanya 121 PV / Rp 68 M. Angka status Approved &
   Draft TIDAK berubah sedikit pun oleh perbaikan ini (diukur di produksi),
   jadi yang terlihat hari ini tetap sama.

   SISA YANG BELUM DIBERESKAN (dilaporkan, belum diubah): dari 121 itu, 59 PV
   (Rp 65,7 M) sebetulnya sudah lunas - bank out-nya tercatat di profit center
   LAIN, sehingga potongan per-PC tidak menemukannya. Yang benar-benar belum
   dibayar 62 PV / Rp 2,4 M. Menutupnya butuh penjaga tingkat dokumen, dan itu
   ikut menyembunyikan 1 baris Approved (Rp 245 juta) yang sekarang tampil -
   karena itu diputuskan terpisah, tidak diselipkan di sini.
   ============================================================================ */
if (!function_exists('pvb_biaya_sql')) {
    /** Subquery pembayaran PV biaya. */
    function pvb_biaya_bayar()
    {
        return "
            select a.profit_center, a.no_reff, sum(a.total) total_pv
              from b_bankout_det a
             inner join b_bankout_h b on b.no_bankout = a.no_bankout
             where b.status <> 'Cancel'
               and a.no_reff like 'PV/%'
               and (a.type_pv = 'Biaya' or a.type_pv is null)
             group by a.no_reff, a.profit_center";
    }

    /**
     * Query sisa PV biaya. $where dipasang pemanggilnya - kolom yang boleh
     * dipakai: a.no_pv, a.pv_date, a.due_date, a.nama_supp, dan
     * (a.total - COALESCE(b.total_pv,0)) untuk sisanya.
     */
    function pvb_biaya_sql($where)
    {
        $bayar = pvb_biaya_bayar();
        return "
with total_pv as (
  select b.profit_center,
         a.nama_supp, a.no_pv, a.pv_date, max(b.due_date) due_date, a.curr,
         a.frm_akun, a.id_cash_flow, a.deskripsi,
         (SUM(b.amount - b.ded_add)
          + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100))
          - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total
    from tbl_pv_h a
    inner join tbl_pv b on b.no_pv = a.no_pv
   where a.status <> 'Cancel'
   group by a.no_pv, b.profit_center),
total_bk as ($bayar)
select a.no_pv, a.pv_date, a.due_date, a.nama_supp, a.curr, a.profit_center,
       a.frm_akun, a.id_cash_flow, a.deskripsi,
       (a.total - COALESCE(b.total_pv,0)) sisa
  from total_pv a
  left join total_bk b on b.no_reff = a.no_pv and b.profit_center <=> a.profit_center
 where $where
 order by a.due_date asc, a.no_pv asc";
    }
}

/* ============================================================================
   KAMIS - hari bayar (aturan tim AP).

   Yang dipakai Kamis DI MINGGU YANG SAMA (Minggu-Sabtu) dgn jatuh temponya,
   BUKAN Kamis berikutnya: rumusnya disalin dari ajx_ap_projection_report.php
   supaya PV biaya dan PV AP mustahil memberi tanggal berbeda untuk minggu
   yang sama. Untuk Jumat & Sabtu hasilnya MUNDUR, dan memang begitu maunya.

   DAYOFWEEK MySQL: Minggu=1 .. Sabtu=7, Kamis=5, jadi selisihnya 5-DAYOFWEEK.
   date('w') PHP: Minggu=0 .. Sabtu=6, jadi selisih yang sama = 4 - w.
   ============================================================================ */
if (!function_exists('pvb_kamis')) {
    function pvb_kamis($tgl)
    {
        $t = strtotime((string) $tgl);
        if ($t === false) { return ''; }
        $geser = 4 - (int) date('w', $t);
        return date('Y-m-d', strtotime(($geser >= 0 ? '+' : '') . $geser . ' day', $t));
    }
}

/* ============================================================================
   DOKUMEN YANG BELUM PUNYA RENCANA BAYAR.

   Diminta pemakai 20 Nov 2026: "yang belum diupdate pun bisa ditarik ...
   harusnya ikut ter-set dulu ke Kamis terdekat seperti yang PV AP". Sebelum
   ini, laporan proyeksi HANYA memuat dokumen yang sudah diberi tanggal di
   menu Update Payment Date - dan di produksi tidak satu pun dari 22 PV biaya
   yang outstanding punya tanggal itu, jadi barisnya memang kosong melompong.

   Aturannya disamakan dgn PV AP: rencana yang disetel orang MENANG; yang
   belum punya memakai Kamis di minggu jatuh temponya. Dokumen yang sudah
   LEWAT tempo karena itu jatuh di Kamis yang sudah lewat, jadi TIDAK muncul
   di periode depan - sama persis dgn perilaku AP, dan memang begitu
   seharusnya: menaruhnya di hari pertama periode sama saja dgn mengarang
   tanggal bayar yang tidak pernah diputuskan siapa pun.
   ============================================================================ */
if (!function_exists('pvb_tanpa_rencana')) {
    function pvb_tanpa_rencana($conn, $mulai, $akhir)
    {
        $mulai = (string) $mulai;
        $akhir = (string) $akhir;
        if ($mulai === '' || $akhir === '') { return array(); }

        /* Yang SUDAH punya rencana - dikecualikan supaya tidak terhitung dua
           kali. KUNCINYA NOMOR + PROFIT CENTER, bukan nomor saja: satu PV bisa
           memuat baris dua profit center dgn nilai sendiri-sendiri (mis.
           PV/NAG/0926/00040 = NAG 1.235.350.803 + NAK 4.643.784), dan layar
           Update Payment Date memang menjadwalkannya sbg dua baris terpisah.
           Kalau dikunci nomornya saja, menjadwalkan sisi NAG saja akan ikut
           MENGHILANGKAN sisi NAK dari laporan - diam-diam, tanpa jejak.
           Tabelnya boleh belum ada (produksi belum dimigrasi). */
        $sudah = array();
        $q = mysqli_query($conn, "show tables like 'ap_pvbiaya_plan_det'");
        if ($q && mysqli_num_rows($q)) {
            $q = mysqli_query($conn, "select distinct d.no_pv, d.profit_center
                                        from ap_pvbiaya_plan_det d
                                       inner join ap_pvbiaya_plan_h h
                                          on h.doc_number = d.doc_number and h.status <> 'CANCEL'
                                       where d.status = 'Y'");
            while ($q && ($r = mysqli_fetch_assoc($q))) {
                $sudah[(string) $r['no_pv'] . '|' . (string) $r['profit_center']] = 1;
            }
        }

        $idKtg = (int) pvb_dpcbd_kategori($conn);
        $hasil = array();

        /* ---- PV Biaya: kategorinya ikut dokumennya ---- */
        $q = mysqli_query($conn, pvb_biaya_sql("(a.total - COALESCE(b.total_pv,0)) >= 1"));
        while ($q && ($r = mysqli_fetch_assoc($q))) {
            $r['jenis'] = 'Biaya';
            $r['id_cash_flow'] = (int) $r['id_cash_flow'];
            $hasil[] = $r;
        }

        /* ---- DP, CBD, FTR cash: kategorinya tetap Uang Muka Supplier ---- */
        foreach (array_merge(pvb_dpcbd_daftar($conn), pvb_ftrcash_daftar($conn)) as $r) {
            $r['id_cash_flow'] = $idKtg;
            $hasil[] = $r;
        }

        $keluar = array();
        foreach ($hasil as $r) {
            $kunci = (string) $r['no_pv'] . '|'
                   . (isset($r['profit_center']) ? (string) $r['profit_center'] : '');
            if (isset($sudah[$kunci])) { continue; }
            /* TANGGALNYA SELALU KAMIS DI MINGGU JATUH TEMPONYA, tanpa kecuali -
               termasuk dokumen yang jatuh temponya sudah lewat jauh (keputusan
               pemakai 20 Nov 2026: "set saja Kamis sesuai tanggal temponya,
               samakan saja walaupun batas temponya sudah lewat jauh").
               Akibatnya yang sudah lewat tempo jatuh di Kamis yang sudah lewat
               dan tidak muncul di periode depan - itu memang konsekuensinya,
               dan sengaja TIDAK ditutupi dgn menggeser tanggalnya: menggeser
               berarti mengarang tanggal bayar yang tidak pernah diputuskan
               siapa pun. Yang perlu dibayar lebih cepat dijadwalkan lewat
               menu Update Payment Date. */
            $tgl = pvb_kamis($r['due_date']);
            if ($tgl === '' || $tgl < $mulai || $tgl > $akhir) { continue; }
            $keluar[] = array(
                'tgl'          => $tgl,
                'no_pv'        => $r['no_pv'],
                'jenis'        => $r['jenis'],
                'curr'         => $r['curr'],
                'amount'       => (float) $r['sisa'],
                'frm_akun'     => isset($r['frm_akun']) ? $r['frm_akun'] : '',
                'id_cash_flow' => (int) $r['id_cash_flow'],
                /* Nama supplier & profit center TIDAK dipakai mesin proyeksi,
                   tapi WAJIB ada untuk lembar sumber Excel: lembar itu harus
                   mendaftar baris yang sama persis dgn yang dihitung, kalau
                   tidak ia menyalahkan angka di laporannya sendiri. */
                'nama_supp'      => isset($r['nama_supp']) ? (string) $r['nama_supp'] : '',
                'profit_center'  => isset($r['profit_center']) ? (string) $r['profit_center'] : '',
                'due_date'       => isset($r['due_date']) ? (string) $r['due_date'] : '',
            );
        }
        return $keluar;
    }
}
