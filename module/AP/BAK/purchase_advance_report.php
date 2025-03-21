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
</style>
<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">PURCHASE ADVANCE REPORT</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="purchase_advance_report.php" method="post">        
                <div class="form-row">

                  <div class="col-md-4">
                    <label for="nama_type"><b>No COA</b></label>            
                    <select style="background-color: gray;" class="form-control selectpicker" name="coa_number" id="coa_number" data-dropup-auto="false" data-live-search="true" required>
                     <option value="ALL"  selected="true">ALL</option> 
                     <?php
                     $coa_number ='';
                     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                    }                 
                    $sql = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2 where nama_coa like '%uang muka pembelian%'",$conn1);
                    while ($row = mysql_fetch_array($sql)) {
                        $data = $row['coa'];
                        $id_ctg2 = $row['no_coa'];
                        if($row['no_coa'] == $_POST['coa_number']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';
                        }
                        echo '<option value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                    }?>
                </select>
            </div>

            <div class="col-md-2 mb-3"> 
                <label for="start_date"><b>From</b></label>          
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
                   echo date("d-m-Y");
               } ?>" 
               placeholder="Tanggal Awal">
           </div>

           <div class="col-md-2 mb-3"> 
            <label for="end_date"><b>To</b></label>          
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
               echo date("d-m-Y");
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
<!--             <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

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
    $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;

    if ($coa_number == 'ALL') {

        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_pa_report_all.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-info " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
    }else{
        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_pa_report_coa.php?start_date='.$start_date.' && end_date='.$end_date.' && coa_number='.$coa_number.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
    }
        //<a style="padding-right: 5px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SFP</i></button></a>

        // <a style="padding-right: 5px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SPL</i></button></a>

        //     <a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= "margin-top: 30px;"><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>


    ?>  

</div>                                                            
</div>
<br/>
</div>
</form> 

<!-- <?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>
            <button id="btnupload" type="button" class="btn-success btn-xs" style="border-radius: 6%"><span class="fa fa-upload" aria-hidden="true"></span> Upload</button>';
        }else{
    echo '';
    }
