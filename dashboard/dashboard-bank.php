<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

/* Dashboard Global Styles */
.div-dashboard {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    background: #f8fafc !important;
    padding: 24px !important;
    border-radius: 24px !important;
}

/* Card Container */
.div-dashboard .card {
    border: none !important;
    border-radius: 20px !important;
    background: #ffffff !important;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    margin-bottom: 24px !important;
    overflow: hidden !important;
}

.div-dashboard .card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -6px rgba(15, 23, 42, 0.08) !important;
}

/* Card Header styles */
.div-dashboard .card-header {
    border: none !important;
    padding: 20px 24px !important;
    font-size: 0.95rem !important;
    font-weight: 700 !important;
    letter-spacing: 1px !important;
    text-transform: uppercase !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

/* Specific Card Header Colors - Green for Cash (matches dashboard-ap.php's
   Purchase green #1f8a4c) */
.div-dashboard .card-header[style*="background-color:#006400"],
.div-dashboard .card-header[style*="background-color: rgb(0, 100, 0)"] {
    background: linear-gradient(135deg, #25a05a, #1f8a4c) !important;
    color: #ffffff !important;
}

/* Specific Card Header Colors - Blue for Loans (matches dashboard-ap.php's
   Account Payable blue #2a78d6) */
.div-dashboard .card-header.bg-info {
    background: linear-gradient(135deg, #3987e5, #2a78d6) !important;
    color: #ffffff !important;
}

/* Secondary/Sub-card Headers (Limit, Month over Month charts) */
.div-dashboard .card-header.border-dark:not(.bg-info):not([style*="background-color:#006400"]):not([style*="background-color: rgb(0, 100, 0)"]) {
    background: #ffffff !important;
    color: #1e293b !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 18px 24px !important;
}

.div-dashboard .card-header b {
    font-size: 0.95rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.8px !important;
}

/* Freshness stamp under each card's headline value - mirrors dashboard-ap.php's
   .ap-hero-updated so both dashboards read as one product. */
.div-dashboard .card-updated {
    text-align: center !important;
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    color: #94a3b8 !important;
    margin-top: -4px !important;
    margin-bottom: 10px !important;
}
.div-dashboard .card-updated .fa {
    font-size: 0.72rem !important;
    margin-right: 3px !important;
}

/* Card Body */
.div-dashboard .card-body {
    padding: 24px !important;
    background: #ffffff !important;
}

/* Card Value (Amount) text */
.div-dashboard .card-text {
    text-align: center !important;
    font-size: 2.1rem !important;
    font-weight: 800 !important;
    color: #0f172a !important; /* Slate-900 */
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    letter-spacing: -0.8px !important;
    margin: 8px 0 !important;
    min-height: 68px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
}

/* Converted/Detail values */
.div-dashboard .card-text span,
.div-dashboard .card-header span {
    font-size: 1rem !important;
    font-weight: 500 !important;
    color: #64748b !important; /* Slate-500 */
    display: block !important;
    margin-top: 6px !important;
    letter-spacing: 0px !important;
    text-transform: none !important;
}

.div-dashboard .card-header span {
    display: inline !important;
    margin-left: 6px !important;
    color: rgba(30, 41, 59, 0.7) !important;
}

/* ========== Trigger Button (Open Modal) ========== */
.btn-detail-trigger {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 6px 14px !important;
    border-radius: 100px !important;
    border: 1.5px solid rgba(255, 255, 255, 0.35) !important;
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 0.68rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06) !important;
    float: right !important;
    margin: -4px 0 -4px auto !important;
    text-decoration: none !important;
    line-height: 1 !important;
    white-space: nowrap !important;
}

.btn-detail-trigger:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.55) !important;
    transform: translateY(-1px) scale(1.04) !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
    color: #ffffff !important;
    text-decoration: none !important;
}

.btn-detail-trigger:active {
    transform: translateY(0) scale(0.97) !important;
}

.btn-detail-trigger .fa {
    font-size: 0.6rem !important;
    transition: transform 0.25s ease !important;
}

.btn-detail-trigger:hover .fa {
    transform: translateX(2px) !important;
}

/* Light Header variant (white/non-colored headers) */
.btn-detail-trigger--light {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0) !important;
    border-color: #cbd5e1 !important;
    color: #475569 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.btn-detail-trigger--light:hover {
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1) !important;
    border-color: #94a3b8 !important;
    color: #1e293b !important;
    box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15) !important;
}

