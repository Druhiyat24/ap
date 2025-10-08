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

                    ?>
                </select>  
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
        <div class="col-md-3">
            <label for="no_bk" style="width: 150px;" ><b>No Bank Out </b></label>  
            <select class="form-control selectpicker" name="no_bk" id="no_bk" data-live-search="true" required onchange="this.form.submit()" data-size="10">
                <?php
                $sqldet_bk = mysqli_query($conn1,"select * from b_bankin_none where no_bankin = '$doc_num' and no_reff != ''");
                $rowdet_bk = mysqli_fetch_array($sqldet_bk);
                $no_reff = $rowdet_bk['no_reff'];
                ?>

                <option value="<?php echo !empty($doc_num) ? $no_reff : ''; ?>" selected="true"><?php echo !empty($doc_num) ? $no_reff : ''; ?></option> ';?> 
                <?php 
                $sqlacc = mysqli_query($conn1,"select no_bankout from b_bankout_h where status = 'Approved' and stat_bi != 'Y' and nama_supp = 'PT. NIRWANA ALABARE GARMENT'");
                while ($row_bk = mysqli_fetch_array($sqlacc)) {
                    $data = $row_bk['no_bankout'];
                    if($row_bk['no_bankout'] == $_POST['no_bk']){
                        $isSelected  = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option name="no_bk" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }
                ?>
            </select>
        </div>
        <div class="col-md-3 mb-3"> </div>

        <div class="col-md-3 mb-3">            
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

<div class="col-md-3 mb-3">            
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

<div class="col-md-4 mb-3"></div>

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
        
        <!-- <button 
        type="button"
        name="edit_data"
        id="edit_data"
        class="btn btn-success align-self-start"
        style="line-height: 1; padding: 4px 12px; font-size: 0.875rem; border-radius: 6px; height: 32px; margin-left: 10px;">
        <i class="fas fa-save"></i> Save
    </button>      -->
</div>
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

                        <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                            <thead>
                                <tr class="text-white" style="background-color: #1E3A8A;">
                                    <th class="text-center">-</th>
                                    <th class="text-center">Profit Center</th>
                                    <th class="text-center">COA</th>
                                    <th class="text-center">Reff Document</th>
                                    <th class="text-center">Reff Date</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Credit</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $doc_num = base64_decode($_GET['doc_num']); 
                                $no_bk = $_POST['no_bk'] ?? ($no_reff ?? null);

                                echo'<input type="text" style="font-size: 14px;text-align: right;display:none;" class="form-control" id="dataamount2" name="dataamount2" placeholder=""
                                value="';?> <?php             
                                $sql_bkdet = mysqli_query($conn2,"select round(sum(eqv_idr),0) as amount from b_bankout_h  where no_bankout = '$no_bk' ");
                                $row_bkdet = mysqli_fetch_array($sql_bkdet);
                                $amount = isset($row_bkdet['amount']) ? $row_bkdet['amount']: null;         

                                if(!empty($no_bk)) {

                                  echo $amount;      
                              }
                              else{
                                echo '';
                            } ?><?php echo '">';

                            $sql = mysqli_query($conn2,"select a.no_bankout, GROUP_CONCAT(c.no_coa,' ',c.nama_coa) as coa,b.no_coa,a.bankout_date,a.nama_supp,a.curr, a.amount, a.outstanding, a.status, a.deskripsi, a.eqv_idr, kode_pc, CONCAT(id_pc,' - ',nama_pc) nama_pc from b_bankout_h a inner join b_bankout_none b on b.no_bankout = a.no_bankout inner join mastercoa_v2 c on c.no_coa = b.no_coa LEFT JOIN master_pc mp on mp.kode_pc = b.profit_center where a.no_bankout = '$no_bk'
                               union
                               select a.no_bankout,GROUP_CONCAT(e.no_coa,' ',e.nama_coa) as coa,d.coa no_coa,a.bankout_date,a.nama_supp,a.curr, a.amount, a.outstanding, a.status, a.deskripsi, a.eqv_idr, '' kode_pc, '' nama_pc from b_bankout_h a inner join b_bankout_det b on b.no_bankout = a.no_bankout INNER JOIN tbl_pv_h c on c.no_pv = b.no_reff inner join tbl_pv d on d.no_pv = c.no_pv left join mastercoa_v2 e on e.no_coa = d.coa where a.no_bankout = '$no_bk'");

                            while($row_detbk = mysqli_fetch_array($sql)){
                                $no_reff = $row_detbk['no_bankout'];
                                if(!empty($no_reff)) {

                                    echo '<tr>
                                    <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>                      
                                    <td style="width:150px;">
                                    <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="kode_pc" name="kode_pc" data="'.$row_detbk['kode_pc'].'" value="'.$row_detbk['nama_pc'].'" readonly>
                                    </td>
                                    <td style="width:150px;">
                                    <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="no_coa" name="no_coa" data="'.$row_detbk['no_coa'].'" value="'.$row_detbk['coa'].'" readonly>
                                    </td>
                                    <td style="width:100px;">
                                    <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="no_bankout" name="no_bankout" value="'.$row_detbk['no_bankout'].'" readonly>
                                    </td>
                                    <td style="width:80px;">
                                    <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="bankout_date" name="bankout_date" value="'.$row_detbk['bankout_date'].'" readonly>
                                    </td>
                                    <td style="width:100px;">
                                    <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="deskripsi" name="deskripsi" value="'.$row_detbk['deskripsi'].'" readonly>
                                    </td>
                                    <td style="width:50px;">
                                    <input style="text-align: right;font-size: 12px" type="text" class="form-control" id="txt_debit_det" name="txt_debit_det" value="0" readonly>
                                    </td>
                                    <td style="width:50px;">
                                    <input style="text-align: right;font-size: 12px" type="text" class="form-control" id="txt_credit_det" name="txt_credit_det" data= "'.number_format($row_detbk['eqv_idr'],2).'" value="'.$row_detbk['eqv_idr'].'" readonly>
                                    </td>

                                    </tr>';
                                }
                            }

                            if ($row['curr'] == 'USD') {
                                echo '<tr>
                                <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>                      
                                <td style="width:150px;">
                                <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="kode_pc" name="kode_pc"  value="" readonly>
                                </td>
                                <td style="width:150px;">
                                <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="no_coa" name="no_coa" data="8.52.01" value="8.52.01 LABA / (RUGI) SELISIH KURS" readonly>
                                </td>
                                <td style="width:100px;">
                                <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="no_bankout" name="no_bankout">
                                </td>
                                <td style="width:80px;">
                                <input style="text-align: center;font-size: 12px" type="text" class="form-control tanggal" id="bankout_date" name="bankout_date">
                                </td>
                                <td style="width:100px;">
                                <input style="text-align: center;font-size: 12px" type="text" class="form-control" id="deskripsi" name="deskripsi">
                                </td>
                                <td style="width:50px;">
                                <input style="text-align: right;font-size: 12px" type="text" class="form-control" value="0" id="datadeb" name="datadeb" readonly>
                                </td>
                                <td style="width:50px;">
                                <input style="text-align: right;font-size: 12px" type="text" class="form-control" value="0" id="datacre" name="datacre"  readonly>
                                </td>

                                </tr>';
                            }
                            ?>
                        </tbody>          
                    </table>
                </div>

                <div class="form-row col mt-3">
                    <div class="form-group">
                        <input type="text" name="total_debit" id="total_debit" value="<?= $row['eqv_idr']; ?>">
                        <input type="text" name="total_credit" id="total_credit" value="<?= $row['eqv_idr']; ?>">

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
                            modal_input_amt();
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

    function UbahCostArc(val) {
                                    var profit = val; // Ambil nilai parameter
    // alert(profit); // Debug: pastikan nilai 'profit' diterima

    // Pastikan selektor dropdown sesuai dengan nama elemen
    const costCtrDropdown = $('select[name="cost"]');
    const no_coa = $('select[name=coa] option').filter(':selected').val();
    console.log(profit + ' ' + no_coa)

    // Hapus opsi yang ada terlebih dahulu, kecuali opsi default
    costCtrDropdown.find('option').not(':first').remove();

    $.ajax({
                                        url: 'getCostCenter.php', // URL endpoint server Anda
                                        type: 'POST',
                                        data: { prof_ctr: profit, no_coa: no_coa }, // Kirim data prof_ctr ke server
                                        dataType: 'json',
                                        success: function (response) {
            // Periksa apakah respons valid
            if (response && response.length > 0) {
                $.each(response, function (index, costCtr) {
                    console.log(costCtr);
                    costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
                });

                costCtrDropdown.selectpicker('refresh');
            } else {
                console.error('Tidak ada data yang diterima dari server.');
                alert('Tidak ada data cost center yang tersedia.');
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

$(document).on('change', '.prof_ctr', function () {
    const selectedProfCtr = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedCoa = row.find('select.no_coa').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});

$(document).on('change', '.no_coa', function () {
    const selectedCoa = $(this).val();
    const row = $(this).closest('tr'); 
    const selectedProfCtr = row.find('select.prof_ctr').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});



// Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
function updateCostCenter(profCtr, noCoa, row) {
                                                            const costCtrDropdown = $(row).find('.cost_ctr'); // dropdown cost center pada baris tsb

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
    <td style="width: 50px">
    <select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
    <option value="-">-</option>
    <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
    <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker cost_ctr" name="cost_ctr[]" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
</select>
</td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" placeholder="" autocomplete="off"></td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" placeholder="" autocomplete="off"></td>
<td>
<select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>
<td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
<td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off"></td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" placeholder="" autocomplete="off"></td>
<td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
</tr>
`;



row.innerHTML = element1; 
initializePlugins();  

var headerPC = $('#profit_center').val();
if (headerPC) {
    $(row).find('.prof_ctr').val(headerPC);
    $(row).find('.prof_ctr').selectpicker('refresh');
                    // updateCostCenter(headerPC, row);
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
                        var chkbox = row.cells[10].childNodes[0];
                        if (null != chkbox && true == chkbox.checked)
                        {
                            if (rowCount <= 1)
                            {
                                alert("Tidak dapat menghapus semua baris.");
                                break;
                            }
                            table.deleteRow(i);
                            modal_input_amt();
                            modal_input_cre();
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
                        var chkbox = row.cells[10].childNodes[0];
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
                            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                            <td style="width: 50px">
                            <select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
                            <option value="-">-</option>
                            <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
                            <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                        <?php endforeach; ?>
                        </select>
                        </td>
                        <td>
                        <select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
                        <option value="-"> - </option>
                        <?php
                        $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                        foreach ($sql3 as $fc) : ?>
                            <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
                        <?php endforeach; ?>
                        </select>
                        </td>
                        <td>
                        <select class="form-control selectpicker cost_ctr" name="cost_ctr[]" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
                        <option value="-"> - </option>
                        </select>
                        </td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" placeholder="" autocomplete="off"></td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" placeholder="" autocomplete="off"></td>
                        <td>
                        <select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                        </select>
                        </td>
                        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
                        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off"></td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" placeholder="" autocomplete="off"></td>
                        <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
                        </tr>
                        `;
                        var newRow = table.insertRow(i+1);
                        newRow.innerHTML = element2;
                        initializePlugins();

                        var headerPC = $('#profit_center').val();
                        if (headerPC) {
                            $(row).find('.prof_ctr').val(headerPC);
                            $(row).find('.prof_ctr').selectpicker('refresh');
                    // updateCostCenter(headerPC, row);
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
function modal_input_amt() {
  const rate   = Number((document.getElementById('rate')?.value || '').replace(/,/g, '')) || 1;
  const ttl_h  = Number((document.getElementById('amount')?.value || '').replace(/,/g, '')) || 0;
  const credit = Number(document.getElementById('dataamount2')?.value || 0);
  const txt_debit = Number(document.getElementsByName('txt_debit_det')?.value || 0);
  const txt_credit = Number(document.getElementById('txt_credit_det')?.value || 0);


  const ttl_jml = ttl_h * rate;
  const debit = 0;
  let jml_debit = 0;
  let jml_credit = 0;

  const diff = credit - ttl_jml;

  let datadeb = 0;
  let datacre = 0;

  if (rate > 1) {
  if (diff >= 0) {
    datadeb = diff;
    datacre = 0;
  } else {
    datadeb = 0;
    datacre = Math.abs(diff);
  }
}else{
    datadeb = 0;
    datacre = 0;
}

  jml_debit = ttl_jml + txt_debit + datadeb;
  jml_credit = txt_credit + datacre;

  const elDeb = document.getElementsByName("datadeb")[0];
  const elCre = document.getElementsByName("datacre")[0];

  document.getElementsByName("total_debit")[0].value = jml_debit.toFixed(2);
  document.getElementsByName("total_credit")[0].value = jml_credit.toFixed(2);

  if (elDeb) elDeb.value = datadeb.toFixed(2);
  if (elCre) elCre.value = datacre.toFixed(2);
}


</script>

<script type="text/javascript">
  function modal_input_cre() { 
    let rate = $("#rate").val().replace(/,/g, '') || 1;

    var table = document.getElementById("tbody2");
    var tota = 0;

    for (var i = 0; i < table.rows.length; i++) {
        var $row = $(table.rows[i]);
        var curr  = $row.find("select[name='currenc']").val();
        var price = parseFloat($row.find("input[name='txt_credit']").val()) || 0;
        var price2 = $row.find("input[name='txt_amount']");

        let harga = 0;
        if (price === 0) {
            price2.prop('readonly', false);
        } else {
            if (curr === 'USD') {
                harga = price * rate;
            } else {
                harga = price;
            }
            price2.prop('readonly', true);
        }

        tota += harga;
    }

    document.getElementsByName("tot_credit")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("h_tot_credit")[0].value = tota.toFixed(2);
}

</script>

<script type="text/javascript">
    $(document).on("click", "#edit_data", function () {
        // alert(1);
        let doc_num         = $("#no_bankin").val();
        let date            = $("#tgl_bankin").val();
        let ref_data        = $("#ref_data").val();
        let customer        = $("#nama_supp").val();
        let profit_center   = $("#profit_center").val();
        let akun            = $("#accountid").val();
        let curr            = $("#valuta").val();
        let bank            = $("#nama_bank").val();
        let amount          = $("#amount").val().replace(/,/g, '');
        let rate            = $("#rate").val().replace(/,/g, '');
        let eqv_idr         = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi       = $("#pesan").val();
        let h_tot_debit     = $("#h_tot_debit").val();
        let h_tot_credit    = $("#h_tot_credit").val();
        var create_user     = '<?php echo $user; ?>';

        console.log ("doc_num : " + doc_num);
        console.log ("date : " + date);
        console.log ("ref_data : " + ref_data);
        console.log ("customer : " + customer);
        console.log ("profit_center : " + profit_center);
        console.log ("akun : " + akun);
        console.log ("curr : " + curr);
        console.log ("bank : " + bank);
        console.log ("amount : " + amount);
        console.log ("rate : " + rate);
        console.log ("eqv_idr : " + eqv_idr);
        console.log ("deskripsi : " + deskripsi);
        console.log ("h_tot_debit : " + h_tot_debit);
        console.log ("h_tot_credit : " + h_tot_credit);
        var total_nak = 0;
        var total_nag = 0;


        if (h_tot_debit != h_tot_credit) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        let details = [];
        $("#tbody2 tr").each(function () {
            let coa         = $(this).find("select[name='nomor_coa']").val();
            let prof_ctr    = $(this).find("select[name='prof_ctr']").val();
            let cost_ctr    = $(this).find("select[name='cost_ctr']").val() || '-';
            let buyer       = $(this).find("input[name='buyer']").val();
            let ws          = $(this).find("input[name='ws']").val();
            let currency    = $(this).find("select[name='currenc']").val();
            let debit       = $(this).find("input[name='txt_amount']").val();
            let credit      = $(this).find("input[name='txt_credit']").val();
            let keterangan  = $(this).find("input[name='keterangan']").val();

            if (prof_ctr === "NAK") {
                total_nak += (credit - debit);
            } else if (prof_ctr === "NAG") {
                total_nag += (credit - debit);
            }

            console.log ("det_coa : " + coa);
            console.log ("det_prof_ctr : " + prof_ctr);
            console.log ("det_cost_ctr : " + cost_ctr);
            console.log ("det_buyer : " + buyer);
            console.log ("det_ws : " + ws);
            console.log ("det_currency : " + currency);
            console.log ("det_debit : " + debit);
            console.log ("det_credit : " + credit);
            console.log ("det_keterangan : " + keterangan);

        // hanya push kalau COA dan nominal terisi
        if (coa && (debit > 0 || credit > 0)) {
            details.push({
                coa: coa,
                prof_ctr: prof_ctr,
                cost_ctr: cost_ctr,
                buyer: buyer,
                ws: ws,
                currency: currency,
                debit: debit,
                credit: credit,
                keterangan: keterangan
            });
        }
    });
        console.log ("det_total_nak : " + total_nak);
        console.log ("det_total_nag : " + total_nag);

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
                    url: "update_bi_none.php",
                    data: {
                        doc_num: doc_num,
                        date: date,
                        ref_data: ref_data,
                        customer: customer,
                        profit_center: profit_center,
                        akun: akun,
                        curr: curr,
                        bank: bank,
                        amount: amount,
                        rate: rate,
                        eqv_idr: eqv_idr,
                        deskripsi: deskripsi,
                        h_tot_debit: h_tot_debit,
                        h_tot_credit: h_tot_credit,
                        create_user: create_user,
                        total_nak: total_nak,
                        total_nag: total_nag,
                        details: JSON.stringify(details)
                    },
                    success: function (res) {
                        if (res.trim() === "OK") {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Data has been successfully updated!"
                            }).then(() => {
                                // window.location.href = "bank-in.php";
                                window.location.reload();
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
        $('#mytablenone').DataTable({
            paging: false,          // Menambahkan paging
            searching: false,       // Menambahkan pencarian
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
