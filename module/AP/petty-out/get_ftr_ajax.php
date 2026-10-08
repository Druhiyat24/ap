<?php
/* ============================================================================
   Daftar FTR yang bisa dibayar dari Kas Kecil (tab FTR di Petty Cash Out).

   Hanya FTR berstatus Approved DAN Payment Method = Cash. Yang BUKAN Cash
   tetap lewat PV-AP CBD/DP seperti biasa - kedua himpunan itu sengaja dibuat
   tidak beririsan, supaya satu dokumen tidak mungkin ditarik dua kali lewat
   dua jalur yang berbeda.

   Keluarannya berupa <tr> siap tempel, bentuk kolomnya SAMA dgn
   bank-out/get_pv_ajax.php supaya JavaScript tabnya bisa dipakai apa adanya.
   ============================================================================ */
include '../../../conn/conn.php';
include __DIR__ . '/../pv_data_functions.php';   // getAlreadyPaidFor()
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_awal  = !empty($_POST['tgl_awal'])  ? date('Y-m-d', strtotime($_POST['tgl_awal']))  : '';
$tgl_akhir = !empty($_POST['tgl_akhir']) ? date('Y-m-d', strtotime($_POST['tgl_akhir'])) : '';
$supplier  = isset($_POST['supplier']) ? trim((string) $_POST['supplier']) : '';

/* Dokumen yang sedang diedit. Alokasi miliknya sendiri TIDAK dihitung sbg
   "sudah dibayar" - kalau dihitung, FTR yang sudah dibayar penuh oleh dokumen
   ini akan hilang dari daftar dan nominalnya tidak bisa diturunkan lagi. */
$exclude_doc = isset($_POST['exclude_doc_num']) ? trim((string) $_POST['exclude_doc_num']) : '';
$linked_only = !empty($_POST['linked_only']);

if (!$linked_only && ($tgl_awal === '' || $tgl_akhir === '' || $supplier === '')) {
    echo '<tr><td colspan="12" class="text-center text-muted">Supplier dan rentang tanggal harus diisi.</td></tr>';
    exit;
}

$supp_esc  = mysqli_real_escape_string($conn2, $supplier);
$excl_esc  = mysqli_real_escape_string($conn2, $exclude_doc);

/* Nilai yang sudah dialokasikan dokumen yang sedang diedit untuk satu FTR.
   Dipakai sbg pengurang "sudah dibayar" supaya barisnya tampil dgn sisa
   seolah dokumen ini belum pernah membayar apa pun. */
