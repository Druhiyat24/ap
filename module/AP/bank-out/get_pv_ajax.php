<?php
include '../../../conn/conn.php';
include '../pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

// Mode "tampilkan yang sudah tertaut saja" - dipakai saat form edit pertama
// dibuka, supaya PV yang sudah dipilih sebelumnya langsung tampil & tercentang
// tanpa perlu Search dulu (data diambil langsung dari b_bankout_det, bukan
// dari pencarian PV/kontrabon yang dibatasi tanggal & supplier).
if (!empty($_POST['linked_only']) && !empty($_POST['exclude_doc_num'])) {
    $doc_num_esc = mysqli_real_escape_string($conn2, $_POST['exclude_doc_num']);

    $sqlDocAccOnly = mysqli_query($conn2, "select akun from b_bankout_h where no_bankout = '$doc_num_esc'");
    $rowDocAccOnly = mysqli_fetch_assoc($sqlDocAccOnly);
    $docAccountOnly = $rowDocAccOnly['akun'] ?? '';

    $sqlLinkedOnly = mysqli_query($conn2, "select no_reff, reff_date, due_date, dpp, ppn, pph, total, curr, rates, profit_center, type_pv from b_bankout_det where no_bankout = '$doc_num_esc'");

    $pcLabelsOnly = [];
    $sqlPcOnly = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc from master_pc");
    while ($r = mysqli_fetch_assoc($sqlPcOnly)) {
        $pcLabelsOnly[$r['kode_pc']] = $r['id_pc'] . ' - ' . $r['nama_pc'];
    }

    while ($row = mysqli_fetch_assoc($sqlLinkedOnly)) {
        echo '
        <tr data-alreadylinked="' . $row['total'] . '">
            <td><input type="checkbox" class="chk_pv"></td>
            <td class="pc_pv" data-pcpv="' . htmlspecialchars($row['profit_center']) . '">' . htmlspecialchars($pcLabelsOnly[$row['profit_center']] ?? $row['profit_center']) . '</td>
            <td class="no_pv" data-nopv="' . htmlspecialchars($row['no_reff']) . '" data-typepv="' . htmlspecialchars($row['type_pv'] ?: 'Biaya') . '" data-account="' . htmlspecialchars($docAccountOnly) . '">' . htmlspecialchars($row['no_reff']) . '<br><small class="text-muted" style="font-size:10px;">' . htmlspecialchars($docAccountOnly ?: '-') . '</small></td>
            <td>' . (!empty($row['reff_date']) ? date("d-M-Y", strtotime($row['reff_date'])) : '-') . '</td>
            <td>' . (!empty($row['due_date']) ? date("d-M-Y", strtotime($row['due_date'])) : '-') . '</td>
            <td>' . number_format($row['dpp'], 2) . '</td>
            <td>' . number_format($row['ppn'], 2) . '</td>
            <td>' . number_format($row['pph'], 2) . '</td>
            <td class="total_pv" data-total="' . $row['total'] . '">' . number_format($row['total'], 2) . '</td>
            <td class="rate_pv" data-ratepv="' . $row['rates'] . '" data-curr="' . htmlspecialchars($row['curr']) . '">' . number_format($row['rates'], 2) . '</td>
            <td style="width: 170px;"><input type="text" class="form-control txt_amount_pv" style="text-align:right" disabled></td>
            <td style="width: 170px;"><input type="text" class="form-control txt_amount_pv_idr" style="text-align:right" disabled></td>
        </tr>';
    }
    exit;
}

$tgl_awal  = date('Y-m-d', strtotime($_POST['tgl_awal']));
$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
$supplier  = $_POST['supplier'];
$supplier_esc = mysqli_real_escape_string($conn2, $supplier);

// Endpoint ini dipakai bareng oleh create-out-bank.php, form_pelunasan.php,
// dan create-pettycash-out.php - fund_type opsional, cuma dipakai
// Petty Cash Out untuk membatasi hasil ke PV yang akunnya Kas Kecil saja
// (bank-funded PV tidak relevan buat petty cash). Kalau tidak dikirim,
// perilaku tetap sama seperti sebelumnya (semua ditampilkan).
$fund_type = $_POST['fund_type'] ?? '';

