-- =====================================================================
-- POSTING SELISIH KURS AP LEWAT SQL  (bukan cuma menghitung - MENULIS)
-- =====================================================================
--
--  *** BERKAS INI MENULIS KE DATABASE ***
--
-- Bedanya dengan sql_test_forex_ap.sql: yang itu hanya SELECT, berkas ini
-- benar-benar MENGISI tbl_list_journal dan tbl_memorial_journal - hasil
-- akhirnya sama persis dengan menekan tombol Post di halaman
-- AP FOREX REVALUATION, atau dengan apa yang dikerjakan
-- sync_selisih_kurs_scheduler.php tiap malam.
--
-- Dipakai kalau mau memposting dari SQL langsung, tanpa lewat halaman.
--
-- CARA PAKAI
--   1. Ubah @per / @sampai / @user di blok parameter kalau perlu.
--   2. Jalankan seluruh berkas dalam SATU koneksi, dari atas ke bawah.
--      Tabel sementaranya hilang sendiri waktu koneksi ditutup.
--   3. Bagian PRATINJAU (sebelum START TRANSACTION) menampilkan apa yang
--      AKAN ditulis. Kalau mau membatalkan setelah melihat hasilnya,
--      ganti COMMIT di bawah menjadi ROLLBACK.
--
-- AMAN DIJALANKAN ULANG. Sama seperti PHP-nya:
--   * dokumen yang sudah ada DIPAKAI NOMORNYA KEMBALI, barisnya dihapus
--     lalu ditulis ulang (bukan di-UPDATE per baris, karena daftar COA-nya
--     bisa berubah antar perhitungan);
--   * dokumen BASI - pernah dijurnal tapi sekarang tidak ada selisihnya
--     lagi, misalnya karena BPB-nya dibatalkan - ikut dihapus;
--   * tanggal yang periodenya sudah Closed TIDAK pernah ditulis, tapi
--     saldonya tetap jalan melewatinya supaya tanggal sesudahnya benar.
--
-- RUMUS, PENJELASAN KURS, DAN ARAH DEBIT/KREDIT: lihat komentar panjang di
-- sql_test_forex_ap.sql. Blok WITH di langkah 1 di bawah adalah salinan
-- apa adanya dari berkas itu, termasuk kedua salinan query AP Report -
-- jadi kalau rumusnya diperbaiki, DUA berkas ini harus diperbaiki sama.
-- =====================================================================


-- ----------------------------------------------------------- parameter --

-- Jurnal buatan aplikasi mengisi reff_date/approve_date/cancel_date dengan
-- string kosong, yang di MySQL jadi '0000-00-00'. Disetel di sini supaya
-- baris hasil SQL ini SAMA BENTUKNYA dengan baris hasil PHP, dan supaya
-- tidak gagal kalau server kebetulan bermode ketat.
SET SESSION sql_mode = 'ALLOW_INVALID_DATES';

SET @per         := '2026-06-30';   -- SEHARI SEBELUM mulai MENGHITUNG (FXAP_MIN_DATE)
SET @jurnal_dari := '2026-11-01';   -- mulai MEMBUAT JURNAL (FXAP_POST_FROM)
SET @sampai      := CURDATE();      -- scheduler selalu s/d HARI INI
SET @user        := 'SQL MANUAL';   -- isi kolom create_by, supaya ketahuan asalnya
SET @mulai       := DATE_ADD(CAST(@per AS DATE), INTERVAL 1 DAY);


