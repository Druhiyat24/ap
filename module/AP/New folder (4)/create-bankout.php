<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM OUTGOING BANK</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Doc Number</b></label>
                <?php
            $sqlx = mysqli_query($conn2,"select max(id) as id FROM b_bankout_h ");
            $rowx = mysqli_fetch_array($sqlx);
            $maxid = $rowx['id'];

            $sql = mysqli_query($conn2,"select max(no_bankout) from b_bankout_h where id = '$maxid'");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_bankout)'];
            $urutan = (int) substr($kodepay,20,5);
            $urutan ++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "BK//NAG/$bln$thn";
            $kodepay = $huruf;

            echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_urut" name="no_urut" value="'.sprintf("%05s", $urutan).'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="bulan" name="bulan" value="'.$bln.'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="tahun" name="tahun" value="'.$thn.'" hidden>'
            ?>
        </div>

            <div class="col-md-3 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php echo date("d-m-Y"); ?>" autocomplete='off'>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Reference</b></label>            
              <select class="form-control selectpicker" name="ref_num" id="ref_num" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                 <option value="" disabled selected="true">Select Reference</option>
                <?php
                $ref_num ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                }                 
                
                   $sql = mysqli_query($conn1,"select ref_doc,ref_doc2 from tbl_ref where ket = 'bankout'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
                    $data2 = $row['ref_doc2'];
                    if($row['ref_doc'] == $_POST['ref_num']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
                </select>
            </div>
    </div>

    <form id="modal-form" method="post">
                <div class="form-row">
                 
                <div class="col-md-4">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="" disabled selected="true">Select Supplier</option>                                                
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
                <div class="col-md-2 mb-3">  
                 <input type="hidden" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                echo $ref_num; 
            ?>">    
                <label><b>Reff Date</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date" name="start_date" 
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

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date" name="end_date" 
                value="<?php
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $end_date = date("Y-m-d",strtotime($_POST['start_date']));
                }
                if(!empty($_POST['end_date'])) {
                    echo $_POST['end_date'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Akhir">
                </div>  
                <div style="padding-top: 30px; padding-left: 10px;">
            <button style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="submit" id="send" name="send" class="btn btn-primary btn-lg" style="width: 100%;"><span class="fa fa-search"></span>
                
                </div> 
         
                
              
 
         
                </div>           
            </form>
    <div class="form-row">

              <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Payment') {
                    echo'<div class="col-md-3">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control selectpicker" name="accountid" id="accountid" data-live-search="true"'; echo"onchange='changeValueACC(this.value)'"; echo'required >
                <option value="" disabled selected="true">Select Account</option>';?>  
                <?php 
                        $sqlacc = mysqli_query($conn1,"select upper(bank_name) as nama_bank,curr,bank_account as no_rek,RIGHT(bank_account,4) as kode from b_masterbank where status = 'Active' group by id");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_rek'];
                            if($row['no_rek'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['no_rek'] . "'] = {nama_bank:'" . addslashes($row['nama_bank']) . "',kode:'" . addslashes($row['kode']) . "',valuta:'".addslashes($row['curr'])."'};\n";
                        }
                        
                echo '</select>

             </div>';
         }else{
            echo '<div class="col-md-3">
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Account </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="accountid" name="accountid" readonly > 
                </div>';
             }?>
            </br>
           
                <div class="col-md-3">
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Bank </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="col-md-3">
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Currency </b></label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >  
                    <input type="text" style="font-size: 12px;" class="form-control" id="kode" name="kode" readonly hidden >         
                </div>
                </div>
                <div class="form-row">
                <div class="col-md-3">
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>
                 <div class="input-group" >
               <input type="number" min="0" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h" name="nominal_h" placeholder="input nominal..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </div>
            <div class="col-md-3">
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Rate </b></label>
                 <div class="input-group" >
                <input type="number" min="0" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." >
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly="">
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat" name="rat" 
                value="<?php

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        ?>">
            </div>
            </div>
            <div class="col-md-3">
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Equivalent IDR</b></label>
                 <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h">
            </div>
            </div>
            <div class="col-md-9">
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="40" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                </div>
            
         </div>
         
</br>
         </div>

    <div class="form-row">

<!--            <div class="col-md-3 mb-3">            
            <label for="txt_pph"><b>PPh (%) </b></label>            
            <input type="text" style="font-size: 14px;" class="form-control" id="txt_pph" name="txt_pph" 
            value="<?php 
            //if(!empty($_POST['txt_pph'])){
            //    echo $_POST['txt_pph'];
            //}else{
            //    echo '';
            //}
            ?>">
            </div>-->                        


    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
                <div class="form-row">
                 <div class="col-md-4">
            <label for="nama_supp"><b>Supplier</b></label>            
              <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">
        </div>
         <div class="col-md-4" style="padding-left: 150px;">

            <label for="nama_supp"><b>Reference</b></label>            
              <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                echo $ref_num; 
            ?>">
        </div>
    </div>

                <label><b>Reference Date</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date" name="start_date" 
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

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date" name="end_date" 
                value="<?php
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $end_date = date("Y-m-d",strtotime($_POST['start_date']));
                }
                if(!empty($_POST['end_date'])) {
                    echo $_POST['end_date'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Akhir">
                </div>  
                <div class="modal-footer">
                    <button type="submit" id="send" name="send" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>


        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>
    </div>                  
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <?php
                        $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;;
                if ($ref_num == 'Payment') {
                    echo '<tr>
                            <th style="width:10px;">Check</th>
                            <th style="width:50px;">No Payment</th>
                            <th style="width:100px;">Payment Date</th>                                                            
                            <th style="width:100px;">Due Date</th>
                            <th style="width:50px;">DPP</th>
                            <th style="width:100px;">PPN</th>                                                            
                            <th style="width:100px;">PPH</th>
                            <th style="width:100px;">Total</th>
                            <th style="display: none">Total</th>
                        </tr>
                        </thead>

            <tbody>';
                }elseif ($ref_num == 'Payment Voucher') {
                    echo '<tr>
                            <th style="width:10px;">Check</th>
                            <th style="width:50px;">No PV</th>
                            <th style="width:100px;">PV Date</th>                                                            
                            <th style="width:100px;">Due Date</th>
                            <th style="width:50px;">DPP</th>
                            <th style="width:100px;">PPN</th>                                                            
                            <th style="width:100px;">PPH</th>
                            <th style="width:100px;">Total</th>
                            <th style="display: none">PPH</th>
                            <th style="display: none">Total</th>
                            <th style="display: none">PPH</th>
                            <th style="display: none">Total</th>
                            <th style="display: none">Total</th>
                        </tr>
                        </thead>

            <tbody>';
                }else{
                    echo '<tr>
                            <th style="width:10px;">Check</th>
                            <th style="width:100px;">Coa</th>
                            <th style="width:100px;">Cost Center</th>                                                           
                            <th style="width:100px;">Buyer</th>
                            <th style="width:100px;">WS</th>
                            <th style="width:50px;">Currency</th>
                            <th style="width:100px;">Debit</th>                                                           
                            <th style="width:100px;">Credit</th>
                            <th style="width:100px;">Description</th>
                            <th style="width:8px;">cek</th>
                        </tr>
                    </thead>

            <tbody id="tbody2">';
                }
                    ?>
                    
            <?php
            $start_date ='';
            $end_date ='';
            $sub = '';
            $tax = '';
            $total = '';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
            }

            if ($ref_num == 'Payment') {
                $sql = mysqli_query($conn2,"select curr,nama_supp,no_payment,tgl_payment,tgl_tempo,curr,sum(total_kbon) as total_kbon,sum(pph_value) as pph_value, 'REG' as kode from list_payment where status_int = '4' and nama_supp = '$nama_supp' and tgl_payment BETWEEN '$start_date' and '$end_date' group by no_payment
union
select list_payment_cbd.curr as valuta, list_payment_cbd.nama_supp as supplier, list_payment_cbd.no_payment as no_pay, list_payment_cbd.tgl_payment as tgl_pay, list_payment_cbd.tgl_tempo,list_payment_cbd.curr as valuta, list_payment_cbd.total_kbon as total, Kontrabon_cbd.pph_value as pph, 'CBD' as kode from list_payment_cbd inner join kontrabon_cbd on kontrabon_cbd.no_kbon = list_payment_cbd.no_kbon where list_payment_cbd.nama_supp = '$nama_supp' and list_payment_cbd.status_int = '4' and total_kbon != 0 and tgl_payment BETWEEN '$start_date' and '$end_date' group by no_pay
union
select list_payment_dp.curr as valuta, list_payment_dp.nama_supp as supplier, list_payment_dp.no_payment as no_pay, list_payment_dp.tgl_payment as tgl_pay, list_payment_dp.tgl_tempo, list_payment_dp.curr as valuta, list_payment_dp.total_kbon as total,  list_payment_dp.pph_value, 'DP' as kode from list_payment_dp  where list_payment_dp.nama_supp = '$nama_supp' and list_payment_dp.status_int = '4' and total_kbon != 0 and tgl_payment BETWEEN '$start_date' and '$end_date' group by no_pay");
            }elseif ($ref_num == 'Payment Voucher') {
                $sql = mysqli_query($conn2,"select a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr,a.subtotal, a.ppn, a.pph ,a.total,a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun where a.nama_supp = '$nama_supp' and a.pv_date BETWEEN '$start_date' and '$end_date' and a.status = 'Approved' and a.outstanding != '0' group by a.no_pv");
            }else{
                '';
            }

                    if ($ref_num == 'Payment Voucher') {  
                    $total_idr = 0;  
            while($row = mysqli_fetch_array($sql)){
                            $total=$row['total'];
                            $curr=$row['curr'];
                            $pv_date=$row['pv_date'];
                            
                            $sqlz = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$pv_date' and v_codecurr = 'HARIAN'");
                            $rowz = mysqli_fetch_array($sqlz);
                            $rates = isset($rowz['rate']) ? $rowz['rate'] : 0;

                            if ($rates == '0') {
                                $sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                                $rowx = mysqli_fetch_array($sqlx);
                                $maxid = $rowx['id'];

                                $sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                                $rowy = mysqli_fetch_array($sqly);
                                $rate = $rowy['rate'];
                            }else{
                                $rate = $rowz['rate'];
                            }

                            if ($curr == 'USD') {
                                $total_idr = $total * $rate;
                            }else{
                                $total_idr = $total;
                            }
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value-nopay="'.$row['no_pv'].'">'.$row['no_pv'].'</td>
                            <td style="width:100px;" value-tanggal="'.$row['pv_date'].'">'.date("d-M-Y",strtotime($row['pv_date'])).'</td>
                            <td style="width:100px;" value-tempo="'.$row['due_date'].'">'.date("d-M-Y",strtotime($row['due_date'])).'</td>
                            <td style="width:100px;" value-sub="'.$row['subtotal'].'">'.number_format($row['subtotal'],2).'</td>
                            <td style="width:100px;" value-ppn="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>
                            <td style="width:100px;" value-pph="'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
                            <td style="width:100px;" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="display: none;" data-total-idr="'.$total_idr.'">'.number_format($total_idr,2).'</td>
                            <td style="display: none;" value-curr="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="display: none;" value-akun="'.$row['frm_akun'].'">'.$row['frm_akun'].'</td>
                            <td style="display: none;" value-bank="'.$row['bank_name'].'">'.$row['bank_name'].'</td>
                            <td style="display: none;" value-code="'.$row['b_code'].'">'.$row['b_code'].'</td>
                 
                        </tr>';
                      } 
                      }elseif ($ref_num == 'Payment'){
                            $total_idr = 0;
                         while($row = mysqli_fetch_array($sql)){
                            $no_pay=$row['no_payment'];
                            $total=$row['total_kbon'];
                            $pph=$row['pph_value'];
                            $curr=$row['curr'];
                            $kode=$row['kode'];
                            $tgl_payment=$row['tgl_payment'];
                            
                            $sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$tgl_payment' and v_codecurr = 'HARIAN'");
                            $rowy = mysqli_fetch_array($sqly);
                            $rate = isset($rowy['rate']) ? $rowy['rate'] : 1;

                            if ($curr == 'USD') {
                                $total_idr = $total * $rate;
                            }else{
                                $total_idr = $total;
                            }

                            if ($kode != 'REG') {
                            $sub = $total;
                            $ppn = 0; 
                            }else{

                            $querys = mysqli_query($conn2,"select sum(a.subtotal) as sub,sum(a.tax) as ppn from kontrabon_h a inner join list_payment b on b.no_kbon = a.no_kbon where b.no_payment = '$no_pay' group by b.no_payment");
                            $rows = mysqli_fetch_array($querys);
                            $sub = isset($rows['sub']) ? $rows['sub'] : 0;
                            $ppn = isset($rows['ppn']) ? $rows['ppn'] : 0; 
                        }
                        echo '
                        <tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value-nopay="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                            <td style="width:100px;" value-tanggal="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
                            <td style="width:100px;" value-tempo="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
                            <td style="width:100px;" value-sub="'.$sub.'">'.number_format($sub,2).'</td>
                            <td style="width:100px;" value-ppn="'.$ppn.'">'.number_format($ppn,2).'</td>
                            <td style="width:100px;" value-pph="'.$pph.'">'.number_format($pph,2).'</td>
                            <td style="width:100px;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td style="display:none;" data-total-idr="'.$total_idr.'">'.number_format($total_idr,2).'</td>
                            <td style="display:none;" value-curr="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="display:none;" value-kode="'.$row['kode'].'">'.$row['kode'].'</td>

                 
                        </tr>';
                    }
                      }else{
                        echo '<tr style="display:none">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td style="width: 200px;">
                <select class="form-control" name="nomor_coa" id="nomor_coa" style="width: 250px"> <option value="-" > - </option>';?> <?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; 
                echo'</select>
            </td>';
            echo '
            <td style="width: 200px;">
                <select class="form-control select2abs4" name="cost_ctr" id="cost_ctr" style="width: 250px"> <option value="-" > - </option>';?> <?php $sql = mysqli_query($conn1," select no_cc as code_combine,cc_name as cost_name from b_master_cc"); foreach ($sql as $cc) : echo'<option value="'.$cc["code_combine"].'"> '.$cc["cost_name"].' </option>'; endforeach; 
                echo'</select>
            </td>';
            
            echo '<td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </td>
                <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </td>
                <td>
                <select class="form-control " name="currenc" id="currenc" data-live-search="true">
                    <option value="IDR">IDR</option>  
                    <option value="USD">USD</option>                       

                </div>
                </td>
                <td>
                <input style="text-align: right;" type="number" min="0" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="input_debit(value)" autocomplete = "off">
                </td>
                <td>
                <input style="text-align: right;" type="number" min="0" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" oninput="input_credit(value)" autocomplete = "off">
                </td>
                <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </td>
                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
       </tr>

       ';
                      } 

                      if ($ref_num == 'Payment Voucher') {
                        echo '</tbody>                    
                            </table>';
                      }elseif($ref_num == 'Payment'){
                        echo '</tbody>                    
                            </table>';
                      }else{
                        echo '</tbody>
                    <tfoot>
          <tr>
            <td colspan="10" align="center">
            <button type="button" class="btn btn-primary" onclick=addRow("tbody2");>Add Row</button>
            <button type="button" class="btn btn-warning" onclick=InsertRow("tbody2");>Interject Row</button>
            <button type="button" class="btn btn-danger" onclick=hapus2();>Delete Row</button>
            </td>
          </tr>
    </tfoot>
                    
    </table>';
                      }                
                    ?>

                   
<div class="box footer">   
        <form id="form-simpan">
            <?php
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;;
                if ($ref_num == 'None') {
                    echo '
                    <div class="col-md-4 mb-1">
                     </br>
                    <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Total Debit </b></label>                  
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="tot_debit" name="tot_debit" readonly placeholder="0.00"> 
                    <input type="hidden" style="font-size: 14px;text-align: right" class="form-control" id="nomdebit" name="nomdebit" readonly >
                </div>
                </br>
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Total Credit </b></label>                  
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="tot_credit" name="tot_credit" readonly placeholder="0.00"> 
                    <input type="hidden" style="font-size: 14px;text-align: right" class="form-control" id="nomcredit" name="nomcredit" readonly > 
                    <input type="hidden" style="font-size: 14px;text-align: right" class="form-control" id="nom_credit" name="nom_credit" readonly > 
                </div>
                </br>
                    </div>';
                }else{
                    echo '</br>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Total</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total_cek" id="total_cek" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" class="form-control-plaintext" name="total_cek_h" id="total_cek_h" value=""  style="font-size: 14px;text-align: right;" readonly>
            </div>
            <div class="col-md-2 mb-5"> 
            <label for="pajak" class="col-form-label" style="padding-left: 50px"><b>Equivalent IDR</b></label>
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total_cek_idr" id="total_cek_idr" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                 <input type="hidden" class="form-control-plaintext" name="total_cek_idr_h" id="total_cek_idr_h" value=""  style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            ';
                } ?>
            
            <?php
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;;
                if ($ref_num == 'None') {
                    $data = 'style = "display : none;"';
                }else{
                    $data = 'style="font-size: 12px;text-align:center;"';
                } ?>
            <table <?php echo $data ?> id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
        <tr><th class="text-center">-</th>
            <th class="text-center">COA</th>
            <th class="text-center">Reff Document</th>
            <th class="text-center">Reff Date</th>
            <th class="text-center">Description</th>
            <th class="text-center">Debit</th>
            <th class="text-center">Credit</th>
            <th class="text-center" width="4"> Action </th>
        </tr>
    </thead>
    <tbody id="tbody3">
        <tr style="display:none">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td style="width: 200px;">
                <select class="form-control" name="nomor_coa" id="nomor_coa" style="width: 250px"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1," select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'>
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class='form-control tanggal' 
             autocomplete='off' placeholder="dd-mm-yyyy">
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'> 
            </td>
            <td>
                <input type="number" min="0" style="text-align: right;" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>
            <td>
                <input type="number" min="0" style="text-align: right;" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete = "off">
            </td>
            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
        </tr>
    </tbody>
    <tfoot>
          <tr>
            <td colspan="8" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody3')">Add Row</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody3')">Interject Row</button>
            <button type="button" class="btn btn-danger" onclick="hapus()">Delete Row</button>
            </td>
          </tr>
    </tfoot>                   
            </table> 

            <?php
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;;
                if ($ref_num == 'None') {
                    echo '';
                }else{
                    echo '<div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Total Credit</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="nominalcre" id="nominalcre" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" class="form-control-plaintext" name="nominalcre_h" id="nominalcre_h" value="" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" class="form-control-plaintext" name="jml_credit" id="jml_credit" value="" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Total Debit</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="nominaldeb" id="nominaldeb" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" class="form-control-plaintext" name="nominaldeb_h" id="nominaldeb_h" value="" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" class="form-control-plaintext" name="jml_debit" id="jml_debit" value="" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>';
                } ?>

           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-out.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
    $('#mytable').dataTable();
    
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
      function input_credit(){ 
    var rate = parseFloat(document.getElementById('rate').value,10) || 1; 
    var deb = parseFloat(document.getElementById('nomrate_h').value,10) || 0;   
    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var tot_deb = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var curr = document.getElementById("tbody2").rows[i].cells[5].children[0].value;
    var price = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[6].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    if (curr == 'USD') {
       tota += parseFloat(harga * rate); 
   }else{
    tota += parseFloat(harga);
}
    tot_deb  = tota + deb;




    document.getElementsByName("nom_credit")[0].value = tota.toFixed(2);
     document.getElementsByName("nomcredit")[0].value = tot_deb.toFixed(2);
    document.getElementsByName("tot_credit")[0].value = formatMoney(tot_deb.toFixed(2));
}
}
  </script>

  <script type="text/javascript">
      function input_debit(){ 
    var rate = parseFloat(document.getElementById('rate').value,10) || 1; 
    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var curr = document.getElementById("tbody2").rows[i].cells[5].children[0].value;
    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    if (curr == 'USD') {
       tota += parseFloat(harga * rate); 
   }else{
    tota += parseFloat(harga);
}



    document.getElementsByName("nomdebit")[0].value = tota.toFixed(2);
    document.getElementsByName("tot_debit")[0].value = formatMoney(tota.toFixed(2));
}
}
  </script>

<script type="text/javascript">
    
   // JavaScript Document
function addRow(tableID) {
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
    
function deleteRow()

{
  try
         {
        var table = document.getElementById("tbody3");
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[7].childNodes[0];
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
                    }
                }
            } catch(e)
    {
    alert(e);
    }
 }

 function deleteRow2()
{
    try
         {
        var table = document.getElementById("tbody2");
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[9].childNodes[0];
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
                var chkbox = row.cells[7].childNodes[0];
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
                                            newCell.children[i2].checked[8] = true;
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


 function hitungulang(){
    var table = document.getElementById("tbody3");
    var rowCount2 = table.rows.length;
    var tota = 0;
    var tota2 = 0;
    var harga = 0;
    var totall = 0;
    var tot_price= 0;
    var harga = 0;
    var harga2 = 0;
    var total_cre = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var total_deb = parseFloat(document.getElementById('total_cek_idr_h').value,10) || 0;
            for(var i=0; i<rowCount2; i++){

    var price = parseFloat(document.getElementById("tbody3").rows[i].cells[5].children[0].value,10) || 0;
    var price2 = parseFloat(document.getElementById("tbody3").rows[i].cells[6].children[0].value,10) || 0;

    if(price == ''){
        tot_price = price2;
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


    tota += price;
    tota2 += price2;

    var total_h = total_deb + tota;
    var total_h2 = total_cre + tota2;


    document.getElementsByName("nominalcre")[0].value = formatMoney(total_h2.toFixed(2));   
    document.getElementsByName("nominalcre_h")[0].value = (total_h2).toFixed(2);
    document.getElementsByName("jml_credit")[0].value = (tota2).toFixed(2);
    document.getElementsByName("nominaldeb")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("nominaldeb_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("jml_debit")[0].value = (tota).toFixed(2);

}
 }

 async function hapus(){
   await deleteRow();
   console.log("hasil");
   hitungulang();
}


function hitungulang2(){
    var table = document.getElementById("tbody2");
    var rowCount2 = table.rows.length;
    var tota = 0;
    var tota2 = 0;
    var harga = 0;
    var totall = 0;
    var tot_price= 0;
    var harga = 0;
    var harga2 = 0;
    var total_cre = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            for(var i=0; i<rowCount2; i++){

    var price = parseFloat(document.getElementById("tbody2").rows[i].cells[6].children[0].value,10) || 0;
    var price2 = parseFloat(document.getElementById("tbody2").rows[i].cells[7].children[0].value,10) || 0;

    if(price == ''){
        tot_price = price2;
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


    tota += price;
    tota2 += price2;

    var total_h2 = total_cre + tota2;


    document.getElementsByName("tot_debit")[0].value = formatMoney(tota.toFixed(2));   
    document.getElementsByName("nomdebit")[0].value = (tota).toFixed(2);

    document.getElementsByName("tot_credit")[0].value = formatMoney(total_h2.toFixed(2));
    document.getElementsByName("nomcredit")[0].value = (total_h2).toFixed(2);
    document.getElementsByName("nom_credit")[0].value = (tota2).toFixed(2);

}
 }

 async function hapus2(){
   await deleteRow2();
   console.log("hasil");
   hitungulang2();
}
</script>





<script type="text/javascript">
        function modal_input_amt(){ 
    var val = document.getElementById('valuta').value;
    var tot_pay = parseFloat(document.getElementById('total_cek_h').value,10) || 0; 
    var tot_pay2 = parseFloat(document.getElementById('total_cek_idr_h').value,10) || 0;     
    var table = document.getElementById("tbody3");
    var tota = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody3").rows[i].cells[5].children[0].value;
    var price2 = document.getElementById("tbody3").rows[i].cells[6].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    tota += parseFloat(harga);

    totall = tot_pay2 + tota;
    



    document.getElementsByName("nominaldeb")[0].value = formatMoney(totall.toFixed(2));
    document.getElementsByName("nominaldeb_h")[0].value = totall.toFixed(2);
    document.getElementsByName("jml_debit")[0].value = tota.toFixed(2);
}
}
  </script>

  <script type="text/javascript">
      function modal_input_cre(){ 
    var tot_pay = parseFloat(document.getElementById('nomrate_h').value,10) || 0;   
    var table = document.getElementById("tbody3");
    var tota = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody3").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody3").rows[i].cells[5].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    tota += parseFloat(harga);
    totall = tot_pay + tota;



    document.getElementsByName("nominalcre")[0].value = formatMoney(totall.toFixed(2));
    document.getElementsByName("nominalcre_h")[0].value = totall.toFixed(2);
    document.getElementsByName("jml_credit")[0].value = tota.toFixed(2);
}
}
  </script>

<script type="text/javascript"> 
    var refer = $('select[name=ref_num] option').filter(':selected').val();
    if (refer == 'Payment Voucher') {
    $("input[type=checkbox]").change(function(){

    var sum_total = 0;
    var sum_total2 = 0;  
    var total2 = 0;
    var total3 = 0;
    var ttl_sum = 0;
    var ttl_sum2 = 0; 
    var data1 = 0;
    var d_curr = '';
    var d_bank = '';
    var d_akun = '';
    var d_code = '';
    var curren = '';
    var valu = ''; 
    var rates = 0;  
    // $("input[type=checkbox]").prop('disabled', false);  
    $("#rate").prop('disabled', false);           
    $("input[type=checkbox]:checked").each(function () { 
    var ttl_debit = parseFloat(document.getElementById('jml_debit').value,10) || 0;
    var select_rate = parseFloat(document.getElementById('rat').value,10) || 0;
    var curr = document.getElementById('valuta').value || null;
    var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10) || 0;
    var tempo = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data-total-idr'),10) || 0;
    var total_idr = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data-total-idr'),10) || 0;
    var curr2 = $(this).closest('tr').find('td:eq(9)').attr('value-curr') || '';
    var akun = $(this).closest('tr').find('td:eq(10)').attr('value-akun') || '';
    var bank = $(this).closest('tr').find('td:eq(11)').attr('value-bank') || '';
    var kode = $(this).closest('tr').find('td:eq(12)').attr('value-code') || '';
    var no_pay = $(this).closest('tr').find('td:eq(1)').attr('value-nopay') || '';
    var urut = document.getElementById('no_urut').value;
    var bulan = document.getElementById('bulan').value;
    var tahun = document.getElementById('tahun').value;

    // var select_pi = $(this).closest('tr').find('td:eq(0) input');
    // select_pi.prop('disabled', false);
    // document.getElementById('rate').prop('disabled', true);

    sum_total += total;
    total2 += total_idr;
    ttl_sum = sum_total + ttl_debit;
    ttl_sum2 = total2 + ttl_debit;
    total3 += total_idr;
    d_curr += curr2;
    d_akun += akun;
    d_bank += bank;
    d_code += kode; 

    if(d_bank == 'BANK CENTRAL ASIA'){

    valu = 'BK'+'/'+d_code+'/'+'NAG'+'/'+bulan+tahun;
}else if(d_bank == 'BANK CIMB NIAGA'){

    valu = 'BK'+'/'+d_code+'/'+'NAG'+'/'+bulan+tahun;
}else{
    valu = 'BK'+'/'+d_code+'/'+'NAG'+'/'+bulan+tahun;
}

    if (d_curr == 'USD') {
        rates = select_rate;
    }else{
        rates = 1;
        $("#rate").prop('disabled', true);
    }     
    sum_total2 = ttl_sum2;
    });
   
    $("#total_cek").val(formatMoney(sum_total));
    $("#total_cek_idr").val(formatMoney(total2));
    $("#total_cek_idr_h").val(total2);
    $("#total_cek_h").val(sum_total);
    $("#nominaldeb_h").val(sum_total2);  
    $("#nominaldeb").val(formatMoney(sum_total2));
    $("#valuta").val(d_curr);
    $("#nama_bank").val(d_bank);
    $("#accountid").val(d_akun);   
    $("#nominal").val(formatMoney(sum_total));  
    $("#nominal_h").val(sum_total);
    $("#rate_h").val(formatMoney(rates));  
    $("#rate").val(rates);
    $("#nomrate_h").val(total2);  
    $("#nomrate").val(formatMoney(total2)); 
    $("#nominalcre_h").val(sum_total2.toFixed(2));  
    $("#nominalcre").val(formatMoney(sum_total2)); 
    $("#no_doc").val(valu);             
});   
}else{
  $("input[type=checkbox]").change(function(){

    var sum_total = 0;
    var sum_total2 = 0;  
    var total2 = 0;
    var ttl_sum = 0;
    var ttl_sum2 = 0;                
    $("input[type=checkbox]:checked").each(function () { 
    var ttl_debit = parseFloat(document.getElementById('jml_debit').value,10) || 0;
    var curr = document.getElementById('valuta').value;
    var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10) || 0;
    var total_idr = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data-total-idr'),10) || 0;
 
    sum_total += total;
    total2 += total_idr;
    ttl_sum = sum_total + ttl_debit;
    ttl_sum2 = total2 + ttl_debit;

    sum_total2 = ttl_sum2;
        
    });
   
    $("#total_cek").val(formatMoney(sum_total));
    $("#total_cek_idr").val(formatMoney(total2));
    $("#total_cek_idr_h").val(total2);
    $("#total_cek_h").val(sum_total);
    $("#nominaldeb_h").val(sum_total2);  
    $("#nominaldeb").val(formatMoney(sum_total2));                  
});     
}     
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

