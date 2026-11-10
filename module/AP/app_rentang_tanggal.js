/* ============================================================================
   PASANGAN TANGGAL From/To - "To" tidak boleh lebih kecil dari "From".
   Diminta pemakai 10 Nov 2026, berlaku untuk beberapa laporan AP sekaligus.

   SATU BERKAS, BUKAN SALINAN PER HALAMAN. Aturannya sama di semua layar dan
   kalau disalin, perbaikan berikutnya harus ditempel berkali-kali - itu jenis
   kebocoran yang paling lama ketahuannya.

   DUA LAPIS, dan dua-duanya perlu:
     1. Pemilihnya (bootstrap-datepicker) dikunci - tanggal sebelum "From"
        tidak bisa diklik di kalender "To", dan sebaliknya.
     2. Penjaga saat tombol Search/Export ditekan - karena isiannya BUKAN
        <input type="date">, melainkan teks biasa yang masih bisa diketik
        tangan atau ditempel dari clipboard, melewati kalendernya.

   TIDAK BERGANTUNG PADA FORMAT TANGGAL. Halaman memakai format berbeda
   ('yyyy-mm-dd' di Payable Card Statement, 'dd-mm-yyyy' di laporan proyeksi),
   jadi tanggalnya dibaca lewat DPGlobal.parseDate dgn format milik pemilih
   itu sendiri - bukan membandingkan teksnya, yang akan salah besar di
   'dd-mm-yyyy'.

   SELURUHNYA BEKERJA DENGAN TEKS, BUKAN OBJEK Date. Ini disengaja:
     - menyalin nilai  -> $sampai.val($dari.val()), formatnya pasti sama;
     - memasang batas  -> setStartDate($dari.val()), dan di dalam
       bootstrap-datepicker teks itu diurai dgn parseDate yang SAMA dgn yang
       dipakai update() waktu membandingkan - jadi batasnya tidak pernah
       meleset sehari.
   Melempar objek Date ke setDate/setStartDate membuka urusan konversi
   UTC <-> waktu setempat (_local_to_utc) yang bisa menggeser tanggal di zona
   tertentu. Jalur teks tidak punya masalah itu sama sekali.

   Dipakai di: payable_card_statement2.php, ap_projection_report.php,
               ap_history_projection.php.
   ============================================================================ */
(function (w, $) {
    'use strict';

    var PESAN = 'The To date must not be earlier than the From date.';

    /* Baca isian sbg Date, lewat format milik pemilih itu sendiri.
       null kalau kosong, tidak sah, atau pemilihnya belum dipasang. */
    function ambil($el) {
        if (!$el || !$el.length) { return null; }
        var dp = $el.data('datepicker');
        var DP = $.fn.datepicker && $.fn.datepicker.DPGlobal;
        if (!dp || !DP) { return null; }
        var d = null;
        try {
            d = DP.parseDate($.trim($el.val() || ''), dp.o.format, dp.o.language);
        } catch (e) { d = null; }
        return (d instanceof Date && !isNaN(d.getTime())) ? d : null;
    }

    /* Bandingkan HARI-nya saja, dan pakai penampil UTC - parseDate memang
       membangun tengah malam UTC. Memakai getFullYear() biasa di sini akan
       mundur sehari di zona waktu negatif. */
    function angkaHari(d) {
        return d.getUTCFullYear() * 10000 + (d.getUTCMonth() + 1) * 100 + d.getUTCDate();
    }

    function peringatkan() {
        if (w.Swal) {
            w.Swal.fire({ icon: 'warning', title: 'Invalid Date Range', text: PESAN });
        } else {
            w.alert(PESAN);
        }
    }

    /* ------------------------------------------------------------------
       apRentangTanggal('id-dari', 'id-sampai')
       Mengunci kalendernya. Dipanggil SESUDAH .datepicker() dipasang.
       ------------------------------------------------------------------ */
    w.apRentangTanggal = function (idDari, idSampai) {
        var $dari = $('#' + idDari), $sampai = $('#' + idSampai);
        if (!$dari.length || !$sampai.length) { return; }

        /* Batas bawah asli milik kalender "To" - mis. Payable Card Statement
           memasang startDate: "2026-07-01" waktu membuat pemilihnya. Disimpan
           supaya bisa dikembalikan kalau "From" dikosongkan; tanpa ini,
           mengosongkan From diam-diam ikut menghapus lantai tahun itu. */
        var dpSampai = $sampai.data('datepicker');
        var batasAsli = (dpSampai && dpSampai.o) ? dpSampai.o.startDate : null;

        /* Penjaga putaran: menyalin nilai memicu changeDate lagi, dan tanpa
           bendera ini kedua penangan di bawah saling memanggil tanpa henti. */
        var sibuk = false;

        /* true kalau ada yang diperbaiki. */
        function perbaiki() {
            var a = ambil($dari), b = ambil($sampai);
            if (!a || !b || angkaHari(b) >= angkaHari(a)) { return false; }
            $sampai.val($dari.val());
            try { $sampai.datepicker('update'); } catch (e) { /* abaikan */ }
            return true;
        }

        function kunci() {
            var tDari = $.trim($dari.val() || '');
            var tSampai = $.trim($sampai.val() || '');
            try {
                $sampai.datepicker('setStartDate',
                    tDari !== '' ? tDari : (batasAsli === -Infinity ? null : batasAsli));
            } catch (e) { /* abaikan */ }
            try {
                $dari.datepicker('setEndDate', tSampai !== '' ? tSampai : null);
            } catch (e) { /* abaikan */ }
        }

        /* "From" digeser maju melewati "To" -> "To" ikut maju. Ini bukan
           kesalahan pemakai, cuma akibat wajar dari menggeser awal periode,
           jadi DIAM-DIAM saja - tidak perlu kotak peringatan. */
        $dari.on('changeDate change', function () {
            if (sibuk) { return; }
            sibuk = true;
            perbaiki();
            kunci();
            sibuk = false;
        });

        /* "To" diketik mundur melewati "From" -> dikembalikan ke "From" dan
           pemakai DIBERI TAHU, karena yang ini memang salah isi. */
        $sampai.on('changeDate change', function () {
            if (sibuk) { return; }
            sibuk = true;
            var dikoreksi = perbaiki();
            kunci();
            sibuk = false;
            if (dikoreksi) { peringatkan(); }
        });

        /* Pemasangan awal. Nilai bawaan halaman diperbaiki dulu BARU
           dikunci - kalau urutannya dibalik dan nilai awalnya kebetulan
           terbalik, update() di dalam setEndDate membuang tanggal yang di
           luar batas dan isiannya jadi tidak terbaca lagi. */
        sibuk = true;
        perbaiki();
        kunci();
        sibuk = false;
    };

    /* ------------------------------------------------------------------
       apRentangSah('id-dari', 'id-sampai')  ->  true / false
       Penjaga lapis kedua, dipanggil di awal fungsi pencarian/ekspor.
       Mengembalikan true kalau salah satu isian kosong - mengosongkan
       tanggal bukan urusan penjaga ini.
       ------------------------------------------------------------------ */
    w.apRentangSah = function (idDari, idSampai) {
        var a = ambil($('#' + idDari)), b = ambil($('#' + idSampai));
        if (!a || !b) { return true; }
        if (angkaHari(b) < angkaHari(a)) {
            peringatkan();
            return false;
        }
        return true;
    };

})(window, jQuery);
