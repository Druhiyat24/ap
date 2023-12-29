<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST PAYMENT</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="Payment.php" method="post">        
        <div class="form-row">
            <div class="col-md-12">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>

            <div class="col-md-5">
            <label for="status"><b>Status</b></label>            
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="draft" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'draft'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Draft</option>
                <option value="Approved" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Approved'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Approved</option>
                <option value="Cancel" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Cancel'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Cancel</option>
                <option value="Closed" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Closed'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Closed</option>                                                                                                             
                </select>
                </div>
        <div class="form-row">
            <div class="col-md-6"> 
            <label for="start_date"><b>From</b></label>
            <input type="text" class="form-control tanggal" id="start_date" name="start_date" 
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
            <input type="text" class="form-control tanggal" id="end_date" name="end_date" 
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
            placeholder="Tanggal Akhir">
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
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>

    <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        $status = isset($_POST['status']) ? $_POST['status']: null;

        if($status == 'ALL'){
            echo '<a target="_blank" href="ekspor_lp_all.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'draft'){
            echo '<a target="_blank" href="ekspor_lp_draft.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Approved'){
            echo '<a target="_blank" href="ekspor_lp_app.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'Cancel'){
            echo '<a target="_blank" href="ekspor_lp_cancel.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Closed'){
            echo '<a target="_blank" href="ekspor_lp_closed.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }else{
            $filterr = ""; 
        }
        ?> 
            </div>                                                            
    </div>
</div>
</form> 

<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }else{
    echo '';
    }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 120px">No List Payment</th>
            <th style="text-align: center;vertical-align: middle;width: 130px">List Payment Date</th>
            <th style="text-align: center;vertical-align: middle;width: 250px">Supplier</th>
            <th style="text-align: center;vertical-align: middle;width: 130px">Total Kontrabon</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Outstanding</th>
            <th style="text-align: center;vertical-align: middle;width: 60px">Currency</th>
            <th style="text-align: center;vertical-align: middle; width: 70px">Create By</th>
            <th style="text-align: center;vertical-align: middle; width: 60px">Status</th>
            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th> 
            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th> 
            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th>                                                
            <th style="text-align: center;vertical-align: middle;width: 190px">Action</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Keterangan</th>  
            <th style="text-align: center;vertical-align: middle;display: none;">Keterangan</th>                                        
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
    if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where tgl_payment = '$date_now'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and tgl_pay = '$date_now' group by no_pay)");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' group by no_pay)");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where tgl_payment between '$start_date' and '$end_date'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and tgl_pay between '$start_date' and '$end_date' group by no_pay)");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where nama_supp = '$nama_supp'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and supplier = '$nama_supp' group by no_pay)");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where nama_supp = '$nama_supp' and tgl_payment between '$start_date' and '$end_date' group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and supplier = '$nama_supp' and tgl_pay between '$start_date' and '$end_date' group by no_pay)");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where status = '$status'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and status = '$status' group by no_pay)");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where status = '$status' and tgl_payment between '$start_date' and '$end_date'  group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and status = '$status' and tgl_pay between '$start_date' and '$end_date' group by no_pay)");
    }
    elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where status = '$status' and nama_supp = '$nama_supp' group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and status = '$status' and supplier = '$nama_supp' group by no_pay)");
    }
    else{
    $sql = mysqli_query($conn2,"(select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where status = '$status' and nama_supp = '$nama_supp' and tgl_payment between '$start_date' and '$end_date' group by no_payment)
        union
        (select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,SUM(outstanding) as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,'' as tgl_tempo,'' as memo from saldo_awal where no_pay not like '%LP/NAG%' and status = '$status' and supplier = '$nama_supp' and tgl_pay between '$start_date' and '$end_date' group by no_pay)");
}
    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {
    $status = $row['status'];         
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="width: 100px;" value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="width: 100px;" value = "'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
            <td style="width: 100px;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style="text-align:right;width: 100px;" value = "'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
            <td style="display: none;" value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td style="display: none;" value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
            <td style="display: none;" value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
            <td style="display: none;" value = "'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
            <td style="display: none;" value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="display: none;" value = "'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
            <td style="display: none;" value = "'.$row['pph_value'].'">'.number_format($row['pph_value'],2).'</td>
            <td style="display: none;" value = "'.$row['memo'].'">'.$row['memo'].'</td>';

            $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];
            $app = $rs['ap_apprv_lp'];

            echo '<td width="100px;">';
            if($status == 'Approved' and $group != 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Approved' and $group == 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
               <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Paid' and $group != 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Paid' and $group == 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
               <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Closed' and $group != 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Closed' and $group == 'STAFF' and $fin == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
               <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'draft' and $group != 'STAFF' and $fin == '1' and $app == '1'){
                echo '<a id="approve" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-paper-plane"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_approve();"> Approve</i></button></a>                
                <a id="delete" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-trash"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_cancel();"> Cancel</i></button></a>
                <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].'" target="_blank"><i class="fa fa-print" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <br />
                <br />
                <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';                
            }elseif($status == 'draft' and $fin == '1') {
                echo ' <a href="pdf_list_payment.php?no_payment='.$row['no_payment'].' && no_kbon='.$row['no_kbon'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';                
            }elseif($status == 'Cancel' and $group != 'STAFF' and $fin == '1') {
                echo ' <p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p>';                    
            }elseif($status == 'Cancel' and $group == 'STAFF' and $fin == '1') {
               echo ' <p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p>';                    
            }                                        
            echo '</td>';
            echo '</tr>';
        }
}?>
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodallistpayment" data-target="#mymodallistpayment" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_list_payment"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_list_payment" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                    
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
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
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
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
        var tgl_payment = $(this).closest('tr').find('td:eq(1)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
        var amount = $(this).closest('tr').find('td:eq(5)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
        var no_kbon = $(this).closest('tr').find('td:eq(9)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(10)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(11)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(13)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(14)').attr('value');
        var pph = $(this).closest('tr').find('td:eq(15)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvelistpayment.php',
            data: {'no_payment':no_payment, 'approve_user':approve_user, 'tgl_payment':tgl_payment, 'supp':supp, 'amount':amount, 'curr':curr, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'no_po':no_po, 'tgl_po':tgl_po, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'pph':pph},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();
                // alert(data);                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';
        $.ajax({
            type:'POST',
            url:'cancellistpayment.php',
            data: {'no_payment':no_payment, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();
                // alert(data);
                                                                            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodallistpayment').modal('show');
    var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_list_payment = $(this).closest('tr').find('td:eq(1)').text();
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(10)').text();
    var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(7)').attr('value');
    var status = $(this).closest('tr').find('td:eq(8)').attr('value');
    var top = $(this).closest('tr').find('td:eq(9)').attr('value');
    var keterangan = $(this).closest('tr').find('td:eq(16)').attr('value');               

    $.ajax({
    type : 'post',
    url : 'ajaxlistpayment.php',
    data : {'no_payment': no_payment},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_list_payment').html(no_payment);
    $('#txt_tgl_list_payment').html('Tgl List Payment : ' + tgl_list_payment + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_keterangan').html('Keterangan : ' + keterangan + '');                                        
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formpayment.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "Payment.php";
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