function alokasiDokumenIni($conn2, $excl_esc, $type_ftr, $no_ftr)
{
    if ($excl_esc === '') { return 0; }
    $q = mysqli_query($conn2, "select sum(amount) n from c_petty_cashout_det
        where no_pco = '$excl_esc'
          and type_pv = '" . mysqli_real_escape_string($conn2, $type_ftr) . "'
          and no_reff = '" . mysqli_real_escape_string($conn2, $no_ftr) . "'");
    $r = $q ? mysqli_fetch_assoc($q) : null;
    return $r && $r['n'] !== null ? (float) $r['n'] : 0;
}
$awal_esc  = mysqli_real_escape_string($conn2, $tgl_awal);
$akhir_esc = mysqli_real_escape_string($conn2, $tgl_akhir);

/* Kurs: IDR selalu 1. Selain itu diambil dari ap_masterrate pada tanggal
   dokumen; kalau tanggal persisnya tidak ada, dipakai kurs terakhir SEBELUM
   tanggal itu - bukan 1, karena 1 untuk mata uang asing akan menghasilkan
   nilai IDR yang jauh meleset tanpa ada tandanya. */
function kursFtr($conn2, $curr, $tgl)
{
    static $cache = array();
    $curr = strtoupper(trim((string) $curr));
    if ($curr === '' || $curr === 'IDR') { return 1; }

    $kunci = $curr . '|' . $tgl;
    if (isset($cache[$kunci])) { return $cache[$kunci]; }

    $c = mysqli_real_escape_string($conn2, $curr);
    $t = mysqli_real_escape_string($conn2, $tgl);
    $q = mysqli_query($conn2, "select rate from ap_masterrate
        where curr = '$c' and v_codecurr = 'PAJAK' and tanggal <= '$t'
        order by tanggal desc limit 1");
    $r = $q ? mysqli_fetch_assoc($q) : null;
    $rate = ($r && (float) $r['rate'] > 0) ? (float) $r['rate'] : 0;

    $cache[$kunci] = $rate;
    return $rate;
}

/* CBD & DP digabung jadi satu daftar. Kolom "total" artinya nilai yang harus
   dibayar: untuk CBD seluruh nilai FTR, untuk DP nilai uang mukanya saja. */
$baris = array();

/* Saat mengedit, FTR yang sudah tertaut ke dokumen ini WAJIB ikut tampil -
   termasuk kalau statusnya di luar rentang tanggal pencarian, atau sudah
   terbayar penuh oleh dokumen ini sendiri. Nomor-nomornya dikumpulkan dulu
   supaya bisa dipaksa masuk ke hasil. */
$wajib_cbd = array();
$wajib_dp  = array();
if ($excl_esc !== '') {
    $qL = mysqli_query($conn2, "select no_reff, type_pv from c_petty_cashout_det where no_pco = '$excl_esc'");
    while ($rL = mysqli_fetch_assoc($qL)) {
        $n = "'" . mysqli_real_escape_string($conn2, $rL['no_reff']) . "'";
        if ($rL['type_pv'] === 'FTR-CBD') { $wajib_cbd[] = $n; }
        elseif ($rL['type_pv'] === 'FTR-DP') { $wajib_dp[] = $n; }
    }
}

/* linked_only = halaman edit baru dibuka: tampilkan HANYA yang sudah tertaut,
   tanpa menunggu user menekan Search. */
$syarat_cbd = $linked_only
    ? ($wajib_cbd ? 'no_ftr_cbd in (' . implode(',', $wajib_cbd) . ')' : '1=0')
    : "(supp = '$supp_esc' and status = 'Approved' and payment_method = 'Cash'"
      . " and tgl_ftr_cbd between '$awal_esc' and '$akhir_esc')"
      . ($wajib_cbd ? ' or no_ftr_cbd in (' . implode(',', $wajib_cbd) . ')' : '');
$syarat_dp = $linked_only
    ? ($wajib_dp ? 'no_ftr_dp in (' . implode(',', $wajib_dp) . ')' : '1=0')
    : "(supp = '$supp_esc' and status = 'Approved' and payment_method = 'Cash'"
      . " and tgl_ftr_dp between '$awal_esc' and '$akhir_esc')"
      . ($wajib_dp ? ' or no_ftr_dp in (' . implode(',', $wajib_dp) . ')' : '');

$qCbd = mysqli_query($conn2, "select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, tgl_bayar,
        SUM(subtotal + biaya_tambahan) sub, SUM(tax) tax, SUM(total + biaya_tambahan) total,
        MIN(curr) curr, MIN(item_type) item_type, MIN(profit_center) profit_center
    from ftr_cbd
    where $syarat_cbd
    group by no_ftr_cbd order by tgl_ftr_cbd asc, no_ftr_cbd asc");
while ($r = mysqli_fetch_assoc($qCbd)) { $r['jenis'] = 'CBD'; $baris[] = $r; }

$qDp = mysqli_query($conn2, "select no_ftr_dp no_ftr, tgl_ftr_dp tgl_ftr, tgl_bayar,
        SUM(total) sub, 0 tax, SUM(dp_value) total,
        MIN(curr) curr, MIN(item_type) item_type, MIN(profit_center) profit_center
    from ftr_dp
    where $syarat_dp
    group by no_ftr_dp order by tgl_ftr_dp asc, no_ftr_dp asc");
while ($r = mysqli_fetch_assoc($qDp)) { $r['jenis'] = 'DP'; $baris[] = $r; }

$ada = 0;
foreach ($baris as $b) {
    $type_ftr = 'FTR-' . $b['jenis'];

    /* Sisa yang belum dibayar. getAlreadyPaidFor() menjumlahkan dari Bank Out,
       Petty Cash Out, dan payment_ftr sekaligus - fungsi yang sama dipakai tab
       Payment Voucher, jadi perhitungan sisanya pasti seragam. */
    $milik_ini = alokasiDokumenIni($conn2, $excl_esc, $type_ftr, $b['no_ftr']);
    $sudah = getAlreadyPaidFor($conn2, $type_ftr, $b['no_ftr']) - $milik_ini;
    $sisa  = (float) $b['total'] - $sudah;

    /* Yang sudah lunas disembunyikan - KECUALI kalau dokumen yang sedang
       diedit memang mengalokasikan ke sana; barisnya harus tetap ada supaya
       nominalnya bisa diubah atau centangnya dilepas. */
    if ($sisa <= 0.009 && $milik_ini <= 0) { continue; }

    $rate = kursFtr($conn2, $b['curr'], !empty($b['tgl_bayar']) ? $b['tgl_bayar'] : $b['tgl_ftr']);

    /* Akun uang muka untuk kas kecil sudah tetap (1.49.98), jadi Item Type &
       area supplier TIDAK lagi menentukan apa pun di sini - keduanya tidak
       boleh menghalangi baris untuk dicentang. Yang tersisa cuma kurs: tanpa
       kurs, nilai IDR-nya tidak bisa dihitung. Barisnya tetap ditampilkan
       lengkap dgn alasannya, bukan hilang tanpa penjelasan. */
    $item_type = trim((string) $b['item_type']);
    $halangan  = ($rate <= 0) ? ('Kurs ' . htmlspecialchars($b['curr']) . ' belum ada di master rate') : '';

    $e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
    $tgl = function ($v) { return (!empty($v) && $v > '1970-01-01') ? date('d-M-Y', strtotime($v)) : '-'; };

    $ada++;
    $tercentang = ($milik_ini > 0);
    $nominal    = $tercentang ? (round($milik_ini, 2) + 0) : '';
    $nominal_idr= $tercentang ? (round($milik_ini * $rate, 2) + 0) : '';
    echo '<tr>'
       . '<td>' . ($halangan === ''
            ? '<input type="checkbox" class="chk_ftr"' . ($tercentang ? ' checked' : '') . '>'
            : '<span class="text-danger" title="' . $e($halangan) . '"><i class="fa fa-ban"></i></span>') . '</td>'
       . '<td class="pc_ftr" data-pcftr="' . $e(trim((string) $b['profit_center'])) . '">' . $e($b['jenis']) . '</td>'
       . '<td class="no_ftr" data-noftr="' . $e($b['no_ftr']) . '" data-typeftr="' . $e($type_ftr) . '">'
         . $e($b['no_ftr'])
         . ($halangan !== '' ? '<br><small class="text-danger" style="font-size:10px;">' . $e($halangan) . '</small>' : '')
       . '</td>'
       . '<td>' . $tgl($b['tgl_ftr']) . '</td>'
       . '<td>' . $tgl($b['tgl_bayar']) . '</td>'
       . '<td style="text-align:right">' . number_format((float) $b['sub'], 2) . '</td>'
       . '<td style="text-align:right">' . number_format((float) $b['tax'], 2) . '</td>'
       . '<td>' . ($item_type !== '' ? $e($item_type) : '-') . '</td>'
       . '<td class="total_ftr" data-total="' . $sisa . '" style="text-align:right">' . number_format($sisa, 2) . '</td>'
       . '<td class="rate_ftr" data-rateftr="' . $rate . '" data-curr="' . $e($b['curr']) . '" style="text-align:right">' . number_format($rate, 2) . '</td>'
       . '<td style="width: 170px;"><input type="text" class="form-control txt_amount_ftr" style="text-align:right" value="' . $e($nominal) . '"' . ($tercentang ? '' : ' disabled') . '></td>'
       . '<td style="width: 170px;"><input type="text" class="form-control txt_amount_ftr_idr" style="text-align:right" value="' . $e($nominal_idr) . '"' . ($tercentang ? '' : ' disabled') . '></td>'
       . '</tr>';
}

if ($ada === 0) {
    echo '<tr><td colspan="12" class="text-center text-muted">'
       . 'Tidak ada FTR Approved ber-Payment Method Cash yang masih bersisa untuk supplier &amp; rentang tanggal ini.'
       . '</td></tr>';
}
