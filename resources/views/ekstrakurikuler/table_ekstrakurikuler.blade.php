@extends('layouts.master')

@section('title', 'Ekstrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Ekstrakurikuler" active="Ekstrakurikuler"/>

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Daftar Ekstrakurikuler</h5>
          <span class="d-block m-t-5">Tahun Ajaran 2025/2026 Ganjil</span>
        </div>
      </div>
      <div class="card-body table-border-style">
        <div class="table-responsive">
          <table class="table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>No</th>
                <th>Ekstrakurikuler</th>
                <th>Tahun Ajaran</th>
                <th>Kelas</th>
                <th>Guru</th>
                <th>Jumlah Siswa</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>OSIS</td>
                <td>2025/2026 Ganjil</td>
                <td>10</td>
                <td>Jimmy Morris</td>
                <td>11</td>
                <td>
                  <a href="{{route('penilaian_ekstrakurikuler.index')}}" class="btn btn-sm btn-light-primary mb-1">Penilaian</a>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Pramuka</td>
                <td>2025/2026 Ganjil</td>
                <td>10</td>
                <td>Jimmy Morris</td>
                <td>47</td>
                <td>
                  <a href="{{route('penilaian_ekstrakurikuler.index')}}" class="btn btn-sm btn-light-primary mb-1">Penilaian</a>
                </td>
              </tr>
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
