<?php
include '../../conn/conn.php'; //Include file koneksi
$searchTerm = 'singa'; // Menerima kiriman data dari inputan pengguna

$sql=" SELECT distinct(Supplier) as buyer from mastersupplier where tipe_sup = 'C' AND Supplier LIKE 'singa' order by Supplier ASC "; // query sql untuk menampilkan data mahasiswa dengan operator LIKE

$hasil=mysqli_query($conn1,$sql); //Query dieksekusi

//Disajikan dengan menggunakan perulangan
while ($row = mysqli_fetch_array($hasil)) {
    $data[] = $row['buyer'];
}
//Nilainya disimpan dalam bentuk json
echo json_encode($data);
?>