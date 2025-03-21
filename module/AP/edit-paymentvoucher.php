<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM EDIT PAYMENT VOUCHER</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Payment Voucher</b></label>
                <?php
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select no_pv from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$row['no_pv'].'">'
            ?>
        </div>

            <div class="col-md-3 mb-3">            
            <label for="tgl_active" class="col-form-label" style="width: 150px;"><b>Payment Voucher Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select pv_date from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);                         
            if(!empty($no_pv)) {
                echo date("d-m-Y",strtotime($row['pv_date']));
            }
            else{
                echo date("d-m-Y");
            }  ?>" autocomplete='off'>

            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
            $shuffle  = substr(str_shuffle($karakter), 0, 8);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>

            
            <div class="col-md-4 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select nama_supp from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $namasupp = $row['nama_supp'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="'.$namasupp.'"'.$isSelected.'">'. $namasupp .'</option>'; 
            }
            else{
                echo '<option value="-">Select Supplier</option>'; 
            }  ?>
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' and Supplier != '$namasupp' order by Supplier ASC");
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

                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat_pv" name="rat_pv" 
                value="<?php
                    $no_pv = base64_decode($_GET['no_pv']);
                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    // $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'PAJAK'");
                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate FROM tbl_pv_h where no_pv = '$no_pv'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        ?>">

        



                                        
    </div>
</br>

        <div class="form-row">

<div class="col-md-4 mb-3">            
            <label for="sup_doc" class="col-form-label" style="width: 150px;"><b>Supporting Document</b></label>
                <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="sup_doc" name="sup_doc" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqlsd = mysqli_query($conn2,"select supp_doc from tbl_pv_h where no_pv = '$no_pv'");
            $rowsd = mysqli_fetch_array($sqlsd);  
            $supp_doc = $rowsd['supp_doc'];

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2," select GROUP_CONCAT(ket) as sup_doc from supp_doc_edit where ref_doc = '$no_pv' ");
            $row = mysqli_fetch_array($sql);
            $sup_doc = $row['sup_doc'];         
    
            // $top = 30;

            if(!empty($sup_doc)) {
                
                  echo $sup_doc;  
                
            }
            else{
                echo $supp_doc;
            } 
              ?>">
            </div>

            <div class="col-md-1 mb-3">            
            <label for="btn2" class="col-form-label" style="width: 200px;"><b>Select</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn2" id="btn2" data-target="#mymodal2" data-toggle="modal" value="Select"> 
            </div>

            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="ct_buyer"><b>Charge To Buyer</b></label>            
              <select class="form-control selectpicker" name="ct_buyer" id="ct_buyer" data-dropup-auto="false" data-live-search="true">
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select ctb from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $ctb = $row['ctb'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="'.$ctb.'"'.$isSelected.'">'. $ctb .'</option>'; 
            }
            else{
                echo '<option value="-">Select Buyer</option>'; 
            }  ?>
                <option value="-" <?php
                $ct_buyer = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ct_buyer = isset($_POST['ct_buyer']) ? $_POST['ct_buyer']: null;
                }                 
                    if($ct_buyer == '-'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >-</option>                                                 
                <?php
                $ct_buyer ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ct_buyer = isset($_POST['ct_buyer']) ? $_POST['ct_buyer']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' and Supplier != '$ctb' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['ct_buyer']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-2 mb-3">            
            <label for="tgl_pay" class="col-form-label" style="width: 150px;"><b>Payment Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_pay" id="tgl_pay" class="form-control tanggal" 
            value="<?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select pay_date from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);                         
            if(!empty($no_pv)) {
                echo date("d-m-Y",strtotime($row['pay_date']));
            }
            else{
                echo date("d-m-Y");
            }  ?>" autocomplete='off'>
            </div>
                <div class="col-md-3 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Pay Methods </label>               
                <select class="form-control selectpicker" name="carabayar" id="carabayar" data-live-search="true" >
                    <option value="" disabled selected="true">Choose pay method</option>
                    <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select pay_meth from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $pay_meth = $row['pay_meth'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="'.$pay_meth.'"'.$isSelected.'">'. $pay_meth .'</option>'; 
            }
            else{
                echo '<option value="-">Select Buyer</option>'; 
            }  ?>  
                    <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['forpay']) ? $_POST['forpay']: null;
                }                 
                $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod where pay_method != '$pay_meth' ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['pay_method'];
                    if($row['pay_method'] == $_POST['carabayar']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?> 
        
                </select> 
                </div>
                <div class="col-md-1 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Currency </label>               
                <select class="form-control selectpicker" name="curre" id="curre" data-live-search="true">
                    <option value="" disabled selected="true">Curr</option> 
                    <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select curr from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $curr = $row['curr'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="'.$curr.'"'.$isSelected.'">'. $curr .'</option>'; 
            }
            else{
                echo '<option value="-">Select Buyer</option>'; 
            }  ?>  
                    <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['curre']) ? $_POST['curre']: null;
                }                 
                $sql = mysqli_query($conn1,"select DISTINCT curr from b_masterbank where curr != '$curr' ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['curr'];
                    if($row['curr'] == $_POST['curre']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?> 
        
                </select> 
                </div>
  
    <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>For Payment</b></label>            
              <select class="form-control selectpicker" name="forpay" id="forpay" data-dropup-auto="false" data-live-search="true" >
                <option value="-" disabled selected="true">Select For Payment</option>  
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select for_pay from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $for_pay = $row['for_pay'];  

            $sqlza = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '1' and ref_doc = '$for_pay'");
            $rowza = mysqli_fetch_array($sqlza);  
            $ref_doc = $rowza['ref_doc'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($ref_doc)) {
                echo '<option value="'.$for_pay.'"'.$isSelected.'">'. $for_pay .'</option>'; 
            }
            else{
                echo '<option value="Lainnya"'.$isSelected.'">Lainnya</option>'; 
            }  ?>                                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['forpay']) ? $_POST['forpay']: null;
                }                 
                $sql = mysqli_query($conn1,"select ref_doc from master_forpay where ket = '1'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
                    if($row['ref_doc'] == $_POST['forpay']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>


        </div>
        <div class="col-md-3 mb-3">            
            <label for="pay_for" class="col-form-label" ><i>(for Option 'Lainnya')</i></label>
                <input type="text" style="font-size: 14px;" class="form-control" id="pay_for" name="pay_for" value="<?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select for_pay from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $for_pay = $row['for_pay'];  

            $sqlza = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '1' and ref_doc = '$for_pay'");
            $rowza = mysqli_fetch_array($sqlza);  
            $ref_doc = isset($rowza['ref_doc']) ? $rowza['ref_doc'] : null;  
            if(!empty($ref_doc)) {
                echo ''; 
            }
            else{
                echo $for_pay; 
            }  ?> " autocomplete = "off">
            </div>
            </div>
        </br>
<div class="form-row">
        <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>From Account</b></label>            
              <select class="form-control selectpicker" name="frcc" id="frcc" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>';?> 
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select a.frm_akun,IF(a.frm_akun = '-','-',b.coa_name) as bank from tbl_pv_h a left join b_masterbank b on b.bank_account = a.frm_akun where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $frm_akun = $row['frm_akun']; 
            $bank = $row['bank'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="'.$frm_akun.'"'.$isSelected.'">'. $bank .'</option>'; 
            }
            else{
                echo '<option value="-">-</option>'; 
            }  ?>
                <?php 
                       $frcc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $frcc = isset($_POST['frcc']) ? $_POST['frcc']: null;
                }                 
                $sql = mysqli_query($conn1,"select coa_name as bank,curr,bank_account as akun from b_masterbank where status = 'Active' and bank_account != '$frm_akun' group by id");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bank'];
                    $indata = $row['akun'];
                    if($row['bank'] == $_POST['frcc']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$indata.'"'.$isSelected.'">'. $data .'</option>';    
                        }
                        ?>
                </select>
                </div>

                <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>To Account</b></label>            
              <select class="form-control selectpicker" name="tocc" id="tocc" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option> 
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select a.to_akun,IF(a.to_akun = '-','-',b.coa_name) as bank from tbl_pv_h a left join b_masterbank b on b.bank_account = a.to_akun where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);  
            $to_akun = $row['to_akun']; 
            $bank = $row['bank']; 
            $isSelected = ' selected="selected"';                      
            if(!empty($no_pv)) {
                echo '<option value="-">-</option><option value="'.$to_akun.'"'.$isSelected.'">'. $bank .'</option>'; 
            }
            else{
                echo '<option value="-">-</option>'; 
            }  ?>
                <?php 
                       $tocc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $tocc = isset($_POST['tocc']) ? $_POST['tocc']: null;
                }                 
                $sql = mysqli_query($conn1,"select coa_name as bank,curr,bank_account as akun from b_masterbank where status = 'Active' and bank_account != '$to_akun' group by id");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bank'];
                    $indata = $row['akun'];
                    if($row['bank'] == $_POST['tocc']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$indata.'"'.$isSelected.'">'. $data .'</option>';    
                        }
                        ?>
                </select>
                   
            </div>
               <div class="col-md-2 mb-3">            
            <label for="ke" class="col-form-label" ><b>To</b> <i>(For Option 'Cicilan')</i></label>
                <input type="text" style="font-size: 14px;" class="form-control" id="ke" name="ke" value="<?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select ke from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);      
            $ke = $row['ke'];                  
            if(!empty($no_pv) && $ke != '0') {
                echo $ke;
            }elseif($ke == '0'){
                echo '';
            }else{
                echo '';
            }  ?>" autocomplete = "off">
            </div>
            <div class="col-md-2 mb-3">            
            <label for="dari" class="col-form-label" ><b>From</b> <i>(For Option 'Cicilan')</i></label>
                <input type="text" style="font-size: 14px;" class="form-control" id="dari" name="dari" value="<?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select dari from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sql);      
            $dari = $row['dari'];                  
            if(!empty($no_pv) && $ke != '0') {
                echo $dari;
            }elseif($ke == '0'){
                echo '';
            }
            else{
                echo '';
            }  ?>" autocomplete = "off">
            </div>
        <input type="hidden" style="font-size: 14px;" class="form-control" id="no_cek" name="no_cek" value="" autocomplete = "off">
        <input type="hidden" style="font-size: 14px;" class="form-control" id="cek_date" name="cek_date" value="" autocomplete = "off">
    </div>