<script type="text/javascript">
   $('#accountid').change(function(){
    var ttl_jml = '';
    var valu = '';
    $("input[type=text]").each(function () {         
    var urut = document.getElementById('no_urut').value;
    var bulan = document.getElementById('bulan').value;
    var nbank = document.getElementById('nama_bank').value;
    var kode = document.getElementById('kode').value;
    var tahun = document.getElementById('tahun').value;

    if(nbank == 'BANK CENTRAL ASIA'){

    valu = 'BK'+'/'+'BCA'+kode+'/'+'NAG'+'/'+bulan+tahun;
}else if(nbank == 'BANK CIMB NIAGA'){

    valu = 'BK'+'/'+'CMB'+kode+'/'+'NAG'+'/'+bulan+tahun;
}else{
    valu = 'BK'+'/'+'BNI'+kode+'/'+'NAG'+'/'+bulan+tahun;
}

    
    });
   $("#no_doc").val(valu);


    });
</script>

<script type="text/javascript"> 
<?php echo $jsArray; ?>
var nom = 0;
var nom2 = 0;
function changeValueACC(id){
    var select_rate = document.getElementById('rate');
      var nominal = parseFloat(document.getElementById('nominal_h').value,10) || 0;     
    var kmk_rate = parseFloat(document.getElementById('rat').value,10) || 0;
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    nom = nominal;
    nom2 = nominal * kmk_rate;
    if (prdName[id].valuta == 'IDR') {
            if (nominal != '') {
                select_rate.disabled = true;
                document.getElementById('rate').value = '1';
                document.getElementById('rate_h').value = '1';
                $("#nomrate").val(formatMoney(nom));
                $("#nomrate_h").val(nom);
                $("#tot_credit").val(formatMoney(nom));
                $("#nomcredit").val(nom);

            }else{
                select_rate.disabled = true;
                document.getElementById('rate').value = '1';
                document.getElementById('rate_h').value = '1';

            }
        }else{
            select_rate.disabled = false;
            $("#rate").val(kmk_rate);
            $("#nomrate").val(formatMoney(nom2));
            $("#tot_credit").val(formatMoney(nom2));
            $("#nomcredit").val(nom2);
            $("#nomrate_h").val(nom2);
        }
};
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    var total_credit = 0;
    $("input[type=text]").each(function () {  
    var refer = $('select[name=ref_num] option').filter(':selected').val();
        if (refer != 'None') {
    var ttl_credit = parseFloat(document.getElementById('jml_credit').value,10) || 0; 
    }else{
     var ttl_credit = parseFloat(document.getElementById('nom_credit').value,10) || 0;    
    }       
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
    total_credit = ttl_jml + ttl_credit;

    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#rate_h").val(formatMoney(rat));
   $("#nominalcre").val(formatMoney(total_credit));
   $("#nominalcre_h").val(total_credit.toFixed(2));
   $("#tot_credit").val(formatMoney(total_credit));
   $("#nomcredit").val(total_credit);

    });
