<?php
/* ============================================================================
   AP HISTORY PROJECTION - EKSPOR EXCEL satu dokumen potret.
   Dipanggil tombol Excel di kartu "Document detail" ap_history_projection.php,
   dgn nomor dokumen yang SEDANG terbuka di layar.

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI, dan ini bukan kerapian belaka:
   halaman History membuka POTRET, jadi menghitung ulang berarti berkas
   Excel-nya bisa memuat angka yang berbeda dari layar hanya karena kurs atau
   rencana bayar berubah kemarin. Berkas ini menyetel $_POST['doc_number'],
   meng-include ajx_history_projection_detail.php dalam mode pustaka
   (HPJ_PUSTAKA), lalu memakai keluarannya apa adanya.

   Bentuknya SENGAJA sama dgn ekspor_ap_projection_report.php - tabel HTML
   ber-tipe vnd-ms-excel, bukan PhpSpreadsheet - supaya dua berkas itu bisa
   ditaruh bersebelahan dan isinya terbaca sama. Sel angka memakai pola
   <td value="MENTAH">TERFORMAT</td> supaya Excel membacanya sebagai ANGKA,
   bukan teks.

   header() DIPANGGIL PALING AWAL, sebelum ada keluaran apa pun - termasuk
   sebelum include di bawah, karena include itu bisa menulis apa saja.
   ============================================================================ */

$docMinta = isset($_REQUEST['doc_number']) ? trim((string) $_REQUEST['doc_number']) : '';

/* Nomor dokumen ikut jadi nama berkas supaya beberapa unduhan tidak saling
   menimpa di folder Downloads. Karakter yang tidak boleh ada di nama berkas
   Windows - '/' dalam AP-PPR/1126/00001 yang pertama - diganti strip. */
$namaBerkas = 'AP History Projection'
            . ($docMinta !== '' ? ' - ' . preg_replace('/[^A-Za-z0-9_-]+/', '-', $docMinta) : '')
            . '.xls';
header('Content-type: application/vnd-ms-excel');
header('Content-Disposition: attachment; filename=' . $namaBerkas);

/* ---- ambil datanya lewat endpoint yang sama dgn layar ---- */
define('HPJ_PUSTAKA', 1);
$_POST['doc_number'] = $docMinta;

ob_start();
include __DIR__ . '/ajx_history_projection_detail.php';
$jawaban = json_decode(trim((string) ob_get_clean()), true);

$dataBaris  = (is_array($jawaban) && isset($jawaban['data']))  ? $jawaban['data']  : array();
$kolomTgl   = (is_array($jawaban) && isset($jawaban['kolom'])) ? $jawaban['kolom'] : array();
$info       = (is_array($jawaban) && isset($jawaban['info']))  ? $jawaban['info']  : array();
$pesanSalah = (is_array($jawaban) && isset($jawaban['error'])) ? $jawaban['error'] : '';

/* Angka di jawaban sudah berformat ribuan (1,234.56). Untuk atribut value=
   Excel butuh bentuk mentahnya. */
function ehpMentah($v)
{
    return (float) str_replace(',', '', (string) $v);
}
function ehpTeks($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES);
}
/* Kolom tanggal yang kosong ditulis kosong, bukan 0 - supaya SUM di Excel
   tidak berubah dan kerangkanya tetap terbaca. */
function ehpSelAngka($v, $kosongJadiStrip = false)
{
    $s = trim((string) $v);
    if ($s === '' || $s === '-') {
        return '<td style="text-align:right;">' . ($kosongJadiStrip ? '-' : '') . '</td>';
    }
    return '<td style="text-align:right;" value="' . ehpMentah($s) . '">' . ehpTeks($s) . '</td>';
}
function ehpInfo($k, $bila = '-')
{
    /* `global` WAJIB. $info dibaca di luar fungsi ini, jadi tanpa baris ini
       $info di dalam sini selalu kosong dan SELURUH keterangan kepala
       tercetak '-' - persis yang terlihat di berkas .xls pertama (Document,
       Period, Type, Outstanding as of, Saved by semuanya strip, sedangkan
       Rate used benar karena yang itu membaca $info langsung di luar fungsi).
       Diam-diam salah: berkasnya tetap terunduh dan tabelnya tetap benar. */
    global $info;
    return isset($info[$k]) && $info[$k] !== '' ? $info[$k] : $bila;
}

