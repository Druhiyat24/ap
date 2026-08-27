<?php
// Helper bersama untuk semua tab "Financial Statement 2 - Monthly". Setiap
// kolom bulan di tab-tab ini adalah angka YTD (kumulatif dari Januari tahun
// berjalan) yang dipotong sampai akhir bulan tersebut - jadi query per bulan
// PERSIS sama dengan query fs_ytd/*.php, cuma bulan_akhir/tahun_akhir yang
// berubah tiap kolom sementara bulan_awal/tahun_awal tetap dikunci ke bulan
// "From" (yang di halaman ini selalu default Januari tahun berjalan).

// Daftar bulan dari (bulan_awal,tahun_awal) sampai (bulan_akhir,tahun_akhir)
// inklusif kedua ujungnya. Dipakai untuk generate 1 kolom tabel per bulan.
function fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $periods = [];
    $cursor = (int) $tahun_awal * 12 + ((int) $bulan_awal - 1);
    $end = (int) $tahun_akhir * 12 + ((int) $bulan_akhir - 1);

    while ($cursor <= $end) {
        $tahun = (int) floor($cursor / 12);
        $bulan = ($cursor % 12) + 1;
        $bulanStr = sprintf('%02d', $bulan);
        $periods[] = [
            'bulan' => $bulanStr,
            'tahun' => (string) $tahun,
            'label' => date('M', mktime(0, 0, 0, $bulan, 1, $tahun)) . ' ' . $tahun,
            'kata_filter' => date('M', mktime(0, 0, 0, $bulan, 1, $tahun)) . '_' . $tahun,
        ];
        $cursor++;
    }

    return $periods;
}

// 1 bulan persis sebelum (bulan_awal,tahun_awal) - dipakai untuk kolom saldo
// awal ("Des <tahun-1>" kalau From = Januari, tapi dinamis ikut "From" yang
// dipilih user - misal From = Feb 2026 maka ini jadi Jan 2026, bukan
// dikunci ke Desember).
function fsMonthlyGetPrevPeriod($bulan_awal, $tahun_awal) {
    $ts = mktime(0, 0, 0, (int) $bulan_awal - 1, 1, (int) $tahun_awal);
    return [
        'bulan' => date('m', $ts),
        'tahun' => date('Y', $ts),
        'label' => date('M Y', $ts),
        'kata_filter' => date('M', $ts) . '_' . date('Y', $ts),
    ];
}

