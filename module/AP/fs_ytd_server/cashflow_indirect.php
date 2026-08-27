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


  .laporan-container-cfindirect {
    border: 2px solid #2c3e50;
    border-radius: 10px;
    padding: 15px 25px;
    background: #fafafa;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  .laporan-table-cfindirect {
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
    text-transform: uppercase;
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
    dicfindirectay: flex;
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
  <button id="btnExcel-cfindirect" class="btn-export excel">
    📊 Export Excel
  </button>
  <button id="btnPDF-cfindirect" class="btn-export pdf">
    🖨️ Print PDF
  </button>
</div>

<div class="table-responsive">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4" mt-0>
      <div class="laporan-container-cfindirect" id="laporan-cfindirect-ytd">
        <table class="laporan-table-cfindirect" border="0" role="grid" cellspacing="0">

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

          <th class="judul-left">LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</th>
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
          <th class="judul-right">STATEMENTS OF CASH FLOW - INDIRECT METHOD</th>
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
            $colspan = '';
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
        <tr class="spacer-small"></tr>
        <tr>
          <td class="item-left">Laba (Rugi) Bersih</td>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$total_lrsp_nag}</td>";
            echo "<td class='item-right'>{$total_lrsp_nak}</td>";
            echo "<td class='item-right'>{$total_lrsp_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$total_lrsp_nag}</td>";
          }else{
            echo "<td class='item-right'>{$total_lrsp_nak}</td>";
          }
          ?>
          <td class="item-italic">Net Income (Loss)</td>
        </tr>
        <tr>
          <td class="item-left">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          $sql = mysqli_query($conn2,"select id_indirect, total_nag, total_nak, (total_nag + total_nak) total_all from(select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nag, ((sum(COALESCE(d.debit_idr,0))-sum(COALESCE(d.credit_idr,0))) * -1) total_nak from (select no_coa,id_indirect from mastercoa_v2) b inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by id) a on b.no_coa = a.coa_no LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by id) d on b.no_coa = d.coa_no GROUP BY b.id_indirect) a where a.id_indirect = '19'");
          $row = mysqli_fetch_array($sql);
          $penyusutan_aset_tetap_nag = $row['total_nag'] ?? 0;
          $penyusutan_aset_tetap_nak = $row['total_nak'] ?? 0;
          $penyusutan_aset_tetap_all = $row['total_all'] ?? 0;
          $apat_nag = $penyusutan_aset_tetap_nag > 0 ? number_format($penyusutan_aset_tetap_nag,2) : '(' . number_format(abs($penyusutan_aset_tetap_nag),2) . ')';
          $apat_nak = $penyusutan_aset_tetap_nak > 0 ? number_format($penyusutan_aset_tetap_nak,2) : '(' . number_format(abs($penyusutan_aset_tetap_nak),2) . ')';
          $apat_all = $penyusutan_aset_tetap_all > 0 ? number_format($penyusutan_aset_tetap_all,2) : '(' . number_format(abs($penyusutan_aset_tetap_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$apat_nag}</td>";
            echo "<td class='item-right'>{$apat_nak}</td>";
            echo "<td class='item-right'>{$apat_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$apat_nag}</td>";
          }else{
            echo "<td class='item-right'>{$apat_nak}</td>";
          }
          ?>
          <td class="item-italic">Accumulated Depreciation Of Fixed Asset Adjustment</td>
        </tr>
        <tr>
          <td class="item-left">Penyesuaian Laba Ditahan Tahun Lalu</td>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          $laba_ditahan_tahun_lalu_nag = 0;
          $laba_ditahan_tahun_lalu_nak = 0;
          $laba_ditahan_tahun_lalu_all = 0;
          $ldtl_nag = $laba_ditahan_tahun_lalu_nag > 0 ? number_format($laba_ditahan_tahun_lalu_nag,2) : '(' . number_format(abs($laba_ditahan_tahun_lalu_nag),2) . ')';
          $ldtl_nak = $laba_ditahan_tahun_lalu_nak > 0 ? number_format($laba_ditahan_tahun_lalu_nak,2) : '(' . number_format(abs($laba_ditahan_tahun_lalu_nak),2) . ')';
          $ldtl_all = $laba_ditahan_tahun_lalu_all > 0 ? number_format($laba_ditahan_tahun_lalu_all,2) : '(' . number_format(abs($laba_ditahan_tahun_lalu_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$ldtl_nag}</td>";
            echo "<td class='item-right'>{$ldtl_nak}</td>";
            echo "<td class='item-right'>{$ldtl_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$ldtl_nag}</td>";
          }else{
            echo "<td class='item-right'>{$ldtl_nak}</td>";
          }
          ?>
          <td class="item-italic">Previous Year Retained Earning Adjustment</td>
        </tr>
        <tr class="spacer-small"></tr>
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
        $sql2 = mysqli_query($conn2,"select id,sub_kategori,COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, COALESCE(total_all,0) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Operasi_ind')) a left JOIN
         (select a.id id_indirect,a.ind_name, COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, (COALESCE(total_nag,0) + COALESCE(total_nak,0)) total_all from (select id,ind_name from tbl_master_cashflow) a LEFT JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nag from (select no_coa,id_indirect from mastercoa_v2) b inner join (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) b on b.id_indirect = a.id LEFT JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nak from (select no_coa,id_indirect from mastercoa_v2) b inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) c on c.id_indirect = a.id ) b on b.ind_name = a.sub_kategori order by id asc");
        $total_aktivitas_operasi_nag = 0;
        $total_aktivitas_operasi_nak = 0;
        $total_aktivitas_operasi_all = 0;
        while($row2 = mysqli_fetch_array($sql2)){
          $aktivitas_operasi_nag = $row2['total_nag'] ?? 0;
          $aktivitas_operasi_nak = $row2['total_nak'] ?? 0;
          $aktivitas_operasi_all = $row2['total_all'] ?? 0;
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
          <td class='item-left'>{$row2['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akao_nag}</td>";
            echo "<td class='item-right'>{$akao_nak}</td>";
            echo "<td class='item-right'>{$akao_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akao_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akao_nak}</td>";
          }
          echo "<td class='item-italic'>{$row2['sub_kategori_eng']}</td>
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
        $sql3 = mysqli_query($conn2,"select id,sub_kategori,COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, COALESCE(total_all,0) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Investasi_ind')) a left JOIN
         (select a.id id_indirect,a.ind_name, COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, (COALESCE(total_nag,0) + COALESCE(total_nak,0)) total_all from (select id,ind_name from tbl_master_cashflow where status = 'Active' and id >= 4) a INNER JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nag from (select no_coa,id_indirect from mastercoa_v2) b inner join (select id,ind_name from tbl_master_cashflow where status = 'Active' and id >= 4) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) b on b.id_indirect = a.id LEFT JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nak from (select no_coa,id_indirect from mastercoa_v2) b inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) c on c.id_indirect = a.id ) b on b.ind_name = a.sub_kategori order by id asc");
        $total_aktivitas_investasi_nag = 0;
        $total_aktivitas_investasi_nak = 0;
        $total_aktivitas_investasi_all = 0;
        while($row3 = mysqli_fetch_array($sql3)){
          $aktivitas_investasi_nag = $row3['total_nag'] ?? 0;
          $aktivitas_investasi_nak = $row3['total_nak'] ?? 0;
          $aktivitas_investasi_all = $row3['total_all'] ?? 0;
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
          <td class='item-left'>{$row3['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$akai_nag}</td>";
            echo "<td class='item-right'>{$akai_nak}</td>";
            echo "<td class='item-right'>{$akai_all}</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$akai_nag}</td>";
          }else{
            echo "<td class='item-right'>{$akai_nak}</td>";
          }
          echo "<td class='item-italic'>{$row3['sub_kategori_eng']}</td>
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
        $sql4 = mysqli_query($conn2,"select id,sub_kategori,COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, COALESCE(total_all,0) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Pendanaan_ind')) a left JOIN
         (select a.id id_indirect,a.ind_name, COALESCE(total_nag,0) total_nag, COALESCE(total_nak,0) total_nak, (COALESCE(total_nag,0) + COALESCE(total_nak,0)) total_all from (select id,ind_name from tbl_master_cashflow) a LEFT JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nag from (select no_coa,id_indirect from mastercoa_v2) b inner join (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) b on b.id_indirect = a.id LEFT JOIN (select id_indirect,ind_name, ((sum(COALESCE(a.debit_idr,0))-sum(COALESCE(a.credit_idr,0))) * -1) total_nak from (select no_coa,id_indirect from mastercoa_v2) b inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect LEFT JOIN (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by id) a on b.no_coa = a.coa_no GROUP BY b.id_indirect) c on c.id_indirect = a.id ) b on b.ind_name = a.sub_kategori order by id asc");
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

          $kas_setara_kas_nag = $total_aktivitas_operasi_nag + $total_aktivitas_investasi_nag + $total_aktivitas_pendanaan_nag + $total_laba_rugi_bersih_nag + $penyusutan_aset_tetap_nag + $laba_ditahan_tahun_lalu_nag;
          $kas_setara_kas_nak = $total_aktivitas_operasi_nak + $total_aktivitas_investasi_nak + $total_aktivitas_pendanaan_nak + $total_laba_rugi_bersih_nak + $penyusutan_aset_tetap_nak + $laba_ditahan_tahun_lalu_nak;
          $kas_setara_kas_all = $total_aktivitas_operasi_all + $total_aktivitas_investasi_all + $total_aktivitas_pendanaan_all + $total_laba_rugi_bersih_all + $penyusutan_aset_tetap_all + $laba_ditahan_tahun_lalu_all;
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


