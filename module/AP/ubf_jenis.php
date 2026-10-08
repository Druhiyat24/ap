<?php
/* ============================================================================
   ubf_jenis.php - SATU TEMPAT yang membedakan varian menu "Update BPB".

   Menu ini punya lebih dari satu jenis barang (Fabric, Accessories, menyusul
   General & WIP). Alur, tampilan, tabel draft, dan penjurnalannya SAMA; yang
   berbeda hanya dari mana dokumen BPB-nya dibaca dan ke mana harga
   koreksinya ditulis.

   Karena itu berkas-berkasnya TIDAK disalin per jenis. Satu set berkas
   dipakai semua varian, dibedakan lewat parameter `jenis` - supaya perbaikan
   (mis. empat perbaikan penjurnalan 6 Okt 2026) cukup dikerjakan sekali dan
   langsung berlaku untuk semua jenis.

   BEDA ANTAR JENIS
     Fabric      : dokumen di whs_inmaterial_fabric (+_det) utk penerimaan dan
                   whs_bppb_h/whs_bppb_ro utk retur. Nomornya GK/IN & GK/RO.
                   Harga ditulis ke bpb + whs_inmaterial_fabric_det, atau
                   bppb + whs_bppb_ro.
     Accessories : TIDAK punya tabel kepala sendiri - dokumennya ada langsung
                   di `bpb`, nomornya GACC/IN dan GACC/RI. Harga cukup ditulis
                   ke `bpb`.
                   CATATAN PENTING: GACC/RI BUKAN retur akuntansi. Diperiksa
                   ke produksi 8 Okt 2026 - jurnalnya SEARAH dgn penerimaan
                   (Persediaan Aksesoris didebit, GR/IR Aksesoris dikredit,
                   type 'AP - BPB'), sama persis dgn GACC/IN. Jadi keduanya
                   diperlakukan sebagai PENERIMAAN, tidak ada cabang arah
                   terbalik seperti GK/RO di Fabric.
   ============================================================================ */

/* Membaca jenis dari permintaan. Apa pun yang tidak dikenali jatuh ke
   'fabric' - itu jenis yang sudah ada lebih dulu, jadi tautan lama yang
   belum membawa parameter tetap bekerja seperti semula. */
function ubf_jenis($dari = null)
{
    if ($dari === null) {
        $dari = isset($_REQUEST['jenis']) ? $_REQUEST['jenis'] : 'fabric';
    }
    $j = strtolower(trim((string) $dari));
    if ($j === 'accessories' || $j === 'aksesoris' || $j === 'acc') {
        return 'accessories';
    }
    return 'fabric';
}

/* Seluruh beda antar jenis dikumpulkan di sini supaya tidak berserak sebagai
   if/else di belasan berkas. */
function ubf_konf($jenis = null)
{
    $jenis = ubf_jenis($jenis);

    if ($jenis === 'accessories') {
        return array(
            'jenis'        => 'accessories',
            'label'        => 'Accessories',
            'ikon'         => 'fa-puzzle-piece',
            // Awalan nomor pengajuan dibuat BERBEDA dari Fabric supaya
            // penomorannya tidak pernah bertabrakan.
            'prefix_dok'   => 'UPD/GACC/',
            // Dokumen aksesoris ada langsung di `bpb`, tidak ada tabel kepala.
            'pakai_whs'    => false,
            'pola_bpb'     => 'GACC/%',
            // Semua dokumen aksesoris (IN maupun RI) dijurnal sbg penerimaan.
            'selalu_terima' => true,
        );
    }

    return array(
        'jenis'        => 'fabric',
        'label'        => 'Fabric',
        'ikon'         => 'fa-warehouse',
        'prefix_dok'   => 'UPD/GK/',
        'pakai_whs'    => true,
        'pola_bpb'     => 'GK/%',
        'selalu_terima' => false,
    );
}

/* Menempelkan parameter jenis ke sebuah tautan, tanpa menimpa query string
   yang sudah ada. */
function ubf_tautan($berkas, $jenis)
{
    $jenis = ubf_jenis($jenis);
    return $berkas . (strpos($berkas, '?') === false ? '?' : '&') . 'jenis=' . $jenis;
}

/* Jumlah pengajuan yang masih menunggu approval untuk satu jenis. Dipakai
   lencana merah di menu. Dibuat tahan banting: kalau tabelnya belum ada
   (basis data yang belum dimigrasi), kembalikan 0 - menu tetap tampil. */
function ubf_jml_pending($conn, $jenis)
{
    $j = mysqli_real_escape_string($conn, ubf_jenis($jenis));
    $r = @mysqli_query($conn, "SELECT COUNT(*) cnt FROM update_bpb_fabric_h
                               WHERE status NOT IN ('Approved','Cancel') AND jenis = '$j'");
    if (!$r) { return 0; }
    $w = mysqli_fetch_assoc($r);
    return (int) (isset($w['cnt']) ? $w['cnt'] : 0);
}
