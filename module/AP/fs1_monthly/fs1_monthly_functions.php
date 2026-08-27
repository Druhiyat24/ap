<?php
// Helper bersama untuk tab-tab "Financial Statement 1 - Monthly". BEDA
// KONVENSI dari fs_monthly_functions.php (punya FS2): FS1 TIDAK ADA
// breakdown profit center (NAG/NAK/ALL - satu perusahaan saja), dan tiap
// kolom bulan berisi nilai MANDIRI bulan itu SENDIRI (bukan kumulatif dari
// Januari seperti FS2), ditutup 1 kolom "Total" di ujung kanan yang
// menjumlahkan SEMUA bulan yang ditampilkan (bukan duplikat nilai bulan
// terakhir seperti "YTD" di FS2). Pola ini disalin apa adanya dari
// ekspor_spl_monthly.php / ekspor_cf_indirect_monthly_fix.php / dst (yang
// sudah berjalan & diverifikasi lewat Export Excel selama ini) - lihat
// catatan lengkap per fungsi di masing-masing fs1_monthly/*.php.

// Data source-nya juga beda: saldo_awal_tb (BUKAN fs_saldo_awal_tb - tidak
// ada kolom profit_center), dengan kolom per-bulan (saldo_jan..saldo_dec)
// yang SUDAH berisi saldo akhir bulan itu (snapshot bulanan yang di-maintain
// terpisah, bukan dihitung ulang dari jurnal tiap request).

// Daftar bulan dari tanggal awal s.d. akhir (inklusif), formatnya cocok
// dipakai baik untuk key kolom saldo_awal_tb (bulan[i] = 'jan'..'dec')
// maupun label tampilan.
function fs1mGetMonths($start_date, $end_date) {
    $start_ts = strtotime($start_date);
    $end_ts = strtotime($end_date);
    $cursor = mktime(0, 0, 0, (int) date('n', $start_ts), 1, (int) date('Y', $start_ts));
    $end_cursor = mktime(0, 0, 0, (int) date('n', $end_ts), 1, (int) date('Y', $end_ts));

    $months = [];
    while ($cursor <= $end_cursor) {
        $months[] = [
            'bulan' => date('m', $cursor),
            'tahun' => date('Y', $cursor),
            'label' => date('M Y', $cursor),
            'kata_filter' => date('M', $cursor) . '_' . date('Y', $cursor),
            // Key alfabetik 3-huruf (jan..dec) - dipakai buat baca kolom
            // saldo_jan..saldo_dec hasil fs1mGetCategoryTotals()/
            // fs1mGetBebanBunga() (kolom SQL-nya memang dinamai gitu, BUKAN
            // saldo_01..saldo_12).
            'bulan_key' => strtolower(date('M', $cursor)),
        ];
        $cursor = strtotime('+1 month', $cursor);
    }
    return $months;
}

// Format 1 angka - negatif ditulis dalam kurung. Duplikat sengaja dari
// fsMonthlyFormatNumber() punya fs_monthly_functions.php (FS2) - TIDAK
// require_once file itu supaya fs1_monthly/*.php tetap mandiri, tidak
// bergantung ke sistem FS2 sama sekali (2 sistem ini memang independen).
function fsMonthlyFormatNumber($val) {
    $val = (float) $val;
    if ($val < 0) {
        return '(' . number_format(abs($val), 2) . ')';
    }
    return number_format($val, 2);
}

// Colgroup: 1 kolom deskripsi + 1 kolom per bulan + 1 kolom "Total" di
// ujung kanan (BUKAN grup YTD 3-kolom seperti FS2 - FS1 tidak ada NAG/NAK,
// jadi cuma 1 kolom polos per bulan).
function fs1mColgroup($monthCount, $descW = 440, $valW = 145) {
    $html = '<colgroup><col style="width:' . $descW . 'px">';
    for ($i = 0; $i < $monthCount + 1; $i++) {
        $html .= '<col style="width:' . $valW . 'px">';
    }
    $html .= '</colgroup>';
    return $html;
}

function fs1mTableWidth($monthCount, $descW = 440, $valW = 145) {
    return $descW + ($monthCount + 1) * $valW;
}

// Tint warna selang-seling per bulan (1 kolom = 1 bulan, beda dari
// sfpmColClass() punya FS2 yang groupSize-nya bisa >1 kolom/bulan) + kolom
// "Total" di ujung kanan dikasih highlight sama seperti kolom YTD di FS2
// (background beda, dianggap "kolom ringkasan").
function fs1mColClass($idx, $monthCount) {
    $cls = $idx % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b';
    $cls .= ' sfpm-month-start';
    if ($idx >= $monthCount) {
        $cls .= ' sfpm-ytd';
    }
    return $cls;
}

