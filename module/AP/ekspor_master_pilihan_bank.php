<?php
include "../../conn/conn.php";

$filter_kategori = $_GET['filter_kategori'] ?? '';
$filter_status   = $_GET['filter_status']   ?? '';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=master-bank-choice.xls");

$where = "WHERE 1=1";
if ($filter_kategori !== '') {
    $fk = mysqli_real_escape_string($conn2, $filter_kategori);
    $where .= " AND katergori_bank = '$fk'";
}
if ($filter_status !== '') {
    $fs = mysqli_real_escape_string($conn2, $filter_status);
    $where .= " AND status = '$fs'";
}

$q = mysqli_query($conn2,
    "SELECT id, katergori_bank, nama_bank, kode_bank, swift_code, status
     FROM master_pilihan_bank
     $where
     ORDER BY nama_bank ASC"
);
?>
<html>
<head>
  <style>
    body  { font-family: Calibri, sans-serif; font-size: 11pt; }
    h3    { font-family: Calibri, sans-serif; font-size: 12pt; font-weight: bold;
            text-align: left; margin: 0 0 6px 0; }
    table { border-collapse: collapse; width: 100%; }
    th    { font-family: Calibri, sans-serif; font-size: 11pt; font-weight: bold;
            background: #1E3A8A; color: #fff;
            border: thin solid windowtext;
            padding: 3px 6px; text-align: left; white-space: nowrap; }
    th.center { text-align: center; }
    td    { font-family: Calibri, sans-serif; font-size: 11pt; font-weight: normal;
            border: thin solid windowtext; padding: 2px 6px; text-align: left; }
    td.center { text-align: center; }
  </style>
</head>
<body>
  <h3>MASTER BANK CHOICE</h3>
  <br>
  <table>
    <thead>
      <tr>
        <th class="center"><b>No</b></th>
        <th><b>ID</b></th>
        <th><b>Bank Category</b></th>
        <th><b>Bank Name</b></th>
        <th><b>Bank Code</b></th>
        <th><b>Swift Code</b></th>
        <th><b>Status</b></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      while ($row = mysqli_fetch_assoc($q)):
        $status_label = $row['status'] === 'Y' ? 'Active' : 'Inactive';
      ?>
      <tr>
        <td class="center"><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['katergori_bank'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['nama_bank'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['kode_bank'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['swift_code'] ?? '-') ?></td>
        <td><?= htmlspecialchars($status_label) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
