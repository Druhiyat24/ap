/* Tema NDS: semua <select> memakai select2 ukuran kecil.
   - Nilai tetap disimpan di <select> asli -> yang dikirim ke server TIDAK berubah.
   - Halaman lama yang memakai bootstrap-select tetap jalan: panggilan
     .selectpicker(...) dialihkan ke select2 (adaptor di bawah).
   Dimuat header.php / Dashboard hanya kalau TAMPILAN_BARU & TEMA_SELECT2 = true. */
(function () {
    var skrip = document.currentScript;
    var dasar = skrip ? skrip.src.replace(/js\/app-tema\.js.*$/, '') : '../';   // alamat folder module/
    var antrian = [];

    document.addEventListener('DOMContentLoaded', function () {
        var $ = window.jQuery;
        if (!$ || !$.fn) { return; }            // halaman tanpa jQuery: biarkan apa adanya
        pasangAdaptor($);
        tambalVal($);
        siapkanSelect2($, function () {
            for (var i = 0; i < antrian.length; i++) { pasang($, antrian[i], true); }
            antrian = [];
            // select biasa diubah SESUDAH skrip halaman selesai (halaman yang memasang select2 sendiri didahulukan)
            if (document.readyState === 'complete') { ubahSemua($); }
            else { window.addEventListener('load', function () { setTimeout(function () { ubahSemua($); }, 0); }); }
            amatiSelectBaru($);
        });
        // form di-reset: tampilan select2 ikut kembali
        document.addEventListener('reset', function (e) {
            setTimeout(function () { $(e.target).find('select.select2-hidden-accessible').each(function () { segarkan($, this); }); }, 0);
        }, true);
    });

    /* ---------- adaptor bootstrap-select -> select2 ---------- */
    function pasangAdaptor($) {
        var asli = $.fn.selectpicker;
        $(window).off('load.bs.select.data-api');      // matikan pemasangan otomatis bootstrap-select

        var adaptor = function (perintah, nilai) {
            if (perintah === 'val') {
                if (arguments.length > 1) { this.val(nilai); return this; }     // seperti bootstrap-select: tanpa memicu change
                return this.val();
            }
            return this.each(function () {
                if (this.tagName !== 'SELECT') { return; }
                if (perintah === 'destroy') { lepas($, this); return; }
                if (perintah === 'deselectAll') { $(this).val(this.multiple ? [] : '').trigger('change'); return; }
                if (perintah === 'selectAll') { $(this).find('option').prop('selected', true); $(this).trigger('change'); return; }
                pasang($, this, true);                 // pasang baru, 'refresh', 'render', dll
            });
        };
        adaptor.Constructor = asli ? asli.Constructor : undefined;
        $.fn.selectpicker = adaptor;

        // yang sudah sempat dipasang bootstrap-select sebelum adaptor aktif -> dialihkan
        if (asli) {
            $('select').each(function () {
                if ($(this).data('selectpicker')) {
                    try { asli.call($(this), 'destroy'); } catch (e) { /* abaikan */ }
                    pasang($, this, true);
                }
            });
        }
    }

    /* ---------- .val(...) dari kode halaman: tampilan select2 ikut berubah ---------- */
    function tambalVal($) {
        var valAsli = $.fn.val;
        $.fn.val = function () {
            var hasil = valAsli.apply(this, arguments);
            if (arguments.length) {
                for (var i = 0; i < this.length; i++) {
                    var el = this[i];
                    if (el && el.tagName === 'SELECT' && el.classList.contains('select2-hidden-accessible')) {
                        segarkan($, el);
                    }
                }
            }
            return hasil;
        };
    }

    /* ---------- muat select2 kalau halaman belum memuatnya ---------- */
    function siapkanSelect2($, lanjut) {
        if ($.fn.select2) { lanjut(); return; }
        var s = document.createElement('script');
        s.src = dasar + 'css/4.1.1/select2.full.min.js';
        s.onload = function () { if ($.fn.select2) { lanjut(); } };
        document.head.appendChild(s);
    }

    /* ---------- pasang select2 pada satu <select> ---------- */
    function pasang($, el, dipaksa) {
        if (!$.fn.select2) { antrian.push(el); return; }
        var $el = $(el);
        if ($el.hasClass('select2-hidden-accessible')) { segarkan($, el); return; }   // sudah select2: segarkan tampilannya
        if (!dipaksa && dilewati($, el)) { return; }
        $el.removeData('nds-lepas');
        kunciUnik(el);
        var modal = $el.closest('.modal');
        var adaCari = el.getAttribute('data-live-search') === 'true' || el.options.length > 8;
        try {
            $el.select2({
                theme: 'bootstrap4',
                width: el.style && el.style.width ? 'style' : '100%',
                dropdownParent: modal.length ? modal : $(document.body),
                minimumResultsForSearch: adaCari ? 0 : Infinity
            });
        } catch (e) { /* kalau gagal, biarkan select asli */ }
    }

    /* select2 menyimpan instance-nya memakai id elemen sbg kunci
       (data-select2-id = "select2-data-<id>"). Halaman lama sering menyalin
       baris dengan id yang SAMA (mis. tombol Add Row di jurnal): select2 baris
       baru lalu membuang select2 baris sebelumnya, dan baris lama berubah jadi
       sel kosong yang tidak bisa diisi. Kunci unik dipasang lebih dulu di sini;
       atribut id aslinya TIDAK diubah supaya kode halaman yang memakai #id aman. */
    var nomorKunci = 0;
    function kunciUnik(el) {
        if (!el.getAttribute('data-select2-id')) {
            el.setAttribute('data-select2-id', 'nds-' + (++nomorKunci) + '-' + Date.now().toString(36));
        }
    }

    /* perbarui teks yang tampil di select2 TANPA memicu event apa pun.
       Jangan pakai trigger('change...'): jQuery tetap menjalankan onchange="..." di HTML
       (mis. onchange="this.form.submit()" -> form terkirim sendiri). */
    function segarkan($, el) {
        var inst = $(el).data('select2');
        if (!inst || !inst.dataAdapter) { return; }
        inst.dataAdapter.current(function (data) { inst.trigger('selection:update', { data: data }); });
    }

    function lepas($, el) {
        var $el = $(el);
        if ($el.hasClass('select2-hidden-accessible')) { $el.select2('destroy'); }
        $el.data('nds-lepas', true);
    }

    /* select yang sengaja dibiarkan asli */
    function dilewati($, el) {
        var $el = $(el);
        if ($el.hasClass('no-select2') || el.getAttribute('data-tema') === 'asli') { return true; }
        if ($el.data('nds-lepas') || $el.data('selectpicker')) { return true; }
        if ($el.closest('.dataTables_length, table.dataTable tbody, .swal2-container, .select2-container').length) { return true; }
        return false;
    }

    function ubahSemua($) {
        $('select').each(function () { pasang($, this, false); });
    }

    /* select yang ditambahkan belakangan (baris baru, isi modal, dll) */
    function amatiSelectBaru($) {
        if (!window.MutationObserver) { return; }
        var baru = [];
        var jadwal = null;
        new MutationObserver(function (catatan) {
            for (var i = 0; i < catatan.length; i++) {
                var n = catatan[i].addedNodes;
                for (var j = 0; j < n.length; j++) {
                    if (n[j].nodeType !== 1) { continue; }
                    if (n[j].tagName === 'SELECT') { baru.push(n[j]); }
                    else if (n[j].querySelectorAll) { baru.push.apply(baru, n[j].querySelectorAll('select')); }
                }
            }
            if (baru.length && !jadwal) {
                jadwal = setTimeout(function () {     // beri kesempatan kode halaman memasang select2-nya sendiri dulu
                    var daftar = baru; baru = []; jadwal = null;
                    for (var k = 0; k < daftar.length; k++) {
                        if (document.body.contains(daftar[k])) { pasang($, daftar[k], false); }
                    }
                }, 0);
            }
        }).observe(document.body, { childList: true, subtree: true });
    }
})();