// Dipakai saat edit dokumen Bank Out yang sudah ada - pembayaran yang SUDAH
// dilakukan OLEH DOKUMEN INI SENDIRI harus dikecualikan dari perhitungan
// outstanding, supaya PV yang sudah tertaut ke dokumen ini tetap muncul di
// pencarian dengan sisa yang benar (seolah dokumen ini belum pernah membayar).
$exclude_doc_num = $_POST['exclude_doc_num'] ?? '';
$exclude_doc_num_esc = mysqli_real_escape_string($conn2, $exclude_doc_num);

// Account dokumen yang sedang diedit (kalau ada) - dipakai untuk menonaktifkan
// checkbox PV yang Account-nya berbeda, supaya tidak bisa digabung dalam 1
// dokumen. Kosong berarti dokumen baru (create) - tidak ada pembatasan.
$docAccount = '';
if ($exclude_doc_num_esc !== '') {
    $sqlDocAcc = mysqli_query($conn2, "select akun from b_bankout_h where no_bankout = '$exclude_doc_num_esc'");
    $rowDocAcc = mysqli_fetch_assoc($sqlDocAcc);
    $docAccount = $rowDocAcc['akun'] ?? '';
}

$kasAccounts = [];
if ($fund_type === 'CASH') {
    $sqlKas = mysqli_query($conn1, "select no_coa from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%'");
    while ($rowKas = mysqli_fetch_assoc($sqlKas)) {
        $kasAccounts[$rowKas['no_coa']] = true;
    }
}

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
function getAlreadyPaid($conn2, $type_pv, $exclude_doc_num = '')
{
    $type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);
    $paid = [];

    $excludeSql = $exclude_doc_num !== '' ? " and b.no_bankout != '" . mysqli_real_escape_string($conn2, $exclude_doc_num) . "'" : '';

    $sqlBank = mysqli_query($conn2, "select a.no_reff, sum(a.total) total_paid
        from b_bankout_det a
        inner join b_bankout_h b on b.no_bankout = a.no_bankout
        where b.status != 'Cancel' and a.type_pv = '$type_pv_esc'$excludeSql
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

// Baris yang sudah tertaut ke dokumen yang sedang diedit ($exclude_doc_num) -
// dipakai supaya JS bisa pre-check checkbox-nya & mengisi ulang amount yang
// SUDAH DIBAYAR (bukan seluruh outstanding, karena bisa saja hanya dibayar
// sebagian) begitu tabel hasil pencarian ditampilkan.
$alreadyLinked = [];
if ($exclude_doc_num_esc !== '') {
    $sqlLinked = mysqli_query($conn2, "select no_reff, total from b_bankout_det where no_bankout = '$exclude_doc_num_esc'");
    while ($rowLinked = mysqli_fetch_assoc($sqlLinked)) {
        $alreadyLinked[$rowLinked['no_reff']] = (float) $rowLinked['total'];
    }
}

// ====================== BIAYA ======================
$sqlBiaya = mysqli_query($conn2, "WITH
    total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.nama_supp = '$supplier_esc' and a.pv_date BETWEEN '$tgl_awal' AND '$tgl_akhir' AND a.status = 'Approved' group by a.no_pv, b.profit_center),

    total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and a.type_pv = 'Biaya'" . ($exclude_doc_num_esc !== '' ? " and b.no_bankout != '$exclude_doc_num_esc'" : "") . " GROUP BY no_reff,a.profit_center)

    select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code, IFNULL(c.rate,1) rate from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.pv_date where (a.total - COALESCE(b.total_pv,0)) > 0
");

while ($row = mysqli_fetch_assoc($sqlBiaya)) {
    // fund_type=CASH: skip PV yang akunnya bukan Kas Kecil (mis. dibayar dari Bank)
    if ($fund_type === 'CASH' && !isset($kasAccounts[$row['frm_akun'] ?? ''])) {
        continue;
    }

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
        'account'   => $row['frm_akun'] ?? '',
        'already_linked' => $alreadyLinked[$row['no_pv']] ?? 0,
    ];
}