/* Chart heights */
#chartdiv, #chartdiv2, #chartdiv3, #chartdiv4, #chartdiv5, #chartdiv6 {
    width: 100% !important;
    height: 300px !important;
    background: transparent !important;
}

/* ========== MODAL DESIGN SYSTEM ========== */

/* Backdrop blur */
.modal-backdrop.show {
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    background: rgba(15, 23, 42, 0.45) !important;
}

/* Entrance animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal.show .modal-dialog {
    animation: modalSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
}

/* Modal Content Shell */
.modal-content {
    border: none !important;
    border-radius: 20px !important;
    box-shadow:
        0 32px 64px -12px rgba(15, 23, 42, 0.2),
        0 0 0 1px rgba(15, 23, 42, 0.05) !important;
    overflow: hidden !important;
    background: #ffffff !important;
}

/* ---- Modal Header Base ---- */
.modal-header {
    border-bottom: none !important;
    padding: 28px 32px 20px 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    position: relative !important;
}

/* Green Header — Cash modals */
.modal-header.bg-success {
    background: linear-gradient(135deg, #25a05a 0%, #1f8a4c 50%, #166a3a 100%) !important;
    color: #ffffff !important;
}

/* Blue Header — Loan modals */
.modal-header.bg-secondary {
    background: linear-gradient(135deg, #3987e5 0%, #2a78d6 50%, #1f5fae 100%) !important;
    color: #ffffff !important;
}

/* Detail modal headers (modaldetcoh, modaldetcib, modaldettc) - Cash family too */
.modal-header.modal-header--detail {
    background: linear-gradient(135deg, #25a05a 0%, #1f8a4c 50%, #166a3a 100%) !important;
    color: #ffffff !important;
    padding: 24px 28px 18px 28px !important;
}

/* Title */
.modal-title {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 800 !important;
    font-size: 1.1rem !important;
    letter-spacing: -0.3px !important;
    line-height: 1.35 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.08) !important;
}

/* ---- Close Button ---- */
.modal-header .close {
    color: #ffffff !important;
    opacity: 0.9 !important;
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 50% !important;
    width: 34px !important;
    height: 34px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    font-size: 0.8rem !important;
    cursor: pointer !important;
    padding: 0 !important;
    margin: 0 !important;
    position: relative !important;
    z-index: 2 !important;
    line-height: 1 !important;
    text-shadow: none !important;
}

.modal-header .close:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.28) !important;
    transform: rotate(90deg) scale(1.1) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
}

/* ---- Modal Body ---- */
.modal-body {
    padding: 28px 32px 32px 32px !important;
    background: #ffffff !important;
}

/* ---- Modal Footer (if any) ---- */
.modal-footer {
    border-top: 1px solid #f1f5f9 !important;
    padding: 16px 32px !important;
}

/* ========== MODAL TABLE STYLING ========== */
.modal-body table {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 0.82rem !important;
    color: #334155 !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    border: 1px solid #e2e8f0 !important;
}

.modal-body thead tr {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
}

.modal-body th {
    font-weight: 700 !important;
    color: #475569 !important;
    text-transform: uppercase !important;
    font-size: 0.7rem !important;
    letter-spacing: 0.8px !important;
    padding: 14px 18px !important;
    border-bottom: 2px solid #e2e8f0 !important;
    white-space: nowrap !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 1 !important;
}

.modal-body td {
    padding: 13px 18px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    color: #475569 !important;
    vertical-align: middle !important;
    transition: background 0.15s ease !important;
}

.modal-body tbody tr:nth-child(even) {
    background: #fafbfd !important;
}

.modal-body tbody tr:hover {
    background: #eff6ff !important;
}

.modal-body tbody tr:hover td {
    color: #1e293b !important;
}

.modal-body tbody tr:last-child td {
    border-bottom: none !important;
}

/* ========== SCROLLABLE MODAL SCROLLBAR ========== */
.modal-dialog-scrollable .modal-body {
    scrollbar-width: thin !important;
    scrollbar-color: #cbd5e1 transparent !important;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar {
    width: 5px !important;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
    background: transparent !important;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
    background-color: #cbd5e1 !important;
    border-radius: 100px !important;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8 !important;
}

/* ========== MODAL SIZES ========== */
.modal-md {
    max-width: 560px !important;
}