-- =====================================================================
-- LANGKAH 1 - hitung rinciannya ke tabel sementara
-- =====================================================================
DROP TEMPORARY TABLE IF EXISTS fxap_baris;
CREATE TEMPORARY TABLE fxap_baris AS
SELECT * FROM (

WITH RECURSIVE

-- Setiap tanggal kalender, bukan cuma yang ada mutasinya: nilai utang bisa
-- berubah murni karena kurs KMK bergerak. Baris pertama = hari pembuka.
fx_kalender AS (
    SELECT CAST(@per AS DATE) AS tgl
    UNION ALL
    SELECT tgl + INTERVAL 1 DAY FROM fx_kalender WHERE tgl < CAST(@sampai AS DATE)
),

-- COA yang ikut direvaluasi - dari flag di master, bukan dari kode.
fx_coa AS (
    SELECT TRIM(no_coa) AS no_coa, nama_coa
      FROM mastercoa_v2 WHERE forex_ap = 'Y'
),

-- ------------------------------------------------------------------- --
-- SALINAN query_bpb2.php - saldo GR/IR. Query ini menyaring SENDIRI
-- nama_coa like 'GR%', jadi tidak perlu dipetakan COA mana dari sini.
-- ------------------------------------------------------------------- --
rpt_bpb AS (
    WITH
    rate as (
        select * from ap_masterrate
        where tanggal = @per
          and v_codecurr = CASE WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END
        GROUP BY curr, rate
    ),

    po_bpb as (
        select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top
        from bpb a
        INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier
        LEFT JOIN po_header c on c.pono = a.pono
        LEFT JOIN po_header_draft d on d.id = c.id_draft
        where a.bpbdate > '2024-12-31' and confirm = 'Y' and cancel = 'N'
        GROUP BY bpbno_int
        UNION
        select a.bppbno_int, b.supplier, '' pono, 0 top
        from bppb a
        INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier
        where a.bppbdate > '2024-12-31' and confirm = 'Y' and cancel = 'N'
        GROUP BY bppbno_int
    ),

    saldo_awal as (
        select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr,
               a.no_coa, nama_coa, item_type1, item_type2, relasi
        from ap_report_saldo_bpb a
        INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
    ),

    trx_in as (
        select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select supplier, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr, IF(type_journal='AP - BPB RETURN',sum(debit),sum(credit)) total,
                   a.rate, IF(type_journal='AP - BPB RETURN',sum(debit*rate),sum(credit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - BPB','AP - BPB RETURN')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.no_journal
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY no_journal, no_coa
        ) a where total != 0
    ),

    trx_in_before as (
        select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select supplier, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr, IF(type_journal='AP - BPB RETURN',sum(debit),sum(credit)) total,
                   a.rate, IF(type_journal='AP - BPB RETURN',sum(debit*rate),sum(credit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - BPB','AP - BPB RETURN')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.no_journal
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY no_journal, no_coa
        ) a where total != 0
    ),

    trx_in_reverse as (
        select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select supplier, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr, IF(type_journal='Reverse AP - BPB',sum(debit),sum(credit)) total,
                   a.rate, IF(type_journal='Reverse AP - BPB',sum(debit*rate),sum(credit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.no_journal
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY no_journal, no_coa
        ) a where total != 0
    ),

    trx_in_reverse_before as (
        select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select supplier, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr, IF(type_journal='Reverse AP - BPB',sum(debit),sum(credit)) total,
                   a.rate, IF(type_journal='Reverse AP - BPB',sum(debit*rate),sum(credit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.no_journal
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY no_journal, no_coa
        ) a where total != 0
    ),

    trx_out_kb as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*a.rate),sum(debit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal not like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_kb_before as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*a.rate),sum(debit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal not like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_reverse_kb as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit*a.rate),sum(credit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select * from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal not like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_reverse_kb_before as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit*a.rate),sum(credit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select * from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal not like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_kb_revisi as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*a.rate),sum(debit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_kb_revisi_before as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*a.rate),sum(debit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_reverse_kb_revisi as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit*a.rate),sum(credit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select * from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_reverse_kb_revisi_before as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, a.curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit*a.rate),sum(credit*a.rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select * from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('Reverse AP - Kontrabon')
                  and nama_coa like 'GR%'
                  and no_journal like '%-REV_%'
            ) a
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal
            where d.status != 'Cancel'
            GROUP BY reff_doc
        ) a where total != 0
    ),

    trx_out_gm as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*rate),sum(debit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal BETWEEN @per and @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('ACCOUNT PAYABLE')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.reff_doc
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY reff_doc, no_coa
        ) a where total != 0
    ),

    trx_out_gm_before as (
        select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top,
                   DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date,
                   curr,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total,
                   a.rate,
                   IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit*rate),sum(debit*rate)) total_idr,
                   a.no_coa, a.nama_coa, c.item_type1, c.item_type2, c.relasi
            from (
                select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit
                from tbl_list_journal
                where tgl_journal < @per
                  and tgl_journal > '2026-06-30'
                  and type_journal IN ('ACCOUNT PAYABLE')
                  and nama_coa like 'GR%'
            ) a
            INNER JOIN po_bpb b on b.bpbno_int = a.reff_doc
            INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa
            GROUP BY reff_doc, no_coa
        ) a where total != 0
    ),

    saldo_in as (
        select supplier, no_bpb, tgl_bpb, top, duedate, curr,
               sum(COALESCE(saldo_awal,0)) saldo_awal,
               sum(COALESCE(total_in,0)) total_in,
               rate, no_coa, nama_coa, item_type1, item_type2, relasi
        from (
            select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in,
                   rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
            UNION ALL
            select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in,
                   rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
            UNION ALL
            select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in,
                   rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in
        ) a GROUP BY no_bpb, no_coa
    ),

    saldo_out_per_coa as (
        select no_journal, no_coa,
               sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before,
               sum(COALESCE(reverse_bpb,0)) reverse_bpb,
               sum(COALESCE(gm_before,0)) gm_before,
               sum(COALESCE(gm,0)) gm
        from (
            select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
            UNION ALL
            select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
            UNION ALL
            select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
            UNION ALL
            select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before
        ) a GROUP BY no_journal, no_coa
    ),

    saldo_out_per_bpb as (
        SELECT reff_doc,
               SUM(COALESCE(kontrabon_before,0)) kontrabon_before,
               SUM(COALESCE(kontrabon,0)) kontrabon,
               SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before,
               SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon,
               SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before,
               SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi,
               SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before,
               SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi
        from (
            select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
            UNION ALL
            select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
            UNION ALL
            select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
        ) a GROUP BY reff_doc
    ),

    data_mutasi as (
        select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa,
               a.item_type1, a.item_type2, a.relasi,
               coalesce(a.saldo_awal,0) saldo_awal,
               coalesce(a.total_in,0) total_in,
               reverse_bpb_before, reverse_bpb, gm_before, gm,
               IF(kontrabon_before>0, if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) kontrabon_before,
               IF(kontrabon>0,        if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) kontrabon,
               IF(reverse_kontrabon_before>0, if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before,
               IF(reverse_kontrabon>0,        if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon,
               IF(kontrabon_revisi_before>0, if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before,
               IF(kontrabon_revisi>0,        if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi,
               IF(reverse_kontrabon_revisi_before>0, if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before,
               IF(reverse_kontrabon_revisi>0,        if(COALESCE(total_in,0)>0,(COALESCE(total_in,0)-COALESCE(reverse_bpb,0)),(COALESCE(saldo_awal,0)-COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi
        from saldo_in a
        LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb
        LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb
    ),

    mutasi_det as (
        select supplier, no_bpb, tgl_bpb, duedate, curr, rate,
               (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0)
                - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0)
                + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal,
               COALESCE(total_in,0) in_bpb,
               COALESCE(reverse_bpb,0) reverse_bpb,
               (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon,
               (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon,
               COALESCE(gm,0) gm,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from data_mutasi
    ),

    mutasi as (
        select supplier, no_bpb, tgl_bpb, duedate, a.curr,
               saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm,
               (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir,
               IFNULL(b.rate,1) rate,
               no_coa, nama_coa, item_type1, item_type2, relasi
        from mutasi_det a
        LEFT JOIN rate b on b.curr = a.curr
    ),

    laporan_mutasi as (
        SELECT
               /* PROFIT CENTER - diambil dari tbl_list_journal, lalu KODE-nya
                  (NAG/NAK) diterjemahkan ke NAMA PENUH lewat master_pc, karena
                  yang diminta di layar nama penuhnya, bukan singkatannya.

                  BENTUKNYA SUBQUERY BERKORELASI, DAN ITU DISENGAJA. Cara yang
                  kelihatan lebih rapi - satu CTE berisi select no_journal,
                  MAX(profit_center) ... group by no_journal lalu di-LEFT JOIN -
                  SUDAH DICOBA dan DIBUANG: mengelompokkan no_journal (varchar
                  panjang) atas
                  seluruh tbl_list_journal makan +-7 DETIK sekali jalan, di atas
                  laporan yang sudah 21 detik. Bentuk ini sebaliknya hanya
                  sebanyak baris laporan (+-4.000) pencarian lewat indeks
                  no_journal, +-0,1 detik. Jangan dirapikan jadi CTE+GROUP BY.

                  DITARUH DI CTE INI, bukan di SELECT terakhir, supaya
                  profit_center menjadi KOLOM SUNGGUHAN milik laporan_mutasi -
                  dgn begitu kotak cari DataTables (lewat variabel where milik
                  berkas ini) bisa menyaringnya.
                  Kalau ditulis di SELECT terakhir ia cuma alias, dan MySQL tidak
                  menerima alias di WHERE.

                  TANPA BATAS TANGGAL - dan itu perlu: BPB sebelum cut-over
                  saldonya datang dari ap_report_saldo_bpb, dan tabel itu TIDAK
                  punya kolom profit center, jadi jurnal satu-satunya sumber.
                  Diperiksa di salinan lokal: 1.260 BPB saldo awal semuanya
                  ketemu jurnalnya (963 NAG, 297 NAK).

                  MAX() penjaga kalau satu dokumen punya lebih dari satu profit
                  center; diperiksa di data: tidak ada, kecuali dua baris sampah
                  ber-no_journal '' dan '-' yang tidak akan pernah dicari di
                  sini. */
               (select IFNULL(MAX(mp.nama_pc), MAX(j.profit_center))
                  from tbl_list_journal j
                  LEFT JOIN master_pc mp on mp.kode_pc = j.profit_center
                 where j.no_journal = m.no_bpb
                   and j.type_journal IN ('AP - BPB','AP - BPB RETURN')
                   and COALESCE(j.profit_center,'') != '') profit_center,
               supplier, no_bpb, tgl_bpb, duedate, curr,
               saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir,
               rate, (saldo_akhir * rate) saldo_akhir_idr,
               no_coa, nama_coa, item_type1, item_type2, relasi,
               CASE WHEN duedate > @per THEN (saldo_akhir * rate) ELSE 0 END AS due_current,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 0  AND 30  THEN (saldo_akhir * rate) ELSE 0 END AS due_1_30,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 31 AND 60  THEN (saldo_akhir * rate) ELSE 0 END AS due_31_60,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 61 AND 90  THEN (saldo_akhir * rate) ELSE 0 END AS due_61_90,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END AS due_91_120,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END AS due_121_180,
               CASE WHEN DATEDIFF(@per, duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END AS due_181_360,
               CASE WHEN DATEDIFF(@per, duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END AS due_gt_360,
               (
                   CASE WHEN duedate > @per THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 0   AND 30  THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 31  AND 60  THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 61  AND 90  THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 91  AND 120 THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 121 AND 180 THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) BETWEEN 181 AND 360 THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN DATEDIFF(@per,duedate) > 360 THEN (saldo_akhir*rate) ELSE 0 END
               ) AS total_due,
               CASE WHEN duedate <= @per THEN (saldo_akhir * rate) ELSE 0 END AS pro_due,
               CASE WHEN duedate > @per
                     AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due0,
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
                     AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due1,
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due2,
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due3,
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due4,
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
                    THEN (saldo_akhir * rate) ELSE 0 END AS pro_due5,
               (
                   CASE WHEN duedate <= @per THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >  @per
                         AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
                        THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
                         AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
                        THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
                         AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
                        THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
                         AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
                        THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
                         AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
                        THEN (saldo_akhir*rate) ELSE 0 END +
                   CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
                        THEN (saldo_akhir*rate) ELSE 0 END
               ) AS tot_produe,
               CASE WHEN no_bpb LIKE '%RO%' OR no_bpb LIKE '%OUT%' THEN -1 ELSE 1 END AS f
        from mutasi m
    )

    SELECT
        IFNULL(profit_center, '-') profit_center,
        supplier, no_bpb, tgl_bpb, duedate, curr,
        saldo_awal*f AS saldo_awal,
        in_bpb*f AS in_bpb,
        reverse_bpb*f AS reverse_bpb,
        ded_kontrabon*f AS ded_kontrabon,
        reverse_kontrabon*f AS reverse_kontrabon,
        gm*f AS gm,
        saldo_akhir*f AS saldo_akhir,
        rate,
        saldo_akhir_idr*f AS saldo_akhir_idr,
        no_coa, nama_coa, item_type1, item_type2, relasi,
        due_current*f AS due_current,
        due_1_30*f AS due_1_30,
        due_31_60*f AS due_31_60,
        due_61_90*f AS due_61_90,
        due_91_120*f AS due_91_120,
        due_121_180*f AS due_121_180,
        due_181_360*f AS due_181_360,
        due_gt_360*f AS due_gt_360,
        total_due*f AS total_due,
        pro_due*f AS pro_due,
        pro_due0*f AS pro_due0,
        pro_due1*f AS pro_due1,
        pro_due2*f AS pro_due2,
        pro_due3*f AS pro_due3,
        pro_due4*f AS pro_due4,
        pro_due5*f AS pro_due5,
        tot_produe*f AS tot_produe
    FROM laporan_mutasi WHERE 1=1
),

-- ------------------------------------------------------------------- --
-- SALINAN query_pv2.php - saldo UTANG USAHA.
-- ------------------------------------------------------------------- --
rpt_pv AS (
    WITH
    rate as (select * from ap_masterrate where tanggal = @per and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

    saldo_awal as (select nama_supp supplier, no_kbon, tgl_kbon, tgl_tempo duedate, curr, (subtotal + tax) total, rate, no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_payment_voucher),

    in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN @per and @per and type_journal = 'AP - Kontrabon' and a.nama_coa like 'GR%' and b.status != 'Cancel' and no_journal not like '%/INS%' GROUP BY no_journal
    UNION
    select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join rate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') BETWEEN @per and @per 
    ),

    in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal = 'AP - Kontrabon' and a.nama_coa like 'GR%' and b.status != 'Cancel' and no_journal not like '%/INS%' GROUP BY no_journal
    UNION
    select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join rate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') < @per
    ),

    reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN @per and @per and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

    reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

    uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN @per and @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
    ),

    uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
    ),

    ppn as (select a.no_journal, sum(a.debit - a.credit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN @per and @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%PPN MASUKAN%' and b.status != 'Cancel' GROUP BY no_journal),

    ppn_before as (select a.no_journal, sum(a.debit - a.credit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%PPN MASUKAN%' and b.status != 'Cancel' GROUP BY no_journal),

    pph as (select a.no_kbon, round(COALESCE(pph_idr,0) + COALESCE(potongan_pph,0),2) pph from kontrabon_h a INNER JOIN potongan b on b.no_kbon = a.no_kbon where COALESCE(pph_idr,0) > 0 GROUP BY a.no_kbon),

    pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(create_date, '%Y-%m-%d') < @per and status != 'Cancel' GROUP BY no_kbon),

    pph_um_all as (select h.no_kbon, MAX(j.tgl_journal) tgl_um,
        round(COALESCE(h.pph_idr,0) / IF(h.curr = 'IDR' OR COALESCE(h.rate,0) = 0, 1, h.rate), 2) pph_um,
        MAX(h.subtotal) subtotal_doc, SUM(j.credit - j.debit) um_jurnal
      from kontrabon_h h
      INNER JOIN tbl_list_journal j on j.no_journal = h.no_kbon and j.type_journal = 'AP - Kontrabon' and j.nama_coa like '%UANG MUKA%'
      where h.status != 'Cancel' and COALESCE(h.pph_idr,0) > 0
        and not exists (select 1 from tbl_list_journal p where p.reff_doc = h.no_kbon and p.type_journal like '%Payment%')
      GROUP BY h.no_kbon
      having round(um_jurnal - (subtotal_doc - pph_um), 2) = 0),

    pph_um as (select no_kbon, pph_um from pph_um_all where tgl_um BETWEEN @per and @per),

    pph_um_before as (select no_kbon, pph_um from pph_um_all where tgl_um > '2026-06-30' and tgl_um < @per),

    potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN @per and @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

    potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

    ded_bank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between @per and @per and type_journal like '%Payment Voucher%' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
    ),

    ded_bank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal like '%Payment Voucher%' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

    ded_cash as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between @per and @per and type_journal like '%Payment Voucher%' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
    ),

    ded_cash_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal like '%Payment Voucher%' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

    ded_nonbank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between @per and @per and type_journal like '%Payment Non Bank%' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
    ),

    ded_nonbank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and type_journal like '%Payment Non Bank%' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

    ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between @per and @per and (reff_doc like '%SI/APR%' OR reff_doc like '%PV-AP/%') and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

    ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < @per and (reff_doc like '%SI/APR%' OR reff_doc like '%PV-AP/%') and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

    saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
    UNION ALL
    select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
    UNION ALL
    select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

    Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_bank_before,0)) ded_bank_before, SUM(COALESCE(ded_bank,0)) ded_bank, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm, SUM(COALESCE(ded_cash,0)) ded_cash, SUM(COALESCE(ded_cash_before,0)) ded_cash_before, SUM(COALESCE(ded_nonbank,0)) ded_nonbank, SUM(COALESCE(ded_nonbank_before,0)) ded_nonbank_before FROM (
    select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from reverse_kontrabon_before
    UNION ALL
    select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from reverse_kontrabon
    UNION ALL
    select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from uang_muka_before
    UNION ALL
    /* PPh atas uang muka periode sebelumnya -> memperbesar pengurang uang muka */
    select no_kbon no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, -pph_um uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph_um_before
    UNION ALL
    select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from uang_muka
    UNION ALL
    /* PPh atas uang muka periode berjalan -> memperbesar pengurang uang muka */
    select no_kbon no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, -pph_um uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph_um
    UNION ALL
    select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph_before
    UNION ALL
    select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph
    UNION ALL
    select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from potongan_before
    UNION ALL
    select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from potongan
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_bank_before
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, total ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_bank
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, total ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_gm_before
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, total ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_gm
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, total ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, total ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash_before
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, total ded_nonbank, 0 ded_nonbank_before from ded_nonbank
    UNION ALL
    select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, total ded_nonbank_before from ded_nonbank_before) A GROUP BY no_journal),

    data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_bank_before,2),0) ded_bank_before, COALESCE(round(ded_bank,2),0) ded_bank, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm, COALESCE(round(ded_cash,2),0) ded_cash, COALESCE(round(ded_cash_before,2),0) ded_cash_before, COALESCE(round(ded_nonbank,2),0) ded_nonbank, COALESCE(round(ded_nonbank_before,2),0) ded_nonbank_before, COALESCE(round(pn.total,2),0) ppn, COALESCE(round(pnb.total,2),0) ppn_before from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon LEFT JOIN ppn pn on pn.no_journal = a.no_kbon LEFT JOIN ppn_before pnb on pnb.no_journal = a.no_kbon),

    mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + potongan_before + ppn_before - (if(ded_bank_before > 0,(ded_bank_before),0) + ded_gm_before + if(ded_cash_before > 0,(ded_cash_before),0) + if(ded_nonbank_before > 0,(ded_nonbank_before),0))) saldo_awal, total_in, pph, uang_muka, (potongan + ppn) potongan, if(ded_bank > 0,(ded_bank),0) ded_bank, if(ded_cash > 0,(ded_cash),0) ded_cash, if(ded_nonbank > 0,(ded_nonbank),0) ded_nonbank, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

    report_mutasi as (select
    /* PROFIT CENTER - sama bentuk & alasannya dgn query_bpb2.php: subquery
       berkorelasi (bukan CTE + GROUP BY, yang makan +-7 detik), ditaruh di CTE
       terakhir supaya jadi kolom sungguhan dan bisa disaring kotak cari.

       TIGA SUMBER BERURUTAN lewat COALESCE, dan urutannya penting:
       1. JURNAL - ini laporan jurnal, jadi jurnal yang menang. Di salinan lokal
          ada 36 PV yang profit center di jurnalnya BEDA dgn yang tercatat di
          kontrabon_h; yang ditampilkan harus yang masuk buku besar.
       2. INDUK CICILAN - anak kontrabon_h_installment_detail muncul di laporan
          dgn nomornya sendiri (no_kbon_det) sementara jurnalnya menempel di
          induk, dan jurnal induk ber-'/INS' justru DIBUANG di CTE in_kontrabon.
          Tanpa langkah ini baris cicilan kosong profit center-nya.
       3. SALDO CUT-OVER - baris dari ap_saldo_payment_voucher (per 30 Jun
          2026). Tabel itu kebetulan punya kolom profit_center sendiri.

       Tiap sumber SUDAH diterjemahkan ke nama penuh di dalam subquery-nya
       masing-masing, lalu nama-nama itu yang di-COALESCE. Sengaja tidak
       meng-COALESCE kodenya dulu lalu menerjemahkan sekali di luar: itu
       menuntut derived table yang menengok kolom query luar, dan MySQL
       melarangnya (perlu LATERAL). */
    COALESCE(
      (select IFNULL(MAX(mp.nama_pc), MAX(j.profit_center))
         from tbl_list_journal j
         LEFT JOIN master_pc mp on mp.kode_pc = j.profit_center
        where j.no_journal = a.no_kbon and j.type_journal = 'AP - Kontrabon'
          and COALESCE(j.profit_center,'') != ''),
      (select IFNULL(MAX(mp.nama_pc), MAX(h.profit_center))
         from kontrabon_h_installment_detail ins
         INNER JOIN kontrabon_h h on h.no_kbon = ins.no_kbon
         LEFT JOIN master_pc mp on mp.kode_pc = h.profit_center
        where ins.no_kbon_det = a.no_kbon and COALESCE(h.profit_center,'') != ''),
      (select IFNULL(MAX(mp.nama_pc), MAX(v.profit_center))
         from ap_saldo_payment_voucher v
         LEFT JOIN master_pc mp on mp.kode_pc = v.profit_center
        where v.no_kbon = a.no_kbon and COALESCE(v.profit_center,'') != '')
    ) profit_center,
    supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in  + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

    select IFNULL(profit_center, '-') profit_center, supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
    CASE
    WHEN duedate > @per THEN saldo_akhir_idr
    ELSE 0
    END AS due_current,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 0 AND 30
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_1_30,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 31 AND 60
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_31_60,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 61 AND 90
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_61_90,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 91 AND 120
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_91_120,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 121 AND 180
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_121_180,

    CASE
    WHEN DATEDIFF(@per, duedate) BETWEEN 181 AND 360
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_181_360,

    CASE
    WHEN DATEDIFF(@per, duedate) > 360
    THEN saldo_akhir_idr
    ELSE 0
    END AS due_gt_360,

    (
    CASE WHEN duedate > @per THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN DATEDIFF(@per, duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,

    CASE
    WHEN duedate <= @per
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due,

    CASE
    WHEN duedate > @per
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due0,

    CASE
    WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
    AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due1,

    CASE
    WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
    AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due2,

    CASE
    WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
    AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due3,

    CASE
    WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
    AND duedate <  DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due4,

    CASE
    WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
    THEN saldo_akhir_idr
    ELSE 0
    END AS pro_due5,

    (
    CASE WHEN duedate <= @per THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN duedate > @per
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
    THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 1 DAY)
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
    THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 2 MONTH)
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
    THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 3 MONTH)
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
    THEN saldo_akhir_idr ELSE 0 END +
    CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 4 MONTH)
    AND duedate < DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
    THEN saldo_akhir_idr ELSE 0 END +

    CASE WHEN duedate >= DATE_ADD(LAST_DAY(@per), INTERVAL 5 MONTH)
    THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from report_mutasi 
     WHERE 1=1
),

