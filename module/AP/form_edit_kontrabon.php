<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }

</style>

<?php 
$no_kbon = base64_decode($_GET['no_kbon']); 

$sql = mysqli_query($conn2,"select no_kbon from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
$row = mysqli_fetch_array($sql);                         
echo $nokbon['no_kbon'];
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0 text-white" >FORM KONTRABON</h5>
  </div>
  <form id="form-data" method="post">
    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">            
                            <label for="nokontrabon"><b>No Kontra Bon</b></label>
                            <?php
                            echo'<input type="text" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="nokontrabon" name="nokontrabon" value="'.$no_kbon.'">'
                            ?>

                            <input type="hidden" style="font-size: 13px;;" name="unik_code" id="unik_code" class="form-control form-control-sm" 
                            value="<?php 
                            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
                            $shuffle  = substr(str_shuffle($karakter), 0, 16);
                            echo $shuffle; ?>" autocomplete='off' readonly>
                        </div>

                        <div class="col-md-2 mb-3">            
                            <label for="tanggal"><b>Kontra Bon Date <i style="color: red;">*</i></b></label>          
                            <input type="text" style="font-size: 13px;;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal"
                            value="<?php 
                            $sql = mysqli_query($conn2,"select tgl_kbon from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
                            $row = mysqli_fetch_array($sql); 

                            if(!empty($no_kbon)) {
                                echo date("Y-m-d",strtotime($row['tgl_kbon']));
                            }
                            else{
                                echo date("Y-m-d");
                            }  ?>">
                            <input type="hidden" style="font-size: 13px;;" name="tgl_perhitungan" id="tgl_perhitungan" class="form-control form-control-sm">
                            <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" name="txt_top" id="txt_top" 
                            value="<?php
                            $start_date ='';
                            $end_date ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $startdate = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                                $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                            }

                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
                            $row = mysqli_fetch_array($sql);
                            $tgl = $row['max(tgl_bpb)'];
                            $top = isset($row['top']) ? $row['top'] : 0;            


                            if(!empty($nama_supp)) {
                                echo $top;
                            }
                            else{
                                echo $top;
                            } 

                        ?>">
                        <input type="hidden" style="font-size: 13px;;" name="tanggal3" id="tanggal3" class="form-control form-control-sm"
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


                        if(!empty($nama_supp)) {

                            echo date("Y-m-d",strtotime($tgl));
                        }
                        else{
                            echo date("Y-m-d");
                        }  ?>">

                        <input type="hidden" style="font-size: 13px;;" name="tanggal4" id="tanggal4" class="form-control form-control-sm"
                        value="<?php             
                        $start_date ='';
                        $end_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $tkbon = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                        }     

                        if(!empty($tkbon)) {

                            echo date("Y-m-d",strtotime($tkbon));
                        }
                        else{
                            echo date("Y-m-d");
                        }  ?>">
                    </div>
                    <!-- onchange="updateNoKontraBon()" -->
                    <div class="col-md-3 mb-3">            
                        <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>            
                        <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true">
                            <option value="" disabled selected="true">Select Profit Center</option>                                                 
                            <?php
                            $sql = mysqli_query($conn2,"select profit_center, nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from ap_edit_kontrabon_h a left join master_pc b on b.kode_pc = a.profit_center where no_kbon = '$no_kbon'");
                            $row = mysqli_fetch_array($sql);  
                            $kode_pc = $row['profit_center']; 
                            $nama_pc = $row['nama_pc'];  
                            $isSelected = ' selected="selected"';                      
                            if(!empty($no_kbon)) {
                                echo '<option value="'.$kode_pc.'"'.$isSelected.'">'. $nama_pc .'</option>'; 
                            }
                            else{
                                echo '<option value="-">Select Supplier</option>'; 
                            }

                            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: null;               
                            $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$kode_pc'");
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

                        <input type="hidden" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="jurnal" name="jurnal" 
                        value="0" placeholder="<?php echo "KONTRA BON" ?>">
                    </div>

                    <div class="col-md-2 mb-3">            
                        <label for="matauang"><b>Currency</b></label>
                        <input type="text" readonly class="form-control form-control-sm" id="matauang" name="matauang" value="<?php 
                        $sql = mysqli_query($conn2,"select curr from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
                        $row = mysqli_fetch_array($sql);                         
                        echo $row['curr'];
                    ?>">                      
                </div>                                         

                <div class="col-md-3 mb-3">            
                    <label for="txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>          
                    <input type="text" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_inv" name="txt_inv" 
                    value="<?php
                    $sql = mysqli_query($conn2,"select supp_inv from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
                    $row = mysqli_fetch_array($sql);                         
                    $supp_inv = $row['supp_inv'];

                    $txt_inv = isset($_POST['txt_inv']) ? $_POST['txt_inv']: $supp_inv;
                    echo $txt_inv; 
                ?>" required>
            </div>

            <div class="col-md-2 mb-3">            
                <label for="txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label>   
                <input type="text" style="font-size: 13px;;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="txt_tglsi" 
                value="<?php 
                $sql = mysqli_query($conn2,"select tgl_inv from ap_edit_kontrabon where no_kbon = '$no_kbon'");
                $row = mysqli_fetch_array($sql);                         
                if(!empty($no_kbon)) {
                    echo date("Y-m-d",strtotime($row['tgl_inv']));
                }
                else{
                    echo date("Y-m-d");
                }  ?>">
            </div>

            <div class="col-md-3 mb-3">            
                <label for="no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>            
                <input type="text" style="font-size: 13px;;" class="form-control form-control-sm" id="no_faktur" name="no_faktur" 
                value="<?php 
                $sql = mysqli_query($conn2,"select no_faktur from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
                $row = mysqli_fetch_array($sql);                         
                $faktur_h = $row['no_faktur'];

                $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: $faktur_h;
                echo $no_faktur; 
            ?>" required>
        </div>

        <div class="col-md-2 mb-3">            
            <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 13px;;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="txt_tgltempo" 
            value="<?php 
            $sql = mysqli_query($conn2,"select tgl_tempo from ap_edit_kontrabon where no_kbon = '$no_kbon'");
            $row = mysqli_fetch_array($sql);                         
            if(!empty($no_kbon)) {
                echo date("Y-m-d",strtotime($row['tgl_tempo']));
            }
            else{
                echo date("Y-m-d");
            }  ?>">
        </div>


        <div class="col-md-6 mb-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
                <input type="text" readonly style="font-size: 13px;" class="form-control" name="txt_supp" id="txt_supp" 
                value="<?php 
                $sql = mysqli_query($conn2,"select nama_supp from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
                $row = mysqli_fetch_array($sql);                         
                $nama_supp_h = $row['nama_supp'];
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: $nama_supp_h;
                echo $nama_supp; 
            ?>">


            <div class="input-group-append col">
                <button 
                type="button"
                name="edit_header"
                id="edit_header"
                class="btn btn-warning"
                style="
                line-height: 1;
                padding: 4px 12px;
                font-size: 0.875rem;
                border-radius: 6px;">
                <i class="fas fa-save"></i> Edit Header
            </button>

            <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
        </div>

    </div>
</div>                   
</div>
</div>
</div>
</form>

<form id="form-simpan">
    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr class="text-white" style="background-color: #1E3A8A;">
                                    <th style="display: none;">Cek</th>
                                    <th style="width:18%;">NO BPB</th>
                                    <th style="width:15%;">NO PO</th>                            
                                    <th style="width:13%;">BPB Date</th>                            
                                    <th style="width:15%;">SubTotal</th>
                                    <th style="width:12%;">Tax (PPn)</th>
                                    <th style="width:12%;">Tax (PPh)</th>                            
                                    <th style="width:15%;">Total (BPB)</th>
                                    <th style="width:80px;display: none;">Total CBD/DP</th>
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

                                $sql = mysqli_query($conn2,"select a.no_bpb, no_po, tgl_bpb, subtotal, a.idtax, IFNULL(percentage,0) percentage, IFNULL(kriteria,'Non PPH') kriteria, tax, pph_code, pph_value, total, dp_value, confirm1, confirm2, nama_supp, tgl_po, tgl_tempo, curr, mattype, matclass, n_code_category, cus_ctg from ap_edit_kontrabon a INNER JOIN (select no_bpb, confirm1, confirm2 from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.no_bpb INNER JOIN (select * from (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc
                                    UNION
                                    select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from ap_journal_temp a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) a GROUP BY reff_doc) c on c.reff_doc = a.no_bpb left join mtax d on d.idtax = a.idtax where a.no_kbon = '$no_kbon' and a.status != 'Cancel'");


                                while($row = mysqli_fetch_array($sql)){
                                    // $bpb = $row['no_bpb'];
                                    // $id_supplier = $row['id_supplier'];
                                    // $pono = isset($row['pono']) ? $row['pono'] : null;

                                   echo '<tr>
                                   <td style="display: none;"><input type="checkbox" class="chkA"  id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? disabled></td>                        
                                   <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                   <td value="'.$row['no_po'].'">'.$row['no_po'].'</td>                            
                                   <td dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                   <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$row['subtotal'].'">'.number_format($row['subtotal'],2).'</td>
                                   <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                                   <td style="width:200px;">                            
                                   <select class="form-control selectpicker" style="width:150px;" name="combo_pph" id="combo_pph" disabled>
                                   <option data-idtax="'.$row['pph_code'].'" value="'.$row['percentage'].'">'.$row['kriteria'].'</option>';
                                   if ($row['pph_code'] != 0) {
                                       echo '<option data-idtax="0" value="0">Non PPH</option>';
                                   }

                                   $sql_tax = mysqli_query(
                                    $conn2,
                                    "SELECT idtax, kriteria, percentage 
                                    FROM mtax 
                                    WHERE category_tax = 'PPH' 
                                    AND idtax != '".$row['idtax']."'"
                                );

                                   while ($tax = mysqli_fetch_assoc($sql_tax)) {
                                    echo '<option data-idtax="'.$tax['idtax'].'" value="'.$tax['percentage'].'">'.$tax['kriteria'].'</option>';
                                }

                                echo '  </select>
                                </td>                        
                                <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                                <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$row['dp_value'].'">'.number_format($row['dp_value'],2).'</td>
                                <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                                <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                                <td style="display: none;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                                <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>    
                                <td style="display: none;" value="'.$row['tgl_tempo'].'">'.$row['tgl_tempo'].'</td> 
                                <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                                <td style="display: none;" value="'.$row['mattype'].'">'.$row['mattype'].'</td> 
                                <td style="display: none;" value="'.$row['matclass'].'">'.$row['matclass'].'</td> 
                                <td style="display: none;" value="'.$row['n_code_category'].'">'.$row['n_code_category'].'</td>              
                                <td style="display: none;" value="'.$row['cus_ctg'].'">'.$row['cus_ctg'].'</td> 
                                </tr>';
                            }  

                            ?>
                        </tbody>                    
                    </table>   
                </div>
                <div class="form-row col mt-3">
                    <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
                    <div class="col-md-2 mb-3">

                        <?php
                        $sql = mysqli_query($conn2,"select sum(subtotal) subtotal from ap_edit_kontrabon where no_kbon = '$no_kbon'");
                        $row = mysqli_fetch_array($sql);                         
                        $subtotal = $row['subtotal'];
                        ?>                              

                        <input type="text" class="form-control form-control-sm" name="subtotal" id="subtotal" value="<?= number_format($subtotal,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                        <input type="hidden" name="subtotal_h" id="subtotal_h" value="<?= $subtotal; ?>">
                        <input type="hidden" name="subtotal_h1" id="subtotal_h1" value="<?= $subtotal; ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <button 
                        type="button" 
                        id="edit_bpb" 
                        class="btn btn-warning btn-xs" 
                        style="border-radius: 6px;">
                        <i class="fas fa-edit"></i> Edit Detail BPB
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr class="text-white" style="background-color: #2563EB;">
                                    <th style="display: none;">Cek</th>
                                    <th style="width:50px;">No RO</th>
                                    <th style="width:50px;">NO BPPB</th>
                                    <th style="width:50px;">BPPB Date</th>                            
                                    <th style="width:50px;">NO BPB</th>                            
                                    <th style="width:100px;">Total Return</th>  
                                    <th style="width:100px;">Pembayaran</th> 
                                    <th style="display: none;">Pembayaran</th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>                                                    
                                </tr>
                            </thead>

                            <tbody>
                                <?php

                                $querys = mysqli_query($conn2,"select a.no_ro, no_bppb, tgl_bppb, no_bpb, total_ro, curr, mattype, matclass, n_code_category, cus_ctg from ap_edit_return_kb a INNER JOIN (select no_bppb, no_ro, tgl_bppb, no_bpb, curr from bppb_new GROUP BY no_bppb) b on b.no_bppb = a.no_bpbrtn LEFT JOIN (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc
                                 UNION
                                 select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from ap_journal_temp a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) c on c.reff_doc = b.no_bppb where no_kbon = '$no_kbon'");

                                while($row1 = mysqli_fetch_array($querys)){
                                    echo '<tr>
                                    <td style="width:10px;" hidden><input type="checkbox" class="chkB" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                        
                                    <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                                    <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                                    <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                                    <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                                    <td style="width:100px;text-align:right;" data-total-ro="'.round($row1['total_ro'],2).'">'.number_format($row1['total_ro'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_amount" name="txt_amount" value="'.round($row1['total_ro'],2).'" disabled>
                                    </td>
                                    <td style="display: none;" valuess="'.$row1['curr'].'">'.$row1['curr'].'</td>     
                                    <td style="display: none;" valuess="'.$row1['mattype'].'">'.$row1['mattype'].'</td> 
                                    <td style="display: none;" valuess="'.$row1['matclass'].'">'.$row1['matclass'].'</td> 
                                    <td style="display: none;" valuess="'.$row1['n_code_category'].'">'.$row1['n_code_category'].'</td>              
                                    <td style="display: none;" valuess="'.$row1['cus_ctg'].'">'.$row1['cus_ctg'].'</td>                                                                                           
                                    </tr>';
                                }
                                ?>
                            </tbody>                    
                        </table> 
                    </div>
                    <div class="form-row col mt-3">
                        <label for="potongan" class="col-form-label" style="width: 150px;font-size: 13px;;"><b><u>Total Return</u></b></label>
                        <div class="col-md-2 mb-3">    
                         <?php
                         $sql = mysqli_query($conn2,"select sum(total_ro) total_ro from ap_edit_return_kb where no_kbon = '$no_kbon'");
                         $row = mysqli_fetch_array($sql);                         
                         $total_ro = $row['total_ro'];
                         ?>                                  
                         <input type="text" class="form-control form-control-sm" name="potongan" id="potongan" value="<?= number_format($total_ro,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                         <input type="hidden" name="potongan_h" id="potongan_h" value="<?= $total_ro; ?>">
                         <input type="hidden" name="h_mattype" id="h_mattype" value="">
                         <input type="hidden" name="h_matclass" id="h_matclass" value="">
                         <input type="hidden" name="h_code_ctg" id="h_code_ctg" value="">
                         <input type="hidden" name="h_cus_ctg" id="h_cus_ctg" value="">
                     </div>
                     <div class="col-md-2 mb-3">
                        <button 
                        type="button" 
                        id="edit_bppb" 
                        class="btn btn-warning btn-xs" 
                        style="border-radius: 6px;">
                        <i class="fas fa-edit"></i> Edit Detail BPPB
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
                            <thead>
                                <tr style="background-color: #60A5FA; color: white;">
                                    <th style="display: none;">Cek</th>
                                    <th style="width:18%;">No FTR</th>
                                    <th style="width:16%;">No PO</th>
                                    <th style="width:15%;">Tgl PO</th>
                                    <th style="width:16%;">No PI</th>
                                    <th style="width:15%;">Total</th>
                                    <th style="width:15%;">Pembayaran</th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $query_ftr = mysqli_query($conn2,"select no_ftr, no_po, tgl_po, no_pi, total_ftr, curr, '' no_kbon, '' tgl_kbon, '' no_payment, '' tgl_payment, no_pv, no_bankout, tgl_bankout, no_coa from ap_edit_kontrabon_ftr where no_kbon = '$no_kbon'");


                                while($row_ftr = mysqli_fetch_array($query_ftr)){
                                    echo '<tr>
                                    <td style="display: none;"><input type="checkbox" class="chkC" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                        
                                    <td style="width:50px;" no-ftr="'.$row_ftr['no_ftr'].'">'.$row_ftr['no_ftr'].'</td>
                                    <td style="width:50px;" po-ftr="'.$row_ftr['no_po'].'">'.$row_ftr['no_po'].'</td>
                                    <td style="width:100px;" tglpo-ftr="'.$row_ftr['tgl_po'].'">'.date("d-M-Y",strtotime($row_ftr['tgl_po'])).'</td>                            
                                    <td style="width:50px;" pi-ftr="'.$row_ftr['no_pi'].'">'.$row_ftr['no_pi'].'</td>                            
                                    <td style="width:100px;text-align:right;" total-ftr="'.round($row_ftr['total_ftr'],2).'">'.number_format($row_ftr['total_ftr'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="amount_ftr" name="amount_ftr" value="'.round($row_ftr['total_ftr'],2).'" disabled>
                                    </td>
                                    <td style="display: none;" curr-ftr="'.$row_ftr['curr'].'">'.$row_ftr['curr'].'</td>
                                    <td style="display: none;" kbon-ftr="'.$row_ftr['no_kbon'].'">'.$row_ftr['no_kbon'].'</td>
                                    <td style="display: none;" tglkbon-ftr="'.$row_ftr['tgl_kbon'].'">'.$row_ftr['tgl_kbon'].'</td>
                                    <td style="display: none;" lp-ftr="'.$row_ftr['no_payment'].'">'.$row_ftr['no_payment'].'</td>
                                    <td style="display: none;" tgllp-ftr="'.$row_ftr['tgl_payment'].'">'.$row_ftr['tgl_payment'].'</td>
                                    <td style="display: none;" pv-ftr="'.$row_ftr['no_pv'].'">'.$row_ftr['no_pv'].'</td>
                                    <td style="display: none;" bankout-ftr="'.$row_ftr['no_bankout'].'">'.$row_ftr['no_bankout'].'</td>
                                    <td style="display: none;" bankoutdate-ftr="'.$row_ftr['tgl_bankout'].'">'.$row_ftr['tgl_bankout'].'</td>
                                    <td style="display: none;" coa-ftr="'.$row_ftr['no_coa'].'">'.$row_ftr['no_coa'].'</td>
                                    </td>                                                                                            
                                    </tr>';
                                }
                                ?>
                            </tbody>                    
                        </table>
                    </div>
                    <div class="form-row col mt-3">
                        <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total DP / CBD</u></b></label>
                        <div class="col-md-2 mb-3"> 
                            <?php
                            $sql = mysqli_query($conn2,"select sum(total_ftr) total_ftr from ap_edit_kontrabon_ftr where no_kbon = '$no_kbon'");
                            $row = mysqli_fetch_array($sql);                         
                            $total_ftr = $row['total_ftr'];
                            ?>                                
                            <input type="text" class="form-control form-control-sm" name="ttl_dp" id="ttl_dp" value="<?= number_format($total_ftr,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="ttl_dp_h" id="ttl_dp_h" value="<?= $total_ftr; ?>">

                        </div>
                        <div class="col-md-2 mb-3">
                            <button 
                            type="button" 
                            id="edit_ftr" 
                            class="btn btn-warning btn-xs" 
                            style="border-radius: 6px;">
                            <i class="fas fa-edit"></i> Edit Detail CBD
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
          <div class="card-body p-2">
            <div class="row">
              <div class="col-md-7 border-end pe-4">

                <?php
                $sql = mysqli_query($conn2,"select select jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong from ap_edit_potongan where no_kbon = '$no_kbon'");
                $row = mysqli_fetch_array($sql);                         
                $jml_return = $row['jml_return'];
                $lr_kurs = $row['lr_kurs'];
                $s_qty = $row['s_qty'];
                $s_harga = $row['s_harga'];
                $materai = $row['materai'];
                $pot_beli = $row['pot_beli'];
                $ekspedisi = $row['ekspedisi'];
                $moq = $row['moq'];
                $jml_potong = $row['jml_potong'];
                ?>

                <div class="row mb-2 align-items-center">
                  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Laba Rugi Kurs</u></b></label>
                  <div class="col-3">
                    <input type="number" class="form-control form-control-sm text-right" name="labarugi" id="labarugi" value="<?= $lr_kurs; ?>" placeholder="0.00">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm text-right" name="labarugi_h" id="labarugi_h" value="<?= number_format($lr_kurs,2); ?>" placeholder="0.00" readonly>
                </div>
                <div class="col-2"></div>
            </div>

            <div class="row mb-2 align-items-center">
              <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Quantity</u></b></label>
              <div class="col-3">
                <input type="number" class="form-control form-control-sm text-right" name="selisihqty" id="selisihqty" value="<?= $s_qty; ?>" placeholder="0.00">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm text-right" name="selisihqty_h" id="selisihqty_h" value="<?= number_format($s_qty,2); ?>" placeholder="0.00" readonly>
            </div>
            <div class="col-2"></div>
        </div>

        <div class="row mb-2 align-items-center">
          <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Harga</u></b></label>
          <div class="col-3">
            <input type="number" class="form-control form-control-sm text-right" name="selisihharga" id="selisihharga" value="<?= $s_harga; ?>" placeholder="0.00">
        </div>
        <div class="col-4">
            <input type="text" class="form-control form-control-sm text-right" name="selisihharga_h" id="selisihharga_h" value="<?= number_format($s_harga,2); ?>" placeholder="0.00" readonly>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Materai</u></b></label>
      <div class="col-3">
        <input type="number" min="0" class="form-control form-control-sm text-right" name="materai" id="materai" value="<?= $materai; ?>" placeholder="0.00">
    </div>
    <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="materai_h" id="materai_h" value="<?= number_format($materai,2); ?>" placeholder="0.00" readonly>
    </div>
    <div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Potongan Pembelian</u></b></label>
  <div class="col-3">
    <input type="number" max="0" class="form-control form-control-sm text-right" name="potongbeli" id="potongbeli" value="<?= $pot_beli; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongbeli_h" id="potongbeli_h" value="<?= number_format($pot_beli,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Expedisi</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="ekspedisi" id="ekspedisi" value="<?= $ekspedisi; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ekspedisi_h" id="ekspedisi_h" value="<?= number_format($ekspedisi,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya MOQ</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="moq" id="moq" value="<?= $moq; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="moq_h" id="moq_h" value="<?= number_format($moq,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Jumlah Potongan</u></b></label>
  <div class="col-7">
    <input type="text" class="form-control form-control-sm text-right" name="jumlahpotong" id="jumlahpotong" value="<?= number_format($jml_potong,2); ?>" placeholder="0.00" readonly>
    <input type="hidden" name="jml_potong" id="jml_potong" value="<?= $jml_potong; ?>">
</div>
<div class="col-2"></div>
</div>

<input type="hidden" name="ttl_sub" id="ttl_sub">
<input type="hidden" name="ttl_sub1" id="ttl_sub1">
<input type="hidden" name="ttl_sub2" id="ttl_sub2">
<input type="hidden" name="ttl_sub3" id="ttl_sub3">
<input type="hidden" name="ttl_sub4" id="ttl_sub4">
<input type="hidden" name="ttl_sub5" id="ttl_sub5">
<input type="hidden" name="ttl_sub6" id="ttl_sub6">
<input type="hidden" name="ttl_sub7" id="ttl_sub7">

</div>

<div class="col-md-5 ps-4">

    <?php
    $sql = mysqli_query($conn2,"select sum(tax) tax_kbon, sum(pph_value) pph_kbon from ap_edit_kontrabon where no_kbon = '$no_kbon'");
    $row = mysqli_fetch_array($sql);                         
    $tax_kbon = $row['tax_kbon'];
    $pph_kbon = $row['pph_kbon'];
    ?>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPn)</u></b></label>
      <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="pajak" id="pajak" value="<?= number_format($tax_kbon,2); ?>" placeholder="0.00" readonly>
        <input type="hidden" name="pajak_h" id="pajak_h" value="<?= $tax_kbon; ?>">
    </div>
    <div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPh)</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph" id="pph" value="<?= number_format($pph_kbon,2); ?>" placeholder="0.00" readonly>
    <input type="hidden" name="pph_h" id="pph_h" value="<?= $pph_kbon; ?>">
</div>
<div class="col-5"></div>
</div>

<!-- <div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total CBD / DP</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ttl_dp" id="ttl_dp" placeholder="0.00" readonly>
    <input type="hidden" name="ttl_dp_h" id="ttl_dp_h">
</div>
<div class="col-5"></div>
</div> -->

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="total" id="total" placeholder="0.00" readonly>
    <input type="hidden" name="total_h" id="total_h">
    <input type="hidden" name="po" id="po">
    <input type="hidden" name="po1" id="po1">
</div>
<div class="col-3">
    <button type="button" class="btn btn-dark btn-sm w-100" id="calculate">
      <span class="fa fa-calculator"></span> Calculate
  </button>
</div>
<div class="col-2"></div>
</div>

<div class="row mt-3">
  <div class="col">
    <button type="button" class="btn btn-primary btn-sm me-2" id="simpan">
      <span class="fa fa-floppy-o"></span> Save
  </button>
  <button type="button" class="btn btn-danger btn-sm" id="batal">
      <span class="fa fa-angle-double-left"></span> Back
  </button>
</div>
</div>

</div>
</div>
</div>
</div>



</div>
</div>
</form>

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
        </div>

        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="h_profit_center" class="form-control form-control-sm" value="">
        <input type="hidden" style="font-size: 13px;" name="tanggal_kbn" id="tanggal_kbn" class="form-control form-control-sm" value="">

        <!-- BPB Date -->
        <div class="form-group">
            <label><b>BPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control tanggal_fil mr-2" id="start_date" name="start_date" 
              value="<?php
              if(!empty($_POST['start_date'])) {
                echo $_POST['start_date'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control tanggal_fil" id="end_date" name="end_date" 
                value="<?php
                if(!empty($_POST['end_date'])) {
                    echo $_POST['end_date'];
                    } else {
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



<!-- Modal Cari BPB -->
<div class="modal fade" id="modalCariBPB" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data BPB</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e1"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e1" id="nama_supp_e1" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e1']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e1"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e1" name="profitcenter_e1" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e1" name="no_kbon_e1" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e1" name="profit_center_e1" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>BPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e1" name="start_date_e1" 
              value="<?php
              if(!empty($_POST['start_date_e1'])) {
                echo $_POST['start_date_e1'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e1" name="end_date_e1" 
                value="<?php
                if(!empty($_POST['end_date_e1'])) {
                    echo $_POST['end_date_e1'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchBPB">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
            <th style="width:6%;">Cek</th>
            <th style="width:18%;">NO BPB</th>
            <th style="width:15%;">NO PO</th>                            
            <th style="width:13%;">BPB Date</th>                            
            <th style="width:15%;">SubTotal</th>
            <th style="width:12%;">Tax (PPn)</th>
            <th style="width:12%;">Tax (PPh)</th>                            
            <th style="width:15%;">Total (BPB)</th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>     
            <th style="display: none;"></th>
            <th style="display: none;"></th> 
            <th style="display: none;"></th>
            <th style="display: none;"></th>     
            <th style="display: none;"></th>
            <th style="display: none;"></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="subtotal_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="subtotal_edit" id="subtotal_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="subtotal_h_edit" id="subtotal_h_edit" value="">
        <input type="hidden" name="subtotal_h1_edit" id="subtotal_h1_edit" value="">
    </div>
</div>

<div class="form-row col mt-0">
    <label for="pph_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total PPH</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm text-right" name="pph_edit" id="pph_edit" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="pph_h_edit" id="pph_h_edit">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveBPB"><i class="fas fa-save"></i> Save</button>
</div>
</div>
</div>
</div>




<!-- Modal Cari BPB -->
<div class="modal fade" id="modalCariBPPB" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data Return</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e2"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e2" id="nama_supp_e2" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e2']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e2"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e2" name="profitcenter_e2" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e2" name="no_kbon_e2" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e2" name="profit_center_e2" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>BPPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e2" name="start_date_e2" 
              value="<?php
              if(!empty($_POST['start_date_e2'])) {
                echo $_POST['start_date_e2'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e2" name="end_date_e2" 
                value="<?php
                if(!empty($_POST['end_date_e2'])) {
                    echo $_POST['end_date_e2'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchBPPB">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable1_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
            <th style="width:6%;">Cek</th>
            <th style="width:50px;">No RO</th>
            <th style="width:50px;">NO BPPB</th>
            <th style="width:50px;">BPPB Date</th>                            
            <th style="width:50px;">NO BPB</th>                            
            <th style="width:100px;">Total Return</th>  
            <th style="width:100px;">Pembayaran</th> 
            <th style="display: none;">Pembayaran</th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="potongan_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total Return</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="potongan_edit" id="potongan_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="potongan_h_edit" id="potongan_h_edit" value="">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveBPPB"><i class="fas fa-save"></i> Save</button>
</div>
</div>
</div>
</div>



<div class="modal fade" id="modalCariFTR" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data FTR</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e3"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e3" id="nama_supp_e3" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e3']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e3"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e3" name="profitcenter_e3" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e3" name="no_kbon_e3" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e3" name="profit_center_e3" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>FTR Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e3" name="start_date_e3" 
              value="<?php
              if(!empty($_POST['start_date_e3'])) {
                echo $_POST['start_date_e3'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e3" name="end_date_e3" 
                value="<?php
                if(!empty($_POST['end_date_e3'])) {
                    echo $_POST['end_date_e3'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchFTR">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable2_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
            <th style="width:6%;">Cek</th>
            <th style="width:18%;">No FTR</th>
            <th style="width:16%;">No PO</th>
            <th style="width:15%;">Tgl PO</th>
            <th style="width:16%;">No PI</th>
            <th style="width:15%;">Total</th>
            <th style="width:15%;">Pembayaran</th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="ttl_dp_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total FTR</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="ttl_dp_edit" id="ttl_dp_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="ttl_dp_h_edit" id="ttl_dp_h_edit" value="">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveFTR"><i class="fas fa-save"></i> Save</button>
</div>
</div>
</div>
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
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_supp2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_top" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>         
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_confirm" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_confirm2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>                              
                  <div id="details" class="modal-body col-12" style="font-size: 13px;; padding: 0.5rem;"></div>          
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
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>



<script>

    // document.addEventListener("DOMContentLoaded", function() {
    //     let savedPc = localStorage.getItem("profit_center");
    //     if (savedPc) {
    //         document.getElementById("profit_center").value = savedPc;
    //         updateNoKontraBon();
    //     }
    //     let tanggal = document.getElementById('tanggal').value;
    //     ubahtanggal(tanggal);
    // });

    // document.getElementById("profit_center").addEventListener("change", function() {
    //     localStorage.setItem("profit_center", this.value);
    //     document.querySelector("#mytable tbody").innerHTML = "";
    //     document.querySelector("#mytable1 tbody").innerHTML = "";
    //     document.querySelector("#mytable2 tbody").innerHTML = "";
    // });

    function updateNoKontraBon() {
        let pc = document.getElementById("profit_center").value;  
        let input = document.getElementById("nokontrabon");  
        let current = input.value.trim();  

        let parts = current.split("/");

        console.log(parts[0] + ' ' + parts[1] + ' ' + parts[2] + ' ' + parts[3] + ' ' + parts[4] + ' ' + parts[5]);

    // kalau masih 5 bagian (belum ada PC)
    if (parts.length === 5) {
        // [0]=SI, [1]=APR, [2]=YYYY, [3]=MM, [4]=00025
        input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[2] + "/" + parts[3] + "/" + parts[4];
    } 
    // kalau sudah 6 bagian (sudah ada PC → replace)
    else if (parts.length === 6) {
        // [0]=SI, [1]=APR, [2]=PC lama, [3]=YYYY, [4]=MM, [5]=00025
        input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[3] + "/" + parts[4] + "/" + parts[5];
    }

}
</script>
<script>

    $(document).on("click", "#edit_header", function () {
        let nokontrabon     = $("#nokontrabon").val();
        let tanggal         = $("#tanggal").val();
        let profit_center   = $("#profit_center").val();
        let txt_inv         = $("#txt_inv").val();
        let txt_tglsi       = $("#txt_tglsi").val();
        let no_faktur       = $("#no_faktur").val();
        let txt_tgltempo    = $("#txt_tgltempo").val();

        if (nokontrabon === "" || profit_center === "" || txt_tglsi === "" || txt_tgltempo === "" || tanggal === "" || txt_inv === "" || no_faktur === "") {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "The header data will be updated.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                url: "update_kontrabon_header.php", // ganti sesuai file PHP kamu
                data: {
                    nokontrabon: nokontrabon,
                    tanggal: tanggal,
                    profit_center: profit_center,
                    txt_inv: txt_inv,
                    txt_tglsi: txt_tglsi,
                    no_faktur: no_faktur,
                    txt_tgltempo: txt_tgltempo
                },
                success: function (res) {
                    if (res.trim() === "OK") {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Header has been successfully updated!"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error", res, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to update header: " + xhr.responseText
                    });
                }
            });
            }
        });
    });


    $(document).ready(function() {
        $("#mysupp").on("click", function() {
            let profit_center = $('select[name=profit_center] option').filter(':selected').val();
            let tanggal = document.getElementById('tanggal').value;

            if(profit_center == ""){
                alert("Please Input Profit Center.");
                $("#profit_center").focus();
                return;
            }

            $('#h_profit_center').val(profit_center);
            $('#tanggal_kbn').val(tanggal);

            $("#mymodal").modal("show");
        });
    });

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
        $("[data-toggle=tooltip]").tooltip();

    } );

    $(document).ready(function () {
        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#mytable1').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#mytable2').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        var tableBPB = $('#mytable_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        var tableBPPB = $('#mytable1_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        var tableFTR = $('#mytable2_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });


        $('#modalCariBPB').on('shown.bs.modal', function () {
            tableBPB.columns.adjust().draw();
        });


        $('#modalCariBPPB').on('shown.bs.modal', function () {
            tableBPPB.columns.adjust().draw();
        });

        $('#modalCariFTR').on('shown.bs.modal', function () {
            tableFTR.columns.adjust().draw();
        });


        $(document).on('change', '.select_edit', function () {
            console.log('Checkbox berubah:', $(this).is(':checked'));
            var sum_sub = 0;

            $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_edit]').prop('disabled', true);         
            $("input[type=checkbox]:checked").each(function () {
                var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_edit]');
                select_pph.prop('disabled', false);
                var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
                sum_sub += price;
            });

            $("#subtotal_edit").val(formatMoney(sum_sub));
            $("#subtotal_h_edit").val(sum_sub.toFixed(2)); 
            // $("#subtotal_h1_edit").val(data1.toFixed(2)); 
        });


        $(document).on('change', '.select2_edit', function () {
            console.log('Checkbox berubah:', $(this).is(':checked'));
            var sum_sub = 0;

            $(this).closest('#mytable1_edit tr').find('td:eq(6) input').prop('disabled', true);       
            $("input[type=checkbox]:checked").each(function () {
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');
                select_amount.prop('disabled', false);
                var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                sum_sub += price;
            });

            $("#potongan_edit").val(formatMoney(sum_sub));
            $("#potongan_h_edit").val(sum_sub.toFixed(2)); 
            // $("#subtotal_h1_edit").val(data1.toFixed(2)); 
        });


        $(document).on('change', '.select3_edit', function () {
            console.log('Checkbox berubah:', $(this).is(':checked'));
            var sum_sub = 0;

            $(this).closest('#mytable2_edit tr').find('td:eq(6) input').prop('disabled', true);       
            $("input[type=checkbox]:checked").each(function () {
                var select_amount = $(this).closest('#mytable2_edit tr').find('td:eq(6) input');
                select_amount.prop('disabled', false);
                var price = parseFloat($(this).closest('#mytable2_edit tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
                sum_sub += price;
            });

            $("#ttl_dp_edit").val(formatMoney(sum_sub));
            $("#ttl_dp_h_edit").val(sum_sub.toFixed(2)); 
            // $("#subtotal_h1_edit").val(data1.toFixed(2)); 
        });


        $(document).on('input', '#mytable1_edit tbody input[name="txt_amount_edit"]', function () {
            var sum_amount = 0;
            var sum_total = 0;
            var sum_balance = 0;        
            $("input[type=checkbox]:checked").each(function () {        
                var amount = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
                var balance = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');                
                if(amount > balance){
                    select_amount.val(balance);
                    sum_amount += balance;
                    sum_total = sum_amount;
                }else{
                    sum_amount += amount;
                    sum_total = sum_amount;        
                }   
            });
            $("#potongan_edit").val(formatMoney(sum_total));
            $("#potongan_h_edit").val(sum_total.toFixed(4));
        });


        $(document).on('input', '#mytable2_edit tbody input[name="txt_amount_edit"]', function () {
            var sum_amount = 0;
            var sum_total = 0;
            var sum_balance = 0;        
            $("input[type=checkbox]:checked").each(function () {        
                var amount = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
                var balance = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');                
                if(amount > balance){
                    select_amount.val(balance);
                    sum_amount += balance;
                    sum_total = sum_amount;
                }else{
                    sum_amount += amount;
                    sum_total = sum_amount;        
                }   
            });
            $("#potongan_edit").val(formatMoney(sum_total));
            $("#potongan_h_edit").val(sum_total.toFixed(4));
        });


        $(document).on('change', '.combo_pph_edit', function() {
            var sum_pph = 0;
            $("input[type=checkbox]:checked").each(function () {        
                var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
                var pph   = parseFloat($(this).closest('tr').find('td:eq(6) select.combo_pph_edit option:selected').val(), 10) || 0;            
                sum_pph  += price * (pph / 100);  
            });  

            $("#pph_edit").val(formatMoney(sum_pph));
            $("#pph_h_edit").val(sum_pph.toFixed(2));
        });

        document.getElementById('edit_bpb').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e1').val(no_kbon);
            $('#profit_center_e1').val(profit_center);
            $('#profitcenter_e1').val(profitcenter);
            $('#nama_supp_e1').val(nama_supp);
            $('#nama_supp_e1').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariBPB'));
            modal.show();
        }
    })
        });


        document.getElementById('edit_bppb').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e2').val(no_kbon);
            $('#profit_center_e2').val(profit_center);
            $('#profitcenter_e2').val(profitcenter);
            $('#nama_supp_e2').val(nama_supp);
            $('#nama_supp_e2').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariBPPB'));
            modal.show();
        }
    })
        });


        document.getElementById('edit_ftr').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e3').val(no_kbon);
            $('#profit_center_e3').val(profit_center);
            $('#profitcenter_e3').val(profitcenter);
            $('#nama_supp_e3').val(nama_supp);
            $('#nama_supp_e3').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariFTR'));
            modal.show();
        }
    })
        });


        $('#btnSearchBPB').on('click', function() {
            let nama_supp = $('#nama_supp_e1').val();
            let start_date = $('#start_date_e1').val();
            let end_date = $('#end_date_e1').val();
            let no_kbon = $('#no_kbon_e1').val();
            let profit_center = $('#profit_center_e1').val();

            $.ajax({
                url: 'get_bpb_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableBPB.clear().rows.add($(res)).draw();
                }

            });
        });


        $('#btnSearchBPPB').on('click', function() {
            let nama_supp = $('#nama_supp_e2').val();
            let start_date = $('#start_date_e2').val();
            let end_date = $('#end_date_e2').val();
            let no_kbon = $('#no_kbon_e2').val();
            let profit_center = $('#profit_center_e2').val();

            $.ajax({
                url: 'get_bppb_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableBPPB.clear().rows.add($(res)).draw();
                }

            });
        });


        $('#btnSearchFTR').on('click', function() {
            let nama_supp = $('#nama_supp_e3').val();
            let start_date = $('#start_date_e3').val();
            let end_date = $('#end_date_e3').val();
            let no_kbon = $('#no_kbon_e3').val();
            let profit_center = $('#profit_center_e3').val();


            $.ajax({
                url: 'get_ftr_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableFTR.clear().rows.add($(res)).draw();
                }

            });
        });


        // Save BPB
        $('#btnSaveBPB').on('click', function(e) {
            e.preventDefault();

            let dataArr = [];

            $('#mytable_edit tbody tr').each(function() {
                let checkbox = $(this).find('.select_edit');
                if (checkbox.is(':checked')) {
                    let no_kbon = $('#nokontrabon').val();
                    let tgl_kbon = $('#tanggal').val();
                    let pc_kbon = $('#profit_center').val();
                    let nama_supp = $('#nama_supp_e1').val();
                    let invoice = $('#txt_inv').val();
                    let faktur = $('#no_faktur').val();
                    let tglsi = $('#txt_tglsi').val();
                    let tgltempo = $('#txt_tgltempo').val();
                    var create_user = '<?php echo $user; ?>';
                    var no_bpb = $(this).closest('#mytable_edit tr').find('td:eq(1)').attr('value');
                    var no_po = $(this).closest('#mytable_edit tr').find('td:eq(2)').attr('value');
                    var tgl_bpb = $(this).closest('#mytable_edit tr').find('td:eq(3)').attr('value');
                    var price = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(4)').attr('data-subtotal'),10) ||0;
                    var tax = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(5)').attr('data-tax'),10) ||0;
                    var cash = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(8)').attr('data'),10) ||0;
                    var tgl_po = $(this).closest('#mytable_edit tr').find('td:eq(12)').attr('value');
                    var pph = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(6)').find('select[name=combo_pph_edit] option').filter(':selected').val(),10) ||0;
                    var idtax = $(this).closest('#mytable_edit tr').find('td:eq(6)').find('select[name=combo_pph_edit] option').filter(':selected').attr('data-idtax');
                    var curr_bpb = $(this).closest('#mytable_edit tr').find('td:eq(14)').attr('value') || '';
                    var mattype = $(this).closest('#mytable_edit tr').find('td:eq(15)').attr('value') || '';
                    var matclass = $(this).closest('#mytable_edit tr').find('td:eq(16)').attr('value') || '';
                    var n_code_category = $(this).closest('#mytable_edit tr').find('td:eq(17)').attr('value') || '';
                    var cus_ctg = $(this).closest('#mytable_edit tr').find('td:eq(18)').attr('value') || ''; 
                    let start_date = $('#start_date_e1').val();
                    let end_date = $('#end_date_e1').val();

                    var sum_pph = 0;
                    var sum_sub = 0;
                    var sum_tax = 0;
                    var sum_total = 0;
                    var sum_dp = 0;
                    sum_sub += price;
                    sum_tax += tax;
                    sum_pph += sum_sub * (pph / 100);   
                    sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


                    dataArr.push({
                        no_kbon: no_kbon,
                        tgl_kbon: tgl_kbon,
                        pc_kbon: pc_kbon,
                        nama_supp: nama_supp,
                        invoice: invoice,
                        faktur: faktur,
                        create_user: create_user,
                        no_bpb: no_bpb,
                        no_po: no_po,
                        tgl_bpb: tgl_bpb,
                        price: price,
                        tax: tax,
                        cash: cash,
                        tgl_po: tgl_po,
                        pph: pph,
                        idtax: idtax,
                        mattype: mattype,
                        matclass: matclass,
                        n_code_category: n_code_category,
                        cus_ctg: cus_ctg,
                        sum_sub: sum_sub,
                        sum_tax: sum_tax,
                        sum_pph: sum_pph,
                        sum_total: sum_total,
                        tglsi: tglsi,
                        tgltempo: tgltempo,
                        curr_bpb: curr_bpb,
                        start_date: start_date,
                        end_date: end_date
                    });
                }
            });

if (dataArr.length === 0) {
    Swal.fire({
        icon: 'warning',
        title: 'No BPB selected',
        text: 'Please check at least one BPB to save.'
    });
    return;
}

Swal.fire({
    title: 'Are you sure?',
    text: "The new data will replace the old data.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, save it!',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
            url: 'insert_bpb_kontrabon_edit.php',
            type: 'POST',
            data: {data: JSON.stringify(dataArr)},
            success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
    }
});
});




