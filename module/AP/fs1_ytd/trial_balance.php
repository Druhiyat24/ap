    <style>
    /* Header tabel disamakan warna biru dgn tab-tab FS1 lain (SFP/SPL/CF
       Direct/CF Indirect/TB Monthly pakai #2563EB) - sebelumnya class
       Bootstrap "thead-dark" bikin header ini hitam sendirian. Gradient
       (bukan flat #2563EB) & padding lega disamakan dgn
       fs_ytd/trial_balance_ytd.php (FS2 YTD) yg jadi acuan "rapi". */
    #datatable thead tr th {
      background: linear-gradient(180deg, #2f6fea, #2354c9) !important;
      color: #fff !important;
      border-color: #1d4fc4 !important;
    }
    #datatable td, #datatable th {
      padding: 8px 10px !important;
      white-space: nowrap;
    }

    /* Title block + card wrapper - pola sama persis dgn tab SFP/SPL/CF
       Direct/CF Indirect/TB Monthly (lihat catatan lengkap di
       fs1_monthly/trial_balance.php) - dulu halaman ini cuma <h4> polos
       tanpa card, tanpa judul laporan/periode. */
    .tb-outer {
      border: 1px solid #dbe3f0;
      border-radius: 14px;
      background: #fafafa;
      box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
      /* PENTING: overflow:hidden SENGAJA dihapus (dulu ada, cuma buat
         "memotong" sudut kotak title-block/tabel biar ikut membulat
         mengikuti border-radius induk) - overflow:hidden bikin elemen di
         dalamnya jadi punya "scrollport" sendiri (walau tak pernah benar2
         discroll), yang bikin position:sticky pada baris Search DataTables
         di bawah nempel ke scrollport itu (yang diam) alih-alih ke scroll
         HALAMAN sungguhan - jadi sticky-nya seperti tidak berefek/baris
         Search ikut ketutup pas discroll. Sudut dibulatkan manual di
         .sfp-title-block sendiri sbg gantinya (lihat border-radius di
         bawah), bukan lewat clipping induk. */
    }
    .sfp-title-block {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      text-align: left;
      padding: 18px 25px 14px;
      background: #fafafa;
      border-bottom: 1px solid #e5e9f2;
      border-radius: 14px 14px 0 0;
    }
    /* Baris "Show entries / Search" bawaan DataTables (Bootstrap4 dom) -
       dibuat sticky ke atas viewport supaya TETAP kelihatan/bisa dipakai
       walau halaman di-scroll ke bawah utk lihat baris tabel yang lebih
       jauh (dulu ikut ketutup/scroll keluar layar). */
    #datatable_wrapper > .row:first-child {
      position: sticky;
      top: 0;
      z-index: 6;
      background: #fafafa;
      padding-top: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eef1f6;
    }
    /* Header tabel (.tableFix thead) SUDAH sticky top:0px lewat rule
       GLOBAL di module/header.php (dipakai banyak halaman lain, jangan
       diubah di sana) - tapi di FILE INI baris Search di atas JUGA sticky
       top:0 pada scrollport YANG SAMA (.tableFix - ternyata #datatable_wrapper
       ada DI DALAM .tableFix, bukan sebaliknya, krn <table> aslinya
       memang anak langsung .tableFix di HTML sebelum di-wrap DataTables),
       jadi keduanya numpuk di titik yang sama & baris Search (z-index
       lebih tinggi) menutupi header tabel. Header digeser turun sejauh
       tinggi baris Search (~60px) supaya tersusun rapi, bukan tumpang
       tindih - pakai selector #datatable (bukan .tableFix) biar spesifik
       ke tabel ini saja & menang lawan rule global (ID > class). */
    #datatable thead {
      top: 60px;
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
    .tb-table-wrap {
      padding: 0 20px 20px;
    }

    /* CATATAN: sempat dikasih aksen keemasan di kolom paling kanan (Ending
       Balance, pola :last-child yang sama dgn "Total"/"YTD" di tab SFP/SPL/
       CF Direct/CF Indirect) tapi DIBATALKAN per permintaan user - semua
       header & kolom TETAP biru merata (#2563EB), tidak ada warna oranye
       sama sekali, konsisten dgn fs_ytd/trial_balance_ytd.php (FS2 YTD)
       yang juga dibalikin full biru. */
    #datatable thead tr th {
      font-weight: 600;
      letter-spacing: .2px;
    }
    #datatable.table-striped tbody tr:nth-of-type(odd) {
      background-color: #f8fafd;
    }
    #datatable tbody tr:hover td {
      background-color: rgba(37, 99, 235, 0.07) !important;
    }

    .tableFix {
      scrollbar-width: thin;
      scrollbar-color: #b7c3e0 #f1f4fa;
    }
    .tableFix::-webkit-scrollbar {
      height: 10px;
    }
    .tableFix::-webkit-scrollbar-track {
      background: #f1f4fa;
    }
    .tableFix::-webkit-scrollbar-thumb {
      background-color: #b7c3e0;
      border-radius: 8px;
      border: 2px solid #f1f4fa;
    }
    </style>
    <form id="form-data" method="post">
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
      $tbStartLabel = $start_date ? date('M Y', strtotime($start_date)) : date('M Y');
      $tbEndLabel = $end_date ? date('M Y', strtotime($end_date)) : date('M Y');
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
        <?php if (!($tanggal2 < $tanggal1)): ?>
        <a class="sfpm-btn-export-excel" target="_blank" href="ekspor_tb_ytd.php?start_date=<?= $start_date; ?> && end_date=<?= $end_date; ?> && kata_filter=<?= $kata_filter; ?>">
          <svg class="sfpm-btn-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
            <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
            <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
            <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
          Export Excel
        </a>
        <?php endif; ?>
      </div>

      <?php
      // Tombol "Copy Saldo" per-tab yang dulu ada di sini SUDAH DIPINDAH jadi
      // 1 tombol gabungan di toolbar atas financial_statement.php
      // (klik sekali menjalankan proses copy utk FS1 & FS2 sekaligus) - lihat
      // catatan lengkap di sana. Logic populate tbl_saldo_tb_temp di bawah
      // TETAP dipertahankan apa adanya karena tombol gabungan itu bergantung
      // pada efek samping render halaman ini (nge-refresh staging table) SAAT
      // background-fetch dilakukan sebelum proses copy jalan.
      ?>

      <div class="tb-table-wrap">
