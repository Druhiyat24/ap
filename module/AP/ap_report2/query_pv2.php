<?php
/**
 * Shared Payment Voucher query builder.
 * Requires $conn2 to be available (include conn.php before this file).
 * Sets: $start_date, $end_date, $where, $sql
 */

$start_date = mysqli_real_escape_string($conn2, $_REQUEST['start_date'] ?? '');
$end_date   = mysqli_real_escape_string($conn2, $_REQUEST['end_date']   ?? '');
$nama_supp  = mysqli_real_escape_string($conn2, $_REQUEST['nama_supp']  ?? 'ALL');
$search     = isset($_POST['search']) && is_array($_POST['search'])
              ? ($_POST['search']['value'] ?? '') : '';

$where = "WHERE 1=1";
if ($nama_supp !== 'ALL') {
    $where .= " AND supplier = '$nama_supp'";
}
if ($search !== '') {
    $s = mysqli_real_escape_string($conn2, $search);
    $where .= " AND (
        supplier   LIKE '%$s%' OR
        no_kbon    LIKE '%$s%' OR
        curr       LIKE '%$s%' OR
        nama_coa   LIKE '%$s%' OR
        item_type1 LIKE '%$s%' OR
        item_type2 LIKE '%$s%' OR
        relasi     LIKE '%$s%'
    )";
}

$sql = "WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select nama_supp supplier, no_kbon, tgl_kbon, tgl_tempo duedate, curr, (subtotal + tax) total, rate, no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_payment_voucher),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' and no_journal not like '%/INS%' GROUP BY no_journal
UNION
select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join masterrate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') BETWEEN '$start_date' and '$end_date' 
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
UNION
select a.nama_supp, b.no_kbon_det, DATE_FORMAT(a.create_date,'%Y-%m-%d') tgl_kbon, b.tgl_tempo, a.curr, (b.dpp + b.ppn) total, IFNULL(d.rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from kontrabon_h a INNER JOIN kontrabon_h_installment_detail b on b.no_kbon = a.no_kbon INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa left join masterrate d on d.tanggal = DATE_FORMAT(a.create_date,'%Y-%m-%d') and d.curr = a.curr where DATE_FORMAT(a.create_date,'%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(a.create_date,'%Y-%m-%d') < '$start_date'
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select a.no_kbon, round(COALESCE(pph_idr,0) + COALESCE(potongan_pph,0),2) pph from kontrabon_h a INNER JOIN potongan b on b.no_kbon = a.no_kbon where COALESCE(pph_idr,0) > 0 GROUP BY a.no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2026-06-30' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_bank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between '$start_date' and '$end_date' and type_journal = 'Payment Voucher' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_bank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'Payment Voucher' and no_journal like '%BK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_cash as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between '$start_date' and '$end_date' and type_journal = 'Payment Voucher' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_cash_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'Payment Voucher' and no_journal like '%KKK%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_nonbank as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between '$start_date' and '$end_date' and type_journal = 'Payment Non Bank' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc
),

ded_nonbank_before as (select reff_doc, curr, sum(debit-credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and type_journal = 'Payment Non Bank' and no_journal like '%PAY%' and nama_coa like '%UTANG USAHA%' GROUP BY reff_doc),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2026-06-30' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_bank_before,0)) ded_bank_before, SUM(COALESCE(ded_bank,0)) ded_bank, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm, SUM(COALESCE(ded_cash,0)) ded_cash, SUM(COALESCE(ded_cash_before,0)) ded_cash_before, SUM(COALESCE(ded_nonbank,0)) ded_nonbank, SUM(COALESCE(ded_nonbank_before,0)) ded_nonbank_before FROM (
select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from potongan
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_bank_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, total ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_bank
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, total ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, total ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, total ded_cash, 0 ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, total ded_cash_before, 0 ded_nonbank, 0 ded_nonbank_before from ded_cash_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, total ded_nonbank, 0 ded_nonbank_before from ded_nonbank
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_bank_before, 0 ded_bank, 0 ded_gm_before, 0 ded_gm, 0 ded_cash, 0 ded_cash_before, 0 ded_nonbank, total ded_nonbank_before from ded_nonbank_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_bank_before,2),0) ded_bank_before, COALESCE(round(ded_bank,2),0) ded_bank, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm, COALESCE(round(ded_cash,2),0) ded_cash, COALESCE(round(ded_cash_before,2),0) ded_cash_before, COALESCE(round(ded_nonbank,2),0) ded_nonbank, COALESCE(round(ded_nonbank_before,2),0) ded_nonbank_before from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (if(ded_bank_before > 0,(ded_bank_before + pph),0) + ded_gm_before + if(ded_cash_before > 0,(ded_cash_before + pph),0) + if(ded_nonbank_before > 0,(ded_nonbank_before + pph),0))) saldo_awal, total_in, pph, uang_muka, potongan, if(ded_bank > 0,(ded_bank + pph),0) ded_bank, if(ded_cash > 0,(ded_cash + pph),0) ded_cash, if(ded_nonbank > 0,(ded_nonbank + pph),0) ded_nonbank, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in  + reverse_kontrabon + uang_muka + potongan - (ded_bank + ded_gm + ded_cash + ded_nonbank)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_bank, ded_cash, ded_nonbank, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +

CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi 
 $where
";
