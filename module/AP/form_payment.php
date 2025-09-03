<?php include '../header.php' ?>
<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

</style>

<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0 text-white" >FORM LIST PAYMENT</h5>
    </div>
    <form id="form-data" method="post">
        <div class="form-row mt-2">
            <div class="col-md-3 mb-3">            
                <label for="nopayment"><b>No List Payment</b></label>
                <?php
                $sql = mysqli_query($conn2,"select CONCAT('LP/NAG/',DATE_FORMAT(CURRENT_DATE(), '%m%y'),'/',LPAD((COALESCE(max(SUBSTR(no_payment,13)),0) + 1),5,0)) nomor from list_payment WHERE YEAR(tgl_payment) = YEAR (CURRENT_DATE())");
                $row = mysqli_fetch_array($sql);
                $kodepay = $row['nomor'];

                echo'<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="nopayment" name="nopayment" value="'.$kodepay.'">'
                ?>
            </div>
            <div class="col-md-2 mb-3">            
                <label for="tanggal"><b>List Payment Date <i style="color: red;">*</i></b></label>          
                <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggal(this.value)"
                value="<?php             
                $start_date ='';
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $tanggal = date("Y-m-d",strtotime($_POST['h_tanggal']));
                    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                }

                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                $sql = mysqli_query($conn2,"select distinct max(tgl_kbon) from kontrabon_h where nama_supp = '$nama_supp'and status = 'Approved' and balance >= 1 and tgl_kbon between '$start_date' and '$end_date' ");
                $row = mysqli_fetch_array($sql);
                $tgl = $row['max(tgl_kbon)'];    


                // echo $tanggal; echo $tgl;

                if(!empty($nama_supp) && $tanggal != '1970-01-01') {
                    if($tanggal < $tgl){
                        echo date("d-m-Y",strtotime($tgl));
                        }else{
                          echo date("Y-m-d",strtotime($tanggal));  
                      }
                  }
                  else{
                    echo date("Y-m-d");
                }  ?>">

                <input type="hidden" style="font-size: 14px;" name="tgl_perhitungan" id="tgl_perhitungan" class="form-control form-control-sm">

                <input type="hidden" style="font-size: 14px;" name="tanggal1" id="tanggal1" class="form-control form-control-sm" 
                value="<?php             
                $start_date ='';
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                }

                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                $sql = mysqli_query($conn2,"select distinct max(tgl_kbon) from kontrabon_h where nama_supp = '$nama_supp'and status = 'Approved' and balance >= 1 and tgl_kbon between '$start_date' and '$end_date' ");
                $row = mysqli_fetch_array($sql);
                $tgl = $row['max(tgl_kbon)'];         


                if(!empty($nama_supp)) {

                    echo date("Y-m-d",strtotime($tgl));
                }
                else{
                    echo date("2022-01-01");
                } 


            ?>">
        </div>

        <div class="col-md-2 mb-3">            
            <label for="tanggal"><b>Due Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="due_date" id="due_date" class="form-control form-control-sm due_date" 
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $due_date = date("Y-m-d",strtotime($_POST['due_date']));
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select max(b.tgl_tempo) as tgl_tempo from kontrabon b inner join kontrabon_h a on a.no_kbon = b.no_kbon where b.tgl_kbon between '$start_date' and '$end_date' and b.nama_supp = '$nama_supp' and b.status = 'Approved' and a.balance != '0' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['tgl_tempo'];         


            if(!empty($nama_supp)) {
                echo date("Y-m-d",strtotime($tgl)); 
            }
            else{
                echo date("Y-m-d");
            } 
        ?>">

        <input type="hidden" style="font-size: 14px;" name="due_date1" id="due_date1" class="form-control form-control-sm" 
        value="<?php             
        $start_date ='';
        $end_date ='';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
        }

        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
        $sql = mysqli_query($conn2,"select max(b.tgl_tempo) as tgl_tempo from kontrabon b inner join kontrabon_h a on a.no_kbon = b.no_kbon where b.tgl_kbon between '$start_date' and '$end_date' and b.nama_supp = '$nama_supp' and b.status = 'Approved' and a.balance != '0' ");
        $row = mysqli_fetch_array($sql);
        $tgl = $row['tgl_tempo'];         

            // $top = 30;

        if(!empty($nama_supp)) {

            echo date("Y-m-d",strtotime($tgl));
        }
        else{
            echo date("2022-01-01");
        } 


    ?>">
