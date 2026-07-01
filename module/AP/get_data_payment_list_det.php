<?php
include '../../conn/conn.php';

$pl_number = isset($_POST['pl_number']) ? $_POST['pl_number'] : null;

$sql = mysqli_query($conn2, "select type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi from pv_payment_list_det where pl_number = '" . mysqli_real_escape_string($conn2, $pl_number) . "' and status != 'Cancel' order by id asc");

while ($row = mysqli_fetch_assoc($sql)) {
    $deskripsi = !empty($row['deskripsi']) ? $row['deskripsi'] : '-';
    echo '<tr>
        <td style="text-align:left;" value="' . htmlspecialchars($row['type_pv']) . '">' . htmlspecialchars($row['type_pv']) . '</td>
        <td style="text-align:left;" value="' . htmlspecialchars($row['no_kbon']) . '">' . htmlspecialchars($row['no_kbon']) . '</td>
        <td style="text-align:left;" value="' . htmlspecialchars($row['tgl_kbon']) . '">' . (!empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-') . '</td>
        <td style="text-align:left;" value="' . htmlspecialchars($row['nama_supp']) . '">' . htmlspecialchars($row['nama_supp']) . '</td>
        <td style="text-align:left;" value="' . htmlspecialchars($row['curr']) . '">' . htmlspecialchars($row['curr']) . '</td>
        <td style="text-align:right;" value="' . $row['total'] . '">' . number_format($row['total'], 2) . '</td>
        <td class="col-keterangan" style="text-align:left;" value="' . htmlspecialchars($deskripsi) . '">' . htmlspecialchars($deskripsi) . '</td>
    </tr>';
}

mysqli_close($conn2);
?>