$('#btnSaveBPPB').on('click', function(e) {
    e.preventDefault();

    let dataArr = [];

    $('#mytable1_edit tbody tr').each(function() {
        let checkbox = $(this).find('.select2_edit');
        if (checkbox.is(':checked')) {
            let no_kbon = $('#nokontrabon').val();
            let tgl_kbon = $('#tanggal').val();
            let pc_kbon = $('#profit_center').val();
            let nama_supp = $('#nama_supp_e2').val();
            let invoice = $('#txt_inv').val();
            let faktur = $('#no_faktur').val();
            let tglsi = $('#txt_tglsi').val();
            let tgltempo = $('#txt_tgltempo').val();
            var create_user = '<?php echo $user; ?>';

            var no_ro = $(this).closest('#mytable1_edit tr').find('td:eq(1)').attr('data-ro');
            var no_bppb = $(this).closest('#mytable1_edit tr').find('td:eq(2)').attr('valuess');
            var tgl_bppb = $(this).closest('#mytable1_edit tr').find('td:eq(3)').attr('valuess');
            var ttl_ro = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
            var curr = $(this).closest('#mytable1_edit tr').find('td:eq(7)').attr('valuess');
            var mattype = $(this).closest('#mytable1_edit tr').find('td:eq(8)').attr('valuess');
            var matclass = $(this).closest('#mytable1_edit tr').find('td:eq(9)').attr('valuess');
            var n_code_category = $(this).closest('#mytable1_edit tr').find('td:eq(10)').attr('valuess');
            var cus_ctg = $(this).closest('#mytable1_edit tr').find('td:eq(11)').attr('valuess');

            let start_date = $('#start_date_e2').val();
            let end_date = $('#end_date_e2').val();

            // var sum_pph = 0;
            // var sum_sub = 0;
            // var sum_tax = 0;
            // var sum_total = 0;
            // var sum_dp = 0;
            // sum_sub += price;
            // sum_tax += tax;
            // sum_pph += sum_sub * (pph / 100);   
            // sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


            dataArr.push({
                no_kbon: no_kbon,
                tgl_kbon: tgl_kbon,
                pc_kbon: pc_kbon,
                nama_supp: nama_supp,
                invoice: invoice,
                faktur: faktur,
                tglsi: tglsi,
                tgltempo: tgltempo,
                create_user: create_user,

                no_ro: no_ro,
                no_bppb: no_bppb,
                tgl_bppb: tgl_bppb,
                ttl_ro: ttl_ro,
                curr: curr,
                mattype: mattype,
                matclass: matclass,
                n_code_category: n_code_category,
                cus_ctg: cus_ctg,

                start_date: start_date,
                end_date: end_date
                
            });
            console.log("Current dataArr:", dataArr);
        }
    });

    if (dataArr.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No BPPB selected',
            text: 'Please check at least one BPPB to save.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "The new data will replace the old data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'insert_bppb_kontrabon_edit.php',
                type: 'POST',
                data: {data: JSON.stringify(dataArr)},
                success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
        }
    });
});


