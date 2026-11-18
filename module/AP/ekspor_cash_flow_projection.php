<?php
/* ============================================================================
   CASH FLOW PROJECTION REPORT - EKSPOR EXCEL.
   Dipanggil tombol Excel di cash_flow_projection.php dgn saringan yang SAMA
   PERSIS seperti yang sedang tampil di layar.

   ANGKANYA TIDAK DIHITUNG ULANG DI SINI. Berkas ini menyetel $_POST lalu
   meng-include ajx_cash_flow_projection.php dalam mode pustaka (CFP_PUSTAKA)
   dan memakai keluarannya apa adanya - jadi isi Excel mustahil berbeda dari
   layar. Pola & alasannya sama dgn ekspor_ap_projection_report.php.

   SUSUNAN KOLOMNYA MENIRU LEMBAR ASLINYA (Cash Flow Daily):
     A,B,C = tiga tingkat keterangan (blok / kelompok+nomor / nama kategori)
     D     = TOTAL
     E..   = satu kolom per tanggal
   Blok pinjaman ditaruh di bawah, dipisah satu baris kosong dan diawali
   baris DESCRIPTIONS lagi - persis seperti di berkas aslinya.

   Bentuknya tabel HTML berkepala vnd-ms-excel, bukan PhpSpreadsheet - sama
   dgn ekspor AP lain dan tidak menambah kebergantungan. Sel angka memakai
   pola <td x:num="MENTAH">TERFORMAT</td> supaya Excel membacanya sebagai
   ANGKA, bukan teks.

   header() DIPANGGIL PALING AWAL, sebelum ada keluaran apa pun.
   ============================================================================ */

header('Content-type: application/vnd-ms-excel');
$namaBerkas = 'Cash Flow Projection ' . ucfirst(strtolower(
    isset($_REQUEST['type']) && $_REQUEST['type'] !== '' ? $_REQUEST['type'] : 'daily'));
header('Content-Disposition: attachment; filename=' . $namaBerkas . '.xls');

define('CFP_PUSTAKA', 1);

$_POST['start_date'] = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$_POST['end_date']   = isset($_REQUEST['end_date'])   ? $_REQUEST['end_date']   : '';
$_POST['pc']         = isset($_REQUEST['pc'])         ? $_REQUEST['pc']         : 'ALL';
$_POST['bank']       = isset($_REQUEST['bank'])       ? $_REQUEST['bank']       : 'ALL';
$_POST['type']       = isset($_REQUEST['type'])       ? $_REQUEST['type']       : 'daily';
$_POST['rate']       = isset($_REQUEST['rate'])       ? $_REQUEST['rate']       : '0';

ob_start();
include __DIR__ . '/ajx_cash_flow_projection.php';
$jawab = json_decode(trim((string) ob_get_clean()), true);

$salah  = (is_array($jawab) && isset($jawab['error'])) ? $jawab['error'] : '';
$tgl    = (is_array($jawab) && isset($jawab['tgl']))      ? $jawab['tgl']      : array();
$baris  = (is_array($jawab) && isset($jawab['baris']))    ? $jawab['baris']    : array();
$pinjam = (is_array($jawab) && isset($jawab['pinjaman'])) ? $jawab['pinjaman'] : array();
$info   = (is_array($jawab) && isset($jawab['info']))     ? $jawab['info']     : array();

function cfxAman($t)
{
    return htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8');
}

/* Sel angka. value mentah supaya Excel menghitungnya, tampilan dibulatkan
   ke rupiah penuh seperti di layar. Sel kosong dibiarkan benar-benar kosong
   (baris judul blok), bukan diisi 0. */
function cfxAngka($v)
{
    if ($v === null || $v === '') { return '<td></td>'; }
    /* floor(x + 0.5), bukan round(): PHP membulatkan setengah MENJAUHI nol,
       JavaScript (layar) membulatkan setengah KE ATAS. Untuk nilai pas ,5
       keduanya beda 1 rupiah - lihat SALDO AWAL PINJAMAN BANK. */
    $n = floor((float) $v + 0.5);

    /* Gaya akuntansi: angka minus DALAM KURUNG, sama dgn layar.

       Nilainya TETAP dikirim sebagai angka negatif lewat x:num, jadi
       penjumlahan & grafik di Excel tetap benar; yang diatur cuma cara
       Excel menampilkannya lewat mso-number-format. Teks di dalam <td>
       ikut dibuat berkurung supaya pembaca yang membuka berkas ini di
       LibreOffice atau di penampil web tetap melihat bentuk yang sama.

       Kalau Excel sampai mengabaikan mso-number-format, akibat terburuknya
       cuma tampil -1,234 seperti dulu - angkanya tidak pernah rusak. */
    $t = $n < 0 ? '(' . number_format(abs($n), 0, '.', ',') . ')'
                : number_format($n, 0, '.', ',');

    return '<td align="right" x:num="' . $n . '" style="mso-number-format:'
         . '&quot;\#\,\#\#0\;\(\#\,\#\#0\)&quot;">' . $t . '</td>';
}

/* Tiga kolom keterangan - tingkatnya mengikuti jenis barisnya, sama dgn
   lembar aslinya. */
