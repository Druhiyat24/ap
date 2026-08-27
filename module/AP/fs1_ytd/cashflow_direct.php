<style>
/* Reskin CSS-only, pola sama persis dgn fs1_ytd/statement_profit_loss.php
   (FS1 YTD SPL) - dulu file ini TIDAK punya class sama sekali. Query/logic
   SQL & PHP (termasuk perhitungan pinjaman/reval yang kompleks di bawah)
   SAMA SEKALI TIDAK diubah - cuma markup+attribute per elemen presentasi
   (judul, section header, total, tombol Export) yang diganti dari inline
   style ke class. */
#cfd1ytd .card-body {
  background: #f9fafb;
}
#cfd1ytd table {
  color: #2c3e50;
}
table th, table td {
  padding: 0 !important;
}

.laporan-container {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  padding: 22px 28px 20px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
}

.laporan-table {
  font-size: 13.5px;
  margin: auto;
  width: 95%;
  border-collapse: collapse;
  color: #2c3e50;
}

.judul-left,
.judul-right {
  font-weight: 700;
  font-size: 16.5px;
  color: #1e3a8a;
  letter-spacing: .2px;
  line-height: 1.3 !important;
  padding-bottom: 2px !important;
  padding-top: 6px !important;
}
.judul-left { text-align: left; }
.judul-right {
  text-align: right;
  font-style: italic;
  font-size: 15px;
  color: #6b7280;
  font-weight: 600;
}

.desc-left,
.desc-right {
  color: #777;
  font-size: 12.5px;
}
.desc-left { text-align: left; }
.desc-right {
  text-align: right;
  font-style: italic;
  color: #999;
}

.judul-periode {
  text-align: right;
  border-bottom: 2px solid #1e3a8a;
  color: #1e3a8a;
  font-weight: 600;
  padding-bottom: 6px !important;
}

.section-left,
.section-right {
  font-weight: bold;
  color: #1e3a8a;
  font-size: 13.5px;
  letter-spacing: .2px;
}
.section-left { text-align: left; }
.section-right {
  text-align: right;
  font-style: italic;
  color: #6b7280;
  font-weight: 600;
}

.item-left {
  text-align: left;
  padding: 4px 0 !important;
}
.item-right {
  text-align: right;
  font-variant-numeric: tabular-nums;
}
.item-italic {
  text-align: right;
  font-style: italic;
  color: #6b7280;
}
#cfd1ytd .laporan-table tr:hover .item-left,
#cfd1ytd .laporan-table tr:hover .item-right {
  background-color: rgba(37, 99, 235, 0.06);
}

.total-line {
  line-height: 28px;
  font-weight: bold;
}
.total-left { text-align: left; }
.total-right {
  text-align: right;
  font-variant-numeric: tabular-nums;
  border-top: 2px solid #94a3c4;
}
.total-italic {
  text-align: right;
  font-style: italic;
  color: #6b7280;
}

.grand-total {
  background: #e8edfa;
  font-weight: bold;
  line-height: 30px;
}
.grand-left {
  text-align: left;
  color: #1e3a8a;
}
.grand-right {
  text-align: right;
  color: #1e3a8a;
  font-variant-numeric: tabular-nums;
  border-top: 2px solid #1e3a8a;
}
.grand-italic {
  text-align: right;
  font-style: italic;
  color: #5b6a94;
}

