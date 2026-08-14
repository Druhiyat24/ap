<?php
include '../../../conn/conn.php';
include '../pv_data_functions.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

mysqli_begin_transaction($conn2);

// getAlreadyPaidFor() ada di pv_data_functions.php - menghitung outstanding
// lintas channel (Bank-Out + Petty Cash Out), bukan cuma Bank-Out saja.

try {

    $json = $_POST['data'] ?? '';
    $data = json_decode($json, true);

    if(!$data){
        throw new Exception("Data tidak valid");
    }

    $header        = $data['header'];
    $detail_pv     = $data['detail_pv'];
    $detail_adjust = $data['detail_adjust'];

    // =========================
    // HEADER VALUE
    // =========================
    $doc_date        = date('Y-m-d', strtotime($header['tgl']));
    $supp       = mysqli_real_escape_string($conn2, $header['supp']);
    $pc_bank    = mysqli_real_escape_string($conn2, $header['pc_header'] ?? '');
    $akun    = mysqli_real_escape_string($conn2, $header['account'] ?? '');
    $bank       = mysqli_real_escape_string($conn2, $header['bank'] ?? '');
    $curr   = mysqli_real_escape_string($conn2, $header['currency'] ?? '');
    $amount     = (float) ($header['amount'] ?? 0);
    $rate       = (float) ($header['rate'] ?? 0);
    $eqv        = (float) ($header['eqv'] ?? 0);
    $desc       = mysqli_real_escape_string($conn2, trim($header['desc'] ?? ''));
    $cash_flow  = $header['cash_flow'] ?? '';

    if ($akun === '') {
        throw new Exception('Account tidak boleh kosong.');
    }

    if ($desc === '') {
        throw new Exception('Description tidak boleh kosong.');
    }

    if ($cash_flow === '') {
        throw new Exception('Cash Flow Category tidak boleh kosong.');
    }
    $cash_flow = (int) $cash_flow;

    if ($amount <= 0) {
        throw new Exception('Amount tidak boleh kosong.');
    }

    if (empty($detail_pv)) {
        throw new Exception('Pilih minimal 1 Payment Voucher.');
    }

    // =========================
    // COA WAJIB CC (sama seperti get_coa_wajib_cc.php)
    // =========================
    $coaWajibCC = [];
    $sqlWajibCcAll = q($conn1, "select no_coa from mastercoa_v2 where support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y'");
    while ($rowWajib = mysqli_fetch_assoc($sqlWajibCcAll)) {
        $coaWajibCC[] = $rowWajib['no_coa'];
    }

    // =========================
    // FORMAT PREFIX
    // =========================
    $kode_bank = mysqli_real_escape_string($conn2, $header['kode_bank'] ?? '');
    $user = $_SESSION['username'] ?? 'system';

    $bulan = date('m',strtotime($doc_date));
    $tahun = date('y',strtotime($doc_date));
    $status = "Draft";
    $create_date = date("Y-m-d H:i:s");

    $prefix = "BK/".$kode_bank."/".$pc_bank."/".$bulan.$tahun;

    // =========================
    // AMBIL NOMOR (LOCK)
    // =========================
    $sql = q($conn2,"
        SELECT MAX(CAST(RIGHT(no_bankout,5) AS UNSIGNED)) AS max_urut
        FROM b_bankout_h
        WHERE no_bankout LIKE '$prefix%'
        FOR UPDATE
        ");

    $row = mysqli_fetch_assoc($sql);
    $maxHeader = (int) ($row['max_urut'] ?? 0);

    // Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
    // walau no_bankout di header sudah berubah (akun diganti saat edit),
    // jadi nomor lama tidak boleh dipakai ulang untuk dokumen baru.
    $sqlJ = q($conn2,"
        SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
        FROM tbl_list_journal
        WHERE no_journal LIKE '$prefix%'
        FOR UPDATE
        ");

    $rowJ = mysqli_fetch_assoc($sqlJ);
    $maxJournal = (int) ($rowJ['max_urut'] ?? 0);

    $urutan = max($maxHeader, $maxJournal) + 1;

    $doc_num = $prefix."/".sprintf("%05d",$urutan);

    $sqlcoa1 = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
    $rowcoa1 = mysqli_fetch_array($sqlcoa1);
    $no_coa1 = $rowcoa1['no_coa'] ?? null;
    $nama_coa1 = $rowcoa1['nama_coa'] ?? null;

    if (!$no_coa1) {
        throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
    }

    // =========================
    // INSERT HEADER
    // =========================
    q($conn2,"
        INSERT INTO b_bankout_h
        (
        no_bankout, bankout_date, reff_doc, nama_supp, akun, bank, curr, amount, outstanding, rate, eqv_idr, deskripsi, status, create_by, create_date, stat_bi, id_cash_flow
        )
        VALUES
        ('$doc_num', '$doc_date', 'Payment Voucher', '$supp', '$akun', '$bank', '$curr', '$amount', '$amount', '$rate', '$eqv', '$desc', '$status', '$user', '$create_date', 'N', '$cash_flow')
        ");


    q($conn2,"
        INSERT INTO b_reportbank
        (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status,id_cash_flow)
        VALUES
        ('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status', '$cash_flow')
        ");

    q($conn2,"
        INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES
        ('$doc_num', '$doc_date', 'Payment Voucher', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
        ");

    // =========================
    // INSERT DETAIL PV
    // =========================
    foreach($detail_pv as $pv){

        $no_pv  = mysqli_real_escape_string($conn2, $pv['no_pv']);
        $amountPv = (float) $pv['amount'];
        $pc     = mysqli_real_escape_string($conn2, $pv['pc']);
        $type_pv = !empty($pv['type_pv']) ? $pv['type_pv'] : 'Biaya';

        if ($type_pv === 'Biaya') {

        $sql_pv = q($conn2,"WITH
            total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.no_pv = '$no_pv' and d.kode_pc = '$pc' group by a.no_pv, b.profit_center),

            total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and a.type_pv = 'Biaya' GROUP BY no_reff,a.profit_center)

            select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code, IFNULL(c.rate,1) rate from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.pv_date");
        $row_pv = mysqli_fetch_array($sql_pv);

        if (!$row_pv) {
            throw new Exception('Payment Voucher "'.$no_pv.'" tidak ditemukan.');
        }

        $pv_pc = mysqli_real_escape_string($conn2, $row_pv['profit_center']);
        $pv_number = mysqli_real_escape_string($conn2, $row_pv['no_pv']);
        $pv_date = $row_pv['pv_date'];
        $pv_duedate = $row_pv['due_date'];
        $pv_curr = mysqli_real_escape_string($conn2, $row_pv['curr']);
        $pv_sub = (float) $row_pv['subtotal'];
        $pv_ppn = (float) $row_pv['ppn'];
        $pv_pph = (float) $row_pv['pph'];
        $pv_total = (float) $row_pv['total'];
        $pv_rate = (float) $row_pv['rate'];

        q($conn2,"
            INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center, type_pv)
            VALUES
            ('$doc_num', '$pv_number', '$pv_date', '$pv_duedate', '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr', '$amountPv', '$rate', '$amountPv', '$pv_pc', 'Biaya')
            ");

        q($conn2,"insert into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '' id, '$doc_num' no_journal, '$doc_date' tgl_journal, type_journal, coa, nama_coa, no_cc, COALESCE(cc_name,'-') cc_name, no_pv no_reff, pv_date reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, a.curr, IF(rate is null,1,rate) rate, debit, credit, round(debit * IF(rate is null,1,rate),4) debit_idr, round(credit * IF(rate is null,1,rate),4) credit_idr, 'Draft' status, deskripsi, '$user' create_by, CURRENT_TIMESTAMP() create_date, '' approve_by, '' approve_date, '' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from
            (select a.id, 'Payment Voucher' type_journal, d.no_coa coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date, a.faktur_pajak, a.tgl_faktur_pajak, '-' buyer, '-' no_ws, h.curr, amount debit, ded_add credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mastercoa_v2 d on d.no_coa = a.coa where a.no_pv = '$pv_number' and a.profit_center = '$pv_pc'
            UNION
            select a.id, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date, a.faktur_pajak, a.tgl_faktur_pajak, '-' buyer, '-' no_ws, h.curr, (ded_add * a.pph/100) debit, (amount * a.pph/100) credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_pph where a.no_pv = '$pv_number' and a.profit_center = '$pv_pc'
            UNION
            select a.id, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,no_pv, pv_date, faktur_pajak, tgl_faktur_pajak, '-' buyer, '-' no_ws, curr, (amount * ppn/100) debit, (ded_add * a.ppn/100) credit, deskripsi, a.profit_center from (select id,no_pv, pv_date, a.profit_center, coa, no_cc, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, deskripsi, curr, ded_add, amount, pph, IF(per_ppn = 0,ppn,per_ppn) ppn, IF(per_ppn = 0,id_ppn,id_per_ppn) id_ppn from (select a.*,b.per_ppn, b.pv_date, b.curr, CASE WHEN b.per_ppn BETWEEN 1.09 AND 1.11 THEN 20 WHEN b.per_ppn = 11 THEN 1 WHEN b.per_ppn BETWEEN 1.19 AND 1.21 THEN 23 ELSE 0 END AS id_per_ppn from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv where a.no_pv = '$pv_number' and a.profit_center = '$pv_pc') a) a left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_ppn
        ) a LEFT JOIN (select tanggal, curr, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.pv_date and b.curr = a.curr order by a.id, credit asc");

        } else {

            // =========================
            // REGULAR / INSTALLMENT / DP / CBD
            // =========================
            // Berbeda dari Biaya: kontrabon-family sudah dibukukan sebagai
            // hutang (kartu_hutang) saat approval, jadi jurnal di sini cuma
            // satu baris pelunasan: Debit COA Hutang. Untuk Regular/Installment
            // dari kontrabon_h.no_coa; untuk DP/CBD dari kontrabon_h_dp/_cbd.no_coa
            // (hasil mapping Item Type + area supplier lewat pv_mapping_jurnal_dp,
            // diisi saat PV DP/CBD dibuat - lihat insertkbon_h_pv_dp.php/insertkbon_h_pv_cbd.php).
            $no_kbon_esc = mysqli_real_escape_string($conn2, $no_pv);
            $filtersOne = [
                'nama_supp'   => 'ALL',
                'status'      => 'ALL',
                'filter_date' => 'tgl_kbon',
                'start_date'  => '',
                'end_date'    => '',
            ];

            $pv_coa = '-';
            $pv_coa_nama = '-';
            $rowOne = null;

            if ($type_pv === 'Regular') {
                $filtersOne['no_kbon'] = $no_pv;
                $rows = getDataRegular($conn1, $conn2, $filtersOne, '1', '1', '');
                $rowOne = $rows[0] ?? null;
                if ($rowOne) {
                    $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
                    $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
                }
            } elseif ($type_pv === 'Installment') {
                $filtersOne['no_kbon_det'] = $no_pv;
                $rows = getDataInstallment($conn1, $conn2, $filtersOne, '1', '1', '');
                $rowOne = $rows[0] ?? null;
                if ($rowOne) {
                    $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
                    $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
                }
            } elseif ($type_pv === 'DP') {
                $filtersOne['no_kbon'] = $no_pv;
                $rows = getDataDp($conn1, $conn2, $filtersOne, '1', '1', '');
                $rowOne = $rows[0] ?? null;
                if ($rowOne) {
                    $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
                    $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
                }
            } elseif ($type_pv === 'CBD') {
                $filtersOne['no_kbon'] = $no_pv;
                $rows = getDataCbd($conn1, $conn2, $filtersOne, '1', '1', '');
                $rowOne = $rows[0] ?? null;
                if ($rowOne) {
                    $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
                    $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
                }
            } elseif ($type_pv === 'SaldoAwal') {
                $filtersOne['no_kbon'] = $no_pv;
                $rows = getDataSaldoAwal($conn2, $filtersOne);
                $rowOne = $rows[0] ?? null;
                if ($rowOne) {
                    $pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
                    $pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
                }
            }

            if (!$rowOne) {
                throw new Exception("Data $type_pv untuk $no_pv tidak ditemukan");
            }

            $pv_pc = $rowOne['profit_center'];
            $pv_date = $rowOne['tgl_kbon_raw'];
            $pv_duedate = $rowOne['tgl_tempo_raw'];
            $pv_curr = $rowOne['curr'];
            $pv_sub = $rowOne['subtotal_raw'];
            $pv_ppn = $rowOne['tax_raw'];
            $pv_pph = $rowOne['pph_raw'];
            if ($pv_curr === 'IDR') {
                $pv_rate = 1;
            } else {
                $sqlPvRate = q($conn2, "SELECT rate FROM ap_masterrate WHERE v_codecurr = 'PAJAK' AND curr = '" . mysqli_real_escape_string($conn2, $pv_curr) . "' AND tanggal = '" . mysqli_real_escape_string($conn2, $doc_date) . "' LIMIT 1");
                $rowPvRate = mysqli_fetch_assoc($sqlPvRate);
                $pv_rate = !empty($rowPvRate['rate']) ? (float)$rowPvRate['rate'] : (float)$rate;
            }

            $alreadyPaid = getAlreadyPaidFor($conn2, $type_pv, $no_pv);
            $pv_total = (float) $rowOne['total_raw'] - $alreadyPaid;

            $pv_pc_esc = mysqli_real_escape_string($conn2, $pv_pc);
            $pv_date_sql = !empty($pv_date) ? "'" . mysqli_real_escape_string($conn2, $pv_date) . "'" : 'NULL';
            $pv_duedate_sql = !empty($pv_duedate) ? "'" . mysqli_real_escape_string($conn2, $pv_duedate) . "'" : 'NULL';
            $pv_curr_esc = mysqli_real_escape_string($conn2, $pv_curr);
            $pv_coa_esc = mysqli_real_escape_string($conn2, $pv_coa);
            $pv_coa_nama_esc = mysqli_real_escape_string($conn2, $pv_coa_nama);
            $type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);

            // PPH split: Regular, Installment & SaldoAwal - jurnal PPH dipisah
            // baris (pola save_lp.php). SaldoAwal no_kbon-nya sama dengan
            // no_kbon di tabel kontrabon (ap_saldo_payment_voucher adalah
            // snapshot dari kontrabon yang sama), jadi lookup COA PPH di
            // bawah (cabang else, query ke tabel kontrabon) tetap valid.
            $bayar_pph = in_array($type_pv, ['Regular', 'Installment', 'SaldoAwal'], true)
                ? min($pv_pph, (float)$amountPv) : 0;
            $total_dppnya = $amountPv + $bayar_pph;
            $debit_idr = round($total_dppnya * $pv_rate, 4);

            q($conn2, "
                INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center, type_pv)
                VALUES
                ('$doc_num', '$no_kbon_esc', $pv_date_sql, $pv_duedate_sql, '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr_esc', '$amountPv', '$pv_rate', '$amountPv', '$pv_pc_esc', '$type_pv_esc')
                ");

            q($conn2, "
                INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
                VALUES
                ('$doc_num', '$doc_date', 'Payment Voucher', '$pv_coa_esc', '$pv_coa_nama_esc', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '$total_dppnya', '0', '$debit_idr', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc_esc')
                ");

            if ($bayar_pph > 0) {
                if ($type_pv === 'Installment') {
                    $sqlpph = q($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(k.idtax) FROM kontrabon k INNER JOIN kontrabon_h_installment_detail d ON d.no_kbon = k.no_kbon WHERE d.no_kbon_det = '$no_kbon_esc')");
                } else {
                    $sqlpph = q($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(idtax) FROM kontrabon WHERE no_kbon = '$no_kbon_esc')");
                }
                $rowpph = mysqli_fetch_assoc($sqlpph);
                $no_coa_pph  = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
                $nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');
                $pph_idr = round($bayar_pph * $pv_rate, 4);

                if (!empty($no_coa_pph)) {
                    q($conn2, "
                        INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
                        VALUES
                        ('$doc_num', '$doc_date', 'Payment Voucher', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '0', '$bayar_pph', '0', '$pph_idr', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc_esc')
                        ");
                }
            }
        }
    }

    // =========================
    // INSERT DETAIL ADJUST (bulk)
    // =========================
    $adjDetValues = [];
    $adjJournalValues = [];

    foreach($detail_adjust as $i => $rowAdj){

        $coa   = trim($rowAdj['coa'] ?? '');
        $pc    = trim($rowAdj['pc'] ?? '');
        $cc    = trim($rowAdj['cc'] ?? '') ?: '-';
        $debit = (float) ($rowAdj['debit'] ?? 0);
        $credit= (float) ($rowAdj['credit'] ?? 0);
        // Baris adjustment boleh IDR atau USD - USD dikonversi pakai rate header
        // (sama seperti PV/PPh di atas), IDR tetap rate 1 apa adanya.
        $rowCurr = strtoupper(trim($rowAdj['curr'] ?? 'IDR')) ?: 'IDR';
        $rowRate = ($rowCurr === 'IDR') ? 1 : $rate;
        $debit_idr_adj = round($debit * $rowRate, 4);
        $credit_idr_adj = round($credit * $rowRate, 4);
        $rowCurrEsc = mysqli_real_escape_string($conn2, $rowCurr);
        $descAdjRaw = trim($rowAdj['desc'] ?? '');
        $desc2 = $descAdjRaw !== '' ? mysqli_real_escape_string($conn2, $descAdjRaw) : $desc;
        $reff  = mysqli_real_escape_string($conn2, $rowAdj['reff_doc'] ?? '-');
        $reff_date = !empty($rowAdj['reff_date']) ? date('Y-m-d', strtotime($rowAdj['reff_date'])) : $doc_date;

        if (!$coa || $coa === '-') {
            throw new Exception('Adjust baris ke-'.($i + 1).': COA wajib diisi.');
        }

        if (!$pc || $pc === '-') {
            throw new Exception('Adjust baris ke-'.($i + 1).': Profit Center wajib diisi.');
        }

        if (in_array($coa, $coaWajibCC, true) && ($cc === '-' || $cc === '')) {
            throw new Exception('Adjust baris ke-'.($i + 1).': COA '.$coa.' wajib isi Cost Center.');
        }

        if ($debit == 0 && $credit == 0) {
            throw new Exception('Adjust baris ke-'.($i + 1).': Debit/Credit harus diisi.');
        }

        $coaEsc = mysqli_real_escape_string($conn2, $coa);
        $pcEsc = mysqli_real_escape_string($conn2, $pc);
        $ccEsc = mysqli_real_escape_string($conn2, $cc);

        $sql_coadet = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where no_coa = '$coaEsc'");
        $row_coadet = mysqli_fetch_array($sql_coadet);
        $nama_coa_adj = $row_coadet['nama_coa'] ?? null;

        if (!$nama_coa_adj) {
            throw new Exception('Adjust baris ke-'.($i + 1).': COA "'.$coa.'" tidak ditemukan.');
        }

        $nama_cc = null;
        if ($ccEsc !== '-' && $ccEsc !== '') {
            $sqlcc = q($conn2,"select cc_name from b_master_cc where no_cc = '$ccEsc'");
            $rowcc = mysqli_fetch_array($sqlcc);
            $nama_cc = $rowcc['cc_name'] ?? null;
        }
        $nama_cc = mysqli_real_escape_string($conn2, $nama_cc ?: '-');

        $adjDetValues[] = "('$doc_num', '$coaEsc', '$ccEsc', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$rowCurrEsc', '$pcEsc')";

        $adjJournalValues[] = "('$doc_num', '$doc_date', 'Payment Voucher', '$coaEsc', '$nama_coa_adj', '$ccEsc', '$nama_cc', '$reff', '$reff_date', '-', '-', '$rowCurrEsc', '$rowRate', '$debit', '$credit', '$debit_idr_adj', '$credit_idr_adj', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pcEsc')";
    }

    if (!empty($adjDetValues)) {
        q($conn2, "INSERT INTO b_bankout_adj_det
            (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit,curr, profit_center)
            VALUES " . implode(',', $adjDetValues));

        q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES " . implode(',', $adjJournalValues));
    }

    // =========================
    // COMMIT
    // =========================
    mysqli_commit($conn2);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Data berhasil disimpan. No: '.$doc_num
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
