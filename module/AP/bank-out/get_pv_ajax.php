<?php
include '../../../conn/conn.php';
include '../pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_awal  = date('Y-m-d', strtotime($_POST['tgl_awal']));
$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
$supplier  = $_POST['supplier'];
$supplier_esc = mysqli_real_escape_string($conn2, $supplier);

// Map kode_pc -> "id_pc - nama_pc" sekali saja, dipakai untuk semua type.
$pcLabels = [];
$sqlPc = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc from master_pc");
while ($rowPc = mysqli_fetch_assoc($sqlPc)) {
    $pcLabels[$rowPc['kode_pc']] = $rowPc['id_pc'] . ' - ' . $rowPc['nama_pc'];
}

// Rate fallback (DP/CBD tidak punya kolom rate sendiri) - sama seperti pola Biaya.
function getRateByCurrDate($conn2, $curr, $tgl)
{
    if (empty($curr) || empty($tgl)) {
        return 1;
    }
    $sql = mysqli_query($conn2, "select rate from ap_masterrate where v_codecurr = 'PAJAK' and curr = '" . mysqli_real_escape_string($conn2, $curr) . "' and tanggal = '" . mysqli_real_escape_string($conn2, $tgl) . "' limit 1");
    $row = mysqli_fetch_assoc($sql);
    return !empty($row['rate']) ? (float) $row['rate'] : 1;
}

// Bank perusahaan (From Account) - sama seperti pola Biaya (frm_akun -> b_masterbank).
function getBankByAccount($conn2, $bank_account)
{
    $bank = ['bank_name' => '-', 'b_code' => null];
    if (empty($bank_account) || $bank_account === '-') {
        return $bank;
    }
    $sql = mysqli_query($conn2, "select bank_name, b_code from b_masterbank where bank_account = '" . mysqli_real_escape_string($conn2, $bank_account) . "' limit 1");
    $row = mysqli_fetch_assoc($sql);
    if ($row) {
        $bank = $row;
    }
    return $bank;
}

