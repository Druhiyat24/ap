-- ============================================================================
-- PERBAIKAN DATA: header IR yang salah status akibat regresi TATP
-- ============================================================================
-- PENYEBAB
--   Commit 165efa8 (2026-09-04 17:27) mengubah kondisi update status header di
--   insert_trf_fintoacc.php menjadi:
--       if ($kode_trans === 'TFTA' || $kode_trans === 'TATP') { ... }
--   sehingga cabang "elseif ($kode_trans == 'TATP')" TIDAK PERNAH TERCAPAI.
--   Akibatnya transfer Acc->Pch (TATP) menulis header dengan nilai milik TFTA:
--       status    -> 'Post Fin To Acc'   (seharusnya 'Post Acc To Pch')
--       tfta_by   -> user TATP           (seharusnya tatp_by)
--       tfta_date -> tanggal TATP        (seharusnya tatp_date)
--   Dokumen jadi TIDAK MUNCUL di form_approve_pch.php, karena menu itu menyaring
--   status = 'Post Acc To Pch'. Baris ir_trans_invoice_supp-nya sendiri BENAR.
--   Kode sudah diperbaiki; skrip ini membereskan baris yang terlanjur rusak.
--
-- CARA PAKAI: jalankan LANGKAH 1 dulu, periksa hasilnya, baru LANGKAH 2 & 3.
-- ============================================================================

-- LANGKAH 1 (PRATINJAU, tidak mengubah apa pun) -------------------------------
-- Tampilkan baris yang akan diperbaiki beserta nilai penggantinya.
SELECT h.doc_number,
       h.status                         AS status_sekarang,
       'Post Acc To Pch'                AS status_jadi,
       h.tfta_by                        AS tfta_by_sekarang,
       x.usr                            AS tatp_by_jadi,
       x.tgl                            AS tatp_date_jadi,
       x.punya_tfta_asli
FROM ir_invoice_supp_h h
JOIN (
    SELECT t.doc_number,
           MAX(t.tgl_trans)  AS tgl,
           MIN(t.created_by) AS usr,
           (SELECT COUNT(*) FROM ir_trans_invoice_supp f
             WHERE f.doc_number = t.doc_number AND f.nama_trans = 'TFTA') AS punya_tfta_asli
    FROM ir_trans_invoice_supp t
    WHERE t.nama_trans = 'TATP'
    GROUP BY t.doc_number
) x ON x.doc_number = h.doc_number
WHERE h.status = 'Post Fin To Acc'
  AND COALESCE(h.tatp_by,'') = ''
ORDER BY h.doc_number;

-- LANGKAH 2 (PERBAIKAN UTAMA) -------------------------------------------------
-- Kembalikan status + isi jejak TATP yang benar.
UPDATE ir_invoice_supp_h h
JOIN (
    SELECT t.doc_number, MAX(t.tgl_trans) AS tgl, MIN(t.created_by) AS usr
    FROM ir_trans_invoice_supp t
    WHERE t.nama_trans = 'TATP'
    GROUP BY t.doc_number
) x ON x.doc_number = h.doc_number
SET h.status         = 'Post Acc To Pch',
    h.tatp_by        = x.usr,
    h.tatp_date      = x.tgl,
    h.cancel_pch_by  = NULL,
    h.cancel_pch_date = NULL
WHERE h.status = 'Post Fin To Acc'
  AND COALESCE(h.tatp_by,'') = '';

-- LANGKAH 3 (BERSIHKAN JEJAK TFTA PALSU) --------------------------------------
-- Hanya untuk dokumen yang MEMANG tidak pernah lewat TFTA: tfta_by/tfta_date di
-- sana ditulis bug, bukan hasil transfer sungguhan. Dokumen yang punya baris
-- TFTA asli TIDAK disentuh supaya jejak aslinya tidak ikut terhapus.
UPDATE ir_invoice_supp_h h
SET h.tfta_by = NULL, h.tfta_date = NULL
WHERE h.status = 'Post Acc To Pch'
  AND h.tatp_by IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM ir_trans_invoice_supp f
                   WHERE f.doc_number = h.doc_number AND f.nama_trans = 'TFTA')
  AND h.tfta_by = h.tatp_by
  AND DATE(h.tfta_date) = DATE(h.tatp_date);

