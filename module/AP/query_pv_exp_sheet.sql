/* ============================================================================
   LEMBAR "PV EXP" - QUERY-NYA
   Isi tab PV EXP di layar Cash Flow Projection, dan lembar PV EXP di Excel.

   Diminta pemakai 20 Nov 2026. Query ini CERMINAN dari cf_kumpul_sumber()
   (cf_sumber_lib.php) + pvb_tanpa_rencana() (pvb_dpcbd_lib.php): hasilnya
   sama, tapi bisa dijalankan langsung di basis data.

   LEMBAR INI PUNYA TIGA SUMBER, disatukan dgn UNION ALL:
     A. Projection Other - entri PV Expense yang diketik tangan
     B. Rencana bayar  - dokumen yang sudah diberi tanggal di Update Payment Date
     C. Belum dijadwalkan - PV biaya / DP / CBD / FTR yang masih outstanding,
        ditaruh di KAMIS minggu jatuh temponya

   Kolom "source" menjelaskan tiap baris datang dari yang mana.

   CATATAN PENTING
   - Diuji di MariaDB 10.11 (produksi).
   - @type menentukan entri Projection Other mana yang ikut (bagian A saja);
     bagian B & C tidak mengenal type.
   - Kurs: hanya rekening USD yang dikalikan; sisanya 1. Kursnya kurs PAJAK
     terakhir yang tanggalnya <= @akhir - sama dgn yang dipakai laporannya.
   - Nilai uang di lembar ini SELALU POSITIF (ABS). Di laporannya ia
     pengeluaran, jadi di sana tandanya negatif.
   ============================================================================ */

/* Kolom teks di basis data ini latin1. Tanpa baris ini, pembanding teks
   bisa gagal dgn "Illegal mix of collations" kalau klien memakai charset
   lain - itu bukan salah query-nya, cuma sambungannya. */
SET NAMES latin1;

SET @mulai := '2026-11-22';       /* tanggal From di layar */
SET @akhir := '2026-11-28';       /* tanggal To            */
SET @type  := 'WEEKLY';           /* DAILY / WEEKLY / MONTHLY */

SET @rate := (SELECT rate FROM masterrate
               WHERE curr = 'USD' AND v_codecurr = 'PAJAK' AND tanggal <= @akhir
               ORDER BY tanggal DESC LIMIT 1);

/* Kategori Uang Muka Supplier - dipakai DP/CBD/FTR yang tidak punya kolom
   kategori sendiri. */
SET @id_uangmuka := (SELECT id FROM master_cash_flow
                      WHERE status = 'Y' AND UPPER(TRIM(nama_subcategory)) = 'UANG MUKA PEMBELIAN'
                      LIMIT 1);

WITH
/* ---- mata uang tiap rekening, untuk menentukan kursnya --------------------
   Kuncinya disamakan dgn cfpKunci(): COA kas dipakai apa adanya, rekening
   bank diambil angkanya saja - sumbernya menulis rekening dgn gaya
   berbeda-beda ("008-997-1979", "BCA - 008-997-1979", dst). */
akun AS (
    SELECT REGEXP_REPLACE(bank_account, '[^0-9]', '') AS kunci,
           CASE WHEN UPPER(TRIM(curr)) = 'USD' THEN 'USD' ELSE 'IDR' END AS curr
      FROM b_masterbank
     WHERE status = 'Active' AND REGEXP_REPLACE(bank_account, '[^0-9]', '') <> ''
    UNION ALL
    SELECT no_coa, 'IDR' FROM mastercoa_v2
     WHERE ind_categori5 = 'KAS' AND nama_coa <> 'POS SILANG'
),

/* ---- yang sudah dibayar: PV biaya lewat Bank Out --------------------------
   type_pv BOLEH NULL: kolom itu baru diisi sejak 1 Juli 2026, dan tanpa
   NULL ikut terpotong, PV yang lunas sebelum tanggal itu akan terlihat
   masih outstanding. */
bayar_pv AS (
    SELECT a.profit_center, a.no_reff, SUM(a.total) AS total_pv
      FROM b_bankout_det a
     INNER JOIN b_bankout_h b ON b.no_bankout = a.no_bankout
     WHERE b.status <> 'Cancel'
       AND a.no_reff LIKE 'PV/%'
       AND (a.type_pv = 'Biaya' OR a.type_pv IS NULL)
     GROUP BY a.no_reff, a.profit_center
),

/* ---- sisa tiap PV biaya yang sudah approve -------------------------------- */
sisa_pv AS (
    SELECT h.no_pv, d.profit_center, h.nama_supp, h.curr, h.frm_akun, h.id_cash_flow,
           MAX(d.due_date) AS due_date,
           (SUM(d.amount - d.ded_add)
            + SUM((d.amount * d.ppn/100) - (d.ded_add * d.ppn/100))
            - SUM((d.amount * d.pph/100) - (d.ded_add * d.pph/100))) AS total
      FROM tbl_pv_h h
     INNER JOIN tbl_pv d ON d.no_pv = h.no_pv
     WHERE h.status = 'Approved'
     GROUP BY h.no_pv, d.profit_center
),

/* ---- nomor yang SUDAH punya rencana bayar --------------------------------
   Kuncinya nomor + profit center: satu PV bisa memuat dua profit center
   dgn nilai sendiri-sendiri, dan keduanya dijadwalkan terpisah. */
sudah_rencana AS (
    SELECT DISTINCT d.no_pv, d.profit_center
      FROM ap_pvbiaya_plan_det d
     INNER JOIN ap_pvbiaya_plan_h h ON h.doc_number = d.doc_number AND h.status <> 'CANCEL'
     WHERE d.status = 'Y'
)

