<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">REPORT BANK ACCOUNT BALANCE</h2>
<div class="box">
    <div class="box header">

        
       <form id="form-data" action="bankreport.php" method="post">        
        <div class="form-row">
            <div class="col-md-5">
            <label for="accountid" class="col-form-label" ><b>Account </b></label>  
                <select class="form-control selectpicker" name="accountid" id="accountid" data-live-search="true" onchange='changeValueACC(this.value)' required >
                <option value="" disabled selected="true">Select Account</option>  
                <?php 
                        $sqlacc = mysqli_query($conn1,"select CONCAT(bank_account,' (',upper(bank_name),')') name_bank, upper(bank_name) as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode from b_masterbank where status = 'Active'");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['account'];
                            $data2 = $row['name_bank'];
                            if($row['account'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                            $jsArray .= "prdName['" . $row['account'] . "'] = {curren:'".addslashes($row['curr'])."'};\n";
                        }
                        ?>
                </select>
                    <input type="hidden" style="font-size: 12px;" class="form-control" id="curren" name="curren" readonly > 
                </div> 

        <div class="form-row">
            <div class="col-md-6"> 
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
            placeholder="Start Date" autocomplete='off'>
            </div>

            <div class="col-md-6 mb-1">
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
            placeholder="End Date" autocomplete='off'>
            </div>
        </div>

            <div class="input-group-append col">                                   
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>
            </div>                                                            
    </div>
</br>
</div>
</form> 


<?php
    //     $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
    //     $rs = mysqli_fetch_array($querys);
    //     $group = $rs['Groupp'];
    //     $fin = $rs['finance'];

    //     if($fin == '0'){
    // echo '';
    //     }else{
    // echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
    // // echo "<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    // // echo '<button id="btndraft" type="button" class="btn-warning btn-xs" hidden><span class="fa fa-bars"></span> List Draft</button>';
    // }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">
                </br>
    <div style="margin-left: 30px">
        <?php
        $nama_bank = '';
        $accountid = isset($_POST['accountid']) ? $_POST['accountid']: null;
        $curren = isset($_POST['curren']) ? $_POST['curren']: null;
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        if ($accountid == '008-997-1979') {
        $nama_bank = 'BCA';
    }elseif ($accountid == '008-998-1982') {
        $nama_bank = 'BCA';
    }elseif ($accountid == '442-244-2000') {
        $nama_bank = 'BNI';
    }elseif ($accountid == '800-17781-4600') {
        $nama_bank = 'CIMB Niaga';
    }elseif ($accountid == '008-403-6249') {
        $nama_bank = 'BCA';
    }else {
        $nama_bank = '';
    }

    	if ($curren == 'USD') {
    		echo '<a target="_blank" href="report_usdbank.php?nama_bank='.$nama_bank.' && accountid='.$accountid.' && curren='.$curren.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success">
        <i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL</i></button></a>';
    	}else{
        echo '<a target="_blank" href="report_idrbank.php?nama_bank='.$nama_bank.' && accountid='.$accountid.' && curren='.$curren.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success">
        <i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL</i></button></a>';
    }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountid = isset($_POST['accountid']) ? $_POST['accountid']: null;
    $curren = isset($_POST['curren']) ? $_POST['curren']: null;
    if ($accountid == '008-997-1979') {
        $nama_bank = 'BCA';
    }elseif ($accountid == '008-998-1982') {
        $nama_bank = 'BCA';
    }elseif ($accountid == '442-244-2000') {
        $nama_bank = 'BNI';
    }elseif ($accountid == '800-17781-4600') {
        $nama_bank = 'CIMB Niaga';
    }elseif ($accountid == '008-403-6249') {
        $nama_bank = 'BCA';
    }else {
        $nama_bank = '';
    }

    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }

$sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
$rowsaldo = mysqli_fetch_array($sqlsaldo);
$salawal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;
$saldo_awal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

