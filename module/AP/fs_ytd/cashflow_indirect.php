<style>
  /* Reskin CSS-only - struktur HTML/PHP tabel TIDAK disentuh (lihat catatan
     lengkap di fs_ytd/statement_financial_position.php) - cuma warna/spacing
     diselaraskan ke bahasa visual biru-emas tab lain. Juga memperbaiki typo
     lama "dicfindirectay: flex" (harusnya "display: flex" - hasil find-
     replace serampangan waktu file ini di-clone) yang bikin tombol Export/
     Print dulu tidak sejajar horizontal. */
  #cf-indirect .card-body {
    background: #f9fafb;
  }
  #cf-indirect table {
    color: #2c3e50;
  }
  #cf-indirect th, #cf-indirect td {
    padding: 8px 10px;
  }
  #cf-indirect .table-primary {
    background-color: #e9f3ff !important;
  }

  table th, table td {
    padding: 0 !important;
  }

  .laporan-container-cfindirect {
    border: 1px solid #dbe3f0;
    border-radius: 14px;
    padding: 22px 28px 20px;
    background: #fafafa;
    box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  }

  .laporan-table-cfindirect {
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
    text-transform: uppercase;
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

  #cf-indirect .laporan-table-cfindirect tr:hover .item-left,
  #cf-indirect .laporan-table-cfindirect tr:hover .item-right {
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

/* ===== Freeze judul (header) CF Indirect YTD - pola sama persis dgn tab SFP.
   Scoped #cf-indirect, class-based (Excel/PDF tidak berubah). */
#cf-indirect .laporan-container-cfindirect {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#cf-indirect .laporan-container-cfindirect::-webkit-scrollbar { height: 10px; width: 10px; }
#cf-indirect .laporan-container-cfindirect::-webkit-scrollbar-track { background: #f1f4fa; }
#cf-indirect .laporan-container-cfindirect::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#cf-indirect .laporan-table-cfindirect { border-collapse: separate; border-spacing: 0; }
#cf-indirect .laporan-table-cfindirect thead { position: sticky; top: 0; z-index: 5; }
#cf-indirect .laporan-table-cfindirect thead th { background: #fafafa; }
#cf-indirect .laporan-table-cfindirect thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
/* Baris TOTAL diberi latar biru muda #e8edfa - sama seperti baris grand total
   di SFP/SPL - supaya menonjol. Scoped #cf-indirect saja. */
#cf-indirect .laporan-table-cfindirect .total-line { background: #e8edfa; }
</style>


<div class="export-buttons mt-2">
  <button id="btnExcel-cfindirect" class="btn-export excel">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
      <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
    Export Excel
  </button>
  <button id="btnPDF-cfindirect" class="btn-export pdf">
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
      <div class="laporan-container-cfindirect" id="laporan-cfindirect-ytd">
        <table class="laporan-table-cfindirect" border="0" role="grid" cellspacing="0">
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
        </thead>
        <tbody>
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


