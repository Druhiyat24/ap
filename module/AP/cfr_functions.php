<?php
// =========================
// FUNGSI BANTU
// =========================
// Saldo Awal (titik nol) per akun, dalam mata uang asli akun - dari b_saldoawal_bank
// (rekening bank) atau b_saldoawal_pettycash (kas kecil), sama seperti bankreport.php/cashreport.php.
function cfrGetSaldoAwalMaster($conn, $account) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $sql = mysqli_query($conn, "select amount from b_saldoawal_bank where account = '$accEsc'");
    $row = mysqli_fetch_assoc($sql);
    if ($row) {
        return (float) $row['amount'];
    }
    $sql2 = mysqli_query($conn, "select amount from b_saldoawal_pettycash where account = '$accEsc'");
    $row2 = mysqli_fetch_assoc($sql2);
    return $row2 ? (float) $row2['amount'] : 0;
}

// Total mutasi (debit/credit) satu akun dalam rentang tanggal tertentu, dalam mata uang
// asli akun - gabungan b_reportbank (Bank In/Out) + c_report_pettycash (Petty Cash In/Out).
// $dateFrom/$dateTo null berarti tidak dibatasi ke arah itu.
function cfrGetMutasi($conn, $account, $dateFrom, $dateTo) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $cond = "akun = '$accEsc' and status != 'Cancel'";
    if ($dateFrom !== null) {
        $cond .= " and transaksi_date >= '" . mysqli_real_escape_string($conn, $dateFrom) . "'";
    }
    if ($dateTo !== null) {
        $cond .= " and transaksi_date <= '" . mysqli_real_escape_string($conn, $dateTo) . "'";
    }
    $sql = mysqli_query($conn, "select coalesce(sum(debit),0) d, coalesce(sum(credit),0) c from (
        select transaksi_date, debit, credit, status, akun from b_reportbank where $cond
        union all
        select transaksi_date, debit, credit, status, akun from c_report_pettycash where $cond
    ) x");
    $row = mysqli_fetch_assoc($sql);
    return ['debit' => (float) $row['d'], 'credit' => (float) $row['c']];
}

// Kurs USD->IDR terkini, dengan metode & fallback yang sama seperti bankreport.php:
// cari rate v_codecurr=$type pada tanggal $date, kalau tidak ada pakai rate HARIAN
// paling baru yang tersedia (on/before $date), kalau tetap tidak ada fallback ke 1.
function cfrGetRate($conn, $date, $type = 'HARIAN') {
    $d = mysqli_real_escape_string($conn, $date);
    $sql = mysqli_query($conn, "select rate from masterrate where v_codecurr = '$type' and curr = 'USD' and tanggal = '$d' limit 1");
    $row = mysqli_fetch_assoc($sql);
    if ($row) {
        return (float) $row['rate'];
    }
    $sql2 = mysqli_query($conn, "select rate from masterrate where v_codecurr = 'HARIAN' and curr = 'USD' and tanggal <= '$d' order by tanggal desc limit 1");
    $row2 = mysqli_fetch_assoc($sql2);
    if ($row2) {
        return (float) $row2['rate'];
    }
    $sql3 = mysqli_query($conn, "select rate from masterrate where v_codecurr = 'HARIAN' and curr = 'USD' order by tanggal desc limit 1");
    $row3 = mysqli_fetch_assoc($sql3);
    return $row3 ? (float) $row3['rate'] : 1;
}

