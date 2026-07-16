-- =====================================================================
-- Purchase dashboard summary: precomputed table + refresh procedure + event
--
-- Same pattern as create_dsb_ap_summary.sql / get_ap_purchase_nak: a small
-- summary table refreshed on a timer, so dashboard-ap.php's "Net Purchase
-- Year to Date" / "Net Purchase Current Month" cards read a single fast
-- row instead of running the SUM/UNION queries live, and get a real
-- "Updated at" timestamp to display (matching the AP Total Outstanding
-- card).
--
-- Run this whole file once (e.g. via phpMyAdmin or mysql CLI) against
-- signalbit_erp.
-- =====================================================================

DROP TABLE IF EXISTS `dsb_purchase_summary`;

CREATE TABLE `dsb_purchase_summary` (
  `id`           tinyint(1) unsigned NOT NULL DEFAULT 1,
  `ytd_purchase` decimal(20,2) DEFAULT NULL COMMENT 'Purchase Year to Date',
  `ytd_retur`    decimal(20,2) DEFAULT NULL COMMENT 'Purchase Return YTD',
  `ytd_net`      decimal(20,2) DEFAULT NULL COMMENT 'Net Purchase Year to Date',
  `cm_purchase`  decimal(20,2) DEFAULT NULL COMMENT 'Purchase Current Month',
  `cm_retur`     decimal(20,2) DEFAULT NULL COMMENT 'Purchase Return Current Month',
  `cm_net`       decimal(20,2) DEFAULT NULL COMMENT 'Net Purchase Current Month',
  `updated_at`   datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP PROCEDURE IF EXISTS `get_purchase_dashboard_summary`;

DELIMITER $$

CREATE DEFINER=`root`@`%` PROCEDURE `get_purchase_dashboard_summary`()
BEGIN
    INSERT INTO dsb_purchase_summary
        (id, ytd_purchase, ytd_retur, ytd_net, cm_purchase, cm_retur, cm_net, updated_at)
    SELECT
        1,
        ytd_purchase,
        ytd_retur,
        (ytd_purchase - ytd_retur) ytd_net,
        cm_purchase,
        cm_retur,
        (cm_purchase - cm_retur) cm_net,
        NOW()
    FROM (
        SELECT
            (SELECT COALESCE(SUM(total_idr),0) FROM dsb_ap_purchase WHERE tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') AND CURRENT_DATE()) ytd_purchase,
            (SELECT COALESCE(SUM(total_idr),0) FROM dsb_ap_retur    WHERE tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') AND CURRENT_DATE()) ytd_retur,
            (SELECT COALESCE(SUM(total_idr),0) FROM dsb_ap_purchase WHERE MONTH(tgl_bpb) = MONTH(CURRENT_DATE)) cm_purchase,
            (SELECT COALESCE(SUM(total_idr),0) FROM dsb_ap_retur    WHERE MONTH(tgl_bpb) = MONTH(CURRENT_DATE)) cm_retur
    ) x
    ON DUPLICATE KEY UPDATE
        ytd_purchase = VALUES(ytd_purchase),
        ytd_retur    = VALUES(ytd_retur),
        ytd_net      = VALUES(ytd_net),
        cm_purchase  = VALUES(cm_purchase),
        cm_retur     = VALUES(cm_retur),
        cm_net       = VALUES(cm_net),
        updated_at   = VALUES(updated_at);
END$$

DELIMITER ;

-- Seed the table immediately so the dashboard has data before the first
-- scheduled tick.
CALL get_purchase_dashboard_summary();

DROP EVENT IF EXISTS `get_purchase_dashboard_summary`;

CREATE DEFINER=`root`@`%` EVENT `get_purchase_dashboard_summary`
ON SCHEDULE EVERY 600 SECOND
ON COMPLETION NOT PRESERVE ENABLE
DO CALL get_purchase_dashboard_summary();
