<?php
/* ============================================================================
   Update Payment Plan - EKSPOR EXCEL daftar dokumen rencana bayar.
   Dipanggil tombol Excel di list-payment-plan.php dgn saringan yang sama
   persis seperti yang sedang tampil di layar.

   Bentuknya mengikuti ekspor_petty_cash_out.php: tabel HTML yang dikirim
   dgn tipe vnd-ms-excel, bukan PhpSpreadsheet - supaya seragam dgn ekspor
   AP lainnya dan tidak menambah kebergantungan.

   BEDA DARI BERKAS ACUAN - header() DIPANGGIL PALING AWAL, sebelum ada
   keluaran apa pun. Di berkas acuan <style> keluar lebih dulu dan header()
   baru menyusul; itu hanya lolos selama output_buffering menyala di
   php.ini. Di sini urutannya dibalik supaya tidak bergantung pada setelan
   server.

   SATU BARIS = SATU VOUCHER, bukan satu dokumen: yang dipakai tim AP adalah
   rincian per voucher, dan nomor dokumennya ikut di tiap baris supaya tetap
   bisa dikelompokkan sendiri di Excel.

   Relationship / Item Type / Flexibility dibaca LANGSUNG dari sumbernya
   (mastercoa_v2, mastersupplier) sama spt layar Create - tidak disalin ke
   tabel rencana, jadi tidak ada dua sumber yang bisa berbeda.
   ============================================================================ */

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Payment Plan.xls");

include '../../conn/conn.php';

function appYmd($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));
    return $t ? date('Y-m-d', $t) : '';
}
function appTgl($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d M Y', strtotime($v)) : '-';
}
/* Relationship HANYA dua nilai: GROUP dan NON GROUP - sama persis spt
   ajx_cari-pv-payment-plan.php, supaya layar dan ekspornya tidak pernah
   menyebut nama yang berbeda untuk voucher yang sama. */
function appRelasi($v)
{
    $v = strtoupper(trim((string) $v));
    if ($v === '') { return ''; }
    /* 'NON' diperiksa lebih dulu - 'NON GROUP' juga memuat kata 'GROUP'. */
    if (strpos($v, 'NON') !== false || strpos($v, 'THIRD') !== false) { return 'NON GROUP'; }
    if (strpos($v, 'GROUP') !== false || strpos($v, 'RELATED') !== false) { return 'GROUP'; }
    return $v;
}

$doc_num    = isset($_GET['doc_num']) ? trim($_GET['doc_num']) : '';
$status     = isset($_GET['status']) ? trim($_GET['status']) : 'ALL';
$start_date = appYmd(isset($_GET['start_date']) ? $_GET['start_date'] : '');
$end_date   = appYmd(isset($_GET['end_date']) ? $_GET['end_date'] : '');

