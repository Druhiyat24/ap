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

// Saldo Awal per rekening bank, sudah dalam IDR & clamp minus ke 0 - persis pola yang
// dipakai user: saldo_awal_native + mutasi_native sebelum $startDate, dikonversi pakai
// kurs PAJAK pada tanggal transaksi terakhir sebelum $startDate (fallback ke $startDate
// itu sendiri kalau belum pernah ada transaksi). Return: [bank_account => saldo_idr].
function cfrGetBankBeginBalances($conn, $startDate) {
    $d = mysqli_real_escape_string($conn, $startDate);
    $sql = mysqli_query($conn, "select bank_account, round(if(bal < 0, 0, bal), 2) total from (
        select m.bank_account,
            (coalesce(s.amount,0) + coalesce(sum(r.debit - r.credit),0))
            * if(m.curr = 'IDR', 1,
                (select mr.rate from masterrate mr
                 where mr.curr = m.curr and mr.v_codecurr = 'PAJAK'
                   and mr.tanggal <= coalesce(
                       (select max(r2.transaksi_date) from b_reportbank r2
                        where r2.akun = m.bank_account and r2.transaksi_date < '$d' and r2.status != 'Cancel'),
                       '$d'
                   )
                 order by mr.tanggal desc limit 1)
            ) as bal
        from b_masterbank m
        left join b_saldoawal_bank s on s.account = m.bank_account
        left join b_reportbank r on r.akun = m.bank_account and r.status != 'Cancel' and r.transaksi_date < '$d'
        where m.status = 'Active'
        group by m.bank_account, s.amount
    ) pa");
    $out = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $out[$row['bank_account']] = (float) $row['total'];
    }
    return $out;
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

