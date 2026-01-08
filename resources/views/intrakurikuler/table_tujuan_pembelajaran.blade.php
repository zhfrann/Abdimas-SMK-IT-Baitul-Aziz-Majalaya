@extends('layouts.master')

@section('title', 'Tujuan Pembelajaran')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Tujuan Pembelajaran"/>

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

        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#tujuanPembelajaranModal"
          data-mode="create"
          data-title="Tambah Tujuan Pembelajaran"
          data-id=""
          data-nama=""
        >
          Tambah Tujuan Pembelajaran
        </button>
      </div>

      {{-- Body --}}
      <div class="card-body table-border-style">
        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama materi</th>
                <th style="width: 180px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="1"
                    data-nama="membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama"
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/1" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="2"
                    data-nama="menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait."
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/2" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs).</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="3"
                    data-nama="menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs)."
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/3" method="POST" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>4</td>
                <td>membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf.</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="4"
                    data-nama="membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf."
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/4" method="POST" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>5</td>
                <td>menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar.</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="5"
                    data-nama="menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar."
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/5" method="POST" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <tr>
                <td>6</td>
                <td>menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits. </td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#tujuanPembelajaranModal"
                    data-mode="edit"
                    data-title="Edit Tujuan Pembelajaran"
                    data-id="6"
                    data-nama="menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits. "
                  >
                    Update
                  </button>

                  <form action="/lingkup-materi/6" method="POST" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger"
                      onclick="return confirm('Yakin hapus data ini?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <a href="{{ route('intrakurikuler.index') }}" class="btn btn-light-secondary">Kembali</a>
      </div>
    </div>
  </div>
  <!-- [ basic-table ] end -->
</div>

{{-- ===================== MODAL ADD/EDIT ===================== --}}
<div class="modal fade" id="tujuanPembelajaranModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form id="lingkupMateriForm" method="POST" action="/lingkup-materi">
        @csrf
        <div id="methodSpoof"></div>

        <div class="modal-header">
          <h5 class="modal-title" id="tujuanPembelajaranModalTitle">Tambah Tujuan Pembelajaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="lm_id">

          <div class="mb-3">
            <label class="form-label">Tujuan Pembelajaran</label>
            <input type="text" class="form-control" name="nama" id="lm_nama" placeholder="Masukkan tujuan pembelajaran" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

      </form>
    </div>
  </div>
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
