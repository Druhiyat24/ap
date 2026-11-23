<?php
/* Tab PV Expense - isian + daftar baris yang akan disimpan.
   Bentuknya mengikuti tab Other Inflow (oi_form.php), bedanya:
   tanggalnya Due Date, namanya Supplier, kategorinya Cost Category
   (sisi Cash Out), dan banknya bank PEMBAYAR.

   Tidak ada isian "Payment Voucher No": di laporan kolom itu memang
   diisi nomor dokumen ini sendiri (EXP/<PC>/<mmyy>/<00001>), jadi
   tidak perlu diketik lagi.

   Pemanggilnya menyiapkan:
     $bank          - daftar akun bank & kas (wajib)
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
<!-- Wadah baris 1 & 2 - lihat keterangan bar unggah di bawah. -->
<div class="cfpu-wrap">
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
      <option value="">Select bank or cash account</option>
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
  <!-- BAR UNGGAH. SENGAJA DI LUAR kedua baris isian, bukan di dalam
       salah satunya: kalau di dalam, tinggi barisnya ikut mengembang
       dan isian di kiri jadi berjarak. Di layar lebar ia ditempel di
       ruang kosong kanan dgn tinggi mengikuti kedua baris itu; di
       layar sempit ia kembali mengalir biasa. Lihat .cfpu-kol di
       module/css/app-cfp.css. -->
  <div class="cfpu-kol">
    <!-- Label-nya ditulis SAMA PERSIS dgn isian tetangganya (<label><b>),
         bukan .app-flabel - dua gaya label tingginya beda 5px dan barnya
         jadi terlihat turun sendiri. -->
    <label><b>Upload rows from Excel</b></label>
    <div class="cfpu-bar" id="pv_drop">
      <!-- Satu-satunya pembuka dialog berkas: <label for>. Input-nya
           display:none dan TANPA atribut accept - accept membuat berkas
           ter-filter/abu-abu di dialog sehingga malah tidak bisa dipilih
           (pelajaran dari bar Memorial Journal). -->
      <label for="pv_berkas" class="cfpu-drop">
        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        <!-- SATU BARIS, bukan dua: bar ini duduk sebaris dgn isian lain, dan
             kalau lebih tinggi dari isian biasa seluruh barisnya ikut
             merenggang (dilaporkan pemakai 19 Nov 2026). Nama berkasnya
             menyambung di kanan teksnya, dipotong elipsis kalau kepanjangan. -->
        <span class="cfpu-txt">Click or drag an Excel file here</span>
        <span class="cfpu-file" id="pv_namafile">No file selected</span>
      </label>
      <div class="cfpu-act">
        <button type="button" class="app-btn app-btn-success app-btn-sm" id="pv_unggah">
          <i class="fa fa-upload"></i> Upload
        </button>
        <a class="app-btn app-btn-dark app-btn-sm" target="_blank"
           href="cash_projection/cfp_template.php?jenis=pv">
          <i class="fa fa-file-excel-o"></i> Template
        </a>
        <!-- Clear = membuang SELURUH isi daftar, bukan cuma hasil unggahan
             terakhir: daftarnya satu, dan memilah mana yang diunggah vs
             diketik akan menebak-nebak maksud pemakai. Kalimat konfirmasinya
             menyebut jumlah barisnya supaya jelas apa yang hilang. -->
        <button type="button" class="app-btn app-btn-danger app-btn-sm" id="pv_bersih">
          <i class="fa fa-eraser"></i> Clear
        </button>
      </div>
      <input type="file" id="pv_berkas" style="display:none;">
    </div>
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

  /* ---------------------------------------------------------------------
     UNGGAH. Barisnya DISAMBUNG ke daftar yang sudah ada, bukan menggantikan -
     "data nya turun ke bawah" (permintaan pemakai 19 Nov 2026). Jadi hasil
     ketikan tangan dan hasil unggahan bisa bercampur dalam satu dokumen, dan
     tombol Save-nya tidak perlu tahu bedanya: tetap SATU dokumen.

     Kotak berkasnya dikosongkan tiap selesai supaya berkas yang SAMA bisa
     diunggah lagi - tanpa itu, 'change' tidak pernah menyala untuk kedua
     kalinya dan terlihat seperti tombolnya rusak. */
  /* Memilih berkas HANYA menampilkan namanya - yang mengirim tombol Upload,
     sama dgn bar Memorial Journal. Pemakai jadi sempat memastikan berkasnya
     benar sebelum ratusan baris masuk ke daftar. */
  $('#pv_berkas').on('change', function () {
    var b = this.files && this.files[0];
    $('#pv_namafile').text(b ? b.name : 'No file selected').toggleClass('has-file', !!b);
  });

  /* Seret-lepas: berkasnya dijatuhkan ke input-nya supaya jalur berikutnya
     sama persis dgn memilih lewat dialog - tidak ada dua jalan berbeda. */
  $('#pv_drop').on('dragover', function (e) { e.preventDefault(); $(this).addClass('is-drag'); })
               .on('dragleave drop', function () { $(this).removeClass('is-drag'); })
               .on('drop', function (e) {
    e.preventDefault();
    var f = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
    if (f && f.length) {
      document.getElementById('pv_berkas').files = f;
      $('#pv_berkas').trigger('change');
    }
  });

  $('#pv_bersih').on('click', function () {
    if (!pvBaris.length) {
      Swal.fire({ icon: 'info', title: 'The list is already empty' });
      return;
    }
    Swal.fire({
      icon: 'warning', title: 'Clear the list?',
      text: pvBaris.length + ' row(s) will be removed from the list. Nothing is deleted from the database.',
      showCancelButton: true, confirmButtonText: 'Yes, clear it', cancelButtonText: 'Cancel'
    }).then(function (h) {
      if (!h.isConfirmed) { return; }
      pvBaris.length = 0;      /* dikosongkan DI TEMPAT, bukan diganti array baru -
                             supaya acuan lain ke daftar ini tidak tertinggal
                             menunjuk array lama. */
      pvModeTambah();
    });
  });

  $('#pv_unggah').on('click', function () {
    var inp = document.getElementById('pv_berkas');
    var berkas = inp.files && inp.files[0];
    if (!berkas) {
      Swal.fire({ icon: 'warning', title: 'No file selected',
                  text: 'Click the box on the left to choose the filled template.' });
      return;
    }

    var data = new FormData();
    data.append('jenis', 'pv');
    data.append('berkas', berkas);

    Swal.fire({ title: 'Reading the file...', allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); } });

    $.ajax({ url: 'cash_projection/cfp_unggah.php', type: 'POST', data: data,
             processData: false, contentType: false, dataType: 'json' })
      .done(function (r) {
        if (!r || r.status !== 'success') {
          Swal.fire({ icon: 'error', title: 'Upload failed',
                      text: (r && r.message) ? r.message : 'The file could not be read.' });
          return;
        }
        for (var i = 0; i < r.baris.length; i++) { pvBaris.push(r.baris[i]); }
        pvGambar();

        /* Kotak berkas dikosongkan SESUDAH berhasil, supaya berkas yang SAMA
           bisa diunggah lagi - tanpa itu 'change' tidak menyala untuk kedua
           kalinya dan tombolnya terlihat rusak. */
        document.getElementById('pv_berkas').value = '';
        $('#pv_namafile').text('No file selected').removeClass('has-file');

        /* Baris yang ditolak ditampilkan APA ADANYA, satu per satu dgn nomor
           barisnya - kalau cuma disebut jumlahnya, pemakai harus menebak mana
           yang salah di antara ratusan baris. */
        if (r.salah && r.salah.length) {
          Swal.fire({ icon: 'warning', title: r.baris.length + ' row(s) added',
                      html: '<div style="text-align:left;max-height:260px;overflow:auto;font-size:12px;">'
                            + r.salah.length + ' row(s) were skipped:<br><br>'
                            + r.salah.join('<br>') + '</div>' });
        } else if (r.baris.length) {
          Swal.fire({ icon: 'success', title: r.baris.length + ' row(s) added',
                      text: 'Check the list below, then press Save.' });
        } else {
          Swal.fire({ icon: 'info', title: 'Nothing to add',
                      text: 'No data row was found in that file.' });
        }
      })
      .fail(function (x) {
        Swal.fire({ icon: 'error', title: 'Upload failed',
                    text: (x.responseText || '').slice(0, 300) });
      });
  });

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

    /* SATU TANGGAL = SATU NOMOR DOKUMEN. Kalau barisnya memuat lebih dari
       satu tanggal - gampang terjadi sejak barisnya bisa diunggah - pemakai
       diberi tahu DULU berapa nomor yang akan lahir, baru disimpan. Waktu
       mengubah dokumen yang sudah ada, nomornya memang sudah tetap satu,
       jadi pertanyaan ini dilewati. */
    var tglUnik = [];
    for (var i = 0; i < pvBaris.length; i++) {
      if (tglUnik.indexOf(pvBaris[i].tanggal) < 0) { tglUnik.push(pvBaris[i].tanggal); }
    }

    if (!edit && tglUnik.length > 1) {
      Swal.fire({
        icon: 'question', title: tglUnik.length + ' document numbers will be created',
        html: 'The rows cover <b>' + tglUnik.length + ' different dates</b>, and one date '
              + 'becomes one document.<br><br>Continue?',
        showCancelButton: true, confirmButtonText: 'Yes, save', cancelButtonText: 'Cancel'
      }).then(function (h) { if (h.isConfirmed) { pvSimpan(edit); } });
      return;
    }
    pvSimpan(edit);
  });

  /* Dipisah jadi fungsi sendiri supaya jalur "langsung simpan" dan jalur
     "simpan sesudah dikonfirmasi" memakai kode yang SAMA - kalau disalin,
     cepat atau lambat keduanya berbeda. */
  function pvSimpan(edit) {
    /* Jenis laporannya ikut dikirim - dipakai hanya saat MEMBUAT;
       waktu mengubah, servernya memakai nilai dokumen lama. */
    var kirim = { baris: JSON.stringify(pvBaris),
                  type_report: (typeof CFP_REPORT !== 'undefined' ? CFP_REPORT : '') };
    if (edit) { kirim.no_doc = pvDoc; }

    var tombol = $('#btnsave').prop('disabled', true);
    $.post(edit ? 'cash_projection/update_pv_expense.php'
                : 'cash_projection/save_pv_expense.php', kirim, function (d) {
      if (d && d.status === 'ok') {
        var nomor = (d.no_doc_list && d.no_doc_list.length)
                  ? d.no_doc_list : [d.no_doc];
        Swal.fire({
          icon: 'success',
          title: edit ? 'Updated' : 'Saved',
          html: d.jml + ' row(s) saved in ' + nomor.length + ' document'
                + (nomor.length === 1 ? '' : 's') + '.<br>Document No:<br><b>'
                + nomor.join('</b><br><b>') + '</b>'
        }).then(function () { location.href = 'cash-projection.php'; });
      } else {
        tombol.prop('disabled', false);
        Swal.fire({ icon: 'error', title: 'Failed', text: (d && d.pesan) ? d.pesan : 'Unknown error' });
      }
    }, 'json').fail(function (xhr) {
      tombol.prop('disabled', false);
      Swal.fire({ icon: 'error', title: 'Failed', text: xhr.responseText || 'Cannot reach the server' });
    });
  }

  pvGambar();
  pvAmbilKurs();
});
</script>
