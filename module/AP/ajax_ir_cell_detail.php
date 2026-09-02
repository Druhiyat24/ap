<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

function irFmtDate($v) {
    if ($v === null || $v === '' || $v === '0000-00-00' || $v === '0000-00-00 00:00:00') return '-';
    return date('d-M-Y', strtotime($v));
}

function irEsc($conn, $v) {
    return mysqli_real_escape_string($conn, (string) $v);
}

function irFindNoKbon($conn2, $no_invoice, $nama_supp) {
    $ni = irEsc($conn2, $no_invoice);
    $ns = irEsc($conn2, $nama_supp);
    $q = mysqli_query($conn2, "select k.no_kbon from bpb_new bn inner join kontrabon k on k.no_bpb = bn.no_bpb where bn.upt_no_inv = '$ni' and bn.supplier = '$ns' and k.status != 'Cancel' order by k.tgl_kbon desc limit 1");
    $r = $q ? mysqli_fetch_assoc($q) : null;
    return $r ? $r['no_kbon'] : null;
}

function irFindBpb($conn2, $no_invoice, $nama_supp, $orderCol) {
    $ni = irEsc($conn2, $no_invoice);
    $ns = irEsc($conn2, $nama_supp);
    $allowed = ['bpbdate', 'dateinput', 'confirm_date', 'trf_date'];
    if (!in_array($orderCol, $allowed, true)) return null;
    $q = mysqli_query($conn2, "select a.bpbno_int, a.bpbdate, a.dateinput, a.username, a.confirm_date, a.confirm_by, a.trf_date from bpb a inner join mastersupplier m on m.id_supplier = a.id_supplier where a.upt_no_inv = '$ni' and m.supplier = '$ns' order by a.$orderCol desc limit 1");
    return $q ? mysqli_fetch_assoc($q) : null;
}

$stage = isset($_POST['stage']) ? $_POST['stage'] : '';
$no_invoice = isset($_POST['no_invoice']) ? $_POST['no_invoice'] : '';
$doc_number = isset($_POST['doc_number']) ? $_POST['doc_number'] : '';
$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : '';

$doc_number_esc = irEsc($conn2, $doc_number);
$no_invoice_esc = irEsc($conn2, $no_invoice);
$nama_supp_esc = irEsc($conn2, $nama_supp);

$no_dokumen = null;
$tanggal = null;
$label = null;
$oleh = null;

