<?php
/* ============================================================================
   Simpan hasil EDIT Petty Cash Out untuk pembayaran FTR CBD / DP.

   Pola penyimpanannya sama persis dgn update_pv_cash.php:
     - hanya dokumen Draft yang boleh diedit
     - jurnal lama DIBALIK (baris "Reverse ...") lalu ditandai 'Updated',
       bukan dihapus - jejaknya tetap ada
     - baris adjustment lama dicadangkan ke _cancel sebelum dihapus
     - detail lama dihapus, lalu seluruhnya ditulis ulang sesuai keadaan form
     - nomor dokumen TIDAK berubah

   Yang khas FTR: jurnal debitnya SATU akun untuk semua baris -
   1.49.98 UANG MUKA PEMBELIAN - KAS KECIL. Lihat save_ftr_cash.php.
   ============================================================================ */
include '../../conn/conn.php';
include 'pv_data_functions.php';   // getAlreadyPaidFor()
session_start();

date_default_timezone_set('Asia/Jakarta');

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

    $data = json_decode($_POST['data'] ?? '', true);
    if (!$data) { throw new Exception('Data tidak valid'); }

    $header        = $data['header'];
    $detail_ftr    = $data['detail_ftr'] ?? array();
    $detail_adjust = $data['detail_adjust'] ?? array();

    $doc_num = isset($header['doc_num']) ? trim((string) $header['doc_num']) : '';
    if ($doc_num === '')              { throw new Exception('Nomor dokumen tidak valid.'); }
    if (count($detail_ftr) === 0)     { throw new Exception('Tidak ada FTR yang dipilih.'); }

    $doc_num_esc = mysqli_real_escape_string($conn2, $doc_num);

    $sqlChk = dbExec($conn2, "SELECT status FROM c_petty_cashout_h
        WHERE no_pco = '$doc_num_esc' AND reff = 'FTR (CBD / DP)' LIMIT 1 FOR UPDATE");
    $rowChk = mysqli_fetch_assoc($sqlChk);
    if (!$rowChk)                        { throw new Exception('Data Petty Cash Out tidak ditemukan.'); }
    if ($rowChk['status'] !== 'Draft')   { throw new Exception('Data sudah bukan Draft, tidak bisa diedit.'); }

    /* ---------------------------------------------------------- HEADER */
    $doc_date  = date('Y-m-d', strtotime($header['tgl']));
    $nama_supp = mysqli_real_escape_string($conn2, $header['supp']);
    $ref_num   = 'FTR (CBD / DP)';
    $akun      = $header['account'];
    $curr      = $header['currency'];
    $pc_kas    = $header['pc_header'];
    $amount    = $header['amount'];
    $desc      = mysqli_real_escape_string($conn2, $header['desc']);
    $cash_flow = $header['cash_flow'] ?? '';
    $user      = $_SESSION['username'] ?? 'system';
    $edit_date = date('Y-m-d H:i:s');

    if ($cash_flow === '') { throw new Exception('Cash Flow Category tidak boleh kosong.'); }
    $cash_flow = (int) $cash_flow;

    $sqlcoa   = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $akun) . "'");
    $rowcoa   = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'] ?? '-';

    /* SATU akun uang muka untuk semua baris. Namanya dibaca dari master supaya
       kalau diubah di master COA, jurnalnya ikut benar tanpa berkas ini
       perlu disentuh. */
    $COA_UM = '1.49.98';
    $qUm = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$COA_UM' limit 1");
    $rUm = $qUm ? mysqli_fetch_assoc($qUm) : null;
    if (!$rUm) { throw new Exception("Akun uang muka $COA_UM tidak ada di master COA."); }
    $coa_um      = mysqli_real_escape_string($conn2, $COA_UM);
    $coa_um_nama = mysqli_real_escape_string($conn2, $rUm['nama_coa']);

    /* Tiap kali diedit, jurnal barunya bertanda "(Rev N)" - naik 1 dari revisi
       sebelumnya. Baris "Reverse ..." tidak dihitung karena bukan revisi baru. */
    $sqlRev   = dbExec($conn2, "SELECT MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(type_journal, '(Rev ', -1), ')', 1) AS UNSIGNED)) mx
        FROM tbl_list_journal
        WHERE no_journal = '$doc_num_esc' AND type_journal LIKE '%(Rev %' AND type_journal NOT LIKE 'Reverse %'");
    $rowRev   = mysqli_fetch_assoc($sqlRev);
    $type_journal = mysqli_real_escape_string($conn2, $ref_num . ' (Rev ' . ((int) ($rowRev['mx'] ?? 0) + 1) . ')');

    /* ------------------------------------------- BALIK JURNAL LAMA */
    dbExec($conn2, "
        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center, supplier)
        SELECT no_journal, tgl_journal, CONCAT('Reverse ', type_journal), no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center, supplier
        FROM tbl_list_journal
        WHERE no_journal = '$doc_num_esc' AND status != 'Updated'
        ");
    dbExec($conn2, "UPDATE tbl_list_journal SET status = 'Updated' WHERE no_journal = '$doc_num_esc' AND status = 'Draft'");

    /* ------------------------------------ CADANGKAN & HAPUS YANG LAMA */
    dbExec($conn2, "INSERT INTO c_petty_cashout_adj_det_cancel SELECT * FROM c_petty_cashout_adj_det WHERE no_pco = '$doc_num_esc'");
    dbExec($conn2, "DELETE FROM c_petty_cashout_adj_det WHERE no_pco = '$doc_num_esc'");

    /* Detail lama dihapus DULU, supaya getAlreadyPaidFor() di bawah tidak ikut
       menghitung alokasi dokumen ini sendiri sbg "sudah dibayar". */
    dbExec($conn2, "DELETE FROM c_petty_cashout_det WHERE no_pco = '$doc_num_esc'");

    dbExec($conn2, "
        UPDATE c_petty_cashout_h
        SET tgl_pco = '$doc_date', nama_supp = '$nama_supp', coa_akun = '$akun', curr = '$curr',
            amount = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow', profit_center = '$pc_kas'
        WHERE no_pco = '$doc_num_esc'
        ");

    dbExec($conn2, "
        UPDATE c_report_pettycash
        SET transaksi_date = '$doc_date', credit = '$amount', balance = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow'
        WHERE no_doc = '$doc_num_esc'
        ");

    $journalRows = array();
    $detRows     = array();
    $adjRows     = array();

    /* Sisi kas: satu baris Credit sebesar nilai dokumen. */
    $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pc_kas', '$nama_supp')";

    /* -------------------------------------------------------- DETAIL FTR */
    foreach ($detail_ftr as $d) {

        $no_ftr   = trim((string) ($d['no_ftr'] ?? ''));
        $type_ftr = trim((string) ($d['type_pv'] ?? ''));
        $amount_d = (float) ($d['amount'] ?? 0);

        if ($no_ftr === '') { throw new Exception('Ada baris FTR tanpa nomor dokumen.'); }
        if ($amount_d <= 0) { throw new Exception("Amount untuk $no_ftr harus lebih dari 0."); }
        if ($type_ftr !== 'FTR-CBD' && $type_ftr !== 'FTR-DP') {
            throw new Exception("Jenis dokumen untuk $no_ftr tidak dikenali.");
        }

        $tabel = ($type_ftr === 'FTR-CBD') ? 'ftr_cbd' : 'ftr_dp';
        $kolom = ($type_ftr === 'FTR-CBD') ? 'no_ftr_cbd' : 'no_ftr_dp';
        $kTgl  = ($type_ftr === 'FTR-CBD') ? 'tgl_ftr_cbd' : 'tgl_ftr_dp';
        $no_esc = mysqli_real_escape_string($conn2, $no_ftr);

        $nilai = ($type_ftr === 'FTR-CBD')
            ? "SUM(subtotal + biaya_tambahan) sub, SUM(tax) tax, SUM(total + biaya_tambahan) total"
            : "SUM(total) sub, 0 tax, SUM(dp_value) total";
        $q = dbExec($conn2, "select MIN(status) status, MIN(payment_method) payment_method, MIN(profit_center) profit_center,
                MIN(curr) curr, MIN($kTgl) tgl_ftr, MIN(tgl_bayar) tgl_bayar, $nilai
            from $tabel where $kolom = '$no_esc'");
        $f = mysqli_fetch_assoc($q);

        if (!$f || $f['status'] === null)   { throw new Exception("$no_ftr tidak ditemukan."); }
        if ($f['status'] !== 'Approved')    { throw new Exception("$no_ftr berstatus " . $f['status'] . ", hanya yang Approved bisa dibayar."); }
        if (trim((string) $f['payment_method']) !== 'Cash') {
            throw new Exception("$no_ftr bukan Payment Method Cash - dibayar lewat PV-AP lalu Bank Out.");
        }

        /* Baris lama dokumen ini sudah dihapus di atas, jadi angka ini benar-benar
           "sudah dibayar dokumen LAIN". */
        $sudah = getAlreadyPaidFor($conn2, $type_ftr, $no_ftr);
        $sisa  = (float) $f['total'] - $sudah;
        if ($amount_d > $sisa + 0.01) {
            throw new Exception("Amount untuk $no_ftr melebihi sisa outstanding (" . number_format($sisa, 2) . ").");
        }

        $ftr_curr = strtoupper(trim((string) $f['curr']));
        $rate     = (float) ($d['rate'] ?? 0);
        if ($ftr_curr === '' || $ftr_curr === 'IDR') { $rate = 1; }
        if ($rate <= 0) { throw new Exception("Kurs untuk $no_ftr ($ftr_curr) tidak valid."); }

        $debit_idr = round($amount_d * $rate, 4);

        /* Profit center jurnal = profit center DOKUMENNYA; dokumen lama belum
           punya, jadi mundur ke profit center akun kas yang membayar. */
        $pc_baris = trim((string) $f['profit_center']);
        $pc_baris = mysqli_real_escape_string($conn2, $pc_baris !== '' ? $pc_baris : $pc_kas);

        $tgl_ftr_sql   = !empty($f['tgl_ftr'])   ? "'" . mysqli_real_escape_string($conn2, $f['tgl_ftr'])   . "'" : 'NULL';
        $tgl_bayar_sql = !empty($f['tgl_bayar']) ? "'" . mysqli_real_escape_string($conn2, $f['tgl_bayar']) . "'" : 'NULL';
        $curr_esc      = mysqli_real_escape_string($conn2, $ftr_curr);
        $type_esc      = mysqli_real_escape_string($conn2, $type_ftr);

        $detRows[] = "('$doc_num_esc', '$doc_date', '$no_esc', $tgl_ftr_sql, $tgl_bayar_sql, '" . (float) $f['sub'] . "', '" . (float) $f['tax'] . "', '0', '" . (float) $f['total'] . "', '$curr_esc', '$debit_idr', '$amount_d', '$type_esc')";

        $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$coa_um', '$coa_um_nama', '-', '-', '$no_esc', $tgl_ftr_sql, '-', '-', '$curr_esc', '$rate', '$amount_d', '0', '$debit_idr', '0', 'Draft', '$desc', '$user', '$edit_date', '', '', '', '', '$pc_baris', '$nama_supp')";
    }

    /* ----------------------------------------------------- DETAIL ADJUST */
    foreach ($detail_adjust as $row) {

        $coa       = $row['coa'];
        $pc        = $row['pc'];
        $cc        = $row['cc'];
        $debit     = $row['debit'];
        $credit    = $row['credit'];
        $desc2     = mysqli_real_escape_string($conn2, $row['desc']);
        $reff      = $row['reff_doc'];
        $reff_date = $row['reff_date'];

        $sql_coadet   = dbExec($conn2, "select no_coa,nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $coa) . "'");
        $row_coadet   = mysqli_fetch_array($sql_coadet);
        $nama_coa_adj = $row_coadet['nama_coa'] ?? '-';

        $sqlcc   = dbExec($conn2, "select cc_name from b_master_cc where no_cc = '" . mysqli_real_escape_string($conn2, $cc) . "'");
        $rowcc   = mysqli_fetch_array($sqlcc);
        $nama_cc = $rowcc['cc_name'] ?? null;

        $adjRows[] = "('$doc_num_esc', '$doc_date', '$coa', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$cc', '$pc')";

        $journalRows[] = "('$doc_num_esc', '$doc_date', '$type_journal', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$edit_date', '', '', '', '', '$pc', '$nama_supp')";
    }

    /* ---------------------------------------------------------- SIMPAN */
    if (!empty($detRows)) {
        dbExec($conn2, "INSERT INTO c_petty_cashout_det
            (no_pco, tgl_pco, no_reff, reff_date, due_date, dpp, ppn, pph, total, curr, eqv_idr, amount, type_pv)
            VALUES " . implode(', ', $detRows));
    }

    if (!empty($adjRows)) {
        dbExec($conn2, "INSERT INTO c_petty_cashout_adj_det
            (no_pco, tgl_pco, id_coa, reff_doc, reff_date, deskripsi, t_debit, t_credit, no_cc, profit_center)
            VALUES " . implode(', ', $adjRows));
    }

    dbExec($conn2, "INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center, supplier)
        VALUES " . implode(', ', $journalRows));

    mysqli_commit($conn2);

    echo json_encode(array(
        'status'  => 'ok',
        'message' => 'Data berhasil diperbarui. No: ' . $doc_num,
    ));

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode(array(
        'status'  => 'error',
        'message' => $e->getMessage(),
    ));
}