// ====================== REGULAR / INSTALLMENT / DP / CBD ======================
// Hanya yang sudah SECOND APPROVED di Payment List (status_pl) yang boleh ditarik.
// Semua type tidak di-filter by date di bank-out — PV/kontrabon bisa dari tanggal berapa pun.
$filters = [
    'nama_supp'   => !empty($supplier) ? $supplier : 'ALL',
    'status'      => 'ALL',
    'filter_date' => '',
    'start_date'  => '',
    'end_date'    => '',
];

$kontrabonRows = array_merge(
    getDataRegular($conn1, $conn2, $filters, '1', '1', ''),
    getDataInstallment($conn1, $conn2, $filters, '1', '1', ''),
    getDataDp($conn1, $conn2, $filters, '1', '1', ''),
    getDataCbd($conn1, $conn2, $filters, '1', '1', ''),
    getDataSaldoAwal($conn2, $filters)
);

// SaldoAwal: getDataSaldoAwal() fallback status_pl ke status saat NULL,
// sehingga $r['status_pl'] tidak bisa diandalkan. Cek nilai asli dari tabel.
$saldoAwalPlApproved = [];
$sqlSaCheck = mysqli_query($conn2, "SELECT no_kbon FROM ap_saldo_payment_voucher WHERE status_pl = 'SECOND APPROVED'");
while ($rr = mysqli_fetch_assoc($sqlSaCheck)) {
    $saldoAwalPlApproved[$rr['no_kbon']] = true;
}

$kontrabonRows = array_filter($kontrabonRows, function ($r) use ($saldoAwalPlApproved) {
    if ($r['type'] === 'SaldoAwal') {
        return isset($saldoAwalPlApproved[$r['no_kbon']]);
    }
    return $r['status_pl'] === 'SECOND APPROVED';
});

$alreadyPaidByType = [
    'Regular'     => getAlreadyPaid($conn2, 'Regular', $exclude_doc_num),
    'Installment' => getAlreadyPaid($conn2, 'Installment', $exclude_doc_num),
    'DP'          => getAlreadyPaid($conn2, 'DP', $exclude_doc_num),
    'CBD'         => getAlreadyPaid($conn2, 'CBD', $exclude_doc_num),
    'SaldoAwal'   => getAlreadyPaid($conn2, 'SaldoAwal', $exclude_doc_num),
];

foreach ($kontrabonRows as $r) {
    $type = $r['type'];
    $noKbon = $r['no_kbon'];
    $paid = $alreadyPaidByType[$type][$noKbon] ?? 0;
    $outstanding = (float) $r['total_raw'] - $paid;

    if ($outstanding <= 0) {
        continue;
    }

    // fund_type=CASH: skip PV yang akunnya bukan Kas Kecil (mis. dibayar dari Bank)
    if ($fund_type === 'CASH' && !isset($kasAccounts[$r['from_account'] ?? ''])) {
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
        'account'   => $r['from_account'] ?? '',
        'already_linked' => $alreadyLinked[$noKbon] ?? 0,
    ];
}

foreach ($rowsOut as $row) {
    $rowAccount = $row['account'] ?? '';
    $isMismatch = ($docAccount !== '' && $rowAccount !== '' && $rowAccount !== $docAccount);
    echo '
    <tr data-alreadylinked="' . $row['already_linked'] . '">
        <td>
            <input type="checkbox" class="chk_pv"' . ($isMismatch ? ' disabled title="Account berbeda dengan Account dokumen ini"' : '') . '>
        </td>
        <td class="pc_pv" data-pcpv="' . htmlspecialchars($row['pc']) . '">' . htmlspecialchars($row['nama_pc']) . '</td>
        <td class="no_pv" data-nopv="' . htmlspecialchars($row['no_pv']) . '" data-typepv="' . htmlspecialchars($row['type_pv']) . '" data-account="' . htmlspecialchars($rowAccount) . '">' . htmlspecialchars($row['no_pv']) . '<br><small class="text-muted" style="font-size:10px;">' . htmlspecialchars($rowAccount ?: '-') . '</small></td>
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
