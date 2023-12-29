<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM INCOMING BANK</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Doc Number</b></label>
                <?php
            $sqlx = mysqli_query($conn2,"select max(id) as id FROM tbl_bankin_arcollection ");
            $rowx = mysqli_fetch_array($sqlx);
            $maxid = $rowx['id'];

            $sql = mysqli_query($conn2,"select max(doc_num) from tbl_bankin_arcollection where id = '$maxid'");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(doc_num)'];
            $urutan = (int) substr($kodepay,20,5);
            $urutan ++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "BM//NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

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
                
                   $sql = mysqli_query($conn1,"select ref_doc,ref_doc2 from tbl_ref where ket = 'bankin'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
                    // $data2 = $row['ref_doc2'];
                    if($row['ref_doc'] == $_POST['ref_num']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
            </div> 
            <div class="col-md-5">
            <label for="nama_supp"><b>Source</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">                                                 
                <?php
                $nama_supp ='';
                $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;

                if ($ref_num == 'Bank Keluar') {
                    $data = 'PT. Nirwana Alabare Garment';
                    if(!empty($_POST['nama_supp'])){
                        $isSelected = 'PT. Nirwana Alabare Garment';
                    }else{
                        $isSelected = '';

                    }
                     echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';  
                }else{
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC");
                echo '<option value="Unrealize"  selected="true">Unrealize</option>';
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }
            }?>
                </select>

                </div>


                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ( $ref == 'Bank Keluar') {
                    echo '
                </br>
             <div class="col-md-3">
                <label for="no_bk" class="col-form-label" style="width: 150px;" ><b>No Bank Out </b></label>  
                <select class="form-control selectpicker" name="no_bk" id="no_bk" data-live-search="true" required onchange="this.form.submit()">
                <option value="" disabled selected="true">Select no bank out</option> ';?> 
                <?php 
                        $sqlacc = mysqli_query($conn1,"select no_bankout from b_bankout_h where status = 'Approved' and stat_bi != 'Y' and nama_supp = 'PT. NIRWANA ALABARE GARMENT'");

                        // $sqlacc = mysqli_query($conn1,"select a.no_bankout from b_bankout_h a inner join b_bankout_none b on b.no_bankout = a.no_bankout inner join mastercoa_v2 c on c.no_coa = b.no_coa where a.status = 'Approved' and a.outstanding != '0' and nama_supp = 'PT. NIRWANA ALABARE GARMENT' and b.no_coa = '1.90.01'");

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_bankout'];
                            if($row['no_bankout'] == $_POST['no_bk']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="no_bk" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                        }
                        ?>
                <?php echo'</select>
                   
            </div>
            </br>';
            }
               else{
                echo '';
               }
               
                
            ?>


                <?php 
//                 $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
//                 if ($ref == 'AR Collection' OR $ref == 'None') {
//                     echo '';
//                 }else{

//         echo '<div style="padding-top: 30px; padding-left: 10px;">
//             <input style="border: 0;
//     line-height: 1;
//     padding: 10px 10px;
//     font-size: 1rem;
//     text-align: center;
//     color: #fff;
//     text-shadow: 1px 1px 1px #000;
//     border-radius: 6px;
//     background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">  
//     </div>';
// }
    ?>


    <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '
                        </div>
                </br>
                <div class="form-row">
             <div class="col-md-3">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control selectpicker" name="accountid" id="accountid" data-live-search="true"'; echo"onchange='changeValueACC(this.value)'"; echo'required >
                <option value="" disabled selected="true">Select Account</option> ';?> 
                <?php 
                        $sqlacc = mysqli_query($conn1,"select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode from b_masterbank where status = 'Active'");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['account'];
                            if($row['account'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['account'] . "'] = {nama_bank:'" . addslashes($row['bank']) . "',kode:'" . addslashes($row['kode']) . "',valuta:'".addslashes($row['curr'])."'};\n";
                        }
                        ?>
                <?php echo'</select>
                   
            </div>
            </br>
           
                <div class="col-md-2" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Currency </b></label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >  
                    <input type="text" style="font-size: 12px;" class="form-control" id="kode" name="kode" readonly hidden >         
                </div>
                </br>
                <div class="col-md-3" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Bank </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
               
                </br>';
                }else{

                echo '';
        }?>

                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    
            echo '</div>
                </br>
                <div class="form-row">
                <div class="col-md-3" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>
                <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h1" name="nominal_h1" placeholder="">
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="nominalcre" name="nominalcre" placeholder="" readonly>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="nominaldeb" name="nominaldeb" placeholder="" readonly>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </div>

            <div class="col-md-2" >
                <label for="nama_supp" class="col-form-label" ><b>Rate </b></label>
            <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." disabled >
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat" name="rat" 
                value="';

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        echo'">
                </div>
                </div>

            <div class="col-md-3" >
                <label for="nama_supp" class="col-form-label" ><b>Equivalent IDR</b></label>
                <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </div>
            </div>
                </br>
                <div class="form-row">
            <div class="col-md-8" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="40" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                </div>';
            }elseif ($ref == 'Bank Keluar') {
                    
            echo '</div>
                </br>
                <div class="form-row">
                <div class="col-md-3" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>
                <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h1" name="nominal_h1" placeholder=""
                 value="';?> <?php             
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $no_bk = isset($_POST['no_bk']) ? $_POST['no_bk']: null;
            }

            $no_bk = isset($_POST['no_bk']) ? $_POST['no_bk']: null;
            $sql = mysqli_query($conn2,"select round(amount,0) as amount from b_bankout_h  where no_bankout = '$no_bk' ");
            $row = mysqli_fetch_array($sql);
            $amount = isset($row['amount']) ? $row['amount']: null;         
    
            // $top = 30;

            if(!empty($no_bk)) {
                
                  echo $amount;      
            }
            else{
                echo '';
            } ?><?php echo '">
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="nominalcre" name="nominalcre" placeholder="" readonly>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="nominaldeb" name="nominaldeb" placeholder="" readonly>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly
                value="';?> <?php             
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $no_bk = isset($_POST['no_bk']) ? $_POST['no_bk']: null;
            }

            $no_bk = isset($_POST['no_bk']) ? $_POST['no_bk']: null;
            $sql = mysqli_query($conn2,"select round(amount,0) as amount from b_bankout_h  where no_bankout = '$no_bk' ");
            $row = mysqli_fetch_array($sql);
            $amount = isset($row['amount']) ? $row['amount']: null;         
    
            // $top = 30;

            if(!empty($no_bk)) {
                
                  echo number_format($amount,2);      
            }
            else{
                echo '';
            } ?><?php echo '">
            </div>
            </div>

            <div class="col-md-2" >
                <label for="nama_supp" class="col-form-label" ><b>Rate </b></label>
            <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." disabled >
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly>
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat" name="rat" 
                value="';

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        echo'">
                </div>
                </div>

            <div class="col-md-3" >
                <label for="nama_supp" class="col-form-label" ><b>Equivalent IDR</b></label>
                <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </div>
            </div>
                </br>
                <div class="form-row">
            <div class="col-md-8" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="40" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                </div>';
            }
               else{
                echo '';
               }
               
                
            ?>



                                        
    </div>
