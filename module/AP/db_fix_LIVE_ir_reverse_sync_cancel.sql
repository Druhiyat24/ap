-- ============================================================================
-- Selaraskan IR yang di-cancel lewat menu Reverse (fil_kode IR).
-- Dulu insert_reverse_ir.php hanya mengubah ir_invoice_supp_h, sehingga daftar
-- Invoice Received menampilkan "Cancel" padahal ir_kontrabon_h masih "Draft".
-- Akibatnya No Invoice & BPB dokumen itu ditolak "already used", dan No Reff-nya
-- tetap Used. Contoh keluhan: IR/NAG/2026/10/06160, invoice CO-26101980.
--
-- Jalankan LANGKAH 1 dulu dan periksa hasilnya. Dokumen yang kolom pv_aktif-nya
-- terisi TIDAK diubah oleh skrip ini; putuskan satu per satu.
-- ============================================================================

-- LANGKAH 1: PRATINJAU (hanya SELECT)
SELECT k.doc_number, k.nama_supp, k.no_reff, k.status AS status_kontrabon,
       s.status AS status_ir, s.cancel_ir_by, s.cancel_ir_date,
       (SELECT GROUP_CONCAT(i.no_inv SEPARATOR ', ') FROM ir_kontrabon_inv i
         WHERE i.unik_code = k.unik_code) AS no_invoice,
       (SELECT GROUP_CONCAT(p.no_kbon SEPARATOR ', ') FROM kontrabon_h p
         WHERE p.ir_number = k.doc_number AND p.status <> 'Cancel') AS pv_aktif
FROM ir_kontrabon_h k
JOIN ir_invoice_supp_h s ON s.doc_number = k.doc_number
WHERE s.status = 'Cancel' AND (k.status IS NULL OR k.status <> 'Cancel')
ORDER BY s.cancel_ir_date DESC;

-- LANGKAH 2: PERBAIKAN
START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_ir_sync;
CREATE TEMPORARY TABLE tmp_ir_sync AS
SELECT k.doc_number, k.unik_code, k.no_reff, s.cancel_ir_by, s.cancel_ir_date
FROM ir_kontrabon_h k
JOIN ir_invoice_supp_h s ON s.doc_number = k.doc_number
WHERE s.status = 'Cancel' AND (k.status IS NULL OR k.status <> 'Cancel')
  AND NOT EXISTS (SELECT 1 FROM kontrabon_h p
                   WHERE p.ir_number = k.doc_number AND p.status <> 'Cancel');

SELECT COUNT(*) AS jumlah_dokumen_diperbaiki FROM tmp_ir_sync;

UPDATE ir_kontrabon_h k
JOIN tmp_ir_sync t ON t.doc_number = k.doc_number
SET k.status = 'Cancel', k.cancel_user = t.cancel_ir_by, k.cancel_date = t.cancel_ir_date;

UPDATE ir_invoice_supp i
JOIN tmp_ir_sync t ON t.doc_number = i.doc_number
SET i.status = 'Cancel';

-- No Reff dilepas hanya bila tidak dipakai dokumen aktif lain.
UPDATE ir_kontrabon_ref r
JOIN tmp_ir_sync t ON t.no_reff = r.ref_number
SET r.status = 'Available'
WHERE r.status = 'Used'
  AND NOT EXISTS (SELECT 1 FROM ir_kontrabon_h k2
                   WHERE k2.no_reff = r.ref_number
                     AND (k2.status IS NULL OR k2.status <> 'Cancel'));

UPDATE bpb_new n
JOIN ir_kontrabon_bpb b ON b.no_bpb = n.no_bpb
JOIN tmp_ir_sync t ON t.unik_code = b.unik_code AND t.doc_number = n.upt_dok_inv
SET n.upt_dok_inv = NULL, n.upt_no_inv = NULL, n.upt_tgl_inv = NULL,
    n.upt_no_faktur = NULL, n.upt_tgl_faktur = NULL;

-- bpb_knitting tidak punya penanda dokumen, jadi BPB yang sudah dipakai IR aktif lain dilewati.
UPDATE bpb_knitting n
JOIN ir_kontrabon_bpb b ON b.no_bpb = n.no_bpb
JOIN tmp_ir_sync t ON t.unik_code = b.unik_code
SET n.no_invoice = NULL, n.tgl_invoice = NULL, n.no_faktur = NULL, n.tgl_faktur = NULL
WHERE NOT EXISTS (SELECT 1 FROM ir_kontrabon_bpb b2
                   JOIN ir_kontrabon_h k2 ON k2.unik_code = b2.unik_code
                   WHERE b2.no_bpb = n.no_bpb
                     AND (k2.status IS NULL OR k2.status <> 'Cancel'));

COMMIT;
DROP TEMPORARY TABLE IF EXISTS tmp_ir_sync;
