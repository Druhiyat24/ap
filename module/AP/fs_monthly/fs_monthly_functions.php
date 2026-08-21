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
