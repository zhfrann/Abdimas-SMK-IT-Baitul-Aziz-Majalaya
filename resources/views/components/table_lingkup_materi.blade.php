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
          <h5 class="mb-0">Pendidikan Agama Islam dan Budi Pekerti</h5>
          <span class="d-block m-t-5">Kelas 12</span>
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
          Tambah Lingkup Materi
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
                <td>A. Kajian Q.S Al-An-fal 8:72 dan hadist tentang pentingnya mengendalikan diri (Mujahadah an-nafs)</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="1"
                    data-nama="A. Kajian Q.S Al-An-fal 8:72 dan hadist tentang pentingnya mengendalikan diri (Mujahadah an-nafs)"
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
              <tr>
                <td>2</td>
                <td>B. Kajian Q.S AL-Hujurat / 49 :12 dan Hadist tentang Berprasangka</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="2"
                    data-nama="B. Kajian Q.S AL-Hujurat / 49 :12 dan Hadist tentang Berprasangka"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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
                <td>C. Kajian Q.S AL-Hujurat 49: 10 dan Hadist tentang Indahnya Persaudaraan (ukkhuwwah)</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="3"
                    data-nama="C. Kajian Q.S AL-Hujurat 49: 10 dan Hadist tentang Indahnya Persaudaraan (ukkhuwwah)"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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
                <td>D.Kajian Q.S AL-ISRA 17:32 Q.S An-nur 24:2 dan Hadist tentang Menjaga Diri dari Pergaulan Bebas dan perbauatan Mendekati Zina</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="4"
                    data-nama="D.Kajian Q.S AL-ISRA 17:32 Q.S An-nur 24:2 dan Hadist tentang Menjaga Diri dari Pergaulan Bebas dan perbauatan Mendekati Zina"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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
                <td>E. Meneladani ALLAH SWT melalui Asmaul husna</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="5"
                    data-nama="E. Meneladani ALLAH SWT melalui Asmaul husna"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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
                <td>F Menghadirkan malaikat dalam kehidupan sehari-hari</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-sm btn-light-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#lingkupMateriModal"
                    data-mode="edit"
                    data-title="Edit Lingkup Materi"
                    data-id="6"
                    data-nama="F Menghadirkan malaikat dalam kehidupan sehari-hari"
                  >
                    Update
                  </button>

                  {{-- DELETE (contoh pakai form biar aman) --}}
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

        <a href="/components/table_intrakurikuler" class="btn btn-light-secondary">Kembali</a>
      </div>
    </div>
  </div>
  <!-- [ basic-table ] end -->
</div>

{{-- ===================== MODAL ADD/EDIT ===================== --}}
<div class="modal fade" id="lingkupMateriModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
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
