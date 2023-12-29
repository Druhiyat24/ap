<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM KONTRA BON</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="nokontrabon"><b>No Kontra Bon</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(no_kbon) from kontrabon");
            $row = mysqli_fetch_array($sql);
            $kodeBarang = $row['max(no_kbon)'];
            $urutan = (int) substr($kodeBarang, 15, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("Y");
            $huruf = "SI/APR/$thn/$bln/";
            $kodeBarang = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nokontrabon" name="nokontrabon" value="'.$kodeBarang.'">'
            ?>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>Kontra Bon Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="jurnal"><b>Journal</b></label>            
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="jurnal" name="jurnal" 
            value="0" placeholder="<?php echo "KONTRA BON" ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="matauang"><b>Currency</b></label>
            <?php
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select curr from bpb_new where supplier = '$nama_supp'");
            $row = mysqli_fetch_array($sql);
            $value = isset($row['curr']) ? $row['curr'] : null;
            if (!empty($nama_supp)) {
            echo '<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="matauang" name="matauang" value="'.$value.'">';   
            } else {
            echo '<input type="text" readonly class="form-control-plaintext" id="matauang" name="matauang" value="">';
            }
            ?>                        
            </div>                                         
    </div>

    <div class="form-row">
            <div class="col-md-6 mb-3">            
            <label for="no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>            
            <input type="text" style="font-size: 14px;" class="form-control" id="no_faktur" name="no_faktur" 
            value="<?php 
            $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: null;
            echo $no_faktur; 
            ?>" required>
            </div>

            <div class="col-md-6 mb-3">            
            <label for="txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" class="form-control" id="txt_inv" name="txt_inv" 
            value="<?php
            $txt_inv = isset($_POST['txt_inv']) ? $_POST['txt_inv']: null;
                echo $txt_inv; 
            ?>" required>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal" name="txt_tglsi" id="txt_tglsi" 
            value="<?php 
            if(!empty($_POST['txt_tglsi'])) {
                echo $_POST['txt_tglsi'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal1" name="txt_tgltempo" id="txt_tgltempo" 
            value="<?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }            
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_bpb)'];
            $top = $row['top'];

            if(!empty($nama_supp)) {
                echo date("d-m-Y",strtotime($tgl . "+$top days"));
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

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

            <div class="col-md-6 mb-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 500px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">

    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Supplier</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
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

                <label><b>BPB Date</b></label>
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

            <div class="input-group-append col">
            <input style="border: 0;
    line-height: 1;
    padding: 0 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">
            <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
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
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                            <th style="width:50px;">NO BPB</th>
                            <th style="width:50px;">NO PO</th>                            
                            <th style="width:50px;">BPB Date</th>                            
                            <th style="width:100px;">SubTotal</th>
                            <th style="width:100px;">Tax (PPn)</th>
                            <th style="width:100px;">Tax (PPh)</th>                            
                            <th style="width:100px;">Total (BPB)</th>
                            <th style="width:80px;">Total CBD/DP</th>
                            <th style="width:100px;display: none;">TOP</th>
                            <th style="width:100px;display: none;">Confirm1</th>
                            <th style="width:100px;display: none;">Confirm2</th>
                            <th style="width:100px;display: none;">Supplier</th>                                                       
                            <!--<th style="width:50px;">Delete</th>-->
                        </tr>
                    </thead>

            <tbody>
            <?php
            $start_date ='';
            $end_date ='';
            $sub = '';
            $tax = '';
            $total = '';
            $persen = '';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }
            $querys = mysqli_query($conn2,"select distinct (no_bpb) from bpb_new");
            $rows = mysqli_fetch_array($querys);
            $no_bpb = isset($rows['no_bpb']) ?  $rows['no_bpb'] : null;


            $q = mysqli_query($conn2,"select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");            
            while($rs = mysqli_fetch_array($q)){
            $persen .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'">'.$rs['kriteria'].'</option>';
            }                        
            
            $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, tgl_po, SUM(qty * price) as sub, SUM((qty * price) * (tax / 100)) as tax, SUM((qty * price) + ((qty * price) * (tax / 100))) as total, top, confirm1, confirm2, supplier, tgl_po from bpb_new where supplier = '$nama_supp' and tgl_bpb between '$start_date' and '$end_date' and is_invoiced != 'Invoiced' and confirm2 != '' group by no_bpb");                                                     
            while($row = mysqli_fetch_array($sql)){
            $bpb = $row['no_bpb'];
            $pono = $row['pono'];

             $Aquer = mysqli_query($conn2,"select no_po, amount from list_payment_dp where no_po = '$pono' and status = 'Approved' and status_int = '5'");
            $b = mysqli_fetch_array($Aquer);
            $po_no = isset($b['no_po']) ? $b['no_po'] : null ;
            $dp = isset($b['amount']) ? $b['amount'] : 0 ;

            $Aquerr = mysqli_query($conn2,"select no_po, amount_update from list_payment_cbd where no_po = '$pono' and status = 'Approved' and status_int = '5'");
            $c = mysqli_fetch_array($Aquerr);
            $po_nno = isset($c['no_po']) ? $c['no_po'] : null ;
            $cbd = isset($c['amount_update']) ? $c['amount_update'] : 0 ;


            $querys = mysqli_query($conn2,"select no_bpb, status from kontrabon where no_bpb = '$bpb' and status != 'Cancel'");
            $rows = mysqli_fetch_array($querys);
            $n_bpb = isset($rows['no_bpb']);
            $stat = isset($rows['status']);                            
            $sub = $row['sub'];
            $tax = $row['tax'];
            $total = $row['total'];
            $dpnol = 0;
            if($bpb == $n_bpb and $stat != 'Cancel'){
                echo '';
            }elseif($pono == $po_no){
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data="'.$dp.'">'.number_format($dp,2).'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                                                                                                                
                        </tr>';
                }elseif($pono == $po_nno){
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data="'.$cbd.'">'.number_format($cbd,2).'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                                                                                                                
                        </tr>';
                } else{
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data="'.$dpnol.'">'.number_format($dpnol,2).'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                                                                                                                
                        </tr>';
                }            
                }  
              
                    ?>
            </tbody>                    
            </table>                    
<div class="col-md-12">   
        <form id="form-simpan">
        </br>
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>SubTotal BPB</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="subtotal_h" id="subtotal_h" value="">
            </div>
            </div>
            <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">-</th>
                            <th style="width:50px;">No RO</th>
                            <th style="width:50px;">NO BPPB</th>
                            <th style="width:50px;">BPPB Date</th>                            
                            <th style="width:50px;">NO BPB</th>                            
                            <th style="width:100px;">Total</th>                                                    
                        </tr>
                    </thead>

            <tbody>
            <?php

            $querys = mysqli_query($conn2,"select no_bppb, tgl_bppb, no_ro, no_bpb, sum(qty * price) as total from bppb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' GROUP BY no_ro");


            while($row1 = mysqli_fetch_array($querys)){
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" valuess="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                            <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                            <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                            <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                            <td style="width:100px;text-align:right;" data-total-ro="'.$row1['total'].'">'.number_format($row1['total'],2).'</td>                                                                                                 
                        </tr>';
                    }
                    ?>
            </tbody>                    
            </table>  
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Potongan</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="potongan" id="potongan" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            </div> 
            <div class="box footer col-md-6">
                </br>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Laba Rugi Kurs</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="labarugi" id="labarugi" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="labarugi_h" id="labarugi_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Selisih Quantity</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="selisihqty" id="selisihqty" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="selisihqty_h" id="selisihqty_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Selisih Harga</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="selisihharga" id="selisihharga" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="selisihharga_h" id="selisihharga_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Biaya Materai</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="materai" id="materai" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="materai_h" id="materai_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Potongan Pembelian</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="potongbeli" id="potongbeli" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="potongbeli_h" id="potongbeli_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Biaya Expedisi</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="ekspedisi" id="ekspedisi" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ekspedisi_h" id="ekspedisi_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Biaya MOQ</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="number" class="form-control" name="moq" id="moq" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
             <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="moq_h" id="moq_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Jumlah Potongan</b></label>
            <div class="col-md-9 mb-3">                              
                <input type="text" class="form-control-plaintext" name="jumlahpotong" id="jumlahpotong" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                 <input type="hidden" name="jml_potong" id="jml_potong" value="">
            </div>
            </div>
        </div>
            <div class="box footer col-md-6">
        </br>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 100px;"><b>SubTotal</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="sisapotongan" id="sisapotongan" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="ttl_sub" id="ttl_sub" value="">
                 <input type="hidden" name="ttl_sub1" id="ttl_sub1" value="">
                 <input type="hidden" name="ttl_sub2" id="ttl_sub2" value="">
                 <input type="hidden" name="ttl_sub3" id="ttl_sub3" value="">
                 <input type="hidden" name="ttl_sub4" id="ttl_sub4" value="">
                 <input type="hidden" name="ttl_sub5" id="ttl_sub5" value="">
                 <input type="hidden" name="ttl_sub6" id="ttl_sub6" value="">
                 <input type="hidden" name="ttl_sub7" id="ttl_sub7" value="">
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Tax (PPn)</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pajak_h" id="pajak_h" value="">
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 100px;"><b>Tax (PPh)</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pph" id="pph" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="">
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>BPB</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ttl_bpb" id="ttl_bpb" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="ttl_bpb_h" id="ttl_bpb_h" value="">
            </div>
            </div>
            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Total Cash</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ttl_dp" id="ttl_dp" value="" placeholder="0.00"  style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="ttl_dp_h" id="ttl_dp_h" value="">
            </div>
            </div>

            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Total</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="total_h" id="total_h" value="">
                <input type="hidden" name="po" id="po" value="">
                <input type="hidden" name="cbd" id="cbd" value="">
            </div>
            </div>
           <div class="form-row col">
            <div class="col-md-6 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='kontrabon.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalbpb" data-target="#mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
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

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal1').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true,
        startDate: new Date()
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
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_bpb = 0;
    var sum_total = 0;
    var total = 0;
    var sisa = 0;
    var sum_cbd = 0;
    var dp = 0;
    var nopo= '';
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () {   
    var po = $(this).closest('tr').find('td:eq(2)').attr('value');       
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
    var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
    var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
    select_pph.prop('disabled', false);  
sum_sub += price;
    total += price_ro;
    sisa = sum_sub - total;
    sum_tax += tax;
    sum_pph += price * (pph / 100);
    sum_bpb = sisa + sum_tax - sum_pph;     
    sum_cbd = cbd; 
    if (sum_bpb <= a) {
    nopo = po;
    dp = sum_bpb;
    sum_total = sum_bpb - dp; 
}else{
    nopo = po;
    dp = a;
    sum_total = sum_bpb - dp;

}

    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_tax));
    $("#pajak_h").val(sum_tax);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#ttl_bpb").val(formatMoney(sum_bpb));
    $("#ttl_dp").val(formatMoney(dp));
     $("#ttl_dp_h").val(dp);
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);
    $("#po").val(nopo); 
    $("#cbd").val(sum_cbd); 
    $("#potongan").val(formatMoney(total));
    $("#sisapotongan").val(formatMoney(sisa));
    $("#ttl_sub").val(sisa);
    $("#select").val("1");                      
});        
</script>

