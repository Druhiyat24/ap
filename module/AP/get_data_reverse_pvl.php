<?php
include '../../conn/conn.php';
$rvs_number = $_POST['no_rvs'] ?? '';
$rvs_number_esc = mysqli_real_escape_string($conn2, $rvs_number);
$sql = mysqli_query($conn2, "SELECT d.doc_number, d.doc_date, h.status, h.deskripsi AS pvl_desc, d.deskripsi
    FROM ap_reverse_det d
    LEFT JOIN pv_payment_voucher_list_h h ON h.pl_number = d.doc_number
    WHERE d.rvs_number = '$rvs_number_esc' AND d.status = 'Y'");
while ($row = mysqli_fetch_assoc($sql)) {
    $pvlDate = !empty($row['doc_date']) ? date('d-M-Y', strtotime($row['doc_date'])) : '-';
    // Fetch inline PV numbers as chips
    $pvl_esc = mysqli_real_escape_string($conn2, $row['doc_number']);
    $sqlPv = mysqli_query($conn2, "SELECT type_pv, no_kbon FROM pv_payment_voucher_list_det WHERE pl_number='$pvl_esc' AND status != 'Cancel' ORDER BY id ASC");
    $pvBadges = [];
    while ($pv = mysqli_fetch_assoc($sqlPv)) {
        $pvBadges[] = '<span style="display:inline-block;background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;border-radius:4px;padding:1px 6px;font-size:10px;margin:1px 2px;">' . htmlspecialchars($pv['no_kbon']) . '</span>';
    }
    $pvCell = !empty($pvBadges) ? implode(' ', $pvBadges) : '-';
    echo '<tr>
        <td>' . htmlspecialchars($row['doc_number']) . '</td>
        <td>' . $pvlDate . '</td>
        <td style="text-align:left;">' . $pvCell . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['pvl_desc'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['status'] ?? '-') . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['deskripsi'] ?? '-') . '</td>
    </tr>';
}
mysqli_close($conn2);