<div class="form-row">


        <div class="col-md-10 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="2" type="text" class="form-control " name="pesan" id="pesan" placeholder="Description..." ><?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select deskripsi from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $deskripsi = $row['deskripsi'];                  
            if(!empty($no_pv)) {
                echo $deskripsi;
            }
            else{
                echo '';
            } ?></textarea>
        </div>
                                
 </div>
</br>

    

    <div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">For Payment</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form3" method="post">
                <div class="form-row">
                    <div class="col-6">

                    <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Check</th>
                            <th style="width:150px;">Supporting Doc</th>                                                    
                        </tr>
                    </thead>

            <tbody>
                    <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '1' ");


            while($row1 = mysqli_fetch_array($querys)){
                
                    echo '<tr>  
                    <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>  
                    <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>';
                    ?> 
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Ke</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="ke_berapa" id="ke_berapa" class="form-control" 
            value="">
            </div>
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Dari</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="dari_berapa" id="dari_berapa" class="form-control" 
            value="">
            </div>

            <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Dari Account</b></label>            
              <select class="form-control selectpicker" name="dari_akun" id="dari_akun" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>                                                 
                <?php
                $dari_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $dari_akun = isset($_POST['dari_akun']) ? $_POST['dari_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['dari_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Ke Account</b></label>            
              <select class="form-control selectpicker" name="ke_akun" id="ke_akun" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>                                                 
                <?php
                $ke_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ke_akun = isset($_POST['ke_akun']) ? $_POST['ke_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['ke_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-12 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Keterangan</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="keter" id="keter" value="<?php             
            if(!empty($_POST['keter'])) {
                echo $_POST['keter'];
            }
            else{
                echo '';
            } ?>" placeholder="..." required></textarea>
        </div>

                    
        </div>
                </div>  
            </div>
                <div class="modal-footer">
                    <button type="submit" id="send3" name="send3" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div> 

 <div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <table id="doc_support" class="table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr><th class="text-center">Cek</th>
            <th class="text-center">Supporting Doc</th>
        </tr>
    </thead>
    <tbody>
        <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '2' ");

            while($row1 = mysqli_fetch_array($querys)){
            $no_pv = base64_decode($_GET['no_pv']);
                $nodoc = $row1['ref_doc'];

                $sql22 = mysqli_query($conn2,"select ket from supp_doc_edit where ket = '$nodoc' and ref_doc = '$no_pv'");
                $row22 = mysqli_fetch_array($sql22);
                $ket = isset($row22['ket']) ? $row22['ket'] : null;

                $sql23 = mysqli_query($conn2,"select ket from supp_doc_edit where ket != 'Sales Order' and ket != 'Purchase Order' and ket != 'PEB' and ket != 'Invoice'");
                $row23 = mysqli_fetch_array($sql23);
                $ket2 = isset($row23['ket']) ? $row23['ket'] : null;
                
                    echo '<tr>'; 
                    if ($ket != '') {
                         echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>'; 
                     } else{
                    echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>'; 
                    }                        
                            echo '<td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>';
                   echo '<tr>'; 
                    if ($ket2 != '') {
                         echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$ket2.'" >
                            </td>                                                                                                 
                        </tr>'; 
                     } else{
                    echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>'; 
                    }  
                    
                    ?> 
    </tbody>                  
            </table> 
              
    </div>
  
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>                  
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr>
            <th class="text-center" style="width: 2%">-</th>
            <th class="text-center" style="width: 12%">COA</th>
            <th class="text-center" style="width: 10%">Cost Center</th>
            <th class="text-center" style="width: 9%">Reff Doc</th>
            <th class="text-center" style="width: 9%">Reff Date</th>
            <th class="text-center" style="width: 11%">Description</th>
            <th class="text-center" style="width: 9%">Amount</th>
            <th class="text-center" style="width: 9%">Deduction</th>
            <th class="text-center" style="width: 9%">Due date</th>
            <th class="text-center" style="width: 10%">PPH</th>
            <th class="text-center" style="width: 10%">PPN</th>
            <th class="text-center" style="width: 2%"> Action </th>
        </tr>
    </thead>
    
    <tbody id="tbody2">
        <tr style="display: none;">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td >
                <select class="form-control" name="nomor_coa" id="nomor_coa" > <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control" name="nomor_cc" id="nomor_cc" > <option value="-" > - </option> <?php $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc where status = 'Active'"); foreach ($sql2 as $ncc) : echo'<option value="'.$ncc["code_combine"].'"> '.$ncc["cost_name"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'>
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class='form-control tanggal' 
            value='' autocomplete='off' placeholder="dd-mm-yyyy">
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'> 
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_dedadd(value)" autocomplete = "off">
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_tempo" id="tgl_tempo" class="form-control tanggal" 
         autocomplete='off' placeholder="dd-mm-yyyy" value='<?= date("d-m-Y"); ?>'>
            </td>
            <td >
                <select class="form-control" name="pphh" id="pphh"  onchange="input_pph()"> <option data-idtax="0" value="0" > Non PPH </option> <?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH'  GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control" name="ppnn" id="ppnn"  onchange="input_ppn()"> <option data-idtax="" value="" > Non PPN </option> <?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN'  GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                </select>
            </td>

            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
        </tr>
        <?php
    $no_pv = base64_decode($_GET['no_pv']);
    $sqlpv = mysql_query("select * from (select a.id,a.id_pph,a.coa,concat(b.no_coa,' ', b.nama_coa) as nama_coa,a.no_cc,d.cc_name,a.reff_doc,a.reff_date,a.deskripsi,a.amount,a.ded_add,a.due_date,a.pph, IF(a.pph = '0','Non PPH',CONCAT(kriteria,' (',percentage,'%)')) as kriteria  from tbl_pv a left join mastercoa_v2 b on b.no_coa = a.coa left join mtax c on c.idtax = a.id_pph left join b_master_cc d on d.no_cc = a.no_cc where a.no_pv = '$no_pv' and IF(a.pph = '0',a.no_pv = '$no_pv', category_tax = 'PPH') group by a.id) a left join
(select a.id,a.id_ppn,a.ppn, IF(a.ppn = '0','Non PPN',CONCAT(kriteria,' (',percentage,'%)')) as kriteria2  from tbl_pv a left join mtax c on c.idtax = a.id_ppn where a.no_pv = '$no_pv' and IF(a.ppn = '0',a.no_pv = '$no_pv', category_tax = 'PPN') group by a.id) b on b.id = a.id",$conn1);

     while($row = mysql_fetch_array($sqlpv)){
                    $id_pph = $row['id_pph'];
                    $id_ppn = $row['id_ppn'];
                    $coa = $row['coa'];
                    $no_cc = $row['no_cc'];
                    $reff_date = $row['reff_date'];
                    $amount = $row['amount'];
                    $ded_add = $row['ded_add'];
                    if ($reff_date == '' || $reff_date == '1970-01-01') { 
                        $reffdate = '';
                    }else{
                        $reffdate = date("d-m-Y",strtotime($row['reff_date'])); 
                    } 

                    $duedate = $row['due_date'];
                    if ($duedate == '' || $duedate == '1970-01-01') { 
                        $due_date = '-';
                    }else{
                        $due_date = date("d-m-Y",strtotime($row['due_date'])); 
                    }
    echo'<tr>
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td >
                <select class="form-control" name="nomor_coa" id="nomor_coa" > <option value="'.$row['coa'].'" >'.$row['nama_coa'].'</option><option value="-" > - </option>';  $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa != '$coa'"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                <?php
                echo '
                </select>
            </td>
            <td >
                <select class="form-control" name="nomor_cc" id="nomor_cc" > <option value="'.$row['no_cc'].'" >'.$row['cc_name'].'</option><option value="-" > - </option>';  $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc where status = 'Active' and no_cc != '$no_cc'"); foreach ($sql2 as $ccs) : echo'<option value="'.$ccs["code_combine"].'"> '.$ccs["cost_name"].' </option>'; endforeach; ?>
                <?php
                echo '
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['reff_doc'].'" autocomplete="off">
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" value="'.$reffdate.'" class="form-control tanggal" 
            value="" autocomplete="off" placeholder="dd-mm-yyyy">
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" value="'.$row['deskripsi'].'" placeholder="" autocomplete="off"> 
            </td>';
            if ($amount == '0') {
                echo '<td>
                <input style="text-align: right;" type="number" min="1" value="" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off" readonly>
            </td>';
            }else{
            echo '<td>
                <input style="text-align: right;" type="number" min="1" value="'.$row['amount'].'" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>';
        }

        if ($ded_add == '0') {
                echo '<td>
                <input style="text-align: right;" type="number" min="1" value="" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_dedadd(value)" autocomplete = "off" readonly>
            </td>';
            }else{
            echo '<td>
                <input style="text-align: right;" type="number" min="1" value="'.$row['ded_add'].'" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_dedadd(value)" autocomplete = "off">
            </td>';
        }
            echo '
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_tempo" id="tgl_tempo" value="'.$due_date.'" class="form-control tanggal" 
         autocomplete="off" placeholder="dd-mm-yyyy" >
            </td>
            <td >
                <select class="form-control" name="pphh" id="pphh"  onchange="input_pph()"> <option data-idtax="'.$row['id_pph'].'" value="'.$row['pph'].'" >'.$row['kriteria'].'</option><option value="0" > Non PPH </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' and idtax != '$id_pph' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                <?php echo'</select>
            </td>

            <td >
                <select class="form-control" name="ppnn" id="ppnn"  onchange="input_ppn()"> <option data-idtax="'.$row['id_ppn'].'" value="'.$row['ppn'].'" >'.$row['kriteria2'].'</option><option value="" > Non PPN </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' and idtax != '$id_ppn' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                <?php echo'</select>
            </td>

            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
        </tr>';
}
?>
    </tbody>
    <tfoot>
          <tr>
            <td colspan="11" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Interject Row</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')" onclick="hitungRow()">Delete Row</button>
            <!-- <input  style="margin-right: 15px;border: 0; line-height: 1; padding: 10px 20px; font-size: 1rem; text-align: center; color: #fff; text-shadow: 1px 1px 1px #000; border-radius: 6px; background-color: rgb(30, 144, 255);" id="add" type="button" value="(+) Add">  -->
            </td>
          </tr>
    </tfoot>                   
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
            <div class="col-md-4">
                </br>

<!--             <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>PPH</b></label>
                 <select class="form-control" name="pilih_pph" id="pilih_pph" data-live-search="true" onchange='changeValueTax(this.value)' required >
                <option value="" disabled selected="true">Select Account</option>  
                <option value="0" selected="selected">Non PPH</option>
                <?php 
                        $sqlacc = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH'  GROUP BY idtax ");

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['percentage'];
                            $data2 = $row['kriteria2'];
                            if($row['kriteria2'] == $_POST['pilih_pph']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="pilih_pph" value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                            
                        }
                        ?>
                </select>
            </div>
        </br> -->
        <div style = "display : none;">
                <select class="form-control selectpicker" name="pilih_pph" id="pilih_pph" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>
                </select>
                </div>
            <!-- <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>PPN</b></label>
                 <select class="form-control" name="pilih_ppn" id="pilih_ppn" data-live-search="true" onchange='changeValueTax2(this.value)' required >
                <option value="" disabled selected="true">Select PPN</option>  
                <?php 
            $no_pv = base64_decode($_GET['no_pv']);
            $sql = mysqli_query($conn2,"select b.percentage,a.per_ppn,IF(a.per_ppn = '0','Non PPN',CONCAT(b.kriteria,' (',b.percentage,'%)')) as kriteria from tbl_pv_h a left join mtax b on b.percentage = a.per_ppn where no_pv = '$no_pv' and IF(a.per_ppn = '0',no_pv = '$no_pv',category_tax = 'PPN')");
            $row = mysqli_fetch_array($sql);  
            $per_ppn = $row['per_ppn']; 
            $kriteria = $row['kriteria'];  
            $isSelected = ' selected="selected"';  
            if($per_ppn == '0') {
                echo '<option value="0" selected="selected">Non PPN</option>'; 
            }
            else{
                echo '<option value="0" selected="selected">Non PPN</option>
                <option value="'.$per_ppn.'"'.$isSelected.'">'. $kriteria .'</option>'; 
            }                     
            // if(!empty($no_pv)) {
            //     echo '<option value="'.$per_ppn.'"'.$isSelected.'">'. $kriteria .'</option>'; 
            // }
            // else{
            //     echo '<option value="-">-</option>'; 
            // }

             ?>
                <?php              

                        $sqlacc = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' and percentage != '$per_ppn' GROUP BY idtax ");

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['percentage'];
                            $data2 = $row['kriteria2'];
                            if($row['kriteria2'] == $_POST['pilih_ppn']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="pilih_ppn" value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                            
                        }
                        ?>
                </select>
            </div>
            </br> -->
        <!--     <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>Tax (11%)</b></label>
                <input type="checkbox" id="check_vat_baru" name="check_vat_baru" onclick="modal_input_vat_baru()">
            </div>
            </br> -->
             
            
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                </br>
                <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Without Tax</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate1" name="nomrate1" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select format(subtotal,2) as subtotal from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $subtotal = $row['subtotal'];                  
            if(!empty($no_pv)) {
                echo $subtotal;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select subtotal from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $subtotal = $row['subtotal'];                  
            if(!empty($no_pv)) {
                echo $subtotal;
            }
            else{
                echo '';
            } ?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Deduction</b></label>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="ded_ad" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select adjust from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $adjust = $row['adjust'];                  
            if(!empty($no_pv)) {
                echo $adjust;
            }
            else{
                echo '';
            } ?>" name="ded_ad" placeholder="0.00" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="ded_ad_h" name="ded_ad_h" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select format(adjust,2) as adjust from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $adjust = $row['adjust'];                  
            if(!empty($no_pv)) {
                echo $adjust;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Incoming Tax</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="pph" name="pph" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select format(pph,2) as pph from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $pph = $row['pph'];                  
            if(!empty($no_pv)) {
                echo $pph;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select pph from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $pph = $row['pph'];                  
            if(!empty($no_pv)) {
                echo $pph;
            }
            else{
                echo '';
            } ?>">
                <input type="hidden" name="pph_min" id="pph_min" value="">
                <input type="hidden" name="pph_plus" id="pph_plus" value="">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Value Added Tax</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="ppn" name="ppn" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select format(ppn,2) as ppn from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $ppn = $row['ppn'];                  
            if(!empty($no_pv)) {
                echo $ppn;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
                <input type="hidden" name="ppn_h" id="ppn_h" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select ppn from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $ppn = $row['ppn'];                  
            if(!empty($no_pv)) {
                echo $ppn;
            }
            else{
                echo '';
            } ?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total</b></label>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="total" name="total" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select format(total,2) as total from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $total = $row['total'];                  
            if(!empty($no_pv)) {
                echo $total;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
                <input type="hidden" name="total_h" id="total_h" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select total from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $total = $row['total'];                  
            if(!empty($no_pv)) {
                echo $total;
            }
            else{
                echo '';
            } ?>">

            <input type="text" style="font-size: 14px;text-align: right;" class="form-control" name="total_h2" id="total_h2" value="<?php             
            $no_pv = base64_decode($_GET['no_pv']);
            $sqldes = mysqli_query($conn2,"select total from tbl_pv_h where no_pv = '$no_pv'");
            $row = mysqli_fetch_array($sqldes);      
            $total = $row['total'];                  
            if(!empty($no_pv)) {
                echo number_format($total,2);
            }
            else{
                echo '';
            } ?>" readonly>
            </div>
            </br>

             
        </div>
            
            
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='payment-voucher.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                           
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
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
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
    <script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

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
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
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

<script>
$(function() {
    $('.selectpicker').selectpicker();
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

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
} );
</script>

<!-- <script >
    $('#add').click( function() {      
 var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);


 $coa = '';
 var element1 = '<tr> <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><select class="form-control select2" name="nomor_coa" id="nomor_coa" style="width: 250px"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                </select></td> </td> <td><input  type="text" class="form-control" id="due_date" name="due_date" value="<?php echo date("Y-m-d"); ?>" style="text-align:center; width: 150px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="total" name="total" style="text-align:center; width: 180px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 180px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="amt" name="amt" style="text-align:center; width: 180px;" onkeypress="javascript:return isNumber(event)" oninput="modal_input_amt(value)" autocomplete="off"></td> <td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td> </tr>';
 row.innerHTML = element1; 
}); 


  </script> -->

<script type="text/javascript">
    
   // JavaScript Document
function addRow(tableID) {

    // var pay_mth = $('select[name=carabayar] option').filter(':selected').val();
    // var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
    // var sup_doc = document.getElementById('sup_doc').value;
    // var ctb = $('select[name=ct_buyer] option').filter(':selected').val();
    // var curr = document.getElementById('curre').value;
    // var for_pay = $('select[name=forpay] option').filter(':selected').val();
    //     if (for_pay == 'Lainnya') {
    //      var forpay = document.getElementById('pay_for').value;
    //     }else{
    //      var forpay = $('select[name=forpay] option').filter(':selected').val();   
    //     }
    // if(pay_mth != '' && nama_supp != '' && ctb != '' && curr != '' && forpay != '' || 
    //     pay_mth != '-' && nama_supp != '-' && ctb != '-' && curr != '-' && forpay != '-'){

        if($('select[name=nama_supp] option').filter(':selected').val() == '' || $('select[name=nama_supp] option').filter(':selected').val() == '-'){
        alert("Please select Supplier");
        document.getElementById('nama_supp').focus();
        }else if(document.getElementById('sup_doc').value == ''){
        alert("Please Select Support Document");
        document.getElementById('sup_doc').focus();
        }else if($('select[name=ct_buyer] option').filter(':selected').val() == ''){
        alert("Please select Charge to Buyer");
        document.getElementById('ct_buyer').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() == '' || $('select[name=carabayar] option').filter(':selected').val() == '-'){
        alert("Please select payment method");
        document.getElementById('carabayar').focus();
        }else if(document.getElementById('curre').value == ''){
        alert("Please Enter Currency");
        document.getElementById('curre').focus();
        }else if($('select[name=forpay] option').filter(':selected').val() == '' || $('select[name=forpay] option').filter(':selected').val() == '-'){
        alert("Please select For payment");
        document.getElementById('forpay').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=forpay] option').filter(':selected').val() == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=forpay] option').filter(':selected').val() == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() != '-' && $('select[name=tocc] option').filter(':selected').val() == '-'){
        alert("Please select To Account");
        document.getElementById('tocc').focus();
        }else{   
         var pay_date = document.getElementById('tgl_pay').value;
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var colCount = table.rows[0].cells.length;

    for(var i=0; i<colCount; i++) {
        var newcell = row.insertCell(i);
        newcell.innerHTML = table.rows[0].cells[i].innerHTML;
        var child = newcell.children;
        for(var i2=0; i2<child.length; i2++) {
            var test = newcell.children[i2].tagName;
            switch(test) {
                case "INPUT":
                    if(newcell.children[i2].type=='checkbox'){
                        // newcell.children[i2].value = "";
                        newcell.children[i2].checked[0] = true;

                    }else{
                        // newcell.children[i2].value = "";
                    }
                break;
                case "SELECT":
                    // newcell.children[i2].value = "";
                break;
                default:
                break;
            }
        }
    }            
        }
    }
    
function deleteRow(tableID)
{
    try
         {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[11].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    if (rowCount <= 1)
                        {
                        alert("Tidak dapat menghapus semua baris.");
                        break;
                        }
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                    
    var table = document.getElementById("tbody2");
    var tota = 0;
    var tota_ppn = 0;
    var t_ppn = 0;
    var tota_amt = 0;
    var tota_ded = 0;
    var harga = 0;
    var totall = 0;
    var tot_price= 0;
    var total_ppn= 0;
    var harga = 0;
    var harga2 = 0;
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var h_ppn = 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;

    if(price == ''){
        tot_price = - price2;
    }else{
        tot_price = price;
    }

    if (price == '') {
        harga = 0;
    }else{
        harga = price;
    }

    if (price2 == '') {
        harga2 = 0;
    }else{
        harga2 = price2;
    }


    tota += tot_price * (pph/100);
    tota_ppn += tot_price * (ppn/100);
    tota_amt += parseFloat(harga);
    tota_ded += parseFloat(- harga2);
    total_ppn = tota_amt * (h_ppn /100);

    if (tota_ppn == 0) {
        t_ppn = total_ppn;
    }else{
        t_ppn = tota_ppn;
    }

    var total_h = tota_amt + t_ppn - tota + tota_ded;


    document.getElementsByName("ppn_h")[0].value = (t_ppn).toFixed(2);
    document.getElementsByName("ppn")[0].value = formatMoney(t_ppn.toFixed(2));   
    document.getElementsByName("pph_h")[0].value = (- tota).toFixed(2);
    document.getElementsByName("pph")[0].value = formatMoney(- tota.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("nomrate_h")[0].value = (tota_amt).toFixed(2);
    document.getElementsByName("nomrate1")[0].value = formatMoney(tota_amt.toFixed(2));
    document.getElementsByName("ded_ad")[0].value = (tota_ded).toFixed(2);
    document.getElementsByName("ded_ad_h")[0].value = formatMoney(tota_ded.toFixed(2));
}
                    }
                }
            } catch(e)
    {
    alert(e);
    }
 }
 
 function InsertRow(tableID)
{
    try{
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[10].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    var newRow = table.insertRow(i+1);
                    var colCount = table.rows[0].cells.length;
                        for (h=0; h<colCount; h++){
                            var newCell = newRow.insertCell(h);
                            newCell.innerHTML = table.rows[0].cells[h].innerHTML;
                            var child = newCell.children;
                            for(var i2=0; i2<child.length; i2++) {
                                var test = newCell.children[i2].tagName;
                                switch(test) {
                                    case "INPUT":
                                        if(newCell.children[i2].type=='checkbox'){
                                            newCell.children[i2].value = "";
                                            newCell.children[i2].checked[9] = true;
                                        }else{
                                            newCell.children[i2].value = "";
                                        }
                                    break;
                                    case "SELECT":
                                        newCell.children[i2].value = "";
                                    break;
                                    default:
                                    break;
                                }
                            }
                        }
                    }
                    
                }
            } catch(e)
    {
    alert(e);
    }
 }

 function hitungRow(){
     
}
</script>

<script type="text/javascript">
//     function input_pph(){
//      var table = document.getElementById("tbody2");
//     var tota = 0;
//     var harga = 0;
//     var totall = 0;
//     var tot_price= 0;
//     var tot_min= 0;
//     var tot_plus= 0;
//     var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
//     var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
//     var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
//             for (var i = 1; i < (table.rows.length); i++) {

//     var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
//     var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
//     var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value;

//     if(price == ''){
//         tot_price = - price2;
//         tot_min += tot_price * (pph/100);
//         document.getElementsByName("pph_min")[0].value = (- tot_min).toFixed(2);
//     }else{
//         tot_price = price;
//         tot_plus += tot_price * (pph/100);
//         document.getElementsByName("pph_plus")[0].value = (- tot_plus).toFixed(2);
//     }

//     tota += tot_price * (pph/100);
//     var total_h = total_pv + ppn_h - tota + ded_ad;
    
//     document.getElementsByName("pph_h")[0].value = (- tota).toFixed(2);
//     document.getElementsByName("pph")[0].value = formatMoney(- tota.toFixed(2));
    // document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    // document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
    // document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
// }
// }

function input_pph(){
     var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var totall = 0;
    var tot_price= 0;
    var tot_min= 0;
    var tot_plus= 0;
    var ppn_h = 0;
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    // var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;

    if(price == ''){
        tot_price = - price2;
        tot_min += tot_price * (pph/100);
        document.getElementsByName("pph_min")[0].value = (- tot_min).toFixed(2);
    }else{
        tot_price = price;
        tot_plus += tot_price * (pph/100);
        document.getElementsByName("pph_plus")[0].value = (- tot_plus).toFixed(2);
    }

    tota += tot_price * (pph/100);
    ppn_h += tot_price * (ppn/100);
    var total_h = total_pv + ppn_h - tota + ded_ad;
    
    document.getElementsByName("pph_h")[0].value = (- tota).toFixed(2);
    document.getElementsByName("pph")[0].value = formatMoney(- tota.toFixed(2));
    document.getElementsByName("ppn_h")[0].value = (ppn_h).toFixed(2);
    document.getElementsByName("ppn")[0].value = formatMoney(ppn_h.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
}
}


function input_ppn(){
     var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var totall = 0;
    var tot_price= 0;
    var tot_min= 0;
    var tot_plus= 0;
    var id = 0;
    var pph_h = 0;
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    // var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var ppn = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;
    var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value || 0;

    if(price == ''){
        tot_price = - price2;
        tot_min += tot_price * (pph/100);
        document.getElementsByName("pph_min")[0].value = (- tot_min).toFixed(2);
    }else{
        tot_price = price;
        tot_plus += tot_price * (pph/100);
        document.getElementsByName("pph_plus")[0].value = (- tot_plus).toFixed(2);
    }

    tota += tot_price * (ppn/100);
    pph_h += tot_price * (pph/100);
    var total_h = total_pv - pph_h + tota + ded_ad;

    console.log(total_pv);
    console.log(ppn_h);
    console.log(tota);
    console.log(ded_ad);
    
    document.getElementsByName("ppn_h")[0].value = (tota).toFixed(2);
    document.getElementsByName("ppn")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("pph_h")[0].value = (- pph_h).toFixed(2);
    document.getElementsByName("pph")[0].value = formatMoney(- pph_h.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    
}
}


function getdate() {
    var pay_date = document.getElementById('tgl_pay').value;
    var table = document.getElementById("tbody2");
    for (var i = 1; i < (table.rows.length); i++) {

    var duedate = document.getElementById("tbody2").rows[i].cells[7].children[0];  
    duedate.value = pay_date;
}
}

// function getdate() {
//     var pay_date = document.getElementById('tgl_pay').value;
//     var table = document.getElementById("tbody2");
//     var rows = table.getElementsByTagName("tr");    
//     for (i = 0; i < rows.length; i++) {
//         var createClickHandler = function(row) {
//         return function() {
//       var currentRow = table.rows[i];

//     row.getElementsByTagName("td")[7];  = pay_date;
//     };
//       };
//       currentRow.onclick = createClickHandler(currentRow);
    
// }
// }
</script>

<script type="text/javascript">
      function modal_input_amt(){ 
    var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var pph_ded = parseFloat(document.getElementById('pph_min').value,10) || 0;
    var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0; 
    // var ppn = parseFloat(document.getElementById('pilih_ppn').value,10) || 0;    
    var table = document.getElementById("tbody2");
    var tota = 0;
    var tota_pph = 0;
    var total_pph = 0;
    var tota_ppn = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0];
    var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;

    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    tota += parseFloat(harga);
    tota_pph += parseFloat(harga) * (pph/100);
    total_pph = tota_pph - pph_ded;
    // console.log(ppn);
    tota_ppn += parseFloat(harga) * (ppn/100);
    totall = tota + ded_ad + tota_ppn - total_pph;


    document.getElementsByName("nomrate_h")[0].value = tota.toFixed(2);
    document.getElementsByName("nomrate1")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("total_h")[0].value = totall.toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(totall.toFixed(2));
    document.getElementsByName("total")[0].value = formatMoney(totall.toFixed(2));
    // document.getElementsByName("pph_h")[0].value = (- total_pph).toFixed(2);
    // document.getElementsByName("pph")[0].value = formatMoney(- total_pph.toFixed(2));
    // document.getElementsByName("ppn_h")[0].value = (tota_ppn).toFixed(2);
    // document.getElementsByName("ppn")[0].value = formatMoney(tota_ppn.toFixed(2));
    document.getElementsByName("pph_plus")[0].value = (- tota_pph).toFixed(2);
}
}

function modal_input_dedadd(){ 
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0; 
    var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var pph_amt = parseFloat(document.getElementById('pph_plus').value,10) || 0;
    var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;   
    var table = document.getElementById("tbody2");
    var tota = 0;
    var total = 0;
    var harga = 0;
    var harga2 = 0;
    var totall = 0;
    var tota_pph = 0;
    var total_pph = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var price_amt = document.getElementById("tbody2").rows[i].cells[6].children[0];
    var pph = document.getElementById("tbody2").rows[i].cells[9].children[0].value;

    if (price == '') {
        harga = 0;
        harga2 = price_amt;
        price_amt.readOnly = false;
    }else{
        harga = price;
        harga2 = 0;
        price_amt.readOnly = true;
    }
    tota += parseFloat(- harga);
    tota_pph += parseFloat(harga) * (pph/100);
    total_pph = pph_amt + tota_pph;
    total = total_pv + tota + ppn_h + total_pph;



    document.getElementsByName("ded_ad")[0].value = tota.toFixed(2);
    document.getElementsByName("ded_ad_h")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("total_h")[0].value = total.toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("total")[0].value = formatMoney(total.toFixed(2));
    // document.getElementsByName("pph_h")[0].value = (total_pph).toFixed(2);
    // document.getElementsByName("pph")[0].value = formatMoney(total_pph.toFixed(2));
    document.getElementsByName("pph_min")[0].value = (tota_pph).toFixed(2);
}
}



