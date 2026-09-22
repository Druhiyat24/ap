-- ============================================================================
-- Rapikan b_reportbank yang kategori Cash Flow-nya masih tertinggal versi lama.
-- Penyebabnya: halaman EDIT Bank In/Bank Out dulu hanya memperbarui tabel header
-- (b_bankout_h / tbl_bankin_arcollection), tidak b_reportbank. Sudah diperbaiki
-- di kode; SQL ini membetulkan baris yang sudah telanjur berbeda.
-- ============================================================================

-- LANGKAH 1 - lihat dulu yang akan berubah (Bank Out)
SELECT h.no_bankout, h.bankout_date, h.status,
       h.id_cash_flow AS kategori_dokumen, r.id_cash_flow AS kategori_laporan
FROM b_bankout_h h
INNER JOIN b_reportbank r ON r.no_doc = h.no_bankout
WHERE h.status <> 'Cancel'
  AND COALESCE(h.id_cash_flow,0) <> COALESCE(r.id_cash_flow,0);

-- LANGKAH 2 - lihat dulu yang akan berubah (Bank In)
SELECT a.doc_num, a.date, a.status,
       a.id_cash_flow AS kategori_dokumen, r.id_cash_flow AS kategori_laporan
FROM tbl_bankin_arcollection a
INNER JOIN b_reportbank r ON r.no_doc = a.doc_num
WHERE a.status <> 'Cancel'
  AND COALESCE(a.id_cash_flow,0) <> COALESCE(r.id_cash_flow,0);

-- LANGKAH 3 - perbaiki (Bank Out)
UPDATE b_reportbank r
INNER JOIN b_bankout_h h ON h.no_bankout = r.no_doc
SET r.id_cash_flow = h.id_cash_flow
WHERE h.status <> 'Cancel'
  AND COALESCE(h.id_cash_flow,0) <> COALESCE(r.id_cash_flow,0);

-- LANGKAH 4 - perbaiki (Bank In)
UPDATE b_reportbank r
INNER JOIN tbl_bankin_arcollection a ON a.doc_num = r.no_doc
SET r.id_cash_flow = a.id_cash_flow
WHERE a.status <> 'Cancel'
  AND COALESCE(a.id_cash_flow,0) <> COALESCE(r.id_cash_flow,0);

-- LANGKAH 5 - cek hasil, LANGKAH 1 & 2 harus kosong
