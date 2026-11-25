<?php
// =========================================================================
// SQL "KARTU AR" - SALINAN dari aplikasi AR (ar_dev).
//
// Asalnya: ar_dev/application/models/Model_nag.php, fungsi
// cari_kartu_ar_new(), cabang $id_cus == 'all_customer'. Disalin apa adanya
// pada 24 November 2026, hanya DUA perubahan:
//   1. kolom profit_center ikut dibawa naik di dua lapis SELECT yang tadinya
//      membuangnya (lapis "select no_invoice, kode_customer, ..." dan lapis
//      terluar) - aslinya kolom itu dipakai di dalam tapi tidak dikeluarkan;
//   2. $dt_dari_alk / $dt_sampai_alk diganti penanda {DARI} / {SAMPAI}.
// Isi logikanya TIDAK diubah sama sekali.
//
// KENAPA DISALIN, BUKAN DIPANGGIL: ar_dev itu aplikasi CodeIgniter dengan
// kerangka kerja sendiri; memanggil model-nya dari ap_dev berarti menyalakan
// seluruh kerangka itu di tengah jalan. Databasenya sama (signalbit_erp),
// jadi menyalin SQL-nya jauh lebih sederhana dan tidak bisa mengganggu
// aplikasi AR.
//
// KALAU LAPORAN AR DI ar_dev DIUBAH, berkas ini TIDAK ikut berubah. Gejalanya:
// saldo awal di Preview FX-AR mulai beda dengan Kartu AR. Cara memeriksa:
// jalankan fungsi cari_kartu_ar_new() di ar_dev untuk tanggal yang sama lalu
// bandingkan totalnya per profit center.
//
// Dipakai hanya oleh fxar_functions.php -> fxArSaldoAwal().
// =========================================================================