// Render 1 baris nilai dari lookup [month_label] => float (urut sesuai
// $months) - kolom terakhir SELALU jumlah (SUM) semua bulan yang
// ditampilkan, bukan duplikat nilai bulan terakhir (beda dari
// sfpmRenderRowValues() punya FS2 - lihat catatan di atas file ini).
function fs1mRenderRow($months, $valuesByMonth, $tag, $cellClass) {
    $html = '';
    $idx = 0;
    $sum = 0.0;
    $monthCount = count($months);
    foreach ($months as $m) {
        $v = (float) ($valuesByMonth[$m['label']] ?? 0);
        $sum += $v;
        $html .= '<' . $tag . ' class="' . $cellClass . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
        $idx++;
    }
    $html .= '<' . $tag . ' class="' . $cellClass . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($sum) . '</' . $tag . '>';
    return $html;
}

// Baris kosong (blank filler) sejumlah bulan + 1 kolom Total.
function fs1mBlankCells($monthCount, $tag = 'th') {
    $html = '';
    for ($idx = 0; $idx <= $monthCount; $idx++) {
        $html .= '<' . $tag . ' class="' . trim(fs1mColClass($idx, $monthCount)) . '"></' . $tag . '>';
    }
    return $html;
}

function fs1mSpacerRow($monthCount, $rowClass = 'spacer') {
    echo '<tr class="' . $rowClass . '"><th class="sfpm-freeze-left"></th>' . fs1mBlankCells($monthCount) . '</tr>';
}

// ===== Helper query "12 bulan sekaligus" (dipakai berulang di semua
// laporan FS1 Monthly - Trial Balance/SPL/CF Direct/SFP) =====
// Potongan SQL "mastercoa_v2 LEFT JOIN 12x (kolom saldo_awal_tb bulan itu +
// pergerakan jurnal bulan itu)" - pola yang berulang PERSIS di setiap
// laporan FS1 Monthly (disalin apa adanya dari ekspor_*_monthly*.php,
// cuma di sini ditulis SEKALI lewat loop PHP, bukan copy-paste 12x manual,
// biar tidak ada risiko salah ketik transkripsi). $prevAlias SELALU "coa"
// (bukan dirantai bulan-ke-bulan) - persis sumber asli, setiap bulan
// join balik ke "coa.no_coa", bukan ke alias bulan sebelumnya.
function fs1mMonthlyJoinChain($tahun_akhir) {
    $months = ['jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12'];
    // Alias "dec" TIDAK BISA dipakai apa adanya - kata reserved di MySQL/
    // MariaDB (singkatan tipe DECIMAL) - sumber asli sudah akali ini pakai
    // alias "des" khusus utk Desember (lihat ekspor_spl_monthly.php baris
    // 220: "...) des on des.coa_no = coa.no_coa"), dipertahankan sama di
    // sini. Kolom saldo_dec (nama kolom, BUKAN alias tabel) tetap aman.
    $aliasOverride = ['dec' => 'des'];
    $sql = "(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa";
    foreach ($months as $m => $mm) {
        $alias = $aliasOverride[$m] ?? $m;
        $awalCol = $m === 'jan' ? 'saldo saldo_awal,' : '';
        $sql .= "\nleft join\n(select nocoa coa_no, $awalCol(saldo + coalesce((debit_idr - credit_idr),0)) saldo_$m from \n(select no_coa nocoa,{$m}_{$tahun_akhir} as saldo from saldo_awal_tb order by no_coa asc) saldo\nleft join\n(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$mm' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '$mm' and tahun = '$tahun_akhir') group by no_coa) \njnl on jnl.coa_no = saldo.nocoa order by nocoa asc) $alias on $alias.coa_no = coa.no_coa";
    }
    return $sql;
}

