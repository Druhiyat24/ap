<?php
// ============================================================================
// Upload dokumen (PDF) untuk satu Bank Out.
//
// RIWAYAT MASALAH
// ---------------
// Versi lama menelan kegagalan upload tanpa suara:
//   1. move_uploaded_file() dipanggil DUA KALI dengan tmp_name yang sama -
//      panggilan kedua pasti gagal (berkas sementara sudah pindah), sehingga
//      alur SELALU masuk cabang "else".
//   2. INSERT ke b_bankout_dok dijalankan TANPA memeriksa hasil pemindahan,
//      dan $_FILES[...]['error'] tidak pernah dicek. Upload yang gagal tetap
//      meninggalkan baris di database tanpa berkas: di halaman Bank Out tombol
//      mata terlihat normal, lalu <embed>-nya menampilkan "Not Found".
//   3. Nama berkas bank sering mengandung SPASI (mis. "..._PT BINTANG_IDR_...").
//      Nama yang masuk database dibuang spasinya, sementara berkas di disk bisa
//      tersimpan dengan nama aslinya - jadi yang dicari tidak sama dengan yang
//      ada. Bukti di server: ada PASANGAN berkas untuk dokumen yang sama, satu
//      berspasi satu tidak, dengan ukuran identik.
//   4. Cabang sukses memanggil alert() - fungsi JavaScript, bukan PHP.
//
// ATURAN SEKARANG
// ---------------
//   * Nama yang ditulis ke disk dan yang dicatat ke database SELALU SAMA
//     (satu variabel $filename dipakai keduanya), jadi tidak bisa menyimpang.
//   * Nama dibersihkan agar aman di Windows & di URL, dan dibuat UNIK supaya
//     dokumen lain tidak tertimpa diam-diam.
//   * Semua penyebab gagal (ukuran, nama, jenis berkas, izin folder, database)
//     dilaporkan ke user dengan pesan yang jelas supaya bisa diunggah ulang.
//   * Baris database HANYA ditulis setelah berkasnya benar-benar ada di disk.
// ============================================================================
session_start();
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$DIR = __DIR__ . '/file_pdf/bank_out';

// --- ukuran ini_get ("8M", "512K", "1G") -> byte -----------------------------
function keByte($v)
{
    $v = trim((string) $v);
    if ($v === '') { return 0; }
    $satuan = strtolower(substr($v, -1));
    $angka  = (float) $v;
    switch ($satuan) {
        case 'g': return (int) ($angka * 1024 * 1024 * 1024);
        case 'm': return (int) ($angka * 1024 * 1024);
        case 'k': return (int) ($angka * 1024);
    }
    return (int) $angka;
}
function ukuranTampil($byte)
{
    if ($byte >= 1024 * 1024) { return round($byte / 1024 / 1024, 1) . ' MB'; }
    if ($byte >= 1024)        { return round($byte / 1024) . ' KB'; }
    return $byte . ' byte';
}

// --- kembali ke halaman Bank Out dengan pesan hasil --------------------------
// Pesan dititipkan lewat SESSION, bukan query string: isinya bisa panjang dan
// tidak perlu ikut terlihat di address bar.
function kembali($pesan, $ok = false)
{
    $_SESSION['bankout_upload'] = ['ok' => (bool) $ok, 'msg' => $pesan];
    $qs = http_build_query([
        'nama_supp'  => $_POST['txt_nama_supp']  ?? 'ALL',
        'status'     => $_POST['txt_status']     ?? 'ALL',
        'start_date' => isset($_POST['txt_start_date']) ? date('Y-m-d', strtotime($_POST['txt_start_date'])) : date('Y-m-d'),
        'end_date'   => isset($_POST['txt_end_date'])   ? date('Y-m-d', strtotime($_POST['txt_end_date']))   : date('Y-m-d'),
        'bank'       => $_POST['txt_nama_bank']  ?? 'ALL',
        'akun'       => $_POST['txt_nama_akun']  ?? 'ALL',
    ]);
    header('Location: bank-out.php?' . $qs);
    exit;
}