$sqlxss2 = mysqli_query($conn1,"select curr  from b_masterbank where bank_account = '$accountid'");
$rowxss2 = mysqli_fetch_array($sqlxss2);
$curren1 = isset($rowxss2['curr']) ? $rowxss2['curr'] : null;


$sqlyss1 = mysqli_query($conn1,"select nomor,date,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
     (SELECT @runtot:= $salawal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
$rowyss1 = mysqli_fetch_array($sqlyss1);
$saldoawal = isset($rowyss1['saldoawal']) ? $rowyss1['saldoawal'] : 0;
$dateswal = isset($rowyss1['date']) ? $rowyss1['date'] : null;


$sqlrates = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateswal'");
$rowrates = mysqli_fetch_array($sqlrates);
$maxidrate = isset($rowrates['id']) ? $rowrates['id'] : null;

if ($maxidrate != null) {
    $rates = $rowrates['rate'];
}else{
$sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
$rowxss = mysqli_fetch_array($sqlxss);
$maxidss = $rowxss['id'];

$sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'HARIAN'");
$rowyss = mysqli_fetch_array($sqlyss);
$rates = isset($rowyss['rate']) ? $rowyss['rate'] : 0;
}

$sqlsaldoidr = mysqli_query($conn1,"select eqv_idr from b_saldoawal_bank where account = '$accountid'");
$rowsaldoidr = mysqli_fetch_array($sqlsaldoidr);
$saldo_ = isset($rowsaldoidr['eqv_idr']) ? $rowsaldoidr['eqv_idr'] : 0;
$saldo__ = $saldoawal * $rates;

        ?>
    </div> 
    <br/>

    <div class="tableFix" style="height: 500px;">        
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
	<?php 
                $curren = isset($_POST['curren']) ? $_POST['curren']: null;
                if ( $curren1 == 'IDR') {
                    echo '
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 11%;">Name Bank</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: ';?><?php echo $nama_bank; ?><?php echo'</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Bank Account</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: ';?><?php echo $accountid; ?><?php echo'</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 23%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Currency</th>
            <th style="text-align: left;vertical-align: middle;width: 11%;">: ';?><?php echo $curren1; ?><?php echo'</th>                                                                            
        </tr>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 11%;">Transaction Date</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Journal No</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 30%;">Description</th>
            <th style="text-align: center;vertical-align: middle;width: 16%;">Category</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Debit</th> 
            <th style="text-align: center;vertical-align: middle;width: 11%;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Balance</th>                                                                            
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 11%;">Beginning Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 78%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 11%;">';?><?php echo number_format($saldoawal,2); ?> <?php echo'</th>                                                                            
        </tr>
    </thead>';
}else{
	echo '
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 10%;">Name Bank</th>
            <th style="text-align: left;vertical-align: middle;width: 10%;">: ';?><?php echo $nama_bank; ?><?php echo'</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Bank Account</th>
            <th style="text-align: left;vertical-align: middle;width: 10%;">: ';?><?php echo $accountid; ?><?php echo'</th>
            <th style="text-align: center;vertical-align: middle;width: 15%;">Benefficiary Name</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 15%;">: PT Nirwana Alabare Garment</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Currency</th>
            <th colspan="2" style="text-align: left;vertical-align: middle;width: 20%;">: ';?><?php echo $curren1; ?><?php echo'</th>                                                                            
        </tr>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 10%;">Transaction Date</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Journal No</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 28%;">Description</th>
            <th style="text-align: center;vertical-align: middle;width: 12%;">Category</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Debit</th> 
            <th style="text-align: center;vertical-align: middle;width: 10%;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Balance</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Balance Eq IDR</th>                                                                            
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Beginning Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 70%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">';?><?php echo number_format($saldoawal,2); ?> <?php echo'</th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">';?><?php echo number_format($saldo__,2); ?> <?php echo'</th>                                                                            
        </tr>
    </thead>';
}?>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accountid = isset($_POST['accountid']) ? $_POST['accountid']: null;
    $curren = isset($_POST['curren']) ? $_POST['curren']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
   if(empty($accountid) and empty($start_date) and empty($end_date)){
    echo '';
    }
    
    else {

     $sqlswl = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
     $rowswl = mysqli_fetch_array($sqlswl);
     $swl = isset($rowswl['amount']) ? $rowswl['amount'] : 0;
    
     $sqlswl2 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $salawal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl2 = mysqli_fetch_array($sqlswl2);
     $saldoswal = isset($rowswl2['saldoawal']) ? $rowswl2['saldoawal'] : 0;


     $sql = mysqli_query($conn1," SELECT '',q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
FROM
   (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
     (SELECT @runtot:= $saldoswal) runtot ");
    }


   while($row = mysqli_fetch_array($sql)){
    $debit = $row['debit'];
    $credit = $row['credit'];
    $credit = $row['credit'];


    $sqlrates2 = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateswal'");
$rowrates2 = mysqli_fetch_array($sqlrates2);
$maxidrate2 = isset($rowrates2['id']) ? $rowrates2['id'] : null;

if ($maxidrate2 != null) {
    $rates2 = $rowrates2['rate'];
}else{
$sqlxss2 = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
$rowxss2 = mysqli_fetch_array($sqlxss2);
$maxidss2 = $rowxss2['id'];

$sqlyss2 = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'HARIAN'");
$rowyss2 = mysqli_fetch_array($sqlyss2);
$rates2 = $rowyss2['rate'];
}

    // $balanc = $saldo_awal + $blc;
    // $balanceidr =  $saldo_ + ($blc* $rates);

    if($debit == '0'){
        $t_debit = '';
    }else{
        $t_debit = number_format($row['debit'],2);
    }

    if($credit == '0'){
        $t_credit = '';
    }else{
        $t_credit = number_format($row['credit'],2);
    }
    
 		if($curren1 == 'IDR'){
        echo '<tr style="font-size:12px;text-align:center;">
            <td value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                            
            <td value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td colspan="3" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td>                            
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.number_format($row['saldo_akhir'],2).'</td>         
             ';
            
        }else{
        echo '<tr style="font-size:12px;text-align:center;">
            <td value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                            
            <td value = "'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td colspan="3" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value=""></td>                           
            <td style="text-align:right;" value = "'.$t_debit.'">'.$t_debit.'</td>
            <td style="text-align:right;" value = "'.$t_credit.'">'.$t_credit.'</td>         
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.number_format($row['saldo_akhir'],2).'</td>  
            <td style="text-align:right;" value = "'.$row['saldo_akhir'].'">'.number_format(($row['saldo_akhir'] * $rates2),2).'</td>        
             ';	
        }


}

     $sqlswl3 = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '$accountid'");
     $rowswl3 = mysqli_fetch_array($sqlswl3);
     $swl3 = isset($rowswl3['amount']) ? $rowswl3['amount'] : 0;
    
     $sqlswl4 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date < '$start_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $salawal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
     $rowswl4 = mysqli_fetch_array($sqlswl4);
     $saldoswal2 = isset($rowswl4['saldoawal']) ? $rowswl4['saldoawal'] : 0;

     $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
        FROM
        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '$accountid' and transaksi_date between '$start_date' and '$end_date' and status != 'Cancel') AS q1 JOIN
        (SELECT @runtot:= $saldoswal2,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
     $rows6 = mysqli_fetch_array($sql6);
     $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : 0;
     $dateakhir = isset($rows6['date']) ? $rows6['date'] : null;

     $sqlrates3 = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateakhir'");
$rowrates3 = mysqli_fetch_array($sqlrates3);
$maxidrate3 = isset($rowrates3['id']) ? $rowrates3['id'] : null;

if ($maxidrate3 != null) {
    $rates3 = $rowrates3['rate'];
}else{
$sqlxss3 = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
$rowxss3 = mysqli_fetch_array($sqlxss3);
$maxidss3 = isset($rowxss3['id']) ? $rowxss3['id'] : null;

$sqlyss3 = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss3' and v_codecurr = 'HARIAN'");
$rowyss3 = mysqli_fetch_array($sqlyss3);
$rates3 = isset($rowyss3['rate']) ? $rowyss3['rate'] : 1;
}


if($curren1 == 'IDR'){
echo '
            <tr >
            <th style="text-align: center;vertical-align: middle;width: 11%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 78%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 11%;">'.number_format($saldoakhir,2).'</th></th>                                                                            
        </tr>';
}else{
echo '
            <tr >
            <th style="text-align: center;vertical-align: middle;width: 10%;">Ending Balance</th>
            <th colspan="7" style="text-align: center;vertical-align: middle;width: 70%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($saldoakhir,2).'</th></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format(($saldoakhir * $rates3),2).'</th></th>                                                                            
        </tr>';	
}
?>


                                                         
</tbody>                    
</table>
</div>

<style type="text/css">
     .modal-dialog-full-width {
        width: 85% !important;
        height: 85% !important;
        margin: 210px 255px !important;
        padding: 0 !important;
        max-width:none !important;


    }

    .modal-content-full-width  {
        height: auto !important;
        min-height: 85% !important;
        border-radius: 0 !important;
        background-color: white !important;
        max-height: calc(100% - 200px);
         overflow-y: scroll;


    }

    .modal-header-full-width  {
        border-bottom: 1px solid #9ea2a2 !important;
    }

    .modal-footer-full-width  {
        border-top: 1px solid #9ea2a2 !important;
    }
</style>

   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade center" id="mymodalbon" data-target="#mymodalbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
       <div class="modal-dialog-full-width modal-dialog momodel modal-fluid" role="document">
        <div class="modal-content-full-width modal-content ">
            <div class=" modal-header-full-width   modal-header text-left">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
     <!--      <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->        
          <!-- <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <!-- <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                                          
          <div id="details" class="modal-body col-15" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        <div class="modal-footer-full-width  modal-footer">
                <button type="button" class="btn btn-danger btn-md btn-rounded" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>         
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
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

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        startDate : "14-04-2022",
        autoclose:true
    });
});
</script>

