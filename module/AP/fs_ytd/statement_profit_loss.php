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


<!-- HEADER -->
<!-- <div class=" mt-2">
  <div>

    <?php
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
    $tanggal_awal = date("Y-m-d", strtotime($start_date));
    $tanggal_akhir = date("Y-m-d", strtotime($end_date));
    $kata_awal = date("M", strtotime($start_date));
    $kata_akhir = date("Y", strtotime($start_date));
    $kata_filter = $kata_awal . '_' . $kata_akhir;

    if ($tanggal_akhir >= $tanggal_awal) {
      echo '
      <a target="_blank" 
      href="ekspor_sfp_ytd.php?start_date='.$start_date.'&end_date='.$end_date.'&kata_filter='.$kata_filter.'"
      class="btn btn-success shadow-sm">
      <i class="fa fa-file-excel-o me-2"></i> Export Excel
      </a>';
    }
    ?>

  </div>
</div> -->

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
              <th></th>
              <th></th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE</th>';
            }elseif ($profit_center == 'NAG') {
              echo '<th class="judul-left">PT NIRWANA ALABARE GARMENT</th>
              <th></th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE GARMENT</th>';
            }else{
              echo '<th class="judul-left">PT NIRWANA ALABARE KNITTING</th>
              <th></th>
              <th></th>
              <th class="judul-right">PT NIRWANA ALABARE KNITTING</th>';
            }
            ?>
          </tr>

          <th class="judul-left">LAPORAN LABA ATAU RUGI DAN PENGHASILAN KOMPREHENSIF LAINNYA</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
          }
          ?>
          <th class="judul-right">STATEMENTS OF PROFIT OR LOSS AND OTHER COMPREHENSIVE INCOME</th>
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
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
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
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
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
            echo '<th class="periode" colspan="2">Nirwana Alabare Garment</th>
            <th class="periode" colspan="2">Nirwana Alabare Knitting</th>
            <th class="periode" colspan="2">Total</th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th class="periode" colspan="2">Nirwana Alabare Garment</th>';
          }else{
            echo '<th class="periode" colspan="2">Nirwana Alabare Knitting</th>';
          }
          ?>
          <th></th>
        </tr>

        <!-- Section PENJUALAN KOTOR -->
        <tr class="spacer"></tr>
        <tr>
          <th class="section-left">PENJUALAN KOTOR</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
          }
          ?>
          <th class="section-right">GROSS SALES</th>
        </tr>
        <tr class="spacer-small"></tr>
        <?php

        $sql_nets = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR','RETURN PENJUALAN','POTONGAN PENJUALAN')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $row_nets = mysqli_fetch_array($sql_nets);
        $penjualan_bersih_nag = isset($row_nets['total_nag']) ? $row_nets['total_nag'] : 0;
        $penjualan_bersih_nak = isset($row_nets['total_nak']) ? $row_nets['total_nak'] : 0;
        $penjualan_bersih_all = isset($row_nets['total_all']) ? $row_nets['total_all'] : 0;

        $sql = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_penjualan_kotor_nag = 0;
        $total_penjualan_kotor_nak = 0;
        $total_penjualan_kotor_all = 0;
        while($row = mysqli_fetch_array($sql)){
          $penjualan_kotor_nag = $row['total_nag'] ?? 0;
          $penjualan_kotor_nak = $row['total_nak'] ?? 0;
          $penjualan_kotor_all = $row['total_all'] ?? 0;
          $per_penjualan_kotor_nag = number_format(($penjualan_kotor_nag / $penjualan_bersih_nag * 100),2);
          $per_penjualan_kotor_nak = number_format(($penjualan_kotor_nak / $penjualan_bersih_nak * 100),2);
          $per_penjualan_kotor_all = number_format(($penjualan_kotor_all / $penjualan_bersih_all * 100),2);
          $pktr_nag = $penjualan_kotor_nag > 0 ? number_format($penjualan_kotor_nag,2) : '(' . number_format(abs($penjualan_kotor_nag),2) . ')';
          $pktr_nak = $penjualan_kotor_nak > 0 ? number_format($penjualan_kotor_nak,2) : '(' . number_format(abs($penjualan_kotor_nak),2) . ')';
          $pktr_all = $penjualan_kotor_all > 0 ? number_format($penjualan_kotor_all,2) : '(' . number_format(abs($penjualan_kotor_all),2) . ')';
          $total_penjualan_kotor_nag += $penjualan_kotor_nag;
          $total_penjualan_kotor_nak += $penjualan_kotor_nak;
          $total_penjualan_kotor_all += $penjualan_kotor_all;
          $per_total_penjualan_kotor_nag = number_format(($total_penjualan_kotor_nag / $penjualan_bersih_nag * 100),2);
          $per_total_penjualan_kotor_nak = number_format(($total_penjualan_kotor_nak / $penjualan_bersih_nak * 100),2);
          $per_total_penjualan_kotor_all = number_format(($total_penjualan_kotor_all / $penjualan_bersih_all * 100),2);
          $total_pktr_nag = $total_penjualan_kotor_nag > 0 ? number_format($total_penjualan_kotor_nag,2) : '(' . number_format(abs($total_penjualan_kotor_nag),2) . ')';
          $total_pktr_nak = $total_penjualan_kotor_nak > 0 ? number_format($total_penjualan_kotor_nak,2) : '(' . number_format(abs($total_penjualan_kotor_nak),2) . ')';
          $total_pktr_all = $total_penjualan_kotor_all > 0 ? number_format($total_penjualan_kotor_all,2) : '(' . number_format(abs($total_penjualan_kotor_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$pktr_nag}</td>";
            echo "<td class='item-right'>{$per_penjualan_kotor_nag}%</td>";
            echo "<td class='item-right'>{$pktr_nak}</td>";
            echo "<td class='item-right'>{$per_penjualan_kotor_nak}%</td>";
            echo "<td class='item-right'>{$pktr_all}</td>";
            echo "<td class='item-right'>{$per_penjualan_kotor_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$pktr_nag}</td>";
            echo "<td class='item-right'>{$per_penjualan_kotor_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$pktr_nak}</td>";
            echo "<td class='item-right'>{$per_penjualan_kotor_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left">TOTAL PENJUALAN KOTOR</th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_pktr_nag}</td>";
            echo "<td class='item-right'>{$per_total_penjualan_kotor_nag}%</td>";
            echo "<td class='total-right'>{$total_pktr_nak}</td>";
            echo "<td class='item-right'>{$per_total_penjualan_kotor_nak}%</td>";
            echo "<td class='total-right'>{$total_pktr_all}</td>";
            echo "<td class='item-right'>{$per_total_penjualan_kotor_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_pktr_nag}</td>";
            echo "<td class='item-right'>{$per_total_penjualan_kotor_nag}%</td>";
          }else{
            echo "<td class='total-right'>{$total_pktr_nak}</td>";
            echo "<td class='item-right'>{$per_total_penjualan_kotor_nak}%</td>";
          }
          ?>
          <th class="total-italic">GROSS SALES TOTAL</th>
        </tr>

        <!-- Section RETURN PENJUALAN -->
        <tr class="spacer-small"></tr>
        <tr>
          <th class="section-left">RETURN PENJUALAN</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
          }
          ?>
          <th class="section-right">SALES RETURN</th>
        </tr>
        <tr class="spacer-small"></tr>
        <?php

        $sql2 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('RETURN PENJUALAN')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_retur_penjualan_nag = 0;
        $total_retur_penjualan_nak = 0;
        $total_retur_penjualan_all = 0;
        while($row2 = mysqli_fetch_array($sql2)){
          $retur_penjualan_nag = $row2['total_nag'] ?? 0;
          $retur_penjualan_nak = $row2['total_nak'] ?? 0;
          $retur_penjualan_all = $row2['total_all'] ?? 0;
          $per_retur_penjualan_nag = number_format(($retur_penjualan_nag / $penjualan_bersih_nag * 100),2);
          $per_retur_penjualan_nak = number_format(($retur_penjualan_nak / $penjualan_bersih_nak * 100),2);
          $per_retur_penjualan_all = number_format(($retur_penjualan_all / $penjualan_bersih_all * 100),2);
          $rpj_nag = $retur_penjualan_nag > 0 ? number_format($retur_penjualan_nag,2) : '(' . number_format(abs($retur_penjualan_nag),2) . ')';
          $rpj_nak = $retur_penjualan_nak > 0 ? number_format($retur_penjualan_nak,2) : '(' . number_format(abs($retur_penjualan_nak),2) . ')';
          $rpj_all = $retur_penjualan_all > 0 ? number_format($retur_penjualan_all,2) : '(' . number_format(abs($retur_penjualan_all),2) . ')';
          $total_retur_penjualan_nag += $retur_penjualan_nag;
          $total_retur_penjualan_nak += $retur_penjualan_nak;
          $total_retur_penjualan_all += $retur_penjualan_all;
          $per_total_retur_penjualan_nag = number_format(($total_retur_penjualan_nag / $penjualan_bersih_nag * 100),2);
          $per_total_retur_penjualan_nak = number_format(($total_retur_penjualan_nak / $penjualan_bersih_nak * 100),2);
          $per_total_retur_penjualan_all = number_format(($total_retur_penjualan_all / $penjualan_bersih_all * 100),2);
          $total_rpj_nag = $total_retur_penjualan_nag > 0 ? number_format($total_retur_penjualan_nag,2) : '(' . number_format(abs($total_retur_penjualan_nag),2) . ')';
          $total_rpj_nak = $total_retur_penjualan_nak > 0 ? number_format($total_retur_penjualan_nak,2) : '(' . number_format(abs($total_retur_penjualan_nak),2) . ')';
          $total_rpj_all = $total_retur_penjualan_all > 0 ? number_format($total_retur_penjualan_all,2) : '(' . number_format(abs($total_retur_penjualan_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row2['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$rpj_nag}</td>";
            echo "<td class='item-right'>{$per_retur_penjualan_nag}%</td>";
            echo "<td class='item-right'>{$rpj_nak}</td>";
            echo "<td class='item-right'>{$per_retur_penjualan_nak}%</td>";
            echo "<td class='item-right'>{$rpj_all}</td>";
            echo "<td class='item-right'>{$per_retur_penjualan_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$rpj_nag}</td>";
            echo "<td class='item-right'>{$per_retur_penjualan_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$rpj_nak}</td>";
            echo "<td class='item-right'>{$per_retur_penjualan_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row2['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left">TOTAL RETUR PENJUALAN</th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_rpj_nag}</td>";
            echo "<td class='item-right'>{$per_total_retur_penjualan_nag}%</td>";
            echo "<td class='total-right'>{$total_rpj_nak}</td>";
            echo "<td class='item-right'>{$per_total_retur_penjualan_nak}%</td>";
            echo "<td class='total-right'>{$total_rpj_all}</td>";
            echo "<td class='item-right'>{$per_total_retur_penjualan_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_rpj_nag}</td>";
            echo "<td class='item-right'>{$per_total_retur_penjualan_nag}%</td>";
          }else{
            echo "<td class='total-right'>{$total_rpj_nak}</td>";
            echo "<td class='item-right'>{$per_total_retur_penjualan_nak}%</td>";
          }
          ?>
          <th class="total-italic">SALES RETURN TOTAL</th>
        </tr>

        <!-- Section POTONGAN PENJUALAN -->
        <tr class="spacer-small"></tr>
        <tr>
          <th class="section-left">POTONGAN PENJUALAN</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
          }
          ?>
          <th class="section-right">SALES DISCOUNT</th>
        </tr>
        <tr class="spacer-small"></tr>
        <?php

        $sql3 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('POTONGAN PENJUALAN')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_potongan_penjualan_nag = 0;
        $total_potongan_penjualan_nak = 0;
        $total_potongan_penjualan_all = 0;
        while($row3 = mysqli_fetch_array($sql3)){
          $potongan_penjualan_nag = $row3['total_nag'] ?? 0;
          $potongan_penjualan_nak = $row3['total_nak'] ?? 0;
          $potongan_penjualan_all = $row3['total_all'] ?? 0;
          $per_potongan_penjualan_nag = number_format(($potongan_penjualan_nag / $penjualan_bersih_nag * 100),2);
          $per_potongan_penjualan_nak = number_format(($potongan_penjualan_nak / $penjualan_bersih_nak * 100),2);
          $per_potongan_penjualan_all = number_format(($potongan_penjualan_all / $penjualan_bersih_all * 100),2);
          $ppj_nag = $potongan_penjualan_nag > 0 ? number_format($potongan_penjualan_nag,2) : '(' . number_format(abs($potongan_penjualan_nag),2) . ')';
          $ppj_nak = $potongan_penjualan_nak > 0 ? number_format($potongan_penjualan_nak,2) : '(' . number_format(abs($potongan_penjualan_nak),2) . ')';
          $ppj_all = $potongan_penjualan_all > 0 ? number_format($potongan_penjualan_all,2) : '(' . number_format(abs($potongan_penjualan_all),2) . ')';
          $total_potongan_penjualan_nag += $potongan_penjualan_nag;
          $total_potongan_penjualan_nak += $potongan_penjualan_nak;
          $total_potongan_penjualan_all += $potongan_penjualan_all;
          $per_total_potongan_penjualan_nag = number_format(($total_potongan_penjualan_nag / $penjualan_bersih_nag * 100),2);
          $per_total_potongan_penjualan_nak = number_format(($total_potongan_penjualan_nak / $penjualan_bersih_nak * 100),2);
          $per_total_potongan_penjualan_all = number_format(($total_potongan_penjualan_all / $penjualan_bersih_all * 100),2);
          $total_ppj_nag = $total_potongan_penjualan_nag > 0 ? number_format($total_potongan_penjualan_nag,2) : '(' . number_format(abs($total_potongan_penjualan_nag),2) . ')';
          $total_ppj_nak = $total_potongan_penjualan_nak > 0 ? number_format($total_potongan_penjualan_nak,2) : '(' . number_format(abs($total_potongan_penjualan_nak),2) . ')';
          $total_ppj_all = $total_potongan_penjualan_all > 0 ? number_format($total_potongan_penjualan_all,2) : '(' . number_format(abs($total_potongan_penjualan_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row3['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$ppj_nag}</td>";
            echo "<td class='item-right'>{$per_potongan_penjualan_nag}%</td>";
            echo "<td class='item-right'>{$ppj_nak}</td>";
            echo "<td class='item-right'>{$per_potongan_penjualan_nak}%</td>";
            echo "<td class='item-right'>{$ppj_all}</td>";
            echo "<td class='item-right'>{$per_potongan_penjualan_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$ppj_nag}</td>";
            echo "<td class='item-right'>{$per_potongan_penjualan_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$ppj_nak}</td>";
            echo "<td class='item-right'>{$per_potongan_penjualan_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row3['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left">TOTAL POTONGAN PENJUALAN</th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_ppj_nag}</td>";
            echo "<td class='item-right'>{$per_total_potongan_penjualan_nag}%</td>";
            echo "<td class='total-right'>{$total_ppj_nak}</td>";
            echo "<td class='item-right'>{$per_total_potongan_penjualan_nak}%</td>";
            echo "<td class='total-right'>{$total_ppj_all}</td>";
            echo "<td class='item-right'>{$per_total_potongan_penjualan_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_ppj_nag}</td>";
            echo "<td class='item-right'>{$per_total_potongan_penjualan_nag}%</td>";
          }else{
            echo "<td class='total-right'>{$total_ppj_nak}</td>";
            echo "<td class='item-right'>{$per_total_potongan_penjualan_nak}%</td>";
          }
          ?>
          <th class="total-italic">SALES DISCOUNT TOTAL</th>
        </tr>

        <tr class="spacer-small"></tr>

        <tr class="grand-total">
          <th class="grand-left">PENJUALAN BERSIH</th>
          <?php
          $total_penjualan_bersih_nag = $total_penjualan_kotor_nag + $total_retur_penjualan_nag + $total_potongan_penjualan_nag;
          $total_penjualan_bersih_nak = $total_penjualan_kotor_nak + $total_retur_penjualan_nak + $total_potongan_penjualan_nak;
          $total_penjualan_bersih_all = $total_penjualan_kotor_all + $total_retur_penjualan_all + $total_potongan_penjualan_all;

          $total_pbsh_nag = $total_penjualan_bersih_nag > 0 ? number_format($total_penjualan_bersih_nag,2) : '(' . number_format(abs($total_penjualan_bersih_nag),2) . ')';
          $total_pbsh_nak = $total_penjualan_bersih_nak > 0 ? number_format($total_penjualan_bersih_nak,2) : '(' . number_format(abs($total_penjualan_bersih_nak),2) . ')';
          $total_pbsh_all = $total_penjualan_bersih_all > 0 ? number_format($total_penjualan_bersih_all,2) : '(' . number_format(abs($total_penjualan_bersih_all),2) . ')';

          if ($profit_center == 'ALL') {
            echo "<th class='grand-right'>{$total_pbsh_nag}</th>";
            echo "<th class='grand-right'>100%</th>";
            echo "<th class='grand-right'>{$total_pbsh_nak}</th>";
            echo "<th class='grand-right'>100%</th>";
            echo "<th class='grand-right'>{$total_pbsh_all}</th>";
            echo "<th class='grand-right'>100%</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='grand-right'>{$total_pbsh_nag}</th>";
            echo "<th class='grand-right'>100%</th>";
          }else{
            echo "<th class='grand-right'>{$total_pbsh_nak}</th>";
            echo "<th class='grand-right'>100%</th>";
          }
          ?>
          <th class="grand-italic">NET SALES</th>
        </tr>
        <tr class="spacer-small"></tr>
        <tr>
          <th class="section-left">BEBAN POKOK PENJUALAN</th>
          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
          }

          if ($profit_center == 'ALL') {
            echo '<th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>';
          }elseif ($profit_center == 'NAG') {
            echo '<th></th>
            <th></th>';
          }else{
            echo '<th></th>
            <th></th>';
          }
          ?>
          <th class="section-right">COST OF GOODS SOLD</th>
        </tr>
        <tr class="spacer-small"></tr>
        <?php

        $sql4 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN POKOK PENJUALAN')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_beban_pokok_nag = 0;
        $total_beban_pokok_nak = 0;
        $total_beban_pokok_all = 0;
        while($row4 = mysqli_fetch_array($sql4)){
          $beban_pokok_nag = $row4['total_nag'] ?? 0;
          $beban_pokok_nak = $row4['total_nak'] ?? 0;
          $beban_pokok_all = $row4['total_all'] ?? 0;
          $per_beban_pokok_nag = number_format(($beban_pokok_nag / $penjualan_bersih_nag * 100),2);
          $per_beban_pokok_nak = number_format(($beban_pokok_nak / $penjualan_bersih_nak * 100),2);
          $per_beban_pokok_all = number_format(($beban_pokok_all / $penjualan_bersih_all * 100),2);
          $bpp_nag = $beban_pokok_nag > 0 ? number_format($beban_pokok_nag,2) : '(' . number_format(abs($beban_pokok_nag),2) . ')';
          $bpp_nak = $beban_pokok_nak > 0 ? number_format($beban_pokok_nak,2) : '(' . number_format(abs($beban_pokok_nak),2) . ')';
          $bpp_all = $beban_pokok_all > 0 ? number_format($beban_pokok_all,2) : '(' . number_format(abs($beban_pokok_all),2) . ')';
          $total_beban_pokok_nag += $beban_pokok_nag;
          $total_beban_pokok_nak += $beban_pokok_nak;
          $total_beban_pokok_all += $beban_pokok_all;
          $per_total_beban_pokok_nag = number_format(($total_beban_pokok_nag / $penjualan_bersih_nag * 100),2);
          $per_total_beban_pokok_nak = number_format(($total_beban_pokok_nak / $penjualan_bersih_nak * 100),2);
          $per_total_beban_pokok_all = number_format(($total_beban_pokok_all / $penjualan_bersih_all * 100),2);
          $total_bpp_nag = $total_beban_pokok_nag > 0 ? number_format($total_beban_pokok_nag,2) : '(' . number_format(abs($total_beban_pokok_nag),2) . ')';
          $total_bpp_nak = $total_beban_pokok_nak > 0 ? number_format($total_beban_pokok_nak,2) : '(' . number_format(abs($total_beban_pokok_nak),2) . ')';
          $total_bpp_all = $total_beban_pokok_all > 0 ? number_format($total_beban_pokok_all,2) : '(' . number_format(abs($total_beban_pokok_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row4['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$bpp_nag}</td>";
            echo "<td class='item-right'>{$per_beban_pokok_nag}%</td>";
            echo "<td class='item-right'>{$bpp_nak}</td>";
            echo "<td class='item-right'>{$per_beban_pokok_nak}%</td>";
            echo "<td class='item-right'>{$bpp_all}</td>";
            echo "<td class='item-right'>{$per_beban_pokok_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$bpp_nag}</td>";
            echo "<td class='item-right'>{$per_beban_pokok_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$bpp_nak}</td>";
            echo "<td class='item-right'>{$per_beban_pokok_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row4['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="total-line">
          <th class="total-left">HARGA POKOK PENJUALAN</th>
          <?php 
          if ($profit_center == 'ALL') {
            echo "<td class='total-right'>{$total_bpp_nag}</td>";
            echo "<td class='item-right'>{$per_total_beban_pokok_nag}%</td>";
            echo "<td class='total-right'>{$total_bpp_nak}</td>";
            echo "<td class='item-right'>{$per_total_beban_pokok_nak}%</td>";
            echo "<td class='total-right'>{$total_bpp_all}</td>";
            echo "<td class='item-right'>{$per_total_beban_pokok_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='total-right'>{$total_bpp_nag}</td>";
            echo "<td class='item-right'>{$per_total_beban_pokok_nag}%</td>";
          }else{
            echo "<td class='total-right'>{$total_bpp_nak}</td>";
            echo "<td class='item-right'>{$per_total_beban_pokok_nak}%</td>";
          }
          ?>
          <th class="total-italic">COST OF GOODS SOLD</th>
        </tr>
        <tr class="spacer-small"></tr>

        <tr class="grand-total">
          <th class="grand-left">LABA RUGI KOTOR</th>
          <?php
          $total_laba_rugi_kotor_nag = $total_penjualan_bersih_nag + $total_beban_pokok_nag;
          $total_laba_rugi_kotor_nak = $total_penjualan_bersih_nak + $total_beban_pokok_nak;
          $total_laba_rugi_kotor_all = $total_penjualan_bersih_all + $total_beban_pokok_all;

          $total_lrk_nag = $total_laba_rugi_kotor_nag > 0 ? number_format($total_laba_rugi_kotor_nag,2) : '(' . number_format(abs($total_laba_rugi_kotor_nag),2) . ')';
          $total_lrk_nak = $total_laba_rugi_kotor_nak > 0 ? number_format($total_laba_rugi_kotor_nak,2) : '(' . number_format(abs($total_laba_rugi_kotor_nak),2) . ')';
          $total_lrk_all = $total_laba_rugi_kotor_all > 0 ? number_format($total_laba_rugi_kotor_all,2) : '(' . number_format(abs($total_laba_rugi_kotor_all),2) . ')';

          $per_total_laba_rugi_kotor_nag = number_format(($total_laba_rugi_kotor_nag / $penjualan_bersih_nag * 100),2);
          $per_total_laba_rugi_kotor_nak = number_format(($total_laba_rugi_kotor_nak / $penjualan_bersih_nak * 100),2);
          $per_total_laba_rugi_kotor_all = number_format(($total_laba_rugi_kotor_all / $penjualan_bersih_all * 100),2);

          if ($profit_center == 'ALL') {
            echo "<th class='grand-right'>{$total_lrk_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_kotor_nag}%</th>";
            echo "<th class='grand-right'>{$total_lrk_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_kotor_nak}%</th>";
            echo "<th class='grand-right'>{$total_lrk_all}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_kotor_all}%</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='grand-right'>{$total_lrk_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_kotor_nag}%</th>";
          }else{
            echo "<th class='grand-right'>{$total_lrk_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_kotor_nak}%</th>";
          }
          ?>
          <th class="grand-italic">GROSS PROFIT / (LOSS)</th>
        </tr>

        <tr class="spacer-small"></tr>
        <?php

        $sql5 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN LAINNYA')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_beban_lainnya_nag = 0;
        $total_beban_lainnya_nak = 0;
        $total_beban_lainnya_all = 0;
        while($row5 = mysqli_fetch_array($sql5)){
          $beban_lainnya_nag = $row5['total_nag'] ?? 0;
          $beban_lainnya_nak = $row5['total_nak'] ?? 0;
          $beban_lainnya_all = $row5['total_all'] ?? 0;
          $per_beban_lainnya_nag = number_format(($beban_lainnya_nag / $penjualan_bersih_nag * 100),2);
          $per_beban_lainnya_nak = number_format(($beban_lainnya_nak / $penjualan_bersih_nak * 100),2);
          $per_beban_lainnya_all = number_format(($beban_lainnya_all / $penjualan_bersih_all * 100),2);
          $bln_nag = $beban_lainnya_nag > 0 ? number_format($beban_lainnya_nag,2) : '(' . number_format(abs($beban_lainnya_nag),2) . ')';
          $bln_nak = $beban_lainnya_nak > 0 ? number_format($beban_lainnya_nak,2) : '(' . number_format(abs($beban_lainnya_nak),2) . ')';
          $bln_all = $beban_lainnya_all > 0 ? number_format($beban_lainnya_all,2) : '(' . number_format(abs($beban_lainnya_all),2) . ')';
          $total_beban_lainnya_nag += $beban_lainnya_nag;
          $total_beban_lainnya_nak += $beban_lainnya_nak;
          $total_beban_lainnya_all += $beban_lainnya_all;
          $per_total_beban_lainnya_nag = number_format(($total_beban_lainnya_nag / $penjualan_bersih_nag * 100),2);
          $per_total_beban_lainnya_nak = number_format(($total_beban_lainnya_nak / $penjualan_bersih_nak * 100),2);
          $per_total_beban_lainnya_all = number_format(($total_beban_lainnya_all / $penjualan_bersih_all * 100),2);
          $total_bln_nag = $total_beban_lainnya_nag > 0 ? number_format($total_beban_lainnya_nag,2) : '(' . number_format(abs($total_beban_lainnya_nag),2) . ')';
          $total_bln_nak = $total_beban_lainnya_nak > 0 ? number_format($total_beban_lainnya_nak,2) : '(' . number_format(abs($total_beban_lainnya_nak),2) . ')';
          $total_bln_all = $total_beban_lainnya_all > 0 ? number_format($total_beban_lainnya_all,2) : '(' . number_format(abs($total_beban_lainnya_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row5['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$bln_nag}</td>";
            echo "<td class='item-right'>{$per_beban_lainnya_nag}%</td>";
            echo "<td class='item-right'>{$bln_nak}</td>";
            echo "<td class='item-right'>{$per_beban_lainnya_nak}%</td>";
            echo "<td class='item-right'>{$bln_all}</td>";
            echo "<td class='item-right'>{$per_beban_lainnya_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$bln_nag}</td>";
            echo "<td class='item-right'>{$per_beban_lainnya_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$bln_nak}</td>";
            echo "<td class='item-right'>{$per_beban_lainnya_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row5['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="spacer-small"></tr>

        <tr class="grand-total">
          <th class="grand-left">LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</th>
          <?php
          $total_laba_rugi_sebelum_bunga_nag = $total_laba_rugi_kotor_nag + $total_beban_lainnya_nag;
          $total_laba_rugi_sebelum_bunga_nak = $total_laba_rugi_kotor_nak + $total_beban_lainnya_nak;
          $total_laba_rugi_sebelum_bunga_all = $total_laba_rugi_kotor_all + $total_beban_lainnya_all;

          $total_lrsb_nag = $total_laba_rugi_sebelum_bunga_nag > 0 ? number_format($total_laba_rugi_sebelum_bunga_nag,2) : '(' . number_format(abs($total_laba_rugi_sebelum_bunga_nag),2) . ')';
          $total_lrsb_nak = $total_laba_rugi_sebelum_bunga_nak > 0 ? number_format($total_laba_rugi_sebelum_bunga_nak,2) : '(' . number_format(abs($total_laba_rugi_sebelum_bunga_nak),2) . ')';
          $total_lrsb_all = $total_laba_rugi_sebelum_bunga_all > 0 ? number_format($total_laba_rugi_sebelum_bunga_all,2) : '(' . number_format(abs($total_laba_rugi_sebelum_bunga_all),2) . ')';

          $per_total_laba_rugi_sebelum_bunga_nag = number_format(($total_laba_rugi_sebelum_bunga_nag / $penjualan_bersih_nag * 100),2);
          $per_total_laba_rugi_sebelum_bunga_nak = number_format(($total_laba_rugi_sebelum_bunga_nak / $penjualan_bersih_nak * 100),2);
          $per_total_laba_rugi_sebelum_bunga_all = number_format(($total_laba_rugi_sebelum_bunga_all / $penjualan_bersih_all * 100),2);

          if ($profit_center == 'ALL') {
            echo "<th class='grand-right'>{$total_lrsb_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_bunga_nag}%</th>";
            echo "<th class='grand-right'>{$total_lrsb_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_bunga_nak}%</th>";
            echo "<th class='grand-right'>{$total_lrsb_all}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_bunga_all}%</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='grand-right'>{$total_lrsb_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_bunga_nag}%</th>";
          }else{
            echo "<th class='grand-right'>{$total_lrsb_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_bunga_nak}%</th>";
          }
          ?>
          <th class="grand-italic">PROFIT / (LOSS) BEFORE INTEREST AND TAX</th>
        </tr>

        <tr class="spacer-small"></tr>
        <?php

        $sql6 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN BUNGA')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_beban_bunga_nag = 0;
        $total_beban_bunga_nak = 0;
        $total_beban_bunga_all = 0;
        while($row6 = mysqli_fetch_array($sql6)){
          $beban_bunga_nag = $row6['total_nag'] ?? 0;
          $beban_bunga_nak = $row6['total_nak'] ?? 0;
          $beban_bunga_all = $row6['total_all'] ?? 0;
          $per_beban_bunga_nag = number_format(($beban_bunga_nag / $penjualan_bersih_nag * 100),2);
          $per_beban_bunga_nak = number_format(($beban_bunga_nak / $penjualan_bersih_nak * 100),2);
          $per_beban_bunga_all = number_format(($beban_bunga_all / $penjualan_bersih_all * 100),2);
          $bbng_nag = $beban_bunga_nag > 0 ? number_format($beban_bunga_nag,2) : '(' . number_format(abs($beban_bunga_nag),2) . ')';
          $bbng_nak = $beban_bunga_nak > 0 ? number_format($beban_bunga_nak,2) : '(' . number_format(abs($beban_bunga_nak),2) . ')';
          $bbng_all = $beban_bunga_all > 0 ? number_format($beban_bunga_all,2) : '(' . number_format(abs($beban_bunga_all),2) . ')';
          $total_beban_bunga_nag += $beban_bunga_nag;
          $total_beban_bunga_nak += $beban_bunga_nak;
          $total_beban_bunga_all += $beban_bunga_all;
          $per_total_beban_bunga_nag = number_format(($total_beban_bunga_nag / $penjualan_bersih_nag * 100),2);
          $per_total_beban_bunga_nak = number_format(($total_beban_bunga_nak / $penjualan_bersih_nak * 100),2);
          $per_total_beban_bunga_all = number_format(($total_beban_bunga_all / $penjualan_bersih_all * 100),2);
          $total_bbng_nag = $total_beban_bunga_nag > 0 ? number_format($total_beban_bunga_nag,2) : '(' . number_format(abs($total_beban_bunga_nag),2) . ')';
          $total_bbng_nak = $total_beban_bunga_nak > 0 ? number_format($total_beban_bunga_nak,2) : '(' . number_format(abs($total_beban_bunga_nak),2) . ')';
          $total_bbng_all = $total_beban_bunga_all > 0 ? number_format($total_beban_bunga_all,2) : '(' . number_format(abs($total_beban_bunga_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row6['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$bbng_nag}</td>";
            echo "<td class='item-right'>{$per_beban_bunga_nag}%</td>";
            echo "<td class='item-right'>{$bbng_nak}</td>";
            echo "<td class='item-right'>{$per_beban_bunga_nak}%</td>";
            echo "<td class='item-right'>{$bbng_all}</td>";
            echo "<td class='item-right'>{$per_beban_bunga_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$bbng_nag}</td>";
            echo "<td class='item-right'>{$per_beban_bunga_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$bbng_nak}</td>";
            echo "<td class='item-right'>{$per_beban_bunga_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row6['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="spacer-small"></tr>

        <tr class="grand-total">
          <th class="grand-left">LABA / (RUGI) SEBELUM PAJAK</th>
          <?php
          $total_laba_rugi_sebelum_pajak_nag = $total_laba_rugi_sebelum_bunga_nag + $total_beban_bunga_nag;
          $total_laba_rugi_sebelum_pajak_nak = $total_laba_rugi_sebelum_bunga_nak + $total_beban_bunga_nak;
          $total_laba_rugi_sebelum_pajak_all = $total_laba_rugi_sebelum_bunga_all + $total_beban_bunga_all;

          $total_lrsp_nag = $total_laba_rugi_sebelum_pajak_nag > 0 ? number_format($total_laba_rugi_sebelum_pajak_nag,2) : '(' . number_format(abs($total_laba_rugi_sebelum_pajak_nag),2) . ')';
          $total_lrsp_nak = $total_laba_rugi_sebelum_pajak_nak > 0 ? number_format($total_laba_rugi_sebelum_pajak_nak,2) : '(' . number_format(abs($total_laba_rugi_sebelum_pajak_nak),2) . ')';
          $total_lrsp_all = $total_laba_rugi_sebelum_pajak_all > 0 ? number_format($total_laba_rugi_sebelum_pajak_all,2) : '(' . number_format(abs($total_laba_rugi_sebelum_pajak_all),2) . ')';

          $per_total_laba_rugi_sebelum_pajak_nag = number_format(($total_laba_rugi_sebelum_pajak_nag / $penjualan_bersih_nag * 100),2);
          $per_total_laba_rugi_sebelum_pajak_nak = number_format(($total_laba_rugi_sebelum_pajak_nak / $penjualan_bersih_nak * 100),2);
          $per_total_laba_rugi_sebelum_pajak_all = number_format(($total_laba_rugi_sebelum_pajak_all / $penjualan_bersih_all * 100),2);

          if ($profit_center == 'ALL') {
            echo "<th class='grand-right'>{$total_lrsp_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_pajak_nag}%</th>";
            echo "<th class='grand-right'>{$total_lrsp_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_pajak_nak}%</th>";
            echo "<th class='grand-right'>{$total_lrsp_all}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_pajak_all}%</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='grand-right'>{$total_lrsp_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_pajak_nag}%</th>";
          }else{
            echo "<th class='grand-right'>{$total_lrsp_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_sebelum_pajak_nak}%</th>";
          }
          ?>
          <th class="grand-italic">PROFIT / (LOSS) BEFORE TAX</th>
        </tr>

        <tr class="spacer-small"></tr>
        <?php

        $sql7 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN PAJAK')) a left JOIN
          (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
          (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
          left join 
          (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + credit_idr - debit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
        $total_beban_pajak_nag = 0;
        $total_beban_pajak_nak = 0;
        $total_beban_pajak_all = 0;
        while($row7 = mysqli_fetch_array($sql7)){
          $beban_pajak_nag = $row7['total_nag'] ?? 0;
          $beban_pajak_nak = $row7['total_nak'] ?? 0;
          $beban_pajak_all = $row7['total_all'] ?? 0;
          $per_beban_pajak_nag = number_format(($beban_pajak_nag / $penjualan_bersih_nag * 100),2);
          $per_beban_pajak_nak = number_format(($beban_pajak_nak / $penjualan_bersih_nak * 100),2);
          $per_beban_pajak_all = number_format(($beban_pajak_all / $penjualan_bersih_all * 100),2);
          $bpjk_nag = $beban_pajak_nag > 0 ? number_format($beban_pajak_nag,2) : '(' . number_format(abs($beban_pajak_nag),2) . ')';
          $bpjk_nak = $beban_pajak_nak > 0 ? number_format($beban_pajak_nak,2) : '(' . number_format(abs($beban_pajak_nak),2) . ')';
          $bpjk_all = $beban_pajak_all > 0 ? number_format($beban_pajak_all,2) : '(' . number_format(abs($beban_pajak_all),2) . ')';
          $total_beban_pajak_nag += $beban_pajak_nag;
          $total_beban_pajak_nak += $beban_pajak_nak;
          $total_beban_pajak_all += $beban_pajak_all;
          $per_total_beban_pajak_nag = number_format(($total_beban_pajak_nag / $penjualan_bersih_nag * 100),2);
          $per_total_beban_pajak_nak = number_format(($total_beban_pajak_nak / $penjualan_bersih_nak * 100),2);
          $per_total_beban_pajak_all = number_format(($total_beban_pajak_all / $penjualan_bersih_all * 100),2);
          $total_bpjk_nag = $total_beban_pajak_nag > 0 ? number_format($total_beban_pajak_nag,2) : '(' . number_format(abs($total_beban_pajak_nag),2) . ')';
          $total_bpjk_nak = $total_beban_pajak_nak > 0 ? number_format($total_beban_pajak_nak,2) : '(' . number_format(abs($total_beban_pajak_nak),2) . ')';
          $total_bpjk_all = $total_beban_pajak_all > 0 ? number_format($total_beban_pajak_all,2) : '(' . number_format(abs($total_beban_pajak_all),2) . ')';
          echo "
          <tr>
          <td class='item-left'>{$row7['sub_kategori']}</td>";
          if ($profit_center == 'ALL') {
            echo "<td class='item-right'>{$bpjk_nag}</td>";
            echo "<td class='item-right'>{$per_beban_pajak_nag}%</td>";
            echo "<td class='item-right'>{$bpjk_nak}</td>";
            echo "<td class='item-right'>{$per_beban_pajak_nak}%</td>";
            echo "<td class='item-right'>{$bpjk_all}</td>";
            echo "<td class='item-right'>{$per_beban_pajak_all}%</td>";
          }elseif ($profit_center == 'NAG') {
            echo "<td class='item-right'>{$bpjk_nag}</td>";
            echo "<td class='item-right'>{$per_beban_pajak_nag}%</td>";
          }else{
            echo "<td class='item-right'>{$bpjk_nak}</td>";
            echo "<td class='item-right'>{$per_beban_pajak_nak}%</td>";
          }
          echo "<td class='item-italic'>{$row7['sub_kategori_eng']}</td>
          </tr>
          ";
        }
        ?>

        <tr class="spacer-small"></tr>

        <tr class="grand-total">
          <th class="grand-left">LABA / (RUGI) BERSIH</th>
          <?php
          $total_laba_rugi_bersih_nag = $total_laba_rugi_sebelum_pajak_nag + $total_beban_pajak_nag;
          $total_laba_rugi_bersih_nak = $total_laba_rugi_sebelum_pajak_nak + $total_beban_pajak_nak;
          $total_laba_rugi_bersih_all = $total_laba_rugi_sebelum_pajak_all + $total_beban_pajak_all;

          $total_lrsp_nag = $total_laba_rugi_bersih_nag > 0 ? number_format($total_laba_rugi_bersih_nag,2) : '(' . number_format(abs($total_laba_rugi_bersih_nag),2) . ')';
          $total_lrsp_nak = $total_laba_rugi_bersih_nak > 0 ? number_format($total_laba_rugi_bersih_nak,2) : '(' . number_format(abs($total_laba_rugi_bersih_nak),2) . ')';
          $total_lrsp_all = $total_laba_rugi_bersih_all > 0 ? number_format($total_laba_rugi_bersih_all,2) : '(' . number_format(abs($total_laba_rugi_bersih_all),2) . ')';

          $per_total_laba_rugi_bersih_nag = number_format(($total_laba_rugi_bersih_nag / $penjualan_bersih_nag * 100),2);
          $per_total_laba_rugi_bersih_nak = number_format(($total_laba_rugi_bersih_nak / $penjualan_bersih_nak * 100),2);
          $per_total_laba_rugi_bersih_all = number_format(($total_laba_rugi_bersih_all / $penjualan_bersih_all * 100),2);

          if ($profit_center == 'ALL') {
            echo "<th class='grand-right'>{$total_lrsp_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_bersih_nag}%</th>";
            echo "<th class='grand-right'>{$total_lrsp_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_bersih_nak}%</th>";
            echo "<th class='grand-right'>{$total_lrsp_all}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_bersih_all}%</th>";
          }elseif ($profit_center == 'NAG') {
            echo "<th class='grand-right'>{$total_lrsp_nag}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_bersih_nag}%</th>";
          }else{
            echo "<th class='grand-right'>{$total_lrsp_nak}</th>";
            echo "<th class='grand-right'>{$per_total_laba_rugi_bersih_nak}%</th>";
          }
          ?>
          <th class="grand-italic">NET INCOME / (LOSS)</th>
        </tr>


      </table>
    </div>
  </div>
</div>
</div>



