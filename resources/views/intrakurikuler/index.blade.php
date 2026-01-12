@extends('layouts.master')

@section('title', 'Intrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Intrakurikuler" />

<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">

        <div>
          <h5 class="mb-0">Daftar Mata Pelajaran Intrakurikuler</h5>
          <span class="d-block m-t-5">
            Tahun Ajaran
            {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
            {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->semester ?? '' }}
          </span>
        </div>

        @role('Bagian Akademik|Super Admin')
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahIntra">
            <i class="bi bi-plus-lg"></i> Tambah Intrakurikuler
          </button>
        </div>
        @endrole
      </div>

      <div class="card-body table-border-style">

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Tahun Ajaran</th>
                <th>Kelas</th>
                <th>Guru</th>
                <th>Jumlah Siswa</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($intrakurikuler as $item)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama_pelajaran }}</td>
                <td>
                  {{ $item->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
                  - {{ ucfirst($item->kelasAjar?->tahunAjaran?->semester ?? '-') }}
                </td>
                <td>{{ $item->kelasAjar?->kelas?->nama_kelas ?? '-' }}</td>
                <td>{{ $item->pengampu?->staff?->nama ?? $item->pengampu?->name ?? 'N/A' }}</td>

                {{-- jumlah siswa: ambil dari withCount riwayatKelas di kelasAjar --}}
                <td>{{ $item->kelasAjar?->riwayat_kelas_count ?? 0 }}</td>

                <td>
                  <a href="{{ route('intrakurikuler.edit', $item->intrakurikuler_id) }}"
                    class="btn btn-sm btn-light-warning mb-1">Edit</a>
                  <a href="{{ route('lingkup-materi.index', $item->intrakurikuler_id) }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">Belum ada data intrakurikuler.</td>
              </tr>
              @endforelse
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===================== MODAL TAMBAH INTRAKURIKULER ===================== --}}
<div class="modal fade" id="modalTambahIntra" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="POST" action="{{ route('intrakurikuler.store') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Tambah Intrakurikuler</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>Mata Pelajaran</label>
            <input type="text" name="nama_pelajaran"
              value="{{ old('nama_pelajaran') }}"
              class="form-control @error('nama_pelajaran') is-invalid @enderror">
            @error('nama_pelajaran')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label>Kelas Ajar</label>
            <select name="kelas_ajar_id" class="form-control @error('kelas_ajar_id') is-invalid @enderror">
              <option value="">Pilih Kelas Ajar</option>
              @foreach ($kelasAjar as $ka)
              <option value="{{ $ka->kelas_ajar_id }}"
                {{ old('kelas_ajar_id') == $ka->kelas_ajar_id ? 'selected' : '' }}>
                {{ $ka->tahunAjaran->tahun }} {{ $ka->tahunAjaran->semester }} • {{ $ka->kelas->nama_kelas }}
              </option>
              @endforeach
            </select>
            @error('kelas_ajar_id')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <div class="mb-3">
            <label>Guru Pengampu</label>
            <select name="pengampu_user_id" class="form-control @error('pengampu_user_id') is-invalid @enderror">
              <option value="">Pilih Guru</option>
              @foreach ($guru as $g)
              <option value="{{ $g->id }}"
                {{ old('pengampu_user_id') == $g->id ? 'selected' : '' }}>
                {{ $g->staff?->nama ?? $g->name }}
                @if($g->staff?->nip) - NIP: {{ $g->staff->nip }} @endif
              </option>
              @endforeach
            </select>
            @error('pengampu_user_id')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
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
<script type="module">
  import {
    DataTable
  } from '/build/js/plugins/module.js';
  window.dt = new DataTable('#pc-dt-simple');
</script>

{{-- auto open modal kalau validasi error --}}
@if ($errors->any())
<script>
  const modal = new bootstrap.Modal(document.getElementById('modalTambahIntra'));
  modal.show();
</script>
@endif
@endsection