-- Saldo awal per profit center x COA pada akhir @per, DARI LAPORAN AP
-- (bukan GL) - keputusan pemakai: "saldo awal ikuti saja excel, excel
-- ambil dari ap report". Akibatnya saldo di sini bisa beda dari saldo GL
-- untuk COA yang sama, dan itu diterima.
fx_awal AS (
    SELECT g.pc, g.no_coa, SUM(g.saldo_usd) AS usd
      FROM (
            SELECT COALESCE((SELECT p.kode_pc FROM master_pc p
                       WHERE UPPER(TRIM(p.kode_pc)) = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(p.id_pc))   = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(p.nama_pc)) = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(CONCAT(p.id_pc, ' - ', p.nama_pc))) = UPPER(TRIM(z.profit_center))
                       LIMIT 1),
                     UPPER(TRIM(z.profit_center))) AS pc,
                   TRIM(z.no_coa) AS no_coa, z.saldo_akhir AS saldo_usd
              FROM rpt_bpb z
             WHERE UPPER(TRIM(z.curr)) = 'USD'
               AND TRIM(COALESCE(z.no_coa, '')) <> ''
            UNION ALL
            SELECT COALESCE((SELECT p.kode_pc FROM master_pc p
                       WHERE UPPER(TRIM(p.kode_pc)) = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(p.id_pc))   = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(p.nama_pc)) = UPPER(TRIM(z.profit_center))
                          OR UPPER(TRIM(CONCAT(p.id_pc, ' - ', p.nama_pc))) = UPPER(TRIM(z.profit_center))
                       LIMIT 1),
                     UPPER(TRIM(z.profit_center))) AS pc,
                   TRIM(z.no_coa) AS no_coa, z.saldo_akhir AS saldo_usd
              FROM rpt_pv z
             WHERE UPPER(TRIM(z.curr)) = 'USD'
               AND TRIM(COALESCE(z.no_coa, '')) <> ''
           ) g
      INNER JOIN fx_coa c ON c.no_coa = g.no_coa
     WHERE TRIM(g.pc) <> ''
     GROUP BY g.pc, g.no_coa
),

