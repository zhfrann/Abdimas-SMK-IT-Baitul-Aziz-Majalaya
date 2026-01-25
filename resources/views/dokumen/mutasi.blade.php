@extends('layouts.master')

@section('title', 'Cetak Mutasi Siswa')

@section('content')
    <x-breadcrumb item="Dokumen" active="Cetak Mutasi" />
    <div class="card">
        <div class="card-header">
            <h5>Cetak Mutasi Siswa</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dokumen.cetak.mutasi') }}" target="_blank">
                @csrf
                <div class="mb-3">
                    <label for="jenis" class="form-label">Jenis Mutasi</label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="masuk">Mutasi Masuk</option>
                        <option value="keluar">Mutasi Keluar</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah Siswa</label>
                    <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" max="50"
                        value="3" required>
                    <div class="form-text">Tentukan berapa baris tabel yang ingin dicetak (1-50).</div>
                </div>
                <button type="submit" class="btn btn-primary">Cetak PDF</button>
            </form>
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
