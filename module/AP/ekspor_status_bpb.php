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
    header("Content-Disposition: attachment; filename=Status.xls");
    $nama_supp=$_GET['nama_supp'];
    $filter=$_GET['filter'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>STATUS INFORMATION <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
    FILTER DATE: <?php echo $filter; ?>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
             <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No BPB</th>
            <th style="text-align: center; vertical-align: middle;">BPB Date</th>
            <th style="text-align: center; vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center; vertical-align: middle;">Kontrabon Date</th>
            <th style="text-align: center; vertical-align: middle;">No List Payment</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Date</th>                         
            <th style="text-align: center; vertical-align: middle;">No Payment</th>                                    
            <th style="text-align: center; vertical-align: middle;">Payment Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $filter=$_GET['filter'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  
//         if ($filter == 'tgl_bpb') {
        
//     if ($nama_supp == 'ALL') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_bpb between '$start_date' and '$end_date' group by no_bpb");
//     }
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_bpb between '$start_date' and '$end_date' group by no_bpb");
//     }

//     }elseif ($filter == 'tgl_kbon') {
//        if ($nama_supp == 'ALL' ) {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
//     } 
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
//     }

// }elseif ($filter == 'tgl_lp') {
//         if ($nama_supp == 'ALL' ) {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_lp between '$start_date' and '$end_date' group by no_bpb");
//     }    
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_lp between '$start_date' and '$end_date' group by no_bpb");
//     }

//     }else{
//         if ($nama_supp == 'ALL') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_pay between '$start_date' and '$end_date' group by no_bpb");
//     }    
    
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_pay between '$start_date' and '$end_date' group by no_bpb");
//     }
//     }

        $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_bpb between '$start_date' and '$end_date' group by no_bpb");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $tgl_kb = isset($row['tgl_kbon']) ? $row['tgl_kbon'] :null;
    $tgl_lp = isset($row['tgl_lp']) ? $row['tgl_lp'] :null;
    $tgl_pay = isset($row['tgl_pay']) ? $row['tgl_pay'] :null;

    if ($tgl_kb != '') {
        $tgl_kbon = date("d-M-Y",strtotime($row['tgl_kbon']));
        $kbon = $row['no_kbon'];
    }else{
        $tgl_kbon = '-';
        $kbon = '-';
    }

    if ($tgl_lp != '') {
        $tgl_lipa = date("d-M-Y",strtotime($row['tgl_lp']));
        $lipa = $row['no_lp'];
    }else{
        $tgl_lipa = '-';
        $lipa = '-';
    }

    if ($tgl_pay != '') {
        $tgl_payment = date("d-M-Y",strtotime($row['tgl_pay']));
        $payment = $row['no_pay'];
    }else{
        $tgl_payment = '-';
        $payment = '-';
    }


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="width: 250px;" value="'.$row['supp'].'">'.$row['supp'].'</td>
            <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
            <td value="'.$kbon.'">'.$kbon.'</td>
            <td style="" value="'.$tgl_kbon.'">'.$tgl_kbon.'</td>
            <td value="'.$lipa.'">'.$lipa.'</td>
            <td style="" value="'.$tgl_lipa.'">'.$tgl_lipa.'</td>
            <td value="'.$payment.'">'.$payment.'</td>
            <td style="" value="'.$tgl_payment.'">'.$tgl_payment.'</td>
             ';
         
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




