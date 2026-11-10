<?php
/* ============================================================================
   AP PAYMENT REALIZATION & VARIANCE REPORT - penyedia datanya.
   Pasangannya ap_realization_report.php.

   Bentuk & kosakatanya dicocokkan ke
   "D:\File Project\AP Payment Realization & Variance Report Ver 1.1.xlsx"
   (lembar "AP Payment Realization & Var", "Realization", "Projection", dan
   "Executive Summary"), tata letaknya mengikuti laporan sejenis di ar_dev
   (Model_report::realization_report + views/arnag/report/realization_report).

   ----------------------------------------------------------------------
   ACUANNYA SATU DOKUMEN PROYEKSI, BUKAN RENTANG TANGGAL
   ----------------------------------------------------------------------
   Laporan ini TIDAK punya menu sendiri - dibuka dari tombol "Realization" di
   daftar AP History Projection, persis seperti di ar_dev. Alasannya: yang
   dibandingkan adalah POTRET proyeksi yang sudah tersimpan dengan pembayaran
   yang benar-benar terjadi di periode potret itu. Tanpa dokumen, tidak ada
   yang bisa dibandingkan - "proyeksi" yang dihitung ulang hari ini sudah
   memperhitungkan pembayaran yang mau diukur, jadi selisihnya selalu nol.

   SISI PROYEKSI dibaca dari ap_projection_history_det APA ADANYA. Tidak
   dihitung ulang: kurs, rencana bayar (PPU), dan master supplier semuanya
   bisa berubah sesudah Save, sedangkan yang mau diukur justru janji yang
   dibuat saat itu.

   SISI REALISASI dibaca dari tbl_list_journal, dgn EMPAT saluran pembayaran
   yang sama persis dgn ap_report2/query_pv2.php:
       BK  (bank)      type_journal '%Payment Voucher%'  + no_journal '%BK%'
       KKK (kas)       type_journal '%Payment Voucher%'  + no_journal '%KKK%'
       PAY (non bank)  type_journal '%Payment Non Bank%' + no_journal '%PAY%'
       GM  (reklas)    type_journal = 'ACCOUNT PAYABLE' + reff_doc SI/APR|PV-AP
   semuanya pada baris ber-nama_coa '%UTANG USAHA%', DIKURANGI potongan PPh
   pada baris 2.52.xx di dokumen yang sama (lihat keterangan panjang di atas
   $sqlBayar - yang dilaporkan KAS yang keluar, bukan mutasi buku utang),
   dikelompokkan per reff_doc. Pola LIKE-nya DISALIN UTUH dari query_pv2.php
   dan jangan
   "dirapikan": kalau pola di sini berbeda satu huruf saja, laporan ini dan
   AP Report akan menyebut angka terbayar yang berbeda, dan itu jenis selisih
   yang paling lama ketahuannya. Jurnal balik ikut tertangkap pola yang sama
   dgn tanda berlawanan, jadi pembayaran yang dibatalkan otomatis nol - itu
   sebabnya penyaringnya TIDAK boleh ditambah 'AND debit > 0'.

   debit/credit = mata uang dokumen (OCY), debit_idr/credit_idr = rupiah;
   diperiksa pada 686 baris pembayaran Jul-Sep 2026: idr/ocy selalu sama dgn
   kolom rate, dan tidak ada satu pun baris ber-debit_idr nol.

   ----------------------------------------------------------------------
   SATU BEDA YANG DISENGAJA DARI LEMBAR EXCEL-nya
   ----------------------------------------------------------------------
   Di lembar Excel, kolom "Amount Eqv Idr" sisi Projection diambil dari
   kolom Payment Amount lembar Projection - yaitu NILAI DOKUMEN voucher-nya.
   Di sini yang dipakai OUTSTANDING: sisa utang yang memang diproyeksikan
   keluar, kolom yang sama yang mengisi kisi tanggal di Projection Report dan
   yang menjadi total_idr di kepala dokumennya.

   Sebabnya: untuk voucher yang belum pernah dibayar keduanya sama, tapi
   untuk voucher yang sudah dibayar sebagian hanya sisanya yang masih jadi
   arus kas. Kalau memakai nilai dokumen, "Projected Payment" di laporan ini
   tidak akan sama dgn total dokumen proyeksinya sendiri - dan dokumen itulah
   acuan yang dijanjikan. Nilai dokumennya tetap dikirim (voucher_amount)
   supaya bedanya bisa dilihat, bukan hilang.

   ----------------------------------------------------------------------
   ANGKA DIKIRIM MENTAH
   ----------------------------------------------------------------------
   Beda dgn ajx_ap_projection_report.php yang mengirim angka sudah
   berformat: di sini angkanya dikirim sebagai angka (float/null), dan
   diformat di tepi - di layar oleh JS, di Excel oleh number format
   PhpSpreadsheet. Dua alasannya: DataTables bisa mengurutkan kolom uang
   sebagai angka (penting, laporan ini dipakai untuk mencari yang paling
   melenceng), dan Excel menerima ANGKA sungguhan, bukan teks yang kebetulan
   mirip angka. Tanggal dikirim ISO (Y-m-d) atau '' - bukan d-m-Y - supaya
   urutan dan hitungan hari tidak bergantung pada pembacaan teks.
   ============================================================================ */

include '../../conn/conn.php';

/* Bisa dipakai sebagai pustaka: ekspor Excel mendefinisikan APRV_PUSTAKA,
   menyetel $_POST, lalu meng-include berkas ini dan menangkap keluarannya.
   Dalam mode itu yang dilewati header() JSON (pemanggilnya sudah mengirim
   headernya sendiri) dan exit (pemanggilnya masih harus menyusun berkas). */
if (!defined('APRV_PUSTAKA')) { header('Content-Type: application/json'); }

function aprvSalah($pesan)
{
    echo json_encode(array('error' => $pesan));
}

$doc = isset($_POST['doc_number']) ? trim((string) $_POST['doc_number'])
     : (isset($_REQUEST['doc_number']) ? trim((string) $_REQUEST['doc_number']) : '');