-- Mutasi harian dari jurnal. credit - debit, karena akun kewajiban.
fx_mutasi AS (
    SELECT j.tgl_journal AS tgl,
           COALESCE((SELECT p.kode_pc FROM master_pc p
                      WHERE UPPER(TRIM(p.kode_pc)) = UPPER(TRIM(j.profit_center))
                         OR UPPER(TRIM(p.id_pc))   = UPPER(TRIM(j.profit_center))
                         OR UPPER(TRIM(p.nama_pc)) = UPPER(TRIM(j.profit_center))
                         OR UPPER(TRIM(CONCAT(p.id_pc, ' - ', p.nama_pc))) = UPPER(TRIM(j.profit_center))
                      LIMIT 1),
                    UPPER(TRIM(j.profit_center))) AS pc,
           TRIM(j.no_coa) AS no_coa,
           SUM(j.credit)     - SUM(j.debit)     AS usd,
           SUM(j.credit_idr) - SUM(j.debit_idr) AS idr
      FROM tbl_list_journal j
      INNER JOIN fx_coa c ON c.no_coa = TRIM(j.no_coa)
     WHERE j.curr = 'USD'
       AND j.status <> 'Cancel'
       AND j.tgl_journal BETWEEN CAST(@mulai AS DATE) AND CAST(@sampai AS DATE)
     GROUP BY j.tgl_journal, pc, TRIM(j.no_coa)
),