</div>

<div class="col-md-3 mb-3">            
    <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>            
    <select class="form-control form-control-sm selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoLP()">
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

    <input type="hidden" readonly style="font-size: 13px;;" class="form-control form-control-sm form-control form-control-sm-sm" id="jurnal" name="jurnal" 
    value="0" placeholder="<?php echo "KONTRA BON" ?>">
</div>

<!-- <div class="col-md-3 mb-3">            
    <label for="jurnal"><b>Journal</b></label>  -->           
    <input type="hidden" readonly style="font-size: 14px;" class="form-control form-control-sm" id="jurnal" name="jurnal" value="0"
    placeholder="<?php echo "Payment" ?>">
    <!-- </div>   -->          

    <div class="col-md-3 mb-3">            
        <label for="memo"><b>Descriptions</b></label>          
        <input type="text" style="font-size: 14px;" class="form-control form-control-sm" name="memo" id="memo" 
        value="<?php             
        if(!empty($_POST['memo'])) {
            echo $_POST['memo'];
        }
        else{
            echo '';
        } ?>">
    </div>            



    <div class="col-md-4 mb-3">
        <label for="nama_supp"><b>Supplier</b></label>            
        <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control form-control-sm" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            echo $nama_supp; 
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

<div class="row">

    <div class="col-md-12">

        <div class="table-responsive p-1">
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                <thead>
                    <tr class="text-white" style="background-color: #1E3A8A;">
                        <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                        <th style="width:50px;">No Kontrabon</th>
                        <th style="width:100px;">Kontrabon Date</th>                            
                        <th style="width:100px;">Total Kontrabon</th>                            
                        <th style="width:50px;">TOP</th>
                        <th style="width:100px;">Outstanding</th>
                        <th style="width:100px;">Amount</th>                                                        
                        <th style="width:100px;">Due Date</th>
                        <th style="width:100px;">Currency</th>                            
                        <th style="width:100px;display: none;">Supplier</th>
                        <th style="width:100px;display: none;">Status</th>
                        <th style="width:100px;display: none;">Create User</th>                            
                        <th style="width:100px;display: none;">No Faktur</th>                        
                        <th style="width:100px;display: none;">Tgl Invoice</th>
                        <th style="width:100px;display: none;">Tgl Invoice</th>
                        <th style="width:100px;display: none;">Status</th>
                        <th style="width:100px;display: none;">Create User</th>                            
                        <th style="width:100px;display: none;">No Faktur</th>                        
                        <th style="width:100px;display: none;">Tgl Invoice</th>
                        <th style="width:100px;display: none;">Tgl Invoice</th>
                        <th style="width:100px;display: none;">Tgl Invoice</th>
                        <th style="width:100px;display: none;">Tgl Invoice</th>
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
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                        $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }

                    $sql = mysqli_query($conn2,"select kontrabon.no_kbon as no_kbon, kontrabon.tgl_kbon as tgl_kbon, kontrabon.no_bpb as no_bpb, kontrabon.no_po as no_po, kontrabon.tgl_bpb as tgl_bpb, kontrabon.tgl_po as tgl_po, SUM(kontrabon.pph_value) as pph_value, kontrabon_h.total as total, kontrabon.nama_supp as supplier, kontrabon_h.balance as balance, kontrabon.tgl_tempo as tgl_tempo, kontrabon.curr as matauang, kontrabon.status as status, kontrabon.create_user as create_user, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon_h.no_coa,kontrabon_h.nama_coa from kontrabon inner join kontrabon_h on kontrabon_h.no_kbon = kontrabon.no_kbon where kontrabon.tgl_kbon between '$start_date' and '$end_date' and kontrabon.nama_supp = '$nama_supp' and kontrabon.status = 'Approved' and kontrabon_h.balance != '0' and kontrabon_h.profit_center = '$profit_center' group by no_kbon");


                    while($row = mysqli_fetch_array($sql)){
                        $tgl_kbon = $row['tgl_kbon'];
                        $tgl_tempo = $row['tgl_tempo'];
                        $diff = strtotime($tgl_tempo) - strtotime($tgl_kbon);
                        $date_diff = round($diff / 86400);                     
                        echo '<tr>
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                        <td style="width:50px;" value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_kbon'])).'" value="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                        <td style ="text-align: right;" class="dt_total" style="width:100px;" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                        <td value="'.$date_diff.'">'.$date_diff.' Day</td>
                        <td style ="text-align: right;" class="dt_out" style="width:100px;" data-out="'.$row['balance'].'">'.$row['balance'].'</td>                            
                        <td style="width:100px;">
                        <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control form-control-sm" id="txt_amount" name="txt_amount" value="" disabled>
                        </td>                            
                        <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>                            
                        <td style="width:50px;" value="'.$row['matauang'].'">'.$row['matauang'].'</td>                            
                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                        <td style="display: none;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                        <td style="display: none;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                                                        
                        <td style="display: none;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                        <td style="display: none;" value="'.$row['pph_value'].'">'.$row['pph_value'].'</td>
                        <td style="display: none;" value="'.$row['supp_inv'].'">'.$row['supp_inv'].'</td>
                        <td style="display: none;" value="'.$row['create_user'].'">'.$row['create_user'].'</td>
                        <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
                        <td style="display: none;" value="'.$row['no_faktur'].'">'.$row['no_faktur'].'</td>
                        <td style="display: none;" value="'.$row['tgl_inv'].'">'.date("d-M-Y",strtotime($row['tgl_inv'])).'</td>
                        <td style="display: none;" value="'.$row['no_coa'].'">'.$row['no_coa'].'</td>
                        <td style="display: none;" value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>

                        </tr>';
                    }                  
                    ?>
                </tbody>  
            </table>                  
        </table>                    
        <form id="form-simpan">
            <div class="form-row col mt-3">
                <label for="subtotal" style="width: 120px;"><b><u>OutStanding</u></b></label>
                <div class="col-md-3 mb-3">                              
                    <input type="text" class="form-control form-control-sm" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px; text-align: right;" readonly>
                </div>
            </div>
            <div class="form-row col">
                <label for="pajak" style="width: 120px;"><b><u>Amount</u></b></label>
                <div class="col-md-3 mb-3">                              
                    <input type="text" class="form-control form-control-sm" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                </div>
            </div>          
            <div class="form-row col">
                <label for="total" style="width: 120px;"><b><u>Balance</u></b></label>
                <div class="col-md-3 mb-3">                              
                    <input type="text" class="form-control form-control-sm" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                </div>
            </div>
            <div class="form-row col">
                <div class="col-md-3 mb-3">                              
                    <button type="button" style="border-radius: 6px" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
                    <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='payment.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
                </div>
            </div>                                    
        </form>
    </div>

