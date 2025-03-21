<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h4 class="text-center">FORM UPDATE BPB</h4>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="noftrcbd"><b>No Dokumen</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(SUBSTR(no_dok,14,5)) no_dok from bpb_faktur_inv where jenis = 'INV'");
            $row = mysqli_fetch_array($sql);
            $urutan = $row['no_dok'];
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "INV/NAG/$bln$thn/";
            $kodeftr = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control" id="no_dok" name="no_dok" value="'.$kodeftr.'">'
            ?>
            </div>
            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $shuffle  = substr(str_shuffle($karakter), 0, 20);
            echo $shuffle; ?>" autocomplete='off' readonly>
            <div class="col-md-2 mb-3">            
            <label for="tanggal"><b>Tanggal</b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>" disabled>
            </div>

            <div class="col-md-7 mb-3">  
            <label for="from_hris" class="col-form-label"><b>Select Data:</b></label>
            <br>            
              <input style="border: 0;line-height: 1.1;padding: 10px 10px;font-size: 1rem;text-align: center;color: #fff;text-shadow: 1px 1px 1px #000;border-radius: 4px;background-color: rgb(95, 158, 160);" type="button" id="mysupp" name="v" data-target="#mymodal" data-toggle="modal" value="Select BPB" class="btn btn-primary btn-lg" style="width: 100%;">              
            </div>

            <div class="col-md-7 mb-3"> 
         <!--    <div class="form-group">
                <div id="reader"></div>
                </div>  -->          
            </div>            
                                        
    </div>