// Sudah pernah ditarik sebelumnya - lewat Bank-Out ATAU Petty Cash Out (per
// type_pv + no_reff) - dipakai untuk mengurangi outstanding, supaya
// PV/kontrabon yang sudah dibayar sebagian (dari channel manapun) tidak bisa
// ditarik lebih dari sisanya. type_pv membedakan referensi Biaya (no_pv) dari
// referensi kontrabon-family (no_kbon), karena format no_kbon ('PV-AP/REG/...')
// juga mengandung substring 'PV' sama seperti no_pv ('PV/NAG/...') - tidak
// bisa dibedakan hanya dari isi no_reff.
function getAlreadyPaid($conn2, $type_pv)
{
    $type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);
    $paid = [];

    $sqlBank = mysqli_query($conn2, "select a.no_reff, sum(a.total) total_paid
        from b_bankout_det a
        inner join b_bankout_h b on b.no_bankout = a.no_bankout
        where b.status != 'Cancel' and a.type_pv = '$type_pv_esc'
        group by a.no_reff");
    while ($row = mysqli_fetch_assoc($sqlBank)) {
        $paid[$row['no_reff']] = (float) $row['total_paid'];
    }

    $sqlCash = mysqli_query($conn2, "select a.no_reff, sum(a.amount) total_paid
        from c_petty_cashout_det a
        inner join c_petty_cashout_h b on b.no_pco = a.no_pco
        where b.status != 'Cancel' and a.type_pv = '$type_pv_esc'
        group by a.no_reff");
    while ($row = mysqli_fetch_assoc($sqlCash)) {
        $paid[$row['no_reff']] = ($paid[$row['no_reff']] ?? 0) + (float) $row['total_paid'];
    }

    $sqlPay = mysqli_query($conn2, "select no_kbon no_reff, sum(nominal) total_paid
        from payment_ftr
        where status != 'Cancel' and type_pv = '$type_pv_esc'
        group by no_kbon");
    while ($row = mysqli_fetch_assoc($sqlPay)) {
        $paid[$row['no_reff']] = ($paid[$row['no_reff']] ?? 0) + (float) $row['total_paid'];
    }

    return $paid;
}

$rowsOut = [];

// ====================== BIAYA ======================
$sqlBiaya = mysqli_query($conn2, "WITH
    total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.nama_supp = '$supplier_esc' and a.pv_date BETWEEN '$tgl_awal' AND '$tgl_akhir' group by a.no_pv, b.profit_center),

    total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and a.type_pv = 'Biaya' GROUP BY no_reff,a.profit_center)

    select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code, IFNULL(c.rate,1) rate from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.pv_date where (a.total - COALESCE(b.total_pv,0)) > 0
");

while ($row = mysqli_fetch_assoc($sqlBiaya)) {
    $rowsOut[] = [
        'type_pv'   => 'Biaya',
        'pc'        => $row['profit_center'],
        'nama_pc'   => $row['nama_pc'],
        'no_pv'     => $row['no_pv'],
        'pv_date'   => $row['pv_date'],
        'due_date'  => $row['due_date'],
        'curr'      => $row['curr'] ?? 'IDR',
        'dpp'       => (float) $row['subtotal'],
        'ppn'       => (float) $row['ppn'],
        'pph'       => (float) $row['pph'],
        'total'     => (float) $row['total'],
        'rate'      => (float) $row['rate'],
    ];
}

// ====================== REGULAR / INSTALLMENT / DP / CBD ======================
// Hanya yang sudah SECOND APPROVED di Payment List (status_pl) yang boleh ditarik.
$filters = [
    'nama_supp'   => !empty($supplier) ? $supplier : 'ALL',
    'status'      => 'ALL',
    'filter_date' => 'tgl_kbon',
    'start_date'  => $tgl_awal,
    'end_date'    => $tgl_akhir,
];

$kontrabonRows = array_merge(
    getDataRegular($conn1, $conn2, $filters, '1', '1', ''),
    getDataInstallment($conn1, $conn2, $filters, '1', '1', ''),
    getDataDp($conn1, $conn2, $filters, '1', '1', ''),
    getDataCbd($conn1, $conn2, $filters, '1', '1', ''),
    getDataSaldoAwal($conn2, $filters)
);

$kontrabonRows = array_filter($kontrabonRows, function ($r) {
    return $r['status_pl'] === 'SECOND APPROVED';
});

$alreadyPaidByType = [
    'Regular'     => getAlreadyPaid($conn2, 'Regular'),
    'Installment' => getAlreadyPaid($conn2, 'Installment'),
    'DP'          => getAlreadyPaid($conn2, 'DP'),
    'CBD'         => getAlreadyPaid($conn2, 'CBD'),
    'SaldoAwal'   => getAlreadyPaid($conn2, 'SaldoAwal'),
];

foreach ($kontrabonRows as $r) {
    $type = $r['type'];
    $noKbon = $r['no_kbon'];
    $paid = $alreadyPaidByType[$type][$noKbon] ?? 0;
    $outstanding = (float) $r['total_raw'] - $paid;

    if ($outstanding <= 0) {
        continue;
    }

    $bank = getBankByAccount($conn2, $r['from_account'] ?? '');
    $pc = $r['profit_center'];

    if ($type === 'DP' || $type === 'CBD') {
        $rate = getRateByCurrDate($conn2, $r['curr'], $r['tgl_kbon_raw'] ?? null);
    } else {
        $rate = !empty($r['rate']) ? (float) $r['rate'] : 1;
    }

    $rowsOut[] = [
        'type_pv'   => $type,
        'pc'        => $pc,
        'nama_pc'   => $pcLabels[$pc] ?? $pc,
        'no_pv'     => $noKbon,
        'pv_date'   => $r['tgl_kbon_raw'] ?? null,
        'due_date'  => $r['tgl_tempo_raw'] ?? null,
        'curr'      => $r['curr'] ?? 'IDR',
        'dpp'       => (float) $r['subtotal_raw'],
        'ppn'       => (float) $r['tax_raw'],
        'pph'       => (float) $r['pph_raw'],
        'total'     => $outstanding,
        'rate'      => $rate,
        'bank_name' => $bank['bank_name'],
        'b_code'    => $bank['b_code'],
    ];
}

foreach ($rowsOut as $row) {
    echo '
    <tr>
        <td>
            <input type="checkbox" class="chk_pv">
        </td>
        <td class="pc_pv" data-pcpv="' . htmlspecialchars($row['pc']) . '">' . htmlspecialchars($row['nama_pc']) . '</td>
        <td class="no_pv" data-nopv="' . htmlspecialchars($row['no_pv']) . '" data-typepv="' . htmlspecialchars($row['type_pv']) . '">' . htmlspecialchars($row['no_pv']) . '</td>
        <td>' . (!empty($row['pv_date']) ? date("d-M-Y", strtotime($row['pv_date'])) : '-') . '</td>
        <td>' . (!empty($row['due_date']) ? date("d-M-Y", strtotime($row['due_date'])) : '-') . '</td>
        <td>' . number_format($row['dpp'], 2) . '</td>
        <td>' . number_format($row['ppn'], 2) . '</td>
        <td>' . number_format($row['pph'], 2) . '</td>
        <td class="total_pv" data-total="' . $row['total'] . '">' . number_format($row['total'], 2) . '</td>
        <td class="rate_pv" data-ratepv="' . $row['rate'] . '" data-curr="' . htmlspecialchars($row['curr']) . '">' . number_format($row['rate'], 2) . '</td>
        <td style="width: 170px;">
            <input type="text" class="form-control txt_amount_pv" style="text-align:right" disabled>
        </td>
        <td style="width: 170px;">
            <input type="text" class="form-control txt_amount_pv_idr" style="text-align:right" disabled>
        </td>
    </tr>';
}
