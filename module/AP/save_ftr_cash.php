<?php
/* ============================================================================
   Simpan Petty Cash Out untuk pembayaran FTR CBD / DP.

   Memotong satu langkah dari alur biasa: FTR -> (tanpa PV-AP) -> Petty Cash Out.
   Karena PV-AP dilewati, JURNAL UANG MUKA-nya dibuat di sini:

       Debit  1.49.98 UANG MUKA PEMBELIAN - KAS KECIL
       Credit Kas Kecil                 <- akun yang dipilih di header

   Akun debitnya TETAP satu untuk semua baris - tidak lagi dipetakan dari Item
   Type + area supplier seperti PV-AP CBD/DP. Uang muka yang keluar lewat kas
   kecil memang dikumpulkan di satu akun tersendiri, jadi Item Type pada FTR
   tidak ikut menentukan apa pun di sini (nilainya tetap tersimpan di FTR-nya).

   Dibangun mengikuti save_pv_cash.php - nomor dokumen, c_report_pettycash, dan
   baris adjustment-nya diperlakukan persis sama.
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

    if (count($detail_ftr) === 0) { throw new Exception('Tidak ada FTR yang dipilih.'); }

    /* ---------------------------------------------------------- HEADER */
    $doc_date    = date('Y-m-d', strtotime($header['tgl']));
    $nama_supp   = mysqli_real_escape_string($conn2, $header['supp']);
    $ref_num     = $header['ref'];
    $akun        = $header['account'];
    $curr        = $header['currency'];
    $kode_kas    = $header['kode_kas'];
    $pc_kas      = $header['pc_header'];
    $amount      = $header['amount'];
    $desc        = mysqli_real_escape_string($conn2, $header['desc']);
    $cash_flow   = $header['cash_flow'] ?? '';
    $user        = $_SESSION['username'] ?? 'system';
    $status      = 'Draft';
    $create_date = date('Y-m-d H:i:s');

    if ($cash_flow === '') { throw new Exception('Cash Flow Category tidak boleh kosong.'); }
    $cash_flow = (int) $cash_flow;

    /* SATU akun uang muka untuk semua baris. Namanya dibaca dari master, bukan
       diketik di sini, supaya kalau namanya diubah di master COA jurnalnya ikut
       benar tanpa berkas ini perlu disentuh. */
    $COA_UM = '1.49.98';
    $qUm = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$COA_UM' limit 1");
    $rUm = $qUm ? mysqli_fetch_assoc($qUm) : null;
    if (!$rUm) { throw new Exception("Akun uang muka $COA_UM tidak ada di master COA."); }
    $coa_um      = mysqli_real_escape_string($conn2, $COA_UM);
    $coa_um_nama = mysqli_real_escape_string($conn2, $rUm['nama_coa']);

    /* --------------------------------------------------- NOMOR DOKUMEN */
    $bulan  = date('m', strtotime($doc_date));
    $tahun  = date('Y', strtotime($doc_date));
    $prefix = 'KKK/' . $kode_kas . '/' . $tahun . '/' . $bulan;

    $sql  = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_pco,5) AS UNSIGNED)) AS max_urut FROM c_petty_cashout_h WHERE no_pco LIKE '$prefix%' FOR UPDATE");
    $sqlJ = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut FROM tbl_list_journal WHERE no_journal LIKE '$prefix%' FOR UPDATE");

    $rowH = mysqli_fetch_assoc($sql);
    $rowJ = mysqli_fetch_assoc($sqlJ);
    $urutan  = max((int) ($rowH['max_urut'] ?? 0), (int) ($rowJ['max_urut'] ?? 0)) + 1;
    $doc_num = $prefix . '/' . sprintf('%05d', $urutan);

    $sqlcoa  = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $akun) . "'");
    $rowcoa  = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'] ?? '-';

    dbExec($conn2, "INSERT INTO c_petty_cashout_h
        (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status,create_by,create_date,reff_doc,id_cash_flow,profit_center)
        VALUES ('$doc_num','$doc_date','$ref_num','$nama_supp','$akun','$curr','$amount','$desc','$status','$user','$create_date','','$cash_flow','$pc_kas')");

    dbExec($conn2, "INSERT INTO c_report_pettycash
        (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit,balance,status,id_cash_flow)
        VALUES ('$doc_date','$doc_num','$desc','$akun','','','$curr','0','$amount','$amount','$status','$cash_flow')");

    $journalRows = array();
    $detRows     = array();
    $adjRows     = array();

    /* Sisi kas: satu baris Credit sebesar nilai dokumen. */
    $journalRows[] = "('$doc_num', '$doc_date', '$ref_num', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas', '$nama_supp')";

    /* -------------------------------------------------------- DETAIL FTR */
    foreach ($detail_ftr as $d) {

        $no_ftr    = trim((string) ($d['no_ftr'] ?? ''));
        $type_ftr  = trim((string) ($d['type_pv'] ?? ''));
        $amount_d  = (float) ($d['amount'] ?? 0);

        if ($no_ftr === '')  { throw new Exception('Ada baris FTR tanpa nomor dokumen.'); }
        if ($amount_d <= 0)  { throw new Exception("Amount untuk $no_ftr harus lebih dari 0."); }
        if ($type_ftr !== 'FTR-CBD' && $type_ftr !== 'FTR-DP') {
            throw new Exception("Jenis dokumen untuk $no_ftr tidak dikenali.");
        }

        $tabel = ($type_ftr === 'FTR-CBD') ? 'ftr_cbd' : 'ftr_dp';
        $kolom = ($type_ftr === 'FTR-CBD') ? 'no_ftr_cbd' : 'no_ftr_dp';
        $kTgl  = ($type_ftr === 'FTR-CBD') ? 'tgl_ftr_cbd' : 'tgl_ftr_dp';
        $no_esc = mysqli_real_escape_string($conn2, $no_ftr);

        /* Nilai dokumen dibaca ULANG dari database, tidak dipercaya dari layar:
           halaman bisa saja sudah basi saat Save ditekan. */
        $nilai = ($type_ftr === 'FTR-CBD')
            ? "SUM(subtotal + biaya_tambahan) sub, SUM(tax) tax, SUM(total + biaya_tambahan) total"
            : "SUM(total) sub, 0 tax, SUM(dp_value) total";
        $q = dbExec($conn2, "select MIN(status) status, MIN(payment_method) payment_method, MIN(profit_center) profit_center,
                MIN(curr) curr, MIN($kTgl) tgl_ftr, MIN(tgl_bayar) tgl_bayar, $nilai
            from $tabel where $kolom = '$no_esc'");
        $f = mysqli_fetch_assoc($q);

        if (!$f || $f['status'] === null)      { throw new Exception("$no_ftr tidak ditemukan."); }
        if ($f['status'] !== 'Approved')       { throw new Exception("$no_ftr berstatus " . $f['status'] . ", hanya yang Approved bisa dibayar."); }
        if (trim((string) $f['payment_method']) !== 'Cash') {
            throw new Exception("$no_ftr bukan Payment Method Cash - dibayar lewat PV-AP lalu Bank Out.");
        }

        /* Sisa dihitung ulang di sini juga - antara layar dibuka dan Save
           ditekan, dokumen yang sama bisa sudah dibayar orang lain. */
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

        /* Profit center jurnal = profit center DOKUMENNYA. Dokumen lama belum
           punya (kolomnya baru 02 Okt 2026), jadi mundur ke profit center akun
           kas yang membayar - aturan yang sama dipakai perhitungan total di
           layar, supaya angka yang dilihat user sama dgn yang tersimpan. */
        $pc_baris = trim((string) $f['profit_center']);
        $pc_baris = mysqli_real_escape_string($conn2, $pc_baris !== '' ? $pc_baris : $pc_kas);

        $tgl_ftr_sql   = !empty($f['tgl_ftr'])   ? "'" . mysqli_real_escape_string($conn2, $f['tgl_ftr'])   . "'" : 'NULL';
        $tgl_bayar_sql = !empty($f['tgl_bayar']) ? "'" . mysqli_real_escape_string($conn2, $f['tgl_bayar']) . "'" : 'NULL';
        $curr_esc      = mysqli_real_escape_string($conn2, $ftr_curr);
        $type_esc      = mysqli_real_escape_string($conn2, $type_ftr);

        $detRows[] = "('$doc_num', '$doc_date', '$no_esc', $tgl_ftr_sql, $tgl_bayar_sql, '" . (float) $f['sub'] . "', '" . (float) $f['tax'] . "', '0', '" . (float) $f['total'] . "', '$curr_esc', '$debit_idr', '$amount_d', '$type_esc')";

        $journalRows[] = "('$doc_num', '$doc_date', '$ref_num', '$coa_um', '$coa_um_nama', '-', '-', '$no_esc', $tgl_ftr_sql, '-', '-', '$curr_esc', '$rate', '$amount_d', '0', '$debit_idr', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_baris', '$nama_supp')";
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

        $adjRows[] = "('$doc_num', '$doc_date', '$coa', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$cc', '$pc')";

        $journalRows[] = "('$doc_num', '$doc_date', '$ref_num', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pc', '$nama_supp')";
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
        'message' => 'Data berhasil disimpan. No: ' . $doc_num,
    ));

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode(array(
        'status'  => 'error',
        'message' => $e->getMessage(),
    ));
}
