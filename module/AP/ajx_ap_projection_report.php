<?php
/* ============================================================================
   AP PAYMENT PROJECTION REPORT - penyedia datanya.
   Pasangannya ap_projection_report.php.

   BENTUKNYA: satu baris per payment voucher yang MASIH BERUTANG, lalu nilai
   utangnya diletakkan di KOLOM TANGGAL tempat uangnya diperkirakan keluar.
   Kolom tanggalnya dibuat dari periode yang diminta, jadi jumlah kolomnya
   berubah-ubah - itu sebabnya daftar kolom ikut dikirim di JSON, bukan
   dipatok di halaman.

   ----------------------------------------------------------------------
   EMPAT KEPUTUSAN YANG MENENTUKAN ISINYA
   ----------------------------------------------------------------------
   1. "MASIH BERUTANG" memakai saldo_akhir dari ap_report2/query_pv2.php -
      berkas yang SAMA yang dipakai AP Report (diputuskan pemakai: "samakan
      dengan yang ada di ap report"). Berkas itu di-include, bukan ditiru,
      supaya angkanya tidak bisa berbeda dgn AP Report.

      JANGAN memakai kontrabon_h.balance atau ada-tidaknya baris Bank Out:
      diperiksa pada 2.225 voucher 2026, KEDUANYA TIDAK SEJALAN - 1.379
      voucher ber-balance 0 padahal belum pernah masuk Bank Out, dan 369
      voucher ber-balance > 0 padahal Bank Out-nya sudah ada.

   2. SALDO PER TANGGAL. query_pv2.php menghitung saldo_akhir secara
      KUMULATIF - saldo_awal sudah memikul seluruh mutasi sebelum
      start_date. Dibuktikan: query yang sama dgn start 2022-01-01 dan
      2026-01-01 (end sama) menghasilkan saldo identik untuk 1.083 voucher,
      nol selisih. Jadi untuk mendapat "sisa utang pada tanggal D" cukup
      memanggilnya dgn end_date = D.

      Yang dipakai D = SEHARI SEBELUM periode proyeksi, meniru laporan AR
      yang menyaring dgn pembayaran SEBELUM periode. Dengan begitu voucher
      yang memang dijadwalkan dan dibayar DI DALAM periode tetap muncul -
      kalau memakai akhir periode, voucher itu saldonya sudah nol dan
      justru hilang dari proyeksinya sendiri.

   3. TANGGAL PROYEKSI - BERJENJANG, tiga lapis (diminta pemakai 5 Nov
      2026: "plan awal itu juga baca ya sebagai filter, mirip seperti di
      ar_dev ambil due date sama due date update nya"):

          1) plan_date - rencana hasil menu Update Payment Plan (PPU),
          2) plan_awal - RENCANA AWAL, yaitu Kamis di minggu Minggu-Sabtu
             milik jatuh temponya (ap_payment_plan_awal),
          3) jatuh tempo - jaring terakhir, kalau dua-duanya kosong.

      Bentuknya sama dgn laporan proyeksi AR yang memakai
      COALESCE(duedate_update, duedate): tanggal bawaan dan tanggal hasil
      perubahan dibaca BERURUTAN, bukan salah satu saja. Bedanya AP punya
      satu lapis tambahan, karena di AP tanggal bayar memang BUKAN tanggal
      jatuh tempo - pembayaran dilakukan hari Kamis.

      Voucher yang belum pernah dijadwalkan TETAP masuk; kalau hanya yang
      sudah dijadwalkan yang masuk, proyeksi kasnya bocor.

      AKIBATNYA ANGKA PER KOLOM BERGESER dibanding versi sebelumnya yang
      memakai jatuh tempo mentah - memang itu yang diminta. Pada data
      lokal, 95,6% voucher jatuh temponya BUKAN hari Kamis sehingga pindah
      kolom tanggal, dan 3,4% bahkan pindah bulan (mis. jatuh tempo Senin
      30 Nov -> Kamis 3 Des).

   4. ARTI Daily / Weekly / Monthly - DISAMAKAN DGN ar_dev (diminta
      pemakai 6 Nov 2026: "ketentuan daily monthly dan weekly samakan
      dengan ar_dev ya termasuk pengambilan rate nya dan tanggal from to
      nya").

      Yang penting dipahami: di AR, type TIDAK menggabungkan kolom.
      renderProjectionHeader() di crud-nag-report.js selalu melangkah SATU
      HARI dari From sampai To, apa pun type-nya. Jadi di sini pun KOLOM
      TANGGAL SELALU SATU PER HARI. Keranjang mingguan/bulanan yang dulu
      ada di berkas ini sudah dibuang.

      Yang benar-benar diatur type cuma dua:

        a) RENTANG From-To yang boleh diminta
           (ar_dev crud-nag-report.js, cari_projection_report):
             weekly  : From hari MINGGU, To hari SABTU, tepat 7 hari
             monthly : From tanggal 1, To hari terakhir BULAN YANG SAMA
             daily   : bebas (di sini dibatasi 62 hari, lihat pagarnya)

        b) TANGGAL ACUAN KURS
           (ar_dev Model_report::_tanggal_rate, ditiru di apprTglKurs()):
             daily   : hari ini
             weekly  : To - 8 hari  -> Jumat sebelum pekan Minggu-Sabtu itu
             monthly : akhir bulan SEBELUM periode
           Kursnya diambil dari baris v_codecurr = 'HARIAN', mundur ke
           tanggal terisi terakhir yang <= acuan.

      AKIBAT YANG PERLU DISADARI: karena monthly kini terikat satu bulan
      dan daily terbatas 62 hari, laporan ini TIDAK BISA LAGI menampilkan
      setahun sekaligus. Dulu bisa, lewat monthly 12 kolom. Itu memang
      konsekuensi menyamakan aturannya dgn AR, bukan kelalaian.

   ----------------------------------------------------------------------
   RUMUS KOLOM UANG - dicocokkan ke lembar "Projection" di
   "AP Payment Realization & Variance Report Ver 1.1.xlsx", lalu diuji ke
   2.000 voucher:
       Payment Amount    = kontrabon_h.total          (SUDAH net PPh 23)
       Income Tax Art 23 = -pph_idr
       Total Invoice     = total + pph_idr
       VAT               = tax
       Tax Base          = total + pph_idr - tax
   Kolom subtotal TIDAK dipakai: isinya tidak konsisten antar jenis dokumen
   (SI/APR/NAG/2026/06/01376 berisi 109.337.000 padahal totalnya 54.668.500).
   ============================================================================ */