// Total per sub_kategori (fs_kategori_laporan) untuk SATU kategori "biasa" -
// dipakai utk PENJUALAN KOTOR/RETURN PENJUALAN/POTONGAN PENJUALAN/BEBAN
// POKOK PENJUALAN/BEBAN LAINNYA/BEBAN PAJAK (SEMUA kategori SPL KECUALI
// BEBAN BUNGA yang query-nya beda sendiri - lihat fs1mGetBebanBunga()).
// Nilai SELALU dinegasikan (if(saldo_X=0,0,-saldo_X)) - disalin apa adanya
// dari ekspor_spl_monthly.php (konvensi tanda FS1, berlaku utk SEMUA
// kategori di laporan SPL, bukan cuma yang "kontra-pendapatan").
// Return: array baris [id, sub_kategori, sub_kategori_eng, saldo_awal,
// saldo_jan..saldo_dec].
function fs1mGetCategoryTotals($conn2, $kategori, $tahun_akhir) {
    $kategoriEsc = mysqli_real_escape_string($conn2, $kategori);
    $joinChain = fs1mMonthlyJoinChain($tahun_akhir);
    $sql = "select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('$kategoriEsc')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from $joinChain order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// "BEBAN BUNGA" - SATU-SATUNYA kategori SPL yang query-nya BEDA (bukan pola
// 12-bulan-join biasa) - disalin apa adanya dari ekspor_spl_monthly.php
// (baris ~882-963): saldo_awal_tb + SUM(CASE WHEN MONTH(tgl_journal)=N ...)
// per bulan dihitung dari tbl_list_journal SATU TAHUN PENUH sekaligus
// (WHERE YEAR(tgl_journal) = $tahun_akhir), difilter coa.id_ctg2 = '8'.
function fs1mGetBebanBunga($conn2, $tahun_akhir) {
    $sql = "select
  a.id,
  a.sub_kategori,
  a.sub_kategori_eng,
IF(t.saldo_awal = 0, 0, -t.saldo_awal) AS saldo_awal,
  IF(t.saldo_jan   = 0, 0, -t.saldo_jan) AS saldo_jan,
  IF(t.saldo_feb   = 0, 0, -t.saldo_feb) AS saldo_feb,
  IF(t.saldo_mar   = 0, 0, -t.saldo_mar) AS saldo_mar,
  IF(t.saldo_apr   = 0, 0, -t.saldo_apr) AS saldo_apr,
  IF(t.saldo_may   = 0, 0, -t.saldo_may) AS saldo_may,
  IF(t.saldo_jun   = 0, 0, -t.saldo_jun) AS saldo_jun,
  IF(t.saldo_jul   = 0, 0, -t.saldo_jul) AS saldo_jul,
  IF(t.saldo_aug   = 0, 0, -t.saldo_aug) AS saldo_aug,
  IF(t.saldo_sep   = 0, 0, -t.saldo_sep) AS saldo_sep,
  IF(t.saldo_oct   = 0, 0, -t.saldo_oct) AS saldo_oct,
  IF(t.saldo_nov   = 0, 0, -t.saldo_nov) AS saldo_nov,
  IF(t.saldo_dec   = 0, 0, -t.saldo_dec) AS saldo_dec
FROM fs_kategori_laporan a
LEFT JOIN master_coa_ctg4 c
  ON c.ind_name = a.sub_kategori
LEFT JOIN (
  SELECT
    id_ctg4,
    SUM(saldo_awal) saldo_awal,
    SUM(saldo_jan)   saldo_jan,
    SUM(saldo_feb)   saldo_feb,
    SUM(saldo_mar)   saldo_mar,
    SUM(saldo_apr)   saldo_apr,
    SUM(saldo_may)   saldo_may,
    SUM(saldo_jun)   saldo_jun,
    SUM(saldo_jul)   saldo_jul,
    SUM(saldo_aug)   saldo_aug,
    SUM(saldo_sep)   saldo_sep,
    SUM(saldo_oct)   saldo_oct,
    SUM(saldo_nov)   saldo_nov,
    SUM(saldo_dec)   saldo_dec
  FROM (
    SELECT
      coa.id_ctg4,
      sa.jan_$tahun_akhir AS saldo_awal,
      COALESCE(sa.jan_$tahun_akhir + j.jan_diff, sa.jan_$tahun_akhir) AS saldo_jan,
      COALESCE(sa.feb_$tahun_akhir + j.feb_diff, sa.feb_$tahun_akhir) AS saldo_feb,
      COALESCE(sa.mar_$tahun_akhir + j.mar_diff, sa.mar_$tahun_akhir) AS saldo_mar,
      COALESCE(sa.apr_$tahun_akhir + j.apr_diff, sa.apr_$tahun_akhir) AS saldo_apr,
      COALESCE(sa.may_$tahun_akhir + j.may_diff, sa.may_$tahun_akhir) AS saldo_may,
      COALESCE(sa.jun_$tahun_akhir + j.jun_diff, sa.jun_$tahun_akhir) AS saldo_jun,
      COALESCE(sa.jul_$tahun_akhir + j.jul_diff, sa.jul_$tahun_akhir) AS saldo_jul,
      COALESCE(sa.aug_$tahun_akhir + j.aug_diff, sa.aug_$tahun_akhir) AS saldo_aug,
      COALESCE(sa.sep_$tahun_akhir + j.sep_diff, sa.sep_$tahun_akhir) AS saldo_sep,
      COALESCE(sa.oct_$tahun_akhir + j.oct_diff, sa.oct_$tahun_akhir) AS saldo_oct,
      COALESCE(sa.nov_$tahun_akhir + j.nov_diff, sa.nov_$tahun_akhir) AS saldo_nov,
      COALESCE(sa.dec_$tahun_akhir + j.dec_diff, sa.dec_$tahun_akhir) AS saldo_dec
    FROM mastercoa_v2 coa
    LEFT JOIN saldo_awal_tb sa
      ON sa.no_coa = coa.no_coa
    LEFT JOIN (
      SELECT
        no_coa,
        SUM(CASE WHEN MONTH(tgl_journal) = 1 THEN debit*rate - credit*rate END) AS jan_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 2 THEN debit*rate - credit*rate END) AS feb_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 3 THEN debit*rate - credit*rate END) AS mar_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 4 THEN debit*rate - credit*rate END) AS apr_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 5 THEN debit*rate - credit*rate END) AS may_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 6 THEN debit*rate - credit*rate END) AS jun_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 7 THEN debit*rate - credit*rate END) AS jul_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 8 THEN debit*rate - credit*rate END) AS aug_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 9 THEN debit*rate - credit*rate END) AS sep_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 10 THEN debit*rate - credit*rate END) AS oct_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 11 THEN debit*rate - credit*rate END) AS nov_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 12 THEN debit*rate - credit*rate END) AS dec_diff
      FROM tbl_list_journal
      WHERE YEAR(tgl_journal) = $tahun_akhir
      GROUP BY no_coa
    ) j ON j.no_coa = coa.no_coa
    WHERE coa.id_ctg2 = '8'
  ) sub
  GROUP BY id_ctg4
) t ON t.id_ctg4 = c.id_ctg4
WHERE a.status = 'Y'
  AND a.kategori = 'BEBAN BUNGA'
