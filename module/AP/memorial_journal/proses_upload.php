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

    // Ambil sheet BERDASARKAN NAMA, jangan getActiveSheet(). "Sheet aktif" adalah
    // tab yang terakhir dibuka saat berkas disimpan - kalau user sempat mengintip
    // tab COST CENTER / TYPE lalu menyimpan, sheet aktifnya bukan UPLOAD lagi dan
    // daftar cost center akan terbaca sebagai baris jurnal.
    $sheet = $spreadsheet->getSheetByName('UPLOAD');
    if ($sheet === null) { $sheet = $spreadsheet->getActiveSheet(); }
    $rows = $sheet->toArray();

    // hapus temp dulu (optional)

    // PENJAGA TEMPLATE. Kolom No Faktur/Tanggal Faktur disisipkan DI TENGAH
    // (setelah Tanggal Reference) dan Supplier setelah Keterangan, sehingga
    // template LAMA tidak lagi sejajar: Buyer akan terbaca sebagai No Faktur,
    // Debit sebagai Currency, dan seterusnya. Tanpa pemeriksaan ini berkas lama
    // tetap terimpor tanpa error, menghasilkan jurnal yang isinya kacau.
    $head = isset($rows[0]) ? array_map(function ($v) {
        return strtoupper(trim((string) $v));
    }, $rows[0]) : [];
    if (($head[8] ?? '') !== 'NO FAKTUR' || ($head[16] ?? '') !== 'SUPPLIER') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Format template tidak sesuai. Kolom "No Faktur" harus di posisi ke-9 '
                       . 'dan "Supplier" di posisi ke-17. Silakan unduh ulang template terbaru '
                       . 'lewat tombol Download Format, lalu isi ulang datanya.'
        ]);
        exit;
    }

    $no = 1;
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");

    mysqli_query($conn2, "delete from tbl_memorial_journal_temp where create_by = '$user'");

    foreach ($rows as $i => $row) {

        if ($i == 0) continue;

        // LEWATI BARIS KOSONG. Sheet UPLOAD punya ~65.000 baris sisa tanpa isi;
        // tanpa penjaga ini semuanya ikut terinsert sebagai baris jurnal kosong
        // (dan memicu Notice "array offset on null" bertubi-tubi).
        // Baris dianggap berisi kalau No Journal / No Coa / Debit / Credit terisi.
        $cek = trim((string) ($row[0] ?? '')) . trim((string) ($row[3] ?? ''))
             . trim((string) ($row[13] ?? '')) . trim((string) ($row[14] ?? ''));
        if ($cek === '') continue;

        // URUTAN KOLOM TEMPLATE (ikut format_upload.xls sheet UPLOAD):
        //  0 No Journal        6 No Reference       12 Currency
        //  1 Tanggal Journal   7 Tanggal Reference  13 Debit
        //  2 Id Type           8 No Faktur          14 Credit
        //  3 No Coa            9 Tanggal Faktur     15 Keterangan
        //  4 No Profit Center 10 Buyer              16 Supplier
        //  5 No Cost Center   11 No_ws              17 Flag
        // TANGGAL: strtotime() mengembalikan false untuk sel kosong/tak terbaca,
        // dan date('Y-m-d', false) menghasilkan 1970-01-01 — bukan error, jadi
        // tanggal palsu itu tersimpan diam-diam. Kolomnya nullable, jadi yang
        // benar adalah NULL, bukan epoch.
        $no_journal     = $row[0];

        $ts_journal = strtotime((string) ($row[1] ?? ''));
        if (!$ts_journal) {
            // Tanggal jurnal WAJIB: baris tanpa tanggal yang sah akan jadi jurnal
            // tak bertanggal / bertanggal 1970. Tolak seluruh berkas dengan pesan
            // yang menunjuk barisnya, daripada menyimpan data rusak.
            echo json_encode([
                'status'  => 'error',
                'message' => 'Tanggal Journal tidak terbaca pada baris ke-' . ($i + 1)
                           . ' (kolom B, isi: "' . trim((string) ($row[1] ?? '')) . '"). '
                           . 'Pakai format bulan/tanggal/tahun, mis. 06/29/2026.'
            ]);
            exit;
        }
        $tgl_journal    = date('Y-m-d', $ts_journal);
        $id_type        = $row[2];
        $no_coa         = $row[3];
        $id_pc          = $row[4];
        $no_cc          = $row[5];
        $no_reff        = $row[6];
        // Tanggal Reference BOLEH kosong (banyak jurnal tidak punya dokumen
        // referensi). Kosong -> NULL, bukan 1970-01-01. Strip "-" ikut dianggap
        // "tidak ada tanggal", sesuai kebiasaan dokumen di aplikasi ini.
        $tgl_reff_raw = trim((string) ($row[7] ?? ''));
        $tgl_reff_sql = 'NULL';
        if ($tgl_reff_raw !== '' && $tgl_reff_raw !== '-') {
            $ts_reff = strtotime($tgl_reff_raw);
            if ($ts_reff) { $tgl_reff_sql = "'" . date('Y-m-d', $ts_reff) . "'"; }
        }
        $buyer          = $row[10];
        $no_ws          = $row[11];
        $curr           = $row[12];
        $debit          = $row[13];
        $credit         = $row[14];
        $keterangan     = $row[15];
        $flag           = $row[17];
        $status         = 'Post';

        // Kolom faktur & supplier. Di-escape karena nama supplier boleh mengandung
        // apostrof (mis. PT. O'BRIEN) yang akan mematahkan INSERT kalau ditempel mentah.
        $no_faktur_raw  = isset($row[8])  ? trim((string) $row[8])  : '';
        $tgl_faktur_raw = isset($row[9])  ? trim((string) $row[9])  : '';
        $supplier_raw   = isset($row[16]) ? trim((string) $row[16]) : '';

        $no_faktur = mysqli_real_escape_string($conn2, $no_faktur_raw);
        $supplier  = mysqli_real_escape_string($conn2, $supplier_raw);

        // Tanggal faktur boleh kosong -> NULL, bukan 1970-01-01. Strip "-" juga
        // dianggap "tidak ada tanggal", sesuai kebiasaan dokumen di aplikasi ini.
        $tgl_faktur_sql = 'NULL';
        if ($tgl_faktur_raw !== '' && $tgl_faktur_raw !== '-') {
            $ts_fak = strtotime($tgl_faktur_raw);
            if ($ts_fak) {
                $tgl_faktur_sql = "'" . date('Y-m-d', $ts_fak) . "'";
            }
        }

        $sql_pc = mysqli_query($conn2,"select kode_pc from master_pc where id_pc = '$id_pc' GROUP BY id_pc");
        $row_pc = mysqli_fetch_array($sql_pc);
        $kode_pc = $row_pc['kode_pc'];

        // Baris IDR: rate SELALU 1, jangan cari ke masterrate (nilai sudah IDR).
        // Kalau tidak dikunci, baris IDR ikut dikali kurs -> nilai IDR meledak.
        if (strtoupper(trim($curr)) === 'IDR') {
            $rate = 1;
        } else {
            $sql_rate = mysqli_query($conn2,"select TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM rate)) rate from ap_masterrate where tanggal = '$tgl_journal' and v_codecurr = 'PAJAK' and curr = '$curr'");
            $row_rate = mysqli_fetch_array($sql_rate);
            $rate = $row_rate['rate'] ?? '1';
        }

        $debit_idr = $debit * $rate;
        $credit_idr = $credit * $rate;


        mysqli_query($conn2, "
            INSERT INTO tbl_memorial_journal_temp 
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, faktur_pajak, tgl_faktur_pajak, supplier, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center )
            VALUES
            ('$no_journal','$tgl_journal','$id_type', '$no_coa','$no_cc','$no_reff',$tgl_reff_sql, '$no_faktur',$tgl_faktur_sql,'$supplier', '$buyer','$no_ws','$curr','$rate', '$debit','$credit','$debit_idr','$credit_idr', '$keterangan','$flag','$user', '$create_date','$kode_pc')");

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
