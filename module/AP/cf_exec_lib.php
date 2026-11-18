<?php
/* ============================================================================
   EXECUTIVE SUMMARY - PERHITUNGANNYA.

   Masukannya BUKAN database, melainkan keluaran ajx_cf_history_detail.php
   (matriks potret yang sudah jadi). Jadi ringkasan ini mustahil berbeda dgn
   tabel yang dilihat pemakai: dua-duanya angka yang sama, cuma disusun lain.

   SUSUNAN & RUMUSNYA MENIRU LEMBAR "Executive Summary" di
   D:\File Project\Cash Flow Projection Report.xlsx. Tiap rumus dicocokkan
   dulu ke lembar "Cash Flow Daily" di berkas yang sama, contohnya:
     - Lowest Balance pekan #1 = nilai TERENDAH baris CLOSING hari 1-7
     - Undrawn IDR pekan #1    = SISA LIMIT di HARI TERAKHIR pekan itu
     - Month-End Liquidity     = saldo akhir + sisa fasilitas

   BARISNYA DICARI LEWAT STRUKTUR, BUKAN NAMA. Baris saldo dikenali dari
   tipe 'sal', total besar dari 'tot2', dan subtotal kegiatan dari 'tot'
   pertama di tiap blok. Mencocokkan nama baris akan diam-diam gagal begitu
   master kategorinya diganti namanya.

   KOLOM TOTAL DIPAKAI APA ADANYA untuk saldo & sisa limit. Mesin proyeksinya
   sudah mengisi TOTAL baris-baris itu dgn NILAI AKHIR (bukan penjumlahan) -
   di mode tanggal = hari terakhir, di mode akun = seluruh rekening. Itulah
   sebabnya kartu atas tetap benar untuk potret Weekly/Monthly, yang tidak
   punya tanggal sama sekali.
   ============================================================================ */

/* Membulatkan PERSIS seperti layar: Math.round = setengah KE ATAS.
   round() bawaan PHP membulatkan setengah MENJAUHI nol - beda 1 rupiah
   untuk nilai yang pas ",5". */
function cfe_bulat($v)
{
    return (float) floor((float) $v + 0.5);
}

function cfe_nilai($baris, $i)
{
    return isset($baris['v'][$i]) ? cfe_bulat($baris['v'][$i]) : 0.0;
}

/* Satu kartu angka di baris atas. $nilai null = tidak bisa dihitung untuk
   potret jenis ini; layar menggantinya dgn keterangan, BUKAN angka nol. */
function cfe_kartu($label, $jenis, $nilai, $teks, $sub)
{
    return array('label' => $label, 'jenis' => $jenis, 'nilai' => $nilai,
                 'teks' => $teks, 'sub' => $sub);
}

