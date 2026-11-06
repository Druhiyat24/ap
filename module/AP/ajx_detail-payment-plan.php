<?php
/* ============================================================================
   Update Payment Plan - isi satu dokumen. Pasangannya list-payment-plan.php
   (tombol View).

   Nilai voucher diambil dari dokumennya (amount & curr saat dijadwalkan),
   bukan dibaca ulang dari kontrabon_h - supaya rincian dokumen lama tetap
   memperlihatkan angka yang dulu dilihat pembuatnya. Nama supplier dan
   tanggal vouchernya memang dibaca dari kontrabon_h karena itu keterangan,
   bukan nilai.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

$doc = trim(isset($_POST['doc_number']) ? $_POST['doc_number'] : '');
if ($doc === '') {
    echo json_encode(array('data' => array()));
    exit;
}
$doc_esc = mysqli_real_escape_string($conn1, $doc);

$sql = "SELECT d.no_kbon, d.plan_date, d.curr, d.amount, d.keterangan, d.status,
               k.nama_supp, k.tgl_kbon, k.tgl_tempo
        FROM ap_payment_plan_det d
        LEFT JOIN kontrabon_h k ON k.no_kbon = d.no_kbon
        WHERE d.doc_number = '$doc_esc'
        ORDER BY k.tgl_tempo ASC, d.no_kbon ASC";

$res = mysqli_query($conn1, $sql);
if ($res === false) {
    echo json_encode(array('data' => array(), 'error' => mysqli_error($conn1)));
    exit;
}

function appTgl($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d M Y', strtotime($v)) : '-';
}

$data = array();
while ($row = mysqli_fetch_assoc($res)) {
    $aktif = (strtoupper((string) $row['status']) === 'Y');
    $data[] = array(
        'no_kbon'    => htmlspecialchars($row['no_kbon'], ENT_QUOTES),
        'nama_supp'  => htmlspecialchars((string) $row['nama_supp'], ENT_QUOTES),
        'tgl_kbon'   => appTgl($row['tgl_kbon']),
        'tgl_tempo'  => appTgl($row['tgl_tempo']),
        'plan_date'  => appTgl($row['plan_date']),
        'curr'       => htmlspecialchars((string) $row['curr'], ENT_QUOTES),
        'amount'     => number_format((float) $row['amount'], 2, '.', ','),
        'keterangan' => htmlspecialchars(((string) $row['keterangan'] !== '') ? $row['keterangan'] : '-', ENT_QUOTES),
        'status'     => '<span class="ftl-st ' . ($aktif ? 'is-approved' : 'is-cancel') . '">'
                        . ($aktif ? 'POST' : 'CANCEL') . '</span>',
    );
}

echo json_encode(array('data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
?>
