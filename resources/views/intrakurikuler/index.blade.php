@extends('layouts.master')

@section('title', 'Intrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Intrakurikuler" />

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Daftar Mata Pelajaran Intrakurikuler</h5>
          <span class="d-block m-t-5">
            Tahun Ajaran
            {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
            {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->semester ?? '' }}
          </span>
        </div>

        @role('Bagian Akademik| Super Admin')
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="{{ route('intrakurikuler.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Intrakurikuler
          </a>
        </div>
        @endrole
      </div>

      <div class="card-body table-border-style">
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

                {{-- kolom di DB: nama_pelajaran --}}
                <td>{{ $item->nama_pelajaran }}</td>

                <td>
                  {{ $item->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
                  - {{ ucfirst($item->kelasAjar?->tahunAjaran?->semester ?? '-') }}
                </td>

                <td>{{ $item->kelasAjar?->kelas?->nama_kelas ?? '-' }}</td>

                <td>
                  {{ $item->pengampu?->staff?->nama ?? $item->pengampu?->name ?? 'N/A' }}
                </td>

                <td>{{ $item->jumlah_siswa ?? 0 }}</td>

                <td>
                  <a href="{{ route('intrakurikuler.edit', $item->intrakurikuler_id) }}"
                     class="btn btn-sm btn-light-warning mb-1">Edit</a>
                     <a href="{{ route('lingkup-materi.index', $item) }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
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
<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<!-- [Page Specific JS] start -->
<script type="module">
  import { DataTable } from '/build/js/plugins/module.js';
  window.dt = new DataTable('#pc-dt-simple');
</script>
<!-- [Page Specific JS] end -->
@endsection
