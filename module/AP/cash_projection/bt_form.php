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

<!-- Wadah baris 1 & 2 - lihat keterangan bar unggah di bawah. -->
<div class="cfpu-wrap">
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
    <div class="cfpu-bar" id="bt_drop">
      <!-- Satu-satunya pembuka dialog berkas: <label for>. Input-nya
           display:none dan TANPA atribut accept - accept membuat berkas
           ter-filter/abu-abu di dialog sehingga malah tidak bisa dipilih
           (pelajaran dari bar Memorial Journal). -->
      <label for="bt_berkas" class="cfpu-drop">
        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        <!-- SATU BARIS, bukan dua: bar ini duduk sebaris dgn isian lain, dan
             kalau lebih tinggi dari isian biasa seluruh barisnya ikut
             merenggang (dilaporkan pemakai 19 Nov 2026). Nama berkasnya
             menyambung di kanan teksnya, dipotong elipsis kalau kepanjangan. -->
        <span class="cfpu-txt">Click or drag an Excel file here</span>
        <span class="cfpu-file" id="bt_namafile">No file selected</span>
      </label>
      <div class="cfpu-act">
        <button type="button" class="app-btn app-btn-success app-btn-sm" id="bt_unggah">
          <i class="fa fa-upload"></i> Upload
        </button>
        <a class="app-btn app-btn-dark app-btn-sm" target="_blank"
           href="cash_projection/cfp_template.php?jenis=bt">
          <i class="fa fa-file-excel-o"></i> Template
        </a>
        <!-- Clear = membuang SELURUH isi daftar, bukan cuma hasil unggahan
             terakhir: daftarnya satu, dan memilah mana yang diunggah vs
             diketik akan menebak-nebak maksud pemakai. Kalimat konfirmasinya
             menyebut jumlah barisnya supaya jelas apa yang hilang. -->
        <button type="button" class="app-btn app-btn-danger app-btn-sm" id="bt_bersih">
          <i class="fa fa-eraser"></i> Clear
        </button>
      </div>
      <input type="file" id="bt_berkas" style="display:none;">
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
  $('#bt_berkas').on('change', function () {
    var b = this.files && this.files[0];
    $('#bt_namafile').text(b ? b.name : 'No file selected').toggleClass('has-file', !!b);
  });

  /* Seret-lepas: berkasnya dijatuhkan ke input-nya supaya jalur berikutnya
     sama persis dgn memilih lewat dialog - tidak ada dua jalan berbeda. */
  $('#bt_drop').on('dragover', function (e) { e.preventDefault(); $(this).addClass('is-drag'); })
               .on('dragleave drop', function () { $(this).removeClass('is-drag'); })
               .on('drop', function (e) {
    e.preventDefault();
    var f = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
    if (f && f.length) {
      document.getElementById('bt_berkas').files = f;
      $('#bt_berkas').trigger('change');
    }
  });

  $('#bt_bersih').on('click', function () {
    if (!btBaris.length) {
      Swal.fire({ icon: 'info', title: 'The list is already empty' });
      return;
    }
    Swal.fire({
      icon: 'warning', title: 'Clear the list?',
      text: btBaris.length + ' row(s) will be removed from the list. Nothing is deleted from the database.',
      showCancelButton: true, confirmButtonText: 'Yes, clear it', cancelButtonText: 'Cancel'
    }).then(function (h) {
      if (!h.isConfirmed) { return; }
      btBaris.length = 0;      /* dikosongkan DI TEMPAT, bukan diganti array baru -
                             supaya acuan lain ke daftar ini tidak tertinggal
                             menunjuk array lama. */
      btModeTambah();
    });
  });

  $('#bt_unggah').on('click', function () {
    var inp = document.getElementById('bt_berkas');
    var berkas = inp.files && inp.files[0];
    if (!berkas) {
      Swal.fire({ icon: 'warning', title: 'No file selected',
                  text: 'Click the box on the left to choose the filled template.' });
      return;
    }

    var data = new FormData();
    data.append('jenis', 'bt');
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
        for (var i = 0; i < r.baris.length; i++) { btBaris.push(r.baris[i]); }
        btGambar();

        /* Kotak berkas dikosongkan SESUDAH berhasil, supaya berkas yang SAMA
           bisa diunggah lagi - tanpa itu 'change' tidak menyala untuk kedua
           kalinya dan tombolnya terlihat rusak. */
        document.getElementById('bt_berkas').value = '';
        $('#bt_namafile').text('No file selected').removeClass('has-file');

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

    /* SATU TANGGAL = SATU NOMOR DOKUMEN. Kalau barisnya memuat lebih dari
       satu tanggal - gampang terjadi sejak barisnya bisa diunggah - pemakai
       diberi tahu DULU berapa nomor yang akan lahir, baru disimpan. Waktu
       mengubah dokumen yang sudah ada, nomornya memang sudah tetap satu,
       jadi pertanyaan ini dilewati. */
    var tglUnik = [];
    for (var i = 0; i < btBaris.length; i++) {
      if (tglUnik.indexOf(btBaris[i].tanggal) < 0) { tglUnik.push(btBaris[i].tanggal); }
    }

    if (!edit && tglUnik.length > 1) {
      Swal.fire({
        icon: 'question', title: tglUnik.length + ' document numbers will be created',
        html: 'The rows cover <b>' + tglUnik.length + ' different dates</b>, and one date '
              + 'becomes one document.<br><br>Continue?',
        showCancelButton: true, confirmButtonText: 'Yes, save', cancelButtonText: 'Cancel'
      }).then(function (h) { if (h.isConfirmed) { btSimpan(edit); } });
      return;
    }
    btSimpan(edit);
  });

  /* Dipisah jadi fungsi sendiri supaya jalur "langsung simpan" dan jalur
     "simpan sesudah dikonfirmasi" memakai kode yang SAMA - kalau disalin,
     cepat atau lambat keduanya berbeda. */
  function btSimpan(edit) {
    /* Jenis laporannya ikut dikirim - dipakai hanya saat MEMBUAT;
       waktu mengubah, servernya memakai nilai dokumen lama. */
    var kirim = { baris: JSON.stringify(btBaris),
                  type_report: (typeof CFP_REPORT !== 'undefined' ? CFP_REPORT : '') };
    if (edit) { kirim.no_doc = btDoc; }

    var tombol = $('#btnsave').prop('disabled', true);
    $.post(edit ? 'cash_projection/update_book_transfer.php'
                : 'cash_projection/save_book_transfer.php', kirim, function (d) {
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

  btGambar();
  btAmbilKurs();   // kurs hari ini langsung terisi
});
</script>
