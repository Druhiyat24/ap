<style>
  /* Reskin CSS-only - struktur HTML/PHP tabel TIDAK disentuh (lihat catatan
     lengkap di fs_ytd/statement_financial_position.php) - cuma warna/spacing
     diselaraskan ke bahasa visual biru-emas tab lain. Juga memperbaiki typo
     lama "dicfdirectay: flex" (harusnya "display: flex" - hasil find-replace
     serampangan waktu file ini di-clone) yang bikin tombol Export/Print
     dulu tidak sejajar horizontal. */
  #cf-direct .card-body {
    background: #f9fafb;
  }
  #cf-direct table {
    color: #2c3e50;
  }
  #cf-direct th, #cf-direct td {
    padding: 8px 10px;
  }
  #cf-direct .table-primary {
    background-color: #e9f3ff !important;
  }

  table th, table td {
    padding: 0 !important;
  }

  .laporan-container-cfdirect {
    border: 1px solid #dbe3f0;
    border-radius: 14px;
    padding: 22px 28px 20px;
    background: #fafafa;
    box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  }

  .laporan-table-cfdirect {
    font-size: 13.5px;
    margin: auto;
    width: 100%;
    border-collapse: collapse;
    color: #2c3e50;
  }

  /* ===== Header Styles ===== */
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

  .judul-left {
    text-align: left;
  }

  .judul-right {
    text-align: right;
    font-style: italic;
    font-size: 15px;
    color: #6b7280;
    font-weight: 600;
  }

  .subjudul-left,
  .subjudul-right {
    line-height: 1.3 !important;
    padding-top: 0;
    font-weight: 700;
    font-size: 14.5px;
    color: #2c3e50;
    padding-bottom: 2px !important;
  }

  .subjudul-left {
    text-align: left;
  }

  .subjudul-right {
    text-align: right;
    font-style: italic;
    color: #6b7280;
    font-weight: 500;
  }

  .tanggal-left {
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
  }

  .tanggal-right {
    text-align: right;
    font-style: italic;
    color: #6b7280;
  }

  .desc-left,
  .desc-right {
    color: #777;
    font-size: 12.5px;
  }

  .desc-left {
    text-align: left;
  }

  .desc-right {
    text-align: right;
    font-style: italic;
    color: #999;
  }

  .periode,
  .persentage,
  .isi-periode,
  .isi-persentage {
    text-align: center;
    border-bottom: 2px solid #1e3a8a;
    color: #1e3a8a;
    font-weight: 600;
    padding-bottom: 6px !important;
  }

  .periode {
    width: 220px !important;
  }

  .isi-periode {
    width: 180px !important;
  }

  .isi-persentage {
    width: 50px !important;
  }

  .judul-periode {
    text-align: center;
    font-weight: 600;
    color: #2c3e50;
    padding-bottom: 6px !important;
  }

  /* ===== Sections ===== */
  .section-left,
  .section-right {
    font-weight: bold;
    color: #1e3a8a;
    font-size: 13.5px;
    letter-spacing: .2px;
  }

  .section-left {
    text-align: left;
  }

  .section-right {
    text-align: right;
    font-style: italic;
    color: #6b7280;
    font-weight: 600;
  }

  .subsection-left,
  .subsection-right {
    font-weight: bold;
    color: #2c3e50;
  }

  .subsection-left {
    text-align: left;
  }

  .subsection-right {
    text-align: right;
    font-style: italic;
    color: #6b7280;
  }

  /* ===== Data Rows ===== */
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

  #cf-direct .laporan-table-cfdirect tr:hover .item-left,
  #cf-direct .laporan-table-cfdirect tr:hover .item-right {
    background-color: rgba(37, 99, 235, 0.06);
  }

  /* ===== Totals ===== */
  .total-line {
    border-top: 1px solid #94a3c4;
    line-height: 28px;
    font-weight: bold;
  }

  .total-left {
    text-align: left;
  }

  .total-right {
    text-align: right;
    font-variant-numeric: tabular-nums;
  }

  .total-italic {
    text-align: right;
    font-style: italic;
    color: #6b7280;
  }

  /* ===== Grand Total ===== */
  .grand-total {
    border-top: 2px solid #1e3a8a;
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
  }

  .grand-italic {
    text-align: right;
    font-style: italic;
    color: #5b6a94;
  }

  /* ===== Spacers ===== */
  .spacer {
    height: 15px;
  }

  .spacer-mid {
    height: 10px;
  }

  .spacer-small {
    height: 5px;
  }

  /* ===== Tombol Export/Print - pill gradien + ikon SVG, konsisten dgn
     tombol Export Excel di tab lain. Dulu tombol kotak flat & emoji. */
  .export-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
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
  }

  .btn-export.excel {
    background: linear-gradient(135deg, #1f7a4d, #14532d);
    box-shadow: 0 2px 6px rgba(20, 83, 45, 0.28);
  }

  .btn-export.excel:hover {
    background: linear-gradient(135deg, #23935d, #185c36);
    box-shadow: 0 4px 10px rgba(20, 83, 45, 0.35);
    transform: translateY(-1px);
  }

  .btn-export.pdf {
    background: linear-gradient(135deg, #d64545, #a52a2a);
    box-shadow: 0 2px 6px rgba(165, 42, 42, 0.28);
  }

  .btn-export.pdf:hover {
    background: linear-gradient(135deg, #e05a5a, #b93333);
    box-shadow: 0 4px 10px rgba(165, 42, 42, 0.35);
    transform: translateY(-1px);
  }

  .btn-export:active {
    transform: translateY(0);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
  }

/* ===== Freeze judul (header) CF Direct YTD - pola sama persis dgn tab SFP.
   Scoped #cf-direct, class-based (Excel/PDF tidak berubah). */
#cf-direct .laporan-container-cfdirect {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#cf-direct .laporan-container-cfdirect::-webkit-scrollbar { height: 10px; width: 10px; }
#cf-direct .laporan-container-cfdirect::-webkit-scrollbar-track { background: #f1f4fa; }
#cf-direct .laporan-container-cfdirect::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#cf-direct .laporan-table-cfdirect { border-collapse: separate; border-spacing: 0; }
#cf-direct .laporan-table-cfdirect thead { position: sticky; top: 0; z-index: 5; }
#cf-direct .laporan-table-cfdirect thead th { background: #fafafa; }
#cf-direct .laporan-table-cfdirect thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
/* Baris TOTAL (mis. "Arus kas ... aktivitas operasi") diberi latar biru muda
   #e8edfa - sama seperti baris grand total di SFP/SPL - supaya menonjol.
   Scoped #cf-direct saja. */
#cf-direct .laporan-table-cfdirect .total-line { background: #e8edfa; }
</style>


<div class="export-buttons mt-2">
  <button id="btnExcel-cfdirect" class="btn-export excel">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
      <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
    Export Excel
  </button>
  <button id="btnPDF-cfdirect" class="btn-export pdf">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
      <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M6 6.5h8M6 10h8M6 13.5h5" stroke="#ffffff" stroke-width="1.1" stroke-linecap="round"/>
    </svg>
    Print PDF
  </button>
</div>

<div class="table-responsive">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4" mt-0>
      <div class="laporan-container-cfdirect" id="laporan-cfdirect-ytd">
        <table class="laporan-table-cfdirect" border="0" role="grid" cellspacing="0">
          <thead>

          <!-- Header Judul -->
          <tr>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
            }

            if ($profit_center == 'ALL') {
              echo '<th class="judul-left">PT NIRWANA ALABARE</th>
              <th></th>
              <th></th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE</th>';
            }elseif ($profit_center == 'NAG') {
              echo '<th class="judul-left">PT NIRWANA ALABARE GARMENT</th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE GARMENT</th>';
            }else{
              echo '<th class="judul-left">PT NIRWANA ALABARE KNITTING</th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE KNITTING</th>';
            }
            ?>
          </tr>

          <tr>
          <th class="judul-left">LAPORAN ARUS KAS - METODE LANGSUNG</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="judul-right">STATEMENTS OF CASH FLOW - DIRECT METHOD</th>
        </tr>

        <tr>
          <th class="judul-left">
            UNTUK TAHUN YANG BERAKHIR PADA TANGGAL 
            <?php
            $sqlakhir = mysqli_query($conn2,"SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan = '$bulan_akhir' AND tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = $rowakhir['tgl_akhir'] ?? null;
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian_indonesia.1252');
            echo strtoupper(strftime("%d %B %Y", strtotime($tgl_akhir)));
            ?>
          </th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="judul-right">
            FOR THE YEARS ENDED 
            <?php echo strtoupper(date("d F Y", strtotime($tgl_akhir))); ?>
          </th>
        </tr>
        <tr>
          <th class="desc-left">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="desc-right">(Expressed in Rupiah, unless otherwise stated)</th>
        </tr>

        <tr>
          <th></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            $colspan = ' colspan="3" ';
          }else{
            $colspan = ' ';
          }
          ?>
          <th <?= $colspan; ?> class="judul-periode">
            YTD <?php echo strtoupper(date("d F Y", strtotime($tgl_akhir))); ?>
          </th>
          <th></th>
        </tr>

        <tr>
          <th></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th class="periode">Nirwana Alabare Garment</th>
            <th class="periode">Nirwana Alabare Knitting</th>
            <th class="periode">Total</th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th class="periode">Nirwana Alabare Garment</th>';
          }else{
            echo '<th class="periode">Nirwana Alabare Knitting</th>';
          }
          ?>
          <th></th>
        </tr>
        </thead>
        <tbody>

        <!-- Section PENJUALAN KOTOR -->
        <tr>
          <th class="subsection-left"><?= strtoupper('Arus Kas dari Aktivitas Operasi'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="subsection-right"><?= strtoupper('Cash Flow from Operating Activities'); ?></th>

        </tr>
        <?php
        $sqlawal = mysqli_query($conn2,"select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal'");
        $rowawal = mysqli_fetch_array($sqlawal);
        $tanggal_awal = date("Y-m-d",strtotime($rowawal['tgl_awal']));

        $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
        $rowakhir = mysqli_fetch_array($sqlakhir);
        $tanggal_akhir = date("Y-m-d",strtotime($rowakhir['tgl_akhir'])); 

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

revaluasi as (select '2' id, periode, if(profit_center = 'NAG',sum(debit_revaluasi - credit_revaluasi),0) revaluasi_nag, if(profit_center = 'NAK',sum(debit_revaluasi - credit_revaluasi),0) revaluasi_nak, sum(debit_revaluasi - credit_revaluasi) revaluasi_all from agg GROUP BY periode),

pembayaran as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, p.periode AS periode, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
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
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, p.periode) a ORDER BY periode, a.id ASC),
          
  hasil as (select a.id, a.periode, sub_kategori, sub_kategori_eng, (COALESCE(total_nag,0) + COALESCE(revaluasi_nag,0)) total_nag, (COALESCE(total_nak,0) + COALESCE(revaluasi_nak,0)) total_nak, (COALESCE(total_all,0) + COALESCE(revaluasi_all,0)) total_all from pembayaran a LEFT JOIN revaluasi b on b.id = a.id and b.periode = a.periode order by periode, a.id asc)
  
  select id, sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from hasil WHERE periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m') group by id ORDER BY id asc");
        $total_aktivitas_operasi_nag = 0;
        $total_aktivitas_operasi_nak = 0;
        $total_aktivitas_operasi_all = 0;
        while($row = mysqli_fetch_array($sql)){
          $aktivitas_operasi_nag = $row['total_nag'] ?? 0;
          $aktivitas_operasi_nak = $row['total_nak'] ?? 0;
          $aktivitas_operasi_all = $row['total_all'] ?? 0;
          $akao_nag = $aktivitas_operasi_nag > 0 ? number_format($aktivitas_operasi_nag,2) : '(' . number_format(abs($aktivitas_operasi_nag),2) . ')';
          $akao_nak = $aktivitas_operasi_nak > 0 ? number_format($aktivitas_operasi_nak,2) : '(' . number_format(abs($aktivitas_operasi_nak),2) . ')';
          $akao_all = $aktivitas_operasi_all > 0 ? number_format($aktivitas_operasi_all,2) : '(' . number_format(abs($aktivitas_operasi_all),2) . ')';
          $total_aktivitas_operasi_nag += $aktivitas_operasi_nag;
          $total_aktivitas_operasi_nak += $aktivitas_operasi_nak;
          $total_aktivitas_operasi_all += $aktivitas_operasi_all;
          $total_akao_nag = $total_aktivitas_operasi_nag > 0 ? number_format($total_aktivitas_operasi_nag,2) : '(' . number_format(abs($total_aktivitas_operasi_nag),2) . ')';
          $total_akao_nak = $total_aktivitas_operasi_nak > 0 ? number_format($total_aktivitas_operasi_nak,2) : '(' . number_format(abs($total_aktivitas_operasi_nak),2) . ')';
          $total_akao_all = $total_aktivitas_operasi_all > 0 ? number_format($total_aktivitas_operasi_all,2) : '(' . number_format(abs($total_aktivitas_operasi_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akao_nag}</td>";
            echo "<td class='item-right'>{$akao_nak}</td>";
            echo "<td class='item-right'>{$akao_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akao_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akao_nak}</td>";
          }
          echo "<td class='item-italic'>{$row['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left"><?= strtoupper('Arus kas yang digunakan untuk aktivitas operasi'); ?></th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_akao_nag}</td>";
            echo "<td class='total-right'>{$total_akao_nak}</td>";
            echo "<td class='total-right'>{$total_akao_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_akao_nag}</td>";
          }else{
            echo "<td class='total-right'>{$total_akao_nak}</td>";
          }
          ?>
          <th class="total-italic"><?= strtoupper('Cash flow used from operating activities'); ?></th>
        </tr>
        <tr class="spacer-small"></tr>
        <tr>
          <th class="subsection-left"><?= strtoupper('Arus Kas dari Aktivitas Investasi'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="subsection-right"><?= strtoupper('Cash Flow from Investing Activities'); ?></th>

        </tr>
        <?php
        $sql = mysqli_query($conn2,"select sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Investasi') a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a ORDER BY a.id ASC");
        $total_aktivitas_investasi_nag = 0;
        $total_aktivitas_investasi_nak = 0;
        $total_aktivitas_investasi_all = 0;
        while($row = mysqli_fetch_array($sql)){
          $aktivitas_investasi_nag = $row['total_nag'] ?? 0;
          $aktivitas_investasi_nak = $row['total_nak'] ?? 0;
          $aktivitas_investasi_all = $row['total_all'] ?? 0;
          $akai_nag = $aktivitas_investasi_nag > 0 ? number_format($aktivitas_investasi_nag,2) : '(' . number_format(abs($aktivitas_investasi_nag),2) . ')';
          $akai_nak = $aktivitas_investasi_nak > 0 ? number_format($aktivitas_investasi_nak,2) : '(' . number_format(abs($aktivitas_investasi_nak),2) . ')';
          $akai_all = $aktivitas_investasi_all > 0 ? number_format($aktivitas_investasi_all,2) : '(' . number_format(abs($aktivitas_investasi_all),2) . ')';
          $total_aktivitas_investasi_nag += $aktivitas_investasi_nag;
          $total_aktivitas_investasi_nak += $aktivitas_investasi_nak;
          $total_aktivitas_investasi_all += $aktivitas_investasi_all;
          $total_akai_nag = $total_aktivitas_investasi_nag > 0 ? number_format($total_aktivitas_investasi_nag,2) : '(' . number_format(abs($total_aktivitas_investasi_nag),2) . ')';
          $total_akai_nak = $total_aktivitas_investasi_nak > 0 ? number_format($total_aktivitas_investasi_nak,2) : '(' . number_format(abs($total_aktivitas_investasi_nak),2) . ')';
          $total_akai_all = $total_aktivitas_investasi_all > 0 ? number_format($total_aktivitas_investasi_all,2) : '(' . number_format(abs($total_aktivitas_investasi_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akai_nag}</td>";
            echo "<td class='item-right'>{$akai_nak}</td>";
            echo "<td class='item-right'>{$akai_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akai_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akai_nak}</td>";
          }
          echo "<td class='item-italic'>{$row['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left"><?= strtoupper('Arus kas yang digunakan untuk aktivitas investasi'); ?></th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_akai_nag}</td>";
            echo "<td class='total-right'>{$total_akai_nak}</td>";
            echo "<td class='total-right'>{$total_akai_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_akai_nag}</td>";
          }else{
            echo "<td class='total-right'>{$total_akai_nak}</td>";
          }
          ?>
          <th class="total-italic"><?= strtoupper('Cash flow used from investing activities'); ?></th>
        </tr>
        <tr class="spacer-small"></tr>

        <tr>
          <th class="subsection-left"><?= strtoupper('Arus Kas dari Aktivitas Pendanaan'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>';
          }else{
            echo '<th></th>';
          }
          ?>
          <th class="subsection-right"><?= strtoupper('Cash Flow from Financing Activities'); ?></th>

        </tr>
        <?php
        $sql4 = mysqli_query($conn2,"WITH
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
    
    SELECT 'NAG' AS profit_center, '1.10.02' AS no_coa, '008-998-1982' AS akun, '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAG', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAG'
    UNION ALL
    SELECT 'NAK', '1.10.02', '008-998-1982', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.02','2.20.02') AND s.profit_center = 'NAK'
    UNION ALL
    SELECT 'NK', '1.10.01', '008-997-1979', '$tahun_awal-07' periode, s.jul_$tahun_awal
    FROM fs_saldo_awal_tb s WHERE s.no_coa IN ('1.10.01','2.20.01') AND s.profit_center = 'NAK'
    
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

pivot_bayar AS (
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
          (select c.id,c.ind_name, DATE_FORMAT(tgl_journal,'%Y-%m') periode,sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.no_coa not in ('1.10.02', '1.10.01') and a.profit_center = 'NAK' GROUP BY c.id, DATE_FORMAT(tgl_journal,'%Y-%m')) e on e.ind_name = a.nama_pilihan and e.periode = p.periode GROUP BY a.id, COALESCE(b.periode, c.periode, d.periode, e.periode)) a WHERE a.id = '14' ORDER BY a.id ASC),
          
          other_value_bayar as (select id, periode, sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, COALESCE(b.periode, c.periode, d.periode, e.periode) AS periode, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas pendanaan') a 
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
FROM pivot a left join other_value b on b.id = a.id and b.periode = a.periode),