$('#btnSaveFTR').on('click', function(e) {
    e.preventDefault();

    let dataArr = [];

    $('#mytable2_edit tbody tr').each(function() {
        let checkbox = $(this).find('.select3_edit');
        if (checkbox.is(':checked')) {
            let no_kbon = $('#nokontrabon').val();
            let tgl_kbon = $('#tanggal').val();
            let pc_kbon = $('#profit_center').val();
            let nama_supp = $('#nama_supp_e2').val();
            let invoice = $('#txt_inv').val();
            let faktur = $('#no_faktur').val();
            let tglsi = $('#txt_tglsi').val();
            let tgltempo = $('#txt_tgltempo').val();
            var create_user = '<?php echo $user; ?>';

            var no_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(1)').attr('no-ftr');
            var no_po_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(2)').attr('po-ftr');
            var tgl_po_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(3)').attr('tglpo-ftr');
            var no_pi_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(4)').attr('pi-ftr');
            var ttl_ftr = parseFloat($(this).closest('#mytable2_edit tr').find('td:eq(6) input').val(),10) || 0;
            var curr_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(7)').attr('curr-ftr');
            var kbon_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(8)').attr('kbon-ftr');
            var tglkbon_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(9)').attr('tglkbon-ftr');
            var lp_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(10)').attr('lp-ftr');
            var tgllp_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(11)').attr('tgllp-ftr');
            var pv_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(12)').attr('pv-ftr');
            var bankout_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(13)').attr('bankout-ftr');
            var bankoutdate_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(14)').attr('bankoutdate-ftr');
            var coa_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(15)').attr('coa-ftr');

            let start_date = $('#start_date_e3').val();
            let end_date = $('#end_date_e3').val();

            // var sum_pph = 0;
            // var sum_sub = 0;
            // var sum_tax = 0;
            // var sum_total = 0;
            // var sum_dp = 0;
            // sum_sub += price;
            // sum_tax += tax;
            // sum_pph += sum_sub * (pph / 100);   
            // sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


            dataArr.push({
                no_kbon: no_kbon,
                tgl_kbon: tgl_kbon,
                pc_kbon: pc_kbon,
                nama_supp: nama_supp,
                invoice: invoice,
                faktur: faktur,
                tglsi: tglsi,
                tgltempo: tgltempo,
                create_user: create_user,

                no_ftr: no_ftr,
                no_po_ftr: no_po_ftr,
                tgl_po_ftr: tgl_po_ftr,
                no_pi_ftr: no_pi_ftr,
                ttl_ftr: ttl_ftr,
                curr_ftr: curr_ftr,
                kbon_ftr: kbon_ftr,
                tglkbon_ftr: tglkbon_ftr,
                lp_ftr: lp_ftr,
                tgllp_ftr: tgllp_ftr,
                pv_ftr: pv_ftr,
                bankout_ftr: bankout_ftr,
                bankoutdate_ftr: bankoutdate_ftr,
                coa_ftr: coa_ftr,

                start_date: start_date,
                end_date: end_date
                
            });
            console.log("Current dataArr:", dataArr);
        }
    });

    if (dataArr.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No BPPB selected',
            text: 'Please check at least one BPPB to save.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "The new data will replace the old data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'insert_ftr_kontrabon_edit.php',
                type: 'POST',
                data: {data: JSON.stringify(dataArr)},
                success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
        }
    });
});

});
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

