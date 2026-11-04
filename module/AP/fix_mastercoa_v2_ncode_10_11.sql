-- =============================================================================
-- fix_mastercoa_v2_ncode_10_11.sql
-- Basis data : signalbit_erp (produksi)
-- Tabel      : mastercoa_v2  (4 baris di-UPDATE, tidak ada baris baru)
-- Dibuat     : 4 November 2026
--
-- MASALAH
--   Simpan PV-AP gagal dengan pesan "COA Utang Usaha tidak ditemukan di master
--   COA". Contoh yang dilaporkan: BPB GEN/IN/1026/03321 (27 Okt 2026, supplier
--   ONLINE SHOP id 660), item 69608 / SPCMAC19 "POMPA SUBMERSIBLE DRAKOS 3 INCH"
--   dengan mattype = 'N', matclass = '-', n_code_category = '10'.
--
-- SEBAB
--   Tabel pemetaan jurnal BPB `ap_mapping_coa_jurnal` SUDAH memuat N-Code 10
--   (INSTALASI AIR) dan 11 (INSTALASI LISTRIK), tetapi `mastercoa_v2` - yang
--   dipakai jalur PV/kontrabon - masih berhenti di N-Code 9. Akibatnya
--   pencarian COA di insertkbon_* tidak mendapat baris sama sekali, lalu
--   penjaga yang dipasang 28 Sep 2026 membatalkan seluruh penyimpanan.
--   Itu perilaku yang benar: sebelum penjaga itu ada, COA-nya tersimpan
--   KOSONG tanpa peringatan.
--
--   Jadi ini bukan master yang salah, melainkan master yang belum disusulkan.
--
-- YANG DIPERBAIKI
--   Baris LAIN-LAIN untuk mattype 'N' diperluas dari '2,5,6,7,8,9' menjadi
--   '2,5,6,7,8,9,10,11', mengikuti pasangan yang sudah ada di
--   ap_mapping_coa_jurnal (N-Code 10 dan 11 -> GR/IR LAIN-LAIN):
--
--     id   COA       Pihak            inv_type
--     88   2.10.99   Third Party      bpb_credit   (kaki DEBIT jurnal PV: balik GR/IR)
--     96   2.11.99   Third Party      kbn_credit   (kaki KREDIT jurnal PV: utang usaha)
--     104  2.12.99   Related Party    bpb_credit
--     112  2.13.99   Related Party    kbn_credit
--
--   Memperluas daftar dipilih, bukan menambah baris baru, supaya ke-45 kolom
--   kategori laporan keuangan (ind_categori1..5, id_ctg1..5, dst) ikut apa
--   adanya dan tidak ada risiko salah salin.
--
--   KEDUA inv_type memang diperlukan. Di insertkbon_core.php ada dua pencarian:
--   kbn_credit mengisi kaki kredit (utang usaha) dan bpb_credit mengisi kaki
--   debit (balik GR/IR). Yang bpb_credit TIDAK punya penjaga - kalau hanya
--   kbn_credit yang ditambal, simpan akan berhasil tetapi kaki debitnya
--   tersimpan dengan COA kosong tanpa bunyi.
--
-- YANG SENGAJA TIDAK DISENTUH
--   inv_type 'bpb_debit' di mastercoa_v2. Sudah ditelusuri ke seluruh repo:
--   tidak ada satu pun kode yang membacanya (sisi debit BPB diambil dari
--   ap_mapping_coa_jurnal, yang sudah memuat 10 = 1.60.22 INSTALASI AIR dan
--   11 = 1.60.21 INSTALASI LISTRIK). Menambahnya hanya akan jadi data mati.
--
-- CATATAN PENTING - masih ada satu kelemahan yang BELUM diperbaiki di sini
--   Pencarian COA memakai `n_code_category LIKE '%<nilai>%'` + `Limit 1` tanpa
--   ORDER BY. Karena kolomnya harus MENGANDUNG nilai yang dicari, sesudah
--   perubahan ini pencarian N-Code '1' cocok ke DUA baris: baris ATK ('1') dan
--   baris LAIN-LAIN (karena mengandung "10"). Hari ini hasilnya masih BENAR
--   (tetap dapat ATK) semata-mata karena baris ATK ber-id lebih kecil
--   (84/92/100/108) sehingga ditemukan lebih dulu saat tabel dipindai urut id.
--   Itu kebetulan yang menguntungkan, bukan jaminan: kalau nanti tabel ini
--   di-OPTIMIZE, di-dump/restore dengan urutan lain, atau diberi indeks pada
--   n_code_category/inv_type, urutan pemindaiannya bisa berubah dan pembelian
--   ATK bisa diam-diam masuk ke LAIN-LAIN.
--   Karena itu langkah 4 di bawah WAJIB diperiksa sesudah dijalankan.
--   Perbaikan tuntasnya: ganti pencocokan N-Code menjadi
--   FIND_IN_SET('<nilai>', n_code_category) di 13 titik pada 8 berkas
--   insertkbon_* - sudah diuji terpisah: hasilnya sama persis untuk seluruh
--   kode 1 digit yang ada sekarang, dan kerancuan 2 digit hilang.
--
-- SIFAT
--   Aman dijalankan berulang. Jalan pertama mengubah 4 baris, jalan berikutnya
--   0 baris, karena WHERE-nya mensyaratkan nilai lama yang sudah tidak ada lagi.
--   Diuji di salinan sementara basis data lokal: jalan ke-1 = 4 baris,
--   jalan ke-2 = 0 baris, dan ke-44 kombinasi (2 inv_type x 2 pihak x N-Code
--   1..11) semuanya mendapat COA - nol kombinasi gagal.
-- =============================================================================