</br>

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
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
                <div class="form-row">
                 <div class="col-md-4">
            <label for="nama_supp"><b>Customer</b></label>   

             <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'Bank Keluar') {
                    echo '<input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="PT. Nirwana Alabare Garment">';
                }else{

                    echo '<input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="';?> <?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?> <?php echo'">';
                }
                ?>         
              <!-- <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>"> -->
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

             <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'AR Collection') {
                    echo '';
                }elseif($ref == 'None'){

        echo '<table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">-</th>
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
                }else{

        echo '<table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
            <th class="text-center">-</th>
            <th class="text-center">COA</th>
            <th class="text-center">Reff Document</th>
            <th class="text-center">Reff Date</th>
            <th class="text-center">Description</th>
            <th class="text-center">Debit</th>
            <th class="text-center">Credit</th>
                        </tr>
                    </thead>

            <tbody> ';
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
            $no_bk = isset($_POST['no_bk']) ? $_POST['no_bk']: null;
            }

            if ($ref_num == 'Bank Keluar') {
                $sql = mysqli_query($conn2,"select a.no_bankout, GROUP_CONCAT(c.no_coa,' ',c.nama_coa) as coa,b.no_coa,a.bankout_date,a.nama_supp,a.curr, a.amount, a.outstanding, a.status, a.deskripsi from b_bankout_h a inner join b_bankout_none b on b.no_bankout = a.no_bankout inner join mastercoa_v2 c on c.no_coa = b.no_coa where a.no_bankout = '$no_bk'
                     union
                    select a.no_bankout,'-' as coa,'-' as no_coa,a.bankout_date,a.nama_supp,a.curr, a.amount, a.outstanding, a.status, a.deskripsi from b_bankout_h a inner join b_bankout_det b on b.no_bankout = a.no_bankout where a.no_bankout = '$no_bk'");
            }else{
                '';
            }
            
            
                if ($ref_num == 'AR Collection') {
                    echo '';
                }elseif($ref_num == 'None'){

                    
                    echo '<tr style="display:none">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td style="width: 200px;">
                <select class="form-control" name="nomor_coa" id="nomor_coa" style="width: 250px"> <option value="-" > - </option>';?> <?php $sql = mysqli_query($conn1," select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; 
                echo'</select>
            </td>';
            echo '
            <td style="width: 200px;">
                <select class="form-control select2abs4" name="cost_ctr" id="cost_ctr" style="width: 250px"> <option value="-" > - </option>';?> <?php $sql = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc"); foreach ($sql as $cc) : echo'<option value="'.$cc["code_combine"].'"> '.$cc["cost_name"].' </option>'; endforeach; 
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
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
                </td>
                <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete = "off">
                </td>
                <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </td>
                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
       </tr>

       ';

                }else{
                    while($row = mysqli_fetch_array($sql)){
                        $no_reff = $row['no_bankout'];
                        if(!empty($no_reff)) {
                        
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>                      
                            <td style="width:150px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" data="'.$row['no_coa'].'" value="'.$row['coa'].'" disabled>
                            </td>
                            <td style="width:100px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" value="'.$row['no_bankout'].'" disabled>
                            </td>
                            <td style="width:80px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" value="'.$row['bankout_date'].'" disabled>
                            </td>
                            <td style="width:100px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" value="'.$row['deskripsi'].'" disabled>
                            </td>
                            <td style="width:50px;">
                            <input style="text-align: right;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" value="0" disabled>
                            </td>
                            <td style="width:50px;">
                            <input style="text-align: right;font-size: 14px" type="text" class="form-control" id="dataamount" name="dataamount" data= "'.number_format($row['amount'],2).'" value="'.$row['amount'].'" disabled>
                            </td>
                 
                        </tr>

                        <tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>                      
                            <td style="width:150px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount" data="8.52.01" value="8.52.01 LABA / (RUGI) SELISIH KURS" disabled>
                            </td>
                            <td style="width:100px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount">
                            </td>
                            <td style="width:80px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control tanggal" id="txt_amount" name="txt_amount">
                            </td>
                            <td style="width:100px;">
                            <input style="text-align: center;font-size: 14px" type="text" class="form-control" id="txt_amount" name="txt_amount">
                            </td>
                            <td style="width:50px;">
                            <input style="text-align: right;font-size: 14px" type="text" class="form-control" value="0" id="datadeb" name="datadeb" disabled>
                            </td>
                            <td style="width:50px;">
                            <input style="text-align: right;font-size: 14px" type="text" class="form-control" value="0" id="datacre" name="datacre"  disabled>
                            </td>
                 
                        </tr>';
                            }
                        }
                      }                  
                    ?>
            <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'AR Collection') {
                    echo '';
                }elseif($ref == 'None'){
                    echo '</tbody>
                    <tfoot>
          <tr>
            <td colspan="10" align="center">
            <button type="button" class="btn btn-primary" onclick=addRow("tbody2");>Add Row</button>
            <button type="button" class="btn btn-warning" onclick=InsertRow("tbody2");>Interject Row</button>
            <button type="button" class="btn btn-danger" onclick=deleteRow("tbody2");>Delete Row</button>
            </td>
          </tr>
    </tfoot>
                    
    </table>';
                }else{

        echo '</tbody>

            </table>';
            }
            ?>
