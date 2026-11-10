<?php
/* ============================================================================
   Update Payment Plan - pencarian voucher untuk formulir Create.
   Pasangannya create-payment-plan.php.

   Yang disebut "Payment Voucher" di lembar kerja tim AP adalah KONTRABON -
   kontrabon_h.no_kbon. Sudah dicocokkan ke lembar Okt 2026: nomor
   'SI/APR/NAG/2026/06/01376' di sana ketemu di kontrabon_h dgn tanggal
   voucher, jatuh tempo, supplier, Total dan VAT yang sama persis.

   Dua catatan tentang nilainya, dua-duanya dari pencocokan itu:

     - Tax Base = total - tax, BUKAN kolom subtotal. Pada voucher contoh,
       subtotal berisi 109.337.000 sedangkan total 54.668.500 dan tax
       6.013.535 - yang cocok dgn lembar kerja (48.654.965) adalah
       total - tax. Kolom subtotal tidak dipakai di sini.
     - Payment Term = tgl_tempo - tgl_kbon. Voucher contoh: 11 Mei -> 10 Jun
       = 30 hari, sama dgn kolom Payment Term di lembar kerja.

   RELATIONSHIP & ITEM TYPE dibaca dari mastercoa_v2 lewat kontrabon_h.no_coa
   - SUMBER YANG SAMA dgn laporan AP (ap_report2/query_pv2.php, kolom relasi
   / item_type1 / item_type2). Dicocokkan ke lembar kerja Okt 2026: dari 95
   voucher yang ada di salinan lokal, 93 cocok untuk DUA-DUANYA; 2 sisanya
   COA-nya memang beda di salinan itu. Seluruh 2.225 voucher 2026 punya COA
   yang relasi dan item_type1-nya terisi, jadi tidak perlu nilai cadangan.

   Kosakatanya HANYA dua: GROUP dan NON GROUP. Dibandingkan dgn UPPER()
   karena isinya campur ('Group' dan 'GROUP' dua-duanya ada di master COA).

   FLEXIBILITY CATEGORY dari mastersupplier.payment_rating, disambung lewat
   NAMA supplier - kontrabon_h tidak menyimpan id supplier. Dari 271 nama
   supplier bervoucher 2026, 270 ketemu. Kolomnya masih kosong untuk semua
   supplier, jadi wajar kalau tampil '-' sampai tim AP mengisinya.

   Barisnya dibatasi voucher yang SUDAH DISETUJUI (draft, Cancel, Updating
   tidak ikut) - yang belum disetujui belum tentu jadi kewajiban bayar.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

/* Isian tanggalnya dd-mm-yyyy (datepicker halaman), sama dgn Update BPB.
   Kalau salah satu kosong, filternya tidak dipasang supaya "tampilkan
   semua" tetap bisa. */
function appTanggal($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    /* Kiriman layar sekarang berbentuk "10 Oct 2026" (diganti 5 Nov 2026).
       str_replace tetap dipertahankan untuk kiriman lama yang memakai garis
       miring; strtotime() membaca dua-duanya. */
    $t = strtotime(str_replace('/', '-', $v));
    return $t ? date('Y-m-d', $t) : '';
}

