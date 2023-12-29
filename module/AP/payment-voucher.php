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
        <h2 class="text-center">LIST PAYMENT VOUCHER</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="payment-voucher.php" method="post">
        <div class="form-row">
            <div class="col-md-6">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $nama_supp = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                    if($nama_supp == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                 
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
                while ($row = mysql_fetch_array($sql)) {
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

                <div class="col-md-3">
            <label for="nama_supp"><b>Outstanding</b></label>            
              <select class="form-control selectpicker" name="ost" id="ost" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $ost = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ost = isset($_POST['ost']) ? $_POST['ost']: null;
                }                 
                    if($ost == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                        
                <option value="0" <?php
                $ost = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ost = isset($_POST['ost']) ? $_POST['ost']: null;
                }                 
                    if($ost == '0'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >Yes</option>
                </select>
                </div>

                <div class="col-md-3">
            <label for="nama_supp"><b>Pay Method</b></label>            
              <select class="form-control selectpicker" name="meth" id="meth" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $meth = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $meth = isset($_POST['meth']) ? $_POST['meth']: null;
                }                 
                    if($meth == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                          
                <?php
                $meth ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $meth = isset($_POST['meth']) ? $_POST['meth']: null;
                }                 
                $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['pay_method'];
                    if($row['pay_method'] == $_POST['meth']){
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
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
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

            <div class="col-md-3 mb-3"> 
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
            </div>

            <div class="col-md-3 mb-3"> 
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
            </div>           
</div>                   
    </div>
</form>
<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Bank'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '37'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }else{
    echo '';
    }
?>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

 <form id="formdata">            
<table id="mytable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No Payment Voucher</th>
            <th style="text-align: center;vertical-align: middle;">PV Date</th>
            <th style="text-align: center;vertical-align: middle;">Due Date</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Outstanding</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;width: 80px;">Action</th> 
            <th style="display: none;">Status</th> 
            <th style="display: none;">Status</th>
            <th style="display: none;">Status</th> 
            <th style="display: none;">Status</th>
            <th style="display: none;">Status</th> 
            <th style="display: none;">Status</th>
            <th style="display: none;">Status</th> 
            <th style="display: none;">Status</th>                                                                                                                                                                      
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
            $start_date ='';
            $end_date ='';
            $meth ='';
            $ost =''; 
            $where =''; 
            $date_now = date("Y-m-d");           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $meth = isset($_POST['meth']) ? $_POST['meth']: null;
            $ost = isset($_POST['ost']) ? $_POST['ost']: null;
            $status = isset($_POST['status']) ? $_POST['status']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }
            // echo $start_date;
            // echo $end_date;

            if($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and !empty($end_date)){
            $where = "where a.pv_date = '$date_now'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)){
            $where = "where a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }else{
              $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";  
            }

            $sql = mysql_query("select a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr,a.total,a.outstanding,a.status,a.no_cek,a.cek_date, a.nama_supp as pay_to,a.pay_date,a.pay_meth,a.frm_akun, a.to_akun, a.for_pay from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv $where group by a.no_pv",$conn1);
                                                                      
                while($row = mysql_fetch_array($sql)){
                    $pay_meth = $row['pay_meth'];
                    $cekdate = $row['cek_date'];
                    $status = $row['status'];
                    if ($cekdate == '' || $cekdate == '1970-01-01') { 
                        $cek_date = '';
                    }else{
                        $cek_date = date("d-m-Y",strtotime($row['cek_date'])); 
                    } 

                    $duedate = $row['due_date'];
                    if ($duedate == '' || $duedate == '1970-01-01') { 
                        $due_date = '-';
                    }else{
                        $due_date = date("d-m-Y",strtotime($row['due_date'])); 
                    }
                    
           echo'<tr>                       
                            <td style="width:50px; text-align : center" value="'.$row['no_pv'].'">'.$row['no_pv'].'</td>
                            <td style="width:100px; text-align : center" value="'.$row['pv_date'].'">'.date("d-M-Y",strtotime($row['pv_date'])).'</td> 
                            <td style="width:100px; text-align : center" value="'.$due_date.'">'.$due_date.'</td>                                                                                             
                            <td style="width:50px; text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px; text-align : center" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="width:50px; text-align : center" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
                            <td style="width:50px; text-align : center" value="'.$row['status'].'">'.$row['status'].'</td>
                            <td style="width:50px; text-align : center">';
                            if ($pay_meth == 'CHEQUE/GIRO' && $status != 'Draft') {
                                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-warning">Update</button>
                                <a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            </td>';
                            }elseif ($pay_meth == 'CHEQUE/GIRO' && $status == 'Draft') {
                                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-warning">Update</button>
                                <a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                                <a href="edit-paymentvoucher.php?no_pv='.base64_encode($row['no_pv']).' "><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-pencil-square-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Edit</i></button></a>
                            </td>';
                            }elseif ($pay_meth != 'CHEQUE/GIRO' && $status != 'Draft') {
                                echo '<a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> ';
                                $querys_pv = mysqli_query($conn1,"select maintain_pv from userpassword where username = '$user'");
                                $rs_pv = mysqli_fetch_array($querys_pv);
                                $maintain_pv = isset($rs_pv['maintain_pv']) ? $rs_pv['maintain_pv'] : 0;
                                if ($maintain_pv == '1') {
                                   echo '<button style="border-radius: 6px" type="button" id="btn_maintainpv" name="btn_maintainpv"  class="btn-xs btn-warning"><i class="fa fa-refresh" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Draft</i></button>';
                                }else{

                                }
                            echo '</td>';
                            }else{
                            echo '<a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            <a href="edit-paymentvoucher.php?no_pv='.base64_encode($row['no_pv']).' "><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-pencil-square-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Edit</i></button></a>';
                    }
                    echo '<td style="display : none;" value="'.$row['no_cek'].'">'.$row['no_cek'].'</td>
                    <td style="display : none;" value="'.$cek_date.'">'.$cek_date.'</td>
                    <td style="display: none; text-align : center" value="'.$row['pay_to'].'">'.$row['pay_to'].'</td>
                    <td style="display: none; text-align : center" value="'.date("d-M-Y",strtotime($row['pay_date'])).'">'.date("d-M-Y",strtotime($row['pay_date'])).'</td>
                    <td style="display: none; text-align : center" value="'.$row['pay_meth'].'">'.$row['pay_meth'].'</td>
                    <td style="display: none; text-align : center" value="'.$row['frm_akun'].'">'.$row['frm_akun'].'</td>
                    <td style="display: none; text-align : center" value="'.$row['to_akun'].'">'.$row['to_akun'].'</td>         
                    <td style="display: none; text-align : center" value="'.$row['for_pay'].'">'.$row['for_pay'].'</td>  
                    </tr>
                    ';
                    
                    
                    } ?>
                    </tbody>
                    </table>
                    </form>  
                    </div> 
                    </div> 