include '../../conn/conn.php';
/* ---------------------------------------------------------------------------
   BISA DIPAKAI SEBAGAI PUSTAKA. Ekspor Excel (ekspor_ap_projection_report.php)
   dan simpan history (simpan_history_ap_projection.php) mendefinisikan
   APPR_PUSTAKA, menyetel $_POST, lalu meng-include berkas ini dan menangkap
   keluarannya. Dgn begitu angka di Excel dan di history DIJAMIN sama dgn yang
   di layar - satu query, bukan tiga salinan yang suatu saat melenceng.

   Dua hal yang WAJIB digerbangi dalam keadaan itu:
     - header() JSON, karena pemanggilnya sudah mengirim header .xls sendiri
       dan header yang belakangan menang;
     - exit, karena exit di dalam include mematikan SELURUH skrip pemanggil,
       sehingga kesalahan apa pun akan menghasilkan berkas Excel kosong tanpa
       pesan. Diganti return, yang hanya keluar dari include ini.
   --------------------------------------------------------------------------- */
if (!defined('APPR_PUSTAKA')) { header('Content-Type: application/json'); }

/* Isian tanggal halaman berbentuk dd-mm-yyyy. */
function apprTgl($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));
    return $t ? date('Y-m-d', $t) : '';
}

$mulai  = apprTgl(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$akhir  = apprTgl(isset($_POST['end_date']) ? $_POST['end_date'] : '');
$supp   = trim(isset($_POST['nama_supp']) ? $_POST['nama_supp'] : '');
/* Saringan Relationship & Item Type DIHAPUS atas permintaan pemakai
   (5 Nov 2026). KOLOMNYA di tabel tetap ada, jadi isinya masih terbaca dan
   masih bisa disaring lewat kotak Search milik DataTables. */
$type   = isset($_POST['type']) ? strtolower($_POST['type']) : 'daily';
if (!in_array($type, array('daily', 'weekly', 'monthly'), true)) { $type = 'daily'; }

if ($mulai === '' || $akhir === '') {
    echo json_encode(array('error' => 'Please fill in both the period From and To dates.'));
    if (defined('APPR_PUSTAKA')) { return; }
    exit;
}
if ($mulai > $akhir) {
    echo json_encode(array('error' => 'The From date must not be later than the To date.'));
    if (defined('APPR_PUSTAKA')) { return; }
    exit;
}
/* ---------------------------------------------------------------------------
   RENTANG from-to MENURUT TYPE - aturannya DISAMAKAN DGN ar_dev (diminta
   pemakai 6 Nov 2026). Di AR pagarnya ada di crud-nag-report.js
   cari_projection_report():

     weekly  : From hari MINGGU, To hari SABTU, tepat 7 hari
     monthly : From tanggal 1, To hari terakhir BULAN YANG SAMA
     daily   : bebas

   Di sini dijaga DI SERVER, bukan cuma di layar seperti AR. Alasannya
   endpoint ini bisa dipanggil langsung (penjaga, ekspor, tautan lama), dan
   rentang weekly yang tidak Minggu-Sabtu membuat tanggal acuan kursnya
   ($akhir - 8 hari) menunjuk hari yang bukan Jumat - kursnya jadi salah
   tanpa satu pun tanda di layar. Layarnya tetap diberi pagar yang sama
   supaya pesannya ramah. */
$hari = (int) ((strtotime($akhir) - strtotime($mulai)) / 86400) + 1;
if ($type === 'weekly') {
    /* date('w'): 0=Minggu, 6=Sabtu */
    if (date('w', strtotime($mulai)) !== '0' || date('w', strtotime($akhir)) !== '6'
        || $hari !== 7) {
        echo json_encode(array('error' => 'Weekly type requires a Sunday to Saturday '
            . 'range (exactly 7 days). From must be Sunday, To must be Saturday.'));
        if (defined('APPR_PUSTAKA')) { return; }
        exit;
    }
} elseif ($type === 'monthly') {
    if (date('j', strtotime($mulai)) !== '1'
        || date('Y-m', strtotime($mulai)) !== date('Y-m', strtotime($akhir))
        || date('d', strtotime($akhir)) !== date('t', strtotime($akhir))) {
        echo json_encode(array('error' => 'Monthly type requires a full month range '
            . '(1st to last day of the same month).'));
        if (defined('APPR_PUSTAKA')) { return; }
        exit;
    }
}
/* Kolom tanggal SELALU satu per hari (lihat butir 4 di kepala berkas), jadi
   rentang yang kelewat lebar menghasilkan tabel yang tidak terbaca DAN berat
   digambar. weekly (7 hari) & monthly (<= 31 hari) selalu lolos sendiri;
   pagar ini praktis hanya mengikat daily. */
if ($hari > 62) {
    echo json_encode(array('error' => 'The projection is limited to 62 days. '
        . 'Pick a shorter period.'));
    if (defined('APPR_PUSTAKA')) { return; }
    exit;
}

/* ---------------------------------------------------------------------------
   1. SISA UTANG per sehari sebelum periode - lewat query AP Report.
   query_pv2.php membaca $_REQUEST, jadi isinya disetel dulu. 'search' milik
   DataTables sengaja dikosongkan: di sini penyaringnya bukan kotak itu.
   --------------------------------------------------------------------------- */
$perTanggal = date('Y-m-d', strtotime($mulai . ' -1 day'));

$simpanPost = $_POST;
$_REQUEST['start_date'] = $perTanggal;
$_REQUEST['end_date']   = $perTanggal;
$_REQUEST['nama_supp']  = ($supp === '' ? 'ALL' : $supp);
unset($_POST['search']);

$dirAsal = getcwd();
chdir(__DIR__ . '/ap_report2');
include 'query_pv2.php';          /* mengisi $sql (dan $where, $start_date, $end_date) */
chdir($dirAsal);
$_POST = $simpanPost;

/* ---------------------------------------------------------------------------
   2. Rencana bayar yang BERLAKU per voucher: baris 'Y' milik dokumen yang
   belum dibatalkan, diambil dari dokumen terbaru. Bentuknya sama persis dgn
   yang dipakai layar Update Payment Plan dan dgn CTE "duedate" di laporan
   proyeksi AR.
   --------------------------------------------------------------------------- */
$rencana = "(SELECT d.no_kbon, d.plan_date, d.doc_number
             FROM ap_payment_plan_det d
             INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
             INNER JOIN (SELECT d2.no_kbon, MAX(h2.id) id_terbaru
                         FROM ap_payment_plan_det d2
                         INNER JOIN ap_payment_plan_h h2 ON h2.doc_number = d2.doc_number
                         WHERE d2.status = 'Y' AND h2.status <> 'CANCEL'
                         GROUP BY d2.no_kbon) t
                     ON t.no_kbon = d.no_kbon AND t.id_terbaru = h.id
             WHERE d.status = 'Y' AND h.status <> 'CANCEL')";