<script type="text/javascript">
    $("select[name=combo_pph]").on('change', function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_bpb = 0;
    var sum_total = 0;
    var sum_cbd = 0;
    var total = 0;
    var sisa = 0;
    var dp = 0;
    var nopo = '';
    $("input[type=checkbox]:checked").each(function () {  
    var po = $(this).closest('tr').find('td:eq(2)').attr('value');      
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
    var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;            
       sum_sub += price;
        total += price_ro;
    sisa = sum_sub - total;
    sum_tax += tax;
    sum_pph += price * (pph / 100);
    sum_bpb = sisa + sum_tax - sum_pph; 
    sum_cbd = cbd;     
    if (sum_bpb <= a) {
    nopo = po;
    dp = sum_bpb;
    sum_total = sum_bpb - dp; 
}else{
    nopo = po;
    dp = a;
    sum_total = sum_bpb - dp;
}
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_tax));
    $("#pajak_h").val(sum_tax);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#ttl_bpb").val(formatMoney(sum_bpb));
    $("#ttl_dp").val(formatMoney(dp));
    $("#ttl_dp_h").val(dp);
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);
    $("#po").val(nopo);
    $("#cbd").val(sum_cbd); 
    $("#ttl_sub").val(sisa);
    $("#select").val("1");
    });
