<?php
/* Tab PV Expense - isian + daftar baris yang akan disimpan.
   Bentuknya mengikuti tab Other Inflow (oi_form.php), bedanya:
   tanggalnya Due Date, namanya Supplier, kategorinya Cost Category
   (sisi Cash Out), dan banknya bank PEMBAYAR.

   Tidak ada isian "Payment Voucher No": di laporan kolom itu memang
   diisi nomor dokumen ini sendiri (EXP/<PC>/<mmyy>/<00001>), jadi
   tidak perlu diketik lagi.

   Pemanggilnya menyiapkan:
     $bank          - daftar akun bank (wajib)
     $kategori_out  - daftar Cost Category dari master (wajib)
     $supplier      - daftar supplier dari mastersupplier, id => nama (wajib)
     $cfp_mode      - 'create' (bawaan) atau 'edit'
     $cfp_doc       - nomor dokumen, hanya untuk mode edit
     $cfp_pv        - isi awal daftar baris, hanya untuk mode edit */

$cfp_mode = isset($cfp_mode) ? $cfp_mode : 'create';
$cfp_doc  = isset($cfp_doc)  ? $cfp_doc  : '';
$cfp_pv   = isset($cfp_pv)   ? $cfp_pv   : array();
?>

<div class="pco-sec"><i class="fa fa-sign-out" aria-hidden="true"></i> PV Expense</div>

<!-- Semua baris memakai col-lg-3: empat kolom sama lebar, jadi isian di
     baris bawah lurus dgn isian di atasnya. -->
