<?php
// ============================================================================
// ppn_masukan_query.php — SATU sumber query report SUB LEDGER PPN MASUKAN.
//
// Dipakai bareng oleh tampilan layar (ajx_ppn_masukan.php) dan ekspor Excel
// (ekspor_ppn_masukan.php), supaya angka di layar dan di file TIDAK MUNGKIN
// berbeda. Sebelumnya query-nya hanya ada di endpoint layar; kalau ekspor
// menyalin ulang, dua-duanya pasti melenceng cepat atau lambat.
//
// ppn_report_sql() mengembalikan STRING SQL; pemanggilnya yang menjalankan dan
// memformat hasilnya (layar butuh tautan HTML, Excel butuh teks polos).
// ============================================================================
if (!function_exists('ppn_report_sql')) {
function ppn_report_sql($conn2, $nama_supp, $start_date, $end_date, $PPN_MIN_DATE) {
    // ===========================================================================
    // REPORT MUTASI PPN MASUKAN (COA 1.52.04).
    //
    // RUANG LINGKUP DOKUMEN — hanya dua yang dipakai (sesuai permintaan user):
    //   ADDITION  : SI/APR/% (Invoice Received) | BK/% (Bank Out) |
    //               MEMO/% (Memo) | PV-AP/% (Payment Voucher) | KKK/% (Kas Kecil)
    //   Tiga kelompok berikut sama-sama memorial journal ber-NOMOR FAKTUR; yang
    //   membedakan cuma TYPE jurnalnya:
    //   DEDUCTION        : %GM/% + faktur_pajak TERISI + type = 'VAT'
    //                      (dari tab PPN Masukan — Type-nya dikunci ke VAT)
    //   RECLASSIFICATION : %GM/% + faktur_pajak TERISI + type = 'OTHERS'
    //   ADJUSTMENT       : %GM/% + faktur_pajak TERISI + type = 'AUDIT ADJUSTMENT'
    // Dokumen 1.52.04 di luar dua kelompok itu (mis. GM reklas tanpa faktur, BM/)
    // SENGAJA TIDAK IKUT sama sekali — tidak di mutasi, tidak di saldo — supaya
    // tiap baris tutup buku PERSIS: Beginning + Addition - Deduction = Ending.
    // Kalau nanti aturan Reclassification / Adjustment sudah ditentukan, tinggal
    // longgarkan filter `and (...)` di CTE `mut` lalu tambah bucket-nya.
    //
    // ATURAN POKOK: ini report MUTASI, jadi
    //     Ending Balance periode ini  ==  Beginning Balance periode berikutnya.
    // Supaya DIJAMIN (bukan kebetulan), Beginning & Ending TIDAK dihitung dari
    // penjumlahan kolom mutasi, tapi langsung dari SALDO KUMULATIF jurnal:
    //     Beginning = SALDO AWAL upload (tbl_ppn_saldo_awal, status Post)
    //               + SUM(debit-credit) baris dlm lingkup, $PPN_MIN_DATE <= tgl < From
    //     Ending    = SUM(debit-credit) baris dlm lingkup, $PPN_MIN_DATE <= tgl <= To
    // Jurnal SEBELUM $PPN_MIN_DATE (2026-01-01) sengaja TIDAK dihitung: saldo lama
    // harus dimasukkan lewat menu UPLOAD SALDO AWAL (belum dibuat). Jadi kalau From =
    // 01-Jan-2026, Beginning dari jurnal = 0.
    // Karena To bulan ini + 1 hari = From bulan depan, kedua angka itu membaca
    // himpunan baris yang PERSIS sama -> nyambung otomatis. Item yang sudah lunas
    // (Ending 0) otomatis hilang dari periode berikutnya.
    //
    // BUCKET:
    //   BEG = tgl_journal < From (apa pun dokumennya, asal masuk lingkup)
    //   DED = %GM/% & faktur terisi & type = 'VAT'
    //   RCL = %GM/% & faktur terisi & type = 'OTHERS'
    //   ADJ = %GM/% & faktur terisi & type = 'AUDIT ADJUSTMENT'
    //   ADD = sisanya (SI/APR, BK, MEMO, PV-AP, KKK)
    //
    // KUNCI BARIS (k): satu "item" yang saldonya diikuti antar periode.
    //   - punya faktur pajak  -> NOMOR FAKTUR PAJAK saja. Itu satu-satunya penghubung
    //     addition <-> deduction; profit center SENGAJA tidak ikut kunci karena GM
    //     hasil upload memakai PC dari dropdown header yang bisa beda dgn PC invoice
    //     (mis. faktur 04002600214931606: SI/APR = NAK, GM = NAG -> tetap 1 baris).
    //   - tidak punya faktur  -> no_journal (per dokumen)
    // Kolom SI No / SI Date / Supplier / Profit Center diambil dari dokumen ASAL
    // (bukan dokumen GM deduction).
    //
    // Kolom `supplier` sudah ada di tbl_list_journal (diisi via trigger), jadi TIDAK
    // perlu JOIN ke kontrabon_h / b_bankout_h / memo_h / mastersupplier lagi.
    // ===========================================================================
    $dFrom = mysqli_real_escape_string($conn2, $start_date);
    $dTo   = mysqli_real_escape_string($conn2, $end_date);
    $dMin  = mysqli_real_escape_string($conn2, $PPN_MIN_DATE);

    // group_concat dipakai utk ambil dokumen pertama & daftar doc deduction.
    mysqli_query($conn2, "SET SESSION group_concat_max_len = 1000000");

    $sql = "WITH
    mut as (
        /* baris 1.52.04 DALAM LINGKUP, s/d tanggal To, sudah diberi KUNCI + BUCKET */
        select
            case when coalesce(faktur_pajak, '') <> '' then concat('FP|', faktur_pajak)
                 else concat('JR|', no_journal) end k,
            id, no_journal, tgl_journal,
            coalesce(supplier, '') nama_supp,
            coalesce(faktur_pajak, '') faktur_pajak,
            coalesce(profit_center, '') profit_center,
            curr, rate,
            (debit - credit) ocy,
            (debit - credit) * rate idr,
            case
                when tgl_journal < '$dFrom' then 'BEG'
                when no_journal like '%GM/%' and coalesce(faktur_pajak, '') <> ''
                     and type_journal = 'VAT' then 'DED'
                when no_journal like '%GM/%' and coalesce(faktur_pajak, '') <> ''
                     and type_journal = 'OTHERS' then 'RCL'
                when no_journal like '%GM/%' and coalesce(faktur_pajak, '') <> ''
                     and type_journal = 'AUDIT ADJUSTMENT' then 'ADJ'
                else 'ADD'
            end bucket
        from tbl_list_journal
        where no_coa = '1.52.04'
          /* Lantai periode: jurnal sebelum $PPN_MIN_DATE TIDAK dipakai sama sekali.
             Saldo sebelum tanggal itu harus masuk lewat menu UPLOAD SALDO AWAL. */
          and tgl_journal >= '$dMin'
          and tgl_journal <= '$dTo'
          and (no_journal like 'SI/APR/%' or no_journal like 'BK/%'
            or no_journal like 'MEMO/%'   or no_journal like 'PV-AP/%'
            or no_journal like 'KKK/%'
            or (no_journal like '%GM/%' and coalesce(faktur_pajak, '') <> ''
                and type_journal in ('VAT', 'OTHERS', 'AUDIT ADJUSTMENT')))
        union all
        /* SALDO AWAL hasil upload (menu Set Opening Balance). SELALU masuk bucket BEG,
           walau tanggalnya sama dgn From, karena ini POSISI awal — bukan mutasi periode.
           Inilah pengganti jurnal sebelum $PPN_MIN_DATE yang sengaja tidak dibaca. */
        select
            case when coalesce(faktur_pajak, '') <> '' then concat('FP|', faktur_pajak)
                 else concat('JR|', si_no) end k,
            0 id, si_no no_journal, coalesce(si_date, as_of) tgl_journal,
            coalesce(supplier, '') nama_supp,
            coalesce(faktur_pajak, '') faktur_pajak,
            coalesce(profit_center, '') profit_center,
            curr, rate,
            amount_ocy ocy, amount_idr idr,
            'BEG' bucket
        from tbl_ppn_saldo_awal
        where status = 'Post' and as_of <= '$dFrom'
    ),
    bal as (
        select k,
            /* identitas baris diambil dari dokumen ASAL (bukan dokumen deduction) */
            coalesce(
                substring_index(group_concat(case when bucket <> 'DED' then no_journal end
                                order by tgl_journal, id separator '||'), '||', 1),
                substring_index(group_concat(no_journal order by tgl_journal, id separator '||'), '||', 1)
            ) si_no,
            coalesce(min(case when bucket <> 'DED' then tgl_journal end), min(tgl_journal)) si_date,
            coalesce(
                nullif(substring_index(group_concat(case when bucket <> 'DED' then nama_supp end
                       order by tgl_journal, id separator '||'), '||', 1), ''),
                substring_index(group_concat(nama_supp order by tgl_journal, id separator '||'), '||', 1)
            ) nama_supp,
            max(faktur_pajak) faktur_pajak,
            /* profit center ikut dokumen ASAL; GM uploadan bisa beda PC dgn invoice-nya */
            coalesce(
                nullif(substring_index(group_concat(case when bucket <> 'DED' then profit_center end
                       order by tgl_journal, id separator '||'), '||', 1), ''),
                substring_index(group_concat(profit_center order by tgl_journal, id separator '||'), '||', 1)
            ) profit_center,
            substring_index(group_concat(curr order by tgl_journal, id separator '||'), '||', 1) curr,

            sum(case when bucket = 'BEG' then ocy else 0 end) beg_ocy,
            sum(case when bucket = 'BEG' then idr else 0 end) beg_idr,
            sum(case when bucket = 'ADD' then ocy else 0 end) add_ocy,
            sum(case when bucket = 'ADD' then idr else 0 end) add_idr,
            sum(case when bucket = 'DED' then ocy else 0 end) ded_ocy,
            sum(case when bucket = 'DED' then idr else 0 end) ded_idr,
            sum(case when bucket = 'RCL' then ocy else 0 end) rcl_ocy,
            sum(case when bucket = 'RCL' then idr else 0 end) rcl_idr,
            sum(case when bucket = 'ADJ' then ocy else 0 end) adj_ocy,
            sum(case when bucket = 'ADJ' then idr else 0 end) adj_idr,

            group_concat(distinct case when bucket = 'DED' then no_journal end separator ', ') ded_no,
            max(case when bucket = 'DED' then tgl_journal end) ded_date,
            group_concat(distinct case when bucket = 'RCL' then no_journal end separator ', ') rcl_no,
            max(case when bucket = 'RCL' then tgl_journal end) rcl_date,
            group_concat(distinct case when bucket = 'ADJ' then no_journal end separator ', ') adj_no,
            max(case when bucket = 'ADJ' then tgl_journal end) adj_date,

            /* Ending = saldo kumulatif s/d To (BUKAN jumlah kolom di atas), jadi dijamin
               sama persis dgn Beginning periode berikutnya. */
            sum(ocy) end_ocy,
            sum(idr) end_idr
        from mut
        group by k
    )
    select * from bal
    where (beg_ocy <> 0 or add_ocy <> 0 or ded_ocy <> 0 or rcl_ocy <> 0 or adj_ocy <> 0 or end_ocy <> 0)";

    // Filter supplier ditempel DI LUAR (setelah nama supplier baris ditentukan) supaya
    // saldo per kunci tidak ikut terpotong -> Beginning/Ending tetap utuh.
    if ($nama_supp !== 'ALL') {
        $sql .= " and nama_supp = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'";
    }
    $sql .= " order by nama_supp, faktur_pajak, si_date, si_no";

    return $sql;
}
}
