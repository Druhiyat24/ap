<?php
include '../../conn/conn.php';
$pvl_number = isset($_POST['pvl_number']) ? $_POST['pvl_number'] : '';
$pvl_esc    = mysqli_real_escape_string($conn2, $pvl_number);
$sql = mysqli_query($conn2, "
    SELECT det.type_pv, det.no_kbon, det.tgl_kbon, det.nama_supp, det.curr, det.total, det.deskripsi,
        (
            (SELECT COUNT(*) FROM b_bankout_det bdet INNER JOIN b_bankout_h bh ON bh.no_bankout = bdet.no_bankout WHERE bdet.no_reff = det.no_kbon AND bh.status != 'Cancel')
            + (SELECT COUNT(*) FROM c_petty_cashout_det pdet INNER JOIN c_petty_cashout_h ph ON ph.no_pco = pdet.no_pco WHERE pdet.no_reff = det.no_kbon AND ph.status != 'Cancel')
            + (SELECT COUNT(*) FROM b_bankout_none bnone INNER JOIN b_bankout_h bh ON bh.no_bankout = bnone.no_bankout WHERE bnone.reff_doc = det.no_kbon AND bh.status != 'Cancel')
        ) AS paid_count,
        (SELECT COUNT(*) FROM pv_payment_list_det pld WHERE pld.no_kbon = det.no_kbon AND pld.status != 'Cancel') AS in_pl_count
    FROM pv_payment_voucher_list_det det
    WHERE det.pl_number = '$pvl_esc' AND det.status != 'Cancel'
    ORDER BY det.id ASC
");
$rows = []; $totals = []; $has_paid = false; $has_in_pl = false;
while ($row = mysqli_fetch_assoc($sql)) {
    $rows[] = $row;
    $totals[$row['curr']] = ($totals[$row['curr']] ?? 0) + (float)$row['total'];
    if ((int)$row['paid_count'] > 0) $has_paid = true;
    if ((int)$row['in_pl_count'] > 0) $has_in_pl = true;
}
foreach ($rows as $row) {
    $isPaid   = (int)$row['paid_count'] > 0;
    $isInPl   = (int)$row['in_pl_count'] > 0;
    $isBlock  = $isPaid || $isInPl;
    $rowStyle = $isBlock ? ' style="background:#fef2f2;"' : '';
    $badges   = '';
    if ($isPaid) $badges .= ' <span style="background:#dc2626;color:#fff;border-radius:4px;padding:1px 5px;font-size:10px;font-weight:bold;">Paid</span>';
    if ($isInPl) $badges .= ' <span style="background:#d97706;color:#fff;border-radius:4px;padding:1px 5px;font-size:10px;font-weight:bold;">In PL</span>';
    $tglFmt    = !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-';
    $deskripsi = !empty($row['deskripsi']) ? htmlspecialchars($row['deskripsi']) : '-';
    echo '<tr' . $rowStyle . '>
        <td style="text-align:left;">' . htmlspecialchars($row['type_pv']) . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['no_kbon']) . $badges . '</td>
        <td>' . $tglFmt . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['nama_supp']) . '</td>
        <td style="text-align:center;">' . htmlspecialchars($row['curr']) . '</td>
        <td style="text-align:right;">' . number_format((float)$row['total'], 2) . '</td>
        <td style="text-align:left;">' . $deskripsi . '</td>
    </tr>';
}
foreach ($totals as $curr => $total) {
    echo '<tr style="background:#dbeafe;font-weight:700;border-top:2px solid #1e3a8a;">
        <td colspan="4" style="text-align:right;padding-right:8px;">Total Amount (' . htmlspecialchars($curr) . ')</td>
        <td style="text-align:center;">' . htmlspecialchars($curr) . '</td>
        <td style="text-align:right;">' . number_format($total, 2) . '</td>
        <td></td>
    </tr>';
}
if ($has_paid) {
    echo '<tr><td colspan="7" style="background:#fef2f2;color:#dc2626;font-weight:bold;text-align:center;padding:8px;border-top:2px solid #dc2626;">&#9888; Some PVs have already been paid and cannot be reversed.</td></tr>';
}
if ($has_in_pl) {
    echo '<tr><td colspan="7" style="background:#fff7ed;color:#d97706;font-weight:bold;text-align:center;padding:8px;border-top:2px solid #d97706;">&#9888; Some PVs are already included in a Payment List and cannot be reversed.</td></tr>';
}
mysqli_close($conn2);
