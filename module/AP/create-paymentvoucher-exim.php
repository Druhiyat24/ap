<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM PAYMENT VOUCHER MEMO EXIM</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div style="padding-left: 10px;padding-top: 5px;">
            <button style="-ms-transform: skew(10deg);-webkit-transform: skew(10deg);transform: skew(15deg);" id="btnpv" type="button" class="btn-secondary btn-xs"><span></span> Payment Voucher</button>
            <button style="-ms-transform: skew(10deg);-webkit-transform: skew(10deg);transform: skew(15deg);" id="btnpve" type="button" class="btn-primary btn-xs"><span>Payment Voucher EXIM</span></button>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Payment Voucher</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(no_pv) from tbl_pv_h where YEAR(pv_date) = YEAR(CURRENT_DATE())");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_pv)'];
            $urutan = (int) substr($kodepay, 12, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "PV/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
        </div>

            <div class="col-md-3 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Payment Voucher Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;            
            if(!empty($_POST['nama_supp'])) {
                echo $_POST['tgl_active'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>

            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
            $shuffle  = substr(str_shuffle($karakter), 0, 8);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>

            
            <div class="col-md-4 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Supplier</b></label>            
<!--               <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Supplier</option>                                                 
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
                </select> -->
                <input type="text" style="font-size: 14px;" class="form-control" id="nama_supp" name="nama_supp" readonly value="<?php 
            $sql = mysqli_query($conn2,"select DISTINCT ms.supplier supplier from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h
                    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
                    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
            $row = mysqli_fetch_array($sql);
            $supplier = isset($row['supplier']) ? $row['supplier'] : null;           
            if(!empty($supplier)) {
                echo $supplier;
            }
            else{
                echo '';
            }?>">
                </div>

                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat_pv" name="rat_pv" 
                value="<?php

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'PAJAK'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        ?>">

        



                                        
    </div>
</br>

        <div class="form-row">

<div class="col-md-4 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Supporting Document</b></label>
                <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="sup_doc" name="sup_doc" value="<?php             

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2," select GROUP_CONCAT(ket) as sup_doc from (select * from supp_doc_temp where ket != '' ) supp_doc_temp ");
            $row = mysqli_fetch_array($sql);
            $sup_doc = $row['sup_doc'];         
    
            // $top = 30;

            // if(!empty($nama_supp)) {
                
                  echo $sup_doc;  
                
            // }
            // else{
            //     echo '';
            // } ?>">
            </div>

            <div class="col-md-1 mb-3">            
            <label for="total" class="col-form-label" style="width: 200px;"><b>Select</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn5" id="btn5" data-target="#mymodal5" data-toggle="modal" value="Select"> 
            </div>

            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="ct_buyer"><b>Charge To Buyer</b></label> 
            <input type="text" readonly style="font-size: 14px;" class="form-control" id="ct_buyer" name="ct_buyer" value="<?php 
            $sql = mysqli_query($conn2,"select DISTINCT mb.supplier buyer from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h
                    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
                    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and a.ditagihkan = 'Y' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
            $row = mysqli_fetch_array($sql);
            $buyer = isset($row['buyer']) ? $row['buyer'] : null;           
            if(!empty($buyer)) {
                echo $buyer;
            }
            else{
                echo '-';
            }?>">           
            </div>

            <div class="col-md-2 mb-3" style="padding-top: 8px;">
            <label for="forpay"><b>For Payment</b></label> 
            <input type="text" readonly style="font-size: 14px;" class="form-control" id="forpay" name="forpay" value="Export - Import">           
            </div>
            <div class="col-md-2 mb-3">
            </div>
            <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Payment Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_pay" id="tgl_pay" class="form-control tanggal" 
            value="<?php 
            if(!empty($_POST['tgl_pay'])) {
                echo $_POST['tgl_pay'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>
            </div>
                <div class="col-md-3 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Pay Methods </label>               
                <select class="form-control selectpicker" name="carabayar" id="carabayar" data-live-search="true" onchange="this.form.submit()">
                    <option value="" disabled selected="true">Choose pay method</option>  
                    <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['forpay']) ? $_POST['forpay']: null;
                }                 
                $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod ");
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
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['curre']) ? $_POST['curre']: null;
                }                 
                $sql = mysqli_query($conn1,"select DISTINCT curr from b_masterbank ");
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
                <?php 
                $cb = isset($_POST['carabayar']) ? $_POST['carabayar']: null;
                if($cb != 'CASH' ){
                    echo '
                    <div class="col-md-2 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>From Account</b></label>            
              <select class="form-control selectpicker" name="frcc" id="frcc" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select Account</option>';?> 
                <?php 
                       $frcc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $frcc = isset($_POST['frcc']) ? $_POST['frcc']: null;
                }                 
                $sql = mysqli_query($conn1,"select coa_name as bank,curr,bank_account as akun from b_masterbank where status = 'Active' group by id");
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
                <?php echo'</select>
                <div style = "display : none;">
                <select class="form-control selectpicker" name="tocc" id="tocc" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>
                </select>
                </div>
                <input type="hidden" style="font-size: 14px;" class="form-control" id="no_cek" name="no_cek" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="cek_date" name="cek_date" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="ke" name="ke" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="dari" name="dari" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="pay_for" name="pay_for" value="" autocomplete = "off">
                   
            </div>
                    ';
                }
                else{

                echo '
                <div style = "display : none;">
                <select class="form-control selectpicker" name="tocc" id="tocc" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>
                </select>
                </div>
                <div style = "display : none;">
                <select class="form-control selectpicker" name="frcc" id="frcc" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>
                </select>
                </div>
                <input type="hidden" style="font-size: 14px;" class="form-control" id="ke" name="ke" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="dari" name="dari" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="no_cek" name="no_cek" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="cek_date" name="cek_date" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="pay_for" name="pay_for" value="" autocomplete = "off">';
        }?>

        <div class="col-md-1 mb-3">            
            <label for="total" class="col-form-label" style="width: 200px;"><b>Select No Memo</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn2" id="btn2" data-target="#mymodal2" data-toggle="modal" value="Select Memo"> 
            </div>
        </div>


<div class="form-row">


        <div class="col-md-10 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="2" type="text" class="form-control " name="pesan" id="pesan" value="<?php             
            if(!empty($_POST['pesan'])) {
                echo $_POST['pesan'];
            }
            else{
                echo '';
            } ?>" placeholder="Description..." ></textarea>
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
        <h4 class="modal-title" id="Heading">Choose Memo</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label style="padding-left: 10px;" for="nama_supp_memo"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp_memo" id="nama_supp_memo" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier),id_supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data2 = $row['id_supplier'];
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_memo']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                    </div>
              
                </div>

                <div class="col-md-12 mb-3">
                     <label><b>Memo Date</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date_memo" name="start_date_memo" 
                value="<?php
                $start_date_memo ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $start_date_memo = date("Y-m-d",strtotime($_POST['start_date_memo']));
                }
                if(!empty($_POST['start_date_memo'])) {
                    echo $_POST['start_date_memo'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date_memo" name="end_date_memo" 
                value="<?php
                $end_date_memo ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $end_date_memo = date("Y-m-d",strtotime($_POST['end_date_memo']));
                }
                if(!empty($_POST['end_date_memo'])) {
                    echo $_POST['end_date_memo'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Akhir">
                &nbsp;&nbsp;
                <input 
                style="border: 0;
                    line-height: 1;
                    padding: 10px 10px;
                    font-size: 1rem;
                    text-align: center;
                    color: #fff;
                    text-shadow: 1px 1px 1px #000;
                    border-radius: 6px;
                    background-color: rgb(95, 158, 160);"
                type="button" id="send2" name="send2" value="Search"> 
                </div>
                </div>  
                
                <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
  
                <div class="modal-footer">
                    <div class="col-md-2 mb-3">
                    <button type="submit" id="savememo" name="savememo" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-floppy-o" aria-hidden="true"></span>
                        Save
                    </button>
                </div>
                <div class="col-md-10 mb-3">
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
    <div class="modal fade" id="mymodal5" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form5" method="post">
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
                $nodoc = $row1['ref_doc'];

                $sql22 = mysqli_query($conn2,"select ket from supp_doc_temp where ket = '$nodoc'");
                $row22 = mysqli_fetch_array($sql22);
                $ket = isset($row22['ket']) ? $row22['ket'] : null;

                $sql23 = mysqli_query($conn2,"select ket from supp_doc_temp where ket != 'Sales Order' and ket != 'Purchase Order' and ket != 'PEB' and ket != 'Invoice'");
                $row23 = mysqli_fetch_array($sql23);
                $ket2 = isset($row23['ket']) ? $row23['ket'] : null;
                
                    echo '<tr>'; 
                    if ($ket != '') {
                         echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>'; 
                     } else{
                    echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>'; 
                    }                        
                            echo '<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>';
                   echo '<tr>'; 
                    if ($ket2 != '') {
                         echo'<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$ket2.'" >
                            </td>                                                                                                 
                        </tr>'; 
                     } else{
                    echo'<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
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
                    <button type="submit" id="send5" name="send5" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
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
        
            <div class="col-md-12" style="overflow-x:auto; width:100%;">

            <table id="mytable" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr><th class="text-center" style="width: 2%">-</th>
            <th class="text-center" style="width: 16%">No Memo</th>
            <th class="text-center" hidden>Jenis Transaksi</th>
            <th class="text-center" hidden>Ditagihkan</th>
            <th class="text-center" hidden>Kategori</th>
            <th class="text-center" hidden>Sub Kategori</th>
            <th class="text-center" hidden>Item</th>
            <th class="text-center" style="width: 8%">COA</th>
            <th class="text-center" style="width: 8%">Cost Center</th>
            <th class="text-center" style="width: 21%">Description</th>
            <th class="text-center" style="width: 8%">Amount</th>
            <th class="text-center" style="width: 8%">Deduction</th>
            <th class="text-center" style="width: 9%">Due date</th>
            <th class="text-center" style="width: 9%">PPH</th>
            <th class="text-center" style="width: 9%">PPN</th>
            <th class="text-center" style="width: 2%">Action</th>
        </tr>
    </thead>
    
    <tbody id="tbody2">
       
        <?php
    // $sqlpv = mysql_query("select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan,mdet.nm_ctg,mdet.nm_sub_ctg,it.item_name,map.no_coa, CONCAT( map.no_coa, ' ', map.nama_coa) nama_coa, map.id_cc,map.cc_name, UPPER(CONCAT(mdet.nm_sub_ctg,' (',ms.supplier, '), BUYER ',mb.supplier, ', ',a.jns_trans, ', ',inv_vendor)) keterangan,mdet.biaya  from memo_h a
    //       inner join mastersupplier ms on a.id_supplier = ms.id_supplier
    //       inner join mastersupplier mb on a.id_buyer = mb.id_supplier
    //       inner join memo_det mdet on mdet.id_h = a.id_h
    //                 left join master_memo_item it on it.id = a.id_item
    //                 inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
    //                 left join memo_mapping map on map.id_ctg = mdet.id_ctg and map.id_sub_ctg = mdet.id_sub_ctg and 
    //                 map.jns_trans = a.jns_trans and map.ditagihkan = a.ditagihkan or map.id_item = a.id_item
    //       where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and mtemp.user = '$user'
    //       GROUP BY mdet.id order by mdet.id_h",$conn1);

//     (select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan,mdet.nm_ctg,mdet.nm_sub_ctg,it.item_name,map.no_coa, CONCAT( map.no_coa, ' ', map.nama_coa) nama_coa, map.id_cc,map.cc_name, UPPER(CONCAT(mdet.nm_sub_ctg,' (',ms.supplier, '), BUYER ',mb.supplier, ', ',a.jns_trans, ', ',inv_vendor)) keterangan,mdet.biaya  from memo_h a
//            inner join mastersupplier ms on a.id_supplier = ms.id_supplier
//            inner join mastersupplier mb on a.id_buyer = mb.id_supplier
//            inner join memo_det mdet on mdet.id_h = a.id_h
//            left join master_memo_item it on it.id = a.id_item
//            inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
//            left join memo_mapping_v2 map on map.id_ctg = mdet.id_ctg and map.id_sub_ctg = mdet.id_sub_ctg and 
//            map.jns_trans = a.jns_trans and map.ditagihkan = a.ditagihkan or map.id_item = a.id_item
//            where mdet.cancel = 'N' and map.status != 'Y' and mtemp.user = '$user'
//            GROUP BY mdet.id order by mdet.id_h)
// UNION
// (select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan, '' nm_ctg,'' nm_sub_ctg,'' item_name,lj.no_coa, CONCAT( lj.no_coa, ' ', lj.nama_coa) nama_coa, lj.no_costcenter id_cc,lj.nama_costcenter cc_name,lj.keterangan,lj.credit biaya  from memo_h a
//   inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
//     inner join tbl_list_journal lj on lj.no_journal = a.nm_memo
//   where lj.credit != 0 and mtemp.user = '$user')

    $sql_jurnal = mysqli_query($conn2,"select no_journal from tbl_list_journal a inner join tbl_pv_memo_temp b on b.no_memo = a.no_journal where b.user = '$user' limit 1");
    $hasil = mysqli_fetch_array($sql_jurnal);
    $no_journal = isset($hasil['no_journal']) ?  $hasil['no_journal'] : null;

    if ($no_journal != null) {
        $sqlpv = mysql_query("select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan, '' nm_ctg,'' nm_sub_ctg,'' item_name,lj.no_coa, CONCAT( lj.no_coa, ' ', lj.nama_coa) nama_coa, lj.no_costcenter id_cc,lj.nama_costcenter cc_name,lj.keterangan,lj.credit biaya  from memo_h a
  inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
    inner join tbl_list_journal lj on lj.no_journal = a.nm_memo
  where lj.credit != 0 and mtemp.user = '$user' order by a.nm_memo asc",$conn1);
    }else{
        $sqlpv = mysql_query("select id_h,nm_memo,tgl_memo,jns_trans, ditagihkan,nm_ctg,nm_sub_ctg,item_name,no_coa, nama_coa, id_cc,cc_name, keterangan,sum(biaya ) biaya from (select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan,mdet.nm_ctg,mdet.nm_sub_ctg,it.item_name,map.no_coa, CONCAT( map.no_coa, ' ', map.nama_coa) nama_coa, map.id_cc,map.cc_name, UPPER(CONCAT(mdet.nm_sub_ctg,' (',ms.supplier, '), BUYER ',mb.supplier, ', ',a.jns_trans, ', ',inv_vendor)) keterangan,mdet.biaya from memo_h a
           inner join mastersupplier ms on a.id_supplier = ms.id_supplier
           inner join mastersupplier mb on a.id_buyer = mb.id_supplier
           inner join memo_det mdet on mdet.id_h = a.id_h
           left join master_memo_item it on it.id = a.id_item
           inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
           left join memo_mapping_v2 map on map.id_ctg = mdet.id_ctg and map.id_sub_ctg = mdet.id_sub_ctg and 
           map.jns_trans = a.jns_trans and map.ditagihkan = a.ditagihkan or map.id_item = a.id_item
           where mdet.cancel = 'N' and map.status != 'Y' and mtemp.user = '$user'
           GROUP BY mdet.id order by mdet.id_h) a GROUP BY nm_memo,nm_ctg,nm_sub_ctg",$conn1);
    }

        $id = 1;
     while($row = mysql_fetch_array($sqlpv)){
                    $reff_date = $row['tgl_memo'];
                    $amount = $row['biaya'];
                    $coa_memo = $row['no_coa'];
                    $cc_memo = $row['id_cc'];
                    $ded_add = 0;
                    if ($reff_date == '' || $reff_date == '1970-01-01') { 
                        $reffdate = '';
                    }else{
                        $reffdate = date("d-m-Y",strtotime($row['tgl_memo'])); 
                    } 

    echo'<tr>
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td >
                <input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_memo'].'" autocomplete="off" >
            </td>
            <td hidden>
                <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['jns_trans'].'" autocomplete="off" >
            </td>
            <td hidden>
                <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['ditagihkan'].'" autocomplete="off" >
            </td>
            <td hidden>
                <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_ctg'].'" autocomplete="off" >
            </td>
            <td hidden>
                <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_sub_ctg'].'" autocomplete="off" >
            </td>
            <td hidden>
                <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['item_name'].'" autocomplete="off" >
            </td>
            <td>
                <select style="font-size: 12px;" class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-width="80px" data-live-search="true" data-size="5"> <option value="'.$row['no_coa'].'" >'.$row['nama_coa'].'</option><option value="-" > - </option>';  $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa != '$coa_memo' order by no_coa asc"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                <?php
                echo '
                </select>
            </td>
            <td >
                <select style="font-size: 12px;" class="form-control selectpicker" name="nomor_cc" id="nomor_cc" data-width="80px" data-live-search="true" data-size="5"> <option value="'.$row['id_cc'].'" >'.$row['cc_name'].'</option>';  $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc where status = 'Active' and no_cc != '$cc_memo'"); foreach ($sql2 as $ccs) : echo'<option value="'.$ccs["code_combine"].'"> '.$ccs["cost_name"].' </option>'; endforeach; ?>
                <?php
                echo '
                </select>
            </td>
            <td>
                <textarea style="font-size: 12px" type="text" class="form-control" name="keterangan[]" value="'.$row['keterangan'].'" placeholder="" autocomplete="off">'.$row['keterangan'].'</textarea>
            </td>';
            if ($amount == '0') {
                echo '<td>
                <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off" readonly>
            </td>';
            }else{
            echo '<td>
                <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="'.$row['biaya'].'" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>';
        }

        if ($ded_add == '0') {
                echo '<td>
                <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off" readonly>
            </td>';
            }else{
            echo '<td>
                <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off">
            </td>';
        }
            echo '
            <td>
                <input  type="text" style="font-size: 12px;" name="tgl_tempo" id="tgl_tempo" value="" class="form-control tanggal" 
         autocomplete="off" placeholder="dd-mm-yyyy" >
            </td>
            <td>
                <select style = "font-size: 12px;" class="form-control" name="pphh" id="pphh"  onchange="input_pph()" data-width="80px" data-live-search="true" data-size="5"> <option data-idtax="0" value="0" > Non PPH </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                <?php echo'</select>
            </td>

            <td >
                <select style = "font-size: 12px;" class="form-control" name="ppnn" id="ppnn'.$id.'"  onchange="input_ppn()" data-width="80px" data-live-search="true" data-size="5"> <option data-idtax="" value="" > Non PPN </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                <?php echo'</select>
            </td>

            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value="" disabled></td>
        </tr>';
$id++;
}
?>
<!-- id="pphh'.$id.'" -->
    </tbody>
    <tfoot>
          <tr>
            <td colspan="16" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Interject Row</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')">Delete Row</button>
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
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>PPN</b></label>
                 <select class="form-control" name="pilih_ppn" id="pilih_ppn" data-live-search="true" onchange='changeValueTax2(this.value)' required>
                <option value="" disabled selected="true">Select PPN</option>  
                <?php 
                        $sqlacc = mysqli_query($conn1,"select '0' idtax, 'Non PPN' kriteria, '0' percentage, 'Non PPN' kriteria2
                                                        union
                                                        select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN'  GROUP BY idtax");
                         $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $idtax = $row['idtax'];
                            $data = $row['percentage'];
                            $data2 = $row['kriteria2'];
                            if($row['kriteria2'] == $_POST['pilih_ppn']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="pilih_ppn" value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>'; 
                            $jsArray .= "prdName['" . $row['percentage'] . "'] = {idtax:'" . addslashes($row['idtax']) . "'};\n";   
                            
                        }
                        ?>
                </select>
                <input type="hidden" name="idtax" id="idtax" value="0">
            </div>
            </br>
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
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate1" name="nomrate1" placeholder="0.00" readonly value="<?php 
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];           
            if(!empty($biaya)) {
                echo number_format($biaya,2);
            }
            else{
                echo '';
            }?>">
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="<?php 
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];           
            if(!empty($biaya)) {
                echo $biaya;
            }
            else{
                echo '';
            }?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Deduction</b></label>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="ded_ad" name="ded_ad" placeholder="0.00" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="ded_ad_h" name="ded_ad_h" placeholder="0.00" readonly>
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Incoming Tax</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="pph" name="pph" placeholder="0.00" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="">
                <input type="hidden" name="pph_min" id="pph_min" value="">
                <input type="hidden" name="pph_plus" id="pph_plus" value="">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Value Added Tax</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="ppn" name="ppn" placeholder="0.00" readonly>
                <input type="hidden" name="ppn_h" id="ppn_h" value="">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="total" name="total" placeholder="0.00" readonly value="<?php 
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];           
            if(!empty($biaya)) {
                echo number_format($biaya,2);
            }
            else{
                echo '';
            }?>">
                <input type="hidden" name="total_h" id="total_h" value="<?php 
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];           
            if(!empty($biaya)) {
                echo $biaya;
            }
            else{
                echo '';
            }?>">
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
  <script language="JavaScript" src="../css/4.1.1/sweetalert2.all.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/sweetalert2.min.js"></script>
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

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_pph').select2({
            dropdownAutoWidth : true
        });

        $('.select2_ppn').select2({
            dropdownAutoWidth : true
        });
    });