function modal_input_vat_baru(){ 

    var vat = 0.11; 
    //
    if ($('[name="check_vat_baru"]').is(':checked')) {          
            var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
            var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            var twot = (total_pv).toFixed(2) * vat;
            var total_h = total_pv - pph_h + twot + ded_ad;
            document.getElementsByName("ppn_h")[0].value = (twot).toFixed(2);
            document.getElementsByName("ppn")[0].value = formatMoney(twot.toFixed(2));
            document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
            document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
  
    } else {        
            var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
            var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            var total_h = total_pv - pph_h + ded_ad;

            document.getElementsByName("ppn")[0].value = "0.00";
            document.getElementsByName("ppn_h")[0].value = "0";
            document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
            document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    }
}


function changeValueTax(id){
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
    var pph = id;
    var twot2 = (total_pv).toFixed(2) * (pph /100);
    var total_h = total_pv + ppn_h - twot2 + ded_ad;
    document.getElementsByName("pph_h")[0].value = (twot2).toFixed(2);
    document.getElementsByName("pph")[0].value = formatMoney(twot2.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
}

function changeValueTax2(id){
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
    var pph = id;
    var twot2 = (total_pv).toFixed(2) * (pph /100);
    var total_h = total_pv + pph_h + twot2 + ded_ad;
    document.getElementsByName("ppn_h")[0].value = (twot2).toFixed(2);
    document.getElementsByName("ppn")[0].value = formatMoney(twot2.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total_h2")[0].value = formatMoney(total_h.toFixed(2));
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
}
  </script>

  <script type="text/javascript">
    $("input[name=ded_ad]").keyup(function(){
    var ttl_jml = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var sub = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var ppn = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var pph = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var dedad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
    valu = sub + ppn - pph + dedad;
    });

   $("#total").val(formatMoney(valu));
   $("#total_h2").val(formatMoney(valu));
   $("#total_h").val(valu);

    });
