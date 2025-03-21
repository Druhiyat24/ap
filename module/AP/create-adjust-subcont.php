<?php include '../header.php' ?>

<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">FORM ADJUSMENT SUBCONTRACTOR</h2>
    <div class="box">
        <div class="box header">
            <form id="form-data" method="post">
                <div class="form-row">
                    <div class="col-md-3 mb-3">            
                        <label for="pajak" class="col-form-label" style="width: 150px;"><b>Document Number</b></label>
                        <?php
                        $sql = mysqli_query($conn2,"select max(no_dok) from ca_adjust_input");
                        $row = mysqli_fetch_array($sql);
                        $kodepay = $row['max(no_dok)'];
                        $urutan = (int) substr($kodepay, 13, 5);
                        $urutan++;
                        $bln = date("m");
                        $thn = date("y");
                        $huruf = "ADS/NAG/$bln$thn/";
                        $kodepay = $huruf . sprintf("%05s", $urutan);

                        echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
                        ?>
                    </div>

                    <div class="col-md-2 mb-3">            
                        <label for="total" class="col-form-label" style="width: 150px;"><b>Date Periode</b></label>
                        <input type="text" style="font-size: 15px;" name="tgl_doc" id="tgl_doc" class="form-control tanggal" 
                        value="<?php 
                        $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null;            
                        if(!empty($_POST['nama_type'])) {
                            echo $_POST['tgl_doc'];
                        }
                        else{
                            echo date("d-m-Y");
                        } ?>" autocomplete='off'>
                    </div>

                    <div class="col-md-6" >
                        <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                        <textarea style="font-size: 15px; text-align: left;" cols="40" rows="1" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                    </div>
                </div>
            </br>

            <input type="hidden" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
            echo gethostbyaddr($_SERVER['REMOTE_ADDR']); echo ' '; if($_SERVER['REMOTE_ADDR'] == '::1'){ echo 'LOCALHOST';}else{ echo $_SERVER['REMOTE_ADDR'];}
        ?>" >

</div>                
</form>
<div class="box body">
    <div class="row">

        <div class="col-md-12">

            <div class="table-wrapper">
                <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr><th class="text-center" style="width: 2%">-</th>
                            <th class="text-center" style="width: 10%">Supplier</th>
                            <th class="text-center" style="width: 12%">No Po</th>
                            <th class="text-center" style="width: 12%">No ws</th>
                            <th class="text-center" style="width: 12%">Id Item</th>
                            <th class="text-center" style="width: 10%">Qty</th>
                            <th class="text-center" style="width: 10%">Unit</th>
                            <th class="text-center" style="width: 10%">Price</th>
                            <th class="text-center" style="width: 2%"> Action </th>
                        </tr>
                    </thead>

                    <tbody id="tbody2">
                        <tr style="display: none;">
                            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                            <td >
                                <select class="form-control txt_supplier" name="txt_supplier" id="txt_supplier" > <option value="-" > - </option> 
                            </select>
                        </td>
                        <td>
                            <select class="form-control selectpicker txt_po" name="txt_po[]" id="txt_po" data-live-search="true" data-width="180px" data-size="5">
                                <option value="-">-</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control selectpicker txt_ws" name="txt_ws[]" id="txt_ws" data-live-search="true" data-width="180px" data-size="5">
                                <option value="-">-</option>
                            </select>
                        </td>
                        <td >
                            <select class="form-control txt_item" name="txt_item" id="txt_item" > <option value="-" > - </option> 
                        </select>
                    </td>
                    <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" oninput="modal_input_amt(value)" autocomplete="off">
                    </td>
                    <td >
                        <select class="form-control txt_unit" name="txt_unit" id="txt_unit" > <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"SELECT * FROM masterpilihan where kode_pilihan='Satuan'"); foreach ($sql as $pil) : echo'<option value="'.$pil["nama_pilihan"].'"> '.$pil["nama_pilihan"].' </option>'; endforeach; ?>
                    </select>
                </td>
                <td>
                    <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
                </td>

                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
            </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="10" align="center">
                <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
                <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Interject Row</button>
                <button type="button" class="btn btn-danger" onclick="hapusbaris()">Delete Row</button>
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
        <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Amount</b></label>
        <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="total_value" name="total_value" placeholder="0.00" readonly>
        <input type="hidden" name="total_value_h" id="total_value_h" value="">
    </div>