if ($doc === '') {
    aprvSalah('No document number was sent.');
    if (defined('APRV_PUSTAKA')) { return; }
    exit;
}

$cek = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_projection_history_h'");
if (!$cek || mysqli_num_rows($cek) === 0) {
    aprvSalah('The projection history table does not exist yet. '
            . 'Run db_migrate_LIVE_20261106_GABUNGAN.sql first.');
    if (defined('APRV_PUSTAKA')) { return; }
    exit;
}

$eDoc = mysqli_real_escape_string($conn2, $doc);

$qh = mysqli_query($conn2, "SELECT * FROM ap_projection_history_h WHERE doc_number = '$eDoc'");
if (!$qh || mysqli_num_rows($qh) === 0) {
    aprvSalah('Projection document ' . htmlspecialchars($doc, ENT_QUOTES) . ' was not found.');
    if (defined('APRV_PUSTAKA')) { return; }
    exit;
}
$h = mysqli_fetch_assoc($qh);

$dari   = $h['periode_dari'];
$sampai = $h['periode_sampai'];
/* Tanggal laporannya = akhir periode dokumennya. Itu yang jadi $A$3 di
   lembar Excel-nya, dan yang menentukan mana utang yang sudah "Days Past
   Due" walau belum dibayar. */
$tglLaporan = $sampai;

$qd = mysqli_query($conn2, "SELECT * FROM ap_projection_history_det
                             WHERE doc_number = '$eDoc' ORDER BY no_urut, id");
if ($qd === false) {
    aprvSalah(mysqli_error($conn2));
    if (defined('APRV_PUSTAKA')) { return; }
    exit;
}

/* ===================================================== pembantu kecil ==== */
function aprvNum($v) { return ($v === null || $v === '') ? null : (float) $v; }
function aprvIso($v)
{
    return (!empty($v) && $v !== '0000-00-00' && $v !== '0000-00-00 00:00:00')
         ? date('Y-m-d', strtotime($v)) : '';
}
function aprvAman($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }
/**
 * Relationship HANYA mengenal dua nilai: GROUP dan NON GROUP.
 *
 * Datanya memakai dua kosakata sekaligus - 'Group'/'Non Group' di
 * mastercoa_v2 dan 'GROUP'/'NON GROUP' di ap_saldo_payment_voucher, dan di
 * produksi ada pula 'Related Party'/'Third Party'. Akibatnya satu kelompok
 * ekonomi yang sama terbelah dua di Executive Summary, yang memang
 * mengelompokkan per Relationship x Item Type - jadi ini bukan sekadar
 * perkara tampilan.
 *
 * Aturannya dari pemakai (9 Nov 2026): related party = GROUP, third party =
 * NON GROUP. Disatukan DI SINI, di pintu masuknya, supaya layar, matriks
 * ringkasan dan ekspor mustahil berbeda pendapat.
 *
 * 'NON' diperiksa LEBIH DULU daripada 'GROUP', karena 'NON GROUP' juga
 * memuat kata 'GROUP' - kalau dibalik, semuanya jadi GROUP.
 */
function aprvRelasi($v)
{
    $t = strtoupper(trim((string) $v));
    if ($t === '') { return ''; }
    if (strpos($t, 'NON') !== false || strpos($t, 'THIRD') !== false) { return 'NON GROUP'; }
    if (strpos($t, 'GROUP') !== false || strpos($t, 'RELATED') !== false) { return 'GROUP'; }
    /* Nilai asing dibiarkan apa adanya, bukan dipaksa jadi salah satu:
       lebih baik kelihatan aneh di layar daripada diam-diam salah golong. */
    return $t;
}
/** Selisih hari, lurus tanpa jam. null kalau salah satu tanggalnya kosong. */
function aprvHari($dari, $sampai)
{
    if ($dari === '' || $sampai === '') { return null; }
    return (int) round((strtotime($sampai) - strtotime($dari)) / 86400);
}
/** Baris kosong satu voucher - semua kuncinya ada sejak awal. */
function aprvBarisKosong($noKbon)
{
    return array(
        'no_kbon' => $noKbon, 'supplier' => '', 'tgl_kbon' => '', 'duedate' => '',
        'term' => '', 'relasi' => '', 'item_type' => '', 'flexibility' => '',
        'curr' => '',
        /* 'dipotret' = voucher ini ADA di dokumen proyeksinya. Itu yang
           menentukan status, BUKAN terisi-tidaknya plan_date - sejak 9 Nov
           2026 voucher di luar potret pun bisa punya tanggal rencana
           (diambil dari menu Update Payment Plan), jadi "tanggalnya kosong"
           tidak lagi sama artinya dgn "tidak direncanakan". */
        'dipotret' => false,
        'plan_date' => '', 'proj_ocy' => 0.0, 'proj_rate' => 0.0, 'proj_idr' => 0.0,
        'voucher_amount' => 0.0,
        'real_date' => '', 'real_ocy' => 0.0, 'real_rate' => 0.0, 'real_idr' => 0.0,
        'status' => '', 'achievement' => null, 'date_variance' => null,
        'days_past_due' => null, 'explanation' => '',
        'variance_excl_fx' => 0.0, 'fx_variance' => 0.0, 'total_variance' => 0.0,
        'bayar' => array(),
    );
}

/* ============================================================ 1. PROYEKSI
   Dikumpulkan per nomor voucher. Satu voucher normalnya satu baris potret,
   tapi dijumlahkan supaya dokumen yang (karena apa pun) memuat voucher yang
   sama dua kali tidak diam-diam kehilangan separuh nilainya. */
$vou      = array();
$proyeksi = array();
/* Komposisi voucher dalam MATA UANG DOKUMEN, dikumpulkan sekalian di sini
   untuk lembar Realization. Potretnya menyimpan rupiah, jadi dibalik dgn
   kurs potret itu - bukan kurs hari ini. Dikumpulkan di memori, BUKAN
   ditanya ulang per baris pembayaran: satu dokumen bisa ratusan baris. */
$komposisi = array();
$noUrut   = 0;
while ($p = mysqli_fetch_assoc($qd)) {
    $noUrut++;
    $proyeksi[] = array(
        'no'             => $noUrut,
        'supplier'       => aprvAman($p['supplier']),
        'no_kbon'        => aprvAman($p['no_kbon']),
        'tgl_kbon'       => aprvIso($p['tgl_kbon']),
        'term'           => (string) $p['term'],
        'duedate'        => aprvIso($p['duedate']),
        'plan_date'      => aprvIso($p['plan_date']),
        'basis'          => aprvAman($p['basis']),
        'relasi'         => aprvAman(aprvRelasi($p['relasi'])),
        'item_type'      => aprvAman($p['item_type']),
        'flexibility'    => aprvAman($p['flexibility']),
        'curr'           => aprvAman($p['curr']),
        'invoice_amount' => aprvNum($p['invoice_amount']),
        'rate'           => aprvNum($p['rate']),
        'tax_base'       => aprvNum($p['tax_base']),
        'vat'            => aprvNum($p['vat']),
        'total_invoice'  => aprvNum($p['total_invoice']),
        'pph'            => aprvNum($p['pph']),
        'payment_amount' => aprvNum($p['payment_amount']),
        'outstanding'    => aprvNum($p['outstanding']),
    );

    $no = trim((string) $p['no_kbon']);
    if ($no === '') { continue; }
    if (!isset($vou[$no])) { $vou[$no] = aprvBarisKosong($no); }

    $vou[$no]['supplier']    = (string) $p['supplier'];
    $vou[$no]['tgl_kbon']    = aprvIso($p['tgl_kbon']);
    $vou[$no]['duedate']     = aprvIso($p['duedate']);
    $vou[$no]['term']        = (string) $p['term'];
    $vou[$no]['relasi']      = aprvRelasi($p['relasi']);
    $vou[$no]['item_type']   = (string) $p['item_type'];
    $vou[$no]['flexibility'] = (string) $p['flexibility'];
    $vou[$no]['curr']        = (string) $p['curr'];
    $vou[$no]['dipotret']    = true;
    $vou[$no]['plan_date']   = aprvIso($p['plan_date']);
    $vou[$no]['proj_rate']   = (float) $p['rate'];
    /* Lihat catatan di kepala berkas: yang diproyeksikan keluar = outstanding. */
    $vou[$no]['proj_idr']       += (float) $p['outstanding'];
    $vou[$no]['voucher_amount'] += (float) $p['payment_amount'];

    $rp = ((float) $p['rate'] != 0) ? (float) $p['rate'] : 1.0;
    if (!isset($komposisi[$no])) {
        $komposisi[$no] = array('total' => 0.0, 'tax' => 0.0, 'pph' => 0.0);
    }
    $komposisi[$no]['total'] += (float) $p['payment_amount'] / $rp;
    $komposisi[$no]['tax']   += (float) $p['vat'] / $rp;
    /* Kolom pph di potret disimpan NEGATIF (Income Tax Art 23); di sini
       dipakai nilai positifnya, sama dgn kontrabon_h.pph_idr. */
    $komposisi[$no]['pph']   += -1 * (float) $p['pph'] / $rp;
}

/* =========================================================== 2. REALISASI
   Satu baris per (dokumen pembayaran x voucher). Dikelompokkan di SQL supaya
   satu dokumen yang menyentuh satu voucher lewat beberapa baris jurnal
   (mis. pecahan beban) tetap jadi satu baris laporan. */
$eDari   = mysqli_real_escape_string($conn2, $dari);
$eSampai = mysqli_real_escape_string($conn2, $sampai);

$saluran = "(
       (j.type_journal LIKE '%Payment Voucher%'  AND j.no_journal LIKE '%BK%')
    OR (j.type_journal LIKE '%Payment Voucher%'  AND j.no_journal LIKE '%KKK%')
    OR (j.type_journal LIKE '%Payment Non Bank%' AND j.no_journal LIKE '%PAY%')
    OR (j.type_journal = 'ACCOUNT PAYABLE'
        AND (j.reff_doc LIKE '%SI/APR%' OR j.reff_doc LIKE '%PV-AP/%'))
  )";