<script type="text/javascript"> 
<?php echo $jsArray; ?>
var nom = 0;
function changeValueACC(id){
    document.getElementById('curren').value = prdName[id].curren;
};
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<!-- <script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvekbon.php',
            data: {'no_kbon':no_kbon, 'approve_user':approve_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';        

        $.ajax({
            type:'POST',
            url:'cancelkbon.php',
            data: {'no_kbon':no_kbon, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                                                                            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script> -->

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(9)', function(){                
    $('#mymodalbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(11)').text();
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;                

    $.ajax({
    type : 'post',
    url : 'ajaxkartu_hutang_new.php',
    data : {'supp': supp, 'start_date':start_date, 'end_date':end_date},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(supp);
    $('#txt_tgl_kbon').html('Periode : ' + start_date + ''+ " - " +''+ end_date +'');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    // $('#txt_tgl_tempo').html('No PO: ' + tgl_tempo + '');
    // $('#txt_curr').html('PO Date : ' + curr + '');        
    // $('#txt_create_user').html('Create By : ' + create_user + '');
    // $('#txt_status').html('Status : ' + status + '');
    // $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    // $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    // $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                                     
});

</script>



<!-- <script type="text/javascript">
    document.getElementById('btnbpb').onclick = function () {
    location.href = "kartuhutang.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnkb').onclick = function () {
    location.href = "pcs2.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnlp').onclick = function () {
    location.href = "pcs.php";
};
</script> -->


<script type="text/javascript">
    document.getElementById('btndraft').onclick = function () {
    location.href = "draft_kb.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "bankreport.php";
};
</script>

<script>
function alert_cancel() {
  alert("Data Berhasil di Cancel");
  location.reload();
}
function alert_approve() {
  alert("Data Berhasil di Approve");
  location.reload();
}
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