<!-- if ($pay_meth == 'CHEQUE/GIRO') {
                                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-warning">Update</button>
                                <a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            </td>';
                            }else{
                            echo '<a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            </td>';
                    } -->

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Update Cheque / Giro</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>No Payment Voucher</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_pv" id="txt_pv" 
            value="">
        </div>
    </div>
                <div class="form-row">
                 <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>No Cheque/Giro</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_cekgiro" id="txt_cekgiro" 
            >
        </div>
        <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>Cheque/Giro Date</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control tanggal" name="txt_cekdate" id="txt_cekdate" 
            >
        </div>   
            </br>
                    <div class="col-md-9">
                    </div>
                <div class="col-md-3">
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-success btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
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
        <h4 class="modal-title" id="Heading">Reverse Payment Voucher</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form3" method="post">
                <div class="form-row">
                <div class="col-md-6 mb-3"> 
                <label for="txt_pv3"><b>No Payment Voucher</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_pv3" id="txt_pv3" 
            value="">
        </div>
    </div>
                <div class="form-row">
                 <div class="col-md-12 mb-3"> 
                <label for="txt_reverse"><b>Description Reverse</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_reverse" id="txt_reverse" 
            >
        </div>
            </br>
                    <div class="col-md-9">
                    </div>
                <div class="col-md-3">
                <div class="modal-footer">
                    <button type="submit" id="send3" name="send3" class="btn btn-success btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
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
          <div id="txt_supp1" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm1" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>                            
                    
