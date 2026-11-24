/* Interaksi NDS - dimuat header.php kalau TAMPILAN_BARU & TEMA_INTERAKSI = true.
   1. alert() bawaan browser -> kotak SweetAlert
   2. form dikirim -> layar loading + tombol dikunci;
      tombol simpan yang memulai AJAX dikunci sampai request selesai (cegah simpan dobel)
   3. warna tombol lama diseragamkan: Search biru, Reset abu, Excel/Export hijau
   4. judul halaman lama (<h2 class="text-center">) diberi ikon sesuai kata di judulnya
   Semua teks yang tampil ke user berbahasa Inggris.
   Penjelasan lengkap: menu-docs.php (entri Tampilan NDS). */
(function () {
    var skrip = document.currentScript;
    var dasar = skrip ? skrip.src.replace(/js\/app-interaksi\.js.*$/, '') : '../';   // alamat folder module/
    var KUNCI_PESAN = 'app_pesan_tertunda';
    document.documentElement.classList.add('app-interaksi');   // penanda utk CSS warna tombol (app-tema.css)

    /* ================= pencatat request XHR (dipakai no.1 & no.2) ================= */
    var semuaXhr = [];
    var kirimAsli = XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send = function () {
        var x = this;
        semuaXhr.push(x);
        x.addEventListener('loadend', function () {
            var i = semuaXhr.indexOf(x);
            if (i > -1) { semuaXhr.splice(i, 1); }
        });
        return kirimAsli.apply(this, arguments);
    };
    // request yang BELUM selesai (readyState 4 = selesai; di dalam callback success-nya sudah 4)
    function jumlahJalan() {
        var n = 0;
        for (var i = 0; i < semuaXhr.length; i++) { if (semuaXhr[i].readyState !== 4) { n++; } }
        return n;
    }

    /* ================= 1. alert() -> SweetAlert ================= */
    var alertAsli = window.alert;
    var antrian = Promise.resolve();       // pesan tampil satu per satu
    var muatSwal = null;

    window.alert = function (pesan) {
        // Masih ada request berjalan: pakai alert asli yang MENAHAN halaman.
        // Kode sesudah alert (mis. pindah halaman) bisa memutus request yang belum selesai.
        if (jumlahJalan() > 0) { return alertAsli.call(window, pesan); }
        var teks = rapikan(pesan);
        var id = simpanTertunda(teks);     // kalau halaman langsung pindah, pesan tampil di halaman berikutnya
        tampilkan(teks, id);
    };

    function rapikan(p) {
        if (p && typeof p === 'object') {
            // alert(xhr) di halaman lama -> dulu tampil "[object Object]"
            if ('readyState' in p && 'status' in p) {
                return 'The request failed' + (p.status ? ' (HTTP ' + p.status + (p.statusText ? ' ' + p.statusText : '') + ')' : '') + '. Please try again.';
            }
            try { return JSON.stringify(p); } catch (e) { return String(p); }
        }
        return (p === undefined || p === null) ? '' : String(p);
    }

    function jenisPesan(t) {
        var s = t.toLowerCase();
        if (/(gagal|failed|error|incorrect|invalid|denied|salah)/.test(s)) { return { ikon: 'error', judul: 'Error' }; }
        if (/(success|berhasil|sukses|saved|tersimpan|disimpan|updated|deleted|approved|posted|completed|selesai)/.test(s)
            && !/(\bnot\b|tidak|belum)/.test(s)) { return { ikon: 'success', judul: 'Success' }; }
        return { ikon: 'warning', judul: 'Attention' };
    }

    function siapkanSwal() {
        if (window.Swal && window.Swal.fire) { return Promise.resolve(window.Swal); }
        if (!muatSwal) {
            muatSwal = new Promise(function (berhasil, gagal) {
                var s = document.createElement('script');
                s.src = dasar + 'css/4.1.1/sweetalert2@11.js';
                s.onload = function () { if (window.Swal) { berhasil(window.Swal); } else { gagal(); } };
                s.onerror = gagal;
                document.head.appendChild(s);
            });
        }
        return muatSwal;
    }

    function amankan(t) {
        return t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r?\n/g, '<br>');
    }

    function tampilkan(teks, id) {
        // halaman sering memindah fokus ke isian sesudah alert -> dikembalikan ke situ setelah ditutup
        var fokus = null;
        var catatFokus = function (e) {
            if (!(e.target.closest && e.target.closest('.swal2-container'))) { fokus = e.target; }
        };
        document.addEventListener('focusin', catatFokus, true);
        setTimeout(function () { document.removeEventListener('focusin', catatFokus, true); }, 400);

        antrian = antrian.then(function () {
            return siapkanSwal().then(function (Swal) {
                var j = jenisPesan(teks);
                return Swal.fire({
                    icon: j.ikon, title: j.judul, html: amankan(teks),
                    confirmButtonText: 'OK', confirmButtonColor: '#1e3a8a', returnFocus: false
                });
            }, function () {
                alertAsli.call(window, teks);        // SweetAlert gagal dimuat -> alert asli
            });
        }).then(function () {
            hapusTertunda(id);
            if (fokus && document.contains(fokus)) { try { fokus.focus(); } catch (e) { /* abaikan */ } }
        }, function () { hapusTertunda(id); });
    }

    function bacaTertunda() {
        try { return JSON.parse(sessionStorage.getItem(KUNCI_PESAN) || '[]'); } catch (e) { return []; }
    }
    function tulisTertunda(a) {
        try { sessionStorage.setItem(KUNCI_PESAN, JSON.stringify(a)); } catch (e) { /* abaikan */ }
    }
    function simpanTertunda(teks) {
        var id = Date.now() + '-' + Math.random();
        var a = bacaTertunda(); a.push({ id: id, t: teks, w: Date.now() }); tulisTertunda(a);
        return id;
    }
    function hapusTertunda(id) {
        tulisTertunda(bacaTertunda().filter(function (x) { return x.id !== id; }));
    }
    // pesan dari halaman sebelumnya (alert lalu langsung reload/pindah) -> tampilkan di sini,
    // sesudah halaman selesai dimuat (SweetAlert milik halaman sudah ada)
    window.addEventListener('load', function () {
        var a = bacaTertunda();
        if (!a.length) { return; }
        tulisTertunda([]);
        a.forEach(function (x) { if (Date.now() - x.w < 20000) { tampilkan(x.t, simpanTertunda(x.t)); } });
    });

    /* ================= 2. loading + kunci tombol ================= */
    var lapis = null, tombolTerkunci = [], waktuLapis = null;

    function bukanPindahHalaman(form, tombol) {
        var target = (tombol && tombol.getAttribute('formtarget')) || form.getAttribute('target') || '';
        if (target && target !== '_self') { return true; }                       // buka tab baru
        var aksi = (tombol && tombol.getAttribute('formaction')) || form.getAttribute('action') || '';
        var pola = /(ekspor|export|excel|xls|pdf|print|cetak|download|unduh)/i;
        if (pola.test(aksi)) { return true; }                                     // unduhan: halaman tetap
        if (tombol && pola.test(labelTombol(tombol) + ' ' + (tombol.name || '') + ' ' + (tombol.id || ''))) { return true; }
        return false;
    }

    function labelTombol(b) {
        return (b.tagName === 'INPUT' ? (b.value || '') : (b.textContent || '')).replace(/\s+/g, ' ').trim();
    }

    function tampilLoading() {
        if (!lapis) {
            lapis = document.createElement('div');
            lapis.className = 'app-sibuk';
            lapis.setAttribute('role', 'status');
            lapis.innerHTML = '<div class="app-sibuk-kotak"><div class="app-sibuk-putar"></div>'
                + '<div class="app-sibuk-teks">Loading, please wait...</div>'
                + '<button type="button" class="app-sibuk-tutup">Hide</button></div>';
            lapis.querySelector('.app-sibuk-tutup').addEventListener('click', tutupLoading);
            document.body.appendChild(lapis);
        }
        lapis.classList.remove('lama');
        lapis.querySelector('.app-sibuk-teks').textContent = 'Loading, please wait...';
        lapis.classList.add('tampil');
        // lebih dari 12 detik: beri tahu masih jalan + tombol Hide (mis. ternyata unduhan)
        clearTimeout(waktuLapis);
        waktuLapis = setTimeout(function () {
            lapis.classList.add('lama');
            lapis.querySelector('.app-sibuk-teks').textContent = 'Still working, please wait...';
        }, 12000);
    }

    function tutupLoading() {
        clearTimeout(waktuLapis);
        if (lapis) { lapis.classList.remove('tampil', 'lama'); }
        tombolTerkunci.forEach(function (b) { b.disabled = false; b.removeAttribute('data-app-kunci'); });
        tombolTerkunci = [];
    }

    document.addEventListener('submit', function (e) {
        var form = e.target, tombol = e.submitter || null;
        // dicek SESUDAH semua handler halaman jalan: kalau dicegah (form AJAX), tidak ada loading
        setTimeout(function () {
            if (e.defaultPrevented || !(form instanceof HTMLFormElement)) { return; }
            if (bukanPindahHalaman(form, tombol)) { return; }
            tampilLoading();
            // dikunci sesudah data form terkumpul -> nilai tombol tetap ikut terkirim
            form.querySelectorAll('button[type=submit], button:not([type]), input[type=submit]').forEach(function (b) {
                if (!b.disabled) { b.disabled = true; b.setAttribute('data-app-kunci', '1'); tombolTerkunci.push(b); }
            });
        }, 0);
    }, false);

    // kembali lewat tombol Back (halaman dari cache browser) -> loading jangan tertinggal
    window.addEventListener('pageshow', function (e) { if (e.persisted) { tutupLoading(); } });

    // tombol simpan yang memulai AJAX: dikunci sampai request selesai (cegah klik dobel)
    var POLA_SIMPAN = /\b(save|simpan|submit|approve|post|process|proses|confirm|update|insert|send|kirim)\b/i;
    document.addEventListener('click', function (e) {
        var b = e.target.closest && e.target.closest('button, input[type=button], input[type=submit]');
        if (!b || b.disabled || !POLA_SIMPAN.test(labelTombol(b))) { return; }
        var sebelum = jumlahJalan();
        setTimeout(function () {
            if (b.disabled || jumlahJalan() <= sebelum) { return; }   // halaman sudah mengunci sendiri / tidak ada request
            b.disabled = true; b.setAttribute('data-app-kunci', '1');
            var mulai = Date.now();
            var cek = setInterval(function () {
                if (jumlahJalan() === 0 || Date.now() - mulai > 120000) {
                    clearInterval(cek);
                    if (b.getAttribute('data-app-kunci')) { b.disabled = false; b.removeAttribute('data-app-kunci'); }
                }
            }, 200);
        }, 0);
    }, true);

    /* ================= 3. warna tombol lama ================= */
    // Tombol lama bergaya inline sudah ditangani CSS (app-tema.css). Di sini tombol lama
    // ber-class bootstrap yang warnanya tidak sesuai artinya. Tombol .app-btn (menu baru) tidak disentuh.
    function warnaiTombol() {
        var semua = document.querySelectorAll('button, input[type=submit], input[type=button], a.btn');
        for (var i = 0; i < semua.length; i++) {
            var b = semua[i];
            // tombol Reset tidak dipakai lagi di semua menu (permintaan user 13 Nov 2026) -> disembunyikan
            // KECUALI yang ditandai data-app-tetap. Dua layar unggah (Upload Journal & VAT In di
            // Create Memorial Journal) memintanya kembali 24 Nov 2026: di sana Reset membuang data
            // staging di server, bukan sekadar mengosongkan filter, jadi tidak ada gantinya.
            // Penandanya dipasang di tombolnya, bukan daftar id di berkas ini, supaya layar lain
            // bisa ikut tanpa menyentuh tema.
            if (/^reset$/i.test(labelTombol(b)) && !b.hasAttribute('data-app-tetap')) { b.classList.add('app-sembunyi'); continue; }
            if (b.classList.contains('app-btn') || b.closest('.dataTables_wrapper tbody, .swal2-container')) { continue; }
            var kata = labelTombol(b).toLowerCase().split(' ')[0].replace(/[^a-z]/g, '');
            var k = b.className;
            if ((kata === 'search' || kata === 'cari') && /\bbtn-(info|warning|primary|success)\b/.test(k)) {
                b.classList.add('app-warna-cari');
            } else if ((kata === 'reset' || kata === 'clear') && /\bbtn-(danger|warning)\b/.test(k)) {
                b.classList.add('app-warna-reset');
            } else if ((kata === 'excel' || kata === 'export') && !/\bbtn-(success|outline-success)\b/.test(k)) {
                b.classList.add('app-warna-excel');
            }
        }
    }
    /* ================= 4. ikon di judul halaman lama ================= */
    // <h2 class="text-center">LIST COST CENTER</h2> -> ikon dipilih dari kata di judul (yang pertama cocok)
    var IKON_JUDUL = [
        [/cash ?flow/i, 'fa-exchange'], [/memo/i, 'fa-sticky-note-o'],
        [/cost center/i, 'fa-bullseye'], [/profit center/i, 'fa-building'],
        [/bank/i, 'fa-university'], [/supplier/i, 'fa-truck'], [/\brate\b|kurs/i, 'fa-line-chart'],
        [/categor|chart of account|\bcoa\b/i, 'fa-sitemap'], [/mapping/i, 'fa-random'],
        [/journal|jurnal/i, 'fa-book'], [/\btax\b|pajak|\bvat\b|\bppn\b|\bpph\b/i, 'fa-percent'],
        [/approv/i, 'fa-check-square-o'], [/closing/i, 'fa-lock'],
        [/payment|voucher|kontrabon|invoice|faktur/i, 'fa-file-text-o'],
        [/\bbpb\b|\bbppb\b|receiv|penerimaan|retur/i, 'fa-inbox'], [/petty|cash/i, 'fa-money'],
        [/report|laporan|ledger|summary|rekap/i, 'fa-bar-chart'],
        [/user|role/i, 'fa-users'], [/create|form|update|edit/i, 'fa-pencil-square-o']
    ];
    function ikonJudul() {
        var judul = document.querySelectorAll('h2.text-center');
        for (var i = 0; i < judul.length; i++) {
            var h = judul[i];
            if (h.closest('.modal, table') || h.querySelector('.fa, .judul-ikon')) { continue; }   // sudah punya ikon
            var ikon = 'fa-list-alt';
            for (var j = 0; j < IKON_JUDUL.length; j++) {
                if (IKON_JUDUL[j][0].test(h.textContent)) { ikon = IKON_JUDUL[j][1]; break; }
            }
            var s = document.createElement('span');
            s.className = 'judul-ikon';
            s.innerHTML = '<i class="fa ' + ikon + '" aria-hidden="true"></i>';
            h.insertBefore(document.createTextNode(' '), h.firstChild);
            h.insertBefore(s, h.firstChild);
        }
    }

    function siap() { warnaiTombol(); ikonJudul(); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', siap); }
    else { siap(); }
})();
