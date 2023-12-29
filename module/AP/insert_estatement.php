<?Php 
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$blnfrom       = $_POST['txt-blnfrom'];
$thnfrom       = $_POST['txt-thn_from'];
$blnto       = $_POST['txt-bln_to'];
$thnto       = $_POST['txt-thn_to'];
$namabank       = $_POST['txt-nama_bank'];
$status       = $_POST['txt-status'];
$txt_bank       = $_POST['txt_bank'];
$txt_kodetgl    = $_POST['txt_kodetgl'];
$txt_user       = $_POST['txt_user'];
$txt_akun       = $_POST['txt_akun'];
$bulan       = $_POST['txt_bulan'];
$tahun       = $_POST['txt_tahun'];
$create_date    = date("Y-m-d H:i:s");

$sqln = mysqli_query($conn2,"select concat(sob,'-',RIGHT(bank_account ,4)) kode from b_masterbank where bank_account = '$txt_akun'");
 $rown = mysqli_fetch_array($sqln);
 $kode = $rown['kode'];
 $bln = $bulan;
 $thn = substr($tahun,2,2);
 $name_upload = $kode ."-". $bln."".$thn.".pdf";
 $nameupload = str_replace(' ', '', $name_upload);
    
if (isset($_FILES['txtfile'])) {
        $nama_file = $_FILES['txtfile']['name'];
        $tmp_file = $_FILES['txtfile']['tmp_name'];
        $filename = str_replace(' ', '', $nama_file);
        $path = "file_pdf/" . $filename;
        // $path = "//10.10.5.2/xampp\htdocs\ap\module\AP\File;
        move_uploaded_file($tmp_file, $path);
    } else {
        $nama_file = "";
    }

$query = "INSERT INTO b_estatement (kode_tanggal,bank_name, account_bank, file_name, file_name_as, created_user, created_date) 
VALUES 
    ('$txt_kodetgl' ,'$txt_bank', '$txt_akun', '$filename', '$name_upload', '$txt_user', '$create_date')";
$execute = mysqli_query($conn2,$query);
    If (move_uploaded_file($tmp_file, $path)) {
        alert("Upload Berhasil!");
        Header("Location: e_statement.Php?blnfrom=$blnfrom&thnfrom=$thnfrom&blnto=$blnto&thnto=$thnto&namabank=$namabank&status=$status");
        Exit();
    } Else {
        // echo "Not uploaded because of error #".$_FILES["txtfile"]["error"];
        // Echo $_FILES['txtfile']['tmp_name'];
        // echo "<pre>";
        // print_r($_FILES);
        // echo "</pre>";
        Header("Location: e_statement.Php?blnfrom=$blnfrom&thnfrom=$thnfrom&blnto=$blnto&thnto=$thnto&namabank=$namabank&status=$status");
        Exit();
    }

 ?>