</div>
</div>
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
            <label><b>Kontrabon Date</b></label>
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

    function updateNoLP() {
        let pc = document.getElementById("profit_center").value;  
        let input = document.getElementById("nopayment");  
        let current = input.value.trim();  

    if (!current) return; // kalau kosong, skip

    let parts = current.split("/");

    if (parts.length === 4) {
        // LP/NAG/0925/00049
        parts[1] = pc; // replace profit center
        input.value = parts.join("/");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    let savedPc = localStorage.getItem("profit_center");
    if (savedPc) {
        document.getElementById("profit_center").value = savedPc;
        updateNoLP();
    }

    let tanggal = document.getElementById('tanggal').value;
    ubahtanggal(tanggal);
});

document.getElementById("profit_center").addEventListener("change", function() {
    localStorage.setItem("profit_center", this.value);
    document.querySelector("#mytable tbody").innerHTML = "";
    updateNoLP();
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

        $("[data-toggle=tooltip]").tooltip();

    } );
</script>
<script type="text/javascript">
    $(document).ready(function () {
        // var tgl1 = document.getElementById('tanggal1').value;
        $('.tanggal').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true,
        // startDate: new Date(tgl1)
    });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var tgl = document.getElementById('due_date1').value;
        $('.due_date').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true
        // startDate: new Date(tgl)
    });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal3').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script type="text/javascript"> 
    var tgl = 0;
    var tgl2 = '';
    function ubahtanggal(value){  
        var tanggal = document.getElementById('tanggal').value; 
        var coba = new Date();
        // alert(tanggal);
        $.ajax({
            type: 'POST', 
            url: 'getnomor_lp.php', 
            data: {'tanggal':tanggal},
            success: function(response) { 
                // alert(response);
                $('#nopayment').val(response);
                updateNoLP(); 
            }
        });
    };
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
    var sum_kb = 0;
    var sum_amount = 0;
    var ceklist = 0;
    var sum_total = 0;
    var dates= '';
    $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(6) input').val("");                  
    $("input[type=checkbox]:checked").each(function () { 
        var tanggal = document.getElementById('tgl_perhitungan').value || '01-01-1970'; 
        var tglkbon = $(this).closest('tr').find('td:eq(2)').attr('dates');       
        var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
        var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;    
        var select_amount = $(this).closest('tr').find('td:eq(6) input');
        select_amount.prop('disabled', false);              
        sum_kb += kb;
        sum_amount += amount;
        sum_total = sum_kb - sum_amount;

        if(tglkbon > tanggal){
          dates = tglkbon;  
      }else{
        dates = tanggal;
    }     
});
    $("#subtotal").val(formatMoney(sum_kb));
    $("#pajak").val(formatMoney(sum_amount));    
    $("#total").val(formatMoney(sum_total));
    $("#tgl_perhitungan").val(dates);
    // $("#select").val("1");                    
});        
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