-- Semua kombinasi pc x coa yang pernah muncul - dari saldo awal MAUPUN
-- dari mutasi, supaya COA yang baru dipakai di tengah periode ikut.
fx_kunci AS (
    SELECT pc, no_coa FROM fx_awal
    UNION
    SELECT pc, no_coa FROM fx_mutasi WHERE TRIM(pc) <> ''
),

-- Kurs penilai, SATU aturan untuk semua tanggal (termasuk hari pembuka):
-- kurs PAJAK/KMK, KECUALI tanggal AKHIR BULAN yang memakai kurs HARIAN.
-- Sama dengan fxApRatePasar() di fxap_functions.php dan sama dengan cara AP
-- Report menilai saldo, jadi saldo akhir bulan di buku besar sama dengan yang
-- dilihat accounting di laporan.
--
-- Akibatnya tiap akhir bulan ada jurnal lonjakan lalu tanggal 1 bulan
-- berikutnya ada jurnal balik sebesar itu juga - memang disengaja, karena
-- neraca akhir bulan disajikan pada kurs HARIAN.
fx_kurs AS (
    SELECT k.tgl,
           COALESCE((SELECT m.rate FROM masterrate m
                      WHERE m.curr = 'USD' AND m.tanggal <= k.tgl
                        AND m.v_codecurr = CASE WHEN k.tgl = LAST_DAY(k.tgl)
                                                THEN 'HARIAN' ELSE 'PAJAK' END
                      ORDER BY m.tanggal DESC LIMIT 1), 1) AS kurs
      FROM fx_kalender k
),