// $dari   : batas bawah data, selalu '2022-05-01' seperti di laporan aslinya
// $sampai : tanggal acuan saldo ("per tanggal")
function fxArSqlKartuAr($dari, $sampai) {
    $sql = <<<'SQLAR'
SELECT a.*, ifnull(b.duedate_update,duedate) duedate_update from (select  profit_center, id_customer, customer, no_invoice, inv_date, shipp, duedate, top, curr, if(curr = 'IDR', 1, rate) rate, sal_awl, tambah, bayar,  total, eqv_idr,amt_aging_0,amt_aging_1,amt_aging_2,amt_aging_3,amt_aging_4,amt_aging_5,amt_aging_6,amt_aging_7, tot_aging, readydue, hasil_bln1, hasil_bln2, hasil_bln3, hasil_bln4, hasil_bln5, hasil_bln6, tot_aging tot_jatem from (select a.*, CASE WHEN jml_bln1 > 0 AND Date(duedate) >= '{SAMPAI}' THEN eqv_idr ELSE 0 END AS hasil_bln1,
        CASE WHEN jml_bln2 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln2,
        CASE WHEN jml_bln3 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln3,
        CASE WHEN jml_bln4 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln4,
        CASE WHEN jml_bln5 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln5,
        CASE WHEN jml_bln6 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln6,
        CASE WHEN total <= 0 THEN 0 WHEN Date(duedate) < '{SAMPAI}' THEN eqv_idr ELSE 0 END AS readydue,
        CASE WHEN total <= 0 THEN 0 ELSE eqv_idr END AS tot_aging2,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top <= 0 THEN eqv_idr ELSE 0 END AS amt_aging_0,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 0  AND diff_top <= 30  THEN eqv_idr ELSE 0 END AS amt_aging_1,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 30 AND diff_top <= 60  THEN eqv_idr ELSE 0 END AS amt_aging_2,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 60 AND diff_top <= 90  THEN eqv_idr ELSE 0 END AS amt_aging_3,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 90 AND diff_top <= 120 THEN eqv_idr ELSE 0 END AS amt_aging_4,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 120 AND diff_top <= 180 THEN eqv_idr ELSE 0 END AS amt_aging_5,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 180 AND diff_top <= 360 THEN eqv_idr ELSE 0 END AS amt_aging_6,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 360 THEN eqv_idr ELSE 0 END AS amt_aging_7,
        CASE WHEN total <= 0 THEN 0 ELSE eqv_idr END AS tot_aging
        from (select a.*, ((sal_awl + tambah) - bayar) total, ROUND(IF(curr = 'USD',((sal_awl + COALESCE(tambah,0)) - COALESCE(bayar,0)) * rate,((sal_awl + COALESCE(tambah,0)) - COALESCE(bayar,0))),2) eqv_idr from (select profit_center, no_invoice, kode_customer, customer, inv_date, id_customer, curr, top, amount1, duedate, bayar, bayar2, rate, shipp, diff_top, ready_due, jml_bln1, jml_bln2, jml_bln3, jml_bln4, jml_bln5, jml_bln6, IF(inv_date >= '{DARI}',0,COALESCE(amount1,0) - COALESCE(bayar2,0)) sal_awl, IF(inv_date >= '{DARI}',COALESCE(amount1,0) - COALESCE(bayar2,0),0) tambah from (SELECT profit_center, no_invoice,kode_customer, customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,coalesce(bayar,0) bayar,no_invoice2,bayar2,rate,shipp, diff_top,ready_due, IF(bln_due = fil_bln1 and thn_due = fil_thn1,amount1,'0') jml_bln1,IF(bln_due = fil_bln2 and thn_due = fil_thn2,amount1,'0') jml_bln2,IF(bln_due = fil_bln3 and thn_due = fil_thn3,amount1,'0') jml_bln3,IF(bln_due = fil_bln4 and thn_due = fil_thn4,amount1,'0') jml_bln4,IF(bln_due = fil_bln5 and thn_due = fil_thn5,amount1,'0') jml_bln5,IF(bln_due = fil_bln6 and thn_due = fil_thn6,amount1,'0') jml_bln6 from (SELECT profit_center, no_invoice,kode_customer, customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,bayar,no_invoice2,bayar2,rate,shipp, diff_top, bln_due, thn_due, LPAD(IF(fil_bln1 <= 12,fil_bln1,(fil_bln1 - 12)),2,0) fil_bln1,LPAD(IF(fil_bln2 <= 12,fil_bln2,(fil_bln2 - 12)),2,0) fil_bln2,LPAD(IF(fil_bln3 <= 12,fil_bln3,(fil_bln3 - 12)),2,0) fil_bln3,LPAD(IF(fil_bln4 <= 12,fil_bln4,(fil_bln4 - 12)),2,0) fil_bln4,LPAD(IF(fil_bln5 <= 12,fil_bln5,(fil_bln5 - 12)),2,0) fil_bln5, LPAD(IF(fil_bln6 <= 12,fil_bln6,(fil_bln6 - 12)),2,0) fil_bln6,LPAD(IF(fil_bln1 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn1,LPAD(IF(fil_bln2 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn2,LPAD(IF(fil_bln3 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn3, LPAD(IF(fil_bln4 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn4,LPAD(IF(fil_bln5 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn5, LPAD(IF(fil_bln6 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn6, ready_due from (SELECT profit_center, no_invoice,kode_customer,customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,CASE WHEN bayar > 0 AND profit_center = 'NAK' THEN amount1 ELSE bayar END bayar,no_invoice2, CASE WHEN bayar2 > 0 AND profit_center = 'NAK' THEN amount1 ELSE bayar2 END bayar2,rate,shipp,DATEDIFF('{SAMPAI}',duedate) diff_top, DATE_FORMAT(duedate,'%m') bln_due, DATE_FORMAT(duedate,'%Y') thn_due,DATE_FORMAT('{SAMPAI}','%m') fil_bln1,LPAD(DATE_FORMAT('{SAMPAI}','%m') + 1,2,0) fil_bln2, LPAD(DATE_FORMAT('{SAMPAI}','%m') + 2,2,0) fil_bln3,LPAD(DATE_FORMAT('{SAMPAI}','%m') + 3,2,0) fil_bln4,LPAD(DATE_FORMAT('{SAMPAI}','%m') + 4,2,0) fil_bln5,LPAD(DATE_FORMAT('{SAMPAI}','%m') + 5,2,0) fil_bln6, DATE_FORMAT('{SAMPAI}','%Y') fil_thn, IF(duedate <= '{SAMPAI}',amount1,0) ready_due from 
            (SELECT distinct a.profit_center, a.no_invoice AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,a.sj_date inv_date,a.sj_date tgl_inv, b.Id_Supplier AS id_customer, a.curr,f.top,
                FORMAT((d.grand_total), 2) AS amount, if(a.curr = 'IDR',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1,if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(a.sj_date, '%Y-%m-%d'), INTERVAL f.top DAY) ,DATE_ADD(h.kontrabon_date, INTERVAL f.top DAY)) AS duedate,a.shipp
                FROM  tbl_book_invoice AS a INNER JOIN 
                mastersupplier AS b ON a.id_customer = b.id_supplier INNER JOIN 
                tbl_type AS c ON a.id_type = c.id_type INNER JOIN
                tbl_invoice_pot AS d ON a.id = d.id_book_invoice INNER JOIN
                tbl_master_top AS f ON a.id_top = f.id left join 
                tbl_duedate AS h ON a.id = h.id_invoice
                where a.sj_date between '2022-05-01' and '{SAMPAI}' and a.profit_center = 'NAG'
                UNION
                SELECT distinct a.profit_center, a.no_invoice AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,a.sj_date inv_date,a.sj_date tgl_inv, b.Id_Supplier AS id_customer, a.curr,f.top,
                FORMAT((d.grand_total), 2) AS amount, if(a.curr = 'IDR',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1,if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(a.sj_date, '%Y-%m-%d'), INTERVAL f.top DAY) ,DATE_ADD(h.kontrabon_date, INTERVAL f.top DAY)) AS duedate,a.shipp
                FROM  tbl_book_invoice AS a INNER JOIN 
                mastersupplier AS b ON a.id_customer = b.id_supplier INNER JOIN 
                tbl_type AS c ON a.id_type = c.id_type INNER JOIN
                tbl_invoice_pot_knitting AS d ON a.id = d.id_book_invoice INNER JOIN
                tbl_master_top AS f ON a.id_top = f.id left join 
                tbl_duedate AS h ON a.id = h.id_invoice
                where a.sj_date between '2022-05-01' and '{SAMPAI}' and a.profit_center = 'NAK'
                UNION
                SELECT distinct 'NAG' profit_center, a.no_inv AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,DATE_FORMAT(e.sj_date, '%Y-%m-%d') AS inv_date,DATE_FORMAT(e.sj_date, '%Y-%m-%d') AS tgl_inv, b.Id_Supplier AS id_customer, e.curr,a.top,
                FORMAT((d.grand_total), 2) AS amount, if(e.curr = 'IDR',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1, if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(e.sj_date, '%Y-%m-%d'), INTERVAL a.top DAY) ,DATE_ADD(DATE_FORMAT(h.kontrabon_date, '%Y-%m-%d'), INTERVAL a.top DAY)) AS duedate,a.shipp
                FROM  tbl_invoice_nb AS a INNER JOIN 
                mastersupplier AS b ON a.customer = b.id_supplier INNER JOIN 
                tbl_invoice_nb_pot AS d ON a.no_inv = d.no_inv INNER JOIN
                tbl_invoice_nb_detail as e on a.no_inv=e.no_inv left JOIN 
                tbl_duedate AS h ON a.id = h.id_invoice left join
                saldoawal_ar as g on g.no_invoice = a.no_inv
                where g.no_invoice is null and a.status != 'CANCEL' and e.sj_date between '2022-05-01' and '{SAMPAI}'
                union                                                                     
                select 'NAG' profit_center, no_invoice, customer, UPPER(b.supplier_code) kode_customer, inv_date, sj_date as tgl_inv,id_customer, curr, top, FORMAT((grand_total), 2) AS amount, if(curr = 'IDR',round((grand_total),0),round((grand_total), 2)) AS amount1, due_date,shipp from saldoawal_ar a INNER JOIN mastersupplier AS b ON a.id_customer = b.id_supplier where no_invoice not like '%DN/%') inv LEFT JOIN
            (select a.no_ref as no_invoice1, sum(a.amount) as bayar from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != 'CANCEL' and b.tgl_alk between '{DARI}' and '{SAMPAI}' and a.total != '0' group by a.no_ref) byr on byr.no_invoice1 = inv.no_invoice LEFT JOIN
            (select a.no_ref as no_invoice2, sum(a.amount) as bayar2 from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != 'CANCEL' and b.tgl_alk < '{DARI}' and a.total != '0' group by a.no_ref) byr2 on byr2.no_invoice2 = inv.no_invoice JOIN
            (select IF((select id from tbl_tgl_tb where tgl_akhir = '{SAMPAI}') != '',(select rate from masterrate where tanggal = '{SAMPAI}' and v_codecurr = 'HARIAN' AND curr = 'USD'),(select rate from masterrate where tanggal = '{SAMPAI}' and v_codecurr = 'PAJAK' AND curr = 'USD')) rate) rt) a) a) a) a) a) a WHERE sal_awl > 0 OR tambah > 0 OR bayar > 0 OR total > 0) a LEFT JOIN (select no_invoice, max(duedate_update) duedate_update from tbl_duedate_update_det where status = 'Y' GROUP BY no_invoice) b on b.no_invoice = a.no_invoice 
SQLAR;
    return str_replace(array('{DARI}', '{SAMPAI}'), array($dari, $sampai), $sql);
}
