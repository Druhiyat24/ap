<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
session_start();
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json');

mysqli_begin_transaction($conn2);

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (!$data) {
        throw new Exception("Data tidak valid");
    }

    $header       = $data['header'];
    $detail_pv    = $data['detail_pv'];
    $detail_adjust = $data['detail_adjust'];

    $tgl_pay      = date('Y-m-d', strtotime($header['tanggal']));
    $nama_supp    = mysqli_real_escape_string($conn2, $header['nama_supp']);
    $profit_center = mysqli_real_escape_string($conn2, $header['profit_center']);
    $pesan        = mysqli_real_escape_string($conn2, $header['pesan']);
    $create_user  = $_SESSION['username'] ?? 'system';
    $create_date  = date('Y-m-d H:i:s');
    $status       = 'draft';

    // Generate doc number server-side (same format as getnomor_pelunasan.php)
    $mmyy = date('my', strtotime($tgl_pay));
    $prefix = "PAY/LP/$profit_center/$mmyy/";

    $sqlno = mysqli_query($conn2, "SELECT LPAD(COALESCE(MAX(CAST(SUBSTR(payment_ftr_id,17) AS UNSIGNED)),0)+1,5,'0') urut
        FROM payment_ftr
        WHERE DATE_FORMAT(tgl_pelunasan,'%m%y') = '$mmyy'
        FOR UPDATE");
    $rowno = mysqli_fetch_assoc($sqlno);
    $doc_num = $prefix . $rowno['urut'];

    // =========================
    // DETAIL PV rows
    // =========================
    foreach ($detail_pv as $pv) {
        $no_pv   = $pv['no_pv'];
        $type_pv = !empty($pv['type_pv']) ? $pv['type_pv'] : 'Regular';
        $amount  = (float) $pv['amount'];

        $no_kbon_esc  = mysqli_real_escape_string($conn2, $no_pv);
        $type_pv_esc  = mysqli_real_escape_string($conn2, $type_pv);

        $filtersOne = [
            'nama_supp'   => 'ALL',
            'status'      => 'ALL',
            'filter_date' => 'tgl_kbon',
            'start_date'  => '',
            'end_date'    => '',
        ];

        $rowOne = null;

        if ($type_pv === 'Regular') {
            $filtersOne['no_kbon'] = $no_pv;
            $rows = getDataRegular($conn1, $conn2, $filtersOne, '1', '1', '');
            $rowOne = $rows[0] ?? null;
        } elseif ($type_pv === 'Installment') {
            $filtersOne['no_kbon_det'] = $no_pv;
            $rows = getDataInstallment($conn1, $conn2, $filtersOne, '1', '1', '');
            $rowOne = $rows[0] ?? null;
        } elseif ($type_pv === 'DP') {
            $filtersOne['no_kbon'] = $no_pv;
            $rows = getDataDp($conn1, $conn2, $filtersOne, '1', '1', '');
            $rowOne = $rows[0] ?? null;
        } elseif ($type_pv === 'CBD') {
            $filtersOne['no_kbon'] = $no_pv;
            $rows = getDataCbd($conn1, $conn2, $filtersOne, '1', '1', '');
            $rowOne = $rows[0] ?? null;
        } elseif ($type_pv === 'SaldoAwal') {
            $filtersOne['no_kbon'] = $no_pv;
            $rows = getDataSaldoAwal($conn2, $filtersOne);
            $rowOne = $rows[0] ?? null;
        }

        if (!$rowOne) {
            throw new Exception("Data $type_pv untuk $no_pv tidak ditemukan");
        }

        $alreadyPaid = getAlreadyPaidFor($conn2, $type_pv, $no_pv);
        $outstanding = (float) $rowOne['total_raw'] - $alreadyPaid;

        if ($amount > $outstanding + 0.01) {
            throw new Exception("Amount untuk $no_pv melebihi sisa outstanding (" . number_format($outstanding, 2) . ")");
        }

        $pv_pc       = mysqli_real_escape_string($conn2, $rowOne['profit_center']);
        $pv_date_raw = $rowOne['tgl_kbon_raw'];
        $pv_date_sql = !empty($pv_date_raw) ? "'" . mysqli_real_escape_string($conn2, $pv_date_raw) . "'" : "NULL";
        $pv_curr     = mysqli_real_escape_string($conn2, $rowOne['curr']);
        $pv_coa      = mysqli_real_escape_string($conn2, $rowOne['no_coa'] ?? '-');
        $pv_coa_nama = '';
        if (!empty($rowOne['no_coa'])) {
            $sqlcn = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$pv_coa' limit 1");
            $rwcn  = mysqli_fetch_assoc($sqlcn);
            $pv_coa_nama = mysqli_real_escape_string($conn2, $rwcn['nama_coa'] ?? '-');
        }
        $pv_rate     = !empty($rowOne['rate']) ? (float) $rowOne['rate'] : 1;
        $pv_total    = (float) $rowOne['total_raw'];
        $pv_pph      = (float) ($rowOne['pph_raw'] ?? 0);
        $tgl_tempo_sql = !empty($rowOne['tgl_tempo_raw']) ? "'" . mysqli_real_escape_string($conn2, $rowOne['tgl_tempo_raw']) . "'" : "NULL";

        // PPH split: Regular & Installment – jurnal PPH dipisah baris (pola save_lp.php)
        $bayar_pph = ($type_pv === 'Regular' || $type_pv === 'Installment')
            ? min($pv_pph, (float)$amount) : 0;
        $total_dppnya = $amount + $bayar_pph;
        $debit_idr   = round($total_dppnya * $pv_rate, 4);

        // payment_ftr row (one per PV, type_pv identifies this as direct-PV payment)
        mysqli_query($conn2, "INSERT INTO payment_ftr
            (payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment,
             no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank,
             valuta_bayar, nominal, nominal_fgn, rate, status, keterangan,
             create_user, create_date, profit_center, no_coa, type_pv)
            VALUES
            ('$doc_num', '$tgl_pay', '$nama_supp', '', '1970-01-01',
             '$no_kbon_esc', $pv_date_sql, '$pv_curr', '$pv_total', 'TRANSFER', '-', '-',
             '$pv_curr', '$amount', '0', '$pv_rate', '$status', '$pesan',
             '$create_user', '$create_date', '$pv_pc', '$pv_coa', '$type_pv_esc')");

        // tbl_list_journal DEBIT row (hutang PV di-debit saat dibayar; gross = amount + bayar_pph)
        mysqli_query($conn2, "INSERT INTO tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
             no_costcenter, nama_costcenter, reff_doc, reff_date,
             buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
             status, keterangan, create_by, create_date,
             approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$doc_num', '$tgl_pay', 'Payment Non Bank', '$pv_coa', '$pv_coa_nama',
             '-', '-', '$no_kbon_esc', $pv_date_sql,
             '-', '-', '$pv_curr', '$pv_rate', '$total_dppnya', '0', '$debit_idr', '0',
             'Draft', '$pesan', '$create_user', '$create_date',
             '', '', '', '', '$pv_pc')");

        if ($bayar_pph > 0) {
            $pv_pc_esc_pph = mysqli_real_escape_string($conn2, $pv_pc);
            $pv_curr_esc_pph = mysqli_real_escape_string($conn2, $pv_curr);
            if ($type_pv === 'Regular') {
                $sqlpph = mysqli_query($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(idtax) FROM kontrabon WHERE no_kbon = '$no_kbon_esc')");
            } else {
                $sqlpph = mysqli_query($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(k.idtax) FROM kontrabon k INNER JOIN kontrabon_h_installment_detail d ON d.no_kbon = k.no_kbon WHERE d.no_kbon_det = '$no_kbon_esc')");
            }
            $rowpph = mysqli_fetch_assoc($sqlpph);
            $no_coa_pph  = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
            $nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');
            $pph_idr = round($bayar_pph * $pv_rate, 4);

            if (!empty($no_coa_pph)) {
                mysqli_query($conn2, "INSERT INTO tbl_list_journal
                    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
                     no_costcenter, nama_costcenter, reff_doc, reff_date,
                     buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
                     status, keterangan, create_by, create_date,
                     approve_by, approve_date, cancel_by, cancel_date, profit_center)
                    VALUES
                    ('$doc_num', '$tgl_pay', 'Payment Non Bank', '$no_coa_pph', '$nama_coa_pph',
                     '-', '-', '$no_kbon_esc', $pv_date_sql,
                     '-', '-', '$pv_curr_esc_pph', '$pv_rate', '0', '$bayar_pph', '0', '$pph_idr',
                     'Draft', '$pesan', '$create_user', '$create_date',
                     '', '', '', '', '$pv_pc_esc_pph')");
            }
        }
    }

    // =========================
    // DETAIL ADJUST (credit / jurnal manual)
    // =========================
    foreach ($detail_adjust as $adj) {
        $coa      = mysqli_real_escape_string($conn2, $adj['coa']);
        $adj_pc   = mysqli_real_escape_string($conn2, $adj['pc']);
        $cc       = mysqli_real_escape_string($conn2, $adj['cc']);
        $reff_doc = mysqli_real_escape_string($conn2, $adj['reff_doc']);
        $reff_date_raw = $adj['reff_date'];
        $reff_date_sql = !empty($reff_date_raw) ? "'" . mysqli_real_escape_string($conn2, date('Y-m-d', strtotime($reff_date_raw))) . "'" : "NULL";
        $desk     = mysqli_real_escape_string($conn2, $adj['deskripsi']);
        $debit_a  = (float) $adj['debit'];
        $credit_a = (float) $adj['credit'];

        $sqlcn2 = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa' limit 1");
        $rwcn2  = mysqli_fetch_assoc($sqlcn2);
        $nama_coa_adj = mysqli_real_escape_string($conn2, $rwcn2['nama_coa'] ?? '-');

        $sqlcc = mysqli_query($conn2, "select cc_name from b_master_cc where no_cc = '$cc' limit 1");
        $rwcc  = mysqli_fetch_assoc($sqlcc);
        $nama_cc = mysqli_real_escape_string($conn2, $rwcc['cc_name'] ?? '-');

        mysqli_query($conn2, "INSERT INTO payment_ftr_det
            (payment_ftr_id, no_coa, no_cc, reff_doc, reff_date, deskripsi, t_debit, t_credit, profit_center, status)
            VALUES
            ('$doc_num', '$coa', '$cc', '$reff_doc', $reff_date_sql, '$desk', '$debit_a', '$credit_a', '$adj_pc', '$status')");

        mysqli_query($conn2, "INSERT INTO tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
             no_costcenter, nama_costcenter, reff_doc, reff_date,
             buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
             status, keterangan, create_by, create_date,
             approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$doc_num', '$tgl_pay', 'Payment Non Bank', '$coa', '$nama_coa_adj',
             '$cc', '$nama_cc', '$reff_doc', $reff_date_sql,
             '-', '-', 'IDR', '1', '$debit_a', '$credit_a', '$debit_a', '$credit_a',
             'Draft', '$desk', '$create_user', '$create_date',
             '', '', '', '', '$adj_pc')");
    }

    mysqli_commit($conn2);

    echo json_encode(['status' => 'ok', 'message' => 'Berhasil disimpan. No: ' . $doc_num]);

} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