// Saldo Awal per rekening bank, sudah dalam IDR & clamp minus ke 0: saldo_awal_native +
// mutasi_native sebelum $startDate, dikonversi pakai kurs HARIAN di (startDate - 1 hari) -
// kurs tetap 1 titik per periode, persis konvensi bankreport.php/auto-jurnal-selisih-kurs.
// Baris penyesuaian selisih kurs (no_doc "FX/...") dikecualikan dari mutasi native -
// itu murni penyesuaian nilai IDR, bukan pergerakan saldo native asli (persis fix yang
// sama di bankreport.php - kalau tidak dikecualikan, saldo awal jadi salah begitu ada
// jurnal FX yang tanggalnya sebelum start_date).
// Return: [bank_account => saldo_idr].
function cfrGetBankBeginBalances($conn, $startDate) {
    $d = mysqli_real_escape_string($conn, $startDate);
    $dateBefore = date('Y-m-d', strtotime($startDate . ' -1 day'));
    $dBeforeEsc = mysqli_real_escape_string($conn, $dateBefore);
    $sql = mysqli_query($conn, "select bank_account, round(if(bal < 0, 0, bal), 2) total from (
        select m.bank_account,
            (coalesce(s.amount,0) + coalesce(sum(r.debit - r.credit),0))
            * if(m.curr = 'IDR', 1,
                (select mr.rate from masterrate mr
                 where mr.curr = m.curr and mr.v_codecurr = 'HARIAN'
                   and mr.tanggal <= '$dBeforeEsc'
                 order by mr.tanggal desc limit 1)
            ) as bal
        from b_masterbank m
        left join b_saldoawal_bank s on s.account = m.bank_account
        left join b_reportbank r on r.akun = m.bank_account and r.status != 'Cancel' and r.transaksi_date < '$d' and r.no_doc not like 'FX/%'
        where m.status = 'Active'
        group by m.bank_account, s.amount
    ) pa");
    $out = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $out[$row['bank_account']] = (float) $row['total'];
    }
    return $out;
}

// Saldo akhir NATIVE (mata uang asli, bukan IDR) 1 rekening bank per $endDate -
// saldo_awal_native + seluruh mutasi native s.d. $endDate, baris selisih kurs
// (no_doc "FX/...") dikecualikan (bukan pergerakan native asli). Dipakai utk cek
// "apakah akun ini beneran positif" independen dari perhitungan selisih kurs itu
// sendiri (supaya tidak sirkular).
function cfrGetBankNativeEndBalance($conn, $account, $endDate) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $dEsc = mysqli_real_escape_string($conn, $endDate);
    $sql = mysqli_query($conn, "select
            coalesce((select amount from b_saldoawal_bank where account = '$accEsc'), 0)
            + coalesce((select sum(debit - credit) from b_reportbank
                        where akun = '$accEsc' and status != 'Cancel' and no_doc not like 'FX/%'
                          and transaksi_date <= '$dEsc'), 0)
            as bal");
    $row = mysqli_fetch_assoc($sql);
    return $row ? (float) $row['bal'] : 0.0;
}

// Saldo Awal RAW (TIDAK di-clamp ke 0) 1 akun bank, dalam IDR, kurs HARIAN di
// (startDate - 1 hari) - kebalikan dari cfrGetBankBeginBalances(): di sana nilai
// negatif di-floor ke 0 (dianggap "tidak ada saldo kas"), di sini JUSTRU cuma
// nilai NEGATIF yang relevan (merepresentasikan pinjaman/overdraft) - dipakai
// khusus baris SALDO AWAL PINJAMAN BANK.
function cfrGetBankRawBeginBalanceIdr($conn, $account, $startDate) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $dEsc = mysqli_real_escape_string($conn, $startDate);
    $dateBefore = date('Y-m-d', strtotime($startDate . ' -1 day'));
    $sql = mysqli_query($conn, "select
            coalesce((select amount from b_saldoawal_bank where account = '$accEsc'), 0)
            + coalesce((select sum(debit - credit) from b_reportbank
                        where akun = '$accEsc' and status != 'Cancel' and no_doc not like 'FX/%'
                          and transaksi_date < '$dEsc'), 0)
            as native_bal");
    $row = mysqli_fetch_assoc($sql);
    $native = $row ? (float) $row['native_bal'] : 0.0;

    $sqlAcc = mysqli_query($conn, "select curr from b_masterbank where bank_account = '$accEsc'");
    $rowAcc = mysqli_fetch_assoc($sqlAcc);
    $curr = $rowAcc ? $rowAcc['curr'] : 'IDR';
    $rate = ($curr === 'IDR') ? 1 : cfrGetRate($conn, $dateBefore);
    return $native * $rate;
}

