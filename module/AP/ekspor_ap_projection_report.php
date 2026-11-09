<?php
/* ============================================================================
   AP PAYMENT PROJECTION REPORT - EKSPOR EXCEL.
   Dipanggil tombol Export di ap_projection_report.php dgn saringan yang SAMA
   PERSIS seperti yang sedang tampil di layar.

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI. Berkas ini menyetel $_POST lalu
   meng-include ajx_ap_projection_report.php dalam mode pustaka
   (APPR_PUSTAKA) dan memakai keluarannya apa adanya. Jadi isi Excel dijamin
   sama dgn layar - kalau rumusnya berubah, dua-duanya ikut berubah
   bersamaan. Menyalin querynya ke sini cepat atau lambat membuat Excel dan
   layar berbeda, dan itu jenis selisih yang paling lama ketahuannya.

   Bentuknya mengikuti ekspor_payment_plan.php: tabel HTML yang dikirim dgn
   tipe vnd-ms-excel, bukan PhpSpreadsheet - seragam dgn ekspor AP lain dan
   tidak menambah kebergantungan. Sel angka memakai pola
   <td value="MENTAH">TERFORMAT</td>, supaya Excel membacanya sebagai ANGKA
   dan bukan teks.

   header() DIPANGGIL PALING AWAL, sebelum ada keluaran apa pun - termasuk
   sebelum include di atas, karena include itu bisa menulis apa saja.
   ============================================================================ */

$namaBerkas = 'AP Payment Projection Report.xls';
header('Content-type: application/vnd-ms-excel');
header('Content-Disposition: attachment; filename=' . $namaBerkas);

/* ---- ambil datanya lewat endpoint yang sama dgn layar ---- */
define('APPR_PUSTAKA', 1);

$_POST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$_POST['end_date']   = isset($_REQUEST['end_date'])   ? $_REQUEST['end_date']   : '';
$_POST['nama_supp']  = isset($_REQUEST['nama_supp'])  ? $_REQUEST['nama_supp']  : 'ALL';
$_POST['type']       = isset($_REQUEST['type'])       ? $_REQUEST['type']       : 'daily';

ob_start();
include __DIR__ . '/ajx_ap_projection_report.php';
$jawaban = json_decode(trim((string) ob_get_clean()), true);

$dataBaris = (is_array($jawaban) && isset($jawaban['data']))  ? $jawaban['data']  : array();
$kolomTgl  = (is_array($jawaban) && isset($jawaban['kolom'])) ? $jawaban['kolom'] : array();
$info      = (is_array($jawaban) && isset($jawaban['info']))  ? $jawaban['info']  : array();
$pesanSalah = (is_array($jawaban) && isset($jawaban['error'])) ? $jawaban['error'] : '';

/* Angka di jawaban sudah berformat ribuan (1,234.56). Untuk atribut value=
   Excel butuh bentuk mentahnya. */
function aprMentah($v)
{
    return (float) str_replace(',', '', (string) $v);
}
function aprTeks($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES);
}
/* Kolom tanggal yang kosong ditulis kosong, bukan 0 - supaya SUM di Excel
   tidak berubah dan kerangkanya tetap terbaca. */
function aprSelAngka($v, $kosongJadiStrip = false)
{
    $s = trim((string) $v);
    if ($s === '' || $s === '-') {
        return '<td style="text-align:right;">' . ($kosongJadiStrip ? '-' : '') . '</td>';
    }
    return '<td style="text-align:right;" value="' . aprMentah($s) . '">' . aprTeks($s) . '</td>';
}

$KOLOM_TETAP = array(
    array('no',             'No'),
    array('supplier',       'Supplier'),
    array('no_kbon',        'Payment Voucher No'),
    array('tgl_kbon',       'PV Date'),
    array('term',           'Payment Term'),
    array('duedate',        'Due Date'),
    array('tgl_proyeksi',   'Payment Plan Date'),
    array('relasi',         'Relationship'),
    array('item_type',      'Item Type'),
    array('flexibility',    'Flexibility Category'),
    array('curr',           'Currency'),
    array('invoice_amount', 'Invoice Amount'),
    array('rate',           'Rate'),
    array('tax_base',       'Tax Base'),
    array('vat',            'VAT'),
    array('total_invoice',  'Total Invoice'),
    array('pph',            'Income Tax Art 23'),
    array('payment_amount', 'Payment Amount'),
);
/* Lima kolom terakhir bernaung di bawah payung "Payable Amount", sama dgn
   lembar "Projection" di berkas spesifikasi. */
$AWAL_PAYABLE = 13;
$JML_PAYABLE  = 5;
/* Kolom yang isinya ANGKA - sisanya ditulis sebagai teks apa adanya. */
$KOLOM_ANGKA = array('invoice_amount', 'rate', 'tax_base', 'vat',
                     'total_invoice', 'pph', 'payment_amount');