<!-- <script type="text/javascript">
    $("#txt_dp").keyup(function(){
        var dp_value = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;
        dp_value = total_po * (dp / 100);
        $("#txt_dp_value").val(formatMoney(dp_value));
    });
</script> -->

<!-- <script type="text/javascript">
    $("#txt_dp_value").keyup(function(){
        var dp_code = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp_value = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        dp_code = (dp_value / total_po) * 100;
        $("#txt_dp").val(dp_code);
    });
</script> -->

<!--<script type="text/javascript">
    $("#form-data").on("click", "#send", function(){
        var datas= $('select[name=nama_supp] option').filter(':selected').val();
        var start_date= $('#start_date').attr('value');
        var end_date= $('#start_date').attr('value');
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'nama_supp':datas, 'start_date': start_date, 'end_date': end_date},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                alert(response);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });
    });
</script>-->

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
            var no_payment = document.getElementById('nopayment').value;        
            var tgl_payment = document.getElementById('tanggal').value;
            var tgl_lp_p = document.getElementById('tgl_perhitungan').value;
            var keterangan = document.getElementById('memo').value;        
            var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
            var curr = $(this).closest('tr').find('td:eq(8)').attr('value');                               
            var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
            var tgl_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
            var no_bpb = $(this).closest('tr').find('td:eq(10)').attr('value');
            var tgl_bpb = $(this).closest('tr').find('td:eq(11)').attr('value');
            var no_po = $(this).closest('tr').find('td:eq(12)').attr('value');
            var tgl_po = $(this).closest('tr').find('td:eq(13)').attr('value');
            var pph_value = $(this).closest('tr').find('td:eq(14)').attr('value');
            var create_user = '<?php echo $user; ?>';         
            var total_kbon = parseFloat($(this).closest('tr').find('td:eq(3)').attr('data-total'),10) ||0;
            var top = $(this).closest('tr').find('td:eq(4)').attr('value');
            var outstanding = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) ||0;
            var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
            var tgl_tempo = $(this).closest('tr').find('td:eq(7)').attr('value');
            var duedate = document.getElementById('due_date').value;
            var no_coa = $(this).closest('tr').find('td:eq(20)').attr('value');
            var nama_coa = $(this).closest('tr').find('td:eq(21)').attr('value');
            var profit_center = $('select[name=profit_center] option').filter(':selected').val();
            var balance = 0;
            balance += outstanding - amount;
            if(amount >= 1 && tgl_payment >= tgl_lp_p){       
                $.ajax({
                    type:'POST',
                    url:'insertlistpayment.php',
                    data: {'no_payment':no_payment, 'tgl_payment':tgl_payment, 'keterangan':keterangan, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'nama_supp':nama_supp, 'curr':curr, 'create_user':create_user, 'total_kbon':total_kbon, 'top':top, 'outstanding':outstanding, 'amount':amount, 'tgl_tempo':tgl_tempo, 'balance':balance, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'no_po':no_po, 'tgl_po':tgl_po, 'pph_value':pph_value, 'duedate':duedate, 'no_coa':no_coa, 'nama_coa':nama_coa, 'profit_center':profit_center},
                    cache: 'false',
                    close: function(e){
                        e.preventDefault();
                    },
                    success: function(response){
                        localStorage.removeItem("profit_center");
                        console.log(response);
                 // alert("Data saved successfully");
                 // alert(response);
                 window.location = 'payment.php';
             },
             error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
            }
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the Kontrabon number");            
        }else if (document.getElementById('tanggal').value < document.getElementById('tgl_perhitungan').value){
            alert("List Payment Date Can't be less than Kontrabon Date ");
        } else{
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