-- Saldo USD berjalan per kombinasi, lewat running total - bukan loop.
fx_jalan AS (
    SELECT k.tgl, u.pc, u.no_coa, r.kurs,
           COALESCE(m.idr, 0) AS mutasi_idr,
           COALESCE(a.usd, 0)
             + SUM(COALESCE(m.usd, 0)) OVER (PARTITION BY u.pc, u.no_coa ORDER BY k.tgl)
             AS saldo_usd
      FROM fx_kalender k
      CROSS JOIN fx_kunci u
      INNER JOIN fx_kurs  r ON r.tgl = k.tgl
      LEFT  JOIN fx_awal  a ON a.pc = u.pc AND a.no_coa = u.no_coa
      LEFT  JOIN fx_mutasi m ON m.tgl = k.tgl AND m.pc = u.pc AND m.no_coa = u.no_coa
),

-- Nilai pasar kemarin = nilai buku pembuka hari ini.
fx_hitung AS (
    SELECT tgl, pc, no_coa, kurs, mutasi_idr, saldo_usd,
           saldo_usd * kurs AS pasar,
           LAG(saldo_usd * kurs) OVER (PARTITION BY pc, no_coa ORDER BY tgl) AS pasar_kemarin
      FROM fx_jalan
),

-- Hanya baris yang benar-benar ada selisihnya, mulai tanggal @mulai.
-- Dipisah ke CTE sendiri supaya net per dokumen di bawah dihitung dari
-- baris yang SUDAH tersaring - sama seperti di PHP.
fx_baris AS (
    SELECT tgl, pc, no_coa, kurs, mutasi_idr, saldo_usd, pasar,
           pasar_kemarin + mutasi_idr AS buku,
           ROUND(pasar - (pasar_kemarin + mutasi_idr), 2) AS selisih
      FROM fx_hitung
     WHERE tgl >= CAST(@jurnal_dari AS DATE)
       AND ROUND(pasar - (pasar_kemarin + mutasi_idr), 2) <> 0
)

SELECT b.tgl, b.pc, b.no_coa, b.selisih,
       ROUND(b.saldo_usd, 2) AS saldo_usd,
       b.kurs,
       ROUND(b.buku, 2)  AS nilai_buku,
       ROUND(b.pasar, 2) AS nilai_pasar,
       COALESCE((SELECT CASE WHEN cp.status_closing = 'Closed' THEN 'Closed' ELSE 'Open' END
                   FROM tbl_closing_periode cp
                  WHERE cp.bulan = LPAD(MONTH(b.tgl), 2, '0')
                    AND cp.tahun = YEAR(b.tgl) LIMIT 1), 'Open') AS periode
  FROM fx_baris b

) q;

ALTER TABLE fxap_baris ADD KEY (tgl, pc), ADD KEY (periode);


-- =====================================================================
-- LANGKAH 2 - kelompokkan jadi dokumen (1 tanggal x 1 profit center)
--             Hanya periode Open yang boleh jadi dokumen.
-- =====================================================================
DROP TEMPORARY TABLE IF EXISTS fxap_dok;
CREATE TEMPORARY TABLE fxap_dok AS
SELECT b.tgl,
       b.pc,
       ROUND(SUM(b.selisih), 2) AS net,
       COUNT(*)                 AS jml_baris,
       (SELECT j.no_journal FROM tbl_list_journal j
         WHERE j.tgl_journal = b.tgl AND j.profit_center = b.pc
           AND j.no_journal LIKE 'FX-AP/%' AND j.status <> 'Cancel'
         LIMIT 1)               AS no_journal
  FROM fxap_baris b
 WHERE b.periode = 'Open'
 GROUP BY b.tgl, b.pc;

ALTER TABLE fxap_dok ADD KEY (tgl, pc), ADD KEY (no_journal);


-- =====================================================================
-- LANGKAH 3 - nomor dokumen untuk yang BELUM punya
--             Pola "FX-AP/{pc}/{mmyy}/{00001}", urut per profit center per
--             bulan, melanjutkan nomor terakhir yang sudah terpakai. Sama
--             dengan fxApNomorBaru(): MAX-nya TIDAK menyaring status, jadi
--             dokumen yang sudah dibatalkan tetap memakan nomor.
-- =====================================================================
DROP TEMPORARY TABLE IF EXISTS fxap_nomor;
CREATE TEMPORARY TABLE fxap_nomor AS
SELECT x.tgl, x.pc,
       CONCAT('FX-AP/', x.pc, '/', DATE_FORMAT(x.tgl, '%m%y'), '/',
              LPAD(x.nomor_akhir
                   + ROW_NUMBER() OVER (PARTITION BY x.pc, DATE_FORMAT(x.tgl, '%m%y')
                                            ORDER BY x.tgl), 5, '0')) AS no_journal
  FROM (
        SELECT d.tgl, d.pc,
               (SELECT COALESCE(MAX(CAST(RIGHT(j.no_journal, 5) AS UNSIGNED)), 0)
                  FROM tbl_list_journal j
                 WHERE j.no_journal LIKE CONCAT('FX-AP/', d.pc, '/',
                                                DATE_FORMAT(d.tgl, '%m%y'), '/%')) AS nomor_akhir
          FROM fxap_dok d
         WHERE d.no_journal IS NULL
       ) x;