$start_date = appTanggal(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$end_date   = appTanggal(isset($_POST['end_date']) ? $_POST['end_date'] : '');
$supplier   = trim(isset($_POST['supplier']) ? $_POST['supplier'] : '');
$dasar      = (isset($_POST['dasar']) && $_POST['dasar'] === 'kbon') ? 'kbon' : 'tempo';

/* Bawaannya MENYARING - hanya voucher yang belum punya rencana aktif.
   Hanya '0' yang tegas yang mematikannya. Dibalik begini (dulu bawaannya
   tidak menyaring) karena formulirnya sudah tidak punya kotak centang lagi
   DAN kolom "Current Plan" sudah dihapus: kalau parameternya hilang, layar
   itu akan memperlihatkan voucher yang sudah dijadwalkan tanpa satu pun
   tanda bahwa voucher itu sudah punya rencana. */
$hanya_blm  = !(isset($_POST['hanya_belum']) && $_POST['hanya_belum'] === '0');

/* Voucher yang sudah disetujui saja. Ejaan statusnya beragam di produksi
   ('Approved', 'FIRST APPROVED', 'SECOND APPROVED'), jadi didaftar tegas -
   bukan lewat LIKE '%APPROVE%' yang ikut menangkap kata lain. */
$where = array("k.status IN ('Approved','FIRST APPROVED','SECOND APPROVED')");

if ($start_date !== '' && $end_date !== '') {
    $kolom = ($dasar === 'kbon') ? 'k.tgl_kbon' : 'k.tgl_tempo';
    $where[] = $kolom . " BETWEEN '" . mysqli_real_escape_string($conn1, $start_date)
             . "' AND '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}
if ($supplier !== '') {
    $where[] = "k.nama_supp = '" . mysqli_real_escape_string($conn1, $supplier) . "'";
}
if ($hanya_blm) {
    $where[] = "p.no_kbon IS NULL";
}

/* Rencana yang BERLAKU untuk tiap voucher: baris 'Y' dari dokumen POST yang
   paling baru. Dokumen yang dibatalkan statusnya 'N', jadi vouchernya
   kembali terhitung belum dijadwalkan. */
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

/* status / create_user / no_faktur / supp_inv / tgl_inv TIDAK dipakai kolom
   tabel mana pun - keduanya khusus untuk modal rincian voucher, yang isinya
   disamakan dgn modal di payment-voucher-ap.php (diminta pemakai 5 Nov
   2026). Diambil sekali di sini bersama baris lainnya, jadi modalnya tidak
   perlu menembak server untuk bagian kepala. */
/* ---------------------------------------------------------------------------
   HANYA YANG MASIH OUTSTANDING (diminta pemakai, 5 Nov 2026), dan
   patokannya SAMA DENGAN AP REPORT - yaitu saldo_akhir dari
   ap_report2/query_pv2.php, berkas yang di-include, bukan ditiru.

   JANGAN diganti kontrabon_h.balance atau ada-tidaknya Bank Out: diperiksa
   pada 2.225 voucher 2026, keduanya TIDAK sejalan - 1.379 voucher
   ber-balance 0 padahal belum pernah masuk Bank Out, dan 369 voucher
   ber-balance > 0 padahal Bank Out-nya sudah ada.

   HARGANYA: query itu berat (sekitar 6 detik untuk rentang panjang), jadi
   layar ini memang lebih lambat memuat daripada sebelumnya. Itu ditukar
   dengan daftar yang tidak lagi memuat voucher yang sebenarnya sudah lunas.

   query_pv2.php MENIMPA $sql DAN $where - dua nama yang juga dipakai berkas
   ini. Karena itu keduanya disimpan dan dikembalikan di sekitar include-nya.
   --------------------------------------------------------------------------- */
$whereSaya  = $where;
$simpanPost = $_POST;
$hariIni    = date('Y-m-d');

$_REQUEST['start_date'] = $hariIni;
$_REQUEST['end_date']   = $hariIni;
$_REQUEST['nama_supp']  = 'ALL';
unset($_POST['search']);          /* 'search' di sini milik layar ini, bukan DataTables AP Report */

$dirAsal = getcwd();
chdir(__DIR__ . '/ap_report2');
include 'query_pv2.php';
$sqlOutstanding = $sql;
chdir($dirAsal);

$_POST = $simpanPost;
$where = $whereSaya;

$sql = "SELECT k.no_kbon, k.tgl_kbon, k.tgl_tempo, k.nama_supp,
               k.curr, k.rate, k.total, k.tax, IFNULL(k.pph_idr, 0) AS pph, k.no_coa,
               k.status, k.create_user, k.no_faktur, k.supp_inv, k.tgl_inv,
               DATEDIFF(k.tgl_tempo, k.tgl_kbon) AS term,
               UPPER(IFNULL(c.relasi, ''))     AS relasi,
               UPPER(IFNULL(c.item_type1, '')) AS item_type,
               IFNULL(s.payment_rating, '')    AS flexibility,
               p.plan_date, p.doc_number,
               /* RENCANA AWAL - Kamis di minggu Minggu-Sabtu milik jatuh
                  tempo. Dibaca dari ap_payment_plan_awal, tapi dihitung
                  langsung kalau barisnya belum ada, supaya kolomnya tidak
                  pernah kosong walau migrasinya belum dijalankan.
                  DAYOFWEEK: 1=Minggu, Kamis=5, jadi selisihnya 5-DAYOFWEEK
                  (negatif untuk Jumat & Sabtu - memang begitu maunya). */
               COALESCE(pa.plan_awal,
                        DATE_ADD(k.tgl_tempo, INTERVAL (5 - DAYOFWEEK(k.tgl_tempo)) DAY)) AS plan_awal
        FROM kontrabon_h k
        LEFT JOIN mastercoa_v2 c   ON c.no_coa = k.no_coa
        /* mastersupplier DIKELOMPOKKAN DULU: tabelnya bisa memuat lebih dari
           satu baris untuk satu nama supplier (mis. 'PT. OBOR MAJU JAYA'
           punya dua), dan LEFT JOIN mentah MENGGANDAKAN baris voucher. */
        LEFT JOIN (SELECT Supplier, MAX(payment_rating) AS payment_rating
                   FROM mastersupplier GROUP BY Supplier) s
               ON s.Supplier = k.nama_supp
        LEFT JOIN $rencana p       ON p.no_kbon = k.no_kbon
        LEFT JOIN ap_payment_plan_awal pa ON pa.no_kbon = k.no_kbon
        /* Penyaring outstanding. GROUP BY-nya penjaga: kalau suatu saat
           query AP Report menghasilkan lebih dari satu baris per voucher,
           daftar di sini tetap tidak menggandakan barisnya.
           Sisa di bawah 1 (mata uang dokumen) dianggap LUNAS: itu pecahan
           pembulatan antara utang di jurnal PV dan nilai yang dibayar, mis.
           PV-AP/REG/NAG/2026/07/01709 utang 15.494.094,41 dibayar BK
           15.494.094,40 - sisa 0,01 dulu membuatnya tetap muncul di sini
           (10 Nov 2026). Sama dgn ajx_ap_projection_report.php. */
        INNER JOIN (SELECT no_kbon FROM ($sqlOutstanding) o
                    WHERE ABS(o.saldo_akhir) >= 1
                    GROUP BY no_kbon) os
               ON os.no_kbon = k.no_kbon
        WHERE " . implode(' AND ', $where) . "
        ORDER BY k.tgl_tempo ASC, k.no_kbon ASC";

$res = mysqli_query($conn1, $sql);
if ($res === false) {
    echo json_encode(array('data' => array(), 'error' => mysqli_error($conn1)));
    exit;
}

function appTgl($v)
{
    /* 'd M Y' -> "10 Oct 2026", sepasang dgn FORMAT_TGL = 'dd M yyyy' di
       create-payment-plan.php. Kolom Payment Plan berubah jadi kotak
       tanggal waktu barisnya dicentang, dan kotak itu membaca isinya
       sendiri - kalau kedua format berbeda, kotaknya gagal membaca lalu
       jatuh ke hari ini. Jadi dua baris ini harus diubah bersamaan. */
    return (!empty($v) && $v !== '0000-00-00') ? date('d M Y', strtotime($v)) : '';
}
function appAngka($v)
{
    return number_format((float) $v, 2, '.', ',');
}

/* Relationship HANYA dua nilai: GROUP dan NON GROUP (permintaan pemakai
   9 Nov 2026). Dulu di sini GROUP diganti nama jadi 'Related Party' dan
   NON GROUP jadi 'Third Party'; padanannya persis itu, jadi yang berubah
   cuma namanya. Disamakan dgn apprRelasi() di laporan proyeksi dan
   aprvRelasi() di laporan realisasi supaya tiga layar ini sekosakata.
   Yang di luar dua itu dibiarkan apa adanya supaya nilai baru tidak
   diam-diam hilang. */
function appRelasi($v)
{
    $v = strtoupper(trim((string) $v));
    if ($v === '') { return ''; }
    /* 'NON' diperiksa lebih dulu - 'NON GROUP' juga memuat kata 'GROUP'. */
    if (strpos($v, 'NON') !== false || strpos($v, 'THIRD') !== false) { return 'NON GROUP'; }
    if (strpos($v, 'GROUP') !== false || strpos($v, 'RELATED') !== false) { return 'GROUP'; }
    return $v;
}

$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    /* RUMUS DIPERBAIKI 5 Nov 2026. kontrabon_h.total sudah NET dipotong
       PPh 23; "Total Invoice" = total + PPh, dan Tax Base dihitung dari
       situ. Rumus lama (total - tax) meleset untuk SETIAP voucher ber-PPh:
       372 voucher di 2026 saja. Dicocokkan ke lembar "Projection" di
       spesifikasi AP Payment Realization & Variance Report Ver 1.1, lalu
       diuji ke 2.000 voucher - Base+VAT selalu = Total Invoice dan tidak
       ada Base negatif. Kolom subtotal TETAP tidak dipakai: isinya tidak
       konsisten antar jenis dokumen (pada SI/APR/NAG/2026/06/01376 berisi
       109.337.000 padahal totalnya 54.668.500). */
    $pph   = (float) $row['pph'];
    $bayar = (float) $row['total'];      /* Payment Amount - sudah net PPh */
    $tax   = (float) $row['tax'];
    $total = $bayar + $pph;              /* Total Invoice */

    /* 'plan_date' = rencana yang SEDANG BERLAKU. Sejak 9 Nov 2026 voucher
       yang sudah punya rencana ikut muncul di daftar (lihat hanya_belum di
       create-payment-plan.php), dan nilai ini yang mengisi kolom Payment
       Plan untuk voucher tsb.

       TIDAK dibuatkan kolom sendiri: pemakai meminta satu kolom tanggal
       yang terus diperbarui, bukan dua kolom yang harus dibandingkan. */
    $relasi = appRelasi($row['relasi']);
    $item   = (string) $row['item_type'];
    $flex   = trim((string) $row['flexibility']);

    $data[] = array(
        'no_kbon'      => htmlspecialchars($row['no_kbon'], ENT_QUOTES),
        'nama_supp'    => htmlspecialchars($row['nama_supp'], ENT_QUOTES),
        'tgl_kbon'     => appTgl($row['tgl_kbon']),
        'tgl_tempo'    => appTgl($row['tgl_tempo']),
        'plan_awal'    => appTgl($row['plan_awal']),
        /* Rencana yang BERLAKU sekarang - kosong kalau vouchernya belum
           pernah dijadwalkan. 'plan_doc' dipakai di modal rincian voucher,
           supaya ketahuan rencana itu datang dari dokumen yang mana. */
        'plan_date'    => appTgl($row['plan_date']),
        'plan_doc'     => htmlspecialchars((string) $row['doc_number'], ENT_QUOTES),
        'term'         => ($row['term'] === null ? '' : (string) $row['term']),
        'relasi'       => htmlspecialchars($relasi !== '' ? $relasi : '-', ENT_QUOTES),
        'item_type'    => htmlspecialchars($item !== '' ? $item : '-', ENT_QUOTES),
        'flexibility'  => htmlspecialchars($flex !== '' ? $flex : '-', ENT_QUOTES),
        'curr'         => htmlspecialchars($row['curr'], ENT_QUOTES),
        /* Tax Base & VAT tidak lagi jadi KOLOM di formulir (tabelnya dibuat
           ramping, 5 Nov 2026) tapi tetap dikirim: keduanya tampil di modal
           rincian yang terbuka waktu nomor voucher diklik. */
        'tax_base'      => appAngka($total - $tax),
        'tax'           => appAngka($tax),
        'pph'           => appAngka($pph),
        'total_invoice' => appAngka($total),
        /* 'total' TETAP berarti NILAI YANG DIBAYAR (kontrabon_h.total, sudah
           net PPh) - itu yang ditampilkan kolom Total di tabel DAN yang
           disimpan simpan_payment_plan.php ke ap_payment_plan_det.amount.
           Kalau diganti jadi Total Invoice, yang terlihat di layar tidak
           lagi sama dgn yang tersimpan. Nilai brutonya dikirim terpisah
           sbg 'total_invoice' untuk modal rincian. */
        'total'         => appAngka($bayar),
        'total_mentah'  => number_format($bayar, 2, '.', ''),
        /* --- hanya untuk modal rincian, bukan kolom tabel --- */
        'status'       => htmlspecialchars((string) $row['status'], ENT_QUOTES),
        'create_user'  => htmlspecialchars((string) $row['create_user'], ENT_QUOTES),
        'no_faktur'    => htmlspecialchars((string) $row['no_faktur'], ENT_QUOTES),
        'supp_inv'     => htmlspecialchars((string) $row['supp_inv'], ENT_QUOTES),
        'tgl_inv'      => appTgl($row['tgl_inv']),
    );
}

echo json_encode(array('data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
?>
