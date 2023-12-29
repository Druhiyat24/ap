<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php

    $sess_prof = $_SESSION;

    if(isset($_POST['submitpost'])) {
        $pilih_kategori =   $_POST['pilih_kategori'];
        $judulpost =   $_POST['judulpost'];
        $isipost =   $_POST['isipost'];
        $date = date("Y-m-d H:i:s");
        $author = $_POST['id_author'];
        $statuspost = $_POST['statuspost'];
        $file_name  =   $_FILES['gambarpost']['name'];
        $tmp_name   =   $_FILES['gambarpost']['tmp_name'];
        move_uploaded_file($tmp_name, "../images/blog/".$file_name);
        mysqli_query($koneksi, "INSERT INTO post VALUES('','$pilih_kategori','$judulpost','$isipost','$file_name','$date','$author','$statuspost')");
        header("location:index.php?daftar-blog&berhasiltambahblog");
    }

    $kategori = mysqli_query($koneksi, "SELECT * FROM kategoripost ORDER BY nama_kategoripost ASC");

?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title" id="berhasiltambahakun">Tambah Post</h4>
                            </div>
                            <div class="content">
                                <form method="post" enctype="multipart/form-data" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="judulpost">Judul Post</label>
                                                <input type="text" name="judulpost" class="form-control border-input" placeholder="Masukkan Judul Post">
                                            </div>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-md-12">
                                          <div class="form-group">
                                            <textarea class="form-control border-input" name="isipost" required placeholder="Tulis Artikel Menarik..." rows="7"></textarea>
                                        </div>
                                        </div>
                                      </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="username_author">Author Username</label>
                                                <input type="text" name="username_author" class="form-control border-input" readonly value="<?php echo $sess_prof['akun_username'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="pilih_kategori">Kategori</label>
                                            <select name="pilih_kategori" class="form-control border-input" required>
                                                <option value="">Pilih Kategori</option>
                                                    <?php if(mysqli_num_rows($kategori)) {?>
                                                        <?php while($row_kategori = mysqli_fetch_array($kategori)) {?>
                                                            <option value="<?php echo $row_kategori['id'] ?>"><?php echo $row_kategori['nama_kategoripost'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="gambarpost">Gambar Post</label>
                                              <input type="file" name="gambarpost" class="form-control border-input" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                              <button type="submit" name="submitpost" class="btn btn-info">Kirim Post</button>
                                              <button type="reset" class="btn btn-danger">Batal</button>
                                            </div>
                                        </div>
                                    <div>
                                    <div class="col-xs-12">
                                        <input type="hidden" name="id_author" value="<?php echo $sess_prof['akun_id'] ?>">
                                    </div>
                                </form>
                            </div>
                            <div class="col-xs-12 text-center">
                                    <a href="#">&larr; Kembali Ke Daftar Artikel Saya</a>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</body>
</html>