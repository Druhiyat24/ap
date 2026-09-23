-- ===========================================================================
-- mastersupplier.to_account_manual
-- Penanda supplier yang "To Account"-nya diisi MANUAL (bukan dipilih dari
-- dropdown rekening master_supplier_bank / b_masterbank).
--
-- Kenapa perlu:
--   Supplier instansi (kantor pajak, bea cukai, dsb) tidak punya rekening
--   tetap - yang diisi di To Account adalah KODE BILLING yang berganti tiap
--   dokumen. Untuk supplier begini form PV menampilkan isian bebas, dan
--   halaman cetak harus menampilkan isiannya apa adanya; kalau dipaksa
--   dicocokkan ke master bank hasilnya gagal dan kolom To Account tampil
--   KOSONG (kasus PL-PV/0926/00039).
--
--   Sebelumnya daftar supplier ini HARDCODE dan disalin ke 6 berkas PHP,
--   sehingga gampang ketinggalan saat ada supplier baru. Sekarang jadi data:
--   cukup set kolom ini = 1, tanpa perlu deploy ulang.
--
-- Urutan aman: jalankan ALTER + UPDATE di bawah SEBELUM/SESUDAH deploy kode -
-- dua-duanya aman. Selama kolomnya belum ada, kode otomatis memakai daftar
-- lama (lihat toccManualSuppliersFallback() di tocc_manual_suppliers.php),
-- jadi tidak ada perubahan perilaku di masa transisi.
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

ALTER TABLE mastersupplier
    ADD COLUMN to_account_manual TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = To Account diisi manual (kode billing), bukan dropdown rekening';

-- Isi awal: 3 supplier yang selama ini hardcode di PHP.
-- Harus mengubah TEPAT 3 baris.
UPDATE mastersupplier
SET to_account_manual = 1
WHERE UPPER(TRIM(Supplier)) IN (
    'KANTOR PAJAK',
    'KPPBC TMP A BANDUNG',
    'KANTOR PELAYANAN UTAMA BEA DAN CUKAI TIPE A'
);

-- Verifikasi - harus keluar 3 baris.
SELECT Id_Supplier, Supplier, to_account_manual
FROM mastersupplier
WHERE to_account_manual = 1
ORDER BY Supplier;

-- ---------------------------------------------------------------------------
-- Menambah supplier manual BARU di kemudian hari - cukup ini saja, tanpa
-- mengubah kode apa pun:
--
--   UPDATE mastersupplier SET to_account_manual = 1
--   WHERE Id_Supplier = <id supplier>;
--
-- Mengembalikan supplier ke mode dropdown rekening:
--
--   UPDATE mastersupplier SET to_account_manual = 0
--   WHERE Id_Supplier = <id supplier>;
-- ---------------------------------------------------------------------------
