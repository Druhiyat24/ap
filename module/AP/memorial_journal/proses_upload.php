<?php
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../../conn/conn.php';

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
    mysqli_query($conn, "TRUNCATE TABLE temp_journal");

    $no = 1;

    foreach ($rows as $i => $row) {

        if ($i == 0) continue;

        $profit_center = $row[0];
        $coa           = $row[1];
        $cost_center   = $row[2];
        $reff_doc      = $row[3];
        $reff_date     = $row[4];
        $buyer         = $row[5];
        $ws            = $row[6];
        $curr          = $row[7];
        $debit         = $row[8];
        $credit        = $row[9];
        $desc          = $row[10];

        mysqli_query($conn, "
            INSERT INTO temp_journal 
            (no_journal, profit_center, no_coa, no_cc, reff_doc, reff_date, buyer, ws, curr, debit, credit, deskripsi)
            VALUES
            ('$no', '$profit_center', '$coa', '$cost_center', '$reff_doc', '$reff_date', '$buyer', '$ws', '$curr', '$debit', '$credit', '$desc')
        ");
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
