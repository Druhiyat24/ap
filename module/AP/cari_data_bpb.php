<?php
// Isi tabel modal "Accept Transfer" (BPB / Surat Jalan) untuk satu no_transfer.
//
// Memakai $conn2 - SAMA dengan koneksi yang dipakai approve_whstoacc.php /
// cancel_whstoacc.php saat menyimpan. Sebelumnya membaca lewat $conn1,
// padahal blok koneksi di conn/conn.php sering ditukar antara localhost dan
// produksi; kalau keduanya sempat berbeda, daftar yang tampil berasal dari
// database yang BUKAN database yang diperbarui.
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$notrf = isset($_POST['notrf']) ? trim((string) $_POST['notrf']) : '';

$sql = mysqli_query($conn2, "select no_transfer, tgl_transfer, no_bpb, tgl_bpb, nama_supp, curr, total, created_at, created_by
    from ir_trans_bpb where no_transfer = '" . mysqli_real_escape_string($conn2, $notrf) . "' GROUP BY id");

$table = '';

if ($sql) {
    while ($row = mysqli_fetch_assoc($sql)) {
        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
        // Default tercentang (= akan di-Accept). Baris yang TIDAK dicentang
        // akan di-CANCEL saat tombol Accept ditekan - lihat handler di
        // form_approve_bpb.php. Dulu atribut checked ditulis lewat potongan tag
        // PHP di dalam string PHP (jadi tidak pernah dieksekusi) dan ikut
        // terkirim mentah ke browser sebagai atribut sampah; hasil akhirnya
        // kebetulan tetap tercentang. Sekarang ditulis eksplisit supaya
        // markup-nya benar dan maksudnya jelas.
        $table .= '<tr>
                           <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="' . $esc($row['no_bpb']) . '" checked></td>
                           <td style="display:none;" value="' . $esc($row['no_transfer']) . '">' . $esc($row['no_transfer']) . '</td>
                            <td style="display:none;" value="' . $esc($row['tgl_transfer']) . '">' . date("d-M-Y", strtotime($row['tgl_transfer'])) . '</td>
                            <td style="width:50px;" value="' . $esc($row['no_bpb']) . '">' . $esc($row['no_bpb']) . '</td>
                            <td style="width:100px;" value="' . $esc($row['tgl_bpb']) . '">' . date("d-M-Y", strtotime($row['tgl_bpb'])) . '</td>
                            <td style="" value="' . $esc($row['nama_supp']) . '">' . $esc($row['nama_supp']) . '</td>
                            <td class="dt_total" style="width:100px;" value="' . $esc($row['created_by']) . '">' . $esc($row['created_by']) . '</td>
                            <td style="" value = "' . $esc($row['created_at']) . '">' . $esc($row['created_at']) . '</td>
                        </tr>';
    }
}

echo $table;

mysqli_close($conn2);