UPDATE fxap_dok d
  JOIN fxap_nomor n ON n.tgl = d.tgl AND n.pc = d.pc
   SET d.no_journal = n.no_journal
 WHERE d.no_journal IS NULL;


-- =====================================================================
-- LANGKAH 4 - dokumen BASI: pernah dijurnal, sekarang tidak ada selisih
--             lagi. Periode Closed tidak disentuh.
-- =====================================================================
DROP TEMPORARY TABLE IF EXISTS fxap_basi;
CREATE TEMPORARY TABLE fxap_basi AS
SELECT DISTINCT j.no_journal, j.tgl_journal AS tgl, j.profit_center AS pc
  FROM tbl_list_journal j
 WHERE j.no_journal LIKE 'FX-AP/%'
   AND j.status <> 'Cancel'
   AND j.tgl_journal BETWEEN CAST(@jurnal_dari AS DATE) AND CAST(@sampai AS DATE)
   AND COALESCE((SELECT CASE WHEN cp.status_closing = 'Closed' THEN 'Closed' ELSE 'Open' END
                   FROM tbl_closing_periode cp
                  WHERE cp.bulan = LPAD(MONTH(j.tgl_journal), 2, '0')
                    AND cp.tahun = YEAR(j.tgl_journal) LIMIT 1), 'Open') = 'Open'
   AND NOT EXISTS (SELECT 1 FROM fxap_dok d
                    WHERE d.tgl = j.tgl_journal AND d.pc = j.profit_center);


-- =====================================================================
-- PRATINJAU - apa yang AKAN ditulis. Belum ada yang berubah sampai sini.
-- =====================================================================

-- (a) daftar dokumen
SELECT d.tgl AS tanggal, d.pc AS profit_center, d.no_journal,
       d.jml_baris AS baris_utang, d.net,
       CASE WHEN d.net > 0 THEN 'RUGI - Debit 8.52.02'
            ELSE 'UNTUNG - Credit 8.52.02' END AS sisi_8_52_02,
       CASE WHEN EXISTS (SELECT 1 FROM tbl_list_journal j
                          WHERE j.no_journal = d.no_journal AND j.status <> 'Cancel')
            THEN 'update - baris lama ditulis ulang'
            ELSE 'insert - dokumen baru' END   AS aksi
  FROM fxap_dok d
 ORDER BY d.tgl, d.pc;

-- (b) dokumen basi yang akan DIHAPUS
SELECT no_journal, tgl AS tanggal, pc AS profit_center,
       'hapus - tidak ada selisih lagi' AS aksi
  FROM fxap_basi ORDER BY tgl, pc;

-- (c) bulan yang dilewati karena periodenya sudah Closed
SELECT DATE_FORMAT(tgl, '%Y-%m') AS bulan_closed,
       COUNT(*) AS baris_dilewati,
       ROUND(SUM(selisih), 2) AS selisih_tidak_dijurnal
  FROM fxap_baris WHERE periode <> 'Open'
 GROUP BY DATE_FORMAT(tgl, '%Y-%m') ORDER BY bulan_closed;


-- =====================================================================
-- MULAI MENULIS
-- =====================================================================
START TRANSACTION;

-- Kategori memorial journal CMJ021 harus ada (fxSkEnsureCategory).
INSERT INTO master_category_mj (id_cmj, nama_cmj)
SELECT 'CMJ021', 'FOREX GAIN OR LOSS'
 WHERE NOT EXISTS (SELECT 1 FROM master_category_mj WHERE id_cmj = 'CMJ021');

UPDATE master_category_mj SET nama_cmj = 'FOREX GAIN OR LOSS'
 WHERE id_cmj = 'CMJ021' AND nama_cmj <> 'FOREX GAIN OR LOSS';


-- --------------------------------------------- hapus baris lama & basi --
-- Selalu dibatasi no_journal LIKE 'FX-AP/%' - tidak boleh menyentuh jurnal
-- jenis lain walau nomornya kebetulan sama.
DELETE j FROM tbl_list_journal j
  JOIN fxap_dok d ON d.no_journal = j.no_journal
 WHERE j.no_journal LIKE 'FX-AP/%';

DELETE m FROM tbl_memorial_journal m
  JOIN fxap_dok d ON d.no_journal = m.no_mj
 WHERE m.no_mj LIKE 'FX-AP/%';

DELETE j FROM tbl_list_journal j
  JOIN fxap_basi s ON s.no_journal = j.no_journal
 WHERE j.no_journal LIKE 'FX-AP/%';

DELETE m FROM tbl_memorial_journal m
  JOIN fxap_basi s ON s.no_journal = m.no_mj
 WHERE m.no_mj LIKE 'FX-AP/%';


