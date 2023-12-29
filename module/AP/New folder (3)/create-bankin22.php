<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM BANK IN</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Doc Number</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(doc_num) from tbl_bankin_arcollection");
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
                
                   $sql = mysqli_query($conn1,"select ref_doc from tbl_ref where ket = 'bankin'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
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
                if ($ref == 'AR Collection' OR $ref == 'None') {
                    echo '';
                }else{

        echo '<div style="padding-top: 30px; padding-left: 10px;">
            <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">  
    </div>';
}
    ?>


    <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    echo '
                        </div>
                </br>
                <div class="form-row">
             <div class="col-md-3">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control selectpicker" name="accountid" id="accountid" data-live-search="true"'; echo"onchange='changeValueACC(this.value)'"; echo'required >
                <option value="" disabled selected="true">Select Account</option> ';?> 
                <?php 
                        $sqlacc = mysqli_query($conn1,"select bank,curr,account,RIGHT(account,4) as kode from ap_masterbank where status = 'Active'");
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
           
                <div class="col-md-3" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Bank </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="col-md-2" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Currency </b></label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >  
                    <input type="text" style="font-size: 12px;" class="form-control" id="kode" name="kode" readonly hidden >         
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
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h2" name="nominal_h2" placeholder="" readonly>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h3" name="nominal_h3" placeholder="" readonly>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </div>

            <div class="col-md-3" >
                <label for="nama_supp" class="col-form-label" ><b>Rate </b></label>
            <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly="">
                </div>
                </div>

            <div class="col-md-2" >
                <label for="nama_supp" class="col-form-label" ><b>Equivalent IDR</b></label>
                <div class="input-group" >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </div>
            </div>
                </br>
                <div class="form-row">
            <div class="col-md-5" >
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
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                            <th style="width:50px;">Doc Number</th>
                            <th style="width:100px;">Document Date</th>                                                           
                            <th style="width:100px;">Total</th>
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
            }

            if ($ref_num == 'Payment') {
                $sql = mysqli_query($conn2,"select * from (
select no_payment, tgl_payment, tgl_tempo from list_payment where status = 'Closed' and tgl_payment BETWEEN '$start_date' and '$end_date' and nama_supp = '$nama_supp' group by no_payment
union
select no_pay, tgl_pay, due_date from saldo_awal  where status = 'Closed' and tgl_pay BETWEEN '$start_date' and '$end_date' and supplier = '$nama_supp' group by no_pay) as b left join 

(select no_lp from ap_bankout where status != 'Cancel') as  c on c.no_lp = b.no_payment where c.no_lp is null");
            }else{
                '';
            }
            
            
                if ($ref_num == 'AR Collection') {
                    echo '';
                }elseif($ref_num == 'None'){

                   $x = 1;

                while($x <= 3) { 
                    echo '<tr>
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td style="width: 200px;">
                <div class="form-group">
                <select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true"  required >
                <option value="" disabled selected="true">Select Account</option>';  
           
                        $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_coa = $row['id_coa'];
                    if($row['coa'] == isset($_POST['nomor_coa']) ? $_POST['nomor_coa'] : null){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_coa.'"'.$isSelected.'">'. $data .'</option>';
                            
                        }
                       
                echo'</select>
                </div>
            </td>';
            
           echo'<td style="width: 200px;">
                <div class="form-group">
                <select class="form-control selectpicker" name="cost_ctr" id="cost_ctr" data-live-search="true"  required >
                <option value="" disabled selected="true">Select Account</option>';  
           
                        $sql = mysqli_query($conn1,"select code_combine,cost_name from tbl_cost_center");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['cost_name'];
                    $code_combine = $row['code_combine'];
                    if($row['cost_name'] == $_POST['cost_ctr']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$code_combine.'"'.$isSelected.'">'. $data .'</option>'; 
                            
                        }
                       
                echo'</select>
                </div>
            </td>';
            echo '<td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </div>
                </td>
                <td>
                <div class="form-group">
                <select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
                <option value="" disabled selected="true">Select Currency</option> 
                    <option value="IDR">IDR</option>  
                    <option value="USD">USD</option>                       
        
                </select>
                </div>
                </td>
                <td>
                <div class="form-group">
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount" value="" autocomplete = "off">
                </div>
                </td>
                <td>
                <div class="form-group">
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" value="" autocomplete = "off">
                </div>
                </td>
                <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete = "off">
                </div>
                </td>
                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
       </tr>

       ';$x++;
   }

                }else{
                    while($row = mysqli_fetch_array($sql)){
                        
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
                            <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
                 
                        </tr>';
                      }
                      }                  
                    ?>
            <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'AR Collection') {
                    echo '';
                }elseif($ref == 'None'){
                    echo '</tbody>
                    
    </table>';
                }else{

        echo '</tbody>

            </table>';
            }
            ?>