/* ========== LOCAL SVG ICONS ========== */
.hdr-icon {
    display: inline-block !important;
    width: 15px !important;
    height: 15px !important;
    margin-right: 8px !important;
    margin-top: 0 !important;
    vertical-align: -2px !important;
    fill: none !important;
    stroke: currentColor !important;
    stroke-width: 2 !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
    color: inherit !important;
    opacity: 0.9 !important;
}

.btn-detail-trigger .hdr-icon {
    width: 10px !important;
    height: 10px !important;
    margin-right: 4px !important;
    vertical-align: -1px !important;
    opacity: 1 !important;
}
</style>

<svg width="0" height="0" style="position:absolute;overflow:hidden;" aria-hidden="true">
    <defs>
        <symbol id="icon-cash" viewBox="0 0 24 24">
            <rect x="2" y="6" width="20" height="12" rx="2"></rect>
            <circle cx="12" cy="12" r="2"></circle>
            <path d="M6 12h.01M18 12h.01"></path>
        </symbol>
        <symbol id="icon-bank" viewBox="0 0 24 24">
            <line x1="3" y1="22" x2="21" y2="22"></line>
            <line x1="6" y1="18" x2="6" y2="11"></line>
            <line x1="10" y1="18" x2="10" y2="11"></line>
            <line x1="14" y1="18" x2="14" y2="11"></line>
            <line x1="18" y1="18" x2="18" y2="11"></line>
            <polygon points="12 2 21 7 3 7"></polygon>
        </symbol>
        <symbol id="icon-calculator" viewBox="0 0 24 24">
            <rect x="4" y="2" width="16" height="20" rx="2"></rect>
            <rect x="8" y="6" width="8" height="4"></rect>
            <line x1="8" y1="14" x2="8" y2="14.01"></line>
            <line x1="12" y1="14" x2="12" y2="14.01"></line>
            <line x1="16" y1="14" x2="16" y2="14.01"></line>
            <line x1="8" y1="18" x2="8" y2="18.01"></line>
            <line x1="12" y1="18" x2="12" y2="18.01"></line>
            <line x1="16" y1="18" x2="16" y2="18.01"></line>
        </symbol>
        <symbol id="icon-credit-card" viewBox="0 0 24 24">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
            <line x1="1" y1="10" x2="23" y2="10"></line>
        </symbol>
        <symbol id="icon-dollar" viewBox="0 0 24 24">
            <line x1="12" y1="1" x2="12" y2="23"></line>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
        </symbol>
        <symbol id="icon-scale" viewBox="0 0 24 24">
            <line x1="12" y1="3" x2="12" y2="21"></line>
            <line x1="5" y1="7" x2="19" y2="7"></line>
            <path d="M5 7l-3 6a3 3 0 0 0 6 0z"></path>
            <path d="M19 7l-3 6a3 3 0 0 0 6 0z"></path>
            <line x1="8" y1="21" x2="16" y2="21"></line>
        </symbol>
        <symbol id="icon-gauge" viewBox="0 0 24 24">
            <circle cx="12" cy="13" r="8"></circle>
            <path d="M12 13l4-4"></path>
            <path d="M12 5V3"></path>
            <path d="M5 13H3"></path>
            <path d="M21 13h-2"></path>
        </symbol>
        <symbol id="icon-trending-up" viewBox="0 0 24 24">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
            <polyline points="17 6 23 6 23 12"></polyline>
        </symbol>
        <symbol id="icon-arrow-right" viewBox="0 0 24 24">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </symbol>
    </defs>
</svg>

<?php
// All figures on this dashboard are computed live on every page load (no
// precomputed summary table like dsb_ap_summary), so "now" is the accurate
// freshness stamp - mirrors the "Updated" line on dashboard-ap.php's cards.
// Explicit WIB: this server's PHP default timezone is Europe/Berlin, not
// Asia/Jakarta, so a bare date()/time() call would be off by several hours.
$bank_updated_at = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('d M Y, H:i');
?>

