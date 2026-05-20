<?php
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../../conn/conn.php';

session_start();

date_default_timezone_set('Asia/Jakarta');

if (!isset($_FILES['file'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'File tidak ditemukan'
    ]);
    exit;
}

$file = $_FILES['file']['tmp_name'];

try {

    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // hapus temp dulu (optional)

    $no = 1;
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");

    mysqli_query($conn2, "delete from tbl_memorial_journal_temp where create_by = '$user'");

    foreach ($rows as $i => $row) {

        if ($i == 0) continue;

        $no_journal     = $row[0];
        $tgl_journal    = date('Y-m-d', strtotime($row[1]));
        $id_type        = $row[2];
        $no_coa         = $row[3];
        $id_pc          = $row[4];
        $no_cc          = $row[5];
        $no_reff        = $row[6];
        $tgl_reff       = date('Y-m-d', strtotime($row[7]));
        $buyer          = $row[8];
        $no_ws          = $row[9];
        $curr           = $row[10];
        $debit          = $row[11];
        $credit         = $row[12];
        $keterangan     = $row[13];
        $flag           = $row[14];
        $status         = 'Post';

        $sql_pc = mysqli_query($conn2,"select kode_pc from master_pc where id_pc = '$id_pc' GROUP BY id_pc");
        $row_pc = mysqli_fetch_array($sql_pc);
        $kode_pc = $row_pc['kode_pc'];

        $sql_rate = mysqli_query($conn2,"select TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate)) rate from ap_masterrate where tanggal = '$tgl_journal' and v_codecurr = 'PAJAK' and curr = '$curr'");
        $row_rate = mysqli_fetch_array($sql_rate);
        $rate = $row_rate['rate'] ?? '1';

        $debit_idr = $debit * $rate;
        $credit_idr = $credit * $rate;


        mysqli_query($conn2, "
            INSERT INTO tbl_memorial_journal_temp 
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center )
            VALUES
            ('$no_journal','$tgl_journal','$id_type', '$no_coa','$no_cc','$no_reff','$tgl_reff', '$buyer','$no_ws','$curr','$rate', '$debit','$credit','$debit_idr','$credit_idr', '$keterangan','$flag','$user', '$create_date','$kode_pc')");

    }

    echo json_encode([
        'status' => 'success'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
