<?php
include '../../conn/conn.php';
include 'bank_post_rule.php';
header('Content-Type: application/json');

$nama_supp  = isset($_POST['nama_supp']) ? trim($_POST['nama_supp']) : 'ALL';
$bank       = isset($_POST['bank']) ? trim($_POST['bank']) : 'ALL';
$akun       = isset($_POST['akun']) ? trim($_POST['akun']) : 'ALL';
$status     = isset($_POST['status']) ? trim($_POST['status']) : 'ALL';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d', strtotime('-1 month'));
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');

$conditions = ["DATE(date) BETWEEN '" . mysqli_real_escape_string($conn1, $start_date) . "' AND '" . mysqli_real_escape_string($conn1, $end_date) . "'"];

if ($nama_supp !== '' && $nama_supp !== 'ALL') {
    $conditions[] = "customer = '" . mysqli_real_escape_string($conn1, $nama_supp) . "'";
}
if ($bank !== '' && $bank !== 'ALL') {
    $conditions[] = "bank = '" . mysqli_real_escape_string($conn1, $bank) . "'";
}
if ($akun !== '' && $akun !== 'ALL') {
    $conditions[] = "akun = '" . mysqli_real_escape_string($conn1, $akun) . "'";
}
if ($status !== '' && $status !== 'ALL') {
    $conditions[] = "status = '" . mysqli_real_escape_string($conn1, $status) . "'";
}

$where = 'WHERE ' . implode(' AND ', $conditions);

$sql = mysqli_query($conn1, "SELECT doc_num, customer, date, ref_data, akun, bank, curr, amount, outstanding, status, deskripsi, approve_date
    FROM tbl_bankin_arcollection $where GROUP BY doc_num ORDER BY id ASC");

$data = [];
$totalAmount = 0;
$totalOutstanding = 0;

while ($row = mysqli_fetch_assoc($sql)) {
    $docNum = $row['doc_num'];
    $rowStatus = $row['status'];
    $refData = $row['ref_data'];
    $tanggal = $row['date'];

    $qClosing = mysqli_query($conn2, "SELECT status_closing FROM tbl_closing_periode WHERE '" . mysqli_real_escape_string($conn2, $tanggal) . "' BETWEEN tgl_awal AND tgl_akhir");
    $rowClosing = mysqli_fetch_array($qClosing);
    $statusClosing = $rowClosing['status_closing'] ?? 'Open';

    $totalAmount += (float) $row['amount'];
    $totalOutstanding += (float) $row['outstanding'];

    if (strcasecmp($rowStatus, 'Cancel') === 0) {
        $action = '<span class="text-danger font-weight-bold">Cancelled</span>';
    } else {
        $action = '<div class="kbon-action-buttons">';

        // Post (tanpa approval) juga boleh diedit/di-cancel selama periodenya
        // Open dan belum dialokasikan di AR - lihat bank_post_rule.php.
        $isPost = strcasecmp($rowStatus, 'Post') === 0;
        if (strcasecmp($rowStatus, 'Draft') === 0 || $isPost) {
            $blok = $isPost ? bank_dipakai_dokumen_lain($conn2, 'in', $docNum) : '';
            $dataEdit = 'data-bank="' . htmlspecialchars($docNum) . '" data-closing="' . htmlspecialchars($statusClosing) . '" data-blok="' . htmlspecialchars($blok) . '"';
            if ($refData === 'AR Collection') {
                $action .= '<button type="button" class="btn btn-sm btn-outline-warning edit-ar-collection" ' . $dataEdit . ' title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($refData === 'None') {
                $action .= '<button type="button" class="btn btn-sm btn-outline-warning edit-none" ' . $dataEdit . ' title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($refData === 'Bank Keluar') {
                $action .= '<button type="button" class="btn btn-sm btn-outline-warning edit-bk" ' . $dataEdit . ' title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            }
            // Cancel: Draft bebas seperti dulu, Post ikut aturan Edit.
            $dataCancel = $isPost ? $dataEdit : 'data-bank="' . htmlspecialchars($docNum) . '"';
            $action .= '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-bankin" ' . $dataCancel . ' title="Cancel"><i class="fa fa-ban"></i> Cancel</button>';
        }

        $action .= '<a href="pdf_bankin.php?doc_num=' . htmlspecialchars(rawurlencode($docNum)) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF"><i class="fa fa-file-pdf-o"></i> Pdf</a>';
        $action .= '</div>';
    }

    $data[] = [
        'doc_num'      => $docNum,
        'customer'     => $row['customer'],
        'date'         => !empty($row['date']) ? date('d-M-Y', strtotime($row['date'])) : '-',
        'curr'         => $row['curr'],
        'amount'       => number_format((float) $row['amount'], 2),
        'outstanding'  => number_format((float) $row['outstanding'], 2),
        'status'       => $rowStatus,
        'approve_date' => !empty($row['approve_date']) ? date('d-M-Y', strtotime($row['approve_date'])) : '-',
        'action'       => $action,
        'ref_data'     => $refData,
        'akun'         => $row['akun'],
        'bank'         => $row['bank'],
        'deskripsi'    => $row['deskripsi'],
    ];
}

echo json_encode([
    'data' => $data,
    'footer' => [
        'amount'      => number_format($totalAmount, 2),
        'outstanding' => number_format($totalOutstanding, 2),
    ],
]);
