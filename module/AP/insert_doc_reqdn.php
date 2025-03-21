<?Php 
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_no_req       = $_POST['txt_no_req'];
$txt_nama_supp       = $_POST['txt_nama_supp'];
$txt_user       = $_POST['txt_user'];
$txt_status       = $_POST['txt_status'];
$txt_start_date       = date("Y-m-d",strtotime($_POST['txt_start_date']));
$txt_end_date       = date("Y-m-d",strtotime($_POST['txt_end_date']));
$create_date    = date("Y-m-d H:i:s");

 $name_upload = $txt_no_req.".pdf";
 $nameupload = str_replace('/', '', $name_upload);
    
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

$query = "INSERT INTO req_dn_dok (no_req, file_name, file_name_as, created_by, created_date) 
VALUES 
    ('$txt_no_req', '$filename','$nameupload', '$txt_user', '$create_date')";
$execute = mysqli_query($conn2,$query);
    If (move_uploaded_file($tmp_file, $path)) {
        alert("Upload Berhasil!");
        Header("Location: request_debitnote.Php?nama_supp=$txt_nama_supp&status=$txt_status&start_date=$txt_start_date&end_date=$txt_end_date");
        Exit();
    } Else {
        // echo "Not uploaded because of error #".$_FILES["txtfile"]["error"];
        // Echo $_FILES['txtfile']['tmp_name'];
        // echo "<pre>";
        // print_r($_FILES);
        // echo "</pre>";
        Header("Location: request_debitnote.Php?nama_supp=$txt_nama_supp&status=$txt_status&start_date=$txt_start_date&end_date=$txt_end_date");
        Exit();
    }

 ?>