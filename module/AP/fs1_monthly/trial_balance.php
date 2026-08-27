<style>
  #myInput {
    border-radius: 6px;
    font-size: 13px;
    width: 200px;
  }

.dataTables_wrapper .dataTables_scrollHeadInner {
  width: auto !important;
}

.dataTables_wrapper .dataTables_scrollBody {
  width: auto !important;
  overflow-x: auto;
}

table.dataTable {
  width: 100%;
  min-width: max-content;
}

/* Kartu bulat/shadow + freeze 6 kolom pertama - pola & alasan identik
   dengan fs_ytd/trial_balance_ytd.php (lihat catatan lengkap di situ),
   cuma ID tabel dibedakan (table_tbfs1monthly) karena tabel ini adalah
   Trial Balance punya FS1 (data dari saldo_awal_tb, TANPA breakdown
   profit center NAG/NAK - beda mesin dari FS2). */
.tb-outer {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  overflow: hidden;
}

/* Title block - SAMA PERSIS pola & class-nya dgn tab SFP/SPL/CF Direct/CF
   Indirect (lihat cashflow_direct.php dkk. utk catatan lengkap) - dulu
   Trial Balance TIDAK punya blok judul ini sama sekali, cuma tombol Export
   polos di pojok, makanya kelihatan "biasa" dibanding tab lain. */
.sfp-title-block {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  padding: 18px 25px 14px;
  background: #fafafa;
  border-bottom: 1px solid #e5e9f2;
}

.sfp-title-company {
  font-weight: 700;
  font-size: 18px;
  color: #1e3a8a;
  letter-spacing: .3px;
}

.sfp-title-report {
  font-weight: 700;
  font-size: 16px;
  color: #2c3e50;
  margin-top: 4px;
}

.sfp-title-report-en {
  font-style: italic;
  font-size: 14.5px;
  color: #6b7280;
}

.sfp-title-period {
  font-weight: 600;
  font-size: 14.5px;
  color: #2c3e50;
  margin-top: 6px;
}

.sfp-title-desc {
  font-size: 13px;
  color: #777;
  margin-top: 3px;
}

.sfp-title-desc-en {
  font-style: italic;
  font-size: 12.5px;
  color: #999;
}

.sfpm-btn-export-excel {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  flex-shrink: 0;
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: .2px;
  color: #fff;
  background: linear-gradient(135deg, #1f7a4d, #14532d);
  border: none;
  border-radius: 999px;
  padding: 8px 18px 8px 14px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(20, 83, 45, 0.28);
  transition: box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
  text-decoration: none;
}
.sfpm-btn-export-excel .sfpm-btn-icon {
  display: inline-flex;
  width: 16px;
  height: 16px;
}
.sfpm-btn-export-excel:hover {
  background: linear-gradient(135deg, #23935d, #185c36);
  box-shadow: 0 4px 10px rgba(20, 83, 45, 0.35);
  transform: translateY(-1px);
  color: #fff;
}
.sfpm-btn-export-excel:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(20, 83, 45, 0.3);
}

.tb-table-wrap {
  padding: 15px 20px 20px;
}

#table_tbfs1monthly td, #table_tbfs1monthly th {
  padding: 8px 14px !important;
}
#table_tbfs1monthly tbody td:nth-child(-n+6),
#table_tbfs1monthly thead tr:first-child th:nth-child(-n+6) {
  position: sticky;
  z-index: 2;
  background-color: #fff;
}
#table_tbfs1monthly thead tr:first-child th:nth-child(-n+6) {
  background-color: #2563EB;
  color: #fff;
  z-index: 3;
}
#table_tbfs1monthly tbody td:nth-child(6),
#table_tbfs1monthly thead tr:first-child th:nth-child(6) {
  box-shadow: 2px 0 3px -1px rgba(0,0,0,0.25);
}
#table_tbfs1monthly tbody tr:hover td:nth-child(-n+6) {
  background-color: #eaf2ff;
}
#table_tbfs1monthly thead th {
  background: linear-gradient(180deg, #2f6fea, #2354c9);
  color: #fff;
  font-weight: 600;
  letter-spacing: .2px;
  border-color: #1d4fc4;
}

/* Kolom paling kanan (YTD) ditonjolkan - konsisten dgn kolom "Total"/"YTD"
   di tab SFP/SPL/CF Direct/CF Indirect (aksen keemasan). Selalu kolom
   TERAKHIR di tiap baris apapun jumlah bulan yg tampil, jadi aman pakai
   :last-child murni CSS tanpa perlu ubah logic render per sel. */
#table_tbfs1monthly td:last-child,
#table_tbfs1monthly th:last-child {
  background-color: #fdf6e6 !important;
  border-left: 2px solid #c9962f;
  font-weight: 600;
}
#table_tbfs1monthly thead tr.thead-dark th:last-child {
  background: linear-gradient(180deg, #d9a53a, #b9822a) !important;
  border-left-color: #b9822a;
}

