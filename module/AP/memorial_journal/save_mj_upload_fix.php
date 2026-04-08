<?php
include "../../../conn/conn.php";
session_start();

date_default_timezone_set('Asia/Jakarta');

/* ================= DEBUG MODE ================= */
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// file_put_contents("debug_log.txt", "===== START =====\n", FILE_APPEND);

/* ================= START TRANSACTION ================= */
mysqli_begin_transaction($conn2);

try {

    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");
    $status = "Post";

    // file_put_contents("debug_log.txt", "USER: $user\n", FILE_APPEND);

    /* ================= AMBIL DATA DARI FORM ================= */

    // file_put_contents("debug_log.txt", "POST RAW: " . json_encode($_POST) . "\n", FILE_APPEND);

    $header = json_decode($_POST['header'], true);
    $detail = json_decode($_POST['detail'], true);

    // file_put_contents("debug_log.txt", "HEADER: " . json_encode($header) . "\n", FILE_APPEND);
    // file_put_contents("debug_log.txt", "DETAIL COUNT: " . count((array)$detail) . "\n", FILE_APPEND);

    if (!$detail || count($detail) == 0) {
        throw new Exception("Data kosong");
    }

    /* ================= SAMAKAN STRUKTUR SEPERTI TEMP ================= */

    $data_temp = $detail;

    /* ================= GROUP BY NO_MJ ================= */

    $grouped = [];
    foreach ($data_temp as $row) {
        $grouped[$row['no_mj']][] = $row;
    }

    $jumlah_header = count($grouped);

    // file_put_contents("debug_log.txt", "JUMLAH HEADER: $jumlah_header\n", FILE_APPEND);

    /* ================= LOOP HEADER ================= */

    $i = 1;
    $list_no_mj = [];
    $list_no_mj_sb = [];

    foreach ($grouped as $no_mj_temp => $rows) {

        // file_put_contents("debug_log.txt", "LOOP NO_MJ TEMP: $no_mj_temp\n", FILE_APPEND);

        /* ================= AMBIL HEADER ================= */

        $mj_date = date('Y-m-d', strtotime($header['mj_date'] ?? $rows[0]['mj_date']));
        $description = $header['keterangan'] ?? $rows[0]['keterangan'];
        $fil_sb1 = $header['status'] ?? ($rows[0]['status'] ?? 'N');

        $bulan = date('m', strtotime($mj_date));
        $tahun = date('y', strtotime($mj_date));

        $prefix = "GM/NAG/" . $bulan . $tahun;

        // file_put_contents("debug_log.txt", "PREFIX: $prefix\n", FILE_APPEND);

        /* ================= GET LAST NUMBER ================= */

        $sql = mysqli_query($conn2, "
            SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
            FROM tbl_memorial_journal
            WHERE no_mj LIKE '$prefix%'
        ");
        $row = mysqli_fetch_assoc($sql);
        $start = ($row['max_urut'] ?? 0);

        $sql_sb = mysqli_query($conn2, "
            SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
            FROM sb_memorial_journal
            WHERE no_mj LIKE '$prefix%'
        ");
        $row_sb = mysqli_fetch_assoc($sql_sb);
        $start_sb = ($row_sb['max_urut'] ?? 0);

        $no_mj    = $prefix . "/" . sprintf("%05d", $start + $i);
        $no_mj_sb = $prefix . "/" . sprintf("%05d", $start_sb + $i);

        // file_put_contents("debug_log.txt", "GENERATE NO_MJ: $no_mj\n", FILE_APPEND);

        $list_no_mj[] = $no_mj;

        if ($fil_sb1 == 'YES') {
            $list_no_mj_sb[] = $no_mj_sb;
        }

        /* ================= HITUNG TOTAL ================= */

        $total_debit = 0;
        $total_credit = 0;

        foreach ($rows as $r) {
            $total_debit += $r['debit'];
            $total_credit += $r['credit'];
        }

        // file_put_contents("debug_log.txt", "TOTAL D:$total_debit C:$total_credit\n", FILE_APPEND);

        /* ================= INSERT STATUS ================= */

        if ($fil_sb1 == 'YES') {
            mysqli_query($conn2, "
                INSERT INTO status_memorial_journal
                (no_mj, mj_date, no_mj_sb, status, create_by, create_date)
                VALUES
                ('$no_mj', '$mj_date', '$no_mj_sb', 'Post', '$user', '$create_date')
            ");
            // file_put_contents("debug_log.txt", "INSERT STATUS OK\n", FILE_APPEND);
        }

        /* ================= INSERT DETAIL ================= */

        foreach ($rows as $r) {

            // file_put_contents("debug_log.txt", "INSERT DETAIL ROW\n", FILE_APPEND);

            $coa_i = $r['no_coa'];
            $cc_i = $r['no_costcenter'];
            $reff_i = $r['no_reff'];
            $reffdate_i = $r['reff_date'];
            $buyer_i = $r['buyer'];
            $ws_i = $r['no_ws'];
            $curr_i = $r['curr'];
            $rate_det = $r['rate'];
            $credit_i = $r['credit'];
            $credit_idr = $r['credit_idr'];
            $debit_i = $r['debit'];
            $debit_idr = $r['debit_idr'];
            $ket_i = $r['keterangan'];
            $pc_i = $r['kode_pc'];
            $mj_type = $r['id_cmj'];
            $nama_cmj = $r['nama_cmj'];
            $nama_cc = $r['cc_name'];

            $sqlcoa = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa_i'");
            $rowcoa = mysqli_fetch_array($sqlcoa);
            $nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

            mysqli_query($conn2, "
            INSERT INTO tbl_memorial_journal
            (
            no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center
            )
            VALUES
            ('$no_mj', '$mj_date', '$mj_type', '$coa_i', '$cc_i', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$ket_i', '$status', '$user', '$create_date', '$pc_i')
            ");

            mysqli_query($conn2, "
            INSERT INTO tbl_list_journal
            (
            no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
            )
            VALUES
            ('$no_mj', '$mj_date', '$nama_cmj', '$coa_i', '$nama_coa', '$cc_i', '$nama_cc', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$status', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
            ");

            if ($fil_sb1 == 'YES') {

                mysqli_query($conn2, "
                INSERT INTO sb_memorial_journal
                (
                no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center
                )
                VALUES
                ('$no_mj_sb', '$mj_date', '$mj_type', '$coa_i', '$cc_i', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$ket_i', '$status', '$user', '$create_date', 'Upload SB2', '$pc_i')
                ");

                mysqli_query($conn2, "
                INSERT INTO sb_list_journal
                (
                no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
                )
                VALUES
                ('$no_mj_sb', '$mj_date', '$nama_cmj', '$coa_i', '$nama_coa', '$cc_i', '$nama_cc', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$status', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
                ");
            }
        }

        $i++;
    }

    /* ================= DELETE TEMP ================= */

    mysqli_query($conn2, "DELETE FROM tbl_memorial_journal_temp WHERE create_by = '$user'");

    // file_put_contents("debug_log.txt", "DELETE TEMP OK\n", FILE_APPEND);

    mysqli_commit($conn2);

    // file_put_contents("debug_log.txt", "COMMIT SUCCESS\n", FILE_APPEND);

    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil disimpan",
        "total_header" => $jumlah_header,
        "no_mj" => $list_no_mj,
        "no_mj_sb" => $list_no_mj_sb
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    // file_put_contents("debug_log.txt", "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