$fMulai = mysqli_real_escape_string($conn2, $mulai);
$fAkhir = mysqli_real_escape_string($conn2, $akhir);
/* $perTanggal TIDAK di-escape jadi $fPer lagi: sejak acuan kurs mengikuti
   aturan ar_dev, satu-satunya query yang memakai tanggal di sini adalah
   query kurs - dan itu memakai $fKurs. $perTanggal sendiri tetap dipakai,
   lewat $_REQUEST, oleh query_pv2.php. */

/* ---------------------------------------------------------------------------
   KURS - DIAMBIL SENDIRI, TIDAK MEMAKAI kolom rate milik query_pv2.php.

   Di sana kursnya dicari dgn `tanggal = '$end_date'` PERSIS. Kalau tanggal itu
   tidak ada isinya di ap_masterrate, LEFT JOIN-nya kosong dan IFNULL(rate,1)
   membuat nilai mata uang asing terhitung SATU BANDING SATU sebagai rupiah -
   salah besar, dan tidak ada tanda apa pun di layar. Terbukti nyata: untuk
   tanggal acuan 2026-09-30 tabel kurs TIDAK punya baris HARIAN sama sekali,
   sehingga sisa utang USD 3.308 terbaca sebagai Rp 3.308.

   TANGGAL ACUANNYA kini DISAMAKAN DGN ar_dev (diminta pemakai 6 Nov 2026),
   yaitu Model_report::_tanggal_rate() - lihat apprTglKurs() di bawah.

   Yang BERBEDA dari AR, dan memang disengaja: AR mengambil satu angka saja
   (_rate_dn() membaca ap_masterrate TANPA menyaring curr, karena yang
   dibutuhkannya cuma USD untuk debit note). Laporan ini bermata uang banyak
   - voucher IDR, USD, dan RMB bercampur - jadi aturan yang sama diterapkan
   PER MATA UANG. Menyalin AR apa adanya akan membuat kurs RMB terpakai untuk
   baris USD.

   Mata uang yang tetap tidak ketemu kursnya DILAPORKAN ke layar - bukan
   diam-diam dianggap 1 seperti AR.
   --------------------------------------------------------------------------- */

