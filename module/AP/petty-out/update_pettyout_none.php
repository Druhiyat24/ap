<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Jalankan query mutating (INSERT/UPDATE/DELETE) dan lempar Exception kalau
// gagal, supaya transaksi ke-rollback dengan benar.
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

    $header = $data['header'];
    $detail = $data['detail'];

    // =========================
    // VALIDASI DOKUMEN
    // =========================
    $doc_num = $header['doc_num'] ?? '';

    if (!$doc_num) {
        throw new Exception("Nomor dokumen tidak valid");
    }

    $doc_num_esc = mysqli_real_escape_string($conn2, $doc_num);

    $sqlChk = dbExec($conn2, "SELECT status, reff, coa_akun FROM c_petty_cashout_h WHERE no_pco = '$doc_num_esc' AND reff IN ('None','Advance') LIMIT 1 FOR UPDATE");
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
    // Reff diambil dari data tersimpan (bukan dari payload client) supaya
    // dokumen 'Advance' tidak ketimpa jadi 'None' - form ini dipakai bareng
    // buat edit reff None & Advance (lihat petty-cashout.php).
    $ref_num   = $rowChk['reff'];
    $akun      = mysqli_real_escape_string($conn2, $header['account']);
    $curr      = mysqli_real_escape_string($conn2, $header['currency']);
    $pc_kas    = mysqli_real_escape_string($conn2, $header['pc_header']);
    $amount    = $header['amount'];
    $desc      = mysqli_real_escape_string($conn2, $header['desc']);
    $cash_flow = $header['cash_flow'] ?? '';
    $user      = $_SESSION['username'] ?? 'system';
    $edit_date = date("Y-m-d H:i:s");

    if ($cash_flow === '') {
        throw new Exception('Cash Flow Category tidak boleh kosong.');
    }
    $cash_flow = (int) $cash_flow;

    $sqlcoa = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$akun'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'];

    $old_akun = $rowChk['coa_akun'];

    // =========================
    // TYPE_JOURNAL REVISI - naik 1 angka dari revisi sebelumnya tiap kali
    // dokumen ini diedit ("None (Rev 1)", "None (Rev 2)", dst). Kecuali kalau
    // account (kode kas) berubah sampai dokumen akan di-renumber di akhir -
    // reset lagi ke nama dasar, jangan lanjutkan Rev dari nomor lama.
    // =========================
    $sqlNewKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '$akun'");
    $rowNewKodeEarly = mysqli_fetch_assoc($sqlNewKodeEarly);
    $new_kode_cash_early = $rowNewKodeEarly['kode_cash'] ?? '';

    $sqlOldKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '" . mysqli_real_escape_string($conn2, $old_akun) . "'");
    $rowOldKodeEarly = mysqli_fetch_assoc($sqlOldKodeEarly);
    $old_kode_cash_early = $rowOldKodeEarly['kode_cash'] ?? '';

    $willRenumber = ($new_kode_cash_early !== '' && $new_kode_cash_early !== $old_kode_cash_early);

    if ($willRenumber) {
        $type_journal = $ref_num;
    } else {
        $sqlRev = dbExec($conn2, "SELECT COUNT(DISTINCT type_journal) cnt FROM tbl_list_journal WHERE no_journal = '$doc_num_esc' AND type_journal LIKE '%(Rev %' AND type_journal NOT LIKE 'Reverse %'");
        $rowRev = mysqli_fetch_assoc($sqlRev);
        $revCount = (int) ($rowRev['cnt'] ?? 0);
        $type_journal = $ref_num . ' (Rev ' . ($revCount + 1) . ')';
    }

    // =========================
    // REVERSE JURNAL LAMA (masih Draft, belum pernah direverse - dokumen ini
    // hanya bisa diedit selagi Draft jadi tidak ada jurnal Approved yang
    // perlu disentuh)
    // =========================
    dbExec($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        SELECT no_journal, tgl_journal, CONCAT('Reverse ', type_journal), no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated', keterangan, create_by, create_date, '$user', '$edit_date', cancel_by, cancel_date, profit_center
        FROM tbl_list_journal
        WHERE no_journal = '$doc_num_esc' AND status != 'Updated'
        ");

    dbExec($conn2, "UPDATE tbl_list_journal SET status = 'Updated' WHERE no_journal = '$doc_num_esc' AND status = 'Draft'");

    // =========================
    // BACKUP & HAPUS DETAIL LAMA
    // =========================
    dbExec($conn2, "INSERT INTO c_petty_cashout_none_cancel SELECT * FROM c_petty_cashout_none WHERE no_pco = '$doc_num_esc'");
    dbExec($conn2, "DELETE FROM c_petty_cashout_none WHERE no_pco = '$doc_num_esc'");

    // =========================
    // UPDATE HEADER
    // =========================
    dbExec($conn2, "
        UPDATE c_petty_cashout_h
        SET tgl_pco = '$doc_date', nama_supp = '$nama_supp', coa_akun = '$akun', curr = '$curr', amount = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow'
        WHERE no_pco = '$doc_num_esc'
        ");

    dbExec($conn2, "
        UPDATE c_report_pettycash
        SET transaksi_date = '$doc_date', credit = '$amount', balance = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow'
        WHERE no_doc = '$doc_num_esc'
        ");

    // =========================
    // DETAIL + JURNAL (bulk insert)
    // =========================
    $detRows     = [];
    $journalRows = [];

    $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pc_kas')";

    foreach ($detail as $row) {

        $coa = mysqli_real_escape_string($conn2, $row['coa']);
        $pc = mysqli_real_escape_string($conn2, $row['pc']);
        $cc = mysqli_real_escape_string($conn2, $row['cc']);
        $buyer = mysqli_real_escape_string($conn2, $row['buyer'] ?? '');
        $ws = mysqli_real_escape_string($conn2, $row['ws'] ?? '');
        $curr_det = mysqli_real_escape_string($conn2, $row['currency'] ?? 'IDR');
        $debit = $row['debit'];
        $credit = $row['credit'];
        $desc2 = mysqli_real_escape_string($conn2, !empty($row['desc']) ? $row['desc'] : $header['desc']);

        $sqlcoadet = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa'");
        $rowcoadet = mysqli_fetch_array($sqlcoadet);
        $nama_coa_det = isset($rowcoadet['nama_coa']) ? $rowcoadet['nama_coa'] : null;

        $sqlcc = dbExec($conn2, "select cc_name from b_master_cc where no_cc = '$cc'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        $detRows[] = "('$doc_num_esc', '$doc_date', '$ref_num', '$coa', '$pc', '$cc', '$buyer', '$ws', '$curr_det', '$debit', '$credit', '$desc2')";

        $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$coa', '$nama_coa_det', '$cc', '$nama_cc', '-', '', '$buyer', '$ws', '$curr_det', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$edit_date', '', '', '', '', '$pc')";
    }

    if (!empty($detRows)) {
        dbExec($conn2, "
            INSERT INTO c_petty_cashout_none (no_pco, tgl_pco, reff_doc, no_coa, profit_center, no_costcntr, buyer, no_ws, curr, debit, credit, deskripsi)
            VALUES " . implode(', ', $detRows)
        );
    }

    dbExec($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES " . implode(', ', $journalRows)
    );

    // =========================
    // RENUMBER JIKA ACCOUNT (KODE KAS) BERUBAH - no_pco mengandung kode kas
    // sebagai bagian nomornya (KKK/{kode_kas}/{tahun}/{bulan}/{urutan}), jadi
    // kalau akun diganti ke kas kecil lain, nomornya perlu di-generate ulang
    // supaya kodenya tetap mencerminkan akun yang benar - baru di-rename di
    // semua tabel sebagai langkah terakhir sebelum commit.
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
            dbExec($conn2, "UPDATE c_petty_cashout_none SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num_esc'");
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