</form>
<div class="form-row">
    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose BPB</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label style="padding-left: 10px;" for="namasupp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="namasupp" id="namasupp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier),id_supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data2 = $row['id_supplier'];
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['namasupp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                    </div>
              
                </div>

                <div class="col-md-12 mb-3">
                     <label><b>BPB Date</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal2" id="startdate_bpb" name="startdate_bpb" 
                value="<?php
                $startdate_bpb ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $startdate_bpb = date("Y-m-d",strtotime($_POST['startdate_bpb']));
                }
                if(!empty($_POST['startdate_bpb'])) {
                    echo $_POST['startdate_bpb'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal2" id="enddate_bpb" name="enddate_bpb" 
                value="<?php
                $enddate_bpb ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $enddate_bpb = date("Y-m-d",strtotime($_POST['enddate_bpb']));
                }
                if(!empty($_POST['enddate_bpb'])) {
                    echo $_POST['enddate_bpb'];
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
                    <div class="col-md-2">
                    <input type="button" id="savebpb" name="savebpb" class="btn btn-warning btn-md" style="width: 100%;" value="Save">
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

      <!-- /.modal-dialog --> 
    </div>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">
            <div class="table-responsive">
            <table id="mytable" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:5%;"></th>                       
                            <th style="width:20%;">No BPB</th>
                            <th style="width:15%;">BPB Date</th>                            
                            <th style="width:22%;">Supplier</th>                          
                            <th style="width:10%;">Currency</th>                            
                            <th style="width:18%;">Total</th>
                            <th style="width:18%;">No Invoice</th>
                            <th style="width:18%;">Tgl Invoice</th>
                            <th style="width:18%;">No Faktur</th>
                            <th style="width:18%;">Tgl Faktur</th>
                            <th style="width:10%;">Action</th>
                        </tr>
                    </thead>

            <tbody id="tbl_bpb">

                <?php

    $sql = mysqli_query($conn2,"select a.no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input,if(no_inv is null OR no_inv = '',upt_no_inv,no_inv) no_inv,if(no_inv is null OR no_inv = '',upt_tgl_inv,tgl_inv) tgl_inv,no_faktur,tgl_faktur from (select no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input,no_inv,tgl_inv,no_faktur,tgl_faktur from tbl_bpb_temp where user_input = '$user' GROUP BY no_bpb) a LEFT JOIN (select no_bpb,upt_no_inv,upt_tgl_inv from bpb_new where upt_no_inv is not null GROUP BY no_bpb) b on b.no_bpb = a.no_bpb");  

    // $resp = simplexml_load_file( 'http://svc.efaktur.pajak.go.id/validasi/faktur/014763379445000/0062478463254/3031300D0609608648016503040201050004208E60CCB161E8ADCC340D1BA76FA4D3A2CE88070E4338A6D819632CEAB809C922' );
    // $json = json_encode($resp);
    // echo $;               

    while ($row = mysqli_fetch_assoc($sql)) {
        $no_inv = isset($row['no_inv']) ? $row['no_inv'] : '-';
        if ($no_inv == '-') {
            $tgl_inv = '-';
        }else{
            $tgl_inv = date("d-M-Y",strtotime($row['tgl_inv']));
        }

        $no_faktur = isset($row['no_faktur']) ? $row['no_faktur'] : '-';
        if ($no_faktur == '-') {
            $tgl_faktur = '-';
        }else{
            $tgl_faktur = date("d-M-Y",strtotime($row['tgl_faktur']));
        }

        if ($no_inv != '-' && $no_faktur != '-') {
            $color = 'class="bg-success"';
        }elseif ($no_inv == '-' && $no_faktur != '-') {
            $color = 'class="bg-warning"';
        }elseif ($no_inv != '-' && $no_faktur == '-') {
            $color = 'class="bg-warning"';
        }else{
            $color = 'class="bg-danger"';
        }
            
            echo '<tr '.$color.'>   
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" class="select_item" value="" checked disabled></td>    
                        <td style="" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                        <td style="" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                        <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                        <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        <td style="" value="'.$row['total'].'">'.number_format($row['total'],2).'</td> 
                        <td style="" value="'.$no_inv.'">'.$no_inv.'</td>
                        <td style="" value="'.$tgl_inv.'">'.$tgl_inv.'</td>
                        <td style="" value="'.$no_faktur.'">'.$no_faktur.'</td>
                        <td style="" value="'.$tgl_faktur.'">'.$tgl_faktur.'</td> 
                        <td><a id="tambah"><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-plus"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Add</i></button></a></td>                  
                             
                       </tr>';
        }
        ?>
            
            </tbody>                    
            </table>   
            </div>                 
<div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <br>
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='update_bpb.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalpo" data-target="#mymodalpo" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_po"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                            
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


<div class="form-row">
    <div class="modal fade" id="modal-scan-inv" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Update Invoice & Faktur</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            
                <div class="form-row">
                <div class="col-md-7 mb-3"> 
                <label for="nama_supp"><b>No BPB</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="mdl_no_bpb" id="mdl_no_bpb" value="">
                </div>
                <div class="col-md-5 mb-3"> 
                <label for="nama_supp"><b>Tgl BPB</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="mdl_tgl_bpb" id="mdl_tgl_bpb" value="">
                </div>
                </div>
                <div class="form-row">
                 <div class="col-md-7 mb-3"> 
                <label for="nama_supp"><b>No Invoice</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="mdl_no_inv" id="mdl_no_inv">
                </div>
                <div class="col-md-5 mb-3"> 
                <label for="nama_supp"><b>Tgl Invoice</b></label> 
                <input type="text" style="font-size: 14px;" name="mdl_tgl_inv" id="mdl_tgl_inv" class="form-control tanggal2" 
                    value="<?php             
                        if(!empty($_POST['mdl_tgl_inv'])) {
                            echo $_POST['mdl_tgl_inv'];
                        }else{
                            echo date("d-m-Y");
                        } ?>">
                </div>   
                <div class="col-md-7 mb-3"> 
                <label for="nama_supp"><b>No Faktur Pajak</b></label> 
                <div class="input-group">
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="mdl_no_faktur" id="mdl_no_faktur"> 
                </div>
                </div>
                <div class="col-md-5 mb-3"> 
                <label for="nama_supp"><b>Tgl Faktur Pajak</b></label> 
                <input type="text" style="font-size: 14px;" name="mdl_tgl_faktur" id="mdl_tgl_faktur" class="form-control tanggal2" 
                    value="<?php             
                        if(!empty($_POST['mdl_tgl_inv'])) {
                            echo $_POST['mdl_tgl_inv'];
                        }else{
                            echo date("d-m-Y");
                        } ?>">
                </div>

                <div class="col-md-12">
                        <label for="nama_supp"><b>Input Link</b></label> 
                    <div class="input-group">
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="input_link" id="input_link" onchange ="onManual()">
                <!-- onchange="testQr()" --> 
                <input style="border: 0;line-height: 1.1;padding: 10px 10px;font-size: 1rem;text-align: center;color: #fff;text-shadow: 1px 1px 1px #000;border-radius: 4px;background-color: rgb(95, 158, 160);" type="button" value="Open Camera" class="btn btn-primary btn-lg" onclick="getscan();">   
                </div>
                </div>

                <div class="col-md-3 mb-3"> 
                </div>
                <div class="col-md-6 mb-3"> 
                    <div id="reader"></div>
                </div>
                <div class="col-md-3 mb-3"> 
                </div>   
            </br>
                    <div class="col-md-9">
                    </div>
                <div class="col-md-3">
            <form id="modal-form3" method="post">
                <div class="modal-footer">
                    <button type="submit" id="send_scan" name="send_scan" class="btn btn-success btn-md" style="width: 100%;"><span class="fa fa-check"></span>
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
 </div>
</div> 

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/html5-qrcode.min.js"></script>
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

<script type="text/javascript">
    $("table tbody tr").on("click", "#tambah", function(){                 
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var user = '<?php echo $user ?>';
        $.ajax({
            type: 'POST',
            url: 'getdatabpbtemp.php', 
            data: {'user':user, 'no_bpb':no_bpb},
            success: function(response) { 
                var datenow = new Date;
                var yyyy = datenow.getFullYear();
                var mm = datenow.getMonth() + 1; // Months start at 0!
                var dd = datenow.getDate();

                if (dd < 10) dd = '0' + dd;
                if (mm < 10) mm = '0' + mm;

                var formattedToday = dd + '-' + mm + '-' + yyyy;
                console.log(formattedToday);
                if (JSON.parse(response)[1] != null) {
                    $('#mdl_no_inv').val(JSON.parse(response)[1]);  
                    $('#mdl_tgl_inv').val(JSON.parse(response)[2]);  
                }else{
                    $('#mdl_no_inv').val('');  
                    $('#mdl_tgl_inv').val(formattedToday);   
                }

                if (JSON.parse(response)[3] != null) {
                    $('#mdl_no_faktur').val(JSON.parse(response)[3]);  
                    $('#mdl_tgl_faktur').val(JSON.parse(response)[4]); 
                }else{
                    $('#mdl_no_faktur').val('');  
                    $('#mdl_tgl_faktur').val(formattedToday);   
                }
            }
        });
        // alert(tgl_bpb);
        $('#mdl_no_bpb').val(no_bpb);
        $('#mdl_tgl_bpb').val(tgl_bpb);
        $('#input_link').val('');
        $('#modal-scan-inv').modal('show');
        });
    function getscan(){
        initScan();
    }
</script>

<script>
    $(document).ready(function() {
    $('#mytable').dataTable();
    // initScan();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>


<script type="text/javascript">

    function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [month, day, year].join('-');
}

    var html5QrcodeScanner = null;

        // Function List :
        // -Initialize Scanner-
        async function initScan() {
            document.getElementById('mdl_no_faktur').value = '';
            if (document.getElementById("reader")) {
                if (html5QrcodeScanner) {
                    await html5QrcodeScanner.clear();
                }

                function onScanSuccess(decodedText, decodedResult) {
                    // handle the scanned code as you like, for example:
                    console.log(`Code matched = ${decodedText}`, decodedResult);

                    // store to input text
                    let breakDecodedText = decodedText.split('-');

                    // document.getElementById('mdl_no_faktur').value = breakDecodedText[0];
                     var url = breakDecodedText[0];
                        $.ajax({
                            type: 'POST',
                            url: 'getdataxml.php', 
                            data: {'url':url},
                            success: function(response) { 
                            console.log(JSON.parse(response));
                            console.log(JSON.parse(response).detailTransaksi);
                            document.getElementById('mdl_no_faktur').value = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;

                            var kd_jns_trans = JSON.parse(response).kdJenisTransaksi;
                            var fg_pengganti = JSON.parse(response).fgPengganti;
                            var no_faktur = JSON.parse(response).nomorFaktur;
                            var tglfaktur = JSON.parse(response).tanggalFaktur;
                            var tglfaktur_h = tglfaktur.split("/").join("-");
                            // var tglfaktur_h = tglfaktur.split("/").reverse().join("-");
                            document.getElementById('mdl_tgl_faktur').value = tglfaktur_h;
                            // let tglfaktur_h = tglfaktur.toLocaleDateString();
                            var npwp_supp = JSON.parse(response).npwpPenjual;
                            var nama_supp = JSON.parse(response).namaPenjual;
                            var alamat_supp = JSON.parse(response).alamatPenjual;
                            var npwp_buyer = JSON.parse(response).npwpLawanTransaksi;
                            var nama_buyer = JSON.parse(response).namaLawanTransaksi;
                            var alamat_buyer = JSON.parse(response).alamatLawanTransaksi;
                            var jml_dpp = JSON.parse(response).jumlahDpp;
                            var jml_ppn = JSON.parse(response).jumlahPpn;
                            var jml_ppn_bm = JSON.parse(response).jumlahPpnBm;
                            var status_approve = JSON.parse(response).statusApproval;
                            var status_faktur = JSON.parse(response).statusFaktur;
                            var no_referensi = JSON.parse(response).referensi;
                            var kd_no_faktur_h = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                            var create_user = '<?php echo $user ?>';

                            $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp_h.php',
                                        data : {'kd_jns_trans': kd_jns_trans,'fg_pengganti': fg_pengganti,'no_faktur': no_faktur,'tglfaktur': tglfaktur_h,'npwp_supp': npwp_supp,'nama_supp': nama_supp,'alamat_supp': alamat_supp,'npwp_buyer': npwp_buyer,'nama_buyer': nama_buyer,'alamat_buyer': alamat_buyer,'jml_dpp': jml_dpp,'jml_ppn': jml_ppn,'jml_ppn_bm': jml_ppn_bm,'status_approve': status_approve,'status_faktur': status_faktur,'no_referensi': no_referensi,'kd_no_faktur_h': kd_no_faktur_h,'create_user': create_user},
                                        success : function(data){
                                            // alert(data);
                                            // document.getElementById('input_link').value = '';
                                        }
                                    }); 

                            if (JSON.parse(response).detailTransaksi.length > 1) {   
                                for (let i = 0; i < JSON.parse(response).detailTransaksi.length; i++) {
                                    // document.getElementById('mdl_no_faktur').value = JSON.parse(response).detailTransaksi[i].nama;
                                    var nama_item = JSON.parse(response).detailTransaksi[i].nama;
                                    var price = JSON.parse(response).detailTransaksi[i].hargaSatuan;
                                    var qty = JSON.parse(response).detailTransaksi[i].jumlahBarang;
                                    var ttl_harga = JSON.parse(response).detailTransaksi[i].hargaTotal;
                                    var diskon = JSON.parse(response).detailTransaksi[i].diskon;
                                    var dpp = JSON.parse(response).detailTransaksi[i].dpp;
                                    var ppn = JSON.parse(response).detailTransaksi[i].ppn;
                                    var tarif_ppn_bm = JSON.parse(response).detailTransaksi[i].tarifPpnbm;
                                    var ppn_bm = JSON.parse(response).detailTransaksi[i].ppnbm;
                                    var kd_no_faktur = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                                    var create_user = '<?php echo $user ?>';
                                    var num = i;
                                    $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp.php',
                                        data : {'nama_item': nama_item,'price': price,'qty': qty,'ttl_harga': ttl_harga,'diskon': diskon,'dpp': dpp,'ppn': ppn,'tarif_ppn_bm': tarif_ppn_bm,'ppn_bm': ppn_bm,'kd_no_faktur': kd_no_faktur,'create_user': create_user,'num': num},
                                        success : function(data){
                                            html5QrcodeScanner.clear();
                                        }
                                    });     
                                }
                            }else{
                                // document.getElementById('mdl_no_faktur').value = JSON.parse(response).detailTransaksi.nama;
                                    var nama_item2 = JSON.parse(response).detailTransaksi.nama;
                                    var price2 = JSON.parse(response).detailTransaksi.hargaSatuan;
                                    var qty2 = JSON.parse(response).detailTransaksi.jumlahBarang;
                                    var ttl_harga2 = JSON.parse(response).detailTransaksi.hargaTotal;
                                    var diskon2 = JSON.parse(response).detailTransaksi.diskon;
                                    var dpp2 = JSON.parse(response).detailTransaksi.dpp;
                                    var ppn2 = JSON.parse(response).detailTransaksi.ppn;
                                    var tarif_ppn_bm2 = JSON.parse(response).detailTransaksi.tarifPpnbm;
                                    var ppn_bm2 = JSON.parse(response).detailTransaksi.ppnbm;
                                    var kd_no_faktur2 = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                                    var create_user = '<?php echo $user ?>';
                                    var num = 1;
                                    // alert(nama_item);
                                    $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp.php',
                                        data : {'nama_item': nama_item2,'price': price2,'qty': qty2,'ttl_harga': ttl_harga2,'diskon': diskon2,'dpp': dpp2,'ppn': ppn2,'tarif_ppn_bm': tarif_ppn_bm2,'ppn_bm': ppn_bm2,'kd_no_faktur': kd_no_faktur2,'create_user': create_user,'num': num},
                                        success : function(data){
                                            html5QrcodeScanner.clear();
                                        }
                                    });   
                            }
                            
                            
                            }
                        });
                    // getdataitem(xml);

                    // getScannedItem(breakDecodedText[0]);

                }

                function onScanFailure(error) {
                    // handle scan failure, usually better to ignore and keep scanning.
                    // for example:
                    console.warn(`Code scan error = ${error}`);
                }

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    /* verbose= */
                    false);

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }

function onManual(){
    var url = document.getElementById('input_link').value;
                        $.ajax({
                            type: 'POST',
                            url: 'getdataxml.php', 
                            data: {'url':url},
                            success: function(response) { 
                            console.log(JSON.parse(response));
                            console.log(JSON.parse(response).detailTransaksi);

                            document.getElementById('mdl_no_faktur').value = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;

                            var kd_jns_trans = JSON.parse(response).kdJenisTransaksi;
                            var fg_pengganti = JSON.parse(response).fgPengganti;
                            var no_faktur = JSON.parse(response).nomorFaktur;
                            var tglfaktur = JSON.parse(response).tanggalFaktur;
                            var tglfaktur_h = tglfaktur.split("/").join("-");
                            document.getElementById('mdl_tgl_faktur').value = tglfaktur_h;
                            // let tglfaktur_h = tglfaktur.toLocaleDateString();
                            var npwp_supp = JSON.parse(response).npwpPenjual;
                            var nama_supp = JSON.parse(response).namaPenjual;
                            var alamat_supp = JSON.parse(response).alamatPenjual;
                            var npwp_buyer = JSON.parse(response).npwpLawanTransaksi;
                            var nama_buyer = JSON.parse(response).namaLawanTransaksi;
                            var alamat_buyer = JSON.parse(response).alamatLawanTransaksi;
                            var jml_dpp = JSON.parse(response).jumlahDpp;
                            var jml_ppn = JSON.parse(response).jumlahPpn;
                            var jml_ppn_bm = JSON.parse(response).jumlahPpnBm;
                            var status_approve = JSON.parse(response).statusApproval;
                            var status_faktur = JSON.parse(response).statusFaktur;
                            var no_referensi = JSON.parse(response).referensi;
                            var kd_no_faktur_h = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                            var create_user = '<?php echo $user ?>';

                            $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp_h.php',
                                        data : {'kd_jns_trans': kd_jns_trans,'fg_pengganti': fg_pengganti,'no_faktur': no_faktur,'tglfaktur': tglfaktur_h,'npwp_supp': npwp_supp,'nama_supp': nama_supp,'alamat_supp': alamat_supp,'npwp_buyer': npwp_buyer,'nama_buyer': nama_buyer,'alamat_buyer': alamat_buyer,'jml_dpp': jml_dpp,'jml_ppn': jml_ppn,'jml_ppn_bm': jml_ppn_bm,'status_approve': status_approve,'status_faktur': status_faktur,'no_referensi': no_referensi,'kd_no_faktur_h': kd_no_faktur_h,'create_user': create_user},
                                        success : function(data){
                                            // alert(data);
                                            // document.getElementById('input_link').value = '';
                                        }
                                    });     

                            if (JSON.parse(response).detailTransaksi.length > 1) {   
                                for (let i = 0; i < JSON.parse(response).detailTransaksi.length; i++) {
                                    // document.getElementById('mdl_no_faktur').value = JSON.parse(response).detailTransaksi[i].nama;
                                    var nama_item = JSON.parse(response).detailTransaksi[i].nama;
                                    var price = JSON.parse(response).detailTransaksi[i].hargaSatuan;
                                    var qty = JSON.parse(response).detailTransaksi[i].jumlahBarang;
                                    var ttl_harga = JSON.parse(response).detailTransaksi[i].hargaTotal;
                                    var diskon = JSON.parse(response).detailTransaksi[i].diskon;
                                    var dpp = JSON.parse(response).detailTransaksi[i].dpp;
                                    var ppn = JSON.parse(response).detailTransaksi[i].ppn;
                                    var tarif_ppn_bm = JSON.parse(response).detailTransaksi[i].tarifPpnbm;
                                    var ppn_bm = JSON.parse(response).detailTransaksi[i].ppnbm;
                                    var kd_no_faktur = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                                    var create_user = '<?php echo $user ?>';
                                    var num = i;
                                    $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp.php',
                                        data : {'nama_item': nama_item,'price': price,'qty': qty,'ttl_harga': ttl_harga,'diskon': diskon,'dpp': dpp,'ppn': ppn,'tarif_ppn_bm': tarif_ppn_bm,'ppn_bm': ppn_bm,'kd_no_faktur': kd_no_faktur,'create_user': create_user,'num': num},
                                        success : function(data){
                                            document.getElementById('input_link').value = '';
                                        }
                                    });     
                                }
                            }else{
                                // document.getElementById('mdl_no_faktur').value = JSON.parse(response).detailTransaksi.nama;
                                    var nama_item2 = JSON.parse(response).detailTransaksi.nama;
                                    var price2 = JSON.parse(response).detailTransaksi.hargaSatuan;
                                    var qty2 = JSON.parse(response).detailTransaksi.jumlahBarang;
                                    var ttl_harga2 = JSON.parse(response).detailTransaksi.hargaTotal;
                                    var diskon2 = JSON.parse(response).detailTransaksi.diskon;
                                    var dpp2 = JSON.parse(response).detailTransaksi.dpp;
                                    var ppn2 = JSON.parse(response).detailTransaksi.ppn;
                                    var tarif_ppn_bm2 = JSON.parse(response).detailTransaksi.tarifPpnbm;
                                    var ppn_bm2 = JSON.parse(response).detailTransaksi.ppnbm;
                                    var kd_no_faktur2 = JSON.parse(response).kdJenisTransaksi+JSON.parse(response).fgPengganti+JSON.parse(response).nomorFaktur;
                                    var create_user = '<?php echo $user ?>';
                                    var num = 1;
                                    // alert(nama_item);
                                    $.ajax({
                                        type : 'post',
                                        url : 'insert_scan_faktur_temp.php',
                                        data : {'nama_item': nama_item2,'price': price2,'qty': qty2,'ttl_harga': ttl_harga2,'diskon': diskon2,'dpp': dpp2,'ppn': ppn2,'tarif_ppn_bm': tarif_ppn_bm2,'ppn_bm': ppn_bm2,'kd_no_faktur': kd_no_faktur2,'create_user': create_user,'num': num},
                                        success : function(data){
                                            document.getElementById('input_link').value = '';
                                        }
                                    });   
                            }
                            
                            
                            }
                        });
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
    $('.tanggal2').datepicker({
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
    $("input[type=radio]").click(function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_total = 0;
    $("input[type=radio]").prop('disabled', true);             
    $("input[type=radio]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
    var select_pi = $(this).closest('tr').find('td:eq(2) input');
    select_pi.prop('disabled', false);                    
    sum_sub += price;
    sum_tax += tax;
    sum_total = sum_sub + sum_tax;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#pajak").val(formatMoney(sum_tax));    
    $("#total").val(formatMoney(sum_total));
    $("#select").val("1");                    
});        
</script>

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

<!-- <script type="text/javascript">
    $("input[type=radio]:checked").click(function(){        
    $(this).closest('tr').find('td:eq(2) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(2) input').val("");        
    })
</script> -->


<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        var nama_supp = $('select[name=namasupp] option').filter(':selected').val(); 
        var start_date = document.getElementById('startdate_bpb').value;
        var end_date = document.getElementById('enddate_bpb').value;        
         
             
        $.ajax({
            type:'POST',
            url:'cari_bpb_verif3.php',
            data: {'nama_supp':nama_supp, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#details').html(data);
                // alert(data);  
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
 
    return false; 
    });


</script>

<script>
    $("#modal-form2").on("click", "#savebpb", function(){
        $("input[type=checkbox]:checked").each(function () {
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(3)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(4)').attr('value');
        var total = $(this).closest('tr').find('td:eq(5)').attr('value');
        var user = '<?php echo $user ?>';
             
        $.ajax({
            type:'POST',
            url:'insert_bpb_temp.php',
            data: {'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb,'supplier':supplier, 'curr':curr, 'total':total, 'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                // return false; 
            },
            success: function(response){
                // console.log(response);
                $('#mymodal').modal('toggle');
                $('#mymodal').modal('hide');
                 // alert(response);
            var user = '<?php echo $user ?>';
            //     $.ajax({
            // type:'POST',
            // url:'load_bpb_temp2.php',
            // data: { 'user':user},
            // cache: 'false',
            // close: function(e){
            //     e.preventDefault();
            //     return false; 
            // },
            // success: function(data){
            //     $('#tbl_bpb').html(data);
            //     $('#table-bpb tbody tr').remove(); 
            //     },
            // error: function (xhr, ajaxOptions, thrownError) {
            //     console.log(xhr);
            //     alert(xhr);
            // }
            // });
            window.location.reload();
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
                // return false; 
    });


    $("#modal-form3").on("click", "#send_scan", function(){
        var no_bpb = document.getElementById('mdl_no_bpb').value;
        var no_inv = document.getElementById('mdl_no_inv').value;
        var tgl_inv = document.getElementById('mdl_tgl_inv').value;
        var no_faktur = document.getElementById('mdl_no_faktur').value;
        var tgl_faktur = document.getElementById('mdl_tgl_faktur').value;
        var user = '<?php echo $user ?>';
             
        $.ajax({
            type:'POST',
            url:'update_bpb_faktur.php',
            data: {'no_bpb':no_bpb, 'no_inv':no_inv,'tgl_inv':tgl_inv, 'no_faktur':no_faktur, 'tgl_faktur':tgl_faktur, 'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                // return false; modal-scan-inv
            },
            success: function(response){
                // console.log(response);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });              
                // return false; 
    });
</script>


<script>
    $("#form-data").on("click", "#mysupp", function(){
        var user = '<?php echo $user ?>';
        $('#mytable tbody tr').remove(); 
             
        $.ajax({
            type:'POST',
            url:'delete_bpb_temp.php',
            data: {'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
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
    var unik_code = document.getElementById('unik_code').value;  
    var create_user = '<?php echo $user; ?>';

        $.ajax({
            type:'POST',
            url:'log_bpb_faktur_inv2.php',
            data: {'unik_code':unik_code, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
        $("input[type=checkbox]:checked").each(function () {
        var no_dok = document.getElementById('no_dok').value;        
        var tgl_dok = document.getElementById('tanggal').value;
        var no_inv = $(this).closest('tr').find('td:eq(6)').attr('value'); 
        var tgl_inv = $(this).closest('tr').find('td:eq(7)').attr('value'); 
        var no_faktur = $(this).closest('tr').find('td:eq(8)').attr('value'); 
        var tgl_faktur = $(this).closest('tr').find('td:eq(9)').attr('value');   
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');                               
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';    
        var unik_code = document.getElementById('unik_code').value;     
        if(no_faktur != '-' && tgl_faktur != '-' && no_bpb != ''){
        $.ajax({
            type:'POST',
            url:'insert_bpb_fakturinv3.php',
            data: {'no_dok':no_dok, 'tgl_dok':tgl_dok, 'no_inv':no_inv, 'tgl_inv':tgl_inv, 'no_faktur':no_faktur, 'tgl_faktur':tgl_faktur, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'supplier':supplier, 'create_user':create_user, 'unik_code':unik_code},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert("Data saved successfully");
                window.location = 'update_bpb.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    } 
    });
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });

        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please input data");
        }else{
            alert("Data saved successfully");
        }
    });
</script>

<!--<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>-->

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalpo').modal('show');
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(8)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');   

    $.ajax({
    type : 'post',
    url : 'ajaxpocbd.php',
    data : {'no_po': no_po},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_po').html(no_po);
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');                               
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
