<?php
/* Halaman tujuan setelah login ulang (sesi habis di tengah jalan).
   Dipakai index.php, auth.php dan module/function/logout.php.
   Hanya menerima halaman di dalam aplikasi: module/<folder>/<berkas>.php[?query]
   - selain itu dikosongkan, supaya tidak bisa dipakai mengalihkan ke situs lain. */
function login_next_aman($next)
{
    $next = trim((string) $next);
    if ($next === '' || strpos($next, '..') !== false || strpos($next, 'module/function/') === 0) {
        return '';
    }
    if (!preg_match('~^module/[A-Za-z0-9_-]+/[A-Za-z0-9_.-]+\.php(\?[^\s"\'<>\\\\]*)?$~', $next)) {
        return '';
    }
    return $next;
}