ORDER BY a.id";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Bantu ambil [month_label] => float dari hasil fs1mGetCategoryTotals()/
// fs1mGetBebanBunga() (rows dgn kolom saldo_jan..saldo_dec) - dijumlah
// SEMUA sub_kategori dalam kategori itu, per bulan yang tampil di $months.
function fs1mSumCategoryByMonth($rows, $months) {
    $byMonth = [];
    foreach ($months as $m) {
        $key = 'saldo_' . $m['bulan_key'];
        $total = 0.0;
        foreach ($rows as $row) {
            $total += (float) ($row[$key] ?? 0);
        }
        $byMonth[$m['label']] = $total;
    }
    return $byMonth;
}

// "Kas dan Setara Kas pada Awal Periode" - query IDENTIK dgn yang dipakai
// fs1ComputeCfIndirectMonth() (cashflow_indirect.php) - disalin lagi di
// sini sebagai fungsi bersama supaya CF Direct tidak perlu duplikasi
// verbatim (CF Indirect tetap pakai versi inline-nya sendiri, TIDAK diubah,
// biar tidak menyentuh kode yang sudah diverifikasi jalan).
function fs1mGetBeginningCash($conn2, $kata_filter) {
    $sql = mysqli_query($conn2, "select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2026') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2026') group by no_coa)
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");
    $row = mysqli_fetch_array($sql);
    return isset($row['total']) ? (float) $row['total'] : 0.0;
}

// ===== Helper khusus CF Direct FS1 =====
// Beda mesin lagi dari CF Indirect/SPL: sebagian besar kategori Operasi &
// SEMUA kategori Investasi diambil dari tabel snapshot bulanan
// tb_monthly_$tahun_akhir (nama tabel per-tahun, di-maintain EKSTERNAL/di
// luar file ini - sudah dicek langsung ke DB, tabelnya beneran ada & terisi
// data real, bukan tabel usang) - BUKAN dihitung ulang dari jurnal seperti
// laporan FS1 lain. HANYA 1 baris Operasi (id=2, "Pembayaran Kepada
// Pemasok") yang dihitung LIVE (lewat tb_master_pilihan + revaluasi kurs),
// disalin apa adanya dari ekspor_cf_direct_monthly_fix.php.
//
// Pinjaman (Penerimaan/Pembayaran) dihitung LIVE lewat accounts CTE (deteksi
// overdraft crossing, pola sama dgn FS2 CF Direct) - accounts CTE ini di
// sumber aslinya PUNYA BUG: baris akun profit_center NAK ditandai 'NAG'
// (bukan cuma di 1 tempat/1 bulan - terjadi di 12 bulan x 2 query
// Penerimaan+Pembayaran, bikin saldo NAG & NAK tercampur SEBELUM deteksi
// overdraft, jadi benar-benar mengubah angka Pinjaman yang dihitung). User
// SUDAH ditanya (fix vs replikasi apa adanya) dan MEMILIH REPLIKASI APA
// ADANYA - fs1mCfDirectLoanAccountsCte() di bawah SENGAJA menulis 'NAG'
// utk baris yang WHERE-nya profit_center='NAK', PERSIS sumber asli. JANGAN
// "diperbaiki" tanpa tanya ulang ke user.

// Accounts CTE 6-akun (NAG x 1.10.02/1.10.01, NAK x 1.10.02/1.10.01/1.10.41/
// 1.10.42) - CORRECTLY labeled, dipakai utk hitung revaluasi (bagian id=2
// Operasi). TIDAK ada bug di sini (beda dari accounts CTE Pinjaman).
function fs1mCfDirectRevalAccountsCte($tahun_awal) {
    $months = ['01' => 'jan', '02' => 'feb', '03' => 'mar', '04' => 'apr', '05' => 'may', '06' => 'jun', '07' => 'jul', '08' => 'aug', '09' => 'sep', '10' => 'oct', '11' => 'nov', '12' => 'dec'];
    $accounts = [
        ['NAG', '1.10.02', '008-998-1982', '2.20.02'],
        ['NAG', '1.10.01', '008-997-1979', '2.20.01'],
        ['NAK', '1.10.02', '008-998-1982', '2.20.02'],
        ['NAK', '1.10.01', '008-997-1979', '2.20.01'],
        ['NAK', '1.10.41', '008-759-5858', null],
        ['NAK', '1.10.42', '008-751-5757', null],
    ];
    $blocks = [];
    foreach ($months as $mm => $m) {
        foreach ($accounts as $acc) {
            [$pc, $coa, $akun, $contra] = $acc;
            $inClause = $contra ? "'$coa','$contra'" : "'$coa'";
            $blocks[] = "SELECT '$pc' AS profit_center, '$coa' AS no_coa, '$akun' AS akun, '$tahun_awal-$mm' periode, s.{$m}_{$tahun_awal} FROM fs_saldo_awal_tb s WHERE s.no_coa IN ($inClause) AND s.profit_center = '$pc'";
        }
    }
    return "accounts AS (SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal FROM (" . implode(" UNION ALL ", $blocks) . ") AS a GROUP BY profit_center, no_coa, akun, periode)";
}

// Accounts CTE 4-akun (NAG x 1.10.02/1.10.01, + 2 baris WHERE-nya NAK tapi
// LABEL OUTPUT-nya 'NAG' - BUG, direplikasi sengaja apa adanya, lihat
// catatan panjang di atas) - dipakai utk deteksi Pinjaman
// (Penerimaan/Pembayaran).
function fs1mCfDirectLoanAccountsCte($tahun_awal) {
    $months = ['01' => 'jan', '02' => 'feb', '03' => 'mar', '04' => 'apr', '05' => 'may', '06' => 'jun', '07' => 'jul', '08' => 'aug', '09' => 'sep', '10' => 'oct', '11' => 'nov', '12' => 'dec'];
    // profit_center KOLOM PERTAMA (label output) vs KOLOM TERAKHIR (filter
    // WHERE sesungguhnya) SENGAJA beda utk 2 baris terakhir - itulah bug-nya.
    $accounts = [
        ['NAG', '1.10.02', '008-998-1982', '2.20.02', 'NAG'],
        ['NAG', '1.10.01', '008-997-1979', '2.20.01', 'NAG'],
        ['NAG', '1.10.02', '008-998-1982', '2.20.02', 'NAK'],
        ['NAG', '1.10.01', '008-997-1979', '2.20.01', 'NAK'],
    ];
    $blocks = [];
    foreach ($months as $mm => $m) {
        foreach ($accounts as $acc) {
            [$labelPc, $coa, $akun, $contra, $wherePc] = $acc;
            $blocks[] = "SELECT '$labelPc' AS profit_center, '$coa' AS no_coa, '$akun' AS akun, '$tahun_awal-$mm' periode, s.{$m}_{$tahun_awal} FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('$coa','$contra') AND s.profit_center = '$wherePc'";
        }
    }
    return "accounts AS (SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal FROM (" . implode(" UNION ALL ", $blocks) . ") AS a GROUP BY profit_center, no_coa, akun, periode)";
}

// Rantai journal_sums -> reval -> base -> calc -> agg, IDENTIK utk kedua
// accounts CTE di atas (cuma "accounts" yang beda) - disalin apa adanya
// dari ekspor_cf_direct_monthly_fix.php (dipakai berkali-kali di file itu,
// isinya sama persis tiap pemakaian).
function fs1mCfDirectBaseCalcAggCte($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    return "
    journal_sums AS (
      SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
             SUM(l.rate * l.debit)  AS debit,
             SUM(l.rate * l.credit) AS credit
      FROM tbl_list_journal l
      WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
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
        AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
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
        c.profit_center, c.periode,
        SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
        SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
        SUM(COALESCE(
            CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
        ,0)) AS debit_revaluasi,
        SUM(COALESCE(
            CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
        ,0)) AS credit_revaluasi
      FROM calc c
      LEFT JOIN reval r
        ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
      GROUP BY c.profit_center, c.periode
    )";
}

function _fs1mCfDirectJournalFilter() {
    return "(no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%')";
}

// "PEMBAYARAN KEPADA PEMASOK" (id=2 tb_master_pilihan, kategori Operasi) -
// SATU-SATUNYA baris Operasi yang dihitung LIVE (bukan dari snapshot
// tb_monthly_$tahun) karena butuh penyesuaian revaluasi kurs di atasnya -
// disalin apa adanya dari ekspor_cf_direct_monthly_fix.php.
function fs1mGetCfDirectOperatingId2($conn2, $bulan, $tahun) {
    $journalFilter = _fs1mCfDirectJournalFilter();
    $dateFilter = "tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun')";
    $sql = "WITH
    " . fs1mCfDirectRevalAccountsCte($tahun) . ",
    " . fs1mCfDirectBaseCalcAggCte($bulan, $tahun, $bulan, $tahun) . ",
    revaluasi as (select '2' id, periode, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg GROUP BY periode),
    pembayaran as (select a.id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, p.periode AS periode, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a
    CROSS JOIN (" . _fs1mCfDirectMonthUnion($tahun) . ") p
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, p.periode) a where a.id = 2 ORDER BY a.id, periode ASC)
    select a.id, sub_kategori, sub_kategori_eng, sum(total_all + revaluasi) total from pembayaran a INNER JOIN revaluasi b on b.id = a.id and b.periode = a.periode group by a.id";

    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    return [
        'sub_kategori' => $row['sub_kategori'] ?? 'Pembayaran Kepada Pemasok',
        'sub_kategori_eng' => $row['sub_kategori_eng'] ?? '',
        'total' => (float) ($row['total'] ?? 0),
    ];
}

