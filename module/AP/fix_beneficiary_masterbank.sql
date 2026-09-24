-- ===========================================================================
-- b_masterbank: lengkapi beneficiary_name yang masih NULL
--
-- Dua rekening perusahaan yang baru ditambahkan (Mandiri & BRI) belum diisi
-- beneficiary_name-nya, sedangkan seluruh rekening lain memakai
-- "PT Nirwana Alabare Garment".
--
-- Kodenya sendiri SUDAH diperbaiki (pdf_payvoucher.php memakai CONCAT_WS
-- sehingga bagian yang kosong dilewati, bukan membuat seluruh teks hilang),
-- jadi To Account di PDF sudah tampil sebagai "BANK ... <nomor rekening>"
-- meski skrip ini belum dijalankan. Menjalankannya membuat nama penerima
-- ikut tampil, persis seperti rekening lainnya.
--
-- Baris "KAS KECIL PABRIK" (bank_account = '-') SENGAJA dibiarkan: itu
-- penanda kas, bukan rekening bank, dan statusnya Deactive.
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM - harus keluar 3 baris (2 rekening bank + KAS KECIL)
SELECT bank_account, bank_name, curr, status, beneficiary_name
FROM b_masterbank
WHERE beneficiary_name IS NULL OR TRIM(beneficiary_name) = '';

-- Isi nama penerima untuk 2 rekening perusahaan. Harus mengubah TEPAT 2 baris.
UPDATE b_masterbank
SET beneficiary_name = 'PT Nirwana Alabare Garment'
WHERE bank_account IN ('130-0002077777', '013201001623307')
  AND (beneficiary_name IS NULL OR TRIM(beneficiary_name) = '');

-- SESUDAH - hanya boleh tersisa baris KAS KECIL PABRIK
SELECT bank_account, bank_name, curr, status, beneficiary_name
FROM b_masterbank
WHERE beneficiary_name IS NULL OR TRIM(beneficiary_name) = '';
