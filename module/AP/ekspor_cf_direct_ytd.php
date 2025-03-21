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
        <?php
        $sql = mysqli_query($conn2,"select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, total from (select * from tb_master_pilihan where status = 'Y') a inner join (SELECT 
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
            GROUP BY id) b on b.ind_name = a.nama_pilihan where type_pilihan = 'Arus Kas dari Aktivitas Operasi' order by a.id asc");

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
            GROUP BY id) b on b.ind_name = a.nama_pilihan where type_pilihan = 'Arus Kas dari Aktivitas Investasi' order by a.id asc");

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
                </tr>';
            }
            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_operasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from operating activities</th> 
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

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas investasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_investasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from investing activities</th> 
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
                    $sql_Penerimaan = mysqli_query($conn2,"select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, total from (select * from tb_master_pilihan where status = 'Y') a inner join (SELECT 
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
                        FROM (select '1' id,'Penerimaan pinjaman' ind_name,
                        SUM(CASE WHEN periode = 'saldo_jan' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_jan,
                        SUM(CASE WHEN periode = 'saldo_feb' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_feb,
                        SUM(CASE WHEN periode = 'saldo_mar' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_mar,
                        SUM(CASE WHEN periode = 'saldo_apr' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_apr,
                        SUM(CASE WHEN periode = 'saldo_may' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_may,
                        SUM(CASE WHEN periode = 'saldo_jun' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_jun,
                        SUM(CASE WHEN periode = 'saldo_jul' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_jul,
                        SUM(CASE WHEN periode = 'saldo_aug' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_aug,
                        SUM(CASE WHEN periode = 'saldo_sep' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_sep,
                        SUM(CASE WHEN periode = 'saldo_oct' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_oct,
                        SUM(CASE WHEN periode = 'saldo_nov' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_nov,
                        SUM(CASE WHEN periode = 'saldo_dec' THEN penerimaan_pinjaman ELSE 0 END) AS saldo_dec
                        FROM act_tb_pinjaman_$tahun_awal) a
                        GROUP BY id) b on b.ind_name = a.nama_pilihan where nama_pilihan = 'Penerimaan pinjaman' order by a.id asc");

                    $row_penerimaan = mysqli_fetch_array($sql_Penerimaan);
                    $total_penerimaan = isset($row_penerimaan['total']) ? $row_penerimaan['total'] : 0;

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
                    $sql_Pembayaran = mysqli_query($conn2,"select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, total from (select * from tb_master_pilihan where status = 'Y') a inner join (SELECT 
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
                        FROM (select '1' id,'Pembayaran pinjaman' ind_name, 
                        - SUM(CASE WHEN periode = 'saldo_jan' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_jan,
                        - SUM(CASE WHEN periode = 'saldo_feb' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_feb,
                        - SUM(CASE WHEN periode = 'saldo_mar' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_mar,
                        - SUM(CASE WHEN periode = 'saldo_apr' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_apr,
                        - SUM(CASE WHEN periode = 'saldo_may' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_may,
                        - SUM(CASE WHEN periode = 'saldo_jun' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_jun,
                        - SUM(CASE WHEN periode = 'saldo_jul' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_jul,
                        - SUM(CASE WHEN periode = 'saldo_aug' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_aug,
                        - SUM(CASE WHEN periode = 'saldo_sep' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_sep,
                        - SUM(CASE WHEN periode = 'saldo_oct' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_oct,
                        - SUM(CASE WHEN periode = 'saldo_nov' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_nov,
                        - SUM(CASE WHEN periode = 'saldo_dec' THEN pembayaran_pinjaman ELSE 0 END) AS saldo_dec
                        FROM act_tb_pinjaman_$tahun_awal) a
                        GROUP BY id) b on b.ind_name = a.nama_pilihan where nama_pilihan = 'Pembayaran pinjaman' order by a.id asc

                        ");

                    $row_Pembayaran = mysqli_fetch_array($sql_Pembayaran);
                    $total_Pembayaran = isset($row_Pembayaran['total']) ? $row_Pembayaran['total'] : 0;

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
            if ($total_aktivitas_pendanaan_ > 0) {
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

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas pendanaan</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_pendanaan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from financing activities</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">'.$bersih_kas_setarakas_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
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