function _fs1mCfDirectMonthUnion($tahun_awal) {
    $parts = [];
    foreach (['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $m) {
        $parts[] = "SELECT '$tahun_awal-$m' periode";
    }
    return implode(' UNION ALL ', $parts);
}

// Kategori "biasa" Operasi (semua KECUALI id=2) & SEMUA kategori Investasi -
// diambil dari tabel snapshot tb_monthly_$tahun_akhir (id, ind_name,
// saldo_jan..saldo_dec - sudah dicek langsung strukturnya ke DB), BUKAN
// dihitung ulang dari jurnal. $excludeId2 = true utk Operasi (buang id=2,
// yang dihitung terpisah lewat fs1mGetCfDirectOperatingId2()).
//
// CATATAN PENTING: tb_monthly_$tahun punya BEBERAPA baris dgn ind_name yang
// SAMA (mis. 3 baris "PEMBELIAN ASET TETAP" dgn id internal beda - 1 baris
// asli berisi angka, sisanya NULL/duplikat usang) - INNER JOIN di sumber
// asli (bukan LEFT JOIN) otomatis membuang kategori yg SAMA SEKALI tidak
// match (spt "Penambahan/Penjualan investasi pada instrumen keuangan"),
// dan agregasi (SUM utk Operasi via query_lama, atau GROUP BY tanpa
// SUM/non-deterministic utk Investasi) menyerap duplikat2 tsb krn nilainya
// NULL->0. Direplikasi di sini dgn INNER JOIN + SUM(COALESCE(saldo,0)) -
// scr matematis identik dgn kedua pola aslinya SELAMA baris duplikat
// bernilai NULL (terverifikasi via query manual ke tb_monthly_2026), tanpa
// bergantung pada perilaku GROUP BY non-standard MySQL (ONLY_FULL_GROUP_BY
// off) yg dipakai versi Investasi di sumber asli.
function fs1mGetCfDirectSnapshotCategory($conn2, $typePilihan, $tahun_akhir, $bulanKey, $excludeId2 = false) {
    $typeEsc = mysqli_real_escape_string($conn2, $typePilihan);
    $excludeClause = $excludeId2 ? ' and id != 2' : '';
    $tableEsc = 'tb_monthly_' . preg_replace('/[^0-9]/', '', $tahun_akhir);
    $bulanKeyWhitelist = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    if (!in_array($bulanKey, $bulanKeyWhitelist, true)) {
        $bulanKey = 'jan';
    }
    $sql = "select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, SUM(COALESCE(b.saldo_$bulanKey,0)) total
        from (select * from tb_master_pilihan where status = 'Y' and type_pilihan = '$typeEsc'$excludeClause) a
        inner join $tableEsc b on b.ind_name = a.nama_pilihan
        group by a.id, a.nama_pilihan, a.nama_pilihan_eng
        order by a.id asc";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Penerimaan/Pembayaran Pinjaman - accounts CTE Pinjaman (dgn bug label
// NAK->NAG, direplikasi apa adanya - lihat catatan panjang di atas file
// ini) + other_value (entri manual non-akun-kas-utama, id=14 utk
// Penerimaan, id=15 utk Pembayaran, TIDAK SAMA formula gabungannya -
// Penerimaan = pivot + other_value(14), Pembayaran = -(pivot -
// other_value(15)) - disalin apa adanya dari ekspor_cf_direct_monthly_fix.php,
// SATU query dipakai bareng utk kedua sub_kategori karena pivot-nya sama
// persis dipakai 2x di sumber asli (duplikasi tidak perlu dipertahankan).
function fs1mGetCfDirectLoans($conn2, $bulan, $tahun, $tanggal_awal, $tanggal_akhir) {
    $journalFilter = _fs1mCfDirectJournalFilter();
    $dateFilter = "tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun')";
    $monthUnion = _fs1mCfDirectMonthUnion($tahun);

    $otherValue = function ($idVal) use ($tahun, $dateFilter, $journalFilter) {
        return "(select p.periode periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak
             from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan' and id = '$idVal') a
             CROSS JOIN (" . _fs1mCfDirectMonthUnion($tahun) . ") p
             LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
             LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
             LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
             LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode
             GROUP BY p.periode)";
    };

    $sql = "WITH
    " . fs1mCfDirectLoanAccountsCte($tahun) . ",
    " . fs1mCfDirectBaseCalcAggCte($bulan, $tahun, $bulan, $tahun) . ",
    pivot AS (
      SELECT periode,
        SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
        SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
        SUM(CASE WHEN profit_center='NAG' THEN pembayaran_pinjaman ELSE 0 END) AS pembayaran_NAG,
        SUM(CASE WHEN profit_center='NAK' THEN pembayaran_pinjaman ELSE 0 END) AS pembayaran_NAK
      FROM agg GROUP BY periode
    ),
    other_terima AS " . $otherValue('14') . ",
    other_bayar AS " . $otherValue('15') . "
    SELECT
      (SELECT COALESCE(SUM(p.penerimaan_NAG + p.penerimaan_NAK + COALESCE(ot.total_nag,0) + COALESCE(ot.total_nak,0)),0) FROM pivot p LEFT JOIN other_terima ot ON ot.periode = p.periode WHERE p.periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')) AS penerimaan_total,
      (SELECT COALESCE(SUM(-(p.pembayaran_NAG + p.pembayaran_NAK) + COALESCE(ob.total_nag,0) + COALESCE(ob.total_nak,0)),0) FROM pivot p LEFT JOIN other_bayar ob ON ob.periode = p.periode WHERE p.periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')) AS pembayaran_total";

    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    return [
        'penerimaan_pinjaman' => (float) ($row['penerimaan_total'] ?? 0),
        'pembayaran_pinjaman' => (float) ($row['pembayaran_total'] ?? 0),
    ];
}

// Fungsi utama - 1 bulan mandiri (bulan_awal=bulan_akhir=$bulan), pola sama
// dengan fs1ComputeCfIndirectMonth(). Disalin apa adanya dari
// ekspor_cf_direct_monthly_fix.php (computeCfDirectMonth()).
function fs1ComputeCfDirectMonth($conn2, $bulan, $tahun, $kata_filter) {
    $tanggal_awal = _fs1mCfDirectTanggal($conn2, $bulan, $tahun, 'tgl_awal');
    $tanggal_akhir = _fs1mCfDirectTanggal($conn2, $bulan, $tahun, 'tgl_akhir');

    $result = [
        'operating' => [],
        'operating_total' => 0.0,
        'investing' => [],
        'investing_total' => 0.0,
        'penerimaan_pinjaman' => 0.0,
        'pembayaran_pinjaman' => 0.0,
        'pendanaan_total' => 0.0,
        'net_change' => 0.0,
        'beginning_cash' => 0.0,
        'ending_cash' => 0.0,
    ];

    // Operasi: kategori "biasa" dari snapshot (kecuali id=2) + id=2 live.
    $bulanKey = strtolower(date('M', mktime(0, 0, 0, (int) $bulan, 1, (int) $tahun)));
    $opRows = fs1mGetCfDirectSnapshotCategory($conn2, 'Arus Kas dari Aktivitas Operasi', $tahun, $bulanKey, true);
    foreach ($opRows as $row) {
        if (empty($row['sub_kategori'])) {
            continue;
        }
        $val = (float) ($row['total'] ?? 0);
        $result['operating'][$row['sub_kategori']] = ['total' => $val, 'eng' => $row['sub_kategori_eng'] ?? ''];
        $result['operating_total'] += $val;
    }
    $id2 = fs1mGetCfDirectOperatingId2($conn2, $bulan, $tahun);
    $result['operating'][$id2['sub_kategori']] = ['total' => $id2['total'], 'eng' => $id2['sub_kategori_eng']];
    $result['operating_total'] += $id2['total'];

    // Investasi: SEMUA kategori dari snapshot.
    $invRows = fs1mGetCfDirectSnapshotCategory($conn2, 'Arus Kas dari Aktivitas Investasi', $tahun, $bulanKey, false);
    foreach ($invRows as $row) {
        if (empty($row['sub_kategori'])) {
            continue;
        }
        $val = (float) ($row['total'] ?? 0);
        $result['investing'][$row['sub_kategori']] = ['total' => $val, 'eng' => $row['sub_kategori_eng'] ?? ''];
        $result['investing_total'] += $val;
    }

    // Pendanaan (Pinjaman) - live.
    $loans = fs1mGetCfDirectLoans($conn2, $bulan, $tahun, $tanggal_awal, $tanggal_akhir);
    $result['penerimaan_pinjaman'] = $loans['penerimaan_pinjaman'];
    $result['pembayaran_pinjaman'] = $loans['pembayaran_pinjaman'];
    $result['pendanaan_total'] = $result['penerimaan_pinjaman'] + $result['pembayaran_pinjaman'];

    $result['net_change'] = $result['operating_total'] + $result['investing_total'] + $result['pendanaan_total'];
    $result['beginning_cash'] = fs1mGetBeginningCash($conn2, $kata_filter);
    $result['ending_cash'] = $result['beginning_cash'] + $result['net_change'];

    return $result;
}

function _fs1mCfDirectTanggal($conn2, $bulan, $tahun, $kolom) {
    $bulanEsc = mysqli_real_escape_string($conn2, $bulan);
    $tahunEsc = mysqli_real_escape_string($conn2, $tahun);
    $result = mysqli_query($conn2, "select $kolom from tbl_tgl_tb where bulan = '$bulanEsc' and tahun = '$tahunEsc'");
    $row = mysqli_fetch_assoc($result);
    $val = $row[$kolom] ?? null;
    return $val ? date('Y-m-d', strtotime($val)) : date('Y-m-d');
}

// ===== Helper khusus SFP (Statement of Financial Position) FS1 =====
// Mesin BEDA lagi dari SPL/CF: SFP adalah laporan POSISI (saldo per akhir
// bulan, bukan arus/pergerakan) - tiap baris = SALDO KUMULATIF akhir bulan
// itu (bukan nilai mandiri bulan itu saja), dan kolom terakhir bukan "Total"
// (SUM 12 bulan - tidak masuk akal utk saldo neraca), tapi "YTD" = nilai
// bulan TERAKHIR yang ditampilkan (persis header komentar
// "<th>YTD</th>" & variabel $saldo_feb_/$saldo_mar_/dst di
// ekspor_sfp_monthly.php baris ~254-283, yang selalu = nilai bulan
// terakhir). Disalin apa adanya, TIDAK memakai fs1mRenderRow() (itu utk
// SUM) - dipakai fs1mRenderRowLastMonth() di bawah.
//
// Nilai per line-item diambil dari fs1mMonthlyJoinChain() yang SAMA dgn
// SPL/TB (mastercoa_v2 + saldo_awal_tb + jurnal per bulan), TANPA negasi
// (beda dari SPL yg selalu -saldo), difilter WHERE a.id_ctg4 = 'XXX' GROUP
// BY a.id_ctg4 - disalin apa adanya dari ekspor_sfp_monthly.php (mis.
// baris ~163-237 utk "Kas dan bank" id_ctg4='111'). Subtotal/grand-total di
// sumber asli dihitung lewat query SQL sum() terpisah (kadang dgn UNION),
// tapi karena sum() itu cuma menjumlah balik nilai per-kategori yg SAMA
// yang sudah saya ambil satu-satu, dijumlah di PHP saja (asosiatif/hasil
// identik, sudah dicek query SQL aslinya tidak ada trik/agregasi lain).
function fs1mGetSfpLineTotal($conn2, $idCtg4, $tahun_akhir) {
    $idCtg4Esc = mysqli_real_escape_string($conn2, $idCtg4);
    $joinChain = fs1mMonthlyJoinChain($tahun_akhir);
    $sql = "select sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec,id_ctg4 from $joinChain order by no_coa asc) a where a.id_ctg4 = '$idCtg4Esc' group by a.id_ctg4";

    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row ?: array_fill_keys(['saldo_awal', 'saldo_jan', 'saldo_feb', 'saldo_mar', 'saldo_apr', 'saldo_may', 'saldo_jun', 'saldo_jul', 'saldo_aug', 'saldo_sep', 'saldo_oct', 'saldo_nov', 'saldo_dec'], 0);
}

// "Laba Tahun Berjalan" (current-year profit plug pada EKUITAS) - SATU-
// SATUNYA baris SFP yang TIDAK difilter id_ctg4, tapi WHERE no_coa >=
// '3.40.01' (rentang akun P&L), TANPA group by (satu baris agregat) -
// disalin apa adanya dari ekspor_sfp_monthly.php (baris ~6634-6720).
function fs1mGetSfpLabaTahunBerjalan($conn2, $tahun_akhir) {
    $joinChain = fs1mMonthlyJoinChain($tahun_akhir);
    $sql = "select sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from $joinChain order by no_coa asc) a where a.no_coa >= '3.40.01'";

    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row ?: array_fill_keys(['saldo_awal', 'saldo_jan', 'saldo_feb', 'saldo_mar', 'saldo_apr', 'saldo_may', 'saldo_jun', 'saldo_jul', 'saldo_aug', 'saldo_sep', 'saldo_oct', 'saldo_nov', 'saldo_dec'], 0);
}

// Baris "saldo_jan".."saldo_dec" (dari fs1mGetSfpLineTotal/
// fs1mGetSfpLabaTahunBerjalan, 1 tahun penuh) -> [month_label] => float,
// dipotong ke bulan2 yang ditampilkan ($months) saja.
function fs1mSfpRowToByMonth($row, $months) {
    $byMonth = [];
    foreach ($months as $m) {
        $byMonth[$m['label']] = (float) ($row['saldo_' . $m['bulan_key']] ?? 0);
    }
    return $byMonth;
}

// Tambah 2 lookup [month_label]=>float elemen-per-elemen (utk menjumlah
// subtotal dari beberapa line-item, pengganti SQL sum() - lihat catatan
// panjang di atas).
function fs1mSfpAddByMonth($a, $b) {
    $out = $a;
    foreach ($b as $label => $v) {
        $out[$label] = ($out[$label] ?? 0) + $v;
    }
    return $out;
}

// Render 1 baris SFP - kolom terakhir ("YTD") = nilai BULAN TERAKHIR yang
// ditampilkan (BUKAN SUM - beda dari fs1mRenderRow(), lihat catatan di
// atas file ini kenapa SFP beda mesin dari SPL/CF).
function fs1mRenderRowLastMonth($months, $valuesByMonth, $tag, $cellClass) {
    $html = '';
    $idx = 0;
    $monthCount = count($months);
    $lastLabel = end($months)['label'];
    foreach ($months as $m) {
        $v = (float) ($valuesByMonth[$m['label']] ?? 0);
        $html .= '<' . $tag . ' class="' . $cellClass . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
        $idx++;
    }
    $lastVal = (float) ($valuesByMonth[$lastLabel] ?? 0);
    $html .= '<' . $tag . ' class="' . $cellClass . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($lastVal) . '</' . $tag . '>';
    return $html;
}
