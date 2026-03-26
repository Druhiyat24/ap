<?php
include "../../../conn/conn.php";
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try {

    /* ================= INPUT ================= */

    $mj_type = $_POST['mj_type2'];
    $profit_center = $_POST['profit_center2'];
    $description = $_POST['pesan2'];
    $rate = $_POST['rate_mj2'] ?? 1;

    $rate = str_replace(',', '', $rate);

    $bulan = date('m', strtotime($mj_date));
    $tahun = date('y', strtotime($mj_date));

    $status = "Post";
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");

    $tgl_hris = date("Y-m", strtotime($_POST['hris_date']));
    $tgl_hris_input = date("Y-m-d", strtotime($_POST['hris_date']));

    /* ================= GET NAMA CMJ ================= */

    $sqlcmj = mysqli_query($conn2, "SELECT nama_cmj FROM master_category_mj WHERE id_cmj = '$mj_type'");
    $rowcmj = mysqli_fetch_assoc($sqlcmj);
    $nama_cmj = $rowcmj['nama_cmj'];

    /* ================= PREFIX ================= */

    $prefix = "GM/NAG/" . $bulan . $tahun;

    /* ================= GET DATA TEMP ================= */

    $data_temp = [];
    $q = mysqli_query($conn2, "SELECT * FROM tbl_memorial_journal_temp WHERE create_by = '$user'");

    while ($r = mysqli_fetch_assoc($q)) {
        $data_temp[] = $r;
    }

    if (count($data_temp) == 0) {
        throw new Exception("Data kosong");
    }

    /* ================= GROUP BY NO_MJ ================= */

    $grouped = [];
    foreach ($data_temp as $row) {
        $grouped[$row['no_mj']][] = $row;
    }

    $jumlah_header = count($grouped);

    /* ================= GET LAST NUMBER ================= */

    $sql = mysqli_query($conn2, "
        SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
        FROM tbl_memorial_journal
        WHERE no_mj LIKE '$prefix%'
        FOR UPDATE
    ");
    $row = mysqli_fetch_assoc($sql);
    $start = ($row['max_urut'] ?? 0);

    $sql_sb = mysqli_query($conn2, "
        SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
        FROM sb_memorial_journal
        WHERE no_mj LIKE '$prefix%'
        FOR UPDATE
    ");
    $row_sb = mysqli_fetch_assoc($sql_sb);
    $start_sb = ($row_sb['max_urut'] ?? 0);

    /* ================= LOOP HEADER ================= */

    $i = 1;

    foreach ($grouped as $no_mj_temp => $rows) {

        $mj_date = date('Y-m-d', strtotime($rows[0]['mj_date']));
        $fil_sb1 = $_POST['fil_sb1'] ?? 'N';

        $urutan = $start + $i;
        $urutan_sb = $start_sb + $i;

        $no_mj = $prefix . "/" . sprintf("%05d", $urutan);
        $no_mj_sb = $prefix . "/" . sprintf("%05d", $urutan_sb);

        /* ================= HITUNG TOTAL ================= */

        $total_debit = 0;
        $total_credit = 0;

        foreach ($rows as $r) {
            $total_debit += $r['debit'];
            $total_credit += $r['credit'];
        }


       if ($fil_sb1 == '1') {

    mysqli_query($conn2, " INSERT INTO status_memorial_journal
(
no_mj, mj_date, no_mj_sb, status, create_by, create_date
)
VALUES
('$no_mj', '$mj_date', '$no_mj_sb', 'Post', '$user', '$create_date')
");
    }

        /* ================= INSERT DETAIL ================= */

        foreach ($rows as $r) {

            mysqli_query($conn2, "
                INSERT INTO tbl_memorial_journal_det
                (no_mj,no_coa,no_costcenter,no_reff,reff_date,buyer,no_ws,curr,rate,debit,credit,debit_idr,credit_idr,keterangan,kode_pc)
                VALUES
                ('$no_mj',
                '".$r['no_coa']."',
                '".$r['no_costcenter']."',
                '".$r['no_reff']."',
                '".$r['reff_date']."',
                '".$r['buyer']."',
                '".$r['no_ws']."',
                '".$r['curr']."',
                '".$r['rate']."',
                '".$r['debit']."',
                '".$r['credit']."',
                '".$r['debit_idr']."',
                '".$r['credit_idr']."',
                '".$r['keterangan']."',
                '".$r['profit_center']."')
            ");
        }

        /* ================= SB1 ================= */

        if ($fil_sb1 == 'Y') {

            mysqli_query($conn2, "
                INSERT INTO sb_memorial_journal
                (no_mj, mj_date, id_cmj, nama_cmj, curr, rate, total_debit, total_credit, keterangan, status, create_by, create_date)
                VALUES
                ('$no_mj_sb','$mj_date','$mj_type','$nama_cmj','".$rows[0]['curr']."','$rate','$total_debit','$total_credit','$description','$status','$user','$create_date')
            ");

            foreach ($rows as $r) {

                mysqli_query($conn2, "
                    INSERT INTO sb_memorial_journal_det
                    (no_mj,no_coa,no_costcenter,no_reff,reff_date,buyer,no_ws,curr,rate,debit,credit,debit_idr,credit_idr,keterangan,kode_pc)
                    VALUES
                    ('$no_mj_sb',
                    '".$r['no_coa']."',
                    '".$r['no_costcenter']."',
                    '".$r['no_reff']."',
                    '".$r['reff_date']."',
                    '".$r['buyer']."',
                    '".$r['no_ws']."',
                    '".$r['curr']."',
                    '".$r['rate']."',
                    '".$r['debit']."',
                    '".$r['credit']."',
                    '".$r['debit_idr']."',
                    '".$r['credit_idr']."',
                    '".$r['keterangan']."',
                    '".$r['profit_center']."')
                ");
            }
        }

        $i++;
    }

    /* ================= DELETE TEMP ================= */

    mysqli_query($conn2, "DELETE FROM tbl_memorial_journal_temp WHERE create_by = '$user'");

    mysqli_commit($conn2);

    echo json_encode([
        "status" => "success",
        "message" => "Data berhasil disimpan",
        "total_header" => $jumlah_header
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
