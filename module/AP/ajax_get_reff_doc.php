<?php

include '../../conn/conn.php'; // Koneksi ke database

$no_coa = isset($_POST['no_coa']) ? $_POST['no_coa'] : '';

// query standar, misal ambil dari tabel referensi dokumen
$query = "SELECT a.no_bankout, a.bankout_date from b_bankout_h a INNER JOIN b_bankout_none b on b.no_bankout = a.no_bankout where nama_supp = 'ONLINE SHOP' and b.no_coa = '$no_coa' and a.bankout_date >= '2025-01-01'";
$result = mysqli_query($conn2, $query);

$list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = [
            'id' => $row['no_bankout'],
            'nama' => $row['no_bankout']
        ];
    }
}

echo json_encode($list);
?>
