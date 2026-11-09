<?php
/* ============================================================================
   AP HISTORY PROJECTION - rincian satu dokumen potret.

   HANYA MEMBACA. Seluruh angkanya diambil dari ap_projection_history_det apa
   adanya - TIDAK dihitung ulang, TIDAK membaca kurs atau rencana bayar hari
   ini. Itu memang gunanya potret: kurs, rencana PPU, dan master supplier
   semuanya bisa berubah besok, sedangkan dokumen ini harus tetap
   memperlihatkan angka yang dilihat pemakai waktu menekan Save.

   KISI TANGGALNYA DIBANGUN ULANG, bukan disimpan. Tabel rincian menyimpan
   plan_date + outstanding per baris; kolom tanggalnya dibentuk di sini dari
   periode dokumennya, satu kolom per hari - sama persis dgn layar
   laporannya. Menyimpan kolom tanggal satu per satu berarti tabel yang
   lebarnya ikut berubah tiap periode.
   ============================================================================ */

include '../../conn/conn.php';

/* ---------------------------------------------------------------------------
   BISA DIPAKAI SEBAGAI PUSTAKA, persis seperti ajx_ap_projection_report.php.
   ekspor_history_projection.php menyetel HPJ_PUSTAKA dan $_POST['doc_number'],
   meng-include berkas ini, lalu memakai keluarannya apa adanya - sehingga isi
   Excel dijamin sama dgn yang tampil di layar.

   Dua hal yang WAJIB digerbangi dalam keadaan itu:
     - header() JSON, karena pemanggilnya sudah mengirim header .xls sendiri
       dan header yang belakangan menang;
     - exit, karena exit di dalam include mematikan SELURUH skrip pemanggil,
       sehingga kesalahan apa pun menghasilkan berkas Excel kosong tanpa satu
       pun pesan. Diganti return, yang hanya keluar dari include ini.
   --------------------------------------------------------------------------- */
if (!defined('HPJ_PUSTAKA')) { header('Content-Type: application/json'); }

$doc = isset($_POST['doc_number']) ? trim((string) $_POST['doc_number']) : '';
if ($doc === '') {
    echo json_encode(array('error' => 'No document number was sent.'));
    if (defined('HPJ_PUSTAKA')) { return; }
    exit;
}

$ada = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_projection_history_h'");
if (!$ada || mysqli_num_rows($ada) === 0) {
    echo json_encode(array('error' => 'The history table does not exist yet. '
        . 'Run db_migrate_LIVE_20261106_GABUNGAN.sql first.'));
    if (defined('HPJ_PUSTAKA')) { return; }
    exit;
}

$eDoc = mysqli_real_escape_string($conn2, $doc);

$qh = mysqli_query($conn2, "SELECT * FROM ap_projection_history_h WHERE doc_number = '$eDoc'");
if (!$qh || mysqli_num_rows($qh) === 0) {
    echo json_encode(array('error' => 'Document ' . htmlspecialchars($doc, ENT_QUOTES) . ' was not found.'));
    if (defined('HPJ_PUSTAKA')) { return; }
    exit;
}
$h = mysqli_fetch_assoc($qh);