</br>

</div>
<div class="form-row col">
    <div class="col-md-3 mb-3">                              
        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='adjust-subcont.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
<script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
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
        $('.select2_coa').select2({
            dropdownAutoWidth : true
        });

        $('.select2_costcenter').select2({
            dropdownAutoWidth : true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    $(document).on('change', '.txt_supplier', function () {
    const selectedProfCtr = $(this).val();  // Ambil nilai prof_ctr yang dipilih
    const costCtrDropdown = $(this).closest('tr').find('.txt_po');  // Temukan dropdown cost_ctr dalam baris yang sama

    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (selectedProfCtr && selectedProfCtr !== '-') {
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getDataPoHeader.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { supplier: selectedProfCtr },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                // Periksa apakah respons valid
                if (response && response.length > 0) {
                    $.each(response, function (index, po) {
                        console.log(po);  // Debug data yang diterima
                        costCtrDropdown.append(`<option value="${po.pono}">${po.pono}</option>`);
                    });

                    // Re-inisialisasi selectpicker setelah menambah opsi
                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.error('Tidak ada data yang diterima dari server.');
                    // alert('Tidak ada data cost center yang tersedia.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Gagal mengambil data po.');
            }
        });
    } else {
        // Jika tidak ada pilihan valid, tambahkan opsi default dan refresh selectpicker
        // costCtrDropdown.append('<option value="-"> - </option>');
        costCtrDropdown.selectpicker('refresh');
    }
});

    $(document).on('change', '.txt_po', function () {
    const selectedProfCtr = $(this).val();  // Ambil nilai prof_ctr yang dipilih
    const costCtrDropdown = $(this).closest('tr').find('.txt_ws');  // Temukan dropdown cost_ctr dalam baris yang sama
    // alert(selectedProfCtr);
    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (selectedProfCtr && selectedProfCtr !== '-') {
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getDataWS.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { pono: selectedProfCtr },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                console.log(response); 
                // Periksa apakah respons valid
                if (response && response.length > 0) {
                    $.each(response, function (index, data) {
                        console.log(data);  // Debug data yang diterima
                        costCtrDropdown.append(`<option value="${data.id_jo}">${data.ws}</option>`);
                    });

                    // Re-inisialisasi selectpicker setelah menambah opsi
                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.error('Tidak ada data yang diterima dari server.');
                    // alert('Tidak ada data cost center yang tersedia.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Gagal mengambil data po.');
            }
        });
    } else {
        // Jika tidak ada pilihan valid, tambahkan opsi default dan refresh selectpicker
        // costCtrDropdown.append('<option value="-"> - </option>');
        costCtrDropdown.selectpicker('refresh');
    }
});


    $(document).on('change', '.txt_ws', function () {
    const selectedProfCtr = $(this).val();  // Ambil nilai prof_ctr yang dipilih
    const costCtrDropdown = $(this).closest('tr').find('.txt_item');  // Temukan dropdown cost_ctr dalam baris yang sama
    // alert(selectedProfCtr);
    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (selectedProfCtr && selectedProfCtr !== '-') {
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getDataItem.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { kpno: selectedProfCtr },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                console.log(response); 
                // Periksa apakah respons valid
                if (response && response.length > 0) {
                    $.each(response, function (index, data) {
                        console.log(data);  // Debug data yang diterima
                        costCtrDropdown.append(`<option value="${data.id_item}">${data.id_item}</option>`);
                    });

                    // Re-inisialisasi selectpicker setelah menambah opsi
                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.error('Tidak ada data yang diterima dari server.');
                    // alert('Tidak ada data cost center yang tersedia.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
                alert('Gagal mengambil data ITEM.');
            }
        });
    } else {
        // Jika tidak ada pilihan valid, tambahkan opsi default dan refresh selectpicker
        // costCtrDropdown.append('<option value="-"> - </option>');
        costCtrDropdown.selectpicker('refresh');
    }
});
</script>

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
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
    $(function() {
      //Initialize Select2 Elements
      var selectcoba = rowCount;
      $('.rowCount').select2({
       theme: 'bootstrap4'
   })
      //Initialize Select2 Elements
      $('.select2add').select2({
        theme: 'bootstrap4'
    })
  });
    $coa = '';
    var element1 = `
    <tr>
    <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
    <td>
    <select class="form-control selectpicker txt_supplier" name="txt_supplier" id="txt_supplier" data-live-search="true" data-width="250px" data-size="5">
    <option value="-">-</option>
    <?php
    $sql = mysqli_query($conn1, "SELECT DISTINCT id_supplier, Supplier AS nama_supp FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
    foreach ($sql as $supp) : ?>
        <option value="<?= $supp['nama_supp']; ?>"><?= $supp['nama_supp']; ?></option>
    <?php endforeach; ?>
    </select>
    </td>
    <td>
    <select class="form-control selectpicker txt_po" name="txt_po[]" id="txt_po" data-live-search="true" data-width="180px" data-size="5">
    <option value="-">-</option>
    </select>
    </td>
    <td>
    <select class="form-control selectpicker txt_ws" name="txt_ws[]" id="txt_ws" data-live-search="true" data-width="180px" data-size="5">
    <option value="-">-</option>
    </select>
    </td>
    <td>
    <select class="form-control selectpicker txt_item" name="txt_item[]" id="txt_item" data-live-search="true" data-width="180px" data-size="5">
    <option value="-">-</option>
    
    </select>
    </td>
    <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" oninput="modal_input_amt(value)" autocomplete="off"></td>
    <td>
    <select class="form-control selectpicker txt_unit" name="txt_unit" id="txt_unit" data-live-search="true" data-width="150px" data-size="5">
    <option value="-">-</option>
    <?php
    $sql = mysqli_query($conn1, "SELECT * FROM masterpilihan where kode_pilihan='Satuan'");
    foreach ($sql as $pil) : ?>
        <option value="<?= $pil['nama_pilihan']; ?>"><?= $pil['nama_pilihan']; ?></option>
    <?php endforeach; ?>
    </select>
    </td>
    <td><input style="text-align: right; font-size: 12px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
    <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
    </tr>`;



    row.innerHTML = element1;    
}

