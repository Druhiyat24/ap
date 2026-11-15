-- ============================================================================
-- PERBAIKAN DATA - nomor BPB kemasukan karakter TAB
--
-- Sebab: alat pindai (scanner) menyelipkan TAB di ujung nomor BPB waktu
-- Request Debit Note dibuat. Di layar tidak kelihatan (HTML menelan tab),
-- tapi di basis data nomornya jadi 18 karakter, bukan 17. MySQL hanya
-- mengabaikan SPASI di belakang saat membandingkan - TAB tidak - sehingga
-- nomor itu tidak pernah ketemu dengan bpb.bpbno_int. Akibatnya:
--   1. Other Receivable Report: kolom No Req / Req Date / No DN / DN Date
--      kosong untuk BPB tsb.
--   2. LEBIH PENTING: tbl_debitnote_det.nm_memo ikut ketularan tab, jadi
--      nanti saat DN-nya dialokasi, pengurangnya TIDAK akan menempel ke
--      baris BPB itu dan saldonya menggantung selamanya.
--
-- Per 12 Nov 2026 di produksi: 3 baris req_dn + 3 baris tbl_debitnote_det,
-- semuanya milik RQDN/NAG/1126/01122 / DN/NAG/1126/1016 (BPB
-- GEN/IN/1026/03287, supplier MITA). DN-nya BELUM dialokasi, jadi belum ada
-- angka yang terlanjur salah.
--
-- Sisi kode sudah dibereskan (tidak perlu dijalankan di sini):
--   - insert_req_dn_bulk.php / update_req_dn_bulk.php / insert_req_dn.php
--     membuang spasi/tab/enter dari no_bpb sebelum disimpan.
--   - other_receivable_report.php & ekspor_or_report_material.php
--     membersihkan nomornya dulu sebelum dicocokkan.
-- ============================================================================

-- 1) LIHAT DULU - harus 3 baris, kolom 'sesudah' sudah rapi 17 karakter.
SELECT id, no_req, CONCAT('[', no_bpb, ']') sebelum,
       CONCAT('[', REPLACE(REPLACE(REPLACE(TRIM(no_bpb), CHAR(9), ''), CHAR(13), ''), CHAR(10), ''), ']') sesudah
FROM req_dn WHERE no_bpb REGEXP '[[:space:]]';

-- 2) LIHAT DULU - sisi Debit Note, harus 3 baris juga.
--    Dibatasi yang mengandung '/' supaya nomor memo biasa tidak ikut tersentuh.
SELECT id, no_dn, CONCAT('[', nm_memo, ']') sebelum,
       CONCAT('[', REPLACE(REPLACE(REPLACE(TRIM(nm_memo), CHAR(9), ''), CHAR(13), ''), CHAR(10), ''), ']') sesudah
FROM tbl_debitnote_det WHERE nm_memo REGEXP '[[:space:]]' AND nm_memo LIKE '%/%';

-- 3) PERBAIKI.
UPDATE req_dn
SET no_bpb = REPLACE(REPLACE(REPLACE(TRIM(no_bpb), CHAR(9), ''), CHAR(13), ''), CHAR(10), '')
WHERE no_bpb REGEXP '[[:space:]]';

UPDATE tbl_debitnote_det
SET nm_memo = REPLACE(REPLACE(REPLACE(TRIM(nm_memo), CHAR(9), ''), CHAR(13), ''), CHAR(10), '')
WHERE nm_memo REGEXP '[[:space:]]' AND nm_memo LIKE '%/%';

-- 4) PASTIKAN - dua-duanya harus 0.
SELECT 'req_dn' tabel, COUNT(*) sisa FROM req_dn WHERE no_bpb REGEXP '[[:space:]]'
UNION ALL
SELECT 'tbl_debitnote_det', COUNT(*) FROM tbl_debitnote_det
WHERE nm_memo REGEXP '[[:space:]]' AND nm_memo LIKE '%/%';

-- 5) CATATAN: ir_kontrabon_inv.no_inv juga ada 30 baris berspasi, TAPI itu
--    WAJAR - nomor invoice supplier memang kadang pakai spasi, mis.
--    "INV 2610127". JANGAN ikut dibersihkan.
