<?php
include '../../conn/conn.php';

$tl_number = isset($_POST['tl_number']) ? $_POST['tl_number'] : null;
$tl_number_esc = mysqli_real_escape_string($conn2, $tl_number);

$sql = mysqli_query($conn2, "SELECT h.pl_number, h.pl_date, h.deskripsi, h.status, h.from_account,
        (SELECT SUM(total) FROM pv_payment_list_det d WHERE d.pl_number = h.pl_number AND d.status != 'Cancel') total_pl,
        (SELECT curr FROM pv_payment_list_det d WHERE d.pl_number = h.pl_number AND d.status != 'Cancel' LIMIT 1) curr_pl
    FROM pv_transfer_list_det t
    INNER JOIN pv_payment_list_h h ON h.pl_number = t.pl_number
    WHERE t.tl_number = '$tl_number_esc'
    ORDER BY h.pl_date ASC, h.pl_number ASC");

$totals = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $curr = $row['curr_pl'] ?: 'IDR';
    $totals[$curr] = ($totals[$curr] ?? 0) + (float) $row['total_pl'];

    echo '<tr>
        <td style="text-align:left;">' . htmlspecialchars($row['pl_number']) . '</td>
        <td style="text-align:left;">' . (!empty($row['pl_date']) ? date('d-M-Y', strtotime($row['pl_date'])) : '-') . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['deskripsi']) . '</td>
        <td style="text-align:center;">' . htmlspecialchars($row['status']) . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['from_account']) . '</td>
        <td style="text-align:center;">' . htmlspecialchars($curr) . '</td>
        <td style="text-align:right;">' . number_format((float) $row['total_pl'], 2) . '</td>
    </tr>';
}

foreach ($totals as $curr => $total) {
    echo '<tr style="background:#dbeafe;font-weight:700;border-top:2px solid #1e3a8a;">
        <td colspan="5" style="text-align:right;padding-right:8px;">Total Amount (' . htmlspecialchars($curr) . ')</td>
        <td style="text-align:center;">' . htmlspecialchars($curr) . '</td>
        <td style="text-align:right;">' . number_format($total, 2) . '</td>
    </tr>';
}

mysqli_close($conn2);
?>
