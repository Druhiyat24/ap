<?php
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

// Query PV eligible ini diambil PERSIS dari create-bankout.php (branch
// ref_num == 'Payment Voucher') supaya konsisten dengan alur create - PV yang
// sudah lunas (outstanding = 0) tidak ikut tampil.
$nama_supp  = $_POST['nama_supp'] ?? '';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d', strtotime('-1 year'));
$end_date   = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');
$exclude    = isset($_POST['exclude']) ? json_decode($_POST['exclude'], true) : [];
$exclude    = is_array($exclude) ? $exclude : [];

if ($nama_supp === '') {
    echo json_encode([]);
    exit;
}

$nama_supp_esc = mysqli_real_escape_string($conn2, $nama_supp);

$sql = mysqli_query($conn2, "select a.nama_supp, a.no_pv, a.pv_date, a.due_date, a.curr,
        (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal,
        (a.ppn - COALESCE(b.ppn_pv,0)) ppn,
        (a.pph - COALESCE(b.pph_pv,0)) pph,
        (a.total - COALESCE(b.total_pv,0)) total,
        a.status, a.frm_akun, a.bank_name, a.b_code
    from (
        select a.nama_supp, a.no_pv, a.pv_date, max(b.due_date) as due_date, a.curr,
            a.subtotal, a.ppn, a.pph, a.total, a.status, a.frm_akun,
            if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code
        from tbl_pv_h a
        inner join tbl_pv b on b.no_pv = a.no_pv
        left join b_masterbank c on c.bank_account = a.frm_akun
        where a.nama_supp = '$nama_supp_esc'
          and a.pv_date BETWEEN '$start_date' and '$end_date'
          and a.status = 'Approved'
          and a.outstanding != '0'
        group by a.no_pv
    ) a
    left join (
        select no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv
        from b_bankout_det a
        inner join b_bankout_h b on b.no_bankout = a.no_bankout
        where b.status != 'Cancel' and no_reff like '%PV%'
        GROUP BY no_reff
    ) b on a.no_pv = b.no_reff
    ORDER BY a.no_pv ASC
");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    if (in_array($row['no_pv'], $exclude, true)) {
        continue;
    }

    $total = (float)$row['total'];
    $curr  = $row['curr'];
    $pv_date = $row['pv_date'];

    $rate = 1;
    if ($curr !== 'IDR') {
        $sqlz = mysqli_query($conn1, "select ROUND(rate,2) as rate FROM masterrate where tanggal = '" . mysqli_real_escape_string($conn1, $pv_date) . "' and v_codecurr = 'PAJAK' and curr = '" . mysqli_real_escape_string($conn1, $curr) . "'");
        $rowz = mysqli_fetch_array($sqlz);
        $rate = !empty($rowz['rate']) ? $rowz['rate'] : 1;
    }

    if ($curr === 'IDR') {
        $total_idr = $total;
    } elseif ($curr === 'CNY') {
        $total_idr = $total * 2234.01;
    } elseif ($curr === 'EUR') {
        $total_idr = $total * 19023.82;
    } else {
        $total_idr = $total * $rate;
    }

    $data[] = [
        'no_pv'     => $row['no_pv'],
        'pv_date'   => $row['pv_date'],
        'due_date'  => $row['due_date'],
        'dpp'       => (float)$row['subtotal'],
        'ppn'       => (float)$row['ppn'],
        'pph'       => (float)$row['pph'],
        'total'     => $total,
        'total_idr' => $total_idr,
        'curr'      => $curr,
        'rates'     => $rate,
    ];
}

echo json_encode($data);