<div class="row div-dashboard">
    <div class="col-md-12">
        <div class="row p-3">
            <div class="col-md-4">
                <div class="card border-dark mb-2 mt-2" >
                    <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;"><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-cash"></use></svg> CASH ON HAND</b><button type="button" class="btn-detail-trigger" onclick="openmodalcoh()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                    <div class="card-body text-secondary">
                        <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                            <?php
                            // Ending balance riil per akun kas (mastercoa_v2 ind_categori5='KAS' +
                            // b_saldoawal_pettycash + c_report_pettycash) - sama polanya dengan
                            // CASH IN BANK, cuma akun bersaldo positif yang dihitung.
                            $sql_coh = mysqli_query($conn1, "
                                SELECT ROUND(SUM(GREATEST(bal,0)),0) AS total
                                FROM (
                                    SELECT c.no_coa,
                                        (COALESCE(s.amount,0) + COALESCE(SUM(
                                            CASE WHEN r.transaksi_date <= CURDATE() AND r.status != 'Cancel'
                                                 THEN r.debit - r.credit ELSE 0 END
                                        ),0))
                                        * IF(s.curr IS NULL OR s.curr='IDR',1,
                                            (SELECT mr.rate FROM masterrate mr
                                             WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= CURDATE()
                                             ORDER BY mr.tanggal DESC LIMIT 1)
                                        ) AS bal
                                    FROM mastercoa_v2 c
                                    LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
                                    LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
                                    WHERE c.ind_categori5 = 'KAS'
                                    GROUP BY c.no_coa, s.amount, s.curr
                                ) pa
                            ");
                            $row_coh = mysqli_fetch_array($sql_coh);
                            $total_coh = isset($row_coh['total']) ? $row_coh['total'] :0;

                            ?>
                            IDR <?= number_format($total_coh,0); ?></p>
                            <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-dark mb-2 mt-2" >
                        <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;" ><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-bank"></use></svg> CASH IN BANK</b><button type="button" class="btn-detail-trigger" onclick="openmodalcib()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                        <div class="card-body text-secondary">
                            <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                            // Ending balance riil per akun (b_masterbank + b_saldoawal_bank + b_reportbank),
                            // dikonversi ke IDR pakai rate HARIAN pada tanggal transaksi TERAKHIR akun
                            // tsb (bukan rate hari ini) - supaya sama persis dengan bankreport.php, yang
                            // pakai rate di tanggal transaksi terakhir dalam periode, bukan rate terkini.
                            // Cuma akun bersaldo positif yang dihitung (saldo negatif = overdraft/loan,
                            // sudah masuk kartu BANK LOAN IDR).
                            $sql_cib = mysqli_query($conn1, "
                                SELECT ROUND(SUM(GREATEST(bal,0)),0) AS total
                                FROM (
                                    SELECT m.bank_account,
                                        (COALESCE(s.amount,0) + COALESCE(SUM(
                                            CASE WHEN r.transaksi_date <= CURDATE() AND r.status != 'Cancel'
                                                 THEN r.debit - r.credit ELSE 0 END
                                        ),0))
                                        * IF(m.curr='IDR',1,
                                            (SELECT mr.rate FROM masterrate mr
                                             WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                                               AND mr.tanggal <= COALESCE(
                                                   (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                                                    WHERE r2.akun = m.bank_account AND r2.transaksi_date <= CURDATE() AND r2.status != 'Cancel'),
                                                   CURDATE()
                                               )
                                             ORDER BY mr.tanggal DESC LIMIT 1)
                                        ) AS bal
                                    FROM b_masterbank m
                                    LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
                                    LEFT JOIN b_reportbank r ON r.akun = m.bank_account
                                    GROUP BY m.bank_account, s.amount
                                ) pa
                            ");
                            $row_cib = mysqli_fetch_array($sql_cib);
                            $total_cib = isset($row_cib['total']) ? $row_cib['total'] :0;

                            ?>
                            IDR <?= number_format($total_cib,0); ?></p>
                            <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-dark mb-2 mt-2" >
                        <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;"><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-calculator"></use></svg> CASH & BANK TOTAL</b><button type="button" class="btn-detail-trigger" onclick="openmodaltc()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                        <div class="card-body text-secondary">
                            <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                                <?php
                                $total_cb = $total_coh + $total_cib 
                                ?>
                                IDR <?= number_format($total_cb,0); ?></p>
                                <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-dark mb-2 mt-2" >
                            <div class="card-header bg-info bg-gradient bg-gradient border-dark text-white"><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-credit-card"></use></svg> BANK LOAN IDR</b></div>
                            <div class="card-body text-secondary">
                                <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                                $bulan = date("M"); 
                                $tahun = date("Y");
                                // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_$tahun where no_coa IN ('2.20.01')
                                //     UNION
                                //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_$tahun where no_coa IN ('1.10.01')) a");
                                // $row_bli = mysqli_fetch_array($sql_bli);
                                // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

                                //nilai new 
                                $sqlswl3_ = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-997-1979'");
                                $rowswl3_ = mysqli_fetch_array($sqlswl3_);
                                $swl3_ = isset($rowswl3_['amount']) ? $rowswl3_['amount'] : 0;

                                $sqlsaldo_ = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-997-1979'");
                                $rowsaldo_ = mysqli_fetch_array($sqlsaldo_);
                                $salawal_ = isset($rowsaldo_['amount']) ? $rowsaldo_['amount'] : 0;

                                $sqlswl4_ = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                    FROM
                                    (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date < CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                    (SELECT @runtot:= $salawal_ ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
                                $rowswl4_ = mysqli_fetch_array($sqlswl4_);
                                $saldoswal2_ = isset($rowswl4_['saldoawal']) ? $rowswl4_['saldoawal'] : 0;

                                $sql6_ = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                    FROM
                                    (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                    (SELECT @runtot:= $saldoswal2_,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
                                $rows6_ = mysqli_fetch_array($sql6_);
                                $total_bli = isset($rows6_['saldo_akhir']) ? $rows6_['saldo_akhir'] : $saldoswal2_;

                                ?>
                                IDR <?= number_format(abs($total_bli),0); ?></p>
                                <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                            </div>
                        </div>
                        <div class="card border-dark mb-2" >
                            <div class="card-header border-dark"><b style="font-size: 1rem;">
                                <?php
                                $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
                                $row1 = mysqli_fetch_array($sql1);
                                $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

                                ?>
                                <svg class="hdr-icon"><use href="#icon-gauge"></use></svg> LIMIT : IDR <?= number_format($limit_idr,0); ?></b></div>
                                <div class="card-body text-secondary">
                                    <div id="chartdiv"></div>
                                </div>
                            </div>
                            <div class="card border-dark mb-2" >
                                <div class="card-header border-dark"><b style="font-size: 1rem;"><svg class="hdr-icon"><use href="#icon-trending-up"></use></svg> LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b><button type="button" class="btn-detail-trigger btn-detail-trigger--light" onclick="openmodalloanidr()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                                <div class="card-body text-secondary">
                                    <div id="chartdiv2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-dark mb-2 mt-2" >
                                <div class="card-header bg-info bg-gradient bg-gradient border-dark text-white"><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-dollar"></use></svg> BANK LOAN USD</b></div>
                                <div class="card-body text-secondary">
                                    <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                                    $bulan = date("M"); 
                                    $tahun = date("Y");
                                    $sql_blu = mysqli_query($conn2,"select total,(total / rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_$tahun where no_coa IN ('2.20.02')
                                        UNION
                                        select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_$tahun where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
                                    $row_blu = mysqli_fetch_array($sql_blu);
                                    $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
                                    $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

                                    //nilai new 
                                    $sqlswl3 = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-998-1982'");
                                    $rowswl3 = mysqli_fetch_array($sqlswl3);
                                    $swl3 = isset($rowswl3['amount']) ? $rowswl3['amount'] : 0;

                                    $sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-998-1982'");
                                    $rowsaldo = mysqli_fetch_array($sqlsaldo);
                                    $salawal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

                                    $sqlswl4 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                        FROM
                                        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date < CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                        (SELECT @runtot:= $salawal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
                                    $rowswl4 = mysqli_fetch_array($sqlswl4);
                                    $saldoswal2 = isset($rowswl4['saldoawal']) ? $rowswl4['saldoawal'] : 0;

                                    $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                        FROM
                                        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                        (SELECT @runtot:= $saldoswal2,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
                                    $rows6 = mysqli_fetch_array($sql6);
                                    if ((isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : $saldoswal2) > 0) {
                                        $saldoakhir = 0;
                                    }else{
                                        $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : $saldoswal2;
                                    }
                                    $dateakhir = isset($rows6['date']) ? $rows6['date'] : null;

                                    $sqlrates3 = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateakhir'");
                                    $rowrates3 = mysqli_fetch_array($sqlrates3);
                                    $maxidrate3 = isset($rowrates3['id']) ? $rowrates3['id'] : null;

                                    if ($maxidrate3 != null) {
                                        $rates3 = $rowrates3['rate'];
                                    }else{
                                        $sqlxss3 = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                                        $rowxss3 = mysqli_fetch_array($sqlxss3);
                                        $maxidss3 = isset($rowxss3['id']) ? $rowxss3['id'] : null;

                                        $sqlyss3 = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss3' and v_codecurr = 'PAJAK'");
                                        $rowyss3 = mysqli_fetch_array($sqlyss3);
                                        $rates3 = isset($rowyss3['rate']) ? $rowyss3['rate'] : 1;
                                    }

                                    ?>
                                    USD <?= number_format(abs($saldoakhir),2); ?> <span>( IDR <?= number_format(abs($saldoakhir * $rates3),0); ?> )</span></p>
                                    <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                                </div>
                            </div>
                            <div class="card border-dark mb-2" >
                                <div class="card-header border-dark"><b style="font-size: 1rem;">
                                    <?php
                                    $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
                                    $row1 = mysqli_fetch_array($sql1);
                                    $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
                                    $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

                                    ?>
                                    <svg class="hdr-icon"><use href="#icon-gauge"></use></svg> LIMIT : USD <?= number_format($fac_limit,0); ?> <span>( IDR <?= number_format($limit_convert,0); ?> )</span></b></div>
                                    <div class="card-body text-secondary">
                                        <div id="chartdiv3"></div>
                                    </div>
                                </div>
                                <div class="card border-dark mb-2" >
                                    <div class="card-header border-dark"><b style="font-size: 1rem;"><svg class="hdr-icon"><use href="#icon-trending-up"></use></svg> LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b><button type="button" class="btn-detail-trigger btn-detail-trigger--light" onclick="openmodalloanusd()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                                    <div class="card-body text-secondary">
                                        <div id="chartdiv4"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-dark mb-2 mt-2" >
                                    <div class="card-header bg-info bg-gradient border-dark text-white"><b style="font-size: 0.9rem;"><svg class="hdr-icon"><use href="#icon-scale"></use></svg> BANK LOAN TOTAL</b></div>
                                    <div class="card-body text-secondary">
                                        <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                                            <?php
                                            $total_bl = abs($total_bli) + abs($saldoakhir * $rates3);
                                            ?>
                                            IDR <?= number_format($total_bl,0); ?></p>
                                            <div class="card-updated"><i class="fa fa-clock-o"></i> Updated <?= $bank_updated_at; ?></div>
                                        </div>
                                    </div>
                                    <div class="card border-dark mb-2" >
                                        <div class="card-header border-dark"><b style="font-size: 1rem;">
                                            <?php
                                            $total_limit = $limit_idr + $limit_convert;
                                            ?>
                                            <svg class="hdr-icon"><use href="#icon-gauge"></use></svg> LIMIT : IDR <?= number_format($total_limit,0); ?></b></div>
                                            <div class="card-body text-secondary">
                                                <div id="chartdiv5"></div>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-2" >
                                            <div class="card-header border-dark"><b style="font-size: 1rem;"><svg class="hdr-icon"><use href="#icon-trending-up"></use></svg> LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b><button type="button" class="btn-detail-trigger btn-detail-trigger--light" onclick="openmodalloantotal()">Detail <svg class="hdr-icon"><use href="#icon-arrow-right"></use></svg></button></div>
                                            <div class="card-body text-secondary">
                                                <div id="chartdiv6"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalloanidr" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-secondary bg-gradient text-white">
                                        <h4 class="modal-title" id="text-tittle">IDR Loan Balance (Month over Month)</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="chartloanidr"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <div class="modal fade" id="modalloanusd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary bg-gradient text-white">
                                    <h4 class="modal-title" id="text-tittle">USD Loan Balance (Month over Month)</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                </div>
                                <div class="modal-body">
                                    <div id="chartloanusd"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="modal fade" id="modalloantotal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-secondary bg-gradient text-white">
                                <h4 class="modal-title" id="text-tittle">Total Loan Balance (Month over Month)</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                            </div>
                            <div class="modal-body">
                                <div id="chartloantotal"></div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="modal fade" id="modalcoh" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success bg-gradient text-white">
                            <h4 class="modal-title" id="text-tittle">Cash on Hand (Month over Month) in Million IDR</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                        </div>
                        <div class="modal-body">
                            <div id="chartcoh"></div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="modal fade" id="modalcib" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success bg-gradient text-white">
                        <h4 class="modal-title" id="text-tittle">Cash in Banks (Month over Month) in Million IDR</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    </div>
                    <div class="modal-body">
                        <div id="chartcib"></div>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="modaltc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success bg-gradient text-white">
                    <h4 class="modal-title" id="text-tittle">Total Cash (Month over Month) in Million IDR</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                </div>
                <div class="modal-body">
                    <div id="charttc"></div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modaldetcoh" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header--detail">
                <h4 class="modal-title" id="jdl_coh"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <div id="detail_coh"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaldetcib" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header--detail">
                <h4 class="modal-title" id="jdl_cib"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <div id="detail_cib"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modaldettc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header--detail">
                <h4 class="modal-title" id="jdl_tc"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <div id="detail_tc"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function openmodalloanidr(){
        $("#modalloanidr").modal("show");
    }

    function openmodalloanusd(){
        $("#modalloanusd").modal("show");
    }

    function openmodalloantotal(){
        $("#modalloantotal").modal("show");
    }

    function openmodalcoh(){
        $("#modalcoh").modal("show");
    }

    function openmodalcib(){
        $("#modalcib").modal("show");
    }

    function openmodaltc(){
        $("#modaltc").modal("show");
    }
</script>

<script>
    var options = {
      series: [{
          name: 'Total Cash',
          data: [<?php
              // Gabungan CASH IN BANK + CASH ON HAND per bulan (tahun berjalan) -
              // union dari kedua sumber (bank & kas) dengan logic ending-balance
              // & rate yang sama persis dengan chart masing-masing di atas.
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln, ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT m.bank_account AS acc, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(m.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                                      AND mr.tanggal <= COALESCE(
                                          (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                                           WHERE r2.akun = m.bank_account AND r2.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r2.status != 'Cancel'),
                                          LEAST(mo.month_end, CURDATE())
                                      )
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM b_masterbank m
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
                        LEFT JOIN b_reportbank r ON r.akun = m.bank_account
                        GROUP BY m.bank_account, mo.bln, mo.month_end, s.amount

                        UNION ALL

                        SELECT c.no_coa AS acc, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(s.curr IS NULL OR s.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= LEAST(mo.month_end, CURDATE())
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM mastercoa_v2 c
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
                        LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
                        WHERE c.ind_categori5 = 'KAS'
                        GROUP BY c.no_coa, mo.bln, mo.month_end, s.amount, s.curr
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_and_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#1f8a4c'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#charttc"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash in Banks',
          data: [<?php
              // Ending balance riil per bulan (tahun berjalan), sumber & logic sama
              // dengan card total di atas - cuma di-pivot per bulan Jan..Dec. Bulan
              // yang belum lewat tetap 0 (IF t.bln <= MONTH(CURDATE())), dan bulan
              // berjalan di-cap ke CURDATE() (LEAST) supaya sama persis dengan angka
              // di card CASH IN BANK.
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln,
                           ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT m.bank_account, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(m.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                                      AND mr.tanggal <= COALESCE(
                                          (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                                           WHERE r2.akun = m.bank_account AND r2.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r2.status != 'Cancel'),
                                          LEAST(mo.month_end, CURDATE())
                                      )
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM b_masterbank m
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
                        LEFT JOIN b_reportbank r ON r.akun = m.bank_account
                        GROUP BY m.bank_account, mo.bln, mo.month_end, s.amount
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_in_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#1f8a4c'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcib"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash On Hand',
          data: [<?php
              // Ending balance riil per bulan (tahun berjalan) untuk akun kas
              // (mastercoa_v2 ind_categori5='KAS' + b_saldoawal_pettycash +
              // c_report_pettycash) - pola sama dengan chart CASH IN BANK.
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln,
                           ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT c.no_coa, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(s.curr IS NULL OR s.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= LEAST(mo.month_end, CURDATE())
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM mastercoa_v2 c
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
                        LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
                        WHERE c.ind_categori5 = 'KAS'
                        GROUP BY c.no_coa, mo.bln, mo.month_end, s.amount, s.curr
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_on_hand.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_coh').html(data);
                        $('#jdl_coh').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcoh').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#1f8a4c'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
     $sql_bln = mysqli_query($conn2, "
WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln
");

      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcoh"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M");
              $tahun = date("Y"); 

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01','2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#2a78d6'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloantotal"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#2a78d6'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanusd"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#2a78d6'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanidr"), options);
chart.render();
</script>

<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $tahun = date("Y");
        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bli = (abs($total_bli) / $limit_idr) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bli ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>

<script>
    var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

    $bulan_list = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $bulan_list[] = [
            'bulan' => date('M', $date),
            'tahun' => date('Y', $date)
        ];
    }

    $data = [];

    foreach ($bulan_list as $bln) {

        $tahun_tb = $bln['tahun'];
        $bulan_tb = $bln['bulan'];

        $sql = mysqli_query($conn2,"
            SELECT 
            ROUND(
                ABS(
                    SUM(IF(no_coa='2.20.01', saldo_$bulan_tb,0)) +
                    SUM(IF(no_coa='1.10.01' AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                ) / 1000000,2
            ) total
            FROM b_trial_balance_$tahun_tb
        ");

        $row = mysqli_fetch_assoc($sql);
        $data[] = $row['total'] ?? 0;
    }

    echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#2a78d6'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
categories: [<?php

    $cat = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $cat[] = "'".date('M Y', $date)."'";
    }

    echo implode(",", $cat);

?>],
axisBorder: { show: false },
axisTicks: { show: false }
},
yaxis: {
labels: {
    formatter: function (val) {
        return val.toLocaleString('en-US');
    }
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv2"), options);
chart.render();

</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv3");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        // $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        // $row_blu = mysqli_fetch_array($sql_blu);
        // $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        // $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        if ($saldoakhir > 0) {
            $saldoakhirnya = 0;
        }else{
            $saldoakhirnya = $saldoakhir;
        }

        $chart_blu = (abs($saldoakhirnya * $rates3) / $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: '<?= $chart_blu ?>',
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>
<!-- select CONCAT('round(abs(sum(saldo2 /1000000)),2) saldo2') -->
<script>
   var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

    $bulan_list = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $bulan_list[] = [
            'bulan' => date('M', $date),
            'tahun' => date('Y', $date)
        ];
    }

    $data = [];

    foreach ($bulan_list as $bln) {

        $tahun_tb = $bln['tahun'];
        $bulan_tb = $bln['bulan'];

        $sql = mysqli_query($conn2,"
            SELECT 
            ROUND(
                ABS(
                    SUM(IF(no_coa='2.20.02', saldo_$bulan_tb,0)) +
                    SUM(IF(no_coa='1.10.02' AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                ) / 1000000,2
            ) total
            FROM b_trial_balance_$tahun_tb
        ");

        $row = mysqli_fetch_assoc($sql);
        $data[] = $row['total'] ?? 0;
    }

    echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#2a78d6'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
categories: [<?php
    $cat = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $cat[] = "'".date('M Y', $date)."'";
    }
    echo implode(",", $cat);
?>],
axisBorder: { show: false },
axisTicks: { show: false }
},
yaxis: {
labels: {
    formatter: function (val) {
        return val.toLocaleString('en-US');
    }
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv4"), options);
chart.render();

</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv5");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
            UNION
            select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        $row_blu = mysqli_fetch_array($sql_blu);
        $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bl = (abs($total_bli) + abs($saldoakhir * $rates3)) / ($limit_idr + $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bl ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>


<script>

var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

        $bulan_list = [];
        for ($i = 3; $i >= 1; $i--) {
            $date = strtotime("-$i month");
            $bulan_list[] = [
                'bulan' => date('M', $date),
                'tahun' => date('Y', $date)
            ];
        }

        $data = [];

        foreach ($bulan_list as $bln) {

            $bulan_tb = $bln['bulan'];
            $tahun_tb = $bln['tahun'];

            $sql = mysqli_query($conn2,"
                SELECT 
                ROUND(
                    ABS(
                        SUM(IF(no_coa IN ('2.20.01','2.20.02'), saldo_$bulan_tb,0)) +
                        SUM(IF(no_coa IN ('1.10.01','1.10.02') AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                    ) / 1000000,2
                ) total
                FROM b_trial_balance_$tahun_tb
            ");

            $row = mysqli_fetch_assoc($sql);
            $data[] = $row['total'] ?? 0;
        }

        echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#2a78d6'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
    categories: [<?php
        $cat = [];
        for ($i = 3; $i >= 1; $i--) {
            $date = strtotime("-$i month");
            $cat[] = "'".date('M Y', $date)."'";
        }
        echo implode(",", $cat);
    ?>],
    axisBorder: { show: false },
    axisTicks: { show: false }
},
yaxis: {
    labels: {
        show: false,
        formatter: function (val) {
            return val.toLocaleString('en-US');
        }
    }
},
tooltip: {
    enabled: true,
    y: {
        formatter: function(val) {
            return val.toLocaleString('en-US') + " Mio";
        }
    }
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv6"), options);
chart.render();

</script>