// ---- 1. POST melebihi post_max_size ----------------------------------------
// Kalau ini terjadi PHP MEMBUANG seluruh isi POST: $_POST dan $_FILES jadi
// KOSONG. Tanpa penanganan khusus, gejalanya terlihat seperti "form tidak
// terkirim" padahal sebenarnya berkasnya kebesaran.
$postMax = keByte(ini_get('post_max_size'));
$lenKirim = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
if ($postMax > 0 && $lenKirim > $postMax && empty($_POST)) {
    kembali('Berkas terlalu besar (' . ukuranTampil($lenKirim) . '). Batas kiriman server saat ini '
        . ukuranTampil($postMax) . '. Kecilkan/kompres PDF-nya lalu unggah ulang, '
        . 'atau minta IT menaikkan post_max_size.');
}

$txt_bankout = isset($_POST['txt_no_bankout']) ? trim((string) $_POST['txt_no_bankout']) : '';
$txt_user    = isset($_POST['txt_user']) ? trim((string) $_POST['txt_user']) : '';
$create_date = date('Y-m-d H:i:s');
$nameupload  = str_replace('/', '', $txt_bankout . '.pdf');

if ($txt_bankout === '') {
    kembali('Nomor Bank Out tidak terbaca, dokumen TIDAK diunggah. Silakan tutup dialog dan coba lagi.');
}

// ---- 2. Status upload dari PHP ---------------------------------------------
if (!isset($_FILES['txtfile'])) {
    kembali('Tidak ada berkas yang dikirim. Pilih dokumen PDF-nya lalu unggah ulang.');
}

$err = $_FILES['txtfile']['error'];
if ($err !== UPLOAD_ERR_OK) {
    $maxUp = keByte(ini_get('upload_max_filesize'));
    $pesanErr = [
        UPLOAD_ERR_INI_SIZE   => 'Ukuran berkas melebihi batas server (' . ukuranTampil($maxUp) . '). '
                               . 'Kecilkan/kompres PDF-nya lalu unggah ulang, atau minta IT menaikkan upload_max_filesize.',
        UPLOAD_ERR_FORM_SIZE  => 'Ukuran berkas melebihi batas yang diizinkan formulir. Kecilkan berkasnya lalu unggah ulang.',
        UPLOAD_ERR_PARTIAL    => 'Berkas hanya terkirim sebagian (koneksi terputus). Dokumen TIDAK tersimpan, silakan unggah ulang.',
        UPLOAD_ERR_NO_FILE    => 'Belum ada berkas yang dipilih. Pilih dokumen PDF-nya lalu unggah ulang.',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara server tidak tersedia. Dokumen TIDAK tersimpan - hubungi IT.',
        UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis berkas ke disk. Dokumen TIDAK tersimpan - hubungi IT.',
        UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh konfigurasi server. Dokumen TIDAK tersimpan - hubungi IT.',
    ];
    kembali($pesanErr[$err] ?? "Upload gagal (kode $err). Dokumen TIDAK tersimpan, silakan unggah ulang.");
}

$tmp_file = $_FILES['txtfile']['tmp_name'];
if (!is_uploaded_file($tmp_file)) {
    kembali('Berkas tidak valid, dokumen TIDAK diunggah. Silakan unggah ulang.');
}
if ((int) $_FILES['txtfile']['size'] <= 0) {
    kembali('Berkas yang dipilih kosong (0 byte). Periksa dokumennya lalu unggah ulang.');
}

