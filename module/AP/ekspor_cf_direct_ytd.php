<html>
<head>
    <title>Export Data CF Direct </title>
</head>
<body>
    <style type="text/css">
        body{
            font-family: sans-serif;
        }
        table{
            margin: 15px auto;
            border-style: none;
        }
        table th,
        table td{
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
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=cf-direct-ytd.xls");
    // $nama_supp =$_GET['nama_supp'];
    // $status =$_GET['status'];
    $bulan_awal = date("m",strtotime($_GET['start_date']));
    $bulan_akhir = date("m",strtotime($_GET['end_date']));  
    $tahun_awal = date("Y",strtotime($_GET['start_date']));
    $tahun_akhir = date("Y",strtotime($_GET['end_date'])); 
    $kata_filter = $_GET['kata_filter'];

    $sqlawal = mysqli_query($conn2,"select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal'");
    $rowawal = mysqli_fetch_array($sqlawal);
    $tgl_awal = isset($rowawal['tgl_awal']) ? $rowawal['tgl_awal'] : null;
    $start_date = date("d F Y",strtotime($tgl_awal));
    $tanggal_awal = date("Y-m-d",strtotime($tgl_awal));

    $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
    $rowakhir = mysqli_fetch_array($sqlakhir);
    $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
    $end_date = date("d F Y",strtotime($tgl_akhir));
    $tanggal_akhir = date("Y-m-d",strtotime($tgl_akhir)); 

    ?>
<!-- 
    <center>
        <h4>TRIAL BALANCE YEAR TO DATE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center> -->
    <!--   STATUS: <?php echo $status; ?> -->

    <table style="width:70%;font-size:15px;" >
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PT NIRWANA ALABARE GARMENT</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN ARUS KAS - METODE LANGSUNG</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF CASH FLOW - DIRECT METHOD</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE PERIODS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"><b><?php echo $end_date; ?>.</b></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- Aktivitas Operasi -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Operasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Operating Activities</i></b></th>
        </tr>
        <?php
        $sql = mysqli_query($conn2,"WITH
accounts AS (
  SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

revaluasi as (select '2' id, periode, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg GROUP BY periode),

pembayaran as (select a.id, periode, sub_kategori, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a where a.id = 2 ORDER BY a.id, periode ASC),
          
    query_lama as (select 
          a.id,
          b.periode, 
          a.nama_pilihan AS sub_kategori, 
          a.nama_pilihan_eng AS sub_kategori_eng, 
          total
          FROM 
          (SELECT * FROM tb_master_pilihan WHERE status = 'Y') a
          INNER JOIN (
              SELECT id, ind_name, '$tahun_awal-01' periode, coalesce(saldo_jan,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-02' periode, coalesce(saldo_feb,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-03' periode, coalesce(saldo_mar,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-04' periode, coalesce(saldo_apr,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-05' periode, coalesce(saldo_may,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-06' periode, coalesce(saldo_jun,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-07' periode, coalesce(saldo_jul,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-08' periode, coalesce(saldo_aug,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-09' periode, coalesce(saldo_sep,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-10' periode, coalesce(saldo_oct,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-11' periode, coalesce(saldo_nov,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-12' periode, coalesce(saldo_dec,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
          ) b 
          ON b.ind_name = a.nama_pilihan
          WHERE a.type_pilihan = 'Arus Kas dari Aktivitas Operasi' and a.id != 2
          ORDER BY a.id ASC),
          
hasil as (select * from (select * from query_lama
          UNION ALL
          select '2' id, a.periode, sub_kategori, sub_kategori_eng, (total_all + revaluasi) total from pembayaran a INNER JOIN revaluasi b on b.id = a.id and b.periode = a.periode) a order by periode, id asc)
          
  select id, sub_kategori, sub_kategori_eng, sum(total) total from hasil WHERE periode
BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m') group by id
          ");

        $sql2 = mysqli_query($conn2,"select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, total from (select * from tb_master_pilihan where status = 'Y') a inner join (SELECT 
            id,
            ind_name,
            COALESCE((
            (CASE WHEN '$tahun_awal-01' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_jan ELSE 0 END) +
            (CASE WHEN '$tahun_awal-02' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_feb ELSE 0 END) +
            (CASE WHEN '$tahun_awal-03' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_mar ELSE 0 END) +
            (CASE WHEN '$tahun_awal-04' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_apr ELSE 0 END) +
            (CASE WHEN '$tahun_awal-05' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_may ELSE 0 END) +
            (CASE WHEN '$tahun_awal-06' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_jun ELSE 0 END) +
            (CASE WHEN '$tahun_awal-07' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_jul ELSE 0 END) +
            (CASE WHEN '$tahun_awal-08' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_aug ELSE 0 END) +
            (CASE WHEN '$tahun_awal-09' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_sep ELSE 0 END) +
            (CASE WHEN '$tahun_awal-10' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_oct ELSE 0 END) +
            (CASE WHEN '$tahun_awal-11' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_nov ELSE 0 END) +
            (CASE WHEN '$tahun_awal-12' BETWEEN LEFT('$tgl_awal', 7) AND LEFT('$tgl_akhir', 7) THEN saldo_dec ELSE 0 END)
            ),0) AS total
            FROM tb_monthly_$tahun_awal
            GROUP BY id) b on b.ind_name = a.nama_pilihan where type_pilihan = 'Arus Kas dari Aktivitas Investasi'  group by sub_kategori order by a.id asc");

        if($tgl_akhir < $tgl_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_aktivitas_operasi = 0;
            while($row = mysqli_fetch_array($sql)){
                $aktivitasoperasi = isset($row['total']) ? $row['total'] : 0;
                $subkategori = $row['sub_kategori'];

                if ($subkategori == 'Pembayaran Kepada Pemasok Lain-Lain') {
                    $aktivitas_operasi_ = $aktivitasoperasi;
                }else{
                    $aktivitas_operasi_ = $aktivitasoperasi;
                }

                if ($aktivitas_operasi_ > 0) {
                    $aktivitas_operasi = number_format($aktivitas_operasi_,2);
                }else{
                    $aktivitas_operasi = '('.number_format(abs($aktivitas_operasi_),2).')';
                }

                $total_aktivitas_operasi += $aktivitas_operasi_;
                if ($total_aktivitas_operasi > 0) {
                    $total_aktivitas_operasi_ = number_format($total_aktivitas_operasi,2);
                }else{
                    $total_aktivitas_operasi_ = '('.number_format(abs($total_aktivitas_operasi),2).')';
                }

                echo '<tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_operasi.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
                </tr>
                ';
            }
            echo '<tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_operasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from operating activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Investasi</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Investing Activities</th>
            </tr>';

            $total_aktivitas_investasi = 0;
            $bersih_kas_setarakas = 0;
            $bersih_kas_setarakas_ = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $aktivitas_investasi = isset($row2['total']) ? $row2['total'] : 0;
                if ($aktivitas_investasi > 0) {
                    $aktivitas_investasi = number_format($aktivitas_investasi,2);
                }else{
                    $aktivitas_investasi = '('.number_format(abs($aktivitas_investasi),2).')';
                }

                $total_aktivitas_investasi += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_aktivitas_investasi > 0) {
                    $total_aktivitas_investasi_ = number_format($total_aktivitas_investasi,2);
                }else{
                    $total_aktivitas_investasi_ = '('.number_format(abs($total_aktivitas_investasi),2).')';
                }

                echo '<tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_investasi.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas investasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_investasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from investing activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Pendanaan</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
            </tr>';
            ?>

            <tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan pinjaman</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php 
                    $sql_Penerimaan = mysqli_query($conn2,"WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

pivot AS (
  SELECT
  14 id,
    periode,
    SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN profit_center='NAG' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAK,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '14' ORDER BY a.id ASC),

data_fix as (SELECT a.periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       (penerimaan_NAG + b.total_nag) AS total_nag,
       (penerimaan_NAK + b.total_nak) AS total_nak,
       (penerimaan_TOTAL + b.total_all) AS total_all
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode
UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_NAG - b.total_nag) pembayaran_NAG,
       - (pembayaran_NAK - b.total_nak) pembayaran_NAK,
       - (pembayaran_TOTAL - b.total_all) pembayaran_TOTAL
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Penerimaan Pinjaman' AND periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')");

                    $row_penerimaan = mysqli_fetch_array($sql_Penerimaan);
                    $total_penerimaan = isset($row_penerimaan['total_all']) ? $row_penerimaan['total_all'] : 0;

                    $totalcf_17 = $total_penerimaan;
                    if ($totalcf_17 > 0) {
                        $total_17 = number_format($totalcf_17,2);
                    }else{
                        $total_17 = '('.number_format(abs($totalcf_17),2).')';
                    }
                    echo $total_17;
                    ?>
                </td>
                <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from loans</i></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran pinjaman</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php
                    $sql_Pembayaran = mysqli_query($conn2,"WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

pivot AS (
  SELECT
  15 id,
    periode,
    SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN profit_center='NAG' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAK,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '15' ORDER BY a.id ASC),

data_fix as (SELECT periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       penerimaan_NAG AS total_nag,
       penerimaan_NAK AS total_nak,
       penerimaan_TOTAL AS total_all
FROM pivot

UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_NAG - b.total_nag) pembayaran_NAG,
       - (pembayaran_NAK - b.total_nak) pembayaran_NAK,
       - (pembayaran_TOTAL - b.total_all) pembayaran_TOTAL
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Pembayaran Pinjaman' AND periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')");

                    $row_Pembayaran = mysqli_fetch_array($sql_Pembayaran);
                    $total_Pembayaran = isset($row_Pembayaran['total_all']) ? $row_Pembayaran['total_all'] : 0;

                    $sql_reval_Pembayaran = mysqli_query($conn2,"WITH
accounts AS (
  SELECT profit_center, no_coa, akun, SUM($kata_filter) AS saldo_awal
  FROM (
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'

    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

  ) AS a
  GROUP BY profit_center, no_coa, akun
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

reval AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center
  GROUP BY c.profit_center
),
revaluasi as (select '2' id, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg),

pembayaran as (select a.id, sub_kategori, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a where a.id = 2 ORDER BY a.id ASC)
          
          select sub_kategori, (total_all + revaluasi) total, revaluasi, sub_kategori_eng from pembayaran a INNER JOIN revaluasi b on b.id = a.id

                        ");

                    $row_reval_Pembayaran = mysqli_fetch_array($sql_reval_Pembayaran);
                    $total_reval_Pembayaran = isset($row_reval_Pembayaran['revaluasi']) ? $row_reval_Pembayaran['revaluasi'] : 0;

                    $totalcf_18 = $total_Pembayaran;
                    if ($totalcf_18 > 0) {
                        $total_18 = number_format($totalcf_18,2);
                    }else{
                        $total_18 = '('.number_format(abs($totalcf_18),2).')';
                    }
                    echo $total_18; 
                    ?>
                </td>
                <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment of loans</i></td>
            </tr>
            <?php
            $total_aktivitas_pendanaan_ = 0;
            $total_aktivitas_pendanaan = $totalcf_17 + $totalcf_18;
            if ($total_aktivitas_pendanaan > 0) {
                $total_aktivitas_pendanaan_ = number_format($total_aktivitas_pendanaan,2);
            }else{
                $total_aktivitas_pendanaan_ = '('.number_format(abs($total_aktivitas_pendanaan),2).')';
            }

            $bersih_kas_setarakas = $total_aktivitas_operasi + $total_aktivitas_investasi + $totalcf_17 + $totalcf_18;
            if ($bersih_kas_setarakas > 0) {
                $bersih_kas_setarakas_ = number_format($bersih_kas_setarakas,2);
            }else{
                $bersih_kas_setarakas_ = '('.number_format(abs($bersih_kas_setarakas),2).')';
            }

            echo '<tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas pendanaan</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_pendanaan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from financing activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">'.$bersih_kas_setarakas_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>';
            ?>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada awal periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php
                    $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
                        left join
                        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                        on coa.no_coa = saldo.nocoa
                        left join
                        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

                    $row = mysqli_fetch_array($sql);
                    $total = isset($row['total']) ? $row['total'] : 0;
                    if ($total > 0) {
                        $total_ = number_format($total,2);
                    }else{
                        $total_ = '('.number_format(abs($total),2).')';
                    }
                    echo $total_; 
                    ?>
                </th>
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the beginning of period</i></th>
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
            </tr>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada akhir periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php 
                    $totalcf_kas = $total + $bersih_kas_setarakas;
                    if ($totalcf_kas > 0) {
                        $total_jmlkas = number_format($totalcf_kas,2);
                    }else{
                        $total_jmlkas = '('.number_format(abs($totalcf_kas),2).')';
                    }
                    echo $total_jmlkas;
                    ?>
                </th>
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the end of period</i></th>
            </tr>
            <?php

            
        }
        ?>
        
    </table>

</body>
</html>




