<html>
<head>
    <title>Export Data </title>
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
    header("Content-Disposition: attachment; filename=Reverse Document.xls");
    $type_doc=$_GET['type_doc'];
    $status=$_GET['status'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>REVERSE DOCUMENT <?php echo $type_doc; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    STATUS: <?php echo $status; ?>

    <table style="width:100%;font-size:11pt;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center; vertical-align: middle;">No Reverse</th>
            <th style="text-align: center; vertical-align: middle;">Reverse Date</th>
            <th style="text-align: center; vertical-align: middle;">Type Document</th>
            <th style="text-align: center; vertical-align: middle;">No Document</th>
            <th style="text-align: center; vertical-align: middle;">Document Date</th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">Curr</th>
            <th style="text-align: center; vertical-align: middle;">Total</th>
            <th style="text-align: center; vertical-align: middle;">Deskripsi</th>
            <th style="text-align: center; vertical-align: middle;">Status</th>
            <th style="text-align: center; vertical-align: middle;">Created By</th>
            <th style="text-align: center; vertical-align: middle;">Created Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $type_doc=$_GET['type_doc'];
        $status=$_GET['status'];
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

            echo '<tr style="font-size: 11pt; text-align: left;">';
            echo '<td >'.$no++.'</td>';
            echo '<td value="'.$row['rvs_number'].'">'.$row['rvs_number'].'</td>';
            echo '<td value="'.$row['rvs_date'].'">'.formatDateOrDash($row['rvs_date']).'</td>';
            echo '<td value="'.$row['type_doc'].'">'.$row['type_doc'].'</td>';
            echo '<td value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>';
            echo '<td value="'.$row['doc_date'].'">'.$row['doc_date'].'</td>';
            echo '<td value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>';
            echo '<td value="'.$row['curr'].'">'.$row['curr'].'</td>';
            echo '<td style="text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>';
            echo '<td value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>';
            echo '<td value="'.$row['status'].'">'.$row['status'].'</td>';
            echo '<td value="'.$row['created_by'].'">'.$row['created_by'].'</td>';
            echo '<td value="'.$row['created_date'].'">'.$row['created_date'].'</td>';
            echo '</tr>';
        }

        ?>
    </table>

</body>
</html>




