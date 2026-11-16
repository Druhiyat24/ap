<html>
<head>
    <title>Export Data General Ledger </title>
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
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Other Receivable Material Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>OTHER RECEIVABLE MATERIAL REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">BPB Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Buyer</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Req</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Req Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No DN</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">DN Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Rate</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Addition</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Addition Return</th>
            <th colspan="5" style="text-align: center;vertical-align: middle;">Deduction Return</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Deduction (ALK)</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Forex Gain / (Loss)</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <tr>
            <?php
            $sub = '<th style="text-align: center;">Currency</th>'
                 . '<th style="text-align: center;">Amount OCY</th>'
                 . '<th style="text-align: center;">Eqv IDR</th>';
            $subRI = '<th style="text-align: center;">Doc No</th>'
                   . '<th style="text-align: center;">Date</th>' . $sub;
            echo str_repeat($sub, 3) . $subRI . str_repeat($sub, 4);
            ?>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        // ---------------------------------------------------------------------------
        // PENYAJIAN SELISIH KURS 12 BPB (permintaan user, 16 Sep 2026).
        //
        // Ke-12 BPB ini BPB USD: nilai awalnya dibukukan pakai kurs PAJAK tanggal BPB,
        // sedangkan alokasi DN-nya (0043/ALK/NAG/0826, 28-Ags-2026) pakai kurs 17.796.
        // Sisanya = jumlah USD x selisih kurs, dan sudah ditutup akuntansi lewat jurnal
        // reklas GM/NAG/0826/00197 (31-Ags-2026, total 472.818,49).
        //
        // Karena sisanya memang selisih kurs, user minta angka itu TAMPIL di kolom
        // Forex Gain/(Loss), bukan di Deduction (GM), DAN jurnal GM-nya akan dibatalkan.
        // Maka forex 12 BPB ini dihitung SENDIRI dari SELISIH KURS:
        //     jumlah USD x (kurs PAJAK tgl BPB - kurs alokasi)
        // tanpa mengambil angka dari jurnal GM, karena jurnal itu akan dibatalkan.
        //
        // Jurnal GM SENGAJA TETAP DITAMPILKAN apa adanya di kolom Deduction (GM)
        // (permintaan user 16 Sep 2026) supaya beda sebelum & sesudah cancel kelihatan:
        //   - SELAMA GM belum dibatalkan: nilainya terhitung dua kali (GM + Forex),
        //     saldo akhir 12 baris ini tampil MINUS 472.818,49. Ini DISENGAJA sbg
        //     penanda bahwa jurnal GM masih perlu dibatalkan - bukan bug.
        //   - SESUDAH GM dibatalkan: Deduction (GM) jadi 0, tinggal kolom Forex,
        //     saldo akhir Agustus 2026 = 0 dan saldo awal September 2026 = 0.
        // Pengecualian ini SENGAJA dibatasi ke 12 nomor di bawah; ratusan alokasi BPB
        // USD lain yang kolom Forex-nya juga 0 DIBIARKAN apa adanya (belum diminta).
        //
        // Kolom ded_fgl yang umum (tot_dn - total_alk2) memang selalu 0 untuk BPB USD -
        // kedua sisinya sama-sama dikali kurs alokasi. Rumus itu dibuat untuk kasus
        // sebaliknya (BPB IDR + alokasi valas) dan sengaja dibiarkan apa adanya.
        //
        // Catatan: query 1.34.05 ini disalin utuh di other_receivable_report.php dan
        // ekspor_or_report_material.php - kalau salah satu diubah, ubah dua-duanya.
        // ---------------------------------------------------------------------------
        $OR_FX_GM  = 'GM/NAG/0826/00197';   // jurnal reklas yang digantikan perhitungan di bawah
        $OR_FX_ARR = array(
           'GACC/IN/0626/03136','GACC/IN/0626/03144','GACC/IN/0626/03357',
           'GACC/IN/0626/03445','GACC/IN/0626/03446','GACC/IN/0626/03518',
           'GACC/IN/0626/03522','GACC/IN/0626/03572','GACC/IN/0626/03573',
           'GACC/IN/0726/03977','GACC/IN/0726/04267','GACC/IN/0726/04388',
           // Ditambahkan 8 Okt 2026 - alokasi 0033/ALK/NAG/0926 (kurs 17.863)
           // vs kurs pajak tgl BPB 17.937. Dicek ke produksi: TIDAK ADA jurnal
           // GM di 1.34.05 untuk keenamnya, jadi tidak ada risiko dobel hitung.
           'GACC/IN/0726/04456','GACC/IN/0726/04475','GACC/IN/0726/04502',
           'GACC/IN/0726/04503','GACC/IN/0726/04504','GACC/IN/0726/04508'
        );
        /* Versi teks utk dipakai di dalam SQL. Satu daftar, dua bentuk -
           supaya penambahan BPB baru cukup di satu tempat. */
        $OR_FX_BPB = "'" . implode("','", $OR_FX_ARR) . "'";
                /* ---------------------------------------------------------
                   BARIS RETUR DIPISAH JADI DUA KOLOM (13 Nov 2026, pemakai).

                   Selama ini BPB bernomor .../RI/... ikut masuk kolom
                   Addition. Sekarang dipisah: nilai RI-nya ke kolom
                   'Deduction Return', dan pasangan RO-nya ke kolom
                   'Addition Return' bertanda MINUS.

                   PASANGAN RI-RO lewat NOMOR SURAT JALAN, bukan nomor
                   dokumen: bpb.bppbno dicocokkan ke bppb.bppbno, lalu
                   bppb.bppbno_int itulah nomor RO-nya (cara ini diberi tahu
                   pemakai). Tabelnya bppb, BUKAN bppb_new - di bppb_new
                   pasangannya tidak ketemu satu pun.

                   NILAI PASANGANNYA SAMA PERSIS dlm mata uang aslinya,
                   jadi kedua kolom itu SALING MENIADAKAN. Memang begitu
                   kenyataannya: barang keluar lalu masuk lagi dgn nilai
                   yang sama.

                   KURSNYA PAKAI TANGGAL DOKUMENNYA MASING-MASING
                   (permintaan pemakai 13 Nov 2026): RI pakai kurs pajak
                   tanggal RI, RO pakai kurs pajak tanggal RO. Utk dokumen
                   USD yang tanggalnya beda, rupiahnya jadi tidak sama lagi
                   - SELISIHNYA MASUK KE KOLOM FOREX GAIN/(LOSS), jadi
                   Ending Balance-nya tetap nol. Dicek ke produksi Okt-Nov
                   2026: 18 pasang, total selisih 8.153,29.

                   BATAS 1 OKT 2026 (pemakai: 'September ke belakang jangan
                   diubah'). Penandanya TANGGAL BPB-nya, bukan periode yang
                   sedang dilihat, supaya laporan bulan lama tetap sama
                   persis - termasuk waktu dokumennya terbawa sbg saldo awal
                   periode berikutnya.
                   --------------------------------------------------------- */
                $eRetur = "(a.bpbno_int LIKE '%/RI/%' AND a.bpbdate >= '2026-10-01')";

                /* Baris retur yang jatuh di SALDO AWAL (tanggalnya sebelum
                   periode). Hanya baris ini yang pasangan RO-nya dipotong
                   lewat saldo_awal - baris periode berjalan sudah ditangani
                   dua kolom barunya, kalau ikut dipotong di sini RO-nya
                   terhitung DUA KALI. */
                $eReturAwal = "($eRetur AND a.bpbdate < '$start_date')";

                /* RI yang terjadi DI periode ini - dibedakan dari yang cuma
                   terbawa sbg saldo awal, supaya kolom returnya tidak terisi
                   lagi di periode-periode berikutnya. */
                $eReturIni = "($eRetur AND a.bpbdate >= '$start_date')";

                /* Nilai RO pasangan dlm rupiah. Pengkursannya dikerjakan di
                   dalam sambungannya, pakai KURS TANGGAL RO SENDIRI. */
                $eRO = "ROUND(COALESCE(ro.idr,0), 2)";

                /* Nilai RI-nya. Satu baris cuma mengisi salah satu kolom:
                   kalau RI-nya di periode ini isinya 'tambah', kalau terbawa
                   dari periode lalu isinya 'saldo_awal'. */
                $eNilaiRI = "ROUND(COALESCE(saldo_awal,0) + COALESCE(tambah,0), 2)";

                /* SELISIH KURS RI vs RO -> kolom Forex Gain/(Loss). Hanya
                   kalau pasangan RO-nya KETEMU; kalau tidak ketemu, RI-nya
                   dibiarkan menggantung supaya kelihatan - jangan disapu
                   diam-diam ke forex. */
                $eSelisih = "IF(ro.bppbno IS NULL, 0, ROUND($eNilaiRI - $eRO, 2))";

                $sql = mysqli_query($conn2,"SELECT pono, bpbno_int, bpbdate, supplier, buyer,no_req, tgl_req, no_dn, tgl_dn, curr, COALESCE(nilai_asli,0) nilai_asli, COALESCE(rate_doc,1) rate_doc, IF($eRetur, COALESCE(ro.asli,0), 0) nilai_asli_ro, IF($eRetur, COALESCE(ro.rate_ro,0), 0) rate_ro, IF($eRetur, COALESCE(ro.no_ro,'-'), '-') no_ro, IF($eRetur, COALESCE(ro.tgl_ro,'-'), '-') tgl_ro, round(IF($eReturAwal, COALESCE(saldo_awal,0) - $eRO - $eSelisih, COALESCE(saldo_awal,0)) - COALESCE(ded_alk_bfr,0) - COALESCE(tot_gm_before,0),2) saldo_awal, IF($eRetur, 0, COALESCE(tambah,0)) tambah, IF($eReturIni, -$eRO, 0) tambah_retur, IF($eReturIni, COALESCE(tambah,0), 0) ded_retur, COALESCE(ded_alk,0) ded_alk, COALESCE(tot_gm,0) ded_gm, round(COALESCE(ded_fgl,0) + IF($eReturIni, $eSelisih, 0),2) ded_fgl, round(IF($eReturAwal, COALESCE(saldo_awal,0) - $eRO - $eSelisih, COALESCE(saldo_awal,0)) - COALESCE(ded_alk_bfr,0)  - COALESCE(tot_gm_before,0) - COALESCE(tot_gm,0) + IF($eRetur, 0, COALESCE(tambah,0)) + IF($eReturIni, -$eRO, 0) + IF($eReturIni, COALESCE(tambah,0), 0) - (COALESCE(ded_alk,0) + COALESCE(ded_fgl,0) + IF($eReturIni, $eSelisih, 0)),2) saldo_akhir from (select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, round(if(a.curr = 'IDR', (sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100))), (round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) * rate)),2) saldo_awal, 0 tambah, round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) nilai_asli, MAX(a.bppbno) bppbno, MAX(rt.rate) rate_doc from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                                INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and a.bpbdate > '2024-10-01' and a.bpbdate < '$start_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int
                    UNION
                    select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, 0 saldo_awal, round(if(a.curr = 'IDR', (sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100))), (round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) * rate)),2) tambah, round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) nilai_asli, MAX(a.bppbno) bppbno, MAX(rt.rate) rate_doc from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                        INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' and a.bpbdate >= '$start_date' and a.bpbdate <= '$end_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int) a 
                LEFT JOIN
                (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(COALESCE(fgl_fix, if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2)))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn,fgl_fix from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount, IF(c.nm_memo IN ($OR_FX_BPB), ROUND(IF(f.amount > c.amount, c.amount, f.amount) * (COALESCE((select mr.rate from masterrate mr where mr.v_codecurr = 'PAJAK' and mr.curr = h.curr and mr.tanggal = h.bpbdate limit 1), g.rate) - g.rate), 2), NULL) fgl_fix from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr,min(bpbdate) bpbdate from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk BETWEEN '$start_date' and '$end_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int
                LEFT JOIN
                (select no_bpb no_bpb_bfr, (ded_alk + ded_fgl) ded_alk_bfr from (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(COALESCE(fgl_fix, if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2)))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn,fgl_fix from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount, IF(c.nm_memo IN ($OR_FX_BPB), ROUND(IF(f.amount > c.amount, c.amount, f.amount) * (COALESCE((select mr.rate from masterrate mr where mr.v_codecurr = 'PAJAK' and mr.curr = h.curr and mr.tanggal = h.bpbdate limit 1), g.rate) - g.rate), 2), NULL) fgl_fix from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr,min(bpbdate) bpbdate from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk < '$start_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) a) c on c.no_bpb_bfr = a.bpbno_int
                LEFT JOIN
                (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.bpbno_int
                LEFT JOIN
                (select reff_doc reff_before, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_before from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal >= '2024-10-01' and tgl_journal < '$start_date' GROUP BY reff_doc) e on e.reff_before = a.bpbno_int
                /* DOKUMEN RO pasangan baris RI, dicari lewat nomor surat
                   jalannya. TIDAK disaring periode: pasangan RO-RI satu
                   kejadian, dan tanggalnya bisa beda bulan (mis.
                   GACC/RI/1126/07004 pasangannya GACC/RO/1026/14182).
                   Kalau RO-nya ikut disaring periode, pasangannya bisa
                   lepas dan barisnya tidak lagi net nol.
                   Dikelompokkan per bppbno supaya tidak menggandakan baris.
                   Rupiahnya dihitung DI SINI, per baris RO, supaya tiap RO
                   memakai kurs pajak tanggalnya sendiri. */
                LEFT JOIN
                (select x.bppbno, GROUP_CONCAT(DISTINCT x.bppbno_int) no_ro,
                        MIN(x.bppbdate) tgl_ro,
                        ROUND(SUM(x.qty * x.price * (1 + COALESCE(x.ppn,0)/100)),4) asli,
                        MAX(r2.rate) rate_ro,
                        ROUND(SUM(IF(x.curr = 'IDR',
                                     x.qty * x.price * (1 + COALESCE(x.ppn,0)/100),
                                     x.qty * x.price * (1 + COALESCE(x.ppn,0)/100) * COALESCE(r2.rate,1))),2) idr
                 from bppb x
                 left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) r2
                        on r2.tanggal = x.bppbdate
                 where x.cancel = 'N' and x.bppbno_int like '%/RO/%'
                 group by x.bppbno) ro on ro.bppbno = a.bppbno
                LEFT JOIN
                /* No Req & No DN per BPB. Dikelompokkan per (BPB, request) supaya 1 BPB yang diminta di beberapa RQDN tampil semuanya, bukan cuma 1. Nomor BPB dibersihkan dulu dari spasi/tab/enter yang kebawa dari alat pindai - TRIM saja TIDAK cukup, MySQL cuma mengabaikan SPASI di belakang, tab tetap bikin tidak ketemu. '-' = request ada tapi DN-nya belum terbit. */ (select GROUP_CONCAT(no_req ORDER BY no_req SEPARATOR ', ') no_req, GROUP_CONCAT(tgl_req ORDER BY no_req SEPARATOR ', ') tgl_req, GROUP_CONCAT(COALESCE(no_dn,'-') ORDER BY no_req SEPARATOR ', ') no_dn, GROUP_CONCAT(COALESCE(tgl_dn,'-') ORDER BY no_req SEPARATOR ', ') tgl_dn, no_bpb from (select no_req, tgl_req, no_dn, tgl_dn, no_bpb from (select a.no_req, a.tgl_req, c.no_dn, c.tgl_dn, replace(replace(replace(trim(b.no_bpb),char(9),''),char(13),''),char(10),'') no_bpb from req_dn_h a left join req_dn b on b.no_req = a.no_req left join tbl_debitnote_h c on c.no_dn = a.no_dn where COALESCE(a.status,'') != 'Cancel' and b.no_bpb is not null) x where no_bpb not in ('','-') GROUP BY no_bpb, no_req) a GROUP BY no_bpb ) f on f.no_bpb = trim(a.bpbno_int)");

/* Sama persis dgn yang dipakai di other_receivable_report.php: satu bagian
   uang = mata uang, nilai di mata uang aslinya (OCY), dan setara rupiahnya. */
if (!function_exists('orGrup')) {
    function orGrup($curr, $ocy, $idr) {
        $des = ($curr == 'IDR') ? 2 : 4;
        return '<td style="text-align:center;">'.$curr.'</td>'
             . '<td style="text-align:right;" value = "'.$ocy.'">'.number_format($ocy,$des).'</td>'
             . '<td style="text-align:right;" value = "'.$idr.'">'.number_format($idr,2).'</td>';
    }
    function orOcy($curr, $kurs, $idr) {
        return ($curr == 'IDR') ? $idr : round($idr / $kurs, 4);
    }
}
$ttl_beg =0;
$ttl_add =0;
$ttl_ded =0;
$ttl_for =0;
$sisa_dn =0;
$sisa_dn_bfr =0;
$ttl_end =0;
$no = 0;
$ttl_mj =0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;
    $saldo_awal = isset($row2['saldo_awal']) ? $row2['saldo_awal'] : 0;
    $tambah = isset($row2['tambah']) ? $row2['tambah'] : 0;
    $ded_alk = isset($row2['ded_alk']) ? $row2['ded_alk'] : 0;
    $ded_gm = isset($row2['ded_gm']) ? $row2['ded_gm'] : 0;
    $ded_fgl = isset($row2['ded_fgl']) ? $row2['ded_fgl'] : 0;
    $saldo_akhir = isset($row2['saldo_akhir']) ? $row2['saldo_akhir'] : 0;
    $tambah_retur = isset($row2['tambah_retur']) ? $row2['tambah_retur'] : 0;
    $ded_retur = isset($row2['ded_retur']) ? $row2['ded_retur'] : 0;
    $nilai_asli = isset($row2['nilai_asli']) ? $row2['nilai_asli'] : 0;
    $rate_doc = isset($row2['rate_doc']) ? $row2['rate_doc'] : 1;
    $nilai_asli_ro = isset($row2['nilai_asli_ro']) ? $row2['nilai_asli_ro'] : 0;
    $cr = isset($row2['curr']) ? $row2['curr'] : 'IDR';
    /* Baris retur: identitas barisnya dokumen RO, nomor RI-nya tampil di
       bagian Deduction Return. Sama spt di other_receivable_report.php. */
    $ada_ro = (isset($row2['no_ro']) && $row2['no_ro'] != '-' && $row2['no_ro'] != '');
    $no_bpb_tampil  = $ada_ro ? $row2['no_ro']  : $row2['bpbno_int'];
    $tgl_bpb_tampil = $ada_ro ? $row2['tgl_ro'] : $row2['bpbdate'];
    $no_ri_tampil   = $ada_ro ? $row2['bpbno_int'] : '-';
    $tgl_ri_tampil  = $ada_ro ? $row2['bpbdate']   : '-';

    /* BPB yang selisih kursnya diakui sbg Forex Gain/(Loss).
       Dipakai SISA SEBENARNYA, bukan selisih kurs yang dihitung ulang:
       saldo awal BPB tidak selalu sama persis dgn amount DN x kurs pajak,
       dan perbedaan kecil itu menyisakan angka di Ending Balance.
       Deduction (GM) ikut dikurangkan, jadi kalau selisihnya sudah
       ditutup lewat jurnal GM, Forex-nya jadi 0 dgn sendirinya - tidak
       pernah terhitung dua kali. */
    /* PENJAGA: hanya kalau BPB itu MEMANG dialokasi pada periode ini.
       Tanpa penjaga ini, pada periode SEBELUM alokasinya terjadi seluruh
       saldo BPB ikut tersapu ke Forex dan Ending-nya jadi nol - padahal
       saat itu saldonya memang masih menggantung. Ketahuan saat menguji
       periode Jun-Agu 2026 ke data produksi. */
    if ($ded_alk != 0 && in_array($row2['bpbno_int'], $OR_FX_ARR)) {
        $ded_fgl = round($saldo_awal + $tambah - $ded_alk - $ded_gm, 2);
        $saldo_akhir = 0;
    }


    $ttl_beg +=$saldo_awal;
    $ttl_add +=$tambah;
    $ttl_ded +=$ded_alk;
    $ttl_mj  +=$ded_gm;
    $ttl_for +=$ded_fgl;
    $ttl_end +=$saldo_akhir;

    $kurs = ($rate_doc > 0) ? $rate_doc : 1;
    $beg_ocy    = orOcy($cr, $kurs, $saldo_awal);
    $add_ocy    = ($tambah == 0) ? 0 : $nilai_asli;
    $addret_ocy = ($tambah_retur == 0) ? 0 : -$nilai_asli_ro;
    $dedret_ocy = ($ded_retur == 0) ? 0 : $nilai_asli;
    $alk_ocy    = orOcy($cr, $kurs, $ded_alk);
    $gm_ocy     = orOcy($cr, $kurs, $ded_gm);
    $end_ocy    = orOcy($cr, $kurs, $saldo_akhir);

    /* Baris retur: Addition & saldo awalnya 0, angkanya ada di dua kolom retur. */
    if ($saldo_awal == 0 && $tambah == 0 && $tambah_retur == 0 && $ded_retur == 0) {
            // code...
    }else{
        echo ' <tr style="font-size:12px;text-align:center;">
        <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
        <td style="text-align : left;" value = "'.$row2['pono'].'">'.$row2['pono'].'</td>
        <td style="text-align : left;" value = "'.$no_bpb_tampil.'">'.$no_bpb_tampil.'</td>
        <td style="width: 100px;" value = "'.$tgl_bpb_tampil.'">'.date("d-M-Y",strtotime($tgl_bpb_tampil)).'</td>
        <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
        <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_req'].'">'.$row2['no_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_req'].'">'.$row2['tgl_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_dn'].'">'.$row2['no_dn'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_dn'].'">'.$row2['tgl_dn'].'</td>
        <td style="text-align : right;" value = "'.$rate_doc.'">'.number_format($rate_doc,2).'</td>'
        . orGrup($cr, $beg_ocy, $saldo_awal)
        . orGrup($cr, $add_ocy, $tambah)
        . orGrup($cr, $addret_ocy, $tambah_retur)
        . '<td style="text-align:left;">'.$no_ri_tampil.'</td>'
        . '<td>'.($tgl_ri_tampil == '-' ? '-' : date("d-M-Y", strtotime($tgl_ri_tampil))).'</td>'
        . orGrup($cr, $dedret_ocy, $ded_retur)
        . orGrup($cr, $alk_ocy, $ded_alk)
        . orGrup($cr, $gm_ocy, $ded_gm)
        . orGrup('IDR', $ded_fgl, $ded_fgl)
        . orGrup($cr, $end_ocy, $saldo_akhir)
        . '</tr>
        ';
}

?>
<?php 

}
?>
</table>

</body>
</html>




