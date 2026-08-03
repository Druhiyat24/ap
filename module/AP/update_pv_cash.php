<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Jalankan query mutating (INSERT/UPDATE/DELETE) dan lempar Exception kalau
// gagal, supaya transaksi ke-rollback dengan benar - sebelumnya hasil
// mysqli_query() tidak pernah dicek jadi error SQL bisa lolos ke commit.
function dbExec($conn, $sql)
{
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception('DB Error: ' . mysqli_error($conn));
    }
    return $result;
}

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
    // VALIDASI DOKUMEN
    // =========================
    $doc_num = $header['doc_num'] ?? '';

    if (!$doc_num) {
        throw new Exception("Nomor dokumen tidak valid");
    }

    $doc_num_esc = mysqli_real_escape_string($conn2, $doc_num);

    $sqlChk = dbExec($conn2, "SELECT status, coa_akun FROM c_petty_cashout_h WHERE no_pco = '$doc_num_esc' AND reff = 'Payment Voucher' LIMIT 1 FOR UPDATE");
    $rowChk = mysqli_fetch_assoc($sqlChk);

    if (!$rowChk) {
        throw new Exception("Data Petty Cash Out tidak ditemukan");
    }

    if ($rowChk['status'] !== 'Draft') {
        throw new Exception("Data sudah bukan Draft, tidak bisa diedit");
    }

    // =========================
    // HEADER VALUE
    // =========================
    $doc_date  = date('Y-m-d', strtotime($header['tgl']));
    $nama_supp = mysqli_real_escape_string($conn2, $header['supp']);
    $ref_num   = 'Payment Voucher';
    $akun      = $header['account'];
    $curr      = $header['currency'];
    $pc_kas    = $header['pc_header'];
    $amount    = $header['amount'];
    $desc      = mysqli_real_escape_string($conn2, $header['desc']);
    $user      = $_SESSION['username'] ?? 'system';
    $edit_date = date("Y-m-d H:i:s");

    $sqlcoa = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $akun) . "'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'];

    // =========================
    // TYPE_JOURNAL REVISI - supaya tiap kali dokumen ini diedit, jurnal
    // barunya kebaca "Payment Voucher (Rev N)", naik 1 angka dari revisi
    // sebelumnya (baris "Reverse ..." tidak dihitung karena bukan revisi baru).
    // =========================
    $sqlRev = dbExec($conn2, "SELECT COUNT(DISTINCT type_journal) cnt FROM tbl_list_journal WHERE no_journal = '$doc_num_esc' AND type_journal LIKE '%(Rev %' AND type_journal NOT LIKE 'Reverse %'");
    $rowRev = mysqli_fetch_assoc($sqlRev);
    $revCount = (int) ($rowRev['cnt'] ?? 0);
    $type_journal = $ref_num . ' (Rev ' . ($revCount + 1) . ')';

    // =========================
    // REVERSE JURNAL LAMA (masih Draft, belum pernah direverse -
    // dokumen ini hanya bisa diedit selagi Draft jadi tidak ada jurnal
    // yang sudah Approved yang perlu disentuh)
    // =========================
    dbExec($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        SELECT no_journal, tgl_journal, CONCAT('Reverse ', type_journal), no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated', keterangan, create_by, create_date, '$user', '$edit_date', cancel_by, cancel_date, profit_center
        FROM tbl_list_journal
        WHERE no_journal = '$doc_num_esc' AND status != 'Updated'
        ");

    dbExec($conn2, "UPDATE tbl_list_journal SET status = 'Updated' WHERE no_journal = '$doc_num_esc' AND status = 'Draft'");

    // =========================
    // BACKUP & HAPUS ADJUSTMENT LAMA
    // =========================
    dbExec($conn2, "INSERT INTO c_petty_cashout_adj_det_cancel SELECT * FROM c_petty_cashout_adj_det WHERE no_pco = '$doc_num_esc'");
    dbExec($conn2, "DELETE FROM c_petty_cashout_adj_det WHERE no_pco = '$doc_num_esc'");

    // =========================
    // HAPUS DETAIL PV LAMA (akan diinsert ulang sesuai state form sekarang -
    // outstanding PV dihitung ulang via getAlreadyPaidFor() setelah baris
    // lama ini dihapus, supaya tidak dobel-hitung alokasi dokumen ini sendiri)
    // =========================
    dbExec($conn2, "DELETE FROM c_petty_cashout_det WHERE no_pco = '$doc_num_esc'");

    // =========================
    // UPDATE HEADER
    // =========================
    dbExec($conn2, "
        UPDATE c_petty_cashout_h
        SET tgl_pco = '$doc_date', nama_supp = '$nama_supp', coa_akun = '$akun', curr = '$curr', amount = '$amount', deskripsi = '$desc'
        WHERE no_pco = '$doc_num_esc'
        ");

    dbExec($conn2, "
        UPDATE c_report_pettycash
        SET transaksi_date = '$doc_date', credit = '$amount', balance = '$amount', deskripsi = '$desc'
        WHERE no_doc = '$doc_num_esc'
        ");

    // Baris-baris tbl_list_journal / c_petty_cashout_det / c_petty_cashout_adj_det
    // dikumpulkan dulu ke array, baru di-insert sekali per tabel (bulk insert)
    // di akhir - lebih cepat daripada 1 query per baris kalau PV/adjustment-nya
    // banyak, dan tetap 1 unit transaksi yang sama.
    $journalRows = [];
    $detRows     = [];
    $adjRows     = [];

    $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pc_kas')";

    // =========================
    // DETAIL PV (pola sama seperti save_pv_cash.php)
    // =========================
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

        $bayar_pph = ($type_pv === 'Regular' || $type_pv === 'Installment')
            ? min($pv_pph, (float)$amount_pv) : 0;
        $total_dppnya = $amount_pv + $bayar_pph;
        $debit_idr = round($total_dppnya * $pv_rate, 4);

        $detRows[] = "('$doc_num_esc', '$doc_date', '$no_kbon_esc', $pv_date_sql, $pv_duedate_sql, '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr_esc', '$amount_pv', '$amount_pv', '$type_pv_esc')";

        $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$pv_coa_esc', '$pv_coa_nama_esc', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '$total_dppnya', '0', '$debit_idr', '0', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pv_pc_esc')";

        if ($bayar_pph > 0) {
            if ($type_pv === 'Regular') {
                $sqlpph = dbExec($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(idtax) FROM kontrabon WHERE no_kbon = '$no_kbon_esc')");
            } else {
                $sqlpph = dbExec($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(k.idtax) FROM kontrabon k INNER JOIN kontrabon_h_installment_detail d ON d.no_kbon = k.no_kbon WHERE d.no_kbon_det = '$no_kbon_esc')");
            }
            $rowpph = mysqli_fetch_assoc($sqlpph);
            $no_coa_pph  = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
            $nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');
            $pph_idr = round($bayar_pph * $pv_rate, 4);

            if (!empty($no_coa_pph)) {
                $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '0', '$bayar_pph', '0', '$pph_idr', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pv_pc_esc')";
            }
        }
    }

    // =========================
    // DETAIL ADJUST
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

        $sql_coadet = dbExec($conn2, "select no_coa,nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $coa) . "'");
        $row_coadet = mysqli_fetch_array($sql_coadet);
        $nama_coa_adj = $row_coadet['nama_coa'];

        $sqlcc = dbExec($conn2, "select cc_name from b_master_cc where no_cc = '" . mysqli_real_escape_string($conn2, $cc) . "'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        $adjRows[] = "('$doc_num_esc', '$doc_date', '$coa', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$cc', '$pc')";

        $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$edit_date', '', '', '', '', '$pc')";
    }

    // =========================
    // BULK INSERT
    // =========================
    if (!empty($detRows)) {
        dbExec($conn2, "
            INSERT INTO c_petty_cashout_det (no_pco, tgl_pco, no_reff, reff_date, due_date, dpp, ppn, pph, total, curr, eqv_idr, amount, type_pv)
            VALUES " . implode(', ', $detRows)
        );
    }

    if (!empty($adjRows)) {
        dbExec($conn2, "
            INSERT INTO c_petty_cashout_adj_det (no_pco, tgl_pco, id_coa, reff_doc, reff_date, deskripsi, t_debit, t_credit, no_cc, profit_center)
            VALUES " . implode(', ', $adjRows)
        );
    }

    dbExec($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES " . implode(', ', $journalRows)
    );

    // =========================
    // RENUMBER JIKA ACCOUNT (KODE KAS) BERUBAH - no_pco mengandung kode kas
    // sebagai bagian nomornya, jadi kalau akun diganti ke kas kecil lain,
    // nomornya perlu di-generate ulang - baru di-rename di semua tabel
    // sebagai langkah terakhir sebelum commit.
    // =========================
    $old_akun = $rowChk['coa_akun'];

    if ($akun !== $old_akun) {

        $sqlNewKode = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '$akun'");
        $rowNewKode = mysqli_fetch_assoc($sqlNewKode);
        $new_kode_cash = $rowNewKode['kode_cash'] ?? '';

        $sqlOldKode = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '" . mysqli_real_escape_string($conn2, $old_akun) . "'");
        $rowOldKode = mysqli_fetch_assoc($sqlOldKode);
        $old_kode_cash = $rowOldKode['kode_cash'] ?? '';

        if ($new_kode_cash !== '' && $new_kode_cash !== $old_kode_cash) {

            $bulanNew = date('m', strtotime($doc_date));
            $tahunNew = date('Y', strtotime($doc_date));
            $newPrefix = "KKK/" . $new_kode_cash . "/" . $tahunNew . "/" . $bulanNew;

            $sqlSeq = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_pco,5) AS UNSIGNED)) AS max_urut FROM c_petty_cashout_h WHERE no_pco LIKE '$newPrefix%' FOR UPDATE");
            $rowSeq = mysqli_fetch_assoc($sqlSeq);
            $maxHeader = (int) ($rowSeq['max_urut'] ?? 0);

            $sqlSeqJ = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut FROM tbl_list_journal WHERE no_journal LIKE '$newPrefix%' FOR UPDATE");
            $rowSeqJ = mysqli_fetch_assoc($sqlSeqJ);
            $maxJournal = (int) ($rowSeqJ['max_urut'] ?? 0);

            $urutan = max($maxHeader, $maxJournal) + 1;
            $new_doc_num = $newPrefix . "/" . sprintf("%05d", $urutan);
            $new_doc_num_esc = mysqli_real_escape_string($conn2, $new_doc_num);

            dbExec($conn2, "UPDATE c_petty_cashout_h SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num_esc'");
            dbExec($conn2, "UPDATE c_petty_cashout_det SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num_esc'");
            dbExec($conn2, "UPDATE c_petty_cashout_adj_det SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num_esc'");
            dbExec($conn2, "UPDATE c_report_pettycash SET no_doc = '$new_doc_num_esc' WHERE no_doc = '$doc_num_esc'");
            dbExec($conn2, "UPDATE tbl_list_journal SET no_journal = '$new_doc_num_esc' WHERE no_journal = '$doc_num_esc'");

            $doc_num = $new_doc_num;
        }
    }

    // =========================
    // COMMIT
    // =========================
    mysqli_commit($conn2);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Data berhasil diupdate. No: ' . $doc_num
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
