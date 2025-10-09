<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

    #mytable2 thead th {
        position: sticky;
        top: 0;
        background-color: #2563EB !important;
        color: #fff !important;
        text-align: center;
        vertical-align: middle;
        z-index: 1;
    }



</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0 text-white" ><i class="fas fa-cash-register"></i> FORM PAYMENT</h5>
    </div>
    <form id="form-data" method="post">
        <div class="form-row mt-2">
            <div class="col-md-3 mb-3">            
                <label for="nopayment"><b>No payment</b></label>
                <?php
                $sql = mysqli_query($conn2,"select max(substr(payment_ftr_id,17,5)) no_pay from payment_ftr");
                $row = mysqli_fetch_array($sql);
                $kodepay = $row['no_pay'];
                $urutan = (int) $kodepay;
                $urutan ++;
                $bln = date("m");
                $thn = date("y");
                $huruf = "PAY/LP/NAG/$bln$thn/";
                $kodepay = $huruf . sprintf("%05s", $urutan);

                echo'<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="nopayment" name="nopayment" value="'.$kodepay.'">'
                ?>
            </div>
            <div class="col-md-2 mb-3">            
                <label for="tanggal"><b>payment Date<i style="color: red;">*</i></b></label>          
                <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggal(this.value)"
                value="<?php             
                if(!empty($_POST['h_tanggal'])) {
                    echo $_POST['h_tanggal'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" autocomplete='off'>
            </div>

            <div class="col-md-3 mb-3">            
                <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>            
                <select class="form-control form-control-sm selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoPAY()">
                    <option value="" disabled selected="true">Select Profit Center</option>                                                 
                    <?php
                    $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: null;               
                    $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    while ($row = mysqli_fetch_array($sql)) {
                        $data = $row['kode_pc'];
                        $data2 = $row['nama_pc'];
                        if($row['kode_pc'] == $profit_center ){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';

                        }
                        echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                    }?>
                </select>  

            </div>

        </div>

        <div class="form-row">


            <div class="col-md-7 mb-3">
                <label for="nama_supp"><b>Supplier</b></label>            
                <div class="input-group">
                    <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control form-control-sm" name="txt_supp" id="txt_supp" 
                    value="<?php 
                    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                    echo $nama_supp; 
                ?>">

                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" 
                value="<?php

                $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                $rowx = mysqli_fetch_array($sqlx);
                $maxid = $rowx['id'];

                $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid'");
                $rowy = mysqli_fetch_array($sqly);
                $rate = $rowy['rate'];    

                echo $rate;

            ?>">

            <div class="input-group-append col">
                <input 
                style="border: 0;
                line-height: 1;
                padding: 0 10px;
                font-size: 1rem;
                text-align: center;
                color: #fff;
                text-shadow: 1px 1px 1px #000;
                border-radius: 6px;
                background-color: rgb(95, 158, 160);"
                type="button"
                name="mysupp"
                id="mysupp"
                value="Select">
                <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
            </div>
        </div>
    </div>                   
</div>
</form>
<form id="form-simpan">
    <div class="row">

        <div class="col-md-12">

            <div class="table-responsive p-1">
                <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                    <thead>
                        <tr class="text-white" style="background-color: #1E3A8A;">
                            <th style="width:10px;"><i class="fa fa-hand-o-down fa-lg" aria-hidden="true"></i></th>
                            <th style="width:50px;">NO LP</th>
                            <th style="width:100px;">LP Date</th>                            
                            <th style="width:50px;display: none;">No KB</th>                            
                            <th style="width:100px;display: none;">KB Date</th>
                            <th style="width:200px;">Supplier</th>
                            <th style="width:100px;">Total KB</th> 
                            <th style="width:100px;">Tax (Pph)</th>                                                        
                            <th style="width:100px;">Valuta</th>
                            <th style="width:100px;">Amount</th>  
                            <th style="width:100px;display: none;">KB Date</th> 
                            <th style="width:100px;display: none;"></th>                          
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no_pay = '';
                        $tgl_pay = '';
                        $no_kbon = '';
                        $tgl_kbon = '';
                        $valuta= '';
                        $total = 0;  

                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                            $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                        }          

                        $sql = mysqli_query($conn2,"select list_payment.no_payment as no_pay, list_payment.tgl_payment as tgl_pay,list_payment.no_kbon as no_kbon, list_payment.tgl_kbon as tgl_kbon,list_payment.nama_supp as supplier, list_payment.total_kbon as total, list_payment.curr as valuta, SUM(list_payment.amount) as dibayar, list_payment.outstanding as outstanding, list_payment.pph_value as pph, ROUND(kontrabon_h.rate,2) as rate, list_payment.no_coa from list_payment left join kontrabon_h on kontrabon_h.no_kbon = list_payment.no_kbon where list_payment.tgl_payment between '$start_date' and '$end_date' and list_payment.nama_supp = '$nama_supp' and list_payment.profit_center = '$profit_center' and list_payment.status_int = '4' and total_kbon != 0 group by no_payment 
                            union
                            select no_pay, tgl_pay, no_kbon, IF(tgl_kbon is null,'-',tgl_kbon) as tgl_kbon, supplier, total, valuta, dibayar,outstanding, pph, rate, '' coa from saldo_awal where supplier = '$nama_supp' and status_int = '4'");

                        while($row = mysqli_fetch_array($sql)){                      
                            echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" class="dt_out" style="width:100px;" dataout1="'.$row['no_pay'].'">'.$row['no_pay'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout2="'.date("d F Y",strtotime($row['tgl_pay'])).'">'.date("d-M-Y",strtotime($row['tgl_pay'])).'</td>
                            <td style="width:50px; display : none;" class="dt_out" style="width:100px;" dataout3="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td style="width:100px; display : none;" class="dt_out" style="width:100px;" dataout4="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td style="width:100px;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="width:50px;" class="dt_out" style="width:100px;" dataout5="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="width:50px;" class="dt_out" style="width:100px;" data-data="'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout6="'.$row['valuta'].'">'.$row['valuta'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout7="'.$row['dibayar'].'">'.number_format($row['dibayar'],2).'</td>
                            <td style="display: none;" class="dt_out" style="width:100px;" data-rate="'.$row['rate'].'">'.number_format($row['rate'],2).'</td>
                            <td style="display: none;" class="dt_out" style="width:100px;" data-coalp="'.$row['no_coa'].'">'.$row['no_coa'].'</td>

                            </tr>';
                        }                  
                        ?>
                    </tbody>                    
                </table> 
            </div> 

        </br> 

        <div class="col-md-4 mb-3">
            <div class="mb-3 row align-items-center">
              <label for="dibayar" class="col-5 text-end fw-semibold">
                <b>Total List Payment</b>
            </label>
            <div class="col-7">
                <input type="text" class="form-control form-control-sm text-right" 
                id="dibayar" name="dibayar" readonly placeholder="0.00">
                <input type="hidden" class="form-control form-control-sm text-right" 
                id="dibayar_h" name="dibayar_h" readonly placeholder="0.00">
            </div>
        </div>

        <div class="mb-3 row align-items-center">
          <label for="valuta1" class="col-5 text-end fw-semibold">
            <b>Valuta</b>
        </label>
        <div class="col-7">
            <input type="text" class="form-control form-control-sm" 
            id="valuta1" name="valuta1" readonly>
        </div>
    </div>

</div>

<div class="table-responsive p-1 mt-3">
    <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr class="text-white" style="background-color: #2563EB;">
                <th class="text-center">-</th>
                <th class="text-center">COA</th>
                <th class="text-center">Profit Center</th>
                <th class="text-center">Cost Center</th>
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
            <td style="width: 200px;">
                <select class="form-control" name="nomor_profit" id="nomor_profit" style="width: 250px"> <option value="-" > - </option> <?php $sqlcoc = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'"); foreach ($sqlcoc as $coc) : echo'<option value="'.$coc["kode_pc"].'"> '.$coc["tampil"].' </option>'; endforeach; ?>
            </select>
        </td>
        <td style="width: 200px;">
            <select class="form-control select2abs4 nomor_coc" name="nomor_coc[]" id="nomor_coc" style="width: 250px"> <option value="-" > - </option>
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
    <td colspan="10" align="center">
        <button type="button" class="btn btn-primary" onclick="addRow2('tbody3')">Add Row</button>
        <button type="button" class="btn btn-warning" onclick="InsertRow2('tbody3')">Interject Row</button>
        <button type="button" class="btn btn-danger" onclick="hapus()">Delete Row</button>
    </td>
</tr>
</tfoot>                   
</table> 
</div>

<div class="col-md-4 mb-3">
    <!-- Total Debit -->
    <div class="mb-3 row align-items-center">
      <label for="tot_debit" class="col-5 text-end fw-semibold">
        <b>Total Debit</b>
    </label>
    <div class="col-7">
        <input type="text" class="form-control form-control-sm text-right" 
        id="nominaldeb" name="nominaldeb" readonly placeholder="0.00">
        <input type="hidden" id="nominaldeb_h" name="nominaldeb_h">
        <input type="hidden" id="jml_debit" name="jml_debit">
    </div>
</div>

<!-- Total Credit -->
<div class="mb-3 row align-items-center">
  <label for="tot_credit" class="col-5 text-end fw-semibold">
    <b>Total Credit</b>
</label>
<div class="col-7">
    <input type="text" class="form-control form-control-sm text-right" 
    id="nominalcre" name="nominalcre" readonly placeholder="0.00">
    <input type="hidden" id="nominalcre_h" name="nominalcre_h">
    <input type="hidden" id="jml_credit" name="jml_credit">
</div>
</div>

</div>


<!-- <div class="col-md-5 mb-2">
    <div class="input-group">
        <label for="carabayar" class="col-form-label" style="width: 150px;">Pay Methods </label>               
        <select class="form-control form-control-sm selectpicker " name="carabayar" id="carabayar" data-live-search="true">
            <option value="" disabled selected="true">Choose pay method</option>  
            <option value="TRANSFER">TRANSFER</option>  
            <option value="TUNAI">TUNAI</option>                      
            <option value="GIRO">GIRO</option>  
            <option value="CEK">CEK</option>  

        </select>        
    </div>
</br>

<div class="input-group">
    <label for="accountid" class="col-form-label" style="width: 150px;" >Account </label>  
    <select class="form-control form-control-sm" name="accountid" id="accountid" data-live-search="true" onchange='changeValueACC(this.value)' required >
        <option value="" disabled selected="true">Select Account</option>  
        <?php 
        $sqlacc = mysqli_query($conn1,"select * from masterbank ");
        $jsArray = "var prdName = new Array();\n";

        while ($row = mysqli_fetch_array($sqlacc)) {
            $data = $row['no_rek'];
            if($row['no_rek'] == $_POST['accountid']){
                $isSelected  = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
            $jsArray .= "prdName['" . $row['no_rek'] . "'] = {nama_bank:'" . addslashes($row['nama_bank']) . "',valuta:'".addslashes($row['curr'])."'};\n";
        }
        ?>
    </select>

</div>
</br>

<div class="input-group" >
    <label for="nama_supp" class="col-form-label" style="width: 150px;">Bank </label>                  
    <input type="text" style="font-size: 12px;" class="form-control form-control-sm" id="nama_bank" name="nama_bank" readonly > 
</div>
</br>

<div class="input-group" >
    <label for="nama_supp" class="col-form-label" style="width: 150px;">Valuta </label>         
    <input type="text" style="font-size: 12px;" class="form-control form-control-sm" id="valuta" name="valuta" readonly >         
</div>
</br>

<div class="input-group" >
    <label for="nama_supp" class="col-form-label" style="width: 150px;">Nominal </label>
    <input type="text" style="font-size: 14px;text-align: right;" class="form-control form-control-sm" id="nominal" name="nominal" placeholder="0.00" readonly>
    <input type="hidden" name="nominal_h" id="nominal_h" value="">
</div>
</br>
<div class="input-group" >
    <label for="nama_supp" class="col-form-label" style="width: 150px;">Rate </label>
    <input type="text" style="font-size: 14px;text-align: right;" class="form-control form-control-sm" id="rate" name="rate" placeholder="input rate here..." >
    <input type="text" style="font-size: 14px;text-align: right;" class="form-control form-control-sm" id="rate_h" name="rate_h" placeholder="0.00" readonly="">
</div>
</br>
<div class="input-group" >
    <label for="nama_supp" class="col-form-label" style="width: 150px;">Nominal After Rate</label>
    <input type="text" style="font-size: 14px;text-align: right;" class="form-control form-control-sm" id="nomrate" name="nomrate" placeholder="0.00" readonly>
    <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
</div>
</br>

</div> -->

<div class="form-row col">
    <div class="col-md-3 mb-3">                              
        <button type="button" style="border-radius: 6px" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
        <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='pelunasanftr.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
    </div>
</div>                                    
</form>
</div>


<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-lg rounded-3">

      <!-- Header -->
      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data</h4>
    </div>

    <!-- Body -->
    <div class="modal-body">
        <form id="modal-form" method="post">

          <!-- Supplier -->
          <div class="form-group mb-3">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control form-control-sm selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
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

        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="h_profit_center" class="form-control form-control-sm form-control form-control-sm-sm" value="">
        <input type="hidden" style="font-size: 13px;" name="h_tanggal" id="h_tanggal" class="form-control form-control-sm" value="">

        <!-- BPB Date -->
        <div class="form-group">
            <label><b>List Payment Date</b></label>
            <div class="d-flex align-items-center gap-2">
               <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="start_date" name="start_date" 
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

            <span class="mx-2">-</span>

            <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="end_date" name="end_date" 
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
</div>

</form>
</div>

<!-- Footer -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">
      <i class="fa fa-times"></i> Close
  </button>
  <button type="submit" form="modal-form" id="send" name="send" class="btn btn-warning">
      <i class="fa fa-search" aria-hidden="true"></i> Search
  </button>
</div>

</div>
</div>
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
                  <!--           <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
<!--           <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                                          
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#mysupp").on("click", function() {
            let profit_center = $('select[name=profit_center] option').filter(':selected').val();
            if (profit_center == "") {
                alert("Please Input Profit Center.");
                $("#profit_center").focus();
                return;
            }

            $('#h_profit_center').val(profit_center);
            let tanggal = document.getElementById('tanggal').value;
            $('#h_tanggal').val(tanggal);
            $("#mymodal").modal("show");
        });
    });

    function updateNoPAY() {
        let pc = document.getElementById("profit_center").value;  
        let input = document.getElementById("nopayment");  
        let current = input.value.trim();  

    if (!current) return; // kalau kosong, skip

    let parts = current.split("/");

    if (parts.length === 5) {
        // LP/NAG/0925/00049
        parts[2] = pc; // replace profit center
        input.value = parts.join("/");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let savedPc = localStorage.getItem("profit_center");
    if (savedPc) {
        document.getElementById("profit_center").value = savedPc;
        updateNoPAY();
    }
    let tanggal = document.getElementById('tanggal').value;
    ubahtanggal(tanggal);
});