// Limit fasilitas pinjaman (b_masterbank.fac_limit) 1 akun bank, dikonversi ke
// IDR (kurs HARIAN di $endDate kalau akunnya bukan IDR - persis konvensi
// "nilai pasar saat ini" yang dipakai Saldo Akhir/Ending Balance).
function cfrGetLoanLimitIdr($conn, $account, $endDate) {
    $accEsc = mysqli_real_escape_string($conn, $account);
    $sql = mysqli_query($conn, "select fac_limit, curr from b_masterbank where bank_account = '$accEsc'");
    $row = mysqli_fetch_assoc($sql);
    if (!$row) {
        return 0.0;
    }
    $rate = ($row['curr'] === 'IDR') ? 1 : cfrGetRate($conn, $endDate);
    return (float) $row['fac_limit'] * $rate;
}

// Saldo Awal per akun kas kecil, sudah dalam IDR & clamp minus ke 0 - pola sama seperti
// cfrGetBankBeginBalances tapi kurs pakai HARIAN (bukan PAJAK) sesuai kurs terakhir sebelum
// $startDate, dan default ke IDR kalau akun itu belum punya baris b_saldoawal_pettycash sama sekali.
function cfrGetKasBeginBalances($conn, $startDate) {
    $d = mysqli_real_escape_string($conn, $startDate);
    $sql = mysqli_query($conn, "select no_coa, round(if(bal < 0, 0, bal), 2) total from (
        select c.no_coa,
            (coalesce(s.amount,0) + coalesce(sum(r.debit - r.credit),0))
            * if(s.curr is null or s.curr = 'IDR', 1,
                (select mr.rate from masterrate mr
                 where mr.curr = s.curr and mr.v_codecurr = 'HARIAN' and mr.tanggal < '$d'
                 order by mr.tanggal desc limit 1)
            ) as bal
        from mastercoa_v2 c
        left join b_saldoawal_pettycash s on s.account = c.no_coa
        left join c_report_pettycash r on r.akun = c.no_coa and r.status != 'Cancel' and r.transaksi_date < '$d'
        where c.ind_categori5 = 'KAS'
        group by c.no_coa, s.amount, s.curr
    ) pa");
    $out = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $out[$row['no_coa']] = (float) $row['total'];
    }
    return $out;
}

// Cari 1 baris kategori dari daftar $rows (elemen $categories['Cash In'/'Cash Out'][activity])
// berdasarkan teks nama_subcategory persis - dipakai buat ambil baris tertentu yang dirujuk
// nama-nya di rumus Penerimaan/Pelunasan Pinjaman Bank di bawah.
function cfrFindCatRow($rows, $label) {
    foreach ($rows as $row) {
        if (trim($row['nama_subcategory']) === $label) {
            return $row;
        }
    }
    return null;
}

// Jumlah cfrRowValue() dari SEMUA baris di 1 activity (mis. semua baris "Cash Out >
// OPERATING ACTIVITIES") untuk 1 akun - dipakai buat ambil angka "TOTAL CASH
// DISBURSEMENT/RECEIPT FROM ... ACTIVITIES" persis seperti yang tampil di baris totalnya.
function cfrSumCatForAccount($rows, $realisasi, $account, $isCashIn) {
    $total = 0;
    foreach ($rows as $row) {
        $total += cfrRowValue($realisasi, $row['id'], $account, $isCashIn);
    }
    return $total;
}

// Nilai 1 baris kategori tertentu (dicari by nama_subcategory) untuk 1 akun - 0 kalau
// barisnya tidak ketemu (mis. nama di master_cash_flow berubah).
function cfrNamedCatValue($rows, $label, $realisasi, $account, $isCashIn) {
    $row = cfrFindCatRow($rows, $label);
    return $row ? cfrRowValue($realisasi, $row['id'], $account, $isCashIn) : 0;
}

