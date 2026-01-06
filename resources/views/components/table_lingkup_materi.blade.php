@extends('layouts.master')

@section('title', 'Lingkup Materi')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Lingkup Materi"/>

<div class="row">
  <!-- [ basic-table ] start -->
  <div class="col-xl-12">
    <div class="card">

      {{-- Header --}}
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Nama pelajaran: </h5>
          <span class="d-block m-t-5">kelas</span>
        </div>

        <button
          type="button"
          class="btn btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#lingkupMateriModal"
          data-mode="create"
          data-title="Tambah Lingkup Materi"
          data-id=""
          data-nama=""
        >
          Add Lingkup Materi
        </button>
      </div>

      {{-- Body --}}
      <div class="card-body table-border-style">
        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>id</th>
                <th>Nama materi</th>
                <th style="width: 180px">Actions</th>
              </tr>
            </thead>
            <tbody>
              {{-- CONTOH DATA STATIS (ganti jadi foreach kalau sudah dari DB) --}}
              <tr>
                <td>1</td>
                <td>membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama</td>
                <td>
                  {{-- UPDATE => BUKA MODAL EDIT & NGISI DATA DARI data-* --}}
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="1"
                    data-nama="membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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

              {{-- Kalau kamu sudah dari DB, contoh:
              @foreach($lingkupMateri as $lm)
                <tr>
                  <td>{{ $lm->id }}</td>
                  <td>{{ $lm->nama }}</td>
                  <td>
                    <button ... data-id="{{ $lm->id }}" data-nama="{{ $lm->nama }}">Update</button>
                    <form action="/lingkup-materi/{{ $lm->id }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button ...>Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
              --}}
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <!-- [ basic-table ] end -->
</div>

{{-- ===================== MODAL ADD/EDIT ===================== --}}
<div class="modal fade" id="lingkupMateriModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="lingkupMateriForm" method="POST" action="/lingkup-materi">
        @csrf
        <div id="methodSpoof"></div>

        <div class="modal-header">
          <h5 class="modal-title" id="lingkupMateriModalTitle">Tambah Lingkup Materi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="lm_id">

          <div class="mb-3">
            <label class="form-label">Nama materi</label>
            <input type="text" class="form-control" name="nama" id="lm_nama" placeholder="Masukkan nama materi" required>
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
    const modalEl = document.getElementById('lingkupMateriModal');

    modalEl.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;

      const mode = btn.getAttribute('data-mode') || 'create';
      const title = btn.getAttribute('data-title') || 'Tambah Lingkup Materi';

      const id = btn.getAttribute('data-id') || '';
      const nama = btn.getAttribute('data-nama') || '';

      // title
      document.getElementById('lingkupMateriModalTitle').textContent = title;

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
      document.getElementById('lingkupMateriModalTitle').textContent = 'Tambah Lingkup Materi';
    });
  </script>
  <!-- [Page Specific JS] end -->
@endsection
