<?Php 
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_bankout       = $_POST['txt_no_bankout'];
$txt_nama_supp       = $_POST['txt_nama_supp'];
$txt_nama_bank       = $_POST['txt_nama_bank'];
$txt_nama_akun       = $_POST['txt_nama_akun'];
$txt_user       = $_POST['txt_user'];
$txt_status       = $_POST['txt_status'];
$txt_start_date       = date("Y-m-d",strtotime($_POST['txt_start_date']));
$txt_end_date       = date("Y-m-d",strtotime($_POST['txt_end_date']));
$create_date    = date("Y-m-d H:i:s");

 $name_upload = $txt_bankout.".pdf";
 $nameupload = str_replace('/', '', $name_upload);
    
if (isset($_FILES['txtfile'])) {
        $nama_file = $_FILES['txtfile']['name'];
        $tmp_file = $_FILES['txtfile']['tmp_name'];
        $filename = str_replace(' ', '', $nama_file);
        $path = "file_pdf/bank_out/" . $filename;
        // $path = "//10.10.5.2/xampp\htdocs\ap\module\AP\File;
        move_uploaded_file($tmp_file, $path);
    } else {
        $nama_file = "";
    }

$query = "INSERT INTO b_bankout_dok (no_bankout, file_name, file_name_as, created_by, created_date) 
VALUES 
    ('$txt_bankout', '$filename','$nameupload', '$txt_user', '$create_date')";
$execute = mysqli_query($conn2,$query);
    If (move_uploaded_file($tmp_file, $path)) {
        alert("Upload Berhasil!");
        Header("Location: bank-out.Php?nama_supp=$txt_nama_supp&status=$txt_status&start_date=$txt_start_date&end_date=$txt_end_date&bank=$txt_nama_bank&akun=$txt_nama_akun");
        Exit();
    } Else {
        // echo "Not uploaded because of error #".$_FILES["txtfile"]["error"];
        // Echo $_FILES['txtfile']['tmp_name'];
        // echo "<pre>";
        // print_r($_FILES);
        // echo "</pre>";
        Header("Location: bank-out.Php?nama_supp=$txt_nama_supp&status=$txt_status&start_date=$txt_start_date&end_date=$txt_end_date&bank=$txt_nama_bank&akun=$txt_nama_akun");
        Exit();
    }

 ?>