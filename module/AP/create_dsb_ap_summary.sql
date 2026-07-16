-- =====================================================================
-- AP Dashboard summary: precomputed table + refresh procedure + event
--
-- Mirrors the existing get_ap_purchase_nak pattern (table + procedure +
-- EVERY 600 SECOND event) already used on this same dashboard for
-- dsb_ap_purchase, and the same combined BPB+Payment Voucher logic used
-- by payable_card_statement2.php's Summary tab (query_bpb2.php +
-- query_pv2.php), just hardcoded to CURDATE() / all suppliers instead
-- of being driven by $_REQUEST.
--
-- Run this whole file once (e.g. via phpMyAdmin or mysql CLI) against
-- signalbit_erp. It creates the table, seeds it immediately with one
-- CALL, then installs the recurring event.
-- =====================================================================

DROP TABLE IF EXISTS `dsb_ap_summary`;

CREATE TABLE `dsb_ap_summary` (
  `id`             tinyint(1) unsigned NOT NULL DEFAULT 1,
  `total`          decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Total Outstanding',
  `not_due`        decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Not Due',
  `over_due`       decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Over Due',
  `ap_group`       decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Group',
  `ap_nongroup`    decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Non Group',
  `ap_machine`     decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Machine',
  `ap_nonmachine`  decimal(20,2) DEFAULT NULL COMMENT 'Account Payable Non Machine',
  `total_usd`      decimal(20,2) DEFAULT NULL COMMENT 'Account Payable USD (foreign currency)',
  `total_usd_idr`  decimal(20,2) DEFAULT NULL COMMENT 'Account Payable USD (IDR equivalent)',
  `total_idr`      decimal(20,2) DEFAULT NULL COMMENT 'Account Payable IDR',
  `updated_at`     datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS `get_ap_dashboard_summary`;

DELIMITER $$

CREATE DEFINER=`root`@`%` PROCEDURE `get_ap_dashboard_summary`()
BEGIN
    DROP TEMPORARY TABLE IF EXISTS tmp_ap_bpb;
    DROP TEMPORARY TABLE IF EXISTS tmp_ap_pv;

    -- ===== BPB position as of today, all suppliers (= query_bpb2.php) =====
    CREATE TEMPORARY TABLE tmp_ap_bpb AS
WITH
rate as (
    select * from ap_masterrate
    where tanggal = CURDATE()
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - BPB','AP - BPB RETURN')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - BPB','AP - BPB RETURN')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('Reverse AP - Kontrabon')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal BETWEEN CURDATE() and CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('ACCOUNT PAYABLE')
              and nama_coa like '%GR/IR%'
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
            where tgl_journal < CURDATE()
              and tgl_journal > '2026-06-30'
              and type_journal IN ('ACCOUNT PAYABLE')
              and nama_coa like '%GR/IR%'
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
    SELECT supplier, no_bpb, tgl_bpb, duedate, curr,
           saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir,
           rate, (saldo_akhir * rate) saldo_akhir_idr,
           no_coa, nama_coa, item_type1, item_type2, relasi,
           CASE WHEN duedate > CURDATE() THEN (saldo_akhir * rate) ELSE 0 END AS due_current,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 0  AND 30  THEN (saldo_akhir * rate) ELSE 0 END AS due_1_30,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 31 AND 60  THEN (saldo_akhir * rate) ELSE 0 END AS due_31_60,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 61 AND 90  THEN (saldo_akhir * rate) ELSE 0 END AS due_61_90,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END AS due_91_120,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END AS due_121_180,
           CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END AS due_181_360,
           CASE WHEN DATEDIFF(CURDATE(), duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END AS due_gt_360,
           (
               CASE WHEN duedate > CURDATE() THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 0   AND 30  THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 31  AND 60  THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 61  AND 90  THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 91  AND 120 THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 121 AND 180 THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) BETWEEN 181 AND 360 THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN DATEDIFF(CURDATE(),duedate) > 360 THEN (saldo_akhir*rate) ELSE 0 END
           ) AS total_due,
           CASE WHEN duedate <= CURDATE() THEN (saldo_akhir * rate) ELSE 0 END AS pro_due,
           CASE WHEN duedate > CURDATE()
                 AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due0,
           CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
                 AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due1,
           CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
                 AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due2,
           CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
                 AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due3,
           CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
                 AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due4,
           CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
                THEN (saldo_akhir * rate) ELSE 0 END AS pro_due5,
           (
               CASE WHEN duedate <= CURDATE() THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >  CURDATE()
                     AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
                    THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
                     AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
                    THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
                    THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
                    THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
                     AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
                    THEN (saldo_akhir*rate) ELSE 0 END +
               CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
                    THEN (saldo_akhir*rate) ELSE 0 END
           ) AS tot_produe,
           CASE WHEN no_bpb LIKE '%RO%' OR no_bpb LIKE '%OUT%' THEN -1 ELSE 1 END AS f
    from mutasi
)

SELECT
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
FROM laporan_mutasi 
;

    -- ===== Payment Voucher / Kontrabon position as of today, all suppliers (= query_pv2.php) =====
    CREATE TEMPORARY TABLE tmp_ap_pv AS