/* ====================== A. PROJECTION OTHER - PV EXPENSE =================== */
SELECT  'Cash Flow Projection (Other)'                    AS source,
        COALESCE(NULLIF(p.no_pv, ''), p.no_doc)           AS payment_voucher_no,
        p.nama_supplier                                   AS supplier_name,
        p.kategori                                        AS cost_category,
        p.curr                                            AS currency,
        ABS(p.amount)                                     AS amount,
        COALESCE(k.kurs, 1)                               AS rate,
        ABS(p.amount) * COALESCE(k.kurs, 1)               AS equivalent_idr,
        p.tanggal                                         AS due_date,
        COALESCE(NULLIF(p.pc_source, ''), p.pc_tujuan)    AS pc,
        COALESCE(NULLIF(p.akun_source, ''), p.akun_tujuan) AS bank
  FROM ap_cf_projection p
  LEFT JOIN (SELECT kunci, CASE WHEN curr = 'USD' THEN @rate ELSE 1 END AS kurs FROM akun) k
         ON k.kunci = REGEXP_REPLACE(COALESCE(NULLIF(p.akun_source, ''), p.akun_tujuan), '[^0-9]', '')
 WHERE p.status <> 'Cancel'
   AND UPPER(TRIM(p.jenis)) = 'PV EXPENSE'
   AND p.tanggal BETWEEN @mulai AND @akhir
   AND p.type_report = @type

UNION ALL

/* ====================== B. SUDAH DIJADWALKAN ============================== */
SELECT  CONCAT('Update Payment Date - ', d.jenis)         AS source,
        d.no_pv                                           AS payment_voucher_no,
        COALESCE(pv.nama_supp, dp.nama_supp, cb.nama_supp) AS supplier_name,
        COALESCE(mc.nama_subcategory, '(no cash flow category)') AS cost_category,
        d.curr                                            AS currency,
        ABS(d.amount)                                     AS amount,
        COALESCE(k.kurs, 1)                               AS rate,
        ABS(d.amount) * COALESCE(k.kurs, 1)               AS equivalent_idr,
        d.plan_date                                       AS due_date,
        d.profit_center                                   AS pc,
        COALESCE(pv.frm_akun, dp.from_account, cb.from_account) AS bank
  FROM ap_pvbiaya_plan_det d
 INNER JOIN ap_pvbiaya_plan_h h ON h.doc_number = d.doc_number AND h.status <> 'CANCEL'
  LEFT JOIN tbl_pv_h      pv ON pv.no_pv   = d.no_pv AND d.jenis = 'Biaya'
  LEFT JOIN kontrabon_h_dp  dp ON dp.no_kbon = d.no_pv AND d.jenis = 'DP'
  LEFT JOIN kontrabon_h_cbd cb ON cb.no_kbon = d.no_pv AND d.jenis = 'CBD'
  LEFT JOIN master_cash_flow mc
         ON mc.id = CASE WHEN d.jenis = 'Biaya' THEN pv.id_cash_flow ELSE @id_uangmuka END
  LEFT JOIN (SELECT kunci, CASE WHEN curr = 'USD' THEN @rate ELSE 1 END AS kurs FROM akun) k
         ON k.kunci = REGEXP_REPLACE(COALESCE(pv.frm_akun, dp.from_account, cb.from_account), '[^0-9]', '')
 WHERE d.status = 'Y'
   AND d.plan_date BETWEEN @mulai AND @akhir

UNION ALL

/* ====================== C. BELUM DIJADWALKAN ==============================
   Tanggalnya KAMIS di minggu jatuh temponya - rumus yang sama dgn PV AP:
   DAYOFWEEK memberi 1=Minggu dan Kamis=5, jadi duedate + (5 - DAYOFWEEK).
   Untuk Jumat & Sabtu hasilnya mundur, dan memang begitu maunya. */
SELECT  'Not scheduled yet (due-date Thursday) - Biaya'   AS source,
        s.no_pv                                           AS payment_voucher_no,
        s.nama_supp                                       AS supplier_name,
        COALESCE(mc.nama_subcategory, '(no cash flow category)') AS cost_category,
        s.curr                                            AS currency,
        ABS(s.total - COALESCE(b.total_pv, 0))            AS amount,
        COALESCE(k.kurs, 1)                               AS rate,
        ABS(s.total - COALESCE(b.total_pv, 0)) * COALESCE(k.kurs, 1) AS equivalent_idr,
        DATE_ADD(s.due_date, INTERVAL (5 - DAYOFWEEK(s.due_date)) DAY) AS due_date,
        s.profit_center                                   AS pc,
        s.frm_akun                                        AS bank
  FROM sisa_pv s
  LEFT JOIN bayar_pv b ON b.no_reff = s.no_pv AND b.profit_center = s.profit_center
  LEFT JOIN sudah_rencana r ON r.no_pv = s.no_pv AND r.profit_center = s.profit_center
  LEFT JOIN master_cash_flow mc ON mc.id = s.id_cash_flow
  LEFT JOIN (SELECT kunci, CASE WHEN curr = 'USD' THEN @rate ELSE 1 END AS kurs FROM akun) k
         ON k.kunci = REGEXP_REPLACE(s.frm_akun, '[^0-9]', '')
 WHERE r.no_pv IS NULL
   AND (s.total - COALESCE(b.total_pv, 0)) >= 1
   AND DATE_ADD(s.due_date, INTERVAL (5 - DAYOFWEEK(s.due_date)) DAY) BETWEEN @mulai AND @akhir

ORDER BY due_date ASC, payment_voucher_no ASC;