-- LANGKAH 4 (VERIFIKASI) ------------------------------------------------------
-- Harus 0 baris.
SELECT COUNT(*) AS sisa_rusak
FROM ir_invoice_supp_h h
JOIN ir_trans_invoice_supp t
  ON t.doc_number = h.doc_number AND t.nama_trans = 'TATP'
WHERE h.status = 'Post Fin To Acc' AND COALESCE(h.tatp_by,'') = '';

-- ============================================================================
-- LANJUTAN (2026-09-08): kalau LANGKAH 2 & 3 TIDAK sempat dijalankan
-- ============================================================================
-- Kondisi nyata di produksi: hanya kolom "status" yang diperbaiki, lalu ke-14
-- dokumen keburu di-approve sehingga status maju jadi 'Accepted Pch'. Akibatnya
-- LANGKAH 2 & 3 di atas TIDAK LAGI COCOK (kriterianya menuntut status
-- 'Post Fin To Acc' / 'Post Acc To Pch'), sementara dua kolom jejak masih salah:
--   * tatp_by / tatp_date  : KOSONG, padahal transfer Acc->Pch benar terjadi
--   * tfta_by / tfta_date  : terisi user+tanggal TATP, padahal transfer Fin->Acc
--                            TIDAK PERNAH ada
-- Keduanya tampil ke user lewat popup pelacakan (ajax_ir_cell_detail.php)
-- sebagai "Dikonfirmasi Oleh" + tanggal, jadi perlu dibereskan.
--
-- CAKUPAN SENGAJA DIIKAT ke batch TATP/NAG/0926/01062. Alasannya: di produksi
-- ada 23 dokumen ber-tatp_by kosong, tapi hanya 14 yang korban regresi ini -
-- 9 sisanya data lama yang tidak boleh ikut disentuh.

-- LANGKAH 5 (PRATINJAU) -------------------------------------------------------
SELECT h.doc_number, h.status,
       h.tatp_by AS tatp_by_skrg, x.usr AS tatp_by_jadi, x.tgl AS tatp_date_jadi,
       h.tfta_by AS tfta_by_skrg, 'NULL' AS tfta_by_jadi
FROM ir_invoice_supp_h h
JOIN (SELECT t.doc_number, MAX(t.tgl_trans) AS tgl, MIN(t.created_by) AS usr
      FROM ir_trans_invoice_supp t
      WHERE t.no_trans = 'TATP/NAG/0926/01062'
      GROUP BY t.doc_number) x ON x.doc_number = h.doc_number
ORDER BY h.doc_number;

-- LANGKAH 6 (ISI JEJAK TATP YANG BENAR) ---------------------------------------
UPDATE ir_invoice_supp_h h
JOIN (SELECT t.doc_number, MAX(t.tgl_trans) AS tgl, MIN(t.created_by) AS usr
      FROM ir_trans_invoice_supp t
      WHERE t.no_trans = 'TATP/NAG/0926/01062'
      GROUP BY t.doc_number) x ON x.doc_number = h.doc_number
SET h.tatp_by = x.usr, h.tatp_date = x.tgl
WHERE COALESCE(h.tatp_by,'') = '';

-- LANGKAH 7 (HAPUS JEJAK TFTA PALSU) ------------------------------------------
-- Hanya dokumen yang memang TIDAK punya baris transfer TFTA sama sekali.
UPDATE ir_invoice_supp_h h
JOIN (SELECT DISTINCT t.doc_number FROM ir_trans_invoice_supp t
      WHERE t.no_trans = 'TATP/NAG/0926/01062') x ON x.doc_number = h.doc_number
SET h.tfta_by = NULL, h.tfta_date = NULL
WHERE NOT EXISTS (SELECT 1 FROM ir_trans_invoice_supp f
                  WHERE f.doc_number = h.doc_number AND f.nama_trans = 'TFTA');

-- LANGKAH 8 (VERIFIKASI) ------------------------------------------------------
-- Harus: 14 baris, tatp_by terisi, tfta_by NULL.
SELECT h.doc_number, h.status, h.tatp_by, h.tatp_date, h.tfta_by
FROM ir_invoice_supp_h h
JOIN (SELECT DISTINCT t.doc_number FROM ir_trans_invoice_supp t
      WHERE t.no_trans = 'TATP/NAG/0926/01062') x ON x.doc_number = h.doc_number
ORDER BY h.doc_number;
