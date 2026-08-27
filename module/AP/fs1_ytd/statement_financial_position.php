<style>
/* Reskin CSS-only, pola sama persis dgn fs_ytd/statement_financial_position.php
   (FS2 YTD SFP) & tab-tab FS1 lain (Trial Balance/CF Direct) - dulu file ini
   TIDAK punya class sama sekali (semua style inline text-align/vertical-align/
   width per <th>/<td>), tanpa card, tanpa tombol Export yg dipercantik - jadi
   BEDA dari class-class di file ini benar2 ditambahkan baru (bukan cuma
   nambah CSS), tapi isi/urutan/logic PHP query & echo-nya TIDAK diubah sama
   sekali, cuma markup+attribute per elemen yg diganti dari inline style ke
   class. FS1 TIDAK ada breakdown profit center (NAG/NAK/Total) makanya
   cuma 3 kolom (desc ID | angka | desc EN), beda dari FS2 yg kolom angkanya
   bisa 1-3x tergantung filter Profit Center. */
#sfp1ytd .card-body {
  background: #f9fafb;
}
#sfp1ytd table {
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

.judul-periode {
  text-align: center;
  border-bottom: 2px solid #1e3a8a;
  color: #1e3a8a;
  font-weight: 600;
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

#sfp1ytd .laporan-table tr:hover .item-left,
#sfp1ytd .laporan-table tr:hover .item-right {
  background-color: rgba(37, 99, 235, 0.06);
}

/* ===== Totals ===== */
.total-line {
  line-height: 28px;
  font-weight: bold;
}

.total-left {
  text-align: left;
}

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

/* ===== Grand Total ===== */
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

/* ===== Spacers ===== */
.spacer {
  height: 15px;
}

.spacer-small {
  height: 5px;
}

/* ===== Tombol Export - pill gradien + ikon SVG, konsisten dgn tab SFP
   FS2 YTD/tab-tab FS1 lain. Dulu tombol Bootstrap kotak polos (btn-success)
   dibungkus <a>. Mekanisme TIDAK diubah (tetap <a href="ekspor_sfp_ytd.php?...">
   navigasi langsung, bukan wired via JS #btnExcel spt FS2 - lebih simpel &
   tidak perlu sentuh financial_statement.php sama sekali). */
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

/* ===== Freeze judul (header) FS1 YTD SFP - pola sama dgn FS2 YTD.
   Scoped #sfp1ytd (wrapper file ini) supaya tidak mempengaruhi tab lain. */