<div class="tableFix" style="height: 600px;">
    <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%" >
        <thead>
            <tr>
                <th style="display: none;width: 2%;">Remark</th>
                <th style="text-align: center;vertical-align: middle;width: 6%;">No coa</th>
                <th style="text-align: center;vertical-align: middle;width: 12%;">COA Name</th>
                <th style="text-align: center;vertical-align: middle;width: 12%;">Category 1</th>
                <th style="text-align: center;vertical-align: middle;width: 12%;">Category 2</th>
                <th style="text-align: center;vertical-align: middle;width: 13%;">Category 3</th>
                <th style="text-align: center;vertical-align: middle;width: 13%;">Category 4</th>
                <th style="text-align: center;vertical-align: middle;width: 10%;">Beginning Balance</th>
                <th style="text-align: center;vertical-align: middle;width: 7%;">Debit</th>
                <th style="text-align: center;vertical-align: middle;width: 7%;">Credit</th>
                <th style="text-align: center;vertical-align: middle;width: 7%;">Ending Balance</th>
 <!--       <th style="text-align: center;vertical-align: middle;">Reff Date</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">WS</th>
            <th style="text-align: center;vertical-align: middle;">curr</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="display: none;">Remark</th>
            <th style="text-align: center;vertical-align: middle;">Remark</th>  -->                                                       
        </tr>
    </thead>

    <tbody>
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

    // echo  $tanggal_awal;
    // echo  $tanggal_akhir;
    // echo  $tahun_akhir;            
        }
        if(empty($start_date) and empty($end_date)){
           $sql = mysqli_query($conn2,"    
            select * from 
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
            LEFT JOIN
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc");
       }
       else{
        $sql = mysqli_query($conn2,"select * from 
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
            LEFT JOIN
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc");
    }

    $sqldelete = "DELETE from tbl_saldo_tb_temp";
    $execute5 = mysqli_query($conn2, $sqldelete);

    if(!$execute5){ 

    }else{

        $queryss2 = "insert into tbl_saldo_tb_temp select '', nocoa, saldo, debit_idr, credit_idr, ((saldo + debit_idr) - credit_idr) end_balance, '','','' from (select nocoa,COALESCE(saldo,0) saldo,COALESCE(debit_idr,0) debit_idr,COALESCE(credit_idr,0) credit_idr from 
        (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
        left join
        (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4 from mastercoa_v2 order by no_coa asc) coa
        on coa.no_coa = saldo.nocoa
        left join
        (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
        jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a order by a.nocoa asc";

        $executess2 = mysqli_query($conn2, $queryss2);

        if(!$executess2){ 

        }else{
            ini_set('date.timezone', 'Asia/Jakarta');
            $sqlx = mysqli_query($conn1,"SELECT to_saldo FROM tbl_bln_tb where from_saldo = '$kata_filter2'");
            $rowx = mysqli_fetch_array($sqlx);
            $saldo_to = isset($rowx['to_saldo']) ? $rowx['to_saldo'] : null;
            $copy_date = date("Y-m-d H:i:s");

            $sqlupdate = "UPDATE tbl_saldo_tb_temp set copy_user = '$user',copy_date = '$copy_date',to_saldo = '$saldo_to'";

            $execute = mysqli_query($conn2, $sqlupdate);
        }
    }

    echo '<input type="hidden" style="font-size: 12px;" class="form-control" id="to_saldo" name="to_saldo" 
    value="'.$kata_filter2.'">';
    $saldoakhir = 0;

    if($tanggal_akhir < $tanggal_awal){
        $message = "Mohon Masukan Tanggal Filter Yang Benar";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
    else{
        while($row = mysqli_fetch_array($sql)){
            $beg_balance = isset($row['saldo']) ? $row['saldo'] : 0;
            $credit_idr = isset($row['credit_idr']) ? $row['credit_idr'] : 0;
            $debit_idr = isset($row['debit_idr']) ? $row['debit_idr'] : 0;
            $saldoakhir = ($beg_balance + $debit_idr) - $credit_idr;
            $balance_idr = isset($row['balance_idr']) ? $row['balance_idr'] : null;

            if ($balance_idr == 'NB') {
             $warna = '#FF7F50';
         }else{
           $warna = 'grey';
       }
        // if ($reff_date == '0000-00-00' || $reff_date == '1970-01-01' || $reff_date == '') {
        //     $Reffdate = '-'; 
        // }else{
        //     $Reffdate = date("d-M-Y",strtotime($reff_date));
        // }
        //background-color:'.$warna.';

       echo '<tr style="font-size:11px;text-align:center;">
       <td style="width:5px;display: none;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>
       <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
       <td style="text-align : left;" value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
       <td style="text-align : left;" value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
       <td style="text-align : left;" value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
       <td style="text-align : left;" value = "'.$row['indname4'].'">'.$row['indname4'].'</td>
       <td style=" text-align : right;" value="'.$beg_balance.'">'.fsMonthlyFormatNumber($beg_balance).'</td>
       <td style=" text-align : right;" value="'.$debit_idr.'">'.fsMonthlyFormatNumber($debit_idr).'</td>
       <td style=" text-align : right;" value="'.$credit_idr.'">'.fsMonthlyFormatNumber($credit_idr).'</td>
       <td style=" text-align : right;" value="'.$saldoakhir.'">'.fsMonthlyFormatNumber($saldoakhir).'</td>

       ';
       echo '</tr>';
   }
}
?>
</tbody>
</table>
</div>
      </div>
    </div>
</form>
