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
    mysqli_query($conn2,"DELETE FROM ap_edit_return_kb WHERE no_kbon = '$no_kbon' ");
    mysqli_query($conn2,"DELETE FROM ap_journal_temp WHERE no_journal = '$no_kbon' and (reff_doc Like '%RO%' OR reff_doc Like '%OUT%')");

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

        $no_ro        = $row['no_ro'];
        $no_bppb         = $row['no_bppb'];
        $tgl_bppb       = date("Y-m-d", strtotime($row['tgl_bppb']));
        $ttl_ro         = $row['ttl_ro'];
        $curr           = $row['curr'];
        $mattype       = $row['mattype'];
        $matclass      = $row['matclass'];
        $n_code_category = $row['n_code_category'];
        $cus_ctg       = $row['cus_ctg'];
        
        $status        = 'draft';
        $status_int    = 2;
        $create_date   = date("Y-m-d H:i:s");
        $post_date     = date("Y-m-d H:i:s");
        $update_date   = date("Y-m-d H:i:s");
        $start_date    = date("Y-m-d", strtotime($row['start_date']));
        $end_date      = date("Y-m-d", strtotime($row['end_date']));
        $keter         = "KONTRABON $nama_supp";

        // insert detail
        $queryess = "INSERT INTO ap_edit_return_kb (no_kbon, no_ro, no_bpbrtn, total_ro, status) 
        VALUES 
        ('$no_kbon', '$no_ro', '$no_bppb', '$ttl_ro', '$status')";
        $executeess = mysqli_query($conn2,$queryess);

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

        $idr_ttl_ro = $ttl_ro * $rate;

        $sqlbppb = mysqli_query($conn1,"select tgl_bppb,tax,round(sum((qty*price) * tax / 100),2) tax_ro from bppb_new where no_bppb = '$no_bppb' GROUP BY no_bppb");
        $rowbppb = mysqli_fetch_array($sqlbppb);
        $tgl_bppb = isset($rowbppb['tgl_bppb']) ? $rowbppb['tgl_bppb'] : 0;
        $val_tax_ro = isset($rowbppb['tax']) ? $rowbppb['tax'] : 0;
        $tax_ro = isset($rowbppb['tax_ro']) ? $rowbppb['tax_ro'] : 0;
        $idr_tax_ro = $tax_ro * $rate;



        $sqlcoa = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%$cus_ctg%' and mattype like '%$mattype%' and matclass like '%$matclass%' and n_code_category like '%$n_code_category%' and inv_type like '%bpb_credit%' Limit 1");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $no_coa_deb = $rowcoa['no_coa'];
        $nama_coa_deb = $rowcoa['nama_coa'];


        $queryss5 = "INSERT INTO ap_journal_temp (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
        VALUES 
        ('$no_kbon', '$create_date', 'AP - Kontrabon', '$no_coa_deb', '$nama_coa_deb', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '0', '$ttl_ro', '0', '$idr_ttl_ro', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

        $executess5 = mysqli_query($conn2,$queryss5);

        // insert jurnal PPN jika ada
       if ($val_tax_ro > 0) {

            $queryss6 = "INSERT INTO ap_journal_temp (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES 
            ('$no_kbon', '$create_date', 'AP - Kontrabon', '1.52.07', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '$tax_ro', '0', '$idr_tax_ro', '0', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

            $executess6 = mysqli_query($conn2,$queryss6);

            $queryss7 = "INSERT INTO ap_journal_temp (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
            VALUES 
            ('$no_kbon', '$create_date', 'AP - Kontrabon', '1.52.04', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '0', '$tax_ro', '0', '$idr_tax_ro', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

            $executess7 = mysqli_query($conn2,$queryss7);

        }
        
    }
}

mysqli_close($conn2);
?>
