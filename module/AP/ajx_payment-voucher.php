<?php
// Endpoint AJAX untuk payment-voucher.php - dipakai supaya Search/filter tidak
// reload seluruh halaman lagi (dulu form filter submit biasa, ganti filter =
// reload penuh). Query & logic $where di sini SAMA PERSIS dengan yang dulu
// ada di payment-voucher.php sendiri, cuma dipindah ke sini supaya bisa
// dipanggil lewat AJAX dan hasilnya di-return sebagai JSON.
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$nama_supp = $_POST['nama_supp'] ?? 'ALL';
$meth = $_POST['meth'] ?? 'ALL';
$ost = $_POST['ost'] ?? 'ALL';
$status = $_POST['status'] ?? 'ALL';
$start_date = date("Y-m-d", strtotime($_POST['start_date'] ?? ''));
$end_date = date("Y-m-d", strtotime($_POST['end_date'] ?? ''));
$date_now = date("Y-m-d");

if ($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and !empty($end_date)) {
    $where = "where a.pv_date = '$date_now'";
} elseif ($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $where = "where a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL') {
    $where = "where a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL') {
    $where = "where a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL') {
    $where = "where a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL') {
    $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL') {
    $where = "where a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL') {
    $where = "where a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL') {
    $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} elseif ($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status != 'ALL') {
    $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
} else {
    $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
}

$sql = mysqli_query($conn1, "select a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr,a.total,a.outstanding,a.status,a.no_cek,a.cek_date, a.nama_supp as pay_to,a.pay_date,a.pay_meth,a.frm_akun, a.to_akun, a.for_pay, a.pv_form_type from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv $where group by a.no_pv");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $pay_meth = $row['pay_meth'];
    $rowStatus = $row['status'];

    $cekdate = $row['cek_date'];
    $cek_date = (empty($cekdate) || $cekdate == '1970-01-01') ? '' : date("d-m-Y", strtotime($cekdate));

    $duedate = $row['due_date'];
    $due_date = (empty($duedate) || $duedate == '1970-01-01') ? '-' : date("d-m-Y", strtotime($duedate));

    // Edit tampilan/alur beda antara Regular dan EXIM (field-nya beda), jadi
    // diarahkan ke halaman edit yang sesuai berdasarkan pv_form_type.
    $editUrl = ($row['pv_form_type'] === 'EXIM') ? 'edit-paymentvoucher-exim.php' : 'edit-paymentvoucher.php';

    $action = '<div class="kbon-action-buttons">';
    $action .= '<button type="button" class="btn btn-sm btn-primary btn-detail-pv" data-pv="' . htmlspecialchars($row['no_pv']) . '" title="Detail Payment Voucher"><i class="fa fa-eye" aria-hidden="true"></i></button>';

    // PV yang sudah Cancel tidak boleh di-PDF/Edit/Update lagi - cuma tombol
    // Detail (mata) yang tetap tampil, sama kaya payment-voucher-list.php.
    if (strcasecmp($rowStatus, 'Cancel') === 0) {
        // no-op, cuma tombol Detail di atas.
    } elseif ($pay_meth == 'CHEQUE/GIRO' && $rowStatus != 'Draft') {
        $action .= '<button type="button" id="btnupdate" name="btnupdate" class="btn btn-sm btn-warning" title="Update Cheque / Giro"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <a href="pdf_payvoucher.php?no_pv=' . $row['no_pv'] . '" target="_blank" class="btn btn-sm btn-success" title="PDF Payment Voucher"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
    } elseif ($pay_meth == 'CHEQUE/GIRO' && $rowStatus == 'Draft') {
        $action .= '<button type="button" id="btnupdate" name="btnupdate" class="btn btn-sm btn-warning" title="Update Cheque / Giro"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <a href="pdf_payvoucher.php?no_pv=' . $row['no_pv'] . '" target="_blank" class="btn btn-sm btn-success" title="PDF Payment Voucher"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
            <a href="' . $editUrl . '?no_pv=' . base64_encode($row['no_pv']) . '" class="btn btn-sm btn-danger" title="Edit Payment Voucher"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    } elseif ($pay_meth != 'CHEQUE/GIRO' && $rowStatus != 'Draft') {
        $action .= '<a href="pdf_payvoucher.php?no_pv=' . $row['no_pv'] . '" target="_blank" class="btn btn-sm btn-success" title="PDF Payment Voucher"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
        $querys_pv = mysqli_query($conn1, "select maintain_pv from userpassword where username = '" . mysqli_real_escape_string($conn1, $_SESSION['username'] ?? '') . "'");
        $rs_pv = mysqli_fetch_array($querys_pv);
        $maintain_pv = isset($rs_pv['maintain_pv']) ? $rs_pv['maintain_pv'] : 0;
        if ($maintain_pv == '1') {
            $action .= '<button type="button" id="btn_maintainpv" name="btn_maintainpv" class="btn btn-sm btn-warning" title="Set ke Draft"><i class="fa fa-refresh" aria-hidden="true"></i></button>';
        }
    } else {
        $action .= '<a href="pdf_payvoucher.php?no_pv=' . $row['no_pv'] . '" target="_blank" class="btn btn-sm btn-success" title="PDF Payment Voucher"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
            <a href="' . $editUrl . '?no_pv=' . base64_encode($row['no_pv']) . '" class="btn btn-sm btn-danger" title="Edit Payment Voucher"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
    }
    $action .= '</div>';

    $data[] = [
        'no_pv' => $row['no_pv'],
        'pv_date' => date("d-M-Y", strtotime($row['pv_date'])),
        'pay_to' => $row['pay_to'],
        'pay_meth' => $row['pay_meth'],
        'for_pay' => $row['for_pay'],
        'due_date' => $due_date,
        'curr' => $row['curr'],
        'total' => number_format((float) $row['total'], 2),
        'outstanding' => number_format((float) $row['outstanding'], 2),
        'status' => $rowStatus,
        'action' => $action,
        'no_cek' => $row['no_cek'],
        'cek_date' => $cek_date,
        'pay_date' => date("d-M-Y", strtotime($row['pay_date'])),
        'frm_akun' => $row['frm_akun'],
        'to_akun' => $row['to_akun'],
    ];
}

$sqlTotal = mysqli_query($conn1, "select sum(a.total) as nominal, sum(a.outstanding) as outstanding from tbl_pv_h a $where");
$rowTotal = mysqli_fetch_assoc($sqlTotal);
$total_amount = (float) ($rowTotal['nominal'] ?? 0);
$total_outstanding = (float) ($rowTotal['outstanding'] ?? 0);

echo json_encode([
    'data' => $data,
    'total_amount' => number_format($total_amount, 2),
    'total_outstanding' => number_format($total_outstanding, 2),
    'where' => $where,
    'start_date' => $start_date,
    'end_date' => $end_date,
]);