function deleteRow()
{
    try
    {
        var table = document.getElementById("tbody2");
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
                $(function() {
                    $('.selectpicker').selectpicker();

                });

                $(document).ready(function () {
                    $('.tanggal').datepicker({
                        format: "dd-mm-yyyy",
                        autoclose:true
                    });
                });
                var element2  = `
                <tr>
                <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                <td>
                <select class="form-control selectpicker txt_supplier" name="txt_supplier" id="txt_supplier" data-live-search="true" data-width="250px" data-size="5">
                <option value="-">-</option>
                <?php
                $sql = mysqli_query($conn1, "SELECT DISTINCT id_supplier, Supplier AS nama_supp FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                foreach ($sql as $supp) : ?>
                    <option value="<?= $supp['nama_supp']; ?>"><?= $supp['nama_supp']; ?></option>
                <?php endforeach; ?>
                </select>
                </td>
                <td>
                <select class="form-control selectpicker txt_po" name="txt_po[]" id="txt_po" data-live-search="true" data-width="180px" data-size="5">
                <option value="-">-</option>
                </select>
                </td>
                <td>
                <select class="form-control selectpicker txt_ws" name="txt_ws[]" id="txt_ws" data-live-search="true" data-width="180px" data-size="5">
                <option value="-">-</option>
                </select>
                </td>
                <td>
                <select class="form-control selectpicker txt_item" name="txt_item" id="txt_item" data-live-search="true" data-width="180px" data-size="5">
                <option value="-">-</option>
                <?php
                $sql = mysqli_query($conn1, "SELECT DISTINCT id_item, CONCAT(id_item,' | ',itemdesc) itemnya from masteritem");
                foreach ($sql as $item) : ?>
                    <option value="<?= $item['id_item']; ?>"><?= $item['id_item']; ?></option>
                <?php endforeach; ?>
                </select>
                </td>
                <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" oninput="modal_input_amt(value)" autocomplete="off"></td>
                <td>
                <select class="form-control selectpicker txt_unit" name="txt_unit" id="txt_unit" data-live-search="true" data-width="150px" data-size="5">
                <option value="-">-</option>
                <?php
                $sql = mysqli_query($conn1, "SELECT * FROM masterpilihan where kode_pilihan='Satuan'");
                foreach ($sql as $pil) : ?>
                    <option value="<?= $pil['nama_pilihan']; ?>"><?= $pil['nama_pilihan']; ?></option>
                <?php endforeach; ?>
                </select>
                </td>
                <td><input style="text-align: right; font-size: 12px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
                </tr>`;
                var newRow = table.insertRow(i+1);
                newRow.innerHTML = element2;

            }

        }
    } catch(e)
    {
        alert(e);
    }
}


