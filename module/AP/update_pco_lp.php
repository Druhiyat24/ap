<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

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

$doc_num_raw = $_POST['doc_num'] ?? '';
$date = date("Y-m-d", strtotime($_POST['date'] ?? ''));
$customer = mysqli_real_escape_string($conn2, $_POST['customer'] ?? '');
$profit_center = mysqli_real_escape_string($conn2, $_POST['profit_center'] ?? '');
$akun = mysqli_real_escape_string($conn2, $_POST['akun'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$rate = floatval($_POST['rate'] ?? 0) ?: 1;
$deskripsi = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');
$create_user = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? 'system');
$create_date = date("Y-m-d H:i:s");

$details = json_decode($_POST['details'] ?? '', true) ?: [];

if (!$doc_num_raw) {
    echo "Invalid data";
    exit;
}

$doc_num = mysqli_real_escape_string($conn2, $doc_num_raw);

mysqli_begin_transaction($conn2);

try {

    $sqlChk = dbExec($conn2, "SELECT status, coa_akun FROM c_petty_cashout_h WHERE no_pco = '$doc_num' AND reff = 'List Payment' LIMIT 1 FOR UPDATE");
    $rowChk = mysqli_fetch_assoc($sqlChk);

    if (!$rowChk) {
        throw new Exception("Data Petty Cash Out tidak ditemukan");
    }

    if ($rowChk['status'] !== 'Draft') {
        throw new Exception("Data sudah bukan Draft, tidak bisa diedit");
    }

    $old_akun = $rowChk['coa_akun'];

    // Cek dulu apakah account berubah sampai kode kas-nya beda (dokumen akan
    // di-renumber di akhir) - kalau iya, reset label jurnal ke nama dasar
    // (jangan lanjutkan Rev dari nomor lama).
    $sqlNewKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '$akun'");
    $rowNewKodeEarly = mysqli_fetch_assoc($sqlNewKodeEarly);
    $new_kode_cash_early = $rowNewKodeEarly['kode_cash'] ?? '';

    $sqlOldKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '" . mysqli_real_escape_string($conn2, $old_akun) . "'");
    $rowOldKodeEarly = mysqli_fetch_assoc($sqlOldKodeEarly);
    $old_kode_cash_early = $rowOldKodeEarly['kode_cash'] ?? '';

    $willRenumber = ($new_kode_cash_early !== '' && $new_kode_cash_early !== $old_kode_cash_early);

    if ($willRenumber) {
        $type_journal = 'List Payment';
    } else {
        $sqlRev = dbExec($conn2, "select type_journal from tbl_list_journal where no_journal = '$doc_num'");
        $maxRev = 0;
        while ($rowRev = mysqli_fetch_assoc($sqlRev)) {
            if (preg_match('/\(Rev (\d+)\)/', $rowRev['type_journal'], $m)) {
                $maxRev = max($maxRev, (int) $m[1]);
            }
        }
        $type_journal = 'List Payment (Rev ' . ($maxRev + 1) . ')';
    }

    $sqlcoa = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$akun'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'];

    dbExec($conn2, "INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$create_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");

    dbExec($conn2, "INSERT into tbl_list_journal select '', no_journal, '$date' tgl_journal, '$type_journal' type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, 'Draft' status, '$deskripsi' keterangan, '$create_user' create_by, '$create_date' create_date, '' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and reff_doc like '%LP/%' and type_journal not like '%Reverse%' and status = 'Draft'");

    dbExec($conn2, "UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num' and status = 'Draft'");

    dbExec($conn2, "UPDATE c_petty_cashout_h SET tgl_pco = '$date', nama_supp = '$customer', profit_center = '$profit_center', amount = '$amount', deskripsi = '$deskripsi' WHERE no_pco = '$doc_num'");

    dbExec($conn2, "UPDATE c_report_pettycash set transaksi_date = '$date', credit = '$amount', deskripsi = '$deskripsi' where no_doc = '$doc_num'");

    // Baris-baris tbl_list_journal / c_petty_cashout_adj_det dikumpulkan dulu
    // ke array, baru di-insert sekali per tabel (bulk insert) di akhir.
    $journalRows = [
        "('$doc_num', '$date', '$type_journal', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', '$profit_center')"
    ];

    dbExec($conn2, "insert into c_petty_cashout_adj_det_cancel (select * from c_petty_cashout_adj_det where no_pco='$doc_num')");
    dbExec($conn2, "Delete from c_petty_cashout_adj_det where no_pco='$doc_num'");

    $detRows = [];

    foreach ($details as $d) {
        $coa            = mysqli_real_escape_string($conn2, $d['coa'] ?? '');
        $prof_ctr       = mysqli_real_escape_string($conn2, $d['prof_ctr'] ?? '');
        $cost_ctr       = mysqli_real_escape_string($conn2, $d['cost_ctr'] ?? '');
        $refferensi     = mysqli_real_escape_string($conn2, $d['refferensi'] ?? '');
        $tgl_refferensi = !empty($d['tgl_refferensi']) ? date("Y-m-d", strtotime($d['tgl_refferensi'])) : date('Y-m-d');
        $debit  = floatval($d['debit'] ?? 0);
        $credit = floatval($d['credit'] ?? 0);
        $ket        = mysqli_real_escape_string($conn2, !empty($d['keterangan']) ? $d['keterangan'] : $deskripsi);

        $sqlcoa2 = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa'");
        $rowcoa2 = mysqli_fetch_array($sqlcoa2);
        $nama_coa2 = isset($rowcoa2['nama_coa']) ? $rowcoa2['nama_coa'] : null;

        $sqlcc = dbExec($conn2, "select cc_name from b_master_cc where no_cc = '$cost_ctr'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        $t_debit = $debit * $rate;
        $t_credit = $credit * $rate;

        $detRows[] = "('$doc_num', '$coa', '$cost_ctr', '$refferensi', '$tgl_refferensi', '$ket', '$debit', '$credit', '$prof_ctr')";

        $journalRows[] = "('$doc_num', '$date', '$type_journal', '$coa', '$nama_coa2', '$cost_ctr', '$nama_cc', '$refferensi', '$tgl_refferensi', '-', '-', 'IDR', '1', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$ket', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";
    }

    if (!empty($detRows)) {
        dbExec($conn2, "
            INSERT INTO c_petty_cashout_adj_det (no_pco,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center)
            VALUES " . implode(', ', $detRows)
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
    // sebagai langkah terakhir sebelum commit. c_petty_cashout_det (link LP)
    // ikut di-rename walau tidak diubah isinya di form edit ini.
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

            $bulanNew = date('m', strtotime($date));
            $tahunNew = date('Y', strtotime($date));
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

            dbExec($conn2, "UPDATE c_petty_cashout_h SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num'");
            dbExec($conn2, "UPDATE c_petty_cashout_det SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num'");
            dbExec($conn2, "UPDATE c_petty_cashout_adj_det SET no_pco = '$new_doc_num_esc' WHERE no_pco = '$doc_num'");
            dbExec($conn2, "UPDATE c_report_pettycash SET no_doc = '$new_doc_num_esc' WHERE no_doc = '$doc_num'");
            dbExec($conn2, "UPDATE tbl_list_journal SET no_journal = '$new_doc_num_esc' WHERE no_journal = '$doc_num'");
        }
    }

    mysqli_commit($conn2);

    echo "OK";

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo "Error: " . $e->getMessage();
}