<div class="row">
  <div class="col-lg-2 col-md-6 mb-2">
    <label for="pv_tanggal"><b>Due Date</b> <i>*</i></label>
    <input type="text" class="form-control tanggal" id="pv_tanggal" autocomplete="off"
           value="<?php echo date('d M Y'); ?>">
  </div>

  <!-- Dipilih dari mastersupplier (yang disimpan Id_Supplier-nya), TAPI masih
       boleh diketik bebas: di data aslinya banyak baris yang pembayarnya bukan
       supplier melainkan departemen atau periode gaji. Yang diketik bebas
       id-nya kosong. -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label for="pv_nama"><b>Supplier Name</b> <i>*</i></label>
    <select class="form-control app-select2-tag" id="pv_nama">
      <option value="">Select supplier or type</option>
      <?php foreach ($supplier as $id => $nm) { ?>
        <option value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" data-m="1">
          <?php echo htmlspecialchars($nm, ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

 

  <div class="col-lg-3 col-md-6 mb-2">
    <label for="pv_bank"><b>Bank</b> <i>*</i></label>
    <select class="form-control app-select2" id="pv_bank">
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
    <label for="pv_amount"><b>Amount</b> <i>*</i></label>
    <input type="text" class="form-control text-right" id="pv_amount" autocomplete="off" value="0,00">
  </div>

  <div class="col-lg-3 col-md-6 mb-2">
    <label class="cfp-dual-label">
      <span style="flex:0 0 38%;"><b>Currency</b></span>
      <span style="flex:1;"><b>Rate</b></span>
    </label>
    <div class="input-group">
      <input type="text" class="form-control" id="pv_curr" value="IDR" readonly style="max-width:38%;">
      <input type="text" class="form-control text-right" id="pv_rate" value="1,00" readonly>
    </div>
  </div>

  <!-- Tidak ada Equivalent USD di sini: rekeningnya cuma SATU, jadi kalau
       mata uangnya USD nilai dolarnya ya Amount itu sendiri - cuma
       mengulang. Yang perlu dilihat tinggal nilai rupiahnya. (Book
       Transfer beda: di sana ada dua rekening yang mata uangnya bisa
       berbeda, jadi dua-duanya memang perlu.) -->
  <div class="col-lg-3 col-md-6 mb-2">
    <label for="pv_idr"><b>Equivalent IDR</b></label>
    <input type="text" class="form-control text-right" id="pv_idr" value="0,00" readonly>
  </div>
</div>

<div class="row">
   <div class="col-lg-2 col-md-6 mb-2">
    <label for="pv_kategori"><b>Cost Category</b> <i>*</i></label>
    <select class="form-control app-select2" id="pv_kategori">
      <option value="">Select cost category</option>
      <?php foreach ($kategori_out as $k) { ?>
        <option value="<?php echo htmlspecialchars($k, ENT_QUOTES); ?>">
          <?php echo htmlspecialchars($k, ENT_QUOTES); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="col-lg-5 col-md-8 mb-2">
    <label for="pv_deskripsi"><b>Description</b></label>
    <input type="text" class="form-control" id="pv_deskripsi" autocomplete="off" maxlength="255">
  </div>
  <div class="col-lg-3 col-md-4 mb-2">
    <label class="d-none d-lg-block">&nbsp;</label>
    <div class="ftl-actions">
      <button type="button" class="app-btn app-btn-primary" id="pv_add">
        <i class="fa fa-plus"></i> <span id="pv_add_teks">Add to list</span>
      </button>
      <button type="button" class="app-btn app-btn-danger" id="pv_batal" style="display:none;">
        <i class="fa fa-times"></i> Cancel edit
      </button>
    </div>
  </div>
</div>

<div id="pv_pesan_kurs" class="mb-2" style="display:none; font-size:11.5px; color:#8a5a06;"></div>

<div class="pco-sec mt-3"><i class="fa fa-list" aria-hidden="true"></i> Rows to be saved</div>
<div class="cfp-gridwrap">
  <table class="table cfp-grid mb-0" id="pv_grid">
    <thead>
      <tr>
        <th>#</th><th>Supplier Name</th><th>Cost Category</th><th>Bank</th><th>Due Date</th><th>Curr</th>
        <th class="num">Amount</th><th class="num">Rate</th>
        <th class="num">Equivalent IDR</th>
        <th>Description</th><th>PC</th><th>Status</th><th></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <td colspan="8">TOTAL <span id="pv_jml">0</span> row(s)</td>
        <td class="num" id="pv_total">0,00</td>
        <td colspan="4"></td>
      </tr>
    </tfoot>
  </table>
</div>

<script>
/* Daftar baris yang akan disimpan. */
var pvBaris = <?php echo json_encode(array_values($cfp_pv)); ?>;

/* Kurs PAJAK USD pada Due Date. */
var pvKurs  = 1;
var pvPesan = '';
var pvMinta = 0;

/* Baris yang sedang diubah; -1 artinya sedang menambah baris baru. */
var pvUbah = -1;

/* Nilai dolar baris yang sedang diisi - tidak ditampilkan, lihat pvHitung(). */
var pvUsd = 0;

var pvMode = '<?php echo $cfp_mode; ?>';
var pvDoc  = '<?php echo htmlspecialchars($cfp_doc, ENT_QUOTES); ?>';

/* 1.234.567,89 <-> angka */
function pvFormat(n) {
  return (Number(n) || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function pvAngka(teks) {
  if (!teks) { return 0; }
  return Number(String(teks).replace(/\./g, '').replace(',', '.')) || 0;
}

/* Currency ikut akun Bank pembayar. */
function pvIsiCurrency() {
  $('#pv_curr').val($('#pv_bank option:selected').data('curr') || 'IDR');
  pvHitung();
}

/* Kurs PAJAK USD dari master, mengikuti Due Date. */
function pvAmbilKurs() {
  pvMinta++;
  var punyaku = pvMinta;

  $.getJSON('get_kurs_projection.php', { tanggal: $('#pv_tanggal').val(), curr: 'USD' }, function (d) {
    if (punyaku !== pvMinta) { return; }
    pvKurs = Number(d.rate) || 0;
    if (!d.ketemu) {
      pvPesan = 'No PAJAK rate for this date in master rate.';
    } else if (d.mundur) {
      pvPesan = 'Using PAJAK rate dated <b>' + d.tanggal_tampil + '</b> - no rate yet for the document date.';
    } else {
      pvPesan = '';
    }
    pvHitung();
  });
}

/* Uang keluar dari akun rupiah tidak menyentuh kurs: Rate 1, dolarnya 0. */
function pvHitung() {
  var amount = pvAngka($('#pv_amount').val());
  var rupiah = ($('#pv_curr').val() === 'IDR');

  var rate = rupiah ? 1 : pvKurs;
  var idr  = rupiah ? amount : amount * rate;
  var usd  = rupiah ? 0 : amount;

  $('#pv_rate').val(pvFormat(rate));
  $('#pv_idr').val(pvFormat(idr));
  /* usd tidak punya kotak isian - nilainya tetap dihitung & ikut disimpan,
     supaya laporan yang butuh sisi dolar tidak perlu menghitung ulang. */
  pvUsd = usd;

  if (rupiah || pvPesan === '') {
    $('#pv_pesan_kurs').hide();
  } else {
    $('#pv_pesan_kurs').html(pvPesan).show();
  }
}

/* Baca isian Supplier Name. Pilihan yang berasal dari master ditandai
   data-m="1" - nilainya Id_Supplier, tulisannya nama resminya. Selain itu
   berarti diketik sendiri, nilainya teks apa adanya dan id-nya kosong. */
function pvBacaNama() {
  var $o = $('#pv_nama option:selected');
  var v  = $('#pv_nama').val() || '';
  if ($o.length && $o.attr('data-m') === '1') {
    return { id: v, nama: $.trim($o.text()) };
  }
  return { id: '', nama: $.trim(v) };
}

/* Isi balik dropdown-nya. Nama ketikan sendiri - dan supplier yang sejak
   dokumen dibuat sudah dinonaktifkan - belum ada di daftar, jadi
   pilihannya ditambahkan dulu. */
function pvPilihNama(id, nama) {
  var $s = $('#pv_nama');

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
function pvModeTambah() {
  pvUbah = -1;
  $('#pv_add_teks').text('Add to list');
  $('#pv_batal').hide();
  $('#pv_amount').val('0,00');
  $('#pv_idr').val('0,00');
  $('#pv_deskripsi').val('');
  $('#pv_nama').val('').trigger('change');
  pvGambar();
}

/* Tarik satu baris kembali ke isian untuk diubah. */
function pvIsiForm(i) {
  var b = pvBaris[i];
  if (!b) { return; }

  pvUbah = i;
  $('#pv_tanggal').val(b.tanggal);
  pvPilihNama(b.id_supplier, b.nama);
  $('#pv_kategori').val(b.kategori).trigger('change');
  $('#pv_bank').val(b.bank).trigger('change');
  $('#pv_amount').val(pvFormat(b.amount));
  $('#pv_deskripsi').val(b.deskripsi || '');

  pvAmbilKurs();
  $('#pv_add_teks').text('Update row');
  $('#pv_batal').show();
  pvGambar();
}

/* Gambar ulang tabel baris. */
function pvGambar() {
  var html = '';
  var total = 0;

  for (var i = 0; i < pvBaris.length; i++) {
    var b = pvBaris[i];
    total += b.amount_idr;
    html += '<tr' + (i === pvUbah ? ' class="bt-sedang"' : '') + '>'
          + '<td>' + (i + 1) + '</td>'
          + '<td>' + b.nama + '</td>'
          + '<td>' + b.kategori + '</td>'
          + '<td>' + b.bank + '</td>'
          + '<td>' + b.tanggal + '</td>'
          + '<td>' + b.curr + '</td>'
          + '<td class="num">' + pvFormat(b.amount) + '</td>'
          + '<td class="num">' + pvFormat(b.rate) + '</td>'
          + '<td class="num">' + pvFormat(b.amount_idr) + '</td>'
          + '<td>' + (b.deskripsi || '-') + '</td>'
          + '<td>' + b.pc + '</td>'
          + '<td>Post</td>'
          + '<td class="text-right" style="white-space:nowrap;">'
          + '<button type="button" class="app-btn app-btn-primary app-btn-sm pv-ubah" data-i="' + i + '">'
          + '<i class="fa fa-pencil"></i></button> '
          + '<button type="button" class="app-btn app-btn-danger app-btn-sm pv-hapus" data-i="' + i + '">'
          + '<i class="fa fa-trash"></i></button></td>'
          + '</tr>';
  }

  if (pvBaris.length === 0) {
    html = '<tr><td colspan="13" class="cfp-kosong">No rows yet. Fill the form above then press Add to list.</td></tr>';
  }

  $('#pv_grid tbody').html(html);
  $('#pv_jml').text(pvBaris.length);
  $('#pv_total').text(pvFormat(total));
}

$(function () {
  $('#pv_bank').on('change', pvIsiCurrency);
  $('#pv_tanggal').on('change', pvAmbilKurs);
  $('#pv_amount').on('keyup change', pvHitung);

  $('#pv_amount').on('blur', function () {
    $(this).val(pvFormat(pvAngka($(this).val())));
    pvHitung();
  });

  $('#pv_add').on('click', function () {
    var sup      = pvBacaNama();
    var nama     = sup.nama;
    var kategori = $('#pv_kategori').val();
    var bank     = $('#pv_bank').val();
    var amount   = pvAngka($('#pv_amount').val());

    if (!nama) {
      Swal.fire({ icon: 'warning', title: 'Supplier Name is required' });
      return;
    }
    if (!kategori) {
      Swal.fire({ icon: 'warning', title: 'Cost Category is required' });
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
      tanggal: $('#pv_tanggal').val(),
      curr: $('#pv_curr').val(),
      amount: amount,
      rate: pvAngka($('#pv_rate').val()),
      amount_idr: pvAngka($('#pv_idr').val()),
      amount_usd: pvUsd,
      deskripsi: $.trim($('#pv_deskripsi').val()),
      pc: $('#pv_bank option:selected').data('pc') || ''
    };

    if (pvUbah >= 0) {
      pvBaris[pvUbah] = isi;
    } else {
      pvBaris.push(isi);
    }
    pvModeTambah();
  });

  $('#pv_batal').on('click', pvModeTambah);

  $('#pv_grid').on('click', '.pv-ubah', function () {
    pvIsiForm($(this).data('i'));
  });

  $('#pv_grid').on('click', '.pv-hapus', function () {
    var i = $(this).data('i');
    pvBaris.splice(i, 1);
    if (pvUbah === i) { pvModeTambah(); return; }
    if (pvUbah > i) { pvUbah--; }
    pvGambar();
  });

  $('#btnsave').on('click', function () {
    // tiap tab punya tombol Save sendiri; yang jalan cuma tab yang aktif
    if (!$('#tab-pv').hasClass('on')) { return; }

    if (pvBaris.length === 0) {
      Swal.fire({ icon: 'warning', title: 'No rows added yet' });
      return;
    }
    if (pvUbah >= 0) {
      Swal.fire({ icon: 'warning', title: 'Finish the row you are editing',
                  text: 'Press Update row to keep the change, or Cancel edit to drop it.' });
      return;
    }

    var edit = (pvMode === 'edit');
    var kirim = { baris: JSON.stringify(pvBaris) };
    if (edit) { kirim.no_doc = pvDoc; }

    var tombol = $(this).prop('disabled', true);
    $.post(edit ? 'cash_projection/update_pv_expense.php'
                : 'cash_projection/save_pv_expense.php', kirim, function (d) {
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

  pvGambar();
  pvAmbilKurs();
});
</script>
