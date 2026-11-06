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

   Kosakatanya diterjemahkan ke bahasa lembar kerja: GROUP -> Related Party,
   NON GROUP -> Third Party. Dibandingkan dgn UPPER() karena isinya campur
   ('Group' dan 'GROUP' dua-duanya ada di master COA).

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
    $t = strtotime(str_replace('/', '-', $v));   /* dd-mm-yyyy dibaca sbg d-m-Y */
    return $t ? date('Y-m-d', $t) : '';
}

$start_date = appTanggal(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$end_date   = appTanggal(isset($_POST['end_date']) ? $_POST['end_date'] : '');
$supplier   = trim(isset($_POST['supplier']) ? $_POST['supplier'] : '');
$dasar      = (isset($_POST['dasar']) && $_POST['dasar'] === 'kbon') ? 'kbon' : 'tempo';
$hanya_blm  = (isset($_POST['hanya_belum']) && $_POST['hanya_belum'] === '1');

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

$sql = "SELECT k.no_kbon, k.tgl_kbon, k.tgl_tempo, k.nama_supp,
               k.curr, k.rate, k.total, k.tax, k.no_coa,
               DATEDIFF(k.tgl_tempo, k.tgl_kbon) AS term,
               UPPER(IFNULL(c.relasi, ''))     AS relasi,
               UPPER(IFNULL(c.item_type1, '')) AS item_type,
               IFNULL(s.payment_rating, '')    AS flexibility,
               p.plan_date, p.doc_number
        FROM kontrabon_h k
        LEFT JOIN mastercoa_v2 c   ON c.no_coa = k.no_coa
        LEFT JOIN mastersupplier s ON s.Supplier = k.nama_supp
        LEFT JOIN $rencana p       ON p.no_kbon = k.no_kbon
        WHERE " . implode(' AND ', $where) . "
        ORDER BY k.tgl_tempo ASC, k.no_kbon ASC";

$res = mysqli_query($conn1, $sql);
if ($res === false) {
    echo json_encode(array('data' => array(), 'error' => mysqli_error($conn1)));
    exit;
}

function appTgl($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d-m-Y', strtotime($v)) : '';
}
function appAngka($v)
{
    return number_format((float) $v, 2, '.', ',');
}

/* Kosakata master COA ke kosakata lembar kerja tim AP. Yang di luar dua itu
   dibiarkan apa adanya supaya nilai baru tidak diam-diam hilang. */
function appRelasi($v)
{
    $v = trim((string) $v);
    if ($v === 'GROUP')     { return 'Related Party'; }
    if ($v === 'NON GROUP') { return 'Third Party'; }
    return $v;
}

$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    $total = (float) $row['total'];
    $tax   = (float) $row['tax'];

    $sudah = !empty($row['plan_date']) && $row['plan_date'] !== '0000-00-00';
    $pil = $sudah
        ? '<span class="ftl-st is-approved">' . htmlspecialchars((string) $row['doc_number'], ENT_QUOTES) . '</span>'
        : '<span class="ftl-st is-draft">Not Scheduled</span>';

    $relasi = appRelasi($row['relasi']);
    $item   = (string) $row['item_type'];
    $flex   = trim((string) $row['flexibility']);

    $data[] = array(
        'no_kbon'      => htmlspecialchars($row['no_kbon'], ENT_QUOTES),
        'nama_supp'    => htmlspecialchars($row['nama_supp'], ENT_QUOTES),
        'tgl_kbon'     => appTgl($row['tgl_kbon']),
        'tgl_tempo'    => appTgl($row['tgl_tempo']),
        'term'         => ($row['term'] === null ? '' : (string) $row['term']),
        'relasi'       => htmlspecialchars($relasi !== '' ? $relasi : '-', ENT_QUOTES),
        'item_type'    => htmlspecialchars($item !== '' ? $item : '-', ENT_QUOTES),
        'flexibility'  => htmlspecialchars($flex !== '' ? $flex : '-', ENT_QUOTES),
        'curr'         => htmlspecialchars($row['curr'], ENT_QUOTES),
        'tax_base'     => appAngka($total - $tax),
        'tax'          => appAngka($tax),
        'total'        => appAngka($total),
        'total_mentah' => number_format($total, 2, '.', ''),
        'plan_date'    => appTgl($row['plan_date']),
        'status_plan'  => $pil,
    );
}

echo json_encode(array('data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
?>