.export-buttons {
  display: flex;
  justify-content: flex-end;
  margin: 4px 4px 14px;
}
.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: .2px;
  padding: 8px 18px 8px 14px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  transition: box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  text-decoration: none;
}
.btn-export.excel {
  background: linear-gradient(135deg, #1f7a4d, #14532d);
  box-shadow: 0 2px 6px rgba(20, 83, 45, 0.28);
}
.btn-export.excel:hover {
  background: linear-gradient(135deg, #23935d, #185c36);
  box-shadow: 0 4px 10px rgba(20, 83, 45, 0.35);
  transform: translateY(-1px);
  color: #fff;
}
.btn-export:active {
  transform: translateY(0);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
}

/* ===== Freeze judul (header) FS1 YTD CF Direct - pola sama dgn FS2. Scoped #cfd1ytd. */
#cfd1ytd .laporan-container {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#cfd1ytd .laporan-container::-webkit-scrollbar { height: 10px; width: 10px; }
#cfd1ytd .laporan-container::-webkit-scrollbar-track { background: #f1f4fa; }
#cfd1ytd .laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#cfd1ytd .laporan-table { border-collapse: separate; border-spacing: 0; }
#cfd1ytd .laporan-table thead { position: sticky; top: 0; z-index: 5; }
#cfd1ytd .laporan-table thead th { background: #fafafa; }
#cfd1ytd .laporan-table thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
/* Baris TOTAL diberi latar biru muda #e8edfa spt grand total di SFP/SPL. */
#cfd1ytd .laporan-table .total-line { background: #e8edfa; }
</style>

<?php
        // $status = isset($_POST['status']) ? $_POST['status']: null;
  $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
  $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
  $tanggal_awal = date("Y-m-d",strtotime($start_date));
  $tanggal_akhir = date("Y-m-d",strtotime($end_date));
  $tanggal1 = isset($tanggal_awal) ? $tanggal_awal : 0;
  $tanggal2 = isset($tanggal_akhir) ? $tanggal_akhir : 0;
  $kata_awal = date("M",strtotime($start_date));
  $tengah = '_';
  $kata_akhir = date("Y",strtotime($start_date));
  $kata_filter = $kata_awal . $tengah . $kata_akhir;

  if($tanggal2 < $tanggal1){
    echo "";
}
else{

    echo '<div class="export-buttons mt-2">
    <a target="_blank" class="btn-export excel" href="ekspor_cf_direct_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'">
      <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
        <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
        <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
        <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
      </svg>
      Export Excel
    </a>
    </div>';
}
?>
<div id="cfd1ytd" class="table-responsive mt-1">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <div class="laporan-container">
    <table class="laporan-table" border="0" role="grid" cellspacing="0">
        <thead>
        <tr>
            <th class="judul-left">PT NIRWANA ALABARE GARMENT</th>
            <th></th>
            <th class="judul-right">PT NIRWANA ALABARE GARMENT</th>
        </tr>
        <tr>
            <th class="judul-left">LAPORAN ARUS KAS - METODE LANGSUNG</th>
            <th></th>
            <th class="judul-right">STATEMENTS OF CASH FLOW - DIRECT METHOD</th>
        </tr>
        <tr>
            <th class="judul-left">UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th></th>
            <th class="judul-right">FOR THE PERIODS ENDED <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
        </tr>

        <tr>
            <th class="desc-left">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th></th>
            <th class="desc-right">(Expressed in Rupiah, unless otherwise stated)</th>
        </tr>
        <tr>
            <th></th>
            <th class="judul-periode"><?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?>.</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <!-- Aktivitas Operasi -->

        <tr>
            <th class="section-left">Arus Kas dari Aktivitas Operasi</th>
            <th></th>
            <th class="section-right">Cash Flow from Operating Activities</th>
        </tr>
        <?php
        $nama_type ='';
        $Status = '';
        $start_date ='';
        $end_date ='';
        $date_now = date("Y-m-d");
        $tanggal_awal = date("Y-m-d",strtotime($date_now ));
        $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
        $bulan_awal = date("m",strtotime($date_now));
        $bulan_akhir = date("m",strtotime($date_now));  
        $tahun_awal = date("Y",strtotime($date_now));
        $tahun_akhir = date("Y",strtotime($date_now));
        $kata_awal = date("M",strtotime($date_now));
        $tengah = '_';
        $kata_akhir = date("Y",strtotime($date_now));
        $kata_filter = $kata_awal . $tengah . $kata_akhir;
        $kata_filter2 = $kata_awal . $tengah . $kata_akhir;                 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null; 
            $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
            $start_date = date("d-m-Y",strtotime($_POST['start_date']));
            $end_date = date("d-m-Y",strtotime($_POST['end_date'])); 

            $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
            $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

            $bulan_awal = date("m",strtotime($_POST['start_date']));
            $bulan_akhir = date("m",strtotime($_POST['end_date']));  
            $tahun_awal = date("Y",strtotime($_POST['start_date']));
            $tahun_akhir = date("Y",strtotime($_POST['end_date']));

            $kata_awal = date("M",strtotime($_POST['start_date']));
            $tengah = '_';
            $kata_akhir = date("Y",strtotime($_POST['start_date']));
            $kata_filter = $kata_awal . $tengah . $kata_akhir;

            $kata_awal2 = date("M",strtotime($_POST['end_date']));
            $tengah2 = '_';
            $kata_akhir2 = date("Y",strtotime($_POST['end_date']));
            $kata_filter2 = $kata_awal2 . $tengah2 . $kata_akhir2;


        }

        $sql = mysqli_query($conn2,"WITH
accounts AS (
  SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

revaluasi as (select '2' id, periode, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg GROUP BY periode),

pembayaran as (select a.id, periode, sub_kategori, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a where a.id = 2 ORDER BY a.id, periode ASC),
          
    query_lama as (select 
          a.id,
          b.periode, 
          a.nama_pilihan AS sub_kategori, 
          a.nama_pilihan_eng AS sub_kategori_eng, 
          total
          FROM 
          (SELECT * FROM tb_master_pilihan WHERE status = 'Y') a
          INNER JOIN (
              SELECT id, ind_name, '$tahun_awal-01' periode, coalesce(saldo_jan,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-02' periode, coalesce(saldo_feb,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-03' periode, coalesce(saldo_mar,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-04' periode, coalesce(saldo_apr,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-05' periode, coalesce(saldo_may,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-06' periode, coalesce(saldo_jun,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-07' periode, coalesce(saldo_jul,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-08' periode, coalesce(saldo_aug,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-09' periode, coalesce(saldo_sep,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-10' periode, coalesce(saldo_oct,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-11' periode, coalesce(saldo_nov,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
              UNION ALL
              SELECT id, ind_name, '$tahun_awal-12' periode, coalesce(saldo_dec,0) total FROM tb_monthly_$tahun_awal GROUP BY id, ind_name
          ) b 
          ON b.ind_name = a.nama_pilihan
          WHERE a.type_pilihan = 'Arus Kas dari Aktivitas Operasi' and a.id != 2
          ORDER BY a.id ASC),
          
hasil as (select * from (select * from query_lama
          UNION ALL
          select '2' id, a.periode, sub_kategori, sub_kategori_eng, (total_all + revaluasi) total from pembayaran a INNER JOIN revaluasi b on b.id = a.id and b.periode = a.periode) a order by periode, id asc)
          
  select id, sub_kategori, sub_kategori_eng, sum(total) total from hasil WHERE periode
BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m') group by id
          
          ");

        $sql2 = mysqli_query($conn2,"select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(total) total from (select * from tb_master_pilihan where status = 'Y') a inner join (SELECT 
            id,
            ind_name,
            COALESCE((
            (CASE WHEN '$tahun_awal-01' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_jan ELSE 0 END) +
            (CASE WHEN '$tahun_awal-02' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_feb ELSE 0 END) +
            (CASE WHEN '$tahun_awal-03' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_mar ELSE 0 END) +
            (CASE WHEN '$tahun_awal-04' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_apr ELSE 0 END) +
            (CASE WHEN '$tahun_awal-05' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_may ELSE 0 END) +
            (CASE WHEN '$tahun_awal-06' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_jun ELSE 0 END) +
            (CASE WHEN '$tahun_awal-07' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_jul ELSE 0 END) +
            (CASE WHEN '$tahun_awal-08' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_aug ELSE 0 END) +
            (CASE WHEN '$tahun_awal-09' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_sep ELSE 0 END) +
            (CASE WHEN '$tahun_awal-10' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_oct ELSE 0 END) +
            (CASE WHEN '$tahun_awal-11' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_nov ELSE 0 END) +
            (CASE WHEN '$tahun_awal-12' BETWEEN LEFT('$tanggal_awal', 7) AND LEFT('$tanggal_akhir', 7) THEN saldo_dec ELSE 0 END)
            ),0) AS total
            FROM tb_monthly_$tahun_awal
            GROUP BY id) b on b.ind_name = a.nama_pilihan where type_pilihan = 'Arus Kas dari Aktivitas Investasi' GROUP BY nama_pilihan order by a.id asc");

        if($tanggal_akhir < $tanggal_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_aktivitas_operasi = 0;
            while($row = mysqli_fetch_array($sql)){
                $aktivitasoperasi = isset($row['total']) ? $row['total'] : 0;
                $subkategori = $row['sub_kategori'];

                if ($subkategori == 'Pembayaran Kepada Pemasok Lain-Lain') {
                    $aktivitas_operasi_ = $aktivitasoperasi;
                }else{
                    $aktivitas_operasi_ = $aktivitasoperasi;
                }

                if ($aktivitas_operasi_ > 0) {
                    $aktivitas_operasi = number_format($aktivitas_operasi_,2);
                }else{
                    $aktivitas_operasi = '('.number_format(abs($aktivitas_operasi_),2).')';
                }

                $total_aktivitas_operasi += $aktivitas_operasi_;
                if ($total_aktivitas_operasi > 0) {
                    $total_aktivitas_operasi_ = number_format($total_aktivitas_operasi,2);
                }else{
                    $total_aktivitas_operasi_ = '('.number_format(abs($total_aktivitas_operasi),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row['sub_kategori'].'</td>
                <td class="item-right">'.$aktivitas_operasi.'</td>
                <td class="item-italic">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }

            //TES

            $sql_tes = mysqli_query($conn2,"WITH
accounts AS (
  SELECT profit_center, no_coa, akun, SUM($kata_filter) AS saldo_awal
  FROM (
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'

    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.41', '008-759-5858', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.41') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.42', '008-751-5757', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.42') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

reval AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center
  GROUP BY c.profit_center
),
revaluasi as (select '2' id, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg),

pembayaran as (select a.id, sub_kategori, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a where a.id = 2 ORDER BY a.id ASC)
          
          select sub_kategori, (total_all + revaluasi) total, sub_kategori_eng from pembayaran a INNER JOIN revaluasi b on b.id = a.id");

            // while($row_tes = mysqli_fetch_array($sql_tes)){

            //   $pembayaran_pemasok = isset($row_tes['total']) ? $row_tes['total'] : 0;
            //     if ($pembayaran_pemasok > 0) {
            //         $pembayaran_pemasok_view = number_format($pembayaran_pemasok,2);
            //     }else{
            //         $pembayaran_pemasok_view = '('.number_format(abs($pembayaran_pemasok),2).')';
            //     }

            //     echo '<tr>
            //     <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row_tes['sub_kategori'].'</td>
            //     <td style="text-align: right;vertical-align: middle;width: 16%;">'.$pembayaran_pemasok_view.'</td>
            //     <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row_tes['sub_kategori_eng'].'</td>
            //     </tr>';
            // }

            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th class="total-right">'.$total_aktivitas_operasi_.'</th>
            <th class="total-italic">Cash flow used from operating activities</th>
            </tr>
            <tr>
            <th class="section-left">Arus Kas dari Aktivitas Investasi</th>
            <th></th>
            <th class="section-right">Cash Flow from Investing Activities</th>
            </tr>';

            $total_aktivitas_investasi = 0;
            $bersih_kas_setarakas = 0;
            $bersih_kas_setarakas_ = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $aktivitas_investasi = isset($row2['total']) ? $row2['total'] : 0;
                if ($aktivitas_investasi > 0) {
                    $aktivitas_investasi = number_format($aktivitas_investasi,2);
                }else{
                    $aktivitas_investasi = '('.number_format(abs($aktivitas_investasi),2).')';
                }

                $total_aktivitas_investasi += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_aktivitas_investasi > 0) {
                    $total_aktivitas_investasi_ = number_format($total_aktivitas_investasi,2);
                }else{
                    $total_aktivitas_investasi_ = '('.number_format(abs($total_aktivitas_investasi),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row2['sub_kategori'].'</td>
                <td class="item-right">'.$aktivitas_investasi.'</td>
                <td class="item-italic">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang digunakan untuk aktivitas investasi</th>
            <th class="total-right">'.$total_aktivitas_investasi_.'</th>
            <th class="total-italic">Cash flow used from investing activities</th>
            </tr>
            <tr>
            <th class="section-left">Arus Kas dari Aktivitas Pendanaan</th>
            <th></th>
            <th class="section-right">Cash Flow from Financing Activities</th>
            </tr>';
            ?>

            <tr>
                <td class="item-left">Penerimaan pinjaman</td>
                <td class="item-right">
                    <?php
                    $sql_Penerimaan = mysqli_query($conn2,"WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

pivot AS (
  SELECT
  14 id,
    periode,
    SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN profit_center='NAG' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAK,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '14' ORDER BY a.id ASC),

data_fix as (SELECT a.periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       (penerimaan_NAG + b.total_nag) AS total_nag,
       (penerimaan_NAK + b.total_nak) AS total_nak,
       (penerimaan_TOTAL + b.total_all) AS total_all
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode

UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_NAG - b.total_nag) pembayaran_NAG,
       - (pembayaran_NAK - b.total_nak) pembayaran_NAK,
       - (pembayaran_TOTAL - b.total_all) pembayaran_TOTAL
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Penerimaan Pinjaman' AND periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')
");

                    $row_penerimaan = mysqli_fetch_array($sql_Penerimaan);
                    $total_penerimaan = isset($row_penerimaan['total_all']) ? $row_penerimaan['total_all'] : 0;

                    $totalcf_17 = $total_penerimaan;
                    if ($totalcf_17 > 0) {
                        $total_17 = number_format($totalcf_17,2);
                    }else{
                        $total_17 = '('.number_format(abs($totalcf_17),2).')';
                    }
                    echo $total_17;
                    ?>
                </td>
                <td class="item-italic">Proceeds from loans</td>
            </tr>
            <tr>
                <td class="item-left">Pembayaran pinjaman</td>
                <td class="item-right">
                    <?php
                    $sql_Pembayaran = mysqli_query($conn2,"WITH
accounts AS (
SELECT profit_center, no_coa, akun, periode, SUM(jan_$tahun_awal) AS saldo_awal
  FROM (
      SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-01' periode, s.jan_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-02' periode, s.feb_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-03' periode, s.mar_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-04' periode, s.apr_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-05' periode, s.may_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-06' periode, s.jun_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-08' periode, s.aug_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-09' periode, s.sep_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-10' periode, s.oct_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-11' periode, s.nov_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
    UNION ALL
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.02', '008-998-1982', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-12' periode, s.dec_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
  ) AS a
  GROUP BY profit_center, no_coa, akun, periode
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

reval AS (
  SELECT l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m') periode,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa, DATE_FORMAT(tgl_journal,'%Y-%m')
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun, a.periode,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center AND j.periode = a.periode
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center, c.periode,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi,
    SUM(IF(r.no_coa IN ('1.10.02', '1.10.01'),COALESCE(r.debit_idr,0) - COALESCE(r.credit_idr,0),0)) AS revaluasi_nya
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center AND r.periode = c.periode
  GROUP BY c.profit_center, c.periode
),

pivot AS (
  SELECT
  15 id,
    periode,
    SUM(CASE WHEN profit_center='NAG' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN penerimaan_pinjaman ELSE 0 END) AS penerimaan_NAK,
    SUM(penerimaan_pinjaman) AS penerimaan_TOTAL,
    SUM(CASE WHEN profit_center='NAG' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAG,
    SUM(CASE WHEN profit_center='NAK' THEN (pembayaran_pinjaman) ELSE 0 END) AS pembayaran_NAK,
    SUM(pembayaran_pinjaman) AS pembayaran_TOTAL
  FROM agg GROUP BY periode
),

other_value as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a 
CROSS JOIN
(
    SELECT '$tahun_awal-01' periode
    UNION ALL SELECT '$tahun_awal-02'
    UNION ALL SELECT '$tahun_awal-03'
    UNION ALL SELECT '$tahun_awal-04'
    UNION ALL SELECT '$tahun_awal-05'
    UNION ALL SELECT '$tahun_awal-06'
    UNION ALL SELECT '$tahun_awal-07'
    UNION ALL SELECT '$tahun_awal-08'
    UNION ALL SELECT '$tahun_awal-09'
    UNION ALL SELECT '$tahun_awal-10'
    UNION ALL SELECT '$tahun_awal-11'
    UNION ALL SELECT '$tahun_awal-12'
) p
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) b on b.ind_name = a.nama_pilihan and b.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAG' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) c on c.ind_name = a.nama_pilihan and c.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) d on d.ind_name = a.nama_pilihan and d.periode = p.periode
          LEFT JOIN
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '15' ORDER BY a.id ASC),

data_fix as (SELECT periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       penerimaan_NAG AS total_nag,
       penerimaan_NAK AS total_nak,
       penerimaan_TOTAL AS total_all
FROM pivot

UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_NAG - b.total_nag) pembayaran_NAG,
       - (pembayaran_NAK - b.total_nak) pembayaran_NAK,
       - (pembayaran_TOTAL - b.total_all) pembayaran_TOTAL
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Pembayaran Pinjaman' AND periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')
");

                    $row_Pembayaran = mysqli_fetch_array($sql_Pembayaran);
                    $total_Pembayaran = isset($row_Pembayaran['total_all']) ? $row_Pembayaran['total_all'] : 0;

                    $sql_reval_Pembayaran = mysqli_query($conn2,"WITH
accounts AS (
  SELECT profit_center, no_coa, akun, SUM($kata_filter) AS saldo_awal
  FROM (
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'

    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NAK', '1.10.01', '008-997-1979', s.$kata_filter
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'

  ) AS a
  GROUP BY profit_center, no_coa, akun
),

journal_sums AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.rate * l.debit)  AS debit,
         SUM(l.rate * l.credit) AS credit
  FROM tbl_list_journal l
  WHERE l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND (l.no_journal LIKE '%BM%' OR l.no_journal LIKE '%BK%')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

reval AS (
  SELECT l.profit_center, l.no_coa,
         SUM(l.debit_idr)  AS debit_idr,
         SUM(l.credit_idr) AS credit_idr
  FROM tbl_list_journal l
  WHERE (l.keterangan LIKE '%REVALUASI%' OR l.keterangan LIKE '%REVALUATION%')
    AND l.tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
    AND l.profit_center IN ('NAG','NAK')
  GROUP BY l.profit_center, l.no_coa
),

base AS (
  SELECT a.profit_center, a.no_coa, a.akun,
         a.saldo_awal,
         COALESCE(j.debit, 0)  AS debit,
         COALESCE(j.credit,0)  AS credit,
         (a.saldo_awal + COALESCE(j.debit,0) - COALESCE(j.credit,0)) AS saldo_akhir
  FROM accounts a
  LEFT JOIN journal_sums j ON j.no_coa = a.no_coa AND j.profit_center = a.profit_center
),

calc AS (
  SELECT b.*,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN b.credit - b.saldo_awal
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN b.credit
      ELSE 0
    END AS penerimaan_pinjaman,
    CASE
      WHEN b.saldo_awal > 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir < 0 THEN ABS(b.debit)
      WHEN b.saldo_awal < 0 AND b.saldo_akhir > 0 THEN ABS(b.saldo_awal)
      ELSE 0
    END AS pembayaran_pinjaman
  FROM base b
),

agg AS (
  SELECT
    c.profit_center,
    SUM(COALESCE(c.penerimaan_pinjaman,0)) AS penerimaan_pinjaman,
    SUM(COALESCE(c.pembayaran_pinjaman,0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.debit_idr,0) END
    ,0)) AS debit_revaluasi,
    SUM(COALESCE(
        CASE WHEN c.saldo_akhir < 0 THEN 0 ELSE COALESCE(r.credit_idr,0) END
    ,0)) AS credit_revaluasi
  FROM calc c
  LEFT JOIN reval r
    ON r.no_coa = c.no_coa AND r.profit_center = c.profit_center
  GROUP BY c.profit_center
),
revaluasi as (select '2' id, sum(debit_revaluasi - credit_revaluasi) revaluasi from agg),

pembayaran as (select a.id, sub_kategori, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a where a.id = 2 ORDER BY a.id ASC)
          
          select sub_kategori, (total_all + revaluasi) total, revaluasi, sub_kategori_eng from pembayaran a INNER JOIN revaluasi b on b.id = a.id

                        ");

                    $row_reval_Pembayaran = mysqli_fetch_array($sql_reval_Pembayaran);
                    $total_reval_Pembayaran = isset($row_reval_Pembayaran['revaluasi']) ? $row_reval_Pembayaran['revaluasi'] : 0;

                    $totalcf_18 = $total_Pembayaran;
                    if ($totalcf_18 > 0) {
                        $total_18 = number_format($totalcf_18,2);
                    }else{
                        $total_18 = '('.number_format(abs($totalcf_18),2).')';
                    }
                    echo $total_18;
                    ?>
                </td>
                <td class="item-italic">Payment of loans</td>
            </tr>
            <?php
            $total_aktivitas_pendanaan_ = 0;
            $total_aktivitas_pendanaan = $totalcf_17 + $totalcf_18;
            if ($total_aktivitas_pendanaan > 0) {
                $total_aktivitas_pendanaan_ = number_format($total_aktivitas_pendanaan,2);
            }else{
                $total_aktivitas_pendanaan_ = '('.number_format(abs($total_aktivitas_pendanaan),2).')';
            }

            $bersih_kas_setarakas = $total_aktivitas_operasi + $total_aktivitas_investasi + $totalcf_17 + $totalcf_18;
            if ($bersih_kas_setarakas > 0) {
                $bersih_kas_setarakas_ = number_format($bersih_kas_setarakas,2);
            }else{
                $bersih_kas_setarakas_ = '('.number_format(abs($bersih_kas_setarakas),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang digunakan untuk aktivitas pendanaan</th>
            <th class="total-right">'.$total_aktivitas_pendanaan_.'</th>
            <th class="total-italic">Cash flow used from financing activities</th>
            </tr>
            <tr class="total-line">
            <th class="total-left">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th class="total-right">'.$bersih_kas_setarakas_.'</th>
            <th class="total-italic">Cash Flow from Financing Activities</th>
            </tr>';
            ?>
            <tr>
                <th class="item-left">Kas dan setara kas pada awal periode</th>
                <th class="item-right">
                    <?php
                    $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
                        left join
                        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                        on coa.no_coa = saldo.nocoa
                        left join
                        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

                    $row = mysqli_fetch_array($sql);
                    $total = isset($row['total']) ? $row['total'] : 0;
                    if ($total > 0) {
                        $total_ = number_format($total,2);
                    }else{
                        $total_ = '('.number_format(abs($total),2).')';
                    }
                    echo $total_;
                    ?>
                </th>
                <th class="item-italic">Cash and cash equivalent at the beginning of period</th>
            </tr>
            <tr class="grand-total">
                <th class="grand-left">Kas dan setara kas pada akhir periode</th>
                <th class="grand-right">
                    <?php
                    $totalcf_kas = $total + $bersih_kas_setarakas;
                    if ($totalcf_kas > 0) {
                        $total_jmlkas = number_format($totalcf_kas,2);
                    }else{
                        $total_jmlkas = '('.number_format(abs($totalcf_kas),2).')';
                    }
                    echo $total_jmlkas;
                    ?>
                </th>
                <th class="grand-italic">Cash and cash equivalent at the end of period</th>
            </tr>
            <?php


        }
        ?>
        </tbody>
    </table>
      </div>
    </div>
  </div>
</div>