// Total realisasi "Penerimaan Pinjaman Bank" (Cash In > Financing Activities) per akun -
// BUKAN lagi dari query dedicated (deteksi perubahan tanda saldo rekening), diganti rumus
// manual sesuai permintaan user:
//   Penerimaan Pinjaman Bank = -(TOTAL CASH DISBURSEMENT FROM OPERATING ACTIVITIES
//     - TOTAL CASH DISBURSEMENT FROM INVESTING ACTIVITIES
//     - (Pelunasan Pinjaman Non Bank + Pelunasan Pinjaman Group + Pelunasan Pinjaman Shareholder
//        + Pembayaran Bunga Pinjaman Bank + Pembayaran Bunga Pinjaman Group
//        + Pembayaran Bunga Pinjaman Shareholder + Pelunasan Pinjaman Antar Divisi))
// Semua suku di rumus ini dipakai APA ADANYA (sudah dalam konvensi tanda "seperti yang
// tampil di laporan" dari cfrRowValue() - Cash Out negatif, Cash In positif), TIDAK ditambah
// pembalikan tanda lagi di luar rumus.
// Dihitung TERPISAH untuk akun 008-997-1979 & 008-998-1982 (pakai angka kolom akun itu
// sendiri di baris-baris rujukan, bukan grand total laporan) - akun lain SELALU 0, sama
// sekali tidak ikut dihitung (konsisten dengan cfrAkunKey() yang cuma kenal 2 akun ini).
function cfrCalcPenerimaanPinjamanBank($categories, $realisasi) {
    $result = ['1979' => 0.0, '1982' => 0.0, 'total' => 0.0];
    $disbFinancing = $categories['Cash Out']['FINANCING ACTIVITIES'];
    foreach (['1979' => '008-997-1979', '1982' => '008-998-1982'] as $key => $account) {
        $disbOperating = cfrSumCatForAccount($categories['Cash Out']['OPERATING ACTIVITIES'], $realisasi, $account, false);
        $disbInvesting = cfrSumCatForAccount($categories['Cash Out']['INVESTING ACTIVITIES'], $realisasi, $account, false);
        $subItems = cfrNamedCatValue($disbFinancing, 'Pelunasan Pinjaman Non Bank', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pelunasan Pinjaman Group', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pelunasan Pinjaman Shareholder', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pembayaran Bunga Pinjaman Bank', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pembayaran Bunga Pinjaman Group', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pembayaran Bunga Pinjaman Shareholder', $realisasi, $account, false)
            + cfrNamedCatValue($disbFinancing, 'Pelunasan Pinjaman Antar Divisi', $realisasi, $account, false);
        $value = -($disbOperating - $disbInvesting - $subItems);
        $result[$key] = $value;
        $result['total'] += $value;
    }
    return $result;
}

// Total realisasi "Pelunasan Pinjaman Bank" (Cash Out > Financing Activities) per akun -
// pasangan dari cfrCalcPenerimaanPinjamanBank() di atas, rumusnya:
//   Pelunasan Pinjaman Bank = -( TOTAL CASH RECEIPT FROM OPERATING ACTIVITIES
//     + TOTAL CASH RECEIPT FROM INVESTING ACTIVITIES
//     + Penerimaan Pinjaman Group + Penerimaan Pinjaman Shareholder + Penerimaan Pinjaman Antar Divisi )
// Sama seperti di atas - dihitung terpisah per akun 008-997-1979/008-998-1982, akun lain 0.
function cfrCalcPembayaranPinjamanBank($categories, $realisasi) {
    $result = ['1979' => 0.0, '1982' => 0.0, 'total' => 0.0];
    $recFinancing = $categories['Cash In']['FINANCING ACTIVITIES'];
    foreach (['1979' => '008-997-1979', '1982' => '008-998-1982'] as $key => $account) {
        $recOperating = cfrSumCatForAccount($categories['Cash In']['OPERATING ACTIVITIES'], $realisasi, $account, true);
        $recInvesting = cfrSumCatForAccount($categories['Cash In']['INVESTING ACTIVITIES'], $realisasi, $account, true);
        $addItems = cfrNamedCatValue($recFinancing, 'Penerimaan Pinjaman Group', $realisasi, $account, true)
            + cfrNamedCatValue($recFinancing, 'Penerimaan Pinjaman Shareholder', $realisasi, $account, true)
            + cfrNamedCatValue($recFinancing, 'Penerimaan Pinjaman Antar Divisi', $realisasi, $account, true);
        $value = -($recOperating + $recInvesting + $addItems);
        $result[$key] = $value;
        $result['total'] += $value;
    }
    return $result;
}

// Format angka ala laporan keuangan: 0/kosong -> '-', negatif -> (1,234.00).
function cfrFmt($num) {
    $num = round((float) $num, 2);
    if ($num == 0) {
        return '-';
    }
    if ($num < 0) {
        return '(' . number_format(abs($num), 2) . ')';
    }
    return number_format($num, 2);
}

// Sama seperti cfrFmt(), tapi negatif dibungkus <span style="color:red"> - dipakai
// khusus di export Excel supaya negatif tampil merah seperti format akuntansi.
function cfrFmtXls($num) {
    $num = round((float) $num, 2);
    if ($num == 0) {
        return '-';
    }
    if ($num < 0) {
        return '<span style="color:#c00000;">(' . number_format(abs($num), 2) . ')</span>';
    }
    return number_format($num, 2);
}

// Helper ambil nilai realisasi 1 baris kategori (Cash In = debit, Cash Out = credit negatif).
// id 9 (Penerimaan Pinjaman Bank) & 47 (Pelunasan Pinjaman Bank) SENGAJA tidak pernah
// baca dari $realisasi (supaya tidak kecampur transaksi ber-tag id_cash_flow biasa) -
// murni dari cfrCalcPenerimaanPinjamanBank()/cfrCalcPembayaranPinjamanBank(), dipecah
// per akun (008-997-1979 & 008-998-1982) supaya penjumlahan per-akun (subtotal & grand
// total) tetap konsisten/nyambung dengan kolom Realisation.
function cfrRowValue($realisasi, $catId, $account, $isCashIn) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank, $cfrSuppressFxAccounts;
    if ($catId == 9) {
        return isset($penerimaanPinjamanBank[cfrAkunKey($account)]) ? $penerimaanPinjamanBank[cfrAkunKey($account)] : 0;
    }
    if ($catId == 47) {
        return isset($pembayaranPinjamanBank[cfrAkunKey($account)]) ? $pembayaranPinjamanBank[cfrAkunKey($account)] : 0;
    }
    if (!isset($realisasi[$catId][$account])) {
        return 0;
    }
    $v = $realisasi[$catId][$account];
    // id_cash_flow 55 = "Pengakuan Kerugian Selisih Kurs" (dari auto-jurnal-selisih-
    // kurs) - kategori ini cuma ADA di section Cash Out, tapi baris jurnalnya sendiri
    // bisa DEBIT (untung) atau CREDIT (rugi) tergantung tanda selisih hari itu - harus
    // di-net (debit - credit), bukan cuma -credit spt kategori Cash Out biasa (yang
    // baris sumbernya memang cuma pernah berisi credit). Khusus akun yang masuk
    // $cfrSuppressFxAccounts (saldo akhir native-nya <= 0, lihat cfrComputeReportData),
    // nilainya dianggap 0 - tidak bermakna secara ekonomi selama akun itu masih minus.
    if ($catId == 55) {
        if (!empty($cfrSuppressFxAccounts[$account])) {
            return 0;
        }
        return $v['debit'] - $v['credit'];
    }
    return $isCashIn ? $v['debit'] : -$v['credit'];
}
function cfrAkunKey($account) {
    if ($account === '008-997-1979') return '1979';
    if ($account === '008-998-1982') return '1982';
    return null;
}
function cfrRowTotal($realisasi, $catId, $isCashIn) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank, $cfrSuppressFxAccounts;
    if ($catId == 9) {
        return $penerimaanPinjamanBank['total'];
    }
    if ($catId == 47) {
        return $pembayaranPinjamanBank['total'];
    }
    if (!isset($realisasi[$catId])) {
        return 0;
    }
    $total = 0;
    foreach ($realisasi[$catId] as $account => $v) {
        if ($catId == 55) {
            $total += empty($cfrSuppressFxAccounts[$account]) ? ($v['debit'] - $v['credit']) : 0;
        } else {
            $total += $isCashIn ? $v['debit'] : -$v['credit'];
        }
    }
    return $total;
}

