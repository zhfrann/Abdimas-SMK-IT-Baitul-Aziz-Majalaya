@extends('layouts.master')

@section('title', 'Ekstrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />

{{-- (opsional) kalau kamu mau style dark-mode choices seperti intra, boleh tempel di sini juga --}}
@endsection

@section('content')
<x-breadcrumb item="Ekstrakurikuler" active="Ekstrakurikuler" />

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-0">Daftar Ekstrakurikuler</h5>
                    <span class="d-block m-t-5">
                        Tahun Ajaran
                        {{ $ekstrakurikuler->first()?->tahunAjaran?->tahun ?? '-' }}
                        {{ $ekstrakurikuler->first()?->tahunAjaran?->semester ?? '' }}
                    </span>
                </div>
            </div>

            <div class="card-body table-border-style">
                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Ekstrakurikuler</th>
                                <th>Tahun Ajaran</th>
                                <th>Guru</th>
                                <th>Jumlah Siswa</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($ekstrakurikuler as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_pelajaran }}</td>
                                <td>
                                    {{ $item->tahunAjaran?->tahun ?? '-' }}
                                    {{ $item->tahunAjaran?->semester ?? '' }}
                                </td>
                                <td>{{ $item->pembina?->name ?? '-' }}</td>
                                <td>{{ $item->peserta_count ?? 0 }}</td>

                                <td>
                                    <a href="{{ route('absensi.ekstrakurikuler.harian', $item->ekstrakurikuler_id) }}">
                                        <button type="button" class="btn btn-sm btn-light-warning mb-1">
                                            Absensi
                                        </button>
                                    </a>

                                    <a href="{{ route('absensi.ekstrakurikuler.rekap', $item->ekstrakurikuler_id) }}">
                                        <button type="button" class="btn btn-sm btn-light-secondary mb-1">
                                            Rekap
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data ekstrakurikuler.</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
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
    window.dt = new DataTable('#pc-dt-simple');
</script>
@endsection