<?php
include "../../conn/conn.php";

$filter_type     = $_GET['filter_type']     ?? '';
$filter_supplier = $_GET['filter_supplier'] ?? '';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=master-bank-supplier-customer.xls");

$where = "WHERE 1=1";
if ($filter_type !== '') {
    $ft = mysqli_real_escape_string($conn2, $filter_type);
    $where .= " AND m.tipe_sup = '$ft'";
}
if ($filter_supplier !== '') {
    $fs = mysqli_real_escape_string($conn2, $filter_supplier);
    $where .= " AND m.id_supplier = '$fs'";
}

$q = mysqli_query($conn2,
    "SELECT m.id,
            ms.Supplier AS supplier_name,
            CASE m.tipe_sup WHEN 'S' THEN 'Supplier' ELSE 'Customer' END AS type,
            m.bank_currency, m.bank_name, m.bank_account,
            m.beneficiary_name, m.status, m.created_by, m.created_date
     FROM master_supplier_bank m
     LEFT JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier
     $where
     ORDER BY ms.Supplier ASC"
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
  <h3>MASTER BANK SUPPLIER - CUSTOMER</h3>
  <br>
  <table>
    <thead>
      <tr>
        <th class="center"><b>No</b></th>
        <th><b>ID</b></th>
        <th><b>Type</b></th>
        <th><b>Supplier / Customer</b></th>
        <th><b>Bank Currency</b></th>
        <th><b>Bank Name</b></th>
        <th><b>Bank Account</b></th>
        <th><b>Beneficiary Name</b></th>
        <th><b>Status</b></th>
        <th><b>Created By</b></th>
        <th><b>Created Date</b></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      while ($row = mysqli_fetch_assoc($q)):
      ?>
      <tr>
        <td class="center"><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['id'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['type']) ?></td>
        <td><?= htmlspecialchars($row['supplier_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['bank_currency']) ?></td>
        <td><?= htmlspecialchars($row['bank_name']) ?></td>
        <td><?= htmlspecialchars($row['bank_account']) ?></td>
        <td><?= htmlspecialchars($row['beneficiary_name']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= htmlspecialchars($row['created_by'] ?? '-') ?></td>
        <td><?= htmlspecialchars($row['created_date'] ?? '-') ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
