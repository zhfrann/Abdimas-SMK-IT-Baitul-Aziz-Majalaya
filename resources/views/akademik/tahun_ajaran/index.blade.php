@extends('layouts.master')

@section('title', 'Tahun Ajaran')

@section('css')
    <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
    <x-breadcrumb item="Manajemen Kelas" active="Tahun Ajaran" />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between"">
                    <div>
                        <h5>Daftar Tahun Ajaran</h5>
                    </div>
                    <div class="">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#modalCreateTahunAjaran">
                            Tambah Tahun Ajaran
                        </button>
                    </div>
                    <!-- Modal Create Tahun Ajaran -->
                    <div class="modal fade" id="modalCreateTahunAjaran" tabindex="-1"
                        aria-labelledby="modalCreateTahunAjaranLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="formCreateTahunAjaran" method="POST"
                                    action="{{ route('akademik.tahun_ajaran.store') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalCreateTahunAjaranLabel">Tambah Tahun Ajaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="tahun" class="form-label">Tahun</label>
                                            <input type="text" class="form-control @error('tahun') is-invalid @enderror"
                                                id="tahun" name="tahun" value="{{ old('tahun') }}" required>
                                            @error('tahun')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="semester" class="form-label">Semester</label>
                                            <select class="form-select @error('semester') is-invalid @enderror"
                                                id="semester" name="semester" required>
                                                <option value="">Pilih Semester</option>
                                                <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>
                                                    Ganjil</option>
                                                <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>
                                                    Genap</option>
                                            </select>
                                            @error('semester')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tahun</th>
                                <th>Semester</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tahunAjaran as $ta)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ta->tahun }}</td>
                                    <td>{{ $ta->semester }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import {
            DataTable
        } from '/build/js/plugins/module.js';
        // Inisialisasi DataTable jika ingin
        window.dt = new DataTable('#pc-dt-simple');
    </script>
    <script>
        // Jika ada error validasi, buka modal otomatis
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('modalCreateTahunAjaran'));
            window.addEventListener('DOMContentLoaded', function() {
                myModal.show();
            });
        @endif
    </script>
@endsection
