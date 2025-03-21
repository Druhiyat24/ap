<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST VERIFIKASI BPB</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="verifikasibpb.php" method="post">        
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
                <option value="GMF" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'GMF'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >GMF</option>
                <option value="GMF-PCH" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'GMF-PCH'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >GMF-PCH</option>
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
                </select>
                </div>

                <div class="col-md-2">
            <label for="status"><b>Date Filter</b></label>            
              <select class="form-control selectpicker" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
                <option value="tgl_bpb" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'tgl_bpb'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >BPB Date</option>
                <option value="create_date" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'create_date'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Create Date</option>
                <option value="confirm_date" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'confirm_date'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Approved Date</option>                                                                                                             
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
               echo '';
            } ?>" 
            placeholder="Start Date" autocomplete='off' >
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
            placeholder="Tanggal Akhir" >
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
</div>
</form>
</form>
<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create BPB'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '3'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }else{
    echo '';
        }
?>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">
 <?php
 $filter = isset($_POST['filter']) ? $_POST['filter']: null; 
 if ($filter == 'tgl_bpb') {
     $fil = 'BPB Date';
 }elseif ($filter == 'create_date') {
     $fil = 'Create Date';
 }else{
    $fil = 'Approve Date';
 }
 ?>

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center; vertical-align: middle;">No BPB</th>
            <th style="text-align: center; vertical-align: middle;">No PO</th>
            <th style="text-align: center; vertical-align: middle;"> <?php
 echo $fil;
 ?></th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">Created By</th>
            <th style="text-align: center; vertical-align: middle;">Confirm By (GMF)</th>
            <th style="text-align: center; vertical-align: middle;">Confirm By (PCH)</th>                         
            <th style="text-align: center; vertical-align: middle;">Status</th>                                    
            <th style="text-align: center; vertical-align: middle; display: none;">Action</th>
            <th style="text-align: center; vertical-align: middle; display: none;">TOP</th>                                    
            <th style="text-align: center; vertical-align: middle; display: none;">Curr</th>
            <th style="text-align: center; vertical-align: middle; display: none;">Tgl PO</th>    
            <th style="text-align: center; vertical-align: middle; display: none;">Tgl PO</th>
            <th style="text-align: center; vertical-align: middle;">No Dokumen</th>                        
        </tr>
    </thead>
    
<tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $filter = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;
    $filter = isset($_POST['filter']) ? $_POST['filter']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }

//    if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
//    $kondisi = '';
//    }
//    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = '';
//    }
//    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = 'where supplier = '.$nama_supp.'';
//    }
//    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where supplier = '.$nama_supp.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = 'where status = '.$status.'';
//    }
//    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where status = '.$status.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    else{
//    $kondisi = 'where supplier = '.$nama_supp.' and status = '.$status.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }

//    echo $kondisi;
    if ($filter == 'tgl_bpb') {
        if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where tgl_bpb = '$date_now' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where tgl_bpb between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and tgl_bpb between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' and tgl_bpb between '$start_date' and '$end_date' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' group by no_bpb, create_date");
    }        
    else{
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' and tgl_bpb between '$start_date' and '$end_date' group by no_bpb, create_date");
    } 
    }elseif ($filter == 'create_date') {
      if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where tgl_bpb = '$date_now' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where create_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and create_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' and create_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' group by no_bpb, create_date");
    }        
    else{
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' and create_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }
    }else{
        if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where tgl_bpb = '$date_now' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where confirm_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and confirm_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }    
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' group by no_bpb, create_date");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where status = '$status' and confirm_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }
    elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' group by no_bpb, create_date");
    }        
    else{
    $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, supplier, create_date, confirm_date, create_user, confirm1, confirm2, status, top, curr, tgl_po, SUM(qty * price) as total,upt_dok_inv,IF(upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur from bpb_new where supplier = '$nama_supp' and status = '$status' and confirm_date between '$start_date' and '$end_date' group by no_bpb, create_date");
    }
    }                

    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {   
        if ($filter == 'tgl_bpb') {
             $fileter = date("d-M-Y",strtotime($row['tgl_bpb']));
        }elseif ($filter == 'create_date') {
            $fileter = date("d-M-Y",strtotime($row['create_date']));
        }else{
            $fileter = date("d-M-Y",strtotime($row['confirm_date']));
         }     
        echo'<tr style="font-size: 12px; text-align: center;">
            <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td value="'.$row['pono'].'">'.$row['pono'].'</td>
            <td value="'.$fileter.'">'.$fileter.'</td>
            <td style="width: 250px;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
            <td value="'.$row['create_user'].'">'.$row['create_user'].'</td>
            <td value="'.$row['confirm1'].'">'.$row['confirm1'].'</td>
            <td value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
            <td value="'.$row['status'].'">'.$row['status'].'</td>
            <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="display: none;" value="'.$row['total'].'">'.$row['total'].'</td>
            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td>
            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
            <td value="'.$row['upt_dok_inv'].'">'.$row['upt_dok_inv'].'</td>';


            echo '<td style="display: none;" width="100px;">';

            $bpb = $row['no_bpb'];
            $query = mysqli_query($conn2,"select confirm2, status from bpb_new where no_bpb ='$bpb'");
            $rows = mysqli_fetch_array($query);
            $confirm2 = $rows['confirm2'];
            $status = $rows['status'];

            $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $pur = $rs['purchasing'];
            $app_po = $rs['approve_po'];
            
            if(!empty($confirm2) || strpos($status, 'Cancel') !== false || $group == 'STAFF' || $pur == '0') {            
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>';
            }else {
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;"></i></a>';
            }

            if(strpos($status, 'GMF-') !== false || strpos($status, 'Cancel') !== false || $group == 'STAFF' || $pur == '0') {                
                echo '<a id="cancel" href=""><i class="fa fa-times" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>';
            }else{
                echo '<a id="cancel" href=""><i class="fa fa-times" style="padding-right: 10px; padding-left: 5px;"></i></a>';                
            }
            
            echo '</td>';
        echo '</tr>';
    }
                        
}?>
</tbody>                    
</table>

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
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                               
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div> 

    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>
  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
        startDate : "01-01-2021",
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
        var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
        var pono = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(11)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
        var total = $(this).closest('tr').find('td:eq(9)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
        var approve_user = '<?php echo $user ?>';
        var update_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvebpb.php',
            data: {'no_bpb':no_bpb, 'approve_user':approve_user, 'update_user':update_user, 'curr':curr, 'pono':pono, 'tgl_bpb':tgl_bpb, 'tgl_po':tgl_po, 'supp':supp, 'total':total},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                alert(data);
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#cancel", function(){                 
        var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
        var update_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancelbpb.php',
            data: {'no_bpb':no_bpb, 'update_user':update_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                alert(response);
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formverifikasibpb.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "verifikasibpb.php";
};
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
