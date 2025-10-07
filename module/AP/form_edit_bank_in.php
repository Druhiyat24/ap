<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }

    .card-header {
      display: flex !important;
      justify-content: flex-start !important;
      align-items: center !important;
  }

  .card-header h5 {
      margin-left: 0 !important;
  }

  .custom-col {
      flex: 0 0 12.5%; 
      max-width: 12.5%;
  }


</style>

<?php 
$doc_num = base64_decode($_GET['doc_num']); 

$sql = mysqli_query($conn2,"select * from tbl_bankin_arcollection where doc_num = '$doc_num'");
$row = mysqli_fetch_array($sql);                         ;
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <div class="d-flex align-items-center justify-content-start">
        <img src="../../images/note.png" alt="Bank Logo" 
        style="width:25px; height:auto; margin-right:10px;">
        <h5 class="mb-0 text-white">FORM EDIT BANK IN</h5>
    </div>
</div>

<form id="form-data" method="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-2">
            <div class="form-row">

                <div class="col-md-3 mb-3">            
                    <label for="pajak" style="width: 150px;"><b>Doc Number</b></label>
                    <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="no_bankin" name="no_bankin" value="<?= $doc_num; ?>">
                </div>

                <div class="col-md-2 mb-3">            
                    <label for="total" style="width: 150px;"><b>Date</b></label>
                    <input type="text" style="font-size: 13px;" name="tgl_bankin" id="tgl_bankin" class="form-control form-control-sm tanggal" 
                    value="<?php if(!empty($doc_num)) {
                        echo date("d-m-Y",strtotime($row['date']));
                    }
                    else{
                        echo date("d-m-Y");
                    } ?>" autocomplete='off'>
                </div>

                <div class="col-md-3 mb-3">            
                    <label for="nama_supp" style="width: 150px;"><b>Reference</b></label>            
                    <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="ref_data" name="ref_data" value="<?php 
                    if(!empty($doc_num)) {
                        echo $row['ref_data'];
                    }
                    else{
                        echo '';
                    } 
                ?>">
            </div> 
            <div class="col-md-4 mb-3"></div>

            <div class="col-md-3 mb-3">            
                <label for="nama_supp"><b>Source</b></label>            
                <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">                                   
                    <?php
                    $customer = $row['customer'];  
                    $isSelected = ' selected="selected"';                      
                    if(!empty($doc_num)) {
                        echo '<option value="'.$customer.'"'.$isSelected.'">'. $customer .'</option>'; 
                    }
                    else{
                        echo '<option value="Unrealize"  selected="true">Unrealize</option>'; 
                    }

                    if ($customer != 'Unrealize') {
                        echo '<option value="Unrealize" >Unrealize</option>'; 
                    }

                    $sql_supp = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' and Supplier != '$customer' order by Supplier ASC");
                    while ($row_supp = mysqli_fetch_array($sql_supp)) {
                        $data = $row_supp['Supplier'];
                        if($row_supp['Supplier'] == $_POST['nama_supp']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';

                        }
                        echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';   
                    }?>
                </select>  
            </div>

            <div class="col-md-2 mb-3">            
                <label for="nama_supp" style="width: 150px;"><b>Account</b></label>            
                <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="accountid" name="accountid" value="<?php 
                if(!empty($doc_num)) {
                    echo $row['akun'];
                }
                else{
                    echo '';
                } 
            ?>">
        </div>

        <div class="col-md-2 mb-3">            
            <label for="nama_supp" style="width: 150px;"><b>Bank</b></label>            
            <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="nama_bank" name="nama_bank" value="<?php 
            if(!empty($doc_num)) {
                echo $row['bank'];
            }
            else{
                echo '';
            } 
        ?>">
    </div>

    <div class="col-md-1 mb-3">            
        <label for="nama_supp" style="width: 150px;"><b>Curr</b></label>            
        <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="valuta" name="valuta" value="<?php 
        if(!empty($doc_num)) {
            echo $row['curr'];
        }
        else{
            echo '';
        } 
    ?>">
</div>
<div class="col-md-4 mb-3"></div>