data_fix_bayar as (SELECT a.periode, 'Penerimaan Pinjaman' AS sub_kategori, 'Proceeds from loans' sub_kategori_eng,
       (penerimaan_NAG + b.total_nag) AS total_nag,
       (penerimaan_NAK + b.total_nak) AS total_nak,
       (penerimaan_TOTAL + b.total_all) AS total_all
FROM pivot_bayar a left join other_value_bayar b on b.id = a.id and b.periode = a.periode
UNION ALL

SELECT a.periode, 'Pembayaran Pinjaman', 'Payment of loans
',
       - (pembayaran_NAG - b.total_nag) pembayaran_NAG,
       - (pembayaran_NAK - b.total_nak) pembayaran_NAK,
       - (pembayaran_TOTAL - b.total_all) pembayaran_TOTAL
FROM pivot_bayar a left join other_value_bayar b on b.id = a.id and b.periode = a.periode)

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Penerimaan Pinjaman' AND periode BETWEEN DATE_FORMAT('$tahun_awal-01-01','%Y-%m') AND DATE_FORMAT('$tahun_awal-12-31','%Y-%m')
UNION ALL
select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix_bayar where sub_kategori = 'Pembayaran Pinjaman' AND periode BETWEEN DATE_FORMAT('$tanggal_awal','%Y-%m') AND DATE_FORMAT('$tanggal_akhir','%Y-%m')
");
        $total_aktivitas_pendanaan_nag = 0;
        $total_aktivitas_pendanaan_nak = 0;
        $total_aktivitas_pendanaan_all = 0;
        while($row4 = mysqli_fetch_array($sql4)){
          $aktivitas_pendanaan_nag = $row4['total_nag'] ?? 0;
          $aktivitas_pendanaan_nak = $row4['total_nak'] ?? 0;
          $aktivitas_pendanaan_all = $row4['total_all'] ?? 0;
          $akap_nag = $aktivitas_pendanaan_nag > 0 ? number_format($aktivitas_pendanaan_nag,2) : '(' . number_format(abs($aktivitas_pendanaan_nag),2) . ')';
          $akap_nak = $aktivitas_pendanaan_nak > 0 ? number_format($aktivitas_pendanaan_nak,2) : '(' . number_format(abs($aktivitas_pendanaan_nak),2) . ')';
          $akap_all = $aktivitas_pendanaan_all > 0 ? number_format($aktivitas_pendanaan_all,2) : '(' . number_format(abs($aktivitas_pendanaan_all),2) . ')';
          $total_aktivitas_pendanaan_nag += $aktivitas_pendanaan_nag;
          $total_aktivitas_pendanaan_nak += $aktivitas_pendanaan_nak;
          $total_aktivitas_pendanaan_all += $aktivitas_pendanaan_all;
          $total_akap_nag = $total_aktivitas_pendanaan_nag > 0 ? number_format($total_aktivitas_pendanaan_nag,2) : '(' . number_format(abs($total_aktivitas_pendanaan_nag),2) . ')';
          $total_akap_nak = $total_aktivitas_pendanaan_nak > 0 ? number_format($total_aktivitas_pendanaan_nak,2) : '(' . number_format(abs($total_aktivitas_pendanaan_nak),2) . ')';
          $total_akap_all = $total_aktivitas_pendanaan_all > 0 ? number_format($total_aktivitas_pendanaan_all,2) : '(' . number_format(abs($total_aktivitas_pendanaan_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row4['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akap_nag}</td>";
            echo "<td class='item-right'>{$akap_nak}</td>";
            echo "<td class='item-right'>{$akap_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akap_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akap_nak}</td>";
          }
          echo "<td class='item-italic'>{$row4['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>


        <?php
        $sql = mysqli_query($conn2,"select sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Pendanaan' and id not in (14,15)) a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a ORDER BY a.id ASC");
        $total_aktivitas_pendanaan_antar_divisi_nag = 0;
        $total_aktivitas_pendanaan_antar_divisi_nak = 0;
        $total_aktivitas_pendanaan_antar_divisi_all = 0;
        while($row = mysqli_fetch_array($sql)){
          $aktivitas_pendanaan_antar_divisi_nag = $row['total_nag'] ?? 0;
          $aktivitas_pendanaan_antar_divisi_nak = $row['total_nak'] ?? 0;
          $aktivitas_pendanaan_antar_divisi_all = $row['total_all'] ?? 0;
          $akapad_nag = $aktivitas_pendanaan_antar_divisi_nag > 0 ? number_format($aktivitas_pendanaan_antar_divisi_nag,2) : '(' . number_format(abs($aktivitas_pendanaan_antar_divisi_nag),2) . ')';
          $akapad_nak = $aktivitas_pendanaan_antar_divisi_nak > 0 ? number_format($aktivitas_pendanaan_antar_divisi_nak,2) : '(' . number_format(abs($aktivitas_pendanaan_antar_divisi_nak),2) . ')';
          $akapad_all = $aktivitas_pendanaan_antar_divisi_all > 0 ? number_format($aktivitas_pendanaan_antar_divisi_all,2) : '(' . number_format(abs($aktivitas_pendanaan_antar_divisi_all),2) . ')';
          $total_aktivitas_pendanaan_antar_divisi_nag += $aktivitas_pendanaan_antar_divisi_nag;
          $total_aktivitas_pendanaan_antar_divisi_nak += $aktivitas_pendanaan_antar_divisi_nak;
          $total_aktivitas_pendanaan_antar_divisi_all += $aktivitas_pendanaan_antar_divisi_all;
          $total_akapad_nag = $total_aktivitas_pendanaan_antar_divisi_nag > 0 ? number_format($total_aktivitas_pendanaan_antar_divisi_nag,2) : '(' . number_format(abs($total_aktivitas_pendanaan_antar_divisi_nag),2) . ')';
          $total_akapad_nak = $total_aktivitas_pendanaan_antar_divisi_nak > 0 ? number_format($total_aktivitas_pendanaan_antar_divisi_nak,2) : '(' . number_format(abs($total_aktivitas_pendanaan_antar_divisi_nak),2) . ')';
          $total_akapad_all = $total_aktivitas_pendanaan_antar_divisi_all > 0 ? number_format($total_aktivitas_pendanaan_antar_divisi_all,2) : '(' . number_format(abs($total_aktivitas_pendanaan_antar_divisi_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akapad_nag}</td>";
            echo "<td class='item-right'>{$akapad_nak}</td>";
            echo "<td class='item-right'>{$akapad_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akapad_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akapad_nak}</td>";
          }
          echo "<td class='item-italic'>{$row['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left"><?= strtoupper('Arus kas yang diperoleh dari aktivitas pendanaan'); ?></th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_akap_nag}</td>";
            echo "<td class='total-right'>{$total_akap_nak}</td>";
            echo "<td class='total-right'>{$total_akap_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_akap_nag}</td>";
          }else{
            echo "<td class='total-right'>{$total_akap_nak}</td>";
          }
          ?>
          <th class="total-italic"><?= strtoupper('Cash flow generated from financing activities'); ?></th>
        </tr>

        <tr class="spacer-small"></tr>

        <tr>
          <th class="subsection-left"><?= strtoupper('Kenaikan / (Penurunan) bersih kas dan setara kas'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          $kas_setara_kas_nag = $total_aktivitas_operasi_nag + $total_aktivitas_investasi_nag + $total_aktivitas_pendanaan_nag + $total_aktivitas_pendanaan_antar_divisi_nag;
          $kas_setara_kas_nak = $total_aktivitas_operasi_nak + $total_aktivitas_investasi_nak + $total_aktivitas_pendanaan_nak + $total_aktivitas_pendanaan_antar_divisi_nak;
          $kas_setara_kas_all = $total_aktivitas_operasi_all + $total_aktivitas_investasi_all + $total_aktivitas_pendanaan_all + $total_aktivitas_pendanaan_antar_divisi_all;
          $ksk_nag = $kas_setara_kas_nag > 0 ? number_format($kas_setara_kas_nag,2) : '(' . number_format(abs($kas_setara_kas_nag),2) . ')';
          $ksk_nak = $kas_setara_kas_nak > 0 ? number_format($kas_setara_kas_nak,2) : '(' . number_format(abs($kas_setara_kas_nak),2) . ')';
          $ksk_all = $kas_setara_kas_all > 0 ? number_format($kas_setara_kas_all,2) : '(' . number_format(abs($kas_setara_kas_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<th class='item-right'>{$ksk_nag}</th>";
            echo "<th class='item-right'>{$ksk_nak}</th>";
            echo "<th class='item-right'>{$ksk_all}</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='item-right'>{$ksk_nag}</th>";
          }else{
            echo "<th class='item-right'>{$ksk_nak}</th>";
          }

          ?>
          <th class="subsection-right"><?= strtoupper('Net Increase / (Decrease) in cash and cash equivalent'); ?></th>

        </tr>
        <tr class="spacer"></tr>

        <tr>
          <th class="subsection-left"><?= strtoupper('Kas dan setara kas pada awal periode'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          // $kata_filter = nama KOLOM bulan di fs_saldo_awal_tb (jan_2026, feb_2026, ...).
          // Berkas ini dulu memakainya tanpa pernah mendefinisikannya, sehingga SQL-nya
          // jadi "..., as saldo from fs_saldo_awal_tb ..." = SYNTAX ERROR; query gagal
          // diam-diam dan Kas dan Setara Kas pada Awal Periode selalu tampil 0.
          // Disusun dari $bulan_awal/$tahun_awal yang SUDAH disiapkan pemanggil
          // (financial_statement.php / fs_tab_fetch.php) - bukan dari $_POST langsung,
          // supaya tetap benar saat tab ini dimuat lewat AJAX. Pola nilainya sama
          // dengan statement_financial_position.php: <3 huruf bulan>_<tahun>.
          $bln_kf = (int) ($bulan_awal ?? date('m'));
          $thn_kf = (int) ($tahun_awal ?? date('Y'));
          if ($bln_kf < 1 || $bln_kf > 12) { $bln_kf = (int) date('m'); }
          $kata_filter = strtolower(date('M', mktime(0, 0, 0, $bln_kf, 1, $thn_kf))) . '_' . $thn_kf;

          $sql5 = mysqli_query($conn2,"select a.id_ctg4, b.total total_nag, c.total total_nak, (c.total + b.total) total_all from (select id_ctg4 from master_coa_ctg4 where id_ctg4 = '111') a LEFT JOIN
  (select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from fs_saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' and profit_center = 'NAG' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from fs_saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 and profit_center = 'NAG' OR no_coa = '1.10.02' and $kata_filter > 0 and profit_center = 'NAG') saldo
                        left join
                        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                        on coa.no_coa = saldo.nocoa
                        left join
                        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) 
                        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111') b on b.id_ctg4 = a.id_ctg4 LEFT JOIN
  (select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from fs_saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' and profit_center = 'NAK' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from fs_saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 and profit_center = 'NAK' OR no_coa = '1.10.02' and $kata_filter > 0 and profit_center = 'NAK') saldo
                        left join
                        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                        on coa.no_coa = saldo.nocoa
                        left join
                        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) 
                        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111') c on c.id_ctg4 = a.id_ctg4");
          $row5 = mysqli_fetch_array($sql5);
          $kas_awal_periode_nag = $row5['total_nag'] ?? 0;
          $kas_awal_periode_nak = $row5['total_nak'] ?? 0;
          $kas_awal_periode_all = $row5['total_all'] ?? 0;
          $kawp_nag = $kas_awal_periode_nag > 0 ? number_format($kas_awal_periode_nag,2) : '(' . number_format(abs($kas_awal_periode_nag),2) . ')';
          $kawp_nak = $kas_awal_periode_nak > 0 ? number_format($kas_awal_periode_nak,2) : '(' . number_format(abs($kas_awal_periode_nak),2) . ')';
          $kawp_all = $kas_awal_periode_all > 0 ? number_format($kas_awal_periode_all,2) : '(' . number_format(abs($kas_awal_periode_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<th class='item-right'>{$kawp_nag}</th>";
            echo "<th class='item-right'>{$kawp_nak}</th>";
            echo "<th class='item-right'>{$kawp_all}</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='item-right'>{$kawp_nag}</th>";
          }else{
            echo "<th class='item-right'>{$kawp_nak}</th>";
          }

          ?>
          <th class="subsection-right"><?= strtoupper('Cash and cash equivalent at the beginning of period'); ?></th>

        </tr>
        <tr>
          <th class="subsection-left"><?= strtoupper('Kas dan setara kas pada akhir periode'); ?></th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          $kas_akhir_periode_nag = $kas_setara_kas_nag + $kas_awal_periode_nag ;
          $kas_akhir_periode_nak = $kas_setara_kas_nak + $kas_awal_periode_nak ;
          $kas_akhir_periode_all = $kas_setara_kas_all + $kas_awal_periode_all ;
          $kakp_nag = $kas_akhir_periode_nag > 0 ? number_format($kas_akhir_periode_nag,2) : '(' . number_format(abs($kas_akhir_periode_nag),2) . ')';
          $kakp_nak = $kas_akhir_periode_nak > 0 ? number_format($kas_akhir_periode_nak,2) : '(' . number_format(abs($kas_akhir_periode_nak),2) . ')';
          $kakp_all = $kas_akhir_periode_all > 0 ? number_format($kas_akhir_periode_all,2) : '(' . number_format(abs($kas_akhir_periode_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<th class='item-right'>{$kakp_nag}</th>";
            echo "<th class='item-right'>{$kakp_nak}</th>";
            echo "<th class='item-right'>{$kakp_all}</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='item-right'>{$kakp_nag}</th>";
          }else{
            echo "<th class='item-right'>{$kakp_nak}</th>";
          }

          ?>
          <th class="subsection-right"><?= strtoupper('Cash and cash equivalent at the end of period'); ?></th>

        </tr>
        </tbody>

      </table>
    </div>
  </div>
</div>
</div>
<!-- 
CARI NILAI PINJAMAN
SELECT 
    SUM(COALESCE(a.penerimaan_pinjaman, 0)) AS penerimaan_pinjaman,
    SUM(COALESCE(a.pembayaran_pinjaman, 0)) AS pembayaran_pinjaman,
    SUM(COALESCE(
        IF(a.saldo_awal_idr > 0 AND a.saldo_akhir < 0, 0, b.debit_idr),
    0)) AS debit_revaluasi,
    SUM(COALESCE(
        IF(a.saldo_awal_idr > 0 AND a.saldo_akhir < 0, 0, b.credit_idr),
    0)) AS credit_revaluasi

FROM (
    SELECT 
        a.no_coa,
        a.akun,
        a.saldo_awal,
        a.saldo_awal_idr,
        a.debit,
        a.credit,
        a.saldo_akhir,

        CASE
            WHEN a.saldo_awal_idr > 0 AND a.saldo_akhir < 0 THEN a.credit - a.saldo_awal_idr
            WHEN a.saldo_awal_idr < 0 AND a.saldo_akhir < 0 THEN a.credit
            WHEN a.saldo_awal_idr > 0 AND a.saldo_akhir > 0 THEN 0
            WHEN a.saldo_awal_idr < 0 AND a.saldo_akhir > 0 THEN 0
        END AS penerimaan_pinjaman,

        CASE
            WHEN a.saldo_awal_idr > 0 AND a.saldo_akhir < 0 THEN ABS(a.debit)
            WHEN a.saldo_awal_idr < 0 AND a.saldo_akhir < 0 THEN ABS(a.debit)
            WHEN a.saldo_awal_idr > 0 AND a.saldo_akhir > 0 THEN 0
            WHEN a.saldo_awal_idr < 0 AND a.saldo_akhir > 0 THEN ABS(a.saldo_awal_idr)
        END AS pembayaran_pinjaman

    FROM (
        -- ===================== COA 1.10.02 =====================
        SELECT 
            a.no_coa,
            a.akun,
            a.saldo_awal,
            a.saldo_awal_idr,
            a.debit,
            a.credit,
            a.saldo_awal_idr + a.debit - a.credit AS saldo_akhir
        FROM (
            SELECT 
                a.no_coa,
                a.akun,
                a.saldo_awal,
                a.saldo_awal AS saldo_awal_idr,
                c.debit,
                c.credit
            FROM (
                SELECT 
                    '1.10.02' AS no_coa,
                    '008-998-1982' AS akun,
                    SUM(saldo_awal_tb.aug_2025) AS saldo_awal
                FROM saldo_awal_tb
                WHERE saldo_awal_tb.no_coa IN ('1.10.02', '2.20.02')
            ) a
            LEFT JOIN (
                SELECT 
                    tbl_list_journal.no_coa,
                    SUM(tbl_list_journal.rate * tbl_list_journal.debit) AS debit,
                    SUM(tbl_list_journal.rate * tbl_list_journal.credit) AS credit
                FROM tbl_list_journal
                WHERE 
                    tbl_list_journal.tgl_journal BETWEEN (
                        SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
                    ) AND (
                        SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
                    )
                    AND tbl_list_journal.no_coa = '1.10.02'
                    AND (
                        tbl_list_journal.no_journal LIKE '%BM%' OR
                        tbl_list_journal.no_journal LIKE '%BK%'
                    )
            ) c ON c.no_coa = a.no_coa
        ) a

        UNION ALL

        -- ===================== COA 1.10.01 =====================
        SELECT 
            a.no_coa,
            a.akun,
            a.saldo_awal,
            a.saldo_awal_idr,
            a.debit,
            a.credit,
            a.saldo_awal_idr + a.debit - a.credit AS saldo_akhir
        FROM (
            SELECT 
                a.no_coa,
                a.akun,
                a.saldo_awal,
                a.saldo_awal AS saldo_awal_idr,
                c.debit,
                c.credit
            FROM (
                SELECT 
                    '1.10.01' AS no_coa,
                    '008-997-1979' AS akun,
                    SUM(saldo_awal_tb.aug_2025) AS saldo_awal
                FROM saldo_awal_tb
                WHERE saldo_awal_tb.no_coa IN ('1.10.01', '2.20.01')
            ) a
            LEFT JOIN (
                SELECT 
                    tbl_list_journal.no_coa,
                    SUM(tbl_list_journal.rate * tbl_list_journal.debit) AS debit,
                    SUM(tbl_list_journal.rate * tbl_list_journal.credit) AS credit
                FROM tbl_list_journal
                WHERE 
                    tbl_list_journal.tgl_journal BETWEEN (
                        SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
                    ) AND (
                        SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
                    )
                    AND tbl_list_journal.no_coa = '1.10.01'
                    AND (
                        tbl_list_journal.no_journal LIKE '%BM%' OR
                        tbl_list_journal.no_journal LIKE '%BK%'
                    )
            ) c ON c.no_coa = a.no_coa
        ) a
    ) a
) a

LEFT JOIN (SELECT no_coa, profit_center, debit_idr, credit_idr FROM tbl_list_journal WHERE (
            (keterangan LIKE '%REVALUASI%' AND no_coa = '1.10.02') OR
            (keterangan LIKE '%REVALUATION%' AND no_coa = '1.10.02'))
        AND tbl_list_journal.tgl_journal BETWEEN (
            SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
        ) AND (
            SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan = '08' AND tahun = '2025'
        )
) b ON b.no_coa = a.no_coa;
 -->



