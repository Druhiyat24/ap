<?php
/* ============================================================================
   Update Payment Plan - isi daftar dokumen. Pasangannya list-payment-plan.php.

   Saringan tanggalnya memakai TANGGAL RENCANA BAYAR (plan_date), bukan
   tanggal dokumen dibuat: yang dicari tim AP biasanya "apa saja yang
   dijadwalkan bulan ini", bukan "dokumen apa yang saya ketik bulan ini".

   TOTAL AMOUNT DIPECAH PER MATA UANG. Satu dokumen boleh memuat voucher IDR
   dan USD sekaligus; menjumlahkannya jadi satu angka akan menghasilkan
   bilangan yang tidak berarti apa pun. Jadi yang ditampilkan "IDR 1.234,00"
   (dan barisnya bertambah kalau mata uangnya lebih dari satu), bukan satu
   jumlah gabungan.

   Jumlah voucher & totalnya diambil lewat SATU query pengelompokan untuk
   seluruh dokumen yang tampil, bukan subquery berkorelasi per baris.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

function appTanggal($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));   /* dd-mm-yyyy dibaca sbg d-m-Y */
    return $t ? date('Y-m-d', $t) : '';
}

$start_date = appTanggal(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$end_date   = appTanggal(isset($_POST['end_date']) ? $_POST['end_date'] : '');
$doc_num    = trim(isset($_POST['doc_num']) ? (string) $_POST['doc_num'] : '');
$status     = trim(isset($_POST['status']) ? (string) $_POST['status'] : 'ALL');

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

$sql = "SELECT h.id, h.doc_number, h.plan_date, h.keterangan, h.status,
               h.created_by, h.created_date, h.cancel_by, h.cancel_date
        FROM ap_payment_plan_h h
        WHERE " . implode(' AND ', $where) . "
        ORDER BY h.id DESC";

$res = mysqli_query($conn1, $sql);
if ($res === false) {
    echo json_encode(array('data' => array(), 'error' => mysqli_error($conn1)));
    exit;
}

$baris = array();
$nomor = array();
while ($row = mysqli_fetch_assoc($res)) {
    $baris[] = $row;
    $nomor[] = "'" . mysqli_real_escape_string($conn1, $row['doc_number']) . "'";
}

/* Jumlah voucher & total per mata uang untuk SELURUH dokumen yang tampil,
   satu query. Isi dokumen yang dibatalkan ikut dihitung - yang berubah hanya
   statusnya, dan angka yang dulu dijadwalkan tetap perlu terbaca. */
$ringkas = array();
if ($nomor) {
    $qr = mysqli_query($conn1, "SELECT doc_number, curr, COUNT(*) AS n, SUM(amount) AS jml
            FROM ap_payment_plan_det
            WHERE doc_number IN (" . implode(',', $nomor) . ")
            GROUP BY doc_number, curr
            ORDER BY doc_number, curr");
    if ($qr === false) {
        echo json_encode(array('data' => array(), 'error' => mysqli_error($conn1)));
        exit;
    }
    while ($r = mysqli_fetch_assoc($qr)) {
        $d = $r['doc_number'];
        if (!isset($ringkas[$d])) { $ringkas[$d] = array('n' => 0, 'mata' => array()); }
        $ringkas[$d]['n'] += (int) $r['n'];
        $ringkas[$d]['mata'][] = htmlspecialchars((string) $r['curr'], ENT_QUOTES)
                               . ' ' . number_format((float) $r['jml'], 2, '.', ',');
    }
}

$data = array();
foreach ($baris as $row) {
    $doc   = htmlspecialchars($row['doc_number'], ENT_QUOTES);
    $batal = (strtoupper((string) $row['status']) === 'CANCEL');

    /* Pil status memakai kosakata .ftl- (app-ftr-list.css), sama dgn daftar
       FTR, Petty Cash Out & Update BPB. Dipetakan tegas, bukan lewat
       strtolower(), supaya status bernama dua kata tidak menghasilkan nama
       kelas rusak. */
    $kelas = $batal ? 'is-cancel' : 'is-approved';
    $pil = '<span class="ftl-st ' . $kelas . '">'
         . htmlspecialchars((string) $row['status'], ENT_QUOTES) . '</span>';

    $pembuat = htmlspecialchars((string) $row['created_by'], ENT_QUOTES);
    if (!empty($row['created_date'])) {
        $pembuat .= '<br><small class="text-muted">' . date('d-M-Y H:i', strtotime($row['created_date'])) . '</small>';
    }
    if ($batal && !empty($row['cancel_by'])) {
        $pembuat .= '<br><small class="text-muted">cancelled by '
                  . htmlspecialchars((string) $row['cancel_by'], ENT_QUOTES)
                  . (!empty($row['cancel_date'])
                     ? ' (' . date('d-M-Y H:i', strtotime($row['cancel_date'])) . ')' : '')
                  . '</small>';
    }

    $aksi = '<div class="ftl-act">'
          . '<button type="button" class="ftl-mini is-info btn-view-doc" title="Show the vouchers in this document" data-doc="' . $doc . '">'
          . '<i class="fa fa-eye" aria-hidden="true"></i> View</button>';
    if (!$batal) {
        $aksi .= '<button type="button" class="ftl-mini is-cancel btn-cancel-doc" title="Cancel this document" data-doc="' . $doc . '">'
               . '<i class="fa fa-trash" aria-hidden="true"></i> Cancel</button>';
    }
    $aksi .= '</div>';

    $r = isset($ringkas[$row['doc_number']]) ? $ringkas[$row['doc_number']] : array('n' => 0, 'mata' => array());

    $data[] = array(
        'id'          => $row['id'],
        'doc_number'  => $doc,
        'plan_date'   => (!empty($row['plan_date']) && $row['plan_date'] !== '0000-00-00')
                         ? date('d-m-Y', strtotime($row['plan_date'])) : '-',
        'jml'         => (string) $r['n'],
        'total'       => $r['mata'] ? implode('<br>', $r['mata']) : '-',
        'keterangan'  => htmlspecialchars(((string) $row['keterangan'] !== '') ? $row['keterangan'] : '-', ENT_QUOTES),
        'status'      => $pil,
        'user_create' => $pembuat,
        'action'      => $aksi,
    );
}

echo json_encode(array('data' => $data), JSON_INVALID_UTF8_SUBSTITUTE);
?>
