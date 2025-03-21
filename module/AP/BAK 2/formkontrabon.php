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

            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
            $shuffle  = substr(str_shuffle($karakter), 0, 16);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>Kontra Bon Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" onchange="ubahtanggal(this.value)"
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tanggal = date("Y-m-d",strtotime($_POST['tanggal']));
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb) from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and status != 'Cancel' and tgl_bpb between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_bpb)'];         
    
            // $top = 30;

            if(!empty($tanggal)) {
                
                  echo date("Y-m-d",strtotime($tanggal));      
            }
            else{
                echo date("Y-m-d");
            } ?>">
            <input type="hidden" style="font-size: 14px;" name="tgl_perhitungan" id="tgl_perhitungan" class="form-control">
            <input type="hidden" style="font-size: 14px;" class="form-control" name="txt_top" id="txt_top" 
            value="<?php
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $startdate = date("Y-m-d",strtotime($_POST['tanggal']));
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_bpb)'];
            $top = isset($row['top']) ? $row['top'] : 0;            
    
            // $top = 30;

            if(!empty($nama_supp)) {
                echo $top;
            }
            else{
                echo $top;
            } 

            // if(!empty($_POST['txt_tgltempo'])) {
            //     echo $_POST['txt_tgltempo'];
            // }
            // else{
            //     echo date("d-m-Y");
            // }
        ?>">
            <input type="hidden" style="font-size: 14px;" name="tanggal3" id="tanggal3" class="form-control"
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb) from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and status != 'Cancel' and tgl_bpb between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_bpb)'];         
    
            // $top = 30;

            if(!empty($nama_supp)) {
                
                echo date("Y-m-d",strtotime($tgl));
            }
            else{
                echo date("Y-m-d");
            }  ?>">

            <input type="hidden" style="font-size: 14px;" name="tanggal4" id="tanggal4" class="form-control"
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tkbon = date("Y-m-d",strtotime($_POST['tanggal']));
            }     
    
            // $top = 30;

            if(!empty($tkbon)) {
                
                echo date("Y-m-d",strtotime($tkbon));
            }
            else{
                echo date("Y-m-d");
            }  ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="jurnal"><b>Journal</b></label>            
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="jurnal" name="jurnal" 
            value="0" placeholder="<?php echo "KONTRA BON" ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="matauang"><b>Currency</b></label>
            <input type="text" readonly class="form-control-plaintext" id="matauang" name="matauang" value="">                      
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
                echo date("Y-m-d",strtotime($_POST['txt_tglsi']));
            }
            else{
                echo date("Y-m-d");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal1" name="txt_tgltempo" id="txt_tgltempo" 
            value="<?php
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $startdate = date("Y-m-d",strtotime($_POST['tanggal']));
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_bpb)'];
            $top = $row['top'];            
    
            // $top = 30;

            if(!empty($nama_supp)) {
                echo date("Y-m-d",strtotime($startdate . "+$top days"));
            }
            else{
                echo date("Y-m-d");
            } 

            // if(!empty($_POST['txt_tgltempo'])) {
            //     echo $_POST['txt_tgltempo'];
            // }
            // else{
            //     echo date("d-m-Y");
            // }
        ?>">
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
                <input type="text" style="font-size: 14px;" class="form-control tanggal_fil" id="start_date" name="start_date" 
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
                <input type="text" style="font-size: 14px;" class="form-control tanggal_fil" id="end_date" name="end_date" 
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

            <div class="col-md-12">
                <div class="container-1">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no bpb..">
                </div>
            </div>
            </br>
        </br>
           <div class="tableFix" style="height: 300px;">
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Cek</th>
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
                            <th style="width:100px;display: none;">Supplier</th>
                            <th style="width:100px;display: none;">Supplier</th> 
                            <th style="width:100px;display: none;">Confirm2</th>
                            <th style="width:100px;display: none;">Supplier</th>     
                            <th style="width:100px;display: none;">Supplier</th>
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


            $q = mysqli_query($conn1,"select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");            
            while($rs = mysqli_fetch_array($q)){
            $persen .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'">'.$rs['kriteria'].'</option>';
            }                        
            
            // $sql = mysqli_query($conn2,"select no_bpb,curr,pono,tgl_bpb,tgl_po,b.sub,b.tax,b.total,top,confirm1,confirm2,a.supplier,id_item,id_supplier,mattype,matclass,n_code_category FROM (select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item  where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' and status != 'Cancel' group by a.no_bpb) a inner join
            //     (select b.Supplier,a.bpbno_int,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) )),2) as sub,  round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100))),2) as tax, round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where bpbdate between '$start_date' and '$end_date' and b.Supplier = '$nama_supp' group by a.bpbno_int order by bpbdate asc) b on b.bpbno_int = a.no_bpb GROUP BY a.no_bpb");

            $sql = mysqli_query($conn2,"select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier, a.tgl_po,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item  where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' and status != 'Cancel' group by a.no_bpb");

            // $sql = mysqli_query($conn2,"select a.no_bpb, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(b.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty - b.qty) * a.price) * (a.tax / 100))) as tax, if(b.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty - b.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier, a.tgl_po from bpb_new  a left join bppb_new b on b.no_bpb = a.no_bpb where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' group by a.no_bpb");


            while($row = mysqli_fetch_array($sql)){
            $bpb = $row['no_bpb'];
            $id_supplier = $row['id_supplier'];
            $pono = isset($row['pono']) ? $row['pono'] : null;

            $mattype = $row['mattype'];
            $matclass1 = $row['matclass'];
            if ($mattype == 'C') {
            if ($matclass1 == 'CMT' || $matclass1 == 'PRINTING' || $matclass1 == 'EMBRODEIRY' || $matclass1 == 'WASHING' || $matclass1 == 'PAINTING' || $matclass1 == 'HEATSEAL') {
                $matclass = $matclass1;
            }else{
                $matclass = 'OTHER';
            }
            }else{
            $matclass = $matclass1;
            }

            if ($id_supplier == '342' || $id_supplier == '20' || $id_supplier == '19' || $id_supplier == '692' || $id_supplier == '17' || $id_supplier == '18') {
            $cust_ctg = 'Related';
            }else{
            $cust_ctg = 'Third';
            }

            $n_code_category = $row['n_code_category'];

             $Aquer = mysqli_query($conn2,"select no_po, sum(amount) as amount from list_payment_dp where no_po = '$pono' and status_int >= '4'");
            $b = mysqli_fetch_array($Aquer);
            $po_no = isset($b['no_po']) ? $b['no_po'] : null ;
            $dp = isset($b['amount']) ? $b['amount'] : 0 ;

            $Aquerrff = mysqli_query($conn2,"select DISTINCT list_payment_dp.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_dp inner join kontrabon_h on kontrabon_h.no_po = list_payment_dp.no_po where kontrabon_h.no_po = '$pono' and list_payment_dp.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
            $f = mysqli_fetch_array($Aquerrff);
            $po_nno22 = isset($f['no_po']) ? $f['no_po'] : null ;
            $cbd22 = isset($f['amount']) ? $f['amount'] : 0 ;

            $tot_sisadp = $dp - $cbd22;

            $Aquerr = mysqli_query($conn2,"select b.no_po, sum(total) as amount_update from list_payment_cbd a inner join kontrabon_cbd b on b.no_kbon = a.no_kbon where b.no_po = '$pono' and a.status_int >= '4'");
            $c = mysqli_fetch_array($Aquerr);
            $po_nno = isset($c['no_po']) ? $c['no_po'] : null ;
            $cbd = isset($c['amount_update']) ? $c['amount_update'] : 0 ;

            $Aquerraa = mysqli_query($conn2,"select DISTINCT list_payment_cbd.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_cbd inner join kontrabon_cbd a on a.no_kbon = list_payment_cbd.no_kbon  inner join kontrabon_h on kontrabon_h.no_po = a.no_po where kontrabon_h.no_po = '$pono' and list_payment_cbd.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
            $d = mysqli_fetch_array($Aquerraa);
            $po_nno11 = isset($d['no_po']) ? $d['no_po'] : null ;
            $cbd11 = isset($d['amount']) ? $d['amount'] : 0 ;

            // $tot_sisa = $cbd - $cbd11;
            $tot_sisa = $cbd - $cbd11;

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
                            <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data="'.$tot_sisadp.'">'.number_format($tot_sisadp,2).'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>    
                            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                            <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                            <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                            <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                            <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td> 
                        </tr>';
                }elseif($pono == $po_nno){
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data="'.$tot_sisa.'">'.number_format($tot_sisa,2).'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td> 
                            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                            <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
                            <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                            <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                            <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                            <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
                        </tr>';
                } else{
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
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
                            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                            <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
                            <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                            <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                            <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                            <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
                        </tr>';
                }            
                }  
              
                    ?>
            </tbody>                    
            </table>   
            </div>                 
<div class="col-md-12">   
        <form id="form-simpan">
        </br>
        <div class="col-md-12">   
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 150px;"><b>Total BPB</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="subtotal_h" id="subtotal_h" value="">
                <input type="hidden" name="subtotal_h1" id="subtotal_h1" value="">
            </div>
            </div>
        </div>
            <div class="col-md-12">   
            <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Cek</th>
                            <th style="width:50px;">No RO</th>
                            <th style="width:50px;">NO BPPB</th>
                            <th style="width:50px;">BPPB Date</th>                            
                            <th style="width:50px;">NO BPB</th>                            
                            <th style="width:100px;">Total Return</th>  
                            <th style="width:100px;">Pembayaran</th> 
                            <th style="display: none;">Pembayaran</th>                                                    
                        </tr>
                    </thead>

            <tbody>
            <?php

            $querys = mysqli_query($conn2,"select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' GROUP BY no_bppb");


            while($row1 = mysqli_fetch_array($querys)){
                $ro_no = isset($row1['no_ro']) ? $row1['no_ro'] : null ;
                $bpb_rtn = isset($row1['no_bppb']) ? $row1['no_bppb'] : null ;
                $tot_ro = isset($row1['total']) ? $row1['total'] : 0;
                $tot_ro2 = isset($row1['total2']) ? $row1['total2'] : 0;

                // $Aquer123 = mysqli_query($conn2,"select DISTINCT no_ro, sum(DISTINCT total_ro) as amount from return_kb where no_ro = '$ro_no' and status != 'Cancel'");

                $Aquer123 = mysqli_query($conn2,"select DISTINCT no_bpbrtn, sum(DISTINCT total_ro) as amount from return_kb where no_bpbrtn = '$bpb_rtn' and status != 'Cancel'");
                    $g = mysqli_fetch_array($Aquer123);
                    $nobpbrtn = isset($g['no_bpbrtn']) ? $g['no_bpbrtn'] : null ;
                    $amt_ro = isset($g['amount']) ? $g['amount'] : 0 ;
                    $sisaro = $tot_ro2 - $amt_ro;

                $querybppb = mysqli_query($conn2,"select bppbno,bppbno_int,bppbdate,curr,id_supplier,supplier,mattype,n_code_category,matclass,curr,COALESCE(tax,0) tax,username,dateinput,(dpp + (dpp * (COALESCE(tax,0)/100))) total,dpp,(dpp * (COALESCE(tax,0)/100)) ppn from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, supplier, mattype, n_code_category, 
        if(matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) matclass, bppb.curr,bppb.username, bppb.dateinput, 
        SUM(((qty) * price)) as dpp,bpbno_ro
        from bppb 
        inner join masteritem mi on bppb.id_item = mi.id_item
        inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier
        where bppbno_int = '$bpb_rtn' group by bppbno) a left join
        
        (select bpbno,pono from bpb GROUP BY bpbno) b on b.bpbno = a.bpbno_ro
        left JOIN
        (select pono,tax from po_header GROUP BY pono) c on c.pono = b.pono");
            $rowbppb = mysqli_fetch_array($querybppb);

            $id_supplier_bppb = isset($rowbppb['id_supplier']) ? $rowbppb['id_supplier'] : null;
            // echo $id_supplier_bppb;
            $mattype_bppb = isset($rowbppb['mattype']) ? $rowbppb['mattype'] : null;
            $matclass1_bppb = isset($rowbppb['matclass']) ? $rowbppb['matclass'] : null;
            if ($mattype_bppb == 'C') {
            if ($matclass1_bppb == 'CMT' || $matclass1_bppb == 'PRINTING' || $matclass1_bppb == 'EMBRODEIRY' || $matclass1_bppb == 'WASHING' || $matclass1_bppb == 'PAINTING' || $matclass1_bppb == 'HEATSEAL') {
                $matclass_bppb = $matclass1_bppb;
            }else{
                $matclass_bppb = 'OTHER';
            }
            }else{
            $matclass_bppb = $matclass1_bppb;
            }

            if ($id_supplier_bppb == '342' || $id_supplier_bppb == '20' || $id_supplier_bppb == '19' || $id_supplier_bppb == '692' || $id_supplier_bppb == '17' || $id_supplier_bppb == '18') {
            $cust_ctg_bppb = 'Related';
            }else{
            $cust_ctg_bppb = 'Third';
            }

            $n_code_category_bppb = isset($rowbppb['n_code_category']) ? $rowbppb['n_code_category'] : null;


                    if($sisaro != 0 || $nobpbrtn == null){
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                            <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                            <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                            <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                            <td style="width:100px;text-align:right;" data-total-ro="'.round($sisaro,4).'">'.number_format($sisaro,4).'</td>
                            <td style="width:100px;">
                            <input style="text-align: right;" type="number" min="0" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount" value="'.round($sisaro,4).'" disabled>
                            </td>
                            <td style="display: none;" valuess="'.$row1['curr'].'">'.$row1['curr'].'</td>     
                            <td style="display: none;" valuess="'.$mattype_bppb.'">'.$mattype_bppb.'</td> 
                            <td style="display: none;" valuess="'.$matclass_bppb.'">'.$matclass_bppb.'</td> 
                            <td style="display: none;" valuess="'.$n_code_category_bppb.'">'.$n_code_category_bppb.'</td>              
                            <td style="display: none;" valuess="'.$cust_ctg_bppb.'">'.$cust_ctg_bppb.'</td>                                                                                            
                        </tr>';
                    }else{
                        echo '';
                    }}
                    ?>
            </tbody>                    
            </table> 
        </div>
            <div class="col-md-12">    
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Total Return</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="potongan" id="potongan" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                 <input type="hidden" name="potongan_h" id="potongan_h" value="">
                 <input type="hidden" name="h_mattype" id="h_mattype" value="">
                 <input type="hidden" name="h_matclass" id="h_matclass" value="">
                 <input type="hidden" name="h_code_ctg" id="h_code_ctg" value="">
                 <input type="hidden" name="h_cus_ctg" id="h_cus_ctg" value="">
            </div>
            </div>
            </div> 
            <div class="box footer col-md-12">
                </br>

            <!-- <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>SubTotal</b></label>
            <div class="col-md-5 mb-3">                              
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
            </div>  -->

            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Laba Rugi Kurs</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" class="form-control" name="labarugi" id="labarugi" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="labarugi_h" id="labarugi_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Selisih Quantity</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" class="form-control" name="selisihqty" id="selisihqty" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="selisihqty_h" id="selisihqty_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Selisih Harga</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" class="form-control" name="selisihharga" id="selisihharga" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="selisihharga_h" id="selisihharga_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Biaya Materai</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" min="0" class="form-control" name="materai" id="materai" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="materai_h" id="materai_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Potongan Pembelian</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" max="0" class="form-control" name="potongbeli" id="potongbeli" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="potongbeli_h" id="potongbeli_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Biaya Expedisi</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" min="0" class="form-control" name="ekspedisi" id="ekspedisi" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ekspedisi_h" id="ekspedisi_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Biaya MOQ</b></label>
            <div class="col-md-2 mb-3">                              
                <input type="number" min="0" class="form-control" name="moq" id="moq" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
             <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="moq_h" id="moq_h" value="" placeholder="0.00" style="font-size: 14px;text-align: right;">
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Jumlah Potongan</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="jumlahpotong" id="jumlahpotong" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                 <input type="hidden" name="jml_potong" id="jml_potong" value="">
            </div>
            </div>
        </br>

     <!--        <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>SubTotal</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="sisapotongan" id="sisapotongan" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly> -->
                 <input type="hidden" name="ttl_sub" id="ttl_sub" value="">
                 <input type="hidden" name="ttl_sub1" id="ttl_sub1" value="">
                 <input type="hidden" name="ttl_sub2" id="ttl_sub2" value="">
                 <input type="hidden" name="ttl_sub3" id="ttl_sub3" value="">
                 <input type="hidden" name="ttl_sub4" id="ttl_sub4" value="">
                 <input type="hidden" name="ttl_sub5" id="ttl_sub5" value="">
                 <input type="hidden" name="ttl_sub6" id="ttl_sub6" value="">
                 <input type="hidden" name="ttl_sub7" id="ttl_sub7" value="">
          <!--   </div>
            </div>   -->      
       <!--     <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>BPB</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ttl_bpb" id="ttl_bpb" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="ttl_bpb_h" id="ttl_bpb_h" value="">
            </div>
            </div> -->

            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Tax (PPn)</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pajak_h" id="pajak_h" value="">
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 150px;"><b>Tax (PPh)</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pph" id="pph" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="">
            </div>
            </div>   
            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Total CBD / DP</b></label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="ttl_dp" id="ttl_dp" value="" placeholder="0.00"  style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="ttl_dp_h" id="ttl_dp_h" value="">
            </div>
            </div> 
            </br>

            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;"><b>Total</b></label>
            <div class="col-md-4 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="total_h" id="total_h" value="">
                <input type="hidden" name="po" id="po" value="">
                <input type="hidden" name="po1" id="po1" value="">
            </div>
            <div class="col-md-1 mb-3">                              
                <button type="button" style="border-radius: 6px" onclick="carinomor()" class="btn-dark btn-sm" name="calculate" id="calculate"><span class="fa fa-calculator"></span> Calculate</button> 
                <!-- <input type="text" name="coba" id="coba" value=""> -->
            </div>

            </div>
           <div class="form-row col">
            <div class="col-md-5 mb-3">                              
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
    $('#mytable1').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<!-- <script type="text/javascript">

    function carinomor(){
        var kode = '123';
        $query = ("select max(no_kbon) from kontrabon_h;");
        $result = mysql_query($query);
        var kodeBarang = $row['max(no_kbon)'];
    $('#nokontrabon').val(kodeBarang);
    }

</script> -->


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
        var tgl1 = document.getElementById('tanggal3').value;
    $('.tanggal').datepicker({
        format: "yyyy-mm-dd",
        autoclose:true
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal_fil').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function () {
        // var tgl = document.getElementById('tanggal').value;
    $('.tanggal1').datepicker({
        format: "yyyy-mm-dd",
        autoclose:true,
        // startDate: new Date(tgl)
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
    function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
</script>

<script type="text/javascript">
    function addDate(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    return formatDate(result);
}
</script>

<script type="text/javascript"> 
    var tgl = 0;
    var tgl2 = '';
function ubahtanggal(value){  
    var tanggal = document.getElementById('tanggal').value; 
    var txt_top = parseFloat(document.getElementById('txt_top').value,10) || 0;
    var coba = new Date();
    var hasil = addDate(tanggal, txt_top);
    // result.setDate(result.getDate() + txt_top);
    // tgl2    = DATEADD(day, txt_top, tanggal);
    $("#tanggal").val(tanggal);
    $("#txt_tgltempo").val(hasil);
        
};
</script>

<!-- <script type="text/javascript"> 
    var tgl = 0;
    var tgl2 = '';
function ubahtanggal(value){  
    var tanggal = document.getElementById('tanggal').value; 
    var txt_top = document.getElementById('txt_top').value;
    var result = new Date(value);
    result.setDate(result.getDate() + txt_top);
    tgl2    = tanggal;
    $("#txt_tgl").val(tanggal);
    $("#tgl_perhitungan").val(result);
        
};
</script> -->

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
     $("input[id=select]").change(function(){
    var sum_sub = 0;
    var total = 0;
    var amt = '';
    var sisa = 0;
    var nopo= '';
    var nopo1= '';
    var curren= '';
    var tanggals= '';
    var dates= '';
    var m_type= '';
    var m_class= '';
    var n_code= '';
    var c_ctg= '';
    var ppn = 0;
    var cbddp = 0;
    var pph = 0;
    var pot = 0;
    var ppn1 = 0;
    var cbddp1 = 0;
    var pph11 = 0;
    var data = 0;
    var data1 = 0;
    $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
    // $(this).closest('tr').find('td:eq(6) input').val(0);   
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () { 
    var tgl4 = document.getElementById('tanggal4').value; 
    var tanggal = document.getElementById('tgl_perhitungan').value || '1970-01-01'; 
    var tglbpb = $(this).closest('tr').find('td:eq(3)').attr('value');  
    var tglbpb2 = $(this).closest('tr').find('td:eq(3)').attr('dates');
    var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
    var curr = $(this).closest('tr').find('td:eq(14)').attr('value') || $(this).closest('tr').find('td:eq(7)').attr('valuess'); 
    var po1 = document.getElementById('po').value;  
    var tax1 = parseFloat(document.getElementById('pajak_h').value,10) || 0;
    var cbd1 = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;
    var h_sub = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
    var h_pot = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(6) input');
    var pph1 = parseFloat(document.getElementById('pph_h').value,10) || 0;      
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
    var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
    var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
    var mattype = $(this).closest('tr').find('td:eq(15)').attr('value'); 
    var matclass = $(this).closest('tr').find('td:eq(16)').attr('value'); 
    var n_code_category = $(this).closest('tr').find('td:eq(17)').attr('value'); 
    var cus_ctg = $(this).closest('tr').find('td:eq(18)').attr('value');
    var h_mattype = document.getElementById('h_mattype').value;
    var h_matclass = document.getElementById('h_matclass').value;
    var h_code_ctg = document.getElementById('h_code_ctg').value;
    var h_cus_ctg = document.getElementById('h_cus_ctg').value; 
    select_pph.prop('disabled', false);  
    select_amount.prop('disabled', false);  
    amt = select_amount.prop('disabled', false);  
    // alert(curr);
    
    sum_sub += price;
    total += price_ro;
    sisa = sum_sub - total; 
    nopo= po;
    ppn += tax;
    cbddp= cbd;
    curren= curr; 
    pph = 0;
    pot = 0; 
    // data1 = h_sub - h_pot;
    // // if(data1 > price_ro){
    //     data = price_ro;
    // // }else{
    // //     data = h_sub;
    // // }
    if(h_mattype == ''){
        m_type= mattype;  
    }else{
        m_type= h_mattype;
    }

    if(h_matclass == ''){
        m_class= matclass;  
    }else{
        m_class= h_matclass;
    }

    if(h_code_ctg == ''){
        n_code= n_code_category;  
    }else{
        n_code= h_code_ctg;
    }

    if(h_cus_ctg == ''){
        c_ctg= cus_ctg;  
    }else{
        c_ctg= h_cus_ctg;
    }

    if(po1 == ''){
      nopo1= po;  
    }else{
        nopo1= po1;
    }
    if(cbd1 == ''){
      cbddp1= cbd;  
    }else{
        cbddp1= cbd1;
    }

    if(tax1 == ''){
      ppn1= tax;  
    }else{
        ppn1= tax1;
    }

    if(pph1 == ''){
      pph11= pph;  
    }else{
        pph11= pph1;
    }

    if(tglbpb > tgl4){
      tanggals = tglbpb;  
    }else{
        tanggals = tgl4;
    }

    if(tglbpb2 > tanggal){
      dates = tglbpb2;  
    }else{
        dates = tanggal;
    }

    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub.toFixed(2)); 
    $("#subtotal_h1").val(data1.toFixed(2)); 
    $("#potongan").val(formatMoney(total));
    $("#potongan_h").val(total.toFixed(4));            
    $("#po").val(nopo1); 
    $("#po1").val(nopo); 
    $("#sisapotongan").val(formatMoney(sisa));
    $("#ttl_sub").val(sisa);
    $("#ttl_dp").val(formatMoney(cbddp1));
    $("#ttl_dp_h").val(cbddp1.toFixed(2));
    $("#pajak").val(formatMoney(ppn));
    $("#pajak_h").val(ppn);
    $("#pph").val(formatMoney(pph11));
    $("#pph_h").val(pph11.toFixed(2));
    $("#jumlahpotong").val(formatMoney(pot));
    $("#jml_potong").val(pot);
    $("#matauang").val(curren);
    $("#tanggal4").val(tanggals);
    $("#tgl_perhitungan").val(dates);

    $("#h_mattype").val(m_type);
    $("#h_matclass").val(m_class);
    $("#h_code_ctg").val(n_code);
    $("#h_cus_ctg").val(c_ctg);
    $("#select").val("1");                      
});        
</script>

<script type="text/javascript">
    $("select[name=combo_pph]").on('change', function(){
    var sum_sub = 0;
    var total = 0;
    var sisa = 0;
    var nopo= '';
    var ppn = 0;
    var cbddp = 0;
    var sum_pph = 0;
    $("input[type=checkbox]:checked").each(function () {  
    var po = $(this).closest('tr').find('td:eq(2)').attr('value');      
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
    var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;            
    sum_pph += price * (pph / 100);
    sum_sub += price;
    total += price_ro;
    sisa = sum_sub - total - sum_pph; 
    nopo= po;
    ppn= tax;
    cbddp= cbd;  

    });  
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph.toFixed(2));        
    // $("#subtotal").val(formatMoney(sum_sub));
    // $("#subtotal_h").val(sum_sub);             
    // $("#po").val(nopo); 
    // $("#potongan").val(formatMoney(total));
    // $("#potongan_h").val(total);
    // $("#sisapotongan").val(formatMoney(sisa));
    // $("#ttl_sub").val(sisa);
    // $("#total").val(formatMoney(sisa));
    // $("#total_h").val(sisa);
    // $("#ttl_dp").val(formatMoney(cbddp));
    // $("#ttl_dp_h").val(cbddp);
    // $("#pajak").val(formatMoney(ppn));
    // $("#pajak_h").val(ppn);
    // $("#select").val("1");
    });
</script>

<script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(6) input');                
    if(amount > balance){
        select_amount.val(balance);
        sum_amount += balance;
        sum_total = sum_amount;
    }else{
    sum_amount += amount;
    sum_total = sum_amount;        
    }   
    });
    $("#potongan").val(formatMoney(sum_total));
    $("#potongan_h").val(sum_total.toFixed(4));
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
    jml1 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;              
    laba = laba_h;
    ttl_jml1 = ttl_h + jml1; 
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
    jml2 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
    selisih = selisih_h;
    ttl_jml2 = ttl_h + jml2; 
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
    jml3 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
    selisihhrg = selisihhrg_h;
    ttl_jml3 = ttl_h + jml3; 
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
    jml4 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
    mater = mater_h;
    ttl_jml4 = ttl_h + jml4; 
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
    jml5 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
    potongbeli = potongbeli_h;
    ttl_jml5 = ttl_h + jml5; 
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
    jml6 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
    ekspedisi = ekspedisi_h;
    ttl_jml6 = ttl_h + jml6; 
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
    var jml = 0;
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
               
    jml = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;
    moq = moq_h;
    ttl_jml7 = ttl_h + jml;

    });
   $("#moq_h").val(formatMoney(moq));
   $("#jumlahpotong").val(formatMoney(jml));
   $("#jml_potong").val(jml);
   $("#sisapotongan").val(formatMoney(ttl_jml7));
   $("#ttl_sub7").val(ttl_jml7);
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#calculate", function(){
    var jumlah = 0; 
    var total = 0;
    var ttl_dp = 0;
    var pajak = 0;
    var pph = 0;
    $("input[type=button]").each(function () { 
    var subtotal_h = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
    var potongan_h = parseFloat(document.getElementById('potongan_h').value,10) || 0;
    var jml_potong = parseFloat(document.getElementById('jml_potong').value,10) || 0;
    var pajak_h = parseFloat(document.getElementById('pajak_h').value,10) || 0;
    var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
    var ttl_dp_h = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;
     
    pajak = pajak_h;
    pph = pph_h;          
    jumlah = (subtotal_h - potongan_h + jml_potong) + pajak - pph; 

    if (jumlah > '0') {
    if (ttl_dp_h > jumlah) {
        ttl_dp = jumlah;
        total = jumlah - ttl_dp;
    }else{
        ttl_dp = ttl_dp_h;
        total = jumlah - ttl_dp;
    }
}
else{
        ttl_dp = ttl_dp_h;
        total = jumlah - ttl_dp;
}
                     
    });

    $("#total").val(formatMoney(total));
    $("#total_h").val(total.toFixed(4));
    $("#ttl_dp").val(formatMoney(ttl_dp));
    $("#ttl_dp_h").val(ttl_dp.toFixed(2));
    $("#pajak").val(formatMoney(pajak));
    $("#pajak_h").val(pajak);
    $("#pph").val(formatMoney(pph));
    $("#pph_h").val(pph);
    });
