<?php
// =========================================================================
// AUTO JURNAL SELISIH KURS - revaluasi saldo bank USD, 1 JURNAL PER TANGGAL.
//
// Fungsi-fungsi murni (kalkulasi + posting) dipisah ke file ini supaya bisa
// dipakai bareng oleh:
//   - auto-jurnal-selisih-kurs.php (form web, manual, ada Preview/Post/
//     konfirmasi SweetAlert)
//   - auto-jurnal-selisih-kurs-scheduler.php (dipanggil OTOMATIS oleh
//     scheduler/cron, tanpa sesi login, tanpa konfirmasi manual, langsung
//     Post)
// (pola yang sama seperti cfr_functions.php utk report-cashflow-realisation.php)
//
// Model "rolling/crystallize harian": tiap tanggal D dari start_date s.d.
// end_date dievaluasi (BUKAN cuma tanggal yang ada transaksinya - saldo
// bisa tetap "beda nilai" murni karena kurs HARIAN bergerak walau tidak ada
// transaksi hari itu). Saldo buku di-"tutup"/crystallize ULANG tiap
// tanggal - opening tanggal D = saldo PASAR (market) tanggal SEBELUMNYA
// yang sudah dihitung (bukan akumulasi terus dari 1 titik jauh di awal):
//   saldo_native_D = saldo native (mata uang asli) SETELAH transaksi
//                    tanggal D (running total dari b_saldoawal_bank + semua
//                    mutasi b_reportbank s.d. tanggal D; kalau D tidak ada
//                    transaksi, sama dengan saldo_native_(D-1)).
//   saldo_buku_D   = opening_D (= saldo_pasar_(D-1), atau utk tanggal
//                    pertama yg diproses = saldo native sebelum periode x
//                    kurs HARIAN sehari sebelum start_date, persis Beginning
//                    Balance Eq IDR bankreport.php) + transaksi BARU tanggal
//                    D (kalau ada) dinilai pakai kurs PAJAK tanggal D - ini
//                    rate yang SESUNGGUHNYA dipakai saat transaksi bank asli
//                    diposting ke GL (bukan HARIAN - sudah diverifikasi
//                    langsung ke tbl_list_journal, rate PAJAK yg dipakai).
//   saldo_pasar_D  = saldo_native_D x kurs HARIAN di tanggal D itu sendiri
//                    (revaluasi ulang tiap tanggal, bukan cuma di akhir
//                    periode).
//   selisih_D      = saldo_pasar_D - saldo_buku_D
// saldo_pasar_D lalu jadi opening ("saldo buku") utk tanggal berikutnya yg
// diproses - jadi PASTI 0 kalau tidak ada transaksi DAN kurs HARIAN tidak
// berubah dari hari sebelumnya (opening == market karena native & rate sama
// persis), tapi bisa != 0 walau tidak ada transaksi kalau kurs HARIAN
// bergerak.
// Kalau selisih_D != 0, itu jadi 1 jurnal untuk tanggal D (nomor dokumen
// sendiri, urut per tanggal) - TIDAK PERNAH digabung lintas tanggal.
//
// Kalau tanggal D SUDAH PERNAH ada jurnal FX sebelumnya (dicek dari
// b_reportbank: akun+tanggal+prefix no_doc "FX/"), di-UPDATE (pakai nomor
// dokumen yang sama, angka diganti yang terbaru) - BUKAN insert baris baru
// (mencegah dobel kalau tool ini dijalankan ulang/lewat scheduler harian
// untuk tanggal yang sama).
//
// selisih > 0 (untung)  -> COA bank di-DEBIT (kesan "Bank In"), COA 8.52.01
//                          di-KREDIT.
// selisih < 0 (rugi)    -> COA bank di-KREDIT (kesan "Bank Out"), COA 8.52.01
//                          di-DEBIT.
// Nilai yang dibukukan SELALU dalam IDR (curr IDR, rate 1) - walau akun
// banknya sendiri USD, baris jurnal penyesuaian ini murni penyesuaian nilai
// IDR, tidak ada mata uang asli yang benar-benar berpindah.
//
// Ditulis ke 3 tempat sekaligus (BUKAN lewat alur Bank In/Bank Out
// manual/approval biasa):
//   - b_reportbank      (id_cash_flow = 55 "Pengakuan Kerugian Selisih
//                        Kurs", dipakai utk untung MAUPUN rugi, nilainya
//                        sendiri yang plus/minus).
//   - tbl_list_journal  (2 baris/leg per tanggal - COA bank vs COA 8.52.01,
//                        COA 8.52.01 wajib punya cost center DEP06SUB001).
//   - tbl_memorial_journal (mirror dari tbl_list_journal, kategori CMJ021
//                        "FOREX GAIN OR LOSS" - dibuat otomatis kalau belum
//                        ada di master_category_mj).
// Nomor dokumen "FX/{kode_bank}/{profit_center}/{mmyy}/{00001}"
// (mis. FX/BCA1979/NAG/0826/00001) - series sendiri, terpisah dari seri
// BM/BK Bank In/Out biasa maupun GM Memorial Journal manual.
//
// Batas bawah keras: tidak pernah memproses tanggal < FXSK_MIN_DATE.
// Tanggal yang periodenya (bulan+tahun) sudah "Closed" di
// tbl_closing_periode juga dilewati (tidak dijurnal), tapi saldo native/
// saldo buku tetap jalan terus melewatinya supaya tanggal SETELAHNYA tetap
// benar - lihat fxSkCalcRevaluation().
// =========================================================================

