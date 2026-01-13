@extends('layouts.master')

@section('title', 'Manajemen Kelas Ajar')

@section('css')
    <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
    <x-breadcrumb item="Manajemen Kelas" active="Manajemen Kelas" />

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Daftar Kelas Ajar</h5>
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#modalCreateKelasAjar">
                        Tambah Kelas Ajar
                    </button>
                </div>
                <div class="card-body">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Wali Kelas</th>
                                <th>Manage Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kelasAjar as $ka)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $ka->kelas->nama_kelas ?? '-' }}</td>
                                    <td>{{ $ka->tahunAjaran->tahun ?? '-' }}</td>
                                    <td>{{ $ka->tahunAjaran->semester ?? '-' }}</td>
                                    <td>{{ $ka->waliKelas->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('akademik.siswa.index', $ka->kelas_ajar_id) }}"
                                            class="btn btn-sm btn-primary">Manage Siswa</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Kelas Ajar -->
    <div class="modal fade" id="modalCreateKelasAjar" tabindex="-1" aria-labelledby="modalCreateKelasAjarLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCreateKelasAjar" method="POST" action="{{ route('akademik.kelas.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCreateKelasAjarLabel">Tambah Kelas Ajar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror"
                                id="nama_kelas" name="nama_kelas" value="{{ old('nama_kelas') }}" required>
                            @error('nama_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror" id="tahun_ajaran_id"
                                name="tahun_ajaran_id" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach (\App\Models\TahunAjaran::orderByDesc('tahun_ajaran_id')->get() as $ta)
                                    <option value="{{ $ta->tahun_ajaran_id }}"
                                        {{ old('tahun_ajaran_id') == $ta->tahun_ajaran_id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} ({{ $ta->semester }})</option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="wali_user_id" class="form-label">Wali Kelas</label>
                            <select class="form-select @error('wali_user_id') is-invalid @enderror" id="wali_user_id"
                                name="wali_user_id" required>
                                <option value="">Pilih Wali Kelas</option>
                                @foreach (\App\Models\User::role('Wali Kelas')->get() as $wali)
                                    <option value="{{ $wali->id }}"
                                        {{ old('wali_user_id') == $wali->id ? 'selected' : '' }}>{{ $wali->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('wali_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module">
        import {
            DataTable
        } from '/build/js/plugins/module.js';
        window.dt = new DataTable('#pc-dt-simple');
    </script>
    <script>
        // Jika ada error validasi, buka modal otomatis
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('modalCreateKelasAjar'));
            window.addEventListener('DOMContentLoaded', function() {
                myModal.show();
            });
        @endif
    </script>
@endsection