<!--             <tfoot>
          <tr>
            <td colspan="9" align="center">
            <button type="button" class="btn btn-primary" onclick=addRow("tbody2");>Tambah Baris</button>
            <button type="button" class="btn btn-warning" onclick=InsertRow("tbody2");>Sisip Baris</button>
            <button type="button" class="btn btn-danger" onclick=deleteRow("tbody2");>Hapus Baris</button>
            </td>
          </tr>
    </tfoot>   -->                   
<div class="box footer">   
        <form id="form-simpan">
            
                <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    echo '';
                }else{

        echo '
        <div class="col-md-5 mb-1">
                </br>
             <div class="input-group">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control" name="accountid" id="accountid" data-live-search="true"'; echo"onchange='changeValueACC(this.value)'"; echo'required >
                <option value="" disabled selected="true">Select Account</option> ';?> 
                <?php 
                        $sqlacc = mysqli_query($conn1,"select bank,curr,account,RIGHT(account,4) as kode from ap_masterbank where status = 'Active'");
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
                if ($ref == 'None') {
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
                if ($ref == 'None') {
                    echo '';
                }
                elseif ($ref == 'AR Collection') {
                    $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail where coa_name like '%PIUTANG USAHA%'");
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
                $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail");
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
                if ($ref == 'None') {
                    echo '';
                }else{

        echo '</select>
                </div>
            </br>';
        }?>

            <?php 
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
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
                if ($ref == 'None') {
                    echo '';
                }
                else{                 
                $sql = mysqli_query($conn1,"select code_combine,cost_name from tbl_cost_center");
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
                if ($ref == 'None') {
                    echo '';
                }else{

        echo '</select>
                </div>
            </br>';
        }?>
                <?php
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    
                echo '';
            }
               else{ 
            echo '<div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>';}
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    
                echo '';
            }
               else{
                echo '

               <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h" name="nominal_h" placeholder="input nominal..." >';
               }
               
                $ref = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                if ($ref == 'None') {
                    echo '';
                }else{
                    echo '<input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Rate </b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly="">
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
                        newcell.children[i2].value = "";
                        newcell.children[i2].checked[0] = true;

                    }else{
                        newcell.children[i2].value = "";
                    }
                break;
                case "SELECT":
                    newcell.children[i2].value = "";
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
                var chkbox = row.cells[8].childNodes[0];
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
                var chkbox = row.cells[8].childNodes[0];
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
    $("input[name=nominal_h1]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h1').value,10) || 0;
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
    $("input[name=txt_amount").keyup(function(){

    var sum_amount = 0;
       
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

    sum_amount += amount;
   
    });

    $("#nominal_h2").val(sum_amount);  
    

    });
</script>

<script type="text/javascript">
    $("input[name=txt_credit").keyup(function(){

    var sum_amount2 = 0;
       
    $("input[type=checkbox]:checked").each(function () {        
    var amount2 = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    sum_amount2 += amount2;
     
    });

    $("#nominal_h3").val(sum_amount2);    

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

    valu = 'BM'+'/'+'BCA'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}else{
    valu = 'BM'+'/'+'BNI'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}

    
    });
   $("#no_doc").val(valu);


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
        if (refer != 'AR Collection') {
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
        var nominal2 = document.getElementById('nominal_h2').value; 
        var nominal3 = document.getElementById('nominal_h3').value; 
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';
        var balance = 0;
        balance = nominal - (nominal3 - nominal2);

        if(akun != '' && balance != '0'){
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
        }else if(document.getElementById('nominal_h1').value - (document.getElementById('nominal_h3').value - document.getElementById('nominal_h2').value) != '0'){
        document.getElementById('accountid').focus();
        alert("please enter account");
        }else{
            alert("Successful payment");
        }
                        

        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nomor_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val();     
        var cost_ctr = $(this).closest('tr').find('td:eq(2)').find('select[name=cost_ctr] option').filter(':selected').val();                   
        var no_ws = $(this).closest('tr').find('td:eq(3) input').val();
        var curre = $(this).closest('tr').find('td:eq(4)').find('select[name=currenc] option').filter(':selected').val();                   
        var debit = $(this).closest('tr').find('td:eq(5) input').val();
        var credit = $(this).closest('tr').find('td:eq(6) input').val();    
        var keterangan = $(this).closest('tr').find('td:eq(7) input').val();
        var nominal = document.getElementById('nominal_h1').value;
        var nominal2 = document.getElementById('nominal_h2').value;
        var balance = 0;
        balance = nominal - nominal2;

        if(debit != '' || credit != ''){   
        $.ajax({
            type:'POST',
            url:'insert_bankin.php',
            data: {'doc_number':doc_number, 'referen':referen, 'nomor_coa':nomor_coa, 'cost_ctr':cost_ctr, 'no_ws':no_ws, 'curre':curre, 'debit':debit, 'credit':credit, 'keterangan':keterangan},
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
 
        }else{
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
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';

        if(akun != '' && coa != '' && nominal != ''){
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
        } else{
            alert("Successful payment");
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
