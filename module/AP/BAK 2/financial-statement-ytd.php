<?php include '../header.php' ?>
<style >
    .modal {
      text-align: center;
      padding: 0!important;
  }

  .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
  }

  .modal-dialog {
      display: inline-table;
      width: 700px;
      text-align: left;
      vertical-align: middle;
  }


/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

/* Style the close button */
.topright {
  float: right;
  cursor: pointer;
  font-size: 28px;
}

.topright:hover {color: red;}
.horizontal{

    height:0;
    width:64%;
    margin: auto;
    border:1px solid #000000;

}
.horizontal_fiskal{

    height:0;
    width:94%;
    margin: auto;
    border:1px solid #000000;

}

.horizontal3{

    height:0;
    width:84%;
    margin: auto;
    border:1px solid #000000;

}

.horizontal4{

    height:0;
    width:67%;
    margin: auto;
    border:1px solid #000000;

}
</style>
<!-- MAIN -->
<div class="col p-4">
    <h3 class="text-center">FINANCIAL STATEMENT YEAR TO DATE</h3>
    <div class="box">
        <div class="box header">

            <form id="form" action="financial-statement-ytd.php" method="post">

                <div class="form-row">

                    <div class="col-md-2 mb-3 mt-1"> 
                        <label for="start_date">From</label>          
                        <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
                        value="<?php
                        $start_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                           $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                       }
                       if(!empty($_POST['start_date'])) {
                           echo $_POST['start_date'];
                       }
                       else{
                           echo date("M Y");
                       } ?>" 
                       placeholder="Tanggal Awal">
                   </div>

                   <div class="col-md-2 mb-3 mt-1"> 
                    <label for="end_date">To</label>          
                    <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
                    value="<?php
                    $end_date ='';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                       $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                   }
                   if(!empty($_POST['end_date'])) {
                       echo $_POST['end_date'];
                   }
                   else{
                       echo date("M Y");
                   } ?>" 
                   placeholder="Tanggal Awal">
               </div>
               <div class="input-group-append col">                                   
                <button  type="submit" id="submit" value=" Search " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
                line-height: 1;
                padding: -2px 8px;
                font-size: 1rem;
                text-align: center;
                color: #fff;
                text-shadow: 1px 1px 1px #000;
                border-radius: 6px;
                background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>



            </div>                                                            
        </div>
        <br/>
    </div>
</form> 

<div class="tab" style="height: 35px;font-size: 12px;">
  <button style="margin-top: -5px;" class="tablinks btn btn-outline-dark" onclick="openCity(event, 'tril-balance')" id="defaultOpen">Trial Balance</button>
  <button style="margin-top: -5px;" class="tablinks btn btn-outline-dark" onclick="openCity(event, 'sfp')">SFP</button>
  <button style="margin-top: -5px;" class="tablinks btn btn-outline-dark" onclick="openCity(event, 'spl')">SPL</button>
  <button style="margin-top: -5px;" class="tablinks btn btn-outline-dark" onclick="openCity(event, 'cf-direct')">CF Direct</button>
  <button style="margin-top: -5px;" class="tablinks btn btn-outline-dark" onclick="openCity(event, 'cf-indirect')">CF Indirect</button>
</div>

<div id="tril-balance" class="tabcontent">
    <form id="form-data" method="post">
      <h4 class="text-center">TRIAL BALANCE</h4>
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

        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_tb_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " ><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 14px;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
    }
    ?> 

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Acct - Copy saldo TB'");
    $rs = mysqli_fetch_array($querys);
    $id = isset($rs['id']) ? $rs['id'] : 0;

    if($id == '56'){
        echo '<a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= ""><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>';
    }else{
        echo '';
    }
    ?> 

    <input type="text" class="col-md-2 form-control float-right" id="myInput" onkeyup="myFunction()" placeholder="Search no coa..">
</br>
</br>



<div class="tableFix" style="height: 400px;">        
    <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%" >
        <thead>
            <tr class="thead-dark"> 
                <th style="display: none;width: 2%;">Remark</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 6%;">No coa</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">COA Name</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Category 1</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Category 2</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 13%;">Category 3</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 13%;">Category 4</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 10%;">Beginning Balance</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 7%;">Debit</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 7%;">Credit</th>
                <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 7%;">Ending Balance</th>
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
       <td style=" text-align : right;" value="'.$beg_balance.'">'.number_format($beg_balance,2).'</td>
       <td style=" text-align : right;" value="'.$debit_idr.'">'.number_format($debit_idr,2).'</td>
       <td style=" text-align : right;" value="'.$credit_idr.'">'.number_format($credit_idr,2).'</td>
       <td style=" text-align : right;" value="'.$saldoakhir.'">'.number_format($saldoakhir,2).'</td>

       ';
       echo '</tr>';
   }
}
?>
</tbody>                    
</table>
</div>
</form>
</div>


