<html>
<head>
    <title>Export Data Ke Excel </title>
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
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Status Information.xls");
    $nama_supp=$_GET['nama_supp'];
    $filter=$_GET['filter'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>STATUS INFORMATION <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    FILTER DATE: <?php echo $filter; ?>

    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No BPB</th>
            <th style="text-align: center; vertical-align: middle;">BPB Date</th>
            <th style="text-align: center; vertical-align: middle;">BPB Approved Date</th>
            <th style="text-align: center; vertical-align: middle;">BPB Verified Date</th>
            <th style="text-align: center; vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center; vertical-align: middle;">Kontrabon Date</th>
            <th style="text-align: center; vertical-align: middle;">Kontrabon Approved Date</th>
            <th style="text-align: center; vertical-align: middle;">No List Payment</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Date</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Approved Date</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Closed Date</th>
            <th style="text-align: center; vertical-align: middle;">No Payment</th>
            <th style="text-align: center; vertical-align: middle;">Payment Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $filter=$_GET['filter'];
        $query=$_GET['query'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $sql = mysqli_query($conn2,$query);

        $no = 1;

        function formatDateOrDash($date) {
            return (!empty($date) && $date != '0000-00-00')
            ? date("d-M-Y", strtotime($date))
            : '-';
        }

        function valueOrDash($value) {
            return !empty($value) ? $value : '-';
        }

        while ($row = mysqli_fetch_array($sql)) {
            $tgl_verif_bpb   = formatDateOrDash($row['verif_date'] ?? null);
            $tgl_approve_bpb = formatDateOrDash($row['approve_bpb'] ?? null);
            $tgl_approve_kbon= formatDateOrDash($row['approve_kbon'] ?? null);
            $tgl_approve_lp  = formatDateOrDash($row['approve_lp'] ?? null);
            $tgl_closing_lp  = formatDateOrDash($row['close_lp'] ?? null);
            $tgl_kbon        = formatDateOrDash($row['tgl_kbon'] ?? null);
            $tgl_lipa        = formatDateOrDash($row['tgl_payment'] ?? null);
            $tgl_payment     = formatDateOrDash($row['tgl_pelunasan'] ?? null);

            $kbon    = valueOrDash($row['no_kbon'] ?? null);
            $lipa    = valueOrDash($row['no_payment'] ?? null);
            $payment = valueOrDash($row['no_pelunasan'] ?? null);

            echo '<tr style="font-size: 12px; text-align: center;">';
            echo '<td >'.$no++.'</td>';
            echo '<td style="width: 250px; text-align: left;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>';
            echo '<td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>';
            echo '<td value="'.$row['tgl_bpb'].'">'.formatDateOrDash($row['tgl_bpb']).'</td>';
            echo '<td value="'.$tgl_approve_bpb.'">'.$tgl_approve_bpb.'</td>';
            echo '<td value="'.$tgl_verif_bpb.'">'.$tgl_verif_bpb.'</td>';
            echo '<td value="'.$kbon.'">'.$kbon.'</td>';
            echo '<td value="'.$tgl_kbon.'">'.$tgl_kbon.'</td>';
            echo '<td value="'.$tgl_approve_kbon.'">'.$tgl_approve_kbon.'</td>';
            echo '<td value="'.$lipa.'">'.$lipa.'</td>';
            echo '<td value="'.$tgl_lipa.'">'.$tgl_lipa.'</td>';
            echo '<td value="'.$tgl_approve_lp.'">'.$tgl_approve_lp.'</td>';
            echo '<td value="'.$tgl_closing_lp.'">'.$tgl_closing_lp.'</td>';
            echo '<td value="'.$payment.'">'.$payment.'</td>';
            echo '<td value="'.$tgl_payment.'">'.$tgl_payment.'</td>';
            echo '</tr>';
        }

        ?>
    </table>

</body>
</html>




