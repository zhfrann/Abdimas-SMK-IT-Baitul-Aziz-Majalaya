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

                  {{-- Pendidikan Agama Islam dan Budi Pekerti --}}
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="https://iaitebo.ac.id/wp-content/uploads/2024/05/pai-img.png" alt="img" class="img-fluid w-100 hei-150 object-fit-cover" />
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
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">40</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Pendidikan Pancasila --}}
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="https://marketplace.canva.com/EAGl_oORb4U/1/0/1600w/canva-belajar-pendidikan-pancasila-materi-negara-kesatuan-oranye-dan-merah-ilustrasi-HohWyWwu2dw.jpg" alt="img" class="img-fluid w-100 hei-150 object-fit-cover" />
                          <!-- <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-light text-uppercase">Free</span>
                          </div> -->
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Pendidikan Pancasila</h6>
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
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">24</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Bahasa Indonesia --}}
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="https://imgv2-1-f.scribdassets.com/img/document/360219758/original/26805f60cb/1?v=1" alt="img" class="img-fluid w-100 hei-150 object-fit-cover" />
                          <!-- <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-light text-uppercase">Free</span>
                          </div> -->
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Bahasa Indonesia</h6>
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
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">26</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Pendidikan Jasmani, Olahraga, dan Kesehatan --}}
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="https://i0.wp.com/rsum.bandaacehkota.go.id/wp-content/uploads/2025/02/lari.webp?fit=1279%2C853&ssl=1" alt="img" class="img-fluid w-100 hei-150 object-fit-cover" />
                          <!-- <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-light text-uppercase">Free</span>
                          </div> -->
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Pendidikan Jasmani, Olahraga, dan Kesehatan</h6>
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
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">37</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="{{ route('tujuan_pembelajaran.index') }}" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="{{ route('lingkup_materi.index') }}" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="{{ route('assesment_formatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="{{ route('assesment_sumatif.index') }}" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  {{-- Sejarah --}}
                  <div class="col-sm-6 col-lg-4 col-xxl-3">
                    <div class="card border">
                      <div class="card-body p-2">
                        <div class="position-relative">
                          <img src="https://lh3.googleusercontent.com/bcCo7DGiIpyIB6dLPkFl6eLB_R3qkmq9S1Ijt7386zF5imqrPKFn6IW_JrBAuo_W8ylSgDICu_O-ElW3gsD47u0_5JJ2GhsWBTGtxyq89iJQapl_WS56hbU9QKzjMQ8TYQE6rx5Q6g=w2400" alt="img" class="img-fluid w-100 hei-150 object-fit-cover" />
                          <!-- <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge text-bg-light text-uppercase">Free</span>
                          </div> -->
                        </div>
                        <ul class="list-group list-group-flush my-2">
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <h6 class="mb-1">Sejarah</h6>
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
                                <p class="mb-0">Guru</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Jimmy Morris</p>
                              </div>
                            </div>
                          </li>
                          <li class="list-group-item px-0 py-2">
                            <div class="d-flex align-items-center">
                              <div class="flex-grow-1 me-2">
                                <p class="mb-0">Jumlah Siswa</p>
                              </div>
                              <div class="flex-shrink-0">
                                <p class="text-muted mb-0">30</p>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                          <a href="#" class="btn btn-sm btn-light-primary">Tujuan pembelajaran</a>
                          <a href="#" class="btn btn-sm btn-light-primary">Lingkup materi</a>
                          <a href="#" class="btn btn-sm btn-light-primary">Asesmen Formatif</a>
                          <a href="#" class="btn btn-sm btn-light-primary">Asesmen Sumatif</a>
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