<div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-4 mb-2">
                <h4><i>Total</i></h4>
                <table >
            <?php
            $nama_supp ='';
            $start_date ='';
            $end_date ='';
            $meth ='';
            $ost =''; 
            $where =''; 
            $date_now = date("Y-m-d");           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $meth = isset($_POST['meth']) ? $_POST['meth']: null;
            $ost = isset($_POST['ost']) ? $_POST['ost']: null;
            $status = isset($_POST['status']) ? $_POST['status']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }


            if($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and empty($start_date) and empty($end_date)){
            $where = "where a.pv_date = '$date_now'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)){
            $where = "where a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status == 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth != 'ALL' and $ost == 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $meth == 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.nama_supp = '$nama_supp' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $meth != 'ALL' and $ost != 'ALL' and $status != 'ALL'){
            $where = "where a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";
            }else{
              $where = "where a.nama_supp = '$nama_supp' and a.pay_meth = '$meth' and a.outstanding != '$ost' and a.status = '$status' and a.pv_date between '$start_date' and '$end_date'";  
            }

            $jml = 0;
            $ost = 0;
            $sql = mysql_query("select sum(a.total) as nominal, sum(a.outstanding) as outstanding from tbl_pv_h a $where",$conn1);
                    
                    $row = mysql_fetch_array($sql);                                                  
                    $jml += $row['nominal'];
                    $ost += $row['outstanding'];

                    
           echo'<tr>   

            <th style="text-align: left;vertical-align: middle;">Amount</th>
            <th style="text-align: left;vertical-align: middle;"> </th>
            <th style="text-align: center;vertical-align: middle;"><input style="border: 3px solid #555; text-align: center; border-radius: 8px;" value = "'.number_format($jml,2).'" readonly></th>                                                                                                                                                                       
                        </tr>
                        <tr>
            <th style="text-align: left;vertical-align: middle;">Outstanding</th>
            <th style="text-align: left;vertical-align: middle;"> </th>
            <th style="text-align: center;vertical-align: middle;"><input style="border: 3px solid #555; text-align: center; border-radius: 8px;" value = "'.number_format($ost,2).'" readonly></th>                                                                                                                                                                                                                                
                        </tr>';
                    
                    
                     ?>
        </table>
            </div>
            </div>                                   
        </form>
        
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

<script>
    $(document).ready(function() {
    $('#mytable').dataTable({
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
  table = document.getElementById("mytable");
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
    $("#formdata").on("click", "#btnupdate", function(){            
    $('#mymodal2').modal('show');
    var no_pv = $(this).closest('tr').find('td:eq(0)').attr('value');
    var txt_cekgiro = $(this).closest('tr').find('td:eq(8)').attr('value');
    var txt_cekdate = $(this).closest('tr').find('td:eq(9)').attr('value');


        
        //make your ajax call populate items or what even you need supp_update
    $('#txt_pv').val(no_pv);
    $('#txt_cekgiro').val(txt_cekgiro);
    $('#txt_cekdate').val(txt_cekdate);
                       
});

</script>

<script type="text/javascript">     
    $("#formdata").on("click", "#btn_maintainpv", function(){            
    $('#mymodal3').modal('show');
    var no_pv = $(this).closest('tr').find('td:eq(0)').attr('value');
    
        //make your ajax call populate items or what even you need supp_update
    $('#txt_pv3').val(no_pv);
                       
});

</script>

<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){ 
        var no_pv = document.getElementById('txt_pv').value; 
        var cekgiro = document.getElementById('txt_cekgiro').value; 
        var cekdate = document.getElementById('txt_cekdate').value; 
        var update_user = '<?php echo $user; ?>';   
         
             
        $.ajax({
            type:'POST',
            url:'update_cekgiro.php',
            data: {'no_pv':no_pv, 'cekgiro':cekgiro, 'cekdate':cekdate, 'update_user':update_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(false);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
                return false; 
 
    });


</script>


<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){ 
        var no_pv = document.getElementById('txt_pv3').value; 
        var txt_reverse = document.getElementById('txt_reverse').value; 
        var update_user = '<?php echo $user; ?>';   
         
        if (txt_reverse != ''){
        $.ajax({
            type:'POST',
            url:'reverse_pv.php',
            data: {'no_pv':no_pv, 'txt_reverse':txt_reverse, 'update_user':update_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(false);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });   
        }          
        if(document.getElementById('txt_reverse').value == '' || document.getElementById('txt_reverse').value == null){
            alert("Please input descriptions");
            return false; 
        }else{
        }
 
    });


</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_pv = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_pv = $(this).closest('tr').find('td:eq(1)').text();
    var pay_to = $(this).closest('tr').find('td:eq(10)').attr('value');
    var pay_date = $(this).closest('tr').find('td:eq(11)').attr('value');
    var pay_meth = $(this).closest('tr').find('td:eq(12)').attr('value');
    var f_akun = $(this).closest('tr').find('td:eq(13)').attr('value');
    var t_akun = $(this).closest('tr').find('td:eq(14)').attr('value');
    var cek_no = $(this).closest('tr').find('td:eq(8)').attr('value');
    var cek_date = $(this).closest('tr').find('td:eq(9)').attr('value');
    var pay_for = $(this).closest('tr').find('td:eq(15)').attr('value');

    $.ajax({
    type : 'post',
    url : 'ajax_pv.php',
    data : {'no_pv': no_pv},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_pv);
    $('#txt_tglbpb').html('PV Date : ' + tgl_pv + '');
    $('#txt_top').html('Payment Method : ' + pay_meth + '');
    $('#txt_no_po').html('Payment To : ' + pay_to + '');
    $('#txt_supp1').html('Payment For : ' + pay_for + '');
    $('#txt_supp').html('Payment Date : ' + pay_date + '');
    $('#txt_curr').html('From Account : ' + f_akun + '');        
    $('#txt_confirm').html('To Account : ' + t_akun + '');
    $('#txt_confirm1').html('Cheque No : ' + cek_no + '');
    $('#txt_confirm2').html('Cheque Date : ' + cek_date + '');                
});

</script>

<!--<script type="text/javascript"> 
    $("#mytable").on("click", "#delbutton", function() {
    var sub = $(this).closest('tr').find('td:eq(4)').attr('data-subtotal');
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-tax');
    var total = $(this).closest('tr').find('td:eq(6)').attr('data-total');        
    var sub_val = document.getElementById("subtotal").value.replace(/[^0-9.]/g, '');
    var sub_tax = document.getElementById("pajak").value.replace(/[^0-9.]/g, '');
    var sub_total = document.getElementById("total").value.replace(/[^0-9.]/g, '');
    var min_sub = 0;
    var min_tax = 0;
    var min_total = 0;
    min_sub = sub_val - sub;
    min_tax = sub_tax - pajak;
    min_total = sub_total - total;
    $('#subtotal').val(formatMoney(min_sub));
    $('#pajak').val(formatMoney(min_tax));
    $('#total').val(formatMoney(min_total));                      
    $(this).closest("tr").remove();

});
</script>-->

<script type="text/javascript">
function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
};
    $("input[type=checkbox]").change(function(){
    var sub = 0;
    var tax = 0;
    var total = 0;
    var ceklist = 0;         
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value'),10) || 0;
    var qty = parseFloat($(this).closest('tr').find('td:eq(7)').attr('value'),10) ||0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(8)').attr('value'),10) ||0;               
    sub += price * qty;
    tax += tax;
    total = sub + tax;     
    });
    $("#subtotal").val(formatMoney(sub));
    $("#pajak").val(formatMoney(tax));
    $("#total").val(formatMoney(total));
    $("#select").val("1");                    
});        
</script>