-- ------------------------------------------------- baris utang per COA --
-- selisih > 0 = nilai utang naik = DIKREDIT ; < 0 = DIDEBIT.
-- Cost center baris utang memang '-' (hanya 8.52.02 yang bercost-center).
INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
     no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws,
     curr, rate, debit, credit, debit_idr, credit_idr,
     status, keterangan, create_by, create_date,
     approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT d.no_journal,
       b.tgl,
       'FOREX GAIN OR LOSS - AP',
       b.no_coa,
       COALESCE((SELECT c.nama_coa FROM mastercoa_v2 c
                  WHERE c.no_coa = b.no_coa LIMIT 1), ''),
       '-', '-', '-', '', '-', '-',
       'IDR', 1,
       CASE WHEN b.selisih < 0 THEN ABS(b.selisih) ELSE 0 END,
       CASE WHEN b.selisih > 0 THEN ABS(b.selisih) ELSE 0 END,
       CASE WHEN b.selisih < 0 THEN ABS(b.selisih) ELSE 0 END,
       CASE WHEN b.selisih > 0 THEN ABS(b.selisih) ELSE 0 END,
       'Post',
       UPPER(CONCAT('REVALUASI KURS UTANG USAHA ',
              CASE UPPER(TRIM(b.pc))
                   WHEN 'NAG' THEN 'Divisi Garment'
                   WHEN 'NAK' THEN 'Divisi Knitting'
                   ELSE UPPER(TRIM(b.pc)) END, ' PER ',
              DATE_FORMAT(b.tgl, '%d'), ' ',
              ELT(MONTH(b.tgl), 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'), ' ',
              YEAR(b.tgl))),
       @user, NOW(),
       '', '', '', '',
       b.pc
  FROM fxap_baris b
  JOIN fxap_dok d ON d.tgl = b.tgl AND d.pc = b.pc
 WHERE b.periode = 'Open'
 ORDER BY b.tgl, b.pc, b.no_coa;


-- --------------------------------------------- baris lawan 8.52.02 --
-- net > 0 = RUGI = DIDEBIT ; net < 0 = UNTUNG = DIKREDIT.
-- Cost center dicari dari master menurut profit center dokumennya, sama
-- dengan fxSkCcSelisih(): NAG -> DEP06SUB001, NAK -> DEPNK09SUB001.
-- Kalau net tepat 0, barisnya tidak ditulis - dan dokumennya tetap
-- seimbang karena jumlah baris utangnya sendiri sudah 0.
INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
     no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws,
     curr, rate, debit, credit, debit_idr, credit_idr,
     status, keterangan, create_by, create_date,
     approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT d.no_journal,
       d.tgl,
       'FOREX GAIN OR LOSS - AP',
       '8.52.02',
       COALESCE((SELECT c.nama_coa FROM mastercoa_v2 c
                  WHERE c.no_coa = '8.52.02' LIMIT 1),
                'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI'),
       COALESCE(cc.no_cc, 'DEP06SUB001'),
       COALESCE(cc.cc_name, 'FINANCE, ACCOUNTING & TAX'),
       '-', '', '-', '-',
       'IDR', 1,
       CASE WHEN d.net > 0 THEN ABS(d.net) ELSE 0 END,
       CASE WHEN d.net < 0 THEN ABS(d.net) ELSE 0 END,
       CASE WHEN d.net > 0 THEN ABS(d.net) ELSE 0 END,
       CASE WHEN d.net < 0 THEN ABS(d.net) ELSE 0 END,
       'Post',
       UPPER(CONCAT('REVALUASI KURS UTANG USAHA ',
              CASE UPPER(TRIM(d.pc))
                   WHEN 'NAG' THEN 'Divisi Garment'
                   WHEN 'NAK' THEN 'Divisi Knitting'
                   ELSE UPPER(TRIM(d.pc)) END, ' PER ',
              DATE_FORMAT(d.tgl, '%d'), ' ',
              ELT(MONTH(d.tgl), 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                             'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'), ' ',
              YEAR(d.tgl))),
       @user, NOW(),
       '', '', '', '',
       d.pc
  FROM fxap_dok d
  LEFT JOIN (
        SELECT x.id_pc, x.no_cc, x.cc_name
          FROM b_master_cc x
         WHERE x.status = 'Active'
           AND x.group2 = 'SUPPORTING GENERAL & ADMINISTRATION'
           AND x.cc_name LIKE '%FINANCE%'
           AND x.no_cc = (SELECT MIN(y.no_cc) FROM b_master_cc y
                           WHERE y.id_pc = x.id_pc AND y.status = 'Active'
                             AND y.group2 = 'SUPPORTING GENERAL & ADMINISTRATION'
                             AND y.cc_name LIKE '%FINANCE%')
       ) cc ON cc.id_pc = d.pc
 WHERE d.net <> 0
 ORDER BY d.tgl, d.pc;


-- ------------------------------------- cermin ke tbl_memorial_journal --
-- Diturunkan dari baris yang BARU SAJA ditulis di atas, bukan dihitung
-- ulang - jadi kedua tabel dijamin tidak mungkin beda.
INSERT INTO tbl_memorial_journal
    (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date,
     buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
     keterangan, status, create_by, create_date, profit_center)
SELECT j.no_journal, j.tgl_journal, 'CMJ021', j.no_coa, j.no_costcenter,
       '-', '', '-', '-', 'IDR', 1,
       j.debit, j.credit, j.debit_idr, j.credit_idr,
       j.keterangan, 'Post', j.create_by, j.create_date, j.profit_center
  FROM tbl_list_journal j
  JOIN fxap_dok d ON d.no_journal = j.no_journal
 WHERE j.no_journal LIKE 'FX-AP/%'
   AND j.status <> 'Cancel'
 ORDER BY j.tgl_journal, j.no_journal, j.no_coa;


-- Ganti baris ini jadi ROLLBACK; kalau cuma mau mencoba tanpa menyimpan.
COMMIT;


-- =====================================================================
-- PEMERIKSAAN SESUDAH MENULIS
-- =====================================================================

-- (a) tiap dokumen WAJIB seimbang - query ini HARUS kosong.
SELECT j.no_journal, ROUND(SUM(j.debit_idr) - SUM(j.credit_idr), 2) AS tidak_seimbang
  FROM tbl_list_journal j
  JOIN fxap_dok d ON d.no_journal = j.no_journal
 WHERE j.no_journal LIKE 'FX-AP/%' AND j.status <> 'Cancel'
 GROUP BY j.no_journal
HAVING tidak_seimbang <> 0;

-- (b) jumlah baris di dua tabel WAJIB sama - query ini HARUS kosong.
SELECT d.no_journal,
       (SELECT COUNT(*) FROM tbl_list_journal j
         WHERE j.no_journal = d.no_journal AND j.status <> 'Cancel') AS baris_jurnal,
       (SELECT COUNT(*) FROM tbl_memorial_journal m
         WHERE m.no_mj = d.no_journal AND m.status <> 'Cancel')      AS baris_memorial
  FROM fxap_dok d
HAVING baris_jurnal <> baris_memorial;

-- (c) rekap hasil
SELECT COUNT(DISTINCT j.no_journal) AS dokumen,
       COUNT(*)                     AS baris,
       ROUND(SUM(j.debit_idr), 2)   AS total_debit,
       ROUND(SUM(j.credit_idr), 2)  AS total_credit
  FROM tbl_list_journal j
  JOIN fxap_dok d ON d.no_journal = j.no_journal
 WHERE j.no_journal LIKE 'FX-AP/%' AND j.status <> 'Cancel';
