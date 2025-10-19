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


  .laporan-container-spl {
    border: 2px solid #2c3e50;
    border-radius: 10px;
    padding: 15px 25px;
    background: #fafafa;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  .laporan-table-spl {
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
    display: flex;
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
  <button id="btnExcel-spl" class="btn-export excel">
    📊 Export Excel
  </button>
  <button id="btnPDF-spl" class="btn-export pdf">
    🖨️ Print PDF
  </button>
</div>

<div class="table-responsive">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4" mt-0>
      <div class="laporan-container-spl" id="laporan-spl-ytd">
        <table class="laporan-table-spl" border="0" role="grid" cellspacing="0">

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
            $colspan = ' colspan="6" ';
          }else{
            $colspan = ' colspan="2" ';
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
        $sql = mysqli_query($conn2,"select sub_kategori, total_nag, total_nak, (total_nag + total_nak) total_all, sub_kategori_eng from (select a.id, a.nama_pilihan sub_kategori, a.nama_pilihan_eng sub_kategori_eng, sum(COALESCE(b.credit,0) - COALESCE(c.debit,0)) total_nag, sum(COALESCE(d.credit,0) - COALESCE(e.debit,0)) total_nak from (SELECT * FROM tb_master_pilihan where status = 'Y' and type_pilihan = 'Arus Kas dari Aktivitas Operasi') a 
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) b on b.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAG' GROUP BY c.id) c on c.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(credit * rate,2)) credit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_credit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) d on d.ind_name = a.nama_pilihan
          LEFT JOIN
          (select c.id,c.ind_name, sum(ROUND(debit * rate,2)) debit from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN tbl_master_cashflow c on c.id = b.id_direct_debit where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') AND (no_journal LIKE '%BM/%' OR no_journal LIKE '%BK/%' OR no_journal LIKE '%RCO/%' OR no_journal LIKE '%RCI/%' OR no_journal LIKE '%KKK/%' OR no_journal LIKE '%KKM/%') and a.profit_center = 'NAK' GROUP BY c.id) e on e.ind_name = a.nama_pilihan GROUP BY a.id) a ORDER BY a.id ASC");
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

          <tr class="grand-total">
            <th class="grand-left">TOTAL ASET</th>
            <?php
            $total_aset_nag = $total_aset_lancar_nag + $total_aset_tidak_lancar_nag;
            $total_aset_nak = $total_aset_lancar_nak + $total_aset_tidak_lancar_nak;
            $total_aset_all = $total_aset_lancar_all + $total_aset_tidak_lancar_all;

            $total_ast_nag = $total_aset_nag > 0 ? number_format($total_aset_nag,2) : '(' . number_format(abs($total_aset_nag),2) . ')';
            $total_ast_nak = $total_aset_nak > 0 ? number_format($total_aset_nak,2) : '(' . number_format(abs($total_aset_nak),2) . ')';
            $total_ast_all = $total_aset_all > 0 ? number_format($total_aset_all,2) : '(' . number_format(abs($total_aset_all),2) . ')';

                  if ($profit_center == 'ALL') {
                    echo "<th class='grand-right'>{$total_ast_nag}</th>";
                    echo "<th class='grand-right'>{$total_ast_nak}</th>";
                    echo "<th class='grand-right'>{$total_ast_all}</th>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<th class='grand-right'>{$total_ast_nag}</th>";
                  }else{
                    echo "<th class='grand-right'>{$total_ast_nak}</th>";
                  }
             ?>
            <th class="grand-italic">TOTAL ASSETS</th>
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



