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


.laporan-container {
  border: 2px solid #2c3e50;
  border-radius: 10px;
  padding: 15px 25px;
  background: #fafafa;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.laporan-table {
  font-size: 14px;
  margin: auto;
  width: 95%;
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
  width: 190px !important;
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
  text-transform: lowercase;
}

.subsection-left::first-letter,
.subsection-right::first-letter {
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
  display: flex;
  justify-content: left;
  gap: 12px;
  margin-top: 10px;
}

.btn-export {
  font-family: Calibri, Arial, sans-serif;
  font-size: 12px;              /* 🔹 lebih kecil */
  font-weight: 600;
  padding: 6px 14px;            /* 🔹 lebih ramping */
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #fff;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn-export.excel {
  background: #1d6f42; /* hijau Excel */
}

.btn-export.excel:hover {
  background: #218c53;
  transform: translateY(-1px);
}

.btn-export.pdf {
  background: #b22222; /* merah PDF */
}

.btn-export.pdf:hover {
  background: #d12b2b;
  transform: translateY(-1px);
}

.btn-export:active {
  transform: translateY(0);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
}



</style>

<!-- <div>

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

      </div> -->


    <!-- HEADER -->
<div class="export-buttons mt-2 ml-4">
  <button id="btnExcel" class=" btn-export excel">
    📊 Export Excel
  </button>
  <button id="btnPDF" class=" btn-export pdf">
    🖨️ Print PDF
  </button>
</div>