</script>


<script type="text/javascript">
    $("input[name=labarugi]").keyup(function(){
    var laba = 0; 
    var jml1 = 0;
    var ttl_jml1 = 0;
    $("input[type=text]").each(function () {         
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml1 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;              
    laba = laba_h;
    ttl_jml1 = ttl_h - jml1; 
    });
   $("#labarugi_h").val(formatMoney(laba));
   $("#jumlahpotong").val(formatMoney(jml1));
   $("#jml_potong").val(jml1);
   $("#sisapotongan").val(formatMoney(ttl_jml1));
   $("#ttl_sub2").val(ttl_jml1);

    });
</script>

<script type="text/javascript">
    $("input[name=selisihqty]").keyup(function(){
    var selisih = 0;
    var jml2 = 0; 
    var ttl_jml2 = 0;
    $("input[type=text]").each(function () {         
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml2 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;                
    selisih = selisih_h;
    ttl_jml2 = ttl_h - jml2; 
    });
   $("#selisihqty_h").val(formatMoney(selisih));
   $("#jumlahpotong").val(formatMoney(jml2));
   $("#jml_potong").val(jml2);
   $("#sisapotongan").val(formatMoney(ttl_jml2));
   $("#ttl_sub1").val(ttl_jml2);
    });
</script>