$qd = mysqli_query($conn2, "SELECT * FROM ap_projection_history_det
                            WHERE doc_number = '$eDoc' ORDER BY no_urut, id");
if ($qd === false) {
    echo json_encode(array('error' => mysqli_error($conn2)));
    if (defined('HPJ_PUSTAKA')) { return; }
    exit;
}

function hpdAngka($v) { return number_format((float) $v, 2, '.', ','); }
/* '01 Oct 2026' - sebentuk dgn apprTampilTgl() di laporan proyeksi dan
   tglRapi() di laporan realisasi (permintaan pemakai 9 Nov 2026). Halaman
   ini membuka POTRET; bentuk tampilan boleh berubah karena yang tersimpan
   di basis data tetap 'Y-m-d'. */
function hpdTgl($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d M Y', strtotime($v)) : '-';
}
function hpdLabel($v) { return date('d M Y', strtotime($v)); }
function hpdAman($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }

/* ---- kerangka kolom tanggal, satu per hari ---- */
$urut  = array();
$kolom = array();
$jalan = $h['periode_dari'];
$batas = $h['periode_sampai'];
$pagar = 0;
while ($jalan <= $batas && $pagar < 800) {   /* pagar: dokumen rusak tidak boleh bikin loop abadi */
    $urut[] = $jalan;
    $kolom[$jalan] = hpdLabel($jalan);
    $jalan = date('Y-m-d', strtotime($jalan . ' +1 day'));
    $pagar++;
}

$jumlah = array_fill_keys($urut, 0.0);
$data   = array();
$no     = 0;
$total  = 0.0;
/* Baris yang plan_date-nya di LUAR periode dokumennya tidak punya kolom
   tempat berdiri. Itu seharusnya mustahil (laporannya menyaring per periode),
   tapi kalau sampai terjadi harus KELIHATAN, bukan diam-diam hilang dari
   penjumlahan. */
$diLuar = array();

while ($r = mysqli_fetch_assoc($qd)) {
    $no++;
    $sisa = (float) $r['outstanding'];
    $total += $sisa;

    $k = (!empty($r['plan_date']) && $r['plan_date'] !== '0000-00-00') ? $r['plan_date'] : '';
    if ($k !== '' && isset($jumlah[$k])) {
        $jumlah[$k] += $sisa;
    } elseif ($k !== '') {
        $diLuar[] = $r['no_kbon'] . ' (' . hpdTgl($k) . ')';
    }

    $b = array(
        'no'             => $no,
        'supplier'       => hpdAman($r['supplier']),
        'no_kbon'        => hpdAman($r['no_kbon']),
        'tgl_kbon'       => hpdTgl($r['tgl_kbon']),
        'term'           => hpdAman($r['term']),
        'duedate'        => hpdTgl($r['duedate']),
        'tgl_proyeksi'   => hpdTgl($r['plan_date']),
        'basis'          => hpdAman($r['basis']),
        'relasi'         => hpdAman($r['relasi']),
        'item_type'      => hpdAman($r['item_type']),
        'flexibility'    => hpdAman($r['flexibility']),
        'curr'           => hpdAman($r['curr']),
        'invoice_amount' => hpdAngka($r['invoice_amount']),
        'rate'           => hpdAngka($r['rate']),
        'tax_base'       => hpdAngka($r['tax_base']),
        'vat'            => hpdAngka($r['vat']),
        'total_invoice'  => hpdAngka($r['total_invoice']),
        'pph'            => hpdAngka($r['pph']),
        'payment_amount' => hpdAngka($r['payment_amount']),
        'outstanding'    => hpdAngka($sisa),
    );
    foreach ($urut as $kk) {
        $b['d_' . $kk] = ($kk === $k) ? hpdAngka($sisa) : '';
    }
    $data[] = $b;
}

$kolomKirim = array();
foreach ($urut as $k) {
    $kolomKirim[] = array('key' => 'd_' . $k, 'label' => $kolom[$k],
                          'total' => hpdAngka($jumlah[$k]));
}

/* Kurs disimpan sbg JSON waktu potretnya dibuat - ditampilkan apa adanya
   supaya angka rupiahnya bisa ditelusuri bertahun-tahun kemudian. */
$kurs = array();
if (!empty($h['kurs'])) {
    $x = json_decode($h['kurs'], true);
    if (is_array($x)) { $kurs = $x; }
}

echo json_encode(array(
    'data'  => $data,
    'kolom' => $kolomKirim,
    'info'  => array(
        'doc_number'  => hpdAman($h['doc_number']),
        'periode'     => hpdLabel($h['periode_dari']) . ' s/d ' . hpdLabel($h['periode_sampai']),
        'type'        => hpdAman(ucfirst((string) $h['type'])),
        'supplier'    => hpdAman($h['nama_supp'] === '' ? 'ALL' : $h['nama_supp']),
        'per_tanggal' => (!empty($h['per_tanggal']) && $h['per_tanggal'] !== '0000-00-00')
                         ? hpdLabel($h['per_tanggal']) : '-',
        'created_by'  => hpdAman($h['created_by']),
        'created_at'  => !empty($h['created_at']) ? date('d M Y H:i', strtotime($h['created_at'])) : '-',
        'baris'       => count($data),
        /* Dua total: yang DISIMPAN di kepala saat potret dibuat, dan yang
           DIHITUNG ULANG dari rinciannya sekarang. Kalau berbeda, ada
           rincian yang hilang - layarnya wajib memperingatkan. */
        'total'          => hpdAngka($total),
        'total_tersimpan'=> hpdAngka($h['total_idr']),
        'utuh'           => (abs($total - (float) $h['total_idr']) <= 0.05),
        'baris_tersimpan'=> (int) $h['jml_baris'],
        'di_luar_periode'=> $diLuar,
        'kurs'           => $kurs,
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
