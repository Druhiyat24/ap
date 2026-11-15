-- ============================================================================
-- CEK (HANYA BACA) - kenapa No Req / No DN kosong di Other Receivable Report
-- untuk akun 1.34.05.
--
-- Contoh kasus: RQDN/NAG/1126/01122 -> BPB GEN/IN/1026/03287 (supplier MITA).
-- Semua perintah di bawah SELECT saja, tidak ada yang mengubah data.
-- Ganti dua nilai di baris SET kalau mau memeriksa dokumen lain.
-- ============================================================================
SET @req := 'RQDN/NAG/1126/01122';
SET @bpb := 'GEN/IN/1026/03287';

-- 1) Header request. Syaratnya: status BUKAN 'Cancel' dan tidak NULL.
--    Kolom no_dn di sinilah sumber kolom "No DN" di laporan.
SELECT no_req, tgl_req, status, no_dn, created_by, created_date
FROM req_dn_h WHERE no_req = @req;

-- 2) Baris item request. no_bpb harus terisi dan PERSIS sama dengan bpbno_int.
--    Kolom 'kurung' memperlihatkan spasi tersembunyi yang tidak kelihatan di layar.
SELECT no_bpb, LENGTH(no_bpb) panjang, CONCAT('[', no_bpb, ']') kurung, COUNT(*) jml_baris
FROM req_dn WHERE no_req = @req GROUP BY no_bpb;

-- 3) BPB-nya di tabel bpb (sisi kiri laporan). Kalau confirm bukan 'Y',
--    cancel = 'Y', PO-nya bukan tipe Buyer, atau tanggal BPB tidak punya kurs
--    PAJAK, barisnya memang tidak akan muncul di laporan sama sekali.
SELECT a.bpbno_int, LENGTH(a.bpbno_int) panjang, CONCAT('[', a.bpbno_int, ']') kurung,
       a.bpbdate, a.pono, a.cancel, a.confirm, phd.tipe_com,
       (SELECT COUNT(*) FROM masterrate r WHERE r.v_codecurr = 'PAJAK' AND r.tanggal = a.bpbdate) ada_kurs
FROM bpb a
LEFT JOIN po_header ph ON ph.pono = a.pono
LEFT JOIN po_header_draft phd ON phd.id = ph.id_draft
WHERE a.bpbno_int = @bpb GROUP BY a.bpbno_int;

-- 4) Nomor DN dari request itu ada / tidak di tbl_debitnote_h.
--    Kalau kolom 'ketemu' NULL, No DN di laporan pasti kosong.
SELECT h.no_dn dari_request, d.no_dn ketemu, d.tgl_dn, d.status
FROM req_dn_h h LEFT JOIN tbl_debitnote_h d ON d.no_dn = h.no_dn
WHERE h.no_req = @req;

-- 5) Apakah BPB ini dipakai di request lain juga (sebelum perbaikan,
--    hanya salah satu yang tampil di laporan).
SELECT h.no_req, h.tgl_req, h.status, h.no_dn, COUNT(*) jml_baris
FROM req_dn d INNER JOIN req_dn_h h ON h.no_req = d.no_req
WHERE TRIM(d.no_bpb) = TRIM(@bpb) GROUP BY h.no_req, h.tgl_req, h.status, h.no_dn;

-- 6) Hasil akhir: persis yang DILIHAT laporan untuk BPB ini (rumus versi BARU).
--    Kalau nomor 1-5 semua wajar tapi di sini kosong, berarti nomor BPB-nya
--    yang tidak sama persis di kedua tabel.
SELECT * FROM (
  SELECT GROUP_CONCAT(no_req ORDER BY no_req SEPARATOR ', ') no_req,
         GROUP_CONCAT(tgl_req ORDER BY no_req SEPARATOR ', ') tgl_req,
         GROUP_CONCAT(COALESCE(no_dn, '-') ORDER BY no_req SEPARATOR ', ') no_dn,
         GROUP_CONCAT(COALESCE(tgl_dn, '-') ORDER BY no_req SEPARATOR ', ') tgl_dn,
         no_bpb
  FROM (SELECT a.no_req, a.tgl_req, c.no_dn, c.tgl_dn, TRIM(b.no_bpb) no_bpb
        FROM req_dn_h a
        LEFT JOIN req_dn b ON b.no_req = a.no_req
        LEFT JOIN tbl_debitnote_h c ON c.no_dn = a.no_dn
        WHERE COALESCE(a.status, '') != 'Cancel' AND b.no_bpb IS NOT NULL
          AND TRIM(b.no_bpb) NOT IN ('', '-')
        GROUP BY TRIM(b.no_bpb), a.no_req) a
  GROUP BY no_bpb) f
WHERE f.no_bpb = TRIM(@bpb);
