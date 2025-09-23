<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

$dataArr = json_decode($_POST['data'], true); // ambil array dari JS

if (!empty($dataArr)) {

    // ambil no_kbon dari array pertama (semua sama)
    $no_kbon = $dataArr[0]['no_kbon'];

    // hapus dulu data lama untuk no_kbon
    mysqli_query($conn2,"DELETE FROM ap_edit_kontrabon_ftr WHERE no_kbon = '$no_kbon' ");
    mysqli_query($conn2,"DELETE FROM ap_journal_temp WHERE no_journal = '$no_kbon' and reff_doc Like '%FTR%'");

    foreach ($dataArr as $row) {
        $no_kbon       = $row['no_kbon'];
        $tgl_kbon      = date("Y-m-d", strtotime($row['tgl_kbon']));
        $profit_center = $row['pc_kbon'];
        $nama_supp     = $row['nama_supp'];
        $invoice       = $row['invoice'];
        $faktur        = $row['faktur'];
        $tgl_inv       = date("Y-m-d", strtotime($row['tglsi']));
        $tgl_tempo     = date("Y-m-d", strtotime($row['tgltempo']));
        $create_user   = $row['create_user'];

        $no_ftr        = $row['no_ftr'];
        $no_po_ftr         = $row['no_po_ftr'];
        $tgl_po_ftr       = date("Y-m-d", strtotime($row['tgl_po_ftr']));
        $no_pi_ftr         = $row['no_pi_ftr'];
        $ttl_ftr           = $row['ttl_ftr'];
        $curr_ftr       = $row['curr_ftr'];
        $kbon_ftr      = $row['kbon_ftr'];
        $tglkbon_ftr       = date("Y-m-d", strtotime($row['tglkbon_ftr']));
        $lp_ftr = $row['lp_ftr'];
        $tgllp_ftr       = date("Y-m-d", strtotime($row['tgllp_ftr']));
        $pv_ftr       = $row['pv_ftr'];
        $bankout_ftr      = $row['bankout_ftr'];
        $bankoutdate_ftr       = date("Y-m-d", strtotime($row['bankoutdate_ftr']));
        $coa_ftr = $row['coa_ftr'];
        
        $status        = 'draft';
        $status_int    = 2;
        $create_date   = date("Y-m-d H:i:s");
        $post_date     = date("Y-m-d H:i:s");
        $update_date   = date("Y-m-d H:i:s");
        $start_date    = date("Y-m-d", strtotime($row['start_date']));
        $end_date      = date("Y-m-d", strtotime($row['end_date']));
        $keter         = "KONTRABON $nama_supp";


        // insert detail
        $query_ftr = "INSERT INTO ap_edit_kontrabon_ftr (no_kbon, tgl_kbon, nama_supp, no_ftr, no_po, tgl_po,no_pi, curr, total_ftr, no_pv, no_bankout, tgl_bankout, no_coa, status, created_by, created_date) 
        VALUES 
        ('$no_kbon', '$tgl_kbon', '$nama_supp', '$no_ftr', '$no_po_ftr', '$tgl_po_ftr', '$no_pi_ftr', '$curr_ftr', '$ttl_ftr', '$pv_ftr', '$bankout_ftr', '$bankoutdate_ftr', '$coa_ftr', '$status', '$create_user', '$create_date')";

        $execute_ftr = mysqli_query($conn2,$query_ftr);

        $sqlcoa_ftr = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '$coa_ftr' Limit 1");
        $rowcoa_ftr = mysqli_fetch_array($sqlcoa_ftr);
        $no_coa_ftr = $rowcoa_ftr['no_coa'];
        $nama_coa_ftr = $rowcoa_ftr['nama_coa'];

        // ambil kurs (rate)
        if ($curr != 'IDR') {
            $sqlx = mysqli_query($conn1,"SELECT ROUND(rate,2) as rate FROM masterrate WHERE tanggal = '$tgl_bpb' and v_codecurr = 'PAJAK'");
            $rowx = mysqli_fetch_array($sqlx);
            $h_rate = isset($rowx['rate']) ? $rowx['rate'] : 0;
            if ($h_rate == 0) {
                $sqly = mysqli_query($conn1,"SELECT ROUND(rate,2) as rate FROM masterrate WHERE id = (SELECT max(id) FROM masterrate WHERE v_codecurr = 'PAJAK')");
                $rowy = mysqli_fetch_array($sqly);
                $rate = $rowy['rate'];
            } else {
                $rate = $h_rate;
            }
        } else {
            $rate = 1;
        }

        $ttl_ftr_idr = $ttl_ftr * $rate_ftr;


        $jurnal_ftr = "INSERT INTO ap_journal_temp (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
        VALUES 
        ('$no_kbon', '$create_date', 'AP - Kontrabon', '$no_coa_ftr', '$nama_coa_ftr', '-', '-', '$no_ftr', '', '-', '-', '$curr_ftr', '$rate_ftr', '0', '$ttl_ftr', '0', '$ttl_ftr_idr', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

        $execute_jurnal_ftr = mysqli_query($conn2,$jurnal_ftr);
        
    }
}

mysqli_close($conn2);
?>
