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
    mysqli_query($conn2,"DELETE FROM ap_edit_kontrabon WHERE no_kbon = '$no_kbon' ");
    mysqli_query($conn2,"DELETE FROM ap_journal_temp WHERE no_journal = '$no_kbon' and (reff_doc Like '%/IN%' OR reff_doc Like '%/RI%')");

    foreach ($dataArr as $row) {
        $no_kbon       = $row['no_kbon'];
        $tgl_kbon      = date("Y-m-d", strtotime($row['tgl_kbon']));
        $profit_center = $row['pc_kbon'];
        $nama_supp     = $row['nama_supp'];
        $invoice       = $row['invoice'];
        $faktur        = $row['faktur'];
        $create_user   = $row['create_user'];
        $no_bpb        = $row['no_bpb'];
        $no_po         = $row['no_po'];
        $tgl_bpb       = date("Y-m-d", strtotime($row['tgl_bpb']));
        $price         = $row['price'];
        $tax           = $row['tax'];
        $sum_dp        = $row['cash'];
        $tgl_po        = date("Y-m-d", strtotime($row['tgl_po']));
        $pph           = $row['pph'];
        $idtax         = $row['idtax'];
        $mattype       = $row['mattype'];
        $matclass      = $row['matclass'];
        $n_code_category = $row['n_code_category'];
        $cus_ctg       = $row['cus_ctg'];
        $sum_sub       = $row['sum_sub'];
        $sum_tax       = $row['sum_tax'];
        $sum_pph       = $row['sum_pph'];
        $sum_total     = $row['sum_total'];
        $sum_dpp       = $sum_sub + $sum_tax;
        $tgl_inv       = date("Y-m-d", strtotime($row['tglsi']));
        $tgl_tempo     = date("Y-m-d", strtotime($row['tgltempo']));
        $curr          = $row['curr_bpb'];
        $status        = 'draft';
        $status_int    = 2;
        $create_date   = date("Y-m-d H:i:s");
        $post_date     = date("Y-m-d H:i:s");
        $update_date   = date("Y-m-d H:i:s");
        $start_date    = date("Y-m-d", strtotime($row['start_date']));
        $end_date      = date("Y-m-d", strtotime($row['end_date']));
        $keter         = "KONTRABON $nama_supp";

        // insert detail ke ap_edit_kontrabon
        $query = "INSERT INTO ap_edit_kontrabon 
        (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb, tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, dp_value, curr, ceklist, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date) 
        VALUES 
        ('$no_kbon', '$tgl_kbon', '0', '$nama_supp', '$faktur', '$no_bpb', '$no_po', '$tgl_bpb', '$tgl_po', '$invoice', '$tgl_inv', '$tgl_tempo', '$sum_sub', '$sum_tax', '$idtax', '$pph', '$sum_pph', '$sum_total', '$sum_dp', '$curr', '1', '$post_date', '$update_date', '$status', '$status_int', '$create_user', '$create_date', '$start_date', '$end_date')";
        mysqli_query($conn2, $query);

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

        $idr_sub = $sum_dpp * $rate;
        $idr_tax = $sum_tax * $rate;

        // ambil COA untuk debit
        $sqlcoa = mysqli_query($conn1,"SELECT no_coa, nama_coa FROM mastercoa_v2 
            WHERE cus_ctg LIKE '%$cus_ctg%' 
            AND mattype LIKE '%$mattype%' 
            AND matclass LIKE '%$matclass%' 
            AND n_code_category LIKE '%$n_code_category%' 
            AND inv_type LIKE '%bpb_credit%' LIMIT 1");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $no_coa_deb = $rowcoa['no_coa'];
        $nama_coa_deb = $rowcoa['nama_coa'];

        // insert jurnal debit
        $querykbon1 = "INSERT INTO ap_journal_temp 
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, profit_center, reff_doc, reff_date) 
        VALUES 
        ('$no_kbon', '$create_date', 'AP - Kontrabon', '$no_coa_deb', '$nama_coa_deb', '$curr', '$rate', '$sum_dpp', '0', '$idr_sub', '0', 'Draft', '$keter', '$create_user', '$create_date', '$profit_center', '$no_bpb', '$tgl_bpb')";
        mysqli_query($conn2, $querykbon1);

        // insert jurnal PPN jika ada
        if ($sum_tax > 0) {
            $sqlcoa3 = mysqli_query($conn1,"SELECT no_coa, nama_coa FROM mastercoa_v2 WHERE inv_type LIKE '%PPN MASUKAN%' LIMIT 1");
            $rowcoa3 = mysqli_fetch_array($sqlcoa3);
            $no_coa_ppn = $rowcoa3['no_coa'];
            $nama_coa_ppn = $rowcoa3['nama_coa'];

            $queryss4 = "INSERT INTO ap_journal_temp 
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, profit_center, reff_doc, reff_date) 
            VALUES 
            ('$no_kbon', '$create_date', 'AP - Kontrabon', '$no_coa_ppn', '$nama_coa_ppn', '$curr', '$rate', '0', '$sum_tax', '0', '$idr_tax', 'Draft', '$keter', '$create_user', '$create_date', '$profit_center', '$no_bpb', '$tgl_bpb')";
            mysqli_query($conn2, $queryss4);
        }
    }
}

mysqli_close($conn2);
?>
