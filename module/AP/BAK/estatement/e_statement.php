<?php include '../header.php'; 
$blnfrom=isset($_GET['blnfrom']) ? $_GET['blnfrom'] : (date("m") -1);
$thnfrom=isset($_GET['thnfrom']) ? $_GET['thnfrom'] : date("y");
$blnto=isset($_GET['blnto']) ? $_GET['blnto'] : date("m");
$thnto=isset($_GET['thnto']) ? $_GET['thnto'] : date("y");
$namabank=isset($_GET['namabank']) ? $_GET['namabank'] : 'ALL';
$status_fil=isset($_GET['status']) ? $_GET['status'] : 'ALL';?>

<style type="text/css">
input[type=file]::file-selector-button {
  margin-right: 20px;
  border: none;
  background: #084cdf;
  padding: 10px 20px;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  transition: background .2s ease-in-out;
}

input[type=file]::file-selector-button:hover {
  background: #0d45a5;
}
</style>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST E-STATEMENT</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="e_statement.php" method="post">        
        <div class="form-row">
            <div class="col-md-5">
                <div class="form-row">
                    <div class="col-md-7">
                <label for="bln_from"><b>Periode From</b></label>            
                <select class="form-control selectpicker" name="bln_from" id="bln_from" data-dropup-auto="false" data-live-search="true">
                <?php
                // $sql_bln = mysqli_query($conn2,"select bulan_text,nama_bulan from dim_date where bulan_text = (DATE_FORMAT(CURRENT_DATE(),"%m") -1) GROUP BY bulan_text");
                // $row_bln = mysqli_fetch_array($sql_bln);  
                // $data = $row['bulan_text'];
                // $data2 = $row['nama_bulan'];  
                // $isSelected = ' selected="selected"';                      
                // echo '<option value="'.$namasupp.'"'.$isSelected.'">'. $namasupp .'</option>'; 
                $bln_from ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bln_from = isset($_POST['bln_from']) ? $_POST['bln_from']: null;
                }                 
                $sql = mysqli_query($conn1,"select bulan_text,nama_bulan from dim_date where tahun = '2025' GROUP BY bulan_text");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bulan_text'];
                    $data2 = $row['nama_bulan'];
                    $data3 = isset($_POST['bln_from']) ? $_POST['bln_from'] : str_replace("'", '', $blnfrom);
                    $fill = (date("m") -1);
                    if ($fill == 0 || $fill == '00') {
                        $fill_ = 12;
                    }else{
                        $fill_ = $fill;
                    }
                    if ($data3 == 0 && $row['bulan_text'] == $fill_) {
                        $isSelected = ' selected="selected"';
                    }else{

                    if($row['bulan_text'] == $data3){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
                </select>
                </div>

                <div class="col-md-5">
                <label for="thn_from"><b>-</b></label>            
                <select class="form-control selectpicker" name="thn_from" id="thn_from" data-dropup-auto="false" data-live-search="true">
                <?php
                $thn_from ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $thn_from = isset($_POST['thn_from']) ? $_POST['thn_from']: null;
                }                 
                $sql = mysqli_query($conn1,"select tahun from dim_date GROUP BY tahun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['tahun'];
                    $data3 = isset($_POST['thn_from']) ? $_POST['thn_from'] : str_replace("'", '', $thnfrom);
                    $fill = (date("m") -1);
                    if ($fill == 0 || $fill == '00') {
                        $fill_tahun = (date("Y") -1);
                    }else{
                        $fill_tahun = date("Y");
                    }
                    if ($data3 == 0 && $row['tahun'] == $fill_tahun) {
                        $isSelected = ' selected="selected"';
                    }else{
                    if($row['tahun'] == $data3){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>
                </div>
                </div>

                <div class="col-md-5">
                <div class="form-row">
                    <div class="col-md-7">
                <label for="bln_to"><b>Periode To</b></label>            
                <select class="form-control selectpicker" name="bln_to" id="bln_to" data-dropup-auto="false" data-live-search="true">
                <?php
                $bln_to ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bln_to = isset($_POST['bln_to']) ? $_POST['bln_to']: null;
                }                 
                $sql = mysqli_query($conn1,"select bulan_text,nama_bulan from dim_date where tahun = '2025' GROUP BY bulan_text");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bulan_text'];
                    $data2 = $row['nama_bulan'];
                    $data3 = isset($_POST['bln_to']) ? $_POST['bln_to'] : str_replace("'", '', $blnto);
                    if ($data3 == 0 && $row['bulan_text'] == date("m")) {
                        $isSelected = ' selected="selected"';
                    }else{
                    if($row['bulan_text'] == $data3){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
                </select>
                </div>

                <div class="col-md-5">
                <label for="thn_to"><b>-</b></label>            
                <select class="form-control selectpicker" name="thn_to" id="thn_to" data-dropup-auto="false" data-live-search="true">
                <?php
                $thn_to ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $thn_to = isset($_POST['thn_to']) ? $_POST['thn_to']: null;
                }                 
                $sql = mysqli_query($conn1,"select tahun from dim_date GROUP BY tahun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['tahun'];
                    $data3 = isset($_POST['thn_to']) ? $_POST['thn_to'] : str_replace("'", '', $thnto);
                    if ($data3 == 0 && $row['tahun'] == date("Y")) {
                        $isSelected = ' selected="selected"';
                    }else{
                    if($row['tahun'] == $data3){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>
                </div>
                </div>
                <div class="col-md-2">
                </div>

            <div class="col-md-5">
                <div class="form-row">
                    <div class="col-md-12">
            <label for="nama_bank"><b>Bank</b></label>            
              <select class="form-control selectpicker" name="nama_bank" id="nama_bank" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_bank ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_bank = isset($_POST['nama_bank']) ? $_POST['nama_bank']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(bank_name) as nama_bank from b_masterbank  order by bank_name ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['nama_bank'];
                    $data3 = isset($_POST['nama_bank']) ? $_POST['nama_bank'] : str_replace("'", '', $namabank);
                    if($row['nama_bank'] == $data3){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
            </div>
        </div>
                </div>

                <div class="col-md-3">
                <div class="form-row">
                    <div class="col-md-12">
            <label for="status"><b>Status</b></label>            
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                } 
                $status2 = isset($_POST['status']) ? $_POST['status']: str_replace("'", '', $status_fil); 
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {               
                    if($status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }else{
                    if($status2 == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="Sudah Upload" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }         
                $status2 = isset($_POST['status']) ? $_POST['status']: str_replace("'", '', $status_fil);        
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {               
                    if($status == 'Sudah Upload'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }else{
                    if($status2 == 'Sudah Upload'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }
                    echo $isSelected;
                ?>
                >Sudah Upload</option>
                <option value="Belum Upload" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                $status2 = isset($_POST['status']) ? $_POST['status']: str_replace("'", '', $status_fil);        
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {               
                    if($status == 'Belum Upload'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }else{
                    if($status2 == 'Belum Upload'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                }
                    echo $isSelected;
                ?>
                >Belum Upload</option>                                                                                                             
                </select>
                </div>
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

<!--     <?php
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
        ?>  -->
            </div>                                                            
    </div>
<br/>
</div>
</form>
<!-- 
<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Bank'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '37'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }else{
    echo '';
    }
?>   -->


    <div class="box body">
        <div class="row">       
            <div class="col-md-12">
                <div id="test" name="test"></div>

    <form id="formdata2">         
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="display: none;">Status</th>
            <th style="text-align: center;vertical-align: middle;width: 25%;">Bank</th>
            <th style="text-align: center;vertical-align: middle;width: 25%;">Account</th>
            <th style="text-align: center;vertical-align: middle;width: 20%;">Periode</th>
            <th style="text-align: center;vertical-align: middle;width: 15%;">Status</th>
            <th style="text-align: center;vertical-align: middle;width: 15%;">Pdf Rekening Koran</th>
            <th style="display: none;">Status</th>
            <th style="display: none;">Pdf Rekening Koran</th>
            <th style="display: none;">Pdf Rekening Koran</th>
            <th style="display: none;">Pdf Rekening Koran</th>
                                                        
        </tr>
    </thead>
   
    <tbody>
    <?php
    $bln_from ='';
    $thn_from = '';
    $bln_to = '';
    $thn_to ='';
    $nama_bank ='';
    $status ='';
//     echo 'post_max_size in bytes = ' . ini_get('post_max_size');
//     echo 'display_errors = ' . ini_get('display_errors');
// echo 'register_globals = ' . ini_get('register_globals');
// echo 'post_max_size = ' . ini_get('post_max_size');
// echo '== = ' . (ini_get('max_input_time'));
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $thn_from = isset($_POST['thn_from']) ? $_POST['thn_from']: date("Y"); 
    $bln_from = isset($_POST['bln_from']) ? $_POST['bln_from']: date("m"); 
    $thn_to = isset($_POST['thn_to']) ? $_POST['thn_to']: date("Y"); 
    $bln_to = isset($_POST['bln_to']) ? $_POST['bln_to']: date("m");
    $nama_bank = isset($_POST['nama_bank']) ? $_POST['nama_bank']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;               
    }else{

    $thn_from = str_replace("'", '', $thnfrom); 
    $bln_from = str_replace("'", '', $blnfrom); 
    $thn_to = str_replace("'", '', $thnto); 
    $bln_to = str_replace("'", '', $blnto);
    $nama_bank = str_replace("'", '', $namabank); 
    $status = str_replace("'", '', $status_fil); 
    }
    if($nama_bank == 'ALL' and $status == 'ALL'){
     $sql = mysqli_query($conn2,"select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun from 
        (select bank_name,bank_account from b_masterbank where status = 'Active') a join
        (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account order by bank_account, b.id desc");
    }
    elseif ($nama_bank != 'ALL' and $status == 'ALL') {            
     $sql = mysqli_query($conn2,"select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun from 
        (select bank_name,bank_account from b_masterbank where status = 'Active') a join
        (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account where a.bank_name = '$nama_bank' order by bank_account, b.id desc");
    }
    elseif ($nama_bank == 'ALL' and $status != 'ALL') {
     $sql = mysqli_query($conn2,"select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun from 
        (select bank_name,bank_account from b_masterbank where status = 'Active') a join
        (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account where IF(b.id is null,'Belum Upload','Sudah Upload') = '$status' order by bank_account, b.id desc");
    }else{
    $sql = mysqli_query($conn2,"select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun from 
        (select bank_name,bank_account from b_masterbank where status = 'Active') a join
        (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account where IF(b.id is null,'Belum Upload','Sudah Upload') = '$status' and a.bank_name = '$nama_bank' order by bank_account, b.id desc");
}
    $akun2 = '';
    while($row = mysqli_fetch_array($sql)){
        $akun = $row['bank_account'];
        $kode_tgl = $row['kode_tanggal'];
        $stat_upload = $row['stat_upload'];
        if ($stat_upload == 'Sudah Upload') {
            $color = 'background-color: #90EE90;';
        }else{
            $color = '';
        }
    if ($akun2 == $akun && $kode_tgl == $kode_tgl2) { 
    }else{      
        echo '<tr style="font-size:12px;text-align:center;'.$color.'">
            <td style="display: none;" value = "'.$row['kode_tanggal'].'">'.$row['kode_tanggal'].'</td>
            <td value = "'.$row['bank_name'].'">'.$row['bank_name'].'</td>
            <td value = "'.$row['bank_account'].'">'.$row['bank_account'].'</td>
            <td value = "'.$row['nama_bulan'].'">'.$row['nama_bulan'].'</td>
            <td value = "'.$row['stat_upload'].'">'.$row['stat_upload'].'</td>
            <td value = ""><button style="border-radius: 6px" type="button" class="btn-xs btn-warning" id="btnupdate" name="btnupdate"><i class="fa fa-cloud-upload" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button>';
            if ($row['stat_upload'] == 'Sudah Upload') {
               echo'<a href="file_pdf/'.$row['file_name'].'" target="_blank" style="padding-left:2px;"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button></a>
               <button style="border-radius: 6px;padding-left:2px;" type="button" class="btn-xs btn-info" id="btnshow" name="btnshow"><i class="fa fa-eye" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button>';
            }
            echo '</td>
            <td style="display: none;" value = "'.$row['bulan_text'].'">'.$row['bulan_text'].'</td>
            <td style="display: none;" value = "'.$row['tahun'].'">'.$row['tahun'].'</td>
            <td style="display: none;" value = "'.$row['file_name'].'">'.$row['file_name'].'</td>
            <td style="display: none;" value = "'.$row['file_name_as'].'">'.$row['file_name_as'].'</td>
            </tr>';
        $akun2 = $row['bank_account'];
        $kode_tgl2 = $row['kode_tanggal'];
        }
}?>
</tbody>                    
</table>
</form>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>


   <div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Uplolad E-Statement</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" Method="Post" Action="insert_estatement.Php" Enctype="Multipart/Form-Data">
                <div class="form-row">
                <div class="col-md-6 mb-3">
                <label for="nama_supp"><b>Bank Name</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_bank" id="txt_bank" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_kodetgl" id="txt_kodetgl" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_user" id="txt_user" value="<?php echo $user; ?>">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_bulan" id="txt_bulan" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_tahun" id="txt_tahun" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-blnfrom" id="txt-blnfrom" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-thn_from" id="txt-thn_from" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-bln_to" id="txt-bln_to" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-thn_to" id="txt-thn_to" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-nama_bank" id="txt-nama_bank" value="">
            <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt-status" id="txt-status" value="">
        </div>
        <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>Bank Account</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_akun" id="txt_akun" value="">
        </div>
    </div>
                <div class="form-row">
                 <div class="col-md-12 mb-3"> 
                <label for="nama_supp"><b>Upload File</b></label> 
                <Input Type="File" class="form-control" id="txtfile" Name="txtfile" Accept="Application/Pdf">
        </div>
            </br>
                    <div class="col-md-9">
                    </div>
                <div class="col-md-3">
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-success btn-lg" style="width: 100%;"><span class="fa fa-floppy-o"></span>
                        Save
                    </button>
                    </div>
                    </div>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div> 


<div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">E-Statement</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <div id="labelfile" name="labelfile"></div>
          <div id="fileshow" name="fileshow"></div>
        </div>
      </div>
    </div>
  </div>
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

    //  var redirect = function() {
    //         alert('test');
    // };

    // setTimeout(redirect, 1000);
});
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<!-- <script >
    $("#modal-form2").on("click", "#send2", function(){               
        var redirect = function() {
            alert('test');
    };

    setTimeout(redirect, 1000);
        });
</script> -->

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

<script type="text/javascript">     
    $("#formdata2").on("click", "#btnupdate", function(){ 
    var akun = $(this).closest('tr').find('td:eq(2)').attr('value');
    var bank = $(this).closest('tr').find('td:eq(1)').attr('value');           
    var kode_tgl = $(this).closest('tr').find('td:eq(0)').attr('value');
    var bulan = $(this).closest('tr').find('td:eq(6)').attr('value');           
    var tahun = $(this).closest('tr').find('td:eq(7)').attr('value');
    var bln_from = $('select[name=bln_from] option').filter(':selected').val(); 
    var thn_from = $('select[name=thn_from] option').filter(':selected').val(); 
    var bln_to = $('select[name=bln_to] option').filter(':selected').val(); 
    var thn_to = $('select[name=thn_to] option').filter(':selected').val(); 
    var nama_bank = $('select[name=nama_bank] option').filter(':selected').val(); 
    var status = $('select[name=status] option').filter(':selected').val();   
    $('#mymodal2').modal('show');
    $('#txt_akun').val(akun);
    $('#txt_bank').val(bank);
    $('#txt_bulan').val(bulan);
    $('#txt_tahun').val(tahun);
    $('#txt_kodetgl').val(kode_tgl);
    $('#txt-blnfrom').val(bln_from);
    $('#txt-thn_from').val(thn_from);
    $('#txt-bln_to').val(bln_to);
    $('#txt-thn_to').val(thn_to);
    $('#txt-nama_bank').val(nama_bank);
    $('#txt-status').val(status);

});

</script>

<script type="text/javascript">
    $(function () {
    $("#formdata2").on("click", "#btnshow", function(){ 
        var namafile = $(this).closest('tr').find('td:eq(8)').attr('value'); 
        var labelfile = $(this).closest('tr').find('td:eq(9)').attr('value'); 
        var fileName = '<embed src="file_pdf/'+ namafile + '" type="application/pdf" toolbar="0" frameborder="0" width="100%" height="400px">';
         // var fileName = '<embed src="file_pdf/'+ namafile + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" toolbar="0" frameborder="0" width="100%" height="400px">';
        var label = '<label for="labelfile"><b>'+ labelfile + '</b></label>';
        // alert(fileName);
        $('#labelfile').html(label);
        $('#fileshow').html(fileName); 
        $('#mymodal3').modal('show');
        
    });
    });

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-e_statement.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "e_statement.php";
};
</script>

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