<div id="sfp" class="tabcontent">
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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " ><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 14px;color: #fff;text-shadow: 1px 1px 1px #000"> Excel </i></button></a>

    ';
}
?>
</br>
<div style="border:1px solid black;margin-top: 5px;">  
    <table style="font-size: 16px; margin:auto" border="0" role="grid" cellspacing="0" width="75%">
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">PT NIRWANA ALABARE GARMENT</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>PT NIRWANA ALABARE GARMENT</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LAPORAN POSISI KEUANGAN</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>STATEMENTS OF FINANCIAL POSITION</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i><?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></i></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>(Expressed in Rupiah, unless otherwise stated)</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"></th>
            <th style="text-align: center;vertical-align: middle;width: 16%;border-bottom: 3px solid black;">YTD <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i></i></th>
        </tr>
        <!-- aset - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">ASET</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>ASSETS</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i></i></th>
        </tr>
        <!-- aset_tetap - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">ASET LANCAR</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>CURRENT ASSETS</i></th>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aset_lancar.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Jumlah Aset Lancar</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aset_lancar_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Total Current Asset</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">ASET TIDAK LANCAR</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"></td>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aset_tidak_lancar.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            $total_asset = $total_aset_lancar + $total_aset_tidak_lancar;
            if ($total_asset > 0) {
                $total_asset_ = number_format($total_asset,2);
            }else{
                $total_asset_ = '('.number_format(abs($total_asset),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Jumlah Aset Tidak Lancar</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aset_tidak_lancar_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Total Nonurrent Asset</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">JUMLAH ASET</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_asset_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">TOTAL ASSET</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">LIABILITAS DAN EKUITAS</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">LIABILITIES AND EQUITY</th>
            </tr>

            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LIABILITAS JANGKA PENDEK</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">CURRENT LIABILITIES</th>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row3['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$liabilitas_jangka_pendek.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Jumlah Liabilitas jangka Pendek</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_liabilitas_jangka_pendek_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Total Current Liabilities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LIABILITAS JANGKA PANJANG</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <tH style="text-align: right;vertical-align: middle;width: 27%;">NONCURRENT LIABILITIES</tH>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row4['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$liabilitas_jangka_panjang.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row4['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Jumlah Liabilitas jangka Panjang</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_liabilitas_jangka_panjang_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Total Noncurrent Liabilities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">EKUITAS</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <tH style="text-align: right;vertical-align: middle;width: 27%;">EQUITY</tH>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row5['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$ekuitas.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row5['sub_kategori_eng'].'</td>
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
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba Tahun Berjalan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$laba_rugi_berjalan.'</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">Profit of the year</td>
            </tr>';
            

            $total_liabilitas_ekuitas = $total_liabilitas_jangka_pendek + $total_liabilitas_jangka_panjang + $total_ekuitas;
            if ($total_liabilitas_ekuitas > 0) {
                $total_liabilitas_ekuitas_ = number_format($total_liabilitas_ekuitas,2);
            }else{
                $total_liabilitas_ekuitas_ = '('.number_format(abs($total_liabilitas_ekuitas),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Jumlah Ekuitas</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_ekuitas_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Total Equity</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">JUMLAH LIABILITAS DAN EKUITAS</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_liabilitas_ekuitas_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">TOTAL LIABILITIES AND EQUITY</th> 
            </tr>';

            
        }
        ?>
    </table>
    <br>      
</div>
</div>



<div id="spl" class="tabcontent">
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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " ><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 14px;color: #fff;text-shadow: 1px 1px 1px #000"> Excel </i></button></a>

    ';
}
?>
</br>
<div style="border:1px solid black;margin-top: 5px;">  
    <table style="font-size: 16px; margin:auto" border="0" role="grid" cellspacing="0" width="90%">
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">PT NIRWANA ALABARE GARMENT</th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>PT NIRWANA ALABARE GARMENT</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LAPORAN LABA ATAU RUGI  DAN PENGHASILAN KOMPREHENSIF LAINNYA</th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>STATEMENTS OF PROFIT OR LOSS AND OTHER COMPREHENSIVE INCOME</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">UNTUK TAHUN YANG BERAKHIR PADA TANGGAL <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>FOR THE YEARS ENDED <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></i></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>(Expressed in Rupiah, unless otherwise stated)</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"></th>
            <th style="text-align: center;vertical-align: middle;width: 14%;">YTD <?php
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: center;vertical-align: middle;width: 7%;">Persentage</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i></i></th>
        </tr>
        <!-- penjualan-kotor - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">PENJUALAN KOTOR</th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>GROSS SALES</i></th>
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
                $per_penjualan_kotor = number_format(($penjualan_kotor / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$penjualan_kotor.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_penjualan_kotor.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL PENJUALAN KOTOR</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_penjualan_kotor_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_penjualan_kotor / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">GROSS SALES TOTAL</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">RETURN PENJUALAN</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">SALES RETURN</th>
            </tr>';

            $total_retur_penjualan = 0;
            $total_retur_penjualan_ = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $retur_penjualan = isset($row2['total']) ? $row2['total'] : 0;
                $per_retur_penjualan = number_format(($retur_penjualan / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$retur_penjualan.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_retur_penjualan.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL RETURN PENJUALAN</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_retur_penjualan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_retur_penjualan / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">SALES RETURN TOTAL</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">POTONGAN PENJUALAN</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">SALES DISCOUNT</th>
            </tr>';

            $total_potongan_penjualan = 0;
            $total_potongan_penjualan_ = 0;
            while($row3 = mysqli_fetch_array($sql3)){
                $potongan_penjualan = isset($row3['total']) ? $row3['total'] : 0;
                $per_potongan_penjualan = number_format(($potongan_penjualan / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row3['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$potongan_penjualan.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_potongan_penjualan.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            if ($penjualan_bersih > 0) {
                $penjualan_bersih_ = number_format($penjualan_bersih,2);
            }else{
                $penjualan_bersih_ = '('.number_format(abs($penjualan_bersih),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL POTONGAN PENJUALAN</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_potongan_penjualan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_potongan_penjualan / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">SALES DISCOUNT TOTAL</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">PENJUALAN BERSIH</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;"">'.$penjualan_bersih_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;"">100%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">NET SALES</th>
            </tr>

            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">BEBAN POKOK PENJUALAN</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">COST OF GOODS SOLD</th>
            </tr>';

            $total_beban_pokok_penjualan = 0;
            $total_beban_pokok_penjualan_ = 0;
            while($row4 = mysqli_fetch_array($sql4)){
                $beban_pokok_penjualan = isset($row4['total']) ? $row4['total'] : 0;
                $per_beban_pokok_penjualan = number_format(($beban_pokok_penjualan / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row4['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_pokok_penjualan.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_pokok_penjualan.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row4['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_kotor = $penjualan_bersih + $total_beban_pokok_penjualan;
            if ($laba_rugi_kotor > 0) {
                $laba_rugi_kotor_ = number_format($laba_rugi_kotor,2);
            }else{
                $laba_rugi_kotor_ = '('.number_format(abs($laba_rugi_kotor),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">HARGA POKOK PENJUALAN</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_beban_pokok_penjualan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_beban_pokok_penjualan / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">COST OF GOODS SOLD</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">LABA RUGI KOTOR</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;"">'.$laba_rugi_kotor_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;"">'.number_format((( $penjualan_bersih + $total_beban_pokok_penjualan) / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">GROSS PROFIT / (LOSS)</th>
            </tr>';

            $total_beban_lainnya = 0;
            $total_beban_lainnya_ = 0;
            while($row5 = mysqli_fetch_array($sql5)){
                $beban_lainnya = isset($row5['total']) ? $row5['total'] : 0;
                $per_beban_lainnya = number_format(($beban_lainnya / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row5['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_lainnya.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_lainnya.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row5['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_sbl_bunga = $total_beban_lainnya + $laba_rugi_kotor;
            if ($laba_rugi_sbl_bunga > 0) {
                $laba_rugi_sbl_bunga_ = number_format($laba_rugi_sbl_bunga,2);
            }else{
                $laba_rugi_sbl_bunga_ = '('.number_format(abs($laba_rugi_sbl_bunga),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_sbl_bunga_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_sbl_bunga / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">PROFIT / (LOSS) BEFORE INTEREST AND TAX</th> 
            </tr>';

            $total_beban_bunga = 0;
            $total_beban_bunga_ = 0;
            while($row6 = mysqli_fetch_array($sql6)){
                $beban_bunga = isset($row6['total']) ? $row6['total'] : 0;
                $per_beban_bunga = number_format(($beban_bunga / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row6['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_bunga.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_bunga.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row6['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_sbl_pajak = $laba_rugi_sbl_bunga + $total_beban_bunga;
            if ($laba_rugi_sbl_pajak > 0) {
                $laba_rugi_sbl_pajak_ = number_format($laba_rugi_sbl_pajak,2);
            }else{
                $laba_rugi_sbl_pajak_ = '('.number_format(abs($laba_rugi_sbl_pajak),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) SEBELUM PAJAK</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_sbl_pajak_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_sbl_pajak / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">PROFIT / (LOSS) BEFORE TAX</th> 
            </tr>';

            $total_beban_pajak = 0;
            $total_beban_pajak_ = 0;
            while($row7 = mysqli_fetch_array($sql7)){
                $beban_pajak = isset($row7['total']) ? $row7['total'] : 0;
                $per_beban_pajak = number_format(($beban_pajak / $penjualan_bersih * 100),2);
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row7['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_pajak.'</td>
                <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_pajak.'%</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row7['sub_kategori_eng'].'</td>
                </tr>';
            }

            $laba_rugi_bersih = $laba_rugi_sbl_pajak + $total_beban_pajak;
            if ($laba_rugi_bersih > 0) {
                $laba_rugi_bersih_ = number_format($laba_rugi_bersih,2);
            }else{
                $laba_rugi_bersih_ = '('.number_format(abs($laba_rugi_bersih),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) BERSIH</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_bersih_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_bersih / $penjualan_bersih * 100),2).'%</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">NET INCOME / (LOSS)X</th> 
            </tr>';

            
        }
        ?>
    </table>
    <br>      
</div>
</div>


<div id="cf-direct" class="tabcontent">
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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_cf_direct_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " ><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 14px;color: #fff;text-shadow: 1px 1px 1px #000"> Excel </i></button></a>

    ';
}
?>
</br>
<div style="border:1px solid black;margin-top: 5px;">  
    <table style="font-size: 16px; margin:auto" border="0" role="grid" cellspacing="0" width="85%">
        <!-- aset - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">PT NIRWANA ALABARE GARMENT</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>PT NIRWANA ALABARE GARMENT</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LAPORAN ARUS KAS - METODE LANGSUNG</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>STATEMENTS OF CASH FLOW - DIRECT METHOD</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>FOR THE PERIODS ENDED <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></i></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>(Expressed in Rupiah, unless otherwise stated)</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"><?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?>.</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i></i></th>
        </tr>
        <!-- Aktivitas Operasi -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash Flow from Operating Activities</i></th>
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

        $sql = mysqli_query($conn2,"select id,sub_kategori,- total total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Operasi')) a left JOIN
           (select kategori,round(sum(debit) - sum(credit),2) total from (select ind_name kategori,debit,0 credit from(select id_direct_debit,ind_name, sum(debit_idr) debit from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
           when no_journal like '%L/NAG%' then 'AR'
           when no_journal like '%E/NAG%' then 'AR'
           when no_journal like '%/INM/%' then 'AR'
           when no_journal like '%/IN/%' then 'AP'
           when no_journal like '%/RI/%' then 'AP'
           when no_journal like '%/RO/%' then 'AP'
           when no_journal like '%/OUT/%' then 'AP'
           when no_journal like '%SI/APR/%' then 'AP'
           when no_journal like '%BM/%' then 'Bank'
           when no_journal like '%BK/%' then 'Bank'
           when no_journal like '%RCO/%' then 'Cash'
           when no_journal like '%RCI/%' then 'Cash'
           when no_journal like '%KKK/%' then 'Petty Cash'
           when no_journal like '%KKM/%' then 'Petty Cash'
           when no_journal like '%GM/%' then 'GM'
           end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
           (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
           (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a GROUP BY id_direct_debit
           UNION                                   
           select ind_name kategori, 0 debit,credit from(select id_direct_credit,ind_name, sum(credit_idr) credit from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
           when no_journal like '%L/NAG%' then 'AR'
           when no_journal like '%E/NAG%' then 'AR'
           when no_journal like '%/INM/%' then 'AR'
           when no_journal like '%/IN/%' then 'AP'
           when no_journal like '%/RI/%' then 'AP'
           when no_journal like '%/RO/%' then 'AP'
           when no_journal like '%/OUT/%' then 'AP'
           when no_journal like '%SI/APR/%' then 'AP'
           when no_journal like '%BM/%' then 'Bank'
           when no_journal like '%BK/%' then 'Bank'
           when no_journal like '%RCO/%' then 'Cash'
           when no_journal like '%RCI/%' then 'Cash'
           when no_journal like '%KKK/%' then 'Petty Cash'
           when no_journal like '%KKM/%' then 'Petty Cash'
           when no_journal like '%GM/%' then 'GM'
           end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
           (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
           (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a GROUP BY id_direct_credit) a GROUP BY kategori) b on b.kategori = a.sub_kategori order by id asc");

        $sql_pemasok = mysqli_query($conn2,"select case when saldo_akhir_ori >= 0 and credit_idr > 0 then (total * -1)
when saldo_akhir_ori >= 0 and debit_idr > 0 then total
else 0
end pemb_pem_lain from (select '1.10.02' no_coa, ROUND(saldo_akhir,2) saldo_akhir_ori from (select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num, debit, credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select amount from b_saldoawal_bank where account = '008-998-1982') ,@runnum:= 0) runtot) a  ORDER BY a.nomor desc limit 1),@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1) a ) a inner join (select no_coa,debit_idr,credit_idr,IF(debit_idr = 0,credit_idr,debit_idr) total from tbl_list_journal where keterangan like '%REVALUASI%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') OR keterangan like '%REVALUATION%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
) b on b.no_coa = a.no_coa");
        $row_pem = mysqli_fetch_array($sql_pemasok);
        $total_pemasok = isset($row_pem['pemb_pem_lain']) ? $row_pem['pemb_pem_lain'] : 0;

        $sql2 = mysqli_query($conn2,"select id,sub_kategori,- total total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('Arus Kas dari Aktivitas Investasi')) a left JOIN
           (select kategori,round(sum(debit) - sum(credit),2) total from (select ind_name kategori,debit,0 credit from(select id_direct_debit,ind_name, sum(debit_idr) debit from (select * from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,case when no_journal like '%/ALK/%' then 'AR'
           when no_journal like '%L/NAG%' then 'AR'
           when no_journal like '%E/NAG%' then 'AR'
           when no_journal like '%/INM/%' then 'AR'
           when no_journal like '%/IN/%' then 'AP'
           when no_journal like '%/RI/%' then 'AP'
           when no_journal like '%/RO/%' then 'AP'
           when no_journal like '%/OUT/%' then 'AP'
           when no_journal like '%SI/APR/%' then 'AP'
           when no_journal like '%BM/%' then 'Bank'
           when no_journal like '%BK/%' then 'Bank'
           when no_journal like '%RCO/%' then 'Cash'
           when no_journal like '%RCI/%' then 'Cash'
           when no_journal like '%KKK/%' then 'Petty Cash'
           when no_journal like '%KKM/%' then 'Petty Cash'
           when no_journal like '%GM/%' then 'GM'
           end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
           (select no_coa,id_direct_debit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
           (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_debit) a GROUP BY a.id_direct_debit) a GROUP BY id_direct_debit
           UNION                                   
           select ind_name kategori, 0 debit,credit from(select id_direct_credit,ind_name, sum(credit_idr) credit from (select * from (select * from (select no_coa coa_no, sum(ROUND(credit * rate,2)) credit_idr, case when no_journal like '%/ALK/%' then 'AR'
           when no_journal like '%L/NAG%' then 'AR'
           when no_journal like '%E/NAG%' then 'AR'
           when no_journal like '%/INM/%' then 'AR'
           when no_journal like '%/IN/%' then 'AP'
           when no_journal like '%/RI/%' then 'AP'
           when no_journal like '%/RO/%' then 'AP'
           when no_journal like '%/OUT/%' then 'AP'
           when no_journal like '%SI/APR/%' then 'AP'
           when no_journal like '%BM/%' then 'Bank'
           when no_journal like '%BK/%' then 'Bank'
           when no_journal like '%RCO/%' then 'Cash'
           when no_journal like '%RCI/%' then 'Cash'
           when no_journal like '%KKK/%' then 'Petty Cash'
           when no_journal like '%KKM/%' then 'Petty Cash'
           when no_journal like '%GM/%' then 'GM'
           end asal from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a where a.asal IN ('Bank','Cash','Petty Cash')) a inner join 
           (select no_coa,id_direct_credit from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
           (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_direct_credit) a GROUP BY a.id_direct_credit) a GROUP BY id_direct_credit) a GROUP BY kategori) b on b.kategori = a.sub_kategori order by id asc");

        if($tanggal_akhir < $tanggal_awal){
            $message = "Mohon Masukan Tanggal Filter Yang Benar";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
        else{
            $no = 01;
            $total_aktivitas_operasi = 0;
            while($row = mysqli_fetch_array($sql)){
                $aktivitasoperasi = isset($row['total']) ? $row['total'] : 0;
                $subkategori = $row['sub_kategori'];

                if ($subkategori == 'Pembayaran Kepada Pemasok Lain-Lain') {
                    $aktivitas_operasi_ = $aktivitasoperasi + $total_pemasok;
                }else{
                    $aktivitas_operasi_ = $aktivitasoperasi;
                }

                if ($aktivitas_operasi_ > 0) {
                    $aktivitas_operasi = number_format($aktivitas_operasi_,2);
                }else{
                    $aktivitas_operasi = '('.number_format(abs($aktivitas_operasi_),2).')';
                }

                $total_aktivitas_operasi += $aktivitas_operasi_;
                if ($total_aktivitas_operasi > 0) {
                    $total_aktivitas_operasi_ = number_format($total_aktivitas_operasi,2);
                }else{
                    $total_aktivitas_operasi_ = '('.number_format(abs($total_aktivitas_operasi),2).')';
                }

                echo '<tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_operasi.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_operasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from operating activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Investasi</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Investing Activities</th>
            </tr>';

            $total_aktivitas_investasi = 0;
            $bersih_kas_setarakas = 0;
            $bersih_kas_setarakas_ = 0;
            while($row2 = mysqli_fetch_array($sql2)){
                $aktivitas_investasi = isset($row2['total']) ? $row2['total'] : 0;
                if ($aktivitas_investasi > 0) {
                    $aktivitas_investasi = number_format($aktivitas_investasi,2);
                }else{
                    $aktivitas_investasi = '('.number_format(abs($aktivitas_investasi),2).')';
                }

                $total_aktivitas_investasi += isset($row2['total']) ? $row2['total'] : 0;
                if ($total_aktivitas_investasi > 0) {
                    $total_aktivitas_investasi_ = number_format($total_aktivitas_investasi,2);
                }else{
                    $total_aktivitas_investasi_ = '('.number_format(abs($total_aktivitas_investasi),2).')';
                }

                echo '<tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_investasi.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas investasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_investasi_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from investing activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Pendanaan</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
            </tr>';
            ?>

            <tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan pinjaman</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php 
                    $sql_debit_17 = mysqli_query($conn2,"select '1.10.01' no_coa, SUM(penerimaan) penerimaan, -SUM(pembayaran) pembayaran from (select *,case when saldo_awal >= 0 and saldo_akhir < 0 then saldo_akhir
when saldo_awal < 0 and saldo_akhir < 0 then credit
else 0
end penerimaan, case when saldo_awal < 0 and saldo_akhir >= 0 then saldo_awal
when saldo_awal < 0 and saldo_akhir < 0 then debit
else 0
end pembayaran from (SELECT q1.date,q1.doc_num,q1.curr,q1.deskripsi,round(@runtot :=@runtot,2) saldo_awal,q1.credit,q1.debit, round(@runtot :=@runtot + q1.debit - q1.credit,2) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel' order by transaksi_date asc) AS q1 JOIN
     (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select amount from b_saldoawal_bank where account = '008-997-1979') ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1)) runtot) a) a");

                    $row_debit_17 = mysqli_fetch_array($sql_debit_17);
                    $total_debit_17 = isset($row_debit_17['penerimaan']) ? $row_debit_17['penerimaan'] : 0;

                    $sql_credit_17 = mysqli_query($conn2,"select '1.10.02' no_coa, SUM(penerimaan) penerimaan, SUM(pembayaran) pembayaran from (select *,case when saldo_awal >= 0 and saldo_akhir < 0 then saldo_akhir
when saldo_awal < 0 and saldo_akhir < 0 then credit
else 0
end penerimaan, case when saldo_awal < 0 and saldo_akhir >= 0 then saldo_awal
when saldo_awal < 0 and saldo_akhir < 0 then debit
else 0
end pembayaran from (SELECT q1.date,q1.doc_num,q1.curr,q1.deskripsi,round(@runtot :=@runtot,2) saldo_awal,q1.credit,q1.debit, round(@runtot :=@runtot + q1.debit - q1.credit,2) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,round(debit * rate,2) debit,round(credit * rate,2) credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel' order by transaksi_date asc) AS q1 JOIN
     (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,round(debit * rate,2) debit,round(credit * rate,2) credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select eqv_idr amount from b_saldoawal_bank where account = '008-998-1982') ,@runnum:= 0) runtot) a  ORDER BY a.nomor desc limit 1)) runtot) a) a");

                    $row_credit_17 = mysqli_fetch_array($sql_credit_17);
                    $total_credit_17 = isset($row_credit_17['penerimaan']) ? $row_credit_17['penerimaan'] : 0;

                    $sql_sa_bank = mysqli_query($conn2,"select IF(saldo_akhir_ori < 0 and credit_idr > 0,total,0) penerimaan from (select '1.10.02' no_coa, ROUND(saldo_akhir,2) saldo_akhir_ori from (select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num, debit, credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select amount from b_saldoawal_bank where account = '008-998-1982') ,@runnum:= 0) runtot) a  ORDER BY a.nomor desc limit 1),@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1) a ) a inner join (select no_coa,debit_idr,credit_idr,IF(debit_idr = 0,credit_idr,debit_idr) total from tbl_list_journal where keterangan like '%REVALUASI%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') OR keterangan like '%REVALUATION%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
) b on b.no_coa = a.no_coa");

                    $row_sa_bank = mysqli_fetch_array($sql_sa_bank);
                    $total_sa_bank = isset($row_sa_bank['penerimaan']) ? $row_sa_bank['penerimaan'] : 0;
                    $totalcf_17 = $total_credit_17 + $total_debit_17 + $total_sa_bank;
                    if ($totalcf_17 > 0) {
                        $total_17 = number_format($totalcf_17,2);
                    }else{
                        $total_17 = '('.number_format(abs($totalcf_17),2).')';
                    }
                    echo $total_17;
                    ?>
                </td>
                <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from loans</i></td>
            </tr>
            <tr>
                <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran pinjaman</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php
                    $sql_debit_18 = mysqli_query($conn2,"select '1.10.01' no_coa, SUM(penerimaan) penerimaan, -SUM(pembayaran) pembayaran from (select *,case when saldo_awal >= 0 and saldo_akhir < 0 then saldo_akhir
when saldo_awal < 0 and saldo_akhir < 0 then credit
else 0
end penerimaan, case when saldo_awal < 0 and saldo_akhir >= 0 then saldo_awal
when saldo_awal < 0 and saldo_akhir < 0 then debit
else 0
end pembayaran from (SELECT q1.date,q1.doc_num,q1.curr,q1.deskripsi,round(@runtot :=@runtot,2) saldo_awal,q1.credit,q1.debit, round(@runtot :=@runtot + q1.debit - q1.credit,2) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel' order by transaksi_date asc) AS q1 JOIN
     (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select amount from b_saldoawal_bank where account = '008-997-1979') ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1)) runtot) a) a");

                    $row_debit_18 = mysqli_fetch_array($sql_debit_18);
                    $total_debit_18 = isset($row_debit_18['pembayaran']) ? $row_debit_18['pembayaran'] : 0;

                    $sql_credit_18 = mysqli_query($conn2,"select '1.10.02' no_coa, SUM(penerimaan) penerimaan, SUM(pembayaran) pembayaran from (select *,case when saldo_awal >= 0 and saldo_akhir < 0 then saldo_akhir
when saldo_awal < 0 and saldo_akhir < 0 then credit
else 0
end penerimaan, case when saldo_awal < 0 and saldo_akhir >= 0 then saldo_awal
when saldo_awal < 0 and saldo_akhir < 0 then debit
else 0
end pembayaran from (SELECT q1.date,q1.doc_num,q1.curr,q1.deskripsi,round(@runtot :=@runtot,2) saldo_awal,q1.credit,q1.debit, round(@runtot :=@runtot + q1.debit - q1.credit,2) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,round(debit * rate,2) debit,round(credit * rate,2) credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel' order by transaksi_date asc) AS q1 JOIN
     (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,round(debit * rate,2) debit,round(credit * rate,2) credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select eqv_idr amount from b_saldoawal_bank where account = '008-998-1982') ,@runnum:= 0) runtot) a  ORDER BY a.nomor desc limit 1)) runtot) a) a");

                    $row_credit_18 = mysqli_fetch_array($sql_credit_18);
                    $total_credit_18 = isset($row_credit_18['pembayaran']) ? $row_credit_18['pembayaran'] : 0;

                    $sql_sk_bank = mysqli_query($conn2,"select IF(saldo_akhir_ori < 0 and debit_idr > 0,total,0) pembayaran from (select '1.10.02' no_coa, ROUND(saldo_akhir,2) saldo_akhir_ori from (select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select Round(saldo_akhir,2) saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num, debit, credit,a.curr from b_reportbank a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.transaksi_date and cr.curr = a.curr where akun = '008-998-1982' and transaksi_date < (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= (select amount from b_saldoawal_bank where account = '008-998-1982') ,@runnum:= 0) runtot) a  ORDER BY a.nomor desc limit 1),@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1) a ) a inner join (select no_coa,debit_idr,credit_idr,IF(debit_idr = 0,credit_idr,debit_idr) total from tbl_list_journal where keterangan like '%REVALUASI%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') OR keterangan like '%REVALUATION%' and no_coa = '1.10.02' and tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')
) b on b.no_coa = a.no_coa");

                    $row_sk_bank = mysqli_fetch_array($sql_sk_bank);
                    $total_sk_bank = isset($row_sk_bank['pembayaran']) ? $row_sk_bank['pembayaran'] : 0;

                // $totalcf_18 = $total_debit_18 - $total_credit_18;
                    $totalcf_18 = $total_credit_18 + $total_debit_18 + $total_sk_bank;
                    if ($totalcf_18 > 0) {
                        $total_18 = number_format($totalcf_18,2);
                    }else{
                        $total_18 = '('.number_format(abs($totalcf_18),2).')';
                    }
                    echo $total_18; 
                    ?>
                </td>
                <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment of loans</i></td>
            </tr>
            <?php
            $total_aktivitas_pendanaan_ = 0;
            $total_aktivitas_pendanaan = $totalcf_17 + $totalcf_18;
            if ($total_aktivitas_pendanaan_ > 0) {
                $total_aktivitas_pendanaan_ = number_format($total_aktivitas_pendanaan,2);
            }else{
                $total_aktivitas_pendanaan_ = '('.number_format(abs($total_aktivitas_pendanaan),2).')';
            }

            $bersih_kas_setarakas = $total_aktivitas_operasi + $total_aktivitas_investasi + $totalcf_17 + $totalcf_18;
            if ($bersih_kas_setarakas > 0) {
                $bersih_kas_setarakas_ = number_format($bersih_kas_setarakas,2);
            }else{
                $bersih_kas_setarakas_ = '('.number_format(abs($bersih_kas_setarakas),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas pendanaan</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_pendanaan_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from financing activities</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">'.$bersih_kas_setarakas_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
            </tr>';
            ?>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada awal periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
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
                    $total = isset($row['total']) ? $row['total'] : 0;
                    if ($total > 0) {
                        $total_ = number_format($total,2);
                    }else{
                        $total_ = '('.number_format(abs($total),2).')';
                    }
                    echo $total_; 
                    ?>
                </th>
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the beginning of period</i></th>
            </tr>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada akhir periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
                    <?php 
                    $totalcf_kas = $total + $bersih_kas_setarakas;
                    if ($totalcf_kas > 0) {
                        $total_jmlkas = number_format($totalcf_kas,2);
                    }else{
                        $total_jmlkas = '('.number_format(abs($totalcf_kas),2).')';
                    }
                    echo $total_jmlkas;
                    ?>
                </th>
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the end of period</i></th>
            </tr>
            <?php

            
        }
        ?>
    </table>
    <br>      
</div>
</div>


<div id="cf-indirect" class="tabcontent">
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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_cf_indirect_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " ><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 14px;color: #fff;text-shadow: 1px 1px 1px #000"> Excel </i></button></a>

    ';
}
?>
</br>
<div style="border:1px solid black;margin-top: 5px;">  
    <table style="font-size: 16px; margin:auto" border="0" role="grid" cellspacing="0" width="85%">
        <!-- aset - start -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">PT NIRWANA ALABARE GARMENT</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>PT NIRWANA ALABARE GARMENT</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>STATEMENTS OF CASH FLOW - INDIRECT METHOD</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>FOR THE PERIODS ENDED <?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></i></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>(Expressed in Rupiah, unless otherwise stated)</i></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"><?php
            $enddate = date("F Y");
            $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
            $rowakhir = mysqli_fetch_array($sqlakhir);
            $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
            $end_date = date("d F Y",strtotime($tgl_akhir));
            echo strtoupper($end_date); ?></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i></i></th>
        </tr>

        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba (Rugi) Bersih</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Net Income (Loss)</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Accumulated Depreciation Of Fixed Asset Adjustment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Laba Ditahan Tahun Lalu</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Previous Year Retained Earning Adjustment</i></td>
        </tr>
        <tr style="line-height: 40px;">
            <td style="text-align: left;vertical-align: middle;width: 27%;"></td>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i></i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash Flow from Operating Activities</i></th>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_operasi_ind.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
                </tr>';
            }
            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang digunakan untuk aktivitas operasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_operasi_ind_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow used from operating activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Investasi</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Investing Activities</th>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_investasi_ind.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
                </tr>';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang diperoleh dari aktivitas investasi</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_investasi_ind_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow generated from investing activities</th> 
            </tr>
            <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus Kas dari Aktivitas Pendanaan</th>
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash Flow from Financing Activities</th>
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
                <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row3['sub_kategori'].'</td>
                <td style="text-align: right;vertical-align: middle;width: 16%;">'.$aktivitas_Pendanaan_ind.'</td>
                <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row3['sub_kategori_eng'].'</td>
                </tr>';
            }

            $bersih_kas_setarakas_ind = $total_aktivitas_operasi_ind + $total_aktivitas_investasi_ind + $total_aktivitas_Pendanaan_ind + $tot_jml9 + $total1;
            if ($bersih_kas_setarakas_ind > 0) {
                $bersih_kas_setarakas_ind_ = number_format($bersih_kas_setarakas_ind,2);
            }else{
                $bersih_kas_setarakas_ind_ = '('.number_format(abs($bersih_kas_setarakas_ind),2).')';
            }

            echo '<tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Arus kas yang diperoleh dari aktivitas pendanaan</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_aktivitas_Pendanaan_ind_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Cash flow generated from financing activities</th> 
            </tr>
            <tr style="line-height: 40px;">
            <th style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan / (Penurunan) bersih kas dan setara kas</th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">'.$bersih_kas_setarakas_ind_.'</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;">Net Increase / (Decrease) in cash and cash equivalent</th>
            </tr>';
            ?>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada awal periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
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
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the beginning of period</i></th>
            </tr>
            <tr>
                <th style="text-align: left;vertical-align: middle;width: 27%;">Kas dan setara kas pada akhir periode</th>
                <th style="text-align: right;vertical-align: middle;width: 16%;">
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
                <th style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash and cash equivalent at the end of period</i></th>
            </tr>
            <?php

        }
        ?>
    </table>
    <br>      
</div>
</div>


</div>

</div>
</div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
            <div style="height: 225px" class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading" style="text-align: center;">UPLOAD</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                        Pilih File:
                        <input class="form-control" name="fileexcel" type="file" required="required">
                        <br>
                        <button class="btn btn-sm btn-info" type="submit">Submit</button>
                        <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
</div>
</div>
</div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
  </div> -->         

</div><!-- body-row END -->
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min.js"></script>
<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide'); 

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left'); 

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    
    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    
    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
    function openCity(evt, cityName) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active text-light bg-dark", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active text-light bg-dark";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script>
    function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("datatable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
    } else {
        tr[i].style.display = "none";
    }
}
}
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "M yyyy",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    $("#form-data").on("click", "#co_sal", function(){ 
        var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
        var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
        var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
        var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
        var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
        var copy_user = '<?php echo $user ?>';
        var to_saldo = document.getElementById('to_saldo').value;

        $.ajax({
            type:'POST',
            url:'copy_saldo_tb.php',
            data: {'no_coa':no_coa, 'beg_balance':beg_balance,'debit':debit, 'credit':credit,'end_balance':end_balance, 'copy_user':copy_user,'to_saldo':to_saldo},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                // alert(response);            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
        alert("Copy Saldo successfully");     
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
    });
</script>


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
        $('#mymodal').modal('show');
        var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
        var date = $(this).closest('tr').find('td:eq(1)').text();
        var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
        var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
        var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
        var curr = "IDR";

        $.ajax({
            type : 'post',
            url : 'ajax_cashin.php',
            data : {'no_ib': no_ib},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ib);
        $('#txt_tglbpb').html('Date : ' + date + '');
        $('#txt_no_po').html('Refference : ' + reff + '');
        $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script> -->


<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

<script>
    function alert_cancel() {
      alert("Master Bank Deactive");
      location.reload();
  }
  function alert_approve() {
      alert("Master Bank Active");
      location.reload();
  }
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>