// Rollup per sub_kategori (fs_kategori_laporan) untuk satu bulan - pola SQL-nya
// diambil apa adanya dari fs_ytd/statement_financial_position.php dan
// fs_ytd/statement_profit_loss.php (cuma bulan_akhir yang beda-beda tiap
// panggilan). $creditNormal=false pakai formula (saldo + debit - credit)
// seperti dipakai SFP untuk SEMUA kategorinya (Aset maupun Liabilitas/Ekuitas -
// bukan typo, memang begitu formula yang sudah berjalan di fs_ytd), sedangkan
// $creditNormal=true pakai (saldo + credit - debit) seperti dipakai SPL untuk
// akun pendapatan/beban.
function fsGetCategoryTotals($conn2, $kategoriList, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir, $kata_filter, $creditNormal = false) {
    $kategoriIn = implode(',', array_map(function ($k) use ($conn2) {
        return "'" . mysqli_real_escape_string($conn2, $k) . "'";
    }, $kategoriList));

    $sign = $creditNormal ? '(saldo + credit_idr - debit_idr)' : '(saldo + debit_idr - credit_idr)';

    $pcSaldo = function ($pc) use ($conn2, $sign, $kata_filter, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
        $pcEsc = mysqli_real_escape_string($conn2, $pc);
        return "
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, $sign saldo_akhir from (
                select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr
                from (select no_coa, $kata_filter as saldo from fs_saldo_awal_tb where profit_center = '$pcEsc' order by no_coa asc) a
                LEFT JOIN (
                    select no_coa, sum(ROUND(credit * rate,2)) credit_idr, sum(ROUND(debit * rate,2)) debit_idr
                    from tbl_list_journal
                    where tgl_journal BETWEEN
                        (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal')
                        and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
                        and profit_center = '$pcEsc'
                    group by no_coa
                ) e on e.no_coa = a.no_coa
            ) a order by no_coa asc)
        ";
    };

    $sql = "
        select id, sub_kategori, sub_kategori_eng, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all
        from (select id, ref, sub_kategori, sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ($kategoriIn)) a
        left JOIN (
            select a.*, sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all
            from (
                select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4
                from mastercoa_v2 a
                left join (
                    select a.id_ctg5 as id_ctg5A, b.ind_name as indname4, c.ind_name as indname3,
                           d.ind_name as indname2, e.ind_name as indname1
                    from master_coa_ctg5 a
                    INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4
                    INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3
                    INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2
                    INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1
                    GROUP BY a.id_ctg5
                ) b on b.id_ctg5A = a.id_ctg5
                GROUP BY no_coa
            ) a
            left join " . $pcSaldo('NAG') . " b on b.no_coa = a.no_coa
            left join " . $pcSaldo('NAK') . " c on c.no_coa = a.no_coa
            GROUP BY id_ctg4
        ) b on b.ind_categori4 = a.sub_kategori
        GROUP BY a.id order by id asc
    ";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// ===== Helper khusus Cash Flow Indirect =====
// Kategorisasi akun di laporan arus kas TIDAK LANGSUNG (indirect method)
// beda mesin sama sekali dari SFP/SPL: bukan lewat hierarki
// master_coa_ctg1..5 (id_ctg4), tapi lewat mastercoa_v2.id_indirect yang
// di-mapping ke tbl_master_cashflow.id/ind_name. Formula-nya juga murni
// PERGERAKAN JURNAL 1 PERIODE SAJA (bukan saldo_awal + pergerakan seperti
// fsGetCategoryTotals) - pola SQL disalin apa adanya dari
// fs_ytd/cashflow_indirect.php ($sql2/$sql3/$sql4), cuma bulan_akhir/
// tahun_akhir yang diparameter-ulang supaya bisa dipanggil per kolom bulan.

// Total per sub_kategori (fs_kategori_laporan) untuk 1 kategori arus kas
// indirect (mis. 'Arus Kas dari Aktivitas Operasi_ind') - dipakai untuk 3
// section utama (Operasi/Investasi/Pendanaan). $cashflowFilter opsional
// (mis. "where status = 'Active' and id >= 4") - dipertahankan apa adanya
// dari query asli buat section Investasi yang punya filter tambahan ini di
// YTD-nya (section lain tidak pakai filter ini).
function fsGetCashflowIndirectCategoryTotals($conn2, $kategoriList, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir, $cashflowFilter = '') {
    $kategoriIn = implode(',', array_map(function ($k) use ($conn2) {
        return "'" . mysqli_real_escape_string($conn2, $k) . "'";
    }, $kategoriList));

    $pcTotal = function ($pc) use ($conn2, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir, $cashflowFilter) {
        $pcEsc = mysqli_real_escape_string($conn2, $pc);
        $alias = strtolower($pcEsc);
        return "
            (select id_indirect, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_$alias
             from (select no_coa, id_indirect from mastercoa_v2) b
             inner join (select id, ind_name from tbl_master_cashflow $cashflowFilter) c on c.id = b.id_indirect
             left join (
                 select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr, sum(ROUND(credit * rate,2)) credit_idr
                 from tbl_list_journal
                 where tgl_journal BETWEEN
                     (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal')
                     and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
                     and profit_center = '$pcEsc'
                 group by no_coa
             ) a on b.no_coa = a.coa_no
             group by b.id_indirect)
        ";
    };

    $sql = "
        select id, sub_kategori, sub_kategori_eng,
               COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, COALESCE(total_all,0) total_all
        from (select id, ref, sub_kategori, sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ($kategoriIn)) a
        left join (
            select a.id id_indirect, a.ind_name,
                   COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak,
                   (COALESCE(total_nag,0) + COALESCE(total_nak,0)) total_all
            from (select id, ind_name from tbl_master_cashflow $cashflowFilter) a
            left join " . $pcTotal('NAG') . " b on b.id_indirect = a.id
            left join " . $pcTotal('NAK') . " c on c.id_indirect = a.id
        ) b on b.ind_name = a.sub_kategori
        order by id asc
    ";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Total 1 baris tunggal by id_indirect (mis. id=19 "Penyesuaian Akumulasi
// Penyusutan Aset Tetap") - baris ini di-hardcode by ID di YTD, bukan lewat
// fs_kategori_laporan, jadi dipisah dari fungsi kategori di atas.
function fsGetCashflowIndirectSingle($conn2, $idIndirect, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $idEsc = mysqli_real_escape_string($conn2, $idIndirect);
    $sql = "
        select id_indirect, total_nag, total_nak, (total_nag + total_nak) total_all
        from (
            select id_indirect,
                   ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nag,
                   ((sum(COALESCE(d.debit_idr,0))-sum(COALESCE(d.credit_idr,0))) * -1) total_nak
            from (select no_coa, id_indirect from mastercoa_v2) b
            inner join (select id from tbl_master_cashflow) c on c.id = b.id_indirect
            left join (
                select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr, sum(ROUND(credit * rate,2)) credit_idr
                from tbl_list_journal
                where tgl_journal BETWEEN
                    (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal')
                    and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
                    and profit_center = 'NAG'
                group by no_coa
            ) a on b.no_coa = a.coa_no
            left join (
                select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr, sum(ROUND(credit * rate,2)) credit_idr
                from tbl_list_journal
                where tgl_journal BETWEEN
                    (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal')
                    and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
                    and profit_center = 'NAK'
                group by no_coa
            ) d on b.no_coa = d.coa_no
            GROUP BY b.id_indirect
        ) a
        where a.id_indirect = '$idEsc'
    ";
    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    $nag = (float) ($row['total_nag'] ?? 0);
    $nak = (float) ($row['total_nak'] ?? 0);
    return ['total_nag' => $nag, 'total_nak' => $nak, 'total_all' => $nag + $nak];
}

// "Kas dan Setara Kas pada Awal Periode" - saldo AWAL TAHUN (snapshot kolom
// $kata_filter di fs_saldo_awal_tb, bukan hasil rollforward jurnal), akun
// kategori "Kas dan Setara Kas" (id_ctg4=111), dengan pengecualian: rekening
// bank utama 1.10.01/1.10.02 cuma dihitung kalau saldo awalnya POSITIF
// (saldo negatif/overdraft dianggap liabilitas, bukan kas - lihat query
// asli $sql5 di fs_ytd/cashflow_indirect.php). Nilainya TETAP/konstan di
// semua kolom bulan (tidak tergantung bulan_akhir) karena memang saldo di
// AWAL rentang filter, jadi cukup dipanggil SEKALI per request, bukan
// di-loop per periode.
function fsGetCashflowBeginningCash($conn2, $kata_filter) {
    $pcTotal = function ($pc) use ($conn2, $kata_filter) {
        $pcEsc = mysqli_real_escape_string($conn2, $pc);
        return "
            (select sum(saldo) total from (
                select no_coa nocoa, $kata_filter as saldo
                from fs_saldo_awal_tb
                where no_coa != '1.10.01' and no_coa != '1.10.02' and profit_center = '$pcEsc'
                UNION
                select no_coa nocoa, $kata_filter as saldo
                from fs_saldo_awal_tb
                where (no_coa = '1.10.01' and $kata_filter > 0 and profit_center = '$pcEsc')
                   OR (no_coa = '1.10.02' and $kata_filter > 0 and profit_center = '$pcEsc')
            ) saldo
            inner join mastercoa_v2 coa on coa.no_coa = saldo.nocoa
            where coa.id_ctg4 = '111')
        ";
    };
    $sql = "select " . $pcTotal('NAG') . " total_nag, " . $pcTotal('NAK') . " total_nak";
    $result = mysqli_query($conn2, $sql);
    $row = mysqli_fetch_assoc($result);
    $nag = (float) ($row['total_nag'] ?? 0);
    $nak = (float) ($row['total_nak'] ?? 0);
    return ['total_nag' => $nag, 'total_nak' => $nak, 'total_all' => $nag + $nak];
}

// "Laba (Rugi) Bersih" - di versi YTD (fs_ytd/cashflow_indirect.php) nilai
// ini TIDAK dihitung sendiri, cuma "nebeng" variabel PHP global yang di-set
// fs_ytd/statement_profit_loss.php (jalan cuma karena urutan include di
// financial_statement_ytd.php - rapuh). Di sini dihitung MANDIRI, pakai
// kategori & formula credit-normal yang PERSIS SAMA dengan running total
// terakhir statement_profit_loss_monthly.php ("LABA / (RUGI) BERSIH") -
// supaya angkanya selalu konsisten dengan tab SPL Monthly tanpa duplikat
// definisi kategori (cukup daftar nama kategori di sini, hitungnya tetap
// lewat fsGetCategoryTotals() yang sama).
function fsMonthlyNetIncomeByPeriod($conn2, $periods, $pcCols, $bulan_awal, $tahun_awal, $kata_filter) {
    $kategoriList = [
        'PENJUALAN KOTOR', 'RETURN PENJUALAN', 'POTONGAN PENJUALAN',
        'BEBAN POKOK PENJUALAN', 'BEBAN LAINNYA', 'BEBAN BUNGA', 'BEBAN PAJAK',
    ];
    $result = []; // [period_label][pcCol] => float
    foreach ($periods as $p) {
        foreach ($pcCols as $pcCol) {
            $result[$p['label']][$pcCol] = 0.0;
        }
        foreach ($kategoriList as $kategori) {
            $rows = fsGetCategoryTotals($conn2, [$kategori], $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun'], $kata_filter, true);
            foreach ($rows as $row) {
                foreach ($pcCols as $pcCol) {
                    $result[$p['label']][$pcCol] += (float) ($row['total_' . strtolower($pcCol)] ?? 0);
                }
            }
        }
    }
    return $result;
}

// Format 1 angka - negatif ditulis dalam kurung (warna sama seperti angka
// biasa, hitam), biar gampang kelihatan mana yang minus tanpa harus baca
// tanda minus-nya.
function fsMonthlyFormatNumber($val) {
    $val = (float) $val;
    if ($val < 0) {
        return '(' . number_format(abs($val), 2) . ')';
    }
    return number_format($val, 2);
}

// Format 1 nilai persentase - kalau bilangannya BULAT (tidak ada pecahan
// sama sekali setelah dibulatkan 2 desimal) ditulis TANPA desimal (mis.
// "100%" bukan "100.00%"), selain itu tetap 2 desimal seperti biasa. Tidak
// pakai kurung buat negatif (beda dari fsMonthlyFormatNumber() - kolom %
// ikut gaya asli fs_ytd/statement_profit_loss.php, cukup tanda minus biasa).
function fsMonthlyFormatPercent($val) {
    $val = (float) $val;
    $rounded = round($val, 2);
    if ($rounded == round($rounded)) {
        return number_format($rounded, 0);
    }
    return number_format($rounded, 2);
}

// Render <td> angka (NAG/NAK/ALL sesuai $pcCols) dari 1 baris hasil
// fsGetCategoryTotals()/query sejenis - negatif ditulis merah dalam kurung,
// sama seperti gaya fs_ytd/*.php (cuma ditambah warna).
function fsMonthlyRenderValueCells($row, $pcCols) {
    $vals = [
        'NAG' => (float) ($row['total_nag'] ?? 0),
        'NAK' => (float) ($row['total_nak'] ?? 0),
        'ALL' => (float) ($row['total_all'] ?? 0),
    ];
    $html = '';
    foreach ($pcCols as $pcCol) {
        $v = $vals[$pcCol] ?? 0;
        $html .= '<td style="text-align:right;">' . fsMonthlyFormatNumber($v) . '</td>';
    }
    return $html;
}

// ===== Helper tabel "freeze header + kolom YTD" =====
// Awalnya ditulis khusus di statement_financial_position_monthly.php, tapi
// isinya generik (tidak spesifik SFP sama sekali - cuma soal layout kolom
// bulan + grup YTD), jadi dipindah ke sini supaya SPL/CF Direct/CF Indirect
// Monthly bisa pakai tanpa duplikat/collision nama fungsi (semua tab di-include
// dalam 1 request PHP yang sama, jadi nama fungsi harus unik lintas file).
// Prefix "sfpm" dipertahankan apa adanya (bukan diganti jadi generik) supaya
// tidak perlu ubah call site di file yang sudah teruji jalan.

// Semua kolom (NAG, NAK, Total) dalam 1 bulan yang sama dikasih class ganjil
// atau genap yang sama - dipakai buat tint warna latar selang-seling per
// bulan (lihat CSS .sfpm-month-a/.sfpm-month-b). $idx = urutan kolom nilai ke
// berapa (0-based, dihitung dari kiri lintas semua bulan). Kolom pertama tiap
// bulan (idx % groupSize === 0) juga dikasih garis vertikal tipis
// (.sfpm-month-start) - tint warna doang ternyata kurang kelihatan buat
// nandain batas antar bulan waktu kolomnya banyak (>15 kolom angka),
// garisnya bikin mata lebih gampang "mengunci" ke grup bulan yang sama pas
// baca ke bawah.
// Pakai $GLOBALS['sfpmYtdStartIdx'] (bukan parameter tambahan) supaya semua
// call site yang sudah ada (sfpmRenderRowValues, sfpmBlankCells, loop
// header) tidak perlu diubah - begitu $idx masuk rentang grup YTD di ujung
// kanan (masing-masing halaman set sendiri sebelum render tabelnya), class
// .sfpm-ytd ditambahkan buat kasih highlight warna beda dari tint bulan biasa.
function sfpmColClass($idx, $groupSize) {
    $monthIdx = intdiv($idx, $groupSize);
    $cls = $monthIdx % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b';
    if ($idx % $groupSize === 0) {
        $cls .= ' sfpm-month-start';
    }
    if (isset($GLOBALS['sfpmYtdStartIdx']) && $idx >= $GLOBALS['sfpmYtdStartIdx']) {
        $cls .= ' sfpm-ytd';
    }
    return $cls;
}

// Lebar kolom DIKUNCI lewat <colgroup> yang sama persis di tabel header
// maupun body (lihat catatan table-layout:fixed di CSS masing-masing tab) -
// dipanggil 2x dengan parameter identik, bukan cuma sekali/di-share, karena
// masing-masing <table> butuh <colgroup>-nya sendiri (tidak bisa dipakai
// bareng lintas elemen table berbeda).
// $descW default 440px - lebar ini dipilih dari label sub_kategori/kategori
// TERPANJANG yang benar-benar ada di data (dicek langsung via query, bukan
// nebak) - mis. "Harga Pokok Penjualan Jasa Jahit Pakaian Jadi Ekspor" (52
// karakter, fs_kategori_laporan) dan "ARUS KAS YANG DIPEROLEH DARI
// AKTIVITAS PENDANAAN" (49 karakter, hardcode CF Direct/Indirect Monthly).
// Sebelumnya 260px - kepotong jadi "..." (text-overflow:ellipsis) buat
// label-label ini.
function sfpmColgroup($valueColCount, $descW = 440, $valW = 155) {
    $html = '<colgroup><col style="width:' . $descW . 'px">';
    for ($i = 0; $i < $valueColCount; $i++) {
        $html .= '<col style="width:' . $valW . 'px">';
    }
    $html .= '</colgroup>';
    return $html;
}

// Lebar total tabel (dipakai sebagai style="width:...px" eksplisit di TAG
// <table> header maupun body, bukan cuma dibiarkan "auto"). Terbukti lewat
// pengujian nyata (headless browser): dengan width:auto, table-layout:fixed
// TIDAK menjamin dua elemen <table> terpisah dengan <colgroup> identik akan
// dirender dengan lebar akhir yang sama persis - lebar minimum konten sel
// (angka panjang tidak boleh wrap di tabel body, teks pendek di tabel
// header) ikut memengaruhi kalkulasi auto-width, dan hasilnya bisa
// menyimpang jauh dari sekadar jumlah <colgroup>. Kalau tabel header jadi
// TIDAK actually overflow (lebih sempit dari wrapper-nya), scrollLeft yang
// di-set lewat JS jadi no-op (browser otomatis clamp balik ke 0 karena
// tidak ada yang bisa discroll) - itu akar masalah header yang
// "ketinggalan" pas tabel body discroll. width eksplisit di sini
// menghapus ambiguitas itu - lebar akhir MURNI dari angka PHP ini, tidak
// diserahkan ke algoritma auto-width browser sama sekali.
function sfpmTableWidth($valueColCount, $descW = 440, $valW = 155) {
    return $descW + $valueColCount * $valW;
}

// Render 1 baris nilai, urutan periode lalu pcCol di dalamnya (NAG, NAK,
// Total per bulan) dari lookup [period_label][pcCol] => float, ditutup
// dengan 1 grup kolom YTD di ujung kanan (duplikat nilai bulan TERAKHIR
// dari filter, bukan periode baru - tiap kolom bulanan di laporan-laporan
// ini MEMANG SUDAH kumulatif YTD dari Januari, jadi nilai bulan terakhir =
// YTD sampai akhir filter).
function sfpmRenderRowValues($valuesByPeriod, $periods, $pcCols, $tag, $cellClass) {
    $html = '';
    $idx = 0;
    foreach ($periods as $p) {
        foreach ($pcCols as $pcCol) {
            $v = $valuesByPeriod[$p['label']][$pcCol] ?? 0;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, count($pcCols)) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
            $idx++;
        }
    }
    $lastPeriod = end($periods);
    if ($lastPeriod) {
        foreach ($pcCols as $pcCol) {
            $v = $valuesByPeriod[$lastPeriod['label']][$pcCol] ?? 0;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, count($pcCols)) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
            $idx++;
        }
    }
    return $html;
}

// Baris kosong (blank filler) sejumlah $valueColCount - tetap dikasih class
// sfpm-month-a/b (lihat sfpmColClass()) biar tint per bulan tetap nyambung
// waktu lewatin baris spacer/section yang kosong.
function sfpmBlankCells($valueColCount, $pcColCount, $tag = 'th') {
    $html = '';
    for ($idx = 0; $idx < $valueColCount; $idx++) {
        $html .= '<' . $tag . ' class="' . trim(sfpmColClass($idx, $pcColCount)) . '"></' . $tag . '>';
    }
    return $html;
}

// Baris spacer/kosong di antara section - jumlah <th> kosongnya ikut jumlah
// kolom nilai (dulu di YTD cuma 1/3 sesuai profit_center, sekarang sejumlah
// periode x pcCols).
function sfpmSpacerRow($valueColCount, $pcColCount, $rowClass = 'spacer') {
    echo '<tr class="' . $rowClass . '"><th class="sfpm-freeze-left"></th>' . sfpmBlankCells($valueColCount, $pcColCount) . '</tr>';
}

// ===== Helper kolom persentase (khusus SPL Monthly) =====
// fs_ytd/statement_profit_loss.php kasih 2 kolom per profit-center di
// SETIAP baris (jumlah Rupiah + % dari PENJUALAN BERSIH/Net Sales, base
// yang SAMA dipakai di sepanjang laporan, bukan base yang berubah per
// section) - kolom % lebih sempit dari kolom jumlah (lihat .isi-persentage
// width:50px vs .isi-periode width:180px di file itu) dan TIDAK ada baris
// header terpisah "Jumlah"/"%" (cukup suffix "%" di angkanya, header
// profit-center cukup colspan=2). Ketiga fungsi di bawah adalah versi
// "kolom dobel" dari sfpmColgroup()/sfpmTableWidth()/sfpmRenderRowValues() -
// dipanggil gantian (bukan bareng) tergantung tab butuh kolom % atau tidak.
// groupSize buat sfpmColClass() (tint bulan) otomatis $pcColCount*2 karena
// tiap pcCol sekarang 2 kolom fisik - value SUDAH URUT idx 0..N di semua
// fungsi ini jadi tetap konsisten dgn sfpmBlankCells()/sfpmSpacerRow() ASAL
// dipanggil dengan $pcColCount yang SUDAH dikali 2 juga (lihat pemakaian di
// statement_profit_loss_monthly.php).
function sfpmColgroupWithPercent($periodCount, $pcColCount, $descW = 440, $valW = 135, $pctW = 65) {
    $html = '<colgroup><col style="width:' . $descW . 'px">';
    for ($m = 0; $m < $periodCount + 1; $m++) { // +1 = grup YTD di ujung kanan
        for ($i = 0; $i < $pcColCount; $i++) {
            $html .= '<col style="width:' . $valW . 'px"><col style="width:' . $pctW . 'px">';
        }
    }
    $html .= '</colgroup>';
    return $html;
}

function sfpmTableWidthWithPercent($periodCount, $pcColCount, $descW = 440, $valW = 135, $pctW = 65) {
    return $descW + ($periodCount + 1) * $pcColCount * ($valW + $pctW);
}

// $percentByPeriod = [period_label][pcCol] => float (sudah dalam skala 0-100,
// BUKAN 0-1). Negatif ditulis pakai tanda minus biasa (BUKAN kurung) persis
// gaya kolom % di fs_ytd/statement_profit_loss.php - beda dari kolom jumlah
// yang pakai kurung (fsMonthlyFormatNumber()).
function sfpmRenderRowValuesWithPercent($valuesByPeriod, $percentByPeriod, $periods, $pcCols, $tag, $cellClass) {
    $html = '';
    $idx = 0;
    $groupSize = count($pcCols) * 2;
    $renderPair = function ($periodLabel) use (&$html, &$idx, $valuesByPeriod, $percentByPeriod, $pcCols, $tag, $cellClass, $groupSize) {
        foreach ($pcCols as $pcCol) {
            $v = $valuesByPeriod[$periodLabel][$pcCol] ?? 0;
            $pct = $percentByPeriod[$periodLabel][$pcCol] ?? 0;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, $groupSize) . '">' . fsMonthlyFormatNumber($v) . '</' . $tag . '>';
            $idx++;
            $html .= '<' . $tag . ' class="' . $cellClass . sfpmColClass($idx, $groupSize) . '">' . fsMonthlyFormatPercent($pct) . '%</' . $tag . '>';
            $idx++;
        }
    };
    foreach ($periods as $p) {
        $renderPair($p['label']);
    }
    $lastPeriod = end($periods);
    if ($lastPeriod) {
        $renderPair($lastPeriod['label']);
    }
    return $html;
}

// ===== Helper khusus Cash Flow Direct =====
// Beda mesin lagi dari CF Indirect: kategorisasi lewat tb_master_pilihan
// (bukan fs_kategori_laporan) yang di-JOIN ke tbl_master_cashflow via
// mastercoa_v2.id_direct_credit/id_direct_debit (bukan id_indirect), dan
// nilainya pakai kolom `rate` (bukan debit_idr/credit_idr). Section
// "Arus Kas dari Aktivitas Operasi" & bagian Pinjaman di "Pendanaan" juga
// butuh deteksi "pinjaman" (overdraft crossing per bulan, CTE `calc`) yang
// disalin apa adanya dari fs_ytd/cashflow_direct.php - lihat komentar di
// masing-masing fungsi. SEMUA pola SQL di bawah disalin/diverifikasi
// LANGSUNG dari fs_ytd/cashflow_direct.php baris ~453-1491, dengan 3
// perbaikan yang sudah disetujui:
// 1. Typo 'NK' -> 'NAK' di accounts CTE section Pendanaan (baris 1041 versi
//    YTD) - cuma salah label kolom profit_center, bikin baris NAK/1.10.01
//    bulan Juli gagal ke-join ke journal_sums (WHERE profit_center='NAK'
//    tidak pernah ketemu 'NK'). Di sini accounts CTE dibangun via helper PHP
//    (_fsCfDirectAccountsCte), bukan ditulis tangan per bulan, jadi typo ini
//    otomatis tidak ada.
// 2. "Penerimaan Pinjaman" di versi YTD SELALU dihitung 1 tahun penuh
//    (Jan-Des) berapa pun bulan_akhir yang dipilih, sedangkan "Pembayaran
//    Pinjaman" sudah benar ikut rentang YTD asli. Di sini KEDUANYA
//    diperlakukan sama (ikut rentang bulan_awal..bulan_akhir yang benar).
// 3. Baris total "ARUS KAS YANG DIPEROLEH DARI AKTIVITAS PENDANAAN" di versi
//    YTD cuma menjumlahkan baris Pinjaman (Penerimaan+Pembayaran), padahal
//    baris "antar divisi" yang ditampilkan tepat di atasnya JUGA ikut
//    dijumlah ke "Kenaikan/(Penurunan) Bersih Kas" - jadi subtotal yang
//    dicetak tidak sama dengan yang benar-benar dipakai. Di sini subtotal
//    yang ditampilkan = jumlah SEMUA baris di atasnya (Pinjaman + antar
//    divisi), konsisten dengan grand total.

// Tanggal awal/akhir 1 bulan (dari tbl_tgl_tb) - dipakai buat filter
// "periode BETWEEN ..." final, persis $tanggal_awal/$tanggal_akhir di YTD.
function _fsCfDirectTanggal($conn2, $bulan, $tahun, $kolom) {
    $bulanEsc = mysqli_real_escape_string($conn2, $bulan);
    $tahunEsc = mysqli_real_escape_string($conn2, $tahun);
    $result = mysqli_query($conn2, "select $kolom from tbl_tgl_tb where bulan = '$bulanEsc' and tahun = '$tahunEsc'");
    $row = mysqli_fetch_assoc($result);
    $val = $row[$kolom] ?? null;
    return $val ? date('Y-m-d', strtotime($val)) : date('Y-m-d');
}

// 12 baris "SELECT '$tahun-01' periode UNION ALL ..." - dipakai di beberapa
// CROSS JOIN kalender bulanan (pembayaran/other_value CTE).
function _fsCfDirectMonthUnion($tahun_awal) {
    $parts = [];
    foreach (['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $m) {
        $parts[] = "SELECT '$tahun_awal-$m' periode";
    }
    return implode(' UNION ALL ', $parts);
}

// CTE "accounts" - saldo awal per bulan (snapshot fs_saldo_awal_tb) untuk
// satu daftar akun kas/bank (profit_center, no_coa, akun kontra). Dibangun
// via loop PHP (bukan ditulis tangan 12x blok UNION ALL seperti versi YTD)
// supaya tidak ada risiko salah ketik/typo transkripsi (lihat perbaikan #1
// di atas) - hasil SQL-nya PERSIS SAMA strukturnya dengan versi YTD.
// $accounts = [[profit_center, no_coa, akun_kontra], ...].
function _fsCfDirectAccountsCte($tahun_awal, $accounts) {
    $months = ['01' => 'jan', '02' => 'feb', '03' => 'mar', '04' => 'apr', '05' => 'may', '06' => 'jun', '07' => 'jul', '08' => 'aug', '09' => 'sep', '10' => 'oct', '11' => 'nov', '12' => 'dec'];
    $contraMap = ['1.10.02' => '2.20.02', '1.10.01' => '2.20.01'];
    $blocks = [];
    foreach ($months as $mm => $col) {
        foreach ($accounts as $acc) {
            list($pc, $coa, $akun) = $acc;
            $contra = $contraMap[$coa] ?? null;
            $inClause = $contra ? "'$coa','$contra'" : "'$coa'";
            $blocks[] = "SELECT '$pc' AS profit_center, '$coa' AS no_coa, '$akun' AS akun, '$tahun_awal-$mm' periode, s.{$col}_{$tahun_awal} FROM fs_saldo_awal_tb s WHERE s.no_coa IN ($inClause) AND s.profit_center = '$pc'";
        }
    }
    return "accounts AS (
        SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
        FROM (" . implode(" UNION ALL ", $blocks) . ") AS a
        GROUP BY profit_center, no_coa, akun, periode
    )";
}

// CTE chain "journal_sums -> reval -> base -> calc -> agg" - disalin apa
// adanya dari versi YTD (dipakai identik di section Operasi maupun bagian
// Pinjaman-nya Pendanaan, cuma daftar akun di CTE accounts yang beda).
// `calc` yang mendeteksi penerimaan/pembayaran pinjaman lewat overdraft
// crossing (saldo_awal/saldo_akhir tanda beda) per bulan per akun.
function _fsCfDirectBaseCalcAggCte($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
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

// Filter jurnal kas/bank yang dipakai di Investasi/Pendanaan (beda dikit
// dari filter journal_sums di atas - lebih spesifik pakai '/' & tambahan
// kode RCO/RCI/KKK/KKM), disalin apa adanya dari versi YTD.
function _fsCfDirectJournalFilter() {
    return "(no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%')";
}

// "Arus Kas dari Aktivitas Operasi" - hasil rollup per sub_kategori
// (tb_master_pilihan) dari CTE chain accounts->...->agg (utk revaluasi bank
// utama) digabung dgn CTE "pembayaran" (transaksi kas/bank biasa per
// sub_kategori x bulan, lewat id_direct_credit/id_direct_debit) - disalin
// apa adanya dari $sql (baris ~453-792 versi YTD).
function fsGetCashflowDirectOperasi($conn2, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $accounts = [
        ['NAG', '1.10.02', '008-998-1982'],
        ['NAG', '1.10.01', '008-997-1979'],
        ['NAK', '1.10.02', '008-998-1982'],
        ['NAK', '1.10.01', '008-997-1979'],
        ['NAK', '1.10.41', '008-759-5858'],
        ['NAK', '1.10.42', '008-751-5757'],
    ];
    $tglAwal = _fsCfDirectTanggal($conn2, $bulan_awal, $tahun_awal, 'tgl_awal');
    $tglAkhir = _fsCfDirectTanggal($conn2, $bulan_akhir, $tahun_akhir, 'tgl_akhir');
    $monthUnion = _fsCfDirectMonthUnion($tahun_awal);
    $journalFilter = _fsCfDirectJournalFilter();
    $dateFilter = "tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')";

    $sql = "WITH
    " . _fsCfDirectAccountsCte($tahun_awal, $accounts) . ",
    " . _fsCfDirectBaseCalcAggCte($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) . ",
    revaluasi as (select '2' id, periode, if(profit_center = 'NAG',sum(debit_revaluasi - credit_revaluasi),0) revaluasi_nag, if(profit_center = 'NAK',sum(debit_revaluasi - credit_revaluasi),0) revaluasi_nak, sum(debit_revaluasi - credit_revaluasi) revaluasi_all from agg GROUP BY periode),
    pembayaran as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, p.periode AS periode, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a
    CROSS JOIN ($monthUnion) p
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
    LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, p.periode) a ORDER BY periode, a.id ASC),
    hasil as (select a.id, a.periode, sub_kategori, sub_kategori_eng, (COALESCE(total_nag,0) + COALESCE(revaluasi_nag,0)) total_nag, (COALESCE(total_nak,0) + COALESCE(revaluasi_nak,0)) total_nak, (COALESCE(total_all,0) + COALESCE(revaluasi_all,0)) total_all from pembayaran a LEFT JOIN revaluasi b on b.id = a.id and b.periode = a.periode order by periode, a.id asc)
    select id, sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from hasil WHERE periode BETWEEN DATE_FORMAT('$tglAwal','%Y-%m') AND DATE_FORMAT('$tglAkhir','%Y-%m') group by id ORDER BY id asc";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// "Arus Kas dari Aktivitas Investasi" (dan bagian "antar divisi" di
// Pendanaan, yang bentuk query-nya identik cuma type_pilihan & filter id
// beda) - query flat tanpa CTE, disalin apa adanya dari versi YTD (baris
// ~864 utk Investasi, ~1327 utk Pendanaan antar divisi).
function fsGetCashflowDirectFlat($conn2, $typePilihan, $extraFilter, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $typeEsc = mysqli_real_escape_string($conn2, $typePilihan);
    $journalFilter = _fsCfDirectJournalFilter();
    $dateFilter = "tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')";

    $sql = "select sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = '$typeEsc'$extraFilter) a
    LEFT JOIN (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
    LEFT JOIN (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
    LEFT JOIN (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
    LEFT JOIN (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a ORDER BY a.id ASC";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Sub-query "other_value"/"other_value_bayar" versi YTD (entri manual, BUKAN
// lewat akun kas/bank utama - no_coa NOT IN 1.10.02/1.10.01) per bulan,
// untuk 1 id tb_master_pilihan tertentu (14="Penerimaan Pinjaman" atau
// 15="Pembayaran Pinjaman"). Beda dari versi YTD: group by periode dari CROSS
// JOIN kalender (p.periode) bukan COALESCE(b.periode,...,e.periode) - efeknya
// SAMA utk bulan yang ada datanya (angkanya identik), cuma versi ini juga
// tetap punya 1 baris per bulan walau tidak ada transaksi (COALESCE ke 0),
// bukan collapse ke 1 baris periode=NULL yang tidak pernah dipakai lagi
// downstream - jadi PHP-nya bisa jumlah per-periode dengan aman.
function _fsCfDirectOtherValueCte($idVal, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $monthUnion = _fsCfDirectMonthUnion($tahun_awal);
    $journalFilter = _fsCfDirectJournalFilter();
    $dateFilter = "tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')";
    return "
        (select p.periode periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak
         from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan' and id = '$idVal') a
         CROSS JOIN ($monthUnion) p
         LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
         LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
         LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
         LEFT JOIN (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where $dateFilter AND $journalFilter and a.no_coa not in ('1.10.02','1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode
         GROUP BY p.periode)
    ";
}

// Kumpulkan baris-baris [period_label => rows[{sub_kategori,...}]] (hasil
// panggilan berulang salah satu fungsi fsGetCashflowDirect*/
// fsGetCashflowIndirectCategoryTotals per bulan) jadi 3 struktur siap-render:
// subList (nama sub_kategori yang muncul di bulan manapun, biar barisnya
// konsisten walau nilainya 0 di sebagian bulan), itemValuesBySub
// ([sub_kategori][period_label][pcCol] => float), sectionTotal
// ([period_label][pcCol] => float, jumlah semua sub_kategori). Dipakai di CF
// Direct Monthly (4x - Operasi/Investasi/Pendanaan-pinjaman/Pendanaan-antar
// divisi) supaya tidak duplikat loop yang sama 4x.
function fsMonthlyAggregateSubKategori($monthDataByPeriod, $periods, $pcCols) {
    $subList = [];
    foreach ($periods as $p) {
        foreach ($monthDataByPeriod[$p['label']] as $row) {
            if (!isset($subList[$row['sub_kategori']])) {
                $subList[$row['sub_kategori']] = $row['sub_kategori_eng'] ?? '';
            }
        }
    }

    $itemValuesBySub = [];
    $sectionTotal = [];
    foreach ($subList as $subKategori => $subKategoriEn) {
        foreach ($periods as $p) {
            $row = null;
            foreach ($monthDataByPeriod[$p['label']] as $r) {
                if ($r['sub_kategori'] === $subKategori) {
                    $row = $r;
                    break;
                }
            }
            $row = $row ?? ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
            foreach ($pcCols as $pcCol) {
                $v = (float) ($row['total_' . strtolower($pcCol)] ?? 0);
                $itemValuesBySub[$subKategori][$p['label']][$pcCol] = $v;
                $sectionTotal[$p['label']][$pcCol] = ($sectionTotal[$p['label']][$pcCol] ?? 0) + $v;
            }
        }
    }

    return ['subList' => $subList, 'itemValuesBySub' => $itemValuesBySub, 'sectionTotal' => $sectionTotal];
}

// "Penerimaan Pinjaman" & "Pembayaran Pinjaman" (bagian dari Pendanaan) -
// gabungan CTE accounts/calc/agg (deteksi otomatis lewat overdraft crossing
// akun kas/bank utama) + other_value manual (id 14/15). Beda dari versi YTD:
// pivot dihitung SEKALI (bukan pivot+pivot_bayar duplikat - isinya identik di
// versi YTD, cuma beda label id yg tidak dipakai lagi di sini) dan KEDUA
// baris (Penerimaan maupun Pembayaran) di-filter rentang YTD yang SAMA
// (perbaikan #2 - lihat catatan di atas fungsi ini).
function fsGetCashflowDirectLoans($conn2, $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
    $accounts = [
        ['NAG', '1.10.02', '008-998-1982'],
        ['NAG', '1.10.01', '008-997-1979'],
        ['NAK', '1.10.02', '008-998-1982'],
        ['NAK', '1.10.01', '008-997-1979'],
    ];
    $tglAwal = _fsCfDirectTanggal($conn2, $bulan_awal, $tahun_awal, 'tgl_awal');
    $tglAkhir = _fsCfDirectTanggal($conn2, $bulan_akhir, $tahun_akhir, 'tgl_akhir');
    $otherTerima = _fsCfDirectOtherValueCte('14', $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);
    $otherBayar = _fsCfDirectOtherValueCte('15', $bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);

    $sql = "WITH
    " . _fsCfDirectAccountsCte($tahun_awal, $accounts) . ",
    " . _fsCfDirectBaseCalcAggCte($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) . ",
    pivot AS (
      SELECT periode,
        SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
        SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
        SUM(CASE WHEN profit_center='NAG' THEN pembayaran_pinjaman ELSE 0 END) AS pembayaran_NAG,
        SUM(CASE WHEN profit_center='NAK' THEN pembayaran_pinjaman ELSE 0 END) AS pembayaran_NAK
      FROM agg GROUP BY periode
    ),
    other_terima AS $otherTerima,
    other_bayar AS $otherBayar,
    final AS (
      SELECT p.periode periode, 'Penerimaan Pinjaman' sub_kategori, 'Proceeds from loans' sub_kategori_eng,
             (p.penerimaan_NAG + COALESCE(ot.total_nag,0)) total_nag,
             (p.penerimaan_NAK + COALESCE(ot.total_nak,0)) total_nak
      FROM pivot p LEFT JOIN other_terima ot ON ot.periode = p.periode
      UNION ALL
      SELECT p.periode periode, 'Pembayaran Pinjaman' sub_kategori, 'Payment of loans' sub_kategori_eng,
             -(p.pembayaran_NAG - COALESCE(ob.total_nag,0)) total_nag,
             -(p.pembayaran_NAK - COALESCE(ob.total_nak,0)) total_nak
      FROM pivot p LEFT JOIN other_bayar ob ON ob.periode = p.periode
    )
    SELECT sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_nag + total_nak) total_all
    FROM final
    WHERE periode BETWEEN DATE_FORMAT('$tglAwal','%Y-%m') AND DATE_FORMAT('$tglAkhir','%Y-%m')
    GROUP BY sub_kategori, sub_kategori_eng
    ORDER BY sub_kategori DESC";

    $result = mysqli_query($conn2, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
