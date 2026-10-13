<html>
<head>
    <title>Export Data to Excel </title>
</head>
<body>
    <style type="text/css">
    body{
        font-family: sans-serif;
    }
    table{
        margin: 20px auto;
        border-collapse: collapse;
    }
    table th,
    table td{
        border: 1px solid #3c3c3c;
        padding: 3px 8px;
    }
    </style>

    <?php
/* ============================================================================
   Update BPB - WIP.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   WIP = barang dalam proses (makloon). Dokumennya dibaca dari tabel `bpb`
   dgn bpbno_int LIKE 'WIP/%' ATAU LIKE 'SPCK/%' - dua awalan nomor, satu
   menu, karena keduanya pekerjaan yang sama. Diperiksa ke data 13 Okt
   2026: 35.916 dokumen WIP dan 11 dokumen SPCK.

   Jurnalnya MAKLOON, bukan persediaan: GR/IR 2.10.06 PIHAK KETIGA -
   MAKLOON lawan beban 5.69.0x (PRINTING / EMBRODEIRY / WASHING / CMT /
   PAKAIAN JADI). Khusus awalan WIP, jurnal aslinya memakai cost center
   DEP04SUB004 - itu sudah ikut terbawa di proses_approve_update_bpb_wip.php,
   jangan diubah.

   AWAS - kunci sambungan ke PO sama dgn WIP, BEDA dari Fabric/
   Accessories: dipakai  pi.id_gen = bpb.id_item , bukan lewat
   masteritem.id_gen . Diukur 13 Okt 2026 atas 26.250 baris WIP/SPCK
   yang ber-PO dan berharga:
     dgn pi.id_gen = masteritem.id_gen :      0 baris dapat harga PO
     dgn pi.id_gen = a.id_item         : 26.193 baris dapat harga PO
   Dgn kunci yang salah, kolom is_match selalu 0 - tombol "isi dari PO"
   dan ceklis "sembunyikan yang sudah cocok" mati tanpa pesan apa pun.
   ============================================================================ */
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Update BPB Fabric.xls");
    include '../../conn/conn.php';
$jenis = 'wip';
$jenis_esc = mysqli_real_escape_string($conn1, $jenis);

    $start_date_disp = date("d F Y", strtotime($_GET['start_date']));
    $end_date_disp   = date("d F Y", strtotime($_GET['end_date']));
    $start_date = date("Y-m-d", strtotime($_GET['start_date']));
    $end_date   = date("Y-m-d", strtotime($_GET['end_date']));
    ?>

        <h4>UPDATE BPB FABRIC - EDIT REQUEST<br/>PERIOD <?php echo $start_date_disp; ?> - <?php echo $end_date_disp; ?></h4>
    <!-- <center>
    </center> -->

    <table style="width:100%;font-size:10px;" border="1">
        <tr>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">No</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">No. Trans</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Trans Date</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">No BPB</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">BPB Date</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">No WS</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">ID Item</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Item</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Qty</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Unit</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Curr</th>
            <th colspan="2" style="text-align:center;vertical-align:middle;">Price</th>
            <th colspan="2" style="text-align:center;vertical-align:middle;">PPN %</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Status</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Description</th>
            <th rowspan="2" style="text-align:center;vertical-align:middle;">Created By</th>
        </tr>
        <tr>
            <th style="text-align:center;vertical-align:middle;">Current</th>
            <th style="text-align:center;vertical-align:middle;">Updated</th>
            <th style="text-align:center;vertical-align:middle;">Current</th>
            <th style="text-align:center;vertical-align:middle;">Updated</th>
        </tr>
        <?php
        $sql = mysqli_query($conn1, "SELECT h.no_pengajuan, h.tgl_pengajuan, h.status, h.deskripsi, h.created_by, h.created_at,
                d.no_bpb, d.tgl_bpb, d.no_ws, d.id_item, d.desc_item, d.qty, d.unit, d.curr, d.price_old, d.price_new, d.ppn_old, d.ppn_new
            FROM Req_update_bpb_h h
            LEFT JOIN Req_update_bpb d ON d.no_pengajuan = h.no_pengajuan
            WHERE h.tgl_pengajuan BETWEEN '$start_date' AND '$end_date'
            ORDER BY h.id DESC, d.no_bpb, d.no_ws, d.id_item");

        $no = 0;
        while ($row = mysqli_fetch_assoc($sql)) {
            $no++;

            $tgl_pengajuan = !empty($row['tgl_pengajuan']) ? date("d-M-Y", strtotime($row['tgl_pengajuan'])) : '-';
            $tgl_bpb       = !empty($row['tgl_bpb']) ? date("d-M-Y", strtotime($row['tgl_bpb'])) : '-';
            $desc_item     = !empty($row['desc_item']) ? $row['desc_item'] : $row['id_item'];

            $created_by = $row['created_by'];
            if (!empty($created_by) && !empty($row['created_at'])) {
                $created_by .= ' (' . date('d-M-Y H:i:s', strtotime($row['created_at'])) . ')';
            }

            echo '<tr style="font-size:12px;">
                <td style="text-align:center;">' . $no . '</td>
                <td style="text-align:left;">' . htmlspecialchars($row['no_pengajuan']) . '</td>
                <td style="text-align:center;">' . $tgl_pengajuan . '</td>
                <td style="text-align:left;">' . htmlspecialchars($row['no_bpb'] ?: '-') . '</td>
                <td style="text-align:center;">' . $tgl_bpb . '</td>
                <td style="text-align:left;">' . htmlspecialchars($row['no_ws'] ?: '-') . '</td>
                <td style="text-align:left;">' . htmlspecialchars($row['id_item'] ?: '-') . '</td>
                <td style="text-align:left;">' . htmlspecialchars($desc_item ?: '-') . '</td>
                <td style="text-align:right;">' . (isset($row['qty']) ? number_format($row['qty'], 2) : '-') . '</td>
                <td style="text-align:center;">' . htmlspecialchars($row['unit'] ?: '-') . '</td>
                <td style="text-align:center;">' . htmlspecialchars($row['curr'] ?: '-') . '</td>
                <td style="text-align:right;">' . (isset($row['price_old']) ? number_format($row['price_old'], 4) : '-') . '</td>
                <td style="text-align:right;">' . (isset($row['price_new']) ? number_format($row['price_new'], 4) : '-') . '</td>
                <td style="text-align:right;">' . (isset($row['ppn_old']) ? number_format($row['ppn_old'], 2) : '-') . '</td>
                <td style="text-align:right;">' . (isset($row['ppn_new']) ? number_format($row['ppn_new'], 2) : '-') . '</td>
                <td style="text-align:center;">' . htmlspecialchars($row['status']) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($row['deskripsi'] ?: '-') . '</td>
                <td style="text-align:left;">' . htmlspecialchars($created_by ?: '-') . '</td>
            </tr>';
        }

        if ($no === 0) {
            echo '<tr><td colspan="17" style="text-align:center;">No data found</td></tr>';
        }
        ?>
    </table>

</body>
</html>