/* URUTAN KOLOM DISALIN DARI ekspor_ap_projection_report.php dan harus tetap
   sama: dokumen potret ini adalah laporan proyeksi yang dibekukan, jadi dua
   berkas Excel itu memang harus bisa ditumpuk. Kuncinya pun sudah seragam -
   ajx_history_projection_detail.php mengirim nama medan yang sama persis
   dgn ajx_ap_projection_report.php. */
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
$AWAL_PAYABLE = 13;
$JML_PAYABLE  = 5;
$KOLOM_ANGKA = array('invoice_amount', 'rate', 'tax_base', 'vat',
                     'total_invoice', 'pph', 'payment_amount');
?>
<style>
    table { border-collapse: collapse; }
    /* Garis tabel HITAM TIPIS, satuan .5pt - sama dgn ekspor proyeksi.
       BUKAN 1px: Excel memetakan 1px ke "medium" dan garisnya jadi tebal. */
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
    <?php
    /* KEPALANYA MENGIKUTI LEMBAR "Projection" di berkas gabungan
       (ekspor_ap_realization_report.php, LEMBAR 4) - diminta pemakai 9 Nov
       2026: judul, nomor dokumen, periode. Tiga baris itu saja.

       Empat keterangan lama - Type / Supplier / Outstanding as of / Saved by
       / Rate used - dibuang DARI BERKAS EXCEL-nya saja. Semuanya tetap
       dikirim endpoint dan tetap tampil di kartu Document detail, jadi tidak
       ada keterangan yang hilang dari sistem.

       Judulnya SENGAJA sama dgn ekspor laporan hidupnya: dokumen potret ini
       memang laporan proyeksi yang dibekukan, dan baris Doc Number di
       bawahnya yang membedakan keduanya.

       Ditulis sbg komentar PHP, BUKAN komentar HTML: komentar HTML ikut
       terkirim ke dalam berkas .xls-nya. */
    ?>
    <tr><td class="judul" colspan="6">AP PAYMENT PROJECTION REPORT</td></tr>
    <tr><td class="ket" colspan="6">Doc Number : <?php echo ehpTeks(ehpInfo('doc_number')); ?></td></tr>
    <tr><td class="ket" colspan="6">Period : <?php echo ehpTeks(ehpInfo('periode')); ?></td></tr>
    <?php
    /* Peringatan DI BAWAH ini tetap dicetak. Ia bukan keterangan kepala yang
       rutin - hanya muncul kalau dokumennya bermasalah, dan justru itulah
       yang tidak boleh diam-diam hilang dari berkas yang beredar. */
    if ($pesanSalah !== '') { ?>
    <tr><td class="ket" colspan="6" style="color:#a3281c;">GAGAL: <?php echo ehpTeks($pesanSalah); ?></td></tr>
    <?php }
    /* Peringatan keutuhan IKUT DICETAK, bukan hanya tampil di layar: kalau
       rincian dokumennya tidak lagi berjumlah sama dgn total yang tersimpan
       di kepalanya, berkas Excel ini pun tidak boleh dipakai diam-diam. */
    if (isset($info['utuh']) && !$info['utuh']) { ?>
    <tr><td class="ket" colspan="6" style="color:#a3281c;">WARNING: the detail rows add up to
        <?php echo ehpTeks(ehpInfo('total')); ?> but the document header stores
        <?php echo ehpTeks(ehpInfo('total_tersimpan')); ?> - some detail rows are missing.</td></tr>
    <?php }
    if (!empty($info['di_luar_periode'])) { ?>
    <tr><td class="ket" colspan="6" style="color:#a3281c;">WARNING: outside the document period -
        <?php echo ehpTeks(implode(', ', $info['di_luar_periode'])); ?></td></tr>
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
                echo '<th rowspan="2">' . ehpTeks($KOLOM_TETAP[$i][1]) . '</th>';
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
                echo '<th class="sub-pay">' . ehpTeks($KOLOM_TETAP[$i][1]) . '</th>';
            }
            foreach ($kolomTgl as $k) {
                echo '<th class="sub-prj">' . ehpTeks($k['label']) . '</th>';
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
                    echo ehpSelAngka($isi);
                } else {
                    echo '<td>' . ehpTeks($isi) . '</td>';
                }
            }
            foreach ($kolomTgl as $k) {
                echo ehpSelAngka(isset($row[$k['key']]) ? $row[$k['key']] : '', true);
            }
            ?>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="total">
            <td colspan="<?php echo count($KOLOM_TETAP); ?>" style="text-align:right;">Projected Cash Outflow</td>
            <?php foreach ($kolomTgl as $k) { echo ehpSelAngka($k['total']); } ?>
        </tr>
    </tfoot>
</table>