/* Zebra & hover lebih lembut, selaras palet biru-abu tab lain (bukan abu
   Bootstrap default). */
#table_tbfs1monthly.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f8fafd;
}
#table_tbfs1monthly tbody tr:hover td {
  background-color: rgba(37, 99, 235, 0.07);
}

.tb-table-wrap {
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
.tb-table-wrap::-webkit-scrollbar {
  height: 10px;
}
.tb-table-wrap::-webkit-scrollbar-track {
  background: #f1f4fa;
}
.tb-table-wrap::-webkit-scrollbar-thumb {
  background-color: #b7c3e0;
  border-radius: 8px;
  border: 2px solid #f1f4fa;
}
</style>

<div class="tb-outer mt-2">
<?php
// Format angka - selaras dgn tab SFP/SPL/CF Direct/CF Indirect/TB
// Monthly (FS2): negatif ditulis dlm kurung, bukan minus di depan.
function fsMonthlyFormatNumber($val) {
    $val = (float) $val;
    if ($val < 0) {
        return '(' . number_format(abs($val), 2) . ')';
    }
    return number_format($val, 2);
}

$nama_type ='';
    $Status = '';
    $start_date ='';
    $end_date ='';
    $t_width = 0;
    $date_now = date("Y-m-d");
    $bulan_awal = date("m",strtotime($date_now));
    $bulan_akhir = date("m",strtotime($date_now));
    $tahun_awal = date("Y",strtotime($date_now));
    $tahun_akhir = date("Y",strtotime($date_now));
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null;
    $Status = isset($_POST['Status']) ? $_POST['Status']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
    $start_date_ = isset($start_date) ? $start_date : null;
    $end_date_ = isset($end_date) ? $end_date : null;
    $bulan_awal = date("m",strtotime($start_date_));
    $bulan_akhir = date("m",strtotime($end_date_));
    $tahun_awal = date("Y",strtotime($start_date_));
    $tahun_akhir = date("Y",strtotime($end_date_));
}

$sql_periode = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");

$sql_t_p = mysqli_query($conn1,"select COUNT(periode) jml_period from (select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a) a");
$row_t_p = mysqli_fetch_array($sql_t_p);
$ttl_periode = isset($row_t_p['jml_period']) ? $row_t_p['jml_period'] : 1;
$t_width = floor(100 / ($ttl_periode + 8)) . '%';
// echo $t_width;

$tbStartLabel = date('M Y', mktime(0, 0, 0, (int) $bulan_awal, 1, (int) $tahun_awal));
$tbEndLabel = date('M Y', mktime(0, 0, 0, (int) $bulan_akhir, 1, (int) $tahun_akhir));
?>
<div class="sfp-title-block">
  <div class="sfp-title-text">
    <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
    <div class="sfp-title-report">NERACA SALDO</div>
    <div class="sfp-title-report-en">Trial Balance</div>
    <div class="sfp-title-period"><?= htmlspecialchars($tbStartLabel); ?> - <?= htmlspecialchars($tbEndLabel); ?></div>
    <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
    <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
  </div>
  <a class="sfpm-btn-export-excel" target="_blank" href="ekspor_tb_monthly.php?start_date=<?= $start_date; ?> && end_date=<?= $end_date; ?>">
    <svg class="sfpm-btn-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
      <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
      <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
      <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
    Export Excel
  </a>
</div>