// Daftar SEMUA kolom akun (bank aktif + kas kecil) - dipakai baik oleh
// cfrComputeReportData() (query data laporan) maupun report-cashflow-realisation.php
// (isi filter "Account" di form, dipanggil terpisah dari situ karena daftar akun
// tidak tergantung periode/pilihan filter, jadi lebih murah dipanggil sendiri
// daripada lewat cfrComputeReportData yang query-nya jauh lebih berat).
function cfrGetAllAccounts($conn) {
    $accounts = [];
    $sqlBank = mysqli_query($conn, "select bank_account as account, curr from b_masterbank where status = 'Active' order by id");
    while ($r = mysqli_fetch_assoc($sqlBank)) {
        $accounts[] = ['account' => $r['account'], 'label' => $r['account'], 'curr' => $r['curr']];
    }
    $sqlKas = mysqli_query($conn, "select no_coa as account, nama_coa as label from mastercoa_v2 where ind_categori5 = 'KAS' and nama_coa != 'POS SILANG' order by nama_coa");
    while ($r = mysqli_fetch_assoc($sqlKas)) {
        $accounts[] = ['account' => $r['account'], 'label' => $r['label'], 'curr' => 'IDR'];
    }
    return $accounts;
}

// Hitung semua data yang dibutuhkan report Cash Flow Realisation (akun, saldo awal/akhir,
// kategori master, realisasi per kategori x akun, dan 2 kategori pinjaman bank dedicated) -
// satu fungsi ini dipakai bareng oleh halaman report (report-cashflow-realisation.php) dan
// export Excel (ekspor_cashflow_realisation.php) supaya logikanya cuma ada di satu tempat.
//
// $selectedAccounts (opsional) = array kode akun yang user pilih (filter "Account" -
// null/kosong berarti semua akun). Filter ini men-scope SELURUH perhitungan (bukan cuma
// kolom mana yang dirender) - SALDO AWAL/AKHIR total dan REALISATION/PROJECTION/VARIANCE
// dihitung HANYA dari akun yang dipilih, seolah-olah akun lain tidak ada. Fallback ke
// semua akun kalau hasil filternya kosong (mis. kode akun yang dikirim sudah tidak ada
// lagi di master) - baris "REALISATION BY BANK" tidak boleh sampai kosong sama sekali.
function cfrComputeReportData($conn, $start_date, $end_date, $selectedAccounts = null) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank, $cfrSuppressFxAccounts;

    $allAccounts = cfrGetAllAccounts($conn);
    $accounts = $allAccounts;
    if (!empty($selectedAccounts)) {
        $selectedSet = array_flip($selectedAccounts);
        $filtered = array_values(array_filter($allAccounts, function ($acc) use ($selectedSet) {
            return isset($selectedSet[$acc['account']]);
        }));
        if (!empty($filtered)) {
            $accounts = $filtered;
        }
    }

    // Khusus akun 008-998-1982: baris "Pengakuan Kerugian Selisih Kurs" (id_cash_flow
    // 55) cuma ditampilkan kalau saldo akhir NATIVE akun ini > 0 - kalau akun sedang
    // minus (spt yang sering terjadi krn transaksinya banyak masih Draft), nilai
    // selisih kurs-nya tidak bermakna secara ekonomi - jadi dianggap 0. Dicek dari
    // saldo native ASLI (independen dari hasil cfrRowValue itu sendiri, supaya tidak
    // sirkular), lihat cfrRowValue/cfrRowTotal.
    $cfrSuppressFxAccounts = [];
    if (cfrGetBankNativeEndBalance($conn, '008-998-1982', $end_date) <= 0) {
        $cfrSuppressFxAccounts['008-998-1982'] = true;
    }

    $dateBefore = date('Y-m-d', strtotime($start_date . ' -1 day'));

    // Kurs Saldo Awal = HARIAN di (start_date - 1 hari) - 1 titik kurs tetap per
    // periode, dipakai utk akun non-bank/non-kas (jalur fallback di bawah) supaya
    // konsisten dgn cfrGetBankBeginBalances/cfrGetKasBeginBalances yang keduanya
    // sudah pakai HARIAN di tanggal yang sama.
    $beginRate = cfrGetRate($conn, $dateBefore);

    // Saldo Awal rekening bank - saldo_awal_native + mutasi native sebelum
    // start_date, dikonversi kurs HARIAN di (start_date - 1 hari), clamp minus ke 0.
    $bankBeginIdr = cfrGetBankBeginBalances($conn, $start_date);

    // Saldo Awal akun kas kecil - pola sama (kurs HARIAN di start_date - 1 hari,
    // default IDR kalau belum ada baris b_saldoawal_pettycash).
    $kasBeginIdr = cfrGetKasBeginBalances($conn, $start_date);

    // Saldo Awal per akun (rekening bank pakai $bankBeginIdr, kas kecil pakai
    // $kasBeginIdr, sisanya fallback native x $beginRate). Cuma akun yang DIPILIH
    // ($accounts, bukan $allAccounts) supaya totalBegin ikut menyusut sesuai filter.
    $beginBalance = [];
    $totalBegin = 0;
    foreach ($accounts as $acc) {
        if (isset($bankBeginIdr[$acc['account']])) {
            $beginIdr = $bankBeginIdr[$acc['account']];
        } elseif (isset($kasBeginIdr[$acc['account']])) {
            $beginIdr = $kasBeginIdr[$acc['account']];
        } else {
            $rate = ($acc['curr'] === 'IDR') ? 1 : $beginRate;
            $base = cfrGetSaldoAwalMaster($conn, $acc['account']);
            $mutBefore = cfrGetMutasi($conn, $acc['account'], null, $dateBefore);
            $begin = $base + $mutBefore['debit'] - $mutBefore['credit'];
            $beginIdr = max(0, $begin) * $rate;
        }
        $beginBalance[$acc['account']] = $beginIdr;
        $totalBegin += $beginIdr;
    }

    // Kategori master cash flow (urutan sesuai display_seq)
    $sqlCat = mysqli_query($conn, "select id, type_cashflow, nama_category, nama_subcategory, display_seq from master_cash_flow where status = 'Y'
        order by type_cashflow asc,
        case nama_category when 'OPERATING ACTIVITIES' then 1 when 'INVESTING ACTIVITIES' then 2 when 'FINANCING ACTIVITIES' then 3 else 4 end asc,
        display_seq asc");
    $categories = ['Cash In' => ['OPERATING ACTIVITIES' => [], 'INVESTING ACTIVITIES' => [], 'FINANCING ACTIVITIES' => []],
                   'Cash Out' => ['OPERATING ACTIVITIES' => [], 'INVESTING ACTIVITIES' => [], 'FINANCING ACTIVITIES' => []]];
    while ($r = mysqli_fetch_assoc($sqlCat)) {
        $categories[$r['type_cashflow']][$r['nama_category']][] = $r;
    }

    // Realisasi per kategori x akun - tiap TRANSAKSI dikonversi ke IDR pakai kurs
    // PAJAK di TANGGAL TRANSAKSI itu sendiri (bukan 1 kurs per akun lagi) - rate
    // yang sesungguhnya dipakai saat transaksi diposting ke GL, konsisten dgn
    // auto-jurnal-selisih-kurs/bankreport.php. Baris TIDAK di-GROUP BY di SQL lagi
    // (perlu tanggal per baris utk cari rate-nya), akumulasi debit/credit_idr
    // dilakukan di PHP. Dibatasi ke akun yang DIPILIH ($accounts) lewat
    // "akun IN (...)" supaya REALISATION/PROJECTION/VARIANCE ikut menyusut sesuai
    // filter - akun yang tidak dipilih dianggap tidak ada sama sekali.
    $akunList = implode(',', array_map(function ($acc) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $acc['account']) . "'";
    }, $accounts));
    // curr diambil per BARIS (bukan curr akun) - baris penyesuaian selisih kurs
    // (dari auto-jurnal-selisih-kurs) punya curr='IDR' walau akunnya sendiri USD,
    // nilainya SUDAH dalam IDR jadi rate=1 (jangan dikonversi PAJAK lagi, dobel
    // konversi -> nilainya bisa meledak jadi ratusan miliar salah).
    $sqlReal = mysqli_query($conn, "select id_cash_flow, akun, curr, transaksi_date, debit, credit from (
        select id_cash_flow, akun, curr, transaksi_date, debit, credit from b_reportbank where status != 'Cancel' and transaksi_date between '$start_date' and '$end_date' and id_cash_flow is not null and akun in ($akunList)
        union all
        select id_cash_flow, akun, curr, transaksi_date, debit, credit from c_report_pettycash where status != 'Cancel' and transaksi_date between '$start_date' and '$end_date' and id_cash_flow is not null and akun in ($akunList)
    ) x");
    $realisasi = [];
    while ($r = mysqli_fetch_assoc($sqlReal)) {
        $akun = $r['akun'];
        $idCf = $r['id_cash_flow'];
        $rate = ($r['curr'] === 'IDR') ? 1 : cfrGetRate($conn, $r['transaksi_date'], 'PAJAK');
        if (!isset($realisasi[$idCf][$akun])) {
            $realisasi[$idCf][$akun] = ['debit' => 0.0, 'credit' => 0.0];
        }
        $realisasi[$idCf][$akun]['debit'] += (float) $r['debit'] * $rate;
        $realisasi[$idCf][$akun]['credit'] += (float) $r['credit'] * $rate;
    }

    // "Penerimaan Pinjaman Bank" (id master_cash_flow = 9) & "Pelunasan Pinjaman Bank"
    // (id 47) BUKAN dari tagging id_cash_flow transaksi biasa - dihitung dari rumus manual
    // (angka "plug"/penyeimbang dari baris-baris lain yang sudah ada di laporan ini), lihat
    // cfrCalcPenerimaanPinjamanBank()/cfrCalcPembayaranPinjamanBank(). Disimpan sebagai
    // global supaya cfrRowValue/cfrRowTotal bisa akses.
    $penerimaanPinjamanBank = cfrCalcPenerimaanPinjamanBank($categories, $realisasi);
    $pembayaranPinjamanBank = cfrCalcPembayaranPinjamanBank($categories, $realisasi);

    // Saldo Akhir per akun = Saldo Awal + Total Cash Receipts - Total Cash Disbursement,
    // pakai angka Realisation yang sama persis dengan yang tampil di tabel (termasuk 2
    // baris pinjaman bank dedicated) - bukan lagi dihitung independen dari ledger native.
    // cfrRowValue Cash Out sudah dalam konvensi negatif (outflow), jadi cukup dijumlahkan
    // langsung (TIDAK dikurangi lagi) supaya tidak dobel-hitung.
    $endBalance = [];
    $totalEnd = 0;
    foreach ($accounts as $acc) {
        $receiptTotal = 0;
        foreach ($categories['Cash In'] as $rows) {
            foreach ($rows as $row) {
                $receiptTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
            }
        }
        $disbTotal = 0;
        foreach ($categories['Cash Out'] as $rows) {
            foreach ($rows as $row) {
                $disbTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
            }
        }
        $endIdr = $beginBalance[$acc['account']] + $receiptTotal + $disbTotal;
        $endBalance[$acc['account']] = $endIdr;
        $totalEnd += $endIdr;
    }

    return [
        'accounts' => $accounts,
        'beginBalance' => $beginBalance,
        'endBalance' => $endBalance,
        'totalBegin' => $totalBegin,
        'totalEnd' => $totalEnd,
        'categories' => $categories,
        'realisasi' => $realisasi,
        'penerimaanPinjamanBank' => $penerimaanPinjamanBank,
        'pembayaranPinjamanBank' => $pembayaranPinjamanBank,
        'colspanAccounts' => count($accounts),
    ];
}
