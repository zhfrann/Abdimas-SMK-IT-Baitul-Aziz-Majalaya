@extends('layouts.master')

@section('title', 'Intrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Intrakurikuler"/>

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div>
          <h5 class="mb-0">Daftar Mata Pelajaran Intrakurikuler</h5>
          <span class="d-block m-t-5">Tahun Ajaran 2025/2026 Ganjil</span>
        </div>
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
              <tr>
                <td>1</td>
                <td>Pendidikan Agama Islam dan Budi Pekerti</td>
                <td>2025/2026 Ganjil</td>
                <td>11</td>
                <td>Jimmy Morris</td>
                <td>40</td>
                <td>
                  <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Pendidikan Pancasila</td>
                <td>2025/2026 Ganjil</td>
                <td>12</td>
                <td>Jimmy Morris</td>
                <td>24</td>
                <td>
                  <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td>Bahasa Indonesia</td>
                <td>2025/2026 Ganjil</td>
                <td>10</td>
                <td>Jimmy Morris</td>
                <td>26</td>
                <td>
                  <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
                </td>
              </tr>
              <tr>
                <td>4</td>
                <td>Pendidikan Jasmani, Olahraga, dan Kesehatan</td>
                <td>2025/2026 Ganjil</td>
                <td>12</td>
                <td>Jimmy Morris</td>
                <td>37</td>
                <td>
                  <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
                </td>
              </tr>
              <tr>
                <td>5</td>
                <td>Sejarah</td>
                <td>2025/2026 Ganjil</td>
                <td>12</td>
                <td>Jimmy Morris</td>
                <td>30</td>
                <td>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Tujuan pembelajaran</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Lingkup materi</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Asesmen Formatif</a>
                  <a href="#" class="btn btn-sm btn-light-primary mb-1">Asesmen Sumatif</a>
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
