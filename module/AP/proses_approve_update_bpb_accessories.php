<?php
/* ============================================================================
   Update BPB - ACCESSORIES : proses APPROVE / CANCEL pengajuan koreksi harga.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen aksesoris ada LANGSUNG di tabel `bpb`, dan GACC/IN maupun GACC/RI
   sama-sama PENERIMAAN secara jurnal - diperiksa ke produksi 8 Okt 2026.
   Karena itu cabang retur (GK/RO) milik Fabric sudah dibuang dari berkas ini.

   Cara penjurnalannya SENGAJA dibiarkan seperti versi Fabric (dihitung di
   PHP, COA dicari di `mastercoa_v2`), supaya perilaku Accessories yang sudah
   berjalan tidak berubah. Dokumen aksesoris tidak pernah berkategori campur
   (0% dari 35.820 dokumen, diperiksa 8 Okt 2026), jadi cara ini memadai.
   Bandingkan dgn versi General yang HARUS memakai SQL per grup karena 2%
   dokumennya berkategori campur.
   ============================================================================ */
include '../../conn/conn.php';
/* Berkas ini khusus Accessories - tidak ada parameter di tautan. */
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$list = $_POST['no_pengajuan'] ?? [];
$approve_user = $_POST['approve_user'] ?? '';
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

if (!is_array($list) || empty($list)) {
    echo json_encode(['success' => false, 'message' => 'Select at least 1 request']);
    exit;
}