<!--<script type="text/javascript">
$(document).ready(function(){
    $("#supp").on("change", function(){
        var supp= $('select[name=supp] option').filter(':selected').val();
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'supp':supp},
            close: function(e){
                e.preventDefault();
            },
            success: function(html){
                console.log(html);
                $("#no_po").html(html);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });            
        });
    });    
</script>-->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {        
        var ceklist = document.getElementById('select').value;         
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb_asal = $(this).closest('tr').find('td:eq(4)').attr('value');
        var create_user = '<?php echo $user ?>';
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;
        var tgl_po = $(this).closest('tr').find('td:eq(9)').attr('value');
        var pterms = $(this).closest('tr').find('td:eq(10)').attr('value');        

        $.ajax({
            type:'POST',
            url:'insertfmvbpb.php',
            data: {'no_bpb':no_bpb, 'no_bpb_asal':no_bpb_asal, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date, 'tgl_po':tgl_po, 'pterms':pterms},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'verifikasibpb.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data saved successfully");
        }else{
            alert("Please check the BPB number");
        }        
    });
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-paymentvoucher.php";
};
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<!--<script>
    $(document).ready(){
        $('#mybpb').click(function){
            $('#mymodal').modal('show');
        }
    }
</script>-->
<!--<script>
$(document).ready(function() {   
    $("#send").click(function(e) {
        e.preventDefault();
        var datas= $(this).children("option:selected").val();
        $.ajax({
            type:"post",
            url:"cek.php",
            dataType: "json",
            data: {datas:datas},
            success: function(data){
                alert("Success: " + data);
            }
        });               
    });
</script>-->
<!--<script>
$(document).ready(function (){
    $("select.selectpicker").change(function(){
        var selectedbpb = $(this).children("option:selected").val();
        document.getElementById("bpbvalue").value = selectedbpb;             
    });
});
</script>-->
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
