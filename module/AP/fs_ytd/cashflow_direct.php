<style>
  #sfp .card-body {
    background: #f9fafb;
  }
  #sfp table {
    color: #333;
  }
  #sfp th, #sfp td {
    padding: 8px 10px;
  }
  #sfp .table-primary {
    background-color: #e9f3ff !important;
  }
  #sfp .btn-success {
    background: linear-gradient(90deg, #28a745, #218838);
    border: none;
    transition: 0.3s;
  }
  #sfp .btn-success:hover {
    opacity: 0.9;
  }

  table th, table td {
    padding: 0 !important;
  }


  .laporan-container-cfdirect {
    border: 2px solid #2c3e50;
    border-radius: 10px;
    padding: 15px 25px;
    background: #fafafa;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  .laporan-table-cfdirect {
    font-size: 14px;
    margin: auto;
    width: 100%;
    border-collapse: collapse;
    color: #2c3e50; 
  }

  /* ===== Header Styles ===== */
  .judul-left,
  .judul-right {
    font-weight: bold;
    line-height: 1.2 !important; 
    padding-bottom: 0;
    padding-top: 0;
  }

  .judul-left {
    text-align: left;
  }

  .judul-right {
    text-align: right;
    font-style: italic;
  }

  .subjudul-left,
  .subjudul-right {
    line-height: 0.7 !important; 
    padding-top: 0;
    font-weight: bold;
    padding-bottom: 0;
    padding-top: 0;
  }

  .subjudul-left {
    text-align: left;
  }

  .subjudul-right {
    text-align: right;
    font-style: italic;
  }

  .tanggal-left {
    text-align: left;
    font-weight: 500;
  }

  .tanggal-right {
    text-align: right;
    font-style: italic;
  }

  .desc-left,
  .desc-right {
    color: #555;
    font-size: 12px;
  }

  .desc-left {
    text-align: left;
  }

  .desc-right {
    text-align: right;
    font-style: italic;
  }

  .periode {
    text-align: center;
    border-bottom: 3px solid #000;
    font-weight: bold;
    padding-bottom: 5px;
    width: 220px !important;
  }

  .persentage {
    text-align: center;
    border-bottom: 3px solid #000;
    font-weight: bold;
    padding-bottom: 5px;
  }

  .isi-periode {
    text-align: center;
    border-bottom: 3px solid #000;
    font-weight: bold;
    padding-bottom: 5px;
    width: 180px !important;
  }

  .isi-persentage {
    text-align: center;
    border-bottom: 3px solid #000;
    font-weight: bold;
    padding-bottom: 5px;
    width: 50px !important;
  }

  .judul-periode {
    text-align: center;
    font-weight: bold;
    padding-bottom: 5px;
  }

  /* ===== Sections ===== */
  .section-left,
  .section-right {
    font-weight: bold;
    color: #2c3e50;
  }

  .section-left {
    text-align: left;
  }

  .section-right {
    text-align: right;
    font-style: italic;
  }

  .subsection-left,
  .subsection-right {
    font-weight: bold;
  }

  .subsection-left {
    text-align: left;
  }

  .subsection-right {
    text-align: right;
    font-style: italic;
  }

  /* ===== Data Rows ===== */
  .item-left {
    text-align: left;
    padding: 3px 0;
  }

  .item-right {
    text-align: right;
  }

  .item-italic {
    text-align: right;
    font-style: italic;
  }

  /* ===== Totals ===== */
  .total-line {
    border-top: 2px solid #000;
    line-height: 28px;
    font-weight: bold;
  }

  .total-left {
    text-align: left;
  }

  .total-right {
    text-align: right;
  }

  .total-italic {
    text-align: right;
    font-style: italic;
  }

  /* ===== Grand Total ===== */
  .grand-total {
    border-top: 3px double #000;
    background: #f5f5f5;
    font-weight: bold;
    line-height: 30px;
  }

  .grand-left {
    text-align: left;
  }

  .grand-right {
    text-align: right;
  }

  .grand-italic {
    text-align: right;
    font-style: italic;
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

  .export-buttons {
    dicfdirectay: flex;
    justify-content: flex-start; /* tombol di kiri */
    align-items: center;
    gap: 10px; /* jarak antar tombol */
    margin-left: 15px; /* sedikit geser dari kiri */
  }

  .btn-export {
    background: #2c3e50;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s ease;
  }

  .btn-export:hover {
    background: #34495e;
  }

  .btn-export.excel {
    background: #27ae60;
  }

  .btn-export.pdf {
    background: #e74c3c;
  }
</style>


<div class="export-buttons mt-2 text-left ml-4">
  <button id="btnExcel-cfdirect" class="btn-export excel">
    📊 Export Excel
  </button>
  <button id="btnPDF-cfdirect" class="btn-export pdf">
    🖨️ Print PDF
  </button>
</div>

<div class="table-responsive">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4" mt-0>
      <div class="laporan-container-cfdirect" id="laporan-cfdirect-ytd">
        <table class="laporan-table-cfdirect" border="0" role="grid" cellspacing="0">

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
    SUM(penerimaan_pinjaman - credit_revaluasi) AS penerimaan_TOTAL,
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
    SUM(penerimaan_pinjaman - credit_revaluasi) AS penerimaan_TOTAL,
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

select sub_kategori, sub_kategori_eng, sum(total_nag) total_nag, sum(total_nak) total_nak, sum(total_all) total_all from data_fix where sub_kategori = 'Penerimaan Pinjaman' AND periode BETWEEN DATE_FORMAT('$tahun_awal-11-01','%Y-%m') AND DATE_FORMAT('$tahun_awal-12-31','%Y-%m')
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