if (!in_array($action, ['approve', 'cancel'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$newStatus = $action === 'approve' ? 'Approved' : 'Cancel';
$updated = 0;
$skipped = 0;
$journalEntries = 0;
$journalWarnings = [];

foreach ($list as $no_pengajuan) {
    $no_pengajuan_esc = mysqli_real_escape_string($conn2, $no_pengajuan);
    $gagalFatal = false;   // true = seluruh pengajuan ini dibatalkan

    $check = mysqli_query($conn2, "SELECT status FROM Req_update_bpb_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
    $row = mysqli_fetch_assoc($check);

    if (!$row || in_array($row['status'], ['Approved', 'Cancel'])) {
        $skipped++;
        continue;
    }

    /* Satu pengajuan = satu transaksi. Harga, jurnal balik, jurnal baru, dan
       status pengajuan harus jadi bersama-sama atau batal bersama-sama. */
    mysqli_begin_transaction($conn2);

    if ($action === 'approve') {
        // For each BPB touched by this request, reverse the old journal lines
        // and book corrected lines if the BPB has already been journaled.
        /* Cukup daftar BPB-nya. Nilai jurnal TIDAK diambil dari sini -
           dihitung ulang dari tabel bpb setelah harganya dikoreksi,
           supaya yang terbukukan persis sama dgn isi dokumennya. */
        $bpbRes = mysqli_query($conn2, "SELECT DISTINCT no_bpb
            FROM Req_update_bpb
            WHERE no_pengajuan = '$no_pengajuan_esc'");

        while ($bpbRow = mysqli_fetch_assoc($bpbRes)) {
            $no_bpb = $bpbRow['no_bpb'];
            $no_bpb_esc = mysqli_real_escape_string($conn2, $no_bpb);

            /* Dokumen aksesoris ada LANGSUNG di `bpb` - tidak punya tabel
               kepala gudang seperti Fabric - dan GACC/IN maupun GACC/RI
               sama-sama PENERIMAAN secara jurnal (diperiksa ke produksi
               8 Okt 2026: Persediaan Aksesoris didebit, GR/IR Aksesoris
               dikredit, type 'AP - BPB'). Jadi TIDAK ADA cabang arah
               terbalik seperti GK/RO di Fabric. */

            /* Harga & PPN baru ditulis apa pun status jurnalnya, supaya isi
               dokumennya selalu mencerminkan nilai yang dikoreksi. */
            $okbpb = mysqli_query($conn2, "UPDATE bpb a
                INNER JOIN (SELECT no_bpb, id_jo, id_item, price_new, ppn_new FROM Req_update_bpb WHERE no_pengajuan = '$no_pengajuan_esc' AND no_bpb = '$no_bpb_esc') b
                    ON b.no_bpb = a.bpbno_int AND b.id_jo = a.id_jo AND b.id_item = a.id_item
                SET a.price = b.price_new, a.ppn = b.ppn_new");
            if (!$okbpb) { $gagalFatal = true; $journalWarnings[] = "$no_bpb: gagal menulis harga ke bpb"; }

            /* Aksesoris tidak punya kolom status tabel kepala, jadi yang
               diperiksa langsung ADA-TIDAKNYA jurnalnya. Dokumen yang belum
               pernah dijurnal cukup dikoreksi harganya. */
            $statusCheck = mysqli_query($conn2, "SELECT IF(COUNT(*) > 0, 'Approved', '-') status
                FROM tbl_list_journal
                WHERE no_journal = '$no_bpb_esc' AND status IN ('Approved','POST')");

            $statusRow = mysqli_fetch_assoc($statusCheck);

            if (!$statusRow || $statusRow['status'] !== 'Approved') {
                continue;
            }

            $reverseSql = "INSERT INTO tbl_list_journal
                (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
                 reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
                 status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
                 created_at, updated_at, profit_center, supplier)
                SELECT '', no_journal, tgl_journal, CONCAT('Reverse ', type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$approve_user_esc' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center, supplier
                FROM tbl_list_journal WHERE no_journal = '$no_bpb_esc' AND status != 'Updated'";

            if (mysqli_query($conn2, $reverseSql)) {
                $journalEntries += mysqli_affected_rows($conn2);
            } else {
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: jurnal balik GAGAL dibuat";
            }

            // The original (now-superseded) journal lines are no longer active -
            // mark them 'Updated' so reports don't double-count them alongside
            // the new corrected lines (status 'Approved') booked below.
            mysqli_query($conn2, "UPDATE tbl_list_journal SET status = 'Updated' WHERE no_journal = '$no_bpb_esc' AND status IN ('Approved', 'POST')");

            /* Nilai jurnal dihitung ULANG dari `bpb` sesudah harganya dikoreksi,
               supaya yang terbukukan persis sama dgn isi dokumennya. */
            // Re-derive the BPB totals from the now-corrected price/PPN
            // (set above) and book a fresh journal, mirroring proses_repost_bpb.php.
            $cekDataRes = mysqli_query($conn2, "
                SELECT
                    phd.tipe_com, mi.itemdesc, bpb.bpbno, bpb.bpbno_int, bpb.bpbdate,
                    bpb.id_supplier, ms.Supplier supplier, mi.mattype, mi.n_code_category,
                    IF(mi.matclass LIKE '%ACCESORIES%', 'ACCESORIES', mi.matclass) matclass,
                    bpb.curr, COALESCE(bpb.ppn,0) tax, bpb.username, bpb.dateinput,
                    ROUND(SUM(((bpb.qty - COALESCE(bpb.qty_reject,0)) * bpb.price) + (((bpb.qty - COALESCE(bpb.qty_reject,0)) * bpb.price) * (COALESCE(bpb.ppn,0) / 100))), 2) total,
                    ROUND(SUM((bpb.qty - COALESCE(bpb.qty_reject,0)) * bpb.price), 2) dpp,
                    ROUND(SUM(((bpb.qty - COALESCE(bpb.qty_reject,0)) * bpb.price) * (COALESCE(bpb.ppn,0) / 100)), 2) ppn
                FROM bpb
                INNER JOIN masteritem mi ON bpb.id_item = mi.id_item
                INNER JOIN mastersupplier ms ON bpb.id_supplier = ms.Id_Supplier
                LEFT JOIN po_header ph ON bpb.pono = ph.pono
                LEFT JOIN po_header_draft phd ON phd.id = ph.id_draft
                WHERE bpb.bpbno_int = '$no_bpb_esc'
                GROUP BY bpb.bpbno, mi.mattype, mi.n_code_category
                ORDER BY ms.Supplier
            ");
            $journalData = $cekDataRes ? mysqli_fetch_assoc($cekDataRes) : null;
            $tgl_bpb_journal_col = 'bpbdate';
            $tgl_bpb_journal_col = 'bpbdate';


            if (!$journalData) {
                /* Jurnal lamanya SUDAH dibalik di atas. Kalau sampai di sini
                   datanya tidak terbaca, dokumen itu kehilangan jurnalnya -
                   harus kelihatan, bukan lewat diam-diam. */
                $gagalFatal = true;
                $journalWarnings[] = "$no_bpb: data BPB tidak terbaca, jurnal baru TIDAK dibuat";
            }

            if ($journalData) {
                $supp             = $journalData['supplier'];
                $id_supplier      = $journalData['id_supplier'];
                $mattype          = $journalData['mattype'];
                $matclass1        = $journalData['matclass'];
                $n_code_category  = $journalData['n_code_category'];
                $tax              = (float) $journalData['tax'];
                $curr             = $journalData['curr'];
                $username         = $journalData['username'];
                $dpp              = round((float) $journalData['dpp'], 2);
                $ppn              = round((float) $journalData['ppn'], 2);
                /* TOTAL dihitung dari dpp + ppn yang SUDAH dibulatkan, bukan
                   diambil dari SQL yang membulatkannya sendiri: ROUND(a+b) tidak
                   selalu sama dgn ROUND(a)+ROUND(b), dan selisih satu sen itu
                   membuat jurnalnya tidak balance (ketahuan di GK/IN/1225/00315,
                   4 baris: kredit 8.209,61 vs debit 8.209,60). Dgn cara ini sisi
                   kredit selalu sama persis dgn jumlah sisi debit. */
                $total            = round($dpp + $ppn, 2);
                $tgl_bpb_journal  = $journalData[$tgl_bpb_journal_col];
                $dateinput_       = $journalData['dateinput'];

                if ($mattype === 'C') {
                    $matclass = in_array($matclass1, ['CMT', 'PRINTING', 'EMBRODEIRY', 'WASHING', 'PAINTING', 'HEATSEAL']) ? $matclass1 : 'OTHER';
                } else {
                    $matclass = $matclass1;
                }

                $rate = 1;
                if ($curr !== 'IDR') {
                    /* MATA UANGNYA WAJIB IKUT DISARING. Tanpa itu, begitu satu
                       tanggal memuat lebih dari satu mata uang, kurs mana pun
                       bisa terambil dan seluruh nilai IDR jurnal ikut salah. */
                    $tgl_bpb_journal_esc = mysqli_real_escape_string($conn2, $tgl_bpb_journal);
                    $curr_esc = mysqli_real_escape_string($conn2, $curr);
                    $rateRes = mysqli_query($conn2, "SELECT ROUND(rate,2) rate FROM masterrate WHERE tanggal = '$tgl_bpb_journal_esc' AND v_codecurr = 'PAJAK' AND curr = '$curr_esc' LIMIT 1");
                    $rateRow = $rateRes ? mysqli_fetch_assoc($rateRes) : null;
                    $rate = $rateRow ? (float) $rateRow['rate'] : 1;
                    if (!$rateRow) {
                        $journalWarnings[] = "$no_bpb: kurs $curr tanggal $tgl_bpb_journal tidak ketemu, dipakai 1";
                    }
                }

                /* Nilai IDR diperlakukan sama: totalnya dijumlah dari dua sisi
                   yang sudah dibulatkan, bukan dibulatkan sendiri. */
                $idr_dpp = round($dpp * $rate, 2);
                $idr_ppn = round($ppn * $rate, 2);
                $idr_total = round($idr_dpp + $idr_ppn, 2);

                $cust_ctg = in_array($id_supplier, ['342', '20', '19', '692', '17', '18']) ? 'Related' : 'Third';

                /* Aksesoris selalu penerimaan, jadi tidak ada kata 'RETURN'. */
                $kata1 = '';
                if ($mattype !== 'N') {
                    switch ($matclass) {
                        case 'FABRIC':      $kata1 = 'PEMBELIAN KAIN'; break;
                        case 'ACCESORIES':  $kata1 = 'PEMBELIAN AKSESORIS'; break;
                        case 'CMT':         $kata1 = 'BIAYA MAKLOON PAKAIAN JADI'; break;
                        case 'PRINTING':    $kata1 = 'BIAYA MAKLOON PRINTING'; break;
                        case 'EMBRODEIRY':  $kata1 = 'BIAYA MAKLOON EMBRODEIRY'; break;
                        case 'WASHING':     $kata1 = 'BIAYA MAKLOON WASHING'; break;
                        case 'PAINTING':    $kata1 = 'BIAYA MAKLOON PAINTING'; break;
                        case 'HEATSEAL':    $kata1 = 'BIAYA MAKLOON HEATSEAL'; break;
                        default:            $kata1 = 'BIAYA MAKLOON LAINNYA';
                    }
                } else {
                    switch ($n_code_category) {
                        case '1': $kata1 = 'PEMBELIAN PERSEDIAAN ATK'; break;
                        case '2': $kata1 = 'PEMBELIAN PERSEDIAAN UMUM'; break;
                        case '3': $kata1 = 'BIAYA PERSEDIAAN SPAREPARTS'; break;
                        case '4': $kata1 = 'BIAYA MESIN'; break;
                        default:  $kata1 = '';
                    }
                }
                // Figure out which revision this correction is, so repeated
                // corrections on the same no_bpb are distinguishable in the journal.
                $revNumber = 1;
                $revRes = mysqli_query($conn2, "SELECT keterangan FROM tbl_list_journal WHERE no_journal = '$no_bpb_esc' AND keterangan LIKE '%(Rev %)'");
                if ($revRes) {
                    while ($revRow = mysqli_fetch_assoc($revRes)) {
                        if (preg_match('/\(Rev (\d+)\)/', $revRow['keterangan'], $revMatch)) {
                            $revNumber = max($revNumber, (int) $revMatch[1] + 1);
                        }
                    }
                }

                $description = $kata1 . ' ' . $no_bpb . ' DARI ' . $supp . " (Rev $revNumber)";

                $cust_ctg_esc        = mysqli_real_escape_string($conn2, $cust_ctg);
                $mattype_esc         = mysqli_real_escape_string($conn2, $mattype);
                $matclass_esc        = mysqli_real_escape_string($conn2, $matclass);
                $n_code_category_esc = mysqli_real_escape_string($conn2, $n_code_category);

                $sqlCoaCreRes = mysqli_query($conn2, "SELECT no_coa, nama_coa FROM mastercoa_v2
                    WHERE cus_ctg LIKE '%$cust_ctg_esc%' AND mattype LIKE '%$mattype_esc%' AND matclass LIKE '%$matclass_esc%' AND n_code_category LIKE '%$n_code_category_esc%' AND inv_type LIKE '%bpb_credit%' LIMIT 1");
                $coaCre = $sqlCoaCreRes ? mysqli_fetch_assoc($sqlCoaCreRes) : null;
                $no_coa_cre = $coaCre ? $coaCre['no_coa'] : '-';
                $nama_coa_cre = $coaCre ? $coaCre['nama_coa'] : '-';

                $sqlCoaDebRes = mysqli_query($conn2, "SELECT no_coa, nama_coa FROM mastercoa_v2
                    WHERE cus_ctg LIKE '%$cust_ctg_esc%' AND mattype LIKE '%$mattype_esc%' AND matclass LIKE '%$matclass_esc%' AND n_code_category LIKE '%$n_code_category_esc%' AND inv_type LIKE '%bpb_debit%' LIMIT 1");
                $coaDeb = $sqlCoaDebRes ? mysqli_fetch_assoc($sqlCoaDebRes) : null;
                $no_coa_deb = $coaDeb ? $coaDeb['no_coa'] : '-';
                $nama_coa_deb = $coaDeb ? $coaDeb['nama_coa'] : '-';

                /* COA '-' tetap dibukukan supaya sisi lawannya tidak hilang dan
                   jurnalnya tetap balance, tapi harus dilaporkan - baris ber-COA
                   '-' tidak akan terbaca laporan mana pun. */
                if (!$coaCre) { $journalWarnings[] = "$no_bpb: COA kredit tidak ketemu di mastercoa_v2"; }
                if (!$coaDeb) { $journalWarnings[] = "$no_bpb: COA debit tidak ketemu di mastercoa_v2"; }

                $journalTimestamp     = date('Y-m-d H:i:s');
                $tgl_bpb_journal_esc  = mysqli_real_escape_string($conn2, $tgl_bpb_journal);
                $curr_esc             = mysqli_real_escape_string($conn2, $curr);
                $username_esc         = mysqli_real_escape_string($conn2, $username);
                $dateinput_esc        = mysqli_real_escape_string($conn2, $dateinput_);
                $description_esc      = mysqli_real_escape_string($conn2, $description);
                $no_coa_cre_esc       = mysqli_real_escape_string($conn2, $no_coa_cre);
                $nama_coa_cre_esc     = mysqli_real_escape_string($conn2, $nama_coa_cre);
                $no_coa_deb_esc       = mysqli_real_escape_string($conn2, $no_coa_deb);
                $nama_coa_deb_esc     = mysqli_real_escape_string($conn2, $nama_coa_deb);
                $type_journal         = 'AP - BPB';

                // Penerimaan: bpb_credit-COA dikredit dengan total, bpb_debit-COA didebit dengan dpp.
                // Pengeluaran (Retur): arahnya dibalik - bpb_credit-COA didebit, bpb_debit-COA dikredit.
                $cre_debit  = 0;
                $cre_credit = $total;
                $cre_debit_idr  = 0;
                $cre_credit_idr = $idr_total;

                $deb_debit  = $dpp;
                $deb_credit = 0;
                $deb_debit_idr  = $idr_dpp;
                $deb_credit_idr = 0;

                $ppn_debit  = $ppn;
                $ppn_credit = 0;
                $ppn_debit_idr  = $idr_ppn;
                $ppn_credit_idr = 0;

                if (mysqli_query($conn2, "INSERT INTO tbl_list_journal
                    (no_journal, tgl_journal, type_journal, no_coa, nama_coa, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, created_at, updated_at, profit_center)
                    VALUES ('$no_bpb_esc', '$tgl_bpb_journal_esc', '$type_journal', '$no_coa_cre_esc', '$nama_coa_cre_esc', '$curr_esc', $rate, $cre_debit, $cre_credit, $cre_debit_idr, $cre_credit_idr, 'Approved', '$description_esc', '$username_esc', '$dateinput_esc', '$approve_user_esc', '$journalTimestamp', '$journalTimestamp', '$journalTimestamp', 'NAG')")) {
                    $journalEntries += mysqli_affected_rows($conn2);
                } else {
                    $gagalFatal = true;
                    $journalWarnings[] = "$no_bpb: baris kredit GAGAL dibukukan";
                }

                if (mysqli_query($conn2, "INSERT INTO tbl_list_journal
                    (no_journal, tgl_journal, type_journal, no_coa, nama_coa, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, created_at, updated_at, profit_center)
                    VALUES ('$no_bpb_esc', '$tgl_bpb_journal_esc', '$type_journal', '$no_coa_deb_esc', '$nama_coa_deb_esc', '$curr_esc', $rate, $deb_debit, $deb_credit, $deb_debit_idr, $deb_credit_idr, 'Approved', '$description_esc', '$username_esc', '$dateinput_esc', '$approve_user_esc', '$journalTimestamp', '$journalTimestamp', '$journalTimestamp', 'NAG')")) {
                    $journalEntries += mysqli_affected_rows($conn2);
                } else {
                    $gagalFatal = true;
                    $journalWarnings[] = "$no_bpb: baris debit GAGAL dibukukan";
                }

                if ($tax >= 1) {
                    $sqlCoaPpnRes = mysqli_query($conn2, "SELECT no_coa, nama_coa FROM mastercoa_v2 WHERE inv_type LIKE '%PPN MASUKAN%' LIMIT 1");
                    $coaPpn = $sqlCoaPpnRes ? mysqli_fetch_assoc($sqlCoaPpnRes) : null;
                    $no_coa_ppn_esc = mysqli_real_escape_string($conn2, $coaPpn ? $coaPpn['no_coa'] : '-');
                    $nama_coa_ppn_esc = mysqli_real_escape_string($conn2, $coaPpn ? $coaPpn['nama_coa'] : '-');

                    if (mysqli_query($conn2, "INSERT INTO tbl_list_journal
                        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, created_at, updated_at, profit_center)
                        VALUES ('$no_bpb_esc', '$tgl_bpb_journal_esc', '$type_journal', '$no_coa_ppn_esc', '$nama_coa_ppn_esc', '$curr_esc', $rate, $ppn_debit, $ppn_credit, $ppn_debit_idr, $ppn_credit_idr, 'Approved', '$description_esc', '$username_esc', '$dateinput_esc', '$approve_user_esc', '$journalTimestamp', '$journalTimestamp', '$journalTimestamp', 'NAG')")) {
                        $journalEntries += mysqli_affected_rows($conn2);
                    } else {
                        $gagalFatal = true;
                    $journalWarnings[] = "$no_bpb: baris PPN GAGAL dibukukan";
                    }
                }
            }
        }
    }

    if ($gagalFatal) {
        /* Dibatalkan SELURUHNYA - termasuk harga yang sudah sempat ditulis.
           Dokumennya dibiarkan belum di-approve supaya bisa diulang setelah
           sebabnya dibereskan. */
        mysqli_rollback($conn2);
        $skipped++;
        $journalWarnings[] = "$no_pengajuan: DIBATALKAN seluruhnya, tidak ada yang tersimpan";
        continue;
    }

    if (!mysqli_query($conn2, "UPDATE Req_update_bpb_h SET status = '$newStatus' WHERE no_pengajuan = '$no_pengajuan_esc'")) {
        mysqli_rollback($conn2);
        $skipped++;
        $journalWarnings[] = "$no_pengajuan: gagal mengubah status, DIBATALKAN seluruhnya";
        continue;
    }

    mysqli_commit($conn2);
    $updated++;
}

echo json_encode([
    'success' => true,
    'updated' => $updated,
    'skipped' => $skipped,
    'journal_entries' => $journalEntries,
    'journal_warnings' => $journalWarnings,
]);
?>
