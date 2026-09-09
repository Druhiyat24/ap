<?php
/* ==========================================================================
   mj_upload.php — tab "Upload Journal" (unggah jurnal memorial dari Excel).

   ALUR: pilih berkas -> proses_upload.php (staging tbl_memorial_journal_temp)
         -> ajx_get_data_mj_upload.php (baris jurnal)  = datatable3
          + ajx_get_sum_mj_upload.php  (rekap per No Journal) = datatable4
         -> save_mj_upload_fix.php.
   Tata letak mengikuti alur kerja: UPLOAD -> REVIEW -> BALANCE -> SAVE.

   KONTRAK dengan JS di create_memorial_journal.php — JANGAN diubah:
     * id: uploadBox, fileUpload, fileName, btnUpload, simpan3, reset,
       table-upload-mj (15 kolom), table-upload-mj-group (8 kolom), form-data3.
     * 24 input total berakhiran "3" (12 tampak + 12 hidden) yang diisi
       drawCallback datatable3 lewat .val().
     * Kedua <tbody> WAJIB kosong (sumbernya ajax; baris statis akan bentrok
       dengan hasil ajax pada draw pertama).
     * Semua tombol WAJIB type="button": form ini tidak punya action, tombol
       submit akan mem-post ulang halaman dan membuang preview yang sudah ada.
   Tab ini sengaja TIDAK punya field tanggal/tipe/profit center: ketiganya
   dibaca PER BARIS dari berkas Excel-nya (COL B / COL C / COL E).

   CATATAN soal id batal: id itu DIHAPUS di sini (tombolnya tetap ada). Nilainya
   nol — tidak ada satu pun JS di halaman ini yang memakai #batal (aksinya
   inline onclick), sedangkan id yang sama sudah dipakai mj_input.php dan
   mj_from_hris.php di HALAMAN YANG SAMA, jadi sebelumnya ada tiga id kembar.
   ========================================================================== */

/* Nama Profit Center dibaca dari master supaya tidak dipatok di markup; kalau
   query gagal (koneksi / hak akses / tabel), dipakai nama cadangan.
   $conn1 disediakan ../header.php yang di-include create_memorial_journal.php
   baris 1 — tab ini memang hanya dipakai dari halaman itu (sama seperti
   mj_ppn_masukan.php), tapi seluruh query di bawah dijaga sehingga berkas ini
   tetap ter-render walau $conn1 tidak ada atau query-nya gagal. */
$mjuPcName = ['NAG' => 'Nirwana Alabare Garment', 'NAK' => 'Nirwana Alabare Knitting'];

/* KARTU TOTAL DIPATOK ke NAG + NAK + Grand Total, TIDAK di-loop dari master_pc.
   Alasannya keras: drawCallback datatable3 di create_memorial_journal.php hanya
   menjumlah kode_pc 'NAG' dan 'NAK', dan hanya mengisi id tot_*_nag3 / tot_*_nak3.
   Membuat kartu ke-4 dari master_pc akan menghasilkan kartu yang selamanya
   kosong. Profit center Active lain tetap DITAMPILKAN sebagai lencana abu-abu
   di catatan panel, dan kalau berkas yang diunggah benar-benar berisi baris
   dengan PC di luar daftar ini, skrip di bawah memunculkan peringatan yang
   menyebut angka barisnya — jadi PC yang tidak terjumlah tidak pernah hilang
   diam-diam dari layar. */
$mjuPcTotalled = ['NAG', 'NAK'];

$mjuPcExtra = [];   // PC Active selain NAG/NAK -> lencana abu-abu
if (isset($conn1) && $conn1) {
    $qMjuPc = @mysqli_query($conn1, "select kode_pc, id_pc, nama_pc from master_pc where status = 'Active' order by kode_pc");
    if ($qMjuPc) {
        while ($rMjuPc = mysqli_fetch_assoc($qMjuPc)) {
            $kode = trim((string) $rMjuPc['kode_pc']);
            if ($kode === '') { continue; }
            if (in_array($kode, $mjuPcTotalled, true)) {
                if (trim((string) $rMjuPc['nama_pc']) !== '') { $mjuPcName[$kode] = $rMjuPc['nama_pc']; }
            } else {
                $mjuPcExtra[$kode] = trim((string) $rMjuPc['nama_pc']);
            }
        }
    }
}
?>
<style>
/* ============================================================================
   Tab UPLOAD JOURNAL — skin senada dengan tab PPN Masukan (mj_ppn_masukan.php).
   Prefiks .mju- (BUKAN prefiks milik tab PPN Masukan) karena kedua tab dirender di HALAMAN YANG SAMA;
   memakai nama kelas yang sama akan membuat keduanya saling menimpa.

   Kontrol yang sudah ada di app-skin-form.css DIPAKAI ULANG, tidak dibuatkan
   kembarannya: .app-flabel, .app-search, .app-actions, .app-btn*,
   .app-loading-wrap. Kelas lama .mju-flabel / .mju-actions / .mju-total /
   .total-box / .table-gradient sudah tidak dipakai lagi di markup.
   TINDAK LANJUT (bukan di berkas ini): setelah versi ini dipasang, aturan
   .total-box (~15 baris) dan #uploadBox:hover di create_memorial_journal.php
   sudah tidak ada pemakainya dan boleh dihapus. .table-gradient th JANGAN
   dihapus — kelas itu masih hidup dan masih dipakai tabel lain.
   ============================================================================ */

/* ---------------------------------------------------------------------------
   1. PANEL HEADER — rail langkah + bar upload + catatan dibungkus SATU blok
   --------------------------------------------------------------------------- */
.mju-head {
    border: 1px solid #e6ebf3; border-radius: 12px; background: #fff;
    padding: 14px 16px 4px; margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
}

/* Rail langkah: menandai user sedang berada di tahap mana. Statusnya diisi
   skrip di bagian bawah berkas ini, bukan oleh JS halaman induk. */