?> -->
<div class="box body">
    <div class="row">       
        <div class="col-md-12">      
           <div class="tableFix" style="height: 450px;">        
            <table id="mytable2" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;width: 10%;">COA</th>
                        <th style="text-align: center;vertical-align: middle;width: 10%;">No FTR</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">FTR Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">Bankout Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">No PI</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">No PO</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Po Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 12%;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Begining Balance</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Addition</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (KB)</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (GM)</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Forex Gain / (Loss)</th>
                        <th style="text-align: center;vertical-align: middle;width: 7%;">Ending Balance</th>

                    </tr>
                </thead>
            </tbody>

            <?php
            $coa_number ='';
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
                $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
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
            }

            if ($coa_number == 'ALL') {
                $sql = mysqli_query($conn2,"SELECT *, if(ded = 0,0,((saldo_awal + addition) - ded)) forex, (saldo_awal + addition - ded - if(ded = 0,0,((saldo_awal + addition) - ded))) saldo_akhir from (select no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout,(saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm,0)) saldoawal_, addition addition_, (saldo_awal_idr - COALESCE(ded_bfr_idr,0) - COALESCE(tot_gm_bfr,0)) saldo_awal, addition_idr addition, COALESCE(ded_idr,0) ded,  COALESCE(tot_gm,0) ded_gm from (select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr, 0 addition, 0 addition_idr, rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                    UNION
                    select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc
                    UNION
                    select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal < '$start_date'
                    UNION
                    select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr, debit addition, debit_idr addition_idr,rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                        UNION
                        select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc
                        UNION
                        select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal BETWEEN '$start_date' and '$end_date') a 
                LEFT JOIN (select no_po, ded_bfr, kbon_date, IF(curr = 'USD',round(ded_bfr * COALESCE(rate,1),2),ded_bfr) ded_bfr_idr from (select no_po, curr, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) b on b.no_po = a.no_po

                LEFT JOIN (select no_po, ded, kbon_date, IF(curr = 'USD',round(ded * COALESCE(rate,1),2),ded) ded_idr from (select no_po, curr, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) c on c.no_po = a.no_po
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_bfr from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal < '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.no_ftr
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a where (saldoawal_ + addition_) > 0");
}else{
    $sql = mysqli_query($conn2,"SELECT *, ((saldo_awal + addition) - ded) forex, (saldo_awal + addition - ded - ((saldo_awal + addition) - ded)) saldo_akhir from (select no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout,(saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm,0)) saldoawal_, addition addition_, (saldo_awal_idr - COALESCE(ded_bfr_idr,0) - COALESCE(tot_gm_bfr,0)) saldo_awal, addition_idr addition, COALESCE(ded_idr,0) ded,  COALESCE(tot_gm,0) ded_gm from (select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr, 0 addition, 0 addition_idr, rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
        UNION
        select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc
        UNION
        select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal < '$start_date'
        UNION
        select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr, debit addition, debit_idr addition_idr,rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
            UNION
            select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%CBD%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc
            UNION
            select no_coa, no_journal, tgl_journal, b.reff_doc, curr, amount debit,rate,round(sum(amount * rate),2) debit_idr from (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%PV%') a INNER JOIN (select no_pv,reff_doc, amount from tbl_pv where reff_doc like '%DP%' group by reff_doc) b on b.no_pv = a.reff_doc group by b.reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal BETWEEN '$start_date' and '$end_date') a 
    LEFT JOIN (select no_po, ded_bfr, kbon_date, IF(curr = 'USD',round(ded_bfr * COALESCE(rate,1),2),ded_bfr) ded_bfr_idr from (select no_po, curr, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) b on b.no_po = a.no_po

    LEFT JOIN (select no_po, ded, kbon_date, IF(curr = 'USD',round(ded * COALESCE(rate,1),2),ded) ded_idr from (select no_po, curr, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po) a left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.kbon_date) c on c.no_po = a.no_po
    LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_bfr from tbl_list_journal where no_coa = '$coa_number' and no_journal like '%GM/%' and tgl_journal < '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.no_ftr
    LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where no_coa = '$coa_number' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a where (saldoawal_ + addition_) > 0 and no_coa = '$coa_number'");
}

$ttl_beg =0;
$ttl_add =0;
$ttl_ded =0;
$ttl_for =0;
$ttl_end =0;
$ttl_mj =0;
$sisa_dn = 0;
$sisa_dn_bfr = 0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no_coa = $row2['no_coa'];
    $sqlcoa = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2 where no_coa = '$no_coa'",$conn1);
    $rowcoa = mysql_fetch_array($sqlcoa);
    $coa = $rowcoa['coa'];

    $ttl_beg += $row2['saldo_awal'];
    $ttl_add += $row2['addition'];
    $ttl_ded += $row2['ded'];
    $ttl_mj += $row2['ded_gm'];
    $ttl_for += $row2['forex'];
    $ttl_end += $row2['saldo_akhir'];

    echo ' <tr style="font-size:12px;text-align:center;">
    <td style="text-align : left;" value = "'.$coa.'">'.$coa.'</td>
    <td style="text-align : left;" value = "'.$row2['no_ftr'].'">'.$row2['no_ftr'].'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_ftr'].'">'.date("d-M-Y",strtotime($row2['tgl_ftr'])).'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_bankout'].'">'.date("d-M-Y",strtotime($row2['tgl_bankout'])).'</td>
    <td style="text-align : left;" value = "'.$row2['no_pi'].'">'.$row2['no_pi'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
    <td style="width: 100px;" value = "'.$row2['tgl_po'].'">'.date("d-M-Y",strtotime($row2['tgl_po'])).'</td>
    <td style="text-align : left;" value = "'.$row2['supp'].'">'.$row2['supp'].'</td>
    <td style="text-align : right;" value = "'.$row2['saldo_awal'].'">'.number_format($row2['saldo_awal'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['ded'].'">'.number_format($row2['ded'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['ded_gm'].'">'.number_format($row2['ded_gm'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['forex'].'">'.number_format($row2['forex'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['saldo_akhir'].'">'.number_format($row2['saldo_akhir'],2).'</td>
    </tr>
    ';
}

echo ' <tr >
<th colspan="8" style="text-align : center;" value = "Total">Total</td>
<th style="text-align : right;" value = "'.$ttl_beg.'">'.number_format($ttl_beg,2).'</th>
<th style="text-align : right;" value = "'.$ttl_add.'">'.number_format($ttl_add,2).'</th>
<th style="text-align : right;" value = "'.$ttl_ded.'">'.number_format($ttl_ded,2).'</th>
<th style="text-align : right;" value = "'.$ttl_mj.'">'.number_format($ttl_mj,2).'</th>
<th style="text-align : right;" value = "'.$ttl_for.'">'.number_format($ttl_for,2).'</th>
<th style="text-align : right;" value = "'.$ttl_end.'">'.number_format($ttl_end,2).'</th>
</tr>
';


?>  
</tbody>
</table>
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
                    <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
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
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script>
    $(document).ready(function() {
        $('#mytable').dataTable({
            'order': [1, 'asc'],
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false
        });

        $("[data-toggle=tooltip]").tooltip();

    } );

</script>

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
            format: "dd-mm-yyyy",
            startDate : "01-01-2023",
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

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('btnupload').onclick = function () {
        location.href = "upload-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "list-journal.php";
    };
</script>

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


<!-- 
if ($coa_number == 'ALL') {
                $sql = mysqli_query($conn2,"SELECT * from (select no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout,(saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm_bfr,0)) saldo_awal, addition, COALESCE(ded,0) ded,  COALESCE(tot_gm,0) ded_gm, ((saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm_bfr,0)) + addition  - (COALESCE(ded,0) + COALESCE(tot_gm,0))) saldo_akhir from (select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr, 0 addition, 0 addition_idr, rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                    UNION
                    select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate, debit_idr from tbl_list_journal where reff_doc like '%CBD%' group by reff_doc
                    UNION
                    select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate, debit_idr from tbl_list_journal where reff_doc like '%DP%' group by reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal < '$start_date'
                    UNION
                    select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr, debit addition, debit_idr addition_idr,rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
                        UNION
                        select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%CBD%' group by reff_doc
                        UNION
                        select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%DP%' group by reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal BETWEEN '$start_date' and '$end_date') a 
                LEFT JOIN (select no_po, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po ) b on b.no_po = a.no_po

                LEFT JOIN (select no_po, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po ) c on c.no_po = a.no_po
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_bfr from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal < '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.no_ftr
                LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where nama_coa like '%uang muka pembelian%' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a where (saldo_awal + addition) > 0");
}else{
    $sql = mysqli_query($conn2,"SELECT * from (select no_coa, no_ftr, tgl_ftr, no_pi, a.no_po, tgl_po, supp, no_bankout, tgl_bankout,(saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm_bfr,0)) saldo_awal, addition, COALESCE(ded,0) ded,  COALESCE(tot_gm,0) ded_gm, ((saldo_awal - COALESCE(ded_bfr,0) - COALESCE(tot_gm_bfr,0)) + addition  - (COALESCE(ded,0) + COALESCE(tot_gm,0))) saldo_akhir from (select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, debit saldo_awal, debit_idr saldo_awal_idr, 0 addition, 0 addition_idr, rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
        UNION
        select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate, debit_idr from tbl_list_journal where reff_doc like '%CBD%' group by reff_doc
        UNION
        select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate, debit_idr from tbl_list_journal where reff_doc like '%DP%' group by reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal < '$start_date'
        UNION
        select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, no_pi, no_po, tgl_po, supp, no_journal no_bankout, tgl_journal tgl_bankout, 0 saldo_awal, 0 saldo_awal_idr, debit addition, debit_idr addition_idr,rate,a.curr,b.curr curr_bk, no_coa from (select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_cbd GROUP BY no_ftr_cbd) a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd INNER JOIN list_payment_cbd c on c.no_kbon = b.no_kbon GROUP BY no_ftr_cbd
            UNION
            select a.no_ftr_dp, a.tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.total, b.no_kbon, c.no_payment, a.curr from (select no_ftr_dp, tgl_ftr_dp, supp, no_po, tgl_po, no_pi, curr, sum(total) total from ftr_dp GROUP BY no_ftr_dp) a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp INNER JOIN list_payment_dp c on c.no_kbon = b.no_kbon GROUP BY no_ftr_dp) a INNER JOIN (select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%CBD%' group by reff_doc
            UNION
            select no_coa, no_journal, tgl_journal, reff_doc, curr, debit,rate,debit_idr from tbl_list_journal where reff_doc like '%DP%' group by reff_doc) b on b.reff_doc = a.no_payment where b.tgl_journal BETWEEN '$start_date' and '$end_date') a 
    LEFT JOIN (select no_po, dp_value ded_bfr, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_po ) b on b.no_po = a.no_po

    LEFT JOIN (select no_po, dp_value ded, DATE_FORMAT(create_date, '%Y-%m-%d') kbon_date from kontrabon_h where dp_value > 0 and status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_po ) c on c.no_po = a.no_po
    LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_bfr from tbl_list_journal where no_coa = '$coa_number' and no_journal like '%GM/%' and tgl_journal < '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.no_ftr
    LEFT JOIN (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where no_coa = '$coa_number' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) e on e.reff_doc = a.no_ftr) a where (saldo_awal + addition) > 0 and no_coa = '$coa_number'");
} -->