/* Tanggal acuan kurs menurut type - salinan aturan ar_dev
   Model_report::_tanggal_rate():
     daily   : hari ini
     weekly  : $akhir - 8 hari, yaitu Jumat sebelum periode Minggu-Sabtu itu
     monthly : akhir bulan SEBELUM periode

   Akhir bulan sebelumnya TIDAK boleh dihitung dgn '-1 month': kalau $akhir
   tanggal 31 dan bulan sebelumnya cuma 30 hari, PHP melimpahkannya balik ke
   bulan yang sama (31 Okt -1 month = 1 Okt), jadi yang terbaca malah akhir
   bulan berjalan - tanggal yang kursnya belum ada, dan kursnya jatuh ke 1.
   Yang aman: mundur satu hari dari tanggal 1 bulan periode. (Keterangan ini
   ikut disalin dari AR supaya jebakannya tidak ditemukan ulang di sini.) */
function apprTglKurs($type, $akhir)
{
    if ($type === 'daily')   { return date('Y-m-d'); }
    if ($type === 'monthly') {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($akhir)) . ' -1 day'));
    }
    return date('Y-m-d', strtotime($akhir . ' -8 day'));
}
$tglKurs  = apprTglKurs($type, $akhir);
$fKurs    = mysqli_real_escape_string($conn2, $tglKurs);

$kurs     = array('IDR' => 1.0);
$kursTgl  = array();
/* v_codecurr = 'HARIAN' mengikuti AR. Mundurnya ke tanggal terisi terakhir
   yang <= acuan juga dari AR (ORDER BY tanggal DESC LIMIT 1) - tanpa itu
   kursnya jatuh ke 1 setiap kali tanggal acuannya hari libur atau belum
   diinput. */