async function hapusbaris(){
 await deleteRow()
 console.log("result");
 modal_input_amt();
}
</script>



<script type="text/javascript">
  function modal_input_amt(){ 

    var table = document.getElementById("tbody2");
    var total = 0;
    for (var i = 1; i < (table.rows.length); i++) {

        var qty = document.getElementById("tbody2").rows[i].cells[5].children[0].value || 0;
        var price = document.getElementById("tbody2").rows[i].cells[7].children[0].value || 0;
        total += parseFloat(qty) * parseFloat(price);

        document.getElementsByName("total_value_h")[0].value = total.toFixed(2);
        document.getElementsByName("total_value")[0].value = formatMoney(total.toFixed(2));

    }
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
            var no_doc = document.getElementById('no_doc').value;        
            var tgl_doc = document.getElementById('tgl_doc').value; 
            var deskripsi = document.getElementById('pesan').value;                               
            var supplier = $(this).closest('tr').find('td:eq(1)').find('select[id=txt_supplier] option').filter(':selected').val();   
            var no_po = $(this).closest('tr').find('td:eq(2)').find('select[id=txt_po] option').filter(':selected').val();  
            var id_jo = $(this).closest('tr').find('td:eq(3)').find('select[id=txt_ws] option').filter(':selected').val(); 
            var no_ws = $(this).closest('tr').find('td:eq(3)').find('select[id=txt_ws] option').filter(':selected').text();  
            var id_item = $(this).closest('tr').find('td:eq(4)').find('select[id=txt_item] option').filter(':selected').val();   
            var qty = $(this).closest('tr').find('td:eq(5) input').val(); 
            var unit = $(this).closest('tr').find('td:eq(6)').find('select[id=txt_unit] option').filter(':selected').val();
            var price = $(this).closest('tr').find('td:eq(7) input').val();
            var total_amount = document.getElementById('total_value_h').value; 
            var create_user = '<?php echo $user; ?>';


            if (total_amount != 0) { 
                $.ajax({
                    type:'POST',
                    url:'insert_adj_subcont.php',
                    data: {'no_doc':no_doc, 'tgl_doc':tgl_doc, 'deskripsi':deskripsi, 'supplier':supplier, 'no_po':no_po, 'no_ws':no_ws, 'id_item':id_item,'qty':qty, 'unit':unit, 'price':price, 'create_user':create_user, 'id_jo':id_jo},
                    cache: 'false',
                    close: function(e){
                        e.preventDefault();
                    },
                    success: function(response){
                        console.log(response);
                  // alert(response);

                  window.location = 'adjust-subcont.php';
              },
              error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
            }

        });

        if(document.getElementById('total_value_h').value == ''){
            alert("Please Input Amount");
        }else if(document.getElementById('total_value_h').value == '0.00'){
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

</body>

</html>