</script>

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
</script>
    

<script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var kb = parseFloat($(this).closest('tr').find('td:eq(6)').attr('data-out'),10) || 0;
    var amount = parseFloat($(this).closest('tr').find('td:eq(7) input').val(),10) || 0;
    var balance = parseFloat($(this).closest('tr').find('td:eq(6)').attr('data-out'),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(7) input');                
    if(amount > balance){
        sum_kb += kb;
        select_amount.val(balance);
        sum_amount += balance;
        sum_total = sum_kb - sum_amount;
    }else{
    sum_kb += kb;
    sum_amount += amount;
    sum_total = sum_kb - sum_amount;        
    }   
    });
    $("#subtotal").val(formatMoney(sum_kb));
    $("#pajak").val(formatMoney(sum_amount));    
    $("#total").val(formatMoney(sum_total));
    });
</script>

<!-- -->

<script type="text/javascript">
    $("input[name=amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;

    sum_amount += amount;
 
     
    });

    $("#nomrate1").val(formatMoney(sum_amount));    
    $("#nomrate2").val(formatMoney(sum_amount));    

    });
</script>


<script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValueACC(id){
    var select_rate = document.getElementById('rate');   
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    if (prdName[id].valuta == 'IDR') {
            select_rate.disabled = true;
        }else{
            select_rate.disabled = false;
        }
};
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = rate;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#rate_h").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("input[name=nominal_h]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = ttl_h;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#nominal").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){
        var valu = '';
        $("input[type=radio]:checked").each(function () {
        var data = $(this).closest('tr').find('td:eq(1) input').val();
        valu = data;
        console.log(data);
         
             
                  
        });
        $("#txt_forpay").val(valu);
 
    });