$qk = mysqli_query($conn2, "
    SELECT a.curr, a.rate, a.tanggal
    FROM ap_masterrate a
    INNER JOIN (SELECT curr, MAX(tanggal) AS t FROM ap_masterrate
                WHERE v_codecurr = 'HARIAN' AND rate > 0 AND tanggal <= '$fKurs'
                GROUP BY curr) m
            ON m.curr = a.curr AND m.t = a.tanggal
    WHERE a.v_codecurr = 'HARIAN' AND a.rate > 0
    GROUP BY a.curr");
if ($qk) {
    while ($rk = mysqli_fetch_assoc($qk)) {
        if ((float) $rk['rate'] > 0) {
            $kurs[strtoupper($rk['curr'])]    = (float) $rk['rate'];
            $kursTgl[strtoupper($rk['curr'])] = $rk['tanggal'];
        }
    }
}
$kursHilang = array();

/* ---------------------------------------------------------------------------
   TANGGAL PROYEKSI - lihat butir 3 di keterangan kepala berkas.

   Rencana awalnya DIHITUNG LANGSUNG kalau barisnya belum ada di
   ap_payment_plan_awal, supaya laporan ini tidak berubah isi hanya karena
   migrasinya belum dijalankan di suatu basis data. Rumus yang persis sama
   dipakai ajx_cari-pv-payment-plan.php: DAYOFWEEK memberi 1=Minggu dan
   Kamis=5, jadi Kamis di minggu yang sama = duedate + (5 - DAYOFWEEK) hari
   - nilainya negatif untuk Jumat & Sabtu, dan memang begitu maunya.

   SATU tempat yang memutuskan tanggalnya, lalu dipakai TIGA kali (daftar
   kolom, saringan periode, urutan). Kalau ditulis tiga kali, suatu saat yang
   satu berubah dan dua lainnya tidak - dan laporannya diam-diam memuat baris
   di luar periode yang diminta. */
$rumusKamis = "DATE_ADD(x.duedate, INTERVAL (5 - DAYOFWEEK(x.duedate)) DAY)";

/* TABELNYA BOLEH BELUM ADA. Migrasi ap_payment_plan_awal dijalankan terpisah
   di produksi, dan LEFT JOIN ke tabel yang belum dibuat membuat SELURUH query
   gagal - laporannya mati, bukan sekadar satu kolom kosong. Jadi tabelnya
   diperiksa dulu.

   Ini BUKAN modus darurat yang hasilnya lebih buruk: migrasinya memakai rumus
   Kamis yang sama persis, jadi selama belum ada baris MANUAL, hasil hitung
   langsung = hasil baca tabel. Yang hilang hanya kemampuan menyetel rencana
   awal satu voucher dgn tangan. */
$adaTabelAwal = false;
$qta = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_payment_plan_awal'");
if ($qta && mysqli_num_rows($qta) > 0) { $adaTabelAwal = true; }

/* HANYA rencana awal yang DISETEL ORANG yang dipakai apa adanya; yang
   sumber = 'AUTO' SELALU dihitung ulang dari jatuh tempo yang ditampilkan
   baris ini.

   Alasannya ditemukan penjaga: baris AUTO di tabel itu tidak punya wibawa
   sendiri - isinya semata rumus Kamis yang diterapkan ke jatuh tempo SAAT
   MIGRASI dijalankan, dan migrasinya membacanya dari kontrabon_h. Padahal
   261 voucher muncul di kontrabon_h DAN ap_saldo_payment_voucher sekaligus,
   2 di antaranya dgn jatuh tempo yang BERBEDA - dan laporan ini menampilkan
   jatuh tempo versi query AP Report. Akibatnya baris SI/APR/NAG/2026/06/01485
   sempat memperlihatkan Due Date 27 Mei tapi Initial Plan 25 Juni: dua minggu
   yang sama sekali berlainan, dan tidak ada cara bagi pemakai untuk tahu mana
   yang benar.

   Dihitung ulang, Initial Plan DIJAMIN sepekan dgn Due Date yang tertulis di
   baris yang sama - dan ikut terkoreksi sendiri kalau jatuh temponya diubah
   setelah migrasi berjalan. Yang disetel tangan tetap menang, karena itu
   justru keputusan yang tidak boleh dihitung ulang. */
$awal = $adaTabelAwal
    ? "COALESCE(CASE WHEN IFNULL(pa.sumber,'AUTO') <> 'AUTO' THEN pa.plan_awal END, $rumusKamis)"
    : $rumusKamis;
$joinAwal = $adaTabelAwal
    /* no_kbon UNIK di tabel itu, jadi join ini tidak bisa menggandakan baris
       laporan - beda dgn mastersupplier di atas. */
    ? "LEFT JOIN ap_payment_plan_awal pa ON pa.no_kbon = x.no_kbon"
    : "";
$eProyeksi = "COALESCE(p.plan_date, $awal, x.duedate)";

/* ROUND(...,2) <> 0, BUKAN <> 0: saldo_akhir_idr menyisakan pecahan
   pembulatan (ada baris 0,01 dan -0,01), dan tanpa pembulatan baris-baris itu
   ikut terbawa sbg "masih berutang". */
$sqlLuar = "
    SELECT x.supplier, x.no_kbon, x.curr, x.relasi, x.item_type1,
           x.duedate, x.saldo_akhir, x.saldo_akhir_idr,
           COALESCE(k.tgl_kbon, sa.tgl_kbon, ki.tgl_kbon)         AS tgl_kbon,
           COALESCE(k.tax, sa.tax, ins.ppn, 0)                    AS tax,
           COALESCE(k.pph_idr, sa.pph, ins.pph, 0)                AS pph,
           COALESCE(k.total, sa.total, ins.total, 0)              AS total,
           /* PAYMENT TERM = TOP menurut PO, SAMA untuk semua cicilan satu
              dokumen - bukan jarak hari ke jatuh tempo cicilan itu.

              Dulu urutannya k -> sa -> ins. Untuk baris CICILAN, `k` tidak
              pernah cocok (nomornya bersufiks ' - 10'), jadi yang terpakai
              `sa`, dan DATEDIFF di sana mengukur jarak ke jatuh tempo
              cicilan KE-10 - hasilnya 300 hari, cicilan ke-9 270, ke-4 120.
              Terus menumpuk tiap cicilan, dilaporkan pemakai 6 Nov 2026.

              Sekarang dokumen INDUKNYA yang ditanya (join `ind` di bawah):
              DATEDIFF(induk.tgl_tempo, induk.tgl_kbon). Diperiksa ke empat
              contoh pemakai - 02515-10, 02773-9, 01430-4 dan 01376 -
              keempatnya 30, dan itu memang TOP PO-nya. Cara yang sama
              dipakai layar Payment Plan (ajx_cari-pv-payment-plan.php
              baris 143), jadi dua layar itu kini sejalan.

              `sa` TETAP jadi cadangan TERAKHIR, bukan dibuang: dokumen
              saldo awal yang induknya tidak ada di kontrabon_h hanya punya
              angka itu - lebih baik angka yang menumpuk daripada kosong. */
           COALESCE(DATEDIFF(k.tgl_tempo,   k.tgl_kbon),
                    DATEDIFF(ind.tgl_tempo, ind.tgl_kbon),
                    ins.top,
                    DATEDIFF(sa.tgl_tempo,  sa.tgl_kbon))         AS term,
           IFNULL(s.payment_rating, '') AS flexibility,
           p.plan_date, p.doc_number AS plan_doc,
           $awal AS plan_awal,
           $eProyeksi AS tgl_proyeksi
    FROM ($sql) x
    LEFT JOIN kontrabon_h k ON k.no_kbon = x.no_kbon

    /* DOKUMEN INDUK dari sebuah baris cicilan, dipakai kolom Payment Term.
       DUA bentuk sufiks yang hidup berdampingan:
         'SI/APR/NAG/2025/12/02515 - 10'  (spasi-strip-spasi, dokumen lama
                                           & baris saldo awal)
         'PV-AP/INS/NAG/2026/08/00001-001' (strip + tiga angka, cicilan baru)
       Nomor yang bukan cicilan menunjuk dirinya sendiri, jadi join ini tidak
       pernah mengubah apa pun untuk baris biasa. Sufiks revisi '-REV_01'
       sengaja TIDAK dipotong - itu dokumen tersendiri, bukan cicilan. */
    LEFT JOIN kontrabon_h ind ON ind.no_kbon =
        CASE WHEN x.no_kbon LIKE '% - %'
                  THEN SUBSTRING_INDEX(x.no_kbon, ' - ', 1)
             WHEN x.no_kbon REGEXP '-[0-9]{3}\$'
                  THEN LEFT(x.no_kbon, CHAR_LENGTH(x.no_kbon) - 4)
             ELSE x.no_kbon END

    /* TIGA SUMBER NILAI DOKUMEN, dipakai berurutan lewat COALESCE. Tidak
       semua baris laporan ini berasal dari kontrabon_h:
         - saldo awal (ap_saldo_payment_voucher) untuk utang pembukaan,
         - cicilan (kontrabon_h_installment_detail) untuk nomor anak
           seperti PV-AP/INS/.../00001-002.
       Tanpa keduanya nilai dokumennya tampil 0 padahal sisa utangnya ada -
       dan lembar contoh tim AP justru memuat baris-baris itu lengkap.

       SEMUA cadangan ini DIKELOMPOKKAN DULU. Itu bukan hiasan: tabel
       sumbernya bisa memuat lebih dari satu baris untuk satu nomor, dan
       LEFT JOIN mentah akan MENGGANDAKAN baris laporan - nilainya ikut
       terhitung dua kali di total. Terbukti pada mastersupplier, yang
       punya DUA baris untuk 'PT. OBOR MAJU JAYA' sehingga
       PV-AP/REG/NAG/2026/09/02217 sempat muncul dua kali. */
    LEFT JOIN (SELECT no_kbon, MAX(tgl_kbon) AS tgl_kbon, MAX(tgl_tempo) AS tgl_tempo,
                      MAX(tax) AS tax, MAX(pph) AS pph, MAX(total) AS total
               FROM ap_saldo_payment_voucher GROUP BY no_kbon) sa
           ON sa.no_kbon = x.no_kbon
    LEFT JOIN (SELECT no_kbon_det, MAX(no_kbon) AS induk, MAX(top) AS top,
                      SUM(ppn) AS ppn, SUM(pph) AS pph, SUM(total) AS total
               FROM kontrabon_h_installment_detail GROUP BY no_kbon_det) ins
           ON ins.no_kbon_det = x.no_kbon
    LEFT JOIN kontrabon_h ki ON ki.no_kbon = ins.induk
    LEFT JOIN (SELECT Supplier, MAX(payment_rating) AS payment_rating
               FROM mastersupplier GROUP BY Supplier) s
           ON s.Supplier = x.supplier
    LEFT JOIN $rencana p ON p.no_kbon = x.no_kbon
    $joinAwal
    WHERE ROUND(x.saldo_akhir_idr, 2) <> 0
      AND $eProyeksi BETWEEN '$fMulai' AND '$fAkhir'
    ORDER BY $eProyeksi ASC, x.supplier ASC, x.no_kbon ASC";

$res = mysqli_query($conn2, $sqlLuar);
if ($res === false) {
    echo json_encode(array('error' => mysqli_error($conn2)));
    if (defined('APPR_PUSTAKA')) { return; }
    exit;
}

/* ---------------------------------------------------------------------------
   3. KOLOM TANGGAL - SELALU SATU KOLOM PER HARI, untuk KETIGA type.

   Ini aturan ar_dev (diminta pemakai 6 Nov 2026). Di AR, type TIDAK pernah
   menggabungkan kolom: renderProjectionHeader() di crud-nag-report.js
   melangkah satu hari sekali dari From sampai To, apa pun type-nya. Yang
   diatur type di AR hanya DUA hal - rentang from-to yang boleh dipakai, dan
   tanggal acuan kursnya.

   Jadi weekly selalu 7 kolom (Minggu-Sabtu) dan monthly sebanyak hari di
   bulan itu. Keranjang mingguan/bulanan yang dulu ada DIBUANG, bukan
   dinonaktifkan: menyisakannya berarti ada dua aturan keranjang di satu
   berkas dan suatu saat yang terpakai bukan yang dimaksud.
   --------------------------------------------------------------------------- */
$kolom = array();
$urut  = array();
$jalan = $mulai;
while ($jalan <= $akhir) {
    $kolom[$jalan] = date('d M Y', strtotime($jalan));
    $urut[] = $jalan;
    $jalan = date('Y-m-d', strtotime($jalan . ' +1 day'));
}

/* ---------------------------------------------------------------------------
   4. Baris.
   --------------------------------------------------------------------------- */
function apprAngka($v) { return number_format((float) $v, 2, '.', ','); }
function apprRelasi($v)
{
    $v = strtoupper(trim((string) $v));
    if ($v === 'GROUP')     { return 'Related Party'; }
    if ($v === 'NON GROUP') { return 'Third Party'; }
    return $v !== '' ? $v : '-';
}
function apprTampilTgl($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d-m-Y', strtotime($v)) : '-';
}
function apprAdaTgl($v) { return (!empty($v) && $v !== '0000-00-00'); }
/* Lapis mana yang akhirnya dipakai - URUTANNYA WAJIB sama dgn COALESCE di
   $eProyeksi. Dibaca dari kolom SUMBERNYA, bukan dgn membandingkan
   tanggalnya: kalau rencana PPU kebetulan sama dgn rencana awal,
   perbandingan tanggal akan menyebutnya "Initial plan" padahal voucher itu
   memang sudah dijadwalkan ulang. */
function apprBasis($row)
{
    if (apprAdaTgl($row['plan_date'])) { return 'Updated plan'; }
    if (apprAdaTgl($row['plan_awal'])) { return 'Initial plan'; }
    return 'Due date';
}

$data   = array();
$jumlah = array_fill_keys($urut, 0.0);
$no     = 0;
$totalProyeksi = 0.0;

while ($row = mysqli_fetch_assoc($res)) {
    $no++;

    $mata = strtoupper((string) $row['curr']);
    if (!isset($kurs[$mata])) { $kursHilang[$mata] = true; }
    $rate  = isset($kurs[$mata]) ? $kurs[$mata] : 1.0;

    $pph   = (float) $row['pph'];
    $bayar = (float) $row['total'];          /* Payment Amount, sudah net PPh */
    $tax   = (float) $row['tax'];
    $inv   = $bayar + $pph;                  /* Total Invoice (mata uang dokumen) */
    $base  = $inv - $tax;                    /* Tax Base */

    /* Yang BENAR-BENAR akan keluar = SISA utangnya, bukan nilai dokumen.
       Untuk voucher yang belum dibayar sama sekali keduanya sama; untuk yang
       sudah dibayar sebagian, hanya sisanya yang masih jadi arus kas.

       Dikalikan kurs DI SINI dari saldo_akhir (mata uang dokumen), bukan
       memakai saldo_akhir_idr - lihat keterangan kurs di atas. */
    $sisa = round((float) $row['saldo_akhir'] * $rate, 2);

    /* Kunci kolomnya = tanggal proyeksinya apa adanya; tidak ada keranjang
       lagi. Baris di luar periode mustahil sampai sini - $eProyeksi sudah
       menyaringnya di WHERE - tapi penjagaan isset() dipertahankan supaya
       satu baris nyasar tidak memunculkan notice & menghilangkan angkanya. */
    $k = date('Y-m-d', strtotime($row['tgl_proyeksi']));
    if (!isset($jumlah[$k])) { $jumlah[$k] = 0.0; }
    $jumlah[$k] += $sisa;
    $totalProyeksi += $sisa;

    $b = array(
        'no'            => $no,
        'supplier'      => htmlspecialchars((string) $row['supplier'], ENT_QUOTES),
        'no_kbon'       => htmlspecialchars((string) $row['no_kbon'], ENT_QUOTES),
        'tgl_kbon'      => apprTampilTgl($row['tgl_kbon']),
        'term'          => ($row['term'] === null ? '-' : (string) $row['term']),
        'duedate'       => apprTampilTgl($row['duedate']),
        /* TIGA ini TIDAK punya kolom sendiri di layar: lembar "Projection"
           hanya mengenal satu kolom Payment Plan Date, dan itu yang diikuti
           (ditegaskan pemakai 6 Nov 2026). Tetap dikirim karena:
             - 'basis' dipakai jadi tooltip kolom Payment Plan Date, jadi
               dasar tanggalnya tetap bisa ditanyakan tanpa menambah kolom;
             - 'plan_awal' & 'plan_date' adalah BAHAN tanggal itu, dan
               itulah yang membuat angkanya bisa diperiksa dari luar -
               penjaga menghitung ulang tanggal proyeksinya dari ketiganya,
               sehingga SELECT yang tidak sejalan dgn WHERE ketahuan.
           Jadi jangan dibuang hanya karena tidak tampak di tabel. */
        'plan_awal'     => apprTampilTgl($row['plan_awal']),
        'plan_date'     => apprTampilTgl($row['plan_date']),
        'tgl_proyeksi'  => apprTampilTgl($row['tgl_proyeksi']),
        'basis'         => apprBasis($row),
        'relasi'        => htmlspecialchars(apprRelasi($row['relasi']), ENT_QUOTES),
        'item_type'     => htmlspecialchars(((string) $row['item_type1'] !== '') ? strtoupper($row['item_type1']) : '-', ENT_QUOTES),
        'flexibility'   => htmlspecialchars(((string) $row['flexibility'] !== '') ? $row['flexibility'] : '-', ENT_QUOTES),
        'curr'          => htmlspecialchars((string) $row['curr'], ENT_QUOTES),
        'invoice_amount'=> apprAngka($inv),
        'rate'          => apprAngka($rate),
        'tax_base'      => apprAngka($base * $rate),
        'vat'           => apprAngka($tax * $rate),
        'total_invoice' => apprAngka($inv * $rate),
        'pph'           => apprAngka(-1 * $pph * $rate),
        'payment_amount'=> apprAngka($bayar * $rate),
        'outstanding'   => apprAngka($sisa),
    );
    foreach ($urut as $kk) { $b['d_' . $kk] = ($kk === $k) ? apprAngka($sisa) : ''; }
    $data[] = $b;
}

/* Kurs yang dipakai, hanya untuk mata uang yang benar-benar muncul di hasil.
   'acuan' = tanggal yang DIMINTA aturan type; 'tanggal' = tanggal yang
   benar-benar ketemu isinya (bisa lebih awal kalau acuannya hari libur atau
   belum diinput). Dua-duanya dikirim: kalau hanya yang kedua, selisih
   keduanya - yang bisa berbulan-bulan, seperti di basis data uji ini -
   tidak kelihatan sama sekali. */
$kursDipakai = array();
foreach ($data as $b) {
    $m = strtoupper($b['curr']);
    if ($m === 'IDR' || isset($kursDipakai[$m])) { continue; }
    $kursDipakai[$m] = array(
        'rate'    => isset($kurs[$m]) ? apprAngka($kurs[$m]) : '-',
        'tanggal' => isset($kursTgl[$m]) ? date('d M Y', strtotime($kursTgl[$m])) : '-',
        'acuan'   => date('d M Y', strtotime($tglKurs)),
    );
}

$kolomKirim = array();
foreach ($urut as $k) {
    $kolomKirim[] = array('key' => 'd_' . $k, 'label' => $kolom[$k],
                          'total' => apprAngka(isset($jumlah[$k]) ? $jumlah[$k] : 0));
}

echo json_encode(array(
    'data'   => $data,
    'kolom'  => $kolomKirim,
    'info'   => array(
        'periode'     => date('d M Y', strtotime($mulai)) . ' s/d ' . date('d M Y', strtotime($akhir)),
        'per_tanggal' => date('d M Y', strtotime($perTanggal)),
        'type'        => $type,
        'baris'       => count($data),
        /* false = tabel rencana awal belum dibuat di basis data ini, jadi
           kolom Initial Plan dihitung langsung dari jatuh tempo. */
        'tabel_awal'  => $adaTabelAwal,
        'total'       => apprAngka($totalProyeksi),
        /* Kurs yang benar-benar dipakai, berikut tanggal berlakunya - supaya
           angka rupiahnya bisa ditelusuri, bukan percaya begitu saja. */
        'kurs'        => $kursDipakai,
        /* Mata uang yang kursnya TIDAK ketemu sama sekali. Kalau ada, layarnya
           WAJIB memperingatkan: nilainya terpaksa dihitung 1:1 dan totalnya
           tidak bisa dipercaya. */
        'kurs_hilang' => array_keys($kursHilang),
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
?>
