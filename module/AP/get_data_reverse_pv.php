<?php
include '../../conn/conn.php';

$rvs_number = $_POST['no_rvs'] ?? '';
if (empty($rvs_number)) {
    echo '<tr><td colspan="7" style="text-align:center;color:red;">Error: no_rvs is empty</td></tr>';
    exit;
}

$rvs_esc = mysqli_real_escape_string($conn2, $rvs_number);

$sql = mysqli_query($conn2,
    "SELECT d.doc_number, d.type_pv, d.doc_date, d.nama_supp, d.curr, d.total, d.deskripsi
     FROM ap_reverse_det d
     WHERE d.rvs_number = '$rvs_esc' AND d.status = 'Y'
     ORDER BY d.id ASC"
);

if (!$sql) {
    echo '<tr><td colspan="7" style="text-align:center;color:red;">Query error: ' . htmlspecialchars(mysqli_error($conn2)) . '</td></tr>';
    exit;
}

$count = 0;
while ($row = mysqli_fetch_assoc($sql)) {
    $count++;
    $pvDate = !empty($row['doc_date']) ? date('d-M-Y', strtotime($row['doc_date'])) : '-';
    $total  = is_numeric($row['total']) ? number_format((float)$row['total'], 2) : '-';
    echo '<tr>
        <td>' . htmlspecialchars($row['doc_number']) . '</td>
        <td>' . htmlspecialchars($row['type_pv'] ?? '-') . '</td>
        <td>' . $pvDate . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['nama_supp'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['curr'] ?? '-') . '</td>
        <td style="text-align:right;">' . $total . '</td>
        <td style="text-align:left;">' . htmlspecialchars($row['deskripsi'] ?? '-') . '</td>
    </tr>';
}

if ($count === 0) {
    echo '<tr><td colspan="7" style="text-align:center;">No data found.</td></tr>';
}

mysqli_close($conn2);