<script type="text/javascript">
    $("input[name=selisihharga]").keyup(function(){
    var selisihhrg = 0; 
    var jml3 = 0;
    var ttl_jml3 = 0;
    $("input[type=text]").each(function () {         
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml3 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;                 
    selisihhrg = selisihhrg_h;
    ttl_jml3 = ttl_h - jml3; 
    });
   $("#selisihharga_h").val(formatMoney(selisihhrg));
   $("#jumlahpotong").val(formatMoney(jml3));
   $("#jml_potong").val(jml3);
   $("#sisapotongan").val(formatMoney(ttl_jml3));
   $("#ttl_sub3").val(ttl_jml3);
    });
</script>

<script type="text/javascript">
    $("input[name=materai]").keyup(function(){
    var mater = 0; 
    var jml4 = 0;
    var ttl_jml4 = 0;
    $("input[type=text]").each(function () {         
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml4 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;                
    mater = mater_h;
    ttl_jml4 = ttl_h - jml4; 
    });
   $("#materai_h").val(formatMoney(mater));
   $("#jumlahpotong").val(formatMoney(jml4));
   $("#jml_potong").val(jml4);
   $("#sisapotongan").val(formatMoney(ttl_jml4));
   $("#ttl_sub4").val(ttl_jml4);
    });
</script>

<script type="text/javascript">
    $("input[name=potongbeli]").keyup(function(){
    var potongbeli = 0; 
    var jml5 = 0;
    var ttl_jml5 = 0;
    $("input[type=text]").each(function () {        
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml5 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;                 
    potongbeli = potongbeli_h;
    ttl_jml5 = ttl_h - jml5; 
    });
   $("#potongbeli_h").val(formatMoney(potongbeli));
   $("#jumlahpotong").val(formatMoney(jml5));
   $("#jml_potong").val(jml5);
   $("#sisapotongan").val(formatMoney(ttl_jml5));
   $("#ttl_sub5").val(ttl_jml5);
    });
</script>

<script type="text/javascript">
    $("input[name=ekspedisi]").keyup(function(){
    var ekspedisi = 0; 
    var jml6 = 0;
    var ttl_jml6 = 0;
    $("input[type=text]").each(function () {        
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml6 = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;                 
    ekspedisi = ekspedisi_h;
    ttl_jml6 = ttl_h - jml6; 
    });
   $("#ekspedisi_h").val(formatMoney(ekspedisi));
   $("#jumlahpotong").val(formatMoney(jml6));
   $("#jml_potong").val(jml6);
   $("#sisapotongan").val(formatMoney(ttl_jml6));
   $("#ttl_sub6").val(ttl_jml6);
    });
</script>

