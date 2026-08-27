<style>
/* Reskin CSS-only, pola sama persis dgn fs1_ytd/statement_financial_position.php
   (FS1 YTD SFP) & fs_ytd/statement_profit_loss.php (FS2 YTD SPL) - dulu
   file ini TIDAK punya class sama sekali (semua style inline text-align/
   vertical-align/width per <th>/<td>), tanpa card, tanpa tombol Export yg
   dipercantik - jadi class-class di file ini benar2 ditambahkan baru
   (bukan cuma nambah CSS), tapi isi/urutan/logic PHP query & echo-nya
   TIDAK diubah sama sekali, cuma markup+attribute per elemen yg diganti
   dari inline style ke class. FS1 TIDAK ada breakdown profit center
   (NAG/NAK/Total) makanya cuma 4 kolom (desc ID | angka | % | desc EN),
   beda dari FS2 yg kolom angkanya bisa dikali 3 tergantung filter Profit
   Center. */
#spl1ytd .card-body {
  background: #f9fafb;
}
#spl1ytd table {
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
  text-align: right;
  border-bottom: 2px solid #1e3a8a;
  color: #1e3a8a;
  font-weight: 600;
  padding-bottom: 6px !important;
  padding-right: 10px !important;
}

.persentage-header {
  text-align: center;
  border-bottom: 2px solid #1e3a8a;
  color: #1e3a8a;
  font-weight: 600;
  padding-bottom: 6px !important;
  padding-left: 10px !important;
  font-size: 12px;
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

/* ===== Data Rows ===== */
.item-left {
  text-align: left;
  padding: 4px 0 !important;
}

.item-right {
  text-align: right;
  font-variant-numeric: tabular-nums;
}

.item-percent {
  text-align: right;
  font-variant-numeric: tabular-nums;
  color: #8a94a6;
  font-size: 12px;
}

.item-italic {
  text-align: right;
  font-style: italic;
  color: #6b7280;
}

#spl1ytd .laporan-table tr:hover .item-left,
#spl1ytd .laporan-table tr:hover .item-right {
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

.total-percent {
  text-align: right;
  font-variant-numeric: tabular-nums;
  border-top: 2px solid #94a3c4;
  font-size: 12px;
  color: #6b7280;
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

.grand-percent {
  text-align: right;
  color: #1e3a8a;
  font-variant-numeric: tabular-nums;
  border-top: 2px solid #1e3a8a;
  font-size: 12px;
}

.grand-italic {
  text-align: right;
  font-style: italic;
  color: #5b6a94;
}

/* ===== Tombol Export - pill gradien + ikon SVG. Mekanisme TIDAK diubah
   (tetap <a href="ekspor_spl_ytd.php?...">, navigasi langsung). */
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

/* ===== Freeze judul (header) FS1 YTD SPL - pola sama dgn FS2. Scoped #spl1ytd. */
#spl1ytd .laporan-container {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#spl1ytd .laporan-container::-webkit-scrollbar { height: 10px; width: 10px; }
#spl1ytd .laporan-container::-webkit-scrollbar-track { background: #f1f4fa; }
#spl1ytd .laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#spl1ytd .laporan-table { border-collapse: separate; border-spacing: 0; }
#spl1ytd .laporan-table thead { position: sticky; top: 0; z-index: 5; }
#spl1ytd .laporan-table thead th { background: #fafafa; }
#spl1ytd .laporan-table thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
</style>

<?php
// Guard pembagian nol - kalau Penjualan Bersih (pembagi) = 0 (mis. periode
// belum ada transaksi sama sekali), PHP menghasilkan NAN (0/0) yang lolos
// ke number_format() jadi teks "nan%" di tampilan - sama seperti bug yang
// sudah diperbaiki di fs_ytd/statement_profit_loss.php (FS2 YTD SPL).
// Semua pemakaian "number_format(($x / $penjualan_bersih * 100),2)" di
// bawah dibungkus lewat helper ini supaya tampil "0.00%" saja kalau
// basisnya kosong.
function fs1YtdSplPercent($nilai, $basis) {
    if (empty($basis)) {
        return number_format(0, 2);
    }
    return number_format(($nilai / $basis * 100), 2);
}

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
    <a target="_blank" class="btn-export excel" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'">
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
<div id="spl1ytd" class="table-responsive mt-1">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <div class="laporan-container">
    <table class="laporan-table" border="0" role="grid" cellspacing="0">
        <thead>
        <tr>
            <th class="judul-left">PT NIRWANA ALABARE GARMENT</th>
            <th></th>
            <th></th>
            <th class="judul-right">PT NIRWANA ALABARE GARMENT</th>
        </tr>
        <tr>
            <th class="judul-left">LAPORAN LABA ATAU RUGI  DAN PENGHASILAN KOMPREHENSIF LAINNYA</th>
            <th></th>
            <th></th>
            <th class="judul-right">STATEMENTS OF PROFIT OR LOSS AND OTHER COMPREHENSIVE INCOME</th>
        </tr>
        <tr>
            <th class="judul-left">UNTUK TAHUN YANG BERAKHIR PADA TANGGAL <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th></th>
            <th></th>
            <th class="judul-right">FOR THE YEARS ENDED <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
        </tr>

        <tr>
            <th class="desc-left">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th></th>
            <th></th>
            <th class="desc-right">(Expressed in Rupiah, unless otherwise stated)</th>
        </tr>
        <tr>
            <th></th>
            <th class="judul-periode">YTD <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th class="persentage-header">Persentage</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <!-- penjualan-kotor - start -->
        <tr>
            <th class="section-left">PENJUALAN KOTOR</th>
            <th></th>
            <th></th>
            <th class="section-right">GROSS SALES</th>
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

        $sql_nets = mysqli_query($conn2,"select id,sub_kategori,- sum(total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR','RETURN PENJUALAN','POTONGAN PENJUALAN')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $row_nets = mysqli_fetch_array($sql_nets);
        $penjualan_bersih = isset($row_nets['total']) ? $row_nets['total'] : 0;

        $sql = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql2 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('RETURN PENJUALAN')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql3 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('POTONGAN PENJUALAN')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql4 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN POKOK PENJUALAN')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql5 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN LAINNYA')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql6 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN BUNGA')) a
            left join (select id_ctg2,id_ctg4,ind_name ind4 from master_coa_ctg4 where id_ctg2 = '8') c on c.ind4 = a.sub_kategori left JOIN
            (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where id_ctg2 = '8' GROUP BY a.id_ctg4) b on b.id_ctg4 = c.id_ctg4 order by id asc");

        $sql7 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN PAJAK')) a left JOIN
           (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
           (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
           left join
           (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
           on coa.no_coa = saldo.nocoa
           left join
           (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
           jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");


        if($tanggal_akhir < $tanggal_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_penjualan_kotor = 0;
            while($row = mysqli_fetch_array($sql)){
                $penjualan_kotor = isset($row['total']) ? $row['total'] : 0;
                $per_penjualan_kotor = fs1YtdSplPercent($penjualan_kotor, $penjualan_bersih);
                if ($penjualan_kotor > 0) {
                    $penjualan_kotor = number_format($penjualan_kotor,2);
                }else{
                    $penjualan_kotor = '('.number_format(abs($penjualan_kotor),2).')';
                }

                $total_penjualan_kotor += isset($row['total']) ? $row['total'] : 0;
                if ($total_penjualan_kotor > 0) {
                    $total_penjualan_kotor_ = number_format($total_penjualan_kotor,2);
                }else{
                    $total_penjualan_kotor_ = '('.number_format(abs($total_penjualan_kotor),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row['sub_kategori'].'</td>
                <td class="item-right">'.$penjualan_kotor.'</td>
                <td class="item-percent">'.$per_penjualan_kotor.'%</td>
                <td class="item-italic">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr class="total-line">
            <th class="total-left">TOTAL PENJUALAN KOTOR</th>
            <th class="total-right">'.$total_penjualan_kotor_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($total_penjualan_kotor, $penjualan_bersih).'%</th>
            <th class="total-italic">GROSS SALES TOTAL</th>
            </tr>
            <tr>
            <th class="section-left">RETURN PENJUALAN</th>
            <th></th>
            <th></th>
            <th class="section-right">SALES RETURN</th>
            </tr>';

            $total_retur_penjualan = 0;
            $total_retur_penjualan_ = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $retur_penjualan = isset($row2['total']) ? $row2['total'] : 0;
                $per_retur_penjualan = fs1YtdSplPercent($retur_penjualan, $penjualan_bersih);
                if ($retur_penjualan > 0) {
                    $retur_penjualan = number_format($retur_penjualan,2);
                }else{
                    $retur_penjualan = '('.number_format(abs($retur_penjualan),2).')';
                }

                $total_retur_penjualan += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_retur_penjualan > 0) {
                    $total_retur_penjualan_ = number_format($total_retur_penjualan,2);
                }else{
                    $total_retur_penjualan_ = '('.number_format(abs($total_retur_penjualan),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row2['sub_kategori'].'</td>
                <td class="item-right">'.$retur_penjualan.'</td>
                <td class="item-percent">'.$per_retur_penjualan.'%</td>
                <td class="item-italic">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr class="total-line">
            <th class="total-left">TOTAL RETURN PENJUALAN</th>
            <th class="total-right">'.$total_retur_penjualan_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($total_retur_penjualan, $penjualan_bersih).'%</th>
            <th class="total-italic">SALES RETURN TOTAL</th>
            </tr>
            <tr>
            <th class="section-left">POTONGAN PENJUALAN</th>
            <th></th>
            <th></th>
            <th class="section-right">SALES DISCOUNT</th>
            </tr>';

            $total_potongan_penjualan = 0;
            $total_potongan_penjualan_ = 0;
            while($row3 = mysqli_fetch_array($sql3)){
                $potongan_penjualan = isset($row3['total']) ? $row3['total'] : 0;
                $per_potongan_penjualan = fs1YtdSplPercent($potongan_penjualan, $penjualan_bersih);
                if ($potongan_penjualan > 0) {
                    $potongan_penjualan = number_format($potongan_penjualan,2);
                }else{
                    $potongan_penjualan = '('.number_format(abs($potongan_penjualan),2).')';
                }

                $total_potongan_penjualan += isset($row3['total']) ? $row3['total'] : 0;
                if ($total_potongan_penjualan > 0) {
                    $total_potongan_penjualan_ = number_format($total_potongan_penjualan,2);
                }else{
                    $total_potongan_penjualan_ = '('.number_format(abs($total_potongan_penjualan),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row3['sub_kategori'].'</td>
                <td class="item-right">'.$potongan_penjualan.'</td>
                <td class="item-percent">'.$per_potongan_penjualan.'%</td>
                <td class="item-italic">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            if ($penjualan_bersih > 0) {
                $penjualan_bersih_ = number_format($penjualan_bersih,2);
            }else{
                $penjualan_bersih_ = '('.number_format(abs($penjualan_bersih),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">TOTAL POTONGAN PENJUALAN</th>
            <th class="total-right">'.$total_potongan_penjualan_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($total_potongan_penjualan, $penjualan_bersih).'%</th>
            <th class="total-italic">SALES DISCOUNT TOTAL</th>
            </tr>
            <tr class="total-line">
            <th class="total-left">PENJUALAN BERSIH</th>
            <th class="total-right">'.$penjualan_bersih_.'</th>
            <th class="total-percent">100%</th>
            <th class="total-italic">NET SALES</th>
            </tr>

            <tr>
            <th class="section-left">BEBAN POKOK PENJUALAN</th>
            <th></th>
            <th></th>
            <th class="section-right">COST OF GOODS SOLD</th>
            </tr>';

            $total_beban_pokok_penjualan = 0;
            $total_beban_pokok_penjualan_ = 0;
            while($row4 = mysqli_fetch_array($sql4)){
                $beban_pokok_penjualan = isset($row4['total']) ? $row4['total'] : 0;
                $per_beban_pokok_penjualan = fs1YtdSplPercent($beban_pokok_penjualan, $penjualan_bersih);
                if ($beban_pokok_penjualan > 0) {
                    $beban_pokok_penjualan = number_format($beban_pokok_penjualan,2);
                }else{
                    $beban_pokok_penjualan = '('.number_format(abs($beban_pokok_penjualan),2).')';
                }

                $total_beban_pokok_penjualan += isset($row4['total']) ? $row4['total'] : 0;
                if ($total_beban_pokok_penjualan > 0) {
                    $total_beban_pokok_penjualan_ = number_format($total_beban_pokok_penjualan,2);
                }else{
                    $total_beban_pokok_penjualan_ = '('.number_format(abs($total_beban_pokok_penjualan),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row4['sub_kategori'].'</td>
                <td class="item-right">'.$beban_pokok_penjualan.'</td>
                <td class="item-percent">'.$per_beban_pokok_penjualan.'%</td>
                <td class="item-italic">'.$row4['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_kotor = $penjualan_bersih + $total_beban_pokok_penjualan;
            if ($laba_rugi_kotor > 0) {
                $laba_rugi_kotor_ = number_format($laba_rugi_kotor,2);
            }else{
                $laba_rugi_kotor_ = '('.number_format(abs($laba_rugi_kotor),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">HARGA POKOK PENJUALAN</th>
            <th class="total-right">'.$total_beban_pokok_penjualan_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($total_beban_pokok_penjualan, $penjualan_bersih).'%</th>
            <th class="total-italic">COST OF GOODS SOLD</th>
            </tr>
            <tr class="total-line">
            <th class="total-left">LABA RUGI KOTOR</th>
            <th class="total-right">'.$laba_rugi_kotor_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($penjualan_bersih + $total_beban_pokok_penjualan, $penjualan_bersih).'%</th>
            <th class="total-italic">GROSS PROFIT / (LOSS)</th>
            </tr>';

            $total_beban_lainnya = 0;
            $total_beban_lainnya_ = 0;
            while($row5 = mysqli_fetch_array($sql5)){
                $beban_lainnya = isset($row5['total']) ? $row5['total'] : 0;
                $per_beban_lainnya = fs1YtdSplPercent($beban_lainnya, $penjualan_bersih);
                if ($beban_lainnya > 0) {
                    $beban_lainnya = number_format($beban_lainnya,2);
                }else{
                    $beban_lainnya = '('.number_format(abs($beban_lainnya),2).')';
                }

                $total_beban_lainnya += isset($row5['total']) ? $row5['total'] : 0;
                if ($total_beban_lainnya > 0) {
                    $total_beban_lainnya_ = number_format($total_beban_lainnya,2);
                }else{
                    $total_beban_lainnya_ = '('.number_format(abs($total_beban_lainnya),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row5['sub_kategori'].'</td>
                <td class="item-right">'.$beban_lainnya.'</td>
                <td class="item-percent">'.$per_beban_lainnya.'%</td>
                <td class="item-italic">'.$row5['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_sbl_bunga = $total_beban_lainnya + $laba_rugi_kotor;
            if ($laba_rugi_sbl_bunga > 0) {
                $laba_rugi_sbl_bunga_ = number_format($laba_rugi_sbl_bunga,2);
            }else{
                $laba_rugi_sbl_bunga_ = '('.number_format(abs($laba_rugi_sbl_bunga),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</th>
            <th class="total-right">'.$laba_rugi_sbl_bunga_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($laba_rugi_sbl_bunga, $penjualan_bersih).'%</th>
            <th class="total-italic">PROFIT / (LOSS) BEFORE INTEREST AND TAX</th>
            </tr>';

            $total_beban_bunga = 0;
            $total_beban_bunga_ = 0;
            while($row6 = mysqli_fetch_array($sql6)){
                $beban_bunga = isset($row6['total']) ? $row6['total'] : 0;
                $per_beban_bunga = fs1YtdSplPercent($beban_bunga, $penjualan_bersih);
                if ($beban_bunga > 0) {
                    $beban_bunga = number_format($beban_bunga,2);
                }else{
                    $beban_bunga = '('.number_format(abs($beban_bunga),2).')';
                }

                $total_beban_bunga += isset($row6['total']) ? $row6['total'] : 0;
                if ($total_beban_bunga > 0) {
                    $total_beban_bunga_ = number_format($total_beban_bunga,2);
                }else{
                    $total_beban_bunga_ = '('.number_format(abs($total_beban_bunga),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row6['sub_kategori'].'</td>
                <td class="item-right">'.$beban_bunga.'</td>
                <td class="item-percent">'.$per_beban_bunga.'%</td>
                <td class="item-italic">'.$row6['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_sbl_pajak = $laba_rugi_sbl_bunga + $total_beban_bunga;
            if ($laba_rugi_sbl_pajak > 0) {
                $laba_rugi_sbl_pajak_ = number_format($laba_rugi_sbl_pajak,2);
            }else{
                $laba_rugi_sbl_pajak_ = '('.number_format(abs($laba_rugi_sbl_pajak),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">LABA / (RUGI) SEBELUM PAJAK</th>
            <th class="total-right">'.$laba_rugi_sbl_pajak_.'</th>
            <th class="total-percent">'.fs1YtdSplPercent($laba_rugi_sbl_pajak, $penjualan_bersih).'%</th>
            <th class="total-italic">PROFIT / (LOSS) BEFORE TAX</th>
            </tr>';

            $total_beban_pajak = 0;
            $total_beban_pajak_ = 0;
            while($row7 = mysqli_fetch_array($sql7)){
                $beban_pajak = isset($row7['total']) ? $row7['total'] : 0;
                $per_beban_pajak = fs1YtdSplPercent($beban_pajak, $penjualan_bersih);
                if ($beban_pajak > 0) {
                    $beban_pajak = number_format($beban_pajak,2);
                }else{
                    $beban_pajak = '('.number_format(abs($beban_pajak),2).')';
                }

                $total_beban_pajak += isset($row7['total']) ? $row7['total'] : 0;
                if ($total_beban_pajak > 0) {
                    $total_beban_pajak_ = number_format($total_beban_pajak,2);
                }else{
                    $total_beban_pajak_ = '('.number_format(abs($total_beban_pajak),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row7['sub_kategori'].'</td>
                <td class="item-right">'.$beban_pajak.'</td>
                <td class="item-percent">'.$per_beban_pajak.'%</td>
                <td class="item-italic">'.$row7['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_bersih = $laba_rugi_sbl_pajak + $total_beban_pajak;
            if ($laba_rugi_bersih > 0) {
                $laba_rugi_bersih_ = number_format($laba_rugi_bersih,2);
            }else{
                $laba_rugi_bersih_ = '('.number_format(abs($laba_rugi_bersih),2).')';
            }

            echo '<tr class="grand-total">
            <th class="grand-left">LABA / (RUGI) BERSIH</th>
            <th class="grand-right">'.$laba_rugi_bersih_.'</th>
            <th class="grand-percent">'.fs1YtdSplPercent($laba_rugi_bersih, $penjualan_bersih).'%</th>
            <th class="grand-italic">NET INCOME / (LOSS)</th>
            </tr>';


        }
        ?>
        </tbody>
    </table>
      </div>
    </div>
  </div>
</div>
