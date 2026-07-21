<?php
ini_set('memory_limit', '4096M');
set_time_limit(0);

include '../../conn/conn.php';

// Format .xls (HTML table, sama seperti export lain di app ini, misal
// ekspor_ca_fabric_trx_in_barcode.php) - jauh lebih cepat daripada PhpSpreadsheet/.xlsx
// karena baris langsung di-echo streaming, tidak lewat proses build objek + zip + XML.
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=List Barcode.xls");

$start_date      = date("Y-m-d", strtotime($_GET['start_date']));
$end_date        = date("Y-m-d", strtotime($_GET['end_date']));
$start_date_text = date("d F Y", strtotime($_GET['start_date']));
$end_date_text   = date("d F Y", strtotime($_GET['end_date']));
?>
<html>
<head>
    <title>Export List Barcode</title>
</head>
<body>
    <style type="text/css">
        body{ font-family: sans-serif; }
        table{ margin: 20px auto; border-collapse: collapse; }
        table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
    </style>

    <h4> LIST BARCODE<br/> PERIODE: <?php echo $start_date_text; ?> - <?php echo $end_date_text; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No BPB</th>
            <th style="text-align: center;vertical-align: middle;">Tgl BPB</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">No Barcode</th>
            <th style="text-align: center;vertical-align: middle;">Id Item</th>
            <th style="text-align: center;vertical-align: middle;">Item Desc</th>
            <th style="text-align: center;vertical-align: middle;">Id JO</th>
            <th style="text-align: center;vertical-align: middle;">No WS</th>
            <th style="text-align: center;vertical-align: middle;">Style</th>
            <th style="text-align: center;vertical-align: middle;">Tgl Terima</th>
            <th style="text-align: center;vertical-align: middle;">Curr</th>
            <th style="text-align: center;vertical-align: middle;">Price</th>
        </tr>
        <?php
        // Dataset besar (bisa ratusan ribu baris) - tidak dibatasi jumlah baris (sengaja),
        // tiap baris di-echo langsung per iterasi while, bukan dikumpulkan ke array dulu.
        $sql = mysqli_query($conn1, "SELECT a.no_dok, a.tgl_dok, a.supplier, IFNULL(a.no_po,'-') no_po, b.no_barcode, b.id_jo, b.id_item,
                mi.itemdesc, tmpjo.kpno, tmpjo.styleno, b.np_curr, b.np_tgl_in, b.np_price
                FROM whs_inmaterial_fabric a
                INNER JOIN whs_lokasi_inmaterial b ON b.no_dok = a.no_dok
                INNER JOIN masteritem mi ON mi.id_item = b.id_item
                LEFT JOIN (
                    SELECT id_jo, kpno, styleno
                    FROM act_costing ac
                    INNER JOIN so ON ac.id = so.id_cost
                    INNER JOIN jo_det jod ON so.id = jod.id_so
                    GROUP BY id_jo
                ) tmpjo ON tmpjo.id_jo = b.id_jo
                WHERE a.supplier NOT LIKE '%Production -%'
                  AND a.status != 'Cancel'
                  AND b.status = 'Y'
                  AND a.tgl_dok BETWEEN '$start_date' AND '$end_date'
                GROUP BY b.no_barcode
                ORDER BY a.tgl_dok ASC, a.no_dok ASC");

        $no = 0;
        while ($row = mysqli_fetch_assoc($sql)) {
            $no++;
            echo ' <tr style="font-size:12px;text-align:center;">
            <td>'.$no.'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['no_dok']).'</td>
            <td style="width:100px;">'.(!empty($row['tgl_dok']) ? date("d-M-Y", strtotime($row['tgl_dok'])) : '-').'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['supplier']).'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['no_po']).'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['no_barcode']).'</td>
            <td>'.htmlspecialchars($row['id_item']).'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['itemdesc']).'</td>
            <td>'.htmlspecialchars($row['id_jo']).'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['kpno']).'</td>
            <td style="text-align:left;">'.htmlspecialchars($row['styleno']).'</td>
            <td style="width:100px;">'.(!empty($row['np_tgl_in']) ? date("d-M-Y", strtotime($row['np_tgl_in'])) : '-').'</td>
            <td>'.htmlspecialchars($row['np_curr']).'</td>
            <td style="text-align:right;">'.number_format((float)$row['np_price'], 4).'</td>
            </tr>';
        }
        ?>
    </table>

</body>
</html>
