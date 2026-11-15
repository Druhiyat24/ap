/* Navbar NDS: menu utama dibuka dgn disorot (CSS) atau diklik (Bootstrap).
   Panel yang terbuka karena diklik ditutup saat menu lain disorot, supaya tidak
   ada dua panel bertumpuk. Dimuat oleh module/header.php dan module/index.php. */
(function () {
    var bisaSorot = window.matchMedia('(min-width: 1200px) and (hover: hover)');
    var menuUtama = document.querySelectorAll('.navbar-nav.mr-auto > .nav-item.dropdown');

    for (var i = 0; i < menuUtama.length; i++) {
        menuUtama[i].addEventListener('mouseenter', function () {
            if (!bisaSorot.matches) { return; }
            var terbuka = document.querySelectorAll('.navbar-nav.mr-auto > .nav-item.dropdown.show');
            for (var j = 0; j < terbuka.length; j++) {
                if (terbuka[j] === this) { continue; }
                terbuka[j].classList.remove('show');
                var panel = terbuka[j].querySelector('.dropdown-menu.show');
                if (panel) { panel.classList.remove('show'); }
                var tombol = terbuka[j].querySelector('.dropdown-toggle');
                if (tombol) { tombol.setAttribute('aria-expanded', 'false'); }
            }
        });
    }
})();
