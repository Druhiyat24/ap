<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">INCOMING BANK</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="bank-in22.php" method="post">
        <div class="form-row">
            <div class="col-md-6">
            <label for="nama_supp"><b>Source</b></label>            
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
                <option value="Unrealize" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                    if($status == 'Unrealize'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Unrealize</option>                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC",$conn1);
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
            <label for="nama_supp"><b>Bank</b></label>            
              <select class="form-control selectpicker" name="bank" id="bank" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $bank ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Upper(bank_name)) as nama_bank from b_masterbank  order by bank_name ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['nama_bank'];
                    if($row['nama_bank'] == $_POST['bank']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>

                <div class="col-md-3">
            <label for="nama_supp"><b>Account</b></label>            
              <select class="form-control selectpicker" name="akun" id="akun" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $akun = isset($_POST['akun']) ? $_POST['akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(bank_account) as no_rek from b_masterbank where bank_name = '$bank' order by no_rek ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['no_rek'];
                    if($row['no_rek'] == $_POST['akun']){
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
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
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
            <th style="text-align: center;vertical-align: middle;width : 20%">No Bank In</th>
            <th style="text-align: center;vertical-align: middle;width : 20%">Source</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Date</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Curreny</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Amount</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Outstanding</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Status</th>
            <th style="text-align: center;vertical-align: middle;width : 10%">Action</th>  
            <th style="display: none;">-</th>
            <th style="display: none;">-</th>
            <th style="display: none;">-</th>
            <th style="display: none;">-</th>                                                                                                                                                                       
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
            $nama_supp ='';
            $start_date ='';
            $end_date =''; 
            $status = '';
            $akun = '';
            $bank = '';           
            $date_now = date("Y-m-d");    
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $status = isset($_POST['status']) ? $_POST['status']: null;
            $akun = isset($_POST['akun']) ? $_POST['akun']: null;
            $bank = isset($_POST['bank']) ? $_POST['bank']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }

            if($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and !empty($end_date)){
            $where = "where date = '$date_now'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)){
            $where = "where date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where bank = '$bank' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where bank = '$bank' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where bank = '$bank' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where bank = '$bank' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }else{
              $where = "where customer = '$nama_supp' and bank = '$bank' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";  
            }

            $sql = mysql_query("select doc_num, customer, date, ref_data, akun, bank, curr, amount, outstanding, status,deskripsi from tbl_bankin_arcollection $where group by doc_num order by id asc",$conn1);
               // echo $start_date;
               //  echo $end_date;                                                       
                while($row = mysql_fetch_array($sql)){
                    $customer = $row['customer'];
                     $status = $row['status'];
                    
           echo'<tr>                       
                            <td style=" text-align : center" value="'.$row['doc_num'].'">'.$row['doc_num'].'</td>
                            <td style=" text-align : center" value="'.$row['customer'].'">'.$row['customer'].'</td>
                            <td style=" text-align : center" value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                                                                                             
                            <td style=" text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style=" text-align : center" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                            <td style=" text-align : center" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
                            <td style=" text-align : center" value="'.$row['status'].'">'.$row['status'].'</td>
                            ';
                            if($customer == 'Unrealize' and $status != 'Cancel') {
                            echo '<td style=" text-align : center">
                            <button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-warning">Update</button>
                            </td>'; 
                            } else{
                                echo'<td style=" text-align : center">
                            -
                            </td>';
                        }
                            echo '<td style="display: none" value="'.$row['ref_data'].'">'.$row['ref_data'].'</td>
                            <td style="display: none" value="'.$row['akun'].'">'.$row['akun'].'</td>
                            <td style="display: none" value="'.$row['bank'].'">'.$row['bank'].'</td>
                            <td style="display: none" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
                            ';
                                                                                                         
                        echo'</tr>';
                    
                    
                    } ?>
                    </tbody>
                    </table> 
                    </form> 
                    </div> 
                    </div> 

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Update Customer</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>No Incoming Bank</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_pv" id="txt_pv" 
            value="">
        </div>
        <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>Customer</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_cstr" id="txt_cstr" 
            value="">
        </div>
    </div>
                <div class="form-row">
                    <div class="col-md-12 mb-3"> 
            <label for="nama_supp"><b>New Customer</b></label>            
              <select class="form-control selectpicker" name="supp_update" id="supp_update" data-dropup-auto="false" data-live-search="true">
                                              
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC",$conn1);
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
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
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
            $nama_supp ='';
            $start_date ='';
            $end_date =''; 
            $status = '';
            $akun = '';
            $bank = '';           
            $date_now = date("Y-m-d");    
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $status = isset($_POST['status']) ? $_POST['status']: null;
            $akun = isset($_POST['akun']) ? $_POST['akun']: null;
            $bank = isset($_POST['bank']) ? $_POST['bank']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }

            if($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and !empty($end_date)){
            $where = "where date = '$date_now'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)){
            $where = "where date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where bank = '$bank' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where bank = '$bank' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where bank = '$bank' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status == 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and akun = '$akun' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank != 'ALL' and $akun == 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and bank = '$bank' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp != 'ALL' and $bank == 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where customer = '$nama_supp' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }elseif($nama_supp == 'ALL' and $bank != 'ALL' and $akun != 'ALL' and $status != 'ALL'){
            $where = "where bank = '$bank' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";
            }else{
              $where = "where customer = '$nama_supp' and bank = '$bank' and akun = '$akun' and status = '$status' and date between '$start_date' and '$end_date'";  
            }

            $jml = 0;
            $ost = 0;
            $sql = mysql_query("select sum(amount) as nominal, sum(outstanding) as outstanding from tbl_bankin_arcollection $where",$conn1);
                    
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
    var txt_cstr = $(this).closest('tr').find('td:eq(1)').attr('value');


        
        //make your ajax call populate items or what even you need supp_update
    $('#txt_pv').val(no_pv);
    $('#txt_cstr').val(txt_cstr);
                       
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
    $("#modal-form2").on("click", "#send2", function(){ 
        var no_bi = document.getElementById('txt_pv').value; 
        var customer = $('select[name=supp_update] option').filter(':selected').val();  
        var update_user = '<?php echo $user; ?>';   
         
             
        $.ajax({
            type:'POST',
            url:'update_cus.php',
            data: {'no_bi':no_bi, 'customer':customer, 'update_user':update_user},
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
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(1)').text();
    var refdoc = $(this).closest('tr').find('td:eq(8)').attr('value');
    var akun = $(this).closest('tr').find('td:eq(9)').attr('value');
    var bank = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(3)').attr('value');
    var status = $(this).closest('tr').find('td:eq(6)').attr('value');
    var desk = $(this).closest('tr').find('td:eq(11)').text();

    $.ajax({
    type : 'post',
    url : 'ajaxbankin.php',
    data : {'no_ib': no_ib, 'refdoc': refdoc},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Customer : ' + supp + '');
    $('#txt_no_po').html('Reff Doc : ' + refdoc + '');
    $('#txt_supp').html('Account : ' + akun + '');
    $('#txt_top').html('Bank : ' + bank + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Status : ' + status + '');
    $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-bank-in2.php";
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