</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
        var unik_code = document.getElementById('unik_code').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var tgl_kbon_p = document.getElementById('tgl_perhitungan').value;
        var tgl_kbon_s = document.getElementById('tanggal4').value; 
        var nama_supp_h = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur_h = document.getElementById('no_faktur').value;
        var no_po_h = document.getElementById('po').value;
        var supp_inv_h = document.getElementById('txt_inv').value;
        var tgl_inv_h = document.getElementById('txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('txt_tgltempo').value;        
        var curr_h = document.getElementById('matauang').value;
        var sub_h = document.getElementById('subtotal_h').value;
        var tax_h = document.getElementById('pajak_h').value;
        var dp_h = document.getElementById('ttl_dp_h').value;
        var pph_h = document.getElementById('pph_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';
        var jml_return = document.getElementById('potongan_h').value;
        var lr_kurs = document.getElementById('labarugi').value;
        var s_qty = document.getElementById('selisihqty').value;
        var s_harga = document.getElementById('selisihharga').value;        
        var materai = document.getElementById('materai').value;                               
        var pot_beli = document.getElementById('potongbeli').value;
        var ekspedisi = document.getElementById('ekspedisi').value;          
        var moq = document.getElementById('moq').value;
        var jml_potong = document.getElementById('jml_potong').value;
        var mattype = document.getElementById('h_mattype').value;
        var matclass = document.getElementById('h_matclass').value;
        var n_code_category = document.getElementById('h_code_ctg').value;
        var cus_ctg = document.getElementById('h_cus_ctg').value;
        //&& tgl_kbon_h >= tgl_kbon_p 
        if(total_h != '' && total_h >= 0 ){        
        $.ajax({
            type:'POST',
            url:'insertkbon_h.php',
            data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'dp_h':dp_h, 'pph_h':pph_h, 'total_h':total_h, 'jml_return':jml_return, 'lr_kurs':lr_kurs, 's_qty':s_qty, 's_harga':s_harga, 'materai':materai, 'pot_beli':pot_beli, 'ekspedisi':ekspedisi, 'moq':moq, 'jml_potong':jml_potong, 'no_po_h':no_po_h, 'tgl_kbon_s':tgl_kbon_s, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $("input[type=checkbox]:checked").each(function () {
        var no_kbon = document.getElementById('nokontrabon').value; 
         var unik_code = document.getElementById('unik_code').value;       
        var tgl_kbon = document.getElementById('tanggal').value;
        var tgl_kbon_p = document.getElementById('tgl_perhitungan').value;
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
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) ||0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var cash = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
        var total = document.getElementById('total_h').value; 
        var dp = document.getElementById('ttl_dp_h').value;
        var tgl_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var no_ro = $(this).closest('tr').find('td:eq(1)').attr('data-ro');
        var no_bppb = $(this).closest('tr').find('td:eq(2)').attr('valuess');
        var ttl_ro = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
        var sum_pph = 0;
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_total = 0;
        var sum_dp = 0;
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;  
        var mattype = $(this).closest('tr').find('td:eq(15)').attr('value') || $(this).closest('tr').find('td:eq(8)').attr('valuess');
        var matclass = $(this).closest('tr').find('td:eq(16)').attr('value') || $(this).closest('tr').find('td:eq(9)').attr('valuess');
        var n_code_category = $(this).closest('tr').find('td:eq(17)').attr('value') || $(this).closest('tr').find('td:eq(10)').attr('valuess');
        var cus_ctg = $(this).closest('tr').find('td:eq(18)').attr('value') || $(this).closest('tr').find('td:eq(11)').attr('valuess');      
        sum_sub += price;
        sum_tax += tax;
        sum_dp += dp;  
        sum_pph += sum_sub * (pph / 100);   
        sum_total += total - sum_pph - sum_dp;
        // && tgl_kbon >= tgl_kbon_p
        if(total != '' && total >= 0){        
        $.ajax({
            type:'POST',
            url:'insertkbon.php',
            data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,  'no_ro':no_ro,
            'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
            'curr':curr, 'ceklist':ceklist, 'cash':cash, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_dp':sum_dp, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax, 'tgl_po':tgl_po, 'ttl_ro':ttl_ro, 'no_bppb':no_bppb, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'kontrabon.php';
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
                window.location = 'kontrabon.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
        }  

        // else if (document.getElementById('tanggal').value < document.getElementById('tgl_perhitungan').value){
        // alert("Contrabon Date Can't be less than BPB Date ");
        // }               
                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the BPB number");
        }else if (document.getElementById('total_h').value == ''){
        document.getElementById('calculate').focus();
        alert("Please do the calculation ");
        }else if (document.getElementById('total_h').value < 0){
        alert("Contrabon can't be minus ");
        }else{
           
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
    var supp = $(this).closest('tr').find('td:eq(11)').attr('value');
    var top = $(this).closest('tr').find('td:eq(13)').attr('value');
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