?>
<style>
    table { border-collapse: collapse; }
    /* Garis tabel HITAM TIPIS (diminta pemakai 6 Nov 2026).
       SATUANNYA .5pt, BUKAN 1px: Excel memetakan lebar garis ke tebalnya
       sendiri, dan 1px jatuh ke "medium" - itulah yang tampak kelewat tebal
       pada percobaan pertama. .5pt adalah garis "thin" bawaan Excel, sama
       dgn yang dipakai kalau orang memasang border lewat menu Excel. */
    th, td { border: .5pt solid #000000; padding: 3px 6px; font-size: 10px;
             font-family: Calibri, Arial, sans-serif; white-space: nowrap; }
    th { background: #1e3a5f; color: #ffffff; font-weight: bold;
         text-align: center; vertical-align: middle; }
    th.grp-pay { background: #2c5282; }
    th.sub-pay { background: #3563a0; }
    th.grp-prj { background: #115e59; }
    th.sub-prj { background: #0f766e; }
    td.judul { border: 0; font-size: 13px; font-weight: bold; }
    td.ket   { border: 0; font-size: 10px; }
    tr.total td { background: #1e3a5f; color: #ffffff; font-weight: bold; }
</style>

<table>
    <tr><td class="judul" colspan="6">AP PAYMENT PROJECTION REPORT</td></tr>
    <tr><td class="ket" colspan="6">Period : <?php echo aprTeks(isset($info['periode']) ? $info['periode'] : '-'); ?></td></tr>
    <?php
    /* "Outstanding as of" DIBUANG dari kepala berkas (diminta pemakai 6 Nov
       2026). Ditulis sbg komentar PHP, BUKAN komentar HTML: komentar HTML
       ikut terkirim ke dalam berkas .xls-nya. Tanggalnya tetap dikirim
       endpoint dan masih tampil di layar History Projection, jadi
       keterangannya tidak hilang dari sistem - hanya tidak dicetak. */
    ?>
    <tr><td class="ket" colspan="6">Type : <?php echo aprTeks(strtoupper(isset($info['type']) ? $info['type'] : '-')); ?></td></tr>
    <?php if ($pesanSalah !== '') { ?>
    <tr><td class="ket" colspan="6" style="color:#a3281c;">GAGAL: <?php echo aprTeks($pesanSalah); ?></td></tr>
    <?php } ?>
    <?php
    /* Mata uang tanpa kurs = angka rupiahnya tidak bisa dipercaya. Ikut
       dicetak di Excel, bukan cuma di layar - berkas ini sering beredar
       lepas dari layarnya. */
    if (!empty($info['kurs_hilang'])) { ?>
    <tr><td class="ket" colspan="6" style="color:#a3281c;">Exchange rate not found for:
        <?php echo aprTeks(implode(', ', $info['kurs_hilang'])); ?> - those rows are converted 1:1,
        so the IDR figures below are not reliable.</td></tr>
    <?php } ?>
    <tr><td class="ket" colspan="6">&nbsp;</td></tr>
</table>

<table>
    <thead>
        <tr>
            <?php
            for ($i = 0; $i < count($KOLOM_TETAP); $i++) {
                if ($i === $AWAL_PAYABLE) {
                    echo '<th class="grp-pay" colspan="' . $JML_PAYABLE . '">Payable Amount</th>';
                }
                if ($i >= $AWAL_PAYABLE && $i < $AWAL_PAYABLE + $JML_PAYABLE) { continue; }
                echo '<th rowspan="2">' . aprTeks($KOLOM_TETAP[$i][1]) . '</th>';
            }
            if ($kolomTgl) {
                echo '<th class="grp-prj" colspan="' . count($kolomTgl) . '">'
                   . 'Projected Cash Outflow for Accounts Payable Payment</th>';
            }
            ?>
        </tr>
        <tr>
            <?php
            for ($i = $AWAL_PAYABLE; $i < $AWAL_PAYABLE + $JML_PAYABLE; $i++) {
                echo '<th class="sub-pay">' . aprTeks($KOLOM_TETAP[$i][1]) . '</th>';
            }
            foreach ($kolomTgl as $k) {
                echo '<th class="sub-prj">' . aprTeks($k['label']) . '</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dataBaris as $row) { ?>
        <tr>
            <?php
            foreach ($KOLOM_TETAP as $kt) {
                $kunci = $kt[0];
                $isi   = isset($row[$kunci]) ? $row[$kunci] : '';
                if (in_array($kunci, $KOLOM_ANGKA, true)) {
                    echo aprSelAngka($isi);
                } else {
                    echo '<td>' . aprTeks($isi) . '</td>';
                }
            }
            foreach ($kolomTgl as $k) {
                echo aprSelAngka(isset($row[$k['key']]) ? $row[$k['key']] : '', true);
            }
            ?>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="total">
            <td colspan="<?php echo count($KOLOM_TETAP); ?>" style="text-align:right;">Projected Cash Outflow</td>
            <?php foreach ($kolomTgl as $k) { echo aprSelAngka($k['total']); } ?>
        </tr>
    </tfoot>
</table>