</script>


<!-- <?php
for ($x = 1; $x <= 50; $x++) {
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa<?= $x?>').select2({
            dropdownAutoWidth : true
        });
    });
</script>
<?php } ?> -->
<!-- <script type="text/javascript">
        $(document).ready(function(){
            $("#btn2").click(function(){
                Swal.fire({
                  type: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong!',
                  footer: ''
                });
            });
            });
</script> -->

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa').select2({
            dropdownAutoWidth : true
        });
        $('.select2_coa2').select2({
            dropdownAutoWidth : true
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

<script>
    $(document).ready(function() {
    $('#table-memo').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    
   // JavaScript Document
function addRow(tableID) {
    var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);

$(function() {
    $('.selectpicker').selectpicker();
});
$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});

 $coa = '';
 var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td style="width: 50px"><select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="80px" data-size="5"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?> <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?> </option><?php endforeach; ?></select></td><td ><select class="form-control selectpicker" name="nomor_cc" id="nomor_cc" data-live-search="true" data-width="80px" data-size="5"> <option value="-" > - </option><?php $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc where status = 'Active'"); foreach ($sql2 as $cc) : ?> <option value="<?= $cc["code_combine"]; ?>"><?= $cc["cost_name"]; ?> </option><?php endforeach; ?></select></td><td><textarea style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></textarea></td><td><input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off"></td><td><input  type="text" style="font-size: 12px;" name="tgl_tempo" id="tgl_tempo" value="" class="form-control tanggal" autocomplete="off" placeholder="dd-mm-yyyy" ></td><td style="width: 50px"><select class="form-control" name="pphh" id="pphh" data-live-search="true" onchange="input_pph()" data-width="80px" data-size="5"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax"); foreach ($sql as $pph) : ?> <option data-idtax="<?= $pph["idtax"]; ?>" value="<?= $pph["percentage"]; ?>"><?= $pph["kriteria2"]; ?> </option><?php endforeach; ?></select></td></td><td style="width: 50px"><select class="form-control" name="ppnn" id="ppnn" data-live-search="true" onchange="input_ppn()" data-width="80px" data-size="5"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax"); foreach ($sql as $ppn) : ?> <option data-idtax="<?= $ppn["idtax"]; ?>" value="<?= $ppn["percentage"]; ?>"><?= $ppn["kriteria2"]; ?> </option><?php endforeach; ?></select></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';


 row.innerHTML = element1;    
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
                var chkbox = row.cells[15].childNodes[0];
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
    var h_ppn = document.getElementById('pilih_ppn').value || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            for (var i = 0; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;
    var price2 = document.getElementById("tbody2").rows[i].cells[11].children[0].value || 0;
    var pph = document.getElementById("tbody2").rows[i].cells[13].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[14].children[0].value || 0;

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
                                            newCell.children[i2].checked[15] = true;
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
            for (var i = 0; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[10].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[11].children[0].value;
    var pph = document.getElementById("tbody2").rows[i].cells[13].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[14].children[0].value || 0;

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
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    // alert(pph);
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
    $('#pilih_ppn').prop('disabled', false);
            for (var i = 0; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[10].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[11].children[0].value;
    var ppn = document.getElementById("tbody2").rows[i].cells[14].children[0].value || 0;
    var pph = document.getElementById("tbody2").rows[i].cells[13].children[0].value || 0;

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
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    if (ppn > 0) {

    $('#pilih_ppn').prop('disabled', true);
    $('#pilih_ppn').val('');
    }
}
}


function getdate() {
    var pay_date = document.getElementById('tgl_pay').value;
    var table = document.getElementById("tbody2");
    for (var i = 0; i < (table.rows.length); i++) {

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
    var ppn = parseFloat(document.getElementById('pilih_ppn').value,10) || 0;    
    var table = document.getElementById("tbody2");
    var tota = 0;
    var tota_pph = 0;
    var total_pph = 0;
    var tota_ppn = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 0; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[10].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[11].children[0];
    var pph = document.getElementById("tbody2").rows[i].cells[13].children[0].value || 0;
    var ppn = document.getElementById("tbody2").rows[i].cells[14].children[0].value || 0;

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
    console.log(price);
    tota_ppn += parseFloat(harga) * (ppn/100);
    totall = tota + ded_ad + tota_ppn - total_pph;



    document.getElementsByName("nomrate_h")[0].value = tota.toFixed(2);
    document.getElementsByName("nomrate1")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("total_h")[0].value = totall.toFixed(2);
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
            for (var i = 0; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[11].children[0].value;
    var price_amt = document.getElementById("tbody2").rows[i].cells[10].children[0];
    var pph = document.getElementById("tbody2").rows[i].cells[13].children[0].value;

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
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
  
    } else {        
            var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
            var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            var total_h = total_pv - pph_h + ded_ad;

            document.getElementsByName("ppn")[0].value = "0.00";
            document.getElementsByName("ppn_h")[0].value = "0";
            document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
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
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
}

function changeValueTax2(id){
    document.getElementById('idtax').value = prdName[id].idtax;
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
    var pph = id;
    var twot2 = (total_pv).toFixed(2) * (pph /100);
    var total_h = total_pv + pph_h + twot2 + ded_ad;
    document.getElementsByName("ppn_h")[0].value = (twot2).toFixed(2);
    document.getElementsByName("ppn")[0].value = formatMoney(twot2.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
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
    var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(6) input');                
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
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

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
        var id_supp = $('select[name=nama_supp_memo] option').filter(':selected').val(); 
        var start_date = document.getElementById('start_date_memo').value;
        var end_date = document.getElementById('end_date_memo').value;        
         
             
        $.ajax({
            type:'POST',
            url:'cari_memo.php',
            data: {'id_supp':id_supp, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#details').html(data);  
                // console.log(response);
                // $('#modal-form2').modal('toggle');
                // // $('#modal-form2').modal('hide');
                //  // alert("Data saved successfully");
                // window.location.reload(false);
                // return false; 
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
    $("#modal-form5").on("click", "#send5", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;        
        var data = $(this).closest('tr').find('td:eq(7) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc.php',
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

<script>
    $("#modal-form2").on("click", "#savememo", function(){
        $("input[type=checkbox]:checked").each(function () {
        var no_memo = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_memo = $(this).closest('tr').find('td:eq(2)').attr('value');
        var jenis_transaksi = $(this).closest('tr').find('td:eq(3)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(4)').attr('value');
        var biaya = $(this).closest('tr').find('td:eq(5)').attr('value');
        var user = '<?php echo $user ?>';
             
        $.ajax({
            type:'POST',
            url:'insert_memo_temp.php',
            data: {'no_memo':no_memo, 'tgl_memo':tgl_memo,'jenis_transaksi':jenis_transaksi, 'supplier':supplier, 'biaya':biaya, 'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                // return false; 

            },
            success: function(response){
                // console.log(response);
                $('#modal-form2').modal('toggle');
                $('#modal-form2').modal('hide');
                 // alert(response);
                // window.location.reload(false);
                // return false; 
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

<script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        var user = '<?php echo $user ?>';        
         
             
        $.ajax({
            type:'POST',
            url:'hapus_memo_temp.php',
            data: {'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $('#mytable tbody tr').remove(); 
                $('#nomrate1').val("");
                $('#nomrate_h').val("");
                $('#total').val("");
                $('#total_h').val("");

                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
 
    });


</script> 

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_pv = document.getElementById('no_doc').value;  
        var rat_pv = document.getElementById('rat_pv').value;        
        var pv_date = document.getElementById('tgl_active').value;
        var nama_supp = document.getElementById('nama_supp').value;  
        var sup_doc = document.getElementById('sup_doc').value;        
        var ctb = document.getElementById('ct_buyer').value;    
        var pay_date = document.getElementById('tgl_pay').value;
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var curr = document.getElementById('curre').value; 
        var forpay = document.getElementById('forpay').value;   
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
        var pilih_ppn = document.getElementById('pilih_ppn').value;
        var pilih_pph = document.getElementById('pilih_pph').value;
        var create_user = '<?php echo $user; ?>';

        if (total >= '1' && curr !='' && pay_mth != '' && forpay != '' && pay_mth == 'CASH' && ctb != '' && nama_supp != '' || total >= '1' && curr !='' && forpay != '' && pay_mth != '' && pay_mth != 'CASH' && ctb != '' && nama_supp != '' && frcc != '') {
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
                //  // alert(response);
                // window.location = 'payment-voucher.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        
                        

        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var no_coa = $(this).closest('tr').find('td:eq(7)').find('select[name=nomor_coa] option').filter(':selected').val(); 
        var no_cc = $(this).closest('tr').find('td:eq(8)').find('select[name=nomor_cc] option').filter(':selected').val();      
        var no_ref = $(this).closest('tr').find('td:eq(1) input').val();                               
        var ref_date = '';
        var deskripsi = $(this).closest('tr').find('td:eq(9) textarea').val();                               
        var amount = $(this).closest('tr').find('td:eq(10) input').val() || 0;
        var due_date = $(this).closest('tr').find('td:eq(12) input').val();
        var ded_add = $(this).closest('tr').find('td:eq(11) input').val() || 0;
        var pph = $(this).closest('tr').find('td:eq(13)').find('select[name=pphh] option').filter(':selected').val() || 0;
        var idtax = $(this).closest('tr').find('td:eq(13)').find('select[name=pphh] option').filter(':selected').attr('data-idtax');
        var ppn = $(this).closest('tr').find('td:eq(14)').find('select[name=ppnn] option').filter(':selected').val() || document.getElementById('pilih_ppn').value;
        var id_ppn = $(this).closest('tr').find('td:eq(14)').find('select[name=ppnn] option').filter(':selected').attr('data-idtax') || document.getElementById('idtax').value;
        var total_h = document.getElementById('total_h').value || 0;
        var curr = document.getElementById('curre').value; 
        var forpay = document.getElementById('forpay').value;
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var nama_supp = document.getElementById('nama_supp').value;
        var ctb = document.getElementById('ct_buyer').value;
        var user = '<?php echo $user; ?>';

        if (total_h >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '' || total_h >= '1' && curr !='' && pay_mth != '' && forpay != '' && ctb != '' && nama_supp != '') { 
        $.ajax({
            type:'POST',
            url:'insertpv.php',
            data: {'doc_number':doc_number, 'no_coa':no_coa, 'no_cc':no_cc, 'no_ref':no_ref, 'ref_date':ref_date, 'deskripsi':deskripsi, 'amount':amount, 'due_date':due_date, 'ded_add':ded_add, 'pph':pph, 'idtax':idtax, 'ppn':ppn, 'id_ppn':id_ppn, 'user':user},
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
    }
       if(document.getElementById('nama_supp').value == '' || document.getElementById('nama_supp').value == '-'){
        alert("Please select Supplier");
        document.getElementById('nama_supp').focus();
        }else if(document.getElementById('sup_doc').value == ''){
        alert("Please Select Support Document");
        document.getElementById('sup_doc').focus();
        }else if(document.getElementById('ct_buyer').value == ''){
        alert("Please select Charge to Buyer");
        document.getElementById('ct_buyer').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() == '' || $('select[name=carabayar] option').filter(':selected').val() == '-'){
        alert("Please select payment method");
        document.getElementById('carabayar').focus();
        }else if(document.getElementById('curre').value == ''){
            alert("Please select currency");
        document.getElementById('curre').focus();
        }else if(document.getElementById('forpay').value == '' || document.getElementById('forpay').value == '-'){
        alert("Please select For payment");
        document.getElementById('forpay').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=frcc] option').filter(':selected').val() == ''){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && document.getElementById('forpay').value == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        alert("Please select From Account");
        document.getElementById('frcc').focus();
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && document.getElementById('forpay').value == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() != '-' && $('select[name=tocc] option').filter(':selected').val() == '-'){
        alert("Please select To Account");
        document.getElementById('tocc').focus();
        }else if(document.getElementById('total_h').value == ''){
        alert("Please Input Amount");
        }else if(document.getElementById('total_h').value <= '0'){
        alert("Amount can't be Minus");
        }else if(document.getElementById('total_h').value == '0.00'){
        alert("Total Amount can't be Zero");
        }else{               
       
            alert("data saved successfully");
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


</script>

<script type="text/javascript">
    document.getElementById('btnpv').onclick = function () {
    location.href = "create-paymentvoucher.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnpve').onclick = function () {
    location.href = "create-paymentvoucher-exim.php";
};
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
