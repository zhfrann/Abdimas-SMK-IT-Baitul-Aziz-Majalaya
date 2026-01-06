@extends('layouts.master')

@section('title', 'Intrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Intrakurikuler" active="Intrakurikuler"/>

        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="/build/images/admin/img-course-1.png" alt="img" class="img-fluid w-100" />
                          <!-- <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-light text-uppercase">Free</span>
                          </div> -->
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Pendidikan Agama Islam dan Budi Pekerti</h6>
                                <p class="mb-0 f-w-400">2025/2026 ganjil</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Kelas</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">12 </p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Teacher</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Students</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">40</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="/components/table_tujuan_pembelajaran" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="/components/table_lingkup_materi" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="/components/asesmen_formatif" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="/components/asesment_sumatif" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
                        </div>
                      </div>
                    </div>
                  </div>
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
