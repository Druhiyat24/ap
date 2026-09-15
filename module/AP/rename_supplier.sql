-- ============================================================================
-- GANTI NAMA SUPPLIER di SELURUH TABEL yang menyimpan nama sbg TEKS.
-- Dibuat 2026-09-14 dgn memindai information_schema signalbit_erp:
-- semua kolom bertipe teks yang namanya mengandung 'supp', lalu diuji
-- apakah isinya benar-benar cocok dgn mastersupplier.Supplier.
-- Kolom id (id_supplier, supplier_id, kode) TIDAK ikut - itu tidak berubah.
--
-- CARA PAKAI: isi @old & @new, jalankan PART 1 dulu utk lihat mana yang kena,
-- baru jalankan PART 2 (boleh dihapus barisnya utk tabel yang hitungannya 0).
-- BACKUP DULU. Jalankan di luar jam sibuk: hampir semua kolom ini TIDAK
-- berindeks, jadi tiap UPDATE = full table scan.
-- ============================================================================
SET @old = 'NAMA LAMA PERSIS';
SET @new = 'NAMA BARU';

-- ============================== PART 1: SCAN ===============================
-- Lihat tabel mana yang benar-benar memuat @old (0 = tidak perlu di-update).
SELECT * FROM (
  SELECT 'upload_tpb.SUPPLIER'                  col, COUNT(*) n FROM `upload_tpb` WHERE `SUPPLIER` = @old
  UNION ALL
  SELECT 'tbl_list_journal.supplier'            col, COUNT(*) n FROM `tbl_list_journal` WHERE `supplier` = @old
  UNION ALL
  SELECT 'whs_sa_fabric_copy.supplier'          col, COUNT(*) n FROM `whs_sa_fabric_copy` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tmp_jrn_supplier_map.supplier'        col, COUNT(*) n FROM `tmp_jrn_supplier_map` WHERE `supplier` = @old
  UNION ALL
  SELECT 'whs_barcode_in.supplier'              col, COUNT(*) n FROM `whs_barcode_in` WHERE `supplier` = @old
  UNION ALL
  SELECT 'whs_data_barcode.supplier'            col, COUNT(*) n FROM `whs_data_barcode` WHERE `supplier` = @old
  UNION ALL
  SELECT 'whs_saldo_awal_nilai_persediaan.supplier' col, COUNT(*) n FROM `whs_saldo_awal_nilai_persediaan` WHERE `supplier` = @old
  UNION ALL
  SELECT 'bpb_new.supplier'                     col, COUNT(*) n FROM `bpb_new` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_list_journal_cancel.supplier'     col, COUNT(*) n FROM `tbl_list_journal_cancel` WHERE `supplier` = @old
  UNION ALL
  SELECT 'whs_mut_lokasi.namasupp'              col, COUNT(*) n FROM `whs_mut_lokasi` WHERE `namasupp` = @old
  UNION ALL
  SELECT 'bpb_faktur_inv.nama_supp'             col, COUNT(*) n FROM `bpb_faktur_inv` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'kartu_hutang.nama_supp'               col, COUNT(*) n FROM `kartu_hutang` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'status.supp'                          col, COUNT(*) n FROM `status` WHERE `supp` = @old
  UNION ALL
  SELECT 'kontrabon.nama_supp'                  col, COUNT(*) n FROM `kontrabon` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'upload_tpb_hist.SUPPLIER'             col, COUNT(*) n FROM `upload_tpb_hist` WHERE `SUPPLIER` = @old
  UNION ALL
  SELECT 'ir_trans_bpb.nama_supp'               col, COUNT(*) n FROM `ir_trans_bpb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'b_bankout_h.nama_supp'                col, COUNT(*) n FROM `b_bankout_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'dsb_ap_purchase.nama_supp'            col, COUNT(*) n FROM `dsb_ap_purchase` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'whs_qc_insp_det.fabric_supp'          col, COUNT(*) n FROM `whs_qc_insp_det` WHERE `fabric_supp` = @old
  UNION ALL
  SELECT 'c_petty_cashout_h.nama_supp'          col, COUNT(*) n FROM `c_petty_cashout_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ir_trans_invoice_supp.nama_supp'      col, COUNT(*) n FROM `ir_trans_invoice_supp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'kontrabon_h.nama_supp'                col, COUNT(*) n FROM `kontrabon_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'potongan.nama_supp'                   col, COUNT(*) n FROM `potongan` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'tbl_debitnote_det.supplier'           col, COUNT(*) n FROM `tbl_debitnote_det` WHERE `supplier` = @old
  UNION ALL
  SELECT 'list_payment.nama_supp'               col, COUNT(*) n FROM `list_payment` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'whs_inmaterial_fabric.supplier'       col, COUNT(*) n FROM `whs_inmaterial_fabric` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_pv_h.nama_supp'                   col, COUNT(*) n FROM `tbl_pv_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ir_invoice_supp_h.nama_supp'          col, COUNT(*) n FROM `ir_invoice_supp_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'bppb_new.supplier'                    col, COUNT(*) n FROM `bppb_new` WHERE `supplier` = @old
  UNION ALL
  SELECT 'rpt_ap_bpb.nama_supp'                 col, COUNT(*) n FROM `rpt_ap_bpb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'mastersupplier.Supplier'              col, COUNT(*) n FROM `mastersupplier` WHERE `Supplier` = @old
  UNION ALL
  SELECT 'tbl_tamb_bpb2.nama_supp'              col, COUNT(*) n FROM `tbl_tamb_bpb2` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'bpb_knitting.supplier'                col, COUNT(*) n FROM `bpb_knitting` WHERE `supplier` = @old
  UNION ALL
  SELECT 'upload_standard.SUPPLIER'             col, COUNT(*) n FROM `upload_standard` WHERE `SUPPLIER` = @old
  UNION ALL
  SELECT 'ap_saldo_awal_bpb.supplier'           col, COUNT(*) n FROM `ap_saldo_awal_bpb` WHERE `supplier` = @old
  UNION ALL
  SELECT 'bpb_scan_faktur_h.nama_supp'          col, COUNT(*) n FROM `bpb_scan_faktur_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'tbl_edit_pv_h.nama_supp'              col, COUNT(*) n FROM `tbl_edit_pv_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ttl_bppb.supp'                        col, COUNT(*) n FROM `ttl_bppb` WHERE `supp` = @old
  UNION ALL
  SELECT 'tbl_tamb_bpb.nama_supp'               col, COUNT(*) n FROM `tbl_tamb_bpb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'saldo_bpb_ap.nama_supp'               col, COUNT(*) n FROM `saldo_bpb_ap` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ap_report_saldo_bpb.supplier'         col, COUNT(*) n FROM `ap_report_saldo_bpb` WHERE `supplier` = @old
  UNION ALL
  SELECT 'payment_ftr.nama_supp'                col, COUNT(*) n FROM `payment_ftr` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'dsb_ap_bpb.nama_supp'                 col, COUNT(*) n FROM `dsb_ap_bpb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'tbl_debitnote_det_edit.supplier'      col, COUNT(*) n FROM `tbl_debitnote_det_edit` WHERE `supplier` = @old
  UNION ALL
  SELECT 'req_dn_h.nama_supp'                   col, COUNT(*) n FROM `req_dn_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ftr_cbd.supp'                         col, COUNT(*) n FROM `ftr_cbd` WHERE `supp` = @old
  UNION ALL
  SELECT 'kontrabon_cbd.nama_supp'              col, COUNT(*) n FROM `kontrabon_cbd` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'pv_payment_voucher_list_det.nama_supp' col, COUNT(*) n FROM `pv_payment_voucher_list_det` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'pv_payment_list_det.nama_supp'        col, COUNT(*) n FROM `pv_payment_list_det` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'kontrabon_h_cbd.nama_supp'            col, COUNT(*) n FROM `kontrabon_h_cbd` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'dsb_ap_kbon.nama_supp'                col, COUNT(*) n FROM `dsb_ap_kbon` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'list_payment_cbd.nama_supp'           col, COUNT(*) n FROM `list_payment_cbd` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'rpt_ap_lp.nama_supp'                  col, COUNT(*) n FROM `rpt_ap_lp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'acc_saldo_awal_prepaid_tax.supplier'  col, COUNT(*) n FROM `acc_saldo_awal_prepaid_tax` WHERE `supplier` = @old
  UNION ALL
  SELECT 'upt_mastersupplier.supplier'          col, COUNT(*) n FROM `upt_mastersupplier` WHERE `supplier` = @old
  UNION ALL
  SELECT 'maintain_bpb_det.nama_supp'           col, COUNT(*) n FROM `maintain_bpb_det` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ap_saldo_payment_voucher.nama_supp'   col, COUNT(*) n FROM `ap_saldo_payment_voucher` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'rpt_ap_kbon.nama_supp'                col, COUNT(*) n FROM `rpt_ap_kbon` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'dsb_ap_lp.nama_supp'                  col, COUNT(*) n FROM `dsb_ap_lp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ap_saldo_awal_listpayment.supplier'   col, COUNT(*) n FROM `ap_saldo_awal_listpayment` WHERE `supplier` = @old
  UNION ALL
  SELECT 'kontrabon_ftr.nama_supp'              col, COUNT(*) n FROM `kontrabon_ftr` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'saldo_awal.supplier'                  col, COUNT(*) n FROM `saldo_awal` WHERE `supplier` = @old
  UNION ALL
  SELECT 'saldo_lp_ap.nama_supp'                col, COUNT(*) n FROM `saldo_lp_ap` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ir_kontrabon_bpb.supplier'            col, COUNT(*) n FROM `ir_kontrabon_bpb` WHERE `supplier` = @old
  UNION ALL
  SELECT 'invoice_view.Supplier'                col, COUNT(*) n FROM `invoice_view` WHERE `Supplier` = @old
  UNION ALL
  SELECT 'ir_kontrabon_faktur.nama_supplier'    col, COUNT(*) n FROM `ir_kontrabon_faktur` WHERE `nama_supplier` = @old
  UNION ALL
  SELECT 'saldo_awal_bpb.nama_supp'             col, COUNT(*) n FROM `saldo_awal_bpb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'tbl_ppn_saldo_awal.supplier'          col, COUNT(*) n FROM `tbl_ppn_saldo_awal` WHERE `supplier` = @old
  UNION ALL
  SELECT 'ca_adjust_input.supplier'             col, COUNT(*) n FROM `ca_adjust_input` WHERE `supplier` = @old
  UNION ALL
  SELECT 'ir_reverse.nama_supp'                 col, COUNT(*) n FROM `ir_reverse` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ir_kontrabon_h.nama_supp'             col, COUNT(*) n FROM `ir_kontrabon_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'saldo_awal_kbon.nama_supp'            col, COUNT(*) n FROM `saldo_awal_kbon` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'whs_ro_barcode_temp.supplier'         col, COUNT(*) n FROM `whs_ro_barcode_temp` WHERE `supplier` = @old
  UNION ALL
  SELECT 'ap_reverse_det.nama_supp'             col, COUNT(*) n FROM `ap_reverse_det` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'pa_saldo_awal.supp'                   col, COUNT(*) n FROM `pa_saldo_awal` WHERE `supp` = @old
  UNION ALL
  SELECT 'ap_saldo_awal_kontrabon.supplier'     col, COUNT(*) n FROM `ap_saldo_awal_kontrabon` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_return_report.supplier'           col, COUNT(*) n FROM `tbl_return_report` WHERE `supplier` = @old
  UNION ALL
  SELECT 'saldo_kbon_ap.nama_supp'              col, COUNT(*) n FROM `saldo_kbon_ap` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'payment_ftrcbd.nama_supp'             col, COUNT(*) n FROM `payment_ftrcbd` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ftr_dp.supp'                          col, COUNT(*) n FROM `ftr_dp` WHERE `supp` = @old
  UNION ALL
  SELECT 'whs_inmaterial_barcode_ri_temp.nama_supplier' col, COUNT(*) n FROM `whs_inmaterial_barcode_ri_temp` WHERE `nama_supplier` = @old
  UNION ALL
  SELECT 'kontrabon_h_dp.nama_supp'             col, COUNT(*) n FROM `kontrabon_h_dp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'kontrabon_dp.nama_supp'               col, COUNT(*) n FROM `kontrabon_dp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'list_payment_dp.nama_supp'            col, COUNT(*) n FROM `list_payment_dp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'pengajuan_kb.nama_supp'               col, COUNT(*) n FROM `pengajuan_kb` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'whs_saldo_awal_subcont_fabric.supplier' col, COUNT(*) n FROM `whs_saldo_awal_subcont_fabric` WHERE `supplier` = @old
  UNION ALL
  SELECT 'upload_po.supplier'                   col, COUNT(*) n FROM `upload_po` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_pv_memo_temp.supplier'            col, COUNT(*) n FROM `tbl_pv_memo_temp` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_memo_temp.supplier'               col, COUNT(*) n FROM `tbl_memo_temp` WHERE `supplier` = @old
  UNION ALL
  SELECT 'pengajuan_payment.nama_supp'          col, COUNT(*) n FROM `pengajuan_payment` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ap_mapping_supplier.supplier'         col, COUNT(*) n FROM `ap_mapping_supplier` WHERE `supplier` = @old
  UNION ALL
  SELECT 'tbl_bpb_temp.nama_supp'               col, COUNT(*) n FROM `tbl_bpb_temp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'ca_sales_group.supplier'              col, COUNT(*) n FROM `ca_sales_group` WHERE `supplier` = @old
  UNION ALL
  SELECT 'payment_ftrdp.nama_supp'              col, COUNT(*) n FROM `payment_ftrdp` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'update_bpb_fabric.nama_supp'          col, COUNT(*) n FROM `update_bpb_fabric` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'tbl_ctg_supplier.supplier'            col, COUNT(*) n FROM `tbl_ctg_supplier` WHERE `supplier` = @old
  UNION ALL
  SELECT 'sb_pv_h.nama_supp'                    col, COUNT(*) n FROM `sb_pv_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_kontrabon.nama_supp'               col, COUNT(*) n FROM `sb_kontrabon` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_potongan.nama_supp'                col, COUNT(*) n FROM `sb_potongan` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_list_payment.nama_supp'            col, COUNT(*) n FROM `sb_list_payment` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_kontrabon_h.nama_supp'             col, COUNT(*) n FROM `sb_kontrabon_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_edit_pv_h.nama_supp'               col, COUNT(*) n FROM `sb_edit_pv_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_c_petty_cashout_h.nama_supp'       col, COUNT(*) n FROM `sb_c_petty_cashout_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'sb_b_bankout_h.nama_supp'             col, COUNT(*) n FROM `sb_b_bankout_h` WHERE `nama_supp` = @old
  UNION ALL
  SELECT 'qc_inspect_list_inspect.supplier'     col, COUNT(*) n FROM `qc_inspect_list_inspect` WHERE `supplier` = @old
  UNION ALL
  SELECT 'bpb_scan_faktur_temp_h.nama_supp'     col, COUNT(*) n FROM `bpb_scan_faktur_temp_h` WHERE `nama_supp` = @old
) s WHERE n > 0 ORDER BY n DESC;

-- ============================= PART 2: UPDATE ==============================
START TRANSACTION;
UPDATE `upload_tpb` SET `SUPPLIER` = @new WHERE `SUPPLIER` = @old;             -- ~18110063 baris
UPDATE `tbl_list_journal` SET `supplier` = @new WHERE `supplier` = @old;       -- ~613637 baris
UPDATE `whs_sa_fabric_copy` SET `supplier` = @new WHERE `supplier` = @old;     -- ~499423 baris
UPDATE `tmp_jrn_supplier_map` SET `supplier` = @new WHERE `supplier` = @old;   -- ~401486 baris
UPDATE `whs_barcode_in` SET `supplier` = @new WHERE `supplier` = @old;         -- ~333475 baris
UPDATE `whs_data_barcode` SET `supplier` = @new WHERE `supplier` = @old;       -- ~328986 baris
UPDATE `whs_saldo_awal_nilai_persediaan` SET `supplier` = @new WHERE `supplier` = @old; -- ~179575 baris
UPDATE `bpb_new` SET `supplier` = @new WHERE `supplier` = @old;                -- ~97041 baris
UPDATE `tbl_list_journal_cancel` SET `supplier` = @new WHERE `supplier` = @old; -- ~71400 baris
UPDATE `whs_mut_lokasi` SET `namasupp` = @new WHERE `namasupp` = @old;         -- ~57018 baris
UPDATE `bpb_faktur_inv` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~45780 baris
UPDATE `kartu_hutang` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~45775 baris
UPDATE `status` SET `supp` = @new WHERE `supp` = @old;                         -- ~43366 baris
UPDATE `kontrabon` SET `nama_supp` = @new WHERE `nama_supp` = @old;            -- ~41995 baris
UPDATE `upload_tpb_hist` SET `SUPPLIER` = @new WHERE `SUPPLIER` = @old;        -- ~38209 baris
UPDATE `ir_trans_bpb` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~26419 baris
UPDATE `b_bankout_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~20054 baris
UPDATE `dsb_ap_purchase` SET `nama_supp` = @new WHERE `nama_supp` = @old;      -- ~18331 baris
UPDATE `whs_qc_insp_det` SET `fabric_supp` = @new WHERE `fabric_supp` = @old;  -- ~15376 baris
UPDATE `c_petty_cashout_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;    -- ~14044 baris
UPDATE `ir_trans_invoice_supp` SET `nama_supp` = @new WHERE `nama_supp` = @old; -- ~11529 baris
UPDATE `kontrabon_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~11390 baris
UPDATE `potongan` SET `nama_supp` = @new WHERE `nama_supp` = @old;             -- ~11310 baris
UPDATE `tbl_debitnote_det` SET `supplier` = @new WHERE `supplier` = @old;      -- ~11157 baris
UPDATE `list_payment` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~10592 baris
UPDATE `whs_inmaterial_fabric` SET `supplier` = @new WHERE `supplier` = @old;  -- ~7453 baris
UPDATE `tbl_pv_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;             -- ~7033 baris
UPDATE `ir_invoice_supp_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;    -- ~5402 baris
UPDATE `bppb_new` SET `supplier` = @new WHERE `supplier` = @old;               -- ~3396 baris
UPDATE `rpt_ap_bpb` SET `nama_supp` = @new WHERE `nama_supp` = @old;           -- ~2894 baris
UPDATE `mastersupplier` SET `Supplier` = @new WHERE `Supplier` = @old;         -- ~2345 baris
UPDATE `tbl_tamb_bpb2` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~2297 baris
UPDATE `bpb_knitting` SET `supplier` = @new WHERE `supplier` = @old;           -- ~2223 baris
UPDATE `upload_standard` SET `SUPPLIER` = @new WHERE `SUPPLIER` = @old;        -- ~2109 baris
UPDATE `ap_saldo_awal_bpb` SET `supplier` = @new WHERE `supplier` = @old;      -- ~1891 baris
UPDATE `bpb_scan_faktur_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;    -- ~1644 baris
UPDATE `tbl_edit_pv_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~1545 baris
UPDATE `ttl_bppb` SET `supp` = @new WHERE `supp` = @old;                       -- ~1473 baris
UPDATE `tbl_tamb_bpb` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~1420 baris
UPDATE `saldo_bpb_ap` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~1349 baris
UPDATE `ap_report_saldo_bpb` SET `supplier` = @new WHERE `supplier` = @old;    -- ~1263 baris
UPDATE `payment_ftr` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~1260 baris
UPDATE `dsb_ap_bpb` SET `nama_supp` = @new WHERE `nama_supp` = @old;           -- ~1227 baris
UPDATE `tbl_debitnote_det_edit` SET `supplier` = @new WHERE `supplier` = @old; -- ~1181 baris
UPDATE `req_dn_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;             -- ~923 baris
UPDATE `ftr_cbd` SET `supp` = @new WHERE `supp` = @old;                        -- ~715 baris
UPDATE `kontrabon_cbd` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~699 baris
UPDATE `pv_payment_voucher_list_det` SET `nama_supp` = @new WHERE `nama_supp` = @old; -- ~689 baris
UPDATE `pv_payment_list_det` SET `nama_supp` = @new WHERE `nama_supp` = @old;  -- ~686 baris
UPDATE `kontrabon_h_cbd` SET `nama_supp` = @new WHERE `nama_supp` = @old;      -- ~649 baris
UPDATE `dsb_ap_kbon` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~647 baris
UPDATE `list_payment_cbd` SET `nama_supp` = @new WHERE `nama_supp` = @old;     -- ~629 baris
UPDATE `rpt_ap_lp` SET `nama_supp` = @new WHERE `nama_supp` = @old;            -- ~570 baris
UPDATE `acc_saldo_awal_prepaid_tax` SET `supplier` = @new WHERE `supplier` = @old; -- ~554 baris
UPDATE `upt_mastersupplier` SET `supplier` = @new WHERE `supplier` = @old;     -- ~518 baris
UPDATE `maintain_bpb_det` SET `nama_supp` = @new WHERE `nama_supp` = @old;     -- ~448 baris
UPDATE `ap_saldo_payment_voucher` SET `nama_supp` = @new WHERE `nama_supp` = @old; -- ~432 baris
UPDATE `rpt_ap_kbon` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~431 baris
UPDATE `dsb_ap_lp` SET `nama_supp` = @new WHERE `nama_supp` = @old;            -- ~411 baris
UPDATE `ap_saldo_awal_listpayment` SET `supplier` = @new WHERE `supplier` = @old; -- ~311 baris
UPDATE `kontrabon_ftr` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~277 baris
UPDATE `saldo_awal` SET `supplier` = @new WHERE `supplier` = @old;             -- ~263 baris
UPDATE `saldo_lp_ap` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~234 baris
UPDATE `ir_kontrabon_bpb` SET `supplier` = @new WHERE `supplier` = @old;       -- ~231 baris
UPDATE `invoice_view` SET `Supplier` = @new WHERE `Supplier` = @old;           -- ~228 baris
UPDATE `ir_kontrabon_faktur` SET `nama_supplier` = @new WHERE `nama_supplier` = @old; -- ~211 baris
UPDATE `saldo_awal_bpb` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~193 baris
UPDATE `tbl_ppn_saldo_awal` SET `supplier` = @new WHERE `supplier` = @old;     -- ~142 baris
UPDATE `ca_adjust_input` SET `supplier` = @new WHERE `supplier` = @old;        -- ~107 baris
UPDATE `ir_reverse` SET `nama_supp` = @new WHERE `nama_supp` = @old;           -- ~97 baris
UPDATE `ir_kontrabon_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~84 baris
UPDATE `saldo_awal_kbon` SET `nama_supp` = @new WHERE `nama_supp` = @old;      -- ~66 baris
UPDATE `whs_ro_barcode_temp` SET `supplier` = @new WHERE `supplier` = @old;    -- ~64 baris
UPDATE `ap_reverse_det` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~63 baris
UPDATE `pa_saldo_awal` SET `supp` = @new WHERE `supp` = @old;                  -- ~58 baris
UPDATE `ap_saldo_awal_kontrabon` SET `supplier` = @new WHERE `supplier` = @old; -- ~56 baris
UPDATE `tbl_return_report` SET `supplier` = @new WHERE `supplier` = @old;      -- ~51 baris
UPDATE `saldo_kbon_ap` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~32 baris
UPDATE `payment_ftrcbd` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~30 baris
UPDATE `ftr_dp` SET `supp` = @new WHERE `supp` = @old;                         -- ~27 baris
UPDATE `whs_inmaterial_barcode_ri_temp` SET `nama_supplier` = @new WHERE `nama_supplier` = @old; -- ~26 baris
UPDATE `kontrabon_h_dp` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~26 baris
UPDATE `kontrabon_dp` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~26 baris
UPDATE `list_payment_dp` SET `nama_supp` = @new WHERE `nama_supp` = @old;      -- ~25 baris
UPDATE `pengajuan_kb` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~24 baris
UPDATE `whs_saldo_awal_subcont_fabric` SET `supplier` = @new WHERE `supplier` = @old; -- ~21 baris
UPDATE `upload_po` SET `supplier` = @new WHERE `supplier` = @old;              -- ~16 baris
UPDATE `tbl_pv_memo_temp` SET `supplier` = @new WHERE `supplier` = @old;       -- ~8 baris
UPDATE `tbl_memo_temp` SET `supplier` = @new WHERE `supplier` = @old;          -- ~6 baris
UPDATE `pengajuan_payment` SET `nama_supp` = @new WHERE `nama_supp` = @old;    -- ~6 baris
UPDATE `ap_mapping_supplier` SET `supplier` = @new WHERE `supplier` = @old;    -- ~6 baris
UPDATE `tbl_bpb_temp` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~5 baris
UPDATE `ca_sales_group` SET `supplier` = @new WHERE `supplier` = @old;         -- ~5 baris
UPDATE `payment_ftrdp` SET `nama_supp` = @new WHERE `nama_supp` = @old;        -- ~4 baris
UPDATE `update_bpb_fabric` SET `nama_supp` = @new WHERE `nama_supp` = @old;    -- ~3 baris
UPDATE `tbl_ctg_supplier` SET `supplier` = @new WHERE `supplier` = @old;       -- ~3 baris
UPDATE `sb_pv_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;              -- ~3 baris
UPDATE `sb_kontrabon` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~3 baris
UPDATE `sb_potongan` SET `nama_supp` = @new WHERE `nama_supp` = @old;          -- ~1 baris
UPDATE `sb_list_payment` SET `nama_supp` = @new WHERE `nama_supp` = @old;      -- ~1 baris
UPDATE `sb_kontrabon_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~1 baris
UPDATE `sb_edit_pv_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;         -- ~1 baris
UPDATE `sb_c_petty_cashout_h` SET `nama_supp` = @new WHERE `nama_supp` = @old; -- ~1 baris
UPDATE `sb_b_bankout_h` SET `nama_supp` = @new WHERE `nama_supp` = @old;       -- ~1 baris
UPDATE `qc_inspect_list_inspect` SET `supplier` = @new WHERE `supplier` = @old; -- ~1 baris
UPDATE `bpb_scan_faktur_temp_h` SET `nama_supp` = @new WHERE `nama_supp` = @old; -- ~1 baris
-- COMMIT;   -- buka komentarnya setelah hasil PART 1 dicek ulang

-- ===================== PERLU DICEK MANUAL (teks bebas) =====================
-- Kolom di bawah memuat nama supplier hasil ketik/OCR, ejaannya sering BEDA
-- dgn master, jadi WHERE = @old belum tentu kena. Cek pakai LIKE dulu.
--   bpb_scan_faktur_h.nama_supp        1.656 isi, hanya 276 cocok master (OCR faktur)
--   ir_kontrabon_faktur.nama_supplier    238 isi, hanya  16 cocok master (OCR faktur)
--   whs_qc_insp_det.fabric_supp       15.286 isi, hanya 809 cocok master (ketik bebas)
--   memo_det.inv_vendor               mayoritas nomor invoice, 161 baris berisi nama supplier
-- Teks keterangan/deskripsi jurnal & bank juga bisa memuat nama supplier
-- (mis. tbl_list_journal.keterangan 'SELISIH KURS KONTRABON <nama>').