// Total realisasi "Penerimaan Pinjaman Bank" (Cash In > Financing Activities) dalam IDR -
// diadaptasi dari query resmi yang sudah dipakai di ekspor_cf_direct_monthly_fix.php
// (sql_Penerimaan), cuma bagian lookup periode bulan/tahun-nya diganti pakai $startDate/
// $endDate langsung karena report ini pakai rentang tanggal bebas, bukan pilihan bulan.
// Logikanya sendiri (deteksi pencairan pinjaman dari perubahan tanda saldo akun 1.10.01/
// 1.10.02 rekening BCA) TIDAK diubah - dipakai apa adanya sesuai yang diberikan.
function cfrGetPenerimaanPinjamanBank($conn, $startDate, $endDate) {
    $tahunAwal = date('Y', strtotime($startDate));
    $sd = mysqli_real_escape_string($conn, $startDate);
    $ed = mysqli_real_escape_string($conn, $endDate);
    $sql = mysqli_query($conn, "WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahunAwal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN '$sd' AND '$ed'
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN '$sd' AND '$ed'
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.akun, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.akun, c.periode
),

pivot AS (
  SELECT
  14 id,
    periode,
    SUM(CASE WHEN akun='008-997-1979' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_1979,
    SUM(CASE WHEN akun='008-998-1982' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_1982,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN akun='008-997-1979' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_1979,
    SUM(CASE WHEN akun='008-998-1982' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_1982,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a
CROSS JOIN
(
    SELECT '$tahunAwal-01' periode
    UNION ALL SELECT '$tahunAwal-02'
    UNION ALL SELECT '$tahunAwal-03'
    UNION ALL SELECT '$tahunAwal-04'
    UNION ALL SELECT '$tahunAwal-05'
    UNION ALL SELECT '$tahunAwal-06'
    UNION ALL SELECT '$tahunAwal-07'
    UNION ALL SELECT '$tahunAwal-08'
    UNION ALL SELECT '$tahunAwal-09'
    UNION ALL SELECT '$tahunAwal-10'
    UNION ALL SELECT '$tahunAwal-11'
    UNION ALL SELECT '$tahunAwal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '14' ORDER BY a.id ASC),

data_fix as (SELECT a.periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       (penerimaan_1979 + COALESCE(b.total_all,0)) AS total_1979,
       penerimaan_1982 AS total_1982,
       (penerimaan_TOTAL + COALESCE(b.total_all,0)) AS total_all
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode

UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_1979 - COALESCE(b.total_all,0)) total_1979,
       - pembayaran_1982 total_1982,
       - (pembayaran_TOTAL - COALESCE(b.total_all,0)) total_all
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_1979) total_1979, sum(total_1982) total_1982, sum(total_all) total_all from data_fix where sub_kategori = 'Penerimaan Pinjaman' AND periode BETWEEN DATE_FORMAT('$sd','%Y-%m') AND DATE_FORMAT('$ed','%Y-%m')");
    $row = mysqli_fetch_assoc($sql);
    return $row ? ['1979' => (float) $row['total_1979'], '1982' => (float) $row['total_1982'], 'total' => (float) $row['total_all']] : ['1979' => 0, '1982' => 0, 'total' => 0];
}

// Total realisasi "Pelunasan Pinjaman Bank" (Cash Out > Financing Activities) dalam IDR -
// pasangan dari cfrGetPenerimaanPinjamanBank() di atas, sama-sama diadaptasi dari query
// resmi ekspor_cf_direct_monthly_fix.php (sql_Pembayaran, pivot id 15).
function cfrGetPembayaranPinjamanBank($conn, $startDate, $endDate) {
    $tahunAwal = date('Y', strtotime($startDate));
    $sd = mysqli_real_escape_string($conn, $startDate);
    $ed = mysqli_real_escape_string($conn, $endDate);
    $sql = mysqli_query($conn, "WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahunAwal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-01' periode, s.jan_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-02' periode, s.feb_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-03' periode, s.mar_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-04' periode, s.apr_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-05' periode, s.may_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-06' periode, s.jun_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-07' periode, s.jul_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-08' periode, s.aug_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-09' periode, s.sep_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-10' periode, s.oct_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-11' periode, s.nov_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

    UNION ALL

    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahunAwal-12' periode, s.dec_$tahunAwal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN '$sd' AND '$ed'
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN '$sd' AND '$ed'
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.akun, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.akun, c.periode
),

pivot AS (
  SELECT
  15 id,
    periode,
    SUM(CASE WHEN akun='008-997-1979' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_1979,
    SUM(CASE WHEN akun='008-998-1982' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_1982,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN akun='008-997-1979' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_1979,
    SUM(CASE WHEN akun='008-998-1982' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_1982,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a
CROSS JOIN
(
    SELECT '$tahunAwal-01' periode
    UNION ALL SELECT '$tahunAwal-02'
    UNION ALL SELECT '$tahunAwal-03'
    UNION ALL SELECT '$tahunAwal-04'
    UNION ALL SELECT '$tahunAwal-05'
    UNION ALL SELECT '$tahunAwal-06'
    UNION ALL SELECT '$tahunAwal-07'
    UNION ALL SELECT '$tahunAwal-08'
    UNION ALL SELECT '$tahunAwal-09'
    UNION ALL SELECT '$tahunAwal-10'
    UNION ALL SELECT '$tahunAwal-11'
    UNION ALL SELECT '$tahunAwal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN sb_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN '$sd' AND '$ed' AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '15' ORDER BY a.id ASC),

data_fix as (SELECT periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       penerimaan_1979 AS total_1979,
       penerimaan_1982 AS total_1982,
       penerimaan_TOTAL AS total_all
FROM pivot

UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_1979 - COALESCE(b.total_all,0)) total_1979,
       - pembayaran_1982 total_1982,
       - (pembayaran_TOTAL - COALESCE(b.total_all,0)) total_all
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_1979) total_1979, sum(total_1982) total_1982, sum(total_all) total_all from data_fix where sub_kategori = 'Pembayaran Pinjaman' AND periode BETWEEN DATE_FORMAT('$sd','%Y-%m') AND DATE_FORMAT('$ed','%Y-%m')");
    $row = mysqli_fetch_assoc($sql);
    return $row ? ['1979' => (float) $row['total_1979'], '1982' => (float) $row['total_1982'], 'total' => (float) $row['total_all']] : ['1979' => 0, '1982' => 0, 'total' => 0];
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
// murni dari cfrGetPenerimaanPinjamanBank()/cfrGetPembayaranPinjamanBank(), dipecah
// per akun (008-997-1979 & 008-998-1982) supaya penjumlahan per-akun (subtotal & grand
// total) tetap konsisten/nyambung dengan kolom Realisation.
function cfrRowValue($realisasi, $catId, $account, $isCashIn) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank;
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
    return $isCashIn ? $v['debit'] : -$v['credit'];
}
function cfrAkunKey($account) {
    if ($account === '008-997-1979') return '1979';
    if ($account === '008-998-1982') return '1982';
    return null;
}
function cfrRowTotal($realisasi, $catId, $isCashIn) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank;
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
    foreach ($realisasi[$catId] as $v) {
        $total += $isCashIn ? $v['debit'] : -$v['credit'];
    }
    return $total;
}

// Hitung semua data yang dibutuhkan report Cash Flow Realisation (akun, saldo awal/akhir,
// kategori master, realisasi per kategori x akun, dan 2 kategori pinjaman bank dedicated) -
// satu fungsi ini dipakai bareng oleh halaman report (report-cashflow-realisation.php) dan
// export Excel (ekspor_cashflow_realisation.php) supaya logikanya cuma ada di satu tempat.
function cfrComputeReportData($conn, $start_date, $end_date) {
    global $penerimaanPinjamanBank, $pembayaranPinjamanBank;

    // Daftar kolom akun (bank aktif + kas kecil)
    $accounts = [];
    $sqlBank = mysqli_query($conn, "select bank_account as account, curr from b_masterbank where status = 'Active' order by id");
    while ($r = mysqli_fetch_assoc($sqlBank)) {
        $accounts[] = ['account' => $r['account'], 'label' => $r['account'], 'curr' => $r['curr']];
    }
    $sqlKas = mysqli_query($conn, "select no_coa as account, nama_coa as label from mastercoa_v2 where ind_categori5 = 'KAS' and nama_coa != 'POS SILANG' order by nama_coa");
    while ($r = mysqli_fetch_assoc($sqlKas)) {
        $accounts[] = ['account' => $r['account'], 'label' => $r['label'], 'curr' => 'IDR'];
    }

    $dateBefore = date('Y-m-d', strtotime($start_date . ' -1 day'));

    // Semua akun non-IDR (saat ini cuma USD) dikonversi ke IDR pakai satu kurs -
    // dicari sekali di tanggal akhir periode, sama seperti pendekatan bankreport.php
    // (kurs "hari ini", bukan kurs historis per transaksi).
    $rateIdr = cfrGetRate($conn, $end_date);
    $rateMap = [];
    foreach ($accounts as $acc) {
        $rateMap[$acc['account']] = ($acc['curr'] === 'IDR') ? 1 : $rateIdr;
    }

    // Saldo Awal rekening bank - pakai query resmi (saldo_awal_native + mutasi
    // native sebelum start_date, dikonversi kurs PAJAK di tanggal transaksi
    // terakhir sebelum start_date, clamp minus ke 0).
    $bankBeginIdr = cfrGetBankBeginBalances($conn, $start_date);

    // Saldo Awal akun kas kecil - pakai query resmi yang sama polanya
    // (kurs HARIAN, default IDR kalau belum ada baris b_saldoawal_pettycash).
    $kasBeginIdr = cfrGetKasBeginBalances($conn, $start_date);

    // Saldo Awal per akun (kas kecil dihitung native lalu dikonversi $rateMap;
    // rekening bank pakai $bankBeginIdr di atas).
    $beginBalance = [];
    $totalBegin = 0;
    foreach ($accounts as $acc) {
        $rate = $rateMap[$acc['account']];
        if (isset($bankBeginIdr[$acc['account']])) {
            $beginIdr = $bankBeginIdr[$acc['account']];
        } elseif (isset($kasBeginIdr[$acc['account']])) {
            $beginIdr = $kasBeginIdr[$acc['account']];
        } else {
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

    // Realisasi per kategori x akun - debit/credit disimpan dalam mata uang asli akun,
    // dikonversi ke IDR di PHP pakai $rateMap (bukan di query) supaya satu kurs per akun
    // konsisten dengan yang dipakai untuk Saldo Awal/Akhir di atas.
    $sqlReal = mysqli_query($conn, "select id_cash_flow, akun, coalesce(sum(debit),0) d, coalesce(sum(credit),0) c from (
        select id_cash_flow, akun, debit, credit from b_reportbank where status != 'Cancel' and transaksi_date between '$start_date' and '$end_date' and id_cash_flow is not null
        union all
        select id_cash_flow, akun, debit, credit from c_report_pettycash where status != 'Cancel' and transaksi_date between '$start_date' and '$end_date' and id_cash_flow is not null
    ) x group by id_cash_flow, akun");
    $realisasi = [];
    while ($r = mysqli_fetch_assoc($sqlReal)) {
        $rate = isset($rateMap[$r['akun']]) ? $rateMap[$r['akun']] : 1;
        $realisasi[$r['id_cash_flow']][$r['akun']] = ['debit' => (float) $r['d'] * $rate, 'credit' => (float) $r['c'] * $rate];
    }

    // "Penerimaan Pinjaman Bank" (id master_cash_flow = 9) & "Pelunasan Pinjaman Bank"
    // (id 47) pakai query dedicated (deteksi pencairan/pelunasan pinjaman dari perubahan
    // tanda saldo rekening BCA 008-997-1979/008-998-1982), bukan dari tagging id_cash_flow
    // transaksi biasa. Disimpan sebagai global supaya cfrRowValue/cfrRowTotal bisa akses.
    $penerimaanPinjamanBank = cfrGetPenerimaanPinjamanBank($conn, $start_date, $end_date);
    $pembayaranPinjamanBank = cfrGetPembayaranPinjamanBank($conn, $start_date, $end_date);

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