<!-- Kontainer Laporan -->
<div class="table-responsive mt-1">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <div class="laporan-container">
        <table class="laporan-table" border="0" role="grid" cellspacing="0">
          <!-- Header Judul -->
          <tr>
            <th class="judul-left">PT NIRWANA ALABARE GARMENT</th>
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
            <th class="judul-right">PT NIRWANA ALABARE GARMENT</th>
          </tr>

          <th class="judul-left">LAPORAN POSISI KEUANGAN</th>
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
            <th class="judul-right">STATEMENTS OF FINANCIAL POSITION</th>
          </tr>

          <tr>
            <th class="judul-left">
              <?php
                $sqlakhir = mysqli_query($conn2,"SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan = '$bulan_akhir' AND tahun = '$tahun_akhir'");
                $rowakhir = mysqli_fetch_array($sqlakhir);
                $tgl_akhir = $rowakhir['tgl_akhir'] ?? null;
                echo strtoupper(date("d F Y", strtotime($tgl_akhir)));
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

          <!-- Section ASET -->
          <tr class="spacer">
            <th></th>
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
            <th></th>
          </tr>
          <tr>
            <th class="section-left">ASET</th>
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
            <th class="section-right">ASSETS</th>
          </tr>
          <tr class="spacer-small">
            <th></th>
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
            <th></th>
          </tr>

          <!-- ASET LANCAR -->
          <tr>
            <th class="subsection-left">ASET LANCAR</th>
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
            <th class="subsection-right">CURRENT ASSETS</th>

          </tr>
          <?php
          $sql = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('ASET LANCAR')) a left JOIN
            (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
          $total_aset_lancar_nag = 0;
          $total_aset_lancar_nak = 0;
          $total_aset_lancar_all = 0;
          while($row = mysqli_fetch_array($sql)){
              $aset_lancar_nag = $row['total_nag'] ?? 0;
              $aset_lancar_nak = $row['total_nak'] ?? 0;
              $aset_lancar_all = $row['total_all'] ?? 0;
              $al_nag = $aset_lancar_nag > 0 ? number_format($aset_lancar_nag,2) : '(' . number_format(abs($aset_lancar_nag),2) . ')';
              $al_nak = $aset_lancar_nak > 0 ? number_format($aset_lancar_nak,2) : '(' . number_format(abs($aset_lancar_nak),2) . ')';
              $al_all = $aset_lancar_all > 0 ? number_format($aset_lancar_all,2) : '(' . number_format(abs($aset_lancar_all),2) . ')';
              $total_aset_lancar_nag += $aset_lancar_nag;
              $total_aset_lancar_nak += $aset_lancar_nak;
              $total_aset_lancar_all += $aset_lancar_all;
              $total_al_nag = $total_aset_lancar_nag > 0 ? number_format($total_aset_lancar_nag,2) : '(' . number_format(abs($total_aset_lancar_nag),2) . ')';
              $total_al_nak = $total_aset_lancar_nak > 0 ? number_format($total_aset_lancar_nak,2) : '(' . number_format(abs($total_aset_lancar_nak),2) . ')';
              $total_al_all = $total_aset_lancar_all > 0 ? number_format($total_aset_lancar_all,2) : '(' . number_format(abs($total_aset_lancar_all),2) . ')';
              echo "
                <tr>
                  <td class='item-left'>{$row['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$al_nag}</td>";
                    echo "<td class='item-right'>{$al_nak}</td>";
                    echo "<td class='item-right'>{$al_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$al_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$al_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <tr class="total-line">
            <th class="total-left">Jumlah Aset Lancar</th>
            <?php 
                  if ($profit_center == 'ALL') {
                    echo "<td class='total-right'>{$total_al_nag}</td>";
                    echo "<td class='total-right'>{$total_al_nak}</td>";
                    echo "<td class='total-right'>{$total_al_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='total-right'>{$total_al_nag}</td>";
                  }else{
                    echo "<td class='total-right'>{$total_al_nak}</td>";
                  }
             ?>
            <th class="total-italic">Total Current Assets</th>
          </tr>

          <tr class="spacer-small">
            <th></th>
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
            <th></th>
          </tr>

          <!-- ASET TIDAK LANCAR -->
          <tr>
            <th class="subsection-left">ASET TIDAK LANCAR</th>
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
            <th class="subsection-right">NON-CURRENT ASSETS</th>

          </tr>
          <?php
          $sql2 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('ASET TIDAK LANCAR')) a left JOIN
            (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
          $total_aset_tidak_lancar_nag = 0;
          $total_aset_tidak_lancar_nak = 0;
          $total_aset_tidak_lancar_all = 0;
          while($row2 = mysqli_fetch_array($sql2)){
              $aset_tidak_lancar_nag = $row2['total_nag'] ?? 0;
              $aset_tidak_lancar_nak = $row2['total_nak'] ?? 0;
              $aset_tidak_lancar_all = $row2['total_all'] ?? 0;
              $atl_nag = $aset_tidak_lancar_nag > 0 ? number_format($aset_tidak_lancar_nag,2) : '(' . number_format(abs($aset_tidak_lancar_nag),2) . ')';
              $atl_nak = $aset_tidak_lancar_nak > 0 ? number_format($aset_tidak_lancar_nak,2) : '(' . number_format(abs($aset_tidak_lancar_nak),2) . ')';
              $atl_all = $aset_tidak_lancar_all > 0 ? number_format($aset_tidak_lancar_all,2) : '(' . number_format(abs($aset_tidak_lancar_all),2) . ')';
              $total_aset_tidak_lancar_nag += $aset_tidak_lancar_nag;
              $total_aset_tidak_lancar_nak += $aset_tidak_lancar_nak;
              $total_aset_tidak_lancar_all += $aset_tidak_lancar_all;
              $total_atl_nag = $total_aset_tidak_lancar_nag > 0 ? number_format($total_aset_tidak_lancar_nag,2) : '(' . number_format(abs($total_aset_tidak_lancar_nag),2) . ')';
              $total_atl_nak = $total_aset_tidak_lancar_nak > 0 ? number_format($total_aset_tidak_lancar_nak,2) : '(' . number_format(abs($total_aset_tidak_lancar_nak),2) . ')';
              $total_atl_all = $total_aset_tidak_lancar_all > 0 ? number_format($total_aset_tidak_lancar_all,2) : '(' . number_format(abs($total_aset_tidak_lancar_all),2) . ')';
              echo "
                <tr>
                  <td class='item-left'>{$row2['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$atl_nag}</td>";
                    echo "<td class='item-right'>{$atl_nak}</td>";
                    echo "<td class='item-right'>{$atl_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$atl_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$atl_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row2['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <tr class="total-line">
            <th class="total-left">Jumlah Aset Tidak Lancar</th>
            <?php 
                  if ($profit_center == 'ALL') {
                    echo "<td class='total-right'>{$total_atl_nag}</td>";
                    echo "<td class='total-right'>{$total_atl_nak}</td>";
                    echo "<td class='total-right'>{$total_atl_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='total-right'>{$total_atl_nag}</td>";
                  }else{
                    echo "<td class='total-right'>{$total_atl_nak}</td>";
                  }
             ?>
            <th class="total-italic">Total Non Current Asset</th>
          </tr>
          <tr class="spacer-mid">
            <th></th>
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
            <th></th>
          </tr>

          <tr class="grand-total">
            <th class="grand-left">JUMLAH ASET</th>
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

          <tr class="spacer">
            <th></th>
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
            <th></th>
          </tr>
          <tr>
            <th class="section-left">LIABILITAS DAN EKUITAS </th>
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
            <th class="section-right">LIABILITIES AND EQUITY</th>
          </tr>
          <tr class="spacer-small">
            <th></th>
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
            <th></th>
          </tr>

          <!-- LIABILITAS JANGKA PENDEK -->
          <tr>
            <th class="subsection-left">LIABILITAS JANGKA PENDEK</th>
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
            <th class="subsection-right">CURRENT LIABILITIES</th>

          </tr>
          <?php
          $sql3 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('LIABILITAS JANGKA PENDEK')) a left JOIN
            (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
          $total_liabilitas_pendek_nag = 0;
          $total_liabilitas_pendek_nak = 0;
          $total_liabilitas_pendek_all = 0;
          while($row3 = mysqli_fetch_array($sql3)){
              $liabilitas_pendek_nag = $row3['total_nag'] ?? 0;
              $liabilitas_pendek_nak = $row3['total_nak'] ?? 0;
              $liabilitas_pendek_all = $row3['total_all'] ?? 0;
              $ljp_nag = $liabilitas_pendek_nag > 0 ? number_format($liabilitas_pendek_nag,2) : '(' . number_format(abs($liabilitas_pendek_nag),2) . ')';
              $ljp_nak = $liabilitas_pendek_nak > 0 ? number_format($liabilitas_pendek_nak,2) : '(' . number_format(abs($liabilitas_pendek_nak),2) . ')';
              $ljp_all = $liabilitas_pendek_all > 0 ? number_format($liabilitas_pendek_all,2) : '(' . number_format(abs($liabilitas_pendek_all),2) . ')';
              $total_liabilitas_pendek_nag += $liabilitas_pendek_nag;
              $total_liabilitas_pendek_nak += $liabilitas_pendek_nak;
              $total_liabilitas_pendek_all += $liabilitas_pendek_all;
              $total_ljp_nag = $total_liabilitas_pendek_nag > 0 ? number_format($total_liabilitas_pendek_nag,2) : '(' . number_format(abs($total_liabilitas_pendek_nag),2) . ')';
              $total_ljp_nak = $total_liabilitas_pendek_nak > 0 ? number_format($total_liabilitas_pendek_nak,2) : '(' . number_format(abs($total_liabilitas_pendek_nak),2) . ')';
              $total_ljp_all = $total_liabilitas_pendek_all > 0 ? number_format($total_liabilitas_pendek_all,2) : '(' . number_format(abs($total_liabilitas_pendek_all),2) . ')';
              echo "
                <tr>
                  <td class='item-left'>{$row3['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$ljp_nag}</td>";
                    echo "<td class='item-right'>{$ljp_nak}</td>";
                    echo "<td class='item-right'>{$ljp_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$ljp_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$ljp_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row3['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <tr class="total-line">
            <th class="total-left">Jumlah Liabilitas jangka Pendek  </th>
            <?php 
                  if ($profit_center == 'ALL') {
                    echo "<td class='total-right'>{$total_ljp_nag}</td>";
                    echo "<td class='total-right'>{$total_ljp_nak}</td>";
                    echo "<td class='total-right'>{$total_ljp_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='total-right'>{$total_ljp_nag}</td>";
                  }else{
                    echo "<td class='total-right'>{$total_ljp_nak}</td>";
                  }
             ?>
            <th class="total-italic">Total Current Liabilities</th>
          </tr>

          <tr class="spacer-small">
            <th></th>
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
            <th></th>
          </tr>
          <!-- LIABILITAS JANGKA PENDEK -->
          <tr>
            <th class="subsection-left">LIABILITAS JANGKA PANJANG</th>
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
            <th class="subsection-right">NONCURRENT LIABILITIES</th>

          </tr>
          <?php
          $sql4 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('LIABILITAS JANGKA PANJANG')) a left JOIN
            (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
          $total_liabilitas_panjang_nag = 0;
          $total_liabilitas_panjang_nak = 0;
          $total_liabilitas_panjang_all = 0;
          while($row4 = mysqli_fetch_array($sql4)){
              $liabilitas_panjang_nag = $row4['total_nag'] ?? 0;
              $liabilitas_panjang_nak = $row4['total_nak'] ?? 0;
              $liabilitas_panjang_all = $row4['total_all'] ?? 0;
              $ljpj_nag = $liabilitas_panjang_nag > 0 ? number_format($liabilitas_panjang_nag,2) : '(' . number_format(abs($liabilitas_panjang_nag),2) . ')';
              $ljpj_nak = $liabilitas_panjang_nak > 0 ? number_format($liabilitas_panjang_nak,2) : '(' . number_format(abs($liabilitas_panjang_nak),2) . ')';
              $ljpj_all = $liabilitas_panjang_all > 0 ? number_format($liabilitas_panjang_all,2) : '(' . number_format(abs($liabilitas_panjang_all),2) . ')';
              $total_liabilitas_panjang_nag += $liabilitas_panjang_nag;
              $total_liabilitas_panjang_nak += $liabilitas_panjang_nak;
              $total_liabilitas_panjang_all += $liabilitas_panjang_all;
              $total_ljpj_nag = $total_liabilitas_panjang_nag > 0 ? number_format($total_liabilitas_panjang_nag,2) : '(' . number_format(abs($total_liabilitas_panjang_nag),2) . ')';
              $total_ljpj_nak = $total_liabilitas_panjang_nak > 0 ? number_format($total_liabilitas_panjang_nak,2) : '(' . number_format(abs($total_liabilitas_panjang_nak),2) . ')';
              $total_ljpj_all = $total_liabilitas_panjang_all > 0 ? number_format($total_liabilitas_panjang_all,2) : '(' . number_format(abs($total_liabilitas_panjang_all),2) . ')';
              echo "
                <tr>
                  <td class='item-left'>{$row4['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$ljpj_nag}</td>";
                    echo "<td class='item-right'>{$ljpj_nak}</td>";
                    echo "<td class='item-right'>{$ljpj_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$ljpj_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$ljpj_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row4['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <tr class="total-line">
            <th class="total-left">Jumlah Liabilitas jangka Panjang </th>
            <?php 
                  if ($profit_center == 'ALL') {
                    echo "<td class='total-right'>{$total_ljpj_nag}</td>";
                    echo "<td class='total-right'>{$total_ljpj_nak}</td>";
                    echo "<td class='total-right'>{$total_ljpj_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='total-right'>{$total_ljpj_nag}</td>";
                  }else{
                    echo "<td class='total-right'>{$total_ljpj_nak}</td>";
                  }
             ?>
            <th class="total-italic">Total Noncurrent Liabilities</th>
          </tr>

          <tr class="spacer-small">
            <th></th>
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
            <th></th>
          </tr>
          <!-- EKUITAS -->
          <tr>
            <th class="subsection-left">EKUITAS</th>
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
            <th class="subsection-right">EQUITY</th>

          </tr>
          <?php
          $sql5 = mysqli_query($conn2,"select id, sub_kategori, sum(saldo_akhir_nag) total_nag, sum(saldo_akhir_nak) total_nak, sum(saldo_akhir_all) total_all, sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('EKUITAS')) a left JOIN
            (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) b on b.ind_categori4 = a.sub_kategori GROUP BY a.id order by id asc");
          $total_ekuitas_nag = 0;
          $total_ekuitas_nak = 0;
          $total_ekuitas_all = 0;
          while($row5 = mysqli_fetch_array($sql5)){
              $ekuitas_nag = $row5['total_nag'] ?? 0;
              $ekuitas_nak = $row5['total_nak'] ?? 0;
              $ekuitas_all = $row5['total_all'] ?? 0;
              $ekt_nag = $ekuitas_nag > 0 ? number_format($ekuitas_nag,2) : '(' . number_format(abs($ekuitas_nag),2) . ')';
              $ekt_nak = $ekuitas_nak > 0 ? number_format($ekuitas_nak,2) : '(' . number_format(abs($ekuitas_nak),2) . ')';
              $ekt_all = $ekuitas_all > 0 ? number_format($ekuitas_all,2) : '(' . number_format(abs($ekuitas_all),2) . ')';
              $total_ekuitas_nag += $ekuitas_nag;
              $total_ekuitas_nak += $ekuitas_nak;
              $total_ekuitas_all += $ekuitas_all;
              
              echo "
                <tr>
                  <td class='item-left'>{$row5['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$ekt_nag}</td>";
                    echo "<td class='item-right'>{$ekt_nak}</td>";
                    echo "<td class='item-right'>{$ekt_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$ekt_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$ekt_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row5['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <?php
          $sql6 = mysqli_query($conn2,"select 'Laba Tahun Berjalan' sub_kategori, 'Profit of the year' sub_kategori_eng, sum(COALESCE(saldo_akhir_nag,0)) as total_nag, sum(COALESCE(saldo_akhir_nak,0)) as total_nak, sum(COALESCE(saldo_akhir_all,0)) as total_all from (select a.*,sum(b.saldo_akhir) saldo_akhir_nag, sum(c.saldo_akhir) saldo_akhir_nak, sum(b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
            (select no_coa, nama_coa, id_ctg2, id_ctg4, ind_categori4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,jan_2025 as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a where no_coa >= '3.40.01' order by no_coa asc) b on b.no_coa = a.no_coa
            left join 
            (select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,jan_2025 as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a where no_coa >= '3.40.01' order by no_coa asc) c on c.no_coa = a.no_coa GROUP BY id_ctg4) a");
          $total_laba_tahun_berjalan_nag = 0;
          $total_laba_tahun_berjalan_nak = 0;
          $total_laba_tahun_berjalan_all = 0;
          while($row6 = mysqli_fetch_array($sql6)){
              $laba_tahun_berjalan_nag = $row6['total_nag'] ?? 0;
              $laba_tahun_berjalan_nak = $row6['total_nak'] ?? 0;
              $laba_tahun_berjalan_all = $row6['total_all'] ?? 0;
              $ltb_nag = $laba_tahun_berjalan_nag > 0 ? number_format($laba_tahun_berjalan_nag,2) : '(' . number_format(abs($laba_tahun_berjalan_nag),2) . ')';
              $ltb_nak = $laba_tahun_berjalan_nak > 0 ? number_format($laba_tahun_berjalan_nak,2) : '(' . number_format(abs($laba_tahun_berjalan_nak),2) . ')';
              $ltb_all = $laba_tahun_berjalan_all > 0 ? number_format($laba_tahun_berjalan_all,2) : '(' . number_format(abs($laba_tahun_berjalan_all),2) . ')';
              $total_laba_tahun_berjalan_nag += $laba_tahun_berjalan_nag;
              $total_laba_tahun_berjalan_nak += $laba_tahun_berjalan_nak;
              $total_laba_tahun_berjalan_all += $laba_tahun_berjalan_all;
              $total_ltb_nag = $total_laba_tahun_berjalan_nag > 0 ? number_format($total_laba_tahun_berjalan_nag,2) : '(' . number_format(abs($total_laba_tahun_berjalan_nag),2) . ')';
              $total_ltb_nak = $total_laba_tahun_berjalan_nak > 0 ? number_format($total_laba_tahun_berjalan_nak,2) : '(' . number_format(abs($total_laba_tahun_berjalan_nak),2) . ')';
              $total_ltb_all = $total_laba_tahun_berjalan_all > 0 ? number_format($total_laba_tahun_berjalan_all,2) : '(' . number_format(abs($total_laba_tahun_berjalan_all),2) . ')';
              echo "
                <tr>
                  <td class='item-left'>{$row6['sub_kategori']}</td>";
                  if ($profit_center == 'ALL') {
                    echo "<td class='item-right'>{$ltb_nag}</td>";
                    echo "<td class='item-right'>{$ltb_nak}</td>";
                    echo "<td class='item-right'>{$ltb_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='item-right'>{$ltb_nag}</td>";
                  }else{
                    echo "<td class='item-right'>{$ltb_nak}</td>";
                  }
                  echo "<td class='item-italic'>{$row6['sub_kategori_eng']}</td>
                </tr>
              ";
          }
          ?>

          <tr class="total-line">
            <th class="total-left">Jumlah Ekuitas </th>
            <?php 
              $total_ekt_nag = ($total_ekuitas_nag + $total_laba_tahun_berjalan_nag) > 0 ? number_format(($total_ekuitas_nag + $total_laba_tahun_berjalan_nag),2) : '(' . number_format(abs(($total_ekuitas_nag + $total_laba_tahun_berjalan_nag)),2) . ')';
              $total_ekt_nak = ($total_ekuitas_nak + $total_laba_tahun_berjalan_nak) > 0 ? number_format(($total_ekuitas_nak + $total_laba_tahun_berjalan_nak),2) : '(' . number_format(abs(($total_ekuitas_nak + $total_laba_tahun_berjalan_nak)),2) . ')';
              $total_ekt_all = ($total_ekuitas_all + $total_laba_tahun_berjalan_all) > 0 ? number_format(($total_ekuitas_all + $total_laba_tahun_berjalan_all),2) : '(' . number_format(abs(($total_ekuitas_all + $total_laba_tahun_berjalan_all)),2) . ')';
                  if ($profit_center == 'ALL') {
                    echo "<td class='total-right'>{$total_ekt_nag}</td>";
                    echo "<td class='total-right'>{$total_ekt_nak}</td>";
                    echo "<td class='total-right'>{$total_ekt_all}</td>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<td class='total-right'>{$total_ekt_nag}</td>";
                  }else{
                    echo "<td class='total-right'>{$total_ekt_nak}</td>";
                  }
             ?>
            <th class="total-italic">Total Equity</th>
          </tr>

          <tr class="spacer-mid">
            <th></th>
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
            <th></th>
          </tr>

          <tr class="grand-total">
            <th class="grand-left">JUMLAH LIABILITAS DAN EKUITAS</th>
            <?php
            $total_liabilitas_ekuitas_nag = $total_liabilitas_pendek_nag + $total_liabilitas_panjang_nag + $total_ekuitas_nag + $total_laba_tahun_berjalan_nag;
            $total_liabilitas_ekuitas_nak = $total_liabilitas_pendek_nak + $total_liabilitas_panjang_nak + $total_ekuitas_nak + $total_laba_tahun_berjalan_nak;
            $total_liabilitas_ekuitas_all = $total_liabilitas_pendek_all + $total_liabilitas_panjang_all + $total_ekuitas_all + $total_laba_tahun_berjalan_all;

            $total_liek_nag = $total_liabilitas_ekuitas_nag > 0 ? number_format($total_liabilitas_ekuitas_nag,2) : '(' . number_format(abs($total_liabilitas_ekuitas_nag),2) . ')';
            $total_liek_nak = $total_liabilitas_ekuitas_nak > 0 ? number_format($total_liabilitas_ekuitas_nak,2) : '(' . number_format(abs($total_liabilitas_ekuitas_nak),2) . ')';
            $total_liek_all = $total_liabilitas_ekuitas_all > 0 ? number_format($total_liabilitas_ekuitas_all,2) : '(' . number_format(abs($total_liabilitas_ekuitas_all),2) . ')';

                  if ($profit_center == 'ALL') {
                    echo "<th class='grand-right'>{$total_liek_nag}</th>";
                    echo "<th class='grand-right'>{$total_liek_nak}</th>";
                    echo "<th class='grand-right'>{$total_liek_all}</th>";
                  }elseif ($profit_center == 'NAG') {
                    echo "<th class='grand-right'>{$total_liek_nag}</th>";
                  }else{
                    echo "<th class='grand-right'>{$total_liek_nak}</th>";
                  }
             ?>
            <th class="grand-italic">TOTAL LIABILITIES AND EQUITY</th>
          </tr>

        </table>
      </div>
    </div>
  </div>
</div>



