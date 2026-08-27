<style>
/* Reskin CSS-only, pola sama persis dgn fs1_ytd/cashflow_direct.php (FS1
   YTD CF Direct) - dulu file ini TIDAK punya class sama sekali. Query/
   logic SQL & PHP SAMA SEKALI TIDAK diubah - cuma markup+attribute per
   elemen presentasi (judul, section header, total, tombol Export) yang
   diganti dari inline style ke class. */
#cfi1ytd .card-body {
  background: #f9fafb;
}
#cfi1ytd table {
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
.judul-left { text-align: left; }
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
.desc-left { text-align: left; }
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
}

.section-left,
.section-right {
  font-weight: bold;
  color: #1e3a8a;
  font-size: 13.5px;
  letter-spacing: .2px;
}
.section-left { text-align: left; }
.section-right {
  text-align: right;
  font-style: italic;
  color: #6b7280;
  font-weight: 600;
}

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
#cfi1ytd .laporan-table tr:hover .item-left,
#cfi1ytd .laporan-table tr:hover .item-right {
  background-color: rgba(37, 99, 235, 0.06);
}

.total-line {
  line-height: 28px;
  font-weight: bold;
}
.total-left { text-align: left; }
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

/* ===== Freeze judul (header) FS1 YTD CF Indirect - pola sama dgn FS2. Scoped #cfi1ytd. */
#cfi1ytd .laporan-container {
  max-height: 70vh;
  overflow: auto;
  padding-top: 0;
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
#cfi1ytd .laporan-container::-webkit-scrollbar { height: 10px; width: 10px; }
#cfi1ytd .laporan-container::-webkit-scrollbar-track { background: #f1f4fa; }
#cfi1ytd .laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0; border-radius: 8px; border: 2px solid #f1f4fa;
}
#cfi1ytd .laporan-table { border-collapse: separate; border-spacing: 0; }
#cfi1ytd .laporan-table thead { position: sticky; top: 0; z-index: 5; }
#cfi1ytd .laporan-table thead th { background: #fafafa; }
#cfi1ytd .laporan-table thead tr:last-child th { box-shadow: inset 0 -1px 0 #ccd6ee; }
/* Baris TOTAL diberi latar biru muda #e8edfa spt grand total di SFP/SPL. */
#cfi1ytd .laporan-table .total-line { background: #e8edfa; }
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
    <a target="_blank" class="btn-export excel" href="ekspor_cf_indirect_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'">
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
<div id="cfi1ytd" class="table-responsive mt-1">
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
            <th class="judul-left">LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</th>
            <th></th>
            <th class="judul-right">STATEMENTS OF CASH FLOW - INDIRECT METHOD</th>
        </tr>
        <tr>
            <th class="judul-left">UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th></th>
            <th class="judul-right">FOR THE PERIODS ENDED <?php
            $enddate = date("F Y");
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
            <th class="judul-periode"><?php
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

        <tr>
            <td class="item-left">Laba (Rugi) Bersih</td>
            <td class="item-right">
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

                $sql7 = mysqli_query($conn2,"select indname1,sum(credit_idr - debit_idr) total from (select * from
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
                    LEFT JOIN
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a where a.indname1 = 'LAPORAN LABA RUGI'");

                $row7 = mysqli_fetch_array($sql7);
                $total7 = isset($row7['total']) ? $row7['total'] : 0;
                $tot_jml9 = ($total7);
                if ($tot_jml9 > 0) {
                    $total_ = number_format($tot_jml9,2);
                }else{
                    $total_ = '('.number_format(abs($tot_jml9),2).')';
                }
                echo $total_;
                ?>
            </td>
            <td class="item-italic">Net Income (Loss)</td>
        </tr>
        <tr>
            <td class="item-left">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>
            <td class="item-right">
                <?php
                $sql1 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '19'");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;
                if ($total1 > 0) {
                    $totalcf_1 = number_format($total1,2);
                }else{
                    $totalcf_1 = '('.number_format(abs($total1),2).')';
                }
                echo $totalcf_1;
                ?>
            </td>
            <td class="item-italic">Accumulated Depreciation Of Fixed Asset Adjustment</td>
        </tr>
        <tr>
            <td class="item-left">Penyesuaian Laba Ditahan Tahun Lalu</td>
            <td class="item-right">
                <?php
                $totalcf_laba1 = 0;
                if ($totalcf_laba1 > 0) {
                    $total_laba1 = number_format($totalcf_laba1,2);
                }else{
                    $total_laba1 = '('.number_format(abs($totalcf_laba1),2).')';
                }
                echo $total_laba1;
                ?>
            </td>
            <td class="item-italic">Previous Year Retained Earning Adjustment</td>
        </tr>
        <tr class="spacer">
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <th class="section-left">Arus Kas dari Aktivitas Operasi</th>
            <th></th>
            <th class="section-right">Cash Flow from Operating Activities</th>
        </tr>

        <?php


        $sql = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Operasi_ind')) a left JOIN
         (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
         (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
         (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a GROUP BY a.id_indirect ) b on b.ind_name = a.sub_kategori order by id asc");

        $sql2 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Investasi_ind')) a left JOIN
         (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
         (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
         (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a GROUP BY a.id_indirect ) b on b.ind_name = a.sub_kategori order by id asc");

        $sql3 = mysqli_query($conn2,"select id,sub_kategori,total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Pendanaan_ind')) a left JOIN
         (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
         (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
         (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a GROUP BY a.id_indirect ) b on b.ind_name = a.sub_kategori order by id asc");

        if($tanggal_akhir < $tanggal_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_aktivitas_operasi_ind = 0;
            while($row = mysqli_fetch_array($sql)){
                $aktivitas_operasi_ind = isset($row['total']) ? $row['total'] : 0;
                if ($aktivitas_operasi_ind > 0) {
                    $aktivitas_operasi_ind = number_format($aktivitas_operasi_ind,2);
                }else{
                    $aktivitas_operasi_ind = '('.number_format(abs($aktivitas_operasi_ind),2).')';
                }

                $total_aktivitas_operasi_ind += isset($row['total']) ? $row['total'] : 0;
                if ($total_aktivitas_operasi_ind > 0) {
                    $total_aktivitas_operasi_ind_ = number_format($total_aktivitas_operasi_ind,2);
                }else{
                    $total_aktivitas_operasi_ind_ = '('.number_format(abs($total_aktivitas_operasi_ind),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row['sub_kategori'].'</td>
                <td class="item-right">'.$aktivitas_operasi_ind.'</td>
                <td class="item-italic">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th class="total-right">'.$total_aktivitas_operasi_ind_.'</th>
            <th class="total-italic">Cash flow used from operating activities</th>
            </tr>
            <tr>
            <th class="section-left">Arus Kas dari Aktivitas Investasi</th>
            <th></th>
            <th class="section-right">Cash Flow from Investing Activities</th>
            </tr>';

            $total_aktivitas_investasi_ind = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $aktivitas_investasi_ind = isset($row2['total']) ? $row2['total'] : 0;
                if ($aktivitas_investasi_ind > 0) {
                    $aktivitas_investasi_ind = number_format($aktivitas_investasi_ind,2);
                }else{
                    $aktivitas_investasi_ind = '('.number_format(abs($aktivitas_investasi_ind),2).')';
                }

                $total_aktivitas_investasi_ind += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_aktivitas_investasi_ind > 0) {
                    $total_aktivitas_investasi_ind_ = number_format($total_aktivitas_investasi_ind,2);
                }else{
                    $total_aktivitas_investasi_ind_ = '('.number_format(abs($total_aktivitas_investasi_ind),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row2['sub_kategori'].'</td>
                <td class="item-right">'.$aktivitas_investasi_ind.'</td>
                <td class="item-italic">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang diperoleh dari aktivitas investasi</th>
            <th class="total-right">'.$total_aktivitas_investasi_ind_.'</th>
            <th class="total-italic">Cash flow generated from investing activities</th>
            </tr>
            <tr>
            <th class="section-left">Arus Kas dari Aktivitas Pendanaan</th>
            <th></th>
            <th class="section-right">Cash Flow from Financing Activities</th>
            </tr>';

            $total_aktivitas_Pendanaan_ind = 0;
            while($row3 = mysqli_fetch_array($sql3)){
                $aktivitas_Pendanaan_ind = isset($row3['total']) ? $row3['total'] : 0;
                if ($aktivitas_Pendanaan_ind > 0) {
                    $aktivitas_Pendanaan_ind = number_format($aktivitas_Pendanaan_ind,2);
                }else{
                    $aktivitas_Pendanaan_ind = '('.number_format(abs($aktivitas_Pendanaan_ind),2).')';
                }

                $total_aktivitas_Pendanaan_ind += isset($row3['total']) ? $row3['total'] : 0;
                if ($total_aktivitas_Pendanaan_ind > 0) {
                    $total_aktivitas_Pendanaan_ind_ = number_format($total_aktivitas_Pendanaan_ind,2);
                }else{
                    $total_aktivitas_Pendanaan_ind_ = '('.number_format(abs($total_aktivitas_Pendanaan_ind),2).')';
                }

                echo '<tr>
                <td class="item-left">'.$row3['sub_kategori'].'</td>
                <td class="item-right">'.$aktivitas_Pendanaan_ind.'</td>
                <td class="item-italic">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            $bersih_kas_setarakas_ind = $total_aktivitas_operasi_ind + $total_aktivitas_investasi_ind + $total_aktivitas_Pendanaan_ind + $tot_jml9 + $total1;
            if ($bersih_kas_setarakas_ind > 0) {
                $bersih_kas_setarakas_ind_ = number_format($bersih_kas_setarakas_ind,2);
            }else{
                $bersih_kas_setarakas_ind_ = '('.number_format(abs($bersih_kas_setarakas_ind),2).')';
            }

            echo '<tr class="total-line">
            <th class="total-left">Arus kas yang diperoleh dari aktivitas pendanaan</th>
            <th class="total-right">'.$total_aktivitas_Pendanaan_ind_.'</th>
            <th class="total-italic">Cash flow generated from financing activities</th>
            </tr>
            <tr class="total-line">
            <th class="total-left">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th class="total-right">'.$bersih_kas_setarakas_ind_.'</th>
            <th class="total-italic">Net Increase / (Decrease) in cash and cash equivalent</th>
            </tr>';
            ?>
            <tr>
                <th class="item-left">Kas dan setara kas pada awal periode</th>
                <th class="item-right">
                    <?php
                    $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
                        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
                        left join
                        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                        on coa.no_coa = saldo.nocoa
                        left join
                        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
                        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

                    $row = mysqli_fetch_array($sql);
                    $totalind = isset($row['total']) ? $row['total'] : 0;
                    if ($totalind > 0) {
                        $total_ = number_format($totalind,2);
                    }else{
                        $total_ = '('.number_format(abs($totalind),2).')';
                    }
                    echo $total_;
                    ?>
                </th>
                <th class="item-italic">Cash and cash equivalent at the beginning of period</th>
            </tr>
            <tr class="grand-total">
                <th class="grand-left">Kas dan setara kas pada akhir periode</th>
                <th class="grand-right">
                    <?php
                    $totalcf_kas = $totalind + $bersih_kas_setarakas_ind;
                    if ($kata_filter == 'Jan_2023') {
                        $total_jmlkas = ($totalcf_kas - 49264896939.97);
                    }else{
                        $total_jmlkas = $totalcf_kas;
                    }
                    if ($total_jmlkas > 0) {
                        $total_jmlkas = number_format($total_jmlkas,2);
                    }else{
                        $total_jmlkas = '('.number_format(abs($total_jmlkas),2).')';
                    }
                // $total_jmlkas = number_format($totalcf_kas,2);
                    echo $total_jmlkas;
                    ?>
                </th>
                <th class="grand-italic">Cash and cash equivalent at the end of period</th>
            </tr>
            <?php

        }
        ?>
        </tbody>
    </table>
      </div>
    </div>
  </div>
</div>
