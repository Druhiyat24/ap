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

    $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
    $rowakhir = mysqli_fetch_array($sqlakhir);
    $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
    $end_date = date("d F Y",strtotime($tgl_akhir));

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
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Pelanggan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_1 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '1'");

                $row_debit_1 = mysqli_fetch_array($sql_debit_1);
                $total_debit_1 = isset($row_debit_1['total']) ? $row_debit_1['total'] : 0;

                $sql_credit_1 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '1'");

                $row_credit_1 = mysqli_fetch_array($sql_credit_1);
                $total_credit_1 = isset($row_credit_1['total']) ? $row_credit_1['total'] : 0;
                // $totalcf_1 = $total_debit_1 - $total_credit_1;
                $totalcf_1 = $total_credit_1 - $total_debit_1;
                $total_1 = number_format($totalcf_1,2);
                echo $total_1;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Customer</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Pemasok</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                 $sql_debit_2 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '2'");

                $row_debit_2 = mysqli_fetch_array($sql_debit_2);
                $total_debit_2 = isset($row_debit_2['total']) ? $row_debit_2['total'] : 0;

                $sql_credit_2 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '2'");

                $row_credit_2 = mysqli_fetch_array($sql_credit_2);
                $total_credit_2 = isset($row_credit_2['total']) ? $row_credit_2['total'] : 0;
                // $totalcf_2 = $total_debit_2 - $total_credit_2;
                $totalcf_2 = $total_credit_2 - $total_debit_2;
                $total_2 = number_format($totalcf_2,2);
                echo $total_2;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Supplier</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Pemasok Lain-Lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_3 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '3'");

                $row_debit_3 = mysqli_fetch_array($sql_debit_3);
                $total_debit_3 = isset($row_debit_3['total']) ? $row_debit_3['total'] : 0;

                $sql_credit_3 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '3'");

                $row_credit_3 = mysqli_fetch_array($sql_credit_3);
                $total_credit_3 = isset($row_credit_3['total']) ? $row_credit_3['total'] : 0;
                // $totalcf_3 = $total_debit_3 - $total_credit_3;
                $totalcf_3 = $total_credit_3 - $total_debit_3;
                $total_3 = number_format($totalcf_3,2);
                echo $total_3;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment For Other Vendors</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Karyawan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_4 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '4'");

                $row_debit_4 = mysqli_fetch_array($sql_debit_4);
                $total_debit_4 = isset($row_debit_4['total']) ? $row_debit_4['total'] : 0;

                $sql_credit_4 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '4'");

                $row_credit_4 = mysqli_fetch_array($sql_credit_4);
                $total_credit_4 = isset($row_credit_4['total']) ? $row_credit_4['total'] : 0;
                // $totalcf_4 = $total_debit_4 - $total_credit_4;
                $totalcf_4 = $total_credit_4 - $total_debit_4;
                $total_4 = number_format($totalcf_4,2);
                echo $total_4;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Employees</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Karyawan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_9 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '9'");

                $row_debit_9 = mysqli_fetch_array($sql_debit_9);
                $total_debit_9 = isset($row_debit_9['total']) ? $row_debit_9['total'] : 0;

                $sql_credit_9 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '9'");

                $row_credit_9 = mysqli_fetch_array($sql_credit_9);
                $total_credit_9 = isset($row_credit_9['total']) ? $row_credit_9['total'] : 0;
                // $totalcf_9 = $total_debit_9 - $total_credit_9;
                $totalcf_9 = $total_credit_9 - $total_debit_9;
                $total_9 = number_format($totalcf_9,2);
                echo $total_9; 
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Employees</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Ke Pemerintah Bukan Pajak</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_6 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '6'");

                $row_debit_6 = mysqli_fetch_array($sql_debit_6);
                $total_debit_6 = isset($row_debit_6['total']) ? $row_debit_6['total'] : 0;

                $sql_credit_6 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '6'");

                $row_credit_6 = mysqli_fetch_array($sql_credit_6);
                $total_credit_6 = isset($row_credit_6['total']) ? $row_credit_6['total'] : 0;
                // $totalcf_6 = $total_debit_6 - $total_credit_6;
                $totalcf_6 = $total_credit_6 - $total_debit_6;
                $total_6 = number_format($totalcf_6,2);
                echo $total_6;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Government Non Tax</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Pajak</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_5 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '5'");

                $row_debit_5 = mysqli_fetch_array($sql_debit_5);
                $total_debit_5 = isset($row_debit_5['total']) ? $row_debit_5['total'] : 0;

                $sql_credit_5 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '5'");

                $row_credit_5 = mysqli_fetch_array($sql_credit_5);
                $total_credit_5 = isset($row_credit_5['total']) ? $row_credit_5['total'] : 0;
                // $totalcf_5 = $total_debit_5 - $total_credit_5;
                $totalcf_5 = $total_credit_5 - $total_debit_5;
                $total_5 = number_format($totalcf_5,2);
                echo $total_5;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Tax Payment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Bunga</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_8 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '8'");

                $row_debit_8 = mysqli_fetch_array($sql_debit_8);
                $total_debit_8 = isset($row_debit_8['total']) ? $row_debit_8['total'] : 0;

                $sql_credit_8 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '8'");

                $row_credit_8 = mysqli_fetch_array($sql_credit_8);
                $total_credit_8 = isset($row_credit_8['total']) ? $row_credit_8['total'] : 0;
                // $totalcf_8 = $total_debit_8 - $total_credit_8;
                $totalcf_8 = $total_credit_8 - $total_debit_8;
                $total_8 = number_format($totalcf_8,2);
                echo $total_8;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment For Interest</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Bunga Bank</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_7 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '7'");

                $row_debit_7 = mysqli_fetch_array($sql_debit_7);
                $total_debit_7 = isset($row_debit_7['total']) ? $row_debit_7['total'] : 0;

                $sql_credit_7 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '7'");

                $row_credit_7 = mysqli_fetch_array($sql_credit_7);
                $total_credit_7 = isset($row_credit_7['total']) ? $row_credit_7['total'] : 0;
                // $totalcf_7 = $total_debit_7 - $total_credit_7;
                $totalcf_7 = $total_credit_7 - $total_debit_7;
                $total_7 = number_format($totalcf_7,2);
                echo $total_7;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Bank Interest</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang digunakan untuk aktivitas operasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_jml1 = mysqli_query($conn2,"select id_direct_debit,ind_name, sum(total) total from (select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit IN ('1','2','3','4','5','6','7','8','9')) a");

                $row_debit_jml1 = mysqli_fetch_array($sql_debit_jml1);
                $total_debit_jml1 = isset($row_debit_jml1['total']) ? $row_debit_jml1['total'] : 0;

                $sql_credit_jml1 = mysqli_query($conn2,"select id_direct_credit,ind_name, sum(total) total from (select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit IN ('1','2','3','4','5','6','7','8','9')) a");

                $row_credit_jml1 = mysqli_fetch_array($sql_credit_jml1);
                $total_credit_jml1 = isset($row_credit_jml1['total']) ? $row_credit_jml1['total'] : 0;
                // $totalcf_jml1 = $total_debit_jml1 - $total_credit_jml1;
                $totalcf_jml1 = $total_credit_jml1 - $total_debit_jml1;
                $total_jml1 = number_format($totalcf_jml1,2);
                echo $total_jml1;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash flow used from operating activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Investing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembelian aset tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_11 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '11'");

                $row_debit_11 = mysqli_fetch_array($sql_debit_11);
                $total_debit_11 = isset($row_debit_11['total']) ? $row_debit_11['total'] : 0;

                $sql_credit_11 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '11'");

                $row_credit_11 = mysqli_fetch_array($sql_credit_11);
                $total_credit_11 = isset($row_credit_11['total']) ? $row_credit_11['total'] : 0;
                // $totalcf_11 = $total_debit_11 - $total_credit_11;
                $totalcf_11 = $total_credit_11 - $total_debit_11;
                $total_11 = number_format($totalcf_11,2);
                echo $total_11;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Acquisition of Fixed Asset</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan dari penjualan aset tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_12 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '12'");

                $row_debit_12 = mysqli_fetch_array($sql_debit_12);
                $total_debit_12 = isset($row_debit_12['total']) ? $row_debit_12['total'] : 0;

                $sql_credit_12 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '12'");

                $row_credit_12 = mysqli_fetch_array($sql_credit_12);
                $total_credit_12 = isset($row_credit_12['total']) ? $row_credit_12['total'] : 0;
                // $totalcf_12 = $total_debit_12 - $total_credit_12;
                $totalcf_12 = $total_credit_12 - $total_debit_12;
                $total_12 = number_format($totalcf_12,2);
                echo $total_12;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from sale of fixed assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penambahan investasi pada instrumen keuangan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $totalcf_inves1 = 0;
                $total_inves1 = number_format($totalcf_inves1,2);
                echo $total_inves1;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Addition of investment in financial instrument</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan investasi pada instrumen keuangan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $totalcf_inves2 = 0;
                $total_inves2 = number_format($totalcf_inves2,2);
                echo $total_inves2;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Sale of investment in financial instrument</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang digunakan untuk aktivitas investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_jml2 = mysqli_query($conn2,"select id_direct_debit,ind_name, sum(total) total from (select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit IN ('11','12')) a");

                $row_debit_jml2 = mysqli_fetch_array($sql_debit_jml2);
                $total_debit_jml2 = isset($row_debit_jml2['total']) ? $row_debit_jml2['total'] : 0;

                $sql_credit_jml2 = mysqli_query($conn2,"select id_direct_credit,ind_name, sum(total) total from (select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit IN ('11','12')) a");

                $row_credit_jml2 = mysqli_fetch_array($sql_credit_jml2);
                $total_credit_jml2 = isset($row_credit_jml2['total']) ? $row_credit_jml2['total'] : 0;
                // $totalcf_jml2 = $total_debit_jml2 - $total_credit_jml2;
                $totalcf_jml2 = $total_credit_jml2 - $total_debit_jml2;
                $total_jml2 = number_format($totalcf_jml2,2);
                echo $total_jml2;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  used from investing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <!-- -->

         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Financing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan pinjaman</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql_debit_17 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '17'");

                $row_debit_17 = mysqli_fetch_array($sql_debit_17);
                $total_debit_17 = isset($row_debit_17['total']) ? $row_debit_17['total'] : 0;

                $sql_credit_17 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '17'");

                $row_credit_17 = mysqli_fetch_array($sql_credit_17);
                $total_credit_17 = isset($row_credit_17['total']) ? $row_credit_17['total'] : 0;

                $sql_sa_bank = mysqli_query($conn2,"select sum(saldo) total from (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) a");

                $row_sa_bank = mysqli_fetch_array($sql_sa_bank);
                $total_sa_bank = isset($row_sa_bank['total']) ? $row_sa_bank['total'] : 0;
                // $totalcf_17 = $total_debit_17 - $total_credit_17;
                $totalcf_17 = $total_credit_17 - $total_debit_17 - $total_sa_bank;
                $total_17 = number_format($totalcf_17,2);
                echo $total_17;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran pinjaman</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_18 = mysqli_query($conn2,"select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit = '18'");

                $row_debit_18 = mysqli_fetch_array($sql_debit_18);
                $total_debit_18 = isset($row_debit_18['total']) ? $row_debit_18['total'] : 0;

                $sql_credit_18 = mysqli_query($conn2,"select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit = '18'");

                $row_credit_18 = mysqli_fetch_array($sql_credit_18);
                $total_credit_18 = isset($row_credit_18['total']) ? $row_credit_18['total'] : 0;

                $sql_sk_bank = mysqli_query($conn2,"select sum(saldo_akhir) total from (select no_coa,namacoa,round((saldo + debit_idr) - credit_idr,2) saldo_akhir from 
(select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
on coa.no_coa = saldo.nocoa
left join
(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
LEFT JOIN
(select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = coa.no_coa where no_coa IN ('1.10.01','1.10.02') and round((saldo + debit_idr) - credit_idr,2) > 0 order by no_coa asc) a");

                $row_sk_bank = mysqli_fetch_array($sql_sk_bank);
                $total_sk_bank = isset($row_sk_bank['total']) ? $row_sk_bank['total'] : 0;

                // $totalcf_18 = $total_debit_18 - $total_credit_18;
                $totalcf_18 = $total_credit_18 - $total_debit_18 + $total_sk_bank;
                $total_18 = number_format($totalcf_18,2);
                echo $total_18; 
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment of loans</i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang diperoleh dari aktivitas pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql_debit_jml3 = mysqli_query($conn2,"select id_direct_debit,ind_name, sum(total) total from (select * from(select id_direct_debit,ind_name, sum(debit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a where a.id_direct_debit IN ('17','18')) a");

                $row_debit_jml3 = mysqli_fetch_array($sql_debit_jml3);
                $total_debit_jml3 = isset($row_debit_jml3['total']) ? $row_debit_jml3['total'] : 0;

                $sql_credit_jml3 = mysqli_query($conn2,"select id_direct_credit,ind_name, sum(total) total from (select * from(select id_direct_credit,ind_name, sum(credit_idr) total from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
                    (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a where a.id_direct_credit IN ('17','18')) a");

                $row_credit_jml3 = mysqli_fetch_array($sql_credit_jml3);
                $total_credit_jml3 = isset($row_credit_jml3['total']) ? $row_credit_jml3['total'] : 0;
                // $totalcf_jml3 = $total_debit_jml3 - $total_credit_jml3;
                $totalcf_jml3 = $total_credit_jml3 - $total_debit_jml3 - $total_sa_bank + $total_sk_bank;
                $total_jml3 = number_format($totalcf_jml3,2);
                echo $total_jml3; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  generated from financing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kenaikan / (Penurunan) bersih kas dan setara kas</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $totalcf_direct = $totalcf_jml1 + $totalcf_jml2 + $totalcf_jml3;
                $total_jmldir = number_format($totalcf_direct,2);
                echo $total_jmldir; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Net Increase / (Decrease) in cash and cash equivalent</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada awal periode</b></th>
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
                $total_ = number_format($total,2);
                echo $total_; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the beginning of period</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada akhir periode</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $totalcf_kas = $total + $totalcf_direct;
                $total_jmlkas = number_format($totalcf_kas,2);
                echo $total_jmlkas;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the end of period</i></b></th>
        </tr>
        
    </table>

</body>
</html>