switch ($stage) {
    case 'no_invoice':
        $q = mysqli_query($conn2, "select no_invoice, created_by, created_date from ir_invoice_supp where no_invoice = '$no_invoice_esc' and doc_number = '$doc_number_esc' limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        if ($r) {
            $no_dokumen = $r['no_invoice'];
            $tanggal = irFmtDate($r['created_date']);
            $label = 'Dibuat Oleh';
            $oleh = $r['created_by'];
        }
        break;

    case 'doc_number':
        $q = mysqli_query($conn2, "select doc_number, created_by, created_date from ir_invoice_supp_h where doc_number = '$doc_number_esc' limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        if ($r) {
            $no_dokumen = $r['doc_number'];
            $tanggal = irFmtDate($r['created_date']);
            $label = 'Dibuat Oleh';
            $oleh = $r['created_by'];
        }
        break;

    case 'bpbdate':
    case 'dateinput':
    case 'confirm_date':
    case 'trf_date':
        $r = irFindBpb($conn2, $no_invoice, $nama_supp, $stage);
        if ($r) {
            $no_dokumen = $r['bpbno_int'];
            if ($stage === 'bpbdate' || $stage === 'dateinput') {
                $tanggal = irFmtDate($r[$stage]);
                $label = 'Dibuat Oleh';
                $oleh = $r['username'];
            } elseif ($stage === 'confirm_date') {
                $tanggal = irFmtDate($r['confirm_date']);
                $label = 'Diapprove Oleh';
                $oleh = $r['confirm_by'];
            } else { // trf_date - no approver column exists for this stage
                $tanggal = irFmtDate($r['trf_date']);
                $label = null;
                $oleh = null;
            }
        }
        break;

    case 'tfta_date':
    case 'receive_acc_date':
    case 'tatp_date':
    case 'receive_pch_date':
    case 'tptf_date':
    case 'receive_fin_date':
        $byCol = [
            'tfta_date' => 'tfta_by',
            'receive_acc_date' => 'receive_acc_by',
            'tatp_date' => 'tatp_by',
            'receive_pch_date' => 'receive_pch_by',
            'tptf_date' => 'tptf_by',
            'receive_fin_date' => 'receive_fin_by',
        ][$stage];
        $q = mysqli_query($conn2, "select doc_number, `$stage`, `$byCol` from ir_invoice_supp_h where doc_number = '$doc_number_esc' limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        if ($r) {
            $no_dokumen = $r['doc_number'];
            $tanggal = irFmtDate($r[$stage]);
            $label = 'Dikonfirmasi Oleh';
            $oleh = $r[$byCol];
        }
        break;

    case 'tgl_kbon':
        $q = mysqli_query($conn2, "select k.no_kbon, k.tgl_kbon, k.confirm_user, k.create_user from bpb_new bn inner join kontrabon k on k.no_bpb = bn.no_bpb where bn.upt_no_inv = '$no_invoice_esc' and bn.supplier = '$nama_supp_esc' and k.status != 'Cancel' order by k.tgl_kbon desc limit 1");
        $r = $q ? mysqli_fetch_assoc($q) : null;
        if ($r) {
            $no_dokumen = $r['no_kbon'];
            $tanggal = irFmtDate($r['tgl_kbon']);
            if (!empty($r['confirm_user'])) {
                $label = 'Dikonfirmasi Oleh';
                $oleh = $r['confirm_user'];
            } else {
                $label = 'Dibuat Oleh';
                $oleh = $r['create_user'];
            }
        }
        break;

    case 'pvl_date':
    case 'pvl_approve_date':
        $no_kbon = irFindNoKbon($conn2, $no_invoice, $nama_supp);
        if ($no_kbon !== null) {
            $nk_esc = irEsc($conn2, $no_kbon);
            $q = mysqli_query($conn2, "select h.pl_number, h.pl_date, h.approve_user, h.approve_date, h.created_by from pv_payment_voucher_list_det d inner join pv_payment_voucher_list_h h on h.pl_number = d.pl_number where d.no_kbon = '$nk_esc' and d.type_pv = 'Regular' and d.status != 'Cancel' and h.status != 'Cancel' order by h.pl_date desc limit 1");
            $r = $q ? mysqli_fetch_assoc($q) : null;
            if ($r) {
                $no_dokumen = $r['pl_number'];
                if ($stage === 'pvl_date') {
                    $tanggal = irFmtDate($r['pl_date']);
                    if (!empty($r['approve_user'])) { $label = 'Diapprove Oleh'; $oleh = $r['approve_user']; }
                    else { $label = 'Dibuat Oleh (Draft)'; $oleh = $r['created_by']; }
                } else { // pvl_approve_date
                    if (!empty($r['approve_user'])) {
                        $tanggal = irFmtDate($r['approve_date']);
                        $label = 'Diapprove Oleh';
                        $oleh = $r['approve_user'];
                    } else {
                        $tanggal = '-';
                        $label = null;
                        $oleh = null;
                    }
                }
            }
        }
        break;

    case 'tgl_payment':
    case 'tgl_approve_lp':
        $no_kbon = irFindNoKbon($conn2, $no_invoice, $nama_supp);
        $r = null;
        if ($no_kbon !== null) {
            $nk_esc = irEsc($conn2, $no_kbon);
            $q = mysqli_query($conn2, "select h.pl_number, h.pl_date, h.first_approve_user, h.first_approve_date, h.second_approve_user, h.second_approve_date, h.created_by from pv_payment_list_det d inner join pv_payment_list_h h on h.pl_number = d.pl_number where d.no_kbon = '$nk_esc' and d.type_pv = 'Regular' and d.status != 'Cancel' and h.status != 'Cancel' order by h.pl_date desc limit 1");
            $r = $q ? mysqli_fetch_assoc($q) : null;
        }
        if ($r) {
            $no_dokumen = $r['pl_number'];
            if ($stage === 'tgl_payment') {
                $tanggal = irFmtDate($r['pl_date']);
                $label = 'Dibuat Oleh';
                $oleh = $r['created_by'];
            } else { // tgl_approve_lp
                if (!empty($r['second_approve_user'])) {
                    $tanggal = irFmtDate($r['second_approve_date']);
                    $label = 'Second Approve Oleh';
                    $oleh = $r['second_approve_user'];
                } elseif (!empty($r['first_approve_user'])) {
                    $tanggal = irFmtDate($r['first_approve_date']);
                    $label = 'First Approve Oleh (Belum Second Approve)';
                    $oleh = $r['first_approve_user'];
                } else {
                    $tanggal = '-';
                    $label = null;
                    $oleh = null;
                }
            }
        } elseif ($no_kbon !== null) {
            // legacy fallback
            $nk_esc = irEsc($conn2, $no_kbon);
            $q = mysqli_query($conn2, "select no_payment, tgl_payment, confirm_date, confirm_user, create_user from list_payment where no_kbon = '$nk_esc' and status != 'Cancel' order by tgl_payment desc limit 1");
            $rl = $q ? mysqli_fetch_assoc($q) : null;
            if ($rl) {
                $no_dokumen = $rl['no_payment'];
                if ($stage === 'tgl_payment') {
                    $tanggal = irFmtDate($rl['tgl_payment']);
                    $label = 'Dibuat Oleh';
                    $oleh = $rl['create_user'];
                } else {
                    $tanggal = irFmtDate($rl['confirm_date']);
                    $label = 'Diapprove Oleh';
                    $oleh = $rl['confirm_user'];
                }
            }
        }
        break;

    case 'bankout_date':
        $no_kbon = irFindNoKbon($conn2, $no_invoice, $nama_supp);
        $r = null;
        if ($no_kbon !== null) {
            $nk_esc = irEsc($conn2, $no_kbon);
            $q = mysqli_query($conn2, "select a.no_bankout doc, b.bankout_date tgl, b.approve_by aby, b.create_by cby from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff = '$nk_esc' and a.type_pv = 'Regular' and b.status != 'Cancel' order by b.bankout_date desc limit 1");
            $r = $q ? mysqli_fetch_assoc($q) : null;
            if (!$r) {
                $q = mysqli_query($conn2, "select a.no_pco doc, b.tgl_pco tgl, b.approve_by aby, b.create_by cby from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where a.no_reff = '$nk_esc' and a.type_pv = 'Regular' and b.status != 'Cancel' order by b.tgl_pco desc limit 1");
                $r = $q ? mysqli_fetch_assoc($q) : null;
            }
            if (!$r) {
                $q = mysqli_query($conn2, "select payment_ftr_id doc, tgl_pelunasan tgl, approve_by aby, create_user cby from payment_ftr where no_kbon = '$nk_esc' and type_pv = 'Regular' and status != 'Cancel' order by tgl_pelunasan desc limit 1");
                $r = $q ? mysqli_fetch_assoc($q) : null;
            }
        }
        if (!$r && $no_kbon !== null) {
            // legacy fallback via list_payment.no_payment
            $nk_esc = irEsc($conn2, $no_kbon);
            $q = mysqli_query($conn2, "select no_payment from list_payment where no_kbon = '$nk_esc' and status != 'Cancel' order by tgl_payment desc limit 1");
            $lp = $q ? mysqli_fetch_assoc($q) : null;
            if ($lp) {
                $nopay_esc = irEsc($conn2, $lp['no_payment']);
                $q = mysqli_query($conn2, "select a.no_bankout doc, b.bankout_date tgl, b.approve_by aby, b.create_by cby from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff = '$nopay_esc' and b.status != 'Cancel' order by b.bankout_date desc limit 1");
                $r = $q ? mysqli_fetch_assoc($q) : null;
                if (!$r) {
                    $q = mysqli_query($conn2, "select a.no_pco doc, b.tgl_pco tgl, b.approve_by aby, b.create_by cby from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where a.no_reff = '$nopay_esc' and b.status != 'Cancel' order by b.tgl_pco desc limit 1");
                    $r = $q ? mysqli_fetch_assoc($q) : null;
                }
                if (!$r) {
                    $q = mysqli_query($conn2, "select payment_ftr_id doc, tgl_pelunasan tgl, approve_by aby, create_user cby from payment_ftr where list_payment_id = '$nopay_esc' and status != 'Cancel' order by tgl_pelunasan desc limit 1");
                    $r = $q ? mysqli_fetch_assoc($q) : null;
                }
            }
        }
        if ($r) {
            $no_dokumen = $r['doc'];
            $tanggal = irFmtDate($r['tgl']);
            if (!empty($r['aby'])) { $label = 'Diapprove Oleh'; $oleh = $r['aby']; }
            else { $label = 'Dibuat Oleh'; $oleh = $r['cby']; }
        }
        break;
}

if ($no_dokumen === null) {
    echo json_encode(['ok' => false, 'message' => 'Data detail tidak ditemukan untuk tahap ini.']);
} else {
    echo json_encode([
        'ok' => true,
        'no_dokumen' => $no_dokumen,
        'tanggal' => $tanggal,
        'label' => $label,
        'oleh' => ($label !== null) ? (empty($oleh) ? '-' : $oleh) : null,
    ]);
}
