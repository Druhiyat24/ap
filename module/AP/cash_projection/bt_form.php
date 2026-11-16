<?php
/* Tab Book Transfer - isian + daftar baris yang akan disimpan.
   Berkas INI dipakai dua halaman, Create dan Edit, biar tidak ada dua
   versi formulir yang lama-lama beda sendiri. Pemanggilnya menyiapkan:
     $akun      - daftar akun (wajib)
     $cfp_mode  - 'create' (bawaan) atau 'edit'
     $cfp_doc   - nomor dokumen, hanya untuk mode edit
     $cfp_baris - isi awal daftar baris, hanya untuk mode edit */

$cfp_mode  = isset($cfp_mode)  ? $cfp_mode  : 'create';
$cfp_doc   = isset($cfp_doc)   ? $cfp_doc   : '';
$cfp_baris = isset($cfp_baris) ? $cfp_baris : array();
?>

<div class="pco-sec"><i class="fa fa-exchange" aria-hidden="true"></i> Book Transfer</div>

<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="bt_tanggal"><b>Received Date</b> <i>*</i></label>
    <input type="text" class="form-control tanggal" id="bt_tanggal" autocomplete="off"
           value="<?php echo date('d M Y'); ?>">
  </div>

  <div class="col-lg-3 col-md-6 mb-2">
    <label for="bt_source"><b>Source</b> <i>*</i></label>
    <select class="form-control app-select2" id="bt_source">
      <option value="">Select source account</option>
      <?php foreach ($akun as $a) { ?>
        <option value="<?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>"
                data-curr="<?php echo htmlspecialchars($a['curr'], ENT_QUOTES); ?>"
                data-pc="<?php echo htmlspecialchars($a['nama_pc'], ENT_QUOTES); ?>">
          <?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="col-lg-3 col-md-6 mb-2">
    <label for="bt_tujuan"><b>Destination</b> <i>*</i></label>
    <select class="form-control app-select2" id="bt_tujuan">
      <option value="">Select destination account</option>
      <?php foreach ($akun as $a) { ?>
        <option value="<?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>"
                data-curr="<?php echo htmlspecialchars($a['curr'], ENT_QUOTES); ?>"
                data-pc="<?php echo htmlspecialchars($a['nama_pc'], ENT_QUOTES); ?>">
          <?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  

  <!-- Currency + Rate disatukan dalam satu kotak, caranya sama dgn
       Create Bank In (bank_in/inbank_none.php). -->

</div>

<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="bt_amount"><b>Amount</b> <i>*</i></label>
    <input type="text" class="form-control text-right" id="bt_amount" autocomplete="off" value="0,00">
  </div>

    <div class="col-lg-3 col-md-6 mb-2">
    <label class="cfp-dual-label">
      <span style="flex:0 0 38%;"><b>Currency</b></span>
      <span style="flex:1;"><b>Rate</b></span>
    </label>
    <div class="input-group">
      <input type="text" class="form-control" id="bt_curr" value="IDR" readonly style="max-width:38%;">
      <input type="text" class="form-control text-right" id="bt_rate" value="1,00" readonly>
    </div>
  </div>
  
  <!-- Dua nilai setara, caranya sama dgn Currency + Rate di sebelah.
       Yang mendarat di akun tujuan = salah satu dari keduanya, tinggal
       lihat mata uang akun Destination. -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label class="cfp-dual-label">
      <span style="flex:1;"><b>Equivalent IDR</b></span>
      <span style="flex:1;"><b>Equivalent USD</b></span>
    </label>
    <div class="input-group">
      <input type="text" class="form-control text-right" id="bt_idr" value="0,00" readonly>
      <input type="text" class="form-control text-right" id="bt_usd" value="0,00" readonly>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-5 col-md-8 mb-2">
    <label for="bt_deskripsi"><b>Description</b></label>
    <input type="text" class="form-control" id="bt_deskripsi" autocomplete="off" maxlength="255">
  </div>
  <div class="col-lg-3 col-md-4 mb-2">
    <label>&nbsp;</label>
    <div class="ftl-actions">
      <button type="button" class="app-btn app-btn-primary" id="bt_add">
        <i class="fa fa-plus"></i> <span id="bt_add_teks">Add to list</span>
      </button>
      <!-- hanya muncul waktu satu baris sedang diubah -->
      <button type="button" class="app-btn app-btn-danger" id="bt_batal" style="display:none;">
        <i class="fa fa-times"></i> Cancel edit
      </button>
    </div>
  </div>
</div>

<div id="bt_pesan_kurs" class="mb-2" style="display:none; font-size:11.5px; color:#8a5a06;"></div>

<div class="pco-sec mt-3"><i class="fa fa-list" aria-hidden="true"></i> Rows to be saved</div>
<div class="cfp-gridwrap">
  <table class="table cfp-grid mb-0" id="bt_grid">
    <thead>
      <tr>
        <th>#</th><th>Source</th><th>Destination</th><th>Date</th><th>Curr</th>
        <th class="num">Amount</th><th class="num">Rate</th>
        <th class="num">Equivalent IDR</th><th class="num">Equivalent USD</th>
        <th>Description</th><th>PC Source</th><th>PC Dest</th><th>Intra / Inter PC</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <td colspan="7">TOTAL <span id="bt_jml">0</span> row(s)</td>
        <td class="num" id="bt_total">0,00</td>
        <td class="num" id="bt_total_usd">0,00</td>
        <td colspan="6"></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
/* Daftar baris yang akan disimpan. Mode create mulai kosong, mode edit
   diisi baris dokumen yang sedang dibuka. */
var btBaris = <?php echo json_encode(array_values($cfp_baris)); ?>;

/* Mode halaman + nomor dokumen yang sedang diedit. */
var btMode = '<?php echo $cfp_mode; ?>';
var btDoc  = '<?php echo htmlspecialchars($cfp_doc, ENT_QUOTES); ?>';

/* Baris yang sedang diubah; -1 artinya sedang menambah baris baru. */
var btUbah = -1;

/* Kurs PAJAK USD pada Received Date, apa adanya dari master.
   Yang dipakai baris ini belum tentu segini - lihat btHitung(). */
var btKurs = 1;

/* Keterangan kurs (mundur / tidak ketemu), ditampilkan kalau kursnya
   memang terpakai. */
var btPesan = '';

/* Nomor permintaan kurs. Jawaban yang datang telat diabaikan - ganti akun
   cepat-cepat bisa membuat jawaban lama menimpa yang baru. */
var btMinta = 0;

/* 1.234.567,89 <-> angka */
function btFormat(n) {
  return (Number(n) || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function btAngka(teks) {
  if (!teks) { return 0; }
  return Number(String(teks).replace(/\./g, '').replace(',', '.')) || 0;
}

/* Mata uang akun Destination. */
function btCurrTujuan() {
  return $('#bt_tujuan option:selected').data('curr') || 'IDR';
}

/* Currency ikut akun Source, hitungannya diulang. Kursnya tidak ikut
   berubah - kurs hanya bergantung pada tanggal. */
function btIsiCurrency() {
  $('#bt_curr').val($('#bt_source option:selected').data('curr') || 'IDR');
  btHitung();
}

/* Kurs PAJAK USD dari master, mengikuti Received Date. */
function btAmbilKurs() {
  btMinta++;
  var punyaku = btMinta;

  $.getJSON('get_kurs_projection.php', { tanggal: $('#bt_tanggal').val(), curr: 'USD' }, function (d) {
    if (punyaku !== btMinta) { return; }   // sudah ada permintaan yang lebih baru
    btKurs = Number(d.rate) || 0;
    if (!d.ketemu) {
      btPesan = 'No PAJAK rate for this date in master rate.';
    } else if (d.mundur) {
      btPesan = 'Using PAJAK rate dated <b>' + d.tanggal_tampil + '</b> - no rate yet for the document date.';
    } else {
      btPesan = '';
    }
    btHitung();
  });
}

/* Amount selalu dalam mata uang akun Source.
   Rupiah ke rupiah sama sekali tidak menyentuh kurs: Rate 1, dolarnya 0.
   Selain itu kursnya kurs PAJAK USD tanggal itu. */
function btHitung() {
  var amount     = btAngka($('#bt_amount').val());
  var rupiah     = ($('#bt_curr').val() === 'IDR');
  var keRupiah   = (btCurrTujuan() === 'IDR');
  var rupiahSaja = (rupiah && keRupiah);

  var rate = rupiahSaja ? 1 : btKurs;
  var idr  = rupiah ? amount : amount * rate;

  var usd = 0;
  if (!rupiah) {
    usd = amount;
  } else if (!keRupiah && rate > 0) {
    usd = amount / rate;
  }

  $('#bt_rate').val(btFormat(rate));
  $('#bt_idr').val(btFormat(idr));
  $('#bt_usd').val(btFormat(usd));

  // keterangan kurs tidak perlu muncul kalau kursnya tidak dipakai
  if (rupiahSaja || btPesan === '') {
    $('#bt_pesan_kurs').hide();
  } else {
    $('#bt_pesan_kurs').html(btPesan).show();
  }
}

/* Kembali ke mode tambah: isian dikosongkan, tombol seperti semula. */
function btModeTambah() {
  btUbah = -1;
  $('#bt_add_teks').text('Add to list');
  $('#bt_batal').hide();
  $('#bt_amount').val('0,00');
  $('#bt_idr').val('0,00');
  $('#bt_usd').val('0,00');
  $('#bt_deskripsi').val('');
  btGambar();
}

/* Tarik satu baris kembali ke isian untuk diubah. */
function btIsiForm(i) {
  var b = btBaris[i];
  if (!b) { return; }

  btUbah = i;
  $('#bt_tanggal').val(b.tanggal);
  $('#bt_source').val(b.source).trigger('change');
  $('#bt_tujuan').val(b.tujuan).trigger('change');
  $('#bt_amount').val(btFormat(b.amount));
  $('#bt_deskripsi').val(b.deskripsi || '');

  btAmbilKurs();   // kurs ikut tanggal baris itu
  $('#bt_add_teks').text('Update row');
  $('#bt_batal').show();
  btGambar();
}

/* Gambar ulang tabel baris. */
function btGambar() {
  var html = '';
  var total = 0;
  var total_usd = 0;

  for (var i = 0; i < btBaris.length; i++) {
    var b = btBaris[i];
    total += b.amount_idr;
    total_usd += b.amount_usd;
    html += '<tr' + (i === btUbah ? ' class="bt-sedang"' : '') + '>'
          + '<td>' + (i + 1) + '</td>'
          + '<td>' + b.source + '</td>'
          + '<td>' + b.tujuan + '</td>'
          + '<td>' + b.tanggal + '</td>'
          + '<td>' + b.curr + '</td>'
          + '<td class="num">' + btFormat(b.amount) + '</td>'
          + '<td class="num">' + btFormat(b.rate) + '</td>'
          + '<td class="num">' + btFormat(b.amount_idr) + '</td>'
          + '<td class="num">' + btFormat(b.amount_usd) + '</td>'
          + '<td>' + (b.deskripsi || '-') + '</td>'
          + '<td>' + b.pc_source + '</td>'
          + '<td>' + b.pc_tujuan + '</td>'
          + '<td>' + b.intra_inter + '</td>'
          + '<td>Post</td>'
          + '<td class="text-right" style="white-space:nowrap;">'
          + '<button type="button" class="app-btn app-btn-primary app-btn-sm bt-ubah" data-i="' + i + '">'
          + '<i class="fa fa-pencil"></i></button> '
          + '<button type="button" class="app-btn app-btn-danger app-btn-sm bt-hapus" data-i="' + i + '">'
          + '<i class="fa fa-trash"></i></button></td>'
          + '</tr>';
  }

  if (btBaris.length === 0) {
    html = '<tr><td colspan="15" class="cfp-kosong">No rows yet. Fill the form above then press Add to list.</td></tr>';
  }

  $('#bt_grid tbody').html(html);
  $('#bt_jml').text(btBaris.length);
  $('#bt_total').text(btFormat(total));
  $('#bt_total_usd').text(btFormat(total_usd));
}

$(function () {
  /* select2 & datepicker dipasang di halamannya, bukan di sini - berkas
     tab ini tidak selalu ikut dimuat (halaman Edit cuma memuat satu tab). */
  $('#bt_source').on('change', btIsiCurrency);
  // akun tujuan menentukan ada tidaknya sisi dolar
  $('#bt_tujuan').on('change', btHitung);
  $('#bt_tanggal').on('change', btAmbilKurs);
  $('#bt_amount').on('keyup change', btHitung);

  // rapikan angka saat kursor keluar
  $('#bt_amount').on('blur', function () {
    $(this).val(btFormat(btAngka($(this).val())));
    btHitung();
  });

  $('#bt_add').on('click', function () {
    var source = $('#bt_source').val();
    var tujuan = $('#bt_tujuan').val();
    var amount = btAngka($('#bt_amount').val());

    if (!source || !tujuan) {
      Swal.fire({ icon: 'warning', title: 'Source and Destination are required' });
      return;
    }
    if (source === tujuan) {
      Swal.fire({ icon: 'warning', title: 'Source and Destination cannot be the same' });
      return;
    }
    if (amount <= 0) {
      Swal.fire({ icon: 'warning', title: 'Amount must be greater than 0' });
      return;
    }

    var pcSource = $('#bt_source option:selected').data('pc') || '';
    var pcTujuan = $('#bt_tujuan option:selected').data('pc') || '';

    var isi = {
      source: source,
      tujuan: tujuan,
      tanggal: $('#bt_tanggal').val(),
      curr: $('#bt_curr').val(),
      amount: amount,
      rate: btAngka($('#bt_rate').val()),
      amount_idr: btAngka($('#bt_idr').val()),
      deskripsi: $.trim($('#bt_deskripsi').val()),
      pc_source: pcSource,
      pc_tujuan: pcTujuan,
      amount_usd: btAngka($('#bt_usd').val()),
      curr_tujuan: btCurrTujuan(),
      intra_inter: (pcSource && pcSource === pcTujuan) ? 'Intra PC' : 'Inter PC'
    };

    if (btUbah >= 0) {
      btBaris[btUbah] = isi;   // ganti baris yang sedang diubah
    } else {
      btBaris.push(isi);
    }
    btModeTambah();
  });

  $('#bt_batal').on('click', btModeTambah);

  $('#bt_grid').on('click', '.bt-ubah', function () {
    btIsiForm($(this).data('i'));
  });

  $('#bt_grid').on('click', '.bt-hapus', function () {
    var i = $(this).data('i');
    btBaris.splice(i, 1);

    // baris yang sedang diubah ikut hilang / nomornya bergeser
    if (btUbah === i) { btModeTambah(); return; }
    if (btUbah > i) { btUbah--; }
    btGambar();
  });

  $('#btnsave').on('click', function () {
    // tiap tab punya tombol Save sendiri; yang jalan cuma tab yang aktif
    if (!$('#tab-bt').hasClass('on')) { return; }

    if (btBaris.length === 0) {
      Swal.fire({ icon: 'warning', title: 'No rows added yet' });
      return;
    }
    if (btUbah >= 0) {
      Swal.fire({ icon: 'warning', title: 'Finish the row you are editing',
                  text: 'Press Update row to keep the change, or Cancel edit to drop it.' });
      return;
    }

    var edit = (btMode === 'edit');
    var kirim = { baris: JSON.stringify(btBaris) };
    if (edit) { kirim.no_doc = btDoc; }

    var tombol = $(this).prop('disabled', true);
    $.post(edit ? 'cash_projection/update_book_transfer.php'
                : 'cash_projection/save_book_transfer.php', kirim, function (d) {
      if (d && d.status === 'ok') {
        Swal.fire({
          icon: 'success',
          title: edit ? 'Updated' : 'Saved',
          html: d.jml + ' row(s) saved.<br>Document No:<br><b>' + d.no_doc + '</b>'
        }).then(function () { location.href = 'cash-projection.php'; });
      } else {
        tombol.prop('disabled', false);
        Swal.fire({ icon: 'error', title: 'Failed', text: (d && d.pesan) ? d.pesan : 'Unknown error' });
      }
    }, 'json').fail(function (xhr) {
      tombol.prop('disabled', false);
      Swal.fire({ icon: 'error', title: 'Failed', text: xhr.responseText || 'Cannot reach the server' });
    });
  });

  btGambar();
  btAmbilKurs();   // kurs hari ini langsung terisi
});
</script>