<script type="text/javascript">
    $("input[name=moq]").keyup(function(){
    var moq = 0; 
    jml = 0;
    var ttl_jml7 = 0;
    $("input[type=text]").each(function () {        
    var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
    var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
    var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
    var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
    var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
    var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
    var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
    var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
    jml = laba_h + selisih_h + selisihhrg_h + mater_h + potongbeli_h + ekspedisi_h + moq_h;
    moq = moq_h;
    ttl_jml7 = ttl_h - jml; 
    });
   $("#moq_h").val(formatMoney(moq));
   $("#jumlahpotong").val(formatMoney(jml));
   $("#jml_potong").val(jml);
   $("#sisapotongan").val(formatMoney(ttl_jml7));
   $("#ttl_sub7").val(ttl_jml7);
    });
</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
        var no_po = document.getElementById('po').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var nama_supp_h = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur_h = document.getElementById('no_faktur').value;
        var supp_inv_h = document.getElementById('txt_inv').value;
        var tgl_inv_h = document.getElementById('txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('txt_tgltempo').value;        
        var curr_h = document.getElementById('matauang').value;
        var sub_h = document.getElementById('ttl_sub').value;
        var tax_h = document.getElementById('pajak_h').value;
        var dp_h = document.getElementById('ttl_dp_h').value;
        var pph_h = document.getElementById('pph_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';
        var no_kbon = document.getElementById('nokontrabon').value;        
        var tgl_kbon = document.getElementById('tanggal').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var jml_return = document.getElementById('no_faktur').value;
        var lr_kurs = document.getElementById('txt_inv').value;
        var s_qty = document.getElementById('txt_tglsi').value;
        var s_harga = document.getElementById('txt_tgltempo').value;        
        var materai = document.getElementById('matauang').value;                               
        var pot_beli = document.getElementById('select').value;          
        var moq = document.getElementById('ttl_sub').value;
        var jml_potong = document.getElementById('total_h').value;        
        $.ajax({
            type:'POST',
            url:'insertkbon_h.php',
            data: {'no_kbon_h':no_kbon_h, 'no_po':no_po, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'dp_h':dp_h, 'pph_h':pph_h, 'total_h':total_h, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'nama_supp':nama_supp, 'jml_return':jml_return, 'lr_kurs':lr_kurs, 's_qty':s_qty, 's_harga':s_harga, 'materai':materai, 'pot_beli':pot_beli, 'moq':moq, 'jml_potong':jml_potong},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                 alert(response);
                // window.location = 'kontrabon.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });                     

        $("input[type=checkbox]:checked").each(function () {
        var no_kbon = document.getElementById('nokontrabon').value;        
        var tgl_kbon = document.getElementById('tanggal').value;
        var jurnal = document.getElementById('jurnal').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur = document.getElementById('no_faktur').value;
        var supp_inv = document.getElementById('txt_inv').value;
        var tgl_inv = document.getElementById('txt_tglsi').value;
        var tgl_tempo = document.getElementById('txt_tgltempo').value;        
        var curr = document.getElementById('matauang').value;                               
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';
        var ceklist = document.getElementById('select').value;          
        var price = document.getElementById('ttl_sub').value;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var cash = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
        var total = document.getElementById('total_h').value; 
        var dp = document.getElementById('ttl_dp_h').value;
        var tgl_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var no_ro = $(this).closest('tr').find('td:eq(1)').attr('valuess');
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        var sum_pph = document.getElementById('pph_h').value;
        var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_total = 0;
        var sum_dp = 0;
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;        
        sum_sub += price;
        sum_tax += tax;
        sum_dp += dp;    
        sum_total += total - sum_pph - sum_dp;
        $.ajax({
            type:'POST',
            url:'insertkbon.php',
            data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,  'no_ro':no_ro,
            'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
            'curr':curr, 'ceklist':ceklist, 'cash':cash, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_dp':sum_dp, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax, 'tgl_po':tgl_po},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  alert(response);
                
                // window.location = 'kontrabon.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
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
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalbpb').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
    var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(10)').attr('value');
    var top = $(this).closest('tr').find('td:eq(8)').attr('value');
    var curr = document.getElementById('matauang').value;
    var confirm = $(this).closest('tr').find('td:eq(9)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(10)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(12)').text();    

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
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
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
