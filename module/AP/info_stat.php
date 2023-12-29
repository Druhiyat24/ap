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
    header("Content-Disposition: attachment; filename=information_status.xls");
    $nama_supp1=$_GET['nama_supp'];
    $filter1=$_GET['filter'];
    $start_date1 = date("d F Y",strtotime($_GET['start_date']));
    $end_date1 = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp1; ?><br/> PERIODE <?php echo $start_date1; ?> - <?php echo $end_date1; ?></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th>No</th>
            <th>Supplier</th>
            <th>No BPB</th>
            <th>BPB Date</th>
            <th>No Kontrabon</th>
            <th>Kontrabon Date</th>
            <th>No List Payment</th>
            <th>List Payment Date</th>                         
            <th>No Payment</th>                                    
            <th>Payment Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $filter=$_GET['filter'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
// //        if ($filter == 'tgl_bpb') {
        if($nama_supp == 'ALL'){
    $qry = " where $filter between '$start_date' and '$end_date' group by no_bpb";
    }else{
    $qry = " where supp = '$nama_supp' and $filter between '$start_date' and '$end_date' group by no_bpb";
    }

     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status $qry");


//     }elseif ($filter == 'tgl_kbon') {
//         if(empty($nama_supp) and empty($start_date) and empty($end_date)){
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_kbon = '$date_now' group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
//     }    
//     elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
//     }
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
//     }

// }elseif ($filter == 'tgl_lp') {
//         if(empty($nama_supp) and empty($start_date) and empty($end_date)){
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_lp = '$date_now' group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_lp between '$start_date' and '$end_date' group by no_bpb");
//     }    
//     elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
//     }
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_lp between '$start_date' and '$end_date' group by no_bpb");
//     }

//     }else{
//         if(empty($nama_supp) and empty($start_date) and empty($end_date)){
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_pay = '$date_now' group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
//     }
//     elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_pay between '$start_date' and '$end_date' group by no_bpb");
//     }    
//     elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
//     }
//     else{
//     $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_pay between '$start_date' and '$end_date' group by no_bpb");
//     }
//     }
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