.mju-steps { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.mju-step {
    display: inline-flex; align-items: center; gap: 7px;
    border: 1px solid #e6ebf3; border-radius: 20px; background: #f8fafc;
    padding: 4px 12px 4px 5px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    color: #94a3b8; white-space: nowrap;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
}
.mju-step-n {
    display: inline-flex; align-items: center; justify-content: center;
    width: 18px; height: 18px; border-radius: 50%;
    background: #e2e8f0; color: #64748b; font-size: 10px; font-weight: 700; line-height: 1;
}
/* Centang tahap selesai memakai <i class="fa fa-check"> ASLI di markup, BUKAN
   content:"\f00c" — halaman ini memuat Font Awesome 5 + v4-shims sehingga nama
   font-nya bukan lagi "FontAwesome" dan pseudo-element tidak bisa diandalkan. */
.mju-step-n i { display: none; font-size: 9px; }
.mju-step.is-done .mju-step-n i { display: inline-block; }
.mju-step.is-done .mju-step-n b { display: none; }
.mju-step.is-done { border-color: #cfe7d6; background: #f2fbf5; color: #127a12; }
.mju-step.is-done .mju-step-n { background: #127a12; color: #fff; }
.mju-step.is-active { border-color: #c7d7f7; background: #eef4ff; color: #1E3A8A; }
.mju-step.is-active .mju-step-n { background: linear-gradient(180deg,#2f6bdd,#1d4ed8); color: #fff; }
.mju-step.is-bad { border-color: #f3cccc; background: #fdeaea; color: #c00000; }
.mju-step.is-bad .mju-step-n { background: #c00000; color: #fff; }
.mju-step-sep { color: #cbd5e1; font-size: 12px; }
/* flex-basis + min-width DIKUNCI: hint-nya bisa sepanjang satu kalimat penuh.
   Tanpa ini ia jadi flex item tanpa basis dan pada lebar menengah membelah diri
   di sela-sela pil langkah dengan cara yang tak terduga. */
.mju-step-hint {
    flex: 1 1 260px; min-width: 200px; margin-left: 4px;
    font-size: 11.5px; color: #64748b; line-height: 1.45;
}
.mju-step-hint.is-bad { color: #c00000; font-weight: 600; }

/* ---------------------------------------------------------------------------
   2. BAR UPLOAD — strip mendatar (area drop di kiri, tombol di kanan)
   --------------------------------------------------------------------------- */
.mju-upload-bar {
    display: flex; align-items: center; gap: 14px;
    border: 1.5px dashed #bcd7f5; border-radius: 10px;
    background: linear-gradient(180deg,#fbfdff,#f5f9ff); padding: 12px 14px;
    transition: border-color .15s ease, background .15s ease;
}
/* Halaman induk masih memuat aturan lama #uploadBox:hover (selector ID = 1-1-0,
   jadi MENGALAHKAN selector kelas biasa). Baris ber-id di bawah sengaja ikut
   menyebut #uploadBox (1-2-0) supaya skin ini menang SEKARANG, tanpa menunggu
   aturan lama itu dihapus dari create_memorial_journal.php. */
.mju-upload-bar:hover, .mju-upload-bar.is-drag,
#uploadBox.mju-upload-bar:hover, #uploadBox.mju-upload-bar.is-drag {
    border-color: var(--app-accent, #17a2b8); background: #f3fbfd;
}
.mju-drop { flex: 1 1 auto; display: flex; align-items: center; gap: 12px; cursor: pointer; margin-bottom: 0; }
.mju-drop > i { font-size: 25px; color: var(--app-accent, #17a2b8); }
.mju-drop-txt { font-size: 13.5px; font-weight: 600; line-height: 1.3; color: #334155; }
.mju-drop-file { font-size: 11.5px; color: #94a3b8; word-break: break-all; }
.mju-drop-file.has-file { color: var(--app-accent-dark, #0e7c8f); font-weight: 700; }
.mju-upload-act { flex: 0 0 auto; display: flex; gap: 8px; }

/* Ringkasan syarat berkas — mengisi kolom kanan yang dulu jadi ruang kosong. */
.mju-tips {
    list-style: none; margin: 0; padding: 8px 12px;
    border: 1px solid #dbe4f3; border-radius: 8px; background: #fff;
}
.mju-tips li { display: flex; gap: 8px; font-size: 11.5px; color: #64748b; line-height: 1.45; }
.mju-tips li + li { margin-top: 5px; }
.mju-tips > li > i { flex: 0 0 auto; margin-top: 3px; font-size: 10px; color: var(--app-accent, #17a2b8); }
.mju-tips b { color: #334155; }
.mju-col { font-weight: 700; font-size: 10px; letter-spacing: .3px; color: #475569;
           background: #eef2f7; border-radius: 4px; padding: 0 5px; }

/* ---------------------------------------------------------------------------
   3. CATATAN PANEL — satu baris ramping berpembatas garis putus-putus
   --------------------------------------------------------------------------- */
.mju-note {
    display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
    border-top: 1px dashed #e6ebf3; margin-top: 12px; padding: 9px 2px 8px;
    font-size: 11.5px; color: #64748b; line-height: 1.45;
}
.mju-note > i { flex: 0 0 auto; color: var(--app-accent, #17a2b8); font-size: 13px; }
.mju-note b { color: #334155; }
.mju-badges { display: inline-flex; flex-wrap: wrap; gap: 5px; }
.mju-badge {
    display: inline-block; padding: 1px 8px; border-radius: 20px;
    background: var(--app-accent-soft, #e4f6f9); color: var(--app-accent-dark, #0e7c8f);
    font-weight: 700; font-size: 10.5px; letter-spacing: .03em;
}
/* PC Active yang TIDAK punya kartu total (drawCallback induk cuma NAG & NAK).
   Sengaja dibedakan abu-abu supaya tidak menjanjikan angka yang tak ada. */
.mju-badge.is-off { background: #eef2f7; color: #94a3b8; }

/* ---------------------------------------------------------------------------
   4. JUDUL SEKSI + TOOLBAR — tiap tabel punya nama, jumlah baris, & pencarian
   --------------------------------------------------------------------------- */
.mju-sec { margin-bottom: 18px; }
.mju-sec-head {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 8px; margin-bottom: 8px;
}
.mju-sec-title { font-size: 15px; font-weight: 400; color: #212529; margin-bottom: 0; }
.mju-sec-sub { font-size: 12px; font-weight: 400; color: #6c757d; margin-left: 6px; }
/* Pil jumlah baris DIKOSONGKAN di markup dan baru diisi skrip: menuliskan
   "0 rows" di HTML berarti layar berbohong sampai draw pertama selesai. */
.mju-count {
    font-size: 11px; font-weight: 600; color: #64748b; white-space: nowrap;
    background: #eef2f7; border-radius: 20px; padding: 2px 9px; margin-left: 6px;
}
.mju-count:empty { display: none; }
/* Chip peringatan: default disembunyikan, ditampilkan skrip lewat .is-on.
   Isinya MENIRU PERSIS dua gerbang di #simpan3, jadi alasan Save akan menolak
   sudah terbaca sebelum tombolnya diklik. */
.mju-chip {
    display: none; align-items: center; gap: 5px; white-space: nowrap;
    font-size: 11px; font-weight: 700; border-radius: 20px; padding: 2px 9px; margin-left: 6px;
}
.mju-chip.is-on { display: inline-flex; }
.mju-chip-bad { background: #fdeaea; color: #c00000; }
.mju-chip-warn { background: #fff7e6; color: #b45309; }
.mju-chip i { font-size: 10px; }
.mju-tools { display: flex; align-items: center; gap: 10px; margin-left: auto; }
/* Toolbar tabel rekap hanya muncul kalau jurnalnya lebih dari satu halaman —
   di berkas biasa (2-3 jurnal) semuanya sudah kelihatan, dan menempelkan
   perabot yang sama persis dua kali cuma menambah keramaian. */
.mju-tools.is-auto { display: none; }
.mju-tools.is-auto.is-on { display: flex; }
/* .app-search (app-skin-form.css) dipakai ulang, tidak dibuatkan kembarannya. */
.mju-tools .app-search input { min-width: 210px; max-width: 100%; }
.mju-len-wrap {
    display: inline-flex; align-items: center; gap: 6px; margin-bottom: 0;
    font-size: 10.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .4px; color: #94a3b8;
}
.mju-len {
    height: 30px; border: 1px solid #dbe4f3; border-radius: 8px; background: #fff;
    color: #334155; font-size: 12px; padding: 0 6px;
}
.mju-len:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); outline: none; }

/* ---------------------------------------------------------------------------
   5. KOTAK TABEL — tinggi dibatasi, header menempel, chrome DataTables dirapikan
   --------------------------------------------------------------------------- */
/* max-height WAJIB ada: position:sticky pada <thead> hanya bekerja di dalam
   kotak scroll yang tingginya dibatasi. Tanpa max-height kotaknya tumbuh
   setinggi isi, tidak pernah ada scroll vertikal, dan header tidak menempel. */
.mju-tbl-wrap {
    max-height: 420px; overflow: auto;
    border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
}
.mju-tbl-wrap.mju-sm { max-height: 300px; }

/* Scrollbar ramping. Tabel rincian punya 15 kolom sehingga PASTI menggulir
   mendatar di layar biasa - dengan scrollbar bawaan Windows yang tebal dan abu
   pekat, kotak tabel terlihat seperti rusak/terpotong, bukan seperti area yang
   memang bisa digulir. Firefox memakai scrollbar-width, Chrome/Edge ::-webkit-. */
.mju-tbl-wrap { scrollbar-width: thin; scrollbar-color: #c7d2e0 transparent; }
.mju-tbl-wrap::-webkit-scrollbar { width: 9px; height: 9px; }
.mju-tbl-wrap::-webkit-scrollbar-track { background: transparent; }
.mju-tbl-wrap::-webkit-scrollbar-thumb {
    background: #c7d2e0; border-radius: 8px; border: 2px solid #fff;
}
.mju-tbl-wrap::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.mju-tbl-wrap::-webkit-scrollbar-corner { background: transparent; }

/* Batasi 2 kolom terlebar di tabel rincian. Profit Center ("PCP001 - NIRWANA
   ALABARE GARMENT") dan COA ("2.52.03 PAJAK YANG MASIH HARUS DIBAYAR - PPN")
   sendirian memakan ~40% lebar tabel, sehingga Debit/Credit/Description
   terdorong keluar layar dan pengguna harus menggulir untuk melihat angkanya -
   padahal justru angka itu yang perlu diperiksa. Dipotong dengan elipsis;
   nilai utuhnya tetap bisa dibaca lewat tooltip (judul disetel di skrip bawah).
   HANYA tabel rincian: tabel ringkasan cuma 8 kolom dan sudah muat. */
#table-upload-mj tbody td:nth-child(1) { max-width: 190px; }
#table-upload-mj tbody td:nth-child(2) { max-width: 240px; }
#table-upload-mj tbody td:nth-child(8),
#table-upload-mj tbody td:nth-child(14) { max-width: 150px; }
#table-upload-mj tbody td:nth-child(1),
#table-upload-mj tbody td:nth-child(2),
#table-upload-mj tbody td:nth-child(8),
#table-upload-mj tbody td:nth-child(14) { overflow: hidden; text-overflow: ellipsis; }

/* -- Chrome DataTables ------------------------------------------------------
   DataTables membungkus <table> DI TEMPAT, jadi .dataTables_wrapper ikut masuk
   ke dalam kotak scroll ini. Seluruh aturan di bawah DISCOPE ke .mju-tbl-wrap /
   .mju-tbl-foot: tab Manual & HRIS memakai DataTables di halaman yang sama dan
   tidak boleh ikut berubah (ini juga alasan halaman induk hanya me-link
   app-skin-form.css, bukan app-skin.css utuh).

   PENTING — sumber scrollbar mendatar palsu. dom bawaan bundel Bootstrap4
   (diverifikasi di css/4.1.1/datatables.min.js, DataTables 1.10.21) adalah:
     "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
      <'row'<'col-sm-12'tr>>
      <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
   Ketiga .row itu mendarat DI DALAM kotak overflow:auto ini, dan margin
   samping -15px milik .row membuat scrollWidth-nya 15px lebih lebar dari
   kotaknya. Akibatnya scrollbar mendatar SELALU muncul — termasuk di tabel
   rekap 8 kolom yang sebetulnya muat. Dua baris berikut yang mematikannya. */
.mju-tbl-wrap div.dataTables_wrapper > .row { margin-left: 0; margin-right: 0; }
.mju-tbl-wrap div.dataTables_wrapper > .row > [class*="col-"] { padding-left: 0; padding-right: 0; }

/* .mju-js ditambahkan skrip SETELAH toolbar penggantinya berhasil dipasang.
   Kalau skripnya gagal jalan, chrome bawaan tetap tampil sehingga search &
   pemilih jumlah baris tidak hilang tanpa pengganti (degradasi aman, bukan
   asumsi bahwa skrip selalu jalan). */
.mju-tbl-wrap.mju-js div.dataTables_wrapper > .row:first-child,
.mju-tbl-wrap.mju-js div.dataTables_wrapper > .row:last-child { display: none; }
.mju-tbl-wrap.mju-js .dataTables_length,
.mju-tbl-wrap.mju-js .dataTables_filter { display: none; }

/* datatables.min.css memasang table.dataTable{margin-top:6px!important;
   margin-bottom:6px!important}. Tanpa dilawan, ada celah di dalam kotak dan
   header lepas dari tepi atas saat menempel. */
.mju-tbl { margin: 0 !important; border: 0; }

/* Gradien dipasang di BARIS (tr), bukan tiap sel — kalau di th, tiap kolom
   menggambar gradiennya sendiri sehingga header terlihat berpita.
   Sticky dipasang di <thead> (bukan th) supaya latar barisnya ikut menempel. */
.mju-tbl thead { position: sticky; top: 0; z-index: 2; background: #1E3A8A; }
.mju-tbl thead tr { background: linear-gradient(90deg,#1E3A8A,#2f5bbf); }
/* Tanpa !important: kelas warisan .table-gradient sudah dilepas dari markup
   tabel ini, jadi tidak ada lagi aturan latar yang perlu dilawan. */
.mju-tbl thead th {
    background: transparent; color: #fff; border: 0; border-bottom: 2px solid #16306e;
    font-size: 11px; font-weight: 600; letter-spacing: .4px; text-transform: uppercase;
    white-space: nowrap; vertical-align: middle; padding: 9px 10px;
}
/* Header kolom angka ikut rata kanan seperti isinya. Ditulis di markup (bukan
   lewat columnDefs className, yang hanya mendarat andal di sel tbody), supaya
   judul Debit/Credit sudah rata kanan bahkan sebelum DataTables jalan. */
.mju-tbl thead th.mju-num { text-align: right; }
/* Ukuran huruf & padding SENGAJA sama persis dengan tabel preview tab PPN Masukan
   (12px isi / 11px header / padding 6px 10px). Dua tab di kartu yang sama tidak
   boleh berbeda tinggi baris. */
.mju-tbl tbody td {
    font-size: 12px; vertical-align: middle; white-space: nowrap;
    padding: 6px 10px; border-top: 0; border-bottom: 1px solid #eef2f7;
}
.mju-tbl tbody tr:nth-child(even) td { background: #fbfcfe; }
/* URUTAN TIGA BLOK DI BAWAH INI PENTING. Semuanya !important, jadi penentunya
   kekhususan lalu urutan:
     a. warna kolom Debit/Credit -> (0,2,2)
     b. hover                    -> (0,2,3), mengalahkan (a)
     c. baris merah tidak valid  -> (0,2,3) juga, karena itu ditaruh SETELAH (b)
   Kolom Debit/Credit ditandai lewat :nth-child karena <td>-nya dibuat
   DataTables (tidak bisa diberi kelas dari markup). Nomor kolomnya POSISIONAL
   dan terikat langsung ke columnDefs di create_memorial_journal.php:
     detail : th ke-12 & ke-13  <-> columnDefs targets [11,12] (0-based)
     group  : th ke-5  & ke-6   <-> columnDefs targets [4,5]
   Kalau urutan kolom digeser, GESER JUGA nomor di sini dan di targets. */
.mju-tbl-detail tbody td:nth-child(12),
.mju-tbl-group  tbody td:nth-child(5) { background: #f7fbff !important; font-weight: 600; color: #1d4ed8; }
.mju-tbl-detail tbody td:nth-child(13),
.mju-tbl-group  tbody td:nth-child(6) { background: #fffdf5 !important; font-weight: 600; color: #b45309; }
.mju-tbl tbody tr:hover td { background: #eaf1ff !important; }
/* Baris yang COA/Cost Center-nya tidak ter-mapping diberi style INLINE #ffcccc
   pada <tr> oleh rowCallback. Tiga penulisan dipasang sekaligus: ejaan rgb()
   (yang sebenarnya ditulis jQuery ke atribut style), ejaan hex, dan kelas
   .mju-row-bad — jadi sorotan ini tetap jalan sebelum maupun sesudah siapa pun
   mengubah rowCallback. !important ada di <td> karena penandanya di <tr>,
   sedangkan zebra & hover mengecat <td>. */
.mju-tbl tbody tr[style*="255, 204, 204"] td,
.mju-tbl tbody tr[style*="#ffcccc"] td,
.mju-tbl tbody tr.mju-row-bad td { background: #ffe3e3 !important; color: #7f1d1d; }
/* Angka rata kanan & sejajar (kelas .text-right disuntikkan DataTables). */
.mju-tbl .text-right, .mju-tbl .mju-num { text-align: right; font-variant-numeric: tabular-nums; }
/* Keadaan kosong bawaan DataTables dibuat lapang, bukan sebaris tipis. */
.mju-tbl td.dataTables_empty { padding: 30px 10px; text-align: center; color: #94a3b8; font-size: 12.5px; }

/* Info & pagination DIPINDAH skrip ke kotak ini (DI LUAR area scroll) supaya
   tidak ikut bergeser saat tabel di-scroll ke samping dan tidak terpotong sudut
   membulat kotak tabel. Node-nya dipindah, bukan disalin: DataTables menulis ke
   node itu lewat referensi, bukan lewat pencarian ulang di DOM. */
.mju-tbl-foot {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px; margin: 8px 2px 0;
}
.mju-tbl-foot:empty { display: none; }
.mju-tbl-foot .dataTables_info {
    float: none !important; margin: 0 !important; padding: 0;
    font-size: 11.5px; color: #94a3b8;
}
.mju-tbl-foot div.dataTables_paginate { float: none !important; margin: 0 !important; padding: 0; }
.mju-tbl-foot div.dataTables_paginate ul.pagination { margin: 0; }
.mju-tbl-foot .page-link {
    border: 1px solid #e2e8f0; border-radius: 8px; margin-left: 4px;
    color: #1E3A8A; font-size: 12px; font-weight: 600; padding: 4px 10px;
}
/* Bootstrap 4 memberi tombol Previous/Next radius sudut luar sendiri lewat
   .page-item:first-child .page-link (0,3,0) — lebih khusus dari aturan .page-link
   di atas (0,2,0), jadi tanpa dua baris ini Previous/Next tetap 4px sementara
   tombol angkanya 8px, dan pager-nya terlihat sumbing. */
.mju-tbl-foot .page-item:first-child .page-link,
.mju-tbl-foot .page-item:last-child .page-link { border-radius: 8px; }
.mju-tbl-foot .page-item.active .page-link { background: #1E3A8A; border-color: #1E3A8A; color: #fff; }
.mju-tbl-foot .page-item.disabled .page-link { color: #cbd5e1; background: #fff; }
.mju-tbl-foot .paginate_button { font-size: 12px; }

/* Keterangan arti baris merah — sebelumnya tidak ada sama sekali, baris merah
   cuma muncul dan Save menolak diam-diam. DISEMBUNYIKAN saat tidak ada baris
   merah: kalimat "Save stays blocked" yang nongkrong di berkas bersih adalah
   informasi palsu. */
.mju-legend {
    display: none; align-items: flex-start; gap: 8px; margin: 8px 2px 0;
    font-size: 11px; color: #c00000; font-weight: 600; line-height: 1.5;
}
.mju-legend.is-on { display: flex; }
.mju-legend-sw {
    flex: 0 0 auto; width: 22px; height: 12px; margin-top: 2px;
    border-radius: 3px; background: #ffe3e3; border: 1px solid #f3c2c2;
}

/* ---------------------------------------------------------------------------
   6. TILE TOTAL — 12 kotak input abu-abu diganti 3 tile tipografis.
   Angka WAJIB tetap <input>: JS induk mengisinya dengan .val(), yang jadi
   NO-OP SENYAP kalau elemennya <span>/<div> (kartu akan kosong selamanya).
   Karena itu input-nya dibuat "tanpa baju" — tak berbingkai, tak berlatar,
   seukuran teks biasa — TAPI tetap <input>.
   --------------------------------------------------------------------------- */
.mju-tot {
    display: flex; flex-direction: column; gap: 4px; height: 100%;
    background: #fff; border: 1px solid #e8edf5; border-left: 4px solid #94a3b8;
    border-radius: 8px; padding: 10px 14px; box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: border-color .15s ease, background .15s ease;
}
.mju-tot.tone-nag { border-left-color: #5b7ba8; }
.mju-tot.tone-nak { border-left-color: #4f8a6b; }
.mju-tot.tone-all { border-left-color: #4a5578; background: #f8fafc; }
/* Status balance ikut mewarnai kartunya, bukan cuma tulisannya. */
.mju-tot.is-ok  { border-left-color: #127a12; }
.mju-tot.is-bad { border-color: #f3cccc; border-left-color: #c00000; background: #fff7f7; }
.mju-tot-name {
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .4px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    padding-bottom: 6px; border-bottom: 1px solid #eef2f7; margin-bottom: 2px;
}
.mju-tot-code {
    background: #eef2f7; color: #475569; border-radius: 4px; padding: 0 5px;
    margin-left: 6px; font-size: 9.5px; letter-spacing: .4px;
}
/* Debit & Credit DITUMPUK, tidak berdampingan. Empat angka dalam kartu selebar
   col-md-4 (~240px di lebar md) menyisakan ~95px per angka, sementara nilai
   rutin seperti 12,345,678,901.00 butuh ~135px — dan text-overflow:ellipsis
   pada <input> tidak bisa diandalkan di luar Chrome/Edge, jadi di Firefox
   angkanya cuma terpotong tanpa tanda apa pun. Ditumpuk, tiap angka dapat
   selebar kartu dikurangi label (~150px). Skrip juga menyalin nilainya ke
   atribut title, jadi angka ekstrem tetap bisa dibaca lewat hover. */
/* CATATAN: komentar di atas menjelaskan versi TERTUMPUK yang lama. Atas
   permintaan user, Debit & Credit kini BERSEBELAHAN mengikuti kartu total di
   tab PPN Masukan. Risiko sempit yang disebut di atas ditekan dengan: label di
   ATAS angka (bukan di sampingnya) sehingga angka dapat selebar penuh kolom,
   dan pada layar sempit kedua kolom otomatis kembali menumpuk (media query di
   bawah). Nilai penuh tetap tersedia lewat atribut title yang diisi skrip. */
.mju-tot-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
    grid-auto-flow: column;          /* isi MENURUN: debit di kolom kiri, credit di kanan */
    column-gap: 14px; row-gap: 3px;
    margin-top: 7px;
}
.mju-tot-col { display: flex; flex-direction: column; min-width: 0; }
.mju-tot-col.is-cre { border-left: 1px solid #eef2f7; padding-left: 14px; }
.mju-tot-split .mju-tot-lbl { min-width: 0; margin-bottom: 1px; }
.mju-tot-split .mju-tot-col.is-deb .mju-tot-lbl { color: #1d4ed8; }
.mju-tot-split .mju-tot-col.is-cre .mju-tot-lbl { color: #b45309; }
.mju-tot-split .mju-tot-col.is-sub .mju-tot-lbl { color: #94a3b8; font-size: 9.5px; letter-spacing: .4px; }
.mju-tot-split input.mju-tot-val { text-align: left; }
@media (max-width: 991.98px) {
    .mju-tot-split { grid-template-columns: 1fr; grid-auto-flow: row; }
    .mju-tot-col.is-cre { border-left: 0; padding-left: 0; margin-top: 6px; }
}

.mju-tot-row { display: flex; align-items: baseline; gap: 8px; }
.mju-tot-row + .mju-tot-row.is-main { border-top: 1px solid #eef2f7; margin-top: 5px; padding-top: 5px; }
.mju-tot-lbl {
    flex: 0 0 auto; min-width: 46px;
    font-size: 10.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: #64748b; cursor: help;
}
.mju-tot-row.is-deb .mju-tot-lbl { color: #1d4ed8; }
.mju-tot-row.is-cre .mju-tot-lbl { color: #b45309; }
.mju-tot-row.is-sub .mju-tot-lbl { color: #94a3b8; font-size: 9.5px; letter-spacing: .4px; }
input.mju-tot-val {
    flex: 1 1 auto; min-width: 0; display: block; width: 100%;
    margin: 0; padding: 0; border: 0; background: transparent;
    height: auto; line-height: 1.3; cursor: default; text-align: right;
    font-size: 15px; font-weight: 700; color: #1f2937; font-variant-numeric: tabular-nums;
}
/* tabindex="-1" ada di markup: readonly SAJA tidak cukup, input readonly tetap
   jadi perhentian Tab. Tanpa itu keyboard user melewati selusin field tak
   berbingkai yang terlihat seperti teks biasa. Fokus lewat klik tetap diberi
   cincin supaya tidak ada fokus yang tak terlihat. */
input.mju-tot-val:focus { outline: none; box-shadow: none; }
input.mju-tot-val:focus-visible { outline: 2px solid #93c5fd; outline-offset: 2px; }
/* CATATAN PENTING: drawCallback halaman induk menulis color merah/hijau INLINE
   ke empat input grand total sebagai penanda balance. JANGAN PERNAH memakai
   !important untuk color / font-weight di sini — penanda itu akan mati. */
.mju-tot.tone-all input.mju-tot-val { font-size: 16px; color: #16306e; }
input.mju-tot-val.is-sub,
.mju-tot.tone-all input.mju-tot-val.is-sub { font-size: 12px; font-weight: 600; color: #64748b; }
/* Baris sendiri: pesan "tidak balance" bisa panjang, jangan sampai terpotong. */
.mju-tot-bal { font-size: 11px; font-weight: 700; margin-top: 6px; padding-top: 6px; border-top: 1px solid #eef2f7; line-height: 1.45; }
.mju-tot-bal i { margin-right: 4px; }
.mju-bal-idle { color: #94a3b8; font-weight: 600; }
.mju-bal-ok   { color: #127a12; }
.mju-bal-bad  { color: #c00000; }
.mju-bal-warn { display: block; margin-top: 4px; color: #b45309; font-weight: 600; }

/* ---------------------------------------------------------------------------
   7. Layar sempit
   --------------------------------------------------------------------------- */
@media (max-width: 575.98px) {
    .mju-tools { width: 100%; margin-left: 0; }
    .mju-tools .app-search { flex: 1 1 auto; }
    .mju-tools .app-search input { min-width: 0; width: 100%; }
    .mju-upload-bar { flex-wrap: wrap; }
    .mju-upload-act { width: 100%; }
}
</style>

<form id="form-data3" method="post">
    <div class="card shadow-sm">
        <div class="card-body">

            <!-- ============ PANEL: langkah + upload + catatan ============ -->
            <div class="mju-head">

                <!-- Rail langkah: tahap yang sedang berjalan ditandai skrip di bawah.
                     Urutan tahap 2 & 3 MENIRU PERSIS urutan gerbang di #simpan3
                     (baris merah dulu, baru balance per jurnal). -->
                <div class="mju-steps" id="mju_steps">
                    <span class="mju-step is-active" id="mju_step1"><span class="mju-step-n"><i class="fa fa-check"></i><b>1</b></span> Upload file</span>
                    <i class="fa fa-angle-right mju-step-sep"></i>
                    <span class="mju-step" id="mju_step2"><span class="mju-step-n"><i class="fa fa-check"></i><b>2</b></span> Review lines</span>
                    <i class="fa fa-angle-right mju-step-sep"></i>
                    <span class="mju-step" id="mju_step3"><span class="mju-step-n"><i class="fa fa-check"></i><b>3</b></span> Check balance</span>
                    <i class="fa fa-angle-right mju-step-sep"></i>
                    <span class="mju-step" id="mju_step4"><span class="mju-step-n"><i class="fa fa-check"></i><b>4</b></span> Save</span>
                    <span class="mju-step-hint" id="mju_step_hint">Choose an Excel file, then press Upload.</span>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <label class="app-flabel" for="fileUpload">Upload Journal File</label>
                        <!-- Elemen ber-id uploadBox + kelas .mju-upload-bar dipakai handler drag
                             (kelas .is-drag) di create_memorial_journal.php. -->
                        <div class="mju-upload-bar" id="uploadBox">
                            <!-- Satu-satunya cara membuka dialog berkas: input-nya
                                 display:none dan tidak ada handler klik di JS. -->
                            <label for="fileUpload" class="mju-drop">
                                <i class="fa fa-cloud-upload"></i>
                                <span>
                                    <span class="mju-drop-txt d-block">Click or drag an Excel file here</span>
                                    <span class="mju-drop-file d-block" id="fileName">No file selected</span>
                                </span>
                            </label>
                            <div class="mju-upload-act">
                                <button type="button" id="btnUpload" class="app-btn app-btn-success app-btn-sm">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                                <a target="_blank" href="format-excel/format_upload.xls?ver=<?php echo time(); ?>"
                                   class="app-btn app-btn-dark app-btn-sm">
                                    <i class="fa fa-file-excel-o"></i> Template
                                </a>
                            </div>
                            <!-- TANPA atribut accept: accept membuat berkas ter-filter /
                                 abu-abu di dialog sehingga malah tidak bisa dipilih.
                                 TANPA name: kunci 'file' dikirim JS lewat FormData;
                                 kalau diberi name, berkasnya ikut terkirim lagi saat Save. -->
                            <input type="file" id="fileUpload" style="display:none">
                        </div>
                    </div>

                    <div class="col-lg-5 mt-3 mt-lg-0">
                        <label class="app-flabel">What the file must contain</label>
                        <ul class="mju-tips">
                            <li><i class="fa fa-check-circle"></i><span>Fill the <b>UPLOAD</b> sheet of the latest template &mdash; the column order is verified on upload.</span></li>
                            <li><i class="fa fa-check-circle"></i><span>One file may hold several journals; rows are grouped by <b>No Journal</b>.</span></li>
                            <li><i class="fa fa-check-circle"></i><span>Every journal must balance: <b>total debit = total credit</b>.</span></li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- ============ SEKSI 1: baris jurnal (detail) ============ -->
            <div class="mju-sec">
                <div class="mju-sec-head">
                    <label class="mju-sec-title mb-0">
                        <b>Journal Lines</b>
                        <span class="mju-count" id="mju_count_detail"></span>
                        <!-- Cermin gerbang #1 di #simpan3 ("Masih ada data mapping
                             (filter) yang kosong"). -->
                        <span class="mju-chip mju-chip-bad" id="mju_chip_bad">
                            <i class="fa fa-exclamation-triangle"></i><span id="mju_chip_bad_txt"></span>
                        </span>
                    </label>
                    <div class="mju-tools">
                        <label class="mju-len-wrap" for="mju_len_detail">Rows
                            <select class="mju-len" id="mju_len_detail">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                        <span class="app-search">
                            <i class="fa fa-search"></i>
                            <!-- TANPA name: apa pun yang ber-name di dalam #form-data3
                                 ikut terkirim ke save_mj_upload_fix.php lewat
                                 new FormData($('#form-data3')[0]). -->
                            <input type="text" id="mju_search_detail" autocomplete="off"
                                   aria-label="Search journal lines"
                                   placeholder="Search COA / cost center / description...">
                        </span>
                    </div>
                </div>

                <!-- .app-loading-wrap: overlay loading skin (app-skin-form.css) yang
                     sekaligus menyembunyikan kotak "Processing" bawaan DataTables. -->
                <div class="app-loading-wrap" id="mju_load_detail">
                    <div class="app-loading">
                        <div class="app-loading-box">
                            <div class="app-spinner"><span>NAG</span></div>
                            <div class="app-loading-text">Loading lines...</div>
                        </div>
                    </div>
                    <div class="mju-tbl-wrap" id="mju_wrap_detail">
                        <!-- TEPAT 15 <th>, URUTAN TERKUNCI. Harus sama persis dengan
                             columns[] datatable3 di create_memorial_journal.php.
                             Debit & Credit = <th> ke-12 & ke-13, yaitu columnDefs
                             targets [11,12] (0-based) — nomor itu POSISIONAL.
                             Jumlah <th> yang salah membuat DataTables melempar saat
                             init, datatable3 tetap undefined, dan #simpan3 mati di
                             baris pertamanya (datatable3.rows()). -->
                        <table id="table-upload-mj" class="table table-hover table-sm mju-tbl mju-tbl-detail">
                            <thead>
                                <tr>
                                    <th>Profit Center</th>
                                    <th>COA</th>
                                    <th>Cost Center</th>
                                    <th>Reff Document</th>
                                    <th>Reff Date</th>
                                    <th>No Faktur</th>
                                    <th>Faktur Date</th>
                                    <th>Supplier</th>
                                    <th>Buyer</th>
                                    <th>Worksheet</th>
                                    <th>Curr</th>
                                    <th class="mju-num">Debit</th>
                                    <th class="mju-num">Credit</th>
                                    <th>Description</th>
                                    <th>Include SB1</th>
                                </tr>
                            </thead>
                            <!-- WAJIB kosong: datanya dari ajax. -->
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="mju-tbl-foot" id="mju_foot_detail"></div>

                <div class="mju-legend" id="mju_legend">
                    <span class="mju-legend-sw"></span>
                    <span>Red rows have no valid COA + Cost Center combination for their profit center.
                          Save stays blocked until they are corrected in the file and uploaded again.</span>
                </div>
            </div>

            <!-- ============ SEKSI 2: rekap per No Journal ============ -->
            <div class="mju-sec">
                <div class="mju-sec-head">
                    <label class="mju-sec-title mb-0">
                        <b>Journal Summary</b>
                        <span class="mju-count" id="mju_count_group"></span>
                        <!-- Cermin gerbang #2 di #simpan3 ("Journal Tidak Balance"). -->
                        <span class="mju-chip mju-chip-bad" id="mju_chip_unbal">
                            <i class="fa fa-exclamation-triangle"></i><span id="mju_chip_unbal_txt"></span>
                        </span>
                    </label>
                    <!-- .is-auto: toolbar ini baru muncul kalau jurnalnya lebih dari
                         satu halaman. Berkas biasa berisi 2-3 jurnal — semuanya sudah
                         terlihat, jadi search & pemilih baris cuma perabot berulang. -->
                    <div class="mju-tools is-auto" id="mju_tools_group">
                        <label class="mju-len-wrap" for="mju_len_group">Rows
                            <select class="mju-len" id="mju_len_group">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                        <span class="app-search">
                            <i class="fa fa-search"></i>
                            <input type="text" id="mju_search_group" autocomplete="off"
                                   aria-label="Search journal summary"
                                   placeholder="Search journal no / type / description...">
                        </span>
                    </div>
                </div>

                <div class="app-loading-wrap" id="mju_load_group">
                    <div class="app-loading">
                        <div class="app-loading-box">
                            <div class="app-spinner"><span>NAG</span></div>
                            <div class="app-loading-text">Loading journals...</div>
                        </div>
                    </div>
                    <div class="mju-tbl-wrap mju-sm" id="mju_wrap_group">
                        <!-- TEPAT 8 <th>, urutannya HARUS sama dengan columns[]
                             datatable4. Debit & Credit = <th> ke-5 & ke-6, yaitu
                             columnDefs targets [4,5] (0-based). -->
                        <table id="table-upload-mj-group" class="table table-hover table-sm mju-tbl mju-tbl-group">
                            <thead>
                                <tr>
                                    <th>No Journal</th>
                                    <th>Journal Date</th>
                                    <th>Type Journal</th>
                                    <th>Curr</th>
                                    <th class="mju-num">Debit</th>
                                    <th class="mju-num">Credit</th>
                                    <th>Description</th>
                                    <th>Include SB1</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="mju-tbl-foot" id="mju_foot_group"></div>
            </div>

            <!-- ============ SEKSI 3: ringkasan balance ============ -->
            <div class="mju-sec">
                <div class="mju-sec-head">
                    <label class="mju-sec-title mb-0">
                        <b>Balance Summary</b>
                        <!-- Muncul HANYA kalau berkasnya benar-benar berisi baris ber-PC
                             di luar NAG/NAK, yang tidak dijumlah drawCallback induk. -->
                        <span class="mju-chip mju-chip-warn" id="mju_chip_pc">
                            <i class="fa fa-exclamation-triangle"></i><span id="mju_chip_pc_txt"></span>
                        </span>
                    </label>
                </div>

                <div class="row">
                    <!-- NAG -->
                    <div class="col-md-4 mb-2">
                        <div class="mju-tot tone-nag">
                            <div class="mju-tot-name"><?php echo htmlspecialchars($mjuPcName['NAG']); ?><span class="mju-tot-code">NAG</span></div>
                            <div class="mju-tot-split">
                                <div class="mju-tot-col is-deb is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Debit column exactly as uploaded — currencies are NOT converted">Debit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_debit_nag3" name="tot_debit_nag3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-deb">
                                <span class="mju-tot-lbl" title="The same debit rows converted to IDR at the journal-date rate">Debit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_debit_idr_nag3" name="tot_debit_idr_nag3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Credit column exactly as uploaded — currencies are NOT converted">Credit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_credit_nag3" name="tot_credit_nag3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre">
                                <span class="mju-tot-lbl" title="The same credit rows converted to IDR at the journal-date rate">Credit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_credit_idr_nag3" name="tot_credit_idr_nag3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            </div>
                            <!-- Nilai mentah (tanpa pemisah ribuan) — diisi drawCallback,
                                 ikut terkirim saat Save lewat FormData(#form-data3). -->
                            <input type="hidden" id="h_tot_debit_nag3" name="h_tot_debit_nag3">
                            <input type="hidden" id="h_tot_credit_nag3" name="h_tot_credit_nag3">
                            <input type="hidden" id="h_tot_debit_idr_nag3" name="h_tot_debit_idr_nag3">
                            <input type="hidden" id="h_tot_credit_idr_nag3" name="h_tot_credit_idr_nag3">
                        </div>
                    </div>

                    <!-- NAK -->
                    <div class="col-md-4 mb-2">
                        <div class="mju-tot tone-nak">
                            <div class="mju-tot-name"><?php echo htmlspecialchars($mjuPcName['NAK']); ?><span class="mju-tot-code">NAK</span></div>
                            <div class="mju-tot-split">
                                <div class="mju-tot-col is-deb is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Debit column exactly as uploaded — currencies are NOT converted">Debit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_debit_nak3" name="tot_debit_nak3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-deb">
                                <span class="mju-tot-lbl" title="The same debit rows converted to IDR at the journal-date rate">Debit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_debit_idr_nak3" name="tot_debit_idr_nak3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Credit column exactly as uploaded — currencies are NOT converted">Credit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_credit_nak3" name="tot_credit_nak3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre">
                                <span class="mju-tot-lbl" title="The same credit rows converted to IDR at the journal-date rate">Credit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_credit_idr_nak3" name="tot_credit_idr_nak3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            </div>
                            <input type="hidden" id="h_tot_debit_nak3" name="h_tot_debit_nak3">
                            <input type="hidden" id="h_tot_credit_nak3" name="h_tot_credit_nak3">
                            <input type="hidden" id="h_tot_debit_idr_nak3" name="h_tot_debit_idr_nak3">
                            <input type="hidden" id="h_tot_credit_idr_nak3" name="h_tot_credit_idr_nak3">
                        </div>
                    </div>

                    <!-- GRAND TOTAL + verdict balance -->
                    <div class="col-md-4 mb-2">
                        <div class="mju-tot tone-all" id="mju_tot_all">
                            <div class="mju-tot-name">Grand Total<span class="mju-tot-code">NAG + NAK</span></div>
                            <div class="mju-tot-split">
                                <div class="mju-tot-col is-deb is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Debit column exactly as uploaded — currencies are NOT converted">Debit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_debit3" name="tot_debit3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-deb">
                                <span class="mju-tot-lbl" title="The same debit rows converted to IDR at the journal-date rate">Debit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_debit_idr3" name="tot_debit_idr3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre is-sub">
                                <span class="mju-tot-lbl" title="Sum of the Credit column exactly as uploaded — currencies are NOT converted">Credit</span>
                                <input type="text" class="mju-tot-val is-sub" id="tot_credit3" name="tot_credit3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                                <div class="mju-tot-col is-cre">
                                <span class="mju-tot-lbl" title="The same credit rows converted to IDR at the journal-date rate">Credit IDR</span>
                                <input type="text" class="mju-tot-val" id="tot_credit_idr3" name="tot_credit_idr3" value="0.00" readonly tabindex="-1" aria-readonly="true">
                            </div>
                            </div>
                            <!-- Baris sendiri: pesan tidak balance bisa panjang. -->
                            <div class="mju-tot-bal" id="mju_balance">
                                <span class="mju-bal-idle"><i class="fa fa-clock-o"></i>Waiting for an uploaded file</span>
                            </div>
                            <input type="hidden" id="h_tot_debit3" name="h_tot_debit3">
                            <input type="hidden" id="h_tot_credit3" name="h_tot_credit3">
                            <input type="hidden" id="h_tot_debit_idr3" name="h_tot_debit_idr3">
                            <input type="hidden" id="h_tot_credit_idr3" name="h_tot_credit_idr3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ AKSI ============ -->
            <div class="form-row">
                <div class="col-md-12 mt-2 mb-2">
                    <div class="app-actions">
                        <button type="button" class="app-btn app-btn-primary" name="simpan3" id="simpan3"><i class="fa fa-floppy-o"></i> Save</button>
                        <!-- Atribut id batal sengaja TIDAK dipasang: tidak ada JS yang
                             memakainya, dan id yang sama sudah ada di mj_input.php &
                             mj_from_hris.php pada halaman yang sama. -->
                        <button type="button" name="batal" id="batal" class="app-btn app-btn-danger" onclick="location.href='memorial-journal.php'"><i class="fa fa-angle-double-left"></i> Back</button>
                        <button type="button" class="app-btn app-btn-warning" name="reset" id="reset"><i class="fa fa-repeat"></i> Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
/* ---------------------------------------------------------------------------
   Skrip KHUSUS TAMPILAN tab Upload Journal. Seluruh logika upload / simpan /
   reset ke server tetap di create_memorial_journal.php dan TIDAK disentuh.
   Yang dikerjakan blok ini hanya:
     1. toolbar sendiri (search + jumlah baris) pengganti chrome bawaan
        DataTables yang selama ini terjebak di dalam kotak scroll;
     2. memindahkan info & pagination ke luar kotak scroll;
     3. mengisi pil jumlah baris, chip peringatan, rail langkah, dan verdict
        balance — semuanya dari sumber data YANG SAMA dengan gerbang #simpan3;
     4. memiliki (bukan menumpuk) handler nama berkas, dan membersihkan nama
        berkas saat Reset.

   KENAPA DOMContentLoaded: berkas ini di-include SEBELUM <script src="jquery">,
   jadi saat blok ini di-parse, $ belum ada. DOMContentLoaded adalah API native
   yang baru jalan setelah seluruh <script> sinkron dimuat.
   KENAPA $(function(){}) DI DALAMNYA: listener native ini terpasang lebih dulu
   daripada listener milik jQuery, jadi ia jalan SEBELUM antrean $(document).ready
   dijalankan. Dengan mendaftarkan ulang lewat $(function(){}), kode di bawah
   dipastikan berjalan SESUDAH datatable3 & datatable4 selesai dibuat.
   Pola ini sebaiknya juga dipakai mj_input.php & mj_from_hris.php.
   --------------------------------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') { return; }
    var $ = jQuery;

    var SEL_DETAIL = '#table-upload-mj';
    var SEL_GROUP  = '#table-upload-mj-group';

    /* Profit center yang BENAR-BENAR dijumlah drawCallback datatable3 di
       create_memorial_journal.php. Dipatok di sini karena di sana pun dipatok
       (if pc === 'NAG' / if pc === 'NAK'). Kalau nanti kartu total ditambah,
       tambahkan kodenya di kedua tempat. */
    var PC_TOTALLED = ['NAG', 'NAK'];

    /* Ringkasan keadaan terakhir kedua tabel. Nilai yang mahal dihitung SEKALI
       per muatan data (lihat needScan*), bukan tiap draw. */
    var st = {
        rows: 0, bad: 0, offPc: 0, offPcCodes: [],
        journals: 0, unbalanced: 0, diff: 0,
        hasData: false, detailReady: false, groupReady: false
    };
    var needScanDetail = true;
    var needScanGroup  = true;

    function dt(sel) {
        return ($.fn.DataTable && $.fn.DataTable.isDataTable(sel)) ? $(sel).DataTable() : null;
    }
    function fmt(n) {
        return (parseFloat(n) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function countText(shown, total) {
        return (shown === total) ? (total + ' rows') : (shown + ' of ' + total + ' rows');
    }

    /* ---- verdict balance di kartu Grand Total ----------------------------
       PENTING — verdict ini dihitung dari TABEL REKAP (datatable4), memakai
       pembulatan 2 desimal PER JURNAL, yaitu pemeriksaan yang sama persis
       dengan gerbang #2 di #simpan3. Sengaja BUKAN dari selisih
       #h_tot_debit3 - #h_tot_credit3, karena dua angka itu:
         a. hanya menjumlah kode_pc NAG & NAK (baris PC lain tidak ikut), dan
         b. menjumlahkan float mentah, sehingga ribuan baris bisa mendarat di
            0.01 walaupun setiap jurnalnya sendiri sudah balance.
       Kalau verdict dipasang di sana, layar akan berteriak "NOT balanced —
       Save is blocked" padahal Save berjalan mulus. Baris di luar NAG/NAK
       dilaporkan terpisah sebagai peringatan, bukan sebagai error balance. */
    function paintBalance() {
        var tile = $('#mju_tot_all'), box = $('#mju_balance');
        var warn = st.offPc > 0
            ? '<span class="mju-bal-warn"><i class="fa fa-info-circle"></i>' + st.offPc +
              ' line(s) in profit center ' + st.offPcCodes.join(', ') +
              ' are not included in the cards above.</span>'
            : '';

        if (!st.detailReady || !st.groupReady || !st.hasData) {
            tile.removeClass('is-ok is-bad');
            box.html('<span class="mju-bal-idle"><i class="fa fa-clock-o"></i>Waiting for an uploaded file</span>');
            /* Tanpa data, penanda merah/hijau inline dari drawCallback tidak
               bermakna (0.00 vs 0.00 selalu "hijau") — dinetralkan supaya
               angkanya tidak terbaca sebagai sudah tervalidasi. */
            $('#tot_debit3, #tot_credit3, #tot_debit_idr3, #tot_credit_idr3').css({ 'color': '', 'font-weight': '' });
            return;
        }
        if (st.unbalanced > 0) {
            tile.removeClass('is-ok').addClass('is-bad');
            box.html('<span class="mju-bal-bad"><i class="fa fa-exclamation-triangle"></i>' +
                     st.unbalanced + ' of ' + st.journals + ' journal(s) NOT balanced' +
                     (st.diff ? ' &mdash; total difference ' + fmt(st.diff) : '') +
                     '</span>' + warn);
        } else {
            tile.removeClass('is-bad').addClass('is-ok');
            box.html('<span class="mju-bal-ok"><i class="fa fa-check-circle"></i>All ' + st.journals +
                     ' journal(s) balanced &mdash; debit = credit</span>' + warn);
        }
    }

    /* ---- rail langkah — urutannya MENIRU gerbang #simpan3 ---------------- */
    function paintSteps() {
        var hint = $('#mju_step_hint').removeClass('is-bad');
        $('#mju_steps .mju-step').removeClass('is-active is-done is-bad');

        if (!st.detailReady || !st.groupReady || !st.hasData) {
            $('#mju_step1').addClass('is-active');
            hint.text('Choose an Excel file, then press Upload.');
            return;
        }
        $('#mju_step1').addClass('is-done');

        if (st.bad > 0) {
            $('#mju_step2').addClass('is-bad');
            hint.addClass('is-bad').text(st.bad + ' line(s) have no valid COA / Cost Center mapping — fix them in the file and upload again.');
            return;
        }
        $('#mju_step2').addClass('is-done');

        if (st.unbalanced > 0) {
            $('#mju_step3').addClass('is-bad');
            hint.addClass('is-bad').text(st.unbalanced + ' journal(s) are not balanced — Save is blocked.');
            return;
        }
        $('#mju_step3').addClass('is-done');
        $('#mju_step4').addClass('is-active');
        hint.text(st.rows + ' line(s) in ' + st.journals + ' journal(s) are ready to save.');
    }

    /* Angka besar bisa terpotong di kartu sempit, dan text-overflow:ellipsis
       pada <input> tidak bisa diandalkan di semua peramban. Nilai penuhnya
       disalin ke title supaya selalu bisa dibaca lewat hover. 12 elemen saja,
       jadi biayanya nol. */
    function syncTotalTitles() {
        $('.mju-tot-val').each(function () { this.title = this.value; });
    }

    /* ---- tabel detail ----------------------------------------------------
       xhr.dt menandai "data BARU datang" -> pemindaian penuh boleh jalan sekali.
       draw.dt sendiri dipicu juga oleh paging & tiap ketikan pencarian; di sana
       hanya pil jumlah baris yang diperbarui. rows().every() membuat satu objek
       API per baris — memanggilnya tiap draw membuat paging & mengetik terasa
       berat pada unggahan ribuan baris. */
    $(SEL_DETAIL).on('xhr.dt', function () { needScanDetail = true; });

    /* Kolom Profit Center / COA / Supplier / Description dipotong elipsis lewat CSS
       supaya Debit & Credit muat di layar. Nilai utuhnya dikembalikan sebagai
       tooltip di sini — tanpa ini teks yang terpotong tidak bisa dibaca sama sekali.
       Hanya baris yang SEDANG tampil (maks 1 halaman), jadi biayanya kecil. */
    function syncCellTitles() {
        $(SEL_DETAIL + ' tbody tr').each(function () {
            var td = this.cells;
            [0, 1, 7, 13].forEach(function (i) {
                if (td[i]) {
                    var txt = (td[i].textContent || '').trim();
                    if (txt && txt !== '-') { td[i].setAttribute('title', txt); }
                }
            });
        });
    }

    $(SEL_DETAIL).on('draw.dt', function () {
        var t = dt(SEL_DETAIL);
        if (!t) { return; }

        syncCellTitles();

        var info = t.page.info();
        $('#mju_count_detail').text(countText(info.recordsDisplay, info.recordsTotal));

        st.rows = info.recordsTotal;
        st.hasData = info.recordsTotal > 0;
        st.detailReady = true;

        if (needScanDetail) {
            needScanDetail = false;
            var bad = 0, offPc = 0, codes = {};
            /* Pemeriksaan filter kosong ini SAMA PERSIS dengan gerbang #1 di
               #simpan3, termasuk perlakuan terhadap "-" . */
            t.rows().every(function () {
                var d = this.data();
                var f = (d.filter || '').toString().trim();
                if (f === '' || f === '-') { bad++; }
                var pc = (d.kode_pc || '').toString().toUpperCase().trim();
                if (pc !== '' && $.inArray(pc, PC_TOTALLED) === -1) { offPc++; codes[pc] = 1; }
            });
            st.bad = bad;
            st.offPc = offPc;
            st.offPcCodes = Object.keys(codes).sort();

            $('#mju_chip_bad').toggleClass('is-on', bad > 0);
            $('#mju_chip_bad_txt').text(bad + (bad === 1 ? ' row needs mapping' : ' rows need mapping'));
            $('#mju_legend').toggleClass('is-on', bad > 0);

            $('#mju_chip_pc').toggleClass('is-on', offPc > 0);
            $('#mju_chip_pc_txt').text(offPc + (offPc === 1 ? ' line' : ' lines') +
                ' outside ' + PC_TOTALLED.join(' / ') + ' — not totalled');
        }

        /* drawCallback datatable3 (yang mengisi seluruh input total) dijalankan
           DataTables SEBELUM event draw ini dipicu, jadi angka di kartu sudah
           final saat title-nya disalin. */
        syncTotalTitles();
        paintBalance();
        paintSteps();
    });

    /* ---- tabel rekap ----------------------------------------------------- */
    $(SEL_GROUP).on('xhr.dt', function () { needScanGroup = true; });

    $(SEL_GROUP).on('draw.dt', function () {
        var t = dt(SEL_GROUP);
        if (!t) { return; }

        var info = t.page.info();
        $('#mju_count_group').text(countText(info.recordsDisplay, info.recordsTotal));

        st.journals = info.recordsTotal;
        st.groupReady = true;

        /* recordsTotal tidak terpengaruh pencarian, jadi toolbar tidak
           berkedip hilang saat user mengetik. */
        $('#mju_tools_group').toggleClass('is-on', info.recordsTotal > 10);

        if (needScanGroup) {
            needScanGroup = false;
            var unbal = 0, diff = 0;
            /* SAMA PERSIS dengan gerbang #2 di #simpan3: bulatkan debit & credit
               tiap jurnal ke 2 desimal lalu bandingkan. */
            t.rows().every(function () {
                var r = this.data();
                var deb = Math.round((parseFloat(r.debit) || 0) * 100) / 100;
                var cre = Math.round((parseFloat(r.credit) || 0) * 100) / 100;
                if (deb !== cre) { unbal++; diff += (deb - cre); }
            });
            st.unbalanced = unbal;
            st.diff = Math.round(diff * 100) / 100;

            $('#mju_chip_unbal').toggleClass('is-on', unbal > 0);
            $('#mju_chip_unbal_txt').text(unbal + (unbal === 1 ? ' journal not balanced' : ' journals not balanced'));
        }

        paintBalance();
        paintSteps();
    });

    /* ---- overlay loading: HANYA saat ajax, bukan tiap draw ----------------
       processing.dt juga menyala saat paging & mengetik; memakainya membuat
       overlay berkedip di tiap ketikan. */
    $(SEL_DETAIL).on('preXhr.dt', function () { $('#mju_load_detail').addClass('is-loading'); });
    $(SEL_DETAIL).on('xhr.dt error.dt', function () { $('#mju_load_detail').removeClass('is-loading'); });
    $(SEL_GROUP).on('preXhr.dt', function () { $('#mju_load_group').addClass('is-loading'); });
    $(SEL_GROUP).on('xhr.dt error.dt', function () { $('#mju_load_group').removeClass('is-loading'); });

    /* ---- nama berkas: handler ini MEMILIKI, bukan menumpuk ----------------
       Handler change & drop milik create_memorial_journal.php dilepas dulu,
       baru dipasang milik berkas ini. Tanpa .off(), KEDUA handler bertahan dan
       hasilnya cuma benar karena kebetulan urutan pendaftaran — dan label akan
       berbalik ke bahasa Indonesia ("Belum ada file") begitu pilihan berkas
       dikosongkan, karena string itu masih dipatok di handler induk.
       Handler drop DITULIS ULANG UTUH di sini, termasuk penyalinan berkas ke
       input, supaya melepas milik induk tidak menghilangkan fungsinya.
       dragover / dragleave milik induk TIDAK dilepas: keduanya hanya
       menyalakan-memadamkan kelas .is-drag. */
    $('#fileUpload').off('change');
    $('#uploadBox').off('drop');

    function showFile(f) {
        // .text(), bukan .html(): nama berkas aneh tidak boleh tersisip sbg HTML.
        if (f) { $('#fileName').addClass('has-file').text(f.name); }
        else   { $('#fileName').removeClass('has-file').text('No file selected'); }
    }
    $('#fileUpload').on('change', function () { showFile(this.files && this.files[0]); });
    $('#uploadBox').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('is-drag');
        var files = e.originalEvent && e.originalEvent.dataTransfer ? e.originalEvent.dataTransfer.files : null;
        if (files && files.length > 0) {
            document.getElementById('fileUpload').files = files;
            showFile(files[0]);
        }
    });

    /* ---- Reset: bersihkan juga pemilih berkas -----------------------------
       Handler #reset milik halaman induk (blok <script> terpisah) mengosongkan
       staging di server lalu memuat ulang kedua tabel, tapi TIDAK menyentuh
       #fileUpload / #fileName. Tanpa baris di bawah, setelah Reset tabelnya
       kosong dan rail kembali ke "Choose an Excel file", sementara nama berkas
       lama masih terpampang tebal — panel-nya berbohong. Handler ini
       DITAMBAHKAN (tidak melepas milik induk), jadi reset server tetap jalan. */
    $('#reset').on('click', function () {
        $('#fileUpload').val('');
        showFile(null);
        $('#mju_search_detail, #mju_search_group').val('');
        var td = dt(SEL_DETAIL); if (td) { td.search(''); }
        var tg = dt(SEL_GROUP);  if (tg) { tg.search(''); }
    });

    /* ---- toolbar sendiri: search + jumlah baris --------------------------- */
    function wireTools(sel, searchSel, lenSel, wrapSel, footSel) {
        var timer = null;
        $(searchSel).on('input', function () {
            var v = this.value;
            clearTimeout(timer);
            // Debounce 200ms: dengan ribuan baris, menggambar ulang tiap
            // ketikan terasa berat.
            timer = setTimeout(function () {
                var t = dt(sel);
                if (t) { t.search(v).draw(); }
            }, 200);
        });
        $(lenSel).on('change', function () {
            var t = dt(sel);
            if (t) { t.page.len(parseInt(this.value, 10) || 10).draw(); }
        });

        // Chrome bawaan: info & pagination dipindah ke luar kotak scroll,
        // baris "Show N entries" & "Search:" disembunyikan (sudah ada
        // gantinya). .mju-js ditambahkan PALING AKHIR — kalau langkah ini tidak
        // pernah tercapai, chrome bawaan tetap terlihat dan tabel tetap bisa
        // dicari & dipaginasi.
        var wrapper = $(sel + '_wrapper');
        if (!wrapper.length) { return false; }
        wrapper.find('.dataTables_info, .dataTables_paginate').appendTo(footSel);
        $(wrapSel).addClass('mju-js');
        return true;
    }

    // Dijalankan lewat antrean ready jQuery supaya kedua DataTable sudah jadi.
    $(function () {
        wireTools(SEL_DETAIL, '#mju_search_detail', '#mju_len_detail', '#mju_wrap_detail', '#mju_foot_detail');
        wireTools(SEL_GROUP,  '#mju_search_group',  '#mju_len_group',  '#mju_wrap_group',  '#mju_foot_group');
        syncTotalTitles();
        paintBalance();
        paintSteps();
    });
});
</script>
