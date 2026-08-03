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
    header("Content-Disposition: attachment; filename=Petty Cash In.xls");
    $reference = isset($_GET['reference']) ? trim($_GET['reference']) : 'ALL';
    $doc_num = isset($_GET['doc_num']) ? trim($_GET['doc_num']) : '';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <h4>LIST PETTY CASH IN <br/> <?php if($reference == 'ALL'){ echo 'ALL'; echo " REFERENCE "; } else { echo htmlspecialchars(strtoupper($reference)); } ?> <br/> PERIODE <?php echo htmlspecialchars(strtoupper($start_date)); ?> - <?php echo htmlspecialchars(strtoupper($end_date)); ?></h4>

    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">Document Number</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Reference</th>
            <th style="text-align: center;vertical-align: middle;">Reference Document</th>
            <th style="text-align: center;vertical-align: middle;">Other Document</th>
            <th style="text-align: center;vertical-align: middle;">Account</th>
            <th style="text-align: center;vertical-align: middle;">Curr</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>                                 
            <th style="text-align: center; vertical-align: middle;">Deskripsi</th>
            <th style="text-align: center; vertical-align: middle;">Status</th>
            <th style="text-align: center; vertical-align: middle;">Create By</th>
            <th style="text-align: center; vertical-align: middle;">Create Date</th>
        </tr>
        <?php
        // koneksi database
        include '../../conn/conn.php';
        $reference = isset($_GET['reference']) ? trim($_GET['reference']) : 'ALL';
        $doc_num = isset($_GET['doc_num']) ? trim($_GET['doc_num']) : '';
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $conditions = [];
        if ($reference !== '' && $reference !== 'ALL') {
            $conditions[] = "a.reff = '" . mysqli_real_escape_string($conn1, $reference) . "'";
        }
        if ($doc_num !== '') {
            $conditions[] = "a.no_pci like '%" . mysqli_real_escape_string($conn1, $doc_num) . "%'";
        }
        if ($start_date !== '' && $end_date !== '') {
            $conditions[] = "a.tgl_pci between '" . mysqli_real_escape_string($conn1, $start_date) . "' and '" . mysqli_real_escape_string($conn1, $end_date) . "'";
        }
        $where = count($conditions) ? ('where ' . implode(' and ', $conditions)) : '';

        $sql = mysqli_query($conn2,"select a.no_pci,a.tgl_pci,a.reff,if(a.reff_doc = '','-',a.reff_doc) as reff_doc,if(a.oth_doc = '','-',a.oth_doc) as oth_doc, a.curr, b.nama_coa,a.amount,if(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.status, a.create_by, a.create_date from c_petty_cashin_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun $where group by a.no_pci");

        // echo "select a.no_pci,a.tgl_pci,a.reff,if(a.reff_doc = '','-',a.reff_doc) as reff_doc,if(a.oth_doc = '','-',a.oth_doc) as oth_doc, a.curr, b.nama_coa,a.amount,if(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.status, a.create_by, a.create_date from c_petty_cashin_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun $where group by a.no_pci";

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

            $reff =$row['reff'];
            $oth = $row['oth_doc'];

            if ($reff == 'None') {
                $reff_doc = $row['oth_doc'];
            }else{
                $reff_doc = $row['reff_doc'];
            }

            if ($reff == 'None') {
                $oth_doc = '-';
            }else{
                $oth_doc = $row['oth_doc'];
            }

            echo '<tr style="font-size:12px;text-align:left;">
            <td >'.$no++.'</td>
            <td style="" value = "'.$row['no_pci'].'">'.$row['no_pci'].'</td>
            <td style="" value = "'.$row['tgl_pci'].'">'.date("d-M-Y",strtotime($row['tgl_pci'])).'</td>
            <td style="" value = "'.$row['reff'].'">'.$row['reff'].'</td>
            <td style="" value = "'.$reff_doc.'">'.$reff_doc.'</td>
            <td style="" value = "'.$oth_doc.'">'.$oth_doc.'</td>
            <td style="" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td style="" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align : right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style="" value = "'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
            <td value = "'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td value = "'.$row['create_date'].'">'.date("d-M-Y H:i:s",strtotime($row['create_date'])).'</td>
            ';

            ?>
            <?php 
        }
        ?>
    </table>

</body>
</html>