<!--             <tfoot>
          <tr>
            <td colspan="9" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow("tbody2")">Tambah Baris</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow("tbody2")">Sisip Baris</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow("tbody2")">Hapus Baris</button>
            </td>
          </tr>
    </tfoot>   -->                   
<div class="box footer">   
        <form id="form-simpan">
            
                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    echo '
                    <div class="col-md-4 mb-1">
                     </br>
                    <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Total Debit </b></label>                  
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="tot_debit" name="tot_debit" readonly >
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="h_tot_debit" name="h_tot_debit" readonly > 
                </div>
                </br>
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Total Credit </b></label>                  
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="tot_credit" name="tot_credit" readonly >
                    <input type="text" style="font-size: 14px;text-align: right" class="form-control" id="h_tot_credit" name="h_tot_credit" readonly > 
                </div>
                </br>
                    </div>';
                }elseif ($ref == 'Bank Keluar') {
                    echo '';
                }else{

        echo '
        <div class="col-md-5 mb-1">
                </br>
             <div class="input-group">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control" name="accountid" id="accountid" data-live-search="true"'; echo"onchange='changeValueACC2(this.value)'"; echo'required >
                <option value="" disabled selected="true">Select Account</option> ';?> 
                <?php 
                        $sqlacc = mysqli_query($conn1,"select bank_name as bank,curr, bank_account as account,RIGHT(bank_account,4) as kode from b_masterbank where status = 'Active'");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['account'];
                            if($row['account'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['account'] . "'] = {nama_bank:'" . addslashes($row['bank']) . "',kode:'" . addslashes($row['kode']) . "',valuta:'".addslashes($row['curr'])."'};\n";
                        }
                        ?>
                <?php echo'</select>
                   
            </div>
            </br>
           
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Bank </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Currency </b></label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >  
                    <input type="text" style="font-size: 12px;" class="form-control" id="kode" name="kode" readonly hidden >         
                </div>
                </br>';}?>
                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }else{

        echo '<div class="input-group">
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>COA</b></label>            
              <select class="form-control selectpicker" name="coa" id="coa" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select COA</option>';
            }?>

                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $coa = isset($_POST['coa']) ? $_POST['coa']: null;
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                } 
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }
                elseif ($ref == 'AR Collection') {
                    $sql = mysqli_query($conn1," select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where nama_coa like '%PIUTANG USAHA%'
");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_coa = $row['id_coa'];
                    if($row['coa'] == $_POST['coa']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_coa.'"'.$isSelected.'">'. $data .'</option>';    
                }
                } else{               
                $sql = mysqli_query($conn1," select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_coa = $row['id_coa'];
                    if($row['coa'] == $_POST['coa']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_coa.'"'.$isSelected.'">'. $data .'</option>';    
                }
            }?>
                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }else{

        echo '</select>
                </div>
            </br>';
        }?>

            <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }else{

        echo '<div class="input-group">
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Cost Center</b></label>            
              <select class="form-control selectpicker" name="cost" id="cost" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select Cost Center</option>';
                }?>                                                
                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $cost = isset($_POST['cost']) ? $_POST['cost']: null;
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                } 
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }
                else{                 
                $sql = mysqli_query($conn1,"    select no_cc as code_combine,cc_name as cost_name from b_master_cc");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['cost_name'];
                    $code_combine = $row['code_combine'];
                    if($row['cost_name'] == $_POST['cost']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$code_combine.'"'.$isSelected.'">'. $data .'</option>';    
                }
            }?>
                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }else{

        echo '</select>
                </div>
            </br>';
        }?>
                <?php
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    
                echo '';
            }
               else{ 
            echo '<div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>';}
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    
                echo '';
            }
               else{
                echo '

               <input type="number" min="0" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h" name="nominal_h" placeholder="input nominal..." >';
               }
               
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None' || $ref == 'Bank Keluar') {
                    echo '';
                }else{
                    echo '<input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Rate </b></label>
                <input type="number" min="0" style="font-size: 14px;text-align: right;" class="form-control" id="rate1" name="rate1" placeholder="input rate here..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h1" name="rate_h1" placeholder="0.00" readonly="">
                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat" name="rat" 
                value="';

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        echo'">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Equivalent IDR</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </br>

            <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="5" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                </div>
                </br>

            </div>';}
            ?>
            
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in22.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
      function modal_input_amt(){ 
    var deb = parseFloat(document.getElementById('nominal_h1').value,10) || 0;   
    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var tot_deb = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[6].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[7].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    tota += parseFloat(harga);
    tot_deb  = tota + deb;




    document.getElementsByName("nominaldeb")[0].value = tota.toFixed(2);
    document.getElementsByName("tot_debit")[0].value = formatMoney(tot_deb.toFixed(2));
    document.getElementsByName("h_tot_debit")[0].value = tota.toFixed(2);
}
}
  </script>

  <script type="text/javascript">
      function modal_input_cre(){ 
      
    var table = document.getElementById("tbody2");
    var tota = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var price = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var price2 = document.getElementById("tbody2").rows[i].cells[6].children[0];
    if (price == '') {
        harga = 0;
        price2.readOnly = false;
    }else{
        harga = price;
        price2.readOnly = true;
    }
    tota += parseFloat(harga);



    document.getElementsByName("nominalcre")[0].value = tota.toFixed(2);
    document.getElementsByName("tot_credit")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("h_tot_credit")[0].value = tota.toFixed(2);
}
}
  </script>
