<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$rvs_number   = $_POST['rvs_number'] ?? '';
$approve_user = $_POST['approve_user'] ?? ($_SESSION['username'] ?? '');
$approve_date = date('Y-m-d H:i:s');

if (empty($rvs_number)) {
    echo 'Error: rvs_number is empty';
    exit;
}

$rvs_esc  = mysqli_real_escape_string($conn2, $rvs_number);
$user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "SELECT status FROM ap_reverse_h WHERE rvs_number = '$rvs_esc' LIMIT 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);
if (!$rowCheck || $rowCheck['status'] !== 'DRAFT') {
    echo 'Error: Status is not DRAFT or document not found';
    exit;
}

$upd = mysqli_query($conn2, "UPDATE ap_reverse_h SET status='APPROVED', approve_by='$user_esc', approve_date='$approve_date' WHERE rvs_number='$rvs_esc'");
if (!$upd) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

/**
 * Reset a PV back to draft status based on its type.
 * Mirrors the reverse of second_approve_*.php: resets BOTH the header AND the detail table.
 */
function resetPvToDraft($conn2, $type_pv, $no_kbon)
{
    $esc = mysqli_real_escape_string($conn2, $no_kbon);

    if ($type_pv === 'Regular') {
        // Detail table (kontrabon): checked by first_approve_kbon.php and second_approve_kbon.php
        mysqli_query($conn2,
            "UPDATE kontrabon SET status='draft',
             confirm_user=NULL, confirm_date=NULL,
             second_approve_by=NULL, second_approve_date=NULL
             WHERE no_kbon='$esc'"
        );
        // Header table (kontrabon_h): checked by pv_data_functions.php getDataRegular
        mysqli_query($conn2,
            "UPDATE kontrabon_h SET status='draft',
             confirm_user=NULL, confirm_date=NULL,
             second_approve_user=NULL, second_approve_date=NULL
             WHERE no_kbon='$esc'"
        );
        return;
    }

    if ($type_pv === 'Installment') {
        // no_kbon here is the cicilan's no_kbon_det
        // Reset the cicilan row: checked by first_approve_installment.php and second_approve_installment.php
        mysqli_query($conn2,
            "UPDATE kontrabon_h_installment_detail
             SET status='draft',
             first_approve_user=NULL, first_approve_date=NULL,
             second_approve_user=NULL, second_approve_date=NULL
             WHERE no_kbon_det='$esc'"
        );
        // Get parent no_kbon
        $sqlP = mysqli_query($conn2,
            "SELECT no_kbon FROM kontrabon_h_installment_detail WHERE no_kbon_det='$esc' LIMIT 1"
        );
        $rowP = mysqli_fetch_assoc($sqlP);
        if (!empty($rowP['no_kbon'])) {
            $pk = mysqli_real_escape_string($conn2, $rowP['no_kbon']);
            // Reset parent kontrabon_h: status no longer valid when any cicilan is draft
            mysqli_query($conn2,
                "UPDATE kontrabon_h SET status='draft',
                 confirm_user=NULL, confirm_date=NULL,
                 second_approve_user=NULL, second_approve_date=NULL
                 WHERE no_kbon='$pk'"
            );
        }
        return;
    }

    if ($type_pv === 'DP') {
        // Detail table (kontrabon_dp): checked by second_approve_dp.php
        mysqli_query($conn2,
            "UPDATE kontrabon_dp SET status='draft',
             confirm_user=NULL, confirm_date=NULL
             WHERE no_kbon='$esc'"
        );
        // Header table (kontrabon_h_dp)
        mysqli_query($conn2,
            "UPDATE kontrabon_h_dp SET status='draft',
             confirm_user=NULL, confirm_date=NULL,
             second_approve_user=NULL, second_approve_date=NULL
             WHERE no_kbon='$esc'"
        );
        return;
    }

    if ($type_pv === 'CBD') {
        // Detail table (kontrabon_cbd): checked by second_approve_cbd.php
        mysqli_query($conn2,
            "UPDATE kontrabon_cbd SET status='draft',
             confirm_user=NULL, confirm_date=NULL
             WHERE no_kbon='$esc'"
        );
        // Header table (kontrabon_h_cbd)
        mysqli_query($conn2,
            "UPDATE kontrabon_h_cbd SET status='draft',
             confirm_user=NULL, confirm_date=NULL,
             second_approve_user=NULL, second_approve_date=NULL
             WHERE no_kbon='$esc'"
        );
        return;
    }

    if ($type_pv === 'Biaya') {
        mysqli_query($conn2,
            "UPDATE tbl_pv_h SET status='draft' WHERE no_pv='$esc'"
        );
        return;
    }
}

// Process each PV in ap_reverse_det
$sqlDet = mysqli_query($conn2,
    "SELECT doc_number, type_pv FROM ap_reverse_det WHERE rvs_number = '$rvs_esc' AND status = 'Y'"
);

while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
    $no_kbon = $rowDet['doc_number'];
    $type_pv = $rowDet['type_pv'];
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);

    // Check if PV is in any active PVL, cancel that PVL and reset all its PVs
    $sqlPvl = mysqli_query($conn2,
        "SELECT pvld.pl_number
         FROM pv_payment_voucher_list_det pvld
         INNER JOIN pv_payment_voucher_list_h pvlh ON pvlh.pl_number = pvld.pl_number
         WHERE pvld.no_kbon = '$no_kbon_esc'
         AND pvld.status != 'Cancel'
         AND pvlh.status != 'Cancel'"
    );

    if ($sqlPvl) {
        while ($rowPvl = mysqli_fetch_assoc($sqlPvl)) {
            $pl_esc = mysqli_real_escape_string($conn2, $rowPvl['pl_number']);

            mysqli_query($conn2,
                "UPDATE pv_payment_voucher_list_h SET status='Cancel' WHERE pl_number='$pl_esc'"
            );

            $sqlPvlDet = mysqli_query($conn2,
                "SELECT type_pv, no_kbon FROM pv_payment_voucher_list_det
                 WHERE pl_number='$pl_esc' AND status != 'Cancel'"
            );
            if ($sqlPvlDet) {
                while ($rowPvDet = mysqli_fetch_assoc($sqlPvlDet)) {
                    updateStatusPvl($conn2, $rowPvDet['type_pv'], $rowPvDet['no_kbon'], null);
                }
            }

            mysqli_query($conn2,
                "UPDATE pv_payment_voucher_list_det SET status='Cancel' WHERE pl_number='$pl_esc'"
            );
        }
    }

    // Reset PV status to draft (header + detail tables)
    resetPvToDraft($conn2, $type_pv, $no_kbon);

    // Reset status_pvl in source table
    updateStatusPvl($conn2, $type_pv, $no_kbon, null);
}

echo 'OK';
mysqli_close($conn2);
