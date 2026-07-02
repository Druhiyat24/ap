<?php
include '../../conn/conn.php';
$rvs_number = $_POST['no_rvs'] ?? '';
$rvs_number_esc = mysqli_real_escape_string($conn2, $rvs_number);

$sql = mysqli_query($conn2, "SELECT d.doc_number, d.doc_date, h.from_account,
        CONCAT(b.bank_account, IF(b.bank_name != '', CONCAT(' - ', b.bank_name), '')) AS from_account_label,
        h.status, h.deskripsi as pl_desc, d.deskripsi
    FROM ap_reverse_det d
    LEFT JOIN pv_payment_list_h h ON h.pl_number = d.doc_number
    LEFT JOIN b_masterbank b ON b.bank_account = h.from_account
    WHERE d.rvs_number = '$rvs_number_esc' AND d.status = 'Y'");

while ($row = mysqli_fetch_assoc($sql)) {
    $plDate = !empty($row['doc_date']) ? date('d-M-Y', strtotime($row['doc_date'])) : '-';

    $pl_esc = mysqli_real_escape_string($conn2, $row['doc_number']);
    $sqlPv = mysqli_query($conn2, "SELECT type_pv, no_kbon FROM pv_payment_list_det WHERE pl_number='$pl_esc' AND status != 'Cancel' ORDER BY id ASC");
    $pvBadges = [];
    while ($pv = mysqli_fetch_assoc($sqlPv)) {
        $pvBadges[] = '<span style="display:inline-block;background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;border-radius:4px;padding:1px 6px;font-size:10px;margin:1px 2px;">'
            . htmlspecialchars($pv['no_kbon']) . '</span>';
    }
    $pvCell = !empty($pvBadges) ? implode(' ', $pvBadges) : '-';

    echo '<tr>
        <td>' . htmlspecialchars($row['doc_number']) . '</td>
        <td>' . $plDate . '</td>
        <td style="text-align:left;">' . $pvCell . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['from_account_label'] ?? '-') . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['pl_desc'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['status'] ?? '-') . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['deskripsi'] ?? '-') . '</td>
    </tr>';
}
mysqli_close($conn2);
?>