/* PPh 23 DIKELUARKAN dari angka realisasi - sebabnya sama dgn di laporan
   proyeksi ($kas di ajx_ap_projection_report.php), dan HARUS dikerjakan
   bersamaan dgn yang di sana: kalau hanya satu sisi yang bersih, setiap
   voucher ber-PPh langsung memperlihatkan variance palsu sebesar PPh-nya.

   Bentuk jurnal pembayaran satu voucher (terbukti di 72 voucher ber-PPh
   yang sudah lunas, tanpa satu pun selisih):
       Dr  UTANG USAHA ...        bruto (subtotal + PPN)
       Cr  2.52.xx PPh 21/23/4(2) potongan pajaknya
       Cr  bank/kas               sisanya -> INI yang benar-benar keluar
   Bruto - potongan = kontrabon_h.total = kolom Payment Amount.

   Baris banknya TIDAK bisa dipakai langsung: satu dokumen BK membayar
   banyak voucher sekaligus dan baris banknya hanya memikul totalnya.

   Disaring '2.52.%' (utang pajak), BUKAN sekadar nama ber-'PPH': 1.52.02
   "PAJAK DIBAYAR DIMUKA PPH PASAL 23" adalah harta dan tidak boleh ikut.
   Pada baris ber-reff_doc dokumen pembayaran PV, 2.52.x yang muncul hanya
   PPh 23, 21 dan 4(2) - semuanya memang potongan, semuanya bersaldo kredit.

   HAVING-nya wajib: tanpa itu, dokumen yang kebetulan hanya menyentuh
   2.52.x tanpa baris Utang Usaha akan muncul sbg realisasi NEGATIF. Karena
   baris Utang Usaha-lah yang wajib ada, dan hanya baris itu yang disaring
   ketat ke periode laporan, satu baris realisasi TIDAK BISA lahir dari baris
   pajak belaka.

   TANGGAL TIDAK IKUT JADI KUNCI KELOMPOK, dan ini bukan kelalaian: dari 131
   dokumen pembayaran ber-PPh di basis data uji, 124 menaruh kedua barisnya
   pada tanggal yang sama tapi 5 menaruh barisnya PPh 7 hari kemudian dan 2
   lagi 14 hari kemudian (contohnya BK/BCA1979/NAG/0726/00014 atas
   SI/APR/NAG/2026/06/01429: utang 2 Juli, PPh 16 Juli). Kalau tanggal ikut
   jadi kunci, kedua baris itu jatuh ke kelompok yang BERBEDA, kelompok
   pajaknya dibuang HAVING, dan PPh-nya tidak pernah terpotong - persis
   kesalahan yang hendak diperbaiki.

   Sebagai gantinya baris pajaknya diberi jendela tanggal yang DILEBARKAN
   sebulan ke dua arah, sedangkan baris utangnya tetap terkurung periode
   laporan. Melebarkannya aman karena kuncinya no_journal + reff_doc: yang
   tertarik masuk hanya baris dari DOKUMEN PEMBAYARAN YANG SAMA, bukan
   pembayaran lain.

   Tanggal realisasi diambil dari baris UTANG USAHA saja, supaya baris pajak
   yang bertanggal belakangan tidak menggeser tanggal pembayarannya. Mata
   uang juga - satu voucher hanya punya satu mata uang, dan membiarkannya
   ikut jadi kunci kelompok membuat baris pajak ber-mata-uang IDR terlepas
   dari baris utang ber-mata-uang asing. */