document.getElementById("profit_center").addEventListener("change", function() {
    localStorage.setItem("profit_center", this.value);
    document.querySelector("#mytable tbody").innerHTML = "";
    updateNoPAY();
});

var tgl = 0;
var tgl2 = '';
function ubahtanggal(value) {  
    var tanggal = document.getElementById('tanggal').value;

    $.ajax({
        type: 'POST',
        url: 'getnomor_pelunasan.php',
        data: { tanggal: tanggal },
        dataType: 'json', // pastikan parse JSON otomatis
        success: function(response) {
            // response.nomor dan response.tgl bisa dipakai
            $('#nopayment').val(response.nomor);
            $('#rate').val(response.rate); 
            updateNoPAY();
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}




$(document).on('change', '.nomor_profit', function () {
    const selectedProfCtr = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedCoa = row.find('select.no_coa').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});

$(document).on('change', '.nomor_coa', function () {
    const selectedCoa = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedProfCtr = row.find('select.nomor_profit').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});



// Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
function updateCostCenter(profCtr, noCoa, row) {
    const costCtrDropdown = $(row).find('.nomor_coc'); // dropdown cost center pada baris tsb

    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getCostCenter.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { prof_ctr: profCtr , no_coa: noCoa },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                if (response && response.length > 0) {
                    $.each(response, function (index, costCtr) {
                        costCtrDropdown.append(
                            `<option value="${costCtr.value}">${costCtr.text}</option>`
                            );
                    });

                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.warn('Tidak ada data cost center dari server.');
                    costCtrDropdown.selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    } else {
        costCtrDropdown.selectpicker('refresh');
    }
}


   // Initialize external plugins
   function initializePlugins() {
    $(function () {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
}



   // JavaScript Document
   function addRow2(tableID) {
    var tableID = "tbody3";
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
    $(document).ready(function () {
      $('.select2add').select2({
        theme: 'bootstrap4'
    })
  });
    $coa = '';
    var element1 = `
    <tr>
    <td>
    <input type="checkbox" id="select" name="select[]" value="" checked disabled>
    </td>
    <td style="width: 50px;">
    <select class="form-control selectpicker nomor_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
    <option value="-">-</option>
    <?php 
    $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2");
    foreach ($sql as $coa) : ?>
        <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
    <?php endforeach; ?>
    </select>
    </td>
    <td>
    <select class="form-control selectpicker nomor_profit" name="nomor_profit" id="nomor_profit" data-live-search="true" data-width="200px" data-size="5">
    <option value="-"> - </option>
    <?php
    $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
    foreach ($sql3 as $fc) : ?>
        <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
    <?php endforeach; ?>
    </select>
    </td>
    <td>
    <select class="form-control selectpicker nomor_coc" name="nomor_coc[]" id="nomor_coc" data-live-search="true" data-width="200px" data-size="5">
    <option value="-"> - </option>
    </select>
    </td>
    <td>
    <input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off">
    </td>
    <td>
    <input type="text" style="font-size: 12px;width: 150px;" name="tgl_active" id="tgl_active" class="form-control tanggal" autocomplete="off" placeholder="dd-mm-yyyy">
    </td>
    <td>
    <input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off">
    </td>
    <td>
    <input style="text-align: right; font-size: 12px;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off">
    </td>
    <td>
    <input style="text-align: right; font-size: 12px;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off">
    </td>
    <td>
    <input name="chk_a[]" type="checkbox" class="checkall_a" value="">
    </td>
    </tr>
    `;



    row.innerHTML = element1;  
    initializePlugins();
    // $('.selectpicker').selectpicker('refresh');

    var headerPC = $('#profit_center').val();
    if (headerPC) {
        $(row).find('.nomor_profit').val(headerPC);
        $(row).find('.nomor_profit').selectpicker('refresh');
        // updateCostCenter(headerPC, row);
    }  
}

function InsertRow2(tableID)
{
    try{
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        for(var i=0; i<rowCount; i++)
        {
            var row = table.rows[i];
            var chkbox = row.cells[row.cells.length - 1]?.querySelector("input[type='checkbox'].checkall_a");
            if (null != chkbox && true == chkbox.checked)
            {
                $(function() {
                    $('.selectpicker').selectpicker();

                });

                $(document).ready(function () {
                    $('.tanggal').datepicker({
                        format: "dd-mm-yyyy",
                        autoclose:true
                    });
                });
                var element2 = `
                <tr>
                <td>
                <input type="checkbox" id="select" name="select[]" value="" checked disabled>
                </td>
                <td style="width: 50px;">
                <select class="form-control selectpicker nomor_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
                <option value="-">-</option>
                <?php 
                $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2");
                foreach ($sql as $coa) : ?>
                    <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                <?php endforeach; ?>
                </select>
                </td>
                <td>
                <select class="form-control selectpicker nomor_profit" name="nomor_profit" id="nomor_profit" data-live-search="true" data-width="200px" data-size="5">
                <option value="-"> - </option>
                <?php
                $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                foreach ($sql3 as $fc) : ?>
                    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
                <?php endforeach; ?>
                </select>
                </td>
                <td>
                <select class="form-control selectpicker nomor_coc" name="nomor_coc[]" id="nomor_coc" data-live-search="true" data-width="200px" data-size="5">
                <option value="-"> - </option>
                </select>
                </td>
                <td>
                <input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off">
                </td>
                <td>
                <input type="text" style="font-size: 12px;width: 150px;" name="tgl_active" id="tgl_active" class="form-control tanggal" autocomplete="off" placeholder="dd-mm-yyyy">
                </td>
                <td>
                <input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off">
                </td>
                <td>
                <input style="text-align: right; font-size: 12px;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off">
                </td>
                <td>
                <input style="text-align: right; font-size: 12px;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off">
                </td>
                <td>
                <input name="chk_a[]" type="checkbox" class="checkall_a" value="">
                </td>
                </tr>
                `;
                var newRow = table.insertRow(i+1);
                newRow.innerHTML = element2;
                initializePlugins();
    // $('.selectpicker').selectpicker('refresh');

    var headerPC = $('#profit_center').val();
    if (headerPC) {
        $(row).find('.nomor_profit').val(headerPC);
        $(row).find('.nomor_profit').selectpicker('refresh');
        // updateCostCenter(headerPC, row);
    }

}

}
} catch(e)
{
    alert(e);
}
}

async function hapus(){
 await deleteRow();
 console.log("hasil");
 hitungulang();
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
        var chkbox = row.cells[row.cells.length - 1]?.querySelector("input[type='checkbox'].checkall_a");
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
    var total_deb = parseFloat(document.getElementById('dibayar_h').value,10) || 0;
    for(var i=0; i<rowCount2; i++){

        var price = parseFloat(document.getElementById("tbody3").rows[i].cells[6].children[0].value,10) || 0;
        var price2 = parseFloat(document.getElementById("tbody3").rows[i].cells[7].children[0].value,10) || 0;

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

function modal_input_amt(){ 
    var val = document.getElementById('rate').value;
    var tot_pay = parseFloat(document.getElementById('dibayar_h').value,10) || 0; 
    var tot_pay2 = parseFloat(document.getElementById('dibayar_h').value,10) || 0;     
    var table = document.getElementById("tbody3");
    var tota = 0;
    var harga = 0;
    var totall = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var price = document.getElementById("tbody3").rows[i].cells[7].children[0].value;
        var price2 = document.getElementById("tbody3").rows[i].cells[8].children[0];
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
function modal_input_cre(){ 
    var tot_pay = 0;   
    var table = document.getElementById("tbody3");
    var tota = 0;
    var harga = 0;
    var totall = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var price = document.getElementById("tbody3").rows[i].cells[8].children[0].value;
        var price2 = document.getElementById("tbody3").rows[i].cells[7].children[0];
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
    $(document).ready(function () {
        $('.tanggal3').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

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
        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#mytable2').DataTable({
            paging: false,          // Menambahkan paging
            searching: true,       // Menambahkan pencarian
            scrollCollapse: true,  // Mengatasi jika data tidak cukup
            fixedHeader: true,     // Menjaga header tetap terlihat
            language: {
            info: "", // Menghilangkan teks "Showing 1 to 1 of 1 entries"
            infoEmpty: "", // Untuk keadaan ketika tidak ada data
            infoFiltered: "" // Untuk keadaan filter
        }    // Menjaga header tetap terlihat
    });

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
    var no_pay = '';
    var tgl_pay = '';
    var no_kbon = '';
    var tgl_kbon = '';
    var valuta = '';
    var total = 0;
    var dibayar = 0;
    var sisa = 0;
    var buka = '';
    var total_debit = 0;

    var table = document.getElementById("tbody3");
    var tota = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var price = document.getElementById("tbody3").rows[i].cells[7].children[0].value;
        var price2 = document.getElementById("tbody3").rows[i].cells[8].children[0];
        if (price == '') {
            harga = 0;
            price2.readOnly = false;
        }else{
            harga = price;
            price2.readOnly = true;
        }
        tota += parseFloat(harga);
    }

    $("#mytable input[type=checkbox]:checked").each(function () {     
        var pay = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
        var tglpay = $(this).closest('tr').find('td:eq(2)').attr('dataout2');
        var kbon = $(this).closest('tr').find('td:eq(3)').attr('dataout3');
        var tglkbon = $(this).closest('tr').find('td:eq(4)').attr('dataout4');
        var val = $(this).closest('tr').find('td:eq(8)').attr('dataout6');
        var ttl = parseFloat($(this).closest('tr').find('td:eq(6)').attr('dataout5'),10) || 0;
        var dbayar = parseFloat($(this).closest('tr').find('td:eq(9)').attr('dataout7'),10) || 0;    

        no_pay = pay;
        tgl_pay = tglpay; 
        no_kbon = kbon;
        tgl_kbon = tglkbon;
        valuta = val;
        total = ttl;
        dibayar += dbayar;
        total_debit = dibayar + tota;


    });

    $("#valuta1").val(valuta);
    $("#total").val(formatMoney(total));
    $("#dibayar").val(formatMoney(dibayar));
    $("#dibayar_h").val(dibayar);
    document.getElementsByName("nominaldeb")[0].value = formatMoney(total_debit.toFixed(2));
    document.getElementsByName("nominaldeb_h")[0].value = total_debit.toFixed(2);
    document.getElementsByName("jml_debit")[0].value = total_debit.toFixed(2);               
});        
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
        var ttl_jml = 0;
        var rat = 0;
        var valu = '';
        $("input[type=text]").each(function () {         
            var rate = parseFloat(document.getElementById('rate').value,10) || 1;
            var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
            var val = document.getElementById('valuta1').value;
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
        $("input[type=checkbox]:checked").each(function () {
            var payment_ftr_id = document.getElementById('nopayment').value;        
            var tgl_pelunasan = document.getElementById('tanggal').value;        
            var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();    
            var profit_center = $('select[name=profit_center] option').filter(':selected').val();

            var list_payment_id = $(this).closest('#mytable tr').find('td:eq(1)').attr('dataout1');                          
            var tgl_list_payment = $(this).closest('#mytable tr').find('td:eq(2)').attr('dataout2');
            var no_kbon = $(this).closest('#mytable tr').find('td:eq(3)').attr('dataout3')
            var tgl_kbon = $(this).closest('#mytable tr').find('td:eq(4)').attr('dataout4');
            var valuta_ftr = document.getElementById('valuta1').value;        
            var ttl_bayar = parseFloat($(this).closest('#mytable tr').find('td:eq(9)').attr('dataout7'),10) ||0;
            var cara_bayar = 'TRANSFER'; 
            var account = '-';        
            var bank = '-';
            var valuta_bayar = document.getElementById('valuta1').value; 
            if (valuta_ftr == 'IDR') {
                var rate = 1;
            }else{
                var rate = document.getElementById('rate').value;
            }
            var nomrate = 0;
            var nominal = parseFloat($(this).closest('#mytable tr').find('td:eq(9)').attr('dataout7'),10) || 0;
            var create_user = '<?php echo $user ?>';
            var dibayar = parseFloat($(this).closest('#mytable tr').find('td:eq(9)').attr('dataout7'),10) || 0;
            var ratebpb = parseFloat($(this).closest('#mytable tr').find('td:eq(10)').attr('data-rate'),10) || 0;
            var coa_lp = $(this).closest('#mytable tr').find('td:eq(11)').attr('data-coalp');

            var no_coa = $(this).closest('#mytable2 tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val(); 
            var prof_ctr = $(this).closest('#mytable2 tr').find('td:eq(2)').find('select[id=nomor_profit] option').filter(':selected').val();
            var no_coc = $(this).closest('#mytable2 tr').find('td:eq(3)').find('select[id=nomor_coc] option').filter(':selected').val();    
            var reff_doc = $(this).closest('#mytable2 tr').find('td:eq(4) input').val();
            var reff_date = $(this).closest('#mytable2 tr').find('td:eq(5) input').val();
            var deskripsi = $(this).closest('#mytable2 tr').find('td:eq(6) input').val();
            var debit = $(this).closest('#mytable2 tr').find('td:eq(7) input').val(); 
            var credit = $(this).closest('#mytable2 tr').find('td:eq(8) input').val();

            var fil_lp = parseFloat(document.getElementById('dibayar_h').value,10) || 0;
            var fil_debit = parseFloat(document.getElementById('nominaldeb_h').value,10) || 0;
            var fil_credit = parseFloat(document.getElementById('nominalcre_h').value,10) || 0;
            nomrate = nominal * rate;

            if(fil_lp > 0 && fil_debit == fil_credit){  
                $.ajax({
                    type:'POST',
                    url:'insertrepaymentftr.php',
                    data: {'payment_ftr_id':payment_ftr_id, 'tgl_pelunasan':tgl_pelunasan, 'nama_supp':nama_supp,'profit_center':profit_center,'list_payment_id':list_payment_id, 'tgl_list_payment':tgl_list_payment, 'no_kbon':no_kbon,'tgl_kbon':tgl_kbon, 'valuta_ftr':valuta_ftr, 'ttl_bayar':ttl_bayar,'cara_bayar':cara_bayar, 'account':account, 'bank':bank, 'valuta_bayar':valuta_bayar, 'nominal':nominal, 'dibayar':dibayar, 'rate':rate, 'nomrate':nomrate, 'create_user':create_user, 'ratebpb':ratebpb, 'no_coa':no_coa, 'prof_ctr':prof_ctr, 'no_coc':no_coc, 'reff_doc':reff_doc, 'reff_date':reff_date, 'deskripsi':deskripsi, 'debit':debit, 'credit':credit, 'coa_lp':coa_lp},
                    cache: 'false',
                    close: function(e){
                        e.preventDefault();
                    },
                    success: function(response){
                        localStorage.removeItem("profit_center");
                        console.log(response);
                // alert(response);
                window.location = 'pelunasanftr.php';
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
            }

        });     

if(document.querySelectorAll("#mytable input[name='select[]']:checked").length == 0){
    alert("Please check the List Payment number");            
} else if((parseFloat(document.getElementById('dibayar_h').value,10) || 0) <= 0){
    alert("Please check the List Payment number");
} else if((parseFloat(document.getElementById('nominaldeb_h').value,10) || 0) != (parseFloat(document.getElementById('nominalcre_h').value,10) || 0)){
    alert("Please Check Total Credit and Debit");
}  else{
    alert("Successful payment");
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
        var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
        var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
        var supp = $(this).closest('tr').find('td:eq(5)').attr('value');
        var tgl_tempo = $(this).closest('tr').find('td:eq(8)').text();
        var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
        var create_user = $(this).closest('tr').find('td:eq(11)').attr('value');
        var status = $(this).closest('tr').find('td:eq(10)').attr('value');
        var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
        var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
        var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

        $.ajax({
            type : 'post',
            url : 'ajaxf_kbon.php',
            data : {'no_kbon': no_kbon},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_kbon').html(no_kbon);
        $('#txt_tgl_kbon').html('List Payment Date : ' + tgl_kbon + '');
        $('#txt_nama_supp').html('Supplier : ' + supp + '');
        $('#txt_tgl_tempo').html('Currency : ' + tgl_tempo + '');
        $('#txt_curr').html('Currency : ' + curr + '');        
        $('#txt_create_user').html('Create By : ' + create_user + '');
        $('#txt_status').html('Status : ' + status + '');
        $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
        $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
        $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
    });

</script>
<script type="text/javascript"> 
    <?php echo $jsArray; ?>
    function changeValueACC(id){
        document.getElementById('nama_bank').value = prdName[id].nama_bank;
        document.getElementById('valuta').value = prdName[id].valuta;
    };
</script>

<script type="text/javascript">
    $(document).ready(function(){
        // Format mata uang.
        $( '.uang' ).mask('0.000.000.000', {reverse: true});
    })
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
