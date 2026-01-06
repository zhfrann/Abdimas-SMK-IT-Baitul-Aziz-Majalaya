@extends('layouts.master')

@section('title', 'Penilaian Ekstrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Ekstrakurikuler" active="Penilaian Ekstrakurikuler Osis"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ basic-table ] start -->
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Tabel Siswa</h5>
                <span class="d-block m-t-5"></span>
              </div>
              <div class="card-body table-border-style">
                <div class="table-responsive">
                  <table class="table" id="pc-dt-simple">
                    <thead>
                      <tr>
                        <th>Siswa</th>
                        {{-- <th data-type="date" data-format="YYYY/DD/MM">Start Date</th> --}}
                        <th>Deskripsi Penilaian</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ALYA NUR ZAHRA</td>
                            <td>
                                <p>Baik mampu menerapkan Dwi Darma maupun Dasa Darma, cakap memahami Sejarah dan teknik kepramukaan.</p>
                            </td>
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-light-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#penilaianModal"
                                    data-deskripsi="Baik mampu menerapkan Dwi Darma maupun Dasa Darma, cakap memahami Sejarah dan teknik kepramukaan.">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td>ADITYA RIZKI ARIFIN</td>
                            <td>
                                <p></p>
                            </td>
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-light-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#penilaianModal"
                                    data-deskripsi="">
                                    Tambah
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger">Hapus</button>
                            </td>
                        </tr>
                        <tr>
                            <td>BABY CANTIKA CAHAYA PERMATA</td>
                            <td>
                                <p>Baik mampu menerapkan Dwi Darma maupun Dasa Darma, cakap memahami Sejarah dan teknik kepramukaan.</p>
                            </td>
                            <td>
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-light-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#penilaianModal"
                                    data-deskripsi="Baik mampu menerapkan Dwi Darma maupun Dasa Darma, cakap memahami Sejarah dan teknik kepramukaan.">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ basic-table ] end -->
        </div>
        <!-- [ Main Content ] end -->

        <!-- Modal Edit Penilaian -->
        <div class="modal fade" id="penilaianModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Edit Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Deskripsi Penilaian</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="deskripsiPenilaianInput"
                    name="deskripsi_penilaian"
                    placeholder="Masukkan deskripsi penilaian">
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                  Batal
                </button>
                <button type="button" class="btn btn-primary">
                  Simpan
                </button>
              </div>

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
    <!-- [Page Specific JS] end -->

    <script>
        const modal = document.getElementById('penilaianModal');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // tombol Edit
            const deskripsi = button.getAttribute('data-deskripsi');

            const input = modal.querySelector('#deskripsiPenilaianInput');
            input.value = deskripsi;
        });
    </script>
@endsection