-- ----------------------------------------------------------------------------
-- 1) SEBELUM - catat keadaan awal (harus 4 baris, semuanya '2,5,6,7,8,9')
-- ----------------------------------------------------------------------------
SELECT 'SEBELUM' AS tahap, id, no_coa, nama_coa, cus_ctg, inv_type, n_code_category
FROM mastercoa_v2
WHERE mattype = 'N' AND matclass = '-'
  AND inv_type IN ('kbn_credit','bpb_credit')
  AND n_code_category IN ('2,5,6,7,8,9','2,5,6,7,8,9,10,11')
ORDER BY id;

-- ----------------------------------------------------------------------------
-- 2) PERBAIKAN - perluas daftar N-Code pada baris LAIN-LAIN mattype N
--    Jalan pertama: 4 baris. Jalan berikutnya: 0 baris.
-- ----------------------------------------------------------------------------
UPDATE mastercoa_v2
SET n_code_category = '2,5,6,7,8,9,10,11'
WHERE mattype = 'N'
  AND matclass = '-'
  AND inv_type IN ('kbn_credit','bpb_credit')
  AND n_code_category = '2,5,6,7,8,9';

-- ----------------------------------------------------------------------------
-- 3) SESUDAH - harus 4 baris, semuanya '2,5,6,7,8,9,10,11'
-- ----------------------------------------------------------------------------
SELECT 'SESUDAH' AS tahap, id, no_coa, nama_coa, cus_ctg, inv_type, n_code_category
FROM mastercoa_v2
WHERE mattype = 'N' AND matclass = '-'
  AND inv_type IN ('kbn_credit','bpb_credit')
  AND n_code_category IN ('2,5,6,7,8,9','2,5,6,7,8,9,10,11')
ORDER BY id;

-- ----------------------------------------------------------------------------
-- 4) PENJAGA - 44 baris, tiap kombinasi harus dapat COA.
--    Kolom "dipakai" meniru persis kode: LIKE + Limit 1 tanpa ORDER BY.
--    Yang harus diperiksa:
--      a. kolom "dipakai" TIDAK BOLEH ada yang NULL/kosong;
--      b. baris n_code = 1 harus tetap menunjuk COA ATK
--         (2.10.04 / 2.11.04 / 2.12.04 / 2.13.04), BUKAN yang berakhiran .99.
--    Kolom jml_kandidat = 2 pada n_code 1 memang diharapkan - lihat
--    CATATAN PENTING di atas.
-- ----------------------------------------------------------------------------
SELECT  k.inv_type,
        k.cus_ctg,
        k.nc AS n_code,
        ( SELECT c.no_coa FROM mastercoa_v2 c
          WHERE c.cus_ctg LIKE CONCAT('%', k.cus_ctg, '%')
            AND c.mattype LIKE '%N%'
            AND c.matclass LIKE '%-%'
            AND c.n_code_category LIKE CONCAT('%', k.nc, '%')
            AND c.inv_type LIKE CONCAT('%', k.inv_type, '%')
          LIMIT 1 ) AS dipakai,
        ( SELECT COUNT(*) FROM mastercoa_v2 c
          WHERE c.cus_ctg LIKE CONCAT('%', k.cus_ctg, '%')
            AND c.mattype LIKE '%N%'
            AND c.matclass LIKE '%-%'
            AND c.n_code_category LIKE CONCAT('%', k.nc, '%')
            AND c.inv_type LIKE CONCAT('%', k.inv_type, '%') ) AS jml_kandidat
FROM (
    SELECT t.inv_type, p.cus_ctg, n.nc
    FROM       (SELECT 'kbn_credit' AS inv_type UNION ALL SELECT 'bpb_credit') t
    CROSS JOIN (SELECT 'Third' AS cus_ctg UNION ALL SELECT 'Related') p
    CROSS JOIN (SELECT '1' AS nc UNION ALL SELECT '2' UNION ALL SELECT '3'
                UNION ALL SELECT '4' UNION ALL SELECT '5' UNION ALL SELECT '6'
                UNION ALL SELECT '7' UNION ALL SELECT '8' UNION ALL SELECT '9'
                UNION ALL SELECT '10' UNION ALL SELECT '11') n
) k
ORDER BY k.inv_type, k.cus_ctg, CAST(k.nc AS UNSIGNED);

-- ----------------------------------------------------------------------------
-- 5) Sesudah berhasil: simpan ulang PV untuk BPB GEN/IN/1026/03321.
--    Tidak ada data transaksi yang perlu dibetulkan - penjaga membatalkan
--    seluruh penyimpanan, jadi tidak ada dokumen setengah jadi yang tertinggal.
-- ----------------------------------------------------------------------------

-- CATATAN TERPISAH (bukan bagian berkas ini, perlu keputusan Akunting):
--   Masih ada 1 item ber-n_code_category = '0' di masteritem. Angka 0 TIDAK ada
--   di ap_mapping_coa_jurnal MAUPUN mastercoa_v2, jadi kemungkinan besar itu
--   salah isi pada master item - bukan kategori yang perlu didaftarkan.