$where = array('1=1');
if ($start_date !== '' && $end_date !== '') {
    $where[] = "h.plan_date BETWEEN '" . mysqli_real_escape_string($conn1, $start_date)
             . "' AND '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}
if ($doc_num !== '') {
    $where[] = "h.doc_number LIKE '%" . mysqli_real_escape_string($conn1, $doc_num) . "%'";
}
if ($status !== '' && strtoupper($status) !== 'ALL') {
    $where[] = "h.status = '" . mysqli_real_escape_string($conn1, strtoupper($status)) . "'";
}

/* d.plan_date = tanggal rencana PER VOUCHER, h.plan_date = tanggal
   DOKUMEN. Sejak 5 Nov 2026 satu dokumen boleh memuat tanggal yang
   berbeda-beda, jadi dua-duanya dikeluarkan - yang dipakai laporan proyeksi
   adalah yang per voucher.

   mastersupplier disambung lewat SUBQUERY BERKELOMPOK, bukan langsung:
   nama supplier di tabel itu TIDAK unik (mis. 'PT. OBOR MAJU JAYA' punya
   dua baris), jadi join langsung akan MENGGANDAKAN baris ekspornya dan ikut
   menggelembungkan total di kaki laporan. Kesalahan yang sama sudah pernah
   kena di layar Create dan di laporan proyeksi. */
$sql = "SELECT h.doc_number, h.plan_date, h.status AS doc_status, h.keterangan AS doc_note,
               h.created_by, h.created_date, h.cancel_by, h.cancel_date,
               d.no_kbon, d.plan_date AS row_plan_date,
               d.curr, d.amount, d.keterangan AS row_note, d.status AS row_status,
               k.nama_supp, k.tgl_kbon, k.tgl_tempo, k.total, k.tax,
               IFNULL(k.pph_idr, 0) AS pph, k.no_coa,
               DATEDIFF(k.tgl_tempo, k.tgl_kbon) AS term,
               c.relasi, c.item_type1, s.payment_rating
        FROM ap_payment_plan_h h
        INNER JOIN ap_payment_plan_det d ON d.doc_number = h.doc_number
        LEFT JOIN kontrabon_h k    ON k.no_kbon = d.no_kbon
        LEFT JOIN mastercoa_v2 c   ON c.no_coa = k.no_coa
        LEFT JOIN (SELECT Supplier, MAX(payment_rating) AS payment_rating
                   FROM mastersupplier GROUP BY Supplier) s ON s.Supplier = k.nama_supp
        WHERE " . implode(' AND ', $where) . "
        ORDER BY h.id DESC, k.tgl_tempo ASC, d.no_kbon ASC";

$res = mysqli_query($conn1, $sql);

$judulPeriode = ($start_date !== '' && $end_date !== '')
    ? strtoupper(date('d F Y', strtotime($start_date))) . ' - ' . strtoupper(date('d F Y', strtotime($end_date)))
    : 'ALL';
?>
<html>
<head>
    <title>Export Data Ke Excel</title>
</head>
<body>
    <style type="text/css">
        body { font-family: sans-serif; }
        table { width: 100%; margin: 20px auto; border-collapse: collapse; }
        table th, table td { border: 1px solid #3c3c3c; padding: 3px 8px; }
    </style>

    <h4>
        LIST PAYMENT PLAN <br/>
        STATUS <?php echo htmlspecialchars(strtoupper($status !== '' ? $status : 'ALL')); ?>
        <?php if ($doc_num !== '') { ?><br/>DOCUMENT <?php echo htmlspecialchars(strtoupper($doc_num)); ?><?php } ?>
        <br/> PAYMENT PLAN DATE <?php echo htmlspecialchars($judulPeriode); ?>
    </h4>

    <table style="width:100%;font-size:10px;" border="1">
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center; vertical-align: middle;">Document Number</th>
            <!-- DUA tanggal, sejak satu dokumen boleh memuat tanggal yang
                 berbeda-beda (5 Nov 2026):
                   Document Plan Date = tanggal di kepala dokumen, dan itu
                       juga yang dipakai saringan periode di judul laporan.
                   Payment Plan Date  = tanggal voucher INI, dari
                       ap_payment_plan_det. Inilah yang dibaca laporan
                       proyeksi, jadi inilah tanggal yang berlaku. -->
            <th style="text-align: center; vertical-align: middle;">Document Plan Date</th>
            <th style="text-align: center; vertical-align: middle;">Payment Plan Date</th>
            <th style="text-align: center; vertical-align: middle;">Document Status</th>
            <th style="text-align: center; vertical-align: middle;">Document Description</th>
            <th style="text-align: center; vertical-align: middle;">Payment Voucher No</th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">PV Date</th>
            <th style="text-align: center; vertical-align: middle;">Due Date</th>
            <th style="text-align: center; vertical-align: middle;">Payment Term</th>
            <th style="text-align: center; vertical-align: middle;">Relationship</th>
            <th style="text-align: center; vertical-align: middle;">Item Type</th>
            <th style="text-align: center; vertical-align: middle;">Flexibility Category</th>
            <th style="text-align: center; vertical-align: middle;">Curr</th>
            <th style="text-align: center; vertical-align: middle;">Tax Base</th>
            <th style="text-align: center; vertical-align: middle;">VAT</th>
            <th style="text-align: center; vertical-align: middle;">Total Invoice</th>
            <th style="text-align: center; vertical-align: middle;">Planned Amount</th>
            <!-- Row Description = ap_payment_plan_det.keterangan, keterangan
                 PER VOUCHER. Kolomnya sudah ikut diambil query sejak awal
                 (row_note) tapi tidak pernah dicetak - tidak ketahuan selama
                 formulirnya belum punya isian per baris. Sejak formulir
                 Create punya kolom Description (5 Nov 2026), isi itu harus
                 ikut keluar di laporan. -->
            <th style="text-align: center; vertical-align: middle;">Row Description</th>
            <th style="text-align: center; vertical-align: middle;">Row Status</th>
            <th style="text-align: center; vertical-align: middle;">Created By</th>
            <th style="text-align: center; vertical-align: middle;">Created Date</th>
            <th style="text-align: center; vertical-align: middle;">Cancelled By</th>
            <th style="text-align: center; vertical-align: middle;">Cancelled Date</th>
        </tr>
        <?php
        $no = 1;
        /* Total DIPISAH PER MATA UANG. Menjumlahkan IDR dgn USD jadi satu
           angka tidak berarti apa pun. */
        $totalPerMata = array();

        if ($res !== false) {
            while ($row = mysqli_fetch_assoc($res)) {
                /* RUMUS DIPERBAIKI 5 Nov 2026. kontrabon_h.total sudah NET
                   dipotong PPh 23, jadi "Total Invoice" harus ditambah PPh-nya
                   kembali dan Tax Base dihitung dari situ. Rumus lama
                   (total - tax) meleset untuk SETIAP voucher ber-PPh - 372
                   voucher di 2026 saja, mis. SI/APR/NAG/2026/01/00017 selisih
                   12.176.200. Dicocokkan ke lembar "Projection" di
                   "AP Payment Realization & Variance Report Ver 1.1.xlsx" dan
                   diuji ke 2.000 voucher: Base+VAT selalu = Total Invoice,
                   tidak ada Base negatif. Kolom subtotal TETAP tidak dipakai -
                   isinya tidak konsisten antar jenis dokumen. */
                $pph   = (float) $row['pph'];
                $bayar = (float) $row['total'];          /* sudah net PPh */
                $tax   = (float) $row['tax'];
                $total = $bayar + $pph;                  /* Total Invoice */
                $base  = $total - $tax;                  /* Tax Base      */
                $curr  = (string) $row['curr'];
                $nilai = (float) $row['amount'];

                if (!isset($totalPerMata[$curr])) { $totalPerMata[$curr] = 0; }
                $totalPerMata[$curr] += $nilai;

                $flex   = trim((string) $row['payment_rating']);
                $relasi = appRelasi($row['relasi']);
                $item   = trim(strtoupper((string) $row['item_type1']));

                echo '<tr style="font-size:12px;text-align:left;">'
                   . '<td>' . $no++ . '</td>'
                   . '<td value="' . htmlspecialchars($row['doc_number'], ENT_QUOTES) . '">' . htmlspecialchars($row['doc_number']) . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['plan_date'], ENT_QUOTES) . '">' . appTgl($row['plan_date']) . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['row_plan_date'], ENT_QUOTES) . '">' . appTgl($row['row_plan_date']) . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['doc_status']) . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['doc_note'] !== '' ? $row['doc_note'] : '-') . '</td>'
                   . '<td value="' . htmlspecialchars($row['no_kbon'], ENT_QUOTES) . '">' . htmlspecialchars($row['no_kbon']) . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['nama_supp']) . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['tgl_kbon'], ENT_QUOTES) . '">' . appTgl($row['tgl_kbon']) . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['tgl_tempo'], ENT_QUOTES) . '">' . appTgl($row['tgl_tempo']) . '</td>'
                   . '<td style="text-align:right;" value="' . (string) $row['term'] . '">' . (($row['term'] === null) ? '-' : (string) $row['term']) . '</td>'
                   . '<td>' . htmlspecialchars($relasi !== '' ? $relasi : '-') . '</td>'
                   . '<td>' . htmlspecialchars($item !== '' ? $item : '-') . '</td>'
                   . '<td>' . htmlspecialchars($flex !== '' ? $flex : '-') . '</td>'
                   . '<td>' . htmlspecialchars($curr) . '</td>'
                   . '<td style="text-align:right;" value="' . $base . '">' . number_format($base, 2) . '</td>'
                   . '<td style="text-align:right;" value="' . $tax . '">' . number_format($tax, 2) . '</td>'
                   . '<td style="text-align:right;" value="' . $total . '">' . number_format($total, 2) . '</td>'
                   . '<td style="text-align:right;" value="' . $nilai . '">' . number_format($nilai, 2) . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['row_note'] !== '' ? $row['row_note'] : '-') . '</td>'
                   . '<td>' . (strtoupper((string) $row['row_status']) === 'Y' ? 'POST' : 'CANCEL') . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['created_by']) . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['created_date'], ENT_QUOTES) . '">'
                   . (!empty($row['created_date']) ? date('d M Y H:i:s', strtotime($row['created_date'])) : '-') . '</td>'
                   . '<td>' . htmlspecialchars((string) $row['cancel_by'] !== '' ? $row['cancel_by'] : '-') . '</td>'
                   . '<td value="' . htmlspecialchars((string) $row['cancel_date'], ENT_QUOTES) . '">'
                   . (!empty($row['cancel_date']) ? date('d M Y H:i:s', strtotime($row['cancel_date'])) : '-') . '</td>'
                   . '</tr>';
            }
        }

        if ($no === 1) {
            echo '<tr><td colspan="25" style="text-align:center;">No payment plan found for this filter.</td></tr>';
        }
        ?>
    </table>

    <?php if ($totalPerMata) { ?>
    <table style="width:auto;font-size:11px;" border="1">
        <tr>
            <th style="text-align: center;">Currency</th>
            <th style="text-align: center;">Total Planned Amount</th>
        </tr>
        <?php foreach ($totalPerMata as $mata => $jml) { ?>
        <tr>
            <td><?php echo htmlspecialchars($mata !== '' ? $mata : '-'); ?></td>
            <td style="text-align:right;" value="<?php echo $jml; ?>"><?php echo number_format($jml, 2); ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>

</body>
</html>