function cfxKeterangan($b)
{
    $no  = cfxAman($b['no']);
    $lbl = cfxAman($b['label']);
    switch ($b['tipe']) {
        case 'grup':  return '<td>' . $no . '</td><td colspan="2">' . $lbl . '</td>';
        case 'item':  return '<td></td><td>' . $no . '</td><td>' . $lbl . '</td>';
        case 'tot':
        case 'net':   return '<td></td><td colspan="2">' . $lbl . '</td>';
        default:      return '<td colspan="3">' . $lbl . '</td>';
    }
}

function cfxGaya($tipe)
{
    switch ($tipe) {
        case 'sal':  return ' style="background:#DCE6F5;font-weight:bold;"';
        case 'blok': return ' style="background:#1F2937;color:#FFFFFF;font-weight:bold;"';
        case 'grup': return ' style="background:#EDF1F7;font-weight:bold;"';
        case 'tot':
        case 'tot2': return ' style="background:#F4F6FA;font-weight:bold;border-top:1px solid #9AA6B8;"';
        case 'net':  return ' style="font-weight:bold;"';
        case 'pinj': return ' style="background:#FDF0E2;font-weight:bold;"';
        case 'sub':  return ' style="background:#FEF8F1;"';
    }
    return '';
}

/* Dua tata letak, kepala kolomnya saja yang beda: Daily berkolom TANGGAL,
   Weekly/Monthly berkolom REKENING. Isi barisnya identik - urutan nilai di
   $b['v'] sudah mengikuti kolom yang dipilih mesin. */
function cfxBaris($daftar, $jmlKolom)
{
    $h = '';
    foreach ($daftar as $b) {
        $gaya = cfxGaya($b['tipe']);
        $h .= '<tr' . $gaya . '>' . cfxKeterangan($b);
        $h .= cfxAngka($b['tot']);
        for ($i = 0; $i < $jmlKolom; $i++) {
            $h .= cfxAngka(isset($b['v'][$i]) ? $b['v'][$i] : null);
        }
        $h .= '</tr>';
    }
    return $h;
}

function cfxKepala($judulKolom)
{
    $h = '<tr style="background:#1E3A8A;color:#FFFFFF;font-weight:bold;">'
       . '<td colspan="3">DESCRIPTIONS</td><td align="right">TOTAL</td>';
    foreach ($judulKolom as $t) { $h .= '<td align="right">' . cfxAman($t) . '</td>'; }
    return $h . '</tr>';
}

$modeAkun  = (is_array($jawab) && isset($jawab['mode']) && $jawab['mode'] === 'akun');
$kolomInfo = (is_array($jawab) && isset($jawab['kolom'])) ? $jawab['kolom'] : array();
$judulKolom = array();
if ($modeAkun) {
    foreach ($kolomInfo as $k) { $judulKolom[] = $k['label']; }
} else {
    foreach ($tgl as $t) { $judulKolom[] = date('d-M-Y', strtotime($t)); }
}
$jmlKolom   = count($judulKolom);
$kolomTotal = 4 + $jmlKolom;
?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="UTF-8"></head>
<body>
<?php if ($salah !== '') { ?>
<table border="0"><tr><td><?php echo cfxAman($salah); ?></td></tr></table>
<?php } else { ?>
<table border="0" cellspacing="0" cellpadding="2" style="font-family:Calibri;font-size:11px;">
    <?php /* Nama perusahaan sengaja TIDAK dicetak: berkas ini dipakai untuk
             Garment (NAG) maupun Knitting (NAK), jadi nama perusahaan yang
             dipatok akan salah separuh waktu. Lingkupnya ikut di judul dan
             tetap ada di baris PROFIT CENTER di bawah. */ ?>
    <tr style="font-weight:bold;"><td colspan="<?php echo $kolomTotal; ?>">CASH FLOW PROJECTION<?php echo cfxAman(isset($info['pc_label']) && $info['pc_label'] !== '' ? ' - ' . strtoupper($info['pc_label']) : ''); ?></td></tr>
    <tr style="font-weight:bold;"><td colspan="<?php echo $kolomTotal; ?>">FOR PERIOD <?php echo strtoupper(cfxAman(isset($info['periode']) ? $info['periode'] : '')); ?></td></tr>
    <tr><td colspan="2">RATE</td><td colspan="<?php echo $kolomTotal - 2; ?>"><?php echo number_format(isset($info['rate']) ? $info['rate'] : 0, 0, '.', ','); ?></td></tr>
    <tr><td colspan="2">PROFIT CENTER</td><td colspan="<?php echo $kolomTotal - 2; ?>"><?php echo cfxAman(isset($info['pc']) ? $info['pc'] : ''); ?></td></tr>
    <tr><td colspan="2">BANK</td><td colspan="<?php echo $kolomTotal - 2; ?>"><?php echo cfxAman(isset($info['bank']) ? $info['bank'] : ''); ?></td></tr>
    <tr><td colspan="<?php echo $kolomTotal; ?>"></td></tr>

    <?php echo cfxKepala($judulKolom); ?>
    <?php echo cfxBaris($baris, $jmlKolom); ?>

    <?php if ($pinjam) { ?>
    <tr><td colspan="<?php echo $kolomTotal; ?>"></td></tr>
    <?php echo cfxKepala($judulKolom); ?>
    <?php echo cfxBaris($pinjam, $jmlKolom); ?>
    <?php } ?>

    <?php /* Catatan sumber & peringatan SENGAJA tidak ikut dicetak
             (permintaan pemakai 16 Nov 2026) - berkas ini beredar lepas dari
             layarnya, dan isinya harus sama dgn yang dilihat pemakai. */ ?>
</table>
<?php } ?>
</body>
</html>
