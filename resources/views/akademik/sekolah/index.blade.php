@extends('layouts.master')

@section('title', 'Profil Sekolah')

@section('css')
    <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
    <x-breadcrumb item="Sekolah" active="Profil Sekolah" />

    <div class="row">
        <div class="col-xl-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Data Sekolah</h5>
                        <span class="d-block m-t-5">Informasi profil sekolah</span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if ($sekolah)
                            <a href="{{ route('akademik.sekolah.edit') }}" class="btn btn-primary">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if (!$sekolah)
                        <div class="alert alert-warning mb-0">
                            Data sekolah belum dibuat di database.
                        </div>
                    @else
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">NPSN</div>
                                    <div class="fw-semibold">{{ $sekolah->npsn }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Nama Sekolah</div>
                                    <div class="fw-semibold">{{ $sekolah->nama_sekolah }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">NSS</div>
                                    <div class="fw-semibold">{{ $sekolah->nss ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Kode Pos</div>
                                    <div class="fw-semibold">{{ $sekolah->kode_pos ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Alamat</div>
                                    <div class="fw-semibold">{{ $sekolah->alamat ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Kelurahan</div>
                                    <div class="fw-semibold">
                                        {{ $sekolah->kelurahan_nama ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Website</div>
                                    <div class="fw-semibold">{{ $sekolah->website ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Email</div>
                                    <div class="fw-semibold">{{ $sekolah->email ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Telepon</div>
                                    <div class="fw-semibold">{{ $sekolah->telp ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">Nama Kepala Sekolah</div>
                                    <div class="fw-semibold">{{ $sekolah->nama_kepala_sekolah ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="text-muted">NUPTK Kepala Sekolah</div>
                                    <div class="fw-semibold">{{ $sekolah->nuptk_kepala_sekolah ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

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
