<?php
/* Tab Other Inflow - isian + daftar baris yang akan disimpan.
   Bentuknya mengikuti tab Book Transfer (bt_form.php), bedanya:
   cuma satu akun (Bank penerima), plus Customer Name & Income Category.
   Pemanggilnya menyiapkan:
     $bank      - daftar akun bank (wajib)
     $kategori  - daftar Income Category dari master (wajib)
     $customer  - daftar customer dari mastersupplier, id => nama (wajib)
     $cfp_mode  - 'create' (bawaan) atau 'edit'
     $cfp_doc   - nomor dokumen, hanya untuk mode edit
     $cfp_oi    - isi awal daftar baris, hanya untuk mode edit */

$cfp_mode = isset($cfp_mode) ? $cfp_mode : 'create';
$cfp_doc  = isset($cfp_doc)  ? $cfp_doc  : '';
$cfp_oi   = isset($cfp_oi)   ? $cfp_oi   : array();
?>

<div class="pco-sec"><i class="fa fa-sign-in" aria-hidden="true"></i> Other Inflow</div>

<!-- Semua baris memakai col-lg-3: empat kolom sama lebar, jadi isian di
     baris bawah lurus dgn isian di atasnya. -->
<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="oi_tanggal"><b>Received Date</b> <i>*</i></label>
    <input type="text" class="form-control tanggal" id="oi_tanggal" autocomplete="off"
           value="<?php echo date('d M Y'); ?>">
  </div>

  <!-- Dipilih dari mastersupplier tipe customer (yang disimpan Id_Supplier-nya),
       TAPI masih boleh diketik bebas: di data aslinya kolom ini juga dipakai
       untuk keterangan spt "Bunga", bukan selalu nama pelanggan. -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label for="oi_nama"><b>Customer Name</b> <i>*</i></label>
    <select class="form-control app-select2-tag" id="oi_nama">
      <option value="">Select customer or type</option>
      <?php foreach ($customer as $id => $nm) { ?>
        <option value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" data-m="1">
          <?php echo htmlspecialchars($nm, ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  

  <div class="col-lg-3 col-md-6 mb-2">
    <label for="oi_bank"><b>Bank</b> <i>*</i></label>
    <select class="form-control app-select2" id="oi_bank">
      <option value="">Select bank account</option>
      <?php foreach ($bank as $a) { ?>
        <option value="<?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>"
                data-curr="<?php echo htmlspecialchars($a['curr'], ENT_QUOTES); ?>"
                data-pc="<?php echo htmlspecialchars($a['nama_pc'], ENT_QUOTES); ?>">
          <?php echo htmlspecialchars($a['label'], ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>
</div>

<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="oi_amount"><b>Amount</b> <i>*</i></label>
    <input type="text" class="form-control text-right" id="oi_amount" autocomplete="off" value="0,00">
  </div>

  <!-- Currency & Rate tetap satu kotak: keduanya sifatnya satu keterangan. -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label class="cfp-dual-label">
      <span style="flex:0 0 38%;"><b>Currency</b></span>
      <span style="flex:1;"><b>Rate</b></span>
    </label>
    <div class="input-group">
      <input type="text" class="form-control" id="oi_curr" value="IDR" readonly style="max-width:38%;">
      <input type="text" class="form-control text-right" id="oi_rate" value="1,00" readonly>
    </div>
  </div>

  <!-- Tidak ada Equivalent USD di sini: rekeningnya cuma SATU, jadi kalau
       mata uangnya USD nilai dolarnya ya Amount itu sendiri - cuma
       mengulang. Yang perlu dilihat tinggal nilai rupiahnya. (Book
       Transfer beda: di sana ada dua rekening yang mata uangnya bisa
       berbeda, jadi dua-duanya memang perlu.) -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label for="oi_idr"><b>Equivalent IDR</b></label>
    <input type="text" class="form-control text-right" id="oi_idr" value="0,00" readonly>
  </div>
</div>

<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="oi_kategori"><b>Income Category</b> <i>*</i></label>
    <select class="form-control app-select2" id="oi_kategori">
      <option value="">Select income category</option>
      <?php foreach ($kategori as $k) { ?>
        <option value="<?php echo htmlspecialchars($k, ENT_QUOTES); ?>">
          <?php echo htmlspecialchars($k, ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="col-lg-5 col-md-8 mb-2">
    <label for="oi_deskripsi"><b>Description</b></label>
    <input type="text" class="form-control" id="oi_deskripsi" autocomplete="off" maxlength="255">
  </div>
  <div class="col-lg-3 col-md-4 mb-2">
    <label class="d-none d-lg-block">&nbsp;</label>
    <div class="ftl-actions">
      <button type="button" class="app-btn app-btn-primary" id="oi_add">
        <i class="fa fa-plus"></i> <span id="oi_add_teks">Add to list</span>
      </button>
      <button type="button" class="app-btn app-btn-danger" id="oi_batal" style="display:none;">
        <i class="fa fa-times"></i> Cancel edit
      </button>
    </div>
  </div>
</div>

<div id="oi_pesan_kurs" class="mb-2" style="display:none; font-size:11.5px; color:#8a5a06;"></div>

<div class="pco-sec mt-3"><i class="fa fa-list" aria-hidden="true"></i> Rows to be saved</div>
<div class="cfp-gridwrap">
  <table class="table cfp-grid mb-0" id="oi_grid">
    <thead>
      <tr>
        <th>#</th><th>Customer Name</th><th>Income Category</th><th>Bank</th><th>Date</th><th>Curr</th>
        <th class="num">Amount</th><th class="num">Rate</th>
        <th class="num">Equivalent IDR</th>
        <th>Description</th><th>PC</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <td colspan="8">TOTAL <span id="oi_jml">0</span> row(s)</td>
        <td class="num" id="oi_total">0,00</td>
        <td colspan="4"></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
/* Daftar baris yang akan disimpan. */
var oiBaris = <?php echo json_encode(array_values($cfp_oi)); ?>;

/* Kurs PAJAK USD pada Received Date. */
var oiKurs  = 1;
var oiPesan = '';
var oiMinta = 0;

/* Baris yang sedang diubah; -1 artinya sedang menambah baris baru. */
var oiUbah = -1;

/* Nilai dolar baris yang sedang diisi - tidak ditampilkan, lihat oiHitung(). */
var oiUsd = 0;

var oiMode = '<?php echo $cfp_mode; ?>';
var oiDoc  = '<?php echo htmlspecialchars($cfp_doc, ENT_QUOTES); ?>';

/* 1.234.567,89 <-> angka */
function oiFormat(n) {
  return (Number(n) || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function oiAngka(teks) {
  if (!teks) { return 0; }
  return Number(String(teks).replace(/\./g, '').replace(',', '.')) || 0;
}

/* Currency ikut akun Bank penerima. */
function oiIsiCurrency() {
  $('#oi_curr').val($('#oi_bank option:selected').data('curr') || 'IDR');
  oiHitung();
}

/* Kurs PAJAK USD dari master, mengikuti Received Date. */
function oiAmbilKurs() {
  oiMinta++;
  var punyaku = oiMinta;

  $.getJSON('get_kurs_projection.php', { tanggal: $('#oi_tanggal').val(), curr: 'USD' }, function (d) {
    if (punyaku !== oiMinta) { return; }
    oiKurs = Number(d.rate) || 0;
    if (!d.ketemu) {
      oiPesan = 'No PAJAK rate for this date in master rate.';
    } else if (d.mundur) {
      oiPesan = 'Using PAJAK rate dated <b>' + d.tanggal_tampil + '</b> - no rate yet for the document date.';
    } else {
      oiPesan = '';
    }
    oiHitung();
  });
}

/* Uang masuk ke akun rupiah tidak menyentuh kurs: Rate 1, dolarnya 0. */
function oiHitung() {
  var amount = oiAngka($('#oi_amount').val());
  var rupiah = ($('#oi_curr').val() === 'IDR');

  var rate = rupiah ? 1 : oiKurs;
  var idr  = rupiah ? amount : amount * rate;
  var usd  = rupiah ? 0 : amount;

  $('#oi_rate').val(oiFormat(rate));
  $('#oi_idr').val(oiFormat(idr));
  /* usd tidak punya kotak isian - nilainya tetap dihitung & ikut disimpan,
     supaya laporan yang butuh sisi dolar tidak perlu menghitung ulang. */
  oiUsd = usd;

  if (rupiah || oiPesan === '') {
    $('#oi_pesan_kurs').hide();
  } else {
    $('#oi_pesan_kurs').html(oiPesan).show();
  }
}

/* Baca isian Customer Name. Pilihan dari master ditandai data-m="1" -
   nilainya Id_Supplier, tulisannya nama resminya. Selain itu berarti
   diketik sendiri: nilainya teks apa adanya dan id-nya kosong. */
function oiBacaNama() {
  var $o = $('#oi_nama option:selected');
  var v  = $('#oi_nama').val() || '';
  if ($o.length && $o.attr('data-m') === '1') {
    return { id: v, nama: $.trim($o.text()) };
  }
  return { id: '', nama: $.trim(v) };
}

/* Isi balik dropdown-nya. Nama ketikan sendiri - dan customer yang sejak
   dokumen dibuat sudah dinonaktifkan - belum ada di daftar, jadi
   pilihannya ditambahkan dulu. */
function oiPilihNama(id, nama) {
  var $s = $('#oi_nama');

  if (id) {
    $s.val(String(id));
    if (!$s.val()) {
      var o = new Option(nama, String(id), true, true);
      $(o).attr('data-m', '1');
      $s.append(o).val(String(id));
    }
  } else {
    var ada = $s.find('option').filter(function () { return this.value === nama; }).length;
    if (nama && !ada) { $s.append(new Option(nama, nama, true, true)); }
    $s.val(nama || '');
  }
  $s.trigger('change');
}

/* Kembali ke mode tambah. */
function oiModeTambah() {
  oiUbah = -1;
  $('#oi_add_teks').text('Add to list');
  $('#oi_batal').hide();
  $('#oi_amount').val('0,00');
  $('#oi_idr').val('0,00');
  $('#oi_deskripsi').val('');
  $('#oi_nama').val('').trigger('change');
  oiGambar();
}

/* Tarik satu baris kembali ke isian untuk diubah. */
function oiIsiForm(i) {
  var b = oiBaris[i];
  if (!b) { return; }

  oiUbah = i;
  $('#oi_tanggal').val(b.tanggal);
  oiPilihNama(b.id_supplier, b.nama);
  $('#oi_kategori').val(b.kategori).trigger('change');
  $('#oi_bank').val(b.bank).trigger('change');
  $('#oi_amount').val(oiFormat(b.amount));
  $('#oi_deskripsi').val(b.deskripsi || '');

  oiAmbilKurs();
  $('#oi_add_teks').text('Update row');
  $('#oi_batal').show();
  oiGambar();
}

/* Gambar ulang tabel baris. */
function oiGambar() {
  var html = '';
  var total = 0;

  for (var i = 0; i < oiBaris.length; i++) {
    var b = oiBaris[i];
    total += b.amount_idr;
    html += '<tr' + (i === oiUbah ? ' class="bt-sedang"' : '') + '>'
          + '<td>' + (i + 1) + '</td>'
          + '<td>' + b.nama + '</td>'
          + '<td>' + b.kategori + '</td>'
          + '<td>' + b.bank + '</td>'
          + '<td>' + b.tanggal + '</td>'
          + '<td>' + b.curr + '</td>'
          + '<td class="num">' + oiFormat(b.amount) + '</td>'
          + '<td class="num">' + oiFormat(b.rate) + '</td>'
          + '<td class="num">' + oiFormat(b.amount_idr) + '</td>'
          + '<td>' + (b.deskripsi || '-') + '</td>'
          + '<td>' + b.pc + '</td>'
          + '<td>Post</td>'
          + '<td class="text-right" style="white-space:nowrap;">'
          + '<button type="button" class="app-btn app-btn-primary app-btn-sm oi-ubah" data-i="' + i + '">'
          + '<i class="fa fa-pencil"></i></button> '
          + '<button type="button" class="app-btn app-btn-danger app-btn-sm oi-hapus" data-i="' + i + '">'
          + '<i class="fa fa-trash"></i></button></td>'
          + '</tr>';
  }

  if (oiBaris.length === 0) {
    html = '<tr><td colspan="13" class="cfp-kosong">No rows yet. Fill the form above then press Add to list.</td></tr>';
  }

  $('#oi_grid tbody').html(html);
  $('#oi_jml').text(oiBaris.length);
  $('#oi_total').text(oiFormat(total));
}

$(function () {
  $('#oi_bank').on('change', oiIsiCurrency);
  $('#oi_tanggal').on('change', oiAmbilKurs);
  $('#oi_amount').on('keyup change', oiHitung);

  $('#oi_amount').on('blur', function () {
    $(this).val(oiFormat(oiAngka($(this).val())));
    oiHitung();
  });

  $('#oi_add').on('click', function () {
    var sup      = oiBacaNama();
    var nama     = sup.nama;
    var kategori = $('#oi_kategori').val();
    var bank     = $('#oi_bank').val();
    var amount   = oiAngka($('#oi_amount').val());

    if (!nama) {
      Swal.fire({ icon: 'warning', title: 'Customer Name is required' });
      return;
    }
    if (!kategori) {
      Swal.fire({ icon: 'warning', title: 'Income Category is required' });
      return;
    }
    if (!bank) {
      Swal.fire({ icon: 'warning', title: 'Bank is required' });
      return;
    }
    if (amount <= 0) {
      Swal.fire({ icon: 'warning', title: 'Amount must be greater than 0' });
      return;
    }

    var isi = {
      nama: nama,
      id_supplier: sup.id,
      kategori: kategori,
      bank: bank,
      tanggal: $('#oi_tanggal').val(),
      curr: $('#oi_curr').val(),
      amount: amount,
      rate: oiAngka($('#oi_rate').val()),
      amount_idr: oiAngka($('#oi_idr').val()),
      amount_usd: oiUsd,
      deskripsi: $.trim($('#oi_deskripsi').val()),
      pc: $('#oi_bank option:selected').data('pc') || ''
    };

    if (oiUbah >= 0) {
      oiBaris[oiUbah] = isi;
    } else {
      oiBaris.push(isi);
    }
    oiModeTambah();
  });

  $('#oi_batal').on('click', oiModeTambah);

  $('#oi_grid').on('click', '.oi-ubah', function () {
    oiIsiForm($(this).data('i'));
  });

  $('#oi_grid').on('click', '.oi-hapus', function () {
    var i = $(this).data('i');
    oiBaris.splice(i, 1);
    if (oiUbah === i) { oiModeTambah(); return; }
    if (oiUbah > i) { oiUbah--; }
    oiGambar();
  });

  $('#btnsave').on('click', function () {
    // tiap tab punya tombol Save sendiri; yang jalan cuma tab yang aktif
    if (!$('#tab-oi').hasClass('on')) { return; }

    if (oiBaris.length === 0) {
      Swal.fire({ icon: 'warning', title: 'No rows added yet' });
      return;
    }
    if (oiUbah >= 0) {
      Swal.fire({ icon: 'warning', title: 'Finish the row you are editing',
                  text: 'Press Update row to keep the change, or Cancel edit to drop it.' });
      return;
    }

    var edit = (oiMode === 'edit');
    var kirim = { baris: JSON.stringify(oiBaris) };
    if (edit) { kirim.no_doc = oiDoc; }

    var tombol = $(this).prop('disabled', true);
    $.post(edit ? 'cash_projection/update_other_inflow.php'
                : 'cash_projection/save_other_inflow.php', kirim, function (d) {
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

  oiGambar();
  oiAmbilKurs();
});
</script>
