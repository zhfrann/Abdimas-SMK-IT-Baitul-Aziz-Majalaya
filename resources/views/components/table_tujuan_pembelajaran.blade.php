@extends('layouts.master')

@section('title', 'Tujuan pembelajaran')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Tujuan pembelajaran"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- [ basic-table ] start -->
          <div class="col-xl-12">
            <div class="card">
              <div class="card-header">
                <h5>Pelajaran <></h5>
                <span class="d-block m-t-5"></span>
              </div>
              <div class="card-body table-border-style">
                <div class="table-responsive">
                  <table class="table" id="pc-dt-simple">
                    <thead>
                      <tr>
                        <th>id</th>
                        <th>Nama materi</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama</td>
                        <td>
                            <a href="/components/table_tujuan_pembelajaran" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                            <a href="/components/table_lingkup_materi" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                            <a href="/components/table_siswa" class="btn btn-sm btn-light-primary">Siswa</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- [ basic-table ] end -->
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