// ---- 3. Rapikan nama berkas -------------------------------------------------
// Nama dari bank bisa mengandung spasi, tanda baca, bahkan karakter yang tidak
// boleh dipakai di Windows. Dibersihkan supaya aman di disk MAUPUN di URL, dan
// hasil bersih inilah yang dicatat ke database - jadi keduanya selalu cocok.
function rapikanNama($namaAsli)
{
    $base = basename(str_replace('\\', '/', (string) $namaAsli)); // buang komponen folder
    $ext  = strtolower(pathinfo($base, PATHINFO_EXTENSION));
    $nama = pathinfo($base, PATHINFO_FILENAME);

    $nama = preg_replace('/[\x00-\x1F\x7F]/', '', $nama);              // karakter kontrol
    $nama = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '-', $nama); // ilegal di Windows
    $nama = preg_replace('/\s+/', '_', $nama);                          // spasi -> underscore
    $nama = preg_replace('/_{2,}/', '_', $nama);
    $nama = trim($nama, " ._-");                                        // Windows tak suka akhiran titik/spasi
    if ($nama === '') { $nama = 'dokumen'; }
    if (strlen($nama) > 120) { $nama = substr($nama, 0, 120); }         // jaga batas panjang path
    if ($ext === '') { $ext = 'pdf'; }

    return $nama . '.' . $ext;
}

$filename = rapikanNama($_FILES['txtfile']['name']);

if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'pdf') {
    kembali('Dokumen harus berupa berkas PDF. Berkas yang dipilih: ' . htmlspecialchars($_FILES['txtfile']['name']) . '.');
}
// Isi berkas benar-benar PDF? (dicek longgar: tanda "%PDF" di awal berkas)
$cuplikan = (string) @file_get_contents($tmp_file, false, null, 0, 1024);
if (strpos($cuplikan, '%PDF') === false) {
    kembali('Isi berkas bukan PDF yang sah (mungkin salah pilih berkas atau file rusak). Silakan periksa lalu unggah ulang.');
}

// ---- 4. Folder tujuan -------------------------------------------------------
if (!is_dir($DIR) && !@mkdir($DIR, 0775, true)) {
    kembali('Folder penyimpanan dokumen tidak ada dan gagal dibuat di server. Dokumen TIDAK tersimpan - hubungi IT.');
}
if (!is_writable($DIR)) {
    kembali('Folder penyimpanan dokumen tidak bisa ditulis di server. Dokumen TIDAK tersimpan - hubungi IT.');
}

// Jangan menimpa berkas lain yang namanya kebetulan sama - beri akhiran angka.
$dasar = pathinfo($filename, PATHINFO_FILENAME);
$ext   = pathinfo($filename, PATHINFO_EXTENSION);
$urut  = 1;
while (file_exists($DIR . '/' . $filename)) {
    $urut++;
    $filename = $dasar . '_' . $urut . '.' . $ext;
    if ($urut > 999) { kembali('Gagal membuat nama berkas yang unik di server. Hubungi IT.'); }
}
$path = $DIR . '/' . $filename;

// ---- 5. Pindahkan SEKALI, lalu pastikan benar-benar ada ---------------------
if (!move_uploaded_file($tmp_file, $path)) {
    kembali('Server gagal menyimpan berkas. Dokumen TIDAK dicatat, silakan unggah ulang.');
}
if (!is_file($path) || filesize($path) <= 0) {
    @unlink($path);
    kembali('Berkas gagal tersimpan utuh di server. Dokumen TIDAK dicatat, silakan unggah ulang.');
}

// ---- 6. Baru catat ke database ----------------------------------------------
$query = "INSERT INTO b_bankout_dok (no_bankout, file_name, file_name_as, created_by, created_date)
VALUES ('" . mysqli_real_escape_string($conn2, $txt_bankout) . "',
        '" . mysqli_real_escape_string($conn2, $filename) . "',
        '" . mysqli_real_escape_string($conn2, $nameupload) . "',
        '" . mysqli_real_escape_string($conn2, $txt_user) . "',
        '" . mysqli_real_escape_string($conn2, $create_date) . "')";

if (!mysqli_query($conn2, $query)) {
    // Berkas terlanjur tersimpan tetapi barisnya gagal - dibuang lagi supaya
    // tidak ada berkas yatim di folder.
    $pesanDb = mysqli_error($conn2);
    @unlink($path);
    kembali('Gagal mencatat dokumen ke database, jadi berkasnya ikut dibatalkan. Silakan unggah ulang. (' . $pesanDb . ')');
}

kembali('Dokumen berhasil diunggah: ' . $filename . ' (' . ukuranTampil(filesize($path)) . ').', true);
