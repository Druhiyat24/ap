<?php
include '../../conn/conn.php'; // Koneksi ke database

if (isset($_POST['kpno'])) {
    $kpno = mysqli_real_escape_string($conn2, $_POST['kpno']);
    $sql = "select DISTINCT pono, b.id_jo, id_item, id_item_out, kpno ws FROM po_header a INNER JOIN po_item b on b.id_po = a.id INNER JOIN masteritem mi on mi.id_gen = b.id_gen left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo where b.id_jo = '$kpno' group by id_item";
    $result = mysqli_query($conn2, $sql);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode($data); // Mengembalikan data sebagai JSON
}
?>