<!-- 
<script >
    $('#add').click( function() {      
 var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);

 var element1 = '<tr><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td style="width: 200px;"><select class="form-control select2bs4" name="cost" id="cost" style="width: 250px"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail"); foreach ($sql as $cc) : ?> <option value="<?= $cc["id_coa"]; ?>"><?= $cc["coa"]; ?> </option><?php endforeach; ?> </select></td><td style="width: 200px;"><select class="form-control selectpicker" name="cost_ctr" id="cost_ctr" data-live-search="true"  required ><option value="" disabled selected="true">Select Account</option></select></td><td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true"><option value="" disabled selected="true">Select Currency</option><option value="IDR">IDR</option><option value="USD">USD</option></select></td><td><input  type="text" class="form-control" id="txt_amount" name="txt_amount" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><input  type="text" class="form-control" id="txt_credit" name="txt_credit" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td></tr>';
 row.innerHTML = element1; 
}); 
</script> -->

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
    
function deleteRow(tableID)
{
    try
         {
        var table = document.getElementById(tableID);
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
                var chkbox = row.cells[9].childNodes[0];
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
                                            // newCell.children[i2].value = "";
                                            newCell.children[i2].checked[8] = true;
                                        }else{
                                            // newCell.children[i2].value = "";
                                        }
                                    break;
                                    case "SELECT":
                                        // newCell.children[i2].value = "";
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
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>


<!-- <script type="text/javascript">
    $("input[name=txt_amount").keyup(function(){

    var sum_amount = 0;
       
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

    sum_amount += amount;
   
    });

    $("#nominal").val(formatMoney(sum_amount));
    $("#nominal_h1").val(sum_amount);
    $("#nomrate").val(formatMoney(sum_amount));
   $("#nomrate_h").val(sum_amount);    
    

    });
