@extends('layouts.master')

@section('title', 'Tambah Kelas Ajar')

@section('css')
    <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')
    <div class="container">
        <h1>Tambah Kelas</h1>
        <form action="{{ route('akademik.kelas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" class="form-control" required>
            </div>
            <button class="btn btn-success">Simpan</button>
        </form>
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