function myFunction2() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable1");
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
        // alert(tanggal);
        $.ajax({
            type: 'POST', 
            url: 'getnomor_kbon.php', 
            data: {'tanggal':tanggal},
            success: function(response) { 
                // console.log(response);
                $('#nokontrabon').val(response); 
                updateNoKontraBon(); 
            }
        });
    // result.setDate(result.getDate() + txt_top);
    // tgl2    = DATEADD(day, txt_top, tanggal);
    $("#tanggal").val(tanggal);
    $("#txt_tgltempo").val(hasil);

};
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


// $(".chkA").change(function(){
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
        var processedPO = [];
        var allowedPO = [];
        var cbddp_total = 0;
        var total_ftr = 0;
        $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
        $(".chkC").prop("disabled", false);
    // $(this).closest('tr').find('td:eq(6) input').val(0);   
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () { 
        var tgl4 = document.getElementById('tanggal4').value; 
        var tanggal = document.getElementById('tgl_perhitungan').value || '1970-01-01'; 
        var tglbpb = $(this).closest('tr').find('td:eq(3)').attr('value');  
        var tglbpb2 = $(this).closest('tr').find('td:eq(3)').attr('dates');
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        var curr = $(this).closest('tr').find('td:eq(14)').attr('value') || $(this).closest('tr').find('td:eq(7)').attr('valuess') || document.getElementById('matauang').value || 'IDR'; 
        var po1 = document.getElementById('po').value;  
        var tax1 = parseFloat(document.getElementById('pajak_h').value,10) || 0;
        var cbd1 = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;
        var h_sub = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
        var h_pot = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        var select_amount = $(this).closest('tr').find('td:eq(6) input');
        var pph1 = parseFloat(document.getElementById('pph_h').value,10) || 0;      
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
        var price_ftr = parseFloat($(this).closest('tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
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
    total_ftr += price_ftr;
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

cbddp1 = cbd;  

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

if (!processedPO.includes(po)) {
    cbddp_total += cbd;
        processedPO.push(po); // simpan PO ke array
    }

    // allowedPO.push(po);

});

    $("#mytable input[type=checkbox]:checked").each(function () { 
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        allowedPO.push(po);

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
    $("#ttl_dp").val(formatMoney(total_ftr));
    $("#ttl_dp_h").val(total_ftr.toFixed(2));
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

    $(".chkC").prop("disabled", true); 
    if (allowedPO.length > 0) {
        $("#mytable2 tbody tr").each(function () {
            var poC = $(this).find("td:eq(2)").attr("po-ftr");
            if (allowedPO.includes(poC)) {
                $(this).find(".chkC").prop("disabled", false);
            }
        });
    }else{
        $("#mytable2 tbody tr").each(function () {
            $(".chkC").prop("disabled", false).prop("checked", false);
            $(this).find('td:eq(6) input').prop('disabled', true);
            $("#ttl_dp").val('');
            $("#ttl_dp_h").val('');
        });
    }

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
    $("#mytable1 input[name=txt_amount]").keyup(function(){
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


    $("#mytable2 input[name=amount_ftr]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("input[type=checkbox]:checked").each(function () {        
            var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
            var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
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
        $("#ttl_dp").val(formatMoney(sum_total));
        $("#ttl_dp_h").val(sum_total.toFixed(4));
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

        var subtotal_h = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
        var potongan_h = parseFloat(document.getElementById('potongan_h').value,10) || 0;
        var ttl_dp_h = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;
        var jml_potong = parseFloat(document.getElementById('jml_potong').value,10) || 0;
        var pajak_h = parseFloat(document.getElementById('pajak_h').value,10) || 0;
        var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;

        pajak = pajak_h;
        pph = pph_h;          
        jumlah = (subtotal_h - (potongan_h + ttl_dp_h) + jml_potong) + pajak - pph; 


        $("#total").val(formatMoney(jumlah));
        $("#total_h").val(jumlah.toFixed(4));
    });
</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
        var unik_code = document.getElementById('unik_code').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var tgl_kbon_p = document.getElementById('tgl_perhitungan').value;
        var tgl_kbon_s = document.getElementById('tanggal4').value; 
        var nama_supp_h = document.getElementById('txt_supp').value;
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
        var profit_center = $('select[name=profit_center] option').filter(':selected').val();
        //&& tgl_kbon_h >= tgl_kbon_p 
        if(total_h != '' && total_h >= 0 ){        
            $.ajax({
                type:'POST',
                url:'insert_kontrabon_edit_all.php',
                data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'dp_h':dp_h, 'pph_h':pph_h, 'total_h':total_h, 'jml_return':jml_return, 'lr_kurs':lr_kurs, 's_qty':s_qty, 's_harga':s_harga, 'materai':materai, 'pot_beli':pot_beli, 'ekspedisi':ekspedisi, 'moq':moq, 'jml_potong':jml_potong, 'no_po_h':no_po_h, 'tgl_kbon_s':tgl_kbon_s, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg, 'profit_center':profit_center},
                cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    localStorage.removeItem("profit_center");
                    console.log(response);
                    alert('Data Saved Successfully');
                    window.location = 'kontrabon.php';
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    alert(xhr);
                }
            }); 
        }  
              

       if (document.getElementById('total_h').value == ''){
            document.getElementById('calculate').focus();
            alert("Please do the calculation ");
        }else if (document.getElementById('total_h').value < 0){
            alert("Contrabon can't be minus ");
        }else{

        } 
    });


    $("#form-simpan").on("click", "#batal", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
      
            $.ajax({
                type:'POST',
                url:'batal_edit_kontrabon.php',
                data: {'no_kbon_h':no_kbon_h},
                cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    localStorage.removeItem("profit_center");
                    window.location = 'kontrabon.php';
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                    alert(xhr);
                }
            });  
              
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

        var targetUrl = no_po.includes("NAK") ? 'ajaxbpb_knitting.php' : 'ajaxbpb.php';  

        $.ajax({
            type : 'post',
            url : targetUrl,
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