WITH
rate as (select * from ap_masterrate where tanggal = CURDATE() and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select nama_supp supplier, no_kbon, tgl_kbon, tgl_tempo duedate, curr, (subtotal + tax) total, rate, no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_payment_voucher),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN CURDATE() and CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' and no_journal not like '%/INS%' GROUP BY no_journal
UNION
select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join masterrate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') BETWEEN CURDATE() and CURDATE() 
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
UNION
select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join masterrate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') < CURDATE()
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN CURDATE() and CURDATE() and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN CURDATE() and CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select a.no_kbon, round(COALESCE(pph_idr,0) + COALESCE(potongan_pph,0),2) pph from kontrabon_h a INNER JOIN potongan b on b.no_kbon = a.no_kbon where COALESCE(pph_idr,0) > 0 GROUP BY a.no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(create_date, '%Y-%m-%d') < CURDATE() and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN CURDATE() and CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_bank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between CURDATE() and CURDATE() and type_journal = 'Payment Voucher' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_bank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'Payment Voucher' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_cash as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between CURDATE() and CURDATE() and type_journal = 'Payment Voucher' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_cash_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'Payment Voucher' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_nonbank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between CURDATE() and CURDATE() and type_journal = 'Payment Non Bank' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_nonbank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and type_journal = 'Payment Non Bank' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between CURDATE() and CURDATE() and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < CURDATE() and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

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
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from uang_muka
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
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, total ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, total ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, total ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, total ded_nonbank, 0 ded_nonbank_before from ded_nonbank
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, total ded_nonbank_before from ded_nonbank_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_bank_before,2),0) ded_bank_before, COALESCE(round(ded_bank,2),0) ded_bank, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm, COALESCE(round(ded_cash,2),0) ded_cash, COALESCE(round(ded_cash_before,2),0) ded_cash_before, COALESCE(round(ded_nonbank,2),0) ded_nonbank, COALESCE(round(ded_nonbank_before,2),0) ded_nonbank_before from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (if(ded_bank_before > 0,(ded_bank_before + pph),0) + ded_gm_before + if(ded_cash_before > 0,(ded_cash_before + pph),0) + if(ded_nonbank_before > 0,(ded_nonbank_before + pph),0))) saldo_awal, total_in, pph, uang_muka, potongan, if(ded_bank > 0,(ded_bank + pph),0) ded_bank, if(ded_cash > 0,(ded_cash + pph),0) ded_cash, if(ded_nonbank > 0,(ded_nonbank + pph),0) ded_nonbank, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in  + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > CURDATE() THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF(CURDATE(), duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > CURDATE() THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF(CURDATE(), duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= CURDATE()
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > CURDATE()
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= CURDATE() THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > CURDATE()
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +

CASE WHEN duedate >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi 
 
;

    INSERT INTO dsb_ap_summary
        (id, total, not_due, over_due, ap_group, ap_nongroup, ap_machine, ap_nonmachine, total_usd, total_usd_idr, total_idr, updated_at)
    SELECT
        1,
        SUM(saldo_akhir_idr) total,
        SUM(due_current) not_due,
        SUM(total_due - due_current) over_due,
        SUM(IF(relasi = 'GROUP', saldo_akhir_idr, 0)) ap_group,
        SUM(IF(relasi = 'NON GROUP', saldo_akhir_idr, 0)) ap_nongroup,
        SUM(IF(relasi = 'NON GROUP' AND item_type2 = 'MACHINE', saldo_akhir_idr, 0)) ap_machine,
        SUM(IF(relasi = 'NON GROUP' AND item_type2 = 'NON MACHINE', saldo_akhir_idr, 0)) ap_nonmachine,
        SUM(IF(curr != 'IDR', saldo_akhir, 0)) total_usd,
        SUM(IF(curr != 'IDR', saldo_akhir_idr, 0)) total_usd_idr,
        SUM(IF(curr = 'IDR', saldo_akhir_idr, 0)) total_idr,
        NOW()
    FROM (
        SELECT item_type2, curr, relasi, saldo_akhir, saldo_akhir_idr, due_current, total_due FROM tmp_ap_bpb
        UNION ALL
        SELECT item_type2, curr, relasi, saldo_akhir, saldo_akhir_idr, due_current, total_due FROM tmp_ap_pv
    ) combined
    ON DUPLICATE KEY UPDATE
        total         = VALUES(total),
        not_due       = VALUES(not_due),
        over_due      = VALUES(over_due),
        ap_group      = VALUES(ap_group),
        ap_nongroup   = VALUES(ap_nongroup),
        ap_machine    = VALUES(ap_machine),
        ap_nonmachine = VALUES(ap_nonmachine),
        total_usd     = VALUES(total_usd),
        total_usd_idr = VALUES(total_usd_idr),
        total_idr     = VALUES(total_idr),
        updated_at    = VALUES(updated_at);

    DROP TEMPORARY TABLE IF EXISTS tmp_ap_bpb;
    DROP TEMPORARY TABLE IF EXISTS tmp_ap_pv;
END$$

DELIMITER ;

-- Seed the table immediately so the dashboard has data before the first
-- scheduled tick.
CALL get_ap_dashboard_summary();

DROP EVENT IF EXISTS `get_ap_dashboard_summary`;

CREATE DEFINER=`root`@`%` EVENT `get_ap_dashboard_summary`
ON SCHEDULE EVERY 600 SECOND
ON COMPLETION NOT PRESERVE ENABLE
DO CALL get_ap_dashboard_summary();
