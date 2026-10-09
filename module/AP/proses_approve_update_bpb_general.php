<?php
/* ============================================================================
   Update BPB - GENERAL : proses APPROVE / CANCEL pengajuan koreksi harga.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen General ada LANGSUNG di tabel `bpb` (tidak punya tabel kepala gudang
   seperti Fabric), dan GEN/IN maupun GEN/RI sama-sama PENERIMAAN secara
   jurnal - diperiksa ke data 8 Okt 2026: persediaan didebit, GR/IR dikredit,
   type 'AP - BPB'. Jadi TIDAK ADA cabang arah terbalik seperti GK/RO di Fabric.

   PENJURNALAN - kenapa beda dari versi Fabric/Accessories
   --------------------------------------------------------
   Versi Fabric membangun jurnal di PHP: mengambil SATU baris ringkasan
   dokumen, mencari COA di `mastercoa_v2`, lalu menulis 2-3 baris jurnal.
   Cara itu TIDAK cukup untuk General, karena:

     1. Satu dokumen GEN bisa memuat beberapa KATEGORI barang sekaligus
        (Spareparts + ATK + Umum + Mesin). Diperiksa 8 Okt 2026: 340 dari
        16.690 dokumen GEN/IN berkategori campur, dan jurnal aslinya memang
        bercabang sampai 4 COA debet. Versi PHP hanya memakai grup PERTAMA,
        jadi jurnalnya diciutkan jadi satu COA - tetap balance, tapi sebarannya
        salah. Fabric & Accessories tidak pernah kena: 0% campur.
     2. `mastercoa_v2` dicocokkan dgn LIKE dan hasilnya tidak selalu sama dgn
        jurnal BPB aslinya (uji 600 dokumen 2026: 10 beda).

   Karena itu jurnal di sini dibangun dgn SQL yang DISALIN APA ADANYA dari
   proses_repost_bpb.php - sumber jurnal BPB yang asli. Ia memakai tabel
   pemetaan `ap_mapping_coa_jurnal`, mengelompokkan per
   (bpbno, mattype, n_code_category) sehingga dokumen campur otomatis dapat
   beberapa baris COA, dan sudah memuat kategori khas General
   (n_code_category 1=ATK, 2=Umum, 3=Spareparts, 4=Mesin).

   Urutan kerja per dokumen: tulis harga baru ke `bpb` -> balik jurnal lama ->
   bangun ulang jurnalnya. Satu pengajuan = satu transaksi.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

$action           = isset($_POST['action']) ? $_POST['action'] : '';
$list             = isset($_POST['no_pengajuan']) ? $_POST['no_pengajuan'] : array();
$approve_user     = isset($_POST['approve_user']) ? $_POST['approve_user'] : '';
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

if (!is_array($list) || empty($list)) {
    echo json_encode(array('success' => false, 'message' => 'Select at least 1 request'));
    exit;
}
if (!in_array($action, array('approve', 'cancel'))) {
    echo json_encode(array('success' => false, 'message' => 'Unknown action'));
    exit;
}

$newStatus       = ($action === 'approve') ? 'Approved' : 'Cancel';
$approved        = 0;
$skipped         = 0;
$journalEntries  = 0;
$journalWarnings = array();

foreach ($list as $no_pengajuan) {
    $no_pengajuan_esc = mysqli_real_escape_string($conn2, $no_pengajuan);
    $gagalFatal = false;   // true = seluruh pengajuan ini dibatalkan

    $check = mysqli_query($conn2, "SELECT status FROM Req_update_bpb_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
    $row   = mysqli_fetch_assoc($check);
    if (!$row || in_array($row['status'], array('Approved', 'Cancel'))) {
        $skipped++;
        continue;
    }

    /* Satu pengajuan = satu transaksi. Harga, jurnal balik, jurnal baru, dan
       status pengajuan harus jadi bersama-sama atau batal bersama-sama. */
    mysqli_begin_transaction($conn2);

    if ($action === 'approve') {
        /* Cukup daftar BPB-nya. Nilai jurnal TIDAK diambil dari sini - dihitung
           ulang dari tabel `bpb` setelah harganya dikoreksi, supaya yang
           terbukukan persis sama dgn isi dokumennya. */
        $bpbRes = mysqli_query($conn2, "SELECT DISTINCT no_bpb
            FROM Req_update_bpb
            WHERE no_pengajuan = '$no_pengajuan_esc'");

        while ($bpbRow = mysqli_fetch_assoc($bpbRes)) {
            $no_bpb     = $bpbRow['no_bpb'];
            $no_bpb_esc = mysqli_real_escape_string($conn2, $no_bpb);

            /* ---- 1. harga & PPN baru ditulis ke `bpb` ---- */
            $okbpb = mysqli_query($conn2, "UPDATE bpb a
                INNER JOIN (SELECT no_bpb, id_jo, id_item, price_new, ppn_new FROM Req_update_bpb WHERE no_pengajuan = '$no_pengajuan_esc' AND no_bpb = '$no_bpb_esc') b
                    ON b.no_bpb = a.bpbno_int AND b.id_jo = a.id_jo AND b.id_item = a.id_item
                SET a.price = b.price_new, a.ppn = b.ppn_new");
            if (!$okbpb) {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: gagal menulis harga ke bpb";
                continue;
            }

            /* ---- 2. dokumen ini sudah pernah dijurnal? ----
               Kalau belum, harganya saja yang dikoreksi. Jurnalnya akan
               terbentuk nanti lewat proses penjurnalan BPB yang biasa. */
            $statusCheck = mysqli_query($conn2, "SELECT IF(COUNT(*) > 0, 'Approved', '-') status
                FROM tbl_list_journal
                WHERE no_journal = '$no_bpb_esc' AND status IN ('Approved','POST')");
            $statusRow = $statusCheck ? mysqli_fetch_assoc($statusCheck) : null;
            if (!$statusRow || $statusRow['status'] !== 'Approved') {
                continue;
            }

            /* ---- 3. nomor revisi, dibaca dari keterangan jurnal lama ---- */
            $revNumber = 1;
            $revRes = mysqli_query($conn2, "SELECT keterangan FROM tbl_list_journal
                WHERE no_journal = '$no_bpb_esc' AND keterangan LIKE '%(Rev %)'");
            if ($revRes) {
                while ($revRow = mysqli_fetch_assoc($revRes)) {
                    if (preg_match('/\(Rev (\d+)\)/', $revRow['keterangan'], $m)) {
                        $revNumber = max($revNumber, ((int) $m[1]) + 1);
                    }
                }
            }

            /* ---- 4. jurnal lama dibalik, lalu ditandai 'Updated' ---- */
            $reverseSql = "INSERT INTO tbl_list_journal
                (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
                 reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
                 status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
                 created_at, updated_at, profit_center, supplier)
                SELECT '', no_journal, tgl_journal, CONCAT('Reverse ', type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$approve_user_esc' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center, supplier
                FROM tbl_list_journal WHERE no_journal = '$no_bpb_esc' AND status != 'Updated'";
            if (!mysqli_query($conn2, $reverseSql)) {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: gagal membuat jurnal balik";
                continue;
            }
            if (!mysqli_query($conn2, "UPDATE tbl_list_journal SET status = 'Updated'
                                       WHERE no_journal = '$no_bpb_esc' AND status IN ('Approved','POST')")) {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: gagal menandai jurnal lama 'Updated'";
                continue;
            }

            /* ---- 5. jurnal dibangun ulang ----
               SQL di bawah DISALIN APA ADANYA dari proses_repost_bpb.php
               (baris 51-117), DGN SATU PERUBAHAN: CTE-nya diberi nama
               `bpb_grup`. Aslinya bernama `bpb` padahal di dalamnya ada
               `from bpb` yang maksudnya TABEL asli; di MariaDB 10.4 nama itu
               dianggap merujuk ke CTE-nya sendiri dan query gagal dgn
               "Unknown column 'qty'". Di 10.11 perilakunya beda, jadi berkas
               aslinya dibiarkan apa adanya.
               Jangan diubah sebagian: kalau sumbernya
               diperbaiki, salin ulang seluruhnya. $in = dokumen ini saja. */
            $in = "'" . $no_bpb_esc . "'";

            $sql_insert = "insert INTO tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center)
WITH
rate as (select curr, tanggal, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY curr, tanggal),

bpb_grup as (select *, CASE 
            WHEN mattype <> 'N' THEN
                CASE 
                    WHEN matclass = 'FABRIC' THEN 'PEMBELIAN KAIN'
                    WHEN matclass = 'ACCESORIES' THEN 'PEMBELIAN AKSESORIS'
                    WHEN matclass = 'CMT' THEN 'BIAYA MAKLOON PAKAIAN JADI'
                    WHEN matclass = 'PRINTING' THEN 'BIAYA MAKLOON PRINTING'
                    WHEN matclass = 'EMBRODEIRY' THEN 'BIAYA MAKLOON EMBRODEIRY'
                    WHEN matclass = 'WASHING' THEN 'BIAYA MAKLOON WASHING'
                    WHEN matclass = 'PAINTING' THEN 'BIAYA MAKLOON PAINTING'
                    WHEN matclass = 'HEATSEAL' THEN 'BIAYA MAKLOON HEATSEAL'
                    ELSE 'BIAYA MAKLOON LAINNYA'
                END
            ELSE
                CASE 
                    WHEN n_code_category = '1' THEN 'PEMBELIAN PERSEDIAAN ATK'
                    WHEN n_code_category = '2' THEN 'PEMBELIAN PERSEDIAAN UMUM'
                    WHEN n_code_category = '3' THEN 'BIAYA PERSEDIAAN SPAREPARTS'
                    WHEN n_code_category = '4' THEN 'BIAYA MESIN'
                    ELSE ''
                END
END AS kata1
 from (select phd.tipe_com, bpbno_int, bpb.bpbdate, ms.supplier, mi.mattype, IFNULL(mi.n_code_category,'-') n_code_category,  CASE WHEN mi.mattype = 'C' AND mi.matclass IN ('CMT','PRINTING','EMBRODEIRY','WASHING','PAINTING','HEATSEAL') THEN mi.matclass WHEN mi.mattype = 'C' THEN 'OTHER' ELSE if(mi.matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) END AS matclass, IFNULL(mps.supplier_ctg,'Third') ctg_supp, bpb.curr,bpb.username, bpb.dateinput, bpb.confirm_by, bpb.confirm_date, round(SUM(((qty - COALESCE(qty_reject,0)) * price) + (((qty - COALESCE(qty_reject,0)) * price) * (COALESCE(ph.tax,0) /100))),4) as total,round(SUM(((qty - COALESCE(qty_reject,0)) * price)),4) as dpp,round(SUM((((qty - COALESCE(qty_reject,0)) * price) * (COALESCE(ph.tax,0) /100))),4) as ppn, IF(SUBSTR(bpbno_int,1,3) = 'GEN',bpb.profit_center,'NAG') profit_center
                                from bpb 
                                inner join masteritem mi on bpb.id_item = mi.id_item
                                inner join mastersupplier ms on bpb.id_supplier = ms.id_supplier
                                left join ap_mapping_supplier mps on mps.id_supplier = ms.id_supplier
                                left join po_header ph on bpb.pono = ph.pono
                                left join po_header_draft phd on phd.id = ph.id_draft
                                where bpbno_int IN ($in) group by bpbno,mi.mattype, mi.n_code_category order by bpbno_int) a),
                                
bpb_credit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.total credit, 0 debit_idr, ROUND(a.total * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_credit' ),
                                
bpb_debit as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.dpp debit, 0 credit, ROUND(a.dpp * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),
                                
ppn as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.52.07' no_coa, 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.ppn debit, 0 credit, ROUND(a.ppn * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

bpb_debit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '1.34.05' no_coa, 'PIUTANG LAIN-LAIN PIHAK KETIGA - BAHAN BAKU / BAHAN PEMBANTU' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, a.total debit, 0 credit, ROUND(a.total * IFNULL(rate,1),2) debit_idr, 0 credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

bpb_credit_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, c.no_coa, c.nama_coa, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DEP04SUB004','-') no_costcenter, IF(SUBSTR(bpbno_int,1,3) = 'WIP','DISTRIBUTION CENTER','-') nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.dpp credit, 0 debit_idr, ROUND(a.dpp * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),

ppn_buyer as (select '' id, bpbno_int no_journal, bpbdate tgl_journal, 'AP - BPB' type_journal, '8.07.01' no_coa, 'PENDAPATAN LAIN-LAIN' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, a.curr, IFNULL(rate,1) rate, 0 debit, a.ppn credit, 0 debit_idr, ROUND(a.ppn * IFNULL(rate,1),2) credit_idr, 'APPROVED' status, CONCAT(kata1,' ',bpbno_int,' DARI ', UPPER(supplier)) keterangan, username create_by, dateinput create_date, confirm_by approve_by, confirm_date approve_date, '-' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from bpb_grup a LEFT JOIN rate b on b.curr = a.curr and b.tanggal = a.bpbdate INNER JOIN ap_mapping_coa_jurnal c on c.mattype = a.mattype and c.ctg_supp = a.ctg_supp and c.matclass = a.matclass and c.n_code_ctg = a.n_code_category where c.type_mapping = 'bpb_debit'),
                                
jurnal as (select * from (select * from bpb_credit where (debit-credit) != 0
UNION ALL
select * from bpb_debit where (debit-credit) != 0
UNION ALL
select * from ppn where (debit-credit) != 0) a order by no_journal, no_coa asc),

jurnal_buyer as (select * from (select * from bpb_credit_buyer where (debit-credit) != 0
UNION ALL
select * from bpb_debit_buyer where (debit-credit) != 0
UNION ALL
select * from ppn_buyer where (debit-credit) != 0) a order by no_journal, no_coa asc)
                                
select * from jurnal
UNION ALL
SELECT * 
FROM jurnal_buyer jb
WHERE EXISTS (
            SELECT 1 
            FROM bpb_grup
            WHERE tipe_com = 'BUYER' GROUP BY bpbno_int)";

            if (!mysqli_query($conn2, $sql_insert)) {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: gagal membangun jurnal baru (" . mysqli_error($conn2) . ")";
                continue;
            }
            $dibuat = mysqli_affected_rows($conn2);
            $journalEntries += $dibuat;

            if ($dibuat == 0) {
                /* Jurnal lamanya SUDAH dibalik di atas. Kalau tidak ada baris baru
                   yang terbentuk, dokumen itu kehilangan jurnalnya - harus
                   kelihatan, bukan lewat diam-diam. Penyebab paling sering:
                   kategori barangnya belum ada di ap_mapping_coa_jurnal. */
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: jurnal baru TIDAK terbentuk - cek pemetaan ap_mapping_coa_jurnal";
                continue;
            }

            /* ---- 6. nomor revisi ditempelkan ke keterangan jurnal baru ----
               Baris yang baru dibuat adalah satu-satunya yang berstatus APPROVED
               utk dokumen ini (yang lama sudah jadi 'Updated'). */
            mysqli_query($conn2, "UPDATE tbl_list_journal
                SET keterangan = CONCAT(keterangan, ' (Rev $revNumber)')
                WHERE no_journal = '$no_bpb_esc' AND status = 'APPROVED'
                  AND keterangan NOT LIKE '%(Rev %)'");

            /* Jurnal baru harus balance. Diperiksa di sini, bukan dipercaya. */
            $bal = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT
                    ROUND(SUM(debit),2) d, ROUND(SUM(credit),2) k,
                    ROUND(SUM(debit_idr),2) d_idr, ROUND(SUM(credit_idr),2) k_idr
                FROM tbl_list_journal WHERE no_journal = '$no_bpb_esc' AND status = 'APPROVED'"));
            if ($bal && (abs((float) $bal['d'] - (float) $bal['k']) > 0.01
                      || abs((float) $bal['d_idr'] - (float) $bal['k_idr']) > 0.01)) {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: jurnal baru TIDAK balance (D {$bal['d']} vs K {$bal['k']})";
            }
        }
    }

    if ($gagalFatal) {
        mysqli_rollback($conn2);
        continue;
    }

    if (!mysqli_query($conn2, "UPDATE Req_update_bpb_h SET status = '$newStatus' WHERE no_pengajuan = '$no_pengajuan_esc'")) {
        mysqli_rollback($conn2);
        $journalWarnings[] = "$no_pengajuan: gagal mengubah status pengajuan";
        continue;
    }

    mysqli_commit($conn2);
    $approved++;
}

$pesan = ($action === 'approve')
    ? "$approved request(s) approved, $journalEntries journal line(s) written"
    : "$approved request(s) cancelled";
if ($skipped > 0)             { $pesan .= ", $skipped skipped"; }
if (!empty($journalWarnings)) { $pesan .= '. PERHATIAN: ' . implode('; ', $journalWarnings); }

echo json_encode(array(
    'success'  => true,
    'message'  => $pesan,
    'warnings' => $journalWarnings,
));
?>