<div class="col-md-3 mb-3">            
    <label for="nama_supp" style="width: 150px;"><b>Coa</b></label>            
    <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="no_coa_view" name="no_coa_view" value="<?php 
    if(!empty($doc_num)) {
        $id_coa = $row['id_coa'];
        $sql_coa = mysqli_query($conn2,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa = '$id_coa'");             
        $row_coa = mysqli_fetch_array($sql_coa); 
        echo $row_coa['coa'];
    }
    else{
        echo '';
    } 
?>">

<input type="hidden" readonly style="font-size: 13px;" class="form-control form-control-sm" id="no_coa" name="no_coa" value="<?php 
if(!empty($doc_num)) {
    echo $row['id_coa'];
}
else{
    echo '';
} 
?>">
</div>

<div class="col-md-2 mb-3">            
   <label for="profit_center" style="width: 150px;"><b>Profit Center</b></label>            
   <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onChange="UbahCostArc(this.value)">                                   
    <?php
    $profit_center = $row['profit_center'];  
    $isSelected = ' selected="selected"';  
    $sql_pctr = mysqli_query($conn2,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where kode_pc = '$profit_center'");             
    $row_pctr = mysqli_fetch_array($sql_pctr); 

    if(!empty($doc_num)) {
        echo '<option value="'.$profit_center.'"'.$isSelected.'">'. $row_pctr['tampil'] .'</option>'; 
    }
    else{
        echo '<option value="" disabled selected="true">Select Profit Center</option>'; 
    }


    $sql_pc = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$profit_center'");
    while ($row_pc = mysqli_fetch_array($sql_pc)) {
        $data = $row_pc['tampil'];
        $code_combine = $row_pc['kode_pc'];
        if($row_pc['kode_pc'] == $_POST['profit_center']){
            $isSelected = ' selected="selected"';
        }else{
            $isSelected = '';
        }
        echo '<option value="'.$code_combine.'"'.$isSelected.'">'. $data .'</option>';    
    }
    ?>
</select>  
</div>

<div class="col-md-2 mb-3">            
   <label for="nama_supp"  style="width: 150px;"><b>Cost Center</b></label>            
   <select class="form-control selectpicker" name="cost" id="cost" data-dropup-auto="false" data-live-search="true">
    <option value="" disabled selected="true">Select Cost Center</option>
</select>  
</div>
<div class="col-md-5 mb-3"></div>

<div class="col-md-2 mb-3 custom-col">            
  <label for="amount" style="width: 150px;"><b>Amount</b></label>            
  <div class="input-group">
    <input type="text" class="form-control" id="amount" name="amount" style="font-size: 13px; text-align: right;" placeholder="0.00"
    value="<?php echo !empty($doc_num) ? number_format($row['amount'], 2) : ''; ?>">
</div>
</div>

<div class="col-md-2 mb-3 custom-col">            
  <label for="rate" style="width: 150px;"><b>Rate</b></label>            
  <div class="input-group">
    <input type="text" class="form-control" id="rate" name="rate" style="font-size: 13px; text-align: right;" placeholder="0.00"
    value="<?php echo !empty($doc_num) ? number_format($row['rate'], 2) : ''; ?>" <?php echo ($row['curr'] === 'IDR') ? 'readonly' : ''; ?>>
</div>
</div>

<div class="col-md-2 mb-3">            
    <label for="nama_supp" style="width: 150px;"><b>Equivalent IDR</b></label>            
    <div class="input-group" >
        <!--         <input type="hidden" min="0" style="font-size: 13px;text-align: right;" class="form-control" id="eqv_idr_h" name="eqv_idr_h" value="" > -->
        <input type="text" style="font-size: 13px;text-align: right;" class="form-control" id="eqv_idr" name="eqv_idr" value="<?php echo !empty($doc_num) ? number_format($row['eqv_idr'], 2) : ''; ?>" placeholder="0.00" readonly>
    </div>
</div>
<div class="col-md-7 mb-3"> </div>

<div class="col-md-5 mb-3"> 
    <label for="nama_supp"><b>Descriptions</b></label>         
    <div class="d-flex">
        <textarea 
        class="form-control me-2"
        style="font-size: 13px; text-align: left;" 
        cols="30" rows="3"
        name="pesan" id="pesan"
        placeholder="descriptions..." required><?php echo !empty($doc_num) ? $row['deskripsi'] : ''; ?></textarea>   
        
    </div>
</div>

<div class="col-md-2 mb-3 mt-4">
    <div class="form-group mt-2">

        <button 
        type="button"
        name="edit_data"
        id="edit_data"
        class="btn btn-success align-self-start"
        style="line-height: 1; padding: 4px 12px; font-size: 0.875rem; border-radius: 6px; height: 32px; margin-left: 10px;">
        <i class="fas fa-save"></i> Save
    </button>

    <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>
</div>
</div>

</div>
</div>
</div>
</form>
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
    function formatLiveIndo(value) {
      value = String(value).replace(/[^0-9,]/g, '');
      if(value === '') return '';
      let parts = value.split(',');
      let intPart = parts[0];
      let decPart = parts[1] || '';
  decPart = decPart.substring(0,2); // batasi 2 desimal
  intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  return decPart ? intPart + ',' + decPart : intPart;
}

function formatLiveEn(value) {
  value = String(value).replace(/[^0-9.]/g, '');
  if (value === '') return '';

  if (value.endsWith('.')) {
    let intPart = value.slice(0, -1);
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return intPart + '.'; 
}

let parts = value.split('.');
let intPart = parts[0];
let decPart = parts[1] || '';

decPart = decPart.substring(0, 2);

intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

return decPart ? intPart + '.' + decPart : intPart;
}


document.getElementById('amount').addEventListener('input', function(e) {
    let amount = document.getElementById('amount');
    amount.value = amount.value.replace(/,/g, '');
    let rate = document.getElementById('rate');
    rate.value = rate.value.replace(/,/g, '');
    let ttl_idr = amount.value * rate.value;  
    $("#eqv_idr").val(formatMoney(ttl_idr));

    const el = e.target;
    const origLen = el.value.length;
    const selStart = el.selectionStart;
    const offsetFromEnd = origLen - selStart;

    const newVal = formatLiveEn(el.value);
    el.value = newVal;

    const newLen = newVal.length;
    let newPos = newLen - offsetFromEnd;
    if (newPos < 0) newPos = 0;
    try { el.setSelectionRange(newPos, newPos); } catch (err) {}

});



document.getElementById('rate').addEventListener('input', function(e) {
    let amount = document.getElementById('amount');
    amount.value = amount.value.replace(/,/g, '');
    let rate = document.getElementById('rate');
    rate.value = rate.value.replace(/,/g, '');
    let ttl_idr = amount.value * rate.value;  
    $("#eqv_idr").val(formatMoney(ttl_idr));

    const el = e.target;
    const origLen = el.value.length;
    const selStart = el.selectionStart;
    const offsetFromEnd = origLen - selStart;

    const newVal = formatLiveEn(el.value);
    el.value = newVal;

    const newLen = newVal.length;
    let newPos = newLen - offsetFromEnd;
    if (newPos < 0) newPos = 0;
    try { el.setSelectionRange(newPos, newPos); } catch (err) {}
});

// // saat form submit -> ubah ke angka mentah (tanpa format)
// document.querySelector('form').addEventListener('submit', function() {
//     let input = document.getElementById('amount');
//     input.value = input.value.replace(/,/g, '');
// });
</script>

<script type="text/javascript">
    $(document).on("click", "#edit_data", function () {
        let doc_num         = $("#no_bankin").val();
        let date            = $("#tgl_bankin").val();
        let customer        = $("#nama_supp").val();
        let profit_center   = $("#profit_center").val();
        let amount          = $("#amount").val().replace(/,/g, '');
        let rate            = $("#rate").val().replace(/,/g, '');
        let eqv_idr         = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi       = $("#pesan").val();
        let akun            = $("#accountid").val();
        let no_coa          = $("#no_coa").val();
        let curr            = $("#valuta").val();
        var create_user     = '<?php echo $user; ?>';

        console.log ("doc_num : " + doc_num);
        console.log ("date : " + date);
        console.log ("customer : " + customer);
        console.log ("profit_center : " + profit_center);
        console.log ("amount : " + amount);
        console.log ("rate : " + rate);
        console.log ("eqv_idr : " + eqv_idr);
        console.log ("deskripsi : " + deskripsi);


        if (date === "" || rate < 1 || eqv_idr < 1 || profit_center < 1) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "The data will be updated.",
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
                url: "update_bi_ar_collection.php", // ganti sesuai file PHP kamu
                data: {
                    doc_num: doc_num,
                    date: date,
                    customer: customer,
                    profit_center: profit_center,
                    amount: amount,
                    rate: rate,
                    eqv_idr: eqv_idr,
                    deskripsi: deskripsi,
                    akun: akun,
                    no_coa: no_coa,
                    curr: curr,
                    create_user: create_user
                },
                success: function (res) {
                    if (res.trim() === "OK") {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Data has been successfully updated!"
                        }).then(() => {
                            window.location.href = "bank-in.php";
                        });
                    } else {
                        Swal.fire("Error", res, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to update : " + xhr.responseText
                    });
                }
            });
            }
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
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2021",
            autoclose:true
        });
    });
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
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

</body>

</html>