$sqlBayar = "
    SELECT j.no_journal, j.reff_doc,
           MAX(CASE WHEN j.nama_coa LIKE '%UTANG USAHA%' THEN j.tgl_journal END) AS tgl_journal,
           MAX(CASE WHEN j.nama_coa LIKE '%UTANG USAHA%' THEN j.curr END)        AS curr,
           MAX(j.type_journal)             AS type_journal,
           MAX(j.supplier)                 AS supplier_jurnal,
           SUM(CASE WHEN j.nama_coa LIKE '%UTANG USAHA%'
                    THEN j.debit - j.credit ELSE 0 END)
         - SUM(CASE WHEN j.no_coa LIKE '2.52.%'
                    THEN j.credit - j.debit ELSE 0 END)         AS ocy,
           SUM(CASE WHEN j.nama_coa LIKE '%UTANG USAHA%'
                    THEN j.debit_idr - j.credit_idr ELSE 0 END)
         - SUM(CASE WHEN j.no_coa LIKE '2.52.%'
                    THEN j.credit_idr - j.debit_idr ELSE 0 END) AS idr
      FROM tbl_list_journal j
     WHERE TRIM(IFNULL(j.reff_doc, '')) <> ''
       AND ( (j.nama_coa LIKE '%UTANG USAHA%'
              AND j.tgl_journal BETWEEN '$eDari' AND '$eSampai')
          OR (j.no_coa LIKE '2.52.%'
              AND j.tgl_journal BETWEEN DATE_SUB('$eDari', INTERVAL 31 DAY)
                                    AND DATE_ADD('$eSampai', INTERVAL 31 DAY)) )
       AND $saluran
     GROUP BY j.no_journal, j.reff_doc
    HAVING SUM(CASE WHEN j.nama_coa LIKE '%UTANG USAHA%' THEN 1 ELSE 0 END) > 0
     ORDER BY tgl_journal, j.no_journal, j.reff_doc";

$qb = mysqli_query($conn2, $sqlBayar);
if ($qb === false) {
    aprvSalah(mysqli_error($conn2));
    if (defined('APRV_PUSTAKA')) { return; }
    exit;
}

$bayar = array();
while ($r = mysqli_fetch_assoc($qb)) { $bayar[] = $r; }

/* ---- keterangan voucher yang BELUM dikenal dari potretnya --------------
   Dua golongan: voucher yang dibayar tapi tidak ikut terjaring dokumen
   proyeksinya (nanti berstatus Unplanned Payment), dan - kalau sampai
   terjadi - potret lama yang kolomnya belum lengkap. Keduanya dibacakan
   dari tabel dokumennya SEKARANG; itu satu-satunya sumber yang ada, dan
   sengaja TIDAK ditulis balik ke potretnya.

   Tiga lapis sumbernya sama dgn ajx_ap_projection_report.php: kontrabon_h
   (+ COA-nya untuk relasi & jenis barang), ap_saldo_payment_voucher, lalu
   cicilan kontrabon_h_installment_detail yang menunjuk induknya. */
