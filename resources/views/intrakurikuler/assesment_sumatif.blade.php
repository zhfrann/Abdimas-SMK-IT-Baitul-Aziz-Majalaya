@extends('layouts.master')

@section('title', 'Asesmen Sumatif')

@section('css')
  <link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Asesmen Sumatif"/>

<div class="row">
  <div class="col-xl-12">
    <div class="card">

      {{-- Header --}}
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Pendidikan Agama Islam dan Budi Pekerti</h5>
          <span class="d-block m-t-5">Kelas 12</span>
        </div>
      </div>

      {{-- Body --}}
      <div class="card-body table-border-style">
        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th style="width:60px;">No</th>
                <th>Nama</th>
                <th style="width:180px;">Total Lingkup Materi</th>
                <th style="width:180px;">Total Akhir Semester</th>
                <th style="width:140px;">Nilai Rapor</th>
                <th style="width:160px;">Actions</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>1</td>
                <td>ADITYA RIZKI ARIFIN</td>
                <td><span class="badge bg-light-primary">86</span></td>
                <td><span class="badge bg-light-primary">82</span></td>
                <td><span class="badge bg-light-primary">84</span></td>
                <td>
                  <a href="{{ route('assesment_sumatif.detail') }}">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>

              <tr>
                <td>2</td>
                <td>ALYA NUR ZAHRA</td>
                <td><span class="badge bg-light-primary">90</span></td>
                <td><span class="badge bg-light-primary">88</span></td>
                <td><span class="badge bg-light-primary">89</span></td>
                <td>
                  <a href="{{ route('assesment_sumatif.detail') }}">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>

              <tr>
                <td>3</td>
                <td>ARSYAD FATHI MAWARDI</td>
                <td><span class="badge bg-light-primary">84</span></td>
                <td><span class="badge bg-light-primary">80</span></td>
                <td><span class="badge bg-light-primary">82</span></td>
                <td>
                  <a href="{{ route('assesment_sumatif.detail') }}">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>

              <tr>
                <td>4</td>
                <td>BABY CANTIKA CAHAYA PERMATA</td>
                <td><span class="badge bg-light-primary">88</span></td>
                <td><span class="badge bg-light-primary">85</span></td>
                <td><span class="badge bg-light-primary">87</span></td>
                <td>
                  <a href="{{ route('assesment_sumatif.detail') }}">
                    <button type="button" class="btn btn-sm btn-light-primary">Detail Nilai</button>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <a href="{{ route('intrakurikuler.index') }}" class="btn btn-light-secondary">Kembali</a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
  <script type="module">
    import { DataTable } from '/build/js/plugins/module.js';
    window.dt = new DataTable('#pc-dt-simple');
  </script>
@endsection
