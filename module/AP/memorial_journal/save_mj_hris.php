<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../../conn/conn.php';
require_once __DIR__ . '/mj_rate_helper.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Stempel no_journal di HRIS ($conn3) berada DI LUAR transaksi $conn2 - beda
// server, tidak ikut rollback. Kalau penyimpanan jurnal gagal setelah stempel
// terpasang, periode itu lenyap dari daftar (query penarik menyaring
// "no_journal is null") padahal jurnalnya tidak pernah jadi. Dua variabel di
// bawah mencatat stempel yang sudah terpasang supaya bisa dicabut di catch.
$hris_stamped_tbl = '';
$hris_stamped_per = '';

mysqli_begin_transaction($conn2);

try {

    file_put_contents('debug_log.txt', "\n\n=== NEW SAVE ===\n", FILE_APPEND);
    file_put_contents('debug_log.txt', print_r($_POST, true), FILE_APPEND);


    $mj_date = date('Y-m-d', strtotime($_POST['mj_date2']));
    $mj_type = $_POST['mj_type2'];

    // ========================================================================
    // SUMBER DATA HRIS — 'jurnal' vs 'jurnal_akhir'
    //
    // Sejak Sep 2026 HRIS memecah payroll menjadi dua tabel berstruktur sama:
    //   jurnal       -> periode berjalan, mis. 26 Agu s/d 25 Sep (perilaku lama)
    //   jurnal_akhir -> potongan akhir bulan, mis. 26 Agu s/d 31 Agu
    //
    // Untuk 'jurnal_akhir', beban itu diakui di AKHIR BULAN lalu DIBALIK di awal
    // bulan berikutnya — supaya tidak terhitung dua kali ketika periode normal
    // (yang juga memuat tanggal 26-31 tersebut) dijurnal bulan depan. Jadi satu
    // kali Save membentuk DUA nomor jurnal sekaligus:
    //   1. jurnal akhir bulan -> tanggal LAST_DAY(periode), baris apa adanya
    //   2. jurnal pembalik    -> tanggal 1 bulan berikutnya, debit <-> credit
    //
    // Tanggalnya diturunkan dari PERIODE, bukan dari field Date. Di layar field
    // Date memang sudah dikunci saat mode ini dipilih, tetapi form tetap bisa
    // dikirim langsung tanpa lewat layar — jadi server yang menentukan.
    // Nilai dari luar dipetakan lewat daftar putih, tidak pernah masuk ke SQL
    // apa adanya. Kategori selain WAGES & SALARY tidak mengenal pemisahan ini.
    // ========================================================================
    $hris_source = ($_POST['hris_source'] ?? 'jurnal') === 'jurnal_akhir' ? 'jurnal_akhir' : 'jurnal';
    $is_akhir    = ($hris_source === 'jurnal_akhir' && $mj_type === 'CMJ001');
    $mj_date_rev = '';

    if ($is_akhir) {
        $periode_raw = trim((string) ($_POST['hris_date'] ?? ''));
        $periode_ts  = $periode_raw !== '' ? strtotime($periode_raw) : false;
        if ($periode_ts === false) {
            throw new Exception("Period Filter wajib diisi untuk sumber Month-End.");
        }
        // Tanggal 1 dipakai sebagai titik tolak +1 month supaya tidak terpeleset:
        // strtotime('2026-01-31 +1 month') menghasilkan 3 Maret, bukan Februari.
        $awal_periode = date('Y-m-01', $periode_ts);
        $mj_date      = date('Y-m-t', $periode_ts);
        $mj_date_rev  = date('Y-m-01', strtotime($awal_periode . ' +1 month'));
    }

    // CLOSING PERIODE — dicek DI SERVER, bukan cuma di datepicker. Jurnal tidak
    // boleh masuk ke periode yang bukunya sudah ditutup. Mode Month-End menyentuh
    // DUA bulan sekaligus, jadi keduanya diperiksa di muka: kalau pembaliknya
    // jatuh di periode tertutup, seluruh transaksi dibatalkan — bukan separuh
    // tersimpan lalu menyisakan jurnal akhir bulan tanpa pembalik.
    require_once __DIR__ . '/../closing_periode_guard.php';
    $errClose = closing_error($conn2, $mj_date);
    if ($errClose !== '') { throw new Exception($errClose); }
    if ($is_akhir) {
        $errCloseRev = closing_error($conn2, $mj_date_rev);
        if ($errCloseRev !== '') { throw new Exception("Jurnal pembalik: " . $errCloseRev); }
    }

    $profit_center = $_POST['profit_center2'];
    $description = $_POST['pesan2'];
    $rate = $_POST['rate_mj2'] ?? 1;
    $fil_sb1 = $_POST['fil_sb1'];
    $rate = str_replace(',', '', $rate);

    $bulan = date('m', strtotime($mj_date));
    $tahun = date('y', strtotime($mj_date));
    $status = "Post";
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");
    $tgl_hris =  date("Y-m",strtotime($_POST['hris_date']));
    $tgl_hris_input =  date("Y-m-d",strtotime($_POST['hris_date']));

    // ================= CMJ =================
    $sqlcmj = mysqli_query($conn2, "select nama_cmj from master_category_mj where id_cmj = '$mj_type'");
    if(!$sqlcmj){
        throw new Exception("ERROR select master_category_mj: ".mysqli_error($conn2));
    }

    $rowcmj = mysqli_fetch_array($sqlcmj);
    $nama_cmj = $rowcmj['nama_cmj'];

    $prefix = "GM/NAG/" . $bulan . $tahun;

    // ================= NO MJ =================
    $sql = mysqli_query($conn2, "
    SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
    FROM tbl_memorial_journal
    WHERE no_mj LIKE '$prefix%'
    ");

    if(!$sql){
        throw new Exception("ERROR generate no_mj: ".mysqli_error($conn2));
    }

    $row = mysqli_fetch_assoc($sql);
    $urutan = ($row['max_urut'] ?? 0) + 1;
    $no_mj = $prefix . "/" . sprintf("%05d", $urutan);

    // ================= NO MJ SB =================
    $sql_sb = mysqli_query($conn2, "
    SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
    FROM sb_memorial_journal
    WHERE no_mj LIKE '$prefix%'
    ");

    if(!$sql_sb){
        throw new Exception("ERROR generate no_mj_sb: ".mysqli_error($conn2));
    }

    $row_sb = mysqli_fetch_assoc($sql_sb);
    $urutan_sb = ($row_sb['max_urut'] ?? 0) + 1;
    $no_mj_sb = $prefix . "/" . sprintf("%05d", $urutan_sb);

    // ================= NO MJ PEMBALIK =================
    // Pembalik selalu jatuh di BULAN BERIKUTNYA, jadi prefiksnya berbeda dan
    // nomornya tidak mungkin bentrok dengan nomor jurnal akhir bulan di atas.
    // Seluruh MAX() dijalankan sebelum INSERT apa pun, jadi urutannya tetap rapi.
    $no_mj_rev    = '';
    $no_mj_rev_sb = '';

    if ($is_akhir) {
        $prefix_rev = "GM/NAG/" . date('m', strtotime($mj_date_rev)) . date('y', strtotime($mj_date_rev));

        $sql_rev = mysqli_query($conn2, "
        SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
        FROM tbl_memorial_journal
        WHERE no_mj LIKE '$prefix_rev%'
        ");
        if(!$sql_rev){
            throw new Exception("ERROR generate no_mj pembalik: ".mysqli_error($conn2));
        }
        $row_rev   = mysqli_fetch_assoc($sql_rev);
        $no_mj_rev = $prefix_rev . "/" . sprintf("%05d", ($row_rev['max_urut'] ?? 0) + 1);

        $sql_rev_sb = mysqli_query($conn2, "
        SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
        FROM sb_memorial_journal
        WHERE no_mj LIKE '$prefix_rev%'
        ");
        if(!$sql_rev_sb){
            throw new Exception("ERROR generate no_mj_sb pembalik: ".mysqli_error($conn2));
        }
        $row_rev_sb   = mysqli_fetch_assoc($sql_rev_sb);
        $no_mj_rev_sb = $prefix_rev . "/" . sprintf("%05d", ($row_rev_sb['max_urut'] ?? 0) + 1);
    }

    // ================= DAFTAR JURNAL YANG AKAN DIBENTUK =================
    // Satu entri = satu nomor jurnal. Mode normal menghasilkan SATU entri —
    // persis seperti sebelumnya. Mode Month-End menambahkan entri kedua yang
    // sisinya dibalik. Baris detailnya sendiri sama, hanya dibaca dua kali.
    $jurnal_set = [[
        'no_mj'    => $no_mj,
        'no_mj_sb' => $no_mj_sb,
        'tgl'      => $mj_date,
        'balik'    => false,
    ]];

    if ($is_akhir) {
        $jurnal_set[] = [
            'no_mj'    => $no_mj_rev,
            'no_mj_sb' => $no_mj_rev_sb,
            'tgl'      => $mj_date_rev,
            'balik'    => true,
        ];
    }

    // ================= STATUS =================
    if ($fil_sb1 == '1') {
        foreach ($jurnal_set as $j) {
            $j_no  = $j['no_mj'];
            $j_sb  = $j['no_mj_sb'];
            $j_tgl = $j['tgl'];

            $q = mysqli_query($conn2, "
            INSERT INTO status_memorial_journal
            (no_mj, mj_date, no_mj_sb, status, create_by, create_date)
            VALUES
            ('$j_no', '$j_tgl', '$j_sb', 'Post', '$user', '$create_date')
            ");

            if(!$q){
                throw new Exception("ERROR status_memorial_journal ($j_no): ".mysqli_error($conn2));
            }
        }
    }

    // ================= CONN3 =================
    if ($mj_type == 'CMJ001') {
        // Distempel ke TABEL YANG DIBACA. ajx_get_data_hris.php menyaring
        // "where no_journal is null", jadi stempel inilah yang menahan satu
        // periode terjurnal dua kali. Yang disimpan nomor jurnal UTAMA saja;
        // nomor pembaliknya bisa ditelusuri dari keterangan ("EX <no utama>").
        $q = mysqli_query($conn3, "update `$hris_source` set no_journal = '$no_mj' where periode_payroll = '$tgl_hris'");
        if(!$q){
            throw new Exception("ERROR update $hris_source (conn3): ".mysqli_error($conn3));
        }
        $hris_stamped_tbl = $hris_source;
        $hris_stamped_per = $tgl_hris;
    }

    if ($mj_type == 'CMJ003') {
        $cek = mysqli_query($conn3, "
        SELECT 1 FROM log_jurnal_bpjs WHERE no_journal = '$no_mj' LIMIT 1
    ");

    if(!$cek){
        throw new Exception("ERROR cek log_jurnal_bpjs: ".mysqli_error($conn3));
    }

    if(mysqli_num_rows($cek) == 0){

        $q = mysqli_query($conn3, "
        INSERT INTO log_jurnal_bpjs
        (no_journal, tgl_journal, status, created_by, created_date)
        VALUES
        ('$no_mj', '$tgl_hris_input', 'POST', '$user', '$create_date')
        ");

        if(!$q){
            throw new Exception("DB ERROR: INSERT log_jurnal_bpjs => ".mysqli_error($conn3));
        }
    }
}

    // ================= DETAIL =================
    $detail = json_decode($_POST['detail'], true);

    if (!$detail) {
        throw new Exception("JSON detail error");
    }

    // VALIDASI BALANCE
    $total_d = 0;
    $total_c = 0;

    foreach ($detail as $row) {
        $total_d += floatval($row['debit']);
        $total_c += floatval($row['credit']);
    }

    if (round($total_d, 2) != round($total_c, 2)) {
        throw new Exception("NOT BALANCE");
    }

    // ================= INSERT DETAIL =================
    // Lapis luar = nomor jurnal (satu untuk mode normal, dua untuk Month-End).
    foreach ($jurnal_set as $j) {

        $j_no    = $j['no_mj'];
        $j_sb    = $j['no_mj_sb'];
        $j_tgl   = $j['tgl'];
        $j_balik = $j['balik'];

        foreach ($detail as $i => $row) {

            $pcdet        = $row['profit_center'];
            if ($pcdet == 'NIRWANA ALABARE GARMENT') {
                $pc_det = 'NAG';
            }elseif ($pcdet == 'NIRWANA ALABARE KNITTING') {
                $pc_det = 'NAK';
            }else{
                $pc_det = $pcdet;
            }
            $no_coa        = $row['no_coa'];
            $nama_coa      = $row['nama_coa'];
            $no_cc         = $row['no_cc'];
            $cc_name       = $row['cc_name'];
            $reff          = $row['reff_number'];
            $reff_date     = $row['reff_date'];
            $buyer         = $row['buyer'];
            $ws            = $row['ws'];
            $curr          = $row['curr'];
            $debit         = floatval($row['debit']);
            $credit        = floatval($row['credit']);
            $ket           = $row['deskripsi'];

            // PEMBALIK: sisi ditukar, dan keterangannya diberi penanda + rujukan
            // ke nomor jurnal asalnya. Bentuknya ditentukan user:
            //   REKLAS <keterangan jurnal akrual> EX <no jurnal akrual>
            // mis. "REKLAS LEMBUR DEPT DRIVER 26 - 31 MAY 2026 EX GM/NAG/0526/00038"
            // - sama dengan yang selama ini ditulis manual, supaya mudah dicari.
            if ($j_balik) {
                $tukar  = $debit;
                $debit  = $credit;
                $credit = $tukar;
                $ket    = "REKLAS " . $ket . " EX " . $no_mj;
            }

            // KURS: sebelumnya $rate dibaca dari $_POST['rate_mj2'] lalu TIDAK PERNAH
            // dipakai — keempat INSERT di bawah menulis rate '1' dan debit_idr = debit
            // secara harfiah. Itu hanya benar selama data HRIS selalu IDR; satu baris
            // non-IDR saja sudah membuat nilai IDR-nya salah tanpa peringatan apa pun.
            // Sekarang kurs diambil dari TANGGAL JURNAL memakai kurs PAJAK untuk mata
            // uang baris ini (IDR tetap 1). $_POST['rate_mj2'] tidak dioper sebagai override
            // karena itu pun lookup USD otomatis, bukan ketikan manual.
            // Jurnal pembalik memakai TANGGALNYA SENDIRI, bukan tanggal jurnal asal.
            // Lihat mj_rate_helper.php.
            $rate_det   = mj_resolve_rate($conn2, $curr, $j_tgl, null);
            $debit_idr  = $debit * $rate_det;
            $credit_idr = $credit * $rate_det;

            // tbl_memorial_journal
            $q1 = mysqli_query($conn2, "
            INSERT INTO tbl_memorial_journal
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center)
            VALUES
            ('$j_no', '$j_tgl', '$mj_type', '$no_coa', '$no_cc', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '$rate_det', '$debit', '$credit', '$debit_idr', '$credit_idr', '$ket', '$status', '$user', '$create_date', '$pc_det')
            ");

            if(!$q1){
                throw new Exception("ERROR tbl_memorial_journal ($j_no row $i): ".mysqli_error($conn2));
            }

            // tbl_list_journal
            $q2 = mysqli_query($conn2, "
            INSERT INTO tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$j_no', '$j_tgl', '$nama_cmj', '$no_coa', '$nama_coa', '$no_cc', '$cc_name', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '$rate_det', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$ket', '$user', '$create_date', '', '', '', '', '$pc_det')
            ");

            if(!$q2){
                throw new Exception("ERROR tbl_list_journal ($j_no row $i): ".mysqli_error($conn2));
            }

            if ($fil_sb1 == '1') {

                $q3 = mysqli_query($conn2, "
                INSERT INTO sb_memorial_journal
                (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center)
                VALUES
                ('$j_sb', '$j_tgl', '$mj_type', '$no_coa', '$no_cc', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '$rate_det', '$debit', '$credit', '$debit_idr', '$credit_idr', '$ket', '$status', '$user', '$create_date', 'Input SB2', '$pc_det')
                ");

                if(!$q3){
                    throw new Exception("ERROR sb_memorial_journal ($j_sb row $i): ".mysqli_error($conn2));
                }

                $q4 = mysqli_query($conn2, "
                INSERT INTO sb_list_journal
                (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
                VALUES
                ('$j_sb', '$j_tgl', '$nama_cmj', '$no_coa', '$nama_coa', '$no_cc', '$cc_name', '$reff', '$reff_date', '$buyer', '$ws', '$curr', '$rate_det', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$ket', '$user', '$create_date', '', '', '', '', '$pc_det')
                ");

                if(!$q4){
                    throw new Exception("ERROR sb_list_journal ($j_sb row $i): ".mysqli_error($conn2));
                }
            }
        }
    }

    mysqli_commit($conn2);

    file_put_contents('debug_log.txt', "\nCOMMIT SUCCESS\n", FILE_APPEND);

    echo json_encode([
        'status' => 'success',
        'no_journal' => $no_mj,
        // Diisi HANYA pada mode Month-End. Halaman memakai keberadaannya untuk
        // memutuskan apakah dialog sukses menampilkan satu nomor atau dua.
        'no_journal_reverse' => $no_mj_rev,
        'mj_date' => $mj_date,
        'mj_date_reverse' => $mj_date_rev,
        'hris_source' => $hris_source
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    // Cabut kembali stempel di HRIS - lihat catatan di atas mysqli_begin_transaction.
    // Hanya baris yang BARU distempel nomor ini yang disentuh, jadi periode yang
    // memang sudah punya nomor jurnal lama tidak ikut dikosongkan.
    if ($hris_stamped_tbl !== '' && !empty($no_mj)) {
        mysqli_query($conn3, "update `$hris_stamped_tbl` set no_journal = null
            where periode_payroll = '$hris_stamped_per' and no_journal = '$no_mj'");
    }

    file_put_contents('debug_log.txt', "\nROLLBACK\n", FILE_APPEND);
    file_put_contents('debug_log.txt', "\nERROR: " . $e->getMessage(), FILE_APPEND);

    echo json_encode([
        'status' => 'error',
        'msg' => $e->getMessage()
    ]);
}
