@extends('layouts.master')

@section('title', 'Asesmen Formatif')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Asesmen Formatif"/>

<div class="row">
  <!-- [ basic-table ] start -->
  <div class="col-xl-12">
    <div class="card">

      {{-- Header --}}
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Pendidikan Agama Islam dan Budi Pekerti</h5>
          <span class="d-block m-t-5">Kelas 12</span>
        </div>
      </div>

      {{-- Body --}}
      <div class="card-body table-border-style">
        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Deskripsi Capaian Tertinggi</th>
                <th>Deskripsi Capaian Terendah</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>ADITYA RIZKI ARIFIN</td>
                <td class="text-wrap">Aditya Rizki Arifin menunjukkan pemahaman dalam membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama, menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.,</td>
                <td class="text-wrap">Aditya Rizki Arifin membutuhkan bimbingan dalam menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf,</td>
                <td>
                  <a href="/components/detail_asesmen_formatif">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>ALYA NUR ZAHRA</td>
                <td class="text-wrap">Alya Nur Zahra menunjukkan pemahaman dalam menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait., menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf, </td>
                <td class="text-wrap">Alya Nur Zahra membutuhkan bimbingan dalam menghafal Q.S. Al-Hujurat/49:12 dengan fasih dan lancar., </td>
                <td>
                  <a href="#">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>ARSYAD FATHI MAWARDI</td>
                <td class="text-wrap">Arsyad Fathi Mawardi menunjukkan pemahaman dalam menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait., menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf, </td>
                <td class="text-wrap">Arsyad Fathi Mawardi membutuhkan bimbingan dalam menghafal Q.S. Al-Hujurat/49:12 dengan fasih dan lancar., </td>
                <td>
                  <a href="#">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>
              <tr>
                <td>4</td>
                <td>BABY CANTIKA CAHAYA PERMATA</td>
                <td class="text-wrap">Baby Cantika Cahaya Permata menunjukkan pemahaman dalam menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait., menganalisisQ.S. Al-Hujurat/49:12, serta Hadits prasangka baik (husnuzzan)., membaca Q.S. Al-Hujurat/49:12, sesuai dengan kaidah tajwid dan makharijul huruf, </td>
                <td class="text-wrap">Baby Cantika Cahaya Permata membutuhkan bimbingan dalam menghafal Q.S. Al-Hujurat/49:12 dengan fasih dan lancar., </td>
                <td>
                  <a href="#">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <a href="/components/table_intrakurikuler" class="btn btn-light-secondary">Kembali</a>
      </div>
    </div>
  </div>
  <!-- [ basic-table ] end -->
</div>

@endsection

@section('scripts')
  <!-- [Page Specific JS] start -->
  <script type="module">
    import { DataTable } from '/build/js/plugins/module.js';
    window.dt = new DataTable('#pc-dt-simple');
  </script>
  
  <script>
    const modalEl = document.getElementById('tujuanPembelajaranModal');

    modalEl.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;

      const mode = btn.getAttribute('data-mode') || 'create';
      const title = btn.getAttribute('data-title') || 'Tambah Lingkup Materi';

      const id = btn.getAttribute('data-id') || '';
      const nama = btn.getAttribute('data-nama') || '';

      // title
      document.getElementById('tujuanPembelajaranModalTitle').textContent = title;

      // fill input
      document.getElementById('lm_id').value = id;
      document.getElementById('lm_nama').value = nama;

      // set form action + method
      const form = document.getElementById('lingkupMateriForm');
      const methodSpoof = document.getElementById('methodSpoof');
      methodSpoof.innerHTML = '';

      if (mode === 'edit' && id) {
        form.action = `/lingkup-materi/${id}`;   // <- SESUAIKAN kalau route kamu beda
        methodSpoof.innerHTML = `@method('PUT')`;
      } else {
        form.action = `/lingkup-materi`;        // <- SESUAIKAN kalau route kamu beda
      }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
      // optional reset ketika modal ditutup
      document.getElementById('lm_id').value = '';
      document.getElementById('lm_nama').value = '';
      document.getElementById('methodSpoof').innerHTML = '';
      document.getElementById('lingkupMateriForm').action = `/lingkup-materi`;
      document.getElementById('tujuanPembelajaranModalTitle').textContent = 'Tambah Lingkup Materi';
    });
  </script>
  <!-- [Page Specific JS] end -->
@endsection