$perluKeterangan = array();
foreach ($bayar as $b) {
    $no = trim((string) $b['reff_doc']);
    if ($no !== '' && !isset($vou[$no])) { $perluKeterangan[$no] = true; }
}
$ketVoucher = array();
if ($perluKeterangan) {
    $daftar = array();
    foreach (array_keys($perluKeterangan) as $no) {
        $daftar[] = "'" . mysqli_real_escape_string($conn2, $no) . "'";
    }
    $inList = implode(',', $daftar);

    $qk = mysqli_query($conn2, "
        SELECT k.no_kbon, k.nama_supp, k.tgl_kbon, k.tgl_tempo, k.curr,
               IFNULL(k.rate, 1) AS rate,
               ROUND(IFNULL(k.total, 0), 2)   AS total,
               ROUND(IFNULL(k.tax, 0), 2)     AS tax,
               ROUND(IFNULL(k.pph_idr, 0), 2) AS pph,
               IFNULL(c.relasi, '')     AS relasi,
               IFNULL(c.item_type1, '') AS item_type1
          FROM kontrabon_h k
          LEFT JOIN mastercoa_v2 c ON c.no_coa = k.no_coa
         WHERE k.no_kbon IN ($inList)");
    if ($qk) {
        while ($r = mysqli_fetch_assoc($qk)) { $ketVoucher[$r['no_kbon']] = $r; }
    }

    /* saldo awal: untuk voucher terbitan sebelum penomoran SI/APR, dan untuk
       baris ' - N' yang memang hidup di tabel ini */
    $qs = mysqli_query($conn2, "
        SELECT no_kbon, MAX(nama_supp) AS nama_supp, MAX(tgl_kbon) AS tgl_kbon,
               MAX(tgl_tempo) AS tgl_tempo, MAX(curr) AS curr,
               MAX(IFNULL(rate, 1)) AS rate,
               ROUND(MAX(IFNULL(total, 0)), 2) AS total,
               ROUND(MAX(IFNULL(tax, 0)), 2)   AS tax,
               ROUND(MAX(IFNULL(pph, 0)), 2)   AS pph,
               MAX(IFNULL(relasi, ''))     AS relasi,
               MAX(IFNULL(item_type1, '')) AS item_type1
          FROM ap_saldo_payment_voucher
         WHERE no_kbon IN ($inList) GROUP BY no_kbon");
    if ($qs) {
        while ($r = mysqli_fetch_assoc($qs)) {
            if (!isset($ketVoucher[$r['no_kbon']])) { $ketVoucher[$r['no_kbon']] = $r; }
        }
    }

    /* cicilan: keterangannya menempel di dokumen INDUK */
    $qi = mysqli_query($conn2, "
        SELECT d.no_kbon_det AS no_kbon, k.nama_supp, k.tgl_kbon, d.tgl_tempo, k.curr,
               IFNULL(k.rate, 1) AS rate,
               ROUND(IFNULL(d.total, 0), 2) AS total,
               ROUND(IFNULL(d.ppn, 0), 2)   AS tax,
               ROUND(IFNULL(d.pph, 0), 2)   AS pph,
               IFNULL(c.relasi, '')     AS relasi,
               IFNULL(c.item_type1, '') AS item_type1
          FROM kontrabon_h_installment_detail d
          INNER JOIN kontrabon_h k ON k.no_kbon = d.no_kbon
          LEFT JOIN mastercoa_v2 c ON c.no_coa = k.no_coa
         WHERE d.no_kbon_det IN ($inList)");
    if ($qi) {
        while ($r = mysqli_fetch_assoc($qi)) {
            if (!isset($ketVoucher[$r['no_kbon']])) { $ketVoucher[$r['no_kbon']] = $r; }
        }
    }
}

/* ---- saringan supplier -------------------------------------------------
   Kalau potretnya dibuat untuk SATU supplier, pembayaran supplier lain tidak
   boleh ikut muncul sbg Unplanned Payment - dokumennya memang tidak pernah
   menjanjikan apa pun tentang supplier itu. Disaring DI PHP, bukan di SQL:
   kolom supplier di tbl_list_journal diisi lewat trigger dan collation-nya
   bisa berbeda dgn tabel dokumen (lihat catatan collation di
   journal-supplier-column), sedangkan di sini namanya sudah ada di tangan. */
$supDok = trim((string) $h['nama_supp']);
$saringSupplier = ($supDok !== '' && strtoupper($supDok) !== 'ALL');

function aprvSupplierBaris($reffDoc, $b, $vou, $ketVoucher)
{
    if (isset($vou[$reffDoc]) && $vou[$reffDoc]['supplier'] !== '') {
        return $vou[$reffDoc]['supplier'];
    }
    if (isset($ketVoucher[$reffDoc]) && (string) $ketVoucher[$reffDoc]['nama_supp'] !== '') {
        return (string) $ketVoucher[$reffDoc]['nama_supp'];
    }
    return (string) $b['supplier_jurnal'];
}

/* ---- gabungkan realisasi ke vouchernya -------------------------------- */
$realisasi = array();
$noReal    = 0;
foreach ($bayar as $b) {
    $no = trim((string) $b['reff_doc']);
    if ($no === '') { continue; }

    $namaSupp = aprvSupplierBaris($no, $b, $vou, $ketVoucher);
    if ($saringSupplier && !isset($vou[$no])
        && strcasecmp(trim($namaSupp), $supDok) !== 0) {
        continue;   /* pembayaran supplier lain, dokumen ini tidak membahasnya */
    }

    if (!isset($vou[$no])) {
        $vou[$no] = aprvBarisKosong($no);
        $k = isset($ketVoucher[$no]) ? $ketVoucher[$no] : null;
        $vou[$no]['supplier']  = $namaSupp;
        $vou[$no]['tgl_kbon']  = $k ? aprvIso($k['tgl_kbon'])  : '';
        $vou[$no]['duedate']   = $k ? aprvIso($k['tgl_tempo']) : '';
        $vou[$no]['curr']      = $k ? (string) $k['curr'] : (string) $b['curr'];
        $vou[$no]['relasi']    = $k ? aprvRelasi($k['relasi']) : '';
        $vou[$no]['item_type'] = $k ? strtoupper((string) $k['item_type1']) : '';
        if ($k) {
            /* Nilai dokumennya dipakai dua hal: pembanding "sudah lunas atau
               belum" dan pembagi komposisi pajak di lembar Realization. */
            $vou[$no]['voucher_amount'] = (float) $k['total'] * (float) $k['rate'];
        }
    }

    $ocy = (float) $b['ocy'];
    $idr = (float) $b['idr'];
    $vou[$no]['real_ocy'] += $ocy;
    $vou[$no]['real_idr'] += $idr;
    /* Tanggal realisasinya tanggal pembayaran TERAKHIR - saat utangnya
       selesai dibayar, bukan saat cicilan pertamanya keluar. */
    $tgl = aprvIso($b['tgl_journal']);
    if ($vou[$no]['real_date'] === '' || $tgl > $vou[$no]['real_date']) {
        $vou[$no]['real_date'] = $tgl;
    }
    $vou[$no]['bayar'][] = array('no_journal' => (string) $b['no_journal'],
                                 'tgl' => $tgl, 'ocy' => $ocy, 'idr' => $idr);
}

/* ---- lembar Realization: satu baris per baris pembayaran --------------
   Payment Amount-nya angka jurnal apa adanya. Empat kolom pajaknya
   (Tax Base, VAT, Total Invoice, Income Tax Art 23) adalah KOMPOSISI
   voucher-nya yang dibagi menurut porsi yang dibayar baris ini:

       porsi = payment_amount baris / nilai dokumen voucher

   Untuk pembayaran lunas porsinya 1, sehingga angkanya sama persis dgn
   voucher-nya - itu keadaan di lembar contoh. Untuk pembayaran sebagian
   angkanya terbagi, dan jumlah seluruh barisnya tetap sama dgn komposisi
   voucher x porsi terbayar. Kalau nilai dokumennya tidak diketahui, empat
   kolom itu dikirim null (tampil '-'): lebih baik kosong daripada angka
   pajak yang dikarang. */
foreach ($bayar as $b) {
    $no = trim((string) $b['reff_doc']);
    if ($no === '' || !isset($vou[$no])) { continue; }

    $v   = $vou[$no];
    $ocy = (float) $b['ocy'];
    $idr = (float) $b['idr'];
    $rate = ($ocy != 0) ? ($idr / $ocy) : (float) 0;

    /* komposisi voucher dalam MATA UANG DOKUMEN, rumusnya sama dgn
       ajx_ap_projection_report.php: payment = total (sudah net PPh),
       invoice = payment + pph, base = invoice - tax, kolom PPh = -pph */
    $vTot = null; $vTax = null; $vPph = null;
    if (isset($komposisi[$no])) {               /* dari potretnya */
        $vTot = $komposisi[$no]['total'];
        $vTax = $komposisi[$no]['tax'];
        $vPph = $komposisi[$no]['pph'];
    } elseif (isset($ketVoucher[$no])) {        /* voucher di luar potret */
        $kv   = $ketVoucher[$no];
        $vTot = (float) $kv['total'];
        $vTax = (float) $kv['tax'];
        $vPph = (float) $kv['pph'];
    }

    $base = null; $vat = null; $inv = null; $pph = null;
    if ($vTot !== null && $vTot != 0) {
        $porsi = $ocy / $vTot;
        $inv   = ($vTot + $vPph) * $porsi;
        $vat   = $vTax * $porsi;
        $base  = $inv - $vat;
        $pph   = -1 * $vPph * $porsi;
    }

    $noReal++;
    $realisasi[] = array(
        'no'          => $noReal,
        'no_payment'  => aprvAman($b['no_journal']),
        'pay_date'    => aprvIso($b['tgl_journal']),
        'supplier'    => aprvAman($v['supplier']),
        'no_kbon'     => aprvAman($no),
        'tgl_kbon'    => $v['tgl_kbon'],
        'relasi'      => aprvAman($v['relasi']),
        'item_type'   => aprvAman($v['item_type']),
        'curr'        => aprvAman($b['curr']),
        'rate'        => $rate,
        /* mata uang dokumen */
        'tax_base'    => $base,
        'vat'         => $vat,
        'total_invoice' => $inv,
        'pph'         => $pph,
        'pay_amount'  => $ocy,
        /* rupiah */
        'tax_base_idr'  => ($base === null) ? null : $base * $rate,
        'vat_idr'       => ($vat  === null) ? null : $vat  * $rate,
        'total_invoice_idr' => ($inv === null) ? null : $inv * $rate,
        'pph_idr'       => ($pph  === null) ? null : $pph  * $rate,
        'pay_amount_idr'=> $idr,
        'type_journal'  => aprvAman($b['type_journal']),
    );
}

/* ---- penjelasan selisih ------------------------------------------------
   DIKOSONGKAN sejak 9 Nov 2026 atas permintaan pemakai: kolom Variance
   Explanation tidak lagi bisa diisi dari layar, jadi tidak ada yang perlu
   dibaca. Kolomnya TETAP ADA dan tetap kosong - tempatnya menulis ada di
   berkas Excel hasil ekspor.

   Tabel ap_realization_variance dgn begitu tidak lagi dipakai sama sekali,
   dan migrasinya tidak perlu dijalankan di produksi. */
$penjelasan = array();

/* ---- tanggal rencana untuk voucher DI LUAR potret ---------------------
   Permintaan pemakai 9 Nov 2026. Voucher yang sudah lunas ketika potret
   dibekukan tidak ikut terpotret (laporan proyeksi hanya memuat yang
   saldonya masih ada), jadi kolom Payment Plan Date-nya kosong - padahal
   tanggalnya ADA di menu Update Payment Plan. Diambilkan dari sana supaya
   kolomnya berisi, TANPA mengubah statusnya: barisnya tetap Unplanned
   Payment, karena dokumen proyeksi ini memang tidak menjanjikannya.

   Aturan "yang berlaku = dokumen terbaru yang belum dibatalkan" disalin
   dari $rencana di ajx_ap_projection_report.php, supaya dua layar tidak
   pernah menyebut tanggal rencana yang berbeda untuk voucher yang sama.

   SATU query untuk semua voucher sekaligus, bukan satu query per baris:
   satu dokumen bisa ratusan voucher. */
$tanpaPotret = array();
foreach ($vou as $no => $v) {
    if (!$v['dipotret']) {
        $tanpaPotret[] = "'" . mysqli_real_escape_string($conn2, $no) . "'";
    }
}
if (!empty($tanpaPotret)) {
    /* Tabelnya BOLEH belum ada - migrasi Payment Plan dijalankan terpisah di
       produksi, dan query ke tabel yang belum dibuat mematikan seluruh
       laporan, bukan sekadar mengosongkan satu kolom. */
    $qtp = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_payment_plan_det'");
    if ($qtp && mysqli_num_rows($qtp) > 0) {
        $daftarVou = implode(',', $tanpaPotret);
        $qr = mysqli_query($conn2, "
            SELECT d.no_kbon, d.plan_date
              FROM ap_payment_plan_det d
              INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
              INNER JOIN (SELECT d2.no_kbon, MAX(h2.id) AS id_terbaru
                            FROM ap_payment_plan_det d2
                            INNER JOIN ap_payment_plan_h h2 ON h2.doc_number = d2.doc_number
                           WHERE d2.status = 'Y' AND h2.status <> 'CANCEL'
                           GROUP BY d2.no_kbon) t
                      ON t.no_kbon = d.no_kbon AND t.id_terbaru = h.id
             WHERE d.status = 'Y' AND h.status <> 'CANCEL'
               AND d.no_kbon IN ($daftarVou)");
        if ($qr) {
            while ($r = mysqli_fetch_assoc($qr)) {
                $noR = (string) $r['no_kbon'];
                /* Penjaga: baris potret TIDAK boleh tertimpa dari sini.
                   Tanggal di potret itu yang dijanjikan dokumennya, dan
                   rencana di menu bisa sudah berubah sesudahnya. */
                if (isset($vou[$noR]) && !$vou[$noR]['dipotret']) {
                    $vou[$noR]['plan_date'] = aprvIso($r['plan_date']);
                }
            }
        }
    }
}

/* ======================================== 3. KOLOM HASIL & KOLOM SELISIH */
foreach ($vou as $no => $v) {
    $v['proj_ocy']  = ($v['proj_rate'] != 0) ? $v['proj_idr'] / $v['proj_rate'] : 0.0;
    /* Kurs realisasinya kurs RATA-RATA TERTIMBANG, bukan kurs pembayaran
       pertama: satu voucher bisa dibayar beberapa kali dgn kurs berbeda, dan
       hasil bagi ini yang pas dgn Amount Eqv Idr-nya. */
    $v['real_rate'] = ($v['real_ocy'] != 0) ? $v['real_idr'] / $v['real_ocy'] : 0.0;

    /* Ambang 0,005: pembayaran yang keluar lalu dibalik di periode yang sama
       berjumlah nol, dan itu memang BUKAN realisasi. */
    $adaRealisasi = ($v['real_date'] !== '' && abs($v['real_ocy']) > 0.005);

    /* Dasarnya ADA/TIDAKNYA voucher ini di potret, bukan kosong-tidaknya
       plan_date: sejak kolom rencana diisikan dari menu Payment Plan,
       baris di luar potret pun bertanggal, dan patokan lama akan
       menggolongkannya sbg Paid - padahal dokumen ini tidak pernah
       menjanjikan pembayaran itu. */
    if (!$v['dipotret'] && $adaRealisasi) {
        $v['status'] = 'Unplanned Payment';
    } elseif (!$adaRealisasi) {
        $v['status'] = 'Not Paid';
    } elseif ($v['proj_ocy'] > 0 && $v['real_ocy'] < $v['proj_ocy'] - 0.01) {
        $v['status'] = 'Partially Paid';
    } else {
        $v['status'] = 'Paid';
    }

    $v['achievement'] = ($v['proj_ocy'] > 0) ? $v['real_ocy'] / $v['proj_ocy'] : null;

    /* Selisih tanggal diukur ke RENCANA bayar, bukan ke jatuh tempo -
       itu yang dijanjikan dokumen ini. Jatuh tempo diukur terpisah di
       Days Past Due. */
    $v['date_variance'] = ($v['plan_date'] !== '' && $v['real_date'] !== '')
        ? aprvHari($v['plan_date'], $v['real_date']) : null;

    if ($v['real_date'] !== '' && $v['duedate'] !== '') {
        $v['days_past_due'] = aprvHari($v['duedate'], $v['real_date']);
    } elseif ($v['duedate'] !== '' && $v['duedate'] < $tglLaporan) {
        $v['days_past_due'] = aprvHari($v['duedate'], $tglLaporan);
    } else {
        $v['days_past_due'] = null;
    }

    /* Selisih kurs dihitung atas nilai yang DIPROYEKSIKAN, jadi hanya ada
       kalau dua sisinya ada. Sisanya selisih nilai murni. */
    $v['fx_variance'] = ($v['proj_ocy'] != 0 && $v['real_ocy'] != 0)
        ? ($v['real_rate'] - $v['proj_rate']) * $v['proj_ocy'] : 0.0;
    $v['total_variance']   = $v['real_idr'] - $v['proj_idr'];
    $v['variance_excl_fx'] = $v['total_variance'] - $v['fx_variance'];

    $v['explanation'] = isset($penjelasan[$no]) ? $penjelasan[$no] : '';

    $vou[$no] = $v;
}

/* Urutannya nama supplier lalu nomor voucher - sama dgn lembar Excel-nya. */
$daftar = array_values($vou);
usort($daftar, function ($x, $y) {
    $c = strcasecmp((string) $x['supplier'], (string) $y['supplier']);
    return ($c !== 0) ? $c : strcmp((string) $x['no_kbon'], (string) $y['no_kbon']);
});

$data = array();
$urut = 0;
foreach ($daftar as $v) {
    $urut++;
    $data[] = array(
        'no'        => $urut,
        'supplier'  => aprvAman($v['supplier']),
        'no_kbon'   => aprvAman($v['no_kbon']),
        'relasi'    => aprvAman($v['relasi']),
        'item_type' => aprvAman($v['item_type']),
        'tgl_kbon'  => $v['tgl_kbon'],
        'duedate'   => $v['duedate'],
        'term'      => (string) $v['term'],
        'curr'      => aprvAman($v['curr']),
        /* Projection */
        'plan_date' => $v['plan_date'],
        'proj_ocy'  => $v['proj_ocy'],
        'proj_rate' => $v['proj_rate'],
        'proj_idr'  => $v['proj_idr'],
        'voucher_amount' => $v['voucher_amount'],
        /* Realization */
        'real_date' => $v['real_date'],
        'real_ocy'  => $v['real_ocy'],
        'real_rate' => $v['real_rate'],
        'real_idr'  => $v['real_idr'],
        /* Result */
        'status'        => $v['status'],
        'achievement'   => $v['achievement'],
        'date_variance' => $v['date_variance'],
        'days_past_due' => $v['days_past_due'],
        'explanation'   => aprvAman($v['explanation']),
        'variance_excl_fx' => $v['variance_excl_fx'],
        'fx_variance'      => $v['fx_variance'],
        'total_variance'   => $v['total_variance'],
        'jml_bayar'        => count($v['bayar']),
    );
}

/* ================================================ 4. EXECUTIVE SUMMARY ==
   Nama & urutan indikatornya mengikuti lembar Executive Summary di berkas
   contohnya, termasuk keterangan di kolom ketiganya. */
$r = array(
    'projected' => 0.0, 'actual' => 0.0,
    'variance_excl_fx' => 0.0, 'fx_variance' => 0.0, 'total_variance' => 0.0,
    'planned_realized' => 0.0, 'unplanned' => 0.0, 'not_executed' => 0.0,
    'overdue_not_paid' => 0.0,
    'jml_not_paid' => 0, 'jml_partial' => 0, 'jml_ontime' => 0, 'jml_late' => 0,
    'jml_voucher' => count($data),
);
$matriks = array();
foreach ($daftar as $v) {
    $r['projected']        += $v['proj_idr'];
    $r['actual']           += $v['real_idr'];
    $r['variance_excl_fx'] += $v['variance_excl_fx'];
    $r['fx_variance']      += $v['fx_variance'];
    $r['total_variance']   += $v['total_variance'];

    /* "Realisasi dari yang DIRENCANAKAN" = yang dijanjikan dokumen ini.
       Memakai plan_date di sini akan menghitung ganda sejak baris di luar
       potret ikut bertanggal: nilainya masuk ke planned_realized DAN ke
       unplanned sekaligus. */
    if ($v['dipotret']) { $r['planned_realized'] += $v['real_idr']; }
    if ($v['status'] === 'Unplanned Payment') { $r['unplanned'] += $v['real_idr']; }
    if ($v['status'] === 'Not Paid') {
        $r['not_executed'] += $v['proj_idr'];
        $r['jml_not_paid']++;
        if ($v['duedate'] !== '' && $v['duedate'] < $tglLaporan) {
            $r['overdue_not_paid'] += $v['proj_idr'];
        }
    }
    if ($v['status'] === 'Partially Paid') { $r['jml_partial']++; }
    if ($v['status'] === 'Paid' && $v['date_variance'] !== null) {
        if ($v['date_variance'] <= 0) { $r['jml_ontime']++; } else { $r['jml_late']++; }
    }

    /* Summary-nya per RELASI x JENIS BARANG (bukan per supplier seperti di
       AR) - begitu bentuknya di lembar contohnya. Barisnya dibentuk dari
       DATA, tidak dipatok seperti di Excel-nya, supaya kombinasi baru tidak
       diam-diam hilang dari rekap. */
    $kRel = ($v['relasi'] !== '')    ? $v['relasi']    : '(tanpa relasi)';
    $kItm = ($v['item_type'] !== '') ? $v['item_type'] : '(tanpa jenis)';
    $kk = $kRel . "\x1f" . $kItm;
    if (!isset($matriks[$kk])) {
        $matriks[$kk] = array('relasi' => $kRel, 'item_type' => $kItm,
                              'projected' => 0.0, 'actual' => 0.0, 'jml' => 0);
    }
    $matriks[$kk]['projected'] += $v['proj_idr'];
    $matriks[$kk]['actual']    += $v['real_idr'];
    $matriks[$kk]['jml']++;
}

$r['ratio']       = ($r['projected'] != 0) ? $r['actual'] / $r['projected'] : null;
$r['net_variance']= $r['actual'] - $r['projected'];
$r['ontime_rate'] = ($r['jml_ontime'] + $r['jml_late'] > 0)
                  ? $r['jml_ontime'] / ($r['jml_ontime'] + $r['jml_late']) : null;
/* PENJAGA, bukan hiasan: kalau jumlah selisih per voucher tidak sama dgn
   selisih totalnya, ada baris yang hilang atau terhitung dua kali. */
$r['rekonsiliasi'] = $r['actual'] - $r['projected'] - $r['total_variance'];

$rekap = array_values($matriks);
foreach ($rekap as $i => $b) {
    /* Tanda selisihnya mengikuti lembar contohnya: Projected - Realization
       (di papan atas sebaliknya, Actual - Projected). Dua-duanya dipakai di
       tempat masing-masing; jangan "diseragamkan" tanpa memberi tahu. */
    $rekap[$i]['variance']    = $b['projected'] - $b['actual'];
    $rekap[$i]['achievement'] = ($b['projected'] != 0) ? $b['actual'] / $b['projected'] : null;
}
usort($rekap, function ($x, $y) {
    $c = strcasecmp($x['relasi'], $y['relasi']);
    return ($c !== 0) ? $c : strcasecmp($x['item_type'], $y['item_type']);
});
$r['rekap'] = $rekap;

/* ================================================================ 5. KIRIM */
$kurs = array();
if (!empty($h['kurs'])) {
    $x = json_decode($h['kurs'], true);
    if (is_array($x)) { $kurs = $x; }
}

echo json_encode(array(
    'info' => array(
        'doc_number'     => aprvAman($h['doc_number']),
        'periode_dari'   => aprvIso($dari),
        'periode_sampai' => aprvIso($sampai),
        'tgl_laporan'    => aprvIso($tglLaporan),
        'type'           => aprvAman($h['type']),
        'supplier'       => aprvAman(($supDok === '') ? 'ALL' : $supDok),
        'created_by'     => aprvAman($h['created_by']),
        'created_at'     => !empty($h['created_at'])
                            ? date('d M Y H:i', strtotime($h['created_at'])) : '-',
        'total_tersimpan'=> (float) $h['total_idr'],
        'baris_tersimpan'=> (int) $h['jml_baris'],
        /* Potret yang totalnya tidak lagi sama dgn rinciannya harus
           KELIHATAN - angka di laporan ini dibangun dari rinciannya. */
        'utuh'           => (abs($r['projected'] - (float) $h['total_idr']) <= 0.05),
        'kurs'           => $kurs,
    ),
    'data'      => $data,
    'ringkas'   => $r,
    'proyeksi'  => $proyeksi,
    'realisasi' => $realisasi,
), JSON_INVALID_UTF8_SUBSTITUTE);
