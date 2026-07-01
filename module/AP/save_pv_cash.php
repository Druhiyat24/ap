<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try {

    $json = $_POST['data'] ?? '';
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception("Data tidak valid");
    }

    $header        = $data['header'];
    $detail_pv     = $data['detail_pv'];
    $detail_adjust = $data['detail_adjust'];

    // =========================
    // HEADER VALUE
    // =========================
    $doc_date = date('Y-m-d', strtotime($header['tgl']));
    $nama_supp = mysqli_real_escape_string($conn2, $header['supp']);
    $ref_num = $header['ref'];
    $akun = $header['account'];
    $curr = $header['currency'];
    $kode_kas = $header['kode_kas'];
    $pc_kas = $header['pc_header'];
    $amount = $header['amount'];
    $desc = mysqli_real_escape_string($conn2, $header['desc']);
    $user = $_SESSION['username'] ?? 'system';
    $status = "Draft";
    $create_date = date("Y-m-d H:i:s");

    // =========================
    // FORMAT PREFIX
    // =========================
    $bulan = date('m', strtotime($doc_date));
    $tahun = date('y', strtotime($doc_date));

    $prefix = "KKK/" . $kode_kas . "/" . $tahun . "/" . $bulan;

    // =========================
    // AMBIL MAX URUTAN
    // =========================
    $sql = mysqli_query($conn2, "
        SELECT MAX(CAST(RIGHT(no_pco,5) AS UNSIGNED)) AS max_urut
        FROM c_petty_cashout_h
        WHERE no_pco LIKE '$prefix%'
        FOR UPDATE
        ");

    $row = mysqli_fetch_assoc($sql);
    $urutan = ($row['max_urut'] ?? 0) + 1;

    $doc_num = $prefix . "/" . sprintf("%05d", $urutan);

    $sqlcoa = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $akun) . "'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'];

    // =========================
    // INSERT HEADER
    // =========================
    mysqli_query($conn2, "
        INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date, reff_doc)
        VALUES
        ('$doc_num', '$doc_date', '$ref_num', '$nama_supp', '$akun', '$curr', '$amount','$desc', '$status', '$user', '$create_date', '')
        ");

    mysqli_query($conn2, "
        INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance, status)
        VALUES
        ('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status')
        ");

    mysqli_query($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES
        ('$doc_num', '$doc_date', '$ref_num', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas')
        ");

    // =========================
    // INSERT DETAIL PV
    // =========================
    // Sama seperti bank-out/save_pv.php (cabang non-Biaya): kontrabon-family
    // sudah dibukukan sebagai hutang (kartu_hutang) saat approval, jadi jurnal
    // di sini cuma satu baris pelunasan per voucher: Debit COA Hutang yang
    // ditarik dari table masing-masing (kontrabon_h.no_coa untuk Regular/
    // Installment; kontrabon_h_dp/_cbd.no_coa untuk DP/CBD).
    foreach ($detail_pv as $pv) {

        $no_pv = $pv['no_pv'];
        $amount_pv = $pv['amount'];
        $type_pv = !empty($pv['type_pv']) ? $pv['type_pv'] : 'Regular';

        $no_kbon_esc = mysqli_real_escape_string($conn2, $no_pv);
        $filtersOne = [
            'nama_supp'   => 'ALL',
            'status'      => 'ALL',
            'filter_date' => 'tgl_kbon',
            'start_date'  => '',
            'end_date'    => '',
        ];

        $pv_coa = '-';
        $pv_coa_nama = '-';
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

        if ($rowOne) {
            $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
            $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
        }

        if (!$rowOne) {
            throw new Exception("Data $type_pv untuk $no_pv tidak ditemukan");
        }

        $pv_pc = $rowOne['profit_center'];
        $pv_date = $rowOne['tgl_kbon_raw'];
        $pv_duedate = $rowOne['tgl_tempo_raw'];
        $pv_curr = $rowOne['curr'];
        $pv_sub = $rowOne['subtotal_raw'];
        $pv_ppn = $rowOne['tax_raw'];
        $pv_pph = $rowOne['pph_raw'];
        $pv_rate = !empty($rowOne['rate']) ? $rowOne['rate'] : 1;

        $alreadyPaid = getAlreadyPaidFor($conn2, $type_pv, $no_pv);
        $outstanding = (float) $rowOne['total_raw'] - $alreadyPaid;

        if ($amount_pv > $outstanding + 0.01) {
            throw new Exception("Amount untuk $no_pv melebihi sisa outstanding (" . number_format($outstanding, 2) . ")");
        }

        $pv_total = $outstanding;

        $pv_pc_esc = mysqli_real_escape_string($conn2, $pv_pc);
        $pv_date_sql = !empty($pv_date) ? "'" . mysqli_real_escape_string($conn2, $pv_date) . "'" : 'NULL';
        $pv_duedate_sql = !empty($pv_duedate) ? "'" . mysqli_real_escape_string($conn2, $pv_duedate) . "'" : 'NULL';
        $pv_curr_esc = mysqli_real_escape_string($conn2, $pv_curr);
        $pv_coa_esc = mysqli_real_escape_string($conn2, $pv_coa);
        $pv_coa_nama_esc = mysqli_real_escape_string($conn2, $pv_coa_nama);
        $type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);

        // PPH split: Regular & Installment – jurnal PPH dipisah baris (pola save_lp.php)
        $bayar_pph = ($type_pv === 'Regular' || $type_pv === 'Installment')
            ? min($pv_pph, (float)$amount_pv) : 0;
        $total_dppnya = $amount_pv + $bayar_pph;
        $debit_idr = round($total_dppnya * $pv_rate, 4);

        mysqli_query($conn2, "
            INSERT INTO c_petty_cashout_det (no_pco, tgl_pco, no_reff, reff_date, due_date, dpp, ppn, pph, total, curr, eqv_idr, amount, type_pv)
            VALUES
            ('$doc_num', '$doc_date', '$no_kbon_esc', $pv_date_sql, $pv_duedate_sql, '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr_esc', '$amount_pv', '$amount_pv', '$type_pv_esc')
            ");

        mysqli_query($conn2, "
            INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$doc_num', '$doc_date', '$ref_num', '$pv_coa_esc', '$pv_coa_nama_esc', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '$total_dppnya', '0', '$debit_idr', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc_esc')
            ");

        if ($bayar_pph > 0) {
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
                mysqli_query($conn2, "
                    INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
                    VALUES
                    ('$doc_num', '$doc_date', '$ref_num', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '0', '$bayar_pph', '0', '$pph_idr', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc_esc')
                    ");
            }
        }
    }

    // =========================
    // INSERT DETAIL ADJUST
    // =========================
    foreach ($detail_adjust as $row) {

        $coa = $row['coa'];
        $pc = $row['pc'];
        $cc = $row['cc'];
        $debit = $row['debit'];
        $credit = $row['credit'];
        $desc2 = mysqli_real_escape_string($conn2, $row['desc']);
        $reff = $row['reff_doc'];
        $reff_date = $row['reff_date'];

        $sql_coadet = mysqli_query($conn2, "select no_coa,nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $coa) . "'");
        $row_coadet = mysqli_fetch_array($sql_coadet);
        $nama_coa_adj = $row_coadet['nama_coa'];

        $sqlcc = mysqli_query($conn2, "select cc_name from b_master_cc where no_cc = '" . mysqli_real_escape_string($conn2, $cc) . "'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        mysqli_query($conn2, "
            INSERT INTO c_petty_cashout_adj_det
            (no_pco, tgl_pco, id_coa, reff_doc, reff_date, deskripsi, t_debit, t_credit, no_cc, profit_center)
            VALUES
            ('$doc_num', '$doc_date', '$coa', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$cc', '$pc')
            ");

        mysqli_query($conn2, "
            INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$doc_num', '$doc_date', '$ref_num', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pc')
            ");
    }

    // =========================
    // COMMIT
    // =========================
    mysqli_commit($conn2);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Data berhasil disimpan. No: ' . $doc_num
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