</script> -->

<script type="text/javascript">
    $("input[name=txt_credit").keyup(function(){

    var sum_amount2 = 0;
       
    $("input[type=checkbox]:checked").each(function () {        
    var amount2 = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    sum_amount2 += amount2;
     
    });

    $("#nominal_h2").val(sum_amount2);    

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
    $("input[name=nominal_h1]").keyup(function(){
    var ttl_jml = 0;
     var ttl_jml2 = 0;
     var ttl_jml3 = 0;
     var ttl_jml4 = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h1').value,10) || 0;
    var reff_doc = $('select[name=ref_num] option').filter(':selected').val();
    if (reff_doc == 'None') {
        var credit = 0;
    }else{
    var credit = parseFloat(document.getElementById('dataamount').value,10) || 0;
}
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = ttl_h;

    ttl_jml = ttl_h * rate;    

    ttl_jml2 = credit - ttl_jml;

    if (ttl_jml2 >= 0) {
        ttl_jml3 = ttl_jml2;
        ttl_jml4 = 0;
    }else{
        ttl_jml3 = 0;
        if (ttl_jml2 == 0) {
            ttl_jml4 = ttl_jml2;
        }else{
            ttl_jml4 = ttl_jml2 * -1;
        }
    }

    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#tot_debit").val(formatMoney(rat));
   $("#nomrate_h").val(ttl_jml);
   $("#nominalh2").val(ttl_jml);
   $("#datadeb").val(ttl_jml3);
   $("#datacre").val(ttl_jml4);
   $("#nominal").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("input[name=rate1]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate1').value,10) || 1;
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
   $("#rate_h1").val(formatMoney(rat));

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

    if(nbank == 'Bank Central Asia'){

    valu = 'BM'+'/'+'BCA'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}else if(nbank == 'Bank CIMB Niaga'){

    valu = 'BM'+'/'+'CMB'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}else{
    valu = 'BM'+'/'+'BNI'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}

    
    });
   $("#no_doc").val(valu);


    });
</script>

<script type="text/javascript"> 
<?php echo $jsArray; ?>
var nom = 0;
function changeValueACC(id){
    var select_rate = document.getElementById('rate');  
    var nominal = parseFloat(document.getElementById('nominal_h1').value,10) || 0;  
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    var kmk_rate = parseFloat(document.getElementById('rat').value,10) || 0;
    nom = nominal;
    if (prdName[id].valuta == 'IDR') {
            if (nominal != '') {
                select_rate.disabled = true;
                document.getElementById('rate').value = '1';
                document.getElementById('rate_h').value = '1';
                $("#nomrate").val(formatMoney(nom));

            }else{
                select_rate.disabled = true;
                document.getElementById('rate').value = '1';
                document.getElementById('rate_h').value = '1';

            }
        }else{
            select_rate.disabled = false;
            $("#rate").val(kmk_rate);
        }
};
</script>

<script type="text/javascript"> 
<?php echo $jsArray; ?>
var nom = 0;
function changeValueACC2(id){
    var select_rate = document.getElementById('rate1'); 
    var nominal = parseFloat(document.getElementById('nominal_h').value,10) || 0;  
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    var kmk_rate = parseFloat(document.getElementById('rat').value,10) || 0;
    nom = nominal;
    if (prdName[id].valuta == 'IDR') {
        if (nominal != '') {
            select_rate.disabled = true;
            document.getElementById('rate1').value = '1';
            document.getElementById('rate_h1').value = '1';
            $("#nomrate").val(formatMoney(nom));
            $("#nomrate_h").val(nom);
        }else{
            select_rate.disabled = true;
            document.getElementById('rate1').value = '1';
            document.getElementById('rate_h1').value = '1';
        }
    }else{
            select_rate.disabled = false;
            $("#rate1").val(kmk_rate);
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
    var ttl_h = parseFloat(document.getElementById('nominal_h1').value,10) || 0;
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
    var rate = parseFloat(document.getElementById('rate1').value,10) || 1;
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
        var doc_number = document.getElementById('no_doc').value;        
        var doc_date = document.getElementById('tgl_active').value;
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var coa = "-";        
        var cost = "-";
        var nominal = document.getElementById('nominal_h1').value;
        var nominaldeb = document.getElementById('nominaldeb').value;
        var nominalcre = document.getElementById('nominalcre').value;
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';
        var balance = nominalcre - nominaldeb;

        if(akun != '' && nominal != '' && nominal != '0' && nominal == balance){
        $.ajax({
            type:'POST',
            url:'insert_bankin_arc.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost, 'nominal':nominal, 'rate':rate, 'eqv_idr':eqv_idr, 'pesan':pesan, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                //  // alert(response);
                window.location = 'bank-in22.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 

         if(document.getElementById('accountid').value == ''){
        document.getElementById('accountid').focus();
        alert("please enter account");
        }else if(document.getElementById('nominal_h1').value == ''){
        document.getElementById('nominal_h1').focus();
        alert("Please Enter Amount");
        }else if(document.getElementById('nominal_h1').value == '0'){
        document.getElementById('nominal_h1').focus();
        alert("Amount Can't be Zero");
        }else if(document.getElementById('nominal_h1').value != (document.getElementById('nominalcre').value - document.getElementById('nominaldeb').value)){
        alert("calculation does not match");
        }else{
            alert("Successful Saved");
        }
                        

        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nomor_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val();     
        var cost_ctr = $(this).closest('tr').find('td:eq(2)').find('select[name=cost_ctr] option').filter(':selected').val();                   
        var buyer = $(this).closest('tr').find('td:eq(3) input').val();
        var no_ws = $(this).closest('tr').find('td:eq(4) input').val();
        var curre = $(this).closest('tr').find('td:eq(5)').find('select[name=currenc] option').filter(':selected').val();                   
        var debit = $(this).closest('tr').find('td:eq(6) input').val();
        var credit = $(this).closest('tr').find('td:eq(7) input').val();    
        var keterangan = $(this).closest('tr').find('td:eq(8) input').val();
        var nominal = document.getElementById('nominal_h1').value;
        var nominaldeb = document.getElementById('nominaldeb').value;
        var nominalcre = document.getElementById('nominalcre').value;
        var balance = nominalcre - nominaldeb;

        if(debit != '' && nominal == balance || credit != '' && nominal == balance){   
        $.ajax({
            type:'POST',
            url:'insert_bankin.php',
            data: {'doc_number':doc_number, 'referen':referen, 'nomor_coa':nomor_coa, 'cost_ctr':cost_ctr, 'buyer':buyer, 'no_ws':no_ws, 'curre':curre, 'debit':debit, 'credit':credit, 'keterangan':keterangan},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'bank-in22.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
    
        });                
 
        }else if(refer == 'AR Collection'){
        var doc_number = document.getElementById('no_doc').value;        
        var doc_date = document.getElementById('tgl_active').value;
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var coa = document.getElementById('coa').value;        
        var cost = document.getElementById('cost').value;
        var nominal = document.getElementById('nominal_h').value; 
        var rate = document.getElementById('rate1').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';

        if(akun != '' && coa != '' && nominal != '' && nominal != '0'){
        $.ajax({
            type:'POST',
            url:'insert_bankin_arc.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost, 'nominal':nominal, 'rate':rate, 'eqv_idr':eqv_idr, 'pesan':pesan, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                //  // alert(response);
                window.location = 'bank-in22.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
        if(document.getElementById('accountid').value == ''){
        document.getElementById('accountid').focus();
        alert("please enter account");
        }else if(document.getElementById('coa').value == ''){
        document.getElementById('coa').focus();
        alert("please enter coa");
        }else if(document.getElementById('nominal_h').value == ''){
        document.getElementById('nominal_h').focus();
        alert("please enter Amount");
        }else if(document.getElementById('nominal_h').value == '0'){
        document.getElementById('nominal_h').focus();
        alert("Amount Can't be Zero");
        }else{
            alert("Successful Saved");
        }
        }else {
        var doc_number = document.getElementById('no_doc').value;        
        var doc_date = document.getElementById('tgl_active').value;
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var no_bk = $('select[name=no_bk] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var coa = "-";        
        var cost = "-";
        var nominal = document.getElementById('nominal_h1').value;
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';

        if(akun != '' && nominal != ''){
        $.ajax({
            type:'POST',
            url:'insert_bankin_arc.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost, 'nominal':nominal, 'rate':rate, 'eqv_idr':eqv_idr, 'pesan':pesan, 'create_user':create_user, 'no_bk':no_bk},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                //  // alert(response);
                window.location = 'bank-in22.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 

         if(document.getElementById('accountid').value == ''){
        document.getElementById('accountid').focus();
        alert("please enter account");
        }else if(document.getElementById('nominal_h1').value == ''){
        alert("Please Input Amount");
        }else{
            alert("Successful Saved");
        }
                        

        $("input[type=checkbox]:checked").each(function () {
        var no_bankin = document.getElementById('no_doc').value;                        
        var id_coa = $(this).closest('tr').find('td:eq(1) input').attr('data');
        var no_reff = $(this).closest('tr').find('td:eq(2) input').val();                  
        var reff_date = $(this).closest('tr').find('td:eq(3) input').val();
        var deskripsi = $(this).closest('tr').find('td:eq(4) input').val();    
        var t_debit = $(this).closest('tr').find('td:eq(5) input').val();
        var t_credit = $(this).closest('tr').find('td:eq(6) input').val();        
        var akun = document.getElementById('accountid').value;        
        var nominal = document.getElementById('nominal_h1').value;

        if(akun != '' && nominal != ''){   
        $.ajax({
            type:'POST',
            url:'insert_bankin_none.php',
            data: {'no_bankin':no_bankin, 'id_coa':id_coa, 'no_reff':no_reff, 'reff_date':reff_date, 'deskripsi':deskripsi, 't_debit':t_debit, 't_credit':t_credit},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'bank-in22.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
    
        });                
 
        }
                         
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
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
