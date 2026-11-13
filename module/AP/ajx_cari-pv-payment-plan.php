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
/* Tanggal voucher. TIDAK semua baris berasal dari kontrabon_h (lihat
   keterangan panjang di bawah), jadi dibaca berurutan dari tiga
   sumbernya - sama dgn laporan Proyeksi AP. */
$eTglKbon = 'COALESCE(k.tgl_kbon, sa.tgl_kbon, ki.tgl_kbon)';

/* Status hanya dimiliki baris kontrabon_h. Baris cicilan & saldo awal
   tidak punya kolom itu, dan sudah lolos saringan "masih outstanding"
   milik AP Report - itu patokan yang sebenarnya. */
$where = array("(k.no_kbon IS NULL OR k.status IN ('Approved','FIRST APPROVED','SECOND APPROVED'))");

if ($start_date !== '' && $end_date !== '') {
    $kolom = ($dasar === 'kbon') ? $eTglKbon : 'x.duedate';
    $where[] = $kolom . " BETWEEN '" . mysqli_real_escape_string($conn1, $start_date)
             . "' AND '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}
if ($supplier !== '') {
    $where[] = "x.supplier = '" . mysqli_real_escape_string($conn1, $supplier) . "'";
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

/* ---------------------------------------------------------------------------
   DAFTARNYA DIBANGUN DARI SISI "MASIH OUTSTANDING", BUKAN DARI kontrabon_h.

   Sampai 12 Nov 2026 query ini berjalan FROM kontrabon_h lalu menyaring
   dgn daftar outstanding. Akibatnya voucher yang TIDAK punya baris di
   kontrabon_h tidak pernah muncul di layar ini sama sekali - padahal
   utangnya ada dan di AP Report maupun laporan Proyeksi AP terbaca.
   Dilaporkan pemakai: nomor CICILAN tidak ada di menu ini.

   Diperiksa ke produksi: dari 326 voucher outstanding, hanya 191 yang ada
   di kontrabon_h. Sisanya 29 baris cicilan (Rp 1,71 M) dan 106 baris saldo
   awal (Rp 6,16 M, 70 di antaranya BELUM jatuh tempo) - semuanya hilang.

   Sekarang dibalik: FROM daftar outstanding, lalu tiga sumber nilainya
   ditempel sbg LEFT JOIN. Susunannya MENIRU ajx_ap_projection_report.php
   supaya dua layar yang memakai patokan sama tidak lagi berbeda isi.

   Semua cadangan DIKELOMPOKKAN DULU (GROUP BY): tabel sumbernya bisa
   memuat lebih dari satu baris untuk satu nomor, dan LEFT JOIN mentah akan
   MENGGANDAKAN baris di layar. Daftar outstanding-nya sendiri sudah satu
   baris per nomor (dicek ke produksi: 326 baris, 326 nomor berbeda).
   --------------------------------------------------------------------------- */
$sql = "SELECT x.no_kbon,
               $eTglKbon                                    AS tgl_kbon,
               x.duedate                                    AS tgl_tempo,
               x.supplier                                   AS nama_supp,
               x.curr,
               COALESCE(k.total,   sa.total, ins.total, 0)  AS total,
               COALESCE(k.tax,     sa.tax,   ins.ppn,   0)  AS tax,
               COALESCE(k.pph_idr, sa.pph,   ins.pph,   0)  AS pph,
               /* Lima kolom berikut hanya dimiliki kontrabon_h - untuk baris
                  cicilan & saldo awal memang kosong, dan di modal rincian
                  tampil sbg tanda hubung. */
               k.status, k.create_user, k.no_faktur, k.supp_inv, k.tgl_inv,
               /* PAYMENT TERM = TOP menurut PO, sama untuk semua cicilan satu
                  dokumen - bukan jarak hari ke jatuh tempo cicilan itu. Urutan
                  cadangannya disamakan dgn laporan Proyeksi AP. */
               COALESCE(DATEDIFF(k.tgl_tempo,   k.tgl_kbon),
                        DATEDIFF(ind.tgl_tempo, ind.tgl_kbon),
                        ins.top,
                        DATEDIFF(sa.tgl_tempo,  sa.tgl_kbon))  AS term,
               /* Relationship & Item Type diambil dari daftar outstanding-nya
                  langsung - di sana sudah disambung ke mastercoa_v2, jadi
                  sumbernya persis sama dgn AP Report. */
               UPPER(IFNULL(x.relasi, ''))     AS relasi,
               UPPER(IFNULL(x.item_type1, '')) AS item_type,
               IFNULL(s.payment_rating, '')    AS flexibility,
               p.plan_date, p.doc_number,
               /* RENCANA AWAL - Kamis di minggu Minggu-Sabtu milik jatuh
                  tempo. Dibaca dari ap_payment_plan_awal, tapi dihitung
                  langsung kalau barisnya belum ada, supaya kolomnya tidak
                  pernah kosong walau migrasinya belum dijalankan.
                  DAYOFWEEK: 1=Minggu, Kamis=5, jadi selisihnya 5-DAYOFWEEK
                  (negatif untuk Jumat & Sabtu - memang begitu maunya). */
               COALESCE(pa.plan_awal,
                        DATE_ADD(x.duedate, INTERVAL (5 - DAYOFWEEK(x.duedate)) DAY)) AS plan_awal
        FROM ($sqlOutstanding) x

        /* 1. voucher biasa */
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
                 WHEN x.no_kbon REGEXP '-[0-9]{3}$'
                      THEN LEFT(x.no_kbon, CHAR_LENGTH(x.no_kbon) - 4)
                 ELSE x.no_kbon END

        /* 2. saldo awal (utang pembukaan per 30 Jun 2026) */
        LEFT JOIN (SELECT no_kbon, MAX(tgl_kbon) AS tgl_kbon, MAX(tgl_tempo) AS tgl_tempo,
                          MAX(tax) AS tax, MAX(pph) AS pph, MAX(total) AS total
                   FROM ap_saldo_payment_voucher GROUP BY no_kbon) sa
               ON sa.no_kbon = x.no_kbon

        /* 3. cicilan - nomor anak spt PV-AP/INS/.../00001-002 */
        LEFT JOIN (SELECT no_kbon_det, MAX(no_kbon) AS induk, MAX(top) AS top,
                          SUM(ppn) AS ppn, SUM(pph) AS pph, SUM(total) AS total
                   FROM kontrabon_h_installment_detail GROUP BY no_kbon_det) ins
               ON ins.no_kbon_det = x.no_kbon
        LEFT JOIN kontrabon_h ki ON ki.no_kbon = ins.induk

        /* mastersupplier DIKELOMPOKKAN DULU: tabelnya bisa memuat lebih dari
           satu baris untuk satu nama supplier (mis. 'PT. OBOR MAJU JAYA'
           punya dua), dan LEFT JOIN mentah MENGGANDAKAN baris voucher. */
        LEFT JOIN (SELECT Supplier, MAX(payment_rating) AS payment_rating
                   FROM mastersupplier GROUP BY Supplier) s
               ON s.Supplier = x.supplier
        LEFT JOIN $rencana p              ON p.no_kbon = x.no_kbon
        LEFT JOIN ap_payment_plan_awal pa ON pa.no_kbon = x.no_kbon

        /* Sisa di bawah 1 (mata uang dokumen) dianggap LUNAS: itu pecahan
           pembulatan antara utang di jurnal PV dan nilai yang dibayar, mis.
           PV-AP/REG/NAG/2026/07/01709 utang 15.494.094,41 dibayar BK
           15.494.094,40 - sisa 0,01 dulu membuatnya tetap muncul di sini
           (10 Nov 2026). Sama dgn ajx_ap_projection_report.php.
           SALDO NEGATIF BUKAN UTANG (12 Nov 2026): yang dipakai saldo_akhir
           >= 1, bukan ABS(...) >= 1. Saldo minus artinya pembayarannya
           MELEBIHI utangnya - tidak ada yang perlu dijadwalkan. Dulu ABS()
           ikut menangkapnya, sehingga PV-AP/REG/NAG/2026/10/02574 (sisa
           -1,66) tetap muncul di sini padahal di AP Report sudah lunas. */
        WHERE x.saldo_akhir >= 1 AND " . implode(' AND ', $where) . "
        ORDER BY x.duedate ASC, x.no_kbon ASC";

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