<div class="tb-table-wrap">
<table id="table_tbfs1monthly" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%" >
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No coa</th>
            <th style="text-align: center;vertical-align: middle;">COA Name</th>
            <th style="text-align: center;vertical-align: middle;">Category 1</th>
            <th style="text-align: center;vertical-align: middle;">Category 2</th>
            <th style="text-align: center;vertical-align: middle;">Category 3</th>
            <th style="text-align: center;vertical-align: middle;">Category 4</th>
            <th style="text-align: center;vertical-align: middle;">Des <?= $tahun_akhir -1 ?></th>
            <?php
             while($periode = mysqli_fetch_array($sql_periode)){
            echo '<th style="text-align: center;vertical-align: middle;">'.$periode['periode'].'</th>';
        };
            ?>
            <th style="text-align: center;vertical-align: middle;">YTD</th>                                                    
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_type ='';
    $Status = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");   
    $bulan_awal = date("m",strtotime($date_now));
    $bulan_akhir = date("m",strtotime($date_now));  
    $tahun_awal = date("Y",strtotime($date_now));
    $tahun_akhir = date("Y",strtotime($date_now)); 
    $tanggal_awal = date("Y-m-d",strtotime($date_now));
    $tanggal_akhir = date("Y-m-d",strtotime($date_now));            
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null; 
    $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
    $start_date_ = isset($start_date) ? $start_date : null;
    $end_date_ = isset($end_date) ? $end_date : null;   
    $bulan_awal = date("m",strtotime($start_date_));
    $bulan_akhir = date("m",strtotime($end_date_));  
    $tahun_awal = date("Y",strtotime($start_date_));
    $tahun_akhir = date("Y",strtotime($end_date_));

    $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
    $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date']));
    // echo  $start_date;
    // echo  $end_date;
    // echo  $tahun_awal;
    // echo  $tahun_akhir;            
    }
    
    $sql = mysqli_query($conn2,"select no_coa,nama_coa,indname1,indname2,indname3,indname4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
left join
(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
LEFT JOIN
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc");


    $sql_jmlper = mysqli_query($conn2,"select COUNT(periode) jml_periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");

    $rowjmlper = mysqli_fetch_array($sql_jmlper);
    $jmlper = isset($rowjmlper['jml_periode']) ? $rowjmlper['jml_periode'] : 0;

        $saldoakhir = 0;
        if($tanggal_akhir < $tanggal_awal){
        $message = "Mohon Masukan Tanggal Filter Yang Benar";
    echo "<script type='text/javascript'>alert('$message');</script>";
    }
        else{
    while($row = mysqli_fetch_array($sql)){
        $coa_number = $row['no_coa'];
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;

        // $saldoakhir = ($beg_balance + $debit_idr) - $credit_idr;
        // $balance_idr = isset($row['saldo_nov']) ? $row['saldo_dec'] : null;

        // if ($balance_idr == 'NB') {
        //    $warna = '#FF7F50';
        // }else{
        //      $warna = 'grey';
        // }
        // if ($reff_date == '0000-00-00' || $reff_date == '1970-01-01' || $reff_date == '') {
        //     $Reffdate = '-'; 
        // }else{
        //     $Reffdate = date("d-M-Y",strtotime($reff_date));
        // }
        //background-color:'.$warna.';
                   
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
            <td style="text-align : left;" value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
            <td style="text-align : left;" value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
            <td style="text-align : left;" value = "'.$row['indname4'].'">'.$row['indname4'].'</td>
            <td style=" text-align : right;" value="'.$saldo_awal.'">'.fsMonthlyFormatNumber($saldo_awal).'</td>
            ';
            if($coa_number >= 4){

                if ($jmlper == '1') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                ';
            }elseif ($jmlper == '2') {
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.fsMonthlyFormatNumber($saldo_nov).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.fsMonthlyFormatNumber($saldo_nov).'</td>
                <td style=" text-align : right;" value="'.$saldo_dec.'">'.fsMonthlyFormatNumber($saldo_dec).'</td>
                <td style=" text-align : right;" value="'.$jumlah_ytd.'">'.fsMonthlyFormatNumber($jumlah_ytd).'</td>
                ';
            }else{

            }

            }else{
            if ($jmlper == '1') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb_.'">'.fsMonthlyFormatNumber($saldo_feb_).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar_.'">'.fsMonthlyFormatNumber($saldo_mar_).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr_.'">'.fsMonthlyFormatNumber($saldo_apr_).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_may_.'">'.fsMonthlyFormatNumber($saldo_may_).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun_.'">'.fsMonthlyFormatNumber($saldo_jun_).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul_.'">'.fsMonthlyFormatNumber($saldo_jul_).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug_.'">'.fsMonthlyFormatNumber($saldo_aug_).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep_.'">'.fsMonthlyFormatNumber($saldo_sep_).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct_.'">'.fsMonthlyFormatNumber($saldo_oct_).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.fsMonthlyFormatNumber($saldo_nov).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov_.'">'.fsMonthlyFormatNumber($saldo_nov_).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.fsMonthlyFormatNumber($saldo_jan).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.fsMonthlyFormatNumber($saldo_feb).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.fsMonthlyFormatNumber($saldo_mar).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.fsMonthlyFormatNumber($saldo_apr).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.fsMonthlyFormatNumber($saldo_may).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.fsMonthlyFormatNumber($saldo_jun).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.fsMonthlyFormatNumber($saldo_jul).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.fsMonthlyFormatNumber($saldo_aug).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.fsMonthlyFormatNumber($saldo_sep).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.fsMonthlyFormatNumber($saldo_oct).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.fsMonthlyFormatNumber($saldo_nov).'</td>
                <td style=" text-align : right;" value="'.$saldo_dec.'">'.fsMonthlyFormatNumber($saldo_dec).'</td>
                <td style=" text-align : right;" value="'.$saldo_dec_.'">'.fsMonthlyFormatNumber($saldo_dec_).'</td>
                ';
            }else{

            }
        }
            echo '</tr>';
}
}?>
</tbody>
</table>
</div>
</div>
