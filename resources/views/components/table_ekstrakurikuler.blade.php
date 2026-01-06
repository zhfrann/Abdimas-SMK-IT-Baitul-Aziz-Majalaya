@extends('layouts.master')

@section('title', 'Ekstrakurikuler')

@section('css')
<link rel="stylesheet" href="/build/css/plugins/style.css" />
@endsection

@section('content')

<x-breadcrumb item="Ekstrakurikuler" active="Ekstrakurikuler"/>

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
                          <img src="/build/images/admin/img-course-3.png" alt="img" class="img-fluid w-100" />
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">OSIS</h6>
                                <p class="mb-0 f-w-400">2025/2026 Ganjil</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Kelas</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">10</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jack Ronan</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">11</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <a href="/components/penilaian_ekstrakurikuler">
                          <button class="btn btn-sm btn-light-primary mb-2">Penilaian</button>
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="/build/images/admin/img-course-2.png" alt="img" class="img-fluid w-100" />
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Pramuka</h6>
                                <p class="mb-0 f-w-400">2025/2026 Ganjil</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Kelas</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">10</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jack Ronan</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">47</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <a href="#">
                          <button class="btn btn-sm btn-light-primary mb-2">Penilaian</button>
                        </a>
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