</script>


<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');

// Loop through the collection and call addListener on each element
Array.prototype.forEach.call(numInputs, addListener); 


function addListener(elm,index){
  elm.setAttribute('min', 1);  // set the min attribute on each field
  
  elm.addEventListener('keypress', function(e){  // add listener to each field 
     var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
     str = String.fromCharCode(key); 
    if (str.localeCompare('-') === 0){
       event.preventDefault();
    }
    
  });
  
}
</script>



<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc_edit.php',
            data: {'doc_number':doc_number, 'unik_code':unik_code, 'data':data},
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
        });
                // return false; 
 
    });


</script>

<!-- <script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
    });


</script> -->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_pv = document.getElementById('no_doc').value;  
        var create_user = '<?php echo $user; ?>';
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var ctb = $('select[name=ct_buyer] option').filter(':selected').val();
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val();
        var curr = document.getElementById('curre').value;
        var for_pay = $('select[name=forpay] option').filter(':selected').val();
        if (for_pay == 'Lainnya') {
         var forpay = document.getElementById('pay_for').value;
        }else{
         var forpay = $('select[name=forpay] option').filter(':selected').val();   
        }
        var total = document.getElementById('total_h').value;
        var frcc = $('select[name=frcc] option').filter(':selected').val();
        var tocc = $('select[name=tocc] option').filter(':selected').val();

        if (total >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '' || total >= '1' && curr !='' && pay_mth != '' && forpay != '-' && ctb != '' && nama_supp != '') {
        $.ajax({
            type:'POST',
            url:'copy_data.php',
            data: {'no_pv':no_pv, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                // alert(response);
                if (response == 'Draft') {
        var no_pv = document.getElementById('no_doc').value;  
        var rat_pv = document.getElementById('rat_pv').value;        
        var pv_date = document.getElementById('tgl_active').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var sup_doc = document.getElementById('sup_doc').value;        
        var ctb = $('select[name=ct_buyer] option').filter(':selected').val();    
        var pay_date = document.getElementById('tgl_pay').value;
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var curr = document.getElementById('curre').value; 
        var for_pay = $('select[name=forpay] option').filter(':selected').val();
        if (for_pay == 'Lainnya') {
         var forpay = document.getElementById('pay_for').value;
        }else{
         var forpay = $('select[name=forpay] option').filter(':selected').val();   
        }
        var frcc = $('select[name=frcc] option').filter(':selected').val();
        var tocc = $('select[name=tocc] option').filter(':selected').val();
        var no_cek = document.getElementById('no_cek').value;        
        var cek_date = document.getElementById('cek_date').value;
        var ke = document.getElementById('ke').value; 
        var dari = document.getElementById('dari').value;        
        var pesan = document.getElementById('pesan').value;
        var subtotal = document.getElementById('nomrate_h').value || 0;
        var adjust = document.getElementById('ded_ad').value;
        var pph = document.getElementById('pph_h').value;
        var ppn = document.getElementById('ppn_h').value;
        var total = document.getElementById('total_h').value;
        var pilih_ppn = '';
        var pilih_pph = '';
        var create_user = '<?php echo $user; ?>';

        if (total >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '' || total >= '1' && curr !='' && pay_mth != '' && forpay != '-' && ctb != '' && nama_supp != '') {
        $.ajax({
            type:'POST',
            url:'insertpv_h.php',
            data: {'rat_pv':rat_pv, 'no_pv':no_pv, 'pv_date':pv_date, 'nama_supp':nama_supp, 'sup_doc':sup_doc, 'ctb':ctb, 'pay_date':pay_date, 'pay_mth':pay_mth, 'curr':curr, 'forpay':forpay, 'frcc':frcc, 'tocc':tocc, 'no_cek':no_cek, 'cek_date':cek_date, 'ke':ke, 'dari':dari, 'pesan':pesan, 'subtotal':subtotal, 'adjust':adjust, 'pph':pph, 'ppn':ppn, 'total':total, 'pilih_ppn':pilih_ppn, 'pilih_pph':pilih_pph, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert(response);
                // window.location = 'payment-voucher.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 
                        
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var no_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val(); 
        var no_cc = $(this).closest('tr').find('td:eq(2)').find('select[name=nomor_cc] option').filter(':selected').val();      
        var no_ref = $(this).closest('tr').find('td:eq(3) input').val();                               
        var ref_date = $(this).closest('tr').find('td:eq(4) input').val();
        var deskripsi = $(this).closest('tr').find('td:eq(5) input').val();                               
        var amount = $(this).closest('tr').find('td:eq(6) input').val() || 0;
        var due_date = $(this).closest('tr').find('td:eq(8) input').val();
        var ded_add = $(this).closest('tr').find('td:eq(7) input').val() || 0;
        var pph = $(this).closest('tr').find('td:eq(9)').find('select[name=pphh] option').filter(':selected').val() || 0;
        var idtax = $(this).closest('tr').find('td:eq(9)').find('select[name=pphh] option').filter(':selected').attr('data-idtax');
        var ppn = $(this).closest('tr').find('td:eq(10)').find('select[name=ppnn] option').filter(':selected').val() || 0;
        var id_ppn = $(this).closest('tr').find('td:eq(10)').find('select[name=ppnn] option').filter(':selected').attr('data-idtax');
        var total_h = document.getElementById('total_h').value || 0;
        var curr = document.getElementById('curre').value; 
        var for_pay = $('select[name=forpay] option').filter(':selected').val();
        if (for_pay == 'Lainnya') {
         var forpay = document.getElementById('pay_for').value;
        }else{
         var forpay = $('select[name=forpay] option').filter(':selected').val();   
        }
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var ctb = $('select[name=ct_buyer] option').filter(':selected').val();

        if (total_h >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '' || total_h >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '') { 
        $.ajax({
            type:'POST',
            url:'insertpv.php',
            data: {'doc_number':doc_number, 'no_coa':no_coa, 'no_cc':no_cc, 'no_ref':no_ref, 'ref_date':ref_date, 'deskripsi':deskripsi, 'amount':amount, 'due_date':due_date, 'ded_add':ded_add, 'pph':pph, 'idtax':idtax, 'ppn':ppn, 'id_ppn':id_ppn},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'payment-voucher.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
    
        });
         alert("Payment Voucher Changed successfully");
         }else{
        alert("Payment Voucher Can't be Changed");
    }
                //  // alert(response);
                window.location = 'payment-voucher.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 
       if($('select[name=nama_supp] option').filter(':selected').val() == '' || $('select[name=nama_supp] option').filter(':selected').val() == '-'){
        alert("Please select Supplier");
        document.getElementById('nama_supp').focus();
        }else if(document.getElementById('sup_doc').value == ''){
        alert("Please Select Support Document");
        document.getElementById('sup_doc').focus();
        }else if($('select[name=ct_buyer] option').filter(':selected').val() == ''){
        alert("Please select Charge to Buyer");
        document.getElementById('ct_buyer').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() == '' || $('select[name=carabayar] option').filter(':selected').val() == '-'){
        alert("Please select payment method");
        document.getElementById('carabayar').focus();
        }else if(document.getElementById('curre').value == ''){
        alert("Please Enter Currency");
        document.getElementById('curre').focus();
        }else if($('select[name=forpay] option').filter(':selected').val() == '' || $('select[name=forpay] option').filter(':selected').val() == '-'){
        alert("Please select For payment");
        document.getElementById('forpay').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=forpay] option').filter(':selected').val() == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=forpay] option').filter(':selected').val() == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() != '-' && $('select[name=tocc] option').filter(':selected').val() == '-'){
        alert("Please select To Account");
        document.getElementById('tocc').focus();
        }else if(document.getElementById('total_h').value == ''){
        alert("Please Input Amount");
        }else if(document.getElementById('total_h').value <= '0'){
        alert("Amount can't be Minus");
        }else if(document.getElementById('total_h').value == '0.00'){
        alert("Total Amount can't be Zero");
        }else{               
       
            // alert("data saved successfully");
        }
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#batal", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc_edit.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
    });


</script>

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalkbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(9)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(7)').text();
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(16)').attr('value');
    var status = $(this).closest('tr').find('td:eq(17)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(18)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(15)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(19)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxkbon.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('Tgl Kontrabon : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Tgl Jatuh Tempo : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
});

</script> -->

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
