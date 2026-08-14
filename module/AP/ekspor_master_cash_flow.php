<html>
<head>
    <title>Export Mapping Cash Flow</title>
</head>
<body>
    <style type="text/css">
    body{
        font-family: sans-serif;
    }
    table{
        margin: 20px auto;
        border-collapse: collapse;
    }
    table th,
    table td{
        border: 1px solid #3c3c3c;
        padding: 3px 8px;
    }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=mapping-cash-flow.xls");
    ?>

    <h4>DATA MAPPING CASH FLOW</h4>

    <table style="width:100%;font-size:12px;" border="1">
        <tr>
            <th style="text-align: center;vertical-align: middle;">ID</th>
            <th style="text-align: center;vertical-align: middle;">Type</th>
            <th style="text-align: center;vertical-align: middle;">Category</th>
            <th style="text-align: center;vertical-align: middle;">Subcategory (Full Name)</th>
            <th style="text-align: center;vertical-align: middle;">Short Name (Used in Transaction)</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
        </tr>
        <?php
        include '../../conn/conn.php';

        $type_cashflow = isset($_GET['type_cashflow']) ? $_GET['type_cashflow'] : 'ALL';
        $status = isset($_GET['status']) ? $_GET['status'] : 'ALL';

        $conditions = [];
        if ($type_cashflow !== '' && $type_cashflow !== 'ALL') {
            $conditions[] = "type_cashflow = '" . mysqli_real_escape_string($conn2, $type_cashflow) . "'";
        }
        if ($status !== '' && $status !== 'ALL') {
            $conditions[] = "status = '" . mysqli_real_escape_string($conn2, $status) . "'";
        }
        $where = count($conditions) > 0 ? 'where ' . implode(' and ', $conditions) : '';

        $sql = mysqli_query($conn2, "select id, type_cashflow, nama_category, nama_subcategory, show_subcategory, status from master_cash_flow $where order by type_cashflow asc, nama_category asc, urutan asc");

        while ($row = mysqli_fetch_assoc($sql)) {
            $statusLabel = ($row['status'] == 'Y') ? 'Active' : 'Inactive';
            echo '<tr style="font-size:12px;text-align:center;">
                <td>' . $row['id'] . '</td>
                <td style="text-align:left">' . $row['type_cashflow'] . '</td>
                <td style="text-align:left">' . $row['nama_category'] . '</td>
                <td style="text-align:left">' . $row['nama_subcategory'] . '</td>
                <td style="text-align:left">' . $row['show_subcategory'] . '</td>
                <td>' . $statusLabel . '</td>
            </tr>';
        }
        ?>
    </table>
</body>
</html>