</script>

<script type="text/javascript">
    $("input[name=nominal_h]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var tot_cre = 0;
    var valu = '';
    var total_credit = 0;
    $("input[type=text]").each(function () {
    var refer = $('select[name=ref_num] option').filter(':selected').val();
        if (refer != 'None') {
    var ttl_credit = parseFloat(document.getElementById('jml_credit').value,10) || 0; 
    }else{
     var ttl_credit = parseFloat(document.getElementById('nom_credit').value,10) || 0;    
    }
    var curr = $(this).closest('tr').find('td:eq(5)').find('select[name=currenc] option').filter(':selected').val();        
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

    tot_cre = ttl_jml; 
    total_credit = tot_cre + ttl_credit;

    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#nominalcre_h").val(total_credit.toFixed(2));
   $("#nominalcre").val(formatMoney(total_credit));
   $("#tot_credit").val(formatMoney(total_credit));
   $("#nomcredit").val(total_credit);
   $("#nominal").val(formatMoney(rat));

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
    $("#form-simpan").on("click", "#simpan", function(){
        var refer = $('select[name=ref_num] option').filter(':selected').val();
        if (refer == 'None') {
        var no_bankout = document.getElementById('no_doc').value;        
        var bankout_date = document.getElementById('tgl_active').value;
        var reff_doc = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var amount = document.getElementById('nominal_h').value;        
        var rate = document.getElementById('rate').value;
        var eqv_idr = document.getElementById('nomrate_h').value;
        var deskripsi = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';
        var nomdebit = document.getElementById('nomdebit').value;
        var nomcredit = document.getElementById('nomcredit').value;
        var balance = nomdebit - nomcredit;
        
        if(amount != '' && nama_supp != '' && akun != '' && balance == '0'){
        $.ajax({
            type:'POST',
            url:'insert_bankout_h.php',
            data: {'no_bankout':no_bankout, 'bankout_date':bankout_date, 'reff_doc':reff_doc, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'amount':amount, 'rate':rate, 'eqv_idr':eqv_idr, 'deskripsi':deskripsi, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $("input[type=checkbox]:checked").each(function () {
        var no_bankout = document.getElementById('no_doc').value; 
        var tgl_bankout = document.getElementById('tgl_active').value;       
        var reff_doc = $('select[name=ref_num] option').filter(':selected').val();    
        var no_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val();     
        var no_costcntr = $(this).closest('tr').find('td:eq(2)').find('select[name=cost_ctr] option').filter(':selected').val();                   
        var buyer = $(this).closest('tr').find('td:eq(3) input').val();
        var no_ws = $(this).closest('tr').find('td:eq(4) input').val();
        var curr = $(this).closest('tr').find('td:eq(5)').find('select[name=currenc] option').filter(':selected').val();                   
        var debit = $(this).closest('tr').find('td:eq(6) input').val();
        var credit = $(this).closest('tr').find('td:eq(7) input').val();    
        var deskripsi = $(this).closest('tr').find('td:eq(8) input').val();
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var akun = document.getElementById('accountid').value;
        var amount = document.getElementById('nominal_h').value;
        var nomdebit = document.getElementById('nomdebit').value;
        var nomcredit = document.getElementById('nomcredit').value;
        var balance = nomdebit - nomcredit;

        if(amount != '' && nama_supp != '' && akun != '' && balance == '0' && debit != '' || amount != '' && nama_supp != '' && akun != '' && balance == '0' && credit != ''){   
        $.ajax({
            type:'POST',
            url:'insert_bankout_none.php',
            data: {'no_bankout':no_bankout, 'tgl_bankout':tgl_bankout, 'reff_doc':reff_doc, 'no_coa':no_coa, 'no_costcntr':no_costcntr, 'buyer':buyer, 'no_ws':no_ws, 'curr':curr, 'debit':debit, 'credit':credit, 'deskripsi':deskripsi},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'bank-out.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
    
        });  
                console.log(response);
               alert(response);
                window.location = 'bank-out.php';
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
        }else if(document.getElementById('accountid').value == ''){
        alert("Please Select Account");
        document.getElementById('accountid').focus();
        }else if(document.getElementById('nominal_h').value == ''){
        alert("Please Enter Amount");
         document.getElementById('nominal_h').focus();
        }else if(document.getElementById('nomdebit').value - document.getElementById('nomcredit').value != '0'){
        alert("Please Check Total Credit and Debit");
        }else{               
       
            alert("data saved successfully");
        }              
 
        }else {
        var no_bankout = document.getElementById('no_doc').value;        
        var bankout_date = document.getElementById('tgl_active').value;
        var reff_doc = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var amount = document.getElementById('nominal_h').value;        
        var rate = document.getElementById('rate').value;
        var eqv_idr = document.getElementById('nomrate_h').value;
        var deskripsi = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';
        var credit = document.getElementById('nominalcre_h').value;
        var debit = document.getElementById('nominaldeb_h').value;
        var balance = debit - credit;
        
        if(amount != '' && nama_supp != '' && akun != '' && balance == '0'){
        $.ajax({
            type:'POST',
            url:'insert_bankout_h.php',
            data: {'no_bankout':no_bankout, 'bankout_date':bankout_date, 'reff_doc':reff_doc, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'amount':amount, 'rate':rate, 'eqv_idr':eqv_idr, 'deskripsi':deskripsi, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $("input[type=checkbox]:checked").each(function () {
        var no_bankout = document.getElementById('no_doc').value; 
        var no_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val();     
        var reff_doc = $(this).closest('tr').find('td:eq(2) input').val();
        var reff_date = $(this).closest('tr').find('td:eq(3) input').val();
        var deskripsi = $(this).closest('tr').find('td:eq(4) input').val();
        var debit = $(this).closest('tr').find('td:eq(5) input').val(); 
        var credit = $(this).closest('tr').find('td:eq(6) input').val(); 
        var no_pay = $(this).closest('tr').find('td:eq(1)').attr('value-nopay');
        var pay_date = $(this).closest('tr').find('td:eq(2)').attr('value-tanggal');
        var due_date = $(this).closest('tr').find('td:eq(3)').attr('value-tempo');
        var dpp = parseFloat($(this).closest('tr').find('td:eq(4)').attr('value-sub'),10); 
        var ppn = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value-ppn'),10); 
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').attr('value-pph'),10); 
        var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10); 
        var total_idr = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data-total-idr'),10); 
        var curr = $(this).closest('tr').find('td:eq(9)').attr('value-curr');
        var kodelp = $(this).closest('tr').find('td:eq(10)').attr('value-kode');
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var akun = document.getElementById('accountid').value;
        var amount = document.getElementById('nominal_h').value;  
        var t_credit = document.getElementById('nominalcre_h').value;
        var t_debit = document.getElementById('nominaldeb_h').value;
        var balance = t_debit - t_credit;
        if(amount != '' && nama_supp != '' && akun != '' && balance == '0'){
        $.ajax({
            type:'POST',
            url:'insert_bankout_det.php',
            data: {'no_bankout':no_bankout, 'no_coa':no_coa, 'reff_doc':reff_doc, 'reff_date':reff_date, 'deskripsi':deskripsi, 'debit':debit, 'credit':credit, 'no_pay':no_pay, 'pay_date':pay_date, 'due_date':due_date, 'dpp':dpp, 'ppn':ppn, 'pph':pph, 'total':total, 'total_idr':total_idr, 'curr':curr, 'kodelp':kodelp},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'bank-out.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        }
    
        });
                console.log(response);
                 alert(response);
                window.location = 'bank-out.php';
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
        }else if(document.getElementById('accountid').value == ''){
        alert("Please Select Account");
        document.getElementById('accountid').focus();
        }else if(document.getElementById('nominal_h').value == ''){
        alert("Please Enter Amount");
         document.getElementById('nominal_h').focus();
        }else if(document.getElementById('nominaldeb_h').value - document.getElementById('nominalcre_h').value != '0'){
        alert("Please Check Total Credit and Debit");
        }else{               
       
            alert("data saved successfully");
        }                
 
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
