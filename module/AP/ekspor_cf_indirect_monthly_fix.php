<html>
<head>
    <title>Export Data CF InDirect Monthly</title>
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
    .text{
        mso-number-format:"\@";/force text/
    }
    </style>

    <?php
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=cf-indirect-monthly.xls");

    function fmt_cf($v) {
        $v = isset($v) ? $v : 0;
        if ($v >= 0) {
            return number_format($v, 2);
        }
        return '(' . number_format(abs($v), 2) . ')';
    }

    function getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, $id_indirect) {
        $sql = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '$id_indirect'");
        $row = mysqli_fetch_array($sql);
        return isset($row['total']) ? $row['total'] : 0;
    }

    function computeCfIndirectMonth($conn2, $bulan, $tahun, $kata_filter) {
        $bulan_awal = $bulan;
        $bulan_akhir = $bulan;
        $tahun_awal = $tahun;
        $tahun_akhir = $tahun;

                $sql7 = mysqli_query($conn2,"select indname1,sum(credit_idr - debit_idr) total from (select * from 
(select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
on coa.no_coa = saldo.nocoa
left join
(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
LEFT JOIN
(select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a where a.indname1 = 'LAPORAN LABA RUGI'");

        $row7 = mysqli_fetch_array($sql7);
        $laba_rugi = isset($row7['total']) ? $row7['total'] : 0;

        $penyusutan = getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '19');
        $laba_ditahan = 0;

        $operating = array(
            array('label' => 'Penurunan (Kenaikan) Piutang Dagang', 'eng' => 'Decrease / (Increase) Trade Receivable', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '20')),
            array('label' => 'Penurunan (Kenaikan) Piutang Lainnya', 'eng' => 'Decrease / (Increase) Other Receivable', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '21')),
            array('label' => 'Penurunan (Kenaikan) Persediaan', 'eng' => 'Decrease / (Increase) Inventory', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '22')),
            array('label' => 'Penurunan (Kenaikan) Biaya Dibayar Dimuka', 'eng' => 'Decrease / (Increase) Prepaid Expense', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '23')),
            array('label' => 'Penurunan (Kenaikan) Aset Lain-Lain', 'eng' => 'Decrease / (Increase) Other Asset', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '24')),
            array('label' => 'Kenaikan (Penurunan) Utang Dagang', 'eng' => 'Decrease / (Increase) Trade Payable', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '25')),
            array('label' => 'Kenaikan (Penurunan) Utang Lainnya', 'eng' => 'Decrease / (Increase) Other Payable', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '26')),
        );
        $operating_total = 0;
        foreach ($operating as $row) {
            $operating_total += $row['total'];
        }

        $investing = array(
            array('label' => 'Pembelian aset tetap', 'eng' => 'Acquisition of Fixed Asset', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '27')),
            array('label' => 'Aset Dalam Penyelesaian', 'eng' => 'Asset In Construction', 'total' => getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '28')),
        );
        $investing_total = 0;
        foreach ($investing as $row) {
            $investing_total += $row['total'];
        }

        $total29_raw = getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '29');
        if ($kata_filter == 'Jan_2023') {
            $total29_display = 2910197113.18;
        } else {
            $total29_display = $total29_raw;
        }
        $total30 = getIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '30');

        $financing = array(
            array('label' => 'Kenaikan (Penurunan) Utang Bank', 'eng' => 'Increase / (Decrease) Bank Loan', 'total' => $total29_display),
            array('label' => 'Kenaikan (Penurunan) Utang Direksi & Afiliasi', 'eng' => 'Increase / (Decrease) Shareholder & Affiliated Payable', 'total' => $total30),
        );

        $totaljml6_raw = $total29_raw + $total30;
        if ($kata_filter == 'Jan_2023') {
            $financing_total = $totaljml6_raw - 49264896939.97;
        } else {
            $financing_total = $totaljml6_raw;
        }

        $net_change = $laba_rugi + $penyusutan + $laba_ditahan + $operating_total + $investing_total + $totaljml6_raw;
        if ($kata_filter == 'Jan_2023') {
            $net_change -= 49264896939.97;
        }

                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

        $rowb = mysqli_fetch_array($sql);
        $beginning_cash = isset($rowb['total']) ? $rowb['total'] : 0;
        $ending_cash = $beginning_cash + $net_change;

        return array(
            'laba_rugi' => $laba_rugi,
            'penyusutan' => $penyusutan,
            'laba_ditahan' => $laba_ditahan,
            'operating' => $operating,
            'operating_total' => $operating_total,
            'investing' => $investing,
            'investing_total' => $investing_total,
            'financing' => $financing,
            'financing_total' => $financing_total,
            'net_change' => $net_change,
            'beginning_cash' => $beginning_cash,
            'ending_cash' => $ending_cash,
        );
    }

    $start_ts = strtotime($_GET['start_date']);
    $end_ts = strtotime($_GET['end_date']);

    $cursor = mktime(0, 0, 0, date('n', $start_ts), 1, date('Y', $start_ts));
    $end_cursor = mktime(0, 0, 0, date('n', $end_ts), 1, date('Y', $end_ts));

    $months = array();
    while ($cursor <= $end_cursor) {
        $months[] = array(
            'bulan' => date('m', $cursor),
            'tahun' => date('Y', $cursor),
            'label' => date('M Y', $cursor),
            'kata_filter' => date('M', $cursor) . '_' . date('Y', $cursor),
        );
        $cursor = strtotime('+1 month', $cursor);
    }

    $end_date = date("d F Y", $end_ts);
    if (count($months) > 0) {
        $last_month = $months[count($months) - 1];
        $sql_end_date = mysqli_query($conn2, "select tgl_akhir from tbl_tgl_tb where bulan = '" . $last_month['bulan'] . "' and tahun = '" . $last_month['tahun'] . "'");
        $row_end_date = mysqli_fetch_array($sql_end_date);
        if (!empty($row_end_date['tgl_akhir'])) {
            $end_date = date("d F Y", strtotime($row_end_date['tgl_akhir']));
        }
    }

    function echo_cf_header_spacer($jml_bulan) {
        for ($i = 0; $i < $jml_bulan + 1; $i++) {
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';
        }
    }

    $monthly_data = array();
    foreach ($months as $m) {
        $monthly_data[] = computeCfIndirectMonth($conn2, $m['bulan'], $m['tahun'], $m['kata_filter']);
    }

    $jml_bulan = count($months);

    if ($jml_bulan == 0) {
        echo "<script type='text/javascript'>alert('Mohon Masukan Tanggal Filter Yang Benar');</script>";
        echo '</body></html>';
        exit;
    }

    function render_cf_indirect_row($label, $eng, $months, $monthly_data, $accessor) {
        echo '<tr><td style="text-align: left;vertical-align: middle;">' . $label . '</td>';
        $sum = 0;
        foreach ($monthly_data as $md) {
            $val = $accessor($md);
            $sum += $val;
            echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($val) . '</td>';
        }
        echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($sum) . '</td>';
        echo '<td style="text-align: right;vertical-align: middle;"><i>' . $eng . '</i></td></tr>';
        return $sum;
    }
    ?>

    <table style="font-size:15px;border-collapse:collapse;" >
        <colgroup>
            <col style="width:260px;">
            <?php for ($i = 0; $i < $jml_bulan; $i++) { echo '<col style="width:160px;">'; } ?>
            <col style="width:160px;">
            <col style="width:260px;">
        </colgroup>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PT NIRWANA ALABARE GARMENT</b></th>
            <?php echo_cf_header_spacer($jml_bulan); ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</b></th>
            <?php echo_cf_header_spacer($jml_bulan); ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF CASH FLOW - INDIRECT METHOD</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <?php echo_cf_header_spacer($jml_bulan); ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE PERIODS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <?php echo_cf_header_spacer($jml_bulan); ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr style="border-bottom: 1px solid black;">
            <th style="text-align: left;vertical-align: middle;"><b>Keterangan</b></th>
            <?php foreach ($months as $m) { echo '<th style="text-align: right;vertical-align: middle;mso-number-format:\'\@\';"><b>' . $m['label'] . '</b></th>'; } ?>
            <th style="text-align: right;vertical-align: middle;"><b>Total</b></th>
            <th style="text-align: right;vertical-align: middle;"><b><i>Description</i></b></th>
        </tr>

        <?php
        render_cf_indirect_row('Laba (Rugi) Bersih', 'Net Income (Loss)', $months, $monthly_data, function ($md) { return $md['laba_rugi']; });
        render_cf_indirect_row('Penyesuaian Akumulasi Penyusutan Aset Tetap', 'Accumulated Depreciation Of Fixed Asset Adjustment', $months, $monthly_data, function ($md) { return $md['penyusutan']; });
        render_cf_indirect_row('Penyesuaian Laba Ditahan Tahun Lalu', 'Previous Year Retained Earning Adjustment', $months, $monthly_data, function ($md) { return $md['laba_ditahan']; });
        echo '<tr><td colspan="' . ($jml_bulan + 3) . '">&nbsp;</td></tr>';
        ?>

        <tr>
            <th style="text-align: left;vertical-align: middle;" colspan="<?php echo $jml_bulan + 2; ?>"><b>Arus Kas dari Aktivitas Operasi</b></th>
            <th style="text-align: right;vertical-align: middle;"><b><i>Cash Flow from Operating Activities</i></b></th>
        </tr>
        <?php
        $n_operating = count($monthly_data[0]['operating']);
        $total_operasi_per_bulan = array_fill(0, $jml_bulan, 0);
        for ($r = 0; $r < $n_operating; $r++) {
            $label = $monthly_data[0]['operating'][$r]['label'];
            $eng = $monthly_data[0]['operating'][$r]['eng'];
            echo '<tr><td style="text-align: left;vertical-align: middle;">' . $label . '</td>';
            $sum = 0;
            foreach ($monthly_data as $idx => $md) {
                $val = $md['operating'][$r]['total'];
                $sum += $val;
                $total_operasi_per_bulan[$idx] += $val;
                echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($val) . '</td>';
            }
            echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($sum) . '</td>';
            echo '<td style="text-align: right;vertical-align: middle;"><i>' . $eng . '</i></td></tr>';
        }
        $total_operasi_sum = array_sum($total_operasi_per_bulan);
        echo '<tr style="line-height: 30px;"><th style="text-align: left;vertical-align: middle;border-top:2px solid #000000;">Arus kas yang digunakan untuk aktivitas operasi</th>';
        foreach ($total_operasi_per_bulan as $v) {
            echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($v) . '</th>';
        }
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($total_operasi_sum) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;"><i>Cash flow used from operating activities</i></th></tr>';

        echo '<tr><td colspan="' . ($jml_bulan + 3) . '">&nbsp;</td></tr>';
        ?>

        <tr>
            <th style="text-align: left;vertical-align: middle;" colspan="<?php echo $jml_bulan + 2; ?>"><b>Arus Kas dari Aktivitas Investasi</b></th>
            <th style="text-align: right;vertical-align: middle;"><b><i>Cash Flow from Investing Activities</i></b></th>
        </tr>
        <?php
        $n_investing = count($monthly_data[0]['investing']);
        $total_investasi_per_bulan = array_fill(0, $jml_bulan, 0);
        for ($r = 0; $r < $n_investing; $r++) {
            $label = $monthly_data[0]['investing'][$r]['label'];
            $eng = $monthly_data[0]['investing'][$r]['eng'];
            echo '<tr><td style="text-align: left;vertical-align: middle;">' . $label . '</td>';
            $sum = 0;
            foreach ($monthly_data as $idx => $md) {
                $val = $md['investing'][$r]['total'];
                $sum += $val;
                $total_investasi_per_bulan[$idx] += $val;
                echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($val) . '</td>';
            }
            echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($sum) . '</td>';
            echo '<td style="text-align: right;vertical-align: middle;"><i>' . $eng . '</i></td></tr>';
        }
        $total_investasi_sum = array_sum($total_investasi_per_bulan);
        echo '<tr style="line-height: 30px;"><th style="text-align: left;vertical-align: middle;border-top:2px solid #000000;">Arus kas yang diperoleh dari aktivitas investasi</th>';
        foreach ($total_investasi_per_bulan as $v) {
            echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($v) . '</th>';
        }
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($total_investasi_sum) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;"><i>Cash flow generated from investing activities</i></th></tr>';

        echo '<tr><td colspan="' . ($jml_bulan + 3) . '">&nbsp;</td></tr>';
        ?>

        <tr>
            <th style="text-align: left;vertical-align: middle;" colspan="<?php echo $jml_bulan + 2; ?>"><b>Arus Kas dari Aktivitas Pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;"><b><i>Cash Flow from Financing Activities</i></b></th>
        </tr>
        <?php
        $n_financing = count($monthly_data[0]['financing']);
        $total_pendanaan_per_bulan = array();
        for ($r = 0; $r < $n_financing; $r++) {
            $label = $monthly_data[0]['financing'][$r]['label'];
            $eng = $monthly_data[0]['financing'][$r]['eng'];
            echo '<tr><td style="text-align: left;vertical-align: middle;">' . $label . '</td>';
            $sum = 0;
            foreach ($monthly_data as $md) {
                $val = $md['financing'][$r]['total'];
                $sum += $val;
                echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($val) . '</td>';
            }
            echo '<td style="text-align: right;vertical-align: middle;">' . fmt_cf($sum) . '</td>';
            echo '<td style="text-align: right;vertical-align: middle;"><i>' . $eng . '</i></td></tr>';
        }

        $total_pendanaan_per_bulan = array();
        $total_pendanaan_sum = 0;
        foreach ($monthly_data as $md) {
            $total_pendanaan_per_bulan[] = $md['financing_total'];
            $total_pendanaan_sum += $md['financing_total'];
        }
        echo '<tr style="line-height: 30px;"><th style="text-align: left;vertical-align: middle;border-top:2px solid #000000;">Arus kas yang diperoleh dari aktivitas pendanaan</th>';
        foreach ($total_pendanaan_per_bulan as $v) {
            echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($v) . '</th>';
        }
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;">' . fmt_cf($total_pendanaan_sum) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;border-top:2px solid #000000;"><i>Cash flow generated from financing activities</i></th></tr>';

        echo '<tr><td colspan="' . ($jml_bulan + 3) . '">&nbsp;</td></tr>';

        $net_change_sum = 0;
        echo '<tr style="line-height: 30px;"><th style="text-align: left;vertical-align: middle;">Kenaikan / (Penurunan) bersih kas dan setara kas</th>';
        foreach ($monthly_data as $md) {
            $net_change_sum += $md['net_change'];
            echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($md['net_change']) . '</th>';
        }
        echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($net_change_sum) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;"><i>Net Increase / (Decrease) in cash and cash equivalent</i></th></tr>';

        echo '<tr><td colspan="' . ($jml_bulan + 3) . '">&nbsp;</td></tr>';

        echo '<tr><th style="text-align: left;vertical-align: middle;">Kas dan setara kas pada awal periode</th>';
        foreach ($monthly_data as $md) {
            echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($md['beginning_cash']) . '</th>';
        }
        $first_beginning = $jml_bulan > 0 ? $monthly_data[0]['beginning_cash'] : 0;
        echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($first_beginning) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;"><i>Cash and cash equivalent at the beginning of period</i></th></tr>';

        echo '<tr><th style="text-align: left;vertical-align: middle;">Kas dan setara kas pada akhir periode</th>';
        foreach ($monthly_data as $md) {
            echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($md['ending_cash']) . '</th>';
        }
        $last_ending = $jml_bulan > 0 ? $monthly_data[$jml_bulan - 1]['ending_cash'] : 0;
        echo '<th style="text-align: right;vertical-align: middle;">' . fmt_cf($last_ending) . '</th>';
        echo '<th style="text-align: right;vertical-align: middle;"><i>Cash and cash equivalent at the end of period</i></th></tr>';
        ?>
    </table>

</body>
</html>