define('FXSK_COA_SELISIH', '8.52.01');
define('FXSK_CC_SELISIH', 'DEP06SUB001');
define('FXSK_ID_CASH_FLOW', 55);
define('FXSK_ID_CMJ', 'CMJ021');
define('FXSK_ID_CMJ_NAME', 'FOREX GAIN OR LOSS');
define('FXSK_MIN_DATE', '2026-08-01');

// Format tanggal "10 Agustus 2026" (nama bulan lengkap bahasa Indonesia) -
// dipakai di deskripsi jurnal, bukan "10-08-2026".
function fxSkTanggalIndo($date) {
    $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulanIndo[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

// Kurs HARIAN terdekat on/before $date, curr USD - dipakai utk revaluasi
// (saldo_pasar) tiap tanggal.
function fxSkNearestRate($conn, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    $sql = mysqli_query($conn, "select rate from masterrate where v_codecurr = 'HARIAN' and curr = 'USD' and tanggal <= '$d' order by tanggal desc limit 1");
    $row = mysqli_fetch_assoc($sql);
    return $row ? (float) $row['rate'] : 1.0;
}

// Kurs PAJAK terdekat on/before $date, curr USD - dipakai utk menilai
// transaksi BARU yang terjadi di tanggal itu (rate yang sesungguhnya
// dipakai saat transaksi bank diposting ke GL, persis logika
// insert_bankin_arc.php).
function fxSkPajakRate($conn, $date) {
    $d = mysqli_real_escape_string($conn, $date);
    $sql = mysqli_query($conn, "select rate from masterrate where v_codecurr = 'PAJAK' and curr = 'USD' and tanggal <= '$d' order by tanggal desc limit 1");
    $row = mysqli_fetch_assoc($sql);
    return $row ? (float) $row['rate'] : 1.0;
}

// Apakah periode akuntansi (bulan+tahun) dari $date masih "Open" di
// tbl_closing_periode? Kalau sudah "Closed", tanggal itu tidak boleh
// dijurnal (tidak boleh insert/update apapun ke periode yang sudah
// ditutup). Kalau tidak ketemu barisnya sama sekali (harusnya tidak
// pernah terjadi karena tabel ini sudah di-pre-populate jauh ke depan),
// default permisif (dianggap Open) supaya tidak diam-diam memblokir tanpa
// alasan jelas.
function fxSkIsPeriodOpen($conn, $date) {
    $bulan = date('m', strtotime($date));
    $tahun = date('Y', strtotime($date));
    $sql = mysqli_query($conn, "select status_closing from tbl_closing_periode where bulan = '$bulan' and tahun = '$tahun' limit 1");
    $row = mysqli_fetch_assoc($sql);
    return !$row || $row['status_closing'] !== 'Closed';
}

// No. dokumen jurnal FX yang SUDAH ADA untuk akun+tanggal ini, kalau ada -
// dipakai untuk mode UPDATE (bukan insert baru) waktu tool ini dijalankan
// ulang untuk tanggal yang sama.
function fxSkFindExistingDoc($conn, $account, $date) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $dateEsc = mysqli_real_escape_string($conn, $date);
    $sql = mysqli_query($conn, "select no_doc from b_reportbank where akun = '$accEsc' and transaksi_date = '$dateEsc' and no_doc like 'FX/%' and status != 'Cancel' limit 1");
    $row = mysqli_fetch_assoc($sql);
    return $row ? $row['no_doc'] : null;
}

// Hitung revaluasi per TANGGAL KALENDER (setiap tanggal, bukan cuma yang
// ada transaksinya - saldo tetap bisa "beda nilai" murni krn kurs HARIAN
// bergerak) untuk 1 akun dalam rentang $startDate s.d. $endDate. Saldo buku
// di-crystallize ulang tiap tanggal (opening_D = saldo pasar D-1) - lihat
// komentar besar di atas file ini utk penjelasan lengkap formulanya. Cuma
// tanggal dengan selisih != 0 yang dikembalikan (selisih pasti 0 kalau
// tidak ada transaksi DAN kurs HARIAN tidak berubah dari hari sebelumnya).
function fxSkCalcRevaluation($conn, $account, $startDate, $endDate) {
    // Batas bawah keras - tidak pernah memproses/menjurnal tanggal sebelum
    // ini, berapapun start_date yang diminta caller.
    if ($startDate < FXSK_MIN_DATE) {
        $startDate = FXSK_MIN_DATE;
    }
    if ($endDate < $startDate) {
        return [];
    }

    $accEsc = mysqli_real_escape_string($conn, $account);
    $edEsc = mysqli_real_escape_string($conn, $endDate);

    $sqlBase = mysqli_query($conn, "select amount from b_saldoawal_bank where account = '$accEsc'");
    $rowBase = mysqli_fetch_assoc($sqlBase);
    $runningNative = $rowBase ? (float) $rowBase['amount'] : 0.0;

    // no_doc not like 'FX/%' - baris penyesuaian selisih kurs yang DIBUAT
    // OLEH TOOL INI SENDIRI (curr=IDR) harus dikecualikan dari saldo native,
    // supaya tool ini tidak "memakan" hasil jurnalnya sendiri kalau
    // dijalankan ulang utk tanggal yang sama/berikutnya (insert-or-update
    // harus tetap idempotent).
    $sqlTx = mysqli_query($conn, "select transaksi_date, debit, credit from b_reportbank
        where akun = '$accEsc' and status != 'Cancel' and no_doc not like 'FX/%' and transaksi_date <= '$edEsc'
        order by transaksi_date asc, id asc");
    $txByDate = [];
    while ($r = mysqli_fetch_assoc($sqlTx)) {
        $txByDate[$r['transaksi_date']][] = $r;
    }

    // Native SEBELUM start_date numpuk dulu (titik awal saldo buku cuma 1x
    // konversi, persis Beginning Balance Eq IDR di bankreport.php).
    foreach ($txByDate as $date => $txs) {
        if ($date >= $startDate) {
            break;
        }
        foreach ($txs as $tx) {
            $runningNative += (float) $tx['debit'] - (float) $tx['credit'];
        }
    }
    $seedRate = fxSkNearestRate($conn, date('Y-m-d', strtotime($startDate . ' -1 day')));
    $openingIdr = $runningNative * $seedRate;

    $rows = [];
    $cursor = strtotime($startDate);
    $endTs = strtotime($endDate);
    while ($cursor <= $endTs) {
        $d = date('Y-m-d', $cursor);
        $cursor = strtotime('+1 day', $cursor);

        $pajakRate = fxSkPajakRate($conn, $d);
        $bookIdr = $openingIdr;
        $hasTransaction = isset($txByDate[$d]);
        if ($hasTransaction) {
            foreach ($txByDate[$d] as $tx) {
                $net = (float) $tx['debit'] - (float) $tx['credit'];
                $runningNative += $net;
                $bookIdr += $net * $pajakRate;
            }
        }

        $harianRate = fxSkNearestRate($conn, $d);
        $marketIdr = $runningNative * $harianRate;
        $selisih = round($marketIdr - $bookIdr, 2);

        // Saldo pasar hari ini jadi opening ("saldo buku") tanggal
        // berikutnya - crystallize harian, bukan akumulasi dari 1 titik -
        // ini HARUS tetap jalan (dan native tetap diakumulasi di atas) walau
        // tanggal ini periodenya sudah closed, supaya saldo tanggal
        // SETELAHNYA tetap benar. Yang di-skip cuma pembuatan jurnalnya.
        $openingIdr = $marketIdr;

        if ($selisih == 0) {
            continue;
        }
        if (!fxSkIsPeriodOpen($conn, $d)) {
            continue;
        }

        $rows[] = [
            'date' => $d,
            'native_balance' => $runningNative,
            'pajak_rate' => $pajakRate,
            'harian_rate' => $harianRate,
            'book_idr' => round($bookIdr, 2),
            'market_idr' => round($marketIdr, 2),
            'selisih' => $selisih,
            'has_transaction' => $hasTransaction,
            'existing_doc' => fxSkFindExistingDoc($conn, $account, $d),
        ];
    }

    return $rows;
}

// Pastikan kategori CMJ021 "FOREX GAIN OR LOSS" ada di master_category_mj
// dgn nama yang benar - insert kalau belum ada, atau update namanya kalau
// sudah ada tapi beda dari FXSK_ID_CMJ_NAME saat ini (idempotent, dan
// otomatis benerin nama lama kalau konstanta ini pernah diganti).
function fxSkEnsureCategory($conn) {
    $sql = mysqli_query($conn, "select id_cmj, nama_cmj from master_category_mj where id_cmj = '" . FXSK_ID_CMJ . "'");
    $row = mysqli_fetch_assoc($sql);
    if (!$row) {
        mysqli_query($conn, "insert into master_category_mj (id_cmj, nama_cmj) values ('" . FXSK_ID_CMJ . "', '" . FXSK_ID_CMJ_NAME . "')");
    } elseif ($row['nama_cmj'] !== FXSK_ID_CMJ_NAME) {
        mysqli_query($conn, "update master_category_mj set nama_cmj = '" . FXSK_ID_CMJ_NAME . "' where id_cmj = '" . FXSK_ID_CMJ . "'");
    }
}

// Nomor dokumen baru "FX/{b_code}/{profit_center}/{mmyy}/{00001}" - series
// sendiri (prefix "FX/"), sequence per prefix (per bank x per bulan) diambil
// dari MAX(no_doc) di b_reportbank yang match prefix itu, +1. $date =
// tanggal jurnal - menentukan segmen mmyy-nya.
function fxSkNextDocNo($conn, $bCode, $profitCenter, $date) {
    $mmyy = date('my', strtotime($date));
    $prefix = "FX/" . $bCode . "/" . $profitCenter . "/" . $mmyy;
    $prefixEsc = mysqli_real_escape_string($conn, $prefix);
    $sql = mysqli_query($conn, "select max(cast(right(no_doc,5) as unsigned)) as max_urut from b_reportbank where no_doc like '$prefixEsc%'");
    $row = mysqli_fetch_assoc($sql);
    $urutan = (isset($row['max_urut']) && $row['max_urut'] !== null) ? ((int) $row['max_urut'] + 1) : 1;
    return $prefix . "/" . sprintf('%05d', $urutan);
}

// Posting/update 1 jurnal (1 baris b_reportbank + 2 baris tbl_list_journal +
// 2 baris tbl_memorial_journal) untuk 1 akun x 1 TANGGAL - dipanggil di
// dalam transaksi DB oleh caller. Kalau $existingDoc diisi (jurnal FX untuk
// akun+tanggal ini sudah pernah ada), semua tabel di-UPDATE pakai nomor
// dokumen yang sama; kalau null, insert baris baru dengan nomor baru.
// $connRead = koneksi untuk lookup data master (mastercoa_v2) - konsisten
// dengan pola file lain di codebase ini (selalu $conn1 utk mastercoa_v2,
// mis. insert_memorial_journal.php, save_bankin_bankout.php). $connWrite =
// koneksi TEMPAT TRANSAKSI DB berjalan ($conn2) - dipakai utk semua
// insert/update ke b_reportbank/tbl_list_journal/tbl_memorial_journal DAN
// utk fxSkNextDocNo (supaya nomor urut per tanggal berikutnya, dalam 1 kali
// Post yang mencakup banyak tanggal, bisa "melihat" insert yang barusan
// terjadi di tanggal sebelumnya walau belum commit - connection lain tidak
// akan melihat baris yang belum di-commit ini).
function fxSkPostOrUpdateJournal($connRead, $connWrite, $account, $bankInfo, $date, $selisih, $existingDoc, $user) {
    $isGain = $selisih > 0;
    $amount = abs($selisih);
    $createDate = date('Y-m-d H:i:s');
    $ket = 'SELISIH KURS REVALUASI BANK ' . $account . ' PER ' . fxSkTanggalIndo($date);
    $ketEsc = mysqli_real_escape_string($connWrite, $ket);
    $accEsc = mysqli_real_escape_string($connWrite, $account);

    $sqlCoaBank = mysqli_query($connRead, "select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($connRead, $bankInfo['id_coa']) . "'");
    $rowCoaBank = mysqli_fetch_assoc($sqlCoaBank);
    $namaCoaBank = $rowCoaBank ? $rowCoaBank['nama_coa'] : $bankInfo['coa_name'];

    $sqlCoaSk = mysqli_query($connRead, "select nama_coa from mastercoa_v2 where no_coa = '" . FXSK_COA_SELISIH . "'");
    $rowCoaSk = mysqli_fetch_assoc($sqlCoaSk);
    $namaCoaSk = $rowCoaSk ? $rowCoaSk['nama_coa'] : 'LABA / (RUGI) SELISIH KURS SUDAH TEREALISASI';

    // COA selisih kurs (8.52.01) wajib punya cost center - DEP06SUB001
    // (Finance, Accounting & Tax). COA bank tetap tanpa cost center ('-'),
    // sama seperti pola entry lain (mis. Beban Administrasi Bank).
    $sqlCcSk = mysqli_query($connRead, "select cc_name from b_master_cc where no_cc = '" . FXSK_CC_SELISIH . "'");
    $rowCcSk = mysqli_fetch_assoc($sqlCcSk);
    $namaCcSk = $rowCcSk ? $rowCcSk['cc_name'] : 'FINANCE, ACCOUNTING & TAX';

    $debitBank = $isGain ? $amount : 0;
    $creditBank = $isGain ? 0 : $amount;
    $debitSk = $isGain ? 0 : $amount;
    $creditSk = $isGain ? $amount : 0;

    if ($existingDoc) {
        $doc = $existingDoc;
        $docEsc = mysqli_real_escape_string($connWrite, $doc);

        mysqli_query($connWrite, "update b_reportbank set deskripsi='$ketEsc', debit='$debitBank', credit='$creditBank', balance='$amount', id_cash_flow='" . FXSK_ID_CASH_FLOW . "'
            where no_doc='$docEsc' and akun='$accEsc' and transaksi_date='$date'");

        $bankCoaEsc = mysqli_real_escape_string($connWrite, $bankInfo['id_coa']);
        $namaCcSkEsc = mysqli_real_escape_string($connWrite, $namaCcSk);
        mysqli_query($connWrite, "update tbl_list_journal set type_journal='" . FXSK_ID_CMJ_NAME . "', debit='$debitBank', credit='$creditBank', debit_idr='$debitBank', credit_idr='$creditBank', keterangan='$ketEsc'
            where no_journal='$docEsc' and no_coa='$bankCoaEsc'");
        mysqli_query($connWrite, "update tbl_list_journal set type_journal='" . FXSK_ID_CMJ_NAME . "', no_costcenter='" . FXSK_CC_SELISIH . "', nama_costcenter='$namaCcSkEsc', debit='$debitSk', credit='$creditSk', debit_idr='$debitSk', credit_idr='$creditSk', keterangan='$ketEsc'
            where no_journal='$docEsc' and no_coa='" . FXSK_COA_SELISIH . "'");

        mysqli_query($connWrite, "update tbl_memorial_journal set debit='$debitBank', credit='$creditBank', debit_idr='$debitBank', credit_idr='$creditBank', keterangan='$ketEsc'
            where no_mj='$docEsc' and no_coa='$bankCoaEsc'");
        mysqli_query($connWrite, "update tbl_memorial_journal set no_costcenter='" . FXSK_CC_SELISIH . "', debit='$debitSk', credit='$creditSk', debit_idr='$debitSk', credit_idr='$creditSk', keterangan='$ketEsc'
            where no_mj='$docEsc' and no_coa='" . FXSK_COA_SELISIH . "'");

        return ['doc' => $doc, 'action' => 'update'];
    }

    $doc = fxSkNextDocNo($connWrite, $bankInfo['b_code'], $bankInfo['profit_center_bank'], $date);

    mysqli_query($connWrite, "insert into b_reportbank
        (transaksi_date, no_doc, deskripsi, akun, categori, cf_categori, curr, debit, credit, balance, status, id_cash_flow)
        values
        ('$date', '$doc', '$ketEsc', '$accEsc', '', '', 'IDR', '$debitBank', '$creditBank', '$amount', 'Post', '" . FXSK_ID_CASH_FLOW . "')");

    $legs = [
        [$bankInfo['id_coa'], $namaCoaBank, $debitBank, $creditBank, '-', '-'],
        [FXSK_COA_SELISIH, $namaCoaSk, $debitSk, $creditSk, FXSK_CC_SELISIH, $namaCcSk],
    ];
    foreach ($legs as $leg) {
        [$coa, $namaCoa, $debit, $credit, $cc, $namaCc] = $leg;
        $coaEsc = mysqli_real_escape_string($connWrite, $coa);
        $namaCoaEsc = mysqli_real_escape_string($connWrite, $namaCoa);
        $ccEsc = mysqli_real_escape_string($connWrite, $cc);
        $namaCcEsc = mysqli_real_escape_string($connWrite, $namaCc);
        mysqli_query($connWrite, "insert into tbl_list_journal
            (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            values
            ('$doc', '$date', '" . FXSK_ID_CMJ_NAME . "', '$coaEsc', '$namaCoaEsc', '$ccEsc', '$namaCcEsc', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Post', '$ketEsc', '$user', '$createDate', '', '', '', '', '" . $bankInfo['profit_center_bank'] . "')");

        mysqli_query($connWrite, "insert into tbl_memorial_journal
            (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center)
            values
            ('$doc', '$date', '" . FXSK_ID_CMJ . "', '$coaEsc', '$ccEsc', '-', '', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', '$ketEsc', 'Post', '$user', '$createDate', '" . $bankInfo['profit_center_bank'] . "')");
    }

    return ['doc' => $doc, 'action' => 'insert'];
}

// Semua akun bank USD aktif - jadi target auto-jurnal selisih kurs (baik
// dari form manual maupun scheduler).
function fxSkGetUsdAccounts($conn) {
    $accounts = [];
    $sql = mysqli_query($conn, "select bank_account, bank_name from b_masterbank where status = 'Active' and curr = 'USD' order by bank_account");
    while ($row = mysqli_fetch_assoc($sql)) {
        $accounts[] = $row;
    }
    return $accounts;
}

// Tanggal utk MULAI proses akun ini secara efisien - hari setelah tanggal
// terakhir yang SUDAH ada jurnal FX-nya (biar scheduler harian tidak perlu
// hitung ulang seluruh histori dari FXSK_MIN_DATE tiap malam), atau
// FXSK_MIN_DATE kalau akun ini belum pernah dijurnal sama sekali. Aman/
// matematis identik dgn selalu mulai dari FXSK_MIN_DATE - formula "seed"
// saldo buku di fxSkCalcRevaluation() memang didesain deterministik dari
// data historis (native balance + masterrate), bukan menyimpan state - ini
// murni optimasi performa.
function fxSkGetResumeDate($conn, $account) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $sql = mysqli_query($conn, "select max(transaksi_date) as last_date from b_reportbank where akun = '$accEsc' and no_doc like 'FX/%' and status != 'Cancel'");
    $row = mysqli_fetch_assoc($sql);
    if ($row && $row['last_date']) {
        return date('Y-m-d', strtotime($row['last_date'] . ' +1 day'));
    }
    return FXSK_MIN_DATE;
}

// Jalankan auto-jurnal selisih kurs utk SEMUA akun USD aktif, dari resume
// date masing-masing akun s.d. $endDate (biasanya H-1/kemarin dari
// pemanggil) - dipakai dari scheduler tanpa sesi login/konfirmasi manual.
// $connRead & $connWrite BOLEH connection yang SAMA (cukup 1 koneksi ke
// signalbit_erp) - functions ini cuma butuh 2 parameter terpisah utk
// konsistensi dgn form web manual yang punya 2 koneksi berbeda ($conn1 vs
// $conn2), bukan keharusan. $user = label pelapor di kolom create_by.
// Return: array hasil per akun, ['account'=>..,'created'=>[...] | 'error'=>..].
function fxSkRunAllAccounts($connRead, $connWrite, $endDate, $user) {
    $summary = [];
    fxSkEnsureCategory($connRead);

    foreach (fxSkGetUsdAccounts($connRead) as $acc) {
        $account = $acc['bank_account'];
        $accEsc = mysqli_real_escape_string($connRead, $account);
        $sqlBank = mysqli_query($connRead, "select b_code, id_coa, coa_name, profit_center_bank from b_masterbank where bank_account = '$accEsc'");
        $bankInfo = mysqli_fetch_assoc($sqlBank);

        if (!$bankInfo || empty($bankInfo['id_coa'])) {
            $summary[] = ['account' => $account, 'error' => 'Belum ada COA (id_coa) di Master Bank - dilewati.'];
            continue;
        }

        // SELALU dari FXSK_MIN_DATE, BUKAN fxSkGetResumeDate() - "resume
        // dari tanggal terakhir" ternyata bisa melewatkan GAP kalau
        // sebagian tanggal awal pernah diposting manual duluan lalu ada
        // tanggal di tengah yang belum (assumsi "kontinu dari awal" salah).
        // Biaya hitung ulang dari awal tiap malam masih sangat murah utk
        // rentang beberapa bulan - lebih aman drpd resiko diam-diam
        // melewatkan tanggal.
        $rows = fxSkCalcRevaluation($connRead, $account, FXSK_MIN_DATE, $endDate);
        if (empty($rows)) {
            $summary[] = ['account' => $account, 'created' => []];
            continue;
        }

        mysqli_begin_transaction($connWrite);
        try {
            $created = [];
            foreach ($rows as $row) {
                $result = fxSkPostOrUpdateJournal($connRead, $connWrite, $account, $bankInfo, $row['date'], $row['selisih'], $row['existing_doc'], $user);
                $created[] = ['date' => $row['date'], 'selisih' => $row['selisih'], 'doc' => $result['doc'], 'action' => $result['action']];
            }
            mysqli_commit($connWrite);
            $summary[] = ['account' => $account, 'created' => $created];
        } catch (\Throwable $e) {
            mysqli_rollback($connWrite);
            $summary[] = ['account' => $account, 'error' => $e->getMessage()];
        }
    }

    return $summary;
}