function cf_exec_ringkasan($jawab)
{
    $kolom = isset($jawab['kolom']) ? $jawab['kolom'] : array();
    $baris = isset($jawab['baris']) ? $jawab['baris'] : array();
    $pinjam = isset($jawab['pinjaman']) ? $jawab['pinjaman'] : array();
    $mode = (isset($jawab['mode']) && $jawab['mode'] === 'akun') ? 'akun' : 'tanggal';
    $nk = count($kolom);

    /* ---- 1. baris penting, dikenali dari strukturnya ---- */
    $buka = null; $tutup = null;
    $masuk = null; $keluar = null;      /* TOTAL CASH INFLOWS / OUTFLOWS */
    $opIn = null; $opOut = null;        /* subtotal kegiatan operasi */
    $itemIn = array(); $itemOut = array();

    $diInflow = true;
    foreach ($baris as $b) {
        $tipe = $b['tipe'];
        if ($tipe === 'blok') {
            $u = strtoupper($b['label']);
            if (strpos($u, 'INFLOW') !== false)  { $diInflow = true; }
            if (strpos($u, 'OUTFLOW') !== false) { $diInflow = false; }
            continue;
        }
        if ($tipe === 'sal') {
            if ($buka === null) { $buka = $b; } else { $tutup = $b; }
        } elseif ($tipe === 'tot') {
            if ($diInflow && $opIn === null)  { $opIn = $b; }
            if (!$diInflow && $opOut === null) { $opOut = $b; }
        } elseif ($tipe === 'tot2') {
            if ($diInflow) { $masuk = $b; } else { $keluar = $b; }
        } elseif ($tipe === 'item') {
            if (cfe_bulat($b['tot']) == 0) { continue; }
            $x = array('label' => $b['label'], 'nilai' => cfe_bulat($b['tot']));
            if ($diInflow) { $itemIn[] = $x; } else { $itemOut[] = $x; }
        }
    }
    if ($buka === null || $tutup === null) {
        return array('status' => 'error', 'message' => 'This snapshot has no balance rows.');
    }

    /* ---- 2. sisa fasilitas pinjaman ----
       Blok pinjaman punya DUA baris "SISA / (KELEBIHAN) LIMIT": yang pertama
       IDR (nilainya di baris itu sendiri), yang kedua USD - nilainya ada di
       baris anak '- Dalam Mata Uang Rupiah' di bawahnya, karena baris USD
       induknya memang selalu nol. */
    $sisaIdr = null; $sisaUsd = null;
    $ketemu = 0;
    for ($i = 0; $i < count($pinjam); $i++) {
        $p = $pinjam[$i];
        if ($p['tipe'] !== 'pinj' || strpos(strtoupper($p['label']), 'SISA') !== 0) { continue; }
        $ketemu++;
        if ($ketemu === 1) {
            $sisaIdr = $p;
        } else {
            for ($k = $i + 1; $k < count($pinjam); $k++) {
                if ($pinjam[$k]['tipe'] !== 'sub') { break; }
                if (strpos($pinjam[$k]['label'], 'Rupiah') !== false) { $sisaUsd = $pinjam[$k]; break; }
            }
            break;
        }
    }
    $kosong = array('tot' => 0, 'v' => array_fill(0, max($nk, 1), 0));
    $adaPisahMU = ($sisaIdr !== null && $sisaUsd !== null);
    if ($sisaIdr === null) { $sisaIdr = $kosong; }
    if ($sisaUsd === null) { $sisaUsd = $kosong; }

    $sTutup = cfe_bulat($tutup['tot']);
    $sUndrawn = cfe_bulat($sisaIdr['tot']) + cfe_bulat($sisaUsd['tot']);

    /* ---- 3. yang butuh TANGGAL ---- */
    $adaTgl = ($mode === 'tanggal' && $nk > 0);
    $tutupV = array();
    $tgl = array();
    for ($i = 0; $i < $nk; $i++) {
        $tutupV[] = cfe_nilai($tutup, $i);
        $tgl[] = isset($kolom[$i]['tgl']) ? $kolom[$i]['tgl'] : '';
    }

    $minNilai = null; $minTgl = ''; $kurangTgl = ''; $kurangHari = null;
    if ($adaTgl) {
        $minNilai = min($tutupV);
        $minTgl = $tgl[array_search($minNilai, $tutupV, true)];
        $kurangHari = 0;
        foreach ($tutupV as $i => $v) {
            if ($v < 0) {
                if ($kurangTgl === '') { $kurangTgl = $tgl[$i]; }
                $kurangHari++;
            }
        }
    }

    $CATATAN = 'Available for Daily snapshots only.';

    $kpi = array(
        cfe_kartu('Opening Cash Balance', 'uang', cfe_bulat($buka['tot']), null, 'First day of the period'),
        cfe_kartu('Closing Cash Balance', 'uang', $sTutup, null, 'Last day of the period'),
        cfe_kartu('Minimum Cash Balance', 'uang', $adaTgl ? $minNilai : null, null,
                  $adaTgl ? 'Lowest daily closing balance' : $CATATAN),
        cfe_kartu('First Shortfall Date', 'tgl', null,
                  $adaTgl ? ($kurangTgl === '' ? 'No Shortfall Found' : $kurangTgl) : '',
                  $adaTgl ? 'First day the closing balance goes negative' : $CATATAN),
        cfe_kartu('Date of Minimum Balance', 'tgl', null, $adaTgl ? $minTgl : '',
                  $adaTgl ? 'Day of the lowest closing balance' : $CATATAN),
        cfe_kartu('Cash Shortfall Days', 'angka', $adaTgl ? (float) $kurangHari : null, null,
                  $adaTgl ? 'Days with a negative closing balance' : $CATATAN),
        cfe_kartu('Undrawn Credit Facilities', 'uang', $sUndrawn, null,
                  $adaPisahMU
                    ? ('IDR ' . number_format(cfe_bulat($sisaIdr['tot']), 0, '.', ',')
                       . ' + USD ' . number_format(cfe_bulat($sisaUsd['tot']), 0, '.', ','))
                    : 'Remaining bank facility limit'),
        cfe_kartu('Projected Month-End Liquidity', 'uang', $sTutup + $sUndrawn, null,
                  'Closing balance + undrawn facilities'),
        cfe_kartu('Net Operating Cash Flow', 'uang',
                  ($opIn ? cfe_bulat($opIn['tot']) : 0) + ($opOut ? cfe_bulat($opOut['tot']) : 0), null,
                  'Operating inflows + operating outflows'),
    );

    /* ---- 4. penggerak terbesar: 5 teratas, nol dilewati ---- */
    usort($itemIn,  'cfe_urutTurun');
    usort($itemOut, 'cfe_urutNaik');
    $itemIn = array_slice($itemIn, 0, 5);
    $itemOut = array_slice($itemOut, 0, 5);

    /* ---- 5. pekan: blok 7 hari dari tanggal awal (sama dgn lembar acuan) ---- */
    $pekan = array();
    if ($adaTgl && $masuk && $keluar) {
        $no = 0;
        for ($a = 0; $a < $nk; $a += 7) {
            $z = min($a + 6, $nk - 1);
            $no++;
            $sumIn = 0; $sumOut = 0; $rendah = null;
            for ($i = $a; $i <= $z; $i++) {
                $sumIn  += cfe_nilai($masuk, $i);
                $sumOut += cfe_nilai($keluar, $i);
                if ($rendah === null || $tutupV[$i] < $rendah) { $rendah = $tutupV[$i]; }
            }
            $idr = cfe_nilai($sisaIdr, $z);
            $usd = cfe_nilai($sisaUsd, $z);
            $pekan[] = array(
                'no'     => $no,
                'mulai'  => $tgl[$a],
                'akhir'  => $tgl[$z],
                'buka'   => cfe_nilai($buka, $a),
                'masuk'  => $sumIn,
                'keluar' => $sumOut,
                'tutup'  => $tutupV[$z],
                'rendah' => $rendah,
                'idr'    => $idr,
                'usd'    => $usd,
                'total'  => $idr + $usd,
            );
        }
    }

    return array(
        'status'  => 'success',
        'mode'    => $mode,
        'kpi'     => $kpi,
        'in'      => $itemIn,
        'out'     => $itemOut,
        'pekan'   => $pekan,
        'catatan' => $adaTgl ? '' : $CATATAN,
        'info'    => isset($jawab['info']) ? $jawab['info'] : array(),
    );
}

function cfe_urutTurun($a, $b)
{
    if ($a['nilai'] == $b['nilai']) { return 0; }
    return ($a['nilai'] < $b['nilai']) ? 1 : -1;
}

function cfe_urutNaik($a, $b)
{
    if ($a['nilai'] == $b['nilai']) { return 0; }
    return ($a['nilai'] > $b['nilai']) ? 1 : -1;
}
