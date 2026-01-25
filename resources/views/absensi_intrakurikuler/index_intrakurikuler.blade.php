@extends('layouts.master')

@section('title', 'Absensi Intrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />

<style>
    /* ===== Choices DARK MODE FIX (Able Pro uses body[data-pc-theme="dark"]) ===== */
    body[data-pc-theme="dark"] .choices__inner {
        background-color: rgba(255, 255, 255, .06) !important;
        border-color: rgba(255, 255, 255, .18) !important;
        color: rgba(255, 255, 255, .90) !important;
    }

    body[data-pc-theme="dark"] .choices__input {
        background-color: transparent !important;
        color: rgba(255, 255, 255, .92) !important;
    }

    body[data-pc-theme="dark"] .choices__input::placeholder {
        color: rgba(255, 255, 255, .55) !important;
    }

    body[data-pc-theme="dark"] .choices__list--dropdown,
    body[data-pc-theme="dark"] .choices__list[aria-expanded] {
        background-color: #1b1f24 !important;
        border-color: rgba(255, 255, 255, .14) !important;
        color: rgba(255, 255, 255, .92) !important;
    }

    body[data-pc-theme="dark"] .choices__list--dropdown .choices__item {
        color: rgba(255, 255, 255, .92) !important;
    }

    body[data-pc-theme="dark"] .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: rgba(255, 255, 255, .08) !important;
    }

    body[data-pc-theme="dark"] .choices__item--selectable {
        color: rgba(255, 255, 255, .92) !important;
    }

    /* selected item chip (kalau single select, ini text yang tampil) */
    body[data-pc-theme="dark"] .choices__item--selectable,
    body[data-pc-theme="dark"] .choices__list--single .choices__item {
        color: rgba(255, 255, 255, .92) !important;
    }

    /* kalau invalid, tetap merah */
    body[data-pc-theme="dark"] select.is-invalid+.choices .choices__inner {
        border-color: #dc3545 !important;
    }
</style>
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Absensi Intrakurikuler" />

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div
                class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-0">Daftar Mata Pelajaran Intrakurikuler</h5>
                    {{-- <span class="d-block m-t-5">
                            Tahun Ajaran
                            {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
                    {{ $intrakurikuler->first()?->kelasAjar?->tahunAjaran?->semester ?? '' }}
                    </span> --}}
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
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Jumlah Siswa</th>
                                @role('Bagian Akademik|Super Admin')
                                <th>Actions</th>
                                @endrole
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($intrakurikuler as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_pelajaran }}</td>
                                <td>
                                    {{ $item->kelasAjar?->tahunAjaran?->tahun ?? '-' }}
                                </td>
                                <td>
                                    {{ ucfirst($item->kelasAjar?->tahunAjaran?->semester ?? '-') }}
                                </td>
                                <td>{{ $item->kelasAjar?->kelas?->nama_kelas ?? '-' }}</td>
                                <td>{{ $item->pengampu?->staff?->nama ?? ($item->pengampu?->name ?? 'N/A') }}</td>
                                <td>{{ $item->kelasAjar?->riwayat_kelas_count ?? 0 }}</td>

                                @role('Bagian Akademik|Guru Mapel')
                                <td>
                                    <a href="{{ route('absensi.intrakurikuler.harian', $item->intrakurikuler_id) }}">
                                        <button type="button" class="btn btn-sm btn-light-warning mb-1">
                                            Absensi
                                        </button>
                                    </a>

                                    @endrole
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data intrakurikuler.</td>
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