#sfp1ytd .laporan-container {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#sfp1ytd .laporan-container::-webkit-scrollbar { height: 10px; width: 10px; }
#sfp1ytd .laporan-container::-webkit-scrollbar-track { background: #f1f4fa; }
#sfp1ytd .laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#sfp1ytd .laporan-table { border-collapse: separate; border-spacing: 0; }
#sfp1ytd .laporan-table thead { position: sticky; top: 0; z-index: 5; }
#sfp1ytd .laporan-table thead th { background: #fafafa; }
#sfp1ytd .laporan-table thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
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
    <a target="_blank" class="btn-export excel" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'">
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
<div id="sfp1ytd" class="table-responsive mt-1">
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
            <th class="judul-left">LAPORAN POSISI KEUANGAN</th>
            <th></th>
            <th class="judul-right">STATEMENTS OF FINANCIAL POSITION</th>
        </tr>
        <tr>
            <th class="judul-left"><?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th></th>
            <th class="judul-right"><?php
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
            <th class="judul-periode">YTD <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <!-- aset - start -->
        <tr class="spacer">
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th class="section-left">ASET</th>
            <th></th>
            <th class="section-right">ASSETS</th>
        </tr>
        <tr class="spacer-small">
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <!-- aset_tetap - start -->
        <tr>
            <th class="subsection-left">ASET LANCAR</th>
            <th></th>
            <th class="subsection-right">CURRENT ASSETS</th>
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

        $sql = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('ASET LANCAR')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql2 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('ASET TIDAK LANCAR')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql3 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('LIABILITAS JANGKA PENDEK')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql4 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('LIABILITAS JANGKA PANJANG')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql5 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('EKUITAS')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql6 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,no_coa,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
            jnl on jnl.coa_no = coa.no_coa where no_coa >= '3.40.01' order by no_coa asc) a group by a.id_ctg4) a) a");

        if($tanggal_akhir < $tanggal_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_aset_lancar = 0;
            while($row = mysqli_fetch_array($sql)){
                $aset_lancar = isset($row['total']) ? $row['total'] : 0;
                if ($aset_lancar > 0) {
                    $aset_lancar = number_format($aset_lancar,2);
                }else{
                    $aset_lancar = '('.number_format(abs($aset_lancar),2).')';
                }

                $total_aset_lancar += isset($row['total']) ? $row['total'] : 0;
                if ($total_aset_lancar > 0) {
                    $total_aset_lancar_ = number_format($total_aset_lancar,2);
                }else{
                    $total_aset_lancar_ = '('.number_format(abs($total_aset_lancar),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row['sub_kategori'].'</td>
                <td class="item-right">'.$aset_lancar.'</td>
                <td class="item-italic">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr class="total-line">
            <th class="total-left">Jumlah Aset Lancar</th>
            <th class="total-right">'.$total_aset_lancar_.'</th>
            <th class="total-italic">Total Current Asset</th>
            </tr>
            <tr class="spacer-small"><th></th><th></th><th></th></tr>
            <tr>
            <th class="subsection-left">ASET TIDAK LANCAR</th>
            <th></th>
            <th></th>
            </tr>';

            $total_aset_tidak_lancar = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $aset_tidak_lancar = isset($row2['total']) ? $row2['total'] : 0;
                if ($aset_tidak_lancar > 0) {
                    $aset_tidak_lancar = number_format($aset_tidak_lancar,2);
                }else{
                    $aset_tidak_lancar = '('.number_format(abs($aset_tidak_lancar),2).')';
                }

                $total_aset_tidak_lancar += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_aset_tidak_lancar > 0) {
                    $total_aset_tidak_lancar_ = number_format($total_aset_tidak_lancar,2);
                }else{
                    $total_aset_tidak_lancar_ = '('.number_format(abs($total_aset_tidak_lancar),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row2['sub_kategori'].'</td>
                <td class="item-right">'.$aset_tidak_lancar.'</td>
                <td class="item-italic">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            $total_asset = $total_aset_lancar + $total_aset_tidak_lancar;
            if ($total_asset > 0) {
                $total_asset_ = number_format($total_asset,2);
            }else{
                $total_asset_ = '('.number_format(abs($total_asset),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">Jumlah Aset Tidak Lancar</th>
            <th class="total-right">'.$total_aset_tidak_lancar_.'</th>
            <th class="total-italic">Total Nonurrent Asset</th>
            </tr>
            <tr class="grand-total">
            <th class="grand-left">JUMLAH ASET</th>
            <th class="grand-right">'.$total_asset_.'</th>
            <th class="grand-italic">TOTAL ASSET</th>
            </tr>
            <tr class="spacer"><th></th><th></th><th></th></tr>
            <tr>
            <th class="section-left">LIABILITAS DAN EKUITAS</th>
            <th></th>
            <th class="section-right">LIABILITIES AND EQUITY</th>
            </tr>
            <tr class="spacer-small"><th></th><th></th><th></th></tr>

            <tr>
            <th class="subsection-left">LIABILITAS JANGKA PENDEK</th>
            <th></th>
            <th class="subsection-right">CURRENT LIABILITIES</th>
            </tr>';

            $total_liabilitas_jangka_pendek  = 0;
            while($row3 = mysqli_fetch_array($sql3)){
                $liabilitas_jangka_pendek = isset($row3['total']) ? $row3['total'] : 0;
                if ($liabilitas_jangka_pendek > 0) {
                    $liabilitas_jangka_pendek = number_format($liabilitas_jangka_pendek,2);
                }else{
                    $liabilitas_jangka_pendek = '('.number_format(abs($liabilitas_jangka_pendek),2).')';
                }

                $total_liabilitas_jangka_pendek += isset($row3['total']) ? $row3['total'] : 0;
                if ($total_liabilitas_jangka_pendek > 0) {
                    $total_liabilitas_jangka_pendek_ = number_format($total_liabilitas_jangka_pendek,2);
                }else{
                    $total_liabilitas_jangka_pendek_ = '('.number_format(abs($total_liabilitas_jangka_pendek),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row3['sub_kategori'].'</td>
                <td class="item-right">'.$liabilitas_jangka_pendek.'</td>
                <td class="item-italic">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr class="total-line">
            <th class="total-left">Jumlah Liabilitas jangka Pendek</th>
            <th class="total-right">'.$total_liabilitas_jangka_pendek_.'</th>
            <th class="total-italic">Total Current Liabilities</th>
            </tr>
            <tr class="spacer-small"><th></th><th></th><th></th></tr>
            <tr>
            <th class="subsection-left">LIABILITAS JANGKA PANJANG</th>
            <th></th>
            <th class="subsection-right">NONCURRENT LIABILITIES</th>
            </tr>';

            $total_liabilitas_jangka_panjang  = 0;
            while($row4 = mysqli_fetch_array($sql4)){
                $liabilitas_jangka_panjang = isset($row4['total']) ? $row4['total'] : 0;
                if ($liabilitas_jangka_panjang > 0) {
                    $liabilitas_jangka_panjang = number_format($liabilitas_jangka_panjang,2);
                }else{
                    $liabilitas_jangka_panjang = '('.number_format(abs($liabilitas_jangka_panjang),2).')';
                }

                $total_liabilitas_jangka_panjang += isset($row4['total']) ? $row4['total'] : 0;
                if ($total_liabilitas_jangka_panjang > 0) {
                    $total_liabilitas_jangka_panjang_ = number_format($total_liabilitas_jangka_panjang,2);
                }else{
                    $total_liabilitas_jangka_panjang_ = '('.number_format(abs($total_liabilitas_jangka_panjang),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row4['sub_kategori'].'</td>
                <td class="item-right">'.$liabilitas_jangka_panjang.'</td>
                <td class="item-italic">'.$row4['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr class="total-line">
            <th class="total-left">Jumlah Liabilitas jangka Panjang</th>
            <th class="total-right">'.$total_liabilitas_jangka_panjang_.'</th>
            <th class="total-italic">Total Noncurrent Liabilities</th>
            </tr>
            <tr class="spacer-small"><th></th><th></th><th></th></tr>
            <tr>
            <th class="subsection-left">EKUITAS</th>
            <th></th>
            <th class="subsection-right">EQUITY</th>
            </tr>';

            $total_ekuitas  = 0;
            $total_ekuits = 0;
            while($row5 = mysqli_fetch_array($sql5)){
                $ekuitas = isset($row5['total']) ? $row5['total'] : 0;
                if ($ekuitas > 0) {
                    $ekuitas = number_format($ekuitas,2);
                }else{
                    $ekuitas = '('.number_format(abs($ekuitas),2).')';
                }

                $total_ekuits += isset($row5['total']) ? $row5['total'] : 0;
                // if ($total_ekuitas > 0) {
                //     $total_ekuitas_ = number_format($total_ekuitas,2);
                // }else{
                //     $total_ekuitas_ = '('.number_format(abs($total_ekuitas),2).')';
                // }

                echo '<tr>
                <td class="item-left">'.$row5['sub_kategori'].'</td>
                <td class="item-right">'.$ekuitas.'</td>
                <td class="item-italic">'.$row5['sub_kategori_eng'].'</td>
                </tr>';
            }

            $total_laba_rugi_berjalan  = 0;
            $row6 = mysqli_fetch_array($sql6);
            $laba_rugi_berjalan = isset($row6['total']) ? $row6['total'] : 0;
            if ($laba_rugi_berjalan > 0) {
                $laba_rugi_berjalan = number_format($laba_rugi_berjalan,2);
            }else{
                $laba_rugi_berjalan = '('.number_format(abs($laba_rugi_berjalan),2).')';
            }

            $total_laba_rugi_berjalan += isset($row6['total']) ? $row6['total'] : 0;
            $total_ekuitas = $total_ekuits + $total_laba_rugi_berjalan;
            if ($total_ekuitas > 0) {
                $total_ekuitas_ = number_format($total_ekuitas,2);
            }else{
                $total_ekuitas_ = '('.number_format(abs($total_ekuitas),2).')';
            }

            echo '<tr>
            <td class="item-left">Laba Tahun Berjalan</td>
            <td class="item-right">'.$laba_rugi_berjalan.'</td>
            <td class="item-italic">Profit of the year</td>
            </tr>';


            $total_liabilitas_ekuitas = $total_liabilitas_jangka_pendek + $total_liabilitas_jangka_panjang + $total_ekuitas;
            if ($total_liabilitas_ekuitas > 0) {
                $total_liabilitas_ekuitas_ = number_format($total_liabilitas_ekuitas,2);
            }else{
                $total_liabilitas_ekuitas_ = '('.number_format(abs($total_liabilitas_ekuitas),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">Jumlah Ekuitas</th>
            <th class="total-right">'.$total_ekuitas_.'</th>
            <th class="total-italic">Total Equity</th>
            </tr>
            <tr class="grand-total">
            <th class="grand-left">JUMLAH LIABILITAS DAN EKUITAS</th>
            <th class="grand-right">'.$total_liabilitas_ekuitas_.'</th>
            <th class="grand-italic">TOTAL LIABILITIES AND EQUITY</th>
            </tr>';


        }
        ?>
        </tbody>
    </table>
      </div>
    </div>
  </div>
</div>
